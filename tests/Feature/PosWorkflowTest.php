<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Role;
use App\Models\SackSize;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PosWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_sign_in_and_open_admin_pages(): void
    {
        $admin = $this->userWithRole('admin');

        $this->post('/login', [
            'username' => $admin->username,
            'password' => 'password',
        ])->assertRedirect('/dashboard');

        $this->assertDatabaseHas('audit_logs', [
            'actor_id' => $admin->id,
            'event' => 'auth.login',
        ]);
        $this->get('/dashboard')->assertOk();
        $this->get('/users')->assertOk();
        $this->get('/sales')->assertOk();
        $this->post('/logout')->assertRedirect('/login');
        $this->assertDatabaseHas('audit_logs', [
            'actor_id' => $admin->id,
            'event' => 'auth.logout',
        ]);
    }

    public function test_cashier_can_manage_inventory_but_not_admin_pages(): void
    {
        $cashier = $this->userWithRole('cashier');
        $sackSize = SackSize::create(['weight_kg' => 25]);

        $this->actingAs($cashier)->get('/products')->assertOk();
        $this->get('/pos')->assertOk();
        $this->post('/products', [
            'name' => 'Chicken Feed - Kilo',
            'type' => 'chicken feed',
            'unit' => 'kilo',
            'sack_size_id' => $sackSize->id,
            'price' => 40,
            'stock' => 15,
        ])->assertRedirect('/products');
        $this->assertDatabaseHas('products', [
            'name' => 'Chicken Feed - Kilo',
            'stock' => 15,
            'sack_size_id' => $sackSize->id,
        ]);
        $this->assertDatabaseHas('audit_logs', [
            'actor_id' => $cashier->id,
            'event' => 'inventory.product_created',
        ]);
        $this->get('/users')->assertForbidden();
        $this->get('/sales')->assertForbidden();
    }

    public function test_checkout_records_sale_and_deducts_stock(): void
    {
        $cashier = $this->userWithRole('cashier');
        $sackSize = SackSize::create(['weight_kg' => 25]);
        $product = Product::create([
            'name' => 'Rice - Kilo',
            'type' => 'rice',
            'unit' => 'kilo',
            'sack_size_id' => $sackSize->id,
            'price' => 55.50,
            'stock' => 12,
        ]);

        $this->actingAs($cashier)->post('/pos/checkout', [
            'items' => [
                ['product_id' => $product->id, 'quantity' => 2.5],
            ],
        ])->assertRedirect('/pos');

        $this->assertDatabaseHas('sales', [
            'user_id' => $cashier->id,
            'product_id' => $product->id,
            'quantity' => 2.5,
            'unit_price' => 55.5,
            'total_price' => 138.75,
        ]);
        $this->assertDatabaseHas('audit_logs', [
            'actor_id' => $cashier->id,
            'event' => 'sale.completed',
        ]);
        $this->assertSame('9.50', $product->fresh()->stock);
    }

    public function test_checkout_rejects_insufficient_stock_without_partial_sale(): void
    {
        $cashier = $this->userWithRole('cashier');
        $product = Product::create([
            'name' => 'Dog Food - Sack',
            'type' => 'dog food',
            'unit' => 'sack',
            'price' => 900,
            'stock' => 1,
        ]);

        $this->actingAs($cashier)->from('/pos')->post('/pos/checkout', [
            'items' => [
                ['product_id' => $product->id, 'quantity' => 2],
            ],
        ])->assertSessionHasErrors('items');

        $this->assertDatabaseCount('sales', 0);
        $this->assertSame('1.00', $product->fresh()->stock);
    }

    private function userWithRole(string $roleName): User
    {
        $role = Role::firstOrCreate(['name' => $roleName]);

        return User::factory()->create([
            'username' => $roleName.'-'.uniqid(),
            'password' => 'password',
            'role_id' => $role->id,
        ]);
    }
}
