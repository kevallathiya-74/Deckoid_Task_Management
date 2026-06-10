PROJECT: DECKOID TASK MANAGEMENT SYSTEM

MISSION:
Perform COMPLETE END-TO-END TESTING of the entire application including Frontend, Backend, APIs, Database, Authentication, Authorization, Notifications, Publishing Module, Task Module, Overdue Module, Daily Reports, and all Admin/Staff workflows.

Use Agency-Agents architecture and execute as a Senior QA Engineer, Senior Backend Engineer, Senior Full-Stack Engineer, Senior Database Engineer, and Senior DevOps Engineer.

========================================================
PHASE 1 — COMPLETE APPLICATION DISCOVERY
========================================

First scan the entire project and automatically discover:

* All pages
* All routes
* All APIs
* All controllers
* All services
* All database tables
* All middleware
* All roles
* All permissions
* All CRUD operations
* All forms
* All modals
* All notifications
* All scheduled jobs

Generate a dependency map showing:

Frontend
↓
API
↓
Controller
↓
Service
↓
Database

for every feature.

========================================================
PHASE 2 — FRONTEND TESTING
==========================

Test every page.

Admin:

* Dashboard
* Projects
* Tasks
* Publishing Report
* Overdue Tasks
* Team Members
* KPI Management
* Leave Requests
* Daily Report Summary
* Notifications
* Account Settings

Staff:

* Dashboard
* Projects
* Tasks
* Publishing Report
* Todo List
* Daily Report
* Notifications
* Leaves
* Account Settings

Verify:

✓ Page loads successfully

✓ No console errors

✓ No JavaScript errors

✓ No blank pages

✓ No broken routes

✓ No missing components

✓ No layout crashes

✓ No disappearing data

✓ No pagination bugs

✓ No filter bugs

✓ No table rendering bugs

✓ No modal issues

✓ No notification issues

========================================================
PHASE 3 — API TESTING
=====================

Automatically discover every API endpoint.

For each endpoint test:

GET
POST
PUT
PATCH
DELETE

Verify:

✓ Correct route

✓ Correct method

✓ Correct authentication

✓ Correct authorization

✓ Valid request payload

✓ Valid response payload

✓ Error handling

✓ Validation handling

✓ Database persistence

✓ Edge cases

Test:

Valid Requests

Invalid Requests

Empty Payloads

Large Payloads

Missing Parameters

Unauthorized Access

Expired Sessions

Role Violations

No API should fail unexpectedly.

========================================================
PHASE 4 — DATABASE TESTING
==========================

Audit schema.sql completely.

Verify:

✓ All tables exist

✓ All relations valid

✓ Foreign keys valid

✓ Indexes valid

✓ Constraints valid

✓ No orphan records

✓ No duplicate records

✓ No broken references

✓ No unused tables

✓ No unused columns

Test every:

INSERT

UPDATE

DELETE

SELECT

Transaction

Rollback

Cascade Rule

========================================================
PHASE 5 — AUTHENTICATION TESTING
================================

Test:

Admin Login

Staff Login

Logout

Session Handling

Remember Me

Password Change

Username Change

Authorization

Verify:

✓ Admin access protected

✓ Staff access protected

✓ Role restrictions enforced

✓ Unauthorized access blocked

✓ Session expiration handled

========================================================
PHASE 6 — TASK MANAGEMENT TESTING
=================================

Test complete workflow:

Admin Creates Task

↓

Assign Staff

↓

Staff Receives Task

↓

Staff Updates Task

↓

Staff Completes Task

↓

Admin Sees Update

Verify:

✓ Real database updates

✓ No sync issues

✓ No missing records

✓ No stale data

✓ No disappearing data

========================================================
PHASE 7 — PUBLISHING REPORT TESTING
===================================

Test:

Admin creates Week 1

Admin adds rows

Admin assigns users

Admin enters task data

Admin changes colors

Admin saves table

Verify:

✓ Database updated

✓ Staff receives correct rows

✓ Assigned users only see assigned rows

✓ Color sync works both directions

✓ Week cloning works

✓ Notifications work

✓ No data loss

✓ No duplicate rows

✓ No disappearing content

========================================================
PHASE 8 — OVERDUE TASK TESTING
==============================

Create test overdue tasks.

Verify:

✓ Automatic overdue detection

✓ Overdue calculations

✓ Sidebar red indicator

✓ Notification generation

✓ Reminder popup

✓ Admin overdue dashboard

✓ Staff overdue section

✓ User filtering works

========================================================
PHASE 9 — DAILY REPORT TESTING
==============================

Test:

Create Report

Update Report

Delete Row

Load Previous Report

Save Report

Verify:

✓ Data saved correctly

✓ Totals calculated correctly

✓ No decimal bugs

✓ No duplicate records

✓ No broken calculations

========================================================
PHASE 10 — CRUD TESTING
=======================

For every entity:

Projects

Tasks

Users

Reports

Publishing Tables

Notifications

Leaves

KPIs

Verify:

CREATE

READ

UPDATE

DELETE

works successfully.

========================================================
PHASE 11 — SECURITY TESTING
===========================

Test:

SQL Injection

XSS

CSRF

Broken Access Control

Privilege Escalation

Session Hijacking

Input Validation

Verify:

✓ Application secure

✓ Sensitive data protected

✓ Role permissions enforced

========================================================
PHASE 12 — PERFORMANCE TESTING
==============================

Check:

✓ Slow Queries

✓ Duplicate Queries

✓ N+1 Problems

✓ Memory Leaks

✓ API Latency

✓ Large Table Handling

✓ Pagination Performance

✓ Notification Performance

========================================================
PHASE 13 — REGRESSION TESTING
=============================

After every fix:

Retest entire application.

Ensure:

✓ No old feature breaks

✓ No side effects

✓ No UI regression

✓ No database regression

✓ No API regression

========================================================
FINAL REPORT FORMAT
===================

For every issue provide:

Severity:
(Critical / Major / Minor)

Module:

File:

Problem:

Root Cause:

Steps To Reproduce:

Impact:

Permanent Fix:

Verification Result:

========================================================
FINAL SUCCESS CRITERIA
======================

Application must be:

✓ Frontend Tested

✓ Backend Tested

✓ API Tested

✓ Database Tested

✓ Authentication Tested

✓ Authorization Tested

✓ CRUD Tested

✓ Notifications Tested

✓ Publishing Tested

✓ Daily Reports Tested

✓ Overdue Tasks Tested

✓ Security Tested

✓ Performance Tested

✓ Regression Tested

Final result:

ZERO Console Errors

ZERO API Errors

ZERO Database Errors

ZERO Broken Routes

ZERO Broken CRUD Operations

ZERO Sync Issues

ZERO Data Loss Issues

ZERO Permission Issues

ZERO Production Blockers

Generate a final Production Readiness Score (0–100%) and do not finish until the application reaches production-ready status.
