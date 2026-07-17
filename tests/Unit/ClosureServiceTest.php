<?php

namespace Tests\Unit;

use App\Models\User;
use App\Services\ClosureService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use InvalidArgumentException;
use Tests\TestCase;

class ClosureServiceTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected ClosureService $service;

    protected function setUp(): void
    {
        parent::setUp();

        DB::table('company_branches')->insert([
            [
                'id' => 1,
                'user_id' => '1',
                'name' => 'Branch A',
                'loc' => 'Loc 1',
                'contact' => '0000000001',
                'tag' => '1',
                'del' => 'no',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'user_id' => '1',
                'name' => 'Branch B',
                'loc' => 'Loc 2',
                'contact' => '0000000002',
                'tag' => '2',
                'del' => 'no',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $adminId = DB::table('users')->insertGetId([
            'company_branch_id' => '1',
            'name' => 'closure.service.admin',
            'email' => 'closure-service@test.example',
            'bv' => '1',
            'status' => 'Administrator',
            'password' => Hash::make('password'),
            'del' => 'no',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->admin = User::findOrFail($adminId);
        $this->service = app(ClosureService::class);
    }

    public function test_bootstrap_can_open_first_month_without_previous_closure(): void
    {
        $month = date('Y-m-01');

        $closure = $this->service->openMonth($month, $this->admin);

        $this->assertSame($month, $closure->month);
        $this->assertSame('open', $closure->status);
        $this->assertDatabaseHas('closures', [
            'month' => $month,
            'status' => 'open',
        ]);
    }

    public function test_cannot_open_past_month(): void
    {
        $pastMonth = date('Y-m-01', strtotime('-1 month'));

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Openings cannot be made for a past month.');

        $this->service->openMonth($pastMonth, $this->admin);
    }

    public function test_cannot_open_when_previous_month_still_open(): void
    {
        $current = date('Y-m-01');
        $previous = date('Y-m-01', strtotime('-1 month'));

        DB::table('closures')->insert([
            'user_id' => (string) $this->admin->id,
            'month' => $previous,
            'status' => 'open',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Close');

        $this->service->openMonth($current, $this->admin);
    }

    public function test_can_open_when_previous_month_is_closed(): void
    {
        $current = date('Y-m-01');
        $previous = date('Y-m-01', strtotime('-1 month'));

        DB::table('closures')->insert([
            'user_id' => (string) $this->admin->id,
            'month' => $previous,
            'status' => 'closed',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $closure = $this->service->openMonth($current, $this->admin);

        $this->assertSame('open', $closure->status);
        $this->assertSame($current, $closure->month);
    }

    public function test_close_month_snapshots_multi_branch_totals(): void
    {
        $month = date('Y-m-01');

        DB::table('closures')->insert([
            'user_id' => (string) $this->admin->id,
            'month' => $month,
            'status' => 'open',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $itemId = DB::table('items')->insertGetId([
            'item_no' => 'ITM-CLS',
            'user_id' => (string) $this->admin->id,
            'name' => 'Close Tile',
            'desc' => 'Grey',
            'cat' => 'Tiles',
            'brand' => 'Brand',
            'barcode' => 'BCCLS',
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

        $saleAt = date('Y-m-05 12:00:00');

        DB::table('sales_histories')->insert([
            [
                'user_id' => (string) $this->admin->id,
                'sale_id' => '201',
                'item_id' => (string) $itemId,
                'user_bv' => '1',
                'item_no' => 'ITM-CLS',
                'name' => 'Close Tile',
                'qty' => '2',
                'cost_price' => '10',
                'unit_price' => '20',
                'profits' => '20',
                'tot' => '40',
                'del_status' => '1',
                'del' => 'no',
                'created_at' => $saleAt,
                'updated_at' => $saleAt,
            ],
            [
                'user_id' => (string) $this->admin->id,
                'sale_id' => '202',
                'item_id' => (string) $itemId,
                'user_bv' => '2',
                'item_no' => 'ITM-CLS',
                'name' => 'Close Tile',
                'qty' => '3',
                'cost_price' => '10',
                'unit_price' => '20',
                'profits' => '30',
                'tot' => '60',
                'del_status' => '1',
                'del' => 'no',
                'created_at' => $saleAt,
                'updated_at' => $saleAt,
            ],
        ]);

        $closure = $this->service->closeMonth($month, $this->admin);

        $this->assertSame('closed', $closure->status);
        $this->assertSame('5', (string) $closure->tot_qty);
        $this->assertSame('100', (string) $closure->amt_sold);
        $this->assertSame('50', (string) $closure->profits);
        $this->assertSame('40', (string) $closure->avl_qty);
        $this->assertSame('2', (string) $closure->q1);
        $this->assertSame('3', (string) $closure->q2);
    }

    public function test_cannot_close_month_that_is_not_open(): void
    {
        $month = date('Y-m-01');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Open');

        $this->service->closeMonth($month, $this->admin);
    }

    public function test_sales_permit_requires_open_status_for_staff(): void
    {
        $staffId = DB::table('users')->insertGetId([
            'company_branch_id' => '1',
            'name' => 'closure.staff',
            'email' => 'closure-staff@test.example',
            'bv' => '1',
            'status' => 'Branch A',
            'password' => Hash::make('password'),
            'del' => 'no',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $staff = User::findOrFail($staffId);

        $this->assertSame(0, $this->service->salesPermitFor($staff));
        $this->assertSame(1, $this->service->salesPermitFor($this->admin));

        DB::table('closures')->insert([
            'user_id' => (string) $this->admin->id,
            'month' => date('Y-m-01'),
            'status' => 'closed',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->assertSame(0, $this->service->salesPermitFor($staff));
        $this->assertSame(1, $this->service->salesPermitFor($this->admin));
        $this->assertStringContainsString('has been closed', $this->service->salesPermitDeniedMessage());

        DB::table('closures')->where('month', date('Y-m-01'))->update(['status' => 'open']);

        $this->assertSame(1, $this->service->salesPermitFor($staff));
        $this->assertSame(1, $this->service->salesPermitFor($this->admin));
    }
}
