<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfilePageTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedProfileBaseline();
        $this->user = $this->createUser();
    }

    protected function seedProfileBaseline(): void
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

    protected function createUser(array $overrides = []): User
    {
        $data = array_merge([
            'company_branch_id' => '1',
            'name' => 'profile.user',
            'email' => 'profile@test.example',
            'bv' => '1',
            'status' => 'Branch User',
            'password' => Hash::make('old-password'),
            'del' => 'no',
        ], $overrides);

        $id = DB::table('users')->insertGetId(array_merge($data, [
            'created_at' => now(),
            'updated_at' => now(),
        ]));

        return User::findOrFail($id);
    }

    public function test_profile_page_renders_for_authenticated_user(): void
    {
        $response = $this->actingAs($this->user)->get('/user_profile');

        $response->assertOk();
        $response->assertSee('My profile');
        $response->assertSee('profile.user');
        $response->assertSee('Branch A');
    }

    public function test_user_can_update_name_and_email_without_changing_password(): void
    {
        $response = $this->actingAs($this->user)->put('/user_profile', [
            'name' => 'updated.user',
            'email' => 'updated@test.example',
            'password' => '',
            'password_confirmation' => '',
        ]);

        $response->assertRedirect(route('user_profile'));
        $response->assertSessionHas('success');

        $this->user->refresh();
        $this->assertSame('updated.user', $this->user->name);
        $this->assertSame('updated@test.example', $this->user->email);
        $this->assertTrue(Hash::check('old-password', $this->user->password));
    }

    public function test_user_can_update_password_when_confirmed(): void
    {
        $response = $this->actingAs($this->user)->put('/user_profile', [
            'name' => $this->user->name,
            'email' => $this->user->email,
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $response->assertRedirect(route('user_profile'));

        $this->user->refresh();
        $this->assertTrue(Hash::check('new-password', $this->user->password));
    }

    public function test_profile_update_rejects_mismatched_password_confirmation(): void
    {
        $response = $this->actingAs($this->user)->put('/user_profile', [
            'name' => $this->user->name,
            'email' => $this->user->email,
            'password' => 'new-password',
            'password_confirmation' => 'different-password',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertTrue(Hash::check('old-password', $this->user->fresh()->password));
    }

    public function test_profile_update_rejects_duplicate_username(): void
    {
        $this->createUser([
            'name' => 'taken.name',
            'email' => 'taken@test.example',
        ]);

        $response = $this->actingAs($this->user)->put('/user_profile', [
            'name' => 'taken.name',
            'email' => $this->user->email,
        ]);

        $response->assertSessionHasErrors('name');
    }
}
