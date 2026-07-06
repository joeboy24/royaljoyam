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
