# flaxt-cms
Fast, flexible and intuitive LLM-first CMS.

## Deploy / desarrollo local

### Requisitos
- PHP 8.3+ (con extensiones típicas de Laravel; `cms:install` valida `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `fileinfo`)
- Composer
- Node.js (recomendado 20+)
- MySQL (o MariaDB) corriendo en local

### Setup inicial
1. Instalar dependencias PHP:
   ```bash
   composer install
   ```

2. Crear el `.env`:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. Instalar dependencias frontend:
   ```bash
   npm install
   ```

4. Instalar el CMS (crea la DB si el usuario tiene permisos, corre migraciones, crea admin y hace `storage:link`):
   ```bash
   php artisan cms:install
   ```

5. (Opcional) Sembrar menú de ejemplo:
   ```bash
   php artisan db:seed --class="Database\\Seeders\\MenuSeeder"
   ```

### Levantar el proyecto
En desarrollo, lo más simple es usar el runner incluido (servidor + queue + logs + Vite):
```bash
composer run dev
```

URLs:
- App: `http://localhost:8000`
- Admin login: `http://localhost:8000/admin/login`

### Credenciales admin
Durante `php artisan cms:install` podés elegirlas. Los defaults del repo (si no cambiás nada) también están en `.env.example`:
```env
CMS_ADMIN_EMAIL=admin@flaxt.local
CMS_ADMIN_PASSWORD=password
```

### Troubleshooting rápido
- Si te conecta a SQLite “sin querer”, revisá que en `.env` esté `DB_CONNECTION=mysql`.
- Si `cms:install` no puede crear la DB, creala manualmente y asegurate de que `DB_DATABASE/DB_USERNAME/DB_PASSWORD` sean correctos.

## Phase 4 highlights

- Blog collection with admin CRUD (`/admin/posts`) and public routes (`/blog`, `/blog/{slug}`)
- Role hardening via `PostPolicy` (editor can create/update, admin can delete)
- Maintenance commands:
  - `php artisan cms:user:reset-password`
  - `php artisan cms:media:audit-orphans`
- Feature tests for blog visibility, post permissions, and auth/media smoke paths

## Documentation

Start at `docs/index.md`.
