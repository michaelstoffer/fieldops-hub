<?php

use App\Http\Controllers\Owner\BillingController;
use App\Http\Controllers\Owner\CalendarController;
use App\Http\Controllers\Owner\DispatchController;
use App\Http\Controllers\Owner\CustomerController;
use App\Http\Controllers\Owner\EstimateController;
use App\Http\Controllers\Owner\InvoiceController;
use App\Http\Controllers\Owner\JobController;
use App\Http\Controllers\Owner\PropertyController;
use App\Http\Controllers\Owner\ReportingController;
use App\Http\Controllers\Owner\SetupController;
use App\Http\Controllers\Owner\SettingsController;
use App\Http\Controllers\Owner\StripeController;
use App\Http\Controllers\HealthController;
use App\Http\Controllers\MarketingController;
use App\Http\Controllers\StripeWebhookController;
use App\Http\Controllers\PublicEstimateController;
use App\Http\Controllers\Technician\DashboardController as TechnicianDashboardController;
use App\Http\Controllers\Technician\JobController as TechnicianJobController;
use Illuminate\Support\Facades\Route;

// Root: guests see the marketing page; authenticated users go to their dashboard
Route::get('/', function () {
    if (auth()->check()) {
        $user = auth()->user();
        if ($user->hasRole('technician')) {
            return redirect()->route('technician.dashboard');
        }
        return redirect()->route('owner.dashboard');
    }
    return app(MarketingController::class)->index();
})->name('home');

// Named 'dashboard' route — used by Fortify post-login redirect and internal links
Route::get('/dashboard', function () {
    $user = auth()->user();
    if ($user->hasRole('technician')) {
        return redirect()->route('technician.dashboard');
    }
    return redirect()->route('owner.dashboard');
})->middleware('auth')->name('dashboard');

// Setup wizard — restricted to owner/admin only
Route::middleware(['auth', 'verified', 'role:owner|admin'])
    ->prefix('owner')
    ->name('owner.')
    ->group(function () {
        Route::get('/setup', [SetupController::class, 'show'])->name('setup');
        Route::post('/setup/company', [SetupController::class, 'saveCompany'])->name('setup.company');
        Route::post('/setup/job-types', [SetupController::class, 'addJobType'])->name('setup.job-types.store');
        Route::delete('/setup/job-types/{jobType}', [SetupController::class, 'removeJobType'])->name('setup.job-types.destroy');
        Route::post('/setup/technicians', [SetupController::class, 'addTechnician'])->name('setup.technicians.store');
        Route::post('/setup/complete', [SetupController::class, 'complete'])->name('setup.complete');
    });

Route::middleware(['auth', 'verified', 'role:owner|admin|dispatcher|bookkeeper'])
    ->prefix('owner')
    ->name('owner.')
    ->group(function () {
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

        Route::get('/dispatch', [DispatchController::class, 'index'])->name('dispatch');
        Route::get('/dispatch/technicians', [DispatchController::class, 'technicianLocations'])->name('dispatch.technicians');
        Route::get('/dispatch/technicians/{user}/trail', [DispatchController::class, 'technicianTrail'])->name('dispatch.trail');

        Route::get('/billing', [BillingController::class, 'index'])->name('billing');

        // Reporting
        Route::get('/dashboard', [ReportingController::class, 'dashboard'])->name('dashboard');
        Route::get('/reports/jobs-by-type', [ReportingController::class, 'jobsByType'])->name('reports.jobs-by-type');
        Route::get('/reports/job-profitability', [ReportingController::class, 'jobProfitability'])->name('reports.job-profitability');
        Route::get('/reports/technician-performance', [ReportingController::class, 'technicianPerformance'])->name('reports.technician-performance');

        // Company & integration settings
        Route::get('/settings/company', [SettingsController::class, 'company'])->name('settings.company');
        Route::post('/settings/company', [SettingsController::class, 'updateCompany'])->name('settings.company.update');
        Route::get('/settings/integrations', [SettingsController::class, 'integrations'])->name('settings.integrations');
        Route::post('/settings/integrations', [SettingsController::class, 'updateIntegrations'])->name('settings.integrations.update');
        Route::resource('invoices', InvoiceController::class)->only(['index', 'show', 'destroy']);
        Route::post('/jobs/{job}/invoice', [InvoiceController::class, 'generateFromJob'])->name('jobs.invoice.generate');
        Route::post('/invoices/{invoice}/send', [InvoiceController::class, 'send'])->name('invoices.send');
        Route::post('/invoices/{invoice}/void', [InvoiceController::class, 'void'])->name('invoices.void');
        Route::post('/invoices/{invoice}/payments', [InvoiceController::class, 'recordPayment'])->name('invoices.payments.store');
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

// Health checks — no auth, no CSRF, used by uptime monitors and orchestrators
Route::get('/health', [HealthController::class, 'liveness'])->name('health');
Route::get('/health/ready', [HealthController::class, 'readiness'])->name('health.ready');

// Stripe webhook — no auth, CSRF excluded in bootstrap/app.php, signature verified in controller
Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle'])
    ->name('stripe.webhook');

require __DIR__.'/auth.php';
