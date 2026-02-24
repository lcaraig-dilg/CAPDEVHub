<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'CAPDEVhub - LGCDD DILG NCR')</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Favicon / Web Tab Icon --}}
    <link rel="icon" type="image/png" href="{{ asset('capdev-logo-png-square.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('capdev-logo-color-bg.png') }}">
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex flex-col">
        <!-- Header -->
        <header class="bg-gray-200 text-gray-900 shadow-sm border-b border-gray-300">
            <div class="container mx-auto px-4 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <img
                            src="{{ asset('capdev-logo-with-name.png') }}"
                            alt="CAPDEVhub - LGCDD DILG NCR"
                            class="h-10 sm:h-12 w-auto"
                        >
                        <div>
                            <h1 class="text-xl font-bold">CAPDEVhub</h1>
                            <p class="text-sm text-gray-600">LGCDD - DILG NCR</p>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-grow flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
            <div class="w-full @yield('content-width', 'max-w-md')">
                @if (session('success'))
                    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-gray-800 text-white py-4 mt-auto">
            <div class="container mx-auto px-4 text-center text-sm">
                <p>&copy; {{ date('Y') }} Department of the Interior and Local Government - National Capital Region</p>
                <p class="text-gray-400 mt-1">Local Government Capability Development Division</p>
            </div>
        </footer>
    </div>
</body>
</html>
