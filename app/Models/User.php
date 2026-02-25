<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'first_name',
        'middle_initial',
        'last_name',
        'suffix',
        'gender',
        'date_of_birth',
        'age',
        'is_pwd',
        'requires_assistance',
        'office',
        'position',
        'lgu_organization',
        'contact_number',
        'email',
        'dietary_restrictions',
        'password',
        'role',
        'username',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'date_of_birth' => 'date',
            'is_pwd' => 'boolean',
            'requires_assistance' => 'boolean',
        ];
    }
    
    /**
     * Check if user is super admin
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }
    
    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin' || $this->isSuperAdmin();
    }
    
    /**
     * Get full name
     */
    public function getFullNameAttribute(): string
    {
        // Format: FirstName MiddleInitial. LastName Suffix
        $name = $this->first_name;
        if ($this->middle_initial) {
            $name .= ' ' . strtoupper($this->middle_initial) . '.';
        }
        $name .= ' ' . $this->last_name;
        if ($this->suffix) {
            $name .= ' ' . $this->suffix;
        }
        return trim($name);
    }
}
