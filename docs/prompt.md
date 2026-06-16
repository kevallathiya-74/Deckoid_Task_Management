# AGENT AGENCY + UI UX PRO MAX

# NEW FEATURE IMPLEMENTATION: SOP MANAGEMENT SYSTEM

## OBJECTIVE

Create a complete SOP (Standard Operating Procedure) Management System inside the Deckoid Task Management project.

This feature must work for both:

* Admin Panel
* Staff Panel

Do NOT redesign the existing UI.

Maintain the current theme, colors, typography, spacing system, and sidebar style.

Only add the SOP functionality.

---

# SIDEBAR CHANGES

## Admin Sidebar

Create a new sidebar menu item:

SOP

Icon:
File Text / Document Icon

Position:

Management Section

Place SOP below:

* Overdue Tasks
* Team Members

or

Any suitable management section location.

Highlight the active SOP menu item exactly like existing sidebar items.

---

# ADMIN SOP PAGE

Route:

/admin/sop

Page Title:

SOP Management

---

# PAGE LAYOUT

Create 3 sections.

## SECTION 1

### Staff Filter

Top of page.

Label:

Select Staff Member

Dropdown contains:

All active staff members.

Searchable dropdown.

Display:

* Staff Name
* Department
* Role

Example:

Keval Lathiya
AI Video Making

Darsh
AI Products

---

# SECTION 2

### SOP Description

Large textarea.

Label:

SOP Details

Placeholder:

Enter SOP instructions, process documentation, workflow steps, responsibilities, or operational guidelines...

Requirements:

* Multi-line textarea
* Rich content support ready
* Minimum height 250px
* Auto expand

---

# SECTION 3

### Assign Member

Dropdown field.

Label:

Assign To

Show all active staff.

Searchable.

Single selection.

Future-ready for multi-assignment.

---

# ACTION BUTTONS

Add:

Save SOP

Cancel

Reset

---

# SAVE FUNCTIONALITY

When Admin clicks Save:

Create SOP record.

Save:

* SOP Title (optional if added later)
* Description
* Assigned Staff ID
* Created By Admin ID
* Created Date
* Updated Date
* Status

Status default:

Active

---

# DATABASE CHANGES

Create table:

sops

Columns:

id

staff_id

created_by

description

status

created_at

updated_at

Indexes:

staff_id

created_by

status

Foreign Keys:

staff_id -> users.id

created_by -> users.id

---

# STAFF PANEL

Create new sidebar menu:

SOP

Route:

/staff/sop

Title:

My SOPs

---

# STAFF VIEW

Staff should NOT edit SOP.

Staff should only view SOP.

Display:

Assigned SOPs

Show:

Description

Assigned Date

Last Updated

---

# STAFF PAGE DESIGN

Card Layout

Example:

SOP #1

Description:
[Full SOP Content]

Created:
16-06-2026

---

# FILTERING

Admin Page

Allow filtering by:

* Staff Member

---

# SEARCH

Add:

Search SOP

Search by:

* Description
* Staff Name

---

# FUTURE READY STRUCTURE

Keep architecture ready for:

* Multiple SOP assignments
* SOP categories
* SOP attachments
* SOP acknowledgement
* SOP completion tracking

Do not implement now.

Only prepare scalable structure.

---

# NOTIFICATIONS

When SOP assigned:

Create notification.

Staff receives:

New SOP Assigned

Notification visible in:

Notification Bell

Staff Dashboard

---

# PERMISSIONS

Admin:

Create SOP

Update SOP

Delete SOP

Assign SOP

View All SOPs

Staff:

View Assigned SOPs Only

Cannot:

Edit SOP

Delete SOP

Assign SOP

---

# UI REQUIREMENTS

Use existing project design system.

Do NOT redesign.

Match:

* Existing cards
* Existing forms
* Existing dropdowns
* Existing buttons

Maintain consistency.

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

No layout breakage

No sidebar overlap

No hidden content

---

# QA TESTING

Test 1

Admin creates SOP.

Expected:

Saved successfully.

---

Test 2

Admin assigns SOP to Staff.

Expected:

Staff receives SOP.

---

Test 3

Staff opens SOP page.

Expected:

Assigned SOP visible.

---

Test 4

Staff cannot edit SOP.

Expected:

Read-only.

---

Test 5

Admin filters SOP by staff.

Expected:

Correct results.

---

# FINAL DELIVERABLE

Provide:

1. Database changes
2. Controller changes
3. Model changes
4. API endpoints
5. Admin UI implementation
6. Staff UI implementation
7. Notification integration
8. Permission implementation
9. Responsive verification report
10. QA testing report

Do not mark complete until SOP assignment, storage, retrieval, notification, and staff visibility are fully tested and working.
