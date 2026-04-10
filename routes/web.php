<?php

use App\Http\Controllers\Owner\CalendarController;
use App\Http\Controllers\Owner\CustomerController;
use App\Http\Controllers\Owner\DashboardController;
use App\Http\Controllers\Owner\JobController;
use App\Http\Controllers\Owner\PropertyController;
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

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])
    ->prefix('owner')
    ->name('owner.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::resource('customers', CustomerController::class);

        // Properties — nested create/store under customer; shallow edit/update/destroy
        Route::get('/customers/{customer}/properties/create', [PropertyController::class, 'create'])->name('customers.properties.create');
        Route::post('/customers/{customer}/properties', [PropertyController::class, 'store'])->name('customers.properties.store');
        Route::get('/properties/{property}/edit', [PropertyController::class, 'edit'])->name('properties.edit');
        Route::patch('/properties/{property}', [PropertyController::class, 'update'])->name('properties.update');
        Route::delete('/properties/{property}', [PropertyController::class, 'destroy'])->name('properties.destroy');

        Route::resource('jobs', JobController::class);
        Route::patch('/jobs/{job}/status', [JobController::class, 'updateStatus'])->name('jobs.status');
        Route::patch('/jobs/{job}/reschedule', [JobController::class, 'reschedule'])->name('jobs.reschedule');
        Route::patch('/jobs/{job}/reassign', [JobController::class, 'reassign'])->name('jobs.reassign');

        Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar');
        Route::get('/calendar/events', [CalendarController::class, 'events'])->name('calendar.events');
    });

require __DIR__.'/auth.php';
