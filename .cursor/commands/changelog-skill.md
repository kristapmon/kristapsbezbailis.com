---
name: changelog
description: Generate a short, scannable, ADHD-friendly changelog entry with proper version numbering. Use when the user says "changelog", "log this", "what changed", "bump version", or after completing a feature/fix/refactor.
---

# Changelog Entry Generator

Generate a concise, visually scannable changelog entry with a version number. Every entry must be **short enough to read in under 10 seconds**.

---

## Versioning — How It Works

Versions follow **Semantic Versioning**: three numbers separated by dots.

```
v MAJOR . MINOR . PATCH
  1     .  3   .  7
```

**Think of it like a street address:**
- **MAJOR** = the street (big, disruptive change — things break if people don't pay attention)
- **MINOR** = the house (new room added, but the house still works the same)
- **PATCH** = a repaired window (fixed something, nothing new)

### Which Number Do I Bump?

| I just...                                      | Bump    | Example            |
|------------------------------------------------|---------|--------------------|
| Fixed a bug, typo, or broken thing             | PATCH   | 1.3.7 → 1.3.**8** |
| Improved performance without changing behavior | PATCH   | 1.3.8 → 1.3.**9** |
| Added a new feature that doesn't break anything| MINOR   | 1.3.9 → 1.**4**.0 |
| Added a new page, endpoint, or setting         | MINOR   | 1.4.0 → 1.**5**.0 |
| Removed or renamed something users depend on   | MAJOR   | 1.5.0 → **2**.0.0 |
| Changed how the DB/API works in a breaking way | MAJOR   | 2.0.0 → **3**.0.0 |
| Completely rewrote or redesigned the app        | MAJOR   | 3.0.0 → **4**.0.0 |

### Rules of Thumb

- **Bumping MINOR resets PATCH to 0.** (1.3.9 → 1.4.0, not 1.4.9)
- **Bumping MAJOR resets both MINOR and PATCH to 0.** (1.5.3 → 2.0.0)
- **Start at `0.1.0`** for new projects. The leading `0` signals "this is still in early development, expect changes."
- **Go to `1.0.0`** when you'd be comfortable saying "this is ready for real users to depend on."
- **When in doubt between PATCH and MINOR**, pick MINOR — it's better to signal "something new" than hide it.
- **When in doubt between MINOR and MAJOR**, pick MINOR — only go MAJOR when something genuinely breaks for existing users.

### Quick Gut-Check

Before you pick a version bump, ask yourself:

> "If someone updated to this version without reading the changelog, would anything break for them?"
>
> **Yes** → MAJOR. &nbsp; **No, but there's new stuff** → MINOR. &nbsp; **No, just fixes** → PATCH.

---

## When to Use This Skill

- After completing a feature, fix, or refactor
- When the user says "changelog", "log this", "log changes", "what changed"
- At the end of a coding session to capture what happened

## Output Format

Append to `CHANGELOG.md` in the project root. Create the file if it doesn't exist, starting with:

```markdown
# Changelog

All notable changes to this project are documented here.
Versioning follows [Semantic Versioning](https://semver.org/) (MAJOR.MINOR.PATCH).
```

Each entry follows this exact structure:

```markdown
## [X.Y.Z] — YYYY-MM-DD — <One-Line Summary>

**Type:** 🆕 New | 🐛 Fix | 🔧 Refactor | ⚡ Perf | 🧹 Chore | 💥 Breaking

**What changed:**
- <Thing 1 that changed — plain language, max ~12 words>
- <Thing 2 if needed>
- <Thing 3 max — stop at 3 bullets>

**Why it matters:** <1 sentence. What does the user/dev gain?>

**Files touched:** `file1.php`, `file2.js` *(list key files only, skip config/boilerplate)*

---
```

## Rules

1. **3 bullets max** in "What changed." If more than 3 things changed, group related changes or summarize. No one reads 8 bullets.
2. **No jargon dumps.** Write like you're telling a teammate, not documenting an API. "Fixed the login redirect loop" not "Resolved infinite recursive redirect in OAuth2 callback handler middleware."
3. **One-line summary is mandatory** and goes in the `##` heading. It should make sense on its own if someone only reads that line.
4. **"Why it matters" must be human.** Focus on impact: "Users won't see a blank screen on first load anymore." Not: "Improved initialization sequence."
5. **Use the emoji type tag** — it's a visual anchor for scanning. Pick only one.
6. **Skip trivial changes.** Don't log formatting-only changes, comment edits, or dependency bumps unless they fix something meaningful.
7. **Date is today's date.** Use ISO format (YYYY-MM-DD).
8. **Files touched is optional** — include it when it's useful context, skip when obvious or too many files to list meaningfully.
9. **Determine the correct version bump.** Check the previous version in `CHANGELOG.md`, then apply the versioning rules above based on what changed. If no previous version exists, start at `0.1.0`.
10. **Use 💥 Breaking type only for MAJOR bumps.** If you're tagging something 💥, the MAJOR number should go up.

## How to Gather Context

Before writing the entry:

1. Check `git diff` or `git diff --cached` to see what actually changed
2. Check `git log --oneline -5` for recent commit messages
3. Look at the latest version in `CHANGELOG.md` to determine the next version number
4. Look at which files were modified and what the changes do
5. If context is ambiguous, ask the user ONE question max: "What was the goal of this change?"

If there's no git history or diff available, ask the user to briefly describe the change.

## Multiple Changes in One Session

If a session covered multiple unrelated changes, write **separate entries** per change — not one mega-entry. Each should still follow the 3-bullet max rule. Each gets its own version bump (smallest bump first, largest last, so the final version is correct).

## Tone

Casual, clear, direct. Write for someone (including future-you) skimming this at 11pm wondering "wait, when did I change that?"

## Example Entries

```markdown
# Changelog

All notable changes to this project are documented here.
Versioning follows [Semantic Versioning](https://semver.org/) (MAJOR.MINOR.PATCH).

## [0.4.0] — 2026-02-13 — BOM search now scores and ranks component matches

**Type:** 🆕 New

**What changed:**
- Added match scoring algorithm that weighs part number, specs, and availability
- Results now sort by confidence score instead of arbitrary API order
- Low-confidence matches get a warning badge in the UI

**Why it matters:** Finding the right component takes seconds instead of eyeballing 30 results.

**Files touched:** `bom-search.php`, `match-scorer.js`, `results-table.php`

---

## [0.3.1] — 2026-02-12 — Fixed timer not pausing when switching tasks

**Type:** 🐛 Fix

**What changed:**
- Timer now auto-pauses when you click into a different task
- Previous behavior: timer kept running silently in the background, inflating hours

**Why it matters:** Time entries are actually accurate now — no more "wait, I didn't work 6 hours on that."

**Files touched:** `timer.js`, `task-switcher.php`

---
```
