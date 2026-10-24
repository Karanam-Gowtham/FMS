-- Migration 009: Add Complete University institutional entity to departments
INSERT INTO `departments` (`dept_name`)
SELECT 'Complete University'
WHERE NOT EXISTS (
    SELECT 1 FROM `departments` WHERE `dept_name` = 'Complete University'
);
