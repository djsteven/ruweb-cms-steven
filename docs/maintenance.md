# Maintenance Commands

Phase 4 adds operational commands for common maintenance tasks.

## Reset user password

```bash
php artisan cms:user:reset-password {email?}
```

- Resets password for an existing admin/editor user
- You can pass email as argument or enter it interactively
- Enforces minimum length of 8 chars

## Audit orphaned media

```bash
php artisan cms:media:audit-orphans
```

- Lists media records not referenced by `mediables`
- Excludes media IDs currently used by settings of type `media`

To remove them:

```bash
php artisan cms:media:audit-orphans --delete
```

- Deletes both physical file and DB record for each orphan
- Review output table before using `--delete`

## Image optimization workflow

Run these commands in order, never in parallel:

```bash
php artisan media:convert-webp
php artisan media:generate-variants
```

Dry-run support:

```bash
php artisan media:convert-webp --dry-run
php artisan media:generate-variants --dry-run
```

Force-regenerate responsive variants:

```bash
php artisan media:generate-variants --force
```

Audit live media health (DB + storage):

```bash
php artisan media:audit-health
```
