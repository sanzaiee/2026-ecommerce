<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@mandira.test'],
            [
                'name' => 'Admin User',
                'password' => 'password',
                'role' => UserRole::Admin,
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'customer@mandira.test'],
            [
                'name' => 'Demo Customer',
                'password' => 'password',
                'role' => UserRole::Customer,
                'email_verified_at' => now(),
            ]
        );

        $this->call(CatalogSeeder::class);
    }
}
