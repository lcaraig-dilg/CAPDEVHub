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
        'activity_date' => '',
        'registration_start' => '',
        'registration_end' => '',
        'shareable_link' => '',
        'banner_image' => null,
        'description' => '',
    ];

    protected function rules()
    {
        $rules = [
            'formData.title' => 'required|string|max:255',
            'formData.venue' => 'required|string|max:255',
            'formData.activity_date' => 'required|date|after_or_equal:today',
            'formData.registration_start' => 'required|date',
            'formData.registration_end' => 'required|date|after:formData.registration_start',
            'formData.shareable_link' => 'required|string|max:255|regex:/^[a-zA-Z0-9\-_]+$/',
            'formData.description' => 'nullable|string',
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
        $this->formData = [
            'title' => $activity->title,
            'venue' => $activity->venue,
            'activity_date' => $activity->activity_date->format('Y-m-d\TH:i'),
            'registration_start' => $activity->registration_start->format('Y-m-d'),
            'registration_end' => $activity->registration_end->format('Y-m-d'),
            'shareable_link' => $activity->shareable_link,
            'banner_image' => null,
            'description' => $activity->description ?? '',
        ];
        $this->editingActivityId = $activityId;
        $this->bannerImagePreview = $activity->banner_image ? asset('storage/' . $activity->banner_image) : null;
        $this->showModal = true;
        
        // Dispatch event to load editor content
        $this->dispatch('load-editor-content', content: $this->formData['description']);
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
            'activity_date' => '',
            'registration_start' => '',
            'registration_end' => '',
            'shareable_link' => '',
            'banner_image' => null,
            'description' => '',
        ];
        $this->resetValidation();
    }

    public function updatedFormDataBannerImage()
    {
        if ($this->formData['banner_image']) {
            $this->bannerImagePreview = $this->formData['banner_image']->temporaryUrl();
        }
    }

    public function save()
    {
        $this->isSubmitting = true;
        
        try {
            $this->validate();

            $activityData = [
                'title' => $this->formData['title'],
                'venue' => $this->formData['venue'],
                'activity_date' => $this->formData['activity_date'],
                'registration_start' => $this->formData['registration_start'],
                'registration_end' => $this->formData['registration_end'],
                'shareable_link' => $this->formData['shareable_link'],
                'description' => $this->formData['description'],
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

    public function getQrCode($link)
    {
        $fullUrl = url('/register/' . $link);
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
