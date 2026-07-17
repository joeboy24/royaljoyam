<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\DailyCloseService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DailyClosePageTest extends TestCase
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

        DB::table('closures')->insert([
            'user_id' => '1',
            'month' => date('Y-m-01'),
            'status' => 'open',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $id = DB::table('users')->insertGetId([
            'company_branch_id' => '1',
            'name' => 'daily.feature',
            'email' => 'daily-feature@test.example',
            'bv' => '1',
            'status' => 'Branch A',
            'password' => Hash::make('password'),
            'del' => 'no',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->branchUser = User::findOrFail($id);
    }

    public function test_sales_page_shows_close_day_form_when_open(): void
    {
        $this->withSession(['date_today' => date('Y-m-d')])
            ->actingAs($this->branchUser)
            ->get('/sales')
            ->assertOk()
            ->assertSee('data-tip="Close day"', false)
            ->assertSee('data-daily-close-toggle', false)
            ->assertSee('>Close day</span>', false)
            ->assertSee(route('dailyclose.store'), false);
    }

    public function test_branch_user_can_close_day_from_sales(): void
    {
        $date = date('Y-m-d');

        $response = $this->withSession(['date_today' => $date])
            ->actingAs($this->branchUser)
            ->post(route('dailyclose.store'), [
                'counted_cash' => 50,
                'notes' => 'Balanced',
            ]);

        $response->assertRedirect('/sales');
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('daily_closures', [
            'close_date' => $date,
            'scope_key' => 'bv:1',
            'counted_cash' => '50',
            'notes' => 'Balanced',
            'status' => 'closed',
        ]);
    }

    public function test_sales_page_shows_closed_state_and_print_link(): void
    {
        $date = date('Y-m-d');

        DB::table('daily_closures')->insert([
            'user_id' => (string) $this->branchUser->id,
            'scope_key' => 'bv:1',
            'branch_label' => 'Branch A',
            'close_date' => $date,
            'cash' => '10',
            'cheque' => '0',
            'momo' => '0',
            'debt_sold' => '0',
            'collected_debt' => '0',
            'expenses' => '0',
            'gross_collected' => '10',
            'net_total' => '10',
            'counted_cash' => '10',
            'variance' => '0',
            'notes' => null,
            'status' => 'closed',
            'del' => 'no',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->withSession(['date_today' => $date])
            ->actingAs($this->branchUser)
            ->get('/sales')
            ->assertOk()
            ->assertSee('data-tip="Day closed"', false)
            ->assertSee('Day closed')
            ->assertSee('Print EOD')
            ->assertDontSee('name="counted_cash"', false);
    }

    public function test_daily_close_print_renders(): void
    {
        $date = date('Y-m-d');

        DB::table('daily_closures')->insert([
            'user_id' => (string) $this->branchUser->id,
            'scope_key' => 'bv:1',
            'branch_label' => 'Branch A',
            'close_date' => $date,
            'cash' => '25',
            'cheque' => '0',
            'momo' => '5',
            'debt_sold' => '0',
            'collected_debt' => '0',
            'expenses' => '2',
            'gross_collected' => '30',
            'net_total' => '28',
            'counted_cash' => null,
            'variance' => null,
            'notes' => 'Ok',
            'status' => 'closed',
            'del' => 'no',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($this->branchUser)
            ->get(route('dailyclose.print', ['date' => $date]))
            ->assertOk()
            ->assertSee('Daily close')
            ->assertSee('25.00')
            ->assertSee('Ok');
    }

    public function test_sales_page_auto_closes_previous_open_day(): void
    {
        $yesterday = date('Y-m-d', strtotime('-1 day'));

        DB::table('sales')->insert([
            'user_id' => (string) $this->branchUser->id,
            'user_bv' => '1',
            'order_no' => 'EOD-AUTO-PAGE',
            'qty' => '1',
            'tot' => '80',
            'pay_mode' => 'Cash',
            'buy_name' => 'Buyer',
            'buy_contact' => '000',
            'del_status' => 'Delivered',
            'discount' => '0',
            'payment' => '80',
            'change' => '0',
            'paid' => 'Paid',
            'del' => 'no',
            'created_at' => $yesterday.' 12:00:00',
            'updated_at' => $yesterday.' 12:00:00',
        ]);

        $this->withSession(['date_today' => date('Y-m-d')])
            ->actingAs($this->branchUser)
            ->get('/sales')
            ->assertOk();

        $this->assertDatabaseHas('daily_closures', [
            'close_date' => $yesterday,
            'scope_key' => 'bv:1',
            'cash' => '80',
            'counted_cash' => '80',
            'variance' => '0',
            'notes' => DailyCloseService::AUTO_CLOSE_NOTE,
            'status' => 'closed',
        ]);
    }
}
