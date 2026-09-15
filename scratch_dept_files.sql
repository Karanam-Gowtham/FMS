CREATE TABLE IF NOT EXISTS `dept_files` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `username` varchar(300) NOT NULL,
  `dept` varchar(300) NOT NULL,
  `academic_year` varchar(100) NOT NULL,
  `file_type` varchar(400) NOT NULL,
  `sub_file_type` varchar(600) NOT NULL,
  `file_name` varchar(400) NOT NULL,
  `file_path` varchar(400) NOT NULL,
  `semester` int(11) DEFAULT NULL,
  `review_period` varchar(50) DEFAULT NULL,
  `study_year` int(11) DEFAULT NULL,
  `meeting_no` int(11) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'Pending HOD',
  `rejection_reason` text DEFAULT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
