---
titel: Fehlersuche
kurzfassung: Die Meldungen von „Zugänge und Lizenzen“ und häufige Probleme mit Basic – Anzeichen, Ursache und Lösung.
zielgruppe: betrieb
sprache: de
stand: 2026-11-12
---

# Fehlersuche

Die Statuszeilen der Seite und das Ergebnis von **Verbindung prüfen** nennen
die meisten Ursachen selbst. Die Tabellen unten führen jede Meldung mit ihrer
Ursache auf. Fehlende Abschnitte, Felder oder Schaltflächen sind fast immer
fehlende Rechte; siehe [Rechte](permissions.md).

## Die Seite

| Anzeichen | Ursache | Lösung |
| --- | --- | --- |
| Kein Menüeintrag *Zugänge und Lizenzen* | Keine installierte Erweiterung fügt der Seite einen Abschnitt hinzu; dann gibt es die Seite nicht. Oder dem Benutzer fehlt das Backend-Modul. | Prüfen, ob die Erweiterung installiert ist, die die Seite braucht. **Backend-Module** → *Zugänge und Lizenzen* erlauben. |
| Die Adresse der Seite führt auf die Startseite des Backends | Keine installierte Erweiterung fügt einen Abschnitt hinzu | So vorgesehen. |
| Contao verweigert die Seite (Zugriff verweigert) | Die Gruppe hat kein erlaubtes Feld von `tl_neo_access`, oder ab Contao 5.7 fehlt das Tabellenrecht | **Erlaubte Felder** → `tl_neo_access`; **Tabellenrechte** → `tl_neo_access` → Bearbeiten. |
| Einem Benutzer fehlt ein Abschnitt oder ein Feld | Das Feld steht nicht unter **Erlaubte Felder** | Es ergänzen ([Liste der Felder](permissions.md#die-felder-von-tl_neo_access)). |
| Keine Schaltfläche **Verbindung prüfen** oder **Jetzt prüfen** | Die Statuszeile des Abschnitts steht nicht unter **Erlaubte Felder**. **Jetzt prüfen** erscheint außerdem erst, wenn ein Lizenzschlüssel gespeichert ist. | `ai_status`, `azure_translator_status`, `deepl_status` oder `license_…_status` ergänzen. |
| Ein Abschnitt ist zugeklappt | So vorgesehen: In ihm ist nichts zu tun | Auf seinen Titel klicken, um ihn zu öffnen. |
| **Verbindung prüfen** ist gesperrt: „Kann geprüft werden, sobald der Zugang gespeichert ist.“ | Kein Schlüssel (bei eigener Adresse: keine Adresse) gespeichert | Eintragen und speichern. |
| Nach **Verbindung prüfen** oder **Jetzt prüfen**: „Das hat nicht geklappt. Bitte laden Sie die Seite neu und versuchen Sie es noch einmal.“ | Die Antwort kam nicht an, z. B. weil die Sitzung abgelaufen ist | Die Seite neu laden (falls verlangt, neu anmelden) und erneut klicken. |
| Nach **Verbindung prüfen** oder **Jetzt prüfen**: „Die Seite ist nicht mehr aktuell. Bitte laden Sie sie neu und versuchen Sie es noch einmal.“ | Das Anfrage-Token der Seite gilt nicht mehr, z. B. nach langer Zeit auf der Seite | Die Seite neu laden und erneut klicken. |
| Ohne JavaScript führt **Verbindung prüfen** oder **Jetzt prüfen** auf Contaos Bestätigungsseite für ein ungültiges Anfrage-Token | Der Link ist veraltet | Zurückgehen, die Seite neu laden und erneut klicken. |
| Ein Schlüsselfeld zeigt acht Punkte | So vorgesehen: Ein Schlüssel ist gespeichert und wird nie wieder angezeigt | Das Feld so lassen, um den Schlüssel zu behalten. |
| Eine Erweiterung meldet einen fehlenden Schlüssel, ohne Link | Der Benutzer darf *Zugänge und Lizenzen* nicht öffnen | Einen Administrator bitten, den Schlüssel einzutragen. |
| Die Statuszeile sagt „Schlüssel gespeichert (verschlüsselt)“, aber **Verbindung prüfen** antwortet „Noch kein Schlüssel gespeichert.“ | Das App-Secret der Installation (`APP_SECRET`) wurde geändert; gespeicherte Schlüssel lassen sich nicht mehr entschlüsseln | Die Schlüssel neu eintragen. |

## Statuszeilen der Abschnitte

| Anzeichen | Ursache | Lösung |
| --- | --- | --- |
| KI-Verbindung: „Noch kein Schlüssel gespeichert.“ | Das Feld **Schlüssel** ist leer | Den API-Schlüssel eintragen und speichern. |
| KI-Verbindung: „Die Route „Eigene Adresse“ braucht eine Adresse.“ | Route **Eigene Adresse (OpenAI-kompatibel)** ohne Adresse | Die **Adresse** eintragen, z. B. `https://example.com/v1`. |
| Azure Translator: „Noch kein Schlüssel gespeichert. Wird nur für Übersetzungen gebraucht.“ | Kein Schlüssel gespeichert; nur Übersetzungen brauchen ihn | Schlüssel und Region eintragen, wenn eine Erweiterung Texte übersetzt. |
| Azure Translator: „Die Verarbeitung „Eigene Adresse der Ressource“ braucht eine Adresse.“ | **Eigene Adresse der Ressource** ohne Adresse | Die **Adresse der Ressource** aus dem Azure-Portal eintragen. |
| DeepL: „Noch kein Schlüssel gespeichert. Wird nur für Übersetzungen gebraucht.“ | Kein Schlüssel gespeichert; nur Übersetzungen brauchen ihn | Den Schlüssel eintragen, wenn eine Erweiterung Texte mit DeepL übersetzt. |
| Abschnitt einer Erweiterung: „Noch nicht hinterlegt.“ | Ein Schlüssel, den die Erweiterung braucht, fehlt | Ihn eintragen; woher er kommt, steht in der Dokumentation der Erweiterung. |
| Abschnitt einer Erweiterung: „Nicht hinterlegt. Wird nur für einzelne Funktionen gebraucht.“ (oder ein Text der Erweiterung) | Ein optionaler Abschnitt ist leer | Nichts zu tun, außer die Funktion wird gebraucht. |

## Verbindung prüfen

| Anzeichen | Ursache | Lösung |
| --- | --- | --- |
| „Noch kein Schlüssel gespeichert.“ | Kein Schlüssel gespeichert | Den Schlüssel eintragen, auf **Speichern** klicken, erneut prüfen. |
| „Keine Verbindung: …“ | Der Server erreicht die Adresse nicht; der Text nach dem Doppelpunkt ist der technische Grund | Die Adresse prüfen; der Server muss ausgehende Verbindungen dorthin aufbauen dürfen. |
| KI-Verbindung: „Die Route antwortet mit HTTP 401 (nach … ms). Antwort:“ | Der Anbieter lehnt den Schlüssel ab | Den Schlüssel neu eintragen; ihn im Konto beim Anbieter prüfen. |
| KI-Verbindung: „Die Route antwortet mit HTTP …“ mit einem anderen Status | Die Antwort unter der Zeile nennt den Grund | Die Antwort lesen; Route und Adresse prüfen. |
| Azure: „Azure antwortet mit HTTP 401 (nach … ms). Antwort:“ | Der Schlüssel ist falsch | Schlüssel 1 oder 2 der Ressource neu eintragen. |
| Azure: „Azure antwortet mit HTTP 403 (nach … ms). Antwort:“ | Oft passt die **Region** nicht zur Ressource | Die Region eintragen, die das Azure-Portal für die Ressource zeigt. |
| DeepL: „DeepL antwortet mit HTTP 403 (nach … ms). Der Schlüssel wird nicht angenommen.“ | Der Schlüssel ist falsch oder wurde im DeepL-Konto gelöscht | Den Schlüssel neu aus dem DeepL-Konto kopieren (**API-Schlüssel**). |
| DeepL: „DeepL antwortet mit HTTP 456 (nach … ms). Die Zeichen dieses Abrechnungszeitraums sind aufgebraucht.“ | Die Zeichengrenze des Tarifs ist erreicht | Den nächsten Abrechnungszeitraum abwarten, die Grenze erhöhen oder zu Pro wechseln. |
| DeepL: Die Statuszeile nennt „DeepL API Free“, obwohl Sie Pro haben (oder umgekehrt) | Free oder Pro ergibt sich aus dem Schlüssel: Schlüssel des kostenlosen Tarifs enden auf `:fx` | Den Schlüssel des gewünschten Tarifs eintragen. |
| Die Prüfung zeigt alte Werte | Die Prüfung nutzt das Gespeicherte | Speichern, dann erneut prüfen. |

## Lizenzen

| Anzeichen | Ursache | Lösung |
| --- | --- | --- |
| „Ohne Lizenz.“ | Kein Lizenzschlüssel gespeichert | Den Schlüssel aus der Bestellbestätigung eintragen. |
| „Dieser Lizenzschlüssel gilt nicht für diese Erweiterung.“ (am Feld, nach dem Speichern) | Der Lizenzdienst kennt den Schlüssel für diese Erweiterung nicht; der Schlüssel wird nicht behalten | Den Schlüssel prüfen und ob er im Feld der richtigen Erweiterung steht. |
| „Der Lizenzschlüssel ist gespeichert, aber noch nicht bestätigt.“ | Der Lizenzdienst hat den Schlüssel noch nicht bestätigt, z. B. weil er beim Speichern nicht erreichbar war | Auf **Jetzt prüfen** klicken. |
| **Jetzt prüfen** zeigt einige Sekunden „Wird geprüft …“ | Der Lizenzdienst wird gefragt; ohne Antwort gibt die Prüfung nach höchstens 15 Sekunden auf | Das Ergebnis in der Zeile abwarten. |
| „Der Lizenzdienst war bei der letzten Prüfung nicht erreichbar. Neuer Versuch automatisch in einer Stunde oder mit „Jetzt prüfen“.“ | Netzwerkproblem, oder der Lizenzdienst war nicht verfügbar | Abwarten oder auf **Jetzt prüfen** klicken. Der Server muss ausgehende HTTPS-Verbindungen aufbauen dürfen. |
| „Der Nachweis des Lizenzdienstes lässt sich mit dieser Fassung der Erweiterung nicht prüfen.“ | Der signierte Nachweis passt nicht zu den öffentlichen Schlüsseln, die in diese Fassung von Basic eingebaut sind | Basic aktualisieren. In einer Testinstallation mit anderer Adresse des Lizenzdienstes ([Anpassen](customising.md)) werden Nachweise trotzdem mit den eingebauten Schlüsseln geprüft. |
| „Der Nachweis gehört zu einer anderen Erweiterung.“ | Der Schlüssel einer anderen Erweiterung wurde eingetragen | Den Schlüssel dieser Erweiterung eintragen. |
| „Die Lizenz ist abgelaufen.“ | Die Lizenz ist abgelaufen, samt Schonfrist | Die Lizenz verlängern, dann **Jetzt prüfen**. |
| „Die Lizenz wurde zurückgezogen.“ | Die Lizenz wurde zurückgezogen | An den Verkäufer der Lizenz wenden. |
| „Lizenz … am … abgelaufen. Ohne Verlängerung gilt sie noch bis …“ | Der bezahlte Zeitraum ist vorbei; die Schonfrist läuft | Die Lizenz vor dem Datum verlängern. |
| „Nicht von der Lizenz gedeckt: ….“ | Eine Domain der Website ist nicht lizenziert | Die Domain in die Lizenz aufnehmen lassen oder den **Domainnamen** der Startpunkte prüfen. |
| Bei einer Domain: „Diese Domain ist in der Lizenz nicht eingetragen. …“ | Die Domain gehört nicht zur Lizenz | Im Kundenbereich eintragen oder zusätzliche Domains buchen, dann **Jetzt prüfen**. |
| Rote Warnung „Kann nicht aktiviert werden: Die Zahl der Installationen dieser Lizenz ist überschritten. …“, bei der Domain „Alle Installationen der Lizenz sind belegt.“ | Die Lizenz ist schon in allen Installationen aktiv, die sie erlaubt (standardmäßig eine); eine Installation, die sich 35 Tage nicht gemeldet hat, zählt nicht mehr | Eine Installation im Kundenbereich freigeben, oder über den Link in der Warnung beim Hersteller mehr Installationen anfragen; dann **Jetzt prüfen**. |
| Bei einer Domain: „Diese Installation nutzt mehr Domains, als die Lizenz erlaubt. …“ | Mehr Startpunkte mit eigener Domain, als die Lizenz je Installation erlaubt | Zusätzliche Domains je Installation buchen oder den **Domainnamen** der Startpunkte prüfen. |

Technische Einzelheiten einer gescheiterten Lizenzprüfung zeigt die Seite
nicht. Basic schreibt sie als Warnung in das Protokoll der Installation
(`var/logs/`); ob Warnungen dort aufbewahrt werden, hängt von den
Protokoll-Einstellungen der Installation ab.

## Hinweis auf der Website

| Anzeichen | Ursache | Lösung |
| --- | --- | --- |
| Der Hinweis bleibt, obwohl die Erweiterung lizenziert ist | Die Seite wird ohne HTTPS ausgeliefert; die Prüfung im Browser braucht HTTPS | Die Website über HTTPS ausliefern. |
| Der Hinweis bleibt nur auf manchen Domains der Website | Die Lizenz gilt nicht für diese Domains | Siehe „Nicht von der Lizenz gedeckt“ oben. |
| Die Erweiterung nimmt keine Eingaben an und meldet „Diese Funktion ist auf dieser Website gerade nicht verfügbar.“ | Der Hinweis ist abgeschnitten oder verdeckt, z. B. durch eine Höhenbegrenzung, verborgenen Überlauf oder ein Element darüber | Den Hinweis sichtbar machen; siehe [Anpassen](customising.md). |

## Bedingungen der Erweiterungen

| Anzeichen | Ursache | Lösung |
| --- | --- | --- |
| „Noch nicht bestätigt.“ | Die Bedingungen der Erweiterung wurden nie bestätigt | Lesen, anhaken, **Bestätigen**. |
| „Die Bedingungen wurden geändert. Bitte bestätigen Sie die markierten Passagen erneut.“ | Eine Passage hat eine neue Fassung, oder eine höhere Stufe wurde lizenziert | Die mit **neu** oder **geändert** markierten Passagen bestätigen. |
| „Bitte setzen Sie zuerst das Häkchen, um die Bedingungen zu bestätigen.“ | Auf **Bestätigen** geklickt, ohne das Häkchen | **Ich habe die Bedingungen gelesen und akzeptiere sie.** anhaken, dann **Bestätigen**. |
| Eine Erweiterung zeigt eine Warnung mit **Jetzt bestätigen** | Bedingungen sind noch zu bestätigen | Dem Link folgen und bestätigen. |
| Kein Abschnitt *Bedingungen der Erweiterungen* | Keine installierte Erweiterung hat Bedingungen, oder `terms_…` steht nicht unter **Erlaubte Felder** | Das Feld ergänzen, wenn die Gruppe Bedingungen bestätigen soll. |

## Noch nicht gelöst?

1. Mit einem Administrator anmelden. Funktioniert dort alles, liegt es an einem
   Recht; die Gruppe mit [Rechte](permissions.md) vergleichen.
2. Im **System-Log** des Backends und in `var/logs/` nachsehen.
