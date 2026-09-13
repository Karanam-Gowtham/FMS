-- FMS Database Backup (Pre-Phase 4)
-- Date: 2026-09-13 17:40:40

SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `_old_academic_years`;
CREATE TABLE `_old_academic_years` (
  `academic_year_id` int(11) NOT NULL AUTO_INCREMENT,
  `year_name` varchar(20) NOT NULL,
  `is_active` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`academic_year_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `_old_academic_years` VALUES ('1', '2023-2024', '1');

DROP TABLE IF EXISTS `_old_approval_roles`;
CREATE TABLE `_old_approval_roles` (
  `role_id` int(11) NOT NULL AUTO_INCREMENT,
  `role_name` varchar(50) NOT NULL,
  `role_order` int(11) NOT NULL,
  PRIMARY KEY (`role_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `_old_approval_roles` VALUES ('1', 'FACULTY', '1');
INSERT INTO `_old_approval_roles` VALUES ('2', 'DEPT_COORD', '2');
INSERT INTO `_old_approval_roles` VALUES ('3', 'HOD', '3');
INSERT INTO `_old_approval_roles` VALUES ('4', 'CENTRAL_COORD', '4');

DROP TABLE IF EXISTS `_old_document_actions`;
CREATE TABLE `_old_document_actions` (
  `action_id` int(11) NOT NULL AUTO_INCREMENT,
  `document_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `action_type` varchar(50) NOT NULL,
  `remarks` text DEFAULT NULL,
  `action_date` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`action_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `_old_document_role_flow`;
CREATE TABLE `_old_document_role_flow` (
  `flow_id` int(11) NOT NULL AUTO_INCREMENT,
  `version_id` int(11) NOT NULL,
  `current_role_id` int(11) NOT NULL,
  `status` enum('PENDING','UNDER_REVIEW','APPROVED') DEFAULT 'PENDING',
  `updated_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`flow_id`),
  KEY `version_id` (`version_id`),
  KEY `current_role_id` (`current_role_id`),
  CONSTRAINT `_old_document_role_flow_ibfk_1` FOREIGN KEY (`version_id`) REFERENCES `_old_document_versions` (`version_id`),
  CONSTRAINT `_old_document_role_flow_ibfk_2` FOREIGN KEY (`current_role_id`) REFERENCES `_old_approval_roles` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `_old_document_types`;
CREATE TABLE `_old_document_types` (
  `type_id` int(11) NOT NULL AUTO_INCREMENT,
  `type_name` varchar(100) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `_old_document_types` VALUES ('1', 'Misc', '1');

DROP TABLE IF EXISTS `_old_document_versions`;
CREATE TABLE `_old_document_versions` (
  `version_id` int(11) NOT NULL AUTO_INCREMENT,
  `document_id` int(11) NOT NULL,
  `version_number` int(11) NOT NULL,
  `file_path` varchar(500) DEFAULT NULL,
  `uploaded_by` int(11) DEFAULT NULL,
  `uploaded_at` datetime DEFAULT current_timestamp(),
  `is_current` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`version_id`),
  KEY `document_id` (`document_id`),
  CONSTRAINT `_old_document_versions_ibfk_1` FOREIGN KEY (`document_id`) REFERENCES `_old_documents` (`document_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `_old_documents`;
CREATE TABLE `_old_documents` (
  `document_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `type_id` int(11) DEFAULT NULL,
  `academic_year_id` int(11) DEFAULT NULL,
  `uploaded_by` int(11) DEFAULT NULL,
  `dept_id` int(11) DEFAULT NULL,
  `current_role_id` int(11) DEFAULT NULL,
  `original_file_name` varchar(255) DEFAULT NULL,
  `file_path` varchar(500) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'Pending',
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`document_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `_old_rejection_history`;
CREATE TABLE `_old_rejection_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `file_id` int(11) NOT NULL,
  `table_name` varchar(100) NOT NULL,
  `rejected_by` varchar(100) DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `_old_role_flow_logs`;
CREATE TABLE `_old_role_flow_logs` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `version_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `action` enum('APPROVED','SENT_BACK') NOT NULL,
  `comments` text DEFAULT NULL,
  `action_by` int(11) DEFAULT NULL,
  `action_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`log_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `a_c_files`;
CREATE TABLE `a_c_files` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `academic_year` varchar(20) NOT NULL,
  `criteria` varchar(100) NOT NULL,
  `criteria_no` varchar(50) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `Faculty_name` varchar(255) NOT NULL,
  `Description` varchar(2000) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `a_cri_files`;
CREATE TABLE `a_cri_files` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `academic_year` varchar(50) NOT NULL,
  `criteria` varchar(100) NOT NULL,
  `criteria_no` varchar(500) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `Faculty_name` varchar(150) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `a_files`;
CREATE TABLE `a_files` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `username` varchar(200) NOT NULL,
  `academic_year` varchar(200) NOT NULL,
  `Dept` varchar(255) NOT NULL,
  `criteria` int(100) NOT NULL,
  `criteria_no` varchar(200) NOT NULL,
  `uploaded_at` timestamp NULL DEFAULT NULL,
  `Faculty_name` varchar(200) NOT NULL,
  `Description` varchar(1000) NOT NULL,
  `file_name` varchar(200) NOT NULL,
  `file_path` varchar(300) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `academic_year`;
CREATE TABLE `academic_year` (
  `year` varchar(40) NOT NULL,
  PRIMARY KEY (`year`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `academic_year` VALUES ('2020-21');
INSERT INTO `academic_year` VALUES ('2021-22');
INSERT INTO `academic_year` VALUES ('2022-23');
INSERT INTO `academic_year` VALUES ('2023-24');
INSERT INTO `academic_year` VALUES ('2024-25');

DROP TABLE IF EXISTS `academic_years`;
CREATE TABLE `academic_years` (
  `year_id` int(11) NOT NULL AUTO_INCREMENT,
  `year_label` varchar(40) NOT NULL,
  `is_active` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`year_id`),
  UNIQUE KEY `year_label` (`year_label`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `academic_years` VALUES ('1', '2020-21', '0');
INSERT INTO `academic_years` VALUES ('2', '2021-22', '0');
INSERT INTO `academic_years` VALUES ('3', '2022-23', '0');
INSERT INTO `academic_years` VALUES ('4', '2023-24', '0');
INSERT INTO `academic_years` VALUES ('5', '2024-25', '1');

DROP TABLE IF EXISTS `admin_login`;
CREATE TABLE `admin_login` (
  `Username` varchar(30) NOT NULL,
  `Password` varchar(30) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=85 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `admin_login` VALUES ('chandu', '123', NULL, '1');

DROP TABLE IF EXISTS `admin_reg`;
CREATE TABLE `admin_reg` (
  `Username` varchar(30) NOT NULL,
  `Password` varchar(20) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `admin_reg` VALUES ('chandu', '123', NULL, '1');

DROP TABLE IF EXISTS `approval_flow`;
CREATE TABLE `approval_flow` (
  `flow_id` int(11) NOT NULL AUTO_INCREMENT,
  `type_id` int(11) NOT NULL,
  `sequence_no` int(11) NOT NULL,
  `current_role_id` int(11) NOT NULL,
  PRIMARY KEY (`flow_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `central_files`;
CREATE TABLE `central_files` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `event` varchar(255) DEFAULT NULL,
  `acd_year` varchar(255) NOT NULL,
  `club_name` varchar(255) DEFAULT NULL,
  `event_name` varchar(255) DEFAULT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `file_path` varchar(500) DEFAULT NULL,
  `uploaded_by` varchar(255) DEFAULT NULL,
  `photo1` varchar(500) DEFAULT NULL,
  `photo2` varchar(500) DEFAULT NULL,
  `photo3` varchar(500) DEFAULT NULL,
  `photo4` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `conf_org_tab`;
CREATE TABLE `conf_org_tab` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) DEFAULT NULL,
  `branch` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `mode` varchar(50) DEFAULT NULL,
  `date_from` date DEFAULT NULL,
  `date_to` date DEFAULT NULL,
  `organised_by` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `brochure` varchar(255) DEFAULT NULL,
  `fdp_schedule_invitation` varchar(255) DEFAULT NULL,
  `attendance_forms` varchar(255) DEFAULT NULL,
  `feedback_forms` varchar(255) DEFAULT NULL,
  `fdp_report` varchar(255) DEFAULT NULL,
  `photo1` varchar(255) DEFAULT NULL,
  `photo2` varchar(255) DEFAULT NULL,
  `photo3` varchar(255) DEFAULT NULL,
  `submission_time` datetime DEFAULT NULL,
  `year` varchar(255) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'Pending HOD',
  `rejection_reason` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `conf_org_tab` VALUES ('1', 'cseuser', 'CSE', 'cseconforg', 'online', '2025-01-01', '2025-01-02', 'gmrit', 'rajam', 'uploads/certificates/conference brochure.pdf', 'uploads/certificates/conference schedule and invitatino.pdf', 'uploads/certificates/conference attended form.pdf', 'uploads/certificates/Feedback form.pdf', 'uploads/certificates/Conference report.pdf', 'uploads/certificates/profile.png', 'uploads/certificates/profile.png', 'uploads/certificates/profile.png', '2026-08-12 07:34:20', '2024-25', 'Accepted', NULL);

DROP TABLE IF EXISTS `conference_tab`;
CREATE TABLE `conference_tab` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `branch` varchar(100) NOT NULL,
  `paper_title` varchar(300) NOT NULL,
  `authors` text DEFAULT NULL,
  `conference_name` varchar(255) DEFAULT NULL,
  `published_paper_name` varchar(255) DEFAULT NULL,
  `from_date` varchar(200) NOT NULL,
  `to_date` varchar(200) NOT NULL,
  `organised_by` varchar(200) NOT NULL,
  `location` varchar(200) NOT NULL,
  `certificate_path` varchar(400) NOT NULL,
  `paper_type` varchar(200) NOT NULL,
  `paper_file_path` varchar(300) NOT NULL,
  `volume_no` varchar(255) DEFAULT NULL,
  `issue_no` varchar(255) DEFAULT NULL,
  `page_no` varchar(255) DEFAULT NULL,
  `indexing` varchar(255) DEFAULT NULL,
  `publication_link` varchar(255) DEFAULT NULL,
  `issn_no` varchar(255) DEFAULT NULL,
  `doi` varchar(255) DEFAULT NULL,
  `submission_time` varchar(300) NOT NULL,
  `year` varchar(255) NOT NULL,
  `status` varchar(50) DEFAULT 'Pending HOD',
  `rejection_reason` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `conference_tab` VALUES ('1', 'cseuser', 'CSE', 'cseconfpro', '[{\"name\":\"cseuser\",\"affiliation\":\"gmrit\",\"position\":\"First author\"}]', 'cseconfpro', 'journal2', '', '', '', '', '', '', 'uploads/paper.pdf', '2', '2', '2', 'SCI', 'https://conferencepaper.com', '2', '11/12/2024', '2026-08-12 07:41:40', '2024-25', 'Accepted', NULL);

DROP TABLE IF EXISTS `contact_form`;
CREATE TABLE `contact_form` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `criteria`;
CREATE TABLE `criteria` (
  `SI_no` int(10) NOT NULL,
  `Sub_no` varchar(30) NOT NULL,
  `Des` varchar(600) NOT NULL,
  `year` varchar(200) NOT NULL,
  PRIMARY KEY (`Sub_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `criteria` VALUES ('1', '1.1.1', 'Copies of PEOs, POs & PSOs for all programs', '2022-23');
INSERT INTO `criteria` VALUES ('1', '1.1.2', 'Number of Programmes where syllabus revision was carried out during the year(Data Requirement: Programme Code ,Names of the Programmes revised)', '2022-23');
INSERT INTO `criteria` VALUES ('1', '1.1.2(A)', 'List of programs where syllabus revision has been carried out signed by the Principal', '2022-23');
INSERT INTO `criteria` VALUES ('1', '1.1.2(B)', 'Approved Minutes of relevant Academic Council/BOS meetings highlighting the specific agenda item relevant to the metric year wise', '2022-23');
INSERT INTO `criteria` VALUES ('1', '1.1.3', 'Number of courses focusing on employability/entrepreneurship/ skill development  offered by the Institution during the year(Data Requirement: Name of the Course with Course Code ,Name of the Programme ,Activities which have a direct bearing on employability/ entrepreneurship/ skill  development)', '2022-23');
INSERT INTO `criteria` VALUES ('1', '1.1.3(A)', 'Syllabus copy of the courses highlighting Focus on employability/entrepreneurship/ skill development along with their course outcomes.', '2022-23');
INSERT INTO `criteria` VALUES ('1', '1.1.3(B)', 'Minutes of the Boards of Studies/ Academic Council meetings with approval for these courses.', '2022-23');
INSERT INTO `criteria` VALUES ('1', '1.1.3(C)', 'List of MoUs', '2022-23');
INSERT INTO `criteria` VALUES ('1', '1.2.1', 'Number of new courses introduced across all programmes offered during the year(Data Requirement: Name of the newly introduced course (s)  ,Name of the Programme)', '2022-23');
INSERT INTO `criteria` VALUES ('1', '1.2.1(A)', 'List of new courses introduced program-wise during the assessment period certified by the Principal.', '2022-23');
INSERT INTO `criteria` VALUES ('1', '1.2.1(B)', 'Minutes of relevant Academic Council/BOS meetings highlighting the name of the new courses introduced', '2022-23');
INSERT INTO `criteria` VALUES ('1', '1.2.2', 'Number of Programmes offered through Choice Based Credit System  (CBCS)/Elective Course System(Data Requirement: ,Names of all Programmes offered through CBCS ,Names of all Programmes offered through Elective Course System)', '2022-23');
INSERT INTO `criteria` VALUES ('1', '1.2.2(A)', 'List of programs in which CBCS/Elective course system implemented in the last completed academic year certified by the principal', '2022-23');
INSERT INTO `criteria` VALUES ('1', '1.2.2(B)', 'Structure of the program clearly indicating courses, credits/Electives and Minutes of relevant Academic Council/BOS meetings highlighting the relevant documents to this metric', '2022-23');
INSERT INTO `criteria` VALUES ('1', '1.3.1', 'List and Description of courses relevant to Professional Ethics, Gender diversity and equality, Human Values, Environment and Sustainability, Women Empowerment introduced in the Curriculum along with the syllabus should be available in all the departments', '2022-23');
INSERT INTO `criteria` VALUES ('1', '1.3.2', 'Number of value-added courses for imparting transferable and life skills offered  during the year:(Data Requirement:  Names of the value-added courses (each with 30 or more contact hours) ,No. of times offered (for each value-added course) during the year ,Total number of students enrolled ,Total number of students completing the course during the year', '2022-23');
INSERT INTO `criteria` VALUES ('1', '1.3.2(A)', 'List of value-added courses which are optional and offered outside the curriculum of the programs with authorized sign.', '2022-23');
INSERT INTO `criteria` VALUES ('1', '1.3.2(B)', 'Brochure and Course content or syllabus along with course outcome of Value-added courses offered.', '2022-23');
INSERT INTO `criteria` VALUES ('1', '1.3.3', 'List of enrolled students for courses addressed in 1.3.2', '2022-23');
INSERT INTO `criteria` VALUES ('1', '1.3.4', 'List of students undertaking the field projects/ internships / student projects program-wise in the last completed academic year along with the details of title, place of work etc.', '2022-23');
INSERT INTO `criteria` VALUES ('1', '1.4.1', 'Sample Filled in feedback forms from the stakeholders to be provided.', '2022-23');
INSERT INTO `criteria` VALUES ('1', '1.4.2', 'Stakeholder feedback analysis report signed by the authority', '2022-23');
INSERT INTO `criteria` VALUES ('2', '2.1.1', 'Enrolment of Students ', '2022-23');
INSERT INTO `criteria` VALUES ('2', '2.1.1(A)', 'Students admitted(HEI signed documents)', '2022-23');
INSERT INTO `criteria` VALUES ('2', '2.1.1(B)', 'Enrolment Number_ AICTE letters of sanctioned intake', '2022-23');
INSERT INTO `criteria` VALUES ('2', '2.1.1(C)', 'Enrolment Number _ Ratified list of admitted students', '2022-23');
INSERT INTO `criteria` VALUES ('2', '2.1.2', 'Number of seats filled against reserved categories (SC, ST, OBC, Divyangjan,  etc.) as per the reservation policy during the year (exclusive of supernumerary  seats)', '2022-23');
INSERT INTO `criteria` VALUES ('2', '2.1.2(A)', 'Reservation seats categories to be considered as per the state rule (APSHE Guidelines)', '2022-23');
INSERT INTO `criteria` VALUES ('2', '2.1.2(B)', 'AP regulation for Admissions GO No. 73', '2022-23');
INSERT INTO `criteria` VALUES ('2', '2.1.2(C)', 'Guidelines for filling the left-over vacancies under reservation', '2022-23');
INSERT INTO `criteria` VALUES ('2', '2.1.2(D)', 'Category wise_ Ratified list of students admitted', '2022-23');
INSERT INTO `criteria` VALUES ('2', '2.1.2(E)', 'Admission Abstract(HEI signed documents)', '2022-23');
INSERT INTO `criteria` VALUES ('2', '2.2.1', 'The institution assesses students’ learning levels and organises special  programmes for both slow and advanced learners(Present a write-up within a maximum of 200 words)', '2022-23');
INSERT INTO `criteria` VALUES ('2', '2.2.1(A)', 'Video records of the specific topics(A document having the links and screen shots of video course/LCS)', '2022-23');
INSERT INTO `criteria` VALUES ('2', '2.2.1(B)', 'Classes for the slow learner(Time table conducted for CA failures and attendance sheets)', '2022-23');
INSERT INTO `criteria` VALUES ('2', '2.2.1(C)', 'Additional assignment(sample copies of either question papers or evaluated assignment)', '2022-23');
INSERT INTO `criteria` VALUES ('2', '2.2.1(D)', 'Make-up classes for course detention students/lateral entry students(Time tables and attendance sheets for each semester(1-8) at least one)', '2022-23');
INSERT INTO `criteria` VALUES ('2', '2.2.1(E)', 'Remedial classes conducted for end semester failures(Time tables, attendance sheets and track sheets for each semester(1-8) at least one)', '2022-23');
INSERT INTO `criteria` VALUES ('2', '2.2.1(F)', 'List of MOOCs courses(Details of student with course name and sample certificates)', '2022-23');
INSERT INTO `criteria` VALUES ('2', '2.2.1(G)', 'List of the advanced learners opted Minors and honors(Abstract certified by controller of examination)', '2022-23');
INSERT INTO `criteria` VALUES ('2', '2.2.1(H)', 'Student participation in technical events list with students event details and SAMPLE certificates', '2022-23');
INSERT INTO `criteria` VALUES ('2', '2.2.2', 'Student – Teacher (full-time) ratio(Data Requirement:  Total number of students in the institution ,Total number of full-time teachers in the institution)', '2022-23');
INSERT INTO `criteria` VALUES ('2', '2.2.2(A)', 'List of the full-time teachers', '2022-23');
INSERT INTO `criteria` VALUES ('2', '2.2.2(B)', 'Student intake', '2022-23');
INSERT INTO `criteria` VALUES ('2', '2.3.1', 'Student-centric methods such as experiential learning, participative learning and  problem-solving methodologies are used for enhancing learning experiences(Present a write-up within a maximum of 200 words.)', '2022-23');
INSERT INTO `criteria` VALUES ('2', '2.3.1(A)', 'Academic regulations and curriculum(Recent)', '2022-23');
INSERT INTO `criteria` VALUES ('2', '2.3.2', 'Teachers use ICT-enabled tools including online resources for effective teaching  and learning(Present a write-up within a maximum of 200 words.)', '2022-23');
INSERT INTO `criteria` VALUES ('2', '2.3.2 (A)', 'ICT Tools Gadgets viz. Graphic tablets, MST, Smart pen, Projector(Geotagged photos with title of photo)', '2022-23');
INSERT INTO `criteria` VALUES ('2', '2.3.3', 'Ratio of students to mentor for academic and other related issues', '2022-23');
INSERT INTO `criteria` VALUES ('2', '2.3.3(A)', 'Circular of mentor – mentees', '2022-23');
INSERT INTO `criteria` VALUES ('2', '2.3.3(B)', 'Mentor list as announced by the HEI', '2022-23');
INSERT INTO `criteria` VALUES ('2', '2.3.3(C)', 'issues raised and resolved in the mentor system(Total records of a student -&gt; one sample from each semester)', '2022-23');
INSERT INTO `criteria` VALUES ('2', '2.3.4', 'Preparation and adherence to Academic Calendar and Teaching Plans by the  institution(Describe the preparation of and adherence to the Academic Calendar and Teaching  Plans by the institution.  Present a write-up within a maximum of 200 words)', '2022-23');
INSERT INTO `criteria` VALUES ('2', '2.3.4(A)', 'Academic calendar', '2022-23');
INSERT INTO `criteria` VALUES ('2', '2.3.4(B)', 'Proof of course allotment for odd and even semesters', '2022-23');
INSERT INTO `criteria` VALUES ('2', '2.3.4(C)', 'Proof for lecture plan, Schedule and diary(one course from each semester)', '2022-23');
INSERT INTO `criteria` VALUES ('2', '2.3.4(D)', 'Minutes of AMC', '2022-23');
INSERT INTO `criteria` VALUES ('2', '2.4.1', 'Number of full-time teachers against sanctioned posts during the year(Data Requirement:  Number of full-time teachers ,Number of sanctioned posts)', '2022-23');
INSERT INTO `criteria` VALUES ('2', '2.4.1(A)', 'Sanction letter indicating number of posts', '2022-23');
INSERT INTO `criteria` VALUES ('2', '2.4.1(B)', 'Department wise List of full-time teachers appointed', '2022-23');
INSERT INTO `criteria` VALUES ('2', '2.4.2', 'Number of full-time teachers with PhD/ D.M. / M.Ch. / D.N.B Super-Specialty /  DSc / DLitt during the year(Data Requirement: List of full-time teachers with PhD/ D.M. / M.Ch. / D.N.B Super-Specialty /  DSc / DLitt.)', '2022-23');
INSERT INTO `criteria` VALUES ('2', '2.4.3', 'Total teaching experience of full-time teachers in the same institution(Data Requirement:  Name and number of full-time teachers and their years of teaching experience in the institution)', '2022-23');
INSERT INTO `criteria` VALUES ('2', '2.5.1', 'Number of days from the date of last semester-end/ year- end examination till the  declaration of results during the year(Number of days from the date of last semester-end / year-end examination till the  declaration of results year-wise during the year)', '2022-23');
INSERT INTO `criteria` VALUES ('2', '2.5.1(A)', 'Examination Result Notifications', '2022-23');
INSERT INTO `criteria` VALUES ('2', '2.5.1(B)', 'List of Programs offered and the last date of the latest semester end exams and date of result declaration', '2022-23');
INSERT INTO `criteria` VALUES ('2', '2.5.2', 'Number of students’ complaints/grievances against evaluation against the total  number who appeared in the examinations during the year', '2022-23');
INSERT INTO `criteria` VALUES ('2', '2.5.2(A)', 'Number of complaints', '2022-23');
INSERT INTO `criteria` VALUES ('2', '2.5.2(B)', 'Minutes of the examination Committee', '2022-23');
INSERT INTO `criteria` VALUES ('2', '2.5.3', 'IT integration and reforms in the examination procedures and processes  including Continuous Internal Assessment (CIA) have brought in considerable  improvement in the Examination Management System (EMS) of the Institution(Describe the examination reforms with reference to the following within a minimum  of 200 words :Examination procedures ,Processes/Procedures integrating IT ,Continuous Internal Assessment System) ', '2022-23');
INSERT INTO `criteria` VALUES ('2', '2.5.3(A)', 'Examination Regulations', '2022-23');
INSERT INTO `criteria` VALUES ('2', '2.5.3(B)', 'Question Paper templates', '2022-23');
INSERT INTO `criteria` VALUES ('2', '2.5.3(C)', 'Proof of the hybrid grading sheet', '2022-23');
INSERT INTO `criteria` VALUES ('2', '2.5.3(D)', 'Examination Results link in the website', '2022-23');
INSERT INTO `criteria` VALUES ('2', '2.5.3(E)', 'Proof of the OMR', '2022-23');
INSERT INTO `criteria` VALUES ('2', '2.6.1', 'Programme Outcomes and Course Outcomes for all Programmes offered by the  institution are stated and displayed on the website and communicated to teachers  and students(Describe Course Outcomes (COs) for all courses and the mechanism of  communication to teachers and students within a maximum of 200 words.  Upload COs for all Courses) ', '2022-23');
INSERT INTO `criteria` VALUES ('2', '2.6.1(A)', 'List of POs and COs(COs for at least two courses from each semester)', '2022-23');
INSERT INTO `criteria` VALUES ('2', '2.6.1(B)', 'Photo gallery of the COs & POs displayed Program wise', '2022-23');
INSERT INTO `criteria` VALUES ('2', '2.6.2', 'Attainment of Programme Outcomes and Course Outcomes as evaluated by the  institution(Describe the method of measuring the attainment of POs, PSOs and COs and the  level of attaiment of POs , PSOs and COs in not more than 200 words.)', '2022-23');
INSERT INTO `criteria` VALUES ('2', '2.6.2(A)', 'CO and PO attainment', '2022-23');
INSERT INTO `criteria` VALUES ('2', '2.6.2(B)', 'Files related to all surveys for the indirect assessment', '2022-23');
INSERT INTO `criteria` VALUES ('2', '2.6.3', 'Pass Percentage of students', '2022-23');
INSERT INTO `criteria` VALUES ('2', '2.6.3(A)', 'Annual report of CoE indicating the pass percentage', '2022-23');
INSERT INTO `criteria` VALUES ('2', '2.6.3(B)', 'Certified report from CoE indicating Students eligible for the degree program', '2022-23');
INSERT INTO `criteria` VALUES ('2', '2.7.1', 'Student Satisfaction Survey (SSS) on overall institutional performance  (Institution may design its own questionnaire). Results and details need to be  provided as a weblink', '2022-23');
INSERT INTO `criteria` VALUES ('3', '3.1.1', 'The institution’s research facilities are frequently updated and there is a welldefined policy for promotion of research which is uploaded on the institutional  website and implemented(Present a write-up within a maximum of 200 words.)', '2022-23');
INSERT INTO `criteria` VALUES ('3', '3.1.1(A)', 'List of research equipment along with the proof of purchases (Bill copies)', '2022-23');
INSERT INTO `criteria` VALUES ('3', '3.1.1(B)', 'Policy for Faculty Assessment and Development Scheme', '2022-23');
INSERT INTO `criteria` VALUES ('3', '3.1.1(C)', 'Financial Incentives for attending conferences, seminars and QIP', '2022-23');
INSERT INTO `criteria` VALUES ('3', '3.1.1(D)', 'Sanction letters for the funded research projects & UCs for the projects completed', '2022-23');
INSERT INTO `criteria` VALUES ('3', '3.1.1(E)', 'Copies of all MoUs for collaborative research', '2022-23');
INSERT INTO `criteria` VALUES ('3', '3.1.1(F)', 'Minutes of the governing council meeting-reflecting the research promotions', '2022-23');
INSERT INTO `criteria` VALUES ('3', '3.1.2', 'Details of Seed money', '2022-23');
INSERT INTO `criteria` VALUES ('3', '3.1.3', 'Number of teachers who were awarded national / international fellowship(s) for  advanced studies/research during the year', '2022-23');
INSERT INTO `criteria` VALUES ('3', '3.2.1', 'Grants received from Government and Non-Governmental agencies for research  projects, endowments, Chairs during the year (INR in Lakhs)', '2022-23');
INSERT INTO `criteria` VALUES ('3', '3.2.1(A)', 'List of Grants received for research projects', '2022-23');
INSERT INTO `criteria` VALUES ('3', '3.2.1(B)', 'e-copies of grants sanctioned', '2022-23');
INSERT INTO `criteria` VALUES ('3', '3.2.2', 'List of teachers having research projects during the year', '2022-23');
INSERT INTO `criteria` VALUES ('3', '3.2.3', 'Number of teachers recognized as research guides', '2022-23');
INSERT INTO `criteria` VALUES ('3', '3.2.4', 'Number of departments having research projects', '2022-23');
INSERT INTO `criteria` VALUES ('3', '3.2.4(A)', 'Web Links to Funding Agencies', '2022-23');
INSERT INTO `criteria` VALUES ('3', '3.3.1', 'Institution has created an ecosystem for innovations and creation and transfer of  knowledge supported by dedicated centres for research, entrepreneurship,  community orientation, incubation, etc.(Present a write-up within a maximum of 200 words)', '2022-23');
INSERT INTO `criteria` VALUES ('3', '3.3.1(A)', 'MSME business incubation center – sanction letter', '2022-23');
INSERT INTO `criteria` VALUES ('3', '3.3.1(B)', 'AICTE sponsored EDC – Sanction letters and list of the activities conducted by EDC', '2022-23');
INSERT INTO `criteria` VALUES ('3', '3.3.1(C)', 'Dedicated centres for research', '2022-23');
INSERT INTO `criteria` VALUES ('3', '3.3.2', 'Detailed report including photos, resource persons etc.', '2022-23');
INSERT INTO `criteria` VALUES ('3', '3.4.1', 'The Institution ensures implementation of its Code of Ethics for Research  uploaded in the website through the following:  1. Research Advisory Committee 2. Ethics Committee 3. Inclusion of Research Ethics in the research methodology course work  4. Plagiarism check through authenticated software Options: A. All of the above B. Any 3 of the above C. Any 2 of the above D. Any 1 of the above E. None of the above', '2022-23');
INSERT INTO `criteria` VALUES ('3', '3.4.1(A)', 'Research Advisory Committee', '2022-23');
INSERT INTO `criteria` VALUES ('3', '3.4.1(B)', 'Ethics Committee', '2022-23');
INSERT INTO `criteria` VALUES ('3', '3.4.1(C)', 'Inclusion of Research Ethics in the research methodology course work', '2022-23');
INSERT INTO `criteria` VALUES ('3', '3.4.1(D)', 'Anti Plagiarism software approved by JNTU', '2022-23');
INSERT INTO `criteria` VALUES ('3', '3.4.2', 'Number of PhD candidates registered per teacher (as per the data given with  regard to recognized PhD guides/ supervisors provided in Metric No. 3.2.3) during  the year', '2022-23');
INSERT INTO `criteria` VALUES ('3', '3.4.2(A)', 'List of Faculty along with the names of Research scholars', '2022-23');
INSERT INTO `criteria` VALUES ('3', '3.4.2(B)', 'Copy of the Registration letters/Joining letters', '2022-23');
INSERT INTO `criteria` VALUES ('3', '3.4.3', 'Number of research papers per teacher in CARE Journals notified on UGC  website during the year', '2022-23');
INSERT INTO `criteria` VALUES ('3', '3.4.4', 'Number of books and chapters in edited volumes / books published per teacher  during the year', '2022-23');
INSERT INTO `criteria` VALUES ('3', '3.4.5', 'Bibliometrics of the publications based on average Citation Index', '2022-23');
INSERT INTO `criteria` VALUES ('3', '3.4.6', 'Bibliometrics of the publication-based h-Index of the University', '2022-23');
INSERT INTO `criteria` VALUES ('3', '3.5.1', 'Audited statements for Revenue generated from consultancy', '2022-23');
INSERT INTO `criteria` VALUES ('3', '3.5.2', 'Total amount spent on developing facilities, training teachers and clerical/project  staff for undertaking consultancy during the year', '2022-23');
INSERT INTO `criteria` VALUES ('3', '3.6.1', 'Extension activities carried out in the neighbourhood sensitising students to social  issues for their holistic development, and the impact thereof during the year', '2022-23');
INSERT INTO `criteria` VALUES ('3', '3.6.1(A)', 'List of NSS activities conducted year wise and number of students participated', '2022-23');
INSERT INTO `criteria` VALUES ('3', '3.6.1(B)', 'Covid-19 booster report', '2022-23');
INSERT INTO `criteria` VALUES ('3', '3.6.1(C)', 'MGNCRE Reports', '2022-23');
INSERT INTO `criteria` VALUES ('3', '3.6.1(D)', 'Community Radio report', '2022-23');
INSERT INTO `criteria` VALUES ('3', '3.6.1(E)', 'List of NCC activities conducted year wise and number of students participated', '2022-23');
INSERT INTO `criteria` VALUES ('3', '3.6.2', 'Number of awards and recognition received by the Institution, its teachers and  students for extension activities from Government / Government-recognised bodies', '2022-23');
INSERT INTO `criteria` VALUES ('3', '3.6.3', 'Number of extension and outreach programmes conducted by the institution through  NSS/NCC during the year', '2022-23');
INSERT INTO `criteria` VALUES ('3', '3.6.4', 'Number of students participating in extension activities listed in 3.6.3 during the  year', '2022-23');
INSERT INTO `criteria` VALUES ('3', '3.7.1', 'Number of collaborative activities during the year for research/ faculty exchange/  student exchange/ internship/ on-the-job training/ project work', '2022-23');
INSERT INTO `criteria` VALUES ('3', '3.7.2', 'Number of functional MoUs with institutions of national and/or international  importance, other universities, industries, corporate houses, etc. during the year  (only functional MoUs with ongoing activities to be considered)', '2022-23');
INSERT INTO `criteria` VALUES ('3', '3.7.2(A)', 'e-copies of functional MoUs', '2022-23');
INSERT INTO `criteria` VALUES ('3', '3.7.2(B)', 'e-copies of Activities of MOUs', '2022-23');
INSERT INTO `criteria` VALUES ('4', '4.1.1', 'The Institution has adequate infrastructure and physical facilities for teaching learning, viz., classrooms, laboratories, computing equipments, etc.', '2022-23');
INSERT INTO `criteria` VALUES ('4', '4.1.1(A)', 'Details of the Classrooms, Labs & Other facilities across all the six blocks', '2022-23');
INSERT INTO `criteria` VALUES ('4', '4.1.1(B)', 'List of the laboratories with titles', '2022-23');
INSERT INTO `criteria` VALUES ('4', '4.1.1(C)', 'Campus LAN diagram', '2022-23');
INSERT INTO `criteria` VALUES ('4', '4.1.1(D)', 'Proof of bandwidth', '2022-23');
INSERT INTO `criteria` VALUES ('4', '4.1.1(E)', 'List of the software', '2022-23');
INSERT INTO `criteria` VALUES ('4', '4.1.1(F)', 'Photo gallery of all the academic blocks, classrooms, drawing hall, seminar hall, auditorium, and Labs', '2022-23');
INSERT INTO `criteria` VALUES ('4', '4.1.2', 'The institution has adequate facilities for cultural activities, yoga, sports and games  (indoor and outdoor) including gymnasium, yoga centre, auditorium etc.)', '2022-23');
INSERT INTO `criteria` VALUES ('4', '4.1.2(A)', 'Colleague of Geo-tagged pictures', '2022-23');
INSERT INTO `criteria` VALUES ('4', '4.1.2(B)', 'Area details of the all the facilities', '2022-23');
INSERT INTO `criteria` VALUES ('4', '4.1.2(C)', 'Photo gallery of the various activities', '2022-23');
INSERT INTO `criteria` VALUES ('4', '4.1.3', 'Number of classrooms and seminar halls with ICT-enabled facilities', '2022-23');
INSERT INTO `criteria` VALUES ('4', '4.1.3(A)', 'Geo-tagged photographs of classrooms with ICT enabled facilities', '2022-23');
INSERT INTO `criteria` VALUES ('4', '4.1.3(B)', 'Class Timetables', '2022-23');
INSERT INTO `criteria` VALUES ('4', '4.1.4', 'Expenditure for infrastructure augmentation, excluding salary, during the year (INR  in Lakhs)', '2022-23');
INSERT INTO `criteria` VALUES ('4', '4.1.4(A)', 'Budget allocation', '2022-23');
INSERT INTO `criteria` VALUES ('4', '4.1.4(B)', 'Provide the consolidated fund allocation towards infrastructure augmentation facilities duly certified by Head of the Institution', '2022-23');
INSERT INTO `criteria` VALUES ('4', '4.2.1', 'Library is automated using Integrated Library Management System (ILMS)', '2022-23');
INSERT INTO `criteria` VALUES ('4', '4.2.1(A)', 'Library operates through LIBSYS', '2022-23');
INSERT INTO `criteria` VALUES ('4', '4.2.1(B)', 'All the books are provided with RF Id security tags', '2022-23');
INSERT INTO `criteria` VALUES ('4', '4.2.1(C)', 'Copy of the latest License agreement of LIBSYS-7', '2022-23');
INSERT INTO `criteria` VALUES ('4', '4.2.1(D)', 'Digital Library', '2022-23');
INSERT INTO `criteria` VALUES ('4', '4.2.2', 'Institution has access to the following', '2022-23');
INSERT INTO `criteria` VALUES ('4', '4.2.2 (D)', 'Specific details in respect of e-resources selected.', '2022-23');
INSERT INTO `criteria` VALUES ('4', '4.2.2 (E)', 'Databases', '2022-23');
INSERT INTO `criteria` VALUES ('4', '4.2.2(A)', 'Details of subscriptions of e-journals', '2022-23');
INSERT INTO `criteria` VALUES ('4', '4.2.2(B)', 'Letter of subscription', '2022-23');
INSERT INTO `criteria` VALUES ('4', '4.2.2(C)', 'Screenshots of the facilities claimed with the name of HEI.', '2022-23');
INSERT INTO `criteria` VALUES ('4', '4.2.3', 'Expenditure on purchase of books/ e-books and subscription to journals/e-journals  during the year (INR in lakhs)', '2022-23');
INSERT INTO `criteria` VALUES ('4', '4.2.3(A)', 'Consolidated extract of expenditure', '2022-23');
INSERT INTO `criteria` VALUES ('4', '4.2.3(B)', 'Invoices of all the expenditure', '2022-23');
INSERT INTO `criteria` VALUES ('4', '4.2.4', 'Usage of library by teachers and students (footfalls and login data for online access)', '2022-23');
INSERT INTO `criteria` VALUES ('4', '4.2.4(A)', 'Certified e-copy of the ledger for footfalls for 5days', '2022-23');
INSERT INTO `criteria` VALUES ('4', '4.2.4(B)', 'Certified screenshots of the data for the same 5 days for online access', '2022-23');
INSERT INTO `criteria` VALUES ('4', '4.2.4(C)', 'Last page of accession register details', '2022-23');
INSERT INTO `criteria` VALUES ('4', '4.3.1', 'Institution has an IT policy covering Wi-Fi, cyber security, etc. and has allocated  budget for updating its IT facilities', '2022-23');
INSERT INTO `criteria` VALUES ('4', '4.3.1(A)', 'Colleague of Geo-tagged pictures', '2022-23');
INSERT INTO `criteria` VALUES ('4', '4.3.1(B)', 'Policy document', '2022-23');
INSERT INTO `criteria` VALUES ('4', '4.3.1(C)', 'Campus Wi-Fi/Network diagram', '2022-23');
INSERT INTO `criteria` VALUES ('4', '4.3.1(D)', 'Budget of the year 2020-21', '2022-23');
INSERT INTO `criteria` VALUES ('4', '4.3.2', 'Student - Computer ratio', '2022-23');
INSERT INTO `criteria` VALUES ('4', '4.3.2(A)', 'Computer Bills', '2022-23');
INSERT INTO `criteria` VALUES ('4', '4.3.2(B)', 'Student strength', '2022-23');
INSERT INTO `criteria` VALUES ('4', '4.3.3', 'Bandwidth of internet connection in the Institution and the number of students on  campus', '2022-23');
INSERT INTO `criteria` VALUES ('4', '4.3.3(A)', 'Details of available bandwidth of internet connection in the Institution', '2022-23');
INSERT INTO `criteria` VALUES ('4', '4.3.3(B)', 'Bills for any one month/one quarter.', '2022-23');
INSERT INTO `criteria` VALUES ('4', '4.3.3(C)', 'e-copy of document of agreement with the service provider.', '2022-23');
INSERT INTO `criteria` VALUES ('4', '4.3.4', 'Institution has facilities for e-content development', '2022-23');
INSERT INTO `criteria` VALUES ('4', '4.3.4(A)', 'Geo tagged photographs of Media Centre, Audio Visual Centre etc.,', '2022-23');
INSERT INTO `criteria` VALUES ('4', '4.3.4(B)', 'Purchase bills for Lecture Capturing System', '2022-23');
INSERT INTO `criteria` VALUES ('4', '4.3.4(C)', 'Audited income expenditure statement highlighting the relevant expenditure.', '2022-23');
INSERT INTO `criteria` VALUES ('4', '4.4.1', 'Expenditure incurred on maintenance of physical and academic support facilities, excluding salary component, during the year (INR in lakhs)', '2022-23');
INSERT INTO `criteria` VALUES ('4', '4.4.1(A)', 'Audited statements of accounts.', '2022-23');
INSERT INTO `criteria` VALUES ('4', '4.4.2', 'There are established systems and procedures for maintaining and utilizing  physical, academic and support facilities – classrooms, laboratory, library, sports  complex, computers, etc.', '2022-23');
INSERT INTO `criteria` VALUES ('4', '4.4.2(A)', 'Schedules of Library, Sport complex ', '2022-23');
INSERT INTO `criteria` VALUES ('4', '4.4.2(B)', 'Laboratory Timetables', '2022-23');
INSERT INTO `criteria` VALUES ('4', '4.4.2(C)', 'SOP for Laboratory utilization', '2022-23');
INSERT INTO `criteria` VALUES ('4', '4.4.2(D)', 'SOP for usage of general amenities', '2022-23');
INSERT INTO `criteria` VALUES ('4', '4.4.2(E)', 'Geo-tagged photos of GAMYA', '2022-23');
INSERT INTO `criteria` VALUES ('4', '4.4.2(F)', 'Maintenance schedules and AMC letters from Estate department', '2022-23');
INSERT INTO `criteria` VALUES ('5', '5.1.1', 'Number of students benefitted by scholarships and freeships provided by the  Government during the year', '2022-23');
INSERT INTO `criteria` VALUES ('5', '5.1.2', 'Number of students benefitted by scholarships and freeships provided by the  institution and non-government agencies during the year', '2022-23');
INSERT INTO `criteria` VALUES ('5', '5.1.3', 'The following Capacity Development and Skill Enhancement activities are  organised for improving students’ capabilities', '2022-23');
INSERT INTO `criteria` VALUES ('5', '5.1.3(A)', 'Soft Skill, Language and Communication', '2022-23');
INSERT INTO `criteria` VALUES ('5', '5.1.3(B)', 'Yoga Class', '2022-23');
INSERT INTO `criteria` VALUES ('5', '5.1.3(C)', 'Awareness of Trends in technology', '2022-23');
INSERT INTO `criteria` VALUES ('5', '5.1.4', 'Number of students benefitted from guidance/coaching for competitive  examinations and career counselling offered by the institution during the year', '2022-23');
INSERT INTO `criteria` VALUES ('5', '5.1.5', 'The institution adopts the following mechanism for redressal of students’  grievances, including sexual harassment and ragging', '2022-23');
INSERT INTO `criteria` VALUES ('5', '5.2.1', 'Number of outgoing students who got placement during the year', '2022-23');
INSERT INTO `criteria` VALUES ('5', '5.2.2', 'Number of outgoing students progressing to higher education during the year', '2022-23');
INSERT INTO `criteria` VALUES ('5', '5.2.3', 'Number of students qualifying in state/ national/ international level examinations  during the year', '2022-23');
INSERT INTO `criteria` VALUES ('5', '5.3.1', 'Number of awards/medals for outstanding performance in sports and/or cultural  activities at inter-university / state /national / international events (award for a team  event should be counted as one) during the year', '2022-23');
INSERT INTO `criteria` VALUES ('5', '5.3.2', 'Presence of an active Student Council and representation of students in academic  and administrative bodies/committees of the institution', '2022-23');
INSERT INTO `criteria` VALUES ('5', '5.3.3', 'Number of sports and cultural events / competitions organised by the institution', '2022-23');
INSERT INTO `criteria` VALUES ('5', '5.4.1', 'The Alumni Association and its Chapters (registered and functional) contribute  significantly to the development of the institution through financial and other  support services', '2022-23');
INSERT INTO `criteria` VALUES ('5', '5.4.2', 'Alumni’s financial contribution during the year', '2022-23');
INSERT INTO `criteria` VALUES ('6', '6.1.1', 'The governance of the institution is reflective of an effective leadership in tune with  the vision and mission of the Institution', '2022-23');
INSERT INTO `criteria` VALUES ('6', '6.1.1(A)', 'Academic Monitoring Committee meeting minutes', '2022-23');
INSERT INTO `criteria` VALUES ('6', '6.1.1(B)', 'Placement Committee meeting minutes', '2022-23');
INSERT INTO `criteria` VALUES ('6', '6.1.1(C)', 'SAC - coordinators meeting minutes', '2022-23');
INSERT INTO `criteria` VALUES ('6', '6.1.1(D)', 'Anti Ragging Committee meeting minutes', '2022-23');
INSERT INTO `criteria` VALUES ('6', '6.1.1(E)', 'IQAC meeting minutes', '2022-23');
INSERT INTO `criteria` VALUES ('6', '6.1.1(F)', 'Board of Studies Meeting minutes', '2022-23');
INSERT INTO `criteria` VALUES ('6', '6.1.1(G)', 'Academic Council meeting minutes', '2022-23');
INSERT INTO `criteria` VALUES ('6', '6.1.1(H)', 'Governing Council meeting minutes', '2022-23');
INSERT INTO `criteria` VALUES ('6', '6.1.1(I)', 'HOD/Academic Development Committee Meeting Minutes', '2022-23');
INSERT INTO `criteria` VALUES ('6', '6.1.1(J)', 'Finance Committee meeting minutes', '2022-23');
INSERT INTO `criteria` VALUES ('6', '6.1.1(K)', 'Library Committee meeting minutes', '2022-23');
INSERT INTO `criteria` VALUES ('6', '6.1.1(L)', 'Town Hall meeting minutes', '2022-23');
INSERT INTO `criteria` VALUES ('6', '6.1.2', 'Effective leadership is reflected in various institutional practices such as decentralization and participative management', '2022-23');
INSERT INTO `criteria` VALUES ('6', '6.1.2(A)', 'Strat-Plan', '2022-23');
INSERT INTO `criteria` VALUES ('6', '6.1.2(B)', 'Requisition for two set of mid question papers from CoE – e-mail proof', '2022-23');
INSERT INTO `criteria` VALUES ('6', '6.1.2(C)', 'Declaration of mid question paper set number from CoE– e-mail proof', '2022-23');
INSERT INTO `criteria` VALUES ('6', '6.1.2(D)', 'Uniform Evaluation', '2022-23');
INSERT INTO `criteria` VALUES ('6', '6.1.2(E)', 'Research Review meeting minutes', '2022-23');
INSERT INTO `criteria` VALUES ('6', '6.2.1', 'The institutional Strategic/ Perspective plan has been clearly articulated and  implemented', '2022-23');
INSERT INTO `criteria` VALUES ('6', '6.2.1(A)', 'Strat-Plan', '2022-23');
INSERT INTO `criteria` VALUES ('6', '6.2.1(B)', 'Merit scholarships based on AP-EAMCET rank', '2022-23');
INSERT INTO `criteria` VALUES ('6', '6.2.1(C)', 'Meritorious scholarships for students', '2022-23');
INSERT INTO `criteria` VALUES ('6', '6.2.1(D)', 'Details of the GATE training classes conducted; List of students attended GATE Coaching & secured score', '2022-23');
INSERT INTO `criteria` VALUES ('6', '6.2.1(E)', 'Details of the CRT programs/ Technical training/Competitions conducted (Total number of hours), the List of the students attended the training programs & Proof of attendance, Branch wise list of the students placed, and the number of companies visited. List of students attended the WTN.', '2022-23');
INSERT INTO `criteria` VALUES ('6', '6.2.1(F)', 'Motivational and inspirational talks by the industry experts', '2022-23');
INSERT INTO `criteria` VALUES ('6', '6.2.1(G)', 'Social media updates', '2022-23');
INSERT INTO `criteria` VALUES ('6', '6.2.2', 'The functioning of the various institutional bodies is effective and efficient as visible  from the policies, administrative set-up, appointment and service rules, procedures,  etc. ', '2022-23');
INSERT INTO `criteria` VALUES ('6', '6.2.2(A)', 'Organogram', '2022-23');
INSERT INTO `criteria` VALUES ('6', '6.2.2(B)', 'HR & Service rules/Incentive policies', '2022-23');
INSERT INTO `criteria` VALUES ('6', '6.2.2(D)', 'Frequency and conduct of the meetings of governance committees (GC, AC, BOS, FC)', '2022-23');
INSERT INTO `criteria` VALUES ('6', '6.2.2(E)', 'SOP for procurement (AOP, MRN, Comparative statements and Purchase Orders)', '2022-23');
INSERT INTO `criteria` VALUES ('6', '6.2.3', 'Implementation of e-governance in areas of operation: 1. Administration 2. Finance and Accounts 3. Student Admission and Support 4. Examination', '2022-23');
INSERT INTO `criteria` VALUES ('6', '6.3.1', 'The institution has effective welfare measures for teaching and non-teaching staff  and avenues for their career development/ progression', '2022-23');
INSERT INTO `criteria` VALUES ('6', '6.3.1(A)', 'HR Policies (Welfare & Career development)', '2022-23');
INSERT INTO `criteria` VALUES ('6', '6.3.1(B)', 'Details of the training programs conducted for teaching & non-teaching staff', '2022-23');
INSERT INTO `criteria` VALUES ('6', '6.3.1(C)', 'Details of the staff (Teaching & Non-teaching promoted)', '2022-23');
INSERT INTO `criteria` VALUES ('6', '6.3.1(D)', 'Details of on campus housing, Term insurance, Medical insurance, Children education, ESI, Cooperative credit society, Concessions in IP/OP services and Gratuity', '2022-23');
INSERT INTO `criteria` VALUES ('6', '6.3.1(E)', 'Details of the faculty received incentives for completion of Ph.D./ QIP', '2022-23');
INSERT INTO `criteria` VALUES ('6', '6.3.2', 'Number of teachers provided with financial support to attend conferences /  workshops and towards payment of membership fee of professional bodies during the  year', '2022-23');
INSERT INTO `criteria` VALUES ('6', '6.3.2(A)', 'Number of teachers provided with financial support to attend conferences / workshops and towards payment of membership fee of professional bodies during the year', '2022-23');
INSERT INTO `criteria` VALUES ('6', '6.3.3', 'Number of professional development / administrative training programmes  organized by the Institution for its teaching and non-teaching staff during the year', '2022-23');
INSERT INTO `criteria` VALUES ('6', '6.3.3(A)', 'Annual Reports highlighting training programs conducted for teaching & non teaching', '2022-23');
INSERT INTO `criteria` VALUES ('6', '6.3.3(B)', 'Training programs conducted for teaching & non-teaching', '2022-23');
INSERT INTO `criteria` VALUES ('6', '6.3.4', 'Number of teachers who have undergone online/ face-to-face Faculty Development  Programmes during the year: (Professional Development Programmes, Orientation / Induction Programmes,  Refresher Courses, Short-Term Course, etc.)', '2022-23');
INSERT INTO `criteria` VALUES ('6', '6.3.4(A)', 'List of Faculty FDPS Attended & Proofs', '2022-23');
INSERT INTO `criteria` VALUES ('6', '6.4.1', 'Institution conducts internal and external financial audits regularly', '2022-23');
INSERT INTO `criteria` VALUES ('6', '6.4.2', 'Funds / Grants received from non-government bodies, individuals, and  philanthropists during the year (not covered in Criterion III and V) (INR in lakhs)', '2022-23');
INSERT INTO `criteria` VALUES ('6', '6.4.3', 'Institutional strategies for mobilisation of funds and the optimal utilisation of  resources', '2022-23');
INSERT INTO `criteria` VALUES ('6', '6.5.1', 'Internal Quality Assurance Cell (IQAC) has contributed significantly for  institutionalizing quality assurance strategies and processes visible in terms of  incremental improvements made during the preceding year with regard to quality (in  case of the First Cycle): Incremental improvements made during the preceding year with regard to quality  and post-accreditation quality initiatives (Second and subsequent cycles)', '2022-23');
INSERT INTO `criteria` VALUES ('6', '6.5.1(A)', 'List of companies hosted Internship', '2022-23');
INSERT INTO `criteria` VALUES ('6', '6.5.1(B)', 'FADS – Supporting Docs', '2022-23');
INSERT INTO `criteria` VALUES ('6', '6.5.1(C)', 'Consolidated initiatives of IQAC (strategies & contributions of IQAC mentioned)', '2022-23');
INSERT INTO `criteria` VALUES ('6', '6.5.2', 'The institution reviews its teaching-learning process, structures and methodologies  of operation and learning outcomes at periodic intervals through its IQAC as per  norms', '2022-23');
INSERT INTO `criteria` VALUES ('6', '6.5.2(A)', 'List of the IQAC initiatives taken up to enhance the students’ performance (Course coordinator meetings, AMC meeting minutes, Remedial classes for slow learners)', '2022-23');
INSERT INTO `criteria` VALUES ('6', '6.5.2(B)', 'Details of the progression of the students from 1st to 8th semesters branch wise for 2020-21 for all the batches graduated – 1 Bar chart, in each bar chart with eight bars', '2022-23');
INSERT INTO `criteria` VALUES ('6', '6.5.2(C)', 'Structures & methodologies of operations ISO audits, Academic audits -Internal & External branch wise', '2022-23');
INSERT INTO `criteria` VALUES ('6', '6.5.2(D)', 'Internal & External Academic Audit Report', '2022-23');
INSERT INTO `criteria` VALUES ('6', '6.5.3', 'Quality assurance initiatives of the institution include', '2022-23');
INSERT INTO `criteria` VALUES ('6', '6.5.3(A)', 'IQAC meeting minutes', '2022-23');
INSERT INTO `criteria` VALUES ('6', '6.5.3(B)', 'Feedback system of the institution', '2022-23');
INSERT INTO `criteria` VALUES ('6', '6.5.3(C)', 'Feedback system for design and review of syllabus', '2022-23');
INSERT INTO `criteria` VALUES ('6', '6.5.3(D)', 'Collaborative quality initiatives', '2022-23');
INSERT INTO `criteria` VALUES ('6', '6.5.3(E)', 'Participation in NIRF', '2022-23');
INSERT INTO `criteria` VALUES ('6', '6.5.3(F)', 'NBA accreditation', '2022-23');
INSERT INTO `criteria` VALUES ('6', '6.5.3(G)', 'NAAC accreditation', '2022-23');
INSERT INTO `criteria` VALUES ('6', '6.5.3(H)', 'IQAC Feedback analysis', '2022-23');
INSERT INTO `criteria` VALUES ('7', '7.1.1', 'Measures initiated by the institution for the promotion of gender equity during the  year', '2022-23');
INSERT INTO `criteria` VALUES ('7', '7.1.1(A)', 'Action plan of WEC for gender sensitization', '2022-23');
INSERT INTO `criteria` VALUES ('7', '7.1.1(B)', 'Campus surveillance with CC TV (Audit report)', '2022-23');
INSERT INTO `criteria` VALUES ('7', '7.1.1(C)', 'Policy for women security and safety (PASH)', '2022-23');
INSERT INTO `criteria` VALUES ('7', '7.1.1(D)', 'Photographs of Exclusive reading rooms, waiting rooms and rest rooms', '2022-23');
INSERT INTO `criteria` VALUES ('7', '7.1.1(E)', 'Day care centre for the kids (Beneficiaries)', '2022-23');
INSERT INTO `criteria` VALUES ('7', '7.1.1(F)', 'Welfare measures (Maternity leave for two kids)', '2022-23');
INSERT INTO `criteria` VALUES ('7', '7.1.1(G)', 'List of the beneficiaries', '2022-23');
INSERT INTO `criteria` VALUES ('7', '7.1.10', 'The institution has a prescribed code of conduct for students, teachers, administrators  and other staff and conducts periodic sensitization programmes in this regard', '2022-23');
INSERT INTO `criteria` VALUES ('7', '7.1.10(A)', 'Details of the monitoring committee composition and minutes of the committee meeting, number of programmes organized, reports on the various programmes, etc. in support of the claims', '2022-23');
INSERT INTO `criteria` VALUES ('7', '7.1.10(B)', 'Policy document on code of ethics', '2022-23');
INSERT INTO `criteria` VALUES ('7', '7.1.10(C)', 'Circulars and geo tagged photographs and caption of the activities organized under the metric for teachers, students, administrators and other staffs', '2022-23');
INSERT INTO `criteria` VALUES ('7', '7.1.11', 'Institution celebrates / organizes national and international commemorative days,  events and festivals', '2022-23');
INSERT INTO `criteria` VALUES ('7', '7.1.11(A)', 'Annual report of the celebrations and commemorative events', '2022-23');
INSERT INTO `criteria` VALUES ('7', '7.1.11(B)', 'Photographs of some of the events', '2022-23');
INSERT INTO `criteria` VALUES ('7', '7.1.2', 'The Institution has facilities for alternate sources of energy and energy conservation ', '2022-23');
INSERT INTO `criteria` VALUES ('7', '7.1.2(A)', 'Geo tagged photographs with caption of the facilities', '2022-23');
INSERT INTO `criteria` VALUES ('7', '7.1.2(B)', 'Bills for the purchase of equipment for the facilities', '2022-23');
INSERT INTO `criteria` VALUES ('7', '7.1.2(C)', 'Permission document for connection to the grid from Government', '2022-23');
INSERT INTO `criteria` VALUES ('7', '7.1.3', 'Describe the facilities in the institution for the management of the following types of  degradable and non-degradable waste (within a maximum of 200 words)', '2022-23');
INSERT INTO `criteria` VALUES ('7', '7.1.3(A)', 'Geo tagged photographs of the facilities', '2022-23');
INSERT INTO `criteria` VALUES ('7', '7.1.3(B)', 'SOP for solid waste management', '2022-23');
INSERT INTO `criteria` VALUES ('7', '7.1.4', 'Water conservation facilities available in the institution', '2022-23');
INSERT INTO `criteria` VALUES ('7', '7.1.4(A)', 'Geo tagged photographs with caption of the facilities', '2022-23');
INSERT INTO `criteria` VALUES ('7', '7.1.4(B)', 'Bills for the purchase of equipment', '2022-23');
INSERT INTO `criteria` VALUES ('7', '7.1.5', 'Green campus initiatives include', '2022-23');
INSERT INTO `criteria` VALUES ('7', '7.1.5(A)', 'Policy document on the green campus', '2022-23');
INSERT INTO `criteria` VALUES ('7', '7.1.5(B)', 'Geo tagged photographs/Videos with caption of the facilities', '2022-23');
INSERT INTO `criteria` VALUES ('7', '7.1.5(C)', 'Circulars for the implementation of the initiatives and any other supporting document', '2022-23');
INSERT INTO `criteria` VALUES ('7', '7.1.6', 'Quality audits on environment and energy undertaken by the institution', '2022-23');
INSERT INTO `criteria` VALUES ('7', '7.1.6(A)', 'Policy document on environment and energy usage', '2022-23');
INSERT INTO `criteria` VALUES ('7', '7.1.6(B)', 'Certificate from the auditing agency', '2022-23');
INSERT INTO `criteria` VALUES ('7', '7.1.6(C)', 'Certificates of the awards received from the recognized agency', '2022-23');
INSERT INTO `criteria` VALUES ('7', '7.1.6(D)', 'Report on environmental promotional activities conducted beyond the campus', '2022-23');
INSERT INTO `criteria` VALUES ('7', '7.1.6(F)', 'Green audit report', '2022-23');
INSERT INTO `criteria` VALUES ('7', '7.1.7', 'The Institution has a Divyangjan-friendly and barrier-free environment', '2022-23');
INSERT INTO `criteria` VALUES ('7', '7.1.7(A)', 'Policy document and information brochure', '2022-23');
INSERT INTO `criteria` VALUES ('7', '7.1.7(B)', 'Geo tagged photos', '2022-23');
INSERT INTO `criteria` VALUES ('7', '7.1.7(C)', 'Bills and invoice/purchase order/AMC', '2022-23');
INSERT INTO `criteria` VALUES ('7', '7.1.7(D)', 'A rest room should include specific requirements of Divyangjan', '2022-23');
INSERT INTO `criteria` VALUES ('7', '7.1.7(E)', 'Bills for the software procured', '2022-23');
INSERT INTO `criteria` VALUES ('7', '7.1.8', 'Describe the Institutional efforts/initiatives in providing an inclusive environment i.e.  tolerance and harmony towards cultural, regional, linguistic, communal, socioeconomic and other diversities (within a maximum of 200 words)', '2022-23');
INSERT INTO `criteria` VALUES ('7', '7.1.8(A)', 'List of the outbound programs', '2022-23');
INSERT INTO `criteria` VALUES ('7', '7.1.8(B)', 'List of national level activities (Cultural, Sports, Academic, Cultural & Sports)', '2022-23');
INSERT INTO `criteria` VALUES ('7', '7.1.8(C)', 'List of the faculty & students coming from the out of state', '2022-23');
INSERT INTO `criteria` VALUES ('7', '7.1.9', 'Sensitization of students and employees of the institution to constitutional obligations:  values, rights, duties and responsibilities of citizens', '2022-23');
INSERT INTO `criteria` VALUES ('7', '7.1.9(A)', 'List of the activities/Events conducted, and the number of students present. (Awareness programs on Women safety & protection, Anti ragging, Judicial rights, Gender equality, Traffic rules, Environment protection, Conservation of natural resources (power & water)) >', '2022-23');
INSERT INTO `criteria` VALUES ('7', '7.2.1', 'Provide the weblink on the Institutional website regarding the Best practices as per  the prescribed format of NAAC', '2022-23');
INSERT INTO `criteria` VALUES ('7', '7.3.1', 'Institutional Distinctiveness', '2022-23');

DROP TABLE IF EXISTS `criteria1`;
CREATE TABLE `criteria1` (
  `SI_no` int(10) NOT NULL,
  `Sub_no` varchar(30) NOT NULL,
  `Des` varchar(600) NOT NULL,
  `year` varchar(200) NOT NULL,
  PRIMARY KEY (`Sub_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `criteria1` VALUES ('1', '1.1.1', 'Copies of PEOs, POs & PSOs for all programs', '2022-23');
INSERT INTO `criteria1` VALUES ('1', '1.1.2', 'Number of Programmes where syllabus revision was carried out during the year(Data Requirement: Programme Code ,Names of the Programmes revised)', '2022-23');
INSERT INTO `criteria1` VALUES ('1', '1.1.2(A)', 'List of programs where syllabus revision has been carried out signed by the Principal', '2022-23');
INSERT INTO `criteria1` VALUES ('1', '1.1.2(B)', 'Approved Minutes of relevant Academic Council/BOS meetings highlighting the specific agenda item relevant to the metric year wise', '2022-23');
INSERT INTO `criteria1` VALUES ('1', '1.1.3', 'Number of courses focusing on employability/entrepreneurship/ skill development  offered by the Institution during the year(Data Requirement: Name of the Course with Course Code ,Name of the Programme ,Activities which have a direct bearing on employability/ entrepreneurship/ skill  development)', '2022-23');
INSERT INTO `criteria1` VALUES ('1', '1.1.3(A)', 'Syllabus copy of the courses highlighting Focus on employability/entrepreneurship/ skill development along with their course outcomes.', '2022-23');
INSERT INTO `criteria1` VALUES ('1', '1.1.3(B)', 'Minutes of the Boards of Studies/ Academic Council meetings with approval for these courses.', '2022-23');
INSERT INTO `criteria1` VALUES ('1', '1.1.3(C)', 'List of MoUs', '2022-23');
INSERT INTO `criteria1` VALUES ('1', '1.2.1', 'Number of new courses introduced across all programmes offered during the year(Data Requirement: Name of the newly introduced course (s)  ,Name of the Programme)', '2022-23');
INSERT INTO `criteria1` VALUES ('1', '1.2.1(A)', 'List of new courses introduced program-wise during the assessment period certified by the Principal.', '2022-23');
INSERT INTO `criteria1` VALUES ('1', '1.2.1(B)', 'Minutes of relevant Academic Council/BOS meetings highlighting the name of the new courses introduced', '2022-23');
INSERT INTO `criteria1` VALUES ('1', '1.2.2', 'Number of Programmes offered through Choice Based Credit System  (CBCS)/Elective Course System(Data Requirement: ,Names of all Programmes offered through CBCS ,Names of all Programmes offered through Elective Course System)', '2022-23');
INSERT INTO `criteria1` VALUES ('1', '1.2.2(A)', 'List of programs in which CBCS/Elective course system implemented in the last completed academic year certified by the principal', '2022-23');
INSERT INTO `criteria1` VALUES ('1', '1.2.2(B)', 'Structure of the program clearly indicating courses, credits/Electives and Minutes of relevant Academic Council/BOS meetings highlighting the relevant documents to this metric', '2022-23');
INSERT INTO `criteria1` VALUES ('1', '1.3.1', 'List and Description of courses relevant to Professional Ethics, Gender diversity and equality, Human Values, Environment and Sustainability, Women Empowerment introduced in the Curriculum along with the syllabus should be available in all the departments', '2022-23');
INSERT INTO `criteria1` VALUES ('1', '1.3.2', 'Number of value-added courses for imparting transferable and life skills offered  during the year:(Data Requirement:  Names of the value-added courses (each with 30 or more contact hours) ,No. of times offered (for each value-added course) during the year ,Total number of students enrolled ,Total number of students completing the course during the year', '2022-23');
INSERT INTO `criteria1` VALUES ('1', '1.3.2(A)', 'List of value-added courses which are optional and offered outside the curriculum of the programs with authorized sign.', '2022-23');
INSERT INTO `criteria1` VALUES ('1', '1.3.2(B)', 'Brochure and Course content or syllabus along with course outcome of Value-added courses offered.', '2022-23');
INSERT INTO `criteria1` VALUES ('1', '1.3.3', 'List of enrolled students for courses addressed in 1.3.2', '2022-23');
INSERT INTO `criteria1` VALUES ('1', '1.3.4', 'List of students undertaking the field projects/ internships / student projects program-wise in the last completed academic year along with the details of title, place of work etc.', '2022-23');
INSERT INTO `criteria1` VALUES ('1', '1.4.1', 'Sample Filled in feedback forms from the stakeholders to be provided.', '2022-23');
INSERT INTO `criteria1` VALUES ('1', '1.4.2', 'Stakeholder feedback analysis report signed by the authority', '2022-23');
INSERT INTO `criteria1` VALUES ('2', '2.1.1', 'Enrolment of Students ', '2022-23');
INSERT INTO `criteria1` VALUES ('2', '2.1.1(A)', 'Students admitted(HEI signed documents)', '2022-23');
INSERT INTO `criteria1` VALUES ('2', '2.1.1(B)', 'Enrolment Number_ AICTE letters of sanctioned intake', '2022-23');
INSERT INTO `criteria1` VALUES ('2', '2.1.1(C)', 'Enrolment Number _ Ratified list of admitted students', '2022-23');
INSERT INTO `criteria1` VALUES ('2', '2.1.2', 'Number of seats filled against reserved categories (SC, ST, OBC, Divyangjan,  etc.) as per the reservation policy during the year (exclusive of supernumerary  seats)', '2022-23');
INSERT INTO `criteria1` VALUES ('2', '2.1.2(A)', 'Reservation seats categories to be considered as per the state rule (APSHE Guidelines)', '2022-23');
INSERT INTO `criteria1` VALUES ('2', '2.1.2(B)', 'AP regulation for Admissions GO No. 73', '2022-23');
INSERT INTO `criteria1` VALUES ('2', '2.1.2(C)', 'Guidelines for filling the left-over vacancies under reservation', '2022-23');
INSERT INTO `criteria1` VALUES ('2', '2.1.2(D)', 'Category wise_ Ratified list of students admitted', '2022-23');
INSERT INTO `criteria1` VALUES ('2', '2.1.2(E)', 'Admission Abstract(HEI signed documents)', '2022-23');
INSERT INTO `criteria1` VALUES ('2', '2.2.1', 'The institution assesses students’ learning levels and organises special  programmes for both slow and advanced learners(Present a write-up within a maximum of 200 words)', '2022-23');
INSERT INTO `criteria1` VALUES ('2', '2.2.1(A)', 'Video records of the specific topics(A document having the links and screen shots of video course/LCS)', '2022-23');
INSERT INTO `criteria1` VALUES ('2', '2.2.1(B)', 'Classes for the slow learner(Time table conducted for CA failures and attendance sheets)', '2022-23');
INSERT INTO `criteria1` VALUES ('2', '2.2.1(C)', 'Additional assignment(sample copies of either question papers or evaluated assignment)', '2022-23');
INSERT INTO `criteria1` VALUES ('2', '2.2.1(D)', 'Make-up classes for course detention students/lateral entry students(Time tables and attendance sheets for each semester(1-8) at least one)', '2022-23');
INSERT INTO `criteria1` VALUES ('2', '2.2.1(E)', 'Remedial classes conducted for end semester failures(Time tables, attendance sheets and track sheets for each semester(1-8) at least one)', '2022-23');
INSERT INTO `criteria1` VALUES ('2', '2.2.1(F)', 'List of MOOCs courses(Details of student with course name and sample certificates)', '2022-23');
INSERT INTO `criteria1` VALUES ('2', '2.2.1(G)', 'List of the advanced learners opted Minors and honors(Abstract certified by controller of examination)', '2022-23');
INSERT INTO `criteria1` VALUES ('2', '2.2.1(H)', 'Student participation in technical events list with students event details and SAMPLE certificates', '2022-23');
INSERT INTO `criteria1` VALUES ('2', '2.2.2', 'Student – Teacher (full-time) ratio(Data Requirement:  Total number of students in the institution ,Total number of full-time teachers in the institution)', '2022-23');
INSERT INTO `criteria1` VALUES ('2', '2.2.2(A)', 'List of the full-time teachers', '2022-23');
INSERT INTO `criteria1` VALUES ('2', '2.2.2(B)', 'Student intake', '2022-23');
INSERT INTO `criteria1` VALUES ('2', '2.3.1', 'Student-centric methods such as experiential learning, participative learning and  problem-solving methodologies are used for enhancing learning experiences(Present a write-up within a maximum of 200 words.)', '2022-23');
INSERT INTO `criteria1` VALUES ('2', '2.3.1(A)', 'Academic regulations and curriculum(Recent)', '2022-23');
INSERT INTO `criteria1` VALUES ('2', '2.3.2', 'Teachers use ICT-enabled tools including online resources for effective teaching  and learning(Present a write-up within a maximum of 200 words.)', '2022-23');
INSERT INTO `criteria1` VALUES ('2', '2.3.2 (A)', 'ICT Tools Gadgets viz. Graphic tablets, MST, Smart pen, Projector(Geotagged photos with title of photo)', '2022-23');
INSERT INTO `criteria1` VALUES ('2', '2.3.3', 'Ratio of students to mentor for academic and other related issues', '2022-23');
INSERT INTO `criteria1` VALUES ('2', '2.3.3(A)', 'Circular of mentor – mentees', '2022-23');
INSERT INTO `criteria1` VALUES ('2', '2.3.3(B)', 'Mentor list as announced by the HEI', '2022-23');
INSERT INTO `criteria1` VALUES ('2', '2.3.3(C)', 'issues raised and resolved in the mentor system(Total records of a student -&gt; one sample from each semester)', '2022-23');
INSERT INTO `criteria1` VALUES ('2', '2.3.4', 'Preparation and adherence to Academic Calendar and Teaching Plans by the  institution(Describe the preparation of and adherence to the Academic Calendar and Teaching  Plans by the institution.  Present a write-up within a maximum of 200 words)', '2022-23');
INSERT INTO `criteria1` VALUES ('2', '2.3.4(A)', 'Academic calendar', '2022-23');
INSERT INTO `criteria1` VALUES ('2', '2.3.4(B)', 'Proof of course allotment for odd and even semesters', '2022-23');
INSERT INTO `criteria1` VALUES ('2', '2.3.4(C)', 'Proof for lecture plan, Schedule and diary(one course from each semester)', '2022-23');
INSERT INTO `criteria1` VALUES ('2', '2.3.4(D)', 'Minutes of AMC', '2022-23');
INSERT INTO `criteria1` VALUES ('2', '2.4.1', 'Number of full-time teachers against sanctioned posts during the year(Data Requirement:  Number of full-time teachers ,Number of sanctioned posts)', '2022-23');
INSERT INTO `criteria1` VALUES ('2', '2.4.1(A)', 'Sanction letter indicating number of posts', '2022-23');
INSERT INTO `criteria1` VALUES ('2', '2.4.1(B)', 'Department wise List of full-time teachers appointed', '2022-23');
INSERT INTO `criteria1` VALUES ('2', '2.4.2', 'Number of full-time teachers with PhD/ D.M. / M.Ch. / D.N.B Super-Specialty /  DSc / DLitt during the year(Data Requirement: List of full-time teachers with PhD/ D.M. / M.Ch. / D.N.B Super-Specialty /  DSc / DLitt.)', '2022-23');
INSERT INTO `criteria1` VALUES ('2', '2.4.3', 'Total teaching experience of full-time teachers in the same institution(Data Requirement:  Name and number of full-time teachers and their years of teaching experience in the institution)', '2022-23');
INSERT INTO `criteria1` VALUES ('2', '2.5.1', 'Number of days from the date of last semester-end/ year- end examination till the  declaration of results during the year(Number of days from the date of last semester-end / year-end examination till the  declaration of results year-wise during the year)', '2022-23');
INSERT INTO `criteria1` VALUES ('2', '2.5.1(A)', 'Examination Result Notifications', '2022-23');
INSERT INTO `criteria1` VALUES ('2', '2.5.1(B)', 'List of Programs offered and the last date of the latest semester end exams and date of result declaration', '2022-23');
INSERT INTO `criteria1` VALUES ('2', '2.5.2', 'Number of students’ complaints/grievances against evaluation against the total  number who appeared in the examinations during the year', '2022-23');
INSERT INTO `criteria1` VALUES ('2', '2.5.2(A)', 'Number of complaints', '2022-23');
INSERT INTO `criteria1` VALUES ('2', '2.5.2(B)', 'Minutes of the examination Committee', '2022-23');
INSERT INTO `criteria1` VALUES ('2', '2.5.3', 'IT integration and reforms in the examination procedures and processes  including Continuous Internal Assessment (CIA) have brought in considerable  improvement in the Examination Management System (EMS) of the Institution(Describe the examination reforms with reference to the following within a minimum  of 200 words :Examination procedures ,Processes/Procedures integrating IT ,Continuous Internal Assessment System) ', '2022-23');
INSERT INTO `criteria1` VALUES ('2', '2.5.3(A)', 'Examination Regulations', '2022-23');
INSERT INTO `criteria1` VALUES ('2', '2.5.3(B)', 'Question Paper templates', '2022-23');
INSERT INTO `criteria1` VALUES ('2', '2.5.3(C)', 'Proof of the hybrid grading sheet', '2022-23');
INSERT INTO `criteria1` VALUES ('2', '2.5.3(D)', 'Examination Results link in the website', '2022-23');
INSERT INTO `criteria1` VALUES ('2', '2.5.3(E)', 'Proof of the OMR', '2022-23');
INSERT INTO `criteria1` VALUES ('2', '2.6.1', 'Programme Outcomes and Course Outcomes for all Programmes offered by the  institution are stated and displayed on the website and communicated to teachers  and students(Describe Course Outcomes (COs) for all courses and the mechanism of  communication to teachers and students within a maximum of 200 words.  Upload COs for all Courses) ', '2022-23');
INSERT INTO `criteria1` VALUES ('2', '2.6.1(A)', 'List of POs and COs(COs for at least two courses from each semester)', '2022-23');
INSERT INTO `criteria1` VALUES ('2', '2.6.1(B)', 'Photo gallery of the COs & POs displayed Program wise', '2022-23');
INSERT INTO `criteria1` VALUES ('2', '2.6.2', 'Attainment of Programme Outcomes and Course Outcomes as evaluated by the  institution(Describe the method of measuring the attainment of POs, PSOs and COs and the  level of attaiment of POs , PSOs and COs in not more than 200 words.)', '2022-23');
INSERT INTO `criteria1` VALUES ('2', '2.6.2(A)', 'CO and PO attainment', '2022-23');
INSERT INTO `criteria1` VALUES ('2', '2.6.2(B)', 'Files related to all surveys for the indirect assessment', '2022-23');
INSERT INTO `criteria1` VALUES ('2', '2.6.3', 'Pass Percentage of students', '2022-23');
INSERT INTO `criteria1` VALUES ('2', '2.6.3(A)', 'Annual report of CoE indicating the pass percentage', '2022-23');
INSERT INTO `criteria1` VALUES ('2', '2.6.3(B)', 'Certified report from CoE indicating Students eligible for the degree program', '2022-23');
INSERT INTO `criteria1` VALUES ('2', '2.7.1', 'Student Satisfaction Survey (SSS) on overall institutional performance  (Institution may design its own questionnaire). Results and details need to be  provided as a weblink', '2022-23');
INSERT INTO `criteria1` VALUES ('3', '3.1.1', 'The institution’s research facilities are frequently updated and there is a welldefined policy for promotion of research which is uploaded on the institutional  website and implemented(Present a write-up within a maximum of 200 words.)', '2022-23');
INSERT INTO `criteria1` VALUES ('3', '3.1.1(A)', 'List of research equipment along with the proof of purchases (Bill copies)', '2022-23');
INSERT INTO `criteria1` VALUES ('3', '3.1.1(B)', 'Policy for Faculty Assessment and Development Scheme', '2022-23');
INSERT INTO `criteria1` VALUES ('3', '3.1.1(C)', 'Financial Incentives for attending conferences, seminars and QIP', '2022-23');
INSERT INTO `criteria1` VALUES ('3', '3.1.1(D)', 'Sanction letters for the funded research projects & UCs for the projects completed', '2022-23');
INSERT INTO `criteria1` VALUES ('3', '3.1.1(E)', 'Copies of all MoUs for collaborative research', '2022-23');
INSERT INTO `criteria1` VALUES ('3', '3.1.1(F)', 'Minutes of the governing council meeting-reflecting the research promotions', '2022-23');
INSERT INTO `criteria1` VALUES ('3', '3.1.2', 'Details of Seed money', '2022-23');
INSERT INTO `criteria1` VALUES ('3', '3.1.3', 'Number of teachers who were awarded national / international fellowship(s) for  advanced studies/research during the year', '2022-23');
INSERT INTO `criteria1` VALUES ('3', '3.2.1', 'Grants received from Government and Non-Governmental agencies for research  projects, endowments, Chairs during the year (INR in Lakhs)', '2022-23');
INSERT INTO `criteria1` VALUES ('3', '3.2.1(A)', 'List of Grants received for research projects', '2022-23');
INSERT INTO `criteria1` VALUES ('3', '3.2.1(B)', 'e-copies of grants sanctioned', '2022-23');
INSERT INTO `criteria1` VALUES ('3', '3.2.2', 'List of teachers having research projects during the year', '2022-23');
INSERT INTO `criteria1` VALUES ('3', '3.2.3', 'Number of teachers recognized as research guides', '2022-23');
INSERT INTO `criteria1` VALUES ('3', '3.2.4', 'Number of departments having research projects', '2022-23');
INSERT INTO `criteria1` VALUES ('3', '3.2.4(A)', 'Web Links to Funding Agencies', '2022-23');
INSERT INTO `criteria1` VALUES ('3', '3.3.1', 'Institution has created an ecosystem for innovations and creation and transfer of  knowledge supported by dedicated centres for research, entrepreneurship,  community orientation, incubation, etc.(Present a write-up within a maximum of 200 words)', '2022-23');
INSERT INTO `criteria1` VALUES ('3', '3.3.1(A)', 'MSME business incubation center – sanction letter', '2022-23');
INSERT INTO `criteria1` VALUES ('3', '3.3.1(B)', 'AICTE sponsored EDC – Sanction letters and list of the activities conducted by EDC', '2022-23');
INSERT INTO `criteria1` VALUES ('3', '3.3.1(C)', 'Dedicated centres for research', '2022-23');
INSERT INTO `criteria1` VALUES ('3', '3.3.2', 'Detailed report including photos, resource persons etc.', '2022-23');
INSERT INTO `criteria1` VALUES ('3', '3.4.1', 'The Institution ensures implementation of its Code of Ethics for Research  uploaded in the website through the following:  1. Research Advisory Committee 2. Ethics Committee 3. Inclusion of Research Ethics in the research methodology course work  4. Plagiarism check through authenticated software Options: A. All of the above B. Any 3 of the above C. Any 2 of the above D. Any 1 of the above E. None of the above', '2022-23');
INSERT INTO `criteria1` VALUES ('3', '3.4.1(A)', 'Research Advisory Committee', '2022-23');
INSERT INTO `criteria1` VALUES ('3', '3.4.1(B)', 'Ethics Committee', '2022-23');
INSERT INTO `criteria1` VALUES ('3', '3.4.1(C)', 'Inclusion of Research Ethics in the research methodology course work', '2022-23');
INSERT INTO `criteria1` VALUES ('3', '3.4.1(D)', 'Anti Plagiarism software approved by JNTU', '2022-23');
INSERT INTO `criteria1` VALUES ('3', '3.4.2', 'Number of PhD candidates registered per teacher (as per the data given with  regard to recognized PhD guides/ supervisors provided in Metric No. 3.2.3) during  the year', '2022-23');
INSERT INTO `criteria1` VALUES ('3', '3.4.2(A)', 'List of Faculty along with the names of Research scholars', '2022-23');
INSERT INTO `criteria1` VALUES ('3', '3.4.2(B)', 'Copy of the Registration letters/Joining letters', '2022-23');
INSERT INTO `criteria1` VALUES ('3', '3.4.3', 'Number of research papers per teacher in CARE Journals notified on UGC  website during the year', '2022-23');
INSERT INTO `criteria1` VALUES ('3', '3.4.4', 'Number of books and chapters in edited volumes / books published per teacher  during the year', '2022-23');
INSERT INTO `criteria1` VALUES ('3', '3.4.5', 'Bibliometrics of the publications based on average Citation Index', '2022-23');
INSERT INTO `criteria1` VALUES ('3', '3.4.6', 'Bibliometrics of the publication-based h-Index of the University', '2022-23');
INSERT INTO `criteria1` VALUES ('3', '3.5.1', 'Audited statements for Revenue generated from consultancy', '2022-23');
INSERT INTO `criteria1` VALUES ('3', '3.5.2', 'Total amount spent on developing facilities, training teachers and clerical/project  staff for undertaking consultancy during the year', '2022-23');
INSERT INTO `criteria1` VALUES ('3', '3.6.1', 'Extension activities carried out in the neighbourhood sensitising students to social  issues for their holistic development, and the impact thereof during the year', '2022-23');
INSERT INTO `criteria1` VALUES ('3', '3.6.1(A)', 'List of NSS activities conducted year wise and number of students participated', '2022-23');
INSERT INTO `criteria1` VALUES ('3', '3.6.1(B)', 'Covid-19 booster report', '2022-23');
INSERT INTO `criteria1` VALUES ('3', '3.6.1(C)', 'MGNCRE Reports', '2022-23');
INSERT INTO `criteria1` VALUES ('3', '3.6.1(D)', 'Community Radio report', '2022-23');
INSERT INTO `criteria1` VALUES ('3', '3.6.1(E)', 'List of NCC activities conducted year wise and number of students participated', '2022-23');
INSERT INTO `criteria1` VALUES ('3', '3.6.2', 'Number of awards and recognition received by the Institution, its teachers and  students for extension activities from Government / Government-recognised bodies', '2022-23');
INSERT INTO `criteria1` VALUES ('3', '3.6.3', 'Number of extension and outreach programmes conducted by the institution through  NSS/NCC during the year', '2022-23');
INSERT INTO `criteria1` VALUES ('3', '3.6.4', 'Number of students participating in extension activities listed in 3.6.3 during the  year', '2022-23');
INSERT INTO `criteria1` VALUES ('3', '3.7.1', 'Number of collaborative activities during the year for research/ faculty exchange/  student exchange/ internship/ on-the-job training/ project work', '2022-23');
INSERT INTO `criteria1` VALUES ('3', '3.7.2', 'Number of functional MoUs with institutions of national and/or international  importance, other universities, industries, corporate houses, etc. during the year  (only functional MoUs with ongoing activities to be considered)', '2022-23');
INSERT INTO `criteria1` VALUES ('3', '3.7.2(A)', 'e-copies of functional MoUs', '2022-23');
INSERT INTO `criteria1` VALUES ('3', '3.7.2(B)', 'e-copies of Activities of MOUs', '2022-23');
INSERT INTO `criteria1` VALUES ('4', '4.1.1', 'The Institution has adequate infrastructure and physical facilities for teachinglearning, viz., classrooms, laboratories, computing equipments, etc.', '2022-23');
INSERT INTO `criteria1` VALUES ('4', '4.1.1(A)', 'Details of the Classrooms, Labs & Other facilities across all the six blocks', '2022-23');
INSERT INTO `criteria1` VALUES ('4', '4.1.1(B)', 'List of the laboratories with titles', '2022-23');
INSERT INTO `criteria1` VALUES ('4', '4.1.1(C)', 'Campus LAN diagram', '2022-23');
INSERT INTO `criteria1` VALUES ('4', '4.1.1(D)', 'Proof of bandwidth', '2022-23');
INSERT INTO `criteria1` VALUES ('4', '4.1.1(E)', 'List of the software', '2022-23');
INSERT INTO `criteria1` VALUES ('4', '4.1.1(F)', 'Photo gallery of all the academic blocks, classrooms, drawing hall, seminar hall, auditorium, and Labs', '2022-23');
INSERT INTO `criteria1` VALUES ('4', '4.1.2', 'The institution has adequate facilities for cultural activities, yoga, sports and games  (indoor and outdoor) including gymnasium, yoga centre, auditorium etc.)', '2022-23');
INSERT INTO `criteria1` VALUES ('4', '4.1.2(A)', 'Colleague of Geo-tagged pictures', '2022-23');
INSERT INTO `criteria1` VALUES ('4', '4.1.2(B)', 'Area details of the all the facilities', '2022-23');
INSERT INTO `criteria1` VALUES ('4', '4.1.2(C)', 'Photo gallery of the various activities', '2022-23');
INSERT INTO `criteria1` VALUES ('4', '4.1.3', 'Number of classrooms and seminar halls with ICT-enabled facilities', '2022-23');
INSERT INTO `criteria1` VALUES ('4', '4.1.3(A)', 'Geo-tagged photographs of classrooms with ICT enabled facilities', '2022-23');
INSERT INTO `criteria1` VALUES ('4', '4.1.3(B)', 'Class Timetables', '2022-23');
INSERT INTO `criteria1` VALUES ('4', '4.1.4', 'Expenditure for infrastructure augmentation, excluding salary, during the year (INR  in Lakhs)', '2022-23');
INSERT INTO `criteria1` VALUES ('4', '4.1.4(A)', 'Budget allocation', '2022-23');
INSERT INTO `criteria1` VALUES ('4', '4.1.4(B)', 'Provide the consolidated fund allocation towards infrastructure augmentation facilities duly certified by Head of the Institution', '2022-23');
INSERT INTO `criteria1` VALUES ('4', '4.2.1', 'Library is automated using Integrated Library Management System (ILMS)', '2022-23');
INSERT INTO `criteria1` VALUES ('4', '4.2.1(A)', 'Library operates through LIBSYS', '2022-23');
INSERT INTO `criteria1` VALUES ('4', '4.2.1(B)', 'All the books are provided with RF Id security tags', '2022-23');
INSERT INTO `criteria1` VALUES ('4', '4.2.1(C)', 'Copy of the latest License agreement of LIBSYS-7', '2022-23');
INSERT INTO `criteria1` VALUES ('4', '4.2.1(D)', 'Digital Library', '2022-23');
INSERT INTO `criteria1` VALUES ('4', '4.2.2', 'Institution has access to the following', '2022-23');
INSERT INTO `criteria1` VALUES ('4', '4.2.2 (D)', 'Specific details in respect of e-resources selected.', '2022-23');
INSERT INTO `criteria1` VALUES ('4', '4.2.2 (E)', 'Databases', '2022-23');
INSERT INTO `criteria1` VALUES ('4', '4.2.2(A)', 'Details of subscriptions of e-journals', '2022-23');
INSERT INTO `criteria1` VALUES ('4', '4.2.2(B)', 'Letter of subscription', '2022-23');
INSERT INTO `criteria1` VALUES ('4', '4.2.2(C)', 'Screenshots of the facilities claimed with the name of HEI.', '2022-23');
INSERT INTO `criteria1` VALUES ('4', '4.2.3', 'Expenditure on purchase of books/ e-books and subscription to journals/e-journals  during the year (INR in lakhs)', '2022-23');
INSERT INTO `criteria1` VALUES ('4', '4.2.3(A)', 'Consolidated extract of expenditure', '2022-23');
INSERT INTO `criteria1` VALUES ('4', '4.2.3(B)', 'Invoices of all the expenditure', '2022-23');
INSERT INTO `criteria1` VALUES ('4', '4.2.4', 'Usage of library by teachers and students (footfalls and login data for online access)', '2022-23');
INSERT INTO `criteria1` VALUES ('4', '4.2.4(A)', 'Certified e-copy of the ledger for footfalls for 5days', '2022-23');
INSERT INTO `criteria1` VALUES ('4', '4.2.4(B)', 'Certified screenshots of the data for the same 5 days for online access', '2022-23');
INSERT INTO `criteria1` VALUES ('4', '4.2.4(C)', 'Last page of accession register details', '2022-23');
INSERT INTO `criteria1` VALUES ('4', '4.3.1', 'Institution has an IT policy covering Wi-Fi, cyber security, etc. and has allocated  budget for updating its IT facilities', '2022-23');
INSERT INTO `criteria1` VALUES ('4', '4.3.1(A)', 'Colleague of Geo-tagged pictures', '2022-23');
INSERT INTO `criteria1` VALUES ('4', '4.3.1(B)', 'Policy document', '2022-23');
INSERT INTO `criteria1` VALUES ('4', '4.3.1(C)', 'Campus Wi-Fi/Network diagram', '2022-23');
INSERT INTO `criteria1` VALUES ('4', '4.3.1(D)', 'Budget of the year 2020-21', '2022-23');
INSERT INTO `criteria1` VALUES ('4', '4.3.2', 'Student - Computer ratio', '2022-23');
INSERT INTO `criteria1` VALUES ('4', '4.3.2(A)', 'Computer Bills', '2022-23');
INSERT INTO `criteria1` VALUES ('4', '4.3.2(B)', 'Student strength', '2022-23');
INSERT INTO `criteria1` VALUES ('4', '4.3.3', 'Bandwidth of internet connection in the Institution and the number of students on  campus', '2022-23');
INSERT INTO `criteria1` VALUES ('4', '4.3.3(A)', 'Details of available bandwidth of internet connection in the Institution', '2022-23');
INSERT INTO `criteria1` VALUES ('4', '4.3.3(B)', 'Bills for any one month/one quarter.', '2022-23');
INSERT INTO `criteria1` VALUES ('4', '4.3.3(C)', 'e-copy of document of agreement with the service provider.', '2022-23');
INSERT INTO `criteria1` VALUES ('4', '4.3.4', 'Institution has facilities for e-content development', '2022-23');
INSERT INTO `criteria1` VALUES ('4', '4.3.4(A)', 'Geo tagged photographs of Media Centre, Audio Visual Centre etc.,', '2022-23');
INSERT INTO `criteria1` VALUES ('4', '4.3.4(B)', 'Purchase bills for Lecture Capturing System', '2022-23');
INSERT INTO `criteria1` VALUES ('4', '4.3.4(C)', 'Audited income expenditure statement highlighting the relevant expenditure.', '2022-23');
INSERT INTO `criteria1` VALUES ('4', '4.4.1', 'Expenditure incurred on maintenance of physical and academic support facilities, excluding salary component, during the year (INR in lakhs)', '2022-23');
INSERT INTO `criteria1` VALUES ('4', '4.4.1(A)', 'Audited statements of accounts.', '2022-23');
INSERT INTO `criteria1` VALUES ('4', '4.4.2', 'There are established systems and procedures for maintaining and utilizing  physical, academic and support facilities – classrooms, laboratory, library, sports  complex, computers, etc.', '2022-23');
INSERT INTO `criteria1` VALUES ('4', '4.4.2(A)', 'Schedules of Library, Sport complex ', '2022-23');
INSERT INTO `criteria1` VALUES ('4', '4.4.2(B)', 'Laboratory Timetables', '2022-23');
INSERT INTO `criteria1` VALUES ('4', '4.4.2(C)', 'SOP for Laboratory utilization', '2022-23');
INSERT INTO `criteria1` VALUES ('4', '4.4.2(D)', 'SOP for usage of general amenities', '2022-23');
INSERT INTO `criteria1` VALUES ('4', '4.4.2(E)', 'Geo-tagged photos of GAMYA', '2022-23');
INSERT INTO `criteria1` VALUES ('4', '4.4.2(F)', 'Maintenance schedules and AMC letters from Estate department', '2022-23');
INSERT INTO `criteria1` VALUES ('5', '5.1.1', 'Number of students benefitted by scholarships and freeships provided by the  Government during the year', '2022-23');
INSERT INTO `criteria1` VALUES ('5', '5.1.2', 'Number of students benefitted by scholarships and freeships provided by the  institution and non-government agencies during the year', '2022-23');
INSERT INTO `criteria1` VALUES ('5', '5.1.3', 'The following Capacity Development and Skill Enhancement activities are  organised for improving students’ capabilities', '2022-23');
INSERT INTO `criteria1` VALUES ('5', '5.1.3(A)', 'Soft Skill, Language and Communication', '2022-23');
INSERT INTO `criteria1` VALUES ('5', '5.1.3(B)', 'Yoga Class', '2022-23');
INSERT INTO `criteria1` VALUES ('5', '5.1.3(C)', 'Awareness of Trends in technology', '2022-23');
INSERT INTO `criteria1` VALUES ('5', '5.1.4', 'Number of students benefitted from guidance/coaching for competitive  examinations and career counselling offered by the institution during the year', '2022-23');
INSERT INTO `criteria1` VALUES ('5', '5.1.5', 'The institution adopts the following mechanism for redressal of students’  grievances, including sexual harassment and ragging', '2022-23');
INSERT INTO `criteria1` VALUES ('5', '5.2.1', 'Number of outgoing students who got placement during the year', '2022-23');
INSERT INTO `criteria1` VALUES ('5', '5.2.2', 'Number of outgoing students progressing to higher education during the year', '2022-23');
INSERT INTO `criteria1` VALUES ('5', '5.2.3', 'Number of students qualifying in state/ national/ international level examinations  during the year', '2022-23');
INSERT INTO `criteria1` VALUES ('5', '5.3.1', 'Number of awards/medals for outstanding performance in sports and/or cultural  activities at inter-university / state /national / international events (award for a team  event should be counted as one) during the year', '2022-23');
INSERT INTO `criteria1` VALUES ('5', '5.3.2', 'Presence of an active Student Council and representation of students in academic  and administrative bodies/committees of the institution', '2022-23');
INSERT INTO `criteria1` VALUES ('5', '5.3.3', 'Number of sports and cultural events / competitions organised by the institution', '2022-23');
INSERT INTO `criteria1` VALUES ('5', '5.4.1', 'The Alumni Association and its Chapters (registered and functional) contribute  significantly to the development of the institution through financial and other  support services', '2022-23');
INSERT INTO `criteria1` VALUES ('5', '5.4.2', 'Alumni’s financial contribution during the year', '2022-23');
INSERT INTO `criteria1` VALUES ('6', '6.1.1', 'The governance of the institution is reflective of an effective leadership in tune with  the vision and mission of the Institution', '2022-23');
INSERT INTO `criteria1` VALUES ('6', '6.1.1(A)', 'Academic Monitoring Committee meeting minutes', '2022-23');
INSERT INTO `criteria1` VALUES ('6', '6.1.1(B)', 'Placement Committee meeting minutes', '2022-23');
INSERT INTO `criteria1` VALUES ('6', '6.1.1(C)', 'SAC - coordinators meeting minutes', '2022-23');
INSERT INTO `criteria1` VALUES ('6', '6.1.1(D)', 'Anti Ragging Committee meeting minutes', '2022-23');
INSERT INTO `criteria1` VALUES ('6', '6.1.1(E)', 'IQAC meeting minutes', '2022-23');
INSERT INTO `criteria1` VALUES ('6', '6.1.1(F)', 'Board of Studies Meeting minutes', '2022-23');
INSERT INTO `criteria1` VALUES ('6', '6.1.1(G)', 'Academic Council meeting minutes', '2022-23');
INSERT INTO `criteria1` VALUES ('6', '6.1.1(H)', 'Governing Council meeting minutes', '2022-23');
INSERT INTO `criteria1` VALUES ('6', '6.1.1(I)', 'HOD/Academic Development Committee Meeting Minutes', '2022-23');
INSERT INTO `criteria1` VALUES ('6', '6.1.1(J)', 'Finance Committee meeting minutes', '2022-23');
INSERT INTO `criteria1` VALUES ('6', '6.1.1(K)', 'Library Committee meeting minutes', '2022-23');
INSERT INTO `criteria1` VALUES ('6', '6.1.1(L)', 'Town Hall meeting minutes', '2022-23');
INSERT INTO `criteria1` VALUES ('6', '6.1.2', 'Effective leadership is reflected in various institutional practices such as decentralization and participative management', '2022-23');
INSERT INTO `criteria1` VALUES ('6', '6.1.2(A)', 'Strat-Plan', '2022-23');
INSERT INTO `criteria1` VALUES ('6', '6.1.2(B)', 'Requisition for two set of mid question papers from CoE – e-mail proof', '2022-23');
INSERT INTO `criteria1` VALUES ('6', '6.1.2(C)', 'Declaration of mid question paper set number from CoE– e-mail proof', '2022-23');
INSERT INTO `criteria1` VALUES ('6', '6.1.2(D)', 'Uniform Evaluation', '2022-23');
INSERT INTO `criteria1` VALUES ('6', '6.1.2(E)', 'Research Review meeting minutes', '2022-23');
INSERT INTO `criteria1` VALUES ('6', '6.2.1', 'The institutional Strategic/ Perspective plan has been clearly articulated and  implemented', '2022-23');
INSERT INTO `criteria1` VALUES ('6', '6.2.1(A)', 'Strat-Plan', '2022-23');
INSERT INTO `criteria1` VALUES ('6', '6.2.1(B)', 'Merit scholarships based on AP-EAMCET rank', '2022-23');
INSERT INTO `criteria1` VALUES ('6', '6.2.1(C)', 'Meritorious scholarships for students', '2022-23');
INSERT INTO `criteria1` VALUES ('6', '6.2.1(D)', 'Details of the GATE training classes conducted; List of students attended GATE Coaching & secured score', '2022-23');
INSERT INTO `criteria1` VALUES ('6', '6.2.1(E)', 'Details of the CRT programs/ Technical training/Competitions conducted (Total number of hours), the List of the students attended the training programs & Proof of attendance, Branch wise list of the students placed, and the number of companies visited. List of students attended the WTN.', '2022-23');
INSERT INTO `criteria1` VALUES ('6', '6.2.1(F)', 'Motivational and inspirational talks by the industry experts', '2022-23');
INSERT INTO `criteria1` VALUES ('6', '6.2.1(G)', 'Social media updates', '2022-23');
INSERT INTO `criteria1` VALUES ('6', '6.2.2', 'The functioning of the various institutional bodies is effective and efficient as visible  from the policies, administrative set-up, appointment and service rules, procedures,  etc. ', '2022-23');
INSERT INTO `criteria1` VALUES ('6', '6.2.2(A)', 'Organogram', '2022-23');
INSERT INTO `criteria1` VALUES ('6', '6.2.2(B)', 'HR & Service rules/Incentive policies', '2022-23');
INSERT INTO `criteria1` VALUES ('6', '6.2.2(D)', 'Frequency and conduct of the meetings of governance committees (GC, AC, BOS, FC)', '2022-23');
INSERT INTO `criteria1` VALUES ('6', '6.2.2(E)', 'SOP for procurement (AOP, MRN, Comparative statements and Purchase Orders)', '2022-23');
INSERT INTO `criteria1` VALUES ('6', '6.2.3', 'Implementation of e-governance in areas of operation: 1. Administration 2. Finance and Accounts 3. Student Admission and Support 4. Examination', '2022-23');
INSERT INTO `criteria1` VALUES ('6', '6.3.1', 'The institution has effective welfare measures for teaching and non-teaching staff  and avenues for their career development/ progression', '2022-23');
INSERT INTO `criteria1` VALUES ('6', '6.3.1(A)', 'HR Policies (Welfare & Career development)', '2022-23');
INSERT INTO `criteria1` VALUES ('6', '6.3.1(B)', 'Details of the training programs conducted for teaching & non-teaching staff', '2022-23');
INSERT INTO `criteria1` VALUES ('6', '6.3.1(C)', 'Details of the staff (Teaching & Non-teaching promoted)', '2022-23');
INSERT INTO `criteria1` VALUES ('6', '6.3.1(D)', 'Details of on campus housing, Term insurance, Medical insurance, Children education, ESI, Cooperative credit society, Concessions in IP/OP services and Gratuity', '2022-23');
INSERT INTO `criteria1` VALUES ('6', '6.3.1(E)', 'Details of the faculty received incentives for completion of Ph.D./ QIP', '2022-23');
INSERT INTO `criteria1` VALUES ('6', '6.3.2', 'Number of teachers provided with financial support to attend conferences /  workshops and towards payment of membership fee of professional bodies during the  year', '2022-23');
INSERT INTO `criteria1` VALUES ('6', '6.3.2(A)', 'Number of teachers provided with financial support to attend conferences / workshops and towards payment of membership fee of professional bodies during the year', '2022-23');
INSERT INTO `criteria1` VALUES ('6', '6.3.3', 'Number of professional development / administrative training programmes  organized by the Institution for its teaching and non-teaching staff during the year', '2022-23');
INSERT INTO `criteria1` VALUES ('6', '6.3.3(A)', 'Annual Reports highlighting training programs conducted for teaching & non teaching', '2022-23');
INSERT INTO `criteria1` VALUES ('6', '6.3.3(B)', 'Training programs conducted for teaching & non-teaching', '2022-23');
INSERT INTO `criteria1` VALUES ('6', '6.3.4', 'Number of teachers who have undergone online/ face-to-face Faculty Development  Programmes during the year: (Professional Development Programmes, Orientation / Induction Programmes,  Refresher Courses, Short-Term Course, etc.)', '2022-23');
INSERT INTO `criteria1` VALUES ('6', '6.3.4(A)', 'List of Faculty FDPS Attended & Proofs', '2022-23');
INSERT INTO `criteria1` VALUES ('6', '6.4.1', 'Institution conducts internal and external financial audits regularly', '2022-23');
INSERT INTO `criteria1` VALUES ('6', '6.4.2', 'Funds / Grants received from non-government bodies, individuals, and  philanthropists during the year (not covered in Criterion III and V) (INR in lakhs)', '2022-23');
INSERT INTO `criteria1` VALUES ('6', '6.4.3', 'Institutional strategies for mobilisation of funds and the optimal utilisation of  resources', '2022-23');
INSERT INTO `criteria1` VALUES ('6', '6.5.1', 'Internal Quality Assurance Cell (IQAC) has contributed significantly for  institutionalizing quality assurance strategies and processes visible in terms of  incremental improvements made during the preceding year with regard to quality (in  case of the First Cycle): Incremental improvements made during the preceding year with regard to quality  and post-accreditation quality initiatives (Second and subsequent cycles)', '2022-23');
INSERT INTO `criteria1` VALUES ('6', '6.5.1(A)', 'List of companies hosted Internship', '2022-23');
INSERT INTO `criteria1` VALUES ('6', '6.5.1(B)', 'FADS – Supporting Docs', '2022-23');
INSERT INTO `criteria1` VALUES ('6', '6.5.1(C)', 'Consolidated initiatives of IQAC (strategies & contributions of IQAC mentioned)', '2022-23');
INSERT INTO `criteria1` VALUES ('6', '6.5.2', 'The institution reviews its teaching-learning process, structures and methodologies  of operation and learning outcomes at periodic intervals through its IQAC as per  norms', '2022-23');
INSERT INTO `criteria1` VALUES ('6', '6.5.2(A)', 'List of the IQAC initiatives taken up to enhance the students’ performance (Course coordinator meetings, AMC meeting minutes, Remedial classes for slow learners)', '2022-23');
INSERT INTO `criteria1` VALUES ('6', '6.5.2(B)', 'Details of the progression of the students from 1st to 8th semesters branch wise for 2020-21 for all the batches graduated – 1 Bar chart, in each bar chart with eight bars', '2022-23');
INSERT INTO `criteria1` VALUES ('6', '6.5.2(C)', 'Structures & methodologies of operations ISO audits, Academic audits -Internal & External branch wise', '2022-23');
INSERT INTO `criteria1` VALUES ('6', '6.5.2(D)', 'Internal & External Academic Audit Report', '2022-23');
INSERT INTO `criteria1` VALUES ('6', '6.5.3', 'Quality assurance initiatives of the institution include', '2022-23');
INSERT INTO `criteria1` VALUES ('6', '6.5.3(A)', 'IQAC meeting minutes', '2022-23');
INSERT INTO `criteria1` VALUES ('6', '6.5.3(B)', 'Feedback system of the institution', '2022-23');
INSERT INTO `criteria1` VALUES ('6', '6.5.3(C)', 'Feedback system for design and review of syllabus', '2022-23');
INSERT INTO `criteria1` VALUES ('6', '6.5.3(D)', 'Collaborative quality initiatives', '2022-23');
INSERT INTO `criteria1` VALUES ('6', '6.5.3(E)', 'Participation in NIRF', '2022-23');
INSERT INTO `criteria1` VALUES ('6', '6.5.3(F)', 'NBA accreditation', '2022-23');
INSERT INTO `criteria1` VALUES ('6', '6.5.3(G)', 'NAAC accreditation', '2022-23');
INSERT INTO `criteria1` VALUES ('6', '6.5.3(H)', 'IQAC Feedback analysis', '2022-23');
INSERT INTO `criteria1` VALUES ('7', '7.1.1', 'Measures initiated by the institution for the promotion of gender equity during the  year', '2022-23');
INSERT INTO `criteria1` VALUES ('7', '7.1.1(A)', 'Action plan of WEC for gender sensitization', '2022-23');
INSERT INTO `criteria1` VALUES ('7', '7.1.1(B)', 'Campus surveillance with CC TV (Audit report)', '2022-23');
INSERT INTO `criteria1` VALUES ('7', '7.1.1(C)', 'Policy for women security and safety (PASH)', '2022-23');
INSERT INTO `criteria1` VALUES ('7', '7.1.1(D)', 'Photographs of Exclusive reading rooms, waiting rooms and rest rooms', '2022-23');
INSERT INTO `criteria1` VALUES ('7', '7.1.1(E)', 'Day care centre for the kids (Beneficiaries)', '2022-23');
INSERT INTO `criteria1` VALUES ('7', '7.1.1(F)', 'Welfare measures (Maternity leave for two kids)', '2022-23');
INSERT INTO `criteria1` VALUES ('7', '7.1.1(G)', 'List of the beneficiaries', '2022-23');
INSERT INTO `criteria1` VALUES ('7', '7.1.10', 'The institution has a prescribed code of conduct for students, teachers, administrators  and other staff and conducts periodic sensitization programmes in this regard', '2022-23');
INSERT INTO `criteria1` VALUES ('7', '7.1.10(A)', 'Details of the monitoring committee composition and minutes of the committee meeting, number of programmes organized, reports on the various programmes, etc. in support of the claims', '2022-23');
INSERT INTO `criteria1` VALUES ('7', '7.1.10(B)', 'Policy document on code of ethics', '2022-23');
INSERT INTO `criteria1` VALUES ('7', '7.1.10(C)', 'Circulars and geo tagged photographs and caption of the activities organized under the metric for teachers, students, administrators and other staffs', '2022-23');
INSERT INTO `criteria1` VALUES ('7', '7.1.11', 'Institution celebrates / organizes national and international commemorative days,  events and festivals', '2022-23');
INSERT INTO `criteria1` VALUES ('7', '7.1.11(A)', 'Annual report of the celebrations and commemorative events', '2022-23');
INSERT INTO `criteria1` VALUES ('7', '7.1.11(B)', 'Photographs of some of the events', '2022-23');
INSERT INTO `criteria1` VALUES ('7', '7.1.2', 'The Institution has facilities for alternate sources of energy and energy conservation ', '2022-23');
INSERT INTO `criteria1` VALUES ('7', '7.1.2(A)', 'Geo tagged photographs with caption of the facilities', '2022-23');
INSERT INTO `criteria1` VALUES ('7', '7.1.2(B)', 'Bills for the purchase of equipment for the facilities', '2022-23');
INSERT INTO `criteria1` VALUES ('7', '7.1.2(C)', 'Permission document for connection to the grid from Government', '2022-23');
INSERT INTO `criteria1` VALUES ('7', '7.1.3', 'Describe the facilities in the institution for the management of the following types of  degradable and non-degradable waste (within a maximum of 200 words)', '2022-23');
INSERT INTO `criteria1` VALUES ('7', '7.1.3(A)', 'Geo tagged photographs of the facilities', '2022-23');
INSERT INTO `criteria1` VALUES ('7', '7.1.3(B)', 'SOP for solid waste management', '2022-23');
INSERT INTO `criteria1` VALUES ('7', '7.1.4', 'Water conservation facilities available in the institution', '2022-23');
INSERT INTO `criteria1` VALUES ('7', '7.1.4(A)', 'Geo tagged photographs with caption of the facilities', '2022-23');
INSERT INTO `criteria1` VALUES ('7', '7.1.4(B)', 'Bills for the purchase of equipment', '2022-23');
INSERT INTO `criteria1` VALUES ('7', '7.1.5', 'Green campus initiatives include', '2022-23');
INSERT INTO `criteria1` VALUES ('7', '7.1.5(A)', 'Policy document on the green campus', '2022-23');
INSERT INTO `criteria1` VALUES ('7', '7.1.5(B)', 'Geo tagged photographs/Videos with caption of the facilities', '2022-23');
INSERT INTO `criteria1` VALUES ('7', '7.1.5(C)', 'Circulars for the implementation of the initiatives and any other supporting document', '2022-23');
INSERT INTO `criteria1` VALUES ('7', '7.1.6', 'Quality audits on environment and energy undertaken by the institution', '2022-23');
INSERT INTO `criteria1` VALUES ('7', '7.1.6(A)', 'Policy document on environment and energy usage', '2022-23');
INSERT INTO `criteria1` VALUES ('7', '7.1.6(B)', 'Certificate from the auditing agency', '2022-23');
INSERT INTO `criteria1` VALUES ('7', '7.1.6(C)', 'Certificates of the awards received from the recognized agency', '2022-23');
INSERT INTO `criteria1` VALUES ('7', '7.1.6(D)', 'Report on environmental promotional activities conducted beyond the campus', '2022-23');
INSERT INTO `criteria1` VALUES ('7', '7.1.6(F)', 'Green audit report', '2022-23');
INSERT INTO `criteria1` VALUES ('7', '7.1.7', 'The Institution has a Divyangjan-friendly and barrier-free environment', '2022-23');
INSERT INTO `criteria1` VALUES ('7', '7.1.7(A)', 'Policy document and information brochure', '2022-23');
INSERT INTO `criteria1` VALUES ('7', '7.1.7(B)', 'Geo tagged photos', '2022-23');
INSERT INTO `criteria1` VALUES ('7', '7.1.7(C)', 'Bills and invoice/purchase order/AMC', '2022-23');
INSERT INTO `criteria1` VALUES ('7', '7.1.7(D)', 'A rest room should include specific requirements of Divyangjan', '2022-23');
INSERT INTO `criteria1` VALUES ('7', '7.1.7(E)', 'Bills for the software procured', '2022-23');
INSERT INTO `criteria1` VALUES ('7', '7.1.8', 'Describe the Institutional efforts/initiatives in providing an inclusive environment i.e.  tolerance and harmony towards cultural, regional, linguistic, communal, socioeconomic and other diversities (within a maximum of 200 words)', '2022-23');
INSERT INTO `criteria1` VALUES ('7', '7.1.8(A)', 'List of the outbound programs', '2022-23');
INSERT INTO `criteria1` VALUES ('7', '7.1.8(B)', 'List of national level activities (Cultural, Sports, Academic, Cultural & Sports)', '2022-23');
INSERT INTO `criteria1` VALUES ('7', '7.1.8(C)', 'List of the faculty & students coming from the out of state', '2022-23');
INSERT INTO `criteria1` VALUES ('7', '7.1.9', 'Sensitization of students and employees of the institution to constitutional obligations:  values, rights, duties and responsibilities of citizens', '2022-23');
INSERT INTO `criteria1` VALUES ('7', '7.1.9(A)', 'List of the activities/Events conducted, and the number of students present. (Awareness programs on Women safety & protection, Anti ragging, Judicial rights, Gender equality, Traffic rules, Environment protection, Conservation of natural resources (power & water)) >', '2022-23');
INSERT INTO `criteria1` VALUES ('7', '7.2.1', 'Provide the weblink on the Institutional website regarding the Best practices as per  the prescribed format of NAAC', '2022-23');
INSERT INTO `criteria1` VALUES ('7', '7.3.1', 'Institutional Distinctiveness', '2022-23');

DROP TABLE IF EXISTS `criteria2`;
CREATE TABLE `criteria2` (
  `SI_no` int(10) NOT NULL,
  `Sub_no` varchar(30) NOT NULL,
  `Des` varchar(600) NOT NULL,
  `year` varchar(200) NOT NULL,
  PRIMARY KEY (`Sub_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `criteria2` VALUES ('1', '1.1.1', 'Copies of PEOs, POs & PSOs for all programs', '2022-23');
INSERT INTO `criteria2` VALUES ('1', '1.1.2', 'Number of Programmes where syllabus revision was carried out during the year(Data Requirement: Programme Code ,Names of the Programmes revised)', '2022-23');
INSERT INTO `criteria2` VALUES ('1', '1.1.2(A)', 'List of programs where syllabus revision has been carried out signed by the Principal', '2022-23');
INSERT INTO `criteria2` VALUES ('1', '1.1.2(B)', 'Approved Minutes of relevant Academic Council/BOS meetings highlighting the specific agenda item relevant to the metric year wise', '2022-23');
INSERT INTO `criteria2` VALUES ('1', '1.1.3', 'Number of courses focusing on employability/entrepreneurship/ skill development  offered by the Institution during the year(Data Requirement: Name of the Course with Course Code ,Name of the Programme ,Activities which have a direct bearing on employability/ entrepreneurship/ skill  development)', '2022-23');
INSERT INTO `criteria2` VALUES ('1', '1.1.3(A)', 'Syllabus copy of the courses highlighting Focus on employability/entrepreneurship/ skill development along with their course outcomes.', '2022-23');
INSERT INTO `criteria2` VALUES ('1', '1.1.3(B)', 'Minutes of the Boards of Studies/ Academic Council meetings with approval for these courses.', '2022-23');
INSERT INTO `criteria2` VALUES ('1', '1.1.3(C)', 'List of MoUs', '2022-23');
INSERT INTO `criteria2` VALUES ('1', '1.2.1', 'Number of new courses introduced across all programmes offered during the year(Data Requirement: Name of the newly introduced course (s)  ,Name of the Programme)', '2022-23');
INSERT INTO `criteria2` VALUES ('1', '1.2.1(A)', 'List of new courses introduced program-wise during the assessment period certified by the Principal.', '2022-23');
INSERT INTO `criteria2` VALUES ('1', '1.2.1(B)', 'Minutes of relevant Academic Council/BOS meetings highlighting the name of the new courses introduced', '2022-23');
INSERT INTO `criteria2` VALUES ('1', '1.2.2', 'Number of Programmes offered through Choice Based Credit System  (CBCS)/Elective Course System(Data Requirement: ,Names of all Programmes offered through CBCS ,Names of all Programmes offered through Elective Course System)', '2022-23');
INSERT INTO `criteria2` VALUES ('1', '1.2.2(A)', 'List of programs in which CBCS/Elective course system implemented in the last completed academic year certified by the principal', '2022-23');
INSERT INTO `criteria2` VALUES ('1', '1.2.2(B)', 'Structure of the program clearly indicating courses, credits/Electives and Minutes of relevant Academic Council/BOS meetings highlighting the relevant documents to this metric', '2022-23');
INSERT INTO `criteria2` VALUES ('1', '1.3.1', 'List and Description of courses relevant to Professional Ethics, Gender diversity and equality, Human Values, Environment and Sustainability, Women Empowerment introduced in the Curriculum along with the syllabus should be available in all the departments', '2022-23');
INSERT INTO `criteria2` VALUES ('1', '1.3.2', 'Number of value-added courses for imparting transferable and life skills offered  during the year:(Data Requirement:  Names of the value-added courses (each with 30 or more contact hours) ,No. of times offered (for each value-added course) during the year ,Total number of students enrolled ,Total number of students completing the course during the year', '2022-23');
INSERT INTO `criteria2` VALUES ('1', '1.3.2(A)', 'List of value-added courses which are optional and offered outside the curriculum of the programs with authorized sign.', '2022-23');
INSERT INTO `criteria2` VALUES ('1', '1.3.2(B)', 'Brochure and Course content or syllabus along with course outcome of Value-added courses offered.', '2022-23');
INSERT INTO `criteria2` VALUES ('1', '1.3.3', 'List of enrolled students for courses addressed in 1.3.2', '2022-23');
INSERT INTO `criteria2` VALUES ('1', '1.3.4', 'List of students undertaking the field projects/ internships / student projects program-wise in the last completed academic year along with the details of title, place of work etc.', '2022-23');
INSERT INTO `criteria2` VALUES ('1', '1.4.1', 'Sample Filled in feedback forms from the stakeholders to be provided.', '2022-23');
INSERT INTO `criteria2` VALUES ('1', '1.4.2', 'Stakeholder feedback analysis report signed by the authority', '2022-23');
INSERT INTO `criteria2` VALUES ('2', '2.1.1', 'Enrolment of Students ', '2022-23');
INSERT INTO `criteria2` VALUES ('2', '2.1.1(A)', 'Students admitted(HEI signed documents)', '2022-23');
INSERT INTO `criteria2` VALUES ('2', '2.1.1(B)', 'Enrolment Number_ AICTE letters of sanctioned intake', '2022-23');
INSERT INTO `criteria2` VALUES ('2', '2.1.1(C)', 'Enrolment Number _ Ratified list of admitted students', '2022-23');
INSERT INTO `criteria2` VALUES ('2', '2.1.2', 'Number of seats filled against reserved categories (SC, ST, OBC, Divyangjan,  etc.) as per the reservation policy during the year (exclusive of supernumerary  seats)', '2022-23');
INSERT INTO `criteria2` VALUES ('2', '2.1.2(A)', 'Reservation seats categories to be considered as per the state rule (APSHE Guidelines)', '2022-23');
INSERT INTO `criteria2` VALUES ('2', '2.1.2(B)', 'AP regulation for Admissions GO No. 73', '2022-23');
INSERT INTO `criteria2` VALUES ('2', '2.1.2(C)', 'Guidelines for filling the left-over vacancies under reservation', '2022-23');
INSERT INTO `criteria2` VALUES ('2', '2.1.2(D)', 'Category wise_ Ratified list of students admitted', '2022-23');
INSERT INTO `criteria2` VALUES ('2', '2.1.2(E)', 'Admission Abstract(HEI signed documents)', '2022-23');
INSERT INTO `criteria2` VALUES ('2', '2.2.1', 'The institution assesses students’ learning levels and organises special  programmes for both slow and advanced learners(Present a write-up within a maximum of 200 words)', '2022-23');
INSERT INTO `criteria2` VALUES ('2', '2.2.1(A)', 'Video records of the specific topics(A document having the links and screen shots of video course/LCS)', '2022-23');
INSERT INTO `criteria2` VALUES ('2', '2.2.1(B)', 'Classes for the slow learner(Time table conducted for CA failures and attendance sheets)', '2022-23');
INSERT INTO `criteria2` VALUES ('2', '2.2.1(C)', 'Additional assignment(sample copies of either question papers or evaluated assignment)', '2022-23');
INSERT INTO `criteria2` VALUES ('2', '2.2.1(D)', 'Make-up classes for course detention students/lateral entry students(Time tables and attendance sheets for each semester(1-8) at least one)', '2022-23');
INSERT INTO `criteria2` VALUES ('2', '2.2.1(E)', 'Remedial classes conducted for end semester failures(Time tables, attendance sheets and track sheets for each semester(1-8) at least one)', '2022-23');
INSERT INTO `criteria2` VALUES ('2', '2.2.1(F)', 'List of MOOCs courses(Details of student with course name and sample certificates)', '2022-23');
INSERT INTO `criteria2` VALUES ('2', '2.2.1(G)', 'List of the advanced learners opted Minors and honors(Abstract certified by controller of examination)', '2022-23');
INSERT INTO `criteria2` VALUES ('2', '2.2.1(H)', 'Student participation in technical events list with students event details and SAMPLE certificates', '2022-23');
INSERT INTO `criteria2` VALUES ('2', '2.2.2', 'Student – Teacher (full-time) ratio(Data Requirement:  Total number of students in the institution ,Total number of full-time teachers in the institution)', '2022-23');
INSERT INTO `criteria2` VALUES ('2', '2.2.2(A)', 'List of the full-time teachers', '2022-23');
INSERT INTO `criteria2` VALUES ('2', '2.2.2(B)', 'Student intake', '2022-23');
INSERT INTO `criteria2` VALUES ('2', '2.3.1', 'Student-centric methods such as experiential learning, participative learning and  problem-solving methodologies are used for enhancing learning experiences(Present a write-up within a maximum of 200 words.)', '2022-23');
INSERT INTO `criteria2` VALUES ('2', '2.3.1(A)', 'Academic regulations and curriculum(Recent)', '2022-23');
INSERT INTO `criteria2` VALUES ('2', '2.3.2', 'Teachers use ICT-enabled tools including online resources for effective teaching  and learning(Present a write-up within a maximum of 200 words.)', '2022-23');
INSERT INTO `criteria2` VALUES ('2', '2.3.2 (A)', 'ICT Tools Gadgets viz. Graphic tablets, MST, Smart pen, Projector(Geotagged photos with title of photo)', '2022-23');
INSERT INTO `criteria2` VALUES ('2', '2.3.3', 'Ratio of students to mentor for academic and other related issues', '2022-23');
INSERT INTO `criteria2` VALUES ('2', '2.3.3(A)', 'Circular of mentor – mentees', '2022-23');
INSERT INTO `criteria2` VALUES ('2', '2.3.3(B)', 'Mentor list as announced by the HEI', '2022-23');
INSERT INTO `criteria2` VALUES ('2', '2.3.3(C)', 'issues raised and resolved in the mentor system(Total records of a student -&gt; one sample from each semester)', '2022-23');
INSERT INTO `criteria2` VALUES ('2', '2.3.4', 'Preparation and adherence to Academic Calendar and Teaching Plans by the  institution(Describe the preparation of and adherence to the Academic Calendar and Teaching  Plans by the institution.  Present a write-up within a maximum of 200 words)', '2022-23');
INSERT INTO `criteria2` VALUES ('2', '2.3.4(A)', 'Academic calendar', '2022-23');
INSERT INTO `criteria2` VALUES ('2', '2.3.4(B)', 'Proof of course allotment for odd and even semesters', '2022-23');
INSERT INTO `criteria2` VALUES ('2', '2.3.4(C)', 'Proof for lecture plan, Schedule and diary(one course from each semester)', '2022-23');
INSERT INTO `criteria2` VALUES ('2', '2.3.4(D)', 'Minutes of AMC', '2022-23');
INSERT INTO `criteria2` VALUES ('2', '2.4.1', 'Number of full-time teachers against sanctioned posts during the year(Data Requirement:  Number of full-time teachers ,Number of sanctioned posts)', '2022-23');
INSERT INTO `criteria2` VALUES ('2', '2.4.1(A)', 'Sanction letter indicating number of posts', '2022-23');
INSERT INTO `criteria2` VALUES ('2', '2.4.1(B)', 'Department wise List of full-time teachers appointed', '2022-23');
INSERT INTO `criteria2` VALUES ('2', '2.4.2', 'Number of full-time teachers with PhD/ D.M. / M.Ch. / D.N.B Super-Specialty /  DSc / DLitt during the year(Data Requirement: List of full-time teachers with PhD/ D.M. / M.Ch. / D.N.B Super-Specialty /  DSc / DLitt.)', '2022-23');
INSERT INTO `criteria2` VALUES ('2', '2.4.3', 'Total teaching experience of full-time teachers in the same institution(Data Requirement:  Name and number of full-time teachers and their years of teaching experience in the institution)', '2022-23');
INSERT INTO `criteria2` VALUES ('2', '2.5.1', 'Number of days from the date of last semester-end/ year- end examination till the  declaration of results during the year(Number of days from the date of last semester-end / year-end examination till the  declaration of results year-wise during the year)', '2022-23');
INSERT INTO `criteria2` VALUES ('2', '2.5.1(A)', 'Examination Result Notifications', '2022-23');
INSERT INTO `criteria2` VALUES ('2', '2.5.1(B)', 'List of Programs offered and the last date of the latest semester end exams and date of result declaration', '2022-23');
INSERT INTO `criteria2` VALUES ('2', '2.5.2', 'Number of students’ complaints/grievances against evaluation against the total  number who appeared in the examinations during the year', '2022-23');
INSERT INTO `criteria2` VALUES ('2', '2.5.2(A)', 'Number of complaints', '2022-23');
INSERT INTO `criteria2` VALUES ('2', '2.5.2(B)', 'Minutes of the examination Committee', '2022-23');
INSERT INTO `criteria2` VALUES ('2', '2.5.3', 'IT integration and reforms in the examination procedures and processes  including Continuous Internal Assessment (CIA) have brought in considerable  improvement in the Examination Management System (EMS) of the Institution(Describe the examination reforms with reference to the following within a minimum  of 200 words :Examination procedures ,Processes/Procedures integrating IT ,Continuous Internal Assessment System) ', '2022-23');
INSERT INTO `criteria2` VALUES ('2', '2.5.3(A)', 'Examination Regulations', '2022-23');
INSERT INTO `criteria2` VALUES ('2', '2.5.3(B)', 'Question Paper templates', '2022-23');
INSERT INTO `criteria2` VALUES ('2', '2.5.3(C)', 'Proof of the hybrid grading sheet', '2022-23');
INSERT INTO `criteria2` VALUES ('2', '2.5.3(D)', 'Examination Results link in the website', '2022-23');
INSERT INTO `criteria2` VALUES ('2', '2.5.3(E)', 'Proof of the OMR', '2022-23');
INSERT INTO `criteria2` VALUES ('2', '2.6.1', 'Programme Outcomes and Course Outcomes for all Programmes offered by the  institution are stated and displayed on the website and communicated to teachers  and students(Describe Course Outcomes (COs) for all courses and the mechanism of  communication to teachers and students within a maximum of 200 words.  Upload COs for all Courses) ', '2022-23');
INSERT INTO `criteria2` VALUES ('2', '2.6.1(A)', 'List of POs and COs(COs for at least two courses from each semester)', '2022-23');
INSERT INTO `criteria2` VALUES ('2', '2.6.1(B)', 'Photo gallery of the COs & POs displayed Program wise', '2022-23');
INSERT INTO `criteria2` VALUES ('2', '2.6.2', 'Attainment of Programme Outcomes and Course Outcomes as evaluated by the  institution(Describe the method of measuring the attainment of POs, PSOs and COs and the  level of attaiment of POs , PSOs and COs in not more than 200 words.)', '2022-23');
INSERT INTO `criteria2` VALUES ('2', '2.6.2(A)', 'CO and PO attainment', '2022-23');
INSERT INTO `criteria2` VALUES ('2', '2.6.2(B)', 'Files related to all surveys for the indirect assessment', '2022-23');
INSERT INTO `criteria2` VALUES ('2', '2.6.3', 'Pass Percentage of students', '2022-23');
INSERT INTO `criteria2` VALUES ('2', '2.6.3(A)', 'Annual report of CoE indicating the pass percentage', '2022-23');
INSERT INTO `criteria2` VALUES ('2', '2.6.3(B)', 'Certified report from CoE indicating Students eligible for the degree program', '2022-23');
INSERT INTO `criteria2` VALUES ('2', '2.7.1', 'Student Satisfaction Survey (SSS) on overall institutional performance  (Institution may design its own questionnaire). Results and details need to be  provided as a weblink', '2022-23');
INSERT INTO `criteria2` VALUES ('3', '3.1.1', 'The institution’s research facilities are frequently updated and there is a welldefined policy for promotion of research which is uploaded on the institutional  website and implemented(Present a write-up within a maximum of 200 words.)', '2022-23');
INSERT INTO `criteria2` VALUES ('3', '3.1.1(A)', 'List of research equipment along with the proof of purchases (Bill copies)', '2022-23');
INSERT INTO `criteria2` VALUES ('3', '3.1.1(B)', 'Policy for Faculty Assessment and Development Scheme', '2022-23');
INSERT INTO `criteria2` VALUES ('3', '3.1.1(C)', 'Financial Incentives for attending conferences, seminars and QIP', '2022-23');
INSERT INTO `criteria2` VALUES ('3', '3.1.1(D)', 'Sanction letters for the funded research projects & UCs for the projects completed', '2022-23');
INSERT INTO `criteria2` VALUES ('3', '3.1.1(E)', 'Copies of all MoUs for collaborative research', '2022-23');
INSERT INTO `criteria2` VALUES ('3', '3.1.1(F)', 'Minutes of the governing council meeting-reflecting the research promotions', '2022-23');
INSERT INTO `criteria2` VALUES ('3', '3.1.2', 'Details of Seed money', '2022-23');
INSERT INTO `criteria2` VALUES ('3', '3.1.3', 'Number of teachers who were awarded national / international fellowship(s) for  advanced studies/research during the year', '2022-23');
INSERT INTO `criteria2` VALUES ('3', '3.2.1', 'Grants received from Government and Non-Governmental agencies for research  projects, endowments, Chairs during the year (INR in Lakhs)', '2022-23');
INSERT INTO `criteria2` VALUES ('3', '3.2.1(A)', 'List of Grants received for research projects', '2022-23');
INSERT INTO `criteria2` VALUES ('3', '3.2.1(B)', 'e-copies of grants sanctioned', '2022-23');
INSERT INTO `criteria2` VALUES ('3', '3.2.2', 'List of teachers having research projects during the year', '2022-23');
INSERT INTO `criteria2` VALUES ('3', '3.2.3', 'Number of teachers recognized as research guides', '2022-23');
INSERT INTO `criteria2` VALUES ('3', '3.2.4', 'Number of departments having research projects', '2022-23');
INSERT INTO `criteria2` VALUES ('3', '3.2.4(A)', 'Web Links to Funding Agencies', '2022-23');
INSERT INTO `criteria2` VALUES ('3', '3.3.1', 'Institution has created an ecosystem for innovations and creation and transfer of  knowledge supported by dedicated centres for research, entrepreneurship,  community orientation, incubation, etc.(Present a write-up within a maximum of 200 words)', '2022-23');
INSERT INTO `criteria2` VALUES ('3', '3.3.1(A)', 'MSME business incubation center – sanction letter', '2022-23');
INSERT INTO `criteria2` VALUES ('3', '3.3.1(B)', 'AICTE sponsored EDC – Sanction letters and list of the activities conducted by EDC', '2022-23');
INSERT INTO `criteria2` VALUES ('3', '3.3.1(C)', 'Dedicated centres for research', '2022-23');
INSERT INTO `criteria2` VALUES ('3', '3.3.2', 'Detailed report including photos, resource persons etc.', '2022-23');
INSERT INTO `criteria2` VALUES ('3', '3.4.1', 'The Institution ensures implementation of its Code of Ethics for Research  uploaded in the website through the following:  1. Research Advisory Committee 2. Ethics Committee 3. Inclusion of Research Ethics in the research methodology course work  4. Plagiarism check through authenticated software Options: A. All of the above B. Any 3 of the above C. Any 2 of the above D. Any 1 of the above E. None of the above', '2022-23');
INSERT INTO `criteria2` VALUES ('3', '3.4.1(A)', 'Research Advisory Committee', '2022-23');
INSERT INTO `criteria2` VALUES ('3', '3.4.1(B)', 'Ethics Committee', '2022-23');
INSERT INTO `criteria2` VALUES ('3', '3.4.1(C)', 'Inclusion of Research Ethics in the research methodology course work', '2022-23');
INSERT INTO `criteria2` VALUES ('3', '3.4.1(D)', 'Anti Plagiarism software approved by JNTU', '2022-23');
INSERT INTO `criteria2` VALUES ('3', '3.4.2', 'Number of PhD candidates registered per teacher (as per the data given with  regard to recognized PhD guides/ supervisors provided in Metric No. 3.2.3) during  the year', '2022-23');
INSERT INTO `criteria2` VALUES ('3', '3.4.2(A)', 'List of Faculty along with the names of Research scholars', '2022-23');
INSERT INTO `criteria2` VALUES ('3', '3.4.2(B)', 'Copy of the Registration letters/Joining letters', '2022-23');
INSERT INTO `criteria2` VALUES ('3', '3.4.3', 'Number of research papers per teacher in CARE Journals notified on UGC  website during the year', '2022-23');
INSERT INTO `criteria2` VALUES ('3', '3.4.4', 'Number of books and chapters in edited volumes / books published per teacher  during the year', '2022-23');
INSERT INTO `criteria2` VALUES ('3', '3.4.5', 'Bibliometrics of the publications based on average Citation Index', '2022-23');
INSERT INTO `criteria2` VALUES ('3', '3.4.6', 'Bibliometrics of the publication-based h-Index of the University', '2022-23');
INSERT INTO `criteria2` VALUES ('3', '3.5.1', 'Audited statements for Revenue generated from consultancy', '2022-23');
INSERT INTO `criteria2` VALUES ('3', '3.5.2', 'Total amount spent on developing facilities, training teachers and clerical/project  staff for undertaking consultancy during the year', '2022-23');
INSERT INTO `criteria2` VALUES ('3', '3.6.1', 'Extension activities carried out in the neighbourhood sensitising students to social  issues for their holistic development, and the impact thereof during the year', '2022-23');
INSERT INTO `criteria2` VALUES ('3', '3.6.1(A)', 'List of NSS activities conducted year wise and number of students participated', '2022-23');
INSERT INTO `criteria2` VALUES ('3', '3.6.1(B)', 'Covid-19 booster report', '2022-23');
INSERT INTO `criteria2` VALUES ('3', '3.6.1(C)', 'MGNCRE Reports', '2022-23');
INSERT INTO `criteria2` VALUES ('3', '3.6.1(D)', 'Community Radio report', '2022-23');
INSERT INTO `criteria2` VALUES ('3', '3.6.1(E)', 'List of NCC activities conducted year wise and number of students participated', '2022-23');
INSERT INTO `criteria2` VALUES ('3', '3.6.2', 'Number of awards and recognition received by the Institution, its teachers and  students for extension activities from Government / Government-recognised bodies', '2022-23');
INSERT INTO `criteria2` VALUES ('3', '3.6.3', 'Number of extension and outreach programmes conducted by the institution through  NSS/NCC during the year', '2022-23');
INSERT INTO `criteria2` VALUES ('3', '3.6.4', 'Number of students participating in extension activities listed in 3.6.3 during the  year', '2022-23');
INSERT INTO `criteria2` VALUES ('3', '3.7.1', 'Number of collaborative activities during the year for research/ faculty exchange/  student exchange/ internship/ on-the-job training/ project work', '2022-23');
INSERT INTO `criteria2` VALUES ('3', '3.7.2', 'Number of functional MoUs with institutions of national and/or international  importance, other universities, industries, corporate houses, etc. during the year  (only functional MoUs with ongoing activities to be considered)', '2022-23');
INSERT INTO `criteria2` VALUES ('3', '3.7.2(A)', 'e-copies of functional MoUs', '2022-23');
INSERT INTO `criteria2` VALUES ('3', '3.7.2(B)', 'e-copies of Activities of MOUs', '2022-23');
INSERT INTO `criteria2` VALUES ('4', '4.1.1', 'The Institution has adequate infrastructure and physical facilities for teachinglearning, viz., classrooms, laboratories, computing equipments, etc.', '2022-23');
INSERT INTO `criteria2` VALUES ('4', '4.1.1(A)', 'Details of the Classrooms, Labs & Other facilities across all the six blocks', '2022-23');
INSERT INTO `criteria2` VALUES ('4', '4.1.1(B)', 'List of the laboratories with titles', '2022-23');
INSERT INTO `criteria2` VALUES ('4', '4.1.1(C)', 'Campus LAN diagram', '2022-23');
INSERT INTO `criteria2` VALUES ('4', '4.1.1(D)', 'Proof of bandwidth', '2022-23');
INSERT INTO `criteria2` VALUES ('4', '4.1.1(E)', 'List of the software', '2022-23');
INSERT INTO `criteria2` VALUES ('4', '4.1.1(F)', 'Photo gallery of all the academic blocks, classrooms, drawing hall, seminar hall, auditorium, and Labs', '2022-23');
INSERT INTO `criteria2` VALUES ('4', '4.1.2', 'The institution has adequate facilities for cultural activities, yoga, sports and games  (indoor and outdoor) including gymnasium, yoga centre, auditorium etc.)', '2022-23');
INSERT INTO `criteria2` VALUES ('4', '4.1.2(A)', 'Colleague of Geo-tagged pictures', '2022-23');
INSERT INTO `criteria2` VALUES ('4', '4.1.2(B)', 'Area details of the all the facilities', '2022-23');
INSERT INTO `criteria2` VALUES ('4', '4.1.2(C)', 'Photo gallery of the various activities', '2022-23');
INSERT INTO `criteria2` VALUES ('4', '4.1.3', 'Number of classrooms and seminar halls with ICT-enabled facilities', '2022-23');
INSERT INTO `criteria2` VALUES ('4', '4.1.3(A)', 'Geo-tagged photographs of classrooms with ICT enabled facilities', '2022-23');
INSERT INTO `criteria2` VALUES ('4', '4.1.3(B)', 'Class Timetables', '2022-23');
INSERT INTO `criteria2` VALUES ('4', '4.1.4', 'Expenditure for infrastructure augmentation, excluding salary, during the year (INR  in Lakhs)', '2022-23');
INSERT INTO `criteria2` VALUES ('4', '4.1.4(A)', 'Budget allocation', '2022-23');
INSERT INTO `criteria2` VALUES ('4', '4.1.4(B)', 'Provide the consolidated fund allocation towards infrastructure augmentation facilities duly certified by Head of the Institution', '2022-23');
INSERT INTO `criteria2` VALUES ('4', '4.2.1', 'Library is automated using Integrated Library Management System (ILMS)', '2022-23');
INSERT INTO `criteria2` VALUES ('4', '4.2.1(A)', 'Library operates through LIBSYS', '2022-23');
INSERT INTO `criteria2` VALUES ('4', '4.2.1(B)', 'All the books are provided with RF Id security tags', '2022-23');
INSERT INTO `criteria2` VALUES ('4', '4.2.1(C)', 'Copy of the latest License agreement of LIBSYS-7', '2022-23');
INSERT INTO `criteria2` VALUES ('4', '4.2.1(D)', 'Digital Library', '2022-23');
INSERT INTO `criteria2` VALUES ('4', '4.2.2', 'Institution has access to the following', '2022-23');
INSERT INTO `criteria2` VALUES ('4', '4.2.2 (D)', 'Specific details in respect of e-resources selected.', '2022-23');
INSERT INTO `criteria2` VALUES ('4', '4.2.2 (E)', 'Databases', '2022-23');
INSERT INTO `criteria2` VALUES ('4', '4.2.2(A)', 'Details of subscriptions of e-journals', '2022-23');
INSERT INTO `criteria2` VALUES ('4', '4.2.2(B)', 'Letter of subscription', '2022-23');
INSERT INTO `criteria2` VALUES ('4', '4.2.2(C)', 'Screenshots of the facilities claimed with the name of HEI.', '2022-23');
INSERT INTO `criteria2` VALUES ('4', '4.2.3', 'Expenditure on purchase of books/ e-books and subscription to journals/e-journals  during the year (INR in lakhs)', '2022-23');
INSERT INTO `criteria2` VALUES ('4', '4.2.3(A)', 'Consolidated extract of expenditure', '2022-23');
INSERT INTO `criteria2` VALUES ('4', '4.2.3(B)', 'Invoices of all the expenditure', '2022-23');
INSERT INTO `criteria2` VALUES ('4', '4.2.4', 'Usage of library by teachers and students (footfalls and login data for online access)', '2022-23');
INSERT INTO `criteria2` VALUES ('4', '4.2.4(A)', 'Certified e-copy of the ledger for footfalls for 5days', '2022-23');
INSERT INTO `criteria2` VALUES ('4', '4.2.4(B)', 'Certified screenshots of the data for the same 5 days for online access', '2022-23');
INSERT INTO `criteria2` VALUES ('4', '4.2.4(C)', 'Last page of accession register details', '2022-23');
INSERT INTO `criteria2` VALUES ('4', '4.3.1', 'Institution has an IT policy covering Wi-Fi, cyber security, etc. and has allocated  budget for updating its IT facilities', '2022-23');
INSERT INTO `criteria2` VALUES ('4', '4.3.1(A)', 'Colleague of Geo-tagged pictures', '2022-23');
INSERT INTO `criteria2` VALUES ('4', '4.3.1(B)', 'Policy document', '2022-23');
INSERT INTO `criteria2` VALUES ('4', '4.3.1(C)', 'Campus Wi-Fi/Network diagram', '2022-23');
INSERT INTO `criteria2` VALUES ('4', '4.3.1(D)', 'Budget of the year 2020-21', '2022-23');
INSERT INTO `criteria2` VALUES ('4', '4.3.2', 'Student - Computer ratio', '2022-23');
INSERT INTO `criteria2` VALUES ('4', '4.3.2(A)', 'Computer Bills', '2022-23');
INSERT INTO `criteria2` VALUES ('4', '4.3.2(B)', 'Student strength', '2022-23');
INSERT INTO `criteria2` VALUES ('4', '4.3.3', 'Bandwidth of internet connection in the Institution and the number of students on  campus', '2022-23');
INSERT INTO `criteria2` VALUES ('4', '4.3.3(A)', 'Details of available bandwidth of internet connection in the Institution', '2022-23');
INSERT INTO `criteria2` VALUES ('4', '4.3.3(B)', 'Bills for any one month/one quarter.', '2022-23');
INSERT INTO `criteria2` VALUES ('4', '4.3.3(C)', 'e-copy of document of agreement with the service provider.', '2022-23');
INSERT INTO `criteria2` VALUES ('4', '4.3.4', 'Institution has facilities for e-content development', '2022-23');
INSERT INTO `criteria2` VALUES ('4', '4.3.4(A)', 'Geo tagged photographs of Media Centre, Audio Visual Centre etc.,', '2022-23');
INSERT INTO `criteria2` VALUES ('4', '4.3.4(B)', 'Purchase bills for Lecture Capturing System', '2022-23');
INSERT INTO `criteria2` VALUES ('4', '4.3.4(C)', 'Audited income expenditure statement highlighting the relevant expenditure.', '2022-23');
INSERT INTO `criteria2` VALUES ('4', '4.4.1', 'Expenditure incurred on maintenance of physical and academic support facilities, excluding salary component, during the year (INR in lakhs)', '2022-23');
INSERT INTO `criteria2` VALUES ('4', '4.4.1(A)', 'Audited statements of accounts.', '2022-23');
INSERT INTO `criteria2` VALUES ('4', '4.4.2', 'There are established systems and procedures for maintaining and utilizing  physical, academic and support facilities – classrooms, laboratory, library, sports  complex, computers, etc.', '2022-23');
INSERT INTO `criteria2` VALUES ('4', '4.4.2(A)', 'Schedules of Library, Sport complex ', '2022-23');
INSERT INTO `criteria2` VALUES ('4', '4.4.2(B)', 'Laboratory Timetables', '2022-23');
INSERT INTO `criteria2` VALUES ('4', '4.4.2(C)', 'SOP for Laboratory utilization', '2022-23');
INSERT INTO `criteria2` VALUES ('4', '4.4.2(D)', 'SOP for usage of general amenities', '2022-23');
INSERT INTO `criteria2` VALUES ('4', '4.4.2(E)', 'Geo-tagged photos of GAMYA', '2022-23');
INSERT INTO `criteria2` VALUES ('4', '4.4.2(F)', 'Maintenance schedules and AMC letters from Estate department', '2022-23');
INSERT INTO `criteria2` VALUES ('5', '5.1.1', 'Number of students benefitted by scholarships and freeships provided by the  Government during the year', '2022-23');
INSERT INTO `criteria2` VALUES ('5', '5.1.2', 'Number of students benefitted by scholarships and freeships provided by the  institution and non-government agencies during the year', '2022-23');
INSERT INTO `criteria2` VALUES ('5', '5.1.3', 'The following Capacity Development and Skill Enhancement activities are  organised for improving students’ capabilities', '2022-23');
INSERT INTO `criteria2` VALUES ('5', '5.1.3(A)', 'Soft Skill, Language and Communication', '2022-23');
INSERT INTO `criteria2` VALUES ('5', '5.1.3(B)', 'Yoga Class', '2022-23');
INSERT INTO `criteria2` VALUES ('5', '5.1.3(C)', 'Awareness of Trends in technology', '2022-23');
INSERT INTO `criteria2` VALUES ('5', '5.1.4', 'Number of students benefitted from guidance/coaching for competitive  examinations and career counselling offered by the institution during the year', '2022-23');
INSERT INTO `criteria2` VALUES ('5', '5.1.5', 'The institution adopts the following mechanism for redressal of students’  grievances, including sexual harassment and ragging', '2022-23');
INSERT INTO `criteria2` VALUES ('5', '5.2.1', 'Number of outgoing students who got placement during the year', '2022-23');
INSERT INTO `criteria2` VALUES ('5', '5.2.2', 'Number of outgoing students progressing to higher education during the year', '2022-23');
INSERT INTO `criteria2` VALUES ('5', '5.2.3', 'Number of students qualifying in state/ national/ international level examinations  during the year', '2022-23');
INSERT INTO `criteria2` VALUES ('5', '5.3.1', 'Number of awards/medals for outstanding performance in sports and/or cultural  activities at inter-university / state /national / international events (award for a team  event should be counted as one) during the year', '2022-23');
INSERT INTO `criteria2` VALUES ('5', '5.3.2', 'Presence of an active Student Council and representation of students in academic  and administrative bodies/committees of the institution', '2022-23');
INSERT INTO `criteria2` VALUES ('5', '5.3.3', 'Number of sports and cultural events / competitions organised by the institution', '2022-23');
INSERT INTO `criteria2` VALUES ('5', '5.4.1', 'The Alumni Association and its Chapters (registered and functional) contribute  significantly to the development of the institution through financial and other  support services', '2022-23');
INSERT INTO `criteria2` VALUES ('5', '5.4.2', 'Alumni’s financial contribution during the year', '2022-23');
INSERT INTO `criteria2` VALUES ('6', '6.1.1', 'The governance of the institution is reflective of an effective leadership in tune with  the vision and mission of the Institution', '2022-23');
INSERT INTO `criteria2` VALUES ('6', '6.1.1(A)', 'Academic Monitoring Committee meeting minutes', '2022-23');
INSERT INTO `criteria2` VALUES ('6', '6.1.1(B)', 'Placement Committee meeting minutes', '2022-23');
INSERT INTO `criteria2` VALUES ('6', '6.1.1(C)', 'SAC - coordinators meeting minutes', '2022-23');
INSERT INTO `criteria2` VALUES ('6', '6.1.1(D)', 'Anti Ragging Committee meeting minutes', '2022-23');
INSERT INTO `criteria2` VALUES ('6', '6.1.1(E)', 'IQAC meeting minutes', '2022-23');
INSERT INTO `criteria2` VALUES ('6', '6.1.1(F)', 'Board of Studies Meeting minutes', '2022-23');
INSERT INTO `criteria2` VALUES ('6', '6.1.1(G)', 'Academic Council meeting minutes', '2022-23');
INSERT INTO `criteria2` VALUES ('6', '6.1.1(H)', 'Governing Council meeting minutes', '2022-23');
INSERT INTO `criteria2` VALUES ('6', '6.1.1(I)', 'HOD/Academic Development Committee Meeting Minutes', '2022-23');
INSERT INTO `criteria2` VALUES ('6', '6.1.1(J)', 'Finance Committee meeting minutes', '2022-23');
INSERT INTO `criteria2` VALUES ('6', '6.1.1(K)', 'Library Committee meeting minutes', '2022-23');
INSERT INTO `criteria2` VALUES ('6', '6.1.1(L)', 'Town Hall meeting minutes', '2022-23');
INSERT INTO `criteria2` VALUES ('6', '6.1.2', 'Effective leadership is reflected in various institutional practices such as decentralization and participative management', '2022-23');
INSERT INTO `criteria2` VALUES ('6', '6.1.2(A)', 'Strat-Plan', '2022-23');
INSERT INTO `criteria2` VALUES ('6', '6.1.2(B)', 'Requisition for two set of mid question papers from CoE – e-mail proof', '2022-23');
INSERT INTO `criteria2` VALUES ('6', '6.1.2(C)', 'Declaration of mid question paper set number from CoE– e-mail proof', '2022-23');
INSERT INTO `criteria2` VALUES ('6', '6.1.2(D)', 'Uniform Evaluation', '2022-23');
INSERT INTO `criteria2` VALUES ('6', '6.1.2(E)', 'Research Review meeting minutes', '2022-23');
INSERT INTO `criteria2` VALUES ('6', '6.2.1', 'The institutional Strategic/ Perspective plan has been clearly articulated and  implemented', '2022-23');
INSERT INTO `criteria2` VALUES ('6', '6.2.1(A)', 'Strat-Plan', '2022-23');
INSERT INTO `criteria2` VALUES ('6', '6.2.1(B)', 'Merit scholarships based on AP-EAMCET rank', '2022-23');
INSERT INTO `criteria2` VALUES ('6', '6.2.1(C)', 'Meritorious scholarships for students', '2022-23');
INSERT INTO `criteria2` VALUES ('6', '6.2.1(D)', 'Details of the GATE training classes conducted; List of students attended GATE Coaching & secured score', '2022-23');
INSERT INTO `criteria2` VALUES ('6', '6.2.1(E)', 'Details of the CRT programs/ Technical training/Competitions conducted (Total number of hours), the List of the students attended the training programs & Proof of attendance, Branch wise list of the students placed, and the number of companies visited. List of students attended the WTN.', '2022-23');
INSERT INTO `criteria2` VALUES ('6', '6.2.1(F)', 'Motivational and inspirational talks by the industry experts', '2022-23');
INSERT INTO `criteria2` VALUES ('6', '6.2.1(G)', 'Social media updates', '2022-23');
INSERT INTO `criteria2` VALUES ('6', '6.2.2', 'The functioning of the various institutional bodies is effective and efficient as visible  from the policies, administrative set-up, appointment and service rules, procedures,  etc. ', '2022-23');
INSERT INTO `criteria2` VALUES ('6', '6.2.2(A)', 'Organogram', '2022-23');
INSERT INTO `criteria2` VALUES ('6', '6.2.2(B)', 'HR & Service rules/Incentive policies', '2022-23');
INSERT INTO `criteria2` VALUES ('6', '6.2.2(D)', 'Frequency and conduct of the meetings of governance committees (GC, AC, BOS, FC)', '2022-23');
INSERT INTO `criteria2` VALUES ('6', '6.2.2(E)', 'SOP for procurement (AOP, MRN, Comparative statements and Purchase Orders)', '2022-23');
INSERT INTO `criteria2` VALUES ('6', '6.2.3', 'Implementation of e-governance in areas of operation: 1. Administration 2. Finance and Accounts 3. Student Admission and Support 4. Examination', '2022-23');
INSERT INTO `criteria2` VALUES ('6', '6.3.1', 'The institution has effective welfare measures for teaching and non-teaching staff  and avenues for their career development/ progression', '2022-23');
INSERT INTO `criteria2` VALUES ('6', '6.3.1(A)', 'HR Policies (Welfare & Career development)', '2022-23');
INSERT INTO `criteria2` VALUES ('6', '6.3.1(B)', 'Details of the training programs conducted for teaching & non-teaching staff', '2022-23');
INSERT INTO `criteria2` VALUES ('6', '6.3.1(C)', 'Details of the staff (Teaching & Non-teaching promoted)', '2022-23');
INSERT INTO `criteria2` VALUES ('6', '6.3.1(D)', 'Details of on campus housing, Term insurance, Medical insurance, Children education, ESI, Cooperative credit society, Concessions in IP/OP services and Gratuity', '2022-23');
INSERT INTO `criteria2` VALUES ('6', '6.3.1(E)', 'Details of the faculty received incentives for completion of Ph.D./ QIP', '2022-23');
INSERT INTO `criteria2` VALUES ('6', '6.3.2', 'Number of teachers provided with financial support to attend conferences /  workshops and towards payment of membership fee of professional bodies during the  year', '2022-23');
INSERT INTO `criteria2` VALUES ('6', '6.3.2(A)', 'Number of teachers provided with financial support to attend conferences / workshops and towards payment of membership fee of professional bodies during the year', '2022-23');
INSERT INTO `criteria2` VALUES ('6', '6.3.3', 'Number of professional development / administrative training programmes  organized by the Institution for its teaching and non-teaching staff during the year', '2022-23');
INSERT INTO `criteria2` VALUES ('6', '6.3.3(A)', 'Annual Reports highlighting training programs conducted for teaching & non teaching', '2022-23');
INSERT INTO `criteria2` VALUES ('6', '6.3.3(B)', 'Training programs conducted for teaching & non-teaching', '2022-23');
INSERT INTO `criteria2` VALUES ('6', '6.3.4', 'Number of teachers who have undergone online/ face-to-face Faculty Development  Programmes during the year: (Professional Development Programmes, Orientation / Induction Programmes,  Refresher Courses, Short-Term Course, etc.)', '2022-23');
INSERT INTO `criteria2` VALUES ('6', '6.3.4(A)', 'List of Faculty FDPS Attended & Proofs', '2022-23');
INSERT INTO `criteria2` VALUES ('6', '6.4.1', 'Institution conducts internal and external financial audits regularly', '2022-23');
INSERT INTO `criteria2` VALUES ('6', '6.4.2', 'Funds / Grants received from non-government bodies, individuals, and  philanthropists during the year (not covered in Criterion III and V) (INR in lakhs)', '2022-23');
INSERT INTO `criteria2` VALUES ('6', '6.4.3', 'Institutional strategies for mobilisation of funds and the optimal utilisation of  resources', '2022-23');
INSERT INTO `criteria2` VALUES ('6', '6.5.1', 'Internal Quality Assurance Cell (IQAC) has contributed significantly for  institutionalizing quality assurance strategies and processes visible in terms of  incremental improvements made during the preceding year with regard to quality (in  case of the First Cycle): Incremental improvements made during the preceding year with regard to quality  and post-accreditation quality initiatives (Second and subsequent cycles)', '2022-23');
INSERT INTO `criteria2` VALUES ('6', '6.5.1(A)', 'List of companies hosted Internship', '2022-23');
INSERT INTO `criteria2` VALUES ('6', '6.5.1(B)', 'FADS – Supporting Docs', '2022-23');
INSERT INTO `criteria2` VALUES ('6', '6.5.1(C)', 'Consolidated initiatives of IQAC (strategies & contributions of IQAC mentioned)', '2022-23');
INSERT INTO `criteria2` VALUES ('6', '6.5.2', 'The institution reviews its teaching-learning process, structures and methodologies  of operation and learning outcomes at periodic intervals through its IQAC as per  norms', '2022-23');
INSERT INTO `criteria2` VALUES ('6', '6.5.2(A)', 'List of the IQAC initiatives taken up to enhance the students’ performance (Course coordinator meetings, AMC meeting minutes, Remedial classes for slow learners)', '2022-23');
INSERT INTO `criteria2` VALUES ('6', '6.5.2(B)', 'Details of the progression of the students from 1st to 8th semesters branch wise for 2020-21 for all the batches graduated – 1 Bar chart, in each bar chart with eight bars', '2022-23');
INSERT INTO `criteria2` VALUES ('6', '6.5.2(C)', 'Structures & methodologies of operations ISO audits, Academic audits -Internal & External branch wise', '2022-23');
INSERT INTO `criteria2` VALUES ('6', '6.5.2(D)', 'Internal & External Academic Audit Report', '2022-23');
INSERT INTO `criteria2` VALUES ('6', '6.5.3', 'Quality assurance initiatives of the institution include', '2022-23');
INSERT INTO `criteria2` VALUES ('6', '6.5.3(A)', 'IQAC meeting minutes', '2022-23');
INSERT INTO `criteria2` VALUES ('6', '6.5.3(B)', 'Feedback system of the institution', '2022-23');
INSERT INTO `criteria2` VALUES ('6', '6.5.3(C)', 'Feedback system for design and review of syllabus', '2022-23');
INSERT INTO `criteria2` VALUES ('6', '6.5.3(D)', 'Collaborative quality initiatives', '2022-23');
INSERT INTO `criteria2` VALUES ('6', '6.5.3(E)', 'Participation in NIRF', '2022-23');
INSERT INTO `criteria2` VALUES ('6', '6.5.3(F)', 'NBA accreditation', '2022-23');
INSERT INTO `criteria2` VALUES ('6', '6.5.3(G)', 'NAAC accreditation', '2022-23');
INSERT INTO `criteria2` VALUES ('6', '6.5.3(H)', 'IQAC Feedback analysis', '2022-23');
INSERT INTO `criteria2` VALUES ('7', '7.1.1', 'Measures initiated by the institution for the promotion of gender equity during the  year', '2022-23');
INSERT INTO `criteria2` VALUES ('7', '7.1.1(A)', 'Action plan of WEC for gender sensitization', '2022-23');
INSERT INTO `criteria2` VALUES ('7', '7.1.1(B)', 'Campus surveillance with CC TV (Audit report)', '2022-23');
INSERT INTO `criteria2` VALUES ('7', '7.1.1(C)', 'Policy for women security and safety (PASH)', '2022-23');
INSERT INTO `criteria2` VALUES ('7', '7.1.1(D)', 'Photographs of Exclusive reading rooms, waiting rooms and rest rooms', '2022-23');
INSERT INTO `criteria2` VALUES ('7', '7.1.1(E)', 'Day care centre for the kids (Beneficiaries)', '2022-23');
INSERT INTO `criteria2` VALUES ('7', '7.1.1(F)', 'Welfare measures (Maternity leave for two kids)', '2022-23');
INSERT INTO `criteria2` VALUES ('7', '7.1.1(G)', 'List of the beneficiaries', '2022-23');
INSERT INTO `criteria2` VALUES ('7', '7.1.10', 'The institution has a prescribed code of conduct for students, teachers, administrators  and other staff and conducts periodic sensitization programmes in this regard', '2022-23');
INSERT INTO `criteria2` VALUES ('7', '7.1.10(A)', 'Details of the monitoring committee composition and minutes of the committee meeting, number of programmes organized, reports on the various programmes, etc. in support of the claims', '2022-23');
INSERT INTO `criteria2` VALUES ('7', '7.1.10(B)', 'Policy document on code of ethics', '2022-23');
INSERT INTO `criteria2` VALUES ('7', '7.1.10(C)', 'Circulars and geo tagged photographs and caption of the activities organized under the metric for teachers, students, administrators and other staffs', '2022-23');
INSERT INTO `criteria2` VALUES ('7', '7.1.11', 'Institution celebrates / organizes national and international commemorative days,  events and festivals', '2022-23');
INSERT INTO `criteria2` VALUES ('7', '7.1.11(A)', 'Annual report of the celebrations and commemorative events', '2022-23');
INSERT INTO `criteria2` VALUES ('7', '7.1.11(B)', 'Photographs of some of the events', '2022-23');
INSERT INTO `criteria2` VALUES ('7', '7.1.2', 'The Institution has facilities for alternate sources of energy and energy conservation ', '2022-23');
INSERT INTO `criteria2` VALUES ('7', '7.1.2(A)', 'Geo tagged photographs with caption of the facilities', '2022-23');
INSERT INTO `criteria2` VALUES ('7', '7.1.2(B)', 'Bills for the purchase of equipment for the facilities', '2022-23');
INSERT INTO `criteria2` VALUES ('7', '7.1.2(C)', 'Permission document for connection to the grid from Government', '2022-23');
INSERT INTO `criteria2` VALUES ('7', '7.1.3', 'Describe the facilities in the institution for the management of the following types of  degradable and non-degradable waste (within a maximum of 200 words)', '2022-23');
INSERT INTO `criteria2` VALUES ('7', '7.1.3(A)', 'Geo tagged photographs of the facilities', '2022-23');
INSERT INTO `criteria2` VALUES ('7', '7.1.3(B)', 'SOP for solid waste management', '2022-23');
INSERT INTO `criteria2` VALUES ('7', '7.1.4', 'Water conservation facilities available in the institution', '2022-23');
INSERT INTO `criteria2` VALUES ('7', '7.1.4(A)', 'Geo tagged photographs with caption of the facilities', '2022-23');
INSERT INTO `criteria2` VALUES ('7', '7.1.4(B)', 'Bills for the purchase of equipment', '2022-23');
INSERT INTO `criteria2` VALUES ('7', '7.1.5', 'Green campus initiatives include', '2022-23');
INSERT INTO `criteria2` VALUES ('7', '7.1.5(A)', 'Policy document on the green campus', '2022-23');
INSERT INTO `criteria2` VALUES ('7', '7.1.5(B)', 'Geo tagged photographs/Videos with caption of the facilities', '2022-23');
INSERT INTO `criteria2` VALUES ('7', '7.1.5(C)', 'Circulars for the implementation of the initiatives and any other supporting document', '2022-23');
INSERT INTO `criteria2` VALUES ('7', '7.1.6', 'Quality audits on environment and energy undertaken by the institution', '2022-23');
INSERT INTO `criteria2` VALUES ('7', '7.1.6(A)', 'Policy document on environment and energy usage', '2022-23');
INSERT INTO `criteria2` VALUES ('7', '7.1.6(B)', 'Certificate from the auditing agency', '2022-23');
INSERT INTO `criteria2` VALUES ('7', '7.1.6(C)', 'Certificates of the awards received from the recognized agency', '2022-23');
INSERT INTO `criteria2` VALUES ('7', '7.1.6(D)', 'Report on environmental promotional activities conducted beyond the campus', '2022-23');
INSERT INTO `criteria2` VALUES ('7', '7.1.6(F)', 'Green audit report', '2022-23');
INSERT INTO `criteria2` VALUES ('7', '7.1.7', 'The Institution has a Divyangjan-friendly and barrier-free environment', '2022-23');
INSERT INTO `criteria2` VALUES ('7', '7.1.7(A)', 'Policy document and information brochure', '2022-23');
INSERT INTO `criteria2` VALUES ('7', '7.1.7(B)', 'Geo tagged photos', '2022-23');
INSERT INTO `criteria2` VALUES ('7', '7.1.7(C)', 'Bills and invoice/purchase order/AMC', '2022-23');
INSERT INTO `criteria2` VALUES ('7', '7.1.7(D)', 'A rest room should include specific requirements of Divyangjan', '2022-23');
INSERT INTO `criteria2` VALUES ('7', '7.1.7(E)', 'Bills for the software procured', '2022-23');
INSERT INTO `criteria2` VALUES ('7', '7.1.8', 'Describe the Institutional efforts/initiatives in providing an inclusive environment i.e.  tolerance and harmony towards cultural, regional, linguistic, communal, socioeconomic and other diversities (within a maximum of 200 words)', '2022-23');
INSERT INTO `criteria2` VALUES ('7', '7.1.8(A)', 'List of the outbound programs', '2022-23');
INSERT INTO `criteria2` VALUES ('7', '7.1.8(B)', 'List of national level activities (Cultural, Sports, Academic, Cultural & Sports)', '2022-23');
INSERT INTO `criteria2` VALUES ('7', '7.1.8(C)', 'List of the faculty & students coming from the out of state', '2022-23');
INSERT INTO `criteria2` VALUES ('7', '7.1.9', 'Sensitization of students and employees of the institution to constitutional obligations:  values, rights, duties and responsibilities of citizens', '2022-23');
INSERT INTO `criteria2` VALUES ('7', '7.1.9(A)', 'List of the activities/Events conducted, and the number of students present. (Awareness programs on Women safety & protection, Anti ragging, Judicial rights, Gender equality, Traffic rules, Environment protection, Conservation of natural resources (power & water)) >', '2022-23');
INSERT INTO `criteria2` VALUES ('7', '7.2.1', 'Provide the weblink on the Institutional website regarding the Best practices as per  the prescribed format of NAAC', '2022-23');
INSERT INTO `criteria2` VALUES ('7', '7.3.1', 'Institutional Distinctiveness', '2022-23');

DROP TABLE IF EXISTS `dc_up_files`;
CREATE TABLE `dc_up_files` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `Username` varchar(100) NOT NULL,
  `file_name` varchar(455) NOT NULL,
  `acd_year` varchar(50) DEFAULT NULL,
  `Main_file_type` varchar(100) NOT NULL,
  `file_type` varchar(200) DEFAULT NULL,
  `file_path` varchar(1000) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `dept`;
CREATE TABLE `dept` (
  `dept_id` int(11) NOT NULL AUTO_INCREMENT,
  `dept_name` varchar(50) NOT NULL,
  PRIMARY KEY (`dept_id`),
  UNIQUE KEY `dept_name` (`dept_name`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `dept` VALUES ('2', 'AI_DS');
INSERT INTO `dept` VALUES ('3', 'AI_ML');
INSERT INTO `dept` VALUES ('19', 'Anti_Ragging');
INSERT INTO `dept` VALUES ('9', 'BSH');
INSERT INTO `dept` VALUES ('24', 'Chemistry');
INSERT INTO `dept` VALUES ('8', 'CIVIL');
INSERT INTO `dept` VALUES ('14', 'Clubs');
INSERT INTO `dept` VALUES ('1', 'CSE');
INSERT INTO `dept` VALUES ('21', 'CSE-CS');
INSERT INTO `dept` VALUES ('5', 'ECE');
INSERT INTO `dept` VALUES ('6', 'EEE');
INSERT INTO `dept` VALUES ('16', 'IIC');
INSERT INTO `dept` VALUES ('4', 'IT');
INSERT INTO `dept` VALUES ('22', 'MatheMatics');
INSERT INTO `dept` VALUES ('7', 'MECH');
INSERT INTO `dept` VALUES ('10', 'NAAC');
INSERT INTO `dept` VALUES ('11', 'NBA');
INSERT INTO `dept` VALUES ('12', 'NCC');
INSERT INTO `dept` VALUES ('15', 'NSS');
INSERT INTO `dept` VALUES ('18', 'PASH');
INSERT INTO `dept` VALUES ('23', 'Physics');
INSERT INTO `dept` VALUES ('20', 'SAC');
INSERT INTO `dept` VALUES ('13', 'Sports');
INSERT INTO `dept` VALUES ('17', 'Women_Empowerment');

DROP TABLE IF EXISTS `dept_files`;
CREATE TABLE `dept_files` (
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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `dept_files` VALUES ('1', 'cseuser', 'CSE', '2024-25', 'admin', 'Result Analysis', 'result analysis', 'uploads/cse - admin file.pdf', NULL, NULL, NULL, NULL, 'Pending HOD', NULL, '2026-08-12 07:49:47');
INSERT INTO `dept_files` VALUES ('2', 'cseuser', 'CSE', '2024-25', 'faculty', 'Publication of Technical Magazines/Newsletters', 'studentrelfile', 'uploads/cse - student related.pdf', NULL, NULL, NULL, NULL, 'Pending HOD', NULL, '2026-08-12 07:51:00');
INSERT INTO `dept_files` VALUES ('3', 'cseuser', 'CSE', '2024-25', 'student', 'Student Addresses', 'studentrelfile', 'uploads/cse - student related.pdf', NULL, NULL, NULL, NULL, 'Pending HOD', NULL, '2026-08-12 07:51:41');
INSERT INTO `dept_files` VALUES ('4', 'cseuser', 'CSE', '2024-25', 'exam', 'Notice for Internal Lab Exams', 'examsec', 'uploads/cse - exam section.pdf', NULL, NULL, NULL, NULL, 'Pending HOD', NULL, '2026-08-12 07:52:22');
INSERT INTO `dept_files` VALUES ('5', 'cseuser', 'CSE', '2023-24', 'admin', 'Industrial Visits', 'kgjm,hbv', 'uploads/.EXP1.html', NULL, NULL, NULL, NULL, 'Rejected', NULL, '2026-08-12 10:31:41');

DROP TABLE IF EXISTS `document_actions`;
CREATE TABLE `document_actions` (
  `action_id` int(11) NOT NULL AUTO_INCREMENT,
  `doc_id` int(11) NOT NULL,
  `acted_by` int(11) NOT NULL,
  `action` enum('uploaded','approved','rejected','resubmitted') NOT NULL,
  `step_id` int(11) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `acted_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`action_id`),
  KEY `acted_by` (`acted_by`),
  KEY `step_id` (`step_id`),
  KEY `idx_doc` (`doc_id`),
  CONSTRAINT `document_actions_ibfk_1` FOREIGN KEY (`doc_id`) REFERENCES `documents` (`doc_id`),
  CONSTRAINT `document_actions_ibfk_2` FOREIGN KEY (`acted_by`) REFERENCES `users` (`user_id`),
  CONSTRAINT `document_actions_ibfk_3` FOREIGN KEY (`step_id`) REFERENCES `workflow_steps` (`step_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `document_categories`;
CREATE TABLE `document_categories` (
  `category_id` int(11) NOT NULL AUTO_INCREMENT,
  `category_name` varchar(100) NOT NULL,
  PRIMARY KEY (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `document_categories` VALUES ('1', 'General');

DROP TABLE IF EXISTS `document_files`;
CREATE TABLE `document_files` (
  `file_id` int(11) NOT NULL AUTO_INCREMENT,
  `doc_id` int(11) NOT NULL,
  `file_label` varchar(100) NOT NULL,
  `original_name` varchar(255) NOT NULL,
  `stored_name` varchar(255) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `mime_type` varchar(100) DEFAULT NULL,
  `file_size` int(11) DEFAULT NULL,
  `uploaded_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`file_id`),
  KEY `idx_doc` (`doc_id`),
  CONSTRAINT `document_files_ibfk_1` FOREIGN KEY (`doc_id`) REFERENCES `documents` (`doc_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `document_types`;
CREATE TABLE `document_types` (
  `type_id` int(11) NOT NULL AUTO_INCREMENT,
  `type_key` varchar(50) NOT NULL,
  `type_label` varchar(200) NOT NULL,
  `category` varchar(50) NOT NULL,
  `workflow_key` varchar(50) NOT NULL,
  `meta_table` varchar(100) DEFAULT NULL,
  `form_template` varchar(100) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`type_id`),
  UNIQUE KEY `type_key` (`type_key`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `document_types` VALUES ('1', 'journal', 'Published Journal Paper', 'research', 'department', 'meta_journal', 'form_journal', '1');
INSERT INTO `document_types` VALUES ('2', 'conference', 'Conference Paper', 'research', 'department', 'meta_conference', 'form_conference', '1');
INSERT INTO `document_types` VALUES ('3', 'patent', 'Patent', 'research', 'department', 'meta_patent', 'form_patent', '1');
INSERT INTO `document_types` VALUES ('4', 'fdp_attended', 'FDP/Workshop Attended', 'research', 'department', 'meta_fdp_attended', 'form_fdp_attended', '1');
INSERT INTO `document_types` VALUES ('5', 'fdp_organised', 'FDP/Workshop Organised', 'research', 'department', 'meta_fdp_organised', 'form_fdp_organised', '1');
INSERT INTO `document_types` VALUES ('6', 'conf_organised', 'Conference Organised', 'research', 'department', 'meta_conf_organised', 'form_conf_organised', '1');
INSERT INTO `document_types` VALUES ('7', 'criteria_file', 'NAAC Criteria Document', 'criteria', 'department', 'meta_criteria_file', 'form_criteria_file', '1');
INSERT INTO `document_types` VALUES ('8', 'dept_file', 'Department Document', 'department', 'department', 'meta_dept_file', 'form_dept_file', '1');
INSERT INTO `document_types` VALUES ('9', 'central_file', 'Central Event Document', 'central', 'central', 'meta_central_file', 'form_central_file', '1');
INSERT INTO `document_types` VALUES ('10', 'scholarship', 'Scholarship Data (5.1.1/5.1.2)', 'criteria', 'department', 'meta_scholarship', 'form_scholarship', '1');
INSERT INTO `document_types` VALUES ('11', 'placement', 'Placement Data (5.2.1)', 'student', 'department', 'meta_placement', 'form_placement', '1');
INSERT INTO `document_types` VALUES ('12', 'higher_ed', 'Higher Education (5.2.2)', 'student', 'department', 'meta_higher_ed', 'form_higher_ed', '1');
INSERT INTO `document_types` VALUES ('13', 'exam_qual', 'Exam Qualification (5.2.3)', 'student', 'department', 'meta_exam_qual', 'form_exam_qual', '1');
INSERT INTO `document_types` VALUES ('14', 'award', 'Award/Medal (5.3.1)', 'student', 'department', 'meta_award', 'form_award', '1');
INSERT INTO `document_types` VALUES ('15', 'student_event', 'Student Event', 'student', 'department', 'meta_student_event', 'form_student_event', '1');
INSERT INTO `document_types` VALUES ('16', 'student_body', 'Professional Body Activity', 'student', 'department', 'meta_student_body', 'form_student_body', '1');
INSERT INTO `document_types` VALUES ('17', 'student_journal', 'Student Journal Paper', 'student', 'department', 'meta_student_journal', 'form_student_journal', '1');
INSERT INTO `document_types` VALUES ('18', 'student_conference', 'Student Conference Paper', 'student', 'department', 'meta_student_conference', 'form_student_conference', '1');

DROP TABLE IF EXISTS `documents`;
CREATE TABLE `documents` (
  `doc_id` int(11) NOT NULL AUTO_INCREMENT,
  `doc_type_id` int(11) NOT NULL,
  `uploaded_by` int(11) NOT NULL,
  `dept_id` int(11) NOT NULL,
  `year_id` int(11) DEFAULT NULL,
  `title` varchar(500) NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'pending',
  `current_step` int(11) DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`doc_id`),
  KEY `current_step` (`current_step`),
  KEY `idx_status` (`status`),
  KEY `idx_dept_status` (`dept_id`,`status`),
  KEY `idx_uploader` (`uploaded_by`),
  KEY `idx_type_year` (`doc_type_id`,`year_id`),
  CONSTRAINT `documents_ibfk_1` FOREIGN KEY (`doc_type_id`) REFERENCES `document_types` (`type_id`),
  CONSTRAINT `documents_ibfk_2` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`user_id`),
  CONSTRAINT `documents_ibfk_3` FOREIGN KEY (`dept_id`) REFERENCES `dept` (`dept_id`),
  CONSTRAINT `documents_ibfk_4` FOREIGN KEY (`current_step`) REFERENCES `workflow_steps` (`step_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `fdps_org_tab`;
CREATE TABLE `fdps_org_tab` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `branch` varchar(100) NOT NULL,
  `title` varchar(255) NOT NULL,
  `mode` varchar(50) DEFAULT NULL,
  `date_from` date NOT NULL,
  `date_to` date NOT NULL,
  `organised_by` varchar(255) NOT NULL,
  `location` varchar(255) NOT NULL,
  `funded_by` varchar(50) DEFAULT NULL,
  `external_funder_name` varchar(200) DEFAULT NULL,
  `certificate` varchar(255) DEFAULT NULL,
  `brochure` varchar(255) NOT NULL,
  `fdp_schedule_invitation` varchar(255) NOT NULL,
  `attendance_forms` varchar(255) NOT NULL,
  `feedback_forms` varchar(255) NOT NULL,
  `fdp_report` varchar(255) NOT NULL,
  `photo1` varchar(255) NOT NULL,
  `photo2` varchar(255) NOT NULL,
  `photo3` varchar(255) NOT NULL,
  `merged_file` varchar(255) DEFAULT NULL,
  `submission_time` datetime NOT NULL,
  `year` varchar(255) NOT NULL,
  `status` varchar(50) DEFAULT 'Pending HOD',
  `rejection_reason` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `fdps_org_tab` VALUES ('1', 'cseuser', 'CSE', 'fdpsorg', 'offline', '2025-01-01', '2025-01-01', 'gmrit', 'rajam', 'Institute', '', NULL, 'uploads/certificates/cse - fdps 1 org Brouche.pdf', 'uploads/certificates/cse - fdps 1 org.pdf', 'uploads/certificates/cse - fdps 1 org.pdf', 'uploads/certificates/cse - fdps 1 org.pdf', 'uploads/certificates/cse - fdps 1.docx', 'uploads/certificates/profile.png', 'uploads/certificates/profile.png', 'uploads/certificates/profile.png', NULL, '2026-08-12 07:26:27', '2024-25', 'Accepted', NULL);

DROP TABLE IF EXISTS `fdps_tab`;
CREATE TABLE `fdps_tab` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `username` varchar(200) NOT NULL,
  `branch` varchar(100) NOT NULL,
  `title` varchar(200) NOT NULL,
  `mode` varchar(50) DEFAULT NULL,
  `date_from` date NOT NULL,
  `date_to` date NOT NULL,
  `organised_by` varchar(200) NOT NULL,
  `location` varchar(200) NOT NULL,
  `certificate` varchar(200) NOT NULL,
  `brochure` varchar(255) DEFAULT NULL,
  `fdp_schedule` varchar(255) DEFAULT NULL,
  `submission_time` varchar(300) NOT NULL,
  `year` varchar(255) NOT NULL,
  `status` varchar(50) DEFAULT 'Pending HOD',
  `rejection_reason` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `fdps_tab` VALUES ('1', 'cseuser', 'CSE', 'csefdps', 'Online', '2025-01-01', '2025-01-02', 'gmrit', 'rajam', 'uploads/certificates/cse - fdps 1 org certificate.pdf', 'uploads/brochures/cse - fdps 1 org Brouche.pdf', 'uploads/schedules/cse - fdps 1 org.pdf', '2026-08-12 07:01:11', '2024-25', 'Accepted', NULL);
INSERT INTO `fdps_tab` VALUES ('2', 'cseuser', 'CSE', 'fdps2', 'Online', '2025-01-01', '2025-01-03', 'gmrit', 'rajam', 'uploads/certificates/cse - fdps 1 org certificate.pdf', 'uploads/brochures/cse - fdps 1 org Brouche.pdf', 'uploads/schedules/cse - fdps 1 org.pdf', '2026-08-12 13:36:56', '2024-25', 'Accepted', NULL);

DROP TABLE IF EXISTS `files`;
CREATE TABLE `files` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `UserName` varchar(30) NOT NULL,
  `description` varchar(1500) NOT NULL,
  `academic_year` varchar(20) NOT NULL,
  `branch` varchar(10) NOT NULL,
  `sem` int(10) NOT NULL,
  `section` varchar(20) NOT NULL,
  `faculty_name` varchar(30) NOT NULL,
  `ext_or_int` varchar(20) NOT NULL,
  `uploaded_at` datetime(6) NOT NULL,
  `file_name` varchar(30) NOT NULL,
  `file_path` varchar(60) NOT NULL,
  `criteria` int(20) NOT NULL,
  `criteria_no` varchar(20) NOT NULL,
  `status` varchar(50) DEFAULT 'Pending HOD',
  `rejection_reason` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `files5_1_1and2`;
CREATE TABLE `files5_1_1and2` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `UserName` varchar(100) DEFAULT NULL,
  `academic_year` varchar(20) DEFAULT NULL,
  `faculty_name` varchar(100) DEFAULT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `file_name` varchar(255) DEFAULT NULL,
  `file_path` varchar(500) DEFAULT NULL,
  `scheme_name` varchar(255) DEFAULT NULL,
  `gov_students` int(11) DEFAULT NULL,
  `gov_amount` decimal(10,2) DEFAULT NULL,
  `inst_students` int(11) DEFAULT NULL,
  `inst_amount` decimal(10,2) DEFAULT NULL,
  `ngo_students` int(11) DEFAULT NULL,
  `ngo_amount` decimal(10,2) DEFAULT NULL,
  `ngo_name` varchar(255) DEFAULT NULL,
  `criteria` varchar(255) DEFAULT NULL,
  `criteria_no` varchar(255) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'Pending HOD',
  `rejection_reason` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `files5_1_3`;
CREATE TABLE `files5_1_3` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) DEFAULT NULL,
  `academic_year` varchar(20) DEFAULT NULL,
  `faculty_name` varchar(100) DEFAULT NULL,
  `programme_name` varchar(255) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `students_enrolled` int(11) DEFAULT NULL,
  `agency_details` varchar(500) DEFAULT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `file_name` varchar(255) DEFAULT NULL,
  `file_path` varchar(500) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'Pending HOD',
  `rejection_reason` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `files5_1_4`;
CREATE TABLE `files5_1_4` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) DEFAULT NULL,
  `faculty_name` varchar(100) DEFAULT NULL,
  `academic_year` varchar(20) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `activity_exam` varchar(255) DEFAULT NULL,
  `students_exam` int(11) DEFAULT NULL,
  `career_details` varchar(500) DEFAULT NULL,
  `students_career` int(11) DEFAULT NULL,
  `students_placed` int(11) DEFAULT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `file_name` varchar(255) DEFAULT NULL,
  `file_path` varchar(500) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'Pending HOD',
  `rejection_reason` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `files5_2_1`;
CREATE TABLE `files5_2_1` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) DEFAULT NULL,
  `faculty_name` varchar(100) DEFAULT NULL,
  `academic_year` varchar(20) DEFAULT NULL,
  `student_name` varchar(100) DEFAULT NULL,
  `programme` varchar(255) DEFAULT NULL,
  `employer` varchar(255) DEFAULT NULL,
  `pay` decimal(10,2) DEFAULT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `file_name` varchar(255) DEFAULT NULL,
  `file_path` varchar(500) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'Pending Dept Coordinator',
  `rejection_reason` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `files5_2_2`;
CREATE TABLE `files5_2_2` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) DEFAULT NULL,
  `faculty_name` varchar(100) DEFAULT NULL,
  `academic_year` varchar(20) DEFAULT NULL,
  `student_name` varchar(100) DEFAULT NULL,
  `programme` varchar(255) DEFAULT NULL,
  `institution` varchar(255) DEFAULT NULL,
  `admitted_programme` varchar(255) DEFAULT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `file_name` varchar(255) DEFAULT NULL,
  `file_path` varchar(500) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'Pending Dept Coordinator',
  `rejection_reason` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `files5_2_3`;
CREATE TABLE `files5_2_3` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) DEFAULT NULL,
  `faculty_name` varchar(100) DEFAULT NULL,
  `academic_year` varchar(20) DEFAULT NULL,
  `reg_no` varchar(50) DEFAULT NULL,
  `exam` varchar(255) DEFAULT NULL,
  `exam_status` varchar(100) DEFAULT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `file_name` varchar(255) DEFAULT NULL,
  `file_path` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `files5_3_1`;
CREATE TABLE `files5_3_1` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) DEFAULT NULL,
  `faculty_name` varchar(100) DEFAULT NULL,
  `academic_year` varchar(20) DEFAULT NULL,
  `award_name` varchar(255) DEFAULT NULL,
  `participation_type` varchar(100) DEFAULT NULL,
  `student_name` varchar(100) DEFAULT NULL,
  `competition_level` varchar(100) DEFAULT NULL,
  `event_name` varchar(255) DEFAULT NULL,
  `month_year` varchar(20) DEFAULT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `file_name` varchar(255) DEFAULT NULL,
  `file_path` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `files5_3_3`;
CREATE TABLE `files5_3_3` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) DEFAULT NULL,
  `faculty_name` varchar(100) DEFAULT NULL,
  `academic_year` varchar(20) DEFAULT NULL,
  `event_name` varchar(255) DEFAULT NULL,
  `event_date` date DEFAULT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `file_name` varchar(255) DEFAULT NULL,
  `file_path` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `login_pg`;
CREATE TABLE `login_pg` (
  `userid` varchar(30) NOT NULL,
  `password` varchar(20) NOT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=330 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `login_pg` VALUES ('gowtham.lite@gmail.com', '123', '237');
INSERT INTO `login_pg` VALUES ('gowtham.lite@gmail.com', '123', '238');
INSERT INTO `login_pg` VALUES ('gowtham.lite@gmail.com', '123', '239');
INSERT INTO `login_pg` VALUES ('gowtham.lite@gmail.com', '123', '240');
INSERT INTO `login_pg` VALUES ('gowtham.lite@gmail.com', '123', '241');
INSERT INTO `login_pg` VALUES ('gowtham.lite@gmail.com', '123', '242');
INSERT INTO `login_pg` VALUES ('gowtham.lite@gmail.com', '123', '243');
INSERT INTO `login_pg` VALUES ('gowtham.lite@gmail.com', '123', '244');
INSERT INTO `login_pg` VALUES ('gowtham.lite@gmail.com', '123', '245');
INSERT INTO `login_pg` VALUES ('gowtham.lite@gmail.com', '123', '246');
INSERT INTO `login_pg` VALUES ('gowtham.lite@gmail.com', '123', '247');
INSERT INTO `login_pg` VALUES ('gowtham.lite@gmail.com', '123', '248');
INSERT INTO `login_pg` VALUES ('gowtham.lite@gmail.com', '123', '249');
INSERT INTO `login_pg` VALUES ('gowtham.lite@gmail.com', '123', '250');
INSERT INTO `login_pg` VALUES ('gowtham.lite@gmail.com', '123', '251');
INSERT INTO `login_pg` VALUES ('gowtham.lite@gmail.com', '123', '252');
INSERT INTO `login_pg` VALUES ('gowtham.lite@gmail.com', '123', '253');
INSERT INTO `login_pg` VALUES ('23341A4535@gmrit.edu.in', '123', '254');
INSERT INTO `login_pg` VALUES ('gowtham.lite@gmail.com', '123', '255');
INSERT INTO `login_pg` VALUES ('gowtham.lite@gmail.com', '123', '256');
INSERT INTO `login_pg` VALUES ('gowtham.lite@gmail.com', '123', '257');
INSERT INTO `login_pg` VALUES ('gowtham.lite@gmail.com', '123', '258');
INSERT INTO `login_pg` VALUES ('gowtham.lite@gmail.com', '123', '259');
INSERT INTO `login_pg` VALUES ('gowtham.lite@gmail.com', '123', '260');
INSERT INTO `login_pg` VALUES ('gowtham.lite@gmail.com', '123', '261');
INSERT INTO `login_pg` VALUES ('gowtham.lite@gmail.com', '123', '262');
INSERT INTO `login_pg` VALUES ('gowtham.lite@gmail.com', '123', '263');
INSERT INTO `login_pg` VALUES ('gowtham.lite@gmail.com', '123', '264');
INSERT INTO `login_pg` VALUES ('gowtham.lite@gmail.com', '123', '265');
INSERT INTO `login_pg` VALUES ('gowtham.lite@gmail.com', '123', '266');
INSERT INTO `login_pg` VALUES ('gowtham.lite@gmail.com', '123', '267');
INSERT INTO `login_pg` VALUES ('gowtham.lite@gmail.com', '123', '268');
INSERT INTO `login_pg` VALUES ('gowtham.lite@gmail.com', '123', '269');
INSERT INTO `login_pg` VALUES ('gowtham.lite@gmail.com', '123', '270');
INSERT INTO `login_pg` VALUES ('gowtham.lite@gmail.com', '123', '271');
INSERT INTO `login_pg` VALUES ('gowtham.lite@gmail.com', '123', '272');
INSERT INTO `login_pg` VALUES ('gowtham.lite@gmail.com', '123', '273');
INSERT INTO `login_pg` VALUES ('gowtham.lite@gmail.com', '123', '274');
INSERT INTO `login_pg` VALUES ('gowtham.lite@gmail.com', '123', '275');
INSERT INTO `login_pg` VALUES ('gowtham.lite@gmail.com', '123', '276');
INSERT INTO `login_pg` VALUES ('gowtham.lite@gmail.com', '123', '277');
INSERT INTO `login_pg` VALUES ('gowtham.lite@gmail.com', '123', '278');
INSERT INTO `login_pg` VALUES ('cseuser1@gmail.com', '123', '279');
INSERT INTO `login_pg` VALUES ('aidsuser@gmail.com', '123', '280');
INSERT INTO `login_pg` VALUES ('gowtham.lite@gmail.com', '123', '281');
INSERT INTO `login_pg` VALUES ('gowtham.lite@gmail.com', '123', '282');
INSERT INTO `login_pg` VALUES ('gowtham.lite@gmail.com', '123', '283');
INSERT INTO `login_pg` VALUES ('gowtham.lite@gmail.com', '123', '284');
INSERT INTO `login_pg` VALUES ('cseuser1@gmail.com', '123', '285');
INSERT INTO `login_pg` VALUES ('gowtham.lite@gmail.com', '123', '286');
INSERT INTO `login_pg` VALUES ('gowtham.lite@gmail.com', '123', '287');
INSERT INTO `login_pg` VALUES ('gowtham.lite@gmail.com', '123', '288');
INSERT INTO `login_pg` VALUES ('gowtham.lite@gmail.com', '123', '289');
INSERT INTO `login_pg` VALUES ('gowtham.lite@gmail.com', '123', '290');
INSERT INTO `login_pg` VALUES ('cseuser1@gmail.com', '123', '291');
INSERT INTO `login_pg` VALUES ('gowtham.lite@gmail.com', '123', '292');
INSERT INTO `login_pg` VALUES ('gowtham.lite@gmail.com', '123', '293');
INSERT INTO `login_pg` VALUES ('gowtham.lite@gmail.com', '123', '294');
INSERT INTO `login_pg` VALUES ('gowtham.lite@gmail.com', '123', '295');
INSERT INTO `login_pg` VALUES ('gowtham.lite@gmail.com', '123', '296');
INSERT INTO `login_pg` VALUES ('gowtham.lite@gmail.com', '123', '297');
INSERT INTO `login_pg` VALUES ('gowtham.lite@gmail.com', '123', '298');
INSERT INTO `login_pg` VALUES ('gowtham.lite@gmail.com', '123', '299');
INSERT INTO `login_pg` VALUES ('gowtham.lite@gmail.com', '123', '300');
INSERT INTO `login_pg` VALUES ('cse-hod', '123', '301');
INSERT INTO `login_pg` VALUES ('cse@gmail.com', '123', '302');
INSERT INTO `login_pg` VALUES ('cse@gmail.com', '123', '303');
INSERT INTO `login_pg` VALUES ('cse-hod', '123', '304');
INSERT INTO `login_pg` VALUES ('cse@gmail.com', '123', '305');
INSERT INTO `login_pg` VALUES ('gowtham.lite@gmail.com', '123', '306');
INSERT INTO `login_pg` VALUES ('cse@gmail.com', '123', '307');
INSERT INTO `login_pg` VALUES ('cse@gmail.com', '123', '308');
INSERT INTO `login_pg` VALUES ('cse@gmail.com', '123', '309');
INSERT INTO `login_pg` VALUES ('cse@gmail.com', '123', '310');
INSERT INTO `login_pg` VALUES ('cse-hod', '123', '311');
INSERT INTO `login_pg` VALUES ('cse@gmail.com', '123', '312');
INSERT INTO `login_pg` VALUES ('cse@gmail.com', '123', '313');
INSERT INTO `login_pg` VALUES ('cse-hod', '123', '314');
INSERT INTO `login_pg` VALUES ('cse@gmail.com', '123', '315');
INSERT INTO `login_pg` VALUES ('cse@gmail.com', '123', '316');
INSERT INTO `login_pg` VALUES ('cse-hod', '123', '317');
INSERT INTO `login_pg` VALUES ('cse@gmail.com', '123', '318');
INSERT INTO `login_pg` VALUES ('cse-hod', '123', '319');
INSERT INTO `login_pg` VALUES ('cse@gmail.com', '123', '320');
INSERT INTO `login_pg` VALUES ('cse@gmail.com', '123', '321');
INSERT INTO `login_pg` VALUES ('cse@gmail.com', '123', '322');
INSERT INTO `login_pg` VALUES ('cse@gmail.com', '123', '323');
INSERT INTO `login_pg` VALUES ('cse@gmail.com', '123', '324');
INSERT INTO `login_pg` VALUES ('cse@gmail.com', '123', '325');
INSERT INTO `login_pg` VALUES ('cse-hod', '123', '326');
INSERT INTO `login_pg` VALUES ('cse@gmail.com', '123', '327');
INSERT INTO `login_pg` VALUES ('cse@gmail.com', '123', '328');
INSERT INTO `login_pg` VALUES ('cse@gmail.com', '123', '329');

DROP TABLE IF EXISTS `meta_award`;
CREATE TABLE `meta_award` (
  `doc_id` int(11) NOT NULL,
  `award_name` varchar(200) DEFAULT NULL,
  `participation_type` varchar(100) DEFAULT NULL,
  `student_name` varchar(200) DEFAULT NULL,
  `competition_level` varchar(100) DEFAULT NULL,
  `event_name` varchar(200) DEFAULT NULL,
  `month_year` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`doc_id`),
  CONSTRAINT `meta_award_ibfk_1` FOREIGN KEY (`doc_id`) REFERENCES `documents` (`doc_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `meta_central_file`;
CREATE TABLE `meta_central_file` (
  `doc_id` int(11) NOT NULL,
  `event` varchar(100) DEFAULT NULL,
  `club_name` varchar(200) DEFAULT NULL,
  `event_name` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`doc_id`),
  CONSTRAINT `meta_central_file_ibfk_1` FOREIGN KEY (`doc_id`) REFERENCES `documents` (`doc_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `meta_conf_organised`;
CREATE TABLE `meta_conf_organised` (
  `doc_id` int(11) NOT NULL,
  `mode` varchar(50) DEFAULT NULL,
  `date_from` date DEFAULT NULL,
  `date_to` date DEFAULT NULL,
  `organised_by` varchar(200) DEFAULT NULL,
  `location` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`doc_id`),
  CONSTRAINT `meta_conf_organised_ibfk_1` FOREIGN KEY (`doc_id`) REFERENCES `documents` (`doc_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `meta_conference`;
CREATE TABLE `meta_conference` (
  `doc_id` int(11) NOT NULL,
  `paper_title` varchar(300) NOT NULL,
  `conference_name` varchar(200) NOT NULL,
  `authors` text DEFAULT NULL,
  `paper_type` varchar(100) DEFAULT NULL,
  `volume_no` varchar(50) DEFAULT NULL,
  `issue_no` varchar(50) DEFAULT NULL,
  `page_no` varchar(50) DEFAULT NULL,
  `indexing` varchar(100) DEFAULT NULL,
  `publication_link` varchar(255) DEFAULT NULL,
  `issn_no` varchar(50) DEFAULT NULL,
  `doi` varchar(255) DEFAULT NULL,
  `from_date` date DEFAULT NULL,
  `to_date` date DEFAULT NULL,
  `organised_by` varchar(200) DEFAULT NULL,
  `location` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`doc_id`),
  CONSTRAINT `meta_conference_ibfk_1` FOREIGN KEY (`doc_id`) REFERENCES `documents` (`doc_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `meta_criteria_file`;
CREATE TABLE `meta_criteria_file` (
  `doc_id` int(11) NOT NULL,
  `criteria_no` varchar(30) NOT NULL,
  `description` varchar(1500) DEFAULT NULL,
  `semester` int(11) DEFAULT NULL,
  `section` varchar(20) DEFAULT NULL,
  `ext_or_int` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`doc_id`),
  CONSTRAINT `meta_criteria_file_ibfk_1` FOREIGN KEY (`doc_id`) REFERENCES `documents` (`doc_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `meta_dept_file`;
CREATE TABLE `meta_dept_file` (
  `doc_id` int(11) NOT NULL,
  `file_type` varchar(100) DEFAULT NULL,
  `sub_file_type` varchar(100) DEFAULT NULL,
  `semester` int(11) DEFAULT NULL,
  `review_period` varchar(50) DEFAULT NULL,
  `study_year` varchar(50) DEFAULT NULL,
  `meeting_no` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`doc_id`),
  CONSTRAINT `meta_dept_file_ibfk_1` FOREIGN KEY (`doc_id`) REFERENCES `documents` (`doc_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `meta_exam_qual`;
CREATE TABLE `meta_exam_qual` (
  `doc_id` int(11) NOT NULL,
  `reg_no` varchar(50) DEFAULT NULL,
  `exam` varchar(200) DEFAULT NULL,
  `exam_status` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`doc_id`),
  CONSTRAINT `meta_exam_qual_ibfk_1` FOREIGN KEY (`doc_id`) REFERENCES `documents` (`doc_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `meta_fdp_attended`;
CREATE TABLE `meta_fdp_attended` (
  `doc_id` int(11) NOT NULL,
  `mode` varchar(50) DEFAULT NULL,
  `date_from` date DEFAULT NULL,
  `date_to` date DEFAULT NULL,
  `organised_by` varchar(200) DEFAULT NULL,
  `location` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`doc_id`),
  CONSTRAINT `meta_fdp_attended_ibfk_1` FOREIGN KEY (`doc_id`) REFERENCES `documents` (`doc_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `meta_fdp_organised`;
CREATE TABLE `meta_fdp_organised` (
  `doc_id` int(11) NOT NULL,
  `date_from` date DEFAULT NULL,
  `date_to` date DEFAULT NULL,
  `organised_by` varchar(200) DEFAULT NULL,
  `location` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`doc_id`),
  CONSTRAINT `meta_fdp_organised_ibfk_1` FOREIGN KEY (`doc_id`) REFERENCES `documents` (`doc_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `meta_higher_ed`;
CREATE TABLE `meta_higher_ed` (
  `doc_id` int(11) NOT NULL,
  `student_name` varchar(200) DEFAULT NULL,
  `programme` varchar(100) DEFAULT NULL,
  `institution` varchar(200) DEFAULT NULL,
  `admitted_programme` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`doc_id`),
  CONSTRAINT `meta_higher_ed_ibfk_1` FOREIGN KEY (`doc_id`) REFERENCES `documents` (`doc_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `meta_journal`;
CREATE TABLE `meta_journal` (
  `doc_id` int(11) NOT NULL,
  `paper_title` varchar(300) NOT NULL,
  `journal_name` varchar(200) NOT NULL,
  `authors` text DEFAULT NULL,
  `issn_no` varchar(50) DEFAULT NULL,
  `volume_no` varchar(50) DEFAULT NULL,
  `issue_no` varchar(50) DEFAULT NULL,
  `page_no` varchar(50) DEFAULT NULL,
  `doi` varchar(255) DEFAULT NULL,
  `jcr_quartile` varchar(50) DEFAULT NULL,
  `scopus_quartile` varchar(50) DEFAULT NULL,
  `publication_link` varchar(255) DEFAULT NULL,
  `indexing` varchar(100) DEFAULT NULL,
  `date_of_publication` date DEFAULT NULL,
  `impact_factor` decimal(10,2) DEFAULT NULL,
  `quality_factor` decimal(10,2) DEFAULT NULL,
  `payment` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`doc_id`),
  CONSTRAINT `meta_journal_ibfk_1` FOREIGN KEY (`doc_id`) REFERENCES `documents` (`doc_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `meta_patent`;
CREATE TABLE `meta_patent` (
  `doc_id` int(11) NOT NULL,
  `patent_title` varchar(300) NOT NULL,
  `patent_no` varchar(255) DEFAULT NULL,
  `patent_type` varchar(100) DEFAULT NULL,
  `date_of_issue` date DEFAULT NULL,
  `inventors` text DEFAULT NULL,
  PRIMARY KEY (`doc_id`),
  CONSTRAINT `meta_patent_ibfk_1` FOREIGN KEY (`doc_id`) REFERENCES `documents` (`doc_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `meta_placement`;
CREATE TABLE `meta_placement` (
  `doc_id` int(11) NOT NULL,
  `student_name` varchar(200) DEFAULT NULL,
  `programme` varchar(100) DEFAULT NULL,
  `employer` varchar(200) DEFAULT NULL,
  `pay` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`doc_id`),
  CONSTRAINT `meta_placement_ibfk_1` FOREIGN KEY (`doc_id`) REFERENCES `documents` (`doc_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `meta_scholarship`;
CREATE TABLE `meta_scholarship` (
  `doc_id` int(11) NOT NULL,
  `scheme_name` varchar(200) DEFAULT NULL,
  `gov_students` int(11) DEFAULT NULL,
  `gov_amount` decimal(12,2) DEFAULT NULL,
  `inst_students` int(11) DEFAULT NULL,
  `inst_amount` decimal(12,2) DEFAULT NULL,
  `ngo_students` int(11) DEFAULT NULL,
  `ngo_amount` decimal(12,2) DEFAULT NULL,
  `ngo_name` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`doc_id`),
  CONSTRAINT `meta_scholarship_ibfk_1` FOREIGN KEY (`doc_id`) REFERENCES `documents` (`doc_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `meta_student_body`;
CREATE TABLE `meta_student_body` (
  `doc_id` int(11) NOT NULL,
  `body_name` varchar(200) DEFAULT NULL,
  `event_name` varchar(200) DEFAULT NULL,
  `from_date` date DEFAULT NULL,
  `to_date` date DEFAULT NULL,
  `organised_by` varchar(200) DEFAULT NULL,
  `location` varchar(200) DEFAULT NULL,
  `participation_status` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`doc_id`),
  CONSTRAINT `meta_student_body_ibfk_1` FOREIGN KEY (`doc_id`) REFERENCES `documents` (`doc_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `meta_student_conference`;
CREATE TABLE `meta_student_conference` (
  `doc_id` int(11) NOT NULL,
  `paper_title` varchar(300) DEFAULT NULL,
  `paper_type` varchar(100) DEFAULT NULL,
  `from_date` date DEFAULT NULL,
  `to_date` date DEFAULT NULL,
  `organised_by` varchar(200) DEFAULT NULL,
  `location` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`doc_id`),
  CONSTRAINT `meta_student_conference_ibfk_1` FOREIGN KEY (`doc_id`) REFERENCES `documents` (`doc_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `meta_student_event`;
CREATE TABLE `meta_student_event` (
  `doc_id` int(11) NOT NULL,
  `activity` varchar(200) DEFAULT NULL,
  `event_name` varchar(200) DEFAULT NULL,
  `from_date` date DEFAULT NULL,
  `to_date` date DEFAULT NULL,
  `organised_by` varchar(200) DEFAULT NULL,
  `location` varchar(200) DEFAULT NULL,
  `participation_status` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`doc_id`),
  CONSTRAINT `meta_student_event_ibfk_1` FOREIGN KEY (`doc_id`) REFERENCES `documents` (`doc_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `meta_student_journal`;
CREATE TABLE `meta_student_journal` (
  `doc_id` int(11) NOT NULL,
  `paper_title` varchar(300) DEFAULT NULL,
  `journal_name` varchar(200) DEFAULT NULL,
  `indexing` varchar(100) DEFAULT NULL,
  `date_of_submission` date DEFAULT NULL,
  `impact_factor` decimal(10,2) DEFAULT NULL,
  `quality_factor` decimal(10,2) DEFAULT NULL,
  `payment` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`doc_id`),
  CONSTRAINT `meta_student_journal_ibfk_1` FOREIGN KEY (`doc_id`) REFERENCES `documents` (`doc_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `nba_criteria`;
CREATE TABLE `nba_criteria` (
  `SI_no` int(10) NOT NULL,
  `Sub_no` varchar(30) NOT NULL,
  `Des` varchar(600) NOT NULL,
  `year` varchar(200) NOT NULL,
  PRIMARY KEY (`Sub_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `nba_criteria` VALUES ('1', '1.1.1', 'Copies of PEOs, POs & PSOs for all programs', '2022-23');
INSERT INTO `nba_criteria` VALUES ('1', '1.1.2', 'Number of Programmes where syllabus revision was carried out during the year(Data Requirement: Programme Code ,Names of the Programmes revised)', '2022-23');
INSERT INTO `nba_criteria` VALUES ('1', '1.1.2(A)', 'List of programs where syllabus revision has been carried out signed by the Principal', '2022-23');
INSERT INTO `nba_criteria` VALUES ('1', '1.1.2(B)', 'Approved Minutes of relevant Academic Council/BOS meetings highlighting the specific agenda item relevant to the metric year wise', '2022-23');
INSERT INTO `nba_criteria` VALUES ('1', '1.1.3', 'Number of courses focusing on employability/entrepreneurship/ skill development  offered by the Institution during the year(Data Requirement: Name of the Course with Course Code ,Name of the Programme ,Activities which have a direct bearing on employability/ entrepreneurship/ skill  development)', '2022-23');
INSERT INTO `nba_criteria` VALUES ('1', '1.1.3(A)', 'Syllabus copy of the courses highlighting Focus on employability/entrepreneurship/ skill development along with their course outcomes.', '2022-23');
INSERT INTO `nba_criteria` VALUES ('1', '1.1.3(B)', 'Minutes of the Boards of Studies/ Academic Council meetings with approval for these courses.', '2022-23');
INSERT INTO `nba_criteria` VALUES ('1', '1.1.3(C)', 'List of MoUs', '2022-23');
INSERT INTO `nba_criteria` VALUES ('1', '1.2.1', 'Number of new courses introduced across all programmes offered during the year(Data Requirement: Name of the newly introduced course (s)  ,Name of the Programme)', '2022-23');
INSERT INTO `nba_criteria` VALUES ('1', '1.2.1(A)', 'List of new courses introduced program-wise during the assessment period certified by the Principal.', '2022-23');
INSERT INTO `nba_criteria` VALUES ('1', '1.2.1(B)', 'Minutes of relevant Academic Council/BOS meetings highlighting the name of the new courses introduced', '2022-23');
INSERT INTO `nba_criteria` VALUES ('1', '1.2.2', 'Number of Programmes offered through Choice Based Credit System  (CBCS)/Elective Course System(Data Requirement: ,Names of all Programmes offered through CBCS ,Names of all Programmes offered through Elective Course System)', '2022-23');
INSERT INTO `nba_criteria` VALUES ('1', '1.2.2(A)', 'List of programs in which CBCS/Elective course system implemented in the last completed academic year certified by the principal', '2022-23');
INSERT INTO `nba_criteria` VALUES ('1', '1.2.2(B)', 'Structure of the program clearly indicating courses, credits/Electives and Minutes of relevant Academic Council/BOS meetings highlighting the relevant documents to this metric', '2022-23');
INSERT INTO `nba_criteria` VALUES ('1', '1.3.1', 'List and Description of courses relevant to Professional Ethics, Gender diversity and equality, Human Values, Environment and Sustainability, Women Empowerment introduced in the Curriculum along with the syllabus should be available in all the departments', '2022-23');
INSERT INTO `nba_criteria` VALUES ('1', '1.3.2', 'Number of value-added courses for imparting transferable and life skills offered  during the year:(Data Requirement:  Names of the value-added courses (each with 30 or more contact hours) ,No. of times offered (for each value-added course) during the year ,Total number of students enrolled ,Total number of students completing the course during the year', '2022-23');
INSERT INTO `nba_criteria` VALUES ('1', '1.3.2(A)', 'List of value-added courses which are optional and offered outside the curriculum of the programs with authorized sign.', '2022-23');
INSERT INTO `nba_criteria` VALUES ('1', '1.3.2(B)', 'Brochure and Course content or syllabus along with course outcome of Value-added courses offered.', '2022-23');
INSERT INTO `nba_criteria` VALUES ('1', '1.3.3', 'List of enrolled students for courses addressed in 1.3.2', '2022-23');
INSERT INTO `nba_criteria` VALUES ('1', '1.3.4', 'List of students undertaking the field projects/ internships / student projects program-wise in the last completed academic year along with the details of title, place of work etc.', '2022-23');
INSERT INTO `nba_criteria` VALUES ('1', '1.4.1', 'Sample Filled in feedback forms from the stakeholders to be provided.', '2022-23');
INSERT INTO `nba_criteria` VALUES ('1', '1.4.2', 'Stakeholder feedback analysis report signed by the authority', '2022-23');
INSERT INTO `nba_criteria` VALUES ('2', '2.1.1', 'Enrolment of Students ', '2022-23');
INSERT INTO `nba_criteria` VALUES ('2', '2.1.1(A)', 'Students admitted(HEI signed documents)', '2022-23');
INSERT INTO `nba_criteria` VALUES ('2', '2.1.1(B)', 'Enrolment Number_ AICTE letters of sanctioned intake', '2022-23');
INSERT INTO `nba_criteria` VALUES ('2', '2.1.1(C)', 'Enrolment Number _ Ratified list of admitted students', '2022-23');
INSERT INTO `nba_criteria` VALUES ('2', '2.1.2', 'Number of seats filled against reserved categories (SC, ST, OBC, Divyangjan,  etc.) as per the reservation policy during the year (exclusive of supernumerary  seats)', '2022-23');
INSERT INTO `nba_criteria` VALUES ('2', '2.1.2(A)', 'Reservation seats categories to be considered as per the state rule (APSHE Guidelines)', '2022-23');
INSERT INTO `nba_criteria` VALUES ('2', '2.1.2(B)', 'AP regulation for Admissions GO No. 73', '2022-23');
INSERT INTO `nba_criteria` VALUES ('2', '2.1.2(C)', 'Guidelines for filling the left-over vacancies under reservation', '2022-23');
INSERT INTO `nba_criteria` VALUES ('2', '2.1.2(D)', 'Category wise_ Ratified list of students admitted', '2022-23');
INSERT INTO `nba_criteria` VALUES ('2', '2.1.2(E)', 'Admission Abstract(HEI signed documents)', '2022-23');
INSERT INTO `nba_criteria` VALUES ('2', '2.2.1', 'The institution assesses students’ learning levels and organises special  programmes for both slow and advanced learners(Present a write-up within a maximum of 200 words)', '2022-23');
INSERT INTO `nba_criteria` VALUES ('2', '2.2.1(A)', 'Video records of the specific topics(A document having the links and screen shots of video course/LCS)', '2022-23');
INSERT INTO `nba_criteria` VALUES ('2', '2.2.1(B)', 'Classes for the slow learner(Time table conducted for CA failures and attendance sheets)', '2022-23');
INSERT INTO `nba_criteria` VALUES ('2', '2.2.1(C)', 'Additional assignment(sample copies of either question papers or evaluated assignment)', '2022-23');
INSERT INTO `nba_criteria` VALUES ('2', '2.2.1(D)', 'Make-up classes for course detention students/lateral entry students(Time tables and attendance sheets for each semester(1-8) at least one)', '2022-23');
INSERT INTO `nba_criteria` VALUES ('2', '2.2.1(E)', 'Remedial classes conducted for end semester failures(Time tables, attendance sheets and track sheets for each semester(1-8) at least one)', '2022-23');
INSERT INTO `nba_criteria` VALUES ('2', '2.2.1(F)', 'List of MOOCs courses(Details of student with course name and sample certificates)', '2022-23');
INSERT INTO `nba_criteria` VALUES ('2', '2.2.1(G)', 'List of the advanced learners opted Minors and honors(Abstract certified by controller of examination)', '2022-23');
INSERT INTO `nba_criteria` VALUES ('2', '2.2.1(H)', 'Student participation in technical events list with students event details and SAMPLE certificates', '2022-23');
INSERT INTO `nba_criteria` VALUES ('2', '2.2.2', 'Student – Teacher (full-time) ratio(Data Requirement:  Total number of students in the institution ,Total number of full-time teachers in the institution)', '2022-23');
INSERT INTO `nba_criteria` VALUES ('2', '2.2.2(A)', 'List of the full-time teachers', '2022-23');
INSERT INTO `nba_criteria` VALUES ('2', '2.2.2(B)', 'Student intake', '2022-23');
INSERT INTO `nba_criteria` VALUES ('2', '2.3.1', 'Student-centric methods such as experiential learning, participative learning and  problem-solving methodologies are used for enhancing learning experiences(Present a write-up within a maximum of 200 words.)', '2022-23');
INSERT INTO `nba_criteria` VALUES ('2', '2.3.1(A)', 'Academic regulations and curriculum(Recent)', '2022-23');
INSERT INTO `nba_criteria` VALUES ('2', '2.3.2', 'Teachers use ICT-enabled tools including online resources for effective teaching  and learning(Present a write-up within a maximum of 200 words.)', '2022-23');
INSERT INTO `nba_criteria` VALUES ('2', '2.3.2 (A)', 'ICT Tools Gadgets viz. Graphic tablets, MST, Smart pen, Projector(Geotagged photos with title of photo)', '2022-23');
INSERT INTO `nba_criteria` VALUES ('2', '2.3.3', 'Ratio of students to mentor for academic and other related issues', '2022-23');
INSERT INTO `nba_criteria` VALUES ('2', '2.3.3(A)', 'Circular of mentor – mentees', '2022-23');
INSERT INTO `nba_criteria` VALUES ('2', '2.3.3(B)', 'Mentor list as announced by the HEI', '2022-23');
INSERT INTO `nba_criteria` VALUES ('2', '2.3.3(C)', 'issues raised and resolved in the mentor system(Total records of a student -&gt; one sample from each semester)', '2022-23');
INSERT INTO `nba_criteria` VALUES ('2', '2.3.4', 'Preparation and adherence to Academic Calendar and Teaching Plans by the  institution(Describe the preparation of and adherence to the Academic Calendar and Teaching  Plans by the institution.  Present a write-up within a maximum of 200 words)', '2022-23');
INSERT INTO `nba_criteria` VALUES ('2', '2.3.4(A)', 'Academic calendar', '2022-23');
INSERT INTO `nba_criteria` VALUES ('2', '2.3.4(B)', 'Proof of course allotment for odd and even semesters', '2022-23');
INSERT INTO `nba_criteria` VALUES ('2', '2.3.4(C)', 'Proof for lecture plan, Schedule and diary(one course from each semester)', '2022-23');
INSERT INTO `nba_criteria` VALUES ('2', '2.3.4(D)', 'Minutes of AMC', '2022-23');
INSERT INTO `nba_criteria` VALUES ('2', '2.4.1', 'Number of full-time teachers against sanctioned posts during the year(Data Requirement:  Number of full-time teachers ,Number of sanctioned posts)', '2022-23');
INSERT INTO `nba_criteria` VALUES ('2', '2.4.1(A)', 'Sanction letter indicating number of posts', '2022-23');
INSERT INTO `nba_criteria` VALUES ('2', '2.4.1(B)', 'Department wise List of full-time teachers appointed', '2022-23');
INSERT INTO `nba_criteria` VALUES ('2', '2.4.2', 'Number of full-time teachers with PhD/ D.M. / M.Ch. / D.N.B Super-Specialty /  DSc / DLitt during the year(Data Requirement: List of full-time teachers with PhD/ D.M. / M.Ch. / D.N.B Super-Specialty /  DSc / DLitt.)', '2022-23');
INSERT INTO `nba_criteria` VALUES ('2', '2.4.3', 'Total teaching experience of full-time teachers in the same institution(Data Requirement:  Name and number of full-time teachers and their years of teaching experience in the institution)', '2022-23');
INSERT INTO `nba_criteria` VALUES ('2', '2.5.1', 'Number of days from the date of last semester-end/ year- end examination till the  declaration of results during the year(Number of days from the date of last semester-end / year-end examination till the  declaration of results year-wise during the year)', '2022-23');
INSERT INTO `nba_criteria` VALUES ('2', '2.5.1(A)', 'Examination Result Notifications', '2022-23');
INSERT INTO `nba_criteria` VALUES ('2', '2.5.1(B)', 'List of Programs offered and the last date of the latest semester end exams and date of result declaration', '2022-23');
INSERT INTO `nba_criteria` VALUES ('2', '2.5.2', 'Number of students’ complaints/grievances against evaluation against the total  number who appeared in the examinations during the year', '2022-23');
INSERT INTO `nba_criteria` VALUES ('2', '2.5.2(A)', 'Number of complaints', '2022-23');
INSERT INTO `nba_criteria` VALUES ('2', '2.5.2(B)', 'Minutes of the examination Committee', '2022-23');
INSERT INTO `nba_criteria` VALUES ('2', '2.5.3', 'IT integration and reforms in the examination procedures and processes  including Continuous Internal Assessment (CIA) have brought in considerable  improvement in the Examination Management System (EMS) of the Institution(Describe the examination reforms with reference to the following within a minimum  of 200 words :Examination procedures ,Processes/Procedures integrating IT ,Continuous Internal Assessment System) ', '2022-23');
INSERT INTO `nba_criteria` VALUES ('2', '2.5.3(A)', 'Examination Regulations', '2022-23');
INSERT INTO `nba_criteria` VALUES ('2', '2.5.3(B)', 'Question Paper templates', '2022-23');
INSERT INTO `nba_criteria` VALUES ('2', '2.5.3(C)', 'Proof of the hybrid grading sheet', '2022-23');
INSERT INTO `nba_criteria` VALUES ('2', '2.5.3(D)', 'Examination Results link in the website', '2022-23');
INSERT INTO `nba_criteria` VALUES ('2', '2.5.3(E)', 'Proof of the OMR', '2022-23');
INSERT INTO `nba_criteria` VALUES ('2', '2.6.1', 'Programme Outcomes and Course Outcomes for all Programmes offered by the  institution are stated and displayed on the website and communicated to teachers  and students(Describe Course Outcomes (COs) for all courses and the mechanism of  communication to teachers and students within a maximum of 200 words.  Upload COs for all Courses) ', '2022-23');
INSERT INTO `nba_criteria` VALUES ('2', '2.6.1(A)', 'List of POs and COs(COs for at least two courses from each semester)', '2022-23');
INSERT INTO `nba_criteria` VALUES ('2', '2.6.1(B)', 'Photo gallery of the COs & POs displayed Program wise', '2022-23');
INSERT INTO `nba_criteria` VALUES ('2', '2.6.2', 'Attainment of Programme Outcomes and Course Outcomes as evaluated by the  institution(Describe the method of measuring the attainment of POs, PSOs and COs and the  level of attaiment of POs , PSOs and COs in not more than 200 words.)', '2022-23');
INSERT INTO `nba_criteria` VALUES ('2', '2.6.2(A)', 'CO and PO attainment', '2022-23');
INSERT INTO `nba_criteria` VALUES ('2', '2.6.2(B)', 'Files related to all surveys for the indirect assessment', '2022-23');
INSERT INTO `nba_criteria` VALUES ('2', '2.6.3', 'Pass Percentage of students', '2022-23');
INSERT INTO `nba_criteria` VALUES ('2', '2.6.3(A)', 'Annual report of CoE indicating the pass percentage', '2022-23');
INSERT INTO `nba_criteria` VALUES ('2', '2.6.3(B)', 'Certified report from CoE indicating Students eligible for the degree program', '2022-23');
INSERT INTO `nba_criteria` VALUES ('2', '2.7.1', 'Student Satisfaction Survey (SSS) on overall institutional performance  (Institution may design its own questionnaire). Results and details need to be  provided as a weblink', '2022-23');
INSERT INTO `nba_criteria` VALUES ('3', '3.1.1', 'The institution’s research facilities are frequently updated and there is a welldefined policy for promotion of research which is uploaded on the institutional  website and implemented(Present a write-up within a maximum of 200 words.)', '2022-23');
INSERT INTO `nba_criteria` VALUES ('3', '3.1.1(A)', 'List of research equipment along with the proof of purchases (Bill copies)', '2022-23');
INSERT INTO `nba_criteria` VALUES ('3', '3.1.1(B)', 'Policy for Faculty Assessment and Development Scheme', '2022-23');
INSERT INTO `nba_criteria` VALUES ('3', '3.1.1(C)', 'Financial Incentives for attending conferences, seminars and QIP', '2022-23');
INSERT INTO `nba_criteria` VALUES ('3', '3.1.1(D)', 'Sanction letters for the funded research projects & UCs for the projects completed', '2022-23');
INSERT INTO `nba_criteria` VALUES ('3', '3.1.1(E)', 'Copies of all MoUs for collaborative research', '2022-23');
INSERT INTO `nba_criteria` VALUES ('3', '3.1.1(F)', 'Minutes of the governing council meeting-reflecting the research promotions', '2022-23');
INSERT INTO `nba_criteria` VALUES ('3', '3.1.2', 'Details of Seed money', '2022-23');
INSERT INTO `nba_criteria` VALUES ('3', '3.1.3', 'Number of teachers who were awarded national / international fellowship(s) for  advanced studies/research during the year', '2022-23');
INSERT INTO `nba_criteria` VALUES ('3', '3.2.1', 'Grants received from Government and Non-Governmental agencies for research  projects, endowments, Chairs during the year (INR in Lakhs)', '2022-23');
INSERT INTO `nba_criteria` VALUES ('3', '3.2.1(A)', 'List of Grants received for research projects', '2022-23');
INSERT INTO `nba_criteria` VALUES ('3', '3.2.1(B)', 'e-copies of grants sanctioned', '2022-23');
INSERT INTO `nba_criteria` VALUES ('3', '3.2.2', 'List of teachers having research projects during the year', '2022-23');
INSERT INTO `nba_criteria` VALUES ('3', '3.2.3', 'Number of teachers recognized as research guides', '2022-23');
INSERT INTO `nba_criteria` VALUES ('3', '3.2.4', 'Number of departments having research projects', '2022-23');
INSERT INTO `nba_criteria` VALUES ('3', '3.2.4(A)', 'Web Links to Funding Agencies', '2022-23');
INSERT INTO `nba_criteria` VALUES ('3', '3.3.1', 'Institution has created an ecosystem for innovations and creation and transfer of  knowledge supported by dedicated centres for research, entrepreneurship,  community orientation, incubation, etc.(Present a write-up within a maximum of 200 words)', '2022-23');
INSERT INTO `nba_criteria` VALUES ('3', '3.3.1(A)', 'MSME business incubation center – sanction letter', '2022-23');
INSERT INTO `nba_criteria` VALUES ('3', '3.3.1(B)', 'AICTE sponsored EDC – Sanction letters and list of the activities conducted by EDC', '2022-23');
INSERT INTO `nba_criteria` VALUES ('3', '3.3.1(C)', 'Dedicated centres for research', '2022-23');
INSERT INTO `nba_criteria` VALUES ('3', '3.3.2', 'Detailed report including photos, resource persons etc.', '2022-23');
INSERT INTO `nba_criteria` VALUES ('3', '3.4.1', 'The Institution ensures implementation of its Code of Ethics for Research  uploaded in the website through the following:  1. Research Advisory Committee 2. Ethics Committee 3. Inclusion of Research Ethics in the research methodology course work  4. Plagiarism check through authenticated software Options: A. All of the above B. Any 3 of the above C. Any 2 of the above D. Any 1 of the above E. None of the above', '2022-23');
INSERT INTO `nba_criteria` VALUES ('3', '3.4.1(A)', 'Research Advisory Committee', '2022-23');
INSERT INTO `nba_criteria` VALUES ('3', '3.4.1(B)', 'Ethics Committee', '2022-23');
INSERT INTO `nba_criteria` VALUES ('3', '3.4.1(C)', 'Inclusion of Research Ethics in the research methodology course work', '2022-23');
INSERT INTO `nba_criteria` VALUES ('3', '3.4.1(D)', 'Anti Plagiarism software approved by JNTU', '2022-23');
INSERT INTO `nba_criteria` VALUES ('3', '3.4.2', 'Number of PhD candidates registered per teacher (as per the data given with  regard to recognized PhD guides/ supervisors provided in Metric No. 3.2.3) during  the year', '2022-23');
INSERT INTO `nba_criteria` VALUES ('3', '3.4.2(A)', 'List of Faculty along with the names of Research scholars', '2022-23');
INSERT INTO `nba_criteria` VALUES ('3', '3.4.2(B)', 'Copy of the Registration letters/Joining letters', '2022-23');
INSERT INTO `nba_criteria` VALUES ('3', '3.4.3', 'Number of research papers per teacher in CARE Journals notified on UGC  website during the year', '2022-23');
INSERT INTO `nba_criteria` VALUES ('3', '3.4.4', 'Number of books and chapters in edited volumes / books published per teacher  during the year', '2022-23');
INSERT INTO `nba_criteria` VALUES ('3', '3.4.5', 'Bibliometrics of the publications based on average Citation Index', '2022-23');
INSERT INTO `nba_criteria` VALUES ('3', '3.4.6', 'Bibliometrics of the publication-based h-Index of the University', '2022-23');
INSERT INTO `nba_criteria` VALUES ('3', '3.5.1', 'Audited statements for Revenue generated from consultancy', '2022-23');
INSERT INTO `nba_criteria` VALUES ('3', '3.5.2', 'Total amount spent on developing facilities, training teachers and clerical/project  staff for undertaking consultancy during the year', '2022-23');
INSERT INTO `nba_criteria` VALUES ('3', '3.6.1', 'Extension activities carried out in the neighbourhood sensitising students to social  issues for their holistic development, and the impact thereof during the year', '2022-23');
INSERT INTO `nba_criteria` VALUES ('3', '3.6.1(A)', 'List of NSS activities conducted year wise and number of students participated', '2022-23');
INSERT INTO `nba_criteria` VALUES ('3', '3.6.1(B)', 'Covid-19 booster report', '2022-23');
INSERT INTO `nba_criteria` VALUES ('3', '3.6.1(C)', 'MGNCRE Reports', '2022-23');
INSERT INTO `nba_criteria` VALUES ('3', '3.6.1(D)', 'Community Radio report', '2022-23');
INSERT INTO `nba_criteria` VALUES ('3', '3.6.1(E)', 'List of NCC activities conducted year wise and number of students participated', '2022-23');
INSERT INTO `nba_criteria` VALUES ('3', '3.6.2', 'Number of awards and recognition received by the Institution, its teachers and  students for extension activities from Government / Government-recognised bodies', '2022-23');
INSERT INTO `nba_criteria` VALUES ('3', '3.6.3', 'Number of extension and outreach programmes conducted by the institution through  NSS/NCC during the year', '2022-23');
INSERT INTO `nba_criteria` VALUES ('3', '3.6.4', 'Number of students participating in extension activities listed in 3.6.3 during the  year', '2022-23');
INSERT INTO `nba_criteria` VALUES ('3', '3.7.1', 'Number of collaborative activities during the year for research/ faculty exchange/  student exchange/ internship/ on-the-job training/ project work', '2022-23');
INSERT INTO `nba_criteria` VALUES ('3', '3.7.2', 'Number of functional MoUs with institutions of national and/or international  importance, other universities, industries, corporate houses, etc. during the year  (only functional MoUs with ongoing activities to be considered)', '2022-23');
INSERT INTO `nba_criteria` VALUES ('3', '3.7.2(A)', 'e-copies of functional MoUs', '2022-23');
INSERT INTO `nba_criteria` VALUES ('3', '3.7.2(B)', 'e-copies of Activities of MOUs', '2022-23');
INSERT INTO `nba_criteria` VALUES ('4', '4.1.1', 'The Institution has adequate infrastructure and physical facilities for teaching learning, viz., classrooms, laboratories, computing equipments, etc.', '2022-23');
INSERT INTO `nba_criteria` VALUES ('4', '4.1.1(A)', 'Details of the Classrooms, Labs & Other facilities across all the six blocks', '2022-23');
INSERT INTO `nba_criteria` VALUES ('4', '4.1.1(B)', 'List of the laboratories with titles', '2022-23');
INSERT INTO `nba_criteria` VALUES ('4', '4.1.1(C)', 'Campus LAN diagram', '2022-23');
INSERT INTO `nba_criteria` VALUES ('4', '4.1.1(D)', 'Proof of bandwidth', '2022-23');
INSERT INTO `nba_criteria` VALUES ('4', '4.1.1(E)', 'List of the software', '2022-23');
INSERT INTO `nba_criteria` VALUES ('4', '4.1.1(F)', 'Photo gallery of all the academic blocks, classrooms, drawing hall, seminar hall, auditorium, and Labs', '2022-23');
INSERT INTO `nba_criteria` VALUES ('4', '4.1.2', 'The institution has adequate facilities for cultural activities, yoga, sports and games  (indoor and outdoor) including gymnasium, yoga centre, auditorium etc.)', '2022-23');
INSERT INTO `nba_criteria` VALUES ('4', '4.1.2(A)', 'Colleague of Geo-tagged pictures', '2022-23');
INSERT INTO `nba_criteria` VALUES ('4', '4.1.2(B)', 'Area details of the all the facilities', '2022-23');
INSERT INTO `nba_criteria` VALUES ('4', '4.1.2(C)', 'Photo gallery of the various activities', '2022-23');
INSERT INTO `nba_criteria` VALUES ('4', '4.1.3', 'Number of classrooms and seminar halls with ICT-enabled facilities', '2022-23');
INSERT INTO `nba_criteria` VALUES ('4', '4.1.3(A)', 'Geo-tagged photographs of classrooms with ICT enabled facilities', '2022-23');
INSERT INTO `nba_criteria` VALUES ('4', '4.1.3(B)', 'Class Timetables', '2022-23');
INSERT INTO `nba_criteria` VALUES ('4', '4.1.4', 'Expenditure for infrastructure augmentation, excluding salary, during the year (INR  in Lakhs)', '2022-23');
INSERT INTO `nba_criteria` VALUES ('4', '4.1.4(A)', 'Budget allocation', '2022-23');
INSERT INTO `nba_criteria` VALUES ('4', '4.1.4(B)', 'Provide the consolidated fund allocation towards infrastructure augmentation facilities duly certified by Head of the Institution', '2022-23');
INSERT INTO `nba_criteria` VALUES ('4', '4.2.1', 'Library is automated using Integrated Library Management System (ILMS)', '2022-23');
INSERT INTO `nba_criteria` VALUES ('4', '4.2.1(A)', 'Library operates through LIBSYS', '2022-23');
INSERT INTO `nba_criteria` VALUES ('4', '4.2.1(B)', 'All the books are provided with RF Id security tags', '2022-23');
INSERT INTO `nba_criteria` VALUES ('4', '4.2.1(C)', 'Copy of the latest License agreement of LIBSYS-7', '2022-23');
INSERT INTO `nba_criteria` VALUES ('4', '4.2.1(D)', 'Digital Library', '2022-23');
INSERT INTO `nba_criteria` VALUES ('4', '4.2.2', 'Institution has access to the following', '2022-23');
INSERT INTO `nba_criteria` VALUES ('4', '4.2.2 (D)', 'Specific details in respect of e-resources selected.', '2022-23');
INSERT INTO `nba_criteria` VALUES ('4', '4.2.2 (E)', 'Databases', '2022-23');
INSERT INTO `nba_criteria` VALUES ('4', '4.2.2(A)', 'Details of subscriptions of e-journals', '2022-23');
INSERT INTO `nba_criteria` VALUES ('4', '4.2.2(B)', 'Letter of subscription', '2022-23');
INSERT INTO `nba_criteria` VALUES ('4', '4.2.2(C)', 'Screenshots of the facilities claimed with the name of HEI.', '2022-23');
INSERT INTO `nba_criteria` VALUES ('4', '4.2.3', 'Expenditure on purchase of books/ e-books and subscription to journals/e-journals  during the year (INR in lakhs)', '2022-23');
INSERT INTO `nba_criteria` VALUES ('4', '4.2.3(A)', 'Consolidated extract of expenditure', '2022-23');
INSERT INTO `nba_criteria` VALUES ('4', '4.2.3(B)', 'Invoices of all the expenditure', '2022-23');
INSERT INTO `nba_criteria` VALUES ('4', '4.2.4', 'Usage of library by teachers and students (footfalls and login data for online access)', '2022-23');
INSERT INTO `nba_criteria` VALUES ('4', '4.2.4(A)', 'Certified e-copy of the ledger for footfalls for 5days', '2022-23');
INSERT INTO `nba_criteria` VALUES ('4', '4.2.4(B)', 'Certified screenshots of the data for the same 5 days for online access', '2022-23');
INSERT INTO `nba_criteria` VALUES ('4', '4.2.4(C)', 'Last page of accession register details', '2022-23');
INSERT INTO `nba_criteria` VALUES ('4', '4.3.1', 'Institution has an IT policy covering Wi-Fi, cyber security, etc. and has allocated  budget for updating its IT facilities', '2022-23');
INSERT INTO `nba_criteria` VALUES ('4', '4.3.1(A)', 'Colleague of Geo-tagged pictures', '2022-23');
INSERT INTO `nba_criteria` VALUES ('4', '4.3.1(B)', 'Policy document', '2022-23');
INSERT INTO `nba_criteria` VALUES ('4', '4.3.1(C)', 'Campus Wi-Fi/Network diagram', '2022-23');
INSERT INTO `nba_criteria` VALUES ('4', '4.3.1(D)', 'Budget of the year 2020-21', '2022-23');
INSERT INTO `nba_criteria` VALUES ('4', '4.3.2', 'Student - Computer ratio', '2022-23');
INSERT INTO `nba_criteria` VALUES ('4', '4.3.2(A)', 'Computer Bills', '2022-23');
INSERT INTO `nba_criteria` VALUES ('4', '4.3.2(B)', 'Student strength', '2022-23');
INSERT INTO `nba_criteria` VALUES ('4', '4.3.3', 'Bandwidth of internet connection in the Institution and the number of students on  campus', '2022-23');
INSERT INTO `nba_criteria` VALUES ('4', '4.3.3(A)', 'Details of available bandwidth of internet connection in the Institution', '2022-23');
INSERT INTO `nba_criteria` VALUES ('4', '4.3.3(B)', 'Bills for any one month/one quarter.', '2022-23');
INSERT INTO `nba_criteria` VALUES ('4', '4.3.3(C)', 'e-copy of document of agreement with the service provider.', '2022-23');
INSERT INTO `nba_criteria` VALUES ('4', '4.3.4', 'Institution has facilities for e-content development', '2022-23');
INSERT INTO `nba_criteria` VALUES ('4', '4.3.4(A)', 'Geo tagged photographs of Media Centre, Audio Visual Centre etc.,', '2022-23');
INSERT INTO `nba_criteria` VALUES ('4', '4.3.4(B)', 'Purchase bills for Lecture Capturing System', '2022-23');
INSERT INTO `nba_criteria` VALUES ('4', '4.3.4(C)', 'Audited income expenditure statement highlighting the relevant expenditure.', '2022-23');
INSERT INTO `nba_criteria` VALUES ('4', '4.4.1', 'Expenditure incurred on maintenance of physical and academic support facilities, excluding salary component, during the year (INR in lakhs)', '2022-23');
INSERT INTO `nba_criteria` VALUES ('4', '4.4.1(A)', 'Audited statements of accounts.', '2022-23');
INSERT INTO `nba_criteria` VALUES ('4', '4.4.2', 'There are established systems and procedures for maintaining and utilizing  physical, academic and support facilities – classrooms, laboratory, library, sports  complex, computers, etc.', '2022-23');
INSERT INTO `nba_criteria` VALUES ('4', '4.4.2(A)', 'Schedules of Library, Sport complex ', '2022-23');
INSERT INTO `nba_criteria` VALUES ('4', '4.4.2(B)', 'Laboratory Timetables', '2022-23');
INSERT INTO `nba_criteria` VALUES ('4', '4.4.2(C)', 'SOP for Laboratory utilization', '2022-23');
INSERT INTO `nba_criteria` VALUES ('4', '4.4.2(D)', 'SOP for usage of general amenities', '2022-23');
INSERT INTO `nba_criteria` VALUES ('4', '4.4.2(E)', 'Geo-tagged photos of GAMYA', '2022-23');
INSERT INTO `nba_criteria` VALUES ('4', '4.4.2(F)', 'Maintenance schedules and AMC letters from Estate department', '2022-23');
INSERT INTO `nba_criteria` VALUES ('5', '5.1.1', 'Number of students benefitted by scholarships and freeships provided by the  Government during the year', '2022-23');
INSERT INTO `nba_criteria` VALUES ('5', '5.1.2', 'Number of students benefitted by scholarships and freeships provided by the  institution and non-government agencies during the year', '2022-23');
INSERT INTO `nba_criteria` VALUES ('5', '5.1.3', 'The following Capacity Development and Skill Enhancement activities are  organised for improving students’ capabilities', '2022-23');
INSERT INTO `nba_criteria` VALUES ('5', '5.1.3(A)', 'Soft Skill, Language and Communication', '2022-23');
INSERT INTO `nba_criteria` VALUES ('5', '5.1.3(B)', 'Yoga Class', '2022-23');
INSERT INTO `nba_criteria` VALUES ('5', '5.1.3(C)', 'Awareness of Trends in technology', '2022-23');
INSERT INTO `nba_criteria` VALUES ('5', '5.1.4', 'Number of students benefitted from guidance/coaching for competitive  examinations and career counselling offered by the institution during the year', '2022-23');
INSERT INTO `nba_criteria` VALUES ('5', '5.1.5', 'The institution adopts the following mechanism for redressal of students’  grievances, including sexual harassment and ragging', '2022-23');
INSERT INTO `nba_criteria` VALUES ('5', '5.2.1', 'Number of outgoing students who got placement during the year', '2022-23');
INSERT INTO `nba_criteria` VALUES ('5', '5.2.2', 'Number of outgoing students progressing to higher education during the year', '2022-23');
INSERT INTO `nba_criteria` VALUES ('5', '5.2.3', 'Number of students qualifying in state/ national/ international level examinations  during the year', '2022-23');
INSERT INTO `nba_criteria` VALUES ('5', '5.3.1', 'Number of awards/medals for outstanding performance in sports and/or cultural  activities at inter-university / state /national / international events (award for a team  event should be counted as one) during the year', '2022-23');
INSERT INTO `nba_criteria` VALUES ('5', '5.3.2', 'Presence of an active Student Council and representation of students in academic  and administrative bodies/committees of the institution', '2022-23');
INSERT INTO `nba_criteria` VALUES ('5', '5.3.3', 'Number of sports and cultural events / competitions organised by the institution', '2022-23');
INSERT INTO `nba_criteria` VALUES ('5', '5.4.1', 'The Alumni Association and its Chapters (registered and functional) contribute  significantly to the development of the institution through financial and other  support services', '2022-23');
INSERT INTO `nba_criteria` VALUES ('5', '5.4.2', 'Alumni’s financial contribution during the year', '2022-23');
INSERT INTO `nba_criteria` VALUES ('6', '6.1.1', 'The governance of the institution is reflective of an effective leadership in tune with  the vision and mission of the Institution', '2022-23');
INSERT INTO `nba_criteria` VALUES ('6', '6.1.1(A)', 'Academic Monitoring Committee meeting minutes', '2022-23');
INSERT INTO `nba_criteria` VALUES ('6', '6.1.1(B)', 'Placement Committee meeting minutes', '2022-23');
INSERT INTO `nba_criteria` VALUES ('6', '6.1.1(C)', 'SAC - coordinators meeting minutes', '2022-23');
INSERT INTO `nba_criteria` VALUES ('6', '6.1.1(D)', 'Anti Ragging Committee meeting minutes', '2022-23');
INSERT INTO `nba_criteria` VALUES ('6', '6.1.1(E)', 'IQAC meeting minutes', '2022-23');
INSERT INTO `nba_criteria` VALUES ('6', '6.1.1(F)', 'Board of Studies Meeting minutes', '2022-23');
INSERT INTO `nba_criteria` VALUES ('6', '6.1.1(G)', 'Academic Council meeting minutes', '2022-23');
INSERT INTO `nba_criteria` VALUES ('6', '6.1.1(H)', 'Governing Council meeting minutes', '2022-23');
INSERT INTO `nba_criteria` VALUES ('6', '6.1.1(I)', 'HOD/Academic Development Committee Meeting Minutes', '2022-23');
INSERT INTO `nba_criteria` VALUES ('6', '6.1.1(J)', 'Finance Committee meeting minutes', '2022-23');
INSERT INTO `nba_criteria` VALUES ('6', '6.1.1(K)', 'Library Committee meeting minutes', '2022-23');
INSERT INTO `nba_criteria` VALUES ('6', '6.1.1(L)', 'Town Hall meeting minutes', '2022-23');
INSERT INTO `nba_criteria` VALUES ('6', '6.1.2', 'Effective leadership is reflected in various institutional practices such as decentralization and participative management', '2022-23');
INSERT INTO `nba_criteria` VALUES ('6', '6.1.2(A)', 'Strat-Plan', '2022-23');
INSERT INTO `nba_criteria` VALUES ('6', '6.1.2(B)', 'Requisition for two set of mid question papers from CoE – e-mail proof', '2022-23');
INSERT INTO `nba_criteria` VALUES ('6', '6.1.2(C)', 'Declaration of mid question paper set number from CoE– e-mail proof', '2022-23');
INSERT INTO `nba_criteria` VALUES ('6', '6.1.2(D)', 'Uniform Evaluation', '2022-23');
INSERT INTO `nba_criteria` VALUES ('6', '6.1.2(E)', 'Research Review meeting minutes', '2022-23');
INSERT INTO `nba_criteria` VALUES ('6', '6.2.1', 'The institutional Strategic/ Perspective plan has been clearly articulated and  implemented', '2022-23');
INSERT INTO `nba_criteria` VALUES ('6', '6.2.1(A)', 'Strat-Plan', '2022-23');
INSERT INTO `nba_criteria` VALUES ('6', '6.2.1(B)', 'Merit scholarships based on AP-EAMCET rank', '2022-23');
INSERT INTO `nba_criteria` VALUES ('6', '6.2.1(C)', 'Meritorious scholarships for students', '2022-23');
INSERT INTO `nba_criteria` VALUES ('6', '6.2.1(D)', 'Details of the GATE training classes conducted; List of students attended GATE Coaching & secured score', '2022-23');
INSERT INTO `nba_criteria` VALUES ('6', '6.2.1(E)', 'Details of the CRT programs/ Technical training/Competitions conducted (Total number of hours), the List of the students attended the training programs & Proof of attendance, Branch wise list of the students placed, and the number of companies visited. List of students attended the WTN.', '2022-23');
INSERT INTO `nba_criteria` VALUES ('6', '6.2.1(F)', 'Motivational and inspirational talks by the industry experts', '2022-23');
INSERT INTO `nba_criteria` VALUES ('6', '6.2.1(G)', 'Social media updates', '2022-23');
INSERT INTO `nba_criteria` VALUES ('6', '6.2.2', 'The functioning of the various institutional bodies is effective and efficient as visible  from the policies, administrative set-up, appointment and service rules, procedures,  etc. ', '2022-23');
INSERT INTO `nba_criteria` VALUES ('6', '6.2.2(A)', 'Organogram', '2022-23');
INSERT INTO `nba_criteria` VALUES ('6', '6.2.2(B)', 'HR & Service rules/Incentive policies', '2022-23');
INSERT INTO `nba_criteria` VALUES ('6', '6.2.2(D)', 'Frequency and conduct of the meetings of governance committees (GC, AC, BOS, FC)', '2022-23');
INSERT INTO `nba_criteria` VALUES ('6', '6.2.2(E)', 'SOP for procurement (AOP, MRN, Comparative statements and Purchase Orders)', '2022-23');
INSERT INTO `nba_criteria` VALUES ('6', '6.2.3', 'Implementation of e-governance in areas of operation: 1. Administration 2. Finance and Accounts 3. Student Admission and Support 4. Examination', '2022-23');
INSERT INTO `nba_criteria` VALUES ('6', '6.3.1', 'The institution has effective welfare measures for teaching and non-teaching staff  and avenues for their career development/ progression', '2022-23');
INSERT INTO `nba_criteria` VALUES ('6', '6.3.1(A)', 'HR Policies (Welfare & Career development)', '2022-23');
INSERT INTO `nba_criteria` VALUES ('6', '6.3.1(B)', 'Details of the training programs conducted for teaching & non-teaching staff', '2022-23');
INSERT INTO `nba_criteria` VALUES ('6', '6.3.1(C)', 'Details of the staff (Teaching & Non-teaching promoted)', '2022-23');
INSERT INTO `nba_criteria` VALUES ('6', '6.3.1(D)', 'Details of on campus housing, Term insurance, Medical insurance, Children education, ESI, Cooperative credit society, Concessions in IP/OP services and Gratuity', '2022-23');
INSERT INTO `nba_criteria` VALUES ('6', '6.3.1(E)', 'Details of the faculty received incentives for completion of Ph.D./ QIP', '2022-23');
INSERT INTO `nba_criteria` VALUES ('6', '6.3.2', 'Number of teachers provided with financial support to attend conferences /  workshops and towards payment of membership fee of professional bodies during the  year', '2022-23');
INSERT INTO `nba_criteria` VALUES ('6', '6.3.2(A)', 'Number of teachers provided with financial support to attend conferences / workshops and towards payment of membership fee of professional bodies during the year', '2022-23');
INSERT INTO `nba_criteria` VALUES ('6', '6.3.3', 'Number of professional development / administrative training programmes  organized by the Institution for its teaching and non-teaching staff during the year', '2022-23');
INSERT INTO `nba_criteria` VALUES ('6', '6.3.3(A)', 'Annual Reports highlighting training programs conducted for teaching & non teaching', '2022-23');
INSERT INTO `nba_criteria` VALUES ('6', '6.3.3(B)', 'Training programs conducted for teaching & non-teaching', '2022-23');
INSERT INTO `nba_criteria` VALUES ('6', '6.3.4', 'Number of teachers who have undergone online/ face-to-face Faculty Development  Programmes during the year: (Professional Development Programmes, Orientation / Induction Programmes,  Refresher Courses, Short-Term Course, etc.)', '2022-23');
INSERT INTO `nba_criteria` VALUES ('6', '6.3.4(A)', 'List of Faculty FDPS Attended & Proofs', '2022-23');
INSERT INTO `nba_criteria` VALUES ('6', '6.4.1', 'Institution conducts internal and external financial audits regularly', '2022-23');
INSERT INTO `nba_criteria` VALUES ('6', '6.4.2', 'Funds / Grants received from non-government bodies, individuals, and  philanthropists during the year (not covered in Criterion III and V) (INR in lakhs)', '2022-23');
INSERT INTO `nba_criteria` VALUES ('6', '6.4.3', 'Institutional strategies for mobilisation of funds and the optimal utilisation of  resources', '2022-23');
INSERT INTO `nba_criteria` VALUES ('6', '6.5.1', 'Internal Quality Assurance Cell (IQAC) has contributed significantly for  institutionalizing quality assurance strategies and processes visible in terms of  incremental improvements made during the preceding year with regard to quality (in  case of the First Cycle): Incremental improvements made during the preceding year with regard to quality  and post-accreditation quality initiatives (Second and subsequent cycles)', '2022-23');
INSERT INTO `nba_criteria` VALUES ('6', '6.5.1(A)', 'List of companies hosted Internship', '2022-23');
INSERT INTO `nba_criteria` VALUES ('6', '6.5.1(B)', 'FADS – Supporting Docs', '2022-23');
INSERT INTO `nba_criteria` VALUES ('6', '6.5.1(C)', 'Consolidated initiatives of IQAC (strategies & contributions of IQAC mentioned)', '2022-23');
INSERT INTO `nba_criteria` VALUES ('6', '6.5.2', 'The institution reviews its teaching-learning process, structures and methodologies  of operation and learning outcomes at periodic intervals through its IQAC as per  norms', '2022-23');
INSERT INTO `nba_criteria` VALUES ('6', '6.5.2(A)', 'List of the IQAC initiatives taken up to enhance the students’ performance (Course coordinator meetings, AMC meeting minutes, Remedial classes for slow learners)', '2022-23');
INSERT INTO `nba_criteria` VALUES ('6', '6.5.2(B)', 'Details of the progression of the students from 1st to 8th semesters branch wise for 2020-21 for all the batches graduated – 1 Bar chart, in each bar chart with eight bars', '2022-23');
INSERT INTO `nba_criteria` VALUES ('6', '6.5.2(C)', 'Structures & methodologies of operations ISO audits, Academic audits -Internal & External branch wise', '2022-23');
INSERT INTO `nba_criteria` VALUES ('6', '6.5.2(D)', 'Internal & External Academic Audit Report', '2022-23');
INSERT INTO `nba_criteria` VALUES ('6', '6.5.3', 'Quality assurance initiatives of the institution include', '2022-23');
INSERT INTO `nba_criteria` VALUES ('6', '6.5.3(A)', 'IQAC meeting minutes', '2022-23');
INSERT INTO `nba_criteria` VALUES ('6', '6.5.3(B)', 'Feedback system of the institution', '2022-23');
INSERT INTO `nba_criteria` VALUES ('6', '6.5.3(C)', 'Feedback system for design and review of syllabus', '2022-23');
INSERT INTO `nba_criteria` VALUES ('6', '6.5.3(D)', 'Collaborative quality initiatives', '2022-23');
INSERT INTO `nba_criteria` VALUES ('6', '6.5.3(E)', 'Participation in NIRF', '2022-23');
INSERT INTO `nba_criteria` VALUES ('6', '6.5.3(F)', 'NBA accreditation', '2022-23');
INSERT INTO `nba_criteria` VALUES ('6', '6.5.3(G)', 'NAAC accreditation', '2022-23');
INSERT INTO `nba_criteria` VALUES ('6', '6.5.3(H)', 'IQAC Feedback analysis', '2022-23');
INSERT INTO `nba_criteria` VALUES ('7', '7.1.1', 'Measures initiated by the institution for the promotion of gender equity during the  year', '2022-23');
INSERT INTO `nba_criteria` VALUES ('7', '7.1.1(A)', 'Action plan of WEC for gender sensitization', '2022-23');
INSERT INTO `nba_criteria` VALUES ('7', '7.1.1(B)', 'Campus surveillance with CC TV (Audit report)', '2022-23');
INSERT INTO `nba_criteria` VALUES ('7', '7.1.1(C)', 'Policy for women security and safety (PASH)', '2022-23');
INSERT INTO `nba_criteria` VALUES ('7', '7.1.1(D)', 'Photographs of Exclusive reading rooms, waiting rooms and rest rooms', '2022-23');
INSERT INTO `nba_criteria` VALUES ('7', '7.1.1(E)', 'Day care centre for the kids (Beneficiaries)', '2022-23');
INSERT INTO `nba_criteria` VALUES ('7', '7.1.1(F)', 'Welfare measures (Maternity leave for two kids)', '2022-23');
INSERT INTO `nba_criteria` VALUES ('7', '7.1.1(G)', 'List of the beneficiaries', '2022-23');
INSERT INTO `nba_criteria` VALUES ('7', '7.1.10', 'The institution has a prescribed code of conduct for students, teachers, administrators  and other staff and conducts periodic sensitization programmes in this regard', '2022-23');
INSERT INTO `nba_criteria` VALUES ('7', '7.1.10(A)', 'Details of the monitoring committee composition and minutes of the committee meeting, number of programmes organized, reports on the various programmes, etc. in support of the claims', '2022-23');
INSERT INTO `nba_criteria` VALUES ('7', '7.1.10(B)', 'Policy document on code of ethics', '2022-23');
INSERT INTO `nba_criteria` VALUES ('7', '7.1.10(C)', 'Circulars and geo tagged photographs and caption of the activities organized under the metric for teachers, students, administrators and other staffs', '2022-23');
INSERT INTO `nba_criteria` VALUES ('7', '7.1.11', 'Institution celebrates / organizes national and international commemorative days,  events and festivals', '2022-23');
INSERT INTO `nba_criteria` VALUES ('7', '7.1.11(A)', 'Annual report of the celebrations and commemorative events', '2022-23');
INSERT INTO `nba_criteria` VALUES ('7', '7.1.11(B)', 'Photographs of some of the events', '2022-23');
INSERT INTO `nba_criteria` VALUES ('7', '7.1.2', 'The Institution has facilities for alternate sources of energy and energy conservation ', '2022-23');
INSERT INTO `nba_criteria` VALUES ('7', '7.1.2(A)', 'Geo tagged photographs with caption of the facilities', '2022-23');
INSERT INTO `nba_criteria` VALUES ('7', '7.1.2(B)', 'Bills for the purchase of equipment for the facilities', '2022-23');
INSERT INTO `nba_criteria` VALUES ('7', '7.1.2(C)', 'Permission document for connection to the grid from Government', '2022-23');
INSERT INTO `nba_criteria` VALUES ('7', '7.1.3', 'Describe the facilities in the institution for the management of the following types of  degradable and non-degradable waste (within a maximum of 200 words)', '2022-23');
INSERT INTO `nba_criteria` VALUES ('7', '7.1.3(A)', 'Geo tagged photographs of the facilities', '2022-23');
INSERT INTO `nba_criteria` VALUES ('7', '7.1.3(B)', 'SOP for solid waste management', '2022-23');
INSERT INTO `nba_criteria` VALUES ('7', '7.1.4', 'Water conservation facilities available in the institution', '2022-23');
INSERT INTO `nba_criteria` VALUES ('7', '7.1.4(A)', 'Geo tagged photographs with caption of the facilities', '2022-23');
INSERT INTO `nba_criteria` VALUES ('7', '7.1.4(B)', 'Bills for the purchase of equipment', '2022-23');
INSERT INTO `nba_criteria` VALUES ('7', '7.1.5', 'Green campus initiatives include', '2022-23');
INSERT INTO `nba_criteria` VALUES ('7', '7.1.5(A)', 'Policy document on the green campus', '2022-23');
INSERT INTO `nba_criteria` VALUES ('7', '7.1.5(B)', 'Geo tagged photographs/Videos with caption of the facilities', '2022-23');
INSERT INTO `nba_criteria` VALUES ('7', '7.1.5(C)', 'Circulars for the implementation of the initiatives and any other supporting document', '2022-23');
INSERT INTO `nba_criteria` VALUES ('7', '7.1.6', 'Quality audits on environment and energy undertaken by the institution', '2022-23');
INSERT INTO `nba_criteria` VALUES ('7', '7.1.6(A)', 'Policy document on environment and energy usage', '2022-23');
INSERT INTO `nba_criteria` VALUES ('7', '7.1.6(B)', 'Certificate from the auditing agency', '2022-23');
INSERT INTO `nba_criteria` VALUES ('7', '7.1.6(C)', 'Certificates of the awards received from the recognized agency', '2022-23');
INSERT INTO `nba_criteria` VALUES ('7', '7.1.6(D)', 'Report on environmental promotional activities conducted beyond the campus', '2022-23');
INSERT INTO `nba_criteria` VALUES ('7', '7.1.6(F)', 'Green audit report', '2022-23');
INSERT INTO `nba_criteria` VALUES ('7', '7.1.7', 'The Institution has a Divyangjan-friendly and barrier-free environment', '2022-23');
INSERT INTO `nba_criteria` VALUES ('7', '7.1.7(A)', 'Policy document and information brochure', '2022-23');
INSERT INTO `nba_criteria` VALUES ('7', '7.1.7(B)', 'Geo tagged photos', '2022-23');
INSERT INTO `nba_criteria` VALUES ('7', '7.1.7(C)', 'Bills and invoice/purchase order/AMC', '2022-23');
INSERT INTO `nba_criteria` VALUES ('7', '7.1.7(D)', 'A rest room should include specific requirements of Divyangjan', '2022-23');
INSERT INTO `nba_criteria` VALUES ('7', '7.1.7(E)', 'Bills for the software procured', '2022-23');
INSERT INTO `nba_criteria` VALUES ('7', '7.1.8', 'Describe the Institutional efforts/initiatives in providing an inclusive environment i.e.  tolerance and harmony towards cultural, regional, linguistic, communal, socioeconomic and other diversities (within a maximum of 200 words)', '2022-23');
INSERT INTO `nba_criteria` VALUES ('7', '7.1.8(A)', 'List of the outbound programs', '2022-23');
INSERT INTO `nba_criteria` VALUES ('7', '7.1.8(B)', 'List of national level activities (Cultural, Sports, Academic, Cultural & Sports)', '2022-23');
INSERT INTO `nba_criteria` VALUES ('7', '7.1.8(C)', 'List of the faculty & students coming from the out of state', '2022-23');
INSERT INTO `nba_criteria` VALUES ('7', '7.1.9', 'Sensitization of students and employees of the institution to constitutional obligations:  values, rights, duties and responsibilities of citizens', '2022-23');
INSERT INTO `nba_criteria` VALUES ('7', '7.1.9(A)', 'List of the activities/Events conducted, and the number of students present. (Awareness programs on Women safety & protection, Anti ragging, Judicial rights, Gender equality, Traffic rules, Environment protection, Conservation of natural resources (power & water)) >', '2022-23');
INSERT INTO `nba_criteria` VALUES ('7', '7.2.1', 'Provide the weblink on the Institutional website regarding the Best practices as per  the prescribed format of NAAC', '2022-23');
INSERT INTO `nba_criteria` VALUES ('7', '7.3.1', 'Institutional Distinctiveness', '2022-23');

DROP TABLE IF EXISTS `nba_criteria1`;
CREATE TABLE `nba_criteria1` (
  `SI_no` int(10) NOT NULL,
  `Sub_no` varchar(30) NOT NULL,
  `Des` varchar(600) NOT NULL,
  `year` varchar(200) NOT NULL,
  PRIMARY KEY (`Sub_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `nba_criteria1` VALUES ('1', '1.1.1', 'Copies of PEOs, POs & PSOs for all programs', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('1', '1.1.2', 'Number of Programmes where syllabus revision was carried out during the year(Data Requirement: Programme Code ,Names of the Programmes revised)', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('1', '1.1.2(A)', 'List of programs where syllabus revision has been carried out signed by the Principal', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('1', '1.1.2(B)', 'Approved Minutes of relevant Academic Council/BOS meetings highlighting the specific agenda item relevant to the metric year wise', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('1', '1.1.3', 'Number of courses focusing on employability/entrepreneurship/ skill development  offered by the Institution during the year(Data Requirement: Name of the Course with Course Code ,Name of the Programme ,Activities which have a direct bearing on employability/ entrepreneurship/ skill  development)', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('1', '1.1.3(A)', 'Syllabus copy of the courses highlighting Focus on employability/entrepreneurship/ skill development along with their course outcomes.', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('1', '1.1.3(B)', 'Minutes of the Boards of Studies/ Academic Council meetings with approval for these courses.', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('1', '1.1.3(C)', 'List of MoUs', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('1', '1.2.1', 'Number of new courses introduced across all programmes offered during the year(Data Requirement: Name of the newly introduced course (s)  ,Name of the Programme)', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('1', '1.2.1(A)', 'List of new courses introduced program-wise during the assessment period certified by the Principal.', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('1', '1.2.1(B)', 'Minutes of relevant Academic Council/BOS meetings highlighting the name of the new courses introduced', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('1', '1.2.2', 'Number of Programmes offered through Choice Based Credit System  (CBCS)/Elective Course System(Data Requirement: ,Names of all Programmes offered through CBCS ,Names of all Programmes offered through Elective Course System)', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('1', '1.2.2(A)', 'List of programs in which CBCS/Elective course system implemented in the last completed academic year certified by the principal', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('1', '1.2.2(B)', 'Structure of the program clearly indicating courses, credits/Electives and Minutes of relevant Academic Council/BOS meetings highlighting the relevant documents to this metric', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('1', '1.3.1', 'List and Description of courses relevant to Professional Ethics, Gender diversity and equality, Human Values, Environment and Sustainability, Women Empowerment introduced in the Curriculum along with the syllabus should be available in all the departments', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('1', '1.3.2', 'Number of value-added courses for imparting transferable and life skills offered  during the year:(Data Requirement:  Names of the value-added courses (each with 30 or more contact hours) ,No. of times offered (for each value-added course) during the year ,Total number of students enrolled ,Total number of students completing the course during the year', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('1', '1.3.2(A)', 'List of value-added courses which are optional and offered outside the curriculum of the programs with authorized sign.', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('1', '1.3.2(B)', 'Brochure and Course content or syllabus along with course outcome of Value-added courses offered.', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('1', '1.3.3', 'List of enrolled students for courses addressed in 1.3.2', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('1', '1.3.4', 'List of students undertaking the field projects/ internships / student projects program-wise in the last completed academic year along with the details of title, place of work etc.', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('1', '1.4.1', 'Sample Filled in feedback forms from the stakeholders to be provided.', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('1', '1.4.2', 'Stakeholder feedback analysis report signed by the authority', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('2', '2.1.1', 'Enrolment of Students ', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('2', '2.1.1(A)', 'Students admitted(HEI signed documents)', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('2', '2.1.1(B)', 'Enrolment Number_ AICTE letters of sanctioned intake', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('2', '2.1.1(C)', 'Enrolment Number _ Ratified list of admitted students', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('2', '2.1.2', 'Number of seats filled against reserved categories (SC, ST, OBC, Divyangjan,  etc.) as per the reservation policy during the year (exclusive of supernumerary  seats)', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('2', '2.1.2(A)', 'Reservation seats categories to be considered as per the state rule (APSHE Guidelines)', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('2', '2.1.2(B)', 'AP regulation for Admissions GO No. 73', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('2', '2.1.2(C)', 'Guidelines for filling the left-over vacancies under reservation', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('2', '2.1.2(D)', 'Category wise_ Ratified list of students admitted', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('2', '2.1.2(E)', 'Admission Abstract(HEI signed documents)', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('2', '2.2.1', 'The institution assesses students’ learning levels and organises special  programmes for both slow and advanced learners(Present a write-up within a maximum of 200 words)', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('2', '2.2.1(A)', 'Video records of the specific topics(A document having the links and screen shots of video course/LCS)', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('2', '2.2.1(B)', 'Classes for the slow learner(Time table conducted for CA failures and attendance sheets)', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('2', '2.2.1(C)', 'Additional assignment(sample copies of either question papers or evaluated assignment)', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('2', '2.2.1(D)', 'Make-up classes for course detention students/lateral entry students(Time tables and attendance sheets for each semester(1-8) at least one)', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('2', '2.2.1(E)', 'Remedial classes conducted for end semester failures(Time tables, attendance sheets and track sheets for each semester(1-8) at least one)', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('2', '2.2.1(F)', 'List of MOOCs courses(Details of student with course name and sample certificates)', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('2', '2.2.1(G)', 'List of the advanced learners opted Minors and honors(Abstract certified by controller of examination)', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('2', '2.2.1(H)', 'Student participation in technical events list with students event details and SAMPLE certificates', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('2', '2.2.2', 'Student – Teacher (full-time) ratio(Data Requirement:  Total number of students in the institution ,Total number of full-time teachers in the institution)', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('2', '2.2.2(A)', 'List of the full-time teachers', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('2', '2.2.2(B)', 'Student intake', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('2', '2.3.1', 'Student-centric methods such as experiential learning, participative learning and  problem-solving methodologies are used for enhancing learning experiences(Present a write-up within a maximum of 200 words.)', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('2', '2.3.1(A)', 'Academic regulations and curriculum(Recent)', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('2', '2.3.2', 'Teachers use ICT-enabled tools including online resources for effective teaching  and learning(Present a write-up within a maximum of 200 words.)', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('2', '2.3.2 (A)', 'ICT Tools Gadgets viz. Graphic tablets, MST, Smart pen, Projector(Geotagged photos with title of photo)', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('2', '2.3.3', 'Ratio of students to mentor for academic and other related issues', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('2', '2.3.3(A)', 'Circular of mentor – mentees', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('2', '2.3.3(B)', 'Mentor list as announced by the HEI', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('2', '2.3.3(C)', 'issues raised and resolved in the mentor system(Total records of a student -&gt; one sample from each semester)', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('2', '2.3.4', 'Preparation and adherence to Academic Calendar and Teaching Plans by the  institution(Describe the preparation of and adherence to the Academic Calendar and Teaching  Plans by the institution.  Present a write-up within a maximum of 200 words)', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('2', '2.3.4(A)', 'Academic calendar', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('2', '2.3.4(B)', 'Proof of course allotment for odd and even semesters', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('2', '2.3.4(C)', 'Proof for lecture plan, Schedule and diary(one course from each semester)', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('2', '2.3.4(D)', 'Minutes of AMC', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('2', '2.4.1', 'Number of full-time teachers against sanctioned posts during the year(Data Requirement:  Number of full-time teachers ,Number of sanctioned posts)', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('2', '2.4.1(A)', 'Sanction letter indicating number of posts', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('2', '2.4.1(B)', 'Department wise List of full-time teachers appointed', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('2', '2.4.2', 'Number of full-time teachers with PhD/ D.M. / M.Ch. / D.N.B Super-Specialty /  DSc / DLitt during the year(Data Requirement: List of full-time teachers with PhD/ D.M. / M.Ch. / D.N.B Super-Specialty /  DSc / DLitt.)', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('2', '2.4.3', 'Total teaching experience of full-time teachers in the same institution(Data Requirement:  Name and number of full-time teachers and their years of teaching experience in the institution)', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('2', '2.5.1', 'Number of days from the date of last semester-end/ year- end examination till the  declaration of results during the year(Number of days from the date of last semester-end / year-end examination till the  declaration of results year-wise during the year)', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('2', '2.5.1(A)', 'Examination Result Notifications', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('2', '2.5.1(B)', 'List of Programs offered and the last date of the latest semester end exams and date of result declaration', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('2', '2.5.2', 'Number of students’ complaints/grievances against evaluation against the total  number who appeared in the examinations during the year', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('2', '2.5.2(A)', 'Number of complaints', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('2', '2.5.2(B)', 'Minutes of the examination Committee', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('2', '2.5.3', 'IT integration and reforms in the examination procedures and processes  including Continuous Internal Assessment (CIA) have brought in considerable  improvement in the Examination Management System (EMS) of the Institution(Describe the examination reforms with reference to the following within a minimum  of 200 words :Examination procedures ,Processes/Procedures integrating IT ,Continuous Internal Assessment System) ', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('2', '2.5.3(A)', 'Examination Regulations', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('2', '2.5.3(B)', 'Question Paper templates', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('2', '2.5.3(C)', 'Proof of the hybrid grading sheet', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('2', '2.5.3(D)', 'Examination Results link in the website', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('2', '2.5.3(E)', 'Proof of the OMR', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('2', '2.6.1', 'Programme Outcomes and Course Outcomes for all Programmes offered by the  institution are stated and displayed on the website and communicated to teachers  and students(Describe Course Outcomes (COs) for all courses and the mechanism of  communication to teachers and students within a maximum of 200 words.  Upload COs for all Courses) ', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('2', '2.6.1(A)', 'List of POs and COs(COs for at least two courses from each semester)', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('2', '2.6.1(B)', 'Photo gallery of the COs & POs displayed Program wise', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('2', '2.6.2', 'Attainment of Programme Outcomes and Course Outcomes as evaluated by the  institution(Describe the method of measuring the attainment of POs, PSOs and COs and the  level of attaiment of POs , PSOs and COs in not more than 200 words.)', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('2', '2.6.2(A)', 'CO and PO attainment', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('2', '2.6.2(B)', 'Files related to all surveys for the indirect assessment', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('2', '2.6.3', 'Pass Percentage of students', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('2', '2.6.3(A)', 'Annual report of CoE indicating the pass percentage', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('2', '2.6.3(B)', 'Certified report from CoE indicating Students eligible for the degree program', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('2', '2.7.1', 'Student Satisfaction Survey (SSS) on overall institutional performance  (Institution may design its own questionnaire). Results and details need to be  provided as a weblink', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('3', '3.1.1', 'The institution’s research facilities are frequently updated and there is a welldefined policy for promotion of research which is uploaded on the institutional  website and implemented(Present a write-up within a maximum of 200 words.)', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('3', '3.1.1(A)', 'List of research equipment along with the proof of purchases (Bill copies)', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('3', '3.1.1(B)', 'Policy for Faculty Assessment and Development Scheme', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('3', '3.1.1(C)', 'Financial Incentives for attending conferences, seminars and QIP', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('3', '3.1.1(D)', 'Sanction letters for the funded research projects & UCs for the projects completed', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('3', '3.1.1(E)', 'Copies of all MoUs for collaborative research', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('3', '3.1.1(F)', 'Minutes of the governing council meeting-reflecting the research promotions', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('3', '3.1.2', 'Details of Seed money', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('3', '3.1.3', 'Number of teachers who were awarded national / international fellowship(s) for  advanced studies/research during the year', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('3', '3.2.1', 'Grants received from Government and Non-Governmental agencies for research  projects, endowments, Chairs during the year (INR in Lakhs)', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('3', '3.2.1(A)', 'List of Grants received for research projects', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('3', '3.2.1(B)', 'e-copies of grants sanctioned', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('3', '3.2.2', 'List of teachers having research projects during the year', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('3', '3.2.3', 'Number of teachers recognized as research guides', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('3', '3.2.4', 'Number of departments having research projects', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('3', '3.2.4(A)', 'Web Links to Funding Agencies', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('3', '3.3.1', 'Institution has created an ecosystem for innovations and creation and transfer of  knowledge supported by dedicated centres for research, entrepreneurship,  community orientation, incubation, etc.(Present a write-up within a maximum of 200 words)', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('3', '3.3.1(A)', 'MSME business incubation center – sanction letter', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('3', '3.3.1(B)', 'AICTE sponsored EDC – Sanction letters and list of the activities conducted by EDC', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('3', '3.3.1(C)', 'Dedicated centres for research', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('3', '3.3.2', 'Detailed report including photos, resource persons etc.', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('3', '3.4.1', 'The Institution ensures implementation of its Code of Ethics for Research  uploaded in the website through the following:  1. Research Advisory Committee 2. Ethics Committee 3. Inclusion of Research Ethics in the research methodology course work  4. Plagiarism check through authenticated software Options: A. All of the above B. Any 3 of the above C. Any 2 of the above D. Any 1 of the above E. None of the above', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('3', '3.4.1(A)', 'Research Advisory Committee', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('3', '3.4.1(B)', 'Ethics Committee', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('3', '3.4.1(C)', 'Inclusion of Research Ethics in the research methodology course work', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('3', '3.4.1(D)', 'Anti Plagiarism software approved by JNTU', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('3', '3.4.2', 'Number of PhD candidates registered per teacher (as per the data given with  regard to recognized PhD guides/ supervisors provided in Metric No. 3.2.3) during  the year', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('3', '3.4.2(A)', 'List of Faculty along with the names of Research scholars', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('3', '3.4.2(B)', 'Copy of the Registration letters/Joining letters', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('3', '3.4.3', 'Number of research papers per teacher in CARE Journals notified on UGC  website during the year', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('3', '3.4.4', 'Number of books and chapters in edited volumes / books published per teacher  during the year', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('3', '3.4.5', 'Bibliometrics of the publications based on average Citation Index', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('3', '3.4.6', 'Bibliometrics of the publication-based h-Index of the University', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('3', '3.5.1', 'Audited statements for Revenue generated from consultancy', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('3', '3.5.2', 'Total amount spent on developing facilities, training teachers and clerical/project  staff for undertaking consultancy during the year', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('3', '3.6.1', 'Extension activities carried out in the neighbourhood sensitising students to social  issues for their holistic development, and the impact thereof during the year', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('3', '3.6.1(A)', 'List of NSS activities conducted year wise and number of students participated', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('3', '3.6.1(B)', 'Covid-19 booster report', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('3', '3.6.1(C)', 'MGNCRE Reports', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('3', '3.6.1(D)', 'Community Radio report', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('3', '3.6.1(E)', 'List of NCC activities conducted year wise and number of students participated', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('3', '3.6.2', 'Number of awards and recognition received by the Institution, its teachers and  students for extension activities from Government / Government-recognised bodies', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('3', '3.6.3', 'Number of extension and outreach programmes conducted by the institution through  NSS/NCC during the year', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('3', '3.6.4', 'Number of students participating in extension activities listed in 3.6.3 during the  year', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('3', '3.7.1', 'Number of collaborative activities during the year for research/ faculty exchange/  student exchange/ internship/ on-the-job training/ project work', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('3', '3.7.2', 'Number of functional MoUs with institutions of national and/or international  importance, other universities, industries, corporate houses, etc. during the year  (only functional MoUs with ongoing activities to be considered)', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('3', '3.7.2(A)', 'e-copies of functional MoUs', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('3', '3.7.2(B)', 'e-copies of Activities of MOUs', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('4', '4.1.1', 'The Institution has adequate infrastructure and physical facilities for teachinglearning, viz., classrooms, laboratories, computing equipments, etc.', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('4', '4.1.1(A)', 'Details of the Classrooms, Labs & Other facilities across all the six blocks', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('4', '4.1.1(B)', 'List of the laboratories with titles', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('4', '4.1.1(C)', 'Campus LAN diagram', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('4', '4.1.1(D)', 'Proof of bandwidth', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('4', '4.1.1(E)', 'List of the software', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('4', '4.1.1(F)', 'Photo gallery of all the academic blocks, classrooms, drawing hall, seminar hall, auditorium, and Labs', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('4', '4.1.2', 'The institution has adequate facilities for cultural activities, yoga, sports and games  (indoor and outdoor) including gymnasium, yoga centre, auditorium etc.)', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('4', '4.1.2(A)', 'Colleague of Geo-tagged pictures', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('4', '4.1.2(B)', 'Area details of the all the facilities', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('4', '4.1.2(C)', 'Photo gallery of the various activities', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('4', '4.1.3', 'Number of classrooms and seminar halls with ICT-enabled facilities', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('4', '4.1.3(A)', 'Geo-tagged photographs of classrooms with ICT enabled facilities', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('4', '4.1.3(B)', 'Class Timetables', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('4', '4.1.4', 'Expenditure for infrastructure augmentation, excluding salary, during the year (INR  in Lakhs)', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('4', '4.1.4(A)', 'Budget allocation', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('4', '4.1.4(B)', 'Provide the consolidated fund allocation towards infrastructure augmentation facilities duly certified by Head of the Institution', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('4', '4.2.1', 'Library is automated using Integrated Library Management System (ILMS)', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('4', '4.2.1(A)', 'Library operates through LIBSYS', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('4', '4.2.1(B)', 'All the books are provided with RF Id security tags', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('4', '4.2.1(C)', 'Copy of the latest License agreement of LIBSYS-7', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('4', '4.2.1(D)', 'Digital Library', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('4', '4.2.2', 'Institution has access to the following', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('4', '4.2.2 (D)', 'Specific details in respect of e-resources selected.', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('4', '4.2.2 (E)', 'Databases', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('4', '4.2.2(A)', 'Details of subscriptions of e-journals', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('4', '4.2.2(B)', 'Letter of subscription', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('4', '4.2.2(C)', 'Screenshots of the facilities claimed with the name of HEI.', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('4', '4.2.3', 'Expenditure on purchase of books/ e-books and subscription to journals/e-journals  during the year (INR in lakhs)', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('4', '4.2.3(A)', 'Consolidated extract of expenditure', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('4', '4.2.3(B)', 'Invoices of all the expenditure', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('4', '4.2.4', 'Usage of library by teachers and students (footfalls and login data for online access)', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('4', '4.2.4(A)', 'Certified e-copy of the ledger for footfalls for 5days', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('4', '4.2.4(B)', 'Certified screenshots of the data for the same 5 days for online access', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('4', '4.2.4(C)', 'Last page of accession register details', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('4', '4.3.1', 'Institution has an IT policy covering Wi-Fi, cyber security, etc. and has allocated  budget for updating its IT facilities', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('4', '4.3.1(A)', 'Colleague of Geo-tagged pictures', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('4', '4.3.1(B)', 'Policy document', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('4', '4.3.1(C)', 'Campus Wi-Fi/Network diagram', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('4', '4.3.1(D)', 'Budget of the year 2020-21', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('4', '4.3.2', 'Student - Computer ratio', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('4', '4.3.2(A)', 'Computer Bills', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('4', '4.3.2(B)', 'Student strength', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('4', '4.3.3', 'Bandwidth of internet connection in the Institution and the number of students on  campus', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('4', '4.3.3(A)', 'Details of available bandwidth of internet connection in the Institution', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('4', '4.3.3(B)', 'Bills for any one month/one quarter.', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('4', '4.3.3(C)', 'e-copy of document of agreement with the service provider.', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('4', '4.3.4', 'Institution has facilities for e-content development', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('4', '4.3.4(A)', 'Geo tagged photographs of Media Centre, Audio Visual Centre etc.,', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('4', '4.3.4(B)', 'Purchase bills for Lecture Capturing System', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('4', '4.3.4(C)', 'Audited income expenditure statement highlighting the relevant expenditure.', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('4', '4.4.1', 'Expenditure incurred on maintenance of physical and academic support facilities, excluding salary component, during the year (INR in lakhs)', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('4', '4.4.1(A)', 'Audited statements of accounts.', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('4', '4.4.2', 'There are established systems and procedures for maintaining and utilizing  physical, academic and support facilities – classrooms, laboratory, library, sports  complex, computers, etc.', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('4', '4.4.2(A)', 'Schedules of Library, Sport complex ', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('4', '4.4.2(B)', 'Laboratory Timetables', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('4', '4.4.2(C)', 'SOP for Laboratory utilization', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('4', '4.4.2(D)', 'SOP for usage of general amenities', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('4', '4.4.2(E)', 'Geo-tagged photos of GAMYA', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('4', '4.4.2(F)', 'Maintenance schedules and AMC letters from Estate department', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('5', '5.1.1', 'Number of students benefitted by scholarships and freeships provided by the  Government during the year', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('5', '5.1.2', 'Number of students benefitted by scholarships and freeships provided by the  institution and non-government agencies during the year', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('5', '5.1.3', 'The following Capacity Development and Skill Enhancement activities are  organised for improving students’ capabilities', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('5', '5.1.3(A)', 'Soft Skill, Language and Communication', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('5', '5.1.3(B)', 'Yoga Class', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('5', '5.1.3(C)', 'Awareness of Trends in technology', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('5', '5.1.4', 'Number of students benefitted from guidance/coaching for competitive  examinations and career counselling offered by the institution during the year', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('5', '5.1.5', 'The institution adopts the following mechanism for redressal of students’  grievances, including sexual harassment and ragging', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('5', '5.2.1', 'Number of outgoing students who got placement during the year', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('5', '5.2.2', 'Number of outgoing students progressing to higher education during the year', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('5', '5.2.3', 'Number of students qualifying in state/ national/ international level examinations  during the year', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('5', '5.3.1', 'Number of awards/medals for outstanding performance in sports and/or cultural  activities at inter-university / state /national / international events (award for a team  event should be counted as one) during the year', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('5', '5.3.2', 'Presence of an active Student Council and representation of students in academic  and administrative bodies/committees of the institution', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('5', '5.3.3', 'Number of sports and cultural events / competitions organised by the institution', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('5', '5.4.1', 'The Alumni Association and its Chapters (registered and functional) contribute  significantly to the development of the institution through financial and other  support services', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('5', '5.4.2', 'Alumni’s financial contribution during the year', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('6', '6.1.1', 'The governance of the institution is reflective of an effective leadership in tune with  the vision and mission of the Institution', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('6', '6.1.1(A)', 'Academic Monitoring Committee meeting minutes', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('6', '6.1.1(B)', 'Placement Committee meeting minutes', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('6', '6.1.1(C)', 'SAC - coordinators meeting minutes', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('6', '6.1.1(D)', 'Anti Ragging Committee meeting minutes', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('6', '6.1.1(E)', 'IQAC meeting minutes', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('6', '6.1.1(F)', 'Board of Studies Meeting minutes', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('6', '6.1.1(G)', 'Academic Council meeting minutes', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('6', '6.1.1(H)', 'Governing Council meeting minutes', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('6', '6.1.1(I)', 'HOD/Academic Development Committee Meeting Minutes', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('6', '6.1.1(J)', 'Finance Committee meeting minutes', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('6', '6.1.1(K)', 'Library Committee meeting minutes', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('6', '6.1.1(L)', 'Town Hall meeting minutes', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('6', '6.1.2', 'Effective leadership is reflected in various institutional practices such as decentralization and participative management', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('6', '6.1.2(A)', 'Strat-Plan', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('6', '6.1.2(B)', 'Requisition for two set of mid question papers from CoE – e-mail proof', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('6', '6.1.2(C)', 'Declaration of mid question paper set number from CoE– e-mail proof', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('6', '6.1.2(D)', 'Uniform Evaluation', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('6', '6.1.2(E)', 'Research Review meeting minutes', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('6', '6.2.1', 'The institutional Strategic/ Perspective plan has been clearly articulated and  implemented', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('6', '6.2.1(A)', 'Strat-Plan', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('6', '6.2.1(B)', 'Merit scholarships based on AP-EAMCET rank', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('6', '6.2.1(C)', 'Meritorious scholarships for students', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('6', '6.2.1(D)', 'Details of the GATE training classes conducted; List of students attended GATE Coaching & secured score', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('6', '6.2.1(E)', 'Details of the CRT programs/ Technical training/Competitions conducted (Total number of hours), the List of the students attended the training programs & Proof of attendance, Branch wise list of the students placed, and the number of companies visited. List of students attended the WTN.', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('6', '6.2.1(F)', 'Motivational and inspirational talks by the industry experts', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('6', '6.2.1(G)', 'Social media updates', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('6', '6.2.2', 'The functioning of the various institutional bodies is effective and efficient as visible  from the policies, administrative set-up, appointment and service rules, procedures,  etc. ', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('6', '6.2.2(A)', 'Organogram', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('6', '6.2.2(B)', 'HR & Service rules/Incentive policies', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('6', '6.2.2(D)', 'Frequency and conduct of the meetings of governance committees (GC, AC, BOS, FC)', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('6', '6.2.2(E)', 'SOP for procurement (AOP, MRN, Comparative statements and Purchase Orders)', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('6', '6.2.3', 'Implementation of e-governance in areas of operation: 1. Administration 2. Finance and Accounts 3. Student Admission and Support 4. Examination', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('6', '6.3.1', 'The institution has effective welfare measures for teaching and non-teaching staff  and avenues for their career development/ progression', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('6', '6.3.1(A)', 'HR Policies (Welfare & Career development)', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('6', '6.3.1(B)', 'Details of the training programs conducted for teaching & non-teaching staff', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('6', '6.3.1(C)', 'Details of the staff (Teaching & Non-teaching promoted)', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('6', '6.3.1(D)', 'Details of on campus housing, Term insurance, Medical insurance, Children education, ESI, Cooperative credit society, Concessions in IP/OP services and Gratuity', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('6', '6.3.1(E)', 'Details of the faculty received incentives for completion of Ph.D./ QIP', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('6', '6.3.2', 'Number of teachers provided with financial support to attend conferences /  workshops and towards payment of membership fee of professional bodies during the  year', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('6', '6.3.2(A)', 'Number of teachers provided with financial support to attend conferences / workshops and towards payment of membership fee of professional bodies during the year', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('6', '6.3.3', 'Number of professional development / administrative training programmes  organized by the Institution for its teaching and non-teaching staff during the year', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('6', '6.3.3(A)', 'Annual Reports highlighting training programs conducted for teaching & non teaching', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('6', '6.3.3(B)', 'Training programs conducted for teaching & non-teaching', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('6', '6.3.4', 'Number of teachers who have undergone online/ face-to-face Faculty Development  Programmes during the year: (Professional Development Programmes, Orientation / Induction Programmes,  Refresher Courses, Short-Term Course, etc.)', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('6', '6.3.4(A)', 'List of Faculty FDPS Attended & Proofs', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('6', '6.4.1', 'Institution conducts internal and external financial audits regularly', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('6', '6.4.2', 'Funds / Grants received from non-government bodies, individuals, and  philanthropists during the year (not covered in Criterion III and V) (INR in lakhs)', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('6', '6.4.3', 'Institutional strategies for mobilisation of funds and the optimal utilisation of  resources', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('6', '6.5.1', 'Internal Quality Assurance Cell (IQAC) has contributed significantly for  institutionalizing quality assurance strategies and processes visible in terms of  incremental improvements made during the preceding year with regard to quality (in  case of the First Cycle): Incremental improvements made during the preceding year with regard to quality  and post-accreditation quality initiatives (Second and subsequent cycles)', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('6', '6.5.1(A)', 'List of companies hosted Internship', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('6', '6.5.1(B)', 'FADS – Supporting Docs', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('6', '6.5.1(C)', 'Consolidated initiatives of IQAC (strategies & contributions of IQAC mentioned)', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('6', '6.5.2', 'The institution reviews its teaching-learning process, structures and methodologies  of operation and learning outcomes at periodic intervals through its IQAC as per  norms', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('6', '6.5.2(A)', 'List of the IQAC initiatives taken up to enhance the students’ performance (Course coordinator meetings, AMC meeting minutes, Remedial classes for slow learners)', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('6', '6.5.2(B)', 'Details of the progression of the students from 1st to 8th semesters branch wise for 2020-21 for all the batches graduated – 1 Bar chart, in each bar chart with eight bars', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('6', '6.5.2(C)', 'Structures & methodologies of operations ISO audits, Academic audits -Internal & External branch wise', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('6', '6.5.2(D)', 'Internal & External Academic Audit Report', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('6', '6.5.3', 'Quality assurance initiatives of the institution include', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('6', '6.5.3(A)', 'IQAC meeting minutes', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('6', '6.5.3(B)', 'Feedback system of the institution', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('6', '6.5.3(C)', 'Feedback system for design and review of syllabus', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('6', '6.5.3(D)', 'Collaborative quality initiatives', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('6', '6.5.3(E)', 'Participation in NIRF', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('6', '6.5.3(F)', 'NBA accreditation', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('6', '6.5.3(G)', 'NAAC accreditation', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('6', '6.5.3(H)', 'IQAC Feedback analysis', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('7', '7.1.1', 'Measures initiated by the institution for the promotion of gender equity during the  year', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('7', '7.1.1(A)', 'Action plan of WEC for gender sensitization', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('7', '7.1.1(B)', 'Campus surveillance with CC TV (Audit report)', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('7', '7.1.1(C)', 'Policy for women security and safety (PASH)', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('7', '7.1.1(D)', 'Photographs of Exclusive reading rooms, waiting rooms and rest rooms', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('7', '7.1.1(E)', 'Day care centre for the kids (Beneficiaries)', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('7', '7.1.1(F)', 'Welfare measures (Maternity leave for two kids)', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('7', '7.1.1(G)', 'List of the beneficiaries', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('7', '7.1.10', 'The institution has a prescribed code of conduct for students, teachers, administrators  and other staff and conducts periodic sensitization programmes in this regard', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('7', '7.1.10(A)', 'Details of the monitoring committee composition and minutes of the committee meeting, number of programmes organized, reports on the various programmes, etc. in support of the claims', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('7', '7.1.10(B)', 'Policy document on code of ethics', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('7', '7.1.10(C)', 'Circulars and geo tagged photographs and caption of the activities organized under the metric for teachers, students, administrators and other staffs', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('7', '7.1.11', 'Institution celebrates / organizes national and international commemorative days,  events and festivals', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('7', '7.1.11(A)', 'Annual report of the celebrations and commemorative events', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('7', '7.1.11(B)', 'Photographs of some of the events', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('7', '7.1.2', 'The Institution has facilities for alternate sources of energy and energy conservation ', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('7', '7.1.2(A)', 'Geo tagged photographs with caption of the facilities', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('7', '7.1.2(B)', 'Bills for the purchase of equipment for the facilities', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('7', '7.1.2(C)', 'Permission document for connection to the grid from Government', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('7', '7.1.3', 'Describe the facilities in the institution for the management of the following types of  degradable and non-degradable waste (within a maximum of 200 words)', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('7', '7.1.3(A)', 'Geo tagged photographs of the facilities', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('7', '7.1.3(B)', 'SOP for solid waste management', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('7', '7.1.4', 'Water conservation facilities available in the institution', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('7', '7.1.4(A)', 'Geo tagged photographs with caption of the facilities', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('7', '7.1.4(B)', 'Bills for the purchase of equipment', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('7', '7.1.5', 'Green campus initiatives include', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('7', '7.1.5(A)', 'Policy document on the green campus', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('7', '7.1.5(B)', 'Geo tagged photographs/Videos with caption of the facilities', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('7', '7.1.5(C)', 'Circulars for the implementation of the initiatives and any other supporting document', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('7', '7.1.6', 'Quality audits on environment and energy undertaken by the institution', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('7', '7.1.6(A)', 'Policy document on environment and energy usage', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('7', '7.1.6(B)', 'Certificate from the auditing agency', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('7', '7.1.6(C)', 'Certificates of the awards received from the recognized agency', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('7', '7.1.6(D)', 'Report on environmental promotional activities conducted beyond the campus', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('7', '7.1.6(F)', 'Green audit report', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('7', '7.1.7', 'The Institution has a Divyangjan-friendly and barrier-free environment', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('7', '7.1.7(A)', 'Policy document and information brochure', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('7', '7.1.7(B)', 'Geo tagged photos', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('7', '7.1.7(C)', 'Bills and invoice/purchase order/AMC', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('7', '7.1.7(D)', 'A rest room should include specific requirements of Divyangjan', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('7', '7.1.7(E)', 'Bills for the software procured', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('7', '7.1.8', 'Describe the Institutional efforts/initiatives in providing an inclusive environment i.e.  tolerance and harmony towards cultural, regional, linguistic, communal, socioeconomic and other diversities (within a maximum of 200 words)', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('7', '7.1.8(A)', 'List of the outbound programs', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('7', '7.1.8(B)', 'List of national level activities (Cultural, Sports, Academic, Cultural & Sports)', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('7', '7.1.8(C)', 'List of the faculty & students coming from the out of state', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('7', '7.1.9', 'Sensitization of students and employees of the institution to constitutional obligations:  values, rights, duties and responsibilities of citizens', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('7', '7.1.9(A)', 'List of the activities/Events conducted, and the number of students present. (Awareness programs on Women safety & protection, Anti ragging, Judicial rights, Gender equality, Traffic rules, Environment protection, Conservation of natural resources (power & water)) >', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('7', '7.2.1', 'Provide the weblink on the Institutional website regarding the Best practices as per  the prescribed format of NAAC', '2022-23');
INSERT INTO `nba_criteria1` VALUES ('7', '7.3.1', 'Institutional Distinctiveness', '2022-23');

DROP TABLE IF EXISTS `nba_criteria2`;
CREATE TABLE `nba_criteria2` (
  `SI_no` int(10) NOT NULL,
  `Sub_no` varchar(30) NOT NULL,
  `Des` varchar(600) NOT NULL,
  `year` varchar(200) NOT NULL,
  PRIMARY KEY (`Sub_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `nba_criteria2` VALUES ('1', '1.1.1', 'Copies of PEOs, POs & PSOs for all programs', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('1', '1.1.2', 'Number of Programmes where syllabus revision was carried out during the year(Data Requirement: Programme Code ,Names of the Programmes revised)', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('1', '1.1.2(A)', 'List of programs where syllabus revision has been carried out signed by the Principal', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('1', '1.1.2(B)', 'Approved Minutes of relevant Academic Council/BOS meetings highlighting the specific agenda item relevant to the metric year wise', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('1', '1.1.3', 'Number of courses focusing on employability/entrepreneurship/ skill development  offered by the Institution during the year(Data Requirement: Name of the Course with Course Code ,Name of the Programme ,Activities which have a direct bearing on employability/ entrepreneurship/ skill  development)', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('1', '1.1.3(A)', 'Syllabus copy of the courses highlighting Focus on employability/entrepreneurship/ skill development along with their course outcomes.', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('1', '1.1.3(B)', 'Minutes of the Boards of Studies/ Academic Council meetings with approval for these courses.', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('1', '1.1.3(C)', 'List of MoUs', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('1', '1.2.1', 'Number of new courses introduced across all programmes offered during the year(Data Requirement: Name of the newly introduced course (s)  ,Name of the Programme)', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('1', '1.2.1(A)', 'List of new courses introduced program-wise during the assessment period certified by the Principal.', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('1', '1.2.1(B)', 'Minutes of relevant Academic Council/BOS meetings highlighting the name of the new courses introduced', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('1', '1.2.2', 'Number of Programmes offered through Choice Based Credit System  (CBCS)/Elective Course System(Data Requirement: ,Names of all Programmes offered through CBCS ,Names of all Programmes offered through Elective Course System)', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('1', '1.2.2(A)', 'List of programs in which CBCS/Elective course system implemented in the last completed academic year certified by the principal', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('1', '1.2.2(B)', 'Structure of the program clearly indicating courses, credits/Electives and Minutes of relevant Academic Council/BOS meetings highlighting the relevant documents to this metric', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('1', '1.3.1', 'List and Description of courses relevant to Professional Ethics, Gender diversity and equality, Human Values, Environment and Sustainability, Women Empowerment introduced in the Curriculum along with the syllabus should be available in all the departments', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('1', '1.3.2', 'Number of value-added courses for imparting transferable and life skills offered  during the year:(Data Requirement:  Names of the value-added courses (each with 30 or more contact hours) ,No. of times offered (for each value-added course) during the year ,Total number of students enrolled ,Total number of students completing the course during the year', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('1', '1.3.2(A)', 'List of value-added courses which are optional and offered outside the curriculum of the programs with authorized sign.', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('1', '1.3.2(B)', 'Brochure and Course content or syllabus along with course outcome of Value-added courses offered.', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('1', '1.3.3', 'List of enrolled students for courses addressed in 1.3.2', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('1', '1.3.4', 'List of students undertaking the field projects/ internships / student projects program-wise in the last completed academic year along with the details of title, place of work etc.', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('1', '1.4.1', 'Sample Filled in feedback forms from the stakeholders to be provided.', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('1', '1.4.2', 'Stakeholder feedback analysis report signed by the authority', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('2', '2.1.1', 'Enrolment of Students ', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('2', '2.1.1(A)', 'Students admitted(HEI signed documents)', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('2', '2.1.1(B)', 'Enrolment Number_ AICTE letters of sanctioned intake', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('2', '2.1.1(C)', 'Enrolment Number _ Ratified list of admitted students', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('2', '2.1.2', 'Number of seats filled against reserved categories (SC, ST, OBC, Divyangjan,  etc.) as per the reservation policy during the year (exclusive of supernumerary  seats)', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('2', '2.1.2(A)', 'Reservation seats categories to be considered as per the state rule (APSHE Guidelines)', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('2', '2.1.2(B)', 'AP regulation for Admissions GO No. 73', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('2', '2.1.2(C)', 'Guidelines for filling the left-over vacancies under reservation', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('2', '2.1.2(D)', 'Category wise_ Ratified list of students admitted', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('2', '2.1.2(E)', 'Admission Abstract(HEI signed documents)', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('2', '2.2.1', 'The institution assesses students’ learning levels and organises special  programmes for both slow and advanced learners(Present a write-up within a maximum of 200 words)', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('2', '2.2.1(A)', 'Video records of the specific topics(A document having the links and screen shots of video course/LCS)', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('2', '2.2.1(B)', 'Classes for the slow learner(Time table conducted for CA failures and attendance sheets)', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('2', '2.2.1(C)', 'Additional assignment(sample copies of either question papers or evaluated assignment)', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('2', '2.2.1(D)', 'Make-up classes for course detention students/lateral entry students(Time tables and attendance sheets for each semester(1-8) at least one)', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('2', '2.2.1(E)', 'Remedial classes conducted for end semester failures(Time tables, attendance sheets and track sheets for each semester(1-8) at least one)', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('2', '2.2.1(F)', 'List of MOOCs courses(Details of student with course name and sample certificates)', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('2', '2.2.1(G)', 'List of the advanced learners opted Minors and honors(Abstract certified by controller of examination)', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('2', '2.2.1(H)', 'Student participation in technical events list with students event details and SAMPLE certificates', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('2', '2.2.2', 'Student – Teacher (full-time) ratio(Data Requirement:  Total number of students in the institution ,Total number of full-time teachers in the institution)', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('2', '2.2.2(A)', 'List of the full-time teachers', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('2', '2.2.2(B)', 'Student intake', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('2', '2.3.1', 'Student-centric methods such as experiential learning, participative learning and  problem-solving methodologies are used for enhancing learning experiences(Present a write-up within a maximum of 200 words.)', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('2', '2.3.1(A)', 'Academic regulations and curriculum(Recent)', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('2', '2.3.2', 'Teachers use ICT-enabled tools including online resources for effective teaching  and learning(Present a write-up within a maximum of 200 words.)', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('2', '2.3.2 (A)', 'ICT Tools Gadgets viz. Graphic tablets, MST, Smart pen, Projector(Geotagged photos with title of photo)', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('2', '2.3.3', 'Ratio of students to mentor for academic and other related issues', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('2', '2.3.3(A)', 'Circular of mentor – mentees', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('2', '2.3.3(B)', 'Mentor list as announced by the HEI', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('2', '2.3.3(C)', 'issues raised and resolved in the mentor system(Total records of a student -&gt; one sample from each semester)', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('2', '2.3.4', 'Preparation and adherence to Academic Calendar and Teaching Plans by the  institution(Describe the preparation of and adherence to the Academic Calendar and Teaching  Plans by the institution.  Present a write-up within a maximum of 200 words)', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('2', '2.3.4(A)', 'Academic calendar', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('2', '2.3.4(B)', 'Proof of course allotment for odd and even semesters', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('2', '2.3.4(C)', 'Proof for lecture plan, Schedule and diary(one course from each semester)', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('2', '2.3.4(D)', 'Minutes of AMC', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('2', '2.4.1', 'Number of full-time teachers against sanctioned posts during the year(Data Requirement:  Number of full-time teachers ,Number of sanctioned posts)', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('2', '2.4.1(A)', 'Sanction letter indicating number of posts', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('2', '2.4.1(B)', 'Department wise List of full-time teachers appointed', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('2', '2.4.2', 'Number of full-time teachers with PhD/ D.M. / M.Ch. / D.N.B Super-Specialty /  DSc / DLitt during the year(Data Requirement: List of full-time teachers with PhD/ D.M. / M.Ch. / D.N.B Super-Specialty /  DSc / DLitt.)', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('2', '2.4.3', 'Total teaching experience of full-time teachers in the same institution(Data Requirement:  Name and number of full-time teachers and their years of teaching experience in the institution)', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('2', '2.5.1', 'Number of days from the date of last semester-end/ year- end examination till the  declaration of results during the year(Number of days from the date of last semester-end / year-end examination till the  declaration of results year-wise during the year)', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('2', '2.5.1(A)', 'Examination Result Notifications', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('2', '2.5.1(B)', 'List of Programs offered and the last date of the latest semester end exams and date of result declaration', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('2', '2.5.2', 'Number of students’ complaints/grievances against evaluation against the total  number who appeared in the examinations during the year', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('2', '2.5.2(A)', 'Number of complaints', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('2', '2.5.2(B)', 'Minutes of the examination Committee', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('2', '2.5.3', 'IT integration and reforms in the examination procedures and processes  including Continuous Internal Assessment (CIA) have brought in considerable  improvement in the Examination Management System (EMS) of the Institution(Describe the examination reforms with reference to the following within a minimum  of 200 words :Examination procedures ,Processes/Procedures integrating IT ,Continuous Internal Assessment System) ', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('2', '2.5.3(A)', 'Examination Regulations', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('2', '2.5.3(B)', 'Question Paper templates', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('2', '2.5.3(C)', 'Proof of the hybrid grading sheet', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('2', '2.5.3(D)', 'Examination Results link in the website', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('2', '2.5.3(E)', 'Proof of the OMR', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('2', '2.6.1', 'Programme Outcomes and Course Outcomes for all Programmes offered by the  institution are stated and displayed on the website and communicated to teachers  and students(Describe Course Outcomes (COs) for all courses and the mechanism of  communication to teachers and students within a maximum of 200 words.  Upload COs for all Courses) ', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('2', '2.6.1(A)', 'List of POs and COs(COs for at least two courses from each semester)', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('2', '2.6.1(B)', 'Photo gallery of the COs & POs displayed Program wise', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('2', '2.6.2', 'Attainment of Programme Outcomes and Course Outcomes as evaluated by the  institution(Describe the method of measuring the attainment of POs, PSOs and COs and the  level of attaiment of POs , PSOs and COs in not more than 200 words.)', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('2', '2.6.2(A)', 'CO and PO attainment', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('2', '2.6.2(B)', 'Files related to all surveys for the indirect assessment', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('2', '2.6.3', 'Pass Percentage of students', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('2', '2.6.3(A)', 'Annual report of CoE indicating the pass percentage', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('2', '2.6.3(B)', 'Certified report from CoE indicating Students eligible for the degree program', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('2', '2.7.1', 'Student Satisfaction Survey (SSS) on overall institutional performance  (Institution may design its own questionnaire). Results and details need to be  provided as a weblink', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('3', '3.1.1', 'The institution’s research facilities are frequently updated and there is a welldefined policy for promotion of research which is uploaded on the institutional  website and implemented(Present a write-up within a maximum of 200 words.)', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('3', '3.1.1(A)', 'List of research equipment along with the proof of purchases (Bill copies)', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('3', '3.1.1(B)', 'Policy for Faculty Assessment and Development Scheme', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('3', '3.1.1(C)', 'Financial Incentives for attending conferences, seminars and QIP', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('3', '3.1.1(D)', 'Sanction letters for the funded research projects & UCs for the projects completed', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('3', '3.1.1(E)', 'Copies of all MoUs for collaborative research', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('3', '3.1.1(F)', 'Minutes of the governing council meeting-reflecting the research promotions', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('3', '3.1.2', 'Details of Seed money', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('3', '3.1.3', 'Number of teachers who were awarded national / international fellowship(s) for  advanced studies/research during the year', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('3', '3.2.1', 'Grants received from Government and Non-Governmental agencies for research  projects, endowments, Chairs during the year (INR in Lakhs)', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('3', '3.2.1(A)', 'List of Grants received for research projects', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('3', '3.2.1(B)', 'e-copies of grants sanctioned', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('3', '3.2.2', 'List of teachers having research projects during the year', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('3', '3.2.3', 'Number of teachers recognized as research guides', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('3', '3.2.4', 'Number of departments having research projects', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('3', '3.2.4(A)', 'Web Links to Funding Agencies', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('3', '3.3.1', 'Institution has created an ecosystem for innovations and creation and transfer of  knowledge supported by dedicated centres for research, entrepreneurship,  community orientation, incubation, etc.(Present a write-up within a maximum of 200 words)', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('3', '3.3.1(A)', 'MSME business incubation center – sanction letter', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('3', '3.3.1(B)', 'AICTE sponsored EDC – Sanction letters and list of the activities conducted by EDC', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('3', '3.3.1(C)', 'Dedicated centres for research', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('3', '3.3.2', 'Detailed report including photos, resource persons etc.', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('3', '3.4.1', 'The Institution ensures implementation of its Code of Ethics for Research  uploaded in the website through the following:  1. Research Advisory Committee 2. Ethics Committee 3. Inclusion of Research Ethics in the research methodology course work  4. Plagiarism check through authenticated software Options: A. All of the above B. Any 3 of the above C. Any 2 of the above D. Any 1 of the above E. None of the above', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('3', '3.4.1(A)', 'Research Advisory Committee', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('3', '3.4.1(B)', 'Ethics Committee', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('3', '3.4.1(C)', 'Inclusion of Research Ethics in the research methodology course work', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('3', '3.4.1(D)', 'Anti Plagiarism software approved by JNTU', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('3', '3.4.2', 'Number of PhD candidates registered per teacher (as per the data given with  regard to recognized PhD guides/ supervisors provided in Metric No. 3.2.3) during  the year', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('3', '3.4.2(A)', 'List of Faculty along with the names of Research scholars', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('3', '3.4.2(B)', 'Copy of the Registration letters/Joining letters', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('3', '3.4.3', 'Number of research papers per teacher in CARE Journals notified on UGC  website during the year', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('3', '3.4.4', 'Number of books and chapters in edited volumes / books published per teacher  during the year', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('3', '3.4.5', 'Bibliometrics of the publications based on average Citation Index', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('3', '3.4.6', 'Bibliometrics of the publication-based h-Index of the University', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('3', '3.5.1', 'Audited statements for Revenue generated from consultancy', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('3', '3.5.2', 'Total amount spent on developing facilities, training teachers and clerical/project  staff for undertaking consultancy during the year', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('3', '3.6.1', 'Extension activities carried out in the neighbourhood sensitising students to social  issues for their holistic development, and the impact thereof during the year', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('3', '3.6.1(A)', 'List of NSS activities conducted year wise and number of students participated', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('3', '3.6.1(B)', 'Covid-19 booster report', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('3', '3.6.1(C)', 'MGNCRE Reports', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('3', '3.6.1(D)', 'Community Radio report', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('3', '3.6.1(E)', 'List of NCC activities conducted year wise and number of students participated', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('3', '3.6.2', 'Number of awards and recognition received by the Institution, its teachers and  students for extension activities from Government / Government-recognised bodies', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('3', '3.6.3', 'Number of extension and outreach programmes conducted by the institution through  NSS/NCC during the year', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('3', '3.6.4', 'Number of students participating in extension activities listed in 3.6.3 during the  year', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('3', '3.7.1', 'Number of collaborative activities during the year for research/ faculty exchange/  student exchange/ internship/ on-the-job training/ project work', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('3', '3.7.2', 'Number of functional MoUs with institutions of national and/or international  importance, other universities, industries, corporate houses, etc. during the year  (only functional MoUs with ongoing activities to be considered)', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('3', '3.7.2(A)', 'e-copies of functional MoUs', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('3', '3.7.2(B)', 'e-copies of Activities of MOUs', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('4', '4.1.1', 'The Institution has adequate infrastructure and physical facilities for teachinglearning, viz., classrooms, laboratories, computing equipments, etc.', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('4', '4.1.1(A)', 'Details of the Classrooms, Labs & Other facilities across all the six blocks', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('4', '4.1.1(B)', 'List of the laboratories with titles', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('4', '4.1.1(C)', 'Campus LAN diagram', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('4', '4.1.1(D)', 'Proof of bandwidth', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('4', '4.1.1(E)', 'List of the software', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('4', '4.1.1(F)', 'Photo gallery of all the academic blocks, classrooms, drawing hall, seminar hall, auditorium, and Labs', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('4', '4.1.2', 'The institution has adequate facilities for cultural activities, yoga, sports and games  (indoor and outdoor) including gymnasium, yoga centre, auditorium etc.)', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('4', '4.1.2(A)', 'Colleague of Geo-tagged pictures', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('4', '4.1.2(B)', 'Area details of the all the facilities', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('4', '4.1.2(C)', 'Photo gallery of the various activities', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('4', '4.1.3', 'Number of classrooms and seminar halls with ICT-enabled facilities', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('4', '4.1.3(A)', 'Geo-tagged photographs of classrooms with ICT enabled facilities', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('4', '4.1.3(B)', 'Class Timetables', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('4', '4.1.4', 'Expenditure for infrastructure augmentation, excluding salary, during the year (INR  in Lakhs)', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('4', '4.1.4(A)', 'Budget allocation', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('4', '4.1.4(B)', 'Provide the consolidated fund allocation towards infrastructure augmentation facilities duly certified by Head of the Institution', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('4', '4.2.1', 'Library is automated using Integrated Library Management System (ILMS)', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('4', '4.2.1(A)', 'Library operates through LIBSYS', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('4', '4.2.1(B)', 'All the books are provided with RF Id security tags', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('4', '4.2.1(C)', 'Copy of the latest License agreement of LIBSYS-7', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('4', '4.2.1(D)', 'Digital Library', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('4', '4.2.2', 'Institution has access to the following', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('4', '4.2.2 (D)', 'Specific details in respect of e-resources selected.', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('4', '4.2.2 (E)', 'Databases', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('4', '4.2.2(A)', 'Details of subscriptions of e-journals', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('4', '4.2.2(B)', 'Letter of subscription', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('4', '4.2.2(C)', 'Screenshots of the facilities claimed with the name of HEI.', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('4', '4.2.3', 'Expenditure on purchase of books/ e-books and subscription to journals/e-journals  during the year (INR in lakhs)', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('4', '4.2.3(A)', 'Consolidated extract of expenditure', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('4', '4.2.3(B)', 'Invoices of all the expenditure', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('4', '4.2.4', 'Usage of library by teachers and students (footfalls and login data for online access)', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('4', '4.2.4(A)', 'Certified e-copy of the ledger for footfalls for 5days', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('4', '4.2.4(B)', 'Certified screenshots of the data for the same 5 days for online access', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('4', '4.2.4(C)', 'Last page of accession register details', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('4', '4.3.1', 'Institution has an IT policy covering Wi-Fi, cyber security, etc. and has allocated  budget for updating its IT facilities', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('4', '4.3.1(A)', 'Colleague of Geo-tagged pictures', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('4', '4.3.1(B)', 'Policy document', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('4', '4.3.1(C)', 'Campus Wi-Fi/Network diagram', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('4', '4.3.1(D)', 'Budget of the year 2020-21', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('4', '4.3.2', 'Student - Computer ratio', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('4', '4.3.2(A)', 'Computer Bills', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('4', '4.3.2(B)', 'Student strength', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('4', '4.3.3', 'Bandwidth of internet connection in the Institution and the number of students on  campus', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('4', '4.3.3(A)', 'Details of available bandwidth of internet connection in the Institution', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('4', '4.3.3(B)', 'Bills for any one month/one quarter.', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('4', '4.3.3(C)', 'e-copy of document of agreement with the service provider.', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('4', '4.3.4', 'Institution has facilities for e-content development', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('4', '4.3.4(A)', 'Geo tagged photographs of Media Centre, Audio Visual Centre etc.,', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('4', '4.3.4(B)', 'Purchase bills for Lecture Capturing System', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('4', '4.3.4(C)', 'Audited income expenditure statement highlighting the relevant expenditure.', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('4', '4.4.1', 'Expenditure incurred on maintenance of physical and academic support facilities, excluding salary component, during the year (INR in lakhs)', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('4', '4.4.1(A)', 'Audited statements of accounts.', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('4', '4.4.2', 'There are established systems and procedures for maintaining and utilizing  physical, academic and support facilities – classrooms, laboratory, library, sports  complex, computers, etc.', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('4', '4.4.2(A)', 'Schedules of Library, Sport complex ', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('4', '4.4.2(B)', 'Laboratory Timetables', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('4', '4.4.2(C)', 'SOP for Laboratory utilization', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('4', '4.4.2(D)', 'SOP for usage of general amenities', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('4', '4.4.2(E)', 'Geo-tagged photos of GAMYA', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('4', '4.4.2(F)', 'Maintenance schedules and AMC letters from Estate department', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('5', '5.1.1', 'Number of students benefitted by scholarships and freeships provided by the  Government during the year', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('5', '5.1.2', 'Number of students benefitted by scholarships and freeships provided by the  institution and non-government agencies during the year', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('5', '5.1.3', 'The following Capacity Development and Skill Enhancement activities are  organised for improving students’ capabilities', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('5', '5.1.3(A)', 'Soft Skill, Language and Communication', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('5', '5.1.3(B)', 'Yoga Class', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('5', '5.1.3(C)', 'Awareness of Trends in technology', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('5', '5.1.4', 'Number of students benefitted from guidance/coaching for competitive  examinations and career counselling offered by the institution during the year', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('5', '5.1.5', 'The institution adopts the following mechanism for redressal of students’  grievances, including sexual harassment and ragging', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('5', '5.2.1', 'Number of outgoing students who got placement during the year', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('5', '5.2.2', 'Number of outgoing students progressing to higher education during the year', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('5', '5.2.3', 'Number of students qualifying in state/ national/ international level examinations  during the year', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('5', '5.3.1', 'Number of awards/medals for outstanding performance in sports and/or cultural  activities at inter-university / state /national / international events (award for a team  event should be counted as one) during the year', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('5', '5.3.2', 'Presence of an active Student Council and representation of students in academic  and administrative bodies/committees of the institution', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('5', '5.3.3', 'Number of sports and cultural events / competitions organised by the institution', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('5', '5.4.1', 'The Alumni Association and its Chapters (registered and functional) contribute  significantly to the development of the institution through financial and other  support services', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('5', '5.4.2', 'Alumni’s financial contribution during the year', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('6', '6.1.1', 'The governance of the institution is reflective of an effective leadership in tune with  the vision and mission of the Institution', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('6', '6.1.1(A)', 'Academic Monitoring Committee meeting minutes', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('6', '6.1.1(B)', 'Placement Committee meeting minutes', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('6', '6.1.1(C)', 'SAC - coordinators meeting minutes', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('6', '6.1.1(D)', 'Anti Ragging Committee meeting minutes', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('6', '6.1.1(E)', 'IQAC meeting minutes', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('6', '6.1.1(F)', 'Board of Studies Meeting minutes', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('6', '6.1.1(G)', 'Academic Council meeting minutes', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('6', '6.1.1(H)', 'Governing Council meeting minutes', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('6', '6.1.1(I)', 'HOD/Academic Development Committee Meeting Minutes', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('6', '6.1.1(J)', 'Finance Committee meeting minutes', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('6', '6.1.1(K)', 'Library Committee meeting minutes', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('6', '6.1.1(L)', 'Town Hall meeting minutes', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('6', '6.1.2', 'Effective leadership is reflected in various institutional practices such as decentralization and participative management', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('6', '6.1.2(A)', 'Strat-Plan', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('6', '6.1.2(B)', 'Requisition for two set of mid question papers from CoE – e-mail proof', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('6', '6.1.2(C)', 'Declaration of mid question paper set number from CoE– e-mail proof', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('6', '6.1.2(D)', 'Uniform Evaluation', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('6', '6.1.2(E)', 'Research Review meeting minutes', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('6', '6.2.1', 'The institutional Strategic/ Perspective plan has been clearly articulated and  implemented', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('6', '6.2.1(A)', 'Strat-Plan', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('6', '6.2.1(B)', 'Merit scholarships based on AP-EAMCET rank', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('6', '6.2.1(C)', 'Meritorious scholarships for students', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('6', '6.2.1(D)', 'Details of the GATE training classes conducted; List of students attended GATE Coaching & secured score', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('6', '6.2.1(E)', 'Details of the CRT programs/ Technical training/Competitions conducted (Total number of hours), the List of the students attended the training programs & Proof of attendance, Branch wise list of the students placed, and the number of companies visited. List of students attended the WTN.', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('6', '6.2.1(F)', 'Motivational and inspirational talks by the industry experts', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('6', '6.2.1(G)', 'Social media updates', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('6', '6.2.2', 'The functioning of the various institutional bodies is effective and efficient as visible  from the policies, administrative set-up, appointment and service rules, procedures,  etc. ', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('6', '6.2.2(A)', 'Organogram', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('6', '6.2.2(B)', 'HR & Service rules/Incentive policies', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('6', '6.2.2(D)', 'Frequency and conduct of the meetings of governance committees (GC, AC, BOS, FC)', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('6', '6.2.2(E)', 'SOP for procurement (AOP, MRN, Comparative statements and Purchase Orders)', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('6', '6.2.3', 'Implementation of e-governance in areas of operation: 1. Administration 2. Finance and Accounts 3. Student Admission and Support 4. Examination', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('6', '6.3.1', 'The institution has effective welfare measures for teaching and non-teaching staff  and avenues for their career development/ progression', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('6', '6.3.1(A)', 'HR Policies (Welfare & Career development)', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('6', '6.3.1(B)', 'Details of the training programs conducted for teaching & non-teaching staff', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('6', '6.3.1(C)', 'Details of the staff (Teaching & Non-teaching promoted)', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('6', '6.3.1(D)', 'Details of on campus housing, Term insurance, Medical insurance, Children education, ESI, Cooperative credit society, Concessions in IP/OP services and Gratuity', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('6', '6.3.1(E)', 'Details of the faculty received incentives for completion of Ph.D./ QIP', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('6', '6.3.2', 'Number of teachers provided with financial support to attend conferences /  workshops and towards payment of membership fee of professional bodies during the  year', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('6', '6.3.2(A)', 'Number of teachers provided with financial support to attend conferences / workshops and towards payment of membership fee of professional bodies during the year', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('6', '6.3.3', 'Number of professional development / administrative training programmes  organized by the Institution for its teaching and non-teaching staff during the year', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('6', '6.3.3(A)', 'Annual Reports highlighting training programs conducted for teaching & non teaching', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('6', '6.3.3(B)', 'Training programs conducted for teaching & non-teaching', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('6', '6.3.4', 'Number of teachers who have undergone online/ face-to-face Faculty Development  Programmes during the year: (Professional Development Programmes, Orientation / Induction Programmes,  Refresher Courses, Short-Term Course, etc.)', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('6', '6.3.4(A)', 'List of Faculty FDPS Attended & Proofs', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('6', '6.4.1', 'Institution conducts internal and external financial audits regularly', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('6', '6.4.2', 'Funds / Grants received from non-government bodies, individuals, and  philanthropists during the year (not covered in Criterion III and V) (INR in lakhs)', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('6', '6.4.3', 'Institutional strategies for mobilisation of funds and the optimal utilisation of  resources', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('6', '6.5.1', 'Internal Quality Assurance Cell (IQAC) has contributed significantly for  institutionalizing quality assurance strategies and processes visible in terms of  incremental improvements made during the preceding year with regard to quality (in  case of the First Cycle): Incremental improvements made during the preceding year with regard to quality  and post-accreditation quality initiatives (Second and subsequent cycles)', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('6', '6.5.1(A)', 'List of companies hosted Internship', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('6', '6.5.1(B)', 'FADS – Supporting Docs', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('6', '6.5.1(C)', 'Consolidated initiatives of IQAC (strategies & contributions of IQAC mentioned)', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('6', '6.5.2', 'The institution reviews its teaching-learning process, structures and methodologies  of operation and learning outcomes at periodic intervals through its IQAC as per  norms', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('6', '6.5.2(A)', 'List of the IQAC initiatives taken up to enhance the students’ performance (Course coordinator meetings, AMC meeting minutes, Remedial classes for slow learners)', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('6', '6.5.2(B)', 'Details of the progression of the students from 1st to 8th semesters branch wise for 2020-21 for all the batches graduated – 1 Bar chart, in each bar chart with eight bars', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('6', '6.5.2(C)', 'Structures & methodologies of operations ISO audits, Academic audits -Internal & External branch wise', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('6', '6.5.2(D)', 'Internal & External Academic Audit Report', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('6', '6.5.3', 'Quality assurance initiatives of the institution include', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('6', '6.5.3(A)', 'IQAC meeting minutes', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('6', '6.5.3(B)', 'Feedback system of the institution', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('6', '6.5.3(C)', 'Feedback system for design and review of syllabus', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('6', '6.5.3(D)', 'Collaborative quality initiatives', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('6', '6.5.3(E)', 'Participation in NIRF', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('6', '6.5.3(F)', 'NBA accreditation', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('6', '6.5.3(G)', 'NAAC accreditation', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('6', '6.5.3(H)', 'IQAC Feedback analysis', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('7', '7.1.1', 'Measures initiated by the institution for the promotion of gender equity during the  year', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('7', '7.1.1(A)', 'Action plan of WEC for gender sensitization', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('7', '7.1.1(B)', 'Campus surveillance with CC TV (Audit report)', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('7', '7.1.1(C)', 'Policy for women security and safety (PASH)', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('7', '7.1.1(D)', 'Photographs of Exclusive reading rooms, waiting rooms and rest rooms', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('7', '7.1.1(E)', 'Day care centre for the kids (Beneficiaries)', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('7', '7.1.1(F)', 'Welfare measures (Maternity leave for two kids)', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('7', '7.1.1(G)', 'List of the beneficiaries', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('7', '7.1.10', 'The institution has a prescribed code of conduct for students, teachers, administrators  and other staff and conducts periodic sensitization programmes in this regard', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('7', '7.1.10(A)', 'Details of the monitoring committee composition and minutes of the committee meeting, number of programmes organized, reports on the various programmes, etc. in support of the claims', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('7', '7.1.10(B)', 'Policy document on code of ethics', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('7', '7.1.10(C)', 'Circulars and geo tagged photographs and caption of the activities organized under the metric for teachers, students, administrators and other staffs', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('7', '7.1.11', 'Institution celebrates / organizes national and international commemorative days,  events and festivals', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('7', '7.1.11(A)', 'Annual report of the celebrations and commemorative events', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('7', '7.1.11(B)', 'Photographs of some of the events', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('7', '7.1.2', 'The Institution has facilities for alternate sources of energy and energy conservation ', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('7', '7.1.2(A)', 'Geo tagged photographs with caption of the facilities', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('7', '7.1.2(B)', 'Bills for the purchase of equipment for the facilities', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('7', '7.1.2(C)', 'Permission document for connection to the grid from Government', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('7', '7.1.3', 'Describe the facilities in the institution for the management of the following types of  degradable and non-degradable waste (within a maximum of 200 words)', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('7', '7.1.3(A)', 'Geo tagged photographs of the facilities', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('7', '7.1.3(B)', 'SOP for solid waste management', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('7', '7.1.4', 'Water conservation facilities available in the institution', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('7', '7.1.4(A)', 'Geo tagged photographs with caption of the facilities', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('7', '7.1.4(B)', 'Bills for the purchase of equipment', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('7', '7.1.5', 'Green campus initiatives include', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('7', '7.1.5(A)', 'Policy document on the green campus', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('7', '7.1.5(B)', 'Geo tagged photographs/Videos with caption of the facilities', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('7', '7.1.5(C)', 'Circulars for the implementation of the initiatives and any other supporting document', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('7', '7.1.6', 'Quality audits on environment and energy undertaken by the institution', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('7', '7.1.6(A)', 'Policy document on environment and energy usage', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('7', '7.1.6(B)', 'Certificate from the auditing agency', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('7', '7.1.6(C)', 'Certificates of the awards received from the recognized agency', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('7', '7.1.6(D)', 'Report on environmental promotional activities conducted beyond the campus', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('7', '7.1.6(F)', 'Green audit report', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('7', '7.1.7', 'The Institution has a Divyangjan-friendly and barrier-free environment', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('7', '7.1.7(A)', 'Policy document and information brochure', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('7', '7.1.7(B)', 'Geo tagged photos', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('7', '7.1.7(C)', 'Bills and invoice/purchase order/AMC', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('7', '7.1.7(D)', 'A rest room should include specific requirements of Divyangjan', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('7', '7.1.7(E)', 'Bills for the software procured', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('7', '7.1.8', 'Describe the Institutional efforts/initiatives in providing an inclusive environment i.e.  tolerance and harmony towards cultural, regional, linguistic, communal, socioeconomic and other diversities (within a maximum of 200 words)', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('7', '7.1.8(A)', 'List of the outbound programs', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('7', '7.1.8(B)', 'List of national level activities (Cultural, Sports, Academic, Cultural & Sports)', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('7', '7.1.8(C)', 'List of the faculty & students coming from the out of state', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('7', '7.1.9', 'Sensitization of students and employees of the institution to constitutional obligations:  values, rights, duties and responsibilities of citizens', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('7', '7.1.9(A)', 'List of the activities/Events conducted, and the number of students present. (Awareness programs on Women safety & protection, Anti ragging, Judicial rights, Gender equality, Traffic rules, Environment protection, Conservation of natural resources (power & water)) >', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('7', '7.2.1', 'Provide the weblink on the Institutional website regarding the Best practices as per  the prescribed format of NAAC', '2022-23');
INSERT INTO `nba_criteria2` VALUES ('7', '7.3.1', 'Institutional Distinctiveness', '2022-23');

DROP TABLE IF EXISTS `patents_table`;
CREATE TABLE `patents_table` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `Username` varchar(300) NOT NULL,
  `branch` varchar(100) NOT NULL,
  `patent_title` varchar(300) NOT NULL,
  `patent_no` varchar(255) DEFAULT NULL,
  `type` varchar(100) DEFAULT NULL,
  `date_of_issue` varchar(100) NOT NULL,
  `investors` text DEFAULT NULL,
  `patent_file` varchar(400) NOT NULL,
  `submission_time` varchar(300) NOT NULL,
  `year` varchar(255) NOT NULL,
  `status` varchar(50) DEFAULT 'Pending HOD',
  `rejection_reason` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `patents_table` VALUES ('1', 'cseuser', 'CSE', 'csepatent', NULL, 'published', '2025-01-01', NULL, 'uploads/patents/patent.pdf', '2026-08-12 07:42:41', '2024-25', 'Accepted', NULL);

DROP TABLE IF EXISTS `published_tab`;
CREATE TABLE `published_tab` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `username` varchar(200) NOT NULL,
  `branch` varchar(100) NOT NULL,
  `paper_title` varchar(200) NOT NULL,
  `journal_name` varchar(200) NOT NULL,
  `authors` text DEFAULT NULL,
  `issn_no` varchar(255) DEFAULT NULL,
  `volume_no` varchar(255) DEFAULT NULL,
  `issue_no` varchar(255) DEFAULT NULL,
  `page_no` varchar(255) DEFAULT NULL,
  `doi` varchar(255) DEFAULT NULL,
  `jcr_quartile` varchar(255) DEFAULT NULL,
  `scopus_quartile` varchar(255) DEFAULT NULL,
  `publication_link` varchar(255) DEFAULT NULL,
  `indexing` varchar(100) NOT NULL,
  `date_of_submission` date NOT NULL,
  `quality_factor` decimal(10,0) NOT NULL,
  `impact_factor` decimal(10,0) NOT NULL,
  `payment` varchar(200) NOT NULL,
  `submission_time` varchar(300) NOT NULL,
  `paper_file` varchar(400) NOT NULL,
  `year` varchar(255) NOT NULL,
  `status` varchar(50) DEFAULT 'Pending HOD',
  `rejection_reason` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `published_tab` VALUES ('1', 'cseuser', 'CSE', 'cseres', 'jounal1', '[{\"name\":\"cseuser\",\"affiliation\":\"gmrit\",\"position\":\"First author\"}]', '1', '1', '1', '1', '10/12/2024', 'Q1', 'Q1', 'https://researchpaper.com', 'SCI', '0000-00-00', '0', '1', '', '2026-08-12 07:40:16', 'uploads/paper_6a7bd608069f4.pdf', '2024-25', 'Accepted', NULL);

DROP TABLE IF EXISTS `reg_central_cord`;
CREATE TABLE `reg_central_cord` (
  `userid` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `reg_central_cord` VALUES ('central_cord', '123', NULL);
INSERT INTO `reg_central_cord` VALUES ('central_cord1', '123', NULL);
INSERT INTO `reg_central_cord` VALUES ('central_cord2', '123', NULL);
INSERT INTO `reg_central_cord` VALUES ('central_cord3', '123', NULL);
INSERT INTO `reg_central_cord` VALUES ('central_cord4', '123', NULL);
INSERT INTO `reg_central_cord` VALUES ('central_cord5', '123', NULL);
INSERT INTO `reg_central_cord` VALUES ('central_cord6', '123', NULL);
INSERT INTO `reg_central_cord` VALUES ('central_cord7', '123', NULL);
INSERT INTO `reg_central_cord` VALUES ('central_cord8', '123', NULL);
INSERT INTO `reg_central_cord` VALUES ('central_cord9', '123', NULL);
INSERT INTO `reg_central_cord` VALUES ('central_cord10', '123', NULL);

DROP TABLE IF EXISTS `reg_cri_cord`;
CREATE TABLE `reg_cri_cord` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `userid` (`userid`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `reg_cri_cord` VALUES ('1', 'cri_cord', '123', NULL);
INSERT INTO `reg_cri_cord` VALUES ('2', 'cri_cord1', '123', NULL);
INSERT INTO `reg_cri_cord` VALUES ('3', 'cri_cord2', '123', NULL);
INSERT INTO `reg_cri_cord` VALUES ('4', 'cri_cord3', '123', NULL);
INSERT INTO `reg_cri_cord` VALUES ('5', 'cri_cord4', '123', NULL);
INSERT INTO `reg_cri_cord` VALUES ('6', 'cri_cord5', '123', NULL);
INSERT INTO `reg_cri_cord` VALUES ('7', 'cri_cord6', '123', NULL);
INSERT INTO `reg_cri_cord` VALUES ('8', 'cri_cord7', '123', NULL);
INSERT INTO `reg_cri_cord` VALUES ('9', 'cri_cord8', '123', NULL);
INSERT INTO `reg_cri_cord` VALUES ('10', 'cri_cord9', '123', NULL);
INSERT INTO `reg_cri_cord` VALUES ('11', 'cri_cord10', '123', NULL);

DROP TABLE IF EXISTS `reg_dept_cord`;
CREATE TABLE `reg_dept_cord` (
  `userid` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `department` varchar(50) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `reg_dept_cord` VALUES ('dept_cord1', '123', 'CSE', NULL);
INSERT INTO `reg_dept_cord` VALUES ('dept_cord2', '123', 'AIML', NULL);
INSERT INTO `reg_dept_cord` VALUES ('dept_cord3', '123', 'AIDS', NULL);
INSERT INTO `reg_dept_cord` VALUES ('dept_cord4', '123', 'IT', NULL);
INSERT INTO `reg_dept_cord` VALUES ('dept_cord5', '123', 'ECE', NULL);
INSERT INTO `reg_dept_cord` VALUES ('dept_cord6', '123', 'EEE', NULL);
INSERT INTO `reg_dept_cord` VALUES ('dept_cord7', '123', 'MECH', NULL);
INSERT INTO `reg_dept_cord` VALUES ('dept_cord8', '123', 'CIVIL', NULL);
INSERT INTO `reg_dept_cord` VALUES ('dept_cord9', '123', 'BSH', NULL);

DROP TABLE IF EXISTS `reg_hod`;
CREATE TABLE `reg_hod` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `department` varchar(100) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `reg_hod` VALUES ('1', 'cse-hod', '123', 'CSE', NULL);
INSERT INTO `reg_hod` VALUES ('2', 'aiml-hod', '123', 'AIML', NULL);
INSERT INTO `reg_hod` VALUES ('3', 'aids-hod', '123', 'AIDS', NULL);
INSERT INTO `reg_hod` VALUES ('4', 'ece-hod', '123', 'ECE', NULL);
INSERT INTO `reg_hod` VALUES ('5', 'eee-hod', '123', 'EEE', NULL);
INSERT INTO `reg_hod` VALUES ('6', 'mech-hod', '123', 'MECH', NULL);
INSERT INTO `reg_hod` VALUES ('7', 'civil-hod', '123', 'CIVIL', NULL);
INSERT INTO `reg_hod` VALUES ('8', 'it-hod', '123', 'IT', NULL);

DROP TABLE IF EXISTS `reg_jr_assistant`;
CREATE TABLE `reg_jr_assistant` (
  `userid` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `department` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `reg_jr_assistant` VALUES ('jr1', 'cse-jr', 'CSE', '123', '2026-02-26 10:45:52');

DROP TABLE IF EXISTS `reg_pg`;
CREATE TABLE `reg_pg` (
  `name` varchar(20) NOT NULL,
  `userid` varchar(20) NOT NULL,
  `email` varchar(30) NOT NULL,
  `password` varchar(20) NOT NULL,
  PRIMARY KEY (`userid`,`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `reg_tab`;
CREATE TABLE `reg_tab` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `faculty_name` varchar(150) NOT NULL,
  `designation` varchar(100) NOT NULL,
  `qualification` varchar(50) NOT NULL,
  `dept` varchar(50) NOT NULL,
  `pern_no` varchar(50) NOT NULL,
  `dob` date NOT NULL,
  `gender` enum('Male','Female','Other') NOT NULL,
  `address` varchar(255) NOT NULL,
  `email` varchar(150) NOT NULL,
  `aadhar` char(12) NOT NULL,
  `pan` char(10) NOT NULL,
  `userid` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` char(10) NOT NULL,
  `experience` text DEFAULT NULL,
  `photo_path` varchar(255) NOT NULL,
  `doj` date NOT NULL,
  `exp_cert_path` varchar(255) NOT NULL,
  `edu_cert_path` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `pern_no` (`pern_no`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `aadhar` (`aadhar`),
  UNIQUE KEY `pan` (`pan`),
  UNIQUE KEY `userid` (`userid`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `reg_tab` VALUES ('8', 'cseuser', '', '', 'CSE', '', '0000-00-00', 'Male', '', 'cse@gmail.com', '', '', 'cse@gmail.com', '123', '', NULL, '', '0000-00-00', '', '', '2026-08-12 06:57:13');

DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles` (
  `role_id` int(11) NOT NULL AUTO_INCREMENT,
  `role_name` varchar(50) NOT NULL,
  `role_description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`role_id`),
  UNIQUE KEY `role_name` (`role_name`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `roles` VALUES ('1', 'Admin', 'System administrator');
INSERT INTO `roles` VALUES ('2', 'IQAC', 'IQAC coordinator');
INSERT INTO `roles` VALUES ('3', 'HOD', 'Head of Department');
INSERT INTO `roles` VALUES ('4', 'Faculty', 'Faculty member');
INSERT INTO `roles` VALUES ('5', 'Dept Coordinator', 'Department coordinator');
INSERT INTO `roles` VALUES ('6', 'Central Coordinator', 'Central repository coordinator');
INSERT INTO `roles` VALUES ('7', 'Junior Assistant', 'Administrative assistant');
INSERT INTO `roles` VALUES ('8', 'RnD_Dean', 'R&D Dean - final approval authority');

DROP TABLE IF EXISTS `s_bodies`;
CREATE TABLE `s_bodies` (
  `ID` int(255) NOT NULL AUTO_INCREMENT,
  `Username` varchar(255) NOT NULL,
  `acd_year` varchar(255) NOT NULL,
  `branch` varchar(255) NOT NULL,
  `Body` varchar(400) NOT NULL,
  `event_name` varchar(400) NOT NULL,
  `from_date` date NOT NULL,
  `to_date` date NOT NULL,
  `organised_by` varchar(400) NOT NULL,
  `location` varchar(400) NOT NULL,
  `participation_status` varchar(400) NOT NULL,
  `certificate_path` varchar(400) NOT NULL,
  `uploaded_by` varchar(400) NOT NULL,
  `submission_time` varchar(400) NOT NULL,
  `status` varchar(50) DEFAULT 'Pending HOD',
  `rejection_reason` text DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `s_conference_tab`;
CREATE TABLE `s_conference_tab` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `Username` varchar(255) NOT NULL,
  `acd_year` int(255) NOT NULL,
  `uploaded_by` varchar(400) NOT NULL,
  `branch` varchar(255) NOT NULL,
  `paper_title` varchar(400) NOT NULL,
  `from_date` date NOT NULL,
  `to_date` date NOT NULL,
  `organised_by` varchar(400) NOT NULL,
  `location` varchar(400) NOT NULL,
  `certificate_path` varchar(400) NOT NULL,
  `paper_type` varchar(400) NOT NULL,
  `paper_file_path` varchar(400) NOT NULL,
  `submission_time` varchar(400) NOT NULL,
  `status` varchar(50) DEFAULT 'Pending HOD',
  `rejection_reason` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `s_events`;
CREATE TABLE `s_events` (
  `ID` int(255) NOT NULL AUTO_INCREMENT,
  `Username` varchar(100) NOT NULL,
  `branch` varchar(100) NOT NULL,
  `acd_year` varchar(255) NOT NULL,
  `activity` varchar(400) NOT NULL,
  `event_name` varchar(400) NOT NULL,
  `from_date` date NOT NULL,
  `to_date` date NOT NULL,
  `organised_by` varchar(400) NOT NULL,
  `location` varchar(400) NOT NULL,
  `participation_status` varchar(400) NOT NULL,
  `certificate_path` varchar(400) NOT NULL,
  `uploaded_by` varchar(400) NOT NULL,
  `submission_time` varchar(400) NOT NULL,
  `status` varchar(50) DEFAULT 'Pending HOD',
  `rejection_reason` text DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `s_journal_tab`;
CREATE TABLE `s_journal_tab` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `Username` varchar(255) NOT NULL,
  `uploaded_by` varchar(300) NOT NULL,
  `branch` varchar(255) NOT NULL,
  `acd_year` varchar(255) NOT NULL,
  `paper_title` varchar(400) NOT NULL,
  `journal_name` varchar(300) NOT NULL,
  `indexing` varchar(200) NOT NULL,
  `date_of_submission` date NOT NULL,
  `quality_factor` int(200) NOT NULL,
  `impact_factor` int(200) NOT NULL,
  `payment` varchar(400) NOT NULL,
  `submission_time` varchar(400) NOT NULL,
  `paper_file` varchar(400) NOT NULL,
  `status` varchar(50) DEFAULT 'Pending HOD',
  `rejection_reason` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `s_journal_tab` VALUES ('1', 'cseuser', 'sjournal', 'CSE', '2024-25', 'sjournal1', 'sjournal1', 'scopus', '2024-12-10', '1', '1', 'paid', '2026-08-12 08:07:42', 'uploads/6a7bdc76699281.01895027.pdf', 'Pending HOD', NULL);

DROP TABLE IF EXISTS `user_roles`;
CREATE TABLE `user_roles` (
  `user_role_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `dept_id` int(11) NOT NULL,
  PRIMARY KEY (`user_role_id`),
  UNIQUE KEY `user_id` (`user_id`,`role_id`,`dept_id`),
  KEY `role_id` (`role_id`),
  KEY `dept_id` (`dept_id`),
  CONSTRAINT `user_roles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  CONSTRAINT `user_roles_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`),
  CONSTRAINT `user_roles_ibfk_3` FOREIGN KEY (`dept_id`) REFERENCES `dept` (`dept_id`)
) ENGINE=InnoDB AUTO_INCREMENT=100 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `user_roles` VALUES ('4', '1', '1', '1');
INSERT INTO `user_roles` VALUES ('1', '1', '1', '10');
INSERT INTO `user_roles` VALUES ('3', '999', '1', '1');
INSERT INTO `user_roles` VALUES ('10', '1004', '3', '1');
INSERT INTO `user_roles` VALUES ('11', '1005', '3', '3');
INSERT INTO `user_roles` VALUES ('12', '1006', '3', '2');
INSERT INTO `user_roles` VALUES ('13', '1007', '3', '5');
INSERT INTO `user_roles` VALUES ('14', '1008', '3', '6');
INSERT INTO `user_roles` VALUES ('15', '1009', '3', '7');
INSERT INTO `user_roles` VALUES ('16', '1010', '3', '8');
INSERT INTO `user_roles` VALUES ('17', '1011', '3', '4');
INSERT INTO `user_roles` VALUES ('18', '1012', '3', '1');
INSERT INTO `user_roles` VALUES ('19', '1013', '5', '1');
INSERT INTO `user_roles` VALUES ('20', '1014', '5', '1');
INSERT INTO `user_roles` VALUES ('21', '1015', '5', '1');
INSERT INTO `user_roles` VALUES ('22', '1016', '5', '4');
INSERT INTO `user_roles` VALUES ('23', '1017', '5', '5');
INSERT INTO `user_roles` VALUES ('24', '1018', '5', '6');
INSERT INTO `user_roles` VALUES ('25', '1019', '5', '7');
INSERT INTO `user_roles` VALUES ('26', '1020', '5', '8');
INSERT INTO `user_roles` VALUES ('27', '1021', '5', '9');
INSERT INTO `user_roles` VALUES ('28', '1022', '6', '14');
INSERT INTO `user_roles` VALUES ('29', '1023', '6', '14');
INSERT INTO `user_roles` VALUES ('30', '1024', '6', '14');
INSERT INTO `user_roles` VALUES ('31', '1025', '6', '14');
INSERT INTO `user_roles` VALUES ('32', '1026', '6', '14');
INSERT INTO `user_roles` VALUES ('33', '1027', '6', '14');
INSERT INTO `user_roles` VALUES ('34', '1028', '6', '14');
INSERT INTO `user_roles` VALUES ('35', '1029', '6', '14');
INSERT INTO `user_roles` VALUES ('36', '1030', '6', '14');
INSERT INTO `user_roles` VALUES ('37', '1031', '6', '14');
INSERT INTO `user_roles` VALUES ('38', '1032', '6', '14');
INSERT INTO `user_roles` VALUES ('39', '1033', '6', '14');
INSERT INTO `user_roles` VALUES ('40', '1034', '7', '1');
INSERT INTO `user_roles` VALUES ('42', '1036', '2', '10');
INSERT INTO `user_roles` VALUES ('43', '1036', '2', '11');
INSERT INTO `user_roles` VALUES ('44', '1037', '2', '10');
INSERT INTO `user_roles` VALUES ('45', '1037', '2', '11');
INSERT INTO `user_roles` VALUES ('46', '1038', '2', '10');
INSERT INTO `user_roles` VALUES ('47', '1038', '2', '11');
INSERT INTO `user_roles` VALUES ('48', '1039', '2', '10');
INSERT INTO `user_roles` VALUES ('49', '1039', '2', '11');
INSERT INTO `user_roles` VALUES ('50', '1040', '2', '10');
INSERT INTO `user_roles` VALUES ('51', '1040', '2', '11');
INSERT INTO `user_roles` VALUES ('52', '1041', '2', '10');
INSERT INTO `user_roles` VALUES ('53', '1041', '2', '11');
INSERT INTO `user_roles` VALUES ('54', '1042', '2', '10');
INSERT INTO `user_roles` VALUES ('55', '1042', '2', '11');
INSERT INTO `user_roles` VALUES ('56', '1043', '2', '10');
INSERT INTO `user_roles` VALUES ('57', '1043', '2', '11');
INSERT INTO `user_roles` VALUES ('58', '1044', '2', '10');
INSERT INTO `user_roles` VALUES ('59', '1044', '2', '11');
INSERT INTO `user_roles` VALUES ('60', '1045', '2', '10');
INSERT INTO `user_roles` VALUES ('61', '1045', '2', '11');
INSERT INTO `user_roles` VALUES ('62', '1046', '2', '10');
INSERT INTO `user_roles` VALUES ('63', '1046', '2', '11');
INSERT INTO `user_roles` VALUES ('64', '1047', '2', '10');
INSERT INTO `user_roles` VALUES ('65', '1048', '2', '11');
INSERT INTO `user_roles` VALUES ('67', '1049', '1', '1');
INSERT INTO `user_roles` VALUES ('99', '1050', '4', '1');

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `full_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `profile_photo` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `last_login` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `phone` (`phone`)
) ENGINE=InnoDB AUTO_INCREMENT=1051 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `users` VALUES ('1', 'Admin', 'admin@gmrit.edu', '9876543210', '$2y$10$0/SUxXg9nsGq9/.Vfz1eCODoR5DePREUJYGvvPLVniB0asMzXH8du', NULL, 'active', '2026-07-29 14:55:39', '2026-09-13 13:30:10', NULL);
INSERT INTO `users` VALUES ('999', 'Test User', 'test@gmrit.edu.in', NULL, '$2y$10$uxA3xmhfENosbrWfUphS/uchsgv55dg.rgHrssmXMG0YMhK4U0hS.', NULL, 'active', '2026-07-29 15:10:24', '2026-09-13 13:30:10', NULL);
INSERT INTO `users` VALUES ('1004', 'cse-hod', 'cse-hod@gmrit.edu.in', NULL, '$2y$10$QCSnK5oDvpgJ2nhhekUs9.LEN6xyJzs.nFR77G7t2EfhvkpenWsjC', NULL, 'active', '2026-08-11 23:48:38', '2026-09-13 13:30:10', NULL);
INSERT INTO `users` VALUES ('1005', 'aiml-hod', 'aiml-hod@gmrit.edu.in', NULL, '$2y$10$wlxS/Q/m3ee/HeewUzWhhuGDoiMrzhqTkfQIXQLiKqH5iv6JRhGxy', NULL, 'active', '2026-08-11 23:48:38', '2026-09-13 13:30:10', NULL);
INSERT INTO `users` VALUES ('1006', 'aids-hod', 'aids-hod@gmrit.edu.in', NULL, '$2y$10$eJTyfeQPvGN9DAgI1DMqXuWeNodaBNFcJVPv1ROZ66e9M/BbfgGXy', NULL, 'active', '2026-08-11 23:48:38', '2026-09-13 13:30:10', NULL);
INSERT INTO `users` VALUES ('1007', 'ece-hod', 'ece-hod@gmrit.edu.in', NULL, '$2y$10$Jt/qOGAo/4.OMBf37dDTZeZBAsOH5PvbZSknqthvChcPnmlaRYTzu', NULL, 'active', '2026-08-11 23:48:38', '2026-09-13 13:30:10', NULL);
INSERT INTO `users` VALUES ('1008', 'eee-hod', 'eee-hod@gmrit.edu.in', NULL, '$2y$10$UYClGB1R8UcN8MZpBUlF4.qUccp8zKieXexUBOM3O8qZi0Kg17hs6', NULL, 'active', '2026-08-11 23:48:38', '2026-09-13 13:30:10', NULL);
INSERT INTO `users` VALUES ('1009', 'mech-hod', 'mech-hod@gmrit.edu.in', NULL, '$2y$10$kpXoWOimZr1muPUAVkIV1.a3ujV1crKgSdzn8zwuVSVWfZbieJgjm', NULL, 'active', '2026-08-11 23:48:38', '2026-09-13 13:30:10', NULL);
INSERT INTO `users` VALUES ('1010', 'civil-hod', 'civil-hod@gmrit.edu.in', NULL, '$2y$10$szF1g7GcIE2pVuzIrcTOjOKI5SK.p6uoq2/QZ75gRdF.Fdwjfmto6', NULL, 'active', '2026-08-11 23:48:38', '2026-09-13 13:30:10', NULL);
INSERT INTO `users` VALUES ('1011', 'it-hod', 'it-hod@gmrit.edu.in', NULL, '$2y$10$faE13GxYLb0CI6egKPXUmujZ4w8OrH0TEMstBKWAHqy.IaQzLyz1.', NULL, 'active', '2026-08-11 23:48:38', '2026-09-13 13:30:10', NULL);
INSERT INTO `users` VALUES ('1012', 'hod', 'hod@gmrit.edu.in', NULL, '$2y$10$rMvdXcovw5Otxt7iPMPZq.Z0qFgySQ3oxsNDQmHs5kvFhSP9SIPj2', NULL, 'active', '2026-08-11 23:48:38', '2026-09-13 13:30:10', NULL);
INSERT INTO `users` VALUES ('1013', 'dept_cord1', 'dept_cord1@gmrit.edu.in', NULL, '$2y$10$54kJgg2LXFYkjIyNGPwjq.bedFGZ.9Ngjv89N9PdPqHSNad79fx6i', NULL, 'active', '2026-08-11 23:48:38', '2026-09-13 13:30:10', NULL);
INSERT INTO `users` VALUES ('1014', 'dept_cord2', 'dept_cord2@gmrit.edu.in', NULL, '$2y$10$2fqVKW5h8v8dPC0ghWVFMuEQhrZwUEzLUj9XGSkxo/g4C61VkbFey', NULL, 'active', '2026-08-11 23:48:38', '2026-09-13 13:30:10', NULL);
INSERT INTO `users` VALUES ('1015', 'dept_cord3', 'dept_cord3@gmrit.edu.in', NULL, '$2y$10$e45RHV.5NxkkBv8ZiW3U3uMZh.b7gMetzZZBnd4j7Gw6TqsiH.oOC', NULL, 'active', '2026-08-11 23:48:38', '2026-09-13 13:30:10', NULL);
INSERT INTO `users` VALUES ('1016', 'dept_cord4', 'dept_cord4@gmrit.edu.in', NULL, '$2y$10$grLJQ0dmpqnl9wsUdV8xxOGtD3EOplOg.m.wHKdHCEn8rrHf5A366', NULL, 'active', '2026-08-11 23:48:38', '2026-09-13 13:30:11', NULL);
INSERT INTO `users` VALUES ('1017', 'dept_cord5', 'dept_cord5@gmrit.edu.in', NULL, '$2y$10$r3OID.krhBusJaGfjO5cyOrW0DWQnmJBSD1qaTCJMZVJjFhytVQJi', NULL, 'active', '2026-08-11 23:48:38', '2026-09-13 13:30:11', NULL);
INSERT INTO `users` VALUES ('1018', 'dept_cord6', 'dept_cord6@gmrit.edu.in', NULL, '$2y$10$c3VH4c4nNcNzmZWbtYUku.JDe9Cz6qrl2Kc7cTUN8OE.Uzl1HuQWK', NULL, 'active', '2026-08-11 23:48:38', '2026-09-13 13:30:11', NULL);
INSERT INTO `users` VALUES ('1019', 'dept_cord7', 'dept_cord7@gmrit.edu.in', NULL, '$2y$10$6xbbpdMfuiBXQCXCItji9O0u0aHUUeXtSfZrNT2zuelnc6zxYWGom', NULL, 'active', '2026-08-11 23:48:38', '2026-09-13 13:30:11', NULL);
INSERT INTO `users` VALUES ('1020', 'dept_cord8', 'dept_cord8@gmrit.edu.in', NULL, '$2y$10$3V8Rfhjpkxr0oHMEyKRLZ.R3MbnK6HzSIwIxKUnFoVFgZ1EY.vtgm', NULL, 'active', '2026-08-11 23:48:38', '2026-09-13 13:30:11', NULL);
INSERT INTO `users` VALUES ('1021', 'dept_cord9', 'dept_cord9@gmrit.edu.in', NULL, '$2y$10$r8.Gq.Gai2xCTZ8vprAR.uXsmZw/t1vVNtOsIK1qVgmw/A8dFOgIO', NULL, 'active', '2026-08-11 23:48:38', '2026-09-13 13:30:11', NULL);
INSERT INTO `users` VALUES ('1022', 'central_cord', 'central_cord@gmrit.edu.in', NULL, '$2y$10$00XX5Aephfr0qJRxg5Ru4ur1BySZTiQFJmefhJUlUGnQOoaT25T8a', NULL, 'active', '2026-08-11 23:48:38', '2026-09-13 13:30:11', NULL);
INSERT INTO `users` VALUES ('1023', 'central_cord1', 'central_cord1@gmrit.edu.in', NULL, '$2y$10$e5CZWZTnZ6xbS1cUVbgzxuipbq3OSFd5kH0n4FMGMxh9thKK1re2a', NULL, 'active', '2026-08-11 23:48:38', '2026-09-13 13:30:11', NULL);
INSERT INTO `users` VALUES ('1024', 'central_cord2', 'central_cord2@gmrit.edu.in', NULL, '$2y$10$L/GFolkiNqEyRnqB5w2Qg./mKSM13d3F2DNwaRwp6JryEH096eiwS', NULL, 'active', '2026-08-11 23:48:38', '2026-09-13 13:30:11', NULL);
INSERT INTO `users` VALUES ('1025', 'central_cord3', 'central_cord3@gmrit.edu.in', NULL, '$2y$10$dIlzt9oUdRuIVpRDQEYNi.syFl08HeV9am9gItmjXkEnBXEfQ86MK', NULL, 'active', '2026-08-11 23:48:38', '2026-09-13 13:30:11', NULL);
INSERT INTO `users` VALUES ('1026', 'central_cord4', 'central_cord4@gmrit.edu.in', NULL, '$2y$10$QDJsGD/GKElhr9ba5wxxO.p1HLQUhDJFC5.R6CX7BJsAmrso/MX9G', NULL, 'active', '2026-08-11 23:48:38', '2026-09-13 13:30:11', NULL);
INSERT INTO `users` VALUES ('1027', 'central_cord5', 'central_cord5@gmrit.edu.in', NULL, '$2y$10$Cy2LsY6b7nOx.ZxLBZCi6OCkGd2m/iXaE4Tbbh9PiY6Tw3OYe5s22', NULL, 'active', '2026-08-11 23:48:38', '2026-09-13 13:30:11', NULL);
INSERT INTO `users` VALUES ('1028', 'central_cord6', 'central_cord6@gmrit.edu.in', NULL, '$2y$10$q5xEx7qaHlW8O53R6YFrpegxerUw.UGITrF1y3nJ1/lp2tl9mva.y', NULL, 'active', '2026-08-11 23:48:38', '2026-09-13 13:30:11', NULL);
INSERT INTO `users` VALUES ('1029', 'central_cord7', 'central_cord7@gmrit.edu.in', NULL, '$2y$10$Qh/mnYy3uIQrCr2qVwpR1OutY/LpAzrHKxUrlffX6QflmQZ/HRYUa', NULL, 'active', '2026-08-11 23:48:38', '2026-09-13 13:30:11', NULL);
INSERT INTO `users` VALUES ('1030', 'central_cord8', 'central_cord8@gmrit.edu.in', NULL, '$2y$10$I/55cz8ucxgAQH9LfKmowurPyNQj5KRQ2KzPJfQIF.zWK0W1d7Hwm', NULL, 'active', '2026-08-11 23:48:38', '2026-09-13 13:30:11', NULL);
INSERT INTO `users` VALUES ('1031', 'central_cord9', 'central_cord9@gmrit.edu.in', NULL, '$2y$10$gHXQyASi3bksKa9ydquxQeHkWk6OqfGJgdhlozdpH.aIUqYaOrjOa', NULL, 'active', '2026-08-11 23:48:38', '2026-09-13 13:30:11', NULL);
INSERT INTO `users` VALUES ('1032', 'central_cord10', 'central_cord10@gmrit.edu.in', NULL, '$2y$10$YMKwwUU3qjh/W8Lzfrt4UenpTJn1RZysyVk1EGZloc90.fKPg1AsS', NULL, 'active', '2026-08-11 23:48:38', '2026-09-13 13:30:11', NULL);
INSERT INTO `users` VALUES ('1033', 'central', 'central@gmrit.edu.in', NULL, '$2y$10$Xn1NJ9Tv5J2UhzRTfoIaxujZ3nnsLAJTmF91ziSutkQCP4Q6NUYLi', NULL, 'active', '2026-08-11 23:48:38', '2026-09-13 13:30:12', NULL);
INSERT INTO `users` VALUES ('1034', 'jr1', 'jr1@gmrit.edu.in', NULL, '$2y$10$ii477PxsukUYd7qqUqkz1ef6fr//D9tcJhebKEH.wpXtzEdQoSxcW', NULL, 'active', '2026-08-11 23:48:38', '2026-09-13 13:30:12', NULL);
INSERT INTO `users` VALUES ('1036', 'cri_cord', 'cri_cord@gmrit.edu.in', NULL, '$2y$10$./RMsPvCUVRoO9yRQNAcXOCGJznjvizUh.ITGOy56i0jkl2pt1U9S', NULL, 'active', '2026-08-12 00:59:42', '2026-09-13 13:30:12', NULL);
INSERT INTO `users` VALUES ('1037', 'cri_cord1', 'cri_cord1@gmrit.edu.in', NULL, '$2y$10$8aZAMiwf7z8gUvFrYMcg5OtFGWut91T96k4I/Eq8vaI1Qby3a7e.6', NULL, 'active', '2026-08-12 00:59:42', '2026-09-13 13:30:12', NULL);
INSERT INTO `users` VALUES ('1038', 'cri_cord2', 'cri_cord2@gmrit.edu.in', NULL, '$2y$10$LTWqZUezWPlGUFvQbA2UteeY2ekSjXTXVnsdlbK9LvayqSSZBbl26', NULL, 'active', '2026-08-12 00:59:42', '2026-09-13 13:30:12', NULL);
INSERT INTO `users` VALUES ('1039', 'cri_cord3', 'cri_cord3@gmrit.edu.in', NULL, '$2y$10$e9uw9DGXT1ey7dOTiOuoLuQ/SF7p4eNnmcd5ZhRB1tBiZt4/Th6PG', NULL, 'active', '2026-08-12 00:59:42', '2026-09-13 13:30:12', NULL);
INSERT INTO `users` VALUES ('1040', 'cri_cord4', 'cri_cord4@gmrit.edu.in', NULL, '$2y$10$FJre0/c/nf2iIlaqqjladuQC9XD/GbLFDLwp9Y4mUUA65rPRZ9HdW', NULL, 'active', '2026-08-12 00:59:42', '2026-09-13 13:30:12', NULL);
INSERT INTO `users` VALUES ('1041', 'cri_cord5', 'cri_cord5@gmrit.edu.in', NULL, '$2y$10$V/hdT8y5LzpmWhlsm4yhhODkcx/9oN9zkT3Nan6E8hfLV1IFLJc8i', NULL, 'active', '2026-08-12 00:59:42', '2026-09-13 13:30:12', NULL);
INSERT INTO `users` VALUES ('1042', 'cri_cord6', 'cri_cord6@gmrit.edu.in', NULL, '$2y$10$m9.YfNwEaGdvC7hvILCwpuZCqx/6rjeaMSL1pETWZAgFmZeksdr9q', NULL, 'active', '2026-08-12 00:59:42', '2026-09-13 13:30:12', NULL);
INSERT INTO `users` VALUES ('1043', 'cri_cord7', 'cri_cord7@gmrit.edu.in', NULL, '$2y$10$Ch2F5ARK45rYGfN8y6TqzufUalfdSO8TMeTrU2rA4fG13OPxYQlx2', NULL, 'active', '2026-08-12 00:59:42', '2026-09-13 13:30:12', NULL);
INSERT INTO `users` VALUES ('1044', 'cri_cord8', 'cri_cord8@gmrit.edu.in', NULL, '$2y$10$BSPe4Kn/MRZhPtSCkdzS3uDwbZIEkesaGczpt5vOugZ/jLreLq3V2', NULL, 'active', '2026-08-12 00:59:42', '2026-09-13 13:30:12', NULL);
INSERT INTO `users` VALUES ('1045', 'cri_cord9', 'cri_cord9@gmrit.edu.in', NULL, '$2y$10$q2LG2h0vTyDna.q5CuVwi.SBkENjA8h5ZS7nGP01S8tecSd7Rsyiy', NULL, 'active', '2026-08-12 00:59:42', '2026-09-13 13:30:12', NULL);
INSERT INTO `users` VALUES ('1046', 'cri_cord10', 'cri_cord10@gmrit.edu.in', NULL, '$2y$10$7W8XjH37912tCynH/5HW5.Md6WKW52h2rFRL.QhNX3CXaGHOGwHe6', NULL, 'active', '2026-08-12 00:59:42', '2026-09-13 13:30:12', NULL);
INSERT INTO `users` VALUES ('1047', 'naac@gmail.com', 'naac@gmail.com', NULL, '$2y$10$vIPSRyeiiTTXMVp50xjJf.kIAhoTCwFzS/xVVJuSDOAmDE9tuwDvm', NULL, 'active', '2026-08-12 00:59:42', '2026-09-13 13:30:12', NULL);
INSERT INTO `users` VALUES ('1048', 'nba@gmail.com', 'nba@gmail.com', NULL, '$2y$10$7wcPaTyqHnxVCbTEajLal.oChenfCd/WwcDssOADkUyqGPDJW1.3K', NULL, 'active', '2026-08-12 00:59:42', '2026-09-13 13:30:12', NULL);
INSERT INTO `users` VALUES ('1049', 'chandu', 'chandu@gmrit.edu.in', NULL, '$2y$10$ScNbgGv1k.AGSs95P9L16O7qc9RSFCENUCrbe6ej4piVdFvJ4clmK', NULL, 'active', '2026-08-12 06:39:18', '2026-09-13 13:30:12', NULL);
INSERT INTO `users` VALUES ('1050', 'cseuser', 'cse@gmail.com', '1234567890', '$2y$10$SPMU4aCoqhT7aNSvkm7DCOFjwUPtp4dxb7qx636R6diRRKVOIlStW', 'uploads/profiles/doc_6a7bcbf1af8786.88473304.png', 'active', '2026-08-12 06:57:13', '2026-09-13 13:30:12', NULL);

DROP TABLE IF EXISTS `workflow_steps`;
CREATE TABLE `workflow_steps` (
  `step_id` int(11) NOT NULL AUTO_INCREMENT,
  `workflow_key` varchar(50) NOT NULL,
  `step_order` int(11) NOT NULL,
  `approver_role_id` int(11) NOT NULL,
  `scope` enum('department','global') NOT NULL DEFAULT 'department',
  `on_approve` varchar(50) NOT NULL,
  `on_reject` varchar(50) NOT NULL DEFAULT 'reject_to_start',
  PRIMARY KEY (`step_id`),
  UNIQUE KEY `uq_workflow_order` (`workflow_key`,`step_order`),
  KEY `approver_role_id` (`approver_role_id`),
  CONSTRAINT `workflow_steps_ibfk_1` FOREIGN KEY (`approver_role_id`) REFERENCES `roles` (`role_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `workflow_steps` VALUES ('1', 'department', '1', '3', 'department', 'next_step', 'reject_to_start');
INSERT INTO `workflow_steps` VALUES ('2', 'department', '2', '8', 'global', 'accept', 'reject_to_start');

SET FOREIGN_KEY_CHECKS=1;
