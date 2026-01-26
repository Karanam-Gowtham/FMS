# Database Documentation - FMS (Faculty Management System)

## Database Overview
**Database Name**: `project-fms`
**System Purpose**: To manage faculty data, research activities, student achievements, and generate reports for accreditation bodies like NAAC/NBA.

## Table Dictionary & Roles

### 1. User Management & Authentication
These tables handle login credentials and profile information for different user roles.
*   **`admin_login`**: Stores credentials for system administrators (Note: Some admin logins currently use hardcoded checks in `c_login_n.php`).
*   **`reg_tab`**: The master Faculty Profile table. Contains detailed personal and professional info (Designation, Qualification, Date of Joining, Aadhar, PAN, Experience, etc.).
*   **`login_pg`**: Logs successful faculty logins.
*   **`reg_central_cord`**: Credentials for Central Coordinators (manage institution-level data).
*   **`reg_cri_cord`**: Credentials for Criteria Coordinators (manage specific NAAC criteria).
*   **`reg_dept_cord`**: Credentials for Department Coordinators (manage department-level data).

### 2. Master Configuration
*   **`academic_year`**: Defines valid academic cycles (e.g., "2022-23", "2023-24") used across the system for filtering.
*   **`criteria` / `criteria1`**: Lookup tables containing standard NAAC/NBA criteria descriptions (e.g., "1.1.1 - Curriculum Design"). Used to populate dropdowns and label uploads.

### 3. Faculty Research & Professional Activities
Tables tracking individual faculty achievements.
*   **`published_tab`**: Journal publications (Scopus, SCI, Impact Factor, etc.).
*   **`conference_tab`**: Conference papers and participation details.
*   **`patents_table`**: Patents filed or granted.
*   **`fdps_tab`**: Faculty Development Programs (FDPs) attended by faculty.
*   **`fdps_org_tab`**: FDPs organized by the faculty/department.

### 4. Student Activities & Achievements
Tables tracking student performance under faculty supervision.
*   **`s_journal_tab`**: Journals published by students.
*   **`s_conference_tab`**: Conferences attended by students.
*   **`s_events`**: Participation in various technical or non-technical events.
*   **`s_bodies`**: Membership or activities in professional bodies (ISTE, CSI, etc.).

### 5. File Management & Accreditation Proofs
These tables store paths to uploaded documents (PDFs), linking them to specific criteria.
*   **`files`**: General repository for file uploads, linked to a specific criteria number.
*   **`a_files`**: Department-level uploads.
*   **`a_cri_files`**: Uploads managed by Criteria Coordinators.
*   **`central_files`**: Institution-level files (NCC, NSS, Sports, Clubs) including event photos.
*   **`a_c_files`**: likely "Agency Central" files, storing descriptions and paths.
*   **`dc_up_files`**: Files uploaded by Department Coordinators.

### 6. Specific NAAC Criterion 5 (Student Support & Progression)
Granular tables created for specific reports in Criterion 5.
*   **`files5_1_1and2`**: Scholarships and Freeships data.
*   **`files5_1_3`**: Capacity building and skill enhancement initiatives.
*   **`files5_1_4`**: Guidance for competitive exams and career counseling.
*   **`files5_2_1`**: Student placement records.
*   **`files5_2_2`**: Student progression to higher education.
*   **`files5_2_3`**: Students qualifying in state/national exams (GATE, GRE, etc.).
*   **`files5_3_1`**: Awards/medals in sports and cultural activities.
*   **`files5_3_3`**: Sports and cultural events organized by the institution.

### 7. Utilities
*   **`contact_form`**: Stores inquiries submitted via the "Contact Us" page.

---

## Authentication & Data Flow

This section details how users are authenticated and where their data is stored based on their roles.

### 1. User Authentication Mapping
The system (`c_login_n.php`) behaves as a central gateway, routing authentication to different tables based on the selected "Designation".

| User Role | Authentication Table | Login Script | Session Variable |
| :--- | :--- | :--- | :--- |
| **Faculty** | `reg_tab` | `c_login_n.php` / `login.php` | `$_SESSION['username']` |
| **Department Coordinator** | `reg_dept_cord` | `c_login_n.php` | `$_SESSION['a_username']` |
| **Central Coordinator** | `reg_central_cord` | `c_login_n.php` | `$_SESSION['c_username']` |
| **Criteria Coordinator** | `reg_cri_cord` | `c_login_n.php` | `$_SESSION['cri_username']` |
| **HOD** | *Hardcoded* | `c_login_n.php` | `$_SESSION['h_username']` |
| **Admin** | *Hardcoded* | `c_login_n.php` | `$_SESSION['admin']` |

*> Note: `c_login.php` also exists for specific event-based logins (e.g., NCC, Sports) using hardcoded credentials.*

### 2. File Upload Mapping
Different users have write access to different tables to maintain data segregation.

| User Role | Input Script | Target Database Table | Key Attributes Stored |
| :--- | :--- | :--- | :--- |
| **Faculty** | `upload.php` | **`files`** | `criteria_no`, `academic_year`, `branch`, `file_path` |
| **Department Coordinator** | `dc_up_files.php` | **`dc_up_files`** | `file_type`, `acd_year`, `Main_file_type` (event) |
| **Central Coordinator** | `c_upload.php` | **`central_files`** | `event_name`, `club_name`, `photo1`...`photo4` |
| **Criteria Coordinator** | `admin/upload_cri.php` | **`a_cri_files`** | `criteria_no`, `academic_year`, `Faculty_name` |

---

## User Roles & Permissions

This section outlines what each user role can access, view, and modify within the FMS project.

### 1. Faculty
*   **Access**: Personal Dashboard (`criteria.php`).
*   **Capabilities**:
    *   **Upload**: Can upload documents/proofs for specific accreditation criteria (e.g., Research Papers, FDP certificates).
    *   **View**: Can view templates/guidelines for criteria.
    *   **Manage**: Can view and track their own uploaded files ("My Uploads").
    *   **Restriction**: Cannot view files uploaded by other faculty members.

### 2. Department Coordinator
*   **Access**: Department Dashboard (`admin/criteria_a.php`).
*   **Capabilities**:
    *   **Upload**: Can upload department-level files.
    *   **View**: Can view/download files uploaded by faculty members within their department.
    *   **Manage**: Can manage department-specific criteria data.

### 3. Central Coordinator
*   **Access**: Central Dashboard (`admin/criteria_cent_a.php`).
*   **Capabilities**:
    *   **Upload**: Can upload institution-level event data (e.g., NCC, NSS, Sports events) including photos.
    *   **View**: Can view files related to central activities.
    *   **Scope**: Focuses on extracurricular and co-curricular activities that span multiple departments.

### 4. Criteria Coordinator
*   **Access**: Criteria Dashboard (`admin/criteria_cri_a.php`).
*   **Capabilities**:
    *   **Upload**: Can upload files specific to their assigned criteria.
    *   **View**: Can view all uploads related to their specific criteria across the institution.
    *   **Goal**: Ensuring compliance with specific NAAC/NBA criteria standards.

### 5. Head of Department (HOD)
*   **Access**: HOD Dashboard (`HOD/hod_faculty_files.php`).
*   **Capabilities**:
    *   **View Only**: primarily a reviewer role.
    *   **Monitor**: Can view all files uploaded by faculty in their department sorted by academic year and criteria.
    *   **No Upload**: Does not typically upload files directly via the HOD interface.

### 6. Admin
*   **Access**: Admin Dashboard (`HOD/acd_year_aa.php`).
*   **Capabilities**:
    *   **Configuration**: Can add new **Academic Years** to the system.
    *   **Supervision**: Has broad access to view data across the system.
    *   **System Management**: Manages high-level settings that affect all other users (e.g., valid reporting years).

---

## Entity-Relationship Overview

Although the database uses MyISAM/InnoDB features, it relies on **logical relationships** enforced by the application layer (PHP) rather than strict Foreign Key constraints in SQL.

### Key Connections:

1.  **User Linkage**:
    *   Almost all Activity and File tables (e.g., `published_tab`, `files`) contain a `username` or `userid` column.
    *   **Relationship**: `reg_tab.userid` (or `login_pg.userid`) ↔ `[Activity_Table].username`.
    *   **Purpose**: To identify "Who uploaded this file?" or "Who did this research?".

2.  **Academic Timeline**:
    *   **Relationship**: `academic_year.year` ↔ `[Any_Table].year` (or `academic_year` column).
    *   **Purpose**: To generate annual reports (e.g., "Show me all publications from 2022-23").

3.  **Accreditation Mapping**:
    *   **Relationship**: `criteria.Sub_no` (e.g., '1.1.1') ↔ `files.criteria_no` / `a_cri_files.criteria_no`.
    *   **Purpose**: To categorize every uploaded document under a specific accreditation standard.

## Schema Notes
*   **File Storage**: The database *does not* store actual files (BLOB). It stores **file paths** (VARCHAR) (e.g., `uploads/certificate.pdf`).
*   **Dates**: Dates are stored in multiple formats across tables (`DATE`, `DATETIME`, or `VARCHAR`). Standardizing this in future updates is recommended.
