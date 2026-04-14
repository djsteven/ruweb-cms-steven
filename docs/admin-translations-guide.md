# Guía de traducciones del Admin

Esta guía define cómo mantener las traducciones del panel de administración (`/admin`) en español e inglés.

## Alcance

Incluye solo el lado admin:

- `resources/views/admin/**`
- `resources/js/admin.js`
- `app/Http/Controllers/Admin/**`
- `app/Http/Requests/Admin/**`
- `lang/es/admin.php`
- `lang/en/admin.php`

No incluye el frontend público:

- `resources/views/templates/**`
- `resources/views/layouts/public.blade.php`
- `resources/views/blog/**`
- `resources/views/partials/**` (excepto parciales admin)

## Fuente de verdad

Todas las etiquetas, textos, placeholders, mensajes y confirmaciones del admin deben salir de:

- `__('admin.<key>')`

Archivos de idioma:

- `lang/es/admin.php`
- `lang/en/admin.php`

Regla: cada clave nueva en `es` debe existir también en `en` y viceversa.

## Convenciones de claves

Usar prefijos consistentes por módulo:

- `settings_*` para ajustes
- `menu_*` para menús
- `taxonomy_*` para taxonomías
- `field_*` para labels de campos
- `btn_*` para acciones
- `confirm_*` para confirmaciones
- `role_*` para etiquetas de roles

Para estructuras anidadas usar arrays, por ejemplo:

- `settings_fields.homepage_slug.label`
- `settings_groups.general`
- `settings_options.admin_locale.es`

## Regla especial para Settings (obligatoria)

Cada setting creado en `database/seeders/SettingsSeeder.php` debe tener traducción en ambos idiomas para:

- `settings_fields.<key>.label`
- `settings_groups.<group>`
- `settings_options.<key>.<option>` (si el setting es `select`)

Si falta alguna, la UI cae a fallback y puede mostrar inglés aunque el admin esté en español.

## Blade (admin)

En vistas de admin evitar texto literal visible.

Ejemplos:

```blade
<h1>{{ __('admin.settings') }}</h1>
<input placeholder="{{ __('admin.search_placeholder') }}">
<button>{{ __('admin.btn_save_changes') }}</button>
```

Para valores dinámicos (roles, tipos) usar fallback seguro:

```blade
@php
$roleKey = 'admin.role_' . $user->role;
$roleLabel = __($roleKey) !== $roleKey ? __($roleKey) : ucfirst($user->role);
@endphp
```

## JavaScript del admin

`resources/js/admin.js` no debe tener textos hardcodeados. Usar diccionario inyectado desde layout:

```blade
<script>
window.adminI18n = @json([
  'uploadFailed' => __('admin.upload_failed'),
  'chooseFile' => __('admin.choose_file'),
]);
</script>
```

Y consumirlo en JS:

```js
const t = (key, fallback = '') => window.adminI18n?.[key] ?? fallback;
alert(t('uploadFailed', 'Upload failed.'));
```

## Controladores y Requests del admin

Mensajes JSON, errores y validaciones del admin deben usar traducciones:

```php
return response()->json(['error' => __('admin.media_error_extension_not_allowed')], 422);
```

```php
public function messages(): array
{
    return [
        'file.mimetypes' => __('admin.validation_file_mimetypes'),
    ];
}
```

## Checklist antes de merge

1. No hay texto visible hardcodeado en archivos de admin.
2. Toda clave nueva existe en `lang/es/admin.php` y `lang/en/admin.php`.
3. `resources/js/admin.js` usa `window.adminI18n` para mensajes visibles.
4. No se agregaron claves `admin.*` dentro de vistas públicas.
5. Los textos de roles/tipos dinámicos tienen traducción o fallback.

## Comandos útiles de verificación

Comparar claves `es/en`:

```bash
php -r '$es=require "lang/es/admin.php"; $en=require "lang/en/admin.php"; $f=function($a,$p="") use (&$f){$r=[]; foreach($a as $k=>$v){$key=$p===""?$k:"$p.$k"; if(is_array($v)){$r+=$f($v,$key);} else {$r[$key]=1;}} return $r;}; $esf=$f($es); $enf=$f($en); echo "missing_in_en=".count(array_diff_key($esf,$enf)).PHP_EOL; echo "missing_in_es=".count(array_diff_key($enf,$esf)).PHP_EOL;'
```

Buscar uso de traducciones admin fuera del admin:

```bash
rg -n "__\('admin\.|@lang\('admin\." resources/views | rg -v "resources/views/admin|resources/views/partials/admin-bar"
```

Buscar textos sospechosos en JS admin:

```bash
rg -n "Upload failed|Loading media|No media found|Unable to load" resources/js/admin.js
```

Validar cobertura de traducciones para settings seeded:

```bash
php <<'PHP'
<?php
$es = require 'lang/es/admin.php';
$en = require 'lang/en/admin.php';
$code = file_get_contents('database/seeders/SettingsSeeder.php');
preg_match_all("/\\['key'\\s*=>\\s*'([^']+)'[^\\]]*'group'\\s*=>\\s*'([^']+)'/", $code, $m, PREG_SET_ORDER);
$get = function($arr, $dot){$parts=explode('.', $dot);$v=$arr;foreach($parts as $p){if(!is_array($v)||!array_key_exists($p,$v)) return null;$v=$v[$p];}return $v;};
foreach ($m as $row) {
  [$full, $key, $group] = $row;
  $paths = ["settings_fields.$key.label", "settings_groups.$group"];
  foreach ($paths as $path) {
    if ($get($es,$path)===null || $get($en,$path)===null) echo "MISSING: $path\n";
  }
}
PHP
```
