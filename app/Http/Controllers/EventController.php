<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function show($slug)
    {
        // Find activity by slug (generated from title)
        $activity = Activity::all()->first(function ($activity) use ($slug) {
            return \Illuminate\Support\Str::slug($activity->title) === $slug;
        });
        
        if (!$activity) {
            abort(404);
        }
        
        return view('events.show', compact('activity'));
    }
}
