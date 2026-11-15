---
title: Setup
summary: Entering the licence keys, the AI connection, the accesses of Azure Translator and DeepL and the keys of the extensions on "Access keys and licences", testing the connections and confirming the terms.
audience: operations
language: en
updated: 2026-11-13
---

# Setup

Everything is set up on one back-end page, *Access keys and licences*. Do the
steps in this order and skip every section the page does not show: it shows
only what the installed extensions need.

## 1. Open the page

1. In the back end, open the menu group of the extensions and click **Access
   keys and licences**. It is always the last entry of the group. Some
   extensions also show a button of the same name above their list.

![The page "Access keys and licences". Frame 1 marks the section "Licences" with one row per paid extension (key field on the left, status on the right); frame 2 the section "Terms of the extensions"; frame 3 the section "AI connection"; frame 4 the sections "Azure Translator" and "DeepL"; frame 5 the sections of the extensions.](bilder/access-page.png)

- Frame 1: **Licences**, the licence keys of the paid extensions (step 2).
- Frame 2: **Terms of the extensions** (step 3).
- Frame 3: **AI connection** (step 4).
- Frame 4: the translation services **Azure Translator** and **DeepL**
  (step 5).
- Frame 5: the sections of the extensions, e.g. the key of a search or
  measuring service (step 6).

A section with nothing left to do is collapsed; click its title to open it. A
section where something is missing is open. Each section starts with a status
line that says what is stored and what is missing, and names the extensions
that use the section; [Usage](usage.md) explains them.

## 2. Licences (paid extensions)

The section **Licences** has one row per paid extension: the field
***Extension*: Licence key** on the left, its status on the right.

1. Enter the key from the order confirmation in the field of the extension.
   Spaces and lower case in the key do not matter.
2. Click **Save**.

When you save, the key is activated at the licence service. A key the licence
service does not know for this extension is not kept; the field then shows
"This licence key is not valid for this extension." Without a key the
extension runs as its free edition.

Licence keys are sold in the store, <https://store.neo-digital.at>. The status
line of each row ends with the link **Buy a licence key in the store**; it
opens the page of that extension, which also shows what each edition
includes.

## 3. Terms of the extensions

The section **Terms of the extensions** appears when an installed extension has
terms to confirm. It is open while something is still to be confirmed.

1. Open each passage with **show text** and read it.
2. Tick **I have read the terms and accept them.**
3. Click **Confirm**.

Details are in [Usage](usage.md#terms-of-the-extensions).

## 4. AI connection

The AI connection is used by every installed extension that works with a
language model.

1. Section **AI connection** → **Route**: choose how the extensions reach the
   language model.
   - **Requesty, EU route (processing in the EU)** (default)
   - **Requesty, standard route (worldwide)**
   - **OpenAI (direct)**
   - **Own address (OpenAI-compatible)**: then enter the address of the
     interface in **Address**, for example `https://example.com/v1`.
2. **Key**: enter the API key from your account with the provider.

The model is not chosen here. Each extension chooses it in its own settings,
from the models the route offers.

## 5. Translation services (only when an extension translates texts)

An extension that translates texts uses Azure Translator, DeepL or a language
model of the AI connection; which one is chosen in the settings of the
extension. Set up the one you want to use. Without a key, both sections show a
note, not an error.

### Azure Translator

You need a *Translator* resource in the Azure portal. Its key and region are
under resource → **Keys and Endpoint**.

1. Section **Azure Translator** → **Processing**: choose where Azure
   translates the texts.
   - **Europe (France or West Europe)** (default)
   - **Worldwide (nearest data centre)**
   - **Americas**
   - **Asia Pacific**
   - **Own address of the resource**: then enter the address from the Azure
     portal in **Address of the resource**, for example
     `https://name.cognitiveservices.azure.com/translator/text/v3.0`. This is
     needed with restricted network access or for processing in Switzerland.
2. **Key**: key 1 or key 2 of the resource.
3. **Region**: the location of the resource, for example `westeurope`. Leave
   it empty for a global resource.

### DeepL

You need an account for the DeepL API (Free or Pro). The key is in the DeepL
account under **API keys**.

1. Section **DeepL** → **Key**: enter the authentication key.

Free or Pro follows from the key: keys of the free plan end in `:fx` and are
sent to `api-free.deepl.com`, all others to `api.deepl.com`. The status line
names the interface in use.

## 6. Sections of the extensions

Further sections hold the keys an installed extension needs, for example for a
search or measuring service. Where to get each key is described in the
documentation of that extension. A section marked as only needed for some
functions may stay empty.

## 7. Save and test the connections

1. Click **Save**.
2. In the section **AI connection**, click **Test connection**. The test uses
   what is saved. The page stays where it is: what you typed and did not save
   yet stays in the fields, but is not tested.
3. Read the result below the button.

![The section "AI connection" after "Test connection". Frame 1 marks the button; frame 2 the result below it: "Connection works: HTTP 200 in 1 ms, 3 models available." and the line with the address queried and the key. The value of the key is masked in this picture.](bilder/test-connection.png)

- Frame 1: the button **Test connection**. While the test runs, it says
  "Testing …" and cannot be clicked.
- Frame 2: the result: whether the connection works, the HTTP status, the
  duration and, for the AI connection, the number of models the route offers;
  below it the address the server queried and the key, shown only as its
  length, its first four and its last two characters.

Then run the test in the sections **Azure Translator** and **DeepL** the same
way. The test of Azure Translator translates one word; the test of DeepL asks
for the characters used in the current billing period and translates nothing.

If the test fails, [Troubleshooting](troubleshooting.md) lists every result
with its cause.

## 8. Daily licence check

Make sure the Contao cron runs (web cron, or the command `contao:cron` started
by the scheduler of the server). The licence of each paid extension is checked
again once a day; without the cron it is only checked when someone saves the
key or clicks **Check now**.

## 9. Who may open the page

The page holds keys. Give access to it only to administrators or to the person
who sets up the website; [Permissions](permissions.md) lists what Contao needs
for that.
