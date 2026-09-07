<?php

use App\Domain\CMS\Models\LandingPage;
use App\Enums\UserRole;
use App\Models\User;
use Database\Seeders\CatalogSeeder;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    $this->seed(CatalogSeeder::class);
});

it('persists meta keywords from admin landing page form', function () {
    $admin = User::factory()->create([
        'email' => 'admin@example.com',
        'password' => Hash::make('password'),
        'role' => UserRole::Admin,
    ]);

    $this->actingAs($admin)
        ->put(route('admin.landing-page.update'), [
            'meta_keywords' => 'Thimi pottery, Nepal clay pots , Newar pottery',
        ])
        ->assertRedirect(route('admin.landing-page.edit'));

    $page = LandingPage::query()->first();

    expect($page->meta_keywords)->toBe('Thimi pottery, Nepal clay pots, Newar pottery');
});

it('shows meta keywords input on admin landing page form', function () {
    $admin = User::factory()->create([
        'role' => UserRole::Admin,
    ]);

    $this->actingAs($admin)
        ->get(route('admin.landing-page.edit'))
        ->assertOk()
        ->assertSee('name="meta_keywords"', false);
});

it('forbids customer from updating landing page', function () {
    $customer = User::factory()->create(['role' => UserRole::Customer]);

    $this->actingAs($customer)
        ->put(route('admin.landing-page.update'), [
            'meta_keywords' => 'spam',
        ])
        ->assertRedirect(route('admin.login'));

    expect(LandingPage::query()->first()->meta_keywords)
        ->not->toBe('spam');
});
