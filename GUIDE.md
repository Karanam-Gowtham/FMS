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
5. [🗄️ Clean Production Database Import](#️-5-clean-production-database-import)
6. [🌐 College LAN & Wi-Fi Configuration](#-6-college-lan--wi-fi-configuration)
7. [📱 Accessing the System from Your Mobile Phone](#-7-accessing-the-system-from-your-mobile-phone)
8. [🔍 Pre-Flight Verification Script (CLI)](#-8-pre-flight-verification-script-cli)
9. [📱 Step-by-Step Mobile Demo Script for Evaluators](#-9-step-by-step-mobile-demo-script-for-evaluators)
10. [🛠️ Emergency On-Site Troubleshooting](#️-10-emergency-on-site-troubleshooting)

---

## 🔑 1. Master Login Credentials Cheat-Sheet

All pre-seeded institutional accounts use the unified default password:  
👉 **`Admin@123`**

*(Any user can also log in by typing their username or email handle in the login box).*

| ID | Institutional Role | Full Name | Login Email | Scope / Department |
| :---: | :--- | :--- | :--- | :--- |
| **1** | **System Administrator** | System Administrator | `admin@gmrit.edu.in` | Campus-Wide (All Depts) |
| **2** | **IQAC Coordinator** | IQAC Coordinator | `iqac@gmrit.edu.in` | College-wide NAAC & NBA |
| **3** | **Head of Department** | Dr. HOD CSE | `hod.cse@gmrit.edu.in` | CSE Department |
| **4** | **Faculty Member** | Faculty Member | `faculty.cse@gmrit.edu.in` | CSE Department |
| **5** | **Dept Coordinator** | CSE Dept Coordinator | `coord.cse@gmrit.edu.in` | CSE Department |
| **6** | **Central Coordinator** | Central Coordinator | `central.coord@gmrit.edu.in` | College-wide Events & Clubs |
| **7** | **Junior Assistant** | Junior Assistant CSE | `ja.cse@gmrit.edu.in` | CSE Department |
| **8** | **Dean R&D** | Dean R&D | `rnd.dean@gmrit.edu.in` | Global Research & Patents |
| **9** | **Student** | Student Sample | `student.cse@gmrit.edu.in` | CSE Department |

> 💡 **Quick Login Tip:**  
> On mobile, you don't even need to type the full `@gmrit.edu.in`. Simply typing `admin` in the email field will log in as the System Administrator!

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
* **Route:** `?route=student_activity/list`
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

## 🗄️ 5. Clean Production Database Import

We generated a clean SQL script:  
📂 **Path:** `database/clean_production_setup.sql`  
*(Contains all 72 tables, NAAC/NBA schemas, and 9 institutional user accounts with 0 dummy clutter).*

### Method A: Import via phpMyAdmin (Recommended)
1. Open the **XAMPP Control Panel** on the desktop.
2. Click **Start** next to **Apache** and **MySQL** (both should turn green).
3. Open browser on the desktop and go to:  
   👉 `http://localhost/phpmyadmin`
4. Click the **Import** tab in the top navigation bar.
5. Click **Choose File** (or Browse) and select:  
   `C:\xampp\htdocs\mini\FMS\database\clean_production_setup.sql`
6. Scroll down and click **Import** (or **Go**).
7. 🎉 You will see a green checkmark: Database `gmrdufms` created with 72 tables!

### Method B: Fast Import via Command Prompt (Alternative)
```cmd
cd C:\xampp\mysql\bin
mysql.exe -u root < "C:\xampp\htdocs\mini\FMS\database\clean_production_setup.sql"
```

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
  `http://<DESKTOP-IP>/mini/FMS/public/index.php?route=student_activity/list`

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
