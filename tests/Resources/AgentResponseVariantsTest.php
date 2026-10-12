<?php

declare(strict_types=1);

namespace Tests\Resources;

use ReflectionClass;
use ReflectionNamedType;
use Tests\Fakers\RecordingTransporter;
use Tests\TestCase;
use VicidialApi\Contracts\AgentContract;
use VicidialApi\Exceptions\InvalidArgumentException;
use VicidialApi\Exceptions\VicidialException;
use VicidialApi\Resources\Agent;
use VicidialApi\Responses\VicidialResponse;

final class AgentResponseVariantsTest extends TestCase
{
    public function test_every_string_method_has_a_response_variant(): void
    {
        $contract = new ReflectionClass(AgentContract::class);
        $missing = [];

        foreach ($contract->getMethods() as $method) {
            $returnType = $method->getReturnType();
            if (! $returnType instanceof ReflectionNamedType || $returnType->getName() !== 'string') {
                continue;
            }

            $variant = $method->getName().'Response';
            if (! $contract->hasMethod($variant)) {
                $missing[] = $variant;

                continue;
            }

            $variantReturn = $contract->getMethod($variant)->getReturnType();
            $this->assertInstanceOf(ReflectionNamedType::class, $variantReturn);
            $this->assertSame(VicidialResponse::class, $variantReturn->getName(), $variant);
            $this->assertSame(
                array_map(fn ($p) => $p->getName(), $method->getParameters()),
                array_map(fn ($p) => $p->getName(), $contract->getMethod($variant)->getParameters()),
                "$variant must take the same parameters as {$method->getName()}"
            );
        }

        $this->assertSame([], $missing, 'Agent methods without a ...Response() variant: '.implode(', ', $missing));
        $this->assertCount(31 * 2, $contract->getMethods());
    }

    /**
     * @dataProvider variantPairs
     *
     * @param  list<mixed>  $arguments
     * @param  array<string, string>  $expectedQuery
     */
    public function test_variants_build_the_same_query(string $method, array $arguments, array $expectedQuery): void
    {
        $transporter = new RecordingTransporter('SUCCESS: ok - 6666');
        $agent = new Agent($transporter);

        $raw = $agent->{$method}(...$arguments);
        $this->assertSame('SUCCESS: ok - 6666', $raw);
        $rawQuery = $transporter->query();

        $response = $agent->{$method.'Response'}(...$arguments);
        $this->assertInstanceOf(VicidialResponse::class, $response);
        $this->assertTrue($response->ok());
        $this->assertSame($rawQuery, $transporter->query(), 'both variants must send the same query');

        foreach ($expectedQuery as $key => $value) {
            $this->assertSame($value, $rawQuery[$key], "$method: $key");
        }
    }

    /**
     * @return iterable<string, array{string, list<mixed>, array<string, string>}>
     */
    public static function variantPairs(): iterable
    {
        yield 'pauseCode' => ['pauseCode', [' 1000 ', 'BREAK'], ['function' => 'pause_code', 'agent_user' => '1000', 'value' => 'BREAK']];
        yield 'webserver' => ['webserver', [], ['function' => 'webserver']];
        yield 'version' => ['version', [], ['function' => 'version']];
        yield 'logout' => ['logout', ['1000'], ['function' => 'logout', 'value' => 'LOGOUT']];
        yield 'previewDial' => ['previewDial', ['1000', ' SKIP '], ['function' => 'preview_dial_action', 'value' => 'SKIP']];
        yield 'addLead' => ['addLead', ['1000', ['phone_number' => '7275551212']], ['function' => 'external_add_lead', 'phone_number' => '7275551212']];
        yield 'changeInGroups' => ['changeInGroups', ['1000', ['value' => 'CHANGE', 'ingroup_choices' => 'SALES']], ['function' => 'change_ingroups', 'ingroup_choices' => 'SALES']];
        yield 'setTimerAction' => ['setTimerAction', ['1000', ['value' => 'MESSAGE_ONLY']], ['function' => 'set_timer_action', 'value' => 'MESSAGE_ONLY']];
        yield 'stLoginLog' => ['stLoginLog', [['agentId' => 'x']], ['function' => 'st_login_log', 'agentId' => 'x']];
        yield 'stGetAgentActiveLead' => ['stGetAgentActiveLead', [['agentId' => 'x']], ['function' => 'st_get_agent_active_lead']];
        yield 'raCallControl' => ['raCallControl', ['1000', ['value' => 'HANGUP']], ['function' => 'ra_call_control', 'agent_user' => '1000', 'value' => 'HANGUP']];
        yield 'sendDtmf' => ['sendDtmf', [['agent_user' => '1000', 'value' => '1234']], ['function' => 'send_dtmf', 'value' => '1234']];
        yield 'transferConference' => ['transferConference', ['1000', ['value' => 'LOCAL_CLOSER']], ['function' => 'transfer_conference', 'value' => 'LOCAL_CLOSER']];
        yield 'parkCall' => ['parkCall', ['1000', ['value' => 'PARK_CUSTOMER']], ['function' => 'park_call', 'value' => 'PARK_CUSTOMER']];
        yield 'callAgent' => ['callAgent', ['1000', 'CALL'], ['function' => 'call_agent', 'value' => 'CALL']];
        yield 'recording' => ['recording', ['1000', ['value' => 'START']], ['function' => 'recording', 'value' => 'START']];
        yield 'webPhoneUrl' => ['webPhoneUrl', ['1000', 'DEFAULT'], ['function' => 'webphone_url', 'value' => 'DEFAULT']];
        yield 'audioPlayBack' => ['audioPlayBack', ['1000', ['stage' => 'PLAY', 'filename' => 'hello']], ['function' => 'audio_playback', 'filename' => 'hello']];
        yield 'switchLead' => ['switchLead', ['1000', '12345'], ['function' => 'switch_lead', 'lead_id' => '12345']];
        yield 'vmMessage' => ['vmMessage', ['1000', ['value' => 'EXship01']], ['function' => 'vm_message', 'value' => 'EXship01']];
        yield 'callsInQueueCount' => ['callsInQueueCount', ['1000', 'DISPLAY'], ['function' => 'calls_in_queue_count', 'value' => 'DISPLAY']];
        yield 'forceFronterLeave3way' => ['forceFronterLeave3way', ['1000', '1'], ['function' => 'force_fronter_leave_3way', 'value' => '1']];
        yield 'forceFronterAudioStop' => ['forceFronterAudioStop', ['1000', '1'], ['function' => 'force_fronter_audio_stop', 'value' => '1']];
        yield 'hangup' => ['hangup', ['1000'], ['function' => 'external_hangup', 'value' => '1']];
        yield 'pause' => ['pause', ['1000', 'PAUSE'], ['function' => 'external_pause', 'value' => 'PAUSE']];
        yield 'dial' => ['dial', ['1000', ['phone_number' => '7275551212']], ['function' => 'external_dial', 'value' => '7275551212', 'search' => 'YES', 'preview' => 'NO', 'focus' => 'YES']];
        yield 'dispo' => ['dispo', ['1000', ['value' => 'SALE']], ['function' => 'external_status', 'value' => 'SALE']];
        yield 'updateFields' => ['updateFields', ['1000', ['first_name' => 'Ada', 'formreload' => '1']], ['function' => 'update_fields', 'first_name' => 'Ada', 'formreload' => '1']];
        yield 'stereoRecording' => ['stereoRecording', ['1000', ['value' => 'BEGIN']], ['function' => 'stereo_recording']];
        yield 'sendNotification' => ['sendNotification', [['recipient_type' => 'USER', 'recipient' => '1000']], ['function' => 'send_notification']];
        yield 'refreshPanel' => ['refreshPanel', ['1000', ['scriptreload' => '1']], ['function' => 'refresh_panel']];
    }

    public function test_dial_without_phone_number_or_lead_id_throws_invalid_argument(): void
    {
        $agent = new Agent(new RecordingTransporter);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('phone_number or a lead_id');

        $agent->dial('1000', ['phone_code' => '1']);
    }

    public function test_dial_by_lead_id_only_is_allowed(): void
    {
        $transporter = new RecordingTransporter;
        $agent = new Agent($transporter);

        $agent->dial('1000', ['lead_id' => '12345', 'alt_dial' => 'ALT']);

        $query = $transporter->query();
        $this->assertSame('12345', $query['lead_id']);
        $this->assertSame('ALT', $query['alt_dial']);
        $this->assertArrayNotHasKey('value', $query);
    }

    public function test_update_fields_rejects_unknown_field_with_invalid_argument(): void
    {
        $agent = new Agent(new RecordingTransporter);

        try {
            $agent->updateFieldsResponse('1000', ['phone_numer' => '123']);
            $this->fail('expected InvalidArgumentException');
        } catch (InvalidArgumentException $e) {
            $this->assertStringContainsString('phone_numer', $e->getMessage());
            $this->assertInstanceOf(VicidialException::class, $e, 'still catchable as the base exception');
        }
    }

    public function test_update_fields_accepts_every_documented_field_and_trigger(): void
    {
        $transporter = new RecordingTransporter;
        $agent = new Agent($transporter);

        $fields = array_fill_keys(Agent::UPDATE_FIELDS_ALLOWED, '1');
        $agent->updateFields('1000', $fields);

        $this->assertSame('update_fields', $transporter->query()['function']);
        $this->assertContains('vendor_lead_code', Agent::UPDATE_FIELDS_ALLOWED);
        $this->assertContains('chatreload', Agent::UPDATE_FIELDS_ALLOWED);
    }
}
