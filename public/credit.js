/* =========================================================================
   Credit line of the free edition, shared by all extensions (Basic bundle).

   An extension calls NeoCredit.attach(container, options) for the element its
   credit line belongs to (e.g. the chat panel). The line is left out only when
   options.proof holds a certificate of the licence service that is valid for
   this extension and this host; the certificate is checked here with the
   service's public keys. Otherwise the line is drawn and kept visible:

   - in a closed shadow root, so page stylesheets do not reach its content;
   - on an element with a new random name on every page view, with display,
     visibility, size, position and effects set inline with !important, which
     no stylesheet overrides;
   - put back when a script removes, moves or changes it;
   - unreadable colours (custom properties) fall back to fixed ones;
   - when it is still not visible (cut off or covered), options.onBlock(true,
     message) asks the extension to stop taking input until it is visible.

   Text, logo and link are part of this file, not of a template or language file.
   No dependencies; plain browser JavaScript.
   ========================================================================= */
(function () {
  'use strict';

  if (window.NeoCredit) return;

  /* Public keys of the licence service: key id => SubjectPublicKeyInfo
     (base64), the same keys as src/License/PublicKeys.php. */
  var KEYS = {
    '2026-11-c6bc62': 'MFkwEwYHKoZIzj0CAQYIKoZIzj0DAQcDQgAEs/CgUyc2Jon/+PSd5pmnihVy9zQaoQ1hlVEDIU5TlEYrVZEdGizPwLINKbF0DJqvCmrL8g1ihBDzuKQJJBRbLg=='
  };

  // NEO wordmark, drawn in the text colour of the line.
  var LOGO =
    '<svg viewBox="0 0 449.49 143.2" width="28" height="9" aria-hidden="true" focusable="false"><g fill="currentColor">' +
    '<polygon points="21.72 66.74 28.64 71.98 28.64 143.2 0 143.2 0 50.29 21.72 66.74"/>' +
    '<polygon points="141.67 0 141.67 92.92 119.95 76.47 113.04 71.23 113.04 0 141.67 0"/>' +
    '<polygon points="141.67 107.29 141.67 143.2 141.66 143.2 113.04 121.52 28.64 57.61 0 35.92 0 0 .01 0 28.64 21.69 113.04 85.6 141.67 107.29"/>' +
    '<rect x="153.14" y="0" width="141.67" height="28.64"/><rect x="153.14" y="114.56" width="141.67" height="28.64"/><rect x="153.14" y="57.28" width="141.67" height="28.64"/>' +
    '<path d="M420.85,28.64v85.92h-85.92V28.64h85.92ZM449.49,0h-143.2v143.2h143.2V0h0Z"/></g></svg>';
  var LINE = '<a href="https://neo-digital.at" target="_blank" rel="nofollow noopener" aria-label="powered by NEO Digital">powered by ' + LOGO + '</a>';

  // Shown by the extension while the credit line is not visible.
  var STOP = {
    en: 'This feature is currently not available on this website.',
    de: 'Diese Funktion ist auf dieser Website gerade nicht verfügbar.',
    fr: 'Cette fonction n’est actuellement pas disponible sur ce site.',
    nl: 'Deze functie is op deze website momenteel niet beschikbaar.',
    it: 'Questa funzione al momento non è disponibile su questo sito.',
    pl: 'Ta funkcja jest obecnie niedostępna na tej stronie.',
    cs: 'Tato funkce není na tomto webu momentálně k dispozici.',
    hu: 'Ez a funkció jelenleg nem érhető el ezen a weboldalon.',
    sl: 'Ta funkcija na tem spletnem mestu trenutno ni na voljo.',
    hr: 'Ova funkcija trenutačno nije dostupna na ovoj web-stranici.',
    es: 'Esta función no está disponible en este sitio web en este momento.'
  };

  // Fixed colours when the themed ones are not readable (contrast 7.0:1).
  var FALLBACK = { ink: '#4f5761', bg: '#ffffff' };

  // Set inline with !important on the element that carries the line.
  var PINNED = {
    display: 'block', visibility: 'visible', opacity: '1', position: 'relative', 'z-index': '2147483647',
    top: 'auto', right: 'auto', bottom: 'auto', left: 'auto', float: 'none',
    width: 'auto', 'min-width': '0px', 'max-width': 'none', height: 'auto', 'min-height': '0px', 'max-height': 'none',
    margin: '0px', padding: '0px', overflow: 'visible', transform: 'none', translate: 'none', rotate: 'none', scale: 'none',
    zoom: '1', 'clip-path': 'none', mask: 'none', filter: 'none', 'mix-blend-mode': 'normal',
    'pointer-events': 'auto', 'content-visibility': 'visible', contain: 'none'
  };

  var CSS = ':host{all:initial}' +
    'a{display:flex;justify-content:center;align-items:center;gap:5px;margin:0;padding:6px 12px 8px;' +
    'border-top:1px solid var(--neo-credit-line,#d1d9e0);background:var(--neo-credit-bg,#ffffff);color:var(--neo-credit-ink,#4f5761);' +
    'font:500 11px/1.4 system-ui,-apple-system,"Segoe UI",Roboto,Arial,sans-serif;text-decoration:none;visibility:visible;opacity:1}' +
    'a:hover{text-decoration:underline}a:focus-visible{outline:2px solid currentColor;outline-offset:-3px}svg{display:block;flex:none;width:28px;height:9px}';

  function bytes(b64url) {
    var b64 = String(b64url).replace(/-/g, '+').replace(/_/g, '/');
    while (b64.length % 4) b64 += '=';
    var bin = atob(b64);
    var out = new Uint8Array(bin.length);
    for (var i = 0; i < bin.length; i++) out[i] = bin.charCodeAt(i);
    return out;
  }

  // ECDSA signature DER (SEQUENCE of two INTEGERs) -> 64 bytes r||s for WebCrypto.
  function derToRaw(der) {
    var pos = 2;
    if (der[0] !== 0x30) throw new Error('signature');
    if (der[1] & 0x80) pos = 2 + (der[1] & 0x7f);
    function integer() {
      if (der[pos++] !== 0x02) throw new Error('signature');
      var len = der[pos++];
      var value = der.slice(pos, pos + len);
      pos += len;
      while (value.length > 32 && value[0] === 0) value = value.slice(1);
      var padded = new Uint8Array(32);
      padded.set(value, 32 - value.length);
      return padded;
    }
    var raw = new Uint8Array(64);
    raw.set(integer(), 0);
    raw.set(integer(), 32);
    return raw;
  }

  function covers(domains, host) {
    host = String(host).toLowerCase().replace(/\.$/, '');
    return (domains || []).some(function (d) {
      d = String(d).toLowerCase();
      return d !== '' && (host === d || host.slice(-(d.length + 1)) === '.' + d);
    });
  }

  // True only for a genuine, current certificate of this extension for this host.
  function proofValid(proof, module) {
    var subtle = window.crypto && window.crypto.subtle;
    if (!proof || !module || !KEYS[proof.keyId] || !subtle || typeof TextDecoder === 'undefined') return Promise.resolve(false);
    if (!/\[native code\]/.test(Function.prototype.toString.call(subtle.verify))) return Promise.resolve(false);
    var payload, signature;
    try {
      payload = bytes(proof.payload);
      signature = derToRaw(bytes(proof.signature));
    } catch (e) {
      return Promise.resolve(false);
    }
    return subtle.importKey('spki', bytes(KEYS[proof.keyId]), { name: 'ECDSA', namedCurve: 'P-256' }, false, ['verify'])
      .then(function (key) { return subtle.verify({ name: 'ECDSA', hash: 'SHA-256' }, key, signature, payload); })
      .then(function (ok) {
        if (!ok) return false;
        var c = JSON.parse(new TextDecoder().decode(payload));
        return c.version === 1 && c.keyId === proof.keyId && c.module === module
          && c.status !== 'expired' && c.status !== 'revoked'
          && Date.parse(c.graceUntil) >= Date.now() && covers(c.domains, location.hostname);
      })
      .catch(function () { return false; });
  }

  // A valid custom element name that changes on every page view, e.g. "nkqzv-hwdte".
  function randomName() {
    var a = new Uint8Array(10);
    if (window.crypto && window.crypto.getRandomValues) {
      window.crypto.getRandomValues(a);
    } else {
      for (var j = 0; j < a.length; j++) a[j] = Math.floor(Math.random() * 256);
    }
    var s = '';
    for (var i = 0; i < a.length; i++) s += String.fromCharCode(97 + (a[i] % 26));
    return s.slice(0, 5) + '-' + s.slice(5);
  }

  function rgba(value) {
    var m = String(value).match(/rgba?\(([^)]+)\)/);
    if (!m) return null;
    var p = m[1].split(/[\s,\/]+/).filter(Boolean).map(parseFloat);
    return { r: p[0], g: p[1], b: p[2], a: p.length > 3 ? p[3] : 1 };
  }

  function luminance(c) {
    return [c.r, c.g, c.b].map(function (v) {
      v /= 255;
      return v <= 0.03928 ? v / 12.92 : Math.pow((v + 0.055) / 1.055, 2.4);
    }).reduce(function (sum, v, i) { return sum + v * [0.2126, 0.7152, 0.0722][i]; }, 0);
  }

  function readable(ink, bg) {
    if (!ink || !bg || ink.a < 1 || bg.a < 1) return false;
    var l1 = luminance(ink), l2 = luminance(bg);
    return (Math.max(l1, l2) + 0.05) / (Math.min(l1, l2) + 0.05) >= 4.5;
  }

  function stopText(lang) {
    lang = String(lang || document.documentElement.lang || 'en').toLowerCase().slice(0, 2);
    return STOP[lang] || STOP.en;
  }

  function draw(container, options) {
    var host = document.createElement(randomName());
    var link;

    if (host.attachShadow) {
      var root = host.attachShadow({ mode: 'closed' });
      root.innerHTML = '<style>' + CSS + '</style>' + LINE;
      link = root.querySelector('a');
    } else {
      host.innerHTML = LINE;
      link = host.firstChild;
    }

    function colours() {
      var s = getComputedStyle(link);
      if (!readable(rgba(s.color), rgba(s.backgroundColor))) {
        link.style.setProperty('color', FALLBACK.ink, 'important');
        link.style.setProperty('background', FALLBACK.bg, 'important');
      }
    }

    // Properties this browser does not know are skipped after the first try,
    // otherwise every pin would change the style attribute and pin again.
    var unknown = {};
    function pinned(p) {
      return host.style.getPropertyValue(p) === PINNED[p] && host.style.getPropertyPriority(p) === 'important';
    }
    function pin() {
      Object.keys(PINNED).forEach(function (p) {
        if (unknown[p] || pinned(p)) return;
        host.style.setProperty(p, PINNED[p], 'important');
        if (!pinned(p)) unknown[p] = true;
      });
      // Only the inline style belongs here: no class, id, hidden, inert or aria-hidden.
      for (var i = host.attributes.length - 1; i >= 0; i--) {
        if (host.attributes[i].name !== 'style') host.removeAttribute(host.attributes[i].name);
      }
      if (host.parentNode !== container || container.lastElementChild !== host) container.appendChild(host);
    }

    pin();
    colours();
    new MutationObserver(pin).observe(container, { childList: true });
    new MutationObserver(pin).observe(host, { attributes: true });

    // Is the line really there for the eye? Size, place on screen and what is on top.
    function visible() {
      pin();
      var r = host.getBoundingClientRect();
      if (r.width < 60 || r.height < 12) return false;
      var vh = window.innerHeight || document.documentElement.clientHeight;
      var vw = window.innerWidth || document.documentElement.clientWidth;
      if (r.top < 0 || r.bottom > vh || r.left < 0 || r.right > vw) {
        // Outside the window: wrong only when the container would fit into it.
        return container.getBoundingClientRect().height > vh;
      }
      // Top, middle and bottom edge, left, centre and right: cut off or covered in part counts too.
      return [r.top + 2, r.top + r.height / 2, r.bottom - 2].every(function (y) {
        return [0.25, 0.5, 0.75].every(function (f) {
          return document.elementFromPoint(r.left + r.width * f, y) === host;
        });
      });
    }

    var blocked = false;
    var misses = 0;
    function check() {
      // A container that is not shown (e.g. a closed panel) is not checked.
      if (!container.getClientRects().length) return;
      colours();
      if (visible()) {
        misses = 0;
        if (blocked) { blocked = false; options.onBlock && options.onBlock(false, ''); }
      } else if (++misses >= 2 && !blocked) {
        blocked = true;
        options.onBlock && options.onBlock(true, stopText(options.lang));
      }
    }
    setInterval(check, 1000);

    return { licensed: false, check: check, isBlocked: function () { return blocked; } };
  }

  /**
   * container: the element the line belongs to (it is added as its last child).
   * options: { module: licence code of the extension, proof: certificate or null,
   *            lang: language of the texts, onBlock: function (blocked, message) }
   * Resolves to { licensed, check(), isBlocked() }. A check that hangs counts as
   * failed: after 3 seconds the line is drawn.
   */
  function attach(container, options) {
    options = options || {};
    return new Promise(function (resolve) {
      var done = false;
      function settle(licensed) {
        if (done) return;
        done = true;
        resolve(licensed
          ? { licensed: true, check: function () {}, isBlocked: function () { return false; } }
          : draw(container, options));
      }
      proofValid(options.proof, options.module).then(settle);
      setTimeout(function () { settle(false); }, 3000);
    });
  }

  Object.defineProperty(window, 'NeoCredit', { value: Object.freeze({ attach: attach }), writable: false, configurable: false });
  document.dispatchEvent(new CustomEvent('neo:credit-ready'));
})();
