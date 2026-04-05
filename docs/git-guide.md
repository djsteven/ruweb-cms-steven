# Git Guide for Client Projects

## Creating a New Project from the Starter

To create a new client project based on Flaxt CMS **without forking** (keeping the repo independent from the start):

```bash
# 1. Clone the starter
git clone git@github.com:your-username/flaxt-cms.git client-name

# 2. Enter the directory
cd client-name

# 3. Remove connection to the original repo
git remote remove origin

# 4. Add the new client remote
git remote add origin git@github.com:your-username/client-name.git

# 5. Initial push to the new repo
git push -u origin main
```

## Why Not Use a Fork?

- **Noise**: Cross-pull requests with the base repo
- **Public visibility**: If the fork is public, client code gets exposed
- **Confusion**: Makes it harder to identify which repo is the "real" one
- **Dependency**: Maintains unwanted ties to the original project

This workflow gives you a clean repo with all the starter's history, but **completely independent** from day one.
