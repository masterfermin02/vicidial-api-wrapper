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
     * Output a list of audio files from the audio store.
     *
     * API user must have user_level 7 or higher. Optional: format (tab, link, selectframe), stage (date, size, name), comments.
     *
     * @param  array<string, string>  $options
     */
    public function soundsList(array $options): string
    {
        return $this->makeApiRequest('sounds_list', $options);
    }

    /**
     * Output a list of audio files from the audio store. Returns a VicidialResponse DTO.
     *
     * @param  array<string, string>  $options
     */
    public function soundsListResponse(array $options): VicidialResponse
    {
        $options['function'] = 'sounds_list';

        return $this->requestResponse(self::ADMIN_URL, $options);
    }

    /**
     * Update multiple leads in vicidial_list with a single set of field values.
     *
     * Required: lead_ids (comma separated), source. API user must have modify_leads = 1 and user_level 8 or higher.
     *
     * @param  array<string, string>  $options
     */
    public function batchUpdateLead(array $options): string
    {
        return $this->makeApiRequest('batch_update_lead', $options);
    }

    /**
     * Update multiple leads in vicidial_list with a single set of field values. Returns a VicidialResponse DTO.
     *
     * @param  array<string, string>  $options
     */
    public function batchUpdateLeadResponse(array $options): VicidialResponse
    {
        $options['function'] = 'batch_update_lead';

        return $this->requestResponse(self::ADMIN_URL, $options);
    }

    /**
     * Show the count of agents available to take calls in the selected in-groups.
     *
     * Required: in_groups or queue_groups (comma separated). Optional: display_field, stage=json, summary_name, ingroup_set_name.
     *
     * @param  array<string, string>  $options
     */
    public function agentInGroupAvailability(array $options): string
    {
        return $this->makeApiRequest('agent_ingroup_availability', $options);
    }

    /**
     * Show the count of agents available to take calls in the selected in-groups. Returns a VicidialResponse DTO.
     *
     * @param  array<string, string>  $options
     */
    public function agentInGroupAvailabilityResponse(array $options): VicidialResponse
    {
        $options['function'] = 'agent_ingroup_availability';

        return $this->requestResponse(self::ADMIN_URL, $options);
    }

    /**
     * Output all lead data for a lead, optionally including custom fields.
     *
     * Required: lead_id (or phone_number). Optional: custom_fields (Y/N), force_entry_list_id, archived_lead, stage, header.
     *
     * @param  array<string, string>  $options
     */
    public function leadAllInfo(array $options): string
    {
        return $this->makeApiRequest('lead_all_info', $options);
    }

    /**
     * Output all lead data for a lead, optionally including custom fields. Returns a VicidialResponse DTO.
     *
     * @param  array<string, string>  $options
     */
    public function leadAllInfoResponse(array $options): VicidialResponse
    {
        $options['function'] = 'lead_all_info';

        return $this->requestResponse(self::ADMIN_URL, $options);
    }

    /**
     * Search for leads in vicidial_list by one or more phone numbers.
     *
     * Required: phone_number (comma separated), source. Optional: records, archived_lead, header.
     *
     * @param  array<string, string>  $options
     */
    public function leadSearch(array $options): string
    {
        return $this->makeApiRequest('lead_search', $options);
    }

    /**
     * Search for leads in vicidial_list by one or more phone numbers. Returns a VicidialResponse DTO.
     *
     * @param  array<string, string>  $options
     */
    public function leadSearchResponse(array $options): VicidialResponse
    {
        $options['function'] = 'lead_search';

        return $this->requestResponse(self::ADMIN_URL, $options);
    }

    /**
     * Add new inbound DID entries copied from an existing DID.
     *
     * Required: source_did_pattern, new_dids (comma separated), source. Optional: did_description.
     *
     * @param  array<string, string>  $options
     */
    public function copyDid(array $options): string
    {
        return $this->makeApiRequest('copy_did', $options);
    }

    /**
     * Add new inbound DID entries copied from an existing DID. Returns a VicidialResponse DTO.
     *
     * @param  array<string, string>  $options
     */
    public function copyDidResponse(array $options): VicidialResponse
    {
        $options['function'] = 'copy_did';

        return $this->requestResponse(self::ADMIN_URL, $options);
    }

    /**
     * Remove a phone number from a DNC list.
     *
     * Required: phone_number, campaign_id (or SYSTEM_INTERNAL). API user needs "Delete From DNC Lists" enabled.
     *
     * @param  array<string, string>  $options
     */
    public function deleteDncPhone(array $options): string
    {
        return $this->makeApiRequest('delete_dnc_phone', $options);
    }

    /**
     * Remove a phone number from a DNC list. Returns a VicidialResponse DTO.
     *
     * @param  array<string, string>  $options
     */
    public function deleteDncPhoneResponse(array $options): VicidialResponse
    {
        $options['function'] = 'delete_dnc_phone';

        return $this->requestResponse(self::ADMIN_URL, $options);
    }

    /**
     * Remove a phone number from a Filter Phone Group.
     *
     * Required: phone_number, group.
     *
     * @param  array<string, string>  $options
     */
    public function deleteFpgPhone(array $options): string
    {
        return $this->makeApiRequest('delete_fpg_phone', $options);
    }

    /**
     * Remove a phone number from a Filter Phone Group. Returns a VicidialResponse DTO.
     *
     * @param  array<string, string>  $options
     */
    public function deleteFpgPhoneResponse(array $options): VicidialResponse
    {
        $options['function'] = 'delete_fpg_phone';

        return $this->requestResponse(self::ADMIN_URL, $options);
    }

    /**
     * Add a group of lead_ids (up to 1000) to the vicidial_hopper table.
     *
     * Required: lead_ids (comma separated), source. Optional: hopper_priority (-99..99), hopper_local_call_time_check (Y/N).
     *
     * @param  array<string, string>  $options
     */
    public function hopperBulkInsert(array $options): string
    {
        return $this->makeApiRequest('hopper_bulk_insert', $options);
    }

    /**
     * Add a group of lead_ids (up to 1000) to the vicidial_hopper table. Returns a VicidialResponse DTO.
     *
     * @param  array<string, string>  $options
     */
    public function hopperBulkInsertResponse(array $options): VicidialResponse
    {
        $options['function'] = 'hopper_bulk_insert';

        return $this->requestResponse(self::ADMIN_URL, $options);
    }

    /**
     * Update, add or list alternate dispo call URL entries for a campaign.
     *
     * Required: campaign_id, source, entry_type (campaign), url_type (dispo, start, addlead, noagent), alt_url_id (id, NEW or LIST).
     *
     * @param  array<string, string>  $options
     */
    public function updateAltUrl(array $options): string
    {
        return $this->makeApiRequest('update_alt_url', $options);
    }

    /**
     * Update, add or list alternate dispo call URL entries for a campaign. Returns a VicidialResponse DTO.
     *
     * @param  array<string, string>  $options
     */
    public function updateAltUrlResponse(array $options): VicidialResponse
    {
        $options['function'] = 'update_alt_url';

        return $this->requestResponse(self::ADMIN_URL, $options);
    }

    /**
     * Update, add, list or delete campaign preset entries.
     *
     * Required: campaign_id, source, preset_name. Optional: action (UPDATE, NEW, DELETE, LIST), preset_number, preset_dtmf, preset_hide_number.
     *
     * @param  array<string, string>  $options
     */
    public function updatePresets(array $options): string
    {
        return $this->makeApiRequest('update_presets', $options);
    }

    /**
     * Update, add, list or delete campaign preset entries. Returns a VicidialResponse DTO.
     *
     * @param  array<string, string>  $options
     */
    public function updatePresetsResponse(array $options): VicidialResponse
    {
        $options['function'] = 'update_presets';

        return $this->requestResponse(self::ADMIN_URL, $options);
    }

    /**
     * Move a lead from the archive back to the active leads table.
     *
     * Required: lead_id, source.
     *
     * @param  array<string, string>  $options
     */
    public function leadDearchive(array $options): string
    {
        return $this->makeApiRequest('lead_dearchive', $options);
    }

    /**
     * Move a lead from the archive back to the active leads table. Returns a VicidialResponse DTO.
     *
     * @param  array<string, string>  $options
     */
    public function leadDearchiveResponse(array $options): VicidialResponse
    {
        $options['function'] = 'lead_dearchive';

        return $this->requestResponse(self::ADMIN_URL, $options);
    }

    /**
     * Update a remote agent entry.
     *
     * Required: agent_user. Optional: status (ACTIVE/INACTIVE), campaign_id, number_of_lines.
     *
     * @param  array<string, string>  $options
     */
    public function updateRemoteAgent(array $options): string
    {
        return $this->makeApiRequest('update_remote_agent', $options);
    }

    /**
     * Update a remote agent entry. Returns a VicidialResponse DTO.
     *
     * @param  array<string, string>  $options
     */
    public function updateRemoteAgentResponse(array $options): VicidialResponse
    {
        $options['function'] = 'update_remote_agent';

        return $this->requestResponse(self::ADMIN_URL, $options);
    }

    /**
     * Display information about one user.
     *
     * Required: agent_user, source. Optional: stage (csv, tab, json, pipe), header (YES/NO).
     *
     * @param  array<string, string>  $options
     */
    public function userDetails(array $options): string
    {
        return $this->makeApiRequest('user_details', $options);
    }

    /**
     * Display information about one user. Returns a VicidialResponse DTO.
     *
     * @param  array<string, string>  $options
     */
    public function userDetailsResponse(array $options): VicidialResponse
    {
        $options['function'] = 'user_details';

        return $this->requestResponse(self::ADMIN_URL, $options);
    }

    /**
     * Copy an existing phone to a new extension.
     *
     * Required: extension, dialplan_number, voicemail_id, phone_login, server_ip, phone_full_name, outbound_cid, source_extension, source_server_ip.
     *
     * @param  array<string, string>  $options
     */
    public function copyPhone(array $options): string
    {
        return $this->makeApiRequest('copy_phone', $options);
    }

    /**
     * Copy an existing phone to a new extension. Returns a VicidialResponse DTO.
     *
     * @param  array<string, string>  $options
     */
    public function copyPhoneResponse(array $options): VicidialResponse
    {
        $options['function'] = 'copy_phone';

        return $this->requestResponse(self::ADMIN_URL, $options);
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
