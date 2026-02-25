<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ProfileController;

// Redirect root to login
Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // User profile
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Admin panel sections
    Route::view('/activities', 'activities.index')->name('activities.index');
    Route::view('/pre-post-tests', 'prepost.index')->name('prepost.index');
    Route::view('/materials-repository', 'materials.index')->name('materials.index');
    Route::view('/quiz', 'quiz.index')->name('quiz.index');
    Route::view('/certificate-of-attendance', 'certificates.index')->name('certificates.index');
    
    // Additional routes for role-based menus
    Route::view('/program-of-activities', 'program-of-activities.index')->name('program-of-activities.index');
    
    // Users management - Super Admin only
    Route::middleware('super_admin')->group(function () {
        Route::view('/users', 'users.index')->name('users.index');
    });
    
    Route::view('/my-activities', 'my-activities.index')->name('my-activities.index');
    Route::view('/my-certificates', 'my-certificates.index')->name('my-certificates.index');
});
