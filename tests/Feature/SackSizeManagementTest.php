<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Role;
use App\Models\SackSize;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SackSizeManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_update_and_delete_sack_sizes(): void
    {
        $admin = $this->userWithRole('admin');

        $this->actingAs($admin)->get('/sack-sizes')->assertOk();

        $this->actingAs($admin)->post('/sack-sizes', ['weight_kg' => 30])
            ->assertRedirect('/sack-sizes');

        $sackSize = SackSize::where('weight_kg', 30)->firstOrFail();
        $this->assertDatabaseHas('sack_sizes', ['id' => $sackSize->id, 'weight_kg' => 30]);
        $this->assertDatabaseHas('audit_logs', [
            'actor_id' => $admin->id,
            'event' => 'inventory.sack_size_created',
        ]);

        $this->put("/sack-sizes/{$sackSize->id}", ['weight_kg' => 35])
            ->assertRedirect('/sack-sizes');

        $this->assertDatabaseHas('sack_sizes', ['id' => $sackSize->id, 'weight_kg' => 35]);
        $this->assertDatabaseHas('audit_logs', [
            'actor_id' => $admin->id,
            'event' => 'inventory.sack_size_updated',
        ]);

        $this->delete("/sack-sizes/{$sackSize->id}")
            ->assertRedirect('/sack-sizes');

        $this->assertDatabaseMissing('sack_sizes', ['id' => $sackSize->id]);
        $this->assertDatabaseHas('audit_logs', [
            'actor_id' => $admin->id,
            'event' => 'inventory.sack_size_deleted',
        ]);
    }

    public function test_cashier_cannot_manage_sack_sizes(): void
    {
        $cashier = $this->userWithRole('cashier');
        $sackSize = SackSize::create(['weight_kg' => 10]);

        $this->actingAs($cashier)->get('/sack-sizes')->assertForbidden();
        $this->post('/sack-sizes', ['weight_kg' => 15])->assertForbidden();
        $this->put("/sack-sizes/{$sackSize->id}", ['weight_kg' => 20])->assertForbidden();
        $this->delete("/sack-sizes/{$sackSize->id}")->assertForbidden();

        $this->assertDatabaseHas('sack_sizes', ['id' => $sackSize->id, 'weight_kg' => 10]);
    }

    public function test_admin_cannot_delete_a_sack_size_assigned_to_a_product(): void
    {
        $admin = $this->userWithRole('admin');
        $sackSize = SackSize::create(['weight_kg' => 25]);
        Product::create([
            'name' => 'Rice - Kilo',
            'type' => 'rice',
            'unit' => 'kilo',
            'sack_size_id' => $sackSize->id,
            'price' => 55,
            'stock' => 10,
        ]);

        $this->actingAs($admin)->delete("/sack-sizes/{$sackSize->id}")
            ->assertSessionHasErrors('sack_size');

        $this->assertDatabaseHas('sack_sizes', ['id' => $sackSize->id]);
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
