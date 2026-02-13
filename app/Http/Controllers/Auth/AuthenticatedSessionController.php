<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        \Log::info('Logout process started');
        \Log::info('User ID: ' . $request->user()?->id);
        \Log::info('Session ID: ' . session()->getId());
        \Log::info('Request method: ' . $request->method());
        \Log::info('Request headers: ', $request->headers->all());
        
        try {
            Auth::guard('web')->logout();
            \Log::info('User logged out successfully');

            $request->session()->invalidate();
            \Log::info('Session invalidated');

            $request->session()->regenerateToken();
            \Log::info('CSRF token regenerated');

            \Log::info('Redirecting to root');
            return redirect('/');
        } catch (\Exception $e) {
            \Log::error('Logout error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            throw $e;
        }
    }
}
