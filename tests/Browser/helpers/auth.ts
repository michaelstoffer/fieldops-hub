import { type Page } from '@playwright/test';

export async function loginAs(page: Page, email: string, password = 'password') {
    await page.goto('/login');
    await page.waitForLoadState('networkidle');
    await page.fill('#email', email);
    await page.fill('#password', password);
    await page.click('button[type=submit]');
    await page.waitForURL(/\/(owner|technician)\//, { timeout: 15_000 });
}

export async function loginAsOwner(page: Page) {
    return loginAs(page, 'owner@demo.test');
}

export async function loginAsTechnician(page: Page) {
    return loginAs(page, 'tech@demo.test');
}
