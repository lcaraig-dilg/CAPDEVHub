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
            <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">
                {{-- Event Content (80% - 4 columns) --}}
                <div class="lg:col-span-4">
                    {{-- Event Title --}}
                    <h1 class="text-4xl font-bold mb-4" style="color: {{ $color3 }};">{{ $activity->title }}</h1>
                    
                    {{-- Event Details --}}
                    <div class="rounded-lg shadow-md p-6 mb-6 backdrop-blur-sm" style="background-color: {{ $color2Rgba }}; color: {{ $color2Text }};">
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
                                                        style="color: {{ $color3 }}"
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
                                        <svg class="w-6 h-6 flex-shrink-0 mt-1" style="color: {{ $color3 }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                                        <svg class="w-6 h-6 flex-shrink-0 mt-1" style="color: {{ $color3 }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                    <div class="rounded-lg shadow-md p-6 mb-6 backdrop-blur-sm" style="background-color: {{ $color2Rgba }}; color: {{ $color2Text }};">
                        <h2 class="text-2xl font-bold mb-4" style="color: {{ $color3 }};">Event Description</h2>
                        <div class="ql-editor prose max-w-none">
                            {!! $activity->description !!}
                        </div>
                    </div>

                    {{-- Share Section --}}
                    <div class="rounded-lg shadow-md p-6 backdrop-blur-sm" style="background-color: {{ $color2Rgba }}; color: {{ $color2Text }};">
                        <h2 class="text-2xl font-bold mb-4" style="color: {{ $color3 }};">Share this event</h2>
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
                            class="inline-flex items-center gap-2 px-4 py-2 text-white rounded-md transition-colors duration-200"
                            style="background-color: {{ $activity->accent_color_3 ?? '#FAB95B' }};"
                            onmouseover="this.style.backgroundColor='{{ $activity->accent_color_3 ?? '#FAB95B' }}'; this.style.filter='brightness(0.95)';"
                            onmouseout="this.style.backgroundColor='{{ $activity->accent_color_3 ?? '#FAB95B' }}'; this.style.filter='brightness(1)';"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            </svg>
                            <span id="copy-link-text">Copy Link</span>
                        </button>
                    </div>
                </div>
                </div>

                {{-- LGCDD / DILG-NCR Ads Sidebar (20% - 1 column) --}}
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
