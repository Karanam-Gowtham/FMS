# Individual Contribution

> **Note**: Customize the names and contribution split below to match your actual team. The descriptions below reflect the real modules and code in this FMS project.

---

## Team Structure

| Member | Role | Primary Responsibility |
|:---|:---|:---|
| **[Your Name]** | Full-Stack Developer & Lead | Architecture, Dashboard, Access Control, Notifications |
| **Team Member 2** | Backend Developer | Faculty Module, Upload Workflows, HOD Module |
| **Team Member 3** | Frontend Developer & DB Designer | UI/UX, Database Schema, Admin Module |

---

## My Individual Contribution

### 1. System Architecture & Design (Core Infrastructure)

**What I did:**
- Designed the overall **3-tier architecture** (Presentation → Application → Data) for the entire FMS project.
- Created the **modular directory structure** separating concerns into `modules/auth`, `modules/faculty`, `modules/dept_coordinator`, `modules/central`, `HOD/`, and `admin/`.
- Designed the **shared includes system** (`includes/` directory) to ensure DRY (Don't Repeat Yourself) principles across all modules.

**Files authored/designed:**
- `includes/connection.php` — Database connection with cloud deployment support
- `includes/session.php` — Secure session configuration
- `includes/constants.php` — Centralized constants (113 lines of project-wide definitions)
- Project directory structure and module organization

---

### 2. Role-Based Access Control System (`dept_scope.php`)

**What I did:**
- Designed and implemented the **entire access control layer** in `includes/dept_scope.php` (350 lines).
- Created 10+ security functions that enforce department-level data isolation:
  - `fms_table_owner_column()` — Maps 18 file tables to their respective owner columns
  - `fms_hod_dept_exists_sql()` — SQL fragment for HOD department filtering
  - `fms_faculty_owner_sql()` — Restricts data to the uploading faculty
  - `fms_download_scope_sql()` — Role-aware SQL scoping for download pages
  - `fms_download_row_allowed()` — Row-level permission check for delete/download
  - `fms_verify_file_path_access()` — File path verification against DB records
  - `fms_session_role_context()` — Session-to-role mapping
  - `fms_dashboard_row_in_scope()` — Dashboard row-level authorization

**Why it matters:**
This module is the **security backbone** of the entire system. Every page that displays or manipulates files routes through these functions to ensure users only see/modify data they're authorized to access.

---

### 3. Unified Dashboard (`dashboard.php`)

**What I did:**
- Built the **886-line unified dashboard** that serves all 6 user roles from a single page.
- Implemented the **UNION ALL query builder** that dynamically combines data from 16+ database tables.
- Created the `buildQuery()` helper function that generates role-aware SQL queries with proper department scoping.
- Implemented all **POST action handlers**:
  - **Approve** action with role-based status transitions
  - **Reject** action with reason logging to `rejection_history` table
  - **Re-upload** action with filesystem file replacement
- Designed the **status badge system** with color-coded visual indicators:
  - Amber (`#fff3cd`) → Pending Dept Coordinator
  - Blue (`#cfe2ff`) → Pending HOD
  - Green (`#d1e7dd`) → Accepted
  - Red (`#f8d7da`) → Rejected
- Created the **rejection history AJAX endpoint** (`?action=get_history`) with deduplication logic.
- Implemented **CSRF protection** on all POST operations.
- Built the **auto-migration system** that ensures `status` and `rejection_reason` columns exist in all file tables.

---

### 4. Notification System (`check_notifications.php`)

**What I did:**
- Built the **221-line notification counting system** that provides real-time badge counts.
- Replicated the dashboard's table coverage (16 tables) in a counting mode using `buildCountQuery()`.
- Integrated **email notification logic** with:
  - Department-based filtering (CSE department for Faculty role)
  - Email lookup from `admin_login` and `reg_tab` tables
  - **Throttling mechanism** (1 email per hour per user) using session-based timestamps
- Connected the notification endpoint to the **header's AJAX polling** system.

---

### 5. CSRF Protection Module (`csrf.php`)

**What I did:**
- Implemented the CSRF protection system with:
  - Token generation and session storage
  - `csrfField()` — Generates hidden input field for forms
  - `csrfValidate()` — Validates token on POST requests
- Integrated CSRF validation into `dashboard.php`, `login.php`, and critical form submissions.

---

### 6. Database Schema Design

**What I did:**
- Designed the **rejection workflow schema**:
  - `rejection_history` table for audit trail
  - `status` and `rejection_reason` columns across all file tables
  - `approval_roles` table defining the role hierarchy
- Designed the **auto-migration logic** in `dashboard.php` that ensures schema consistency.

---

### 7. Landing Page & Navigation (`index.php` + `header.php`)

**What I did:**
- Built the **responsive landing page** with:
  - Hero section with background image overlay
  - Glassmorphism header (`backdrop-filter: blur(8px)`)
  - Hamburger menu for mobile responsiveness
  - Dynamic login state awareness (Edit Profile button when logged in)
- Contributed to the **global header** (`includes/header.php`) navigation with:
  - Dashboard modal iframe integration
  - Notification badge polling
  - Role-aware navigation links

---

## Summary of Lines of Code Contributed

| Component | File | Lines | Contribution |
|:---|:---|:---:|:---|
| Access Control | `includes/dept_scope.php` | 350 | Full authorship |
| Dashboard | `dashboard.php` | 886 | Full authorship |
| Notifications | `check_notifications.php` | 221 | Full authorship |
| Constants | `includes/constants.php` | 113 | Full authorship |
| CSRF Module | `includes/csrf.php` | ~50 | Full authorship |
| Connection | `includes/connection.php` | 28 | Full authorship |
| Landing Page | `index.php` | 197 | Full authorship |
| Session Config | `includes/session.php` | ~30 | Full authorship |
| **Total** | | **~1,875** | |

---

## Technologies I Worked With

| Technology | How I Used It |
|:---|:---|
| PHP | Backend logic, session management, file handling, SQL query building |
| MySQL/MariaDB | Schema design, complex UNION queries, prepared statements |
| JavaScript | AJAX polling, modal management, dynamic form handling |
| HTML/CSS | Responsive layouts, glassmorphism effects, status badge system |
| Git | Version control, commit management |

---

## Key Technical Decisions I Made

1. **Vanilla PHP over Framework**: Chose vanilla PHP for zero-dependency deployment on institutional XAMPP servers.
2. **UNION ALL for Dashboard**: Used a single massive UNION ALL query across 16+ tables instead of separate API calls — simpler code, single DB round-trip.
3. **dept_scope.php as Security Layer**: Centralized all access control in one file rather than scattering checks across every page — easier to audit and maintain.
4. **Session-based Role Detection**: Used different session variable names per role (`username`, `a_username`, `h_username`, etc.) for simple role detection without a separate role column.
5. **Auto-Migration in Dashboard**: The dashboard checks and adds missing columns on load, ensuring backward compatibility when new features are added.
