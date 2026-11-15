---
titel: Datenschutz
kurzfassung: Was Basic speichert, was an den Lizenzdienst, den KI-Anbieter, Azure Translator und DeepL geht und was der Hinweis im Browser der Besucher tut.
zielgruppe: betrieb
sprache: de
stand: 2026-11-12
---

# Datenschutz

Basic selbst verarbeitet keine Daten von Besuchern. Es speichert die Schlüssel
und Lizenzen der Installation und die Bestätigungen der Bedingungen, und es
spricht mit den Diensten, die der Administrator einrichtet. Welche Daten eine
Erweiterung an den KI-Anbieter, an Azure Translator oder an DeepL schickt, steht
in der Dokumentation dieser Erweiterung.

## Was gespeichert wird

| Was | Wo | Wie lange |
| --- | --- | --- |
| Route und Adresse der KI-Verbindung, Ort der Verarbeitung, Adresse und Region des Zugangs zu Azure Translator, der Schlüssel von DeepL, die Schlüssel der Erweiterungen; alle Schlüssel verschlüsselt (libsodium, Schlüssel aus dem App-Secret) | Tabelle `tl_neo_access`, ein Datensatz | bis sie ersetzt werden |
| Lizenzschlüssel jeder bezahlten Erweiterung (verschlüsselt), der letzte signierte Nachweis des Lizenzdienstes, die gemeldeten Domains, je Domain, ob sie abgedeckt ist und warum nicht, die belegten Installationen der Lizenz, der Zeitpunkt der letzten Prüfung, der letzte Fehler | Tabelle `tl_neo_license`, eine Zeile je Erweiterung | bis der Lizenzschlüssel geleert und gespeichert wird |
| Kennung dieser Installation (32 zufällige Zeichen, nicht aus einer Domain oder dem Server abgeleitet) und die Adresse, die für Startpunkte ohne Domainnamen steht | Datensatz der Seite in `tl_neo_access` | solange die Installation besteht; nicht angezeigt, nicht protokolliert |
| Bestätigungen der Bedingungen: Erweiterung, Passage, Fassung, Benutzername und Benutzer-ID, Zeitpunkt, Sprache, in der der Text angezeigt wurde | Tabelle `tl_neo_terms` | alle, als Nachweis; das Backend bietet keine Möglichkeit, sie zu löschen |
| Die Modellliste der Route | Zwischenspeicher (Cache) der Anwendung | eine Stunde |

## Was an fremde Dienste geht

| Funktion | Dienst | Was gesendet wird | Wann |
| --- | --- | --- | --- |
| Lizenzprüfung | der Lizenzdienst | der Lizenzschlüssel, der Code der Erweiterung, die Kennung der Installation, die Domains der Website, das System (Contao) mit seiner Version und die Versionen von Erweiterung und PHP; keine Daten von Besuchern | beim Speichern eines Schlüssels, einmal täglich, mit **Jetzt prüfen** und beim Entfernen eines Schlüssels (Schlüssel, Kennung der Installation und Domains) |
| KI-Verbindung | der Anbieter der gewählten Route | **Verbindung prüfen** und Modellliste: eine Abfrage der Liste der Modelle, mit dem Schlüssel | beim Klick auf **Verbindung prüfen** und wenn eine Erweiterung ihr Feld für das Modell zeigt (höchstens einmal pro Stunde) |
| KI in den Erweiterungen | der Anbieter der gewählten Route | steht in der Dokumentation jeder Erweiterung | – |
| Azure Translator | Microsoft Azure Translator, verarbeitet am gewählten Ort (Vorgabe Europa) | nur die Texte, die eine Erweiterung übersetzen lässt, und die beiden Sprachkürzel; **Verbindung prüfen** schickt ein Wort | wenn eine Erweiterung übersetzt; beim Klick auf **Verbindung prüfen** |
| DeepL | DeepL SE (Deutschland), DeepL API Free oder Pro je nach Schlüssel | nur die Texte, die eine Erweiterung übersetzen lässt, und die beiden Sprachkürzel; **Verbindung prüfen** schickt keinen Text, sie fragt die verbrauchten Zeichen ab | wenn eine Erweiterung übersetzt; beim Klick auf **Verbindung prüfen** |
| Übersetzung durch ein KI-Modell | der Anbieter der gewählten Route, mit dem Modell, das die Erweiterung wählt | nur die Texte, die eine Erweiterung übersetzen lässt, die beiden Sprachkürzel und feste Anweisungen für die Übersetzung | wenn eine Erweiterung mit dem KI-Modell übersetzt |

## Der Hinweis im Browser der Besucher

Die Gratisfassungen der Erweiterungen zeigen auf der Website einen Hinweis. Das
Skript, das ihn zeichnet, wird von der Website selbst geladen
(`bundles/neobasic/credit.js`). Es prüft den signierten Nachweis einer Lizenz
im Browser des Besuchers, lädt nichts von anderen Servern und sendet nichts.
Der Hinweis ist ein Link zum Hersteller der Erweiterungen; nur wer ihn
anklickt, verlässt die Website.

## Was der Betreiber der Website tut

Den KI-Anbieter, Microsoft Azure und DeepL in der Datenschutzerklärung der
Website nennen, wenn eine installierte Erweiterung Daten von Besuchern an sie
schickt; was sie schickt, steht in der Dokumentation der Erweiterung. Bei DeepL
API Free darf DeepL die Texte für eine begrenzte Zeit nutzen, um seinen Dienst
zu trainieren und zu verbessern; bei DeepL API Pro werden die Texte nach der
Übersetzung gelöscht und nicht zur Verbesserung genutzt (Datenschutzerklärung
von DeepL, Abschnitte zu den kostenlosen Diensten, zu DeepL API Free und zu
DeepL Pro). Für Texte, die unter einem Auftragsverarbeitungsvertrag bleiben
müssen, DeepL API Pro, Azure Translator oder einen KI-Anbieter mit einem
solchen Vertrag nutzen.
