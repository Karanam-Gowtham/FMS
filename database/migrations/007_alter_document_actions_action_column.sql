-- Migration 007: Expand document_actions action column to VARCHAR(50)
-- Ensures audit logging of all workflow transitions and updates without ENUM truncation.

ALTER TABLE `document_actions` MODIFY `action` VARCHAR(50) NOT NULL DEFAULT 'uploaded';
