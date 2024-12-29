<?php

namespace VicidialApi\Resources;

use VicidialApi\Contracts\TransporterContract;
use VicidialApi\Exceptions\ErrorException;
use VicidialApi\Exceptions\TransporterException;
use VicidialApi\ValueObjects\Transporter\Payload;

final class RemoteAgent
{
    public const REMOTE_AGENT_END_POINT = '/vicidial/vdremote.php';

    public const ACTION = 41111;

    protected Agent $agent;

    public function __construct(
        protected readonly TransporterContract $transporter,
    ) {
        $this->agent = new Agent($this->transporter);
    }

    /**
     * @throws ErrorException
     * @throws TransporterException
     */
    public function active(
        string $remoteAgentId,
        string $confExten,
        int $numberOfLines = 1,
    ): string {
        $payload = Payload::retrieveWithParameters(self::REMOTE_AGENT_END_POINT, [
            'status' => 'ACTIVE',
            'ADD' => self::ACTION,
            'remote_agent_id' => $remoteAgentId,
            'number_of_lines' => $numberOfLines,
            'conf_exten' => $confExten,
        ]);

        return $this->transporter->requestContent($payload);
    }

    /**
     * @throws ErrorException
     * @throws TransporterException
     */
    public function inactive(
        string $remoteAgentId,
        string $confExten,
        int $numberOfLines = 1,
    ): string {
        $payload = Payload::retrieveWithParameters(self::REMOTE_AGENT_END_POINT, [
            'status' => 'INACTIVE',
            'ADD' => self::ACTION,
            'remote_agent_id' => $remoteAgentId,
            'number_of_lines' => $numberOfLines,
            'conf_exten' => $confExten,
        ]);

        return $this->transporter->requestContent($payload);
    }

    /**
     * @throws \Exception
     */
    public function hangUp(string $agent, array $options): string
    {
        return $this->agent->raCallControl(
            $agent,
            $options
        );
    }

    public function transfer(string $agent, array $options): string
    {
        return $this->agent->raCallControl(
            $agent,
            $options
        );
    }

    public function ingroupTransfer(string $agent, array $options): string
    {
        return $this->agent->raCallControl(
            $agent,
            $options
        );
    }
}
