import { test, expect } from '@playwright/test';
import { loginAsOwner } from './helpers/auth';

/** Creates a fresh draft invoice and returns the page (now on the invoice Show page). */
async function createDraftInvoice(page: import('@playwright/test').Page): Promise<void> {
    await page.goto('/owner/invoices/create');
    await page.waitForLoadState('networkidle');

    // Customer: first real option in the select
    await page.locator('select').first().selectOption({ index: 1 });

    // Add a line item name + price (Qty field uses placeholder "Qty", Price uses "Price")
    await page.locator('input[placeholder="Item name *"]').first().fill('PW Test Service');
    await page.locator('input[placeholder="Price"]').first().fill('100');

    await page.locator('button[type=submit]').click();
    await page.waitForURL(/\/owner\/invoices\/\d+/, { timeout: 15_000 });
    await page.waitForLoadState('networkidle');
}

test.describe('Invoice Payment', () => {
    test('sends a draft invoice and its status changes to sent', async ({ page }) => {
        await loginAsOwner(page);
        await createDraftInvoice(page);

        // Button: "Send to Customer" (shown for draft/overdue)
        const sendBtn = page.locator('button:has-text("Send to Customer")');
        await expect(sendBtn).toBeVisible();
        await sendBtn.click();
        await page.waitForLoadState('networkidle');

        // Status badge should now be "Sent"
        await expect(page.locator('span:has-text("Sent")').first()).toBeVisible();
    });

    test('records a full payment and invoice becomes paid', async ({ page }) => {
        await loginAsOwner(page);
        await createDraftInvoice(page);

        // Send it first so the payment section appears (not shown on draft)
        await page.locator('button:has-text("Send to Customer")').click();
        await page.waitForLoadState('networkidle');

        // Payment form is auto-shown when balance > 0 and status is sent
        // If "+ Record Payment" button is visible, click it to show the form
        const recordBtn = page.locator('button:has-text("+ Record Payment")');
        if (await recordBtn.isVisible({ timeout: 2_000 }).catch(() => false)) {
            await recordBtn.click();
        }

        const amountInput = page.locator('input[type=number]').first();
        await expect(amountInput).toBeVisible();

        const today = new Date().toISOString().split('T')[0];
        await page.locator('input[type=date]').fill(today);

        await page.locator('button:has-text("Save Payment"), button[type=submit]').last().click();
        await page.waitForLoadState('networkidle');

        await expect(page.locator('span:has-text("Paid")').first()).toBeVisible();
    });

    test('records partial payment then remainder — invoice goes paid', async ({ page }) => {
        await loginAsOwner(page);
        await createDraftInvoice(page);

        // Send first
        await page.locator('button:has-text("Send to Customer")').click();
        await page.waitForLoadState('networkidle');

        const recordBtn = page.locator('button:has-text("+ Record Payment")');
        if (await recordBtn.isVisible({ timeout: 2_000 }).catch(() => false)) {
            await recordBtn.click();
        }

        const amountInput = page.locator('input[type=number]').first();
        await expect(amountInput).toBeVisible();

        const fullAmount = parseFloat(await amountInput.inputValue() || '100');
        const partial = parseFloat((fullAmount / 2).toFixed(2));

        const today = new Date().toISOString().split('T')[0];

        // First partial payment
        await amountInput.fill(String(partial));
        await page.locator('input[type=date]').fill(today);
        await page.locator('button:has-text("Save Payment"), button[type=submit]').last().click();
        await page.waitForLoadState('networkidle');

        await expect(page.locator('span:has-text("Partial")').first()).toBeVisible();

        // Second payment clears the balance
        const recordBtn2 = page.locator('button:has-text("+ Record Payment")');
        if (await recordBtn2.isVisible({ timeout: 2_000 }).catch(() => false)) {
            await recordBtn2.click();
        }

        // The amount field may be stale — overwrite with the remaining balance
        const amountInput2 = page.locator('input[type=number]').first();
        await amountInput2.fill(String(partial));
        await page.locator('input[type=date]').fill(today);
        await page.locator('button:has-text("Save Payment"), button[type=submit]').last().click();
        await page.waitForLoadState('networkidle');

        await expect(page.locator('span:has-text("Paid")').first()).toBeVisible();
    });
});
