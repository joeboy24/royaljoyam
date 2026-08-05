<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class EnsureCode80User
{
    /**
     * Ensure Code80 SuperAdmin exists on each page load (when users table is ready).
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            if (Schema::hasTable('users')) {
                User::ensureCode80Exists();
            }
        } catch (\Throwable $e) {
            // Ignore during migrate / incomplete bootstrap.
        }

        return $next($request);
    }
}
