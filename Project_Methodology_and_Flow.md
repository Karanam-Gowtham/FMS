# Project Methodology and Flow Architecture

This document details the operational methodology, architectural structure, and workflow state machines powering the **File Management System (FMS)**.

---

## 1. Project Methodology

The **File Management System (FMS)** is engineered around a **Decoupled MVC Architecture** with a **Data-Driven Workflow Engine**, strict **Role-Based Access Control (RBAC)**, and comprehensive compliance reporting for higher education accreditation bodies (NBA / NAAC).

### 1.1 Architectural Principles
*   **Central Front-Controller Pattern:** All HTTP requests enter through `public/index.php`, where route resolution and request dispatching occur securely without exposing internal core scripts.
*   **Data-Driven Dynamic Workflow:** State transitions (`draft`, `pending`, `accepted`, `rejected`) and approver assignments are configured entirely in relational database tables (`workflows`, `workflow_steps`, `workflow_transitions`, `document_actions`), completely removing hardcoded procedural logic.
*   **Metadata Extensibility:** 21 document categories (Journals, Conferences, Patents, FDPs, Student Activities, etc.) store polymorphic schemas into specialized `meta_*` extension tables referenced through a central `meta_registry.php`.
*   **Multi-Tier Defense-in-Depth:**
    *   Strict CSRF tokens for all state-changing requests, with automatic JSON fallback for AJAX/APIs.
    *   Canonical path bounding with `realpath()` verifying that every streamed file resides within the approved `uploads/` directory.
    *   Strict MIME verification via PHP `finfo` (File Information) rather than untrusted client headers.
    *   Dynamic session security with `HttpOnly`, `SameSite=Lax`, and `Secure` cookie flags.
    *   Comprehensive `.htaccess` execution denial in `uploads/`, `tmp/`, `bin/`, and `database/`.

---

## 2. High-Level System Architecture

```mermaid
graph TD
    Client([Browser / Client]) -->|HTTP Request| Router[public/index.php]
    
    subgraph Core Framework Layer
        Router --> AuthGuards[core/auth.php & includes/csrf.php]
        AuthGuards --> Dispatcher{Route Dispatcher}
    end

    subgraph Controller Layer [src/Controllers/]
        Dispatcher --> AuthCtrl[AuthController]
        Dispatcher --> DashCtrl[DashboardController]
        Dispatcher --> DocCtrl[DocumentController & DocumentActionController]
        Dispatcher --> NBACtrl[NBADashboard & NBACriterionController]
        Dispatcher --> NAACCtrl[NAACDashboard & NAACCriterionController]
        Dispatcher --> SACtrl[StudentActivityDashboardController]
        Dispatcher --> PubCtrl[PublicController]
        Dispatcher --> RepCtrl[ReportDownloadController]
    end

    subgraph Service & Engine Layer [core/]
        DocCtrl --> DocSvc[core/document_service.php]
        DocCtrl --> WfEngine[core/workflow_engine.php]
        DocCtrl --> FileSvc[core/file_service.php]
        NBACtrl & NAACCtrl --> PdfSvc[core/accreditation_pdf_service.php]
        NBACtrl & NAACCtrl --> MergeSvc[core/pdf_merger_service.php]
    end

    subgraph Persistence Layer
        DocSvc & WfEngine --> DB[(MySQL / MariaDB: gmrdufms)]
        FileSvc & MergeSvc --> Storage[(Storage: /uploads/ & /tmp/)]
    end

    subgraph Presentation Layer [src/Views/]
        Dispatcher --> Views[Blade-like PHP Views with Bootstrap 5 & Chart.js]
        Views --> Client
    end
```

---

## 3. Workflow State Machine Lifecycle

Documents move through a transactional state machine managed by `core/workflow_engine.php` under exclusive row locks (`SELECT ... FOR UPDATE`):

```mermaid
sequenceDiagram
    autonumber
    participant Uploader as Faculty / Student
    participant Engine as Workflow Engine
    participant Reviewer1 as Dept Coordinator / Mentor
    participant Reviewer2 as HOD / R&D Dean
    participant Storage as Database & Storage

    Uploader->>Engine: Submit Document (Metadata + Binary Proof)
    Note over Engine: Transition: Initialize -> Step 1 (Status: Pending)
    Engine->>Storage: Record in documents & meta_* table

    Engine->>Reviewer1: Route to Pending Approvals Dashboard
    alt Rejection by Initial Reviewer
        Reviewer1->>Engine: Reject with Mandatory Remarks
        Note over Engine: Status: Rejected (Audit logged in document_actions)
        Engine->>Uploader: Notify on Dashboard with Remarks
        Uploader->>Engine: Re-upload proof & Resubmit (Re-enters workflow)
    else Approval by Initial Reviewer
        Reviewer1->>Engine: Approve Submission
        Note over Engine: Transition: Step 1 -> Step 2 (Status: Pending)
        Engine->>Reviewer2: Route to Final Approver Dashboard
        Reviewer2->>Engine: Final Approval
        Note over Engine: Transition: Step 2 -> Terminal (Status: Accepted)
        Engine->>Storage: Mark Status 'accepted', current_step = NULL
        Engine->>Uploader: Proof Approved & Available in Public/Accreditation Reports
    end
```

---

## 4. Accreditation Module Flow (NBA & NAAC)

Accreditation data compilation bridges departmental documents with institutional SAR/SSR generation:

```mermaid
graph LR
    Submissions[Approved Department Documents] --> Aggregator[Accreditation Aggregator]
    CriterionInputs[Manual Criterion Data & Rubrics] --> Aggregator
    
    Aggregator --> Engine[Accreditation Service]
    Engine --> PDF[AccreditationPdfService / FPDF]
    Engine --> Merger[PDF Merger Service / FPDI]
    
    PDF --> Report[Compiled SAR / SSR Criterion Report]
    Merger --> FinalDossier[Consolidated Inspection Dossier with Bookmarks & DVV Links]
```

---

## 5. Technology Stack Summary

| Layer | Implementation |
| :--- | :--- |
| **Language & Runtime** | PHP 8.2+ (strict types, prepared statements, native autoloader) |
| **Database** | MySQL 8.0+ / MariaDB (`utf8mb4` charset, composite covering indexes) |
| **Web Server** | Apache 2.4+ (`mod_rewrite`, `mod_headers`, `.htaccess` isolation) |
| **Frontend** | Vanilla JS (ES6+), Bootstrap 5, FontAwesome 6, Chart.js, Google Fonts (Inter) |
| **Document Generation** | FPDF 1.8x, FPDI 2.6.0 (PDF importing, merging, and stamping) |
| **Security Standards** | OWASP Top 10 aligned: CSRF tokens, strict MIME sniffing, path traversal defenses |
