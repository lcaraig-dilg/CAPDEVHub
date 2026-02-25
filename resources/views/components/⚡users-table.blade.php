<?php

use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component
{
    use WithPagination;

    public $search = '';
    public $searchField = 'all'; // all, email, username, name, office, lgu_organization
    public $showModal = false;
    public $editingUserId = null;
    public $formData = [
        'first_name' => '',
        'middle_initial' => '',
        'last_name' => '',
        'suffix' => '',
        'gender' => '',
        'date_of_birth' => '',
        'age' => '',
        'is_pwd' => '0',
        'requires_assistance' => '0',
        'office' => '',
        'position' => '',
        'lgu_organization' => '',
        'contact_number' => '',
        'email' => '',
        'username' => '',
        'dietary_restrictions' => '',
        'password' => '',
        'password_confirmation' => '',
        'role' => 'user',
    ];

    protected function rules()
    {
        $rules = [
            'formData.first_name' => 'required|string|max:255',
            'formData.middle_initial' => 'nullable|string|size:1|regex:/^[A-Za-z]$/',
            'formData.last_name' => 'required|string|max:255',
            'formData.suffix' => 'nullable|string|max:10',
            'formData.gender' => 'required|in:Male,Female,Prefer not to say',
            'formData.date_of_birth' => 'required|date|before:today|after:1900-01-01',
            'formData.is_pwd' => 'required|in:0,1',
            'formData.requires_assistance' => 'required_if:formData.is_pwd,1|in:0,1',
            'formData.office' => 'required|string|max:255',
            'formData.position' => 'required|string|max:255',
            'formData.lgu_organization' => 'required|string|max:255',
            'formData.contact_number' => 'required|string|max:20|regex:/^[0-9+\-() ]+$/',
            'formData.dietary_restrictions' => 'nullable|string|max:500',
            'formData.role' => 'required|in:user,admin,super_admin',
        ];

        if ($this->editingUserId) {
            $rules['formData.email'] = 'required|email|unique:users,email,' . $this->editingUserId;
            $rules['formData.username'] = 'nullable|string|max:255|unique:users,username,' . $this->editingUserId;
            // Password is optional when editing
            if (!empty($this->formData['password'])) {
                $rules['formData.password'] = 'string|min:8|confirmed';
            }
        } else {
            $rules['formData.email'] = 'required|email|unique:users,email';
            $rules['formData.username'] = 'nullable|string|max:255|unique:users,username';
            $rules['formData.password'] = 'required|string|min:8|confirmed';
        }

        return $rules;
    }

    public function mount()
    {
        // Only super_admin can access this (also checked by middleware)
        $user = auth()->user();
        if (!$user || $user->role !== 'super_admin') {
            abort(403, 'Unauthorized access.');
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingSearchField()
    {
        $this->resetPage();
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->editingUserId = null;
        $this->showModal = true;
    }

    public function openEditModal($userId)
    {
        $user = User::findOrFail($userId);
        $this->formData = [
            'first_name' => $user->first_name,
            'middle_initial' => $user->middle_initial ?? '',
            'last_name' => $user->last_name,
            'suffix' => $user->suffix ?? '',
            'gender' => $user->gender ?? '',
            'date_of_birth' => $user->date_of_birth ? $user->date_of_birth->format('Y-m-d') : '',
            'age' => $user->age ?? '',
            'is_pwd' => $user->is_pwd ? '1' : '0',
            'requires_assistance' => $user->requires_assistance ? '1' : '0',
            'office' => $user->office,
            'position' => $user->position,
            'lgu_organization' => $user->lgu_organization,
            'contact_number' => $user->contact_number ?? '',
            'email' => $user->email,
            'username' => $user->username ?? '',
            'dietary_restrictions' => $user->dietary_restrictions ?? '',
            'password' => '',
            'password_confirmation' => '',
            'role' => $user->role,
        ];
        $this->editingUserId = $userId;
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
        $this->editingUserId = null;
    }

    public function resetForm()
    {
        $this->formData = [
            'first_name' => '',
            'middle_initial' => '',
            'last_name' => '',
            'suffix' => '',
            'gender' => '',
            'date_of_birth' => '',
            'age' => '',
            'is_pwd' => '0',
            'requires_assistance' => '0',
            'office' => '',
            'position' => '',
            'lgu_organization' => '',
            'contact_number' => '',
            'email' => '',
            'username' => '',
            'dietary_restrictions' => '',
            'password' => '',
            'password_confirmation' => '',
            'role' => 'user',
        ];
        $this->resetValidation();
    }

    public function updatedFormDataDateOfBirth()
    {
        if (!empty($this->formData['date_of_birth'])) {
            $dob = new \DateTime($this->formData['date_of_birth']);
            $today = new \DateTime();
            $age = $today->diff($dob)->y;
            $this->formData['age'] = $age;
        }
    }

    public function save()
    {
        $this->validate();

        // Calculate age if not set
        if (empty($this->formData['age']) && !empty($this->formData['date_of_birth'])) {
            $dob = new \DateTime($this->formData['date_of_birth']);
            $today = new \DateTime();
            $this->formData['age'] = $today->diff($dob)->y;
        }

        // Construct name: FirstName MiddleInitial. LastName Suffix
        $name = trim($this->formData['first_name']);
        if (!empty($this->formData['middle_initial'])) {
            $name .= ' ' . strtoupper($this->formData['middle_initial']) . '.';
        }
        $name .= ' ' . $this->formData['last_name'];
        if (!empty($this->formData['suffix'])) {
            $name .= ' ' . $this->formData['suffix'];
        }
        $name = trim($name);

        $userData = [
            'first_name' => $this->formData['first_name'],
            'middle_initial' => !empty($this->formData['middle_initial']) ? strtoupper($this->formData['middle_initial']) : null,
            'last_name' => $this->formData['last_name'],
            'suffix' => $this->formData['suffix'] ?? null,
            'gender' => $this->formData['gender'],
            'date_of_birth' => $this->formData['date_of_birth'],
            'age' => $this->formData['age'],
            'is_pwd' => $this->formData['is_pwd'] == '1',
            'requires_assistance' => $this->formData['is_pwd'] == '1' ? ($this->formData['requires_assistance'] == '1') : null,
            'office' => $this->formData['office'],
            'position' => $this->formData['position'],
            'lgu_organization' => $this->formData['lgu_organization'],
            'contact_number' => $this->formData['contact_number'],
            'email' => $this->formData['email'],
            'username' => $this->formData['username'] ?? null,
            'dietary_restrictions' => $this->formData['dietary_restrictions'] ?? null,
            'role' => $this->formData['role'],
            'name' => $name,
        ];

        if ($this->editingUserId) {
            $user = User::findOrFail($this->editingUserId);
            // Only update password if provided
            if (!empty($this->formData['password'])) {
                $userData['password'] = $this->formData['password'];
            }
            $user->update($userData);
            session()->flash('success', 'User updated successfully.');
        } else {
            // Generate username from email if not provided
            if (empty($this->formData['username'])) {
                $userData['username'] = explode('@', $this->formData['email'])[0];
            }
            $userData['password'] = $this->formData['password'];
            User::create($userData);
            session()->flash('success', 'User created successfully.');
        }

        $this->closeModal();
    }

    public function delete($userId)
    {
        $user = User::findOrFail($userId);
        
        // Prevent deleting super admin
        if ($user->role === 'super_admin') {
            session()->flash('error', 'Cannot delete super admin user.');
            return;
        }

        // Prevent deleting yourself
        if ($user->id === auth()->id()) {
            session()->flash('error', 'Cannot delete your own account.');
            return;
        }

        $user->delete();
        session()->flash('success', 'User deleted successfully.');
    }

    public function render()
    {
        $query = User::query();

        if (!empty($this->search)) {
            $searchTerm = '%' . $this->search . '%';
            
            switch ($this->searchField) {
                case 'email':
                    $query->where('email', 'like', $searchTerm);
                    break;
                case 'username':
                    $query->where('username', 'like', $searchTerm);
                    break;
                case 'name':
                    $query->where(function($q) use ($searchTerm) {
                        $q->where('first_name', 'like', $searchTerm)
                          ->orWhere('last_name', 'like', $searchTerm)
                          ->orWhere('name', 'like', $searchTerm);
                    });
                    break;
                case 'office':
                    $query->where('office', 'like', $searchTerm);
                    break;
                case 'lgu_organization':
                    $query->where('lgu_organization', 'like', $searchTerm);
                    break;
                default: // 'all'
                    $query->where(function($q) use ($searchTerm) {
                        $q->where('email', 'like', $searchTerm)
                          ->orWhere('username', 'like', $searchTerm)
                          ->orWhere('first_name', 'like', $searchTerm)
                          ->orWhere('last_name', 'like', $searchTerm)
                          ->orWhere('name', 'like', $searchTerm)
                          ->orWhere('office', 'like', $searchTerm)
                          ->orWhere('lgu_organization', 'like', $searchTerm);
                    });
                    break;
            }
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('livewire.users-table', [
            'users' => $users,
        ]);
    }
};
?>
