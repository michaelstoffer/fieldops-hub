# Security Policy

FieldOps Hub handles sensitive business data — customer personally identifiable information, service location details, financial records, and payment history. We take security seriously and appreciate responsible disclosure from the security community.

---

## Supported Versions

Only the latest release on the `production` branch receives security patches. We do not backport fixes to older releases.

| Branch / Version | Security Updates |
|-----------------|:----------------:|
| `production` (latest) | ✅ |
| Older commits | ❌ |

---

## Reporting a Vulnerability

**Please do not open a public GitHub issue for security vulnerabilities.**

Report vulnerabilities privately using one of the following methods:

- **GitHub Private Advisory** — [Submit via GitHub Security Advisories](../../security/advisories/new) *(preferred)*
- **Email** — Send details to the repository owner through GitHub's contact information

### What to include in your report

To help us triage quickly, please provide as much of the following as possible:

- A clear description of the vulnerability and its potential impact
- The component, route, or feature affected
- Step-by-step reproduction instructions
- Proof-of-concept code or a screenshot (if applicable)
- Your suggested severity (Critical / High / Medium / Low)
- Whether you have already disclosed this to any third party

---

## Response Timeline

| Milestone | Target |
|-----------|--------|
| Acknowledgement of report | Within **48 hours** |
| Initial triage and severity assessment | Within **5 business days** |
| Fix or mitigation deployed | Within **30 days** for Critical/High, **90 days** for Medium/Low |
| Public disclosure coordination | After fix is deployed and you are notified |

We will keep you updated throughout the process and credit you in the release notes unless you prefer to remain anonymous.

---

## Scope

### In scope

The following are valid targets for security research:

- **Authentication** — login, registration, password reset, 2FA (TOTP), session management
- **Authorization** — role-based access control, permission enforcement, cross-tenant data access
- **API endpoints** — all routes in `routes/web.php`, `routes/auth.php`, and `routes/settings.php`
- **File uploads** — attachment handling, MIME type validation, path traversal
- **Data exposure** — unintended PII or financial data leakage in Inertia shared props or API responses
- **Injection** — SQL injection, XSS, command injection, mass assignment
- **Multi-tenancy** — ability to access or modify another organization's data

### Out of scope

The following are explicitly excluded:

- Denial of service (DoS/DDoS) attacks
- Spam, phishing, or social engineering attacks against users or staff
- Vulnerabilities in third-party dependencies that are already publicly disclosed (report these to the upstream project)
- Issues requiring physical access to infrastructure
- Missing HTTP security headers on non-sensitive static assets
- Rate limiting on endpoints that do not process sensitive data
- Self-XSS that cannot be used to attack other users
- Theoretical vulnerabilities without a working proof-of-concept
- The `.env.example` file or other non-production configuration examples

---

## Security Controls

For context when evaluating the attack surface, FieldOps Hub includes the following security controls:

| Control | Implementation |
|---------|---------------|
| Authentication | Laravel Fortify — bcrypt password hashing, configurable 2FA (TOTP) |
| Authorization | spatie/laravel-permission v7 — per-organization roles and granular permissions |
| Session management | Server-side sessions with CSRF protection on all state-changing requests |
| Multi-tenancy isolation | All domain models are scoped by `organization_id` at the Eloquent model layer |
| Input validation | Laravel Form Requests with strict validation rules on all write endpoints |
| XSS protection | Inertia.js renders data server-side; cookie values are whitelisted before use in views |
| Password policy | Laravel's `Password::defaults()` rule set enforced on all password fields |
| Sensitive data in transit | Passwords and 2FA secrets are excluded from all Inertia shared props |
| Soft deletes | Customer, property, invoice, and item records are soft-deleted, not permanently destroyed |
| File attachments | Stored on a configurable disk; path and MIME type are recorded separately from the stored file |

---

## Disclosure Policy

We follow a **coordinated disclosure** model:

1. Reporter submits a private report.
2. We triage, reproduce, and assess severity.
3. We develop and test a fix.
4. We deploy the fix to `production`.
5. We notify the reporter and agree on a disclosure date (typically 7 days after the fix ships).
6. We publish a security advisory crediting the reporter (unless anonymity is requested).

We ask that you:

- Give us a reasonable amount of time to fix the issue before any public disclosure.
- Avoid accessing, modifying, or deleting data that does not belong to a test account you control.
- Not perform testing that degrades service availability for other users.

---

## Hall of Fame

We gratefully acknowledge security researchers who have helped improve FieldOps Hub. Researchers who responsibly disclose valid vulnerabilities will be listed here (with permission).

*No entries yet.*

---

## Legal

Security research conducted in good faith, following this policy, will not result in legal action from us. We consider this a safe-harbor for researchers acting within the scope defined above.
