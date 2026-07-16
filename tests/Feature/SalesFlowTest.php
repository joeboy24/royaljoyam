<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\Sale;
use App\Models\SalesPayment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SalesFlowTest extends TestCase
{
    use RefreshDatabase;

    protected User $branchUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedSalesBaseline();
        $this->branchUser = $this->createBranchUser('branch.one', 'branch1@test.example', 'Branch A', '1');
    }

    protected function seedSalesBaseline(): void
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

    protected function createBranchUser(string $name, string $email, string $status, string $bv): User
    {
        $id = DB::table('users')->insertGetId([
            'company_branch_id' => '1',
            'name' => $name,
            'email' => $email,
            'bv' => $bv,
            'status' => $status,
            'password' => Hash::make('password'),
            'del' => 'no',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return User::findOrFail($id);
    }

    protected function createItem(array $overrides = []): Item
    {
        $id = DB::table('items')->insertGetId(array_merge([
            'item_no' => 'ITM-0001',
            'user_id' => (string) $this->branchUser->id,
            'name' => 'Sample Tile',
            'desc' => 'Sample',
            'cat' => 'Building',
            'brand' => 'Brand',
            'barcode' => 'BC1',
            'qty' => '100',
            'price' => '35.00',
            'cost_price' => '35.00',
            'q1' => '100',
            'q2' => '0',
            'q3' => '0',
            'b1' => '35.00',
            'b2' => '35.00',
            'b3' => '35.00',
            'del' => 'no',
            'created_at' => now(),
            'updated_at' => now(),
        ], $overrides));

        return Item::findOrFail($id);
    }

    protected function cartPayload(Item $item, int $qty = 2): array
    {
        return [
            'item_id' => $item->id,
            'item_no' => $item->item_no,
            'name' => $item->name,
            'qty' => $qty,
            'price' => 35.00,
        ];
    }

    protected function createSale(array $overrides = []): Sale
    {
        static $orderCounter = 0;
        $orderCounter++;

        return Sale::create(array_merge([
            'user_id' => (string) $this->branchUser->id,
            'user_bv' => '1',
            'order_no' => 'MTEST' . str_pad((string) $orderCounter, 4, '0', STR_PAD_LEFT) . now()->format('is'),
            'qty' => '2',
            'tot' => '70',
            'pay_mode' => 'Cash',
            'buy_name' => 'Jane Buyer',
            'buy_contact' => '0244000000',
            'del_status' => 'Delivered',
            'discount' => '0',
            'payment' => '70',
            'change' => '0',
            'paid' => 'Paid',
            'paid_debt' => '0',
            'del' => 'no',
        ], $overrides));
    }

    public function test_post_payment_checkout_records_partial_debt(): void
    {
        $item = $this->createItem();
        $this->actingAs($this->branchUser)->post('/sales/cart', $this->cartPayload($item, 2));

        $response = $this->actingAs($this->branchUser)->post('/sales/checkout', [
            'pay_mode' => 'Post Payment(Debt)',
            'del_status' => 'Not Delivered',
            'buy_name' => 'Debt Buyer',
            'buy_contact' => '0244111111',
            'discount' => 0,
            'payment' => 50,
        ]);

        $response->assertRedirect('/sales');
        $response->assertSessionHas('success');

        $sale = Sale::first();
        $this->assertNotNull($sale);
        $this->assertSame('Post Payment(Debt)', $sale->pay_mode);
        $this->assertSame('No', $sale->paid);
        $this->assertSame(-20.0, (float) $sale->change);
        $this->assertSame(50.0, (float) $sale->paid_debt);
        $this->assertSame(20.0, $sale->debtBalance());
        $this->assertSame(1, SalesPayment::count());
    }

    public function test_pay_debt_clears_remaining_balance(): void
    {
        $sale = $this->createSale([
            'tot' => '100',
            'payment' => '30',
            'change' => '-70',
            'pay_mode' => 'Post Payment(Debt)',
            'paid' => 'No',
            'paid_debt' => '30',
            'del_status' => 'Not Delivered',
        ]);

        SalesPayment::create([
            'user_id' => (string) $this->branchUser->id,
            'sale_id' => $sale->id,
            'amt_paid' => 30,
            'bal' => 70,
            'del' => 'no',
        ]);

        $response = $this->actingAs($this->branchUser)->post('/sales/pay-debt', [
            'send_id' => $sale->id,
            'send_tot' => 100,
            'amt_paid' => 70,
        ]);

        $response->assertSessionHas('success');

        $sale->refresh();
        $this->assertSame('Paid', $sale->paid);
        $this->assertSame(100.0, (float) $sale->paid_debt);
        $this->assertSame(0.0, $sale->debtBalance());
    }

    public function test_pay_debt_rejects_amount_above_remaining_balance(): void
    {
        $sale = $this->createSale([
            'tot' => '100',
            'payment' => '30',
            'change' => '-70',
            'pay_mode' => 'Post Payment(Debt)',
            'paid' => 'No',
            'paid_debt' => '30',
        ]);

        $response = $this->actingAs($this->branchUser)->post('/sales/pay-debt', [
            'send_id' => $sale->id,
            'send_tot' => 100,
            'amt_paid' => 80,
        ]);

        $response->assertRedirect('/sales');
        $response->assertSessionHas('error');

        $sale->refresh();
        $this->assertSame('No', $sale->paid);
        $this->assertSame(30.0, (float) $sale->paid_debt);
    }

    public function test_deliverer_toggles_sale_delivery_status(): void
    {
        $sale = $this->createSale(['del_status' => 'Delivered']);

        $response = $this->actingAs($this->branchUser)->get('/deliverer?' . http_build_query([
            'deliverer' => $sale->id,
            'deliverer_text' => 'Not Delivered',
        ]));

        $response->assertRedirect('/sales');
        $response->assertSessionHas('success');

        $this->assertSame('Not Delivered', $sale->fresh()->del_status);
    }

    public function test_sales_page_filters_by_pay_mode_and_status(): void
    {
        $today = now()->format('Y-m-d');

        $this->createSale([
            'order_no' => 'MCASH' . now()->format('is'),
            'pay_mode' => 'Cash',
            'del_status' => 'Delivered',
            'buy_name' => 'Cash Customer',
        ]);

        $this->createSale([
            'order_no' => 'MDEBT' . now()->format('is'),
            'pay_mode' => 'Post Payment(Debt)',
            'del_status' => 'Not Delivered',
            'paid' => 'No',
            'payment' => '0',
            'change' => '0',
            'buy_name' => 'Debt Customer',
        ]);

        $response = $this->withSession(['date_today' => $today])
            ->actingAs($this->branchUser)
            ->get('/sales?' . http_build_query([
                'pay_mode' => 'Post Payment(Debt)',
                'status' => 'Not Delivered',
            ]));

        $response->assertOk();
        $response->assertSee('Debt Customer');
        $response->assertDontSee('Cash Customer');
        $response->assertViewHas('sales', function ($paginator) {
            return $paginator->total() === 1
                && $paginator->first()->pay_mode === 'Post Payment(Debt)'
                && $paginator->first()->del_status === 'Not Delivered';
        });
    }

    public function test_sales_log_renders_status_badges_and_notes_column(): void
    {
        $today = now()->format('Y-m-d');

        $this->createSale([
            'order_no' => 'MNOTE' . now()->format('is'),
            'pay_mode' => 'Post Payment(Debt)',
            'del_status' => 'Not Delivered',
            'paid' => 'No',
            'payment' => '0',
            'change' => '0',
            'buy_name' => 'Notes Customer',
            'notes' => str_repeat('Delivery note ', 8),
        ]);

        $response = $this->withSession(['date_today' => $today])
            ->actingAs($this->branchUser)
            ->get('/sales');

        $response->assertOk()
            ->assertSee('dash-sales-badge--debt', false)
            ->assertSee('dash-sales-payment-badge--debt', false)
            ->assertSee('Outstanding')
            ->assertSee('view_notes', false)
            ->assertSee('delivery-form-', false)
            ->assertSee('Notes Customer');
    }

    public function test_sales_page_includes_barcode_in_pos_catalog(): void
    {
        $this->createItem(['barcode' => 'SCAN-12345', 'name' => 'Barcode Tile']);

        $response = $this->withSession(['date_today' => now()->format('Y-m-d')])
            ->actingAs($this->branchUser)
            ->get('/sales');

        $response->assertOk()
            ->assertSee('SCAN-12345', false)
            ->assertSee('data-barcode="SCAN-12345"', false)
            ->assertSee('scan barcode', false);
    }

    public function test_sales_page_totals_count_collected_money_for_selected_day(): void
    {
        $today = '2026-07-06';

        $mobileSale = $this->createSale([
            'order_no' => 'MMCSC42347',
            'pay_mode' => 'Mobile Money',
            'tot' => '220',
            'payment' => '225',
            'change' => '5',
            'discount' => '5',
            'paid' => 'Paid',
        ]);
        $mobileSale->created_at = '2026-07-06 21:23:47';
        $mobileSale->updated_at = '2026-07-06 21:23:47';
        $mobileSale->save();

        $debtSale = $this->createSale([
            'order_no' => 'MHH4B3522',
            'pay_mode' => 'Post Payment(Debt)',
            'tot' => '3950',
            'payment' => '2700',
            'change' => '-1250',
            'paid' => 'No',
            'paid_debt' => '2700',
            'del_status' => 'Not Delivered',
        ]);
        $debtSale->created_at = '2026-07-06 21:35:22';
        $debtSale->updated_at = '2026-07-06 21:35:22';
        $debtSale->save();

        $response = $this->withSession(['date_today' => $today])
            ->actingAs($this->branchUser)
            ->get('/sales');

        $response->assertOk();
        $response->assertViewHas('momo', 220.0);
        $response->assertViewHas('collected_debt', 2700.0);
        $response->assertViewHas('gross_collected', 2920.0);
        $response->assertViewHas('net_total', 2920.0);
        $response->assertViewHas('sum_ex_dbt', 220.0);
    }
}
