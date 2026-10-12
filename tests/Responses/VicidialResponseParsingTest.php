<?php

declare(strict_types=1);

namespace Tests\Responses;

use GuzzleHttp\Psr7\Response;
use Tests\Mocks\MockPsrClient;
use Tests\TestCase;
use VicidialApi\Responses\VicidialResponse;
use VicidialApi\Transporters\HttpTransporter;
use VicidialApi\ValueObjects\Transporter\BaseUri;
use VicidialApi\ValueObjects\Transporter\Headers;
use VicidialApi\ValueObjects\Transporter\Payload;
use VicidialApi\ValueObjects\Transporter\QueryParams;

/**
 * Parsing of the payload shapes documented in AGENT_API.txt / NON-AGENT_API.txt
 * plus the failure shapes a proxy or web server can produce.
 */
final class VicidialResponseParsingTest extends TestCase
{
    public function test_empty_body_is_an_error(): void
    {
        $response = VicidialResponse::from("  \n");

        $this->assertSame('error', $response->status());
        $this->assertTrue($response->failed());
        $this->assertSame('Empty response body', $response->message());
    }

    public function test_html_body_is_an_error_by_content_type(): void
    {
        $response = VicidialResponse::from('Welcome', ['content-type' => 'text/html; charset=utf-8'], 200);

        $this->assertTrue($response->failed());
        $this->assertStringContainsString('HTML response', (string) $response->message());
        $this->assertSame('Welcome', $response->get('result'));
    }

    public function test_html_body_is_an_error_by_sniffing(): void
    {
        $response = VicidialResponse::from("<!DOCTYPE html>\n<html><body>login</body></html>");

        $this->assertTrue($response->failed());
        $this->assertSame(200, VicidialResponse::from('<html>', [], 200)->statusCode());
    }

    public function test_http_error_status_without_error_prefix_is_an_error(): void
    {
        $response = VicidialResponse::from('Service Unavailable', ['Content-Type' => 'text/plain'], 503);

        $this->assertSame('error', $response->status());
        $this->assertSame(503, $response->statusCode());
    }

    public function test_success_prefix_wins_over_http_status(): void
    {
        $response = VicidialResponse::from('SUCCESS: external_hangup function set - 1|6666', [], 500);

        $this->assertTrue($response->ok());
    }

    public function test_headers_are_exposed_case_insensitively(): void
    {
        $response = VicidialResponse::from('SUCCESS: ok', ['Content-Type' => 'text/plain', 'X-Served-By' => 'lb-2'], 200);

        $this->assertSame(['Content-Type' => 'text/plain', 'X-Served-By' => 'lb-2'], $response->headers());
        $this->assertSame('lb-2', $response->header('x-served-by'));
        $this->assertNull($response->header('X-Missing'));
        $this->assertSame(200, $response->toArray()['statusCode']);
    }

    public function test_fields_strip_the_message_and_keep_data_n_compatible(): void
    {
        $response = VicidialResponse::from('SUCCESS: external_pause function set - PAUSE|1232020456|6666');

        $this->assertSame(['PAUSE', '1232020456', '6666'], $response->fields());
        $this->assertSame('external_pause function set - PAUSE', $response->get('data_0'), 'data_0 keeps its historical shape');
        $this->assertSame('6666', $response->get('data_2'));
        $this->assertSame('external_pause function set - PAUSE|1232020456|6666', $response->message());
    }

    public function test_fields_for_raw_pipe_line_and_for_messages_without_data(): void
    {
        $this->assertSame(['INCALL', 'M405', '12345'], VicidialResponse::from('INCALL|M405|12345')->fields());
        $this->assertSame([], VicidialResponse::from('SUCCESS: notification queued')->fields());
        $this->assertSame(['72755', '6666'], VicidialResponse::from('ERROR: add_lead INVALID PHONE NUMBER AREACODE - 72755|6666 ')->fields());
    }

    public function test_csv_report_with_header_row_parses_into_keyed_rows(): void
    {
        $body = "status,call_id,lead_id,campaign_id\nINCALL,M4050908070000012345,12345,TESTCAMP\nPAUSED,,105,TESTBLND";
        $response = VicidialResponse::from($body, [], 200, true);

        $this->assertTrue($response->ok());
        $this->assertSame(['status', 'call_id', 'lead_id', 'campaign_id'], $response->columns());
        $this->assertCount(2, $response->rows());
        $this->assertSame('12345', $response->rows()[0]['lead_id']);
        $this->assertSame('PAUSED', $response->rows()[1]['status']);
        $this->assertSame('', $response->rows()[1]['call_id']);
        $this->assertSame(',', $response->get('delimiter'));
    }

    public function test_header_row_is_detected_when_not_told(): void
    {
        $body = "user|full_name|user_group|calls\n1029|test agent|ADMIN|1\n6666|Admin|ADMIN|5";
        $response = VicidialResponse::from($body);

        $this->assertSame(['user', 'full_name', 'user_group', 'calls'], $response->columns());
        $this->assertSame('test agent', $response->rows()[0]['full_name']);
    }

    public function test_table_without_header_keeps_positional_rows(): void
    {
        $body = "default|default_hold.gsm|on_hold_music.wav\nSALES|sales_hold.gsm|sales.wav";
        $response = VicidialResponse::from($body);

        $this->assertSame([], $response->columns());
        $this->assertSame(['default', 'default_hold.gsm', 'on_hold_music.wav'], $response->rows()[0]);
        $this->assertSame('SALES', $response->rows()[1][0]);
    }

    public function test_explicit_no_header_overrides_detection(): void
    {
        $body = "user|full_name\nagent|name";
        $response = VicidialResponse::from($body, [], null, false);

        $this->assertSame([], $response->columns());
        $this->assertCount(2, $response->rows());
    }

    public function test_tab_delimited_report(): void
    {
        $body = "user\tcalls\n1029\t1\n6666\t5";
        $response = VicidialResponse::from($body);

        $this->assertSame("\t", $response->get('delimiter'));
        $this->assertSame('5', $response->rows()[1]['calls']);
    }

    public function test_json_object_is_decoded(): void
    {
        $body = "{\"availableCount\": 5,\n\"INGROUPS\": {\n\"INGROUP1\": 5,\n\"INGROUP2\": 3\n}\n}";
        $response = VicidialResponse::from($body);

        $this->assertTrue($response->ok());
        $this->assertSame(5, $response->get('availableCount'));
        $this->assertSame(['INGROUP1' => 5, 'INGROUP2' => 3], $response->get('INGROUPS'));
    }

    public function test_json_list_becomes_rows(): void
    {
        $response = VicidialResponse::from('[{"user":"1000","status":"READY"},{"user":"1001","status":"INCALL"}]');

        $this->assertCount(2, $response->rows());
        $this->assertSame('INCALL', $response->rows()[1]['status']);
    }

    public function test_multi_line_key_value_still_parses(): void
    {
        $body = "Webserver Data:\nset.timezone: America/New_York\nabbr.timezone: EDT\nphp version: 5.3.3";
        $response = VicidialResponse::from($body);

        $this->assertSame('America/New_York', $response->get('set.timezone'));
        $this->assertSame('5.3.3', $response->get('php version'));
        $this->assertSame([], $response->rows());
    }

    public function test_single_line_payloads_have_no_rows(): void
    {
        $this->assertSame([], VicidialResponse::from('user|allowed_campaigns')->rows());
        $this->assertSame([], VicidialResponse::from('SUCCESS: ok - 1|2')->rows());
    }

    public function test_report_rejection_inside_body_is_an_error(): void
    {
        $response = VicidialResponse::from('ERROR: logged_in_agents NO LOGGED IN AGENTS');

        $this->assertTrue($response->failed());
        $this->assertSame('logged_in_agents NO LOGGED IN AGENTS', $response->message());
    }

    public function test_transporter_passes_status_code_headers_and_header_hint(): void
    {
        $client = new MockPsrClient([
            new Response(200, ['Content-Type' => 'text/plain', 'X-Request-ID' => 'r-1'], "user|calls\n1000|3"),
        ]);
        $transporter = new HttpTransporter(
            client: $client,
            baseUri: BaseUri::from('http://vicidial.test'),
            headers: Headers::create(),
            queryParams: QueryParams::create(),
            streamHandler: fn ($request) => $client->sendRequest($request),
        );

        $response = $transporter->requestResponse(Payload::retrieveWithParameters(
            '/vicidial/non_agent_api.php',
            ['function' => 'agent_stats_export', 'header' => 'YES'],
        ));

        $this->assertSame(200, $response->statusCode());
        $this->assertSame('r-1', $response->requestId());
        $this->assertSame('text/plain', $response->header('content-type'));
        $this->assertSame('3', $response->rows()[0]['calls']);
    }
}
