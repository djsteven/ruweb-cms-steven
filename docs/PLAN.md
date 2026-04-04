# Planning — CMS v1

Minimal Laravel + Editorial Starter Kit  
Working Document — April 2026

## 1. Vision and Principles

### System Vision

Build a lightweight, extensible editorial CMS starter kit designed for AI-assisted development.

It does not seek to compete with WordPress nor solve every possible case. It serves as a productive base to clone, start up quickly, and build custom sites while maintaining technical control of the frontend and a simple editing experience for the client.

It is conceived as an LLM framework: it delivers resolved the expensive and sensitive pieces, and leaves the rest open so the developer can build to measure following guides, conventions, and recommended practices.

### Guiding Principles

- The developer defines structure, templates, fields, and behavior.
- The client only edits content within clear limits.
- The frontend lives in code; the backend manages content.
- The core must be small and understandable.
- There will be no heavy contracts or premature abstractions.
- All complex customization is built as a module separate from the core.
- The system must be easy to extend by humans and by LLMs.
- AI accelerates development and documentation, but does not replace architecture decisions.
- Mobile first in public frontend and in the editing interface.
- Performance, clarity, and maintainability weigh more than hyper-flexibility.
- The base solves what is expensive, delicate, and repetitive; it does not attempt to solve all business cases.
- The developer can vibe-code features, templates, modules, and DB changes following the starter's guides.
- Technical documentation is part of the system and functions as a work interface for humans and LLMs.

### Architectural Approach

- The frontend is defined in Blade templates made by developers.
- The backend saves and delivers structured content.
- The client does not build layout or design.
- Each page responds with a consistent JSON, defined by its template.
- The starter does not impose a closed model for extending business, collections, or integrations.
- The developer can modify the DB, internal structure, and modules according to the project.
- Guides and recommended practices order the work without turning the base into a rigid framework.

## 2. Technical Stack

### Framework Decision: Minimal Laravel

Laravel (12.x) is adopted as the project's base, but with a minimalist approach: what the framework solves well out of the box is used, and unnecessary packages or layers are not incorporated.

#### What is used from Laravel

- Routing and middleware
- Eloquent ORM for models and relationships (including polymorphic for media)
- Migrations and seeders
- Declarative validation (FormRequest)
- Blade as template engine
- Own auth scaffolding (no Breeze or Jetstream)
- CSRF, rate limiting, hashing (bcrypt/argon2)
- Storage abstraction for media
- Artisan for setup and maintenance commands

#### What is NOT used in v1

- Breeze / Jetstream / Fortify
- Queues (queues) or background jobs
- Broadcasting / WebSockets
- Events / Listeners as a heavy system
- Notifications as a subsystem
- API Resources / Sanctum / Passport
- Inertia / Livewire

### Backend

- PHP 8.2+ on Laravel 12.x
- MySQL 8+
- Eloquent for database access
- Simple modular architecture within Laravel conventions
- Server-side rendering with Blade for public frontend
- Admin with internal endpoints and JSON responses for reactive editor
- Base prepared for cloneable starter kit via Git

### Public Frontend

- Semantic HTML rendered with Blade
- Tailwind CSS for styling
- Modular vanilla JavaScript
- Blade templates defined by developers

### Admin / Editor

- Blade + Tailwind CSS (dark mode by default)
- Side panel editing with live preview (iframe)
- Light interaction without heavy frontend framework in v1

### LLM / MCP Layer

- System exposure via MCP for external agent tools of the developer
- No AI UI is planned within the CMS at this stage
- The goal is to enable automation, inspection, and operations from external tools

### Infrastructure

- Deployment on PHP/MySQL compatible hosting or VPS
- Local media storage in v1
- Videos always embedded from third parties
- Base repository designed for git clone and quick startup

### Stack Conventions

- Media uses many-to-many polymorphic via `mediables` pivot table (a single file can be attached to multiple entities).
- Each page has template_key + content_json.
- Each Blade template defines what data it expects.
- All complex customization lives outside the core.
- Every new piece must be quickly documentable.
- The starter prioritizes hardening in auth and media before genericness in features.
- MD documentation is part of the technical product, not an extra.

## 3. Content Model

### Philosophy

Each page has content structured in JSON with two levels: global page metadata and logical blocks within sections.

### content_json Structure

The JSON of each page follows this form:

- `meta`: title, slug, featured_image (media_id), description, image, and optional og_title / og_description only when they differ from the base values
- `sections`: logical blocks defined by the template (hero, features, cta, etc.)

Meta unifies page metadata, SEO, and social sharing in a single block with sensible fallbacks. No separate `seo` or `share` blocks — less fields for the client, less fallback logic in templates.

### System Entities

- `collections` is not planned as a generic subsystem of the core. The base will include documentation for creating collections as needed. The only example included will be blog.
- `sections` is kept as a concept of organization within a page's JSON, not as an independent entity.

## 4. Scope of v1

### Productized Core in the Starter

- User authentication (own auth on Laravel)
- Role field on users (string: admin, editor) with middleware
- Media system for images and documents
- Base structure of the Laravel project
- Minimal base Blade templates
- Pages and settings as a starting point
- Extensive technical documentation in MD files

### Included in v1

- Indexable pages by URL
- Base Blade templates
- Content editor with side panel and live preview
- Media manager for images and documents
- Global settings
- Auth for administrators and role-based middleware
- SEO and share meta tags (unified in content_json meta block)
- Blog as a functional example collection
- Cloneable starter kit for new projects
- Artisan install command (`php artisan cms:install`)

### Excluded from v1

- Native video hosting
- Global style editor
- Native ecommerce
- Advanced forms as a core system
- Plugin system
- Multi-tenant
- Universal visual builder
- Generic collections subsystem
- Inline editing (only side panel + preview in v1)
- Password recovery via email (use Artisan command instead)
- Separate roles table (role is a field on users)
- content_json schema validation (template Blade implicitly defines structure)
- Orphaned media UI (use Artisan command to audit)
- Complete automated testing (basic tests for auth and media are recommended)

## 5. Development Phases

### Phase 1 — Core, Auth, and Media

**Objective:** Deliver the critical technical base with authentication and the media subsystem resolved.

**Includes**

- Clean Laravel 12.x project and configured
- Starter folder structure definition
- Base configuration (.env, database, storage)
- DB connection with Eloquent
- Initial migrations (users with role field, media with polymorphic support)
- Seeders for first admin user
- Artisan install command (`php artisan cms:install` — creates DB, first user, initial config)
- Administrator authentication (login, logout, session)
- Role-based middleware (role field on users, no separate table)
- CSRF active by default (Laravel built-in)
- Rate limiting on login
- Password hashing with bcrypt (configurable to argon2)
- Base validations with FormRequest
- Initial admin panel layout
- Basic error handling and logging (Laravel built-in)
- Media model with Eloquent (polymorphic)
- Media table migration
- Image and document upload
- MIME, size, and extension validation (FormRequest)
- Unique name generation
- Local storage via Storage facade
- Media selector in admin
- Metadata: alt, title, size, mime, path
- References by media_id with polymorphic relationships
- Upload hardening

**Deliverable:** Launchable project with auth, roles, and media manager resolved. Functional install command.

### Phase 2 — Content and Public Frontend

**Objective:** Implement the editorial model and connect it to a real frontend.

**Includes**

- Page model with Eloquent
- Migration with template_key + content_json (JSON column)
- Pages CRUD in admin
- Setting model with Eloquent
- Settings CRUD in admin
- Basic status: draft / published
- Blade template system
- template_key resolution to Blade view
- Render helpers for content_json
- Base partials: header, footer
- Default page template
- Public URL resolution (route model binding)
- Custom 404 page
- content_json render within Blade templates
- SEO meta tags and share preview (from unified meta block)
- Initial structure of MD documentation for extensions

**Deliverable:** Editable content with clear structure, rendered on a functional public frontend.

### Phase 3 — Editor with Live Preview

**Objective:** Create the editorial UX that differentiates the system.

**Includes**

- Side panel for editing
- Live preview via iframe
- Manual or basic autosave
- content_json synchronization (panel edits refresh preview)
- Reasonable mobile-first experience in admin

**Deliverable:** Intuitive editing flow for end client.

**Note:** This phase remains the most complex in UX. Synchronization between panel and preview can consume significant time. Plan accordingly. Inline editing is explicitly deferred — only side panel + iframe preview in v1.

### Phase 4 — Blog, Docs, and Hardening

**Objective:** Deliver the blog as a reference collection, convert documentation into the central piece of the starter, and solidify the base.

**Includes**

- Example collection: blog (model, migration, routes, views)
- Blog archive and single with Blade
- MD files: architecture, conventions, workflow
- Guides for creating pages, templates, collections, and custom modules
- Documentation index for LLM-assisted work
- Clear separation between what comes in the starter and what is implemented per project
- Additional validations
- Clearer permissions per role (middleware + policies)
- Custom module organization
- Naming conventions
- Performance and security review
- Core cleanup
- Basic tests for auth and media
- Quality checklist for new features
- Artisan commands for maintenance (password reset, orphaned media audit)

**Deliverable:** Documented, solid starter with functional blog and clear guides to extend it.

### Phase 5 — MCP Layer / Agent Integration

**Objective:** Expose the system for use from personal agents and external tooling.

**Includes**

- MCP surface design
- Resources and actions exposed for pages, collections, media, and settings
- Authentication for access from external agents
- Safe and limited operations on the CMS
- Technical documentation for consumption from developer tools
- Criteria to avoid coupling the core to an internal AI UX

**Deliverable:** System prepared to be operated and inspected from a personal agent via MCP.

## 6. Decisions Taken

- Framework: Minimal Laravel (no Breeze/Jetstream/Inertia/Livewire)
- Tailwind CSS for all UI (frontend and admin backoffice)
- Admin backoffice is dark mode by default
- Media uses many-to-many polymorphic via mediables pivot table
- Videos will not be uploaded to the system
- No global editable styles in v1
- Design and layout are resolved in code (Blade)
- The backend delivers content; the frontend interprets it
- Custom features are built to measure on the base
- Auth, media, and routing/render come resolved seriously
- The rest of the system relies on documentation, conventions, and AI-assisted development
- The developer is free to modify the DB and extend the system
- The repository includes extensive MD files for extension guides
- Artisan install command replaces web-based setup wizard
- Basic tests for auth and media are recommended from phase 4
- Laravel is used as infrastructure, not as product opinion
- Roles are a string field on users, not a separate table (sufficient for admin/editor in v1)
- Password recovery is an Artisan command, not an email flow (avoids SMTP config dependency)
- SEO/share/meta unified in a single content_json block (less fields, sensible fallbacks)
- No inline editing in v1 — side panel + iframe preview only
- No content_json schema validation — the Blade template implicitly defines expected structure
- Orphaned media is audited via Artisan command, not a dedicated admin UI

## 7. Immediate Next Steps

- Create clean Laravel 12.x project
- Define starter folder structure
- Close exact scope of auth (login, logout, session, role middleware)
- Design media table (Eloquent migration with polymorphic support)
- Decide exact form of pages + content_json (unified meta block)
- Define base index of MD documentation
- Build `php artisan cms:install` command
- Start Phase 1

## 8. Entity Reference

| Entity | Description |
|--------|-------------|
| `pages` | Unique URL indexable unit. Has template_key + content_json (with unified meta block). |
| `settings` | Global site settings (name, logo, social networks, etc.). |
| `users` | Administrators with role field (admin, editor). Own auth, no external packages. |
| `media` | Images and documents. Polymorphic relationships for dedup and usage tracking. |
