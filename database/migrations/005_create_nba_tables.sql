CREATE TABLE IF NOT EXISTS `nba_submissions` (
    `submission_id` INT(11) NOT NULL AUTO_INCREMENT,
    `dept_id` INT(11) NOT NULL,
    `academic_year` VARCHAR(20) NOT NULL,
    `status` ENUM('Draft', 'Submitted', 'Approved') NOT NULL DEFAULT 'Draft',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`submission_id`),
    UNIQUE KEY `uk_dept_year` (`dept_id`, `academic_year`),
    CONSTRAINT `fk_nba_sub_dept` FOREIGN KEY (`dept_id`) REFERENCES `departments` (`dept_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `nba_criteria_data` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `submission_id` INT(11) NOT NULL,
    `criterion_number` INT(11) NOT NULL,
    `data_json` LONGTEXT,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_sub_crit` (`submission_id`, `criterion_number`),
    CONSTRAINT `fk_nba_data_sub` FOREIGN KEY (`submission_id`) REFERENCES `nba_submissions` (`submission_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `nba_criteria_files` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `submission_id` INT(11) NOT NULL,
    `criterion_number` INT(11) NOT NULL,
    `subsection` VARCHAR(20) NOT NULL,
    `file_id` INT(11) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    CONSTRAINT `fk_nba_file_sub` FOREIGN KEY (`submission_id`) REFERENCES `nba_submissions` (`submission_id`) ON DELETE CASCADE,
    CONSTRAINT `fk_nba_file_fid` FOREIGN KEY (`file_id`) REFERENCES `document_files` (`file_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
