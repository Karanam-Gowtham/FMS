-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: master
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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `a_c_files`
--

LOCK TABLES `a_c_files` WRITE;
/*!40000 ALTER TABLE `a_c_files` DISABLE KEYS */;
/*!40000 ALTER TABLE `a_c_files` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `a_cri_files`
--

LOCK TABLES `a_cri_files` WRITE;
/*!40000 ALTER TABLE `a_cri_files` DISABLE KEYS */;
/*!40000 ALTER TABLE `a_cri_files` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `a_files`
--

LOCK TABLES `a_files` WRITE;
/*!40000 ALTER TABLE `a_files` DISABLE KEYS */;
/*!40000 ALTER TABLE `a_files` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `academic_year`
--

LOCK TABLES `academic_year` WRITE;
/*!40000 ALTER TABLE `academic_year` DISABLE KEYS */;
INSERT INTO `academic_year` VALUES ('2020-21'),('2021-22'),('2022-23'),('2023-24'),('2024-25');
/*!40000 ALTER TABLE `academic_year` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `academic_years`
--

DROP TABLE IF EXISTS `academic_years`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `academic_years` (
  `academic_year_id` int(11) NOT NULL AUTO_INCREMENT,
  `year_name` varchar(20) NOT NULL,
  `is_active` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`academic_year_id`),
  UNIQUE KEY `year_name` (`year_name`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `academic_years`
--

LOCK TABLES `academic_years` WRITE;
/*!40000 ALTER TABLE `academic_years` DISABLE KEYS */;
INSERT INTO `academic_years` VALUES (1,'2022-2023',0),(2,'2023-2024',0),(3,'2024-2025',0),(4,'2025-2026',1);
/*!40000 ALTER TABLE `academic_years` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `admin_login`
--

LOCK TABLES `admin_login` WRITE;
/*!40000 ALTER TABLE `admin_login` DISABLE KEYS */;
INSERT INTO `admin_login` VALUES ('chandu','123',NULL,1);
/*!40000 ALTER TABLE `admin_login` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `admin_reg`
--

LOCK TABLES `admin_reg` WRITE;
/*!40000 ALTER TABLE `admin_reg` DISABLE KEYS */;
INSERT INTO `admin_reg` VALUES ('chandu','123',NULL,1);
/*!40000 ALTER TABLE `admin_reg` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `approval_flow`
--

DROP TABLE IF EXISTS `approval_flow`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `approval_flow` (
  `flow_id` int(11) NOT NULL AUTO_INCREMENT,
  `type_id` int(11) NOT NULL,
  `current_role_id` int(11) NOT NULL,
  `next_role_id` int(11) NOT NULL,
  `sequence_no` int(11) NOT NULL,
  PRIMARY KEY (`flow_id`),
  UNIQUE KEY `type_id` (`type_id`,`sequence_no`),
  KEY `current_role_id` (`current_role_id`),
  KEY `next_role_id` (`next_role_id`),
  CONSTRAINT `approval_flow_ibfk_1` FOREIGN KEY (`type_id`) REFERENCES `document_types` (`type_id`),
  CONSTRAINT `approval_flow_ibfk_2` FOREIGN KEY (`current_role_id`) REFERENCES `roles` (`role_id`),
  CONSTRAINT `approval_flow_ibfk_3` FOREIGN KEY (`next_role_id`) REFERENCES `roles` (`role_id`)
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `approval_flow`
--

LOCK TABLES `approval_flow` WRITE;
/*!40000 ALTER TABLE `approval_flow` DISABLE KEYS */;
INSERT INTO `approval_flow` VALUES (1,1,4,3,1),(2,1,3,2,2),(3,2,4,3,1),(4,2,3,2,2),(5,3,4,3,1),(6,3,3,2,2),(7,4,4,3,1),(8,4,3,2,2),(9,5,4,3,1),(10,5,3,2,2),(11,6,4,3,1),(12,7,4,3,1),(13,8,4,3,1),(14,9,4,3,1),(15,10,4,3,1),(16,11,4,3,1),(17,12,7,3,1),(18,13,7,3,1),(19,14,7,3,1),(20,15,6,2,1),(21,15,2,1,2),(22,16,6,2,1),(23,16,2,1,2),(24,17,6,2,1),(25,17,2,1,2),(26,18,6,2,1),(27,18,2,1,2),(28,19,6,2,1),(29,19,2,1,2),(30,20,6,2,1),(31,20,2,1,2),(32,21,6,2,1),(33,21,2,1,2),(34,22,6,2,1),(35,22,2,1,2),(36,23,6,2,1),(37,23,2,1,2),(38,24,6,2,1),(39,24,2,1,2);
/*!40000 ALTER TABLE `approval_flow` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `approval_roles`
--

LOCK TABLES `approval_roles` WRITE;
/*!40000 ALTER TABLE `approval_roles` DISABLE KEYS */;
INSERT INTO `approval_roles` VALUES (1,'FACULTY',1),(2,'DEPT_COORD',2),(3,'HOD',3),(4,'CENTRAL_COORD',4);
/*!40000 ALTER TABLE `approval_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `archive_documents`
--

DROP TABLE IF EXISTS `archive_documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `archive_documents` (
  `document_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`document_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `archive_documents`
--

LOCK TABLES `archive_documents` WRITE;
/*!40000 ALTER TABLE `archive_documents` DISABLE KEYS */;
/*!40000 ALTER TABLE `archive_documents` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `central_files`
--

LOCK TABLES `central_files` WRITE;
/*!40000 ALTER TABLE `central_files` DISABLE KEYS */;
/*!40000 ALTER TABLE `central_files` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `conf_org_tab`
--

DROP TABLE IF EXISTS `conf_org_tab`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `conf_org_tab` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) DEFAULT NULL,
  `branch` varchar(100) DEFAULT NULL,
  `title` varchar(500) DEFAULT NULL,
  `mode` varchar(50) DEFAULT NULL,
  `date_from` date DEFAULT NULL,
  `date_to` date DEFAULT NULL,
  `organised_by` varchar(500) DEFAULT NULL,
  `location` varchar(500) DEFAULT NULL,
  `year` varchar(50) DEFAULT NULL,
  `brochure` varchar(500) DEFAULT NULL,
  `fdp_schedule_invitation` varchar(500) DEFAULT NULL,
  `attendance_forms` varchar(500) DEFAULT NULL,
  `feedback_forms` varchar(500) DEFAULT NULL,
  `fdp_report` varchar(500) DEFAULT NULL,
  `photos_pdf` varchar(500) DEFAULT NULL,
  `merged_file` varchar(500) DEFAULT NULL,
  `submission_time` datetime DEFAULT NULL,
  `status` varchar(100) DEFAULT 'Pending HOD',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `conf_org_tab`
--

LOCK TABLES `conf_org_tab` WRITE;
/*!40000 ALTER TABLE `conf_org_tab` DISABLE KEYS */;
/*!40000 ALTER TABLE `conf_org_tab` ENABLE KEYS */;
UNLOCK TABLES;

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
  `authors` text DEFAULT NULL,
  `paper_title` varchar(300) NOT NULL,
  `conference_name` varchar(500) DEFAULT NULL,
  `published_journal_name` varchar(500) DEFAULT NULL,
  `issn_no` varchar(100) DEFAULT NULL,
  `volume_no` varchar(50) DEFAULT NULL,
  `issue_no` varchar(50) DEFAULT NULL,
  `page_no` varchar(50) DEFAULT NULL,
  `doi` varchar(255) DEFAULT NULL,
  `indexing` varchar(50) DEFAULT NULL,
  `publication_link` varchar(500) DEFAULT NULL,
  `from_date` varchar(200) NOT NULL,
  `to_date` varchar(200) NOT NULL,
  `organised_by` varchar(200) NOT NULL,
  `location` varchar(200) NOT NULL,
  `certificate_path` varchar(400) NOT NULL,
  `paper_type` varchar(200) NOT NULL,
  `paper_file_path` varchar(300) NOT NULL,
  `submission_time` varchar(300) NOT NULL,
  `year` varchar(255) NOT NULL,
  `status` varchar(50) DEFAULT 'Pending HOD',
  `rejection_reason` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `conference_tab`
--

LOCK TABLES `conference_tab` WRITE;
/*!40000 ALTER TABLE `conference_tab` DISABLE KEYS */;
/*!40000 ALTER TABLE `conference_tab` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `contact_form`
--

LOCK TABLES `contact_form` WRITE;
/*!40000 ALTER TABLE `contact_form` DISABLE KEYS */;
/*!40000 ALTER TABLE `contact_form` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `criteria`
--

LOCK TABLES `criteria` WRITE;
/*!40000 ALTER TABLE `criteria` DISABLE KEYS */;
INSERT INTO `criteria` VALUES (1,'1.1.1','Copies of PEOs, POs & PSOs for all programs','2022-23'),(1,'1.1.2','Number of Programmes where syllabus revision was carried out during the year(Data Requirement: Programme Code ,Names of the Programmes revised)','2022-23'),(1,'1.1.2(A)','List of programs where syllabus revision has been carried out signed by the Principal','2022-23'),(1,'1.1.2(B)','Approved Minutes of relevant Academic Council/BOS meetings highlighting the specific agenda item relevant to the metric year wise','2022-23'),(1,'1.1.3','Number of courses focusing on employability/entrepreneurship/ skill development  offered by the Institution during the year(Data Requirement: Name of the Course with Course Code ,Name of the Programme ,Activities which have a direct bearing on employability/ entrepreneurship/ skill  development)','2022-23'),(1,'1.1.3(A)','Syllabus copy of the courses highlighting Focus on employability/entrepreneurship/ skill development along with their course outcomes.','2022-23'),(1,'1.1.3(B)','Minutes of the Boards of Studies/ Academic Council meetings with approval for these courses.','2022-23'),(1,'1.1.3(C)','List of MoUs','2022-23'),(1,'1.2.1','Number of new courses introduced across all programmes offered during the year(Data Requirement: Name of the newly introduced course (s)  ,Name of the Programme)','2022-23'),(1,'1.2.1(A)','List of new courses introduced program-wise during the assessment period certified by the Principal.','2022-23'),(1,'1.2.1(B)','Minutes of relevant Academic Council/BOS meetings highlighting the name of the new courses introduced','2022-23'),(1,'1.2.2','Number of Programmes offered through Choice Based Credit System  (CBCS)/Elective Course System(Data Requirement: ,Names of all Programmes offered through CBCS ,Names of all Programmes offered through Elective Course System)','2022-23'),(1,'1.2.2(A)','List of programs in which CBCS/Elective course system implemented in the last completed academic year certified by the principal','2022-23'),(1,'1.2.2(B)','Structure of the program clearly indicating courses, credits/Electives and Minutes of relevant Academic Council/BOS meetings highlighting the relevant documents to this metric','2022-23'),(1,'1.3.1','List and Description of courses relevant to Professional Ethics, Gender diversity and equality, Human Values, Environment and Sustainability, Women Empowerment introduced in the Curriculum along with the syllabus should be available in all the departments','2022-23'),(1,'1.3.2','Number of value-added courses for imparting transferable and life skills offered  during the year:(Data Requirement:  Names of the value-added courses (each with 30 or more contact hours) ,No. of times offered (for each value-added course) during the year ,Total number of students enrolled ,Total number of students completing the course during the year','2022-23'),(1,'1.3.2(A)','List of value-added courses which are optional and offered outside the curriculum of the programs with authorized sign.','2022-23'),(1,'1.3.2(B)','Brochure and Course content or syllabus along with course outcome of Value-added courses offered.','2022-23'),(1,'1.3.3','List of enrolled students for courses addressed in 1.3.2','2022-23'),(1,'1.3.4','List of students undertaking the field projects/ internships / student projects program-wise in the last completed academic year along with the details of title, place of work etc.','2022-23'),(1,'1.4.1','Sample Filled in feedback forms from the stakeholders to be provided.','2022-23'),(1,'1.4.2','Stakeholder feedback analysis report signed by the authority','2022-23'),(2,'2.1.1','Enrolment of Students ','2022-23'),(2,'2.1.1(A)','Students admitted(HEI signed documents)','2022-23'),(2,'2.1.1(B)','Enrolment Number_ AICTE letters of sanctioned intake','2022-23'),(2,'2.1.1(C)','Enrolment Number _ Ratified list of admitted students','2022-23'),(2,'2.1.2','Number of seats filled against reserved categories (SC, ST, OBC, Divyangjan,  etc.) as per the reservation policy during the year (exclusive of supernumerary  seats)','2022-23'),(2,'2.1.2(A)','Reservation seats categories to be considered as per the state rule (APSHE Guidelines)','2022-23'),(2,'2.1.2(B)','AP regulation for Admissions GO No. 73','2022-23'),(2,'2.1.2(C)','Guidelines for filling the left-over vacancies under reservation','2022-23'),(2,'2.1.2(D)','Category wise_ Ratified list of students admitted','2022-23'),(2,'2.1.2(E)','Admission Abstract(HEI signed documents)','2022-23'),(2,'2.2.1','The institution assesses students’ learning levels and organises special  programmes for both slow and advanced learners(Present a write-up within a maximum of 200 words)','2022-23'),(2,'2.2.1(A)','Video records of the specific topics(A document having the links and screen shots of video course/LCS)','2022-23'),(2,'2.2.1(B)','Classes for the slow learner(Time table conducted for CA failures and attendance sheets)','2022-23'),(2,'2.2.1(C)','Additional assignment(sample copies of either question papers or evaluated assignment)','2022-23'),(2,'2.2.1(D)','Make-up classes for course detention students/lateral entry students(Time tables and attendance sheets for each semester(1-8) at least one)','2022-23'),(2,'2.2.1(E)','Remedial classes conducted for end semester failures(Time tables, attendance sheets and track sheets for each semester(1-8) at least one)','2022-23'),(2,'2.2.1(F)','List of MOOCs courses(Details of student with course name and sample certificates)','2022-23'),(2,'2.2.1(G)','List of the advanced learners opted Minors and honors(Abstract certified by controller of examination)','2022-23'),(2,'2.2.1(H)','Student participation in technical events list with students event details and SAMPLE certificates','2022-23'),(2,'2.2.2','Student – Teacher (full-time) ratio(Data Requirement:  Total number of students in the institution ,Total number of full-time teachers in the institution)','2022-23'),(2,'2.2.2(A)','List of the full-time teachers','2022-23'),(2,'2.2.2(B)','Student intake','2022-23'),(2,'2.3.1','Student-centric methods such as experiential learning, participative learning and  problem-solving methodologies are used for enhancing learning experiences(Present a write-up within a maximum of 200 words.)','2022-23'),(2,'2.3.1(A)','Academic regulations and curriculum(Recent)','2022-23'),(2,'2.3.2','Teachers use ICT-enabled tools including online resources for effective teaching  and learning(Present a write-up within a maximum of 200 words.)','2022-23'),(2,'2.3.2 (A)','ICT Tools Gadgets viz. Graphic tablets, MST, Smart pen, Projector(Geotagged photos with title of photo)','2022-23'),(2,'2.3.3','Ratio of students to mentor for academic and other related issues','2022-23'),(2,'2.3.3(A)','Circular of mentor – mentees','2022-23'),(2,'2.3.3(B)','Mentor list as announced by the HEI','2022-23'),(2,'2.3.3(C)','issues raised and resolved in the mentor system(Total records of a student -&gt; one sample from each semester)','2022-23'),(2,'2.3.4','Preparation and adherence to Academic Calendar and Teaching Plans by the  institution(Describe the preparation of and adherence to the Academic Calendar and Teaching  Plans by the institution.  Present a write-up within a maximum of 200 words)','2022-23'),(2,'2.3.4(A)','Academic calendar','2022-23'),(2,'2.3.4(B)','Proof of course allotment for odd and even semesters','2022-23'),(2,'2.3.4(C)','Proof for lecture plan, Schedule and diary(one course from each semester)','2022-23'),(2,'2.3.4(D)','Minutes of AMC','2022-23'),(2,'2.4.1','Number of full-time teachers against sanctioned posts during the year(Data Requirement:  Number of full-time teachers ,Number of sanctioned posts)','2022-23'),(2,'2.4.1(A)','Sanction letter indicating number of posts','2022-23'),(2,'2.4.1(B)','Department wise List of full-time teachers appointed','2022-23'),(2,'2.4.2','Number of full-time teachers with PhD/ D.M. / M.Ch. / D.N.B Super-Specialty /  DSc / DLitt during the year(Data Requirement: List of full-time teachers with PhD/ D.M. / M.Ch. / D.N.B Super-Specialty /  DSc / DLitt.)','2022-23'),(2,'2.4.3','Total teaching experience of full-time teachers in the same institution(Data Requirement:  Name and number of full-time teachers and their years of teaching experience in the institution)','2022-23'),(2,'2.5.1','Number of days from the date of last semester-end/ year- end examination till the  declaration of results during the year(Number of days from the date of last semester-end / year-end examination till the  declaration of results year-wise during the year)','2022-23'),(2,'2.5.1(A)','Examination Result Notifications','2022-23'),(2,'2.5.1(B)','List of Programs offered and the last date of the latest semester end exams and date of result declaration','2022-23'),(2,'2.5.2','Number of students’ complaints/grievances against evaluation against the total  number who appeared in the examinations during the year','2022-23'),(2,'2.5.2(A)','Number of complaints','2022-23'),(2,'2.5.2(B)','Minutes of the examination Committee','2022-23'),(2,'2.5.3','IT integration and reforms in the examination procedures and processes  including Continuous Internal Assessment (CIA) have brought in considerable  improvement in the Examination Management System (EMS) of the Institution(Describe the examination reforms with reference to the following within a minimum  of 200 words :Examination procedures ,Processes/Procedures integrating IT ,Continuous Internal Assessment System) ','2022-23'),(2,'2.5.3(A)','Examination Regulations','2022-23'),(2,'2.5.3(B)','Question Paper templates','2022-23'),(2,'2.5.3(C)','Proof of the hybrid grading sheet','2022-23'),(2,'2.5.3(D)','Examination Results link in the website','2022-23'),(2,'2.5.3(E)','Proof of the OMR','2022-23'),(2,'2.6.1','Programme Outcomes and Course Outcomes for all Programmes offered by the  institution are stated and displayed on the website and communicated to teachers  and students(Describe Course Outcomes (COs) for all courses and the mechanism of  communication to teachers and students within a maximum of 200 words.  Upload COs for all Courses) ','2022-23'),(2,'2.6.1(A)','List of POs and COs(COs for at least two courses from each semester)','2022-23'),(2,'2.6.1(B)','Photo gallery of the COs & POs displayed Program wise','2022-23'),(2,'2.6.2','Attainment of Programme Outcomes and Course Outcomes as evaluated by the  institution(Describe the method of measuring the attainment of POs, PSOs and COs and the  level of attaiment of POs , PSOs and COs in not more than 200 words.)','2022-23'),(2,'2.6.2(A)','CO and PO attainment','2022-23'),(2,'2.6.2(B)','Files related to all surveys for the indirect assessment','2022-23'),(2,'2.6.3','Pass Percentage of students','2022-23'),(2,'2.6.3(A)','Annual report of CoE indicating the pass percentage','2022-23'),(2,'2.6.3(B)','Certified report from CoE indicating Students eligible for the degree program','2022-23'),(2,'2.7.1','Student Satisfaction Survey (SSS) on overall institutional performance  (Institution may design its own questionnaire). Results and details need to be  provided as a weblink','2022-23'),(3,'3.1.1','The institution’s research facilities are frequently updated and there is a welldefined policy for promotion of research which is uploaded on the institutional  website and implemented(Present a write-up within a maximum of 200 words.)','2022-23'),(3,'3.1.1(A)','List of research equipment along with the proof of purchases (Bill copies)','2022-23'),(3,'3.1.1(B)','Policy for Faculty Assessment and Development Scheme','2022-23'),(3,'3.1.1(C)','Financial Incentives for attending conferences, seminars and QIP','2022-23'),(3,'3.1.1(D)','Sanction letters for the funded research projects & UCs for the projects completed','2022-23'),(3,'3.1.1(E)','Copies of all MoUs for collaborative research','2022-23'),(3,'3.1.1(F)','Minutes of the governing council meeting-reflecting the research promotions','2022-23'),(3,'3.1.2','Details of Seed money','2022-23'),(3,'3.1.3','Number of teachers who were awarded national / international fellowship(s) for  advanced studies/research during the year','2022-23'),(3,'3.2.1','Grants received from Government and Non-Governmental agencies for research  projects, endowments, Chairs during the year (INR in Lakhs)','2022-23'),(3,'3.2.1(A)','List of Grants received for research projects','2022-23'),(3,'3.2.1(B)','e-copies of grants sanctioned','2022-23'),(3,'3.2.2','List of teachers having research projects during the year','2022-23'),(3,'3.2.3','Number of teachers recognized as research guides','2022-23'),(3,'3.2.4','Number of departments having research projects','2022-23'),(3,'3.2.4(A)','Web Links to Funding Agencies','2022-23'),(3,'3.3.1','Institution has created an ecosystem for innovations and creation and transfer of  knowledge supported by dedicated centres for research, entrepreneurship,  community orientation, incubation, etc.(Present a write-up within a maximum of 200 words)','2022-23'),(3,'3.3.1(A)','MSME business incubation center – sanction letter','2022-23'),(3,'3.3.1(B)','AICTE sponsored EDC – Sanction letters and list of the activities conducted by EDC','2022-23'),(3,'3.3.1(C)','Dedicated centres for research','2022-23'),(3,'3.3.2','Detailed report including photos, resource persons etc.','2022-23'),(3,'3.4.1','The Institution ensures implementation of its Code of Ethics for Research  uploaded in the website through the following:  1. Research Advisory Committee 2. Ethics Committee 3. Inclusion of Research Ethics in the research methodology course work  4. Plagiarism check through authenticated software Options: A. All of the above B. Any 3 of the above C. Any 2 of the above D. Any 1 of the above E. None of the above','2022-23'),(3,'3.4.1(A)','Research Advisory Committee','2022-23'),(3,'3.4.1(B)','Ethics Committee','2022-23'),(3,'3.4.1(C)','Inclusion of Research Ethics in the research methodology course work','2022-23'),(3,'3.4.1(D)','Anti Plagiarism software approved by JNTU','2022-23'),(3,'3.4.2','Number of PhD candidates registered per teacher (as per the data given with  regard to recognized PhD guides/ supervisors provided in Metric No. 3.2.3) during  the year','2022-23'),(3,'3.4.2(A)','List of Faculty along with the names of Research scholars','2022-23'),(3,'3.4.2(B)','Copy of the Registration letters/Joining letters','2022-23'),(3,'3.4.3','Number of research papers per teacher in CARE Journals notified on UGC  website during the year','2022-23'),(3,'3.4.4','Number of books and chapters in edited volumes / books published per teacher  during the year','2022-23'),(3,'3.4.5','Bibliometrics of the publications based on average Citation Index','2022-23'),(3,'3.4.6','Bibliometrics of the publication-based h-Index of the University','2022-23'),(3,'3.5.1','Audited statements for Revenue generated from consultancy','2022-23'),(3,'3.5.2','Total amount spent on developing facilities, training teachers and clerical/project  staff for undertaking consultancy during the year','2022-23'),(3,'3.6.1','Extension activities carried out in the neighbourhood sensitising students to social  issues for their holistic development, and the impact thereof during the year','2022-23'),(3,'3.6.1(A)','List of NSS activities conducted year wise and number of students participated','2022-23'),(3,'3.6.1(B)','Covid-19 booster report','2022-23'),(3,'3.6.1(C)','MGNCRE Reports','2022-23'),(3,'3.6.1(D)','Community Radio report','2022-23'),(3,'3.6.1(E)','List of NCC activities conducted year wise and number of students participated','2022-23'),(3,'3.6.2','Number of awards and recognition received by the Institution, its teachers and  students for extension activities from Government / Government-recognised bodies','2022-23'),(3,'3.6.3','Number of extension and outreach programmes conducted by the institution through  NSS/NCC during the year','2022-23'),(3,'3.6.4','Number of students participating in extension activities listed in 3.6.3 during the  year','2022-23'),(3,'3.7.1','Number of collaborative activities during the year for research/ faculty exchange/  student exchange/ internship/ on-the-job training/ project work','2022-23'),(3,'3.7.2','Number of functional MoUs with institutions of national and/or international  importance, other universities, industries, corporate houses, etc. during the year  (only functional MoUs with ongoing activities to be considered)','2022-23'),(3,'3.7.2(A)','e-copies of functional MoUs','2022-23'),(3,'3.7.2(B)','e-copies of Activities of MOUs','2022-23'),(4,'4.1.1','The Institution has adequate infrastructure and physical facilities for teaching learning, viz., classrooms, laboratories, computing equipments, etc.','2022-23'),(4,'4.1.1(A)','Details of the Classrooms, Labs & Other facilities across all the six blocks','2022-23'),(4,'4.1.1(B)','List of the laboratories with titles','2022-23'),(4,'4.1.1(C)','Campus LAN diagram','2022-23'),(4,'4.1.1(D)','Proof of bandwidth','2022-23'),(4,'4.1.1(E)','List of the software','2022-23'),(4,'4.1.1(F)','Photo gallery of all the academic blocks, classrooms, drawing hall, seminar hall, auditorium, and Labs','2022-23'),(4,'4.1.2','The institution has adequate facilities for cultural activities, yoga, sports and games  (indoor and outdoor) including gymnasium, yoga centre, auditorium etc.)','2022-23'),(4,'4.1.2(A)','Colleague of Geo-tagged pictures','2022-23'),(4,'4.1.2(B)','Area details of the all the facilities','2022-23'),(4,'4.1.2(C)','Photo gallery of the various activities','2022-23'),(4,'4.1.3','Number of classrooms and seminar halls with ICT-enabled facilities','2022-23'),(4,'4.1.3(A)','Geo-tagged photographs of classrooms with ICT enabled facilities','2022-23'),(4,'4.1.3(B)','Class Timetables','2022-23'),(4,'4.1.4','Expenditure for infrastructure augmentation, excluding salary, during the year (INR  in Lakhs)','2022-23'),(4,'4.1.4(A)','Budget allocation','2022-23'),(4,'4.1.4(B)','Provide the consolidated fund allocation towards infrastructure augmentation facilities duly certified by Head of the Institution','2022-23'),(4,'4.2.1','Library is automated using Integrated Library Management System (ILMS)','2022-23'),(4,'4.2.1(A)','Library operates through LIBSYS','2022-23'),(4,'4.2.1(B)','All the books are provided with RF Id security tags','2022-23'),(4,'4.2.1(C)','Copy of the latest License agreement of LIBSYS-7','2022-23'),(4,'4.2.1(D)','Digital Library','2022-23'),(4,'4.2.2','Institution has access to the following','2022-23'),(4,'4.2.2 (D)','Specific details in respect of e-resources selected.','2022-23'),(4,'4.2.2 (E)','Databases','2022-23'),(4,'4.2.2(A)','Details of subscriptions of e-journals','2022-23'),(4,'4.2.2(B)','Letter of subscription','2022-23'),(4,'4.2.2(C)','Screenshots of the facilities claimed with the name of HEI.','2022-23'),(4,'4.2.3','Expenditure on purchase of books/ e-books and subscription to journals/e-journals  during the year (INR in lakhs)','2022-23'),(4,'4.2.3(A)','Consolidated extract of expenditure','2022-23'),(4,'4.2.3(B)','Invoices of all the expenditure','2022-23'),(4,'4.2.4','Usage of library by teachers and students (footfalls and login data for online access)','2022-23'),(4,'4.2.4(A)','Certified e-copy of the ledger for footfalls for 5days','2022-23'),(4,'4.2.4(B)','Certified screenshots of the data for the same 5 days for online access','2022-23'),(4,'4.2.4(C)','Last page of accession register details','2022-23'),(4,'4.3.1','Institution has an IT policy covering Wi-Fi, cyber security, etc. and has allocated  budget for updating its IT facilities','2022-23'),(4,'4.3.1(A)','Colleague of Geo-tagged pictures','2022-23'),(4,'4.3.1(B)','Policy document','2022-23'),(4,'4.3.1(C)','Campus Wi-Fi/Network diagram','2022-23'),(4,'4.3.1(D)','Budget of the year 2020-21','2022-23'),(4,'4.3.2','Student - Computer ratio','2022-23'),(4,'4.3.2(A)','Computer Bills','2022-23'),(4,'4.3.2(B)','Student strength','2022-23'),(4,'4.3.3','Bandwidth of internet connection in the Institution and the number of students on  campus','2022-23'),(4,'4.3.3(A)','Details of available bandwidth of internet connection in the Institution','2022-23'),(4,'4.3.3(B)','Bills for any one month/one quarter.','2022-23'),(4,'4.3.3(C)','e-copy of document of agreement with the service provider.','2022-23'),(4,'4.3.4','Institution has facilities for e-content development','2022-23'),(4,'4.3.4(A)','Geo tagged photographs of Media Centre, Audio Visual Centre etc.,','2022-23'),(4,'4.3.4(B)','Purchase bills for Lecture Capturing System','2022-23'),(4,'4.3.4(C)','Audited income expenditure statement highlighting the relevant expenditure.','2022-23'),(4,'4.4.1','Expenditure incurred on maintenance of physical and academic support facilities, excluding salary component, during the year (INR in lakhs)','2022-23'),(4,'4.4.1(A)','Audited statements of accounts.','2022-23'),(4,'4.4.2','There are established systems and procedures for maintaining and utilizing  physical, academic and support facilities – classrooms, laboratory, library, sports  complex, computers, etc.','2022-23'),(4,'4.4.2(A)','Schedules of Library, Sport complex ','2022-23'),(4,'4.4.2(B)','Laboratory Timetables','2022-23'),(4,'4.4.2(C)','SOP for Laboratory utilization','2022-23'),(4,'4.4.2(D)','SOP for usage of general amenities','2022-23'),(4,'4.4.2(E)','Geo-tagged photos of GAMYA','2022-23'),(4,'4.4.2(F)','Maintenance schedules and AMC letters from Estate department','2022-23'),(5,'5.1.1','Number of students benefitted by scholarships and freeships provided by the  Government during the year','2022-23'),(5,'5.1.2','Number of students benefitted by scholarships and freeships provided by the  institution and non-government agencies during the year','2022-23'),(5,'5.1.3','The following Capacity Development and Skill Enhancement activities are  organised for improving students’ capabilities','2022-23'),(5,'5.1.3(A)','Soft Skill, Language and Communication','2022-23'),(5,'5.1.3(B)','Yoga Class','2022-23'),(5,'5.1.3(C)','Awareness of Trends in technology','2022-23'),(5,'5.1.4','Number of students benefitted from guidance/coaching for competitive  examinations and career counselling offered by the institution during the year','2022-23'),(5,'5.1.5','The institution adopts the following mechanism for redressal of students’  grievances, including sexual harassment and ragging','2022-23'),(5,'5.2.1','Number of outgoing students who got placement during the year','2022-23'),(5,'5.2.2','Number of outgoing students progressing to higher education during the year','2022-23'),(5,'5.2.3','Number of students qualifying in state/ national/ international level examinations  during the year','2022-23'),(5,'5.3.1','Number of awards/medals for outstanding performance in sports and/or cultural  activities at inter-university / state /national / international events (award for a team  event should be counted as one) during the year','2022-23'),(5,'5.3.2','Presence of an active Student Council and representation of students in academic  and administrative bodies/committees of the institution','2022-23'),(5,'5.3.3','Number of sports and cultural events / competitions organised by the institution','2022-23'),(5,'5.4.1','The Alumni Association and its Chapters (registered and functional) contribute  significantly to the development of the institution through financial and other  support services','2022-23'),(5,'5.4.2','Alumni’s financial contribution during the year','2022-23'),(6,'6.1.1','The governance of the institution is reflective of an effective leadership in tune with  the vision and mission of the Institution','2022-23'),(6,'6.1.1(A)','Academic Monitoring Committee meeting minutes','2022-23'),(6,'6.1.1(B)','Placement Committee meeting minutes','2022-23'),(6,'6.1.1(C)','SAC - coordinators meeting minutes','2022-23'),(6,'6.1.1(D)','Anti Ragging Committee meeting minutes','2022-23'),(6,'6.1.1(E)','IQAC meeting minutes','2022-23'),(6,'6.1.1(F)','Board of Studies Meeting minutes','2022-23'),(6,'6.1.1(G)','Academic Council meeting minutes','2022-23'),(6,'6.1.1(H)','Governing Council meeting minutes','2022-23'),(6,'6.1.1(I)','HOD/Academic Development Committee Meeting Minutes','2022-23'),(6,'6.1.1(J)','Finance Committee meeting minutes','2022-23'),(6,'6.1.1(K)','Library Committee meeting minutes','2022-23'),(6,'6.1.1(L)','Town Hall meeting minutes','2022-23'),(6,'6.1.2','Effective leadership is reflected in various institutional practices such as decentralization and participative management','2022-23'),(6,'6.1.2(A)','Strat-Plan','2022-23'),(6,'6.1.2(B)','Requisition for two set of mid question papers from CoE – e-mail proof','2022-23'),(6,'6.1.2(C)','Declaration of mid question paper set number from CoE– e-mail proof','2022-23'),(6,'6.1.2(D)','Uniform Evaluation','2022-23'),(6,'6.1.2(E)','Research Review meeting minutes','2022-23'),(6,'6.2.1','The institutional Strategic/ Perspective plan has been clearly articulated and  implemented','2022-23'),(6,'6.2.1(A)','Strat-Plan','2022-23'),(6,'6.2.1(B)','Merit scholarships based on AP-EAMCET rank','2022-23'),(6,'6.2.1(C)','Meritorious scholarships for students','2022-23'),(6,'6.2.1(D)','Details of the GATE training classes conducted; List of students attended GATE Coaching & secured score','2022-23'),(6,'6.2.1(E)','Details of the CRT programs/ Technical training/Competitions conducted (Total number of hours), the List of the students attended the training programs & Proof of attendance, Branch wise list of the students placed, and the number of companies visited. List of students attended the WTN.','2022-23'),(6,'6.2.1(F)','Motivational and inspirational talks by the industry experts','2022-23'),(6,'6.2.1(G)','Social media updates','2022-23'),(6,'6.2.2','The functioning of the various institutional bodies is effective and efficient as visible  from the policies, administrative set-up, appointment and service rules, procedures,  etc. ','2022-23'),(6,'6.2.2(A)','Organogram','2022-23'),(6,'6.2.2(B)','HR & Service rules/Incentive policies','2022-23'),(6,'6.2.2(D)','Frequency and conduct of the meetings of governance committees (GC, AC, BOS, FC)','2022-23'),(6,'6.2.2(E)','SOP for procurement (AOP, MRN, Comparative statements and Purchase Orders)','2022-23'),(6,'6.2.3','Implementation of e-governance in areas of operation: 1. Administration 2. Finance and Accounts 3. Student Admission and Support 4. Examination','2022-23'),(6,'6.3.1','The institution has effective welfare measures for teaching and non-teaching staff  and avenues for their career development/ progression','2022-23'),(6,'6.3.1(A)','HR Policies (Welfare & Career development)','2022-23'),(6,'6.3.1(B)','Details of the training programs conducted for teaching & non-teaching staff','2022-23'),(6,'6.3.1(C)','Details of the staff (Teaching & Non-teaching promoted)','2022-23'),(6,'6.3.1(D)','Details of on campus housing, Term insurance, Medical insurance, Children education, ESI, Cooperative credit society, Concessions in IP/OP services and Gratuity','2022-23'),(6,'6.3.1(E)','Details of the faculty received incentives for completion of Ph.D./ QIP','2022-23'),(6,'6.3.2','Number of teachers provided with financial support to attend conferences /  workshops and towards payment of membership fee of professional bodies during the  year','2022-23'),(6,'6.3.2(A)','Number of teachers provided with financial support to attend conferences / workshops and towards payment of membership fee of professional bodies during the year','2022-23'),(6,'6.3.3','Number of professional development / administrative training programmes  organized by the Institution for its teaching and non-teaching staff during the year','2022-23'),(6,'6.3.3(A)','Annual Reports highlighting training programs conducted for teaching & non teaching','2022-23'),(6,'6.3.3(B)','Training programs conducted for teaching & non-teaching','2022-23'),(6,'6.3.4','Number of teachers who have undergone online/ face-to-face Faculty Development  Programmes during the year: (Professional Development Programmes, Orientation / Induction Programmes,  Refresher Courses, Short-Term Course, etc.)','2022-23'),(6,'6.3.4(A)','List of Faculty FDPS Attended & Proofs','2022-23'),(6,'6.4.1','Institution conducts internal and external financial audits regularly','2022-23'),(6,'6.4.2','Funds / Grants received from non-government bodies, individuals, and  philanthropists during the year (not covered in Criterion III and V) (INR in lakhs)','2022-23'),(6,'6.4.3','Institutional strategies for mobilisation of funds and the optimal utilisation of  resources','2022-23'),(6,'6.5.1','Internal Quality Assurance Cell (IQAC) has contributed significantly for  institutionalizing quality assurance strategies and processes visible in terms of  incremental improvements made during the preceding year with regard to quality (in  case of the First Cycle): Incremental improvements made during the preceding year with regard to quality  and post-accreditation quality initiatives (Second and subsequent cycles)','2022-23'),(6,'6.5.1(A)','List of companies hosted Internship','2022-23'),(6,'6.5.1(B)','FADS – Supporting Docs','2022-23'),(6,'6.5.1(C)','Consolidated initiatives of IQAC (strategies & contributions of IQAC mentioned)','2022-23'),(6,'6.5.2','The institution reviews its teaching-learning process, structures and methodologies  of operation and learning outcomes at periodic intervals through its IQAC as per  norms','2022-23'),(6,'6.5.2(A)','List of the IQAC initiatives taken up to enhance the students’ performance (Course coordinator meetings, AMC meeting minutes, Remedial classes for slow learners)','2022-23'),(6,'6.5.2(B)','Details of the progression of the students from 1st to 8th semesters branch wise for 2020-21 for all the batches graduated – 1 Bar chart, in each bar chart with eight bars','2022-23'),(6,'6.5.2(C)','Structures & methodologies of operations ISO audits, Academic audits -Internal & External branch wise','2022-23'),(6,'6.5.2(D)','Internal & External Academic Audit Report','2022-23'),(6,'6.5.3','Quality assurance initiatives of the institution include','2022-23'),(6,'6.5.3(A)','IQAC meeting minutes','2022-23'),(6,'6.5.3(B)','Feedback system of the institution','2022-23'),(6,'6.5.3(C)','Feedback system for design and review of syllabus','2022-23'),(6,'6.5.3(D)','Collaborative quality initiatives','2022-23'),(6,'6.5.3(E)','Participation in NIRF','2022-23'),(6,'6.5.3(F)','NBA accreditation','2022-23'),(6,'6.5.3(G)','NAAC accreditation','2022-23'),(6,'6.5.3(H)','IQAC Feedback analysis','2022-23'),(7,'7.1.1','Measures initiated by the institution for the promotion of gender equity during the  year','2022-23'),(7,'7.1.1(A)','Action plan of WEC for gender sensitization','2022-23'),(7,'7.1.1(B)','Campus surveillance with CC TV (Audit report)','2022-23'),(7,'7.1.1(C)','Policy for women security and safety (PASH)','2022-23'),(7,'7.1.1(D)','Photographs of Exclusive reading rooms, waiting rooms and rest rooms','2022-23'),(7,'7.1.1(E)','Day care centre for the kids (Beneficiaries)','2022-23'),(7,'7.1.1(F)','Welfare measures (Maternity leave for two kids)','2022-23'),(7,'7.1.1(G)','List of the beneficiaries','2022-23'),(7,'7.1.10','The institution has a prescribed code of conduct for students, teachers, administrators  and other staff and conducts periodic sensitization programmes in this regard','2022-23'),(7,'7.1.10(A)','Details of the monitoring committee composition and minutes of the committee meeting, number of programmes organized, reports on the various programmes, etc. in support of the claims','2022-23'),(7,'7.1.10(B)','Policy document on code of ethics','2022-23'),(7,'7.1.10(C)','Circulars and geo tagged photographs and caption of the activities organized under the metric for teachers, students, administrators and other staffs','2022-23'),(7,'7.1.11','Institution celebrates / organizes national and international commemorative days,  events and festivals','2022-23'),(7,'7.1.11(A)','Annual report of the celebrations and commemorative events','2022-23'),(7,'7.1.11(B)','Photographs of some of the events','2022-23'),(7,'7.1.2','The Institution has facilities for alternate sources of energy and energy conservation ','2022-23'),(7,'7.1.2(A)','Geo tagged photographs with caption of the facilities','2022-23'),(7,'7.1.2(B)','Bills for the purchase of equipment for the facilities','2022-23'),(7,'7.1.2(C)','Permission document for connection to the grid from Government','2022-23'),(7,'7.1.3','Describe the facilities in the institution for the management of the following types of  degradable and non-degradable waste (within a maximum of 200 words)','2022-23'),(7,'7.1.3(A)','Geo tagged photographs of the facilities','2022-23'),(7,'7.1.3(B)','SOP for solid waste management','2022-23'),(7,'7.1.4','Water conservation facilities available in the institution','2022-23'),(7,'7.1.4(A)','Geo tagged photographs with caption of the facilities','2022-23'),(7,'7.1.4(B)','Bills for the purchase of equipment','2022-23'),(7,'7.1.5','Green campus initiatives include','2022-23'),(7,'7.1.5(A)','Policy document on the green campus','2022-23'),(7,'7.1.5(B)','Geo tagged photographs/Videos with caption of the facilities','2022-23'),(7,'7.1.5(C)','Circulars for the implementation of the initiatives and any other supporting document','2022-23'),(7,'7.1.6','Quality audits on environment and energy undertaken by the institution','2022-23'),(7,'7.1.6(A)','Policy document on environment and energy usage','2022-23'),(7,'7.1.6(B)','Certificate from the auditing agency','2022-23'),(7,'7.1.6(C)','Certificates of the awards received from the recognized agency','2022-23'),(7,'7.1.6(D)','Report on environmental promotional activities conducted beyond the campus','2022-23'),(7,'7.1.6(F)','Green audit report','2022-23'),(7,'7.1.7','The Institution has a Divyangjan-friendly and barrier-free environment','2022-23'),(7,'7.1.7(A)','Policy document and information brochure','2022-23'),(7,'7.1.7(B)','Geo tagged photos','2022-23'),(7,'7.1.7(C)','Bills and invoice/purchase order/AMC','2022-23'),(7,'7.1.7(D)','A rest room should include specific requirements of Divyangjan','2022-23'),(7,'7.1.7(E)','Bills for the software procured','2022-23'),(7,'7.1.8','Describe the Institutional efforts/initiatives in providing an inclusive environment i.e.  tolerance and harmony towards cultural, regional, linguistic, communal, socioeconomic and other diversities (within a maximum of 200 words)','2022-23'),(7,'7.1.8(A)','List of the outbound programs','2022-23'),(7,'7.1.8(B)','List of national level activities (Cultural, Sports, Academic, Cultural & Sports)','2022-23'),(7,'7.1.8(C)','List of the faculty & students coming from the out of state','2022-23'),(7,'7.1.9','Sensitization of students and employees of the institution to constitutional obligations:  values, rights, duties and responsibilities of citizens','2022-23'),(7,'7.1.9(A)','List of the activities/Events conducted, and the number of students present. (Awareness programs on Women safety & protection, Anti ragging, Judicial rights, Gender equality, Traffic rules, Environment protection, Conservation of natural resources (power & water)) >','2022-23'),(7,'7.2.1','Provide the weblink on the Institutional website regarding the Best practices as per  the prescribed format of NAAC','2022-23'),(7,'7.3.1','Institutional Distinctiveness','2022-23');
/*!40000 ALTER TABLE `criteria` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `criteria1`
--

LOCK TABLES `criteria1` WRITE;
/*!40000 ALTER TABLE `criteria1` DISABLE KEYS */;
INSERT INTO `criteria1` VALUES (1,'1.1.1','Copies of PEOs, POs & PSOs for all programs','2022-23'),(1,'1.1.2','Number of Programmes where syllabus revision was carried out during the year(Data Requirement: Programme Code ,Names of the Programmes revised)','2022-23'),(1,'1.1.2(A)','List of programs where syllabus revision has been carried out signed by the Principal','2022-23'),(1,'1.1.2(B)','Approved Minutes of relevant Academic Council/BOS meetings highlighting the specific agenda item relevant to the metric year wise','2022-23'),(1,'1.1.3','Number of courses focusing on employability/entrepreneurship/ skill development  offered by the Institution during the year(Data Requirement: Name of the Course with Course Code ,Name of the Programme ,Activities which have a direct bearing on employability/ entrepreneurship/ skill  development)','2022-23'),(1,'1.1.3(A)','Syllabus copy of the courses highlighting Focus on employability/entrepreneurship/ skill development along with their course outcomes.','2022-23'),(1,'1.1.3(B)','Minutes of the Boards of Studies/ Academic Council meetings with approval for these courses.','2022-23'),(1,'1.1.3(C)','List of MoUs','2022-23'),(1,'1.2.1','Number of new courses introduced across all programmes offered during the year(Data Requirement: Name of the newly introduced course (s)  ,Name of the Programme)','2022-23'),(1,'1.2.1(A)','List of new courses introduced program-wise during the assessment period certified by the Principal.','2022-23'),(1,'1.2.1(B)','Minutes of relevant Academic Council/BOS meetings highlighting the name of the new courses introduced','2022-23'),(1,'1.2.2','Number of Programmes offered through Choice Based Credit System  (CBCS)/Elective Course System(Data Requirement: ,Names of all Programmes offered through CBCS ,Names of all Programmes offered through Elective Course System)','2022-23'),(1,'1.2.2(A)','List of programs in which CBCS/Elective course system implemented in the last completed academic year certified by the principal','2022-23'),(1,'1.2.2(B)','Structure of the program clearly indicating courses, credits/Electives and Minutes of relevant Academic Council/BOS meetings highlighting the relevant documents to this metric','2022-23'),(1,'1.3.1','List and Description of courses relevant to Professional Ethics, Gender diversity and equality, Human Values, Environment and Sustainability, Women Empowerment introduced in the Curriculum along with the syllabus should be available in all the departments','2022-23'),(1,'1.3.2','Number of value-added courses for imparting transferable and life skills offered  during the year:(Data Requirement:  Names of the value-added courses (each with 30 or more contact hours) ,No. of times offered (for each value-added course) during the year ,Total number of students enrolled ,Total number of students completing the course during the year','2022-23'),(1,'1.3.2(A)','List of value-added courses which are optional and offered outside the curriculum of the programs with authorized sign.','2022-23'),(1,'1.3.2(B)','Brochure and Course content or syllabus along with course outcome of Value-added courses offered.','2022-23'),(1,'1.3.3','List of enrolled students for courses addressed in 1.3.2','2022-23'),(1,'1.3.4','List of students undertaking the field projects/ internships / student projects program-wise in the last completed academic year along with the details of title, place of work etc.','2022-23'),(1,'1.4.1','Sample Filled in feedback forms from the stakeholders to be provided.','2022-23'),(1,'1.4.2','Stakeholder feedback analysis report signed by the authority','2022-23'),(2,'2.1.1','Enrolment of Students ','2022-23'),(2,'2.1.1(A)','Students admitted(HEI signed documents)','2022-23'),(2,'2.1.1(B)','Enrolment Number_ AICTE letters of sanctioned intake','2022-23'),(2,'2.1.1(C)','Enrolment Number _ Ratified list of admitted students','2022-23'),(2,'2.1.2','Number of seats filled against reserved categories (SC, ST, OBC, Divyangjan,  etc.) as per the reservation policy during the year (exclusive of supernumerary  seats)','2022-23'),(2,'2.1.2(A)','Reservation seats categories to be considered as per the state rule (APSHE Guidelines)','2022-23'),(2,'2.1.2(B)','AP regulation for Admissions GO No. 73','2022-23'),(2,'2.1.2(C)','Guidelines for filling the left-over vacancies under reservation','2022-23'),(2,'2.1.2(D)','Category wise_ Ratified list of students admitted','2022-23'),(2,'2.1.2(E)','Admission Abstract(HEI signed documents)','2022-23'),(2,'2.2.1','The institution assesses students’ learning levels and organises special  programmes for both slow and advanced learners(Present a write-up within a maximum of 200 words)','2022-23'),(2,'2.2.1(A)','Video records of the specific topics(A document having the links and screen shots of video course/LCS)','2022-23'),(2,'2.2.1(B)','Classes for the slow learner(Time table conducted for CA failures and attendance sheets)','2022-23'),(2,'2.2.1(C)','Additional assignment(sample copies of either question papers or evaluated assignment)','2022-23'),(2,'2.2.1(D)','Make-up classes for course detention students/lateral entry students(Time tables and attendance sheets for each semester(1-8) at least one)','2022-23'),(2,'2.2.1(E)','Remedial classes conducted for end semester failures(Time tables, attendance sheets and track sheets for each semester(1-8) at least one)','2022-23'),(2,'2.2.1(F)','List of MOOCs courses(Details of student with course name and sample certificates)','2022-23'),(2,'2.2.1(G)','List of the advanced learners opted Minors and honors(Abstract certified by controller of examination)','2022-23'),(2,'2.2.1(H)','Student participation in technical events list with students event details and SAMPLE certificates','2022-23'),(2,'2.2.2','Student – Teacher (full-time) ratio(Data Requirement:  Total number of students in the institution ,Total number of full-time teachers in the institution)','2022-23'),(2,'2.2.2(A)','List of the full-time teachers','2022-23'),(2,'2.2.2(B)','Student intake','2022-23'),(2,'2.3.1','Student-centric methods such as experiential learning, participative learning and  problem-solving methodologies are used for enhancing learning experiences(Present a write-up within a maximum of 200 words.)','2022-23'),(2,'2.3.1(A)','Academic regulations and curriculum(Recent)','2022-23'),(2,'2.3.2','Teachers use ICT-enabled tools including online resources for effective teaching  and learning(Present a write-up within a maximum of 200 words.)','2022-23'),(2,'2.3.2 (A)','ICT Tools Gadgets viz. Graphic tablets, MST, Smart pen, Projector(Geotagged photos with title of photo)','2022-23'),(2,'2.3.3','Ratio of students to mentor for academic and other related issues','2022-23'),(2,'2.3.3(A)','Circular of mentor – mentees','2022-23'),(2,'2.3.3(B)','Mentor list as announced by the HEI','2022-23'),(2,'2.3.3(C)','issues raised and resolved in the mentor system(Total records of a student -&gt; one sample from each semester)','2022-23'),(2,'2.3.4','Preparation and adherence to Academic Calendar and Teaching Plans by the  institution(Describe the preparation of and adherence to the Academic Calendar and Teaching  Plans by the institution.  Present a write-up within a maximum of 200 words)','2022-23'),(2,'2.3.4(A)','Academic calendar','2022-23'),(2,'2.3.4(B)','Proof of course allotment for odd and even semesters','2022-23'),(2,'2.3.4(C)','Proof for lecture plan, Schedule and diary(one course from each semester)','2022-23'),(2,'2.3.4(D)','Minutes of AMC','2022-23'),(2,'2.4.1','Number of full-time teachers against sanctioned posts during the year(Data Requirement:  Number of full-time teachers ,Number of sanctioned posts)','2022-23'),(2,'2.4.1(A)','Sanction letter indicating number of posts','2022-23'),(2,'2.4.1(B)','Department wise List of full-time teachers appointed','2022-23'),(2,'2.4.2','Number of full-time teachers with PhD/ D.M. / M.Ch. / D.N.B Super-Specialty /  DSc / DLitt during the year(Data Requirement: List of full-time teachers with PhD/ D.M. / M.Ch. / D.N.B Super-Specialty /  DSc / DLitt.)','2022-23'),(2,'2.4.3','Total teaching experience of full-time teachers in the same institution(Data Requirement:  Name and number of full-time teachers and their years of teaching experience in the institution)','2022-23'),(2,'2.5.1','Number of days from the date of last semester-end/ year- end examination till the  declaration of results during the year(Number of days from the date of last semester-end / year-end examination till the  declaration of results year-wise during the year)','2022-23'),(2,'2.5.1(A)','Examination Result Notifications','2022-23'),(2,'2.5.1(B)','List of Programs offered and the last date of the latest semester end exams and date of result declaration','2022-23'),(2,'2.5.2','Number of students’ complaints/grievances against evaluation against the total  number who appeared in the examinations during the year','2022-23'),(2,'2.5.2(A)','Number of complaints','2022-23'),(2,'2.5.2(B)','Minutes of the examination Committee','2022-23'),(2,'2.5.3','IT integration and reforms in the examination procedures and processes  including Continuous Internal Assessment (CIA) have brought in considerable  improvement in the Examination Management System (EMS) of the Institution(Describe the examination reforms with reference to the following within a minimum  of 200 words :Examination procedures ,Processes/Procedures integrating IT ,Continuous Internal Assessment System) ','2022-23'),(2,'2.5.3(A)','Examination Regulations','2022-23'),(2,'2.5.3(B)','Question Paper templates','2022-23'),(2,'2.5.3(C)','Proof of the hybrid grading sheet','2022-23'),(2,'2.5.3(D)','Examination Results link in the website','2022-23'),(2,'2.5.3(E)','Proof of the OMR','2022-23'),(2,'2.6.1','Programme Outcomes and Course Outcomes for all Programmes offered by the  institution are stated and displayed on the website and communicated to teachers  and students(Describe Course Outcomes (COs) for all courses and the mechanism of  communication to teachers and students within a maximum of 200 words.  Upload COs for all Courses) ','2022-23'),(2,'2.6.1(A)','List of POs and COs(COs for at least two courses from each semester)','2022-23'),(2,'2.6.1(B)','Photo gallery of the COs & POs displayed Program wise','2022-23'),(2,'2.6.2','Attainment of Programme Outcomes and Course Outcomes as evaluated by the  institution(Describe the method of measuring the attainment of POs, PSOs and COs and the  level of attaiment of POs , PSOs and COs in not more than 200 words.)','2022-23'),(2,'2.6.2(A)','CO and PO attainment','2022-23'),(2,'2.6.2(B)','Files related to all surveys for the indirect assessment','2022-23'),(2,'2.6.3','Pass Percentage of students','2022-23'),(2,'2.6.3(A)','Annual report of CoE indicating the pass percentage','2022-23'),(2,'2.6.3(B)','Certified report from CoE indicating Students eligible for the degree program','2022-23'),(2,'2.7.1','Student Satisfaction Survey (SSS) on overall institutional performance  (Institution may design its own questionnaire). Results and details need to be  provided as a weblink','2022-23'),(3,'3.1.1','The institution’s research facilities are frequently updated and there is a welldefined policy for promotion of research which is uploaded on the institutional  website and implemented(Present a write-up within a maximum of 200 words.)','2022-23'),(3,'3.1.1(A)','List of research equipment along with the proof of purchases (Bill copies)','2022-23'),(3,'3.1.1(B)','Policy for Faculty Assessment and Development Scheme','2022-23'),(3,'3.1.1(C)','Financial Incentives for attending conferences, seminars and QIP','2022-23'),(3,'3.1.1(D)','Sanction letters for the funded research projects & UCs for the projects completed','2022-23'),(3,'3.1.1(E)','Copies of all MoUs for collaborative research','2022-23'),(3,'3.1.1(F)','Minutes of the governing council meeting-reflecting the research promotions','2022-23'),(3,'3.1.2','Details of Seed money','2022-23'),(3,'3.1.3','Number of teachers who were awarded national / international fellowship(s) for  advanced studies/research during the year','2022-23'),(3,'3.2.1','Grants received from Government and Non-Governmental agencies for research  projects, endowments, Chairs during the year (INR in Lakhs)','2022-23'),(3,'3.2.1(A)','List of Grants received for research projects','2022-23'),(3,'3.2.1(B)','e-copies of grants sanctioned','2022-23'),(3,'3.2.2','List of teachers having research projects during the year','2022-23'),(3,'3.2.3','Number of teachers recognized as research guides','2022-23'),(3,'3.2.4','Number of departments having research projects','2022-23'),(3,'3.2.4(A)','Web Links to Funding Agencies','2022-23'),(3,'3.3.1','Institution has created an ecosystem for innovations and creation and transfer of  knowledge supported by dedicated centres for research, entrepreneurship,  community orientation, incubation, etc.(Present a write-up within a maximum of 200 words)','2022-23'),(3,'3.3.1(A)','MSME business incubation center – sanction letter','2022-23'),(3,'3.3.1(B)','AICTE sponsored EDC – Sanction letters and list of the activities conducted by EDC','2022-23'),(3,'3.3.1(C)','Dedicated centres for research','2022-23'),(3,'3.3.2','Detailed report including photos, resource persons etc.','2022-23'),(3,'3.4.1','The Institution ensures implementation of its Code of Ethics for Research  uploaded in the website through the following:  1. Research Advisory Committee 2. Ethics Committee 3. Inclusion of Research Ethics in the research methodology course work  4. Plagiarism check through authenticated software Options: A. All of the above B. Any 3 of the above C. Any 2 of the above D. Any 1 of the above E. None of the above','2022-23'),(3,'3.4.1(A)','Research Advisory Committee','2022-23'),(3,'3.4.1(B)','Ethics Committee','2022-23'),(3,'3.4.1(C)','Inclusion of Research Ethics in the research methodology course work','2022-23'),(3,'3.4.1(D)','Anti Plagiarism software approved by JNTU','2022-23'),(3,'3.4.2','Number of PhD candidates registered per teacher (as per the data given with  regard to recognized PhD guides/ supervisors provided in Metric No. 3.2.3) during  the year','2022-23'),(3,'3.4.2(A)','List of Faculty along with the names of Research scholars','2022-23'),(3,'3.4.2(B)','Copy of the Registration letters/Joining letters','2022-23'),(3,'3.4.3','Number of research papers per teacher in CARE Journals notified on UGC  website during the year','2022-23'),(3,'3.4.4','Number of books and chapters in edited volumes / books published per teacher  during the year','2022-23'),(3,'3.4.5','Bibliometrics of the publications based on average Citation Index','2022-23'),(3,'3.4.6','Bibliometrics of the publication-based h-Index of the University','2022-23'),(3,'3.5.1','Audited statements for Revenue generated from consultancy','2022-23'),(3,'3.5.2','Total amount spent on developing facilities, training teachers and clerical/project  staff for undertaking consultancy during the year','2022-23'),(3,'3.6.1','Extension activities carried out in the neighbourhood sensitising students to social  issues for their holistic development, and the impact thereof during the year','2022-23'),(3,'3.6.1(A)','List of NSS activities conducted year wise and number of students participated','2022-23'),(3,'3.6.1(B)','Covid-19 booster report','2022-23'),(3,'3.6.1(C)','MGNCRE Reports','2022-23'),(3,'3.6.1(D)','Community Radio report','2022-23'),(3,'3.6.1(E)','List of NCC activities conducted year wise and number of students participated','2022-23'),(3,'3.6.2','Number of awards and recognition received by the Institution, its teachers and  students for extension activities from Government / Government-recognised bodies','2022-23'),(3,'3.6.3','Number of extension and outreach programmes conducted by the institution through  NSS/NCC during the year','2022-23'),(3,'3.6.4','Number of students participating in extension activities listed in 3.6.3 during the  year','2022-23'),(3,'3.7.1','Number of collaborative activities during the year for research/ faculty exchange/  student exchange/ internship/ on-the-job training/ project work','2022-23'),(3,'3.7.2','Number of functional MoUs with institutions of national and/or international  importance, other universities, industries, corporate houses, etc. during the year  (only functional MoUs with ongoing activities to be considered)','2022-23'),(3,'3.7.2(A)','e-copies of functional MoUs','2022-23'),(3,'3.7.2(B)','e-copies of Activities of MOUs','2022-23'),(4,'4.1.1','The Institution has adequate infrastructure and physical facilities for teachinglearning, viz., classrooms, laboratories, computing equipments, etc.','2022-23'),(4,'4.1.1(A)','Details of the Classrooms, Labs & Other facilities across all the six blocks','2022-23'),(4,'4.1.1(B)','List of the laboratories with titles','2022-23'),(4,'4.1.1(C)','Campus LAN diagram','2022-23'),(4,'4.1.1(D)','Proof of bandwidth','2022-23'),(4,'4.1.1(E)','List of the software','2022-23'),(4,'4.1.1(F)','Photo gallery of all the academic blocks, classrooms, drawing hall, seminar hall, auditorium, and Labs','2022-23'),(4,'4.1.2','The institution has adequate facilities for cultural activities, yoga, sports and games  (indoor and outdoor) including gymnasium, yoga centre, auditorium etc.)','2022-23'),(4,'4.1.2(A)','Colleague of Geo-tagged pictures','2022-23'),(4,'4.1.2(B)','Area details of the all the facilities','2022-23'),(4,'4.1.2(C)','Photo gallery of the various activities','2022-23'),(4,'4.1.3','Number of classrooms and seminar halls with ICT-enabled facilities','2022-23'),(4,'4.1.3(A)','Geo-tagged photographs of classrooms with ICT enabled facilities','2022-23'),(4,'4.1.3(B)','Class Timetables','2022-23'),(4,'4.1.4','Expenditure for infrastructure augmentation, excluding salary, during the year (INR  in Lakhs)','2022-23'),(4,'4.1.4(A)','Budget allocation','2022-23'),(4,'4.1.4(B)','Provide the consolidated fund allocation towards infrastructure augmentation facilities duly certified by Head of the Institution','2022-23'),(4,'4.2.1','Library is automated using Integrated Library Management System (ILMS)','2022-23'),(4,'4.2.1(A)','Library operates through LIBSYS','2022-23'),(4,'4.2.1(B)','All the books are provided with RF Id security tags','2022-23'),(4,'4.2.1(C)','Copy of the latest License agreement of LIBSYS-7','2022-23'),(4,'4.2.1(D)','Digital Library','2022-23'),(4,'4.2.2','Institution has access to the following','2022-23'),(4,'4.2.2 (D)','Specific details in respect of e-resources selected.','2022-23'),(4,'4.2.2 (E)','Databases','2022-23'),(4,'4.2.2(A)','Details of subscriptions of e-journals','2022-23'),(4,'4.2.2(B)','Letter of subscription','2022-23'),(4,'4.2.2(C)','Screenshots of the facilities claimed with the name of HEI.','2022-23'),(4,'4.2.3','Expenditure on purchase of books/ e-books and subscription to journals/e-journals  during the year (INR in lakhs)','2022-23'),(4,'4.2.3(A)','Consolidated extract of expenditure','2022-23'),(4,'4.2.3(B)','Invoices of all the expenditure','2022-23'),(4,'4.2.4','Usage of library by teachers and students (footfalls and login data for online access)','2022-23'),(4,'4.2.4(A)','Certified e-copy of the ledger for footfalls for 5days','2022-23'),(4,'4.2.4(B)','Certified screenshots of the data for the same 5 days for online access','2022-23'),(4,'4.2.4(C)','Last page of accession register details','2022-23'),(4,'4.3.1','Institution has an IT policy covering Wi-Fi, cyber security, etc. and has allocated  budget for updating its IT facilities','2022-23'),(4,'4.3.1(A)','Colleague of Geo-tagged pictures','2022-23'),(4,'4.3.1(B)','Policy document','2022-23'),(4,'4.3.1(C)','Campus Wi-Fi/Network diagram','2022-23'),(4,'4.3.1(D)','Budget of the year 2020-21','2022-23'),(4,'4.3.2','Student - Computer ratio','2022-23'),(4,'4.3.2(A)','Computer Bills','2022-23'),(4,'4.3.2(B)','Student strength','2022-23'),(4,'4.3.3','Bandwidth of internet connection in the Institution and the number of students on  campus','2022-23'),(4,'4.3.3(A)','Details of available bandwidth of internet connection in the Institution','2022-23'),(4,'4.3.3(B)','Bills for any one month/one quarter.','2022-23'),(4,'4.3.3(C)','e-copy of document of agreement with the service provider.','2022-23'),(4,'4.3.4','Institution has facilities for e-content development','2022-23'),(4,'4.3.4(A)','Geo tagged photographs of Media Centre, Audio Visual Centre etc.,','2022-23'),(4,'4.3.4(B)','Purchase bills for Lecture Capturing System','2022-23'),(4,'4.3.4(C)','Audited income expenditure statement highlighting the relevant expenditure.','2022-23'),(4,'4.4.1','Expenditure incurred on maintenance of physical and academic support facilities, excluding salary component, during the year (INR in lakhs)','2022-23'),(4,'4.4.1(A)','Audited statements of accounts.','2022-23'),(4,'4.4.2','There are established systems and procedures for maintaining and utilizing  physical, academic and support facilities – classrooms, laboratory, library, sports  complex, computers, etc.','2022-23'),(4,'4.4.2(A)','Schedules of Library, Sport complex ','2022-23'),(4,'4.4.2(B)','Laboratory Timetables','2022-23'),(4,'4.4.2(C)','SOP for Laboratory utilization','2022-23'),(4,'4.4.2(D)','SOP for usage of general amenities','2022-23'),(4,'4.4.2(E)','Geo-tagged photos of GAMYA','2022-23'),(4,'4.4.2(F)','Maintenance schedules and AMC letters from Estate department','2022-23'),(5,'5.1.1','Number of students benefitted by scholarships and freeships provided by the  Government during the year','2022-23'),(5,'5.1.2','Number of students benefitted by scholarships and freeships provided by the  institution and non-government agencies during the year','2022-23'),(5,'5.1.3','The following Capacity Development and Skill Enhancement activities are  organised for improving students’ capabilities','2022-23'),(5,'5.1.3(A)','Soft Skill, Language and Communication','2022-23'),(5,'5.1.3(B)','Yoga Class','2022-23'),(5,'5.1.3(C)','Awareness of Trends in technology','2022-23'),(5,'5.1.4','Number of students benefitted from guidance/coaching for competitive  examinations and career counselling offered by the institution during the year','2022-23'),(5,'5.1.5','The institution adopts the following mechanism for redressal of students’  grievances, including sexual harassment and ragging','2022-23'),(5,'5.2.1','Number of outgoing students who got placement during the year','2022-23'),(5,'5.2.2','Number of outgoing students progressing to higher education during the year','2022-23'),(5,'5.2.3','Number of students qualifying in state/ national/ international level examinations  during the year','2022-23'),(5,'5.3.1','Number of awards/medals for outstanding performance in sports and/or cultural  activities at inter-university / state /national / international events (award for a team  event should be counted as one) during the year','2022-23'),(5,'5.3.2','Presence of an active Student Council and representation of students in academic  and administrative bodies/committees of the institution','2022-23'),(5,'5.3.3','Number of sports and cultural events / competitions organised by the institution','2022-23'),(5,'5.4.1','The Alumni Association and its Chapters (registered and functional) contribute  significantly to the development of the institution through financial and other  support services','2022-23'),(5,'5.4.2','Alumni’s financial contribution during the year','2022-23'),(6,'6.1.1','The governance of the institution is reflective of an effective leadership in tune with  the vision and mission of the Institution','2022-23'),(6,'6.1.1(A)','Academic Monitoring Committee meeting minutes','2022-23'),(6,'6.1.1(B)','Placement Committee meeting minutes','2022-23'),(6,'6.1.1(C)','SAC - coordinators meeting minutes','2022-23'),(6,'6.1.1(D)','Anti Ragging Committee meeting minutes','2022-23'),(6,'6.1.1(E)','IQAC meeting minutes','2022-23'),(6,'6.1.1(F)','Board of Studies Meeting minutes','2022-23'),(6,'6.1.1(G)','Academic Council meeting minutes','2022-23'),(6,'6.1.1(H)','Governing Council meeting minutes','2022-23'),(6,'6.1.1(I)','HOD/Academic Development Committee Meeting Minutes','2022-23'),(6,'6.1.1(J)','Finance Committee meeting minutes','2022-23'),(6,'6.1.1(K)','Library Committee meeting minutes','2022-23'),(6,'6.1.1(L)','Town Hall meeting minutes','2022-23'),(6,'6.1.2','Effective leadership is reflected in various institutional practices such as decentralization and participative management','2022-23'),(6,'6.1.2(A)','Strat-Plan','2022-23'),(6,'6.1.2(B)','Requisition for two set of mid question papers from CoE – e-mail proof','2022-23'),(6,'6.1.2(C)','Declaration of mid question paper set number from CoE– e-mail proof','2022-23'),(6,'6.1.2(D)','Uniform Evaluation','2022-23'),(6,'6.1.2(E)','Research Review meeting minutes','2022-23'),(6,'6.2.1','The institutional Strategic/ Perspective plan has been clearly articulated and  implemented','2022-23'),(6,'6.2.1(A)','Strat-Plan','2022-23'),(6,'6.2.1(B)','Merit scholarships based on AP-EAMCET rank','2022-23'),(6,'6.2.1(C)','Meritorious scholarships for students','2022-23'),(6,'6.2.1(D)','Details of the GATE training classes conducted; List of students attended GATE Coaching & secured score','2022-23'),(6,'6.2.1(E)','Details of the CRT programs/ Technical training/Competitions conducted (Total number of hours), the List of the students attended the training programs & Proof of attendance, Branch wise list of the students placed, and the number of companies visited. List of students attended the WTN.','2022-23'),(6,'6.2.1(F)','Motivational and inspirational talks by the industry experts','2022-23'),(6,'6.2.1(G)','Social media updates','2022-23'),(6,'6.2.2','The functioning of the various institutional bodies is effective and efficient as visible  from the policies, administrative set-up, appointment and service rules, procedures,  etc. ','2022-23'),(6,'6.2.2(A)','Organogram','2022-23'),(6,'6.2.2(B)','HR & Service rules/Incentive policies','2022-23'),(6,'6.2.2(D)','Frequency and conduct of the meetings of governance committees (GC, AC, BOS, FC)','2022-23'),(6,'6.2.2(E)','SOP for procurement (AOP, MRN, Comparative statements and Purchase Orders)','2022-23'),(6,'6.2.3','Implementation of e-governance in areas of operation: 1. Administration 2. Finance and Accounts 3. Student Admission and Support 4. Examination','2022-23'),(6,'6.3.1','The institution has effective welfare measures for teaching and non-teaching staff  and avenues for their career development/ progression','2022-23'),(6,'6.3.1(A)','HR Policies (Welfare & Career development)','2022-23'),(6,'6.3.1(B)','Details of the training programs conducted for teaching & non-teaching staff','2022-23'),(6,'6.3.1(C)','Details of the staff (Teaching & Non-teaching promoted)','2022-23'),(6,'6.3.1(D)','Details of on campus housing, Term insurance, Medical insurance, Children education, ESI, Cooperative credit society, Concessions in IP/OP services and Gratuity','2022-23'),(6,'6.3.1(E)','Details of the faculty received incentives for completion of Ph.D./ QIP','2022-23'),(6,'6.3.2','Number of teachers provided with financial support to attend conferences /  workshops and towards payment of membership fee of professional bodies during the  year','2022-23'),(6,'6.3.2(A)','Number of teachers provided with financial support to attend conferences / workshops and towards payment of membership fee of professional bodies during the year','2022-23'),(6,'6.3.3','Number of professional development / administrative training programmes  organized by the Institution for its teaching and non-teaching staff during the year','2022-23'),(6,'6.3.3(A)','Annual Reports highlighting training programs conducted for teaching & non teaching','2022-23'),(6,'6.3.3(B)','Training programs conducted for teaching & non-teaching','2022-23'),(6,'6.3.4','Number of teachers who have undergone online/ face-to-face Faculty Development  Programmes during the year: (Professional Development Programmes, Orientation / Induction Programmes,  Refresher Courses, Short-Term Course, etc.)','2022-23'),(6,'6.3.4(A)','List of Faculty FDPS Attended & Proofs','2022-23'),(6,'6.4.1','Institution conducts internal and external financial audits regularly','2022-23'),(6,'6.4.2','Funds / Grants received from non-government bodies, individuals, and  philanthropists during the year (not covered in Criterion III and V) (INR in lakhs)','2022-23'),(6,'6.4.3','Institutional strategies for mobilisation of funds and the optimal utilisation of  resources','2022-23'),(6,'6.5.1','Internal Quality Assurance Cell (IQAC) has contributed significantly for  institutionalizing quality assurance strategies and processes visible in terms of  incremental improvements made during the preceding year with regard to quality (in  case of the First Cycle): Incremental improvements made during the preceding year with regard to quality  and post-accreditation quality initiatives (Second and subsequent cycles)','2022-23'),(6,'6.5.1(A)','List of companies hosted Internship','2022-23'),(6,'6.5.1(B)','FADS – Supporting Docs','2022-23'),(6,'6.5.1(C)','Consolidated initiatives of IQAC (strategies & contributions of IQAC mentioned)','2022-23'),(6,'6.5.2','The institution reviews its teaching-learning process, structures and methodologies  of operation and learning outcomes at periodic intervals through its IQAC as per  norms','2022-23'),(6,'6.5.2(A)','List of the IQAC initiatives taken up to enhance the students’ performance (Course coordinator meetings, AMC meeting minutes, Remedial classes for slow learners)','2022-23'),(6,'6.5.2(B)','Details of the progression of the students from 1st to 8th semesters branch wise for 2020-21 for all the batches graduated – 1 Bar chart, in each bar chart with eight bars','2022-23'),(6,'6.5.2(C)','Structures & methodologies of operations ISO audits, Academic audits -Internal & External branch wise','2022-23'),(6,'6.5.2(D)','Internal & External Academic Audit Report','2022-23'),(6,'6.5.3','Quality assurance initiatives of the institution include','2022-23'),(6,'6.5.3(A)','IQAC meeting minutes','2022-23'),(6,'6.5.3(B)','Feedback system of the institution','2022-23'),(6,'6.5.3(C)','Feedback system for design and review of syllabus','2022-23'),(6,'6.5.3(D)','Collaborative quality initiatives','2022-23'),(6,'6.5.3(E)','Participation in NIRF','2022-23'),(6,'6.5.3(F)','NBA accreditation','2022-23'),(6,'6.5.3(G)','NAAC accreditation','2022-23'),(6,'6.5.3(H)','IQAC Feedback analysis','2022-23'),(7,'7.1.1','Measures initiated by the institution for the promotion of gender equity during the  year','2022-23'),(7,'7.1.1(A)','Action plan of WEC for gender sensitization','2022-23'),(7,'7.1.1(B)','Campus surveillance with CC TV (Audit report)','2022-23'),(7,'7.1.1(C)','Policy for women security and safety (PASH)','2022-23'),(7,'7.1.1(D)','Photographs of Exclusive reading rooms, waiting rooms and rest rooms','2022-23'),(7,'7.1.1(E)','Day care centre for the kids (Beneficiaries)','2022-23'),(7,'7.1.1(F)','Welfare measures (Maternity leave for two kids)','2022-23'),(7,'7.1.1(G)','List of the beneficiaries','2022-23'),(7,'7.1.10','The institution has a prescribed code of conduct for students, teachers, administrators  and other staff and conducts periodic sensitization programmes in this regard','2022-23'),(7,'7.1.10(A)','Details of the monitoring committee composition and minutes of the committee meeting, number of programmes organized, reports on the various programmes, etc. in support of the claims','2022-23'),(7,'7.1.10(B)','Policy document on code of ethics','2022-23'),(7,'7.1.10(C)','Circulars and geo tagged photographs and caption of the activities organized under the metric for teachers, students, administrators and other staffs','2022-23'),(7,'7.1.11','Institution celebrates / organizes national and international commemorative days,  events and festivals','2022-23'),(7,'7.1.11(A)','Annual report of the celebrations and commemorative events','2022-23'),(7,'7.1.11(B)','Photographs of some of the events','2022-23'),(7,'7.1.2','The Institution has facilities for alternate sources of energy and energy conservation ','2022-23'),(7,'7.1.2(A)','Geo tagged photographs with caption of the facilities','2022-23'),(7,'7.1.2(B)','Bills for the purchase of equipment for the facilities','2022-23'),(7,'7.1.2(C)','Permission document for connection to the grid from Government','2022-23'),(7,'7.1.3','Describe the facilities in the institution for the management of the following types of  degradable and non-degradable waste (within a maximum of 200 words)','2022-23'),(7,'7.1.3(A)','Geo tagged photographs of the facilities','2022-23'),(7,'7.1.3(B)','SOP for solid waste management','2022-23'),(7,'7.1.4','Water conservation facilities available in the institution','2022-23'),(7,'7.1.4(A)','Geo tagged photographs with caption of the facilities','2022-23'),(7,'7.1.4(B)','Bills for the purchase of equipment','2022-23'),(7,'7.1.5','Green campus initiatives include','2022-23'),(7,'7.1.5(A)','Policy document on the green campus','2022-23'),(7,'7.1.5(B)','Geo tagged photographs/Videos with caption of the facilities','2022-23'),(7,'7.1.5(C)','Circulars for the implementation of the initiatives and any other supporting document','2022-23'),(7,'7.1.6','Quality audits on environment and energy undertaken by the institution','2022-23'),(7,'7.1.6(A)','Policy document on environment and energy usage','2022-23'),(7,'7.1.6(B)','Certificate from the auditing agency','2022-23'),(7,'7.1.6(C)','Certificates of the awards received from the recognized agency','2022-23'),(7,'7.1.6(D)','Report on environmental promotional activities conducted beyond the campus','2022-23'),(7,'7.1.6(F)','Green audit report','2022-23'),(7,'7.1.7','The Institution has a Divyangjan-friendly and barrier-free environment','2022-23'),(7,'7.1.7(A)','Policy document and information brochure','2022-23'),(7,'7.1.7(B)','Geo tagged photos','2022-23'),(7,'7.1.7(C)','Bills and invoice/purchase order/AMC','2022-23'),(7,'7.1.7(D)','A rest room should include specific requirements of Divyangjan','2022-23'),(7,'7.1.7(E)','Bills for the software procured','2022-23'),(7,'7.1.8','Describe the Institutional efforts/initiatives in providing an inclusive environment i.e.  tolerance and harmony towards cultural, regional, linguistic, communal, socioeconomic and other diversities (within a maximum of 200 words)','2022-23'),(7,'7.1.8(A)','List of the outbound programs','2022-23'),(7,'7.1.8(B)','List of national level activities (Cultural, Sports, Academic, Cultural & Sports)','2022-23'),(7,'7.1.8(C)','List of the faculty & students coming from the out of state','2022-23'),(7,'7.1.9','Sensitization of students and employees of the institution to constitutional obligations:  values, rights, duties and responsibilities of citizens','2022-23'),(7,'7.1.9(A)','List of the activities/Events conducted, and the number of students present. (Awareness programs on Women safety & protection, Anti ragging, Judicial rights, Gender equality, Traffic rules, Environment protection, Conservation of natural resources (power & water)) >','2022-23'),(7,'7.2.1','Provide the weblink on the Institutional website regarding the Best practices as per  the prescribed format of NAAC','2022-23'),(7,'7.3.1','Institutional Distinctiveness','2022-23');
/*!40000 ALTER TABLE `criteria1` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `criteria2`
--

LOCK TABLES `criteria2` WRITE;
/*!40000 ALTER TABLE `criteria2` DISABLE KEYS */;
INSERT INTO `criteria2` VALUES (1,'1.1.1','Copies of PEOs, POs & PSOs for all programs','2022-23'),(1,'1.1.2','Number of Programmes where syllabus revision was carried out during the year(Data Requirement: Programme Code ,Names of the Programmes revised)','2022-23'),(1,'1.1.2(A)','List of programs where syllabus revision has been carried out signed by the Principal','2022-23'),(1,'1.1.2(B)','Approved Minutes of relevant Academic Council/BOS meetings highlighting the specific agenda item relevant to the metric year wise','2022-23'),(1,'1.1.3','Number of courses focusing on employability/entrepreneurship/ skill development  offered by the Institution during the year(Data Requirement: Name of the Course with Course Code ,Name of the Programme ,Activities which have a direct bearing on employability/ entrepreneurship/ skill  development)','2022-23'),(1,'1.1.3(A)','Syllabus copy of the courses highlighting Focus on employability/entrepreneurship/ skill development along with their course outcomes.','2022-23'),(1,'1.1.3(B)','Minutes of the Boards of Studies/ Academic Council meetings with approval for these courses.','2022-23'),(1,'1.1.3(C)','List of MoUs','2022-23'),(1,'1.2.1','Number of new courses introduced across all programmes offered during the year(Data Requirement: Name of the newly introduced course (s)  ,Name of the Programme)','2022-23'),(1,'1.2.1(A)','List of new courses introduced program-wise during the assessment period certified by the Principal.','2022-23'),(1,'1.2.1(B)','Minutes of relevant Academic Council/BOS meetings highlighting the name of the new courses introduced','2022-23'),(1,'1.2.2','Number of Programmes offered through Choice Based Credit System  (CBCS)/Elective Course System(Data Requirement: ,Names of all Programmes offered through CBCS ,Names of all Programmes offered through Elective Course System)','2022-23'),(1,'1.2.2(A)','List of programs in which CBCS/Elective course system implemented in the last completed academic year certified by the principal','2022-23'),(1,'1.2.2(B)','Structure of the program clearly indicating courses, credits/Electives and Minutes of relevant Academic Council/BOS meetings highlighting the relevant documents to this metric','2022-23'),(1,'1.3.1','List and Description of courses relevant to Professional Ethics, Gender diversity and equality, Human Values, Environment and Sustainability, Women Empowerment introduced in the Curriculum along with the syllabus should be available in all the departments','2022-23'),(1,'1.3.2','Number of value-added courses for imparting transferable and life skills offered  during the year:(Data Requirement:  Names of the value-added courses (each with 30 or more contact hours) ,No. of times offered (for each value-added course) during the year ,Total number of students enrolled ,Total number of students completing the course during the year','2022-23'),(1,'1.3.2(A)','List of value-added courses which are optional and offered outside the curriculum of the programs with authorized sign.','2022-23'),(1,'1.3.2(B)','Brochure and Course content or syllabus along with course outcome of Value-added courses offered.','2022-23'),(1,'1.3.3','List of enrolled students for courses addressed in 1.3.2','2022-23'),(1,'1.3.4','List of students undertaking the field projects/ internships / student projects program-wise in the last completed academic year along with the details of title, place of work etc.','2022-23'),(1,'1.4.1','Sample Filled in feedback forms from the stakeholders to be provided.','2022-23'),(1,'1.4.2','Stakeholder feedback analysis report signed by the authority','2022-23'),(2,'2.1.1','Enrolment of Students ','2022-23'),(2,'2.1.1(A)','Students admitted(HEI signed documents)','2022-23'),(2,'2.1.1(B)','Enrolment Number_ AICTE letters of sanctioned intake','2022-23'),(2,'2.1.1(C)','Enrolment Number _ Ratified list of admitted students','2022-23'),(2,'2.1.2','Number of seats filled against reserved categories (SC, ST, OBC, Divyangjan,  etc.) as per the reservation policy during the year (exclusive of supernumerary  seats)','2022-23'),(2,'2.1.2(A)','Reservation seats categories to be considered as per the state rule (APSHE Guidelines)','2022-23'),(2,'2.1.2(B)','AP regulation for Admissions GO No. 73','2022-23'),(2,'2.1.2(C)','Guidelines for filling the left-over vacancies under reservation','2022-23'),(2,'2.1.2(D)','Category wise_ Ratified list of students admitted','2022-23'),(2,'2.1.2(E)','Admission Abstract(HEI signed documents)','2022-23'),(2,'2.2.1','The institution assesses students’ learning levels and organises special  programmes for both slow and advanced learners(Present a write-up within a maximum of 200 words)','2022-23'),(2,'2.2.1(A)','Video records of the specific topics(A document having the links and screen shots of video course/LCS)','2022-23'),(2,'2.2.1(B)','Classes for the slow learner(Time table conducted for CA failures and attendance sheets)','2022-23'),(2,'2.2.1(C)','Additional assignment(sample copies of either question papers or evaluated assignment)','2022-23'),(2,'2.2.1(D)','Make-up classes for course detention students/lateral entry students(Time tables and attendance sheets for each semester(1-8) at least one)','2022-23'),(2,'2.2.1(E)','Remedial classes conducted for end semester failures(Time tables, attendance sheets and track sheets for each semester(1-8) at least one)','2022-23'),(2,'2.2.1(F)','List of MOOCs courses(Details of student with course name and sample certificates)','2022-23'),(2,'2.2.1(G)','List of the advanced learners opted Minors and honors(Abstract certified by controller of examination)','2022-23'),(2,'2.2.1(H)','Student participation in technical events list with students event details and SAMPLE certificates','2022-23'),(2,'2.2.2','Student – Teacher (full-time) ratio(Data Requirement:  Total number of students in the institution ,Total number of full-time teachers in the institution)','2022-23'),(2,'2.2.2(A)','List of the full-time teachers','2022-23'),(2,'2.2.2(B)','Student intake','2022-23'),(2,'2.3.1','Student-centric methods such as experiential learning, participative learning and  problem-solving methodologies are used for enhancing learning experiences(Present a write-up within a maximum of 200 words.)','2022-23'),(2,'2.3.1(A)','Academic regulations and curriculum(Recent)','2022-23'),(2,'2.3.2','Teachers use ICT-enabled tools including online resources for effective teaching  and learning(Present a write-up within a maximum of 200 words.)','2022-23'),(2,'2.3.2 (A)','ICT Tools Gadgets viz. Graphic tablets, MST, Smart pen, Projector(Geotagged photos with title of photo)','2022-23'),(2,'2.3.3','Ratio of students to mentor for academic and other related issues','2022-23'),(2,'2.3.3(A)','Circular of mentor – mentees','2022-23'),(2,'2.3.3(B)','Mentor list as announced by the HEI','2022-23'),(2,'2.3.3(C)','issues raised and resolved in the mentor system(Total records of a student -&gt; one sample from each semester)','2022-23'),(2,'2.3.4','Preparation and adherence to Academic Calendar and Teaching Plans by the  institution(Describe the preparation of and adherence to the Academic Calendar and Teaching  Plans by the institution.  Present a write-up within a maximum of 200 words)','2022-23'),(2,'2.3.4(A)','Academic calendar','2022-23'),(2,'2.3.4(B)','Proof of course allotment for odd and even semesters','2022-23'),(2,'2.3.4(C)','Proof for lecture plan, Schedule and diary(one course from each semester)','2022-23'),(2,'2.3.4(D)','Minutes of AMC','2022-23'),(2,'2.4.1','Number of full-time teachers against sanctioned posts during the year(Data Requirement:  Number of full-time teachers ,Number of sanctioned posts)','2022-23'),(2,'2.4.1(A)','Sanction letter indicating number of posts','2022-23'),(2,'2.4.1(B)','Department wise List of full-time teachers appointed','2022-23'),(2,'2.4.2','Number of full-time teachers with PhD/ D.M. / M.Ch. / D.N.B Super-Specialty /  DSc / DLitt during the year(Data Requirement: List of full-time teachers with PhD/ D.M. / M.Ch. / D.N.B Super-Specialty /  DSc / DLitt.)','2022-23'),(2,'2.4.3','Total teaching experience of full-time teachers in the same institution(Data Requirement:  Name and number of full-time teachers and their years of teaching experience in the institution)','2022-23'),(2,'2.5.1','Number of days from the date of last semester-end/ year- end examination till the  declaration of results during the year(Number of days from the date of last semester-end / year-end examination till the  declaration of results year-wise during the year)','2022-23'),(2,'2.5.1(A)','Examination Result Notifications','2022-23'),(2,'2.5.1(B)','List of Programs offered and the last date of the latest semester end exams and date of result declaration','2022-23'),(2,'2.5.2','Number of students’ complaints/grievances against evaluation against the total  number who appeared in the examinations during the year','2022-23'),(2,'2.5.2(A)','Number of complaints','2022-23'),(2,'2.5.2(B)','Minutes of the examination Committee','2022-23'),(2,'2.5.3','IT integration and reforms in the examination procedures and processes  including Continuous Internal Assessment (CIA) have brought in considerable  improvement in the Examination Management System (EMS) of the Institution(Describe the examination reforms with reference to the following within a minimum  of 200 words :Examination procedures ,Processes/Procedures integrating IT ,Continuous Internal Assessment System) ','2022-23'),(2,'2.5.3(A)','Examination Regulations','2022-23'),(2,'2.5.3(B)','Question Paper templates','2022-23'),(2,'2.5.3(C)','Proof of the hybrid grading sheet','2022-23'),(2,'2.5.3(D)','Examination Results link in the website','2022-23'),(2,'2.5.3(E)','Proof of the OMR','2022-23'),(2,'2.6.1','Programme Outcomes and Course Outcomes for all Programmes offered by the  institution are stated and displayed on the website and communicated to teachers  and students(Describe Course Outcomes (COs) for all courses and the mechanism of  communication to teachers and students within a maximum of 200 words.  Upload COs for all Courses) ','2022-23'),(2,'2.6.1(A)','List of POs and COs(COs for at least two courses from each semester)','2022-23'),(2,'2.6.1(B)','Photo gallery of the COs & POs displayed Program wise','2022-23'),(2,'2.6.2','Attainment of Programme Outcomes and Course Outcomes as evaluated by the  institution(Describe the method of measuring the attainment of POs, PSOs and COs and the  level of attaiment of POs , PSOs and COs in not more than 200 words.)','2022-23'),(2,'2.6.2(A)','CO and PO attainment','2022-23'),(2,'2.6.2(B)','Files related to all surveys for the indirect assessment','2022-23'),(2,'2.6.3','Pass Percentage of students','2022-23'),(2,'2.6.3(A)','Annual report of CoE indicating the pass percentage','2022-23'),(2,'2.6.3(B)','Certified report from CoE indicating Students eligible for the degree program','2022-23'),(2,'2.7.1','Student Satisfaction Survey (SSS) on overall institutional performance  (Institution may design its own questionnaire). Results and details need to be  provided as a weblink','2022-23'),(3,'3.1.1','The institution’s research facilities are frequently updated and there is a welldefined policy for promotion of research which is uploaded on the institutional  website and implemented(Present a write-up within a maximum of 200 words.)','2022-23'),(3,'3.1.1(A)','List of research equipment along with the proof of purchases (Bill copies)','2022-23'),(3,'3.1.1(B)','Policy for Faculty Assessment and Development Scheme','2022-23'),(3,'3.1.1(C)','Financial Incentives for attending conferences, seminars and QIP','2022-23'),(3,'3.1.1(D)','Sanction letters for the funded research projects & UCs for the projects completed','2022-23'),(3,'3.1.1(E)','Copies of all MoUs for collaborative research','2022-23'),(3,'3.1.1(F)','Minutes of the governing council meeting-reflecting the research promotions','2022-23'),(3,'3.1.2','Details of Seed money','2022-23'),(3,'3.1.3','Number of teachers who were awarded national / international fellowship(s) for  advanced studies/research during the year','2022-23'),(3,'3.2.1','Grants received from Government and Non-Governmental agencies for research  projects, endowments, Chairs during the year (INR in Lakhs)','2022-23'),(3,'3.2.1(A)','List of Grants received for research projects','2022-23'),(3,'3.2.1(B)','e-copies of grants sanctioned','2022-23'),(3,'3.2.2','List of teachers having research projects during the year','2022-23'),(3,'3.2.3','Number of teachers recognized as research guides','2022-23'),(3,'3.2.4','Number of departments having research projects','2022-23'),(3,'3.2.4(A)','Web Links to Funding Agencies','2022-23'),(3,'3.3.1','Institution has created an ecosystem for innovations and creation and transfer of  knowledge supported by dedicated centres for research, entrepreneurship,  community orientation, incubation, etc.(Present a write-up within a maximum of 200 words)','2022-23'),(3,'3.3.1(A)','MSME business incubation center – sanction letter','2022-23'),(3,'3.3.1(B)','AICTE sponsored EDC – Sanction letters and list of the activities conducted by EDC','2022-23'),(3,'3.3.1(C)','Dedicated centres for research','2022-23'),(3,'3.3.2','Detailed report including photos, resource persons etc.','2022-23'),(3,'3.4.1','The Institution ensures implementation of its Code of Ethics for Research  uploaded in the website through the following:  1. Research Advisory Committee 2. Ethics Committee 3. Inclusion of Research Ethics in the research methodology course work  4. Plagiarism check through authenticated software Options: A. All of the above B. Any 3 of the above C. Any 2 of the above D. Any 1 of the above E. None of the above','2022-23'),(3,'3.4.1(A)','Research Advisory Committee','2022-23'),(3,'3.4.1(B)','Ethics Committee','2022-23'),(3,'3.4.1(C)','Inclusion of Research Ethics in the research methodology course work','2022-23'),(3,'3.4.1(D)','Anti Plagiarism software approved by JNTU','2022-23'),(3,'3.4.2','Number of PhD candidates registered per teacher (as per the data given with  regard to recognized PhD guides/ supervisors provided in Metric No. 3.2.3) during  the year','2022-23'),(3,'3.4.2(A)','List of Faculty along with the names of Research scholars','2022-23'),(3,'3.4.2(B)','Copy of the Registration letters/Joining letters','2022-23'),(3,'3.4.3','Number of research papers per teacher in CARE Journals notified on UGC  website during the year','2022-23'),(3,'3.4.4','Number of books and chapters in edited volumes / books published per teacher  during the year','2022-23'),(3,'3.4.5','Bibliometrics of the publications based on average Citation Index','2022-23'),(3,'3.4.6','Bibliometrics of the publication-based h-Index of the University','2022-23'),(3,'3.5.1','Audited statements for Revenue generated from consultancy','2022-23'),(3,'3.5.2','Total amount spent on developing facilities, training teachers and clerical/project  staff for undertaking consultancy during the year','2022-23'),(3,'3.6.1','Extension activities carried out in the neighbourhood sensitising students to social  issues for their holistic development, and the impact thereof during the year','2022-23'),(3,'3.6.1(A)','List of NSS activities conducted year wise and number of students participated','2022-23'),(3,'3.6.1(B)','Covid-19 booster report','2022-23'),(3,'3.6.1(C)','MGNCRE Reports','2022-23'),(3,'3.6.1(D)','Community Radio report','2022-23'),(3,'3.6.1(E)','List of NCC activities conducted year wise and number of students participated','2022-23'),(3,'3.6.2','Number of awards and recognition received by the Institution, its teachers and  students for extension activities from Government / Government-recognised bodies','2022-23'),(3,'3.6.3','Number of extension and outreach programmes conducted by the institution through  NSS/NCC during the year','2022-23'),(3,'3.6.4','Number of students participating in extension activities listed in 3.6.3 during the  year','2022-23'),(3,'3.7.1','Number of collaborative activities during the year for research/ faculty exchange/  student exchange/ internship/ on-the-job training/ project work','2022-23'),(3,'3.7.2','Number of functional MoUs with institutions of national and/or international  importance, other universities, industries, corporate houses, etc. during the year  (only functional MoUs with ongoing activities to be considered)','2022-23'),(3,'3.7.2(A)','e-copies of functional MoUs','2022-23'),(3,'3.7.2(B)','e-copies of Activities of MOUs','2022-23'),(4,'4.1.1','The Institution has adequate infrastructure and physical facilities for teachinglearning, viz., classrooms, laboratories, computing equipments, etc.','2022-23'),(4,'4.1.1(A)','Details of the Classrooms, Labs & Other facilities across all the six blocks','2022-23'),(4,'4.1.1(B)','List of the laboratories with titles','2022-23'),(4,'4.1.1(C)','Campus LAN diagram','2022-23'),(4,'4.1.1(D)','Proof of bandwidth','2022-23'),(4,'4.1.1(E)','List of the software','2022-23'),(4,'4.1.1(F)','Photo gallery of all the academic blocks, classrooms, drawing hall, seminar hall, auditorium, and Labs','2022-23'),(4,'4.1.2','The institution has adequate facilities for cultural activities, yoga, sports and games  (indoor and outdoor) including gymnasium, yoga centre, auditorium etc.)','2022-23'),(4,'4.1.2(A)','Colleague of Geo-tagged pictures','2022-23'),(4,'4.1.2(B)','Area details of the all the facilities','2022-23'),(4,'4.1.2(C)','Photo gallery of the various activities','2022-23'),(4,'4.1.3','Number of classrooms and seminar halls with ICT-enabled facilities','2022-23'),(4,'4.1.3(A)','Geo-tagged photographs of classrooms with ICT enabled facilities','2022-23'),(4,'4.1.3(B)','Class Timetables','2022-23'),(4,'4.1.4','Expenditure for infrastructure augmentation, excluding salary, during the year (INR  in Lakhs)','2022-23'),(4,'4.1.4(A)','Budget allocation','2022-23'),(4,'4.1.4(B)','Provide the consolidated fund allocation towards infrastructure augmentation facilities duly certified by Head of the Institution','2022-23'),(4,'4.2.1','Library is automated using Integrated Library Management System (ILMS)','2022-23'),(4,'4.2.1(A)','Library operates through LIBSYS','2022-23'),(4,'4.2.1(B)','All the books are provided with RF Id security tags','2022-23'),(4,'4.2.1(C)','Copy of the latest License agreement of LIBSYS-7','2022-23'),(4,'4.2.1(D)','Digital Library','2022-23'),(4,'4.2.2','Institution has access to the following','2022-23'),(4,'4.2.2 (D)','Specific details in respect of e-resources selected.','2022-23'),(4,'4.2.2 (E)','Databases','2022-23'),(4,'4.2.2(A)','Details of subscriptions of e-journals','2022-23'),(4,'4.2.2(B)','Letter of subscription','2022-23'),(4,'4.2.2(C)','Screenshots of the facilities claimed with the name of HEI.','2022-23'),(4,'4.2.3','Expenditure on purchase of books/ e-books and subscription to journals/e-journals  during the year (INR in lakhs)','2022-23'),(4,'4.2.3(A)','Consolidated extract of expenditure','2022-23'),(4,'4.2.3(B)','Invoices of all the expenditure','2022-23'),(4,'4.2.4','Usage of library by teachers and students (footfalls and login data for online access)','2022-23'),(4,'4.2.4(A)','Certified e-copy of the ledger for footfalls for 5days','2022-23'),(4,'4.2.4(B)','Certified screenshots of the data for the same 5 days for online access','2022-23'),(4,'4.2.4(C)','Last page of accession register details','2022-23'),(4,'4.3.1','Institution has an IT policy covering Wi-Fi, cyber security, etc. and has allocated  budget for updating its IT facilities','2022-23'),(4,'4.3.1(A)','Colleague of Geo-tagged pictures','2022-23'),(4,'4.3.1(B)','Policy document','2022-23'),(4,'4.3.1(C)','Campus Wi-Fi/Network diagram','2022-23'),(4,'4.3.1(D)','Budget of the year 2020-21','2022-23'),(4,'4.3.2','Student - Computer ratio','2022-23'),(4,'4.3.2(A)','Computer Bills','2022-23'),(4,'4.3.2(B)','Student strength','2022-23'),(4,'4.3.3','Bandwidth of internet connection in the Institution and the number of students on  campus','2022-23'),(4,'4.3.3(A)','Details of available bandwidth of internet connection in the Institution','2022-23'),(4,'4.3.3(B)','Bills for any one month/one quarter.','2022-23'),(4,'4.3.3(C)','e-copy of document of agreement with the service provider.','2022-23'),(4,'4.3.4','Institution has facilities for e-content development','2022-23'),(4,'4.3.4(A)','Geo tagged photographs of Media Centre, Audio Visual Centre etc.,','2022-23'),(4,'4.3.4(B)','Purchase bills for Lecture Capturing System','2022-23'),(4,'4.3.4(C)','Audited income expenditure statement highlighting the relevant expenditure.','2022-23'),(4,'4.4.1','Expenditure incurred on maintenance of physical and academic support facilities, excluding salary component, during the year (INR in lakhs)','2022-23'),(4,'4.4.1(A)','Audited statements of accounts.','2022-23'),(4,'4.4.2','There are established systems and procedures for maintaining and utilizing  physical, academic and support facilities – classrooms, laboratory, library, sports  complex, computers, etc.','2022-23'),(4,'4.4.2(A)','Schedules of Library, Sport complex ','2022-23'),(4,'4.4.2(B)','Laboratory Timetables','2022-23'),(4,'4.4.2(C)','SOP for Laboratory utilization','2022-23'),(4,'4.4.2(D)','SOP for usage of general amenities','2022-23'),(4,'4.4.2(E)','Geo-tagged photos of GAMYA','2022-23'),(4,'4.4.2(F)','Maintenance schedules and AMC letters from Estate department','2022-23'),(5,'5.1.1','Number of students benefitted by scholarships and freeships provided by the  Government during the year','2022-23'),(5,'5.1.2','Number of students benefitted by scholarships and freeships provided by the  institution and non-government agencies during the year','2022-23'),(5,'5.1.3','The following Capacity Development and Skill Enhancement activities are  organised for improving students’ capabilities','2022-23'),(5,'5.1.3(A)','Soft Skill, Language and Communication','2022-23'),(5,'5.1.3(B)','Yoga Class','2022-23'),(5,'5.1.3(C)','Awareness of Trends in technology','2022-23'),(5,'5.1.4','Number of students benefitted from guidance/coaching for competitive  examinations and career counselling offered by the institution during the year','2022-23'),(5,'5.1.5','The institution adopts the following mechanism for redressal of students’  grievances, including sexual harassment and ragging','2022-23'),(5,'5.2.1','Number of outgoing students who got placement during the year','2022-23'),(5,'5.2.2','Number of outgoing students progressing to higher education during the year','2022-23'),(5,'5.2.3','Number of students qualifying in state/ national/ international level examinations  during the year','2022-23'),(5,'5.3.1','Number of awards/medals for outstanding performance in sports and/or cultural  activities at inter-university / state /national / international events (award for a team  event should be counted as one) during the year','2022-23'),(5,'5.3.2','Presence of an active Student Council and representation of students in academic  and administrative bodies/committees of the institution','2022-23'),(5,'5.3.3','Number of sports and cultural events / competitions organised by the institution','2022-23'),(5,'5.4.1','The Alumni Association and its Chapters (registered and functional) contribute  significantly to the development of the institution through financial and other  support services','2022-23'),(5,'5.4.2','Alumni’s financial contribution during the year','2022-23'),(6,'6.1.1','The governance of the institution is reflective of an effective leadership in tune with  the vision and mission of the Institution','2022-23'),(6,'6.1.1(A)','Academic Monitoring Committee meeting minutes','2022-23'),(6,'6.1.1(B)','Placement Committee meeting minutes','2022-23'),(6,'6.1.1(C)','SAC - coordinators meeting minutes','2022-23'),(6,'6.1.1(D)','Anti Ragging Committee meeting minutes','2022-23'),(6,'6.1.1(E)','IQAC meeting minutes','2022-23'),(6,'6.1.1(F)','Board of Studies Meeting minutes','2022-23'),(6,'6.1.1(G)','Academic Council meeting minutes','2022-23'),(6,'6.1.1(H)','Governing Council meeting minutes','2022-23'),(6,'6.1.1(I)','HOD/Academic Development Committee Meeting Minutes','2022-23'),(6,'6.1.1(J)','Finance Committee meeting minutes','2022-23'),(6,'6.1.1(K)','Library Committee meeting minutes','2022-23'),(6,'6.1.1(L)','Town Hall meeting minutes','2022-23'),(6,'6.1.2','Effective leadership is reflected in various institutional practices such as decentralization and participative management','2022-23'),(6,'6.1.2(A)','Strat-Plan','2022-23'),(6,'6.1.2(B)','Requisition for two set of mid question papers from CoE – e-mail proof','2022-23'),(6,'6.1.2(C)','Declaration of mid question paper set number from CoE– e-mail proof','2022-23'),(6,'6.1.2(D)','Uniform Evaluation','2022-23'),(6,'6.1.2(E)','Research Review meeting minutes','2022-23'),(6,'6.2.1','The institutional Strategic/ Perspective plan has been clearly articulated and  implemented','2022-23'),(6,'6.2.1(A)','Strat-Plan','2022-23'),(6,'6.2.1(B)','Merit scholarships based on AP-EAMCET rank','2022-23'),(6,'6.2.1(C)','Meritorious scholarships for students','2022-23'),(6,'6.2.1(D)','Details of the GATE training classes conducted; List of students attended GATE Coaching & secured score','2022-23'),(6,'6.2.1(E)','Details of the CRT programs/ Technical training/Competitions conducted (Total number of hours), the List of the students attended the training programs & Proof of attendance, Branch wise list of the students placed, and the number of companies visited. List of students attended the WTN.','2022-23'),(6,'6.2.1(F)','Motivational and inspirational talks by the industry experts','2022-23'),(6,'6.2.1(G)','Social media updates','2022-23'),(6,'6.2.2','The functioning of the various institutional bodies is effective and efficient as visible  from the policies, administrative set-up, appointment and service rules, procedures,  etc. ','2022-23'),(6,'6.2.2(A)','Organogram','2022-23'),(6,'6.2.2(B)','HR & Service rules/Incentive policies','2022-23'),(6,'6.2.2(D)','Frequency and conduct of the meetings of governance committees (GC, AC, BOS, FC)','2022-23'),(6,'6.2.2(E)','SOP for procurement (AOP, MRN, Comparative statements and Purchase Orders)','2022-23'),(6,'6.2.3','Implementation of e-governance in areas of operation: 1. Administration 2. Finance and Accounts 3. Student Admission and Support 4. Examination','2022-23'),(6,'6.3.1','The institution has effective welfare measures for teaching and non-teaching staff  and avenues for their career development/ progression','2022-23'),(6,'6.3.1(A)','HR Policies (Welfare & Career development)','2022-23'),(6,'6.3.1(B)','Details of the training programs conducted for teaching & non-teaching staff','2022-23'),(6,'6.3.1(C)','Details of the staff (Teaching & Non-teaching promoted)','2022-23'),(6,'6.3.1(D)','Details of on campus housing, Term insurance, Medical insurance, Children education, ESI, Cooperative credit society, Concessions in IP/OP services and Gratuity','2022-23'),(6,'6.3.1(E)','Details of the faculty received incentives for completion of Ph.D./ QIP','2022-23'),(6,'6.3.2','Number of teachers provided with financial support to attend conferences /  workshops and towards payment of membership fee of professional bodies during the  year','2022-23'),(6,'6.3.2(A)','Number of teachers provided with financial support to attend conferences / workshops and towards payment of membership fee of professional bodies during the year','2022-23'),(6,'6.3.3','Number of professional development / administrative training programmes  organized by the Institution for its teaching and non-teaching staff during the year','2022-23'),(6,'6.3.3(A)','Annual Reports highlighting training programs conducted for teaching & non teaching','2022-23'),(6,'6.3.3(B)','Training programs conducted for teaching & non-teaching','2022-23'),(6,'6.3.4','Number of teachers who have undergone online/ face-to-face Faculty Development  Programmes during the year: (Professional Development Programmes, Orientation / Induction Programmes,  Refresher Courses, Short-Term Course, etc.)','2022-23'),(6,'6.3.4(A)','List of Faculty FDPS Attended & Proofs','2022-23'),(6,'6.4.1','Institution conducts internal and external financial audits regularly','2022-23'),(6,'6.4.2','Funds / Grants received from non-government bodies, individuals, and  philanthropists during the year (not covered in Criterion III and V) (INR in lakhs)','2022-23'),(6,'6.4.3','Institutional strategies for mobilisation of funds and the optimal utilisation of  resources','2022-23'),(6,'6.5.1','Internal Quality Assurance Cell (IQAC) has contributed significantly for  institutionalizing quality assurance strategies and processes visible in terms of  incremental improvements made during the preceding year with regard to quality (in  case of the First Cycle): Incremental improvements made during the preceding year with regard to quality  and post-accreditation quality initiatives (Second and subsequent cycles)','2022-23'),(6,'6.5.1(A)','List of companies hosted Internship','2022-23'),(6,'6.5.1(B)','FADS – Supporting Docs','2022-23'),(6,'6.5.1(C)','Consolidated initiatives of IQAC (strategies & contributions of IQAC mentioned)','2022-23'),(6,'6.5.2','The institution reviews its teaching-learning process, structures and methodologies  of operation and learning outcomes at periodic intervals through its IQAC as per  norms','2022-23'),(6,'6.5.2(A)','List of the IQAC initiatives taken up to enhance the students’ performance (Course coordinator meetings, AMC meeting minutes, Remedial classes for slow learners)','2022-23'),(6,'6.5.2(B)','Details of the progression of the students from 1st to 8th semesters branch wise for 2020-21 for all the batches graduated – 1 Bar chart, in each bar chart with eight bars','2022-23'),(6,'6.5.2(C)','Structures & methodologies of operations ISO audits, Academic audits -Internal & External branch wise','2022-23'),(6,'6.5.2(D)','Internal & External Academic Audit Report','2022-23'),(6,'6.5.3','Quality assurance initiatives of the institution include','2022-23'),(6,'6.5.3(A)','IQAC meeting minutes','2022-23'),(6,'6.5.3(B)','Feedback system of the institution','2022-23'),(6,'6.5.3(C)','Feedback system for design and review of syllabus','2022-23'),(6,'6.5.3(D)','Collaborative quality initiatives','2022-23'),(6,'6.5.3(E)','Participation in NIRF','2022-23'),(6,'6.5.3(F)','NBA accreditation','2022-23'),(6,'6.5.3(G)','NAAC accreditation','2022-23'),(6,'6.5.3(H)','IQAC Feedback analysis','2022-23'),(7,'7.1.1','Measures initiated by the institution for the promotion of gender equity during the  year','2022-23'),(7,'7.1.1(A)','Action plan of WEC for gender sensitization','2022-23'),(7,'7.1.1(B)','Campus surveillance with CC TV (Audit report)','2022-23'),(7,'7.1.1(C)','Policy for women security and safety (PASH)','2022-23'),(7,'7.1.1(D)','Photographs of Exclusive reading rooms, waiting rooms and rest rooms','2022-23'),(7,'7.1.1(E)','Day care centre for the kids (Beneficiaries)','2022-23'),(7,'7.1.1(F)','Welfare measures (Maternity leave for two kids)','2022-23'),(7,'7.1.1(G)','List of the beneficiaries','2022-23'),(7,'7.1.10','The institution has a prescribed code of conduct for students, teachers, administrators  and other staff and conducts periodic sensitization programmes in this regard','2022-23'),(7,'7.1.10(A)','Details of the monitoring committee composition and minutes of the committee meeting, number of programmes organized, reports on the various programmes, etc. in support of the claims','2022-23'),(7,'7.1.10(B)','Policy document on code of ethics','2022-23'),(7,'7.1.10(C)','Circulars and geo tagged photographs and caption of the activities organized under the metric for teachers, students, administrators and other staffs','2022-23'),(7,'7.1.11','Institution celebrates / organizes national and international commemorative days,  events and festivals','2022-23'),(7,'7.1.11(A)','Annual report of the celebrations and commemorative events','2022-23'),(7,'7.1.11(B)','Photographs of some of the events','2022-23'),(7,'7.1.2','The Institution has facilities for alternate sources of energy and energy conservation ','2022-23'),(7,'7.1.2(A)','Geo tagged photographs with caption of the facilities','2022-23'),(7,'7.1.2(B)','Bills for the purchase of equipment for the facilities','2022-23'),(7,'7.1.2(C)','Permission document for connection to the grid from Government','2022-23'),(7,'7.1.3','Describe the facilities in the institution for the management of the following types of  degradable and non-degradable waste (within a maximum of 200 words)','2022-23'),(7,'7.1.3(A)','Geo tagged photographs of the facilities','2022-23'),(7,'7.1.3(B)','SOP for solid waste management','2022-23'),(7,'7.1.4','Water conservation facilities available in the institution','2022-23'),(7,'7.1.4(A)','Geo tagged photographs with caption of the facilities','2022-23'),(7,'7.1.4(B)','Bills for the purchase of equipment','2022-23'),(7,'7.1.5','Green campus initiatives include','2022-23'),(7,'7.1.5(A)','Policy document on the green campus','2022-23'),(7,'7.1.5(B)','Geo tagged photographs/Videos with caption of the facilities','2022-23'),(7,'7.1.5(C)','Circulars for the implementation of the initiatives and any other supporting document','2022-23'),(7,'7.1.6','Quality audits on environment and energy undertaken by the institution','2022-23'),(7,'7.1.6(A)','Policy document on environment and energy usage','2022-23'),(7,'7.1.6(B)','Certificate from the auditing agency','2022-23'),(7,'7.1.6(C)','Certificates of the awards received from the recognized agency','2022-23'),(7,'7.1.6(D)','Report on environmental promotional activities conducted beyond the campus','2022-23'),(7,'7.1.6(F)','Green audit report','2022-23'),(7,'7.1.7','The Institution has a Divyangjan-friendly and barrier-free environment','2022-23'),(7,'7.1.7(A)','Policy document and information brochure','2022-23'),(7,'7.1.7(B)','Geo tagged photos','2022-23'),(7,'7.1.7(C)','Bills and invoice/purchase order/AMC','2022-23'),(7,'7.1.7(D)','A rest room should include specific requirements of Divyangjan','2022-23'),(7,'7.1.7(E)','Bills for the software procured','2022-23'),(7,'7.1.8','Describe the Institutional efforts/initiatives in providing an inclusive environment i.e.  tolerance and harmony towards cultural, regional, linguistic, communal, socioeconomic and other diversities (within a maximum of 200 words)','2022-23'),(7,'7.1.8(A)','List of the outbound programs','2022-23'),(7,'7.1.8(B)','List of national level activities (Cultural, Sports, Academic, Cultural & Sports)','2022-23'),(7,'7.1.8(C)','List of the faculty & students coming from the out of state','2022-23'),(7,'7.1.9','Sensitization of students and employees of the institution to constitutional obligations:  values, rights, duties and responsibilities of citizens','2022-23'),(7,'7.1.9(A)','List of the activities/Events conducted, and the number of students present. (Awareness programs on Women safety & protection, Anti ragging, Judicial rights, Gender equality, Traffic rules, Environment protection, Conservation of natural resources (power & water)) >','2022-23'),(7,'7.2.1','Provide the weblink on the Institutional website regarding the Best practices as per  the prescribed format of NAAC','2022-23'),(7,'7.3.1','Institutional Distinctiveness','2022-23');
/*!40000 ALTER TABLE `criteria2` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dc_up_files`
--

LOCK TABLES `dc_up_files` WRITE;
/*!40000 ALTER TABLE `dc_up_files` DISABLE KEYS */;
/*!40000 ALTER TABLE `dc_up_files` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dept`
--

LOCK TABLES `dept` WRITE;
/*!40000 ALTER TABLE `dept` DISABLE KEYS */;
INSERT INTO `dept` VALUES (2,'AI_DS'),(3,'AI_ML'),(19,'Anti_Ragging'),(9,'BSH'),(8,'CIVIL'),(14,'Clubs'),(1,'CSE'),(5,'ECE'),(6,'EEE'),(16,'IIC'),(4,'IT'),(7,'MECH'),(10,'NAAC'),(11,'NBA'),(12,'NCC'),(15,'NSS'),(18,'PASH'),(20,'SAC'),(13,'Sports'),(17,'Women_Empowerment');
/*!40000 ALTER TABLE `dept` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dept_files`
--

LOCK TABLES `dept_files` WRITE;
/*!40000 ALTER TABLE `dept_files` DISABLE KEYS */;
/*!40000 ALTER TABLE `dept_files` ENABLE KEYS */;
UNLOCK TABLES;

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
  `action_type` enum('Submitted','Approved','Returned','Resubmitted','Rejected') NOT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`action_id`),
  KEY `document_id` (`document_id`),
  KEY `user_id` (`user_id`),
  KEY `role_id` (`role_id`),
  CONSTRAINT `document_actions_ibfk_1` FOREIGN KEY (`document_id`) REFERENCES `documents` (`document_id`),
  CONSTRAINT `document_actions_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  CONSTRAINT `document_actions_ibfk_3` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `document_actions`
--

LOCK TABLES `document_actions` WRITE;
/*!40000 ALTER TABLE `document_actions` DISABLE KEYS */;
/*!40000 ALTER TABLE `document_actions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `document_categories`
--

DROP TABLE IF EXISTS `document_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `document_categories` (
  `category_id` int(11) NOT NULL AUTO_INCREMENT,
  `category_name` varchar(100) NOT NULL,
  PRIMARY KEY (`category_id`),
  UNIQUE KEY `category_name` (`category_name`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `document_categories`
--

LOCK TABLES `document_categories` WRITE;
/*!40000 ALTER TABLE `document_categories` DISABLE KEYS */;
INSERT INTO `document_categories` VALUES (3,'Central Repository'),(2,'Department Repository'),(1,'Faculty Repository');
/*!40000 ALTER TABLE `document_categories` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `document_role_flow`
--

LOCK TABLES `document_role_flow` WRITE;
/*!40000 ALTER TABLE `document_role_flow` DISABLE KEYS */;
/*!40000 ALTER TABLE `document_role_flow` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `document_types`
--

DROP TABLE IF EXISTS `document_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `document_types` (
  `type_id` int(11) NOT NULL AUTO_INCREMENT,
  `type_name` varchar(100) NOT NULL,
  `category_id` int(11) NOT NULL,
  `is_public` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`type_id`),
  UNIQUE KEY `type_name` (`type_name`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `document_types_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `document_categories` (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `document_types`
--

LOCK TABLES `document_types` WRITE;
/*!40000 ALTER TABLE `document_types` DISABLE KEYS */;
INSERT INTO `document_types` VALUES (1,'FDPs Attended',1,1),(2,'FDPs Organized',1,1),(3,'Papers Published',1,1),(4,'Conferences Published',1,1),(5,'Patents',1,1),(6,'Admin Files',2,0),(7,'Faculty Files',2,0),(8,'Student Related Files',2,0),(9,'Exam Section Files',2,0),(10,'Academic Calendar',2,0),(11,'Student Activity Files',2,0),(12,'Department Meeting Minutes',2,0),(13,'AMC Meeting Minutes',2,0),(14,'Board Of Studies',2,0),(15,'NAAC Accreditation Docs',3,1),(16,'NBA Accreditation Docs',3,1),(17,'NCC Activity Reports',3,1),(18,'Sports & Events Reports',3,1),(19,'Clubs & Societies Reports',3,1),(20,'NSS Activity Reports',3,1),(21,'IIC Innovation Reports',3,1),(22,'Women Empowerment Cell Docs',3,1),(23,'Anti-Ragging Committee Reports',3,1),(24,'Student Affairs Council (SAC) Docs',3,1);
/*!40000 ALTER TABLE `document_types` ENABLE KEYS */;
UNLOCK TABLES;

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
  CONSTRAINT `document_versions_ibfk_1` FOREIGN KEY (`document_id`) REFERENCES `archive_documents` (`document_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `document_versions`
--

LOCK TABLES `document_versions` WRITE;
/*!40000 ALTER TABLE `document_versions` DISABLE KEYS */;
/*!40000 ALTER TABLE `document_versions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `documents`
--

DROP TABLE IF EXISTS `documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `documents` (
  `document_id` int(11) NOT NULL AUTO_INCREMENT,
  `type_id` int(11) NOT NULL,
  `dept_id` int(11) NOT NULL,
  `uploaded_by` int(11) NOT NULL,
  `academic_year_id` int(11) NOT NULL,
  `original_file_name` varchar(255) NOT NULL,
  `stored_file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `status` enum('Pending','Approved','Rejected') DEFAULT 'Pending',
  `current_role_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`document_id`),
  UNIQUE KEY `stored_file_name` (`stored_file_name`),
  KEY `academic_year_id` (`academic_year_id`),
  KEY `current_role_id` (`current_role_id`),
  KEY `type_id` (`type_id`),
  KEY `dept_id` (`dept_id`),
  KEY `uploaded_by` (`uploaded_by`),
  CONSTRAINT `documents_ibfk_1` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`academic_year_id`),
  CONSTRAINT `documents_ibfk_2` FOREIGN KEY (`current_role_id`) REFERENCES `roles` (`role_id`),
  CONSTRAINT `documents_ibfk_3` FOREIGN KEY (`type_id`) REFERENCES `document_types` (`type_id`),
  CONSTRAINT `documents_ibfk_4` FOREIGN KEY (`dept_id`) REFERENCES `dept` (`dept_id`),
  CONSTRAINT `documents_ibfk_5` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `documents`
--

LOCK TABLES `documents` WRITE;
/*!40000 ALTER TABLE `documents` DISABLE KEYS */;
/*!40000 ALTER TABLE `documents` ENABLE KEYS */;
UNLOCK TABLES;

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
  `mode` varchar(50) DEFAULT NULL,
  `funded_by` varchar(50) DEFAULT NULL,
  `external_funder_name` varchar(500) DEFAULT NULL,
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
  `photos_pdf` varchar(500) DEFAULT NULL,
  `submission_time` datetime NOT NULL,
  `year` varchar(255) NOT NULL,
  `status` varchar(50) DEFAULT 'Pending HOD',
  `rejection_reason` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fdps_org_tab`
--

LOCK TABLES `fdps_org_tab` WRITE;
/*!40000 ALTER TABLE `fdps_org_tab` DISABLE KEYS */;
/*!40000 ALTER TABLE `fdps_org_tab` ENABLE KEYS */;
UNLOCK TABLES;

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
  `brochure` varchar(500) DEFAULT NULL,
  `schedule` varchar(500) DEFAULT NULL,
  `certificate` varchar(200) NOT NULL,
  `submission_time` varchar(300) NOT NULL,
  `year` varchar(255) NOT NULL,
  `status` varchar(50) DEFAULT 'Pending HOD',
  `rejection_reason` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fdps_tab`
--

LOCK TABLES `fdps_tab` WRITE;
/*!40000 ALTER TABLE `fdps_tab` DISABLE KEYS */;
/*!40000 ALTER TABLE `fdps_tab` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=97 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `files`
--

LOCK TABLES `files` WRITE;
/*!40000 ALTER TABLE `files` DISABLE KEYS */;
/*!40000 ALTER TABLE `files` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `files5_1_1and2`
--

LOCK TABLES `files5_1_1and2` WRITE;
/*!40000 ALTER TABLE `files5_1_1and2` DISABLE KEYS */;
/*!40000 ALTER TABLE `files5_1_1and2` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `files5_1_3`
--

LOCK TABLES `files5_1_3` WRITE;
/*!40000 ALTER TABLE `files5_1_3` DISABLE KEYS */;
/*!40000 ALTER TABLE `files5_1_3` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `files5_1_4`
--

LOCK TABLES `files5_1_4` WRITE;
/*!40000 ALTER TABLE `files5_1_4` DISABLE KEYS */;
/*!40000 ALTER TABLE `files5_1_4` ENABLE KEYS */;
UNLOCK TABLES;

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
  `status` varchar(50) DEFAULT 'Pending HOD',
  `rejection_reason` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `files5_2_1`
--

LOCK TABLES `files5_2_1` WRITE;
/*!40000 ALTER TABLE `files5_2_1` DISABLE KEYS */;
/*!40000 ALTER TABLE `files5_2_1` ENABLE KEYS */;
UNLOCK TABLES;

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
  `status` varchar(50) DEFAULT 'Pending HOD',
  `rejection_reason` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `files5_2_2`
--

LOCK TABLES `files5_2_2` WRITE;
/*!40000 ALTER TABLE `files5_2_2` DISABLE KEYS */;
/*!40000 ALTER TABLE `files5_2_2` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `files5_2_3`
--

LOCK TABLES `files5_2_3` WRITE;
/*!40000 ALTER TABLE `files5_2_3` DISABLE KEYS */;
/*!40000 ALTER TABLE `files5_2_3` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `files5_3_1`
--

LOCK TABLES `files5_3_1` WRITE;
/*!40000 ALTER TABLE `files5_3_1` DISABLE KEYS */;
/*!40000 ALTER TABLE `files5_3_1` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `files5_3_3`
--

LOCK TABLES `files5_3_3` WRITE;
/*!40000 ALTER TABLE `files5_3_3` DISABLE KEYS */;
/*!40000 ALTER TABLE `files5_3_3` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=237 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `login_pg`
--

LOCK TABLES `login_pg` WRITE;
/*!40000 ALTER TABLE `login_pg` DISABLE KEYS */;
/*!40000 ALTER TABLE `login_pg` ENABLE KEYS */;
UNLOCK TABLES;

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
  `type` varchar(50) DEFAULT NULL,
  `date_of_issue` varchar(100) NOT NULL,
  `patent_file` varchar(400) NOT NULL,
  `submission_time` varchar(300) NOT NULL,
  `year` varchar(255) NOT NULL,
  `status` varchar(50) DEFAULT 'Pending HOD',
  `rejection_reason` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `patents_table`
--

LOCK TABLES `patents_table` WRITE;
/*!40000 ALTER TABLE `patents_table` DISABLE KEYS */;
/*!40000 ALTER TABLE `patents_table` ENABLE KEYS */;
UNLOCK TABLES;

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
  `authors` text DEFAULT NULL,
  `paper_title` varchar(200) NOT NULL,
  `journal_name` varchar(200) NOT NULL,
  `issn_no` varchar(100) DEFAULT NULL,
  `volume_no` varchar(50) DEFAULT NULL,
  `issue_no` varchar(50) DEFAULT NULL,
  `page_no` varchar(50) DEFAULT NULL,
  `doi` varchar(255) DEFAULT NULL,
  `indexing` varchar(100) NOT NULL,
  `date_of_submission` date NOT NULL,
  `quality_factor` decimal(10,0) NOT NULL,
  `impact_factor` decimal(10,0) NOT NULL,
  `jcr_quartile` varchar(10) DEFAULT NULL,
  `scopus_quartile` varchar(10) DEFAULT NULL,
  `publication_link` varchar(500) DEFAULT NULL,
  `payment` varchar(200) NOT NULL,
  `submission_time` varchar(300) NOT NULL,
  `paper_file` varchar(400) NOT NULL,
  `year` varchar(255) NOT NULL,
  `status` varchar(50) DEFAULT 'Pending HOD',
  `rejection_reason` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `published_tab`
--

LOCK TABLES `published_tab` WRITE;
/*!40000 ALTER TABLE `published_tab` DISABLE KEYS */;
/*!40000 ALTER TABLE `published_tab` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `reg_central_cord`
--

LOCK TABLES `reg_central_cord` WRITE;
/*!40000 ALTER TABLE `reg_central_cord` DISABLE KEYS */;
INSERT INTO `reg_central_cord` VALUES ('central_cord','123',NULL),('central_cord1','123',NULL),('central_cord2','123',NULL),('central_cord3','123',NULL),('central_cord4','123',NULL),('central_cord5','123',NULL),('central_cord6','123',NULL),('central_cord7','123',NULL),('central_cord8','123',NULL),('central_cord9','123',NULL),('central_cord10','123',NULL);
/*!40000 ALTER TABLE `reg_central_cord` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `reg_cri_cord`
--

LOCK TABLES `reg_cri_cord` WRITE;
/*!40000 ALTER TABLE `reg_cri_cord` DISABLE KEYS */;
INSERT INTO `reg_cri_cord` VALUES (1,'cri_cord','123',NULL),(2,'cri_cord1','123',NULL),(3,'cri_cord2','123',NULL),(4,'cri_cord3','123',NULL),(5,'cri_cord4','123',NULL),(6,'cri_cord5','123',NULL),(7,'cri_cord6','123',NULL),(8,'cri_cord7','123',NULL),(9,'cri_cord8','123',NULL),(10,'cri_cord9','123',NULL),(11,'cri_cord10','123',NULL);
/*!40000 ALTER TABLE `reg_cri_cord` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `reg_dept_cord`
--

LOCK TABLES `reg_dept_cord` WRITE;
/*!40000 ALTER TABLE `reg_dept_cord` DISABLE KEYS */;
INSERT INTO `reg_dept_cord` VALUES ('dept_cord1','123','CSE',NULL),('dept_cord2','123','CSE-AI&ML',NULL),('dept_cord3','123','CSE-AI&DS',NULL),('dept_cord4','123','IT',NULL),('dept_cord5','123','ECE',NULL),('dept_cord6','123','EEE',NULL),('dept_cord7','123','MECH',NULL),('dept_cord8','123','CIVIL',NULL),('dept_cord9','123','BSH',NULL);
/*!40000 ALTER TABLE `reg_dept_cord` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `reg_hod`
--

LOCK TABLES `reg_hod` WRITE;
/*!40000 ALTER TABLE `reg_hod` DISABLE KEYS */;
INSERT INTO `reg_hod` VALUES (1,'cse-hod','123','CSE',NULL),(2,'aiml-hod','123','CSE-AI&ML',NULL),(3,'aids-hod','123','CSE-AI&DS',NULL),(4,'ece-hod','123','ECE',NULL),(5,'eee-hod','123','EEE',NULL),(6,'mech-hod','123','MECH',NULL),(7,'civil-hod','123','CIVIL',NULL),(8,'it-hod','123','IT',NULL);
/*!40000 ALTER TABLE `reg_hod` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `reg_jr_assistant`
--

LOCK TABLES `reg_jr_assistant` WRITE;
/*!40000 ALTER TABLE `reg_jr_assistant` DISABLE KEYS */;
/*!40000 ALTER TABLE `reg_jr_assistant` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `reg_pg`
--

LOCK TABLES `reg_pg` WRITE;
/*!40000 ALTER TABLE `reg_pg` DISABLE KEYS */;
INSERT INTO `reg_pg` VALUES ('kkm','1234','aa@gmail.com','123456'),('c','c','c1@gmail.com','123'),('chandu','chandu123','chandu@gmail.com','123'),('kalyan','k123','kalyan@gmail.com','123'),('keshav','keshav','keshav123@gmail.com','123'),('ram','r123','ram1@gmail.com','123'),('ram','ram1','ram@gmai.com','123'),('ram','ram12','ram12@gmail.com','123'),('sriram','ram123','sek@gmail.colm','123'),('ram','ram143','ram143@gmail.com','123'),('chandu','sekh123','sekhar@gmail.com','123'),('vinnu','vinnu123','v@gmail.com','123');
/*!40000 ALTER TABLE `reg_pg` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reg_tab`
--

LOCK TABLES `reg_tab` WRITE;
/*!40000 ALTER TABLE `reg_tab` DISABLE KEYS */;
INSERT INTO `reg_tab` VALUES (1,'chandu','professor','B.Tech','CSE','12345','2005-07-07','Male','2-72 first street gollagandi sompeta mandal srikakulam dist Andhra Pradesh','chandu@gmail.com','123456789781','ABCDE1234Z','chandu@gmail.com','123','6281865140','1.2years at Aitam as assistance professor','uploads/reg_tab/M.png','2022-07-07','uploads/reg_tab/m.pdf','uploads/reg_tab/8.pdf','2025-09-22 08:35:09'),(2,'Demo cse','Faculty','B.Tech','CSE','1234567891','2000-01-01','Male','Rajam','gowtham.lite@gmail.com','123123123123','ABCDE1234F','gowtham.lite@gmail.com','123','1234567890','0','uploads/reg_tab/e.png','2024-01-01','uploads/reg_tab/e.pdf','uploads/reg_tab/e.pdf','2026-02-10 05:13:49');
/*!40000 ALTER TABLE `reg_tab` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `rejection_history`
--

LOCK TABLES `rejection_history` WRITE;
/*!40000 ALTER TABLE `rejection_history` DISABLE KEYS */;
/*!40000 ALTER TABLE `rejection_history` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `role_flow_logs`
--

LOCK TABLES `role_flow_logs` WRITE;
/*!40000 ALTER TABLE `role_flow_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `role_flow_logs` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'Admin','System administrator'),(2,'IQAC','IQAC coordinator'),(3,'HOD','Head of Department'),(4,'Faculty','Faculty member'),(5,'Coordinator','Department/Cell coordinator'),(6,'Central_Coordinator','Central repository coordinator'),(7,'Junior_Assistant','Administrative assistant');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `s_bodies`
--

LOCK TABLES `s_bodies` WRITE;
/*!40000 ALTER TABLE `s_bodies` DISABLE KEYS */;
INSERT INTO `s_bodies` VALUES (1,'','','','CSI','event1','2025-01-14','2025-01-31','gmr','rajam','2nd','uploads/s_bodies/).jpg','chandru','05-01-2025 19:34:58','Pending HOD',NULL),(2,'chandu','2022-23','CSE','ISTE','radio auditions','2025-04-02','2025-04-30','gmr','rajam','Participated','uploads/s_bodies/6.pdf','kalyan','16-04-2025 20:04:41','Pending HOD',NULL),(3,'chandu','2022-23','AIML','ISTE','radio auditions','2025-04-02','2025-04-30','gmr','rajam','Participated','uploads/s_bodies/6.pdf','kalyan','16-04-2025 20:05:04','Pending HOD',NULL),(4,'chandu','2024-25','AIML','ISTE','radio auditions','2025-07-17','2025-07-02','gmr','rajam','Participated','uploads/s_bodies/k.pdf','chandu','27-07-2025 23:41:07','Pending HOD',NULL);
/*!40000 ALTER TABLE `s_bodies` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `s_conference_tab`
--

LOCK TABLES `s_conference_tab` WRITE;
/*!40000 ALTER TABLE `s_conference_tab` DISABLE KEYS */;
/*!40000 ALTER TABLE `s_conference_tab` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `s_events`
--

LOCK TABLES `s_events` WRITE;
/*!40000 ALTER TABLE `s_events` DISABLE KEYS */;
/*!40000 ALTER TABLE `s_events` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `s_journal_tab`
--

LOCK TABLES `s_journal_tab` WRITE;
/*!40000 ALTER TABLE `s_journal_tab` DISABLE KEYS */;
/*!40000 ALTER TABLE `s_journal_tab` ENABLE KEYS */;
UNLOCK TABLES;

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
  `dept_id` int(11) NOT NULL,
  PRIMARY KEY (`user_role_id`),
  UNIQUE KEY `user_id` (`user_id`,`role_id`,`dept_id`),
  KEY `role_id` (`role_id`),
  KEY `dept_id` (`dept_id`),
  CONSTRAINT `user_roles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  CONSTRAINT `user_roles_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`),
  CONSTRAINT `user_roles_ibfk_3` FOREIGN KEY (`dept_id`) REFERENCES `dept` (`dept_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_roles`
--

LOCK TABLES `user_roles` WRITE;
/*!40000 ALTER TABLE `user_roles` DISABLE KEYS */;
INSERT INTO `user_roles` VALUES (1,1,1,10),(2,2,4,2),(3,999,1,1);
/*!40000 ALTER TABLE `user_roles` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=1000 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Admin','admin@gmrit.edu','9876543210','admin123',NULL,'active','2026-07-29 14:55:39','2026-07-29 14:55:39',NULL),(2,'gowtham','gowtham.lite@gmail.com','6304779532','123','uploads/profiles/doc_6a6a171134c8d4.40223605.png','active','2026-07-29 15:06:57','2026-08-02 12:09:17','2026-08-02 12:09:17'),(999,'Test User','test@gmrit.edu.in',NULL,'password123',NULL,'active','2026-07-29 15:10:24','2026-07-29 15:10:24',NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-08-02 18:11:15
