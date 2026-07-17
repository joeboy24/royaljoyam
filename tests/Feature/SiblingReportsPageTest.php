<?php

namespace Tests\Feature;

use App\Models\Expense;
use App\Models\OrderReturn;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SiblingReportsPageTest extends TestCase
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

        DB::table('company_branches')->insert([
            'user_id' => '1',
            'name' => 'Branch A',
            'loc' => 'Loc 1',
            'contact' => '0000000001',
            'tag' => '1',
            'del' => 'no',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('closures')->insert([
            'user_id' => '1',
            'month' => now()->format('Y-m-01'),
            'tot_qty' => '0',
            'avl_qty' => '0',
            'amt_sold' => '0',
            'exp_amt' => '0',
            'profits' => '0',
            'q1' => '0',
            'q2' => '0',
            'q3' => '0',
            'q4' => '0',
            'q5' => '0',
            'q6' => '0',
            'q7' => '0',
            'status' => 'open',
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

    public function test_expenses_report_shows_empty_state_when_no_records(): void
    {
        $today = now()->format('Y-m-d');

        $this->actingAs($this->admin)
            ->withSession(['date_today' => $today])
            ->get('/expensereport')
            ->assertOk()
            ->assertSee('Expenses Report')
            ->assertSee('Expense filters', false)
            ->assertSee('dash-reports-empty', false)
            ->assertSee('No expenses found for the selected filters.');
    }

    public function test_expenses_report_renders_modern_table_for_matching_record(): void
    {
        $today = now()->format('Y-m-d');

        Expense::create([
            'user_id' => (string) $this->admin->id,
            'companybranch_id' => '1',
            'title' => 'Fuel refill',
            'desc' => 'Generator fuel',
            'expense_cost' => '75',
            'del' => 'no',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($this->admin)
            ->withSession(['date_today' => $today])
            ->get('/expensereport')
            ->assertOk()
            ->assertSee('dash-reports-data-table', false)
            ->assertSee('Fuel refill')
            ->assertSee('Generator fuel')
            ->assertSee('Total expenditure:', false)
            ->assertSee('Gh₵ 75.00');
    }

    public function test_returns_report_shows_empty_state_when_no_records(): void
    {
        $this->actingAs($this->admin)
            ->get('/returnsreport')
            ->assertOk()
            ->assertSee('Returns Report')
            ->assertSee('Returns filters', false)
            ->assertSee('No returns found for the selected filters.');
    }

    public function test_returns_report_renders_modern_table_for_matching_record(): void
    {
        OrderReturn::create([
            'user_id' => (string) $this->admin->id,
            'sale_id' => '1',
            'item_id' => '1',
            'user_bv' => '1',
            'item_no' => 'RTN-001',
            'name' => 'Returned Widget',
            'qty' => '2',
            'cost_price' => '10',
            'unit_price' => '25',
            'profits' => '30',
            'tot' => '50',
            'del_status' => 'Delivered',
            'del' => 'no',
            'order_date' => now()->format('Y-m-d'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        OrderReturn::create([
            'user_id' => (string) $this->admin->id,
            'sale_id' => '2',
            'item_id' => '2',
            'user_bv' => '1',
            'item_no' => 'RTN-002',
            'name' => 'Other Gadget',
            'qty' => '1',
            'cost_price' => '5',
            'unit_price' => '15',
            'profits' => '10',
            'tot' => '15',
            'del_status' => 'Delivered',
            'del' => 'no',
            'order_date' => now()->format('Y-m-d'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($this->admin)
            ->get('/returnsreport')
            ->assertOk()
            ->assertSee('dash-reports-data-table', false)
            ->assertSee('Returned Widget')
            ->assertSee('RTN-001')
            ->assertSee('Total qty:', false);

        $this->actingAs($this->admin)
            ->get('/returnsreport?returnsearch=Returned+Widget')
            ->assertOk()
            ->assertSee('Returned Widget')
            ->assertSee('RTN-001')
            ->assertDontSee('RTN-002');
    }

    public function test_debts_report_shows_empty_state_when_no_records(): void
    {
        $this->actingAs($this->admin)
            ->get('/debts')
            ->assertOk()
            ->assertSee('Debts Report')
            ->assertSee('Debts filters', false)
            ->assertSee('/paid_debts', false)
            ->assertSee('Paid debts', false)
            ->assertSee('No outstanding debts found for the selected filters.');
    }

    public function test_debts_report_renders_modern_table_and_shared_modals(): void
    {
        Sale::create([
            'user_id' => (string) $this->admin->id,
            'user_bv' => '1',
            'order_no' => 'DEBT-001',
            'qty' => '1',
            'tot' => '300',
            'pay_mode' => Sale::PAY_MODE_DEBT,
            'buy_name' => 'Debt Buyer',
            'buy_contact' => '0244000000',
            'del_status' => 'Delivered',
            'discount' => '0',
            'payment' => '0',
            'change' => '0',
            'paid' => 'no',
            'paid_debt' => '0',
            'del' => 'no',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Sale::create([
            'user_id' => (string) $this->admin->id,
            'user_bv' => '1',
            'order_no' => 'DEBT-002',
            'qty' => '1',
            'tot' => '150',
            'pay_mode' => Sale::PAY_MODE_DEBT,
            'buy_name' => 'Other Buyer',
            'buy_contact' => '0244111111',
            'del_status' => 'Delivered',
            'discount' => '0',
            'payment' => '0',
            'change' => '0',
            'paid' => 'no',
            'paid_debt' => '0',
            'del' => 'no',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($this->admin)
            ->get('/debts')
            ->assertOk()
            ->assertSee('dash-reports-data-table', false)
            ->assertSee('DEBT-001')
            ->assertSee('Debt Buyer')
            ->assertSee('reportPayDebtModal', false)
            ->assertSee('reportEditOrderModal', false)
            ->assertSee('Outstanding total:', false)
            ->assertSee('Gh₵ 450.00');

        $this->actingAs($this->admin)
            ->get('/debts?debtsearch=Debt+Buyer')
            ->assertOk()
            ->assertSee('DEBT-001')
            ->assertSee('Debt Buyer')
            ->assertDontSee('DEBT-002')
            ->assertSee('Gh₵ 300.00');
    }

    public function test_debts_report_filters_by_cleared_status(): void
    {
        Sale::create([
            'user_id' => (string) $this->admin->id,
            'user_bv' => '1',
            'order_no' => 'DEBT-CLEARED',
            'qty' => '1',
            'tot' => '200',
            'pay_mode' => Sale::PAY_MODE_DEBT,
            'buy_name' => 'Cleared Buyer',
            'buy_contact' => '0244222222',
            'del_status' => 'Delivered',
            'discount' => '0',
            'payment' => '200',
            'change' => '0',
            'paid' => 'Paid',
            'paid_debt' => '200',
            'del' => 'no',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Sale::create([
            'user_id' => (string) $this->admin->id,
            'user_bv' => '1',
            'order_no' => 'DEBT-OPEN',
            'qty' => '1',
            'tot' => '100',
            'pay_mode' => Sale::PAY_MODE_DEBT,
            'buy_name' => 'Open Buyer',
            'buy_contact' => '0244333333',
            'del_status' => 'Delivered',
            'discount' => '0',
            'payment' => '0',
            'change' => '0',
            'paid' => 'no',
            'paid_debt' => '0',
            'del' => 'no',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($this->admin)
            ->get('/debts?debt_status=cleared')
            ->assertOk()
            ->assertSee('DEBT-CLEARED')
            ->assertSee('Cleared total:', false)
            ->assertDontSee('DEBT-OPEN');

        $this->actingAs($this->admin)
            ->get('/debts?debt_status=all')
            ->assertOk()
            ->assertSee('DEBT-CLEARED')
            ->assertSee('DEBT-OPEN')
            ->assertSee('Debt orders total:', false);
    }

    public function test_report_nav_preserves_active_filters(): void
    {
        $this->actingAs($this->admin)
            ->get('/debts?date_from=2026-07-01&date_to=2026-07-16&branch=1&debt_status=cleared')
            ->assertOk()
            ->assertSee('date_from=2026-07-01', false)
            ->assertSee('date_to=2026-07-16', false)
            ->assertSee('branch=1', false)
            ->assertSee('debt_status=cleared', false);
    }

    public function test_expenses_and_returns_reports_do_not_include_related_links(): void
    {
        $today = now()->format('Y-m-d');

        $this->actingAs($this->admin)
            ->withSession(['date_today' => $today])
            ->get('/expensereport')
            ->assertOk()
            ->assertDontSee('dash-reports-related-links', false);

        $this->actingAs($this->admin)
            ->get('/returnsreport')
            ->assertOk()
            ->assertDontSee('dash-reports-related-links', false);
    }

    public function test_stock_balances_report_shows_empty_state_when_no_data_loaded(): void
    {
        $this->actingAs($this->admin)
            ->withSession(['date_today' => now()->format('Y-m-d')])
            ->get('/stockbal')
            ->assertOk()
            ->assertSee('Stock Balances')
            ->assertSee('Branch transfers', false)
            ->assertSee('/branchtransfers', false)
            ->assertSee('No stock data for the selected filters.');
    }

    public function test_branch_transfers_report_shows_empty_state_when_no_records(): void
    {
        $today = now()->format('Y-m-d');

        $this->actingAs($this->admin)
            ->withSession(['date_today' => $today])
            ->get('/branchtransfers')
            ->assertOk()
            ->assertSee('Branch Transfers')
            ->assertSee('Transfer filters', false)
            ->assertSee('Stock balances', false)
            ->assertSee('No branch transfers found for the selected filters.');
    }

    public function test_branch_transfers_report_lists_matching_transfer(): void
    {
        $item = \App\Models\Item::create([
            'item_no' => 'TR-001',
            'user_id' => (string) $this->admin->id,
            'name' => 'Transfer Rod',
            'desc' => 'Steel rod',
            'cat' => 'General',
            'brand' => 'Brand',
            'barcode' => 'TR001',
            'qty' => '100',
            'price' => '10.00',
            'cost_price' => '8.00',
            'q1' => '100',
            'q2' => '0',
            'q3' => '0',
            'del' => 'no',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        \App\Models\BranchTransfer::create([
            'user_id' => (string) $this->admin->id,
            'item_id' => (string) $item->id,
            'from_branch' => '2',
            'to_branch' => '1',
            'qty' => '30',
            'notes' => 'Customer pickup from Branch B',
            'del' => 'no',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $today = now()->format('Y-m-d');

        $this->actingAs($this->admin)
            ->withSession(['date_today' => $today])
            ->get('/branchtransfers?transfersearch=Transfer+Rod')
            ->assertOk()
            ->assertSee('Transfer Rod')
            ->assertSee('Customer pickup from Branch B')
            ->assertSee('Total qty moved:', false)
            ->assertSee('30', false);

        $this->actingAs($this->admin)
            ->withSession(['date_today' => $today])
            ->get('/branchtransfers?from_branch=2&to_branch=1')
            ->assertOk()
            ->assertSee('Transfer Rod')
            ->assertSee('Branch A', false)
            ->assertSee('Branch B', false);
    }

    public function test_branch_transfers_report_date_to_without_from_redirects_with_error(): void
    {
        $this->actingAs($this->admin)
            ->from('/branchtransfers')
            ->get('/branchtransfers?date_to=2026-07-16')
            ->assertRedirect('/branchtransfers')
            ->assertSessionHas('error');
    }
}
