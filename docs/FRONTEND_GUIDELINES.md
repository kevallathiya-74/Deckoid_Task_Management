Here’s the complete `FRONTEND_GUIDELINES.md` for your modern SaaS-style Task Management System.

# FRONTEND_GUIDELINES.md — Task Management System

# 1. Design Philosophy

## Product Style

Modern + Minimal + Professional SaaS Dashboard

## Visual Direction

The UI should feel:

* Fast
* Premium
* Clean
* Focused
* Data-driven
* Agency-oriented

Avoid:

* Outdated gradients
* Heavy skeuomorphic effects
* Overcrowded dashboards
* Excessive animations
* Low contrast text

---

# 2. Core Design Principles

# Principle 1 — Clarity First

Every screen must prioritize:

* Readability
* Clear hierarchy
* Minimal cognitive load

Rules:

* One primary action per section
* Use whitespace aggressively
* Keep forms visually grouped

---

# Principle 2 — Speed Perception

The app must feel fast even during loading.

Requirements:

* Skeleton loaders
* Instant UI feedback
* Optimistic interactions where possible
* Smooth transitions under 300ms

---

# Principle 3 — Consistency Everywhere

All:

* Buttons
* Inputs
* Cards
* Tables
* Alerts

must use the same design token system.

No random styling allowed.

---

# Principle 4 — Responsive by Default

Every component must support:

* Mobile
* Tablet
* Desktop
* Large screens

No desktop-only layouts.

---

# Principle 5 — Accessibility Matters

Must support:

* Keyboard navigation
* Screen readers
* Focus visibility
* Proper contrast ratios

WCAG 2.1 AA compliance mandatory.

---

# 3. Color System

# Primary Color Scale

| Scale | Hex     |
| ----- | ------- |
| 50    | #EEF4FF |
| 100   | #D9E7FF |
| 200   | #B9D3FF |
| 300   | #8DB8FF |
| 400   | #5C95FF |
| 500   | #356DFF |
| 600   | #1D4FFF |
| 700   | #173EE0 |
| 800   | #1835B5 |
| 900   | #1A318D |

---

# Neutral Color Scale

| Scale | Hex     |
| ----- | ------- |
| 50    | #F8FAFC |
| 100   | #F1F5F9 |
| 200   | #E2E8F0 |
| 300   | #CBD5E1 |
| 400   | #94A3B8 |
| 500   | #64748B |
| 600   | #475569 |
| 700   | #334155 |
| 800   | #1E293B |
| 900   | #0F172A |

---

# Semantic Colors

| Type    | Hex     |
| ------- | ------- |
| Success | #16A34A |
| Warning | #F59E0B |
| Error   | #DC2626 |
| Info    | #0284C7 |

---

# Color Usage Rules

| Color Category | Usage                                 |
| -------------- | ------------------------------------- |
| Primary        | Primary actions, links, active states |
| Neutral        | Backgrounds, borders, text            |
| Success        | Completed tasks                       |
| Warning        | Pending/review states                 |
| Error          | Validation and destructive actions    |
| Info           | Notifications and system updates      |

---

# 4. Typography System

# Font Families

| Usage        | Font       |
| ------------ | ---------- |
| Primary UI   | Poppins    |
| Secondary UI | Inter      |
| Fallback     | sans-serif |
| Data Tables  | Poppins    |
| Numbers      | Poppins    |

Google Font:

```html
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
```

---

# Typography Scale

| Type | Size     | Weight | Line Height |
| ---- | -------- | ------ | ----------- |
| xs   | 0.75rem  | 400    | 1rem        |
| sm   | 0.875rem | 400    | 1.25rem     |
| base | 1rem     | 400    | 1.5rem      |
| lg   | 1.125rem | 500    | 1.75rem     |
| xl   | 1.25rem  | 600    | 1.75rem     |
| 2xl  | 1.5rem   | 700    | 2rem        |
| 3xl  | 1.875rem | 700    | 2.25rem     |
| 4xl  | 2.25rem  | 800    | 2.5rem      |

---

# Text Usage Rules

| Element        | Style        |
| -------------- | ------------ |
| Page Titles    | 3xl bold     |
| Section Titles | xl semibold  |
| Body Text      | base regular |
| Labels         | sm medium    |
| Table Content  | sm regular   |

---

# 5. Spacing System

# Spacing Scale

| Token | Value   |
| ----- | ------- |
| 0     | 0rem    |
| 1     | 0.25rem |
| 2     | 0.5rem  |
| 3     | 0.75rem |
| 4     | 1rem    |
| 5     | 1.25rem |
| 6     | 1.5rem  |
| 8     | 2rem    |
| 10    | 2.5rem  |
| 12    | 3rem    |
| 14    | 3.5rem  |
| 16    | 4rem    |

---

# Usage Rules

| Area               | Recommended Spacing |
| ------------------ | ------------------- |
| Card Padding       | p-6                 |
| Section Gap        | gap-6               |
| Form Field Gap     | gap-4               |
| Dashboard Grid Gap | gap-6               |

---

# 6. Border Radius System

| Token | Value    |
| ----- | -------- |
| none  | 0        |
| sm    | 0.125rem |
| md    | 0.375rem |
| lg    | 0.5rem   |
| xl    | 0.75rem  |
| 2xl   | 1rem     |
| full  | 9999px   |

---

# 7. Component Library

# BUTTON COMPONENT

# Primary Button

## Tailwind Classes

```html
<button class="inline-flex items-center justify-center rounded-xl bg-[#356DFF] px-4 py-2 text-sm font-semibold text-white shadow-sm transition-all duration-200 hover:bg-[#1D4FFF] focus:outline-none focus:ring-2 focus:ring-[#356DFF] focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50">
  Save Changes
</button>
```

---

# Secondary Button

```html
<button class="inline-flex items-center justify-center rounded-xl border border-[#CBD5E1] bg-white px-4 py-2 text-sm font-medium text-[#1E293B] transition-all duration-200 hover:bg-[#F8FAFC] focus:outline-none focus:ring-2 focus:ring-[#356DFF] focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50">
  Cancel
</button>
```

---

# Danger Button

```html
<button class="inline-flex items-center justify-center rounded-xl bg-[#DC2626] px-4 py-2 text-sm font-semibold text-white transition-all duration-200 hover:bg-[#B91C1C] focus:outline-none focus:ring-2 focus:ring-[#DC2626] focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50">
  Delete
</button>
```

---

# Button Sizes

| Size | Classes             |
| ---- | ------------------- |
| sm   | px-3 py-1.5 text-xs |
| md   | px-4 py-2 text-sm   |
| lg   | px-6 py-3 text-base |

---

# Button Accessibility

Requirements:

* Minimum touch target 44x44px
* Visible focus ring mandatory
* Use aria-disabled when disabled

---

# INPUT COMPONENT

# Default Input

```html
<input type="text" class="w-full rounded-xl border border-[#CBD5E1] bg-white px-4 py-3 text-sm text-[#0F172A] placeholder:text-[#94A3B8] shadow-sm transition-all duration-200 focus:border-[#356DFF] focus:outline-none focus:ring-4 focus:ring-[#D9E7FF] disabled:cursor-not-allowed disabled:bg-[#F1F5F9]" placeholder="Enter project name">
```

---

# Error Input

```html
<input type="text" class="w-full rounded-xl border border-[#DC2626] bg-white px-4 py-3 text-sm text-[#0F172A] placeholder:text-[#94A3B8] focus:border-[#DC2626] focus:outline-none focus:ring-4 focus:ring-[#FEE2E2]">
```

---

# Input States

| State    | Behavior              |
| -------- | --------------------- |
| Default  | Neutral border        |
| Focus    | Blue ring             |
| Error    | Red border            |
| Disabled | Gray background       |
| Loading  | Spinner icon optional |

---

# CARD COMPONENT

```html
<div class="rounded-2xl border border-[#E2E8F0] bg-white p-6 shadow-sm transition-all duration-200 hover:shadow-md">
  <h3 class="text-lg font-semibold text-[#0F172A]">Card Title</h3>
  <p class="mt-2 text-sm text-[#64748B]">Card content goes here.</p>
</div>
```

---

# MODAL COMPONENT

```html
<div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
  <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-2xl">
    <div class="flex items-center justify-between">
      <h2 class="text-xl font-semibold text-[#0F172A]">Modal Title</h2>
      <button class="text-[#64748B] hover:text-[#0F172A]">✕</button>
    </div>

    <div class="mt-6">
      Modal content
    </div>
  </div>
</div>
```

---

# Modal Accessibility

Requirements:

* ESC key closes modal
* Focus trap required
* aria-modal="true"
* role="dialog"

---

# ALERT / TOAST COMPONENT

# Success Toast

```html
<div class="flex items-start gap-3 rounded-xl border border-[#BBF7D0] bg-[#F0FDF4] p-4 text-[#166534] shadow-sm">
  <span>✓</span>
  <p class="text-sm font-medium">Task updated successfully.</p>
</div>
```

---

# Error Toast

```html
<div class="flex items-start gap-3 rounded-xl border border-[#FECACA] bg-[#FEF2F2] p-4 text-[#991B1B] shadow-sm">
  <span>✕</span>
  <p class="text-sm font-medium">Something went wrong.</p>
</div>
```

---

# LOADING STATES

# Skeleton Card

```html
<div class="animate-pulse rounded-2xl border border-[#E2E8F0] bg-white p-6">
  <div class="h-4 w-1/3 rounded bg-[#E2E8F0]"></div>
  <div class="mt-4 h-3 w-full rounded bg-[#E2E8F0]"></div>
  <div class="mt-2 h-3 w-2/3 rounded bg-[#E2E8F0]"></div>
</div>
```

---

# EMPTY STATES

```html
<div class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-[#CBD5E1] bg-white p-12 text-center">
  <div class="text-5xl">📂</div>
  <h3 class="mt-4 text-lg font-semibold text-[#0F172A]">No Projects Found</h3>
  <p class="mt-2 text-sm text-[#64748B]">Create your first project to start managing tasks.</p>
</div>
```

---

# 8. Layout System

# Grid System

| Property  | Value  |
| --------- | ------ |
| Max Width | 1440px |
| Columns   | 12     |
| Gutter    | 24px   |

---

# Responsive Breakpoints

| Breakpoint | Width  |
| ---------- | ------ |
| sm         | 640px  |
| md         | 768px  |
| lg         | 1024px |
| xl         | 1280px |
| 2xl        | 1536px |

---

# Centered Layout

```html
<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
  Content
</div>
```

---

# Two Column Layout

```html
<div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
  <div>Left Column</div>
  <div>Right Column</div>
</div>
```

---

# Sidebar Layout

```html
<div class="flex min-h-screen bg-[#F8FAFC]">
  <aside class="hidden w-72 border-r border-[#E2E8F0] bg-white lg:block">
    Sidebar
  </aside>

  <main class="flex-1 p-6">
    Main Content
  </main>
</div>
```

---

# 9. Accessibility Standards

# WCAG 2.1 AA Rules

Requirements:

* Contrast ratio minimum 4.5:1
* Keyboard accessible forms
* Focus indicators mandatory
* Proper label/input association
* Semantic HTML only

---

# Keyboard Navigation

| Key   | Action      |
| ----- | ----------- |
| TAB   | Navigate    |
| ENTER | Submit      |
| ESC   | Close modal |

---

# Focus Indicator Rules

All interactive elements must include:

```html
focus:outline-none focus:ring-2 focus:ring-[#356DFF] focus:ring-offset-2
```

---

# 10. Animation System

# Transition Duration

| Usage   | Duration |
| ------- | -------- |
| Hover   | 150ms    |
| Modal   | 250ms    |
| Sidebar | 300ms    |
| Toast   | 200ms    |

---

# Easing Curves

| Type     | Value       |
| -------- | ----------- |
| Standard | ease-in-out |
| Entrance | ease-out    |
| Exit     | ease-in     |

---

# Allowed Animations

Use animations only for:

* Modals
* Dropdowns
* Sidebar transitions
* Toast notifications
* Hover interactions

Avoid:

* Continuous animations
* Distracting motion
* Large bouncing effects

---

# Reduced Motion Support

Must support:

```css
@media (prefers-reduced-motion: reduce) {
  * {
    animation: none !important;
    transition: none !important;
  }
}
```

---

# 11. Dashboard Design Rules

# Cards

Requirements:

* Rounded 2xl
* Soft shadow
* White background
* Clear spacing

---

# Tables

Requirements:

* Sticky headers
* Hover rows
* Search/filter support
* Responsive scroll

---

# Forms

Requirements:

* Max width 640px
* Clear labels
* Inline validation
* Required field indicators

---

# Charts

Requirements:

* Minimal gridlines
* Clear legends
* Responsive scaling
* Accessible colors

---

# 12. Mobile UX Rules

Requirements:

* Sidebar becomes drawer
* Buttons minimum 44px height
* Tables become cards
* Sticky bottom actions optional

Avoid:

* Tiny clickable elements
* Horizontal overflow
* Dense layouts
