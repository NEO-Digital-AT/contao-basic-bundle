---
titel: Für Entwickler
kurzfassung: Wie eine Erweiterung auf Basic aufbaut – Abschnitte auf „Zugänge und Lizenzen“ ergänzen, Schlüssel lesen, mit Azure Translator, DeepL oder einem KI-Modell übersetzen, Lizenz und Bedingungen prüfen, den Hinweis der Gratisfassung zeigen und die Brotkrumen der Menügruppe.
zielgruppe: entwickler
sprache: de
stand: 2026-11-14
---

# Für Entwickler

Eine Erweiterung, die auf Basic aufbaut, verlangt
`neo-digital/contao-basic-bundle` in ihrer `composer.json`. Die öffentlichen
Methoden von Basic behalten ihre Signatur; Neues kommt als neue Methode.

## Einen Teil der Seite ergänzen

Eine Erweiterung ergänzt ihren Teil in ihrer eigenen
`contao/dca/tl_neo_access.php`, mit den statischen Methoden von
`Neo\BasicBundle\Access\AccessPage`:

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

| Methode | Was sie tut |
| --- | --- |
| `useAi($name)` | Die Erweiterung nutzt die KI-Verbindung; der Abschnitt **KI-Verbindung** erscheint und nennt die Erweiterung. |
| `useAzureTranslator($name)` | Die Erweiterung nutzt den Zugang zu Azure Translator; der Abschnitt **Azure Translator** erscheint. |
| `useDeepL($name)` | Die Erweiterung nutzt den Zugang zu DeepL; der Abschnitt **DeepL** erscheint. |
| `secretField($maxlength = 512)` | Felddefinition für einen Schlüssel: als Maske angezeigt, ein leeres Feld behält den gespeicherten Wert, verschlüsselt gespeichert, genau so, wie er eingegeben wurde. |
| `addSection($legend, $fields, $name, $optional = false)` | Ein eigener Abschnitt mit den Feldern der Erweiterung; `$legend` endet auf `_legend`. Name des Abschnitts: `TL_LANG.tl_neo_access.<legend>_name`. Ein optionaler Abschnitt zeigt leer einen Hinweis statt eines Fehlers: `TL_LANG.tl_neo_access.<abschnitt>_optional` (`<abschnitt>` ist die Legende ohne `_legend`), sonst der allgemeine Text. Ein Abschnitt ist Pflicht, sobald ihn eine Erweiterung als Pflicht ergänzt. |
| `addLicense($module, $package, $name)` | Eine Lizenzzeile unter **Lizenzen**. `$module` ist der Lizenzcode der Erweiterung. Die Statuszeile verlinkt die Seite des Pakets im Store (`Store`); dort steht, was jede Stufe umfasst. |
| `addTerms($module, $name, $übersetzungsdomain, $passagen)` | Bedingungen unter **Bedingungen der Erweiterungen**: Passagen in Reihenfolge, Passage => Fassung; die erste gilt für jede Stufe, jede weitere für die gleichnamige Stufe und darüber. Texte unter `<passage>.title` und `<passage>.text` der Übersetzungsdomain, Absätze durch eine Leerzeile getrennt; verbindlich ist der deutsche Text. |

Die Seite zeigt zuerst die Lizenzen, dann die Bedingungen, die
KI-Verbindung, Azure Translator, DeepL und die Abschnitte in der Reihenfolge
der Erweiterungen. Jeder Abschnitt nennt die Erweiterungen, die ihn nutzen, in
seiner Statuszeile (nicht im Titel). Abschnitte ohne offene Aufgabe sind zu
Beginn zugeklappt: die KI-Verbindung, sobald sie eingerichtet ist, Azure
Translator und DeepL, außer halb ausgefüllt, die Bedingungen, sobald sie
bestätigt sind, ein Abschnitt einer Erweiterung, sobald seine Schlüssel
gespeichert sind (ein optionaler auch, solange er leer ist). Die Lizenzen sind
immer offen.

## Schlüssel und Verbindungen lesen

- `AccessSettings::secret($spalte)` liefert einen gespeicherten Schlüssel im
  Klartext, `''`, wenn keiner gespeichert ist oder er sich nicht entschlüsseln
  lässt.
- `AiConnection` (`baseUrl()`, `apiKey()`, `isConfigured()`) liefert Adresse
  und Schlüssel der Route.
- `ModelCatalog` listet die Modelle der Route (eine Stunde
  zwischengespeichert); ohne Schlüssel leer, sodass das Feld der Erweiterung
  ein Textfeld bleibt. `transcriptionModels()` listet die
  Transkriptionsmodelle, die eine Route freigibt; `capabilities()` sagt, was
  die Route über ein Modell meldet (denkt nach, ruft Werkzeuge auf, längste
  Antwort), oder null, wo eine Route es nicht sagt.

## Seiten in der Sprache des Besuchers

`Page\LanguageVersion::in($pageId, $rootId)` liefert die ID der Seite, die mit
`$pageId` in der Website mit dem Startpunkt `$rootId` verknüpft ist — für eine
Einstellung, die eine einzige Seite hält (Datenschutzerklärung,
Barrierefreiheitserklärung), auf Websites in mehreren Sprachen. Verknüpft werden die
Sprachen mit `terminal42/contao-changelanguage` („Hauptsprachseite“ einer Seite).
Ohne diese Erweiterung oder ohne verknüpfte Seite in dieser Website kommt die Seite
selbst zurück.

## Warnungen in der Erweiterung

- `AccessPage::addMissingMessage($text, $legend)` setzt oben auf der Seite der
  Erweiterung die Warnung über einen fehlenden Schlüssel, mit dem Link auf den
  Abschnitt für Benutzer, die die Seite öffnen dürfen.
- `AccessPage::addTermsMessage($text)` tut dasselbe für noch zu bestätigende
  Bedingungen.
- `AccessPage::canOpen()` sagt, ob der aktuelle Benutzer die Seite öffnen darf.

## Übersetzen

Drei Dienste, eine Schnittstelle: `Translation\TranslatorInterface` mit
`translate($texte, $von, $nach, $html = true)` (eine Liste von HTML- oder
reinen Texten, die Übersetzungen in derselben Reihenfolge und Form) und
`isConfigured()`.

| Klasse | Dienst |
| --- | --- |
| `Translation\AzureTranslator` | Azure Translator (Abschnitt **Azure Translator**). |
| `Translation\DeepLTranslator` | DeepL API v2 (Abschnitt **DeepL**); `usage()` liefert die verbrauchten Zeichen und die Grenze. |
| `Translation\AiTranslator` | Ein Sprachmodell der KI-Verbindung; `withModel($model)` liefert den Übersetzer mit dem Modell der Erweiterung, ohne Modell ist er nicht eingerichtet. |

`Translation\Translators` gibt den Übersetzer eines Dienstes nach seinem Code
(`azure`, `deepl`, `ai`): `get($dienst, $modell)`, `hasAccess($dienst)`
(der Zugang ist auf der Seite eingerichtet; für `ai` die KI-Verbindung),
`withAccess()` (die Dienste mit Zugang, in dieser Reihenfolge) und
`Translators::legend($dienst)` (der Abschnitt, z. B. für `AccessPage::url()`).

Alle drei schicken die Texte als HTML und schützen Contaos Insert-Tags: im
Text als Element mit `translate="no"`, innerhalb eines Tags (z. B. im `href`
eines Links) als Platzhalter; sie kommen unverändert zurück. Die Texte gehen in
Paketen (Azure 100, DeepL 50, KI-Modell 20 je Anfrage). DeepL bekommt
`tag_handling=html` und die Sprachcodes, die DeepL erwartet (`EN-GB`, außer
bei `en-US`, `PT-PT`, außer bei `pt-BR`, `ZH-HANS`/`ZH-HANT`); die Anrede
(formality) bleibt DeepL überlassen. Das KI-Modell bekommt feste Anweisungen
(`AiTranslator::instructions()`): treu übersetzen, Tags, Insert-Tags und
Platzhalter behalten, nur die Übersetzungen zurückgeben; Temperatur 0,1, oder
die eigene des Modells, wo es keine andere annimmt.

`TranslationException::$reason` sagt, warum eine Übersetzung scheiterte:
`not_configured`, `unreachable` oder `rejected` (`$status` ist der
HTTP-Status, `$service` nennt den Dienst). Das Ergebnis dort, wo der Benutzer
es sieht, als maschinelle Übersetzung kennzeichnen.

## Lizenz und Bedingungen

- `LicenseService::state($module)` liefert den Zustand der Lizenz auf dem
  aktuellen Host; Funktionen einer bezahlten Stufe fragen
  `hasEdition('plus', 'pro')`.
- Eine Lizenz gilt für die in ihr eingetragenen Domains; der Lizenzdienst
  zählt zusätzlich die Installationen, in denen ein Schlüssel läuft. Basic
  schickt für jede Erweiterung die Kennung der Installation (`License\Installation`) und die Domains ihrer
  Startpunkte; eine Erweiterung stellt nie selbst Domains zusammen und fragt den
  Lizenzdienst nie selbst. `LicenseState::$reasons` (Domain => `null` oder
  `notLicensed`, `installationsExhausted`, `domainsPerInstallationExceeded`),
  `$installations` (`allowed`, `used`) und `$domainsPerInstallation` sagen, was
  der Lizenzdienst geantwortet hat; ohne Zählung der Installationen beim
  Lizenzdienst sind sie leer bzw. `null`.
- `Terms\TermsService::acceptedUpTo()` sagt, bis zu welcher Passage die
  Bedingungen einer Stufe bestätigt sind. Die Fassung einer Passage erhöhen,
  wenn sich ihr Text inhaltlich ändert.

## Hinweis der Gratisfassung

- `CreditLine::register()` bindet das gemeinsame Skript in die Seite ein.
- `CreditLine::proof($module, $host)` liefert den signierten Nachweis für die
  Konfiguration des Skripts der Erweiterung, oder null ohne gültige Lizenz.
- Das Skript der Erweiterung ruft
  `NeoCredit.attach(container, {module, proof, lang, onBlock})` auf. `onBlock`
  wird mit `true` und einer Meldung aufgerufen, solange der Hinweis nicht
  sichtbar ist.

Der Lizenzcode steht im Skript der Erweiterung, nicht in ihrer Konfiguration.
Ohne das gemeinsame Skript nimmt die Erweiterung keine Eingaben an.

## Cache-Tags der Seiten

`Cache\PageCacheTags` versieht Seiten im Frontend mit Cache-Tags und macht
Tags ungültig, auf jeder unterstützten Contao-Fassung: Es nutzt Contaos
`CacheTagManager` (ab Contao 5.5) und auf Contao 5.3 und 5.4
`EntityCacheTags`. `tag(['contao.db.tl_x'])` hängt Tags an die aktuelle
Antwort, `invalidate([...])` entfernt jede zwischengespeicherte Seite mit
einem dieser Tags. Ohne Contaos Seiten-Cache tun beide nichts.

## Brotkrumen in der Menügruppe

Module der Menügruppe `neo` bekommen Brotkrumen ohne Klicks, die auf der Seite
bleiben (`EventListener\BreadcrumbListener`, nach Contaos eigenem Listener):

- Auf einer weiteren Tabelle eines Moduls (Texte, ein Protokoll …) führt der
  Modulname zum Modulstart; danach folgt die Beschriftung der globalen
  Operation der ersten Tabelle, die diese Tabelle öffnet (`href` mit
  `table=…`).
- Eine Tabelle mit nur einem Datensatz, deren Liste das Formular dieses
  Datensatzes öffnet, setzt `'neoSingleRecord' => true` in der `config` ihres
  DCA. Als erste Tabelle eines Moduls zeigen die Brotkrumen des Formulars nur
  den Modulnamen, ohne Link; als weitere Tabelle heißt das Formular wie die
  globale Operation, die es öffnet. Ihre Kind-Tabellen behalten Contaos
  Einträge.

Das Formular eines solchen Datensatzes öffnet man am besten mit `nb=1` (kein
„Zurück“) und ohne „Speichern und schließen“ (`edit.buttons_callback`), weil es
keine Liste gibt, zu der es zurückginge.

## Rückmeldung bei einem Klick im Backend

Jedes Modul der Menügruppe `neo` lädt `public/actions.js` und `actions.css`
(`EventListener\BackendActionsListener`):

- Ein Absende-Knopf, ein Knopf-Link (`a.tl_submit`) oder eine globale
  Operation zeigt ab dem Klick einen Ladekreis und `aria-busy`, bis die Seite
  antwortet.
- Ein Absende-Knopf, der eine Aktion ausführt und das Formular neu lädt (ein
  Scan, Vorschläge laden, „Speichern und generieren“ …), trägt
  `data-neo-action` und einen `name`. Nach der Antwort steht der Knopf, wo er
  auf dem Bildschirm war, und die Meldungen der Antwort (`Message::add…()`)
  stehen direkt darunter, in der Leiste der Formular-Knöpfe nach ihnen, statt
  oben auf der Seite.
- Eine Aktion an Ort und Stelle (fetch) nutzt
  `window.NeoActions.busy(knopf, true|false)` für denselben Ladekreis und zeigt
  ihr Ergebnis direkt unter oder neben dem Knopf.

## Alte Schlüssel übernehmen

Erweiterungen, die ihre Schlüssel früher selbst gespeichert haben, können sie
einmal in einer Migration übernehmen: `AiConnection::store()` speichert Route,
Adresse und Schlüssel; `isAdopted()` und `markAdopted()` halten fest, dass die
Übernahme lief, sodass ein später entfernter Schlüssel nicht zurückkommt.
`AiConnection::isStorable()` sagt, ob die Spalten schon existieren; bei einer
neuen Installation wartet die Übernahme auf den zweiten Durchlauf von
`contao:migrate`.

## Schaltflächen am Bildschirmrand

Eine Erweiterung mit einer schwebenden Schaltfläche (eine kleine Schaltfläche
fest in einer Ecke des Bildschirms, die ihr Panel öffnet) lässt Basic sie mit
den Schaltflächen anderer Erweiterungen in derselben Ecke anordnen: eine
Einstellungs-Schaltfläche mit Menü, übereinander oder nebeneinander, wie im
Seitenlayout eingestellt ([Schaltflächen am Bildschirmrand](dock.md)). Die
Erweiterung behält ihre Schaltfläche, ihr Panel und ihre eigene Einstellung
für die Ecke; nichts hängt davon ab, in welcher Reihenfolge die Skripte laden,
und ohne das Skript von Basic bleibt jede Schaltfläche, wo ihr eigenes CSS sie
hinsetzt.

1. Wo die Schaltfläche ausgegeben wird (Frontend-Modul, Hook `generatePage`
   oder ein Listener der Antwort), `Neo\BasicBundle\Dock\Dock::register($page)`
   aufrufen. Das fügt `bundles/neobasic/dock.js` und `dock.css` den
   Seiten-Dateien von Contao hinzu (mit den anderen zusammengefasst) und die
   Einstellung des Seitenlayouts als `<meta name="neo-dock"
   content="menu|vertical|horizontal" data-neo-dock-label="…">`. Eine Seite,
   die ohne sie herauskommt (die Schaltfläche wurde nach den Seiten-Dateien
   geschrieben), bekommt sie vom `DockResponseListener`. Den Dienst als
   `Dock|null $dock = null` einbinden, damit die Erweiterung auch mit einem
   älteren Basic läuft.
2. In `contao/dca/tl_layout.php` der Erweiterung
   `Neo\BasicBundle\Dock\DockPalette::apply()` aufrufen (innerhalb von
   `class_exists(DockPalette::class)`): Das Feld erscheint im Seitenlayout nur,
   solange eine installierte Erweiterung es nutzt.
3. Das Markup kennzeichnen:

| Attribut | An | Bedeutung |
| --- | --- | --- |
| `data-neo-dock="<name>"` | dem Element, das in der Ecke sitzt (`position: fixed`) | Macht mit. Name des Eintrags, z. B. `cookies` |
| `data-neo-dock-corner` | demselben Element | `bottom-left`, `bottom-right`, `top-left` oder `top-right` (die eigene Einstellung der Erweiterung) |
| `data-neo-dock-label` | demselben Element | Text des Eintrags im Menü |
| `data-neo-dock-order` | demselben Element | Zahl; niedriger sitzt näher an der Ecke und steht im Menü weiter oben. Vergeben: `10` KI-Chat, `20` Barrierefreiheits-Panel, `30` Cookie-Einstellungen |
| `data-neo-dock-launcher` | der Schaltfläche, die das Panel öffnet | Vorgabe: das Element selbst |
| `data-neo-dock-icon` | einem `<svg>` (oder einem `<template>` damit) im Element | Symbol des Eintrags; wird außerhalb des Widgets der Erweiterung gezeichnet, also mit eigenem `fill`/`stroke` und `currentColor` |
| `data-neo-dock-badge`, `data-neo-dock-badge-label` | dem Element, gesetzt vom Skript der Erweiterung | Kurzer Zustand neben dem Eintrag (z. B. eine Zahl) und seine Formulierung für Screenreader |

Eine Schaltfläche macht mit, solange weder sie noch ihr Element das Attribut
`hidden` trägt; das Skript beobachtet diese Attribute und die Seite, sodass
Schaltflächen, die kommen, gehen oder später erscheinen, sofort angeordnet
werden.

Was das Skript mit dem Element macht:

- `data-neo-dock-place="menu"`: Die Schaltfläche der Erweiterung wird
  ausgeblendet (`display: none`), der Eintrag klickt sie
  (`launcher.click()`), nachdem er den Fokus auf die Einstellungs-Schaltfläche
  gesetzt hat. Das Element wird auf die Einstellungs-Schaltfläche gerückt: Seine
  Kante zur Ecke hin liegt an der äußeren Kante der Einstellungs-Schaltfläche,
  sodass ein Panel, das sich neben der eigenen Schaltfläche öffnet, sich neben
  der Einstellungs-Schaltfläche öffnet.
- `data-neo-dock-place="stack"`: Das Element wird so verschoben, dass seine
  Schaltfläche in der Spalte oder Reihe sitzt; in einer Spalte bekommen die
  Elemente jenseits eines offenen Panels (Schaltfläche mit
  `aria-expanded="true"`) `data-neo-dock-covered`, das ihre Schaltflächen
  ausblendet, bis es sich schließt.
- Die Verschiebung kommt als Custom Properties `--neo-dock-x` und
  `--neo-dock-y` am Element: Abstände in Pixeln weg von den beiden Rändern
  seiner Ecke (negativ zu ihnen hin). Das CSS der Erweiterung addiert sie zu
  seinen Abständen, z. B. `bottom: calc(var(--offset) + var(--neo-dock-y, 0px))`,
  und verschiebt das Element nicht mit `transform` (das verschöbe feste Panels
  darin).
- Allein in ihrer Ecke bleibt das Element unberührt.

Fokus: Schließt sich das Panel, den Fokus dem Element zurückgeben, das ihn
beim Öffnen hatte, wenn es noch sichtbar ist, sonst der eigenen Schaltfläche.
Im Menü ist das die Einstellungs-Schaltfläche. Ein Skript, das seine
Schaltfläche wieder zeigt und im selben Schritt den Fokus setzt, ruft vorher
`window.NeoDock.refresh()` auf, damit die Einstellungs-Schaltfläche sofort
wieder da ist (`NeoDock.refresh()` ordnet neu an, auch bei Änderungen, die das
Skript nicht sehen kann, z. B. ein Stylesheet, das eine Größe ändert).

`tests/e2e/dock.e2e.cjs` prüft das alles im Browser (siehe README in diesem
Ordner).
