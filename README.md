# Task Management System

A complete production-ready Task Management System built with Core PHP and MySQL.

## Features
- Multi-user support (Admin & Staff)
- Role-based access control
- Project & Task management
- Analytics dashboard
- Secure authentication

## Tech Stack
- Core PHP 8.2+
- MySQL 8.0+
- Bootstrap 5.3.3
- jQuery 3.7.1
- PDO for database interactions

## Installation
1. Clone the repository into your XAMPP `htdocs` folder.
2. Create a database named `task_management` in MySQL.
3. Import `database/schema.sql` into your database.
4. Configure `.env` file with your database credentials.
5. Access the application via `http://localhost/Task_Management`.

## Project Structure
- `app/`: Application logic (MVC)
- `config/`: Configuration files
- `database/`: Migrations, seeders, and schema
- `public/`: Publicly accessible files (entry point, assets)
- `routes/`: Routing definitions
- `storage/`: Logs and cache
