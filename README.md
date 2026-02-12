# Faculty Management System (FMS)

## 📌 Project Overview
The **Faculty Management System (FMS)** is a comprehensive web-based application designed to streamline the management of faculty-related data and administrative processes within an educational institution. It serves as a centralized platform for storing, retrieving, and managing documentation related to:
- **Academic Activities:** Publications, FDPs, Student Activities.
- **Accreditation:** NAAC/NBA criteria compliance.
- **Faculty Achievements:** Awards, recognitions, and professional development.

This system automates the collection of data required for compliance reports, annual quality assurance, and departmental audits, replacing manual paper-based workflows.

---

## 🚀 Key Features

### 1. 🔐 Role-Based Access Control (RBAC)
The system provides secure, role-specific dashboards for different user types:
*   **Faculty**: Upload documents, view status of uploads, manage profile.
*   **Department Coordinator (Dept Co-ordinator)**: Review faculty uploads, approve/reject files, manage department data.
*   **Head of Department (HOD)**: Oversee department activities, generate reports, view analytics.
*   **Central Coordinator**: Manage institution-wide criteria, central files, and overall accreditation data.
*   **Administrator**: System configuration, user management, and high-level oversight.

### 2. 📄 comprehensive File Management
*   **Criteria Mapping**: Uploads are linked to specific NAAC/NBA criteria (e.g., 1.1.1, 1.2.1) for easy retrieval during inspections.
*   **Academic Year Tracking**: All data is organized by academic year (e.g., 2022-23).
*   **Status Tracking**: Files go through a workflow (Pending -> Approved/Rejected).
*   **Bulk Downloads**: Capability to download all files for a specific criteria or department.

### 3. 📊 Activity Tracking Modules
*   **Publications**: Journal publications, Conference papers, Patents.
*   **FDPs (Faculty Development Programs)**: Workshops attended or organized by faculty.
*   **Student Activities**: Projects, internships, and events supervised by faculty.
*   **Books/Chapters**: Book publications and book chapters.

### 4. 🛠️ Utility Tools
*   **PDF Merger**: Integrated tool to combine multiple PDF documents.
*   **Dynamic Reporting**: Generate reports based on criteria, year, or department.

---

## 📂 Project Structure & Components

The project is organized into modular directories to separate concerns and user roles.

### 1. 📁 Root Directory
*   `index.php`: The main entry point and landing page of the application.
*   `dashboard.php`: The central dashboard logic that routes users based on their role.
*   `check_notifications.php`: Handles notification logic for pending tasks.
*   `project_introduction.txt`: High-level summary of the project.

### 2. 📁 `modules/` (Core Logic)
This directory contains the specific logic for different user roles:

*   **`modules/auth/`**: Authentication scripts (login/logout processing).
    *   `login.php`: Handles user login requests.
*   **`modules/faculty/`**: Faculty-specific features.
    *   `criteria.php`: Interface for selecting accreditation criteria to upload files against.
    *   `upload.php`: Generic file upload handler.
    *   Specific upload handlers (e.g., `up5.1.1&2.php`) for complex criteria.
*   **`modules/dept_coordinator/`**: Department Coordinator workflow.
    *   `dept_files.php`: View files submitted by faculty in their department.
    *   `approve_file.php`: Logic to approve a pending file.
    *   `reject_file.php`: Logic to reject a file with comments.
*   **`modules/central/`**: Central Coordinator workflow.
    *   `c_aqar_files.php`: Manage institution-level AQAR files.
    *   `c_down_files.php`: Download consolidated files.

### 3. 📁 `HOD/` (Head of Department)
Specific functionalities for HODs:
*   `HOD_lg.php`: HOD Login page.
*   `hod_faculty_files.php`: View all files uploaded by faculty in the department.
*   `files_view_fac.php`: Detailed view of specific faculty files.
*   `Add_academic_year.php`: Utility to manage academic years.
*   `approve.php` / `reject.php`: Approval workflow actions.

### 4. 📁 `admin/` (System Administration)
*   `admins.php`: Dashboard for system administrators.
*   `criteria_a.php`: Manage the list of accreditation criteria.
*   `full_report.php`: Generate comprehensive system reports.

### 5. 📁 `includes/` (Shared Utilities)
*   `connection.php`: Database connection configuration (`project-fms`).
*   `header.php` / `footer.php`: Reusable UI components.
*   `session.php`: Session management and security headers.
*   `csrf.php`: Cross-Site Request Forgery protection.

### 6. 📁 `database/`
*   `project-fms.sql`: The main SQL dump file to set up the database schema.

### 7. 📁 `assets/`
*   Contains CSS, JavaScript, images, and third-party libraries (e.g., Bootstrap, FontAwesome).

---

## 💾 Database Schema

The application uses a MySQL database (`project-fms`). Key tables include:

*   **User Management**:
    *   `login_pg`: Stores user credentials for Faculty.
    *   `admin_login`: Credentials for Administrators.
    *   `hod_login` (if applicable): Credentials for HODs.
*   **Criteria Data**:
    *   `criteria`: Master list of NAAC/NBA criteria descriptions and IDs.
*   **File Data**:
    *   `files`: General file uploads linked to criteria.
    *   `conference_tab`: Conference records.
    *   `fdps_tab`: FDP records.
    *   `patents_tab`: Patent records.
*   **Workflow**:
    *   Status columns in file tables (e.g., `status = 'Pending'`, `rejection_reason`).

---

## ⚙️ Installation & Setup Guide

### 1. Prerequisites
*   **Web Server**: Apache (XAMPP, WAMP, or MAMP recommended).
*   **PHP**: Version 7.4 or higher.
*   **Database**: MySQL / MariaDB.

### 2. Database Setup
1.  Open **phpMyAdmin** (e.g., `http://localhost/phpmyadmin`).
2.  Create a new database named `project-fms`.
3.  Import the SQL file located at:
    `e:\set\xampp\htdocs\mini\FMS\database\project-fms.sql`

### 3. Application Configuration
1.  Place the project folder (`FMS`) in your server's root directory (`htdocs` or `www`).
2.  Open `includes/connection.php` and verify the database credentials:
    ```php
    $conn = mysqli_connect("localhost", "root", "", "project-fms");
    ```
    *Update the username (default: `root`) and password (default: empty) if your local setup differs.*

### 4. Running the Project
1.  Start `Apache` and `MySQL` in XAMPP control panel.
2.  Open your browser and visit:
    `http://localhost/mini/FMS/` (Adjust path based on your folder name).

---

## 🔄 User Workflow Example (File Upload)

1.  **Faculty Login**: User logs in with faculty credentials.
2.  **Select Criteria**: User navigates to "Criteria", selects an Academic Year and specific Criteria (e.g., 1.1.1).
3.  **Upload**: User uploads a PDF proof.
4.  **Notification**: The system marks the file as "Pending Dept Coordinator".
5.  **Review**: Dept Coordinator logs in, sees the pending file.
6.  **Action**:
    *   **Approve**: File status updates to "Approved".
    *   **Reject**: Coordinator provides a reason. Status updates to "Rejected". Faculty sees the rejection and can re-upload.

---

## ✨ Developed By
**FMS Team** - Google Deepmind Advanced Agentic Coding
