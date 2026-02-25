<div class="bg-gray-100 rounded-lg p-6 transition-all duration-300">
    {{-- Success Message --}}
    @if (session()->has('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative transition-all duration-300 animate-fade-in" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    {{-- Error Message --}}
    @if (session()->has('error'))
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative transition-all duration-300 animate-fade-in" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    {{-- Header with Create Button and Batch Delete --}}
    <div class="mb-6 flex justify-between items-center">
        <h1 class="text-2xl font-bold">Users Management</h1>
        <div class="flex gap-3">
            <div
                x-data
                x-show="$wire.selectedUsers.length > 0"
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
                    wire:confirm="Are you sure you want to delete {{ count($selectedUsers) }} selected user(s)?"
                    class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-all duration-200 font-medium flex items-center gap-2 shadow-lg"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    Delete Selected ({{ count($selectedUsers) }})
                </button>
            </div>
            <button 
                wire:click="openCreateModal"
                class="px-4 py-2 bg-blue-900 text-white rounded-md hover:bg-[#0a7ba1] transition-all duration-200 font-medium flex items-center gap-2"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add New User
            </button>
        </div>
    </div>

    {{-- Search and Filter Section --}}
    <div class="bg-gray-50 shadow rounded-lg p-4 mb-6 transition-all duration-300">
        <label class="block text-sm font-medium text-gray-700 mb-3">Filter By:</label>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="md:col-span-2">
                <input 
                    type="text" 
                    wire:model.live.debounce.300ms="search"
                    placeholder="Search users..."
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                >
            </div>
            <div>
                <select 
                    wire:model.live="searchField"
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                >
                    <option value="all">All Fields</option>
                    <option value="email">Email</option>
                    <option value="username">Username</option>
                    <option value="name">Name</option>
                    <option value="office">Office</option>
                    <option value="lgu_organization">LGU Organization</option>
                </select>
            </div>
        </div>
    </div>

    {{-- Users Table --}}
    <div class="bg-gray-50 shadow rounded-lg overflow-hidden transition-all duration-300">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-blue-900">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
                            <input 
                                type="checkbox" 
                                wire:model.live="selectAll"
                                class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2"
                            >
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Username</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Office</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">LGU Organization</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Role</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-white uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-gray-50 divide-y divide-gray-200">
                    @forelse ($users as $user)
                        <tr class="hover:bg-gray-100 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input 
                                    type="checkbox" 
                                    wire:model.live="selectedUsers"
                                    value="{{ $user->id }}"
                                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2"
                                >
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $user->full_name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $user->email }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $user->username ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $user->office }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $user->lgu_organization }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    @if($user->role === 'super_admin') bg-purple-100 text-purple-800
                                    @elseif($user->role === 'admin') bg-blue-100 text-blue-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ strtoupper(str_replace('_', ' ', $user->role)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end gap-2">
                                    <button 
                                        wire:click="openEditModal({{ $user->id }})"
                                        class="text-blue-600 hover:text-blue-900 transition-colors duration-200 p-1 rounded hover:bg-blue-50"
                                        title="Edit User"
                                    >
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>
                                    @if ($user->role !== 'super_admin' && $user->id !== auth()->id())
                                        <button 
                                            wire:click="delete({{ $user->id }})"
                                            wire:confirm="Are you sure you want to delete this user?"
                                            class="text-red-600 hover:text-red-900 transition-colors duration-200 p-1 rounded hover:bg-red-50"
                                            title="Delete User"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">
                                No users found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
            {{ $users->links() }}
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
            class="relative top-10 mx-auto p-5 border w-11/12 md:w-4/5 lg:w-3/4 xl:w-2/3 shadow-2xl rounded-lg bg-white max-h-[90vh] transform"
            wire:click.stop
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-8 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
            x-transition:leave-end="opacity-0 translate-y-8 scale-95"
        >
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-gray-900">
                        {{ $editingUserId ? 'Edit User' : 'Create New User' }}
                    </h3>
                    <button 
                        wire:click="closeModal" 
                        class="text-gray-400 hover:text-gray-600 transition-colors duration-200 p-1 rounded hover:bg-gray-100"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form wire:submit.prevent="save">
                    <div class="space-y-6 max-h-[70vh] overflow-y-auto pr-2" wire:loading.class="opacity-50 pointer-events-none" wire:target="save">
                        {{-- Personal Information Section --}}
                        <div class="border-b border-gray-200 pb-4">
                            <h4 class="text-md font-semibold text-gray-900 mb-4">Personal Information</h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">First Name <span class="text-red-500">*</span></label>
                                    <input 
                                        type="text" 
                                        wire:model="formData.first_name"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    >
                                    @error('formData.first_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Middle Initial</label>
                                    <input 
                                        type="text" 
                                        wire:model="formData.middle_initial"
                                        maxlength="1"
                                        style="text-transform: uppercase;"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    >
                                    @error('formData.middle_initial') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Last Name <span class="text-red-500">*</span></label>
                                    <input 
                                        type="text" 
                                        wire:model="formData.last_name"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    >
                                    @error('formData.last_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Suffix</label>
                                    <input 
                                        type="text" 
                                        wire:model="formData.suffix"
                                        placeholder="Jr., Sr., III, etc."
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    >
                                    @error('formData.suffix') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Gender <span class="text-red-500">*</span></label>
                                    <select 
                                        wire:model="formData.gender"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    >
                                        <option value="">Select Gender</option>
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                        <option value="Prefer not to say">Prefer not to say</option>
                                    </select>
                                    @error('formData.gender') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Date of Birth <span class="text-red-500">*</span></label>
                                    <input 
                                        type="date" 
                                        wire:model="formData.date_of_birth"
                                        max="{{ date('Y-m-d', strtotime('-1 day')) }}"
                                        min="1900-01-01"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    >
                                    @error('formData.date_of_birth') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Age (Auto-computed)</label>
                                    <input 
                                        type="number"
                                        placeholder="Auto-computed after submitted"
                                        wire:model="formData.age"
                                        readonly
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100 text-gray-600"
                                    >
                                </div>
                            </div>
                        </div>

                        {{-- PWD Information Section --}}
                        <div class="border-b border-gray-200 pb-4">
                            <h4 class="text-md font-semibold text-gray-900 mb-4">Person with Disability Information</h4>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Are you a person with disability? <span class="text-red-500">*</span></label>
                                <div class="flex space-x-6">
                                    <label class="flex items-center">
                                        <input 
                                            type="radio" 
                                            wire:model="formData.is_pwd"
                                            value="1"
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                                        >
                                        <span class="ml-2 text-sm text-gray-700">Yes</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input 
                                            type="radio" 
                                            wire:model="formData.is_pwd"
                                            value="0"
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                                        >
                                        <span class="ml-2 text-sm text-gray-700">No</span>
                                    </label>
                                </div>
                                @error('formData.is_pwd') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            @if($formData['is_pwd'] == '1')
                                <div class="mt-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Do you require assistance? <span class="text-red-500">*</span></label>
                                    <div class="flex space-x-6">
                                        <label class="flex items-center">
                                            <input 
                                                type="radio" 
                                                wire:model="formData.requires_assistance"
                                                value="1"
                                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                                            >
                                            <span class="ml-2 text-sm text-gray-700">Yes</span>
                                        </label>
                                        <label class="flex items-center">
                                            <input 
                                                type="radio" 
                                                wire:model="formData.requires_assistance"
                                                value="0"
                                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                                            >
                                            <span class="ml-2 text-sm text-gray-700">No</span>
                                        </label>
                                    </div>
                                    @error('formData.requires_assistance') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                            @endif
                        </div>

                        {{-- Professional Information Section --}}
                        <div class="border-b border-gray-200 pb-4">
                            <h4 class="text-md font-semibold text-gray-900 mb-4">Professional Information</h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Office <span class="text-red-500">*</span></label>
                                    <input 
                                        type="text" 
                                        wire:model="formData.office"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    >
                                    @error('formData.office') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Position <span class="text-red-500">*</span></label>
                                    <input 
                                        type="text" 
                                        wire:model="formData.position"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    >
                                    @error('formData.position') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">LGU/Organization <span class="text-red-500">*</span></label>
                                    <input 
                                        type="text" 
                                        wire:model="formData.lgu_organization"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    >
                                    @error('formData.lgu_organization') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Contact Information Section --}}
                        <div class="border-b border-gray-200 pb-4">
                            <h4 class="text-md font-semibold text-gray-900 mb-4">Contact Information</h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Contact Number <span class="text-red-500">*</span></label>
                                    <input 
                                        type="tel" 
                                        wire:model="formData.contact_number"
                                        placeholder="09XX XXX XXXX"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    >
                                    @error('formData.contact_number') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Email Address <span class="text-red-500">*</span></label>
                                    <input 
                                        type="email" 
                                        wire:model="formData.email"
                                        placeholder="your.email@example.com"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    >
                                    @error('formData.email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="mt-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                                <input 
                                    type="text" 
                                    wire:model="formData.username"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                >
                                @error('formData.username') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                <p class="mt-1 text-xs text-gray-500">Leave blank to auto-generate from email</p>
                            </div>
                            <div class="mt-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Dietary Restrictions</label>
                                <textarea 
                                    wire:model="formData.dietary_restrictions"
                                    rows="3"
                                    placeholder="Please specify any dietary restrictions or allergies"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                ></textarea>
                                @error('formData.dietary_restrictions') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        {{-- Account Security Section --}}
                        <div>
                            <h4 class="text-md font-semibold text-gray-900 mb-4">Account Security</h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Password @if(!$editingUserId)<span class="text-red-500">*</span>@endif
                                    </label>
                                    <input 
                                        type="password" 
                                        wire:model="formData.password"
                                        minlength="8"
                                        placeholder="{{ $editingUserId ? 'Leave blank to keep current password' : 'Minimum 8 characters' }}"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    >
                                    @error('formData.password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    @if(!$editingUserId)
                                        <p class="mt-1 text-xs text-gray-500">Password must be at least 8 characters long</p>
                                    @endif
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Confirm Password @if(!$editingUserId)<span class="text-red-500">*</span>@endif
                                    </label>
                                    <input 
                                        type="password" 
                                        wire:model="formData.password_confirmation"
                                        minlength="8"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    >
                                </div>
                            </div>
                            <div class="mt-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Role <span class="text-red-500">*</span></label>
                                <select 
                                    wire:model="formData.role"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                >
                                    <option value="user">User</option>
                                    <option value="admin">Admin</option>
                                    <option value="super_admin">Super Admin</option>
                                </select>
                                @error('formData.role') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
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
                            class="px-4 py-2 bg-blue-900 text-white rounded-md hover:bg-blue-800 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2 min-w-[120px]"
                        >
                            <span wire:loading.remove wire:target="save" class="flex items-center justify-center gap-2">
                                @if($editingUserId)
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                @else
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                @endif
                                {{ $editingUserId ? 'Update' : 'Create' }}
                            </span>
                            <span wire:loading wire:target="save" class="flex items-center justify-center gap-2">
                                <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                {{ $editingUserId ? 'Updating...' : 'Creating...' }}
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
