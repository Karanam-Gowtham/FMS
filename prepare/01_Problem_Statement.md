# Problem Statement

## Title
**Faculty Management System (FMS)** — A Role-Based Document Management Solution for NAAC/NBA Accreditation Readiness

---

## Background & Context

Higher education institutions in India, such as **GMR Institute of Technology (GMRIT)**, are periodically assessed by national accreditation bodies like **NAAC (National Assessment and Accreditation Council)** and **NBA (National Board of Accreditation)**. These assessments require institutions to produce and present large volumes of structured documentary evidence across **7 major criteria** with hundreds of sub-criteria (e.g., 1.1.1, 2.3.4(A), 5.2.1, etc.).

---

## The Problem

### 1. Manual, Paper-Based Document Management
Before this system, the entire lifecycle of academic and administrative proof documents — including faculty publications, FDP certificates, conference papers, patents, student activity records, meeting minutes, placement data, and more — was handled **manually** through physical files, emails, and ad-hoc folder sharing. This approach is:
- **Error-prone** — Documents get lost, mislabeled, or duplicated.
- **Time-consuming** — Faculty and administrators spend excessive hours organizing and searching for specific proofs.
- **Non-auditable** — No clear trail of who uploaded what, when it was reviewed, or why it was rejected.

### 2. No Structured Approval Workflow
Documents uploaded by faculty members had **no formal verification pipeline**. There was no mechanism for:
- The **Head of Department (HOD)** to review and approve/reject documents.
- The **Department Coordinator** to verify documents before forwarding them to the HOD.
- Tracking the **status** of each document (Pending, Approved, Rejected) in real-time.

### 3. Lack of Centralized Access
Different departments maintained their own isolated records with no central visibility. The **IQAC (Internal Quality Assurance Cell)** and **Central Coordinators** had no unified platform to:
- View consolidated reports across all departments.
- Download or merge documents for accreditation submissions.
- Monitor compliance with criteria requirements.

### 4. Role Confusion and Security Gaps
Without a proper Role-Based Access Control (RBAC) system:
- Faculty could potentially access or modify others' documents.
- There was no department-level isolation for sensitive data.
- Bulk operations (download, delete, merge) lacked authorization checks.

---

## Objectives of the Project

1. **Digitize** the entire document lifecycle — from faculty upload to final approval.
2. **Implement a multi-tier approval workflow**: Faculty → HOD → Department Coordinator → (optionally) Central Coordinator.
3. **Map all documents** to specific NAAC/NBA criteria and sub-criteria for instant accreditation readiness.
4. **Enforce Role-Based Access Control (RBAC)** with 6 distinct user roles: Faculty, HOD, Department Coordinator, Junior Assistant, Central Coordinator, and Admin.
5. **Provide a unified dashboard** showing pending tasks, notification badges, and real-time status updates for every role.
6. **Enable bulk operations** — download, merge (PDF), and export (Excel) — scoped by department and role.
7. **Support email notifications** to alert users about pending actions.

---

## Scope

| In Scope | Out of Scope |
|:---|:---|
| Faculty document uploads (publications, FDPs, conferences, patents, student activities, placement, higher education) | Student-facing portals |
| Multi-tier approval workflow with rejection reasons | Integration with external university ERP systems |
| NAAC/NBA criteria mapping (Criteria 1–7 with sub-criteria) | Mobile application (responsive web only) |
| Role-based dashboards and notifications | Automated accreditation scoring |
| PDF merging and bulk download/export | Password hashing (legacy plain-text in DB) |
| Central coordinator flows (AQAR, events, clubs) | |
| Email notification system | |

---

## Target Users

| Role | Description |
|:---|:---|
| **Faculty** | Uploads academic proofs, tracks status, re-uploads on rejection |
| **Head of Department (HOD)** | Reviews and approves/rejects documents from their department |
| **Department Coordinator** | Verifies approved documents, manages meeting minutes |
| **Junior Assistant** | Assists the department coordinator with data entry |
| **Central Coordinator** | Oversees consolidated institutional data (NAAC/NBA/NCC/Sports/Clubs) |
| **Admin** | Manages users, criteria definitions, and system configuration |

---

## Expected Outcome

A fully functional, web-based **Faculty Management System** that:
- Reduces document preparation time for accreditation visits by **~70%**.
- Provides **100% audit trail** for every uploaded document.
- Enables instant accreditation readiness with pre-mapped criteria reports.
- Eliminates inter-departmental data silos through centralized access.
