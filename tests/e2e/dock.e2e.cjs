/*
 * Browser test of the buttons at the edge of the screen (dock) with the three
 * extensions that put a button there: NEO Cookie Consent (button to reopen the
 * cookie settings), NEO Accessibility (floating button of the panel) and NEO
 * SuperAgent (chat button). See README.md in this folder.
 *
 * In Chromium at 1440 and 390 px, by keyboard and pointer: all three in one
 * corner as one settings button with a menu (menu button pattern: aria, arrow
 * keys, Home/End, Esc, Tab, focus return, each entry opens its panel, the
 * number of settings that are on, no movement with reduced motion), stacked one above the other and side by
 * side (alignment, gaps, buttons under an open panel step back), the right
 * corners, two corners, each extension alone, a button in a top corner, an
 * extension whose template has no dock attributes (keeps its own place) and a
 * page without the dock script (every button at its own place, working); axe
 * (WCAG 2.2 A/AA) on the buttons, the menu and the panels.
 *
 *   node tests/e2e/dock.e2e.cjs <axe.min.js> <screenshot-dir>
 *
 * Environment: DOCK_PAGE (default http://127.0.0.1:8000/de/dock-test),
 * DOCK_SWITCH (a command that sets up the test installation: it is called
 * with the settings of a scenario, e.g. "set arrangement=menu cc=left
 * a11y=bottom-left chat=left"), DOCK_SCENARIOS (comma separated; default
 * all), DOCK_PLAYWRIGHT (name of the Playwright package), DOCK_CHROME (path
 * of a Chrome or Chromium binary).
 */
'use strict';

const { execSync } = require('child_process');
const fs = require('fs');
const path = require('path');
const { chromium } = require(process.env.DOCK_PLAYWRIGHT || 'playwright');

const PAGE = process.env.DOCK_PAGE || 'http://127.0.0.1:8000/de/dock-test';
const AXE = fs.readFileSync(process.argv[2], 'utf8');
const SHOTS = process.argv[3];
const SWITCH = process.env.DOCK_SWITCH || '';
const ONLY = (process.env.DOCK_SCENARIOS || '').split(',').filter(Boolean);
const WIDTHS = [{ name: 'desktop', width: 1440, height: 900 }, { name: 'phone', width: 390, height: 844 }];

// The buttons of the three extensions and their panels.
const PARTS = {
  assistant: { frame: '[data-neo-dock="assistant"]', launcher: '[data-nsa-toggle]', panel: '[data-nsa-panel]', label: 'Nova — Chat öffnen' },
  accessibility: { frame: '[data-neo-dock="accessibility"]', launcher: '[data-nax-toggle]', panel: '[data-nax-panel]', label: 'Barrierefreiheit' },
  cookies: { frame: '[data-neo-dock="cookies"]', launcher: '[data-ncc-reopen]', panel: '[data-ncc]', label: 'Cookie-Einstellungen' },
};
const GAP = 12;

fs.mkdirSync(SHOTS, { recursive: true });

let passes = 0;
let failures = 0;
function check(label, ok, detail) {
  if (ok) passes++; else failures++;
  console.log((ok ? 'PASS ' : 'FAIL ') + label + (!ok && detail !== undefined ? '  -> ' + JSON.stringify(detail) : ''));
}

function scenario(name, settings) {
  if (ONLY.length && ONLY.indexOf(name) === -1) {
    return false;
  }

  if (SWITCH) {
    execSync(SWITCH + ' set ' + settings, { stdio: 'pipe' });
  }

  console.log('-- ' + name + ' (' + settings + ')');

  return true;
}

async function box(page, sel) {
  return page.evaluate((s) => {
    const el = document.querySelector(s);
    if (!el) return null;
    const r = el.getBoundingClientRect();
    const style = getComputedStyle(el);
    return {
      left: Math.round(r.left), top: Math.round(r.top), right: Math.round(r.right), bottom: Math.round(r.bottom),
      width: Math.round(r.width), height: Math.round(r.height),
      shown: r.width > 0 && r.height > 0 && style.visibility !== 'hidden' && style.display !== 'none',
      vw: document.documentElement.clientWidth, vh: document.documentElement.clientHeight,
    };
  }, sel);
}

async function shown(page, sel) {
  const b = await box(page, sel);
  return !!b && b.shown;
}

async function active(page) {
  return page.evaluate(() => {
    const el = document.activeElement;
    if (!el || el === document.body) return '';
    return (el.getAttribute('aria-label') || el.textContent || el.tagName).trim().replace(/\s+/g, ' ');
  });
}

async function activeIn(page, sel) {
  return page.evaluate((s) => { const el = document.querySelector(s); return !!el && el.contains(document.activeElement); }, sel);
}

async function axe(page, label) {
  await page.addScriptTag({ content: AXE });
  const result = await page.evaluate(async () => {
    const include = ['[data-neo-dock-root]', '[data-neo-dock]'].filter((s) => document.querySelector(s));
    const r = await window.axe.run({ include: include.map((s) => [s]) }, { runOnly: { type: 'tag', values: ['wcag2a', 'wcag2aa', 'wcag21a', 'wcag21aa', 'wcag22aa'] } });
    return r.violations.map((v) => v.id + ': ' + v.nodes.map((n) => n.target.join(' ')).join(', '));
  });
  check('axe ' + label, result.length === 0, result);
}

// A new visitor: the cookie choice is made ("Necessary only"), so the button to reopen shows.
async function open(browser, size, options) {
  const context = await browser.newContext({ viewport: { width: size.width, height: size.height } });
  const page = await context.newPage();
  const errors = [];
  page.on('pageerror', (e) => errors.push(e.message));

  if (options && options.route) {
    await page.route('**/*', options.route);
  }

  await page.goto(PAGE);

  if (await shown(page, '[data-ncc-action="necessary"]')) {
    await page.click('[data-ncc-action="necessary"]');
  }

  await page.waitForTimeout(250);
  page.errors = errors;

  return { context, page };
}

async function tabTo(page, sel, max) {
  for (let i = 0; i < (max || 60); i++) {
    await page.keyboard.press('Tab');
    if (await page.evaluate((s) => document.activeElement && document.activeElement.matches(s), sel)) {
      return true;
    }
  }
  return false;
}

async function entries(page, corner) {
  return page.evaluate((c) => [...document.querySelectorAll('[data-neo-dock-menu="' + c + '"] [role="menuitem"]')].map((el) => el.querySelector('.neo-dock__label').textContent), corner);
}

function gear(corner) {
  return '[data-neo-dock-button="' + corner + '"]';
}

function menu(corner) {
  return '[data-neo-dock-menu="' + corner + '"]';
}

// ---------------------------------------------------------------------------
// One settings button with a menu
// ---------------------------------------------------------------------------

async function menuScenario(browser, size, corner, names, tag) {
  const { context, page } = await open(browser, size);
  const g = await box(page, gear(corner));
  const right = corner.indexOf('right') > -1;
  const label = tag + ' ' + size.name;

  check(label + ': settings button shown in the corner', g && g.shown && Math.abs((right ? g.vw - g.right : g.left) - 16) <= 1 && Math.abs(g.vh - g.bottom - 16) <= 1, g);
  check(label + ': settings button 44 x 44', g && g.width >= 44 && g.height >= 44, g);

  for (const name of names) {
    check(label + ': own button of ' + name + ' hidden', !(await shown(page, PARTS[name].launcher)));
    check(label + ': ' + name + ' marked for the menu', await page.getAttribute(PARTS[name].frame, 'data-neo-dock-place') === 'menu');
  }

  const attrs = await page.evaluate((s) => { const b = document.querySelector(s); return { popup: b.getAttribute('aria-haspopup'), expanded: b.getAttribute('aria-expanded'), controls: b.getAttribute('aria-controls'), name: b.getAttribute('aria-label') }; }, gear(corner));
  check(label + ': menu button pattern', attrs.popup === 'menu' && attrs.expanded === 'false' && attrs.controls === 'neo-dock-' + corner + '-menu' && attrs.name === 'Einstellungen', attrs);
  check(label + ': entries in their order', JSON.stringify(await entries(page, corner)) === JSON.stringify(names.map((n) => PARTS[n].label)), await entries(page, corner));

  // Keyboard only.
  check(label + ': Tab reaches the settings button', await tabTo(page, gear(corner)));
  await page.keyboard.press('Enter');
  await page.waitForTimeout(250);
  check(label + ': Enter opens the menu on its first entry', await shown(page, menu(corner)) && await active(page) === PARTS[names[0]].label && await page.getAttribute(gear(corner), 'aria-expanded') === 'true', await active(page));
  const items = await page.evaluate((s) => [...document.querySelectorAll(s + ' [role="menuitem"]')].map((el) => Math.round(el.getBoundingClientRect().height)), menu(corner));
  check(label + ': entries at least 44 px high', items.every((h) => h >= 44), items);
  const m = await box(page, menu(corner));
  const g2 = await box(page, gear(corner));
  check(label + ': menu above the button, on the screen', m.bottom <= g2.top - 8 && m.top >= 0 && m.left >= 0 && m.right <= m.vw, { m, g2 });
  await page.screenshot({ path: path.join(SHOTS, tag + '-menu-' + size.name + '.png') });
  await axe(page, label + ' (menu open)');

  await page.keyboard.press('ArrowDown');
  check(label + ': Arrow down moves on', await active(page) === PARTS[names[1]].label, await active(page));
  await page.keyboard.press('End');
  check(label + ': End goes to the last entry', await active(page) === PARTS[names[names.length - 1]].label, await active(page));
  await page.keyboard.press('ArrowDown');
  check(label + ': Arrow down from the last goes round to the first', await active(page) === PARTS[names[0]].label, await active(page));
  await page.keyboard.press('ArrowUp');
  check(label + ': Arrow up from the first goes round to the last', await active(page) === PARTS[names[names.length - 1]].label, await active(page));
  await page.keyboard.press('Home');
  check(label + ': Home goes to the first entry', await active(page) === PARTS[names[0]].label, await active(page));
  await page.keyboard.press('Escape');
  check(label + ': Esc closes the menu, the focus is on the settings button', !(await shown(page, menu(corner))) && await page.evaluate((s) => document.activeElement === document.querySelector(s), gear(corner)) && await page.getAttribute(gear(corner), 'aria-expanded') === 'false');
  await page.keyboard.press('ArrowUp');
  check(label + ': Arrow up on the button opens the menu on its last entry', await active(page) === PARTS[names[names.length - 1]].label, await active(page));
  await page.keyboard.press('Tab');
  check(label + ': Tab closes the menu', !(await shown(page, menu(corner))));

  // Each entry opens its panel next to the settings button; the focus comes back to the button.
  for (const name of names) {
    await page.focus(gear(corner));
    await page.keyboard.press('Enter');
    for (let i = 0; i < names.indexOf(name); i++) {
      await page.keyboard.press('ArrowDown');
    }
    await page.keyboard.press('Enter');
    await page.waitForTimeout(300);

    const p = PARTS[name];
    check(label + ': the entry ' + name + ' opens its panel', await shown(page, p.panel) && !(await shown(page, menu(corner))));
    check(label + ': the focus is in the panel of ' + name, await activeIn(page, name === 'cookies' ? '[data-ncc]' : p.frame), await active(page));

    if (name !== 'cookies') {
      const panel = await box(page, p.panel);
      const g3 = await box(page, gear(corner));
      check(label + ': panel of ' + name + ' above the settings button, on the screen', panel.bottom <= g3.top && panel.top >= 0 && panel.left >= 0 && panel.right <= panel.vw, { panel, g3 });
      await page.screenshot({ path: path.join(SHOTS, tag + '-' + name + '-' + size.name + '.png') });
      await axe(page, label + ' (' + name + ' open)');
      await page.keyboard.press('Escape');
    } else {
      // The choice by keyboard ("Save choice" in the settings): the focus goes back to the settings button.
      await page.focus('[data-ncc-view="settings"] [data-ncc-action="save"]');
      await page.keyboard.press('Enter');
    }

    await page.waitForTimeout(300);
    check(label + ': ' + name + ' closed, the focus is back on the settings button', !(await shown(page, p.panel)) && await page.evaluate((s) => document.activeElement === document.querySelector(s), gear(corner)) && await shown(page, gear(corner)), await active(page));
  }

  // Number of settings that are on, at the entry Accessibility.
  if (names.indexOf('accessibility') > -1) {
    await page.click(gear(corner));
    await page.click(menu(corner) + ' [data-neo-dock-for="accessibility"]');
    await page.click('[data-nax-flag="contrast"]');
    await page.keyboard.press('Escape');
    await page.click(gear(corner));
    await page.waitForTimeout(250);
    const badge = await page.evaluate((s) => {
      const entry = document.querySelector(s + ' [data-neo-dock-for="accessibility"]');
      const said = entry && document.getElementById(entry.getAttribute('aria-describedby') || '-');
      return { shown: entry && entry.querySelector('.neo-dock__badge') ? entry.querySelector('.neo-dock__badge').textContent : '', said: said ? said.textContent : '' };
    }, menu(corner));
    check(label + ': the entry Accessibility shows the number of settings that are on', badge.shown === '1' && badge.said === 'Einstellungen zur Barrierefreiheit, 1 eingeschaltet', badge);
    await page.screenshot({ path: path.join(SHOTS, tag + '-badge-' + size.name + '.png') });

    // Pointer: a click outside closes the menu.
    await page.mouse.click(Math.round(size.width / 2), 40);
    check(label + ': a click outside closes the menu', !(await shown(page, menu(corner))));
  }

  // No movement: the system setting, and the switch of an accessibility panel.
  if (size.name === 'desktop') {
    const animation = () => page.evaluate((s) => getComputedStyle(document.querySelector(s)).animationName, menu(corner));
    await page.emulateMedia({ reducedMotion: 'reduce' });
    await page.click(gear(corner));
    check(label + ': the menu opens without movement with reduced motion', await animation() === 'none', await animation());
    await page.keyboard.press('Escape');
    await page.emulateMedia({ reducedMotion: 'no-preference' });
    await page.evaluate(() => document.documentElement.setAttribute('data-a11y-motion', ''));
    await page.click(gear(corner));
    check(label + ': the menu opens without movement with html[data-a11y-motion]', await animation() === 'none', await animation());
    await page.keyboard.press('Escape');
    await page.evaluate(() => document.documentElement.removeAttribute('data-a11y-motion'));
    await page.click(gear(corner));
    check(label + ': otherwise the menu fades in', await animation() !== 'none', await animation());
    await page.keyboard.press('Escape');
  }

  check(label + ': no script errors', page.errors.length === 0, page.errors);
  await context.close();
}

// ---------------------------------------------------------------------------
// Stacked one above the other or side by side
// ---------------------------------------------------------------------------

async function stackScenario(browser, size, corner, names, vertical, tag) {
  const { context, page } = await open(browser, size);
  const label = tag + ' ' + size.name;
  const right = corner.indexOf('right') > -1;
  const boxes = [];

  check(label + ': no settings button', !(await shown(page, gear(corner))));

  for (const name of names) {
    const b = await box(page, PARTS[name].launcher);
    boxes.push(b);
    check(label + ': button of ' + name + ' shown, on the screen', b && b.shown && b.left >= 0 && b.right <= b.vw && b.top >= 0, b);
  }

  for (let i = 1; i < boxes.length; i++) {
    const a = boxes[i - 1];
    const b = boxes[i];

    if (vertical) {
      check(label + ': ' + names[i] + ' above ' + names[i - 1] + ' with the gap', Math.abs(a.top - b.bottom - GAP) <= 1, { a, b });
      check(label + ': ' + names[i] + ' aligned at the edge', Math.abs(right ? a.right - b.right : a.left - b.left) <= 1, { a, b });
    } else {
      check(label + ': ' + names[i] + ' beside ' + names[i - 1] + ' with the gap', Math.abs(right ? a.left - b.right - GAP : b.left - a.right - GAP) <= 1, { a, b });
      check(label + ': ' + names[i] + ' aligned at the bottom', Math.abs(a.bottom - b.bottom) <= 1, { a, b });
    }
  }

  await page.screenshot({ path: path.join(SHOTS, tag + '-' + size.name + '.png') });
  await axe(page, label);

  // The chat button nearest the corner: its window opens over the buttons above it.
  await page.click(PARTS[names[0]].launcher);
  await page.waitForTimeout(300);
  const panel = await box(page, PARTS[names[0]].panel);
  check(label + ': the panel of ' + names[0] + ' opens, on the screen', panel && panel.shown && panel.top >= 0 && panel.left >= 0 && panel.right <= panel.vw, panel);

  for (const name of names.slice(1)) {
    const covered = vertical;
    check(label + ': button of ' + name + (covered ? ' steps back under the open panel' : ' stays beside it'), (await shown(page, PARTS[name].launcher)) === !covered);
  }

  await page.screenshot({ path: path.join(SHOTS, tag + '-open-' + size.name + '.png') });
  await page.keyboard.press('Escape');
  await page.waitForTimeout(200);
  check(label + ': closed, the focus is on its button', !(await shown(page, PARTS[names[0]].panel)) && await activeIn(page, PARTS[names[0]].frame), await active(page));

  for (const name of names.slice(1)) {
    check(label + ': button of ' + name + ' back', await shown(page, PARTS[name].launcher));
  }

  // The accessibility panel opens at its own button, on the screen.
  if (names.indexOf('accessibility') > -1) {
    await page.click(PARTS.accessibility.launcher);
    await page.waitForTimeout(300);
    const p = await box(page, PARTS.accessibility.panel);
    const t = await box(page, PARTS.accessibility.launcher);
    check(label + ': accessibility panel next to its button, on the screen', p.shown && p.top >= 0 && p.bottom <= p.vh && (p.bottom <= t.top || p.left >= t.right || p.right <= t.left), { p, t });
    await page.keyboard.press('Escape');
  }

  check(label + ': no script errors', page.errors.length === 0, page.errors);
  await context.close();
}

// ---------------------------------------------------------------------------
// Buttons at their own place (alone in a corner, or without the dock)
// ---------------------------------------------------------------------------

async function ownPlaces(browser, size, expected, tag, options) {
  const { context, page } = await open(browser, size, options);
  const label = tag + ' ' + size.name;

  for (const corner of ['bottom-left', 'bottom-right', 'top-left', 'top-right']) {
    if (!expected.menus || expected.menus.indexOf(corner) < 0) {
      check(label + ': no settings button ' + corner, !(await shown(page, gear(corner))));
    }
  }

  for (const [name, corner] of Object.entries(expected.own)) {
    const b = await box(page, PARTS[name].launcher);
    const right = corner.indexOf('right') > -1;
    const bottom = corner.indexOf('bottom') === 0;
    const okCorner = b && b.shown && (right ? b.left > b.vw / 2 : b.right < b.vw / 2) && (bottom ? b.top > b.vh / 2 : b.bottom < b.vh / 2);
    check(label + ': button of ' + name + ' at its own place ' + corner, okCorner, b);
    check(label + ': ' + name + ' not moved by the dock', await page.evaluate((s) => {
      const launcher = document.querySelector(s);
      const frame = launcher.closest('[data-neo-dock]') || launcher;
      return !frame.hasAttribute('data-neo-dock-place') && !frame.style.getPropertyValue('--neo-dock-y') && !frame.style.getPropertyValue('--neo-dock-x');
    }, PARTS[name].launcher));

    // Without the dock, buttons in one corner lie on top of each other: a click on the element itself.
    await page.$eval(PARTS[name].launcher, (el) => el.click());
    await page.waitForTimeout(300);
    check(label + ': its button opens the panel of ' + name, await shown(page, PARTS[name].panel));
    await page.keyboard.press('Escape');
    if (name === 'cookies' && await shown(page, '[data-ncc-action="necessary"]')) {
      await page.click('[data-ncc-action="necessary"]');
    }
    await page.waitForTimeout(200);
  }

  for (const corner of expected.menus || []) {
    check(label + ': settings button ' + corner + ' with ' + expected.entries[corner].join(', '), await shown(page, gear(corner)) && JSON.stringify(await entries(page, corner)) === JSON.stringify(expected.entries[corner].map((n) => PARTS[n].label)), await entries(page, corner));
  }

  await page.screenshot({ path: path.join(SHOTS, tag + '-' + size.name + '.png') });

  // Without the dock, two buttons in one corner overlap (axe: target-size): what the dock is for.
  if (!options || options.axe !== false) {
    await axe(page, label);
  }

  check(label + ': no script errors', page.errors.length === 0, page.errors);
  await context.close();
}

(async () => {
  const browser = await chromium.launch(process.env.DOCK_CHROME ? { executablePath: process.env.DOCK_CHROME } : {});
  const all = ['assistant', 'accessibility', 'cookies'];

  if (scenario('all-left-menu', 'arrangement=menu cc=left a11y=bottom-left chat=left')) {
    for (const size of WIDTHS) await menuScenario(browser, size, 'bottom-left', all, 'all-left-menu');
  }

  if (scenario('all-right-menu', 'arrangement=menu cc=right a11y=bottom-right chat=right')) {
    for (const size of WIDTHS) await menuScenario(browser, size, 'bottom-right', all, 'all-right-menu');
  }

  if (scenario('all-left-vertical', 'arrangement=vertical cc=left a11y=bottom-left chat=left')) {
    for (const size of WIDTHS) await stackScenario(browser, size, 'bottom-left', all, true, 'all-left-vertical');
  }

  if (scenario('all-right-vertical', 'arrangement=vertical cc=right a11y=bottom-right chat=right')) {
    for (const size of WIDTHS) await stackScenario(browser, size, 'bottom-right', all, true, 'all-right-vertical');
  }

  if (scenario('all-left-horizontal', 'arrangement=horizontal cc=left a11y=bottom-left chat=left')) {
    for (const size of WIDTHS) await stackScenario(browser, size, 'bottom-left', all, false, 'all-left-horizontal');
  }

  if (scenario('all-right-horizontal', 'arrangement=horizontal cc=right a11y=bottom-right chat=right')) {
    for (const size of WIDTHS) await stackScenario(browser, size, 'bottom-right', all, false, 'all-right-horizontal');
  }

  if (scenario('two-corners', 'arrangement=menu cc=left a11y=bottom-left chat=right')) {
    for (const size of WIDTHS) {
      await menuScenario(browser, size, 'bottom-left', ['accessibility', 'cookies'], 'two-corners');
      await ownPlaces(browser, size, { own: { assistant: 'bottom-right' }, menus: ['bottom-left'], entries: { 'bottom-left': ['accessibility', 'cookies'] } }, 'two-corners-chat');
    }
  }

  if (scenario('top-corner', 'arrangement=menu cc=left a11y=top-right chat=left')) {
    for (const size of WIDTHS) await ownPlaces(browser, size, { own: { accessibility: 'top-right' }, menus: ['bottom-left'], entries: { 'bottom-left': ['assistant', 'cookies'] } }, 'top-corner');
  }

  if (scenario('cookies-alone', 'arrangement=menu cc=left a11y=absent chat=absent')) {
    for (const size of WIDTHS) await ownPlaces(browser, size, { own: { cookies: 'bottom-left' } }, 'cookies-alone');
  }

  if (scenario('accessibility-alone', 'arrangement=menu cc=off a11y=bottom-left chat=absent')) {
    for (const size of WIDTHS) await ownPlaces(browser, size, { own: { accessibility: 'bottom-left' } }, 'accessibility-alone');
  }

  if (scenario('assistant-alone', 'arrangement=menu cc=off a11y=absent chat=left')) {
    for (const size of WIDTHS) await ownPlaces(browser, size, { own: { assistant: 'bottom-left' } }, 'assistant-alone');
  }

  // A copy of the chat template from before the dock: no data-neo-dock attributes.
  if (scenario('template-without-dock', 'arrangement=menu cc=left a11y=bottom-left chat=right')) {
    const route = async (route) => {
      if (route.request().resourceType() !== 'document') return route.continue();
      const response = await route.fetch();
      const html = (await response.text()).replace(/(<div class="nsa[^"]*" data-nsa[^>]*?) data-neo-dock="assistant"[^>]*>/, '$1>').replace(' data-nsa-toggle data-neo-dock-launcher', ' data-nsa-toggle');
      return route.fulfill({ response, body: html });
    };
    for (const size of WIDTHS) await ownPlaces(browser, size, { own: { assistant: 'bottom-right' }, menus: ['bottom-left'], entries: { 'bottom-left': ['accessibility', 'cookies'] } }, 'template-without-dock', { route });
  }

  // A Basic without the dock (the script stops at once): every button at its own place,
  // also where two share a corner (as before the dock).
  if (scenario('without-dock', 'arrangement=menu cc=left a11y=bottom-left chat=right')) {
    const route = async (route) => {
      if (route.request().resourceType() !== 'script' || route.request().url().indexOf('dock') < 0) return route.continue();
      const response = await route.fetch();
      return route.fulfill({ response, body: 'window.NeoDock={refresh:function(){}};' + (await response.text()) });
    };
    for (const size of WIDTHS) await ownPlaces(browser, size, { own: { cookies: 'bottom-left', accessibility: 'bottom-left', assistant: 'bottom-right' } }, 'without-dock', { route, axe: false });
  }

  await browser.close();
  console.log('\n' + passes + ' passed, ' + failures + ' failed');
  process.exit(failures ? 1 : 0);
})();
