---
title: Troubleshooting
summary: The messages of "Access keys and licences" and common problems with Basic – what you see, why, and what to do.
audience: operations
language: en
updated: 2026-11-12
---

# Troubleshooting

The status lines of the page and the result of **Test connection** name most
causes themselves. The tables below list every message with its cause. Missing
sections, fields or buttons are nearly always missing permissions; see
[Permissions](permissions.md).

## The page

| What you see | Why | What to do |
| --- | --- | --- |
| No menu entry *Access keys and licences* | No installed extension adds a section to the page; then the page does not exist. Or the user lacks the back-end module. | Check that the extension that needs the page is installed. Grant **Back end modules** → *Access keys and licences*. |
| The address of the page leads to the back-end start page | No installed extension adds a section | Intended. |
| Contao refuses to open the page (access denied) | The group has no allowed field of `tl_neo_access`, or, from Contao 5.7, the table permission is missing | **Allowed fields** → `tl_neo_access`; **Table permissions** → `tl_neo_access` → Update. |
| A section or a field is missing for a user | The field is not among the **Allowed fields** | Add it ([list of fields](permissions.md#the-fields-of-tl_neo_access)). |
| No button **Test connection** or **Check now** | The status line of the section is not among the **Allowed fields**. **Check now** also appears only once a licence key is stored. | Add `ai_status`, `azure_translator_status`, `deepl_status` or `license_…_status`. |
| A section is collapsed | Intended: there is nothing left to do in it | Click its title to open it. |
| **Test connection** is disabled: "Can be tested once the access is saved." | No key (or, with an own address, no address) is saved | Enter it and save. |
| After **Test connection** or **Check now**: "That did not work. Please reload the page and try again." | The answer did not arrive, e.g. because the session ended | Reload the page (log in again if asked) and click the button again. |
| After **Test connection** or **Check now**: "The page is out of date. Please reload it and try again." | The request token of the page is no longer valid, e.g. after a long time on the page | Reload the page and click the button again. |
| Without JavaScript, **Test connection** or **Check now** leads to Contao's confirmation page for an invalid request token | The link is out of date | Go back, reload the page and click again. |
| A key field shows eight dots | Intended: a key is stored and is never shown again | Leave the field as it is to keep the key. |
| An extension says that a key is missing, without a link | The user may not open *Access keys and licences* | Ask an administrator to enter the key. |
| The status line says "Key stored (encrypted)", but **Test connection** answers "No key stored yet." | The app secret of the installation (`APP_SECRET`) was changed; stored keys can no longer be decrypted | Enter the keys again. |

## Status lines of the sections

| What you see | Why | What to do |
| --- | --- | --- |
| AI connection: "No key stored yet." | The field **Key** is empty | Enter the API key and save. |
| AI connection: "The route "Own address" needs an address." | Route **Own address (OpenAI-compatible)** without an address | Enter the **Address**, e.g. `https://example.com/v1`. |
| Azure Translator: "No key stored yet. Only needed for translations." | No key stored; only translations need it | Enter key and region if an extension translates texts. |
| Azure Translator: "The processing "Own address of the resource" needs an address." | **Own address of the resource** without an address | Enter the **Address of the resource** from the Azure portal. |
| DeepL: "No key stored yet. Only needed for translations." | No key stored; only translations need it | Enter the key if an extension translates texts with DeepL. |
| Section of an extension: "Not stored yet." | A key the extension needs is missing | Enter it; where to get it is in the documentation of the extension. |
| Section of an extension: "Not stored. Only needed for some functions." (or a text of the extension) | An optional section is empty | Nothing to do, unless you need the function. |

## Test connection

| What you see | Why | What to do |
| --- | --- | --- |
| "No key stored yet." | No key saved | Enter the key, **Save**, test again. |
| "No connection: …" | The server cannot reach the address; the text after the colon is the technical reason | Check the address; the server must be allowed to open outgoing connections to it. |
| AI connection: "The route answers with HTTP 401 (after … ms). Answer:" | The provider rejects the key | Enter the key again; check it in your account with the provider. |
| AI connection: "The route answers with HTTP …" with another status | The answer below the line says why | Read the answer; check route and address. |
| Azure: "Azure answers with HTTP 401 (after … ms). Answer:" | The key is wrong | Enter key 1 or 2 of the resource again. |
| Azure: "Azure answers with HTTP 403 (after … ms). Answer:" | Often the **Region** does not match the resource | Enter the region shown for the resource in the Azure portal. |
| DeepL: "DeepL answers with HTTP 403 (after … ms). The key is not accepted." | The key is wrong or was deleted in the DeepL account | Copy the key again from the DeepL account (**API keys**). |
| DeepL: "DeepL answers with HTTP 456 (after … ms). The characters of this billing period are used up." | The character limit of the plan is reached | Wait for the next billing period, raise the limit or change to Pro. |
| DeepL: the status line names "DeepL API Free" although you have Pro (or the other way round) | Free or Pro follows from the key: keys of the free plan end in `:fx` | Enter the key of the plan you want to use. |
| The test shows old values | The test uses what is saved | Save, then test again. |

## Licences

| What you see | Why | What to do |
| --- | --- | --- |
| "No licence." | No licence key stored | Enter the key from the order confirmation. |
| "This licence key is not valid for this extension." (at the field, after saving) | The licence service does not know the key for this extension; the key is not kept | Check the key, and that it is entered in the field of the right extension. |
| "The licence key is stored but not confirmed yet." | The licence service has not confirmed the key yet, e.g. because it could not be reached while saving | Click **Check now**. |
| **Check now** says "Checking …" for a few seconds | The licence service is asked; without an answer the check gives up after at most 15 seconds | Wait for the result in the row. |
| "The licence service could not be reached at the last check. It is tried again automatically within an hour, or with "Check now"." | Network problem, or the licence service was not available | Wait, or click **Check now**. The server must be allowed to open outgoing HTTPS connections. |
| "The certificate of the licence service cannot be checked with this version of the extension." | The signed confirmation does not match the public keys built into this version of Basic | Update Basic. On a test installation with another address of the licence service ([Customising](customising.md)), confirmations are still checked against the built-in keys. |
| "The certificate belongs to another extension." | The key of another extension was entered | Enter the key of this extension. |
| "The licence has expired." | The licence ran out, including the grace period | Renew the licence, then **Check now**. |
| "The licence has been withdrawn." | The licence was withdrawn | Contact the seller of the licence. |
| "Licence … expired on …. Without renewal it remains valid until …" | The paid period is over; the grace period runs | Renew the licence before the date. |
| "Not covered by the licence: …." | A domain of the website is not licensed | Add the domain to the licence, or check the **Domain name** of the root pages. |
| At a domain: "This domain is not entered in the licence. …" | The domain is not part of the licence | Enter it in the customer area, or book additional domains; then **Check now**. |
| Red warning "Cannot be activated: the number of installations of this licence has been exceeded. …", at the domain "All installations of the licence are in use." | The licence is already active in all installations it allows (one by default); an installation that has not reported for 35 days no longer counts | Release an installation in the customer area, or ask the manufacturer for more installations through the link in the warning; then **Check now**. |
| At a domain: "This installation uses more domains than the licence allows. …" | More root pages with their own domain than the licence allows per installation | Book additional domains per installation, or check the **Domain name** of the root pages. |

Technical details of a failed licence check are not shown on the page. Basic
writes them to the log of the installation (`var/logs/`) as a warning; whether
warnings are kept there depends on the log settings of the installation.

## Credit line on the website

| What you see | Why | What to do |
| --- | --- | --- |
| The credit line stays although the extension is licensed | The page is served over plain HTTP; the check in the browser needs HTTPS | Serve the website over HTTPS. |
| The credit line stays on some domains of the website only | The licence does not cover those domains | See "Not covered by the licence" above. |
| The extension takes no input; it says "This feature is currently not available on this website." | The credit line is cut off or covered, e.g. by a height limit, hidden overflow or an element on top | Make the credit line visible; see [Customising](customising.md). |

## Terms of the extensions

| What you see | Why | What to do |
| --- | --- | --- |
| "Not confirmed yet." | The terms of the extension were never confirmed | Read, tick, **Confirm**. |
| "The terms have changed. Please confirm the marked passages again." | A passage got a new version, or a higher edition was licensed | Confirm the passages marked **new** or **changed**. |
| "Please tick the box first to confirm the terms." | **Confirm** was clicked without the checkbox | Tick **I have read the terms and accept them.**, then **Confirm**. |
| An extension shows a warning with **Confirm now** | Terms still to be confirmed | Follow the link and confirm. |
| No section *Terms of the extensions* | No installed extension has terms, or `terms_…` is not among the **Allowed fields** | Add the field, if the group should confirm terms. |

## Still stuck?

1. Log in as an administrator. If everything works there, it is a permission;
   compare the group with [Permissions](permissions.md).
2. Look into the **System log** of the back end and into `var/logs/`.
