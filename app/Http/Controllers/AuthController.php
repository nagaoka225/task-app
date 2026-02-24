<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class AuthController extends Controller
{
    public function showLogin(): View|RedirectResponse
    {
        if (session('auth_verified')) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $code = config('app.auth_code');
        if (empty($code)) {
            return back()->with('error', '認証コードが設定されていません。');
        }
        if ($request->input('auth_code') === $code) {
            session(['auth_verified' => true]);
            return redirect()->route('dashboard');
        }
        return back()->with('error', '認証コードが正しくありません。');
    }

    public function logout(Request $request): RedirectResponse
    {
        session()->forget('auth_verified');
        return redirect()->route('login');
    }
}
