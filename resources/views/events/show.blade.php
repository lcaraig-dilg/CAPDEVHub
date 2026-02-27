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
    @php
        $colorPalette = $activity->color_palette ?? 'default';
        
        // Set colors based on palette choice
        if ($colorPalette === 'default') {
            // Default color palette (signature colors)
            $color1 = '#013141'; // dark teal
            $color2 = '#0A7CA1'; // light blue/cyan
            $color3 = '#FAB95B'; // orange/yellow
        } elseif ($colorPalette === 'plain') {
            // Plain color palette
            $color1 = '#FFFFFF'; // white
            $color2 = '#F3F4F6'; // gray-100
            $color3 = '#000000'; // black
        } else {
            // Custom colors
            $color1 = $activity->accent_color_1 ?? '#013141';
            $color2 = $activity->accent_color_2 ?? '#0A7CA1';
            $color3 = $activity->accent_color_3 ?? '#FAB95B';
        }

        $isDark = function ($hex) {
            $hex = ltrim($hex, '#');
            if (strlen($hex) === 3) {
                $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
            }
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
            $luminance = 0.299 * $r + 0.587 * $g + 0.114 * $b;
            return $luminance < 150;
        };

        // For plain palette, always use black text on white/gray cards
        // For other palettes, calculate based on color2 luminance
        if ($colorPalette === 'plain') {
            $color2Text = '#000000'; // black text on white/gray cards
        } else {
            $color2Text = $isDark($color2) ? '#FFFFFF' : '#000000';
        }
        
        // Convert hex to rgba for translucent effect
        $hexToRgba = function($hex, $alpha = 0.50) {
            $hex = ltrim($hex, '#');
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
            return "rgba($r, $g, $b, $alpha)";
        };
        
        $color2Rgba = $hexToRgba($color2, 0.50);
        
        // Determine text stroke colors based on background colors
        // If background is dark, use light outline; if background is light, use dark outline
        // Determine text stroke colors based on text color (not background)
        // If text color is dark, use light stroke; if text color is light, use dark stroke
        $titleStrokeColor = $isDark($color3) ? 'rgba(255, 255, 255, 0.8)' : 'rgba(0, 0, 0, 0.5)';
        $sectionHeadingStrokeColor = $isDark($color3) ? 'rgba(255, 255, 255, 0.8)' : 'rgba(0, 0, 0, 0.5)';
        $buttonIconColor = $isDark($color3) ? '#FFFFFF' : '#000000';
        $buttonBorderColor = $isDark($color3) ? 'rgba(255, 255, 255, 0.5)' : 'rgba(0, 0, 0, 0.3)';
        $sectionIconColor = $isDark($color2) ? '#FFFFFF' : '#000000';
        
        // Convert Google Maps link to embeddable iframe URL
        // Users can paste the regular share link (from "Share" → "Copy link") - no need for embed code!
        $getEmbedUrl = function($mapsLink, $venueName) {
            // If no link provided, use venue name for search
            if (empty($mapsLink)) {
                return 'https://maps.google.com/maps?q=' . urlencode($venueName) . '&output=embed';
            }
            
            // If the link is already in embed format (from iframe src), use it directly
            if (strpos($mapsLink, 'output=embed') !== false) {
                return $mapsLink;
            }
            
            // Decode URL-encoded characters
            $decodedLink = urldecode($mapsLink);
            
            // Method 1: Extract coordinates from @lat,lng format (most accurate)
            // This works with standard Google Maps share links
            // Format: https://www.google.com/maps/@14.5995,120.9842,15z
            // Or: https://www.google.com/maps/place/.../@14.5995,120.9842,15z
            if (preg_match('/@(-?\d+\.?\d+),(-?\d+\.?\d+)(?:,(\d+[a-z]?))?/', $decodedLink, $matches)) {
                $lat = trim($matches[1]);
                $lng = trim($matches[2]);
                $zoom = isset($matches[3]) ? trim($matches[3]) : '14';
                // Remove 'z' from zoom if present
                $zoom = str_replace('z', '', $zoom);
                return 'https://maps.google.com/maps?q=' . $lat . ',' . $lng . '&hl=en&z=' . $zoom . '&output=embed';
            }
            
            // Method 2: Extract place ID and coordinates from /place/ format
            // This is the most common format from Google Maps share links
            // Format: https://www.google.com/maps/place/Place+Name/@lat,lng,zoomz/data=...
            if (preg_match('/\/place\/([^\/\?]+)/', $decodedLink, $placeMatches)) {
                $placeInfo = $placeMatches[1];
                // Try to extract coordinates from the place info
                if (preg_match('/@(-?\d+\.?\d+),(-?\d+\.?\d+)(?:,(\d+[a-z]?))?/', $placeInfo, $coordMatches)) {
                    $lat = trim($coordMatches[1]);
                    $lng = trim($coordMatches[2]);
                    $zoom = isset($coordMatches[3]) ? trim($coordMatches[3]) : '14';
                    $zoom = str_replace('z', '', $zoom);
                    return 'https://maps.google.com/maps?q=' . $lat . ',' . $lng . '&hl=en&z=' . $zoom . '&output=embed';
                }
                // If no coordinates, use the place name/ID
                $placeName = preg_replace('/@.*$/', '', $placeInfo);
                $placeName = str_replace('+', ' ', $placeName);
                $placeName = urldecode($placeName);
                return 'https://maps.google.com/maps?q=' . urlencode($placeName) . '&hl=en&output=embed';
            }
            
            // Method 3: Handle shortened Google Maps links (goo.gl, maps.app.goo.gl)
            // These need to be expanded, but we can try to extract info or use as-is
            if (preg_match('/(goo\.gl|maps\.app\.goo\.gl)\/maps\/([A-Za-z0-9]+)/', $mapsLink, $shortMatches)) {
                // For shortened links, we'll use the venue name as fallback
                // In production, you might want to expand these URLs first
                return 'https://maps.google.com/maps?q=' . urlencode($venueName) . '&output=embed';
            }
            
            // Method 4: Extract query from ?q= format
            if (preg_match('/[?&]q=([^&]+)/', $decodedLink, $matches)) {
                $query = urldecode($matches[1]);
                return 'https://maps.google.com/maps?q=' . urlencode($query) . '&hl=en&output=embed';
            }
            
            // Method 5: Extract ll parameter (lat,lng)
            if (preg_match('/[?&]ll=(-?\d+\.?\d+),(-?\d+\.?\d+)/', $decodedLink, $matches)) {
                $lat = trim($matches[1]);
                $lng = trim($matches[2]);
                return 'https://maps.google.com/maps?q=' . $lat . ',' . $lng . '&hl=en&z=14&output=embed';
            }
            
            // Method 6: For any Google Maps link, try to append output=embed
            if (strpos($mapsLink, 'maps.google.com') !== false || strpos($mapsLink, 'google.com/maps') !== false) {
                // Check if it already has query parameters
                $separator = strpos($mapsLink, '?') !== false ? '&' : '?';
                return $mapsLink . $separator . 'output=embed';
            }
            
            // Fallback: use venue name
            return 'https://maps.google.com/maps?q=' . urlencode($venueName) . '&output=embed';
        };
        
        $embedUrl = $getEmbedUrl($activity->venue_google_maps_link ?? '', $activity->venue);
    @endphp

    <div class="min-h-screen flex flex-col" style="background: {{ $colorPalette === 'plain' ? '#F3F4F6' : 'radial-gradient(circle, #FFFFFF 0, #FFFFFF 10%, ' . $color1 . ' 80%)' }};">
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
        <main class="flex-grow container mx-auto px-4 py-8 max-w-[95%]">
            {{-- Registration alerts --}}
            @if (session('success'))
                <div class="mb-4 rounded-md bg-green-100 border border-green-400 text-green-800 px-4 py-3">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 rounded-md bg-red-100 border border-red-400 text-red-800 px-4 py-3">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Two Column Layout --}}
            <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">
                {{-- Left Column: Event Information (80% - 4 columns) --}}
                <div class="lg:col-span-4 space-y-6">
                    {{-- Event Title and Registration Button --}}
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <h1 class="text-4xl font-bold" style="color: {{ $color3 }}; -webkit-text-stroke: 1px {{ $titleStrokeColor }}; text-stroke: 1px {{ $titleStrokeColor }};">{{ $activity->title }}</h1>

                        <div class="flex items-center">
                            @auth
                                @if ($alreadyRegistered ?? false)
                                    <div class="inline-flex items-center px-4 py-2 rounded-md bg-gray-200 text-gray-700 text-sm font-medium cursor-not-allowed">
                                        You are already registered for this activity.
                                    </div>
                                @else
                                    <form method="POST" action="{{ route('events.register', \Illuminate\Support\Str::slug($activity->title)) }}">
                                        @csrf
                                        <button
                                            type="submit"
                                            class="inline-flex items-center gap-2 px-6 py-3 rounded-md text-sm font-semibold shadow-md transition-colors duration-200"
                                            style="background-color: {{ $color3 }}; color: {{ $buttonIconColor }}; border: 2px solid {{ $buttonBorderColor }};"
                                            onmouseover="this.style.filter='brightness(0.95)'"
                                            onmouseout="this.style.filter='brightness(1)'"
                                            onclick="return confirm('You will be joining {{ addslashes($activity->title) }} at {{ addslashes($activity->venue) }} on {{ $activity->activity_date->format('F d, Y h:i A') }}.\n\nYou are not required to fill up any form and we will use the information in your profile.');"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="{{ $buttonIconColor }}" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                            </svg>
                                            <span>Register for this activity</span>
                                        </button>
                                    </form>
                                @endif
                            @endauth

                            @guest
                                <button
                                    type="button"
                                    class="inline-flex items-center gap-2 px-6 py-3 rounded-md text-sm font-semibold shadow-md transition-colors duration-200"
                                    style="background-color: {{ $color3 }}; color: {{ $buttonIconColor }}; border: 2px solid {{ $buttonBorderColor }};"
                                    onmouseover="this.style.filter='brightness(0.95)'"
                                    onmouseout="this.style.filter='brightness(1)'"
                                    onclick="openGuestRegistrationForm()"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="{{ $buttonIconColor }}" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    <span>Register for this activity</span>
                                </button>
                            @endguest
                        </div>
                    </div>

                    {{-- Venue and Date Time Card --}}
                    <div class="rounded-lg shadow-md p-6 backdrop-blur-sm" style="background-color: {{ $color2Rgba }}; color: {{ $color2Text }};">
                        <div class="space-y-4">
                            <div class="grid grid-cols-1 {{ $activity->venue_google_maps_link ? 'md:grid-cols-2' : '' }} gap-4">
                                @if($activity->venue_google_maps_link)
                                    {{-- Google Maps Embed (Left) --}}
                                    <div class="w-full">
                                        <div class="w-full rounded-lg overflow-hidden shadow-sm" style="height: 200px; min-height: 200px;">
                                            <iframe 
                                                src="{{ $embedUrl }}"
                                                width="100%" 
                                                height="100%" 
                                                frameborder="0"
                                                style="border:0;" 
                                                allowfullscreen
                                                loading="lazy" 
                                                referrerpolicy="no-referrer-when-downgrade"
                                                aria-label="Google Maps location for {{ $activity->venue }}"
                                            ></iframe>
                                        </div>
                                        <p class="mt-2 text-xs leading-relaxed" style="color: {{ $color2Text }}; opacity: 0.8;">
                                            <span class="flex items-start gap-1 flex-wrap">
                                                <svg class="w-3 h-3 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                <span class="flex-1 min-w-0">
                                                    This figure sometimes does not show the accurate location, please 
                                                    <a 
                                                        href="{{ $activity->venue_google_maps_link }}" 
                                                        target="_blank" 
                                                        rel="noopener noreferrer"
                                                        class="underline font-medium hover:opacity-80 transition-opacity whitespace-nowrap"
                                                        style="color: {{ $color3 }}; -webkit-text-stroke: 0.2px {{ $sectionHeadingStrokeColor }}; text-stroke: 1px {{ $sectionHeadingStrokeColor }};"
                                                    >
                                                        click this link
                                                    </a>
                                                    <span class="whitespace-normal"> to show the accurate location in Google Maps</span>
                                                </span>
                                            </span>
                                        </p>
                                    </div>
                                @endif
                                
                                {{-- Venue and Date/Time Information (Right or Full Width) --}}
                                <div class="space-y-4">
                                    {{-- Venue Information --}}
                                    <div class="flex items-start gap-3">
                                        <svg class="w-6 h-6 flex-shrink-0 mt-1" fill="none" stroke="{{ $sectionIconColor }}" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        <div class="flex-1">
                                            <p class="text-sm">Venue</p>
                                            <p class="text-lg font-semibold">{{ $activity->venue }}</p>
                                        </div>
                                    </div>
                                    
                                    {{-- Date & Time --}}
                                    <div class="flex items-start gap-3">
                                        <svg class="w-6 h-6 flex-shrink-0 mt-1" fill="none" stroke="{{ $sectionIconColor }}" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        <div>
                                            <p class="text-sm">Date & Time</p>
                                            <p class="text-lg font-semibold">{{ $activity->activity_date->format('F d, Y h:i A') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Event Description --}}
                    <div class="rounded-lg shadow-md p-6 backdrop-blur-sm" style="background-color: {{ $color2Rgba }}; color: {{ $color2Text }};">
                        <h2 class="text-2xl font-bold mb-4" style="color: {{ $color3 }}; -webkit-text-stroke: 1px {{ $sectionHeadingStrokeColor }}; text-stroke: 2px {{ $sectionHeadingStrokeColor }};">Event Description</h2>
                        <div class="ql-editor prose max-w-none">
                            {!! $activity->description !!}
                        </div>
                    </div>

                    {{-- Share Section --}}
                    @php
                        // Build multiple short, formal government-style invitation messages and randomize them on copy
                        $dateTime = $activity->activity_date->format('F d, Y h:i A');
                        $eventUrl = url()->current();
                        $title    = $activity->title;
                        $venue    = $activity->venue;

                        $sharingTemplates = [
                            // Template 1 – Standard formal invite
                            "The DILG–NCR, through the LGCDD, cordially invites you to {$title}.\n\n📅 Date & Time: {$dateTime}\n📍 Venue: {$venue}\n\nFor complete event details, please visit: {$eventUrl}",

                            // Template 2 – Emphasis on participation
                            "You are formally invited to participate in {$title}, organized by DILG–NCR.\n\n📅 Date & Time: {$dateTime}\n📍 Venue: {$venue}\n\nFurther information is available at: {$eventUrl}",

                            // Template 3 – Emphasis on purpose and details link
                            "Please join us for {$title}, a capacity development activity of DILG–NCR.\n\n📅 Date & Time: {$dateTime}\n📍 Venue: {$venue}\n\nTo view the full invitation and event details, kindly visit: {$eventUrl}",

                            // Template 4 – Save-the-date style, still formal
                            "You are invited to {$title}.\n\n📅 Schedule: {$dateTime}\n📍 Venue: {$venue}\n\nFor official information and updates, please refer to: {$eventUrl}",
                        ];
                    @endphp
                    <div class="rounded-lg shadow-md p-6 backdrop-blur-sm" style="background-color: {{ $color2Rgba }}; color: {{ $color2Text }};">
                        <h2 class="text-2xl font-bold mb-4" style="color: {{ $color3 }}; -webkit-text-stroke: 2px {{ $sectionHeadingStrokeColor }}; text-stroke: 1px {{ $sectionHeadingStrokeColor }};">Share this event</h2>
                        <div class="flex flex-wrap gap-3">
                    {{-- Facebook Share --}}
                    <button
                        type="button"
                        onclick="shareToSocialMedia('facebook', 'https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}')"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-[#1877F2] text-white rounded-md hover:bg-[#166FE5] transition-colors duration-200"
                    >
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                        <span class="sr-only">Facebook</span>
                    </button>

                    {{-- X (Twitter) Share --}}
                    <button
                        type="button"
                        onclick="shareToSocialMedia('twitter', 'https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ urlencode($activity->title) }}')"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-[#000000] text-white rounded-md hover:bg-[#000000] transition-colors duration-200"
                    >
                        <svg class="w-5 h-5" viewBox="0 0 1200 1226.37" aria-hidden="true">
                            <path d="M714.163 519.284L1160.89 0H1055.03L667.137 450.887L357.328 0H0L468.492 681.821L0 1226.37H105.866L515.491 750.218L842.672 1226.37H1200L714.137 519.284H714.163ZM569.165 687.828L521.697 619.934L144.011 79.6944H306.615L611.412 515.685L658.88 583.579L1055.08 1150.3H892.476L569.165 687.854V687.828Z" fill="white"/>
                        </svg>
                        <span class="sr-only">X (Twitter)</span>
                    </button>

                        {{-- Instagram --}}
                        <button
                            type="button"
                            onclick="shareToSocialMedia('instagram', 'https://www.instagram.com/')"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-[#833AB4] via-[#FD1D1D] to-[#FCB045] text-white rounded-md hover:opacity-90 transition-opacity duration-200"
                        >
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                            </svg>
                            <span class="sr-only">Instagram</span>
                        </button>
                    </div>
                    </div>

            {{-- Sharing Details Modal --}}
            <div id="sharing-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center transition-opacity duration-300 ease-out" style="background-color: rgba(0, 0, 0, 0); -webkit-backdrop-filter: blur(0px); backdrop-filter: blur(0px);">
                <div id="sharing-modal-content" class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4 p-6 transform transition-all duration-300 ease-out scale-95 opacity-0 touch-manipulation">
                    <div class="text-center">
                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
                            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Sharing Details Copied!</h3>
                        <p class="text-sm text-gray-600 mb-6">
                            The event details have been copied to your clipboard. Paste this on your post and it's ready for posting.
                        </p>
                        <button
                            type="button"
                            onclick="closeSharingModal()"
                            class="w-full px-4 py-2 bg-[#FAB95B] text-[#013141] font-semibold rounded-md hover:bg-[#F9A84D] transition-colors duration-200"
                        >
                            OK
                        </button>
                    </div>
                </div>
            </div>
                </div>

                {{-- Right Column: Ads Sidebar (20% - 1 column) --}}
                <aside class="lg:col-span-1">
                    <div class="bg-[#E8E2DB] rounded-lg shadow-md p-6 space-y-6">
                        <div class="border-l-4 border-[#0a7ca1] pl-4">
                            <h2 class="text-xl font-bold text-[#013141] mb-2">About LGCDD</h2>
                            <p class="text-sm text-gray-700 leading-relaxed">
                                The Local Government Capability Development Division (LGCDD) of DILG-NCR leads the design and implementation 
                                of capacity development programs for local government units in the National Capital Region.
                            </p>
                        </div>

                        <div class="space-y-3">
                            <h3 class="text-sm font-semibold text-[#013141] uppercase tracking-wide">What LGCDD does</h3>
                            <ul class="text-sm text-gray-700 space-y-2 list-disc list-inside">
                                <li>Designs and conducts capability development activities for LGUs</li>
                                <li>Supports local governance reforms and innovation</li>
                                <li>Provides technical assistance to local officials and functionaries</li>
                            </ul>
                        </div>

                        {{-- Recent Posts from DILG-NCR Facebook --}}
                        <div class="bg-white rounded-md shadow-md p-3 space-y-2">
                            <h3 class="text-sm font-bold text-[#013141] flex items-center gap-2">
                                <svg class="w-4 h-4 text-[#0a7ca1]" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M24 12.073C24 5.405 18.627 0 12 0S0 5.405 0 12.073c0 6.028 4.388 11.022 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.095 24 18.101 24 12.073z"/>
                                </svg>
                                Recent Posts from DILG-NCR Facebook
                            </h3>
                            <div class="fb-page"
                                 data-href="https://www.facebook.com/dilgncr"
                                 data-tabs="timeline"
                                 data-width="320"
                                 data-height="300"
                                 data-small-header="true"
                                 data-adapt-container-width="true"
                                 data-hide-cover="false"
                                 data-show-facepile="false">
                                <blockquote cite="https://www.facebook.com/dilgncr" class="fb-xfbml-parse-ignore">
                                    <a href="https://www.facebook.com/dilgncr">DILG-NCR Facebook</a>
                                </blockquote>
                            </div>
                        </div>

                        {{-- Recent Posts from DILG-NCR Website --}}
                        <div class="bg-gradient-to-br from-[#0a7ca1] to-[#013141] rounded-md p-4 shadow-lg space-y-3">
                            <h3 class="text-sm font-bold text-white flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Recent Posts from DILG-NCR Website
                            </h3>
                            <div class="space-y-2 max-h-64 overflow-y-auto">
                                @if(isset($websitePosts) && count($websitePosts) > 0)
                                    @foreach($websitePosts as $post)
                                        <a 
                                            href="{{ $post['link'] ?? env('DILG_NCR_WEBSITE', 'https://ncr.dilg.gov.ph') }}" 
                                            target="_blank" 
                                            rel="noopener noreferrer"
                                            class="block bg-white bg-opacity-90 rounded p-2 hover:bg-opacity-100 transition-all duration-200 group"
                                        >
                                            <p class="text-xs font-semibold text-[#013141] group-hover:text-[#0a7ca1] line-clamp-2">
                                                {{ $post['title'] ?? 'Latest Update' }}
                                            </p>
                                            @if(isset($post['date']))
                                                <p class="text-xs text-gray-500 mt-1">{{ $post['date'] }}</p>
                                            @endif
                                        </a>
                                    @endforeach
                                @else
                                    <div class="bg-white bg-opacity-90 rounded p-2">
                                        <p class="text-xs text-gray-600 italic">No recent posts available</p>
                                    </div>
                                @endif
                            </div>
                            <a 
                                href="{{ env('DILG_NCR_WEBSITE', 'https://ncr.dilg.gov.ph') }}" 
                                target="_blank" 
                                rel="noopener noreferrer"
                                class="inline-flex items-center gap-2 px-4 py-2 bg-[#FAB95B] text-[#013141] font-semibold rounded-md hover:bg-[#F9A84D] transition-colors duration-200 text-sm w-full justify-center mt-2"
                            >
                                <span>View All Posts</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 3h7m0 0v7m0-7L10 14"></path>
                                </svg>
                            </a>
                        </div>

                        {{-- Quick Links for PDMU-NCR --}}
                        <div class="bg-white rounded-md p-4 shadow-sm space-y-4">
                            <h3 class="text-sm font-semibold text-[#013141] flex items-center gap-2">
                                <span class="w-1 h-5 bg-[#0a7ca1] inline-block rounded-sm"></span>
                                Quick Links
                            </h3>
                            
                            {{-- PDMU-NCR Website --}}
                            <div class="space-y-2">
                                <p class="text-xs text-gray-600 font-medium">PDMU-NCR Website</p>
                                <a 
                                    href="https://pdmu.ncr.dilg.gov.ph/" 
                                    target="_blank" 
                                    rel="noopener noreferrer"
                                    class="inline-flex items-center gap-2 px-3 py-2 bg-[#0a7ca1] text-white font-semibold rounded-md hover:bg-[#013141] transition-colors duration-200 text-xs w-full justify-center"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path>
                                    </svg>
                                    <span>PDMU-NCR Website</span>
                                </a>
                            </div>

                            {{-- PDMU-NCR Facebook --}}
                            <div class="space-y-2">
                                <p class="text-xs text-gray-600 font-medium">PDMU-NCR Facebook</p>
                                <a 
                                    href="https://www.facebook.com/pdmudilgncr" 
                                    target="_blank" 
                                    rel="noopener noreferrer"
                                    class="inline-flex items-center gap-2 px-3 py-2 bg-[#1877F2] text-white font-semibold rounded-md hover:bg-[#166FE5] transition-colors duration-200 text-xs w-full justify-center"
                                >
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                    </svg>
                                    <span>PDMU-NCR Facebook Page</span>
                                </a>
                            </div>
                        </div>

                        <div class="text-xs text-gray-600">
                            <p class="font-semibold text-[#013141] mb-1">LGCDD - DILG NCR</p>
                            <p>Committed to building capable, accountable, and responsive local governments in the National Capital Region.</p>
                        </div>
                    </div>
                </aside>
            </div>

            {{-- Guest registration form (for users without an account) --}}
                    @guest
                    <div
                        id="guest-registration-form"
                        class="mt-8"
                        style="{{ (!auth()->check() && ($errors->any() || old('first_name'))) ? '' : 'display: none;' }}"
                    >
                        <hr class="mb-6 border-[#c5b5a4]">

                        <div class="rounded-lg shadow-md p-6 backdrop-blur-sm" style="background-color: {{ $color2Rgba }}; color: {{ $color2Text }};">
                            <h2 class="text-2xl font-bold mb-4" style="color: {{ $color3 }}; -webkit-text-stroke: 1px {{ $sectionHeadingStrokeColor }}; text-stroke: 2px {{ $sectionHeadingStrokeColor }};">Event Registration Form</h2>

                            <p class="mb-4 text-sm">
                                <strong>Note:</strong> If you already have a CAPDEVhub account, you do not need to fill out this form.
                                Please
                                <a
                                    href="{{ route('login', ['redirect' => url()->current()]) }}"
                                    class="font-semibold underline"
                                    style="color: {{ $color3 }}; -webkit-text-stroke: 1px {{ $sectionHeadingStrokeColor }}; text-stroke: 1px {{ $sectionHeadingStrokeColor }};"
                                >
                                    login first
                                </a>
                                and then return to this page to register using your profile information.
                            </p>

                            {{-- Data Privacy and Consent Notice (match register page design) --}}
                            <div class="mb-6 p-4 bg-blue-50 border-l-4 border-blue-900 rounded-r-lg">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-blue-900 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <div class="ml-3 flex-1">
                                        <h3 class="text-sm font-semibold text-blue-900 mb-2">Data Privacy and Consent Notice</h3>
                                        <p class="text-sm text-gray-700 leading-relaxed">
                                            By registering, you acknowledge and consent to the collection, generation, use, processing, storage, and retention of your personal
                                            data provided in this registration form for the purpose of Capacity Development events organized by the Local Government Capability
                                            Development Division (LGCDD) of the Department of the Interior and Local Government - National Capital Region (DILG NCR).
                                        </p>
                                        <p class="text-sm text-gray-700 leading-relaxed mt-2">
                                            You also grant the LGCDD permission to take your photograph and include you in video recordings for documentation and promotional
                                            purposes related to capacity development activities. You understand that the collection, processing, and use of your personal data
                                            shall be in strict accordance with the <strong>Data Privacy Act of 2012 (Republic Act No. 10173)</strong> and other applicable privacy
                                            laws and regulations of the Philippines.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            {{-- Important Note on Certificate Details (match register page design) --}}
                            <div class="mb-6 p-3 bg-amber-50 border-l-4 border-amber-500 rounded-r-lg">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-amber-600 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <div class="ml-3 flex-1">
                                        <p class="text-sm text-amber-800 leading-relaxed">
                                            <strong>Important:</strong> The personal information you provide in this section will be used exactly as entered for generating event
                                            certificates. Please ensure all details (name, suffix, etc.) are accurate and complete, as they will appear on your official
                                            certificates of participation/completion.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <form method="POST" action="{{ route('events.register', \Illuminate\Support\Str::slug($activity->title)) }}" class="space-y-6">
                                @csrf

                                {{-- Personal Information --}}
                                <div class="border-b border-gray-200 pb-4">
                                    <h3 class="text-lg font-semibold mb-3">Personal Information</h3>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium mb-2">
                                                First Name <span class="text-red-500">*</span>
                                            </label>
                                            <input
                                                type="text"
                                                name="first_name"
                                                value="{{ old('first_name') }}"
                                                required
                                                class="w-full px-4 py-2 border border-gray-300 rounded-md bg-white shadow-sm focus:ring-[#0a7ca1] focus:border-[#0a7ca1] text-gray-900 @error('first_name') border-red-500 @enderror"
                                            >
                                            @error('first_name')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium mb-2">
                                                Middle Initial
                                            </label>
                                            <input
                                                type="text"
                                                name="middle_initial"
                                                value="{{ old('middle_initial') }}"
                                                maxlength="1"
                                                pattern="[A-Za-z]"
                                                class="w-full px-4 py-2 border border-gray-300 rounded-md bg-white shadow-sm focus:ring-[#0a7ca1] focus:border-[#0a7ca1] text-gray-900 @error('middle_initial') border-red-500 @enderror"
                                                style="text-transform: uppercase;"
                                            >
                                            @error('middle_initial')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium mb-2">
                                                Last Name <span class="text-red-500">*</span>
                                            </label>
                                            <input
                                                type="text"
                                                name="last_name"
                                                value="{{ old('last_name') }}"
                                                required
                                                class="w-full px-4 py-2 border border-gray-300 rounded-md bg-white shadow-sm focus:ring-[#0a7ca1] focus:border-[#0a7ca1] text-gray-900 @error('last_name') border-red-500 @enderror"
                                            >
                                            @error('last_name')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium mb-2">
                                                Suffix (Optional)
                                            </label>
                                            <input
                                                type="text"
                                                name="suffix"
                                                value="{{ old('suffix') }}"
                                                class="w-full px-4 py-2 border border-gray-300 rounded-md bg-white shadow-sm focus:ring-[#0a7ca1] focus:border-[#0a7ca1] text-gray-900 @error('suffix') border-red-500 @enderror"
                                                placeholder="Jr., Sr., III, etc."
                                            >
                                            @error('suffix')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                                        <div>
                                            <label class="block text-sm font-medium mb-2">
                                                Sex / Gender <span class="text-red-500">*</span>
                                            </label>
                                            <select
                                                name="gender"
                                                required
                                                class="w-full px-4 py-2 border border-gray-300 rounded-md bg-white shadow-sm focus:ring-[#0a7ca1] focus:border-[#0a7ca1] text-gray-900 @error('gender') border-red-500 @enderror"
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
                                            <label class="block text-sm font-medium mb-2">
                                                Date of Birth <span class="text-red-500">*</span>
                                            </label>
                                            <input
                                                type="date"
                                                name="date_of_birth"
                                                value="{{ old('date_of_birth') }}"
                                                required
                                                max="{{ date('Y-m-d', strtotime('-1 day')) }}"
                                                min="1900-01-01"
                                                class="w-full px-4 py-2 border border-gray-300 rounded-md bg-white shadow-sm focus:ring-[#0a7ca1] focus:border-[#0a7ca1] text-gray-900 @error('date_of_birth') border-red-500 @enderror"
                                            >
                                            @error('date_of_birth')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                {{-- PWD Information --}}
                                <div class="border-b border-gray-200 pb-4">
                                    <h3 class="text-lg font-semibold mb-4">Person with Disability Information</h3>

                                    <div>
                                        <label class="block text-sm font-medium mb-2">
                                            Are you a person with disability? <span class="text-red-500">*</span>
                                        </label>
                                        <div class="flex flex-wrap gap-6">
                                            <label class="flex items-center">
                                                <input
                                                    type="radio"
                                                    name="is_pwd"
                                                    value="1"
                                                    {{ old('is_pwd') == '1' ? 'checked' : '' }}
                                                    class="h-4 w-4 text-[#0a7ca1] focus:ring-[#0a7ca1] border-gray-300"
                                                    onchange="toggleGuestAssistanceField()"
                                                >
                                                <span class="ml-2 text-sm">Yes</span>
                                            </label>
                                            <label class="flex items-center">
                                                <input
                                                    type="radio"
                                                    name="is_pwd"
                                                    value="0"
                                                    {{ old('is_pwd', '0') == '0' ? 'checked' : '' }}
                                                    class="h-4 w-4 text-[#0a7ca1] focus:ring-[#0a7ca1] border-gray-300"
                                                    onchange="toggleGuestAssistanceField()"
                                                >
                                                <span class="ml-2 text-sm">No</span>
                                            </label>
                                        </div>
                                        @error('is_pwd')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div id="guest_assistance_field" class="mt-4" style="display: none;">
                                        <label class="block text-sm font-medium mb-2">
                                            Do you require assistance? <span class="text-red-500">*</span>
                                        </label>
                                        <div class="flex flex-wrap gap-6">
                                            <label class="flex items-center">
                                                <input
                                                    type="radio"
                                                    name="requires_assistance"
                                                    value="1"
                                                    {{ old('requires_assistance') == '1' ? 'checked' : '' }}
                                                    class="h-4 w-4 text-[#0a7ca1] focus:ring-[#0a7ca1] border-gray-300"
                                                >
                                                <span class="ml-2 text-sm">Yes</span>
                                            </label>
                                            <label class="flex items-center">
                                                <input
                                                    type="radio"
                                                    name="requires_assistance"
                                                    value="0"
                                                    {{ old('requires_assistance') == '0' ? 'checked' : '' }}
                                                    class="h-4 w-4 text-[#0a7ca1] focus:ring-[#0a7ca1] border-gray-300"
                                                >
                                                <span class="ml-2 text-sm">No</span>
                                            </label>
                                        </div>
                                        @error('requires_assistance')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Professional Information --}}
                                <div class="border-b border-gray-200 pb-4">
                                    <h3 class="text-lg font-semibold mb-4">Professional Information</h3>

                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium mb-2">
                                                Office <span class="text-red-500">*</span>
                                            </label>
                                            <input
                                                type="text"
                                                name="office"
                                                value="{{ old('office') }}"
                                                required
                                                class="w-full px-4 py-2 border border-gray-300 rounded-md bg-white shadow-sm focus:ring-[#0a7ca1] focus:border-[#0a7ca1] text-gray-900 @error('office') border-red-500 @enderror"
                                            >
                                            @error('office')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium mb-2">
                                                Position <span class="text-red-500">*</span>
                                            </label>
                                            <input
                                                type="text"
                                                name="position"
                                                value="{{ old('position') }}"
                                                required
                                                class="w-full px-4 py-2 border border-gray-300 rounded-md bg-white shadow-sm focus:ring-[#0a7ca1] focus:border-[#0a7ca1] text-gray-900 @error('position') border-red-500 @enderror"
                                            >
                                            @error('position')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium mb-2">
                                                LGU/Organization <span class="text-red-500">*</span>
                                            </label>
                                            <input
                                                type="text"
                                                name="lgu_organization"
                                                value="{{ old('lgu_organization') }}"
                                                required
                                                class="w-full px-4 py-2 border border-gray-300 rounded-md bg-white shadow-sm focus:ring-[#0a7ca1] focus:border-[#0a7ca1] text-gray-900 @error('lgu_organization') border-red-500 @enderror"
                                            >
                                            @error('lgu_organization')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                {{-- Contact Information --}}
                                <div class="border-b border-gray-200 pb-4">
                                    <h3 class="text-lg font-semibold mb-4">Contact Information</h3>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium mb-2">
                                                Contact Number <span class="text-red-500">*</span>
                                            </label>
                                            <input
                                                type="tel"
                                                name="contact_number"
                                                value="{{ old('contact_number') }}"
                                                required
                                                pattern="[0-9+\-() ]+"
                                                class="w-full px-4 py-2 border border-gray-300 rounded-md bg-white shadow-sm focus:ring-[#0a7ca1] focus:border-[#0a7ca1] text-gray-900 @error('contact_number') border-red-500 @enderror"
                                                placeholder="09XX XXX XXXX"
                                            >
                                            @error('contact_number')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium mb-2">
                                                Email Address <span class="text-red-500">*</span>
                                            </label>
                                            <input
                                                type="email"
                                                name="email"
                                                value="{{ old('email') }}"
                                                required
                                                class="w-full px-4 py-2 border border-gray-300 rounded-md bg-white shadow-sm focus:ring-[#0a7ca1] focus:border-[#0a7ca1] text-gray-900 @error('email') border-red-500 @enderror"
                                                placeholder="your.email@example.com"
                                            >
                                            @error('email')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="mt-4">
                                        <label class="block text-sm font-medium mb-2">
                                            Dietary Restrictions
                                        </label>
                                        <textarea
                                            name="dietary_restrictions"
                                            rows="3"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-md bg-white shadow-sm focus:ring-[#0a7ca1] focus:border-[#0a7ca1] text-gray-900 @error('dietary_restrictions') border-red-500 @enderror"
                                            placeholder="Please specify any dietary restrictions or allergies"
                                        >{{ old('dietary_restrictions') }}</textarea>
                                        @error('dietary_restrictions')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Captcha --}}
                                <div>
                                    <h3 class="text-lg font-semibold mb-3">Security Check</h3>
                                    <p class="text-sm mb-3">
                                        Please enter the characters shown in the image below. This helps us prevent automated or fraudulent registrations.
                                    </p>

                                    <div class="flex flex-wrap items-center gap-4 mb-3">
                                        <img
                                            id="captcha-image"
                                            src="{{ route('captcha.image') }}"
                                            alt="CAPTCHA"
                                            class="rounded-md border border-[#c5b5a4] bg-[#E8E2DB] px-2 py-1"
                                        >
                                        <button
                                            type="button"
                                            onclick="reloadCaptcha()"
                                            class="inline-flex items-center px-3 py-2 rounded-md text-sm font-medium text-[#013141] bg-[#c5b5a4] hover:bg-[#b39f8a] transition-colors"
                                        >
                                            Reload Captcha
                                        </button>
                                    </div>

                                    <div class="max-w-xs">
                                        <label class="block text-sm font-medium mb-2">
                                            Enter the text shown above <span class="text-red-500">*</span>
                                        </label>
                                        <input
                                            type="text"
                                            name="captcha"
                                            required
                                            class="w-full px-4 py-2 border border-gray-300 rounded-md bg-white shadow-sm focus:ring-[#0a7ca1] focus:border-[#0a7ca1] text-gray-900 @error('captcha') border-red-500 @enderror"
                                            autocomplete="off"
                                        >
                                        @error('captcha')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <div>
                                    <button
                                        type="submit"
                                        class="w-full inline-flex justify-center items-center gap-2 px-4 py-3 rounded-md text-sm font-semibold shadow-md transition-colors duration-200"
                                        style="background-color: {{ $color3 }}; color: {{ $buttonIconColor }}; border: 2px solid {{ $buttonBorderColor }};"
                                        onmouseover="this.style.filter='brightness(0.95)'"
                                        onmouseout="this.style.filter='brightness(1)'"
                                    >
                                        <svg class="w-5 h-5" fill="none" stroke="{{ $buttonIconColor }}" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        <span>Submit Registration</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    @endguest

                    {{-- Authenticated registration confirmation modal --}}
                    @auth
                    <div
                        x-show="showUserConfirm"
                        x-cloak
                        class="fixed inset-0 z-40 flex items-center justify-center"
                        style="background-color: rgba(0, 0, 0, 0.5);"
                        x-transition:enter="ease-out duration-300"
                        x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100"
                        x-transition:leave="ease-in duration-200"
                        x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0"
                        @keydown.escape.window="showUserConfirm = false"
                    >
                        <div
                            class="bg-white rounded-lg shadow-xl p-6 max-w-md w-full mx-4"
                            @click.away="showUserConfirm = false"
                        >
                            <h3 class="text-lg font-semibold text-gray-900 mb-3">Confirm Registration</h3>
                            <p class="text-sm text-gray-700 mb-4">
                                You will be joining <strong>{{ $activity->title }}</strong> at
                                <strong>{{ $activity->venue }}</strong> on
                                <strong>{{ $activity->activity_date->format('F d, Y h:i A') }}</strong>.
                            </p>
                            <p class="text-sm text-gray-700 mb-4">
                                You are <strong>not required to fill up any form</strong>. We will use the information in your CAPDEVhub
                                profile for this registration.
                            </p>
                            <div class="flex justify-end gap-3 mt-4">
                                <button
                                    type="button"
                                    class="px-4 py-2 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-50"
                                    @click="showUserConfirm = false"
                                >
                                    Cancel
                                </button>
                                <form method="POST" action="{{ route('events.register', \Illuminate\Support\Str::slug($activity->title)) }}">
                                    @csrf
                                    <button
                                        type="submit"
                                        class="px-4 py-2 rounded-md text-sm font-semibold text-white"
                                        style="background-color: {{ $color3 }};"
                                        onmouseover="this.style.filter='brightness(0.95)'"
                                        onmouseout="this.style.filter='brightness(1)'"
                                    >
                                        Confirm Registration
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endauth
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

    <div id="fb-root"></div>
    <script async defer crossorigin="anonymous" src="https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v18.0" nonce="capdevhub"></script>

    <script>
        // Toggle PWD assistance field for guest registration form
        function toggleGuestAssistanceField() {
            var isPwd = document.querySelector('input[name="is_pwd"]:checked');
            var assistanceField = document.getElementById('guest_assistance_field');

            if (! assistanceField) {
                return;
            }

            if (isPwd && isPwd.value === '1') {
                assistanceField.style.display = 'block';
                assistanceField.querySelectorAll('input[type="radio"]').forEach(function (radio) {
                    radio.required = true;
                });
            } else {
                assistanceField.style.display = 'none';
                assistanceField.querySelectorAll('input[type="radio"]').forEach(function (radio) {
                    radio.required = false;
                    radio.checked = false;
                });
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            toggleGuestAssistanceField();
        });

        function openGuestRegistrationForm() {
            var formContainer = document.getElementById('guest-registration-form');
            if (! formContainer) {
                return;
            }
            formContainer.style.display = 'block';
            formContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }

        function reloadCaptcha() {
            var img = document.getElementById('captcha-image');
            if (img) {
                var baseSrc = '{{ route('captcha.image') }}';
                img.src = baseSrc + '?t=' + Date.now();
            }
        }

        // Store the social media URL to open after modal is closed
        let pendingSocialMediaUrl = null;

        // Fallback clipboard function for older browsers
        function fallbackCopyToClipboard(text) {
            const textArea = document.createElement('textarea');
            textArea.value = text;
            textArea.style.position = 'fixed';
            textArea.style.left = '-999999px';
            textArea.style.top = '-999999px';
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();
            
            try {
                const successful = document.execCommand('copy');
                document.body.removeChild(textArea);
                return successful;
            } catch (err) {
                document.body.removeChild(textArea);
                return false;
            }
        }

        // Copy to clipboard with fallback
        function copyToClipboard(text) {
            if (navigator.clipboard && window.isSecureContext) {
                return navigator.clipboard.writeText(text);
            } else {
                // Fallback for older browsers and non-HTTPS
                return new Promise(function(resolve, reject) {
                    if (fallbackCopyToClipboard(text)) {
                        resolve();
                    } else {
                        reject(new Error('Failed to copy'));
                    }
                });
            }
        }

        function shareToSocialMedia(platform, url) {
            // @ts-ignore - Blade directive, not a decorator
            const templates = @json($sharingTemplates);
            const randomIndex = Math.floor(Math.random() * templates.length);
            const sharingDetails = templates[randomIndex] || templates[0];

            // Copy sharing details to clipboard with fallback
            copyToClipboard(sharingDetails).then(function() {
                // Store the URL to open after modal is closed
                pendingSocialMediaUrl = url;
                
                // Show the modal with animation
                const modal = document.getElementById('sharing-modal');
                const modalContent = document.getElementById('sharing-modal-content');
                if (modal && modalContent) {
                    // Remove hidden class first
                    modal.classList.remove('hidden');
                    // Prevent body scroll on mobile
                    document.body.style.overflow = 'hidden';
                    // Force reflow to ensure the transition works
                    void modal.offsetWidth;
                    // Trigger animation by updating styles
                    setTimeout(function() {
                        modal.style.backgroundColor = 'rgba(0, 0, 0, 0.5)';
                        // Add webkit prefix for iOS Safari
                        modal.style.webkitBackdropFilter = 'blur(4px)';
                        modal.style.backdropFilter = 'blur(4px)';
                        modalContent.classList.remove('scale-95', 'opacity-0');
                        modalContent.classList.add('scale-100', 'opacity-100');
                    }, 10);
                }
            }).catch(function(err) {
                console.error('Failed to copy sharing details:', err);
                // If clipboard fails, still show modal and open the social media link
                pendingSocialMediaUrl = url;
                const modal = document.getElementById('sharing-modal');
                const modalContent = document.getElementById('sharing-modal-content');
                if (modal && modalContent) {
                    modal.classList.remove('hidden');
                    document.body.style.overflow = 'hidden';
                    void modal.offsetWidth;
                    setTimeout(function() {
                        modal.style.backgroundColor = 'rgba(0, 0, 0, 0.5)';
                        modal.style.webkitBackdropFilter = 'blur(4px)';
                        modal.style.backdropFilter = 'blur(4px)';
                        modalContent.classList.remove('scale-95', 'opacity-0');
                        modalContent.classList.add('scale-100', 'opacity-100');
                    }, 10);
                }
            });
        }

        function closeSharingModal() {
            const modal = document.getElementById('sharing-modal');
            const modalContent = document.getElementById('sharing-modal-content');
            
            if (modal && modalContent) {
                // Start fade out animation
                modal.style.backgroundColor = 'rgba(0, 0, 0, 0)';
                modal.style.webkitBackdropFilter = 'blur(0px)';
                modal.style.backdropFilter = 'blur(0px)';
                modalContent.classList.remove('scale-100', 'opacity-100');
                modalContent.classList.add('scale-95', 'opacity-0');
                
                // Restore body scroll
                document.body.style.overflow = '';
                
                // Wait for animation to complete before hiding
                setTimeout(function() {
                    modal.classList.add('hidden');
                    
                    // Open the social media URL if it was stored
                    if (pendingSocialMediaUrl) {
                        window.open(pendingSocialMediaUrl, '_blank');
                        pendingSocialMediaUrl = null;
                    }
                }, 300); // Match the transition duration
            } else {
                // Fallback if elements not found
                document.body.style.overflow = '';
                if (pendingSocialMediaUrl) {
                    window.open(pendingSocialMediaUrl, '_blank');
                    pendingSocialMediaUrl = null;
                }
            }
        }

        // Close modal when clicking/touching outside of it
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('sharing-modal');
            if (modal) {
                // Handle click events
                modal.addEventListener('click', function(e) {
                    if (e.target === modal) {
                        closeSharingModal();
                    }
                });
                // Handle touch events for mobile
                modal.addEventListener('touchend', function(e) {
                    if (e.target === modal) {
                        e.preventDefault();
                        closeSharingModal();
                    }
                });
            }
        });
    </script>
</body>
</html>
