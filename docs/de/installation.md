---
titel: Installation
kurzfassung: Voraussetzungen, Installation mit den Erweiterungen oder mit Composer, öffentliche Dateien, Aktualisieren und Entfernen von Basic.
zielgruppe: betrieb
sprache: de
stand: 2026-11-15
---

# Installation

Basic wird meist nicht eigens installiert: Jede Erweiterung, die darauf
aufbaut, verlangt es und installiert es automatisch mit. Von Hand nur
installieren, wenn es vor den Erweiterungen da sein soll.

## Voraussetzungen

| Was | Gebraucht für |
| --- | --- |
| Contao 5.3 bis 6.0 | alles |
| PHP 8.2 bis 8.5 (Contao 5.7 braucht 8.3, Contao 6.0 braucht 8.4) mit den Erweiterungen `sodium`, `openssl` und `json` | alles; `sodium` verschlüsselt die gespeicherten Schlüssel, `openssl` prüft den signierten Nachweis einer Lizenz |
| Ein Konto und ein API-Schlüssel bei einem OpenAI-kompatiblen Anbieter, z. B. [Requesty](https://www.requesty.ai/) oder [OpenAI](https://platform.openai.com/) | die KI-Verbindung, nur wenn eine installierte Erweiterung sie nutzt |
| Eine Azure-Translator-Ressource im Azure-Portal (Schlüssel und Region), ein Konto für die DeepL API (Free oder Pro) oder die KI-Verbindung | Übersetzungen, nur wenn eine installierte Erweiterung sie nutzt |
| Der Contao-Cron (Web-Cron oder der Befehl `contao:cron`) | die tägliche Lizenzprüfung bezahlter Erweiterungen |
| HTTPS auf der Website | das Weglassen des Hinweises einer lizenzierten Erweiterung (siehe [Anpassen](customising.md)) |

## Im Contao Manager

Basic steht nicht in der Liste des Contao Managers (Pakettyp `library`): Es
kommt mit den Erweiterungen, die es brauchen. Der Contao Manager installiert
es, sobald Sie eine davon hinzufügen, und aktualisiert es mit ihnen.

## Installation mit Composer

```bash
composer require neo-digital/contao-basic-bundle
php vendor/bin/contao-console contao:migrate
```

`contao:migrate` legt die Tabellen der Erweiterung an. Composer kopiert
außerdem die öffentliche Datei `credit.js` (der Hinweis der Gratisfassungen)
nach `public/bundles/neobasic/`. Ist das nicht geschehen, diesen Befehl
ausführen:

```bash
php vendor/bin/contao-console assets:install
```

## Nach der Installation

Die Seite *Zugänge und Lizenzen* gibt es nur, wenn eine installierte
Erweiterung ihr einen Abschnitt hinzufügt. Mit Basic allein erscheinen weder
die Seite noch ihr Menüeintrag. Sobald eine Erweiterung installiert ist, weiter
mit der [Einrichtung](setup.md).

Erweiterungen, die ihre Schlüssel früher selbst gespeichert haben, übernehmen
sie bei der ersten Datenbank-Aktualisierung einmal auf die Seite. Was auf der
Seite schon gespeichert ist, wird nie überschrieben, und ein später entfernter
Schlüssel kommt nicht zurück.

## Backend-Sprachen

Das Backend von Basic gibt es auf Englisch, Deutsch, Französisch,
Niederländisch, Italienisch, Polnisch, Tschechisch, Ungarisch, Slowenisch,
Kroatisch und Spanisch. Ungarisch und Kroatisch bietet Contao als
Backend-Sprache erst an, wenn sie in `config/config.yaml` freigeschaltet sind:

```yaml
contao:
    intl:
        enabled_locales: ['+hu', '+hr']
```

## Aktualisieren

1. Das Paket mit dem Contao Manager aktualisieren oder mit
   `composer update neo-digital/contao-basic-bundle`.
2. Die Datenbank aktualisieren (`contao:migrate`).
3. `CHANGELOG.md` der neuen Version lesen. Braucht eine Version neue Rechte
   oder einen Schritt nach dem Aktualisieren, steht das dort.

## Entfernen

Solange eine installierte Erweiterung Basic verlangt, bleibt es installiert.
Diese Erweiterungen zuerst entfernen.

1. Vor dem Entfernen: unter *Zugänge und Lizenzen* den Lizenzschlüssel jeder
   bezahlten Erweiterung leeren und speichern. Das entfernt die Lizenz von der
   Website und meldet es dem Lizenzdienst.
2. Das Paket mit dem Contao Manager entfernen oder mit
   `composer remove neo-digital/contao-basic-bundle`.
3. Die Datenbank aktualisieren. Contao bietet dann an, die Tabellen der
   Erweiterung zu löschen: die gespeicherten Schlüssel, die Lizenzen und den
   Nachweis der bestätigten Bedingungen. Nur löschen, wenn die Erweiterung
   nicht wieder installiert werden soll und der Nachweis der Bestätigungen
   nicht mehr gebraucht wird.
