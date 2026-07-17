<?php

namespace Tests\Unit;

use App\Models\Item;
use App\Models\SalesHistory;
use App\Models\Wbdistribution;
use App\Services\ClosureSummaryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ClosureSummaryServiceTest extends TestCase
{
    use RefreshDatabase;

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
    }

    public function test_build_aggregates_all_branches_for_sales_and_distribution(): void
    {
        $itemId = DB::table('items')->insertGetId([
            'item_no' => 'ITM-100',
            'user_id' => '1',
            'name' => 'Ceramic Tile',
            'desc' => 'White',
            'cat' => 'Tiles',
            'brand' => 'Brand',
            'barcode' => 'BC100',
            'qty' => '80',
            'price' => '50',
            'cost_price' => '30',
            'q1' => '50',
            'q2' => '30',
            'q3' => '0',
            'del' => 'no',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('sales_histories')->insert([
            [
                'user_id' => '1',
                'sale_id' => '10',
                'item_id' => (string) $itemId,
                'user_bv' => '1',
                'item_no' => 'ITM-100',
                'name' => 'Ceramic Tile',
                'qty' => '4',
                'cost_price' => '30',
                'unit_price' => '50',
                'profits' => '80',
                'tot' => '200',
                'del_status' => '1',
                'del' => 'no',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => '2',
                'sale_id' => '11',
                'item_id' => (string) $itemId,
                'user_bv' => '2',
                'item_no' => 'ITM-100',
                'name' => 'Ceramic Tile',
                'qty' => '3',
                'cost_price' => '30',
                'unit_price' => '50',
                'profits' => '60',
                'tot' => '150',
                'del_status' => '1',
                'del' => 'no',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        DB::table('wbdistributions')->insert([
            'user_id' => '1',
            'waybill_id' => '1',
            'item_id' => (string) $itemId,
            'q1' => '10',
            'q2' => '5',
            'q3' => '0',
            'del' => 'no',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $summary = app(ClosureSummaryService::class)->build(
            SalesHistory::with('item')->get(),
            Wbdistribution::with('item')->get(),
            Item::all()
        );

        $this->assertSame(7.0, $summary['summary']['qty_sold']);
        $this->assertSame(350.0, $summary['summary']['amt_sold']);
        $this->assertSame(140.0, $summary['summary']['profit']);
        $this->assertSame(80.0, $summary['summary']['qty_available']);

        $this->assertCount(2, $summary['branch_summaries']);
        $this->assertSame(4.0, $summary['branch_summaries'][0]['qty_sold']);
        $this->assertSame(3.0, $summary['branch_summaries'][1]['qty_sold']);

        $this->assertCount(1, $summary['distribution_rows']);
        $this->assertSame(10.0, $summary['distribution_rows'][0]['quantities']['q1']);
        $this->assertSame(5.0, $summary['distribution_rows'][0]['quantities']['q2']);
        $this->assertSame(15.0, $summary['distribution_rows'][0]['total']);
        $this->assertSame(15.0, $summary['distribution_totals']['total']);

        $this->assertCount(1, $summary['sales_rows']);
        $this->assertSame(4.0, $summary['sales_rows'][0]['branches']['1']['qty_sold']);
        $this->assertSame(3.0, $summary['sales_rows'][0]['branches']['2']['qty_sold']);
        $this->assertSame(50.0, $summary['sales_rows'][0]['branches']['1']['qty_rem']);
        $this->assertSame(30.0, $summary['sales_rows'][0]['branches']['2']['qty_rem']);
        $this->assertSame(4.0, $summary['sales_totals']['1']['qty_sold']);
        $this->assertSame(3.0, $summary['sales_totals']['2']['qty_sold']);
    }
}
