---
titel: Rechte
kurzfassung: Wer „Zugänge und Lizenzen“ öffnen können soll und welche Contao-Rechte eine Benutzergruppe dafür braucht.
zielgruppe: betrieb
sprache: de
stand: 2026-11-12
---

# Rechte

## Wer den Zugang bekommen soll

*Zugänge und Lizenzen* enthält die Schlüssel fremder Dienste und die
Lizenzschlüssel der Installation. Wer die Seite öffnen darf, kann diese
Schlüssel ersetzen, mit ihnen die Verbindungen prüfen und eine Lizenz von der
Website entfernen.

Den Zugang nur Administratoren oder der Person geben, die die Website
einrichtet. Redakteure brauchen ihn nicht: Die Erweiterungen lesen ihre
Schlüssel von der Seite, ohne dass ein Redakteur sie je öffnet.

Administratoren haben den Zugang ohne weitere Einstellung.

## Was Contao braucht

*Zugänge und Lizenzen* ist ein eigenes Backend-Modul (`neo_access`). Alle
Rechte werden im Backend unter **Benutzergruppen** (oder **Benutzer**) gesetzt,
mit den Bezeichnungen, die Contao dort zeigt. Basic hat keine eigenen Schalter.

| Contao-Recht | Einstellung | Wofür |
| --- | --- | --- |
| **Backend-Module** | *Zugänge und Lizenzen*, in der Menügruppe der Erweiterungen | Die Seite öffnen. Ohne dieses Recht sieht der Benutzer weder den Menüeintrag noch die gleichnamige Schaltfläche über den Listen der Erweiterungen noch den Link in deren Warnungen. |
| **Erlaubte Felder**, Tabelle `tl_neo_access` | die Felder der Abschnitte, die die Gruppe sehen soll; für die Person, die die Website einrichtet, alle (Liste unten) | Contao zeigt nur erlaubte Felder. Ein Abschnitt ohne erlaubtes Feld fehlt. Ohne ein einziges erlaubtes Feld dieser Tabelle verweigert Contao die Seite. |
| **Tabellenrechte** (ab Contao 5.7), `tl_neo_access` | Bearbeiten | Die Seite öffnen und speichern, auch mit **Bestätigen** im Abschnitt *Bedingungen der Erweiterungen*. Ohne dieses Recht verweigert Contao schon das Öffnen. Anlegen und Löschen sind nicht nötig: Die Seite hat genau einen Datensatz, den die Erweiterung selbst anlegt. |

Wie bei Contaos eigenen Rechten bekommt ein Benutzer die Rechte des Benutzers,
die der Gruppen oder beide, je nach **Rechtevererbung**.

## Die Felder von `tl_neo_access`

Unter **Erlaubte Felder** führt Contao die Felder der Seite unter dem
Tabellennamen `tl_neo_access`, jedes mit seiner Bezeichnung und dem Feldnamen
in eckigen Klammern. Statuszeilen haben keine Bezeichnung; Contao führt sie nur
mit ihrem Feldnamen. Sie tragen die Schaltflächen **Verbindung prüfen** und
**Jetzt prüfen**: Ohne sie fehlen die Schaltflächen.

| Abschnitt | Felder |
| --- | --- |
| Lizenzen | `neo_license_intro` (wo der Schlüssel steht), je bezahlter Erweiterung: *Erweiterung*: Lizenzschlüssel `[license_…_key]` und `license_…_status` (Stand mit **Jetzt prüfen**) |
| KI-Verbindung | `ai_status` (Statuszeile mit **Verbindung prüfen**), Route `[ai_route]`, Adresse `[ai_base_url]`, Schlüssel `[ai_api_key]` |
| Azure Translator | `azure_translator_status` (Statuszeile mit **Verbindung prüfen**), Verarbeitung `[azure_translator_location]`, Adresse der Ressource `[azure_translator_url]`, Schlüssel `[azure_translator_key]`, Region `[azure_translator_region]` |
| DeepL | `deepl_status` (Statuszeile mit **Verbindung prüfen**), Schlüssel `[deepl_key]` |
| Bedingungen der Erweiterungen | je Erweiterung: `terms_…` (Passagen, Häkchen und **Bestätigen**) |
| Abschnitte der Erweiterungen | die Statuszeile `…_status` und die Schlüsselfelder des Abschnitts, mit den Bezeichnungen der Erweiterung |

Die Liste enthält nur die Felder der Erweiterungen, die gerade installiert
sind. Fügt eine neue Erweiterung einen Abschnitt hinzu, ihre Felder in der
Gruppe ergänzen.

## Beispielgruppe „Einrichtung der Website“

Für die Person, die die Website einrichtet und die Schlüssel einträgt:

1. **Backend-Module**: *Zugänge und Lizenzen*.
2. **Erlaubte Felder** → `tl_neo_access`: alle Felder.
3. **Tabellenrechte** (ab Contao 5.7): `tl_neo_access` → Bearbeiten.
4. Speichern.

Danach mit einem Testbenutzer der Gruppe anmelden und die Seite öffnen. Jeder
Abschnitt muss mit seiner Statuszeile und seinen Schaltflächen da sein. Fehlt
etwas, siehe [Fehlersuche](troubleshooting.md).

## Mehrere Websites in einer Installation

Die Seite hat einen Datensatz für die ganze Installation. Eine Trennung nach
Websites gibt es nicht: Wer sie öffnen darf, sieht und ändert die Schlüssel und
Lizenzen aller Websites der Installation. Eine Lizenz wird für die
Installation und die Domains aller ihrer Startpunkte geprüft (siehe
[Bedienung](usage.md#domains-und-installationen)).

## Einstellungen der Erweiterungen

Das Modell der KI-Verbindung und alle übrigen Einstellungen einer Erweiterung
stehen in den Einstellungen dieser Erweiterung, nicht auf dieser Seite. Welche
Rechte sie brauchen, steht in der Dokumentation der Erweiterung.
