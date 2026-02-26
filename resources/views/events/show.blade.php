<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ $activity->title }} - CAPDEVhub</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    {{-- Open Graph / Facebook --}}
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="{{ $activity->title }}">
    <meta property="og:description" content="{{ strip_tags($activity->description) }}">
    @if($activity->banner_image)
    <meta property="og:image" content="{{ asset('storage/' . $activity->banner_image) }}">
    @endif
    
    {{-- Twitter --}}
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url()->current() }}">
    <meta property="twitter:title" content="{{ $activity->title }}">
    <meta property="twitter:description" content="{{ strip_tags($activity->description) }}">
    @if($activity->banner_image)
    <meta property="twitter:image" content="{{ asset('storage/' . $activity->banner_image) }}">
    @endif
    
    {{-- Favicon --}}
    <link rel="icon" type="image/png" href="{{ asset('capdev-logo-png-square.png') }}">
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex flex-col">
        {{-- Banner Image --}}
        @if($activity->banner_image)
        <div class="w-full">
            <img 
                src="{{ asset('storage/' . $activity->banner_image) }}" 
                alt="{{ $activity->title }} Banner"
                class="w-full h-auto"
            >
        </div>
        @endif

        {{-- Main Content --}}
        <main class="flex-grow container mx-auto px-4 py-8 max-w-7xl">
            {{-- Event Title --}}
            <h1 class="text-4xl font-bold text-[#013141] mb-4">{{ $activity->title }}</h1>
            
            {{-- Event Details --}}
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <div class="space-y-4">
                    <div class="flex items-start gap-3">
                        <svg class="w-6 h-6 text-[#0a7ca1] flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <div>
                            <p class="text-sm text-gray-500">Venue</p>
                            <p class="text-lg font-semibold text-gray-900">{{ $activity->venue }}</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start gap-3">
                        <svg class="w-6 h-6 text-[#0a7ca1] flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <div>
                            <p class="text-sm text-gray-500">Date & Time</p>
                            <p class="text-lg font-semibold text-gray-900">{{ $activity->activity_date->format('F d, Y h:i A') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Event Description --}}
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-2xl font-bold text-[#013141] mb-4">Event Description</h2>
                <div class="ql-editor prose max-w-none">
                    {!! $activity->description !!}
                </div>
            </div>

            {{-- Share Section --}}
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-2xl font-bold text-[#013141] mb-4">Share this event</h2>
                <div class="flex flex-wrap gap-3">
                    {{-- Facebook Share --}}
                    <a 
                        href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-[#1877F2] text-white rounded-md hover:bg-[#166FE5] transition-colors duration-200"
                    >
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                        <span>Facebook</span>
                    </a>

                    {{-- Twitter Share --}}
                    <a 
                        href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ urlencode($activity->title) }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-[#1DA1F2] text-white rounded-md hover:bg-[#1a91da] transition-colors duration-200"
                    >
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                        </svg>
                        <span>Twitter</span>
                    </a>

                    {{-- Instagram --}}
                    <button
                        type="button"
                        onclick="shareToInstagram()"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-[#833AB4] via-[#FD1D1D] to-[#FCB045] text-white rounded-md hover:opacity-90 transition-opacity duration-200"
                    >
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                        </svg>
                        <span>Instagram</span>
                    </button>

                    {{-- Copy Link --}}
                    <button
                        type="button"
                        onclick="copyEventLink()"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-[#FAB95B] text-white rounded-md hover:bg-[#F9A84D] transition-colors duration-200"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                        <span id="copy-link-text">Copy Link</span>
                    </button>
                </div>
            </div>
        </main>

        {{-- Footer --}}
        <footer class="bg-[#013141] text-white mt-auto">
            <div class="container mx-auto px-4 py-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    {{-- Location --}}
                    <div>
                        <h3 class="text-lg font-semibold mb-3">Location</h3>
                        <p class="text-sm text-gray-300">{{ env('LOCATION', 'Annex Building, Sugar Center Compound') }}</p>
                        <p class="text-sm text-gray-300">{{ env('LOCATION_ADDRESS', 'North Ave, Diliman, Quezon City') }}</p>
                        <p class="text-sm text-gray-300">{{ env('LOCATION_REGION', 'National Capital Region') }}</p>
                    </div>

                    {{-- Office Hours --}}
                    <div>
                        <h3 class="text-lg font-semibold mb-3">Office Hours</h3>
                        <p class="text-sm text-gray-300">{{ env('OFFICE_HOURS_WEEKDAYS', 'Mon-Fri 8:00 am to 5:00 pm') }}</p>
                        <p class="text-sm text-gray-300">{{ env('OFFICE_HOURS_WEEKEND', 'Saturday & Sunday Closed') }}</p>
                    </div>

                    {{-- Queries & Concerns --}}
                    <div>
                        <h3 class="text-lg font-semibold mb-3">Queries & Concerns</h3>
                        <p class="text-sm text-gray-300">{{ env('QUERIES_PHONE', '(02) 3453 4748') }}</p>
                    </div>
                </div>

                {{-- Copyright --}}
                <div class="border-t border-gray-700 pt-6 text-center">
                    <p class="text-sm text-gray-400">
                        &copy; {{ date('Y') }} {{ env('COPYRIGHT_TEXT', 'Local Government Capability Development Division (LGCDD) - DILG NCR') }}. All rights reserved.
                    </p>
                </div>
            </div>
        </footer>
    </div>

    <script>
        function copyEventLink() {
            const url = window.location.href;
            navigator.clipboard.writeText(url).then(function() {
                const buttonText = document.getElementById('copy-link-text');
                const originalText = buttonText.textContent;
                buttonText.textContent = 'Link Copied!';
                setTimeout(function() {
                    buttonText.textContent = originalText;
                }, 2000);
            }).catch(function(err) {
                console.error('Failed to copy link:', err);
                alert('Failed to copy link. Please copy manually: ' + url);
            });
        }

        function shareToInstagram() {
            // Instagram doesn't have a direct share URL, so we'll copy the link and open Instagram
            const url = window.location.href;
            const text = '{{ $activity->title }}';
            
            // Copy link to clipboard first
            navigator.clipboard.writeText(url).then(function() {
                // Open Instagram (user can paste the link manually)
                window.open('https://www.instagram.com/', '_blank');
                alert('Link copied! Open Instagram and paste it in your story or post.');
            }).catch(function(err) {
                // Fallback: show the URL
                alert('Please copy this link and share it on Instagram:\n' + url);
            });
        }
    </script>
</body>
</html>
