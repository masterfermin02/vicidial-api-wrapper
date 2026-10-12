<?php

declare(strict_types=1);

namespace Tests\Diagnostics;

use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Tests\Mocks\MockPsrClient;
use Tests\TestCase;
use VicidialApi\Diagnostics\Ping;
use VicidialApi\Exceptions\RetryException;
use VicidialApi\Exceptions\TransportException;
use VicidialApi\Responses\VicidialResponse;
use VicidialApi\VicidialClient;
use VicidialApi\VicidialConfig;

final class PingTest extends TestCase
{
    private const VERSION = 'VERSION: 2.14-890a|BUILD: 210303-1234|DATE: 2021-03-03 12:00:00|EPOCH: 1614772800';

    private static function text(string $body, int $status = 200): Response
    {
        return new Response($status, ['Content-Type' => 'text/plain'], $body);
    }

    /**
     * @param  list<Response|\Throwable>  $answers
     */
    private static function client(array $answers): VicidialClient
    {
        $config = VicidialConfig::create(host: 'vicidial.test', user: '6666', pass: 'secret');

        return VicidialClient::make($config, new MockPsrClient($answers));
    }

    public function test_all_green(): void
    {
        $client = self::client([
            self::text(self::VERSION),
            self::text("Webserver Data:\nset.timezone: America/New_York\nserver name: www.vicidial.org"),
            self::text(self::VERSION),
            self::text("TESTCAMP|Test Campaign|Y|---ALL---\nSALES|Sales|Y|---ALL---"),
        ]);

        $report = (new Ping($client))->run();

        $this->assertTrue($report->ok());
        $this->assertCount(4, $report->checks());
        $this->assertSame([], $report->failures());
        $this->assertSame('VICIdial 2.14-890a build 210303-1234', $report->checks()[0]->summary);
        $this->assertSame('authenticated, web server www.vicidial.org', $report->checks()[1]->summary);
        $this->assertSame('authenticated, 2 campaign(s) visible', $report->checks()[3]->summary);
        $this->assertStringContainsString('[OK] Agent API reachable (version)', $report->toText());
    }

    public function test_permission_error_is_explained(): void
    {
        $client = self::client([
            self::text(self::VERSION),
            self::text('ERROR: auth USER DOES NOT HAVE PERMISSION TO USE THIS FUNCTION - 6666|webserver|ADMIN'),
            self::text(self::VERSION),
            self::text('ERROR: campaigns_list USER DOES NOT HAVE PERMISSION TO GET CAMPAIGN INFO - 6666|0'),
        ]);

        $report = (new Ping($client))->run();

        $this->assertFalse($report->ok());
        $failures = $report->failures();
        $this->assertCount(2, $failures);
        $this->assertSame('webserver', $failures[0]->function);
        $this->assertStringContainsString('user_level', (string) $failures[0]->explanation);
        $this->assertStringContainsString('[FAIL] Agent API credentials (webserver)', $report->toText());
    }

    public function test_wrong_url_is_explained_from_html_and_404(): void
    {
        $client = self::client([
            new Response(404, ['Content-Type' => 'text/html'], '<html><body>Not Found</body></html>'),
            new Response(200, ['Content-Type' => 'text/html'], '<!DOCTYPE html><html>login</html>'),
            self::text(''),
            self::text('ERROR: NO FUNCTION SPECIFIED'),
        ]);

        $report = (new Ping($client))->run();
        $explanations = array_map(fn ($check) => (string) $check->explanation, $report->checks());

        $this->assertStringContainsString('HTTP 404', $explanations[0]);
        $this->assertStringContainsString('HTML page', $explanations[1]);
        $this->assertStringContainsString('empty body', $explanations[2]);
        $this->assertStringContainsString('query string', $explanations[3]);
    }

    public function test_transport_errors_are_explained(): void
    {
        $refused = new ConnectException('cURL error 7: Failed to connect: Connection refused', new Request('GET', 'http://vicidial.test'));
        $client = self::client([$refused, $refused, $refused, $refused]);

        $report = (new Ping($client))->run();

        $this->assertFalse($report->ok());
        $this->assertStringContainsString('Connection refused', (string) $report->checks()[0]->explanation);
        $this->assertStringContainsString('TransportException', $report->checks()[0]->summary);
    }

    public function test_explain_covers_credentials_api_access_and_fallback(): void
    {
        $this->assertStringContainsString('password', Ping::explain(VicidialResponse::from('ERROR: Invalid Username/Password: |6666|1234|')));
        $this->assertStringContainsString('API Access', Ping::explain(VicidialResponse::from('ERROR: sounds_list USER DOES NOT HAVE API ACCESS')));
        $this->assertStringContainsString('no campaigns', Ping::explain(VicidialResponse::from('ERROR: campaigns_list THIS USER HAS NO VIEWABLE CAMPAIGNS - 6666')));
        $this->assertStringContainsString('basic auth', Ping::explain(VicidialResponse::from('Unauthorized', [], 401)));
        $this->assertStringContainsString('proxy', Ping::explain(VicidialResponse::from('Bad Gateway', [], 502)));
        $this->assertSame('VICIdial answered: something else', Ping::explain(VicidialResponse::from('ERROR: something else')));
    }

    public function test_explain_exception_unwraps_retry_and_transport_exceptions(): void
    {
        $dns = new ConnectException('cURL error 6: Could not resolve host: vicidial.test', new Request('GET', 'http://vicidial.test'));
        $retry = new RetryException('Request failed after 3 attempts', 3, 0, new TransportException($dns));

        $explanation = Ping::explainException($retry);

        $this->assertStringContainsString('Gave up after 3 attempts', $explanation);
        $this->assertStringContainsString('DNS lookup failed', $explanation);
        $this->assertStringContainsString('TLS problem', Ping::explainException(new \RuntimeException('SSL certificate problem: self signed certificate')));
        $this->assertStringContainsString('Timed out', Ping::explainException(new \RuntimeException('Operation timed out after 30000 milliseconds')));
    }
}
