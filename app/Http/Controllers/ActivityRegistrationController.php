<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\ActivityRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class ActivityRegistrationController extends Controller
{
    /**
     * Handle registration for an activity for both authenticated users and guests.
     */
    public function register(Request $request, string $slug)
    {
        // Find activity by slug (generated from title)
        $activity = Activity::all()->first(function ($activity) use ($slug) {
            return \Illuminate\Support\Str::slug($activity->title) === $slug;
        });

        if (! $activity) {
            abort(404);
        }

        // Determine email to use for duplicate checking
        $user = Auth::user();
        $emailForCheck = $user?->email ?? $request->input('email');

        if (! $emailForCheck) {
            throw ValidationException::withMessages([
                'email' => 'Email address is required for registration.',
            ]);
        }

        // Prevent duplicate registrations for the same activity by email
        $existing = ActivityRegistration::where('activity_id', $activity->id)
            ->where('email', $emailForCheck)
            ->exists();

        if ($existing) {
            return redirect()
                ->route('events.show', $slug)
                ->with('error', 'You are already registered for this activity using this email address.');
        }

        if ($user) {
            return $this->registerAuthenticatedUser($activity, $user);
        }

        return $this->registerGuest($request, $activity);
    }

    /**
     * Register an authenticated user using their profile information.
     */
    protected function registerAuthenticatedUser(Activity $activity, $user)
    {
        // Compute age if not set on profile
        $age = $user->age;
        if (! $age && $user->date_of_birth) {
            $age = Carbon::parse($user->date_of_birth)->age;
        }

        ActivityRegistration::create([
            'activity_id' => $activity->id,
            'user_id' => $user->id,
            'first_name' => $user->first_name,
            'middle_initial' => $user->middle_initial,
            'last_name' => $user->last_name,
            'suffix' => $user->suffix,
            'gender' => $user->gender,
            'date_of_birth' => $user->date_of_birth,
            'age' => $age,
            'is_pwd' => (bool) $user->is_pwd,
            'requires_assistance' => $user->requires_assistance,
            'office' => $user->office,
            'position' => $user->position,
            'lgu_organization' => $user->lgu_organization,
            'contact_number' => $user->contact_number,
            'email' => $user->email,
            'dietary_restrictions' => $user->dietary_restrictions,
            'registration_type' => 'user',
        ]);

        return redirect()
            ->route('events.show', \Illuminate\Support\Str::slug($activity->title))
            ->with('success', 'You have been successfully registered for this activity using your CAPDEVhub profile information.');
    }

    /**
     * Register a guest participant using a one-time form with captcha.
     */
    protected function registerGuest(Request $request, Activity $activity)
    {
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
            'email' => ['required', 'string', 'email', 'max:255'],
            'dietary_restrictions' => ['nullable', 'string', 'max:500'],
            'captcha' => ['required', 'string'],
        ], [
            'date_of_birth.before' => 'Date of birth must be in the past.',
            'date_of_birth.after' => 'Date of birth must be after 1900.',
            'requires_assistance.required_if' => 'Please specify if you require assistance.',
            'contact_number.regex' => 'Please enter a valid contact number.',
        ]);

        $data = $validator->validate();

        // Validate captcha (case-insensitive)
        $storedCaptcha = $request->session()->get('captcha_text');
        $request->session()->forget('captcha_text');

        if (! $storedCaptcha || strtoupper($data['captcha']) !== strtoupper($storedCaptcha)) {
            return back()
                ->withErrors(['captcha' => 'The security code is incorrect. Please try again.'])
                ->withInput($request->except('captcha'));
        }

        // Compute age from date of birth
        $dateOfBirth = Carbon::parse($data['date_of_birth']);
        $age = $dateOfBirth->age;

        ActivityRegistration::create([
            'activity_id' => $activity->id,
            'user_id' => null,
            'first_name' => $data['first_name'],
            'middle_initial' => $data['middle_initial'] ?? null,
            'last_name' => $data['last_name'],
            'suffix' => $data['suffix'] ?? null,
            'gender' => $data['gender'],
            'date_of_birth' => $data['date_of_birth'],
            'age' => $age,
            'is_pwd' => $data['is_pwd'] === '1',
            'requires_assistance' => $data['is_pwd'] === '1'
                ? ($data['requires_assistance'] === '1')
                : null,
            'office' => $data['office'],
            'position' => $data['position'],
            'lgu_organization' => $data['lgu_organization'],
            'contact_number' => $data['contact_number'],
            'email' => $data['email'],
            'dietary_restrictions' => $data['dietary_restrictions'] ?? null,
            'registration_type' => 'guest',
        ]);

        return redirect()
            ->route('events.show', \Illuminate\Support\Str::slug($activity->title))
            ->with('success', 'You have been successfully registered for this activity.');
    }
}

