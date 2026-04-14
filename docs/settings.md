# Settings

## Overview

Global site settings are stored in the `settings` table as key-value pairs. Each setting has a `type` that determines how it's rendered in the admin form and how its value is cast when retrieved.

## Types

| Type | Admin Field | PHP Cast |
|------|-------------|----------|
| `string` | Text input | `string` |
| `text` | Textarea | `string` |
| `boolean` | Toggle switch | `bool` |
| `integer` | Number input | `int` |
| `media` | Media selector | `Media` model or `null` |

## Groups

Settings are organized by `group` (stored as a column). Groups become tabs in the admin settings form. Default groups:

- **general**: site_name, site_description, site_logo, site_favicon, homepage_slug
- **seo**: default_meta_title, default_meta_description
- **admin**: admin_locale

## Usage in Code

```php
use App\Models\Setting;

// Get a setting value (type-aware casting)
$siteName = Setting::get('site_name', 'Default Name');

// Get a media setting (returns Media model)
$logo = Setting::get('site_logo');
$logoUrl = $logo?->url();

// Set a value
Setting::set('site_name', 'New Name');

// Get all settings in a group
$seoSettings = Setting::getGroup('seo');

// Get all settings grouped
$allGrouped = Setting::allGrouped();
```

## Adding a New Setting

### 1. Add to the seeder

In `database/seeders/SettingsSeeder.php`, add your setting to the `$settings` array:

```php
['key' => 'analytics_id', 'value' => '', 'type' => 'string', 'group' => 'general'],
```

### 2. Run the seeder

```bash
php artisan db:seed --class=SettingsSeeder
```

The seeder uses `updateOrCreate` on the `key`, so it won't overwrite existing values.

### 3. Use it

```php
$analyticsId = Setting::get('analytics_id');
```

## Caching

Settings use an in-memory static cache within each request. Multiple calls to `Setting::get()` in the same request only query the database on the first call per key. The cache is cleared by calling `Setting::clearCache()`.

## Admin Access

The settings admin page is restricted to the `admin` role. Editors cannot view or modify settings.
