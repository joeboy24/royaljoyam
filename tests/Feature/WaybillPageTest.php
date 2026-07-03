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

    public function test_waybill_history_renders_fancy_flash_messages(): void
    {
        $this->actingAs($this->admin)
            ->withSession(['success' => 'Bill Successfully Updated'])
            ->get('/waybillview')
            ->assertOk()
            ->assertSee('dash-flash-success', false)
            ->assertSee('Bill Successfully Updated')
            ->assertSee('data-dash-flash-close', false);

        $this->actingAs($this->admin)
            ->withSession(['error' => 'Waybill not found'])
            ->get('/waybillview')
            ->assertOk()
            ->assertSee('dash-flash-error', false)
            ->assertSee('Waybill not found');
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

    public function test_add_waybill_rejects_non_numeric_weight(): void
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

        $response->assertSessionHasErrors('weight');
        $this->assertNull(Waybill::where('bill_no', 'WB-SACK-001')->first());
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

    public function test_recycle_bin_shows_deleted_waybills(): void
    {
        $this->createWaybill(['bill_no' => 'WB-ACTIVE-RECYCLE']);
        $deleted = $this->createWaybill(['bill_no' => 'WB-DELETED-RECYCLE']);
        $deleted->del = 'yes';
        $deleted->save();

        $this->actingAs($this->admin)
            ->get('/waybillview?recycle=1')
            ->assertOk()
            ->assertSee('WB-DELETED-RECYCLE')
            ->assertDontSee('WB-ACTIVE-RECYCLE')
            ->assertSee('restore_waybill', false);
    }

    public function test_admin_can_restore_waybill_from_recycle_bin(): void
    {
        $deleted = $this->createWaybill(['bill_no' => 'WB-RESTORE-001']);
        $deleted->del = 'yes';
        $deleted->save();

        $response = $this->actingAs($this->admin)->put('/items/'.$deleted->id, [
            'store_action' => 'restore_waybill',
        ]);

        $response->assertRedirect('/waybillview?recycle=1');
        $this->assertDatabaseHas('waybills', [
            'id' => $deleted->id,
            'del' => 'no',
        ]);
    }

    public function test_waybill_history_filters_by_status(): void
    {
        $this->createWaybill(['bill_no' => 'WB-PENDING-FILTER', 'status' => 'Pending']);
        $this->createWaybill(['bill_no' => 'WB-DELIVERED-FILTER', 'status' => 'Delivered']);

        $this->actingAs($this->admin)
            ->get('/waybillview?status=Delivered')
            ->assertOk()
            ->assertSee('WB-DELIVERED-FILTER')
            ->assertDontSee('WB-PENDING-FILTER');
    }

    public function test_waybill_history_shows_distribution_status_and_remaining(): void
    {
        $pending = $this->createWaybill(['bill_no' => 'WB-DIST-PEND', 'status' => 'Delivered']);
        $partial = $this->createWaybill(['bill_no' => 'WB-DIST-PART', 'status' => 'Delivered']);
        $complete = $this->createWaybill(['bill_no' => 'WB-DIST-DONE', 'status' => 'Delivered']);

        $pendingItem = $this->createItem(['item_no' => 'MT-DIST-PEND']);
        $partialItem = $this->createItem(['item_no' => 'MT-DIST-PART']);
        $completeItem = $this->createItem(['item_no' => 'MT-DIST-DONE']);

        $this->createWbcontent($pending->id, $pendingItem, ['qty' => '10', 'qty_dist' => '0']);
        $this->createWbcontent($partial->id, $partialItem, ['qty' => '10', 'qty_dist' => '4']);
        $this->createWbcontent($complete->id, $completeItem, ['qty' => '10', 'qty_dist' => '10']);

        $response = $this->actingAs($this->admin)->get('/waybillview');

        $response->assertOk()
            ->assertSee('waybill-table-dist-col', false)
            ->assertSee('waybill-dist-pending', false)
            ->assertSee('waybill-dist-partial', false)
            ->assertSee('waybill-dist-complete', false)
            ->assertSee('10 rem.', false)
            ->assertSee('6 rem.', false)
            ->assertSee('waybill-row-dist-open', false)
            ->assertSee('waybill-action-badge', false)
            ->assertSee('waybill-actions-more', false);
    }

    public function test_waybill_history_filters_by_distribution_status(): void
    {
        $pending = $this->createWaybill(['bill_no' => 'WB-FILTER-PEND']);
        $partial = $this->createWaybill(['bill_no' => 'WB-FILTER-PART']);
        $complete = $this->createWaybill(['bill_no' => 'WB-FILTER-DONE']);

        $this->createWbcontent($pending->id, $this->createItem(), ['qty' => '10', 'qty_dist' => '0']);
        $this->createWbcontent($partial->id, $this->createItem(), ['qty' => '10', 'qty_dist' => '3']);
        $this->createWbcontent($complete->id, $this->createItem(), ['qty' => '10', 'qty_dist' => '10']);

        $this->actingAs($this->admin)
            ->get('/waybillview?distribution=pending')
            ->assertOk()
            ->assertSee('WB-FILTER-PEND')
            ->assertDontSee('WB-FILTER-PART')
            ->assertDontSee('WB-FILTER-DONE');

        $this->actingAs($this->admin)
            ->get('/waybillview?distribution=partial')
            ->assertOk()
            ->assertSee('WB-FILTER-PART')
            ->assertDontSee('WB-FILTER-PEND')
            ->assertDontSee('WB-FILTER-DONE');

        $this->actingAs($this->admin)
            ->get('/waybillview?distribution=complete')
            ->assertOk()
            ->assertSee('WB-FILTER-DONE')
            ->assertDontSee('WB-FILTER-PEND')
            ->assertDontSee('WB-FILTER-PART');
    }

    public function test_waybill_history_has_distribution_filter(): void
    {
        $this->actingAs($this->admin)
            ->get('/waybillview')
            ->assertOk()
            ->assertSee('name="distribution"', false)
            ->assertSee('All distribution');
    }

    public function test_waybill_history_has_collapsible_filters(): void
    {
        $this->actingAs($this->admin)
            ->get('/waybillview')
            ->assertOk()
            ->assertSee('data-collapsible-filters', false)
            ->assertSee('inventory-filters-toggle', false);
    }

    public function test_single_waybill_print_route_loads(): void
    {
        DB::table('companies')->insert([
            'id' => 1,
            'user_id' => (string) $this->admin->id,
            'name' => 'Royal Joyam',
            'address' => 'Test Address',
            'contact' => '0244000000',
            'email' => 'test@example.com',
            'logo' => 'logo.png',
            'del' => 'no',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $waybill = $this->createWaybill(['bill_no' => 'WB-PRINT-001', 'status' => 'Delivered']);

        $this->actingAs($this->admin)
            ->get('/waybillprint/'.$waybill->id)
            ->assertOk()
            ->assertSee('WB-PRINT-001')
            ->assertSee('waybill-print-status-col', false)
            ->assertSee('Delivered')
            ->assertDontSee('Del..');
    }

    public function test_distribution_page_has_searchable_item_picker(): void
    {
        $waybill = $this->createWaybill(['bill_no' => 'WB-DIST-001']);

        $itemId = DB::table('items')->insertGetId([
            'item_no' => 'MT095158',
            'user_id' => (string) $this->admin->id,
            'name' => 'STRIPS',
            'brand' => 'Test Brand',
            'qty' => '100',
            'del' => 'no',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('wbcontents')->insert([
            'user_id' => (string) $this->admin->id,
            'waybill_id' => (string) $waybill->id,
            'item_id' => (string) $itemId,
            'qty' => '10',
            'qty_dist' => '0',
            'del' => 'no',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($this->admin)
            ->get('/distribution/'.$waybill->id)
            ->assertOk()
            ->assertSee('id="distItemSearch"', false)
            ->assertSee('id="distItemList"', false)
            ->assertSee('Back to waybills')
            ->assertSee('Branch distribution')
            ->assertSee('MT095158')
            ->assertSee('STRIPS');
    }

    public function test_cannot_delete_waybill_item_when_already_distributed(): void
    {
        $waybill = $this->createWaybill(['bill_no' => 'WB-DEL-001']);
        $itemId = $this->createItem(['item_no' => 'MT-DEL-001']);
        $wbcId = $this->createWbcontent($waybill->id, $itemId, ['qty' => '10', 'qty_dist' => '5']);

        $this->actingAs($this->admin)
            ->put('/items/'.$wbcId, [
                '_token' => csrf_token(),
                'store_action' => 'del_wbcontent',
            ])
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertDatabaseHas('wbcontents', ['id' => $wbcId]);
    }

    public function test_cannot_reduce_waybill_qty_below_distributed_amount(): void
    {
        $waybill = $this->createWaybill(['bill_no' => 'WB-QTY-001']);
        $itemId = $this->createItem(['item_no' => 'MT-QTY-001']);
        $wbcId = $this->createWbcontent($waybill->id, $itemId, ['qty' => '10', 'qty_dist' => '6']);

        $this->actingAs($this->admin)
            ->put('/items/'.$wbcId, [
                '_token' => csrf_token(),
                'store_action' => 'up_wbcontent',
                'qty' => '4',
            ])
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertDatabaseHas('wbcontents', ['id' => $wbcId, 'qty' => '10']);
    }

    public function test_branch_distribution_validates_remaining_quantity(): void
    {
        $waybill = $this->createWaybill(['bill_no' => 'WB-DIST-VAL', 'status' => 'Delivered']);
        $itemId = $this->createItem(['item_no' => 'MT-DIST-VAL', 'qty' => '100']);
        $wbcId = $this->createWbcontent($waybill->id, $itemId, ['qty' => '10', 'qty_dist' => '0']);

        $this->actingAs($this->admin)
            ->put('/items/'.$wbcId, [
                '_token' => csrf_token(),
                'store_action' => 'up_wbdist',
                'q1'.$itemId => '12',
            ])
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->actingAs($this->admin)
            ->put('/items/'.$wbcId, [
                '_token' => csrf_token(),
                'store_action' => 'up_wbdist',
                'q1'.$itemId => '4',
                'q2'.$itemId => '3',
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('wbcontents', ['id' => $wbcId, 'qty_dist' => '7']);
        $this->assertDatabaseHas('wbdistributions', [
            'waybill_id' => (string) $waybill->id,
            'item_id' => (string) $itemId,
            'q1' => '4',
            'q2' => '3',
        ]);
    }

    public function test_add_wbcontent_syncs_waybill_tot_qty(): void
    {
        $waybill = $this->createWaybill(['bill_no' => 'WB-TOTQTY', 'tot_qty' => '0']);
        $itemA = $this->createItem(['item_no' => 'MT-TOT-A']);
        $itemB = $this->createItem(['item_no' => 'MT-TOT-B']);

        $this->actingAs($this->admin)->post('/items', [
            '_token' => csrf_token(),
            'store_action' => 'add_wbcontent',
            'wb_id' => $waybill->id,
            'item' => $itemA,
            'qty' => '10',
        ])->assertRedirect();

        $this->actingAs($this->admin)->post('/items', [
            '_token' => csrf_token(),
            'store_action' => 'add_wbcontent',
            'wb_id' => $waybill->id,
            'item' => $itemB,
            'qty' => '5',
        ])->assertRedirect();

        $this->assertDatabaseHas('waybills', [
            'id' => $waybill->id,
            'tot_qty' => '15',
        ]);
    }

    public function test_waybill_print_loads_from_query_params_without_session(): void
    {
        DB::table('companies')->insert([
            'id' => 1,
            'user_id' => (string) $this->admin->id,
            'name' => 'Royal Joyam',
            'address' => 'Test Address',
            'contact' => '0244000000',
            'email' => 'test@example.com',
            'logo' => 'logo.png',
            'del' => 'no',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $waybill = $this->createWaybill(['bill_no' => 'WB-PRINT-QP', 'status' => 'Delivered']);

        $this->actingAs($this->admin)
            ->get('/waybillprint?date_from='.now()->toDateString())
            ->assertOk()
            ->assertSee('WB-PRINT-QP');
    }

    public function test_waybill_report_shows_status_summary(): void
    {
        $this->createWaybill(['bill_no' => 'WB-SUM-PEND', 'status' => 'Pending']);
        $this->createWaybill(['bill_no' => 'WB-SUM-DEL', 'status' => 'Delivered']);

        $this->actingAs($this->admin)
            ->get('/waybillreport')
            ->assertOk()
            ->assertSee('waybill-report-status-summary', false)
            ->assertSee('Pending:')
            ->assertSee('Delivered:');
    }

    public function test_waybill_report_csv_export_respects_filters(): void
    {
        $this->createWaybill(['bill_no' => 'WB-CSV-001', 'status' => 'Delivered']);
        $this->createWaybill(['bill_no' => 'WB-CSV-002', 'status' => 'Pending']);

        $response = $this->actingAs($this->admin)->get('/waybillreport/export');

        $response->assertOk();
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('WB-CSV-001', $response->streamedContent());
        $this->assertStringContainsString('WB-CSV-002', $response->streamedContent());
    }

    public function test_cannot_distribute_until_waybill_is_delivered(): void
    {
        $waybill = $this->createWaybill(['bill_no' => 'WB-NOT-DEL', 'status' => 'Pending']);
        $itemId = $this->createItem(['item_no' => 'MT-NOT-DEL']);
        $wbcId = $this->createWbcontent($waybill->id, $itemId, ['qty' => '10', 'qty_dist' => '0']);

        $this->actingAs($this->admin)
            ->put('/items/'.$wbcId, [
                '_token' => csrf_token(),
                'store_action' => 'up_wbdist',
                'q1'.$itemId => '3',
            ])
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->actingAs($this->admin)
            ->get('/waybillview')
            ->assertOk()
            ->assertSee('Delivered status required', false);

        $this->actingAs($this->admin)
            ->get('/distribution/'.$waybill->id)
            ->assertOk()
            ->assertSee('dist-callout-warning', false)
            ->assertSee('Pending');
    }

    public function test_distribution_page_shows_sent_branch_totals(): void
    {
        DB::table('company_branches')->insert([
            'user_id' => (string) $this->admin->id,
            'name' => 'Branch A',
            'loc' => 'Loc 1',
            'contact' => '0000000001',
            'tag' => '1',
            'del' => 'no',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $waybill = $this->createWaybill(['bill_no' => 'WB-SENT-001', 'status' => 'Delivered']);
        $itemId = $this->createItem(['item_no' => 'MT-SENT-001']);
        $this->createWbcontent($waybill->id, $itemId, ['qty' => '10', 'qty_dist' => '5']);

        DB::table('wbdistributions')->insert([
            'user_id' => (string) $this->admin->id,
            'waybill_id' => (string) $waybill->id,
            'item_id' => (string) $itemId,
            'q1' => '3',
            'q2' => '2',
            'del' => 'no',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($this->admin)
            ->get('/distribution/'.$waybill->id)
            ->assertOk()
            ->assertSee('dist-branch-sent', false)
            ->assertSee('5 remaining')
            ->assertSee('MT-SENT-001');
    }

    protected function createItem(array $overrides = []): int
    {
        return DB::table('items')->insertGetId(array_merge([
            'item_no' => 'MT-'.uniqid(),
            'user_id' => (string) $this->admin->id,
            'name' => 'Test Item',
            'qty' => '100',
            'del' => 'no',
            'created_at' => now(),
            'updated_at' => now(),
        ], $overrides));
    }

    protected function createWbcontent(int|string $waybillId, int|string $itemId, array $overrides = []): int
    {
        return DB::table('wbcontents')->insertGetId(array_merge([
            'user_id' => (string) $this->admin->id,
            'waybill_id' => (string) $waybillId,
            'item_id' => (string) $itemId,
            'qty' => '10',
            'qty_dist' => '0',
            'del' => 'no',
            'created_at' => now(),
            'updated_at' => now(),
        ], $overrides));
    }
}
