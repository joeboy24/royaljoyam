<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Waybill;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DistReportPageTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedBaseline();
        $this->admin = $this->createAdministrator();
    }

    protected function seedBaseline(): void
    {
        DB::table('companies')->insert([
            'id' => 1,
            'user_id' => '1',
            'name' => 'Royal Joyam Ventures',
            'address' => 'Test Address',
            'contact' => '0000000000',
            'logo' => 'logo.png',
            'del' => 'no',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        foreach ([1 => 'Branch A', 2 => 'Branch B'] as $tag => $name) {
            DB::table('company_branches')->insert([
                'user_id' => '1',
                'name' => $name,
                'loc' => 'Loc '.$tag,
                'contact' => '000000000'.$tag,
                'tag' => (string) $tag,
                'del' => 'no',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        DB::table('categories')->insert([
            'user_id' => '1',
            'name' => 'General',
            'desc' => 'General category',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    protected function createAdministrator(): User
    {
        $id = DB::table('users')->insertGetId([
            'company_branch_id' => '1',
            'name' => 'admin.test',
            'email' => 'admin@test.example',
            'bv' => 'A',
            'status' => 'Administrator',
            'password' => Hash::make('password'),
            'del' => 'no',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return User::findOrFail($id);
    }

    public function test_distreport_page_loads_with_distribution_rows(): void
    {
        $waybill = Waybill::create([
            'user_id' => (string) $this->admin->id,
            'stock_no' => 'ST-DIST-001',
            'comp_name' => 'Acme Supplies',
            'comp_add' => '12 Market Street',
            'comp_contact' => '0244000000',
            'drv_name' => 'John Driver',
            'drv_contact' => '0244111111',
            'vno' => 'GR-1234-20',
            'bill_no' => 'WB-DIST-001',
            'weight' => '10',
            'nop' => '2',
            'tot_qty' => '20',
            'del_date' => '2026-07-15',
            'status' => 'Pending',
            'del' => 'no',
        ]);

        $itemId = DB::table('items')->insertGetId([
            'item_no' => 'MT-DIST-001',
            'user_id' => (string) $this->admin->id,
            'name' => 'Distribution Item',
            'qty' => '100',
            'del' => 'no',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('wbdistributions')->insert([
            'user_id' => (string) $this->admin->id,
            'waybill_id' => (string) $waybill->id,
            'item_id' => (string) $itemId,
            'q1' => '5',
            'q2' => '3',
            'del' => 'no',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($this->admin)
            ->get('/distreport')
            ->assertOk()
            ->assertSee('Distribution Report')
            ->assertSee('Load data')
            ->assertSee('MT-DIST-001')
            ->assertSee('Distribution Item')
            ->assertSee('Acme Supplies');
    }

    public function test_distreport_handles_missing_item_and_waybill_relations(): void
    {
        DB::table('wbdistributions')->insert([
            'user_id' => (string) $this->admin->id,
            'waybill_id' => '99999',
            'item_id' => '99999',
            'q1' => '1',
            'del' => 'no',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($this->admin)
            ->get('/distreport')
            ->assertOk()
            ->assertSee('Item unavailable');
    }

    public function test_distribution_index_redirects_to_distreport(): void
    {
        $this->actingAs($this->admin)
            ->get('/distribution')
            ->assertRedirect('/distreport');
    }

    public function test_distreport_print_page_loads(): void
    {
        $waybill = Waybill::create([
            'user_id' => (string) $this->admin->id,
            'stock_no' => 'ST-DIST-PRINT',
            'comp_name' => 'Print Corp',
            'comp_add' => '12 Market Street',
            'comp_contact' => '0244000000',
            'drv_name' => 'John Driver',
            'drv_contact' => '0244111111',
            'vno' => 'GR-1234-20',
            'bill_no' => 'WB-DIST-PRINT',
            'weight' => '10',
            'nop' => '2',
            'tot_qty' => '20',
            'del_date' => '2026-07-15',
            'status' => 'Pending',
            'del' => 'no',
        ]);

        $itemId = DB::table('items')->insertGetId([
            'item_no' => 'MT-PRINT-001',
            'user_id' => (string) $this->admin->id,
            'name' => 'Print Item',
            'qty' => '100',
            'del' => 'no',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('wbdistributions')->insert([
            'user_id' => (string) $this->admin->id,
            'waybill_id' => (string) $waybill->id,
            'item_id' => (string) $itemId,
            'q1' => '2',
            'del' => 'no',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($this->admin)->get('/distreport')->assertOk();

        $this->actingAs($this->admin)
            ->get('/distreportprint?date_from='.now()->toDateString())
            ->assertOk()
            ->assertSee('MT-PRINT-001')
            ->assertSee('Print Corp');
    }

    public function test_distreport_csv_export_includes_distribution_rows(): void
    {
        $waybill = Waybill::create([
            'user_id' => (string) $this->admin->id,
            'stock_no' => 'ST-CSV-001',
            'comp_name' => 'CSV Corp',
            'comp_add' => '12 Market Street',
            'comp_contact' => '0244000000',
            'drv_name' => 'John Driver',
            'drv_contact' => '0244111111',
            'vno' => 'GR-1234-20',
            'bill_no' => 'WB-CSV-001',
            'weight' => '10',
            'nop' => '2',
            'tot_qty' => '20',
            'del_date' => '2026-07-15',
            'status' => 'Delivered',
            'del' => 'no',
        ]);

        $itemId = DB::table('items')->insertGetId([
            'item_no' => 'MT-CSV-001',
            'user_id' => (string) $this->admin->id,
            'name' => 'CSV Item',
            'qty' => '100',
            'del' => 'no',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('wbdistributions')->insert([
            'user_id' => (string) $this->admin->id,
            'waybill_id' => (string) $waybill->id,
            'item_id' => (string) $itemId,
            'q1' => '4',
            'q2' => '2',
            'del' => 'no',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->actingAs($this->admin)->get('/distreport/export');

        $response->assertOk();
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('MT-CSV-001', $response->streamedContent());
        $this->assertStringContainsString('CSV Corp', $response->streamedContent());
    }

    public function test_distreport_shows_all_active_branch_columns(): void
    {
        DB::table('company_branches')->insert([
            'user_id' => (string) $this->admin->id,
            'name' => 'Branch C',
            'loc' => 'Loc 3',
            'contact' => '0000000003',
            'tag' => '3',
            'del' => 'no',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $waybill = Waybill::create([
            'user_id' => (string) $this->admin->id,
            'stock_no' => 'ST-BR-COLS',
            'comp_name' => 'Branch Col Corp',
            'comp_add' => '12 Market Street',
            'comp_contact' => '0244000000',
            'drv_name' => 'John Driver',
            'drv_contact' => '0244111111',
            'vno' => 'GR-1234-20',
            'bill_no' => 'WB-BR-COLS',
            'weight' => '10',
            'nop' => '2',
            'tot_qty' => '5',
            'del_date' => '2026-07-15',
            'status' => 'Delivered',
            'del' => 'no',
        ]);

        $itemId = DB::table('items')->insertGetId([
            'item_no' => 'MT-BR-COLS',
            'user_id' => (string) $this->admin->id,
            'name' => 'Branch Column Item',
            'qty' => '100',
            'del' => 'no',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('wbdistributions')->insert([
            'user_id' => (string) $this->admin->id,
            'waybill_id' => (string) $waybill->id,
            'item_id' => (string) $itemId,
            'q1' => '1',
            'q2' => '2',
            'q3' => '3',
            'del' => 'no',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($this->admin)
            ->get('/distreport')
            ->assertOk()
            ->assertSee('Br 1')
            ->assertSee('Br 2')
            ->assertSee('Br 3');
    }

    public function test_distreport_print_loads_from_query_params_without_session(): void
    {
        $waybill = Waybill::create([
            'user_id' => (string) $this->admin->id,
            'stock_no' => 'ST-DIST-QP',
            'comp_name' => 'Query Corp',
            'comp_add' => '12 Market Street',
            'comp_contact' => '0244000000',
            'drv_name' => 'John Driver',
            'drv_contact' => '0244111111',
            'vno' => 'GR-1234-20',
            'bill_no' => 'WB-DIST-QP',
            'weight' => '10',
            'nop' => '2',
            'tot_qty' => '20',
            'del_date' => '2026-07-15',
            'status' => 'Delivered',
            'del' => 'no',
        ]);

        $itemId = DB::table('items')->insertGetId([
            'item_no' => 'MT-DIST-QP',
            'user_id' => (string) $this->admin->id,
            'name' => 'Query Item',
            'qty' => '100',
            'del' => 'no',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('wbdistributions')->insert([
            'user_id' => (string) $this->admin->id,
            'waybill_id' => (string) $waybill->id,
            'item_id' => (string) $itemId,
            'q1' => '2',
            'del' => 'no',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($this->admin)
            ->get('/distreportprint?date_from='.now()->toDateString())
            ->assertOk()
            ->assertSee('MT-DIST-QP')
            ->assertSee('Query Corp')
            ->assertSee('Br 1');
    }

    public function test_distreport_print_handles_missing_waybill(): void
    {
        DB::table('wbdistributions')->insert([
            'user_id' => (string) $this->admin->id,
            'waybill_id' => '99999',
            'item_id' => '99999',
            'q1' => '1',
            'del' => 'no',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($this->admin)->get('/distreport')->assertOk();

        $this->actingAs($this->admin)
            ->get('/distreportprint')
            ->assertOk()
            ->assertSee('Item unavailable');
    }
}
