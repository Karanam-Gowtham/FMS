-- Add department column to reg_dept_cord table
ALTER TABLE `reg_dept_cord` ADD COLUMN `department` varchar(100) DEFAULT NULL AFTER `userid`;

-- Update existing records with department values
UPDATE `reg_dept_cord` SET `department` = 'CSE' WHERE `userid` = 'dept_cord1';
UPDATE `reg_dept_cord` SET `department` = 'AIML' WHERE `userid` = 'dept_cord2';
UPDATE `reg_dept_cord` SET `department` = 'AIDS' WHERE `userid` = 'dept_cord3';
UPDATE `reg_dept_cord` SET `department` = 'IT' WHERE `userid` = 'dept_cord4';
UPDATE `reg_dept_cord` SET `department` = 'ECE' WHERE `userid` = 'dept_cord5';
UPDATE `reg_dept_cord` SET `department` = 'EEE' WHERE `userid` = 'dept_cord6';
UPDATE `reg_dept_cord` SET `department` = 'MECH' WHERE `userid` = 'dept_cord7';
UPDATE `reg_dept_cord` SET `department` = 'CIVIL' WHERE `userid` = 'dept_cord8';
UPDATE `reg_dept_cord` SET `department` = 'BSH' WHERE `userid` = 'dept_cord9';
-- dept_cord10 is left without a department
