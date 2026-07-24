# Architecture & Design

## 1. High-Level Architecture

The Faculty Management System follows a **3-Tier Web Architecture** deployed on an Apache/XAMPP server:

```
┌─────────────────────────────────────────────────────────┐
│                   PRESENTATION TIER                      │
│  (HTML5 / CSS3 / JavaScript / Bootstrap / FontAwesome)   │
│  Browser-side PDF processing (pdf-lib)                   │
└──────────────────────┬──────────────────────────────────┘
                       │  HTTP Requests
┌──────────────────────▼──────────────────────────────────┐
│                   APPLICATION TIER                        │
│  PHP 8.x (Vanilla Core — No Framework)                   │
│  ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌─────────────┐ │
│  │   Auth   │ │ Faculty  │ │   HOD    │ │   Admin     │ │
│  │  Module  │ │  Module  │ │  Module  │ │   Module    │ │
│  └──────────┘ └──────────┘ └──────────┘ └─────────────┘ │
│  ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌─────────────┐ │
│  │Dept Coord│ │ Central  │ │Jr Assist │ │   Common    │ │
│  │  Module  │ │  Module  │ │  Module  │ │  Utilities  │ │
│  └──────────┘ └──────────┘ └──────────┘ └─────────────┘ │
│  Includes: connection.php | session.php | dept_scope.php │
│            constants.php  | csrf.php    | header.php     │
└──────────────────────┬──────────────────────────────────┘
                       │  mysqli (Prepared Statements)
┌──────────────────────▼──────────────────────────────────┐
│                     DATA TIER                            │
│  MySQL / MariaDB (Database: project-fms)                 │
│  30+ Tables: reg_tab, files, fdps_tab, conference_tab,   │
│  published_tab, patents_table, dept_files, criteria,     │
│  academic_year, rejection_history, central_files, etc.   │
│                                                          │
│  File Storage: /uploads/ directory (Filesystem)          │
└─────────────────────────────────────────────────────────┘
```

---

## 2. Module Architecture

The project is organized into **6 functional modules** under the `modules/` directory, plus a top-level `HOD/` directory and `admin/` directory:

```
FMS/
├── index.php                    ← Landing page (entry point)
├── dashboard.php                ← Unified task management (all roles)
├── check_notifications.php      ← AJAX notification badge counter
│
├── modules/
│   ├── auth/                    ← Login, Logout, Registration
│   │   ├── login.php            (Faculty login with dept selection)
│   │   ├── logout.php           (Session destruction)
│   │   └── reg.php              (Faculty registration)
│   │
│   ├── faculty/                 ← Faculty upload & profile management
│   │   ├── acd_year.php         (Academic year + criteria selection)
│   │   ├── conference.php       (Conference paper uploads)
│   │   ├── published.php        (Journal publication uploads)
│   │   ├── fdps.php             (FDP attended uploads)
│   │   ├── fdps_org.php         (FDP organized uploads)
│   │   ├── patents.php          (Patent uploads)
│   │   ├── student_act.php      (Student activity uploads)
│   │   ├── edit_profile.php     (Faculty profile editing)
│   │   └── dashboard_profiles.php
│   │
│   ├── dept_coordinator/        ← Department coordinator workflows
│   │   ├── dc_acd_year.php      (Academic year management)
│   │   ├── department.php       (Department dashboard)
│   │   ├── dept_files.php       (Department file management)
│   │   ├── dc_up_files.php      (Upload files for department)
│   │   ├── dc_down_*.php        (Download pages for various categories)
│   │   ├── amc_meeting_minutes.php
│   │   ├── bos_meeting_minutes.php
│   │   └── dept_meeting_minutes.php
│   │
│   ├── central/                 ← Central coordinator flows
│   │   ├── c_login.php          (Central login)
│   │   ├── c_login_n.php        (Central login alternate)
│   │   ├── central_events.php   (Event management)
│   │   ├── c_upload.php         (Central file uploads)
│   │   ├── c_aqar_files.php     (AQAR file management)
│   │   ├── c_down_files.php     (Download by criteria)
│   │   └── cc_down_dc_files.php (Download DC files)
│   │
│   ├── jr_assistant/            ← Junior assistant data entry
│   │   └── jr_acd_year.php
│   │
│   └── common/                  ← Shared utilities
│       ├── pdf_merger.php       (Client-side PDF merge UI)
│       ├── save_merged_pdf.php  (Server-side merge handler)
│       ├── view_file1.php       (Secure file viewer)
│       ├── download_papers1.php (Bulk download)
│       ├── about.php            (About page)
│       └── contact.php          (Contact form)
│
├── HOD/                         ← HOD-specific pages
│   ├── HOD_lg.php               (HOD login)
│   ├── main_admin.php           (HOD main dashboard)
│   ├── hod_fac_download.php     (Faculty file downloads with merge)
│   ├── hod_down_*.php           (Download pages per category)
│   ├── files_fac.php / files_cor.php  (Faculty/coordinator file views)
│   ├── admin_criteria.php       (Criteria management)
│   ├── see_uploads.php          (View uploaded files)
│   └── view_file_hod.php       (Secure HOD file viewer)
│
├── admin/                       ← System administration
│   ├── admins.php               (User & department management)
│   ├── criteria_*.php           (Criteria CRUD operations)
│   ├── upload*.php              (Admin upload pages)
│   ├── download.php             (Bulk download with merge/export)
│   ├── my_uploads*.php          (Upload management)
│   └── save_merged_pdf.php      (PDF merge handler)
│
├── includes/                    ← Shared infrastructure
│   ├── connection.php           (DB connection + session bootstrap)
│   ├── session.php              (Cookie/session config)
│   ├── csrf.php                 (CSRF token helpers)
│   ├── dept_scope.php           (RBAC & department-level access control)
│   ├── constants.php            (Criteria labels, table names, statuses)
│   ├── header.php               (Global navigation header)
│   ├── send_email.php           (PHPMailer email helper)
│   └── PDFMerger.php            (Server-side PDF merge library)
│
├── assets/                      ← Static resources
│   ├── css/                     (24 stylesheet files)
│   ├── img/                     (Images and logos)
│   └── templates/               (HTML templates)
│
├── database/
│   └── project-fms.sql          (Full schema dump — 2500+ lines)
│
└── uploads/                     ← File storage directory
```

---

## 3. Database Design (ER Summary)

The database `project-fms` contains **30+ tables** organized into the following categories:

### 3.1 User/Role Tables
| Table | Purpose |
|:---|:---|
| `reg_tab` | Faculty registration (userid, password, name, dept, email) |
| `reg_dept_cord` | Department coordinator registration |
| `reg_hod` | HOD registration |
| `reg_jr_assistant` | Junior assistant registration |
| `admin_login` / `admin_reg` | Admin credentials |
| `login_pg` | Login audit log |
| `approval_roles` | Role definitions with ordering (FACULTY→DEPT_COORD→HOD→CENTRAL_COORD) |

### 3.2 File/Document Tables
| Table | Purpose |
|:---|:---|
| `files` | General criteria-based file uploads (Criteria 1–4, 6, 7) |
| `files5_1_1and2` | Scholarship/freeship records (Criterion 5.1.1 & 5.1.2) |
| `files5_1_3` | Capacity development records (Criterion 5.1.3) |
| `files5_1_4` | Career counselling records (Criterion 5.1.4) |
| `files5_2_1` | Placement details (Criterion 5.2.1) |
| `files5_2_2` | Higher education progression (Criterion 5.2.2) |
| `files5_2_3` | Qualifying exams (Criterion 5.2.3) |
| `files5_3_1` | Sports/cultural awards (Criterion 5.3.1) |
| `files5_3_3` | Sports/cultural events (Criterion 5.3.3) |
| `fdps_tab` | Faculty Development Programs (Attended) |
| `fdps_org_tab` | Faculty Development Programs (Organized) |
| `conference_tab` | Conference papers |
| `published_tab` | Journal publications |
| `patents_table` | Patent records |
| `dept_files` | Department-level files (meeting minutes, etc.) |
| `central_files` | Central coordinator files (events, clubs) |
| `a_files` / `a_c_files` / `a_cri_files` | Admin-uploaded criteria files |

### 3.3 Student Activity Tables
| Table | Purpose |
|:---|:---|
| `s_journal_tab` | Student journal papers |
| `s_conference_tab` | Student conference papers |
| `s_events` | Student event participation |
| `s_bodies` | Student professional body membership |

### 3.4 Support Tables
| Table | Purpose |
|:---|:---|
| `academic_year` | Academic year definitions (e.g., 2020-21, 2024-25) |
| `criteria` / `criteria1` | NAAC criteria definitions with descriptions |
| `rejection_history` | Audit trail for all rejections |
| `contact_form` | Contact form submissions |

---

## 4. Access Control Design

The system implements a layered access control mechanism through `includes/dept_scope.php`:

```
┌─────────────────────────────────────────────────┐
│              ACCESS CONTROL LAYERS               │
├─────────────────────────────────────────────────┤
│                                                  │
│  Layer 1: SESSION-BASED AUTHENTICATION           │
│  ├── $_SESSION['username']    → Faculty           │
│  ├── $_SESSION['a_username']  → Dept Coordinator  │
│  ├── $_SESSION['j_username']  → Jr Assistant       │
│  ├── $_SESSION['h_username']  → HOD / Central      │
│  └── $_SESSION['admin']       → Admin              │
│                                                  │
│  Layer 2: ROLE-BASED AUTHORIZATION               │
│  ├── fms_session_role_context()  → Detects role   │
│  ├── fms_download_scope_sql()   → SQL filtering   │
│  └── fms_dashboard_row_in_scope() → Row-level     │
│                                                  │
│  Layer 3: DEPARTMENT-LEVEL ISOLATION              │
│  ├── fms_hod_dept_exists_sql()  → Dept match      │
│  └── fms_faculty_owner_sql()    → Owner match      │
│                                                  │
│  Layer 4: FILE PATH VERIFICATION                 │
│  └── fms_verify_file_path_access() → Path check   │
│                                                  │
│  Layer 5: CSRF PROTECTION                        │
│  └── csrfValidate() on all POST operations        │
│                                                  │
└─────────────────────────────────────────────────┘
```

### Access Matrix

| Feature | Faculty | HOD | Dept Coord | Jr Assist | Central | Admin |
|:---|:---:|:---:|:---:|:---:|:---:|:---:|
| Upload own files | ✅ | ❌ | ❌ | ❌ | ✅ | ✅ |
| View own status | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ |
| Approve/Reject (Tier 1) | ❌ | ✅ | ❌ | ❌ | ❌ | ❌ |
| Approve/Reject (Tier 2) | ❌ | ❌ | ✅ | ✅ | ❌ | ❌ |
| View dept files | ❌ | ✅ | ✅ | ✅ | ❌ | ❌ |
| View all files | ❌ | ❌ | ❌ | ❌ | ✅ | ✅ |
| Manage users/criteria | ❌ | ❌ | ❌ | ❌ | ❌ | ✅ |
| Bulk download/merge | ❌ | ✅ | ✅ | ❌ | ✅ | ✅ |
| Re-upload on rejection | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ |
| Upload dept files | ❌ | ❌ | ✅ | ✅ | ❌ | ❌ |

---

## 5. Design Patterns Used

| Pattern | Application in FMS |
|:---|:---|
| **Modular Architecture** | Each role has its own directory/module with isolated pages |
| **Shared Includes** | Common DB, session, header, and security code in `includes/` |
| **UNION-based Dashboard** | Single `dashboard.php` queries 16+ tables via UNION ALL |
| **Table-Owner Mapping** | `fms_table_owner_column()` maps each table to its owner column |
| **Status State Machine** | Documents transition: `Pending HOD` → `Pending Dept Coordinator` → `Accepted` (or `Rejected by HOD`/`Rejected by Dept Coordinator` with re-upload loop) |
| **AJAX Polling** | `check_notifications.php` polled by header for badge count |
| **CSRF Token Protection** | Token generated per session, validated on every POST |
