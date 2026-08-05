<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\SetupService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class SetupWizardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['setup.enforce' => true]);
        @unlink(app(SetupService::class)->completionFlagPath());
    }

    protected function seedCompanyAndBranch(): void
    {
        DB::table('companies')->insert([
            'id' => 1,
            'user_id' => '1',
            'name' => 'Royal Joyam Ventures',
            'address' => 'Test Address',
            'contact' => '0000000000',
            'logo' => 'logo.png',
            'del' => 'no',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('company_branches')->insert([
            'user_id' => '1',
            'name' => 'Main Branch',
            'loc' => 'Accra',
            'contact' => '0000000001',
            'tag' => '1',
            'del' => 'no',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    protected function seedAdministrator(): User
    {
        $id = DB::table('users')->insertGetId([
            'company_branch_id' => '1',
            'name' => 'admin.setup',
            'email' => 'admin-setup@test.example',
            'bv' => 'A',
            'status' => User::STATUS_ADMINISTRATOR,
            'password' => Hash::make('password'),
            'del' => 'no',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return User::findOrFail($id);
    }

    public function test_incomplete_setup_redirects_dashboard_to_setup(): void
    {
        $this->get('/dashboard')->assertRedirect(route('setup.show'));
    }

    public function test_setup_page_shows_company_step_when_migrations_are_done(): void
    {
        // RefreshDatabase already migrated; no company yet.
        $response = $this->get(route('setup.show'));

        $response->assertOk();
        $response->assertSee('Company details');
        $response->assertSee('Continue');
        $response->assertSee('Company Manager');
        $response->assertSee('by PivoApps');
    }

    public function test_can_save_company_branch_and_admin_to_complete_setup(): void
    {
        User::ensureCode80Exists();

        $this->post(route('setup.company'), [
            'name' => 'Setup Co',
            'company_add' => '12 Test Street',
            'loc' => 'Kumasi',
            'contact' => '0244000000',
            'email' => 'office@setup.test',
            'company_web' => 'https://setup.test',
        ])->assertRedirect(route('setup.show'));

        $this->assertDatabaseHas('companies', [
            'id' => 1,
            'name' => 'Setup Co',
            'address' => '12 Test Street',
            'contact' => '0244000000',
        ]);

        $this->post(route('setup.branch'), [
            'name' => 'HQ Branch',
            'loc' => 'Kumasi',
            'contact' => '0244111111',
        ])->assertRedirect(route('setup.show'));

        $this->assertDatabaseHas('company_branches', [
            'name' => 'HQ Branch',
            'del' => 'no',
        ]);

        $this->post(route('setup.admin'), [
            'name' => 'first.admin',
            'email' => 'first.admin@setup.test',
            'password' => 'secret12',
            'password_confirmation' => 'secret12',
        ])->assertRedirect(route('login'));

        $this->assertDatabaseHas('users', [
            'name' => 'first.admin',
            'status' => User::STATUS_ADMINISTRATOR,
        ]);

        $this->assertTrue(app(SetupService::class)->isComplete());
        $this->assertTrue(app(SetupService::class)->isAppReady());
        $this->assertTrue(app(SetupService::class)->hasCompletionFlag());
        $this->assertDatabaseHas('categories', [
            'name' => 'General',
        ]);
        $this->get('/dashboard')->assertRedirect(route('login'));
    }

    public function test_app_ready_without_admin_allows_dashboard_after_login(): void
    {
        $this->seedCompanyAndBranch();
        User::ensureCode80Exists();

        $this->assertTrue(app(SetupService::class)->isAppReady());
        $this->assertFalse(app(SetupService::class)->isComplete());

        $this->actingAs(User::where('name', User::CODE80_NAME)->first())
            ->get('/dashboard')
            ->assertOk();
    }

    public function test_completed_setup_redirects_setup_page_to_login(): void
    {
        $this->seedCompanyAndBranch();
        $this->seedAdministrator();

        $this->get(route('setup.show'))->assertRedirect(route('login'));
    }

    public function test_migrate_endpoint_is_safe_when_already_migrated(): void
    {
        $this->post(route('setup.migrate'))->assertRedirect(route('setup.show'));
        $this->assertTrue(Schema::hasTable('users'));
        $this->assertTrue(Schema::hasTable('companies'));
    }

    public function test_signin_stays_on_setup_when_migrations_are_pending(): void
    {
        $this->mock(SetupService::class, function ($mock) {
            $mock->shouldReceive('isComplete')->andReturn(false);
            $mock->shouldReceive('migrationsPending')->andReturn(true);
            $mock->shouldReceive('isAppReady')->andReturn(false);
            $mock->shouldReceive('currentStep')->andReturn('migrate');
            $mock->shouldReceive('hasCompany')->andReturn(false);
            $mock->shouldReceive('hasBranch')->andReturn(false);
            $mock->shouldReceive('hasAdministrator')->andReturn(false);
        });

        $this->get(route('setup.signin'))
            ->assertRedirect(route('setup.show'))
            ->assertSessionHas('info');
    }

    public function test_public_register_routes_are_disabled(): void
    {
        $this->get('/register')->assertNotFound();
        $this->post('/register', [
            'name' => 'public.user',
            'email' => 'public@test.example',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertNotFound();

        $this->assertDatabaseMissing('users', ['email' => 'public@test.example']);
    }

    public function test_ensure_default_category_is_idempotent(): void
    {
        User::ensureCode80Exists();
        $setup = app(SetupService::class);

        $setup->ensureDefaultCategory();
        $setup->ensureDefaultCategory();

        $this->assertSame(1, \App\Models\Category::query()->where('name', 'General')->count());
    }
}
