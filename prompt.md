# AGENT AGENCY MASTER PROMPT

# TODO LIST SYSTEM ENHANCEMENT (ADMIN + STAFF)

## IMPORTANT RULES

DO NOT change existing Todo List functionality.

DO NOT remove any existing features.

DO NOT redesign UI.

DO NOT modify current styling, colors, layout, or UX flow.

Only extend the existing Todo system with the requirements below.

If database changes are required:

Create a NEW SQL file only.

Do NOT modify existing tables directly.

Provide a separate SQL migration file that can be imported into the existing database.

---

# OBJECTIVE

Implement a Staff Personal Todo System while maintaining full Admin visibility and control.

---

# STAFF SIDE CHANGES

Route:

/staff/todo

## ADD TODO FORM

Allow staff users to create their own todos.

Form Fields:

### Task Field

Label:

Task

Type:

Text Input

Required:

Yes

Validation:

Cannot be empty

Maximum Length:

255 characters

---

### Todo Type

Label:

Todo Type

Dropdown Options:

Normal Task

Pinned Task

Default:

Normal Task

Required:

Yes

---

### Add Button

Label:

Add Todo

On Click:

Validate form

Save todo

Refresh todo list

Show success message

---

# SAVE FUNCTIONALITY

When Staff clicks Add Todo:

Create new todo record.

Save:

todo_text

todo_type

created_by

created_at

updated_at

status

Default Status:

Active

---

# STAFF TODO DISPLAY

Show only:

Todos created by logged-in staff user.

Do NOT show todos from other staff members.

Pinned Tasks:

Display at top.

Normal Tasks:

Display below pinned tasks.

Keep all existing todo actions working.

---

# ADMIN SIDE CHANGES

Route:

/admin/todo

---

# CURRENT ISSUE

Currently Admin sees todos from all staff by default.

This behavior must be removed.

---

# NEW BEHAVIOR

When Admin opens Todo List:

Show ONLY Admin's own todos.

Do NOT automatically display staff todos.

---

# STAFF FILTER

Add Staff Filter Dropdown.

Position:

Top Right

Beside existing filters.

---

Dropdown Label:

Select Staff

Options:

All Staff Members

Example:

Darsh

Keval

Devansh

etc.

---

# FILTER FUNCTIONALITY

If No Staff Selected:

Show Admin's own todos only.

If Staff Selected:

Show only selected staff member's todos.

Do NOT mix records.

---

# TODO DISPLAY

Display:

Task

Todo Type

Created Date

Status

Created By

Actions

---

# PINNED TASK SUPPORT

Pinned Tasks must remain at top.

Sorting Order:

Pinned Tasks First

Normal Tasks Second

Latest First

---

# DATABASE REQUIREMENTS

IF EXISTING TODO TABLE CANNOT SUPPORT THIS FEATURE

Create separate SQL file:

todo_staff_upgrade.sql

Only add missing columns.

Example:

created_by

todo_type

updated_at

Do NOT remove existing columns.

Do NOT modify existing data.

Do NOT drop tables.

Existing todos must remain intact.

---

# BACKEND CHANGES

Update:

Todo Controller

Todo Model

Todo Service

Todo Repository

Todo API

---

# API REQUIREMENTS

Create / Update APIs

GET

/admin/todos

Default:

Admin todos only

Filter:

staff_id

---

GET

/staff/todos

Return:

Logged-in staff todos only

---

POST

/staff/todos/create

Create new todo

---

PUT

/todos/update

Keep existing functionality

---

DELETE

/todos/delete

Keep existing functionality

---

# SECURITY RULES

Staff User:

Can create own todos

Can view own todos

Can edit own todos

Can delete own todos

Cannot access other staff todos

---

Admin:

Can view own todos

Can filter by staff

Can view staff todos

Can manage staff todos

---

# UI REQUIREMENTS

Use existing design system.

Do NOT redesign.

Use current:

Cards

Buttons

Dropdowns

Typography

Spacing

Theme

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

No horizontal scroll

No layout break

No hidden controls

No sidebar overlap

---

# TEST CASES

TEST 1

Staff creates Normal Task.

Expected:

Saved successfully.

Visible in own list.

---

TEST 2

Staff creates Pinned Task.

Expected:

Saved successfully.

Visible at top.

---

TEST 3

Admin opens Todo page.

Expected:

Only Admin todos visible.

---

TEST 4

Admin selects Darsh.

Expected:

Only Darsh todos visible.

---

TEST 5

Admin switches to Keval.

Expected:

Only Keval todos visible.

---

TEST 6

Staff A cannot see Staff B todos.

Expected:

Access denied.

---

TEST 7

Existing todo functionality.

Expected:

No regression.

Everything continues working.

---

# FINAL DELIVERY

Provide:

1. Database SQL file (separate migration)
2. Controller updates
3. Model updates
4. API updates
5. Admin UI updates
6. Staff UI updates
7. Security validation
8. Responsive validation
9. QA report
10. Regression test report

Mark task complete only after all existing todo functionality remains unchanged and all new requirements are fully tested.
