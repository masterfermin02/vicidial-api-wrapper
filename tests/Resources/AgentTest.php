<?php

namespace Resources;

use Fakers\FakeHttpTransporter;
use VicidialApi\Resources\Agent;

class AgentTest extends \PHPUnit\Framework\TestCase
{
    public function testAgentPauseCode(): void
    {
        $agent = new Agent(
            new FakeHttpTransporter('PAUSE')
        );
        $this->assertEquals('PAUSE', $agent->pauseCode('testuser', '1234'));
    }
}
