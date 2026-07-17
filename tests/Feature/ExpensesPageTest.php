<?php

namespace Tests\Feature;

use App\Models\Expense;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ExpensesPageTest extends TestCase
{
    use RefreshDatabase;

    protected User $branchUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedExpensesBaseline();
        $this->branchUser = $this->createBranchUser();
    }

    protected function seedExpensesBaseline(): void
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
            'id' => 1,
            'user_id' => '1',
            'name' => 'Branch A',
            'loc' => 'Loc 1',
            'contact' => '0000000001',
            'tag' => '1',
            'del' => 'no',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('closures')->insert([
            'user_id' => '1',
            'month' => date('Y-m-01'),
            'status' => 'open',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    protected function createBranchUser(): User
    {
        $id = DB::table('users')->insertGetId([
            'company_branch_id' => '1',
            'name' => 'branch.expense',
            'email' => 'expense@test.example',
            'bv' => '1',
            'status' => 'Branch A',
            'password' => Hash::make('password'),
            'del' => 'no',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return User::findOrFail($id);
    }

    public function test_expenses_page_renders_for_branch_user(): void
    {
        $response = $this->withSession(['date_today' => '2026-07-16', 'sales_permit' => 1])
            ->actingAs($this->branchUser)
            ->get('/expenses');

        $response->assertOk();
        $response->assertSee('Expenditure');
        $response->assertSee('Add expense');
        $response->assertSee('/maindir/css/dash-expenses.css', false);
    }

    public function test_branch_user_can_create_expense(): void
    {
        $response = $this->withSession(['date_today' => '2026-07-16', 'sales_permit' => 1])
            ->actingAs($this->branchUser)
            ->post('/expenses', [
                'title' => 'Fuel',
                'desc' => 'Delivery run',
                'expense_cost' => 150,
            ]);

        $response->assertRedirect(route('expenses.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('expenses', [
            'title' => 'Fuel',
            'companybranch_id' => '1',
            'expense_cost' => '150',
            'del' => 'no',
        ]);
    }

    public function test_branch_user_can_delete_own_branch_expense(): void
    {
        $expenseId = DB::table('expenses')->insertGetId([
            'user_id' => (string) $this->branchUser->id,
            'companybranch_id' => '1',
            'title' => 'Old expense',
            'desc' => 'Test',
            'expense_cost' => '50',
            'del' => 'no',
            'created_at' => '2026-07-16 10:00:00',
            'updated_at' => '2026-07-16 10:00:00',
        ]);

        $response = $this->withSession(['date_today' => '2026-07-16', 'sales_permit' => 1])
            ->actingAs($this->branchUser)
            ->delete('/expenses/'.$expenseId);

        $response->assertRedirect(route('expenses.index'));

        $this->assertSame('yes', Expense::findOrFail($expenseId)->del);
    }
}
