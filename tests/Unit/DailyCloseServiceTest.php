<?php

namespace Tests\Unit;

use App\Models\User;
use App\Services\DailyCloseService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use InvalidArgumentException;
use Tests\TestCase;

class DailyCloseServiceTest extends TestCase
{
    use RefreshDatabase;

    protected User $branchUser;

    protected DailyCloseService $service;

    protected function setUp(): void
    {
        parent::setUp();

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

        $id = DB::table('users')->insertGetId([
            'company_branch_id' => '1',
            'name' => 'daily.branch',
            'email' => 'daily-branch@test.example',
            'bv' => '1',
            'status' => 'Branch A',
            'password' => Hash::make('password'),
            'del' => 'no',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->branchUser = User::findOrFail($id);
        $this->service = app(DailyCloseService::class);
    }

    public function test_close_day_snapshots_totals_and_cash_variance(): void
    {
        $date = date('Y-m-d');

        DB::table('sales')->insert([
            'user_id' => (string) $this->branchUser->id,
            'user_bv' => '1',
            'order_no' => 'EOD-1',
            'qty' => '1',
            'tot' => '100',
            'pay_mode' => 'Cash',
            'buy_name' => 'Buyer',
            'buy_contact' => '000',
            'del_status' => 'Delivered',
            'discount' => '0',
            'payment' => '100',
            'change' => '0',
            'paid' => 'Paid',
            'del' => 'no',
            'created_at' => $date.' 10:00:00',
            'updated_at' => $date.' 10:00:00',
        ]);

        DB::table('expenses')->insert([
            'user_id' => (string) $this->branchUser->id,
            'companybranch_id' => '1',
            'title' => 'Fuel',
            'desc' => 'Trip',
            'expense_cost' => '20',
            'del' => 'no',
            'created_at' => $date.' 11:00:00',
            'updated_at' => $date.' 11:00:00',
        ]);

        $closure = $this->service->closeDay($this->branchUser, $date, 95.0, 'Till short');

        $this->assertSame($date, $closure->close_date);
        $this->assertSame('100', (string) $closure->cash);
        $this->assertSame('20', (string) $closure->expenses);
        $this->assertSame('80', (string) $closure->net_total);
        $this->assertSame('95', (string) $closure->counted_cash);
        $this->assertSame('-5', (string) $closure->variance);
        $this->assertSame('Till short', $closure->notes);
    }

    public function test_cannot_close_same_day_twice(): void
    {
        $date = date('Y-m-d');
        $this->service->closeDay($this->branchUser, $date);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('already closed');

        $this->service->closeDay($this->branchUser, $date);
    }

    public function test_auto_close_uses_assumed_cash_and_system_note(): void
    {
        $date = date('Y-m-d', strtotime('-1 day'));

        DB::table('sales')->insert([
            'user_id' => (string) $this->branchUser->id,
            'user_bv' => '1',
            'order_no' => 'EOD-AUTO-1',
            'qty' => '1',
            'tot' => '150',
            'pay_mode' => 'Cash',
            'buy_name' => 'Buyer',
            'buy_contact' => '000',
            'del_status' => 'Delivered',
            'discount' => '0',
            'payment' => '150',
            'change' => '0',
            'paid' => 'Paid',
            'del' => 'no',
            'created_at' => $date.' 10:00:00',
            'updated_at' => $date.' 10:00:00',
        ]);

        $closure = $this->service->autoCloseDay($this->branchUser, $date);

        $this->assertNotNull($closure);
        $this->assertSame($date, $closure->close_date);
        $this->assertSame('150', (string) $closure->cash);
        $this->assertSame('150', (string) $closure->counted_cash);
        $this->assertSame('0', (string) $closure->variance);
        $this->assertSame(DailyCloseService::AUTO_CLOSE_NOTE, $closure->notes);
    }

    public function test_auto_close_skips_today_and_empty_days(): void
    {
        $today = date('Y-m-d');
        $emptyYesterday = date('Y-m-d', strtotime('-1 day'));

        $this->assertNull($this->service->autoCloseDay($this->branchUser, $today));
        $this->assertNull($this->service->autoCloseDay($this->branchUser, $emptyYesterday));
    }

    public function test_auto_close_past_days_closes_recent_open_activity(): void
    {
        $date = date('Y-m-d', strtotime('-2 days'));

        DB::table('sales')->insert([
            'user_id' => (string) $this->branchUser->id,
            'user_bv' => '1',
            'order_no' => 'EOD-AUTO-2',
            'qty' => '1',
            'tot' => '40',
            'pay_mode' => 'Cash',
            'buy_name' => 'Buyer',
            'buy_contact' => '000',
            'del_status' => 'Delivered',
            'discount' => '0',
            'payment' => '40',
            'change' => '0',
            'paid' => 'Paid',
            'del' => 'no',
            'created_at' => $date.' 09:00:00',
            'updated_at' => $date.' 09:00:00',
        ]);

        $closed = $this->service->autoClosePastDays($this->branchUser, 7);

        $this->assertCount(1, $closed);
        $this->assertSame($date, $closed[0]->close_date);
        $this->assertSame([], $this->service->autoClosePastDays($this->branchUser, 7));
    }

    public function test_summarize_branches_for_date_splits_by_user_bv(): void
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

        $date = date('Y-m-d', strtotime('-1 day'));

        DB::table('sales')->insert([
            [
                'user_id' => (string) $this->branchUser->id,
                'user_bv' => '1',
                'order_no' => 'EOD-BR-1',
                'qty' => '1',
                'tot' => '100',
                'pay_mode' => 'Cash',
                'buy_name' => 'Buyer',
                'buy_contact' => '000',
                'del_status' => 'Delivered',
                'discount' => '0',
                'payment' => '100',
                'change' => '0',
                'paid' => 'Paid',
                'del' => 'no',
                'created_at' => $date.' 10:00:00',
                'updated_at' => $date.' 10:00:00',
            ],
            [
                'user_id' => '99',
                'user_bv' => '2',
                'order_no' => 'EOD-BR-2',
                'qty' => '1',
                'tot' => '55',
                'pay_mode' => 'Cash',
                'buy_name' => 'Buyer 2',
                'buy_contact' => '000',
                'del_status' => 'Delivered',
                'discount' => '0',
                'payment' => '55',
                'change' => '0',
                'paid' => 'Paid',
                'del' => 'no',
                'created_at' => $date.' 11:00:00',
                'updated_at' => $date.' 11:00:00',
            ],
        ]);

        $rows = $this->service->summarizeBranchesForDate($date);
        $byTag = collect($rows)->keyBy('tag');

        $this->assertSame(100.0, $byTag['1']['cash']);
        $this->assertSame(100.0, $byTag['1']['gross_collected']);
        $this->assertSame(55.0, $byTag['2']['cash']);
        $this->assertSame('Branch A', $byTag['1']['name']);
        $this->assertSame('Branch B', $byTag['2']['name']);
    }
}
