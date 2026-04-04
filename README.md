# flaxt-cms
Fast, flexible and intuitive LLM-first CMS.

## Phase 4 highlights

- Blog collection with admin CRUD (`/admin/posts`) and public routes (`/blog`, `/blog/{slug}`)
- Role hardening via `PostPolicy` (editor can create/update, admin can delete)
- Maintenance commands:
  - `php artisan cms:user:reset-password`
  - `php artisan cms:media:audit-orphans`
- Feature tests for blog visibility, post permissions, and auth/media smoke paths

## Documentation

Start at `docs/index.md`.
