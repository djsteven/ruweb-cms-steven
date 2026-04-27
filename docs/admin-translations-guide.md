# Admin Translations Guide

## Purpose

This guide defines how to keep the administration interface translatable and consistent across supported locales.

Use it for:

- admin-facing Blade text
- admin JavaScript messages
- admin validation and JSON responses
- translation key conventions

## Scope

Applies to the administration surface only, such as:

- `resources/views/admin/**`
- admin JavaScript entry points
- `app/Http/Controllers/Admin/**`
- `app/Http/Requests/Admin/**`
- admin translation files under `lang/*/admin.php`

It does not define public-site translation rules.

## Source Of Truth

Visible admin text should come from translation keys, not hardcoded strings.

Typical pattern:

- `__('admin.<key>')`

Every supported locale should contain the same keys.

## Key Naming Conventions

Use stable prefixes by concern:

- `settings_*`
- `menu_*`
- `taxonomy_*`
- `field_*`
- `btn_*`
- `confirm_*`
- `role_*`

For nested structures, prefer grouped arrays:

- `settings_fields.site_name.label`
- `settings_groups.general`
- `settings_options.locale.en`

## Settings Rule

If a setting is created through a seeder or other bootstrap path, its admin label and group label should exist in every supported locale.

If a setting exposes selectable options, those option labels should also be translated.

## Blade Rules

- avoid visible literal text in admin views
- use translation keys for labels, placeholders, help text, and actions
- provide a safe fallback for dynamic values when a locale key is missing

## JavaScript Rules

Admin JavaScript should not hardcode visible UI messages.

Inject a translation dictionary from the layout or page and read from it in JS.

If legacy code still contains visible literals, treat this guide as the target standard and move those strings into the injected dictionary when touching the related code.

## Controller And Request Rules

Admin JSON messages, exceptions, and validation messages should use translation keys instead of literal strings.

## Verification Checklist

1. No visible admin text is hardcoded.
2. Every new admin key exists in every supported locale.
3. Admin JavaScript reads visible messages from an injected dictionary.
4. Public templates do not depend on admin translation namespaces.
5. Dynamic labels have a deterministic fallback when a translation is missing.

## Useful Checks

- compare flattened keys across locale files
- search admin code for literal visible strings
- verify seeded settings have matching translation keys

## Scope Boundary

This document should not become:

- a general i18n guide for the public site
- a copywriting guide
- a catalog of all current admin strings
