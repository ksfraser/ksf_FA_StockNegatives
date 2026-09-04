/**
 * E2E Tests for Stock Negatives Report Module
 *
 * Run with: npx playwright test
 *
 * @BABOK Related: FR-RE-001-001
 * @since 1.0.0
 */

const { test, expect } = require('@playwright/test');

const BASE_URL = process.env.FA_URL || 'http://localhost/frontaccounting';

test.describe('Stock Negatives Report', () => {
    test.beforeEach(async ({ page }) => {
        await page.goto(`${BASE_URL}/index.php`);
        await page.waitForLoadState('networkidle');
    });

    test('SN-E2E-001: Access negative stock report', async ({ page }) => {
        await page.login('admin', 'admin');

        await page.goto(`${BASE_URL}/modules/ksf_FA_StockNegatives/pages/stock_negatives.php`);

        await expect(page.locator('h1')).toContainText('Stock Negatives');
    });

    test('SN-E2E-002: Generate report with no filters', async ({ page }) => {
        await page.login('admin', 'admin');

        await page.goto(`${BASE_URL}/modules/ksf_FA_StockNegatives/pages/stock_negatives.php`);

        await page.click('button[type="submit"][name="generate"]');

        await page.waitForSelector('.report-results, .no-results');

        const hasResults = await page.locator('.report-results').isVisible();
        const hasNoResults = await page.locator('.no-results').isVisible();

        expect(hasResults || hasNoResults).toBeTruthy();
    });

    test('SN-E2E-003: Filter by category', async ({ page }) => {
        await page.login('admin', 'admin');

        await page.goto(`${BASE_URL}/modules/ksf_FA_StockNegatives/pages/stock_negatives.php`);

        const categorySelect = page.locator('select[name="category_id"]');
        if (await categorySelect.isVisible()) {
            const options = await categorySelect.locator('option').allTextContents();
            expect(options.length).toBeGreaterThan(1);

            await page.selectOption('select[name="category_id"]', { index: 1 });
            await page.click('button[type="submit"][name="generate"]');

            await page.waitForLoadState('networkidle');
        }
    });

    test('SN-E2E-004: Sort by quantity ascending', async ({ page }) => {
        await page.login('admin', 'admin');

        await page.goto(`${BASE_URL}/modules/ksf_FA_StockNegatives/pages/stock_negatives.php`);

        await page.selectOption('select[name="sort_by"]', 'quantity');
        await page.selectOption('select[name="sort_order"]', 'ASC');

        await page.click('button[type="submit"][name="generate"]');

        await page.waitForSelector('table.negatives-report');

        const quantities = await page.locator('table.negatives-report tbody td.quantity')
            .allTextContents();

        if (quantities.length > 1) {
            const numQuantities = quantities.map(q => parseFloat(q));
            for (let i = 0; i < numQuantities.length - 1; i++) {
                expect(numQuantities[i]).toBeLessThanOrEqual(numQuantities[i + 1]);
            }
        }
    });

    test('SN-E2E-005: Download CSV', async ({ page }) => {
        await page.login('admin', 'admin');

        await page.goto(`${BASE_URL}/modules/ksf_FA_StockNegatives/pages/stock_negatives.php`);

        const downloadPromise = page.waitForEvent('download');
        await page.click('a.download-csv');
        const download = await downloadPromise;

        expect(download.suggestedFilename()).toMatch(/\.csv$/);
    });

    test('SN-E2E-006: Report shows item details', async ({ page }) => {
        await page.login('admin', 'admin');

        await page.goto(`${BASE_URL}/modules/ksf_FA_StockNegatives/pages/stock_negatives.php`);

        await page.click('button[type="submit"][name="generate"]');

        await page.waitForSelector('table.negatives-report, .no-results');

        const resultsTable = page.locator('table.negatives-report');
        if (await resultsTable.isVisible()) {
            const headers = await resultsTable.locator('thead th').allTextContents();

            expect(headers).toContain('Item Code');
            expect(headers).toContain('Description');
            expect(headers).toContain('Category');
            expect(headers).toContain('Quantity');
        }
    });
});

test.describe('Stock Negatives Report - Edge Cases', () => {
    test('SN-E2E-007: Empty result message', async ({ page }) => {
        await page.login('admin', 'admin');

        await page.goto(`${BASE_URL}/modules/ksf_FA_StockNegatives/pages/stock_negatives.php`);

        await page.click('button[type="submit"][name="generate"]');

        const noResults = page.locator('.no-results');
        const hasNoResults = await noResults.isVisible().catch(() => false);

        if (hasNoResults) {
            await expect(noResults).toContainText('No negative stock');
        }
    });

    test('SN-E2E-008: Report summary counts', async ({ page }) => {
        await page.login('admin', 'admin');

        await page.goto(`${BASE_URL}/modules/ksf_FA_StockNegatives/pages/stock_negatives.php`);

        await page.click('button[type="submit"][name="generate"]');

        const summary = page.locator('.report-summary');
        if (await summary.isVisible()) {
            const itemCount = await summary.locator('.item-count').textContent();
            const totalQty = await summary.locator('.total-qty').textContent();

            expect(parseInt(itemCount)).toBeGreaterThanOrEqual(0);
            expect(parseFloat(totalQty)).toBeLessThan(0);
        }
    });
});