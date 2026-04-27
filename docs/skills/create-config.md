# Create Config Skill

## How To Use

This file is not meant to be copied into the prompt.

It is meant to be referenced directly, for example:

```md
[create-config.md](docs/skills/create-config.md)

I want the maximum responsive image size to be configurable from settings
and I want an option to disable variant generation per environment.
```

When this file is referenced, the agent should treat the rest of the user's message as the configuration change specification to implement.

## Instruction For The Agent

If this file was referenced in the prompt:

1. Use these canonical documents as the source of truth:
   - [architecture.md](../architecture.md): architectural boundaries and responsibilities
   - [settings.md](../settings.md): runtime settings model
   - [admin-translations-guide.md](../admin-translations-guide.md): admin translations for new labels or settings
   - [image-strategy.md](../image-strategy.md): when the change affects media or images
   - [mcp.md](../mcp.md): when the change affects integrations or exposed capabilities
2. Treat all text after the reference to this file as the functional requirement.
3. Decide explicitly whether the change should live in `config/*.php`, in `settings`, or in both.
4. If you add new settings, create the required definition, grouping, options for selectable values, and translations.
5. If the change affects cross-cutting behavior, also adjust the integration points that depend on that configuration.
6. Treat sensitive runtime values as protected settings and avoid rendering plaintext secrets back into admin forms.
7. Keep the design agnostic and reusable; avoid hardcoding project-specific details.
8. If the requirement is ambiguous, choose the option most consistent with the reusable architecture.

## What The User Should Describe After Referencing This File

- what should be configurable
- whether it should be editable at runtime or only in code
- which module or feature it affects
- any security, visibility, or integration constraints
