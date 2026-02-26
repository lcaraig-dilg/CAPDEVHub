<div class="bg-gray-100 rounded-lg p-6 transition-all duration-300">
    {{-- Success Message --}}
    @if (session()->has('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative transition-all duration-300" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    {{-- Error Message --}}
    @if (session()->has('error'))
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative transition-all duration-300" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    {{-- Header with Create Button and Batch Delete --}}
    <div class="mb-6 flex justify-between items-center">
        <h1 class="text-2xl font-bold">Activities Management</h1>
        <div class="flex gap-3">
            <div
                x-data
                x-show="$wire.selectedActivities.length > 0"
                x-cloak
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform translate-x-4 scale-95"
                x-transition:enter-end="opacity-100 transform translate-x-0 scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 transform translate-x-0 scale-100"
                x-transition:leave-end="opacity-0 transform translate-x-4 scale-95"
                style="display: none;"
            >
                <button 
                    wire:click="batchDelete"
                    wire:confirm="Are you sure you want to delete {{ count($selectedActivities) }} selected activity(ies)?"
                    class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-all duration-200 font-medium flex items-center gap-2 shadow-lg"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    Delete Selected ({{ count($selectedActivities) }})
                </button>
            </div>
            <button 
                wire:click="openCreateModal"
                class="px-4 py-2 bg-[#FAB95B] text-white rounded-md hover:bg-[#F9A84D] rounded-md transition-all duration-200 font-medium flex items-center gap-2"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add New Activity
            </button>
        </div>
    </div>

    {{-- Search Section --}}
    <div class="bg-gray-50 shadow rounded-lg p-4 mb-6 transition-all duration-300">
        <label class="block text-sm font-medium text-gray-700 mb-3">Search:</label>
        <input 
            type="text" 
            wire:model.live.debounce.300ms="search"
            placeholder="Search activities by title, venue, or link..."
            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
        >
    </div>

    {{-- Activities Table --}}
    <div class="bg-gray-50 shadow rounded-lg overflow-hidden transition-all duration-300">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-[#c5b5a4]">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-[#013141] uppercase tracking-wider">
                            <input 
                                type="checkbox" 
                                wire:model.live="selectAll"
                                class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2"
                            >
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-[#013141] uppercase tracking-wider">Title</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-[#013141] uppercase tracking-wider">Venue</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-[#013141] uppercase tracking-wider">Activity Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-[#013141] uppercase tracking-wider">Registration Span</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-[#013141] uppercase tracking-wider">Shareable Link</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-[#013141] uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-gray-50 divide-y divide-gray-200">
                    @forelse ($activities as $activity)
                        <tr class="hover:bg-gray-100 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input 
                                    type="checkbox" 
                                    wire:model.live="selectedActivities"
                                    value="{{ $activity->id }}"
                                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2"
                                >
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <a 
                                    href="{{ route('events.show', \Illuminate\Support\Str::slug($activity->title)) }}"
                                    target="_blank"
                                    class="text-[#0a7ca1] hover:text-[#013141] hover:underline transition-colors duration-200 font-medium"
                                >
                                    {{ $activity->title }}
                                </a>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $activity->venue }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $activity->activity_date->format('M d, Y h:i A') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $activity->registration_start->format('M d, Y') }} - {{ $activity->registration_end->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                <div class="flex items-center gap-3">
                                    <div 
                                        class="flex-shrink-0 cursor-pointer"
                                        x-data="{ showConfirm: false }"
                                        @click="
                                            showConfirm = true;
                                        "
                                    >
                                        <div class="bg-white p-1 rounded border inline-block hover:border-[#0a7ca1] transition-colors qr-code-container" style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                                            {!! $this->getQrCode($activity) !!}
                                        </div>
                                        
                                        <!-- Confirmation Modal -->
                                        <div 
                                            x-show="showConfirm"
                                            x-cloak
                                            class="fixed inset-0 z-50 flex items-center justify-center"
                                            style="background-color: rgba(0, 0, 0, 0.5);"
                                            x-transition:enter="ease-out duration-300"
                                            x-transition:enter-start="opacity-0"
                                            x-transition:enter-end="opacity-100"
                                            x-transition:leave="ease-in duration-200"
                                            x-transition:leave-start="opacity-100"
                                            x-transition:leave-end="opacity-0"
                                            @click.away="showConfirm = false"
                                        >
                                            <div 
                                                class="bg-white rounded-lg shadow-xl p-6 max-w-md w-full mx-4"
                                                @click.stop
                                                x-transition:enter="ease-out duration-300"
                                                x-transition:enter-start="opacity-0 scale-95"
                                                x-transition:enter-end="opacity-100 scale-100"
                                                x-transition:leave="ease-in duration-200"
                                                x-transition:leave-start="opacity-100 scale-100"
                                                x-transition:leave-end="opacity-0 scale-95"
                                            >
                                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Download QR Code</h3>
                                                <p class="text-sm text-gray-600 mb-6">You are about to download the QR code of the event.</p>
                                                <div class="flex justify-end gap-3">
                                                    <button
                                                        @click="showConfirm = false"
                                                        class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors duration-200"
                                                    >
                                                        Close
                                                    </button>
                                                    <button
                                                        @click="
                                                            const parentDiv = $el.closest('[x-data]');
                                                            const qrContainer = parentDiv.querySelector('.bg-white.p-1');
                                                            const svg = qrContainer ? qrContainer.querySelector('svg') : null;
                                                            if (!svg) {
                                                                showConfirm = false;
                                                                return;
                                                            }
                                                            
                                                            const serializer = new XMLSerializer();
                                                            const svgString = serializer.serializeToString(svg);
                                                            const svgBlob = new Blob([svgString], { type: 'image/svg+xml;charset=utf-8' });
                                                            const url = URL.createObjectURL(svgBlob);
                                                            const img = new Image();
                                                            
                                                            img.onload = () => {
                                                                const canvas = document.createElement('canvas');
                                                                canvas.width = img.width;
                                                                canvas.height = img.height;
                                                                const ctx = canvas.getContext('2d');
                                                                ctx.drawImage(img, 0, 0);
                                                                URL.revokeObjectURL(url);
                                                                
                                                                canvas.toBlob((blob) => {
                                                                    if (!blob) {
                                                                        showConfirm = false;
                                                                        return;
                                                                    }
                                                                    const pngUrl = URL.createObjectURL(blob);
                                                                    const link = document.createElement('a');
                                                                    link.href = pngUrl;
                                                                    link.download = 'activity-qr-{{ \Illuminate\Support\Str::slug($activity->title) }}.png';
                                                                    document.body.appendChild(link);
                                                                    link.click();
                                                                    document.body.removeChild(link);
                                                                    URL.revokeObjectURL(pngUrl);
                                                                    showConfirm = false;
                                                                }, 'image/png');
                                                            };
                                                            
                                                            img.onerror = () => {
                                                                showConfirm = false;
                                                            };
                                                            
                                                            img.src = url;
                                                        "
                                                        class="px-4 py-2 bg-[#FAB95B] text-white rounded-md hover:bg-[#F9A84D] transition-colors duration-200"
                                                    >
                                                        Continue
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <a href="{{ route('events.show', \Illuminate\Support\Str::slug($activity->title)) }}" target="_blank" class="text-[#0a7ca1] hover:text-[#013141] hover:underline break-all">
                                        {{ route('events.show', \Illuminate\Support\Str::slug($activity->title)) }}
                                    </a>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end gap-2">
                                    <button 
                                        wire:click="openEditModal({{ $activity->id }})"
                                        class="text-[#0a7ca1] hover:text-[#013141] transition-colors duration-200 p-1 rounded hover:bg-[#0a7ca1] hover:bg-opacity-10"
                                        title="Edit Activity"
                                    >
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>
                                    <button 
                                        wire:click="delete({{ $activity->id }})"
                                        wire:confirm="Are you sure you want to delete this activity?"
                                        class="text-red-600 hover:text-red-900 transition-colors duration-200 p-1 rounded hover:bg-red-50"
                                        title="Delete Activity"
                                    >
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                                No activities found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
            {{ $activities->links() }}
        </div>
    </div>

    {{-- Create/Edit Modal --}}
    <div 
        x-data="{ show: @entangle('showModal') }"
        x-show="show"
        x-cloak
        class="fixed inset-0 overflow-y-auto h-full w-full z-50"
        style="display: none; background-color: rgba(0, 0, 0, 0.25); backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px);"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
        <div 
            class="relative top-10 mx-auto p-5 border w-11/12 md:w-4/5 lg:w-3/4 xl:w-2/3 shadow-2xl rounded-lg bg-white max-h-[90vh] transform overflow-y-auto"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-8 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
            x-transition:leave-end="opacity-0 translate-y-8 scale-95"
        >
            <div class="mb-4 sticky top-0 bg-white pb-4 border-b">
                <h3 class="text-lg font-bold text-gray-900">
                    {{ $editingActivityId ? 'Edit Activity' : 'Create New Activity' }}
                </h3>
            </div>

            <form wire:submit.prevent="save">
                <div class="space-y-6" wire:loading.class="opacity-50 pointer-events-none" wire:target="save">
                    {{-- Activity Title --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Activity Title <span class="text-red-500">*</span></label>
                        <input 
                            type="text" 
                            wire:model="formData.title"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        >
                        @error('formData.title') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    {{-- Activity Venue --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Activity Venue <span class="text-red-500">*</span></label>
                        <input 
                            type="text" 
                            wire:model="formData.venue"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        >
                        @error('formData.venue') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    {{-- Google Maps Link (Optional) --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Google Maps Link (Optional)</label>
                        <input 
                            type="url" 
                            wire:model="formData.venue_google_maps_link"
                            placeholder="https://maps.google.com/..."
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        >
                        @error('formData.venue_google_maps_link') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    {{-- Date & Time of Activity --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date & Time of Activity <span class="text-red-500">*</span></label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">Date</label>
                                <input 
                                    type="date" 
                                    wire:model="formData.activity_date_date"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                >
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">Time</label>
                                <select 
                                    wire:model="formData.activity_date_time"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                >
                                    <option value="">Select Time</option>
                                    @for ($h = 0; $h < 24; $h++)
                                        @php
                                            $value = sprintf('%02d:00', $h);
                                            $label = \Carbon\Carbon::createFromTime($h, 0)->format('g:i A');
                                        @endphp
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        @error('formData.activity_date') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    {{-- Registration Span --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Registration Start <span class="text-red-500">*</span></label>
                            <input 
                                type="date" 
                                wire:model="formData.registration_start"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            >
                            @error('formData.registration_start') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Registration End <span class="text-red-500">*</span></label>
                            <input 
                                type="date" 
                                wire:model="formData.registration_end"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            >
                            @error('formData.registration_end') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    {{-- Shareable Link --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Activity Shareable Link <span class="text-red-500">*</span></label>
                        <div class="flex items-center">
                            <span class="px-3 py-2 bg-gray-100 border border-r-0 border-gray-300 rounded-l-md text-gray-700">https://website.name/</span>
                            <input 
                                type="text" 
                                wire:model="formData.shareable_link"
                                placeholder="activity-link"
                                class="flex-1 px-3 py-2 border border-gray-300 rounded-r-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            >
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Only letters, numbers, hyphens, and underscores allowed</p>
                        @error('formData.shareable_link') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    {{-- Banner Image --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Activity Banner Image</label>
                        <input 
                            type="file" 
                            wire:model="formData.banner_image"
                            accept="image/*"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        >
                        @if($bannerImagePreview)
                            <div class="mt-2">
                                <img src="{{ $bannerImagePreview }}" alt="Banner preview" class="max-w-xs h-auto rounded-md">
                            </div>
                        @endif
                        @error('formData.banner_image') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    {{-- Activity Description (Rich Text Editor) --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Activity Description</label>
                        <div wire:ignore>
                            <div id="description-editor" style="min-height: 300px;" class="border border-gray-300 rounded-md"></div>
                        </div>
                        <textarea 
                            wire:model="formData.description" 
                            id="description-hidden" 
                            style="display: none;"
                        ></textarea>
                        @error('formData.description') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    {{-- Accent Colors --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Site Accent Colors</label>
                        
                        {{-- Color Palette Selection --}}
                        <div class="mb-4">
                            <label class="block text-xs text-gray-500 mb-1">Color Palette</label>
                            <select 
                                wire:model.live="formData.color_palette"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            >
                                <option value="default">Default Color Palette</option>
                                <option value="plain">Plain Color Palette</option>
                                <option value="custom">Custom Colors</option>
                            </select>
                            @error('formData.color_palette') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            
                            {{-- Palette Description --}}
                            <div class="mt-2 text-xs text-gray-500">
                                @if($formData['color_palette'] === 'default')
                                    <p>Color 1: #013141 (dark teal), Color 2: #0A7CA1 (light blue/cyan), Color 3: #FAB95B (orange/yellow)</p>
                                @elseif($formData['color_palette'] === 'plain')
                                    <p>Color 1: White, Color 2: Gray-100, Color 3: Black</p>
                                @else
                                    <p>Choose your own custom colors for Color 1, 2, and 3</p>
                                @endif
                            </div>
                        </div>

                        {{-- Color Pickers (Only show for custom) --}}
                        @if($formData['color_palette'] === 'custom')
                            <div class="grid grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">Color 1</label>
                                    <input 
                                        type="color" 
                                        wire:model="formData.accent_color_1"
                                        class="w-full h-10 border border-gray-300 rounded-md cursor-pointer"
                                    >
                                    @error('formData.accent_color_1') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">Color 2</label>
                                    <input 
                                        type="color" 
                                        wire:model="formData.accent_color_2"
                                        class="w-full h-10 border border-gray-300 rounded-md cursor-pointer"
                                    >
                                    @error('formData.accent_color_2') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">Color 3</label>
                                    <input 
                                        type="color" 
                                        wire:model="formData.accent_color_3"
                                        class="w-full h-10 border border-gray-300 rounded-md cursor-pointer"
                                    >
                                    @error('formData.accent_color_3') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="mt-6 flex justify-end space-x-3 sticky bottom-0 bg-white pt-4 border-t">
                    <button 
                        type="button"
                        wire:click="closeModal"
                        wire:loading.attr="disabled"
                        wire:target="save"
                        class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Cancel
                    </button>
                    <button 
                        type="submit"
                        wire:loading.attr="disabled"
                        wire:target="save"
                            class="px-4 py-2 bg-[#FAB95B] text-white rounded-md hover:bg-[#F9A84D] transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2 min-w-[120px]"
                    >
                        <span wire:loading.remove wire:target="save" class="flex items-center justify-center gap-2">
                            @if($editingActivityId)
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            @else
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                            @endif
                            {{ $editingActivityId ? 'Update' : 'Create' }}
                        </span>
                        <span wire:loading wire:target="save" class="flex items-center justify-center gap-2">
                            <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            {{ $editingActivityId ? 'Updating...' : 'Creating...' }}
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Activity Details Modal --}}
    <div 
        x-data="{ show: @entangle('showDetailsModal') }"
        x-show="show"
        x-cloak
        class="fixed inset-0 overflow-y-auto h-full w-full z-50"
        style="display: none; background-color: rgba(0, 0, 0, 0.25); backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px);"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
        <div 
            class="relative top-10 mx-auto p-5 border w-11/12 md:w-4/5 lg:w-3/4 xl:w-2/3 shadow-2xl rounded-lg bg-white max-h-[90vh] transform overflow-y-auto"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-8 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
            x-transition:leave-end="opacity-0 translate-y-8 scale-95"
        >
            @if($viewingActivity)
                <div class="mb-4 sticky top-0 bg-white pb-4 border-b">
                    <h3 class="text-lg font-bold text-gray-900">{{ $viewingActivity->title }}</h3>
                </div>

                <div class="space-y-6">
                    {{-- Registration Link Section --}}
                    <div class="border-b pb-4">
                        <h4 class="text-md font-semibold text-gray-900 mb-3">Registration Link</h4>
                        <div class="flex flex-col gap-4">
                            <div>
                                <p class="text-sm text-gray-600 mb-2">Shareable Link:</p>
                                <a href="{{ route('events.show', \Illuminate\Support\Str::slug($viewingActivity->title)) }}" target="_blank" class="text-[#0a7ca1] hover:text-[#013141] hover:underline break-all">
                                    {{ route('events.show', \Illuminate\Support\Str::slug($viewingActivity->title)) }}
                                </a>
                            </div>

                            <div 
                                class="flex flex-col items-center gap-2"
                                x-data
                            >
                                <p class="text-sm text-gray-600">QR Code:</p>
                                <div class="bg-white p-2 rounded border inline-block">
                                    {!! $this->getQrCode($viewingActivity) !!}
                                </div>
                                <button
                                    type="button"
                                    class="mt-1 inline-flex items-center gap-1 text-xs px-3 py-1 rounded-md bg-[#FAB95B] text-white hover:bg-[#F9A84D] transition-colors duration-200"
                                    @click="
                                        const container = $el.previousElementSibling;
                                        const svg = container.querySelector('svg');
                                        if (!svg) return;

                                        const serializer = new XMLSerializer();
                                        const svgString = serializer.serializeToString(svg);
                                        const svgBlob = new Blob([svgString], { type: 'image/svg+xml;charset=utf-8' });
                                        const url = URL.createObjectURL(svgBlob);
                                        const img = new Image();

                                        img.onload = () => {
                                            const canvas = document.createElement('canvas');
                                            canvas.width = img.width;
                                            canvas.height = img.height;
                                            const ctx = canvas.getContext('2d');
                                            ctx.drawImage(img, 0, 0);
                                            URL.revokeObjectURL(url);

                                            canvas.toBlob((blob) => {
                                                if (!blob) return;
                                                const pngUrl = URL.createObjectURL(blob);
                                                const link = document.createElement('a');
                                                link.href = pngUrl;
                                                link.download = 'activity-qr-{{ \Illuminate\Support\Str::slug($viewingActivity->title) }}.png';
                                                document.body.appendChild(link);
                                                link.click();
                                                document.body.removeChild(link);
                                                URL.revokeObjectURL(pngUrl);
                                            }, 'image/png');
                                        };

                                        img.src = url;
                                    "
                                >
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5m0 0l5-5m-5 5V4"></path>
                                    </svg>
                                    <span>Save QR as image</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Banner Image --}}
                    @if($viewingActivity->banner_image)
                        <div class="border-b pb-4">
                            <h4 class="text-md font-semibold text-gray-900 mb-3">Banner Image</h4>
                            <img src="{{ asset('storage/' . $viewingActivity->banner_image) }}" alt="Activity Banner" class="max-w-full h-auto rounded-md">
                        </div>
                    @endif

                    {{-- Registration Span --}}
                    <div class="border-b pb-4">
                        <h4 class="text-md font-semibold text-gray-900 mb-2">Registration Span</h4>
                        <p class="text-sm text-gray-600">
                            {{ $viewingActivity->registration_start->format('F d, Y') }} - {{ $viewingActivity->registration_end->format('F d, Y') }}
                        </p>
                    </div>

                    {{-- Activity Description --}}
                    <div>
                        <h4 class="text-md font-semibold text-gray-900 mb-3">Activity Description</h4>
                        <div class="prose max-w-none">
                            {!! $viewingActivity->description !!}
                        </div>
                    </div>

                    {{-- Registration Responses (Placeholder) --}}
                    <div class="border-t pt-4">
                        <h4 class="text-md font-semibold text-gray-900 mb-3">Registration Responses</h4>
                        <p class="text-sm text-gray-500 italic">This section will be implemented later.</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Quill.js Rich Text Editor with image resize & drag-drop modules --}}
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/quill-image-resize-module@3.0.0/image-resize.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/quill-image-drop-module@1.0.3/image-drop.min.js"></script>
    <script>
        (function() {
            // Use globals so state persists across Livewire re-renders
            window.activityQuill = window.activityQuill || {
                instance: null,
                initialized: false,
            };

            function initQuill() {
                const editorElement = document.getElementById('description-editor');
                if (editorElement && !window.activityQuill.initialized) {
                    // Clear any existing content
                    editorElement.innerHTML = '';
                    
                    const quillInstance = new Quill('#description-editor', {
                        theme: 'snow',
                        modules: {
                            toolbar: [
                                [{ header: [1, 2, 3, 4, 5, 6, false] }],
                                [{ font: [] }],
                                [{ size: [] }],
                                ['bold', 'italic', 'underline', 'strike'],
                                [{ color: [] }, { background: [] }],
                                [{ script: 'sub' }, { script: 'super' }],
                                [{ list: 'ordered' }, { list: 'bullet' }],
                                [{ indent: '-1' }, { indent: '+1' }],
                                [{ align: [] }],
                                ['link', 'image', 'video'],
                                ['clean']
                            ],
                            imageResize: {
                                modules: [ 'Resize', 'DisplaySize', 'Toolbar' ],
                            },
                            imageDrop: true
                        },
                    });

                    window.activityQuill.instance = quillInstance;

                    // Update Livewire on text change
                    quillInstance.on('text-change', function() {
                        const content = quillInstance.root.innerHTML;
                        const hiddenInput = document.getElementById('description-hidden');
                        if (hiddenInput) {
                            hiddenInput.value = content;
                        }
                        @this.set('formData.description', content);
                    });

                    // Load existing content if editing from the hidden textarea (Livewire-bound)
                    const hiddenInput = document.getElementById('description-hidden');
                    if (hiddenInput && hiddenInput.value) {
                        quillInstance.root.innerHTML = hiddenInput.value;
                    }

                    window.activityQuill.initialized = true;
                }
            }

            function destroyQuill() {
                const qi = window.activityQuill.instance;
                if (qi) {
                    const editorElement = document.getElementById('description-editor');
                    if (editorElement) {
                        editorElement.innerHTML = '';
                    }
                    window.activityQuill.instance = null;
                    window.activityQuill.initialized = false;
                }
            }

            // Watch for modal visibility
            document.addEventListener('DOMContentLoaded', function() {
                setInterval(function() {
                    const modal = document.querySelector('[x-data*="showModal"]');
                    if (modal) {
                        const isVisible = modal.style.display !== 'none' && !modal.hasAttribute('x-cloak');
                        if (isVisible && !window.activityQuill.initialized) {
                            setTimeout(initQuill, 300);
                        } else if (!isVisible && isInitialized) {
                            destroyQuill();
                        }
                    }
                }, 500);
            });

            // Initialize / sync on Livewire updates (e.g. when opening Edit)
            document.addEventListener('livewire:init', () => {
                Livewire.hook('morph.updated', () => {
                    setTimeout(() => {
                        const editorElement = document.getElementById('description-editor');
                        const hiddenInput = document.getElementById('description-hidden');
                        const qi = window.activityQuill.instance;

                        if (editorElement && editorElement.offsetParent !== null) {
                            // First time: initialize Quill
                            if (!window.activityQuill.initialized) {
                                initQuill();
                            }

                            // After init, always sync from hidden textarea (Livewire formData.description)
                            if (window.activityQuill.initialized && qi && hiddenInput) {
                                qi.root.innerHTML = hiddenInput.value || '';
                            }
                        }
                    }, 100);
                });
            });
        })();
    </script>
    
    <style>
        .qr-code-container svg {
            width: 80px !important;
            height: 80px !important;
            max-width: 80px !important;
            max-height: 80px !important;
        }
    </style>
</div>
