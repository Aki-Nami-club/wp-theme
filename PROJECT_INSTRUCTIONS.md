# Project: VibeMag FSE WordPress Theme
# Reference: Merimag (News/Magazine Style)

## 1. General Overview
A modern WordPress Full Site Editing (FSE) theme built for high-content density blogs.
- **Vibe:** Clean, professional news magazine.
- **Tech Stack:** WordPress FSE, PHP 8.1+, Tailwind CSS, Swiper.js, Alpine.js (for lightweight interactivity).
- **Typography:** EXO2 (Google Fonts).
- **Color Palette:** Light/Dark mode support. Accent color: #E67E22 (Restrained Orange).

## 2. Layout Structure (The Grid)
- **Header:** - Row 1: News Ticker (Automatic latest posts titles).
  - Row 2: Logo (Left) + 728x90 Ad Banner (Right).
  - Row 3: Sticky Main Menu + AJAX Search Icon.
- **Home Layout:** - Section 1: Swiper.js Slider (4x4 posts loop).
  - Section 2: Two-column grid (Main Content + Sticky Sidebar). Blocks: Anime, Games.
  - Section 3: Full-width 970x250 Ad Banner.
  - Section 4: Two-column grid (Main Content + 2nd Sticky Sidebar). Blocks: Reading.
- **Footer:** Square logo, Page list, Category list.

## 3. Core Features
- **Smart Search:** AJAX-powered search overlay.
- **FSE Compatible:** All parts (header, footer, sidebars) managed via WordPress Site Editor.
- **Translation:** Ready for .po/.mo, default language: English.
- **Performance:** No jQuery, minimal dependencies.
