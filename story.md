You are working on the existing Laravel project, want to turn landing page of this as a platform showcasing traditional Newar clay pottery from **Thimi, Nepal**.

### Objective

Redesign **ONLY the landing/home page UI** based on the backend and database structure that already exists.

The backend is already complete. The database, models, relationships, product/category data, and required functionality are already implemented.

Your responsibility is to **understand the existing backend and build a polished, modern landing page that consumes the existing data correctly.**

**Do NOT redesign or rebuild the backend.**

---

# 1. First Understand the Existing Application

Before writing or changing frontend code, inspect the existing project.

Pay particular attention to:

* `HomeController.php`, especially lines 27–30


The purpose of this inspection is **not to change the backend**.

The purpose is to understand:

> **What data is already available to the homepage?**

Main Design Direction

Transform the homepage into a:

## **Traditional Pottery Marketplace + Cultural Storytelling Platform**

The visitor should immediately understand:

**Jheekuma is a place to discover traditional Newar clay pottery from Thimi, Nepal.**

The website should feel like a combination of:

* Premium artisan marketplace
* Traditional craft showcase
* Cultural heritage platform
* Modern Nepalese design
* Editorial storytelling website

It should NOT look like:

* Generic e-commerce template
* Generic Bootstrap website
* Corporate SaaS website
* Ordinary product catalog

The design should feel **warm, authentic, handcrafted, premium, and culturally connected.**


Recommended Homepage Structure

Build the landing page with the following sections, using **existing backend data wherever applicable**.

---

## Section 1 — Hero

Create a visually powerful hero section.

The hero should communicate:

**Traditional Newar pottery from Thimi, Nepal.**

Use the best available pottery/product imagery from the existing project.

Possible messaging direction:

> **Where Clay Becomes Culture**

Supporting text:

> Discover traditional clay pottery shaped by generations of Newar craftsmanship in Thimi, Nepal.

Primary CTA:

**Explore Pottery**

Secondary CTA:

**Discover Our Tradition**

The hero should be image-driven and immersive.

Avoid excessive text.

---

Featured / Popular Pottery

Immediately introduce the marketplace aspect.

Section heading:

**Explore Our Pottery**

or

**Discover Traditional Pottery**

Display products dynamically from the existing backend.

Each product card should potentially include:

* Product image
* Product name
* Category
* Price, if available
* Short description, if available
* Availability, if available
* View Details CTA

Use the existing product data.

Do not invent fields.

The cards should look premium and image-focused.

Prioritize the pottery photography.

---

Categories

Create a beautiful category discovery section using the existing category data.

For example, if the backend contains categories such as:

* Cooking pottery
* Storage pottery
* Water vessels
* Ritual pottery
* Decorative pottery

display them dynamically.

But **do not assume these categories exist**.

Read the actual database/category data and render whatever exists.

Category cards should contain:

* Category image where available
* Category name
* Short description where available
* Product count where possible
* Explore category CTA

Make this section visually different from the product grid.

---

Cultural Storytelling Section

Jheekuma should not feel like only a shop.

Introduce the cultural context behind the products.

Create an editorial-style section around:

## **More Than Clay**

Explain the relationship between:

**Clay → Craft → Community → Culture → Tradition**

Use existing content if the project already contains appropriate content.

The section should communicate the importance of traditional pottery within Newar culture and Thimi.

Use authentic imagery from the existing assets where available.

The design should feel like a cultural magazine/editorial section.

---

# 9. Pottery Making Process

Create a visually engaging section showing the pottery journey.

Possible visual sequence:

**Clay → Shape → Dry → Fire → Finished Pottery**

If the existing backend/content provides process images or information, use them.

If not, create the UI structure so content/images can easily be added later.

Do not fabricate historical information.

This section should visually communicate the handmade nature of the products.

---

# 10. Thimi & Newar Pottery Heritage

Create a dedicated section connecting the products to their origin.

Concept:

## **From Thimi, With Tradition**

Highlight the relationship between:

* Thimi
* Newar craftsmanship
* Traditional pottery
* Community
* Generational knowledge

This should establish authenticity and provenance.

The section should feel more like storytelling than advertising.

---

Stories / Blog

If the backend already contains blogs/articles/stories, use the existing data.

Create:

## **Stories From the Tradition**

Display the latest or featured content.

Each card can contain:

* Image
* Title
* Excerpt
* Date
* Read More

Use the actual blog data.

If the backend does not provide blogs, do not create fake blog records. Simply avoid rendering this section or structure it based on what is actually available.

---

Artisan / Community Section

Check whether the existing backend contains artisan/community information.

If it does, create a:

## **Meet the Makers**

section.

Display available:

* Name
* Image
* Description
* Craft specialty
* Story

Again, only use existing data.

If artisan data does not exist, don't create fake artisans.

---

Product Discovery UX

The homepage should make it very easy to continue into the existing marketplace.

Use clear CTAs such as:

* Explore Pottery
* View All Products
* Browse Categories
* View Details
* Discover the Tradition
* Read Our Stories

All links must use the **existing routes**.

Do not create unnecessary new routes.

---

Visual Design System

Create a strong visual identity inspired by:

### Earth

Clay
Terracotta
Sand
Natural stone
Wood
Traditional Nepalese architecture

Use a warm, earthy palette.

Possible direction:

* Cream / off-white backgrounds
* Terracotta-inspired accents
* Deep brown / charcoal typography
* Muted natural tones
* Subtle neutral borders

Avoid making every section the same color.

Create visual rhythm using:

* Large imagery
* White space
* Editorial layouts
* Alternating sections
* Asymmetric compositions where appropriate
* Large typography
* Subtle borders
* Elegant cards

---

Typography

Typography should feel:

**Modern + Editorial + Traditional**

Use strong hierarchy between:

* Hero heading
* Section headings
* Product titles
* Supporting text
* Metadata

Do not overuse decorative fonts.

Prioritize readability.

---

Product Card Design

Product cards are extremely important.

They should emphasize the actual pottery.

Recommended structure:

```text
┌──────────────────────────┐
│                          │
│      PRODUCT IMAGE       │
│                          │
├──────────────────────────┤
│ Category                 │
│ Traditional Water Pot    │
│ Short description...     │
│                          │
│ Price          View →    │
└──────────────────────────┘
```

Use subtle interactions such as:

* Image zoom on hover
* Card elevation
* Smooth transitions
* CTA appearance

Keep animations subtle.

---

# 17. Image Handling

Inspect how the current project stores and serves product images.

Use the existing image/storage system.

Do not introduce another image management system.

Make sure:

* Missing images don't break the layout
* Images have appropriate aspect ratios
* Images are lazy-loaded where appropriate
* Images have useful alt text
* Product images remain visually prominent

---

# 18. Responsive Design

The homepage must be carefully designed for:

### Desktop

Large editorial layouts and product grids.

### Tablet

Balanced two/three-column layouts.

### Mobile

Strong vertical storytelling and easy product browsing.

Pay special attention to:

* Hero cropping
* Navigation
* Product cards
* Category cards
* Typography
* CTA buttons
* Image ratios
* Section spacing

Do not simply shrink the desktop design.

---

# 19. Animation

Use animation sparingly.

Good examples:

* Fade/slide-in sections
* Image hover zoom
* Product card hover
* Button transitions
* Subtle scroll animations

Avoid:

* Excessive parallax
* Flashy animations
* Long loading animations
* Animation on every element

The pottery itself should remain the visual focus.

---

# 20. Navigation

Review the existing navigation and improve its presentation if necessary.

Suggested conceptual structure:

**Jheekuma**

Home
Pottery
Categories
Our Tradition
Stories
About
Contact

If existing cart/account/order functionality exists, preserve and integrate it appropriately.

Do not break existing navigation.

---

# 21. Footer

Create a polished footer containing appropriate existing links.

Potential structure:

**Jheekuma**

Traditional pottery from Thimi, Nepal.

Navigation
Pottery
Categories
Our Tradition
Stories
Contact

Social links if already configured.

Copyright.

Do not invent social media accounts.

---

# 22. Important: Do Not Touch Backend

This is a **frontend/landing-page redesign task**.

Do NOT:

* Create migrations
* Modify database schema
* Create new models
* Rewrite controllers unnecessarily
* Change product logic
* Change order logic
* Change authentication
* Change APIs
* Change admin functionality
* Change existing business logic

You may make **minimal changes to `HomeController.php` only if required to expose existing backend data that is already available through the existing models/relationships.**

If you believe backend changes are necessary, stop and explain exactly why before making them.

---

# 23. Code Quality

Follow the existing project's conventions.

Before creating new components, check whether equivalent components already exist.

Prefer reusable Blade components when appropriate.

Avoid:

* duplicated markup
* inline styles everywhere
* hardcoded product data
* unnecessary JavaScript
* unnecessary dependencies
* unnecessary CSS frameworks
* duplicate layouts

Use the project's existing frontend stack.

If the project uses Tailwind, use Tailwind.

If it uses Bootstrap, use Bootstrap.

If it has custom CSS, work within that system.

**Do not introduce a new frontend framework simply for the redesign.**

---

# 24. Implementation Process

Follow these steps in order.

### STEP 1 — Inspect

Understand:

* HomeController
* Homepage route
* Homepage Blade
* Existing layout
* Models
* Existing data passed to homepage
* Existing frontend stack
* Existing assets
* Existing routes

### STEP 2 — Map Data to UI

Before coding, determine:

| Existing Data          | Homepage Usage                    |
| ---------------------- | --------------------------------- |
| Products               | Product showcase                  |
| Categories             | Category discovery                |
| Product images         | Hero/product imagery              |
| Blog/articles          | Cultural stories                  |
| Other existing content | Appropriate storytelling sections |

Only use data that actually exists.

### STEP 3 — Implement

Build the redesigned homepage.

Start with:

1. Layout
2. Hero
3. Product showcase
4. Categories
5. Cultural storytelling
6. Pottery process
7. Stories
8. Artisan/community if available
9. CTA
10. Footer

### STEP 4 — Polish

Improve:

* spacing
* typography
* image treatment
* responsive behavior
* hover states
* transitions
* visual hierarchy

### STEP 5 — Verify

Check:

* All products render correctly
* Categories render correctly
* Images work
* Existing routes work
* Product links work
* Mobile layout works
* Desktop layout works
* No console errors
* No broken images
* No hardcoded database values
* No existing functionality has been broken

---

# 25. Final Deliverable

When finished, report:

### Files Modified

List every file changed.

### UI Changes

Briefly explain each homepage section implemented.

### Backend Usage

Explain which existing backend variables/data were consumed.

### Components

List any new Blade components created.

### Dependencies

Mention if any new dependency was added.

Prefer **zero new dependencies** unless genuinely necessary.

### Backend Changes

State clearly:

> Backend/database changes: None

unless a minimal controller adjustment was genuinely required.

### Remaining Improvements

Mention any improvements that would require additional backend data or content in the future.

---

# Final Principle

Think of the task as:

> **"Build the best possible frontend experience around the backend that already exists."**

Do not redesign the backend.

Do not invent data.

Do not replace working functionality.

**Inspect → Understand → Map existing data → Design → Implement → Polish → Verify.**

The final homepage should make Jheekuma feel like a **premium digital home for traditional Newar pottery and culture from Thimi, Nepal**, while remaining fully compatible with the existing Laravel application.
