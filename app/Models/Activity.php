<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $fillable = [
        'title',
        'venue',
        'activity_date',
        'registration_start',
        'registration_end',
        'shareable_link',
        'banner_image',
        'description',
    ];

    protected $casts = [
        'activity_date' => 'datetime',
        'registration_start' => 'date',
        'registration_end' => 'date',
    ];
}
