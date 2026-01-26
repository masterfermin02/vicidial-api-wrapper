<?php

declare(strict_types=1);

namespace Tests\Fixtures;

/**
 * Fixture repository for known Vicidial API responses.
 * Based on actual Vicidial API documentation and real-world responses.
 */
final class VicidialResponses
{
    /**
     * Success response for external_pause function
     */
    public static function externalPauseSuccess(): string
    {
        return 'SUCCESS: external_pause function set - PAUSE|1232020456|6666';
    }

    /**
     * Error response when agent is not logged in
     */
    public static function agentNotLoggedInError(): string
    {
        return 'ERROR: agent_user is not logged in - 6666';
    }

    /**
     * Version information response
     */
    public static function versionInfo(): string
    {
        return 'VERSION: 2.14.0-1|BUILD: 210901-1234|DATE: 2021-09-01 12:34:56|EPOCH: 1630498496';
    }

    /**
     * Success response for status update
     */
    public static function statusUpdateSuccess(): string
    {
        return 'SUCCESS: status - READY';
    }

    /**
     * Webserver settings response (multi-line key-value)
     */
    public static function webserverSettings(): string
    {
        return <<<'EOT'
set.timezone: America/New_York
abbr.timezone: EDT
local.gmt: -04:00
epoch: 1630498496
date: 2021-09-01 12:34:56
EOT;
    }

    /**
     * Lead list response
     */
    public static function leadList(): string
    {
        return 'user|allowed_campaigns|allowed_ingroups|closer_campaigns|group_id|user_level|phone_login|phone_pass';
    }

    /**
     * Campaign list with pipe-delimited data
     */
    public static function campaignList(): string
    {
        return 'campaign_id|campaign_name|active|dial_status_a|dial_status_b|dial_status_c';
    }

    /**
     * Success response with multiple data points
     */
    public static function callPlacedSuccess(): string
    {
        return 'SUCCESS: Call placed - CALL_ID|1234567890|PHONE|5551234567|STATUS|SENT';
    }

    /**
     * Error response with details
     */
    public static function invalidCredentialsError(): string
    {
        return 'ERROR: Invalid username or password';
    }

    /**
     * Notice response
     */
    public static function callAlreadyActiveNotice(): string
    {
        return 'NOTICE: call_already_placed - 6666';
    }

    /**
     * Empty/minimal success response
     */
    public static function minimalSuccess(): string
    {
        return 'SUCCESS';
    }

    /**
     * Agent status response (complex pipe format)
     */
    public static function agentStatus(): string
    {
        return '6666|TEST_AGENT|READY|0|TESTCAMP|1630498496|127.0.0.1';
    }

    /**
     * MOH (Music on Hold) list response
     */
    public static function mohList(): string
    {
        return 'default|default_hold.gsm|on_hold_music.wav';
    }

    /**
     * Plain text response without standard format
     */
    public static function plainTextResponse(): string
    {
        return 'This is a plain text response without standard formatting';
    }

    /**
     * JSON error response (for API endpoints that return JSON)
     */
    public static function jsonError(): string
    {
        return json_encode([
            'error' => [
                'message' => 'Authentication failed',
                'type' => 'authentication_error',
                'code' => 401,
            ],
        ], JSON_THROW_ON_ERROR);
    }

    /**
     * JSON success response
     */
    public static function jsonSuccess(): string
    {
        return json_encode([
            'status' => 'success',
            'data' => [
                'id' => '12345',
                'campaign' => 'TEST_CAMPAIGN',
                'active' => true,
            ],
        ], JSON_THROW_ON_ERROR);
    }

    /**
     * Lead update success response
     */
    public static function leadUpdateSuccess(): string
    {
        return 'SUCCESS: Lead updated - LEAD_ID|98765|STATUS|UPDATED';
    }

    /**
     * Pause code list
     */
    public static function pauseCodeList(): string
    {
        return 'BREAK|Break Time|LUNCH|Lunch Break|MEET|Meeting';
    }

    /**
     * Disposition list
     */
    public static function dispositionList(): string
    {
        return 'SALE|Sale Made|NI|Not Interested|CB|Call Back|NA|No Answer';
    }

    /**
     * Call log entry
     */
    public static function callLogEntry(): string
    {
        return '20210901123456|1234567890|6666|TEST_AGENT|TESTCAMP|ANSWERED|127|SALE';
    }

    /**
     * Error with pipe-delimited additional info
     */
    public static function errorWithDetails(): string
    {
        return 'ERROR: Invalid campaign - CAMPAIGN|INVALID_CAMP|REASON|Campaign does not exist';
    }

    /**
     * Response with special characters
     */
    public static function responseWithSpecialCharacters(): string
    {
        return 'SUCCESS: Name updated - NAME|O\'Brien & Sons|COMPANY|Test & Co.';
    }

    /**
     * Empty response
     */
    public static function emptyResponse(): string
    {
        return '';
    }

    /**
     * Response with only whitespace
     */
    public static function whitespaceResponse(): string
    {
        return "   \n\t  \r\n  ";
    }
}
