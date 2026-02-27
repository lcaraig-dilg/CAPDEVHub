<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityRegistration extends Model
{
    use HasFactory;

    protected $fillable = [
        'activity_id',
        'user_id',
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
        'registration_type',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'is_pwd' => 'boolean',
        'requires_assistance' => 'boolean',
    ];

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

