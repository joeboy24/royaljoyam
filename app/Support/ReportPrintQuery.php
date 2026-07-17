<?php

namespace App\Support;

use Illuminate\Http\Request;

class ReportPrintQuery
{
    public static function filters(?Request $request = null): array
    {
        $request ??= request();

        return array_filter([
            'date_from' => $request->query('date_from'),
            'date_to' => $request->query('date_to'),
            'branch' => $request->query('branch'),
            'delvr' => $request->query('delvr'),
            'debtsearch' => $request->query('debtsearch'),
            'debt_status' => $request->query('debt_status'),
            'returnsearch' => $request->query('returnsearch'),
            'waybillsearch' => $request->query('waybillsearch'),
            'transfersearch' => $request->query('transfersearch'),
            'from_branch' => $request->query('from_branch'),
            'to_branch' => $request->query('to_branch'),
        ], fn ($value) => $value !== null && $value !== '');
    }

    public static function url(string $path, ?Request $request = null, array $extra = []): string
    {
        $parsed = parse_url($path);
        $basePath = $parsed['path'] ?? $path;
        $existing = [];

        if (! empty($parsed['query'])) {
            parse_str($parsed['query'], $existing);
        }

        $query = array_filter(array_merge($existing, self::filters($request), $extra));

        return $query ? $basePath.'?'.http_build_query($query) : $basePath;
    }
}
