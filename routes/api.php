<?php

use App\Http\Controllers\Technician\JobController as TechnicianJobController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:technician'])
    ->prefix('technician')
    ->name('technician.')
    ->group(function () {
        Route::get('/jobs/today', [TechnicianJobController::class, 'today'])
            ->name('jobs.today');
        Route::get('/jobs/{job}', [TechnicianJobController::class, 'apiShow'])
            ->name('jobs.show');
        Route::patch('/jobs/{job}/status', [TechnicianJobController::class, 'updateStatus'])
            ->name('jobs.status');
        Route::patch('/jobs/{job}/notes', [TechnicianJobController::class, 'updateNotes'])
            ->name('jobs.notes');
    });
