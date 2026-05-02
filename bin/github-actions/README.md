# GitHub Actions templates

The two workflows in this directory are ready-to-install but live here
instead of `.github/workflows/` because the agent that wrote them runs
inside a security harness that blocks direct writes to workflow files.

## Install

```bash
mkdir -p .github/workflows
cp bin/github-actions/upstream-check.yml .github/workflows/
cp bin/github-actions/release.yml .github/workflows/
git add .github/workflows/upstream-check.yml .github/workflows/release.yml
git commit -m "ci: add upstream-sync + release workflows"
git push
```

That's it — the workflows pick up automatically.

## What each one does

### `upstream-check.yml`

Scheduled (Mondays 09:00 UTC) + on-demand runner of `bin/check-upstream
.sh`. The first run opens an issue labelled `upstream-sync` with the
diff report; subsequent runs comment on the same issue so the timeline
of drift stays in one place. Closes long-term roadmap §5.

Permissions used: `contents: read`, `issues: write`. The `GITHUB_TOKEN`
that ships with every Actions run is sufficient — no extra secrets.

### `release.yml`

Manual `workflow_dispatch` trigger that takes a semver version
(e.g. `1.3.4`), validates the format, asserts the tag doesn't already
exist on origin, bumps `style.css` (`Version: …`) and `readme.txt`
(`Stable tag: …`), commits as the GitHub Actions bot, then pushes the
new tag.

The release zip itself is still built locally with `bin/release.sh
1.3.4` and attached to the GitHub Release with `gh release create`. The
workflow only handles the version-bump + tag — that's the part that
benefits from being automated.

Permissions used: `contents: write`. Same `GITHUB_TOKEN`.

## Why these aren't already installed

The harness running the agent that authored them flags any write to
`.github/workflows/*.yml` as "potential workflow injection". Both
templates have been reviewed for the relevant patterns (no `${{ ... }}`
of untrusted event payload interpolated into shell, all user inputs
read via `env:` and quoted) — so the install is safe. They live here
as a one-`cp`-away artifact rather than in the conversation history.
