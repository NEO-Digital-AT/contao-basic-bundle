/*
 * Buttons at the edge of the screen (dock), shared by the extensions.
 *
 * Several extensions put a floating button into a corner of the screen: the
 * button to reopen the cookie settings, the button of the accessibility panel,
 * the button of an AI chat. Each extension keeps its button, its panel and its
 * own setting for the corner. This script arranges the buttons that share a
 * corner, as set in the page layout (<meta name="neo-dock" content="…">):
 *  - "menu" (default): one settings button (cogwheel) with a menu; an entry
 *    clicks the hidden button of its extension;
 *  - "vertical": the buttons stacked one above the other;
 *  - "horizontal": the buttons side by side.
 * A corner with a single button is left as it is.
 *
 * Interface (docs/en/developers.md, "Buttons at the edge of the screen"):
 *  data-neo-dock="<name>"     the element that sits in the corner (fixed). The
 *                             script moves it with --neo-dock-x/--neo-dock-y
 *                             (added by the extension's CSS to its distance from
 *                             the edges) and marks it with data-neo-dock-place
 *                             "menu" or "stack"; in a stack one above the other,
 *                             the buttons over an open panel are marked
 *                             data-neo-dock-covered (hidden until it closes).
 *  data-neo-dock-corner       bottom-left | bottom-right | top-left | top-right
 *  data-neo-dock-label        text of the entry in the menu
 *  data-neo-dock-order        number: lower sits nearer the corner and comes first in the menu
 *  data-neo-dock-launcher     the button that opens the panel (default: the element itself)
 *  data-neo-dock-icon         icon of the entry: an element, or a <template> holding it
 *  data-neo-dock-badge        short state next to the entry (e.g. a number), with
 *  data-neo-dock-badge-label  its wording for screen readers
 * A button takes part while neither it nor its element has the attribute hidden.
 * The order in which the scripts load does not matter: this script watches the page.
 *
 * Plain ES5, no dependencies. Loaded with "defer".
 */
(function () {
  'use strict';

  if (window.NeoDock && typeof window.NeoDock.refresh === 'function') {
    return;
  }

  var CORNERS = ['bottom-left', 'bottom-right', 'top-left', 'top-right'];
  var ARRANGEMENTS = ['menu', 'vertical', 'horizontal'];
  var FRAME = '[data-neo-dock]';
  var LAUNCHER = '[data-neo-dock-launcher]';
  var WATCHED = ['hidden', 'disabled', 'aria-expanded', 'data-neo-dock', 'data-neo-dock-corner', 'data-neo-dock-order',
    'data-neo-dock-label', 'data-neo-dock-badge', 'data-neo-dock-badge-label', 'data-neo-dock-launcher'];
  var GEAR = '<svg viewBox="0 0 24 24" width="22" height="22" aria-hidden="true" focusable="false">'
    + '<path d="M9.78 4.73 10.44 2.12 13.56 2.12 14.22 4.73 15.57 5.29 17.88 3.91 20.09 6.12 18.71 8.43 19.27 9.78 21.88 10.44 21.88 13.56 19.27 14.22 18.71 15.57 20.09 17.88 17.88 20.09 15.57 18.71 14.22 19.27 13.56 21.88 10.44 21.88 9.78 19.27 8.43 18.71 6.12 20.09 3.91 17.88 5.29 15.57 4.73 14.22 2.12 13.56 2.12 10.44 4.73 9.78 5.29 8.43 3.91 6.12 6.12 3.91 8.43 5.29z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>'
    + '<circle cx="12" cy="12" r="3" fill="none" stroke="currentColor" stroke-width="1.8"/></svg>';

  var meta = document.querySelector('meta[name="neo-dock"]');
  var arrangement = meta && ARRANGEMENTS.indexOf(meta.getAttribute('content')) > -1 ? meta.getAttribute('content') : 'menu';
  var label = (meta && meta.getAttribute('data-neo-dock-label')) || 'Settings';

  var host = null;
  var parts = {};
  var touched = [];
  var scheduled = false;
  var sized = null;
  var sizedElements = [];
  var uid = 0;

  function toArray(list) {
    return Array.prototype.slice.call(list || []);
  }

  function setAttr(el, name, value) {
    if (value === null) {
      if (el.hasAttribute(name)) {
        el.removeAttribute(name);
      }
    } else if (el.getAttribute(name) !== value) {
      el.setAttribute(name, value);
    }
  }

  function setVar(el, name, value) {
    if (value === null) {
      el.style.removeProperty(name);
    } else if (el.style.getPropertyValue(name) !== value) {
      el.style.setProperty(name, value);
    }
  }

  // ---------------------------------------------------------------------------
  // Buttons of the extensions
  // ---------------------------------------------------------------------------

  function launcherOf(frame) {
    return frame.hasAttribute('data-neo-dock-launcher') ? frame : frame.querySelector(LAUNCHER) || frame;
  }

  function cornerOf(frame) {
    var value = frame.getAttribute('data-neo-dock-corner');

    return CORNERS.indexOf(value) > -1 ? value : 'bottom-right';
  }

  function orderOf(frame) {
    var value = parseFloat(frame.getAttribute('data-neo-dock-order'));

    return isFinite(value) ? value : 50;
  }

  function labelOf(frame) {
    var launcher = launcherOf(frame);

    return frame.getAttribute('data-neo-dock-label') || launcher.getAttribute('aria-label') || (launcher.textContent || '').trim();
  }

  // Every button on the page, with its corner; the ones the extension hides
  // (attribute hidden) or disables take no part.
  function items() {
    return toArray(document.querySelectorAll(FRAME)).filter(function (frame) {
      return !host || !host.contains(frame);
    }).map(function (frame, index) {
      var launcher = launcherOf(frame);

      return {
        frame: frame,
        launcher: launcher,
        corner: cornerOf(frame),
        order: orderOf(frame),
        index: index,
        active: !launcher.closest('[hidden]') && !launcher.disabled
      };
    });
  }

  // ---------------------------------------------------------------------------
  // Settings button and menu of a corner
  // ---------------------------------------------------------------------------

  function ensureHost() {
    if (!host) {
      host = document.createElement('div');
      host.className = 'neo-dock';
      host.setAttribute('data-neo-dock-root', '');
      document.body.appendChild(host);
    }

    if (host.parentNode !== document.body) {
      document.body.appendChild(host);
    }
  }

  function partsOf(name) {
    if (parts[name]) {
      return parts[name];
    }

    ensureHost();

    var id = 'neo-dock-' + name;
    var button = document.createElement('button');
    var menu = document.createElement('div');

    button.type = 'button';
    button.className = 'neo-dock__button neo-dock__button--' + name;
    button.id = id;
    button.setAttribute('data-neo-dock-button', name);
    button.setAttribute('aria-haspopup', 'menu');
    button.setAttribute('aria-expanded', 'false');
    button.setAttribute('aria-controls', id + '-menu');
    button.setAttribute('aria-label', label);
    button.title = label;
    button.innerHTML = GEAR;
    button.hidden = true;

    menu.className = 'neo-dock__menu neo-dock__menu--' + name;
    menu.id = id + '-menu';
    menu.setAttribute('role', 'menu');
    menu.setAttribute('aria-labelledby', id);
    menu.setAttribute('data-neo-dock-menu', name);
    menu.hidden = true;

    host.appendChild(button);
    host.appendChild(menu);

    parts[name] = { name: name, button: button, menu: menu, key: '' };
    bindMenu(parts[name]);

    return parts[name];
  }

  function entries(part) {
    return toArray(part.menu.querySelectorAll('[role="menuitem"]')).filter(function (el) {
      return !el.hidden;
    });
  }

  function isOpen(part) {
    return !part.menu.hidden;
  }

  function openMenu(part, last) {
    var list = entries(part);

    if (!list.length) {
      return;
    }

    part.menu.hidden = false;
    part.button.setAttribute('aria-expanded', 'true');
    list[last ? list.length - 1 : 0].focus();
  }

  function closeMenu(part, returnFocus) {
    if (!isOpen(part)) {
      return;
    }

    part.menu.hidden = true;
    part.button.setAttribute('aria-expanded', 'false');

    if (returnFocus && !part.button.hidden) {
      part.button.focus({ preventScroll: true });
    }
  }

  // An entry opens the extension's panel by clicking its (hidden) button. The
  // focus is on the settings button first, so the extension, which gives the
  // focus back to where it came from, returns it there when the panel closes.
  function activate(part, frame) {
    closeMenu(part, true);
    launcherOf(frame).click();
  }

  function bindMenu(part) {
    part.button.addEventListener('click', function () {
      if (isOpen(part)) {
        closeMenu(part, true);
      } else {
        openMenu(part, false);
      }
    });

    part.button.addEventListener('keydown', function (e) {
      if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
        e.preventDefault();
        openMenu(part, e.key === 'ArrowUp');
      }
    });

    part.menu.addEventListener('keydown', function (e) {
      var list = entries(part);
      var at = list.indexOf(document.activeElement);

      if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
        e.preventDefault();
        if (list.length) {
          list[(at + (e.key === 'ArrowDown' ? 1 : list.length - 1)) % list.length].focus();
        }
      } else if (e.key === 'Home' || e.key === 'End') {
        e.preventDefault();
        if (list.length) {
          list[e.key === 'Home' ? 0 : list.length - 1].focus();
        }
      } else if (e.key === 'Escape') {
        e.preventDefault();
        e.stopPropagation();
        closeMenu(part, true);
      } else if (e.key === 'Tab') {
        closeMenu(part, false);
      }
    });

    part.menu.addEventListener('click', function (e) {
      var entry = e.target.closest ? e.target.closest('[data-neo-dock-for]') : null;
      var frame = entry ? entry.neoDockFrame : null;

      if (frame) {
        activate(part, frame);
      }
    });

    part.menu.addEventListener('focusout', function (e) {
      var next = e.relatedTarget;

      if (next && !part.menu.contains(next) && next !== part.button) {
        closeMenu(part, false);
      }
    });

    document.addEventListener('pointerdown', function (e) {
      if (isOpen(part) && !part.menu.contains(e.target) && !part.button.contains(e.target)) {
        closeMenu(part, false);
      }
    }, true);
  }

  // The icon of an entry: a copy of the element marked data-neo-dock-icon (or
  // of the content of such a <template>).
  function iconOf(frame) {
    var source = frame.querySelector('[data-neo-dock-icon]');
    var icon = null;

    if (!source) {
      return null;
    }

    if (source.tagName === 'TEMPLATE' && source.content) {
      icon = source.content.firstElementChild ? source.content.firstElementChild.cloneNode(true) : null;
    } else {
      icon = source.cloneNode(true);
    }

    if (icon) {
      icon.removeAttribute('data-neo-dock-icon');
      icon.removeAttribute('hidden');
      icon.removeAttribute('id');
      toArray(icon.querySelectorAll('[id]')).forEach(function (el) { el.removeAttribute('id'); });
      icon.setAttribute('aria-hidden', 'true');

      if (icon.tagName.toLowerCase() === 'svg') {
        icon.setAttribute('focusable', 'false');
      }
    }

    return icon;
  }

  function entryKey(list) {
    return list.map(function (item) {
      var f = item.frame;

      return [f.getAttribute('data-neo-dock'), labelOf(f), f.getAttribute('data-neo-dock-badge') || '', f.getAttribute('data-neo-dock-badge-label') || ''].join('\u0001');
    }).join('\u0002');
  }

  function fillMenu(part, list) {
    var key = entryKey(list);

    if (key === part.key && part.frames && part.frames.length === list.length && list.every(function (item, i) { return part.frames[i] === item.frame; })) {
      return;
    }

    var focused = entries(part).indexOf(document.activeElement);

    part.key = key;
    part.frames = list.map(function (item) { return item.frame; });
    part.menu.innerHTML = '';

    list.forEach(function (item) {
      var frame = item.frame;
      var entry = document.createElement('button');
      var icon = iconOf(frame);
      var text = document.createElement('span');
      var badge = frame.getAttribute('data-neo-dock-badge');

      entry.type = 'button';
      entry.className = 'neo-dock__item';
      entry.setAttribute('role', 'menuitem');
      entry.tabIndex = -1;
      entry.setAttribute('data-neo-dock-for', frame.getAttribute('data-neo-dock') || '');
      entry.neoDockFrame = frame;

      if (icon) {
        var box = document.createElement('span');

        box.className = 'neo-dock__icon';
        box.setAttribute('aria-hidden', 'true');
        box.appendChild(icon);
        entry.appendChild(box);
      }

      text.className = 'neo-dock__label';
      text.textContent = labelOf(frame);
      entry.appendChild(text);

      if (badge) {
        var shown = document.createElement('span');
        var said = document.createElement('span');

        shown.className = 'neo-dock__badge';
        shown.setAttribute('aria-hidden', 'true');
        shown.textContent = badge;
        entry.appendChild(shown);

        said.id = 'neo-dock-note-' + (++uid);
        said.hidden = true;
        said.textContent = frame.getAttribute('data-neo-dock-badge-label') || badge;
        entry.appendChild(said);
        entry.setAttribute('aria-describedby', said.id);
      }

      part.menu.appendChild(entry);
    });

    if (focused > -1 && isOpen(part)) {
      var list2 = entries(part);

      if (list2.length) {
        list2[Math.min(focused, list2.length - 1)].focus();
      }
    }
  }

  // ---------------------------------------------------------------------------
  // Arrangement
  // ---------------------------------------------------------------------------

  function viewport() {
    var root = document.documentElement;

    return { width: root.clientWidth || window.innerWidth, height: root.clientHeight || window.innerHeight };
  }

  // Distances of a box from the two edges of its corner, in pixels on the screen.
  function distances(rect, corner, view) {
    return {
      v: corner.indexOf('bottom') === 0 ? view.height - rect.bottom : rect.top,
      h: corner.indexOf('left') > -1 ? rect.left : view.width - rect.right
    };
  }

  // Pixels on the screen to CSS pixels of the element (CSS zoom of the page).
  function zoomOf(el) {
    var zoom = el.currentCSSZoom;

    return typeof zoom === 'number' && zoom > 0 ? zoom : 1;
  }

  // Gap between stacked buttons: --neo-dock-gap, read as pixels from the
  // container (row-gap computes lengths in any unit to pixels).
  function gap() {
    var value = host ? parseFloat(window.getComputedStyle(host).rowGap) : NaN;

    return isFinite(value) ? value : 12;
  }

  function move(frame, x, y) {
    var zoom = zoomOf(frame);

    setVar(frame, '--neo-dock-x', x === null ? null : Math.round(x / zoom * 100) / 100 + 'px');
    setVar(frame, '--neo-dock-y', y === null ? null : Math.round(y / zoom * 100) / 100 + 'px');
  }

  function release(frame) {
    move(frame, null, null);
    setAttr(frame, 'data-neo-dock-place', null);
    setAttr(frame, 'data-neo-dock-covered', null);
  }

  function stack(list, corner, view) {
    var space = gap();
    var vertical = arrangement === 'vertical';
    var rects = list.map(function (item) { return item.launcher.getBoundingClientRect(); });
    var first = distances(rects[0], corner, view);
    var along = 0;
    var open = -1;

    list.forEach(function (item, i) {
      var rect = rects[i];
      var at = distances(rect, corner, view);
      var target = { v: first.v, h: first.h };

      if (i > 0) {
        if (vertical) {
          target.v = along;
        } else {
          target.h = along;
        }
      }

      along = (vertical ? target.v + rect.height : target.h + rect.width) + space;
      move(item.frame, target.h - at.h, target.v - at.v);
      setAttr(item.frame, 'data-neo-dock-place', 'stack');

      if (open < 0 && item.launcher.getAttribute('aria-expanded') === 'true') {
        open = i;
      }
    });

    // One above the other, a panel opens over the buttons further up: they
    // step back (data-neo-dock-covered hides them) until it closes, so none
    // of them shows through it or on top of it.
    list.forEach(function (item, i) {
      setAttr(item.frame, 'data-neo-dock-covered', vertical && open > -1 && i > open ? '' : null);
    });
  }

  // In the menu, an element sits on the settings button as if it were its own
  // button: its edge towards the corner at the far edge of the settings button,
  // so its panel opens next to that button.
  function dockToMenu(list, corner, part, view) {
    var button = part.button.getBoundingClientRect();
    var at = distances(button, corner, view);

    list.forEach(function (item) {
      setAttr(item.frame, 'data-neo-dock-covered', null);

      if (item.launcher === item.frame) {
        move(item.frame, null, null);

        return;
      }

      var own = distances(item.frame.getBoundingClientRect(), corner, view);

      move(item.frame, at.h - own.h, at.v + button.height - own.v);
    });
  }

  function layout() {
    scheduled = false;

    if (!document.body) {
      return;
    }

    var all = items();
    var groups = {};
    var view = viewport();

    CORNERS.forEach(function (name) { groups[name] = []; });

    all.forEach(function (item) {
      if (item.active) {
        groups[item.corner].push(item);
      }
    });

    // Back to the place the extension gives them, then measure from there.
    touched.forEach(function (frame) {
      if (document.body.contains(frame)) {
        move(frame, 0, 0);
      }
    });

    all.forEach(function (item) {
      var group = groups[item.corner];
      var place = item.active && group.length > 1 ? (arrangement === 'menu' ? 'menu' : 'stack') : null;

      if (!place) {
        release(item.frame);
      } else {
        setAttr(item.frame, 'data-neo-dock-place', place);

        if (touched.indexOf(item.frame) < 0) {
          touched.push(item.frame);
        }
      }
    });

    touched = touched.filter(function (frame) {
      return document.body.contains(frame) && frame.hasAttribute('data-neo-dock-place');
    });

    CORNERS.forEach(function (name) {
      var group = groups[name].sort(function (a, b) {
        return a.order - b.order || a.index - b.index;
      });
      var part = group.length > 1 || parts[name] ? partsOf(name) : null;

      if (!part) {
        return;
      }

      if (group.length < 2) {
        closeMenu(part, part.menu.contains(document.activeElement));
        part.button.hidden = true;

        return;
      }

      if (arrangement === 'menu') {
        fillMenu(part, group);
        part.button.hidden = false;
        dockToMenu(group, name, part, view);
      } else {
        closeMenu(part, false);
        part.button.hidden = true;
        stack(group, name, view);
      }
    });

    watchSizes(all);
  }

  function schedule() {
    if (scheduled) {
      return;
    }

    scheduled = true;

    // As a microtask: the arrangement is done before the browser paints, so a
    // button never shows at a place it is about to leave.
    if (window.Promise) {
      Promise.resolve().then(layout);
    } else {
      setTimeout(layout, 0);
    }
  }

  // ---------------------------------------------------------------------------
  // Watching the page
  // ---------------------------------------------------------------------------

  function watchSizes(all) {
    if (!sized) {
      return;
    }

    all.forEach(function (item) {
      if (sizedElements.indexOf(item.launcher) < 0) {
        sizedElements.push(item.launcher);
        sized.observe(item.launcher);
      }
    });

    Object.keys(parts).forEach(function (name) {
      if (sizedElements.indexOf(parts[name].button) < 0) {
        sizedElements.push(parts[name].button);
        sized.observe(parts[name].button);
      }
    });
  }

  function concerns(node) {
    return node.nodeType === 1 && (node.matches(FRAME) || node.matches(LAUNCHER) || !!node.querySelector(FRAME));
  }

  function relevant(records) {
    return records.some(function (record) {
      if (host && host.contains(record.target)) {
        return false;
      }

      if (record.type === 'childList') {
        return toArray(record.addedNodes).concat(toArray(record.removedNodes)).some(concerns);
      }

      return record.target === document.documentElement || concerns(record.target);
    });
  }

  function start() {
    if (window.MutationObserver) {
      new MutationObserver(function (records) {
        if (relevant(records)) {
          schedule();
        }
      }).observe(document.documentElement, { childList: true, subtree: true, attributes: true, attributeFilter: WATCHED });

      // Attributes of <html>: settings of an accessibility tool (text size, zoom) change sizes.
      new MutationObserver(schedule).observe(document.documentElement, { attributes: true });
    }

    if (window.ResizeObserver) {
      sized = new ResizeObserver(schedule);
    }

    window.addEventListener('resize', schedule);

    if (document.fonts && document.fonts.ready) {
      document.fonts.ready.then(schedule);
    }

    layout();
  }

  window.NeoDock = {
    version: 1,
    arrangement: arrangement,
    // Arranges the buttons again, for changes the script cannot see (e.g. a
    // stylesheet that changes the size of a button).
    refresh: layout
  };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', start);
  } else {
    start();
  }
})();
