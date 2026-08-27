<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View { return view('auth.login'); }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate(['email' => ['required','email'], 'password' => ['required','string']]);
        if (!Auth::attempt($credentials, $request->boolean('remember'))) return back()->withErrors(['email' => 'Email atau password tidak sesuai.'])->onlyInput('email');
        $request->session()->regenerate();
        $user = Auth::user();
        if (!$user->is_active || (!$user->isAdmin() && !$user->location_id)) {
            Auth::logout(); $request->session()->invalidate();
            return back()->withErrors(['email' => 'Akun petugas belum memiliki lokasi atau sedang dinonaktifkan.']);
        }
        return redirect()->intended(route('admin.dashboard'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout(); $request->session()->invalidate(); $request->session()->regenerateToken();
        return redirect()->route('login')->with('success', 'Anda telah keluar dari sistem.');
    }
}
