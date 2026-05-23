<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsCustomer
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user?->isCustomer()) {
            return $next($request);
        }

        if ($user?->isAdmin()) {
            return redirect()
                ->route('admin.dashboard')
                ->withErrors(['email' => 'Please use a customer account to place store orders.']);
        }

        return redirect()
            ->route('login')
            ->withErrors(['email' => 'Please sign in to complete checkout.']);
    }
}
