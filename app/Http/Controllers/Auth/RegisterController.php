<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class RegisterController extends Controller
{
    /**
     * Show the registration form.
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function showRegistrationForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        
        return view('auth.register');
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function register(Request $request)
    {
        // Rate limiting - 3 registrations per hour per IP
        $key = 'register.' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);
            throw ValidationException::withMessages([
                'email' => "Too many registration attempts. Please try again in " . ceil($seconds / 60) . " minutes.",
            ]);
        }

        // Honeypot field for bot protection
        if ($request->filled('website')) {
            // Bot detected - silently fail
            return redirect()->route('register')->with('success', 'Registration successful!');
        }

        // Debug: Log all incoming request data
        \Log::info('Registration attempt', ['data' => $request->all()]);
        
        // Ensure all required fields are present
        $missingFields = [];
        $requiredFields = ['first_name', 'last_name', 'gender', 'date_of_birth', 'is_pwd', 'office', 'position', 'lgu_organization', 'contact_number', 'email', 'username', 'password'];
        
        foreach ($requiredFields as $field) {
            if (!$request->has($field) || $request->input($field) === null || $request->input($field) === '') {
                $missingFields[] = $field;
            }
        }
        
        if (!empty($missingFields)) {
            return back()->withErrors(['form' => 'Missing required fields: ' . implode(', ', $missingFields)])->withInput($request->all());
        }

        $validator = Validator::make($request->all(), [
            'first_name' => ['required', 'string', 'max:255'],
            'middle_initial' => ['nullable', 'string', 'size:1', 'regex:/^[A-Za-z]$/'],
            'last_name' => ['required', 'string', 'max:255'],
            'suffix' => ['nullable', 'string', 'max:10'],
            'gender' => ['required', 'in:Male,Female,Prefer not to say'],
            'date_of_birth' => ['required', 'date', 'before:today', 'after:1900-01-01'],
            'is_pwd' => ['required', 'in:0,1'],
            'requires_assistance' => ['required_if:is_pwd,1', 'in:0,1'],
            'office' => ['required', 'string', 'max:255'],
            'position' => ['required', 'string', 'max:255'],
            'lgu_organization' => ['required', 'string', 'max:255'],
            'contact_number' => ['required', 'string', 'max:20', 'regex:/^[0-9+\-() ]+$/'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'username' => ['required', 'string', 'max:255', 'unique:users'],
            'dietary_restrictions' => ['nullable', 'string', 'max:500'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'captcha' => ['required', 'string'],
        ], [
            'date_of_birth.before' => 'Date of birth must be in the past.',
            'date_of_birth.after' => 'Date of birth must be after 1900.',
            'requires_assistance.required_if' => 'Please specify if you require assistance.',
            'password.min' => 'Password must be at least 8 characters.',
            'password.confirmed' => 'Password confirmation does not match.',
            'contact_number.regex' => 'Please enter a valid contact number.',
        ]);

        if ($validator->fails()) {
            RateLimiter::hit($key);
            return back()->withErrors($validator)->withInput($request->all());
        }

        // Validate captcha (case-insensitive, same as event registration)
        $storedCaptcha = $request->session()->get('captcha_text');
        $request->session()->forget('captcha_text');

        if (! $storedCaptcha || strtoupper($request->input('captcha')) !== strtoupper($storedCaptcha)) {
            RateLimiter::hit($key);
            return back()
                ->withErrors(['captcha' => 'The security code is incorrect. Please try again.'])
                ->withInput($request->except('captcha'));
        }

        // Additional validation - ensure all required fields are actually present in the request
        $requiredFields = [
            'first_name', 'last_name', 'gender', 'date_of_birth', 
            'is_pwd', 'office', 'position', 'lgu_organization', 
            'contact_number', 'email', 'username', 'password'
        ];
        
        $missingFields = [];
        foreach ($requiredFields as $field) {
            if (!$request->has($field) || $request->input($field) === null || $request->input($field) === '') {
                $missingFields[] = $field;
            }
        }
        
        if (!empty($missingFields)) {
            \Log::error('Missing required fields in registration', [
                'missing' => $missingFields,
                'all_request_data' => $request->all()
            ]);
            return back()->withErrors(['form' => 'Missing required fields: ' . implode(', ', $missingFields)])->withInput($request->all());
        }

        // Calculate age
        $dateOfBirth = Carbon::parse($request->date_of_birth);
        $age = $dateOfBirth->age;

        // Create user with all fields
        // Note: password will be automatically hashed by Laravel's 'hashed' cast
        $userData = [
            'first_name' => $request->input('first_name'),
            'middle_initial' => $request->filled('middle_initial') ? strtoupper($request->input('middle_initial')) : '',
            'last_name' => $request->input('last_name'),
            'suffix' => $request->input('suffix'),
            'gender' => $request->input('gender'),
            'date_of_birth' => $request->input('date_of_birth'),
            'age' => $age,
            'is_pwd' => $request->input('is_pwd') == '1',
            'requires_assistance' => $request->input('is_pwd') == '1' ? ($request->input('requires_assistance') == '1') : null,
            'office' => $request->input('office'),
            'position' => $request->input('position'),
            'lgu_organization' => $request->input('lgu_organization'),
            'contact_number' => $request->input('contact_number'),
            'email' => $request->input('email'),
            'dietary_restrictions' => $request->input('dietary_restrictions'),
            'password' => $request->input('password'), // Let Laravel's 'hashed' cast handle hashing
            'role' => 'user',
            'username' => $request->input('username'),
            'name' => trim($request->input('first_name') . ' ' . ($request->filled('middle_initial') ? $request->input('middle_initial') . '. ' : '') . $request->input('last_name') . ($request->filled('suffix') ? ' ' . $request->input('suffix') : '')),
        ];
        
        // Log the data being inserted for debugging
        \Log::info('Creating user with data', ['fields' => array_keys($userData), 'has_first_name' => isset($userData['first_name'])]);
        
        try {
            // Use forceFill to ensure all fields are inserted, bypassing mass assignment
            $user = new User();
            $user->forceFill($userData);
            $user->save();
        } catch (\Exception $e) {
            \Log::error('Registration error: ' . $e->getMessage());
            \Log::error('Request data: ' . json_encode($request->all()));
            \Log::error('User data array: ' . json_encode($userData));
            return back()->withErrors(['form' => 'Registration failed: ' . $e->getMessage()])->withInput($request->all());
        }

        RateLimiter::clear($key);

        Auth::login($user);

        return redirect()->route('dashboard')->with('success', 'Registration successful! Welcome to CAPDEVhub.');
    }
}
