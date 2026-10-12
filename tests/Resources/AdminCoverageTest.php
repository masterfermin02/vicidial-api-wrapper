<?php

declare(strict_types=1);

namespace Tests\Resources;

use Tests\Fakers\RecordingTransporter;
use Tests\TestCase;
use VicidialApi\Resources\Admin;
use VicidialApi\Responses\VicidialResponse;

/**
 * Verifies that every Non-Agent API function added for parity with
 * NON-AGENT_API.txt (updated 2026-09-25) sends the right `function` name
 * to /vicidial/non_agent_api.php, for both the raw and the Response variants.
 */
final class AdminCoverageTest extends TestCase
{
    /**
     * @return array<string, array{string, string, array<string, string>}>
     */
    public static function functionProvider(): array
    {
        return [
            'sounds_list' => ['soundsList', 'sounds_list', ['format' => 'selectframe']],
            'batch_update_lead' => ['batchUpdateLead', 'batch_update_lead', ['lead_ids' => '1,2,3', 'status' => 'NEW']],
            'agent_ingroup_availability' => ['agentInGroupAvailability', 'agent_ingroup_availability', ['in_groups' => 'SALES,SUPPORT']],
            'lead_all_info' => ['leadAllInfo', 'lead_all_info', ['lead_id' => '1079425', 'custom_fields' => 'Y']],
            'lead_search' => ['leadSearch', 'lead_search', ['phone_number' => '3125551212,9055551212']],
            'copy_did' => ['copyDid', 'copy_did', ['source_did_pattern' => '7275551114', 'new_dids' => '7275553338']],
            'delete_dnc_phone' => ['deleteDncPhone', 'delete_dnc_phone', ['phone_number' => '7275551212', 'campaign_id' => 'TESTCAMP']],
            'delete_fpg_phone' => ['deleteFpgPhone', 'delete_fpg_phone', ['phone_number' => '7275551212', 'group' => 'TEST_IN_FILTER']],
            'hopper_bulk_insert' => ['hopperBulkInsert', 'hopper_bulk_insert', ['lead_ids' => '193715,1677922', 'hopper_priority' => '97']],
            'update_alt_url' => ['updateAltUrl', 'update_alt_url', ['campaign_id' => 'ALTTEST', 'entry_type' => 'campaign', 'url_type' => 'dispo', 'alt_url_id' => 'LIST']],
            'update_presets' => ['updatePresets', 'update_presets', ['campaign_id' => 'ALTTEST', 'action' => 'LIST']],
            'lead_dearchive' => ['leadDearchive', 'lead_dearchive', ['lead_id' => '274071']],
            'update_remote_agent' => ['updateRemoteAgent', 'update_remote_agent', ['agent_user' => '2424', 'status' => 'INACTIVE']],
            'user_details' => ['userDetails', 'user_details', ['agent_user' => '1234', 'stage' => 'csv']],
            'copy_phone' => ['copyPhone', 'copy_phone', ['extension' => '55000', 'source_extension' => 'cc100', 'source_server_ip' => '10.0.9.8']],
        ];
    }

    /**
     * @dataProvider functionProvider
     *
     * @param  array<string, string>  $options
     */
    public function test_raw_method_sends_expected_function(string $method, string $function, array $options): void
    {
        $transporter = new RecordingTransporter('SUCCESS: '.$function.' done - 6666');
        $admin = new Admin($transporter);

        $result = $admin->{$method}($options);

        $this->assertSame('SUCCESS: '.$function.' done - 6666', $result);
        $this->assertStringContainsString(Admin::ADMIN_URL, $transporter->uri());

        $query = $transporter->query();
        $this->assertSame($function, $query['function']);
        $this->assertSame('u', $query['user']);
        $this->assertSame('p', $query['pass']);
        foreach ($options as $key => $value) {
            $this->assertSame($value, $query[$key], "option {$key} was not forwarded");
        }
    }

    /**
     * @dataProvider functionProvider
     *
     * @param  array<string, string>  $options
     */
    public function test_response_method_sends_expected_function(string $method, string $function, array $options): void
    {
        $transporter = new RecordingTransporter('SUCCESS: '.$function.' done - 6666|1');
        $admin = new Admin($transporter);

        $response = $admin->{$method.'Response'}($options);

        $this->assertInstanceOf(VicidialResponse::class, $response);
        $this->assertTrue($response->ok());
        $this->assertSame($function, $transporter->query()['function']);
    }

    public function test_every_documented_non_agent_function_is_wrapped(): void
    {
        // Index of NON-AGENT_API.txt (vicidial.org/docs, updated 2026-09-25) plus the two
        // functions that are documented in the body but missing from the index.
        $documented = [
            'version', 'sounds_list', 'moh_list', 'vm_list', 'blind_monitor', 'add_lead', 'update_lead',
            'batch_update_lead', 'agent_ingroup_info', 'agent_ingroup_availability', 'recording_lookup',
            'did_log_export', 'agent_stats_export', 'add_user', 'update_user', 'copy_user', 'add_phone',
            'update_phone', 'add_phone_alias', 'update_phone_alias', 'add_list', 'update_list', 'list_info',
            'list_custom_fields', 'add_group_alias', 'user_group_status', 'in_group_status', 'agent_status',
            'callid_info', 'lead_field_info', 'lead_callback_info', 'lead_all_info', 'lead_status_search',
            'lead_search', 'ccc_lead_info', 'server_refresh', 'check_phone_number', 'logged_in_agents',
            'update_campaign', 'add_did', 'copy_did', 'update_did', 'phone_number_log', 'call_status_stats',
            'call_dispo_report', 'agent_campaigns', 'update_cid_group_entry', 'add_dnc_phone',
            'delete_dnc_phone', 'add_fpg_phone', 'delete_fpg_phone', 'campaigns_list', 'hopper_list',
            'hopper_bulk_insert', 'update_alt_url', 'update_presets', 'lead_dearchive', 'update_remote_agent',
            'user_details', 'update_log_entry', 'copy_phone',
        ];

        $source = (string) file_get_contents(__DIR__.'/../../src/Resources/Admin.php');
        preg_match_all("/makeApiRequest\('([a-z_]+)'/", $source, $matches);
        $wrapped = array_unique($matches[1]);

        $missing = array_values(array_diff($documented, $wrapped));
        $this->assertSame([], $missing, 'Non-Agent API functions without a wrapper method: '.implode(', ', $missing));
    }
}
