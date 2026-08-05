<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\SetupService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Throwable;

class SetupController extends Controller
{
    public function __construct(private readonly SetupService $setup)
    {
    }

    public function show()
    {
        if ($this->setup->isComplete()) {
            return redirect(auth()->check() ? '/dashboard' : '/login');
        }

        return view('setup.index', [
            'step' => $this->setup->currentStep(),
            'hasCompany' => $this->setup->hasCompany(),
            'hasBranch' => $this->setup->hasBranch(),
            'hasAdministrator' => $this->setup->hasAdministrator(),
            'migrationsPending' => $this->setup->migrationsPending(),
            'appReady' => $this->setup->isAppReady(),
        ]);
    }

    public function migrate()
    {
        if ($this->setup->isAppReady() && $this->setup->isComplete()) {
            return redirect(auth()->check() ? '/dashboard' : '/login');
        }

        try {
            $this->setup->runMigrations();
        } catch (Throwable $e) {
            return redirect()
                ->route('setup.show')
                ->with('error', $e->getMessage() ?: 'Database initialization failed.');
        }

        return redirect()
            ->route('setup.show')
            ->with('success', 'Database initialized successfully. Continue with company details.');
    }

    public function storeCompany(Request $request)
    {
        if ($this->setup->migrationsPending()) {
            return redirect()->route('setup.show')->with('error', 'Initialize the database first.');
        }

        if ($this->setup->hasCompany()) {
            return redirect()->route('setup.show');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:191',
            'company_add' => 'required|string|max:1000',
            'loc' => 'nullable|string|max:191',
            'contact' => 'required|string|max:191',
            'email' => 'nullable|email|max:191',
            'company_web' => 'nullable|string|max:191',
            'company_logo' => 'nullable|image|max:5000|mimes:jpeg,jpg,png',
        ]);

        if ($validator->fails()) {
            return redirect()->route('setup.show')->withErrors($validator)->withInput();
        }

        try {
            $actor = auth()->user() instanceof User ? auth()->user() : null;
            $this->setup->saveCompany($validator->validated(), $request->file('company_logo'), $actor);
        } catch (Throwable $e) {
            return redirect()->route('setup.show')->with('error', $e->getMessage())->withInput();
        }

        return redirect()
            ->route('setup.show')
            ->with('success', 'Company saved. Add your first branch.');
    }

    public function storeBranch(Request $request)
    {
        if (! $this->setup->hasCompany()) {
            return redirect()->route('setup.show')->with('error', 'Save company details first.');
        }

        if ($this->setup->hasBranch()) {
            return redirect()->route('setup.show');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:191|unique:company_branches,name',
            'loc' => 'required|string|max:191',
            'contact' => 'required|string|max:191',
        ]);

        if ($validator->fails()) {
            return redirect()->route('setup.show')->withErrors($validator)->withInput();
        }

        try {
            $actor = auth()->user() instanceof User ? auth()->user() : null;
            $this->setup->saveBranch($validator->validated(), $actor);
        } catch (Throwable $e) {
            return redirect()->route('setup.show')->with('error', $e->getMessage())->withInput();
        }

        return redirect()
            ->route('setup.show')
            ->with('success', 'Branch saved. Create your first administrator.');
    }

    public function storeAdmin(Request $request)
    {
        if (! $this->setup->hasCompany() || ! $this->setup->hasBranch()) {
            return redirect()->route('setup.show')->with('error', 'Finish company and branch setup first.');
        }

        if ($this->setup->hasAdministrator()) {
            return redirect()->route('setup.show');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:191|unique:users,name',
            'email' => 'required|email|max:191|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->route('setup.show')->withErrors($validator)->withInput();
        }

        try {
            $this->setup->saveAdministrator($validator->validated());
        } catch (Throwable $e) {
            return redirect()->route('setup.show')->with('error', $e->getMessage())->withInput();
        }

        return redirect()
            ->route('login')
            ->with('success', 'Setup complete. Sign in with your administrator account.');
    }

    public function skipAdmin()
    {
        if (! $this->setup->isAppReady()) {
            return redirect()->route('setup.show');
        }

        return redirect()
            ->route('login')
            ->with('success', 'Setup is ready. Sign in to continue. You can add an administrator later from Registry.');
    }

    public function signin()
    {
        if ($this->setup->migrationsPending()) {
            return redirect()
                ->route('setup.show')
                ->with('info', 'Complete setup first before signing in.');
        }

        return redirect()->route('login');
    }
}
