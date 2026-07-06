<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\Item;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SalesCheckoutTest extends TestCase
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

    protected function seedCart(Item $item, int $qty = 2): Cart
    {
        $id = DB::table('carts')->insertGetId([
            'user_id' => (string) $this->branchUser->id,
            'item_id' => (string) $item->id,
            'item_no' => $item->item_no,
            'name' => $item->name,
            'qty' => (string) $qty,
            'cost_price' => '35.00',
            'unit_price' => '35.00',
            'profits' => '0',
            'tot' => (string) (35 * $qty),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return Cart::findOrFail($id);
    }

    public function test_checkout_persists_optional_notes(): void
    {
        $item = $this->createItem();
        $this->seedCart($item);

        $response = $this->actingAs($this->branchUser)->post('/sales/checkout', [
            'pay_mode' => 'Cash',
            'del_status' => 'Delivered',
            'buy_name' => 'Jane Buyer',
            'buy_contact' => '0244000000',
            'discount' => 0,
            'payment' => 70,
            'notes' => 'Deliver to back gate',
        ]);

        $response->assertRedirect('/sales');
        $response->assertSessionHas('success');

        $sale = Sale::first();
        $this->assertNotNull($sale);
        $this->assertSame('Deliver to back gate', $sale->notes);
        $this->assertSame('Jane Buyer', $sale->buy_name);
        $this->assertSame(0, Cart::count());
    }

    public function test_checkout_validation_rejects_missing_pay_mode(): void
    {
        $item = $this->createItem();
        $this->seedCart($item);

        $response = $this->actingAs($this->branchUser)->post('/sales/checkout', [
            'del_status' => 'Delivered',
            'buy_name' => 'Jane Buyer',
            'buy_contact' => '0244000000',
            'payment' => 70,
        ]);

        $response->assertSessionHasErrors('pay_mode');
        $this->assertSame(0, Sale::count());
    }

    public function test_add_to_cart_requires_item_fields(): void
    {
        $response = $this->actingAs($this->branchUser)->post('/sales/cart', []);

        $response->assertSessionHasErrors(['item_id', 'item_no', 'name', 'qty', 'price']);
    }
}
