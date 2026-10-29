-- Migration 010: Ensure is_academic column exists and mark non-academic cells/committees
-- Safe and idempotent to execute on any MySQL/MariaDB environment

-- Step 1: Ensure column `is_academic` exists on `departments`
ALTER TABLE `departments` ADD COLUMN IF NOT EXISTS `is_academic` TINYINT(1) DEFAULT 1;

-- Step 2: Mark all non-academic cells, clubs, and committees as is_academic = 0
UPDATE `departments` 
SET `is_academic` = 0 
WHERE `dept_name` IN (
    'Antiragging', 'Clubs', 'Exam_Section', 'IIC', 'IQAC', 
    'NAAC', 'NBA', 'NCC', 'NSS', 'PASH', 'PE', 'PG', 
    'R&D', 'SAC', 'Sports', 'Women_Empowerment'
);

-- Step 3: Ensure all teaching academic departments are is_academic = 1
UPDATE `departments` 
SET `is_academic` = 1 
WHERE `dept_name` IN (
    'CSE', 'AIDS', 'AIML', 'ECE', 'EEE', 'MECH', 
    'CIVIL', 'IT', 'BSH', 'CSE-CS', 'Mathematics', 'MatheMatics', 'Physics', 'Chemistry'
);

-- Step 4: Normalize capitalization of Mathematics
UPDATE `departments` 
SET `dept_name` = 'Mathematics' 
WHERE `dept_name` = 'MatheMatics';
