@agency-ui-ux-pro
@agency-frontend-engineer
@agency-fullstack-engineer
@agency-qa-engineer

CRITICAL DASHBOARD FIX REQUIRED

Review the attached dashboard screenshots and implement the following changes on BOTH:

1. Admin Dashboard
2. Staff Dashboard

====================================================
PART 1 - SUMMARY CARDS REDESIGN
====================================================

CURRENT ISSUE

The 3 summary cards are broken.

Problems:

- Text is small
- Text is not vertically aligned
- Numbers are too small
- Content appears stuck in the top-left corner
- Cards do not look professional
- Typography hierarchy is missing
- Icons are not properly positioned
- Layout is visually unbalanced

COMPLETELY REDESIGN THE CARD LAYOUT.

====================================================

CARDS

1. Total Projects
2. Due Today
3. Overdue Tasks

====================================================

COLORS

Total Projects
Background: #2563EB
Text: White

Due Today
Background: #EAB308
Text: White

Overdue Tasks
Background: #DC2626
Text: White

====================================================

CARD DESIGN

Desktop Height:
140px

Tablet:
130px

Mobile:
120px

Border Radius:
16px

Padding:
24px

Display:
flex

Align:
center vertically

Use:

display:flex;
align-items:center;
justify-content:space-between;

====================================================

LEFT SIDE

Display:

Icon

Title

Count

====================================================

TITLE

Font Size:
20px

Font Weight:
700

Color:
White

Text Transform:
Uppercase

====================================================

COUNT

Font Size:
36px

Font Weight:
800

Color:
White

====================================================

ICON

Size:
48px

Background:
rgba(255,255,255,0.15)

Width:
56px

Height:
56px

Border Radius:
12px

Icon Color:
White

====================================================

GRID LAYOUT

Desktop:
3 columns

Tablet:
2 columns

Mobile:
1 column

All cards must be equal height.

====================================================

REMOVE

Broken spacing

Extra margins

Nested wrappers

Absolute positioning

Any CSS causing top-left alignment

====================================================

PART 2 - ALL TASKS SECTION
====================================================

CURRENT ISSUE

Dashboard task section still does not match required layout.

====================================================

HEADER

LEFT SIDE

Show:

ALL TASKS

Font Size:
32px

Font Weight:
800

Color:
Current Theme

====================================================

RIGHT SIDE

Move filters to the right.

Order:

All Staff Filter

Task Status Filter

No other filters.

====================================================

REMOVE

Create Task Button

from Dashboard.

Dashboard is for viewing only.

Task creation belongs only to Task Management page.

====================================================

PART 3 - TASK TABLE
====================================================

Keep current functionality.

Do NOT change data.

Do NOT change API.

Only improve layout.

====================================================

TABLE REQUIREMENTS

Show only:

Task Name

Description

Status

Deadline

Priority

Assigned User

Project

Actions

====================================================

ASSIGNED USER

IMPORTANT

Currently role names are displayed.

This is WRONG.

Display:

Real User Name

Examples:

Keval

Darsh

Rahul

etc.

Never show:

AI VIDEO MAKING

AI PRODUCTS

GRAPHICS DESIGN

GOOGLE ADS

or any role name.

Show only assigned user's actual username.

====================================================

USER CELL

Avatar Circle

User Name

Horizontal Alignment

Perfect vertical centering

====================================================

PART 4 - PAGINATION
====================================================

Dashboard Task List

Show maximum:

10 tasks per page

Add pagination below table.

Use project-wide pagination design.

Small compact pagination.

====================================================

PART 5 - RESPONSIVENESS
====================================================

Check:

Desktop

Laptop

Tablet

Mobile

====================================================

VERIFY

No overflow

No overlapping

No horizontal scrolling

No broken cards

No clipped text

No layout shifts

====================================================

DO NOT MODIFY

Business Logic

Database

APIs

Task CRUD

Filters Logic

Permissions

Notifications

Only redesign Dashboard Summary Cards and Dashboard All Tasks UI.

====================================================

FINAL QA

Before completion verify:

✓ Cards visually centered
✓ Large typography
✓ White text
✓ Correct card colors
✓ Professional spacing
✓ All Tasks title on left
✓ Filters on right
✓ Create Task removed
✓ Real usernames shown
✓ 10 records per page
✓ Pagination working
✓ Admin dashboard fixed
✓ Staff dashboard fixed
✓ Fully responsi