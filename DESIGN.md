# DESIGN.md

Design source of truth for the Amatierra implementation on top of this CMS.

Use this document before changing public templates, CSS theme tokens, page sections, media treatment, or visual copy. The goal is to keep the site calm, premium, nature-connected, and easy to maintain through the CMS.

## Brand Direction

Amatierra should feel:

- warm and restorative
- grounded in Costa Rican nature
- refined but not corporate
- spacious, tactile, and human
- trustworthy for wellness, hospitality, and retreat bookings

Avoid:

- generic hotel-template visuals
- heavy dark overlays on every image
- overly busy card grids
- loud gradients or neon accents
- decorative elements that compete with photography

## Visual Priorities

1. Photography carries the emotional weight.
2. Typography should feel calm, editorial, and readable.
3. Colors should come from earth, canopy, clay, sun, water, and natural materials.
4. Layouts should leave space for breathing, especially around retreat and wellness content.
5. Calls to action should be clear but not aggressive.

## Color Palette

Core palette placeholders. Replace exact hex values after the final visual reference is approved.

| Token | Purpose | Hex |
|---|---|---|
| `brand-forest` | Primary text, nav, footer, deep accents | `#173B32` |
| `brand-leaf` | Secondary accents, icons, subtle highlights | `#5F7F55` |
| `brand-clay` | Warm CTA, active states, small emphasis | `#B76E4C` |
| `brand-sun` | Soft highlight, badges, small warmth | `#D9A85C` |
| `brand-cream` | Page background | `#F7F1E8` |
| `brand-linen` | Alternating section background | `#EFE6D8` |
| `brand-charcoal` | Body text | `#25231F` |
| `brand-muted` | Secondary text | `#6F6A60` |
| `brand-white` | Light surfaces | `#FFFFFF` |

Implementation home:

```text
resources/css/app.css
```

Tailwind 4 theme tokens should be declared in the `@theme` block.

## Typography

Recommended direction:

- Display/headings: elegant serif or warm editorial face.
- Body/UI: highly readable sans-serif.
- Avoid condensed, futuristic, or overly decorative fonts.

Suggested pairings to evaluate:

- Cormorant Garamond + Inter
- Fraunces + Source Sans 3
- Libre Baskerville + Work Sans
- Lora + Nunito Sans

Rules:

- Do not scale font sizes with viewport width.
- Keep letter spacing normal unless a specific small label requires slight tracking.
- Hero H1 can be expressive; admin/UI and compact components must stay restrained.
- Spanish and English text must both be tested for wrapping.

## Layout System

Preferred feel:

- full-width sections with constrained inner content
- generous vertical spacing
- image-led hero sections
- editorial two-column sections for story/wellness content
- controlled grids for rooms, experiences, posts, and amenities

Avoid putting full page sections inside floating cards. Cards are for repeated items only.

## Components

### Buttons

Primary:

- background: `brand-clay`
- text: white or cream
- radius: small, not pill by default
- use for booking/contact CTAs

Secondary:

- transparent or cream background
- border in forest/clay
- use for learn-more actions

### Cards

Use for:

- rooms
- retreats
- blog posts
- review excerpts
- amenities

Rules:

- radius should stay subtle, around 6-8px
- avoid nested cards
- images should have stable aspect ratios
- titles and CTAs must not jump on hover

### Navigation

Header should be calm and readable:

- logo/name visible immediately
- menu items clear
- booking/contact CTA prominent but balanced
- mobile menu should be simple and touch-friendly

Footer should include:

- brand summary
- contact information
- menu links
- social links
- newsletter only if implemented for real

## Photography And Media

Photography should show the actual experience whenever possible:

- rooms and architecture
- forest/gardens
- yoga/wellness spaces
- dining/food
- guest experience
- landscape and arrival moments

Rules:

- Upload content images through the CMS media library.
- Store media references as IDs in page sections, not hardcoded paths.
- Use responsive image variants from the media system.
- Avoid dark, blurred, overly cropped images when users need to inspect the space.

Relevant implementation docs:

```text
docs/image-strategy.md
docs/pages-guide.md
docs/content-model.md
```

## CMS Page Sections

Recommended homepage sections:

1. `hero` - primary image, headline, short copy, booking/contact CTA
2. `intro` - short Amatierra story/value proposition
3. `stays` - rooms or lodging highlights
4. `wellness` - yoga, retreats, spa, healing focus
5. `experiences` - nature, local culture, day trips
6. `dining` - restaurant/food story if applicable
7. `google_reviews` - social proof from synced reviews
8. `cta` - final booking/contact prompt

Each section should include `is_visible` so editors can hide it without code changes.

## Suggested Pages

Initial sitemap:

- `/` Home
- `/rooms` Rooms / Stay
- `/retreats` Retreats
- `/wellness` Wellness
- `/experiences` Experiences
- `/restaurant` Dining
- `/about` About
- `/blog` Blog
- `/contact` Contact

For multilingual rollout, create translated entities rather than mixing languages inside one content JSON field.

## Admin And Editorial Rules

- Editable copy belongs in the database-backed content structure.
- Templates should not hardcode public marketing copy.
- Templates must respect `is_visible`.
- SEO values come from each entity's `meta` block plus global settings fallback.
- Header/footer navigation should be managed through CMS menus.
- Global brand/contact values should live in settings when reused across templates.

## Implementation Files

Primary styling:

```text
resources/css/app.css
```

Public layout:

```text
resources/views/layouts/public.blade.php
```

Page templates:

```text
resources/views/templates/*.blade.php
```

Template registration and editable section schemas:

```text
config/cms.php
```

Admin/editor section partials:

```text
resources/views/admin/pages/sections/*.blade.php
```

## Build And Deployment Notes

After frontend style/template changes:

```bash
npm run build
php artisan view:clear
php artisan config:clear
```

On cPanel, Node.js may need to be run from the local portable install if global `npm` is unavailable.

## Open Decisions

Fill these before final implementation:

- Final logo file and usage rules
- Final color values
- Final font pairing
- Photography set
- Primary booking/contact destination
- Spanish-only or Spanish + English launch
- Exact page list for launch
- Whether Google Reviews should render on the homepage
