---
name: boost-authoring
description: Laravel Boost third-party package conventions for authoring AI guidelines and agent skills. Use when creating or updating files in resources/boost/.
---

# Boost Authoring

## When to use this skill

Load this skill when you need to:

- Create or update files in `resources/boost/guidelines/`
- Create or update files in `resources/boost/skills/`
- Understand the difference between guidelines and skills in the Boost ecosystem

## Overview

Laravel Boost auto-discovers third-party package resources when consumers run `php artisan boost:install`. This package ships two types of Boost resources:

| Type | Path | Loaded |
|---|---|---|
| AI Guidelines | `resources/boost/guidelines/core.blade.php` | Upfront, always present in agent context |
| Agent Skills | `resources/boost/skills/{skill-name}/SKILL.md` | On-demand, when agent activates the skill |

## Third-Party Package AI Guidelines

### File location

```
resources/boost/guidelines/core.blade.php
```

Boost loads this file automatically when users run `php artisan boost:install`.

### Format

Guidelines are `.blade.php` files. Use `@verbatim` and `<code-snippet>` for code examples:

```blade
## Package Name

This package provides [brief description of functionality].

### Features

- Feature 1: [clear & short description].
- Feature 2: [clear & short description]. Example usage:

@verbatim
<code-snippet name="How to use Feature 2" lang="php">
$result = PackageName::featureTwo($param1, $param2);
</code-snippet>
@endverbatim
```

### Content guidelines

- Provide a short overview of what the package does
- Outline required file structure or conventions
- Include example commands or code snippets for main features
- Keep concise, actionable, and focused on best practices
- Guidelines are broad and foundational — detailed patterns belong in skills
- Reference the corresponding skill for in-depth usage (e.g., "use the `kvk-api-development` skill")

## Third-Party Package Agent Skills

### File location

```
resources/boost/skills/{skill-name}/SKILL.md
```

Each skill is a folder containing a `SKILL.md` file. Boost installs skills based on user preference during `php artisan boost:install`.

### Format

Skills use the [Agent Skills format](https://agentskills.io/what-are-skills) — a Markdown file with YAML frontmatter:

```markdown
---
name: package-name-development
description: Build and work with PackageName features, including components and workflows.
---

# Package Name Development

## When to use this skill
Use this skill when working with PackageName features...

## Features

- Feature 1: [clear & short description].
- Feature 2: [clear & short description]. Example usage:

$result = PackageName::featureTwo($param1, $param2);
```

### Frontmatter

| Field | Required | Description |
|---|---|---|
| `name` | Yes | Skill identifier (kebab-case, e.g., `kvk-api-development`) |
| `description` | Yes | When this skill should be activated — include trigger phrases and keywords |

### Content guidelines

- Explain when to use the skill (trigger conditions)
- Outline file structure and conventions
- Include example code snippets for main features
- Keep focused on actionable implementation patterns
- Skills are detailed and task-specific — broad context belongs in guidelines

## Guidelines vs Skills

| Aspect | Guidelines | Skills |
|---|---|---|
| **Loaded** | Upfront, always present | On-demand, when relevant |
| **Scope** | Broad, foundational | Focused, task-specific |
| **Purpose** | Core conventions & best practices | Detailed implementation patterns |
| **Format** | Blade template (`.blade.php`) | Markdown with YAML frontmatter (`SKILL.md`) |
| **Path** | `resources/boost/guidelines/` | `resources/boost/skills/{name}/SKILL.md` |
