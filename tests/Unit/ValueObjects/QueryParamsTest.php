<?php

declare(strict_types=1);

namespace Tests\Unit\ValueObjects;

use PHPUnit\Framework\TestCase;
use VicidialApi\ValueObjects\Transporter\QueryParams;

/**
 * Unit tests for QueryParams value object
 * Tests query parameter building and encoding
 */
final class QueryParamsTest extends TestCase
{
    public function test_creates_empty_query_params(): void
    {
        $params = QueryParams::create();

        $this->assertEquals([], $params->toArray());
    }

    public function test_adds_single_param(): void
    {
        $params = QueryParams::create()
            ->withParam('user', 'testuser');

        $this->assertEquals(['user' => 'testuser'], $params->toArray());
    }

    public function test_adds_multiple_params(): void
    {
        $params = QueryParams::create()
            ->withParam('user', 'testuser')
            ->withParam('pass', 'testpass')
            ->withParam('agent_user', '6666');

        $expected = [
            'user' => 'testuser',
            'pass' => 'testpass',
            'agent_user' => '6666',
        ];

        $this->assertEquals($expected, $params->toArray());
    }

    public function test_handles_integer_values(): void
    {
        $params = QueryParams::create()
            ->withParam('campaign_id', 123)
            ->withParam('lead_id', 456789);

        $expected = [
            'campaign_id' => 123,
            'lead_id' => 456789,
        ];

        $this->assertEquals($expected, $params->toArray());
    }

    public function test_immutability(): void
    {
        $original = QueryParams::create()->withParam('key1', 'value1');
        $modified = $original->withParam('key2', 'value2');

        // Original should not be changed
        $this->assertEquals(['key1' => 'value1'], $original->toArray());
        $this->assertEquals(['key1' => 'value1', 'key2' => 'value2'], $modified->toArray());
    }

    public function test_handles_special_characters(): void
    {
        $params = QueryParams::create()
            ->withParam('name', "O'Brien")
            ->withParam('company', 'Test & Co.');

        $expected = [
            'name' => "O'Brien",
            'company' => 'Test & Co.',
        ];

        $this->assertEquals($expected, $params->toArray());
    }

    public function test_handles_empty_string_value(): void
    {
        $params = QueryParams::create()
            ->withParam('optional_field', '');

        $this->assertEquals(['optional_field' => ''], $params->toArray());
    }

    public function test_handles_numeric_string(): void
    {
        $params = QueryParams::create()
            ->withParam('phone', '5551234567');

        $this->assertEquals(['phone' => '5551234567'], $params->toArray());
    }

    public function test_overwrites_existing_param(): void
    {
        $params = QueryParams::create()
            ->withParam('status', 'READY')
            ->withParam('status', 'PAUSED');

        $this->assertEquals(['status' => 'PAUSED'], $params->toArray());
    }

    public function test_handles_spaces_in_values(): void
    {
        $params = QueryParams::create()
            ->withParam('message', 'Hello World')
            ->withParam('reason', 'Break Time');

        $expected = [
            'message' => 'Hello World',
            'reason' => 'Break Time',
        ];

        $this->assertEquals($expected, $params->toArray());
    }
}
