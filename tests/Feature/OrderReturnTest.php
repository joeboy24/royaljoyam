<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\OrderReturn;
use App\Models\Sale;
use App\Models\SalesHistory;
use App\Models\SalesPayment;
use App\Models\User;
use App\Services\OrderReturnService;
use App\Services\SalesReportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class OrderReturnTest extends TestCase
{
    use RefreshDatabase;

    protected User $branchUser;

    protected User $admin;

    protected OrderReturnService $returnService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedBaseline();
        $this->branchUser = $this->createBranchUser('1', 'Branch A');
        $this->admin = $this->createAdministrator();
        $this->returnService = app(OrderReturnService::class);
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

    protected function createItem(array $overrides = []): Item
    {
        $id = DB::table('items')->insertGetId(array_merge([
            'item_no' => 'ITM-RET-001',
            'user_id' => (string) $this->branchUser->id,
            'name' => 'Return Tile',
            'desc' => 'Sample',
            'cat' => 'Building',
            'brand' => 'Brand',
            'barcode' => 'BC-RET-1',
            'qty' => '100',
            'price' => '35.00',
            'cost_price' => '25.00',
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

    protected function createSaleWithHistory(Item $item, array $saleOverrides = [], array $historyOverrides = []): Sale
    {
        $sale = Sale::create(array_merge([
            'user_id' => (string) $this->branchUser->id,
            'user_bv' => '1',
            'order_no' => 'RET-ORD-'.uniqid(),
            'qty' => '2',
            'tot' => '70',
            'pay_mode' => Sale::PAY_MODE_CASH,
            'buy_name' => 'Return Buyer',
            'buy_contact' => '0244000000',
            'del_status' => 'Delivered',
            'discount' => '0',
            'payment' => '70',
            'change' => '0',
            'paid' => 'Paid',
            'paid_debt' => '0',
            'del' => 'no',
        ], $saleOverrides));

        SalesHistory::create(array_merge([
            'user_id' => (string) $this->branchUser->id,
            'sale_id' => (string) $sale->id,
            'item_id' => (string) $item->id,
            'user_bv' => '1',
            'item_no' => $item->item_no,
            'name' => $item->name,
            'qty' => '2',
            'cost_price' => '25',
            'unit_price' => '35',
            'profits' => '20',
            'tot' => '70',
            'del_status' => 'Delivered',
            'del' => 'no',
        ], $historyOverrides));

        return $sale;
    }

    public function test_return_sale_restores_branch_and_main_stock(): void
    {
        $item = $this->createItem(['qty' => '98', 'q1' => '98']);
        $sale = $this->createSaleWithHistory($item);

        $this->returnService->returnSale((int) $sale->id);

        $item->refresh();
        $this->assertSame(100, (int) $item->qty);
        $this->assertSame(100, (int) $item->q1);
    }

    public function test_return_sale_archives_line_items_with_correct_delivery_status_and_totals(): void
    {
        $item = $this->createItem();
        $sale = $this->createSaleWithHistory($item, [], [
            'profits' => '20',
            'tot' => '70',
            'del_status' => 'Not Delivered',
        ]);

        $this->returnService->returnSale((int) $sale->id);

        $return = OrderReturn::first();
        $this->assertNotNull($return);
        $this->assertSame('70', $return->tot);
        $this->assertSame('20', $return->profits);
        $this->assertSame('Not Delivered', $return->del_status);
        $this->assertSame('2', $return->qty);
    }

    public function test_return_sale_soft_deletes_sale_history_and_payments(): void
    {
        $item = $this->createItem();
        $sale = $this->createSaleWithHistory($item, [
            'pay_mode' => Sale::PAY_MODE_DEBT,
            'tot' => '100',
            'payment' => '40',
            'paid' => 'No',
            'paid_debt' => '40',
        ]);

        SalesPayment::create([
            'user_id' => (string) $this->branchUser->id,
            'sale_id' => (string) $sale->id,
            'amt_paid' => '40',
            'bal' => '60',
            'del' => 'no',
        ]);

        $this->returnService->returnSale((int) $sale->id);

        $this->assertSame('yes', $sale->fresh()->del);
        $this->assertSame(0, SalesHistory::where('sale_id', $sale->id)->where('del', 'no')->count());
        $this->assertSame(0, SalesPayment::where('sale_id', $sale->id)->where('del', 'no')->count());
    }

    public function test_returned_sale_is_excluded_from_sales_report_totals(): void
    {
        $today = now()->format('Y-m-d');
        $item = $this->createItem();
        $sale = $this->createSaleWithHistory($item, ['tot' => '70']);

        $reportService = app(SalesReportService::class);

        $before = $reportService->build([
            'date_from' => $today,
            'branch' => 'All Branches',
            'delvr' => 'Del. / Not Delivered',
            'session_sales_date' => $today,
        ]);

        $this->assertSame(70.0, $before['cash']);

        $this->returnService->returnSale((int) $sale->id);

        $after = $reportService->build([
            'date_from' => $today,
            'branch' => 'All Branches',
            'delvr' => 'Del. / Not Delivered',
            'session_sales_date' => $today,
        ]);

        $this->assertSame(0.0, $after['cash']);
        $this->assertSame(0.0, $after['gen_profits']);
    }

    public function test_admin_return_route_processes_order(): void
    {
        $item = $this->createItem(['qty' => '98', 'q1' => '98']);
        $sale = $this->createSaleWithHistory($item, ['tot' => '70'], ['qty' => '2', 'tot' => '70']);

        $response = $this->actingAs($this->admin)
            ->from('/reporting')
            ->get('/reporting/'.$sale->id.'/edit');

        $response->assertRedirect('/reporting');
        $response->assertSessionHas('success');

        $item->refresh();
        $this->assertSame(100, (int) $item->q1);
        $this->assertSame('yes', $sale->fresh()->del);
    }

    public function test_non_admin_cannot_return_order(): void
    {
        $item = $this->createItem();
        $sale = $this->createSaleWithHistory($item);

        $this->actingAs($this->branchUser)
            ->get('/reporting/'.$sale->id.'/edit')
            ->assertRedirect('/dashboard');

        $this->assertSame('no', $sale->fresh()->del);
    }
}
