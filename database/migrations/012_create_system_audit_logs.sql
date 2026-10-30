-- Migration 012: Create system_audit_logs table for institutional administration audit trail
-- Tracks security events, administrative role modifications, and permanent purges.

CREATE TABLE IF NOT EXISTS `system_audit_logs` (
    `log_id` INT AUTO_INCREMENT PRIMARY KEY,
    `action` VARCHAR(60) NOT NULL,
    `actor_id` INT NOT NULL,
    `target_type` VARCHAR(50) NOT NULL DEFAULT 'system',
    `target_id` INT NULL,
    `details` TEXT NULL,
    `ip_address` VARCHAR(45) NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY `idx_actor` (`actor_id`),
    KEY `idx_action` (`action`),
    KEY `idx_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
