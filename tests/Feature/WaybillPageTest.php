<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Waybill;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class WaybillPageTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = $this->createAdministrator();
    }

    protected function createAdministrator(array $overrides = []): User
    {
        $data = array_merge([
            'company_branch_id' => '1',
            'name' => 'admin.test',
            'email' => 'admin@test.example',
            'bv' => 'A',
            'status' => 'Administrator',
            'password' => Hash::make('password'),
            'del' => 'no',
        ], $overrides);

        $id = DB::table('users')->insertGetId(array_merge($data, [
            'created_at' => now(),
            'updated_at' => now(),
        ]));

        return User::findOrFail($id);
    }

    protected function createWaybill(array $overrides = []): Waybill
    {
        return Waybill::create(array_merge([
            'user_id' => (string) $this->admin->id,
            'stock_no' => 'ST'.uniqid(),
            'comp_name' => 'Acme Supplies',
            'comp_add' => '12 Market Street',
            'comp_contact' => '0244000000',
            'drv_name' => 'John Driver',
            'drv_contact' => '0244111111',
            'vno' => 'GR-1234-20',
            'bill_no' => 'WB-'.uniqid(),
            'weight' => '10',
            'nop' => '2',
            'tot_qty' => '20',
            'del_date' => '2026-07-15',
            'status' => 'Pending',
            'del' => 'no',
        ], $overrides));
    }

    public function test_waybill_history_search_finds_bill_and_stock_numbers(): void
    {
        $waybill = $this->createWaybill([
            'bill_no' => 'WB-SEARCH-001',
            'stock_no' => 'ST-SEARCH-001',
        ]);

        $this->actingAs($this->admin)
            ->get('/waybillview?waybillsearch=WB-SEARCH-001')
            ->assertOk()
            ->assertSee($waybill->bill_no);

        $this->actingAs($this->admin)
            ->get('/waybillview?waybillsearch=ST-SEARCH-001')
            ->assertOk()
            ->assertSee($waybill->stock_no);
    }

    public function test_waybill_history_search_excludes_deleted_records(): void
    {
        $this->createWaybill([
            'bill_no' => 'WB-ACTIVE-001',
            'drv_name' => 'Visible Driver',
        ]);

        $deleted = $this->createWaybill([
            'bill_no' => 'WB-DELETED-001',
            'drv_name' => 'Visible Driver',
        ]);
        $deleted->del = 'yes';
        $deleted->save();

        $response = $this->actingAs($this->admin)
            ->get('/waybillview?waybillsearch='.urlencode('Visible Driver'));

        $response->assertOk()
            ->assertSee('WB-ACTIVE-001')
            ->assertDontSee('WB-DELETED-001');
    }

    public function test_waybill_history_shows_total_record_count_not_page_count(): void
    {
        for ($i = 1; $i <= 11; $i++) {
            $this->createWaybill([
                'bill_no' => 'WB-TOTAL-'.str_pad((string) $i, 2, '0', STR_PAD_LEFT),
            ]);
        }

        $this->actingAs($this->admin)
            ->get('/waybillview')
            ->assertOk()
            ->assertSee('Total:')
            ->assertSee('>11<', false);
    }

    public function test_waybill_history_handles_missing_delivery_date(): void
    {
        $this->createWaybill([
            'bill_no' => 'WB-NODATE-001',
            'del_date' => '',
        ]);

        $this->actingAs($this->admin)
            ->get('/waybillview?waybillsearch=WB-NODATE-001')
            ->assertOk()
            ->assertSee('—');
    }

    public function test_add_waybill_creates_record_with_valid_data(): void
    {
        $response = $this->actingAs($this->admin)->post('/items', [
            'store_action' => 'add_waybill',
            'comp_name' => 'Acme Supplies',
            'comp_add' => '12 Market Street',
            'comp_contact' => '0244000000',
            'drv_name' => 'John Driver',
            'drv_contact' => '0244111111',
            'vno' => 'GR-1234-20',
            'bill_no' => 'WB-VALID-001',
            'weight' => '12.5',
            'nop' => '3',
            'tot_qty' => '30',
            'del_date' => '2026-07-20',
            'status' => 'Pending',
        ]);

        $waybill = Waybill::where('bill_no', 'WB-VALID-001')->firstOrFail();
        $response->assertRedirect('/distribution/'.$waybill->id);
        $this->assertDatabaseHas('waybills', [
            'bill_no' => 'WB-VALID-001',
            'nop' => '3',
            'del' => 'no',
        ]);
    }

    public function test_add_waybill_redirects_to_distribution_after_save(): void
    {
        $response = $this->actingAs($this->admin)->post('/items', [
            'store_action' => 'add_waybill',
            'comp_name' => 'Acme Supplies',
            'comp_add' => '12 Market Street',
            'comp_contact' => '0244000000',
            'drv_name' => 'John Driver',
            'drv_contact' => '0244111111',
            'vno' => 'GR-1234-20',
            'bill_no' => 'WB-REDIRECT-001',
            'status' => 'Pending',
        ]);

        $waybill = Waybill::where('bill_no', 'WB-REDIRECT-001')->firstOrFail();
        $response->assertRedirect('/distribution/'.$waybill->id);
    }

    public function test_add_waybill_accepts_in_transit_status(): void
    {
        $response = $this->actingAs($this->admin)->post('/items', [
            'store_action' => 'add_waybill',
            'comp_name' => 'Acme Supplies',
            'comp_add' => '12 Market Street',
            'comp_contact' => '0244000000',
            'drv_name' => 'John Driver',
            'drv_contact' => '0244111111',
            'vno' => 'GR-1234-20',
            'bill_no' => 'WB-TRANSIT-001',
            'status' => 'In Transit',
        ]);

        $waybill = Waybill::where('bill_no', 'WB-TRANSIT-001')->firstOrFail();
        $response->assertRedirect('/distribution/'.$waybill->id);
        $this->assertDatabaseHas('waybills', [
            'bill_no' => 'WB-TRANSIT-001',
            'status' => 'In Transit',
        ]);
    }

    public function test_waybill_create_page_suggests_bill_number_and_default_date(): void
    {
        $this->actingAs($this->admin)
            ->get('/waybill')
            ->assertOk()
            ->assertSee('name="bill_no"', false)
            ->assertSee('WB-'.now()->format('Ymd'), false)
            ->assertSee('value="'.now()->format('Y-m-d').'"', false)
            ->assertSee('In Transit');
    }

    public function test_add_waybill_allows_blank_optional_fields(): void
    {
        $response = $this->actingAs($this->admin)->post('/items', [
            'store_action' => 'add_waybill',
            'comp_name' => 'Acme Supplies',
            'comp_add' => '12 Market Street',
            'comp_contact' => '0244000000',
            'drv_name' => 'John Driver',
            'drv_contact' => '0244111111',
            'vno' => 'GR-1234-20',
            'bill_no' => 'WB-BLANK-001',
            'weight' => '',
            'nop' => '',
            'tot_qty' => '',
            'del_date' => '',
            'status' => 'Pending',
        ]);

        $waybill = Waybill::where('bill_no', 'WB-BLANK-001')->firstOrFail();
        $response->assertRedirect('/distribution/'.$waybill->id);
    }

    public function test_add_waybill_accepts_text_weight_like_legacy_records(): void
    {
        $response = $this->actingAs($this->admin)->post('/items', [
            'store_action' => 'add_waybill',
            'comp_name' => 'Acme Supplies',
            'comp_add' => 'EJISU',
            'comp_contact' => '0503113033',
            'drv_name' => 'MARTIN',
            'drv_contact' => '0541135470',
            'vno' => 'AS 8787-14',
            'bill_no' => 'WB-SACK-001',
            'weight' => 'SACK',
            'nop' => '1000',
            'tot_qty' => '1000',
            'del_date' => '2021-08-23',
            'status' => 'Delivered',
        ]);

        $waybill = Waybill::where('bill_no', 'WB-SACK-001')->firstOrFail();
        $response->assertRedirect('/distribution/'.$waybill->id);
    }

    public function test_add_waybill_rejects_duplicate_bill_number(): void
    {
        $this->createWaybill(['bill_no' => 'WB-DUP-001']);

        $response = $this->actingAs($this->admin)->post('/items', [
            'store_action' => 'add_waybill',
            'comp_name' => 'Acme Supplies',
            'comp_add' => '12 Market Street',
            'comp_contact' => '0244000000',
            'drv_name' => 'John Driver',
            'drv_contact' => '0244111111',
            'vno' => 'GR-1234-20',
            'bill_no' => 'WB-DUP-001',
            'status' => 'Pending',
        ]);

        $response->assertSessionHasErrors('bill_no');
    }

    public function test_add_waybill_rejects_address_longer_than_form_limit(): void
    {
        $response = $this->actingAs($this->admin)->post('/items', [
            'store_action' => 'add_waybill',
            'comp_name' => 'Acme Supplies',
            'comp_add' => str_repeat('A', 2001),
            'comp_contact' => '0244000000',
            'drv_name' => 'John Driver',
            'drv_contact' => '0244111111',
            'vno' => 'GR-1234-20',
            'bill_no' => 'WB-LONG-001',
            'status' => 'Pending',
        ]);

        $response->assertSessionHasErrors('comp_add');
    }

    public function test_waybill_report_refresh_link_points_to_waybill_report(): void
    {
        $this->actingAs($this->admin)
            ->get('/waybillreport')
            ->assertOk()
            ->assertSee('href="/waybillreport"', false);
    }
}
