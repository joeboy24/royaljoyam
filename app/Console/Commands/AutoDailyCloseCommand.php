<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\DailyCloseService;
use Illuminate\Console\Command;

class AutoDailyCloseCommand extends Command
{
    protected $signature = 'daily-close:auto
                            {--days=14 : How many past days to scan}
                            {--dry-run : List days that would be auto-closed without saving}';

    protected $description = 'Auto-close past sales days left open, using assumed cash from recorded totals';

    public function handle(DailyCloseService $dailyCloseService): int
    {
        $days = max(1, min(60, (int) $this->option('days')));
        $dryRun = (bool) $this->option('dry-run');
        $closedCount = 0;

        $users = User::query()
            ->where('del', 'no')
            ->orderBy('id')
            ->get();

        $seenScopes = [];

        foreach ($users as $user) {
            $scope = $dailyCloseService->scopeKeyFor($user);
            if (isset($seenScopes[$scope])) {
                continue;
            }
            $seenScopes[$scope] = true;

            if ($dryRun) {
                $today = strtotime(date('Y-m-d'));
                for ($i = 1; $i <= $days; $i++) {
                    $date = date('Y-m-d', strtotime('-'.$i.' days', $today));
                    if ($dailyCloseService->findForUser($user, $date)) {
                        continue;
                    }
                    $summary = $dailyCloseService->summarizeForUser($user, $date);
                    $hasActivity = $summary['gross_collected'] > 0
                        || $summary['debt_sold'] > 0
                        || $summary['expenses'] > 0;
                    if (! $hasActivity) {
                        continue;
                    }
                    $this->line(sprintf(
                        '[dry-run] would auto-close %s (%s) cash=%s',
                        $date,
                        $scope,
                        $summary['cash']
                    ));
                    $closedCount++;
                }
                continue;
            }

            $closed = $dailyCloseService->autoClosePastDays($user, $days);
            foreach ($closed as $closure) {
                $this->line(sprintf(
                    'Auto-closed %s (%s)',
                    $closure->close_date,
                    $closure->scope_key
                ));
            }
            $closedCount += count($closed);
        }

        $this->info(($dryRun ? 'Would auto-close ' : 'Auto-closed ').$closedCount.' day(s).');

        return self::SUCCESS;
    }
}
