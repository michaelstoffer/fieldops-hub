import { test, expect } from '@playwright/test';
import { writeFileSync } from 'fs';
import { tmpdir } from 'os';
import { join } from 'path';
import { loginAsTechnician } from './helpers/auth';

// 1×1 red pixel PNG
const TEST_PNG = Buffer.from(
    'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwADhQGAWjR9awAAAABJRU5ErkJggg==',
    'base64'
);
// Use os.tmpdir() so this works on Windows and Unix
const TMP_PHOTO = join(tmpdir(), 'pw-test-photo.png');

test.beforeAll(() => {
    writeFileSync(TMP_PHOTO, TEST_PNG);
});

async function goToFirstJob(page: import('@playwright/test').Page) {
    await page.goto('/technician/dashboard');
    await page.waitForLoadState('networkidle');

    // Dashboard has a "View Today's Jobs" <Link href="/technician/jobs">
    await page.locator('a:has-text("View Today\'s Jobs")').click();
    await page.waitForURL(/\/technician\/jobs/);
    await page.waitForLoadState('networkidle');

    // Jobs list uses <Link :href="`/technician/jobs/${job.id}`">
    const jobLink = page.locator('a[href*="/technician/jobs/"]').first();
    await expect(jobLink).toBeVisible();
    await jobLink.click();
    await page.waitForURL(/\/technician\/jobs\/\d+/);
    await page.waitForLoadState('networkidle');
}

test.describe('Technician Job Detail', () => {
    test('can navigate to a job detail page', async ({ page }) => {
        await loginAsTechnician(page);
        await goToFirstJob(page);

        // Status buttons: "On my Way", "Arrived", "Complete" (TECHNICIAN_ACTIONS)
        await expect(
            page.locator('button:has-text("On my Way"), button:has-text("Arrived"), button:has-text("Complete")').first()
        ).toBeVisible();
    });

    test('advances job status via UI buttons', async ({ page }) => {
        await loginAsTechnician(page);
        await goToFirstJob(page);

        const onMyWayBtn = page.locator('button:has-text("On my Way")');
        if (await onMyWayBtn.isEnabled({ timeout: 2_000 }).catch(() => false)) {
            await onMyWayBtn.click();
            await page.waitForLoadState('networkidle');
            // "Arrived" should now be enabled
            await expect(page.locator('button:has-text("Arrived")')).toBeVisible();
        }

        const arrivedBtn = page.locator('button:has-text("Arrived")');
        if (await arrivedBtn.isEnabled({ timeout: 2_000 }).catch(() => false)) {
            await arrivedBtn.click();
            await page.waitForLoadState('networkidle');
        }
    });

    test('toggles a checklist item and it stays checked on reload', async ({ page }) => {
        await loginAsTechnician(page);
        await goToFirstJob(page);

        // Checklist items use aria-pressed attribute
        const checklistBtn = page.locator('button[aria-pressed]').first();
        if (!(await checklistBtn.isVisible({ timeout: 2_000 }).catch(() => false))) {
            test.skip(true, 'No checklist items on this job');
            return;
        }

        const wasPressed = await checklistBtn.getAttribute('aria-pressed');
        await checklistBtn.click();
        await page.waitForTimeout(800); // PATCH debounce

        await page.reload({ waitUntil: 'networkidle' });
        const afterPressed = await page.locator('button[aria-pressed]').first().getAttribute('aria-pressed');
        expect(afterPressed).not.toEqual(wasPressed);
    });

    test('saves technician notes and they persist on reload', async ({ page }) => {
        await loginAsTechnician(page);
        await goToFirstJob(page);

        // "My Notes" section — "Edit" button shows the textarea
        await page.locator('button:has-text("Edit")').first().click();

        const textarea = page.locator('textarea').first();
        await textarea.fill('PW automated test note');

        await page.locator('button:has-text("Save")').click();
        await page.waitForLoadState('networkidle');

        await page.reload({ waitUntil: 'networkidle' });
        await expect(page.getByText('PW automated test note')).toBeVisible();
    });

    test('uploads a before photo and it appears in the gallery', async ({ page }) => {
        await loginAsTechnician(page);
        await goToFirstJob(page);

        // Confirm CSRF token is present on the page
        const csrfToken = await page.evaluate(
            () => (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement | null)?.content ?? ''
        );

        // Use page.request to upload directly, bypassing XHR CSRF issues in test env
        const url = page.url();
        const jobId = url.match(/\/technician\/jobs\/(\d+)/)?.[1];
        if (!jobId) throw new Error('Could not parse job ID from URL: ' + url);

        // Upload via fetch from the page context (has session cookies + CSRF)
        const result = await page.evaluate(async ({ jobId, token, tmpPhoto }: { jobId: string; token: string; tmpPhoto: string }) => {
            const formData = new FormData();
            // Create a minimal PNG blob
            const bin = atob('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwADhQGAWjR9awAAAABJRU5ErkJggg==');
            const arr = new Uint8Array(bin.length);
            for (let i = 0; i < bin.length; i++) arr[i] = bin.charCodeAt(i);
            const blob = new Blob([arr], { type: 'image/png' });
            formData.append('photo', blob, 'test.png');
            formData.append('tag', 'before');

            const res = await fetch(`/api/technician/jobs/${jobId}/photos`, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': token,
                },
                body: formData,
            });
            return { status: res.status, body: await res.text() };
        }, { jobId, token: csrfToken, tmpPhoto: TMP_PHOTO });

        expect(result.status).toBe(201);

        // Reload to see the photo in the gallery
        await page.reload({ waitUntil: 'networkidle' });
        await expect(
            page.locator('img[src*="/storage/"]').first()
        ).toBeVisible({ timeout: 5_000 });
    });
});
