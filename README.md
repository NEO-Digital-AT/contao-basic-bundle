# Basic for Contao

Basic is the shared foundation of a family of Contao extensions. It adds the back-end page **Access keys and licences**, on which the licence keys of the installed extensions, one AI connection, the accesses of Azure Translator and DeepL and the keys of external services are entered once and stored encrypted. Extensions that need it install it as a dependency; on its own it adds nothing to the back end.

*Deutsche Fassung weiter unten: [Deutsch](#basic-für-contao-deutsch).*

## Features

- One page for keys and licences: licences first, then terms, AI connection, translation services and the sections of the extensions; it shows only what the installed extensions add, each section names the extensions that use it, and sections with nothing left to do start collapsed.
- One AI connection for all extensions: route (Requesty EU route, Requesty standard route, OpenAI or an own OpenAI-compatible address) and key; each extension chooses its own model.
- Shared translation services: Azure Translator (key, processing location, region), DeepL (key; Free or Pro from the key) and translation by a language model of the AI connection, behind one interface for the extensions.
- **Test connection** and **Check now** run in place, from the server, without leaving the page; the key is shown only as length, start and end.
- Keys stored encrypted (libsodium, keyed by the app secret) and never shown again.
- Licence check for paid extensions: activated when the key is saved, checked again once a day, the signed confirmation checked locally on every request.
- Licences per domain, installations counted: a licence covers the domains entered in it (subdomains included). Each Contao installation sends its own identifier with every check, together with the domains of its root pages, so that a key does not run in more installations than paid for (agency licences: many installations with one key; additional domains per installation as an add-on). The row of the licence shows per domain whether it is covered and why not, and how many installations are in use.
- Sent to the licence service: the licence key, the code of the extension, the identifier of the installation, the domains of the website and the versions of the system, the extension and PHP. No visitor data.
- Terms of the extensions confirmed on the page, every confirmation kept as a record.
- The shared credit line of the free editions; back end in eleven languages.
- Buttons at the edge of the screen: the floating buttons of the extensions (cookie settings, accessibility panel, AI chat) that share a corner become one settings button with a menu, or are stacked one above the other or side by side, as set in the page layout.

## Requirements

- Contao 5.3 to 6.0 with PHP 8.2 to 8.5, as far as the Contao version supports it (Contao 5.7 needs PHP 8.3, Contao 6.0 PHP 8.4), with the PHP extensions `sodium`, `openssl` and `json`.
- For the AI connection: an account and an API key with an OpenAI-compatible provider, e.g. [Requesty](https://www.requesty.ai/) or [OpenAI](https://platform.openai.com/).
- For translations (only when an installed extension uses them): an Azure Translator resource in the Azure portal (key and region), a DeepL API account (Free or Pro), or the AI connection.
- The Contao cron (web cron or `contao:cron`) for the daily licence check.

## Installation

Extensions that build on Basic install it automatically. Basic is not listed in the Contao Manager: it comes with the extensions that need it and adds nothing on its own.

### Composer

```bash
composer require neo-digital/contao-basic-bundle
php vendor/bin/contao-console contao:migrate
```

Then enter the keys under **Access keys and licences** in the menu group of the extensions.

## Documentation

The full documentation comes with the extension: [docs/en/README.md](docs/en/README.md).

- [Installation](docs/en/installation.md) – requirements, updating, removing
- [Setup](docs/en/setup.md) – licences, terms, AI connection, Azure Translator, DeepL
- [Permissions](docs/en/permissions.md) – who may open the page and what Contao needs for it
- [Usage](docs/en/usage.md) – the order of the sections, status lines, testing a connection, checking a licence, terms
- [Buttons at the edge of the screen](docs/en/dock.md) – how the buttons of the extensions share a corner, set in the page layout
- [Troubleshooting](docs/en/troubleshooting.md) – the messages of the page and what to do
- [For developers](docs/en/developers.md) – building an extension on Basic

## Development

The unit tests (PHPUnit 11, on PHP 8.2 with Contao 5.3 PHPUnit 10.5; folder `tests/`) need neither a database server nor a Contao installation:

```
composer install
composer test
```

The workflow `.github/workflows/tests.yml` runs them on PHP 8.2 to 8.5 and checks `composer.json` with `composer validate`.

## License

**Licence keys for the extensions:** <https://store.neo-digital.at>

Free of charge. The version available at the time of the order (for free use: of the download) for the then current Contao long-term support version works with it until its end of life; there is no entitlement to versions for later long-term support versions. Support by separate agreement. Full terms: [LICENSE](LICENSE).

---

# Basic für Contao (Deutsch)

Basic ist die gemeinsame Grundlage einer Familie von Contao-Erweiterungen. Es ergänzt die Backend-Seite **Zugänge und Lizenzen**, auf der die Lizenzschlüssel der installierten Erweiterungen, eine KI-Verbindung, die Zugänge zu Azure Translator und DeepL und die Schlüssel fremder Dienste einmal eingetragen und verschlüsselt gespeichert werden. Erweiterungen, die darauf aufbauen, bringen es als Abhängigkeit mit; allein ergänzt es im Backend nichts.

## Funktionen

- Eine Seite für Schlüssel und Lizenzen: zuerst die Lizenzen, dann Bedingungen, KI-Verbindung, Übersetzungsdienste und die Abschnitte der Erweiterungen; sie zeigt nur, was die installierten Erweiterungen ergänzen, jeder Abschnitt nennt die Erweiterungen, die ihn nutzen, und Abschnitte ohne offene Aufgabe sind zugeklappt.
- Eine KI-Verbindung für alle Erweiterungen: Route (Requesty EU-Route, Requesty Standard-Route, OpenAI oder eine eigene OpenAI-kompatible Adresse) und Schlüssel; das Modell wählt jede Erweiterung selbst.
- Gemeinsame Übersetzungsdienste: Azure Translator (Schlüssel, Ort der Verarbeitung, Region), DeepL (Schlüssel; Free oder Pro ergibt sich aus dem Schlüssel) und Übersetzung durch ein Sprachmodell der KI-Verbindung, für die Erweiterungen hinter einer Schnittstelle.
- **Verbindung prüfen** und **Jetzt prüfen** laufen an Ort und Stelle, vom Server aus, ohne die Seite zu verlassen; vom Schlüssel nur Länge, Anfang und Ende.
- Schlüssel verschlüsselt gespeichert (libsodium, Schlüssel aus dem App-Secret) und nie wieder angezeigt.
- Lizenzprüfung bezahlter Erweiterungen: aktiviert beim Speichern des Schlüssels, einmal täglich neu geprüft, der signierte Nachweis bei jedem Aufruf lokal geprüft.
- Lizenz je Domain, Installationen gezählt: Eine Lizenz gilt für die in ihr eingetragenen Domains (Subdomains eingeschlossen). Jede Contao-Installation schickt bei jeder Prüfung ihre eigene Kennung mit, dazu die Domains ihrer Startpunkte, damit ein Schlüssel nicht in mehr Installationen läuft als bezahlt (Agenturlizenzen: viele Installationen mit einem Schlüssel; zusätzliche Domains je Installation als Add-on). Die Zeile der Lizenz zeigt je Domain, ob sie abgedeckt ist und warum nicht, und wie viele Installationen belegt sind.
- An den Lizenzdienst gehen: der Lizenzschlüssel, der Code der Erweiterung, die Kennung der Installation, die Domains der Website und die Versionen von System, Erweiterung und PHP. Keine Daten von Besuchern.
- Bedingungen der Erweiterungen auf der Seite bestätigen, jede Bestätigung als Nachweis aufbewahrt.
- Der gemeinsame Hinweis der Gratisfassungen; Backend in elf Sprachen.
- Schaltflächen am Bildschirmrand: Die schwebenden Schaltflächen der Erweiterungen (Cookie-Einstellungen, Barrierefreiheits-Panel, KI-Chat), die sich eine Ecke teilen, werden zu einer Einstellungs-Schaltfläche mit Menü oder übereinander oder nebeneinander gestapelt, wie im Seitenlayout eingestellt.

## Voraussetzungen

- Contao 5.3 bis 6.0 mit PHP 8.2 bis 8.5, soweit die Contao-Fassung sie unterstützt (Contao 5.7 braucht PHP 8.3, Contao 6.0 PHP 8.4), mit den PHP-Erweiterungen `sodium`, `openssl` und `json`.
- Für die KI-Verbindung: ein Konto und ein API-Schlüssel bei einem OpenAI-kompatiblen Anbieter, z. B. [Requesty](https://www.requesty.ai/) oder [OpenAI](https://platform.openai.com/).
- Für Übersetzungen (nur wenn eine installierte Erweiterung sie nutzt): eine Azure-Translator-Ressource im Azure-Portal (Schlüssel und Region), ein Konto für die DeepL API (Free oder Pro) oder die KI-Verbindung.
- Der Contao-Cron (Web-Cron oder `contao:cron`) für die tägliche Lizenzprüfung.

## Installation

Erweiterungen, die auf Basic aufbauen, installieren es automatisch mit. Im Contao Manager steht Basic nicht in der Liste: Es kommt mit den Erweiterungen, die es brauchen, und bringt für sich allein nichts.

### Composer

```bash
composer require neo-digital/contao-basic-bundle
php vendor/bin/contao-console contao:migrate
```

Danach die Schlüssel unter **Zugänge und Lizenzen** in der Menügruppe der Erweiterungen eintragen.

## Dokumentation

Die vollständige Dokumentation liegt der Erweiterung bei: [docs/de/README.md](docs/de/README.md).

- [Installation](docs/de/installation.md) – Voraussetzungen, Aktualisieren, Entfernen
- [Einrichtung](docs/de/setup.md) – Lizenzen, Bedingungen, KI-Verbindung, Azure Translator, DeepL
- [Rechte](docs/de/permissions.md) – wer die Seite öffnen darf und was Contao dafür braucht
- [Bedienung](docs/de/usage.md) – Reihenfolge der Abschnitte, Statuszeilen, Verbindung prüfen, Lizenz prüfen, Bedingungen
- [Schaltflächen am Bildschirmrand](docs/de/dock.md) – wie sich die Schaltflächen der Erweiterungen eine Ecke teilen, eingestellt im Seitenlayout
- [Fehlersuche](docs/de/troubleshooting.md) – die Meldungen der Seite und was zu tun ist
- [Für Entwickler](docs/de/developers.md) – eine Erweiterung auf Basic aufbauen

## Entwicklung

Die Unit-Tests (PHPUnit 11, auf PHP 8.2 mit Contao 5.3 PHPUnit 10.5; Ordner `tests/`) brauchen weder einen Datenbankserver noch eine Contao-Installation:

```
composer install
composer test
```

Der Workflow `.github/workflows/tests.yml` führt sie auf PHP 8.2 bis 8.5 aus und prüft `composer.json` mit `composer validate`.

## Lizenz

**Lizenzschlüssel für die Erweiterungen:** <https://store.neo-digital.at>

Kostenlos. Die bei der Bestellung (bei unentgeltlicher Nutzung: beim Herunterladen) für die dann aktuelle Long-Term-Support-Version von Contao bereitstehende Fassung funktioniert mit dieser bis zu deren End of Life; ein Anspruch auf Fassungen für spätere Long-Term-Support-Versionen besteht nicht. Support nach gesonderter Vereinbarung. Vollständige Bedingungen: [LICENSE](LICENSE).
