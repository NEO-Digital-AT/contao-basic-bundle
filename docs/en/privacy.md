---
title: Privacy
summary: What Basic stores, what goes to the licence service, the AI provider, Azure Translator and DeepL, and what the credit line does in the visitor's browser.
audience: operations
language: en
updated: 2026-11-12
---

# Privacy

Basic itself processes no visitor data. It stores the keys and licences of the
installation and the confirmations of the terms, and it talks to the services
the administrator sets up. Which data an extension sends to the AI provider,
to Azure Translator or to DeepL is described in the documentation of that
extension.

## What is stored

| What | Where | How long |
| --- | --- | --- |
| Route and address of the AI connection, processing location, address and region of the Azure Translator access, the key of DeepL, the keys of the extensions; all keys encrypted (libsodium, keyed by the app secret) | table `tl_neo_access`, one record | until replaced |
| Licence key of each paid extension (encrypted), the last signed confirmation of the licence service, the domains reported, per domain whether it is covered and why not, the installations of the licence in use, the time of the last check, the last error | table `tl_neo_license`, one row per extension | until the licence key is emptied and saved |
| Identifier of this installation (32 random characters, not derived from a domain or the server) and the address that stands for root pages without a domain name | record of the page in `tl_neo_access` | as long as the installation exists; not shown, not logged |
| Confirmations of the terms: extension, passage, version, user name and user ID, time, language the text was shown in | table `tl_neo_terms` | all of them, as a record; the back end offers no way to delete them |
| The model list of the route | Contao's application cache | one hour |

## What goes to external services

| Function | Service | What is sent | When |
| --- | --- | --- | --- |
| Licence check | the licence service | the licence key, the code of the extension, the identifier of the installation, the domains of the website, the system (Contao) with its version and the versions of the extension and PHP; no visitor data | when a key is saved, once a day, with **Check now**, and when a key is removed (key, identifier of the installation and domains) |
| AI connection | the provider of the chosen route | **Test connection** and the model list: a request for the list of models, with the key | when **Test connection** is clicked, and when an extension shows its model field (at most once an hour) |
| AI in the extensions | the provider of the chosen route | described in the documentation of each extension | – |
| Azure Translator | Microsoft Azure Translator, processed at the chosen location (default Europe) | only the texts an extension asks to translate and the two language codes; **Test connection** sends one word | when an extension translates; when **Test connection** is clicked |
| DeepL | DeepL SE (Germany), DeepL API Free or Pro according to the key | only the texts an extension asks to translate and the two language codes; **Test connection** sends no text, it asks for the characters used | when an extension translates; when **Test connection** is clicked |
| Translation by an AI model | the provider of the chosen route, with the model the extension chooses | only the texts an extension asks to translate, the two language codes and fixed instructions for the translation | when an extension translates with the AI model |

## The credit line in the visitor's browser

The free editions of the extensions show a credit line on the website. The
script that draws it is loaded from the website itself
(`bundles/neobasic/credit.js`). It checks the signed confirmation of a licence
in the visitor's browser, loads nothing from other servers and sends nothing.
The line is a link to the maker of the extensions; only a visitor who clicks it
leaves the website.

## What the website operator does

Name the AI provider, Microsoft Azure and DeepL in the privacy policy of the
website where an installed extension sends visitor data to them; the
documentation of the extension says what it sends. With DeepL API Free,
DeepL may use the texts for a limited time to train and improve its service;
with DeepL API Pro, the texts are deleted after the translation and not used
to improve the service (DeepL privacy policy, sections on the free services,
DeepL API Free and DeepL Pro). For texts that must stay within a data
processing agreement, use DeepL API Pro, Azure Translator or an AI provider
with such an agreement.
