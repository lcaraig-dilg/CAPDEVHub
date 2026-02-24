<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class ProfileController extends Controller
{
    /**
     * Show the authenticated user's profile (read-only).
     */
    public function show()
    {
        $user = Auth::user();

        return view('profile.show', compact('user'));
    }

    /**
     * Show the authenticated user's profile edit form.
     */
    public function edit()
    {
        $user = Auth::user();

        // Super Admin profile is managed by the system and cannot be edited
        if (method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin()) {
            return redirect()
                ->route('dashboard')
                ->withErrors([
                    'profile' => 'Super Admin account information is managed by the system and cannot be edited.',
                ]);
        }

        return view('profile.edit', compact('user'));
    }

    /**
     * Update the authenticated user's profile information.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        // Block updates for Super Admin for security and audit reasons
        if (method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin()) {
            return redirect()
                ->route('dashboard')
                ->withErrors([
                    'profile' => 'Super Admin account information cannot be modified from the user interface.',
                ]);
        }

        // Basic validation (similar to registration, but allow current email)
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
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'dietary_restrictions' => ['nullable', 'string', 'max:500'],
        ], [
            'date_of_birth.before' => 'Date of birth must be in the past.',
            'date_of_birth.after' => 'Date of birth must be after 1900.',
            'requires_assistance.required_if' => 'Please specify if you require assistance.',
            'contact_number.regex' => 'Please enter a valid contact number.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Calculate age from date of birth
        $dateOfBirth = Carbon::parse($request->date_of_birth);
        $age = $dateOfBirth->age;

        // Update user fields
        $user->first_name = $request->first_name;
        $user->middle_initial = $request->middle_initial ? strtoupper($request->middle_initial) : null;
        $user->last_name = $request->last_name;
        $user->suffix = $request->suffix ?: null;
        $user->gender = $request->gender;
        $user->date_of_birth = $request->date_of_birth;
        $user->age = $age;
        $user->is_pwd = $request->input('is_pwd') == '1';
        $user->requires_assistance = $request->input('is_pwd') == '1'
            ? ($request->input('requires_assistance') == '1')
            : null;
        $user->office = $request->office;
        $user->position = $request->position;
        $user->lgu_organization = $request->lgu_organization;
        $user->contact_number = $request->contact_number;
        $user->email = $request->email;
        $user->dietary_restrictions = $request->dietary_restrictions;

        // Keep role and username unchanged, but recompute display name
        $user->name = trim(
            $user->first_name . ' ' .
            ($user->middle_initial ? $user->middle_initial . '. ' : '') .
            $user->last_name .
            ($user->suffix ? ' ' . $user->suffix : '')
        );

        $user->save();

        return redirect()
            ->route('profile.show')
            ->with('success', 'Your information has been updated successfully.');
    }
}

