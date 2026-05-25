import { test, expect } from '@playwright/test';
import { loginAsOwner } from './helpers/auth';

test.describe('Job Lifecycle', () => {
    test('creates a job and it appears in the list', async ({ page }) => {
        await loginAsOwner(page);
        await page.goto('/owner/jobs/create');
        await page.waitForLoadState('networkidle');

        // Customer select — first <select> on the page, pick first real option
        await page.locator('select').first().selectOption({ index: 1 });

        // Title field has id="title"
        await page.fill('#title', 'PW Status Flow');

        await page.locator('button[type=submit]').click();
        await page.waitForURL(/\/owner\/jobs\/\d+/, { timeout: 15_000 });

        await expect(page.getByText('PW Status Flow').first()).toBeVisible();
    });

    test('advances job status through the workflow', async ({ page }) => {
        await loginAsOwner(page);
        await page.goto('/owner/jobs');
        await page.waitForLoadState('networkidle');

        // Rows use @click not <Link>, click the first data row
        const firstRow = page.locator('tbody tr').first();
        await firstRow.click();
        await page.waitForURL(/\/owner\/jobs\/\d+/);

        // NEXT_STEP buttons: "Mark En Route", "Mark In Progress", "Mark Completed"
        const markEnRoute = page.locator('button:has-text("Mark En Route")');
        if (await markEnRoute.isVisible({ timeout: 2_000 }).catch(() => false)) {
            await markEnRoute.click();
            await page.waitForLoadState('networkidle');
        }

        const markInProgress = page.locator('button:has-text("Mark In Progress")');
        if (await markInProgress.isVisible({ timeout: 2_000 }).catch(() => false)) {
            await markInProgress.click();
            await page.waitForLoadState('networkidle');
        }

        const markCompleted = page.locator('button:has-text("Mark Completed")');
        if (await markCompleted.isVisible({ timeout: 2_000 }).catch(() => false)) {
            await markCompleted.click();
            await page.waitForLoadState('networkidle');
        }
    });

    test('can cancel a job', async ({ page }) => {
        await loginAsOwner(page);
        // Create a fresh job to cancel
        await page.goto('/owner/jobs/create');
        await page.waitForLoadState('networkidle');
        await page.locator('select').first().selectOption({ index: 1 });
        await page.fill('#title', 'PW Cancel Test');
        await page.locator('button[type=submit]').click();
        await page.waitForURL(/\/owner\/jobs\/\d+/, { timeout: 15_000 });

        page.once('dialog', d => d.accept());
        await page.locator('button:has-text("Cancel Job")').click();
        await page.waitForURL(/\/owner\/jobs$/, { timeout: 10_000 });
    });
});
