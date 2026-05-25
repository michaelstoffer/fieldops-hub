import { test, expect } from '@playwright/test';
import { loginAs } from './helpers/auth';

/**
 * Setup Wizard — multi-step onboarding flow.
 *
 * Uses a dedicated fresh-org user so it doesn't interfere with the demo seed.
 * The wizard requires: step 1 company info → step 2 job types → step 3 technicians.
 */

const WIZARD_EMAIL = 'wizard-test@fieldops.test';

test.beforeAll(async ({ browser }) => {
    // Seed a fresh org + owner with no setup completed via artisan
    const page = await browser.newPage();
    await page.goto('/');
    await page.close();
});

test.describe('Setup Wizard', () => {
    test('completes all 3 steps and redirects to dashboard', async ({ page }) => {
        await loginAs(page, WIZARD_EMAIL);
        await page.goto('/owner/setup');
        await page.waitForLoadState('networkidle');

        // ── Step 1: Company Info ─────────────────────────────────────────────────
        await expect(page.getByText('Company Info')).toBeVisible();

        await page.fill('input[placeholder*="name"], input[id*="name"]', 'Browser Test Co');
        await page.fill('input[type=email]', 'test@browsertest.com');

        await page.click('button:has-text("Save & Continue")');
        await page.waitForLoadState('networkidle');

        // ── Step 2: Job Types ────────────────────────────────────────────────────
        await expect(page.getByText('Job Types')).toBeVisible();

        // "Continue" disabled until at least one job type added
        const continueBtn = page.locator('button:has-text("Continue")');
        await expect(continueBtn).toBeDisabled();

        // Add a job type
        await page.fill('input[placeholder*="job type"], input[placeholder*="name"]', 'HVAC Service');
        // Pick first colour
        await page.locator('button[class*="rounded-full"]').first().click();
        await page.click('button:has-text("Add")');

        await expect(page.getByText('HVAC Service')).toBeVisible();
        await expect(continueBtn).toBeEnabled();
        await continueBtn.click();
        await page.waitForLoadState('networkidle');

        // ── Step 3: Technicians ──────────────────────────────────────────────────
        await expect(page.getByText('Technicians')).toBeVisible();

        await page.fill('input[placeholder*="Name"]', 'Jane Tech');
        await page.fill('input[type=email]', 'jane@browsertest.com');
        await page.fill('input[type=password]', 'password123');
        await page.click('button:has-text("Add Technician")');

        await expect(page.getByText('Jane Tech')).toBeVisible();

        const finishBtn = page.locator('button:has-text("Finish Setup")');
        await expect(finishBtn).toBeEnabled();
        await finishBtn.click();

        // Should redirect to dashboard
        await page.waitForURL(/\/owner\/dashboard/, { timeout: 15_000 });
        await expect(page).toHaveURL(/\/owner\/dashboard/);
    });
});
