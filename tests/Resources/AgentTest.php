<?php

namespace Tests\Resources;

use PHPUnit\Framework\TestCase;
use Tests\Fakers\FakeHttpTransporter;
use VicidialApi\Resources\Agent;

class AgentTest extends TestCase
{
    public function test_agent_pause_code(): void
    {
        $agent = new Agent(
            new FakeHttpTransporter('PAUSE')
        );
        $this->assertEquals('PAUSE', $agent->pauseCode('testuser', '1234'));
    }
}
