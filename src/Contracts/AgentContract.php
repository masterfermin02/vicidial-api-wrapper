<?php

declare(strict_types=1);

namespace VicidialApi\Contracts;

use VicidialApi\Parameters\DialParameters;
use VicidialApi\Parameters\DispoParameters;
use VicidialApi\Responses\VicidialResponse;

/**
 * Agent API (`/agc/api.php`). Each function has a raw-string variant and a
 * `...Response()` variant returning a parsed VicidialResponse.
 */
interface AgentContract
{
    public function hangup(string $agentUser): string;

    public function hangupResponse(string $agentUser): VicidialResponse;

    /**
     * @param  array<string, string>|DispoParameters  $options
     */
    public function dispo(string $agentUser, array|DispoParameters $options): string;

    /**
     * @param  array<string, string>|DispoParameters  $options
     */
    public function dispoResponse(string $agentUser, array|DispoParameters $options): VicidialResponse;

    public function pause(string $agentUser, string $status): string;

    public function pauseResponse(string $agentUser, string $status): VicidialResponse;

    public function pauseCode(string $agentUser, string $code): string;

    public function pauseCodeResponse(string $agentUser, string $code): VicidialResponse;

    public function webserver(): string;

    public function webserverResponse(): VicidialResponse;

    public function version(): string;

    public function versionResponse(): VicidialResponse;

    public function logout(string $agentUser): string;

    public function logoutResponse(string $agentUser): VicidialResponse;

    /**
     * @param  array<string, string>|DialParameters  $options
     */
    public function dial(string $agentUser, array|DialParameters $options): string;

    /**
     * @param  array<string, string>|DialParameters  $options
     */
    public function dialResponse(string $agentUser, array|DialParameters $options): VicidialResponse;

    public function previewDial(string $agentUser, string $value): string;

    public function previewDialResponse(string $agentUser, string $value): VicidialResponse;

    /**
     * @param  array<string, string>  $options
     */
    public function addLead(string $agentUser, array $options): string;

    /**
     * @param  array<string, string>  $options
     */
    public function addLeadResponse(string $agentUser, array $options): VicidialResponse;

    /**
     * @param  array<string, string>  $options
     */
    public function changeInGroups(string $agentUser, array $options): string;

    /**
     * @param  array<string, string>  $options
     */
    public function changeInGroupsResponse(string $agentUser, array $options): VicidialResponse;

    /**
     * @param  array<string, string>  $fieldsToUpdate
     */
    public function updateFields(string $agentUser, array $fieldsToUpdate): string;

    /**
     * @param  array<string, string>  $fieldsToUpdate
     */
    public function updateFieldsResponse(string $agentUser, array $fieldsToUpdate): VicidialResponse;

    /**
     * @param  array<string, string>  $options
     */
    public function setTimerAction(string $agentUser, array $options): string;

    /**
     * @param  array<string, string>  $options
     */
    public function setTimerActionResponse(string $agentUser, array $options): VicidialResponse;

    /**
     * @param  array<string, string>  $options
     */
    public function stLoginLog(array $options): string;

    /**
     * @param  array<string, string>  $options
     */
    public function stLoginLogResponse(array $options): VicidialResponse;

    /**
     * @param  array<string, string>  $options
     */
    public function stGetAgentActiveLead(array $options): string;

    /**
     * @param  array<string, string>  $options
     */
    public function stGetAgentActiveLeadResponse(array $options): VicidialResponse;

    /**
     * @param  array<string, string>  $options
     */
    public function raCallControl(string $agent, array $options): string;

    /**
     * @param  array<string, string>  $options
     */
    public function raCallControlResponse(string $agent, array $options): VicidialResponse;

    /**
     * @param  array<string, string>  $options
     */
    public function sendDtmf(array $options): string;

    /**
     * @param  array<string, string>  $options
     */
    public function sendDtmfResponse(array $options): VicidialResponse;

    /**
     * @param  array<string, string>  $options
     */
    public function transferConference(string $agent, array $options): string;

    /**
     * @param  array<string, string>  $options
     */
    public function transferConferenceResponse(string $agent, array $options): VicidialResponse;

    /**
     * @param  array<string, string>  $options
     */
    public function parkCall(string $agent, array $options): string;

    /**
     * @param  array<string, string>  $options
     */
    public function parkCallResponse(string $agent, array $options): VicidialResponse;

    public function callAgent(string $agentUser, string $value): string;

    public function callAgentResponse(string $agentUser, string $value): VicidialResponse;

    /**
     * @param  array<string, string>  $options
     */
    public function recording(string $agent, array $options): string;

    /**
     * @param  array<string, string>  $options
     */
    public function recordingResponse(string $agent, array $options): VicidialResponse;

    public function webPhoneUrl(string $agentUser, string $value): string;

    public function webPhoneUrlResponse(string $agentUser, string $value): VicidialResponse;

    /**
     * @param  array<string, string>  $options
     */
    public function audioPlayBack(string $agentUser, array $options): string;

    /**
     * @param  array<string, string>  $options
     */
    public function audioPlayBackResponse(string $agentUser, array $options): VicidialResponse;

    public function switchLead(string $agentUser, string $leadId): string;

    public function switchLeadResponse(string $agentUser, string $leadId): VicidialResponse;

    /**
     * @param  array<string, string>  $options
     */
    public function vmMessage(string $agentUser, array $options): string;

    /**
     * @param  array<string, string>  $options
     */
    public function vmMessageResponse(string $agentUser, array $options): VicidialResponse;

    public function callsInQueueCount(string $agentUser, string $status): string;

    public function callsInQueueCountResponse(string $agentUser, string $status): VicidialResponse;

    public function forceFronterLeave3way(string $agentUser, string $status): string;

    public function forceFronterLeave3wayResponse(string $agentUser, string $status): VicidialResponse;

    public function forceFronterAudioStop(string $agentUser, string $status): string;

    public function forceFronterAudioStopResponse(string $agentUser, string $status): VicidialResponse;

    /**
     * @param  array<string, string>  $options
     */
    public function stereoRecording(string $agentUser, array $options): string;

    /**
     * @param  array<string, string>  $options
     */
    public function stereoRecordingResponse(string $agentUser, array $options): VicidialResponse;

    /**
     * @param  array<string, string>  $options
     */
    public function sendNotification(array $options): string;

    /**
     * @param  array<string, string>  $options
     */
    public function sendNotificationResponse(array $options): VicidialResponse;

    /**
     * @param  array<string, string>  $options
     */
    public function refreshPanel(string $agentUser, array $options): string;

    /**
     * @param  array<string, string>  $options
     */
    public function refreshPanelResponse(string $agentUser, array $options): VicidialResponse;
}
