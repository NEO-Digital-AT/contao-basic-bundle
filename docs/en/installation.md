---
title: Installation
summary: Requirements, installed with the extensions or with Composer, public files, updating and removing Basic.
audience: operations
language: en
updated: 2026-11-15
---

# Installation

Basic is usually not installed on its own: every extension that builds on it
requires it and installs it automatically. Install it by hand only when you
want to add it before the extensions.

## Requirements

| What | Needed for |
| --- | --- |
| Contao 5.3 to 6.0 | everything |
| PHP 8.2 to 8.5 (Contao 5.7 needs 8.3, Contao 6.0 needs 8.4) with the extensions `sodium`, `openssl` and `json` | everything; `sodium` encrypts the stored keys, `openssl` checks the signed confirmation of a licence |
| An account and an API key with an OpenAI-compatible provider, e.g. [Requesty](https://www.requesty.ai/) or [OpenAI](https://platform.openai.com/) | the AI connection, only when an installed extension uses it |
| An Azure Translator resource in the Azure portal (key and region), or a DeepL API account (Free or Pro), or the AI connection | translations, only when an installed extension uses them |
| The Contao cron (web cron or the command `contao:cron`) | the daily licence check of paid extensions |
| HTTPS on the website | leaving out the credit line of a licensed extension (see [Customising](customising.md)) |

## In the Contao Manager

Basic is not listed in the Contao Manager (package type `library`): it comes
with the extensions that need it. The Contao Manager installs it when you add
one of them, and updates it with them.

## Installing with Composer

```bash
composer require neo-digital/contao-basic-bundle
php vendor/bin/contao-console contao:migrate
```

`contao:migrate` creates the tables of the extension. Composer also copies the
public file `credit.js` (the credit line of the free editions) to
`public/bundles/neobasic/`; if that did not happen, run:

```bash
php vendor/bin/contao-console assets:install
```

## After the installation

The page *Access keys and licences* exists only when an installed extension
adds a section to it. With Basic alone, neither the page nor its menu entry is
shown. Once an extension is installed, continue with [Setup](setup.md).

Extensions that stored their own keys before they used Basic take them over to
the page on the first database update, once. Nothing already stored on the page
is overwritten, and a key removed later does not come back.

## Back-end languages

The back end of Basic is available in English, German, French, Dutch, Italian,
Polish, Czech, Hungarian, Slovenian, Croatian and Spanish. Contao offers
Hungarian and Croatian as back-end languages only once they are enabled in
`config/config.yaml`:

```yaml
contao:
    intl:
        enabled_locales: ['+hu', '+hr']
```

## Updating

1. Update the package with the Contao Manager or with
   `composer update neo-digital/contao-basic-bundle`.
2. Run the database update (`contao:migrate`).
3. Read `CHANGELOG.md` of the new version. When a version needs new
   permissions or a step after the update, the changelog says so.

## Removing

As long as an installed extension requires Basic, it stays installed. Remove
those extensions first.

1. Before removing: empty the licence key of every paid extension on *Access
   keys and licences* and save. This removes the licence from the website and
   tells the licence service.
2. Remove the package with the Contao Manager or with
   `composer remove neo-digital/contao-basic-bundle`.
3. Run the database update. Contao then offers to delete the tables of the
   extension: the stored keys, the licences and the record of the confirmed
   terms. Delete them only if you do not plan to install the extension again
   and no longer need the record of the confirmations.
