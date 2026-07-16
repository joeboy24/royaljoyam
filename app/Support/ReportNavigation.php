<?php

namespace App\Support;

use Illuminate\Http\Request;

class ReportNavigation
{
    public static function activeKey(?Request $request = null, ?string $override = null): ?string
    {
        if ($override !== null && $override !== '') {
            return $override;
        }

        $request = $request ?? request();

        if (! $request) {
            return null;
        }

        foreach (config('report-nav.items', []) as $item) {
            foreach ($item['match'] as $pattern) {
                if ($request->is($pattern)) {
                    return $item['key'];
                }
            }
        }

        return null;
    }

    public static function items(?Request $request = null, ?string $activeOverride = null): array
    {
        $activeKey = static::activeKey($request, $activeOverride);

        return collect(config('report-nav.items', []))
            ->map(function (array $item) use ($activeKey) {
                $item['active'] = $activeKey === $item['key'];

                return $item;
            })
            ->all();
    }
}
