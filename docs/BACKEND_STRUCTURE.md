Here’s the comprehensive production-grade `BACKEND_STRUCTURE.md` for your Task Management System.

# BACKEND_STRUCTURE.md — Task Management System

# 1. Architecture Overview

# Architecture Pattern

## Pattern Type

MVC-Inspired Modular Monolith

## Layers

```text
Client UI
   ↓
Routes Layer
   ↓
Middleware Layer
   ↓
Controller Layer
   ↓
Service Layer
   ↓
Model Layer
   ↓
Database Layer
```

---

# Backend Principles

* Thin controllers
* Reusable services
* Centralized validation
* Secure database abstraction
* API-ready structure
* Role-based authorization
* Modular business logic

---

# Authentication Strategy

## Authentication Type

Session-based authentication with secure PHP sessions.

## Session Rules

| Setting              | Value       |
| -------------------- | ----------- |
| Session Timeout      | 12 Hours    |
| Session Regeneration | After login |
| Cookie HTTP Only     | Enabled     |
| Secure Cookies       | HTTPS only  |
| SameSite             | Lax         |

---

# Authorization Strategy

| Role  | Access              |
| ----- | ------------------- |
| Admin | Full system access  |
| Staff | Assigned tasks only |

---

# Data Flow

```text
Request
 ↓
Route Validation
 ↓
Authentication Middleware
 ↓
Role Authorization
 ↓
Controller
 ↓
Service Layer
 ↓
Database
 ↓
JSON Response / View
```

---

# Caching Strategy

## Cache Driver

File-based caching

## Cache Areas

* Dashboard statistics
* Recent activity
* User permissions
* Reports

---

# 2. Database Schema

# DATABASE SETTINGS

| Property        | Value              |
| --------------- | ------------------ |
| Database Engine | InnoDB             |
| Charset         | utf8mb4            |
| Collation       | utf8mb4_unicode_ci |

---

# TABLE: users

## Purpose

Stores admin and staff accounts.

| Column        | Type                      | Constraints                                           | Description            |
| ------------- | ------------------------- | ----------------------------------------------------- | ---------------------- |
| id            | CHAR(36)                  | PK, NOT NULL                                          | UUID primary key       |
| role_id       | CHAR(36)                  | FK, NOT NULL                                          | References roles.id    |
| full_name     | VARCHAR(150)              | NOT NULL                                              | User full name         |
| username      | VARCHAR(100)              | UNIQUE, NOT NULL                                      | Login username         |
| email         | VARCHAR(255)              | UNIQUE, NOT NULL                                      | Email address          |
| phone         | VARCHAR(20)               | NULL                                                  | Contact number         |
| password_hash | VARCHAR(255)              | NOT NULL                                              | Bcrypt hashed password |
|               |                           |                                                       |                        |
| status        | ENUM('active','inactive') | DEFAULT 'active'                                      | Account status         |
| last_login_at | TIMESTAMP                 | NULL                                                  | Last login timestamp   |
| created_at    | TIMESTAMP                 | DEFAULT CURRENT_TIMESTAMP                             | Creation time          |
| updated_at    | TIMESTAMP                 | DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP | Update time            |
| deleted_at    | TIMESTAMP                 | NULL                                                  | Soft delete timestamp  |

---

## Indexes

| Index   | Columns  |
| ------- | -------- |
| PRIMARY | id       |
| UNIQUE  | username |
| UNIQUE  | email    |
| INDEX   | role_id  |
| INDEX   | status   |

---

## Relationships

| Relationship             | Type        |
| ------------------------ | ----------- |
| users.role_id → roles.id | Many-to-One |

---

# TABLE: roles

| Column     | Type         | Constraints                                           | Description  |
| ---------- | ------------ | ----------------------------------------------------- | ------------ |
| id         | CHAR(36)     | PK, NOT NULL                                          | UUID         |
| name       | VARCHAR(100) | UNIQUE, NOT NULL                                      | Role name    |
| slug       | VARCHAR(100) | UNIQUE, NOT NULL                                      | System slug  |
| created_at | TIMESTAMP    | DEFAULT CURRENT_TIMESTAMP                             | Created time |
| updated_at | TIMESTAMP    | DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP | Updated time |

---

# TABLE: sessions

| Column        | Type         | Constraints                                           | Description        |
| ------------- | ------------ | ----------------------------------------------------- | ------------------ |
| id            | CHAR(36)     | PK, NOT NULL                                          | UUID               |
| user_id       | CHAR(36)     | FK, NOT NULL                                          | User reference     |
| session_token | VARCHAR(255) | UNIQUE, NOT NULL                                      | Session identifier |
| user_agent    | TEXT         | NOT NULL                                              | Browser metadata   |
| expires_at    | TIMESTAMP    | NOT NULL                                              | Expiry timestamp   |
| created_at    | TIMESTAMP    | DEFAULT CURRENT_TIMESTAMP                             | Created time       |
| updated_at    | TIMESTAMP    | DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP | Updated time       |

---

## Foreign Keys

| FK                 | Action            |
| ------------------ | ----------------- |
| user_id → users.id | ON DELETE CASCADE |

---

# TABLE: projects

| Column       | Type                                                                                                                                                  | Constraints                                           | Description         |
| ------------ | ----------------------------------------------------------------------------------------------------------------------------------------------------- | ----------------------------------------------------- | ------------------- |
| id           | CHAR(36)                                                                                                                                              | PK, NOT NULL                                          | UUID                |
| created_by   | CHAR(36)                                                                                                                                              | FK, NOT NULL                                          | Admin creator       |
| role_id      | CHAR(36)                                                                                                                                              | FK, NOT NULL                                          | Department role     |
| project_name | VARCHAR(255)                                                                                                                                          | UNIQUE, NOT NULL                                      | Project title       |
| client_name  | VARCHAR(255)                                                                                                                                          | NOT NULL                                              | Client              |
| description  | TEXT                                                                                                                                                  | NULL                                                  | Project description |
| start_date   | DATE                                                                                                                                                  | NOT NULL                                              | Start date          |
| deadline     | DATE                                                                                                                                                  | NOT NULL                                              | Deadline            |
| status       | ENUM('pending','active','completed','cancelled','other') if other selete so frontend on textarea field show and user enter reason and this data save  | DEFAULT 'pending'                                     | Project status      |
| created_at   | TIMESTAMP                                                                                                                                             | DEFAULT CURRENT_TIMESTAMP                             | Created             |
| updated_at   | TIMESTAMP                                                                                                                                             | DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP | Updated             |
| deleted_at   | TIMESTAMP                                                                                                                                             | NULL                                                  | Soft delete         |

---

## Indexes

| Index   | Columns      |
| ------- | ------------ |
| PRIMARY | id           |
| UNIQUE  | project_name |
| INDEX   | role_id      |
| INDEX   | status       |
| INDEX   | deadline     |

---

## Foreign Keys

| FK                    | Action             |
| --------------------- | ------------------ |
| created_by → users.id | ON DELETE RESTRICT |
| role_id → roles.id    | ON DELETE RESTRICT |

---

# TABLE: tasks

| Column              | Type                                                       | Constraints                                           | Description          |
| ------------------- | ---------------------------------------------------------- | ----------------------------------------------------- | -------------------- |
| id                  | CHAR(36)                                                   | PK, NOT NULL                                          | UUID                 |
| project_id          | CHAR(36)                                                   | FK, NOT NULL                                          | Project reference    |
| assigned_to         | CHAR(36)                                                   | FK, NOT NULL                                          | Staff reference      |
| role_id             | CHAR(36)                                                   | FK, NOT NULL                                          | Department role      |
| title               | VARCHAR(255)                                               | NOT NULL                                              | Task title           |
| description         | TEXT                                                       | NULL                                                  | Task details         |
| status              | ENUM('pending','in_progress','review','completed','other') | DEFAULT 'pending'                                     | Task status          |
| progress_percentage | TINYINT UNSIGNED                                           | DEFAULT 0                                             | Task progress        |
| due_date            | DATE                                                       | NOT NULL                                              | Due date             |
| priority            | ENUM('low','medium','high')                                | DEFAULT 'medium'                                      | Priority             |
| completed_at        | TIMESTAMP                                                  | NULL                                                  | Completion timestamp |
| created_at          | TIMESTAMP                                                  | DEFAULT CURRENT_TIMESTAMP                             | Created              |
| updated_at          | TIMESTAMP                                                  | DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP | Updated              |
| deleted_at          | TIMESTAMP                                                  | NULL                                                  | Soft delete          |

---

## Foreign Keys

| FK                       | Action             |
| ------------------------ | ------------------ |
| project_id → projects.id | ON DELETE CASCADE  |
| assigned_to → users.id   | ON DELETE RESTRICT |
| role_id → roles.id       | ON DELETE RESTRICT |

---

## Indexes

| Index   | Columns     |
| ------- | ----------- |
| PRIMARY | id          |
| INDEX   | project_id  |
| INDEX   | assigned_to |
| INDEX   | status      |
| INDEX   | due_date    |

---

# TABLE: task_comments

| Column     | Type      | Constraints                                           | Description     |
| ---------- | --------- | ----------------------------------------------------- | --------------- |
| id         | CHAR(36)  | PK, NOT NULL                                          | UUID            |
| task_id    | CHAR(36)  | FK, NOT NULL                                          | Task reference  |
| user_id    | CHAR(36)  | FK, NOT NULL                                          | Comment creator |
| comment    | TEXT      | NOT NULL                                              | Comment body    |
| created_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP                             | Created         |
| updated_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP | Updated         |

---

# TABLE: activity_logs

| Column      | Type         | Constraints                                           | Description     |
| ----------- | ------------ | ----------------------------------------------------- | --------------- |
| id          | CHAR(36)     | PK, NOT NULL                                          | UUID            |
| user_id     | CHAR(36)     | FK, NOT NULL                                          | Actor           |
| entity_type | VARCHAR(100) | NOT NULL                                              | Affected entity |
| entity_id   | CHAR(36)     | NOT NULL                                              | Related UUID    |
| action      | VARCHAR(100) | NOT NULL                                              | Action name     |
| metadata    | JSON         | NULL                                                  | Additional data |
| created_at  | TIMESTAMP    | DEFAULT CURRENT_TIMESTAMP                             | Created         |
| updated_at  | TIMESTAMP    | DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP | Updated         |

---

# TABLE: settings

| Column        | Type         | Constraints                                           | Description   |
| ------------- | ------------ | ----------------------------------------------------- | ------------- |
| id            | CHAR(36)     | PK, NOT NULL                                          | UUID          |
| setting_key   | VARCHAR(150) | UNIQUE, NOT NULL                                      | Setting key   |
| setting_value | TEXT         | NULL                                                  | Setting value |
| created_at    | TIMESTAMP    | DEFAULT CURRENT_TIMESTAMP                             | Created       |
| updated_at    | TIMESTAMP    | DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP | Updated       |

---

# 3. API ENDPOINTS

# AUTH ENDPOINTS

# POST /api/auth/login

## Authentication Required

No

---

## Request Body

```json
{
  "username": "admin",
  "password": "password123"
}
```

---

## Validation Rules

| Field    | Rules                  |
| -------- | ---------------------- |
| username | required,min:3,max:100 |
| password | required,min:8,max:64  |

---

## Success Response

### 200 OK

```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "user": {
      "id": "uuid",
      "fullName": "Admin User",
      "role": "admin"
    }
  }
}
```

---

## Error Cases

| Status | Condition           |
| ------ | ------------------- |
| 401    | Invalid credentials |
| 403    | Account inactive    |
| 422    | Validation failed   |
| 429    | Too many attempts   |
| 500    | Server error        |

---

## Side Effects

* Creates session
* Updates last_login_at
* Creates activity log

---

# POST /api/auth/logout

## Authentication Required

Yes

---

## Success Response

```json
{
  "success": true,
  "message": "Logout successful"
}
```

---

## Side Effects

* Deletes active session
* Clears cookies
* Logs logout activity

---

# PROJECT ENDPOINTS

# POST /api/projects

## Authentication Required

Admin

---

## Request Body

```json
{
  "projectName": "Website Redesign",
  "clientName": "ABC Company",
  "description": "Project details",
  "startDate": "2026-05-10",
  "deadline": "2026-06-10",
  "roleId": "uuid"
}
```

---

## Validation Rules

| Field       | Rules            |
| ----------- | ---------------- |
| projectName | required,max:255 |
| clientName  | required,max:255 |
| deadline    | required,date    |
| roleId      | required,uuid    |

---

## Success Response

### 201 Created

```json
{
  "success": true,
  "message": "Project created successfully"
}
```

---

## Error Cases

| Status | Condition         |
| ------ | ----------------- |
| 401    | Unauthorized      |
| 403    | Forbidden         |
| 409    | Duplicate project |
| 422    | Validation error  |
| 500    | Database failure  |

---

# GET /api/projects

## Authentication Required

Yes

---

## Query Parameters

| Parameter | Type    |
| --------- | ------- |
| page      | integer |
| search    | string  |
| status    | string  |

---

## Success Response

```json
{
  "success": true,
  "data": {
    "projects": [],
    "pagination": {}
  }
}
```

---

# TASK ENDPOINTS

# POST /api/tasks

## Authentication Required

Admin

---

## Request Body

```json
{
  "projectId": "uuid",
  "assignedTo": "uuid",
  "title": "Create landing page",
  "description": "Task details",
  "dueDate": "2026-05-20"
}
```

---

## Validation Rules

| Field      | Rules            |
| ---------- | ---------------- |
| projectId  | required,uuid    |
| assignedTo | required,uuid    |
| title      | required,max:255 |
| dueDate    | required,date    |

---

## Error Cases

| Status | Condition         |
| ------ | ----------------- |
| 404    | User not found    |
| 404    | Project not found |
| 422    | Invalid data      |
| 500    | DB error          |

---

# PATCH /api/tasks/{id}

## Authentication Required

Assigned Staff or Admin

---

## Request Body

```json
{
  "status": "completed",
  "progressPercentage": 100
}
```

---

## Validation Rules

| Field              | Rules   |       |         |
| ------------------ | ------- | ----- | ------- |
| status             | enum    |       |         |
| progressPercentage | integer | min:0 | max:100 |

---

## Side Effects

* Updates task
* Updates completed_at
* Creates activity log

---

# STAFF ENDPOINTS

# POST /api/staff

## Authentication Required

Admin

---

## Request Body

```json
{
  "fullName": "John Doe",
  "username": "johndoe",
  "email": "john@example.com",
  "password": "StrongPass123",
  "roleId": "uuid"
}
```

---

# 4. Authentication

# JWT Structure

## Access Token Payload

```json
{
  "sub": "uuid",
  "role": "admin",
  "sessionId": "uuid",
  "iat": 1710000000,
  "exp": 1710043200
}
```

---

## Refresh Token Payload

```json
{
  "sub": "uuid",
  "tokenType": "refresh",
  "iat": 1710000000,
  "exp": 1712592000
}
```

---

# Token Expiry

| Token         | Expiry   |
| ------------- | -------- |
| Access Token  | 12 Hours |
| Refresh Token | 30 Days  |

---

# Password Hashing

| Setting   | Value  |
| --------- | ------ |
| Algorithm | bcrypt |
| Rounds    | 12     |

---

# Authorization Levels

| Role  | Routes   |
| ----- | -------- |
| Admin | /admin/* |
| Staff | /staff/* |

---

# 5. Error Response Format

# Standard Error JSON

```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "email": [
      "Email is required"
    ]
  },
  "code": 422
}
```

---

# HTTP Status Mapping

| Code | Meaning               |
| ---- | --------------------- |
| 200  | Success               |
| 201  | Created               |
| 400  | Bad Request           |
| 401  | Unauthorized          |
| 403  | Forbidden             |
| 404  | Not Found             |
| 409  | Conflict              |
| 422  | Validation Error      |
| 429  | Too Many Requests     |
| 500  | Internal Server Error |

---

# 6. Caching Strategy

# Cache Rules

| Resource         | Key Format            | TTL      |
| ---------------- | --------------------- | -------- |
| Dashboard Stats  | dashboard:{user_id}   | 300 sec  |
| Project List     | projects:page:{n}     | 120 sec  |
| User Permissions | permissions:{user_id} | 3600 sec |

---

# Cache Invalidation

| Trigger        | Action                  |
| -------------- | ----------------------- |
| Task update    | Clear dashboard cache   |
| Project create | Clear projects cache    |
| Role update    | Clear permissions cache |

---

# 7. Rate Limiting

| Endpoint Type | Limit           |
| ------------- | --------------- |
| Login         | 5 req / 15 mins |
| API CRUD      | 100 req / min   |
