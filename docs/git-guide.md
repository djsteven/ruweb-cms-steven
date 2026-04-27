# Git Guide For Reusable Starters

## Purpose

This guide explains how to create an independent repository from a starter without keeping an unnecessary upstream coupling.

## Creating A New Project From The Starter

```bash
# 1. Clone the starter
git clone git@github.com:your-organization/starter-repo.git new-project

# 2. Enter the directory
cd new-project

# 3. Remove the original origin
git remote remove origin

# 4. Add the new project remote
git remote add origin git@github.com:your-organization/new-project.git

# 5. Push the independent repository
git push -u origin main
```

## Why Not Use A Fork By Default

- It creates unnecessary coupling between the starter and the client or product repository.
- It makes ownership and pull request flow less clear.
- It may expose implementation history or metadata that should not follow the derived project.
- It suggests an upstream synchronization model that many starter-based projects do not actually want.

Use a fork only when there is an explicit plan to keep syncing with the original repository.
