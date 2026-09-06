<?php

use App\Domain\Blog\Models\Blog;
use App\Enums\UserRole;
use App\Models\User;
use App\Policies\BlogPolicy;

test('anyone may view blog posts', function (?UserRole $role) {
    $user = $role ? User::factory()->make(['role' => $role]) : null;
    $post = Blog::factory()->make();
    $policy = new BlogPolicy;

    expect($policy->viewAny($user))->toBeTrue()
        ->and($policy->view($user, $post))->toBeTrue();
})->with([
    'guest' => [null],
    'customer' => [UserRole::Customer],
    'admin' => [UserRole::Admin],
]);

test('only admins may create blog posts', function (UserRole $role, bool $allowed) {
    $user = User::factory()->make(['role' => $role]);

    expect((new BlogPolicy)->create($user))->toBe($allowed);
})->with([
    'admin' => [UserRole::Admin, true],
    'customer' => [UserRole::Customer, false],
]);

test('only admins may update and delete blog posts', function (UserRole $role, bool $allowed) {
    $user = User::factory()->make(['role' => $role]);
    $post = Blog::factory()->make();
    $policy = new BlogPolicy;

    expect($policy->update($user, $post))->toBe($allowed)
        ->and($policy->delete($user, $post))->toBe($allowed);
})->with([
    'admin' => [UserRole::Admin, true],
    'customer' => [UserRole::Customer, false],
]);
