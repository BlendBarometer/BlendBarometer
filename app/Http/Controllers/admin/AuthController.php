<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController
{
    public function index()
    {
        if (Auth::check() && Auth::user()->hasRole('admin'))
        {
            return redirect()->route('admin.edit-content');
        }

        return view('admin.login');
    }

    public function submitLogin(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['password']])) {
            $request->session()->regenerate();

            Auth::user()->assignRole('admin');

            return redirect()->route('admin.edit-content');
        }

        return back()->withErrors([
            'Incorrecte inloggegevens' => 'Inloggegevens onjuist',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
