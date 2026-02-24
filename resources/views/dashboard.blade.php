<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>Dashboard - CAPDEVhub</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <div class="min-h-screen">
        <!-- Header -->
        <header class="bg-blue-900 text-white shadow-md">
            <div class="container mx-auto px-4 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="bg-white p-2 rounded">
                            <svg class="w-8 h-8 text-blue-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-xl font-bold">CAPDEVhub</h1>
                            <p class="text-sm text-blue-200">LGCDD - DILG NCR</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <span class="text-sm">Welcome, {{ auth()->user()->full_name }}</span>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="bg-blue-800 hover:bg-blue-700 px-4 py-2 rounded text-sm transition">
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="container mx-auto px-4 py-8">
            <div class="bg-white shadow-lg rounded-lg p-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">Dashboard</h2>
                <p class="text-gray-600">Welcome to CAPDEVhub! Your account has been successfully created.</p>
                
                <div class="mt-6 p-4 bg-blue-50 rounded-lg">
                    <h3 class="font-semibold text-gray-900 mb-2">Your Account Information</h3>
                    <div class="space-y-2 text-sm">
                        <p><strong>Name:</strong> {{ auth()->user()->full_name }}</p>
                        <p><strong>Email:</strong> {{ auth()->user()->email }}</p>
                        <p><strong>Role:</strong> <span class="uppercase">{{ auth()->user()->role }}</span></p>
                        <p><strong>Office:</strong> {{ auth()->user()->office }}</p>
                        <p><strong>Position:</strong> {{ auth()->user()->position }}</p>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
