// Screenshots of the documentation (docs/<lang>/bilder/*.png) in the back end of a test installation, marked with
// the NEO annotation layer. See README.md in this folder.
//
//   node capture.cjs <en|de>
//
// Environment: NB_BASE (default http://127.0.0.1:8000), NB_ADMIN_USER / NB_ADMIN_PASSWORD, NB_MYSQL (default
// "mysql contao"), NB_ANNOTATE (path to annotate.js of the plugin neo-doku).
const { chromium } = require('playwright');
const { execSync } = require('child_process');
const path = require('path');

const lang = process.argv[2] || 'en';
const env = (name, fallback) => process.env[name] || fallback;
const base = env('NB_BASE', 'http://127.0.0.1:8000');
const user = env('NB_ADMIN_USER', 'admin');
const password = env('NB_ADMIN_PASSWORD', '');
const mysql = env('NB_MYSQL', 'mysql contao');
const out = path.resolve(__dirname, '../../docs', lang, 'bilder');
const annotate = env('NB_ANNOTATE', path.resolve(__dirname, '../../../NEO-Claude-Plugins/plugins/neo-doku/scripts/annotate.js'));
const sql = (query) => execSync(mysql + ' -N -e "' + query.replace(/"/g, '\\"') + '"').toString().trim();

// An empty box in document coordinates for neoAnnotate.frame().
const BOX = `window.docBox = function (left, top, right, bottom) {
  var el = document.createElement('div');
  el.style.cssText = 'position:absolute;pointer-events:none;left:' + (left + scrollX) + 'px;top:' + (top + scrollY) + 'px;width:' + (right - left) + 'px;height:' + (bottom - top) + 'px';
  document.body.appendChild(el);
  return el;
};`;

(async () => {
  if (!password) {
    console.error('Set NB_ADMIN_PASSWORD.');
    process.exit(2);
  }
  // The back end in the language of the images; the user's own language is restored at the end.
  const language = sql("SELECT language FROM tl_user WHERE username = '" + user + "'");
  sql("UPDATE tl_user SET language = '" + lang + "' WHERE username = '" + user + "'");
  const browser = await chromium.launch();
  const context = await browser.newContext({
    viewport: { width: 1280, height: 800 },
    deviceScaleFactor: 2,
    reducedMotion: 'reduce',
    colorScheme: 'light',
    locale: lang === 'de' ? 'de-AT' : 'en-GB',
    timezoneId: 'Europe/Vienna',
  });
  const page = await context.newPage();

  await page.goto(base + '/contao/login');
  await page.fill('input[name="username"]', user);
  await page.fill('input[name="password"]', password);
  await Promise.all([page.waitForNavigation(), page.click('button[type="submit"]')]);

  // 1. The whole page, as it opens: sections with nothing left to do are collapsed. Five areas numbered.
  await page.goto(base + '/contao?do=neo_access');
  await page.waitForSelector('#pal_neo_license_legend');
  await page.addStyleTag({ content: '.sf-toolbar,.sf-minitoolbar{display:none!important}' });
  await page.evaluate(() => document.fonts.ready);
  // Viewport as tall as the page, so the sticky save bar sits at the end of the form.
  const height = await page.evaluate(() => document.documentElement.scrollHeight);
  await page.setViewportSize({ width: 1280, height });
  await page.waitForTimeout(300);
  await page.addScriptTag({ path: annotate });
  await page.addScriptTag({ content: BOX });
  await page.evaluate(() => {
    // Each frame from just above the first section title to just above the next area, so they do not touch.
    const frame = (selectors, number) => {
      const boxes = selectors.map((s) => document.querySelector(s)).filter(Boolean).map((e) => e.getBoundingClientRect());
      if (!boxes.length) return;
      const top = Math.min(...boxes.map((r) => r.top));
      const bottom = Math.max(...boxes.map((r) => r.bottom));
      neoAnnotate.frame(docBox(boxes[0].left + 2, top - 2, boxes[0].right - 2, bottom - 4), { number, padding: 0 });
    };
    const sections = [...document.querySelectorAll('#tl_neo_access fieldset')].map((f) => '#' + f.id);
    const own = sections.filter((id) => !['#pal_neo_license_legend', '#pal_neo_terms_legend', '#pal_ai_legend', '#pal_azure_translator_legend', '#pal_deepl_legend'].includes(id));
    frame(['#pal_neo_license_legend'], 1);
    frame(['#pal_neo_terms_legend'], 2);
    frame(['#pal_ai_legend'], 3);
    frame(['#pal_azure_translator_legend', '#pal_deepl_legend'], 4);
    frame(own, 5);
  });
  await page.screenshot({ path: path.join(out, 'access-page.png'), fullPage: true });

  // 2. "Test connection" in the section AI connection, in place. The key appears as length, start and end: masked.
  await page.setViewportSize({ width: 1280, height: 800 });
  await page.goto(base + '/contao?do=neo_access');
  await page.addStyleTag({ content: '.sf-toolbar,.sf-minitoolbar{display:none!important}' });
  await page.evaluate(() => { const f = document.getElementById('pal_ai_legend'); if (f.classList.contains('collapsed')) f.querySelector('legend button').click(); });
  await page.waitForSelector('#neo_access_ai [data-neo-access-action]');
  await page.evaluate(() => document.getElementById('pal_ai_legend').scrollIntoView({ block: 'start', behavior: 'instant' }));
  await page.waitForTimeout(400);
  await page.click('#neo_access_ai [data-neo-access-action]');
  await page.waitForFunction(() => document.querySelector('#neo_access_ai [data-neo-access-result] .tl_message'));
  await page.evaluate(() => document.fonts.ready);
  await page.addScriptTag({ path: annotate });
  await page.addScriptTag({ content: BOX });
  await page.evaluate(() => {
    const b = document.querySelector('#neo_access_ai [data-neo-access-action]').getBoundingClientRect();
    const r = document.querySelector('#neo_access_ai [data-neo-access-result]').getBoundingClientRect();
    neoAnnotate
      .frame(docBox(b.left - 4, b.top - 4, b.right + 4, b.bottom + 4), { number: 1, padding: 0 })
      .frame(docBox(r.left - 4, r.top - 2, r.right + 4, r.bottom + 4), { number: 2, padding: 0 });
  });
  // The key (the last part of the line below the result, after "·"): masked in the picture.
  await page.evaluate(() => {
    const line = document.querySelector('#neo_access_ai [data-neo-access-result] p.tl_help');
    const parts = line.textContent.split(' · ');
    const key = parts.slice(-2).join(' · ');
    const label = key.slice(0, key.indexOf(':') + 1);
    line.textContent = parts.slice(0, -2).join(' · ') + ' · ' + label + ' ';
    const box = document.createElement('span');
    box.style.cssText = 'display:inline-block;width:90px;height:1em;vertical-align:middle;background:#b8bcc2';
    line.appendChild(box);
  });
  const section = await page.locator('#pal_ai_legend').boundingBox();
  await page.screenshot({ path: path.join(out, 'test-connection.png'), clip: { x: 0, y: Math.max(0, section.y - 10), width: 1280, height: Math.min(800 - Math.max(0, section.y - 10), 460) } });

  await browser.close();
  sql("UPDATE tl_user SET language = '" + language + "' WHERE username = '" + user + "'");
  console.log(path.join(out, 'access-page.png'), path.join(out, 'test-connection.png'));
})();
