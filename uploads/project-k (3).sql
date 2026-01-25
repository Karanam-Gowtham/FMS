-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 05, 2025 at 12:24 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `project-k`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_login`
--

CREATE TABLE `admin_login` (
  `Username` varchar(30) NOT NULL,
  `Password` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_login`
--

INSERT INTO `admin_login` (`Username`, `Password`) VALUES
('chandu', '123'),
('chandu', '123'),
('chandu', '123'),
('chandu', '123'),
('chandu', '123'),
('chandu', '123'),
('chandu', '123'),
('chandu', '123'),
('chandu', '123'),
('chandu', '123'),
('chandu', '123'),
('chandu', '123'),
('chandu', '123'),
('chandu', '123'),
('chandu', '123'),
('chandu', '123'),
('chandu', '123'),
('chandu', '123'),
('chandu', '123'),
('chandu', '123'),
('chandu', '123'),
('chandu', '123'),
('chandu', '123'),
('chandu', '123'),
('chandu', '123'),
('chandu', '123'),
('chandu', '123'),
('chandu', '123'),
('chandu', '123'),
('chandu', '123'),
('chandu', '123'),
('chandu', '123'),
('chandu', '123'),
('chandu', '123'),
('chandu', '123'),
('chandu', '123'),
('chandu', '123'),
('chandu', '123'),
('chandu', '123'),
('chandu', '123'),
('chandu', '123'),
('chandu', '123'),
('chandu', '123'),
('chandu', '123'),
('chandu', '123'),
('chandu', '123'),
('chandu', '123'),
('chandu', '123'),
('chandu', '123'),
('chandu', '123'),
('chandu', '123'),
('chandu', '123'),
('chandu', '123'),
('chandu', '123'),
('chandu', '123'),
('chandu', '123'),
('chandu', '123'),
('chandu', '123'),
('chandu', '123'),
('chandu', '123'),
('chandu', '123'),
('chandu', '123'),
('chandu', '123'),
('chandu', '123'),
('chandu', '123'),
('chandu', '123'),
('chandu', '123'),
('chandu', '123'),
('chandu', '123'),
('chandu', '123'),
('chandu', '123'),
('chandu', '123'),
('chandu', '123');

-- --------------------------------------------------------

--
-- Table structure for table `admin_reg`
--

CREATE TABLE `admin_reg` (
  `Username` varchar(30) NOT NULL,
  `Password` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_reg`
--

INSERT INTO `admin_reg` (`Username`, `Password`) VALUES
('chandu', '123');

-- --------------------------------------------------------

--
-- Table structure for table `a_files`
--

CREATE TABLE `a_files` (
  `id` int(255) NOT NULL,
  `username` varchar(200) NOT NULL,
  `academic_year` varchar(200) NOT NULL,
  `criteria` int(100) NOT NULL,
  `uploaded_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE current_timestamp(),
  `Faculty_name` varchar(200) NOT NULL,
  `file_name` varchar(200) NOT NULL,
  `file_path` varchar(300) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `a_files`
--

INSERT INTO `a_files` (`id`, `username`, `academic_year`, `criteria`, `uploaded_at`, `Faculty_name`, `file_name`, `file_path`) VALUES
(1, 'chandu', '2022-23', 6, '2024-09-24 18:30:29', 'chandu', 'cir-6', 'uploads1/hanuman.jpeg'),
(2, 'chandu', '2022-23', 2, '2024-09-24 19:28:01', 'ram', 'c2', 'uploads1/1224.png'),
(3, 'chandu', '2022-23', 6, '2024-09-26 14:48:43', 'kan', 'acm', 'uploads1/grm-web.png'),
(4, 'chandu', '2022-23', 1, '2024-10-04 20:06:39', 'kan', 'fi1', 'uploads1/lab handout.pdf'),
(5, 'chandu', '2022-23', 1, '2024-12-23 15:20:29', 'chandu', 'aakkk', 'uploads1/Screenshot (312).png');

-- --------------------------------------------------------

--
-- Table structure for table `central_files`
--

CREATE TABLE `central_files` (
  `event` varchar(200) NOT NULL,
  `file_name` varchar(200) NOT NULL,
  `file_path` varchar(400) NOT NULL,
  `uploaded_by` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `central_files`
--

INSERT INTO `central_files` (`event`, `file_name`, `file_path`, `uploaded_by`) VALUES
('NCC', 'kk', 'uploads/WhatsApp Image 2024-12-31 at 23.39.09_e25e823a.jpg', 'chandu;'),
('Sports', 'sportsfile', 'uploads/gmr.png', 'chandru'),
('Sports', 'file1', 'uploads/gmr_landing_page.jpg', 'chanduuu'),
('NCC', 'file1', 'uploads/gmr_landing_page.jpg', 'chandu');

-- --------------------------------------------------------

--
-- Table structure for table `conference_tab`
--

CREATE TABLE `conference_tab` (
  `id` int(255) NOT NULL,
  `username` varchar(100) NOT NULL,
  `branch` varchar(100) NOT NULL,
  `paper_title` varchar(300) NOT NULL,
  `from_date` varchar(200) NOT NULL,
  `to_date` varchar(200) NOT NULL,
  `organised_by` varchar(200) NOT NULL,
  `location` varchar(200) NOT NULL,
  `certificate_path` varchar(400) NOT NULL,
  `paper_type` varchar(200) NOT NULL,
  `paper_file_path` varchar(300) NOT NULL,
  `submission_time` varchar(300) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `conference_tab`
--

INSERT INTO `conference_tab` (`id`, `username`, `branch`, `paper_title`, `from_date`, `to_date`, `organised_by`, `location`, `certificate_path`, `paper_type`, `paper_file_path`, `submission_time`) VALUES
(2, 'chandu', 'AIML', 'paper1', '2025-01-01', '2025-01-31', 'gmr', 'rajam', 'uploads/gmr_landing_page (1).jpg', 'participated', '', '2025-01-04 14:35:37'),
(3, 'chandu', 'AIML', 'paper2', '2025-01-17', '2025-02-07', 'gmrit', 'rajam', 'uploads/cat.jpg', 'paper_publication', 'uploads/iit mandi.webp', '2025-01-04 19:02:07');

-- --------------------------------------------------------

--
-- Table structure for table `criteria`
--

CREATE TABLE `criteria` (
  `SI_no` int(10) NOT NULL,
  `Sub_no` varchar(30) NOT NULL,
  `Des` varchar(600) NOT NULL,
  `year` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `criteria`
--

INSERT INTO `criteria` (`SI_no`, `Sub_no`, `Des`, `year`) VALUES
(1, '1.1.1', 'Copies of PEOs, POs & PSOs for all programs', '2022-23'),
(1, '1.1.2(A)', 'List of programs where syllabus revision has been carried out signed by the Principal', '2022-23'),
(1, '1.1.2(B)', 'Approved Minutes of relevant Academic Council/BOS meetings highlighting the specific agenda item relevant to the metric year wise', '2022-23'),
(1, '1.1.3(A)', 'Syllabus copy of the courses highlighting Focus on employability/entrepreneurship/ skill development along with their course outcomes.', '2022-23'),
(1, '1.1.3(B)', 'Minutes of the Boards of Studies/ Academic Council meetings with approval for these courses.', '2022-23'),
(1, '1.1.3(C)', 'List of MoUs', '2022-23'),
(1, '1.2.1(A)', 'List of new courses introduced program-wise during the assessment period certified by the Principal.', '2022-23'),
(1, '1.2.1(B)', 'Minutes of relevant Academic Council/BOS meetings highlighting the name of the new courses introduced', '2022-23'),
(1, '1.2.2(A)', 'List of programs in which CBCS/Elective course system implemented in the last completed academic year certified by the principal', '2022-23'),
(1, '1.2.2(B)', 'Structure of the program clearly indicating courses, credits/Electives and Minutes of relevant Academic Council/BOS meetings highlighting the relevant documents to this metric', '2022-23'),
(1, '1.3.1', 'List and Description of courses relevant to Professional Ethics, Gender diversity and equality, Human Values, Environment and Sustainability, Women Empowerment introduced in the Curriculum along with the syllabus should be available in all the departments', '2022-23'),
(1, '1.3.2(A)', 'List of value-added courses which are optional and offered outside the curriculum of the programs with authorized sign.', '2022-23'),
(1, '1.3.2(B)', 'Brochure and Course content or syllabus along with course outcome of Value-added courses offered.', '2022-23'),
(1, '1.3.3', 'List of enrolled students for courses addressed in 1.3.2', '2022-23'),
(1, '1.3.4', 'List of students undertaking the field projects/ internships / student projects program-wise in the last completed academic year along with the details of title, place of work etc.', '2022-23'),
(1, '1.4.1', 'Sample Filled in feedback forms from the stakeholders to be provided.', '2022-23'),
(1, '1.4.2', 'Stakeholder feedback analysis report signed by the authority', '2022-23'),
(2, '2.1.1(A)', 'Students admitted', '2022-23'),
(2, '2.1.1(B)', 'Enrolment Number_ AICTE letters of sanctioned intake', '2022-23'),
(2, '2.1.1(C)', 'Enrolment Number _ Ratified list of admitted students', '2022-23'),
(2, '2.1.2(A)', 'Reservation seats categories to be considered as per the state rule (APSHE Guidelines)', '2022-23'),
(2, '2.1.2(B)', 'AP regulation for Admissions GO No. 73', '2022-23'),
(2, '2.1.2(C)', 'Guidelines for filling the left-over vacancies under reservation', '2022-23'),
(2, '2.1.2(D)', 'Category wise_ Ratified list of students admitted', '2022-23'),
(2, '2.1.2(E)', 'Admission Abstract', '2022-23'),
(2, '2.2.1(A)', 'Video records of the specific topics', '2022-23'),
(2, '2.2.1(B)', 'Classes for the slow learner', '2022-23'),
(2, '2.2.1(C)', 'Additional assignment', '2022-23'),
(2, '2.2.1(D)', 'List of the advanced learners opted MOOCs', '2022-23'),
(2, '2.2.1(E)', 'List of the advanced learners opted Minors and honors', '2022-23'),
(2, '2.2.1(F)', 'Make-up classes', '2022-23'),
(2, '2.2.1(G)', 'Remedial classes', '2022-23'),
(2, '2.2.1(H)', 'Student participation in technical events', '2022-23'),
(2, '2.2.2(A)', 'List of the full-time teachers', '2022-23'),
(2, '2.2.2(B)', 'Student intake', '2022-23'),
(2, '2.3.1(A)', 'Academic regulations and curriculum', '2022-23'),
(2, '2.3.2 (A)', 'ICT Tools Gadgets viz. Graphic tablets, MST, Smart pen, Projector,', '2022-23'),
(2, '2.3.3(A)', 'Circular of mentor – mentees', '2022-23'),
(2, '2.3.3(B)', 'Mentor list as announced by the HEI', '2022-23'),
(2, '2.3.3(C)', 'issues raised and resolved in the mentor system', '2022-23'),
(2, '2.3.4(A)', 'Academic calendar', '2022-23'),
(2, '2.3.4(B)', 'Proof of course allotment', '2022-23'),
(2, '2.3.4(C)', 'Proof for lecture plan, Schedule and diary', '2022-23'),
(2, '2.3.4(D)', 'Minutes of AMC', '2022-23'),
(2, '2.4.1(A)', 'Sanction letter indicating number of posts', '2022-23'),
(2, '2.4.1(B)', 'Department wise List of full-time teachers appointed', '2022-23'),
(2, '2.4.2', 'List of faculties having Ph.D', '2022-23'),
(2, '2.4.3', 'List of faculties along with particulars of the date of Appointment', '2022-23'),
(2, '2.5.1(A)', 'Examination Result Notifications', '2022-23'),
(2, '2.5.1(B)', 'List of Programs offered and the last date of the latest semester end exams and date of result declaration', '2022-23'),
(2, '2.5.2(A)', 'Number of complaints', '2022-23'),
(2, '2.5.2(B)', 'Minutes of the examination Committee', '2022-23'),
(2, '2.5.3(A)', 'Examination Regulations', '2022-23'),
(2, '2.5.3(B)', 'Question Paper templates', '2022-23'),
(2, '2.5.3(C)', 'Proof of the hybrid grading sheet', '2022-23'),
(2, '2.5.3(D)', 'Examination Results link in the website', '2022-23'),
(2, '2.5.3(E)', 'Proof of the OMR', '2022-23'),
(2, '2.6.1(A)', 'List of POs and COs', '2022-23'),
(2, '2.6.1(B)', 'Photo gallery of the COs & POs displayed Program wise', '2022-23'),
(2, '2.6.2(A)', 'CO and PO attainment', '2022-23'),
(2, '2.6.2(B)', 'Files related to all surveys for the indirect assessment', '2022-23'),
(2, '2.6.3(A)', 'Annual report of CoE', '2022-23'),
(2, '2.6.3(B)', 'Certified report from CoE indicating Students eligible for the degree program', '2022-23'),
(2, '2.7.1', 'Student satisfaction survey', '2022-23'),
(3, '3.1.1(A)', 'List of research equipment along with the proof of purchases (Bill copies)', '2022-23'),
(3, '3.1.1(B)', 'Policy for Faculty Assessment and Development Scheme', '2022-23'),
(3, '3.1.1(C)', 'Financial Incentives for attending conferences, seminars and QIP', '2022-23'),
(3, '3.1.1(D)', 'Sanction letters for the funded research projects & UCs for the projects completed', '2022-23'),
(3, '3.1.1(E)', 'Copies of all MoUs for collaborative research', '2022-23'),
(3, '3.1.1(F)', 'Minutes of the governing council meeting-reflecting the research promotions', '2022-23'),
(3, '3.1.2', 'Details of Seed money', '2022-23'),
(3, '3.1.3', 'Awards of teachers for research', '2022-23'),
(3, '3.2.1(A)', 'List of Grants received for research projects', '2022-23'),
(3, '3.2.1(B)', 'e-copies of grants sanctioned', '2022-23'),
(3, '3.2.2', 'List of teachers having research projects during the year', '2022-23'),
(3, '3.2.3', 'Number of teachers recognized as research guides', '2022-23'),
(3, '3.2.4', 'Number of departments having research projects', '2022-23'),
(3, '3.2.4(A)', 'Web Links to Funding Agencies', '2022-23'),
(3, '3.3.1(A)', 'MSME business incubation center – sanction letter', '2022-23'),
(3, '3.3.1(B)', 'AICTE sponsored EDC – Sanction letters and list of the activities conducted by EDC', '2022-23'),
(3, '3.3.1(C)', 'Dedicated centres for research', '2022-23'),
(3, '3.3.2', 'Detailed report including photos, resource persons etc.', '2022-23'),
(3, '3.4.1(A)', 'Research Advisory Committee', '2022-23'),
(3, '3.4.1(B)', 'Ethics Committee', '2022-23'),
(3, '3.4.1(C)', 'Inclusion of Research Ethics in the research methodology course work', '2022-23'),
(3, '3.4.1(D)', 'Anti Plagiarism software approved by JNTU', '2022-23'),
(3, '3.4.2(A)', 'List of Faculty along with the names of Research scholars', '2022-23'),
(3, '3.4.2(B)', 'Copy of the Registration letters/Joining letters', '2022-23'),
(3, '3.4.3', 'e-copies of publications', '2022-23'),
(3, '3.4.4', 'e-copies of books/book chapters', '2022-23'),
(3, '3.4.5', 'Bibliometrics of the publications based on average Citation Index', '2022-23'),
(3, '3.4.6', 'Bibliometrics of the publication-based h-Index of the University', '2022-23'),
(3, '3.5.1', 'Audited statements for Revenue generated from consultancy', '2022-23'),
(3, '3.5.2', 'Audited statements related to the expenditure on developing facilities/Training the staff', '2022-23'),
(3, '3.6.1(A)', 'List of NSS activities conducted year wise and number of students participated', '2022-23'),
(3, '3.6.1(B)', 'Covid-19 booster report', '2022-23'),
(3, '3.6.1(C)', 'MGNCRE Reports', '2022-23'),
(3, '3.6.1(D)', 'Community Radio report', '2022-23'),
(3, '3.6.1(E)', 'List of NCC activities conducted year wise and number of students participated', '2022-23'),
(3, '3.6.2', 'e-copies of awards and recognition for extension activities', '2022-23'),
(3, '3.6.3', 'Number of extension and outreach programmes conducted by the institution', '2022-23'),
(3, '3.6.4', 'Number of students participating in extension activities', '2022-23'),
(3, '3.7.1', 'e-copies of collaborative activities indicating nature of collaboration', '2022-23'),
(3, '3.7.2(A)', 'e-copies of functional MoUs', '2022-23'),
(3, '3.7.2(B)', 'e-copies of Activities of MOUs', '2022-23'),
(4, '4.1.1(A)', 'Details of the Classrooms, Labs & Other facilities across all the six blocks', '2022-23'),
(4, '4.1.1(B)', 'List of the laboratories with titles', '2022-23'),
(4, '4.1.1(C)', 'Campus LAN diagram', '2022-23'),
(4, '4.1.1(D)', 'Proof of bandwidth', '2022-23'),
(4, '4.1.1(E)', 'List of the software', '2022-23'),
(4, '4.1.1(F)', 'Photo gallery of all the academic blocks, classrooms, drawing hall, seminar hall, auditorium, and Labs', '2022-23'),
(4, '4.1.2(A)', 'Colleague of Geo-tagged pictures', '2022-23'),
(4, '4.1.2(B)', 'Area details of the all the facilities', '2022-23'),
(4, '4.1.2(C)', 'Photo gallery of the various activities', '2022-23'),
(4, '4.1.3(A)', 'Geo-tagged photographs of classrooms with ICT enabled facilities', '2022-23'),
(4, '4.1.3(B)', 'Class Timetables', '2022-23'),
(4, '4.1.4(A)', 'Budget allocation', '2022-23'),
(4, '4.1.4(B)', 'Provide the consolidated fund allocation towards infrastructure augmentation facilities duly certified by Head of the Institution', '2022-23'),
(4, '4.2.1(A)', 'Library operates through LIBSYS', '2022-23'),
(4, '4.2.1(B)', 'All the books are provided with RF Id security tags', '2022-23'),
(4, '4.2.1(C)', 'Copy of the latest License agreement of LIBSYS-7', '2022-23'),
(4, '4.2.1(D)', 'Digital Library', '2022-23'),
(4, '4.2.2 (D)', 'Specific details in respect of e-resources selected.', '2022-23'),
(4, '4.2.2 (E)', 'Databases', '2022-23'),
(4, '4.2.2(A)', 'Details of subscriptions of e-journals', '2022-23'),
(4, '4.2.2(B)', 'Letter of subscription', '2022-23'),
(4, '4.2.2(C)', 'Screenshots of the facilities claimed with the name of HEI.', '2022-23'),
(4, '4.2.3(A)', 'Consolidated extract of expenditure', '2022-23'),
(4, '4.2.3(B)', 'Invoices of all the expenditure', '2022-23'),
(4, '4.2.4(A)', 'Certified e-copy of the ledger for footfalls for 5days', '2022-23'),
(4, '4.2.4(B)', 'Certified screenshots of the data for the same 5 days for online access', '2022-23'),
(4, '4.2.4(C)', 'Last page of accession register details', '2022-23'),
(4, '4.3.1(A)', 'Colleague of Geo-tagged pictures', '2022-23'),
(4, '4.3.1(B)', 'Policy document', '2022-23'),
(4, '4.3.1(C)', 'Campus Wi-Fi/Network diagram', '2022-23'),
(4, '4.3.1(D)', 'Budget of the year 2020-21', '2022-23'),
(4, '4.3.2(A)', 'Computer Bills', '2022-23'),
(4, '4.3.2(B)', 'Student strength', '2022-23'),
(4, '4.3.3(A)', 'Details of available bandwidth of internet connection in the Institution', '2022-23'),
(4, '4.3.3(B)', 'Bills for any one month/one quarter.', '2022-23'),
(4, '4.3.3(C)', 'e-copy of document of agreement with the service provider.', '2022-23'),
(4, '4.3.4(A)', 'Geo tagged photographs of Media Centre, Audio Visual Centre etc.,', '2022-23'),
(4, '4.3.4(B)', 'Purchase bills for Lecture Capturing System', '2022-23'),
(4, '4.3.4(C)', 'Audited income expenditure statement highlighting the relevant expenditure.', '2022-23'),
(4, '4.4.1(A)', 'Audited statements of accounts.', '2022-23'),
(4, '4.4.2(A)', 'Schedules of Library, Sport complex ', '2022-23'),
(4, '4.4.2(B)', 'Laboratory Timetables', '2022-23'),
(4, '4.4.2(C)', 'SOP for Laboratory utilization', '2022-23'),
(4, '4.4.2(D)', 'SOP for usage of general amenities', '2022-23'),
(4, '4.4.2(E)', 'Geo-tagged photos of GAMYA', '2022-23'),
(4, '4.4.2(F)', 'Maintenance schedules and AMC letters from Estate department', '2022-23'),
(5, '5.1.1', 'Sanction letter of scholarship', '2022-23'),
(5, '5.1.2', 'List of students benefitted and award of scholarships', '2022-23'),
(5, '5.1.3', 'Capacity Development and Skill Enhancement Activities ', '2022-23'),
(5, '5.1.3(A)', 'Soft Skill, Language and Communication', '2022-23'),
(5, '5.1.3(B)', 'Yoga Class', '2022-23'),
(5, '5.1.3(C)', 'Awareness of Trends in technology', '2022-23'),
(5, '5.1.4(A)', 'Guidance for Competitive Exams', '2022-23'),
(5, '5.1.4(B)', 'Career Counselling', '2022-23'),
(5, '5.1.5', 'Redressal of Students Grievances', '2022-23'),
(5, '5.2.1', 'List of outgoing students got placement', '2022-23'),
(5, '5.2.2', 'List of outgoing students progressing to higher education', '2022-23'),
(5, '5.2.3(A)', 'List of Students qualifying in competitive Examinations', '2022-23'),
(5, '5.2.3(B)', 'List of students appeared in competitive Examinations', '2022-23'),
(5, '5.3.1', 'Number of awards/medals for outstanding performance in sports and/or cultural activities', '2022-23'),
(5, '5.3.2', 'Proof of representation of the students in academic and non-academic bodies (AMC, EDC, WEC, Placement Committee, Anti-ragging Committee, Food Committee, Professional Bodies – respective files', '2022-23'),
(5, '5.3.3', 'Number of sports and cultural events / competitions organized by the institution', '2022-23'),
(5, '5.4.1', 'Proof of Alumni registration, Proof of alumni chapter activities conducted and details of the number of students participated, Number of alumni registered as on date (Portal directory), Alumni contributions Representation in the BoS, Guest lectures/GRAD talks', '2022-23'),
(5, '5.4.2', 'Alumni’s financial contribution during the year', '2022-23'),
(6, '6.1.1(A)', 'Academic Monitoring Committee meeting minutes', '2022-23'),
(6, '6.1.1(B)', 'Placement Committee meeting minutes', '2022-23'),
(6, '6.1.1(C)', 'SAC - coordinators meeting minutes', '2022-23'),
(6, '6.1.1(D)', 'Anti Ragging Committee meeting minutes', '2022-23'),
(6, '6.1.1(E)', 'IQAC meeting minutes', '2022-23'),
(6, '6.1.1(F)', 'Board of Studies Meeting minutes', '2022-23'),
(6, '6.1.1(G)', 'Academic Council meeting minutes', '2022-23'),
(6, '6.1.1(H)', 'Governing Council meeting minutes', '2022-23'),
(6, '6.1.1(I)', 'HOD/Academic Development Committee Meeting Minutes', '2022-23'),
(6, '6.1.1(J)', 'Finance Committee meeting minutes', '2022-23'),
(6, '6.1.1(K)', 'Library Committee meeting minutes', '2022-23'),
(6, '6.1.1(L)', 'Town Hall meeting minutes', '2022-23'),
(6, '6.1.2(A)', 'Strat-Plan', '2022-23'),
(6, '6.1.2(B)', 'Requisition for two set of mid question papers from CoE – e-mail proof', '2022-23'),
(6, '6.1.2(C)', 'Declaration of mid question paper set number from CoE– e-mail proof', '2022-23'),
(6, '6.1.2(D)', 'Uniform Evaluation', '2022-23'),
(6, '6.1.2(E)', 'Research Review meeting minutes', '2022-23'),
(6, '6.2.1(A)', 'Strat-Plan', '2022-23'),
(6, '6.2.1(B)', 'Merit scholarships based on AP-EAMCET rank', '2022-23'),
(6, '6.2.1(C)', 'Meritorious scholarships for students', '2022-23'),
(6, '6.2.1(D)', 'Details of the GATE training classes conducted; List of students attended GATE Coaching & secured score', '2022-23'),
(6, '6.2.1(E)', 'Details of the CRT programs/ Technical training/Competitions conducted (Total number of hours), the List of the students attended the training programs & Proof of attendance, Branch wise list of the students placed, and the number of companies visited. List of students attended the WTN.', '2022-23'),
(6, '6.2.1(F)', 'Motivational and inspirational talks by the industry experts', '2022-23'),
(6, '6.2.1(G)', 'Social media updates', '2022-23'),
(6, '6.2.2(A)', 'Organogram', '2022-23'),
(6, '6.2.2(B)', 'HR & Service rules/Incentive policies', '2022-23'),
(6, '6.2.2(D)', 'Frequency and conduct of the meetings of governance committees (GC, AC, BOS, FC)', '2022-23'),
(6, '6.2.2(E)', 'SOP for procurement (AOP, MRN, Comparative statements and Purchase Orders)', '2022-23'),
(6, '6.3.1(A)', 'HR Policies (Welfare & Career development)', '2022-23'),
(6, '6.3.1(B)', 'Details of the training programs conducted for teaching & non-teaching staff', '2022-23'),
(6, '6.3.1(C)', 'Details of the staff (Teaching & Non-teaching promoted)', '2022-23'),
(6, '6.3.1(D)', 'Details of on campus housing, Term insurance, Medical insurance, Children education, ESI, Cooperative credit society, Concessions in IP/OP services and Gratuity', '2022-23'),
(6, '6.3.1(E)', 'Details of the faculty received incentives for completion of Ph.D./ QIP', '2022-23'),
(6, '6.3.2(A)', 'Number of teachers provided with financial support to attend conferences / workshops and towards payment of membership fee of professional bodies during the year', '2022-23'),
(6, '6.3.3(A)', 'Annual Reports highlighting training programs conducted for teaching & non teaching', '2022-23'),
(6, '6.3.3(B)', 'Training programs conducted for teaching & non-teaching', '2022-23'),
(6, '6.3.4(A)', 'List of Faculty FDPS Attended & Proofs', '2022-23'),
(6, '6.5.1(A)', 'List of companies hosted Internship', '2022-23'),
(6, '6.5.1(B)', 'FADS – Supporting Docs', '2022-23'),
(6, '6.5.1(C)', 'Consolidated initiatives of IQAC (strategies & contributions of IQAC mentioned)', '2022-23'),
(6, '6.5.2(A)', 'List of the IQAC initiatives taken up to enhance the students’ performance (Course coordinator meetings, AMC meeting minutes, Remedial classes for slow learners)', '2022-23'),
(6, '6.5.2(B)', 'Details of the progression of the students from 1st to 8th semesters branch wise for 2020-21 for all the batches graduated – 1 Bar chart, in each bar chart with eight bars', '2022-23'),
(6, '6.5.2(C)', 'Structures & methodologies of operations ISO audits, Academic audits -Internal & External branch wise', '2022-23'),
(6, '6.5.2(D)', 'Internal & External Academic Audit Report', '2022-23'),
(6, '6.5.3(A)', 'IQAC meeting minutes', '2022-23'),
(6, '6.5.3(B)', 'Feedback system of the institution', '2022-23'),
(6, '6.5.3(C)', 'Feedback system for design and review of syllabus', '2022-23'),
(6, '6.5.3(D)', 'Collaborative quality initiatives', '2022-23'),
(6, '6.5.3(E)', 'Participation in NIRF', '2022-23'),
(6, '6.5.3(F)', 'NBA accreditation', '2022-23'),
(6, '6.5.3(G)', 'NAAC accreditation', '2022-23'),
(6, '6.5.3(H)', 'IQAC Feedback analysis', '2022-23'),
(7, '7.1.1(A)', 'Action plan of WEC for gender sensitization', '2022-23'),
(7, '7.1.1(B)', 'Campus surveillance with CC TV (Audit report)', '2022-23'),
(7, '7.1.1(C)', 'Policy for women security and safety (PASH)', '2022-23'),
(7, '7.1.1(D)', 'Photographs of Exclusive reading rooms, waiting rooms and rest rooms', '2022-23'),
(7, '7.1.1(E)', 'Day care centre for the kids (Beneficiaries)', '2022-23'),
(7, '7.1.1(F)', 'Welfare measures (Maternity leave for two kids)', '2022-23'),
(7, '7.1.1(G)', 'List of the beneficiaries', '2022-23'),
(7, '7.1.10(A)', 'Details of the monitoring committee composition and minutes of the committee meeting, number of programmes organized, reports on the various programmes, etc. in support of the claims', '2022-23'),
(7, '7.1.10(B)', 'Policy document on code of ethics', '2022-23'),
(7, '7.1.10(C)', 'Circulars and geo tagged photographs and caption of the activities organized under the metric for teachers, students, administrators and other staffs', '2022-23'),
(7, '7.1.11(A)', 'Annual report of the celebrations and commemorative events', '2022-23'),
(7, '7.1.11(B)', 'Photographs of some of the events', '2022-23'),
(7, '7.1.2(A)', 'Geo tagged photographs with caption of the facilities', '2022-23'),
(7, '7.1.2(B)', 'Bills for the purchase of equipment for the facilities', '2022-23'),
(7, '7.1.2(C)', 'Permission document for connection to the grid from Government', '2022-23'),
(7, '7.1.3(A)', 'Geo tagged photographs of the facilities', '2022-23'),
(7, '7.1.3(B)', 'SOP for solid waste management', '2022-23'),
(7, '7.1.4(A)', 'Geo tagged photographs with caption of the facilities', '2022-23'),
(7, '7.1.4(B)', 'Bills for the purchase of equipment', '2022-23'),
(7, '7.1.5(A)', 'Policy document on the green campus', '2022-23'),
(7, '7.1.5(B)', 'Geo tagged photographs/Videos with caption of the facilities', '2022-23'),
(7, '7.1.5(C)', 'Circulars for the implementation of the initiatives and any other supporting document', '2022-23'),
(7, '7.1.6(A)', 'Policy document on environment and energy usage', '2022-23'),
(7, '7.1.6(B)', 'Certificate from the auditing agency', '2022-23'),
(7, '7.1.6(C)', 'Certificates of the awards received from the recognized agency', '2022-23'),
(7, '7.1.6(D)', 'Report on environmental promotional activities conducted beyond the campus', '2022-23'),
(7, '7.1.6(F)', 'Green audit report', '2022-23'),
(7, '7.1.7(A)', 'Policy document and information brochure', '2022-23'),
(7, '7.1.7(B)', 'Geo tagged photos', '2022-23'),
(7, '7.1.7(C)', 'Bills and invoice/purchase order/AMC', '2022-23'),
(7, '7.1.7(D)', 'A rest room should include specific requirements of Divyangjan', '2022-23'),
(7, '7.1.7(E)', 'Bills for the software procured', '2022-23'),
(7, '7.1.8(A)', 'List of the outbound programs', '2022-23'),
(7, '7.1.8(B)', 'List of national level activities (Cultural, Sports, Academic, Cultural & Sports)', '2022-23'),
(7, '7.1.8(C)', 'List of the faculty & students coming from the out of state', '2022-23'),
(7, '7.1.9(A)', 'List of the activities/Events conducted, and the number of students present. (Awareness programs on Women safety & protection, Anti ragging, Judicial rights, Gender equality, Traffic rules, Environment protection, Conservation of natural resources (power & water)) >', '2022-23'),
(7, '7.2.1', 'Institutional Values and Best Practices', '2022-23'),
(7, '7.3.1', 'Institutional Distinctiveness', '2022-23');

-- --------------------------------------------------------

--
-- Table structure for table `criteria1`
--

CREATE TABLE `criteria1` (
  `SI_no` int(10) NOT NULL,
  `Sub_no` varchar(30) NOT NULL,
  `Des` varchar(600) NOT NULL,
  `year` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `criteria1`
--

INSERT INTO `criteria1` (`SI_no`, `Sub_no`, `Des`, `year`) VALUES
(1, '1.1.1', 'Copies of PEOs, POs & PSOs for all programs', '2020-21'),
(1, '1.1.2(A)', 'List of programs where syllabus revision has been carried out signed by the Principal', '2020-21'),
(1, '1.1.2(B)', 'Approved Minutes of relevant Academic Council/BOS meetings highlighting the specific agenda item relevant to the metric year wise', '2020-21'),
(1, '1.1.3(A)', 'Syllabus copy of the courses highlighting Focus on employability/entrepreneurship/ skill development along with their course outcomes.', '2020-21'),
(1, '1.1.3(B)', 'Minutes of the Boards of Studies/ Academic Council meetings with approval for these courses.', '2020-21'),
(1, '1.1.3(C)', 'List of MoUs', '2020-21'),
(1, '1.2.1(A)', 'List of new courses introduced program-wise during the assessment period certified by the Principal.', '2020-21'),
(1, '1.2.1(B)', 'Minutes of relevant Academic Council/BOS meetings highlighting the name of the new courses introduced', '2020-21'),
(1, '1.2.2(A)', 'List of programs in which CBCS/Elective course system implemented in the last completed academic year certified by the principal', '2020-21'),
(1, '1.2.2(B)', 'Structure of the program clearly indicating courses, credits/Electives and Minutes of relevant Academic Council/BOS meetings highlighting the relevant documents to this metric', '2020-21'),
(1, '1.3.1', 'List and Description of courses relevant to Professional Ethics, Gender diversity and equality, Human Values, Environment and Sustainability, Women Empowerment introduced in the Curriculum along with the syllabus should be available in all the departments', '2020-21'),
(1, '1.3.2(A)', 'List of value-added courses which are optional and offered outside the curriculum of the programs with authorized sign.', '2020-21'),
(1, '1.3.2(B)', 'Brochure and Course content or syllabus along with course outcome of Value-added courses offered.', '2020-21'),
(1, '1.3.3', 'List of enrolled students for courses addressed in 1.3.2', '2020-21'),
(1, '1.3.4', 'List of students undertaking the field projects/ internships / student projects program-wise in the last completed academic year along with the details of title, place of work etc.', '2020-21'),
(1, '1.4.1', 'Sample Filled in feedback forms from the stakeholders to be provided.', '2020-21'),
(1, '1.4.2', 'Stakeholder feedback analysis report signed by the authority', '2020-21'),
(2, '2.1.1(A)', 'Students admitted', '2020-21'),
(2, '2.1.1(B)', 'Enrolment Number_ AICTE letters of sanctioned intake', '2020-21'),
(2, '2.1.1(C)', 'Enrolment Number _ Ratified list of admitted students', '2020-21'),
(2, '2.1.2(A)', 'Reservation seats categories to be considered as per the state rule (APSHE Guidelines)', '2020-21'),
(2, '2.1.2(B)', 'AP regulation for Admissions GO No. 73', '2020-21'),
(2, '2.1.2(C)', 'Guidelines for filling the left-over vacancies under reservation', '2020-21'),
(2, '2.1.2(D)', 'Category wise_ Ratified list of students admitted', '2020-21'),
(2, '2.1.2(E)', 'Admission Abstract', '2020-21'),
(2, '2.2.1(A)', 'Video records of the specific topics', '2020-21'),
(2, '2.2.1(B)', 'Classes for the slow learner', '2020-21'),
(2, '2.2.1(C)', 'Additional assignment', '2020-21'),
(2, '2.2.1(D)', 'List of the advanced learners opted MOOCs', '2020-21'),
(2, '2.2.1(E)', 'List of the advanced learners opted Minors and honors', '2020-21'),
(2, '2.2.1(F)', 'Make-up classes', '2020-21'),
(2, '2.2.1(G)', 'Remedial classes', '2020-21'),
(2, '2.2.1(H)', 'Student participation in technical events', '2020-21'),
(2, '2.2.2(A)', 'List of the full-time teachers', '2020-21'),
(2, '2.2.2(B)', 'Student intake', '2020-21'),
(2, '2.3.1(A)', 'Academic regulations and curriculum', '2020-21'),
(2, '2.3.2 (A)', 'ICT Tools Gadgets viz. Graphic tablets, MST, Smart pen, Projector,', '2020-21'),
(2, '2.3.3(A)', 'Circular of mentor – mentees', '2020-21'),
(2, '2.3.3(B)', 'Mentor list as announced by the HEI', '2020-21'),
(2, '2.3.3(C)', 'issues raised and resolved in the mentor system', '2020-21'),
(2, '2.3.4(A)', 'Academic calendar', '2020-21'),
(2, '2.3.4(B)', 'Proof of course allotment', '2020-21'),
(2, '2.3.4(C)', 'Proof for lecture plan, Schedule and diary', '2020-21'),
(2, '2.3.4(D)', 'Minutes of AMC', '2020-21'),
(2, '2.4.1(A)', 'Sanction letter indicating number of posts', '2020-21'),
(2, '2.4.1(B)', 'Department wise List of full-time teachers appointed', '2020-21'),
(2, '2.4.2', 'List of faculties having Ph.D', '2020-21'),
(2, '2.4.3', 'List of faculties along with particulars of the date of Appointment', '2020-21'),
(2, '2.5.1(A)', 'Examination Result Notifications', '2020-21'),
(2, '2.5.1(B)', 'List of Programs offered and the last date of the latest semester end exams and date of result declaration', '2020-21'),
(2, '2.5.2(A)', 'Number of complaints', '2020-21'),
(2, '2.5.2(B)', 'Minutes of the examination Committee', '2020-21'),
(2, '2.5.3(A)', 'Examination Regulations', '2020-21'),
(2, '2.5.3(B)', 'Question Paper templates', '2020-21'),
(2, '2.5.3(C)', 'Proof of the hybrid grading sheet', '2020-21'),
(2, '2.5.3(D)', 'Examination Results link in the website', '2020-21'),
(2, '2.5.3(E)', 'Proof of the OMR', '2020-21'),
(2, '2.6.1(A)', 'List of POs and COs', '2020-21'),
(2, '2.6.1(B)', 'Photo gallery of the COs & POs displayed Program wise', '2020-21'),
(2, '2.6.2(A)', 'CO and PO attainment', '2020-21'),
(2, '2.6.2(B)', 'Files related to all surveys for the indirect assessment', '2020-21'),
(2, '2.6.3(A)', 'Annual report of CoE', '2020-21'),
(2, '2.6.3(B)', 'Certified report from CoE indicating Students eligible for the degree program', '2020-21'),
(2, '2.7.1', 'Student satisfaction survey', '2020-21'),
(3, '3.1.1(A)', 'List of research equipment along with the proof of purchases (Bill copies)', '2020-21'),
(3, '3.1.1(B)', 'Policy for Faculty Assessment and Development Scheme', '2020-21'),
(3, '3.1.1(C)', 'Financial Incentives for attending conferences, seminars and QIP', '2020-21'),
(3, '3.1.1(D)', 'Sanction letters for the funded research projects & UCs for the projects completed', '2020-21'),
(3, '3.1.1(E)', 'Copies of all MoUs for collaborative research', '2020-21'),
(3, '3.1.1(F)', 'Minutes of the governing council meeting-reflecting the research promotions', '2020-21'),
(3, '3.1.2', 'Details of Seed money', '2020-21'),
(3, '3.1.3', 'Awards of teachers for research', '2020-21'),
(3, '3.2.1(A)', 'List of Grants received for research projects', '2020-21'),
(3, '3.2.1(B)', 'e-copies of grants sanctioned', '2020-21'),
(3, '3.2.2', 'List of teachers having research projects during the year', '2020-21'),
(3, '3.2.3', 'Number of teachers recognized as research guides', '2020-21'),
(3, '3.2.4', 'Number of departments having research projects', '2020-21'),
(3, '3.2.4(A)', 'Web Links to Funding Agencies', '2020-21'),
(3, '3.3.1(A)', 'MSME business incubation center – sanction letter', '2020-21'),
(3, '3.3.1(B)', 'AICTE sponsored EDC – Sanction letters and list of the activities conducted by EDC', '2020-21'),
(3, '3.3.1(C)', 'Dedicated centres for research', '2020-21'),
(3, '3.3.2', 'Detailed report including photos, resource persons etc.', '2020-21'),
(3, '3.4.1(A)', 'Research Advisory Committee', '2020-21'),
(3, '3.4.1(B)', 'Ethics Committee', '2020-21'),
(3, '3.4.1(C)', 'Inclusion of Research Ethics in the research methodology course work', '2020-21'),
(3, '3.4.1(D)', 'Anti Plagiarism software approved by JNTU', '2020-21'),
(3, '3.4.2(A)', 'List of Faculty along with the names of Research scholars', '2020-21'),
(3, '3.4.2(B)', 'Copy of the Registration letters/Joining letters', '2020-21'),
(3, '3.4.3', 'e-copies of publications', '2020-21'),
(3, '3.4.4', 'e-copies of books/book chapters', '2020-21'),
(3, '3.4.5', 'Bibliometrics of the publications based on average Citation Index', '2020-21'),
(3, '3.4.6', 'Bibliometrics of the publication-based h-Index of the University', '2020-21'),
(3, '3.5.1', 'Audited statements for Revenue generated from consultancy', '2020-21'),
(3, '3.5.2', 'Audited statements related to the expenditure on developing facilities/Training the staff', '2020-21'),
(3, '3.6.1(A)', 'List of NSS activities conducted year wise and number of students participated', '2020-21'),
(3, '3.6.1(B)', 'Covid-19 booster report', '2020-21'),
(3, '3.6.1(C)', 'MGNCRE Reports', '2020-21'),
(3, '3.6.1(D)', 'Community Radio report', '2020-21'),
(3, '3.6.1(E)', 'List of NCC activities conducted year wise and number of students participated', '2020-21'),
(3, '3.6.2', 'e-copies of awards and recognition for extension activities', '2020-21'),
(3, '3.6.3', 'Number of extension and outreach programmes conducted by the institution', '2020-21'),
(3, '3.6.4', 'Number of students participating in extension activities', '2020-21'),
(3, '3.7.1', 'e-copies of collaborative activities indicating nature of collaboration', '2020-21'),
(3, '3.7.2(A)', 'e-copies of functional MoUs', '2020-21'),
(3, '3.7.2(B)', 'e-copies of Activities of MOUs', '2020-21'),
(4, '4.1.1(A)', 'Details of the Classrooms, Labs & Other facilities across all the six blocks', '2020-21'),
(4, '4.1.1(B)', 'List of the laboratories with titles', '2020-21'),
(4, '4.1.1(C)', 'Campus LAN diagram', '2020-21'),
(4, '4.1.1(D)', 'Proof of bandwidth', '2020-21'),
(4, '4.1.1(E)', 'List of the software', '2020-21'),
(4, '4.1.1(F)', 'Photo gallery of all the academic blocks, classrooms, drawing hall, seminar hall, auditorium, and Labs', '2020-21'),
(4, '4.1.2(A)', 'Colleague of Geo-tagged pictures', '2020-21'),
(4, '4.1.2(B)', 'Area details of the all the facilities', '2020-21'),
(4, '4.1.2(C)', 'Photo gallery of the various activities', '2020-21'),
(4, '4.1.3(A)', 'Geo-tagged photographs of classrooms with ICT enabled facilities', '2020-21'),
(4, '4.1.3(B)', 'Class Timetables', '2020-21'),
(4, '4.1.4(A)', 'Budget allocation', '2020-21'),
(4, '4.1.4(B)', 'Provide the consolidated fund allocation towards infrastructure augmentation facilities duly certified by Head of the Institution', '2020-21'),
(4, '4.2.1(A)', 'Library operates through LIBSYS', '2020-21'),
(4, '4.2.1(B)', 'All the books are provided with RF Id security tags', '2020-21'),
(4, '4.2.1(C)', 'Copy of the latest License agreement of LIBSYS-7', '2020-21'),
(4, '4.2.1(D)', 'Digital Library', '2020-21'),
(4, '4.2.2 (D)', 'Specific details in respect of e-resources selected.', '2020-21'),
(4, '4.2.2 (E)', 'Databases', '2020-21'),
(4, '4.2.2(A)', 'Details of subscriptions of e-journals', '2020-21'),
(4, '4.2.2(B)', 'Letter of subscription', '2020-21'),
(4, '4.2.2(C)', 'Screenshots of the facilities claimed with the name of HEI.', '2020-21'),
(4, '4.2.3(A)', 'Consolidated extract of expenditure', '2020-21'),
(4, '4.2.3(B)', 'Invoices of all the expenditure', '2020-21'),
(4, '4.2.4(A)', 'Certified e-copy of the ledger for footfalls for 5days', '2020-21'),
(4, '4.2.4(B)', 'Certified screenshots of the data for the same 5 days for online access', '2020-21'),
(4, '4.2.4(C)', 'Last page of accession register details', '2020-21'),
(4, '4.3.1(A)', 'Colleague of Geo-tagged pictures', '2020-21'),
(4, '4.3.1(B)', 'Policy document', '2020-21'),
(4, '4.3.1(C)', 'Campus Wi-Fi/Network diagram', '2020-21'),
(4, '4.3.1(D)', 'Budget of the year 2020-21', '2020-21'),
(4, '4.3.2(A)', 'Computer Bills', '2020-21'),
(4, '4.3.2(B)', 'Student strength', '2020-21'),
(4, '4.3.3(A)', 'Details of available bandwidth of internet connection in the Institution', '2020-21'),
(4, '4.3.3(B)', 'Bills for any one month/one quarter.', '2020-21'),
(4, '4.3.3(C)', 'e-copy of document of agreement with the service provider.', '2020-21'),
(4, '4.3.4(A)', 'Geo tagged photographs of Media Centre, Audio Visual Centre etc.,', '2020-21'),
(4, '4.3.4(B)', 'Purchase bills for Lecture Capturing System', '2020-21'),
(4, '4.3.4(C)', 'Audited income expenditure statement highlighting the relevant expenditure.', '2020-21'),
(4, '4.4.1(A)', 'Audited statements of accounts.', '2020-21'),
(4, '4.4.2(A)', 'Schedules of Library, Sport complex ', '2020-21'),
(4, '4.4.2(B)', 'Laboratory Timetables', '2020-21'),
(4, '4.4.2(C)', 'SOP for Laboratory utilization', '2020-21'),
(4, '4.4.2(D)', 'SOP for usage of general amenities', '2020-21'),
(4, '4.4.2(E)', 'Geo-tagged photos of GAMYA', '2020-21'),
(4, '4.4.2(F)', 'Maintenance schedules and AMC letters from Estate department', '2020-21'),
(5, '5.1.1', 'Sanction letter of scholarship', '2020-21'),
(5, '5.1.2', 'List of students benefitted and award of scholarships', '2020-21'),
(5, '5.1.3', 'Capacity Development and Skill Enhancement Activities ', '2020-21'),
(5, '5.1.3(A)', 'Soft Skill, Language and Communication', '2020-21'),
(5, '5.1.3(B)', 'Yoga Class', '2020-21'),
(5, '5.1.3(C)', 'Awareness of Trends in technology', '2020-21'),
(5, '5.1.4(A)', 'Guidance for Competitive Exams', '2020-21'),
(5, '5.1.4(B)', 'Career Counselling', '2020-21'),
(5, '5.1.5', 'Redressal of Students Grievances', '2020-21'),
(5, '5.2.1', 'List of outgoing students got placement', '2020-21'),
(5, '5.2.2', 'List of outgoing students progressing to higher education', '2020-21'),
(5, '5.2.3(A)', 'List of Students qualifying in competitive Examinations', '2020-21'),
(5, '5.2.3(B)', 'List of students appeared in competitive Examinations', '2020-21'),
(5, '5.3.1', 'Number of awards/medals for outstanding performance in sports and/or cultural activities', '2020-21'),
(5, '5.3.2', 'Proof of representation of the students in academic and non-academic bodies (AMC, EDC, WEC, Placement Committee, Anti-ragging Committee, Food Committee, Professional Bodies – respective files', '2020-21'),
(5, '5.3.3', 'Number of sports and cultural events / competitions organized by the institution', '2020-21'),
(5, '5.4.1', 'Proof of Alumni registration, Proof of alumni chapter activities conducted and details of the number of students participated, Number of alumni registered as on date (Portal directory), Alumni contributions Representation in the BoS, Guest lectures/GRAD talks', '2020-21'),
(5, '5.4.2', 'Alumni’s financial contribution during the year', '2020-21'),
(6, '6.1.1(A)', 'Academic Monitoring Committee meeting minutes', '2020-21'),
(6, '6.1.1(B)', 'Placement Committee meeting minutes', '2020-21'),
(6, '6.1.1(C)', 'SAC - coordinators meeting minutes', '2020-21'),
(6, '6.1.1(D)', 'Anti Ragging Committee meeting minutes', '2020-21'),
(6, '6.1.1(E)', 'IQAC meeting minutes', '2020-21'),
(6, '6.1.1(F)', 'Board of Studies Meeting minutes', '2020-21'),
(6, '6.1.1(G)', 'Academic Council meeting minutes', '2020-21'),
(6, '6.1.1(H)', 'Governing Council meeting minutes', '2020-21'),
(6, '6.1.1(I)', 'HOD/Academic Development Committee Meeting Minutes', '2020-21'),
(6, '6.1.1(J)', 'Finance Committee meeting minutes', '2020-21'),
(6, '6.1.1(K)', 'Library Committee meeting minutes', '2020-21'),
(6, '6.1.1(L)', 'Town Hall meeting minutes', '2020-21'),
(6, '6.1.2(A)', 'Strat-Plan', '2020-21'),
(6, '6.1.2(B)', 'Requisition for two set of mid question papers from CoE – e-mail proof', '2020-21'),
(6, '6.1.2(C)', 'Declaration of mid question paper set number from CoE– e-mail proof', '2020-21'),
(6, '6.1.2(D)', 'Uniform Evaluation', '2020-21'),
(6, '6.1.2(E)', 'Research Review meeting minutes', '2020-21'),
(6, '6.2.1(A)', 'Strat-Plan', '2020-21'),
(6, '6.2.1(B)', 'Merit scholarships based on AP-EAMCET rank', '2020-21'),
(6, '6.2.1(C)', 'Meritorious scholarships for students', '2020-21'),
(6, '6.2.1(D)', 'Details of the GATE training classes conducted; List of students attended GATE Coaching & secured score', '2020-21'),
(6, '6.2.1(E)', 'Details of the CRT programs/ Technical training/Competitions conducted (Total number of hours), the List of the students attended the training programs & Proof of attendance, Branch wise list of the students placed, and the number of companies visited. List of students attended the WTN.', '2020-21'),
(6, '6.2.1(F)', 'Motivational and inspirational talks by the industry experts', '2020-21'),
(6, '6.2.1(G)', 'Social media updates', '2020-21'),
(6, '6.2.2(A)', 'Organogram', '2020-21'),
(6, '6.2.2(B)', 'HR & Service rules/Incentive policies', '2020-21'),
(6, '6.2.2(D)', 'Frequency and conduct of the meetings of governance committees (GC, AC, BOS, FC)', '2020-21'),
(6, '6.2.2(E)', 'SOP for procurement (AOP, MRN, Comparative statements and Purchase Orders)', '2020-21'),
(6, '6.3.1(A)', 'HR Policies (Welfare & Career development)', '2020-21'),
(6, '6.3.1(B)', 'Details of the training programs conducted for teaching & non-teaching staff', '2020-21'),
(6, '6.3.1(C)', 'Details of the staff (Teaching & Non-teaching promoted)', '2020-21'),
(6, '6.3.1(D)', 'Details of on campus housing, Term insurance, Medical insurance, Children education, ESI, Cooperative credit society, Concessions in IP/OP services and Gratuity', '2020-21'),
(6, '6.3.1(E)', 'Details of the faculty received incentives for completion of Ph.D./ QIP', '2020-21'),
(6, '6.3.2(A)', 'Number of teachers provided with financial support to attend conferences / workshops and towards payment of membership fee of professional bodies during the year', '2020-21'),
(6, '6.3.3(A)', 'Annual Reports highlighting training programs conducted for teaching & non teaching', '2020-21'),
(6, '6.3.3(B)', 'Training programs conducted for teaching & non-teaching', '2020-21'),
(6, '6.3.4(A)', 'List of Faculty FDPS Attended & Proofs', '2020-21'),
(6, '6.5.1(A)', 'List of companies hosted Internship', '2020-21'),
(6, '6.5.1(B)', 'FADS – Supporting Docs', '2020-21'),
(6, '6.5.1(C)', 'Consolidated initiatives of IQAC (strategies & contributions of IQAC mentioned)', '2020-21'),
(6, '6.5.2(A)', 'List of the IQAC initiatives taken up to enhance the students’ performance (Course coordinator meetings, AMC meeting minutes, Remedial classes for slow learners)', '2020-21'),
(6, '6.5.2(B)', 'Details of the progression of the students from 1st to 8th semesters branch wise for 2020-21 for all the batches graduated – 1 Bar chart, in each bar chart with eight bars', '2020-21'),
(6, '6.5.2(C)', 'Structures & methodologies of operations ISO audits, Academic audits -Internal & External branch wise', '2020-21'),
(6, '6.5.2(D)', 'Internal & External Academic Audit Report', '2020-21'),
(6, '6.5.3(A)', 'IQAC meeting minutes', '2020-21'),
(6, '6.5.3(B)', 'Feedback system of the institution', '2020-21'),
(6, '6.5.3(C)', 'Feedback system for design and review of syllabus', '2020-21'),
(6, '6.5.3(D)', 'Collaborative quality initiatives', '2020-21'),
(6, '6.5.3(E)', 'Participation in NIRF', '2020-21'),
(6, '6.5.3(F)', 'NBA accreditation', '2020-21'),
(6, '6.5.3(G)', 'NAAC accreditation', '2020-21'),
(6, '6.5.3(H)', 'IQAC Feedback analysis', '2020-21'),
(7, '7.1.1(A)', 'Action plan of WEC for gender sensitization', '2020-21'),
(7, '7.1.1(B)', 'Campus surveillance with CC TV (Audit report)', '2020-21'),
(7, '7.1.1(C)', 'Policy for women security and safety (PASH)', '2020-21'),
(7, '7.1.1(D)', 'Photographs of Exclusive reading rooms, waiting rooms and rest rooms', '2020-21'),
(7, '7.1.1(E)', 'Day care centre for the kids (Beneficiaries)', '2020-21'),
(7, '7.1.1(F)', 'Welfare measures (Maternity leave for two kids)', '2020-21'),
(7, '7.1.1(G)', 'List of the beneficiaries', '2020-21'),
(7, '7.1.10(A)', 'Details of the monitoring committee composition and minutes of the committee meeting, number of programmes organized, reports on the various programmes, etc. in support of the claims', '2020-21'),
(7, '7.1.10(B)', 'Policy document on code of ethics', '2020-21'),
(7, '7.1.10(C)', 'Circulars and geo tagged photographs and caption of the activities organized under the metric for teachers, students, administrators and other staffs', '2020-21'),
(7, '7.1.11(A)', 'Annual report of the celebrations and commemorative events', '2020-21'),
(7, '7.1.11(B)', 'Photographs of some of the events', '2020-21'),
(7, '7.1.2(A)', 'Geo tagged photographs with caption of the facilities', '2020-21'),
(7, '7.1.2(B)', 'Bills for the purchase of equipment for the facilities', '2020-21'),
(7, '7.1.2(C)', 'Permission document for connection to the grid from Government', '2020-21'),
(7, '7.1.3(A)', 'Geo tagged photographs of the facilities', '2020-21'),
(7, '7.1.3(B)', 'SOP for solid waste management', '2020-21'),
(7, '7.1.4(A)', 'Geo tagged photographs with caption of the facilities', '2020-21'),
(7, '7.1.4(B)', 'Bills for the purchase of equipment', '2020-21'),
(7, '7.1.5(A)', 'Policy document on the green campus', '2020-21'),
(7, '7.1.5(B)', 'Geo tagged photographs/Videos with caption of the facilities', '2020-21'),
(7, '7.1.5(C)', 'Circulars for the implementation of the initiatives and any other supporting document', '2020-21'),
(7, '7.1.6(A)', 'Policy document on environment and energy usage', '2020-21'),
(7, '7.1.6(B)', 'Certificate from the auditing agency', '2020-21'),
(7, '7.1.6(C)', 'Certificates of the awards received from the recognized agency', '2020-21'),
(7, '7.1.6(D)', 'Report on environmental promotional activities conducted beyond the campus', '2020-21'),
(7, '7.1.6(F)', 'Green audit report', '2020-21'),
(7, '7.1.7(A)', 'Policy document and information brochure', '2020-21'),
(7, '7.1.7(B)', 'Geo tagged photos', '2020-21'),
(7, '7.1.7(C)', 'Bills and invoice/purchase order/AMC', '2020-21'),
(7, '7.1.7(D)', 'A rest room should include specific requirements of Divyangjan', '2020-21'),
(7, '7.1.7(E)', 'Bills for the software procured', '2020-21'),
(7, '7.1.8(A)', 'List of the outbound programs', '2020-21'),
(7, '7.1.8(B)', 'List of national level activities (Cultural, Sports, Academic, Cultural & Sports)', '2020-21'),
(7, '7.1.8(C)', 'List of the faculty & students coming from the out of state', '2020-21'),
(7, '7.1.9(A)', 'List of the activities/Events conducted, and the number of students present. (Awareness programs on Women safety & protection, Anti ragging, Judicial rights, Gender equality, Traffic rules, Environment protection, Conservation of natural resources (power & water)) >', '2020-21'),
(7, '7.2.1', 'Institutional Values and Best Practices', '2020-21'),
(7, '7.3.1', 'Institutional Distinctiveness', '2020-21');

-- --------------------------------------------------------

--
-- Table structure for table `criteria2`
--

CREATE TABLE `criteria2` (
  `SI_no` int(10) NOT NULL,
  `Sub_no` varchar(30) NOT NULL,
  `Des` varchar(600) NOT NULL,
  `year` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `criteria2`
--

INSERT INTO `criteria2` (`SI_no`, `Sub_no`, `Des`, `year`) VALUES
(1, '1.1.1', 'Copies of PEOs, POs & PSOs for all programs', '2021-22'),
(1, '1.1.2(A)', 'List of programs where syllabus revision has been carried out signed by the Principal', '2021-22'),
(1, '1.1.2(B)', 'Approved Minutes of relevant Academic Council/BOS meetings highlighting the specific agenda item relevant to the metric year wise', '2021-22'),
(1, '1.1.3(A)', 'Syllabus copy of the courses highlighting Focus on employability/entrepreneurship/ skill development along with their course outcomes.', '2021-22'),
(1, '1.1.3(B)', 'Minutes of the Boards of Studies/ Academic Council meetings with approval for these courses.', '2021-22'),
(1, '1.1.3(C)', 'List of MoUs', '2021-22'),
(1, '1.2.1(A)', 'List of new courses introduced program-wise during the assessment period certified by the Principal.', '2021-22'),
(1, '1.2.1(B)', 'Minutes of relevant Academic Council/BOS meetings highlighting the name of the new courses introduced', '2021-22'),
(1, '1.2.2(A)', 'List of programs in which CBCS/Elective course system implemented in the last completed academic year certified by the principal', '2021-22'),
(1, '1.2.2(B)', 'Structure of the program clearly indicating courses, credits/Electives and Minutes of relevant Academic Council/BOS meetings highlighting the relevant documents to this metric', '2021-22'),
(1, '1.3.1', 'List and Description of courses relevant to Professional Ethics, Gender diversity and equality, Human Values, Environment and Sustainability, Women Empowerment introduced in the Curriculum along with the syllabus should be available in all the departments', '2021-22'),
(1, '1.3.2(A)', 'List of value-added courses which are optional and offered outside the curriculum of the programs with authorized sign.', '2021-22'),
(1, '1.3.2(B)', 'Brochure and Course content or syllabus along with course outcome of Value-added courses offered.', '2021-22'),
(1, '1.3.3', 'List of enrolled students for courses addressed in 1.3.2', '2021-22'),
(1, '1.3.4', 'List of students undertaking the field projects/ internships / student projects program-wise in the last completed academic year along with the details of title, place of work etc.', '2021-22'),
(1, '1.4.1', 'Sample Filled in feedback forms from the stakeholders to be provided.', '2021-22'),
(1, '1.4.2', 'Stakeholder feedback analysis report signed by the authority', '2021-22'),
(2, '2.1.1(A)', 'Students admitted', '2021-22'),
(2, '2.1.1(B)', 'Enrolment Number_ AICTE letters of sanctioned intake', '2021-22'),
(2, '2.1.1(C)', 'Enrolment Number _ Ratified list of admitted students', '2021-22'),
(2, '2.1.2(A)', 'Reservation seats categories to be considered as per the state rule (APSHE Guidelines)', '2021-22'),
(2, '2.1.2(B)', 'AP regulation for Admissions GO No. 73', '2021-22'),
(2, '2.1.2(C)', 'Guidelines for filling the left-over vacancies under reservation', '2021-22'),
(2, '2.1.2(D)', 'Category wise_ Ratified list of students admitted', '2021-22'),
(2, '2.1.2(E)', 'Admission Abstract', '2021-22'),
(2, '2.2.1(A)', 'Video records of the specific topics', '2021-22'),
(2, '2.2.1(B)', 'Classes for the slow learner', '2021-22'),
(2, '2.2.1(C)', 'Additional assignment', '2021-22'),
(2, '2.2.1(D)', 'List of the advanced learners opted MOOCs', '2021-22'),
(2, '2.2.1(E)', 'List of the advanced learners opted Minors and honors', '2021-22'),
(2, '2.2.1(F)', 'Make-up classes', '2021-22'),
(2, '2.2.1(G)', 'Remedial classes', '2021-22'),
(2, '2.2.1(H)', 'Student participation in technical events', '2021-22'),
(2, '2.2.2(A)', 'List of the full-time teachers', '2021-22'),
(2, '2.2.2(B)', 'Student intake', '2021-22'),
(2, '2.3.1(A)', 'Academic regulations and curriculum', '2021-22'),
(2, '2.3.2 (A)', 'ICT Tools Gadgets viz. Graphic tablets, MST, Smart pen, Projector,', '2021-22'),
(2, '2.3.3(A)', 'Circular of mentor – mentees', '2021-22'),
(2, '2.3.3(B)', 'Mentor list as announced by the HEI', '2021-22'),
(2, '2.3.3(C)', 'issues raised and resolved in the mentor system', '2021-22'),
(2, '2.3.4(A)', 'Academic calendar', '2021-22'),
(2, '2.3.4(B)', 'Proof of course allotment', '2021-22'),
(2, '2.3.4(C)', 'Proof for lecture plan, Schedule and diary', '2021-22'),
(2, '2.3.4(D)', 'Minutes of AMC', '2021-22'),
(2, '2.4.1(A)', 'Sanction letter indicating number of posts', '2021-22'),
(2, '2.4.1(B)', 'Department wise List of full-time teachers appointed', '2021-22'),
(2, '2.4.2', 'List of faculties having Ph.D', '2021-22'),
(2, '2.4.3', 'List of faculties along with particulars of the date of Appointment', '2021-22'),
(2, '2.5.1(A)', 'Examination Result Notifications', '2021-22'),
(2, '2.5.1(B)', 'List of Programs offered and the last date of the latest semester end exams and date of result declaration', '2021-22'),
(2, '2.5.2(A)', 'Number of complaints', '2021-22'),
(2, '2.5.2(B)', 'Minutes of the examination Committee', '2021-22'),
(2, '2.5.3(A)', 'Examination Regulations', '2021-22'),
(2, '2.5.3(B)', 'Question Paper templates', '2021-22'),
(2, '2.5.3(C)', 'Proof of the hybrid grading sheet', '2021-22'),
(2, '2.5.3(D)', 'Examination Results link in the website', '2021-22'),
(2, '2.5.3(E)', 'Proof of the OMR', '2021-22'),
(2, '2.6.1(A)', 'List of POs and COs', '2021-22'),
(2, '2.6.1(B)', 'Photo gallery of the COs & POs displayed Program wise', '2021-22'),
(2, '2.6.2(A)', 'CO and PO attainment', '2021-22'),
(2, '2.6.2(B)', 'Files related to all surveys for the indirect assessment', '2021-22'),
(2, '2.6.3(A)', 'Annual report of CoE', '2021-22'),
(2, '2.6.3(B)', 'Certified report from CoE indicating Students eligible for the degree program', '2021-22'),
(2, '2.7.1', 'Student satisfaction survey', '2021-22'),
(3, '3.1.1(A)', 'List of research equipment along with the proof of purchases (Bill copies)', '2021-22'),
(3, '3.1.1(B)', 'Policy for Faculty Assessment and Development Scheme', '2021-22'),
(3, '3.1.1(C)', 'Financial Incentives for attending conferences, seminars and QIP', '2021-22'),
(3, '3.1.1(D)', 'Sanction letters for the funded research projects & UCs for the projects completed', '2021-22'),
(3, '3.1.1(E)', 'Copies of all MoUs for collaborative research', '2021-22'),
(3, '3.1.1(F)', 'Minutes of the governing council meeting-reflecting the research promotions', '2021-22'),
(3, '3.1.2', 'Details of Seed money', '2021-22'),
(3, '3.1.3', 'Awards of teachers for research', '2021-22'),
(3, '3.2.1(A)', 'List of Grants received for research projects', '2021-22'),
(3, '3.2.1(B)', 'e-copies of grants sanctioned', '2021-22'),
(3, '3.2.2', 'List of teachers having research projects during the year', '2021-22'),
(3, '3.2.3', 'Number of teachers recognized as research guides', '2021-22'),
(3, '3.2.4', 'Number of departments having research projects', '2021-22'),
(3, '3.2.4(A)', 'Web Links to Funding Agencies', '2021-22'),
(3, '3.3.1(A)', 'MSME business incubation center – sanction letter', '2021-22'),
(3, '3.3.1(B)', 'AICTE sponsored EDC – Sanction letters and list of the activities conducted by EDC', '2021-22'),
(3, '3.3.1(C)', 'Dedicated centres for research', '2021-22'),
(3, '3.3.2', 'Detailed report including photos, resource persons etc.', '2021-22'),
(3, '3.4.1(A)', 'Research Advisory Committee', '2021-22'),
(3, '3.4.1(B)', 'Ethics Committee', '2021-22'),
(3, '3.4.1(C)', 'Inclusion of Research Ethics in the research methodology course work', '2021-22'),
(3, '3.4.1(D)', 'Anti Plagiarism software approved by JNTU', '2021-22'),
(3, '3.4.2(A)', 'List of Faculty along with the names of Research scholars', '2021-22'),
(3, '3.4.2(B)', 'Copy of the Registration letters/Joining letters', '2021-22'),
(3, '3.4.3', 'e-copies of publications', '2021-22'),
(3, '3.4.4', 'e-copies of books/book chapters', '2021-22'),
(3, '3.4.5', 'Bibliometrics of the publications based on average Citation Index', '2021-22'),
(3, '3.4.6', 'Bibliometrics of the publication-based h-Index of the University', '2021-22'),
(3, '3.5.1', 'Audited statements for Revenue generated from consultancy', '2021-22'),
(3, '3.5.2', 'Audited statements related to the expenditure on developing facilities/Training the staff', '2021-22'),
(3, '3.6.1(A)', 'List of NSS activities conducted year wise and number of students participated', '2021-22'),
(3, '3.6.1(B)', 'Covid-19 booster report', '2021-22'),
(3, '3.6.1(C)', 'MGNCRE Reports', '2021-22'),
(3, '3.6.1(D)', 'Community Radio report', '2021-22'),
(3, '3.6.1(E)', 'List of NCC activities conducted year wise and number of students participated', '2021-22'),
(3, '3.6.2', 'e-copies of awards and recognition for extension activities', '2021-22'),
(3, '3.6.3', 'Number of extension and outreach programmes conducted by the institution', '2021-22'),
(3, '3.6.4', 'Number of students participating in extension activities', '2021-22'),
(3, '3.7.1', 'e-copies of collaborative activities indicating nature of collaboration', '2021-22'),
(3, '3.7.2(A)', 'e-copies of functional MoUs', '2021-22'),
(3, '3.7.2(B)', 'e-copies of Activities of MOUs', '2021-22'),
(4, '4.1.1(A)', 'Details of the Classrooms, Labs & Other facilities across all the six blocks', '2021-22'),
(4, '4.1.1(B)', 'List of the laboratories with titles', '2021-22'),
(4, '4.1.1(C)', 'Campus LAN diagram', '2021-22'),
(4, '4.1.1(D)', 'Proof of bandwidth', '2021-22'),
(4, '4.1.1(E)', 'List of the software', '2021-22'),
(4, '4.1.1(F)', 'Photo gallery of all the academic blocks, classrooms, drawing hall, seminar hall, auditorium, and Labs', '2021-22'),
(4, '4.1.2(A)', 'Colleague of Geo-tagged pictures', '2021-22'),
(4, '4.1.2(B)', 'Area details of the all the facilities', '2021-22'),
(4, '4.1.2(C)', 'Photo gallery of the various activities', '2021-22'),
(4, '4.1.3(A)', 'Geo-tagged photographs of classrooms with ICT enabled facilities', '2021-22'),
(4, '4.1.3(B)', 'Class Timetables', '2021-22'),
(4, '4.1.4(A)', 'Budget allocation', '2021-22'),
(4, '4.1.4(B)', 'Provide the consolidated fund allocation towards infrastructure augmentation facilities duly certified by Head of the Institution', '2021-22'),
(4, '4.2.1(A)', 'Library operates through LIBSYS', '2021-22'),
(4, '4.2.1(B)', 'All the books are provided with RF Id security tags', '2021-22'),
(4, '4.2.1(C)', 'Copy of the latest License agreement of LIBSYS-7', '2021-22'),
(4, '4.2.1(D)', 'Digital Library', '2021-22'),
(4, '4.2.2 (D)', 'Specific details in respect of e-resources selected.', '2021-22'),
(4, '4.2.2 (E)', 'Databases', '2021-22'),
(4, '4.2.2(A)', 'Details of subscriptions of e-journals', '2021-22'),
(4, '4.2.2(B)', 'Letter of subscription', '2021-22'),
(4, '4.2.2(C)', 'Screenshots of the facilities claimed with the name of HEI.', '2021-22'),
(4, '4.2.3(A)', 'Consolidated extract of expenditure', '2021-22'),
(4, '4.2.3(B)', 'Invoices of all the expenditure', '2021-22'),
(4, '4.2.4(A)', 'Certified e-copy of the ledger for footfalls for 5days', '2021-22'),
(4, '4.2.4(B)', 'Certified screenshots of the data for the same 5 days for online access', '2021-22'),
(4, '4.2.4(C)', 'Last page of accession register details', '2021-22'),
(4, '4.3.1(A)', 'Colleague of Geo-tagged pictures', '2021-22'),
(4, '4.3.1(B)', 'Policy document', '2021-22'),
(4, '4.3.1(C)', 'Campus Wi-Fi/Network diagram', '2021-22'),
(4, '4.3.1(D)', 'Budget of the year 2020-21', '2021-22'),
(4, '4.3.2(A)', 'Computer Bills', '2021-22'),
(4, '4.3.2(B)', 'Student strength', '2021-22'),
(4, '4.3.3(A)', 'Details of available bandwidth of internet connection in the Institution', '2021-22'),
(4, '4.3.3(B)', 'Bills for any one month/one quarter.', '2021-22'),
(4, '4.3.3(C)', 'e-copy of document of agreement with the service provider.', '2021-22'),
(4, '4.3.4(A)', 'Geo tagged photographs of Media Centre, Audio Visual Centre etc.,', '2021-22'),
(4, '4.3.4(B)', 'Purchase bills for Lecture Capturing System', '2021-22'),
(4, '4.3.4(C)', 'Audited income expenditure statement highlighting the relevant expenditure.', '2021-22'),
(4, '4.4.1(A)', 'Audited statements of accounts.', '2021-22'),
(4, '4.4.2(A)', 'Schedules of Library, Sport complex ', '2021-22'),
(4, '4.4.2(B)', 'Laboratory Timetables', '2021-22'),
(4, '4.4.2(C)', 'SOP for Laboratory utilization', '2021-22'),
(4, '4.4.2(D)', 'SOP for usage of general amenities', '2021-22'),
(4, '4.4.2(E)', 'Geo-tagged photos of GAMYA', '2021-22'),
(4, '4.4.2(F)', 'Maintenance schedules and AMC letters from Estate department', '2021-22'),
(5, '5.1.1', 'Sanction letter of scholarship', '2021-22'),
(5, '5.1.2', 'List of students benefitted and award of scholarships', '2021-22'),
(5, '5.1.3', 'Capacity Development and Skill Enhancement Activities ', '2021-22'),
(5, '5.1.3(A)', 'Soft Skill, Language and Communication', '2021-22'),
(5, '5.1.3(B)', 'Yoga Class', '2021-22'),
(5, '5.1.3(C)', 'Awareness of Trends in technology', '2021-22'),
(5, '5.1.4(A)', 'Guidance for Competitive Exams', '2021-22'),
(5, '5.1.4(B)', 'Career Counselling', '2021-22'),
(5, '5.1.5', 'Redressal of Students Grievances', '2021-22'),
(5, '5.2.1', 'List of outgoing students got placement', '2021-22'),
(5, '5.2.2', 'List of outgoing students progressing to higher education', '2021-22'),
(5, '5.2.3(A)', 'List of Students qualifying in competitive Examinations', '2021-22'),
(5, '5.2.3(B)', 'List of students appeared in competitive Examinations', '2021-22'),
(5, '5.3.1', 'Number of awards/medals for outstanding performance in sports and/or cultural activities', '2021-22'),
(5, '5.3.2', 'Proof of representation of the students in academic and non-academic bodies (AMC, EDC, WEC, Placement Committee, Anti-ragging Committee, Food Committee, Professional Bodies – respective files', '2021-22'),
(5, '5.3.3', 'Number of sports and cultural events / competitions organized by the institution', '2021-22'),
(5, '5.4.1', 'Proof of Alumni registration, Proof of alumni chapter activities conducted and details of the number of students participated, Number of alumni registered as on date (Portal directory), Alumni contributions Representation in the BoS, Guest lectures/GRAD talks', '2021-22'),
(5, '5.4.2', 'Alumni’s financial contribution during the year', '2021-22'),
(6, '6.1.1(A)', 'Academic Monitoring Committee meeting minutes', '2021-22'),
(6, '6.1.1(B)', 'Placement Committee meeting minutes', '2021-22'),
(6, '6.1.1(C)', 'SAC - coordinators meeting minutes', '2021-22'),
(6, '6.1.1(D)', 'Anti Ragging Committee meeting minutes', '2021-22'),
(6, '6.1.1(E)', 'IQAC meeting minutes', '2021-22'),
(6, '6.1.1(F)', 'Board of Studies Meeting minutes', '2021-22'),
(6, '6.1.1(G)', 'Academic Council meeting minutes', '2021-22'),
(6, '6.1.1(H)', 'Governing Council meeting minutes', '2021-22'),
(6, '6.1.1(I)', 'HOD/Academic Development Committee Meeting Minutes', '2021-22'),
(6, '6.1.1(J)', 'Finance Committee meeting minutes', '2021-22'),
(6, '6.1.1(K)', 'Library Committee meeting minutes', '2021-22'),
(6, '6.1.1(L)', 'Town Hall meeting minutes', '2021-22'),
(6, '6.1.2(A)', 'Strat-Plan', '2021-22'),
(6, '6.1.2(B)', 'Requisition for two set of mid question papers from CoE – e-mail proof', '2021-22'),
(6, '6.1.2(C)', 'Declaration of mid question paper set number from CoE– e-mail proof', '2021-22'),
(6, '6.1.2(D)', 'Uniform Evaluation', '2021-22'),
(6, '6.1.2(E)', 'Research Review meeting minutes', '2021-22'),
(6, '6.2.1(A)', 'Strat-Plan', '2021-22'),
(6, '6.2.1(B)', 'Merit scholarships based on AP-EAMCET rank', '2021-22'),
(6, '6.2.1(C)', 'Meritorious scholarships for students', '2021-22'),
(6, '6.2.1(D)', 'Details of the GATE training classes conducted; List of students attended GATE Coaching & secured score', '2021-22'),
(6, '6.2.1(E)', 'Details of the CRT programs/ Technical training/Competitions conducted (Total number of hours), the List of the students attended the training programs & Proof of attendance, Branch wise list of the students placed, and the number of companies visited. List of students attended the WTN.', '2021-22'),
(6, '6.2.1(F)', 'Motivational and inspirational talks by the industry experts', '2021-22'),
(6, '6.2.1(G)', 'Social media updates', '2021-22'),
(6, '6.2.2(A)', 'Organogram', '2021-22'),
(6, '6.2.2(B)', 'HR & Service rules/Incentive policies', '2021-22'),
(6, '6.2.2(D)', 'Frequency and conduct of the meetings of governance committees (GC, AC, BOS, FC)', '2021-22'),
(6, '6.2.2(E)', 'SOP for procurement (AOP, MRN, Comparative statements and Purchase Orders)', '2021-22'),
(6, '6.3.1(A)', 'HR Policies (Welfare & Career development)', '2021-22'),
(6, '6.3.1(B)', 'Details of the training programs conducted for teaching & non-teaching staff', '2021-22'),
(6, '6.3.1(C)', 'Details of the staff (Teaching & Non-teaching promoted)', '2021-22'),
(6, '6.3.1(D)', 'Details of on campus housing, Term insurance, Medical insurance, Children education, ESI, Cooperative credit society, Concessions in IP/OP services and Gratuity', '2021-22'),
(6, '6.3.1(E)', 'Details of the faculty received incentives for completion of Ph.D./ QIP', '2021-22'),
(6, '6.3.2(A)', 'Number of teachers provided with financial support to attend conferences / workshops and towards payment of membership fee of professional bodies during the year', '2021-22'),
(6, '6.3.3(A)', 'Annual Reports highlighting training programs conducted for teaching & non teaching', '2021-22'),
(6, '6.3.3(B)', 'Training programs conducted for teaching & non-teaching', '2021-22'),
(6, '6.3.4(A)', 'List of Faculty FDPS Attended & Proofs', '2021-22'),
(6, '6.5.1(A)', 'List of companies hosted Internship', '2021-22'),
(6, '6.5.1(B)', 'FADS – Supporting Docs', '2021-22'),
(6, '6.5.1(C)', 'Consolidated initiatives of IQAC (strategies & contributions of IQAC mentioned)', '2021-22'),
(6, '6.5.2(A)', 'List of the IQAC initiatives taken up to enhance the students’ performance (Course coordinator meetings, AMC meeting minutes, Remedial classes for slow learners)', '2021-22'),
(6, '6.5.2(B)', 'Details of the progression of the students from 1st to 8th semesters branch wise for 2020-21 for all the batches graduated – 1 Bar chart, in each bar chart with eight bars', '2021-22'),
(6, '6.5.2(C)', 'Structures & methodologies of operations ISO audits, Academic audits -Internal & External branch wise', '2021-22'),
(6, '6.5.2(D)', 'Internal & External Academic Audit Report', '2021-22'),
(6, '6.5.3(A)', 'IQAC meeting minutes', '2021-22'),
(6, '6.5.3(B)', 'Feedback system of the institution', '2021-22'),
(6, '6.5.3(C)', 'Feedback system for design and review of syllabus', '2021-22'),
(6, '6.5.3(D)', 'Collaborative quality initiatives', '2021-22'),
(6, '6.5.3(E)', 'Participation in NIRF', '2021-22'),
(6, '6.5.3(F)', 'NBA accreditation', '2021-22'),
(6, '6.5.3(G)', 'NAAC accreditation', '2021-22'),
(6, '6.5.3(H)', 'IQAC Feedback analysis', '2021-22'),
(7, '7.1.1(A)', 'Action plan of WEC for gender sensitization', '2021-22'),
(7, '7.1.1(B)', 'Campus surveillance with CC TV (Audit report)', '2021-22'),
(7, '7.1.1(C)', 'Policy for women security and safety (PASH)', '2021-22'),
(7, '7.1.1(D)', 'Photographs of Exclusive reading rooms, waiting rooms and rest rooms', '2021-22'),
(7, '7.1.1(E)', 'Day care centre for the kids (Beneficiaries)', '2021-22'),
(7, '7.1.1(F)', 'Welfare measures (Maternity leave for two kids)', '2021-22'),
(7, '7.1.1(G)', 'List of the beneficiaries', '2021-22'),
(7, '7.1.10(A)', 'Details of the monitoring committee composition and minutes of the committee meeting, number of programmes organized, reports on the various programmes, etc. in support of the claims', '2021-22'),
(7, '7.1.10(B)', 'Policy document on code of ethics', '2021-22'),
(7, '7.1.10(C)', 'Circulars and geo tagged photographs and caption of the activities organized under the metric for teachers, students, administrators and other staffs', '2021-22'),
(7, '7.1.11(A)', 'Annual report of the celebrations and commemorative events', '2021-22'),
(7, '7.1.11(B)', 'Photographs of some of the events', '2021-22'),
(7, '7.1.2(A)', 'Geo tagged photographs with caption of the facilities', '2021-22'),
(7, '7.1.2(B)', 'Bills for the purchase of equipment for the facilities', '2021-22'),
(7, '7.1.2(C)', 'Permission document for connection to the grid from Government', '2021-22'),
(7, '7.1.3(A)', 'Geo tagged photographs of the facilities', '2021-22'),
(7, '7.1.3(B)', 'SOP for solid waste management', '2021-22'),
(7, '7.1.4(A)', 'Geo tagged photographs with caption of the facilities', '2021-22'),
(7, '7.1.4(B)', 'Bills for the purchase of equipment', '2021-22'),
(7, '7.1.5(A)', 'Policy document on the green campus', '2021-22'),
(7, '7.1.5(B)', 'Geo tagged photographs/Videos with caption of the facilities', '2021-22'),
(7, '7.1.5(C)', 'Circulars for the implementation of the initiatives and any other supporting document', '2021-22'),
(7, '7.1.6(A)', 'Policy document on environment and energy usage', '2021-22'),
(7, '7.1.6(B)', 'Certificate from the auditing agency', '2021-22'),
(7, '7.1.6(C)', 'Certificates of the awards received from the recognized agency', '2021-22'),
(7, '7.1.6(D)', 'Report on environmental promotional activities conducted beyond the campus', '2021-22'),
(7, '7.1.6(F)', 'Green audit report', '2021-22'),
(7, '7.1.7(A)', 'Policy document and information brochure', '2021-22'),
(7, '7.1.7(B)', 'Geo tagged photos', '2021-22'),
(7, '7.1.7(C)', 'Bills and invoice/purchase order/AMC', '2021-22'),
(7, '7.1.7(D)', 'A rest room should include specific requirements of Divyangjan', '2021-22'),
(7, '7.1.7(E)', 'Bills for the software procured', '2021-22'),
(7, '7.1.8(A)', 'List of the outbound programs', '2021-22'),
(7, '7.1.8(B)', 'List of national level activities (Cultural, Sports, Academic, Cultural & Sports)', '2021-22'),
(7, '7.1.8(C)', 'List of the faculty & students coming from the out of state', '2021-22'),
(7, '7.1.9(A)', 'List of the activities/Events conducted, and the number of students present. (Awareness programs on Women safety & protection, Anti ragging, Judicial rights, Gender equality, Traffic rules, Environment protection, Conservation of natural resources (power & water)) >', '2021-22'),
(7, '7.2.1', 'Institutional Values and Best Practices', '2021-22'),
(7, '7.3.1', 'Institutional Distinctiveness', '2021-22');

-- --------------------------------------------------------

--
-- Table structure for table `fdps_tab`
--

CREATE TABLE `fdps_tab` (
  `id` int(255) NOT NULL,
  `username` varchar(200) NOT NULL,
  `branch` varchar(100) NOT NULL,
  `title` varchar(200) NOT NULL,
  `date_from` date NOT NULL,
  `date_to` date NOT NULL,
  `organised_by` varchar(200) NOT NULL,
  `location` varchar(200) NOT NULL,
  `certificate` varchar(200) NOT NULL,
  `submission_time` varchar(300) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `fdps_tab`
--

INSERT INTO `fdps_tab` (`id`, `username`, `branch`, `title`, `date_from`, `date_to`, `organised_by`, `location`, `certificate`, `submission_time`) VALUES
(5, 'chandru', '', 'fdps_file1', '2025-01-10', '2025-01-31', 'gmr', 'rajam', 'uploads/certificates/WhatsApp Image 2024-12-31 at 23.39.09_5ad12fb7.jpg', '2025-01-03 16:13:34'),
(7, 'chandu', 'AIML', 'paper1', '2025-01-01', '2025-01-31', 'gmrit', 'rajam', 'uploads/certificates/gmr_landing_page (1).jpg', '2025-01-04 14:11:31');

-- --------------------------------------------------------

--
-- Table structure for table `files`
--

CREATE TABLE `files` (
  `id` int(255) NOT NULL,
  `UserName` varchar(30) NOT NULL,
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
  `criteria_no` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `files`
--

INSERT INTO `files` (`id`, `UserName`, `academic_year`, `branch`, `sem`, `section`, `faculty_name`, `ext_or_int`, `uploaded_at`, `file_name`, `file_path`, `criteria`, `criteria_no`) VALUES
(37, 'c', '2022-23', 'AIDS', 3, 'section C', 'sekhar', '', '2024-09-18 18:13:26.000000', 'lll', 'uploads/310e358c8e9641e7fba44c80ea045207.jpg', 6, '6.1.1(A)'),
(38, 'c', '2022-23', 'CSE', 5, 'A', 'ram', '', '2024-09-19 15:42:10.000000', 'photo', 'uploads/57deff5ab072e37be9327eb4ba2ab1cf.jpg', 6, '6.1.1(A)'),
(39, 'c', '2022-23', 'CSE', 6, 'C', 'rams', '', '2024-09-19 16:55:45.000000', 'sldjkf', 'uploads/a125d447-c9bc-49f4-9619-6a360ea9fde9.svg', 6, '6.1.1(A)'),
(40, 'ram1', '2022-23', 'MECH', 5, '', 'ram', '', '2024-09-19 19:43:37.000000', 'file1', 'uploads/57deff5ab072e37be9327eb4ba2ab1cf.jpg', 6, '6.1.1(B)'),
(41, 'ram1', '2022-23', 'CSE', 5, '', 'chandu', '', '2024-09-19 20:09:50.000000', 'placement_file', 'uploads/AIML EXP 4.pdf', 6, '6.1.1(B)'),
(42, 'ram1', '2022-23', 'CSE', 5, '', 'ram', '', '2024-09-19 20:11:03.000000', 'start-plan', 'uploads/lab_handout_new.pdf', 6, '6.1.2(A)'),
(43, 'keshav', '2022-23', 'CSE', 6, '', 'keshav', '', '2024-09-20 15:59:45.000000', 'SAC', 'uploads/lab_handout_new.pdf', 6, '6.1.1(C)'),
(44, 'keshav', '2022-23', 'AIML', 3, 'c', 'chandu', '', '2024-09-20 16:54:31.000000', 'aa', 'uploads/lab_handout_new.pdf', 6, '6.1.1(A)'),
(45, 'keshav', '2022-23', 'AIML', 5, '', 'aa', '', '2024-09-20 16:58:20.000000', 'kk', 'uploads/lab handout.pdf', 6, '6.1.1(C)'),
(46, 'keshav', '2022-23', 'CSE', 6, 'section A', 'chandu', '', '2024-09-20 17:19:03.000000', 'AMC', 'uploads/lab_handout_new.pdf', 6, '6.1.1(A)'),
(47, 'keshav', '2022-23', 'CSE', 5, 'aa', 'aa', '', '2024-09-20 17:31:00.000000', 'aa', 'uploads/seattle-weather.csv', 6, '6.1.1(A)'),
(48, 'c', '2022-23', 'CSE', 5, 'section B', 'chandu', '', '2024-09-20 17:43:08.000000', 'AMC minutes', 'uploads/AIML EXP 4.pdf', 6, '6.1.1(A)'),
(53, 'keshav', '2022-23', '', 0, '', 'tri', '', '2024-09-20 19:35:30.000000', 'lskfj', 'uploads/lab handout.pdf', 6, '6.1.1(G)'),
(55, 'keshav', '2022-23', '', 0, '', 'chandu', '', '2024-09-20 19:43:06.000000', 'aldfkj', 'uploads/lab_handout_new.pdf', 6, '6.1.1(D)'),
(56, 'keshav', '2022-23', '', 0, '', 'skfjd', '', '2024-09-20 19:43:36.000000', 'lsdkf', 'uploads/lab handout.pdf', 6, '6.1.1(E)'),
(57, 'keshav', '2022-23', '', 0, '', 'chandu', 'Internal', '2024-09-20 20:32:48.000000', 'bod', 'uploads/lab_handout_new.pdf', 6, '6.1.1(F)'),
(58, 'keshav', '2022-23', 'AIML', 0, '', 'slkfj', 'Internal', '2024-09-20 20:43:09.000000', 'sklfhjskjf', 'uploads/lab handout.pdf', 6, '6.1.1(F)'),
(59, 'keshav', '2022-23', 'CSE', 0, '', 'chandu', 'Internal', '2024-09-20 21:40:16.000000', 'sklf', 'uploads/lab handout.pdf', 6, '6.1.1(F)'),
(60, 'keshav', '2022-23', 'AIML', 0, '', 'chandu', 'External', '2024-09-20 21:42:31.000000', 'skfj', 'uploads/lab handout.pdf', 6, '6.1.1(F)'),
(61, 'keshav', '2022-23', 'AIML', 0, '', 'chandu', 'Internal', '2024-09-20 21:44:52.000000', 'slfkj', 'uploads/lab handout.pdf', 6, '6.1.1(F)'),
(63, 'keshav', '2022-23', '', 0, '', 'ram', '', '2024-10-04 15:48:25.000000', 'pfile', 'uploads/lab_handout_new.pdf', 6, '6.5.3(H)'),
(64, '1234', '2022-23', '', 0, '', 'kkm', '', '2024-10-04 16:25:39.000000', 'f1', 'uploads/AIML EXP 4.pdf', 1, '1.1.1'),
(65, 'keshav', '2022-23', '', 0, '', 'keh', '', '2024-10-04 20:03:15.000000', 'ff1', 'uploads/AIML EXP 4.pdf', 1, '1.1.1'),
(66, 'keshav', '2022-23', '', 0, '', 'keshav', '', '2024-10-04 20:03:48.000000', 'ff2', 'uploads/lab handout.pdf', 1, '1.1.2(A)'),
(67, 'chandu', '2022-23', '', 0, '', 'sekhar', '', '2025-01-03 16:31:17.000000', 'kk', 'uploads/gmr_landing_page.jpg', 1, '1.1.1');

-- --------------------------------------------------------

--
-- Table structure for table `login_pg`
--

CREATE TABLE `login_pg` (
  `userid` varchar(30) NOT NULL,
  `password` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `login_pg`
--

INSERT INTO `login_pg` (`userid`, `password`) VALUES
('chandu123', '123'),
('chandu123', '123'),
('ram123', '123'),
('sekh123', '123'),
('sekh123', '123'),
('sekh123', '123'),
('k123', '123'),
('k123', '123'),
('c', '123'),
('c', '123'),
('c', '123'),
('chandu123', '123'),
('c', '123'),
('c', '123'),
('c', '123'),
('vinnu123', '123'),
('c', '123'),
('c', '123'),
('c', '123'),
('c', '123'),
('c', '123'),
('c', '123'),
('c', '123'),
('c', '123'),
('ram1', '123'),
('c', '123'),
('c', '123'),
('c', '123'),
('c', '123'),
('ram1', '123'),
('ram1', '123'),
('ram1', '123'),
('ram1', '123'),
('ram1', '123'),
('keshav', '123'),
('keshav', '123'),
('keshav', '123'),
('keshav', '123'),
('keshav', '123'),
('keshav', '123'),
('c', '123'),
('c', '123'),
('keshav', '123'),
('keshav', '123'),
('keshav', '123'),
('keshav', '123'),
('keshav', '123'),
('keshav', '123'),
('keshav', '123'),
('keshav', '123'),
('keshav', '123'),
('keshav', '123'),
('keshav', '123'),
('keshav', '123'),
('keshav', '123'),
('keshav', '123'),
('keshav', '123'),
('keshav', '123'),
('keshav', '123'),
('keshav', '123'),
('1234', '123456'),
('ram12', '123'),
('keshav', '123'),
('keshav', '123'),
('keshav', '123'),
('keshav', '123'),
('keshav', '123'),
('keshav', '123'),
('keshav', '123'),
('c', '123'),
('c', '123'),
('c', '123'),
('chandu', '123'),
('chandu', '123'),
('chandu', '123'),
('chandu', '123'),
('chandu', '123'),
('chandu', '123'),
('chandu', '123'),
('chandu', '123'),
('chandu', '123'),
('chandu', '123'),
('chandu', '123'),
('chandu', '123'),
('chandu', '123'),
('chandu', '123'),
('chandu', '123'),
('chandu', '123'),
('chandu', '123'),
('chandu', '123'),
('chandu', '123'),
('chandu', '123'),
('chandu', '123'),
('chandu', '123'),
('chandu', '123'),
('chandu', '123'),
('chandu', '123'),
('chandu', '123'),
('chandu', '123'),
('chandu', '123'),
('chandu', '123'),
('chandu', '123'),
('chandu', '123'),
('chandu', '123'),
('chandu', '123'),
('chandu', '123'),
('chandu', '123'),
('chandu', '123'),
('chandu', '123'),
('chandu', '123'),
('chandu', '123'),
('chandu', '123'),
('chandru', '123'),
('chandu', '123'),
('chandu', '123'),
('chandu', '123'),
('chandu', '123'),
('chandu', '123');

-- --------------------------------------------------------

--
-- Table structure for table `patents_table`
--

CREATE TABLE `patents_table` (
  `id` int(255) NOT NULL,
  `Username` varchar(300) NOT NULL,
  `branch` varchar(100) NOT NULL,
  `patent_title` varchar(300) NOT NULL,
  `date_of_issue` varchar(100) NOT NULL,
  `patent_file` varchar(400) NOT NULL,
  `submission_time` varchar(300) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `patents_table`
--

INSERT INTO `patents_table` (`id`, `Username`, `branch`, `patent_title`, `date_of_issue`, `patent_file`, `submission_time`) VALUES
(1, '', '', 'my_patent', '', '', ''),
(2, '', '', 'my_patent', '', '', ''),
(4, 'chandru', '', 'chandu', '', '', ''),
(5, 'chandru', '', 'chandu', '', '', ''),
(6, 'chandru', '', 'title1', '2025-01-01', 'uploads/patents/WhatsApp Image 2024-12-31 at 23.39.10_29ad3666.jpg', '2025-01-03 16:21:51'),
(7, 'chandru', '', 'my_patent1', '2025-01-01', 'uploads/patents/WhatsApp Image 2024-12-31 at 23.39.10_29ad3666.jpg', '2025-01-03 16:33:05'),
(8, 'chandru', '', 'my_patent', '2025-01-16', 'uploads/patents/WhatsApp Image 2024-12-31 at 23.39.08_bcd087b0.jpg', '2025-01-03 16:34:02'),
(9, 'chandru', '', 'my_patent', '2025-01-16', 'uploads/patents/WhatsApp Image 2024-12-31 at 23.39.08_bcd087b0.jpg', '2025-01-03 16:34:13'),
(10, 'chandru', 'CSE', 'new patentt', '2025-01-10', 'uploads/patents/WhatsApp Image 2024-12-31 at 23.39.08_19d40353.jpg', '2025-01-03 16:35:25'),
(11, 'chandu', 'AIML', 'my_patent', '2025-01-09', 'uploads/patents/gmr_landing_page (1).jpg', '2025-01-04 14:49:19');

-- --------------------------------------------------------

--
-- Table structure for table `published_tab`
--

CREATE TABLE `published_tab` (
  `id` int(255) NOT NULL,
  `username` varchar(200) NOT NULL,
  `branch` varchar(100) NOT NULL,
  `paper_title` varchar(200) NOT NULL,
  `journal_name` varchar(200) NOT NULL,
  `indexing` varchar(100) NOT NULL,
  `date_of_submission` date NOT NULL,
  `quality_factor` decimal(10,0) NOT NULL,
  `impact_factor` decimal(10,0) NOT NULL,
  `payment` varchar(200) NOT NULL,
  `submission_time` varchar(300) NOT NULL,
  `paper_file` varchar(400) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `published_tab`
--

INSERT INTO `published_tab` (`id`, `username`, `branch`, `paper_title`, `journal_name`, `indexing`, `date_of_submission`, `quality_factor`, `impact_factor`, `payment`, `submission_time`, `paper_file`) VALUES
(2, 'chandu', '', 'xyz', 'aaa', 'sci', '2024-11-18', 0, 0, 'free', '', ''),
(3, 'chandru', '', 'paper1', 'chandu_journal', 'scie', '2025-01-08', 0, 23, 'paid', '', ''),
(4, 'chandru', '', 'jour_title', 'name', 'scopus', '2025-01-14', 23, 12, 'free', '2025-01-03 16:14:38', ''),
(5, 'chandu', 'AIML', 'papern', 'aaa', 'scopus', '2025-01-31', 23, 23, 'free', '2025-01-04 19:53:38', 'uploads/677983b21263c7.43845631.pdf');

-- --------------------------------------------------------

--
-- Table structure for table `reg_pg`
--

CREATE TABLE `reg_pg` (
  `name` varchar(20) NOT NULL,
  `userid` varchar(20) NOT NULL,
  `email` varchar(30) NOT NULL,
  `password` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reg_pg`
--

INSERT INTO `reg_pg` (`name`, `userid`, `email`, `password`) VALUES
('kkm', '1234', 'aa@gmail.com', '123456'),
('c', 'c', 'c1@gmail.com', '123'),
('chandu', 'chandu123', 'chandu@gmail.com', '123'),
('kalyan', 'k123', 'kalyan@gmail.com', '123'),
('keshav', 'keshav', 'keshav123@gmail.com', '123'),
('ram', 'r123', 'ram1@gmail.com', '123'),
('ram', 'ram1', 'ram@gmai.com', '123'),
('ram', 'ram12', 'ram12@gmail.com', '123'),
('sriram', 'ram123', 'sek@gmail.colm', '123'),
('ram', 'ram143', 'ram143@gmail.com', '123'),
('chandu', 'sekh123', 'sekhar@gmail.com', '123'),
('vinnu', 'vinnu123', 'v@gmail.com', '123');

-- --------------------------------------------------------

--
-- Table structure for table `reg_tab`
--

CREATE TABLE `reg_tab` (
  `emp_name` varchar(200) NOT NULL,
  `emp_no` varchar(20) NOT NULL,
  `dept` varchar(200) NOT NULL,
  `date_of_joining` date NOT NULL,
  `aadhar` int(12) NOT NULL,
  `pan` varchar(10) NOT NULL,
  `phone` int(10) NOT NULL,
  `address` varchar(200) NOT NULL,
  `orc_id` varchar(20) NOT NULL,
  `scopus_id` varchar(20) NOT NULL,
  `vidwan_id` varchar(20) NOT NULL,
  `def_id` varchar(20) NOT NULL,
  `userid` varchar(50) NOT NULL,
  `password` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reg_tab`
--

INSERT INTO `reg_tab` (`emp_name`, `emp_no`, `dept`, `date_of_joining`, `aadhar`, `pan`, `phone`, `address`, `orc_id`, `scopus_id`, `vidwan_id`, `def_id`, `userid`, `password`) VALUES
('sdfj', '777', 'AIML', '2024-11-06', 2147483647, 'AKDIK1234K', 1234839384, 'qer', 'wer', 'qe', 'qwer', 'qwer', 'aa', '$2y$10$EGIUDhwEq1Z/QEZsyfu72udHjVjo9Ffzy.ikLEBxMaugfuLu2nxQu'),
('sdfj', '777', 'AIML', '2024-11-06', 2147483647, 'AKDIK1234K', 1234839384, 'qer', 'wer', 'qe', 'qwer', 'qwer', 'aaa', '$2y$10$YtdSAx3EeiMRj3IR0R5px.fDeLOVTGw.cfvOP4RK7Z6qJ/oHy513e'),
('sdfj', '777', 'AIML', '2024-11-06', 2147483647, 'AKDIK1234K', 1234839384, 'qer', 'wer', 'qe', 'qwer', 'qwer', 'aaaa', '$2y$10$lZOIeEwYYircP0P5rOThJOjRYAEer/0iR0A3hgQNn.3YIOYs66U6m'),
('chandu', '123', 'AIDS', '2024-11-07', 2147483647, 'AKDIK1234K', 2147483647, 'qer', 'wer', 'qe', 'qwer', 'qwer', 'bb', '$2y$10$qSX/P.eLu01T7XJwy/VmyeVDtGmfCWt0PDhLZTizh.pEebiBP1fYq'),
('chandu', '52', 'CSE', '2025-01-03', 2147483647, 'AKDIK1234K', 2147483647, 'srikakulam', '123', '123r', '123e', '123r', 'chandru', '123'),
('sdfj', '22', 'AIML', '2024-10-30', 2147483647, 'AKDIK1234K', 2147483647, 'qer', 'wer', 'qe', 'qwer', 'qwer', 'chandu', '123');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `a_files`
--
ALTER TABLE `a_files`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `conference_tab`
--
ALTER TABLE `conference_tab`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `criteria`
--
ALTER TABLE `criteria`
  ADD PRIMARY KEY (`Sub_no`);

--
-- Indexes for table `criteria1`
--
ALTER TABLE `criteria1`
  ADD PRIMARY KEY (`Sub_no`);

--
-- Indexes for table `criteria2`
--
ALTER TABLE `criteria2`
  ADD PRIMARY KEY (`Sub_no`);

--
-- Indexes for table `fdps_tab`
--
ALTER TABLE `fdps_tab`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `files`
--
ALTER TABLE `files`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `patents_table`
--
ALTER TABLE `patents_table`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `published_tab`
--
ALTER TABLE `published_tab`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reg_pg`
--
ALTER TABLE `reg_pg`
  ADD PRIMARY KEY (`userid`,`email`);

--
-- Indexes for table `reg_tab`
--
ALTER TABLE `reg_tab`
  ADD PRIMARY KEY (`userid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `a_files`
--
ALTER TABLE `a_files`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `conference_tab`
--
ALTER TABLE `conference_tab`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `fdps_tab`
--
ALTER TABLE `fdps_tab`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `files`
--
ALTER TABLE `files`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;

--
-- AUTO_INCREMENT for table `patents_table`
--
ALTER TABLE `patents_table`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `published_tab`
--
ALTER TABLE `published_tab`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
