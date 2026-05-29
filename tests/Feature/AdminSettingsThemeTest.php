<?php

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    $this->admin = User::factory()->create([
        'role' => UserRole::Admin,
        'password' => Hash::make('password'),
    ]);
});

it('shows configured admin theme on admin layout', function () {
    $this->actingAs($this->admin)
        ->get(route('admin.settings.edit'))
        ->assertOk()
        ->assertSee('Admin panel')
        ->assertSee('admin_color_primary', false)
        ->assertSee('name="admin_theme"', false);
});

it('persists admin theme in site settings', function () {
    $this->actingAs($this->admin)
        ->put(route('admin.settings.update'), [
            'site_name' => 'Test Store',
            'admin_theme' => 'dark',
        ])
        ->assertRedirect();

    expect(app(\App\Domain\Settings\Repositories\SiteSettingRepository::class)->getSingleton()->admin_theme)
        ->toBe('dark');

    $this->get(route('admin.dashboard'))
        ->assertOk()
        ->assertSee('data-admin-theme-default', false)
        ->assertSee('"dark"', false);
});

it('persists admin panel colors in site settings', function () {
    $this->actingAs($this->admin)
        ->put(route('admin.settings.update'), [
            'site_name' => 'Test Store',
            'admin_color_primary' => '#112233',
            'admin_color_secondary' => '#AABBCC',
            'admin_color_neutral' => '#556677',
        ])
        ->assertRedirect();

    $settings = app(\App\Domain\Settings\Repositories\SiteSettingRepository::class)->getSingleton();

    expect($settings->admin_color_primary)->toBe('#112233')
        ->and($settings->admin_color_secondary)->toBe('#AABBCC')
        ->and($settings->admin_color_neutral)->toBe('#556677');

    $this->get(route('admin.dashboard'))
        ->assertOk()
        ->assertSee('--admin-color-primary: #112233', false)
        ->assertSee('--admin-color-secondary: #AABBCC', false)
        ->assertSee('--admin-color-neutral: #556677', false);
});
