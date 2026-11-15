---
title: For developers
summary: How an extension builds on Basic - adding sections to "Access keys and licences", reading keys, translating with Azure Translator, DeepL or an AI model, checking its licence and terms, showing the credit line, and the breadcrumb of the menu group.
audience: developers
language: en
updated: 2026-11-14
---

# For developers

An extension that builds on Basic requires `neo-digital/contao-basic-bundle`
in its `composer.json`. The public methods of Basic keep their signatures; new
functions come as new methods.

## Adding a part to the page

An extension adds its part in its own `contao/dca/tl_neo_access.php`, with the
static methods of `Neo\BasicBundle\Access\AccessPage`:

```php
use Neo\BasicBundle\Access\AccessPage;

AccessPage::useAi('Example Extension');
AccessPage::useAzureTranslator('Example Extension');
AccessPage::useDeepL('Example Extension');
$GLOBALS['TL_DCA']['tl_neo_access']['fields']['example_api_key'] = AccessPage::secretField();
AccessPage::addSection('example_legend', ['example_api_key'], 'Example Extension');
AccessPage::addLicense('example', 'vendor/example-bundle', 'Example Extension');
AccessPage::addTerms('example', 'Example Extension', 'example_terms', ['all' => 1, 'basic' => 1]);
```

| Method | What it does |
| --- | --- |
| `useAi($name)` | The extension uses the AI connection; the section **AI** is shown and names the extension. |
| `useAzureTranslator($name)` | The extension uses the Azure Translator access; the section **Azure Translator** is shown. |
| `useDeepL($name)` | The extension uses the DeepL access; the section **DeepL** is shown. |
| `secretField($maxlength = 512)` | Field definition for a key: shown as a mask, an empty field keeps the stored value, stored encrypted, exactly as typed. |
| `addSection($legend, $fields, $name, $optional = false)` | An own section with the extension's fields; `$legend` ends in `_legend`. Label of the section: `TL_LANG.tl_neo_access.<legend>_name`. An optional section shows a note instead of an error while it is empty: `TL_LANG.tl_neo_access.<section>_optional` (`<section>` is the legend without `_legend`), otherwise the general text. A section is required as soon as one extension adds it as required. |
| `addLicense($module, $package, $name)` | A licence row under **Licences**. `$module` is the licence code of the extension. The status line links to the page of the package in the store (`Store`); what each edition includes is shown there. |
| `addTerms($module, $name, $translationDomain, $passages)` | Terms under **Terms of the extensions**: passages in order, passage => version; the first applies to every edition, each further one to the edition of the same name and above. Texts under `<passage>.title` and `<passage>.text` of the translation domain, paragraphs separated by a blank line; the German text is binding. |

The page shows the licences first, then the terms, the AI connection, Azure
Translator, DeepL and the sections in the order of the extensions. Each
section names the extensions that use it in its status line (not in its
title). Sections with nothing left to do start collapsed: the AI connection
once it is set up, Azure Translator and DeepL unless half filled in, the terms
once confirmed, a section of an extension once its keys are stored (an
optional one also while it is empty). The licences are always open.

## Reading keys and connections

- `AccessSettings::secret($column)` returns a stored key in plain text, `''`
  when none is stored or it cannot be decrypted.
- `AiConnection` (`baseUrl()`, `apiKey()`, `isConfigured()`) gives address and
  key of the route.
- `ModelCatalog` lists the models of the route (cached for an hour); empty
  without a key, so the field of the extension stays a text field.
  `transcriptionModels()` lists the transcription models a route approves;
  `capabilities()` says what the route reports about a model (reasons, calls
  tools, longest answer), or null where a route does not say.

## Pages in the language of the visitor

`Page\LanguageVersion::in($pageId, $rootId)` returns the ID of the page linked to
`$pageId` in the website with the root page `$rootId`, for a setting that holds one
page (privacy policy, accessibility statement) on websites in several languages.
The languages are linked with `terminal42/contao-changelanguage` ("Main language
page" of a page). Without that extension, or without a linked page in that
website, the page itself comes back.

## Warnings in the extension

- `AccessPage::addMissingMessage($text, $legend)` adds the warning about a
  missing key at the top of the extension's page, with the link to the section
  for users who may open the page.
- `AccessPage::addTermsMessage($text)` does the same for terms still to be
  confirmed.
- `AccessPage::canOpen()` tells whether the current user may open the page.

## Translating

Three services, one interface: `Translation\TranslatorInterface` with
`translate($texts, $from, $to, $html = true)` (a list of HTML or plain texts,
the translations in the same order and form) and `isConfigured()`.

| Class | Service |
| --- | --- |
| `Translation\AzureTranslator` | Azure Translator (section **Azure Translator**). |
| `Translation\DeepLTranslator` | DeepL API v2 (section **DeepL**); `usage()` returns the characters used and the limit. |
| `Translation\AiTranslator` | A language model of the AI connection; `withModel($model)` returns the translator with the model of the extension, without a model it is not configured. |

`Translation\Translators` gives the translator of a service by its code
(`azure`, `deepl`, `ai`): `get($service, $model)`, `hasAccess($service)`
(the access is set up on the page; for `ai` the AI connection),
`withAccess()` (the services with access, in that order) and
`Translators::legend($service)` (the section, e.g. for
`AccessPage::url()`).

All three send the texts as HTML and protect Contao's insert tags: in text as
an element with `translate="no"`, inside a tag (e.g. a link's `href`) as a
placeholder; they come back unchanged. Texts go in batches (Azure 100, DeepL
50, AI model 20 per request). DeepL gets `tag_handling=html` and the language
codes DeepL expects (`EN-GB` unless `en-US`, `PT-PT` unless `pt-BR`,
`ZH-HANS`/`ZH-HANT`); the formality is left to DeepL. The AI model gets fixed
instructions (`AiTranslator::instructions()`): translate faithfully, keep tags,
insert tags and placeholders, answer with the translations only; temperature
0.1, or the model's own where it accepts no other.

`TranslationException::$reason` says why a translation failed:
`not_configured`, `unreachable` or `rejected` (`$status` is the HTTP status,
`$service` names the service). Mark the result as a machine translation where
the user sees it.

## Licence and terms

- `LicenseService::state($module)` returns the state of the licence on the
  current host; functions of a paid edition ask `hasEdition('plus', 'pro')`.
- A licence covers the domains entered in it; the licence service also counts
  the installations a key runs in. Basic sends the identifier of the
  installation (`License\Installation`) and the domains of its root pages for
  every extension; an extension never assembles domains itself and never asks
  the licence service directly. `LicenseState::$reasons` (domain => `null` or
  `notLicensed`, `installationsExhausted`, `domainsPerInstallationExceeded`),
  `$installations` (`allowed`, `used`) and `$domainsPerInstallation` say what
  the licence service answered; they are empty or `null` with a licence service
  that does not count installations.
- `Terms\TermsService::acceptedUpTo()` tells up to which passage the terms of
  an edition are confirmed. Raise the version of a passage when its text
  changes in substance.

## Credit line

- `CreditLine::register()` adds the shared script to the page.
- `CreditLine::proof($module, $host)` returns the signed confirmation for the
  configuration of the extension's script, or null without a valid licence.
- The extension's script calls
  `NeoCredit.attach(container, {module, proof, lang, onBlock})`. `onBlock`
  is called with `true` and a message while the credit line is not visible.

The licence code is written into the extension's script, not taken from its
configuration. Without the shared script the extension takes no input.

## Cache tags of pages

`Cache\PageCacheTags` tags front-end pages and invalidates tags on every
supported Contao version: it uses Contao's `CacheTagManager` (Contao 5.5 and
later) and `EntityCacheTags` on Contao 5.3 and 5.4. `tag(['contao.db.tl_x'])`
adds tags to the current response, `invalidate([...])` removes every cached
page that carries one of them. Without Contao's page cache both do nothing.

## Breadcrumb in the menu group

Modules in the menu group `neo` get a breadcrumb without clicks that stay on the
page (`EventListener\BreadcrumbListener`, after Contao's own listener):

- On a further table of a module (texts, a log …) the module name leads to the
  module start, followed by the label of the global operation of the first
  table that opens this table (`href` with `table=…`).
- A table with only one record, whose list opens the form of that record, sets
  `'neoSingleRecord' => true` in its DCA `config`. As the first table of a
  module, the breadcrumb of the form is the module name only, without a link;
  as a further table, the form is named like the global operation that opens
  it. Its child tables keep Contao's items.

The form of such a record is best opened with `nb=1` (no "Go back") and without
"Save and close" (`edit.buttons_callback`), since there is no list to return to.

## Feedback on a click in the back end

Every module of the menu group `neo` loads `public/actions.js` and
`actions.css` (`EventListener\BackendActionsListener`):

- A submit button, a button link (`a.tl_submit`) or a global operation shows a
  spinner and `aria-busy` from the click until the page answers.
- A submit button that runs an action and reloads the form (a scan, loading
  suggestions, "Save and generate" …) carries `data-neo-action` and a `name`.
  After the answer the button is where it was on the screen, and the messages
  of the answer (`Message::add…()`) stand right below it, in the bar of the
  form buttons after them, instead of at the top of the page.
- An action that runs in place (a fetch) uses
  `window.NeoActions.busy(button, true|false)` for the same spinner and shows
  its result right below or next to the button.

## Taking over old keys

Extensions that stored their own keys before can take them over once, in a
migration: `AiConnection::store()` stores route, address and key;
`isAdopted()` and `markAdopted()` record that the takeover ran, so a key
removed later does not come back. `AiConnection::isStorable()` tells whether
the columns exist yet; on a new installation the takeover waits for the second
pass of `contao:migrate`.

## Buttons at the edge of the screen

An extension with a floating button (a small button fixed in a corner of the
screen that opens its panel) lets Basic arrange it with the buttons of other
extensions in the same corner: one settings button with a menu, stacked or
side by side, as set in the page layout ([Buttons at the edge of the
screen](dock.md)). The extension keeps its button, its panel and its own
setting for the corner; nothing depends on the order in which the scripts
load, and without Basic's script every button stays where its own CSS puts it.

1. Where the button is rendered (front-end module, hook `generatePage`, or a
   listener of the response), call `Neo\BasicBundle\Dock\Dock::register($page)`.
   It adds `bundles/neobasic/dock.js` and `dock.css` to the page assets of
   Contao (combined with the others) and the setting of the page layout as
   `<meta name="neo-dock" content="menu|vertical|horizontal"
   data-neo-dock-label="…">`. A page that comes out without them (the button
   was written after the page assets were processed) gets them from
   `DockResponseListener`. Inject the service as `Dock|null $dock = null` to
   keep working with an older Basic.
2. In the extension's `contao/dca/tl_layout.php`, call
   `Neo\BasicBundle\Dock\DockPalette::apply()` (inside
   `class_exists(DockPalette::class)`): the field appears in the page layout
   only while an installed extension uses it.
3. Mark the markup:

| Attribute | On | Meaning |
| --- | --- | --- |
| `data-neo-dock="<name>"` | the element that sits in the corner (`position: fixed`) | Takes part. Name of the entry, e.g. `cookies` |
| `data-neo-dock-corner` | the same element | `bottom-left`, `bottom-right`, `top-left` or `top-right` (the extension's own setting) |
| `data-neo-dock-label` | the same element | Text of the entry in the menu |
| `data-neo-dock-order` | the same element | Number; lower sits nearer the corner and comes first in the menu. In use: `10` AI chat, `20` accessibility panel, `30` cookie settings |
| `data-neo-dock-launcher` | the button that opens the panel | Default: the element itself |
| `data-neo-dock-icon` | an `<svg>` (or a `<template>` holding it) inside the element | Icon of the entry; drawn outside the extension's widget, so with its own `fill`/`stroke` and `currentColor` |
| `data-neo-dock-badge`, `data-neo-dock-badge-label` | the element, set by the extension's script | Short state next to the entry (e.g. a number) and its wording for screen readers |

A button takes part while neither it nor its element has the attribute
`hidden`; the script watches these attributes and the page, so buttons that
come, go, or show later are arranged at once.

What the script does with the element:

- `data-neo-dock-place="menu"`: the launcher is hidden (`display: none`) and
  the entry clicks it (`launcher.click()`), after moving the focus to the
  settings button. The element is moved onto the settings button: its edge
  towards the corner lies at the far edge of the settings button, so a panel
  that opens next to the extension's own button opens next to the settings
  button.
- `data-neo-dock-place="stack"`: the element is moved, so its launcher sits in
  the column or row; in a column, the elements beyond an open panel (launcher
  `aria-expanded="true"`) get `data-neo-dock-covered`, which hides their
  launchers until it closes.
- The move comes as the custom properties `--neo-dock-x` and `--neo-dock-y`
  on the element: distances in pixels away from the two edges of its corner
  (negative towards them). The extension's CSS adds them to its distances,
  e.g. `bottom: calc(var(--offset) + var(--neo-dock-y, 0px))`, and does not
  move the element with `transform` (that would move fixed panels inside it).
- Alone in its corner (or without a second button there), the element stays
  untouched.

Focus: when the panel closes, give the focus back to the element that had it
when the panel opened, if it is still visible, otherwise to the launcher. In
the menu that is the settings button. A script that shows its launcher again
and moves the focus in the same step calls `window.NeoDock.refresh()` first,
so the settings button is back at once (`NeoDock.refresh()` arranges again for
changes the script cannot see, e.g. a stylesheet that changes a size).

`tests/e2e/dock.e2e.cjs` checks all of this in a browser (see the README in
that folder).
