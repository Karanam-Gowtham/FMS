-- ==============================================================================
-- FMS V2 SCHEMA (Phase 1)
-- Database: gmrdufms
-- Architecture: Fully Normalized + EAV Document Engine + Data-Driven Workflow
-- ==============================================================================

CREATE DATABASE IF NOT EXISTS gmrdufms CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE gmrdufms;

-- ==========================================
-- 1. MASTER DATA
-- ==========================================
CREATE TABLE academic_years (
    year_id INT AUTO_INCREMENT PRIMARY KEY,
    year_label VARCHAR(20) NOT NULL UNIQUE
);

CREATE TABLE departments (
    dept_id INT AUTO_INCREMENT PRIMARY KEY,
    dept_name VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE criteria_master (
    criteria_id INT AUTO_INCREMENT PRIMARY KEY,
    parent_id INT DEFAULT NULL,
    criteria_no VARCHAR(50) NOT NULL,
    description TEXT,
    FOREIGN KEY (parent_id) REFERENCES criteria_master(criteria_id)
);

-- ==========================================
-- 2. UNIFIED AUTHENTICATION
-- ==========================================
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE roles (
    role_id INT AUTO_INCREMENT PRIMARY KEY,
    role_name VARCHAR(50) NOT NULL UNIQUE,
    description VARCHAR(255)
);

CREATE TABLE user_roles (
    user_role_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    role_id INT NOT NULL,
    dept_id INT DEFAULT NULL,
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (role_id) REFERENCES roles(role_id),
    FOREIGN KEY (dept_id) REFERENCES departments(dept_id),
    UNIQUE KEY uq_user_role_dept (user_id, role_id, dept_id)
);

-- ==========================================
-- 3. DATA-DRIVEN WORKFLOW ENGINE
-- ==========================================
CREATE TABLE workflows (
    workflow_id INT AUTO_INCREMENT PRIMARY KEY,
    workflow_key VARCHAR(50) NOT NULL UNIQUE,
    label VARCHAR(100) NOT NULL,
    description TEXT
);

CREATE TABLE workflow_steps (
    step_id INT AUTO_INCREMENT PRIMARY KEY,
    workflow_id INT NOT NULL,
    step_order INT NOT NULL,
    step_label VARCHAR(100) NOT NULL,
    responsible_role_id INT NOT NULL,
    scope ENUM('department', 'global') DEFAULT 'global',
    FOREIGN KEY (workflow_id) REFERENCES workflows(workflow_id),
    FOREIGN KEY (responsible_role_id) REFERENCES roles(role_id),
    UNIQUE KEY uq_workflow_step (workflow_id, step_order)
);

CREATE TABLE workflow_actions (
    action_id INT AUTO_INCREMENT PRIMARY KEY,
    action_key VARCHAR(50) NOT NULL UNIQUE,
    label VARCHAR(100) NOT NULL
);

CREATE TABLE workflow_transitions (
    transition_id INT AUTO_INCREMENT PRIMARY KEY,
    step_id INT NOT NULL,
    action_id INT NOT NULL,
    to_step_id INT DEFAULT NULL,
    resulting_status VARCHAR(50) NOT NULL,
    FOREIGN KEY (step_id) REFERENCES workflow_steps(step_id),
    FOREIGN KEY (action_id) REFERENCES workflow_actions(action_id),
    FOREIGN KEY (to_step_id) REFERENCES workflow_steps(step_id),
    UNIQUE KEY uq_step_action (step_id, action_id)
);

-- ==========================================
-- 4. POLYMORPHIC DOCUMENT ENGINE (EAV)
-- ==========================================
CREATE TABLE document_types (
    type_id INT AUTO_INCREMENT PRIMARY KEY,
    type_code VARCHAR(50) NOT NULL UNIQUE,
    label VARCHAR(150) NOT NULL,
    workflow_id INT DEFAULT NULL,
    FOREIGN KEY (workflow_id) REFERENCES workflows(workflow_id)
);

CREATE TABLE documents (
    doc_id INT AUTO_INCREMENT PRIMARY KEY,
    type_id INT NOT NULL,
    uploaded_by INT NOT NULL,
    dept_id INT DEFAULT NULL,
    academic_year_id INT DEFAULT NULL,
    file_path VARCHAR(500) NOT NULL,
    status VARCHAR(50) NOT NULL DEFAULT 'draft',
    current_step INT DEFAULT NULL,
    rejection_reason TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (type_id) REFERENCES document_types(type_id),
    FOREIGN KEY (uploaded_by) REFERENCES users(user_id),
    FOREIGN KEY (dept_id) REFERENCES departments(dept_id),
    FOREIGN KEY (academic_year_id) REFERENCES academic_years(year_id),
    FOREIGN KEY (current_step) REFERENCES workflow_steps(step_id)
);

CREATE TABLE document_metadata (
    meta_id INT AUTO_INCREMENT PRIMARY KEY,
    doc_id INT NOT NULL,
    meta_key VARCHAR(100) NOT NULL,
    meta_value TEXT,
    FOREIGN KEY (doc_id) REFERENCES documents(doc_id) ON DELETE CASCADE,
    UNIQUE KEY uq_doc_meta (doc_id, meta_key)
);

CREATE TABLE document_actions (
    audit_id INT AUTO_INCREMENT PRIMARY KEY,
    doc_id INT NOT NULL,
    acted_by INT NOT NULL,
    action_key VARCHAR(50) NOT NULL,
    step_id INT DEFAULT NULL,
    remarks TEXT,
    acted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (doc_id) REFERENCES documents(doc_id) ON DELETE CASCADE,
    FOREIGN KEY (acted_by) REFERENCES users(user_id),
    FOREIGN KEY (step_id) REFERENCES workflow_steps(step_id)
);

-- ==========================================
-- 5. INITIAL SEED DATA
-- ==========================================

-- Seed Roles
INSERT INTO roles (role_id, role_name, description) VALUES
(1, 'Admin', 'System administrator'),
(2, 'IQAC', 'IQAC coordinator'),
(3, 'HOD', 'Head of Department'),
(4, 'Faculty', 'Faculty member'),
(5, 'Coordinator', 'Department/Cell coordinator'),
(6, 'Central_Coordinator', 'Central repository coordinator'),
(7, 'Junior_Assistant', 'Administrative assistant'),
(8, 'RnD_Dean', 'Dean of Research and Development');

-- Seed Actions
INSERT INTO workflow_actions (action_id, action_key, label) VALUES
(1, 'approve',  'Approve'),
(2, 'reject',   'Reject'),
(3, 'resubmit', 'Resubmit');

-- Seed "Department Standard" workflow (Faculty -> Dept Coordinator -> HOD -> Dean)
INSERT INTO workflows (workflow_id, workflow_key, label) VALUES
(1, 'department_standard', 'Standard Department Review');

INSERT INTO workflow_steps (step_id, workflow_id, step_order, step_label, responsible_role_id, scope) VALUES
(1, 1, 1, 'Dept Coordinator Review', 5, 'department'),
(2, 1, 2, 'HOD Review',              3, 'department'),
(3, 1, 3, 'R&D Dean Review',         8, 'global');

INSERT INTO workflow_transitions (step_id, action_id, to_step_id, resulting_status) VALUES
(1, 1, 2, 'pending'),     -- Coordinator Approve -> HOD
(1, 2, NULL, 'rejected'), -- Coordinator Reject -> Stop
(2, 1, 3, 'pending'),     -- HOD Approve -> Dean
(2, 2, NULL, 'rejected'), -- HOD Reject -> Stop
(3, 1, NULL, 'accepted'), -- Dean Approve -> Stop (Accepted)
(3, 2, NULL, 'rejected'), -- Dean Reject -> Stop
(1, 3, 1, 'pending'),     -- Resubmit from Coordinator
(2, 3, 1, 'pending'),     -- Resubmit from HOD
(3, 3, 1, 'pending');     -- Resubmit from Dean

-- Seed some Document Types
INSERT INTO document_types (type_id, type_code, label, workflow_id) VALUES
(1, 'fdp', 'Faculty Development Program', 1),
(2, 'patent', 'Patent Publication', 1);
