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

    public function test_paid_debts_by_branch_groups_payments_by_sale_branch(): void
    {
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

        $paidDebts = $this->service->paidDebtsByBranch(collect([$debtSale]));

        $this->assertSame(55.0, $paidDebts['1']);
    }
}
