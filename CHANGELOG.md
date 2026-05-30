# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Security

- **Mandatory 2FA for owner and admin accounts** — `RequireTwoFactor` middleware redirects privileged users to the 2FA setup page until a TOTP device is confirmed
- **Laravel Policies** added for `Invoice`, `Job`, `Customer`, `Estimate`, and `Payment` — all resource controllers now use `$this->authorize()` instead of ad-hoc `abort_unless()` checks, with role + org-scoping enforced in one place
- **Content-Security-Policy header** added to `SecurityHeaders` middleware — restricts script, style, frame, and connection sources; Stripe iframes explicitly allowed
- **Magic-byte file validation** — new `ValidImage` rule verifies image uploads by reading file header bytes, not just the declared MIME type or extension; applied to logo and job photo uploads
- **API rate limiting** — technician API endpoints now have per-user throttle limits: reads 120/min, mutations 60/min, photo uploads 20/min
- **Password confirmation timeout** reduced from 3 hours to 10 minutes
- **Attachment disk default** changed from `public` to `local` (private); set `ATTACHMENT_DISK=public` in `.env` for local dev or `ATTACHMENT_DISK=s3` for production object storage

## [0.9.0-alpha] - 2026-05-25

### Added

- **Real-time broadcasting** — Laravel Echo + Reverb integration; driver location updates broadcast on organisation-scoped private channels, keeping the dispatch map live without polling
- **Privacy Policy and Terms of Service** pages with full legal copy; linked from the marketing footer
- **Quick-create flows in job form** — inline dialogs to create a new Job Type or Property without leaving the job create/edit page; results auto-selected on save
- **Recent jobs on customer profile** — last 10 jobs shown on the customer detail page with status badges and links
- **Catalog item activation/deactivation** — items and job types can now be deactivated to hide them from pickers without deleting them
- **Dashboard quick-create actions** — `+ New Job`, `+ New Customer`, and `+ New Invoice` shortcut buttons on the owner dashboard
- **Playwright end-to-end test suite** — browser tests covering company settings (logo upload/remove, name save), estimate builder (live totals, tier toggling), invoice payment flow (send, full payment, partial + remainder), job lifecycle (create, advance status, cancel), and technician job detail (navigation, status buttons, notes, photo upload); runs against the local Herd dev site

### Changed

- Owner sidebar navigation now uses Lucide icons throughout for visual consistency
- Team Members page migrated to `OwnerLayout` and `useForm`; role dropdown width fixed to prevent text clipping
- Company Settings form refactored to `useForm` with `forceFormData: true`; logo field now correctly included in the multipart POST
- Table row click navigation standardised across Jobs, Invoices, and Estimates — rows use `@click="router.visit()"` for a consistent pointer cursor and hover state
- Default reporting date ranges standardised across Jobs by Type, Profitability, and Technician Performance reports
- `app.blade.php` now includes `<meta name="csrf-token">` so XHR requests (photo uploads, checklist toggles, technician API calls) can read the token correctly

### Fixed

- **Company logo not persisting after save** — `useForm` definition was missing the `logo` field; the file was being built into a `FormData` object that Inertia never sent. Logo now saves correctly and persists across reloads and navigation
- **`ReferenceError: route is not defined`** on the Team Members page — all `route()` helper calls replaced with Wayfinder-generated TypeScript bindings (`@/routes/…`)
- Technician PWA photo uploads were silently returning HTTP 419 (CSRF token mismatch) because `meta[name="csrf-token"]` was absent from the HTML shell — now included in `app.blade.php`
- SSR compatibility for browser-specific APIs (`window`, `navigator`, `document`) guarded behind `typeof window !== 'undefined'` checks
- HTTPS enforced in production; `SESSION_SECURE_COOKIE` set to `true`
- `.env.development` removed from repository; `.gitignore` updated to exclude all `.env.*` variants

### Security

- `SecurityHeaders` middleware sets `X-Content-Type-Options`, `X-Frame-Options`, `Referrer-Policy`, `Permissions-Policy`, and `Strict-Transport-Security` on every response
- Rate limiting on registration (10/min), forgot-password (5/min), and public estimate token endpoints (30/min)

### Tests

- Playwright browser test suite added (`npm run test:browser`) — 16 tests across 5 spec files covering JS-driven interactions that PHP/Pest tests cannot reach
- `FrontendRouteUsageTest` added — scans all `.vue` files and fails if any call the global `route()` helper, preventing regression of the Wayfinder migration
- Feature tests added for catalog item CRUD

## [0.8.14-alpha] - 2026-05-14

### Added

- Billing interval toggle (monthly/annual) on the registration plan selection step, with live price switching
- Annual pricing displayed on plan cards for founding member invites, with regular price struck through
- Atomic founding member coupon redemption using a database transaction and row-level lock to prevent overselling

### Changed

- Founding member invite now lands on plan selection (step 1) so users can see locked-in prices before continuing
- Registration validates and stores `billing_interval` (monthly or annual) on the trial subscription record

### Fixed

- Founding member `?founding=1` link now shows the callout and pricing on page load rather than requiring navigation to step 2

### Performance

- Self-hosted Figtree font (eliminated render-blocking external CDN request); font files preloaded in `<head>`
- Vite build target set to `es2020` — smaller output, no legacy polyfills for modern browsers
- CSS code splitting enabled — each page loads only its own stylesheet
- HTTP caching (`Cache-Control: immutable, max-age=31536000`) and gzip compression added to `.htaccess` for all static assets
- Composite index added on `attachments(organization_id, attachable_type, attachable_id)` to avoid full table scans on morphMany queries
- Email sending switched from synchronous `Mail::send` to queued `Mail::queue` in `MessageDispatcher`

### Tests

- Removed 20 redundant, duplicate, and boilerplate test cases
- Centralised `RolesAndPermissionsSeeder` into `TestCase::$seeder` — eliminated ~30 repeated seeder calls across test files; suite runs ~37% faster
- Removed unused `tw-animate-css` npm dependency

## [0.8.13-alpha] - 2026-05-14

### Added

- Scroll-reveal animations on the marketing page: sections and cards fade up into view as the user scrolls, using IntersectionObserver with staggered delays on feature cards, how-it-works steps, pricing cards, and FAQ items
- Smooth scrolling for marketing page navigation links (Features, Pricing, FAQ)
- Back-to-top button fixed to the bottom-right corner of the marketing page, visible after scrolling down
- Logo click scrolls to top of the marketing page
- `FUNDING.yml` with GitHub Sponsors, Buy Me a Coffee, and Ko-fi links
- Laravel Boost and MCP server configuration for AI-assisted local development

### Fixed

- `public/hot` presence causing the production build to load Vite dev server URLs instead of the compiled manifest

## [0.8.12-alpha] - 2026-05-14

### Added

- `CONTRIBUTING.md` with local setup, branch naming, coding standards, and PR guidelines
- `CHANGELOG.md` in Keep a Changelog format
- `CODE_OF_CONDUCT.md` based on Contributor Covenant 2.1
- `SECURITY.md` with supported versions, reporting instructions, scope, and disclosure policy
- Pull request template with type of change checkboxes and pre-submit checklist
- Issue templates for bug reports and feature requests, replacing the generic GitHub defaults
- `ISSUE_TEMPLATE/config.yml` disabling blank issues and linking to GitHub Discussions
- Release badge added to README; Changelog section linked from README

### Changed

- Security vulnerability contact email updated to <security@fieldops-hub.com>

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

[Unreleased]: https://github.com/michaelstoffer/fieldops-hub/compare/v0.9.0-alpha...HEAD
[0.9.0-alpha]: https://github.com/michaelstoffer/fieldops-hub/compare/v0.8.14-alpha...v0.9.0-alpha
[0.8.14-alpha]: https://github.com/michaelstoffer/fieldops-hub/compare/v0.8.13-alpha...v0.8.14-alpha
[0.8.13-alpha]: https://github.com/michaelstoffer/fieldops-hub/compare/v0.8.12-alpha...v0.8.13-alpha
[0.8.12-alpha]: https://github.com/michaelstoffer/fieldops-hub/compare/v0.8.11-alpha...v0.8.12-alpha
[0.8.11-alpha]: https://github.com/michaelstoffer/fieldops-hub/compare/v0.8.10-alpha...v0.8.11-alpha
[0.8.10-alpha]: https://github.com/michaelstoffer/fieldops-hub/compare/v0.8.9-alpha...v0.8.10-alpha
[0.8.9-alpha]: https://github.com/michaelstoffer/fieldops-hub/compare/v0.1.0...v0.8.9-alpha
[0.1.0]: https://github.com/michaelstoffer/fieldops-hub/releases/tag/v0.1.0
