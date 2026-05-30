<?php

use App\Http\Controllers\Technician\JobController as TechnicianJobController;
use App\Http\Controllers\Technician\LocationController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:technician'])
    ->prefix('technician')
    ->name('technician.')
    ->group(function () {
        // Read-only endpoints — generous limit
        Route::middleware('throttle:120,1')->group(function () {
            Route::get('/jobs/today', [TechnicianJobController::class, 'today'])
                ->name('jobs.today');
            Route::get('/jobs/{job}', [TechnicianJobController::class, 'apiShow'])
                ->name('jobs.show');
            Route::get('/catalog', [TechnicianJobController::class, 'catalogItems'])
                ->name('catalog.index');
        });

        // Mutation endpoints — tighter limit
        Route::middleware('throttle:60,1')->group(function () {
            Route::patch('/jobs/{job}/status', [TechnicianJobController::class, 'updateStatus'])
                ->name('jobs.status');
            Route::patch('/jobs/{job}/notes', [TechnicianJobController::class, 'updateNotes'])
                ->name('jobs.notes');
            Route::patch('/jobs/{job}/customer-notes', [TechnicianJobController::class, 'updateCustomerNotes'])
                ->name('jobs.customer-notes');
            Route::patch('/jobs/{job}/checklist/{item}', [TechnicianJobController::class, 'toggleChecklistItem'])
                ->name('jobs.checklist.toggle');
            Route::delete('/jobs/{job}/photos/{attachment}', [TechnicianJobController::class, 'deletePhoto'])
                ->name('jobs.photos.destroy');
            Route::post('/jobs/{job}/line-items', [TechnicianJobController::class, 'addLineItem'])
                ->name('jobs.line-items.store');
            Route::patch('/jobs/{job}/line-items/{lineItem}', [TechnicianJobController::class, 'updateLineItem'])
                ->name('jobs.line-items.update');
            Route::delete('/jobs/{job}/line-items/{lineItem}', [TechnicianJobController::class, 'deleteLineItem'])
                ->name('jobs.line-items.destroy');
        });

        // Photo uploads — strict limit (10 MB each, bandwidth-sensitive)
        Route::middleware('throttle:20,1')->group(function () {
            Route::post('/jobs/{job}/photos', [TechnicianJobController::class, 'uploadPhoto'])
                ->name('jobs.photos.store');
        });

        // Location pings — high frequency but per-user bucketed
        Route::middleware('throttle:120,1')->group(function () {
            Route::post('/location', [LocationController::class, 'store'])
                ->name('location.store');
        });
    });
