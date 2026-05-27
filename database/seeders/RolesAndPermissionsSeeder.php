<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            // Customers
            'customers.view', 'customers.create', 'customers.edit', 'customers.delete',
            // Properties
            'properties.view', 'properties.create', 'properties.edit', 'properties.delete',
            // Jobs
            'jobs.view', 'jobs.create', 'jobs.edit', 'jobs.delete',
            'jobs.assign', 'jobs.update_status',
            // Invoices
            'invoices.view', 'invoices.create', 'invoices.edit', 'invoices.delete', 'invoices.send',
            // Payments
            'payments.view', 'payments.record',
            // Items (catalog)
            'items.view', 'items.create', 'items.edit', 'items.delete',
            // Reports
            'reports.view',
            // Settings
            'settings.view', 'settings.edit',
            // Users
            'users.view', 'users.create', 'users.edit', 'users.delete',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Admin — full access (super-user within their org)
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->syncPermissions(Permission::all());

        // Owner — same as admin but typically the org creator
        $owner = Role::firstOrCreate(['name' => 'owner']);
        $owner->syncPermissions(Permission::all());

        // Dispatcher — manages scheduling and customer communication
        $dispatcher = Role::firstOrCreate(['name' => 'dispatcher']);
        $dispatcher->syncPermissions([
            'customers.view', 'customers.create', 'customers.edit',
            'properties.view', 'properties.create', 'properties.edit',
            'jobs.view', 'jobs.create', 'jobs.edit', 'jobs.assign', 'jobs.update_status',
            'invoices.view', 'invoices.create', 'invoices.edit', 'invoices.send',
            'payments.view', 'payments.record',
            'items.view',
            'reports.view',
        ]);

        // Technician — read and update their own jobs in the field
        $technician = Role::firstOrCreate(['name' => 'technician']);
        $technician->syncPermissions([
            'jobs.view', 'jobs.update_status',
            'customers.view',
            'properties.view',
        ]);

        // Bookkeeper — billing and financial focus
        $bookkeeper = Role::firstOrCreate(['name' => 'bookkeeper']);
        $bookkeeper->syncPermissions([
            'customers.view',
            'jobs.view',
            'invoices.view', 'invoices.create', 'invoices.edit', 'invoices.send',
            'payments.view', 'payments.record',
            'items.view', 'items.create', 'items.edit',
            'reports.view',
        ]);

        // Master — system-level super-admin, no org affiliation, manages all tenants
        Role::firstOrCreate(['name' => 'master']);
    }
}
