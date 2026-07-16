<?php

namespace Tests\Unit;

use App\Models\Sale;
use App\Models\SalesPayment;
use App\Models\User;
use App\Services\SalesReportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SalesReportServiceTest extends TestCase
{
    use RefreshDatabase;

    protected SalesReportService $service;

    protected User $branchUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(SalesReportService::class);
        $this->seedBaseline();
        $this->branchUser = $this->createBranchUser('1', 'Branch A');
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
    }

    protected function createBranchUser(string $bv, string $status): User
    {
        $id = DB::table('users')->insertGetId([
            'company_branch_id' => '1',
            'name' => 'branch.'.$bv,
            'email' => 'branch'.$bv.'@test.example',
            'bv' => $bv,
            'status' => $status,
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
            'order_no' => 'SRV-ORD-'.str_pad((string) $counter, 4, '0', STR_PAD_LEFT),
            'qty' => '1',
            'tot' => '100',
            'pay_mode' => Sale::PAY_MODE_CASH,
            'buy_name' => 'Buyer',
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

    protected function createSalesHistory(Sale $sale, array $overrides = []): void
    {
        DB::table('sales_histories')->insert(array_merge([
            'user_id' => (string) $sale->user_id,
            'sale_id' => (string) $sale->id,
            'item_id' => '1',
            'user_bv' => (string) $sale->user_bv,
            'item_no' => 'ITEM-001',
            'name' => 'Test Item',
            'qty' => '1',
            'cost_price' => '70',
            'unit_price' => '100',
            'profits' => '30',
            'tot' => '100',
            'del_status' => (string) $sale->del_status,
            'del' => 'no',
            'created_at' => $sale->created_at,
            'updated_at' => $sale->updated_at,
        ], $overrides));
    }

    public function test_build_aggregates_payment_modes_and_branch_totals(): void
    {
        $today = now()->format('Y-m-d');

        $this->createSale([
            'order_no' => 'SRV-CASH-1',
            'tot' => '200',
            'pay_mode' => Sale::PAY_MODE_CASH,
            'user_bv' => '1',
        ]);

        $branchTwoUser = $this->createBranchUser('2', 'Branch B');
        $this->createSale([
            'order_no' => 'SRV-CASH-2',
            'tot' => '150',
            'pay_mode' => Sale::PAY_MODE_CASH,
            'user_id' => (string) $branchTwoUser->id,
            'user_bv' => '2',
        ]);

        $report = $this->service->build([
            'date_from' => $today,
            'branch' => 'All Branches',
            'delvr' => 'Del. / Not Delivered',
            'session_sales_date' => $today,
        ]);

        $legacy = $this->service->toLegacyViewData($report);

        $this->assertSame(350.0, $report['cash']);
        $this->assertSame(200.0, $legacy['b1']);
        $this->assertSame(150.0, $legacy['b2']);
        $this->assertSame(200.0, $legacy['cash_b1']);
        $this->assertSame(150.0, $legacy['cash_b2']);
    }

    public function test_build_limits_branch_metrics_when_single_branch_selected(): void
    {
        $today = now()->format('Y-m-d');

        $branchTwoUser = $this->createBranchUser('2', 'Branch B');
        $this->createSale([
            'order_no' => 'SRV-B1',
            'tot' => '300',
            'user_bv' => '1',
        ]);
        $this->createSale([
            'order_no' => 'SRV-B2',
            'tot' => '400',
            'user_id' => (string) $branchTwoUser->id,
            'user_bv' => '2',
        ]);

        $report = $this->service->build([
            'date_from' => $today,
            'branch' => '2',
            'delvr' => 'Del. / Not Delivered',
            'session_sales_date' => $today,
        ]);

        $legacy = $this->service->toLegacyViewData($report);

        $this->assertSame(400.0, $report['cash']);
        $this->assertSame(0.0, $legacy['b1']);
        $this->assertSame(400.0, $legacy['b2']);
    }

    public function test_collected_debt_by_branch_groups_payments_by_sale_branch_and_date(): void
    {
        $today = now()->format('Y-m-d');

        $debtSale = $this->createSale([
            'order_no' => 'SRV-DEBT',
            'pay_mode' => Sale::PAY_MODE_DEBT,
            'paid' => 'Not Paid',
            'user_bv' => '1',
        ]);

        SalesPayment::create([
            'user_id' => (string) $this->branchUser->id,
            'sale_id' => (string) $debtSale->id,
            'amt_paid' => '55',
            'bal' => '45',
            'del' => 'no',
        ]);

        $paidDebts = $this->service->collectedDebtByBranch(
            'All Branches',
            'single_date',
            $today,
            null,
            $today
        );

        $this->assertSame(55.0, $paidDebts['1']);
    }

    public function test_collected_debt_by_branch_includes_payments_on_older_sales_for_selected_day(): void
    {
        $today = now()->format('Y-m-d');
        $yesterday = now()->subDay()->format('Y-m-d');

        $oldDebtSale = $this->createSale([
            'order_no' => 'SRV-OLD-DEBT',
            'pay_mode' => Sale::PAY_MODE_DEBT,
            'paid' => 'Not Paid',
            'user_bv' => '1',
        ]);
        $oldDebtSale->forceFill([
            'created_at' => $yesterday.' 15:00:00',
            'updated_at' => $yesterday.' 15:00:00',
        ])->saveQuietly();

        SalesPayment::create([
            'user_id' => (string) $this->branchUser->id,
            'sale_id' => (string) $oldDebtSale->id,
            'amt_paid' => '125',
            'bal' => '0',
            'del' => 'no',
        ]);

        $legacyPaidDebts = $this->service->paidDebtsByBranch(collect([
            $this->createSale(['order_no' => 'SRV-UNRELATED', 'user_bv' => '1']),
        ]));
        $this->assertSame(0.0, $legacyPaidDebts['1'] ?? 0.0);

        $collectedDebts = $this->service->collectedDebtByBranch(
            'All Branches',
            'single_date',
            $today,
            null,
            $today
        );

        $this->assertSame(125.0, $collectedDebts['1']);
    }

    public function test_collected_debt_by_branch_includes_checkout_payment_without_sales_payment_row(): void
    {
        $today = now()->format('Y-m-d');

        $debtSale = $this->createSale([
            'order_no' => 'SRV-DEBT-CHECKOUT',
            'pay_mode' => Sale::PAY_MODE_DEBT,
            'tot' => '500',
            'payment' => '150',
            'paid' => 'No',
            'user_bv' => '1',
        ]);

        $paidDebts = $this->service->collectedDebtByBranch(
            'All Branches',
            'single_date',
            $today,
            null,
            $today
        );

        $this->assertSame(150.0, $paidDebts['1']);
    }

    public function test_build_breakdown_table_calculates_net_total_from_collected_payments_minus_expenses(): void
    {
        $today = now()->format('Y-m-d');

        $cashSale = $this->createSale([
            'order_no' => 'SRV-CASH',
            'tot' => '500',
            'user_bv' => '1',
        ]);
        $debtSale = $this->createSale([
            'order_no' => 'SRV-DEBT',
            'pay_mode' => Sale::PAY_MODE_DEBT,
            'tot' => '200',
            'user_bv' => '1',
        ]);

        SalesPayment::create([
            'user_id' => (string) $this->branchUser->id,
            'sale_id' => (string) $debtSale->id,
            'amt_paid' => '80',
            'bal' => '120',
            'del' => 'no',
        ]);

        DB::table('expenses')->insert([
            'user_id' => (string) $this->branchUser->id,
            'companybranch_id' => '1',
            'title' => 'Supplies',
            'desc' => 'Test expense',
            'expense_cost' => '120',
            'del' => 'no',
            'created_at' => $today.' 10:00:00',
            'updated_at' => $today.' 10:00:00',
        ]);

        $report = $this->service->build([
            'date_from' => $today,
            'branch' => 'All Branches',
            'delvr' => 'Del. / Not Delivered',
            'session_sales_date' => $today,
        ]);

        $breakdown = $this->service->buildBreakdownTable($report);

        $this->assertSame(460.0, $breakdown['net_total']);
        $this->assertSame(460.0, $breakdown['columns'][0]['net']);
        $this->assertSame('Paid debts collected', $breakdown['rows'][3]['subrow']['label']);
        $this->assertSame('Cash in drawer (est.)', $breakdown['rows'][6]['label']);
        $this->assertSame(460.0, $breakdown['columns'][0]['cash_at_hand']);
    }

    public function test_branch_net_total_uses_collected_debt_not_outstanding_debt_sales(): void
    {
        $metric = [
            'cash' => 770.0,
            'cheque' => 0.0,
            'momo' => 0.0,
            'debt' => 1500.0,
            'expenses' => 1200.0,
            'profits' => 230.0,
            'sales_total' => 2270.0,
            'tag' => '1',
        ];

        $this->assertSame(770.0, $this->service->branchNetTotal($metric, 1200.0));
    }

    public function test_build_uses_session_sales_date_when_no_date_filters(): void
    {
        $sessionDate = now()->subDay()->format('Y-m-d');

        $sessionSale = $this->createSale([
            'order_no' => 'SRV-SESSION-DAY',
            'tot' => '275',
        ]);
        $sessionSale->forceFill([
            'created_at' => $sessionDate.' 11:00:00',
            'updated_at' => $sessionDate.' 11:00:00',
        ])->saveQuietly();

        $this->createSale([
            'order_no' => 'SRV-TODAY',
            'tot' => '999',
        ]);

        $report = $this->service->build([
            'branch' => 'All Branches',
            'delvr' => 'Del. / Not Delivered',
            'session_sales_date' => $sessionDate,
        ]);

        $this->assertSame(275.0, $report['cash']);
    }

    public function test_branch_cash_at_hand_uses_cash_plus_paid_debts_minus_expenses(): void
    {
        $metric = [
            'cash' => 770.0,
            'cheque' => 0.0,
            'momo' => 0.0,
            'debt' => 1500.0,
            'expenses' => 1200.0,
            'profits' => 230.0,
            'sales_total' => 2270.0,
            'tag' => '1',
        ];

        $this->assertSame(770.0, $this->service->branchCashAtHand($metric, 1200.0));
    }

    public function test_profits_sum_line_item_margins_and_match_branch_total(): void
    {
        $today = now()->format('Y-m-d');

        $saleOne = $this->createSale([
            'order_no' => 'SRV-PROF-1',
            'tot' => '100',
            'user_bv' => '1',
        ]);
        $this->createSalesHistory($saleOne, ['profits' => '30']);

        $saleTwo = $this->createSale([
            'order_no' => 'SRV-PROF-2',
            'tot' => '150',
            'user_bv' => '1',
        ]);
        $this->createSalesHistory($saleTwo, ['profits' => '45']);

        $report = $this->service->build([
            'date_from' => $today,
            'branch' => 'All Branches',
            'delvr' => 'Del. / Not Delivered',
            'session_sales_date' => $today,
        ]);

        $breakdown = $this->service->buildBreakdownTable($report);

        $this->assertSame(75.0, $report['gen_profits']);
        $this->assertSame(75.0, $breakdown['rows'][5]['total']);
        $this->assertSame(75.0, $breakdown['columns'][0]['metric']['profits']);
    }

    public function test_profits_and_branch_totals_respect_delivery_filter(): void
    {
        $today = now()->format('Y-m-d');

        $deliveredSale = $this->createSale([
            'order_no' => 'SRV-DELIVERED',
            'tot' => '200',
            'del_status' => 'Delivered',
            'user_bv' => '1',
        ]);
        $this->createSalesHistory($deliveredSale, ['profits' => '80']);

        $pendingSale = $this->createSale([
            'order_no' => 'SRV-PENDING',
            'tot' => '120',
            'del_status' => 'Not Delivered',
            'user_bv' => '1',
        ]);
        $this->createSalesHistory($pendingSale, ['profits' => '20', 'del_status' => 'Not Delivered']);

        $report = $this->service->build([
            'date_from' => $today,
            'branch' => 'All Branches',
            'delvr' => 'Delivered',
            'session_sales_date' => $today,
        ]);

        $this->assertSame(200.0, $report['cash']);
        $this->assertSame(80.0, $report['gen_profits']);
        $this->assertSame(200.0, $report['branch_metrics'][0]['sales_total']);
    }

    public function test_breakdown_net_total_matches_sum_of_branch_columns(): void
    {
        $today = now()->format('Y-m-d');

        $this->createSale([
            'order_no' => 'SRV-NET-1',
            'tot' => '300',
            'pay_mode' => Sale::PAY_MODE_CASH,
            'user_bv' => '1',
        ]);

        $debtSale = $this->createSale([
            'order_no' => 'SRV-NET-DEBT',
            'pay_mode' => Sale::PAY_MODE_DEBT,
            'tot' => '100',
            'user_bv' => '1',
        ]);

        SalesPayment::create([
            'user_id' => (string) $this->branchUser->id,
            'sale_id' => (string) $debtSale->id,
            'amt_paid' => '40',
            'bal' => '60',
            'del' => 'no',
        ]);

        DB::table('expenses')->insert([
            'user_id' => (string) $this->branchUser->id,
            'companybranch_id' => '1',
            'title' => 'Fuel',
            'desc' => 'Transport',
            'expense_cost' => '50',
            'del' => 'no',
            'created_at' => $today.' 12:00:00',
            'updated_at' => $today.' 12:00:00',
        ]);

        $report = $this->service->build([
            'date_from' => $today,
            'branch' => 'All Branches',
            'delvr' => 'Del. / Not Delivered',
            'session_sales_date' => $today,
        ]);

        $breakdown = $this->service->buildBreakdownTable($report);
        $branchNetSum = round(array_sum(array_column($breakdown['columns'], 'net')), 2);

        $this->assertSame($breakdown['net_total'], $branchNetSum);
        $this->assertSame(290.0, $breakdown['net_total']);
    }
}
