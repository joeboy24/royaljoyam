<?php

namespace Tests\Feature;

use App\Models\CompanyBranch;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class BranchDeleteGuardTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

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

        DB::table('company_branches')->insert([
            'id' => 2,
            'user_id' => '1',
            'name' => 'Branch B',
            'loc' => 'Loc 2',
            'contact' => '0000000002',
            'tag' => '2',
            'del' => 'no',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->admin = User::findOrFail(DB::table('users')->insertGetId([
            'company_branch_id' => '1',
            'name' => 'admin.branchdel',
            'email' => 'admin.branchdel@test.example',
            'bv' => 'A',
            'status' => User::STATUS_ADMINISTRATOR,
            'password' => Hash::make('password'),
            'del' => 'no',
            'created_at' => now(),
            'updated_at' => now(),
        ]));
    }

    public function test_cannot_delete_last_active_branch(): void
    {
        $second = CompanyBranch::find(2);
        $second->del = 'yes';
        $second->save();

        $branch = CompanyBranch::find(1);

        $response = $this->actingAs($this->admin)->delete('/items/'.$branch->id, [
            'del_action' => 'branch_del',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertSame('no', $branch->fresh()->del);
    }

    public function test_cannot_delete_branch_with_assigned_user(): void
    {
        DB::table('users')->insert([
            'company_branch_id' => '2',
            'name' => 'branch.staff',
            'email' => 'branch.staff@test.example',
            'bv' => '2',
            'status' => 'Branch B',
            'password' => Hash::make('password'),
            'del' => 'no',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $branch = CompanyBranch::find(2);

        $response = $this->actingAs($this->admin)->delete('/items/'.$branch->id, [
            'del_action' => 'branch_del',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertStringContainsString('user', session('error'));
        $this->assertSame('no', $branch->fresh()->del);
    }

    public function test_cannot_delete_branch_with_sales_history(): void
    {
        $saleId = DB::table('sales')->insertGetId([
            'user_id' => (string) $this->admin->id,
            'user_bv' => '2',
            'order_no' => 'ORD-1',
            'qty' => '1',
            'tot' => '12',
            'pay_mode' => 'Cash',
            'buy_name' => 'Buyer',
            'buy_contact' => '000',
            'del_status' => 'Delivered',
            'discount' => '0',
            'payment' => '12',
            'change' => '0',
            'paid' => 'Paid',
            'del' => 'no',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('sales_histories')->insert([
            'sale_id' => (string) $saleId,
            'user_id' => (string) $this->admin->id,
            'user_bv' => '2',
            'item_id' => '1',
            'item_no' => 'ITM-1',
            'name' => 'Item',
            'qty' => '1',
            'cost_price' => '10',
            'unit_price' => '12',
            'profits' => '2',
            'tot' => '12',
            'del_status' => 'Delivered',
            'del' => 'no',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $branch = CompanyBranch::find(2);

        $response = $this->actingAs($this->admin)->delete('/items/'.$branch->id, [
            'del_action' => 'branch_del',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertTrue(
            str_contains(strtolower(session('error')), 'sale')
            || str_contains(strtolower(session('error')), 'history')
        );
        $this->assertSame('no', $branch->fresh()->del);
    }

    public function test_cannot_delete_branch_with_stock(): void
    {
        DB::table('items')->insert([
            'item_no' => 'ITM-STOCK',
            'user_id' => (string) $this->admin->id,
            'name' => 'Stocked Item',
            'desc' => 'Desc',
            'cat' => 'General',
            'brand' => 'Brand',
            'barcode' => 'BC-STOCK',
            'qty' => '5',
            'price' => '10.00',
            'cost_price' => '8.00',
            'q1' => '0',
            'q2' => '5',
            'q3' => '0',
            'b1' => '10.00',
            'b2' => '10.00',
            'b3' => '10.00',
            'del' => 'no',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $branch = CompanyBranch::find(2);

        $response = $this->actingAs($this->admin)->delete('/items/'.$branch->id, [
            'del_action' => 'branch_del',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertStringContainsString('stock', session('error'));
        $this->assertSame('no', $branch->fresh()->del);
    }

    public function test_can_delete_unused_branch(): void
    {
        $branch = CompanyBranch::find(2);

        $response = $this->actingAs($this->admin)->delete('/items/'.$branch->id, [
            'del_action' => 'branch_del',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertSame('yes', $branch->fresh()->del);
    }
}
