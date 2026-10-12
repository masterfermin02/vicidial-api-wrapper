<?php

declare(strict_types=1);

namespace VicidialApi\Contracts;

use VicidialApi\Parameters\AddLeadParameters;
use VicidialApi\Parameters\UpdateLeadParameters;

interface AdminContract
{
    /**
     * Get the version.
     */
    public function version(): string;

    /**
     * Get the list of MOH.
     *
     * @param  array<string, string>  $options
     */
    public function mohList(array $options): string;

    /**
     * Get the list of VM.
     *
     * @param  array<string, string>  $options
     */
    public function vmList(array $options): string;

    /**
     * Get blind monitor information.
     *
     * @param  array<string, string>  $options
     */
    public function blindMonitor(array $options): string;

    /**
     * Get agent in group information.
     *
     * @param  array<string, string>  $options
     */
    public function agentInGroupInfo(array $options): string;

    /**
     * Get agent campaigns.
     *
     * @param  array<string, string>  $options
     */
    public function agentCampaigns(array $options): string;

    /**
     * Get the campaigns list.
     *
     * @param  array<string, string>  $options
     */
    public function campaignsList(array $options): string;

    /**
     * Get the hopper list.
     *
     * @param  array<string, string>  $options
     */
    public function hopperList(array $options): string;

    /**
     * Look up recording information.
     *
     * @param  array<string, string>  $options
     */
    public function recordingLookup(array $options): string;

    /**
     * Export DID log.
     *
     * @param  array<string, string>  $options
     */
    public function didLogExport(array $options): string;

    /**
     * Get phone number log.
     *
     * @param  array<string, string>  $options
     */
    public function phoneNumberLog(array $options): string;

    /**
     * Export agent stats.
     *
     * @param  array<string, string>  $options
     */
    public function agentStatsExport(array $options): string;

    /**
     * Get user group status.
     *
     * @param  array<string, string>  $options
     */
    public function userGroupStatus(array $options): string;

    /**
     * Get in-group status.
     *
     * @param  array<string, string>  $options
     */
    public function inGroupStatus(array $options): string;

    /**
     * Get agent status.
     *
     * @param  array<string, string>  $options
     */
    public function agentStatus(array $options): string;

    /**
     * Get call ID information.
     *
     * @param  array<string, string>  $options
     */
    public function callIdInfo(array $options): string;

    /**
     * Get lead field information.
     *
     * @param  array<string, string>  $options
     */
    public function leadFieldInfo(array $options): string;

    /**
     * Search lead status.
     *
     * @param  array<string, string>  $options
     */
    public function leadStatusSearch(array $options): string;

    /**
     * Get CCC lead information.
     *
     * @param  array<string, string>  $options
     */
    public function cccLeadInfo(array $options): string;

    /**
     * Get lead callback information.
     *
     * @param  array<string, string>  $options
     */
    public function leadCallbackInfo(array $options): string;

    /**
     * Update log entry.
     *
     * @param  array<string, string>  $options
     */
    public function updateLogEntry(array $options): string;

    /**
     * Add lead.
     *
     * @param  array<string, string>|AddLeadParameters  $options
     */
    public function addLead(array|AddLeadParameters $options): string;

    /**
     * Update lead.
     *
     * @param  array<string, string>|UpdateLeadParameters  $options
     */
    public function updateLead(array|UpdateLeadParameters $options): string;

    /**
     * Add user.
     *
     * @param  array<string, string>  $options
     */
    public function addUser(array $options): string;

    /**
     * Update user.
     *
     * @param  array<string, string>  $options
     */
    public function updateUser(array $options): string;

    /**
     * Add group alias.
     *
     * @param  array<string, string>  $options
     */
    public function addGroupAlias(array $options): string;

    /**
     * Add DNC phone.
     *
     * @param  array<string, string>  $options
     */
    public function addDncPhone(array $options): string;

    /**
     * Add FPG phone.
     *
     * @param  array<string, string>  $options
     */
    public function addFpgPhone(array $options): string;

    /**
     * Add phone.
     *
     * @param  array<string, string>  $options
     */
    public function addPhone(array $options): string;

    /**
     * Update phone.
     *
     * @param  array<string, string>  $options
     */
    public function updatePhone(array $options): string;

    /**
     * Add phone alias.
     *
     * @param  array<string, string>  $options
     */
    public function addPhoneAlias(array $options): string;

    /**
     * Update phone alias.
     *
     * @param  array<string, string>  $options
     */
    public function updatePhoneAlias(array $options): string;

    /**
     * Refresh server.
     *
     * @param  array<string, string>  $options
     */
    public function serverRefresh(array $options): string;

    /**
     * Add list.
     *
     * @param  array<string, string>  $options
     */
    public function addList(array $options): string;

    /**
     * Update list.
     *
     * @param  array<string, string>  $options
     */
    public function updateList(array $options): string;

    /**
     * Get list information.
     *
     * @param  array<string, string>  $options
     */
    public function listInfo(array $options): string;

    /**
     * Get list custom fields.
     *
     * @param  array<string, string>  $options
     */
    public function listCustomFields(array $options): string;

    /**
     * Check phone number.
     *
     * @param  array<string, string>  $options
     */
    public function checkPhoneNumber(array $options): string;

    /**
     * Get logged-in agents.
     *
     * @param  array<string, string>  $options
     */
    public function loggedInAgents(array $options): string;

    /**
     * Get call status stats.
     *
     * @param  array<string, string>  $options
     */
    public function callStatusStats(array $options): string;

    /**
     * Get call disposition report.
     *
     * @param  array<string, string>  $options
     */
    public function callDispoReport(array $options): string;

    /**
     * Update campaign.
     *
     * @param  array<string, string>  $options
     */
    public function updateCampaign(array $options): string;

    /**
     * Add DID.
     *
     * @param  array<string, string>  $options
     */
    public function addDid(array $options): string;

    /**
     * Update DID.
     *
     * @param  array<string, string>  $options
     */
    public function updateDid(array $options): string;

    /**
     * Update CID group entry.
     *
     * @param  array<string, string>  $options
     */
    public function updateCidGroupEntry(array $options): string;

    /**
     * Copy user.
     *
     * @param  array<string, string>  $options
     */
    public function copyUser(array $options): string;

    /**
     * Output a list of audio files from the audio store.
     *
     * @param  array<string, string>  $options
     */
    public function soundsList(array $options): string;

    /**
     * Update multiple leads in vicidial_list with a single set of field values.
     *
     * @param  array<string, string>  $options
     */
    public function batchUpdateLead(array $options): string;

    /**
     * Show the count of agents available to take calls in the selected in-groups.
     *
     * @param  array<string, string>  $options
     */
    public function agentInGroupAvailability(array $options): string;

    /**
     * Output all lead data for a lead, optionally including custom fields.
     *
     * @param  array<string, string>  $options
     */
    public function leadAllInfo(array $options): string;

    /**
     * Search for leads in vicidial_list by one or more phone numbers.
     *
     * @param  array<string, string>  $options
     */
    public function leadSearch(array $options): string;

    /**
     * Add new inbound DID entries copied from an existing DID.
     *
     * @param  array<string, string>  $options
     */
    public function copyDid(array $options): string;

    /**
     * Remove a phone number from a DNC list.
     *
     * @param  array<string, string>  $options
     */
    public function deleteDncPhone(array $options): string;

    /**
     * Remove a phone number from a Filter Phone Group.
     *
     * @param  array<string, string>  $options
     */
    public function deleteFpgPhone(array $options): string;

    /**
     * Add a group of lead_ids (up to 1000) to the vicidial_hopper table.
     *
     * @param  array<string, string>  $options
     */
    public function hopperBulkInsert(array $options): string;

    /**
     * Update, add or list alternate dispo call URL entries for a campaign.
     *
     * @param  array<string, string>  $options
     */
    public function updateAltUrl(array $options): string;

    /**
     * Update, add, list or delete campaign preset entries.
     *
     * @param  array<string, string>  $options
     */
    public function updatePresets(array $options): string;

    /**
     * Move a lead from the archive back to the active leads table.
     *
     * @param  array<string, string>  $options
     */
    public function leadDearchive(array $options): string;

    /**
     * Update a remote agent entry.
     *
     * @param  array<string, string>  $options
     */
    public function updateRemoteAgent(array $options): string;

    /**
     * Display information about one user.
     *
     * @param  array<string, string>  $options
     */
    public function userDetails(array $options): string;

    /**
     * Copy an existing phone to a new extension.
     *
     * @param  array<string, string>  $options
     */
    public function copyPhone(array $options): string;
}
