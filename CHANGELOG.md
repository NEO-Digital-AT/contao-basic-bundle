# Changelog

All notable changes to this extension are documented here. The format follows
[Keep a Changelog](https://keepachangelog.com/en/1.1.0/); versions follow
[Semantic Versioning](https://semver.org/).

## [1.0.0] - 2026-11-14

Initial release. Runs on Contao 5.3 to 6.0 with PHP 8.2 to 8.5, as far as the Contao version supports it (Contao 5.7 needs PHP 8.3, Contao 6.0 PHP 8.4).

### Added
- Installed as a dependency of the extensions that build on it (package type `library`): not listed in the Contao Manager, loaded by Contao through its Manager plugin.
- Back-end page **Access keys and licences** in the menu group of the extensions: licences first, then terms, AI connection, translation services and the sections of the installed extensions; it shows only what they add, and sections with nothing left to do start collapsed.
- One **AI connection** for all extensions: route (Requesty EU route, Requesty standard route, OpenAI or an own OpenAI-compatible address) and key; each extension chooses its own model from the model list of the route.
- Shared **translation services** behind one interface: Azure Translator (key, processing location, region), DeepL (Free or Pro from the key) and translation by a language model of the AI connection.
- **Test connection** and **Check now** run in place, from the server, without leaving the page, with a spinner on the button and the result right below it (tests) or next to it (licence rows); a key is shown only as length, start and end.
- Feedback on a click in every back-end module of the extensions: a spinner on the button or link until the page answers; actions that reload the form keep the button's place and show their message right at it (`data-neo-action`).
- Keys stored encrypted (libsodium, keyed by the app secret) and never shown again.
- **Licence check** for paid extensions: activated when the key is saved, checked once a day, the signed confirmation checked locally on every request.
- Terms of the extensions confirmed on the page per edition, every confirmation kept as a record.
- Shared services for the extensions: language version of a page (pages linked with terminal42/contao-changelanguage), cache tags of pages, the credit line of the free editions.
- Breadcrumb of the menu group without clicks that stay on the page: further tables of a module lead to the module start, settings with one record show the module name only (`neoSingleRecord` in the DCA).
- **Licences per domain, installations counted.** A licence covers the domains entered in it (subdomains included); the licence service also counts the installations a key runs in. Each Contao installation has its own identifier (32 random characters, created once, stored with the settings of the page, never derived from a domain or the server; a copy of the database keeps it). It is sent on activate, refresh and deactivate, together with the domains of the root pages; a root page without a domain name stands for the host of the request, or the host seen last when the check runs without a request. Licence services that do not count installations yet are answered as before.
- The row of a licence shows per domain "valid" or why the licence does not cover it (domain not entered in the licence, all installations of the licence in use, more domains than allowed per installation), and "Installations: m of n in use · up to n domains per installation" when the licence service says so. When all installations are in use, a red warning says that the licence cannot be activated and links to the manufacturer, who sets the number of installations (one by default). `LicenseState::$reasons`, `$installations` and `$domainsPerInstallation` for extensions.
- In the status line of every licence row, a link to the page of the extension in the store: **Buy a licence key in the store**, or **Higher edition or renewal in the store** once licensed.
- **Buttons at the edge of the screen** (`Dock\Dock`, `bundles/neobasic/dock.js`): the floating buttons of the extensions that share a corner of the screen become one settings button with a menu (WAI-ARIA menu button, default), or are stacked one above the other or side by side; set per page layout (**Several buttons in one corner**, shown once an extension uses it). Extensions mark their button with `data-neo-dock` attributes; the order in which the scripts load does not matter, and without the script every button keeps its own place. Design through `--neo-dock-*` variables.
- Back end in eleven languages; documentation in English and German; unit tests.

After installing: update the database, then enter the keys under **NEO Digital › Access keys and licences**.
