<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_low_stock_section_shows_configured_products_only(): void
    {
        $adminRole = Role::create(['name' => 'admin']);
        $admin = User::factory()->create(['role_id' => $adminRole->id]);

        Product::create([
            'name' => 'Configured low stock',
            'type' => 'rice',
            'unit' => 'kilo',
            'price' => 55,
            'stock' => 4,
        ]);
        Product::create([
            'name' => 'Configured but out of stock',
            'type' => 'dog food',
            'unit' => 'sack',
            'price' => 900,
            'stock' => 0,
        ]);
        Product::create([
            'name' => 'Unpriced starter product',
            'type' => 'pig feed',
            'unit' => 'kilo',
            'price' => 0,
            'stock' => 0,
        ]);
        Product::create([
            'name' => 'Healthy stock',
            'type' => 'cat food',
            'unit' => 'kilo',
            'price' => 135,
            'stock' => 292,
        ]);

        $response = $this->actingAs($admin)->get('/dashboard');

        $response->assertOk()
            ->assertSee('Configured low stock')
            ->assertSee('Configured but out of stock')
            ->assertSee('Available stock (more than 5)')
            ->assertSee('Healthy stock')
            ->assertDontSee('Unpriced starter product');
    }

    public function test_cashier_can_see_available_stock_on_dashboard(): void
    {
        $cashierRole = Role::create(['name' => 'cashier']);
        $cashier = User::factory()->create(['role_id' => $cashierRole->id]);
        Product::create([
            'name' => 'Available rice',
            'type' => 'rice',
            'unit' => 'kilo',
            'price' => 60,
            'stock' => 6,
        ]);

        $this->actingAs($cashier)->get('/dashboard')
            ->assertOk()
            ->assertSee('Available stock (more than 5)')
            ->assertSee('Available rice')
            ->assertDontSee('No products currently have more than 5 units in stock.');
    }
}
