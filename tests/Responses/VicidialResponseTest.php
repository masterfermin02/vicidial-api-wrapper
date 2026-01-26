<?php

namespace Responses;

use PHPUnit\Framework\TestCase;
use VicidialApi\Responses\VicidialResponse;

class VicidialResponseTest extends TestCase
{
    public function test_creates_response_from_simple_string(): void
    {
        $response = VicidialResponse::from('SUCCESS: external_hangup function set - 1|6666');

        $this->assertStringContainsString('SUCCESS', $response->raw());
        $this->assertEquals('success', $response->status());
        $this->assertTrue($response->ok());
        $this->assertFalse($response->failed());
    }

    public function test_creates_response_from_error_string(): void
    {
        $response = VicidialResponse::from('ERROR: agent_user is not logged in - 6666');

        $this->assertStringContainsString('ERROR', $response->raw());
        $this->assertEquals('error', $response->status());
        $this->assertFalse($response->ok());
        $this->assertTrue($response->failed());
    }

    public function test_creates_response_from_version_format(): void
    {
        $response = VicidialResponse::from('VERSION: 2.0.5-2|BUILD: 90116-1229|DATE: 2009-01-15 14:59:33|EPOCH: 1222020803');

        $this->assertEquals('success', $response->status());
        $this->assertTrue($response->ok());
        $this->assertEquals('2.0.5-2', $response->get('version'));
        $this->assertEquals('90116-1229', $response->get('build'));
    }

    public function test_creates_response_from_pipe_delimited(): void
    {
        $content = 'user|allowed_campaigns|allowed_ingroups';
        $response = VicidialResponse::from($content);

        $this->assertEquals($content, $response->raw());
        $this->assertEquals('user', $response->get('field_0'));
        $this->assertEquals('allowed_campaigns', $response->get('field_1'));
        $this->assertEquals('allowed_ingroups', $response->get('field_2'));
    }

    public function test_creates_response_from_multi_line_key_value(): void
    {
        $content = "set.timezone: America/New_York\nabbr.timezone: EDT\ndst.timezone: 1";
        $response = VicidialResponse::from($content);

        $this->assertEquals($content, $response->raw());
        $this->assertEquals('America/New_York', $response->get('set.timezone'));
        $this->assertEquals('EDT', $response->get('abbr.timezone'));
        $this->assertEquals('1', $response->get('dst.timezone'));
    }

    public function test_success_response_with_data(): void
    {
        $response = VicidialResponse::from('SUCCESS: external_pause function set - PAUSE|1232020456|6666');

        $this->assertTrue($response->ok());
        $this->assertEquals('success', $response->status());
        $this->assertEquals('SUCCESS', $response->get('result_type'));
        $this->assertStringContainsString('external_pause', $response->get('message'));
    }

    public function test_error_response_with_data(): void
    {
        $response = VicidialResponse::from('ERROR: auth USER DOES NOT HAVE PERMISSION - 6666|add_lead');

        $this->assertFalse($response->ok());
        $this->assertTrue($response->failed());
        $this->assertEquals('error', $response->status());
        $this->assertEquals('ERROR', $response->get('result_type'));
    }

    public function test_as_array_returns_parsed_data(): void
    {
        $response = VicidialResponse::from('SUCCESS: external_status function set - A|6666');

        $array = $response->asArray();

        $this->assertIsArray($array);
        $this->assertEquals('SUCCESS', $array['result_type']);
    }

    public function test_as_text_returns_raw_string(): void
    {
        $content = 'SUCCESS: operation completed';
        $response = VicidialResponse::from($content);

        $this->assertEquals($content, $response->asText());
    }

    public function test_get_returns_value_or_default(): void
    {
        $response = VicidialResponse::from('SUCCESS: test - data');

        $this->assertEquals('SUCCESS', $response->get('result_type'));
        $this->assertEquals('default', $response->get('nonexistent', 'default'));
        $this->assertNull($response->get('nonexistent'));
    }

    public function test_has_checks_key_existence(): void
    {
        $response = VicidialResponse::from('VERSION: 2.0.5|BUILD: 12345');

        $this->assertTrue($response->has('version'));
        $this->assertTrue($response->has('build'));
        $this->assertFalse($response->has('nonexistent'));
    }

    public function test_to_string_returns_raw_content(): void
    {
        $content = 'SUCCESS: Operation completed';
        $response = VicidialResponse::from($content);

        $this->assertEquals($content, (string) $response);
    }

    public function test_to_array_includes_all_response_data(): void
    {
        $response = VicidialResponse::from('SUCCESS: test', ['X-Request-ID' => 'test-123']);

        $array = $response->toArray();

        $this->assertArrayHasKey('status', $array);
        $this->assertArrayHasKey('raw', $array);
        $this->assertArrayHasKey('parsed', $array);
        $this->assertArrayHasKey('requestId', $array);
        $this->assertEquals('test-123', $array['requestId']);
    }

    public function test_to_json_returns_valid_json(): void
    {
        $response = VicidialResponse::from('SUCCESS: test');

        $json = $response->toJson();
        $decoded = json_decode($json, true);

        $this->assertIsArray($decoded);
        $this->assertArrayHasKey('status', $decoded);
        $this->assertArrayHasKey('raw', $decoded);
    }

    public function test_request_id_is_extracted_from_headers(): void
    {
        $response = VicidialResponse::from('SUCCESS: test', ['X-Request-ID' => 'req-123']);

        $this->assertEquals('req-123', $response->requestId());
    }

    public function test_request_id_is_null_when_not_present(): void
    {
        $response = VicidialResponse::from('SUCCESS: test');

        $this->assertNull($response->requestId());
    }

    public function test_array_access_works(): void
    {
        $response = VicidialResponse::from('SUCCESS: test - data|more');

        $this->assertTrue(isset($response['result_type']));
        $this->assertEquals('SUCCESS', $response['result_type']);
        $this->assertFalse(isset($response['nonexistent']));
    }

    public function test_ok_detects_success_in_various_formats(): void
    {
        $this->assertTrue(VicidialResponse::from('SUCCESS: operation completed')->ok());
        $this->assertTrue(VicidialResponse::from('SUCCESS: external_hangup function set - 1|6666')->ok());
        $this->assertTrue(VicidialResponse::from('VERSION: 2.0.5|BUILD: 12345')->ok());
    }

    public function test_failed_detects_errors_in_various_formats(): void
    {
        $this->assertTrue(VicidialResponse::from('ERROR: Invalid user')->failed());
        $this->assertTrue(VicidialResponse::from('ERROR: agent_user is not logged in - 6666')->failed());
        $this->assertTrue(VicidialResponse::from('ERROR: auth USER DOES NOT HAVE PERMISSION - 6666|add_lead')->failed());
    }

    public function test_parsed_returns_array(): void
    {
        $response = VicidialResponse::from('SUCCESS: external_status function set - A|6666');

        $parsed = $response->parsed();

        $this->assertIsArray($parsed);
        $this->assertEquals('SUCCESS', $parsed['result_type']);
    }

    public function test_notice_response(): void
    {
        $response = VicidialResponse::from('NOTICE: defined dial_ingroup not found - FAKE_INGROUP');

        $this->assertEquals('notice', $response->status());
        $this->assertEquals('NOTICE', $response->get('result_type'));
    }
}
