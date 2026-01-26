<?php

namespace Tests\Resources;

use Tests\Fakers\FakeHttpTransporter;
use VicidialApi\Resources\Agent;

class AgentTest extends \PHPUnit\Framework\TestCase
{
    public function test_agent_pause_code(): void
    {
        $agent = new Agent(
            new FakeHttpTransporter('PAUSE')
        );
        $this->assertEquals('PAUSE', $agent->pauseCode('testuser', '1234'));
    }
}
