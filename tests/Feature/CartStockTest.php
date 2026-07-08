<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CartStockTest extends TestCase
{
    use RefreshDatabase;

    protected User $branchUser;

    protected function setUp(): void
    {
        parent::setUp();

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

        $this->branchUser = User::findOrFail(DB::table('users')->insertGetId([
            'company_branch_id' => '1',
            'name' => 'branch.one',
            'email' => 'branch1@test.example',
            'bv' => '1',
            'status' => 'Branch A',
            'password' => Hash::make('password'),
            'del' => 'no',
            'created_at' => now(),
            'updated_at' => now(),
        ]));
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

    protected function cartPayload(Item $item, int $qty = 10): array
    {
        return [
            'item_id' => $item->id,
            'item_no' => $item->item_no,
            'name' => $item->name,
            'qty' => $qty,
            'price' => 35.00,
        ];
    }

    public function test_add_to_cart_reserves_branch_and_main_qty(): void
    {
        $item = $this->createItem();

        $response = $this->actingAs($this->branchUser)->post('/sales/cart', $this->cartPayload($item, 10));

        $response->assertRedirect('/sales');
        $this->assertSame(1, Cart::count());

        $item->refresh();
        $this->assertSame(90, (int) $item->qty);
        $this->assertSame(90, (int) $item->q1);
    }

    public function test_add_to_cart_rejects_insufficient_stock(): void
    {
        $item = $this->createItem(['q1' => '5', 'qty' => '5']);

        $response = $this->actingAs($this->branchUser)->post('/sales/cart', $this->cartPayload($item, 10));

        $response->assertRedirect('/sales');
        $response->assertSessionHas('error');
        $this->assertSame(0, Cart::count());

        $item->refresh();
        $this->assertSame(5, (int) $item->qty);
        $this->assertSame(5, (int) $item->q1);
    }

    public function test_checkout_keeps_reserved_stock_decremented(): void
    {
        $item = $this->createItem();

        $this->actingAs($this->branchUser)->post('/sales/cart', $this->cartPayload($item, 10));

        $response = $this->actingAs($this->branchUser)->post('/sales/checkout', [
            'pay_mode' => 'Cash',
            'del_status' => 'Delivered',
            'buy_name' => 'Jane Buyer',
            'buy_contact' => '0244000000',
            'discount' => 0,
            'payment' => 350,
        ]);

        $response->assertRedirect('/sales');
        $response->assertSessionHas('success');
        $this->assertSame(0, Cart::count());

        $item->refresh();
        $this->assertSame(90, (int) $item->qty);
        $this->assertSame(90, (int) $item->q1);
    }

    public function test_cart_delete_restores_main_and_branch_qty(): void
    {
        $item = $this->createItem();
        $item->qty = 90;
        $item->q1 = 90;
        $item->save();

        $cartId = DB::table('carts')->insertGetId([
            'user_id' => (string) $this->branchUser->id,
            'item_id' => (string) $item->id,
            'item_no' => $item->item_no,
            'name' => $item->name,
            'qty' => '10',
            'cost_price' => '35.00',
            'unit_price' => '35.00',
            'profits' => '0',
            'tot' => '350.00',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->actingAs($this->branchUser)->delete('/sales/cart/' . $cartId);

        $response->assertRedirect();
        $this->assertNull(Cart::find($cartId));

        $item->refresh();
        $this->assertSame(100, (int) $item->qty);
        $this->assertSame(100, (int) $item->q1);
    }

    public function test_repair_inventory_qty_command_fixes_negative_qty(): void
    {
        $item = $this->createItem([
            'qty' => '-575',
            'q1' => '20',
            'q2' => '0',
            'q3' => '0',
        ]);

        Artisan::call('inventory:repair-qty');

        $item->refresh();
        $this->assertSame(20, (int) $item->qty);
    }

    public function test_repair_inventory_qty_command_dry_run_does_not_save(): void
    {
        $item = $this->createItem([
            'qty' => '-10',
            'q1' => '5',
            'q2' => '0',
            'q3' => '0',
        ]);

        Artisan::call('inventory:repair-qty', ['--dry-run' => true]);

        $item->refresh();
        $this->assertSame(-10, (int) $item->qty);
    }
}
