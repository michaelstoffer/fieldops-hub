# FieldOps Hub

A modern field service management platform built for small-to-medium field operations businesses — HVAC, plumbing, electrical, landscaping, and more. Manage customers, schedule jobs, dispatch technicians, and handle billing from a single, fast web application.

---

## What It Does

**FieldOps Hub** gives your team a shared, real-time workspace to run field operations end to end:

| Area | Capabilities |
|------|-------------|
| **Customers & Properties** | Customer profiles with multiple service locations, address history, and attachments |
| **Job Management** | Create, assign, and track jobs through a full status lifecycle (scheduled → en route → in progress → completed) |
| **Dispatch & Scheduling** | Assign jobs to technicians, manage job types, and track field activity |
| **Invoicing** | Draft, edit, and send invoices tied to completed jobs; track line items, tax, discounts, and balance due |
| **Payments** | Record payments by method (cash, check, card, bank transfer, Stripe); automatic balance calculation |
| **Item Catalog** | Maintain a reusable catalog of services and parts for quick invoice line item entry |
| **Roles & Permissions** | Five built-in roles scoped per organization with granular permission control |
| **Two-Factor Auth** | Optional TOTP 2FA for all user accounts via Laravel Fortify |

---

## Tech Stack

| Layer | Technology |
|-------|-----------|
| Backend | PHP 8.4 · Laravel 12 |
| Frontend | Vue 3 · TypeScript · Inertia.js |
| Styling | Tailwind CSS v4 · Reka UI (headless primitives) |
| Auth | Laravel Fortify (registration, password reset, 2FA) |
| Permissions | spatie/laravel-permission v7 |
| Route types | Laravel Wayfinder (auto-generated TypeScript bindings) |
| Testing | Pest v4 |
| Dev tooling | Vite 7 · Laravel Pint · Prettier · ESLint |

---

## Data Model

```
Organization
├── Users (with roles)
├── Customers
│   └── Properties (service locations)
├── Job Types
├── Jobs (field_jobs)
│   ├── Job Line Items
│   ├── Invoice (1:1)
│   └── Attachments (polymorphic)
├── Items (catalog)
├── Invoices
│   ├── Invoice Line Items
│   ├── Payments
│   └── Attachments (polymorphic)
└── Attachments (polymorphic)
```

Every resource is scoped to an **Organization**, enabling clean multi-tenant data isolation.

---

## Roles

| Role | Description |
|------|-------------|
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

The test suite uses an in-memory SQLite database — no setup required.

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

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Auth/              # Breeze-style auth controllers
│   │   └── Settings/          # Profile, password, 2FA settings
│   ├── Middleware/            # HandleInertiaRequests, HandleAppearance
│   └── Requests/              # Form request validation
├── Models/                    # Eloquent models (all org-scoped)
└── Providers/                 # FortifyServiceProvider

database/
├── migrations/                # Chronological schema history
├── seeders/                   # DemoSeeder, RolesAndPermissionsSeeder
└── factories/                 # UserFactory (with withTwoFactor state)

resources/js/
├── pages/                     # Inertia page components (Vue SFCs)
├── layouts/                   # AppLayout (authenticated), AuthLayout
├── components/
│   ├── *.vue                  # App-level components (sidebar, nav)
│   └── ui/                    # Headless UI primitives (each has index.ts)
├── composables/               # useAppearance, useInitials, useTwoFactorAuth
└── types/index.d.ts           # AppPageProps, Auth, User, NavItem, BreadcrumbItem

routes/
├── web.php                    # Public and dashboard routes
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

# Optional: Stripe for card payments
STRIPE_KEY=
STRIPE_SECRET=
```

---

## License

MIT
