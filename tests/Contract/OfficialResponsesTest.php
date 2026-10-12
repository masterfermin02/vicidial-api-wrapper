<?php

declare(strict_types=1);

namespace Tests\Contract;

use Tests\TestCase;
use VicidialApi\Responses\VicidialResponse;

/**
 * Every example response printed in AGENT_API.txt and NON-AGENT_API.txt
 * (tests/Fixtures/official/responses.json, regenerated with scripts/scrape-official-responses.py)
 * must parse with the status its prefix announces.
 */
final class OfficialResponsesTest extends TestCase
{
    /**
     * @return array{agent: array<string, list<string>>, non_agent: array<string, list<string>>}
     */
    private static function fixtures(): array
    {
        /** @var array{agent: array<string, list<string>>, non_agent: array<string, list<string>>} $data */
        $data = json_decode((string) file_get_contents(__DIR__.'/../Fixtures/official/responses.json'), true, 512, JSON_THROW_ON_ERROR);

        return $data;
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function documentedResponses(): iterable
    {
        $fixtures = self::fixtures();

        foreach (['agent' => $fixtures['agent'], 'non_agent' => $fixtures['non_agent']] as $api => $functions) {
            foreach ($functions as $function => $responses) {
                foreach ($responses as $index => $response) {
                    yield "$api $function #$index" => [$function, $response];
                }
            }
        }
    }

    /**
     * @dataProvider documentedResponses
     */
    public function test_documented_response_parses_with_the_announced_status(string $function, string $raw): void
    {
        $response = VicidialResponse::from($raw);
        $prefix = strtolower((string) strstr($raw, ':', true));

        $this->assertSame($prefix, $response->status(), $raw);
        $this->assertSame($prefix === 'success', $response->ok(), $raw);
        $this->assertSame(strtoupper($prefix), $response->get('result_type'));

        $message = $response->message();
        $this->assertIsString($message);
        $this->assertSame(trim(substr($raw, strlen($prefix) + 1)), $message, $raw);

        if (str_contains($message, ' - ') && str_contains($message, '|')) {
            $data = substr($message, strpos($message, ' - ') + 3);
            $this->assertCount(substr_count($data, '|') + 1, $response->fields(), $raw);
            $this->assertSame(trim(explode('|', $data)[0]), $response->fields()[0], $raw);
        }
    }

    public function test_fixture_functions_all_have_a_wrapper_method(): void
    {
        $fixtures = self::fixtures();

        $agentSource = (string) file_get_contents(__DIR__.'/../../src/Resources/Agent.php');
        $adminSource = (string) file_get_contents(__DIR__.'/../../src/Resources/Admin.php');

        foreach (array_keys($fixtures['agent']) as $function) {
            $this->assertStringContainsString("'$function'", $agentSource, "Agent API function $function has no wrapper method");
        }

        foreach (array_keys($fixtures['non_agent']) as $function) {
            $this->assertStringContainsString("makeApiRequest('$function'", $adminSource, "Non-Agent API function $function has no wrapper method");
        }

        $this->assertGreaterThan(25, count($fixtures['agent']));
        $this->assertGreaterThan(55, count($fixtures['non_agent']));
    }
}
