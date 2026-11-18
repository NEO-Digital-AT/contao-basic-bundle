# Documentation images

The images in `docs/<lang>/bilder/` are screenshots of the page *Access keys
and licences* in the back end of a test installation, marked with the NEO
annotation layer (frames and numbers). `capture.cjs` takes them again, for
example after a change to the page. This folder is not part of the Composer
package (`.gitattributes`: `export-ignore`).

## What the test installation needs

- Contao 5.7 with this bundle and at least one extension that adds a section
  (the images show the sections AI and Azure Translator and the licence row
  of an extension).
- The AI connection and Azure Translator set up, with a local stand-in that
  answers like the services; the image `test-connection.png` shows its
  answer. The key row is masked in the image.
- An administrator (`NB_ADMIN_USER`).
- Node.js with Playwright and its Chromium, and the plugin `neo-doku` of
  `NEO-Claude-Plugins` (`annotate.js`) next to this repository, or its path
  in `NB_ANNOTATE`.

## Taking the images

```bash
NB_ADMIN_PASSWORD=… node tools/docs-images/capture.cjs en
```

```bash
NB_ADMIN_PASSWORD=… node tools/docs-images/capture.cjs de
```

The script sets the back-end language of the administrator to the language of
the images and restores it at the end. Do not run two captures at the same
time: both change that language.

| Variable | Default | Meaning |
| --- | --- | --- |
| `NB_BASE` | `http://127.0.0.1:8000` | Address of the test installation |
| `NB_ADMIN_USER`, `NB_ADMIN_PASSWORD` | `admin`, – | Administrator |
| `NB_MYSQL` | `mysql contao` | Command for the database of the test installation |
| `NB_ANNOTATE` | `../NEO-Claude-Plugins/plugins/neo-doku/scripts/annotate.js` | Annotation layer |

Check every image after taking it: frames on the right section, keys masked,
no real names, addresses or keys.
