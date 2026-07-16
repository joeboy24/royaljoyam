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

    public function test_stock_balances_report_shows_empty_state_when_no_data_loaded(): void
    {
        $this->actingAs($this->admin)
            ->withSession(['date_today' => now()->format('Y-m-d')])
            ->get('/stockbal')
            ->assertOk()
            ->assertSee('Stock Balances')
            ->assertSee('No stock data for the selected filters.');
    }
}
