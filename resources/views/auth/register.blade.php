@extends('layouts.auth')

@section('title', 'Register - CAPDEVhub')

@section('content-width', 'max-w-5xl')

@section('content')
<div class="bg-white shadow-lg rounded-lg p-8">
    <div class="text-center mb-8">
        <h2 class="text-3xl font-bold text-gray-900 mb-2">Create Account</h2>
        <p class="text-gray-600">Register for CAPDEVhub access</p>
    </div>

    <!-- Privacy and Data Consent Notice -->
    <div class="mb-8 p-4 bg-blue-50 border-l-4 border-blue-900 rounded-r-lg">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-900 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3 flex-1">
                <h3 class="text-sm font-semibold text-blue-900 mb-2">Data Privacy and Consent Notice</h3>
                <p class="text-sm text-gray-700 leading-relaxed">
                    By registering, you acknowledge and consent to the collection, generation, use, processing, storage, and retention of your personal data provided in this registration form for the purpose of Capacity Development events organized by the Local Government Capability Development Division (LGCDD) of the Department of the Interior and Local Government - National Capital Region (DILG NCR).
                </p>
                <p class="text-sm text-gray-700 leading-relaxed mt-2">
                    You also grant the LGCDD permission to take your photograph and include you in video recordings for documentation and promotional purposes related to capacity development activities. You understand that the collection, processing, and use of your personal data shall be in strict accordance with the <strong>Data Privacy Act of 2012 (Republic Act No. 10173)</strong> and other applicable privacy laws and regulations of the Philippines.
                </p>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-6" id="registrationForm">
        @csrf

        <!-- Honeypot field for bot protection (hidden) -->
        <input type="text" name="website" tabindex="-1" autocomplete="off" style="position: absolute; left: -9999px;">

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
                            <strong>Important:</strong> The personal information you provide in this section will be used exactly as entered for generating event certificates. Please ensure all details (name, suffix, etc.) are accurate and complete, as they will appear on your official certificates of participation/completion.
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
                        value="{{ old('first_name') }}"
                        required 
                        class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-[#0a7ca1] focus:border-[#0a7ca1] @error('first_name') border-red-500 @enderror"
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
                        value="{{ old('middle_initial') }}"
                        maxlength="1"
                        pattern="[A-Za-z]"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-[#0a7ca1] focus:border-[#0a7ca1] @error('middle_initial') border-red-500 @enderror"
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
                        value="{{ old('last_name') }}"
                        required 
                        class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-[#0a7ca1] focus:border-[#0a7ca1] @error('last_name') border-red-500 @enderror"
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
                        value="{{ old('suffix') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-[#0a7ca1] focus:border-[#0a7ca1] @error('suffix') border-red-500 @enderror"
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
                        class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-[#0a7ca1] focus:border-[#0a7ca1] @error('gender') border-red-500 @enderror"
                    >
                        <option value="">Select Gender</option>
                        <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                        <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                        <option value="Prefer not to say" {{ old('gender') == 'Prefer not to say' ? 'selected' : '' }}>Prefer not to say</option>
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
                        value="{{ old('date_of_birth') }}"
                        required 
                        max="{{ date('Y-m-d', strtotime('-1 day')) }}"
                        min="1900-01-01"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-[#0a7ca1] focus:border-[#0a7ca1] @error('date_of_birth') border-red-500 @enderror"
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
                    <label class="flex items-center">
                        <input 
                            type="radio" 
                            name="is_pwd" 
                            value="1" 
                            {{ old('is_pwd') == '1' ? 'checked' : '' }}
                            class="h-4 w-4 text-[#0a7ca1] focus:ring-[#0a7ca1] border-gray-300"
                            onchange="toggleAssistanceField()"
                        >
                        <span class="ml-2 text-sm text-gray-700">Yes</span>
                    </label>
                    <label class="flex items-center">
                        <input 
                            type="radio" 
                            name="is_pwd" 
                            value="0" 
                            {{ old('is_pwd', '0') == '0' ? 'checked' : '' }}
                            class="h-4 w-4 text-[#0a7ca1] focus:ring-[#0a7ca1] border-gray-300"
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
                    <label class="flex items-center">
                        <input 
                            type="radio" 
                            name="requires_assistance" 
                            value="1" 
                            {{ old('requires_assistance') == '1' ? 'checked' : '' }}
                            class="h-4 w-4 text-[#0a7ca1] focus:ring-[#0a7ca1] border-gray-300"
                        >
                        <span class="ml-2 text-sm text-gray-700">Yes</span>
                    </label>
                    <label class="flex items-center">
                        <input 
                            type="radio" 
                            name="requires_assistance" 
                            value="0" 
                            {{ old('requires_assistance') == '0' ? 'checked' : '' }}
                            class="h-4 w-4 text-[#0a7ca1] focus:ring-[#0a7ca1] border-gray-300"
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
                        value="{{ old('office') }}"
                        required 
                        class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-[#0a7ca1] focus:border-[#0a7ca1] @error('office') border-red-500 @enderror"
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
                        value="{{ old('position') }}"
                        required 
                        class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-[#0a7ca1] focus:border-[#0a7ca1] @error('position') border-red-500 @enderror"
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
                        value="{{ old('lgu_organization') }}"
                        required 
                        class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-[#0a7ca1] focus:border-[#0a7ca1] @error('lgu_organization') border-red-500 @enderror"
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
                        value="{{ old('contact_number') }}"
                        required 
                        pattern="[0-9+\-() ]+"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-[#0a7ca1] focus:border-[#0a7ca1] @error('contact_number') border-red-500 @enderror"
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
                        value="{{ old('email') }}"
                        required 
                        class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-[#0a7ca1] focus:border-[#0a7ca1] @error('email') border-red-500 @enderror"
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
                    class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-[#0a7ca1] focus:border-[#0a7ca1] @error('dietary_restrictions') border-red-500 @enderror"
                    placeholder="Please specify any dietary restrictions or allergies"
                >{{ old('dietary_restrictions') }}</textarea>
                @error('dietary_restrictions')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Account Security Section -->
        <div>
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Account Information</h3>

            <div class="mt-4">
                <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                    Username
                </label>
                <input 
                    type="text" 
                    id="username" 
                    name="username" 
                    value="{{ old('username') }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-[#0a7ca1] focus:border-[#0a7ca1] @error('username') border-red-500 @enderror"
                    placeholder="Leave blank to auto-generate from email"
                >
                @error('username')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500">Leave blank to auto-generate from your email address</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        Password <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        required 
                        minlength="8"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-[#0a7ca1] focus:border-[#0a7ca1] @error('password') border-red-500 @enderror"
                        placeholder="Minimum 8 characters"
                    >
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Password must be at least 8 characters long</p>
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                        Confirm Password <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="password" 
                        id="password_confirmation" 
                        name="password_confirmation" 
                        required 
                        minlength="8"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-[#0a7ca1] focus:border-[#0a7ca1]"
                    >
                </div>
            </div>
        </div>

        <div>
            <button 
                type="submit" 
                class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-[#FAB95B] hover:bg-[#F9A84D] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#FAB95B] transition duration-150"
            >
                Register Account
            </button>
        </div>

        <div class="text-center">
            <p class="text-sm text-gray-600">
                Already have an account? 
                <a href="{{ route('login') }}" class="font-medium text-[#013141] hover:text-[#0a7ca1]">
                    Sign in here
                </a>
            </p>
        </div>
    </form>
</div>

<script>
    // Auto-calculate age from date of birth
    document.getElementById('date_of_birth').addEventListener('change', function() {
        const dob = new Date(this.value);
        const today = new Date();
        let age = today.getFullYear() - dob.getFullYear();
        const monthDiff = today.getMonth() - dob.getMonth();
        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < dob.getDate())) {
            age--;
        }
        document.getElementById('age').value = age;
    });

    // Toggle assistance field based on PWD selection
    function toggleAssistanceField() {
        const isPwd = document.querySelector('input[name="is_pwd"]:checked');
        const assistanceField = document.getElementById('assistance_field');
        
        if (isPwd && isPwd.value === '1') {
            assistanceField.style.display = 'block';
            assistanceField.querySelector('input[type="radio"]').required = true;
        } else {
            assistanceField.style.display = 'none';
            assistanceField.querySelectorAll('input[type="radio"]').forEach(radio => {
                radio.required = false;
                radio.checked = false;
            });
        }
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        toggleAssistanceField();
    });
</script>
@endsection
