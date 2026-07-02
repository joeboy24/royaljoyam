<?php

namespace Tests\Unit;

use App\Support\DashNavigation;
use Illuminate\Http\Request;
use Tests\TestCase;

class DashNavigationTest extends TestCase
{
    public function test_resolves_inventory_for_items_routes(): void
    {
        $request = Request::create('/items', 'GET');

        $this->assertSame('inventory', DashNavigation::activeKey($request));
    }

    public function test_resolves_report_for_sales_history_route(): void
    {
        $request = Request::create('/saleshistory', 'GET');

        $this->assertSame('report', DashNavigation::activeKey($request));
    }

    public function test_resolves_sales_for_sales_route(): void
    {
        $request = Request::create('/sales', 'GET');

        $this->assertSame('sales', DashNavigation::activeKey($request));
    }

    public function test_items_include_active_flag(): void
    {
        $request = Request::create('/dashuser', 'GET');
        $items = DashNavigation::items($request);

        $registry = collect($items)->firstWhere('key', 'registry');
        $inventory = collect($items)->firstWhere('key', 'inventory');

        $this->assertTrue($registry['active']);
        $this->assertFalse($inventory['active']);
    }
}
