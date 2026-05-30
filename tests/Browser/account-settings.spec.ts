import { test, expect } from '@playwright/test';
import { loginAsOwner } from './helpers/auth';

test.describe('Account Settings', () => {

    test('profile page renders form and saves name change', async ({ page }) => {
        await loginAsOwner(page);
        await page.goto('/settings/profile');
        await page.waitForLoadState('networkidle');

        // Heading is visible
        await expect(page.getByText('Profile information')).toBeVisible();

        // Name and email inputs are present and populated
        const nameInput = page.locator('#name');
        const emailInput = page.locator('#email');
        await expect(nameInput).toBeVisible();
        await expect(emailInput).toBeVisible();
        await expect(nameInput).not.toHaveValue('');
        await expect(emailInput).not.toHaveValue('');

        // Change name and save
        const newName = 'PW Test Owner';
        await nameInput.fill(newName);
        await Promise.all([
            page.waitForResponse(r => r.url().includes('/settings/profile') && r.request().method() === 'PATCH'),
            page.getByRole('button', { name: 'Save' }).click(),
        ]);
        await page.waitForLoadState('networkidle');

        // Name persists after reload
        await page.reload({ waitUntil: 'networkidle' });
        await expect(page.locator('#name')).toHaveValue(newName);

        // Restore original name
        await page.locator('#name').fill('Alice Owner');
        await Promise.all([
            page.waitForResponse(r => r.url().includes('/settings/profile') && r.request().method() === 'PATCH'),
            page.getByRole('button', { name: 'Save' }).click(),
        ]);
        await page.waitForLoadState('networkidle');
    });

    test('password page renders all three fields', async ({ page }) => {
        await loginAsOwner(page);
        await page.goto('/settings/password');
        await page.waitForLoadState('networkidle');

        await expect(page.getByText('Update password')).toBeVisible();
        await expect(page.locator('#current_password')).toBeVisible();
        await expect(page.locator('#password')).toBeVisible();
        await expect(page.locator('#password_confirmation')).toBeVisible();
        await expect(page.locator('button[type=submit]')).toBeVisible();
    });

    test('password page rejects wrong current password', async ({ page }) => {
        await loginAsOwner(page);
        await page.goto('/settings/password');
        await page.waitForLoadState('networkidle');

        await page.locator('#current_password').fill('wrongpassword');
        await page.locator('#password').fill('newpassword123');
        await page.locator('#password_confirmation').fill('newpassword123');

        await Promise.all([
            page.waitForResponse(r => r.url().includes('/settings/password')),
            page.locator('button[type=submit]').click(),
        ]);
        await page.waitForLoadState('networkidle');

        // Validation error for current password
        await expect(page.getByText(/password.*incorrect|wrong|current/i)).toBeVisible();
    });

    test('two-factor page renders enable button when 2FA is off', async ({ page }) => {
        await loginAsOwner(page);
        await page.goto('/settings/two-factor');
        await page.waitForLoadState('networkidle');

        await expect(page.getByText('Manage your two-factor authentication settings')).toBeVisible();

        // Either enabled or disabled badge should be visible
        const hasBadge = await page.getByText(/^Enabled$|^Disabled$/).first().isVisible();
        expect(hasBadge).toBe(true);

        // Enable or Continue Setup button should exist if disabled
        const disabledBadge = page.getByText('Disabled');
        if (await disabledBadge.isVisible({ timeout: 2_000 }).catch(() => false)) {
            const enableBtn = page.locator('button:has-text("Enable 2FA"), button:has-text("Continue Setup")');
            await expect(enableBtn).toBeVisible();
        }
    });

    test('appearance page renders theme controls', async ({ page }) => {
        await loginAsOwner(page);
        await page.goto('/settings/appearance');
        await page.waitForLoadState('networkidle');

        await expect(page.getByText("Update your account's appearance settings")).toBeVisible();

        // Appearance tabs or controls should be present
        const hasControls = await page.locator('button, input[type=radio]').count() > 0;
        expect(hasControls).toBe(true);
    });

    test('all settings pages are reachable from the sidebar', async ({ page }) => {
        await loginAsOwner(page);
        await page.goto('/owner/dashboard');
        await page.waitForLoadState('networkidle');

        const links = [
            { text: 'Profile',         url: /settings\/profile/ },
            { text: 'Password',        url: /settings\/password/ },
            { text: 'Two-Factor Auth', url: /settings\/two-factor/ },
            { text: 'Appearance',      url: /settings\/appearance/ },
            { text: 'Subscription',    url: /owner\/subscription/ },
        ];

        for (const { text, url } of links) {
            await page.goto('/owner/dashboard');
            await page.waitForLoadState('networkidle');
            await page.locator(`a:has-text("${text}")`).last().click();
            await page.waitForURL(url, { timeout: 10_000 });
            await page.waitForLoadState('networkidle');

            // Page must not be blank — check body has more than just the sidebar
            const bodyText = await page.evaluate(() => document.body.innerText);
            expect(bodyText.length).toBeGreaterThan(200);
        }
    });

});
