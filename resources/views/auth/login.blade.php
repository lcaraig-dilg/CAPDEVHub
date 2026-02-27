@extends('layouts.auth')

@section('title', 'Login - CAPDEVhub')

@section('content')
<div class="bg-white shadow-lg rounded-lg p-8">
    <div class="text-center mb-8">
        <h2 class="text-3xl font-bold text-gray-900 mb-2">Welcome Back</h2>
        <p class="text-gray-600">Sign in to your CAPDEVhub account</p>
    </div>

    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf

        @if (!empty($redirect))
            <input type="hidden" name="redirect" value="{{ $redirect }}">
        @endif

        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                Email Address or Username
            </label>
            <input 
                type="text" 
                id="email" 
                name="email" 
                value="{{ old('email') }}"
                required 
                autofocus
                class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-[#0a7ca1] focus:border-[#0a7ca1] @error('email') border-red-500 @enderror"
                placeholder="Enter your email or username"
            >
            @error('email')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                Password
            </label>
            <input 
                type="password" 
                id="password" 
                name="password" 
                required
                class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-[#0a7ca1] focus:border-[#0a7ca1] @error('password') border-red-500 @enderror"
                placeholder="Enter your password"
            >
            @error('password')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <input 
                    id="remember" 
                    name="remember" 
                    type="checkbox" 
                    class="h-4 w-4 text-[#0a7ca1] focus:ring-[#0a7ca1] border-gray-300 rounded"
                >
                <label for="remember" class="ml-2 block text-sm text-gray-700">
                    Remember me
                </label>
            </div>
        </div>

        <div>
            <button 
                type="submit" 
                class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-[#FAB95B] hover:bg-[#F9A84D] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#FAB95B] transition duration-150"
            >
                Sign In
            </button>
        </div>

        <div class="text-center">
            <p class="text-sm text-gray-600">
                Don't have an account? 
                <a href="{{ route('register') }}" class="font-medium text-[#013141] hover:text-[#0a7ca1]">
                    Register here
                </a>
            </p>
        </div>
    </form>
</div>
@endsection
