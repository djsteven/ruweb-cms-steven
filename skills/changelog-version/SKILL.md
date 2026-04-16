# Skill: Versionar commits en `CHANGELOG.md`

Objetivo: mantener un changelog **por commit** usando **SemVer pre-1.0** (`0.MINOR.PATCH`) hasta llegar a `1.0.0`.

## Reglas de bump (por commit)

- **MINOR** (`0.(x+1).0`) si el commit agrega o cambia funcionalidades del producto (UI/UX, módulos, endpoints, features).
- **PATCH** (`0.x.(y+1)`) si el commit es fix, refactor sin impacto funcional, docs, chores, CI/deploy, estilos.
- Si hay duda, preferir **PATCH**.

## Cómo actualizar `CHANGELOG.md`

- Mantener `## Unreleased` arriba.
- Agregar una entrada nueva arriba del todo (debajo de `Unreleased`):
  - `## <version> - <YYYY-MM-DD> (<short-sha>)`
  - 2–6 bullets claros, orientados a impacto (qué se habilita o qué se arregla).
- Categorías opcionales dentro de la versión (si aporta claridad): `Added`, `Changed`, `Fixed`, `Docs`.
- No incluir trabajo futuro ni “v1”; sólo lo que ya está en `main`.

## Checklist rápido

- ¿Esto cambia lo que un usuario puede hacer? => MINOR
- ¿Sólo corrige/mejora sin nuevas capacidades? => PATCH
- ¿Docs/deploy/CI? => PATCH

