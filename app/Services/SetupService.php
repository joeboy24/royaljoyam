<?php

namespace App\Services;

use App\Models\Company;
use App\Models\CompanyBranch;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Throwable;

class SetupService
{
    public function migrationsPending(): bool
    {
        try {
            $migrator = app('migrator');

            if (! $migrator->repositoryExists()) {
                return true;
            }

            $files = $migrator->getMigrationFiles(database_path('migrations'));
            $ran = $migrator->getRepository()->getRan();

            return count(array_diff(array_keys($files), $ran)) > 0;
        } catch (Throwable $e) {
            return true;
        }
    }

    public function hasCompany(): bool
    {
        try {
            if (! Schema::hasTable('companies')) {
                return false;
            }

            $company = Company::find(1);

            return $company
                && $company->del !== 'yes'
                && trim((string) $company->name) !== '';
        } catch (Throwable $e) {
            return false;
        }
    }

    public function hasBranch(): bool
    {
        try {
            if (! Schema::hasTable('company_branches')) {
                return false;
            }

            return CompanyBranch::query()->where('del', 'no')->exists();
        } catch (Throwable $e) {
            return false;
        }
    }

    public function hasAdministrator(): bool
    {
        try {
            if (! Schema::hasTable('users')) {
                return false;
            }

            return User::query()
                ->where('status', User::STATUS_ADMINISTRATOR)
                ->where('del', 'no')
                ->where('name', '!=', User::CODE80_NAME)
                ->exists();
        } catch (Throwable $e) {
            return false;
        }
    }

    /**
     * Minimum data required for the app to run without null company/branch crashes.
     */
    public function isAppReady(): bool
    {
        return ! $this->migrationsPending()
            && $this->hasCompany()
            && $this->hasBranch();
    }

    /**
     * Full first-run wizard finished (includes day-to-day Administrator).
     */
    public function isComplete(): bool
    {
        return $this->isAppReady() && $this->hasAdministrator();
    }

    /**
     * Current wizard step: migrate | company | branch | admin | done
     */
    public function currentStep(): string
    {
        if ($this->migrationsPending()) {
            return 'migrate';
        }

        if (! $this->hasCompany()) {
            return 'company';
        }

        if (! $this->hasBranch()) {
            return 'branch';
        }

        if (! $this->hasAdministrator()) {
            return 'admin';
        }

        return 'done';
    }

    public function runMigrations(): void
    {
        $lockPath = storage_path('framework/setup-migrate.lock');
        $lock = fopen($lockPath, 'c+');

        if ($lock === false || ! flock($lock, LOCK_EX | LOCK_NB)) {
            if (is_resource($lock)) {
                fclose($lock);
            }

            throw new \RuntimeException('Database initialization is already running. Please wait and refresh.');
        }

        try {
            Artisan::call('migrate', ['--force' => true]);

            if (Schema::hasTable('users')) {
                User::ensureCode80Exists();
            }
        } finally {
            flock($lock, LOCK_UN);
            fclose($lock);
        }
    }

    public function saveCompany(array $data, ?UploadedFile $logo, ?User $actor): Company
    {
        $ownerId = $actor?->id ?? User::query()->where('name', User::CODE80_NAME)->value('id') ?? 1;

        $filenameToStore = 'logo.png';
        if ($logo) {
            $fileExt = $logo->getClientOriginalExtension() ?: 'png';
            $filenameToStore = 'company_logo.'.$fileExt;
            $logo->storeAs('public/ss_imgs', $filenameToStore);
        }

        $company = Company::find(1) ?? new Company;
        $company->user_id = (string) $ownerId;
        $company->name = $data['name'];
        $company->address = $data['company_add'];
        $company->location = $data['loc'] ?? null;
        $company->contact = $data['contact'];
        $company->email = $data['email'] ?? null;
        $company->website = $data['company_web'] ?? null;
        $company->reg_date = date('d-m-Y');
        $company->logo = $filenameToStore;
        $company->del = 'no';
        $company->save();

        // Ensure the primary company row is id=1 for the rest of the app.
        if ((int) $company->id !== 1) {
            throw new \RuntimeException('Company must be stored as id 1. Please contact support.');
        }

        return $company;
    }

    public function saveBranch(array $data, ?User $actor): CompanyBranch
    {
        $ownerId = $actor?->id ?? User::query()->where('name', User::CODE80_NAME)->value('id') ?? 1;
        $activeCount = CompanyBranch::query()->where('del', 'no')->count();

        if ($activeCount >= 5) {
            throw new \RuntimeException('Maximum of 5 branches reached.');
        }

        $tag = (int) CompanyBranch::query()->max('tag') + 1;
        if ($tag < 1) {
            $tag = 1;
        }

        $branch = new CompanyBranch;
        $branch->user_id = (string) $ownerId;
        $branch->name = $data['name'];
        $branch->loc = $data['loc'];
        $branch->contact = $data['contact'];
        $branch->tag = (string) $tag;
        $branch->del = 'no';
        $branch->save();

        return $branch;
    }

    public function saveAdministrator(array $data): User
    {
        if (strcasecmp((string) $data['name'], User::CODE80_NAME) === 0) {
            throw new \RuntimeException('That username is reserved.');
        }

        $user = new User;
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->password = Hash::make($data['password']);
        $user->status = User::STATUS_ADMINISTRATOR;
        $user->bv = 'A';
        $user->company_branch_id = '1';
        $user->del = 'no';
        $user->save();

        return $user;
    }
}
