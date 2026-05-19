# FieldOps Hub

[![Tests](https://github.com/michaelstoffer/fieldops-hub/actions/workflows/tests.yml/badge.svg)](https://github.com/michaelstoffer/fieldops-hub/actions/workflows/tests.yml)
[![Deploy](https://github.com/michaelstoffer/fieldops-hub/actions/workflows/deploy.yml/badge.svg)](https://github.com/michaelstoffer/fieldops-hub/actions/workflows/deploy.yml)
[![PHP](https://img.shields.io/badge/PHP-8.4-777BB4?logo=php&logoColor=white)](https://php.net)
[![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?logo=laravel&logoColor=white)](https://laravel.com)
[![Vue](https://img.shields.io/badge/Vue-3-42b883?logo=vue.js&logoColor=white)](https://vuejs.org)
[![TypeScript](https://img.shields.io/badge/TypeScript-5-3178C6?logo=typescript&logoColor=white)](https://typescriptlang.org)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind-v4-06B6D4?logo=tailwindcss&logoColor=white)](https://tailwindcss.com)
[![License](https://img.shields.io/badge/license-MIT-green)](LICENSE)
[![Release](https://img.shields.io/github/v/release/michaelstoffer/fieldops-hub?include_prereleases&label=release)](https://github.com/michaelstoffer/fieldops-hub/releases)

A modern field service management platform built for small-to-medium field operations businesses — HVAC, plumbing, electrical, landscaping, and more. Manage customers, schedule jobs, dispatch technicians, and handle billing from a single, fast web application.

---

## What It Does

**FieldOps Hub** gives your team a shared, real-time workspace to run field operations end to end:

| Area | Capabilities |
| ---- | ----------- |
| **Customers & Properties** | Customer profiles with multiple service locations, address history, and attachments |
| **Job Management** | Create, assign, and track jobs through a full status lifecycle (scheduled → en route → in progress → completed) |
| **Job Checklists** | Per-job-type checklist templates that auto-copy onto new jobs; technicians check off items in the field |
| **Estimates** | Multi-tier estimate packages (Good/Better/Best); send via shareable public link; customers accept or decline online |
| **Dispatch & Live Tracking** | Assign jobs to technicians, view a live map of technician locations with current job overlay and location trail |
| **Invoicing** | Draft, edit, and send invoices tied to completed jobs; track line items, tax, discounts, and balance due |
| **Payments** | Record payments by method (cash, check, card, bank transfer); Stripe-hosted checkout for online card payments |
| **Reporting** | Owner dashboard with real-time KPIs; jobs-by-type, job profitability, and technician performance reports |
| **Company Settings** | Company branding (name, logo, contact info), default tax rate, and billing defaults per organization |
| **Integration Settings** | Per-organization Stripe, Twilio (SMS), SendGrid (email), and Google Maps API key management — keys stored encrypted at rest |
| **Automated Notifications** | Job confirmation emails/SMS on creation; en-route and completion status notifications via Twilio and SendGrid |
| **Message Templates** | Reusable message templates for job notifications and customer communications |
| **Item Catalog** | Maintain a reusable catalog of services and parts for quick line item entry |
| **Roles & Permissions** | Five built-in roles scoped per organization with granular permission control |
| **Two-Factor Auth** | Optional TOTP 2FA for all user accounts via Laravel Fortify |
| **Technician PWA** | Mobile-optimized progressive web app with offline support and background sync for field technicians |

---

## Tech Stack

| Layer | Technology |
| ----- | ---------- |
| Backend | PHP 8.4 · Laravel 12 |
| Frontend | Vue 3 · TypeScript · Inertia.js |
| Styling | Tailwind CSS v4 · Reka UI (headless primitives) |
| Auth | Laravel Fortify (registration, password reset, 2FA) |
| Permissions | spatie/laravel-permission v7 |
| Route types | Laravel Wayfinder (auto-generated TypeScript bindings) |
| Payments | Stripe (hosted checkout + webhooks) |
| SMS | Twilio |
| Email | SendGrid |
| Maps | Google Maps API |
| Testing | Pest v4 |
| Dev tooling | Vite 7 · Laravel Pint · Prettier · ESLint |

---

## Data Model

```text
Organization
├── OrganizationSettings (branding, tax rate, encrypted integration keys)
├── Users (with roles)
├── Customers
│   └── Properties (service locations)
├── Job Types
│   └── JobTypeChecklistItems (checklist templates)
├── Jobs (field_jobs)
│   ├── Job Line Items
│   ├── Job Checklist Items (auto-copied from type template on creation)
│   ├── Job Messages (email/SMS log)
│   ├── Invoice (1:1)
│   └── Attachments (polymorphic)
├── Estimates
│   ├── EstimatePackages (tiered: Good/Better/Best)
│   ├── Estimate Line Items
│   └── Attachments (polymorphic)
├── Items (catalog)
├── Invoices
│   ├── Invoice Line Items
│   ├── Payments
│   └── Attachments (polymorphic)
├── DriverLocations (technician GPS trail)
└── MessageTemplates
```

Every resource is scoped to an **Organization**, enabling clean multi-tenant data isolation.

---

## Roles

| Role | Description |
| ---- | ----------- |
| `owner` | Full access — typically the business owner / org creator |
| `admin` | Full access — can manage users and settings |
| `dispatcher` | Manage customers, properties, jobs, invoices, and payments |
| `technician` | View and update job status; read-only customers and properties |
| `bookkeeper` | Full invoice and payment access; read-only jobs and customers |

---

## Getting Started

### Requirements

- PHP 8.4+
- Composer
- Node.js 20+ and npm
- A local database (SQLite works out of the box for development)

### First-time setup

```bash
git clone <repo-url> fieldops-hub
cd fieldops-hub
composer run setup
```

This single command installs all dependencies, generates your app key, runs migrations, and builds frontend assets.

### Start the dev server

```bash
composer run dev
```

Starts four concurrent processes: PHP server, queue worker, Pail log viewer, and Vite HMR — all with color-coded output.

### Seed demo data

```bash
php artisan db:seed
```

Creates a demo organization with four users (one per role), four job types, and three sample customers. All demo users share the password `password`.

---

## Development Commands

### Testing

```bash
composer run test                          # Clear config cache then run all tests
./vendor/bin/pest                          # Run Pest directly
./vendor/bin/pest tests/Feature/Auth       # Run a specific directory
./vendor/bin/pest --filter "test name"     # Run a single test by name
```

The test suite uses an in-memory SQLite database — no setup required. The `composer run test` script passes `-d memory_limit=512M` automatically as the suite has grown large.

### Code style

```bash
vendor/bin/pint                # PHP (Laravel Pint)
npm run format                 # Prettier — write
npm run format:check           # Prettier — check only
npm run lint                   # ESLint — auto-fix
```

### Building

```bash
npm run build                  # Client-only Vite build
npm run build:ssr              # SSR build (client + server bundle)
```

---

## Project Structure

```text
app/
├── Http/
│   ├── Controllers/
│   │   ├── Owner/             # Owner web app controllers
│   │   │   ├── ReportingController.php   # Dashboard KPIs + all reports
│   │   │   ├── SettingsController.php    # Company + integration settings
│   │   │   └── ...
│   │   ├── Technician/        # Technician PWA controllers
│   │   ├── Auth/              # Fortify-style auth controllers
│   │   └── Settings/          # Profile, password, 2FA settings
│   ├── Middleware/            # HandleInertiaRequests, HandleAppearance, SecurityHeaders
│   └── Requests/              # Form request validation
├── Models/                    # Eloquent models (all org-scoped)
└── Providers/                 # FortifyServiceProvider

database/
├── migrations/                # Chronological schema history
├── seeders/                   # DemoSeeder, RolesAndPermissionsSeeder
└── factories/                 # Model factories for testing

resources/js/
├── pages/
│   ├── Owner/                 # Owner web app pages
│   │   ├── Dashboard.vue      # KPI dashboard
│   │   ├── Reports/           # JobsByType, JobProfitability, TechnicianPerformance
│   │   ├── Settings/          # Company.vue, Integrations.vue
│   │   └── ...
│   └── Technician/            # Technician PWA pages
├── layouts/                   # OwnerLayout, TechnicianLayout, AuthLayout
├── components/
│   ├── *.vue                  # App-level components (sidebar, nav)
│   └── ui/                    # Headless UI primitives (each has index.ts)
├── composables/               # useAppearance, useInitials, useTwoFactorAuth
└── types/index.d.ts           # AppPageProps, Auth, User, NavItem, BreadcrumbItem

routes/
├── web.php                    # Owner + public + technician routes
├── api.php                    # Technician JSON API (location, jobs)
├── auth.php                   # Auth routes (login, register, reset, verify)
└── settings.php               # Settings routes (profile, password, appearance, 2FA)
```

---

## Request Flow

1. Browser hits a Laravel route (`routes/web.php`, `routes/auth.php`, or `routes/settings.php`)
2. Controller calls `Inertia::render('PageName', $props)`
3. Inertia serves the matching Vue page at `resources/js/pages/PageName.vue`
4. `HandleInertiaRequests` middleware automatically shares `auth.user`, `name`, `quote`, and `sidebarOpen` to every page

---

## Environment

Copy `.env.example` to `.env` and adjust as needed. Key settings:

```env
APP_URL=http://fieldops-hub.test
DB_CONNECTION=sqlite           # or mysql / pgsql for production

MAIL_MAILER=log                # Change for real email delivery

# Stripe — required for online card payments
STRIPE_KEY=
STRIPE_SECRET=
STRIPE_WEBHOOK_SECRET=

# Twilio — required for SMS notifications
TWILIO_SID=
TWILIO_TOKEN=
TWILIO_FROM=

# SendGrid — required for transactional email
SENDGRID_API_KEY=

# Google Maps — required for dispatch map
GOOGLE_MAPS_API_KEY=
```

Integration keys can also be managed per-organization through the **Settings → Integrations** UI, where they are stored encrypted in the database.

---

## Security Notes

- All owner routes require authentication (`auth` + `verified` middleware)
- All queries are scoped to `organization_id` — no cross-tenant data leakage
- Integration API keys are stored using Laravel's `encrypted` cast (AES-256-CBC)
- Keys are masked in the settings UI (last 4 chars visible); submitting a masked value does not overwrite the stored key
- Logo uploads are validated for file type (`image` rule) and size (2 MB limit)
- Stripe webhook endpoint verifies the request signature before processing
- CSRF protection is enabled on all routes except the Stripe webhook
- HTTP security headers set on every response: `X-Content-Type-Options`, `X-Frame-Options`, `Referrer-Policy`, `Permissions-Policy`, and `Strict-Transport-Security` (HTTPS only)
- Rate limiting on all auth endpoints: login (5/min), registration (10/min), password reset (5/min), 2FA (5/min)
- Public estimate endpoints are throttled (30 req/min) to prevent token enumeration
- Session cookie secure flag should be set via `SESSION_SECURE_COOKIE=true` in production

---

## Changelog

See [CHANGELOG.md](CHANGELOG.md) for a full history of notable changes.

---

## License

MIT
