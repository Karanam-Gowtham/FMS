-- ============================================================================
-- GMR INSTITUTE OF TECHNOLOGY - FILE MANAGEMENT SYSTEM (FMS)
-- FRESH PRODUCTION DATABASE SETUP SCRIPT
-- ============================================================================
-- 
-- Description:
--   This script creates a 100% clean, production-ready 'gmrdufms' database.
--   All test documents, dummy uploads, and trial submissions have been removed.
--   Core system lookups, departments, roles, workflows, academic years,
--   NAAC form schemas, and default institutional role accounts are seeded.
--
-- Target Database: gmrdufms (MySQL 8.0+ / MariaDB 10.4+)
-- Encoding: utf8mb4 / utf8mb4_unicode_ci
-- Default Password for ALL Seeded Accounts: Admin@123
-- ============================================================================

CREATE DATABASE IF NOT EXISTS `gmrdufms` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `gmrdufms`;

SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';
SET time_zone = '+00:00';


-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: gmrdufms
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
-- Table structure for table `academic_years`
--

DROP TABLE IF EXISTS `academic_years`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `academic_years` (
  `year_id` int(11) NOT NULL AUTO_INCREMENT,
  `year_label` varchar(20) NOT NULL,
  PRIMARY KEY (`year_id`),
  UNIQUE KEY `year_label` (`year_label`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
-- Table structure for table `departments`
--

DROP TABLE IF EXISTS `departments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `departments` (
  `dept_id` int(11) NOT NULL AUTO_INCREMENT,
  `dept_name` varchar(100) NOT NULL,
  `is_academic` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`dept_id`),
  UNIQUE KEY `dept_name` (`dept_name`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `document_actions`
--

DROP TABLE IF EXISTS `document_actions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `document_actions` (
  `action_id` int(11) NOT NULL AUTO_INCREMENT,
  `doc_id` int(11) NOT NULL,
  `acted_by` int(11) NOT NULL,
  `action` varchar(50) NOT NULL DEFAULT 'uploaded',
  `step_id` int(11) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `acted_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`action_id`),
  KEY `acted_by` (`acted_by`),
  KEY `step_id` (`step_id`),
  KEY `idx_doc` (`doc_id`),
  CONSTRAINT `document_actions_ibfk_1` FOREIGN KEY (`doc_id`) REFERENCES `documents` (`doc_id`) ON DELETE CASCADE,
  CONSTRAINT `document_actions_ibfk_2` FOREIGN KEY (`acted_by`) REFERENCES `users` (`user_id`),
  CONSTRAINT `document_actions_ibfk_3` FOREIGN KEY (`step_id`) REFERENCES `workflow_steps` (`step_id`)
) ENGINE=InnoDB AUTO_INCREMENT=129 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `document_files`
--

DROP TABLE IF EXISTS `document_files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=InnoDB AUTO_INCREMENT=177 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `document_meta_student_activity`
--

DROP TABLE IF EXISTS `document_meta_student_activity`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `document_meta_student_activity` (
  `meta_id` int(11) NOT NULL AUTO_INCREMENT,
  `doc_id` int(11) NOT NULL,
  `activity_category` varchar(100) NOT NULL,
  `participation_type` enum('Individual','Team','Batch') NOT NULL DEFAULT 'Individual',
  `event_details` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`event_details`)),
  `participants` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`participants`)),
  PRIMARY KEY (`meta_id`),
  KEY `doc_id` (`doc_id`),
  CONSTRAINT `document_meta_student_activity_ibfk_1` FOREIGN KEY (`doc_id`) REFERENCES `documents` (`doc_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `document_metadata`
--

DROP TABLE IF EXISTS `document_metadata`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `document_metadata` (
  `meta_id` int(11) NOT NULL AUTO_INCREMENT,
  `doc_id` int(11) NOT NULL,
  `meta_key` varchar(100) NOT NULL,
  `meta_value` text DEFAULT NULL,
  PRIMARY KEY (`meta_id`),
  UNIQUE KEY `uq_doc_meta` (`doc_id`,`meta_key`),
  CONSTRAINT `document_metadata_ibfk_1` FOREIGN KEY (`doc_id`) REFERENCES `documents` (`doc_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `document_types`
--

DROP TABLE IF EXISTS `document_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `document_types` (
  `type_id` int(11) NOT NULL AUTO_INCREMENT,
  `type_code` varchar(50) NOT NULL,
  `label` varchar(150) NOT NULL,
  `workflow_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`type_id`),
  UNIQUE KEY `type_code` (`type_code`),
  KEY `workflow_id` (`workflow_id`),
  CONSTRAINT `document_types_ibfk_1` FOREIGN KEY (`workflow_id`) REFERENCES `workflows` (`workflow_id`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `documents`
--

DROP TABLE IF EXISTS `documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `documents` (
  `doc_id` int(11) NOT NULL AUTO_INCREMENT,
  `type_id` int(11) NOT NULL,
  `uploaded_by` int(11) NOT NULL,
  `dept_id` int(11) DEFAULT NULL,
  `mentor_id` int(11) DEFAULT NULL,
  `academic_year_id` int(11) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `file_path` varchar(500) NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'draft',
  `current_step` int(11) DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`doc_id`),
  KEY `type_id` (`type_id`),
  KEY `uploaded_by` (`uploaded_by`),
  KEY `academic_year_id` (`academic_year_id`),
  KEY `current_step` (`current_step`),
  KEY `idx_status` (`status`),
  KEY `idx_dept_status` (`dept_id`,`status`),
  CONSTRAINT `documents_ibfk_1` FOREIGN KEY (`type_id`) REFERENCES `document_types` (`type_id`),
  CONSTRAINT `documents_ibfk_2` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`user_id`),
  CONSTRAINT `documents_ibfk_3` FOREIGN KEY (`dept_id`) REFERENCES `departments` (`dept_id`),
  CONSTRAINT `documents_ibfk_4` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`year_id`),
  CONSTRAINT `documents_ibfk_5` FOREIGN KEY (`current_step`) REFERENCES `workflow_steps` (`step_id`)
) ENGINE=InnoDB AUTO_INCREMENT=57 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
-- Table structure for table `meta_award`
--

DROP TABLE IF EXISTS `meta_award`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `meta_award` (
  `meta_id` int(11) NOT NULL AUTO_INCREMENT,
  `doc_id` int(11) NOT NULL,
  `award_name` varchar(255) DEFAULT NULL,
  `participation_type` varchar(255) DEFAULT NULL,
  `student_name` varchar(255) DEFAULT NULL,
  `competition_level` varchar(255) DEFAULT NULL,
  `event_name` varchar(255) DEFAULT NULL,
  `month_year` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`meta_id`),
  KEY `idx_doc_id` (`doc_id`),
  CONSTRAINT `fk_meta_award_doc` FOREIGN KEY (`doc_id`) REFERENCES `documents` (`doc_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `meta_central_file`
--

DROP TABLE IF EXISTS `meta_central_file`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `meta_central_file` (
  `meta_id` int(11) NOT NULL AUTO_INCREMENT,
  `doc_id` int(11) NOT NULL,
  `event` varchar(255) DEFAULT NULL,
  `club_name` varchar(255) DEFAULT NULL,
  `event_name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`meta_id`),
  KEY `idx_doc_id` (`doc_id`),
  CONSTRAINT `fk_meta_central_file_doc` FOREIGN KEY (`doc_id`) REFERENCES `documents` (`doc_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `meta_conf_organised`
--

DROP TABLE IF EXISTS `meta_conf_organised`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `meta_conf_organised` (
  `meta_id` int(11) NOT NULL AUTO_INCREMENT,
  `doc_id` int(11) NOT NULL,
  `mode` varchar(255) DEFAULT NULL,
  `date_from` date DEFAULT NULL,
  `date_to` date DEFAULT NULL,
  `organised_by` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`meta_id`),
  KEY `idx_doc_id` (`doc_id`),
  CONSTRAINT `fk_meta_conf_organised_doc` FOREIGN KEY (`doc_id`) REFERENCES `documents` (`doc_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `meta_conference`
--

DROP TABLE IF EXISTS `meta_conference`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `meta_conference` (
  `meta_id` int(11) NOT NULL AUTO_INCREMENT,
  `doc_id` int(11) NOT NULL,
  `paper_title` varchar(255) DEFAULT NULL,
  `conference_name` varchar(255) DEFAULT NULL,
  `published_paper_name` varchar(255) DEFAULT NULL,
  `authors` text DEFAULT NULL,
  `paper_type` varchar(255) DEFAULT NULL,
  `volume_no` varchar(255) DEFAULT NULL,
  `issue_no` varchar(255) DEFAULT NULL,
  `page_no` varchar(255) DEFAULT NULL,
  `indexing` varchar(255) DEFAULT NULL,
  `publication_link` varchar(255) DEFAULT NULL,
  `issn_no` varchar(255) DEFAULT NULL,
  `doi` varchar(255) DEFAULT NULL,
  `from_date` date DEFAULT NULL,
  `to_date` date DEFAULT NULL,
  `organised_by` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`meta_id`),
  KEY `idx_doc_id` (`doc_id`),
  CONSTRAINT `fk_meta_conference_doc` FOREIGN KEY (`doc_id`) REFERENCES `documents` (`doc_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `meta_criteria_file`
--

DROP TABLE IF EXISTS `meta_criteria_file`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `meta_criteria_file` (
  `meta_id` int(11) NOT NULL AUTO_INCREMENT,
  `doc_id` int(11) NOT NULL,
  `criteria_no` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `semester` varchar(255) DEFAULT NULL,
  `section` varchar(255) DEFAULT NULL,
  `ext_or_int` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`meta_id`),
  KEY `idx_doc_id` (`doc_id`),
  CONSTRAINT `fk_meta_criteria_file_doc` FOREIGN KEY (`doc_id`) REFERENCES `documents` (`doc_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `meta_dept_file`
--

DROP TABLE IF EXISTS `meta_dept_file`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `meta_dept_file` (
  `meta_id` int(11) NOT NULL AUTO_INCREMENT,
  `doc_id` int(11) NOT NULL,
  `file_type` varchar(255) DEFAULT NULL,
  `sub_file_type` varchar(255) DEFAULT NULL,
  `semester` varchar(255) DEFAULT NULL,
  `review_period` varchar(255) DEFAULT NULL,
  `study_year` varchar(255) DEFAULT NULL,
  `meeting_no` varchar(255) DEFAULT NULL,
  `event_details` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`event_details`)),
  PRIMARY KEY (`meta_id`),
  KEY `idx_doc_id` (`doc_id`),
  CONSTRAINT `fk_meta_dept_file_doc` FOREIGN KEY (`doc_id`) REFERENCES `documents` (`doc_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `meta_exam_qual`
--

DROP TABLE IF EXISTS `meta_exam_qual`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `meta_exam_qual` (
  `meta_id` int(11) NOT NULL AUTO_INCREMENT,
  `doc_id` int(11) NOT NULL,
  `reg_no` varchar(255) DEFAULT NULL,
  `exam` varchar(255) DEFAULT NULL,
  `exam_status` varchar(255) DEFAULT NULL,
  `rank` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`meta_id`),
  KEY `idx_doc_id` (`doc_id`),
  CONSTRAINT `fk_meta_exam_qual_doc` FOREIGN KEY (`doc_id`) REFERENCES `documents` (`doc_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `meta_fdp_attended`
--

DROP TABLE IF EXISTS `meta_fdp_attended`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `meta_fdp_attended` (
  `meta_id` int(11) NOT NULL AUTO_INCREMENT,
  `doc_id` int(11) NOT NULL,
  `date_from` date DEFAULT NULL,
  `date_to` date DEFAULT NULL,
  `organised_by` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `mode` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`meta_id`),
  KEY `idx_doc_id` (`doc_id`),
  CONSTRAINT `fk_meta_fdp_attended_doc` FOREIGN KEY (`doc_id`) REFERENCES `documents` (`doc_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `meta_fdp_organised`
--

DROP TABLE IF EXISTS `meta_fdp_organised`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `meta_fdp_organised` (
  `meta_id` int(11) NOT NULL AUTO_INCREMENT,
  `doc_id` int(11) NOT NULL,
  `mode` varchar(50) DEFAULT NULL,
  `date_from` date DEFAULT NULL,
  `date_to` date DEFAULT NULL,
  `organised_by` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`meta_id`),
  KEY `idx_doc_id` (`doc_id`),
  CONSTRAINT `fk_meta_fdp_organised_doc` FOREIGN KEY (`doc_id`) REFERENCES `documents` (`doc_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `meta_higher_ed`
--

DROP TABLE IF EXISTS `meta_higher_ed`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `meta_higher_ed` (
  `meta_id` int(11) NOT NULL AUTO_INCREMENT,
  `doc_id` int(11) NOT NULL,
  `student_name` varchar(255) DEFAULT NULL,
  `programme` varchar(255) DEFAULT NULL,
  `institution` varchar(255) DEFAULT NULL,
  `admitted_programme` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`meta_id`),
  KEY `idx_doc_id` (`doc_id`),
  CONSTRAINT `fk_meta_higher_ed_doc` FOREIGN KEY (`doc_id`) REFERENCES `documents` (`doc_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `meta_journal`
--

DROP TABLE IF EXISTS `meta_journal`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `meta_journal` (
  `meta_id` int(11) NOT NULL AUTO_INCREMENT,
  `doc_id` int(11) NOT NULL,
  `paper_title` varchar(255) DEFAULT NULL,
  `journal_name` varchar(255) DEFAULT NULL,
  `authors` text DEFAULT NULL,
  `issn_no` varchar(255) DEFAULT NULL,
  `volume_no` varchar(255) DEFAULT NULL,
  `issue_no` varchar(255) DEFAULT NULL,
  `page_no` varchar(255) DEFAULT NULL,
  `doi` varchar(255) DEFAULT NULL,
  `jcr_quartile` varchar(50) DEFAULT NULL,
  `scopus_quartile` varchar(255) DEFAULT NULL,
  `publication_link` varchar(255) DEFAULT NULL,
  `indexing` varchar(255) DEFAULT NULL,
  `date_of_publication` date DEFAULT NULL,
  `impact_factor` varchar(255) DEFAULT NULL,
  `quality_factor` varchar(255) DEFAULT NULL,
  `payment` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`meta_id`),
  KEY `idx_doc_id` (`doc_id`),
  CONSTRAINT `fk_meta_journal_doc` FOREIGN KEY (`doc_id`) REFERENCES `documents` (`doc_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `meta_patent`
--

DROP TABLE IF EXISTS `meta_patent`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `meta_patent` (
  `meta_id` int(11) NOT NULL AUTO_INCREMENT,
  `doc_id` int(11) NOT NULL,
  `patent_title` varchar(255) DEFAULT NULL,
  `patent_no` varchar(255) DEFAULT NULL,
  `patent_type` varchar(255) DEFAULT NULL,
  `date_of_issue` date DEFAULT NULL,
  `inventors` text DEFAULT NULL,
  PRIMARY KEY (`meta_id`),
  KEY `idx_doc_id` (`doc_id`),
  CONSTRAINT `fk_meta_patent_doc` FOREIGN KEY (`doc_id`) REFERENCES `documents` (`doc_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `meta_placement`
--

DROP TABLE IF EXISTS `meta_placement`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `meta_placement` (
  `meta_id` int(11) NOT NULL AUTO_INCREMENT,
  `doc_id` int(11) NOT NULL,
  `student_name` varchar(255) DEFAULT NULL,
  `programme` varchar(255) DEFAULT NULL,
  `employer` varchar(255) DEFAULT NULL,
  `pay` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`meta_id`),
  KEY `idx_doc_id` (`doc_id`),
  CONSTRAINT `fk_meta_placement_doc` FOREIGN KEY (`doc_id`) REFERENCES `documents` (`doc_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `meta_scholarship`
--

DROP TABLE IF EXISTS `meta_scholarship`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `meta_scholarship` (
  `meta_id` int(11) NOT NULL AUTO_INCREMENT,
  `doc_id` int(11) NOT NULL,
  `scheme_name` varchar(255) DEFAULT NULL,
  `gov_students` varchar(255) DEFAULT NULL,
  `gov_amount` varchar(255) DEFAULT NULL,
  `inst_students` varchar(255) DEFAULT NULL,
  `inst_amount` varchar(255) DEFAULT NULL,
  `ngo_students` varchar(255) DEFAULT NULL,
  `ngo_amount` varchar(255) DEFAULT NULL,
  `ngo_name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`meta_id`),
  KEY `idx_doc_id` (`doc_id`),
  CONSTRAINT `fk_meta_scholarship_doc` FOREIGN KEY (`doc_id`) REFERENCES `documents` (`doc_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `meta_student_activity_file`
--

DROP TABLE IF EXISTS `meta_student_activity_file`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `meta_student_activity_file` (
  `doc_id` int(11) NOT NULL,
  `activity_category` varchar(100) NOT NULL,
  `sub_category` varchar(200) DEFAULT NULL,
  `event_type` varchar(100) DEFAULT NULL,
  `topic_domain` varchar(200) DEFAULT NULL,
  `event_title` varchar(300) NOT NULL,
  `date_from` date DEFAULT NULL,
  `date_to` date DEFAULT NULL,
  `resource_person` text DEFAULT NULL,
  `target_audience` varchar(200) DEFAULT NULL,
  `participant_count` int(11) DEFAULT NULL,
  `location` varchar(200) DEFAULT NULL,
  `event_mode` varchar(50) DEFAULT NULL,
  `objective_outcome` text DEFAULT NULL,
  PRIMARY KEY (`doc_id`),
  CONSTRAINT `meta_student_activity_file_ibfk_1` FOREIGN KEY (`doc_id`) REFERENCES `documents` (`doc_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `meta_student_body`
--

DROP TABLE IF EXISTS `meta_student_body`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `meta_student_body` (
  `meta_id` int(11) NOT NULL AUTO_INCREMENT,
  `doc_id` int(11) NOT NULL,
  `body_name` varchar(255) DEFAULT NULL,
  `event_name` varchar(255) DEFAULT NULL,
  `from_date` date DEFAULT NULL,
  `to_date` date DEFAULT NULL,
  `organised_by` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `participation_status` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`meta_id`),
  KEY `idx_doc_id` (`doc_id`),
  CONSTRAINT `fk_meta_student_body_doc` FOREIGN KEY (`doc_id`) REFERENCES `documents` (`doc_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `meta_student_conference`
--

DROP TABLE IF EXISTS `meta_student_conference`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `meta_student_conference` (
  `meta_id` int(11) NOT NULL AUTO_INCREMENT,
  `doc_id` int(11) NOT NULL,
  `paper_title` varchar(255) DEFAULT NULL,
  `paper_type` varchar(255) DEFAULT NULL,
  `from_date` date DEFAULT NULL,
  `to_date` date DEFAULT NULL,
  `organised_by` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`meta_id`),
  KEY `idx_doc_id` (`doc_id`),
  CONSTRAINT `fk_meta_student_conference_doc` FOREIGN KEY (`doc_id`) REFERENCES `documents` (`doc_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `meta_student_event`
--

DROP TABLE IF EXISTS `meta_student_event`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `meta_student_event` (
  `meta_id` int(11) NOT NULL AUTO_INCREMENT,
  `doc_id` int(11) NOT NULL,
  `activity` varchar(255) DEFAULT NULL,
  `event_name` varchar(255) DEFAULT NULL,
  `from_date` date DEFAULT NULL,
  `to_date` date DEFAULT NULL,
  `organised_by` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `participation_status` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`meta_id`),
  KEY `idx_doc_id` (`doc_id`),
  CONSTRAINT `fk_meta_student_event_doc` FOREIGN KEY (`doc_id`) REFERENCES `documents` (`doc_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `meta_student_journal`
--

DROP TABLE IF EXISTS `meta_student_journal`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `meta_student_journal` (
  `meta_id` int(11) NOT NULL AUTO_INCREMENT,
  `doc_id` int(11) NOT NULL,
  `paper_title` varchar(255) DEFAULT NULL,
  `journal_name` varchar(255) DEFAULT NULL,
  `indexing` varchar(255) DEFAULT NULL,
  `date_of_submission` date DEFAULT NULL,
  `impact_factor` varchar(255) DEFAULT NULL,
  `quality_factor` varchar(255) DEFAULT NULL,
  `payment` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`meta_id`),
  KEY `idx_doc_id` (`doc_id`),
  CONSTRAINT `fk_meta_student_journal_doc` FOREIGN KEY (`doc_id`) REFERENCES `documents` (`doc_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `naac_criteria_data`
--

DROP TABLE IF EXISTS `naac_criteria_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `naac_criteria_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `submission_id` int(11) NOT NULL,
  `criterion_number` int(11) NOT NULL,
  `data_json` longtext NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `sub_crit` (`submission_id`,`criterion_number`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `naac_form_schemas`
--

DROP TABLE IF EXISTS `naac_form_schemas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `naac_form_schemas` (
  `schema_id` int(11) NOT NULL AUTO_INCREMENT,
  `criterion_number` int(11) NOT NULL,
  `version_name` varchar(50) NOT NULL DEFAULT 'v1',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `schema_json` longtext NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`schema_id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `naac_submissions`
--

DROP TABLE IF EXISTS `naac_submissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `naac_submissions` (
  `submission_id` int(11) NOT NULL AUTO_INCREMENT,
  `dept_id` int(11) NOT NULL,
  `academic_year` varchar(20) NOT NULL,
  `status` varchar(50) DEFAULT 'Draft',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`submission_id`),
  UNIQUE KEY `dept_year` (`dept_id`,`academic_year`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nba_criteria`
--

DROP TABLE IF EXISTS `nba_criteria`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `nba_criteria` (
  `SI_no` int(10) NOT NULL,
  `Sub_no` varchar(30) NOT NULL,
  `Des` varchar(600) NOT NULL,
  `year` varchar(200) NOT NULL,
  PRIMARY KEY (`Sub_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nba_criteria1`
--

DROP TABLE IF EXISTS `nba_criteria1`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `nba_criteria1` (
  `SI_no` int(10) NOT NULL,
  `Sub_no` varchar(30) NOT NULL,
  `Des` varchar(600) NOT NULL,
  `year` varchar(200) NOT NULL,
  PRIMARY KEY (`Sub_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nba_criteria2`
--

DROP TABLE IF EXISTS `nba_criteria2`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `nba_criteria2` (
  `SI_no` int(10) NOT NULL,
  `Sub_no` varchar(30) NOT NULL,
  `Des` varchar(600) NOT NULL,
  `year` varchar(200) NOT NULL,
  PRIMARY KEY (`Sub_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nba_criteria_data`
--

DROP TABLE IF EXISTS `nba_criteria_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `nba_criteria_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `submission_id` int(11) NOT NULL,
  `criterion_number` int(11) NOT NULL,
  `data_json` longtext DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_sub_crit` (`submission_id`,`criterion_number`),
  CONSTRAINT `fk_nba_data_sub` FOREIGN KEY (`submission_id`) REFERENCES `nba_submissions` (`submission_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nba_criteria_files`
--

DROP TABLE IF EXISTS `nba_criteria_files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `nba_criteria_files` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `submission_id` int(11) NOT NULL,
  `criterion_number` int(11) NOT NULL,
  `subsection` varchar(20) NOT NULL,
  `file_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_nba_file_sub` (`submission_id`),
  KEY `fk_nba_file_fid` (`file_id`),
  CONSTRAINT `fk_nba_file_fid` FOREIGN KEY (`file_id`) REFERENCES `document_files` (`file_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_nba_file_sub` FOREIGN KEY (`submission_id`) REFERENCES `nba_submissions` (`submission_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nba_submissions`
--

DROP TABLE IF EXISTS `nba_submissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `nba_submissions` (
  `submission_id` int(11) NOT NULL AUTO_INCREMENT,
  `dept_id` int(11) NOT NULL,
  `academic_year` varchar(20) NOT NULL,
  `status` enum('Draft','Submitted','Approved') NOT NULL DEFAULT 'Draft',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`submission_id`),
  UNIQUE KEY `uk_dept_year` (`dept_id`,`academic_year`),
  CONSTRAINT `fk_nba_sub_dept` FOREIGN KEY (`dept_id`) REFERENCES `departments` (`dept_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
  `type` varchar(100) DEFAULT NULL,
  `date_of_issue` varchar(100) NOT NULL,
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
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `roles` (
  `role_id` int(11) NOT NULL AUTO_INCREMENT,
  `role_name` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`role_id`),
  UNIQUE KEY `role_name` (`role_name`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
-- Table structure for table `user_profiles`
--

DROP TABLE IF EXISTS `user_profiles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_profiles` (
  `profile_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `per_no` varchar(50) NOT NULL,
  `pan_no` varchar(20) NOT NULL,
  `apaar_id` varchar(50) DEFAULT NULL,
  `highest_degree` varchar(100) NOT NULL,
  `university` varchar(150) NOT NULL,
  `specialization` varchar(150) NOT NULL,
  `doj_institution` date NOT NULL,
  `doj_department` date DEFAULT NULL,
  `experience_years` decimal(4,1) DEFAULT 0.0,
  `designation_joining` varchar(100) NOT NULL,
  `designation_present` varchar(100) NOT NULL,
  `date_designated_prof` date DEFAULT NULL,
  `association_nature` enum('Regular','Contract','Ad hoc') NOT NULL,
  `contract_type` enum('Full time','Part time','Hourly based') DEFAULT NULL,
  `is_currently_associated` tinyint(1) NOT NULL DEFAULT 1,
  `date_of_leaving` date DEFAULT NULL,
  `profile_photo` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`profile_id`),
  UNIQUE KEY `uk_user_id` (`user_id`),
  CONSTRAINT `fk_profile_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
  UNIQUE KEY `uq_user_role_dept` (`user_id`,`role_id`,`dept_id`),
  KEY `role_id` (`role_id`),
  KEY `dept_id` (`dept_id`),
  CONSTRAINT `user_roles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  CONSTRAINT `user_roles_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`),
  CONSTRAINT `user_roles_ibfk_3` FOREIGN KEY (`dept_id`) REFERENCES `departments` (`dept_id`)
) ENGINE=InnoDB AUTO_INCREMENT=74 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `full_name` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_login` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=73 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
  `scope` enum('department','global') DEFAULT 'global',
  PRIMARY KEY (`step_id`),
  UNIQUE KEY `uq_workflow_step` (`workflow_id`,`step_order`),
  KEY `responsible_role_id` (`responsible_role_id`),
  CONSTRAINT `workflow_steps_ibfk_1` FOREIGN KEY (`workflow_id`) REFERENCES `workflows` (`workflow_id`),
  CONSTRAINT `workflow_steps_ibfk_2` FOREIGN KEY (`responsible_role_id`) REFERENCES `roles` (`role_id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
  `label` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  PRIMARY KEY (`workflow_id`),
  UNIQUE KEY `workflow_key` (`workflow_key`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping routines for database 'gmrdufms'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-10-26  0:22:19


-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: gmrdufms
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'Admin','System administrator'),(2,'IQAC','IQAC coordinator'),(3,'HOD','Head of Department'),(4,'Faculty','Faculty member'),(5,'Coordinator','Department/Cell coordinator'),(6,'Central_Coordinator','Central repository coordinator'),(7,'Junior_Assistant','Administrative assistant'),(8,'RnD_Dean','Dean of Research and Development'),(9,'Student','Student Role');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `departments`
--

LOCK TABLES `departments` WRITE;
/*!40000 ALTER TABLE `departments` DISABLE KEYS */;
INSERT INTO `departments` VALUES (1,'CSE',1),(2,'AIDS',1),(3,'AIML',1),(4,'ECE',1),(5,'EEE',1),(6,'MECH',1),(7,'CIVIL',1),(8,'IT',1),(9,'BSH',1),(10,'CSE-CS',1),(11,'PE',0),(12,'PG',0),(14,'Mathematics',1),(15,'Physics',1),(16,'Chemistry',1),(17,'IQAC',0),(18,'R&D',0),(19,'Exam_Section',0),(20,'NAAC',0),(21,'NBA',0),(22,'NCC',0),(23,'Sports',0),(24,'Clubs',0),(25,'NSS',0),(26,'Women_Empowerment',0),(27,'IIC',0),(28,'PASH',0),(29,'Antiragging',0),(30,'SAC',0),(31,'Complete University',1);
/*!40000 ALTER TABLE `departments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `document_types`
--

LOCK TABLES `document_types` WRITE;
/*!40000 ALTER TABLE `document_types` DISABLE KEYS */;
INSERT INTO `document_types` VALUES (1,'fdp','Faculty Development Program',1),(2,'patent','Patent Publication',1),(3,'journal','Journal Paper',1),(4,'conference','Conference Proceedings Published Form',1),(6,'fdp_attended','FDP Attended',1),(7,'fdp_organised','FDP Organised',1),(8,'conf_organised','Conference Organised',1),(9,'criteria_file','Criteria File',1),(10,'dept_file','Department File',5),(11,'central_file','Central File',1),(12,'scholarship','Scholarship',1),(13,'placement','Placement',1),(14,'higher_ed','Higher Education',1),(15,'exam_qual','Exam Qualification',1),(16,'award','Award',1),(17,'student_event','Student Event',1),(18,'student_body','Student Body',1),(19,'student_journal','Student Journal',1),(20,'student_conference','Student Conference',1),(21,'stu_act','Student Activity',4),(29,'student_activity_file','Student Activity File',5);
/*!40000 ALTER TABLE `document_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `workflows`
--

LOCK TABLES `workflows` WRITE;
/*!40000 ALTER TABLE `workflows` DISABLE KEYS */;
INSERT INTO `workflows` VALUES (1,'department_standard','Standard Department Review',NULL),(4,'student_activity_wf','Student Activity Workflow',NULL),(5,'department_internal','Internal Department Review','Faculty uploads -> Dept Coordinator -> HOD -> Accepted');
/*!40000 ALTER TABLE `workflows` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `workflow_steps`
--

LOCK TABLES `workflow_steps` WRITE;
/*!40000 ALTER TABLE `workflow_steps` DISABLE KEYS */;
INSERT INTO `workflow_steps` VALUES (1,1,1,'Dept Coordinator Review',5,'department'),(2,1,2,'HOD Review',3,'department'),(3,1,3,'R&D Dean Review',8,'global'),(10,4,1,'Coordinator Review',5,'global'),(11,5,1,'Dept Coordinator Review',5,'department'),(12,5,2,'HOD Review',3,'department');
/*!40000 ALTER TABLE `workflow_steps` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `workflow_transitions`
--

LOCK TABLES `workflow_transitions` WRITE;
/*!40000 ALTER TABLE `workflow_transitions` DISABLE KEYS */;
INSERT INTO `workflow_transitions` VALUES (1,1,1,2,'pending'),(2,1,2,NULL,'rejected'),(3,2,1,3,'pending'),(4,2,2,NULL,'rejected'),(5,3,1,NULL,'accepted'),(6,3,2,NULL,'rejected'),(7,1,3,1,'pending'),(8,2,3,1,'pending'),(9,3,3,1,'pending'),(10,10,1,NULL,'accepted'),(11,10,2,NULL,'rejected'),(12,11,1,12,'pending'),(13,11,2,NULL,'rejected'),(14,11,3,11,'pending'),(15,12,1,NULL,'accepted'),(16,12,2,NULL,'rejected'),(17,12,3,11,'pending');
/*!40000 ALTER TABLE `workflow_transitions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `workflow_actions`
--

LOCK TABLES `workflow_actions` WRITE;
/*!40000 ALTER TABLE `workflow_actions` DISABLE KEYS */;
INSERT INTO `workflow_actions` VALUES (1,'approve','Approve'),(2,'reject','Reject'),(3,'resubmit','Resubmit');
/*!40000 ALTER TABLE `workflow_actions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `academic_years`
--

LOCK TABLES `academic_years` WRITE;
/*!40000 ALTER TABLE `academic_years` DISABLE KEYS */;
INSERT INTO `academic_years` VALUES (2,'2023-24'),(1,'2024-25'),(3,'2025-26'),(5,'2026-27');
/*!40000 ALTER TABLE `academic_years` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `naac_form_schemas`
--

LOCK TABLES `naac_form_schemas` WRITE;
/*!40000 ALTER TABLE `naac_form_schemas` DISABLE KEYS */;
INSERT INTO `naac_form_schemas` VALUES (1,1,'v1',0,'{\"sections\":[{\"title\":\"1.1 Curricular Planning and Implementation\",\"metrics\":[{\"id\":\"1.1.1\",\"title\":\"1.1.1: Effective curriculum planning and delivery\",\"description\":\"The Institution ensures effective curriculum planning and delivery through a well-planned and documented process including Academic calendar and conduct of continuous internal Assessment. Write description in maximum of 500 words.\",\"fields\":[{\"name\":\"qnm_1_1_1_text\",\"label\":\"Description\",\"type\":\"textarea\",\"placeholder\":\"Describe the process of curriculum planning and delivery...\"},{\"name\":\"file_1_1_1\",\"label\":\"Supporting Document (Academic Calendar, Delivery Plans, etc.)\",\"type\":\"file\",\"accept\":\".pdf\",\"description\":\"Please upload a combined PDF.\"}]}]},{\"title\":\"1.2 Academic Flexibility\",\"metrics\":[{\"id\":\"1.2.1\",\"title\":\"1.2.1: Certificate\\/Value added courses\",\"description\":\"Number of Certificate\\/Value added courses offered and online courses of MOOCs, SWAYAM, NPTEL etc. (where the students of the institution have enrolled and successfully completed).\",\"fields\":[{\"name\":\"qnm_1_2_1_count\",\"label\":\"Total Number of Courses\",\"type\":\"number\",\"placeholder\":\"Enter number of courses\"},{\"name\":\"file_1_2_1\",\"label\":\"Institutional Data \\/ List of Courses\",\"type\":\"file\",\"accept\":\".pdf\",\"description\":\"Upload PDF containing course list and details.\"}]},{\"id\":\"1.2.2\",\"title\":\"1.2.2: Percentage of students enrolled in these courses\",\"description\":\"Percentage of students enrolled in Certificate\\/ Value added courses and also completed online courses of MOOCs, SWAYAM, NPTEL etc.\",\"fields\":[{\"name\":\"qnm_1_2_2_enrolled\",\"label\":\"Total Students Enrolled\",\"type\":\"number\",\"placeholder\":\"Enrolled students\"},{\"name\":\"qnm_1_2_2_total\",\"label\":\"Total Students in Institution\",\"type\":\"number\",\"placeholder\":\"Total students\"},{\"name\":\"file_1_2_2\",\"label\":\"Attendance \\/ Certificates Proof\",\"type\":\"file\",\"accept\":\".pdf\",\"description\":\"Upload attendance sheets or sample certificates as a single PDF.\"}]}]},{\"title\":\"1.3 Curriculum Enrichment\",\"metrics\":[{\"id\":\"1.3.1\",\"title\":\"1.3.1: Integration of Crosscutting Issues\",\"description\":\"Institution integrates crosscutting issues relevant to Professional Ethics, Gender, Human Values, Environment and Sustainability into the Curriculum. Write description in maximum of 500 words.\",\"fields\":[{\"name\":\"qnm_1_3_1_text\",\"label\":\"Description\",\"type\":\"textarea\",\"placeholder\":\"Describe how these issues are integrated...\"},{\"name\":\"file_1_3_1\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\",\"description\":\"Upload list of relevant courses and syllabus copy.\"}]},{\"id\":\"1.3.2\",\"title\":\"1.3.2: Project Work \\/ Field Work \\/ Internships\",\"description\":\"Percentage of students undertaking project work\\/field work\\/ internships.\",\"fields\":[{\"name\":\"qnm_1_3_2_count\",\"label\":\"Number of students undertaking project\\/field work\\/internships\",\"type\":\"number\",\"placeholder\":\"Number of students\"},{\"name\":\"file_1_3_2\",\"label\":\"List of Students & Completion Certificates\",\"type\":\"file\",\"accept\":\".pdf\",\"description\":\"Upload relevant PDF proofs.\"}]}]},{\"title\":\"1.4 Feedback System\",\"metrics\":[{\"id\":\"1.4.1\",\"title\":\"1.4.1: Structured Feedback System\",\"description\":\"Institution obtains feedback on the academic performance and ambience of the institution from various stakeholders and action taken report is on the website.\",\"fields\":[{\"name\":\"feedback_stakeholders\",\"label\":\"Feedback collected from (Check all that apply):\",\"type\":\"checkbox_group\",\"options\":[{\"name\":\"fb_students\",\"label\":\"Students\"},{\"name\":\"fb_teachers\",\"label\":\"Teachers\"},{\"name\":\"fb_employers\",\"label\":\"Employers\"},{\"name\":\"fb_alumni\",\"label\":\"Alumni\"}]},{\"name\":\"qnm_1_4_1_url\",\"label\":\"Action Taken Report URL (on institutional website)\",\"type\":\"url\",\"placeholder\":\"https:\\/\\/...\"},{\"name\":\"file_1_4_1\",\"label\":\"Feedback Analysis & Action Taken Report\",\"type\":\"file\",\"accept\":\".pdf\",\"description\":\"Upload the official signed PDF.\"}]}]}]}','2026-10-07 15:29:06'),(2,2,'v1',0,'{\"sections\":[{\"title\":\"2.1 Student Enrolment and Profile\",\"metrics\":[{\"id\":\"2.1.1\",\"title\":\"2.1.1: Enrolment Percentage\",\"description\":\"Average enrolment percentage (Students admitted vs sanctioned seats).\",\"fields\":[{\"name\":\"qnm_2_1_1_admitted\",\"label\":\"Number of students admitted\",\"type\":\"number\"},{\"name\":\"qnm_2_1_1_sanctioned\",\"label\":\"Number of sanctioned seats\",\"type\":\"number\"},{\"name\":\"file_2_1_1\",\"label\":\"Document Upload\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"2.1.2\",\"title\":\"2.1.2: Reserved Categories\",\"description\":\"Average percentage of seats filled against reserved categories (SC, ST, OBC, Divyangjan, etc.).\",\"fields\":[{\"name\":\"qnm_2_1_2_filled\",\"label\":\"Seats filled against reserved categories\",\"type\":\"number\"},{\"name\":\"qnm_2_1_2_reserved\",\"label\":\"Total seats earmarked for reserved categories\",\"type\":\"number\"},{\"name\":\"file_2_1_2\",\"label\":\"Document Upload\",\"type\":\"file\",\"accept\":\".pdf\"}]}]},{\"title\":\"2.2 Catering to Student Diversity\",\"metrics\":[{\"id\":\"2.2.1\",\"title\":\"2.2.1: Advanced and Slow Learners\",\"description\":\"The institution assesses the learning levels of the students and organizes special Programmes for advanced learners and slow learners. Write description in max 500 words.\",\"fields\":[{\"name\":\"qlm_2_2_1_text\",\"label\":\"Description\",\"type\":\"textarea\"},{\"name\":\"file_2_2_1\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"2.2.2\",\"title\":\"2.2.2: Student-Teacher Ratio\",\"description\":\"Student - Full time teacher ratio (Data for the latest completed academic year).\",\"fields\":[{\"name\":\"qnm_2_2_2_students\",\"label\":\"Total Number of Students\",\"type\":\"number\"},{\"name\":\"qnm_2_2_2_teachers\",\"label\":\"Total Number of Full-Time Teachers\",\"type\":\"number\"}]}]},{\"title\":\"2.3 Teaching-Learning Process\",\"metrics\":[{\"id\":\"2.3.1\",\"title\":\"2.3.1: Student-centric methods\",\"description\":\"Student centric methods, such as experiential learning, participative learning and problem solving methodologies are used for enhancing learning experiences.\",\"fields\":[{\"name\":\"qlm_2_3_1_text\",\"label\":\"Description\",\"type\":\"textarea\"},{\"name\":\"file_2_3_1\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"2.3.2\",\"title\":\"2.3.2: ICT Tools\",\"description\":\"Teachers use ICT enabled tools for effective teaching-learning process.\",\"fields\":[{\"name\":\"qlm_2_3_2_text\",\"label\":\"Description of ICT tools used\",\"type\":\"textarea\"},{\"name\":\"file_2_3_2\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"2.3.3\",\"title\":\"2.3.3: Mentor-Mentee Ratio\",\"description\":\"Ratio of mentor to students for academic and other related issues.\",\"fields\":[{\"name\":\"qnm_2_3_3_mentors\",\"label\":\"Number of Mentors\",\"type\":\"number\"},{\"name\":\"qnm_2_3_3_students\",\"label\":\"Number of Students assigned to mentors\",\"type\":\"number\"},{\"name\":\"file_2_3_3\",\"label\":\"Mentor-Mentee List Document\",\"type\":\"file\",\"accept\":\".pdf\"}]}]},{\"title\":\"2.4 Teacher Profile and Quality\",\"metrics\":[{\"id\":\"2.4.1\",\"title\":\"2.4.1: Full-time teachers against sanctioned posts\",\"description\":\"Average percentage of full time teachers against sanctioned posts.\",\"fields\":[{\"name\":\"qnm_2_4_1_teachers\",\"label\":\"Number of Full-Time Teachers\",\"type\":\"number\"},{\"name\":\"qnm_2_4_1_sanctioned\",\"label\":\"Number of Sanctioned Posts\",\"type\":\"number\"},{\"name\":\"file_2_4_1\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"2.4.2\",\"title\":\"2.4.2: Teachers with Ph.D.\",\"description\":\"Average percentage of full time teachers with Ph. D. \\/ D.M. \\/ M.Ch. \\/ D.N.B Superspeciality \\/ D.Sc. \\/ D.Litt.\",\"fields\":[{\"name\":\"qnm_2_4_2_phd\",\"label\":\"Number of teachers with Ph.D. etc.\",\"type\":\"number\"},{\"name\":\"file_2_4_2\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]}]},{\"title\":\"2.5 Evaluation Process and Reforms\",\"metrics\":[{\"id\":\"2.5.1\",\"title\":\"2.5.1: CIE Reforms\",\"description\":\"Mechanism of internal assessment is transparent and robust in terms of frequency and mode.\",\"fields\":[{\"name\":\"qlm_2_5_1_text\",\"label\":\"Description\",\"type\":\"textarea\"},{\"name\":\"file_2_5_1\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]}]},{\"title\":\"2.6 Student Performance and Learning Outcomes\",\"metrics\":[{\"id\":\"2.6.1\",\"title\":\"2.6.1: Outcomes definition and publication\",\"description\":\"Programme and course outcomes for all Programmes offered by the institution are stated and displayed on website and communicated to teachers and students.\",\"fields\":[{\"name\":\"qlm_2_6_1_text\",\"label\":\"Description\",\"type\":\"textarea\"},{\"name\":\"qlm_2_6_1_url\",\"label\":\"Website URL link for Outcomes\",\"type\":\"url\"},{\"name\":\"file_2_6_1\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"2.6.3\",\"title\":\"2.6.3: Pass percentage\",\"description\":\"Pass percentage of Students during last five years.\",\"fields\":[{\"name\":\"qnm_2_6_3_passed\",\"label\":\"Number of students passed in final year exam\",\"type\":\"number\"},{\"name\":\"qnm_2_6_3_appeared\",\"label\":\"Number of students appeared in final year exam\",\"type\":\"number\"},{\"name\":\"file_2_6_3\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]}]}]}','2026-10-07 15:35:57'),(3,1,'v2_autonomous',0,'{\"sections\":[{\"title\":\"1.1 Curriculum Design and Development\",\"metrics\":[{\"id\":\"1.1.1\",\"title\":\"1.1.1: Curricula developed and implemented have relevance to needs\",\"description\":\"Curricula developed and implemented have relevance to the local, national, regional and global developmental needs which is reflected in POs, PSOs and COs.\",\"fields\":[{\"name\":\"qlm_1_1_1_text\",\"label\":\"Description (Max 500 words)\",\"type\":\"textarea\"},{\"name\":\"file_1_1_1\",\"label\":\"Document Upload\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"1.1.2\",\"title\":\"1.1.2: Syllabus revision\",\"description\":\"Percentage of Programmes where syllabus revision was carried out during the last five years.\",\"fields\":[{\"name\":\"qnm_1_1_2_revised\",\"label\":\"Number of programmes where syllabus was revised\",\"type\":\"number\"},{\"name\":\"qnm_1_1_2_total\",\"label\":\"Total number of programmes offered\",\"type\":\"number\"},{\"name\":\"file_1_1_2\",\"label\":\"Minutes of relevant Academic Council\\/BOS meeting\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"1.1.3\",\"title\":\"1.1.3: Courses focusing on employability\\/entrepreneurship\\/skill development\",\"description\":\"Average percentage of courses having focus on employability\\/entrepreneurship\\/skill development.\",\"fields\":[{\"name\":\"qnm_1_1_3_focused\",\"label\":\"Number of courses focusing on employability\\/entrepreneurship\\/skill development\",\"type\":\"number\"},{\"name\":\"qnm_1_1_3_total\",\"label\":\"Total number of courses in all programmes\",\"type\":\"number\"},{\"name\":\"file_1_1_3\",\"label\":\"Programme\\/Curriculum\\/Syllabus of courses\",\"type\":\"file\",\"accept\":\".pdf\"}]}]},{\"title\":\"1.2 Academic Flexibility\",\"metrics\":[{\"id\":\"1.2.1\",\"title\":\"1.2.1: New courses introduced\",\"description\":\"Percentage of new courses introduced of the total number of courses across all Programmes offered during the last five years.\",\"fields\":[{\"name\":\"qnm_1_2_1_new\",\"label\":\"Number of new courses introduced\",\"type\":\"number\"},{\"name\":\"qnm_1_2_1_total\",\"label\":\"Total number of courses offered\",\"type\":\"number\"},{\"name\":\"file_1_2_1\",\"label\":\"Document Upload\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"1.2.2\",\"title\":\"1.2.2: Choice Based Credit System (CBCS)\\/elective course system\",\"description\":\"Percentage of Programmes in which CBCS\\/elective course system has been implemented.\",\"fields\":[{\"name\":\"qnm_1_2_2_cbcs\",\"label\":\"Number of programmes in which CBCS\\/elective course system implemented\",\"type\":\"number\"},{\"name\":\"qnm_1_2_2_total\",\"label\":\"Total number of programmes offered\",\"type\":\"number\"},{\"name\":\"file_1_2_2\",\"label\":\"Document Upload\",\"type\":\"file\",\"accept\":\".pdf\"}]}]},{\"title\":\"1.3 Curriculum Enrichment\",\"metrics\":[{\"id\":\"1.3.1\",\"title\":\"1.3.1: Integration of crosscutting issues\",\"description\":\"Institution integrates crosscutting issues relevant to Professional Ethics, Gender, Human Values, Environment and Sustainability into the Curriculum.\",\"fields\":[{\"name\":\"qlm_1_3_1_text\",\"label\":\"Description (Max 500 words)\",\"type\":\"textarea\"},{\"name\":\"file_1_3_1\",\"label\":\"Document Upload\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"1.3.2\",\"title\":\"1.3.2: Value-added courses\",\"description\":\"Number of value-added courses imparting transferable and life skills offered during the last five years.\",\"fields\":[{\"name\":\"qnm_1_3_2_courses\",\"label\":\"Number of value-added courses\",\"type\":\"number\"},{\"name\":\"file_1_3_2\",\"label\":\"Document Upload\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"1.3.3\",\"title\":\"1.3.3: Students enrolled in value-added courses\",\"description\":\"Average Percentage of students enrolled in the value-added courses (from 1.3.2).\",\"fields\":[{\"name\":\"qnm_1_3_3_enrolled\",\"label\":\"Number of students enrolled\",\"type\":\"number\"},{\"name\":\"qnm_1_3_3_total\",\"label\":\"Total number of students\",\"type\":\"number\"},{\"name\":\"file_1_3_3\",\"label\":\"Document Upload\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"1.3.4\",\"title\":\"1.3.4: Field projects\\/ internships\\/ student projects\",\"description\":\"Percentage of students undertaking field projects\\/ internships \\/ student projects.\",\"fields\":[{\"name\":\"qnm_1_3_4_undertaking\",\"label\":\"Number of students undertaking projects\\/internships\",\"type\":\"number\"},{\"name\":\"qnm_1_3_4_total\",\"label\":\"Total number of students\",\"type\":\"number\"},{\"name\":\"file_1_3_4\",\"label\":\"Document Upload\",\"type\":\"file\",\"accept\":\".pdf\"}]}]},{\"title\":\"1.4 Feedback System\",\"metrics\":[{\"id\":\"1.4.1\",\"title\":\"1.4.1: Structured feedback for design and review of syllabus\",\"description\":\"Structured feedback for design and review of syllabus received from Stakeholders.\",\"fields\":[{\"name\":\"feedback_stakeholders\",\"label\":\"Feedback received from (Check all that apply):\",\"type\":\"checkbox_group\",\"options\":[{\"name\":\"fb_students\",\"label\":\"Students\"},{\"name\":\"fb_teachers\",\"label\":\"Teachers\"},{\"name\":\"fb_employers\",\"label\":\"Employers\"},{\"name\":\"fb_alumni\",\"label\":\"Alumni\"}]},{\"name\":\"file_1_4_1\",\"label\":\"Document Upload (Action Taken Report)\",\"type\":\"file\",\"accept\":\".pdf\"}]}]}]}','2026-10-07 15:50:40'),(4,1,'v3_university',0,'{\"sections\":[{\"title\":\"1.1 Curriculum Design and Development\",\"metrics\":[{\"id\":\"1.1.1\",\"title\":\"1.1.1: Curricula developed and implemented have relevance to needs\",\"description\":\"Curricula developed and implemented have relevance to the local, national, regional and global developmental needs which is reflected in POs, PSOs and COs.\",\"fields\":[{\"name\":\"qlm_1_1_1_text\",\"label\":\"Description (Max 500 words)\",\"type\":\"textarea\"},{\"name\":\"file_1_1_1\",\"label\":\"Document Upload\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"1.1.2\",\"title\":\"1.1.2: Syllabus revision\",\"description\":\"Percentage of Programmes where syllabus revision was carried out during the last five years.\",\"fields\":[{\"name\":\"qnm_1_1_2_revised\",\"label\":\"Number of programmes where syllabus was revised\",\"type\":\"number\"},{\"name\":\"qnm_1_1_2_total\",\"label\":\"Total number of programmes offered\",\"type\":\"number\"},{\"name\":\"file_1_1_2\",\"label\":\"Minutes of relevant Academic Council\\/BOS meeting\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"1.1.3\",\"title\":\"1.1.3: Courses focusing on employability\\/entrepreneurship\\/skill development\",\"description\":\"Average percentage of courses having focus on employability\\/entrepreneurship\\/skill development.\",\"fields\":[{\"name\":\"qnm_1_1_3_focused\",\"label\":\"Number of courses focusing on employability\\/entrepreneurship\\/skill development\",\"type\":\"number\"},{\"name\":\"qnm_1_1_3_total\",\"label\":\"Total number of courses in all programmes\",\"type\":\"number\"},{\"name\":\"file_1_1_3\",\"label\":\"Programme\\/Curriculum\\/Syllabus of courses\",\"type\":\"file\",\"accept\":\".pdf\"}]}]},{\"title\":\"1.2 Academic Flexibility\",\"metrics\":[{\"id\":\"1.2.1\",\"title\":\"1.2.1: New courses introduced\",\"description\":\"Percentage of new courses introduced of the total number of courses across all Programmes offered during the last five years.\",\"fields\":[{\"name\":\"qnm_1_2_1_new\",\"label\":\"Number of new courses introduced\",\"type\":\"number\"},{\"name\":\"qnm_1_2_1_total\",\"label\":\"Total number of courses offered\",\"type\":\"number\"},{\"name\":\"file_1_2_1\",\"label\":\"Document Upload\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"1.2.2\",\"title\":\"1.2.2: Choice Based Credit System (CBCS)\\/elective course system\",\"description\":\"Percentage of Programmes in which CBCS\\/elective course system has been implemented.\",\"fields\":[{\"name\":\"qnm_1_2_2_cbcs\",\"label\":\"Number of programmes in which CBCS\\/elective course system implemented\",\"type\":\"number\"},{\"name\":\"qnm_1_2_2_total\",\"label\":\"Total number of programmes offered\",\"type\":\"number\"},{\"name\":\"file_1_2_2\",\"label\":\"Document Upload\",\"type\":\"file\",\"accept\":\".pdf\"}]}]},{\"title\":\"1.3 Curriculum Enrichment\",\"metrics\":[{\"id\":\"1.3.1\",\"title\":\"1.3.1: Integration of crosscutting issues\",\"description\":\"Institution integrates crosscutting issues relevant to Professional Ethics, Gender, Human Values, Environment and Sustainability into the Curriculum.\",\"fields\":[{\"name\":\"qlm_1_3_1_text\",\"label\":\"Description (Max 500 words)\",\"type\":\"textarea\"},{\"name\":\"file_1_3_1\",\"label\":\"Document Upload\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"1.3.2\",\"title\":\"1.3.2: Value-added courses\",\"description\":\"Number of value-added courses imparting transferable and life skills offered during the last five years.\",\"fields\":[{\"name\":\"qnm_1_3_2_courses\",\"label\":\"Number of value-added courses\",\"type\":\"number\"},{\"name\":\"file_1_3_2\",\"label\":\"Document Upload\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"1.3.3\",\"title\":\"1.3.3: Students enrolled in value-added courses\",\"description\":\"Average Percentage of students enrolled in the value-added courses (from 1.3.2).\",\"fields\":[{\"name\":\"qnm_1_3_3_enrolled\",\"label\":\"Number of students enrolled\",\"type\":\"number\"},{\"name\":\"qnm_1_3_3_total\",\"label\":\"Total number of students\",\"type\":\"number\"},{\"name\":\"file_1_3_3\",\"label\":\"Document Upload\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"1.3.4\",\"title\":\"1.3.4: Field projects\\/ internships\\/ student projects\",\"description\":\"Percentage of students undertaking field projects\\/ internships \\/ student projects.\",\"fields\":[{\"name\":\"qnm_1_3_4_undertaking\",\"label\":\"Number of students undertaking projects\\/internships\",\"type\":\"number\"},{\"name\":\"qnm_1_3_4_total\",\"label\":\"Total number of students\",\"type\":\"number\"},{\"name\":\"file_1_3_4\",\"label\":\"Document Upload\",\"type\":\"file\",\"accept\":\".pdf\"}]}]},{\"title\":\"1.4 Feedback System\",\"metrics\":[{\"id\":\"1.4.1\",\"title\":\"1.4.1: Structured feedback for design and review of syllabus\",\"description\":\"Structured feedback for design and review of syllabus received from Stakeholders.\",\"fields\":[{\"name\":\"feedback_stakeholders\",\"label\":\"Feedback received from (Check all that apply):\",\"type\":\"checkbox_group\",\"options\":[{\"name\":\"fb_students\",\"label\":\"Students\"},{\"name\":\"fb_teachers\",\"label\":\"Teachers\"},{\"name\":\"fb_employers\",\"label\":\"Employers\"},{\"name\":\"fb_alumni\",\"label\":\"Alumni\"}]},{\"name\":\"file_1_4_1\",\"label\":\"Document Upload (Action Taken Report)\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"1.4.2\",\"title\":\"1.4.2: Feedback processes\",\"description\":\"Feedback processes of the institution.\",\"fields\":[{\"name\":\"qlm_1_4_2_process\",\"label\":\"Describe the process of collecting and analyzing feedback\",\"type\":\"textarea\"},{\"name\":\"file_1_4_2\",\"label\":\"Document Upload\",\"type\":\"file\",\"accept\":\".pdf\"}]}]}]}','2026-10-07 16:00:27'),(5,2,'v3_university',0,'{\"sections\":[{\"title\":\"2.1 Student Enrolment and Profile\",\"metrics\":[{\"id\":\"2.1.1\",\"title\":\"2.1.1: Demand Ratio\",\"description\":\"Demand Ratio: Number of seats available vs Number of eligible applications received.\",\"fields\":[{\"name\":\"qnm_2_1_1_seats\",\"label\":\"Number of seats available\",\"type\":\"number\"},{\"name\":\"qnm_2_1_1_apps\",\"label\":\"Number of eligible applications received\",\"type\":\"number\"},{\"name\":\"file_2_1_1\",\"label\":\"Document Upload\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"2.1.2\",\"title\":\"2.1.2: Reserved Categories\",\"description\":\"Average percentage of seats filled against reserved categories (SC, ST, OBC, Divyangjan, etc.).\",\"fields\":[{\"name\":\"qnm_2_1_2_filled\",\"label\":\"Seats filled against reserved categories\",\"type\":\"number\"},{\"name\":\"qnm_2_1_2_reserved\",\"label\":\"Total seats earmarked for reserved categories\",\"type\":\"number\"},{\"name\":\"file_2_1_2\",\"label\":\"Document Upload\",\"type\":\"file\",\"accept\":\".pdf\"}]}]},{\"title\":\"2.2 Catering to Student Diversity\",\"metrics\":[{\"id\":\"2.2.1\",\"title\":\"2.2.1: Advanced and Slow Learners\",\"description\":\"The institution assesses the learning levels of the students and organizes special Programmes for advanced learners and slow learners.\",\"fields\":[{\"name\":\"qlm_2_2_1_text\",\"label\":\"Description\",\"type\":\"textarea\"},{\"name\":\"file_2_2_1\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"2.2.2\",\"title\":\"2.2.2: Student-Teacher Ratio\",\"description\":\"Student - Full time teacher ratio.\",\"fields\":[{\"name\":\"qnm_2_2_2_students\",\"label\":\"Total Number of Students\",\"type\":\"number\"},{\"name\":\"qnm_2_2_2_teachers\",\"label\":\"Total Number of Full-Time Teachers\",\"type\":\"number\"}]}]},{\"title\":\"2.3 Teaching-Learning Process\",\"metrics\":[{\"id\":\"2.3.1\",\"title\":\"2.3.1: Student-centric methods\",\"description\":\"Student centric methods, such as experiential learning, participative learning and problem solving methodologies.\",\"fields\":[{\"name\":\"qlm_2_3_1_text\",\"label\":\"Description\",\"type\":\"textarea\"},{\"name\":\"file_2_3_1\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"2.3.2\",\"title\":\"2.3.2: ICT Tools\",\"description\":\"Teachers use ICT enabled tools for effective teaching-learning process.\",\"fields\":[{\"name\":\"qlm_2_3_2_text\",\"label\":\"Description of ICT tools used\",\"type\":\"textarea\"},{\"name\":\"file_2_3_2\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"2.3.3\",\"title\":\"2.3.3: Mentor-Mentee Ratio\",\"description\":\"Ratio of students to mentor for academic and other related issues.\",\"fields\":[{\"name\":\"qnm_2_3_3_mentors\",\"label\":\"Number of Mentors\",\"type\":\"number\"},{\"name\":\"qnm_2_3_3_students\",\"label\":\"Number of Students assigned to mentors\",\"type\":\"number\"},{\"name\":\"file_2_3_3\",\"label\":\"Mentor-Mentee List Document\",\"type\":\"file\",\"accept\":\".pdf\"}]}]},{\"title\":\"2.4 Teacher Profile and Quality\",\"metrics\":[{\"id\":\"2.4.1\",\"title\":\"2.4.1: Full-time teachers against sanctioned posts\",\"description\":\"Average percentage of full time teachers against sanctioned posts.\",\"fields\":[{\"name\":\"qnm_2_4_1_teachers\",\"label\":\"Number of Full-Time Teachers\",\"type\":\"number\"},{\"name\":\"qnm_2_4_1_sanctioned\",\"label\":\"Number of Sanctioned Posts\",\"type\":\"number\"},{\"name\":\"file_2_4_1\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"2.4.2\",\"title\":\"2.4.2: Teachers with Ph.D.\",\"description\":\"Average percentage of full time teachers with Ph.D.\\/D.M\\/M.Ch.\\/D.N.B Superspeciality\\/D.Sc.\\/D\'Lit.\",\"fields\":[{\"name\":\"qnm_2_4_2_phd\",\"label\":\"Number of teachers with Ph.D. etc.\",\"type\":\"number\"},{\"name\":\"file_2_4_2\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"2.4.3\",\"title\":\"2.4.3: Teaching experience\",\"description\":\"Average teaching experience of full time teachers in the same institution (Data for the latest completed academic year in number of years).\",\"fields\":[{\"name\":\"qnm_2_4_3_years\",\"label\":\"Total experience of full-time teachers (in years)\",\"type\":\"number\"},{\"name\":\"file_2_4_3\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"2.4.4\",\"title\":\"2.4.4: Teacher Awards\",\"description\":\"Average percentage of full time teachers who received awards, recognition, fellowships at State, National, International level from Government\\/Govt. recognized bodies.\",\"fields\":[{\"name\":\"qnm_2_4_4_awards\",\"label\":\"Number of teachers receiving awards\",\"type\":\"number\"},{\"name\":\"file_2_4_4\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]}]},{\"title\":\"2.5 Evaluation Process and Reforms\",\"metrics\":[{\"id\":\"2.5.1\",\"title\":\"2.5.1: Result Declaration Days\",\"description\":\"Average number of days from the date of last semester-end\\/ year-end examination till the declaration of results.\",\"fields\":[{\"name\":\"qnm_2_5_1_days\",\"label\":\"Average number of days\",\"type\":\"number\"},{\"name\":\"file_2_5_1\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"2.5.2\",\"title\":\"2.5.2: Student complaints\\/grievances\",\"description\":\"Average percentage of student complaints\\/grievances about evaluation against total number appeared in the examinations.\",\"fields\":[{\"name\":\"qnm_2_5_2_grievances\",\"label\":\"Number of complaints\\/grievances\",\"type\":\"number\"},{\"name\":\"qnm_2_5_2_appeared\",\"label\":\"Total number appeared\",\"type\":\"number\"},{\"name\":\"file_2_5_2\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"2.5.3\",\"title\":\"2.5.3: IT integration in exams\",\"description\":\"IT integration and reforms in the examination procedures and processes (continuous internal assessment and end-semester assessment) have brought in considerable improvement in examination management system.\",\"fields\":[{\"name\":\"qlm_2_5_3_text\",\"label\":\"Description\",\"type\":\"textarea\"},{\"name\":\"file_2_5_3\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]}]},{\"title\":\"2.6 Student Performance and Learning Outcomes\",\"metrics\":[{\"id\":\"2.6.1\",\"title\":\"2.6.1: Outcomes definition\",\"description\":\"Programme and course outcomes for all Programmes offered by the institution are stated and displayed on website and communicated to teachers and students.\",\"fields\":[{\"name\":\"qlm_2_6_1_text\",\"label\":\"Description\",\"type\":\"textarea\"},{\"name\":\"qlm_2_6_1_url\",\"label\":\"Website URL link for Outcomes\",\"type\":\"url\"},{\"name\":\"file_2_6_1\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"2.6.2\",\"title\":\"2.6.2: Outcomes attainment\",\"description\":\"Attainment of POs and COs are evaluated.\",\"fields\":[{\"name\":\"qlm_2_6_2_text\",\"label\":\"Description\",\"type\":\"textarea\"},{\"name\":\"file_2_6_2\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"2.6.3\",\"title\":\"2.6.3: Pass percentage\",\"description\":\"Pass percentage of Students (Data for the latest completed academic year).\",\"fields\":[{\"name\":\"qnm_2_6_3_passed\",\"label\":\"Number of students passed in final year exam\",\"type\":\"number\"},{\"name\":\"qnm_2_6_3_appeared\",\"label\":\"Number of students appeared in final year exam\",\"type\":\"number\"},{\"name\":\"file_2_6_3\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]}]}]}','2026-10-07 16:00:27'),(6,1,'v4_university_with_tables',0,'{\"sections\":[{\"title\":\"1.1 Curriculum Design and Development\",\"metrics\":[{\"id\":\"1.1.1\",\"title\":\"1.1.1: Curricula developed and implemented have relevance to needs\",\"description\":\"Curricula developed and implemented have relevance to the local, national, regional and global developmental needs which is reflected in POs, PSOs and COs.\",\"fields\":[{\"name\":\"qlm_1_1_1_text\",\"label\":\"Description (Max 500 words)\",\"type\":\"textarea\"},{\"name\":\"file_1_1_1\",\"label\":\"Document Upload\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"1.1.2\",\"title\":\"1.1.2: Syllabus revision\",\"description\":\"Percentage of Programmes where syllabus revision was carried out during the last five years.\",\"fields\":[{\"name\":\"qnm_1_1_2_revised\",\"label\":\"Number of programmes where syllabus was revised (Formula Numerator)\",\"type\":\"number\"},{\"name\":\"qnm_1_1_2_total\",\"label\":\"Total number of programmes offered (Formula Denominator)\",\"type\":\"number\"},{\"name\":\"table_1_1_2\",\"label\":\"Data Template: Programmes and Syllabus Revision\",\"type\":\"table\",\"columns\":[{\"name\":\"prog_code\",\"label\":\"Programme Code\",\"type\":\"text\"},{\"name\":\"prog_name\",\"label\":\"Programme Name\",\"type\":\"text\"},{\"name\":\"dept_name\",\"label\":\"Department\",\"type\":\"text\"},{\"name\":\"year_intro\",\"label\":\"Year of Introduction\",\"type\":\"number\"},{\"name\":\"is_revised\",\"label\":\"Revised in last 5 years? (Yes\\/No)\",\"type\":\"text\"},{\"name\":\"year_revision\",\"label\":\"Year of Revision\",\"type\":\"number\"},{\"name\":\"pct_replaced\",\"label\":\"% Content replaced\",\"type\":\"number\"}]},{\"name\":\"file_1_1_2\",\"label\":\"Minutes of relevant Academic Council\\/BOS meeting\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"1.1.3\",\"title\":\"1.1.3: Courses focusing on employability\\/entrepreneurship\\/skill development\",\"description\":\"Average percentage of courses having focus on employability\\/entrepreneurship\\/skill development.\",\"fields\":[{\"name\":\"qnm_1_1_3_focused\",\"label\":\"Number of courses focusing on employability\\/entrepreneurship\\/skill dev\",\"type\":\"number\"},{\"name\":\"qnm_1_1_3_total\",\"label\":\"Total number of courses in all programmes\",\"type\":\"number\"},{\"name\":\"table_1_1_3\",\"label\":\"Data Template: Employability & Skill Development Courses\",\"type\":\"table\",\"columns\":[{\"name\":\"course_name\",\"label\":\"Course Name\",\"type\":\"text\"},{\"name\":\"course_code\",\"label\":\"Course Code\",\"type\":\"text\"},{\"name\":\"prog_name\",\"label\":\"Programme Name\",\"type\":\"text\"},{\"name\":\"activities\",\"label\":\"Activities bearing on Employability\",\"type\":\"text\"},{\"name\":\"year_intro\",\"label\":\"Year of Introduction\",\"type\":\"number\"}]},{\"name\":\"file_1_1_3\",\"label\":\"Programme\\/Curriculum\\/Syllabus of courses\",\"type\":\"file\",\"accept\":\".pdf\"}]}]},{\"title\":\"1.2 Academic Flexibility\",\"metrics\":[{\"id\":\"1.2.1\",\"title\":\"1.2.1: New courses introduced\",\"description\":\"Percentage of new courses introduced of the total number of courses across all Programmes offered during the last five years.\",\"fields\":[{\"name\":\"qnm_1_2_1_new\",\"label\":\"Number of new courses introduced\",\"type\":\"number\"},{\"name\":\"qnm_1_2_1_total\",\"label\":\"Total number of courses offered\",\"type\":\"number\"},{\"name\":\"table_1_2_1\",\"label\":\"Data Template: New Courses\",\"type\":\"table\",\"columns\":[{\"name\":\"course_name\",\"label\":\"Name of new course\",\"type\":\"text\"},{\"name\":\"course_code\",\"label\":\"Course Code\",\"type\":\"text\"},{\"name\":\"prog_name\",\"label\":\"Programme Name\",\"type\":\"text\"},{\"name\":\"year_intro\",\"label\":\"Year of Introduction\",\"type\":\"number\"}]},{\"name\":\"file_1_2_1\",\"label\":\"Document Upload\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"1.2.2\",\"title\":\"1.2.2: Choice Based Credit System (CBCS)\\/elective course system\",\"description\":\"Percentage of Programmes in which CBCS\\/elective course system has been implemented.\",\"fields\":[{\"name\":\"qnm_1_2_2_cbcs\",\"label\":\"Number of programmes in which CBCS\\/elective course system implemented\",\"type\":\"number\"},{\"name\":\"qnm_1_2_2_total\",\"label\":\"Total number of programmes offered\",\"type\":\"number\"},{\"name\":\"table_1_2_2\",\"label\":\"Data Template: CBCS Programmes\",\"type\":\"table\",\"columns\":[{\"name\":\"prog_name\",\"label\":\"Programme Name\",\"type\":\"text\"},{\"name\":\"prog_code\",\"label\":\"Programme Code\",\"type\":\"text\"},{\"name\":\"is_cbcs\",\"label\":\"CBCS Implemented? (Yes\\/No)\",\"type\":\"text\"},{\"name\":\"year_cbcs\",\"label\":\"Year of Implementation\",\"type\":\"number\"}]},{\"name\":\"file_1_2_2\",\"label\":\"Document Upload\",\"type\":\"file\",\"accept\":\".pdf\"}]}]},{\"title\":\"1.3 Curriculum Enrichment\",\"metrics\":[{\"id\":\"1.3.1\",\"title\":\"1.3.1: Integration of crosscutting issues\",\"description\":\"Institution integrates crosscutting issues relevant to Professional Ethics, Gender, Human Values, Environment and Sustainability into the Curriculum.\",\"fields\":[{\"name\":\"qlm_1_3_1_text\",\"label\":\"Description (Max 500 words)\",\"type\":\"textarea\"},{\"name\":\"file_1_3_1\",\"label\":\"Document Upload\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"1.3.2\",\"title\":\"1.3.2 & 1.3.3: Value-added courses & Student Enrollment\",\"description\":\"Value-added courses imparting transferable and life skills and percentage of students enrolled.\",\"fields\":[{\"name\":\"qnm_1_3_2_courses\",\"label\":\"Number of value-added courses\",\"type\":\"number\"},{\"name\":\"qnm_1_3_3_enrolled\",\"label\":\"Number of students enrolled in value-added courses\",\"type\":\"number\"},{\"name\":\"qnm_1_3_3_total\",\"label\":\"Total number of students in institution\",\"type\":\"number\"},{\"name\":\"table_1_3_2\",\"label\":\"Data Template: Value-added Courses\",\"type\":\"table\",\"columns\":[{\"name\":\"course_name\",\"label\":\"Name of value added course\",\"type\":\"text\"},{\"name\":\"course_code\",\"label\":\"Course Code\",\"type\":\"text\"},{\"name\":\"year_offering\",\"label\":\"Year of offering\",\"type\":\"number\"},{\"name\":\"times_offered\",\"label\":\"Times offered in year\",\"type\":\"number\"},{\"name\":\"duration\",\"label\":\"Duration (Hours)\",\"type\":\"number\"},{\"name\":\"enrolled_count\",\"label\":\"Students enrolled\",\"type\":\"number\"},{\"name\":\"completed_count\",\"label\":\"Students completed\",\"type\":\"number\"}]},{\"name\":\"file_1_3_2\",\"label\":\"Document Upload\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"1.3.4\",\"title\":\"1.3.4: Field projects\\/ internships\\/ student projects\",\"description\":\"Percentage of students undertaking field projects\\/ internships \\/ student projects.\",\"fields\":[{\"name\":\"qnm_1_3_4_undertaking\",\"label\":\"Number of students undertaking projects\\/internships\",\"type\":\"number\"},{\"name\":\"qnm_1_3_4_total\",\"label\":\"Total number of students\",\"type\":\"number\"},{\"name\":\"table_1_3_4\",\"label\":\"Data Template: Internships and Projects\",\"type\":\"table\",\"columns\":[{\"name\":\"prog_name\",\"label\":\"Programme Name\",\"type\":\"text\"},{\"name\":\"prog_code\",\"label\":\"Programme Code\",\"type\":\"text\"},{\"name\":\"student_name\",\"label\":\"Name of Student\",\"type\":\"text\"},{\"name\":\"project_type\",\"label\":\"Type (Project\\/Internship\\/Field Work)\",\"type\":\"text\"}]},{\"name\":\"file_1_3_4\",\"label\":\"Document Upload\",\"type\":\"file\",\"accept\":\".pdf\"}]}]},{\"title\":\"1.4 Feedback System\",\"metrics\":[{\"id\":\"1.4.1\",\"title\":\"1.4.1 & 1.4.2: Structured feedback for design and review of syllabus\",\"description\":\"Structured feedback for design and review of syllabus received from Stakeholders and processes.\",\"fields\":[{\"name\":\"feedback_stakeholders\",\"label\":\"Feedback received from (Check all that apply):\",\"type\":\"checkbox_group\",\"options\":[{\"name\":\"fb_students\",\"label\":\"Students\"},{\"name\":\"fb_teachers\",\"label\":\"Teachers\"},{\"name\":\"fb_employers\",\"label\":\"Employers\"},{\"name\":\"fb_alumni\",\"label\":\"Alumni\"}]},{\"name\":\"qlm_1_4_2_process\",\"label\":\"Describe the process of collecting and analyzing feedback\",\"type\":\"textarea\"},{\"name\":\"file_1_4_1\",\"label\":\"Document Upload (Action Taken Report)\",\"type\":\"file\",\"accept\":\".pdf\"}]}]}]}','2026-10-07 16:21:47'),(7,1,'v4_university_with_tables',1,'{\"sections\":[{\"title\":\"1.1 Curriculum Design and Development\",\"metrics\":[{\"id\":\"1.1.1\",\"title\":\"1.1.1: Curricula developed and implemented have relevance to needs\",\"description\":\"Curricula developed and implemented have relevance to the local, national, regional and global developmental needs which is reflected in POs, PSOs and COs.\",\"fields\":[{\"name\":\"qlm_1_1_1_text\",\"label\":\"Description (Max 500 words)\",\"type\":\"textarea\"},{\"name\":\"file_1_1_1\",\"label\":\"Document Upload\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"1.1.2\",\"title\":\"1.1.2: Syllabus revision\",\"description\":\"Percentage of Programmes where syllabus revision was carried out during the last five years.\",\"fields\":[{\"name\":\"qnm_1_1_2_revised\",\"label\":\"Number of programmes where syllabus was revised (Formula Numerator)\",\"type\":\"number\"},{\"name\":\"qnm_1_1_2_total\",\"label\":\"Total number of programmes offered (Formula Denominator)\",\"type\":\"number\"},{\"name\":\"table_1_1_2\",\"label\":\"Data Template: Programmes and Syllabus Revision\",\"type\":\"table\",\"columns\":[{\"name\":\"prog_code\",\"label\":\"Programme Code\",\"type\":\"text\"},{\"name\":\"prog_name\",\"label\":\"Programme Name\",\"type\":\"text\"},{\"name\":\"dept_name\",\"label\":\"Department\",\"type\":\"text\"},{\"name\":\"year_intro\",\"label\":\"Year of Introduction\",\"type\":\"number\"},{\"name\":\"is_revised\",\"label\":\"Revised in last 5 years? (Yes\\/No)\",\"type\":\"text\"},{\"name\":\"year_revision\",\"label\":\"Year of Revision\",\"type\":\"number\"},{\"name\":\"pct_replaced\",\"label\":\"% Content replaced\",\"type\":\"number\"}]},{\"name\":\"file_1_1_2\",\"label\":\"Minutes of relevant Academic Council\\/BOS meeting\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"1.1.3\",\"title\":\"1.1.3: Courses focusing on employability\\/entrepreneurship\\/skill development\",\"description\":\"Average percentage of courses having focus on employability\\/entrepreneurship\\/skill development.\",\"fields\":[{\"name\":\"qnm_1_1_3_focused\",\"label\":\"Number of courses focusing on employability\\/entrepreneurship\\/skill dev\",\"type\":\"number\"},{\"name\":\"qnm_1_1_3_total\",\"label\":\"Total number of courses in all programmes\",\"type\":\"number\"},{\"name\":\"table_1_1_3\",\"label\":\"Data Template: Employability & Skill Development Courses\",\"type\":\"table\",\"columns\":[{\"name\":\"course_name\",\"label\":\"Course Name\",\"type\":\"text\"},{\"name\":\"course_code\",\"label\":\"Course Code\",\"type\":\"text\"},{\"name\":\"prog_name\",\"label\":\"Programme Name\",\"type\":\"text\"},{\"name\":\"activities\",\"label\":\"Activities bearing on Employability\",\"type\":\"text\"},{\"name\":\"year_intro\",\"label\":\"Year of Introduction\",\"type\":\"number\"}]},{\"name\":\"file_1_1_3\",\"label\":\"Programme\\/Curriculum\\/Syllabus of courses\",\"type\":\"file\",\"accept\":\".pdf\"}]}]},{\"title\":\"1.2 Academic Flexibility\",\"metrics\":[{\"id\":\"1.2.1\",\"title\":\"1.2.1: New courses introduced\",\"description\":\"Percentage of new courses introduced of the total number of courses across all Programmes offered during the last five years.\",\"fields\":[{\"name\":\"qnm_1_2_1_new\",\"label\":\"Number of new courses introduced\",\"type\":\"number\"},{\"name\":\"qnm_1_2_1_total\",\"label\":\"Total number of courses offered\",\"type\":\"number\"},{\"name\":\"table_1_2_1\",\"label\":\"Data Template: New Courses\",\"type\":\"table\",\"columns\":[{\"name\":\"course_name\",\"label\":\"Name of new course\",\"type\":\"text\"},{\"name\":\"course_code\",\"label\":\"Course Code\",\"type\":\"text\"},{\"name\":\"prog_name\",\"label\":\"Programme Name\",\"type\":\"text\"},{\"name\":\"year_intro\",\"label\":\"Year of Introduction\",\"type\":\"number\"}]},{\"name\":\"file_1_2_1\",\"label\":\"Document Upload\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"1.2.2\",\"title\":\"1.2.2: Choice Based Credit System (CBCS)\\/elective course system\",\"description\":\"Percentage of Programmes in which CBCS\\/elective course system has been implemented.\",\"fields\":[{\"name\":\"qnm_1_2_2_cbcs\",\"label\":\"Number of programmes in which CBCS\\/elective course system implemented\",\"type\":\"number\"},{\"name\":\"qnm_1_2_2_total\",\"label\":\"Total number of programmes offered\",\"type\":\"number\"},{\"name\":\"table_1_2_2\",\"label\":\"Data Template: CBCS Programmes\",\"type\":\"table\",\"columns\":[{\"name\":\"prog_name\",\"label\":\"Programme Name\",\"type\":\"text\"},{\"name\":\"prog_code\",\"label\":\"Programme Code\",\"type\":\"text\"},{\"name\":\"is_cbcs\",\"label\":\"CBCS Implemented? (Yes\\/No)\",\"type\":\"text\"},{\"name\":\"year_cbcs\",\"label\":\"Year of Implementation\",\"type\":\"number\"}]},{\"name\":\"file_1_2_2\",\"label\":\"Document Upload\",\"type\":\"file\",\"accept\":\".pdf\"}]}]},{\"title\":\"1.3 Curriculum Enrichment\",\"metrics\":[{\"id\":\"1.3.1\",\"title\":\"1.3.1: Integration of crosscutting issues\",\"description\":\"Institution integrates crosscutting issues relevant to Professional Ethics, Gender, Human Values, Environment and Sustainability into the Curriculum.\",\"fields\":[{\"name\":\"qlm_1_3_1_text\",\"label\":\"Description (Max 500 words)\",\"type\":\"textarea\"},{\"name\":\"file_1_3_1\",\"label\":\"Document Upload\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"1.3.2\",\"title\":\"1.3.2: Value-added courses\",\"description\":\"Number of value-added courses imparting transferable and life skills offered during the last five years.\",\"fields\":[{\"name\":\"qnm_1_3_2_courses\",\"label\":\"Number of value-added courses\",\"type\":\"number\"},{\"name\":\"table_1_3_2\",\"label\":\"Data Template: Value-added Courses\",\"type\":\"table\",\"columns\":[{\"name\":\"course_name\",\"label\":\"Name of value added course\",\"type\":\"text\"},{\"name\":\"course_code\",\"label\":\"Course Code\",\"type\":\"text\"},{\"name\":\"year_offering\",\"label\":\"Year of offering\",\"type\":\"number\"},{\"name\":\"times_offered\",\"label\":\"Times offered in year\",\"type\":\"number\"},{\"name\":\"duration\",\"label\":\"Duration (Hours)\",\"type\":\"number\"}]},{\"name\":\"file_1_3_2\",\"label\":\"Document Upload\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"1.3.3\",\"title\":\"1.3.3: Students enrolled in value-added courses\",\"description\":\"Average Percentage of students enrolled in the value-added courses.\",\"fields\":[{\"name\":\"qnm_1_3_3_enrolled\",\"label\":\"Number of students enrolled in value-added courses\",\"type\":\"number\"},{\"name\":\"qnm_1_3_3_total\",\"label\":\"Total number of students in institution\",\"type\":\"number\"},{\"name\":\"table_1_3_3\",\"label\":\"Data Template: Value-added Course Enrollment\",\"type\":\"table\",\"columns\":[{\"name\":\"course_name\",\"label\":\"Name of value added course\",\"type\":\"text\"},{\"name\":\"course_code\",\"label\":\"Course Code\",\"type\":\"text\"},{\"name\":\"enrolled_count\",\"label\":\"Students enrolled\",\"type\":\"number\"},{\"name\":\"completed_count\",\"label\":\"Students completed\",\"type\":\"number\"}]},{\"name\":\"file_1_3_3\",\"label\":\"Document Upload\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"1.3.4\",\"title\":\"1.3.4: Field projects\\/ internships\\/ student projects\",\"description\":\"Percentage of students undertaking field projects\\/ internships \\/ student projects.\",\"fields\":[{\"name\":\"qnm_1_3_4_undertaking\",\"label\":\"Number of students undertaking projects\\/internships\",\"type\":\"number\"},{\"name\":\"qnm_1_3_4_total\",\"label\":\"Total number of students\",\"type\":\"number\"},{\"name\":\"table_1_3_4\",\"label\":\"Data Template: Internships and Projects\",\"type\":\"table\",\"columns\":[{\"name\":\"prog_name\",\"label\":\"Programme Name\",\"type\":\"text\"},{\"name\":\"prog_code\",\"label\":\"Programme Code\",\"type\":\"text\"},{\"name\":\"student_name\",\"label\":\"Name of Student\",\"type\":\"text\"},{\"name\":\"project_type\",\"label\":\"Type (Project\\/Internship\\/Field Work)\",\"type\":\"text\"}]},{\"name\":\"file_1_3_4\",\"label\":\"Document Upload\",\"type\":\"file\",\"accept\":\".pdf\"}]}]},{\"title\":\"1.4 Feedback System\",\"metrics\":[{\"id\":\"1.4.1\",\"title\":\"1.4.1: Structured feedback for design and review of syllabus\",\"description\":\"Structured feedback for design and review of syllabus received from Stakeholders.\",\"fields\":[{\"name\":\"feedback_stakeholders\",\"label\":\"Feedback received from (Check all that apply):\",\"type\":\"checkbox_group\",\"options\":[{\"name\":\"fb_students\",\"label\":\"Students\"},{\"name\":\"fb_teachers\",\"label\":\"Teachers\"},{\"name\":\"fb_employers\",\"label\":\"Employers\"},{\"name\":\"fb_alumni\",\"label\":\"Alumni\"}]},{\"name\":\"file_1_4_1\",\"label\":\"Document Upload (Feedback Reports)\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"1.4.2\",\"title\":\"1.4.2: Feedback processes\",\"description\":\"Feedback processes of the institution.\",\"fields\":[{\"name\":\"qlm_1_4_2_process\",\"label\":\"Describe the process of collecting and analyzing feedback\",\"type\":\"textarea\"},{\"name\":\"file_1_4_2\",\"label\":\"Document Upload (Action Taken Report)\",\"type\":\"file\",\"accept\":\".pdf\"}]}]}]}','2026-10-07 16:32:07'),(8,2,'v4_university_with_tables',1,'{\"sections\":[{\"title\":\"2.1 Student Enrolment and Profile\",\"metrics\":[{\"id\":\"2.1.1\",\"title\":\"2.1.1: Demand Ratio\",\"description\":\"Demand Ratio: Number of seats available vs Number of eligible applications received.\",\"fields\":[{\"name\":\"qnm_2_1_1_apps\",\"label\":\"Number of eligible applications received (Formula Numerator)\",\"type\":\"number\"},{\"name\":\"qnm_2_1_1_seats\",\"label\":\"Number of seats available (Formula Denominator)\",\"type\":\"number\"},{\"name\":\"table_2_1_1\",\"label\":\"Data Template: Demand Ratio\",\"type\":\"table\",\"columns\":[{\"name\":\"prog_name\",\"label\":\"Programme Name\",\"type\":\"text\"},{\"name\":\"prog_code\",\"label\":\"Programme Code\",\"type\":\"text\"},{\"name\":\"seats_avail\",\"label\":\"Seats Available\",\"type\":\"number\"},{\"name\":\"apps_recv\",\"label\":\"Applications Received\",\"type\":\"number\"},{\"name\":\"students_admit\",\"label\":\"Students Admitted\",\"type\":\"number\"}]},{\"name\":\"file_2_1_1\",\"label\":\"Document Upload\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"2.1.2\",\"title\":\"2.1.2: Reserved Categories\",\"description\":\"Average percentage of seats filled against reserved categories (SC, ST, OBC, Divyangjan, etc.).\",\"fields\":[{\"name\":\"qnm_2_1_2_filled\",\"label\":\"Seats filled against reserved categories (Formula Numerator)\",\"type\":\"number\"},{\"name\":\"qnm_2_1_2_reserved\",\"label\":\"Total seats earmarked for reserved categories (Formula Denominator)\",\"type\":\"number\"},{\"name\":\"table_2_1_2\",\"label\":\"Data Template: Reserved Category Admissions\",\"type\":\"table\",\"columns\":[{\"name\":\"year\",\"label\":\"Year\",\"type\":\"number\"},{\"name\":\"earmark_sc\",\"label\":\"Earmarked SC\",\"type\":\"number\"},{\"name\":\"earmark_st\",\"label\":\"Earmarked ST\",\"type\":\"number\"},{\"name\":\"earmark_obc\",\"label\":\"Earmarked OBC\",\"type\":\"number\"},{\"name\":\"earmark_gen\",\"label\":\"Earmarked Gen\",\"type\":\"number\"},{\"name\":\"admit_sc\",\"label\":\"Admitted SC\",\"type\":\"number\"},{\"name\":\"admit_st\",\"label\":\"Admitted ST\",\"type\":\"number\"},{\"name\":\"admit_obc\",\"label\":\"Admitted OBC\",\"type\":\"number\"},{\"name\":\"admit_gen\",\"label\":\"Admitted Gen\",\"type\":\"number\"}]},{\"name\":\"file_2_1_2\",\"label\":\"Document Upload\",\"type\":\"file\",\"accept\":\".pdf\"}]}]},{\"title\":\"2.2 Catering to Student Diversity\",\"metrics\":[{\"id\":\"2.2.1\",\"title\":\"2.2.1: Advanced and Slow Learners\",\"description\":\"The institution assesses the learning levels of the students and organizes special Programmes for advanced learners and slow learners.\",\"fields\":[{\"name\":\"qlm_2_2_1_text\",\"label\":\"Description\",\"type\":\"textarea\"},{\"name\":\"file_2_2_1\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"2.2.2\",\"title\":\"2.2.2: Student-Teacher Ratio\",\"description\":\"Student - Full time teacher ratio.\",\"fields\":[{\"name\":\"qnm_2_2_2_students\",\"label\":\"Total Number of Students\",\"type\":\"number\"},{\"name\":\"qnm_2_2_2_teachers\",\"label\":\"Total Number of Full-Time Teachers\",\"type\":\"number\"}]}]},{\"title\":\"2.3 Teaching-Learning Process\",\"metrics\":[{\"id\":\"2.3.1\",\"title\":\"2.3.1: Student-centric methods\",\"description\":\"Student centric methods, such as experiential learning, participative learning and problem solving methodologies.\",\"fields\":[{\"name\":\"qlm_2_3_1_text\",\"label\":\"Description\",\"type\":\"textarea\"},{\"name\":\"file_2_3_1\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"2.3.2\",\"title\":\"2.3.2: ICT Tools\",\"description\":\"Teachers use ICT enabled tools for effective teaching-learning process.\",\"fields\":[{\"name\":\"qlm_2_3_2_text\",\"label\":\"Description of ICT tools used\",\"type\":\"textarea\"},{\"name\":\"file_2_3_2\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"2.3.3\",\"title\":\"2.3.3: Mentor-Mentee Ratio\",\"description\":\"Ratio of students to mentor for academic and other related issues.\",\"fields\":[{\"name\":\"qnm_2_3_3_students\",\"label\":\"Number of Students assigned to mentors (Numerator)\",\"type\":\"number\"},{\"name\":\"qnm_2_3_3_mentors\",\"label\":\"Number of Mentors (Denominator)\",\"type\":\"number\"},{\"name\":\"table_2_3_3\",\"label\":\"Data Template: Mentor-Mentee\",\"type\":\"table\",\"columns\":[{\"name\":\"mentor_name\",\"label\":\"Name of Mentor\",\"type\":\"text\"},{\"name\":\"mentor_dept\",\"label\":\"Department\",\"type\":\"text\"},{\"name\":\"assigned_count\",\"label\":\"Students Assigned\",\"type\":\"number\"}]},{\"name\":\"file_2_3_3\",\"label\":\"Mentor-Mentee List Document\",\"type\":\"file\",\"accept\":\".pdf\"}]}]},{\"title\":\"2.4 Teacher Profile and Quality\",\"metrics\":[{\"id\":\"2.4.1\",\"title\":\"2.4.1: Full-time teachers against sanctioned posts\",\"description\":\"Average percentage of full time teachers against sanctioned posts.\",\"fields\":[{\"name\":\"qnm_2_4_1_teachers\",\"label\":\"Number of Full-Time Teachers (Numerator)\",\"type\":\"number\"},{\"name\":\"qnm_2_4_1_sanctioned\",\"label\":\"Number of Sanctioned Posts (Denominator)\",\"type\":\"number\"},{\"name\":\"table_2_4_1\",\"label\":\"Data Template: Teachers vs Sanctioned\",\"type\":\"table\",\"columns\":[{\"name\":\"teacher_name\",\"label\":\"Name of Teacher\",\"type\":\"text\"},{\"name\":\"pan\",\"label\":\"PAN\",\"type\":\"text\"},{\"name\":\"designation\",\"label\":\"Designation\",\"type\":\"text\"},{\"name\":\"year_appt\",\"label\":\"Year of Appointment\",\"type\":\"number\"},{\"name\":\"nature_appt\",\"label\":\"Nature (Permanent\\/Temp)\",\"type\":\"text\"},{\"name\":\"dept\",\"label\":\"Department\",\"type\":\"text\"}]},{\"name\":\"file_2_4_1\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"2.4.2\",\"title\":\"2.4.2: Teachers with Ph.D.\",\"description\":\"Average percentage of full time teachers with Ph.D.\\/D.M\\/M.Ch.\\/D.N.B Superspeciality\\/D.Sc.\\/D\'Lit.\",\"fields\":[{\"name\":\"qnm_2_4_2_phd\",\"label\":\"Number of teachers with Ph.D. etc.\",\"type\":\"number\"},{\"name\":\"table_2_4_2\",\"label\":\"Data Template: Teachers with Ph.D.\",\"type\":\"table\",\"columns\":[{\"name\":\"teacher_name\",\"label\":\"Name of Teacher\",\"type\":\"text\"},{\"name\":\"qualification\",\"label\":\"Qualification (Ph.D\\/D.M etc)\",\"type\":\"text\"},{\"name\":\"year_obt\",\"label\":\"Year Obtained\",\"type\":\"number\"},{\"name\":\"is_guide\",\"label\":\"Research Guide? (Yes\\/No)\",\"type\":\"text\"},{\"name\":\"still_serving\",\"label\":\"Still Serving? (Yes\\/No)\",\"type\":\"text\"}]},{\"name\":\"file_2_4_2\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"2.4.3\",\"title\":\"2.4.3: Teaching experience\",\"description\":\"Average teaching experience of full time teachers in the same institution.\",\"fields\":[{\"name\":\"qnm_2_4_3_years\",\"label\":\"Total experience of full-time teachers (in years)\",\"type\":\"number\"},{\"name\":\"table_2_4_3\",\"label\":\"Data Template: Teaching Experience\",\"type\":\"table\",\"columns\":[{\"name\":\"teacher_name\",\"label\":\"Name of Teacher\",\"type\":\"text\"},{\"name\":\"designation\",\"label\":\"Designation\",\"type\":\"text\"},{\"name\":\"dept\",\"label\":\"Department\",\"type\":\"text\"},{\"name\":\"year_appt\",\"label\":\"Year of Appointment\",\"type\":\"number\"},{\"name\":\"exp_years\",\"label\":\"Experience (Years)\",\"type\":\"number\"},{\"name\":\"still_serving\",\"label\":\"Still Serving?\",\"type\":\"text\"}]},{\"name\":\"file_2_4_3\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"2.4.4\",\"title\":\"2.4.4: Teacher Awards\",\"description\":\"Average percentage of full time teachers who received awards, recognition.\",\"fields\":[{\"name\":\"qnm_2_4_4_awards\",\"label\":\"Number of teachers receiving awards\",\"type\":\"number\"},{\"name\":\"table_2_4_4\",\"label\":\"Data Template: Teacher Awards\",\"type\":\"table\",\"columns\":[{\"name\":\"teacher_name\",\"label\":\"Name of Teacher\",\"type\":\"text\"},{\"name\":\"year_award\",\"label\":\"Year of Award\",\"type\":\"number\"},{\"name\":\"pan\",\"label\":\"PAN\",\"type\":\"text\"},{\"name\":\"award_name\",\"label\":\"Name of Award\",\"type\":\"text\"},{\"name\":\"issuing_body\",\"label\":\"Issuing Body\",\"type\":\"text\"}]},{\"name\":\"file_2_4_4\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]}]},{\"title\":\"2.5 Evaluation Process and Reforms\",\"metrics\":[{\"id\":\"2.5.1\",\"title\":\"2.5.1: Result Declaration Days\",\"description\":\"Average number of days from the date of last semester-end\\/ year-end examination till the declaration of results.\",\"fields\":[{\"name\":\"qnm_2_5_1_days\",\"label\":\"Average number of days\",\"type\":\"number\"},{\"name\":\"table_2_5_1\",\"label\":\"Data Template: Result Declaration\",\"type\":\"table\",\"columns\":[{\"name\":\"prog_name\",\"label\":\"Programme Name\",\"type\":\"text\"},{\"name\":\"prog_code\",\"label\":\"Programme Code\",\"type\":\"text\"},{\"name\":\"sem_year\",\"label\":\"Semester\\/Year\",\"type\":\"text\"},{\"name\":\"date_exam\",\"label\":\"Date of last exam (DD\\/MM\\/YYYY)\",\"type\":\"text\"},{\"name\":\"date_result\",\"label\":\"Date of result (DD\\/MM\\/YYYY)\",\"type\":\"text\"}]},{\"name\":\"file_2_5_1\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"2.5.2\",\"title\":\"2.5.2: Student complaints\\/grievances\",\"description\":\"Average percentage of student complaints\\/grievances about evaluation against total number appeared in the examinations.\",\"fields\":[{\"name\":\"qnm_2_5_2_grievances\",\"label\":\"Number of complaints\\/grievances (Numerator)\",\"type\":\"number\"},{\"name\":\"qnm_2_5_2_appeared\",\"label\":\"Total number appeared (Denominator)\",\"type\":\"number\"},{\"name\":\"file_2_5_2\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"2.5.3\",\"title\":\"2.5.3: IT integration in exams\",\"description\":\"IT integration and reforms in the examination procedures and processes.\",\"fields\":[{\"name\":\"qlm_2_5_3_text\",\"label\":\"Description\",\"type\":\"textarea\"},{\"name\":\"file_2_5_3\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]}]},{\"title\":\"2.6 Student Performance and Learning Outcomes\",\"metrics\":[{\"id\":\"2.6.1\",\"title\":\"2.6.1: Outcomes definition\",\"description\":\"Programme and course outcomes for all Programmes offered by the institution are stated.\",\"fields\":[{\"name\":\"qlm_2_6_1_text\",\"label\":\"Description\",\"type\":\"textarea\"},{\"name\":\"qlm_2_6_1_url\",\"label\":\"Website URL link for Outcomes\",\"type\":\"url\"},{\"name\":\"file_2_6_1\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"2.6.2\",\"title\":\"2.6.2: Outcomes attainment\",\"description\":\"Attainment of POs and COs are evaluated.\",\"fields\":[{\"name\":\"qlm_2_6_2_text\",\"label\":\"Description\",\"type\":\"textarea\"},{\"name\":\"file_2_6_2\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"2.6.3\",\"title\":\"2.6.3: Pass percentage\",\"description\":\"Pass percentage of Students (Data for the latest completed academic year).\",\"fields\":[{\"name\":\"qnm_2_6_3_passed\",\"label\":\"Number of students passed in final year exam (Numerator)\",\"type\":\"number\"},{\"name\":\"qnm_2_6_3_appeared\",\"label\":\"Number of students appeared in final year exam (Denominator)\",\"type\":\"number\"},{\"name\":\"table_2_6_3\",\"label\":\"Data Template: Pass Percentage\",\"type\":\"table\",\"columns\":[{\"name\":\"prog_code\",\"label\":\"Programme Code\",\"type\":\"text\"},{\"name\":\"prog_name\",\"label\":\"Programme Name\",\"type\":\"text\"},{\"name\":\"appeared\",\"label\":\"Appeared Count\",\"type\":\"number\"},{\"name\":\"passed\",\"label\":\"Passed Count\",\"type\":\"number\"},{\"name\":\"pass_pct\",\"label\":\"Pass Percentage\",\"type\":\"number\"}]},{\"name\":\"file_2_6_3\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]}]},{\"title\":\"2.7 Student Satisfaction Survey\",\"metrics\":[{\"id\":\"2.7.1\",\"title\":\"2.7.1: Student Satisfaction Survey\",\"description\":\"Online student satisfaction survey regarding to teaching learning process.\",\"fields\":[{\"name\":\"table_2_7_1\",\"label\":\"Data Template: Student Database for SSS\",\"type\":\"table\",\"columns\":[{\"name\":\"student_name\",\"label\":\"Name of Student\",\"type\":\"text\"},{\"name\":\"gender\",\"label\":\"Gender\",\"type\":\"text\"},{\"name\":\"category\",\"label\":\"Category\",\"type\":\"text\"},{\"name\":\"nationality\",\"label\":\"Nationality\",\"type\":\"text\"},{\"name\":\"email\",\"label\":\"Email ID\",\"type\":\"text\"},{\"name\":\"prog_name\",\"label\":\"Programme Name\",\"type\":\"text\"},{\"name\":\"enrol_id\",\"label\":\"Enrolment ID\",\"type\":\"text\"},{\"name\":\"mobile\",\"label\":\"Mobile Number\",\"type\":\"text\"}]}]}]}]}','2026-10-07 16:45:15'),(9,3,'v4_university_with_tables',0,'{\"sections\":[{\"title\":\"3.1 Promotion of Research and Facilities\",\"metrics\":[{\"id\":\"3.1.1\",\"title\":\"3.1.1: Research facilities\",\"description\":\"The institution Research facilities are frequently updated and there is well defined policy for promotion of research.\",\"fields\":[{\"name\":\"qlm_3_1_1_text\",\"label\":\"Description\",\"type\":\"textarea\"},{\"name\":\"file_3_1_1\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"3.1.2\",\"title\":\"3.1.2: Seed money for research\",\"description\":\"The institution provides seed money to its teachers for research.\",\"fields\":[{\"name\":\"qnm_3_1_2_amt\",\"label\":\"Total Amount of seed money provided (in Lakhs)\",\"type\":\"number\"},{\"name\":\"table_3_1_2\",\"label\":\"Data Template: Seed Money\",\"type\":\"table\",\"columns\":[{\"name\":\"teacher_name\",\"label\":\"Name of Teacher\",\"type\":\"text\"},{\"name\":\"amount\",\"label\":\"Amount provided (INR in Lakhs)\",\"type\":\"number\"},{\"name\":\"year\",\"label\":\"Year of receiving\",\"type\":\"number\"}]},{\"name\":\"file_3_1_2\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"3.1.3\",\"title\":\"3.1.3: Fellowships to teachers\",\"description\":\"Percentage of teachers receiving national\\/ international fellowship\\/financial support by various agencies for advanced studies\\/ research.\",\"fields\":[{\"name\":\"qnm_3_1_3_teachers\",\"label\":\"Number of teachers receiving fellowship (Numerator)\",\"type\":\"number\"},{\"name\":\"qnm_3_1_3_total\",\"label\":\"Total number of full time teachers (Denominator)\",\"type\":\"number\"},{\"name\":\"table_3_1_3\",\"label\":\"Data Template: Fellowships\",\"type\":\"table\",\"columns\":[{\"name\":\"teacher_name\",\"label\":\"Name of Teacher\",\"type\":\"text\"},{\"name\":\"fellowship_name\",\"label\":\"Name of Fellowship\",\"type\":\"text\"},{\"name\":\"year\",\"label\":\"Year of Award\",\"type\":\"number\"},{\"name\":\"awarding_agency\",\"label\":\"Awarding Agency\",\"type\":\"text\"}]},{\"name\":\"file_3_1_3\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"3.1.4\",\"title\":\"3.1.4: Research Fellows\",\"description\":\"Number of JRFs, SRFs, Post Doctoral Fellows, Research Associates and other research fellows enrolled.\",\"fields\":[{\"name\":\"qnm_3_1_4_fellows\",\"label\":\"Number of Research Fellows\",\"type\":\"number\"},{\"name\":\"table_3_1_4\",\"label\":\"Data Template: Research Fellows\",\"type\":\"table\",\"columns\":[{\"name\":\"fellow_name\",\"label\":\"Name of Research Fellow\",\"type\":\"text\"},{\"name\":\"year_enroll\",\"label\":\"Year of Enrolment\",\"type\":\"number\"},{\"name\":\"duration\",\"label\":\"Duration\",\"type\":\"text\"},{\"name\":\"type\",\"label\":\"Type (JRF\\/SRF\\/PDF\\/RA)\",\"type\":\"text\"},{\"name\":\"agency\",\"label\":\"Granting Agency\",\"type\":\"text\"}]},{\"name\":\"file_3_1_4\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"3.1.5\",\"title\":\"3.1.5: Institution Facilities\",\"description\":\"Institution has Central Instrumentation Centre, Animal House, Museum, Media laboratory, Business Lab, etc.\",\"fields\":[{\"name\":\"facilities_list\",\"label\":\"Facilities available (Check all that apply):\",\"type\":\"checkbox_group\",\"options\":[{\"name\":\"fac_cic\",\"label\":\"Central Instrumentation Centre\"},{\"name\":\"fac_animal\",\"label\":\"Animal House\\/Green House\"},{\"name\":\"fac_museum\",\"label\":\"Museum\"},{\"name\":\"fac_media\",\"label\":\"Media laboratory\\/Studios\"},{\"name\":\"fac_business\",\"label\":\"Business Lab\"}]},{\"name\":\"file_3_1_5\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"3.1.6\",\"title\":\"3.1.6: Departmental Research Schemes\",\"description\":\"Percentage of departments with UGC-SAP, CAS, DST-FIST, DBT, ICSSR etc.\",\"fields\":[{\"name\":\"qnm_3_1_6_depts\",\"label\":\"Number of Departments with schemes (Numerator)\",\"type\":\"number\"},{\"name\":\"qnm_3_1_6_total\",\"label\":\"Total number of Departments (Denominator)\",\"type\":\"number\"},{\"name\":\"table_3_1_6\",\"label\":\"Data Template: Departmental Schemes\",\"type\":\"table\",\"columns\":[{\"name\":\"dept_name\",\"label\":\"Name of Department\",\"type\":\"text\"},{\"name\":\"scheme_name\",\"label\":\"Name of Scheme\",\"type\":\"text\"},{\"name\":\"funding_agency\",\"label\":\"Funding Agency\",\"type\":\"text\"},{\"name\":\"year_award\",\"label\":\"Year of Award\",\"type\":\"number\"},{\"name\":\"funds\",\"label\":\"Funds Provided (Lakhs)\",\"type\":\"number\"}]},{\"name\":\"file_3_1_6\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]}]},{\"title\":\"3.2 Resource Mobilization for Research\",\"metrics\":[{\"id\":\"3.2.1_3.2.2_3.2.3\",\"title\":\"3.2.1, 3.2.2 & 3.2.3: Research Grants and Projects\",\"description\":\"Extramural funding, government grants, and number of research projects per teacher.\",\"fields\":[{\"name\":\"qnm_3_2_1_funds\",\"label\":\"Total Extramural Funding (in Lakhs)\",\"type\":\"number\"},{\"name\":\"qnm_3_2_2_grants\",\"label\":\"Total Grants for Research Projects (in Lakhs)\",\"type\":\"number\"},{\"name\":\"qnm_3_2_3_projects\",\"label\":\"Number of research projects (Numerator)\",\"type\":\"number\"},{\"name\":\"qnm_3_2_3_teachers\",\"label\":\"Total number of full time teachers (Denominator)\",\"type\":\"number\"},{\"name\":\"table_3_2_x\",\"label\":\"Data Template: Research Projects\",\"type\":\"table\",\"columns\":[{\"name\":\"pi_name\",\"label\":\"Name of PI\\/Co-PI\",\"type\":\"text\"},{\"name\":\"proj_title\",\"label\":\"Title of Project\",\"type\":\"text\"},{\"name\":\"funding_agency\",\"label\":\"Funding Agency\",\"type\":\"text\"},{\"name\":\"dept\",\"label\":\"Department\",\"type\":\"text\"},{\"name\":\"year_award\",\"label\":\"Year of Award\",\"type\":\"number\"},{\"name\":\"funds\",\"label\":\"Funds Provided (Lakhs)\",\"type\":\"number\"},{\"name\":\"duration\",\"label\":\"Duration (Years)\",\"type\":\"number\"}]},{\"name\":\"file_3_2_x\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]}]},{\"title\":\"3.3 Innovation Ecosystem\",\"metrics\":[{\"id\":\"3.3.1\",\"title\":\"3.3.1: Innovation ecosystem\",\"description\":\"Institution has created an eco system for innovations including Incubation centre.\",\"fields\":[{\"name\":\"qlm_3_3_1_text\",\"label\":\"Description\",\"type\":\"textarea\"},{\"name\":\"file_3_3_1\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"3.3.2\",\"title\":\"3.3.2: Workshops\\/seminars on Research\\/IPR\",\"description\":\"Number of workshops\\/seminars conducted on Research methodology, IPR, entrepreneurship.\",\"fields\":[{\"name\":\"qnm_3_3_2_count\",\"label\":\"Number of workshops\\/seminars\",\"type\":\"number\"},{\"name\":\"table_3_3_2\",\"label\":\"Data Template: Workshops\\/Seminars\",\"type\":\"table\",\"columns\":[{\"name\":\"workshop_name\",\"label\":\"Name of Workshop\",\"type\":\"text\"},{\"name\":\"participants\",\"label\":\"Number of Participants\",\"type\":\"number\"},{\"name\":\"date_range\",\"label\":\"Date (From - To)\",\"type\":\"text\"}]},{\"name\":\"file_3_3_2\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"3.3.3\",\"title\":\"3.3.3: Innovation Awards\",\"description\":\"Number of awards \\/ recognitions received for research\\/innovations.\",\"fields\":[{\"name\":\"qnm_3_3_3_awards\",\"label\":\"Number of awards\\/recognitions\",\"type\":\"number\"},{\"name\":\"table_3_3_3\",\"label\":\"Data Template: Innovation Awards\",\"type\":\"table\",\"columns\":[{\"name\":\"title_innov\",\"label\":\"Title of Innovation\",\"type\":\"text\"},{\"name\":\"awardee_name\",\"label\":\"Name of Awardee\",\"type\":\"text\"},{\"name\":\"agency_name\",\"label\":\"Awarding Agency\",\"type\":\"text\"},{\"name\":\"year_award\",\"label\":\"Year of Award\",\"type\":\"number\"}]},{\"name\":\"file_3_3_3\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]}]},{\"title\":\"3.4 Research Publications and Awards\",\"metrics\":[{\"id\":\"3.4.1\",\"title\":\"3.4.1: Code of Ethics\",\"description\":\"The Institution ensures implementation of its stated Code of Ethics for research.\",\"fields\":[{\"name\":\"ethics_list\",\"label\":\"Options (Check all that apply):\",\"type\":\"checkbox_group\",\"options\":[{\"name\":\"eth_comm\",\"label\":\"Ethics Committee exists\"},{\"name\":\"eth_plag\",\"label\":\"Plagiarism check software\"},{\"name\":\"eth_comm_meets\",\"label\":\"Research Advisory Committee meets\"}]},{\"name\":\"file_3_4_1\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"3.4.2\",\"title\":\"3.4.2: Teacher Incentives\",\"description\":\"The institution provides incentives to teachers who receive state, national and international recognitions.\",\"fields\":[{\"name\":\"incentives_list\",\"label\":\"Incentives (Check all that apply):\",\"type\":\"checkbox_group\",\"options\":[{\"name\":\"inc_salary\",\"label\":\"Commendation & monetary reward\"},{\"name\":\"inc_cert\",\"label\":\"Certificate of honor\"}]},{\"name\":\"file_3_4_2\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"3.4.3\",\"title\":\"3.4.3: Patents\",\"description\":\"Number of Patents published\\/awarded.\",\"fields\":[{\"name\":\"qnm_3_4_3_patents\",\"label\":\"Number of Patents\",\"type\":\"number\"},{\"name\":\"table_3_4_3\",\"label\":\"Data Template: Patents\",\"type\":\"table\",\"columns\":[{\"name\":\"patenter_name\",\"label\":\"Name of Patenter\",\"type\":\"text\"},{\"name\":\"patent_num\",\"label\":\"Patent Number\",\"type\":\"text\"},{\"name\":\"patent_title\",\"label\":\"Title of Patent\",\"type\":\"text\"},{\"name\":\"year_award\",\"label\":\"Year of Award\",\"type\":\"number\"}]},{\"name\":\"file_3_4_3\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"3.4.4\",\"title\":\"3.4.4: Ph.D\'s awarded per teacher\",\"description\":\"Number of Ph.D\'s awarded per teacher.\",\"fields\":[{\"name\":\"qnm_3_4_4_phd\",\"label\":\"Number of Ph.D\'s awarded (Numerator)\",\"type\":\"number\"},{\"name\":\"qnm_3_4_4_guides\",\"label\":\"Number of recognized guides (Denominator)\",\"type\":\"number\"},{\"name\":\"table_3_4_4\",\"label\":\"Data Template: Ph.D.s Awarded\",\"type\":\"table\",\"columns\":[{\"name\":\"scholar_name\",\"label\":\"Name of Scholar\",\"type\":\"text\"},{\"name\":\"dept\",\"label\":\"Department\",\"type\":\"text\"},{\"name\":\"guide_name\",\"label\":\"Name of Guide\",\"type\":\"text\"},{\"name\":\"thesis_title\",\"label\":\"Title of Thesis\",\"type\":\"text\"},{\"name\":\"year_reg\",\"label\":\"Year of Registration\",\"type\":\"number\"},{\"name\":\"year_award\",\"label\":\"Year of Award\",\"type\":\"number\"}]},{\"name\":\"file_3_4_4\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"3.4.5\",\"title\":\"3.4.5: Research Papers in UGC Journals\",\"description\":\"Number of research papers per teacher in the Journals notified on UGC website.\",\"fields\":[{\"name\":\"qnm_3_4_5_papers\",\"label\":\"Number of Research Papers (Numerator)\",\"type\":\"number\"},{\"name\":\"qnm_3_4_5_teachers\",\"label\":\"Number of Full-Time Teachers (Denominator)\",\"type\":\"number\"},{\"name\":\"table_3_4_5\",\"label\":\"Data Template: Research Papers\",\"type\":\"table\",\"columns\":[{\"name\":\"paper_title\",\"label\":\"Title of Paper\",\"type\":\"text\"},{\"name\":\"author_name\",\"label\":\"Name of Author\",\"type\":\"text\"},{\"name\":\"dept\",\"label\":\"Department\",\"type\":\"text\"},{\"name\":\"journal_name\",\"label\":\"Journal Name\",\"type\":\"text\"},{\"name\":\"year_pub\",\"label\":\"Year of Publication\",\"type\":\"number\"},{\"name\":\"issn\",\"label\":\"ISSN Number\",\"type\":\"text\"}]},{\"name\":\"file_3_4_5\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"3.4.6\",\"title\":\"3.4.6: Books and Chapters published\",\"description\":\"Number of books and chapters in edited volumes\\/books published.\",\"fields\":[{\"name\":\"qnm_3_4_6_books\",\"label\":\"Number of books and chapters\",\"type\":\"number\"},{\"name\":\"table_3_4_6\",\"label\":\"Data Template: Books and Chapters\",\"type\":\"table\",\"columns\":[{\"name\":\"book_title\",\"label\":\"Title of Book\\/Chapter\",\"type\":\"text\"},{\"name\":\"author_name\",\"label\":\"Author\",\"type\":\"text\"},{\"name\":\"proceedings\",\"label\":\"Title of proceedings\",\"type\":\"text\"},{\"name\":\"publisher\",\"label\":\"Publisher Name\",\"type\":\"text\"},{\"name\":\"isbn\",\"label\":\"ISBN\",\"type\":\"text\"},{\"name\":\"year_pub\",\"label\":\"Year of Publication\",\"type\":\"number\"}]},{\"name\":\"file_3_4_6\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"3.4.7\",\"title\":\"3.4.7: E-content developed by teachers\",\"description\":\"E-content developed by teachers for platforms like NMEICT\\/NPTEL\\/SWAYAM.\",\"fields\":[{\"name\":\"qnm_3_4_7_content\",\"label\":\"Number of E-contents developed\",\"type\":\"number\"},{\"name\":\"table_3_4_7\",\"label\":\"Data Template: E-Content\",\"type\":\"table\",\"columns\":[{\"name\":\"teacher_name\",\"label\":\"Name of Teacher\",\"type\":\"text\"},{\"name\":\"module_name\",\"label\":\"Name of Module\",\"type\":\"text\"},{\"name\":\"platform\",\"label\":\"Platform (SWAYAM etc)\",\"type\":\"text\"},{\"name\":\"date_launch\",\"label\":\"Date of Launch\",\"type\":\"text\"}]},{\"name\":\"file_3_4_7\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"3.4.8_3.4.9\",\"title\":\"3.4.8 & 3.4.9: Bibliometrics (Citation Index and h-index)\",\"description\":\"Bibliometrics of the publications based on average Citation Index and h-index in Scopus\\/ Web of Science.\",\"fields\":[{\"name\":\"qnm_3_4_8_citation\",\"label\":\"Average Citation Index\",\"type\":\"number\"},{\"name\":\"qnm_3_4_9_hindex\",\"label\":\"h-index of the institution\",\"type\":\"number\"},{\"name\":\"file_3_4_8\",\"label\":\"Supporting Document (Citation Index)\",\"type\":\"file\",\"accept\":\".pdf\"},{\"name\":\"file_3_4_9\",\"label\":\"Supporting Document (h-index)\",\"type\":\"file\",\"accept\":\".pdf\"}]}]},{\"title\":\"3.5 Consultancy\",\"metrics\":[{\"id\":\"3.5.1\",\"title\":\"3.5.1: Consultancy Policy\",\"description\":\"Institution has a policy on consultancy including revenue sharing.\",\"fields\":[{\"name\":\"qlm_3_5_1_text\",\"label\":\"Description\",\"type\":\"textarea\"},{\"name\":\"file_3_5_1\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"3.5.2\",\"title\":\"3.5.2: Revenue from Consultancy\",\"description\":\"Revenue generated from consultancy and corporate training.\",\"fields\":[{\"name\":\"qnm_3_5_2_revenue\",\"label\":\"Total Revenue Generated (in Lakhs)\",\"type\":\"number\"},{\"name\":\"table_3_5_2\",\"label\":\"Data Template: Consultancy Revenue\",\"type\":\"table\",\"columns\":[{\"name\":\"consultant_name\",\"label\":\"Name of Consultant\",\"type\":\"text\"},{\"name\":\"project_name\",\"label\":\"Name of Project\",\"type\":\"text\"},{\"name\":\"agency_name\",\"label\":\"Sponsoring Agency\",\"type\":\"text\"},{\"name\":\"revenue\",\"label\":\"Revenue (Lakhs)\",\"type\":\"number\"}]},{\"name\":\"file_3_5_2\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]}]},{\"title\":\"3.6 Extension Activities\",\"metrics\":[{\"id\":\"3.6.1\",\"title\":\"3.6.1: Extension activities in neighbourhood\",\"description\":\"Extension activities in the neighbourhood community in terms of impact.\",\"fields\":[{\"name\":\"qlm_3_6_1_text\",\"label\":\"Description\",\"type\":\"textarea\"},{\"name\":\"file_3_6_1\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"3.6.2\",\"title\":\"3.6.2: Extension Awards\",\"description\":\"Number of awards received by the Institution, its teachers and students for extension activities.\",\"fields\":[{\"name\":\"qnm_3_6_2_awards\",\"label\":\"Number of awards\",\"type\":\"number\"},{\"name\":\"table_3_6_2\",\"label\":\"Data Template: Extension Awards\",\"type\":\"table\",\"columns\":[{\"name\":\"activity_name\",\"label\":\"Name of Activity\",\"type\":\"text\"},{\"name\":\"award_name\",\"label\":\"Name of Award\",\"type\":\"text\"},{\"name\":\"awarding_body\",\"label\":\"Awarding Body\",\"type\":\"text\"},{\"name\":\"year\",\"label\":\"Year of Award\",\"type\":\"number\"}]},{\"name\":\"file_3_6_2\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"3.6.3_3.6.4\",\"title\":\"3.6.3 & 3.6.4: Extension Programs & Student Participation\",\"description\":\"Number of extension and outreach programs conducted and average percentage of students participating.\",\"fields\":[{\"name\":\"qnm_3_6_3_programs\",\"label\":\"Number of programs conducted\",\"type\":\"number\"},{\"name\":\"qnm_3_6_4_participating\",\"label\":\"Number of students participating (Numerator)\",\"type\":\"number\"},{\"name\":\"qnm_3_6_4_total\",\"label\":\"Total number of students (Denominator)\",\"type\":\"number\"},{\"name\":\"table_3_6_x\",\"label\":\"Data Template: Extension Programs\",\"type\":\"table\",\"columns\":[{\"name\":\"activity_name\",\"label\":\"Name of Activity\",\"type\":\"text\"},{\"name\":\"organising_unit\",\"label\":\"Organising Unit\\/Agency\",\"type\":\"text\"},{\"name\":\"scheme_name\",\"label\":\"Name of Scheme\",\"type\":\"text\"},{\"name\":\"year\",\"label\":\"Year\",\"type\":\"number\"},{\"name\":\"student_count\",\"label\":\"Number of Students Participated\",\"type\":\"number\"}]},{\"name\":\"file_3_6_x\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]}]},{\"title\":\"3.7 Collaboration\",\"metrics\":[{\"id\":\"3.7.1\",\"title\":\"3.7.1: Collaborative activities\",\"description\":\"Number of collaborative activities for research, Faculty exchange, Student exchange.\",\"fields\":[{\"name\":\"qnm_3_7_1_collabs\",\"label\":\"Number of collaborative activities\",\"type\":\"number\"},{\"name\":\"table_3_7_1\",\"label\":\"Data Template: Collaborations\",\"type\":\"table\",\"columns\":[{\"name\":\"title_collab\",\"label\":\"Title of Collaboration\",\"type\":\"text\"},{\"name\":\"agency_name\",\"label\":\"Collaborating Agency\",\"type\":\"text\"},{\"name\":\"fin_support\",\"label\":\"Financial Support Source\",\"type\":\"text\"},{\"name\":\"year\",\"label\":\"Year of Collaboration\",\"type\":\"number\"},{\"name\":\"duration\",\"label\":\"Duration\",\"type\":\"text\"}]},{\"name\":\"file_3_7_1\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"3.7.2\",\"title\":\"3.7.2: Functional MoUs\",\"description\":\"Number of functional MoUs with institutions, industries, corporate houses etc.\",\"fields\":[{\"name\":\"qnm_3_7_2_mous\",\"label\":\"Number of functional MoUs\",\"type\":\"number\"},{\"name\":\"table_3_7_2\",\"label\":\"Data Template: Functional MoUs\",\"type\":\"table\",\"columns\":[{\"name\":\"org_name\",\"label\":\"Organisation with MoU\",\"type\":\"text\"},{\"name\":\"inst_name\",\"label\":\"Name of Institution\\/Industry\",\"type\":\"text\"},{\"name\":\"year_sign\",\"label\":\"Year of Signing\",\"type\":\"number\"},{\"name\":\"duration\",\"label\":\"Duration\",\"type\":\"text\"},{\"name\":\"activities_list\",\"label\":\"List of Activities\",\"type\":\"text\"}]},{\"name\":\"file_3_7_2\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]}]}]}','2026-10-07 16:52:56'),(10,3,'v4_university_with_tables',1,'{\"sections\":[{\"title\":\"3.1 Promotion of Research and Facilities\",\"metrics\":[{\"id\":\"3.1.1\",\"title\":\"3.1.1: Research facilities\",\"description\":\"The institution Research facilities are frequently updated and there is well defined policy for promotion of research.\",\"fields\":[{\"name\":\"qlm_3_1_1_text\",\"label\":\"Description\",\"type\":\"textarea\"},{\"name\":\"file_3_1_1\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"3.1.2\",\"title\":\"3.1.2: Seed money for research\",\"description\":\"The institution provides seed money to its teachers for research.\",\"fields\":[{\"name\":\"qnm_3_1_2_amt\",\"label\":\"Total Amount of seed money provided (in Lakhs)\",\"type\":\"number\"},{\"name\":\"table_3_1_2\",\"label\":\"Data Template: Seed Money\",\"type\":\"table\",\"columns\":[{\"name\":\"teacher_name\",\"label\":\"Name of Teacher\",\"type\":\"text\"},{\"name\":\"amount\",\"label\":\"Amount provided (INR in Lakhs)\",\"type\":\"number\"},{\"name\":\"year\",\"label\":\"Year of receiving\",\"type\":\"number\"}]},{\"name\":\"file_3_1_2\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"3.1.3\",\"title\":\"3.1.3: Fellowships to teachers\",\"description\":\"Percentage of teachers receiving national\\/ international fellowship\\/financial support by various agencies for advanced studies\\/ research.\",\"fields\":[{\"name\":\"qnm_3_1_3_teachers\",\"label\":\"Number of teachers receiving fellowship (Numerator)\",\"type\":\"number\"},{\"name\":\"qnm_3_1_3_total\",\"label\":\"Total number of full time teachers (Denominator)\",\"type\":\"number\"},{\"name\":\"table_3_1_3\",\"label\":\"Data Template: Fellowships\",\"type\":\"table\",\"columns\":[{\"name\":\"teacher_name\",\"label\":\"Name of Teacher\",\"type\":\"text\"},{\"name\":\"fellowship_name\",\"label\":\"Name of Fellowship\",\"type\":\"text\"},{\"name\":\"year\",\"label\":\"Year of Award\",\"type\":\"number\"},{\"name\":\"awarding_agency\",\"label\":\"Awarding Agency\",\"type\":\"text\"}]},{\"name\":\"file_3_1_3\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"3.1.4\",\"title\":\"3.1.4: Research Fellows\",\"description\":\"Number of JRFs, SRFs, Post Doctoral Fellows, Research Associates and other research fellows enrolled.\",\"fields\":[{\"name\":\"qnm_3_1_4_fellows\",\"label\":\"Number of Research Fellows\",\"type\":\"number\"},{\"name\":\"table_3_1_4\",\"label\":\"Data Template: Research Fellows\",\"type\":\"table\",\"columns\":[{\"name\":\"fellow_name\",\"label\":\"Name of Research Fellow\",\"type\":\"text\"},{\"name\":\"year_enroll\",\"label\":\"Year of Enrolment\",\"type\":\"number\"},{\"name\":\"duration\",\"label\":\"Duration\",\"type\":\"text\"},{\"name\":\"type\",\"label\":\"Type (JRF\\/SRF\\/PDF\\/RA)\",\"type\":\"text\"},{\"name\":\"agency\",\"label\":\"Granting Agency\",\"type\":\"text\"}]},{\"name\":\"file_3_1_4\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"3.1.5\",\"title\":\"3.1.5: Institution Facilities\",\"description\":\"Institution has Central Instrumentation Centre, Animal House, Museum, Media laboratory, Business Lab, etc.\",\"fields\":[{\"name\":\"facilities_list\",\"label\":\"Facilities available (Check all that apply):\",\"type\":\"checkbox_group\",\"options\":[{\"name\":\"fac_cic\",\"label\":\"Central Instrumentation Centre\"},{\"name\":\"fac_animal\",\"label\":\"Animal House\\/Green House\"},{\"name\":\"fac_museum\",\"label\":\"Museum\"},{\"name\":\"fac_media\",\"label\":\"Media laboratory\\/Studios\"},{\"name\":\"fac_business\",\"label\":\"Business Lab\"}]},{\"name\":\"file_3_1_5\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"3.1.6\",\"title\":\"3.1.6: Departmental Research Schemes\",\"description\":\"Percentage of departments with UGC-SAP, CAS, DST-FIST, DBT, ICSSR etc.\",\"fields\":[{\"name\":\"qnm_3_1_6_depts\",\"label\":\"Number of Departments with schemes (Numerator)\",\"type\":\"number\"},{\"name\":\"qnm_3_1_6_total\",\"label\":\"Total number of Departments (Denominator)\",\"type\":\"number\"},{\"name\":\"table_3_1_6\",\"label\":\"Data Template: Departmental Schemes\",\"type\":\"table\",\"columns\":[{\"name\":\"dept_name\",\"label\":\"Name of Department\",\"type\":\"text\"},{\"name\":\"scheme_name\",\"label\":\"Name of Scheme\",\"type\":\"text\"},{\"name\":\"funding_agency\",\"label\":\"Funding Agency\",\"type\":\"text\"},{\"name\":\"year_award\",\"label\":\"Year of Award\",\"type\":\"number\"},{\"name\":\"funds\",\"label\":\"Funds Provided (Lakhs)\",\"type\":\"number\"}]},{\"name\":\"file_3_1_6\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]}]},{\"title\":\"3.2 Resource Mobilization for Research\",\"metrics\":[{\"id\":\"3.2.1\",\"title\":\"3.2.1: Extramural funding for Research\",\"description\":\"Extramural funding for Research (Grants in INR Lakhs).\",\"fields\":[{\"name\":\"qnm_3_2_1_funds\",\"label\":\"Total Extramural Funding (in Lakhs)\",\"type\":\"number\"},{\"name\":\"table_3_2_1\",\"label\":\"Data Template: Extramural Funding\",\"type\":\"table\",\"columns\":[{\"name\":\"pi_name\",\"label\":\"Name of PI\\/Co-PI\",\"type\":\"text\"},{\"name\":\"proj_title\",\"label\":\"Title of Project\",\"type\":\"text\"},{\"name\":\"funding_agency\",\"label\":\"Funding Agency\",\"type\":\"text\"},{\"name\":\"dept\",\"label\":\"Department\",\"type\":\"text\"},{\"name\":\"year_award\",\"label\":\"Year of Award\",\"type\":\"number\"},{\"name\":\"funds\",\"label\":\"Funds Provided (Lakhs)\",\"type\":\"number\"},{\"name\":\"duration\",\"label\":\"Duration (Years)\",\"type\":\"number\"}]},{\"name\":\"file_3_2_1\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"3.2.2\",\"title\":\"3.2.2: Grants for research projects\",\"description\":\"Grants for research projects sponsored by the government agencies.\",\"fields\":[{\"name\":\"qnm_3_2_2_grants\",\"label\":\"Total Grants for Research Projects (in Lakhs)\",\"type\":\"number\"},{\"name\":\"table_3_2_2\",\"label\":\"Data Template: Government Grants\",\"type\":\"table\",\"columns\":[{\"name\":\"pi_name\",\"label\":\"Name of PI\\/Co-PI\",\"type\":\"text\"},{\"name\":\"proj_title\",\"label\":\"Title of Project\",\"type\":\"text\"},{\"name\":\"funding_agency\",\"label\":\"Funding Agency\",\"type\":\"text\"},{\"name\":\"dept\",\"label\":\"Department\",\"type\":\"text\"},{\"name\":\"year_award\",\"label\":\"Year of Award\",\"type\":\"number\"},{\"name\":\"funds\",\"label\":\"Funds Provided (Lakhs)\",\"type\":\"number\"},{\"name\":\"duration\",\"label\":\"Duration (Years)\",\"type\":\"number\"}]},{\"name\":\"file_3_2_2\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"3.2.3\",\"title\":\"3.2.3: Research projects per teacher\",\"description\":\"Number of research projects per teacher funded by government and non-government agencies.\",\"fields\":[{\"name\":\"qnm_3_2_3_projects\",\"label\":\"Number of research projects (Numerator)\",\"type\":\"number\"},{\"name\":\"qnm_3_2_3_teachers\",\"label\":\"Total number of full time teachers (Denominator)\",\"type\":\"number\"},{\"name\":\"table_3_2_3\",\"label\":\"Data Template: Projects Per Teacher\",\"type\":\"table\",\"columns\":[{\"name\":\"pi_name\",\"label\":\"Name of PI\\/Co-PI\",\"type\":\"text\"},{\"name\":\"proj_title\",\"label\":\"Title of Project\",\"type\":\"text\"},{\"name\":\"funding_agency\",\"label\":\"Funding Agency\",\"type\":\"text\"},{\"name\":\"dept\",\"label\":\"Department\",\"type\":\"text\"},{\"name\":\"year_award\",\"label\":\"Year of Award\",\"type\":\"number\"},{\"name\":\"funds\",\"label\":\"Funds Provided (Lakhs)\",\"type\":\"number\"},{\"name\":\"duration\",\"label\":\"Duration (Years)\",\"type\":\"number\"}]},{\"name\":\"file_3_2_3\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]}]},{\"title\":\"3.3 Innovation Ecosystem\",\"metrics\":[{\"id\":\"3.3.1\",\"title\":\"3.3.1: Innovation ecosystem\",\"description\":\"Institution has created an eco system for innovations including Incubation centre.\",\"fields\":[{\"name\":\"qlm_3_3_1_text\",\"label\":\"Description\",\"type\":\"textarea\"},{\"name\":\"file_3_3_1\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"3.3.2\",\"title\":\"3.3.2: Workshops\\/seminars on Research\\/IPR\",\"description\":\"Number of workshops\\/seminars conducted on Research methodology, IPR, entrepreneurship.\",\"fields\":[{\"name\":\"qnm_3_3_2_count\",\"label\":\"Number of workshops\\/seminars\",\"type\":\"number\"},{\"name\":\"table_3_3_2\",\"label\":\"Data Template: Workshops\\/Seminars\",\"type\":\"table\",\"columns\":[{\"name\":\"workshop_name\",\"label\":\"Name of Workshop\",\"type\":\"text\"},{\"name\":\"participants\",\"label\":\"Number of Participants\",\"type\":\"number\"},{\"name\":\"date_range\",\"label\":\"Date (From - To)\",\"type\":\"text\"}]},{\"name\":\"file_3_3_2\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"3.3.3\",\"title\":\"3.3.3: Innovation Awards\",\"description\":\"Number of awards \\/ recognitions received for research\\/innovations.\",\"fields\":[{\"name\":\"qnm_3_3_3_awards\",\"label\":\"Number of awards\\/recognitions\",\"type\":\"number\"},{\"name\":\"table_3_3_3\",\"label\":\"Data Template: Innovation Awards\",\"type\":\"table\",\"columns\":[{\"name\":\"title_innov\",\"label\":\"Title of Innovation\",\"type\":\"text\"},{\"name\":\"awardee_name\",\"label\":\"Name of Awardee\",\"type\":\"text\"},{\"name\":\"agency_name\",\"label\":\"Awarding Agency\",\"type\":\"text\"},{\"name\":\"year_award\",\"label\":\"Year of Award\",\"type\":\"number\"}]},{\"name\":\"file_3_3_3\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]}]},{\"title\":\"3.4 Research Publications and Awards\",\"metrics\":[{\"id\":\"3.4.1\",\"title\":\"3.4.1: Code of Ethics\",\"description\":\"The Institution ensures implementation of its stated Code of Ethics for research.\",\"fields\":[{\"name\":\"ethics_list\",\"label\":\"Options (Check all that apply):\",\"type\":\"checkbox_group\",\"options\":[{\"name\":\"eth_comm\",\"label\":\"Ethics Committee exists\"},{\"name\":\"eth_plag\",\"label\":\"Plagiarism check software\"},{\"name\":\"eth_comm_meets\",\"label\":\"Research Advisory Committee meets\"}]},{\"name\":\"file_3_4_1\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"3.4.2\",\"title\":\"3.4.2: Teacher Incentives\",\"description\":\"The institution provides incentives to teachers who receive state, national and international recognitions.\",\"fields\":[{\"name\":\"incentives_list\",\"label\":\"Incentives (Check all that apply):\",\"type\":\"checkbox_group\",\"options\":[{\"name\":\"inc_salary\",\"label\":\"Commendation & monetary reward\"},{\"name\":\"inc_cert\",\"label\":\"Certificate of honor\"}]},{\"name\":\"file_3_4_2\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"3.4.3\",\"title\":\"3.4.3: Patents\",\"description\":\"Number of Patents published\\/awarded.\",\"fields\":[{\"name\":\"qnm_3_4_3_patents\",\"label\":\"Number of Patents\",\"type\":\"number\"},{\"name\":\"table_3_4_3\",\"label\":\"Data Template: Patents\",\"type\":\"table\",\"columns\":[{\"name\":\"patenter_name\",\"label\":\"Name of Patenter\",\"type\":\"text\"},{\"name\":\"patent_num\",\"label\":\"Patent Number\",\"type\":\"text\"},{\"name\":\"patent_title\",\"label\":\"Title of Patent\",\"type\":\"text\"},{\"name\":\"year_award\",\"label\":\"Year of Award\",\"type\":\"number\"}]},{\"name\":\"file_3_4_3\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"3.4.4\",\"title\":\"3.4.4: Ph.D\'s awarded per teacher\",\"description\":\"Number of Ph.D\'s awarded per teacher.\",\"fields\":[{\"name\":\"qnm_3_4_4_phd\",\"label\":\"Number of Ph.D\'s awarded (Numerator)\",\"type\":\"number\"},{\"name\":\"qnm_3_4_4_guides\",\"label\":\"Number of recognized guides (Denominator)\",\"type\":\"number\"},{\"name\":\"table_3_4_4\",\"label\":\"Data Template: Ph.D.s Awarded\",\"type\":\"table\",\"columns\":[{\"name\":\"scholar_name\",\"label\":\"Name of Scholar\",\"type\":\"text\"},{\"name\":\"dept\",\"label\":\"Department\",\"type\":\"text\"},{\"name\":\"guide_name\",\"label\":\"Name of Guide\",\"type\":\"text\"},{\"name\":\"thesis_title\",\"label\":\"Title of Thesis\",\"type\":\"text\"},{\"name\":\"year_reg\",\"label\":\"Year of Registration\",\"type\":\"number\"},{\"name\":\"year_award\",\"label\":\"Year of Award\",\"type\":\"number\"}]},{\"name\":\"file_3_4_4\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"3.4.5\",\"title\":\"3.4.5: Research Papers in UGC Journals\",\"description\":\"Number of research papers per teacher in the Journals notified on UGC website.\",\"fields\":[{\"name\":\"qnm_3_4_5_papers\",\"label\":\"Number of Research Papers (Numerator)\",\"type\":\"number\"},{\"name\":\"qnm_3_4_5_teachers\",\"label\":\"Number of Full-Time Teachers (Denominator)\",\"type\":\"number\"},{\"name\":\"table_3_4_5\",\"label\":\"Data Template: Research Papers\",\"type\":\"table\",\"columns\":[{\"name\":\"paper_title\",\"label\":\"Title of Paper\",\"type\":\"text\"},{\"name\":\"author_name\",\"label\":\"Name of Author\",\"type\":\"text\"},{\"name\":\"dept\",\"label\":\"Department\",\"type\":\"text\"},{\"name\":\"journal_name\",\"label\":\"Journal Name\",\"type\":\"text\"},{\"name\":\"year_pub\",\"label\":\"Year of Publication\",\"type\":\"number\"},{\"name\":\"issn\",\"label\":\"ISSN Number\",\"type\":\"text\"}]},{\"name\":\"file_3_4_5\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"3.4.6\",\"title\":\"3.4.6: Books and Chapters published\",\"description\":\"Number of books and chapters in edited volumes\\/books published.\",\"fields\":[{\"name\":\"qnm_3_4_6_books\",\"label\":\"Number of books and chapters\",\"type\":\"number\"},{\"name\":\"table_3_4_6\",\"label\":\"Data Template: Books and Chapters\",\"type\":\"table\",\"columns\":[{\"name\":\"book_title\",\"label\":\"Title of Book\\/Chapter\",\"type\":\"text\"},{\"name\":\"author_name\",\"label\":\"Author\",\"type\":\"text\"},{\"name\":\"proceedings\",\"label\":\"Title of proceedings\",\"type\":\"text\"},{\"name\":\"publisher\",\"label\":\"Publisher Name\",\"type\":\"text\"},{\"name\":\"isbn\",\"label\":\"ISBN\",\"type\":\"text\"},{\"name\":\"year_pub\",\"label\":\"Year of Publication\",\"type\":\"number\"}]},{\"name\":\"file_3_4_6\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"3.4.7\",\"title\":\"3.4.7: E-content developed by teachers\",\"description\":\"E-content developed by teachers for platforms like NMEICT\\/NPTEL\\/SWAYAM.\",\"fields\":[{\"name\":\"qnm_3_4_7_content\",\"label\":\"Number of E-contents developed\",\"type\":\"number\"},{\"name\":\"table_3_4_7\",\"label\":\"Data Template: E-Content\",\"type\":\"table\",\"columns\":[{\"name\":\"teacher_name\",\"label\":\"Name of Teacher\",\"type\":\"text\"},{\"name\":\"module_name\",\"label\":\"Name of Module\",\"type\":\"text\"},{\"name\":\"platform\",\"label\":\"Platform (SWAYAM etc)\",\"type\":\"text\"},{\"name\":\"date_launch\",\"label\":\"Date of Launch\",\"type\":\"text\"}]},{\"name\":\"file_3_4_7\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"3.4.8\",\"title\":\"3.4.8: Bibliometrics (Citation Index)\",\"description\":\"Bibliometrics of the publications based on average Citation Index in Scopus\\/ Web of Science.\",\"fields\":[{\"name\":\"qnm_3_4_8_citation\",\"label\":\"Average Citation Index\",\"type\":\"number\"},{\"name\":\"file_3_4_8\",\"label\":\"Supporting Document (Citation Index)\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"3.4.9\",\"title\":\"3.4.9: Bibliometrics (h-index)\",\"description\":\"Bibliometrics of the publications based on Scopus\\/ Web of Science - h-index.\",\"fields\":[{\"name\":\"qnm_3_4_9_hindex\",\"label\":\"h-index of the institution\",\"type\":\"number\"},{\"name\":\"file_3_4_9\",\"label\":\"Supporting Document (h-index)\",\"type\":\"file\",\"accept\":\".pdf\"}]}]},{\"title\":\"3.5 Consultancy\",\"metrics\":[{\"id\":\"3.5.1\",\"title\":\"3.5.1: Consultancy Policy\",\"description\":\"Institution has a policy on consultancy including revenue sharing.\",\"fields\":[{\"name\":\"qlm_3_5_1_text\",\"label\":\"Description\",\"type\":\"textarea\"},{\"name\":\"file_3_5_1\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"3.5.2\",\"title\":\"3.5.2: Revenue from Consultancy\",\"description\":\"Revenue generated from consultancy and corporate training.\",\"fields\":[{\"name\":\"qnm_3_5_2_revenue\",\"label\":\"Total Revenue Generated (in Lakhs)\",\"type\":\"number\"},{\"name\":\"table_3_5_2\",\"label\":\"Data Template: Consultancy Revenue\",\"type\":\"table\",\"columns\":[{\"name\":\"consultant_name\",\"label\":\"Name of Consultant\",\"type\":\"text\"},{\"name\":\"project_name\",\"label\":\"Name of Project\",\"type\":\"text\"},{\"name\":\"agency_name\",\"label\":\"Sponsoring Agency\",\"type\":\"text\"},{\"name\":\"revenue\",\"label\":\"Revenue (Lakhs)\",\"type\":\"number\"}]},{\"name\":\"file_3_5_2\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]}]},{\"title\":\"3.6 Extension Activities\",\"metrics\":[{\"id\":\"3.6.1\",\"title\":\"3.6.1: Extension activities in neighbourhood\",\"description\":\"Extension activities in the neighbourhood community in terms of impact.\",\"fields\":[{\"name\":\"qlm_3_6_1_text\",\"label\":\"Description\",\"type\":\"textarea\"},{\"name\":\"file_3_6_1\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"3.6.2\",\"title\":\"3.6.2: Extension Awards\",\"description\":\"Number of awards received by the Institution, its teachers and students for extension activities.\",\"fields\":[{\"name\":\"qnm_3_6_2_awards\",\"label\":\"Number of awards\",\"type\":\"number\"},{\"name\":\"table_3_6_2\",\"label\":\"Data Template: Extension Awards\",\"type\":\"table\",\"columns\":[{\"name\":\"activity_name\",\"label\":\"Name of Activity\",\"type\":\"text\"},{\"name\":\"award_name\",\"label\":\"Name of Award\",\"type\":\"text\"},{\"name\":\"awarding_body\",\"label\":\"Awarding Body\",\"type\":\"text\"},{\"name\":\"year\",\"label\":\"Year of Award\",\"type\":\"number\"}]},{\"name\":\"file_3_6_2\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"3.6.3\",\"title\":\"3.6.3: Extension Programs Conducted\",\"description\":\"Number of extension and outreach programs conducted in collaboration with industry, community and Non-Government Organisations.\",\"fields\":[{\"name\":\"qnm_3_6_3_programs\",\"label\":\"Number of programs conducted\",\"type\":\"number\"},{\"name\":\"table_3_6_3\",\"label\":\"Data Template: Extension Programs\",\"type\":\"table\",\"columns\":[{\"name\":\"activity_name\",\"label\":\"Name of Activity\",\"type\":\"text\"},{\"name\":\"organising_unit\",\"label\":\"Organising Unit\\/Agency\",\"type\":\"text\"},{\"name\":\"scheme_name\",\"label\":\"Name of Scheme\",\"type\":\"text\"},{\"name\":\"year\",\"label\":\"Year\",\"type\":\"number\"}]},{\"name\":\"file_3_6_3\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"3.6.4\",\"title\":\"3.6.4: Student Participation in Extension\",\"description\":\"Average percentage of students participating in extension activities listed at 3.6.3 above.\",\"fields\":[{\"name\":\"qnm_3_6_4_participating\",\"label\":\"Number of students participating (Numerator)\",\"type\":\"number\"},{\"name\":\"qnm_3_6_4_total\",\"label\":\"Total number of students (Denominator)\",\"type\":\"number\"},{\"name\":\"table_3_6_4\",\"label\":\"Data Template: Student Participation\",\"type\":\"table\",\"columns\":[{\"name\":\"activity_name\",\"label\":\"Name of Activity\",\"type\":\"text\"},{\"name\":\"organising_unit\",\"label\":\"Organising Unit\\/Agency\",\"type\":\"text\"},{\"name\":\"scheme_name\",\"label\":\"Name of Scheme\",\"type\":\"text\"},{\"name\":\"year\",\"label\":\"Year\",\"type\":\"number\"},{\"name\":\"student_count\",\"label\":\"Number of Students Participated\",\"type\":\"number\"}]},{\"name\":\"file_3_6_4\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]}]},{\"title\":\"3.7 Collaboration\",\"metrics\":[{\"id\":\"3.7.1\",\"title\":\"3.7.1: Collaborative activities\",\"description\":\"Number of collaborative activities for research, Faculty exchange, Student exchange.\",\"fields\":[{\"name\":\"qnm_3_7_1_collabs\",\"label\":\"Number of collaborative activities\",\"type\":\"number\"},{\"name\":\"table_3_7_1\",\"label\":\"Data Template: Collaborations\",\"type\":\"table\",\"columns\":[{\"name\":\"title_collab\",\"label\":\"Title of Collaboration\",\"type\":\"text\"},{\"name\":\"agency_name\",\"label\":\"Collaborating Agency\",\"type\":\"text\"},{\"name\":\"fin_support\",\"label\":\"Financial Support Source\",\"type\":\"text\"},{\"name\":\"year\",\"label\":\"Year of Collaboration\",\"type\":\"number\"},{\"name\":\"duration\",\"label\":\"Duration\",\"type\":\"text\"}]},{\"name\":\"file_3_7_1\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"3.7.2\",\"title\":\"3.7.2: Functional MoUs\",\"description\":\"Number of functional MoUs with institutions, industries, corporate houses etc.\",\"fields\":[{\"name\":\"qnm_3_7_2_mous\",\"label\":\"Number of functional MoUs\",\"type\":\"number\"},{\"name\":\"table_3_7_2\",\"label\":\"Data Template: Functional MoUs\",\"type\":\"table\",\"columns\":[{\"name\":\"org_name\",\"label\":\"Organisation with MoU\",\"type\":\"text\"},{\"name\":\"inst_name\",\"label\":\"Name of Institution\\/Industry\",\"type\":\"text\"},{\"name\":\"year_sign\",\"label\":\"Year of Signing\",\"type\":\"number\"},{\"name\":\"duration\",\"label\":\"Duration\",\"type\":\"text\"},{\"name\":\"activities_list\",\"label\":\"List of Activities\",\"type\":\"text\"}]},{\"name\":\"file_3_7_2\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]}]}]}','2026-10-07 17:00:48'),(11,4,'v4_university_with_tables',0,'{\"sections\":[{\"title\":\"4.1 Physical Facilities\",\"metrics\":[{\"id\":\"4.1.1\",\"title\":\"4.1.1: Facilities for teaching-learning\",\"description\":\"The institution has adequate facilities for teaching- learning. viz., classrooms, laboratories, computing equipment, etc.\",\"fields\":[{\"name\":\"qlm_4_1_1_text\",\"label\":\"Description\",\"type\":\"textarea\"},{\"name\":\"file_4_1_1\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"4.1.2\",\"title\":\"4.1.2: Facilities for sports and cultural activities\",\"description\":\"The institution has adequate facilities for cultural activities, yoga, games (indoor, outdoor) and sports.\",\"fields\":[{\"name\":\"qlm_4_1_2_text\",\"label\":\"Description\",\"type\":\"textarea\"},{\"name\":\"file_4_1_2\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"4.1.3\",\"title\":\"4.1.3: ICT-enabled classrooms\",\"description\":\"Percentage of classrooms and seminar halls with ICT- enabled facilities such as smart class, LMS, etc.\",\"fields\":[{\"name\":\"qnm_4_1_3_ict\",\"label\":\"Number of classrooms and seminar halls with ICT facilities (Numerator)\",\"type\":\"number\"},{\"name\":\"qnm_4_1_3_total\",\"label\":\"Total number of classrooms and seminar halls in the institution (Denominator)\",\"type\":\"number\"},{\"name\":\"table_4_1_3\",\"label\":\"Data Template: ICT Classrooms\",\"type\":\"table\",\"columns\":[{\"name\":\"room_no\",\"label\":\"Room number or Name of classrooms\\/Seminar Hall\",\"type\":\"text\"},{\"name\":\"facility_type\",\"label\":\"Type of ICT facility\",\"type\":\"text\"}]},{\"name\":\"file_4_1_3\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"4.1.4\",\"title\":\"4.1.4: Expenditure for infrastructure augmentation\",\"description\":\"Average percentage of expenditure, excluding salary for infrastructure augmentation during last five years.\",\"fields\":[{\"name\":\"qnm_4_1_4_aug\",\"label\":\"Expenditure for infrastructure augmentation (Numerator) (Lakhs)\",\"type\":\"number\"},{\"name\":\"qnm_4_1_4_total\",\"label\":\"Total expenditure excluding salary (Denominator) (Lakhs)\",\"type\":\"number\"},{\"name\":\"table_4_1_4\",\"label\":\"Data Template: Infrastructure Expenditure\",\"type\":\"table\",\"columns\":[{\"name\":\"year\",\"label\":\"Year\",\"type\":\"number\"},{\"name\":\"budget_infra\",\"label\":\"Budget allocated for infra (Lakhs)\",\"type\":\"number\"},{\"name\":\"exp_infra\",\"label\":\"Exp for infra (Lakhs)\",\"type\":\"number\"},{\"name\":\"total_exp\",\"label\":\"Total exp excluding salary (Lakhs)\",\"type\":\"number\"}]},{\"name\":\"file_4_1_4\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]}]},{\"title\":\"4.2 Library as a Learning Resource\",\"metrics\":[{\"id\":\"4.2.1\",\"title\":\"4.2.1: Library automation (ILMS)\",\"description\":\"Library is automated using Integrated Library Management System (ILMS).\",\"fields\":[{\"name\":\"qlm_4_2_1_text\",\"label\":\"Description\",\"type\":\"textarea\"},{\"name\":\"file_4_2_1\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"4.2.2\",\"title\":\"4.2.2: E-resources subscription\",\"description\":\"The institution has subscription for the following e-resources.\",\"fields\":[{\"name\":\"eresources_list\",\"label\":\"Options (Check all that apply):\",\"type\":\"checkbox_group\",\"options\":[{\"name\":\"e_journals\",\"label\":\"1. e-journals\"},{\"name\":\"e_shodh\",\"label\":\"2. e-ShodhSindhu\"},{\"name\":\"e_shodhganga\",\"label\":\"3. Shodhganga Membership\"},{\"name\":\"e_books\",\"label\":\"4. e-books\"},{\"name\":\"e_databases\",\"label\":\"5. Databases\"},{\"name\":\"e_remote\",\"label\":\"6. Remote access to e-resources\"}]},{\"name\":\"file_4_2_2\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"4.2.3\",\"title\":\"4.2.3: Expenditure on books and journals\",\"description\":\"Average annual expenditure for purchase of books\\/e-books and subscription to journals\\/e-journals during the last five years.\",\"fields\":[{\"name\":\"qnm_4_2_3_exp\",\"label\":\"Annual expenditure on books and journals (Lakhs)\",\"type\":\"number\"},{\"name\":\"table_4_2_3\",\"label\":\"Data Template: Books & Journals Expenditure\",\"type\":\"table\",\"columns\":[{\"name\":\"year\",\"label\":\"Year\",\"type\":\"number\"},{\"name\":\"exp_books\",\"label\":\"Exp on books\\/e-books (Lakhs)\",\"type\":\"number\"},{\"name\":\"exp_journals\",\"label\":\"Exp on journals\\/e-journals (Lakhs)\",\"type\":\"number\"}]},{\"name\":\"file_4_2_3\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"4.2.4\",\"title\":\"4.2.4: Library usage per day\",\"description\":\"Percentage per day usage of library by teachers and students.\",\"fields\":[{\"name\":\"qnm_4_2_4_usage\",\"label\":\"Number of teachers and students using library per day (Numerator)\",\"type\":\"number\"},{\"name\":\"qnm_4_2_4_total\",\"label\":\"Total number of teachers and students (Denominator)\",\"type\":\"number\"},{\"name\":\"file_4_2_4\",\"label\":\"Supporting Document (e.g. Method of computing per day usage)\",\"type\":\"file\",\"accept\":\".pdf\"}]}]},{\"title\":\"4.3 IT Infrastructure\",\"metrics\":[{\"id\":\"4.3.1\",\"title\":\"4.3.1: IT updates and bandwidth\",\"description\":\"Institution frequently updates its IT facilities and provides sufficient bandwidth for internet connection.\",\"fields\":[{\"name\":\"qlm_4_3_1_text\",\"label\":\"Description\",\"type\":\"textarea\"},{\"name\":\"file_4_3_1\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"4.3.2\",\"title\":\"4.3.2: Student - Computer ratio\",\"description\":\"Student \\u2013 Computer ratio.\",\"fields\":[{\"name\":\"qnm_4_3_2_students\",\"label\":\"Total number of students (Numerator)\",\"type\":\"number\"},{\"name\":\"qnm_4_3_2_computers\",\"label\":\"Total number of computers in working condition (Denominator)\",\"type\":\"number\"},{\"name\":\"file_4_3_2\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"4.3.3\",\"title\":\"4.3.3: Internet Bandwidth\",\"description\":\"Bandwidth of internet connection in the Institution.\",\"fields\":[{\"name\":\"bandwidth_opts\",\"label\":\"Select available bandwidth (Check one):\",\"type\":\"checkbox_group\",\"options\":[{\"name\":\"bw_1gbps\",\"label\":\"\\u2265 1 GBPS\"},{\"name\":\"bw_500mbps\",\"label\":\"500 MBPS - 1 GBPS\"},{\"name\":\"bw_250mbps\",\"label\":\"250 MBPS - 500 MBPS\"},{\"name\":\"bw_50mbps\",\"label\":\"50 MBPS - 250 MBPS\"},{\"name\":\"bw_less50\",\"label\":\"< 50 MBPS\"}]},{\"name\":\"file_4_3_3\",\"label\":\"Supporting Document (Bills\\/Agreements)\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"4.3.4\",\"title\":\"4.3.4: E-content development facilities\",\"description\":\"Facilities for e-content development such as Media centre, Audio visual centre, Lecture Capturing System (LCS).\",\"fields\":[{\"name\":\"econtent_facs\",\"label\":\"Available facilities (Check all that apply):\",\"type\":\"checkbox_group\",\"options\":[{\"name\":\"ec_media\",\"label\":\"1. Media centre\"},{\"name\":\"ec_audio\",\"label\":\"2. Audio visual centre\"},{\"name\":\"ec_lecture\",\"label\":\"3. Lecture Capturing System(LCS)\"},{\"name\":\"ec_mixing\",\"label\":\"4. Mixing equipments and softwares for editing\"}]},{\"name\":\"file_4_3_4\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]}]},{\"title\":\"4.4 Maintenance of Campus Infrastructure\",\"metrics\":[{\"id\":\"4.4.1\",\"title\":\"4.4.1: Expenditure on maintenance\",\"description\":\"Average percentage of expenditure incurred on maintenance of infrastructure (physical and academic support facilities) excluding salary component.\",\"fields\":[{\"name\":\"qnm_4_4_1_maint\",\"label\":\"Expenditure on maintenance of infra (Numerator) (Lakhs)\",\"type\":\"number\"},{\"name\":\"qnm_4_4_1_total\",\"label\":\"Total expenditure excluding salary (Denominator) (Lakhs)\",\"type\":\"number\"},{\"name\":\"table_4_4_1\",\"label\":\"Data Template: Maintenance Expenditure\",\"type\":\"table\",\"columns\":[{\"name\":\"year\",\"label\":\"Year\",\"type\":\"number\"},{\"name\":\"budget_acad\",\"label\":\"Assigned budget on academic facilities (Lakhs)\",\"type\":\"number\"},{\"name\":\"exp_acad\",\"label\":\"Exp on academic facilities (Lakhs)\",\"type\":\"number\"},{\"name\":\"budget_phys\",\"label\":\"Assigned budget on physical facilities (Lakhs)\",\"type\":\"number\"},{\"name\":\"exp_phys\",\"label\":\"Exp on physical facilities (Lakhs)\",\"type\":\"number\"}]},{\"name\":\"file_4_4_1\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"4.4.2\",\"title\":\"4.4.2: Maintenance systems and procedures\",\"description\":\"There are established systems and procedures for maintaining and utilizing physical, academic and support facilities.\",\"fields\":[{\"name\":\"qlm_4_4_2_text\",\"label\":\"Description\",\"type\":\"textarea\"},{\"name\":\"qlm_4_4_2_url\",\"label\":\"URL for policy document\",\"type\":\"url\"},{\"name\":\"file_4_4_2\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]}]}]}','2026-10-07 17:04:31'),(12,4,'v4_university_with_tables',1,'{\"sections\":[{\"title\":\"4.1 Physical Facilities\",\"metrics\":[{\"id\":\"4.1.1\",\"title\":\"4.1.1: Facilities for teaching-learning\",\"description\":\"The institution has adequate facilities for teaching- learning. viz., classrooms, laboratories, computing equipment, etc.\",\"fields\":[{\"name\":\"qlm_4_1_1_text\",\"label\":\"Description\",\"type\":\"textarea\"},{\"name\":\"file_4_1_1\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"4.1.2\",\"title\":\"4.1.2: Facilities for sports and cultural activities\",\"description\":\"The institution has adequate facilities for cultural activities, yoga, games (indoor, outdoor) and sports.\",\"fields\":[{\"name\":\"qlm_4_1_2_text\",\"label\":\"Description\",\"type\":\"textarea\"},{\"name\":\"file_4_1_2\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"4.1.3\",\"title\":\"4.1.3: General campus facilities\",\"description\":\"Availability of general campus facilities and overall ambience.\",\"fields\":[{\"name\":\"qlm_4_1_3_text\",\"label\":\"Description\",\"type\":\"textarea\"},{\"name\":\"file_4_1_3\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"4.1.4\",\"title\":\"4.1.4: Expenditure for infrastructure augmentation\",\"description\":\"Average percentage of expenditure excluding salary, for infrastructure augmentation during the last five years.\",\"fields\":[{\"name\":\"qnm_4_1_4_aug\",\"label\":\"Expenditure for infrastructure augmentation (Numerator) (Lakhs)\",\"type\":\"number\"},{\"name\":\"qnm_4_1_4_total\",\"label\":\"Total expenditure excluding salary (Denominator) (Lakhs)\",\"type\":\"number\"},{\"name\":\"table_4_1_4\",\"label\":\"Data Template: Infrastructure Expenditure\",\"type\":\"table\",\"columns\":[{\"name\":\"year\",\"label\":\"Year\",\"type\":\"number\"},{\"name\":\"budget_infra\",\"label\":\"Budget allocated for infra (Lakhs)\",\"type\":\"number\"},{\"name\":\"exp_infra\",\"label\":\"Exp for infra (Lakhs)\",\"type\":\"number\"},{\"name\":\"total_exp\",\"label\":\"Total exp excluding salary (Lakhs)\",\"type\":\"number\"}]},{\"name\":\"file_4_1_4\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]}]},{\"title\":\"4.2 Library as a Learning Resource\",\"metrics\":[{\"id\":\"4.2.1\",\"title\":\"4.2.1: Library automation (ILMS)\",\"description\":\"Library is automated using Integrated Library Management System (ILMS).\",\"fields\":[{\"name\":\"qlm_4_2_1_text\",\"label\":\"Description\",\"type\":\"textarea\"},{\"name\":\"file_4_2_1\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"4.2.2\",\"title\":\"4.2.2: E-resources subscription\",\"description\":\"The institution has subscription for the following e-resources.\",\"fields\":[{\"name\":\"eresources_list\",\"label\":\"Options (Check all that apply):\",\"type\":\"checkbox_group\",\"options\":[{\"name\":\"e_journals\",\"label\":\"1. e-journals\"},{\"name\":\"e_shodh\",\"label\":\"2. e-ShodhSindhu\"},{\"name\":\"e_shodhganga\",\"label\":\"3. Shodhganga Membership\"},{\"name\":\"e_books\",\"label\":\"4. e-books\"},{\"name\":\"e_databases\",\"label\":\"5. Databases\"},{\"name\":\"e_remote\",\"label\":\"6. Remote access to e-resources\"}]},{\"name\":\"file_4_2_2\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"4.2.3\",\"title\":\"4.2.3: Expenditure on books and journals\",\"description\":\"Average annual expenditure for purchase of books\\/e-books and subscription to journals\\/e-journals during the last five years.\",\"fields\":[{\"name\":\"qnm_4_2_3_exp\",\"label\":\"Annual expenditure on books and journals (Lakhs)\",\"type\":\"number\"},{\"name\":\"table_4_2_3\",\"label\":\"Data Template: Books & Journals Expenditure\",\"type\":\"table\",\"columns\":[{\"name\":\"year\",\"label\":\"Year\",\"type\":\"number\"},{\"name\":\"exp_books\",\"label\":\"Exp on books\\/e-books (Lakhs)\",\"type\":\"number\"},{\"name\":\"exp_journals\",\"label\":\"Exp on journals\\/e-journals (Lakhs)\",\"type\":\"number\"}]},{\"name\":\"file_4_2_3\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"4.2.4\",\"title\":\"4.2.4: Library usage per day\",\"description\":\"Percentage per day usage of library by teachers and students.\",\"fields\":[{\"name\":\"qnm_4_2_4_usage\",\"label\":\"Number of teachers and students using library per day (Numerator)\",\"type\":\"number\"},{\"name\":\"qnm_4_2_4_total\",\"label\":\"Total number of teachers and students (Denominator)\",\"type\":\"number\"},{\"name\":\"file_4_2_4\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]}]},{\"title\":\"4.3 IT Infrastructure\",\"metrics\":[{\"id\":\"4.3.1\",\"title\":\"4.3.1: ICT-enabled classrooms\",\"description\":\"Percentage of classrooms and seminar halls with ICT- enabled facilities such as LCD, smart board, Wi-Fi\\/LAN.\",\"fields\":[{\"name\":\"qnm_4_3_1_ict\",\"label\":\"Number of classrooms and seminar halls with ICT facilities (Numerator)\",\"type\":\"number\"},{\"name\":\"qnm_4_3_1_total\",\"label\":\"Total number of classrooms and seminar halls (Denominator)\",\"type\":\"number\"},{\"name\":\"table_4_3_1\",\"label\":\"Data Template: ICT Classrooms\",\"type\":\"table\",\"columns\":[{\"name\":\"room_no\",\"label\":\"Room number or Name of classrooms\\/Seminar Hall\",\"type\":\"text\"},{\"name\":\"facility_type\",\"label\":\"Type of ICT facility\",\"type\":\"text\"}]},{\"name\":\"file_4_3_1\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"4.3.2\",\"title\":\"4.3.2: IT updates and Wi-Fi\",\"description\":\"Institution has an IT policy, makes appropriate budgetary provisions and updates its IT facilities including the Wi-Fi facility.\",\"fields\":[{\"name\":\"qlm_4_3_2_text\",\"label\":\"Description\",\"type\":\"textarea\"},{\"name\":\"file_4_3_2\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"4.3.3\",\"title\":\"4.3.3: Student - Computer ratio\",\"description\":\"Student \\u2013 Computer ratio.\",\"fields\":[{\"name\":\"qnm_4_3_3_students\",\"label\":\"Total number of students (Numerator)\",\"type\":\"number\"},{\"name\":\"qnm_4_3_3_computers\",\"label\":\"Total number of computers in working condition (Denominator)\",\"type\":\"number\"},{\"name\":\"file_4_3_3\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"4.3.4\",\"title\":\"4.3.4: Internet Bandwidth\",\"description\":\"Available bandwidth of internet connection in the Institution.\",\"fields\":[{\"name\":\"bandwidth_opts\",\"label\":\"Select available bandwidth (Check one):\",\"type\":\"checkbox_group\",\"options\":[{\"name\":\"bw_1gbps\",\"label\":\"A. \\u2265 1 GBPS\"},{\"name\":\"bw_500mbps\",\"label\":\"B. 500 MBPS - 1 GBPS\"},{\"name\":\"bw_250mbps\",\"label\":\"C. 250 MBPS - 500 MBPS\"},{\"name\":\"bw_50mbps\",\"label\":\"D. 50 MBPS - 250 MBPS\"},{\"name\":\"bw_less50\",\"label\":\"E. < 50 MBPS\"}]},{\"name\":\"file_4_3_4\",\"label\":\"Supporting Document (Bills\\/Agreements)\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"4.3.5\",\"title\":\"4.3.5: E-content development facilities\",\"description\":\"Institution has the following Facilities for e-content development.\",\"fields\":[{\"name\":\"econtent_facs\",\"label\":\"Available facilities (Check all that apply):\",\"type\":\"checkbox_group\",\"options\":[{\"name\":\"ec_media\",\"label\":\"1. Media centre\"},{\"name\":\"ec_audio\",\"label\":\"2. Audio visual centre\"},{\"name\":\"ec_lecture\",\"label\":\"3. Lecture Capturing System(LCS)\"},{\"name\":\"ec_mixing\",\"label\":\"4. Mixing equipments and softwares for editing\"}]},{\"name\":\"file_4_3_5\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]}]},{\"title\":\"4.4 Maintenance of Campus Infrastructure\",\"metrics\":[{\"id\":\"4.4.1\",\"title\":\"4.4.1: Expenditure on maintenance\",\"description\":\"Average percentage of expenditure incurred on maintenance of physical facilities and academic support facilities excluding salary.\",\"fields\":[{\"name\":\"qnm_4_4_1_maint\",\"label\":\"Expenditure on maintenance (Numerator) (Lakhs)\",\"type\":\"number\"},{\"name\":\"qnm_4_4_1_total\",\"label\":\"Total expenditure excluding salary (Denominator) (Lakhs)\",\"type\":\"number\"},{\"name\":\"table_4_4_1\",\"label\":\"Data Template: Maintenance Expenditure\",\"type\":\"table\",\"columns\":[{\"name\":\"year\",\"label\":\"Year\",\"type\":\"number\"},{\"name\":\"budget_acad\",\"label\":\"Assigned budget on academic facilities (Lakhs)\",\"type\":\"number\"},{\"name\":\"exp_acad\",\"label\":\"Exp on academic facilities (Lakhs)\",\"type\":\"number\"},{\"name\":\"budget_phys\",\"label\":\"Assigned budget on physical facilities (Lakhs)\",\"type\":\"number\"},{\"name\":\"exp_phys\",\"label\":\"Exp on physical facilities (Lakhs)\",\"type\":\"number\"}]},{\"name\":\"file_4_4_1\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"4.4.2\",\"title\":\"4.4.2: Maintenance systems and procedures\",\"description\":\"There are established systems and procedures for maintaining and utilizing physical, academic and support facilities.\",\"fields\":[{\"name\":\"qlm_4_4_2_text\",\"label\":\"Description\",\"type\":\"textarea\"},{\"name\":\"qlm_4_4_2_url\",\"label\":\"URL for policy document\",\"type\":\"url\"},{\"name\":\"file_4_4_2\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]}]}]}','2026-10-07 17:24:46'),(13,5,'v4_university_with_tables',0,'{\"sections\":[{\"title\":\"5.1 Student Support\",\"metrics\":[{\"id\":\"5.1.1\",\"title\":\"5.1.1: Scholarships and Freeships\",\"description\":\"Average percentage of students benefited by scholarships and freeships provided by the institution, Government and non-government bodies.\",\"fields\":[{\"name\":\"qnm_5_1_1_benefited\",\"label\":\"Number of students benefited (Numerator)\",\"type\":\"number\"},{\"name\":\"qnm_5_1_1_total\",\"label\":\"Total number of students (Denominator)\",\"type\":\"number\"},{\"name\":\"table_5_1_1\",\"label\":\"Data Template: Scholarships\",\"type\":\"table\",\"columns\":[{\"name\":\"year\",\"label\":\"Year\",\"type\":\"number\"},{\"name\":\"scheme_name\",\"label\":\"Name of the scheme\",\"type\":\"text\"},{\"name\":\"num_benefited\",\"label\":\"Number of students benefiting\",\"type\":\"number\"}]},{\"name\":\"file_5_1_1\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"5.1.2\",\"title\":\"5.1.2: Career Counseling and Guidance\",\"description\":\"Average percentage of students benefited by career counseling and guidance for competitive examinations.\",\"fields\":[{\"name\":\"qnm_5_1_2_benefited\",\"label\":\"Number of students benefited (Numerator)\",\"type\":\"number\"},{\"name\":\"qnm_5_1_2_total\",\"label\":\"Total number of students (Denominator)\",\"type\":\"number\"},{\"name\":\"table_5_1_2\",\"label\":\"Data Template: Career Counseling\",\"type\":\"table\",\"columns\":[{\"name\":\"year\",\"label\":\"Year\",\"type\":\"number\"},{\"name\":\"scheme_name\",\"label\":\"Name of the scheme\",\"type\":\"text\"},{\"name\":\"num_passed\",\"label\":\"Students passed in competitive exams\",\"type\":\"number\"},{\"name\":\"num_benefited\",\"label\":\"Number of students benefited\",\"type\":\"number\"}]},{\"name\":\"file_5_1_2\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"5.1.3\",\"title\":\"5.1.3: Capacity Development and Skills Enhancement\",\"description\":\"Following Capacity development and skills enhancement initiatives are undertaken by the institution.\",\"fields\":[{\"name\":\"skills_opts\",\"label\":\"Initiatives undertaken (Check all that apply):\",\"type\":\"checkbox_group\",\"options\":[{\"name\":\"skill_soft\",\"label\":\"1. Soft skills\"},{\"name\":\"skill_lang\",\"label\":\"2. Language and communication skills\"},{\"name\":\"skill_life\",\"label\":\"3. Life skills (Yoga, physical fitness, health and hygiene)\"},{\"name\":\"skill_tech\",\"label\":\"4. Awareness of trends in technology\"}]},{\"name\":\"table_5_1_3\",\"label\":\"Data Template: Skill Enhancement\",\"type\":\"table\",\"columns\":[{\"name\":\"scheme_name\",\"label\":\"Name of the scheme\",\"type\":\"text\"},{\"name\":\"year_impl\",\"label\":\"Year of implementation\",\"type\":\"number\"},{\"name\":\"num_enrolled\",\"label\":\"Number of students enrolled\",\"type\":\"number\"},{\"name\":\"agencies\",\"label\":\"Name of agencies involved\",\"type\":\"text\"}]},{\"name\":\"file_5_1_3\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"5.1.4\",\"title\":\"5.1.4: Student Grievances Redressal\",\"description\":\"The Institution adopts the following for redressal of student grievances including sexual harassment and ragging cases.\",\"fields\":[{\"name\":\"grievance_opts\",\"label\":\"Options (Check all that apply):\",\"type\":\"checkbox_group\",\"options\":[{\"name\":\"gr_guide\",\"label\":\"1. Implementation of guidelines of statutory\\/regulatory bodies\"},{\"name\":\"gr_aware\",\"label\":\"2. Organisation-wide awareness and undertakings on policies\"},{\"name\":\"gr_mech\",\"label\":\"3. Mechanisms for submission of online\\/offline grievances\"},{\"name\":\"gr_timely\",\"label\":\"4. Timely redressal of grievances through appropriate committees\"}]},{\"name\":\"file_5_1_4\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]}]},{\"title\":\"5.2 Student Progression\",\"metrics\":[{\"id\":\"5.2.1\",\"title\":\"5.2.1: Qualifying in State\\/National\\/International Exams\",\"description\":\"Average percentage of students qualifying in state\\/ national\\/ international level examinations.\",\"fields\":[{\"name\":\"qnm_5_2_1_qual\",\"label\":\"Number of students qualifying (Numerator)\",\"type\":\"number\"},{\"name\":\"qnm_5_2_1_appear\",\"label\":\"Number of students appeared (Denominator)\",\"type\":\"number\"},{\"name\":\"table_5_2_1\",\"label\":\"Data Template: Competitive Exams\",\"type\":\"table\",\"columns\":[{\"name\":\"year\",\"label\":\"Year\",\"type\":\"number\"},{\"name\":\"exam_name\",\"label\":\"Exam (NET\\/SLET\\/GATE\\/etc)\",\"type\":\"text\"},{\"name\":\"num_selected\",\"label\":\"Number of students selected\",\"type\":\"number\"}]},{\"name\":\"file_5_2_1\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"5.2.2\",\"title\":\"5.2.2: Placement of Outgoing Students\",\"description\":\"Average percentage of placement of outgoing students during the last five years.\",\"fields\":[{\"name\":\"qnm_5_2_2_placed\",\"label\":\"Number of outgoing students placed (Numerator)\",\"type\":\"number\"},{\"name\":\"qnm_5_2_2_total\",\"label\":\"Number of outgoing students (Denominator)\",\"type\":\"number\"},{\"name\":\"table_5_2_2\",\"label\":\"Data Template: Placement\",\"type\":\"table\",\"columns\":[{\"name\":\"year\",\"label\":\"Year\",\"type\":\"number\"},{\"name\":\"employer_name\",\"label\":\"Name of the employer with contact\",\"type\":\"text\"},{\"name\":\"num_placed\",\"label\":\"Number of students placed\",\"type\":\"number\"}]},{\"name\":\"file_5_2_2\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"5.2.3\",\"title\":\"5.2.3: Progression to Higher Education\",\"description\":\"Percentage of recently-graduated students who have progressed to higher education.\",\"fields\":[{\"name\":\"qnm_5_2_3_prog\",\"label\":\"Number of students progressing to higher education (Numerator)\",\"type\":\"number\"},{\"name\":\"qnm_5_2_3_final\",\"label\":\"Total number of final year students (Denominator)\",\"type\":\"number\"},{\"name\":\"table_5_2_3\",\"label\":\"Data Template: Higher Education\",\"type\":\"table\",\"columns\":[{\"name\":\"year\",\"label\":\"Year\",\"type\":\"number\"},{\"name\":\"student_name\",\"label\":\"Name of student enrolling\",\"type\":\"text\"},{\"name\":\"prog_graduated\",\"label\":\"Program graduated from\",\"type\":\"text\"},{\"name\":\"inst_joined\",\"label\":\"Name of institution joined\",\"type\":\"text\"},{\"name\":\"prog_admitted\",\"label\":\"Name of program admitted to\",\"type\":\"text\"}]},{\"name\":\"file_5_2_3\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]}]},{\"title\":\"5.3 Student Participation and Activities\",\"metrics\":[{\"id\":\"5.3.1\",\"title\":\"5.3.1: Awards\\/Medals for Sports\\/Cultural Activities\",\"description\":\"Number of awards\\/medals won by students for outstanding performance in sports\\/cultural activities.\",\"fields\":[{\"name\":\"qnm_5_3_1_awards\",\"label\":\"Number of awards\\/medals\",\"type\":\"number\"},{\"name\":\"table_5_3_1\",\"label\":\"Data Template: Sports\\/Cultural Awards\",\"type\":\"table\",\"columns\":[{\"name\":\"year\",\"label\":\"Year\",\"type\":\"number\"},{\"name\":\"award_name\",\"label\":\"Name of the award\\/medal\",\"type\":\"text\"},{\"name\":\"level\",\"label\":\"Level (Inter-university\\/State\\/National\\/Intl)\",\"type\":\"text\"},{\"name\":\"event_name\",\"label\":\"Name of the event\",\"type\":\"text\"}]},{\"name\":\"file_5_3_1\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"5.3.2\",\"title\":\"5.3.2: Student Council\",\"description\":\"Presence of Student Council and its activities for institutional development and student welfare.\",\"fields\":[{\"name\":\"qlm_5_3_2_text\",\"label\":\"Description\",\"type\":\"textarea\"},{\"name\":\"file_5_3_2\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"5.3.3\",\"title\":\"5.3.3: Sports and Cultural Events\",\"description\":\"Average number of sports and cultural events \\/ competitions organised by the institution per year.\",\"fields\":[{\"name\":\"qnm_5_3_3_events\",\"label\":\"Number of events organized\",\"type\":\"number\"},{\"name\":\"table_5_3_3\",\"label\":\"Data Template: Events Organized\",\"type\":\"table\",\"columns\":[{\"name\":\"year\",\"label\":\"Year\",\"type\":\"number\"},{\"name\":\"event_name\",\"label\":\"Name of the event \\/ competition\",\"type\":\"text\"}]},{\"name\":\"file_5_3_3\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]}]},{\"title\":\"5.4 Alumni Engagement\",\"metrics\":[{\"id\":\"5.4.1\",\"title\":\"5.4.1: Alumni Contribution\",\"description\":\"The Alumni Association\\/Chapters (registered and functional) contributes significantly to the development of the institution.\",\"fields\":[{\"name\":\"qlm_5_4_1_text\",\"label\":\"Description\",\"type\":\"textarea\"},{\"name\":\"file_5_4_1\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]}]}]}','2026-10-07 17:30:00'),(14,5,'v5_university_with_tables',1,'{\"sections\":[{\"title\":\"5.1 Student Support\",\"metrics\":[{\"id\":\"5.1.1\",\"title\":\"5.1.1: Scholarships and Freeships\",\"description\":\"Average percentage of students benefited by scholarships and freeships provided by the institution, Government and non-government bodies.\",\"fields\":[{\"name\":\"qnm_5_1_1_benefited\",\"label\":\"Number of students benefited (Numerator)\",\"type\":\"number\"},{\"name\":\"qnm_5_1_1_total\",\"label\":\"Total number of students (Denominator)\",\"type\":\"number\"},{\"name\":\"table_5_1_1\",\"label\":\"Data Template: Scholarships\",\"type\":\"table\",\"columns\":[{\"name\":\"year\",\"label\":\"Year\",\"type\":\"number\"},{\"name\":\"scheme_name\",\"label\":\"Name of the scheme\",\"type\":\"text\"},{\"name\":\"num_benefited\",\"label\":\"Number of students benefiting\",\"type\":\"number\"}]},{\"name\":\"file_5_1_1\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"5.1.2\",\"title\":\"5.1.2: Career Counseling and Guidance\",\"description\":\"Average percentage of students benefited by career counseling and guidance for competitive examinations.\",\"fields\":[{\"name\":\"qnm_5_1_2_benefited\",\"label\":\"Number of students benefited (Numerator)\",\"type\":\"number\"},{\"name\":\"qnm_5_1_2_total\",\"label\":\"Total number of students (Denominator)\",\"type\":\"number\"},{\"name\":\"table_5_1_2\",\"label\":\"Data Template: Career Counseling\",\"type\":\"table\",\"columns\":[{\"name\":\"year\",\"label\":\"Year\",\"type\":\"number\"},{\"name\":\"scheme_name\",\"label\":\"Name of the scheme\",\"type\":\"text\"},{\"name\":\"num_passed\",\"label\":\"Students passed in competitive exams\",\"type\":\"number\"},{\"name\":\"num_benefited\",\"label\":\"Number of students benefited\",\"type\":\"number\"}]},{\"name\":\"file_5_1_2\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"5.1.3\",\"title\":\"5.1.3: Capacity Development and Skills Enhancement\",\"description\":\"Following Capacity development and skills enhancement initiatives are undertaken by the institution.\",\"fields\":[{\"name\":\"skills_opts\",\"label\":\"Initiatives undertaken (Check all that apply):\",\"type\":\"checkbox_group\",\"options\":[{\"name\":\"skill_soft\",\"label\":\"1. Soft skills\"},{\"name\":\"skill_lang\",\"label\":\"2. Language and communication skills\"},{\"name\":\"skill_life\",\"label\":\"3. Life skills (Yoga, physical fitness, health and hygiene)\"},{\"name\":\"skill_tech\",\"label\":\"4. Awareness of trends in technology\"}]},{\"name\":\"table_5_1_3\",\"label\":\"Data Template: Skill Enhancement\",\"type\":\"table\",\"columns\":[{\"name\":\"scheme_name\",\"label\":\"Name of the scheme\",\"type\":\"text\"},{\"name\":\"year_impl\",\"label\":\"Year of implementation\",\"type\":\"number\"},{\"name\":\"num_enrolled\",\"label\":\"Number of students enrolled\",\"type\":\"number\"},{\"name\":\"agencies\",\"label\":\"Name of agencies involved\",\"type\":\"text\"}]},{\"name\":\"file_5_1_3\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"5.1.4\",\"title\":\"5.1.4: Student Grievances Redressal\",\"description\":\"The Institution adopts the following for redressal of student grievances including sexual harassment and ragging cases.\",\"fields\":[{\"name\":\"grievance_opts\",\"label\":\"Options (Check all that apply):\",\"type\":\"checkbox_group\",\"options\":[{\"name\":\"gr_guide\",\"label\":\"1. Implementation of guidelines of statutory\\/regulatory bodies\"},{\"name\":\"gr_aware\",\"label\":\"2. Organisation-wide awareness and undertakings on policies\"},{\"name\":\"gr_mech\",\"label\":\"3. Mechanisms for submission of online\\/offline grievances\"},{\"name\":\"gr_timely\",\"label\":\"4. Timely redressal of grievances through appropriate committees\"}]},{\"name\":\"file_5_1_4\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]}]},{\"title\":\"5.2 Student Progression\",\"metrics\":[{\"id\":\"5.2.1\",\"title\":\"5.2.1: Qualifying in State\\/National\\/International Exams\",\"description\":\"Average percentage of students qualifying in state\\/ national\\/ international level examinations.\",\"fields\":[{\"name\":\"qnm_5_2_1_qual\",\"label\":\"Number of students qualifying (Numerator)\",\"type\":\"number\"},{\"name\":\"qnm_5_2_1_appear\",\"label\":\"Number of students appeared (Denominator)\",\"type\":\"number\"},{\"name\":\"table_5_2_1\",\"label\":\"Data Template: Competitive Exams\",\"type\":\"table\",\"columns\":[{\"name\":\"year\",\"label\":\"Year\",\"type\":\"number\"},{\"name\":\"exam_name\",\"label\":\"Exam (NET\\/SLET\\/GATE\\/etc)\",\"type\":\"text\"},{\"name\":\"num_selected\",\"label\":\"Number of students selected\",\"type\":\"number\"}]},{\"name\":\"file_5_2_1\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"5.2.2\",\"title\":\"5.2.2: Placement of Outgoing Students\",\"description\":\"Average percentage of placement of outgoing students during the last five years.\",\"fields\":[{\"name\":\"qnm_5_2_2_placed\",\"label\":\"Number of outgoing students placed (Numerator)\",\"type\":\"number\"},{\"name\":\"qnm_5_2_2_total\",\"label\":\"Number of outgoing students (Denominator)\",\"type\":\"number\"},{\"name\":\"table_5_2_2\",\"label\":\"Data Template: Placement\",\"type\":\"table\",\"columns\":[{\"name\":\"year\",\"label\":\"Year\",\"type\":\"number\"},{\"name\":\"employer_name\",\"label\":\"Name of the employer with contact\",\"type\":\"text\"},{\"name\":\"num_placed\",\"label\":\"Number of students placed\",\"type\":\"number\"}]},{\"name\":\"file_5_2_2\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"5.2.3\",\"title\":\"5.2.3: Progression to Higher Education\",\"description\":\"Percentage of recently-graduated students who have progressed to higher education.\",\"fields\":[{\"name\":\"qnm_5_2_3_prog\",\"label\":\"Number of students progressing to higher education (Numerator)\",\"type\":\"number\"},{\"name\":\"qnm_5_2_3_final\",\"label\":\"Total number of final year students (Denominator)\",\"type\":\"number\"},{\"name\":\"table_5_2_3\",\"label\":\"Data Template: Higher Education\",\"type\":\"table\",\"columns\":[{\"name\":\"year\",\"label\":\"Year\",\"type\":\"number\"},{\"name\":\"student_name\",\"label\":\"Name of student enrolling\",\"type\":\"text\"},{\"name\":\"prog_graduated\",\"label\":\"Program graduated from\",\"type\":\"text\"},{\"name\":\"inst_joined\",\"label\":\"Name of institution joined\",\"type\":\"text\"},{\"name\":\"prog_admitted\",\"label\":\"Name of program admitted to\",\"type\":\"text\"}]},{\"name\":\"file_5_2_3\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]}]},{\"title\":\"5.3 Student Participation and Activities\",\"metrics\":[{\"id\":\"5.3.1\",\"title\":\"5.3.1: Awards\\/Medals for Sports\\/Cultural Activities\",\"description\":\"Number of awards\\/medals won by students for outstanding performance in sports\\/cultural activities.\",\"fields\":[{\"name\":\"qnm_5_3_1_awards\",\"label\":\"Number of awards\\/medals\",\"type\":\"number\"},{\"name\":\"table_5_3_1\",\"label\":\"Data Template: Sports\\/Cultural Awards\",\"type\":\"table\",\"columns\":[{\"name\":\"year\",\"label\":\"Year\",\"type\":\"number\"},{\"name\":\"award_name\",\"label\":\"Name of the award\\/medal\",\"type\":\"text\"},{\"name\":\"level\",\"label\":\"Level (Inter-university\\/State\\/National\\/Intl)\",\"type\":\"text\"},{\"name\":\"event_name\",\"label\":\"Name of the event\",\"type\":\"text\"}]},{\"name\":\"file_5_3_1\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"5.3.2\",\"title\":\"5.3.2: Student Council\",\"description\":\"Presence of Student Council and its activities for institutional development and student welfare.\",\"fields\":[{\"name\":\"qlm_5_3_2_text\",\"label\":\"Description\",\"type\":\"textarea\"},{\"name\":\"file_5_3_2\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"5.3.3\",\"title\":\"5.3.3: Sports and Cultural Events\",\"description\":\"Average number of sports and cultural events \\/ competitions organised by the institution per year.\",\"fields\":[{\"name\":\"qnm_5_3_3_events\",\"label\":\"Number of events organized\",\"type\":\"number\"},{\"name\":\"table_5_3_3\",\"label\":\"Data Template: Events Organized\",\"type\":\"table\",\"columns\":[{\"name\":\"year\",\"label\":\"Year\",\"type\":\"number\"},{\"name\":\"event_name\",\"label\":\"Name of the event \\/ competition\",\"type\":\"text\"}]},{\"name\":\"file_5_3_3\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]}]},{\"title\":\"5.4 Alumni Engagement\",\"metrics\":[{\"id\":\"5.4.1\",\"title\":\"5.4.1: Alumni Contribution (QlM)\",\"description\":\"The Alumni Association\\/Chapters (registered and functional) contributes significantly to the development of the institution.\",\"fields\":[{\"name\":\"qlm_5_4_1_text\",\"label\":\"Description\",\"type\":\"textarea\"},{\"name\":\"file_5_4_1\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"5.4.2\",\"title\":\"5.4.2: Alumni contribution (INR in lakhs)\",\"description\":\"Alumni contribution during the last five years (INR in lakhs).\",\"fields\":[{\"name\":\"alumni_contrib_opts\",\"label\":\"Select contribution amount (Check one):\",\"type\":\"checkbox_group\",\"options\":[{\"name\":\"ac_100\",\"label\":\"A. \\u2265 100 Lakhs\"},{\"name\":\"ac_50\",\"label\":\"B. 50 Lakhs - 100 Lakhs\"},{\"name\":\"ac_20\",\"label\":\"C. 20 Lakhs - 50 Lakhs\"},{\"name\":\"ac_5\",\"label\":\"D. 5 Lakhs - 20 Lakhs\"},{\"name\":\"ac_less5\",\"label\":\"E. < 5 Lakhs\"}]},{\"name\":\"file_5_4_2\",\"label\":\"Supporting Document (Audited Statement)\",\"type\":\"file\",\"accept\":\".pdf\"}]}]}]}','2026-10-07 17:35:26'),(15,6,'v1_university_with_tables',1,'{\"sections\":[{\"title\":\"6.1 Institutional Vision and Leadership\",\"metrics\":[{\"id\":\"6.1.1\",\"title\":\"6.1.1: Vision and Mission\",\"description\":\"The institution has a clearly stated vision and mission which are reflected in its academic and administrative governance.\",\"fields\":[{\"name\":\"qlm_6_1_1_text\",\"label\":\"Description\",\"type\":\"textarea\"},{\"name\":\"file_6_1_1\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"6.1.2\",\"title\":\"6.1.2: Effective Leadership\",\"description\":\"Effective leadership is reflected in various institutional practices such as decentralization and participative management.\",\"fields\":[{\"name\":\"qlm_6_1_2_text\",\"label\":\"Description\",\"type\":\"textarea\"},{\"name\":\"file_6_1_2\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]}]},{\"title\":\"6.2 Strategy Development and Deployment\",\"metrics\":[{\"id\":\"6.2.1\",\"title\":\"6.2.1: Strategic Plan Deployment\",\"description\":\"The institutional Strategic plan is effectively deployed.\",\"fields\":[{\"name\":\"qlm_6_2_1_text\",\"label\":\"Description\",\"type\":\"textarea\"},{\"name\":\"file_6_2_1\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"6.2.2\",\"title\":\"6.2.2: Functioning of Institutional Bodies\",\"description\":\"Functioning of the institutional bodies is effective and efficient as visible from policies, administrative setup, appointment, service rules, and procedures, etc.\",\"fields\":[{\"name\":\"qlm_6_2_2_text\",\"label\":\"Description\",\"type\":\"textarea\"},{\"name\":\"file_6_2_2\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"6.2.3\",\"title\":\"6.2.3: Implementation of e-governance\",\"description\":\"Implementation of e-governance in areas of operation.\",\"fields\":[{\"name\":\"egov_opts\",\"label\":\"Areas of e-governance (Check all that apply):\",\"type\":\"checkbox_group\",\"options\":[{\"name\":\"egov_admin\",\"label\":\"1. Administration\"},{\"name\":\"egov_finance\",\"label\":\"2. Finance and Accounts\"},{\"name\":\"egov_student\",\"label\":\"3. Student Admission and Support\"},{\"name\":\"egov_exam\",\"label\":\"4. Examination\"}]},{\"name\":\"table_6_2_3\",\"label\":\"Data Template: e-Governance\",\"type\":\"table\",\"columns\":[{\"name\":\"area\",\"label\":\"Areas of e-governance\",\"type\":\"text\"},{\"name\":\"vendor\",\"label\":\"Name of the Vendor with contact details\",\"type\":\"text\"},{\"name\":\"year_impl\",\"label\":\"Year of implementation\",\"type\":\"number\"}]},{\"name\":\"file_6_2_3\",\"label\":\"Supporting Document (ERP Document\\/Screenshots)\",\"type\":\"file\",\"accept\":\".pdf\"}]}]},{\"title\":\"6.3 Faculty Empowerment Strategies\",\"metrics\":[{\"id\":\"6.3.1\",\"title\":\"6.3.1: Welfare Measures\",\"description\":\"The institution has effective welfare measures for teaching and non-teaching staff.\",\"fields\":[{\"name\":\"qlm_6_3_1_text\",\"label\":\"Description\",\"type\":\"textarea\"},{\"name\":\"file_6_3_1\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"6.3.2\",\"title\":\"6.3.2: Financial Support for Conferences\",\"description\":\"Average percentage of teachers provided with financial support to attend conferences\\/workshops and towards membership fee of professional bodies.\",\"fields\":[{\"name\":\"qnm_6_3_2_supported\",\"label\":\"Number of teachers provided with financial support (Numerator)\",\"type\":\"number\"},{\"name\":\"qnm_6_3_2_total\",\"label\":\"Total number of teachers (Denominator)\",\"type\":\"number\"},{\"name\":\"table_6_3_2\",\"label\":\"Data Template: Financial Support\",\"type\":\"table\",\"columns\":[{\"name\":\"year\",\"label\":\"Year\",\"type\":\"number\"},{\"name\":\"teacher_name\",\"label\":\"Name of teacher\",\"type\":\"text\"},{\"name\":\"conf_name\",\"label\":\"Name of conference\\/workshop\",\"type\":\"text\"},{\"name\":\"amount\",\"label\":\"Amount of support (INR)\",\"type\":\"number\"}]},{\"name\":\"file_6_3_2\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"6.3.3\",\"title\":\"6.3.3: Professional Development Programs\",\"description\":\"Average number of professional development \\/ administrative training programs organized by the institution for teaching and non-teaching staff.\",\"fields\":[{\"name\":\"qnm_6_3_3_progs\",\"label\":\"Total number of programs organized\",\"type\":\"number\"},{\"name\":\"table_6_3_3\",\"label\":\"Data Template: Development Programs\",\"type\":\"table\",\"columns\":[{\"name\":\"year\",\"label\":\"Year\",\"type\":\"number\"},{\"name\":\"prog_title\",\"label\":\"Title of the professional development program\",\"type\":\"text\"},{\"name\":\"participants\",\"label\":\"No. of participants (Teaching\\/Non-Teaching)\",\"type\":\"number\"}]},{\"name\":\"file_6_3_3\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"6.3.4\",\"title\":\"6.3.4: Faculty Development Programmes (FDP)\",\"description\":\"Average percentage of teachers undergoing online\\/face-to-face Faculty Development Programmes (FDP).\",\"fields\":[{\"name\":\"qnm_6_3_4_fdp\",\"label\":\"Number of teachers undergoing FDP (Numerator)\",\"type\":\"number\"},{\"name\":\"qnm_6_3_4_total\",\"label\":\"Total number of teachers (Denominator)\",\"type\":\"number\"},{\"name\":\"table_6_3_4\",\"label\":\"Data Template: FDP Details\",\"type\":\"table\",\"columns\":[{\"name\":\"year\",\"label\":\"Year\",\"type\":\"number\"},{\"name\":\"teacher_name\",\"label\":\"Name of teacher who attended\",\"type\":\"text\"},{\"name\":\"prog_title\",\"label\":\"Title of the program\",\"type\":\"text\"},{\"name\":\"duration\",\"label\":\"Duration (from - to)\",\"type\":\"text\"}]},{\"name\":\"file_6_3_4\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]}]},{\"title\":\"6.4 Financial Management and Resource Mobilization\",\"metrics\":[{\"id\":\"6.4.1\",\"title\":\"6.4.1: Internal and External Audits\",\"description\":\"Institution conducts internal and external financial audits regularly.\",\"fields\":[{\"name\":\"qlm_6_4_1_text\",\"label\":\"Description\",\"type\":\"textarea\"},{\"name\":\"file_6_4_1\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"6.4.2\",\"title\":\"6.4.2: Funds \\/ Grants from Government Bodies\",\"description\":\"Funds \\/ Grants received from government bodies during the last five years for development and maintenance of infrastructure.\",\"fields\":[{\"name\":\"qnm_6_4_2_grants\",\"label\":\"Total Grants received from government bodies (INR in Lakhs)\",\"type\":\"number\"},{\"name\":\"table_6_4_2\",\"label\":\"Data Template: Govt Grants\",\"type\":\"table\",\"columns\":[{\"name\":\"year\",\"label\":\"Year\",\"type\":\"number\"},{\"name\":\"grant_name\",\"label\":\"Name of the government funding agencies\",\"type\":\"text\"},{\"name\":\"funds\",\"label\":\"Funds\\/ Grants received (INR in Lakhs)\",\"type\":\"number\"}]},{\"name\":\"file_6_4_2\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"6.4.3\",\"title\":\"6.4.3: Funds \\/ Grants from Non-Government Bodies\",\"description\":\"Funds \\/ Grants received from non-government bodies, individuals, philanthropists during the last five years.\",\"fields\":[{\"name\":\"qnm_6_4_3_grants\",\"label\":\"Total Grants received from non-government bodies (INR in Lakhs)\",\"type\":\"number\"},{\"name\":\"table_6_4_3\",\"label\":\"Data Template: Non-Govt Grants\",\"type\":\"table\",\"columns\":[{\"name\":\"year\",\"label\":\"Year\",\"type\":\"number\"},{\"name\":\"grant_name\",\"label\":\"Name of the non-government funding agencies\\/individuals\",\"type\":\"text\"},{\"name\":\"funds\",\"label\":\"Funds\\/ Grants received (INR in Lakhs)\",\"type\":\"number\"}]},{\"name\":\"file_6_4_3\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"6.4.4\",\"title\":\"6.4.4: Institutional strategies for mobilisation of funds\",\"description\":\"Institutional strategies for mobilisation of funds and the optimal utilisation of resources.\",\"fields\":[{\"name\":\"qlm_6_4_4_text\",\"label\":\"Description\",\"type\":\"textarea\"},{\"name\":\"file_6_4_4\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]}]},{\"title\":\"6.5 Internal Quality Assurance System\",\"metrics\":[{\"id\":\"6.5.1\",\"title\":\"6.5.1: IQAC Contribution\",\"description\":\"Internal Quality Assurance Cell (IQAC) has contributed significantly for institutionalizing the quality assurance strategies and processes.\",\"fields\":[{\"name\":\"qlm_6_5_1_text\",\"label\":\"Description\",\"type\":\"textarea\"},{\"name\":\"file_6_5_1\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"6.5.2\",\"title\":\"6.5.2: Quality Assurance Initiatives\",\"description\":\"Quality assurance initiatives of the institution.\",\"fields\":[{\"name\":\"qa_opts\",\"label\":\"Initiatives undertaken (Check all that apply):\",\"type\":\"checkbox_group\",\"options\":[{\"name\":\"qa_meeting\",\"label\":\"1. Regular meeting of Internal Quality Assurance Cell (IQAC)\"},{\"name\":\"qa_feedback\",\"label\":\"2. Feedback collected, analyzed and used for improvements\"},{\"name\":\"qa_collab\",\"label\":\"3. Collaborative quality initiatives with other institution(s)\"},{\"name\":\"qa_audit\",\"label\":\"4. Academic Administrative Audit (AAA) and follow-up action\"},{\"name\":\"qa_cert\",\"label\":\"5. ISO \\/ NBA certification or other quality audits\"}]},{\"name\":\"table_6_5_2\",\"label\":\"Data Template: Quality Initiatives\",\"type\":\"table\",\"columns\":[{\"name\":\"year\",\"label\":\"Year\",\"type\":\"number\"},{\"name\":\"initiative_name\",\"label\":\"Name of quality initiative by IQAC\",\"type\":\"text\"},{\"name\":\"duration\",\"label\":\"Duration (from - to)\",\"type\":\"text\"},{\"name\":\"participants\",\"label\":\"Number of participants\",\"type\":\"number\"}]},{\"name\":\"file_6_5_2\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"6.5.3\",\"title\":\"6.5.3: Incremental Improvements\",\"description\":\"Incremental improvements made for the preceding five years with regard to quality.\",\"fields\":[{\"name\":\"qlm_6_5_3_text\",\"label\":\"Description\",\"type\":\"textarea\"},{\"name\":\"file_6_5_3\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]}]}]}','2026-10-08 03:54:52'),(16,7,'v1_university_standard',1,'{\"sections\":[{\"title\":\"7.1 Institutional Values and Social Responsibilities\",\"metrics\":[{\"id\":\"7.1.1\",\"title\":\"7.1.1: Promotion of Gender Equity\",\"description\":\"Measures initiated by the Institution for the promotion of gender equity during the last five years.\",\"fields\":[{\"name\":\"qlm_7_1_1_text\",\"label\":\"Description\",\"type\":\"textarea\"},{\"name\":\"file_7_1_1\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"7.1.2\",\"title\":\"7.1.2: Alternate Sources of Energy\",\"description\":\"The Institution has facilities for alternate sources of energy and energy conservation measures.\",\"fields\":[{\"name\":\"energy_opts\",\"label\":\"Facilities available (Check all that apply):\",\"type\":\"checkbox_group\",\"options\":[{\"name\":\"en_solar\",\"label\":\"1. Solar energy\"},{\"name\":\"en_biogas\",\"label\":\"2. Biogas plant\"},{\"name\":\"en_grid\",\"label\":\"3. Wheeling to the Grid\"},{\"name\":\"en_sensor\",\"label\":\"4. Sensor-based energy conservation\"},{\"name\":\"en_led\",\"label\":\"5. Use of LED bulbs\\/ power efficient equipment\"}]},{\"name\":\"file_7_1_2\",\"label\":\"Supporting Document (Geotagged Photographs)\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"7.1.3\",\"title\":\"7.1.3: Waste Management Facilities\",\"description\":\"Describe the facilities in the Institution for the management of the degradable and non-degradable waste.\",\"fields\":[{\"name\":\"qlm_7_1_3_text\",\"label\":\"Description\",\"type\":\"textarea\"},{\"name\":\"file_7_1_3\",\"label\":\"Supporting Document (Geotagged Photographs)\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"7.1.4\",\"title\":\"7.1.4: Water Conservation Facilities\",\"description\":\"Water conservation facilities available in the Institution.\",\"fields\":[{\"name\":\"water_opts\",\"label\":\"Facilities available (Check all that apply):\",\"type\":\"checkbox_group\",\"options\":[{\"name\":\"wa_rain\",\"label\":\"1. Rain water harvesting\"},{\"name\":\"wa_bore\",\"label\":\"2. Borewell \\/Open well recharge\"},{\"name\":\"wa_tank\",\"label\":\"3. Construction of tanks and bunds\"},{\"name\":\"wa_waste\",\"label\":\"4. Waste water recycling\"},{\"name\":\"wa_maint\",\"label\":\"5. Maintenance of water bodies and distribution system in the campus\"}]},{\"name\":\"file_7_1_4\",\"label\":\"Supporting Document (Geotagged Photographs)\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"7.1.5\",\"title\":\"7.1.5: Green Campus Initiatives\",\"description\":\"Green campus initiatives include restricted entry of automobiles, use of bicycles\\/battery powered vehicles, pedestrian friendly pathways, ban on use of plastic, landscaping.\",\"fields\":[{\"name\":\"green_opts\",\"label\":\"Initiatives available (Check all that apply):\",\"type\":\"checkbox_group\",\"options\":[{\"name\":\"gc_auto\",\"label\":\"1. Restricted entry of automobiles\"},{\"name\":\"gc_battery\",\"label\":\"2. Use of Bicycles\\/ Battery powered vehicles\"},{\"name\":\"gc_path\",\"label\":\"3. Pedestrian Friendly pathways\"},{\"name\":\"gc_plastic\",\"label\":\"4. Ban on use of Plastic\"},{\"name\":\"gc_landscape\",\"label\":\"5. landscaping with trees and plants\"}]},{\"name\":\"file_7_1_5\",\"label\":\"Supporting Document (Geotagged Photographs\\/Circulars)\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"7.1.6\",\"title\":\"7.1.6: Environment and Energy Audits\",\"description\":\"Quality audits on environment and energy are regularly undertaken by the institution.\",\"fields\":[{\"name\":\"audit_opts\",\"label\":\"Audits undertaken (Check all that apply):\",\"type\":\"checkbox_group\",\"options\":[{\"name\":\"au_green\",\"label\":\"1. Green audit\"},{\"name\":\"au_energy\",\"label\":\"2. Energy audit\"},{\"name\":\"au_env\",\"label\":\"3. Environment audit\"},{\"name\":\"au_clean\",\"label\":\"4. Clean and green campus recognitions\\/awards\"},{\"name\":\"au_beyond\",\"label\":\"5. Beyond the campus environmental promotional activities\"}]},{\"name\":\"file_7_1_6\",\"label\":\"Supporting Document (Audit reports\\/Certificates)\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"7.1.7\",\"title\":\"7.1.7: Disabled-friendly Environment\",\"description\":\"The Institution has disabled-friendly, barrier free environment.\",\"fields\":[{\"name\":\"disable_opts\",\"label\":\"Facilities available (Check all that apply):\",\"type\":\"checkbox_group\",\"options\":[{\"name\":\"df_built\",\"label\":\"1. Built environment with ramps\\/lifts for easy access to classrooms\"},{\"name\":\"df_wash\",\"label\":\"2. Disabled-friendly washrooms\"},{\"name\":\"df_sign\",\"label\":\"3. Signage including tactile path, lights, display boards and signposts\"},{\"name\":\"df_assist\",\"label\":\"4. Assistive technology and facilities for Divyangjan accessible website, screen-reading software, mechanized equipment\"},{\"name\":\"df_enquiry\",\"label\":\"5. Provision for enquiry and information: Human assistance, reader, scribe, soft copies of reading material, screen reading\"}]},{\"name\":\"file_7_1_7\",\"label\":\"Supporting Document (Geotagged Photographs)\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"7.1.8\",\"title\":\"7.1.8: Inclusive Environment\",\"description\":\"Describe the Institutional efforts\\/initiatives in providing an inclusive environment i.e., tolerance and harmony towards cultural, regional, linguistic, communal socioeconomic and other diversities.\",\"fields\":[{\"name\":\"qlm_7_1_8_text\",\"label\":\"Description\",\"type\":\"textarea\"},{\"name\":\"file_7_1_8\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"7.1.9\",\"title\":\"7.1.9: Constitutional Obligations\",\"description\":\"Sensitization of students and employees of the Institution to the constitutional obligations: values, rights, duties and responsibilities of citizens.\",\"fields\":[{\"name\":\"qlm_7_1_9_text\",\"label\":\"Description\",\"type\":\"textarea\"},{\"name\":\"file_7_1_9\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"7.1.10\",\"title\":\"7.1.10: Code of Conduct\",\"description\":\"The Institution has a prescribed code of conduct for students, teachers, administrators and other staff and conducts periodic programmes in this regard.\",\"fields\":[{\"name\":\"conduct_opts\",\"label\":\"Initiatives (Check all that apply):\",\"type\":\"checkbox_group\",\"options\":[{\"name\":\"cc_display\",\"label\":\"1. The Code of Conduct is displayed on the website\"},{\"name\":\"cc_comm\",\"label\":\"2. There is a committee to monitor adherence to the Code of Conduct\"},{\"name\":\"cc_prog\",\"label\":\"3. Institution organizes professional ethics programmes for students, teachers, administrators and other staff\"},{\"name\":\"cc_aware\",\"label\":\"4. Annual awareness programmes on Code of Conduct are organized\"}]},{\"name\":\"file_7_1_10\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"7.1.11\",\"title\":\"7.1.11: Commemorative Days and Events\",\"description\":\"Institution celebrates \\/ organizes national and international commemorative days, events and festivals.\",\"fields\":[{\"name\":\"qlm_7_1_11_text\",\"label\":\"Description\",\"type\":\"textarea\"},{\"name\":\"file_7_1_11\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]}]},{\"title\":\"7.2 Best Practices\",\"metrics\":[{\"id\":\"7.2.1\",\"title\":\"7.2.1: Institutional Best Practices\",\"description\":\"Describe two best practices successfully implemented by the Institution as per NAAC format provided in the Manual.\",\"fields\":[{\"name\":\"qlm_7_2_1_text\",\"label\":\"Description of Best Practices\",\"type\":\"textarea\"},{\"name\":\"file_7_2_1\",\"label\":\"Supporting Document \\/ URL\",\"type\":\"file\",\"accept\":\".pdf\"}]}]},{\"title\":\"7.3 Institutional Distinctiveness\",\"metrics\":[{\"id\":\"7.3.1\",\"title\":\"7.3.1: Institutional Distinctiveness\",\"description\":\"Portray the performance of the Institution in one area distinctive to its priority and thrust within 1000 words.\",\"fields\":[{\"name\":\"qlm_7_3_1_text\",\"label\":\"Description\",\"type\":\"textarea\"},{\"name\":\"file_7_3_1\",\"label\":\"Supporting Document \\/ URL\",\"type\":\"file\",\"accept\":\".pdf\"}]}]}]}','2026-10-08 04:05:02');
/*!40000 ALTER TABLE `naac_form_schemas` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-10-26  0:22:50



-- ============================================================================
-- DEFAULT PRODUCTION USER ACCOUNTS & ROLE ASSIGNMENTS
-- Default Password: Admin@123 (bcrypt-hashed)
-- ============================================================================

INSERT INTO `users` (`user_id`, `full_name`, `email`, `password`, `status`, `created_at`) VALUES
(1, 'System Administrator', 'admin@gmrit.edu.in', '$2y$10$PwoOS7qwkTpUBFoIx//nx.Ost.I/SrLdLUXjtqn8IeXJDrhbvKMwu', 'active', NOW()),
(2, 'IQAC Coordinator', 'iqac@gmrit.edu.in', '$2y$10$PwoOS7qwkTpUBFoIx//nx.Ost.I/SrLdLUXjtqn8IeXJDrhbvKMwu', 'active', NOW()),
(3, 'Dr. HOD CSE', 'hod.cse@gmrit.edu.in', '$2y$10$PwoOS7qwkTpUBFoIx//nx.Ost.I/SrLdLUXjtqn8IeXJDrhbvKMwu', 'active', NOW()),
(4, 'Faculty Member', 'faculty.cse@gmrit.edu.in', '$2y$10$PwoOS7qwkTpUBFoIx//nx.Ost.I/SrLdLUXjtqn8IeXJDrhbvKMwu', 'active', NOW()),
(5, 'CSE Dept Coordinator', 'coord.cse@gmrit.edu.in', '$2y$10$PwoOS7qwkTpUBFoIx//nx.Ost.I/SrLdLUXjtqn8IeXJDrhbvKMwu', 'active', NOW()),
(6, 'Central Coordinator', 'central.coord@gmrit.edu.in', '$2y$10$PwoOS7qwkTpUBFoIx//nx.Ost.I/SrLdLUXjtqn8IeXJDrhbvKMwu', 'active', NOW()),
(7, 'Junior Assistant CSE', 'ja.cse@gmrit.edu.in', '$2y$10$PwoOS7qwkTpUBFoIx//nx.Ost.I/SrLdLUXjtqn8IeXJDrhbvKMwu', 'active', NOW()),
(8, 'Dean R&D', 'rnd.dean@gmrit.edu.in', '$2y$10$PwoOS7qwkTpUBFoIx//nx.Ost.I/SrLdLUXjtqn8IeXJDrhbvKMwu', 'active', NOW()),
(9, 'Student Sample', 'student.cse@gmrit.edu.in', '$2y$10$PwoOS7qwkTpUBFoIx//nx.Ost.I/SrLdLUXjtqn8IeXJDrhbvKMwu', 'active', NOW());

INSERT INTO `user_roles` (`user_role_id`, `user_id`, `role_id`, `dept_id`) VALUES
(1, 1, 1, NULL), -- Admin (Global Institutional Scope)
(2, 2, 2, NULL), -- IQAC Coordinator (Global Accreditation Scope)
(3, 3, 3, 1),    -- Head of Department (CSE)
(4, 4, 4, 1),    -- Faculty Member (CSE)
(5, 5, 5, 1),    -- Department Coordinator (CSE)
(6, 6, 6, NULL), -- Central Coordinator (Global Events Scope)
(7, 7, 7, 1),    -- Junior Assistant (CSE)
(8, 8, 8, NULL), -- Dean of R&D (Global Research Scope)
(9, 9, 9, 1);    -- Student (CSE)

INSERT INTO `user_profiles` (`profile_id`, `user_id`, `per_no`, `pan_no`, `apaar_id`, `highest_degree`, `university`, `specialization`, `doj_institution`, `doj_department`, `experience_years`, `designation_joining`, `designation_present`, `association_nature`, `is_currently_associated`) VALUES
(1, 1, 'ADM001', 'ABCDE1234F', 'APAAR-001', 'Ph.D', 'JNTUK', 'Computer Science', '2015-06-01', '2015-06-01', 12.0, 'System Administrator', 'System Administrator', 'Regular', 1),
(2, 2, 'IQC001', 'ABCDE1234G', 'APAAR-002', 'Ph.D', 'Andhra University', 'Quality Assurance', '2016-07-01', '2016-07-01', 10.0, 'Professor', 'IQAC Coordinator', 'Regular', 1),
(3, 3, 'HOD001', 'ABCDE1234H', 'APAAR-003', 'Ph.D', 'IIT Madras', 'Computer Science', '2014-05-10', '2014-05-10', 15.0, 'Associate Professor', 'Professor & HOD', 'Regular', 1),
(4, 4, 'FAC001', 'ABCDE1234I', 'APAAR-004', 'M.Tech', 'JNTUK', 'Software Engineering', '2019-08-01', '2019-08-01', 6.0, 'Assistant Professor', 'Assistant Professor', 'Regular', 1);



SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================================
-- VERIFICATION QUERIES (Optional Post-Install Verification):
--   SELECT COUNT(*) AS total_roles FROM roles;           -- Expected: 9
--   SELECT COUNT(*) AS total_depts FROM departments;     -- Expected: >= 14
--   SELECT COUNT(*) AS total_types FROM document_types;  -- Expected: 21
--   SELECT COUNT(*) AS total_users FROM users;           -- Expected: 9
--   SELECT COUNT(*) AS total_docs FROM documents;        -- Expected: 0 (Clean)
-- ============================================================================