-- Migration 008: Add status and dept_id+status indexes to documents table
-- Optimizes workflow state filtering, dashboard counts, and document listing queries.

ALTER TABLE `documents` 
    ADD INDEX `idx_status` (`status`), 
    ADD INDEX `idx_dept_status` (`dept_id`, `status`);
