<?php

namespace App\Support;

use Illuminate\Http\Request;

class DashNavigation
{
    public static function activeKey(?Request $request = null): ?string
    {
        $request = $request ?? request();

        if (!$request) {
            return null;
        }

        foreach (config('dash-nav.items', []) as $item) {
            foreach ($item['match'] as $pattern) {
                if ($request->is($pattern)) {
                    return $item['key'];
                }
            }
        }

        return null;
    }

    public static function items(?Request $request = null): array
    {
        $activeKey = static::activeKey($request);

        return collect(config('dash-nav.items', []))
            ->map(function (array $item) use ($activeKey) {
                $item['active'] = $activeKey === $item['key'];

                return $item;
            })
            ->all();
    }
}
