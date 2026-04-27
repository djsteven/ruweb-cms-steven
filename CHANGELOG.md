# Changelog

Este changelog empieza **desde el primer snapshot funcional** del proyecto y se mantiene **por commit**.

- Esquema: **SemVer** (pre-1.0): `0.MINOR.PATCH`
- Regla práctica: **MINOR** para features/cambios funcionales, **PATCH** para fixes/docs/ajustes menores.
- Nota: el commit raíz `4c90c74` sólo creó `.gitignore`/`README` (scaffolding), por eso no se considera un “release” funcional.

## Unreleased

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
