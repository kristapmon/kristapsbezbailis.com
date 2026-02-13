---
name: changelog
description: Generate a short, scannable, ADHD-friendly changelog entry for the current work. Use when the user says "changelog", "log this", "what changed", or after completing a feature/fix/refactor.
---

# Changelog Entry Generator

Generate a concise, visually scannable changelog entry. Every entry must be **short enough to read in under 10 seconds**.

## When to Use

- After completing a feature, fix, or refactor
- When the user says "changelog", "log this", "log changes", "what changed"
- At the end of a coding session to capture what happened

## Output Format

Append to `CHANGELOG.md` in the project root. Create the file if it doesn't exist, starting with a `# Changelog` heading.

Each entry follows this exact structure:

```markdown
## [YYYY-MM-DD] — <One-Line Summary>

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

## How to Gather Context

Before writing the entry:

1. Check `git diff` or `git diff --cached` to see what actually changed
2. Check `git log --oneline -5` for recent commit messages
3. Look at which files were modified and what the changes do
4. If context is ambiguous, ask the user ONE question max: "What was the goal of this change?"

If there's no git history or diff available, ask the user to briefly describe the change.

## Multiple Changes in One Session

If a session covered multiple unrelated changes, write **separate entries** per change — not one mega-entry. Each should still follow the 3-bullet max rule.

## Tone

Casual, clear, direct. Write for someone (including future-you) skimming this at 11pm wondering "wait, when did I change that?"

## Example Entry

```markdown
## 2026-02-13 — BOM search now scores and ranks component matches

**Type:** 🆕 New

**What changed:**
- Added match scoring algorithm that weighs part number, specs, and availability
- Results now sort by confidence score instead of arbitrary API order
- Low-confidence matches get a warning badge in the UI

**Why it matters:** Finding the right component takes seconds instead of eyeballing 30 results.

**Files touched:** `bom-search.php`, `match-scorer.js`, `results-table.php`

---
```
