<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ClosurePageTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $branchUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedClosureBaseline();
        $this->admin = $this->createUser([
            'name' => 'closure.admin',
            'email' => 'closure-admin@test.example',
            'status' => 'Administrator',
        ]);
        $this->branchUser = $this->createUser([
            'name' => 'closure.branch',
            'email' => 'closure-branch@test.example',
            'status' => 'Branch A',
        ]);
    }

    protected function seedClosureBaseline(): void
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
            'id' => 1,
            'user_id' => '1',
            'name' => 'Branch A',
            'loc' => 'Loc 1',
            'contact' => '0000000001',
            'tag' => '1',
            'del' => 'no',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    protected function createUser(array $overrides = []): User
    {
        $data = array_merge([
            'company_branch_id' => '1',
            'name' => 'closure.user',
            'email' => 'closure@test.example',
            'bv' => '1',
            'status' => 'Branch A',
            'password' => Hash::make('password'),
            'del' => 'no',
        ], $overrides);

        $id = DB::table('users')->insertGetId(array_merge($data, [
            'created_at' => now(),
            'updated_at' => now(),
        ]));

        return User::findOrFail($id);
    }

    public function test_closure_list_page_renders_status_tiles_for_admin(): void
    {
        $month = date('Y-m-01');

        DB::table('closures')->insert([
            'user_id' => (string) $this->admin->id,
            'month' => $month,
            'status' => 'open',
            'amt_sold' => '0',
            'profits' => '0',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->actingAs($this->admin)->get('/closure_page');

        $response->assertOk();
        $response->assertSee('Month-end closure');
        $response->assertSee('Open');
        $response->assertSee('Not opened');
        $response->assertSee('Current month');
        $response->assertSee('/maindir/css/dash-closure.css', false);
        $response->assertSee(date('F, Y', strtotime($month)));
    }

    public function test_closure_list_shows_closed_snapshot_totals(): void
    {
        $month = date('Y-m-01');

        DB::table('closures')->insert([
            'user_id' => (string) $this->admin->id,
            'month' => $month,
            'status' => 'closed',
            'amt_sold' => '1250.50',
            'profits' => '320.25',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->actingAs($this->admin)->get('/closure_page');

        $response->assertOk();
        $response->assertSee('Closed');
        $response->assertSee('Sold Gh₵ 1,250.50');
        $response->assertSee('Profit Gh₵ 320.25');
    }

    public function test_closure_detail_renders_inside_dash_shell(): void
    {
        $slug = '01-'.date('m').'-'.date('Y');

        $response = $this->actingAs($this->admin)->get('/closure/'.$slug);

        $response->assertOk();
        $response->assertSee(date('F, Y'));
        $response->assertSee('Not opened');
        $response->assertSee('Open month');
        $response->assertSee('All months');
        $response->assertSee('Items summary');
        $response->assertSee('/maindir/css/dash-closure.css', false);
        $response->assertDontSee('invoiceContent', false);
    }

    public function test_closure_detail_shows_close_action_when_open(): void
    {
        $month = date('Y-m-01');
        $slug = '01-'.date('m').'-'.date('Y');

        DB::table('closures')->insert([
            'user_id' => (string) $this->admin->id,
            'month' => $month,
            'status' => 'open',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->actingAs($this->admin)->get('/closure/'.$slug);

        $response->assertOk();
        $response->assertSee('Close month');
        $response->assertSee('This month is open for sales and expenses.');
        $response->assertSee('Close '.date('F, Y').'?', false);
    }

    public function test_closure_detail_shows_closed_state_without_actions(): void
    {
        $month = date('Y-m-01');
        $slug = '01-'.date('m').'-'.date('Y');

        DB::table('closures')->insert([
            'user_id' => (string) $this->admin->id,
            'month' => $month,
            'status' => 'closed',
            'amt_sold' => '100',
            'profits' => '20',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->actingAs($this->admin)->get('/closure/'.$slug);

        $response->assertOk();
        $response->assertSee('This month has been closed.');
        $response->assertSee('No further actions');
        $response->assertDontSee('Open month');
        $response->assertDontSee('>Close month<', false);
    }

    public function test_non_admin_is_redirected_from_closure_pages(): void
    {
        $slug = '01-'.date('m').'-'.date('Y');

        $this->actingAs($this->branchUser)
            ->get('/closure_page')
            ->assertRedirect('/dashboard');

        $this->actingAs($this->branchUser)
            ->get('/closure/'.$slug)
            ->assertRedirect('/dashboard');
    }

    public function test_closure_detail_shows_multi_branch_sales_and_distribution(): void
    {
        DB::table('company_branches')->insert([
            'id' => 2,
            'user_id' => '1',
            'name' => 'Branch B',
            'loc' => 'Loc 2',
            'contact' => '0000000002',
            'tag' => '2',
            'del' => 'no',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $itemId = DB::table('items')->insertGetId([
            'item_no' => 'ITM-CL',
            'user_id' => (string) $this->admin->id,
            'name' => 'Closure Tile',
            'desc' => 'Blue',
            'cat' => 'Tiles',
            'brand' => 'Brand',
            'barcode' => 'BCCL',
            'qty' => '40',
            'price' => '20',
            'cost_price' => '10',
            'q1' => '25',
            'q2' => '15',
            'q3' => '0',
            'del' => 'no',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $monthStart = date('Y-m-01 10:00:00');

        DB::table('sales_histories')->insert([
            [
                'user_id' => (string) $this->admin->id,
                'sale_id' => '101',
                'item_id' => (string) $itemId,
                'user_bv' => '1',
                'item_no' => 'ITM-CL',
                'name' => 'Closure Tile',
                'qty' => '2',
                'cost_price' => '10',
                'unit_price' => '20',
                'profits' => '20',
                'tot' => '40',
                'del_status' => '1',
                'del' => 'no',
                'created_at' => $monthStart,
                'updated_at' => $monthStart,
            ],
            [
                'user_id' => (string) $this->admin->id,
                'sale_id' => '102',
                'item_id' => (string) $itemId,
                'user_bv' => '2',
                'item_no' => 'ITM-CL',
                'name' => 'Closure Tile',
                'qty' => '5',
                'cost_price' => '10',
                'unit_price' => '20',
                'profits' => '50',
                'tot' => '100',
                'del_status' => '1',
                'del' => 'no',
                'created_at' => $monthStart,
                'updated_at' => $monthStart,
            ],
        ]);

        DB::table('wbdistributions')->insert([
            'user_id' => (string) $this->admin->id,
            'waybill_id' => '9',
            'item_id' => (string) $itemId,
            'q1' => '8',
            'q2' => '6',
            'q3' => '0',
            'del' => 'no',
            'created_at' => $monthStart,
            'updated_at' => $monthStart,
        ]);

        $slug = '01-'.date('m').'-'.date('Y');
        $response = $this->actingAs($this->admin)->get('/closure/'.$slug);

        $response->assertOk();
        $response->assertSee('Branch A');
        $response->assertSee('Branch B');
        $response->assertSee('Closure Tile');
        $response->assertSee('Month-end summary across all branches.');
        $response->assertSee('Qty 2');
        $response->assertSee('Qty 5');
        $response->assertSee('Total distribution');
    }
}
