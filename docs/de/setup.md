---
titel: Einrichtung
kurzfassung: Die Lizenzschlüssel, die KI-Verbindung, die Zugänge zu Azure Translator und DeepL und die Schlüssel der Erweiterungen unter „Zugänge und Lizenzen“ eintragen, die Verbindungen prüfen und die Bedingungen bestätigen.
zielgruppe: betrieb
sprache: de
stand: 2026-11-13
---

# Einrichtung

Eingerichtet wird alles auf einer Backend-Seite, *Zugänge und Lizenzen*. Die
Schritte in dieser Reihenfolge ausführen und jeden Abschnitt überspringen, den
die Seite nicht zeigt: Sie zeigt nur, was die installierten Erweiterungen
brauchen.

## 1. Die Seite öffnen

1. Im Backend die Menügruppe der Erweiterungen öffnen und auf **Zugänge und
   Lizenzen** klicken. Es ist immer der letzte Eintrag der Gruppe. Manche
   Erweiterungen zeigen außerdem eine gleichnamige Schaltfläche über ihrer
   Liste.

![Die Seite „Zugänge und Lizenzen“. Rahmen 1 markiert den Abschnitt „Lizenzen“ mit einer Zeile je bezahlter Erweiterung (links das Feld für den Schlüssel, rechts der Stand); Rahmen 2 den Abschnitt „Bedingungen der Erweiterungen“; Rahmen 3 den Abschnitt „KI-Verbindung“; Rahmen 4 die Abschnitte „Azure Translator“ und „DeepL“; Rahmen 5 die Abschnitte der Erweiterungen.](bilder/access-page.png)

- Rahmen 1: **Lizenzen**, die Lizenzschlüssel der bezahlten Erweiterungen
  (Schritt 2).
- Rahmen 2: **Bedingungen der Erweiterungen** (Schritt 3).
- Rahmen 3: **KI-Verbindung** (Schritt 4).
- Rahmen 4: die Übersetzungsdienste **Azure Translator** und **DeepL**
  (Schritt 5).
- Rahmen 5: die Abschnitte der Erweiterungen, z. B. der Schlüssel eines
  Such- oder Messdienstes (Schritt 6).

Ein Abschnitt ohne offene Aufgabe ist zugeklappt; ein Klick auf seinen Titel
öffnet ihn. Ein Abschnitt, in dem etwas fehlt, ist offen. Jeder Abschnitt
beginnt mit einer Statuszeile, die sagt, was gespeichert ist und was fehlt,
und die Erweiterungen nennt, die den Abschnitt nutzen; die
[Bedienung](usage.md) erklärt sie.

## 2. Lizenzen (bezahlte Erweiterungen)

Der Abschnitt **Lizenzen** hat eine Zeile je bezahlter Erweiterung: links das
Feld ***Erweiterung*: Lizenzschlüssel**, rechts ihr Stand.

1. Den Schlüssel aus der Bestellbestätigung in das Feld der Erweiterung
   eintragen. Leerzeichen und Kleinbuchstaben im Schlüssel spielen keine Rolle.
2. Auf **Speichern** klicken.

Beim Speichern wird der Schlüssel beim Lizenzdienst aktiviert. Einen
Schlüssel, den der Lizenzdienst für diese Erweiterung nicht kennt, behält die
Seite nicht; das Feld zeigt dann „Dieser Lizenzschlüssel gilt nicht für diese
Erweiterung.“ Ohne Schlüssel läuft die Erweiterung als Gratisfassung.

Lizenzschlüssel gibt es im Store, <https://store.neo-digital.at>. Die
Statuszeile jeder Zeile endet mit dem Link **Lizenzschlüssel im Store
kaufen**; er öffnet die Seite dieser Erweiterung, die auch zeigt, was jede
Stufe umfasst.

## 3. Bedingungen der Erweiterungen

Der Abschnitt **Bedingungen der Erweiterungen** erscheint, wenn eine
installierte Erweiterung Bedingungen zum Bestätigen hat. Er ist offen, solange
etwas zu bestätigen ist.

1. Jede Passage mit **Text anzeigen** öffnen und lesen.
2. **Ich habe die Bedingungen gelesen und akzeptiere sie.** ankreuzen.
3. Auf **Bestätigen** klicken.

Einzelheiten stehen in der [Bedienung](usage.md#bedingungen-der-erweiterungen).

## 4. KI-Verbindung

Die KI-Verbindung nutzt jede installierte Erweiterung, die mit einem
Sprachmodell arbeitet.

1. Abschnitt **KI-Verbindung** → **Route**: wählen, über welchen Anbieter die
   Erweiterungen das Sprachmodell erreichen.
   - **Requesty, EU-Route (Verarbeitung in der EU)** (Vorgabe)
   - **Requesty, Standard-Route (weltweit)**
   - **OpenAI (direkt)**
   - **Eigene Adresse (OpenAI-kompatibel)**: dann unter **Adresse** die
     Adresse der Schnittstelle eintragen, zum Beispiel `https://example.com/v1`.
2. **Schlüssel**: den API-Schlüssel aus dem Konto beim Anbieter eintragen.

Das Modell wird hier nicht gewählt. Jede Erweiterung wählt es in ihren eigenen
Einstellungen, aus den Modellen, die die Route anbietet.

## 5. Übersetzungsdienste (nur wenn eine Erweiterung Texte übersetzt)

Eine Erweiterung, die Texte übersetzt, nutzt Azure Translator, DeepL oder ein
Sprachmodell der KI-Verbindung; welchen Dienst, wird in den Einstellungen der
Erweiterung gewählt. Den gewünschten Dienst einrichten. Ohne Schlüssel zeigen
beide Abschnitte einen Hinweis, keinen Fehler.

### Azure Translator

Sie brauchen eine *Translator*-Ressource im Azure-Portal. Schlüssel und Region
stehen unter Ressource → **Schlüssel und Endpunkt**.

1. Abschnitt **Azure Translator** → **Verarbeitung**: wählen, wo Azure die
   Texte übersetzt.
   - **Europa (Frankreich oder Westeuropa)** (Vorgabe)
   - **Weltweit (nächstes Rechenzentrum)**
   - **Amerika**
   - **Asien-Pazifik**
   - **Eigene Adresse der Ressource**: dann unter **Adresse der Ressource** die
     Adresse aus dem Azure-Portal eintragen, zum Beispiel
     `https://name.cognitiveservices.azure.com/translator/text/v3.0`. Nötig bei
     eingeschränktem Netzwerkzugang oder für die Verarbeitung in der Schweiz.
2. **Schlüssel**: Schlüssel 1 oder Schlüssel 2 der Ressource.
3. **Region**: der Standort der Ressource, zum Beispiel `westeurope`. Bei einer
   globalen Ressource leer lassen.

### DeepL

Sie brauchen ein Konto für die DeepL API (Free oder Pro). Der Schlüssel steht
im DeepL-Konto unter **API-Schlüssel**.

1. Abschnitt **DeepL** → **Schlüssel**: den Authentifizierungsschlüssel
   eintragen.

Free oder Pro ergibt sich aus dem Schlüssel: Schlüssel des kostenlosen Tarifs
enden auf `:fx` und gehen an `api-free.deepl.com`, alle anderen an
`api.deepl.com`. Die Statuszeile nennt die genutzte Schnittstelle.

## 6. Abschnitte der Erweiterungen

Weitere Abschnitte enthalten die Schlüssel, die eine installierte Erweiterung
braucht, zum Beispiel für einen Such- oder Messdienst. Woher ein Schlüssel
kommt, steht in der Dokumentation der jeweiligen Erweiterung. Ein Abschnitt,
der als nur für einzelne Funktionen nötig markiert ist, darf leer bleiben.

## 7. Speichern und die Verbindungen prüfen

1. Auf **Speichern** klicken.
2. Im Abschnitt **KI-Verbindung** auf **Verbindung prüfen** klicken. Geprüft
   wird, was gespeichert ist. Die Seite bleibt, wo sie ist: Was Sie getippt und
   noch nicht gespeichert haben, bleibt in den Feldern, wird aber nicht
   geprüft.
3. Das Ergebnis unter der Schaltfläche lesen.

![Der Abschnitt „KI-Verbindung“ nach „Verbindung prüfen“. Rahmen 1 markiert die Schaltfläche; Rahmen 2 das Ergebnis darunter: „Verbindung in Ordnung: HTTP 200 in 1 ms, 3 Modelle verfügbar.“ und die Zeile mit der abgefragten Adresse und dem Schlüssel. Der Wert des Schlüssels ist in diesem Bild abgedeckt.](bilder/test-connection.png)

- Rahmen 1: die Schaltfläche **Verbindung prüfen**. Während der Prüfung steht
  darauf „Wird geprüft …“, und sie lässt sich nicht klicken.
- Rahmen 2: das Ergebnis: ob die Verbindung funktioniert, der HTTP-Status, die
  Dauer und bei der KI-Verbindung die Zahl der Modelle, die die Route
  anbietet; darunter die Adresse, die der Server abgefragt hat, und der
  Schlüssel, nur als Länge, erste vier und letzte zwei Zeichen.

Danach die Prüfung in den Abschnitten **Azure Translator** und **DeepL** genauso
ausführen. Die Prüfung von Azure Translator übersetzt ein Wort; die Prüfung von
DeepL fragt die im aktuellen Abrechnungszeitraum verbrauchten Zeichen ab und
übersetzt nichts.

Schlägt die Prüfung fehl, nennt die [Fehlersuche](troubleshooting.md) jedes
Ergebnis mit seiner Ursache.

## 8. Tägliche Lizenzprüfung

Sicherstellen, dass der Contao-Cron läuft (Web-Cron oder der Befehl
`contao:cron`, vom Planer des Servers gestartet). Die Lizenz jeder bezahlten
Erweiterung wird einmal am Tag neu geprüft; ohne Cron nur, wenn jemand den
Schlüssel speichert oder auf **Jetzt prüfen** klickt.

## 9. Wer die Seite öffnen darf

Die Seite enthält Schlüssel. Den Zugang nur Administratoren oder der Person
geben, die die Website einrichtet; die [Rechte](permissions.md) nennen, was
Contao dafür braucht.
