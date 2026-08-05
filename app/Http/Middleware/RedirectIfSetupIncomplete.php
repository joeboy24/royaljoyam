<?php

namespace App\Http\Middleware;

use App\Services\SetupService;
use Closure;
use Illuminate\Http\Request;

class RedirectIfSetupIncomplete
{
    public function __construct(private readonly SetupService $setup)
    {
    }

    /**
     * Force unfinished installs through /setup until company + branch exist.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (! config('setup.enforce', true)) {
            return $next($request);
        }

        $isSetupRoute = $request->is('setup') || $request->is('setup/*');

        if ($isSetupRoute) {
            if ($this->setup->isComplete()) {
                return redirect(auth()->check() ? '/dashboard' : '/login');
            }

            return $next($request);
        }

        if ($this->shouldAllowWithoutSetup($request)) {
            return $next($request);
        }

        if (! $this->setup->isAppReady()) {
            return redirect()->route('setup.show');
        }

        return $next($request);
    }

    protected function shouldAllowWithoutSetup(Request $request): bool
    {
        return $request->is(
            'login',
            'logout',
            'register',
            'password/*',
            'code80',
            'test_mode'
        );
    }
}
