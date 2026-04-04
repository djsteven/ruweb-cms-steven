# Custom Modules Guide

This starter intentionally keeps the core small. Project-specific logic should live in custom modules.

## Recommended structure

```
app/
├── Modules/
│   ├── Blog/
│   │   ├── Http/
│   │   │   ├── Controllers/
│   │   │   └── Requests/
│   │   ├── Models/
│   │   ├── Policies/
│   │   └── Services/
│   └── {YourModule}/
```

If your team prefers staying strictly in default Laravel folders, keep a naming prefix (`Blog*`, `Catalog*`) and group files by domain.

## Rules

- Keep generic starter behavior in core (`pages`, `settings`, `media`, auth, base middleware).
- Keep domain/business behavior in module files.
- Avoid changing core conventions unless needed for multiple projects.
- Document every module with:
  - domain purpose
  - routes
  - models/tables
  - role rules
  - extension points

## Module checklist

- [ ] migration + model
- [ ] admin requests + controller
- [ ] public controller/routes (if needed)
- [ ] policy permissions
- [ ] blade views
- [ ] feature tests
- [ ] docs section
