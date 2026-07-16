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
            ->assertSee('Back to sales', false);
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
