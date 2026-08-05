<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Throwable;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Show the application's login form with company branding when configured.
     */
    public function showLoginForm()
    {
        $company = null;
        $companyLogoUrl = null;

        try {
            if (Schema::hasTable('companies')) {
                $company = Company::find(1);
                if ($company && $company->del === 'yes') {
                    $company = null;
                }
            }

            if ($company) {
                $logo = trim((string) $company->logo);
                if ($logo !== '' && Storage::disk('public')->exists('ss_imgs/'.$logo)) {
                    $companyLogoUrl = asset('storage/ss_imgs/'.$logo);
                }
            }
        } catch (Throwable $e) {
            $company = null;
            $companyLogoUrl = null;
        }

        return view('auth.login', [
            'company' => $company,
            'companyLogoUrl' => $companyLogoUrl,
        ]);
    }

    /**
     * Allow sign-in with email or username (name).
     */
    protected function credentials(Request $request)
    {
        $login = (string) $request->input('email');
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'name';

        return [
            $field => $login,
            'password' => $request->input('password'),
        ];
    }

    /**
     * Use present (not required) so a whitespace-only password can authenticate Code80.
     */
    protected function validateLogin(Request $request)
    {
        $request->validate([
            $this->username() => 'required|string',
            'password' => 'present|string',
        ]);
    }
}
