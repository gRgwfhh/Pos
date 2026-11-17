<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Product;
use App\Models\Role;
use App\Models\SackSize;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_cashier_can_assign_a_sack_size_to_a_kilo_product(): void
    {
        $cashier = $this->userWithRole('cashier');
        $sackSize = SackSize::create(['weight_kg' => 25]);

        $this->actingAs($cashier)->get('/products/create')
            ->assertOk()
            ->assertSee('25 kg sack');

        $this->post('/products', [
            'name' => 'Premium Rice - Kilo',
            'type' => 'rice',
            'unit' => 'kilo',
            'sack_size_id' => $sackSize->id,
            'price' => 55,
            'stock' => 20,
        ])->assertRedirect('/products');

        $this->assertDatabaseHas('products', [
            'name' => 'Premium Rice - Kilo',
            'unit' => 'kilo',
            'sack_size_id' => $sackSize->id,
        ]);
        $this->assertDatabaseHas('audit_logs', [
            'actor_id' => $cashier->id,
            'event' => 'inventory.product_created',
        ]);
    }

    public function test_kilo_product_requires_a_managed_sack_size(): void
    {
        $cashier = $this->userWithRole('cashier');

        $this->actingAs($cashier)->from('/products/create')->post('/products', [
            'name' => 'Premium Rice - Kilo',
            'type' => 'rice',
            'unit' => 'kilo',
            'price' => 55,
            'stock' => 20,
        ])->assertSessionHasErrors('sack_size_id');

        $this->assertDatabaseCount('products', 0);
    }

    public function test_sack_product_does_not_keep_a_kilo_sack_size_assignment(): void
    {
        $cashier = $this->userWithRole('cashier');
        $sackSize = SackSize::create(['weight_kg' => 50]);

        $this->actingAs($cashier)->post('/products', [
            'name' => 'Premium Rice - Sack',
            'type' => 'rice',
            'unit' => 'sack',
            'sack_size_id' => $sackSize->id,
            'price' => 2500,
            'stock' => 10,
        ])->assertRedirect('/products');

        $product = Product::where('name', 'Premium Rice - Sack')->firstOrFail();
        $this->assertNull($product->sack_size_id);
    }

    public function test_product_stock_and_price_changes_are_audited(): void
    {
        $cashier = $this->userWithRole('cashier');
        $product = Product::create([
            'name' => 'Rice - Sack',
            'type' => 'rice',
            'unit' => 'sack',
            'price' => 2500,
            'stock' => 12,
        ]);

        $this->actingAs($cashier)->put("/products/{$product->id}", [
            'name' => 'Rice - Sack',
            'type' => 'rice',
            'unit' => 'sack',
            'price' => 2600,
            'stock' => 8,
        ])->assertRedirect('/products');

        $auditLog = AuditLog::where('event', 'inventory.product_updated')->firstOrFail();
        $this->assertSame('12.00', $auditLog->metadata['before']['stock']);
        $this->assertSame('8.00', $auditLog->metadata['after']['stock']);
        $this->assertSame('2500.00', $auditLog->metadata['before']['price']);
        $this->assertSame('2600.00', $auditLog->metadata['after']['price']);
    }

    public function test_product_deletion_is_audited(): void
    {
        $cashier = $this->userWithRole('cashier');
        $product = Product::create([
            'name' => 'Dog Food - Sack',
            'type' => 'dog food',
            'unit' => 'sack',
            'price' => 900,
            'stock' => 3,
        ]);

        $this->actingAs($cashier)->delete("/products/{$product->id}")
            ->assertRedirect('/products');

        $this->assertDatabaseHas('audit_logs', [
            'actor_id' => $cashier->id,
            'event' => 'inventory.product_deleted',
            'subject_id' => $product->id,
        ]);
    }

    private function userWithRole(string $roleName): User
    {
        $role = Role::firstOrCreate(['name' => $roleName]);

        return User::factory()->create([
            'username' => $roleName.'-'.uniqid(),
            'role_id' => $role->id,
        ]);
    }
}
