---
titel: Anpassen
kurzfassung: Der Hinweis der Gratisfassungen auf der Website, die Adressen des Lizenzdienstes und von DeepL für Testinstallationen und die Texte des Backends.
zielgruppe: entwickler
sprache: de
stand: 2026-11-12
---

# Anpassen

*Zugänge und Lizenzen* nutzt Contaos eigene Backend-Masken und -Stile; im
Backend gibt es nichts zu gestalten. Ein kleines Skript von Basic
(`bundles/neobasic/access.js`, nur auf dieser Seite geladen) führt **Verbindung
prüfen** und **Jetzt prüfen** an Ort und Stelle aus. Auf der Website zeigt Basic eine Sache: den Hinweis der
Gratisfassungen.

## Der Hinweis der Gratisfassungen

Eine Erweiterung ohne gültige Lizenz zeigt auf der Website einen kurzen
Hinweis, für jede Erweiterung der Familie gleich. Das gemeinsame Skript
`bundles/neobasic/credit.js` von Basic zeichnet ihn; die Erweiterung legt fest,
wo (als letzten Teil ihres eigenen Elements, z. B. eines Fensters). Text, Logo
und Link sind Teil des Skripts, nicht eines Templates oder einer Sprachdatei,
und lassen sich nicht ändern.

### Wann er fehlt

Nur wenn der signierte Nachweis des Lizenzdienstes für die Website gilt. Das
wird auf dem Server und noch einmal im Browser des Besuchers geprüft, mit den
öffentlichen Schlüsseln, die in Basic eingebaut sind. Die Prüfung im Browser
braucht HTTPS (nur dort gibt es die Kryptografie-Schnittstelle Web Crypto):
Auf einer Seite ohne HTTPS bleibt der Hinweis auch mit Lizenz stehen.

### Wie er sichtbar bleibt

Der Hinweis steht in einem abgeschlossenen Bereich (Shadow DOM) auf einem
Element mit bei jedem Seitenaufruf neuem Namen. Seine Sichtbarkeit ist direkt
am Element festgelegt, sodass kein Stylesheet ihn ausblendet, und er wird
zurückgesetzt, wenn ein Skript ihn entfernt oder ändert.

Ist er abgeschnitten oder verdeckt, nimmt die Erweiterung keine Eingaben an,
bis er wieder sichtbar ist, und zeigt „Diese Funktion ist auf dieser Website
gerade nicht verfügbar.“ in der Sprache der Seite. Geprüft wird einmal pro
Sekunde; ein geschlossenes Element der Erweiterung (z. B. ein geschlossenes
Fenster) wird nicht geprüft.

Was im Layout der Website zu vermeiden ist:

- eine Höhenbegrenzung oder `overflow: hidden` am Element der Erweiterung, die
  seine letzte Zeile abschneidet,
- Elemente, die darüber liegen (feste Leisten, Cookie-Hinweise, Overlays),
- das Element so zu verschieben, dass sein Ende aus dem Fenster ragt, obwohl es
  hineinpassen würde.

### Farben

Der Hinweis nimmt seine Farben aus drei CSS-Variablen. Sie am Element der
Erweiterung oder weiter oben setzen, zum Beispiel an `:root`:

```css
:root {
    --neo-credit-ink: #1f2933;
    --neo-credit-bg: #f5f7fa;
    --neo-credit-line: #d1d9e0;
}
```

| Variable | Was sie festlegt | Vorgabe |
| --- | --- | --- |
| `--neo-credit-ink` | Text und Logo | `#4f5761` |
| `--neo-credit-bg` | Hintergrund | `#ffffff` |
| `--neo-credit-line` | Linie über dem Text | `#d1d9e0` |

Liegen Text und Hintergrund weniger als 4,5:1 auseinander oder ist eine der
beiden Farben durchsichtig, nimmt der Hinweis die festen Farben `#4f5761` auf
`#ffffff`.

## Adresse des Lizenzdienstes (Testinstallationen)

Die Adresse des Lizenzdienstes lässt sich für eine Testinstallation ändern:

```yaml
# config/config.yaml
neo_basic:
    license_url: 'https://licence-test.example.com'
```

Nachweise werden auch dann mit den in Basic eingebauten öffentlichen
Schlüsseln geprüft.

## Adresse von DeepL (Testinstallationen)

Ohne Einstellung gehen die Anfragen an DeepL bei einem Schlüssel des
kostenlosen Tarifs (Endung `:fx`) an `https://api-free.deepl.com`, sonst an
`https://api.deepl.com`. Eine Testinstallation kann sie stattdessen an einen
lokalen Ersatzdienst schicken:

```yaml
# config/config.yaml
neo_basic:
    deepl_url: 'http://127.0.0.1:8186'
```

Die Adresse steht ohne `/v2`; die Statuszeile des Abschnitts **DeepL** zeigt
die genutzte Adresse.

## Texte des Backends

Die Texte der Seite sind Contao-Sprachdateien in `contao/languages/<sprache>/`
von Basic (`modules.php`, `tl_neo_access.php`, `neo_license.php`), in elf
Sprachen. Einzelne Texte wie jede Contao-Sprachdatei überschreiben, in
`contao/languages/<sprache>/` des Projekts.
