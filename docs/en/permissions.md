---
title: Permissions
summary: Who should be able to open "Access keys and licences", and which Contao permissions a user group needs for it.
audience: operations
language: en
updated: 2026-11-12
---

# Permissions

## Who should get access

*Access keys and licences* holds the keys of external services and the licence
keys of the installation. Whoever may open the page can replace these keys,
run the connection tests with them and remove a licence from the website.

Give access only to administrators or to the person who sets up the website.
Editors do not need it: the extensions read their keys from the page without
the editor ever opening it.

Administrators have access without any further setting.

## What Contao needs

*Access keys and licences* is a back-end module of its own (`neo_access`). All
permissions are set in the back end under **User groups** (or **Users**), with
the names Contao shows there. Basic has no switches of its own.

| Contao permission | Setting | What it is for |
| --- | --- | --- |
| **Back end modules** | *Access keys and licences*, in the menu group of the extensions | Opening the page. Without it the user sees neither the menu entry nor the button of the same name above the lists of the extensions nor the link in their warnings. |
| **Allowed fields**, table `tl_neo_access` | the fields of the sections the group may see; for the person who sets up the website, all of them (list below) | Contao shows only allowed fields. A section without an allowed field is left out. Without any allowed field of this table, Contao refuses to open the page. |
| **Table permissions** (Contao 5.7 and later), `tl_neo_access` | Update | Opening and saving the page, also with **Confirm** in the section *Terms of the extensions*. Without it Contao refuses to open the page. Create and Delete are not needed: the page has exactly one record, which the extension creates itself. |

As with Contao's own permissions, a user gets the permissions of the user, those
of the groups, or both, depending on **Permission inheritance**.

## The fields of `tl_neo_access`

Under **Allowed fields**, Contao lists the fields of the page under the table
name `tl_neo_access`, each with its label and the field name in square
brackets. Status lines have no label; Contao lists them by their field name
only. They carry the buttons **Test connection** and **Check now**: without
them, the buttons are missing.

| Section | Fields |
| --- | --- |
| Licences | `neo_license_intro` (where the key is), per paid extension: *Extension*: Licence key `[license_…_key]` and `license_…_status` (status with **Check now**) |
| AI connection | `ai_status` (status line with **Test connection**), Route `[ai_route]`, Address `[ai_base_url]`, Key `[ai_api_key]` |
| Azure Translator | `azure_translator_status` (status line with **Test connection**), Processing `[azure_translator_location]`, Address of the resource `[azure_translator_url]`, Key `[azure_translator_key]`, Region `[azure_translator_region]` |
| DeepL | `deepl_status` (status line with **Test connection**), Key `[deepl_key]` |
| Terms of the extensions | per extension: `terms_…` (passages, checkbox and **Confirm**) |
| Sections of the extensions | the status line `…_status` and the key fields of the section, with the labels of the extension |

The list contains only the fields of the extensions installed at that moment.
When a new extension adds a section, add its fields to the group.

## Example group "Website setup"

For the person who sets up the website and enters the keys:

1. **Back end modules**: *Access keys and licences*.
2. **Allowed fields** → `tl_neo_access`: all fields.
3. **Table permissions** (Contao 5.7 and later): `tl_neo_access` → Update.
4. Save.

Then log in with a test user of the group and open the page. Every section must
be there with its status line and its buttons. If something is missing, see
[Troubleshooting](troubleshooting.md).

## Several websites in one installation

The page has one record for the whole installation. There is no separation per
website: whoever may open it sees and changes the keys and licences of every
website in the installation. A licence is checked for the installation and
the domains of all its root pages (see [Usage](usage.md#domains-and-installations)).

## Settings of the extensions

The model of the AI connection and all other settings of an extension are in
the settings of that extension, not on this page. Which permissions they need
is described in the documentation of the extension.
