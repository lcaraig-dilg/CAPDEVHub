@extends('layouts.app')

@section('title', 'My Profile - CAPDEVhub')
@section('page-title', 'My Profile')

@section('content')
<div class="bg-white shadow-lg rounded-lg p-8">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h2 class="text-3xl font-bold text-gray-900 mb-1">My Profile</h2>
            <p class="text-gray-600">Review your personal and professional information</p>
        </div>
        @if (session('success'))
            <div class="ml-4 bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded text-sm">
                {{ session('success') }}
            </div>
        @endif
    </div>

    <div class="space-y-8">
        <!-- Personal Information -->
        <section>
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Personal Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-gray-500">First Name</p>
                    <p class="font-medium text-gray-900">{{ $user->first_name }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Middle Initial</p>
                    <p class="font-medium text-gray-900">{{ $user->middle_initial ?: '—' }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Last Name</p>
                    <p class="font-medium text-gray-900">{{ $user->last_name }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Suffix</p>
                    <p class="font-medium text-gray-900">{{ $user->suffix ?: '—' }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Sex / Gender</p>
                    <p class="font-medium text-gray-900">{{ $user->gender }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Date of Birth</p>
                    <p class="font-medium text-gray-900">
                        {{ optional($user->date_of_birth)->format('F d, Y') ?? '—' }}
                    </p>
                </div>
                <div>
                    <p class="text-gray-500">Age</p>
                    <p class="font-medium text-gray-900">{{ $user->age ?? '—' }}</p>
                </div>
            </div>
        </section>

        <!-- PWD Information -->
        <section>
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Person with Disability Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-gray-500">Person with Disability</p>
                    <p class="font-medium text-gray-900">{{ $user->is_pwd ? 'Yes' : 'No' }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Requires Assistance</p>
                    <p class="font-medium text-gray-900">
                        @if ($user->is_pwd)
                            {{ $user->requires_assistance ? 'Yes' : 'No' }}
                        @else
                            —
                        @endif
                    </p>
                </div>
            </div>
        </section>

        <!-- Professional Information -->
        <section>
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Professional Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-gray-500">Office</p>
                    <p class="font-medium text-gray-900">{{ $user->office }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Position</p>
                    <p class="font-medium text-gray-900">{{ $user->position }}</p>
                </div>
                <div>
                    <p class="text-gray-500">LGU/Organization</p>
                    <p class="font-medium text-gray-900">{{ $user->lgu_organization }}</p>
                </div>
            </div>
        </section>

        <!-- Contact Information -->
        <section>
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Contact Information</h3>
            <div class="grid grid-cols-1 md-grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-gray-500">Contact Number</p>
                    <p class="font-medium text-gray-900">{{ $user->contact_number }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Email Address</p>
                    <p class="font-medium text-gray-900">{{ $user->email }}</p>
                </div>
                <div class="md:col-span-2">
                    <p class="text-gray-500">Dietary Restrictions</p>
                    <p class="font-medium text-gray-900">
                        {{ $user->dietary_restrictions ?: 'None specified' }}
                    </p>
                </div>
            </div>
        </section>
    </div>

    <div class="mt-8 flex items-center justify-between">
        <a 
            href="{{ route('dashboard') }}" 
            class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#0a7ca1] transition"
        >
            Back to Dashboard
        </a>

        @if (!method_exists($user, 'isSuperAdmin') || !$user->isSuperAdmin())
            <a 
                href="{{ route('profile.edit') }}" 
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-[#013141] hover:bg-[#0a7ca1] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#0a7ca1] transition"
            >
                Edit Information
            </a>
        @endif
    </div>
</div>
@endsection

