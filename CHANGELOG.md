# Changelog

Este changelog empieza **desde el primer snapshot funcional** del proyecto y se mantiene **por commit**.

- Esquema: **SemVer** (pre-1.0): `0.MINOR.PATCH`
- Regla práctica: **MINOR** para features/cambios funcionales, **PATCH** para fixes/docs/ajustes menores.
- Nota: el commit raíz `4c90c74` sólo creó `.gitignore`/`README` (scaffolding), por eso no se considera un “release” funcional.

## Unreleased
- Agregado `CHANGELOG.md` inicial (historial desde `0.0.0` hasta `0.9.0`).
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
