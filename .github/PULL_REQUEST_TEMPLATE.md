## Description

<!-- What does this PR do? Why? Link any related issues with "Closes #123". -->

## Type of change

- [ ] Bug fix (non-breaking change that fixes an issue)
- [ ] New feature (non-breaking change that adds functionality)
- [ ] Breaking change (fix or feature that changes existing behaviour)
- [ ] Documentation update

## How has this been tested?

<!-- Describe the tests you ran. Include commands if relevant. -->

- [ ] Feature tests added / updated
- [ ] Tested manually in browser

## Checklist

- [ ] `composer run test` passes locally
- [ ] `vendor/bin/pint` has been run (PHP style)
- [ ] `npm run format && npm run lint` have been run (frontend style)
- [ ] All new Eloquent queries are scoped to `organization_id` (multi-tenant isolation)
- [ ] No secrets, API keys, or `.env` values committed
- [ ] New environment variables are documented in `.env.example`
- [ ] Migrations have a working `down()` method
