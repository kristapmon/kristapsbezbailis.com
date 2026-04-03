# Changelog

All notable changes to this project are documented here.
Versioning follows [Semantic Versioning](https://semver.org/) (MAJOR.MINOR.PATCH).

---

## [2.9.0] — 2026-03-30 — SEO/GEO metadata, semantic dates, security hardening, accessibility, and code hygiene

### Added
- `<meta name="author" content="Kristaps Bezbailis">` tag output on every page via `wp_head` hook in `functions.php`
- `<time datetime="...">` elements wrapping all post dates (ISO 8601 machine-readable attribute) in `content.php` (blog index timeline) and `single-blog.php` (single post meta)
- `theme_robots_meta()` function hooked to `wp_head` — outputs `noindex, follow` on search/date/author/paginated pages; outputs `index, follow, max-snippet:-1, max-image-preview:large` on singular posts/pages
- `potentialAction` (SearchAction) added to the WebSite JSON-LD schema in `theme_schema_person_website()` — enables Google sitelinks search box in search results
- `assets/js/theme-nav.js` — extracted `toggleMobileMenu()` from inline `<head>` script into an enqueued file; extended with a full keyboard focus trap (Tab/Shift+Tab cycles within overlay), Escape key to close, focus-on-open (first focusable element), and focus-return-on-close (trigger button); `aria-hidden` toggled on the overlay

### Changed
- Enhanced `llms.txt` with expertise areas, a skills summary paragraph, and full canonical URLs for all sections — improves how AI crawlers (ChatGPT, Perplexity, etc.) represent the site
- Replaced `style="margin-bottom: 0px;"` inline styles on both `<h1>` tags in `single-notes.php` with a `.notes-title` CSS class in `style.css`
- Renamed `assets/js/jQuery.js` → `assets/js/theme-ui.js` and updated the `wp_enqueue_script` handle — file contained only navigation click handlers, not a jQuery library copy; renaming removes the misleading name from the Network tab
- All hardcoded favicon, apple-touch-icon, and `manifest.json` paths in `header.php` replaced with `get_template_directory_uri()` — previously pointed at old theme folder name, causing 404s for every icon and the web manifest

### Removed
- Legacy `$GLOBALS['comment'] = $comment;` assignment from `customized_comment()` in `functions.php` — WordPress's walker loop sets this global automatically; the explicit assignment was redundant
- Inline `<script>` block containing `toggleMobileMenu()` removed from `header.php` `<head>` — function now lives in the enqueued `theme-nav.js`

### Fixed
- `Uncaught TypeError: $ is not a function` console error on all pages — WordPress loads jQuery in no-conflict mode; wrapped `theme-ui.js` content in `jQuery(function ($) { … })` to pass `$` as a scoped alias

### Security
- Added `if ( ! defined( 'ABSPATH' ) ) { exit; }` guard to all 14 PHP template files (`header.php`, `footer.php`, `index.php`, `page.php`, `page-single.php`, `front-page.php`, `single.php`, `single-blog.php`, `single-notes.php`, `single-projects.php`, `template-notes.php`, `template-projects.php`, `content.php`, `comments.php`) — prevents direct HTTP execution of template files
- `theme_add_sri_attributes()` filter on `style_loader_tag` — adds `integrity` and `crossorigin="anonymous"` attributes to the Font Awesome (6.5.1), Phosphor Icons (2.0.3), and Remix Icon (4.2.0) CDN stylesheet tags; hashes generated from the exact pinned CDN URLs

**Files touched:** `llms.txt`, `functions.php`, `content.php`, `single-blog.php`, `single-notes.php`, `single-projects.php`, `single.php`, `header.php`, `footer.php`, `index.php`, `page.php`, `page-single.php`, `front-page.php`, `template-notes.php`, `template-projects.php`, `comments.php`, `style.css`, `assets/js/theme-nav.js` (new), `assets/js/theme-ui.js` (renamed from `jQuery.js`)

---

## [2.8.0] — 2026-03-30 — Menu hover cleanup, active state fix, and footer menu sync

### Changed
- Removed underline hover effects from all navigation menus: stripped `border-bottom` from global `nav a` and `text-decoration: underline` from footer links — hover now only changes opacity/visibility
- Footer menu links now use the same opacity-based hover as the header (0.7 default, 1 on hover with smooth transition)
- Footer menu now uses `wp_nav_menu()` with the `header-menu` location instead of `wp_list_pages()`, so it shows the same items in the same order as the header

### Fixed
- Menu items now stay visually active when viewing a single blog post or project detail page (added `nav_menu_css_class` filter that applies `current_page_item` to the parent page's menu item)

### Added
- Active state styling (`current_page_item`) for footer and mobile overlay menus, matching the header's bold/full-opacity treatment

**Files touched:** `style.css`, `functions.php`, `footer.php`

---

## [2.7.0] — 2026-03-30 — Newsletter redesign with admin settings

### Added
- Newsletter Settings admin page (Settings > Newsletter) for configuring heading, description, button text, form action URL, email field name, honeypot field name, and a toggle to show/hide the entire section — no template editing needed to change email provider or content
- Footer separator line matching the header navigation gradient border, always visible regardless of newsletter toggle

### Changed
- Redesigned newsletter signup section: compact card layout with icon + text on the left, inline email input and JOIN button on the right (replaces the old two-column Mailchimp form)
- Newsletter form values (action URL, field names, copy) are now pulled dynamically from WordPress options instead of being hardcoded in the template

### Removed
- Hardcoded Mailchimp form markup and Mailchimp validation script from footer.php (replaced by provider-agnostic form driven by admin settings)

**Files touched:** `functions.php`, `footer.php`, `style.css`

---

## [2.6.0] — 2026-03-29 — Mobile overlay menu, code snippet fix, and mobile font scaling

### Fixed
- Removed unwanted border on code snippets inside blog posts (Skeleton's `border: 1px solid #E1E1E1` was bleeding through — added `border: none` to `.single-post-content code` and `.single-post-content pre code`)
- Fixed mobile menu button never appearing (CSS selector targeted `a.icon` but HTML used `<button>` — changed to `.mobile-menu .icon`)

### Added
- Fullscreen overlay mobile menu: hamburger button (top-right, Font Awesome `fa-bars`) opens a centered overlay with vertically-stacked menu links; close button swaps to `fa-xmark`; body scroll locked while open; smooth fade transition
- Mobile font size override for tag pills (`.single-post-tag`) in the 600px breakpoint

### Changed
- Increased mobile font sizes (max-width: 600px) across all content sections using a 1.4x proportional multiplier:
  - **Single post:** body text 2rem → 2.8rem, h1 3.8rem → 5.3rem, h2 2.8rem → 3.9rem, h3 2.3rem → 3.2rem, h4 (new) 2.7rem, pre 1.6rem → 2.2rem, meta 11px → 15px, tags 11px → 2rem
  - **Timeline (blog index):** title 3rem → 4.2rem, excerpt 2rem → 2.8rem, date 13px → 18px
  - **Projects:** card title 3rem → 4.2rem, card excerpt 2rem → 2.8rem, card link 2rem → 2.8rem, single project excerpt 1.1rem → 1.5rem
- Enlarged mobile hamburger icon from 22px to 30px; close (X) button on overlay set to 36px for better visibility
- Replaced `content: "MENU"` text override on `.fa-bars:before` with proper Font Awesome bars icon

**Files touched:** `style.css`, `header.php`

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
