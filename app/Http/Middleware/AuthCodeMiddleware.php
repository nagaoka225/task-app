<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthCodeMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!session('auth_verified')) {
            return redirect()->route('login')->with('error', '認証コードを入力してください。');
        }
        return $next($request);
    }
}
