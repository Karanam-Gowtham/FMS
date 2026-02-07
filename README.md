# Faculty Management System (FMS)

## Overview
The **Faculty Management System (FMS)** is a comprehensive web-based application designed to streamline the management of faculty-related data and administrative processes within an educational institution. It serves as a centralized platform for storing, retrieving, and managing documentation related to academic activities, accreditation criteria (NAAC/NBA), and faculty achievements.

This system is particularly useful for automating the collection of data required for compliance reports, annual quality assurance, and departmental audits.

## Key Features

*   **Role-Based Access Control**: Secure login and dashboard views for different user roles (Administrators, HODs, Faculty, and Coordinators).
*   **Academic Activity Tracking**:
    *   **Publications**: Manage records of Journal publications, Conference papers, and Patents.
    *   **Professional Development**: Track Faculty Development Programs (FDPs) attended or organized.
    *   **Student Activities**: Record student achievements and activities supervised by faculty.
*   **File Management System**:
    *   Upload and organize documents (PDFs, images) linked to specific academic years and accreditation criteria.
    *   Bulk download capabilities for departmental or central files.
*   **Accreditation Support (NAAC/NBA)**:
    *   Built-in database of inspection criteria (e.g., 1.1.1, 1.2.1).
    *   Mapping of uploaded files to specific criteria numbers for easy retrieval during audits.
*   **Utility Tools**:
    *   **PDF Merger**: Integrated tool to merge multiple PDF documents into a single file.
*   **Communication**: Built-in contact form for inquiries.

## Technology Stack

*   **Frontend**: HTML5, CSS3, JavaScript
*   **Backend**: PHP
*   **Database**: MySQL / MariaDB
*   **Server**: Apache (via XAMPP/WAMP)

## Installation Guide

1.  **Prerequisites**:
    *   Install a local server environment like [XAMPP](https://www.apachefriends.org/), WAMP, or MAMP.
    *   Ensure PHP and MySQL services are running.

2.  **Setup Codebase**:
    *   Clone or extract the project source code into your server's root directory (e.g., `C:\xampp\htdocs\FMS`).

3.  **Database Configuration**:
    *   Open `phpMyAdmin` (typically at `http://localhost/phpmyadmin`).
    *   Create a new database named `project-fms` (or update `includes/connection.php` if you choose a different name).
    *   Import the SQL dump at `database/project-fms` (no extension).
    *   *Note: See `DATABASE_README.md` for detailed database documentation.*

4.  **Configure Connection & Sessions**:
    *   Open `includes/connection.php` and verify the credentials:
        ```php
        $conn = mysqli_connect("localhost", "root", "", "project-fms");
        ```
      Update if your local setup differs.
    *   Sessions and CSRF protection are centralized in `includes/session.php` and `includes/csrf.php`. Any new form that mutates data should include `csrf_field()` and call `csrf_validate()` on POST.

5.  **Run the Application**:
    *   Open your web browser and navigate to `http://localhost/FMS/`.

## Directory Structure (high level)

*   `/admin`, `/HOD`, `/modules`: Role-specific UI and actions.
*   `/includes`: Core utilities (`connection.php`, `session.php`, `csrf.php`).
*   `/assets`: Static assets (CSS, JS, templates).
*   `/uploads`: User-uploaded files (served by the app; ensure this is not executable).
*   `database/project-fms`: SQL dump.
*   `index.php`: Landing page and shared header wiring.
