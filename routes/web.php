<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\GoalController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CalendarController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');
    Route::get('/dashboard/export/pdf', [DashboardController::class, 'exportPdf'])->name('dashboard.export.pdf');
    Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar');
    Route::get('/calendar/export/pdf', [CalendarController::class, 'exportPdf'])->name('calendar.export.pdf');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Goals routes - specific routes first
    Route::get('/goals/create', function () {
        return Inertia::render('Goals/Create');
    })->name('goals.create');
    
    Route::post('/goals', [GoalController::class, 'store'])->name('goals.store');
    
    Route::get('/goals', [GoalController::class, 'index'])->name('goals.index');
    Route::get('/goals/{hash}', [GoalController::class, 'show'])->name('goals.show');
    Route::get('/goals/{hash}/edit', [GoalController::class, 'edit'])->name('goals.edit');
    Route::patch('/goals/{hash}', [GoalController::class, 'update'])->name('goals.update');
    Route::patch('/goals/{hash}/progress', [GoalController::class, 'updateProgress'])->name('goals.updateProgress');
    Route::delete('/goals/{hash}', [GoalController::class, 'destroy'])->name('goals.destroy');
    
    // Task routes
    Route::get('/goals/{goal}/tasks', [TaskController::class, 'index'])->name('goals.tasks.index');
    Route::post('/goals/{goal}/tasks', [TaskController::class, 'store'])->name('goals.tasks.store');
    Route::patch('/goals/{goal}/tasks/{task}', [TaskController::class, 'update'])->name('goals.tasks.update');
    Route::patch('/goals/{goal}/tasks/{task}/toggle', [TaskController::class, 'toggle'])->name('goals.tasks.toggle');
    Route::delete('/goals/{goal}/tasks/{task}', [TaskController::class, 'destroy'])->name('goals.tasks.destroy');
});

require __DIR__.'/auth.php';
