<?php

namespace Tests\Resources;

use Tests\Fakers\FakeHttpTransporter;
use Tests\TestCase;
use VicidialApi\Resources\Agent;
use VicidialApi\Responses\VicidialResponse;

class AgentResponseTest extends TestCase
{
    public function test_hangup_response_returns_vicidial_response(): void
    {
        $agent = new Agent(
            new FakeHttpTransporter('SUCCESS: external_hangup function set - 1|6666')
        );

        $response = $agent->hangupResponse('testuser');

        $this->assertInstanceOf(VicidialResponse::class, $response);
        $this->assertTrue($response->ok());
        $this->assertStringContainsString('SUCCESS', $response->raw());
    }

    public function test_dispo_response_returns_vicidial_response(): void
    {
        $agent = new Agent(
            new FakeHttpTransporter('SUCCESS: external_status function set - A|6666')
        );

        $response = $agent->dispoResponse('testuser', ['value' => 'SALE']);

        $this->assertInstanceOf(VicidialResponse::class, $response);
        $this->assertTrue($response->ok());
    }

    public function test_pause_response_returns_vicidial_response(): void
    {
        $agent = new Agent(
            new FakeHttpTransporter('SUCCESS: external_pause function set - PAUSE|1232020456|6666')
        );

        $response = $agent->pauseResponse('testuser', 'PAUSE');

        $this->assertInstanceOf(VicidialResponse::class, $response);
        $this->assertTrue($response->ok());
    }

    public function test_dial_response_returns_vicidial_response(): void
    {
        $agent = new Agent(
            new FakeHttpTransporter('SUCCESS: external_dial function set - 7275551212|6666|1|YES|NO|YES|123456|1232020456|9|TESTING|7275551211|')
        );

        $response = $agent->dialResponse('testuser', [
            'phone_number' => '1234567890',
            'phone_code' => '1',
        ]);

        $this->assertInstanceOf(VicidialResponse::class, $response);
        $this->assertTrue($response->ok());
    }

    public function test_update_fields_response_returns_vicidial_response(): void
    {
        $agent = new Agent(
            new FakeHttpTransporter('SUCCESS: update_fields function set - 12345|6666')
        );

        $response = $agent->updateFieldsResponse('testuser', [
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);

        $this->assertInstanceOf(VicidialResponse::class, $response);
        $this->assertTrue($response->ok());
    }

    public function test_error_response_is_detected(): void
    {
        $agent = new Agent(
            new FakeHttpTransporter('ERROR: agent_user is not logged in - 6666')
        );

        $response = $agent->hangupResponse('invaliduser');

        $this->assertInstanceOf(VicidialResponse::class, $response);
        $this->assertFalse($response->ok());
        $this->assertTrue($response->failed());
        $this->assertEquals('error', $response->status());
    }

    public function test_backward_compatibility_with_string_methods(): void
    {
        $agent = new Agent(
            new FakeHttpTransporter('PAUSE')
        );

        // Original method still returns string
        $stringResult = $agent->pauseCode('testuser', '1234');
        $this->assertIsString($stringResult);
        $this->assertEquals('PAUSE', $stringResult);

        // Response method returns DTO
        $agent2 = new Agent(
            new FakeHttpTransporter('PAUSE')
        );
        $response = $agent2->pauseResponse('testuser', 'PAUSE');
        $this->assertInstanceOf(VicidialResponse::class, $response);
    }
}
