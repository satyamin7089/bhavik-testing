/* Controlled Home/About fixtures, not a live WordPress integration test.
 * NODE_PATH=/path/to/node_modules node tests/test_category_tabs.cjs
 * Needs playwright-core and either CHROMIUM_PATH or @sparticuz/chromium.
 * Optional CATEGORY_SCREENSHOTS directory and CATEGORY_FONT local font file.
 */
const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');
const http = require('node:http');
const { chromium } = require('playwright-core');
const root = path.resolve(__dirname, '..');
const section = name => fs.readFileSync(path.join(root, `page-template/template-${name}.php`), 'utf8')
  .match(/<section class="ab-section ab-product-categories"[\s\S]*?<\/section>/)[0]
  .replace(/<\?php echo esc_url\( (?:home_url|get_theme_file_uri)\( '([^']+)' \) \); \?>/g, '$1');
const html = name => `<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="/assets/css/${name === 'home' ? 'bhavik' : 'about'}.css">
<link rel="stylesheet" href="/assets/css/product-categories.css"><link rel="stylesheet" href="/assets/css/site.css">
${process.env.CATEGORY_FONT ? '<style>@font-face{font-family:"Reddit Sans";src:url(/font.ttf)}</style>' : ''}
</head><body><header class="ab-mainnav" style="position:static;height:80px;padding:20px">Bhavik Enterprises · category review</header>
<main class="${name === 'home' ? 'ab-bhavik-page' : ''}">${section(name)}</main><footer style="height:300px"></footer><script src="/assets/js/product-categories.js"></script></body></html>`;
const server = http.createServer((req, res) => {
  const url = new URL(req.url, 'http://fixture.test');
  if (url.pathname === '/') { res.setHeader('Content-Type', 'text/html'); res.end(html(url.searchParams.get('page') === 'about' ? 'about' : 'home')); return; }
  if (url.pathname === '/font.ttf' && process.env.CATEGORY_FONT) { res.end(fs.readFileSync(process.env.CATEGORY_FONT)); return; }
  const file = path.resolve(root, '.' + url.pathname);
  if (!file.startsWith(path.join(root, 'assets') + path.sep) || !fs.existsSync(file)) { res.writeHead(404); res.end(); return; }
  res.setHeader('Content-Type', file.endsWith('.css') ? 'text/css' : file.endsWith('.js') ? 'text/javascript' : 'image/webp');
  res.end(fs.readFileSync(file));
});
const delay = ms => new Promise(resolve => setTimeout(resolve, ms));
(async () => {
  await new Promise(resolve => server.listen(0, '127.0.0.1', resolve));
  let browser;
  try {
    const module = process.env.CHROMIUM_PATH ? null : require('@sparticuz/chromium');
    const packaged = module && (module.default || module);
    browser = await chromium.launch({ executablePath: process.env.CHROMIUM_PATH || await packaged.executablePath(), args: packaged ? packaged.args : ['--no-sandbox'], headless: true });
    const page = await browser.newPage();
    // The supplied remote PNG is intentionally not fetched in offline tests.
    // Its exact per-tab source and contain/bottom/no-filter rules are checked.
    const grainsUrl = 'https://bhavikenterprises.in/wp-content/uploads/2026/11/image-grains.png';
    await page.route(grainsUrl, route => route.abort());
    const errors = [];
    page.on('pageerror', e => errors.push(e.message));
    const url = `http://127.0.0.1:${server.address().port}/`;
    for (const layout of (process.env.CATEGORY_INTERACTION_ONLY ? [] : (process.env.CATEGORY_LAYOUTS || 'home,about').split(','))) {
      for (const width of (process.env.CATEGORY_WIDTHS || '1920,1440,1280,1200,1199,1101,1100,1024,850,768,700,600,420,390,320').split(',').map(Number)) {
        // Unload the previous fixture before resizing, so its ResizeObserver
        // and smooth rail scrolling cannot race the next viewport case.
        await page.goto('about:blank');
        await page.setViewportSize({ width, height: 1000 });
        await page.goto(url + '?page=' + layout);
        const root = page.locator('[data-category-tabs]');
        await page.waitForFunction(() => document.querySelector('[data-category-tabs]').dataset.tabsReady === 'true');
        assert.equal(await root.locator('[role="tab"]').count(), 7);
        assert.equal(await root.locator('[role="tab"][aria-selected="true"]').getAttribute('data-material'), 'hdpe');
        assert.equal(await root.locator('[role="tabpanel"]:visible').count(), 1);
        const beforeY = await page.evaluate(() => scrollY);
        await root.locator('.ab-category-next').click();
        assert.equal(await root.locator('[aria-selected="true"]').getAttribute('data-material'), 'pp');
        await root.locator('.ab-category-prev').click();
        await root.locator('.ab-category-prev').click();
        for (const material of ['ldpe','hdpe','pp','pvc','ps','abs','eva']) {
          if (material !== 'ldpe') await root.locator('.ab-category-next').click();
          const panel = root.locator('[role="tabpanel"]:visible');
          assert.equal(await panel.getAttribute('data-material'), material);
          assert.equal(await panel.locator('.ab-category-card').count(), 4);
          for (const suffix of ['', '-prime', '-offgrade', '-recycled', '-scrap-regrind']) assert.equal(await panel.locator(`a[href="/${material}${suffix}/"]`).count(), 1);
          const result = await panel.evaluate(e => ({
            overflow: document.documentElement.scrollWidth > innerWidth,
            clipped: [...e.querySelectorAll('a,h5,p')].filter(n => n.scrollWidth > n.clientWidth + 2).map(n => n.textContent.trim()),
            columns: getComputedStyle(e.querySelector('.ab-category-grid')).gridTemplateColumns.split(' ').length,
            cards: [...e.querySelectorAll('.ab-category-card')].map(n => ({y:n.offsetTop,height:n.offsetHeight,width:n.offsetWidth,pTop:n.querySelector('p').offsetTop,linkBottom:n.querySelector('a').offsetTop+n.querySelector('a').offsetHeight})),
          }));
          const imagery = await panel.evaluate(e => [...e.querySelectorAll('.ab-category-card')].map(card => {
            const c = card.getBoundingClientRect();
            const image = card.querySelector('.ab-category-card-art');
            const i = image.getBoundingClientRect();
            const style = getComputedStyle(image);
            return { top: Math.abs(c.top-i.top), bottom: Math.abs(c.bottom-i.bottom), fit: style.objectFit, opacity: style.opacity };
          }));
          assert(imagery.every(i => i.top < 1 && i.bottom < 1 && i.fit === 'cover' && i.opacity === '1'), `Slanted images do not fill cards: ${layout} ${width}px ${JSON.stringify(imagery)}`);
          const tabsStyle = await root.locator('.ab-category-tab img').evaluateAll(images => images.map(img => {
            const style = getComputedStyle(img);
            return { source: img.src, height: style.height, fit: style.objectFit, position: style.objectPosition, filter: style.filter, mask: style.maskImage, opacity: style.opacity };
          }));
          assert(tabsStyle.every(i => i.source === 'https://bhavikenterprises.in/wp-content/uploads/2026/11/image-grains.png' && i.height === '80px' && i.fit === 'contain' && i.position === '50% 100%' && i.filter === 'none' && i.mask === 'none' && i.opacity === '1'), 'Grain image alignment, sizing or clarity changed');
          const borders = await root.locator('.ab-category-tab, .ab-category-panel:not([hidden]), .ab-category-card, .ab-category-card a, .ab-category-details').evaluateAll(nodes => nodes.map(n => getComputedStyle(n).borderWidth));
          assert(borders.every(b => b === '0px'), 'Decorative borders returned');
          const geometry = await panel.evaluate(e => {
            const image = e.querySelector('.ab-category-card-art');
            return { imageRatio: image.getBoundingClientRect().width / image.parentElement.getBoundingClientRect().width, cut: getComputedStyle(e.querySelector('.ab-category-card-copy'), '::before').clipPath };
          });
          const expectedImageRatio = width >= 1200 ? .5 : width <= 420 ? .32 : .4;
          assert(Math.abs(geometry.imageRatio - expectedImageRatio) < .005, 'Slanted image width changed outside its breakpoint');
          if (width >= 1200) assert.equal(geometry.cut, 'polygon(0px 0px, 70% 0px, 50% 100%, 0px 100%)');
          const tabHeights = await root.locator('.ab-category-tab').evaluateAll(nodes => nodes.map(n => n.getBoundingClientRect().height));
          assert(tabHeights.every(height => height >= 176 && height <= 200), `Tabs are not compact: ${tabHeights}`);
          const tabSpacing = await root.locator('.ab-category-tab').evaluateAll(nodes => nodes.map(tab => tab.querySelector('img').getBoundingClientRect().top - tab.querySelector('span').getBoundingClientRect().bottom));
          assert(tabSpacing.every(gap => gap >= 3), 'Compact tabs overlap their labels and grain images');
          assert.equal(result.overflow, false, `${layout} overflow at ${width}`);
          assert.deepEqual(result.clipped, [], `${layout} clipped labels at ${width}`);
          assert.equal(result.columns, width > 700 ? 2 : 1);
          const row = result.cards.slice(0, result.columns);
          assert(row.every(n => Math.abs(n.height-row[0].height)<2 && Math.abs(n.width-row[0].width)<2 && Math.abs(n.pTop-row[0].pTop)<2 && Math.abs(n.linkBottom-row[0].linkBottom)<2), 'Cards, descriptions or buttons do not align');
        }
        assert(await root.locator('.ab-category-next').isDisabled());
        await root.locator('[role="tab"][aria-selected="true"]').evaluate(e => e.focus({preventScroll:true}));
        await page.keyboard.press('Home');
        assert(await root.locator('.ab-category-prev').isDisabled());
        await page.keyboard.press('ArrowRight');
        assert.equal(await root.locator('[aria-selected="true"]').getAttribute('data-material'), 'hdpe');
        assert.equal(await page.evaluate(() => document.activeElement.dataset.material), 'hdpe');
        assert.equal(await page.evaluate(() => scrollY), beforeY, 'Tab selection moved page vertically');
        await delay(280);
        if (process.env.CATEGORY_SCREENSHOTS && layout === 'home' && [1440,768,390].includes(width)) {
          fs.mkdirSync(process.env.CATEGORY_SCREENSHOTS, { recursive: true });
          await root.screenshot({ path: path.join(process.env.CATEGORY_SCREENSHOTS, `categories-${width}.png`) });
        }
        console.log(`${layout} ${width}px: tabs, four cards, routes, alignment and keyboard pass`);
      }
    }
    if (process.env.CATEGORY_SKIP_INTERACTIONS) return;
    await page.goto(url);
    const frames = await page.evaluate(() => {
      document.querySelector('.ab-category-next').click();
      return document.querySelector('.ab-category-panel:not([hidden])').getAnimations()[0].effect.getKeyframes();
    });
    assert.equal(frames[0].transform, 'translateY(22px)');
    const backwards = await page.evaluate(() => {
      document.querySelector('.ab-category-prev').click();
      return document.querySelector('.ab-category-panel:not([hidden])').getAnimations()[0].effect.getKeyframes();
    });
    assert.equal(backwards[0].transform, 'translateY(-22px)');
    await page.evaluate(() => { for(let i=0;i<8;i++) { document.querySelector('.ab-category-next').click(); document.querySelector('.ab-category-prev').click(); } });
    assert.equal(await page.locator('[role="tabpanel"]:visible').count(), 1, 'Rapid changes left multiple panels visible');
    const context = await browser.newContext({ hasTouch: true, isMobile: true, viewport: { width: 390, height: 850 } });
    const touch = await context.newPage();
    await touch.route(grainsUrl, route => route.abort());
    await touch.goto(url);
    const rail = touch.locator('.ab-category-polymers');
    const box = await rail.boundingBox();
    const before = await rail.evaluate(e => e.scrollLeft);
    const cdp = await context.newCDPSession(touch);
    const y = box.y + 100;
    await cdp.send('Input.dispatchTouchEvent', { type: 'touchStart', touchPoints: [{ x: 310, y }] });
    for (let i = 1; i <= 16; i++) { await cdp.send('Input.dispatchTouchEvent', { type: 'touchMove', touchPoints: [{ x: 310-i*13, y }] }); await delay(16); }
    await cdp.send('Input.dispatchTouchEvent', { type: 'touchEnd', touchPoints: [] });
    await delay(150);
    assert((await rail.evaluate(e => e.scrollLeft)) > before + 60, 'Tab rail does not swipe');
    await touch.setViewportSize({ width: 850, height: 390 });
    await touch.locator('.ab-category-next').dispatchEvent('click');
    assert.equal(await touch.locator('[aria-selected="true"]').getAttribute('data-material'), 'pp');
    const reduced = await browser.newPage({ reducedMotion: 'reduce' });
    await reduced.route(grainsUrl, route => route.abort());
    await reduced.goto(url);
    await reduced.locator('[data-material="pp"][role="tab"]').evaluate(e => e.focus({preventScroll:true}));
    await reduced.keyboard.press('Enter');
    assert.equal(await reduced.locator('[role="tabpanel"]:visible').evaluate(e => e.getAnimations().length), 0);
    const fallback = await browser.newPage({ javaScriptEnabled: false });
    await fallback.route(grainsUrl, route => route.abort());
    await fallback.goto(url + '?page=about');
    assert.equal(await fallback.locator('.ab-category-panel:visible').count(), 7);
    assert.equal(await fallback.locator('.ab-category-panel a').count(), 35);
    assert.equal(await fallback.locator('.ab-category-toolbar').isVisible(), false);
    assert.deepEqual(errors, []);
    console.log('Touch swiping, rotation, reduced motion and no-JS fallback pass.');
  } finally {
    if (browser) await browser.close();
    await new Promise(resolve => server.close(resolve));
  }
})().catch(e => { console.error(e); process.exitCode = 1; });
