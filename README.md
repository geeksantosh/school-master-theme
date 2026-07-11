# School Master — WordPress Theme for Schools, Colleges & Campuses

A flexible, self-hosted WordPress theme for educational institutions. Everything
that changes from one school to the next — branding, colors, contact details,
courses, notices, faculty, homepage layout — is configured from the WordPress
admin. **No code editing is required per site.**

The product ships as two pieces:

| Package | Role |
| --- | --- |
| **School Master** (`school-master/`) | The theme — all presentation, templates, Customizer options. |
| **School Master Core** (`school-master-core/`) | Companion plugin — registers the content types (Courses, Notices, Faculty, Events, Gallery, Downloads, Testimonials, Partners) and their fields. |

> **Why two packages?** Content lives in the plugin, not the theme. That means a
> school can update or even switch themes later without losing its courses,
> notices or faculty. This is the standard, recommended split for commercial
> WordPress themes.

---

## Requirements

- WordPress **6.0** or newer
- PHP **7.4** or newer
- A standalone WordPress install (own database, domain and hosting). This is a
  single-site theme; it is not built for Multisite networks.

---

## Installation

Install the plugin **first**, then the theme.

### 1. Install the companion plugin

1. In wp-admin, go to **Plugins → Add New → Upload Plugin**.
2. Choose `school-master-core.zip` and click **Install Now**.
3. Click **Activate**.

Activating flushes permalinks automatically, so the content-type archives
(`/courses/`, `/notices/`, …) work immediately.

### 2. Install the theme

1. Go to **Appearance → Themes → Add New → Upload Theme**.
2. Choose `school-master.zip` and click **Install Now**.
3. Click **Activate**.

If you activate the theme before the plugin, an admin notice appears at the top
of the dashboard with a one-click link to install and activate School Master
Core. Follow it and you are set.

> **Zipping from source:** if you are working from this repository rather than
> pre-built zips, create each zip from the *folder* so the archive contains
> `school-master/style.css` and `school-master-core/school-master-core.php` at
> its top level.

---

## Quick start: import demo content (optional)

Want to see the finished layout before you start? With the theme **and** the
*School Master Core* plugin both active, go to **Appearance → Demo Content** and
click **Import Demo Content**. This seeds sample courses, notices, faculty,
events and downloads, fills in the homepage sections and statistics, sets a
static front page and builds a starter navigation menu — so your homepage looks
complete immediately. Edit or delete any of it afterwards.

Changed your mind? The same screen has a **Remove Demo Content** button that
deletes only what the importer created; anything you added yourself is kept.

Demo items ship without photos — add your own *Featured images* to faculty,
courses and the hero for the full effect.

---

## First-time setup checklist

Work top to bottom and the site is presentable in about 15 minutes. (If you ran
the demo import above, most of these are already done — review and replace with
your real details.)

1. **Set the homepage.** Go to **Settings → Reading → Your homepage displays →
   A static page**. The theme's `front-page.php` builds the sections
   automatically, so any page can be the front page. (Create an empty "Home"
   page and select it, plus a "Blog" page for Posts if you want a news feed.)
2. **Upload your logo & colors.** **Appearance → Customize → Site Identity** for
   the logo; **Appearance → Customize → Brand Colors** for the palette.
3. **Fill in contact details.** **Customize → Top Bar & Contact**.
4. **Add social links.** **Customize → Social Links** (blank ones are hidden).
5. **Build the homepage.** **Customize → Homepage Sections** (see below).
6. **Add content.** Create your Courses, Notices, Faculty, etc. from the admin
   menu (see *Managing content*).
7. **Build the menus.** **Appearance → Menus** — assign one menu to the
   *Primary* location and, optionally, one to *Footer*.
8. **Permalinks.** If any archive 404s, visit **Settings → Permalinks** and
   click **Save** once to refresh the rewrite rules.

---

## Customizer reference

Everything below lives under **Appearance → Customize**.

### Site Identity
- Logo, site title, tagline, site icon (favicon). Title and tagline update live
  in the preview.

### Top Bar & Contact
- Toggle the top bar on/off.
- Address, phone and email — shown in the header top bar and reused elsewhere.
- **Action buttons** — up to two buttons at the far right of the top bar (e.g.
  "Apply Now", "Login"). Each takes a label, a URL and a style (**solid** or
  **outline**). Leave a label blank to hide that button. If a button is set,
  the top bar still shows even when the toggle above is off.

### Notice Ticker & Popup
Two independent, optional ways to surface Notices site-wide. Both pull from the
**Notices** content type (important notices first), and both stay hidden until
the *School Master Core* plugin is active and at least one notice exists.

- **Scrolling ticker** — a marquee under the header on every page. Toggle it on,
  set the label (default "Notices"), how many notices to include, and the scroll
  speed (slow / normal / fast). Important notices get a "New" flag. Motion pauses
  on hover/focus and is disabled for visitors who prefer reduced motion.
- **First-visit popup** — a dismissible modal shown once per browser session
  (off by default). Pull **important notices** automatically, or write a
  **custom** title/text and an optional button.
  - In *important* mode, **every** notice marked important pops up — one at a
    time, newest first. Closing one reveals the next; once all are closed (or
    the visitor follows a "Read more" link), nothing else pops up for that
    session. A dismissed notice never re-opens on the page it links to.
  - Give any notice a **"Popup until"** date to stop it popping up after that
    day; leave it blank to keep showing it. (See *Managing content*.)
  - A notice's **Attachment** shows inside the popup too: image files appear as
    a preview, and PDFs/documents get a "View attachment" button.
  - If a message changes, visitors who dismissed the old version see the new one.

### Brand Colors
| Setting | Default | Where it shows |
| --- | --- | --- |
| Primary Color | `#0b5394` | Links, buttons, header accents |
| Secondary / Accent Color | `#e8a33d` | Highlights, hovers, call-to-action |
| Dark Color | `#0b2545` | Footer background, dark headings |

Colors are injected as CSS custom properties, so the whole site re-skins from
these three controls.

### Social Links
- Facebook, Instagram, YouTube, X/Twitter, LinkedIn, TikTok. Enter a full URL;
  any left blank is simply not rendered. Icons are built-in inline SVGs (no
  external requests, no icon font to load).

### Homepage Sections
A panel with one section per homepage block. Each block has an **Enable**
toggle, so a school shows only what it needs. Blocks render top to bottom in the
order below:

1. **Hero** — background image *or* a YouTube video, with a title, subtitle and
   call-to-action button.
2. **Notice Board** — pulls the latest Notices. Set the section title and how
   many to show.
3. **Welcome / About** — heading, rich text and an image.
4. **Courses** — a grid of Courses. Set title and count.
5. **Why Choose Us** — feature highlights (ships with four sensible defaults).
6. **Statistics Counters** — up to four animated number counters (e.g.
   "1200 Graduates"). Each has a number and a label.
7. **Latest News** — recent blog Posts.
8. **Partners** — logos from the Partners content type.
9. **Call to Action** — a closing banner with a button (e.g. "Ready to Apply?").

### Footer
- Custom copyright text (leave blank to fall back to the site name + year).

---

## Managing content

The companion plugin adds these items to the wp-admin menu. Each has its own
**Details** box on the edit screen for the extra fields listed here.

| Content type | Extra fields | Categories/Taxonomy | Public archive |
| --- | --- | --- | --- |
| **Courses** | Duration, Total Seats, Eligibility, Fee | Course Categories | `/courses/` |
| **Notices** | Mark as important, Popup until (date), Attachment (PDF/Doc) | Notice Categories | `/notices/` |
| **Faculty** | Designation, Qualification, Email, Phone, Facebook URL | Departments | `/faculty/` |
| **Events** | Start Date, End Date, Location | — | `/events/` |
| **Gallery** | (uses the featured image) | Albums | `/gallery/` |
| **Downloads** | File | Download Categories | `/downloads/` |
| **Testimonials** | Author Role | — | shown on-site only |
| **Partners** | Website URL | — | shown on-site only |

Tips:
- **Featured images matter.** Faculty photos, course cards, gallery items and
  partner logos all use the *Featured image*. Set one on every item.
- **Important notices** (checkbox) are highlighted and pinned to the top of the
  notice list, and are the ones eligible for the first-visit popup. Use
  **Popup until** to give an important notice a last day in the popup (blank =
  no expiry).
- **Attachments / files** use the WordPress media library — click *Select File*
  in the Details box.

### Recommended image sizes
The theme registers and crops these automatically, but starting from a large,
well-composed image gives the best result:

| Use | Size |
| --- | --- |
| Cards (courses, gallery, news) | 400 × 280 |
| Faculty photos | 300 × 300 (square) |
| Hero background | 1600 × 700 |

---

## Menus & widgets

- **Menus** — *Appearance → Menus*. Locations: **Primary** (main navigation,
  supports dropdown sub-menus) and **Footer**.
- **Widgets** — *Appearance → Widgets*. The sidebar (shown on blog/archive
  pages) and footer widget areas are widget-ready.

---

## Translation & localization

Both packages are translation-ready.
- Theme text domain: `school-master`
- Plugin text domain: `school-master-core`

Drop a `.po/.mo` pair into the respective `languages/` folder, or use a plugin
such as Loco Translate. All user-facing strings are wrapped for translation.

---

## Frequently asked questions

**I activated the theme but there's no content.** Install and activate the
*School Master Core* plugin — it provides Courses, Notices, Faculty, etc. The
dashboard notice links you straight to it.

**A homepage section is missing.** Open *Customize → Homepage Sections* and check
that section's **Enable** toggle. Some sections (Notices, Courses, News,
Partners) also need at least one matching item to display.

**An archive page shows "Page not found."** Go to *Settings → Permalinks* and
click **Save Changes** once. This refreshes WordPress's URL rules.

**Can I switch themes later without losing content?** Yes — the content types
live in the plugin, so your courses, notices and faculty stay intact.

**Does this work on WordPress Multisite?** The theme is built and supported for
standalone single-site installs (its intended use). It is not tested against
Multisite networks.

---

## Support & credits

- Theme version: 1.0.0
- Plugin version: 1.0.0
- License: GNU GPL v2 or later

For setup questions, start with the *First-time setup checklist* and the FAQ
above.
