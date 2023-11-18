<?php

namespace VicidialApi\Resources;

use VicidialApi\Contracts\AdminContract;
use VicidialApi\ValueObjects\Transporter\Payload;

class Admin implements AdminContract
{
    use Concerns\Transportable;

    const ADMIN_URL = '/vicidial/non_agent_api.php';

    public function version(): string
    {
        return $this->makeApiRequest('version', []);
    }

    public function mohList(array $options): string
    {
        return $this->makeApiRequest('moh_list', $options);
    }

    public function vmList(array $options): string
    {
        return $this->makeApiRequest('vm_list', $options);
    }

    public function blindMonitor(array $options): string
    {
        return $this->makeApiRequest('blind_monitor', $options);
    }

    public function agentInGroupInfo(array $options): string
    {
        return $this->makeApiRequest('agent_ingroup_info', $options);
    }

    public function agentCampaigns(array $options): string
    {
        return $this->makeApiRequest('agent_campaigns', $options);
    }

    /**
     * Get the campaigns list.
     *
     * @param array<string, string> $options
     * @return string
     */
    public function campaignsList(array $options): string
    {
        return $this->makeApiRequest('campaigns_list', $options);
    }

    /**
     * Get the hopper list.
     *
     * @param array<string, string> $options
     * @return string
     */
    public function hopperList(array $options): string
    {
        return $this->makeApiRequest('hopper_list', $options);
    }

    /**
     * Look up recording information.
     *
     * @param array<string, string> $options
     * @return string
     */
    public function recordingLookup(array $options): string
    {
        return $this->makeApiRequest('recording_lookup', $options);
    }

    /**
     * Export DID log.
     *
     * @param array<string, string> $options
     * @return string
     */
    public function didLogExport(array $options): string
    {
        return $this->makeApiRequest('did_log_export', $options);
    }

    /**
     * Get phone number log.
     *
     * @param array<string, string> $options
     * @return string
     */
    public function phoneNumberLog(array $options): string
    {
        return $this->makeApiRequest('phone_number_log', $options);
    }

    /**
     * Export agent stats.
     *
     * @param array<string, string> $options
     * @return string
     */
    public function agentStatsExport(array $options): string
    {
        return $this->makeApiRequest('agent_stats_export', $options);
    }

    /**
     * Get user group status.
     *
     * @param array<string, string> $options
     * @return string
     */
    public function userGroupStatus(array $options): string
    {
        return $this->makeApiRequest('user_group_status', $options);
    }

    /**
     * Get in-group status.
     *
     * @param array<string, string> $options
     * @return string
     */
    public function inGroupStatus(array $options): string
    {
        return $this->makeApiRequest('in_group_status', $options);
    }

    /**
     * Get agent status.
     *
     * @param array<string, string> $options
     * @return string
     */
    public function agentStatus(array $options): string
    {
        return $this->makeApiRequest('agent_status', $options);
    }

    /**
     * Get call ID information.
     *
     * @param array<string, string> $options
     * @return string
     */
    public function callIdInfo(array $options): string
    {
        return $this->makeApiRequest('callid_info', $options);
    }

    /**
     * Get lead field information.
     *
     * @param array<string, string> $options
     * @return string
     */
    public function leadFieldInfo(array $options): string
    {
        return $this->makeApiRequest('lead_field_info', $options);
    }

    /**
     * Search lead status.
     *
     * @param array<string, string> $options
     * @return string
     */
    public function leadStatusSearch(array $options): string
    {
        return $this->makeApiRequest('lead_status_search', $options);
    }

    /**
     * Get CCC lead information.
     *
     * @param array<string, string> $options
     * @return string
     */
    public function cccLeadInfo(array $options): string
    {
        return $this->makeApiRequest('ccc_lead_info', $options);
    }

    /**
     * Get lead callback information.
     *
     * @param array<string, string> $options
     * @return string
     */
    public function leadCallbackInfo(array $options): string
    {
        return $this->makeApiRequest('lead_callback_info', $options);
    }

    /**
     * Update log entry.
     *
     * @param array<string, string> $options
     * @return string
     */
    public function updateLogEntry(array $options): string
    {
        return $this->makeApiRequest('update_log_entry', $options);
    }

    /**
     * Add lead.
     *
     * @param array<string, string> $options
     * @return string
     */
    public function addLead(array $options): string
    {
        return $this->makeApiRequest('add_lead', $options);
    }

    /**
     * Update lead.
     *
     * @param array<string, string> $options
     * @return string
     */
    public function updateLead(array $options): string
    {
        return $this->makeApiRequest('update_lead', $options);
    }

    /**
     * Add user.
     *
     * @param array<string, string> $options
     * @return string
     */
    public function addUser(array $options): string
    {
        return $this->makeApiRequest('add_user', $options);
    }

    /**
     * Update user.
     *
     * @param array<string, string> $options
     * @return string
     */
    public function updateUser(array $options): string
    {
        return $this->makeApiRequest('update_user', $options);
    }

    /**
     * Add group alias.
     *
     * @param array<string, string> $options
     * @return string
     */
    public function addGroupAlias(array $options): string
    {
        return $this->makeApiRequest('add_group_alias', $options);
    }

    /**
     * Add DNC phone.
     *
     * @param array<string, string> $options
     * @return string
     */
    public function addDncPhone(array $options): string
    {
        return $this->makeApiRequest('add_dnc_phone', $options);
    }

    /**
     * Add FPG phone.
     *
     * @param array<string, string> $options
     * @return string
     */
    public function addFpgPhone(array $options): string
    {
        return $this->makeApiRequest('add_fpg_phone', $options);
    }

    /**
     * Add phone.
     *
     * @param array<string, string> $options
     * @return string
     */
    public function addPhone(array $options): string
    {
        return $this->makeApiRequest('add_phone', $options);
    }

    /**
     * Update phone.
     *
     * @param array<string, string> $options
     * @return string
     */
    public function updatePhone(array $options): string
    {
        return $this->makeApiRequest('update_phone', $options);
    }

    /**
     * Add phone alias.
     *
     * @param array<string, string> $options
     * @return string
     */
    public function addPhoneAlias(array $options): string
    {
        return $this->makeApiRequest('add_phone_alias', $options);
    }

    /**
     * Update phone alias.
     *
     * @param array<string, string> $options
     * @return string
     */
    public function updatePhoneAlias(array $options): string
    {
        return $this->makeApiRequest('update_phone_alias', $options);
    }

    /**
     * Refresh server.
     *
     * @param array<string, string> $options
     * @return string
     */
    public function serverRefresh(array $options): string
    {
        return $this->makeApiRequest('server_refresh', $options);
    }

    /**
     * Add list.
     *
     * @param array<string, string> $options
     * @return string
     */
    public function addList(array $options): string
    {
        return $this->makeApiRequest('add_list', $options);
    }

    /**
     * Update list.
     *
     * @param array<string, string> $options
     * @return string
     */
    public function updateList(array $options): string
    {
        return $this->makeApiRequest('update_list', $options);
    }

    /**
     * Get list information.
     *
     * @param array<string, string> $options
     * @return string
     */
    public function listInfo(array $options): string
    {
        return $this->makeApiRequest('list_info', $options);
    }

    /**
     * Get list custom fields.
     *
     * @param array<string, string> $options
     * @return string
     */
    public function listCustomFields(array $options): string
    {
        return $this->makeApiRequest('list_custom_fields', $options);
    }

    /**
     * Check phone number.
     *
     * @param array<string, string> $options
     * @return string
     */
    public function checkPhoneNumber(array $options): string
    {
        return $this->makeApiRequest('check_phone_number', $options);
    }

    /**
     * Get logged-in agents.
     *
     * @param array<string, string> $options
     * @return string
     */
    public function loggedInAgents(array $options): string
    {
        return $this->makeApiRequest('logged_in_agents', $options);
    }

    /**
     * Get call status stats.
     *
     * @param array<string, string> $options
     * @return string
     */
    public function callStatusStats(array $options): string
    {
        return $this->makeApiRequest('call_status_stats', $options);
    }

    /**
     * Get call disposition report.
     *
     * @param array<string, string> $options
     * @return string
     */
    public function callDispoReport(array $options): string
    {
        return $this->makeApiRequest('call_dispo_report', $options);
    }

    /**
     * Update campaign.
     *
     * @param array<string, string> $options
     * @return string
     */
    public function updateCampaign(array $options): string
    {
        return $this->makeApiRequest('update_campaign', $options);
    }

    /**
     * Add DID.
     *
     * @param array<string, string> $options
     * @return string
     */
    public function addDid(array $options): string
    {
        return $this->makeApiRequest('add_did', $options);
    }

    /**
     * Update DID.
     *
     * @param array<string, string> $options
     * @return string
     */
    public function updateDid(array $options): string
    {
        return $this->makeApiRequest('update_did', $options);
    }

    /**
     * Update CID group entry.
     *
     * @param array<string, string> $options
     * @return string
     */
    public function updateCidGroupEntry(array $options): string
    {
        return $this->makeApiRequest('update_cid_group_entry', $options);
    }

    public function copyUser(array $options): string
    {
        return $this->makeApiRequest('copy_user', $options);
    }

    /**
     * Helper method to make the actual API request.
     *
     * @param string $function
     * @param array<string, string> $options
     * @return string
     */
    private function makeApiRequest(string $function, array $options): string
    {
        $options = array_merge($options, [
                'function' => $function,
            ]);

        $payload = Payload::retrieveWithParameters(self::ADMIN_URL, $options);

        return $this->transporter->requestContent($payload);
    }
}
