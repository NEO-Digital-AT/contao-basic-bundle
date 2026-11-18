# Version matrix

Installs all extensions of `packages.txt` together into every Contao version of
`matrix.txt`, each with every PHP version that Contao version runs on, and checks
that they work. Run it before every release; the tool is not part of the package.

Each combination:

1. `composer create-project contao/managed-edition:<version>.*`, then
   `composer require` of all packages from their working copies, the same two
   steps the Contao Manager runs. Composer resolves the dependencies for the PHP
   version under test. `pins.txt` holds back dependencies for Contao 5.4 to
   5.6, which get no more updates and do not run with today's newest
   dependencies, also without the extensions (existing installations keep the
   older versions in their lock file); the file names each package and why.
2. `contao:migrate` into a new database, `contao:user:create`, then a small site
   (`fixture.php`): a page layout with the modules *Accessibility*, *Brevo
   Newsletter* and *SuperAgent*, an article with the elements *Cookie list* and
   *Website check*, and a placeholder AI key (`aikey.php`; no request goes out).
3. The PHP built-in server of that PHP version serves the installation in the
   `prod` environment; `smoke.php` logs in and opens:
   - every back-end module of the menu group *NEO Digital*, and every page it
     links to within the module, up to 30 per module (no links that change data:
     delete, copy, cut, toggle, `key=`);
   - every link from one of these modules to another as it is (such a link must
     not carry over the table of the current list);
   - the forms and lists of Contao that the extensions extend: page tree, root
     and regular page, articles, content elements, modules, layout, user, user
     groups, settings, maintenance, system log, and the edit forms of the
     modules and elements of the site;
   - the front end as a visitor, which must contain the markup of all five
     front-end parts, and the preview.
4. Passed: every page answered 200 without an error text, and the Contao log has
   no entry of level ERROR or higher (apart from "not found").

## Requirements

- `php8.2` … `php8.5` on the `PATH` with `pdo_mysql`, `intl`, `gd`, `mbstring`,
  `dom`, `curl`, `zip`, `sodium`; `composer`; `curl`.
- A MySQL server and a user that may create databases:
  `MATRIX_DATABASE_URL=mysql://root:secret@127.0.0.1:3306`.
- The working copies of the packages next to each other, by default in the
  folder above this repository (`NEO_REPOS` to change it).
- Space: Composer installs from git when archive downloads are not available;
  after `composer require` the run removes the git histories (an installation
  then takes about 250 MB)
  and a passed run removes its installation. `MATRIX_WORK` sets the folder (default `/tmp/neo-version-matrix`).

## Running

```bash
export MATRIX_DATABASE_URL=mysql://root:secret@127.0.0.1:3306
tools/version-matrix/matrix.sh 2
```

The argument is the number of runs at the same time. One line per combination:

```
c53-p82 OK Contao 5.3.54, PHP 8.2.35, 92 pages
c57-p84 FAIL composer require (Contao 5.7.17) — see /tmp/neo-version-matrix/c57-p84.log
```

A single combination: `tools/version-matrix/run.sh 5.7 8.4 8801`. A failed run
keeps its installation and log in `MATRIX_WORK`.
