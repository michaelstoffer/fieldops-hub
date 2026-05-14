# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [0.8.11-alpha] - 2026-05-14

### Added

- Founding Member price lock: organisations that register via an invite link (`?founding=1`) are permanently locked in at the annual rate billed monthly, applied silently via a Stripe coupon at checkout
- `stripe:setup-founding-coupon` artisan command to create the 20%-forever Stripe coupon and write its ID to `.env`
- `founding_member` boolean column on the `organizations` table
- Founding Member invite callout on the registration form
- Price lock active banner on the Subscription page for founding members
- Map now fits bounds on first load when technician markers are present
- Demo technician location seeding for dispatch map simulation
- Perpetual trial subscription and checkout restrictions in the demo environment

## [0.8.10-alpha]

### Added

- Laravel Telescope integration for debugging and monitoring (dev only)
- GitHub Actions deployment workflow to development environment

### Fixed

- Telescope service provider conditionally registered to prevent conflicts in production
- Asset synchronisation path corrected in deployment workflow
- Forge webhook triggered before asset sync in deployment pipeline
- Replaced SCP with rsync for asset synchronisation

## [0.8.9-alpha]

### Added

- Branded FieldOps Hub favicon and web app manifest
- Registration page layout refactor with improved UX
- Role-based dashboard redirection for authenticated users
- Improved Twilio SMS sending logic

### Fixed

- Node heap size increased to 4 GB for Vite build in CI to prevent OOM crashes
- Auth page directory casing corrected for case-sensitive Linux CI compatibility
- Wayfinder generated routes and runtime helper committed to git so CI build succeeds without a generation step
- Wayfinder duplicate route exports resolved
- Wayfinder routes generated before Vite build in CI
- Register and login links updated to correct URL format
- TypeScript syntax corrected in `Expired.vue` props definition
- Deploy job skips gracefully when `DEPLOY_HOST` secret is not configured
- Tests now run on the `development` branch in CI

## [0.1.0] - 2025-11-30

### Added

- Initial application — Laravel 12 + Vue 3 + TypeScript + Inertia.js
- Multi-tenant organisation model with role-based access (owner, admin, dispatcher, technician, bookkeeper) via spatie/laravel-permission
- Customer and property management with full CRUD
- Job scheduling, assignment, and full status lifecycle (scheduled → en route → in progress → completed)
- Per-job-type checklist templates that auto-copy onto new jobs; technicians check off items in the field
- Multi-tier estimate packages (Good/Better/Best) with shareable public acceptance link
- Live dispatch map with real-time technician GPS tracking and location trail history
- Invoicing with line items, discounts, tax, and balance due tracking
- Payment recording by method (cash, check, card, bank transfer)
- Stripe-hosted checkout for online card payments on invoices
- Owner KPI dashboard with real-time metrics
- Reports: jobs by type, job profitability, technician performance
- Automated SMS (Twilio) and email (SendGrid) job notifications with reusable message templates
- Technician mobile PWA with offline support and background sync via service worker
- Per-organisation integration settings with encrypted key storage (Stripe, Twilio, SendGrid, Google Maps)
- Two-factor authentication (TOTP) via Laravel Fortify
- 14-day free trial with Stripe subscription management and plan enforcement middleware
- Item catalogue for reusable services and parts on line items
- Setup wizard for first-time organisation onboarding
- Demo seeder with sample organisation, users, job types, and customers
- GitHub Actions CI: test suite (PHP 8.4 + Node 22) and linter (Pint + Prettier + ESLint)

[Unreleased]: https://github.com/michaelstoffer/fieldops-hub/compare/v0.8.11-alpha...HEAD
[0.8.11-alpha]: https://github.com/michaelstoffer/fieldops-hub/compare/v0.8.10-alpha...v0.8.11-alpha
[0.8.10-alpha]: https://github.com/michaelstoffer/fieldops-hub/compare/v0.8.9-alpha...v0.8.10-alpha
[0.8.9-alpha]: https://github.com/michaelstoffer/fieldops-hub/compare/v0.1.0...v0.8.9-alpha
[0.1.0]: https://github.com/michaelstoffer/fieldops-hub/releases/tag/v0.1.0
