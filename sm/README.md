# Student Management System (Core PHP, MySQL, Bootstrap)

## Overview
A full-stack Student Management System with Admin and Student roles. Built with Core PHP (PDO), MySQL, HTML/CSS/JS, and Bootstrap. Features secure session-based auth, CRUD for students/teachers/subjects, attendance and marks management, and exports (CSV/Excel and PDF).

## Requirements
- XAMPP (Apache + MySQL)
- PHP 8.0+ recommended
- Composer not required (uses bundled FPDF for PDF)

## Setup (XAMPP)
1. Copy this folder `sm/` to `xampp/htdocs/`.
2. Start Apache and MySQL in XAMPP Control Panel.
3. Create database and tables:
   - Open phpMyAdmin: http://localhost/phpmyadmin
   - Create database `sms_db`
   - Import `sql/schema.sql`
4. Configure DB credentials:
   - Edit `config/config.php` to match your MySQL user/password (default XAMPP: user `root`, empty password)
5. Create first Admin user:
   - In phpMyAdmin, run:
```
INSERT INTO users (name, email, password_hash, role) VALUES
('Admin', 'admin@example.com', PASSWORD('change_me'), 'admin');
```
   - Then update to use PHP's password_hash via the UI by resetting password, or insert with PHP hash:
```
INSERT INTO users (name, email, password_hash, role) VALUES
('Admin', 'admin@example.com', '$2y$10$REPLACE_WITH_PASSWORD_HASH', 'admin');
```
   - To generate a password hash: temporarily browse to `http://localhost/sm/util/make_hash.php?pwd=YourPassword` after setup.
6. Visit app:
   - Admin login: http://localhost/sm/public/
   - Student login: http://localhost/sm/public/

## Default Roles
- admin: Full access to admin panel
- student: Limited access to student panel

## Project Structure
- `config/`: app config and database connection (PDO)
- `includes/`: shared layout and auth helpers
- `public/`: login and logout
- `admin/`: admin panel modules
- `student/`: student panel modules
- `assets/`: CSS/JS
- `lib/`: third-party libraries (FPDF)
- `sql/`: database schema
- `util/`: development helpers

## Security
- Sessions for auth + role checks
- Prepared statements everywhere
- `password_hash` / `password_verify`
- CSRF token on sensitive POST forms

## Notes
- Excel export provided as CSV (openable by Excel). PDF provided via FPDF.
- If you see errors, enable dev mode in `config/config.php` (set `APP_DEBUG` true).
