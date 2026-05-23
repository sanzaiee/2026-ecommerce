<?php

namespace App\Http\Requests\Auth;

use App\Domain\Auth\DTOs\LoginUserData;
use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    public function toDto(?UserRole $role = null): LoginUserData
    {
        return new LoginUserData(
            email: $this->string('email')->toString(),
            password: $this->string('password')->toString(),
            requiredRole: $role,
        );
    }
}
