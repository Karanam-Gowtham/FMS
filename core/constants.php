<?php
/**
 * FMS Application Constants
 * 
 * All role IDs, status values, and configuration constants.
 * These must match the `roles` table in the database.
 * Do NOT hard-code role names elsewhere in the application.
 */

// Role IDs (must match roles.role_id)
define('ROLE_ADMIN',               1);
define('ROLE_IQAC',                2);
define('ROLE_HOD',                 3);
define('ROLE_FACULTY',             4);
define('ROLE_DEPT_COORDINATOR',    5);
define('ROLE_CENTRAL_COORDINATOR', 6);
define('ROLE_JUNIOR_ASSISTANT',    7);
define('ROLE_RND_DEAN',            8);

// Document status values (3-state model)
// 'pending'  — awaiting action at current_step
// 'rejected' — rejected, uploader can resubmit
// 'accepted' — passed all steps, terminal state
define('DOC_STATUS_PENDING',    'pending');
define('DOC_STATUS_REJECTED',   'rejected');
define('DOC_STATUS_ACCEPTED',   'accepted');

// Roles that may self-register publicly
define('ROLE_STUDENT',             9);
define('SELF_REGISTER_ROLES', [ROLE_FACULTY, ROLE_STUDENT]);

// NOTE: Workflow approval roles are NOT hard-coded here.
// They are determined by workflow_steps.responsible_role_id in the database.

// Session key for canonical auth context
define('SESSION_AUTH_KEY', '_fms_auth');
