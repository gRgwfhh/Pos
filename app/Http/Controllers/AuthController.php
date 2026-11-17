<?php

namespace App\Http\Controllers;

use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(Request $request, AuditLogger $auditLogger): RedirectResponse
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withErrors(['username' => 'The username or password is incorrect.'])
                ->onlyInput('username');
        }

        $request->session()->regenerate();
        $auditLogger->record($request->user(), 'auth.login', 'Signed in.');

        return redirect()->intended(route('dashboard'));
    }

    public function destroy(Request $request, AuditLogger $auditLogger): RedirectResponse
    {
        $auditLogger->record($request->user(), 'auth.logout', 'Signed out.');
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
