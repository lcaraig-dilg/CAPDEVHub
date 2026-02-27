<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Show the login form.
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function showLoginForm(Request $request)
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        // Optional redirect back to a specific page after successful login
        $redirect = $request->query('redirect');
        
        return view('auth.login', [
            'redirect' => $redirect,
        ]);
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(Request $request)
    {
        // Rate limiting - 5 attempts per minute
        $key = 'login.' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            throw ValidationException::withMessages([
                'email' => "Too many login attempts. Please try again in {$seconds} seconds.",
            ]);
        }

        $credentials = $request->validate([
            'email' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        // Attempt login with email or username
        $loginField = filter_var($credentials['email'], FILTER_VALIDATE_EMAIL) 
            ? 'email' 
            : 'username';
        
        $attemptCredentials = [
            $loginField => $credentials['email'],
            'password' => $credentials['password'],
        ];

        if (Auth::attempt($attemptCredentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            RateLimiter::clear($key);

            // Prefer explicit redirect parameter if provided (e.g. from event registration flow)
            if ($request->filled('redirect')) {
                return redirect()->to($request->input('redirect'));
            }
            
            return redirect()->intended(route('dashboard'));
        }

        RateLimiter::hit($key);

        throw ValidationException::withMessages([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
