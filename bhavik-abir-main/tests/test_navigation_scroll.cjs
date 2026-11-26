/* Real input regression: scrollIntoView() alone can bypass nested scroll traps.
 * Dependencies: playwright-core and @sparticuz/chromium (or CHROMIUM_PATH).
 * Run: NODE_PATH=/path/to/node_modules node tests/test_navigation_scroll.cjs
 * This serves a controlled header fixture, not a full WordPress installation.
 */
const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');
const http = require('node:http');
const { chromium } = require('playwright-core');
const root = path.resolve(__dirname, '..');
const partial = fs.readFileSync(path.join(root, 'template-parts/products-mega-menu.php'), 'utf8')
  .replace(/<\?php echo esc_url\( home_url\( '([^']+)' \) \); \?>/g, '$1')
  .replace(/<\?php echo \$extra_links;[\s\S]*?\?>/, '<a href="/custom-product/">Custom product link</a>')
  .replace(/<\?php[\s\S]*?\?>/g, '');
const html = `<!doctype html><html lang="en"><head><meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="/material.css"><link rel="stylesheet" href="/site.css"></head><body>
<div class="ab-topbar" style="height:60px">Contact details</div>
<header class="ab-mainnav" id="site-header"><div class="ab-wrap ab-nav-inner">
<a class="ab-logo-slot" href="/">Bhavik</a>
<button class="ab-nav-toggle" aria-expanded="false" aria-controls="primary-navigation">Menu</button>
<nav class="ab-nav-links" id="primary-navigation"><ul class="ab-nav-menu">
<li><a href="/">Home</a></li><li><a href="/about/">About</a></li>
<li class="menu-item-has-children ab-menu-mega"><a href="/products/">Products</a>${partial}</li>
<li><a href="/industries/">Industries</a></li><li><a href="/contact/">Contact</a></li>
<li class="ab-nav-quote"><a href="/quote/">Get a Custom Quote</a></li></ul></nav>
</div></header><main style="height:2200px;padding:50px"><h1>Navigation scroll fixture</h1></main>
<script src="/navigation.js"></script></body></html>`;
const routes = {
  '/': ['text/html', html],
  '/site.css': ['text/css', fs.readFileSync(path.join(root, 'assets/css/site.css'))],
  '/material.css': ['text/css', fs.readFileSync(path.join(root, 'assets/css/material.css'))],
  '/navigation.js': ['text/javascript', fs.readFileSync(path.join(root, 'assets/js/navigation.js'))],
};
const server = http.createServer((req, res) => {
  const route = routes[req.url];
  res.writeHead(route ? 200 : 404, { 'Content-Type': route ? route[0] : 'text/plain' });
  res.end(route ? route[1] : 'Not found');
});
const delay = ms => new Promise(resolve => setTimeout(resolve, ms));
(async () => {
  await new Promise(resolve => server.listen(0, '127.0.0.1', resolve));
  let browser;
  try {
    const module = process.env.CHROMIUM_PATH ? null : require('@sparticuz/chromium');
    const packaged = module && (module.default || module);
    browser = await chromium.launch({
      executablePath: process.env.CHROMIUM_PATH || await packaged.executablePath(),
      args: packaged ? packaged.args : ['--no-sandbox'], headless: true,
    });
    const page = await browser.newPage();
    const errors = [];
    page.on('pageerror', error => errors.push(error.message));
    const url = `http://127.0.0.1:${server.address().port}/`;
    const sizes = [[1440,650], [1101,500], [1100,650], [1024,768], [768,1024], [600,500], [390,640], [320,480], [844,390]];
    for (const [width, height] of sizes) {
      await page.setViewportSize({ width, height });
      await page.goto(url);
      const mobile = width <= 1100;
      if (mobile) {
        await page.locator('.ab-nav-toggle').click();
        await page.locator('.ab-menu-mega > .ab-submenu-toggle').click();
      } else {
        await page.locator('.ab-menu-mega > a').focus();
      }
      const scroller = mobile ? '.ab-nav-links' : '.ab-mega-menu';
      const box = await page.locator('.ab-mega-menu').boundingBox();
      const x = box.x + box.width / 2;
      const y = Math.min(box.y + 120, height - 50);
      await page.mouse.move(x, y);
      const before = await page.locator(scroller).evaluate(e => e.scrollTop);
      await page.mouse.wheel(0, 400);
      await page.waitForFunction(({selector, before}) => document.querySelector(selector).scrollTop > before + 30,
        { selector: scroller, before }, { timeout: 2500 });
      // Wheel all the way through the cards, CTA, custom link, and outer nav.
      for (let i = 0; i < 12; i++) { await page.mouse.wheel(0, 650); await delay(40); }
      const metrics = await page.locator(scroller).evaluate(e => ({ top: e.scrollTop, height: e.clientHeight, full: e.scrollHeight, bottom: e.getBoundingClientRect().bottom }));
      assert(metrics.top + metrics.height >= metrics.full - 3, `Cannot wheel to bottom at ${width}x${height}`);
      assert(metrics.bottom <= height + 1, `Panel exceeds viewport at ${width}x${height}`);
      assert.equal(await page.evaluate(() => window.scrollY), 0, 'Background page scrolled');
      assert.equal(await page.locator('.ab-mega-menu').isVisible(), true, 'Scrolling closed the menu');
      await page.keyboard.press('Escape');
      assert.equal(await page.locator('.ab-mega-menu').isVisible(), false);
      console.log(`Wheel scrolling and bottom reachability pass: ${width}x${height}`);
    }
    // Touch input over a link, not programmatic scrollTop/scrollIntoView.
    const context = await browser.newContext({ hasTouch: true, isMobile: true, viewport: { width: 390, height: 640 } });
    const touch = await context.newPage();
    await touch.goto(url);
    await touch.locator('.ab-nav-toggle').tap();
    await touch.locator('.ab-menu-mega > .ab-submenu-toggle').tap();
    const cdp = await context.newCDPSession(touch);
    const swipe = async (x, from, to) => {
      await cdp.send('Input.dispatchTouchEvent', { type: 'touchStart', touchPoints: [{ x, y: from }] });
      for (let step = 1; step <= 16; step++) {
        await cdp.send('Input.dispatchTouchEvent', { type: 'touchMove', touchPoints: [{ x, y: from + (to - from) * step / 16 }] });
        await delay(16);
      }
      await cdp.send('Input.dispatchTouchEvent', { type: 'touchEnd', touchPoints: [] });
      await delay(150);
    };
    await swipe(170, 470, 200);
    assert(await touch.locator('.ab-nav-links').evaluate(e => e.scrollTop > 30), 'Touch gesture trapped inside mega menu');
    for (let attempt = 0; attempt < 20; attempt++) {
      if (await touch.locator('.ab-nav-links').evaluate(e => e.scrollTop + e.clientHeight >= e.scrollHeight - 3)) break;
      await swipe(170, 470, 200);
    }
    assert(await touch.locator('.ab-nav-links').evaluate(e => e.scrollTop + e.clientHeight >= e.scrollHeight - 3), 'Cannot swipe to final navigation links');
    const lastLink = await touch.locator('.ab-nav-quote a').boundingBox();
    assert(lastLink.y >= 0 && lastLink.y + lastLink.height <= 640, 'Final mobile link is clipped');
    assert.equal(await touch.evaluate(() => window.scrollY), 0);
    // Rotate while open; the outer panel must refit and remain scrollable.
    await touch.setViewportSize({ width: 844, height: 390 });
    await touch.waitForFunction(() => document.querySelector('.ab-nav-links').getBoundingClientRect().bottom <= innerHeight + 1);
    const afterRotation = await touch.locator('.ab-nav-links').evaluate(e => e.scrollTop);
    await swipe(300, 180, 340);
    assert((await touch.locator('.ab-nav-links').evaluate(e => e.scrollTop)) < afterRotation, 'Reverse touch scrolling failed');
    assert.deepEqual(errors, []);
    console.log('Touch swipes, reverse scrolling, and open-menu rotation pass.');
  } finally {
    if (browser) await browser.close();
    await new Promise(resolve => server.close(resolve));
  }
})().catch(error => { console.error(error); process.exitCode = 1; });
