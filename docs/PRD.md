# PRD.md — Task Management System

# 1. Product Overview

## Product Name

Task Management System

## Product Type

Multi-user SaaS-style internal task and project management platform for digital agencies.

## Vision

Build a centralized workflow system where admins can manage projects, assign role-based tasks, monitor staff productivity, and streamline digital agency operations using a secure and scalable PHP + MySQL application.

---

# 2. Problem Statement

## Current Problem

Small and medium digital agencies commonly manage projects using:

* WhatsApp
* Excel sheets
* Google Sheets
* Trello-like fragmented tools
* Manual follow-ups

This creates major operational problems:

* Tasks get lost in chats
* Staff do not know priorities
* Project deadlines are missed
* No centralized task tracking
* No productivity visibility
* No role-based organization
* Difficult to scale teams
* Poor accountability

Most existing tools are either:

* Too expensive
* Too complex
* Require subscriptions
* Not customized for agency workflows

---

## Primary User Pain Points

### Admin Pain Points

* Cannot track staff productivity easily
* Difficult to manage multiple client projects
* Manual task assignment consumes time
* No centralized dashboard
* Hard to identify delayed tasks
* No department-wise organization

### Staff Pain Points

* Unclear task priorities
* Task confusion across multiple projects
* No centralized task visibility
* No proper update/reporting system
* Communication gaps with admin

---

# 3. Target Users

## Primary Target Users

* Digital marketing agencies
* Web development agencies
* Creative studios
* SEO agencies
* Video editing teams
* Small-to-mid sized remote teams

---

# 4. Unique Value Proposition

## Why This Solution?

Unlike generic task managers, this platform is:

* Built specifically for agency workflows
* Role/department oriented
* Lightweight and fast
* Self-hosted on XAMPP
* One-time deployment cost
* Fully customizable
* Designed for non-technical business owners

---

# 5. Goals & Objectives

## SMART Goals

### Goal 1

Reduce project coordination time by 60% within 3 months of deployment.

### Goal 2

Allow admins to assign tasks in under 2 minutes per project.

### Goal 3

Achieve 95% successful task tracking visibility across all active projects.

### Goal 4

Reduce missed deadlines by 40% through centralized task monitoring.

### Goal 5

Support minimum 100 concurrent staff users without performance degradation.

---

# 6. Success Metrics

| Metric                                  | Target                  |
| --------------------------------------- | ----------------------- |
| Average task assignment time            | < 2 minutes             |
| Dashboard load time                     | < 3 seconds             |
| Staff task completion tracking accuracy | 95%                     |
| Missed task deadlines reduction         | 40%                     |
| User session stability                  | 99% successful sessions |
| Search/filter response time             | < 1 second              |
| Concurrent user support                 | 100+ users              |

---

# 7. Target Personas

# Persona 1 — Agency Owner

## Demographics

* Age: 28-40
* Runs digital agency
* Manages 5-30 staff members

## Goals

* Manage projects efficiently
* Track staff productivity
* Reduce operational chaos
* Improve delivery timelines

## Pain Points

* Team communication gaps
* No task accountability
* Delayed projects
* Hard to scale operations

## Tech Proficiency

Intermediate

---

# Persona 2 — Staff Member

## Demographics

* Age: 20-35
* Remote or office-based employee

## Goals

* View assigned work clearly
* Update progress quickly
* Meet deadlines efficiently

## Pain Points

* Confusing instructions
* Task overload
* Missing deadlines accidentally

## Tech Proficiency

Basic to Intermediate

---

# 8. Features

# P0 — MVP Must-Have Features

---

## Feature: Authentication System

### Description

Secure login system for Admin and Staff users.

### User Story

As a staff member, I want to securely login so I can access my assigned tasks.

### Acceptance Criteria

* User can login using username/password
* Invalid credentials show proper error
* Passwords stored hashed
* Session expires after inactivity
* Logout destroys session securely

### Success Metric

99% successful authenticated login sessions.

---

## Feature: Dashboard

### Description

Central dashboard for analytics and quick overview.

### User Story

As an admin, I want to view all project and task statistics so I can monitor operations.

### Acceptance Criteria

* Show active projects count
* Show completed projects count
* Show total staff
* Show pending tasks
* Show completed tasks
* Dashboard loads within 3 seconds

### Success Metric

Dashboard usage by admin daily.

---

## Feature: Project Management

### Description

Admin can create and manage projects.

### User Story

As an admin, I want to create projects and assign department roles.

### Acceptance Criteria

* Admin can add/edit/delete projects
* Role dropdown available
* Validation on required fields
* Deadline must be future date
* Project status selectable

### Success Metric

Project creation under 2 minutes.

---

## Feature: Task Assignment

### Description

Tasks can be assigned per role and project.

### User Story

As an admin, I want to assign tasks to staff so work can be tracked.

### Acceptance Criteria

* Multiple tasks can be added
* Tasks saved individually
* Tasks linked to projects
* Tasks linked to staff
* Task status updates correctly

### Success Metric

95% task tracking visibility.

---

## Feature: Staff Management

### Description

Admin manages staff accounts.

### User Story

As an admin, I want to create staff accounts for department members.

### Acceptance Criteria

* Add/edit/delete staff
* Assign department role
* Username uniqueness validation
* Email validation
* Activate/deactivate users

### Success Metric

Staff onboarding under 3 minutes.

---

## Feature: Staff Task Dashboard

### Description

Staff can view and update assigned tasks.

### User Story

As staff, I want to manage my assigned work efficiently.

### Acceptance Criteria

* Staff sees only assigned tasks
* Staff can update task status
* Staff can comment on tasks
* Progress visible
* Deadline visible

### Success Metric

80% daily active usage by staff.

---

# P1 — Important Features

---

## Feature: Search & Filters

### Acceptance Criteria

* Filter by project
* Filter by role
* Filter by staff
* Search tasks instantly
* Search response under 1 second

---

## Feature: Activity Logs

### Acceptance Criteria

* Log all admin actions
* Log task updates
* Log project creation
* Store timestamps
* Admin can view logs

---

# P2 — Nice-to-Have Features

---

## Feature: Reports Module

### Acceptance Criteria

* Export task reports
* Export productivity reports
* Date-range filtering
* Download CSV/PDF

---

## Feature: Staff Productivity Charts

### Acceptance Criteria

* Weekly charts
* Monthly charts
* Task completion rate
* Graph rendering under 2 seconds

---

# 9. Explicitly Out of Scope

The following will NOT be built in MVP:

1. Real-time chat system
2. Video calling
3. Mobile applications
4. AI-generated task suggestions
5. Payroll management
6. Attendance tracking
7. Third-party payment gateways
8. Multi-company tenancy
9. Public client portal
10. Offline desktop application
11. Advanced automation workflows
12. Voice-based commands

---

# 10. User Scenarios

# Scenario 1 — Admin Creates Project

## Steps

1. Admin logs in
2. Opens Add Project
3. Fills project details
4. Selects department role
5. Adds tasks
6. Assigns tasks to staff
7. Saves project

## Outcome

Project and tasks created successfully.

## Edge Cases

* Empty required fields
* Invalid deadline
* Duplicate project names

---

# Scenario 2 — Staff Updates Task

## Steps

1. Staff logs in
2. Opens assigned task
3. Changes status
4. Adds comment
5. Marks progress

## Outcome

Task updated successfully.

## Edge Cases

* Unauthorized task access
* Invalid status update
* Session timeout

---

# Scenario 3 — Admin Monitors Dashboard

## Steps

1. Admin logs in
2. Opens dashboard
3. Reviews pending tasks
4. Filters delayed projects
5. Reviews productivity

## Outcome

Admin identifies operational bottlenecks quickly.

## Edge Cases

* Large data volume
* Slow internet
* Empty dashboard state

---

# 11. Non-Functional Requirements

# Performance

* Dashboard load under 3 seconds
* Search/filter response under 1 second
* Support 100+ concurrent users
* Pagination required for large datasets

---

# Security

* Password hashing mandatory
* Prepared SQL statements mandatory
* CSRF protection required
* XSS sanitization required
* Session regeneration after login
* Role-based authorization

---

# Accessibility

* Mobile responsive
* Keyboard navigable
* Proper form labels
* High contrast readability
* Accessible validation messages

---

# 12. Constraints

## Technical Constraints

* Must use Core PHP only
* Must use MySQL database
* Must run on XAMPP
* No Laravel/Node.js frameworks

---

# 13. MVP Definition

The MVP is complete when:

* Admin authentication works
* Staff authentication works
* Project CRUD works
* Task assignment works
* Staff task updates work
* Dashboard analytics work
* Search/filter works
* Security requirements implemented
* Responsive UI implemented