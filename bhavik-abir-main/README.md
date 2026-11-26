# Bhavik Enterprises Polymer Materials — WordPress Theme (`v1.7.6`)

A custom, lightweight classic WordPress theme engineered for **Bhavik Enterprises** — the authorised distributor of **Abiroverseas LLC** in India — with a structured Knowledge Center.

This `README.md` is written so any developer, designer, or site administrator can understand **the entire theme flow, file map, CSS/JS isolation rules, header/footer setup, and editing workflow in one read**.

---

## 1. High-Level Theme Flow (How a Request Renders)

```text
Incoming WordPress URL
        │
        1. Loads header.php   (Bhavik Enterprises topbar + text logo + Appearance → Menus primary nav + SVG sprite)
        2. Runs the Page / CPT template (e.g., template-home.php, template-product-range.php, single-knowledge.php)
        3. Enqueues ONLY that page's CSS + JS (via ab_enqueue_assets() in functions.php) + site.css + navigation.js
        4. Loads footer.php   (Bhavik Enterprises 4-column footer: address, phone, email, WhatsApp + scroll-to-top)
```

The home page (`/`) renders `page-template/template-home.php`, which is the **Bhavik Enterprises layout**, so it
loads `bhavik.css` + `bhavik.js`. `/bhavik-enterprises/` is a legacy alias of the same home layout. There is exactly
**one header and one footer** in the theme — no page carries its own chrome.

---

## 2. Directory Structure at a Glance

```text
bhavik-enterprises-theme/
├── style.css                                   # WordPress theme header metadata (v1.6.0)
├── functions.php                               # Theme setup, per-page CSS/JS enqueue, CPT/taxonomies, SEO/FAQ meta, enquiry handler
├── header.php                                  # The site header (Bhavik Enterprises topbar, text logo, primary menu, icon sprite)
├── footer.php                                  # The site footer (Bhavik Enterprises 4-column footer + partner marquee)
├── header-bhavik.php                           # Legacy, no longer included anywhere (kept for reference)
├── footer-bhavik.php                           # Legacy, no longer included anywhere (kept for reference)
├── 404.php                                     # Custom Error 404 page with smart "Back to Previous Page" button
├── page.php / index.php                        # Fallback standard WordPress page & index templates
├── archive-knowledge.php                       # Knowledge Center hub (/knowledge/) with search & taxonomy filters
├── single-knowledge.php                        # Single Knowledge Center article (/knowledge/{slug}/)
├── taxonomy-knowledge_hub.php                  # Knowledge Hub taxonomy archive
├── taxonomy-knowledge_material.php             # Knowledge Material taxonomy archive
├── taxonomy-knowledge_topic.php                # Knowledge Topic taxonomy archive
├── inc/
│   └── default-content.php                     # Starter page definitions & default SEO/FAQ metadata seeded once on activation
├── page-template/
│   ├── template-home.php                       # Front Page / Home — Bhavik Enterprises layout
│   ├── template-about.php                      # About Us (/about/)
│   ├── template-contact.php                    # Contact Us (/contact-us/)
│   ├── template-product-families.php           # Product Families 6-card searchable directory (/products/)
│   ├── template-product-range.php              # Product Range 12-card 2x2 3D borderless directory (/product-range/)
│   ├── template-bhavik-enterprises.php         # Legacy alias of template-home.php (/bhavik-enterprises/)
│   ├── template-material.php                   # HDPE Material Page (/hdpe/)
│   ├── template-material-LDPE.php              # LDPE Material Page (/ldpe/)
│   ├── template-material-PP.php                # Polypropylene (PP) Material Page (/pp/)
│   ├── template-material-PVC.php               # PVC Material Page (/pvc/)
│   ├── template-material-PS.php                # Polystyrene (PS) Material Page (/ps/)
│   ├── template-material-ABS.php               # ABS Material Page (/abs/)
│   ├── template-material-EVA.php               # EVA Material Page (/eva/)
│   └── template-material-Recycled-Plastics.php # Recycled Plastics Material Page (/recycled-plastics/)
└── assets/
    ├── css/
    │   ├── home.css                            # Retained from the previous Abiroverseas home layout (no longer enqueued)
    │   ├── about.css                           # Isolated styles for About Us page ONLY
    │   ├── contact.css                         # Isolated styles for Contact Us page ONLY
    │   ├── products.css                        # Isolated styles for Product Families & 2x2 Product Range ONLY
    │   ├── material.css                        # Isolated styles for the 8 Material pages ONLY
    │   ├── knowledge.css                       # Isolated styles for Knowledge Center archive & articles ONLY
    │   ├── bhavik.css                          # Isolated styles for Bhavik Enterprises page ONLY
    │   ├── error-404.css                       # Isolated styles for Error 404 page ONLY
    │   ├── site.css                            # Shared header, topbar, mobile nav, 4-column footer & scroll-to-top ONLY
    │   └── knowledge-admin.css                 # WP Admin meta box styles for Knowledge Center editor
    ├── js/
    │   ├── navigation.js                       # Shared mobile drawer & scroll-to-top controller (loaded on every page)
    │   ├── home.js                             # Retained from the previous Abiroverseas home layout (no longer enqueued)
    │   ├── about.js                            # About scroll reveal & FAQ category tabs
    │   ├── products.js                         # Live instant client-side search filter for Product Families & Product Range
    │   ├── material.js                         # Material gallery carousel & scroll reveal
    │   ├── bhavik.js                           # Bhavik Enterprises scroll reveal controller
    │   ├── error-404.js                        # Smart "Back to Previous Page" referrer & history controller (404 ONLY)
    │   ├── knowledge-hub.js                    # Knowledge Hub filter interactions
    │   ├── knowledge-single.js                 # Single Knowledge article TOC / interactions
    │   └── knowledge-admin.js                  # WP Admin repeater & rich-text controls
    └── img/                                    # Bundled polymer & industrial photography assets
```

---

## 3. Strict Per-Page CSS & JS Isolation (Core Rule)

**Every page type owns its own dedicated CSS file.** Do **not** place page-specific card or section rules into `site.css`, and do **not** chain unrelated page stylesheets together.

`ab_enqueue_assets()` in `functions.php` resolves the current route (by slug first, then by assigned template) and loads **only** that route's CSS and JS:

| Page / Route | PHP Template | Dedicated CSS | Dedicated JS | Header / Footer |
| :--- | :--- | :--- | :--- | :--- |
| **Home** (`/`) — Bhavik Enterprises layout | `page-template/template-home.php` | `bhavik.css` | `bhavik.js` | `header.php` / `footer.php` |
| **About Us** (`/about/`) | `page-template/template-about.php` | `about.css` | `about.js` | `header.php` / `footer.php` |
| **Contact Us** (`/contact-us/`) | `page-template/template-contact.php` | `contact.css` | — | `header.php` / `footer.php` |
| **Product Families** (`/products/`) | `page-template/template-product-families.php` | `products.css` | `products.js` | `header.php` / `footer.php` |
| **Product Range** (`/product-range/`) | `page-template/template-product-range.php` | `products.css` | `products.js` | `header.php` / `footer.php` |
| **Material Pages** (`/hdpe/`, `/pp/`, `/ldpe/`, `/pvc/`, `/ps/`, `/abs/`, `/eva/`, `/recycled-plastics/`) | `page-template/template-material*.php` | `material.css` | `material.js` | `header.php` / `footer.php` |
| **Knowledge Hub** (`/knowledge/` & taxonomies) | `archive-knowledge.php`, `taxonomy-*.php` | `knowledge.css` | `knowledge-hub.js` | `header.php` / `footer.php` |
| **Knowledge Article** (`/knowledge/{slug}/`) | `single-knowledge.php` | `knowledge.css` | `knowledge-single.js` | `header.php` / `footer.php` |
| **Bhavik Enterprises** (`/bhavik-enterprises/`, legacy alias of the home layout) | `page-template/template-bhavik-enterprises.php` | `bhavik.css` | `bhavik.js` | `header.php` / `footer.php` |
| **Error 404** (any missing URL) | `404.php` | `error-404.css` | `error-404.js` | `header.php` / `footer.php` |

> **What `site.css` & `navigation.js` do:** Loaded on all front-end views solely to power the sticky header bar, topbar ticker, mobile hamburger drawer, 4-column footer, and floating back-to-top button.

---

## 4. One Header, One Footer — Bhavik Enterprises Brand Data

The site is the **Bhavik Enterprises** website, so every page shares the same header and footer:

1. **`header.php`**
   - Bhavik Enterprises topbar: `info@bhavikenterprises.com` · `+91 7007049478`
   - Bhavik Enterprises text logo, rendered as styled text without a logo-image dependency.
   - Primary menu from **Appearance → Menus → Primary Menu**; `wp_nav_menu()` is called with `echo => false` so the
     `ab_primary_menu_fallback()` menu is still printed when no menu is assigned.

2. **`footer.php`**
   - Bhavik Enterprises text logo, partner marquee, and the full Bhavik Enterprises data:
     - **Address:** Village Barhat Bangar, Mandhana Bithoor Road, Kanpur – 209217, UP, India
     - **Head Office:** Kanpur · **Branches:** Mumbai, Mundra
     - **Phone / WhatsApp:** `+91 7007049478`
     - **Email:** `info@bhavikenterprises.com`
   - Every internal link is generated with `home_url()` / `ab_home_anchor_url()` — no hardcoded paths.

3. **Legacy files:** `header-bhavik.php` and `footer-bhavik.php` are no longer included by any template. They are kept
   only for reference and can be deleted safely.

4. **Social profiles:** `ab_social_links()` in `functions.php` currently returns empty URLs, so no Abiroverseas profile
   is linked from the Bhavik Enterprises site. Add Bhavik Enterprises' own profile URLs there to show the icons.

---

## 5. Visual & UI Design System Conventions

When editing or adding sections, follow these established design rules:

- **All Buttons = Borderless 3D Orange Accent (Subtle Shadows):** Every button across the theme (`.ab-btn`, `.ab-btn--blue`, `.ab-btn--outline`, `.ab-btn--light`, `.ab-contact-submit`, `.ab-range-card-cta`, `.kh-button`, `.kh-button-light`, `.kh-enquiry-button`, header quote button, footer CTA, and scroll-to-top) uses the orange accent gradient (`#ff9238 → #f58220 → #e26d0c`), white text, **`border: 0`**, and restrained 3D elevation shadows in both resting and `:hover` states.
- **Unified Eyebrows & Equal-Height Split/Hero Images:** Every page (including Bhavik Enterprises) shares the exact `.ab-eyebrow` design (`17px` desktop/tablet-landscape, `15px` tablet portrait, `14px` mobile, `#008080` teal text or `#a8ded6` on dark sections, and a `24px × 2px` `#f58220` orange leading dash aligned to the first line) and stretches right-side banner/split image containers (`align-items: stretch` + `position: absolute; inset: 0` cover media) to equal the exact height of the left-side copy container on desktop and tablet landscape (`>= 1024px`).
- **Borderless 3D Image Cards:**
  - **Bhavik "Industries We Serve"** (`#industries` in `bhavik.css`) uses a 3-column desktop / 2-column tablet / 1-column mobile borderless 3D pedestal card with a rounded 16:10 photo stage, frosted badge, and floating `#008080` icon box overlapping the image bottom.
  - **Product Range** (`#ab-range-grid` in `products.css`) uses the same borderless 3D image-card craft in a **2×2 layout** (`repeat(2, minmax(0, 1fr))` on desktop & tablet, `1fr` on mobile), paired with a recessed borderless spec table and two separate 3D orange CTA buttons (`Explore <Material>` + `Enquire Now`) per card, isolated strictly inside `products.css`.
- **Bhavik FAQ Question Boxes:** Styled in `bhavik.css` (`.ab-faq-item`) with a tinted sage-mist 3D surface, **no border and no left accent bar**, 3D circular rotating trigger icon, and recessed white answer well so each question stands out cleanly against the white section background.
- **Unified Icon Boxes:** Icon containers (`.ab-icon-box`, `.ab-contact-detail-icon`, `.ab-range-card-icon`) use the `#008080` teal palette on soft 3D surfaces.
- **Clean Photography:** Banner and slider images do not render decorative circle/ring overlays on top of photography.

---

## 6. Forms & Lead Submission Flow

All enquiry forms across **Home**, **Contact Us**, **all 8 Material pages**, and **Bhavik Enterprises** post securely to WordPress `admin-post.php` (`action=ab_material_enquiry`, handled in `functions.php`):

1. Verifies WordPress nonce (`ab_material_enquiry_nonce`) and invisible honeypot (`ab_website`).
2. Sanitizes all submitted fields (`name`, `company`, `email`, `phone`, `material`, `quantity`, `application`, `enquiry_kind`).
3. Supports optional spec/datasheet file attachments up to 5 MB (`.pdf`, `.doc`, `.docx`, `.xls`, `.xlsx`, `.csv`, `.jpg`, `.jpeg`, `.png`) on Material forms without exposing uploads in the public Media Library.
4. Sends formatted notification email to **Settings → General → Administration Email Address** and redirects back to the originating page with `?ab_enquiry=sent` (or `?ab_enquiry=error`).

---

## 7. Knowledge Center Architecture (`/knowledge/`)

- **Custom Post Type:** `knowledge` (URL structure `/knowledge/{exact-slug}/`).
- **Taxonomies:**
  1. `knowledge_hub` — Material/topic hubs (HDPE, PP, LDPE / LLDPE, PVC, Recycled Polymers)
  2. `knowledge_material` — Product Type / Material filter
  3. `knowledge_topic` — Topic filter (Comparisons, FAQs, Grades & Selection, Processing Methods, Applications, Recycling & Sustainability)
- **Structured Editor Fields (`functions.php`):** Custom native meta boxes let editors manage Exact URL Slug, SEO Title/Description, Direct Answer, 3-Column Comparison Table, Why Materials Differ, WordPress Block Editor extra content, When to Consider, FAQs (auto-generating `FAQPage` JSON-LD schema), Related Materials, and Article-End CTA — with zero paid plugin dependencies.

---

## 8. Installation & Quick Editing Guide

### Installing the Theme
1. Zip the theme directory (ensuring `style.css` is at the root of the archive).
2. In WordPress Admin, go to **Appearance → Themes → Add New → Upload Theme**, upload the zip, and click **Activate**.
3. Starter pages (`Home`, `About`, `HDPE`, `Product Families`, `Product Range`, `Bhavik Enterprises`) and the starter `Primary Menu` are created automatically without overwriting existing content. The Home page renders the Bhavik Enterprises layout.
4. Visit **Settings → Permalinks** and click **Save Changes** once to flush `/knowledge/` rewrite rules.
5. The shared header and footer render "Bhavik Enterprises" as a styled text logo; no logo uploads are required for them. Retain the existing partner-logo uploads for the footer marquee.

### Common Maintenance Tasks
- **Edit the Header / Menu / Footer:** Edit `header.php`, `footer.php`, or **Appearance → Menus → Primary Menu**.
- **Edit Contact Details:** The Bhavik Enterprises email/phone/WhatsApp/address live in `header.php`, `footer.php`, the `$bv_*` variables at the top of `page-template/template-home.php`, and the legal/support pages.
- **Edit the Home Page:** Edit `page-template/template-home.php` (the layout is Bhavik Enterprises; `home.css` / `home.js` from the previous Abiroverseas home page are no longer used).
- **Edit Styles Safely:** Open only the target page's CSS file in `assets/css/` (see the table in Section 3) and bump `AB_THEME_VERSION` in `functions.php` so browsers fetch the updated stylesheet immediately.
- **Requirements:** WordPress 6.0+ and PHP 7.4+. No WooCommerce or ACF Pro required.
#   b h a v i k - a b i r 
 
 #   b h a v i k - a b i r 
 
 
## 9. Separate Material Supply Pages (v1.7.0)

The product design stays on the existing `material.css` / `material.js` layout. The compact supply selector below the shared header is **normal page navigation**, not same-page tabs. Its current-page highlight is written into the HTML with `aria-current="page"`; it does not need JavaScript.

### Routes

Each of these materials has an overview and four independent, root-level WordPress pages:

| Material | Overview | Prime / Virgin | Off-grade | Recycled | Scrap / Regrind |
| --- | --- | --- | --- | --- | --- |
| LDPE | `/ldpe/` | `/ldpe-prime/` | `/ldpe-offgrade/` | `/ldpe-recycled/` | `/ldpe-scrap-regrind/` |
| HDPE | `/hdpe/` | `/hdpe-prime/` | `/hdpe-offgrade/` | `/hdpe-recycled/` | `/hdpe-scrap-regrind/` |
| PP | `/pp/` | `/pp-prime/` | `/pp-offgrade/` | `/pp-recycled/` | `/pp-scrap-regrind/` |
| PVC | `/pvc/` | `/pvc-prime/` | `/pvc-offgrade/` | `/pvc-recycled/` | `/pvc-scrap-regrind/` |
| PS | `/ps/` | `/ps-prime/` | `/ps-offgrade/` | `/ps-recycled/` | `/ps-scrap-regrind/` |
| ABS | `/abs/` | `/abs-prime/` | `/abs-offgrade/` | `/abs-recycled/` | `/abs-scrap-regrind/` |
| EVA | `/eva/` | `/eva-prime/` | `/eva-offgrade/` | `/eva-recycled/` | `/eva-scrap-regrind/` |

- Every overview introduces all four supply types and links to their pages.
- Every supply page links to its overview and the other three types in both the top selector and the related-supply section.
- PS is a Polystyrene-specific GPPS/HIPS guide, not the old HDPE placeholder. It is linked from the home page and both product directories.
- The shared wordmark is **Bhavik** on the large upper line with **Enterprises** below it. Its sizing and colours are shared by `header.php`, `footer.php` and `assets/css/site.css`.

### Static editing

All new product copy, links, checklists, FAQs and selected form options are **literal HTML in each template**. There is no product-content array, shared data-driven renderer, AJAX fetch or client-side content swap. For example, edit `page-template/template-material-LDPE-prime.php` directly to change the prime LDPE page. Each template uses the same existing hero/gallery, section navigation and enquiry-form classes. The existing general-purpose page interaction script handles the gallery and form validation only; supply navigation works with JavaScript disabled.

The grade form defaults to that page's supply type and carries its own `enquiry_page` value, so a submission can return to the same grade page even without a browser referrer. The shared handler accepts detailed enquiries for all seven materials, includes the supply type in the email, and applies the existing nonce, honeypot and attachment checks.

### WordPress provisioning and deployment

`inc/material-supply-pages.php` contains explicit installation calls for all 35 material routes. On the first request after deployment, it creates missing pages with their own template, SEO title and description. It preserves existing page titles, editorial content, publication status, custom templates and SEO values. A short-lived installation lock prevents concurrent first requests from creating duplicate pages; failed insertions leave the migration available to retry. The completion option is `ab_static_supply_pages_v1_ready`.

After deployment:
1. Visit the site once, then check **Pages** for the material overviews and 28 new supply pages.
2. Check any pre-existing draft, private or custom-template page occupying one of the listed slugs. It is intentionally not republished or overwritten; publish or assign the appropriate supplied template manually if needed.
3. Test navigation from an overview through all four types and back, including on mobile. Keep these documented slugs if editing the pages: their static cross-links use `home_url()`.
4. Submit a real test enquiry and verify email delivery on the WordPress host. Local tests stub the mail transport; they do not prove SMTP delivery.
5. Confirm the site's existing pretty-permalink configuration. These are regular WordPress pages, not custom rewrite endpoints.

### Search indexing

These are separate published pages with distinct titles, descriptions and material/supply-specific guidance. WordPress's normal singular-page canonical links and page sitemap apply; the new templates do not canonicalise grades to the overview or add `noindex`. Existing SEO plugin settings and WordPress search-visibility settings remain authoritative. Check those settings on the deployed site and submit the sitemap through your usual search tools. Separate URLs can be indexed independently, but no implementation can guarantee that a search engine will index or rank every page.

### Regression checks

Run `PYTHONDONTWRITEBYTECODE=1 python3 -m unittest discover -s tests -v` for route, link, active-state, static-content, HTML-nesting, form-default, PS and logo contracts. Also perform PHP lint and browser checks on deployment. Knowledge Hub templates, assets and logic are outside this change and remain untouched.


## 10. Product category navigation (v1.7.1)

- **Home and About:** Each has a material-first category explorer with seven polymer tabs and four static supply cards per polymer. Each panel links to its material overview and all four dedicated supply pages. Edit these literal sections in `page-template/template-home.php` and `page-template/template-about.php`; their shared styling is in `assets/css/product-categories.css`, loaded only on those layouts (including the legacy Bhavik home alias).
- **Products mega menu:** `template-parts/products-mega-menu.php` contains the static seven-polymer/four-supply-type link grid. `inc/navigation-mega-menu.php` attaches it to the primary menu's top-level Products item, recognised by its Products/Product Range URL, Products title, or explicit `ab-menu-mega` CSS class. The fallback primary menu uses the same panel. Saved menu items are not deleted or rewritten. Additional custom links under Products are retained under “More product links”; other menu branches and locations are left alone.
- **Interaction:** The shared `assets/js/navigation.js` controller supports desktop hover, focus, disclosure buttons, touch, Escape with focus return, outside-click dismissal and viewport resizing. At 1100px and below, navigation collapses before the logo, links and quote button can overlap; the mega menu expands inside that scrollable panel, so its final links remain reachable. Without JavaScript, desktop hover/focus still exposes the panel.
- **Supply-page cleanup:** The new supply templates no longer render breadcrumb trails or the repeated supply disclaimer note. Their body copy, eyebrow labels and text-arrow links use 17px; the new supply-card headings use 22px. Typography overrides are scoped to the new supply-page layout and new supply-directory components, not older page headings or Knowledge Hub styles. The five-way links between separate supply pages remain intact.
- **Checks:** Run `PYTHONDONTWRITEBYTECODE=1 python3 -m unittest discover -s tests -v`. In WordPress, also check your assigned primary menu's Products branch, any additional custom links, mobile scrolling, keyboard access and the new Home/About category destinations.


### Mega-menu visual refinement and consistent supply bars (v1.7.2)

- The Products panel follows a four-column desktop / two-column tablet (600–1100px) / single-column phone layout. It retains all seven material overview links, 28 supply links, both product directories, enquiry CTA and optional custom links. The panel and cards use no CSS borders: depth comes from inset highlights, soft shadows, rounded corners and translucent card fills over a pale blue/teal surface.
- Material icons, link/disclosure arrows, molecules and pellet accents are inline SVG; the menu requires no raster images or external icon libraries. Its symbols and gradients are self-contained in `template-parts/products-mega-menu.php`. Decorative artwork is hidden from assistive technology and cannot intercept pointer events.
- Every five-way material/supply navigation bar now uses 17px Reddit Sans with weight 600 on overview and grade pages, including mobile. The previous 12/14px overview and 13px mobile rules have been removed. This correction is limited to `.ab-material-switch`; other material typography and Knowledge Center code are unchanged.
- Validation: 10 Python tests; PHP 8.2 syntax and controlled menu-render checks; Chromium checks from 320px to 1920px across the 599/600px and 1100/1101px boundaries, all 35 bars at desktop/phone sizes, and Home/About/Product stylesheet contexts. These are controlled theme fixtures, not validation of the live WordPress host.


### Mega-menu scrolling correction (v1.7.3)

At mobile/tablet widths (1100px and below), `.ab-nav-links` is the single scroll container. The inline mega panel explicitly uses `overflow: visible` and `overscroll-behavior: auto`, rather than inheriting the desktop panel's `overflow-y: auto` and `overscroll-behavior: contain`. Those desktop rules trapped wheel/touch gestures inside a full-height inner panel, so real input could fail even when `scrollIntoView()` appeared to work. The outer menu list also opts out of flex shrinking. Desktop retains its independently bounded, scrollable dropdown. No PHP rendering, menu content, Knowledge Center logic or visual design is changed.

Regression test: `tests/test_navigation_scroll.cjs` serves a controlled header using the real menu markup, CSS and JavaScript. It tests actual wheel input at nine desktop/tablet/phone/landscape sizes, scroll-to-bottom reachability, background-scroll isolation, Escape, and CDP touch swipes in both directions with rotation while open. It reproduced the previous mobile failure before the CSS correction.

Run with Node.js and a Chromium browser:

```sh
npm install --prefix /tmp/bhavik-scroll-tests playwright-core @sparticuz/chromium
NODE_PATH=/tmp/bhavik-scroll-tests/node_modules node tests/test_navigation_scroll.cjs
```

Alternatively set `CHROMIUM_PATH` to a local Chromium executable (only `playwright-core` is then needed). Minimal Linux environments may need Chromium's shared libraries installed or available through `LD_LIBRARY_PATH`. These are controlled browser checks, not verification of the deployed WordPress server or of the separately reported live PHP critical error.


### Material-first category explorer (v1.7.4)

- Home and About now follow the supplied visual reference: seven photographic polymer selectors, a teal active state, a material-details header, four angled-image supply cards (including **Prime / Virgin**), and a compact sourcing/application benefit strip. Existing theme fonts, teal/ink colours and shared container widths are retained. The local WebP photos in `assets/img/categories/` are small crops of existing theme assets and are decorative, not lot-specific product photographs.
- All seven panels and 35 page destinations remain literal HTML/PHP in each template. There is no runtime product array, content fetch, new PHP include, page migration, or change to the separate material/supply routes. Edit the Home and About sections together when changing product copy.
- `assets/js/product-categories.js` adds accessible tab roles, roving keyboard focus, Arrow/Home/End keys, previous/next buttons, a polite selection indicator, and a short **vertical** panel-entry transition. The material-card rail scrolls horizontally on smaller screens; controls centre the selected card without deliberately scrolling the page vertically. Motion is disabled for reduced-motion users. Without JavaScript, every panel and all 35 destinations remain visible and the selectors are anchor links.
- Supply cards use four columns from 1200px, two columns from 701–1199px, and a single column up to 700px. The seven-tab rail becomes horizontally scrollable at 1100px and below. The arrows work at every size and stop at the first/last material. HDPE is initially selected to match the reference.
- The controller is enqueued only when `product-categories.css` is loaded (Home, About and the existing legacy home alias). Shared header/menu, Knowledge Center assets and logic, and other product layouts are unchanged.

Validation: `python3 -m unittest discover -s tests -v` checks static markup, references and destinations. `tests/test_category_tabs.cjs` exercises all seven materials, four supply cards, route targets, equal-sized grid cards, overflow, keyboard navigation and arrow state at 15 viewport widths on both Home/About stylesheets, plus actual touch swiping, rotation, reduced motion and no-JS fallback. Run with Node.js and `playwright-core` plus either a Chromium executable or `@sparticuz/chromium`, as with `test_navigation_scroll.cjs` above:

```sh
NODE_PATH=/tmp/bhavik-scroll-tests/node_modules node tests/test_category_tabs.cjs
# Optional: split a longer run by layout.
CATEGORY_LAYOUTS=home NODE_PATH=/tmp/bhavik-scroll-tests/node_modules node tests/test_category_tabs.cjs
CATEGORY_LAYOUTS=about NODE_PATH=/tmp/bhavik-scroll-tests/node_modules node tests/test_category_tabs.cjs
```

These browser fixtures and PHP syntax checks do not replace a deployment check on the real WordPress host.


### Full-height diagonal artwork and borderless grain tabs (v1.7.5)

- The diagonal card artwork now uses the section-qualified `.ab-product-categories .ab-category-card-art` selector with `height: 100%`, `min-height: 100%` and `object-fit: cover`. This overrides Home's general `.ab-bhavik-page img { height: auto; }` rule, which previously reduced the image to its intrinsic ratio and left the lower slanted area empty. Breakpoint image-width overrides use the same specificity. Artwork stays fully opaque, including on phones.
- Tabs, supply cards, panel and CTAs use rounded corners, soft blurred **shadows** and inset highlights, not borders or zero-blur spread rings. The active tab keeps its solid-looking dark teal gradient; its image has no blur, colour filter, mask or fade. Keyboard focus outlines remain for accessibility.
- Every material selector on Home/About independently references the exact supplied transparent PNG: `https://bhavikenterprises.in/wp-content/uploads/2026/11/image-grains.png`. Each `<img data-category-grains="hdpe" ...>` (or other material key) has its own literal `src`; there is no shared CSS image or runtime source array. To change only HDPE later, replace that image's `src` in both `page-template/template-home.php` and `page-template/template-about.php`. Repeat for any other material as needed. The existing local supply-card photos are unchanged.
- Grain images are contained without distortion in a bottom-aligned 112px area, with room reserved below the tab labels. The source PNG is not transformed or re-encoded.
- The category browser fixture now includes the actual `.ab-bhavik-page` Home wrapper, so generic theme image rules participate in the cascade. Added checks cover image-to-card top/bottom bounds, full opacity, border widths, and every grain element's exact URL, contain/bottom alignment, and lack of filters/masks. Home/About checks cover desktop, tablet, phone and layout boundaries, alongside existing tab, touch, keyboard and route assertions. Python checks: 11 tests.

The supplied remote PNG could not be downloaded in the sandbox. Browser fixtures deliberately abort that external image request and validate its literal URL and layout rules; they do **not** claim to verify the actual remote image pixels, availability or transparent padding. Confirm its final appearance on the WordPress host, or supply the PNG file for a complete local visual review. No Knowledge Center or shared header/menu code is changed.


### Compact selectors and desktop 2×2 supply cards (v1.7.6)

- Material selector minimum height is reduced from 236px to 176px, with smaller reserved image space (80px rather than 112px) and tighter padding. Long material names can still grow the tabs naturally without overlapping the grain image. Fonts, tab widths, active styling, grain sources, keyboard and arrow behaviour are unchanged.
- **Only at desktop widths of 1200px and above**, the four supply cards now form a two-column/two-row grid. The diagonal window reveals 30% of a card at the top and 50% at the bottom, approximately 40% on average. The photo backs the right half of the card so it fills that entire slanted window. Desktop text/button widths are constrained to suit the wider image; existing full-height cover sizing and borderless soft shadows remain intact.
- Existing tablet/phone card media rules (1199px, 1100px, 700px and 420px) are unchanged, including their grid, photo widths and slant geometry. The shorter tabs apply across screen sizes as requested. No templates, product content, tab JavaScript, shared navigation or Knowledge Center code is changed.
- Regression checks now cover compact tab bounds, label/image separation, desktop 2×2 columns and diagonal geometry, and unchanged smaller-screen image ratios. All 12 Python tests pass. Controlled browser layout cases and isolated touch/rotation/reduced-motion/no-JS checks passed; longer batched Chromium runs in this sandbox also exhibited intermittent actionability timeouts and a browser startup crash. Use `CATEGORY_WIDTHS`/`CATEGORY_LAYOUTS` to isolate viewport cases; `CATEGORY_INTERACTION_ONLY=1` and `CATEGORY_SKIP_INTERACTIONS=1` split interaction checks from layout checks. The supplied remote grain PNG remains excluded from offline browser downloads as documented above.

### Category edge/background refinement

Only category card/tab shadows and the section background tint are refined. Soft bevel overlays sit above the artwork so their highlights stay visible; they have `pointer-events: none` and do not add borders, blur images, or affect sizing. The selected tab keeps its existing dark-teal background. The section gradient's tint strength increases by 20% relative to white (`#f2f8f7` → `#eff7f5`, equivalent to a 20% → 24% blend of `#bedcd7` over white). No layout, breakpoint, typography, image, content, tab-controller, header or Knowledge Center changes are included. The existing CSS file-modification-time cache version picks up the stylesheet change without a theme-wide version bump.

Validation: all 13 Python tests pass. Controlled Home/About before/after browser comparisons at 1440, 1200, 1199, 768, 390 and 320px confirm identical tab/card/text/link geometry, borderless click-through bevels, working next-material controls and no page overflow. As before, the remote grain image request is excluded from offline fixtures; its source is unchanged.
