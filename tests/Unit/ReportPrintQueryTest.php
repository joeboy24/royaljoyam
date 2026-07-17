<?php

namespace Tests\Unit;

use App\Support\ReportPrintQuery;
use Illuminate\Http\Request;
use Tests\TestCase;

class ReportPrintQueryTest extends TestCase
{
    public function test_url_appends_active_report_filters(): void
    {
        $request = Request::create('/debts', 'GET', [
            'date_from' => '2026-07-01',
            'date_to' => '2026-07-16',
            'debtsearch' => 'Buyer',
        ]);

        $this->app->instance('request', $request);

        $url = ReportPrintQuery::url('/debtsreportprinting', $request);

        $this->assertStringContainsString('date_from=2026-07-01', $url);
        $this->assertStringContainsString('date_to=2026-07-16', $url);
        $this->assertStringContainsString('debtsearch=Buyer', $url);
    }

    public function test_url_includes_debt_status_filter(): void
    {
        $request = Request::create('/debts', 'GET', [
            'debt_status' => 'cleared',
        ]);

        $this->app->instance('request', $request);

        $url = ReportPrintQuery::url('/debts', $request);

        $this->assertStringContainsString('debt_status=cleared', $url);
    }

    public function test_url_includes_transfer_report_filters(): void
    {
        $request = Request::create('/branchtransfers', 'GET', [
            'transfersearch' => 'Rod',
            'from_branch' => '2',
            'to_branch' => '1',
        ]);

        $this->app->instance('request', $request);

        $url = ReportPrintQuery::url('/stockbal', $request);

        $this->assertStringContainsString('transfersearch=Rod', $url);
        $this->assertStringContainsString('from_branch=2', $url);
        $this->assertStringContainsString('to_branch=1', $url);
    }

    public function test_url_merges_existing_query_string(): void
    {
        $request = Request::create('/waybillreport', 'GET', [
            'waybillsearch' => 'Alpha',
        ]);

        $this->app->instance('request', $request);

        $url = ReportPrintQuery::url('/waybillprint?date_from=2026-07-01', $request);

        $this->assertStringContainsString('date_from=2026-07-01', $url);
        $this->assertStringContainsString('waybillsearch=Alpha', $url);
    }
}
