<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Role;
use App\Models\SackSize;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'cashier']);

        foreach (['10.00', '15.00', '25.00', '50.00'] as $weight) {
            SackSize::firstOrCreate(['weight_kg' => $weight]);
        }

        User::firstOrCreate(
            ['username' => env('POS_ADMIN_USERNAME', 'admin')],
            [
                'password' => Hash::make(env('POS_ADMIN_PASSWORD', 'ChangeMe123!')),
                'role_id' => $adminRole->id,
            ],
        );

        foreach ([
            ['Rice - Kilo', 'rice', 'kilo'],
            ['Rice - Sack', 'rice', 'sack'],
            ['Cat Food - Kilo', 'cat food', 'kilo'],
            ['Cat Food - Sack', 'cat food', 'sack'],
            ['Dog Food - Kilo', 'dog food', 'kilo'],
            ['Dog Food - Sack', 'dog food', 'sack'],
            ['Chicken Feed - Kilo', 'chicken feed', 'kilo'],
            ['Chicken Feed - Sack', 'chicken feed', 'sack'],
            ['Pig Feed - Kilo', 'pig feed', 'kilo'],
            ['Pig Feed - Sack', 'pig feed', 'sack'],
        ] as [$name, $type, $unit]) {
            Product::firstOrCreate(
                ['name' => $name, 'unit' => $unit],
                ['type' => $type, 'price' => 0, 'stock' => 0],
            );
        }
    }
}
