<?php

namespace Tests\Resources;

use Tests\Fakers\FakeHttpTransporter;
use Tests\TestCase;
use VicidialApi\Resources\Admin;
use VicidialApi\Responses\VicidialResponse;

class AdminResponseTest extends TestCase
{
    public function test_version_response_returns_vicidial_response(): void
    {
        $admin = new Admin(
            new FakeHttpTransporter('VERSION: 2.14-890a|BUILD: 210303-1234|DATE: 2021-03-03 12:00:00|EPOCH: 1614772800')
        );

        $response = $admin->versionResponse();

        $this->assertInstanceOf(VicidialResponse::class, $response);
        $this->assertEquals('2.14-890a', $response->get('version'));
        $this->assertEquals('210303-1234', $response->get('build'));
    }

    public function test_moh_list_response_returns_vicidial_response(): void
    {
        $admin = new Admin(
            new FakeHttpTransporter('default|Default Music|Y')
        );

        $response = $admin->mohListResponse(['format' => 'selectframe']);

        $this->assertInstanceOf(VicidialResponse::class, $response);
        $this->assertTrue($response->ok());
        // MOH list returns pipe-delimited data
        $this->assertEquals('default', $response->get('field_0'));
        $this->assertEquals('Default Music', $response->get('field_1'));
    }

    public function test_add_lead_response_returns_vicidial_response(): void
    {
        $admin = new Admin(
            new FakeHttpTransporter('SUCCESS: lead added - 12345|9876543210')
        );

        $response = $admin->addLeadResponse([
            'phone_number' => '9876543210',
            'first_name' => 'Jane',
            'last_name' => 'Doe',
        ]);

        $this->assertInstanceOf(VicidialResponse::class, $response);
        $this->assertTrue($response->ok());
    }

    public function test_agent_status_response_returns_vicidial_response(): void
    {
        $admin = new Admin(
            new FakeHttpTransporter('INCALL|M4050908070000012345|12345|TESTCAMP|1|Test Agent|AGENTS|3|LOGIN||7275551212|123456|8600051')
        );

        $response = $admin->agentStatusResponse(['agent_user' => 'testuser']);

        $this->assertInstanceOf(VicidialResponse::class, $response);
        // Agent status returns pipe-delimited format: status|call_id|lead_id|campaign_id|...
        $this->assertEquals('INCALL', $response->get('field_0')); // status
        $this->assertEquals('M4050908070000012345', $response->get('field_1')); // call_id
        $this->assertEquals('12345', $response->get('field_2')); // lead_id
        $this->assertEquals('TESTCAMP', $response->get('field_3')); // campaign_id
    }

    public function test_campaigns_list_response_returns_vicidial_response(): void
    {
        $admin = new Admin(
            new FakeHttpTransporter('TESTCAMP|Test Campaign|Y|---ALL---|INBOUND_MAN|1|DOWN|PDROP DROP B NEW')
        );

        $response = $admin->campaignsListResponse([]);

        $this->assertInstanceOf(VicidialResponse::class, $response);
        $this->assertEquals('TESTCAMP', $response->get('field_0'));
        $this->assertEquals('Test Campaign', $response->get('field_1'));
    }

    public function test_error_response_in_admin_api(): void
    {
        $admin = new Admin(
            new FakeHttpTransporter('ERROR: add_lead USER DOES NOT HAVE PERMISSION - 6666|add_lead')
        );

        $response = $admin->addLeadResponse([
            'phone_number' => '1234567890',
        ]);

        $this->assertInstanceOf(VicidialResponse::class, $response);
        $this->assertFalse($response->ok());
        $this->assertTrue($response->failed());
        $this->assertEquals('error', $response->status());
        $this->assertEquals('ERROR', $response->get('result_type'));
    }

    public function test_backward_compatibility_with_string_methods(): void
    {
        $admin = new Admin(
            new FakeHttpTransporter('VERSION: 2.14-890a')
        );

        // Original method still returns string
        $stringResult = $admin->version();
        $this->assertIsString($stringResult);
        $this->assertEquals('VERSION: 2.14-890a', $stringResult);

        // Response method returns DTO
        $admin2 = new Admin(
            new FakeHttpTransporter('VERSION: 2.14-890a')
        );
        $response = $admin2->versionResponse();
        $this->assertInstanceOf(VicidialResponse::class, $response);
    }
}
