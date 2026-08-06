---
name: AmaTierra Website
version: 1.2.0
description: Dark editorial design system for AmaTierra Retreat & Wellness Center.
colors:
  ink:
    value: "#0F1710"
    role: Primary page background. Deep forest-black, the default surface of the site.
  inkAlt:
    value: "#1A2A1C"
    role: Alternating section background and lifted dark panels.
  moss:
    value: "#2D4A2F"
    role: Mid-tone green for panels, overlays, and grounded brand detail.
  sage:
    value: "#4A7A4D"
    role: Secondary green for subtle highlights and organic accents.
  dusk:
    value: "#8FB58E"
    role: Muted green for low-emphasis iconography and quiet text accents.
  bone:
    value: "#F0EBE0"
    role: Primary text color on dark surfaces.
  parchment:
    value: "#E8E0D0"
    role: Warm light surface for rare inversions.
  gold:
    value: "#C9A96E"
    role: Eyebrows, rules, premium accents, and secondary CTA details.
  goldPale:
    value: "#E8D5A8"
    role: Warm highlight for editorial headings and special title moments.
  ctaGreen:
    value: "#25713B"
    role: Header booking CTA start color.
  ctaGreenLight:
    value: "#69B342"
    role: Header booking CTA end color and audience panel eyebrow color.
  white:
    value: "#FFFFFF"
    role: Text only on green CTA fills.
typography:
  display:
    fontFamily: Playfair Display
    fontWeight: 400
    color: "#F0EBE0"
    letterSpacing: "0"
    role: All major headings, hero headlines, section titles, and large editorial text.
  displayItalic:
    fontFamily: Playfair Display
    fontStyle: italic
    fontWeight: 400
    color: "#E8D5A8"
    letterSpacing: "0"
    role: Signature title moments, audience panel headings, and short emotional accents.
  body:
    fontFamily: DM Sans
    fontWeight: 300
    color: "rgba(240, 235, 224, 0.60)"
    letterSpacing: "0"
    role: Paragraphs, captions, navigation, forms, and general UI.
  eyebrow:
    fontFamily: DM Sans
    fontWeight: 300
    color: "#C9A96E"
    letterSpacing: "0.3em"
    textTransform: uppercase
    fontSize: "10px"
components:
  headerBookingCta:
    background: "linear-gradient(90deg, #25713B 0%, #69B342 100%)"
    textColor: "#FFFFFF"
    borderRadius: "999px"
    minHeight: "48px"
    minWidth: "142px"
    fontFamily: DM Sans
    fontSize: "14px"
    letterSpacing: "0"
    textTransform: uppercase
  audiencePanel:
    layout: Full-bleed two-column split panel
    headingFont: Playfair Display Italic
    bodyFont: DM Sans
    eyebrowColor: "#69B342"
    overlay: "dark green/black gradient over image"
  buttonPrimary:
    backgroundColor: "#C9A96E"
    textColor: "#0F1710"
    hoverBackgroundColor: "#E8D5A8"
    borderRadius: "0"
    fontFamily: DM Sans
    letterSpacing: "0.2em"
    textTransform: uppercase
  buttonSecondary:
    backgroundColor: transparent
    textColor: "#F0EBE0"
    borderColor: "rgba(240, 235, 224, 0.3)"
    borderRadius: "0"
assets:
  fonts:
    source: Google Fonts
    playfairDisplay: "Playfair+Display:ital,wght@0,400;0,500;1,400;1,500"
    dmSans: "DM+Sans:wght@200;300;400"
---

## Overview

AmaTierra should feel like a private, cinematic retreat property: dark, quiet, spacious, organic, and premium. The reference point is a boutique retreat sanctuary or editorial travel magazine, not a generic wellness marketplace.

The site should keep two visitor paths visible: hosting a retreat and joining a retreat. The visual system uses dark green space, warm gold accents, immersive video/photo, and large Playfair Display headings.

## Typography

Use two active families:

- **Playfair Display**: all major headings, hero title, section titles, large editorial words, and special italic heading moments.
- **DM Sans**: body copy, navigation, buttons, labels, form fields, metadata, and all standard UI text.

Do not use Italiana for new headings. `display-title` maps to Playfair Display. Paragraph text should remain DM Sans and should not drop below 14px.

The signature section-heading pattern is a Playfair heading with optional italic styling for special words:

```html
<h2 class="display-title">Group Retreats in Costa Rica</h2>
<h2 class="display-title italic text-ama-gold-pale">AmaTierra</h2>
```

Eyebrows sit above headings in uppercase DM Sans with a short horizontal rule. Standard editorial eyebrows use gold; the two audience split panels use green to match the supplied reference.

## Color System

Default surfaces are `ink` and `inkAlt`. Alternate between them to create section rhythm. Use `ink` for the darkest content runs and `inkAlt` for elevated editorial sections like the post-hero intro.

Gold is the premium accent and should stay scarce: eyebrows, rules, subtle highlights, and decorative details. The persistent header booking CTA is green, not gold, matching the client reference.

Body copy on dark surfaces should be `bone` at reduced opacity, usually between 0.60 and 0.72. Do not use gold for small paragraph copy.

## Header And Menu

The header is a fixed, light rounded capsule over the hero. It contains the AmaTierra logo, the main navigation, and a persistent green `Book Now` CTA.

Current main menu:

- Home
- Retreats
- Wellness & Yoga Programs
- Accommodations
- About Us
- Blog

Retreats uses a two-column mega menu:

- **Retreat Types**: Group Yoga Retreats, Individual Yoga & Wellness Retreats, Upcoming Retreats
- **Retreat Packages**: 5-Night Relax in Nature, 7-Night Yoga & Wellness, Detox Program (5 or 7 Nights), Detox at Home E-book, Herbal Medicine Workshops, Specials & Seasonal Offers

All menu items may start as `#` links in the CMS until the final pages are created.

## Home Page Structure

Current implemented section order:

1. **Hero**: full-screen video/photo background with Playfair Display heading and CTAs.
2. **Mountain Sanctuary Intro**: dark green editorial band immediately after the hero.
3. **Audience Split Panels**: two full-bleed image panels for retreat leaders and individual guests.
4. **Features / Why AmaTierra**: editable CMS feature content.
5. **CTA**: inquiry-focused call to action.
6. **Google Reviews**: optional visible reviews block.

### Mountain Sanctuary Intro

This section appears immediately after the hero and is editable through `sections.intro` in the CMS.

Default content:

- Eyebrow: `Mountain Sanctuary · Costa Rica`
- Heading: `AmaTierra`
- Body: `A private retreat sanctuary in Costa Rica’s mist-covered mountain forest — where the jungle itself becomes the teacher.`

Visual rules:

- Background: `inkAlt`
- Left column: gold rule/eyebrow and large italic Playfair title in `goldPale`
- Right column: DM Sans paragraph, muted bone, max width around 420px
- Desktop layout: wide left title column plus narrower right copy column
- Mobile layout: stacked content with generous spacing

### Audience Split Panels

This section appears after the Mountain Sanctuary Intro and is editable through `sections.audience` in the CMS.

Default panels:

- **For Retreat Leaders**: `Host / a Retreat`
- **For Individual Guests**: `Join / a Retreat`

Visual rules:

- Two equal full-bleed columns on desktop; stacked panels on mobile.
- Each panel uses a background image with dark overlay and subtle hover scale.
- Eyebrow: green uppercase DM Sans with a short green rule.
- Heading: large italic Playfair Display in `goldPale`, split across lines using line breaks.
- Body: DM Sans, bone at reduced opacity, max width around 680px.
- Entire panel may link to its configured CTA URL, while menu/page routes can remain placeholders until final content exists.

## Buttons

General primary buttons remain square gold editorial CTAs unless a component specifies otherwise.

The header `Book Now` button is a special green capsule:

- Gradient: `#25713B` to `#69B342`
- Text: white, uppercase, DM Sans, 14px
- Radius: full pill
- Arrow: right arrow `→`

## Layout

Sections are full-bleed bands, not nested cards inside decorative wrappers. Desktop horizontal padding is 64px; mobile horizontal padding is 24px. Desktop section padding is usually 120px, with compact editorial bands allowed when matching a supplied visual reference.

## Accessibility

Default readable text is `bone` on `ink` or `inkAlt`. Body copy at reduced opacity must remain legible. Interactive elements need visible focus states. Photo/video hero overlays must be dark enough to preserve heading contrast.

## Tailwind And CSS

Fonts load from Google Fonts. Tailwind v4 tokens are exposed through the `@theme` block:

- `font-display`: Playfair Display
- `font-editorial`: Playfair Display
- `font-sans`: DM Sans
- Colors: `ama-ink`, `ama-ink-alt`, `ama-moss`, `ama-sage`, `ama-dusk`, `ama-bone`, `ama-parchment`, `ama-gold`, `ama-gold-pale`, `ama-white`

Use `.display-title` for Playfair Display headings, `.text-editorial` for italic Playfair accents, and `.overline` for gold uppercase labels with a leading rule.
