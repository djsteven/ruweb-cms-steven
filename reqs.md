# Requerimientos pendientes de lanzamiento

## Críticos ❌

- [ ] Generar sitemap.xml dinámico y enviarlo a Google Search Console
- [ ] Integrar Google Analytics (GA4 tracking code en el layout)
- [ ] Configurar SMTP con Brevo en `.env` (actualmente usa driver `log`)
- [ ] Forzar HTTPS en Laravel (middleware + `SESSION_SECURE_COOKIE=true`)
- [ ] Agregar security headers (HSTS, X-Frame-Options, X-Content-Type-Options, CSP)
- [ ] Configurar sistema de backups automáticos

## Pendientes ❌

- [ ] Implementar CAPTCHA en formularios públicos
- [ ] Implementar CloudFlare (a nivel DNS)
- [ ] Agregar texto "Desarrollado por Rugertek" en el footer
- [ ] Crear páginas de Política de Privacidad y Términos y Condiciones
- [ ] Ocultar URL de login (actualmente `/admin/login`)
- [ ] Realizar pruebas de rendimiento (PageSpeed Insights / GTmetrix)

## Parcialmente cubiertos ⚠️

- [ ] Ajustar zona horaria, idioma y formato de fecha al proyecto del cliente (actualmente UTC/en)
- [ ] Verificar y ajustar email/username del usuario administrador (no usar "admin")
- [ ] Personalizar robots.txt según necesidades del proyecto

## Tareas manuales / de proceso

- [ ] Decidir con el cliente el plan de actualizaciones automáticas y documentarlo
- [ ] Enviar accesos al cliente por correo
- [ ] Desactivar modo mantenimiento (si está activo)
- [ ] Comprobar ortografía y reemplazar textos genéricos (lorem ipsums, etc.)
- [ ] Revisar que todas las páginas y enlaces funcionen correctamente
- [ ] Crear el sitio con el correo de administración de Rugertek
- [ ] Probar formularios de contacto y verificar recepción de correos
- [ ] Probar proceso de compra (si aplica)
- [ ] Verificar que el sitio esté indexado por Google
- [ ] Verificar diseño responsivo en desktop, tablet y mobile
- [ ] Comprobar menús, botones y enlaces internos

## Equivalencias Laravel de plugins WordPress

---

**Rank Math** (SEO):
- Cubierto: meta titles/descriptions dinámicos, OG tags, Twitter cards, canonical URLs, favicon, viewport, lang tag (`seo-meta.blade.php` + `ContentHelper.php`)
- Pendiente: generación de sitemap.xml, datos estructurados JSON-LD (Schema.org), gestión de redirecciones 301, control de noindex/nofollow por página desde el admin, editor de robots.txt en el panel

---

**Site Kit** (Google Analytics / Search Console):
- Cubierto: nada
- Pendiente: insertar GA4 tracking snippet en el layout, mostrar métricas de Search Console y PageSpeed dentro del admin, integración con Google Tag Manager

---

**WP Activity Log** (registro de actividad):
- Cubierto: logs de sistema vía Laravel (`storage/logs/`), pero solo errores y excepciones
- Pendiente: registrar acciones de usuarios en el admin (login, logout, creación/edición/borrado de contenido, cambios de configuración), rastrear IP y user agent por acción, alertas por actividad sospechosa, tabla `activity_logs` en base de datos consultable desde el panel

---

**ShortPixel** (optimización de imágenes):
- Cubierto: validación de mime-type, límite de tamaño en uploads, escaneo de SVG maliciosos
- Pendiente: compresión automática de imágenes al subir (lossy/lossless), conversión a WebP/AVIF, generación de múltiples tamaños/srcset, lazy loading en el frontend, integración con CDN

---

**Wordfence** (seguridad):
- Cubierto: rate limiting en login (5 intentos / 60s), CSRF en formularios, protección SQL injection via Eloquent, validación de uploads, hashing bcrypt (12 rounds), autenticación OAuth2
- Pendiente: security headers HTTP (HSTS, X-Frame-Options, X-Content-Type-Options, CSP), 2FA para el admin, bloqueo de IPs por intentos fallidos, CAPTCHA en formularios públicos, monitoreo de integridad de archivos, WAF a nivel aplicación, rate limiting en rutas API

---

**WPS Hide Login** (ocultar URL de login):
- Cubierto: la ruta de login es `/admin/login` (no es el estándar `/wp-login.php`, ya hay diferenciación)
- Pendiente: hacer la URL de login configurable desde `.env` (ej. `LOGIN_PATH=/acceso`), bloquear/redirigir accesos directos a `/admin/login` si se cambia la ruta, middleware que devuelva 404 en la ruta por defecto si se usa una personalizada

---

**LiteSpeed Cache** (caché y rendimiento):
- Cubierto: caché de objetos/datos vía Laravel Cache (driver database), assets compilados y versionados con Vite
- Pendiente: full-page cache (caché de respuestas HTML completas), minificación de CSS/JS en producción (configurar Vite para prod), lazy loading de imágenes en el frontend, integración con CDN para assets estáticos, caché de consultas frecuentes a base de datos, headers de caché HTTP (`Cache-Control`, `ETag`) en respuestas
