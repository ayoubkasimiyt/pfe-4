# Requirements Document

## Introduction

MailFlow is an email marketing SaaS application built with PHP, vanilla JavaScript, and CSS. The public-facing landing page (the `auth-screen` section in `index.php`) needs a complete redesign into a modern, high-converting SaaS landing page comparable to Mailchimp and ActiveCampaign.

The redesign targets visitors who have not yet signed up. The goal is to communicate MailFlow's value proposition clearly, build trust through social proof, and drive free-trial sign-ups. The existing app shell, sidebar navigation, and all post-login features must remain completely unchanged.

The tech stack is intentionally kept as PHP + vanilla JS + CSS (no framework). The existing Syne/DM Sans fonts, CSS design tokens (dark/light themes, gradient palette, border radius variables), and the `auth-card` login/signup form must be preserved and integrated into the new layout.

---

## Glossary

- **Landing_Page**: The `auth-screen` section rendered by `index.php` that is visible to unauthenticated visitors.
- **Auth_Card**: The existing glassmorphism card containing the login and signup forms (`#authScreen aside.auth-card`).
- **App_Shell**: The post-login application shell (sidebar + main content). Must not be modified.
- **Hero_Section**: The topmost, full-viewport-height section of the Landing_Page containing the headline, subheadline, CTA, and visual.
- **CTA_Button**: A call-to-action button that triggers the sign-up flow by calling `showAuthView('signup')`.
- **Design_Tokens**: CSS custom properties defined in `:root` in `style.css` (e.g., `--accent`, `--gradient-1`, `--radius`, `--bg-primary`).
- **Social_Proof_Section**: A section displaying testimonials, trusted-by logos, and key statistics.
- **Features_Section**: A section highlighting MailFlow's six core capabilities.
- **Pricing_Preview_Section**: A condensed display of the three paid plan tiers.
- **FAQ_Section**: An accordion-based section answering common visitor questions.
- **Final_CTA_Section**: A full-width bottom section with a strong sign-up call to action.
- **Topbar**: The sticky navigation bar at the top of the Landing_Page.
- **Scroll_Animation**: A CSS or JS-driven entrance animation triggered when a section enters the viewport.
- **Glassmorphism**: A UI style using `backdrop-filter: blur()`, semi-transparent backgrounds, and subtle borders.
- **Responsive_Breakpoint**: A CSS media query breakpoint at which the layout adapts (tablet: ≤ 900px, mobile: ≤ 600px).

---

## Requirements

### Requirement 1: Landing Page Structure and Navigation

**User Story:** As a visitor, I want to navigate a clearly structured landing page so that I can quickly understand what MailFlow offers and find the sign-up action.

#### Acceptance Criteria

1. THE Landing_Page SHALL render as a single scrollable page composed of the following sections in order: Topbar, Hero_Section, Social_Proof_Section, Features_Section, Pricing_Preview_Section, FAQ_Section, and Final_CTA_Section.
2. THE Topbar SHALL remain sticky at the top of the viewport while the visitor scrolls.
3. THE Topbar SHALL contain the MailFlow logo (gradient icon + wordmark using Syne font), navigation links for "Features", "Pricing", and "FAQ", and a "Log In" button and a "Start Free Trial" CTA_Button.
4. WHEN a visitor clicks the "Log In" Topbar button, THE Landing_Page SHALL call `showAuthView('login')` and scroll the Auth_Card into view.
5. WHEN a visitor clicks the "Start Free Trial" Topbar CTA_Button, THE Landing_Page SHALL call `showAuthView('signup')` and scroll the Auth_Card into view.
6. WHEN a visitor clicks a Topbar navigation link (e.g., "Features"), THE Landing_Page SHALL transition to the corresponding section anchor via an animated scroll (not an instant jump).
7. IF the visitor has scrolled more than 20px from the top of the page, THE Topbar SHALL apply a partially transparent background (opacity less than 1) with `backdrop-filter: blur()` to create a Glassmorphism effect.
8. THE Landing_Page SHALL use only existing Design_Tokens (CSS custom properties defined in `:root` in `style.css`, such as `--accent`, `--gradient-1`, `--radius`, `--bg-primary`) for all colors, gradients, shadows, and border radii — no hardcoded color, gradient, shadow, or border-radius values shall be introduced.

---

### Requirement 2: Hero Section

**User Story:** As a visitor, I want an impactful hero section so that I immediately understand MailFlow's value and feel compelled to start a free trial.

#### Acceptance Criteria

1. THE Hero_Section SHALL occupy the full viewport height on desktop (min-height: 100vh).
2. THE Hero_Section SHALL contain a kicker badge, a primary headline, a subheadline, a primary CTA_Button labeled "Start for Free", a secondary button labeled "See How It Works", and a social-proof trust note (e.g., "Join 12,000+ marketers — no credit card required").
3. THE Hero_Section headline SHALL use the Syne font at a fluid size between 52px and 80px (using `clamp()`), with a gradient text effect using `--gradient-1`.
4. WHEN a visitor clicks the "Start for Free" CTA_Button in the Hero_Section, THE Landing_Page SHALL call `showAuthView('signup')` and scroll the Auth_Card into view.
5. THE Hero_Section SHALL display the Auth_Card to the right of the hero copy on desktop (grid layout: `1fr 420px`), embedded directly in the section so visitors can sign up without scrolling.
6. THE Hero_Section SHALL display decorative background orbs (radial gradients) and a subtle animated gradient mesh to create visual depth, using `--accent` and `--accent-2` color values.
7. THE Hero_Section SHALL include a row of feature pills below the hero copy listing: "Email Automation", "AI Optimization", "Analytics", "Segmentation", "Deliverability", and "Campaign Management".
8. THE Hero_Section SHALL include a metrics row with three stat cards showing: "12K+ Users", "98% Deliverability", and "3.2× Avg ROI".

---

### Requirement 3: Social Proof Section

**User Story:** As a visitor, I want to see that credible companies and real users trust MailFlow so that I feel confident signing up.

#### Acceptance Criteria

1. THE Social_Proof_Section SHALL display a "Trusted by teams at" label followed by a horizontal row of at least five placeholder company logo placeholders rendered as styled text/SVG badges.
2. THE Social_Proof_Section SHALL display a grid of three customer testimonial cards, each containing a quote, a star rating (5 stars), a avatar placeholder, a customer name, and a customer title.
3. THE Social_Proof_Section SHALL display three key statistics: total emails sent (e.g., "2.4B+"), average open rate improvement (e.g., "+47%"), and customer satisfaction score (e.g., "4.9/5").
4. WHEN the Social_Proof_Section enters the viewport, THE Landing_Page SHALL trigger a Scroll_Animation (fade-up with 0.3s ease) on each testimonial card with a staggered delay of 100ms per card.

---

### Requirement 4: Features Section

**User Story:** As a visitor, I want to see a clear breakdown of MailFlow's key features so that I can evaluate whether the platform meets my needs.

#### Acceptance Criteria

1. THE Features_Section SHALL display a section heading "Everything you need to grow" and a subheading describing the integrated platform.
2. THE Features_Section SHALL display six feature cards, one for each capability: Email Automation, Campaign Management, Analytics & Reporting, Audience Segmentation, Deliverability Optimization, and AI-Powered Optimization.
3. EACH feature card SHALL contain an icon (SVG or emoji), a feature title in Syne font, and a two-sentence description using DM Sans.
4. THE Features_Section SHALL use a responsive grid layout: 3 columns on desktop (≥ 900px), 2 columns on tablet (600px–899px), and 1 column on mobile (< 600px).
5. WHEN a feature card enters the viewport with at least 10% of its area visible, THE Landing_Page SHALL trigger a Scroll_Animation (fade-up, 0.3s ease) with a staggered delay of 80ms per card; the animation SHALL play only once per card and SHALL NOT replay on subsequent scroll events.
6. EACH feature card SHALL use the `--bg-card` background, `--border` border, and `--radius` border radius from Design_Tokens; on hover, the card SHALL display a box-shadow glow using a color from the existing palette assigned in this order: card 1 → `--accent`, card 2 → `--accent-2`, card 3 → `--accent-3`, card 4 → `--accent-4`, card 5 → `--accent`, card 6 → `--accent-2`; the hover transition SHALL use `var(--t)`.

---

### Requirement 5: Pricing Preview Section

**User Story:** As a visitor, I want to see a transparent pricing overview so that I can choose the right plan before signing up.

#### Acceptance Criteria

1. THE Pricing_Preview_Section SHALL display a section heading "Simple, transparent pricing" and a subheading.
2. THE Pricing_Preview_Section SHALL display four plan cards: Free, Pro (marked as "Most Popular"), Business, and Enterprise.
3. EACH plan card SHALL display the plan name, monthly price (or "Custom" for Enterprise), a bulleted list of up to four key plan features, and an action button.
4. THE plan card for "Pro" SHALL be visually distinguished with a gradient border, a "Most Popular" badge, and a slightly elevated scale on hover.
5. WHEN a visitor clicks an action button on any Pricing_Preview plan card, THE Landing_Page SHALL call `showAuthView('signup')` and scroll the Auth_Card into view.
6. THE Pricing_Preview_Section action buttons SHALL display: "Get Started Free" for the Free plan, "Start Pro Trial" for Pro, "Get Business" for Business, and "Contact Sales" for Enterprise.

---

### Requirement 6: FAQ Section

**User Story:** As a visitor, I want answers to common questions so that I can resolve doubts without needing to contact support.

#### Acceptance Criteria

1. THE FAQ_Section SHALL display a section heading "Frequently asked questions" and at least six question-and-answer pairs covering: free trial details, credit card requirements, plan upgrades, sending limits, data security, and cancellation policy.
2. THE FAQ_Section SHALL render each question as an accordion item with a visible question text and a toggle icon (chevron).
3. WHEN a visitor clicks a closed FAQ accordion item, THE Landing_Page SHALL expand the item to reveal the answer text with a CSS transition (max-height ease, 0.3s).
4. WHEN a visitor clicks an open FAQ accordion item, THE Landing_Page SHALL collapse it with the same CSS transition.
5. WHEN the FAQ_Section first loads, THE Landing_Page SHALL render all accordion items in the collapsed state.
6. THE FAQ_Section SHALL allow only one accordion item to be open at a time; opening a new item SHALL automatically close the previously open item.

---

### Requirement 7: Final CTA Section

**User Story:** As a visitor who has scrolled through the entire page, I want a final strong call-to-action so that I am prompted to start my free trial before leaving.

#### Acceptance Criteria

1. THE Final_CTA_Section SHALL display a bold headline (e.g., "Ready to grow your audience?"), a supporting subline, and a primary CTA_Button labeled "Start Your Free Trial".
2. THE Final_CTA_Section SHALL display a full-width background using a gradient derived from `--gradient-1` with a subtle radial glow overlay.
3. WHEN a visitor clicks the "Start Your Free Trial" CTA_Button in the Final_CTA_Section, THE Landing_Page SHALL call `showAuthView('signup')` and scroll the Auth_Card into view.
4. THE Final_CTA_Section SHALL include a trust note stating "No credit card required · Cancel anytime · Free forever plan available".

---

### Requirement 8: Responsive Design

**User Story:** As a visitor on any device, I want the landing page to display correctly and be fully usable so that I have a consistent experience regardless of screen size.

#### Acceptance Criteria

1. THE Landing_Page SHALL be fully responsive across three Responsive_Breakpoints: desktop (≥ 900px), tablet (600px–899px), and mobile (< 600px).
2. WHILE the viewport width is less than 900px, THE Landing_Page SHALL switch the Hero_Section layout from a two-column grid to a single stacked column with the Auth_Card appearing below the hero copy.
3. WHILE the viewport width is less than 900px, THE Topbar navigation links SHALL be hidden and replaced by a hamburger menu icon button.
4. WHEN a visitor taps the hamburger menu icon and the mobile navigation menu is closed, THE Landing_Page SHALL show a full-width dropdown navigation menu. WHEN a visitor taps the hamburger menu icon and the mobile navigation menu is open, THE Landing_Page SHALL hide the dropdown navigation menu.
5. WHEN a visitor taps a link inside the mobile navigation menu, THE Landing_Page SHALL close the menu and begin an animated scroll to the targeted section that completes within 500 milliseconds.
6. WHILE the viewport width is less than 600px, THE Landing_Page SHALL use a minimum tap target size of 44×44px for all interactive elements (buttons, accordion toggles, nav links).
7. THE Landing_Page heading text SHALL not exceed the viewport width at any viewport size and SHALL scale without fixed jumps between breakpoints.
8. IF the viewport is resized to ≥ 900px while the mobile navigation menu is open, THE Landing_Page SHALL automatically close the mobile menu and restore the Topbar navigation links.

---

### Requirement 9: Visual Design and Animations

**User Story:** As a visitor, I want the landing page to feel modern and premium so that I perceive MailFlow as a professional, trustworthy platform.

#### Acceptance Criteria

1. THE Landing_Page SHALL use only the existing Design_Tokens (`--bg-primary`, `--bg-card`, `--accent`, `--gradient-1`, `--gradient-2`, `--gradient-3`, `--radius`, `--shadow`, `--shadow-glow`, etc.) for all styling, ensuring that no color, gradient, shadow, or border-radius value on the Landing_Page is hardcoded outside of `style.css` Design_Token declarations.
2. THE Landing_Page SHALL support both dark and light themes via the existing `[data-theme="light"]` CSS selector; all Landing_Page elements SHALL visually update when the `data-theme` attribute changes without requiring any additional JavaScript.
3. THE Landing_Page SHALL apply Glassmorphism styling (a CSS class that sets `backdrop-filter: blur(20px)`, a semi-transparent `--bg-card` background, and a `--border` border) to the Auth_Card, to the Topbar when `scrollY ≥ 10px`, and to testimonial cards.
4. THE Landing_Page SHALL define a CSS keyframe animation named `landingFadeUp` that transitions from `opacity: 0; transform: translateY(20px)` to `opacity: 1; transform: none`; this animation SHALL be the sole animation used for all Scroll_Animations on the Landing_Page.
5. WHEN any Landing_Page element that has the `data-animate` attribute enters the viewport with at least 10% of its area visible (as observed by `IntersectionObserver`), THE Landing_Page SHALL add the CSS class `is-visible` to that element, which triggers the `landingFadeUp` animation; the animation SHALL play only once per element.
6. THE Landing_Page SHALL use `var(--t)` (`all .25s cubic-bezier(.4,0,.2,1)`) as the transition value on all interactive Landing_Page elements (buttons, cards, nav links), so that hover and focus state changes animate consistently.
7. THE Landing_Page SHALL NOT load or reference any third-party CSS framework or JavaScript library beyond what is already declared in the `<head>` of `index.php`.

---

### Requirement 10: Accessibility and Performance

**User Story:** As a visitor using assistive technology or a slow connection, I want the landing page to be accessible and fast so that I can use it effectively.

#### Acceptance Criteria

1. THE Landing_Page SHALL use semantic HTML5 elements: `<header>`, `<nav>`, `<main>`, `<section>`, `<article>`, `<footer>`, where each element that does not derive its accessible name from visible text content SHALL have an `aria-label` attribute that identifies its purpose or region (e.g., "Main navigation", "Primary content").
2. EACH interactive element on the Landing_Page (buttons, links, accordion items) SHALL have a visible focus ring of at least 2px solid outline that meets WCAG 2.1 AA contrast requirements (minimum 3:1 contrast ratio between focus indicator color and the adjacent background color).
3. EACH image and SVG icon on the Landing_Page SHALL have an `alt` attribute or `aria-label` whose value conveys the subject or function of the element in 150 characters or fewer; purely decorative elements SHALL have `alt=""` or `aria-hidden="true"` and SHALL NOT have an `aria-label`.
4. THE Landing_Page SHALL achieve a color contrast ratio of at least 4.5:1 between all body text (paragraphs, labels, list items, and captions) and its immediate background color in both dark and light themes.
5. THE Landing_Page SHALL NOT introduce any new external HTTP requests (additional fonts, icon libraries, CDN scripts) beyond those already present in `index.php`.
6. THE Landing_Page SHALL use CSS-only animations (no JS-driven animation loops) to avoid CPU overhead on low-end devices; the `IntersectionObserver` callback SHALL only add or remove a CSS class and SHALL NOT read or write layout-triggering CSS properties (such as `width`, `height`, `top`, `left`, `margin`, or `padding`) on any DOM element.
7. ALL accordion expand/collapse interactions SHALL be implemented with `aria-expanded` set to `"true"` or `"false"` on the trigger button, `aria-controls` on the trigger button referencing the `id` of its associated answer panel, and `aria-hidden` set to `"true"` or `"false"` on the answer panel; the trigger button SHALL respond to both Enter and Space key presses to toggle the accordion state.
