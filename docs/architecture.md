# Architecture

## Project Structure

```
app/
├── Console/Commands/       # Artisan commands (cms:install)
├── Helpers/                # Static helper classes (ContentHelper)
├── Http/
│   ├── Controllers/
│   │   ├── Admin/          # Admin CRUD controllers (pages, media, posts)
│   │   ├── BlogController  # Public blog archive/single rendering
│   │   └── PageController  # Public page rendering
│   ├── Middleware/          # RoleMiddleware
│   └── Requests/           # FormRequest validation classes
├── Models/                 # Eloquent models (User, Media, Page, Post, Setting)
├── Providers/              # Service providers
└── Traits/                 # Reusable traits (HasMedia)

config/
└── cms.php                 # CMS configuration (upload, templates, statuses, roles)

database/
├── migrations/             # Table schemas
├── seeders/                # Admin user and default settings
└── factories/              # Test factories

resources/views/
├── admin/                  # Admin panel views
│   ├── layouts/            # Admin layouts (app, guest)
│   ├── pages/              # Pages CRUD views
│   ├── media/              # Media manager views
│   ├── settings/           # Settings form
│   └── partials/           # Sidebar, alerts
├── components/             # Blade components (seo-meta)
├── layouts/                # Public layout
├── partials/               # Public header, footer
├── templates/              # Page templates (default, home)
└── errors/                 # Error pages (403, 404, 500)

routes/
├── web.php                 # Public routes (home, catch-all slug)
└── admin.php               # Admin routes (/admin prefix)
```

## Request Lifecycle

### Public Request

1. Request hits `routes/web.php`
2. `PageController::show()` finds the `Page` by slug using the `published` scope
3. `Page::resolveTemplate()` maps `template_key` to a Blade view in `resources/views/templates/`
4. The view extends `layouts.public`, which includes the SEO meta component and header/footer partials
5. The view composer on `layouts.public` shares global settings (site name and logo)

### Public Blog Request

1. Request hits `routes/web.php` on `/blog` or `/blog/{slug}`
2. `BlogController::index()` paginates published posts
3. `BlogController::show()` resolves a published post by slug
4. Blog views extend `layouts.public` and reuse shared settings and SEO component

### Admin Request

1. Request hits `routes/admin.php` (prefix `/admin`)
2. Middleware chain: `auth` → `role:admin,editor`
3. Controller handles CRUD and returns a view extending `admin.layouts.app`
4. Admin layout includes sidebar navigation and alert partials

## Key Patterns

- **HasMedia trait**: Provides polymorphic many-to-many media relationships to any model via the `mediables` pivot table
- **FormRequest classes**: All validation is declarative in dedicated request classes
- **JSON content model**: Pages store structured content in a `content_json` column with `meta` and `sections` keys
- **Collection reference model**: Blog posts store content in first-class fields (`excerpt`, `content`) and optional `meta_json`
- **Template resolution**: `template_key` on a page maps to `resources/views/templates/{key}.blade.php`
- **Settings as key-value**: Global settings use a `settings` table with type-aware casting and in-memory request cache
- **Role hardening with policy**: `PostPolicy` centralizes admin/editor permissions for blog CRUD
- **Maintenance commands**: `cms:user:reset-password` and `cms:media:audit-orphans` support common operational tasks
