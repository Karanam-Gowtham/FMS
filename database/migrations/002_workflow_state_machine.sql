-- ============================================================
-- FMS Phase 4 Migration: Workflow State Machine
-- Date: 2026-09-13
-- Prerequisite: backup_pre_phase4_YYYYMMDD.sql must exist
-- ============================================================
-- This migration:
--   1. Drops the Phase 2 workflow_steps table (0 data rows)
--   2. Creates the 4-table workflow state machine
--   3. Seeds workflow configurations
--   4. Updates document_types.workflow_key values
--   5. Removes hard-coded WORKFLOW_APPROVAL_ROLES from constants
-- ============================================================

SET FOREIGN_KEY_CHECKS = 0;

-- ============================================================
-- STEP 1: Remove Phase 2 workflow_steps and related FKs
-- (documents and document_actions have 0 rows, safe to drop FKs)
-- ============================================================

-- Drop FK on documents.current_step if it exists
SET @fk_name = (
    SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'documents'
      AND COLUMN_NAME = 'current_step'
      AND REFERENCED_TABLE_NAME = 'workflow_steps'
    LIMIT 1
);
SET @sql = IF(@fk_name IS NOT NULL,
    CONCAT('ALTER TABLE documents DROP FOREIGN KEY `', @fk_name, '`'),
    'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Drop FK on document_actions.step_id if it exists
SET @fk_name2 = (
    SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'document_actions'
      AND COLUMN_NAME = 'step_id'
      AND REFERENCED_TABLE_NAME = 'workflow_steps'
    LIMIT 1
);
SET @sql2 = IF(@fk_name2 IS NOT NULL,
    CONCAT('ALTER TABLE document_actions DROP FOREIGN KEY `', @fk_name2, '`'),
    'SELECT 1');
PREPARE stmt2 FROM @sql2;
EXECUTE stmt2;
DEALLOCATE PREPARE stmt2;

-- Drop old workflow_steps table
DROP TABLE IF EXISTS workflow_steps;

-- ============================================================
-- STEP 2: Create the 4-table workflow state machine
-- ============================================================

CREATE TABLE workflows (
    workflow_id   INT AUTO_INCREMENT PRIMARY KEY,
    workflow_key  VARCHAR(50) NOT NULL UNIQUE,
    label         VARCHAR(200) NOT NULL,
    description   TEXT DEFAULT NULL,
    is_active     TINYINT(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE workflow_steps (
    step_id             INT AUTO_INCREMENT PRIMARY KEY,
    workflow_id         INT NOT NULL,
    step_order          INT NOT NULL,
    step_label          VARCHAR(100) NOT NULL,
    responsible_role_id INT NOT NULL,
    scope               ENUM('department','global') NOT NULL DEFAULT 'department',

    FOREIGN KEY (workflow_id) REFERENCES workflows(workflow_id),
    FOREIGN KEY (responsible_role_id) REFERENCES roles(role_id),
    UNIQUE KEY uq_workflow_step (workflow_id, step_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE workflow_actions (
    action_id   INT AUTO_INCREMENT PRIMARY KEY,
    action_key  VARCHAR(50) NOT NULL UNIQUE,
    label       VARCHAR(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE workflow_transitions (
    transition_id    INT AUTO_INCREMENT PRIMARY KEY,
    step_id          INT NOT NULL,
    action_id        INT NOT NULL,
    to_step_id       INT DEFAULT NULL,
    resulting_status VARCHAR(50) NOT NULL,

    FOREIGN KEY (step_id) REFERENCES workflow_steps(step_id),
    FOREIGN KEY (action_id) REFERENCES workflow_actions(action_id),
    FOREIGN KEY (to_step_id) REFERENCES workflow_steps(step_id),
    UNIQUE KEY uq_step_action (step_id, action_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- STEP 3: Seed workflow actions
-- ============================================================

INSERT INTO workflow_actions (action_key, label) VALUES
    ('approve',  'Approve'),
    ('reject',   'Reject'),
    ('resubmit', 'Resubmit');

-- ============================================================
-- STEP 4: Seed workflow configurations
-- ============================================================

-- Workflow A: department_standard (Faculty → Dept Coordinator → HOD → R&D Dean → Accepted)
INSERT INTO workflows (workflow_key, label, description) VALUES
    ('department_standard', 'Standard Department Review',
     'Faculty uploads → Dept Coordinator reviews (department scope) → HOD reviews (department scope) → R&D Dean reviews (global scope) → Accepted');

SET @wf_dept = LAST_INSERT_ID();

INSERT INTO workflow_steps (workflow_id, step_order, step_label, responsible_role_id, scope) VALUES
    (@wf_dept, 1, 'Dept Coordinator Review', 5, 'department'),
    (@wf_dept, 2, 'HOD Review',              3, 'department'),
    (@wf_dept, 3, 'R&D Dean Review',         8, 'global');

-- Get the step IDs we just inserted
SET @step_cord = (SELECT step_id FROM workflow_steps WHERE workflow_id = @wf_dept AND step_order = 1);
SET @step_hod  = (SELECT step_id FROM workflow_steps WHERE workflow_id = @wf_dept AND step_order = 2);
SET @step_dean = (SELECT step_id FROM workflow_steps WHERE workflow_id = @wf_dept AND step_order = 3);

-- Actions: approve=1, reject=2, resubmit=3
INSERT INTO workflow_transitions (step_id, action_id, to_step_id, resulting_status) VALUES
    -- Dept Coordinator Review
    (@step_cord, 1, @step_hod,  'pending'),      -- approve → advance to HOD
    (@step_cord, 2, NULL,       'rejected'),     -- reject  → rejected
    
    -- HOD Review
    (@step_hod,  1, @step_dean, 'pending'),      -- approve → advance to Dean
    (@step_hod,  2, NULL,       'rejected'),     -- reject  → rejected
    
    -- R&D Dean Review
    (@step_dean, 1, NULL,       'accepted'),     -- approve → final accept
    (@step_dean, 2, NULL,       'rejected');     -- reject  → rejected

-- Resubmit transitions (uploader only, from rejected state)
-- Always go back to the first step (Dept Coordinator)
INSERT INTO workflow_transitions (step_id, action_id, to_step_id, resulting_status) VALUES
    (@step_cord, 3, @step_cord, 'pending'),      -- resubmit from step 1
    (@step_hod,  3, @step_cord, 'pending'),      -- resubmit from step 2
    (@step_dean, 3, @step_cord, 'pending');      -- resubmit from step 3


-- Workflow B: central (Central Coordinator uploads → R&D Dean reviews → Accepted)
INSERT INTO workflows (workflow_key, label, description) VALUES
    ('central', 'Central Event Review',
     'Central Coordinator uploads → R&D Dean reviews (global scope) → Accepted');

SET @wf_central = LAST_INSERT_ID();

INSERT INTO workflow_steps (workflow_id, step_order, step_label, responsible_role_id, scope) VALUES
    (@wf_central, 1, 'R&D Dean Review', 8, 'global');

SET @step_central_dean = (SELECT step_id FROM workflow_steps WHERE workflow_id = @wf_central AND step_order = 1);

INSERT INTO workflow_transitions (step_id, action_id, to_step_id, resulting_status) VALUES
    (@step_central_dean, 1, NULL, 'accepted'),   -- approve → accepted
    (@step_central_dean, 2, NULL, 'rejected'),   -- reject  → rejected
    (@step_central_dean, 3, @step_central_dean, 'pending'); -- resubmit → back to step 1


-- Workflow C: auto_accept (zero-step, auto-accept on upload)
INSERT INTO workflows (workflow_key, label, description) VALUES
    ('auto_accept', 'No Approval Required',
     'Documents are accepted immediately upon upload. No review steps.');
-- No steps or transitions for auto_accept.


-- ============================================================
-- STEP 5: Update document_types.workflow_key
-- ============================================================

UPDATE document_types SET workflow_key = 'department_standard'
    WHERE workflow_key = 'department';
-- 'central' already matches the new workflow key


-- ============================================================
-- STEP 6: Re-add foreign keys on documents and document_actions
-- ============================================================

ALTER TABLE documents
    ADD CONSTRAINT fk_documents_current_step
    FOREIGN KEY (current_step) REFERENCES workflow_steps(step_id);

ALTER TABLE document_actions
    ADD CONSTRAINT fk_doc_actions_step
    FOREIGN KEY (step_id) REFERENCES workflow_steps(step_id);

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
-- VERIFICATION QUERIES
-- ============================================================
-- SELECT COUNT(*) FROM workflows;            -- expect 3
-- SELECT COUNT(*) FROM workflow_steps;       -- expect 3
-- SELECT COUNT(*) FROM workflow_actions;     -- expect 3
-- SELECT COUNT(*) FROM workflow_transitions; -- expect 9
-- SELECT workflow_key FROM document_types GROUP BY workflow_key;
--   expect: 'department_standard', 'central'
