# Faculty Management System (FMS) 🚀

A premium, role-based document management solution designed for **GMRIT** and higher education institutions. FMS streamlines the lifecycle of academic and administrative proofs, from faculty uploads to HOD approval and accreditation-ready consolidation (NAAC/NBA).

---

## What it does

- **Faculty** upload proofs (publications, FDPs, conferences, patents, student activities, placement/higher-education files, etc.) and track status.
- **Head of Department (HOD)** reviews items first (`Pending HOD`), then **Department Coordinator** or **Junior Assistant** (`Pending Dept Coordinator`).
- **Central** flows (NAAC, NBA, NCC, Sports, clubs, etc.) use `modules/central/` and `c_login_n.php` / `c_login.php` with event-based navigation.
- **Unified dashboard** (`dashboard.php`) lists pending work by role; the main header can open it in a modal iframe and polls `check_notifications.php` for a badge count.
- **Admin** area under `admin/` handles criteria uploads, bulk download/delete, and department entry via `admins.php`.
- **Access control helpers** in `includes/dept_scope.php` scope listings and file actions by **faculty ownership** or **uploader department** (`reg_tab.dept`), and gate **path-based file views** so users cannot open arbitrary `uploads/` URLs.

---

## Tech stack

| Layer | Details |
|--------|---------|
| Server | Apache (typical: XAMPP on Windows) |
| Language | PHP (7.4+ recommended; 8.x supported) |
| Database | MySQL / MariaDB, database name `project-fms` |
| DB API | `mysqli` with prepared statements in many paths |
| Sessions | PHP sessions; `includes/session.php` sets secure cookie flags when used |
| CSRF | `includes/csrf.php` — tokens on protected POST forms |
| Email | Optional notifications via `includes/send_email.php` (e.g. faculty CSE throttle in `check_notifications.php`) |

**Configuration:** edit `includes/connection.php` for DB host, user, password, database name, and `$base_url` (must match your deployed URL path, e.g. `http://localhost/mini/FMS/`).

---

## 🛠️ Technology Stack

| Layer | Technologies |
| :--- | :--- |
| **Backend** | PHP 7.4+ / 8.x (Core logic for maximum performance) |
| **Database** | MySQL / MariaDB (Project-FMS Schema) |
| **Frontend** | Vanilla JS, CSS3, Google Fonts (Inter/Roboto), Bootstrap, FontAwesome |
| **Libraries** | `pdf-lib` (Client-side PDF processing), `mysqli` (Prepared statements) |
| **Deployment** | Apache (XAMPP / Render / Linux VPS) |

---

## 📂 Project Architecture

```text
FMS/
├── dashboard.php             # Centralized Task Management (Role-aware)
├── includes/
│   ├── connection.php        # DB + base_url + session bootstrap + CSRF token seed
│   ├── session.php           # Cookie parameters
│   ├── csrf.php              # CSRF helpers
│   ├── dept_scope.php        # Table→owner column, dept/faculty SQL fragments, row scope, file_path checks
│   ├── constants.php         # Criteria labels and shared defines
│   ├── header.php            # Shared navigation (Central / Department / Dashboard modal)
│   └── send_email.php        # Mail helper
├── modules/
│   ├── auth/                 # login.php, logout.php, reg.php
│   ├── faculty/              # Academic year, criteria uploads, profiles, FDPS, etc.
│   ├── dept_coordinator/     # DC workflows, minutes, downloads
│   ├── central/              # Central logins and file flows (AQAR, events, uploads)
│   ├── jr_assistant/         # Junior assistant entry (e.g. jr_acd_year.php)
│   └── common/               # pdf_merger.php, view_file1.php, save_merged_pdf.php
├── HOD/                      # HOD pages, downloads, academic year tools, view_file*.php
├── admin/                    # admins.php, criteria_*.php, upload*.php, download.php, etc.
├── database/
│   └── project-fms.sql       # Schema dump (import for fresh install)
├── assets/                   # CSS, JS, images
└── _deprecated/              # Old copies; do not use for production paths
```

Root also contains **maintenance/debug scripts** (`migrate_*.php`, `debug_*.php`, `verify_schema.php`, etc.) — use only in development.

---

## Database

1. Create database **`project-fms`** in phpMyAdmin (or CLI).
2. Import **`database/project-fms.sql`**.
3. Adjust credentials in **`includes/connection.php`** if not using `root` with empty password.

Notable concepts:

- Multiple **file tables** (`files`, `files5_*`, `fdps_tab`, `conference_tab`, `published_tab`, `patents_table`, `dept_files`, student activity tables, etc.) with **`status`** and **`rejection_reason`** where applicable.
- **`rejection_history`** stores rejection audit rows (used from `dashboard.php`).
- **`academic_year`** and related tables drive year pickers across modules.

---

## Installation (quick)

1. Install **XAMPP** (or similar): Apache + MySQL + PHP.
2. Copy the project folder under `htdocs` (e.g. `htdocs/mini/FMS`).
3. Import **`database/project-fms.sql`** into **`project-fms`**.
4. Set **`includes/connection.php`** database settings and **`$base_url`** to match your URL.
5. Open **`http://localhost/mini/FMS/`** (adjust host and path).

---

## Security notes (operator awareness)

- **CSRF** is enforced on several POST flows (e.g. dashboard actions, some admin forms, central login form).
- **Dashboard** (`dashboard.php`) unions pending rows with **department-aware** filters for HOD / department coordinator / junior assistant (via `dept_scope.php`); approve/reject/re-upload checks the same row scope.
- **Bulk download / delete** on `admin/download.php` and **`HOD/hod_fac_download.php`** resolve the correct upload table per criteria, filter lists and Excel exports by role (**faculty** = own uploads; **HOD / DC / Jr** = uploaders in `reg_tab` for that department; **admin** and **central coordinator** sessions = unfiltered on those pages), and allow delete/download only for rows the user is allowed to see.
- **File viewing:** `admin/view_file.php` (with `file_path`), `HOD/view_file_hod.php`, `HOD/view_file.php` (faculty), and `modules/common/view_file1.php` resolve the path against the database and enforce the same ownership/dept rules (admin bypass where implemented). `a_files` lookups by `id` in `admin/view_file.php` are limited to the owning faculty unless `admin`.
- **Merged PDF POST target:** from `admin/download.php` use **`admin/save_merged_pdf.php`** (same directory). From **`HOD/hod_fac_download.php`** the client posts to **`../admin/save_merged_pdf.php`**.
- **Other download UIs** (`admin/download_cri.php`, `admin/download_cent.php`) use **`a_files` / `a_c_files`** — they are separate from the main `files` / `files5_*` flows; review those if you need the same dept/owner guarantees.
- Passwords are handled **as stored in the database** (plain text in typical legacy flows). Treat the DB as sensitive and restrict access; prefer HTTPS in production.

---

## 📝 Recent Version Notes
> [!NOTE]
> **Version Update (HEAD):** Reverted to commit `d127825` to stabilize the dashboard logic and ensure full compatibility with the new "View" button architecture.

---

## ⚖️ License
This project is developed for institutional use. Maintain the `includes/dept_scope.php` integrity when extending file tables to ensure security compliance. Update **`$base_url`** and any hardcoded paths (`/mini/FMS/` in `includes/header.php` iframe) when deploying to a different base path.
