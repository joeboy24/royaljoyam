<?php

namespace Tests\Unit;

use App\Support\ReportNavigation;
use Illuminate\Http\Request;
use Tests\TestCase;

class ReportNavigationTest extends TestCase
{
    public function test_resolves_sales_tab_for_reporting_route(): void
    {
        $request = Request::create('/reporting', 'GET');

        $this->assertSame('sales', ReportNavigation::activeKey($request));
    }

    public function test_resolves_distribution_tab_for_distreport_route(): void
    {
        $request = Request::create('/distreport', 'GET');

        $this->assertSame('distribution', ReportNavigation::activeKey($request));
    }

    public function test_items_include_active_flag(): void
    {
        $request = Request::create('/debts', 'GET');
        $items = ReportNavigation::items($request);

        $debts = collect($items)->firstWhere('key', 'debts');
        $sales = collect($items)->firstWhere('key', 'sales');

        $this->assertTrue($debts['active']);
        $this->assertFalse($sales['active']);
        $this->assertCount(7, $items);
    }
}
