# APP_FLOW.md — Task Management System

# 1. Application Overview

## System Type

Role-based multi-user task and project management system for digital agencies.

## User Roles

### 1. Admin

Full system access:

* Dashboard
* Staff management
* Project management
* Task assignment
* Reports
* Settings

### 2. Staff

Limited access:

* Assigned tasks
* Task updates
* Comments
* Progress tracking

---

# 2. Entry Points

# Public Entry Points

| Entry Point              | URL                    | Access |
| ------------------------ | ---------------------- | ------ |
| Login Page               | `/login.php`           | Public |
| Session Expired Redirect | `/login.php?expired=1` | Public |
| Unauthorized Access Page | `/403.php`             | Public |
| Not Found Page           | `/404.php`             | Public |
| Server Error Page        | `/500.php`             | Public |

---

# Protected Entry Points

## Admin Access

| Screen           | URL                    |
| ---------------- | ---------------------- |
| Dashboard        | `/admin/dashboard.php` |
| Projects         | `/admin/projects/`     |
| Staff Management | `/admin/staff/`        |
| Tasks            | `/admin/tasks/`        |
| Reports          | `/admin/reports/`      |
| Settings         | `/admin/settings/`     |

---

## Staff Access

| Screen           | URL                                 |
| ---------------- | ----------------------------------- |
| Dashboard        | `/staff/dashboard.php`              |
| My Tasks         | `/staff/tasks/`                     |
| Task Details     | `/staff/task-view.php?id={task_id}` |
| Profile Settings | `/staff/profile.php`                |

---

# 3. Core User Flows

# FLOW 1 — Authentication Login Flow

## Happy Path

### Step 1

Page:
`/login.php`

### Elements

* Username input
* Password input
* Login button
* Remember session checkbox

### User Action

User enters credentials and clicks Login.

### Validation Rules

* Username required
* Password required

### System Response

* Validate credentials
* Verify hashed password
* Create secure session
* Regenerate session ID
* Redirect based on role

### Next Step

* Admin → `/admin/dashboard.php`
* Staff → `/staff/dashboard.php`

---

## Error States

### Invalid Username

Message:
`Username does not exist.`

### Invalid Password

Message:
`Incorrect password. Please try again.`

### Empty Fields

Message:
`All fields are required.`

### Account Disabled

Message:
`Your account has been deactivated. Contact administrator.`

### Session Timeout

Message:
`Your session has expired. Please login again.`

---

## Edge Cases

| Edge Case                      | Behavior                          |
| ------------------------------ | --------------------------------- |
| User refreshes after login     | Redirect to dashboard             |
| User presses back after logout | Prevent cached dashboard          |
| Multiple failed attempts       | Temporary login cooldown          |
| Browser closed suddenly        | Session invalidated after timeout |

---

# FLOW 2 — Admin Creates Project

## Happy Path

### Step 1

Page:
`/admin/projects/create.php`

### Elements

* Project name
* Description
* Client name
* Start date
* Deadline
* Status dropdown
* Role dropdown
* Task textarea
* Assign staff dropdown

### User Action

Admin fills form and clicks Save Project.

### Validation Rules

* Project name required
* Role required

### System Response

* Save project
* Create linked tasks
* Assign tasks to selected staff
* Create activity log
* Show success toast

### Success Message

`Project created successfully.`

### Next Step

Redirect:
`/admin/projects/view.php?id={project_id}`

---

## Error States

### Duplicate Project Name

Message:
`Project name already exists.`

### Invalid Date

Message:
`Deadline must be greater than start date.`

### Empty Task List

Message:
`Please add at least one task.`

### Database Failure

Message:
`Unable to save project. Please try again.`

---

## Edge Cases

| Edge Case                       | Behavior                    |
| ------------------------------- | --------------------------- |
| Admin closes tab mid-save       | Unsaved changes warning     |
| Staff deleted during assignment | Refresh assignment dropdown |
| Large task list                 | Paginated task rendering    |
| Network interruption            | Retry submission prompt     |

---

# FLOW 3 — Staff Updates Task

## Happy Path

### Step 1

Page:
`/staff/task-view.php?id={task_id}`

### Elements

* Task title
* Description
* Deadline
* Status dropdown
* Comment box
* Progress slider
* Save button

### User Action

Staff updates task status and clicks Save.

### Validation Rules

* Status required
* Progress between 0-100
* Comment maximum 1000 characters

### System Response

* Update task record
* Save comment
* Log activity
* Update progress
* Notify admin

### Success Message

`Task updated successfully.`

### Next Step

Redirect:
`/staff/tasks/`

---

## Error States

### Unauthorized Access

Message:
`You do not have permission to access this task.`

### Invalid Task ID

Message:
`Task not found.`

### Session Expired

Message:
`Please login again.`

### Save Failure

Message:
`Unable to update task. Please retry.`

---

## Edge Cases

| Edge Case                       | Behavior                    |
| ------------------------------- | --------------------------- |
| Two users update simultaneously | Last update timestamp shown |
| Task already completed          | Disable status editing      |
| Browser offline                 | Local retry notification    |
| Invalid progress value          | Prevent submission          |

---

# FLOW 4 — Staff Management Flow

## Happy Path

### Step 1

Page:
`/admin/staff/create.php`

### User Action

Admin creates staff profile.

### Validation Rules

* Unique username
* Valid email
* Phone numeric only
* Password minimum 8 characters

### System Response

* Hash password
* Save user
* Assign role
* Generate activity log

### Success Message

`Staff account created successfully.`

---

## Error States

| Error              | Message                                       |
| ------------------ | --------------------------------------------- |
| Duplicate username | `Username already exists.`                    |
| Invalid email      | `Please enter valid email address.`           |
| Weak password      | `Password must contain minimum 8 characters.` |
| Missing role       | `Please select department role.`              |

---

# FLOW 5 — Logout Flow

## Happy Path

### User Action

Click Logout.

### System Response

* Destroy session
* Clear cookies
* Redirect to login

### Success Message

`You have been logged out successfully.`

---

# 4. Navigation Map

```text
LOGIN
│
├── Admin Dashboard
│   ├── Dashboard Overview
│   ├── Projects
│   │   ├── Project List
│   │   ├── Create Project
│   │   ├── Edit Project
│   │   ├── View Project
│   │   └── Project Tasks
│   │
│   ├── Staff Management
│   │   ├── Staff List
│   │   ├── Add Staff
│   │   ├── Edit Staff
│   │   └── Staff Details
│   │
│   ├── Task Management
│   │   ├── Task List
│   │   ├── Task Details
│   │   ├── Task Filters
│   │   └── Activity Logs
│   │
│   ├── Settings
│   └── Logout
│
└── Staff Dashboard
    ├── My Tasks
    ├── Task Details
    ├── Update Task
    ├── Comments
    ├── Progress Tracking
    ├── Profile
    └── Logout
```

---

# 5. Screen Inventory

# Login Screen

| Property | Value              |
| -------- | ------------------ |
| Route    | `/login.php`       |
| Access   | Public             |
| Purpose  | Authenticate users |

## Key Elements

* Username
* Password
* Login button

## Actions

| Action        | Result             |
| ------------- | ------------------ |
| Submit login  | Dashboard redirect |
| Invalid login | Error message      |

## States

* Loading
* Validation error
* Success redirect
* Session expired

---

# Admin Dashboard

| Property | Value                  |
| -------- | ---------------------- |
| Route    | `/admin/dashboard.php` |
| Access   | Admin                  |

## Key Elements

* Analytics cards
* Project overview
* Pending tasks
* Recent activities

## States

* Loading skeleton
* Empty analytics
* Success
* Server error

---

# Project Management Screen

| Property | Value              |
| -------- | ------------------ |
| Route    | `/admin/projects/` |
| Access   | Admin              |

## Actions

| Action         | Next Step          |
| -------------- | ------------------ |
| Add Project    | Create form        |
| Edit Project   | Edit screen        |
| Delete Project | Confirmation modal |
| View Project   | Project details    |

---

# Staff Dashboard

| Property | Value                  |
| -------- | ---------------------- |
| Route    | `/staff/dashboard.php` |
| Access   | Staff                  |

## Key Elements

* Assigned tasks
* Due dates
* Progress
* Notifications

---

# 6. Decision Points

# Authentication Logic

```text
IF username exists
    THEN validate password
        IF password valid
            THEN create session
        ELSE show invalid password
ELSE
    show invalid username
```

---

# Role Routing Logic

```text
IF role = admin
    THEN redirect admin dashboard
ELSE IF role = staff
    THEN redirect staff dashboard
ELSE
    deny access
```

---

# Session Validation Logic

```text
IF session expired
    THEN logout user
    redirect login
```

---

# Task Permission Logic

```text
IF logged user owns task
    THEN allow access
ELSE
    show unauthorized page
```

---

# 7. Error Handling

# 404 Error

## Display

* Friendly error illustration
* Message:
  `Page not found.`

## Actions Available

* Go dashboard
* Return previous page

---

# 500 Error

## Display

Message:
`Something went wrong on our server.`

## Actions

* Retry button
* Contact admin

---

# AJAX Failure

## Display

Toast notification:
`Request failed. Please retry.`

---

# Database Timeout

## Display

Message:
`Server is taking too long to respond.`

---

# 8. Responsive Behavior

# Desktop Behavior

* Persistent sidebar
* Multi-column dashboard
* Hover tooltips
* Full analytics charts

---

# Mobile Behavior

* Collapsible sidebar
* Single-column cards
* Bottom spacing for touch
* Larger buttons
* Compact tables with horizontal scroll

---

# Mobile Flow Differences

| Desktop                | Mobile          |
| ---------------------- | --------------- |
| Sidebar always visible | Hamburger menu  |
| Multi-column layout    | Single-column   |
| Table view             | Card-based list |
| Hover actions          | Tap actions     |

---

# 9. Validation Rules

# Global Validation Rules

| Field            | Rule                  |
| ---------------- | --------------------- |
| Username         | Required, min 3 chars |
| Password         | Required, min 8 chars |
| Email            | Valid email format    |
| Phone            | Numeric only          |
| Project Name     | Required, unique      |
| Deadline         | Future date only      |
| Task Description | Required              |
| Progress         | 0-100 only            |

---

# 10. Session Management Flow

## Session Timeout

12 hours inactivity.

## Expiry Behavior

* Destroy session
* Redirect login
* Show timeout message

## Security Rules

* Regenerate session ID after login
* Block session fixation
* Secure HTTP-only cookies

---

# 11. Activity Logging Flow

Actions logged:

* Login
* Logout
* Project creation
* Task updates
* Staff creation
* Status changes

Each log contains:

* User ID
* Action type
* Timestamp
* IP address
* Related entity ID
