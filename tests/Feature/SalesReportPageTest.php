<?php

namespace Tests\Feature;

use App\Models\Sale;
use App\Models\SalesPayment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SalesReportPageTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $branchUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedBaseline();
        $this->admin = $this->createAdministrator();
        $this->branchUser = $this->createBranchUser();
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

    protected function createBranchUser(): User
    {
        $id = DB::table('users')->insertGetId([
            'company_branch_id' => '1',
            'name' => 'branch.one',
            'email' => 'branch1@test.example',
            'bv' => '1',
            'status' => 'Branch A',
            'password' => Hash::make('password'),
            'del' => 'no',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return User::findOrFail($id);
    }

    protected function createSale(array $overrides = []): Sale
    {
        static $counter = 0;
        $counter++;

        return Sale::create(array_merge([
            'user_id' => (string) $this->branchUser->id,
            'user_bv' => '1',
            'order_no' => 'RPT-ORD-' . str_pad((string) $counter, 4, '0', STR_PAD_LEFT),
            'qty' => '1',
            'tot' => '100',
            'pay_mode' => Sale::PAY_MODE_CASH,
            'buy_name' => 'Report Buyer',
            'buy_contact' => '0244000000',
            'del_status' => 'Delivered',
            'discount' => '0',
            'payment' => '100',
            'change' => '0',
            'paid' => 'Paid',
            'paid_debt' => '0',
            'del' => 'no',
            'created_at' => now(),
            'updated_at' => now(),
        ], $overrides));
    }

    public function test_non_admin_is_redirected_from_sales_report(): void
    {
        $this->actingAs($this->branchUser)
            ->get('/reporting')
            ->assertRedirect('/dashboard');
    }

    public function test_admin_can_load_sales_report(): void
    {
        $this->actingAs($this->admin)
            ->get('/reporting')
            ->assertOk()
            ->assertSee('dash-reports-nav', false)
            ->assertSee('Sales Report')
            ->assertSee('Load data');
    }

    public function test_sales_report_filter_toolbar_includes_validation_markup(): void
    {
        $this->actingAs($this->admin)
            ->get('/reporting')
            ->assertOk()
            ->assertSee('data-report-filter-form', false)
            ->assertSee('dash-reports-filter-actions', false)
            ->assertSee('Sales filters', false);
    }

    public function test_cash_sales_with_canonical_pay_mode_are_included_in_totals(): void
    {
        $this->createSale([
            'order_no' => 'RPT-CASH-001',
            'tot' => '250',
            'pay_mode' => Sale::PAY_MODE_CASH,
        ]);

        $this->actingAs($this->admin)
            ->get('/reporting?date_from=' . now()->format('Y-m-d'))
            ->assertOk()
            ->assertSee('Total amount:', false)
            ->assertSee('Gh₵ 250.00');
    }

    public function test_sales_report_table_shows_notes_and_modern_columns(): void
    {
        $this->createSale([
            'order_no' => 'RPT-NOTES-001',
            'tot' => '120',
            'notes' => 'Leave at front desk',
        ]);

        $this->actingAs($this->admin)
            ->get('/reporting?date_from=' . now()->format('Y-m-d'))
            ->assertOk()
            ->assertSee('dash-reports-sales-table', false)
            ->assertSee('Leave at front desk', false)
            ->assertSee('reportEditOrderModal', false)
            ->assertSee('Pay mode', false);
    }

    public function test_date_to_without_date_from_redirects_with_error(): void
    {
        $this->actingAs($this->admin)
            ->get('/reporting?date_to=' . now()->format('Y-m-d'))
            ->assertRedirect('/reporting')
            ->assertSessionHas('error');
    }

    public function test_paid_debt_totals_use_full_filtered_set_not_only_current_page(): void
    {
        $today = now()->format('Y-m-d');

        for ($i = 1; $i <= 11; $i++) {
            $this->createSale([
                'order_no' => 'RPT-DEBT-' . str_pad((string) $i, 2, '0', STR_PAD_LEFT),
                'tot' => '100',
                'pay_mode' => Sale::PAY_MODE_DEBT,
                'paid' => 'Not Paid',
            ]);
        }

        $eleventhSale = Sale::where('order_no', 'RPT-DEBT-11')->firstOrFail();

        SalesPayment::create([
            'user_id' => (string) $this->branchUser->id,
            'sale_id' => (string) $eleventhSale->id,
            'amt_paid' => '75',
            'bal' => '25',
            'del' => 'no',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($this->admin)
            ->get('/reporting?date_from=' . $today . '&page=1')
            ->assertOk()
            ->assertSee('Paid debts collected', false)
            ->assertSee('75.00', false)
            ->assertSee('1,100.00');
    }

    public function test_legacy_lowercase_cash_pay_mode_is_included_in_totals(): void
    {
        $this->createSale([
            'order_no' => 'RPT-LEG-CASH',
            'tot' => '180',
            'pay_mode' => 'cash',
        ]);

        $this->actingAs($this->admin)
            ->get('/reporting?date_from=' . now()->format('Y-m-d'))
            ->assertOk()
            ->assertSee('Gh₵ 180.00');
    }

    public function test_pagination_preserves_filter_query_string(): void
    {
        $today = now()->format('Y-m-d');

        for ($i = 1; $i <= 11; $i++) {
            $this->createSale([
                'order_no' => 'RPT-PAGE-' . str_pad((string) $i, 2, '0', STR_PAD_LEFT),
            ]);
        }

        $response = $this->actingAs($this->admin)
            ->get('/reporting?date_from=' . $today . '&branch=All+Branches&delvr=Del.+%2F+Not+Delivered');

        $response->assertOk()
            ->assertSee('date_from=' . $today, false)
            ->assertSee('branch=All%20Branches', false)
            ->assertSee('delvr=Del.', false);
    }

    public function test_changedate_updates_report_when_no_date_filters_are_applied(): void
    {
        $sessionDate = now()->subDay()->format('Y-m-d');

        $sessionSale = $this->createSale([
            'order_no' => 'RPT-SESSION-DATE',
            'tot' => '432',
        ]);
        $sessionSale->forceFill([
            'created_at' => $sessionDate . ' 09:30:00',
            'updated_at' => $sessionDate . ' 09:30:00',
        ])->saveQuietly();

        $this->createSale([
            'order_no' => 'RPT-TODAY-ONLY',
            'tot' => '888',
        ]);

        $this->actingAs($this->admin)
            ->from('/reporting')
            ->get('/changedate?date_today=' . $sessionDate)
            ->assertRedirect('/reporting')
            ->assertSessionHas('date_today', $sessionDate);

        $this->actingAs($this->admin)
            ->withSession(['date_today' => $sessionDate])
            ->get('/reporting')
            ->assertOk()
            ->assertSee('432.00', false)
            ->assertDontSee('888.00', false);
    }

    public function test_breakdown_modal_includes_cash_at_hand_row(): void
    {
        $today = now()->format('Y-m-d');

        $this->createSale([
            'order_no' => 'RPT-CASH-HAND',
            'tot' => '500',
        ]);

        $this->actingAs($this->admin)
            ->get('/reporting?date_from=' . $today)
            ->assertOk()
            ->assertSee('Cash in drawer (est.)', false)
            ->assertSee('dash-reports-breakdown-formulas', false)
            ->assertSee('dash-reports-breakdown-formula-chain', false)
            ->assertSee('dash-reports-breakdown-tag--drawer', false)
            ->assertSee('dash-reports-breakdown-formula-badge--drawer', false)
            ->assertSee('dash-reports-breakdown-formula-badge--orange', false)
            ->assertDontSee('data-rail-drawer', false)
            ->assertDontSee('data-rail-orange', false);
    }

    public function test_saleshistory_redirects_to_sales_report_with_filters(): void
    {
        $this->actingAs($this->admin)
            ->get('/saleshistory?date_from=2026-07-01&date_to=2026-07-16&branch=1')
            ->assertRedirect('/reporting?date_from=2026-07-01&date_to=2026-07-16&branch=1')
            ->assertSessionHas('info', 'Sales history is available on the main Sales report.');
    }
}
