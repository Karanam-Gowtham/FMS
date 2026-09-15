-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: gmritfms
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `a_c_files`
--

DROP TABLE IF EXISTS `a_c_files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `a_cri_files`
--

DROP TABLE IF EXISTS `a_cri_files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `a_files`
--

DROP TABLE IF EXISTS `a_files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `academic_year`
--

DROP TABLE IF EXISTS `academic_year`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `academic_year` (
  `year` varchar(40) NOT NULL,
  PRIMARY KEY (`year`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `admin_login`
--

DROP TABLE IF EXISTS `admin_login`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin_login` (
  `Username` varchar(30) NOT NULL,
  `Password` varchar(30) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=85 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `admin_reg`
--

DROP TABLE IF EXISTS `admin_reg`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin_reg` (
  `Username` varchar(30) NOT NULL,
  `Password` varchar(20) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `approval_roles`
--

DROP TABLE IF EXISTS `approval_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `approval_roles` (
  `role_id` int(11) NOT NULL AUTO_INCREMENT,
  `role_name` varchar(50) NOT NULL,
  `role_order` int(11) NOT NULL,
  PRIMARY KEY (`role_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `central_files`
--

DROP TABLE IF EXISTS `central_files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `conf_org_tab`
--

DROP TABLE IF EXISTS `conf_org_tab`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `conference_tab`
--

DROP TABLE IF EXISTS `conference_tab`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `contact_form`
--

DROP TABLE IF EXISTS `contact_form`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `criteria`
--

DROP TABLE IF EXISTS `criteria`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `criteria` (
  `SI_no` int(10) NOT NULL,
  `Sub_no` varchar(30) NOT NULL,
  `Des` varchar(600) NOT NULL,
  `year` varchar(200) NOT NULL,
  PRIMARY KEY (`Sub_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `criteria1`
--

DROP TABLE IF EXISTS `criteria1`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `criteria1` (
  `SI_no` int(10) NOT NULL,
  `Sub_no` varchar(30) NOT NULL,
  `Des` varchar(600) NOT NULL,
  `year` varchar(200) NOT NULL,
  PRIMARY KEY (`Sub_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `criteria2`
--

DROP TABLE IF EXISTS `criteria2`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `criteria2` (
  `SI_no` int(10) NOT NULL,
  `Sub_no` varchar(30) NOT NULL,
  `Des` varchar(600) NOT NULL,
  `year` varchar(200) NOT NULL,
  PRIMARY KEY (`Sub_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `dc_up_files`
--

DROP TABLE IF EXISTS `dc_up_files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `dept`
--

DROP TABLE IF EXISTS `dept`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dept` (
  `dept_id` int(11) NOT NULL AUTO_INCREMENT,
  `dept_name` varchar(50) NOT NULL,
  PRIMARY KEY (`dept_id`),
  UNIQUE KEY `dept_name` (`dept_name`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `dept_files`
--

DROP TABLE IF EXISTS `dept_files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `document_actions`
--

DROP TABLE IF EXISTS `document_actions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `document_actions` (
  `action_id` int(11) NOT NULL AUTO_INCREMENT,
  `document_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `action_type` varchar(50) NOT NULL,
  `remarks` text DEFAULT NULL,
  `action_date` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`action_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `document_role_flow`
--

DROP TABLE IF EXISTS `document_role_flow`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `document_role_flow` (
  `flow_id` int(11) NOT NULL AUTO_INCREMENT,
  `version_id` int(11) NOT NULL,
  `current_role_id` int(11) NOT NULL,
  `status` enum('PENDING','UNDER_REVIEW','APPROVED') DEFAULT 'PENDING',
  `updated_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`flow_id`),
  KEY `version_id` (`version_id`),
  KEY `current_role_id` (`current_role_id`),
  CONSTRAINT `document_role_flow_ibfk_1` FOREIGN KEY (`version_id`) REFERENCES `document_versions` (`version_id`),
  CONSTRAINT `document_role_flow_ibfk_2` FOREIGN KEY (`current_role_id`) REFERENCES `approval_roles` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `document_versions`
--

DROP TABLE IF EXISTS `document_versions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `document_versions` (
  `version_id` int(11) NOT NULL AUTO_INCREMENT,
  `document_id` int(11) NOT NULL,
  `version_number` int(11) NOT NULL,
  `file_path` varchar(500) DEFAULT NULL,
  `uploaded_by` int(11) DEFAULT NULL,
  `uploaded_at` datetime DEFAULT current_timestamp(),
  `is_current` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`version_id`),
  KEY `document_id` (`document_id`),
  CONSTRAINT `document_versions_ibfk_1` FOREIGN KEY (`document_id`) REFERENCES `documents` (`document_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `documents`
--

DROP TABLE IF EXISTS `documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `documents` (
  `document_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`document_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `fdps_org_tab`
--

DROP TABLE IF EXISTS `fdps_org_tab`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fdps_org_tab` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `branch` varchar(100) NOT NULL,
  `title` varchar(255) NOT NULL,
  `date_from` date NOT NULL,
  `date_to` date NOT NULL,
  `organised_by` varchar(255) NOT NULL,
  `location` varchar(255) NOT NULL,
  `certificate` varchar(255) NOT NULL,
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `fdps_tab`
--

DROP TABLE IF EXISTS `fdps_tab`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `files`
--

DROP TABLE IF EXISTS `files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `files5_1_1and2`
--

DROP TABLE IF EXISTS `files5_1_1and2`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `files5_1_3`
--

DROP TABLE IF EXISTS `files5_1_3`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `files5_1_4`
--

DROP TABLE IF EXISTS `files5_1_4`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `files5_2_1`
--

DROP TABLE IF EXISTS `files5_2_1`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `files5_2_2`
--

DROP TABLE IF EXISTS `files5_2_2`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `files5_2_3`
--

DROP TABLE IF EXISTS `files5_2_3`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `files5_3_1`
--

DROP TABLE IF EXISTS `files5_3_1`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `files5_3_3`
--

DROP TABLE IF EXISTS `files5_3_3`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `login_pg`
--

DROP TABLE IF EXISTS `login_pg`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `login_pg` (
  `userid` varchar(30) NOT NULL,
  `password` varchar(20) NOT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=308 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `patents_table`
--

DROP TABLE IF EXISTS `patents_table`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `published_tab`
--

DROP TABLE IF EXISTS `published_tab`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `reg_central_cord`
--

DROP TABLE IF EXISTS `reg_central_cord`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reg_central_cord` (
  `userid` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `reg_cri_cord`
--

DROP TABLE IF EXISTS `reg_cri_cord`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reg_cri_cord` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `userid` (`userid`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `reg_dept_cord`
--

DROP TABLE IF EXISTS `reg_dept_cord`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reg_dept_cord` (
  `userid` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `department` varchar(50) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `reg_hod`
--

DROP TABLE IF EXISTS `reg_hod`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reg_hod` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `department` varchar(100) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `reg_jr_assistant`
--

DROP TABLE IF EXISTS `reg_jr_assistant`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reg_jr_assistant` (
  `userid` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `department` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `reg_pg`
--

DROP TABLE IF EXISTS `reg_pg`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reg_pg` (
  `name` varchar(20) NOT NULL,
  `userid` varchar(20) NOT NULL,
  `email` varchar(30) NOT NULL,
  `password` varchar(20) NOT NULL,
  PRIMARY KEY (`userid`,`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `reg_tab`
--

DROP TABLE IF EXISTS `reg_tab`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rejection_history`
--

DROP TABLE IF EXISTS `rejection_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rejection_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `file_id` int(11) NOT NULL,
  `table_name` varchar(100) NOT NULL,
  `rejected_by` varchar(100) DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rnd_central_documents`
--

DROP TABLE IF EXISTS `rnd_central_documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rnd_central_documents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uploader_id` int(11) NOT NULL,
  `category` varchar(100) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `academic_year` varchar(50) DEFAULT NULL,
  `upload_date` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `uploader_id` (`uploader_id`),
  CONSTRAINT `rnd_central_documents_ibfk_1` FOREIGN KEY (`uploader_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `role_flow_logs`
--

DROP TABLE IF EXISTS `role_flow_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `role_flow_logs` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `version_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `action` enum('APPROVED','SENT_BACK') NOT NULL,
  `comments` text DEFAULT NULL,
  `action_by` int(11) DEFAULT NULL,
  `action_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`log_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `roles` (
  `role_id` int(11) NOT NULL AUTO_INCREMENT,
  `role_name` varchar(50) NOT NULL,
  `role_description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`role_id`),
  UNIQUE KEY `role_name` (`role_name`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `s_bodies`
--

DROP TABLE IF EXISTS `s_bodies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `s_conference_tab`
--

DROP TABLE IF EXISTS `s_conference_tab`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `s_events`
--

DROP TABLE IF EXISTS `s_events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `s_journal_tab`
--

DROP TABLE IF EXISTS `s_journal_tab`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_roles`
--

DROP TABLE IF EXISTS `user_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_roles` (
  `user_role_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `dept_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`user_role_id`),
  UNIQUE KEY `user_id` (`user_id`,`role_id`,`dept_id`),
  KEY `role_id` (`role_id`),
  KEY `dept_id` (`dept_id`),
  CONSTRAINT `user_roles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  CONSTRAINT `user_roles_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`),
  CONSTRAINT `user_roles_ibfk_3` FOREIGN KEY (`dept_id`) REFERENCES `dept` (`dept_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=InnoDB AUTO_INCREMENT=1005 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `workflow_actions`
--

DROP TABLE IF EXISTS `workflow_actions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `workflow_actions` (
  `action_id` int(11) NOT NULL AUTO_INCREMENT,
  `action_key` varchar(50) NOT NULL,
  `label` varchar(100) NOT NULL,
  PRIMARY KEY (`action_id`),
  UNIQUE KEY `action_key` (`action_key`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `workflow_steps`
--

DROP TABLE IF EXISTS `workflow_steps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `workflow_steps` (
  `step_id` int(11) NOT NULL AUTO_INCREMENT,
  `workflow_id` int(11) NOT NULL,
  `step_order` int(11) NOT NULL,
  `step_label` varchar(100) NOT NULL,
  `responsible_role_id` int(11) NOT NULL,
  `scope` enum('department','global') NOT NULL DEFAULT 'department',
  PRIMARY KEY (`step_id`),
  UNIQUE KEY `uq_workflow_step` (`workflow_id`,`step_order`),
  CONSTRAINT `workflow_steps_ibfk_1` FOREIGN KEY (`workflow_id`) REFERENCES `workflows` (`workflow_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `workflow_transitions`
--

DROP TABLE IF EXISTS `workflow_transitions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `workflow_transitions` (
  `transition_id` int(11) NOT NULL AUTO_INCREMENT,
  `step_id` int(11) NOT NULL,
  `action_id` int(11) NOT NULL,
  `to_step_id` int(11) DEFAULT NULL,
  `resulting_status` varchar(50) NOT NULL,
  PRIMARY KEY (`transition_id`),
  UNIQUE KEY `uq_step_action` (`step_id`,`action_id`),
  KEY `action_id` (`action_id`),
  KEY `to_step_id` (`to_step_id`),
  CONSTRAINT `workflow_transitions_ibfk_1` FOREIGN KEY (`step_id`) REFERENCES `workflow_steps` (`step_id`),
  CONSTRAINT `workflow_transitions_ibfk_2` FOREIGN KEY (`action_id`) REFERENCES `workflow_actions` (`action_id`),
  CONSTRAINT `workflow_transitions_ibfk_3` FOREIGN KEY (`to_step_id`) REFERENCES `workflow_steps` (`step_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `workflows`
--

DROP TABLE IF EXISTS `workflows`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `workflows` (
  `workflow_id` int(11) NOT NULL AUTO_INCREMENT,
  `workflow_key` varchar(50) NOT NULL,
  `label` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`workflow_id`),
  UNIQUE KEY `workflow_key` (`workflow_key`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-09-15 11:48:31
