import { defineConfig, devices } from '@playwright/test';

/**
 * Playwright config for the brndle E2E journey.
 *
 * Runs against the developer's Local-by-Flywheel install; default
 * baseURL is the brndle site this repo lives in (`elementor.local`).
 * Override via `BRNDLE_BASE_URL=… npx playwright test` for any other
 * site that has the theme + the dev-auto-login mu-plugin enabled.
 */
export default defineConfig({
  testDir: './',
  timeout: 30_000,
  fullyParallel: false,
  retries: 0,
  reporter: [['list']],
  use: {
    baseURL: process.env.BRNDLE_BASE_URL || 'http://elementor.local',
    headless: true,
    trace: 'retain-on-failure',
    screenshot: 'only-on-failure',
    actionTimeout: 10_000,
  },
  projects: [
    { name: 'chromium', use: { ...devices['Desktop Chrome'] } },
  ],
});
