<?php
require_once __DIR__ . '/core/bootstrap.php';
global $conn;

$sql = "
CREATE TABLE IF NOT EXISTS `naac_submissions` (
  `submission_id` int(11) NOT NULL AUTO_INCREMENT,
  `dept_id` int(11) NOT NULL,
  `academic_year` varchar(20) NOT NULL,
  `status` varchar(50) DEFAULT 'Draft',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`submission_id`),
  UNIQUE KEY `dept_year` (`dept_id`,`academic_year`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";
$conn->query($sql);

$sql = "
CREATE TABLE IF NOT EXISTS `naac_criteria_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `submission_id` int(11) NOT NULL,
  `criterion_number` int(11) NOT NULL,
  `data_json` longtext NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sub_crit` (`submission_id`,`criterion_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";
$conn->query($sql);

echo "NAAC tables created successfully.\n";
