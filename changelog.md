# Changelog

All notable changes to this project are documented here.
Versioning follows [Semantic Versioning](https://semver.org/) (MAJOR.MINOR.PATCH).

---

## [2.1.0] — 2026-02-13 — Added Projects custom post type with card grid layout

**Type:** 🆕 New

**What changed:**
- Added "Projects" custom post type with card grid (3-column responsive), external URL field, and admin controls to show/hide featured image and excerpt on detail pages
- Created `template-projects.php` (archive) and `single-projects.php` (detail) with modern styling and hover effects
- Switched site font to Lora serif and adjusted line-height to 1.625 for better readability

**Why it matters:** You can now showcase projects in a clean, visual portfolio grid with flexible display options per project.

**Files touched:** `functions.php`, `template-projects.php`, `single-projects.php`, `style.css`, `header.php`

---

## [2.0.1] — 2026-02-13 — Switched body font to Plus Jakarta Sans

**Type:** 🔧 Refactor

**What changed:**
- Replaced Inter with Plus Jakarta Sans for all body text (modern, highly readable)
- Removed duplicate font loading (was loaded via both @import and link tags)
- Cleaned up unused Raleway font that was being loaded but never used

**Why it matters:** Text is now easier on the eyes with a more modern feel, and the page loads slightly faster with cleaner font loading.

**Files touched:** `header.php`, `style.css`

---

## [2.0.0] — 2025-10-16 — Major theme refactor with homepage hero and CSS variables

**Type:** 🆕 New

**What changed:**
- Added homepage hero section with featured image (60/40 split, mobile-first responsive)
- Introduced CSS custom properties for colors (`--bg`, `--text`, `--muted`, `--accent`)
- Switched typography to EB Garamond (headings) + Inter (body), reorganized assets folder

**Why it matters:** The theme now has a proper homepage layout, consistent color theming, and a cleaner codebase structure.

**Files touched:** `front-page.php`, `style.css`, `functions.php`, `header.php`

---

## [1.2.0] — 2021-05-22 — Roboto font and removed comments

**Type:** 🔧 Refactor

**What changed:**
- Changed default font-family to Roboto
- Removed comments section from blog posts

**Why it matters:** Cleaner reading experience without comment clutter.

---

## [1.1.0] — 2020-12-05 — Added favicon links

**Type:** 🆕 New

**What changed:**
- Hardcoded favicon links into header.php for Apple touch icons and favicons

**Why it matters:** Site now has proper icons across all devices and browsers.

**Files touched:** `header.php`

---

## [1.0.0] — 2018-11-19 — Newsletter polish and navigation fixes

**Type:** 🐛 Fix

**What changed:**
- Cleaned up newsletter subscription box CSS styling
- Fixed navigation element jumping on hover
- Added dynamic copyright year and "Mailing list" nav link

**Why it matters:** First polished, stable release with professional newsletter signup and smooth navigation.

**Files touched:** `style.css`, `footer.php`

---

## [0.2.0] — 2017-11-05 — CSS structure and comments system

**Type:** 🆕 New

**What changed:**
- Fixed CSS load order so style.css can override skeleton.css
- Adjusted heading sizes and list margins for better typography
- Added comments template to enable blog post comments

**Why it matters:** Theme now has proper CSS cascading and a working comments system.

**Files touched:** `style.css`, `skeleton.css`, `functions.php`, `single-blog.php`, `comments.php`

---

## [0.1.0] — 2017-11-04 — Initial theme setup

**Type:** 🧹 Chore

**What changed:**
- Fixed font-family inheritance by adjusting skeleton.css

**Why it matters:** Foundation for consistent typography across the theme.

**Files touched:** `skeleton.css`

---
