# Contributing to FieldOps Hub

Thank you for your interest in contributing. This document covers how to get the project running locally, the conventions we follow, and what to expect from the review process.

---

## Table of Contents

- [Code of Conduct](#code-of-conduct)
- [Getting Started](#getting-started)
- [Development Workflow](#development-workflow)
- [Coding Standards](#coding-standards)
- [Testing](#testing)
- [Submitting a Pull Request](#submitting-a-pull-request)
- [Reporting Bugs](#reporting-bugs)
- [Requesting Features](#requesting-features)

---

## Code of Conduct

This project follows our [Code of Conduct](CODE_OF_CONDUCT.md). By participating, you agree to uphold it. Please report unacceptable behavior to the maintainers.

---

## Getting Started

### Prerequisites

- PHP 8.4+
- Composer 2
- Node.js 20+ and npm
- Git

### Local setup

```bash
git clone https://github.com/michaelstoffer/fieldops-hub.git
cd fieldops-hub
composer run setup
```

`composer run setup` installs all dependencies, copies `.env.example` to `.env`, generates an application key, runs migrations, and builds frontend assets.

### Start the dev server

```bash
composer run dev
```

Starts PHP, queue worker, Pail log viewer, and Vite HMR concurrently.

### Seed demo data

```bash
php artisan db:seed
```

Creates a demo organization with sample users (password: `password`), job types, and customers.

---

## Development Workflow

1. **Fork** the repository and create a branch from `development`:

   ```bash
   git checkout -b feat/your-feature-name
   ```

2. **Make your changes.** Keep commits focused — one logical change per commit.

3. **Run tests and linters** before pushing (see [Testing](#testing) and [Coding Standards](#coding-standards)).

4. **Push your branch** and open a pull request against `development`.

### Branch naming

| Type | Pattern | Example |
|---|---|---|
| Feature | `feat/short-description` | `feat/estimate-pdf-export` |
| Bug fix | `fix/short-description` | `fix/invoice-tax-rounding` |
| Refactor | `refactor/short-description` | `refactor/subscription-service` |
| Docs | `docs/short-description` | `docs/api-keys-setup` |
| Chore | `chore/short-description` | `chore/update-dependencies` |

---

## Coding Standards

### PHP

We use [Laravel Pint](https://laravel.com/docs/pint) with the default Laravel preset. Run it before committing:

```bash
vendor/bin/pint
```

Rules to follow:

- Follow the existing controller / service / model patterns — see `app/Http/Controllers/Owner/` for examples
- Keep controllers thin; business logic belongs in `app/Services/`
- All Eloquent queries **must** be scoped to `organization_id` — never return cross-tenant data
- Use `$table->encrypted()` cast for any user-supplied API keys or secrets
- Prefer named routes over hardcoded URLs in controllers and templates
- Add form request classes for any validation that isn't trivial

### TypeScript / Vue

We use [Prettier](https://prettier.io) and [ESLint](https://eslint.org):

```bash
npm run format   # format all files
npm run lint     # lint and auto-fix
```

Rules to follow:

- Vue pages live in `resources/js/pages/`; components in `resources/js/components/`
- Use Inertia's `useForm` for all form submissions
- Use the generated Wayfinder route helpers (`@/routes`) instead of hardcoding URLs
- Type all Inertia props via `defineProps<{...}>()`
- Keep components focused — extract reusable logic to composables in `resources/js/composables/`

### Commits

Follow [Conventional Commits](https://www.conventionalcommits.org/):

```
feat: add estimate PDF export
fix: correct invoice tax rounding when discount applied
refactor: extract checkout session logic into SubscriptionService
docs: update env setup in README
chore: upgrade Stripe SDK to v21
```

---

## Testing

The test suite uses [Pest](https://pestphp.com/) with an in-memory SQLite database — no additional database setup required.

```bash
composer run test                          # full suite
./vendor/bin/pest tests/Feature/Auth       # specific directory
./vendor/bin/pest --filter "test name"     # single test
```

### What to test

- **Every new controller action** should have a feature test covering the happy path and at least one failure case (auth, validation, or authorization)
- **All queries must be multi-tenant safe** — write a test that proves a second organization cannot access the first's data
- **Services** with non-trivial logic should have unit tests

### Test conventions

- Use `RefreshDatabase` on feature tests
- Create test organizations and users with factories; avoid seeding
- Use `actingAs($user)` rather than manually setting session state
- Test the HTTP response and the database state — not just one of them

---

## Submitting a Pull Request

1. Fill out the pull request template completely
2. Link any related issues with `Closes #123` in the description
3. Keep PRs focused — avoid bundling unrelated changes
4. Ensure CI passes (tests + linter) before requesting review
5. Be responsive to review feedback; PRs that go stale for two weeks without activity may be closed

### What reviewers check

- Correctness and test coverage
- Multi-tenant data isolation (all queries scoped to `organization_id`)
- Security — no secrets in code, no XSS, no SQL injection, proper auth middleware
- Adherence to existing patterns (thin controllers, service layer, Wayfinder routes)
- Code style (Pint + Prettier + ESLint must pass)

---

## Reporting Bugs

Use the [Bug Report](https://github.com/michaelstoffer/fieldops-hub/issues/new?template=bug_report.md) issue template. Include:

- Steps to reproduce
- Expected vs actual behavior
- PHP version, browser, and OS
- Any relevant logs or screenshots

For security vulnerabilities, please **do not** open a public issue — see [SECURITY.md](SECURITY.md) instead.

---

## Requesting Features

Use the [Feature Request](https://github.com/michaelstoffer/fieldops-hub/issues/new?template=feature_request.md) issue template. The more context you provide about the problem you're solving, the better.

Features that align with the core use case — field service operations for small-to-medium businesses — are most likely to be accepted.
