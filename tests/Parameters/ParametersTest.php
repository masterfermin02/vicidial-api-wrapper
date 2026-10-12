<?php

declare(strict_types=1);

namespace Tests\Parameters;

use Tests\Fakers\RecordingTransporter;
use Tests\TestCase;
use VicidialApi\Parameters\AddLeadParameters;
use VicidialApi\Parameters\DialParameters;
use VicidialApi\Parameters\DispoParameters;
use VicidialApi\Parameters\UpdateLeadParameters;
use VicidialApi\Resources\Admin;
use VicidialApi\Resources\Agent;

final class ParametersTest extends TestCase
{
    public function test_dial_parameters_render_snake_case_and_yes_no(): void
    {
        $parameters = new DialParameters(
            phoneNumber: '7275551212',
            phoneCode: 1,
            search: true,
            preview: false,
            outboundCid: '8005551212',
        );

        $this->assertSame([
            'phone_number' => '7275551212',
            'phone_code' => '1',
            'search' => 'YES',
            'preview' => 'NO',
            'outbound_cid' => '8005551212',
        ], $parameters->toArray());
    }

    public function test_dispo_status_is_sent_as_value(): void
    {
        $parameters = new DispoParameters(status: 'CALLBK', callbackDatetime: '2026-10-13 12:00:00', callbackType: 'USERONLY');

        $this->assertSame([
            'value' => 'CALLBK',
            'callback_datetime' => '2026-10-13 12:00:00',
            'callback_type' => 'USERONLY',
        ], $parameters->toArray());
    }

    public function test_add_lead_parameters_use_y_n_and_allow_custom_fields(): void
    {
        $parameters = (new AddLeadParameters(
            phoneNumber: '7275551212',
            listId: 999,
            dncCheck: 'AREACODE',
            addToHopper: true,
            customFields: true,
            firstName: 'Ada',
        ))->with('favorite_color', 'blue');

        $this->assertSame([
            'phone_number' => '7275551212',
            'list_id' => '999',
            'dnc_check' => 'AREACODE',
            'add_to_hopper' => 'Y',
            'custom_fields' => 'Y',
            'first_name' => 'Ada',
            'favorite_color' => 'blue',
        ], $parameters->toArray());
    }

    public function test_with_returns_a_new_instance(): void
    {
        $base = new UpdateLeadParameters(leadId: 42);
        $extended = $base->with('favorite_color', 'blue');

        $this->assertSame(['lead_id' => '42'], $base->toArray());
        $this->assertSame(['lead_id' => '42', 'favorite_color' => 'blue'], $extended->toArray());
    }

    public function test_update_lead_parameters_cover_search_and_hopper_flags(): void
    {
        $parameters = new UpdateLeadParameters(
            vendorLeadCode: 'ABC',
            searchMethod: 'VENDOR_LEAD_CODE',
            callback: 'REMOVE',
            removeFromHopper: true,
            province: '--BLANK--',
        );

        $this->assertSame([
            'vendor_lead_code' => 'ABC',
            'search_method' => 'VENDOR_LEAD_CODE',
            'callback' => 'REMOVE',
            'remove_from_hopper' => 'Y',
            'province' => '--BLANK--',
        ], $parameters->toArray());
    }

    public function test_agent_dial_accepts_typed_parameters(): void
    {
        $transporter = new RecordingTransporter;
        $agent = new Agent($transporter);

        $agent->dial('1000', new DialParameters(phoneNumber: '7275551212', phoneCode: '1', focus: false));

        $query = $transporter->query();
        $this->assertSame('external_dial', $query['function']);
        $this->assertSame('7275551212', $query['value']);
        $this->assertSame('1', $query['phone_code']);
        $this->assertSame('NO', $query['focus']);
        $this->assertSame('YES', $query['search'], 'defaults still apply');
    }

    public function test_agent_dispo_accepts_typed_parameters(): void
    {
        $transporter = new RecordingTransporter;
        $agent = new Agent($transporter);

        $agent->dispoResponse('1000', new DispoParameters(status: 'SALE'));

        $this->assertSame('external_status', $transporter->query()['function']);
        $this->assertSame('SALE', $transporter->query()['value']);
    }

    public function test_admin_add_and_update_lead_accept_typed_parameters(): void
    {
        $transporter = new RecordingTransporter;
        $admin = new Admin($transporter);

        $admin->addLead(new AddLeadParameters(phoneNumber: '7275551212', listId: '101'));
        $this->assertSame('add_lead', $transporter->query()['function']);
        $this->assertSame('101', $transporter->query()['list_id']);

        $admin->updateLeadResponse(new UpdateLeadParameters(leadId: 7, status: 'NEW'));
        $this->assertSame('update_lead', $transporter->query()['function']);
        $this->assertSame('7', $transporter->query()['lead_id']);
        $this->assertSame('NEW', $transporter->query()['status']);
    }
}
