# Browser test of the buttons at the edge of the screen

`dock.e2e.cjs` drives the buttons at the edge of the screen (dock) of a Contao
test installation in Chromium and checks, with numbers instead of looking, at
1440 and 390 px:

- all three buttons of NEO Cookie Consent, NEO Accessibility and NEO
  SuperAgent in one corner (left and right) as one settings button with a
  menu: position and size, the own buttons hidden, the menu button pattern
  (aria attributes, Enter, arrow keys round, Home/End, Esc, Tab), each entry
  opening its panel next to the settings button with the focus inside and
  back on the settings button afterwards (keyboard only), the number of
  settings that are on at the entry *Accessibility*, a click outside, no
  movement with reduced motion or with the switch of the accessibility panel;
- stacked one above the other and side by side (left and right): order,
  gaps, alignment, everything on the screen, buttons above an open chat
  window stepping back and coming back, the accessibility panel next to its
  button;
- two corners, a button in a top corner, each extension alone;
- a chat template without the `data-neo-dock` attributes (keeps its own
  place) and a page whose dock script stops at once (every button at its own
  place and working);
- axe (WCAG 2.2 A/AA) on the buttons, the menu and the open panels, and no
  script errors.

It writes a screenshot per state. This folder is not part of the package
(`.gitattributes`).

## What the test installation needs

- Contao 5.3 or later with Basic, NEO Cookie Consent, NEO Accessibility and
  NEO SuperAgent, served under `DOCK_PAGE` (default
  `http://127.0.0.1:8000/de/dock-test`, a German page).
- An AI connection in *Access keys and licences* (any key: the test sends no
  message, the chat window shows its consent notice) and a published
  assistant with the name *Nova*.
- A page layout for that page with a module of NEO Accessibility, a module of
  SuperAgent and some text, and NEO Cookie Consent switched on with at least
  one service that needs consent.
- `DOCK_SWITCH`: a command that changes the installation for a scenario. It
  is called with `set` and the settings of the scenario: `arrangement=` the
  field of the page layout (`menu`, `vertical`, `horizontal`), `cc=` the
  button to reopen (`left`, `right`, `none`, `off` for the banner switched
  off), `a11y=` the position of the panel button (a corner, `none`, or
  `absent` for no module in the layout), `chat=` the position of the chat
  (`left`, `right`, or `absent`). Without it, set up the installation by hand
  and run one scenario with `DOCK_SCENARIOS`.
- Node.js with Playwright and Chromium, and `axe-core` (`npm install axe-core`
  anywhere; the path of `axe.min.js` is the first argument).

## Running it

```bash
DOCK_SWITCH="php /path/to/switch.php /path/to/contao" \
  node tests/e2e/dock.e2e.cjs /path/to/node_modules/axe-core/axe.min.js /tmp/dock-shots
```

`DOCK_SCENARIOS` (comma separated) runs only some scenarios: `all-left-menu`,
`all-right-menu`, `all-left-vertical`, `all-right-vertical`,
`all-left-horizontal`, `all-right-horizontal`, `two-corners`, `top-corner`,
`cookies-alone`, `accessibility-alone`, `assistant-alone`,
`template-without-dock`, `without-dock`. `DOCK_PLAYWRIGHT` names the
Playwright package if it is not installed next to the script, `DOCK_CHROME`
a Chrome or Chromium binary. The exit code is 0 when every check passed; each
check prints a line with `PASS` or `FAIL`.
