#[Unreleased]

## Added
- Homepage hero section with featured image and content display
  - Created front-page.php template for homepage-specific layout
  - Added 60/40 split layout (content left, featured image right) on desktop
  - Implemented mobile-first responsive design with image-first stacking
  - Added fallback to full-width content when no featured image is present
- Enhanced responsive container system with improved spacing
  - Updated container widths (85% desktop, 90% mobile) for better content flow
  - Added proper horizontal centering and consistent side spacing
  - Implemented flexbox-based mobile reordering for optimal UX

## Changed
- Updated theme color palette with CSS custom properties for better maintainability
  - Added `--bg: #ffffff` (background), `--text: #111111` (text), `--muted: #6c757d` (muted elements), `--accent: #007acc` (accent color)
  - Replaced all hardcoded color values throughout style.css with CSS variables
  - Improved color consistency and ease of future theme updates
- Updated typography to use EB Garamond and Inter fonts
  - Changed headings (h1-h6) to use EB Garamond serif font
  - Changed body text and form elements to use Inter sans-serif font
  - Updated Google Fonts import URL to load the new font families
- Improved homepage vertical spacing and alignment
  - Adjusted section margins to match navigation spacing (1rem top margins)
  - Aligned hero section positioning with header border styling
  - Enhanced mobile image centering with proper block-level positioning
- Reorganized asset structure by moving CSS and JS folders under assets/
  - Created assets/ directory to house static resources
  - Moved css/ and js/ folders into assets/
  - Updated all WordPress enqueue paths in functions.php to reflect new structure

#[RELEASED]

22 May 2021:
- Changed the default font-family on the theme to Roboto.
- Removed the comments section from blog posts.

5 Dec 2020:
- Hardcoded the KB fav icon links into the header.php file

19 Nov 2018:
- Cleaned up the newsletter subscription box CSS and made it pretty
- Fixed the navigation moving element when hovering over it
- Added PHO code to display the Copyright year automatically
- Added 'Mailing list' link on the left side of the navigation bar
- Made styling changes in the Blog area

5 Nov 2017:
 - added Lines 133-136 in style.css to move unordered list to the right by 3rems
 - changed Line 135 (margin-bottom for h3 tag) in skeleton.css from 2.0rem to 1.0rem
 - swapped around skeleton.css (to Line 5) and style.css (to Line 6) functions so that style.css can override skeleton.css values
 - added Lines 133-135 setting h1 tag font-size to 4.0rem (to override skeleton.css value of 5.0rem)
 - added <?php comments_template(); ?> on Line 15 in single-blog.php to enable comments for the blog posts
 - created comments.php file
4 Nov 2017:
 - commented out Line 127 in css/skeleton.css to allow font-family setting to be taken from style.css body attribute
