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
        $response->assertSee('Add item');
        $response->assertSee('href="/items"', false);
        $response->assertSee('active2');
        $response->assertSee('Widget Alpha');
        $response->assertSee('item-row-' . $item->id, false);
        $response->assertSee('branch-detail-' . $item->id, false);
        $response->assertSee('edit_' . $item->id, false);
        $response->assertSee('toggleBranchDetail', false);
        $response->assertSee('Expand all');
        $response->assertSee('toggleAllBranches', false);
        $response->assertSee('toggleAllBranchDetails', false);
        $response->assertSee('restoreBranchDetailState', false);
        $response->assertSee('inventoryExpandedBranchIds', false);
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
        $response->assertSee('value="Alpha"', false);
        $response->assertSee('Alpha Widget');
        $response->assertDontSee('Beta Gadget');
        $response->assertSeeText('1 match');
        $response->assertSeeText('2 total items');
    }

    public function test_inventory_search_with_no_matches_shows_zero_match_summary(): void
    {
        $this->createItem(['name' => 'Alpha Widget']);

        $response = $this->actingAs($this->admin)->get('/items?itemsearch=Missing');

        $response->assertOk();
        $response->assertSee('value="Missing"', false);
        $response->assertSee('No matches for');
        $response->assertSee('1 total items');
    }

    public function test_inventory_list_shows_total_item_count_without_search(): void
    {
        $this->createItem(['name' => 'One']);
        $this->createItem(['name' => 'Two']);

        $response = $this->actingAs($this->admin)->get('/items');

        $response->assertOk();
        $response->assertSeeText('Showing 1-2 of 2 items');
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

    public function test_inventory_table_does_not_show_thumbnail_filename(): void
    {
        $this->createItem([
            'name' => 'Visible Item',
            'thumb_img' => 'hidden_thumb_xyz.png',
        ]);

        $response = $this->actingAs($this->admin)->get('/items');

        $response->assertOk();
        $response->assertSee('Visible Item');
        $response->assertDontSee('<p class="small_p">hidden_thumb_xyz.png</p>', false);
    }

    public function test_inventory_table_shows_formatted_created_date(): void
    {
        $item = $this->createItem(['name' => 'Dated Item']);
        DB::table('items')->where('id', $item->id)->update([
            'created_at' => '2024-03-15 10:30:00',
        ]);

        $response = $this->actingAs($this->admin)->get('/items');

        $response->assertOk();
        $response->assertSee('15 Mar 2024');
    }

    public function test_recycle_bin_shows_deleted_items(): void
    {
        $this->createItem(['name' => 'Active Item', 'del' => 'no']);
        $this->createItem(['name' => 'Trashed Item', 'del' => 'yes']);

        $response = $this->actingAs($this->admin)->get('/items?recycle=1');

        $response->assertOk();
        $response->assertSee('Recycle Bin');
        $response->assertSee('href="' . url('/items') . '"', false);
        $response->assertSee('data-tip="Back to inventory"', false);
        $response->assertSee('Trashed Item');
        $response->assertDontSee('id="toggleAllBranches"', false);
        $response->assertDontSee('Active Item');
        $response->assertSee('restore_item', false);
    }

    public function test_admin_can_restore_item_from_recycle_bin(): void
    {
        $item = $this->createItem(['name' => 'Restore Me', 'del' => 'yes']);

        $response = $this->actingAs($this->admin)->put('/items/' . $item->id, [
            'store_action' => 'restore_item',
        ]);

        $response->assertRedirect('/items?recycle=1');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'del' => 'no',
        ]);
    }

    public function test_inventory_shows_stock_status_badges(): void
    {
        $this->createItem(['name' => 'Full Stock', 'qty' => '10']);
        $this->createItem(['name' => 'Low Stock', 'qty' => '3']);
        $this->createItem(['name' => 'Empty Stock', 'qty' => '0']);

        $response = $this->actingAs($this->admin)->get('/items');

        $response->assertOk();
        $response->assertSee('stock-badge-legend', false);
        $response->assertSee('In stock');
        $response->assertSee('Low stock');
        $response->assertSee('Out of stock');
        $response->assertSee('stock-badge-ok', false);
        $response->assertSee('stock-badge-low', false);
        $response->assertSee('stock-badge-out', false);
    }

    public function test_recycle_bin_does_not_show_stock_badge_legend(): void
    {
        $this->createItem(['name' => 'Trashed Item', 'del' => 'yes', 'qty' => '0']);

        $response = $this->actingAs($this->admin)->get('/items?recycle=1');

        $response->assertOk();
        $response->assertDontSee('<p class="stock-badge-legend', false);
        $response->assertSee('Out of stock');
    }

    public function test_inventory_filters_by_category(): void
    {
        $this->createItem(['name' => 'Soap Item', 'cat' => 'General']);
        $this->createItem(['name' => 'Phone Item', 'cat' => 'Electronics']);

        $response = $this->actingAs($this->admin)->get('/items?category=Electronics');

        $response->assertOk();
        $response->assertSee('Phone Item');
        $response->assertDontSee('Soap Item');
        $response->assertSee('value="Electronics"', false);
    }

    public function test_inventory_low_stock_filter_shows_only_low_and_out_items(): void
    {
        $this->createItem(['name' => 'Healthy Stock', 'qty' => '20']);
        $this->createItem(['name' => 'Low Stock Item', 'qty' => '2']);
        $this->createItem(['name' => 'Empty Stock', 'qty' => '0']);

        $response = $this->actingAs($this->admin)->get('/items?stock=low');

        $response->assertOk();
        $response->assertSee('Low Stock Item');
        $response->assertSee('Empty Stock');
        $response->assertDontSee('Healthy Stock');
    }

    public function test_inventory_has_branch_stock_filter(): void
    {
        $this->createItem(['name' => 'Branch Stock', 'q1' => '2', 'q2' => '0', 'q3' => '0']);
        $this->createItem(['name' => 'No Branch Stock', 'q1' => '0', 'q2' => '0', 'q3' => '0']);

        $response = $this->actingAs($this->admin)->get('/items?stock=has_branch');

        $response->assertOk();
        $response->assertSee('Branch Stock');
        $response->assertDontSee('No Branch Stock');
    }

    public function test_inventory_per_page_setting_shows_more_rows(): void
    {
        for ($i = 0; $i < 11; $i++) {
            $this->createItem(['name' => 'Sized Item ' . $i]);
        }

        $response = $this->actingAs($this->admin)->get('/items?per_page=25');

        $response->assertOk();
        $response->assertSee('Sized Item 0');
        $response->assertSee('Sized Item 10');
        $response->assertDontSee('href="http://localhost/items?page=2', false);
    }

    public function test_inventory_filters_persist_in_pagination_links(): void
    {
        DB::table('categories')->insert([
            'user_id' => '1',
            'name' => 'Filtered Cat',
            'desc' => 'Filter test category',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        for ($i = 0; $i < 11; $i++) {
            $this->createItem(['name' => 'Filtered Item ' . $i, 'cat' => 'Filtered Cat']);
        }

        $response = $this->actingAs($this->admin)->get('/items?category=Filtered+Cat&per_page=10');

        $response->assertOk();
        $response->assertSee('category=Filtered', false);
        $response->assertSee('page=2', false);
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

    public function test_admin_can_export_inventory_csv(): void
    {
        $this->createItem(['name' => 'CSV Alpha', 'cat' => 'General']);
        $this->createItem(['name' => 'CSV Beta', 'cat' => 'General']);

        $response = $this->actingAs($this->admin)->get('/items/export?itemsearch=Alpha');

        $response->assertOk();
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $content = $response->streamedContent();
        $this->assertStringContainsString('CSV Alpha', $content);
        $this->assertStringNotContainsString('CSV Beta', $content);
        $this->assertStringContainsString('Stock Status', $content);
    }

    public function test_admin_can_open_inventory_print_view(): void
    {
        $this->createItem(['name' => 'Printable Widget', 'cat' => 'General']);

        $response = $this->actingAs($this->admin)->get('/items/print?category=General');

        $response->assertOk();
        $response->assertSee('Inventory Report');
        $response->assertSee('Printable Widget');
        $response->assertSee('General');
    }

    public function test_inventory_update_rejects_branch_qty_above_general_qty(): void
    {
        $item = $this->createItem(['name' => 'Qty Check Item']);

        $response = $this->actingAs($this->admin)->put('/items/' . $item->id, [
            'store_action' => 'update_item',
            'name' => 'Qty Check Item',
            'desc' => 'Updated description',
            'cat' => 'General',
            'brand' => 'Brand',
            'barcode' => 'BC999',
            'qty' => 5,
            'price' => '30.00',
            'q1' => 4,
            'q2' => 4,
            'q3' => 0,
            'b1' => '30.00',
            'b2' => '31.00',
            'b3' => '32.00',
        ]);

        $response->assertRedirect('/items');
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'qty' => '10',
        ]);
    }

    public function test_inventory_update_rejects_negative_base_price(): void
    {
        $item = $this->createItem(['name' => 'Price Check Item']);

        $response = $this->actingAs($this->admin)->put('/items/' . $item->id, [
            'store_action' => 'update_item',
            'name' => 'Price Check Item',
            'desc' => 'Updated description',
            'cat' => 'General',
            'brand' => 'Brand',
            'barcode' => 'BC999',
            'qty' => 10,
            'price' => '-5',
            'q1' => 4,
            'q2' => 3,
            'q3' => 3,
            'b1' => '30.00',
            'b2' => '31.00',
            'b3' => '32.00',
        ]);

        $response->assertRedirect('/items');
        $response->assertSessionHas('error');
    }

    public function test_inventory_edit_modal_shows_polished_labels(): void
    {
        $item = $this->createItem(['name' => 'Label Check Item']);

        $response = $this->actingAs($this->admin)->get('/items');

        $response->assertOk();
        $response->assertSee('Base price (Gh', false);
        $response->assertSee('Branch A qty', false);
        $response->assertSee('Branch A price (Gh', false);
        $response->assertSee('id="branch_status_' . $item->id . '"', false);
        $response->assertSee('inventory-edit-submit', false);
    }

    public function test_inventory_page_shows_print_and_export_actions(): void
    {
        $response = $this->actingAs($this->admin)->get('/items');

        $response->assertOk();
        $response->assertSee('/items/print?', false);
        $response->assertSee('/items/export?', false);
        $response->assertSee('data-tip="Print list"', false);
        $response->assertSee('data-tip="Export CSV"', false);
    }
}
