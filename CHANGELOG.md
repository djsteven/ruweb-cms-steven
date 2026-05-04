# Changelog

Este changelog empieza **desde el primer snapshot funcional** del proyecto y se mantiene **por commit**.

- Esquema: **SemVer** (pre-1.0): `0.MINOR.PATCH`
- Regla práctica: **MINOR** para features/cambios funcionales, **PATCH** para fixes/docs/ajustes menores.
- Nota: el commit raíz `4c90c74` sólo creó `.gitignore`/`README` (scaffolding), por eso no se considera un “release” funcional.

## Unreleased

## 0.21.0 - 2026-05-04

- La pantalla de `Profile` fue refactorizada para usar tabs separadas de Información y Seguridad.
- Información de cuenta simplificada a `Full name`, `Email` y `Role` de solo lectura.
- Cambio de contraseña movido a una tab de Seguridad dedicada con una UI más ordenada.
- La gestión de `MCP API Key` se mueve desde `/admin/profile` a `/admin/claude-mcp`.

## 0.20.0 - 2026-05-04 (d968920)

- Nueva sección admin `Developer tools` con tabs de diagnóstico del sistema y migración entre entornos.
- Exportación portable de snapshots `.appbackup` con `manifest`, `checksums`, dump JSONL por tabla y `uploads` públicos.
- Restore seguro con validación de ZIP, checksums, tablas compatibles, pre-backup automático, maintenance mode y rollback de uploads.
- Nuevos comandos Artisan `snapshot:create` y `snapshot:restore` para operar backups grandes por CLI.
- Documentación de deploy actualizada para separar flujo de código por Git y datos dinámicos por snapshots.

## 0.19.2 - 2026-04-28 (7bcb7ae)

- Completado el seguimiento por commit del changelog resolviendo el SHA pendiente de la entrada anterior.

## 0.19.1 - 2026-04-28 (c2a9737)

- Protegida la suite de tests contra config cache local, limpiando `bootstrap/cache` antes de bootear PHPUnit.
- Evitado que `RefreshDatabase` pueda correr contra la base MySQL local cuando existe `config.php` generado por `php artisan optimize`.

## 0.19.0 - 2026-04-28 (ac38c2b)

- Agregado botón de refresco de caché de Laravel en la admin bar pública, visible solo para admins.
- Corregido el idioma de la admin bar pública para que use `admin_locale` también fuera de rutas `/admin`.
- Actualizado `.env.example` para alinearlo con las variables reales de OAuth, mail e imágenes del proyecto.
- Actualizado `reqs.md` para reflejar la nueva capacidad de refresco de caché y el comportamiento de localización del admin bar.

## 0.18.0 - 2026-04-28 (448cb32)

- Agregadas acciones de borrado directo en las tablas de Pages y Posts del admin.
- Cada fila ahora expone la convención completa de acciones: abrir si existe detail público, editar y borrar con confirmación.
- Skill y documentación de colecciones actualizadas para exigir esta convención en nuevos listados admin.

## 0.17.0 - 2026-04-28 (dd27976)

- Tema de acento del sistema cambiado de verde esmeralda a azul `sky-600`.
- Botones, tabs activas, focus states, badges y enlaces del admin fueron alineados al nuevo color de acento.
- Ajustado el glow del login y otros acentos derivados para mantener coherencia visual con el nuevo tema.

## 0.16.3 - 2026-04-28 (63266b4)

- Documentación de arquitectura y guías alineada con las capacidades reales del sitio actual.
- Referencias a comportamientos no implementados fueron ajustadas para evitar desfasajes entre docs y producto.

## 0.16.2 - 2026-04-28 (9a9ef3f)

- Rebrand del starter desde Flaxt a Rüweb en configuración, textos visibles, seeders y documentación.
- Defaults de instalación actualizados para usar `ruweb-cms` y `admin@ruweb.local` en lugar de los valores anteriores.
- Prefijo de API keys MCP y nombre expuesto por el servidor MCP alineados con la nueva identidad del proyecto.

## 0.16.1 - 2026-04-28 (f43a785)

- Starter público simplificado para usar únicamente `favicon.ico` por defecto.
- Eliminadas referencias rotas a variantes de favicon que ya no existen en `public`.
- El favicon configurado desde Settings ahora publica el MIME correcto según el archivo real subido por el admin.
- Limpieza de assets públicos sobrantes del starter, incluyendo el logo no utilizado.

## 0.16.0 - 2026-04-27 (5370c6c)

- Contrato editorial común para Pages y Posts mediante interfaces `Publishable`, `Seoable`, `Mediable` y `Previewable`.
- Trait compartido para la regla de publicación estándar (`status = published` y `published_at <= now()`).
- SEO público actualizado para consumir entidades `Seoable` sin depender del tipo concreto de contenido.
- Documentación del contrato editorial y guías enlazadas para nuevas colecciones.
- Cobertura de tests para contratos, publicación y metadata SEO de Pages y Posts.

## 0.15.1 - 2026-04-27 (e080e07)

- Live editor: eliminado auto-guardado en localStorage; los cambios solo persisten al guardar explícitamente.
- Live editor: aviso nativo del navegador al intentar salir con cambios pendientes.

## 0.15.0 - 2026-04-27 (d6e3a46)

- Paginación por páginas en el modal de la media library, reemplazando el infinite scroll anterior.

## 0.14.0 - 2026-04-27 (97bf5c5)

- Configuración SEO consolidada en la tab General de Settings (antes dispersa en tab propia).
- Test de email movido a un controller dedicado.

## 0.13.0 - 2026-04-27 (a3e38d2)

- Alertas del panel admin estandarizadas visualmente.
- La tab activa en Settings persiste entre recargas de página.

## 0.12.2 - 2026-04-27 (0538ecd)

- Scrollbars del live editor estilizados para coincidir con el tema oscuro.

## 0.12.1 - 2026-04-21 (b86aec6)

- Documentación del warning de persistencia de `content_json` en el live editor.

## 0.12.0 - 2026-04-17 (15f3f67)

- Sistema de email transaccional con Brevo: transport HTTP API propio (sin paquetes externos), solo requiere pegar una API key en el admin para funcionar.
- Tab **Email** en Settings con API key cifrada, from address/name, master switch, acordeón de instrucciones paso a paso y botón "Enviar email de prueba".
- Password reset completo desde el panel admin (flujo forgot → email → reset con vistas propias).
- Welcome email al crear usuarios: el nuevo usuario recibe sus credenciales por email y su cuenta queda verificada de inmediato.
- `User` implementa `MustVerifyEmail`; backfill automático en seeder para usuarios existentes.
- Comando `php artisan mail:test {email}` para verificar la configuración desde CLI.
- Documentación actualizada: `settings.md` (nuevo tipo `password` y grupo `email`), `architecture.md` (nuevos directorios `app/Mail/`, `emails/`), `maintenance.md` (comando `mail:test`).

## 0.11.0 - 2026-04-17 (4483642)
- Panel de analytics rediseñado con navegación por tabs (Google tag / Meta Pixel / Search Console) con indicador de estado de configuración por tab.
- Botón "Guardar cambios" promovido al header para visibilidad inmediata sin scroll.
- UI estandarizada en los tres tabs: mismo patrón de campo + resumen + acordeón "Cómo configurarlo" colapsado por defecto.
- Añadidas guías paso a paso para Google tag y Meta Pixel, equivalentes a las que ya tenía Search Console.
- Eliminadas cajas anidadas redundantes (valor guardado, meta tag generado, acordeón DNS).
- Tono de avisos informativos cambiado de warning amber a gris neutral.

## 0.10.0 - 2026-04-16 (6d4e0c9)
- Nueva sección `Analytics` en el admin para centralizar configuración de Google tag, Meta Pixel y Search Console.
- El frontend público ahora inyecta automáticamente Google tag, Meta Pixel y el meta tag de verificación de Search Console a partir de IDs/tokens normalizados.
- Validación y copy del panel unificadas para pedir solo IDs/tokens, sin aceptar snippets HTML/JS pegados.
- Documentación SEO actualizada para reflejar la nueva inyección centralizada de analytics y verificación.
- Cobertura de tests agregada para acceso admin, validación de inputs, renderizado público y defaults de settings.

## 0.9.3 - 2026-04-16 (9a9049b)
- Skill del changelog actualizada para versionar el commit actual sin depender de un SHA previo.
- Nuevo flujo: el siguiente commit completa el SHA faltante de la entrada anterior y crea una nueva entrada sin SHA.
- `Unreleased` queda reservado solo para trabajo todavía no committeado.

## 0.9.2 - 2026-04-16 (dfd5374)
- Fix en subidas múltiples: el lote ahora se valida completo antes de persistir archivos o registros.
- Fix en el live editor: publicar ya no deja el estado `published` pegado si el guardado falla.
- Ajustado el pipeline de media para tratar GIF como raster soportado en dimensiones y health metrics, sin variantes ni conversión.

## 0.9.1 - 2026-04-16 (765bc58)
- Agregado `CHANGELOG.md` inicial con historial desde `0.0.0` hasta `0.9.0`.
- Agregada skill de repo para estandarizar bumps y formato del changelog.

## 0.9.0 - 2026-04-14 (a53af7c)
- Pipeline de optimización de imágenes.
- Dashboard de “media health” para detectar/seguir issues del media.

## 0.8.0 - 2026-04-14 (7807dd3)
- Motor del live editor estandarizado.
- Modo de preview para tablet.

## 0.7.0 - 2026-04-14 (36b84a3)
- Infinite scroll en la media library.
- Subidas multi-archivo en la media library.

## 0.6.2 - 2026-04-14 (f7c2f99)
- Previews de media en admin con `object-contain` para mejor encuadre.

## 0.6.1 - 2026-04-14 (69d7724)
- Fix a drag & drop uploads en media.
- Remoción de previews de imágenes en el flujo de subida.

## 0.6.0 - 2026-04-14 (f6f55ff)
- Limpieza de settings: removidos social/footer.
- Logo del admin enlaza al homepage.

## 0.5.0 - 2026-04-14 (c477a46)
- Localización de settings del admin y mensajes de media (ES/EN).

## 0.4.0 - 2026-04-14 (fddc796)
- Homepage configurable vía settings.
- Seeding y traducciones del admin para homepage.

## 0.3.2 - 2026-04-14 (fee7e77)
- README: instrucciones de deploy/local setup.

## 0.3.1 - 2026-04-05 (9cd3641)
- Docs: guía de Git para workflow de proyecto con cliente.

## 0.3.0 - 2026-04-05 (87ef959)
- Generación dinámica de `sitemap.xml`.
- Guía SEO asociada.

## 0.2.0 - 2026-04-05 (1dae4e7)
- Admin bar estilo WordPress para usuarios autenticados.

## 0.1.0 - 2026-04-04 (73a42b0)
- OAuth 2.0 + PKCE para custom connector de `claude.ai`.

## 0.0.1 - 2026-04-04 (ae14124)
- Fix deploy cPanel: pin de PHP 8.3 (platform) y ajuste de `siteName` en vistas de auth.

## 0.0.0 - 2026-04-04 (e89c18f)
- Base Laravel + Vite (estructura, config, dependencias).
- Admin panel con auth, perfiles y control de acceso (policies/roles).
- Módulos CMS: Pages, Posts/Blog, Media, Menús, Taxonomías y Settings.
- Media library con modelo de Media + relación “mediables” para adjuntar media a contenido.
- API/MCP: servidor/controladores + registry de tools + middleware de API key para exponer capacidades del CMS.
- Comandos CLI: instalación del CMS, reset de password y auditoría de media huérfano.
- DB: migraciones y seeders (admin user, settings, menús).
- Documentación inicial (arquitectura, modelo de contenido, guías de colecciones/plantillas/menús/settings/taxonomías, mantenimiento).
