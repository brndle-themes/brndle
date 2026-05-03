# Brndle Documentation

Reference docs for working with the Brndle theme — both for humans and for AI agents creating content programmatically.

## What's here

| File | For | Use when |
|---|---|---|
| **[AI-USAGE-GUIDE.md](./AI-USAGE-GUIDE.md)** | AI agents (Claude, GPT, others) and humans scripting content | You need to publish blogs / landing pages / configure sites without opening the editor by hand. Covers all 18 blocks, recipes for common page types, settings + REST API, complete worked examples. **Start here for any AI-driven workflow.** |
| **[blocks/](./blocks/)** | Reference per block | You're working with one specific block and want the deep spec — every attribute, every variant, every edge case. |
| **[recipes/](./recipes/)** | Copy-paste templates | You need a working starting point for a common page type (SaaS landing, blog post with code samples, changelog, etc.). |

## Companion files (not in `docs/`)

| File | What it is |
|---|---|
| `CLAUDE.md` (theme root) | Theme architecture for Claude Code — Blade syntax rules, CSS color tokens, build commands. |
| `.claude/skills/brndle-pages.md` | Claude Code skill — same content as `AI-USAGE-GUIDE.md` but exposed as an invocable skill. |
| `plans/2026-05-04-v2.1-editorial-blocks.md` | The v2.1 implementation plan + spec. Read this before extending the editorial blocks. |
| `plans/2026-05-03-theme-audit-roadmap.md` | The post-1.9.2 audit roadmap — defines v2.0 / v2.1 / v2.2 bundles. |
| `readme.txt` | WordPress.org readme — feature list, changelog, install instructions. |

## Quick map

If you just want to **publish a page or blog**, jump to **[AI-USAGE-GUIDE.md](./AI-USAGE-GUIDE.md)**. It's self-contained and covers 95% of the work.

If you're **building a new block** or **changing how an existing block renders**, read:

1. The block's spec under `docs/blocks/`.
2. The relevant plan under `plans/`.
3. `CLAUDE.md` for the build / cache / token rules.

If you're **configuring a site** (colors, fonts, header style, footer style), the REST API is documented in **[AI-USAGE-GUIDE.md → Site Configuration](./AI-USAGE-GUIDE.md#site-configuration-rest-api)**.
