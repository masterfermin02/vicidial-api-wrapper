<?php

declare(strict_types=1);

namespace Tests\Resources;

use Tests\Fakers\RecordingTransporter;
use Tests\TestCase;
use VicidialApi\Resources\Agent;
use VicidialApi\Responses\VicidialResponse;

final class AgentCoverageTest extends TestCase
{
    public function test_stereo_recording(): void
    {
        $transporter = new RecordingTransporter('SUCCESS: stereo_recording function sent - 6666|BEGIN||||192.168.1.5|8600051|PAUSED');
        $agent = new Agent($transporter);

        $agent->stereoRecording('1000', ['value' => 'BEGIN', 'stage' => '_MIDCALL']);

        $query = $transporter->query();
        $this->assertSame('stereo_recording', $query['function']);
        $this->assertSame('1000', $query['agent_user']);
        $this->assertSame('BEGIN', $query['value']);
        $this->assertSame('_MIDCALL', $query['stage']);

        $response = $agent->stereoRecordingResponse('1000', ['value' => 'STATUS']);
        $this->assertInstanceOf(VicidialResponse::class, $response);
        $this->assertTrue($response->ok());
        $this->assertSame('STATUS', $transporter->query()['value']);
    }

    public function test_send_notification(): void
    {
        $transporter = new RecordingTransporter('SUCCESS: send_notification - 6666');
        $agent = new Agent($transporter);

        $agent->sendNotification([
            'recipient_type' => 'USER_GROUP',
            'recipient' => 'AGENTS',
            'notification_text' => 'Ten minutes to closing',
            'show_confetti' => 'Y',
        ]);

        $query = $transporter->query();
        $this->assertSame('send_notification', $query['function']);
        $this->assertSame('USER_GROUP', $query['recipient_type']);
        $this->assertSame('AGENTS', $query['recipient']);
        $this->assertSame('Ten minutes to closing', $query['notification_text']);
        $this->assertArrayNotHasKey('agent_user', $query, 'send_notification is not scoped to an agent session');

        $response = $agent->sendNotificationResponse(['recipient_type' => 'USER', 'recipient' => '6666']);
        $this->assertTrue($response->ok());
    }

    public function test_refresh_panel(): void
    {
        $transporter = new RecordingTransporter('SUCCESS: refresh_panel request sent - 6666|1234|1|scriptreload,callbacksreload');
        $agent = new Agent($transporter);

        $agent->refreshPanel('6666', ['scriptreload' => '1', 'callbacksreload' => '1']);

        $query = $transporter->query();
        $this->assertSame('refresh_panel', $query['function']);
        $this->assertSame('6666', $query['agent_user']);
        $this->assertSame('1', $query['scriptreload']);
        $this->assertSame('1', $query['callbacksreload']);

        $response = $agent->refreshPanelResponse('6666', ['formreload' => '1']);
        $this->assertTrue($response->ok());
        $this->assertSame('1', $transporter->query()['formreload']);
    }

    public function test_every_documented_agent_function_is_wrapped(): void
    {
        // Index of AGENT_API.txt (vicidial.org/docs, updated 2025-08-30).
        $documented = [
            'version', 'external_hangup', 'external_status', 'external_pause', 'external_dial',
            'preview_dial_action', 'external_add_lead', 'change_ingroups', 'update_fields', 'set_timer_action',
            'st_login_log', 'st_get_agent_active_lead', 'ra_call_control', 'send_dtmf', 'transfer_conference',
            'park_call', 'logout', 'recording', 'stereo_recording', 'webserver', 'webphone_url', 'call_agent',
            'pause_code', 'audio_playback', 'switch_lead', 'calls_in_queue_count', 'force_fronter_leave_3way',
            'force_fronter_audio_stop', 'send_notification', 'vm_message', 'refresh_panel',
        ];

        $source = (string) file_get_contents(__DIR__.'/../../src/Resources/Agent.php');
        preg_match_all("/'function' => '([a-z_0-9]+)'/", $source, $matches);
        $wrapped = array_unique($matches[1]);

        $missing = array_values(array_diff($documented, $wrapped));
        $this->assertSame([], $missing, 'Agent API functions without a wrapper method: '.implode(', ', $missing));
    }
}
