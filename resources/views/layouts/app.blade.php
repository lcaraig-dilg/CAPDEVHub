<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'CAPDEVhub - LGCDD DILG NCR')</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Global favicon / web tab icon --}}
    <link rel="icon" type="image/png" href="{{ asset('capdev-logo-png-square.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('capdev-logo-png-square.png') }}">
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <aside class="w-64 bg-gray-100 text-gray-900 flex flex-col border-r border-gray-200">
            <div class="p-4 border-b border-gray-200 bg-white">
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-2">
                    <img
                        src="{{ asset('capdev-logo-with-name.png') }}"
                        alt="CAPDEVhub - LGCDD DILG NCR"
                        class="h-10 w-auto"
                    >
                </a>
            </div>

            <nav class="flex-1 p-4 space-y-1 text-sm">
                <a href="{{ route('dashboard') }}"
                   class="flex items-center px-3 py-2 rounded-md transition
                          {{ request()->routeIs('dashboard') ? 'bg-blue-100 text-blue-900 font-semibold' : 'text-gray-800 hover:bg-gray-200' }}">
                    <span>Dashboard</span>
                </a>

                <a href="{{ route('activities.index') }}"
                   class="flex items-center px-3 py-2 rounded-md transition
                          {{ request()->routeIs('activities.*') ? 'bg-blue-100 text-blue-900 font-semibold' : 'text-gray-800 hover:bg-gray-200' }}">
                    <span>Activities</span>
                </a>

                <a href="{{ route('prepost.index') }}"
                   class="flex items-center px-3 py-2 rounded-md transition
                          {{ request()->routeIs('prepost.*') ? 'bg-blue-100 text-blue-900 font-semibold' : 'text-gray-800 hover:bg-gray-200' }}">
                    <span>Pre &amp; Post Tests</span>
                </a>

                <a href="{{ route('materials.index') }}"
                   class="flex items-center px-3 py-2 rounded-md transition
                          {{ request()->routeIs('materials.*') ? 'bg-blue-100 text-blue-900 font-semibold' : 'text-gray-800 hover:bg-gray-200' }}">
                    <span>Materials Repository</span>
                </a>

                <a href="{{ route('quiz.index') }}"
                   class="flex items-center px-3 py-2 rounded-md transition
                          {{ request()->routeIs('quiz.*') ? 'bg-blue-100 text-blue-900 font-semibold' : 'text-gray-800 hover:bg-gray-200' }}">
                    <span>Quiz</span>
                </a>

                <a href="{{ route('certificates.index') }}"
                   class="flex items-center px-3 py-2 rounded-md transition
                          {{ request()->routeIs('certificates.*') ? 'bg-blue-100 text-blue-900 font-semibold' : 'text-gray-800 hover:bg-gray-200' }}">
                    <span>Certificate of Attendance Generator</span>
                </a>

                <hr class="my-4 border-gray-300">

                <a href="{{ route('profile.show') }}"
                   class="flex items-center px-3 py-2 rounded-md transition
                          {{ request()->routeIs('profile.*') ? 'bg-gray-200 text-gray-900 font-semibold' : 'text-gray-800 hover:bg-gray-200' }}">
                    <span>My Profile</span>
                </a>
            </nav>

            <div class="p-4 border-t border-gray-200 text-xs text-gray-500 bg-white">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button
                        type="submit"
                        class="w-full inline-flex items-center justify-center px-3 py-2 rounded-md bg-blue-900 hover:bg-blue-800 text-sm font-medium text-white transition"
                    >
                        Logout
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main area -->
        <div class="flex-1 flex flex-col">
            <!-- Top bar -->
            <header class="h-16 bg-blue-900 text-white shadow-md flex items-center justify-between px-6">
                <div class="text-sm font-medium">
                    @yield('page-title')
                </div>
                <div class="text-sm">
                    @php $user = auth()->user(); @endphp
                    @if ($user)
                        Welcome,
                        <a
                            href="{{ route('profile.show') }}"
                            class="font-semibold underline-offset-2 hover:underline"
                        >
                            {{ $user->first_name }}
                        </a>
                    @endif
                </div>
            </header>

            <!-- Page content -->
            <main class="flex-1 p-6">
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
            </main>
        </div>
    </div>
</body>
</html>

