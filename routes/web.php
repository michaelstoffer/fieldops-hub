<?php

use App\Http\Controllers\Owner\CalendarController;
use App\Http\Controllers\Owner\CustomerController;
use App\Http\Controllers\Owner\DashboardController;
use App\Http\Controllers\Owner\EstimateController;
use App\Http\Controllers\Owner\InvoiceController;
use App\Http\Controllers\Owner\JobController;
use App\Http\Controllers\Owner\PropertyController;
use App\Http\Controllers\Owner\StripeController;
use App\Http\Controllers\StripeWebhookController;
use App\Http\Controllers\PublicEstimateController;
use App\Http\Controllers\Technician\DashboardController as TechnicianDashboardController;
use App\Http\Controllers\Technician\JobController as TechnicianJobController;
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

        Route::resource('estimates', EstimateController::class);
        Route::post('/estimates/{estimate}/send', [EstimateController::class, 'send'])->name('estimates.send');
        Route::post('/estimates/{estimate}/convert', [EstimateController::class, 'convertToJob'])->name('estimates.convert');

        Route::resource('invoices', InvoiceController::class)->only(['index', 'show', 'destroy']);
        Route::post('/jobs/{job}/invoice', [InvoiceController::class, 'generateFromJob'])->name('jobs.invoice.generate');
        Route::post('/invoices/{invoice}/send', [InvoiceController::class, 'send'])->name('invoices.send');
        Route::post('/invoices/{invoice}/void', [InvoiceController::class, 'void'])->name('invoices.void');
        Route::post('/invoices/{invoice}/checkout', [StripeController::class, 'createCheckoutSession'])->name('invoices.checkout');
    });

// Public estimate page — no auth required
Route::get('/estimates/{token}', [PublicEstimateController::class, 'show'])->name('estimates.public');
Route::post('/estimates/{token}/accept', [PublicEstimateController::class, 'accept'])->name('estimates.accept');
Route::post('/estimates/{token}/decline', [PublicEstimateController::class, 'decline'])->name('estimates.decline');

Route::middleware(['auth', 'role:technician'])
    ->prefix('technician')
    ->name('technician.')
    ->group(function () {
        Route::get('/dashboard', [TechnicianDashboardController::class, 'index'])->name('dashboard');
        Route::get('/jobs', [TechnicianJobController::class, 'index'])->name('jobs.index');
        Route::get('/jobs/{job}', [TechnicianJobController::class, 'show'])->name('jobs.show');
    });

// Stripe webhook — no auth, CSRF excluded in bootstrap/app.php, signature verified in controller
Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle'])
    ->name('stripe.webhook');

require __DIR__.'/auth.php';
