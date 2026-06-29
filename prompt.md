# MASTER PROMPT — ADD "SUB ADMIN" ROLE (PRODUCTION READY)

## ROLE

You are a Senior Software Architect, Senior Full Stack Engineer, Database Architect, Security Engineer, QA Engineer, UI/UX Engineer, and Production Deployment Engineer.

Work like an experienced development agency.

Do not partially implement anything.

Analyze the entire project before making changes.

Do not break any existing functionality.

Implement production-ready code only.

---

# PROJECT

Deckoid Task Management System

Current Roles

* Admin
* Staff

New Role

* Admin
* Sub Admin
* Staff

This is a full-stack implementation.

It includes:

* Database
* Backend
* Frontend
* Authentication
* Authorization
* RBAC
* APIs
* Middleware
* Controllers
* Models
* Validation
* Navigation
* Dashboard
* Notifications
* Testing
* Security
* Migration

---

# IMPORTANT REQUIREMENTS

Never duplicate Admin logic.

Sub Admin must inherit Admin permissions.

Implement Role Based Access Control (RBAC).

Replace hardcoded role checks with centralized permission helpers.

Example:

Current

```
if ($user->role == 'admin')
```

Replace with

```
$user->isAdmin()
```

or

```
$user->hasRole(['admin','sub_admin'])
```

Never scatter role logic throughout the project.

---

# DATABASE AUDIT

Audit the complete database.

Detect how roles are stored.

Possible cases

### CASE 1

Role Table exists.

Insert

```
Sub Admin
```

using Seeder or Migration.

---

### CASE 2

users.role is ENUM

Create NEW migration

Example

```
add_sub_admin_to_user_role_enum
```

Do NOT edit existing migration.

---

### CASE 3

users.role is VARCHAR

Update validation only.

---

Never modify existing records.

Never remove old roles.

Migration must be backward compatible.

---

# NEW ROLE

Create

```
sub_admin
```

Do NOT use

```
SubAdmin

subadmin

sub-admin
```

Use exactly

```
sub_admin
```

---

# USER CREATION

Only

Admin

and

Sub Admin

can create users.

User creation page must support

Staff

Sub Admin

If system already supports Admin creation,

keep it.

Otherwise show

Sub Admin

Staff

only.

---

# USER MANAGEMENT

Sub Admin can

Create Staff

Create Sub Admin

Edit Staff

Edit Sub Admin

Delete Staff

Delete Sub Admin

Reset Password

Activate

Deactivate

Assign Project

Assign Task

View Users

Search

Filters

Pagination

Export

Everything.

---

# SECURITY

Sub Admin CANNOT

Delete Admin

Deactivate Admin

Reset Admin password

Edit Admin

Change Admin role

Modify protected Admin account

Return proper HTTP status.

403 Forbidden

---

# AUTHENTICATION

Login

Logout

Forgot Password

Remember Me

Session

Token

Everything must support

Sub Admin.

---

# AUTHORIZATION

Audit every

Controller

Middleware

Policy

Helper

Gate

Permission

Route

Validation

API

Replace

```
admin
```

checks with

```
admin

OR

sub_admin
```

using centralized RBAC helper.

Never duplicate code.

---

# SIDEBAR

Sub Admin sidebar must match Admin exactly.

Workspace

Dashboard

Projects

Tasks

Publishing Report

Todo List

Overdue Tasks

Notifications

Everything.

Same order.

Same icons.

Same routes.

No hidden menus.

No disabled menus.

---

# DASHBOARD

Sub Admin dashboard must be identical to Admin.

Cards

Statistics

Charts

Graphs

Recent Tasks

Projects

Todo

Publishing

Notifications

Overdue

Widgets

Counts

Same UI.

Same APIs.

---

# PROJECT MODULE

Full CRUD

Create

Edit

Delete

Archive

Restore

Assign Members

Timeline

Status

Priority

Reports

Everything.

---

# TASK MODULE

Full CRUD

Create

Edit

Delete

Assign

Deadline

Priority

Comments

Attachments

Activity

History

Filters

Reports

Everything.

---

# TODO MODULE

Full CRUD.

Create Todo

Assign Todo

Pinned

Deadline

Reminder

Complete

Delete

Edit

Remarks

Everything.

---

# PUBLISHING REPORT

Full CRUD

Week

Month

Year

Posts

Reels

Ads

Approvals

Assignments

Everything.

---

# OVERDUE TASKS

Full CRUD.

Project Tasks

Todo Tasks

Filters

Staff Filter

Task Type Filter

Reports

Everything.

---

# NOTIFICATIONS

Sub Admin receives

Task

Todo

Publishing

Deadline

Reminder

Overdue

Project

Leave

System

Notifications.

Same as Admin.

---

# APIs

Audit every API.

Where Admin access exists,

Sub Admin must also work.

Update

Routes

Controllers

Validation

Authorization

Middleware

Responses

Swagger (if available)

Postman Collection (if available)

Everything.

---

# ROUTES

Audit every route.

Admin routes

Sub Admin routes

Shared routes

Protected routes

Permission middleware

404 handling

Unauthorized handling

Everything.

---

# FRONTEND

Update

Sidebar

Role Dropdown

Profile Badge

Filters

Tables

Forms

Cards

Badges

Search

Pagination

Modals

Everything.

Display role badge

Admin

Sub Admin

Staff

correctly.

---

# SEARCH

All search must support

Sub Admin.

---

# FILTERS

All filters must include

Sub Admin.

---

# EXPORT

CSV

Excel

PDF

must include

Sub Admin.

---

# LOGGING

Audit logs must store

Created By

Updated By

Deleted By

Role

Admin

Sub Admin

Staff

properly.

---

# AUDIT LOG

Track

Create

Edit

Delete

Reset Password

Login

Logout

Role Changes

Assignments

Everything.

---

# VALIDATION

Prevent

Sub Admin deleting Admin

Sub Admin editing Admin

Sub Admin changing Admin role

Sub Admin resetting Admin password

Sub Admin deactivating Admin

Return proper validation errors.

---

# DATABASE

Never modify existing schema directly.

If schema changes required

Create NEW migration.

Example

```
2026_xx_xx_add_sub_admin_role.php
```

or

```
insert_sub_admin_role.php
```

Do not edit old migrations.

---

# SEEDERS

Create Seeder if needed.

Insert

Sub Admin

without affecting existing data.

---

# SECURITY REVIEW

Review

SQL Injection

XSS

CSRF

Session

Role Escalation

Privilege Escalation

Mass Assignment

Hidden APIs

Broken Access Control

Everything.

---

# PERFORMANCE

No duplicate queries.

No duplicate role lookups.

Cache permissions where appropriate.

Avoid N+1 queries.

Optimize middleware.

---

# CODE QUALITY

Remove duplicated Admin permission code.

Create reusable helpers.

Reusable middleware.

Reusable policy.

Reusable service.

Follow SOLID principles.

---

# TESTING

Verify

✓ Admin Login

✓ Sub Admin Login

✓ Staff Login

✓ Admin creates Sub Admin

✓ Sub Admin creates Staff

✓ Sub Admin creates Sub Admin

✓ Admin edits Sub Admin

✓ Sub Admin edits Staff

✓ Sub Admin cannot edit Admin

✓ Sub Admin cannot delete Admin

✓ Admin dashboard works

✓ Sub Admin dashboard works

✓ Staff dashboard unchanged

✓ Sidebar identical

✓ Notifications work

✓ Projects work

✓ Tasks work

✓ Todo works

✓ Publishing works

✓ Overdue works

✓ APIs work

✓ Authentication works

✓ Authorization works

✓ Migration works

✓ Existing data preserved

✓ Zero regression

---

# FINAL DELIVERABLES

Generate complete production-ready implementation including:

1. Database Migration
2. Seeder
3. Model Updates
4. Permission Helper
5. Middleware Updates
6. Controller Updates
7. Policy Updates
8. Route Updates
9. API Updates
10. Validation Updates
11. Authentication Updates
12. Authorization Updates
13. Sidebar Updates
14. User Management Updates
15. Dashboard Updates
16. Project Module Updates
17. Task Module Updates
18. Todo Module Updates
19. Publishing Module Updates
22. Notification Updates
23. Security Updates
24. RBAC Refactoring
25. UI Updates
26. Testing Report
27. Migration Instructions
28. Deployment Checklist
29. Regression Test Report
30. Production Verification Report

---

# SUCCESS CRITERIA

The implementation is complete only when:

* Admin functionality remains unchanged.
* Staff functionality remains unchanged.
* Sub Admin has the same operational capabilities as Admin except for protected Admin account management.
* No duplicate permission logic exists.
* All modules support Sub Admin.
* Database migrations are backward compatible.
* Existing data remains intact.
* No UI regressions occur.
* No API regressions occur.
* No authorization bypasses exist.
* The project passes full production testing with zero critical issues.



note : if database change then u create separate sql file 
