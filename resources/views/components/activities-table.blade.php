<?php

use App\Models\Activity;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

new class extends Component
{
    use WithPagination, WithFileUploads;

    public $search = '';
    public $showModal = false;
    public $showDetailsModal = false;
    public $editingActivityId = null;
    public $isSubmitting = false;
    public $selectedActivities = [];
    public $selectAll = false;
    public $viewingActivity = null;
    public $bannerImagePreview = null;
    public $formData = [
        'title' => '',
        'venue' => '',
        'venue_google_maps_link' => '',
        'activity_date' => '',
        'activity_date_date' => '',
        'activity_date_time' => '',
        'registration_start' => '',
        'registration_end' => '',
        'shareable_link' => '',
        'banner_image' => null,
        'description' => '',
        'color_palette' => 'default',
        'accent_color_1' => '#013141',
        'accent_color_2' => '#0A7CA1',
        'accent_color_3' => '#FAB95B',
    ];

    protected function rules()
    {
        $rules = [
            'formData.title' => 'required|string|max:255',
            'formData.venue' => 'required|string|max:255',
            'formData.venue_google_maps_link' => 'nullable|url|max:500',
            'formData.activity_date' => 'required|date|after_or_equal:today',
            'formData.registration_start' => 'required|date',
            'formData.registration_end' => 'required|date|after:formData.registration_start',
            'formData.shareable_link' => 'required|string|max:255|regex:/^[a-zA-Z0-9\-_]+$/',
            'formData.description' => 'nullable|string',
            'formData.accent_color_1' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'formData.accent_color_2' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'formData.accent_color_3' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
        ];

        if ($this->editingActivityId) {
            $rules['formData.shareable_link'] = 'required|string|max:255|regex:/^[a-zA-Z0-9\-_]+$/|unique:activities,shareable_link,' . $this->editingActivityId;
        } else {
            $rules['formData.shareable_link'] = 'required|string|max:255|regex:/^[a-zA-Z0-9\-_]+$/|unique:activities,shareable_link';
            $rules['formData.banner_image'] = 'nullable|image|max:5120'; // 5MB max
        }

        return $rules;
    }

    public function mount()
    {
        // Only admin and super_admin can access this
        $user = auth()->user();
        if (!$user || !in_array($user->role, ['admin', 'super_admin'])) {
            abort(403, 'Unauthorized access.');
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->editingActivityId = null;
        $this->bannerImagePreview = null;
        $this->showModal = true;
    }

    public function openEditModal($activityId)
    {
        $activity = Activity::findOrFail($activityId);
        $hour = $activity->activity_date->format('H');
        $minute = $activity->activity_date->format('i');
        
        // Round to nearest allowed hour or use exact if it matches
        $allowedHours = ['08', '09', '10', '15', '16', '17'];
        $timeValue = $hour . ':00';
        if (!in_array($hour, $allowedHours) || $minute != '00') {
            // Find nearest allowed hour
            $hourInt = (int)$hour;
            if ($hourInt < 8) {
                $timeValue = '08:00';
            } elseif ($hourInt <= 10) {
                $timeValue = $hourInt . ':00';
                if ($timeValue == '11:00' || $timeValue == '12:00' || $timeValue == '13:00' || $timeValue == '14:00') {
                    $timeValue = '10:00';
                }
            } elseif ($hourInt < 15) {
                $timeValue = '10:00';
            } elseif ($hourInt <= 17) {
                $timeValue = $hourInt . ':00';
            } else {
                $timeValue = '17:00';
            }
        }
        
        $this->formData = [
            'title' => $activity->title,
            'venue' => $activity->venue,
            'venue_google_maps_link' => $activity->venue_google_maps_link ?? '',
            'activity_date' => $activity->activity_date->format('Y-m-d\TH:i'),
            'activity_date_date' => $activity->activity_date->format('Y-m-d'),
            'activity_date_time' => $timeValue,
            'registration_start' => $activity->registration_start->format('Y-m-d'),
            'registration_end' => $activity->registration_end->format('Y-m-d'),
            'shareable_link' => $activity->shareable_link,
            'banner_image' => null,
            'description' => $activity->description ?? '',
            'color_palette' => $activity->color_palette ?? 'default',
            'accent_color_1' => $activity->accent_color_1 ?? '#013141',
            'accent_color_2' => $activity->accent_color_2 ?? '#0A7CA1',
            'accent_color_3' => $activity->accent_color_3 ?? '#FAB95B',
        ];
        $this->editingActivityId = $activityId;
        $this->bannerImagePreview = $activity->banner_image ? asset('storage/' . $activity->banner_image) : null;
        $this->showModal = true;
    }

    public function openDetailsModal($activityId)
    {
        $this->viewingActivity = Activity::findOrFail($activityId);
        $this->showDetailsModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
        $this->editingActivityId = null;
        $this->bannerImagePreview = null;
    }

    public function closeDetailsModal()
    {
        $this->showDetailsModal = false;
        $this->viewingActivity = null;
    }

    public function resetForm()
    {
        $this->formData = [
            'title' => '',
            'venue' => '',
            'venue_google_maps_link' => '',
            'activity_date' => '',
            'activity_date_date' => '',
            'activity_date_time' => '',
            'registration_start' => '',
            'registration_end' => '',
            'shareable_link' => '',
            'banner_image' => null,
            'description' => '',
            'color_palette' => 'default',
            'accent_color_1' => '#013141',
            'accent_color_2' => '#0A7CA1',
            'accent_color_3' => '#FAB95B',
        ];
        $this->resetValidation();
    }

    public function updatedFormDataBannerImage()
    {
        if ($this->formData['banner_image']) {
            $this->bannerImagePreview = $this->formData['banner_image']->temporaryUrl();
        }
    }

    public function updatedFormDataColorPalette()
    {
        // Set colors based on palette selection
        if ($this->formData['color_palette'] === 'default') {
            // Default color palette
            $this->formData['accent_color_1'] = '#013141';
            $this->formData['accent_color_2'] = '#0A7CA1';
            $this->formData['accent_color_3'] = '#FAB95B';
        } elseif ($this->formData['color_palette'] === 'plain') {
            // Plain color palette
            $this->formData['accent_color_1'] = '#FFFFFF'; // white
            $this->formData['accent_color_2'] = '#F3F4F6'; // gray-100
            $this->formData['accent_color_3'] = '#000000'; // black
        }
        // For 'custom', keep existing values
    }

    protected function processDescriptionImages($description, $activityId = null)
    {
        if (empty($description)) {
            return $description;
        }

        // Get old images if editing
        $oldImages = [];
        if ($activityId) {
            $oldActivity = Activity::find($activityId);
            if ($oldActivity && $oldActivity->description) {
                // Extract existing image paths from old description
                preg_match_all('/<img[^>]+src=["\']([^"\']+)["\'][^>]*>/i', $oldActivity->description, $matches);
                if (!empty($matches[1])) {
                    foreach ($matches[1] as $imgSrc) {
                        // Only track storage paths, not external URLs
                        if (strpos($imgSrc, 'storage/') !== false) {
                            $path = str_replace(asset('storage/'), '', $imgSrc);
                            $oldImages[] = $path;
                        }
                    }
                }
            }
        }

        // Process base64 images in description
        $processedDescription = preg_replace_callback(
            '/<img[^>]+src=["\']data:image\/([^;]+);base64,([^"\']+)["\'][^>]*>/i',
            function ($matches) use ($activityId) {
                $imageType = $matches[1];
                $base64Data = $matches[2];
                
                // Decode base64 image
                $imageData = base64_decode($base64Data);
                if ($imageData === false) {
                    return $matches[0]; // Return original if decode fails
                }
                
                // Generate unique filename
                $filename = 'activity-' . ($activityId ?? 'new') . '-' . uniqid() . '.' . $imageType;
                $path = 'activities/descriptions/' . $filename;
                
                // Store image
                Storage::disk('public')->put($path, $imageData);
                
                // Get the full img tag and replace src
                $imgTag = $matches[0];
                $newSrc = asset('storage/' . $path);
                
                // Replace the src attribute
                $newImgTag = preg_replace(
                    '/src=["\']data:image\/[^"\']+["\']/i',
                    'src="' . $newSrc . '"',
                    $imgTag
                );
                
                // Add style if not present
                if (strpos($newImgTag, 'style=') === false) {
                    $newImgTag = str_replace('>', ' style="max-width: 100%; height: auto;">', $newImgTag);
                }
                
                return $newImgTag;
            },
            $description
        );

        // Clean up old images that are no longer in the description
        if ($activityId && !empty($oldImages)) {
            preg_match_all('/<img[^>]+src=["\']([^"\']+)["\'][^>]*>/i', $processedDescription, $newMatches);
            $newImagePaths = [];
            if (!empty($newMatches[1])) {
                foreach ($newMatches[1] as $imgSrc) {
                    if (strpos($imgSrc, 'storage/') !== false) {
                        $path = str_replace(asset('storage/'), '', $imgSrc);
                        $newImagePaths[] = $path;
                    }
                }
            }
            
            // Delete images that are no longer referenced
            foreach ($oldImages as $oldPath) {
                if (!in_array($oldPath, $newImagePaths) && Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }
        }

        return $processedDescription;
    }

    public function updatedFormDataActivityDateDate()
    {
        $this->updateActivityDateTime();
    }

    public function updatedFormDataActivityDateTime()
    {
        $this->updateActivityDateTime();
    }

    protected function updateActivityDateTime()
    {
        if (!empty($this->formData['activity_date_date']) && !empty($this->formData['activity_date_time'])) {
            // Extract hour from time and set minutes to 00
            $time = $this->formData['activity_date_time'];
            $timeParts = explode(':', $time);
            $hour = $timeParts[0] ?? '00';
            $this->formData['activity_date'] = $this->formData['activity_date_date'] . ' ' . $hour . ':00:00';
        }
    }

    public function save()
    {
        $this->isSubmitting = true;
        
        try {
            // Combine date and time, ensuring minutes are always 00
            if (!empty($this->formData['activity_date_date']) && !empty($this->formData['activity_date_time'])) {
                $time = $this->formData['activity_date_time'];
                $timeParts = explode(':', $time);
                $hour = $timeParts[0] ?? '00';
                $this->formData['activity_date'] = $this->formData['activity_date_date'] . ' ' . $hour . ':00:00';
            }
            
            $this->validate();

            // Process description images (convert base64 to files)
            $processedDescription = $this->processDescriptionImages(
                $this->formData['description'],
                $this->editingActivityId
            );

            $activityData = [
                'title' => $this->formData['title'],
                'venue' => $this->formData['venue'],
                'venue_google_maps_link' => $this->formData['venue_google_maps_link'] ?? null,
                'activity_date' => $this->formData['activity_date'],
                'registration_start' => $this->formData['registration_start'],
                'registration_end' => $this->formData['registration_end'],
                'shareable_link' => $this->formData['shareable_link'],
                'description' => $processedDescription,
                'color_palette' => $this->formData['color_palette'] ?? 'default',
                'accent_color_1' => $this->formData['accent_color_1'] ?? '#013141',
                'accent_color_2' => $this->formData['accent_color_2'] ?? '#0A7CA1',
                'accent_color_3' => $this->formData['accent_color_3'] ?? '#FAB95B',
            ];

            // Handle banner image upload
            if ($this->formData['banner_image']) {
                if ($this->editingActivityId) {
                    $oldActivity = Activity::findOrFail($this->editingActivityId);
                    if ($oldActivity->banner_image && Storage::disk('public')->exists($oldActivity->banner_image)) {
                        Storage::disk('public')->delete($oldActivity->banner_image);
                    }
                }
                $path = $this->formData['banner_image']->store('activities/banners', 'public');
                $activityData['banner_image'] = $path;
            }

            if ($this->editingActivityId) {
                $activity = Activity::findOrFail($this->editingActivityId);
                $activity->update($activityData);
                session()->flash('success', 'Activity updated successfully.');
            } else {
                Activity::create($activityData);
                session()->flash('success', 'Activity created successfully.');
            }

            $this->isSubmitting = false;
            $this->closeModal();
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->isSubmitting = false;
            throw $e;
        } catch (\Exception $e) {
            $this->isSubmitting = false;
            session()->flash('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function delete($activityId)
    {
        $activity = Activity::findOrFail($activityId);
        
        // Delete banner image if exists
        if ($activity->banner_image && Storage::disk('public')->exists($activity->banner_image)) {
            Storage::disk('public')->delete($activity->banner_image);
        }

        $activity->delete();
        session()->flash('success', 'Activity deleted successfully.');
        $this->selectedActivities = [];
    }

    public function updatedSelectAll($value)
    {
        $activities = $this->getActivitiesQuery()->paginate(10);
        $currentPageIds = $activities->pluck('id')->toArray();
        
        if ($value) {
            $this->selectedActivities = array_unique(array_merge($this->selectedActivities, $currentPageIds));
        } else {
            $this->selectedActivities = array_values(array_diff($this->selectedActivities, $currentPageIds));
        }
    }

    public function updatedSelectedActivities()
    {
        $activities = $this->getActivitiesQuery()->paginate(10);
        $currentPageIds = $activities->pluck('id')->toArray();
        $selectedOnPage = array_intersect($this->selectedActivities, $currentPageIds);
        $this->selectAll = !empty($currentPageIds) && count($selectedOnPage) === count($currentPageIds);
    }

    public function batchDelete()
    {
        if (empty($this->selectedActivities)) {
            session()->flash('error', 'Please select at least one activity to delete.');
            return;
        }

        $activities = Activity::whereIn('id', $this->selectedActivities)->get();
        $deletedCount = 0;

        foreach ($activities as $activity) {
            // Delete banner image if exists
            if ($activity->banner_image && Storage::disk('public')->exists($activity->banner_image)) {
                Storage::disk('public')->delete($activity->banner_image);
            }
            $activity->delete();
            $deletedCount++;
        }

        if ($deletedCount > 0) {
            session()->flash('success', "Successfully deleted {$deletedCount} activity(ies).");
        }

        $this->selectedActivities = [];
        $this->selectAll = false;
    }

    public function getQrCode($activity)
    {
        $slug = \Illuminate\Support\Str::slug($activity->title);
        $fullUrl = url('/show/' . $slug);
        // Use default SVG output (no Imagick dependency)
        return QrCode::size(200)->generate($fullUrl);
    }

    protected function getActivitiesQuery()
    {
        $query = Activity::query();

        if (!empty($this->search)) {
            $searchTerm = '%' . $this->search . '%';
            $query->where(function($q) use ($searchTerm) {
                $q->where('title', 'like', $searchTerm)
                  ->orWhere('venue', 'like', $searchTerm)
                  ->orWhere('shareable_link', 'like', $searchTerm);
            });
        }

        return $query->orderBy('activity_date', 'desc')->orderBy('created_at', 'desc');
    }

    public function render()
    {
        $activities = $this->getActivitiesQuery()->paginate(10);

        return view('livewire.activities-table', [
            'activities' => $activities,
        ]);
    }
};
?>
