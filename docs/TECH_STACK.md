Here’s the production-grade `TECH_STACK.md` tailored specifically for your Core PHP + MySQL Task Management System.

# TECH_STACK.md — Task Management System

# 1. Technology Stack Overview

## Application Type

Web Application

## Architecture Type

Monolithic Modular MVC-Inspired Architecture

## Scale Target

MVP → Small-to-Medium Agency Scale

## Deployment Environment

* XAMPP Local Server
* Apache HTTP Server
* MySQL Database
* PHP Runtime

---

# 2. Core Technology Decisions

# Frontend Stack

| Technology   | Version              | Documentation                                                                                                                      | Reason for Selection                         |
| ------------ | -------------------- | ---------------------------------------------------------------------------------------------------------------------------------- | -------------------------------------------- |
| HTML5        | HTML Living Standard | [https://developer.mozilla.org/en-US/docs/Web/HTML](https://developer.mozilla.org/en-US/docs/Web/HTML)                             | Semantic structure and browser compatibility |
| CSS3         | CSS Level 3          | [https://developer.mozilla.org/en-US/docs/Web/CSS](https://developer.mozilla.org/en-US/docs/Web/CSS)                               | Responsive styling and animations            |
| JavaScript   | ECMAScript 2022      | [https://developer.mozilla.org/en-US/docs/Web/JavaScript](https://developer.mozilla.org/en-US/docs/Web/JavaScript)                 | Dynamic UI interactions                      |
| Bootstrap    | 5.3.3                | [https://getbootstrap.com/docs/5.3/getting-started/introduction/](https://getbootstrap.com/docs/5.3/getting-started/introduction/) | Fast responsive SaaS UI development          |
| jQuery       | 3.7.1                | [https://api.jquery.com/](https://api.jquery.com/)                                                                                 | AJAX handling and DOM utilities              |
| DataTables   | 1.13.8               | [https://datatables.net/manual/](https://datatables.net/manual/)                                                                   | Dynamic searchable tables                    |
| Chart.js     | 4.4.1                | [https://www.chartjs.org/docs/latest/](https://www.chartjs.org/docs/latest/)                                                       | Dashboard analytics charts                   |
| Toastr.js    | 2.1.4                | [https://codeseven.github.io/toastr/](https://codeseven.github.io/toastr/)                                                         | Toast notifications                          |
| Font Awesome | 6.5.1                | [https://fontawesome.com/docs](https://fontawesome.com/docs)                                                                       | Premium admin dashboard icons                |
| SweetAlert2  | 11.10.5              | [https://sweetalert2.github.io/](https://sweetalert2.github.io/)                                                                   | Modern confirmation modals                   |
| Flatpickr    | 4.6.13               | [https://flatpickr.js.org/](https://flatpickr.js.org/)                                                                             | Lightweight date picker                      |

---

# Backend Stack

| Technology         | Version    | Documentation                                                                            | Reason for Selection                      |
| ------------------ | ---------- | ---------------------------------------------------------------------------------------- | ----------------------------------------- |
| PHP                | 8.2.12     | [https://www.php.net/docs.php](https://www.php.net/docs.php)                             | Stable modern PHP with strong performance |
| Apache             | 2.4.58     | [https://httpd.apache.org/docs/2.4/](https://httpd.apache.org/docs/2.4/)                 | Compatible with XAMPP and PHP             |
| MySQL              | 8.0.36     | [https://dev.mysql.com/doc/](https://dev.mysql.com/doc/)                                 | Relational database with indexing support |
| PDO                | PHP Native | [https://www.php.net/manual/en/book.pdo.php](https://www.php.net/manual/en/book.pdo.php) | Secure prepared SQL statements            |

|                    |            |                                                                                          |                                           |
| vlucas/phpdotenv   | 5.6.0      | [https://github.com/vlucas/phpdotenv](https://github.com/vlucas/phpdotenv)               | Environment variable management           |
| Monolog            | 3.5.0      | [https://seldaek.github.io/monolog/](https://seldaek.github.io/monolog/)                 | Logging and debugging                     |
| Respect/Validation | 2.3.0      | [https://respect-validation.readthedocs.io/](https://respect-validation.readthedocs.io/) | Validation layer                          |
| Ramsey UUID        | 4.7.5      | [https://uuid.ramsey.dev/en/stable/](https://uuid.ramsey.dev/en/stable/)                 | Unique identifiers                        |

---

# State Management Strategy

| Technology       | Version        | Documentation                                                                                                                                | Reason                             |
| ---------------- | -------------- | -------------------------------------------------------------------------------------------------------------------------------------------- | ---------------------------------- |
| PHP Sessions     | PHP Native     | [https://www.php.net/manual/en/book.session.php](https://www.php.net/manual/en/book.session.php)                                             | Simple secure authentication state |
| LocalStorage API | Browser Native | [https://developer.mozilla.org/en-US/docs/Web/API/Window/localStorage](https://developer.mozilla.org/en-US/docs/Web/API/Window/localStorage) | Temporary UI persistence           |

---

# Form Handling

| Technology       | Version        | Documentation                                                                                                            | Reason                                         |
| ---------------- | -------------- | ------------------------------------------------------------------------------------------------------------------------ | ---------------------------------------------- |
| AJAX + Fetch API | Browser Native | [https://developer.mozilla.org/en-US/docs/Web/API/Fetch_API](https://developer.mozilla.org/en-US/docs/Web/API/Fetch_API) | Partial page updates                           |
| jQuery AJAX      | 3.7.1          | [https://api.jquery.com/jquery.ajax/](https://api.jquery.com/jquery.ajax/)                                               | Legacy compatibility and faster implementation |

---

# File Storage

| Technology         | Version           | Documentation                                                                                                    | Reason                                 |
| ------------------ | ----------------- | ---------------------------------------------------------------------------------------------------------------- | -------------------------------------- |
| Local File Storage | Native Filesystem | [https://www.php.net/manual/en/features.file-upload.php](https://www.php.net/manual/en/features.file-upload.php) | MVP simplicity and XAMPP compatibility |

Upload Path:

```text
/uploads/tasks/
/uploads/profile/
/uploads/temp/
```

---

# 3. Folder Architecture

```text
/project-root
│
├── /app
│   ├── /controllers
│   ├── /models
│   ├── /views
│   ├── /services
│   ├── /middleware
│   ├── /helpers
│   ├── /validators
│   └── /core
│
├── /config
│
├── /database
│   ├── /migrations
│   ├── /seeders
│   └── schema.sql
│
├── /routes
│
├── /public
│   ├── /assets
│   │   ├── /css
│   │   ├── /js
│   │   ├── /images
│   │   └── /fonts
│   │
│   ├── /uploads
│   └── index.php
│
├── /.env
├── /.htaccess
└── README.md
```

---

# 4. Environment Variables

# Application

```env
APP_NAME=TaskManagementSystem
APP_ENV=production
APP_DEBUG=false
APP_URL=http://localhost/task-management-system
APP_TIMEZONE=Asia/Kolkata
```

---

# Database

```env
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=task_management
DB_USERNAME=root
DB_PASSWORD=
DB_CHARSET=utf8mb4
```

---

# Session Configuration

```env
SESSION_DRIVER=file
SESSION_LIFETIME=720
SESSION_SECURE_COOKIE=false
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=Lax
```

---

# Security

```env
BCRYPT_ROUNDS=12
CSRF_TOKEN_EXPIRY=3600
LOGIN_RATE_LIMIT=5
LOGIN_BLOCK_MINUTES=15
```

---

# Logging

```env
LOG_CHANNEL=file
LOG_LEVEL=debug
LOG_PATH=storage/logs
```

---

# Cache

```env
CACHE_DRIVER=file
CACHE_LIFETIME=3600
```

---

# 6. Frontend Dependency Lock Block

```json
{
  "bootstrap": "5.3.3",
  "jquery": "3.7.1",
  "datatables.net": "1.13.8",
  "chart.js": "4.4.1",
  "toastr": "2.1.4",
  "sweetalert2": "11.10.5",
  "flatpickr": "4.6.13",
  "font-awesome": "6.5.1"
}
```

---

# 7. Backend Dependency Lock Block

```json
{
  "php": "8.2.12",
  "apache": "2.4.58",
  "mysql": "8.0.36",
  "vlucas/phpdotenv": "5.6.0",
  "monolog/monolog": "3.5.0",
  "respect/validation": "2.3.0",
  "ramsey/uuid": "4.7.5"
}
```

---

# 8. Security Configuration

# Password Security

| Setting             | Value           |
| ------------------- | --------------- |
| Algorithm           | PASSWORD_BCRYPT |
| Bcrypt Rounds       | 12              |
| Password Min Length | 8               |
| Password Max Length | 64              |

---

# Session Security

| Setting              | Value            |
| -------------------- | ---------------- |
| Session Lifetime     | 12 Hours         |
| HTTP Only Cookies    | Enabled          |
| Secure Cookies       | Enabled in HTTPS |
| SameSite Policy      | Lax              |
| Session Regeneration | After Login      |

---

# Authentication Protection

| Feature                   | Value      |
| ------------------------- | ---------- |
| Failed Login Attempts     | 5          |
| Temporary Lock Duration   | 15 Minutes |
| CSRF Token Expiry         | 1 Hour     |
| Account Status Validation | Enabled    |

---

# SQL Injection Protection

Protection Method:

* PDO Prepared Statements Only
* Parameterized Queries Mandatory

---

# XSS Protection

Protection Method:

* htmlspecialchars()
* Input sanitization
* Output escaping

---

# CORS Configuration

| Setting         | Value               |
| --------------- | ------------------- |
| Allowed Origin  | Same Domain Only    |
| Allowed Methods | GET,POST,PUT,DELETE |
| Credentials     | Enabled             |

---

# Rate Limiting

| Endpoint        | Limit                |
| --------------- | -------------------- |
| Login API       | 5 requests / 15 mins |
| Task Update API | 60 requests / minute |

---

# 9. Performance Configuration

# Database Optimization

* Indexed foreign keys
* Query pagination
* Optimized joins
* Lazy loading

---

# Frontend Optimization

* Minified assets
* Deferred JS loading
* Lazy image loading
* CDN-ready architecture

---

# Backend Optimization

* Reusable helper classes
* Service layer architecture
* Query caching
* File-based caching

---

# 10. Browser Support

| Browser | Supported Version |
| ------- | ----------------- |
| Chrome  | 120+              |
| Edge    | 120+              |
| Firefox | 121+              |
| Safari  | 17+               |

---

# 11. Development Standards

# Coding Standards

| Standard          | Value                     |
| ----------------- | ------------------------- |
| PHP Standard      | PSR-12                    |
| Naming Convention | camelCase + snake_case DB |
| Database Naming   | snake_case                |
| File Naming       | kebab-case                |
