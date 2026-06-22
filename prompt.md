# AGENT AGENCY MASTER PROMPT

# OVERDUE TASKS + OVERDUE TODO TASKS FILTER ENHANCEMENT

## OBJECTIVE

Enhance the existing Overdue Tasks Management module to support both:

1. Overdue Project Tasks
2. Overdue Todo Tasks

without changing any existing functionality.

The current overdue page only displays overdue project tasks.

I want overdue todo items to be included as well and provide filtering between both task types.

---

# AFFECTED PAGES

Admin:

/admin/overdue

Staff:

/staff/overdue

Apply the same logic on both sides.

---

# CURRENT BEHAVIOR

Current page displays:

* Task Title
* Assignee
* Due Date
* Days Overdue
* Project

Only project tasks appear.

---

# REQUIRED BEHAVIOR

The Overdue Page must support:

## Overdue Project Tasks

Existing functionality.

Keep exactly as it is.

---

## Overdue Todo Tasks

If:

Todo Deadline < Current DateTime

AND

Todo Status != Completed

Then:

Automatically move into Overdue System.

Display on Overdue page.

---

# NEW FILTER SECTION

Current Filter:

All Staff Members

Keep existing filter.

---

# ADD NEW FILTER

Position:

Beside

All Staff Members

Top Right

Same Row

Same UI Style

Same Height

Same Width Pattern

---

# TASK TYPE FILTER

Label:

Task Type

Options:

All

Project Tasks

Todo Tasks

Default:

All

---

# FILTER BEHAVIOR

## ALL

Show:

Project Tasks

*

Todo Tasks

---

## PROJECT TASKS

Show:

Only project tasks

Hide todo tasks

---

## TODO TASKS

Show:

Only overdue todo items

Hide project tasks

---

# TABLE CHANGES

Add new column:

Task Type

Position:

After Task Title

---

# VALUES

For project task:

Project Task

Badge Color:

Blue

---

For todo task:

Todo Task

Badge Color:

Purple

---

# OVERDUE TODO DISPLAY

For overdue todo tasks show:

Task Title

Task Type

Assigned User

Due Date

Days Overdue

Created By

Status

---

# OVERDUE CALCULATION

Formula:

Current DateTime - Due DateTime

Example:

Deadline:

16 Jun 2026

Current:

22 Jun 2026

Display:

6 Days

---

# ADMIN SIDE

Admin should be able to:

View all overdue project tasks

View all overdue todo tasks

Filter by:

Staff

Task Type

---

# STAFF SIDE

Staff should only see:

Their own overdue project tasks

Their own overdue todo tasks

---

# DASHBOARD COUNTS

Update existing overdue statistics.

Current:

Total Overdue Tasks

Affected Staff

---

# TOTAL OVERDUE TASKS

Must include:

Overdue Project Tasks

*

Overdue Todo Tasks

---

# AFFECTED STAFF

Count users having:

At least one overdue project task

OR

At least one overdue todo task

---

# DATABASE

Do NOT modify existing overdue tables.

Use existing:

tasks table

todos table

deadline fields

status fields

---

If additional optimization is needed:

Create separate migration file only.

Do not modify existing schema directly.

---

# NOTIFICATIONS

Use existing reminder system.

When Todo becomes overdue:

Create overdue notification.

Show in:

Notification Bell

Dashboard Notifications

Overdue Page

---

# RESPONSIVE REQUIREMENTS

Verify:

1920px

1600px

1440px

1366px

1280px

1024px

768px

480px

375px

320px

Requirements:

No overflow

No horizontal scrolling

No broken table

No sidebar overlap

No hidden filters

No pagination issues

---

# UI REQUIREMENTS

Maintain current design.

Do NOT redesign.

Match existing:

Cards

Filters

Table

Badges

Typography

Colors

Spacing

---

# QA TESTING

TEST 1

Overdue Project Task.

Expected:

Visible.

---

TEST 2

Overdue Todo Task.

Expected:

Visible.

---

TEST 3

Filter:

Project Tasks.

Expected:

Only project tasks shown.

---

TEST 4

Filter:

Todo Tasks.

Expected:

Only overdue todos shown.

---

TEST 5

Filter:

All.

Expected:

Both shown.

---

TEST 6

Staff View.

Expected:

Only own overdue items.

---

TEST 7

Admin View.

Expected:

All overdue items.

---

# FINAL DELIVERY

Provide:

1. Controller Changes
2. Model Changes
3. Query Changes
4. Filter Implementation
5. Notification Integration
6. Responsive Verification
7. QA Testing Report
8. Regression Testing Report

IMPORTANT:

Do NOT remove existing overdue task functionality.

Do NOT change existing UI.

Only extend the module so overdue Todo Tasks and Project Tasks can be filtered separately using a new Task Type filter beside the existing Staff Filter.
