<?php

use App\Enums\UserRole;
use App\Models\User;
use Database\Seeders\CatalogSeeder;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    $this->seed(CatalogSeeder::class);
});

it('shows admin login page', function () {
    $this->get(route('admin.login'))->assertOk();
});

it('allows admin to access dashboard with session', function () {
    $admin = User::factory()->create([
        'email' => 'admin@example.com',
        'password' => Hash::make('password'),
        'role' => UserRole::Admin,
    ]);

    $this->actingAs($admin)
        ->get(route('admin.dashboard'))
        ->assertOk();
});

it('forbids customer from admin dashboard', function () {
    $customer = User::factory()->create(['role' => UserRole::Customer]);

    $this->actingAs($customer)
        ->get(route('admin.dashboard'))
        ->assertRedirect(route('admin.login'));
});

it('logs in admin via form', function () {
    User::factory()->create([
        'email' => 'admin@example.com',
        'password' => Hash::make('password'),
        'role' => UserRole::Admin,
    ]);

    $this->post(route('admin.login'), [
        'email' => 'admin@example.com',
        'password' => 'password',
    ])->assertRedirect(route('admin.dashboard'));
});
