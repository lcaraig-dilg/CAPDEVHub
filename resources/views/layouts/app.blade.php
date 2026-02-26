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
<body class="bg-gray-50" x-data="{ sidebarOpen: true }">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <aside 
            class="bg-[#E8E2DB] text-gray-900 flex flex-col border-r border-gray-200 overflow-hidden"
            :style="sidebarOpen ? 'width: 16rem; transition: width 300ms cubic-bezier(0.4, 0, 0.2, 1);' : 'width: 0; transition: width 300ms cubic-bezier(0.4, 0, 0.2, 1);'"
        >
            <div class="p-4 border-b border-gray-200 bg-[#c5b5a4] flex items-center justify-between whitespace-nowrap" style="min-width: 16rem;">
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-2" x-show="sidebarOpen" x-cloak x-transition:enter="transition ease-out duration-200 delay-100" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                    <img
                        src="{{ asset('capdev-logo-with-name.png') }}"
                        alt="CAPDEVhub - LGCDD DILG NCR"
                        class="h-10 w-auto"
                    >
                </a>
            </div>

            <nav class="flex-1 p-4 space-y-1 text-sm overflow-y-auto" style="min-width: 16rem;" x-data="{ adminOpen: true, pagesOpen: true }" x-show="sidebarOpen" x-cloak x-transition:enter="transition ease-out duration-200 delay-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                @php
                    $user = auth()->user();
                    $role = $user->role ?? 'user';
                @endphp

                {{-- Super Admin Menu Items --}}
                @if($role === 'super_admin')
                    {{-- Admin Dropdown --}}
                    <div>
                        <button @click="adminOpen = !adminOpen"
                                class="w-full flex items-center justify-between px-3 py-2 rounded-md transition text-gray-800 hover:bg-gray-200 font-semibold">
                            <span>Admin</span>
                            <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': adminOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div x-show="adminOpen" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 -translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 -translate-y-1"
                             class="ml-4 mt-1 space-y-1">
                            <a href="{{ route('dashboard') }}"
                               class="flex items-center px-3 py-2 rounded-md transition
                                      {{ request()->routeIs('dashboard') ? 'bg-[#0a7ca1] bg-opacity-20 text-white font-semibold' : 'text-gray-800 hover:bg-gray-200' }}">
                                <span>Dashboard</span>
                            </a>

                            <a href="{{ route('activities.index') }}"
                               class="flex items-center px-3 py-2 rounded-md transition
                                      {{ request()->routeIs('activities.*') ? 'bg-[#0a7ca1] bg-opacity-20 text-white font-semibold' : 'text-gray-800 hover:bg-gray-200' }}">
                                <span>Activities</span>
                            </a>

                            <a href="{{ route('prepost.index') }}"
                               class="flex items-center px-3 py-2 rounded-md transition
                                      {{ request()->routeIs('prepost.*') ? 'bg-[#0a7ca1] bg-opacity-20 text-white font-semibold' : 'text-gray-800 hover:bg-gray-200' }}">
                                <span>Pre &amp; Post Tests</span>
                            </a>

                            <a href="{{ route('program-of-activities.index') }}"
                               class="flex items-center px-3 py-2 rounded-md transition
                                      {{ request()->routeIs('program-of-activities.*') ? 'bg-[#0a7ca1] bg-opacity-20 text-white font-semibold' : 'text-gray-800 hover:bg-gray-200' }}">
                                <span>Program of Activities</span>
                            </a>

                            <a href="{{ route('materials.index') }}"
                               class="flex items-center px-3 py-2 rounded-md transition
                                      {{ request()->routeIs('materials.*') ? 'bg-[#0a7ca1] bg-opacity-20 text-white font-semibold' : 'text-gray-800 hover:bg-gray-200' }}">
                                <span>Materials Repository</span>
                            </a>

                            <a href="{{ route('quiz.index') }}"
                               class="flex items-center px-3 py-2 rounded-md transition
                                      {{ request()->routeIs('quiz.*') ? 'bg-[#0a7ca1] bg-opacity-20 text-white font-semibold' : 'text-gray-800 hover:bg-gray-200' }}">
                                <span>Quiz</span>
                            </a>

                            <a href="{{ route('certificates.index') }}"
                               class="flex items-start px-3 py-2 rounded-md transition
                                      {{ request()->routeIs('certificates.*') ? 'bg-[#0a7ca1] bg-opacity-20 text-white font-semibold' : 'text-gray-800 hover:bg-gray-200' }}">
                                <span class="leading-tight">Certificate of Attendance Generator</span>
                            </a>

                            <a href="{{ route('users.index') }}"
                               class="flex items-center px-3 py-2 rounded-md transition
                                      {{ request()->routeIs('users.*') ? 'bg-[#0a7ca1] bg-opacity-20 text-white font-semibold' : 'text-gray-800 hover:bg-gray-200' }}">
                                <span>Users</span>
                            </a>
                        </div>
                    </div>
                @endif

                {{-- Admin Menu Items --}}
                @if($role === 'admin')
                    {{-- Admin Dropdown --}}
                    <div>
                        <button @click="adminOpen = !adminOpen"
                                class="w-full flex items-center justify-between px-3 py-2 rounded-md transition text-gray-800 hover:bg-gray-200 font-semibold">
                            <span>Admin</span>
                            <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': adminOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div x-show="adminOpen" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 -translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 -translate-y-1"
                             class="ml-4 mt-1 space-y-1">
                            <a href="{{ route('dashboard') }}"
                               class="flex items-center px-3 py-2 rounded-md transition
                                      {{ request()->routeIs('dashboard') ? 'bg-[#0a7ca1] bg-opacity-20 text-white font-semibold' : 'text-gray-800 hover:bg-gray-200' }}">
                                <span>Dashboard</span>
                            </a>

                            <a href="{{ route('activities.index') }}"
                               class="flex items-center px-3 py-2 rounded-md transition
                                      {{ request()->routeIs('activities.*') ? 'bg-[#0a7ca1] bg-opacity-20 text-white font-semibold' : 'text-gray-800 hover:bg-gray-200' }}">
                                <span>Activities</span>
                            </a>

                            <a href="{{ route('prepost.index') }}"
                               class="flex items-center px-3 py-2 rounded-md transition
                                      {{ request()->routeIs('prepost.*') ? 'bg-[#0a7ca1] bg-opacity-20 text-white font-semibold' : 'text-gray-800 hover:bg-gray-200' }}">
                                <span>Pre &amp; Post Tests</span>
                            </a>

                            <a href="{{ route('program-of-activities.index') }}"
                               class="flex items-center px-3 py-2 rounded-md transition
                                      {{ request()->routeIs('program-of-activities.*') ? 'bg-[#0a7ca1] bg-opacity-20 text-white font-semibold' : 'text-gray-800 hover:bg-gray-200' }}">
                                <span>Program of Activities</span>
                            </a>

                            <a href="{{ route('materials.index') }}"
                               class="flex items-center px-3 py-2 rounded-md transition
                                      {{ request()->routeIs('materials.*') ? 'bg-[#0a7ca1] bg-opacity-20 text-white font-semibold' : 'text-gray-800 hover:bg-gray-200' }}">
                                <span>Materials Repository</span>
                            </a>

                            <a href="{{ route('quiz.index') }}"
                               class="flex items-center px-3 py-2 rounded-md transition
                                      {{ request()->routeIs('quiz.*') ? 'bg-[#0a7ca1] bg-opacity-20 text-white font-semibold' : 'text-gray-800 hover:bg-gray-200' }}">
                                <span>Quiz</span>
                            </a>

                            <a href="{{ route('certificates.index') }}"
                               class="flex items-start px-3 py-2 rounded-md transition
                                      {{ request()->routeIs('certificates.*') ? 'bg-[#0a7ca1] bg-opacity-20 text-white font-semibold' : 'text-gray-800 hover:bg-gray-200' }}">
                                <span class="leading-tight">Certificate of Attendance Generator</span>
                            </a>
                        </div>
                    </div>

                    {{-- Pages Dropdown --}}
                    <div>
                        <button @click="pagesOpen = !pagesOpen"
                                class="w-full flex items-center justify-between px-3 py-2 rounded-md transition text-gray-800 hover:bg-gray-200 font-semibold">
                            <span>Pages</span>
                            <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': pagesOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div x-show="pagesOpen"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 -translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 -translate-y-1"
                             class="ml-4 mt-1 space-y-1">
                            <a href="{{ route('my-activities.index') }}"
                               class="flex items-center px-3 py-2 rounded-md transition
                                      {{ request()->routeIs('my-activities.*') ? 'bg-[#0a7ca1] bg-opacity-20 text-white font-semibold' : 'text-gray-800 hover:bg-gray-200' }}">
                                <span>My Activities</span>
                            </a>

                            <a href="{{ route('profile.show') }}"
                               class="flex items-center px-3 py-2 rounded-md transition
                                      {{ request()->routeIs('profile.*') ? 'bg-[#0a7ca1] bg-opacity-20 text-white font-semibold' : 'text-gray-800 hover:bg-gray-200' }}">
                                <span>My Profile</span>
                            </a>

                            <a href="{{ route('my-certificates.index') }}"
                               class="flex items-center px-3 py-2 rounded-md transition
                                      {{ request()->routeIs('my-certificates.*') ? 'bg-[#0a7ca1] bg-opacity-20 text-white font-semibold' : 'text-gray-800 hover:bg-gray-200' }}">
                                <span>My Certificates</span>
                            </a>

                            <a href="{{ route('materials.index') }}"
                               class="flex items-center px-3 py-2 rounded-md transition
                                      {{ request()->routeIs('materials.*') ? 'bg-[#0a7ca1] bg-opacity-20 text-white font-semibold' : 'text-gray-800 hover:bg-gray-200' }}">
                                <span>Materials Repository</span>
                            </a>
                        </div>
                    </div>
                @endif

                {{-- User Menu Items --}}
                @if($role === 'user')
                    {{-- Pages Dropdown --}}
                    <div>
                        <button @click="pagesOpen = !pagesOpen"
                                class="w-full flex items-center justify-between px-3 py-2 rounded-md transition text-gray-800 hover:bg-gray-200 font-semibold">
                            <span>Pages</span>
                            <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': pagesOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div x-show="pagesOpen"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 -translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 -translate-y-1"
                             class="ml-4 mt-1 space-y-1">
                            <a href="{{ route('my-activities.index') }}"
                               class="flex items-center px-3 py-2 rounded-md transition
                                      {{ request()->routeIs('my-activities.*') ? 'bg-[#0a7ca1] bg-opacity-20 text-white font-semibold' : 'text-gray-800 hover:bg-gray-200' }}">
                                <span>My Activities</span>
                            </a>

                            <a href="{{ route('profile.show') }}"
                               class="flex items-center px-3 py-2 rounded-md transition
                                      {{ request()->routeIs('profile.*') ? 'bg-[#0a7ca1] bg-opacity-20 text-white font-semibold' : 'text-gray-800 hover:bg-gray-200' }}">
                                <span>My Profile</span>
                            </a>

                            <a href="{{ route('my-certificates.index') }}"
                               class="flex items-center px-3 py-2 rounded-md transition
                                      {{ request()->routeIs('my-certificates.*') ? 'bg-[#0a7ca1] bg-opacity-20 text-white font-semibold' : 'text-gray-800 hover:bg-gray-200' }}">
                                <span>My Certificates</span>
                            </a>

                            <a href="{{ route('materials.index') }}"
                               class="flex items-center px-3 py-2 rounded-md transition
                                      {{ request()->routeIs('materials.*') ? 'bg-[#0a7ca1] bg-opacity-20 text-white font-semibold' : 'text-gray-800 hover:bg-gray-200' }}">
                                <span>Materials Repository</span>
                            </a>
                        </div>
                    </div>
                @endif
            </nav>

            <div class="p-4 border-t border-gray-200 text-xs text-gray-500 bg-[#c5b5a4] whitespace-nowrap" style="min-width: 16rem;" x-show="sidebarOpen" x-cloak x-transition:enter="transition ease-out duration-200 delay-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button
                        type="submit"
                        class="w-full inline-flex items-center justify-center px-3 py-2 rounded-md bg-[#FAB95B] hover:bg-[#F9A84D] text-sm font-medium text-white transition"
                    >
                        Logout
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main area -->
        <div class="flex-1 flex flex-col">
            <!-- Top bar -->
            <header
                class="h-16 text-white shadow-md flex items-center justify-between px-6"
                style="background: linear-gradient(to right, #0a7ba1, #013141);"
            >
                <div class="flex items-center gap-4">
                    <button
                        @click="sidebarOpen = !sidebarOpen"
                        class="p-2 rounded-md hover:bg-white/10 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-white/50"
                        title="Toggle Sidebar"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                    <div class="text-sm font-medium">
                        @yield('page-title')
                    </div>
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
    @livewireScripts
</body>
</html>

