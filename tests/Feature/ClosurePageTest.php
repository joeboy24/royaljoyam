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
        $response->assertSee(date('M', strtotime($month)));
        $response->assertSee((string) date('Y', strtotime($month)));
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
        $response->assertSee('Sold');
        $response->assertSee('Gh₵ 1,250.50');
        $response->assertSee('Profit');
        $response->assertSee('Gh₵ 320.25');
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
        $response->assertSee('Print');
        $response->assertSee(route('closure.print', ['month' => $slug]), false);
        $response->assertSee(route('closure.open', ['month' => $slug]), false);
        $response->assertSee('/maindir/css/dash-closure.css', false);
        $response->assertDontSee('invoiceContent', false);
        $response->assertDontSee('ItemsController@store', false);
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

    public function test_non_admin_cannot_open_or_close_month(): void
    {
        $slug = '01-'.date('m').'-'.date('Y');

        DB::table('closures')->insert([
            'user_id' => (string) $this->admin->id,
            'month' => date('Y-m-01'),
            'status' => 'open',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($this->branchUser)
            ->post(route('closure.open', ['month' => $slug]))
            ->assertRedirect('/dashboard');

        $this->actingAs($this->branchUser)
            ->post(route('closure.close', ['month' => $slug]))
            ->assertRedirect('/dashboard');

        $this->assertDatabaseHas('closures', [
            'month' => date('Y-m-01'),
            'status' => 'open',
        ]);
    }

    public function test_invalid_month_slug_redirects_to_closure_list(): void
    {
        $this->actingAs($this->admin)
            ->get('/closure/not-a-date')
            ->assertRedirect('/closure_page')
            ->assertSessionHas('error');
    }

    public function test_open_past_month_via_http_is_blocked(): void
    {
        $past = strtotime('-1 month');
        $slug = '01-'.date('m', $past).'-'.date('Y', $past);

        $response = $this->from('/closure/'.$slug)
            ->actingAs($this->admin)
            ->post(route('closure.open', ['month' => $slug]));

        $response->assertRedirect('/closure/'.$slug);
        $response->assertSessionHas('error');
        $this->assertStringContainsString('past month', session('error'));
        $this->assertDatabaseMissing('closures', [
            'month' => date('Y-m-01', $past),
            'status' => 'open',
        ]);
    }

    public function test_admin_can_bootstrap_open_current_month(): void
    {
        $slug = '01-'.date('m').'-'.date('Y');

        $response = $this->from('/closure/'.$slug)
            ->actingAs($this->admin)
            ->post(route('closure.open', ['month' => $slug]));

        $response->assertRedirect('/closure/'.$slug);
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('closures', [
            'month' => date('Y-m-01'),
            'status' => 'open',
        ]);
    }

    public function test_admin_can_close_open_month_via_dedicated_route(): void
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

        $response = $this->from('/closure/'.$slug)
            ->actingAs($this->admin)
            ->post(route('closure.close', ['month' => $slug]));

        $response->assertRedirect('/closure/'.$slug);
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('closures', [
            'month' => $month,
            'status' => 'closed',
        ]);
    }

    public function test_open_is_blocked_when_previous_month_is_still_open(): void
    {
        $slug = '01-'.date('m').'-'.date('Y');
        $previous = date('Y-m-01', strtotime('-1 month'));
        $previousLabel = date('F, Y', strtotime($previous));

        DB::table('closures')->insert([
            'user_id' => (string) $this->admin->id,
            'month' => $previous,
            'status' => 'open',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($this->admin)
            ->get('/closure/'.$slug)
            ->assertOk()
            ->assertSee('Open blocked')
            ->assertSee('Close '.$previousLabel)
            ->assertDontSee('Open month');

        $response = $this->from('/closure/'.$slug)
            ->actingAs($this->admin)
            ->post(route('closure.open', ['month' => $slug]));

        $response->assertRedirect('/closure/'.$slug);
        $response->assertSessionHas('error');
        $this->assertStringContainsString('Close '.$previousLabel, session('error'));
        $this->assertDatabaseMissing('closures', [
            'month' => date('Y-m-01'),
            'status' => 'open',
        ]);
    }

    public function test_branch_user_is_blocked_from_sales_when_month_not_open(): void
    {
        $response = $this->actingAs($this->branchUser)->get('/sales');

        $response->assertRedirect('/dashboard');
        $response->assertSessionHas('error');
        $this->assertStringContainsString(
            'open '.date('F, Y'),
            session('error')
        );
    }

    public function test_branch_user_is_blocked_from_sales_when_month_is_closed(): void
    {
        DB::table('closures')->insert([
            'user_id' => (string) $this->admin->id,
            'month' => date('Y-m-01'),
            'status' => 'closed',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->actingAs($this->branchUser)->get('/sales');

        $response->assertRedirect('/dashboard');
        $response->assertSessionHas('error');
        $this->assertStringContainsString('has been closed', session('error'));
    }

    public function test_branch_user_can_access_sales_when_month_is_open(): void
    {
        DB::table('closures')->insert([
            'user_id' => (string) $this->admin->id,
            'month' => date('Y-m-01'),
            'status' => 'open',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->withSession(['date_today' => date('Y-m-d')])
            ->actingAs($this->branchUser)
            ->get('/sales')
            ->assertOk();
    }

    public function test_admin_can_access_sales_even_when_month_not_open(): void
    {
        $this->withSession(['date_today' => date('Y-m-d')])
            ->actingAs($this->admin)
            ->get('/sales')
            ->assertOk();
    }

    public function test_branch_user_is_blocked_from_expenses_when_month_not_open(): void
    {
        $response = $this->actingAs($this->branchUser)->get('/expenses');

        $response->assertRedirect('/dashboard');
        $response->assertSessionHas('error');
        $this->assertStringContainsString('open '.date('F, Y'), session('error'));
    }

    public function test_branch_user_is_blocked_from_expenses_when_month_is_closed(): void
    {
        DB::table('closures')->insert([
            'user_id' => (string) $this->admin->id,
            'month' => date('Y-m-01'),
            'status' => 'closed',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->actingAs($this->branchUser)->get('/expenses');

        $response->assertRedirect('/dashboard');
        $response->assertSessionHas('error');
        $this->assertStringContainsString('has been closed', session('error'));
    }

    public function test_closure_list_supports_year_navigation(): void
    {
        $year = (int) date('Y');
        $prevYear = $year - 1;

        DB::table('closures')->insert([
            'user_id' => (string) $this->admin->id,
            'month' => $prevYear.'-03-01',
            'status' => 'closed',
            'amt_sold' => '500',
            'profits' => '100',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->actingAs($this->admin)->get('/closure_page?year='.$prevYear);

        $response->assertOk();
        $response->assertSee('Browse and manage monthly periods for '.$prevYear);
        $response->assertSee('Mar');
        $response->assertSee((string) $prevYear);
        $response->assertSee('Closed');
        $response->assertSee('Daily closes');
        $response->assertSee((string) $year, false);
    }

    public function test_closure_list_daily_closes_include_branch_breakdown(): void
    {
        $date = date('Y-m-d', strtotime('-1 day'));

        DB::table('sales')->insert([
            'user_id' => (string) $this->branchUser->id,
            'user_bv' => '1',
            'order_no' => 'EOD-LIST-1',
            'qty' => '1',
            'tot' => '75',
            'pay_mode' => 'Cash',
            'buy_name' => 'Buyer',
            'buy_contact' => '000',
            'del_status' => 'Delivered',
            'discount' => '0',
            'payment' => '75',
            'change' => '0',
            'paid' => 'Paid',
            'del' => 'no',
            'created_at' => $date.' 10:00:00',
            'updated_at' => $date.' 10:00:00',
        ]);

        DB::table('daily_closures')->insert([
            'user_id' => (string) $this->admin->id,
            'scope_key' => 'admin',
            'branch_label' => 'All branches',
            'close_date' => $date,
            'cash' => '75',
            'cheque' => '0',
            'momo' => '0',
            'debt_sold' => '0',
            'collected_debt' => '0',
            'expenses' => '0',
            'gross_collected' => '75',
            'net_total' => '75',
            'counted_cash' => '75',
            'variance' => '0',
            'notes' => null,
            'status' => 'closed',
            'del' => 'no',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($this->admin)
            ->get('/closure_page')
            ->assertOk()
            ->assertSee('data-daily-close-toggle', false)
            ->assertSee('Branch breakdown')
            ->assertSee('Branch A')
            ->assertSee('75.00')
            ->assertSee('/maindir/js/dash-closure.js', false);
    }

    public function test_closure_print_and_export_routes_work_for_admin(): void
    {
        $slug = '01-'.date('m').'-'.date('Y');

        $this->actingAs($this->admin)
            ->get(route('closure.print', ['month' => $slug]))
            ->assertOk()
            ->assertSee('Month-end closure')
            ->assertSee(date('F, Y'));

        $export = $this->actingAs($this->admin)
            ->get(route('closure.export', ['month' => $slug]));

        $export->assertOk();
        $export->assertHeader('content-disposition');
        $this->assertStringContainsString('Month', $export->streamedContent());
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
        $response->assertSee('By branch');
        $response->assertSee('Profit');
        $response->assertSee('Total distribution');
    }
}
