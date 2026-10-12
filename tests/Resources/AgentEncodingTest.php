<?php

declare(strict_types=1);

namespace Tests\Resources;

use Tests\Fakers\RecordingTransporter;
use Tests\TestCase;
use VicidialApi\Resources\Agent;

/**
 * Regression tests: Agent methods used to call urlencode() on values that are then
 * passed through http_build_query(), which encoded them a second time
 * ("john doe" became "john%2Bdoe" instead of "john+doe").
 */
final class AgentEncodingTest extends TestCase
{
    public function test_agent_user_is_encoded_exactly_once(): void
    {
        $transporter = new RecordingTransporter;
        $agent = new Agent($transporter);

        $agent->pause('  john doe ', 'PAUSE');

        $this->assertStringContainsString('agent_user=john+doe', $transporter->rawQuery());
        $this->assertStringNotContainsString('%25', $transporter->rawQuery(), 'value was double encoded');
        $this->assertSame('john doe', $transporter->query()['agent_user']);
        $this->assertSame('PAUSE', $transporter->query()['value']);
    }

    public function test_dial_phone_number_with_plus_is_encoded_exactly_once(): void
    {
        $transporter = new RecordingTransporter;
        $agent = new Agent($transporter);

        $agent->dial('1000', ['phone_number' => '+18002474747', 'phone_code' => '1']);

        $query = $transporter->query();
        $this->assertSame('+18002474747', $query['value']);
        $this->assertSame('external_dial', $query['function']);
        $this->assertStringContainsString('value=%2B18002474747', $transporter->rawQuery());
    }

    public function test_switch_lead_value_is_not_double_encoded(): void
    {
        $transporter = new RecordingTransporter;
        $agent = new Agent($transporter);

        $agent->switchLead('1000', 'ABC 123');

        $this->assertSame('ABC 123', $transporter->query()['lead_id']);
    }
}
