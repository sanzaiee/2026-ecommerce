<?php

use App\Domain\Settings\Repositories\SiteSettingRepositoryInterface;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->admin = User::factory()->create([
        'role' => UserRole::Admin,
        'password' => Hash::make('password'),
    ]);
});

it('keeps png format on logo header conversion so transparency is preserved', function () {
    Storage::fake('public');

    $this->actingAs($this->admin)
        ->put(route('admin.settings.update'), [
            'site_name' => 'Test Store',
            'logo' => UploadedFile::fake()->image('brand-logo.png', 640, 160),
        ])
        ->assertRedirect();

    $settings = app(SiteSettingRepositoryInterface::class)->getSingleton();
    $media = $settings->getFirstMedia('logo');

    expect($media)->not->toBeNull()
        ->and($media->mime_type)->toBe('image/png')
        ->and($media->hasGeneratedConversion('header'))->toBeTrue();

    $headerPath = $media->getPath('header');

    expect($headerPath)->toEndWith('.png')
        ->and(file_exists($headerPath))->toBeTrue();
});
