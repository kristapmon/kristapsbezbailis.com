# Changelog

All notable changes to this project are documented here.
Versioning follows [Semantic Versioning](https://semver.org/) (MAJOR.MINOR.PATCH).

---

## [2.5.0] — 2026-02-16 — Blog timeline redesign with chat bubble cards and infinite scroll

**Type:** 🆕 New

**What changed:**
- Redesigned blog posts section with timeline-style layout featuring chat bubble cards
- Each post displays date, title, and excerpt in a rounded card with a speech bubble tail pointing up from the date
- Added infinite scroll lazy loading — loads 10 posts at a time as user scrolls (replaces pagination)
- Added support for intro text on blog page (add content to your Posts page and it appears below the headline)
- Restructured blog page to match Projects page structure for consistent styling (same container, header, and title sizes)
- Hover effect on cards: background darkens slightly for visual feedback
- Fully responsive design for tablet and mobile views
- Redesigned individual blog post pages with a new typography-focused layout: back link, centered date + reading time meta, large serif title, tag pills, full-width featured image, and scoped content styles
- Scoped single post content styles cover headings (h2–h4), styled links, blockquotes with left accent border, dark code blocks, circle-bullet lists, image captions, and CTA buttons
- Added `get_reading_time()` PHP helper that estimates reading time based on word count (~200 wpm)
- Updated body text color to `#111827` (rgb(17, 24, 39)) for deeper, richer contrast

**Why it matters:** The blog section now has a modern, visually engaging timeline design that's easier to browse with seamless infinite scrolling instead of clicking through pages. Individual posts now have a polished reading layout with rich typography that matches the overall site aesthetic.

**Files touched:** `index.php`, `content.php`, `style.css`, `functions.php`, `single-blog.php`, `assets/js/timeline-lazy-load.js`

---

## [2.4.0] — 2026-02-13 — Full SEO overhaul with admin settings panel

**Type:** 🆕 New

**What changed:**
- Added SEO meta tags (description, canonical, Open Graph, Twitter Cards) and JSON-LD schema (Person, WebSite, Article, Breadcrumbs)
- Created admin settings page (Settings > Theme SEO) to configure GA4, social profiles, author info, and default OG image
- Updated templates with semantic HTML5 elements (header, nav, main, footer), ARIA labels, and skip links; added `llms.txt` for AI crawlers

**Why it matters:** Your site is now search engine and AI-ready with proper structured data, social sharing previews, and all SEO settings manageable from the admin panel — no code editing needed.

**Files touched:** `functions.php`, `header.php`, `footer.php`, `style.css`, `llms.txt`, `assets/js/theme-seo-admin.js`

---

## [2.3.0] — 2026-02-13 — Page icon picker with emoji + 3 icon libraries

**Type:** 🆕 New

**What changed:**
- Added Page Icon meta box with visual grids: Emojis (categorized), Font Awesome (150+), Phosphor (100+), and Remix (100+) icons
- Icons appear in navigation menu via custom nav walker, with option to show/hide in page headlines
- Updated Notes template to match Projects structure; both pages now have consistent header styling

**Why it matters:** You can now pick icons visually from the admin panel instead of copy-pasting emojis, and they sync between the menu and page title.

**Files touched:** `functions.php`, `header.php`, `template-notes.php`, `page-single.php`, `index.php`, `style.css`

---

## [2.2.0] — 2026-02-13 — Header styling overhaul with modern menu effects

**Type:** 🆕 New

**What changed:**
- Site title now has black background, white text, and slight rotation (-2deg) for visual flair
- Menu items: inactive = gray/faded, active = black/bold, hover = smooth opacity transition (no underline)
- Header separator line fades out at edges using gradient; added custom navigation menu support

**Why it matters:** Header feels more polished and modern — active page is obvious, hover is subtle, and the tilted logo adds personality.

**Files touched:** `style.css`, `functions.php`, `header.php`

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
