<?php

namespace App\Support\Store;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Cookie as SymfonyCookie;

class GuestStoreToken
{
    public const COOKIE_NAME = 'mandira_guest';

    public function resolve(Request $request): string
    {
        $token = $request->cookie(self::COOKIE_NAME);

        if (is_string($token) && strlen($token) >= 32) {
            return $token;
        }

        return Str::random(40);
    }

    public function remember(string $token): SymfonyCookie
    {
        return Cookie::make(
            name: self::COOKIE_NAME,
            value: $token,
            minutes: 60 * 24 * 365,
            path: '/',
            secure: config('session.secure'),
            httpOnly: true,
            sameSite: 'lax',
        );
    }
}
