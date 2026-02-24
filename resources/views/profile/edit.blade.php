@extends('layouts.app')

@section('title', 'Edit Profile - CAPDEVhub')
@section('page-title', 'Edit Profile')

@section('content')
<div class="bg-white shadow-lg rounded-lg p-8">
    <div class="text-center mb-8">
        <h2 class="text-3xl font-bold text-gray-900 mb-2">My Profile</h2>
        <p class="text-gray-600">Review and update your personal information</p>
    </div>

    <form method="POST" action="{{ route('profile.update') }}" class="space-y-6" id="profileForm">
        @csrf
        @method('PUT')

        <!-- Personal Information Section -->
        <div class="border-b border-gray-200 pb-4">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Personal Information</h3>

            <div class="mb-4 p-3 bg-amber-50 border-l-4 border-amber-500 rounded-r-lg">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-amber-600 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3 flex-1">
                        <p class="text-sm text-amber-800 leading-relaxed">
                            <strong>Reminder:</strong> Your personal information here will be used for event certificates. Make sure your name and details are correct, as they will appear on official certificates.
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">
                        First Name <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="first_name" 
                        name="first_name" 
                        value="{{ old('first_name', $user->first_name) }}"
                        required 
                        class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('first_name') border-red-500 @enderror"
                    >
                    @error('first_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="middle_initial" class="block text-sm font-medium text-gray-700 mb-2">
                        Middle Initial
                    </label>
                    <input 
                        type="text" 
                        id="middle_initial" 
                        name="middle_initial" 
                        value="{{ old('middle_initial', $user->middle_initial) }}"
                        maxlength="1"
                        pattern="[A-Za-z]"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('middle_initial') border-red-500 @enderror"
                        style="text-transform: uppercase;"
                    >
                    @error('middle_initial')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">
                        Last Name <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="last_name" 
                        name="last_name" 
                        value="{{ old('last_name', $user->last_name) }}"
                        required 
                        class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('last_name') border-red-500 @enderror"
                    >
                    @error('last_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="suffix" class="block text-sm font-medium text-gray-700 mb-2">
                        Suffix (Optional)
                    </label>
                    <input 
                        type="text" 
                        id="suffix" 
                        name="suffix" 
                        value="{{ old('suffix', $user->suffix) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('suffix') border-red-500 @enderror"
                        placeholder="Jr., Sr., III, etc."
                    >
                    @error('suffix')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                <div>
                    <label for="gender" class="block text-sm font-medium text-gray-700 mb-2">
                        Sex / Gender <span class="text-red-500">*</span>
                    </label>
                    <select 
                        id="gender" 
                        name="gender" 
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('gender') border-red-500 @enderror"
                    >
                        @php
                            $genderValue = old('gender', $user->gender);
                        @endphp
                        <option value="">Select Gender</option>
                        <option value="Male" {{ $genderValue === 'Male' ? 'selected' : '' }}>Male</option>
                        <option value="Female" {{ $genderValue === 'Female' ? 'selected' : '' }}>Female</option>
                        <option value="Prefer not to say" {{ $genderValue === 'Prefer not to say' ? 'selected' : '' }}>Prefer not to say</option>
                    </select>
                    @error('gender')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="date_of_birth" class="block text-sm font-medium text-gray-700 mb-2">
                        Date of Birth <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="date" 
                        id="date_of_birth" 
                        name="date_of_birth" 
                        value="{{ old('date_of_birth', optional($user->date_of_birth)->format('Y-m-d')) }}"
                        required 
                        max="{{ date('Y-m-d', strtotime('-1 day')) }}"
                        min="1900-01-01"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('date_of_birth') border-red-500 @enderror"
                    >
                    @error('date_of_birth')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="age" class="block text-sm font-medium text-gray-700 mb-2">
                        Age (Auto-computed)
                    </label>
                    <input 
                        type="number" 
                        id="age" 
                        name="age" 
                        readonly
                        value="{{ old('age', $user->age) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-100 text-gray-600"
                    >
                </div>
            </div>
        </div>

        <!-- PWD Information Section -->
        <div class="border-b border-gray-200 pb-4">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Person with Disability Information</h3>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Are you a person with disability? <span class="text-red-500">*</span>
                </label>
                <div class="flex space-x-6">
                    @php
                        $isPwdValue = old('is_pwd', $user->is_pwd ? '1' : '0');
                    @endphp
                    <label class="flex items-center">
                        <input 
                            type="radio" 
                            name="is_pwd" 
                            value="1" 
                            {{ $isPwdValue === '1' ? 'checked' : '' }}
                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                            onchange="toggleAssistanceField()"
                        >
                        <span class="ml-2 text-sm text-gray-700">Yes</span>
                    </label>
                    <label class="flex items-center">
                        <input 
                            type="radio" 
                            name="is_pwd" 
                            value="0" 
                            {{ $isPwdValue === '0' ? 'checked' : '' }}
                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                            onchange="toggleAssistanceField()"
                        >
                        <span class="ml-2 text-sm text-gray-700">No</span>
                    </label>
                </div>
                @error('is_pwd')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div id="assistance_field" class="mt-4" style="display: none;">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Do you require assistance? <span class="text-red-500">*</span>
                </label>
                <div class="flex space-x-6">
                    @php
                        $requiresAssistanceValue = old(
                            'requires_assistance',
                            $user->requires_assistance === null ? null : ($user->requires_assistance ? '1' : '0')
                        );
                    @endphp
                    <label class="flex items-center">
                        <input 
                            type="radio" 
                            name="requires_assistance" 
                            value="1" 
                            {{ $requiresAssistanceValue === '1' ? 'checked' : '' }}
                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                        >
                        <span class="ml-2 text-sm text-gray-700">Yes</span>
                    </label>
                    <label class="flex items-center">
                        <input 
                            type="radio" 
                            name="requires_assistance" 
                            value="0" 
                            {{ $requiresAssistanceValue === '0' ? 'checked' : '' }}
                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                        >
                        <span class="ml-2 text-sm text-gray-700">No</span>
                    </label>
                </div>
                @error('requires_assistance')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Professional Information Section -->
        <div class="border-b border-gray-200 pb-4">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Professional Information</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="office" class="block text-sm font-medium text-gray-700 mb-2">
                        Office <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="office" 
                        name="office" 
                        value="{{ old('office', $user->office) }}"
                        required 
                        class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('office') border-red-500 @enderror"
                    >
                    @error('office')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="position" class="block text-sm font-medium text-gray-700 mb-2">
                        Position <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="position" 
                        name="position" 
                        value="{{ old('position', $user->position) }}"
                        required 
                        class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('position') border-red-500 @enderror"
                    >
                    @error('position')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="lgu_organization" class="block text-sm font-medium text-gray-700 mb-2">
                        LGU/Organization <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="lgu_organization" 
                        name="lgu_organization" 
                        value="{{ old('lgu_organization', $user->lgu_organization) }}"
                        required 
                        class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('lgu_organization') border-red-500 @enderror"
                    >
                    @error('lgu_organization')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Contact Information Section -->
        <div class="border-b border-gray-200 pb-4">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Contact Information</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="contact_number" class="block text-sm font-medium text-gray-700 mb-2">
                        Contact Number <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="tel" 
                        id="contact_number" 
                        name="contact_number" 
                        value="{{ old('contact_number', $user->contact_number) }}"
                        required 
                        pattern="[0-9+\-() ]+"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('contact_number') border-red-500 @enderror"
                        placeholder="09XX XXX XXXX"
                    >
                    @error('contact_number')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        Email Address <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        value="{{ old('email', $user->email) }}"
                        required 
                        class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-500 @enderror"
                        placeholder="your.email@example.com"
                    >
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <div class="mt-4">
                <label for="dietary_restrictions" class="block text-sm font-medium text-gray-700 mb-2">
                    Dietary Restrictions
                </label>
                <textarea 
                    id="dietary_restrictions" 
                    name="dietary_restrictions" 
                    rows="3"
                    class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('dietary_restrictions') border-red-500 @enderror"
                    placeholder="Please specify any dietary restrictions or allergies"
                >{{ old('dietary_restrictions', $user->dietary_restrictions) }}</textarea>
                @error('dietary_restrictions')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div>
            <button 
                type="submit" 
                class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-900 hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150"
            >
                Save Changes
            </button>
        </div>

        <div class="text-center">
            <p class="text-sm text-gray-600">
                Back to 
                <a href="{{ route('dashboard') }}" class="font-medium text-blue-900 hover:text-blue-800">
                    Dashboard
                </a>
            </p>
        </div>
    </form>
</div>

<script>
    // Auto-calculate age from date of birth
    const dobInput = document.getElementById('date_of_birth');
    if (dobInput) {
        dobInput.addEventListener('change', function() {
            const dob = new Date(this.value);
            const today = new Date();
            let age = today.getFullYear() - dob.getFullYear();
            const monthDiff = today.getMonth() - dob.getMonth();
            if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < dob.getDate())) {
                age--;
            }
            document.getElementById('age').value = age;
        });
    }

    // Toggle assistance field based on PWD selection
    function toggleAssistanceField() {
        const isPwd = document.querySelector('input[name="is_pwd"]:checked');
        const assistanceField = document.getElementById('assistance_field');
        
        if (isPwd && isPwd.value === '1') {
            assistanceField.style.display = 'block';
            assistanceField.querySelectorAll('input[type="radio"]').forEach(radio => {
                radio.required = true;
            });
        } else {
            assistanceField.style.display = 'none';
            assistanceField.querySelectorAll('input[type="radio"]').forEach(radio => {
                radio.required = false;
            });
        }
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        toggleAssistanceField();
    });
</script>
@endsection

