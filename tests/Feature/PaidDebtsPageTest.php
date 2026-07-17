<?php

namespace Tests\Feature;

use App\Models\Sale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PaidDebtsPageTest extends TestCase
{
    use RefreshDatabase;

    protected User $branchUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedBaseline();
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
        return Sale::create(array_merge([
            'user_id' => (string) $this->branchUser->id,
            'user_bv' => '1',
            'order_no' => 'ORD' . uniqid(),
            'qty' => '1',
            'tot' => '100',
            'pay_mode' => 'Post Payment(Debt)',
            'buy_name' => 'Debt Buyer',
            'buy_contact' => '0244000000',
            'del_status' => 'Delivered',
            'discount' => '0',
            'payment' => '40',
            'change' => '0',
            'paid' => 'No',
            'paid_debt' => '40',
            'del' => 'no',
        ], $overrides));
    }

    public function test_paid_debts_page_renders_modern_layout_for_branch_user(): void
    {
        $today = now()->format('Y-m-d');
        $sale = $this->createSale(['order_no' => 'MDEBTPAID001']);

        DB::table('sales_payments')->insert([
            'user_id' => (string) $this->branchUser->id,
            'sale_id' => (string) $sale->id,
            'amt_paid' => '40',
            'bal' => '60',
            'del' => 'no',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->withSession(['date_today' => $today])
            ->actingAs($this->branchUser)
            ->get('/paid_debts');

        $response->assertOk()
            ->assertSee('dash-paid-debts-card', false)
            ->assertSee('MDEBTPAID001')
            ->assertSee('Debt Buyer')
            ->assertSee('Total collected')
            ->assertSee('data-collapsible-filters', false)
            ->assertSee('paiddebtsearch', false)
            ->assertSee('date_from', false)
            ->assertSee('Back to sales', false)
            ->assertDontSee('href="/debts"', false);
    }

    public function test_branch_user_sees_all_payments_for_their_branch(): void
    {
        $today = now()->format('Y-m-d');
        $otherBranchUserId = DB::table('users')->insertGetId([
            'company_branch_id' => '1',
            'name' => 'branch.two',
            'email' => 'branch2@test.example',
            'bv' => '1',
            'status' => 'Branch A',
            'password' => Hash::make('password'),
            'del' => 'no',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $ownSale = $this->createSale(['order_no' => 'OWN-BRANCH-001']);
        $otherUserSale = $this->createSale([
            'order_no' => 'OTHER-USER-002',
            'user_id' => (string) $otherBranchUserId,
        ]);

        foreach ([$ownSale, $otherUserSale] as $sale) {
            DB::table('sales_payments')->insert([
                'user_id' => (string) $sale->user_id,
                'sale_id' => (string) $sale->id,
                'amt_paid' => '30',
                'bal' => '0',
                'del' => 'no',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->withSession(['date_today' => $today])
            ->actingAs($this->branchUser)
            ->get('/paid_debts')
            ->assertOk()
            ->assertSee('OWN-BRANCH-001')
            ->assertSee('OTHER-USER-002')
            ->assertSee('Branch A', false);
    }

    public function test_branch_user_branch_filter_cannot_be_overridden_by_query(): void
    {
        $today = now()->format('Y-m-d');
        $sale = $this->createSale(['order_no' => 'BRANCH-LOCK-001', 'user_bv' => '1']);

        DB::table('sales_payments')->insert([
            'user_id' => (string) $this->branchUser->id,
            'sale_id' => (string) $sale->id,
            'amt_paid' => '30',
            'bal' => '0',
            'del' => 'no',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $branchTwoSale = $this->createSale(['order_no' => 'BRANCH2-002', 'user_bv' => '2']);
        DB::table('sales_payments')->insert([
            'user_id' => (string) $this->branchUser->id,
            'sale_id' => (string) $branchTwoSale->id,
            'amt_paid' => '40',
            'bal' => '0',
            'del' => 'no',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('company_branches')->insert([
            'user_id' => '1',
            'name' => 'Branch B',
            'loc' => 'Loc 2',
            'contact' => '0000000002',
            'tag' => '2',
            'del' => 'no',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->withSession(['date_today' => $today])
            ->actingAs($this->branchUser)
            ->get('/paid_debts?branch=2')
            ->assertOk()
            ->assertSee('BRANCH-LOCK-001')
            ->assertDontSee('BRANCH2-002');
    }

    public function test_branch_user_can_filter_paid_debts_by_date_range(): void
    {
        $today = now()->format('Y-m-d');
        $yesterday = now()->subDay()->format('Y-m-d');
        $todaySale = $this->createSale(['order_no' => 'TODAY-PAID-001']);
        $yesterdaySale = $this->createSale(['order_no' => 'YDAY-PAID-002']);

        DB::table('sales_payments')->insert([
            'user_id' => (string) $this->branchUser->id,
            'sale_id' => (string) $todaySale->id,
            'amt_paid' => '20',
            'bal' => '0',
            'del' => 'no',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('sales_payments')->insert([
            'user_id' => (string) $this->branchUser->id,
            'sale_id' => (string) $yesterdaySale->id,
            'amt_paid' => '15',
            'bal' => '0',
            'del' => 'no',
            'created_at' => now()->subDay(),
            'updated_at' => now()->subDay(),
        ]);

        $this->withSession(['date_today' => $today])
            ->actingAs($this->branchUser)
            ->get('/paid_debts?date_from='.$yesterday.'&date_to='.$yesterday)
            ->assertOk()
            ->assertSee('YDAY-PAID-002')
            ->assertDontSee('TODAY-PAID-001');
    }

    public function test_paid_debts_date_to_without_date_from_redirects_with_error(): void
    {
        $this->withSession(['date_today' => now()->format('Y-m-d')])
            ->actingAs($this->branchUser)
            ->from('/paid_debts')
            ->get('/paid_debts?date_to='.now()->format('Y-m-d'))
            ->assertRedirect('/paid_debts')
            ->assertSessionHas('error');
    }

    public function test_paid_debts_search_filters_by_order_buyer_or_contact(): void
    {
        $today = now()->format('Y-m-d');
        $matchingSale = $this->createSale([
            'order_no' => 'MATCH-PAID-001',
            'buy_name' => 'Alpha Buyer',
            'buy_contact' => '0244111222',
        ]);
        $otherSale = $this->createSale([
            'order_no' => 'OTHER-PAID-002',
            'buy_name' => 'Beta Buyer',
            'buy_contact' => '0244333444',
        ]);

        foreach ([$matchingSale, $otherSale] as $sale) {
            DB::table('sales_payments')->insert([
                'user_id' => (string) $this->branchUser->id,
                'sale_id' => (string) $sale->id,
                'amt_paid' => '25',
                'bal' => '0',
                'del' => 'no',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->withSession(['date_today' => $today])
            ->actingAs($this->branchUser)
            ->get('/paid_debts?paiddebtsearch=Alpha+Buyer')
            ->assertOk()
            ->assertSee('MATCH-PAID-001')
            ->assertDontSee('OTHER-PAID-002');
    }

    public function test_invoice_print_shows_purchase_notes_when_present(): void
    {
        $sale = $this->createSale([
            'order_no' => 'MNOTEINV001',
            'notes' => 'Deliver to back gate',
            'pay_mode' => 'Cash',
            'paid' => 'Paid',
        ]);

        DB::table('sales_histories')->insert([
            'user_id' => (string) $this->branchUser->id,
            'sale_id' => (string) $sale->id,
            'item_id' => '1',
            'user_bv' => '1',
            'item_no' => 'ITM-001',
            'name' => 'Sample Item',
            'qty' => '1',
            'cost_price' => '10',
            'unit_price' => '100',
            'profits' => '90',
            'tot' => '100',
            'del_status' => 'Delivered',
            'del' => 'no',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->withSession([
            'company' => (object) [
                'address' => 'Test Address',
                'contact' => '0244000000',
                'email' => 'test@example.com',
            ],
        ])->actingAs($this->branchUser)
            ->get('/reporting/' . $sale->id);

        $response->assertOk()
            ->assertSee('Notes :')
            ->assertSee('Deliver to back gate');
    }
}
