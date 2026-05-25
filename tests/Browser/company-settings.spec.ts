import { test, expect } from '@playwright/test';
import { writeFileSync } from 'fs';
import { loginAsOwner } from './helpers/auth';

// 1×1 red pixel PNG
const TEST_PNG = Buffer.from(
    'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwADhQGAWjR9awAAAABJRU5ErkJggg==',
    'base64'
);
const TMP_LOGO = 'test-logo-pw.png';

test.beforeAll(() => {
    writeFileSync(TMP_LOGO, TEST_PNG);
});

test.describe('Company Settings', () => {
    test('saves company name and it persists on reload', async ({ page }) => {
        await loginAsOwner(page);
        await page.goto('/owner/settings/company');
        await page.waitForLoadState('networkidle');

        // Company Name is the first text input in the Company Info section
        const companyNameInput = page.locator('input[type=text]').first();
        await companyNameInput.fill('PW Updated Co');

        await Promise.all([
            page.waitForResponse(r => r.url().includes('/owner/settings/company') && r.request().method() === 'POST'),
            page.locator('button[type=submit]').click(),
        ]);
        await page.waitForLoadState('networkidle');

        // Value should be present immediately after save (Inertia reloads props)
        await expect(page.locator('input[type=text]').first()).toHaveValue('PW Updated Co');

        // And after a hard reload
        await page.reload({ waitUntil: 'networkidle' });
        await expect(page.locator('input[type=text]').first()).toHaveValue('PW Updated Co');
    });

    test('uploads a logo and it persists after reload and navigation', async ({ page }) => {
        await loginAsOwner(page);
        await page.goto('/owner/settings/company');
        await page.waitForLoadState('networkidle');

        // Upload logo file
        await page.locator('input[type=file][accept="image/*"]').setInputFiles(TMP_LOGO);

        // Local preview appears immediately
        await expect(page.locator('img[alt="Company logo"]')).toBeVisible();

        // Submit and wait for the POST + redirect
        const [response] = await Promise.all([
            page.waitForResponse(r => r.url().includes('/owner/settings/company') && r.request().method() === 'POST'),
            page.locator('button[type=submit]').click(),
        ]);
        expect(response.status()).toBe(302);

        // Wait for Inertia to reload the page with the server-side logo_path
        await page.waitForLoadState('networkidle');
        await page.waitForFunction(
            () => {
                const img = document.querySelector('img[alt="Company logo"]') as HTMLImageElement | null;
                return img && img.src.includes('/storage/');
            },
            { timeout: 10_000 }
        );

        const srcAfterSave = await page.locator('img[alt="Company logo"]').getAttribute('src');
        expect(srcAfterSave).toContain('/storage/logos/');

        // Hard reload — logo must persist
        await page.reload({ waitUntil: 'networkidle' });
        const srcAfterReload = await page.locator('img[alt="Company logo"]').getAttribute('src');
        expect(srcAfterReload).toContain('/storage/logos/');

        // Navigate away and back — logo must still be there
        await page.goto('/owner/dashboard');
        await page.waitForLoadState('networkidle');
        await page.goto('/owner/settings/company');
        await page.waitForLoadState('networkidle');
        const srcAfterNav = await page.locator('img[alt="Company logo"]').getAttribute('src');
        expect(srcAfterNav).toContain('/storage/logos/');
    });

    test('removes a logo and it is gone after reload', async ({ page }) => {
        await loginAsOwner(page);
        await page.goto('/owner/settings/company');
        await page.waitForLoadState('networkidle');

        // Only run if a logo is already present
        const removeLogo = page.locator('button:has-text("Remove logo")');
        if (!(await removeLogo.isVisible())) {
            // Upload one first
            await page.locator('input[type=file][accept="image/*"]').setInputFiles(TMP_LOGO);
            await Promise.all([
                page.waitForResponse(r => r.url().includes('/owner/settings/company') && r.request().method() === 'POST'),
                page.locator('button[type=submit]').click(),
            ]);
            await page.waitForLoadState('networkidle');
        }

        page.once('dialog', d => d.accept());
        await Promise.all([
            page.waitForResponse(r => r.url().includes('/owner/settings/company/logo') && r.request().method() === 'DELETE'),
            page.locator('button:has-text("Remove logo")').click(),
        ]);
        await page.waitForLoadState('networkidle');

        // After removal the img should be gone
        await page.reload({ waitUntil: 'networkidle' });
        await expect(page.locator('img[alt="Company logo"]')).toHaveCount(0);
    });
});
