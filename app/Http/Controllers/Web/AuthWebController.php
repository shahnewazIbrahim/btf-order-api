<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthWebController extends Controller
{
    public function showLoginForm()
    {
        // যদি আগে থেকেই logged in থাকে, dashboard এ পাঠাবো
        if (Auth::guard('web')->check()) {
            return redirect()->route('admin.dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::guard('web')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended(route('admin.dashboard'))
                ->with('success', 'Logged in successfully.');
        }

        return back()
            ->withInput()
            ->withErrors(['email' => 'Invalid credentials.']);
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Logged out.');
    }
}
