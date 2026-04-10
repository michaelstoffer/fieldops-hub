# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Stack

Laravel 12 (PHP 8.2+) backend + Vue 3 + TypeScript frontend, connected via Inertia.js. Authentication is handled by Laravel Fortify (with 2FA support). UI components come from a Reka UI / shadcn-style library with Tailwind CSS v4.

## Commands

### Development
```bash
composer run dev          # Starts all services concurrently: PHP server, queue worker, Pail log viewer, Vite
```

### Build
```bash
npm run build             # Vite build (client only)
npm run build:ssr         # Vite build for SSR (client + server)
composer run setup        # Full first-time setup: install deps, .env, key:generate, migrate, npm install & build
```

### Testing
```bash
composer run test                        # Clear config cache then run full test suite
./vendor/bin/pest                        # Run Pest directly
./vendor/bin/pest tests/Feature/Auth     # Run a specific test directory
./vendor/bin/pest --filter "test name"   # Run a single test by name
```
Tests use an in-memory SQLite database (configured in `phpunit.xml`).

### Linting & Formatting
```bash
vendor/bin/pint           # PHP code style (Laravel Pint)
npm run format            # Prettier (writes)
npm run format:check      # Prettier (check only)
npm run lint              # ESLint (auto-fix)
```

## Architecture

### Request flow
1. Browser hits a Laravel route (`routes/web.php` or `routes/settings.php`)
2. Controller returns `Inertia::render('PageName', $props)` — this renders the matching Vue page at `resources/js/pages/PageName.vue`
3. `HandleInertiaRequests` middleware shares global props to every page: `auth.user`, `name`, `quote`, `sidebarOpen`

### Frontend structure
- **Pages** live in `resources/js/pages/` — Inertia maps route responses directly to these files
- **Layouts** in `resources/js/layouts/` — `AppLayout.vue` (re-exports `AppSidebarLayout`) for authenticated pages, `AuthLayout.vue` for auth pages
- **Components** in `resources/js/components/` — app-level components (sidebar, header, nav) at the top level; headless UI primitives under `components/ui/` (each component folder has an `index.ts` barrel)
- **Composables** in `resources/js/composables/` — `useAppearance` (light/dark theme), `useInitials`, `useTwoFactorAuth`
- **Types** defined in `resources/js/types/index.d.ts` — `AppPageProps`, `Auth`, `User`, `NavItem`, `BreadcrumbItem`
- Path alias `@/` maps to `resources/js/`

### Route typing (Wayfinder)
`@laravel/vite-plugin-wayfinder` auto-generates TypeScript bindings for Laravel named routes. Use the generated helpers instead of hardcoding URLs.

### Auth
Fortify handles all auth routes (login, register, password reset, email verification, 2FA). Views are registered in `FortifyServiceProvider::configureViews()` to render Inertia pages. Custom user creation/password reset logic lives in `app/Actions/Fortify/`.

### Settings
Settings routes are in `routes/settings.php` (all under `auth` middleware). Controllers are in `app/Http/Controllers/Settings/`.

### SSR
SSR entry point is `resources/js/ssr.ts`. Run with `composer run dev:ssr` or build with `npm run build:ssr` then `php artisan inertia:start-ssr`.
