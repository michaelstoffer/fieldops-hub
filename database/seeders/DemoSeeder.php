<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceLineItem;
use App\Models\Job;
use App\Models\JobLineItem;
use App\Models\JobType;
use App\Models\Organization;
use App\Models\OrganizationSetting;
use App\Models\Payment;
use App\Models\Property;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RolesAndPermissionsSeeder::class);

        // ── Organization ────────────────────────────────────────────────────────
        $org = Organization::firstOrCreate(
            ['slug' => 'demo-fieldops'],
            [
                'name'     => 'Demo Field Ops',
                'timezone' => 'America/New_York',
            ]
        );

        OrganizationSetting::firstOrCreate(
            ['organization_id' => $org->id],
            [
                'company_name'    => 'Demo Field Ops LLC',
                'company_email'   => 'hello@demo-fieldops.test',
                'company_phone'   => '555-800-1234',
                'company_address' => '100 Main Street',
                'company_city'    => 'Springfield',
                'company_state'   => 'IL',
                'company_zip'     => '62701',
            ]
        );

        // ── Users ────────────────────────────────────────────────────────────────
        $userDefs = [
            ['name' => 'Admin User',     'email' => 'admin@demo.test',       'role' => 'admin'],
            ['name' => 'Alice Owner',    'email' => 'owner@demo.test',       'role' => 'owner'],
            ['name' => 'Bob Dispatcher', 'email' => 'dispatcher@demo.test',  'role' => 'dispatcher'],
            ['name' => 'Carol Tech',     'email' => 'tech@demo.test',        'role' => 'technician'],
            ['name' => 'Dan Wrench',     'email' => 'tech2@demo.test',       'role' => 'technician'],
            ['name' => 'Eve Books',      'email' => 'bookkeeper@demo.test',  'role' => 'bookkeeper'],
        ];

        $usersByEmail = [];
        foreach ($userDefs as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name'              => $data['name'],
                    'password'          => Hash::make('password'),
                    'organization_id'   => $org->id,
                    'email_verified_at' => now(),
                ]
            );
            $user->syncRoles([$data['role']]);
            $usersByEmail[$data['email']] = $user;
        }

        $tech1 = $usersByEmail['tech@demo.test'];
        $tech2 = $usersByEmail['tech2@demo.test'];

        // ── Job Types ────────────────────────────────────────────────────────────
        $jobTypeDefs = [
            ['name' => 'HVAC Service',        'color' => '#3b82f6'],
            ['name' => 'Plumbing',            'color' => '#10b981'],
            ['name' => 'Electrical',          'color' => '#f59e0b'],
            ['name' => 'General Maintenance', 'color' => '#6366f1'],
        ];

        $jobTypes = [];
        foreach ($jobTypeDefs as $type) {
            $jobTypes[$type['name']] = JobType::firstOrCreate(
                ['organization_id' => $org->id, 'name' => $type['name']],
                ['color' => $type['color']]
            );
        }

        // ── Customers & Properties ────────────────────────────────────────────────
        $customerDefs = [
            [
                'first_name' => 'John', 'last_name' => 'Smith',
                'email' => 'john.smith@example.com', 'phone' => '555-0101',
                'properties' => [
                    ['name' => 'Home', 'address_line1' => '123 Elm Street', 'city' => 'Springfield', 'state' => 'IL', 'postal_code' => '62701'],
                    ['name' => 'Rental', 'address_line1' => '456 Oak Avenue', 'city' => 'Springfield', 'state' => 'IL', 'postal_code' => '62702'],
                ],
            ],
            [
                'first_name' => 'Jane', 'last_name' => 'Doe',
                'email' => 'jane.doe@example.com', 'phone' => '555-0102',
                'properties' => [
                    ['name' => 'Home', 'address_line1' => '789 Maple Drive', 'city' => 'Shelbyville', 'state' => 'IL', 'postal_code' => '62565'],
                ],
            ],
            [
                'first_name' => 'Bob', 'last_name' => 'Johnson',
                'email' => 'bob.johnson@example.com', 'phone' => '555-0103',
                'properties' => [
                    ['name' => 'Office', 'address_line1' => '10 Commerce Blvd', 'city' => 'Springfield', 'state' => 'IL', 'postal_code' => '62704'],
                ],
            ],
            [
                'first_name' => 'Maria', 'last_name' => 'Garcia',
                'email' => 'maria.garcia@example.com', 'phone' => '555-0104',
                'properties' => [
                    ['name' => 'Home', 'address_line1' => '22 Birch Lane', 'city' => 'Capital City', 'state' => 'IL', 'postal_code' => '62703'],
                ],
            ],
            [
                'first_name' => 'Tom', 'last_name' => 'Williams',
                'email' => 'tom.williams@example.com', 'phone' => '555-0105',
                'properties' => [
                    ['name' => 'Warehouse', 'address_line1' => '500 Industrial Pkwy', 'city' => 'Springfield', 'state' => 'IL', 'postal_code' => '62706'],
                    ['name' => 'Home', 'address_line1' => '88 Cedar Court', 'city' => 'Springfield', 'state' => 'IL', 'postal_code' => '62701'],
                ],
            ],
            [
                'first_name' => 'Susan', 'last_name' => 'Lee',
                'email' => 'susan.lee@example.com', 'phone' => '555-0106',
                'properties' => [
                    ['name' => 'Home', 'address_line1' => '15 Willow Way', 'city' => 'Shelbyville', 'state' => 'IL', 'postal_code' => '62565'],
                ],
            ],
        ];

        $customers   = [];
        $properties  = [];
        foreach ($customerDefs as $cDef) {
            $propsData = $cDef['properties'];
            unset($cDef['properties']);

            $customer = Customer::firstOrCreate(
                ['organization_id' => $org->id, 'email' => $cDef['email']],
                array_merge($cDef, ['organization_id' => $org->id])
            );
            $customers[] = $customer;

            $custProps = [];
            foreach ($propsData as $p) {
                $prop = Property::firstOrCreate(
                    ['organization_id' => $org->id, 'customer_id' => $customer->id, 'address_line1' => $p['address_line1']],
                    array_merge($p, ['organization_id' => $org->id, 'customer_id' => $customer->id, 'country' => 'US'])
                );
                $custProps[] = $prop;
            }
            $properties[$customer->id] = $custProps;
        }

        // Helper: primary property for a customer
        $prop = fn (Customer $c) => $properties[$c->id][0];

        [$smith, $doe, $bob, $garcia, $tom, $susan] = $customers;

        // ── Jobs ─────────────────────────────────────────────────────────────────
        // Skip seeding if jobs already exist (idempotency)
        if (Job::where('organization_id', $org->id)->exists()) {
            return;
        }

        $now = now();

        $jobDefs = [
            // Past completed jobs (with invoices)
            [
                'customer' => $smith, 'type' => 'HVAC Service', 'tech' => $tech1,
                'title'    => 'Annual HVAC Tune-Up',
                'status'   => Job::STATUS_COMPLETED,
                'scheduled_at' => $now->copy()->subDays(30)->setTime(9, 0),
                'completed_at' => $now->copy()->subDays(30)->setTime(11, 30),
                'invoice' => ['status' => Invoice::STATUS_PAID, 'amount_paid' => 185.00],
                'line_items' => [
                    ['description' => 'HVAC Tune-Up Labor (2.5 hrs)', 'unit_price' => 120.00, 'quantity' => 1],
                    ['description' => 'Air Filter Replacement', 'unit_price' => 35.00, 'quantity' => 1, 'is_taxable' => true],
                    ['description' => 'Refrigerant Check', 'unit_price' => 30.00, 'quantity' => 1],
                ],
            ],
            [
                'customer' => $doe, 'type' => 'Plumbing', 'tech' => $tech2,
                'title'    => 'Leaky Faucet Repair',
                'status'   => Job::STATUS_COMPLETED,
                'scheduled_at' => $now->copy()->subDays(21)->setTime(14, 0),
                'completed_at' => $now->copy()->subDays(21)->setTime(15, 0),
                'invoice' => ['status' => Invoice::STATUS_PAID, 'amount_paid' => 95.00],
                'line_items' => [
                    ['description' => 'Faucet Repair Labor (1 hr)', 'unit_price' => 75.00, 'quantity' => 1],
                    ['description' => 'Replacement Cartridge', 'unit_price' => 20.00, 'quantity' => 1, 'is_taxable' => true],
                ],
            ],
            [
                'customer' => $bob, 'type' => 'Electrical', 'tech' => $tech1,
                'title'    => 'Panel Inspection & GFCI Install',
                'status'   => Job::STATUS_COMPLETED,
                'scheduled_at' => $now->copy()->subDays(14)->setTime(10, 0),
                'completed_at' => $now->copy()->subDays(14)->setTime(12, 0),
                'invoice' => ['status' => Invoice::STATUS_SENT, 'amount_paid' => 0],
                'line_items' => [
                    ['description' => 'Electrical Labor (2 hrs)', 'unit_price' => 110.00, 'quantity' => 2],
                    ['description' => 'GFCI Outlets (4-pack)', 'unit_price' => 45.00, 'quantity' => 1, 'is_taxable' => true],
                    ['description' => 'Panel Inspection Fee', 'unit_price' => 65.00, 'quantity' => 1],
                ],
            ],
            [
                'customer' => $garcia, 'type' => 'General Maintenance', 'tech' => $tech2,
                'title'    => 'Seasonal Property Walkthrough',
                'status'   => Job::STATUS_COMPLETED,
                'scheduled_at' => $now->copy()->subDays(7)->setTime(8, 0),
                'completed_at' => $now->copy()->subDays(7)->setTime(10, 0),
                'invoice' => ['status' => Invoice::STATUS_OVERDUE, 'amount_paid' => 0, 'due_days_ago' => 3],
                'line_items' => [
                    ['description' => 'Property Inspection (2 hrs)', 'unit_price' => 90.00, 'quantity' => 1],
                    ['description' => 'Minor Repairs', 'unit_price' => 50.00, 'quantity' => 1, 'is_taxable' => true],
                ],
            ],
            [
                'customer' => $tom, 'type' => 'HVAC Service', 'tech' => $tech1,
                'title'    => 'Commercial HVAC Repair',
                'status'   => Job::STATUS_COMPLETED,
                'scheduled_at' => $now->copy()->subDays(45)->setTime(7, 0),
                'completed_at' => $now->copy()->subDays(45)->setTime(11, 0),
                'invoice' => ['status' => Invoice::STATUS_PARTIAL, 'amount_paid' => 250.00],
                'line_items' => [
                    ['description' => 'Emergency HVAC Labor (4 hrs)', 'unit_price' => 130.00, 'quantity' => 4],
                    ['description' => 'Compressor Capacitor', 'unit_price' => 85.00, 'quantity' => 1, 'is_taxable' => true],
                    ['description' => 'Refrigerant (R-410A, 2 lbs)', 'unit_price' => 55.00, 'quantity' => 2, 'is_taxable' => true],
                ],
            ],
            [
                'customer' => $susan, 'type' => 'Plumbing', 'tech' => $tech2,
                'title'    => 'Water Heater Replacement',
                'status'   => Job::STATUS_COMPLETED,
                'scheduled_at' => $now->copy()->subDays(60)->setTime(9, 0),
                'completed_at' => $now->copy()->subDays(60)->setTime(13, 0),
                'invoice' => ['status' => Invoice::STATUS_PAID, 'amount_paid' => 895.00],
                'line_items' => [
                    ['description' => 'Water Heater Installation (4 hrs)', 'unit_price' => 90.00, 'quantity' => 4],
                    ['description' => '50-Gal Gas Water Heater', 'unit_price' => 485.00, 'quantity' => 1, 'is_taxable' => true],
                    ['description' => 'Expansion Tank', 'unit_price' => 55.00, 'quantity' => 1, 'is_taxable' => true],
                ],
            ],
            // Current / active jobs
            [
                'customer' => $smith, 'type' => 'Electrical', 'tech' => $tech1,
                'title'    => 'Outlet Replacement — Kitchen',
                'status'   => Job::STATUS_IN_PROGRESS,
                'scheduled_at' => $now->copy()->setTime(10, 0),
            ],
            [
                'customer' => $doe, 'type' => 'HVAC Service', 'tech' => $tech2,
                'title'    => 'AC Not Cooling — Diagnostic',
                'status'   => Job::STATUS_EN_ROUTE,
                'scheduled_at' => $now->copy()->setTime(13, 0),
            ],
            // Upcoming jobs
            [
                'customer' => $garcia, 'type' => 'Plumbing', 'tech' => $tech1,
                'title'    => 'Bathroom Remodel Rough-In',
                'status'   => Job::STATUS_SCHEDULED,
                'scheduled_at' => $now->copy()->addDays(2)->setTime(8, 0),
            ],
            [
                'customer' => $bob, 'type' => 'General Maintenance', 'tech' => $tech2,
                'title'    => 'Quarterly Office Maintenance',
                'status'   => Job::STATUS_SCHEDULED,
                'scheduled_at' => $now->copy()->addDays(3)->setTime(9, 0),
            ],
            [
                'customer' => $tom, 'type' => 'HVAC Service', 'tech' => $tech1,
                'title'    => 'Warehouse HVAC Filter Change',
                'status'   => Job::STATUS_SCHEDULED,
                'scheduled_at' => $now->copy()->addDays(5)->setTime(7, 30),
            ],
            [
                'customer' => $susan, 'type' => 'Electrical', 'tech' => $tech2,
                'title'    => 'Ceiling Fan Install — Master Bedroom',
                'status'   => Job::STATUS_SCHEDULED,
                'scheduled_at' => $now->copy()->addDays(7)->setTime(11, 0),
            ],
            // On hold / cancelled
            [
                'customer' => $smith, 'type' => 'Plumbing', 'tech' => $tech2,
                'title'    => 'Main Line Hydro-Jet — Awaiting Customer',
                'status'   => Job::STATUS_ON_HOLD,
                'scheduled_at' => $now->copy()->addDays(10)->setTime(9, 0),
            ],
            [
                'customer' => $garcia, 'type' => 'HVAC Service', 'tech' => null,
                'title'    => 'New System Install — Cancelled',
                'status'   => Job::STATUS_CANCELLED,
                'scheduled_at' => $now->copy()->subDays(5)->setTime(9, 0),
                'cancelled_at' => $now->copy()->subDays(6),
            ],
        ];

        $invoiceNumber = 1001;

        foreach ($jobDefs as $def) {
            $customer = $def['customer'];
            $property = $prop($customer);
            $jobType  = $jobTypes[$def['type']];

            $jobData = [
                'organization_id' => $org->id,
                'customer_id'     => $customer->id,
                'property_id'     => $property->id,
                'job_type_id'     => $jobType->id,
                'assigned_to'     => $def['tech']?->id,
                'title'           => $def['title'],
                'status'          => $def['status'],
                'scheduled_at'    => $def['scheduled_at'],
            ];

            if (isset($def['completed_at'])) {
                $jobData['completed_at'] = $def['completed_at'];
                $jobData['started_at']   = $def['scheduled_at'];
                $jobData['arrived_at']   = $def['scheduled_at']->copy()->addMinutes(15);
            }
            if (isset($def['cancelled_at'])) {
                $jobData['cancelled_at'] = $def['cancelled_at'];
            }

            $job = Job::create($jobData);

            // Add job line items (parts/labor recorded on the job itself)
            if (isset($def['line_items'])) {
                foreach ($def['line_items'] as $i => $li) {
                    JobLineItem::create([
                        'job_id'      => $job->id,
                        'name'        => $li['description'],
                        'description' => $li['description'],
                        'unit_price'  => $li['unit_price'],
                        'quantity'    => $li['quantity'],
                        'sort_order'  => $i + 1,
                    ]);
                }
            }

            // Generate invoice for completed jobs
            if (isset($def['invoice'])) {
                $invDef    = $def['invoice'];
                $issuedAt  = $job->completed_at->copy()->addDay();
                $dueAt     = isset($invDef['due_days_ago'])
                    ? now()->subDays($invDef['due_days_ago'])
                    : $issuedAt->copy()->addDays(30);

                $invoice = Invoice::create([
                    'organization_id' => $org->id,
                    'customer_id'     => $customer->id,
                    'job_id'          => $job->id,
                    'invoice_number'  => (string) $invoiceNumber++,
                    'status'          => Invoice::STATUS_DRAFT,
                    'tax_rate'        => 0.0800,
                    'discount_amount' => 0,
                    'amount_paid'     => 0,
                    'issued_at'       => $issuedAt,
                    'due_at'          => $dueAt,
                    'sent_at'         => $invDef['status'] !== Invoice::STATUS_DRAFT ? $issuedAt : null,
                ]);

                // Mirror job line items onto invoice
                foreach ($def['line_items'] as $i => $li) {
                    InvoiceLineItem::create([
                        'invoice_id'  => $invoice->id,
                        'name'        => $li['description'],
                        'description' => $li['description'],
                        'unit_price'  => $li['unit_price'],
                        'quantity'    => $li['quantity'],
                        'is_taxable'  => $li['is_taxable'] ?? false,
                        'sort_order'  => $i + 1,
                    ]);
                }

                $invoice->recalculate();

                // Record payment if applicable
                $amountPaid = $invDef['amount_paid'] ?? 0;
                if ($amountPaid > 0) {
                    Payment::create([
                        'organization_id' => $org->id,
                        'invoice_id'      => $invoice->id,
                        'amount'          => $amountPaid,
                        'method'          => 'check',
                        'paid_at'         => $issuedAt->copy()->addDays(rand(1, 10)),
                        'notes'           => 'Demo payment',
                    ]);
                    $invoice->update(['amount_paid' => $amountPaid]);
                    $invoice->recalculate();
                }

                // Set final status
                $finalStatus = $invDef['status'];
                $updateData  = ['status' => $finalStatus];
                if ($finalStatus === Invoice::STATUS_PAID) {
                    $updateData['paid_at'] = $issuedAt->copy()->addDays(rand(3, 14));
                }
                $invoice->update($updateData);
            }
        }
    }
}
