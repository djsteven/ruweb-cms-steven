# SEO Guide

## Purpose

This document defines the shared SEO conventions of the starter.

Use it for:

- sitemap inclusion rules
- metadata conventions
- global analytics and verification tag placement
- crawler-facing files such as `robots.txt`

## Sitemap

The application may expose a dynamic sitemap such as `/sitemap.xml`.

Typical inclusion rules:

- homepage
- published pages
- published collection indexes
- published collection detail pages

Only publicly visible content should appear in the sitemap.

## Adding A Collection To The Sitemap

When a new public collection is introduced:

1. add its published query to the sitemap builder
2. add its URL entries to the sitemap view or serializer

Prerequisites usually include:

- a published visibility scope or equivalent
- a `slug`
- an `updated_at` timestamp or another last-modified signal

## Priority Conventions

Use stable, intention-revealing priorities.

Example guidance:

| Content type | Priority | Changefreq |
|-------------|----------|------------|
| Homepage | 1.0 | daily |
| Top-level index pages | 0.8 | weekly |
| Section indexes | 0.6 | daily |
| Individual detail pages | 0.5 | monthly |
| Archived or low-priority content | 0.3 | yearly |

## Metadata Model

Public entities should support metadata overrides for:

- description
- social title override
- social description override

These values should integrate with the shared SEO component and global fallbacks.

Collections that render public detail pages should expose a metadata shape compatible with the rest of the system.

## Global Integrations

The shared SEO layer is also the correct place for global browser-side integrations such as:

- analytics tags
- advertising pixels
- search verification tokens

Guidelines:

- store normalized IDs or tokens, not pasted arbitrary scripts
- render them only on the public site unless there is a clear reason otherwise
- keep the integration surface centralized

## `robots.txt`

The application should ship a crawler policy file that:

- blocks non-public admin areas when appropriate
- points crawlers to the sitemap URL

Default starter policy:

```text
User-agent: *
Disallow: /admin/

Sitemap: /sitemap.xml
```

## Checklist For A New Public Collection

- [ ] It has a public visibility rule.
- [ ] Its routes are registered before any page catch-all route.
- [ ] It is included in the sitemap builder.
- [ ] It exposes compatible metadata fields.
- [ ] `robots.txt` does not accidentally block its URLs.

## Scope Boundary

This document should not become:

- a per-controller implementation guide
- a marketing analytics playbook
- a list of one project's current third-party IDs
