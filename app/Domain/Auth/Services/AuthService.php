<?php

namespace App\Domain\Auth\Services;

use App\Domain\Auth\DTOs\LoginUserData;
use App\Domain\Auth\DTOs\RegisterUserData;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function register(RegisterUserData $data): User
    {
        return User::create([
            'name' => $data->name,
            'email' => $data->email,
            'password' => $data->password,
            'role' => $data->role,
        ]);
    }

    public function attemptLogin(LoginUserData $data): User
    {
        $user = User::where('email', $data->email)->first();

        if (! $user || ! Hash::check($data->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['These credentials do not match our records.'],
            ]);
        }

        if ($data->requiredRole && $user->role !== $data->requiredRole) {
            throw ValidationException::withMessages([
                'email' => ['You are not authorized to access this area.'],
            ]);
        }

        return $user;
    }

    public function loginSession(LoginUserData $data, bool $remember = false): User
    {
        $user = $this->attemptLogin($data);

        Auth::login($user, $remember);

        return $user;
    }

    public function createToken(User $user, string $name = 'api'): string
    {
        return $user->createToken($name)->plainTextToken;
    }

    public function logoutSession(): void
    {
        Auth::logout();
    }

    public function revokeTokens(User $user): void
    {
        $user->tokens()->delete();
    }
}
