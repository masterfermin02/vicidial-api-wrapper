<?php

declare(strict_types=1);

namespace VicidialApi\Contracts;

interface AgentContract
{
    public function hangup(string $agentUser): string;

    /**
     * Creates the URL for  the external_status method and calls 'call_api_url' to execute it
     * @param array<string, string> $options
     * @return string
     */
    public function dispo(string $agentUser, array $options): string;

    /**
     * Creates the URL for  the external_pause method and calls 'call_api_url' to execute it
     *
     * @return string
     */
    public function pause(string $agentUser, string $status): string;


    /**
     * Creates the URL for  the pause_code method and calls 'call_api_url' to execute it
     * @return string
     */
    public function pauseCode(string $agentUser, int $code): string;

    /**
     * Creates the URL for the webserver method and calls 'call_api_url' to execute it
     * @return string
     */
    public function webserver(): string;

    /**
     * Creates the URL for the version method and calls 'call_api_url' to execute it
     * @return string
     */
    public function version(): string;

    /**
     * Creates the URL for the logout method and calls 'call_api_url' to execute it
     * @return string
     */
    public function logout(string $agentUser): string;

    /**
     * Creates the URL for  the external_dial method and calls 'call_api_url' to execute it
     * @param array<string, string> $options
     * @return string
     */
    public function dial(string $agentUser, array $options): string;

    /**
     * Sends a SKIP, DIALONLY, ALTDIAL, ADR3DIAL or FINISH when a lead is being previewed or in Manual Alt Dial
     * @return string
     */
    public function previewDial(string $agentUser, string $value): string;

    /**
     * Adds a lead in the manual dial list of the campaign for logged-in agent. A much simplified add lead function compared to the Non-Agent API function
     * @param array<string, string> $options
     * @return string
     */
    public function addLead(string $agentUser, array $options): string;

    /**
     * This function will change the selected in-groups for an agent that is logged into a campaign that allows
     * for inbound calls to be handled. Allows the selected in-groups for an agent to be changed while they are
     * logged-in to the ViciDial Agent screen only. Once changed in this way, the agent would need to log out
     * and back in to be able to select in-groups themselves(If Agent Choose In-Groups is enabled for that user).
     * The blended checkbox can also be changed using this function. The API user performing this function must
     * have vicidial_users.change_agent_campaign = 1.
     *
     * @param array<string, string> $options
     * @return string
     */
    public function changeInGroups(string $agentUser, array $options): string;

    /**
     * Creates the URL for  the external_dial method and calls 'call_api_url' to execute it
     *
     * @param array<string, string> $fieldsToUpdate
     * @return string
     */
    public function updateFields(string $agentUser, array $fieldsToUpdate): string;

    /**
     * Updates the fields that are specified with the values. This will update the data
     * that is on the agent's screen in the customer information section.
     *
     * @param array<string, string> $options
     * @return string
     */
    public function setTimerAction(string $agentUser, array $options): string;

    /**
     * Looks up the vicidial_users.custom_three field(as "agentId") to associate with a vicidial user ID.
     * If found it will populate the custom_four field with a "teamId" value, then output the vicidial user ID
     *
     * @param array<string, string> $options
     * @return string
     */
    public function stLoginLog(array $options): string;

    /**
     * Looks up the vicidial_users.custom_three field(as "agentId") to associate with a vicidial user ID.
     * If found it will output the active lead_id and phone number, vendor_lead_code, province, security_phrase and source_id fields.
     *
     * @param array<string, string> $options
     * @return string
     */
    public function stGetAgentActiveLead(array $options): string;

    /**
     *
     * @param array<string, string> $options
     * @return string
     */
    public function raCallControl(string $agent, array $options): string;

    /**
     *
     * @param array<string, string> $options
     * @return string
     */
    public function sendDtmf(array $options): string;

    /**
     *
     * @param array<string, string> $options
     * @return string
     */
    public function transferConference(string $agent, array $options): string;

    /**
     *
     * @param array<string, string> $options
     * @return string
     */
    public function parkCall(string $agent, array $options): string;

    /**
     *
     * @return string
     */
    public function callAgent(string $agentUser, string $value): string;

    /**
     *
     * @param array<string, string> $options
     * @return string
     */
    public function recording(string $agent, array $options): string;

    /**
     *
     * @return string
     */
    public function webPhoneUrl(string $agentUser, string $value): string;

    /**
     *
     * @param array<string, string> $options
     * @return string
     */
    public function audioPlayBack(string $agentUser, array $options): string;

    /**
     * Creates the URL for  the pause_code method and calls 'call_api_url' to execute it
     * @return string
     */
    public function switchLead(string $agentUser, string $leadId): string;

    /**
     * Set a custom voicemail message to be played when agent clicks the VM button on the agent screen
     *
     * @param array<string, string> $options
     *
     * @return string
     */
    public function vmMessage(string $agentUser, array $options): string;

    /**
     * display a count of the calls waiting in queue for the specific agent
     * @return string
     */
    public function callsInQueueCount(string $agentUser, string $status): string;

    /**
     *
     * @return string
     */
    public function forceFronterLeave3way(string $agentUser, string $status): string;

    /**
     *
     * @return string
     */
    public function forceFronterAudioStop(string $agentUser, string $status): string;
}
