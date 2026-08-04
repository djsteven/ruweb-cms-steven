---
name: AmaTierra Website
version: 1.0.0
description: Dark editorial design system for AmaTierra Yoga & Wellness Retreat's public website.
colors:
  ink:
    value: "#0F1710"
    role: Primary page background. Deep forest-black, the default surface of the site.
  inkAlt:
    value: "#1A2A1C"
    role: Alternating section background, card surfaces, and the hero text band.
  moss:
    value: "#2D4A2F"
    role: Mid-tone green for panels, gradient stops, and quiet elevation.
  sage:
    value: "#4A7A4D"
    role: Lighter green for status indicators, available/open badges, and subtle brand cues.
  dusk:
    value: "#8FB58E"
    role: Muted green for icon strokes and low-emphasis iconography.
  bone:
    value: "#F0EBE0"
    role: Primary text color on dark surfaces. Warm off-white, never pure white.
  parchment:
    value: "#E8E0D0"
    role: Warm light surface for the rare light-on-dark inversion block.
  gold:
    value: "#C9A96E"
    role: Primary accent. Eyebrows, rules, primary buttons, active states, and premium detail.
  goldPale:
    value: "#E8D5A8"
    role: Editorial italic accents inside headlines, and hover state for gold fills.
  white:
    value: "#FAF8F3"
    role: Maximum-contrast text only. Reserved, not a background.
typography:
  display:
    fontFamily: Italiana
    fontWeight: 400
    color: "#F0EBE0"
    letterSpacing: "-0.01em"
    role: Large headlines, section titles, statistics, and the logo wordmark.
  editorialAccent:
    fontFamily: Playfair Display
    fontStyle: italic
    fontWeight: 400
    color: "#E8D5A8"
    letterSpacing: "0"
    role: Short emotional phrases inside a display headline. Never full paragraphs.
  body:
    fontFamily: DM Sans
    fontWeight: 300
    color: "rgba(240, 235, 224, 0.55)"
    letterSpacing: "0"
  eyebrow:
    fontFamily: DM Sans
    fontWeight: 300
    color: "#C9A96E"
    letterSpacing: "0.3em"
    textTransform: uppercase
    fontSize: "10px"
  uiLabel:
    fontFamily: DM Sans
    fontWeight: 300
    color: "rgba(240, 235, 224, 0.65)"
    letterSpacing: "0.18em"
    textTransform: uppercase
radii:
  none: "0"
  sm: "2px"
  full: "9999px"
spacing:
  xs: "4px"
  sm: "8px"
  md: "16px"
  lg: "24px"
  xl: "40px"
  section: "120px"
  sectionInline: "64px"
components:
  buttonPrimary:
    backgroundColor: "#C9A96E"
    textColor: "#0F1710"
    hoverBackgroundColor: "#E8D5A8"
    borderRadius: "0"
    fontFamily: DM Sans
    fontWeight: 300
    letterSpacing: "0.2em"
    textTransform: uppercase
  buttonSecondary:
    backgroundColor: transparent
    textColor: "#F0EBE0"
    borderColor: "rgba(240, 235, 224, 0.3)"
    hoverBorderColor: "rgba(240, 235, 224, 0.6)"
    borderRadius: "0"
  card:
    backgroundColor: "#1A2A1C"
    borderColor: "rgba(255, 255, 255, 0.06)"
    borderRadius: "0"
    shadow: none
  circleMedia:
    borderRadius: "9999px"
    borderColor: "rgba(201, 169, 110, 0.35)"
    role: Photo circles in the "What AmaTierra Offers" section only.
assets:
  fonts:
    source: Google Fonts
    italiana: "Italiana:wght@400"
    playfairDisplay: "Playfair+Display:ital,wght@0,400;0,500;1,400;1,500"
    dmSans: "DM+Sans:wght@200;300;400"
---

## Overview

AmaTierra should feel like a private, cinematic retreat property: dark, quiet, spacious, and expensive. The reference point is a boutique hotel or an editorial travel magazine, not a wellness marketplace. The site sells two things above all else — **hosting a retreat** and **joining a retreat** — and every page should keep at least one of those paths visible.

This is a **dark-dominant** system. The default page surface is deep forest-black, and light surfaces are the exception. Do not invert this to a light theme; the premium feel depends on photography sitting inside dark space.

Use the `colors`, `typography`, and `components` tokens above as normative values. The prose below explains how to apply them.

## Color System

Default surfaces are `ink` and `inkAlt`. Alternate between them to create section rhythm — `ink` for the primary run of content, `inkAlt` for sections that need to feel slightly lifted (cards, the hero text band, dining, testimonials). `moss` appears mainly inside gradients and overlays, rarely as a flat fill.

`gold` is the single accent color. It carries eyebrows, thin rules, primary button fills, active navigation, dropdown labels, and hover borders. It should feel scarce. If a screen has more than a few gold elements competing, remove some.

`goldPale` is used only for the italic editorial phrase inside a headline, and as the hover state of a gold fill.

Never use pure `#FFFFFF` as a background. Text on dark surfaces is `bone`, and body copy sits at reduced opacity (`rgba(240, 235, 224, 0.55)`) so headlines keep hierarchy without changing size.

Photography is the main source of color. Images should be lightly desaturated at rest (`filter: saturate(0.85)`) and return to full saturation on hover where the image is interactive.

## Typography

Three families, each with one job:

- **Italiana** — display only. Headlines, section titles, large statistics, the logo wordmark. Never body copy, never UI labels, never below ~20px.
- **Playfair Display Italic** — editorial accents only. A short phrase inside an Italiana headline, or a pulled quote in a testimonial. Never a full paragraph.
- **DM Sans Light** — everything else. Body copy, navigation, buttons, labels, form fields, metadata.

The signature headline pattern is a display line with one italic phrase carrying the emotion:

```html
<h2 class="display-title">
    Where the forest
    <span class="text-editorial">heals.</span>
</h2>
```

Eyebrows sit above headlines in gold, uppercase, at `10px` with `0.3em` letter spacing, and are preceded by a short horizontal rule:

```html
<p class="overline">Why AmaTierra</p>
```

Letter spacing rules differ by role and are not interchangeable: display type is slightly negative (`-0.01em`), body is `0`, and uppercase UI/eyebrow text is widely tracked (`0.18em`–`0.3em`). Do not track out display headlines.

**Sizing note from the client:** Bob and Jill asked for all text site-wide to run larger than the original Figma sizing. Treat the Figma type scale as a floor, not a target. Body copy should not drop below `14px`.

## Layout

Sections are full-bleed bands, not nested cards inside containers. Horizontal padding is `64px` on desktop and `24px` below `900px`. Vertical section padding is `120px` desktop, `80px` mobile.

Grids use a `2px` gap, not a large gutter — adjacent cards read as a single divided surface rather than floating tiles. Card separation comes from a `1px` border at `rgba(255,255,255,0.06)`, never from shadow.

**Corners are square.** `border-radius: 0` is the default for buttons, cards, inputs, images, and panels. The only exceptions are the photo circles in the "What AmaTierra Offers" section and small dot indicators. This is a deliberate contrast with softer wellness-brand conventions and should not be rounded off.

Asymmetry is welcome in editorial sections: offset columns, rotated vertical labels, and unequal grid tracks are all in-system. Symmetry is the default for repeating content (retreat cards, feature circles).

## Motion

Motion should be slow and atmospheric, never bouncy. Standard transition is `0.3s`–`0.4s` ease for interactive states, `0.7s`–`1.4s` for image and section transitions.

- Scroll reveal: content fades up `28px` over `0.9s`, staggered `0.1s` per item.
- Hero reel: crossfade between frames over `1.4s`, holding roughly `4s` per frame.
- Image hover: scale to `1.04`–`1.06` over `0.6s`–`0.8s`.
- Marquee and testimonial scrollers: linear, `22s`–`35s` per loop, paused on hover.

Respect `prefers-reduced-motion`: disable the hero reel auto-advance, marquee, and scroll reveals, and render content in its final state.

## Components

**Buttons** are square, uppercase, widely tracked, and have generous horizontal padding (`40px`). Primary is a gold fill with ink text. Secondary is transparent with a low-opacity bone border. Hover on primary lightens to `goldPale`; hover on secondary raises border opacity. Never add a border radius.

**Cards** (retreat listings, feature blocks) use `inkAlt` background, a hairline border, and no shadow. Interactive cards lift slightly on hover and desaturate/resaturate their image.

**Navigation** is fixed, transparent over the hero, and gains a blurred `ink` background on scroll. Dropdowns are dark panels with a hairline gold border and uppercase section labels. The Retreats menu is a horizontal mega-menu with three columns separated by vertical hairlines; all other dropdowns are single-column. Below `1180px` the navigation collapses to a slide-out panel with tap-to-expand accordions.

**Forms** use transparent-dark inputs (`rgba(255,255,255,0.03)`), hairline borders, and a gold focus border. Labels are uppercase `10px` gold-muted. Selects use a custom gold chevron.

## Menu Structure

This is the client-approved structure. Item names are in English because that is the production site language.

- **Home**
- **Retreats** — three-column mega-menu:
  - *Retreat Types*: Group Yoga Retreats, Individual Yoga & Wellness Retreats, Upcoming Retreats
  - *Retreat Packages*: 5-Night Relax in Nature, 7-Night Yoga & Wellness, Detox Program (5 or 7 Nights), Detox at Home E-book, Herbal Medicine Workshops
  - *Tours & Activities*: Tours & Activities, Specials & Seasonal Offers, Optional Experiences
- **Wellness & Yoga** — Detox & Cleanse, Massage & Spa Therapies, Nutrition & Herbal Consultation, Energy Healing & Integrative Therapies, AmaTierra Integrative Herbs, Yoga & Meditation, Our Wellness Professionals
- **Restaurant**
- **Accommodations** — Accommodations Overview, Rooms & Amenities, Rates & Inclusions, Guest Services, FAQs
- **About Us** — Our Story & Our Team, Sustainable Property, Location & Getting Here, Guest Reviews, Gallery, Contact Us
- **Blog**
- **Book Now** — persistent button, styled as an outlined gold CTA

Unresolved with the client: whether Book Now should also appear as a menu item, and where newsletter sign-up lives. `Specials & Seasonal Offers` currently appears in two places in the client's notes; confirm before wiring routes.

## Home Page Structure

Section order is client-approved and should not be rearranged without confirmation:

1. **Hero** — full-bleed photo/video reel
2. **Host a Retreat / Join a Retreat** — dual full-height panels
3. **What AmaTierra Offers + Why Choose Us** — merged, four large photo circles
4. **Solo Retreats** — with photo
5. **Dining** — the Restaurant menu item anchor-links here
6. **Upcoming Retreats**
7. **Testimonials**
8. **Footer**

### Hero rules

No text sits on top of the imagery. The reel runs full-bleed, and the headline, supporting line, and CTAs live in an `inkAlt` band directly below it. The logo appears large on the first frame only, then the reel continues without it. First frame is the yoga studio photo; second is the drone forest shot. The reel must include: rooms, people doing yoga, greenhouse, food, nature trail, pool.

The main headline is `Group Retreats in Costa Rica`. Do not use the word "Yoga" in the main title — the property hosts other retreat types. Prefer "host a retreat" phrasing over "yoga retreat" throughout.

### Host / Join panels

The two panels are the highest-priority conversion element on the site and should read at equal weight. Each is a full-height photo panel with a dark bottom gradient, an eyebrow, a display headline with an italic accent, supporting copy, and an outlined CTA. Panels widen slightly on hover; supporting copy fades in.

### What AmaTierra Offers

Four large photo circles, not six small ones, and not icons. Each circle links to its most relevant page. Use this copy verbatim:

> **What AmaTierra Offers**
>
> - Support for group leaders to host memorable retreats at reasonable rates
> - A variety of retreats for individuals to choose from where you can immerse in nature and renew your Spirit
> - A supportive environment for reflection and healing where you can be yourself and transform your energy to become more positive and whole
>
> *Our unique forest environment, friendly staff, comfortable accommodations and healthy, delicious food all add to a retreat experience that you will remember forever.*

## Booking Flow

AmaTierra does not take payment on the website. The reservation page is an inquiry form only; a team member follows up by email or phone.

The form opens with two selectable boxes acting as a single-select choice:

- `I would like to host a group retreat`
- `I want to join a retreat`

Selected state uses a gold border, a tinted gold background, and a filled gold icon circle.

Do not include on the reservation form:

- Street address, address line 2, city, state/province, or postal code — keep **Country** only
- `Number of children under 12`
- The `I'd like rates for...` checkbox group
- Any payment fields

Submit label is `Request availability`, followed by a reassurance line that no payment is required to inquire.

## Video Assets

The testimonial video is embedded, not linked — use a responsive YouTube iframe on the Host a Retreat page. Source is vertical (Shorts format); constrain its container rather than letterboxing it into a wide frame.

The funnel/advertising video belongs on the home page. After it plays, the visitor must be routed toward the retreat-leader signup form — place a `Host a Retreat` CTA immediately beneath it.

A new vertical ad for individual retreats replaces the existing individual-retreat ad when the client delivers it; its likely home is the Solo Retreats section.

## Accessibility

Default readable text is `bone` on `ink` or `inkAlt`. Body copy at reduced opacity must stay at or above `rgba(240,235,224,0.55)`; do not go lighter for essential content.

Gold on dark passes for large text and decorative use. Do not use gold for small body copy. On a gold fill, text must be `ink`, never bone or white.

Interactive elements need visible focus states: a gold outline on dark surfaces, and an ink outline on gold surfaces. The site uses a custom cursor on desktop — ensure it is disabled for touch and keyboard users and never replaces focus indication.

Photo panels that carry text must keep a gradient overlay dark enough to hold contrast at the text position, not just at the panel edge.

## Tailwind And CSS

Fonts load from Google Fonts. Tailwind v4 tokens are exposed through the `@theme` block:

- Fonts: `font-display` (Italiana), `font-editorial` (Playfair Display Italic), `font-sans` (DM Sans)
- Colors: `ama-ink`, `ama-ink-alt`, `ama-moss`, `ama-sage`, `ama-dusk`, `ama-bone`, `ama-parchment`, `ama-gold`, `ama-gold-pale`
- Spacing: `p-section` (120px), `px-section-inline` (64px)

Use `.text-editorial` for the Playfair italic accent inside display headlines. Use `.overline` for gold uppercase eyebrows with the leading rule. Use `.display-title` for Italiana section headings.

Because the system is dark by default, set the base background and text color on `body` rather than per-section, and let light sections opt in explicitly.

## Font Licensing

Italiana, Playfair Display, and DM Sans are all served from Google Fonts under the SIL Open Font License and are cleared for commercial use. No license replacement is needed before launch.
