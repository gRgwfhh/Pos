<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_account_creation_and_password_reset_are_audited_without_password_values(): void
    {
        $admin = $this->userWithRole('admin');
        $cashierRole = Role::firstOrCreate(['name' => 'cashier']);

        $this->actingAs($admin)->post('/users', [
            'username' => 'new-cashier',
            'role_id' => $cashierRole->id,
            'password' => 'InitialPass123!',
            'password_confirmation' => 'InitialPass123!',
        ])->assertRedirect('/users');

        $cashier = User::where('username', 'new-cashier')->firstOrFail();
        $this->assertDatabaseHas('audit_logs', [
            'actor_id' => $admin->id,
            'actor_username' => $admin->username,
            'event' => 'account.created',
            'subject_id' => $cashier->id,
        ]);

        $this->put("/users/{$cashier->id}", [
            'username' => $cashier->username,
            'role_id' => $cashierRole->id,
            'password' => 'ResetPass456!',
            'password_confirmation' => 'ResetPass456!',
        ])->assertRedirect('/users');

        $passwordReset = AuditLog::where('event', 'account.password_reset')->firstOrFail();
        $this->assertSame(['username' => 'new-cashier'], $passwordReset->metadata);
        $this->assertStringNotContainsString('ResetPass456!', json_encode(AuditLog::all()->toArray()));
    }

    public function test_audit_log_is_visible_to_admin_and_forbidden_to_cashiers(): void
    {
        $admin = $this->userWithRole('admin');
        $cashier = $this->userWithRole('cashier');
        AuditLog::create([
            'actor_id' => $cashier->id,
            'actor_username' => $cashier->username,
            'event' => 'inventory.product_updated',
            'description' => 'Updated product Rice - Kilo.',
            'metadata' => ['before' => ['stock' => '10.00'], 'after' => ['stock' => '8.00']],
            'occurred_at' => now(),
        ]);

        $this->actingAs($admin)->get('/audit-logs')
            ->assertOk()
            ->assertSee($cashier->username)
            ->assertSee('inventory.product_updated')
            ->assertSee('Updated product Rice - Kilo.')
            ->assertSee('View recorded details');

        $this->actingAs($cashier)->get('/audit-logs')->assertForbidden();
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
