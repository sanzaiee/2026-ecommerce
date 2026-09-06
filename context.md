You are working on the existing Laravel project,I want to turn this landing page to a platform showcasing and promoting traditional Newar clay pottery from **Thimi, Nepal**.

I want you to redesign the current landing/home page into a **modern pottery marketplace + cultural knowledge platform**, while preserving and reusing the project's existing architecture, database structure, models, relationships, routes, and available data.

## 1. First: Analyze the Existing Project

Before modifying any code, inspect the existing implementation thoroughly.

Start with:

* `HomeController.php`, especially lines 27–30
* The route that points to the landing/home page
* The current Blade view used by the landing page
* All related Models
* Database migrations/schema relevant to:

  * products
  * categories
  * product images
  * users/customers
  * orders, if relevant
  * blogs/articles/content, if already present
  * any other pottery/product-related entities
* Existing relationships between models
* Existing reusable Blade components/layouts
* Existing CSS/SCSS/Tailwind/Bootstrap structure
* Existing JavaScript/Vite setup
* Existing image/storage conventions
* Existing admin/CMS functionality that already manages products or content

Do NOT immediately create a new database structure.

First understand what data is already available and determine how the existing data can support the new design.

At the end of your analysis, briefly identify:

1. Current landing page architecture
2. Available product/category data
3. Available images
4. Existing content/blog functionality
5. Existing routes/controllers
6. Which existing components can be reused
7. What needs to be created
8. What should NOT be changed

Then proceed with implementation.

---

# 2. Overall Product Vision

Transform the landing page from a simple website homepage into a combination of:

**Traditional Pottery Marketplace + Cultural Heritage Platform + Storytelling Website**

The website should immediately communicate:

> "Discover authentic traditional Newar pottery from Thimi, Nepal."

The visitor should be able to:

* Discover different traditional clay pots
* Browse pottery by category
* View individual pottery products
* Understand how each pottery item is traditionally used
* Learn about Newar pottery traditions
* Learn about pottery-making techniques
* Discover the cultural significance of pottery
* Read stories/articles about Newar culture and pottery
* Learn about Thimi's pottery heritage
* Eventually purchase/order pottery

The site should feel like an **authentic cultural marketplace**, not a generic e-commerce template.

---

# 3. Landing Page Structure

Design the homepage with the following sections.

## A. Hero Section

Create a strong visual hero section focused on traditional pottery.

Include:

* Large high-quality pottery imagery using existing product images where possible
* Strong headline
* Short cultural description
* Primary CTA:

  * "Explore Pottery"
* Secondary CTA:

  * "Discover Our Tradition"

Suggested messaging direction:

"Handcrafted Clay. Living Tradition."

or

"Discover the Living Tradition of Newar Pottery"

The hero should visually communicate:

* Handmade
* Traditional
* Authentic
* Nepal
* Newar culture
* Thimi pottery heritage

Avoid making it look like a generic online shopping site.

---

# 4. Featured Pottery / Marketplace Section

Create a visually attractive product discovery section.

Display products dynamically from the existing database.

For each product card, consider showing:

* Product image
* Product name
* Category
* Short description
* Price, if available
* Availability, if available
* "View Details" button

Use real database data.

Do not hardcode products.

If the existing product structure contains additional useful fields, use them intelligently.

Provide:

* Featured products
* Latest products
* Popular/recommended products if the existing data supports this

Do not invent unsupported database fields just to populate the UI.

---

# 5. Pottery Categories

Create a category discovery section based on the **actual category data in the database**.

For example, if the database contains categories such as:

* Water pots
* Cooking pots
* Storage pots
* Ritual/religious pottery
* Decorative pottery
* Traditional household pottery

display them dynamically.

Each category should have:

* Image if available
* Category name
* Short description if available
* Product count if possible
* Link to browse that category

If category images do not currently exist, design the component so it gracefully handles missing images rather than introducing broken images.

---

# 6. "Explore Our Pottery" Marketplace Experience

The homepage should encourage users to continue browsing.

Add a section such as:

**Explore Traditional Pottery**

with:

* Category filters
* Product grid
* Clean product cards
* Responsive layout
* "View All Pottery" CTA

The experience should feel closer to a curated artisan marketplace than a conventional supermarket-style e-commerce website.

Prioritize:

* Large product photography
* Clean whitespace
* Natural visual hierarchy
* Authentic cultural storytelling
* Minimal but useful UI

---

# 7. Cultural Heritage Section

This is a major part of the redesign.

The website should not only sell/show products.

It should educate visitors about **Newar pottery traditions of Thimi**.

Create a section introducing:

### Newar Pottery Tradition

Explain, using existing content if available, topics such as:

* History of pottery in Thimi
* Importance of pottery in Newar culture
* Traditional uses of clay pots
* Religious and ceremonial uses
* Household uses
* Relationship between pottery and festivals
* Traditional craftsmanship
* Knowledge passed between generations
* Importance of preserving the craft

Do not fabricate historical claims.

If the project already contains blog/content data, use that data.

If content does not exist yet, create suitable content placeholders/components without pretending that unsupported historical facts are database content.

---

# 8. Pottery-Making Process

Create a visual storytelling section explaining the traditional pottery process.

Possible stages:

1. Selecting and preparing clay
2. Preparing the clay
3. Shaping the vessel
4. Drying
5. Decorating / finishing
6. Firing
7. Final preparation

Use existing images if available.

If the project does not contain process images, create a design that supports images being added later.

The design should make this section feel like a **craft story**, not a technical tutorial.

---

# 9. Thimi / Newar Cultural Story

Create a section connecting the pottery to its place and community.

The messaging should establish:

**Thimi → Newar community → pottery → tradition → craftsmanship**

Possible content structure:

> "More than a vessel"

Explain that traditional clay pottery represents:

* Everyday life
* Festivals
* Rituals
* Family traditions
* Craftsmanship
* Community identity
* Generational knowledge

Keep the tone authentic and respectful.

Do not over-commercialize the cultural content.

---

# 10. Articles / Stories / Blog Section

If the current application has blog/article/content models, use them.

Create a homepage section such as:

**Stories from the Pottery Tradition**

Display:

* Latest articles
* Featured story
* Article image
* Title
* Short excerpt
* Date
* "Read Story"

Possible article themes:

* Traditional Newar pottery
* Pottery of Thimi
* Pottery-making techniques
* Traditional festivals
* Cultural uses of clay pots
* Stories from local potters
* Preservation of traditional crafts
* Modern challenges faced by traditional pottery
* Traditional vs modern pottery

Again, use the existing data structure wherever possible.

---

# 11. Artisan / Potter Section

If the existing database contains artisan/potter information, showcase it.

Example:

**Meet the Makers**

Show:

* Artisan name
* Photo
* Experience
* Specialty
* Short story
* Link to profile

If artisan information doesn't currently exist, create the UI structure without inventing database records.

This section should emphasize that the products are made by real craftspeople.

---

# 12. Product Detail Experience

Review the existing product detail page.

If necessary, improve it so that a visitor can understand more than just the price.

A product detail page should ideally contain:

* Large image gallery
* Product name
* Category
* Price
* Description
* Dimensions
* Material
* Traditional usage
* Cultural significance
* Availability
* Related pottery
* Order/purchase CTA

Only display fields that actually exist.

If some information is unavailable, don't create fake values.

Design the system so those fields can be added later.

---

# 13. Navigation

Create a clear navigation structure.

Suggested navigation:

* Home
* Pottery
* Categories
* Our Tradition
* Pottery Process
* Stories / Blog
* About Jheekuma
* Contact

If authentication/cart/order functionality already exists, integrate appropriate links such as:

* Cart
* Orders
* Account

Do not break existing authentication or shopping functionality.

---

# 14. Visual Design Direction

The visual identity should communicate:

**Traditional Nepalese craftsmanship + modern premium marketplace**

Avoid:

* Generic Bootstrap-looking pages
* Excessive gradients
* Overly bright colors
* Generic stock-photo aesthetic
* Excessive animations
* Corporate SaaS styling
* Overloaded UI

Prefer:

* Warm earthy palette
* Clay/terracotta-inspired visual language
* Off-white/cream backgrounds
* Dark charcoal/brown typography
* Natural photography
* Editorial-style layouts
* Large imagery
* Subtle borders
* Elegant spacing
* Rounded corners only where appropriate
* Subtle hover effects
* Minimal animations

The design should feel like a **premium artisan marketplace and cultural archive**.

---

# 15. Responsive Design

The page must work properly on:

* Desktop
* Laptop
* Tablet
* Mobile

Pay particular attention to:

* Hero image cropping
* Product grids
* Category cards
* Navigation
* Typography
* CTA buttons
* Image aspect ratios
* Touch-friendly controls

Do not simply shrink the desktop layout.

Create intentional responsive layouts.

---

# 16. Database / Backend Rules

This is extremely important.

Do NOT unnecessarily modify the database.

Before creating migrations or changing models:

1. Inspect existing schema.
2. Determine what information already exists.
3. Reuse existing relationships.
4. Reuse existing queries where appropriate.
5. Avoid duplicate models/tables.
6. Avoid hardcoded product/category data.
7. Avoid fake content being presented as real database information.

If a new field/table is genuinely necessary, explain why before implementing it.

Prefer minimal backend changes.

---

# 17. Controller Requirements

Review `HomeController.php`, especially lines 27–30.

Refactor the controller if necessary so the homepage receives the required dynamic data.

The controller should be:

* Clean
* Readable
* Efficient
* Based on existing relationships
* Free from unnecessary duplicate queries

Avoid putting complex business logic directly inside Blade templates.

Use eager loading where appropriate to prevent N+1 queries.

Example categories of data the controller may need to provide:

* featured products
* latest products
* categories
* featured stories/articles
* artisan information
* cultural content

Only retrieve what is actually needed.

---

# 18. Blade Architecture

Keep the implementation maintainable.

If the project already uses reusable Blade components, reuse them.

Otherwise consider creating components such as:

* `hero`
* `product-card`
* `category-card`
* `story-card`
* `section-heading`
* `artisan-card`

Do not create unnecessary abstractions.

Follow the project's existing conventions.

---

# 19. Performance

The landing page should be optimized.

Pay attention to:

* Database query count
* Eager loading
* Image sizes
* Lazy loading
* Responsive images
* Asset loading
* JavaScript bundle size
* CSS duplication

Do not introduce a large frontend framework just for this redesign if the existing project is already Blade-based.

---

# 20. SEO

Make the landing page SEO-friendly.

Include appropriate:

* `<title>`
* Meta description
* Heading hierarchy
* Image alt attributes
* Semantic HTML
* Open Graph metadata if the existing project supports it

SEO direction should target concepts such as:

* Traditional Newar pottery
* Thimi pottery
* Nepalese clay pots
* Traditional clay pots Nepal
* Newar pottery
* Handmade pottery Nepal

Do not keyword-stuff.

---

# 21. Accessibility

Ensure:

* Proper heading hierarchy
* Alt text for meaningful images
* Keyboard-accessible buttons/links
* Good contrast
* Semantic HTML
* Accessible navigation
* Visible focus states

---

# 22. Existing Functionality Must Not Break

Before completing the work, verify that the redesign does not break:

* Existing routes
* Product pages
* Category pages
* Authentication
* Cart/order functionality
* Admin/CMS functionality
* Existing API endpoints
* Existing JavaScript
* Existing forms
* Existing image handling

Do not replace working functionality simply because a new implementation looks cleaner.

---

# 23. Implementation Workflow

Follow this exact workflow:

### Phase 1 — Discovery

Inspect:

* routes
* controller
* views
* models
* migrations
* database relationships
* assets
* existing product/category functionality

### Phase 2 — Design Planning

Create a short implementation plan describing:

* Homepage sections
* Data required for each section
* Controller changes
* Blade components/views
* CSS/JS changes
* Any database changes

### Phase 3 — Backend

Update the controller and required backend logic.

Use existing data structures wherever possible.

### Phase 4 — Frontend

Implement the new homepage.

Build each section as a cohesive visual system rather than isolated components.

### Phase 5 — Responsive Design

Test desktop, tablet and mobile layouts.

### Phase 6 — Functional Verification

Verify:

* Product links
* Category links
* Blog links
* Images
* Existing navigation
* Buttons
* Forms
* Routes

### Phase 7 — Code Quality

Review the implementation for:

* duplicated code
* unnecessary queries
* unused CSS
* unused JS
* hardcoded database values
* broken links
* missing images
* accessibility issues

### Phase 8 — Final Report

After implementation, provide me with:

1. Files changed
2. What was changed in each file
3. Database changes, if any
4. New components created
5. Routes changed/added
6. Important design decisions
7. Potential future improvements
8. Any assumptions you made
9. Any content/images that still need to be supplied

---

# 24. Important Instruction

Do not treat this as simply "redesign the homepage".

Treat this as a **product-level transformation of Jheekuma into a digital showcase and marketplace for traditional Newar pottery from Thimi**.

The homepage should tell a story:

**Discover → Explore → Understand → Connect → Purchase**

A visitor who knows nothing about Newar pottery should be able to land on the homepage and understand:

* What Jheekuma is
* What traditional pottery is being showcased
* Where it comes from
* Why it matters
* What different pottery categories exist
* How pottery is made
* Who makes it
* What cultural traditions surround it
* How they can explore or purchase the pottery

Use the existing project architecture and data as the foundation.

**Do not blindly overwrite existing code. Inspect first, plan second, implement third, and verify everything at the end.**
