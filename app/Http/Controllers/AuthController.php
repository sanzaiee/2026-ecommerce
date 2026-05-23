<?php

namespace App\Http\Controllers;

use App\Domain\Auth\DTOs\LoginUserData;
use App\Domain\Auth\Services\AuthService;
use App\Enums\UserRole;
use App\Support\Store\StoreBasketMerge;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function __construct(
        private AuthService $auth,
        private StoreBasketMerge $basketMerge,
    ) {}

    public function showLogin(): View
    {
        return view('auth.login', [
            'cartTotal' => 'Rs. 0',
        ]);
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        $user = $this->auth->loginSession($request->toDto(UserRole::Customer), $request->boolean('remember'));

        $request->session()->regenerate();
        $this->basketMerge->mergeGuestIntoUser($request, $user);

        return redirect()->intended(route('account'));
    }

    public function showRegister(): View
    {
        return view('auth.register', [
            'cartTotal' => 'Rs. 0',
        ]);
    }

    public function register(RegisterRequest $request): RedirectResponse
    {
        $data = $request->toDto();

        $this->auth->register($data);

        $user = $this->auth->loginSession(new LoginUserData(
            email: $data->email,
            password: $data->password,
            requiredRole: UserRole::Customer,
        ));

        $request->session()->regenerate();
        $this->basketMerge->mergeGuestIntoUser($request, $user);

        return redirect()
            ->intended(route('account'))
            ->with('status', 'Welcome to Mandira Foods!');
    }

    public function logout(Request $request): RedirectResponse
    {
        $this->auth->logoutSession();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect(route('login'))->with('status', 'You have been signed out.');
    }
}
