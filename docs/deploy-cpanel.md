# Deploy en cPanel (flaxt.alemany.dev)

## Requisitos
- PHP 8.3+ (usar MultiPHP Manager en cPanel)
- MySQL
- Acceso a Terminal de cPanel

## Estructura de carpetas
El proyecto va directamente en la carpeta del subdominio (al mismo nivel que `public_html`):
```
home/zncjmgrc/
├── public_html/
└── flaxt.alemany.dev/   ← raíz del proyecto
    ├── app/
    ├── public/          ← document root del subdominio
    └── ...
```

En cPanel → Domains, el document root del subdominio debe apuntar a:
```
/home/zncjmgrc/flaxt.alemany.dev/public
```

## Pasos de deploy

### 1. Preparar localmente
```bash
npm run build
```
Subir todo el proyecto **excepto** `node_modules/` al servidor (zip o FTP).

### 2. PHP en la Terminal
El PHP del sistema puede ser viejo. Usar el binario de cPanel:
```bash
alias php=/opt/cpanel/ea-php83/root/usr/bin/php
php -v  # verificar 8.3+
```

### 3. Instalar dependencias
El `composer.lock` generado con PHP 8.4 no es compatible con 8.3. Hay que actualizar en el servidor:
```bash
php /opt/cpanel/composer/bin/composer update --no-dev --optimize-autoloader
```
Esto hace downgrade de Symfony 8.x → 7.x compatible con PHP 8.3.

### 4. Configurar .env
```bash
cp .env.example .env
nano .env
```
Valores requeridos:
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://flaxt.alemany.dev
DB_CONNECTION=mysql
DB_HOST=localhost
DB_DATABASE=zncjmgrc_flaxt
DB_USERNAME=zncjmgrc_flaxt
DB_PASSWORD=...
```
> Nota: `DB_CONNECTION=mysql` es obligatorio, sin él Laravel usa SQLite por defecto.

### 5. Comandos finales
```bash
php artisan key:generate
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan migrate --force
php artisan db:seed
php artisan storage:link
mkdir -p storage/app/public/media
chmod -R 755 storage bootstrap/cache storage/app/public
```

## Credenciales admin por defecto
- Email: `admin@flaxt.local`
- Password: `password`

Configurables vía `.env`:
```env
CMS_ADMIN_NAME=Admin
CMS_ADMIN_EMAIL=admin@flaxt.local
CMS_ADMIN_PASSWORD=password
```

## Notas
- El alias `php` solo dura la sesión de terminal. Repetirlo en cada sesión nueva.
- El composer de cPanel está en `/opt/cpanel/composer/bin/composer`.
- Al actualizar código: subir archivos, correr `composer install` (no update) y `php artisan optimize`.
