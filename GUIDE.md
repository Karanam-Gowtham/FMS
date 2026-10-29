# 🎓 GMRIT File Management System (FMS)
# 📱 Mobile-Friendly Deployment & Operations Guide

> **Target Audience:** College deployment team, evaluators, and system administrators.  
> **Status:** Production Ready | 0 Bugs | 26/26 Checks Passed | Clean Database Prepared.

---

## 📑 Quick Navigation (Tap to Jump)

1. [🔑 Master Login Credentials Cheat-Sheet](#-1-master-login-credentials-cheat-sheet)
2. [🛡️ What Can the System Administrator Do?](#️-2-what-can-the-system-administrator-do)
3. [📂 Student Activities & NAAC Criterion 5 Workflow](#-3-student-activities--naac-criterion-5-workflow)
4. [🖥️ Step-by-Step Desktop Server Setup](#️-4-step-by-step-desktop-server-setup)
5. [🗄️ Server Database Setup & Synchronization](#️-5-server-database-setup--synchronization)
6. [🌐 College LAN & Wi-Fi Configuration](#-6-college-lan--wi-fi-configuration)
7. [📱 Accessing the System from Your Mobile Phone](#-7-accessing-the-system-from-your-mobile-phone)
8. [🔍 Pre-Flight Verification Script (CLI)](#-8-pre-flight-verification-script-cli)
9. [📱 Step-by-Step Mobile Demo Script for Evaluators](#-9-step-by-step-mobile-demo-script-for-evaluators)
10. [🛠️ Emergency On-Site Troubleshooting](#️-10-emergency-on-site-troubleshooting)

---

## 🔑 1. Master Login Credentials Cheat-Sheet

All pre-seeded institutional accounts use the unified default password:  
👉 **`Admin@123`**

> 💡 **Mobile Quick Login Tip:**  
> On mobile and desktop, you don't need to type the full `@gmrit.edu.in`. Simply typing the handle (e.g. `admin`, `hod.ece`, `faculty.civil`, `coord.aids`) in the email field will log you in directly!

---

### 🌐 Central & Campus-Wide Roles
| Role | Full Name | Quick Handle | Login Email | Scope |
| :--- | :--- | :--- | :--- | :--- |
| **System Administrator** | System Administrator | `admin` | `admin@gmrit.edu.in` | Campus-Wide (All Depts & Settings) |
| **IQAC Coordinator** | IQAC Coordinator | `iqac` | `iqac@gmrit.edu.in` | College-wide NAAC & NBA Oversight |
| **Central Coordinator** | Central Coordinator | `central.coord` | `central.coord@gmrit.edu.in` | College-wide Events & Student Clubs |
| **Dean R&D** | Dean R&D | `rnd.dean` | `rnd.dean@gmrit.edu.in` | Institutional Research & Patents |

---

### 🏛️ Academic Department Accounts (All 13 Departments)
Every department has **3 primary dedicated accounts**: **HOD**, **Faculty Member**, and **Department Coordinator**.

| # | Department | Role | Full Name | Quick Handle | Login Email |
| :-: | :--- | :--- | :--- | :--- | :--- |
| **1** | **CSE** (Computer Science & Eng.) | HOD | Dr. HOD CSE | `hod.cse` | `hod.cse@gmrit.edu.in` |
| | | Faculty | Faculty Member CSE | `faculty.cse` | `faculty.cse@gmrit.edu.in` |
| | | Coordinator | CSE Dept Coordinator | `coord.cse` | `coord.cse@gmrit.edu.in` |
| | | Junior Asst. | Junior Assistant CSE | `ja.cse` | `ja.cse@gmrit.edu.in` |
| | | Student | Student Sample CSE | `student.cse` | `student.cse@gmrit.edu.in` |
| **2** | **AIDS** (AI & Data Science) | HOD | Dr. HOD AIDS | `hod.aids` | `hod.aids@gmrit.edu.in` |
| | | Faculty | Faculty Member AIDS | `faculty.aids` | `faculty.aids@gmrit.edu.in` |
| | | Coordinator | AIDS Dept Coordinator | `coord.aids` | `coord.aids@gmrit.edu.in` |
| **3** | **AIML** (AI & Machine Learning) | HOD | Dr. HOD AIML | `hod.aiml` | `hod.aiml@gmrit.edu.in` |
| | | Faculty | Faculty Member AIML | `faculty.aiml` | `faculty.aiml@gmrit.edu.in` |
| | | Coordinator | AIML Dept Coordinator | `coord.aiml` | `coord.aiml@gmrit.edu.in` |
| **4** | **ECE** (Electronics & Comm. Eng.) | HOD | Dr. HOD ECE | `hod.ece` | `hod.ece@gmrit.edu.in` |
| | | Faculty | Faculty Member ECE | `faculty.ece` | `faculty.ece@gmrit.edu.in` |
| | | Coordinator | ECE Dept Coordinator | `coord.ece` | `coord.ece@gmrit.edu.in` |
| **5** | **EEE** (Electrical & Electronics Eng.) | HOD | Dr. HOD EEE | `hod.eee` | `hod.eee@gmrit.edu.in` |
| | | Faculty | Faculty Member EEE | `faculty.eee` | `faculty.eee@gmrit.edu.in` |
| | | Coordinator | EEE Dept Coordinator | `coord.eee` | `coord.eee@gmrit.edu.in` |
| **6** | **MECH** (Mechanical Engineering) | HOD | Dr. HOD MECH | `hod.mech` | `hod.mech@gmrit.edu.in` |
| | | Faculty | Faculty Member MECH | `faculty.mech` | `faculty.mech@gmrit.edu.in` |
| | | Coordinator | MECH Dept Coordinator | `coord.mech` | `coord.mech@gmrit.edu.in` |
| **7** | **CIVIL** (Civil Engineering) | HOD | Dr. HOD CIVIL | `hod.civil` | `hod.civil@gmrit.edu.in` |
| | | Faculty | Faculty Member CIVIL | `faculty.civil` | `faculty.civil@gmrit.edu.in` |
| | | Coordinator | CIVIL Dept Coordinator | `coord.civil` | `coord.civil@gmrit.edu.in` |
| **8** | **IT** (Information Technology) | HOD | Dr. HOD IT | `hod.it` | `hod.it@gmrit.edu.in` |
| | | Faculty | Faculty Member IT | `faculty.it` | `faculty.it@gmrit.edu.in` |
| | | Coordinator | IT Dept Coordinator | `coord.it` | `coord.it@gmrit.edu.in` |
| **9** | **BSH** (Basic Sciences & Humanities) | HOD | Dr. HOD BSH | `hod.bsh` | `hod.bsh@gmrit.edu.in` |
| | | Faculty | Faculty Member BSH | `faculty.bsh` | `faculty.bsh@gmrit.edu.in` |
| | | Coordinator | BSH Dept Coordinator | `coord.bsh` | `coord.bsh@gmrit.edu.in` |
| **10** | **CSE-CS** (Cyber Security) | HOD | Dr. HOD CSE-CS | `hod.csecs` | `hod.csecs@gmrit.edu.in` |
| | | Faculty | Faculty Member CSE-CS | `faculty.csecs` | `faculty.csecs@gmrit.edu.in` |
| | | Coordinator | CSE-CS Dept Coordinator | `coord.csecs` | `coord.csecs@gmrit.edu.in` |
| **11** | **Mathematics** | HOD | Dr. HOD Mathematics | `hod.math` | `hod.math@gmrit.edu.in` |
| | | Faculty | Faculty Member Mathematics | `faculty.math` | `faculty.math@gmrit.edu.in` |
| | | Coordinator | Mathematics Dept Coordinator | `coord.math` | `coord.math@gmrit.edu.in` |
| **12** | **Physics** | HOD | Dr. HOD Physics | `hod.physics` | `hod.physics@gmrit.edu.in` |
| | | Faculty | Faculty Member Physics | `faculty.physics` | `faculty.physics@gmrit.edu.in` |
| | | Coordinator | Physics Dept Coordinator | `coord.physics` | `coord.physics@gmrit.edu.in` |
| **13** | **Chemistry** | HOD | Dr. HOD Chemistry | `hod.chem` | `hod.chem@gmrit.edu.in` |
| | | Faculty | Faculty Member Chemistry | `faculty.chem` | `faculty.chem@gmrit.edu.in` |
| | | Coordinator | Chemistry Dept Coordinator | `coord.chem` | `coord.chem@gmrit.edu.in` |

---

## 🛡️ 2. What Can the System Administrator Do?

As the highest authority in the FMS (`role_id = 1`), the Admin possesses complete institutional and technical authority:

### 1. Academic Year Management
* **Route:** `?route=academic_years/list`
* Add new academic sessions (e.g., `2025-2026`, `2026-2027`).
* Toggle the **Active Academic Year** (sets the baseline for all NAAC metrics and faculty submissions).
* Set Start and End dates for academic audits.

### 2. Institutional Document Oversight
* **All College Documents (`?route=documents/college`):** View, search, and audit every document across all 31 departments and all academic years.
* **All Department Files (`?route=documents/department`):** Inspect departmental files, minutes of meetings, course files, and lesson plans.
* **Universal Upload (`?route=documents/upload`):** Upload files directly under any department or under `Complete University` (Dept ID 31).

### 3. Accreditation Command Centers
* **NBA Accreditation (`?route=nba/dashboard`):** Tier-1 SAR tracking, program educational objectives (PEOs), course outcomes (COs), and criterion compliance.
* **NAAC Accreditation (`?route=naac/dashboard`):** Criteria 1 through 7 dashboards, SSR document repositories, institutional benchmarks.

### 4. Student Activities & Events
* **Route:** `?route=student_activities/dashboard`
* Full visibility into all student co-curricular, extra-curricular, NSS, club, and hackathon certificates and event reports.
* Filter by academic year, activity type, or participating department.

### 5. User & Role Management
* Create faculty and staff accounts, assign roles, lock/unlock accounts, and reset credentials.

---

## 📂 3. Student Activities & NAAC Criterion 5 Workflow

### How the Event Sync Problem is Solved:
* **The Challenge:** Often, an event is conducted today, but student activity reports or certificates are uploaded weeks later. If uploaded over existing files, records can get corrupted or dates mismatched.
* **How FMS Handles It:**
  1. **Strict Metadata Separation:** Each activity record stores both the **Event Date** (`event_start_date` / `event_end_date`) and the **Upload Timestamp** (`created_at`).
  2. **Academic Year Binding:** Files are strictly bound to their respective academic year and criterion code (e.g., `5.1.1` Scholarships, `5.2.1` Placements, `5.3.1` Cultural/Sports Awards).
  3. **Zero Collision Guarantee:** Every uploaded file receives a cryptographically unique storage hash (`uuid.pdf`), preventing accidental file overwrites even if multiple files share identical original names.
  4. **Clean Slate for Production:** The included `clean_production_setup.sql` clears all past trial uploads, allowing genuine college events to be recorded in chronological order.

---

## 🖥️ 4. Step-by-Step Desktop Server Setup

Follow these steps on the desktop computer provided by the college:

### Step 4.1: Copy Project Folder
1. Copy the entire `FMS` folder into the XAMPP web root on the desktop:
   * **Target Path:** `C:\xampp\htdocs\mini\FMS`  
   *(If your XAMPP is on `D:` or `E:`, adjust the drive letter accordingly).*

### Step 4.2: Clean File Storage (`uploads/`)
1. Open `C:\xampp\htdocs\mini\FMS\uploads\` on the desktop.
2. Delete any old trial files/PDFs inside this folder.
3. ⚠️ **Do NOT delete the file `uploads/.htaccess`!**  
   *(This security file blocks malicious PHP script execution inside the uploads folder).*
4. Ensure `C:\xampp\htdocs\mini\FMS\tmp\` exists and is empty.

---

## 🗄️ 5. Server Database Setup & Synchronization

Depending on whether your server is already live with existing data or you are starting fresh, choose **Option A** or **Option B**:

---

### 🚀 Option A: Sync an Already-Deployed / Live Server (Zero Data Loss - Recommended)
Use this option if you have already deployed FMS on your server and you want to match the updated database **without losing any uploaded documents, workflows, or existing records**.

#### Step 1: Pull the latest code on your server
```bash
git pull origin main
```

#### Step 2: Run the two non-destructive migrations
Execute the following two SQL scripts located in `database/migrations/`:
1. **`database/migrations/010_update_departments_is_academic.sql`**  
   *(Flags the 13 academic degree-granting departments so registration and criteria dropdowns strictly show genuine academic departments).*
2. **`database/migrations/011_seed_all_department_accounts.sql`**  
   *(Adds/updates all 42 institutional accounts across all 13 departments with password `Admin@123`. Uses safe `ON DUPLICATE KEY UPDATE` and preserves all existing foreign keys and records).*

#### How to execute on your server:
* **Via phpMyAdmin (Easiest):**
  1. Open phpMyAdmin on the server (`http://localhost/phpmyadmin` or your server's phpMyAdmin URL).
  2. Click on the database name: **`gmrdufms`**.
  3. Click on the **SQL** tab at the top.
  4. Copy and paste the contents of `database/migrations/010_update_departments_is_academic.sql` and click **Go**.
  5. Copy and paste the contents of `database/migrations/011_seed_all_department_accounts.sql` and click **Go**.
* **Via Command Line / Terminal (Linux / Windows Server):**
  ```bash
  mysql -u root -p gmrdufms < database/migrations/010_update_departments_is_academic.sql
  mysql -u root -p gmrdufms < database/migrations/011_seed_all_department_accounts.sql
  ```
  *(Enter your MySQL root password when prompted. If MySQL has no password, omit `-p`).*

🎉 **Done!** Your live database is now 100% synchronized with all 13 departments, clean dropdowns, and all 42 login accounts.

---

### 🆕 Option B: Fresh Database Setup / Complete Reset
Use this option if you are deploying to a brand-new computer/server or if you want to wipe test clutter and start with a completely fresh production database.

📂 **File:** `database/clean_production_setup.sql`  
*(Contains all 72 production tables, NAAC/NBA schemas, core lookups, and all 42 institutional accounts pre-seeded with 0 test documents).*

#### Method 1: Import via phpMyAdmin
1. Open **XAMPP Control Panel** -> Ensure **Apache** and **MySQL** are running.
2. Open your browser and go to: `http://localhost/phpmyadmin`
3. Click the **Import** tab in the top navigation bar.
4. Click **Choose File** (or Browse) and select:  
   `database/clean_production_setup.sql`
5. Scroll to the bottom and click **Import** (or **Go**).
6. 🎉 Green checkmark confirms database `gmrdufms` created with all 72 tables and 42 user accounts!

#### Method 2: Fast Import via Command Prompt / Terminal
```cmd
cd C:\xampp\mysql\bin
mysql.exe -u root < "C:\xampp\htdocs\mini\FMS\database\clean_production_setup.sql"
```
*(On Linux: `mysql -u root -p < database/clean_production_setup.sql`).*

---

## 🌐 6. College LAN & Wi-Fi Configuration

To allow mobile phones, laptops, and tablets on the college network to access the server:

### Step 6.1: Find Desktop LAN IP
1. On the desktop, press `Win + R`, type `cmd`, and press Enter.
2. Run:
   ```cmd
   ipconfig
   ```
3. Look for **IPv4 Address** under your active Ethernet or Wi-Fi adapter.
   * *Example:* `192.168.1.105` or `10.10.4.52`
   * 📝 **Write this IP down.**

### Step 6.2: Open Windows Firewall (One-Line Command)
Open **PowerShell as Administrator** on the desktop and paste:
```powershell
New-NetFirewallRule -DisplayName "XAMPP Apache Server" -Direction Inbound -LocalPort 80,443 -Protocol TCP -Action Allow
```
*(Press Enter. This allows incoming HTTP requests from the college LAN).*

---

## 📱 7. Accessing the System from Your Mobile Phone

1. Connect your mobile phone to the **SAME Wi-Fi or LAN** as the desktop server.
2. Open Chrome, Safari, or Brave on your phone.
3. Type the following URL (substitute your desktop's actual IP):
   ```text
   http://<DESKTOP-IP>/mini/FMS
   ```
   *Example:* 👉 **`http://192.168.1.105/mini/FMS`**

### Direct Mobile Shortcut URLs:
* **Login Screen:**  
  `http://<DESKTOP-IP>/mini/FMS/public/index.php?route=auth/login`
* **Admin Dashboard:**  
  `http://<DESKTOP-IP>/mini/FMS/public/index.php?route=dashboard`
* **Academic Years:**  
  `http://<DESKTOP-IP>/mini/FMS/public/index.php?route=academic_years/list`
* **NAAC Dashboard:**  
  `http://<DESKTOP-IP>/mini/FMS/public/index.php?route=naac/dashboard`
* **NBA Dashboard:**  
  `http://<DESKTOP-IP>/mini/FMS/public/index.php?route=nba/dashboard`
* **Student Activities:**  
  `http://<DESKTOP-IP>/mini/FMS/public/index.php?route=student_activities/dashboard`

*(FMS automatically configures all internal links and redirects dynamically using the requesting device's IP, so navigation on mobile is completely seamless).*

---

## 🔍 8. Pre-Flight Verification Script (CLI)

Run this quick command on the desktop to verify everything is 100% operational:
```cmd
cd C:\xampp\htdocs\mini\FMS
php bin/deploy_check.php
```

### Expected Output:
```text
================================================================================
           FILE MANAGEMENT SYSTEM (FMS) - PRE-FLIGHT DEPLOYMENT CHECK           
================================================================================
[PHP ENVIRONMENT]        [PASS] PHP Version >= 8.1
[REQUIRED EXTENSIONS]    [PASS] mysqli, fileinfo, mbstring, session, json
[CONFIGURATION]          [PASS] Environment Set (production), Debug: false
[FILESYSTEM]             [PASS] uploads/ and tmp/ are writable
[DATABASE]               [PASS] MySQL Connection Active (gmrdufms)
[DATABASE SCHEMA]        [PASS] All 72 core tables present
[WEB SERVER SECURITY]    [PASS] .htaccess guards active on uploads, tmp, database
================================================================================
STATUS: ALL PRE-FLIGHT CHECKS PASSED. SYSTEM IS PRODUCTION READY!
================================================================================
```

---

## 📱 9. Step-by-Step Mobile Demo Script for Evaluators

When presenting the project from your mobile phone to evaluators or professors:

### Phase 1: Institutional Authority (Admin Login)
1. Open mobile browser &rarr; go to `http://<IP>/mini/FMS`.
2. Login with: `admin` / `Admin@123`.
3. Point out the header badge: **`🛡️ System Administrator`**.
4. Show the dedicated **"Administrator Control & Actions"** cards on mobile:
   * Tap **Manage Academic Years** &rarr; demonstrate creating `2025-2026`.
   * Tap **All College Documents** &rarr; demonstrate global search across all 31 departments.
   * Tap **NAAC Accreditation** &rarr; show Criteria 1 to 7 status.
   * Tap **NBA Accreditation** &rarr; show Tier-1 metrics.

### Phase 2: NAAC Criterion 5 & Student Activities
1. Tap **Student Activities** on mobile.
2. Demonstrate how student co-curricular and club achievements are linked to Criterion 5.
3. Highlight that events conducted weeks prior are sorted chronologically by actual event date, eliminating record overlap.

### Phase 3: Role Separation (Faculty/HOD View)
1. Tap **Logout** on mobile.
2. Login with `hod.cse@gmrit.edu.in` / `Admin@123`.
3. Notice that the HOD interface restricts actions strictly to the **CSE Department**, proving multi-tenant role-based access control (RBAC).

---

## 🛠️ 10. Emergency On-Site Troubleshooting

| Issue on Mobile / Desktop | Probable Cause | Instant Fix |
| :--- | :--- | :--- |
| **"Site cannot be reached" on mobile** | Windows Firewall is blocking incoming connections | Run the PowerShell firewall command in **Section 6.2**. |
| **Mobile is on cellular data** | Mobile not connected to college Wi-Fi | Turn off 4G/5G mobile data; connect to the **same Wi-Fi router / LAN** as the desktop. |
| **College Wi-Fi has AP Isolation** | College router blocks devices from talking to each other | Enable **Mobile Hotspot** on your phone &rarr; connect the desktop to your phone hotspot &rarr; run `ipconfig` &rarr; use the new hotspot IP! |
| **Desktop IP changes after reboot** | DHCP lease expired | Run `ipconfig` in CMD, check the new IPv4 address, and update the URL. |
| **Apache won't start (Port 80 busy)** | Port 80 used by Skype, IIS, or VMware | In XAMPP, click Apache **Config** &rarr; `httpd.conf` &rarr; change `Listen 80` to `Listen 8080`. Mobile URL becomes `http://<IP>:8080/mini/FMS`. |
| **Database error on page load** | MySQL service stopped | Open XAMPP Control Panel &rarr; ensure MySQL displays a green light with PID and Port 3306. |

---

*GMRIT File Management System (FMS) — Official Deployment Guide*  
*Ready for Tomorrow's Evaluation & Campus Intranet Launch.*
