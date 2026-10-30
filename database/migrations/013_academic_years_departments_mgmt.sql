-- Migration 013: Academic Years & Department Settings Management Enhancements
-- Safe and idempotent to execute on MySQL 8.0+ and MariaDB 10.2+

-- Step 1: Add is_current flag and created_at to academic_years
ALTER TABLE `academic_years` ADD COLUMN IF NOT EXISTS `is_current` TINYINT(1) NOT NULL DEFAULT 0;
ALTER TABLE `academic_years` ADD COLUMN IF NOT EXISTS `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP;

-- Step 2: Add created_at timestamp to departments
ALTER TABLE `departments` ADD COLUMN IF NOT EXISTS `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP;

-- Step 3: Default active academic year to '2025-26' if none is currently active
UPDATE `academic_years` 
SET `is_current` = 1 
WHERE `year_label` = '2025-26';

-- Step 4: Ensure exactly one academic year is active
UPDATE `academic_years`
SET `is_current` = 0
WHERE `year_label` != '2025-26' 
  AND (SELECT COUNT(*) FROM (SELECT * FROM `academic_years` WHERE `is_current` = 1) AS t) > 1;
