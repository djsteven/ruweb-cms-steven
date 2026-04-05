# MCP Integration

## Overview

El CMS expone un servidor MCP real sobre HTTP que implementa el protocolo JSON-RPC 2.0.
Cuando Claude (u otro agente) se conecta, el servidor describe automáticamente todas sus herramientas: nombre, descripción y esquema de parámetros. El agente usa esa información para decidir qué tool llamar y cuándo.

Hay dos superficies disponibles:

| Superficie | Base URL | Uso |
|---|---|---|
| **MCP Server (JSON-RPC 2.0)** | `POST /mcp/rpc` | Agentes (Claude, etc.) |
| **REST API** | `GET/POST/PATCH /mcp/*` | Integraciones directas, scripts |

Ambas comparten el mismo middleware de autenticación y la misma lógica de negocio.

---

## Autenticación

Hay dos mecanismos de autenticación, ambos usan el header `Authorization: Bearer <token>`.

### OAuth 2.0 (claude.ai custom connectors)

El servidor implementa OAuth 2.0 Authorization Code + PKCE. Es el método requerido para conectar claude.ai como custom connector.

**Endpoints:**

| Endpoint | Descripción |
|---|---|
| `GET /.well-known/oauth-authorization-server` | Metadata de discovery |
| `GET /authorize` | Pantalla de login (redirige desde claude.ai) |
| `POST /authorize` | Procesa el login y emite un authorization code |
| `POST /token` | Intercambia el code por un access token (válido 8 horas) |

**Configuración en `.env`:**

```env
OAUTH_CLIENT_ID=<id que elegiste en claude.ai>
OAUTH_CLIENT_SECRET=<secret que elegiste en claude.ai>
OAUTH_ALLOWED_REDIRECT_URIS=https://claude.ai/api/mcp/auth_callback
```

Los valores del client ID y secret se muestran en `Admin > Claude MCP` para copiarlos al configurar el connector en claude.ai.

### API Key (clientes directos)

Cada usuario puede tener **una API key activa**, gestionada en `Admin > Profile`.

```http
Authorization: Bearer flaxt_mcp_xxx...
```

- Generar una nueva key invalida la anterior.
- Revocar la key elimina el acceso del agente de inmediato.
- La key autentica al usuario propietario — el agente actúa con sus permisos.

### Roles

| Operación | admin | editor |
|---|---|---|
| Leer pages, posts, media, settings, menus | ✓ | ✓ |
| Crear/editar/publicar pages y posts | ✓ | ✓ |
| Actualizar media y menus | ✓ | ✓ |
| Crear/eliminar menus | ✓ | — |
| Actualizar settings | ✓ | — |

---

## MCP Server (JSON-RPC 2.0)

### Endpoint

```
POST /mcp/rpc
Content-Type: application/json
Authorization: Bearer <api_key>
```

### Protocolo

Todos los mensajes siguen JSON-RPC 2.0:

```json
// Request
{
  "jsonrpc": "2.0",
  "id": 1,
  "method": "tools/call",
  "params": {
    "name": "pages_list",
    "arguments": { "status": "draft" }
  }
}

// Response
{
  "jsonrpc": "2.0",
  "id": 1,
  "result": {
    "content": [
      { "type": "text", "text": "{ ... }" }
    ]
  }
}
```

### Métodos disponibles

| Método | Descripción |
|---|---|
| `initialize` | Handshake inicial. Devuelve `protocolVersion`, `capabilities` y `serverInfo`. |
| `tools/list` | Devuelve todas las tools con su `inputSchema`. El agente lo llama una sola vez al conectarse. |
| `tools/call` | Ejecuta una tool por nombre. |

### Tools disponibles

#### Pages
| Tool | Descripción | Admin | Editor |
|---|---|---|---|
| `pages_list` | Lista páginas con filtros de estado y búsqueda | ✓ | ✓ |
| `pages_get` | Detalle completo con content_json | ✓ | ✓ |
| `pages_create_draft` | Crea una página en draft | ✓ | ✓ |
| `pages_update_draft` | Actualiza una página y la pone en draft | ✓ | ✓ |
| `pages_publish` | Publica una página | ✓ | ✓ |

#### Posts
| Tool | Descripción | Admin | Editor |
|---|---|---|---|
| `posts_list` | Lista posts con filtros | ✓ | ✓ |
| `posts_get` | Detalle completo con contenido | ✓ | ✓ |
| `posts_create_draft` | Crea un post en draft | ✓ | ✓ |
| `posts_update_draft` | Actualiza un post y lo pone en draft | ✓ | ✓ |
| `posts_publish` | Publica un post | ✓ | ✓ |

#### Media
| Tool | Descripción | Admin | Editor |
|---|---|---|---|
| `media_list` | Lista archivos con filtros de tipo y búsqueda | ✓ | ✓ |
| `media_get` | Detalle con URL y metadatos | ✓ | ✓ |
| `media_update_metadata` | Actualiza alt y título | ✓ | ✓ |

#### Settings
| Tool | Descripción | Admin | Editor |
|---|---|---|---|
| `settings_list` | Todos los settings agrupados | ✓ | ✓ |
| `settings_get` | Valor de un setting por clave | ✓ | ✓ |
| `settings_update` | Actualiza un setting (allowlist) | ✓ | — |

#### Menus
| Tool | Descripción | Admin | Editor |
|---|---|---|---|
| `menus_list` | Lista menús con cantidad de items | ✓ | ✓ |
| `menus_get` | Árbol completo de items anidados | ✓ | ✓ |
| `menus_create` | Crea un menú nuevo | ✓ | — |
| `menus_update` | Actualiza nombre, slug o location | ✓ | ✓ |
| `menus_sync_items` | Reemplaza todos los items de forma atómica | ✓ | ✓ |
| `menus_delete` | Elimina un menú y sus items | ✓ | — |

---

## REST API

Las rutas REST siguen disponibles para integraciones directas.

### Pages
- `GET /mcp/pages`
- `GET /mcp/pages/{page}`
- `POST /mcp/pages/drafts`
- `PATCH /mcp/pages/{page}/draft`
- `POST /mcp/pages/{page}/publish`

### Posts
- `GET /mcp/posts`
- `GET /mcp/posts/{post}`
- `POST /mcp/posts/drafts`
- `PATCH /mcp/posts/{post}/draft`
- `POST /mcp/posts/{post}/publish`

### Media
- `GET /mcp/media`
- `GET /mcp/media/{media}`
- `PATCH /mcp/media/{media}/metadata`

### Settings
- `GET /mcp/settings`
- `GET /mcp/settings/{key}`
- `PATCH /mcp/settings/{key}` (admin only, allowlist)

### Menus
- `GET /mcp/menus`
- `GET /mcp/menus/{menu}`
- `POST /mcp/menus` (admin only)
- `PATCH /mcp/menus/{menu}`
- `PUT /mcp/menus/{menu}/items`
- `DELETE /mcp/menus/{menu}` (admin only)

### Settings Update Allowlist

Las claves editables por MCP se configuran en `config/cms.php`:

```php
'mcp' => [
    'settings_writable_keys' => ['site.name', 'site.description', ...],
],
```

---

## Extender el MCP

Para agregar nuevas herramientas al MCP server seguís tres pasos. No hay que tocar nada del protocolo.

### 1. Implementar la lógica en `McpController`

Agregá los métodos de negocio en `app/Http/Controllers/McpController.php`, siguiendo el patrón existente: validación con `Validator::make`, autorización con `$this->authorize()` o `$this->authorizeCmsEditor()`, y serialización con un método `serialize*` dedicado.

```php
// McpController.php
public function commentsList(Request $request): JsonResponse
{
    $this->authorizeCmsEditor($request);

    $comments = Comment::query()->latest()->paginate(20);

    return response()->json([
        'data' => $comments->getCollection()->map(fn ($c) => $this->serializeComment($c))->values(),
        'meta' => [ /* paginación */ ],
    ]);
}

protected function serializeComment(Comment $comment): array
{
    return [
        'id'      => $comment->id,
        'body'    => $comment->body,
        // ...
    ];
}
```

### 2. Registrar las tools en `ToolRegistry`

Agregá un método privado en `app/Mcp/ToolRegistry.php` y sumalo al array de `all()`:

```php
// ToolRegistry.php

public static function all(): array
{
    return [
        ...self::pageTools(),
        ...self::postTools(),
        // ...
        ...self::commentTools(), // ← agregar acá
    ];
}

private static function commentTools(): array
{
    return [
        [
            'name'        => 'comments_list',
            'description' => 'Lista los comentarios del blog. El agente usa esta descripción para saber cuándo llamar la tool.',
            'inputSchema' => [
                'type'       => 'object',
                'properties' => [
                    'post_id' => ['type' => 'integer', 'description' => 'Filtrar por post.'],
                ],
            ],
        ],
    ];
}
```

**Las descripciones son las instrucciones del agente.** Cuanto más específicas, mejor decide Claude cuándo y cómo usar cada tool.

### 3. Agregar el handler en `McpServerController`

En `app/Http/Controllers/McpServerController.php`, sumá la entrada al array de `resolveHandler()`:

```php
// McpServerController.php — dentro de resolveHandler()

'comments_list' => fn ($req, $args) =>
    $this->mcp->commentsList($this->synth($req, $args)),

// Si la tool necesita un modelo por ID:
'comments_get' => fn ($req, $args) =>
    $this->mcp->commentsGet(
        $this->synth($req),
        Comment::findOrFail($args['comment_id'])
    ),
```

El helper `synth()` crea un `Request` sintético con los argumentos del agente y el usuario autenticado del request original. El helper `without()` elimina los IDs de ruta del body antes de pasarlo al controller.

### Resumen

```
Nueva feature
    │
    ├─ app/Http/Controllers/McpController.php   → lógica + serialización
    ├─ app/Mcp/ToolRegistry.php                  → definición + inputSchema
    └─ app/Http/Controllers/McpServerController.php → dispatch handler
```

---

## Ejemplo rápido

```bash
# Handshake
curl -X POST http://localhost:8000/mcp/rpc \
  -H "Authorization: Bearer $MCP_API_KEY" \
  -H "Content-Type: application/json" \
  -d '{"jsonrpc":"2.0","id":1,"method":"initialize","params":{}}'

# Ver tools disponibles
curl -X POST http://localhost:8000/mcp/rpc \
  -H "Authorization: Bearer $MCP_API_KEY" \
  -H "Content-Type: application/json" \
  -d '{"jsonrpc":"2.0","id":2,"method":"tools/list","params":{}}'

# Llamar una tool
curl -X POST http://localhost:8000/mcp/rpc \
  -H "Authorization: Bearer $MCP_API_KEY" \
  -H "Content-Type: application/json" \
  -d '{
    "jsonrpc": "2.0",
    "id": 3,
    "method": "tools/call",
    "params": {
      "name": "pages_list",
      "arguments": { "status": "draft" }
    }
  }'
```

> `$MCP_API_KEY` puede ser tanto una API key del perfil (`flaxt_mcp_xxx`) como un OAuth access token emitido por el flujo de claude.ai.
