<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function show(): View
    {
        return view('superadmin.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($data, $request->boolean('remember'))) {
            return back()
                ->withErrors(['email' => 'Invalid credentials.'])
                ->onlyInput('email');
        }

        $user = Auth::user();
        if (! $user->is_superadmin) {
            Auth::logout();
            return back()->withErrors(['email' => 'This account is not a superadmin.']);
        }
        if (! $user->is_active) {
            Auth::logout();
            return back()->withErrors(['email' => 'This account is disabled.']);
        }

        $request->session()->regenerate();
        return redirect()->intended(route('superadmin.dashboard'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('superadmin.login');
    }
}
