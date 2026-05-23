<?php

use App\Enums\UserRole;
use App\Models\User;
use Database\Seeders\CatalogSeeder;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    $this->seed(CatalogSeeder::class);
});

it('shows login and register pages for guests', function () {
    $this->get(route('login'))->assertOk();
    $this->get(route('register'))->assertOk();
});

it('redirects authenticated customers away from login', function () {
    $customer = User::factory()->create(['role' => UserRole::Customer]);

    $this->actingAs($customer)
        ->get(route('login'))
        ->assertRedirect(route('account'));
});

it('registers a new customer and lands on account', function () {
    $this->post(route('register'), [
        'name' => 'Jane Customer',
        'email' => 'jane@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'terms' => '1',
    ])
        ->assertRedirect(route('account'))
        ->assertSessionHas('status');

    $this->assertAuthenticated();
    expect(auth()->user())
        ->name->toBe('Jane Customer')
        ->email->toBe('jane@example.com')
        ->role->toBe(UserRole::Customer);
});

it('logs in an existing customer', function () {
    User::factory()->create([
        'email' => 'customer@example.com',
        'password' => Hash::make('password123'),
        'role' => UserRole::Customer,
    ]);

    $this->post(route('login'), [
        'email' => 'customer@example.com',
        'password' => 'password123',
    ])->assertRedirect(route('account'));

    $this->assertAuthenticatedAs(
        User::where('email', 'customer@example.com')->first()
    );
});

it('rejects invalid login credentials', function () {
    User::factory()->create([
        'email' => 'customer@example.com',
        'password' => Hash::make('password123'),
    ]);

    $this->from(route('login'))
        ->post(route('login'), [
            'email' => 'customer@example.com',
            'password' => 'wrong-password',
        ])
        ->assertRedirect(route('login'))
        ->assertSessionHasErrors('email');

    $this->assertGuest();
});

it('forbids admin credentials on customer login', function () {
    User::factory()->create([
        'email' => 'admin@example.com',
        'password' => Hash::make('password123'),
        'role' => UserRole::Admin,
    ]);

    $this->from(route('login'))
        ->post(route('login'), [
            'email' => 'admin@example.com',
            'password' => 'password123',
        ])
        ->assertRedirect(route('login'))
        ->assertSessionHasErrors('email');

    $this->assertGuest();
});

it('requires account page authentication', function () {
    $this->get(route('account'))->assertRedirect(route('login'));
});

it('shows account page for logged-in customer', function () {
    $customer = User::factory()->create(['role' => UserRole::Customer]);

    $this->actingAs($customer)
        ->get(route('account'))
        ->assertOk()
        ->assertSee($customer->name)
        ->assertSee($customer->email);
});

it('logs out customer', function () {
    $customer = User::factory()->create(['role' => UserRole::Customer]);

    $this->actingAs($customer)
        ->post(route('logout'))
        ->assertRedirect(route('login'))
        ->assertSessionHas('status');

    $this->assertGuest();
});
