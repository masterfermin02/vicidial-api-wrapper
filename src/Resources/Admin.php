<?php

namespace VicidialApi\Resources;

use VicidialApi\Contracts\AdminContract;
use VicidialApi\Responses\VicidialResponse;
use VicidialApi\ValueObjects\Transporter\Payload;

final class Admin implements AdminContract
{
    use Concerns\HasResponseHelpers;
    use Concerns\Transportable;

    public const ADMIN_URL = '/vicidial/non_agent_api.php';

    public function version(): string
    {
        return $this->makeApiRequest('version', []);
    }

    public function versionResponse(): VicidialResponse
    {
        return $this->requestResponse(self::ADMIN_URL, ['function' => 'version']);
    }

    /**
     * @param  array<string, string>  $options
     */
    public function mohList(array $options): string
    {
        return $this->makeApiRequest('moh_list', $options);
    }

    /**
     * @param  array<string, string>  $options
     */
    public function mohListResponse(array $options): VicidialResponse
    {
        $options['function'] = 'moh_list';

        return $this->requestResponse(self::ADMIN_URL, $options);
    }

    /**
     * @param  array<string, string>  $options
     */
    public function vmList(array $options): string
    {
        return $this->makeApiRequest('vm_list', $options);
    }

    /**
     * @param  array<string, string>  $options
     */
    public function vmListResponse(array $options): VicidialResponse
    {
        $options['function'] = 'vm_list';

        return $this->requestResponse(self::ADMIN_URL, $options);
    }

    /**
     * @param  array<string, string>  $options
     */
    public function blindMonitor(array $options): string
    {
        return $this->makeApiRequest('blind_monitor', $options);
    }

    /**
     * @param  array<string, string>  $options
     */
    public function blindMonitorResponse(array $options): VicidialResponse
    {
        $options['function'] = 'blind_monitor';

        return $this->requestResponse(self::ADMIN_URL, $options);
    }

    /**
     * @param  array<string, string>  $options
     */
    public function agentInGroupInfo(array $options): string
    {
        return $this->makeApiRequest('agent_ingroup_info', $options);
    }

    /**
     * @param  array<string, string>  $options
     */
    public function agentInGroupInfoResponse(array $options): VicidialResponse
    {
        $options['function'] = 'agent_ingroup_info';

        return $this->requestResponse(self::ADMIN_URL, $options);
    }

    /**
     * @param  array<string, string>  $options
     */
    public function agentCampaigns(array $options): string
    {
        return $this->makeApiRequest('agent_campaigns', $options);
    }

    /**
     * @param  array<string, string>  $options
     */
    public function agentCampaignsResponse(array $options): VicidialResponse
    {
        $options['function'] = 'agent_campaigns';

        return $this->requestResponse(self::ADMIN_URL, $options);
    }

    /**
     * Get the campaigns list.
     *
     * @param  array<string, string>  $options
     */
    public function campaignsList(array $options): string
    {
        return $this->makeApiRequest('campaigns_list', $options);
    }

    /**
     * @param  array<string, string>  $options
     */
    public function campaignsListResponse(array $options): VicidialResponse
    {
        $options['function'] = 'campaigns_list';

        return $this->requestResponse(self::ADMIN_URL, $options);
    }

    /**
     * Get the hopper list.
     *
     * @param  array<string, string>  $options
     */
    public function hopperList(array $options): string
    {
        return $this->makeApiRequest('hopper_list', $options);
    }

    /**
     * @param  array<string, string>  $options
     */
    public function hopperListResponse(array $options): VicidialResponse
    {
        $options['function'] = 'hopper_list';

        return $this->requestResponse(self::ADMIN_URL, $options);
    }

    /**
     * Look up recording information.
     *
     * @param  array<string, string>  $options
     */
    public function recordingLookup(array $options): string
    {
        return $this->makeApiRequest('recording_lookup', $options);
    }

    /**
     * @param  array<string, string>  $options
     */
    public function recordingLookupResponse(array $options): VicidialResponse
    {
        $options['function'] = 'recording_lookup';

        return $this->requestResponse(self::ADMIN_URL, $options);
    }

    /**
     * Export DID log.
     *
     * @param  array<string, string>  $options
     */
    public function didLogExport(array $options): string
    {
        return $this->makeApiRequest('did_log_export', $options);
    }

    /**
     * @param  array<string, string>  $options
     */
    public function didLogExportResponse(array $options): VicidialResponse
    {
        $options['function'] = 'did_log_export';

        return $this->requestResponse(self::ADMIN_URL, $options);
    }

    /**
     * Get phone number log.
     *
     * @param  array<string, string>  $options
     */
    public function phoneNumberLog(array $options): string
    {
        return $this->makeApiRequest('phone_number_log', $options);
    }

    /**
     * @param  array<string, string>  $options
     */
    public function phoneNumberLogResponse(array $options): VicidialResponse
    {
        $options['function'] = 'phone_number_log';

        return $this->requestResponse(self::ADMIN_URL, $options);
    }

    /**
     * Export agent stats.
     *
     * @param  array<string, string>  $options
     */
    public function agentStatsExport(array $options): string
    {
        return $this->makeApiRequest('agent_stats_export', $options);
    }

    /**
     * @param  array<string, string>  $options
     */
    public function agentStatsExportResponse(array $options): VicidialResponse
    {
        $options['function'] = 'agent_stats_export';

        return $this->requestResponse(self::ADMIN_URL, $options);
    }

    /**
     * Get user group status.
     *
     * @param  array<string, string>  $options
     */
    public function userGroupStatus(array $options): string
    {
        return $this->makeApiRequest('user_group_status', $options);
    }

    /**
     * @param  array<string, string>  $options
     */
    public function userGroupStatusResponse(array $options): VicidialResponse
    {
        $options['function'] = 'user_group_status';

        return $this->requestResponse(self::ADMIN_URL, $options);
    }

    /**
     * Get in-group status.
     *
     * @param  array<string, string>  $options
     */
    public function inGroupStatus(array $options): string
    {
        return $this->makeApiRequest('in_group_status', $options);
    }

    /**
     * @param  array<string, string>  $options
     */
    public function inGroupStatusResponse(array $options): VicidialResponse
    {
        $options['function'] = 'in_group_status';

        return $this->requestResponse(self::ADMIN_URL, $options);
    }

    /**
     * Get agent status.
     *
     * @param  array<string, string>  $options
     */
    public function agentStatus(array $options): string
    {
        return $this->makeApiRequest('agent_status', $options);
    }

    /**
     * @param  array<string, string>  $options
     */
    public function agentStatusResponse(array $options): VicidialResponse
    {
        $options['function'] = 'agent_status';

        return $this->requestResponse(self::ADMIN_URL, $options);
    }

    /**
     * Get call ID information.
     *
     * @param  array<string, string>  $options
     */
    public function callIdInfo(array $options): string
    {
        return $this->makeApiRequest('callid_info', $options);
    }

    /**
     * @param  array<string, string>  $options
     */
    public function callIdInfoResponse(array $options): VicidialResponse
    {
        $options['function'] = 'callid_info';

        return $this->requestResponse(self::ADMIN_URL, $options);
    }

    /**
     * Get lead field information.
     *
     * @param  array<string, string>  $options
     */
    public function leadFieldInfo(array $options): string
    {
        return $this->makeApiRequest('lead_field_info', $options);
    }

    /**
     * @param  array<string, string>  $options
     */
    public function leadFieldInfoResponse(array $options): VicidialResponse
    {
        $options['function'] = 'lead_field_info';

        return $this->requestResponse(self::ADMIN_URL, $options);
    }

    /**
     * Search lead status.
     *
     * @param  array<string, string>  $options
     */
    public function leadStatusSearch(array $options): string
    {
        return $this->makeApiRequest('lead_status_search', $options);
    }

    /**
     * @param  array<string, string>  $options
     */
    public function leadStatusSearchResponse(array $options): VicidialResponse
    {
        $options['function'] = 'lead_status_search';

        return $this->requestResponse(self::ADMIN_URL, $options);
    }

    /**
     * Get CCC lead information.
     *
     * @param  array<string, string>  $options
     */
    public function cccLeadInfo(array $options): string
    {
        return $this->makeApiRequest('ccc_lead_info', $options);
    }

    /**
     * @param  array<string, string>  $options
     */
    public function cccLeadInfoResponse(array $options): VicidialResponse
    {
        $options['function'] = 'ccc_lead_info';

        return $this->requestResponse(self::ADMIN_URL, $options);
    }

    /**
     * Get lead callback information.
     *
     * @param  array<string, string>  $options
     */
    public function leadCallbackInfo(array $options): string
    {
        return $this->makeApiRequest('lead_callback_info', $options);
    }

    /**
     * @param  array<string, string>  $options
     */
    public function leadCallbackInfoResponse(array $options): VicidialResponse
    {
        $options['function'] = 'lead_callback_info';

        return $this->requestResponse(self::ADMIN_URL, $options);
    }

    /**
     * Update log entry.
     *
     * @param  array<string, string>  $options
     */
    public function updateLogEntry(array $options): string
    {
        return $this->makeApiRequest('update_log_entry', $options);
    }

    /**
     * @param  array<string, string>  $options
     */
    public function updateLogEntryResponse(array $options): VicidialResponse
    {
        $options['function'] = 'update_log_entry';

        return $this->requestResponse(self::ADMIN_URL, $options);
    }

    /**
     * Add lead.
     *
     * @param  array<string, string>  $options
     */
    public function addLead(array $options): string
    {
        return $this->makeApiRequest('add_lead', $options);
    }

    /**
     * @param  array<string, string>  $options
     */
    public function addLeadResponse(array $options): VicidialResponse
    {
        $options['function'] = 'add_lead';

        return $this->requestResponse(self::ADMIN_URL, $options);
    }

    /**
     * Update lead.
     *
     * @param  array<string, string>  $options
     */
    public function updateLead(array $options): string
    {
        return $this->makeApiRequest('update_lead', $options);
    }

    /**
     * @param  array<string, string>  $options
     */
    public function updateLeadResponse(array $options): VicidialResponse
    {
        $options['function'] = 'update_lead';

        return $this->requestResponse(self::ADMIN_URL, $options);
    }

    /**
     * Add user.
     *
     * @param  array<string, string>  $options
     */
    public function addUser(array $options): string
    {
        return $this->makeApiRequest('add_user', $options);
    }

    /**
     * Update user.
     *
     * @param  array<string, string>  $options
     */
    public function updateUser(array $options): string
    {
        return $this->makeApiRequest('update_user', $options);
    }

    /**
     * Add group alias.
     *
     * @param  array<string, string>  $options
     */
    public function addGroupAlias(array $options): string
    {
        return $this->makeApiRequest('add_group_alias', $options);
    }

    /**
     * Add DNC phone.
     *
     * @param  array<string, string>  $options
     */
    public function addDncPhone(array $options): string
    {
        return $this->makeApiRequest('add_dnc_phone', $options);
    }

    /**
     * Add FPG phone.
     *
     * @param  array<string, string>  $options
     */
    public function addFpgPhone(array $options): string
    {
        return $this->makeApiRequest('add_fpg_phone', $options);
    }

    /**
     * Add phone.
     *
     * @param  array<string, string>  $options
     */
    public function addPhone(array $options): string
    {
        return $this->makeApiRequest('add_phone', $options);
    }

    /**
     * Update phone.
     *
     * @param  array<string, string>  $options
     */
    public function updatePhone(array $options): string
    {
        return $this->makeApiRequest('update_phone', $options);
    }

    /**
     * Add phone alias.
     *
     * @param  array<string, string>  $options
     */
    public function addPhoneAlias(array $options): string
    {
        return $this->makeApiRequest('add_phone_alias', $options);
    }

    /**
     * Update phone alias.
     *
     * @param  array<string, string>  $options
     */
    public function updatePhoneAlias(array $options): string
    {
        return $this->makeApiRequest('update_phone_alias', $options);
    }

    /**
     * Refresh server.
     *
     * @param  array<string, string>  $options
     */
    public function serverRefresh(array $options): string
    {
        return $this->makeApiRequest('server_refresh', $options);
    }

    /**
     * Add list.
     *
     * @param  array<string, string>  $options
     */
    public function addList(array $options): string
    {
        return $this->makeApiRequest('add_list', $options);
    }

    /**
     * Update list.
     *
     * @param  array<string, string>  $options
     */
    public function updateList(array $options): string
    {
        return $this->makeApiRequest('update_list', $options);
    }

    /**
     * Get list information.
     *
     * @param  array<string, string>  $options
     */
    public function listInfo(array $options): string
    {
        return $this->makeApiRequest('list_info', $options);
    }

    /**
     * Get list custom fields.
     *
     * @param  array<string, string>  $options
     */
    public function listCustomFields(array $options): string
    {
        return $this->makeApiRequest('list_custom_fields', $options);
    }

    /**
     * Check phone number.
     *
     * @param  array<string, string>  $options
     */
    public function checkPhoneNumber(array $options): string
    {
        return $this->makeApiRequest('check_phone_number', $options);
    }

    /**
     * Get logged-in agents.
     *
     * @param  array<string, string>  $options
     */
    public function loggedInAgents(array $options): string
    {
        return $this->makeApiRequest('logged_in_agents', $options);
    }

    /**
     * Get call status stats.
     *
     * @param  array<string, string>  $options
     */
    public function callStatusStats(array $options): string
    {
        return $this->makeApiRequest('call_status_stats', $options);
    }

    /**
     * Get call disposition report.
     *
     * @param  array<string, string>  $options
     */
    public function callDispoReport(array $options): string
    {
        return $this->makeApiRequest('call_dispo_report', $options);
    }

    /**
     * Update campaign.
     *
     * @param  array<string, string>  $options
     */
    public function updateCampaign(array $options): string
    {
        return $this->makeApiRequest('update_campaign', $options);
    }

    /**
     * Add DID.
     *
     * @param  array<string, string>  $options
     */
    public function addDid(array $options): string
    {
        return $this->makeApiRequest('add_did', $options);
    }

    /**
     * Update DID.
     *
     * @param  array<string, string>  $options
     */
    public function updateDid(array $options): string
    {
        return $this->makeApiRequest('update_did', $options);
    }

    /**
     * Update CID group entry.
     *
     * @param  array<string, string>  $options
     */
    public function updateCidGroupEntry(array $options): string
    {
        return $this->makeApiRequest('update_cid_group_entry', $options);
    }

    /**
     * @param  array<string, string>  $options
     */
    public function copyUser(array $options): string
    {
        return $this->makeApiRequest('copy_user', $options);
    }

    /**
     * Helper method to make the actual API request.
     *
     * @param  array<string, string>  $options
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
