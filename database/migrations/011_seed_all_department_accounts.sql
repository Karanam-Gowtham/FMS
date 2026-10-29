-- Migration 011: Seed institutional accounts for all academic departments and central roles
-- Unified password: Admin@123 for all accounts
-- Safe for live server execution (uses INSERT IGNORE and dynamic user_id subqueries)

SET FOREIGN_KEY_CHECKS = 0;

-- 1. Ensure all users exist with Admin@123 password
INSERT INTO `users` (`full_name`, `email`, `password`, `status`, `created_at`) VALUES
('System Administrator', 'admin@gmrit.edu.in', '$2y$10$PwoOS7qwkTpUBFoIx//nx.Ost.I/SrLdLUXjtqn8IeXJDrhbvKMwu', 'active', NOW()),
('IQAC Coordinator', 'iqac@gmrit.edu.in', '$2y$10$PwoOS7qwkTpUBFoIx//nx.Ost.I/SrLdLUXjtqn8IeXJDrhbvKMwu', 'active', NOW()),
('Central Coordinator', 'central.coord@gmrit.edu.in', '$2y$10$PwoOS7qwkTpUBFoIx//nx.Ost.I/SrLdLUXjtqn8IeXJDrhbvKMwu', 'active', NOW()),
('Dean R&D', 'rnd.dean@gmrit.edu.in', '$2y$10$PwoOS7qwkTpUBFoIx//nx.Ost.I/SrLdLUXjtqn8IeXJDrhbvKMwu', 'active', NOW()),
('Dr. HOD CSE', 'hod.cse@gmrit.edu.in', '$2y$10$PwoOS7qwkTpUBFoIx//nx.Ost.I/SrLdLUXjtqn8IeXJDrhbvKMwu', 'active', NOW()),
('Faculty Member CSE', 'faculty.cse@gmrit.edu.in', '$2y$10$PwoOS7qwkTpUBFoIx//nx.Ost.I/SrLdLUXjtqn8IeXJDrhbvKMwu', 'active', NOW()),
('CSE Dept Coordinator', 'coord.cse@gmrit.edu.in', '$2y$10$PwoOS7qwkTpUBFoIx//nx.Ost.I/SrLdLUXjtqn8IeXJDrhbvKMwu', 'active', NOW()),
('Junior Assistant CSE', 'ja.cse@gmrit.edu.in', '$2y$10$PwoOS7qwkTpUBFoIx//nx.Ost.I/SrLdLUXjtqn8IeXJDrhbvKMwu', 'active', NOW()),
('Student CSE', 'student.cse@gmrit.edu.in', '$2y$10$PwoOS7qwkTpUBFoIx//nx.Ost.I/SrLdLUXjtqn8IeXJDrhbvKMwu', 'active', NOW()),
('Dr. HOD AIDS', 'hod.aids@gmrit.edu.in', '$2y$10$PwoOS7qwkTpUBFoIx//nx.Ost.I/SrLdLUXjtqn8IeXJDrhbvKMwu', 'active', NOW()),
('Faculty Member AIDS', 'faculty.aids@gmrit.edu.in', '$2y$10$PwoOS7qwkTpUBFoIx//nx.Ost.I/SrLdLUXjtqn8IeXJDrhbvKMwu', 'active', NOW()),
('AIDS Dept Coordinator', 'coord.aids@gmrit.edu.in', '$2y$10$PwoOS7qwkTpUBFoIx//nx.Ost.I/SrLdLUXjtqn8IeXJDrhbvKMwu', 'active', NOW()),
('Dr. HOD AIML', 'hod.aiml@gmrit.edu.in', '$2y$10$PwoOS7qwkTpUBFoIx//nx.Ost.I/SrLdLUXjtqn8IeXJDrhbvKMwu', 'active', NOW()),
('Faculty Member AIML', 'faculty.aiml@gmrit.edu.in', '$2y$10$PwoOS7qwkTpUBFoIx//nx.Ost.I/SrLdLUXjtqn8IeXJDrhbvKMwu', 'active', NOW()),
('AIML Dept Coordinator', 'coord.aiml@gmrit.edu.in', '$2y$10$PwoOS7qwkTpUBFoIx//nx.Ost.I/SrLdLUXjtqn8IeXJDrhbvKMwu', 'active', NOW()),
('Dr. HOD ECE', 'hod.ece@gmrit.edu.in', '$2y$10$PwoOS7qwkTpUBFoIx//nx.Ost.I/SrLdLUXjtqn8IeXJDrhbvKMwu', 'active', NOW()),
('Faculty Member ECE', 'faculty.ece@gmrit.edu.in', '$2y$10$PwoOS7qwkTpUBFoIx//nx.Ost.I/SrLdLUXjtqn8IeXJDrhbvKMwu', 'active', NOW()),
('ECE Dept Coordinator', 'coord.ece@gmrit.edu.in', '$2y$10$PwoOS7qwkTpUBFoIx//nx.Ost.I/SrLdLUXjtqn8IeXJDrhbvKMwu', 'active', NOW()),
('Dr. HOD EEE', 'hod.eee@gmrit.edu.in', '$2y$10$PwoOS7qwkTpUBFoIx//nx.Ost.I/SrLdLUXjtqn8IeXJDrhbvKMwu', 'active', NOW()),
('Faculty Member EEE', 'faculty.eee@gmrit.edu.in', '$2y$10$PwoOS7qwkTpUBFoIx//nx.Ost.I/SrLdLUXjtqn8IeXJDrhbvKMwu', 'active', NOW()),
('EEE Dept Coordinator', 'coord.eee@gmrit.edu.in', '$2y$10$PwoOS7qwkTpUBFoIx//nx.Ost.I/SrLdLUXjtqn8IeXJDrhbvKMwu', 'active', NOW()),
('Dr. HOD MECH', 'hod.mech@gmrit.edu.in', '$2y$10$PwoOS7qwkTpUBFoIx//nx.Ost.I/SrLdLUXjtqn8IeXJDrhbvKMwu', 'active', NOW()),
('Faculty Member MECH', 'faculty.mech@gmrit.edu.in', '$2y$10$PwoOS7qwkTpUBFoIx//nx.Ost.I/SrLdLUXjtqn8IeXJDrhbvKMwu', 'active', NOW()),
('MECH Dept Coordinator', 'coord.mech@gmrit.edu.in', '$2y$10$PwoOS7qwkTpUBFoIx//nx.Ost.I/SrLdLUXjtqn8IeXJDrhbvKMwu', 'active', NOW()),
('Dr. HOD CIVIL', 'hod.civil@gmrit.edu.in', '$2y$10$PwoOS7qwkTpUBFoIx//nx.Ost.I/SrLdLUXjtqn8IeXJDrhbvKMwu', 'active', NOW()),
('Faculty Member CIVIL', 'faculty.civil@gmrit.edu.in', '$2y$10$PwoOS7qwkTpUBFoIx//nx.Ost.I/SrLdLUXjtqn8IeXJDrhbvKMwu', 'active', NOW()),
('CIVIL Dept Coordinator', 'coord.civil@gmrit.edu.in', '$2y$10$PwoOS7qwkTpUBFoIx//nx.Ost.I/SrLdLUXjtqn8IeXJDrhbvKMwu', 'active', NOW()),
('Dr. HOD IT', 'hod.it@gmrit.edu.in', '$2y$10$PwoOS7qwkTpUBFoIx//nx.Ost.I/SrLdLUXjtqn8IeXJDrhbvKMwu', 'active', NOW()),
('Faculty Member IT', 'faculty.it@gmrit.edu.in', '$2y$10$PwoOS7qwkTpUBFoIx//nx.Ost.I/SrLdLUXjtqn8IeXJDrhbvKMwu', 'active', NOW()),
('IT Dept Coordinator', 'coord.it@gmrit.edu.in', '$2y$10$PwoOS7qwkTpUBFoIx//nx.Ost.I/SrLdLUXjtqn8IeXJDrhbvKMwu', 'active', NOW()),
('Dr. HOD BSH', 'hod.bsh@gmrit.edu.in', '$2y$10$PwoOS7qwkTpUBFoIx//nx.Ost.I/SrLdLUXjtqn8IeXJDrhbvKMwu', 'active', NOW()),
('Faculty Member BSH', 'faculty.bsh@gmrit.edu.in', '$2y$10$PwoOS7qwkTpUBFoIx//nx.Ost.I/SrLdLUXjtqn8IeXJDrhbvKMwu', 'active', NOW()),
('BSH Dept Coordinator', 'coord.bsh@gmrit.edu.in', '$2y$10$PwoOS7qwkTpUBFoIx//nx.Ost.I/SrLdLUXjtqn8IeXJDrhbvKMwu', 'active', NOW()),
('Dr. HOD CSE-CS', 'hod.csecs@gmrit.edu.in', '$2y$10$PwoOS7qwkTpUBFoIx//nx.Ost.I/SrLdLUXjtqn8IeXJDrhbvKMwu', 'active', NOW()),
('Faculty Member CSE-CS', 'faculty.csecs@gmrit.edu.in', '$2y$10$PwoOS7qwkTpUBFoIx//nx.Ost.I/SrLdLUXjtqn8IeXJDrhbvKMwu', 'active', NOW()),
('CSE-CS Dept Coordinator', 'coord.csecs@gmrit.edu.in', '$2y$10$PwoOS7qwkTpUBFoIx//nx.Ost.I/SrLdLUXjtqn8IeXJDrhbvKMwu', 'active', NOW()),
('Dr. HOD Mathematics', 'hod.maths@gmrit.edu.in', '$2y$10$PwoOS7qwkTpUBFoIx//nx.Ost.I/SrLdLUXjtqn8IeXJDrhbvKMwu', 'active', NOW()),
('Faculty Member Mathematics', 'faculty.maths@gmrit.edu.in', '$2y$10$PwoOS7qwkTpUBFoIx//nx.Ost.I/SrLdLUXjtqn8IeXJDrhbvKMwu', 'active', NOW()),
('Dr. HOD Physics', 'hod.physics@gmrit.edu.in', '$2y$10$PwoOS7qwkTpUBFoIx//nx.Ost.I/SrLdLUXjtqn8IeXJDrhbvKMwu', 'active', NOW()),
('Faculty Member Physics', 'faculty.physics@gmrit.edu.in', '$2y$10$PwoOS7qwkTpUBFoIx//nx.Ost.I/SrLdLUXjtqn8IeXJDrhbvKMwu', 'active', NOW()),
('Dr. HOD Chemistry', 'hod.chemistry@gmrit.edu.in', '$2y$10$PwoOS7qwkTpUBFoIx//nx.Ost.I/SrLdLUXjtqn8IeXJDrhbvKMwu', 'active', NOW()),
('Faculty Member Chemistry', 'faculty.chemistry@gmrit.edu.in', '$2y$10$PwoOS7qwkTpUBFoIx//nx.Ost.I/SrLdLUXjtqn8IeXJDrhbvKMwu', 'active', NOW())
ON DUPLICATE KEY UPDATE `full_name` = VALUES(`full_name`), `password` = VALUES(`password`), `status` = 'active';

-- 2. Assign Roles dynamically (Safe against duplicate runs)
INSERT IGNORE INTO `user_roles` (`user_id`, `role_id`, `dept_id`)
SELECT u.user_id, 1, NULL FROM `users` u WHERE u.email = 'admin@gmrit.edu.in';
INSERT IGNORE INTO `user_roles` (`user_id`, `role_id`, `dept_id`)
SELECT u.user_id, 2, NULL FROM `users` u WHERE u.email = 'iqac@gmrit.edu.in';
INSERT IGNORE INTO `user_roles` (`user_id`, `role_id`, `dept_id`)
SELECT u.user_id, 6, NULL FROM `users` u WHERE u.email = 'central.coord@gmrit.edu.in';
INSERT IGNORE INTO `user_roles` (`user_id`, `role_id`, `dept_id`)
SELECT u.user_id, 8, NULL FROM `users` u WHERE u.email = 'rnd.dean@gmrit.edu.in';
INSERT IGNORE INTO `user_roles` (`user_id`, `role_id`, `dept_id`)
SELECT u.user_id, 3, 1 FROM `users` u WHERE u.email = 'hod.cse@gmrit.edu.in';
INSERT IGNORE INTO `user_roles` (`user_id`, `role_id`, `dept_id`)
SELECT u.user_id, 4, 1 FROM `users` u WHERE u.email = 'faculty.cse@gmrit.edu.in';
INSERT IGNORE INTO `user_roles` (`user_id`, `role_id`, `dept_id`)
SELECT u.user_id, 5, 1 FROM `users` u WHERE u.email = 'coord.cse@gmrit.edu.in';
INSERT IGNORE INTO `user_roles` (`user_id`, `role_id`, `dept_id`)
SELECT u.user_id, 7, 1 FROM `users` u WHERE u.email = 'ja.cse@gmrit.edu.in';
INSERT IGNORE INTO `user_roles` (`user_id`, `role_id`, `dept_id`)
SELECT u.user_id, 9, 1 FROM `users` u WHERE u.email = 'student.cse@gmrit.edu.in';
INSERT IGNORE INTO `user_roles` (`user_id`, `role_id`, `dept_id`)
SELECT u.user_id, 3, 2 FROM `users` u WHERE u.email = 'hod.aids@gmrit.edu.in';
INSERT IGNORE INTO `user_roles` (`user_id`, `role_id`, `dept_id`)
SELECT u.user_id, 4, 2 FROM `users` u WHERE u.email = 'faculty.aids@gmrit.edu.in';
INSERT IGNORE INTO `user_roles` (`user_id`, `role_id`, `dept_id`)
SELECT u.user_id, 5, 2 FROM `users` u WHERE u.email = 'coord.aids@gmrit.edu.in';
INSERT IGNORE INTO `user_roles` (`user_id`, `role_id`, `dept_id`)
SELECT u.user_id, 3, 3 FROM `users` u WHERE u.email = 'hod.aiml@gmrit.edu.in';
INSERT IGNORE INTO `user_roles` (`user_id`, `role_id`, `dept_id`)
SELECT u.user_id, 4, 3 FROM `users` u WHERE u.email = 'faculty.aiml@gmrit.edu.in';
INSERT IGNORE INTO `user_roles` (`user_id`, `role_id`, `dept_id`)
SELECT u.user_id, 5, 3 FROM `users` u WHERE u.email = 'coord.aiml@gmrit.edu.in';
INSERT IGNORE INTO `user_roles` (`user_id`, `role_id`, `dept_id`)
SELECT u.user_id, 3, 4 FROM `users` u WHERE u.email = 'hod.ece@gmrit.edu.in';
INSERT IGNORE INTO `user_roles` (`user_id`, `role_id`, `dept_id`)
SELECT u.user_id, 4, 4 FROM `users` u WHERE u.email = 'faculty.ece@gmrit.edu.in';
INSERT IGNORE INTO `user_roles` (`user_id`, `role_id`, `dept_id`)
SELECT u.user_id, 5, 4 FROM `users` u WHERE u.email = 'coord.ece@gmrit.edu.in';
INSERT IGNORE INTO `user_roles` (`user_id`, `role_id`, `dept_id`)
SELECT u.user_id, 3, 5 FROM `users` u WHERE u.email = 'hod.eee@gmrit.edu.in';
INSERT IGNORE INTO `user_roles` (`user_id`, `role_id`, `dept_id`)
SELECT u.user_id, 4, 5 FROM `users` u WHERE u.email = 'faculty.eee@gmrit.edu.in';
INSERT IGNORE INTO `user_roles` (`user_id`, `role_id`, `dept_id`)
SELECT u.user_id, 5, 5 FROM `users` u WHERE u.email = 'coord.eee@gmrit.edu.in';
INSERT IGNORE INTO `user_roles` (`user_id`, `role_id`, `dept_id`)
SELECT u.user_id, 3, 6 FROM `users` u WHERE u.email = 'hod.mech@gmrit.edu.in';
INSERT IGNORE INTO `user_roles` (`user_id`, `role_id`, `dept_id`)
SELECT u.user_id, 4, 6 FROM `users` u WHERE u.email = 'faculty.mech@gmrit.edu.in';
INSERT IGNORE INTO `user_roles` (`user_id`, `role_id`, `dept_id`)
SELECT u.user_id, 5, 6 FROM `users` u WHERE u.email = 'coord.mech@gmrit.edu.in';
INSERT IGNORE INTO `user_roles` (`user_id`, `role_id`, `dept_id`)
SELECT u.user_id, 3, 7 FROM `users` u WHERE u.email = 'hod.civil@gmrit.edu.in';
INSERT IGNORE INTO `user_roles` (`user_id`, `role_id`, `dept_id`)
SELECT u.user_id, 4, 7 FROM `users` u WHERE u.email = 'faculty.civil@gmrit.edu.in';
INSERT IGNORE INTO `user_roles` (`user_id`, `role_id`, `dept_id`)
SELECT u.user_id, 5, 7 FROM `users` u WHERE u.email = 'coord.civil@gmrit.edu.in';
INSERT IGNORE INTO `user_roles` (`user_id`, `role_id`, `dept_id`)
SELECT u.user_id, 3, 8 FROM `users` u WHERE u.email = 'hod.it@gmrit.edu.in';
INSERT IGNORE INTO `user_roles` (`user_id`, `role_id`, `dept_id`)
SELECT u.user_id, 4, 8 FROM `users` u WHERE u.email = 'faculty.it@gmrit.edu.in';
INSERT IGNORE INTO `user_roles` (`user_id`, `role_id`, `dept_id`)
SELECT u.user_id, 5, 8 FROM `users` u WHERE u.email = 'coord.it@gmrit.edu.in';
INSERT IGNORE INTO `user_roles` (`user_id`, `role_id`, `dept_id`)
SELECT u.user_id, 3, 9 FROM `users` u WHERE u.email = 'hod.bsh@gmrit.edu.in';
INSERT IGNORE INTO `user_roles` (`user_id`, `role_id`, `dept_id`)
SELECT u.user_id, 4, 9 FROM `users` u WHERE u.email = 'faculty.bsh@gmrit.edu.in';
INSERT IGNORE INTO `user_roles` (`user_id`, `role_id`, `dept_id`)
SELECT u.user_id, 5, 9 FROM `users` u WHERE u.email = 'coord.bsh@gmrit.edu.in';
INSERT IGNORE INTO `user_roles` (`user_id`, `role_id`, `dept_id`)
SELECT u.user_id, 3, 10 FROM `users` u WHERE u.email = 'hod.csecs@gmrit.edu.in';
INSERT IGNORE INTO `user_roles` (`user_id`, `role_id`, `dept_id`)
SELECT u.user_id, 4, 10 FROM `users` u WHERE u.email = 'faculty.csecs@gmrit.edu.in';
INSERT IGNORE INTO `user_roles` (`user_id`, `role_id`, `dept_id`)
SELECT u.user_id, 5, 10 FROM `users` u WHERE u.email = 'coord.csecs@gmrit.edu.in';
INSERT IGNORE INTO `user_roles` (`user_id`, `role_id`, `dept_id`)
SELECT u.user_id, 3, 14 FROM `users` u WHERE u.email = 'hod.maths@gmrit.edu.in';
INSERT IGNORE INTO `user_roles` (`user_id`, `role_id`, `dept_id`)
SELECT u.user_id, 4, 14 FROM `users` u WHERE u.email = 'faculty.maths@gmrit.edu.in';
INSERT IGNORE INTO `user_roles` (`user_id`, `role_id`, `dept_id`)
SELECT u.user_id, 3, 15 FROM `users` u WHERE u.email = 'hod.physics@gmrit.edu.in';
INSERT IGNORE INTO `user_roles` (`user_id`, `role_id`, `dept_id`)
SELECT u.user_id, 4, 15 FROM `users` u WHERE u.email = 'faculty.physics@gmrit.edu.in';
INSERT IGNORE INTO `user_roles` (`user_id`, `role_id`, `dept_id`)
SELECT u.user_id, 3, 16 FROM `users` u WHERE u.email = 'hod.chemistry@gmrit.edu.in';
INSERT IGNORE INTO `user_roles` (`user_id`, `role_id`, `dept_id`)
SELECT u.user_id, 4, 16 FROM `users` u WHERE u.email = 'faculty.chemistry@gmrit.edu.in';

-- 3. Create User Profiles dynamically (Safe against duplicate runs)
INSERT INTO `user_profiles` (`user_id`, `per_no`, `pan_no`, `apaar_id`, `highest_degree`, `university`, `specialization`, `doj_institution`, `doj_department`, `experience_years`, `designation_joining`, `designation_present`, `association_nature`, `is_currently_associated`)
SELECT u.user_id, 'ADM001', 'ABCDE0001F', 'APAAR-001', 'Ph.D', 'JNTUK', 'Computer Science', '2015-06-01', '2015-06-01', 12.0, 'System Administrator', 'System Administrator', 'Regular', 1
FROM `users` u WHERE u.email = 'admin@gmrit.edu.in'
ON DUPLICATE KEY UPDATE `designation_present` = VALUES(`designation_present`), `experience_years` = VALUES(`experience_years`);
INSERT INTO `user_profiles` (`user_id`, `per_no`, `pan_no`, `apaar_id`, `highest_degree`, `university`, `specialization`, `doj_institution`, `doj_department`, `experience_years`, `designation_joining`, `designation_present`, `association_nature`, `is_currently_associated`)
SELECT u.user_id, 'IQC001', 'ABCDE0002F', 'APAAR-002', 'Ph.D', 'Andhra University', 'Quality Assurance', '2016-07-01', '2016-07-01', 10.0, 'Professor', 'IQAC Coordinator', 'Regular', 1
FROM `users` u WHERE u.email = 'iqac@gmrit.edu.in'
ON DUPLICATE KEY UPDATE `designation_present` = VALUES(`designation_present`), `experience_years` = VALUES(`experience_years`);
INSERT INTO `user_profiles` (`user_id`, `per_no`, `pan_no`, `apaar_id`, `highest_degree`, `university`, `specialization`, `doj_institution`, `doj_department`, `experience_years`, `designation_joining`, `designation_present`, `association_nature`, `is_currently_associated`)
SELECT u.user_id, 'CCD001', 'ABCDE0003F', 'APAAR-003', 'Ph.D', 'JNTUK', 'Institutional Operations', '2017-06-01', '2017-06-01', 9.0, 'Associate Professor', 'Central Coordinator', 'Regular', 1
FROM `users` u WHERE u.email = 'central.coord@gmrit.edu.in'
ON DUPLICATE KEY UPDATE `designation_present` = VALUES(`designation_present`), `experience_years` = VALUES(`experience_years`);
INSERT INTO `user_profiles` (`user_id`, `per_no`, `pan_no`, `apaar_id`, `highest_degree`, `university`, `specialization`, `doj_institution`, `doj_department`, `experience_years`, `designation_joining`, `designation_present`, `association_nature`, `is_currently_associated`)
SELECT u.user_id, 'RND001', 'ABCDE0004F', 'APAAR-004', 'Ph.D', 'IIT Madras', 'Research & Innovation', '2013-05-15', '2013-05-15', 16.0, 'Professor', 'Dean R&D', 'Regular', 1
FROM `users` u WHERE u.email = 'rnd.dean@gmrit.edu.in'
ON DUPLICATE KEY UPDATE `designation_present` = VALUES(`designation_present`), `experience_years` = VALUES(`experience_years`);
INSERT INTO `user_profiles` (`user_id`, `per_no`, `pan_no`, `apaar_id`, `highest_degree`, `university`, `specialization`, `doj_institution`, `doj_department`, `experience_years`, `designation_joining`, `designation_present`, `association_nature`, `is_currently_associated`)
SELECT u.user_id, 'HOD_CSE', 'ABCDE0005F', 'APAAR-005', 'Ph.D', 'IIT Madras', 'Computer Science & Engineering', '2014-05-10', '2014-05-10', 15.0, 'Associate Professor', 'Professor & HOD', 'Regular', 1
FROM `users` u WHERE u.email = 'hod.cse@gmrit.edu.in'
ON DUPLICATE KEY UPDATE `designation_present` = VALUES(`designation_present`), `experience_years` = VALUES(`experience_years`);
INSERT INTO `user_profiles` (`user_id`, `per_no`, `pan_no`, `apaar_id`, `highest_degree`, `university`, `specialization`, `doj_institution`, `doj_department`, `experience_years`, `designation_joining`, `designation_present`, `association_nature`, `is_currently_associated`)
SELECT u.user_id, 'FAC_CSE', 'ABCDE0006F', 'APAAR-006', 'M.Tech', 'JNTUK', 'Software Engineering', '2019-08-01', '2019-08-01', 6.0, 'Assistant Professor', 'Assistant Professor', 'Regular', 1
FROM `users` u WHERE u.email = 'faculty.cse@gmrit.edu.in'
ON DUPLICATE KEY UPDATE `designation_present` = VALUES(`designation_present`), `experience_years` = VALUES(`experience_years`);
INSERT INTO `user_profiles` (`user_id`, `per_no`, `pan_no`, `apaar_id`, `highest_degree`, `university`, `specialization`, `doj_institution`, `doj_department`, `experience_years`, `designation_joining`, `designation_present`, `association_nature`, `is_currently_associated`)
SELECT u.user_id, 'CRD_CSE', 'ABCDE0007F', 'APAAR-007', 'M.Tech', 'JNTUK', 'Computer Networks', '2018-07-01', '2018-07-01', 7.0, 'Assistant Professor', 'Assistant Professor & Coordinator', 'Regular', 1
FROM `users` u WHERE u.email = 'coord.cse@gmrit.edu.in'
ON DUPLICATE KEY UPDATE `designation_present` = VALUES(`designation_present`), `experience_years` = VALUES(`experience_years`);
INSERT INTO `user_profiles` (`user_id`, `per_no`, `pan_no`, `apaar_id`, `highest_degree`, `university`, `specialization`, `doj_institution`, `doj_department`, `experience_years`, `designation_joining`, `designation_present`, `association_nature`, `is_currently_associated`)
SELECT u.user_id, 'JA_CSE', 'ABCDE0008F', 'APAAR-008', 'B.Com', 'Andhra University', 'Office Administration', '2020-01-15', '2020-01-15', 5.0, 'Junior Assistant', 'Junior Assistant', 'Regular', 1
FROM `users` u WHERE u.email = 'ja.cse@gmrit.edu.in'
ON DUPLICATE KEY UPDATE `designation_present` = VALUES(`designation_present`), `experience_years` = VALUES(`experience_years`);
INSERT INTO `user_profiles` (`user_id`, `per_no`, `pan_no`, `apaar_id`, `highest_degree`, `university`, `specialization`, `doj_institution`, `doj_department`, `experience_years`, `designation_joining`, `designation_present`, `association_nature`, `is_currently_associated`)
SELECT u.user_id, 'STU_CSE', 'ABCDE0009F', 'APAAR-009', 'B.Tech (Pursuing)', 'GMRIT', 'Computer Science', '2022-09-01', '2022-09-01', 0.0, 'Student', 'Student', 'Regular', 1
FROM `users` u WHERE u.email = 'student.cse@gmrit.edu.in'
ON DUPLICATE KEY UPDATE `designation_present` = VALUES(`designation_present`), `experience_years` = VALUES(`experience_years`);
INSERT INTO `user_profiles` (`user_id`, `per_no`, `pan_no`, `apaar_id`, `highest_degree`, `university`, `specialization`, `doj_institution`, `doj_department`, `experience_years`, `designation_joining`, `designation_present`, `association_nature`, `is_currently_associated`)
SELECT u.user_id, 'HOD_AIDS', 'ABCDE0010F', 'APAAR-010', 'Ph.D', 'IIT Kharagpur', 'Artificial Intelligence & Data Science', '2016-06-01', '2016-06-01', 12.0, 'Associate Professor', 'Professor & HOD', 'Regular', 1
FROM `users` u WHERE u.email = 'hod.aids@gmrit.edu.in'
ON DUPLICATE KEY UPDATE `designation_present` = VALUES(`designation_present`), `experience_years` = VALUES(`experience_years`);
INSERT INTO `user_profiles` (`user_id`, `per_no`, `pan_no`, `apaar_id`, `highest_degree`, `university`, `specialization`, `doj_institution`, `doj_department`, `experience_years`, `designation_joining`, `designation_present`, `association_nature`, `is_currently_associated`)
SELECT u.user_id, 'FAC_AIDS', 'ABCDE0011F', 'APAAR-011', 'M.Tech', 'JNTUK', 'Data Science & Big Data', '2020-07-01', '2020-07-01', 5.0, 'Assistant Professor', 'Assistant Professor', 'Regular', 1
FROM `users` u WHERE u.email = 'faculty.aids@gmrit.edu.in'
ON DUPLICATE KEY UPDATE `designation_present` = VALUES(`designation_present`), `experience_years` = VALUES(`experience_years`);
INSERT INTO `user_profiles` (`user_id`, `per_no`, `pan_no`, `apaar_id`, `highest_degree`, `university`, `specialization`, `doj_institution`, `doj_department`, `experience_years`, `designation_joining`, `designation_present`, `association_nature`, `is_currently_associated`)
SELECT u.user_id, 'CRD_AIDS', 'ABCDE0012F', 'APAAR-012', 'M.Tech', 'NIT Warangal', 'Machine Learning', '2019-06-15', '2019-06-15', 6.0, 'Assistant Professor', 'Assistant Professor & Coordinator', 'Regular', 1
FROM `users` u WHERE u.email = 'coord.aids@gmrit.edu.in'
ON DUPLICATE KEY UPDATE `designation_present` = VALUES(`designation_present`), `experience_years` = VALUES(`experience_years`);
INSERT INTO `user_profiles` (`user_id`, `per_no`, `pan_no`, `apaar_id`, `highest_degree`, `university`, `specialization`, `doj_institution`, `doj_department`, `experience_years`, `designation_joining`, `designation_present`, `association_nature`, `is_currently_associated`)
SELECT u.user_id, 'HOD_AIML', 'ABCDE0013F', 'APAAR-013', 'Ph.D', 'IIT Hyderabad', 'Deep Learning & Neural Networks', '2015-07-01', '2015-07-01', 13.0, 'Associate Professor', 'Professor & HOD', 'Regular', 1
FROM `users` u WHERE u.email = 'hod.aiml@gmrit.edu.in'
ON DUPLICATE KEY UPDATE `designation_present` = VALUES(`designation_present`), `experience_years` = VALUES(`experience_years`);
INSERT INTO `user_profiles` (`user_id`, `per_no`, `pan_no`, `apaar_id`, `highest_degree`, `university`, `specialization`, `doj_institution`, `doj_department`, `experience_years`, `designation_joining`, `designation_present`, `association_nature`, `is_currently_associated`)
SELECT u.user_id, 'FAC_AIML', 'ABCDE0014F', 'APAAR-014', 'M.Tech', 'Andhra University', 'Artificial Intelligence', '2020-08-01', '2020-08-01', 5.0, 'Assistant Professor', 'Assistant Professor', 'Regular', 1
FROM `users` u WHERE u.email = 'faculty.aiml@gmrit.edu.in'
ON DUPLICATE KEY UPDATE `designation_present` = VALUES(`designation_present`), `experience_years` = VALUES(`experience_years`);
INSERT INTO `user_profiles` (`user_id`, `per_no`, `pan_no`, `apaar_id`, `highest_degree`, `university`, `specialization`, `doj_institution`, `doj_department`, `experience_years`, `designation_joining`, `designation_present`, `association_nature`, `is_currently_associated`)
SELECT u.user_id, 'CRD_AIML', 'ABCDE0015F', 'APAAR-015', 'M.Tech', 'JNTUK', 'Computer Vision', '2019-07-01', '2019-07-01', 6.0, 'Assistant Professor', 'Assistant Professor & Coordinator', 'Regular', 1
FROM `users` u WHERE u.email = 'coord.aiml@gmrit.edu.in'
ON DUPLICATE KEY UPDATE `designation_present` = VALUES(`designation_present`), `experience_years` = VALUES(`experience_years`);
INSERT INTO `user_profiles` (`user_id`, `per_no`, `pan_no`, `apaar_id`, `highest_degree`, `university`, `specialization`, `doj_institution`, `doj_department`, `experience_years`, `designation_joining`, `designation_present`, `association_nature`, `is_currently_associated`)
SELECT u.user_id, 'HOD_ECE', 'ABCDE0016F', 'APAAR-016', 'Ph.D', 'IIT Roorkee', 'VLSI & Embedded Systems', '2013-06-01', '2013-06-01', 16.0, 'Associate Professor', 'Professor & HOD', 'Regular', 1
FROM `users` u WHERE u.email = 'hod.ece@gmrit.edu.in'
ON DUPLICATE KEY UPDATE `designation_present` = VALUES(`designation_present`), `experience_years` = VALUES(`experience_years`);
INSERT INTO `user_profiles` (`user_id`, `per_no`, `pan_no`, `apaar_id`, `highest_degree`, `university`, `specialization`, `doj_institution`, `doj_department`, `experience_years`, `designation_joining`, `designation_present`, `association_nature`, `is_currently_associated`)
SELECT u.user_id, 'FAC_ECE', 'ABCDE0017F', 'APAAR-017', 'M.Tech', 'JNTUK', 'Communication Systems', '2018-06-01', '2018-06-01', 7.0, 'Assistant Professor', 'Assistant Professor', 'Regular', 1
FROM `users` u WHERE u.email = 'faculty.ece@gmrit.edu.in'
ON DUPLICATE KEY UPDATE `designation_present` = VALUES(`designation_present`), `experience_years` = VALUES(`experience_years`);
INSERT INTO `user_profiles` (`user_id`, `per_no`, `pan_no`, `apaar_id`, `highest_degree`, `university`, `specialization`, `doj_institution`, `doj_department`, `experience_years`, `designation_joining`, `designation_present`, `association_nature`, `is_currently_associated`)
SELECT u.user_id, 'CRD_ECE', 'ABCDE0018F', 'APAAR-018', 'M.Tech', 'NIT Trichy', 'Signal Processing', '2017-07-01', '2017-07-01', 8.0, 'Assistant Professor', 'Assistant Professor & Coordinator', 'Regular', 1
FROM `users` u WHERE u.email = 'coord.ece@gmrit.edu.in'
ON DUPLICATE KEY UPDATE `designation_present` = VALUES(`designation_present`), `experience_years` = VALUES(`experience_years`);
INSERT INTO `user_profiles` (`user_id`, `per_no`, `pan_no`, `apaar_id`, `highest_degree`, `university`, `specialization`, `doj_institution`, `doj_department`, `experience_years`, `designation_joining`, `designation_present`, `association_nature`, `is_currently_associated`)
SELECT u.user_id, 'HOD_EEE', 'ABCDE0019F', 'APAAR-019', 'Ph.D', 'NIT Warangal', 'Power Systems & Renewable Energy', '2012-07-01', '2012-07-01', 17.0, 'Associate Professor', 'Professor & HOD', 'Regular', 1
FROM `users` u WHERE u.email = 'hod.eee@gmrit.edu.in'
ON DUPLICATE KEY UPDATE `designation_present` = VALUES(`designation_present`), `experience_years` = VALUES(`experience_years`);
INSERT INTO `user_profiles` (`user_id`, `per_no`, `pan_no`, `apaar_id`, `highest_degree`, `university`, `specialization`, `doj_institution`, `doj_department`, `experience_years`, `designation_joining`, `designation_present`, `association_nature`, `is_currently_associated`)
SELECT u.user_id, 'FAC_EEE', 'ABCDE0020F', 'APAAR-020', 'M.Tech', 'JNTUK', 'Power Electronics', '2019-06-01', '2019-06-01', 6.0, 'Assistant Professor', 'Assistant Professor', 'Regular', 1
FROM `users` u WHERE u.email = 'faculty.eee@gmrit.edu.in'
ON DUPLICATE KEY UPDATE `designation_present` = VALUES(`designation_present`), `experience_years` = VALUES(`experience_years`);
INSERT INTO `user_profiles` (`user_id`, `per_no`, `pan_no`, `apaar_id`, `highest_degree`, `university`, `specialization`, `doj_institution`, `doj_department`, `experience_years`, `designation_joining`, `designation_present`, `association_nature`, `is_currently_associated`)
SELECT u.user_id, 'CRD_EEE', 'ABCDE0021F', 'APAAR-021', 'M.Tech', 'Andhra University', 'Control Systems', '2018-07-01', '2018-07-01', 7.0, 'Assistant Professor', 'Assistant Professor & Coordinator', 'Regular', 1
FROM `users` u WHERE u.email = 'coord.eee@gmrit.edu.in'
ON DUPLICATE KEY UPDATE `designation_present` = VALUES(`designation_present`), `experience_years` = VALUES(`experience_years`);
INSERT INTO `user_profiles` (`user_id`, `per_no`, `pan_no`, `apaar_id`, `highest_degree`, `university`, `specialization`, `doj_institution`, `doj_department`, `experience_years`, `designation_joining`, `designation_present`, `association_nature`, `is_currently_associated`)
SELECT u.user_id, 'HOD_MECH', 'ABCDE0022F', 'APAAR-022', 'Ph.D', 'IIT Madras', 'Thermal Engineering & Robotics', '2011-06-01', '2011-06-01', 18.0, 'Associate Professor', 'Professor & HOD', 'Regular', 1
FROM `users` u WHERE u.email = 'hod.mech@gmrit.edu.in'
ON DUPLICATE KEY UPDATE `designation_present` = VALUES(`designation_present`), `experience_years` = VALUES(`experience_years`);
INSERT INTO `user_profiles` (`user_id`, `per_no`, `pan_no`, `apaar_id`, `highest_degree`, `university`, `specialization`, `doj_institution`, `doj_department`, `experience_years`, `designation_joining`, `designation_present`, `association_nature`, `is_currently_associated`)
SELECT u.user_id, 'FAC_MECH', 'ABCDE0023F', 'APAAR-023', 'M.Tech', 'JNTUK', 'Machine Design', '2018-06-01', '2018-06-01', 7.0, 'Assistant Professor', 'Assistant Professor', 'Regular', 1
FROM `users` u WHERE u.email = 'faculty.mech@gmrit.edu.in'
ON DUPLICATE KEY UPDATE `designation_present` = VALUES(`designation_present`), `experience_years` = VALUES(`experience_years`);
INSERT INTO `user_profiles` (`user_id`, `per_no`, `pan_no`, `apaar_id`, `highest_degree`, `university`, `specialization`, `doj_institution`, `doj_department`, `experience_years`, `designation_joining`, `designation_present`, `association_nature`, `is_currently_associated`)
SELECT u.user_id, 'CRD_MECH', 'ABCDE0024F', 'APAAR-024', 'M.Tech', 'NIT Rourkela', 'Manufacturing Science', '2017-07-01', '2017-07-01', 8.0, 'Assistant Professor', 'Assistant Professor & Coordinator', 'Regular', 1
FROM `users` u WHERE u.email = 'coord.mech@gmrit.edu.in'
ON DUPLICATE KEY UPDATE `designation_present` = VALUES(`designation_present`), `experience_years` = VALUES(`experience_years`);
INSERT INTO `user_profiles` (`user_id`, `per_no`, `pan_no`, `apaar_id`, `highest_degree`, `university`, `specialization`, `doj_institution`, `doj_department`, `experience_years`, `designation_joining`, `designation_present`, `association_nature`, `is_currently_associated`)
SELECT u.user_id, 'HOD_CIVIL', 'ABCDE0025F', 'APAAR-025', 'Ph.D', 'IIT Kharagpur', 'Structural & Geotechnical Engineering', '2013-05-01', '2013-05-01', 16.0, 'Associate Professor', 'Professor & HOD', 'Regular', 1
FROM `users` u WHERE u.email = 'hod.civil@gmrit.edu.in'
ON DUPLICATE KEY UPDATE `designation_present` = VALUES(`designation_present`), `experience_years` = VALUES(`experience_years`);
INSERT INTO `user_profiles` (`user_id`, `per_no`, `pan_no`, `apaar_id`, `highest_degree`, `university`, `specialization`, `doj_institution`, `doj_department`, `experience_years`, `designation_joining`, `designation_present`, `association_nature`, `is_currently_associated`)
SELECT u.user_id, 'FAC_CIVIL', 'ABCDE0026F', 'APAAR-026', 'M.Tech', 'JNTUK', 'Transportation Engineering', '2019-07-01', '2019-07-01', 6.0, 'Assistant Professor', 'Assistant Professor', 'Regular', 1
FROM `users` u WHERE u.email = 'faculty.civil@gmrit.edu.in'
ON DUPLICATE KEY UPDATE `designation_present` = VALUES(`designation_present`), `experience_years` = VALUES(`experience_years`);
INSERT INTO `user_profiles` (`user_id`, `per_no`, `pan_no`, `apaar_id`, `highest_degree`, `university`, `specialization`, `doj_institution`, `doj_department`, `experience_years`, `designation_joining`, `designation_present`, `association_nature`, `is_currently_associated`)
SELECT u.user_id, 'CRD_CIVIL', 'ABCDE0027F', 'APAAR-027', 'M.Tech', 'Andhra University', 'Environmental Engineering', '2018-08-01', '2018-08-01', 7.0, 'Assistant Professor', 'Assistant Professor & Coordinator', 'Regular', 1
FROM `users` u WHERE u.email = 'coord.civil@gmrit.edu.in'
ON DUPLICATE KEY UPDATE `designation_present` = VALUES(`designation_present`), `experience_years` = VALUES(`experience_years`);
INSERT INTO `user_profiles` (`user_id`, `per_no`, `pan_no`, `apaar_id`, `highest_degree`, `university`, `specialization`, `doj_institution`, `doj_department`, `experience_years`, `designation_joining`, `designation_present`, `association_nature`, `is_currently_associated`)
SELECT u.user_id, 'HOD_IT', 'ABCDE0028F', 'APAAR-028', 'Ph.D', 'Andhra University', 'Information Technology & Cloud Computing', '2014-06-01', '2014-06-01', 15.0, 'Associate Professor', 'Professor & HOD', 'Regular', 1
FROM `users` u WHERE u.email = 'hod.it@gmrit.edu.in'
ON DUPLICATE KEY UPDATE `designation_present` = VALUES(`designation_present`), `experience_years` = VALUES(`experience_years`);
INSERT INTO `user_profiles` (`user_id`, `per_no`, `pan_no`, `apaar_id`, `highest_degree`, `university`, `specialization`, `doj_institution`, `doj_department`, `experience_years`, `designation_joining`, `designation_present`, `association_nature`, `is_currently_associated`)
SELECT u.user_id, 'FAC_IT', 'ABCDE0029F', 'APAAR-029', 'M.Tech', 'JNTUK', 'Information Security', '2019-06-01', '2019-06-01', 6.0, 'Assistant Professor', 'Assistant Professor', 'Regular', 1
FROM `users` u WHERE u.email = 'faculty.it@gmrit.edu.in'
ON DUPLICATE KEY UPDATE `designation_present` = VALUES(`designation_present`), `experience_years` = VALUES(`experience_years`);
INSERT INTO `user_profiles` (`user_id`, `per_no`, `pan_no`, `apaar_id`, `highest_degree`, `university`, `specialization`, `doj_institution`, `doj_department`, `experience_years`, `designation_joining`, `designation_present`, `association_nature`, `is_currently_associated`)
SELECT u.user_id, 'CRD_IT', 'ABCDE0030F', 'APAAR-030', 'M.Tech', 'NIT Calicut', 'Data Engineering', '2018-07-01', '2018-07-01', 7.0, 'Assistant Professor', 'Assistant Professor & Coordinator', 'Regular', 1
FROM `users` u WHERE u.email = 'coord.it@gmrit.edu.in'
ON DUPLICATE KEY UPDATE `designation_present` = VALUES(`designation_present`), `experience_years` = VALUES(`experience_years`);
INSERT INTO `user_profiles` (`user_id`, `per_no`, `pan_no`, `apaar_id`, `highest_degree`, `university`, `specialization`, `doj_institution`, `doj_department`, `experience_years`, `designation_joining`, `designation_present`, `association_nature`, `is_currently_associated`)
SELECT u.user_id, 'HOD_BSH', 'ABCDE0031F', 'APAAR-031', 'Ph.D', 'Andhra University', 'Basic Sciences & Humanities', '2012-06-01', '2012-06-01', 17.0, 'Associate Professor', 'Professor & HOD', 'Regular', 1
FROM `users` u WHERE u.email = 'hod.bsh@gmrit.edu.in'
ON DUPLICATE KEY UPDATE `designation_present` = VALUES(`designation_present`), `experience_years` = VALUES(`experience_years`);
INSERT INTO `user_profiles` (`user_id`, `per_no`, `pan_no`, `apaar_id`, `highest_degree`, `university`, `specialization`, `doj_institution`, `doj_department`, `experience_years`, `designation_joining`, `designation_present`, `association_nature`, `is_currently_associated`)
SELECT u.user_id, 'FAC_BSH', 'ABCDE0032F', 'APAAR-032', 'M.Phil / Ph.D', 'JNTUK', 'English & Communication Skills', '2019-07-01', '2019-07-01', 6.0, 'Assistant Professor', 'Assistant Professor', 'Regular', 1
FROM `users` u WHERE u.email = 'faculty.bsh@gmrit.edu.in'
ON DUPLICATE KEY UPDATE `designation_present` = VALUES(`designation_present`), `experience_years` = VALUES(`experience_years`);
INSERT INTO `user_profiles` (`user_id`, `per_no`, `pan_no`, `apaar_id`, `highest_degree`, `university`, `specialization`, `doj_institution`, `doj_department`, `experience_years`, `designation_joining`, `designation_present`, `association_nature`, `is_currently_associated`)
SELECT u.user_id, 'CRD_BSH', 'ABCDE0033F', 'APAAR-033', 'Ph.D', 'Andhra University', 'Humanities', '2018-08-01', '2018-08-01', 7.0, 'Assistant Professor', 'Assistant Professor & Coordinator', 'Regular', 1
FROM `users` u WHERE u.email = 'coord.bsh@gmrit.edu.in'
ON DUPLICATE KEY UPDATE `designation_present` = VALUES(`designation_present`), `experience_years` = VALUES(`experience_years`);
INSERT INTO `user_profiles` (`user_id`, `per_no`, `pan_no`, `apaar_id`, `highest_degree`, `university`, `specialization`, `doj_institution`, `doj_department`, `experience_years`, `designation_joining`, `designation_present`, `association_nature`, `is_currently_associated`)
SELECT u.user_id, 'HOD_CSECS', 'ABCDE0034F', 'APAAR-034', 'Ph.D', 'IIT Delhi', 'Cyber Security & Cryptography', '2016-07-01', '2016-07-01', 12.0, 'Associate Professor', 'Professor & HOD', 'Regular', 1
FROM `users` u WHERE u.email = 'hod.csecs@gmrit.edu.in'
ON DUPLICATE KEY UPDATE `designation_present` = VALUES(`designation_present`), `experience_years` = VALUES(`experience_years`);
INSERT INTO `user_profiles` (`user_id`, `per_no`, `pan_no`, `apaar_id`, `highest_degree`, `university`, `specialization`, `doj_institution`, `doj_department`, `experience_years`, `designation_joining`, `designation_present`, `association_nature`, `is_currently_associated`)
SELECT u.user_id, 'FAC_CSECS', 'ABCDE0035F', 'APAAR-035', 'M.Tech', 'JNTUK', 'Network & Cyber Security', '2020-07-01', '2020-07-01', 5.0, 'Assistant Professor', 'Assistant Professor', 'Regular', 1
FROM `users` u WHERE u.email = 'faculty.csecs@gmrit.edu.in'
ON DUPLICATE KEY UPDATE `designation_present` = VALUES(`designation_present`), `experience_years` = VALUES(`experience_years`);
INSERT INTO `user_profiles` (`user_id`, `per_no`, `pan_no`, `apaar_id`, `highest_degree`, `university`, `specialization`, `doj_institution`, `doj_department`, `experience_years`, `designation_joining`, `designation_present`, `association_nature`, `is_currently_associated`)
SELECT u.user_id, 'CRD_CSECS', 'ABCDE0036F', 'APAAR-036', 'M.Tech', 'NIT Warangal', 'Cyber Defense', '2019-06-01', '2019-06-01', 6.0, 'Assistant Professor', 'Assistant Professor & Coordinator', 'Regular', 1
FROM `users` u WHERE u.email = 'coord.csecs@gmrit.edu.in'
ON DUPLICATE KEY UPDATE `designation_present` = VALUES(`designation_present`), `experience_years` = VALUES(`experience_years`);
INSERT INTO `user_profiles` (`user_id`, `per_no`, `pan_no`, `apaar_id`, `highest_degree`, `university`, `specialization`, `doj_institution`, `doj_department`, `experience_years`, `designation_joining`, `designation_present`, `association_nature`, `is_currently_associated`)
SELECT u.user_id, 'HOD_MATHS', 'ABCDE0037F', 'APAAR-037', 'Ph.D', 'Andhra University', 'Applied Mathematics & Statistics', '2013-06-01', '2013-06-01', 16.0, 'Associate Professor', 'Professor & HOD', 'Regular', 1
FROM `users` u WHERE u.email = 'hod.maths@gmrit.edu.in'
ON DUPLICATE KEY UPDATE `designation_present` = VALUES(`designation_present`), `experience_years` = VALUES(`experience_years`);
INSERT INTO `user_profiles` (`user_id`, `per_no`, `pan_no`, `apaar_id`, `highest_degree`, `university`, `specialization`, `doj_institution`, `doj_department`, `experience_years`, `designation_joining`, `designation_present`, `association_nature`, `is_currently_associated`)
SELECT u.user_id, 'FAC_MATHS', 'ABCDE0038F', 'APAAR-038', 'Ph.D', 'JNTUK', 'Pure & Computational Mathematics', '2019-07-01', '2019-07-01', 6.0, 'Assistant Professor', 'Assistant Professor', 'Regular', 1
FROM `users` u WHERE u.email = 'faculty.maths@gmrit.edu.in'
ON DUPLICATE KEY UPDATE `designation_present` = VALUES(`designation_present`), `experience_years` = VALUES(`experience_years`);
INSERT INTO `user_profiles` (`user_id`, `per_no`, `pan_no`, `apaar_id`, `highest_degree`, `university`, `specialization`, `doj_institution`, `doj_department`, `experience_years`, `designation_joining`, `designation_present`, `association_nature`, `is_currently_associated`)
SELECT u.user_id, 'HOD_PHYS', 'ABCDE0039F', 'APAAR-039', 'Ph.D', 'IIT Madras', 'Material Science & Condensed Matter', '2014-06-01', '2014-06-01', 15.0, 'Associate Professor', 'Professor & HOD', 'Regular', 1
FROM `users` u WHERE u.email = 'hod.physics@gmrit.edu.in'
ON DUPLICATE KEY UPDATE `designation_present` = VALUES(`designation_present`), `experience_years` = VALUES(`experience_years`);
INSERT INTO `user_profiles` (`user_id`, `per_no`, `pan_no`, `apaar_id`, `highest_degree`, `university`, `specialization`, `doj_institution`, `doj_department`, `experience_years`, `designation_joining`, `designation_present`, `association_nature`, `is_currently_associated`)
SELECT u.user_id, 'FAC_PHYS', 'ABCDE0040F', 'APAAR-040', 'Ph.D', 'Andhra University', 'Optics & Nanotechnology', '2019-06-01', '2019-06-01', 6.0, 'Assistant Professor', 'Assistant Professor', 'Regular', 1
FROM `users` u WHERE u.email = 'faculty.physics@gmrit.edu.in'
ON DUPLICATE KEY UPDATE `designation_present` = VALUES(`designation_present`), `experience_years` = VALUES(`experience_years`);
INSERT INTO `user_profiles` (`user_id`, `per_no`, `pan_no`, `apaar_id`, `highest_degree`, `university`, `specialization`, `doj_institution`, `doj_department`, `experience_years`, `designation_joining`, `designation_present`, `association_nature`, `is_currently_associated`)
SELECT u.user_id, 'HOD_CHEM', 'ABCDE0041F', 'APAAR-041', 'Ph.D', 'IIT Kharagpur', 'Organic & Polymer Chemistry', '2013-07-01', '2013-07-01', 16.0, 'Associate Professor', 'Professor & HOD', 'Regular', 1
FROM `users` u WHERE u.email = 'hod.chemistry@gmrit.edu.in'
ON DUPLICATE KEY UPDATE `designation_present` = VALUES(`designation_present`), `experience_years` = VALUES(`experience_years`);
INSERT INTO `user_profiles` (`user_id`, `per_no`, `pan_no`, `apaar_id`, `highest_degree`, `university`, `specialization`, `doj_institution`, `doj_department`, `experience_years`, `designation_joining`, `designation_present`, `association_nature`, `is_currently_associated`)
SELECT u.user_id, 'FAC_CHEM', 'ABCDE0042F', 'APAAR-042', 'Ph.D', 'JNTUK', 'Analytical Chemistry', '2019-07-01', '2019-07-01', 6.0, 'Assistant Professor', 'Assistant Professor', 'Regular', 1
FROM `users` u WHERE u.email = 'faculty.chemistry@gmrit.edu.in'
ON DUPLICATE KEY UPDATE `designation_present` = VALUES(`designation_present`), `experience_years` = VALUES(`experience_years`);

SET FOREIGN_KEY_CHECKS = 1;
