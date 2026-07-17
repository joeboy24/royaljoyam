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

class ReportPrintPageTest extends TestCase
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

    public function test_returns_print_respects_search_query_without_session(): void
    {
        OrderReturn::create([
            'user_id' => (string) $this->admin->id,
            'sale_id' => '1',
            'item_id' => '1',
            'user_bv' => '1',
            'item_no' => 'RTN-PRINT-001',
            'name' => 'Printed Widget',
            'qty' => '1',
            'cost_price' => '10',
            'unit_price' => '25',
            'profits' => '15',
            'tot' => '25',
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
            'item_no' => 'RTN-PRINT-002',
            'name' => 'Hidden Gadget',
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
            ->get('/returnprint?returnsearch=Printed+Widget')
            ->assertOk()
            ->assertSee('Printed Widget')
            ->assertSee('RTN-PRINT-001')
            ->assertDontSee('Hidden Gadget');
    }

    public function test_debts_print_respects_search_query_without_session(): void
    {
        Sale::create([
            'user_id' => (string) $this->admin->id,
            'user_bv' => '1',
            'order_no' => 'DEBT-PRINT-001',
            'qty' => '1',
            'tot' => '300',
            'pay_mode' => Sale::PAY_MODE_DEBT,
            'buy_name' => 'Print Buyer',
            'buy_contact' => '0244000000',
            'notes' => 'Collect on Friday',
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
            'order_no' => 'DEBT-PRINT-002',
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
            ->get('/debtsreportprinting?debtsearch=Print+Buyer')
            ->assertOk()
            ->assertSee('DEBT-PRINT-001')
            ->assertSee('Collect on Friday')
            ->assertDontSee('DEBT-PRINT-002');
    }

    public function test_expenses_print_loads_filtered_rows_from_query(): void
    {
        Expense::create([
            'user_id' => (string) $this->admin->id,
            'companybranch_id' => '1',
            'title' => 'Print Fuel',
            'desc' => 'Generator',
            'expense_cost' => '50',
            'del' => 'no',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($this->admin)
            ->withSession(['date_today' => now()->format('Y-m-d')])
            ->get('/expensereportprinting')
            ->assertOk()
            ->assertSee('Print Fuel')
            ->assertSee('Expenses Report');
    }

    public function test_sales_report_page_does_not_include_related_report_links(): void
    {
        $this->actingAs($this->admin)
            ->get('/reporting')
            ->assertOk()
            ->assertDontSee('dash-reports-related-links', false)
            ->assertDontSee('Expenses report', false);
    }
}
