---
title: Customising
summary: The credit line of the free editions on the website, the addresses of the licence service and of DeepL for test installations, and the texts of the back end.
audience: developers
language: en
updated: 2026-11-12
---

# Customising

*Access keys and licences* uses Contao's own back-end forms and styles; there
is nothing to style in the back end. A small script of Basic
(`bundles/neobasic/access.js`, loaded only on this page) runs **Test
connection** and **Check now** in place. On the website, Basic shows one thing: the credit
line of the free editions.

## The credit line of the free editions

An extension without a valid licence shows a short credit line on the website,
the same for every extension of the family. The shared script
`bundles/neobasic/credit.js` of Basic draws it; the extension decides where (as
the last part of its own element, e.g. a panel). Text, logo and link are part
of the script, not of a template or a language file, and cannot be changed.

### When it is left out

Only when the signed confirmation of the licence service is valid for the
website. This is checked on the server and again in the visitor's browser,
with the public keys built into Basic. The check in the browser needs HTTPS
(the Web Crypto interface of the browser exists only there): on a page served
over plain HTTP the credit line stays, even with a licence.

### How it stays visible

The credit line sits in a closed shadow root on an element with a new name on
every page view. Its visibility is set inline, so stylesheets cannot hide it,
and it is put back when a script removes or changes it.

When it is cut off or covered, the extension takes no input until the line is
visible again, and shows "This feature is currently not available on this
website." in the language of the page. The line is checked once a second; a
closed element of the extension (e.g. a closed panel) is not checked.

What to avoid in the layout of the website:

- a height limit or `overflow: hidden` on the element of the extension that
  cuts off its last line,
- elements placed on top of it (fixed bars, cookie banners, overlays),
- moving the element so far that its end leaves the window although it would
  fit.

### Colours

The credit line takes its colours from three CSS custom properties. Set them on
the element of the extension or higher up, for example on `:root`:

```css
:root {
    --neo-credit-ink: #1f2933;
    --neo-credit-bg: #f5f7fa;
    --neo-credit-line: #d1d9e0;
}
```

| Property | What it sets | Default |
| --- | --- | --- |
| `--neo-credit-ink` | text and logo | `#4f5761` |
| `--neo-credit-bg` | background | `#ffffff` |
| `--neo-credit-line` | line above the text | `#d1d9e0` |

When text and background are less than 4.5:1 apart, or one of them is
transparent, the line uses the fixed colours `#4f5761` on `#ffffff`.

## Address of the licence service (test installations)

The address of the licence service can be changed for a test installation:

```yaml
# config/config.yaml
neo_basic:
    license_url: 'https://licence-test.example.com'
```

Confirmations are still checked against the public keys built into Basic.

## Address of DeepL (test installations)

Without a setting, the requests to DeepL go to `https://api-free.deepl.com`
for a key of the free plan (ending in `:fx`) and to `https://api.deepl.com`
for all others. A test installation can send them to a local stand-in instead:

```yaml
# config/config.yaml
neo_basic:
    deepl_url: 'http://127.0.0.1:8186'
```

The address is given without `/v2`; the status line of the section **DeepL**
shows the address in use.

## Texts of the back end

The texts of the page are Contao language files in `contao/languages/<language>/`
of Basic (`modules.php`, `tl_neo_access.php`, `neo_license.php`), in eleven
languages. Override single texts like any Contao language file, in
`contao/languages/<language>/` of the project.
