# Challenges Faced

## 1. Multi-Table UNION Query for the Dashboard

### The Challenge
The dashboard needs to display pending files from **16+ different database tables** (files, fdps_tab, conference_tab, published_tab, patents_table, files5_1_1and2, files5_1_3, files5_1_4, files5_2_1, files5_2_2, s_journal_tab, s_conference_tab, s_events, s_bodies, dept_files, fdps_org_tab) in a single unified view. Each table has **different column names** for the same logical fields (e.g., `UserName` vs `username`, `file_path` vs `certificate_path` vs `paper_file`).

### The Problem
- No common interface across tables — each was designed independently.
- A simple `SELECT *` approach was impossible due to mismatched schemas.
- Joining all tables would be extremely complex and slow.

### The Solution
Built a `buildQuery()` helper function that accepts a column mapping array `[$id_col, $user_col, $desc_col, $date_col, $file_name_col, $file_path_col]` and aliases everything to uniform names (`id`, `username`, `description`, `uploaded_at`, `file_name`, `file_path`). All 16 queries are combined with `UNION ALL` and sorted by `uploaded_at DESC`.

### Lesson Learned
When designing a system from scratch, define a **consistent schema convention** upfront. If column names were standardized across all tables, the dashboard logic would have been 70% simpler.

---

## 2. Role-Based Access Control Across Multiple Tables

### The Challenge
Implementing department-level data isolation across 18+ file tables, where each table stores the "owner" in a differently-named column (`UserName`, `username`, or `Username` — note the case differences).

### The Problem
- PHP's `mysqli` is case-sensitive for column names.
- A faculty member from CSE should never see ECE's files.
- HOD needs to see all files from their department, but Faculty should only see their own.
- The mapping between uploader → department requires a JOIN with `reg_tab`.

### The Solution
Created `fms_table_owner_column()` — a static mapping of 18 tables to their owner column names. Built layered SQL fragment generators (`fms_hod_dept_exists_sql()`, `fms_faculty_owner_sql()`) that produce secure SQL `WHERE` clauses. Every page uses these functions, ensuring consistent security.

### Lesson Learned
Centralizing access control in a single module (`dept_scope.php`) rather than copy-pasting checks into each page was the right decision. Any future table additions only need one line added to the mapping.

---

## 3. File Re-upload Without Breaking References

### The Challenge
When a faculty member re-uploads a corrected file after rejection, the **database path must remain stable** so that all existing references (bookmarks, download links) continue to work.

### The Problem
- If we generate a new filename, all existing references break.
- If we overwrite in place, the old file content is lost.
- Different tables store file paths in different columns (`file_path`, `certificate`, `certificate_path`, `paper_file`, `patent_file`).
- Path prefixes vary: some tables store `../../uploads/...`, others store `../uploads/...`.

### The Solution
Created a `$table_schema_map` that maps each table to its path and name columns. On re-upload:
1. Fetch the existing DB path.
2. Resolve the filesystem path by stripping `../../` prefixes.
3. Delete the old file at the resolved path.
4. `move_uploaded_file()` the new file to the same location.
5. Keep the same DB path so all references remain stable.
6. Reset status to "Pending Dept Coordinator" for Faculty re-uploads.

### Lesson Learned
File path normalization is non-trivial when different modules store paths with different relative prefixes. A dedicated path normalization utility (`fms_normalize_uploads_relative_path()`) saved us from path-related bugs.

---

## 4. Session-Based Role Detection (Multiple Session Variables)

### The Challenge
The system uses **different session variable names** for different roles:
- `$_SESSION['username']` → Faculty
- `$_SESSION['a_username']` → Dept Coordinator
- `$_SESSION['j_username']` → Junior Assistant
- `$_SESSION['h_username']` → HOD / Central Coordinator
- `$_SESSION['admin']` → Admin
- `$_SESSION['c_cord']`, `$_SESSION['c_username']`, `$_SESSION['cri_username']` → Various Central roles

### The Problem
- This is **legacy design** — ideally a single `$_SESSION['role']` variable would suffice.
- The same role detection logic must be replicated in `dashboard.php`, `check_notifications.php`, `dept_scope.php`, and `header.php`.
- Forgetting to check one variable leads to security gaps.

### The Solution
Created `fms_session_role_context()` in `dept_scope.php` that encapsulates all the session-checking logic into a single function returning a normalized `{role, user_id, dept}` array. New pages use this function instead of writing their own detection logic.

### Lesson Learned
Refactoring legacy session management into a central function prevents drift and ensures consistency. However, fully replacing all old code would require touching 50+ files — a risk we managed by keeping backward compatibility.

---

## 5. Dashboard Iframe in Header (Cross-Context Sessions)

### The Challenge
The global header (`includes/header.php`) opens the dashboard in a **modal iframe** for quick access. However, iframes can have session issues due to browser cookie policies.

### The Problem
- Modern browsers (Chrome 80+) enforce `SameSite=Lax` by default for cookies.
- An iframe is considered a cross-context request, potentially not sending the session cookie.
- Without the session cookie, the dashboard iframe shows "Not logged in."

### The Solution
- Configured `session.php` with explicit cookie parameters including `SameSite`.
- The dashboard supports a `?mode=iframe` parameter that hides the header when embedded (avoiding nested headers).
- Ensured `session_start()` is called identically in both the parent page and the iframe page.

### Lesson Learned
Iframe-based UIs are convenient but introduce browser security policy complications. Testing across Chrome, Firefox, and Edge was essential.

---

## 6. PDF Merging — Client-Side vs Server-Side

### The Challenge
HODs and Admins need to merge multiple PDF files (10–50 documents) into a single accreditation-ready PDF for submission.

### The Problem
- Server-side merging with PHP requires loading all PDFs into memory — risky for large files.
- Client-side merging requires downloading all files to the browser first — slow for large batches.
- Some uploaded files are images, not PDFs — need to handle gracefully.

### The Solution
Used a **hybrid approach**:
1. **Client-side**: `pdf-lib` (JavaScript) handles PDF fetching and merging in the browser. This offloads CPU-intensive work from the server.
2. **Server-side**: `PDFMerger.php` serves as a fallback. `save_merged_pdf.php` receives the merged PDF blob from the client and saves it to `uploads/merged/`.
3. Non-PDF files are skipped during merge with user notification.

### Lesson Learned
Client-side PDF processing is faster and more scalable than server-side, but requires careful error handling for corrupt or password-protected files.

---

## 7. Auto-Migration for Schema Evolution

### The Challenge
As the project evolved, new columns (`status`, `rejection_reason`) needed to be added to all 16+ file tables. Not all development environments had the latest schema.

### The Problem
- Manually running ALTER TABLE on 16 tables per environment is error-prone.
- If a column is missing, the dashboard UNION ALL query breaks entirely.
- No formal migration tool (like Laravel's migrations) was available.

### The Solution
Added auto-migration logic at the top of `dashboard.php`:
```php
foreach ($tables as $table) {
    $check_col = $conn->query("SHOW COLUMNS FROM $table LIKE 'status'");
    if ($check_col->num_rows == 0) {
        $conn->query("ALTER TABLE $table ADD COLUMN status VARCHAR(50) DEFAULT 'Pending HOD'");
    }
    // Same for rejection_reason
}
```
Also auto-creates the `rejection_history` table if it doesn't exist.

### Lesson Learned
Auto-migration at application startup is a pragmatic solution for small teams without formal migration tooling. However, it should only be used for additive changes (adding columns/tables), never for destructive ones.

---

## 8. Notification Email Throttling

### The Challenge
The notification system (`check_notifications.php`) is polled every 30 seconds by every logged-in user's browser. If we sent an email on every poll cycle, users would receive hundreds of emails.

### The Problem
- 30-second poll × 120 cycles/hour = 120 potential emails per hour per user.
- No persistent "last sent" storage — sessions are per-browser.

### The Solution
Implemented session-based throttling:
```php
$session_key = "last_email_sent_" . $role . "_" . $user_id;
$throttle_time = 3600; // 1 hour
$last_sent = $_SESSION[$session_key] ?? 0;
if (time() - $last_sent > $throttle_time) {
    sendNotificationEmail($email, $recipientName, $count);
    $_SESSION[$session_key] = time();
}
```

### Lesson Learned
Session-based throttling works for single-browser scenarios but doesn't prevent duplicate emails if a user is logged in on multiple devices. A database-based throttle (last_email_sent column in user table) would be more robust for production.

---

## 9. Handling Legacy Password Storage

### The Challenge
The system stores passwords in **plain text** in the database — a known security risk inherited from the initial design.

### The Problem
- Changing to hashed passwords would break all existing user accounts.
- 50+ existing users would need password resets.
- Multiple login pages check passwords differently.

### The Solution (Pragmatic)
- Documented the limitation prominently in README.md.
- Recommended HTTPS for production deployments.
- Restricted database access as a compensating control.
- Planned password hashing as a future enhancement (using `password_hash()` / `password_verify()`).

### Lesson Learned
Security should be built in from day one. Retrofitting security into a legacy system is always harder than building it correctly initially.

---

## Summary of Challenges

| # | Challenge | Difficulty | Resolution |
|:---:|:---|:---:|:---|
| 1 | Multi-table UNION dashboard | High | Column mapping + buildQuery() helper |
| 2 | RBAC across 18+ tables | High | Centralized dept_scope.php |
| 3 | File re-upload path stability | Medium | Table schema map + path normalization |
| 4 | Legacy session variables | Medium | fms_session_role_context() wrapper |
| 5 | Iframe session issues | Medium | Cookie config + iframe mode |
| 6 | PDF merge scalability | Medium | Hybrid client+server approach |
| 7 | Schema evolution | Medium | Auto-migration on page load |
| 8 | Email throttling | Low | Session-based timestamps |
| 9 | Plain-text passwords | Low (risk: high) | Documented + compensating controls |
