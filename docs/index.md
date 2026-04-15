# Documentation Index

This index is the entry point for developers and LLM agents working on the starter.

## Core

- `docs/architecture.md` — folders, lifecycle, and architectural patterns
- `docs/pages-guide.md` — rules and workflow for creating and editing pages (media, content_json, templates, live editor)
- `docs/content-model.md` — `content_json`/meta structure reference for pages
- `docs/templates.md` — how page templates are registered and rendered
- `docs/live-editor.md` — shared editor engine: initEditorEngine API, DOM contract, layout variables, scroll retention
- `docs/settings.md` — global settings model and usage
- `docs/admin-translations-guide.md` — i18n rules and verification checklist for admin-only translations (`es/en`)
- `docs/image-strategy.md` — image optimization pipeline, traceability model, responsive delivery, and media health metrics

## Collections & Modules

- `docs/collections-guide.md` — rules and step-by-step guide for creating new collections (media, taxonomies, SEO, permissions)
- `docs/blog-collection.md` — complete blog collection reference (model, routes, admin, frontend)
- `docs/taxonomies.md` — taxonomy system, adding types, attaching to models, conventions
- `docs/modules.md` — how to organize project-specific custom modules
- `docs/maintenance.md` — operational commands for password reset, media orphan audit, and image optimization workflow

## Phase 5 Additions

- `docs/mcp.md` — MCP authentication model, exposed endpoints, and integration usage

## Quick Workflow

1. Start from `docs/architecture.md`.
2. **For pages**, read `docs/pages-guide.md` before creating or extending them.
3. **For collections**, read `docs/collections-guide.md` — it defines mandatory rules.
4. Use `docs/blog-collection.md` as the reference collection implementation.
5. Keep project-specific logic inside modules documented in `docs/modules.md`.
6. Use maintenance commands from `docs/maintenance.md` for operations.
