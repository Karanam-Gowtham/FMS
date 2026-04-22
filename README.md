# Faculty Management System (FMS) 🚀

A premium, role-based document management solution designed for **GMRIT** and higher education institutions. FMS streamlines the lifecycle of academic and administrative proofs, from faculty uploads to HOD approval and accreditation-ready consolidation (NAAC/NBA).

---

## ✨ Key Features

- **🎯 Intelligent Dashboard**: A unified, role-aware dashboard that dynamically lists pending tasks. It features real-time badge counts and asynchronous notification polling.
- **📄 Instant File Previews**: Integrated "View" buttons across all file management tables allow instant PDF and image previews without forced downloads.
- **🔗 Smart PDF Merging**: Client-side PDF merging capabilities using `pdf-lib` allow users to consolidate multiple proofs into a single document on-the-fly.
- **🛡️ Secure Scoping (RBAC)**: Robust **Role-Based Access Control** enforced via a centralized scoping engine (`dept_scope.php`), ensuring data isolation between departments and users.
- **📊 Accreditation-Ready Excel Exports**: Direct export of metadata and file lists to Excel, filtered by criteria and academic year.
- **🔒 Security by Design**: Comprehensive protection against CSRF, SQL injection (prepared statements), and unauthorized path traversal.

---

## 🛠️ Technology Stack

| Layer | Technologies |
| :--- | :--- |
| **Backend** | PHP 7.4+ / 8.x (Core logic for maximum performance) |
| **Database** | MySQL / MariaDB (Project-FMS Schema) |
| **Frontend** | Vanilla JS, CSS3, Google Fonts (Inter/Roboto), Bootstrap, FontAwesome |
| **Libraries** | `pdf-lib` (Client-side PDF processing), `mysqli` (Prepared statements) |
| **Deployment** | Apache (XAMPP / Render / Linux VPS) |

---

## 📂 Project Architecture

```text
FMS/
├── dashboard.php             # Centralized Task Management (Role-aware)
├── includes/
│   ├── dept_scope.php        # The Core Security Engine (RBAC & Query Scoping)
│   ├── connection.php        # Database Configuration & CSRF Seeding
│   ├── header.php            # Dynamic Navigation & Dashboard Modal
│   └── csrf.php              # Anti-CSRF Token Management
├── modules/
│   ├── faculty/              # Submission center (Publications, FDPs, Students)
│   ├── dept_coordinator/     # Review & Verification Workflows
│   ├── central/              # Institutional Reporting (NAAC/AQAR)
│   └── common/               # Shared Utilities (merger, viewing)
├── admin/                    # System Configuration & Bulk Management
└── uploads/                  # Secure Document Storage
```

---

## 🔐 Security & Data Isolation

The system employs a sophisticated isolation strategy:
- **Departmental Silos**: Users can only see and interact with files belonging to their assigned department.
- **Ownership Validation**: Faculty members are restricted to their own uploads unless granted higher privileges.
- **Safe Viewers**: Files are resolved through database-backed handlers (`view_file.php`) which validate permissions before serving content from the `uploads/` directory, preventing direct URL access.
- **CSRF Protection**: All state-changing operations (approvals, deletes, uploads) are guarded by unique session-bound tokens.

---

## 🚀 Getting Started

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7+ / MariaDB 10.4+
- Apache Server

### Quick Setup
1. **Clone & Drop**: Place the project in your web servant root (e.g., `htdocs/mini/FMS`).
2. **Database Initialization**:
   - Create a database named `project-fms`.
   - Import `database/project-fms.sql` to seed the schema.
3. **Configuration**:
   - Edit `includes/connection.php`.
   - Update `$base_url` to match your local environment (e.g., `http://localhost/mini/FMS/`).
   - Configure your DB credentials (`$host`, `$user`, `$pass`, `$db`).
4. **Access**: Navigate to the base URL and log in with your credentials.

---

## 📝 Recent Version Notes
> [!NOTE]
> **Version Update (HEAD):** Reverted to commit `d127825` to stabilize the dashboard logic and ensure full compatibility with the new "View" button architecture.

---

## ⚖️ License
This project is developed for institutional use. Maintain the `includes/dept_scope.php` integrity when extending file tables to ensure security compliance. Update **`$base_url`** and any hardcoded paths (`/mini/FMS/` in `includes/header.php` iframe) when deploying to a different base path.
