<?php

declare(strict_types=1);

namespace VicidialApi\Contracts;

interface AdminContract
{
    /**
     * Get the version.
     *
     * @return string
     */
    public function version(): string;

    /**
     * Get the list of MOH.
     *
     * @param array<string, string> $options
     * @return string
     */
    public function mohList(array $options): string;

    /**
     * Get the list of VM.
     *
     * @param array<string, string> $options
     * @return string
     */
    public function vmList(array $options): string;

    /**
     * Get blind monitor information.
     *
     * @param array<string, string> $options
     * @return string
     */
    public function blindMonitor(array $options): string;

    /**
     * Get agent in group information.
     *
     * @param array<string, string> $options
     * @return string
     */
    public function agentInGroupInfo(array $options): string;

    /**
     * Get agent campaigns.
     *
     * @param array<string, string> $options
     * @return string
     */
    public function agentCampaigns(array $options): string;

    /**
     * Get the campaigns list.
     *
     * @param array<string, string> $options
     * @return string
     */
    public function campaignsList(array $options): string;

    /**
     * Get the hopper list.
     *
     * @param array<string, string> $options
     * @return string
     */
    public function hopperList(array $options): string;

    /**
     * Look up recording information.
     *
     * @param array<string, string> $options
     * @return string
     */
    public function recordingLookup(array $options): string;

    /**
     * Export DID log.
     *
     * @param array<string, string> $options
     * @return string
     */
    public function didLogExport(array $options): string;

    /**
     * Get phone number log.
     *
     * @param array<string, string> $options
     * @return string
     */
    public function phoneNumberLog(array $options): string;

    /**
     * Export agent stats.
     *
     * @param array<string, string> $options
     * @return string
     */
    public function agentStatsExport(array $options): string;

    /**
     * Get user group status.
     *
     * @param array<string, string> $options
     * @return string
     */
    public function userGroupStatus(array $options): string;

    /**
     * Get in-group status.
     *
     * @param array<string, string> $options
     * @return string
     */
    public function inGroupStatus(array $options): string;

    /**
     * Get agent status.
     *
     * @param array<string, string> $options
     * @return string
     */
    public function agentStatus(array $options): string;

    /**
     * Get call ID information.
     *
     * @param array<string, string> $options
     * @return string
     */
    public function callIdInfo(array $options): string;

    /**
     * Get lead field information.
     *
     * @param array<string, string> $options
     * @return string
     */
    public function leadFieldInfo(array $options): string;

    /**
     * Search lead status.
     *
     * @param array<string, string> $options
     * @return string
     */
    public function leadStatusSearch(array $options): string;

    /**
     * Get CCC lead information.
     *
     * @param array<string, string> $options
     * @return string
     */
    public function cccLeadInfo(array $options): string;

    /**
     * Get lead callback information.
     *
     * @param array<string, string> $options
     * @return string
     */
    public function leadCallbackInfo(array $options): string;

    /**
     * Update log entry.
     *
     * @param array<string, string> $options
     * @return string
     */
    public function updateLogEntry(array $options): string;

    /**
     * Add lead.
     *
     * @param array<string, string> $options
     * @return string
     */
    public function addLead(array $options): string;

    /**
     * Update lead.
     *
     * @param array<string, string> $options
     * @return string
     */
    public function updateLead(array $options): string;

    /**
     * Add user.
     *
     * @param array<string, string> $options
     * @return string
     */
    public function addUser(array $options): string;

    /**
     * Update user.
     *
     * @param array<string, string> $options
     * @return string
     */
    public function updateUser(array $options): string;

    /**
     * Add group alias.
     *
     * @param array<string, string> $options
     * @return string
     */
    public function addGroupAlias(array $options): string;

    /**
     * Add DNC phone.
     *
     * @param array<string, string> $options
     * @return string
     */
    public function addDncPhone(array $options): string;

    /**
     * Add FPG phone.
     *
     * @param array<string, string> $options
     * @return string
     */
    public function addFpgPhone(array $options): string;

    /**
     * Add phone.
     *
     * @param array<string, string> $options
     * @return string
     */
    public function addPhone(array $options): string;

    /**
     * Update phone.
     *
     * @param array<string, string> $options
     * @return string
     */
    public function updatePhone(array $options): string;

    /**
     * Add phone alias.
     *
     * @param array<string, string> $options
     * @return string
     */
    public function addPhoneAlias(array $options): string;

    /**
     * Update phone alias.
     *
     * @param array<string, string> $options
     * @return string
     */
    public function updatePhoneAlias(array $options): string;

    /**
     * Refresh server.
     *
     * @param array<string, string> $options
     * @return string
     */
    public function serverRefresh(array $options): string;

    /**
     * Add list.
     *
     * @param array<string, string> $options
     * @return string
     */
    public function addList(array $options): string;

    /**
     * Update list.
     *
     * @param array<string, string> $options
     * @return string
     */
    public function updateList(array $options): string;

    /**
     * Get list information.
     *
     * @param array<string, string> $options
     * @return string
     */
    public function listInfo(array $options): string;

    /**
     * Get list custom fields.
     *
     * @param array<string, string> $options
     * @return string
     */
    public function listCustomFields(array $options): string;

    /**
     * Check phone number.
     *
     * @param array<string, string> $options
     * @return string
     */
    public function checkPhoneNumber(array $options): string;

    /**
     * Get logged-in agents.
     *
     * @param array<string, string> $options
     * @return string
     */
    public function loggedInAgents(array $options): string;

    /**
     * Get call status stats.
     *
     * @param array<string, string> $options
     * @return string
     */
    public function callStatusStats(array $options): string;

    /**
     * Get call disposition report.
     *
     * @param array<string, string> $options
     * @return string
     */
    public function callDispoReport(array $options): string;

    /**
     * Update campaign.
     *
     * @param array<string, string> $options
     * @return string
     */
    public function updateCampaign(array $options): string;

    /**
     * Add DID.
     *
     * @param array<string, string> $options
     * @return string
     */
    public function addDid(array $options): string;

    /**
     * Update DID.
     *
     * @param array<string, string> $options
     * @return string
     */
    public function updateDid(array $options): string;

    /**
     * Update CID group entry.
     *
     * @param array<string, string> $options
     * @return string
     */
    public function updateCidGroupEntry(array $options): string;

    /**
     * Copy user.
     *
     * @param array<string, string> $options
     * @return string
     */
    public function copyUser(array $options): string;
}
