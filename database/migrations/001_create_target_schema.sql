-- ============================================================
-- FMS Phase 2 Migration: Target Schema Creation
-- Date: 2026-09-13
-- Backup: database/backup_pre_phase2_20260913.sql
-- ============================================================

-- ============================================================
-- STEP 1: Rename old empty modern tables (preserve for rollback)
-- All these tables have 0 data rows.
-- ============================================================

RENAME TABLE documents TO _old_documents;
RENAME TABLE document_actions TO _old_document_actions;
RENAME TABLE document_versions TO _old_document_versions;
RENAME TABLE document_role_flow TO _old_document_role_flow;
RENAME TABLE role_flow_logs TO _old_role_flow_logs;
RENAME TABLE rejection_history TO _old_rejection_history;
RENAME TABLE approval_roles TO _old_approval_roles;

-- ============================================================
-- STEP 2: Add RnD_Dean role
-- ============================================================

INSERT INTO roles (role_id, role_name, role_description)
VALUES (8, 'RnD_Dean', 'R&D Dean - final approval authority');

-- ============================================================
-- STEP 3: Create academic_years (with auto-increment PK)
-- ============================================================

CREATE TABLE academic_years (
    year_id    INT AUTO_INCREMENT PRIMARY KEY,
    year_label VARCHAR(40) NOT NULL UNIQUE,
    is_active  TINYINT(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Migrate from legacy academic_year table
INSERT INTO academic_years (year_label, is_active)
SELECT year, CASE WHEN year = '2024-25' THEN 1 ELSE 0 END
FROM academic_year
ORDER BY year;

-- ============================================================
-- STEP 4: Create document_types
-- ============================================================

CREATE TABLE document_types (
    type_id       INT AUTO_INCREMENT PRIMARY KEY,
    type_key      VARCHAR(50) NOT NULL UNIQUE,
    type_label    VARCHAR(200) NOT NULL,
    category      VARCHAR(50) NOT NULL,
    workflow_key  VARCHAR(50) NOT NULL,
    meta_table    VARCHAR(100) DEFAULT NULL,
    form_template VARCHAR(100) DEFAULT NULL,
    is_active     TINYINT(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Populate all document types
INSERT INTO document_types (type_key, type_label, category, workflow_key, meta_table, form_template) VALUES
('journal',             'Published Journal Paper',       'research',   'department', 'meta_journal',             'form_journal'),
('conference',          'Conference Paper',              'research',   'department', 'meta_conference',          'form_conference'),
('patent',              'Patent',                        'research',   'department', 'meta_patent',              'form_patent'),
('fdp_attended',        'FDP/Workshop Attended',         'research',   'department', 'meta_fdp_attended',        'form_fdp_attended'),
('fdp_organised',       'FDP/Workshop Organised',        'research',   'department', 'meta_fdp_organised',       'form_fdp_organised'),
('conf_organised',      'Conference Organised',          'research',   'department', 'meta_conf_organised',      'form_conf_organised'),
('criteria_file',       'NAAC Criteria Document',        'criteria',   'department', 'meta_criteria_file',       'form_criteria_file'),
('dept_file',           'Department Document',           'department', 'department', 'meta_dept_file',           'form_dept_file'),
('central_file',        'Central Event Document',        'central',    'central',    'meta_central_file',        'form_central_file'),
('scholarship',         'Scholarship Data (5.1.1/5.1.2)','criteria',   'department', 'meta_scholarship',         'form_scholarship'),
('placement',           'Placement Data (5.2.1)',        'student',    'department', 'meta_placement',           'form_placement'),
('higher_ed',           'Higher Education (5.2.2)',      'student',    'department', 'meta_higher_ed',           'form_higher_ed'),
('exam_qual',           'Exam Qualification (5.2.3)',    'student',    'department', 'meta_exam_qual',           'form_exam_qual'),
('award',               'Award/Medal (5.3.1)',           'student',    'department', 'meta_award',               'form_award'),
('student_event',       'Student Event',                 'student',    'department', 'meta_student_event',       'form_student_event'),
('student_body',        'Professional Body Activity',    'student',    'department', 'meta_student_body',        'form_student_body'),
('student_journal',     'Student Journal Paper',         'student',    'department', 'meta_student_journal',     'form_student_journal'),
('student_conference',  'Student Conference Paper',      'student',    'department', 'meta_student_conference',  'form_student_conference');

-- ============================================================
-- STEP 5: Create workflow_steps
-- ============================================================

CREATE TABLE workflow_steps (
    step_id          INT AUTO_INCREMENT PRIMARY KEY,
    workflow_key     VARCHAR(50) NOT NULL,
    step_order       INT NOT NULL,
    approver_role_id INT NOT NULL,
    scope            ENUM('department','global') NOT NULL DEFAULT 'department',
    on_approve       VARCHAR(50) NOT NULL,
    on_reject        VARCHAR(50) NOT NULL DEFAULT 'reject_to_start',
    FOREIGN KEY (approver_role_id) REFERENCES roles(role_id),
    UNIQUE KEY uq_workflow_order (workflow_key, step_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Department workflow: Faculty → HOD → RnD_Dean → Accepted
INSERT INTO workflow_steps (workflow_key, step_order, approver_role_id, scope, on_approve, on_reject) VALUES
('department', 1, 3, 'department', 'next_step',  'reject_to_start'),
('department', 2, 8, 'global',     'accept',     'reject_to_start');

-- Central workflow: zero steps (auto-accept on upload by RnD_Dean)
-- No rows inserted for 'central' workflow_key.

-- ============================================================
-- STEP 6: Create documents (single source of truth)
-- ============================================================

CREATE TABLE documents (
    doc_id           INT AUTO_INCREMENT PRIMARY KEY,
    doc_type_id      INT NOT NULL,
    uploaded_by      INT NOT NULL,
    dept_id          INT NOT NULL,
    year_id          INT DEFAULT NULL,
    title            VARCHAR(500) NOT NULL,
    status           VARCHAR(50) NOT NULL DEFAULT 'pending',
    current_step     INT DEFAULT NULL,
    rejection_reason TEXT DEFAULT NULL,
    created_at       DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at       DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (doc_type_id) REFERENCES document_types(type_id),
    FOREIGN KEY (uploaded_by) REFERENCES users(user_id),
    FOREIGN KEY (dept_id) REFERENCES dept(dept_id),
    FOREIGN KEY (current_step) REFERENCES workflow_steps(step_id),
    INDEX idx_status (status),
    INDEX idx_dept_status (dept_id, status),
    INDEX idx_uploader (uploaded_by),
    INDEX idx_type_year (doc_type_id, year_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- STEP 7: Create document_files
-- ============================================================

CREATE TABLE document_files (
    file_id       INT AUTO_INCREMENT PRIMARY KEY,
    doc_id        INT NOT NULL,
    file_label    VARCHAR(100) NOT NULL,
    original_name VARCHAR(255) NOT NULL,
    stored_name   VARCHAR(255) NOT NULL,
    file_path     VARCHAR(500) NOT NULL,
    mime_type     VARCHAR(100) DEFAULT NULL,
    file_size     INT DEFAULT NULL,
    uploaded_at   DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (doc_id) REFERENCES documents(doc_id) ON DELETE CASCADE,
    INDEX idx_doc (doc_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- STEP 8: Create document_actions (audit trail)
-- ============================================================

CREATE TABLE document_actions (
    action_id  INT AUTO_INCREMENT PRIMARY KEY,
    doc_id     INT NOT NULL,
    acted_by   INT NOT NULL,
    action     ENUM('uploaded','approved','rejected','resubmitted') NOT NULL,
    step_id    INT DEFAULT NULL,
    remarks    TEXT DEFAULT NULL,
    acted_at   DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (doc_id) REFERENCES documents(doc_id),
    FOREIGN KEY (acted_by) REFERENCES users(user_id),
    FOREIGN KEY (step_id) REFERENCES workflow_steps(step_id),
    INDEX idx_doc (doc_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- STEP 9: Create all 18 meta_* extension tables
-- ============================================================

CREATE TABLE meta_journal (
    doc_id             INT PRIMARY KEY,
    paper_title        VARCHAR(300) NOT NULL,
    journal_name       VARCHAR(200) NOT NULL,
    authors            TEXT,
    issn_no            VARCHAR(50),
    volume_no          VARCHAR(50),
    issue_no           VARCHAR(50),
    page_no            VARCHAR(50),
    doi                VARCHAR(255),
    jcr_quartile       VARCHAR(50),
    scopus_quartile    VARCHAR(50),
    publication_link   VARCHAR(255),
    indexing           VARCHAR(100),
    date_of_publication DATE,
    impact_factor      DECIMAL(10,2),
    quality_factor     DECIMAL(10,2),
    payment            VARCHAR(200),
    FOREIGN KEY (doc_id) REFERENCES documents(doc_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE meta_conference (
    doc_id             INT PRIMARY KEY,
    paper_title        VARCHAR(300) NOT NULL,
    conference_name    VARCHAR(200) NOT NULL,
    authors            TEXT,
    paper_type         VARCHAR(100),
    volume_no          VARCHAR(50),
    issue_no           VARCHAR(50),
    page_no            VARCHAR(50),
    indexing           VARCHAR(100),
    publication_link   VARCHAR(255),
    issn_no            VARCHAR(50),
    doi                VARCHAR(255),
    from_date          DATE,
    to_date            DATE,
    organised_by       VARCHAR(200),
    location           VARCHAR(200),
    FOREIGN KEY (doc_id) REFERENCES documents(doc_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE meta_patent (
    doc_id        INT PRIMARY KEY,
    patent_title  VARCHAR(300) NOT NULL,
    patent_no     VARCHAR(255),
    patent_type   VARCHAR(100),
    date_of_issue DATE,
    inventors     TEXT,
    FOREIGN KEY (doc_id) REFERENCES documents(doc_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE meta_fdp_attended (
    doc_id       INT PRIMARY KEY,
    mode         VARCHAR(50),
    date_from    DATE,
    date_to      DATE,
    organised_by VARCHAR(200),
    location     VARCHAR(200),
    FOREIGN KEY (doc_id) REFERENCES documents(doc_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE meta_fdp_organised (
    doc_id       INT PRIMARY KEY,
    date_from    DATE,
    date_to      DATE,
    organised_by VARCHAR(200),
    location     VARCHAR(200),
    FOREIGN KEY (doc_id) REFERENCES documents(doc_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE meta_conf_organised (
    doc_id       INT PRIMARY KEY,
    mode         VARCHAR(50),
    date_from    DATE,
    date_to      DATE,
    organised_by VARCHAR(200),
    location     VARCHAR(200),
    FOREIGN KEY (doc_id) REFERENCES documents(doc_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE meta_criteria_file (
    doc_id      INT PRIMARY KEY,
    criteria_no VARCHAR(30) NOT NULL,
    description VARCHAR(1500),
    semester    INT,
    section     VARCHAR(20),
    ext_or_int  VARCHAR(20),
    FOREIGN KEY (doc_id) REFERENCES documents(doc_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE meta_dept_file (
    doc_id        INT PRIMARY KEY,
    file_type     VARCHAR(100),
    sub_file_type VARCHAR(100),
    semester      INT,
    review_period VARCHAR(50),
    study_year    VARCHAR(50),
    meeting_no    VARCHAR(50),
    FOREIGN KEY (doc_id) REFERENCES documents(doc_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE meta_central_file (
    doc_id     INT PRIMARY KEY,
    event      VARCHAR(100),
    club_name  VARCHAR(200),
    event_name VARCHAR(200),
    FOREIGN KEY (doc_id) REFERENCES documents(doc_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE meta_scholarship (
    doc_id        INT PRIMARY KEY,
    scheme_name   VARCHAR(200),
    gov_students  INT,
    gov_amount    DECIMAL(12,2),
    inst_students INT,
    inst_amount   DECIMAL(12,2),
    ngo_students  INT,
    ngo_amount    DECIMAL(12,2),
    ngo_name      VARCHAR(200),
    FOREIGN KEY (doc_id) REFERENCES documents(doc_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE meta_placement (
    doc_id       INT PRIMARY KEY,
    student_name VARCHAR(200),
    programme    VARCHAR(100),
    employer     VARCHAR(200),
    pay          VARCHAR(100),
    FOREIGN KEY (doc_id) REFERENCES documents(doc_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE meta_higher_ed (
    doc_id             INT PRIMARY KEY,
    student_name       VARCHAR(200),
    programme          VARCHAR(100),
    institution        VARCHAR(200),
    admitted_programme VARCHAR(200),
    FOREIGN KEY (doc_id) REFERENCES documents(doc_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE meta_exam_qual (
    doc_id      INT PRIMARY KEY,
    reg_no      VARCHAR(50),
    exam        VARCHAR(200),
    exam_status VARCHAR(50),
    FOREIGN KEY (doc_id) REFERENCES documents(doc_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE meta_award (
    doc_id             INT PRIMARY KEY,
    award_name         VARCHAR(200),
    participation_type VARCHAR(100),
    student_name       VARCHAR(200),
    competition_level  VARCHAR(100),
    event_name         VARCHAR(200),
    month_year         VARCHAR(50),
    FOREIGN KEY (doc_id) REFERENCES documents(doc_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE meta_student_event (
    doc_id               INT PRIMARY KEY,
    activity             VARCHAR(200),
    event_name           VARCHAR(200),
    from_date            DATE,
    to_date              DATE,
    organised_by         VARCHAR(200),
    location             VARCHAR(200),
    participation_status VARCHAR(100),
    FOREIGN KEY (doc_id) REFERENCES documents(doc_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE meta_student_body (
    doc_id               INT PRIMARY KEY,
    body_name            VARCHAR(200),
    event_name           VARCHAR(200),
    from_date            DATE,
    to_date              DATE,
    organised_by         VARCHAR(200),
    location             VARCHAR(200),
    participation_status VARCHAR(100),
    FOREIGN KEY (doc_id) REFERENCES documents(doc_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE meta_student_journal (
    doc_id              INT PRIMARY KEY,
    paper_title         VARCHAR(300),
    journal_name        VARCHAR(200),
    indexing            VARCHAR(100),
    date_of_submission  DATE,
    impact_factor       DECIMAL(10,2),
    quality_factor      DECIMAL(10,2),
    payment             VARCHAR(200),
    FOREIGN KEY (doc_id) REFERENCES documents(doc_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE meta_student_conference (
    doc_id       INT PRIMARY KEY,
    paper_title  VARCHAR(300),
    paper_type   VARCHAR(100),
    from_date    DATE,
    to_date      DATE,
    organised_by VARCHAR(200),
    location     VARCHAR(200),
    FOREIGN KEY (doc_id) REFERENCES documents(doc_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- VERIFICATION QUERIES (run after migration)
-- ============================================================
-- SELECT COUNT(*) FROM roles;              -- expect 8
-- SELECT COUNT(*) FROM academic_years;     -- expect 5
-- SELECT COUNT(*) FROM document_types;     -- expect 18
-- SELECT COUNT(*) FROM workflow_steps;     -- expect 2
-- SELECT COUNT(*) FROM documents;          -- expect 0
-- SHOW TABLES LIKE '_old_%';              -- expect 7 renamed tables
-- SHOW TABLES LIKE 'meta_%';             -- expect 18 meta tables
