<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Schema;

class Code80Controller extends Controller
{
    /**
     * Legacy bootstrap endpoint — only ensures Code80 SuperAdmin exists.
     * No longer creates public Admin/Jay4 accounts.
     */
    public function code80()
    {
        if (Schema::hasTable('users')) {
            User::ensureCode80Exists();
        }

        if (auth()->check()) {
            return redirect('/dashboard');
        }

        return redirect('/login');
    }
}
