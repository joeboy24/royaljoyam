<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class InventoryPageTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedInventoryBaseline();
        $this->admin = $this->createAdministrator();
    }

    protected function seedInventoryBaseline(): void
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

        foreach ([1 => 'Branch A', 2 => 'Branch B', 3 => 'Branch C'] as $tag => $name) {
            DB::table('company_branches')->insert([
                'user_id' => '1',
                'name' => $name,
                'loc' => 'Loc ' . $tag,
                'contact' => '000000000' . $tag,
                'tag' => (string) $tag,
                'del' => 'no',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        DB::table('categories')->insert([
            'user_id' => '1',
            'name' => 'General',
            'desc' => 'General category',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    protected function createAdministrator(array $overrides = []): User
    {
        $data = array_merge([
            'company_branch_id' => '1',
            'name' => 'admin.test',
            'email' => 'admin@test.example',
            'bv' => 'A',
            'status' => 'Administrator',
            'password' => Hash::make('password'),
            'del' => 'no',
        ], $overrides);

        $id = DB::table('users')->insertGetId(array_merge($data, [
            'created_at' => now(),
            'updated_at' => now(),
        ]));

        return User::findOrFail($id);
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
        static $counter = 0;
        $counter++;

        $data = array_merge([
            'item_no' => 'ITM-' . str_pad((string) $counter, 4, '0', STR_PAD_LEFT),
            'user_id' => (string) $this->admin->id,
            'name' => 'Test Item ' . $counter,
            'desc' => 'Test description',
            'cat' => 'General',
            'brand' => 'Brand',
            'barcode' => 'BC' . $counter,
            'qty' => '10',
            'price' => '25.50',
            'cost_price' => '25.50',
            'q1' => '4',
            'q2' => '3',
            'q3' => '3',
            'b1' => '25.50',
            'b2' => '26.00',
            'b3' => '27.00',
            'del' => 'no',
        ], $overrides);

        $id = DB::table('items')->insertGetId(array_merge($data, [
            'created_at' => now(),
            'updated_at' => now(),
        ]));

        return Item::findOrFail($id);
    }

    public function test_guest_is_redirected_from_inventory_page(): void
    {
        $response = $this->get('/items');

        $response->assertRedirect('/login');
    }

    public function test_non_admin_is_redirected_from_inventory_page(): void
    {
        $branchUser = $this->createBranchUser('branch.user', 'branch@test.example', 'Branch A', '1');

        $response = $this->actingAs($branchUser)->get('/items');

        $response->assertRedirect('/dashboard');
    }

    public function test_admin_can_view_inventory_page(): void
    {
        $item = $this->createItem(['name' => 'Widget Alpha']);

        $response = $this->actingAs($this->admin)->get('/items');

        $response->assertOk();
        $response->assertSee('Inventory');
        $response->assertSee('addItemModal', false);
        $response->assertSee('Add Item');
        $response->assertSee('href="/items"', false);
        $response->assertSee('active2');
        $response->assertSee('Widget Alpha');
        $response->assertSee('item-row-' . $item->id, false);
        $response->assertSee('branch-detail-' . $item->id, false);
        $response->assertSee('edit_' . $item->id, false);
        $response->assertSee('toggleBranchDetail', false);
    }

    public function test_admin_can_add_item_from_inventory_page(): void
    {
        $response = $this->actingAs($this->admin)->post('/items', [
            'store_action' => 'add_item',
            'return_to' => 'items',
            'name' => 'New Inventory Item',
            'desc' => 'Added from inventory page',
            'cat' => 'General',
            'brand' => 'Test Brand',
            'barcode' => 'NEW001',
            'qty' => 5,
            'price' => '19.99',
        ]);

        $response->assertRedirect('/items');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('items', [
            'name' => 'New Inventory Item',
            'qty' => '5',
            'price' => '19.99',
            'del' => 'no',
        ]);
    }

    public function test_add_item_from_inventory_page_rejects_duplicate_names(): void
    {
        $this->createItem(['name' => 'Duplicate Name']);

        $response = $this->actingAs($this->admin)->post('/items', [
            'store_action' => 'add_item',
            'return_to' => 'items',
            'name' => 'Duplicate Name',
            'desc' => 'Another item',
            'cat' => 'General',
            'qty' => 1,
            'price' => '10.00',
        ]);

        $response->assertRedirect('/items');
        $response->assertSessionHas('error');
    }

    public function test_inventory_search_filters_by_item_name(): void
    {
        $this->createItem(['name' => 'Alpha Widget']);
        $this->createItem(['name' => 'Beta Gadget']);

        $response = $this->actingAs($this->admin)->get('/items?itemsearch=Alpha');

        $response->assertOk();
        $response->assertSee('Alpha Widget');
        $response->assertDontSee('Beta Gadget');
    }

    public function test_admin_can_update_item_from_inventory_page(): void
    {
        $item = $this->createItem(['name' => 'Before Update']);

        $response = $this->actingAs($this->admin)->put('/items/' . $item->id, [
            'store_action' => 'update_item',
            'name' => 'After Update',
            'desc' => 'Updated description',
            'cat' => 'General',
            'brand' => 'Brand',
            'barcode' => 'BC999',
            'qty' => 10,
            'price' => '30.00',
            'q1' => 4,
            'q2' => 3,
            'q3' => 3,
            'b1' => '30.00',
            'b2' => '31.00',
            'b3' => '32.00',
        ]);

        $response->assertRedirect('/items');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'name' => 'After Update',
            'qty' => '10',
            'price' => '30.00',
            'del' => 'no',
        ]);
    }

    public function test_admin_can_soft_delete_item_from_inventory_page(): void
    {
        $item = $this->createItem(['name' => 'Delete Me']);

        $response = $this->actingAs($this->admin)->put('/items/' . $item->id, [
            'store_action' => 'del_item',
        ]);

        $response->assertRedirect('/items');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'del' => 'yes',
        ]);
    }

    public function test_inventory_pagination_links_appear_when_more_than_ten_items(): void
    {
        for ($i = 0; $i < 11; $i++) {
            $this->createItem(['name' => 'Paginated Item ' . $i]);
        }

        $response = $this->actingAs($this->admin)->get('/items');

        $response->assertOk();
        $response->assertSee('Paginated Item 10');
        $response->assertDontSee('Paginated Item 0');
        $response->assertSee('page=2', false);

        $pageTwo = $this->actingAs($this->admin)->get('/items?page=2');
        $pageTwo->assertOk();
        $pageTwo->assertSee('Paginated Item 0');
    }
}
