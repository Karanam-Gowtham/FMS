# Technology Stack

## Overview

The Faculty Management System (FMS) is built using a **traditional LAMP-like stack** (Linux/Apache/MySQL/PHP) optimized for institutional deployment on XAMPP environments, with modern frontend enhancements.

---

## 1. Complete Technology Stack

| Layer | Technology | Version | Purpose |
|:---|:---|:---|:---|
| **Server** | Apache (XAMPP) | 2.4.x | HTTP server, URL rewriting via `.htaccess` |
| **Backend** | PHP | 8.2.12 / 7.4+ compatible | Core application logic, session management, file handling |
| **Database** | MariaDB (MySQL-compatible) | 10.4.32 | Relational data storage for all entities |
| **Frontend** | HTML5 | — | Page structure and semantic markup |
| **Styling** | CSS3 | — | Custom stylesheets (24 CSS files) |
| **JavaScript** | Vanilla JS | ES6+ | Dynamic UI, AJAX calls, form validation |
| **UI Framework** | Bootstrap | 4.x/5.x | Responsive grid, components, utilities |
| **Icons** | FontAwesome | 5.x/6.x | Icon set for navigation and action buttons |
| **Fonts** | Google Fonts (Inter, Roboto) | — | Modern typography |
| **PDF Processing** | pdf-lib (JS) | Latest | Client-side PDF merging |
| **PDF Merging (Server)** | PDFMerger.php | Custom | Server-side PDF concatenation |
| **Email** | PHPMailer | 6.x | SMTP-based email notifications |
| **DB Driver** | mysqli | Built-in | Prepared statements for SQL injection prevention |
| **Version Control** | Git | — | Source code versioning |

---

## 2. Backend Stack Details

### PHP (Core Language)
- **No framework used** — The project uses **vanilla PHP** for maximum performance and zero dependency overhead.
- All database queries use **`mysqli` prepared statements** to prevent SQL injection.
- Session management via PHP's built-in `$_SESSION` with secure cookie configuration.
- File uploads handled with PHP's `move_uploaded_file()` with directory creation support.

### Key PHP Includes Architecture
```
includes/
├── connection.php    → DB connection (supports localhost + cloud env variables)
├── session.php       → Cookie parameters (httponly, samesite)
├── csrf.php          → CSRF token generation & validation
├── dept_scope.php    → Role-based access control (350 lines of security logic)
├── constants.php     → Centralized constants (table names, statuses, labels)
├── header.php        → Global navigation with notification badge
├── send_email.php    → PHPMailer wrapper for notifications
└── PDFMerger.php     → Server-side PDF merge utility
```

### Why Vanilla PHP?
1. **Performance**: No framework overhead; direct request handling.
2. **Simplicity**: Easy to understand and maintain for institutional IT teams.
3. **Portability**: Runs on any PHP hosting (shared hosting, XAMPP, Linux VPS).
4. **No Dependency Management**: No Composer, no `vendor/` directory complexities.

---

## 3. Frontend Stack Details

### HTML5
- Semantic elements (`<main>`, `<nav>`, `<header>`, `<section>`)
- Form elements with HTML5 validation attributes (`required`, `type="email"`)

### CSS3
- **24 custom stylesheet files** in `assets/css/`
- CSS animations (`@keyframes fadeIn`) for login page effects
- Glassmorphism effects (`backdrop-filter: blur(8px)`) on header
- Responsive design with `@media` queries
- Color-coded status badges (amber=pending, green=accepted, red=rejected)

### JavaScript (Vanilla)
- AJAX polling for notification badge (`setInterval` + `fetch`)
- Dynamic modal management (reject reason, re-upload, history)
- Client-side PDF merging using `pdf-lib`
- Form validation and dynamic select population

### Bootstrap
- Responsive grid system
- Modal dialogs
- Button styles and utility classes
- Table styling

### FontAwesome
- Navigation icons
- Action button icons (approve ✓, reject ✗, download ↓)
- Dashboard status indicators

---

## 4. Database Stack Details

### MariaDB 10.4.32
- **Database name**: `project-fms`
- **Engine**: InnoDB (all tables)
- **Character set**: utf8mb4 (full Unicode support)
- **Schema size**: 2500+ lines of SQL (30+ tables)

### Key Database Features Used
- **AUTO_INCREMENT** primary keys
- **DEFAULT values** for status columns
- **TIMESTAMP** with `DEFAULT current_timestamp()` for audit trails
- **VARCHAR** with appropriate length constraints
- **TEXT** type for rejection reasons and descriptions
- **Foreign key relationships** via application-level joins (not DB-level FK constraints)

### Connection Configuration
```php
// Local XAMPP
$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "project-fms";
$db_port = "3306";

// Cloud (Render/Railway) — via environment variables
$db_host = getenv('MYSQLHOST');
$db_user = getenv('MYSQLUSER');
// ... etc.
```

---

## 5. Libraries & Tools

| Library/Tool | Type | Usage |
|:---|:---|:---|
| **pdf-lib** | JavaScript (CDN) | Client-side PDF fetching, merging, and downloading |
| **PDFMerger.php** | PHP | Server-side PDF concatenation for bulk downloads |
| **PHPMailer** | PHP | SMTP email delivery for notification system |
| **phpMyAdmin** | Database Tool | Database management, schema import/export |
| **Git** | Version Control | Source code management (v1.0.0, commit 317ef43) |

---

## 6. Development & Deployment Environment

### Development
| Tool | Purpose |
|:---|:---|
| XAMPP | Local Apache + MariaDB + PHP stack |
| phpMyAdmin | Database administration |
| VS Code / IDE | Code editing |
| Git | Version control |
| Browser DevTools | Frontend debugging |

### Deployment Options
| Platform | Configuration |
|:---|:---|
| **Local XAMPP** | Place in `htdocs/mini/FMS/`, import SQL, configure `$base_url` |
| **Render** | Environment variables for DB connection, auto `$base_url` detection |
| **Railway** | Environment variables for MySQL, auto `$base_url` detection |
| **Linux VPS** | Apache + PHP + MySQL, standard LAMP deployment |

### Environment Variable Support
The `connection.php` supports automatic detection of cloud environments:
- `MYSQLHOST`, `MYSQLUSER`, `MYSQLPASSWORD`, `MYSQLDATABASE`, `MYSQLPORT`
- `RENDER_EXTERNAL_HOSTNAME` for Render deployments
- `RAILWAY_STATIC_URL` for Railway deployments

---

## 7. Security Technologies

| Technology | Implementation |
|:---|:---|
| **CSRF Protection** | Token-based via `includes/csrf.php` — token in session, hidden field in forms, validated on POST |
| **Prepared Statements** | All `mysqli` queries use `bind_param()` to prevent SQL injection |
| **Session Management** | Secure cookie params (`httponly`, `samesite`), `session_regenerate_id()` on login |
| **File Path Verification** | `fms_verify_file_path_access()` checks DB records before serving files |
| **Department Isolation** | `fms_hod_dept_exists_sql()` restricts data to user's department via `reg_tab.dept` join |
| **Input Sanitization** | `htmlspecialchars()` on all output, `mysqli_real_escape_string()` where needed |
| **URL Rewriting** | `.htaccess` rules for clean URLs and access control |
