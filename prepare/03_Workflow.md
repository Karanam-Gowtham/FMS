# Workflow

## 1. Overall System Flow

```
┌────────────┐     ┌─────────────┐     ┌──────────────┐     ┌──────────────┐
│   Landing  │────▶│   Login     │────▶│  Role-Based  │────▶│  Module      │
│   Page     │     │  (Auth)     │     │  Routing     │     │  Dashboard   │
│ index.php  │     │ login.php   │     │              │     │              │
└────────────┘     └─────────────┘     └──────────────┘     └──────────────┘
                         │                    │
                    Session created      Detects role from
                    with role-specific   session variables
                    variable             and redirects
```

---

## 2. User Authentication Flow

### 2.1 Faculty Login
```
User visits → index.php (Landing Page)
    │
    ├── Clicks "Login" in header navigation
    │
    ▼
modules/auth/login.php
    │
    ├── Enters: User ID + Password + Department
    ├── CSRF token validated
    ├── Query: SELECT * FROM reg_tab WHERE userid=? AND password=? AND dept=?
    │
    ├── ✅ Success:
    │   ├── INSERT INTO login_pg (audit log)
    │   ├── session_regenerate_id(true)
    │   ├── $_SESSION['username'] = userid
    │   └── Redirect → modules/faculty/acd_year.php
    │
    └── ❌ Failure:
        └── Alert: "Wrong User ID, Password or Department!"
```

### 2.2 HOD Login
```
User visits → HOD/HOD_lg.php
    │
    ├── Enters: Username + Password + Department
    ├── Query: SELECT * FROM reg_hod WHERE userid=? AND password=? AND department=?
    │
    ├── ✅ Success:
    │   ├── $_SESSION['h_username'] = userid
    │   ├── $_SESSION['dept'] = department
    │   └── Redirect → HOD/main_admin.php
    │
    └── (Special case: h_username='central' → Central Coordinator role)
```

### 2.3 Other Logins
- **Dept Coordinator**: `admin/dept_co_lg.php` → sets `$_SESSION['a_username']`
- **Junior Assistant**: `admin/jr_assist_lg.php` → sets `$_SESSION['j_username']`
- **Central Coordinator**: `modules/central/c_login.php` / `c_login_n.php` → sets `$_SESSION['c_cord']` or `$_SESSION['c_username']`
- **Admin**: `admin/admins.php` → sets `$_SESSION['admin']`

---

## 3. Document Submission & Approval Lifecycle

This is the **core workflow** of the entire system:

```
┌──────────────────────────────────────────────────────────────────┐
│                    DOCUMENT LIFECYCLE                             │
│                                                                  │
│   ┌──────────┐                                                   │
│   │ FACULTY  │                                                   │
│   │ uploads  │──────┐                                            │
│   │ document │      │                                            │
│   └──────────┘      ▼                                            │
│              ┌──────────────┐                                    │
│              │ Status:      │                                    │
│              │ Pending HOD  │                                    │
│              └──────┬───────┘                                    │
│                     │                                            │
│                     ▼                                            │
│              ┌──────────────┐      ┌──────────────────┐          │
│              │   HOD        │──NO──▶│ Status:          │          │
│              │   Reviews    │      │ Rejected by HOD  │──────┐   │
│              └──────┬───────┘      │ + Reason logged  │      │   │
│                     │              └──────────────────┘      │   │
│                    YES                                       │   │
│                     │                                        │   │
│                     ▼                                        │   │
│              ┌─────────────────────┐                         │   │
│              │ Status:             │                         │   │
│              │ Pending Dept Coord  │                         │   │
│              └──────┬──────────────┘                         │   │
│                     │                                        │   │
│                     ▼                                        │   │
│              ┌──────────────┐      ┌──────────────────────┐  │   │
│              │ DEPT COORD   │──NO──▶│ Status:              │  │   │
│              │ Reviews      │      │ Rejected by          │  │   │
│              └──────┬───────┘      │ Dept Coordinator     │──┤   │
│                     │              │ + Reason logged      │  │   │
│                    YES             └──────────────────────┘  │   │
│                     │                                        │   │
│                     ▼                                        │   │
│              ┌──────────────┐                                │   │
│              │ Status:      │                                │   │
│              │ ACCEPTED ✅  │                                │   │
│              └──────────────┘                                │   │
│                                                              │   │
│              ┌──────────────────────────────────────────┐    │   │
│              │          RE-UPLOAD LOOP                   │◀───┘   │
│              │                                          │         │
│              │  Faculty sees rejection reason on        │         │
│              │  dashboard → Re-uploads corrected file   │         │
│              │  → Status resets to "Pending Dept Coord" │         │
│              └──────────────────────────────────────────┘         │
│                                                                  │
└──────────────────────────────────────────────────────────────────┘
```

### Status Transitions Table

| Current Status | Action | By Role | New Status |
|:---|:---|:---|:---|
| Pending HOD | Approve | HOD | Pending Dept Coordinator |
| Pending HOD | Reject | HOD | Rejected by HOD |
| Pending Dept Coordinator | Approve | Dept Coordinator / Jr Assistant | Accepted |
| Pending Dept Coordinator | Reject | Dept Coordinator / Jr Assistant | Rejected by Dept Coordinator |
| Rejected by HOD | Re-upload | Faculty | Pending Dept Coordinator |
| Rejected by Dept Coordinator | Re-upload | Faculty | Pending Dept Coordinator |

> **Special case**: For `dept_files` (department-level files), HOD approval directly sets status to **Accepted** (skips Dept Coordinator step since the Dept Coordinator is the uploader).

---

## 4. Faculty Upload Workflow (Detailed)

```
Faculty logged in → modules/faculty/acd_year.php
    │
    ├── Select Academic Year (from `academic_year` table)
    ├── Select Criteria Category:
    │   ├── Criteria 1-7 → General file upload
    │   ├── Conferences → conference.php
    │   ├── Publications → published.php
    │   ├── FDPs Attended → fdps.php
    │   ├── FDPs Organized → fdps_org.php
    │   ├── Patents → patents.php
    │   └── Student Activities → student_act.php
    │
    ▼
Upload Form (example: conference.php)
    │
    ├── Fill metadata:
    │   ├── Paper Title
    │   ├── From Date / To Date
    │   ├── Organised By
    │   ├── Location
    │   ├── Paper Type (National/International)
    │   └── Year
    │
    ├── Attach files:
    │   ├── Certificate (PDF/Image)
    │   └── Paper File (PDF)
    │
    ├── Files moved to: uploads/<category>/<filename>
    ├── Metadata inserted into: conference_tab
    ├── Status set to: "Pending HOD"
    │
    └── Faculty can track status on dashboard.php
```

---

## 5. Dashboard Workflow

The unified `dashboard.php` serves all roles with role-aware content:

```
dashboard.php loaded
    │
    ├── 1. Detect role from session variables
    ├── 2. Build UNION ALL query across 16+ tables
    ├── 3. Apply role-based filters:
    │   ├── Faculty → Own files only (pending/rejected)
    │   ├── HOD → Dept files with status "Pending HOD"
    │   ├── Dept Coord → Dept files with "Pending Dept Coordinator"
    │   └── Central → View-only (no approval actions)
    │
    ├── 4. Display results in table with:
    │   ├── File name (clickable link)
    │   ├── Uploader username
    │   ├── Description
    │   ├── Upload date
    │   ├── Source table
    │   ├── Status badge (color-coded)
    │   └── Action buttons (Approve / Reject / Re-upload)
    │
    └── 5. Handle POST actions:
        ├── Approve → Update status
        ├── Reject → Update status + log to rejection_history
        └── Re-upload → Replace file + reset status
```

---

## 6. Notification Flow

```
Browser (header.php)
    │
    ├── Every 30 seconds: AJAX GET → check_notifications.php
    │
    ▼
check_notifications.php
    │
    ├── Detect role from session
    ├── Count pending items across 16+ tables (same tables as dashboard)
    ├── Apply role-based counting rules
    ├── (Optional) Send email notification if:
    │   ├── Count > 0
    │   ├── User is Faculty in CSE department
    │   ├── Email found in reg_tab or admin_login
    │   └── Last email was sent > 1 hour ago (throttling)
    │
    └── Return JSON: { "count": N }
         │
         ▼
    Header badge updated with count
    (Dashboard modal can be opened from header)
```

---

## 7. Bulk Download & PDF Merge Workflow

```
HOD/Admin navigates to download page
(e.g., admin/download.php or HOD/hod_fac_download.php)
    │
    ├── Select: Academic Year + Criteria + Sub-Criteria
    ├── View filtered list of files (scoped by dept/role)
    │
    ├── Option 1: Download Individual File
    │   └── Direct file download
    │
    ├── Option 2: Export to Excel
    │   └── Server generates .xls with file metadata
    │
    ├── Option 3: Select files → Merge PDFs
    │   ├── Client-side: pdf-lib fetches & merges selected PDFs
    │   ├── Merged PDF sent to server via POST
    │   └── admin/save_merged_pdf.php saves to uploads/merged/
    │
    └── Option 4: Bulk Delete (Admin only)
        └── Delete selected file records and filesystem files
```

---

## 8. Central Coordinator Flow

```
Central Coordinator logs in
    │
    ├── modules/central/c_login.php → Event-based navigation
    │   ├── NAAC
    │   ├── NBA
    │   ├── NCC
    │   ├── Sports
    │   └── Clubs
    │
    ├── Upload files: c_upload.php
    │   ├── Select Event type
    │   ├── Select Academic Year
    │   ├── Enter event details (club name, event name)
    │   ├── Upload file + up to 4 photos
    │   └── Data saved to: central_files table
    │
    ├── View AQAR files: c_aqar_files.php
    └── Download files: c_down_files.php
```
