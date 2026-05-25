import { test, expect } from '@playwright/test';
import { loginAsOwner } from './helpers/auth';

test.describe('Estimate Builder', () => {
    test('creates an estimate with a line item and correct total', async ({ page }) => {
        await loginAsOwner(page);
        await page.goto('/owner/estimates/create');
        await page.waitForLoadState('networkidle');

        // Customer: first <select> on the page
        await page.locator('select').first().selectOption({ index: 1 });

        // Title: placeholder is "e.g. HVAC Service Estimate"
        await page.fill('input[placeholder="e.g. HVAC Service Estimate"]', 'PW Estimate Test');

        // Add a line item to the "Good" tier (all tiers active by default)
        await page.locator('button:has-text("+ Add line item")').first().click();

        // Item name: placeholder "Item name *"
        await page.locator('input[placeholder="Item name *"]').last().fill('Test Service');

        // Price: placeholder "Price"
        await page.locator('input[placeholder="Price"]').last().fill('150');

        // Qty: placeholder "Qty"
        await page.locator('input[placeholder="Qty"]').last().fill('2');

        // Line total shows inline (150 * 2 = 300) — package total in header
        await expect(page.locator('text=$300.00').first()).toBeVisible({ timeout: 3_000 });

        // Submit
        await page.locator('button[type=submit]').click();
        await page.waitForURL(/\/owner\/estimates\/\d+/, { timeout: 15_000 });

        await expect(page.getByText('PW Estimate Test').first()).toBeVisible();
    });

    test('toggling tiers shows and hides package forms', async ({ page }) => {
        await loginAsOwner(page);
        await page.goto('/owner/estimates/create');
        await page.waitForLoadState('networkidle');

        // All three tiers (Good, Better, Best) are active by default
        // Click "Better" to deactivate it
        await page.locator('button:has-text("Better")').click();

        // v-show hides section — check visible "Add line item" buttons drop to 2
        const visibleAddBtns = page.locator('button:has-text("+ Add line item"):visible');
        await expect(visibleAddBtns).toHaveCount(2, { timeout: 2_000 });

        // Click "Better" again to reactivate
        await page.locator('button:has-text("Better")').click();
        await expect(visibleAddBtns).toHaveCount(3, { timeout: 2_000 });
    });

    test('estimate totals update live as prices change', async ({ page }) => {
        await loginAsOwner(page);
        await page.goto('/owner/estimates/create');
        await page.waitForLoadState('networkidle');

        await page.locator('button:has-text("+ Add line item")').first().click();

        const priceInput = page.locator('input[placeholder="Price"]').last();
        const qtyInput = page.locator('input[placeholder="Qty"]').last();

        await qtyInput.fill('3');
        await priceInput.fill('100');
        await priceInput.blur();

        // Total: 300
        await expect(page.locator('text=$300.00').first()).toBeVisible({ timeout: 3_000 });

        // Change price — total updates
        await priceInput.fill('200');
        await priceInput.blur();

        // Total: 600
        await expect(page.locator('text=$600.00').first()).toBeVisible({ timeout: 3_000 });
    });
});
