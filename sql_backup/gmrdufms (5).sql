-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 21, 2026 at 06:38 AM
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
-- Database: `gmrdufms`
--

-- --------------------------------------------------------

--
-- Table structure for table `academic_years`
--

CREATE TABLE `academic_years` (
  `year_id` int(11) NOT NULL,
  `year_label` varchar(20) NOT NULL,
  `is_current` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `academic_years`
--

INSERT INTO `academic_years` (`year_id`, `year_label`, `is_current`, `created_at`) VALUES
(1, '2024-25', 0, '2026-10-30 18:09:41'),
(2, '2023-24', 0, '2026-10-30 18:09:41'),
(3, '2025-26', 1, '2026-10-30 18:09:41'),
(5, '2026-27', 0, '2026-10-30 18:09:41');

-- --------------------------------------------------------

--
-- Table structure for table `a_cri_files`
--

CREATE TABLE `a_cri_files` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `academic_year` varchar(50) NOT NULL,
  `criteria` varchar(100) NOT NULL,
  `criteria_no` varchar(500) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `Faculty_name` varchar(150) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `a_c_files`
--

CREATE TABLE `a_c_files` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `academic_year` varchar(20) NOT NULL,
  `criteria` varchar(100) NOT NULL,
  `criteria_no` varchar(50) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `Faculty_name` varchar(255) NOT NULL,
  `Description` varchar(2000) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `a_files`
--

CREATE TABLE `a_files` (
  `id` int(255) NOT NULL,
  `username` varchar(200) NOT NULL,
  `academic_year` varchar(200) NOT NULL,
  `Dept` varchar(255) NOT NULL,
  `criteria` int(100) NOT NULL,
  `criteria_no` varchar(200) NOT NULL,
  `uploaded_at` timestamp NULL DEFAULT NULL,
  `Faculty_name` varchar(200) NOT NULL,
  `Description` varchar(1000) NOT NULL,
  `file_name` varchar(200) NOT NULL,
  `file_path` varchar(300) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `central_files`
--

CREATE TABLE `central_files` (
  `id` int(11) NOT NULL,
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
  `photo4` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `conference_tab`
--

CREATE TABLE `conference_tab` (
  `id` int(255) NOT NULL,
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
  `rejection_reason` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `conf_org_tab`
--

CREATE TABLE `conf_org_tab` (
  `id` int(11) NOT NULL,
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
  `rejection_reason` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(1, '1.1.2', 'Number of Programmes where syllabus revision was carried out during the year(Data Requirement: Programme Code ,Names of the Programmes revised)', '2022-23'),
(1, '1.1.2(A)', 'List of programs where syllabus revision has been carried out signed by the Principal', '2022-23'),
(1, '1.1.2(B)', 'Approved Minutes of relevant Academic Council/BOS meetings highlighting the specific agenda item relevant to the metric year wise', '2022-23'),
(1, '1.1.3', 'Number of courses focusing on employability/entrepreneurship/ skill development  offered by the Institution during the year(Data Requirement: Name of the Course with Course Code ,Name of the Programme ,Activities which have a direct bearing on employability/ entrepreneurship/ skill  development)', '2022-23'),
(1, '1.1.3(A)', 'Syllabus copy of the courses highlighting Focus on employability/entrepreneurship/ skill development along with their course outcomes.', '2022-23'),
(1, '1.1.3(B)', 'Minutes of the Boards of Studies/ Academic Council meetings with approval for these courses.', '2022-23'),
(1, '1.1.3(C)', 'List of MoUs', '2022-23'),
(1, '1.2.1', 'Number of new courses introduced across all programmes offered during the year(Data Requirement: Name of the newly introduced course (s)  ,Name of the Programme)', '2022-23'),
(1, '1.2.1(A)', 'List of new courses introduced program-wise during the assessment period certified by the Principal.', '2022-23'),
(1, '1.2.1(B)', 'Minutes of relevant Academic Council/BOS meetings highlighting the name of the new courses introduced', '2022-23'),
(1, '1.2.2', 'Number of Programmes offered through Choice Based Credit System  (CBCS)/Elective Course System(Data Requirement: ,Names of all Programmes offered through CBCS ,Names of all Programmes offered through Elective Course System)', '2022-23'),
(1, '1.2.2(A)', 'List of programs in which CBCS/Elective course system implemented in the last completed academic year certified by the principal', '2022-23'),
(1, '1.2.2(B)', 'Structure of the program clearly indicating courses, credits/Electives and Minutes of relevant Academic Council/BOS meetings highlighting the relevant documents to this metric', '2022-23'),
(1, '1.3.1', 'List and Description of courses relevant to Professional Ethics, Gender diversity and equality, Human Values, Environment and Sustainability, Women Empowerment introduced in the Curriculum along with the syllabus should be available in all the departments', '2022-23'),
(1, '1.3.2', 'Number of value-added courses for imparting transferable and life skills offered  during the year:(Data Requirement:  Names of the value-added courses (each with 30 or more contact hours) ,No. of times offered (for each value-added course) during the year ,Total number of students enrolled ,Total number of students completing the course during the year', '2022-23'),
(1, '1.3.2(A)', 'List of value-added courses which are optional and offered outside the curriculum of the programs with authorized sign.', '2022-23'),
(1, '1.3.2(B)', 'Brochure and Course content or syllabus along with course outcome of Value-added courses offered.', '2022-23'),
(1, '1.3.3', 'List of enrolled students for courses addressed in 1.3.2', '2022-23'),
(1, '1.3.4', 'List of students undertaking the field projects/ internships / student projects program-wise in the last completed academic year along with the details of title, place of work etc.', '2022-23'),
(1, '1.4.1', 'Sample Filled in feedback forms from the stakeholders to be provided.', '2022-23'),
(1, '1.4.2', 'Stakeholder feedback analysis report signed by the authority', '2022-23'),
(2, '2.1.1', 'Enrolment of Students ', '2022-23'),
(2, '2.1.1(A)', 'Students admitted(HEI signed documents)', '2022-23'),
(2, '2.1.1(B)', 'Enrolment Number_ AICTE letters of sanctioned intake', '2022-23'),
(2, '2.1.1(C)', 'Enrolment Number _ Ratified list of admitted students', '2022-23'),
(2, '2.1.2', 'Number of seats filled against reserved categories (SC, ST, OBC, Divyangjan,  etc.) as per the reservation policy during the year (exclusive of supernumerary  seats)', '2022-23'),
(2, '2.1.2(A)', 'Reservation seats categories to be considered as per the state rule (APSHE Guidelines)', '2022-23'),
(2, '2.1.2(B)', 'AP regulation for Admissions GO No. 73', '2022-23'),
(2, '2.1.2(C)', 'Guidelines for filling the left-over vacancies under reservation', '2022-23'),
(2, '2.1.2(D)', 'Category wise_ Ratified list of students admitted', '2022-23'),
(2, '2.1.2(E)', 'Admission Abstract(HEI signed documents)', '2022-23'),
(2, '2.2.1', 'The institution assesses students??? learning levels and organises special  programmes for both slow and advanced learners(Present a write-up within a maximum of 200 words)', '2022-23'),
(2, '2.2.1(A)', 'Video records of the specific topics(A document having the links and screen shots of video course/LCS)', '2022-23'),
(2, '2.2.1(B)', 'Classes for the slow learner(Time table conducted for CA failures and attendance sheets)', '2022-23'),
(2, '2.2.1(C)', 'Additional assignment(sample copies of either question papers or evaluated assignment)', '2022-23'),
(2, '2.2.1(D)', 'Make-up classes for course detention students/lateral entry students(Time tables and attendance sheets for each semester(1-8) at least one)', '2022-23'),
(2, '2.2.1(E)', 'Remedial classes conducted for end semester failures(Time tables, attendance sheets and track sheets for each semester(1-8) at least one)', '2022-23'),
(2, '2.2.1(F)', 'List of MOOCs courses(Details of student with course name and sample certificates)', '2022-23'),
(2, '2.2.1(G)', 'List of the advanced learners opted Minors and honors(Abstract certified by controller of examination)', '2022-23'),
(2, '2.2.1(H)', 'Student participation in technical events list with students event details and SAMPLE certificates', '2022-23'),
(2, '2.2.2', 'Student ??? Teacher (full-time) ratio(Data Requirement:  Total number of students in the institution ,Total number of full-time teachers in the institution)', '2022-23'),
(2, '2.2.2(A)', 'List of the full-time teachers', '2022-23'),
(2, '2.2.2(B)', 'Student intake', '2022-23'),
(2, '2.3.1', 'Student-centric methods such as experiential learning, participative learning and  problem-solving methodologies are used for enhancing learning experiences(Present a write-up within a maximum of 200 words.)', '2022-23'),
(2, '2.3.1(A)', 'Academic regulations and curriculum(Recent)', '2022-23'),
(2, '2.3.2', 'Teachers use ICT-enabled tools including online resources for effective teaching  and learning(Present a write-up within a maximum of 200 words.)', '2022-23'),
(2, '2.3.2 (A)', 'ICT Tools Gadgets viz. Graphic tablets, MST, Smart pen, Projector(Geotagged photos with title of photo)', '2022-23'),
(2, '2.3.3', 'Ratio of students to mentor for academic and other related issues', '2022-23'),
(2, '2.3.3(A)', 'Circular of mentor ??? mentees', '2022-23'),
(2, '2.3.3(B)', 'Mentor list as announced by the HEI', '2022-23'),
(2, '2.3.3(C)', 'issues raised and resolved in the mentor system(Total records of a student -&gt; one sample from each semester)', '2022-23'),
(2, '2.3.4', 'Preparation and adherence to Academic Calendar and Teaching Plans by the  institution(Describe the preparation of and adherence to the Academic Calendar and Teaching  Plans by the institution.  Present a write-up within a maximum of 200 words)', '2022-23'),
(2, '2.3.4(A)', 'Academic calendar', '2022-23'),
(2, '2.3.4(B)', 'Proof of course allotment for odd and even semesters', '2022-23'),
(2, '2.3.4(C)', 'Proof for lecture plan, Schedule and diary(one course from each semester)', '2022-23'),
(2, '2.3.4(D)', 'Minutes of AMC', '2022-23'),
(2, '2.4.1', 'Number of full-time teachers against sanctioned posts during the year(Data Requirement:  Number of full-time teachers ,Number of sanctioned posts)', '2022-23'),
(2, '2.4.1(A)', 'Sanction letter indicating number of posts', '2022-23'),
(2, '2.4.1(B)', 'Department wise List of full-time teachers appointed', '2022-23'),
(2, '2.4.2', 'Number of full-time teachers with PhD/ D.M. / M.Ch. / D.N.B Super-Specialty /  DSc / DLitt during the year(Data Requirement: List of full-time teachers with PhD/ D.M. / M.Ch. / D.N.B Super-Specialty /  DSc / DLitt.)', '2022-23'),
(2, '2.4.3', 'Total teaching experience of full-time teachers in the same institution(Data Requirement:  Name and number of full-time teachers and their years of teaching experience in the institution)', '2022-23'),
(2, '2.5.1', 'Number of days from the date of last semester-end/ year- end examination till the  declaration of results during the year(Number of days from the date of last semester-end / year-end examination till the  declaration of results year-wise during the year)', '2022-23'),
(2, '2.5.1(A)', 'Examination Result Notifications', '2022-23'),
(2, '2.5.1(B)', 'List of Programs offered and the last date of the latest semester end exams and date of result declaration', '2022-23'),
(2, '2.5.2', 'Number of students??? complaints/grievances against evaluation against the total  number who appeared in the examinations during the year', '2022-23'),
(2, '2.5.2(A)', 'Number of complaints', '2022-23'),
(2, '2.5.2(B)', 'Minutes of the examination Committee', '2022-23'),
(2, '2.5.3', 'IT integration and reforms in the examination procedures and processes  including Continuous Internal Assessment (CIA) have brought in considerable  improvement in the Examination Management System (EMS) of the Institution(Describe the examination reforms with reference to the following within a minimum  of 200 words :Examination procedures ,Processes/Procedures integrating IT ,Continuous Internal Assessment System) ', '2022-23'),
(2, '2.5.3(A)', 'Examination Regulations', '2022-23'),
(2, '2.5.3(B)', 'Question Paper templates', '2022-23'),
(2, '2.5.3(C)', 'Proof of the hybrid grading sheet', '2022-23'),
(2, '2.5.3(D)', 'Examination Results link in the website', '2022-23'),
(2, '2.5.3(E)', 'Proof of the OMR', '2022-23'),
(2, '2.6.1', 'Programme Outcomes and Course Outcomes for all Programmes offered by the  institution are stated and displayed on the website and communicated to teachers  and students(Describe Course Outcomes (COs) for all courses and the mechanism of  communication to teachers and students within a maximum of 200 words.  Upload COs for all Courses) ', '2022-23'),
(2, '2.6.1(A)', 'List of POs and COs(COs for at least two courses from each semester)', '2022-23'),
(2, '2.6.1(B)', 'Photo gallery of the COs & POs displayed Program wise', '2022-23'),
(2, '2.6.2', 'Attainment of Programme Outcomes and Course Outcomes as evaluated by the  institution(Describe the method of measuring the attainment of POs, PSOs and COs and the  level of attaiment of POs , PSOs and COs in not more than 200 words.)', '2022-23'),
(2, '2.6.2(A)', 'CO and PO attainment', '2022-23'),
(2, '2.6.2(B)', 'Files related to all surveys for the indirect assessment', '2022-23'),
(2, '2.6.3', 'Pass Percentage of students', '2022-23'),
(2, '2.6.3(A)', 'Annual report of CoE indicating the pass percentage', '2022-23'),
(2, '2.6.3(B)', 'Certified report from CoE indicating Students eligible for the degree program', '2022-23'),
(2, '2.7.1', 'Student Satisfaction Survey (SSS) on overall institutional performance  (Institution may design its own questionnaire). Results and details need to be  provided as a weblink', '2022-23'),
(3, '3.1.1', 'The institution???s research facilities are frequently updated and there is a welldefined policy for promotion of research which is uploaded on the institutional  website and implemented(Present a write-up within a maximum of 200 words.)', '2022-23'),
(3, '3.1.1(A)', 'List of research equipment along with the proof of purchases (Bill copies)', '2022-23'),
(3, '3.1.1(B)', 'Policy for Faculty Assessment and Development Scheme', '2022-23'),
(3, '3.1.1(C)', 'Financial Incentives for attending conferences, seminars and QIP', '2022-23'),
(3, '3.1.1(D)', 'Sanction letters for the funded research projects & UCs for the projects completed', '2022-23'),
(3, '3.1.1(E)', 'Copies of all MoUs for collaborative research', '2022-23'),
(3, '3.1.1(F)', 'Minutes of the governing council meeting-reflecting the research promotions', '2022-23'),
(3, '3.1.2', 'Details of Seed money', '2022-23'),
(3, '3.1.3', 'Number of teachers who were awarded national / international fellowship(s) for  advanced studies/research during the year', '2022-23'),
(3, '3.2.1', 'Grants received from Government and Non-Governmental agencies for research  projects, endowments, Chairs during the year (INR in Lakhs)', '2022-23'),
(3, '3.2.1(A)', 'List of Grants received for research projects', '2022-23'),
(3, '3.2.1(B)', 'e-copies of grants sanctioned', '2022-23'),
(3, '3.2.2', 'List of teachers having research projects during the year', '2022-23'),
(3, '3.2.3', 'Number of teachers recognized as research guides', '2022-23'),
(3, '3.2.4', 'Number of departments having research projects', '2022-23'),
(3, '3.2.4(A)', 'Web Links to Funding Agencies', '2022-23'),
(3, '3.3.1', 'Institution has created an ecosystem for innovations and creation and transfer of  knowledge supported by dedicated centres for research, entrepreneurship,  community orientation, incubation, etc.(Present a write-up within a maximum of 200 words)', '2022-23'),
(3, '3.3.1(A)', 'MSME business incubation center ??? sanction letter', '2022-23'),
(3, '3.3.1(B)', 'AICTE sponsored EDC ??? Sanction letters and list of the activities conducted by EDC', '2022-23'),
(3, '3.3.1(C)', 'Dedicated centres for research', '2022-23'),
(3, '3.3.2', 'Detailed report including photos, resource persons etc.', '2022-23'),
(3, '3.4.1', 'The Institution ensures implementation of its Code of Ethics for Research  uploaded in the website through the following:  1. Research Advisory Committee 2. Ethics Committee 3. Inclusion of Research Ethics in the research methodology course work  4. Plagiarism check through authenticated software Options: A. All of the above B. Any 3 of the above C. Any 2 of the above D. Any 1 of the above E. None of the above', '2022-23'),
(3, '3.4.1(A)', 'Research Advisory Committee', '2022-23'),
(3, '3.4.1(B)', 'Ethics Committee', '2022-23'),
(3, '3.4.1(C)', 'Inclusion of Research Ethics in the research methodology course work', '2022-23'),
(3, '3.4.1(D)', 'Anti Plagiarism software approved by JNTU', '2022-23'),
(3, '3.4.2', 'Number of PhD candidates registered per teacher (as per the data given with  regard to recognized PhD guides/ supervisors provided in Metric No. 3.2.3) during  the year', '2022-23'),
(3, '3.4.2(A)', 'List of Faculty along with the names of Research scholars', '2022-23'),
(3, '3.4.2(B)', 'Copy of the Registration letters/Joining letters', '2022-23'),
(3, '3.4.3', 'Number of research papers per teacher in CARE Journals notified on UGC  website during the year', '2022-23'),
(3, '3.4.4', 'Number of books and chapters in edited volumes / books published per teacher  during the year', '2022-23'),
(3, '3.4.5', 'Bibliometrics of the publications based on average Citation Index', '2022-23'),
(3, '3.4.6', 'Bibliometrics of the publication-based h-Index of the University', '2022-23'),
(3, '3.5.1', 'Audited statements for Revenue generated from consultancy', '2022-23'),
(3, '3.5.2', 'Total amount spent on developing facilities, training teachers and clerical/project  staff for undertaking consultancy during the year', '2022-23'),
(3, '3.6.1', 'Extension activities carried out in the neighbourhood sensitising students to social  issues for their holistic development, and the impact thereof during the year', '2022-23'),
(3, '3.6.1(A)', 'List of NSS activities conducted year wise and number of students participated', '2022-23'),
(3, '3.6.1(B)', 'Covid-19 booster report', '2022-23'),
(3, '3.6.1(C)', 'MGNCRE Reports', '2022-23'),
(3, '3.6.1(D)', 'Community Radio report', '2022-23'),
(3, '3.6.1(E)', 'List of NCC activities conducted year wise and number of students participated', '2022-23'),
(3, '3.6.2', 'Number of awards and recognition received by the Institution, its teachers and  students for extension activities from Government / Government-recognised bodies', '2022-23'),
(3, '3.6.3', 'Number of extension and outreach programmes conducted by the institution through  NSS/NCC during the year', '2022-23'),
(3, '3.6.4', 'Number of students participating in extension activities listed in 3.6.3 during the  year', '2022-23'),
(3, '3.7.1', 'Number of collaborative activities during the year for research/ faculty exchange/  student exchange/ internship/ on-the-job training/ project work', '2022-23'),
(3, '3.7.2', 'Number of functional MoUs with institutions of national and/or international  importance, other universities, industries, corporate houses, etc. during the year  (only functional MoUs with ongoing activities to be considered)', '2022-23'),
(3, '3.7.2(A)', 'e-copies of functional MoUs', '2022-23'),
(3, '3.7.2(B)', 'e-copies of Activities of MOUs', '2022-23'),
(4, '4.1.1', 'The Institution has adequate infrastructure and physical facilities for teaching learning, viz., classrooms, laboratories, computing equipments, etc.', '2022-23'),
(4, '4.1.1(A)', 'Details of the Classrooms, Labs & Other facilities across all the six blocks', '2022-23'),
(4, '4.1.1(B)', 'List of the laboratories with titles', '2022-23'),
(4, '4.1.1(C)', 'Campus LAN diagram', '2022-23'),
(4, '4.1.1(D)', 'Proof of bandwidth', '2022-23'),
(4, '4.1.1(E)', 'List of the software', '2022-23'),
(4, '4.1.1(F)', 'Photo gallery of all the academic blocks, classrooms, drawing hall, seminar hall, auditorium, and Labs', '2022-23'),
(4, '4.1.2', 'The institution has adequate facilities for cultural activities, yoga, sports and games  (indoor and outdoor) including gymnasium, yoga centre, auditorium etc.)', '2022-23'),
(4, '4.1.2(A)', 'Colleague of Geo-tagged pictures', '2022-23'),
(4, '4.1.2(B)', 'Area details of the all the facilities', '2022-23'),
(4, '4.1.2(C)', 'Photo gallery of the various activities', '2022-23'),
(4, '4.1.3', 'Number of classrooms and seminar halls with ICT-enabled facilities', '2022-23'),
(4, '4.1.3(A)', 'Geo-tagged photographs of classrooms with ICT enabled facilities', '2022-23'),
(4, '4.1.3(B)', 'Class Timetables', '2022-23'),
(4, '4.1.4', 'Expenditure for infrastructure augmentation, excluding salary, during the year (INR  in Lakhs)', '2022-23'),
(4, '4.1.4(A)', 'Budget allocation', '2022-23'),
(4, '4.1.4(B)', 'Provide the consolidated fund allocation towards infrastructure augmentation facilities duly certified by Head of the Institution', '2022-23'),
(4, '4.2.1', 'Library is automated using Integrated Library Management System (ILMS)', '2022-23'),
(4, '4.2.1(A)', 'Library operates through LIBSYS', '2022-23'),
(4, '4.2.1(B)', 'All the books are provided with RF Id security tags', '2022-23'),
(4, '4.2.1(C)', 'Copy of the latest License agreement of LIBSYS-7', '2022-23'),
(4, '4.2.1(D)', 'Digital Library', '2022-23'),
(4, '4.2.2', 'Institution has access to the following', '2022-23'),
(4, '4.2.2 (D)', 'Specific details in respect of e-resources selected.', '2022-23'),
(4, '4.2.2 (E)', 'Databases', '2022-23'),
(4, '4.2.2(A)', 'Details of subscriptions of e-journals', '2022-23'),
(4, '4.2.2(B)', 'Letter of subscription', '2022-23'),
(4, '4.2.2(C)', 'Screenshots of the facilities claimed with the name of HEI.', '2022-23'),
(4, '4.2.3', 'Expenditure on purchase of books/ e-books and subscription to journals/e-journals  during the year (INR in lakhs)', '2022-23'),
(4, '4.2.3(A)', 'Consolidated extract of expenditure', '2022-23'),
(4, '4.2.3(B)', 'Invoices of all the expenditure', '2022-23'),
(4, '4.2.4', 'Usage of library by teachers and students (footfalls and login data for online access)', '2022-23'),
(4, '4.2.4(A)', 'Certified e-copy of the ledger for footfalls for 5days', '2022-23'),
(4, '4.2.4(B)', 'Certified screenshots of the data for the same 5 days for online access', '2022-23'),
(4, '4.2.4(C)', 'Last page of accession register details', '2022-23'),
(4, '4.3.1', 'Institution has an IT policy covering Wi-Fi, cyber security, etc. and has allocated  budget for updating its IT facilities', '2022-23'),
(4, '4.3.1(A)', 'Colleague of Geo-tagged pictures', '2022-23'),
(4, '4.3.1(B)', 'Policy document', '2022-23'),
(4, '4.3.1(C)', 'Campus Wi-Fi/Network diagram', '2022-23'),
(4, '4.3.1(D)', 'Budget of the year 2020-21', '2022-23'),
(4, '4.3.2', 'Student - Computer ratio', '2022-23'),
(4, '4.3.2(A)', 'Computer Bills', '2022-23'),
(4, '4.3.2(B)', 'Student strength', '2022-23'),
(4, '4.3.3', 'Bandwidth of internet connection in the Institution and the number of students on  campus', '2022-23'),
(4, '4.3.3(A)', 'Details of available bandwidth of internet connection in the Institution', '2022-23'),
(4, '4.3.3(B)', 'Bills for any one month/one quarter.', '2022-23'),
(4, '4.3.3(C)', 'e-copy of document of agreement with the service provider.', '2022-23'),
(4, '4.3.4', 'Institution has facilities for e-content development', '2022-23'),
(4, '4.3.4(A)', 'Geo tagged photographs of Media Centre, Audio Visual Centre etc.,', '2022-23'),
(4, '4.3.4(B)', 'Purchase bills for Lecture Capturing System', '2022-23'),
(4, '4.3.4(C)', 'Audited income expenditure statement highlighting the relevant expenditure.', '2022-23'),
(4, '4.4.1', 'Expenditure incurred on maintenance of physical and academic support facilities, excluding salary component, during the year (INR in lakhs)', '2022-23'),
(4, '4.4.1(A)', 'Audited statements of accounts.', '2022-23'),
(4, '4.4.2', 'There are established systems and procedures for maintaining and utilizing  physical, academic and support facilities ??? classrooms, laboratory, library, sports  complex, computers, etc.', '2022-23'),
(4, '4.4.2(A)', 'Schedules of Library, Sport complex??', '2022-23'),
(4, '4.4.2(B)', 'Laboratory Timetables', '2022-23'),
(4, '4.4.2(C)', 'SOP for Laboratory utilization', '2022-23'),
(4, '4.4.2(D)', 'SOP for usage of general amenities', '2022-23'),
(4, '4.4.2(E)', 'Geo-tagged photos of GAMYA', '2022-23'),
(4, '4.4.2(F)', 'Maintenance schedules and AMC letters from Estate department', '2022-23'),
(5, '5.1.1', 'Number of students benefitted by scholarships and freeships provided by the  Government during the year', '2022-23'),
(5, '5.1.2', 'Number of students benefitted by scholarships and freeships provided by the  institution and non-government agencies during the year', '2022-23'),
(5, '5.1.3', 'The following Capacity Development and Skill Enhancement activities are  organised for improving students??? capabilities', '2022-23'),
(5, '5.1.3(A)', 'Soft Skill, Language and Communication', '2022-23'),
(5, '5.1.3(B)', 'Yoga Class', '2022-23'),
(5, '5.1.3(C)', 'Awareness of Trends in technology', '2022-23'),
(5, '5.1.4', 'Number of students benefitted from guidance/coaching for competitive  examinations and career counselling offered by the institution during the year', '2022-23'),
(5, '5.1.5', 'The institution adopts the following mechanism for redressal of students???  grievances, including sexual harassment and ragging', '2022-23'),
(5, '5.2.1', 'Number of outgoing students who got placement during the year', '2022-23'),
(5, '5.2.2', 'Number of outgoing students progressing to higher education during the year', '2022-23'),
(5, '5.2.3', 'Number of students qualifying in state/ national/ international level examinations  during the year', '2022-23'),
(5, '5.3.1', 'Number of awards/medals for outstanding performance in sports and/or cultural  activities at inter-university / state /national / international events (award for a team  event should be counted as one) during the year', '2022-23'),
(5, '5.3.2', 'Presence of an active Student Council and representation of students in academic  and administrative bodies/committees of the institution', '2022-23'),
(5, '5.3.3', 'Number of sports and cultural events / competitions organised by the institution', '2022-23'),
(5, '5.4.1', 'The Alumni Association and its Chapters (registered and functional) contribute  significantly to the development of the institution through financial and other  support services', '2022-23'),
(5, '5.4.2', 'Alumni???s financial contribution during the year', '2022-23'),
(6, '6.1.1', 'The governance of the institution is reflective of an effective leadership in tune with  the vision and mission of the Institution', '2022-23'),
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
(6, '6.1.2', 'Effective leadership is reflected in various institutional practices such as decentralization and participative management', '2022-23'),
(6, '6.1.2(A)', 'Strat-Plan', '2022-23'),
(6, '6.1.2(B)', 'Requisition for two set of mid question papers from CoE ??? e-mail proof', '2022-23'),
(6, '6.1.2(C)', 'Declaration of mid question paper set number from CoE??? e-mail proof', '2022-23'),
(6, '6.1.2(D)', 'Uniform Evaluation', '2022-23'),
(6, '6.1.2(E)', 'Research Review meeting minutes', '2022-23'),
(6, '6.2.1', 'The institutional Strategic/ Perspective plan has been clearly articulated and  implemented', '2022-23'),
(6, '6.2.1(A)', 'Strat-Plan', '2022-23'),
(6, '6.2.1(B)', 'Merit scholarships based on AP-EAMCET rank', '2022-23'),
(6, '6.2.1(C)', 'Meritorious scholarships for students', '2022-23'),
(6, '6.2.1(D)', 'Details of the GATE training classes conducted; List of students attended GATE Coaching & secured score', '2022-23'),
(6, '6.2.1(E)', 'Details of the CRT programs/ Technical training/Competitions conducted (Total number of hours), the List of the students attended the training programs & Proof of attendance, Branch wise list of the students placed, and the number of companies visited. List of students attended the WTN.', '2022-23'),
(6, '6.2.1(F)', 'Motivational and inspirational talks by the industry experts', '2022-23'),
(6, '6.2.1(G)', 'Social media updates', '2022-23'),
(6, '6.2.2', 'The functioning of the various institutional bodies is effective and efficient as visible  from the policies, administrative set-up, appointment and service rules, procedures,  etc. ', '2022-23'),
(6, '6.2.2(A)', 'Organogram', '2022-23'),
(6, '6.2.2(B)', 'HR & Service rules/Incentive policies', '2022-23'),
(6, '6.2.2(D)', 'Frequency and conduct of the meetings of governance committees (GC, AC, BOS, FC)', '2022-23'),
(6, '6.2.2(E)', 'SOP for procurement (AOP, MRN, Comparative statements and Purchase Orders)', '2022-23'),
(6, '6.2.3', 'Implementation of e-governance in areas of operation: 1. Administration 2. Finance and Accounts 3. Student Admission and Support 4. Examination', '2022-23'),
(6, '6.3.1', 'The institution has effective welfare measures for teaching and non-teaching staff  and avenues for their career development/ progression', '2022-23'),
(6, '6.3.1(A)', 'HR Policies (Welfare & Career development)', '2022-23'),
(6, '6.3.1(B)', 'Details of the training programs conducted for teaching & non-teaching staff', '2022-23'),
(6, '6.3.1(C)', 'Details of the staff (Teaching & Non-teaching promoted)', '2022-23'),
(6, '6.3.1(D)', 'Details of on campus housing, Term insurance, Medical insurance, Children education, ESI, Cooperative credit society, Concessions in IP/OP services and Gratuity', '2022-23'),
(6, '6.3.1(E)', 'Details of the faculty received incentives for completion of Ph.D./ QIP', '2022-23'),
(6, '6.3.2', 'Number of teachers provided with financial support to attend conferences /  workshops and towards payment of membership fee of professional bodies during the  year', '2022-23'),
(6, '6.3.2(A)', 'Number of teachers provided with financial support to attend conferences / workshops and towards payment of membership fee of professional bodies during the year', '2022-23'),
(6, '6.3.3', 'Number of professional development / administrative training programmes  organized by the Institution for its teaching and non-teaching staff during the year', '2022-23'),
(6, '6.3.3(A)', 'Annual Reports highlighting training programs conducted for teaching & non teaching', '2022-23'),
(6, '6.3.3(B)', 'Training programs conducted for teaching & non-teaching', '2022-23'),
(6, '6.3.4', 'Number of teachers who have undergone online/ face-to-face Faculty Development  Programmes during the year: (Professional Development Programmes, Orientation / Induction Programmes,  Refresher Courses, Short-Term Course, etc.)', '2022-23'),
(6, '6.3.4(A)', 'List of Faculty FDPS Attended & Proofs', '2022-23'),
(6, '6.4.1', 'Institution conducts internal and external financial audits regularly', '2022-23'),
(6, '6.4.2', 'Funds / Grants received from non-government bodies, individuals, and  philanthropists during the year (not covered in Criterion III and V) (INR in lakhs)', '2022-23'),
(6, '6.4.3', 'Institutional strategies for mobilisation of funds and the optimal utilisation of  resources', '2022-23'),
(6, '6.5.1', 'Internal Quality Assurance Cell (IQAC) has contributed significantly for  institutionalizing quality assurance strategies and processes visible in terms of  incremental improvements made during the preceding year with regard to quality (in  case of the First Cycle): Incremental improvements made during the preceding year with regard to quality  and post-accreditation quality initiatives (Second and subsequent cycles)', '2022-23'),
(6, '6.5.1(A)', 'List of companies hosted Internship', '2022-23'),
(6, '6.5.1(B)', 'FADS ??? Supporting Docs', '2022-23'),
(6, '6.5.1(C)', 'Consolidated initiatives of IQAC (strategies & contributions of IQAC mentioned)', '2022-23'),
(6, '6.5.2', 'The institution reviews its teaching-learning process, structures and methodologies  of operation and learning outcomes at periodic intervals through its IQAC as per  norms', '2022-23'),
(6, '6.5.2(A)', 'List of the IQAC initiatives taken up to enhance the students??? performance (Course coordinator meetings, AMC meeting minutes, Remedial classes for slow learners)', '2022-23'),
(6, '6.5.2(B)', 'Details of the progression of the students from 1st to 8th semesters branch wise for 2020-21 for all the batches graduated ??? 1 Bar chart, in each bar chart with eight bars', '2022-23'),
(6, '6.5.2(C)', 'Structures & methodologies of operations ISO audits, Academic audits -Internal & External branch wise', '2022-23'),
(6, '6.5.2(D)', 'Internal & External Academic Audit Report', '2022-23'),
(6, '6.5.3', 'Quality assurance initiatives of the institution include', '2022-23'),
(6, '6.5.3(A)', 'IQAC meeting minutes', '2022-23'),
(6, '6.5.3(B)', 'Feedback system of the institution', '2022-23'),
(6, '6.5.3(C)', 'Feedback system for design and review of syllabus', '2022-23'),
(6, '6.5.3(D)', 'Collaborative quality initiatives', '2022-23'),
(6, '6.5.3(E)', 'Participation in NIRF', '2022-23'),
(6, '6.5.3(F)', 'NBA accreditation', '2022-23'),
(6, '6.5.3(G)', 'NAAC accreditation', '2022-23'),
(6, '6.5.3(H)', 'IQAC Feedback analysis', '2022-23'),
(7, '7.1.1', 'Measures initiated by the institution for the promotion of gender equity during the  year', '2022-23'),
(7, '7.1.1(A)', 'Action plan of WEC for gender sensitization', '2022-23'),
(7, '7.1.1(B)', 'Campus surveillance with CC TV (Audit report)', '2022-23'),
(7, '7.1.1(C)', 'Policy for women security and safety (PASH)', '2022-23'),
(7, '7.1.1(D)', 'Photographs of Exclusive reading rooms, waiting rooms and rest rooms', '2022-23'),
(7, '7.1.1(E)', 'Day care centre for the kids (Beneficiaries)', '2022-23'),
(7, '7.1.1(F)', 'Welfare measures (Maternity leave for two kids)', '2022-23'),
(7, '7.1.1(G)', 'List of the beneficiaries', '2022-23'),
(7, '7.1.10', 'The institution has a prescribed code of conduct for students, teachers, administrators  and other staff and conducts periodic sensitization programmes in this regard', '2022-23'),
(7, '7.1.10(A)', 'Details of the monitoring committee composition and minutes of the committee meeting, number of programmes organized, reports on the various programmes, etc. in support of the claims', '2022-23'),
(7, '7.1.10(B)', 'Policy document on code of ethics', '2022-23'),
(7, '7.1.10(C)', 'Circulars and geo tagged photographs and caption of the activities organized under the metric for teachers, students, administrators and other staffs', '2022-23'),
(7, '7.1.11', 'Institution celebrates / organizes national and international commemorative days,  events and festivals', '2022-23'),
(7, '7.1.11(A)', 'Annual report of the celebrations and commemorative events', '2022-23'),
(7, '7.1.11(B)', 'Photographs of some of the events', '2022-23'),
(7, '7.1.2', 'The Institution has facilities for alternate sources of energy and energy conservation ', '2022-23'),
(7, '7.1.2(A)', 'Geo tagged photographs with caption of the facilities', '2022-23'),
(7, '7.1.2(B)', 'Bills for the purchase of equipment for the facilities', '2022-23'),
(7, '7.1.2(C)', 'Permission document for connection to the grid from Government', '2022-23'),
(7, '7.1.3', 'Describe the facilities in the institution for the management of the following types of  degradable and non-degradable waste (within a maximum of 200 words)', '2022-23'),
(7, '7.1.3(A)', 'Geo tagged photographs of the facilities', '2022-23'),
(7, '7.1.3(B)', 'SOP for solid waste management', '2022-23'),
(7, '7.1.4', 'Water conservation facilities available in the institution', '2022-23'),
(7, '7.1.4(A)', 'Geo tagged photographs with caption of the facilities', '2022-23'),
(7, '7.1.4(B)', 'Bills for the purchase of equipment', '2022-23'),
(7, '7.1.5', 'Green campus initiatives include', '2022-23'),
(7, '7.1.5(A)', 'Policy document on the green campus', '2022-23'),
(7, '7.1.5(B)', 'Geo tagged photographs/Videos with caption of the facilities', '2022-23'),
(7, '7.1.5(C)', 'Circulars for the implementation of the initiatives and any other supporting document', '2022-23'),
(7, '7.1.6', 'Quality audits on environment and energy undertaken by the institution', '2022-23'),
(7, '7.1.6(A)', 'Policy document on environment and energy usage', '2022-23'),
(7, '7.1.6(B)', 'Certificate from the auditing agency', '2022-23'),
(7, '7.1.6(C)', 'Certificates of the awards received from the recognized agency', '2022-23'),
(7, '7.1.6(D)', 'Report on environmental promotional activities conducted beyond the campus', '2022-23'),
(7, '7.1.6(F)', 'Green audit report', '2022-23'),
(7, '7.1.7', 'The Institution has a Divyangjan-friendly and barrier-free environment', '2022-23'),
(7, '7.1.7(A)', 'Policy document and information brochure', '2022-23'),
(7, '7.1.7(B)', 'Geo tagged photos', '2022-23'),
(7, '7.1.7(C)', 'Bills and invoice/purchase order/AMC', '2022-23'),
(7, '7.1.7(D)', 'A rest room should include specific requirements of Divyangjan', '2022-23'),
(7, '7.1.7(E)', 'Bills for the software procured', '2022-23'),
(7, '7.1.8', 'Describe the Institutional efforts/initiatives in providing an inclusive environment i.e.  tolerance and harmony towards cultural, regional, linguistic, communal, socioeconomic and other diversities (within a maximum of 200 words)', '2022-23'),
(7, '7.1.8(A)', 'List of the outbound programs', '2022-23'),
(7, '7.1.8(B)', 'List of national level activities (Cultural, Sports, Academic, Cultural & Sports)', '2022-23'),
(7, '7.1.8(C)', 'List of the faculty & students coming from the out of state', '2022-23'),
(7, '7.1.9', 'Sensitization of students and employees of the institution to constitutional obligations:  values, rights, duties and responsibilities of citizens', '2022-23'),
(7, '7.1.9(A)', 'List of the activities/Events conducted, and the number of students present. (Awareness programs on Women safety & protection, Anti ragging, Judicial rights, Gender equality, Traffic rules, Environment protection, Conservation of natural resources (power & water)) >', '2022-23'),
(7, '7.2.1', 'Provide the weblink on the Institutional website regarding the Best practices as per  the prescribed format of NAAC', '2022-23'),
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
(1, '1.1.1', 'Copies of PEOs, POs & PSOs for all programs', '2022-23'),
(1, '1.1.2', 'Number of Programmes where syllabus revision was carried out during the year(Data Requirement: Programme Code ,Names of the Programmes revised)', '2022-23'),
(1, '1.1.2(A)', 'List of programs where syllabus revision has been carried out signed by the Principal', '2022-23'),
(1, '1.1.2(B)', 'Approved Minutes of relevant Academic Council/BOS meetings highlighting the specific agenda item relevant to the metric year wise', '2022-23'),
(1, '1.1.3', 'Number of courses focusing on employability/entrepreneurship/ skill development  offered by the Institution during the year(Data Requirement: Name of the Course with Course Code ,Name of the Programme ,Activities which have a direct bearing on employability/ entrepreneurship/ skill  development)', '2022-23'),
(1, '1.1.3(A)', 'Syllabus copy of the courses highlighting Focus on employability/entrepreneurship/ skill development along with their course outcomes.', '2022-23'),
(1, '1.1.3(B)', 'Minutes of the Boards of Studies/ Academic Council meetings with approval for these courses.', '2022-23'),
(1, '1.1.3(C)', 'List of MoUs', '2022-23'),
(1, '1.2.1', 'Number of new courses introduced across all programmes offered during the year(Data Requirement: Name of the newly introduced course (s)  ,Name of the Programme)', '2022-23'),
(1, '1.2.1(A)', 'List of new courses introduced program-wise during the assessment period certified by the Principal.', '2022-23'),
(1, '1.2.1(B)', 'Minutes of relevant Academic Council/BOS meetings highlighting the name of the new courses introduced', '2022-23'),
(1, '1.2.2', 'Number of Programmes offered through Choice Based Credit System  (CBCS)/Elective Course System(Data Requirement: ,Names of all Programmes offered through CBCS ,Names of all Programmes offered through Elective Course System)', '2022-23'),
(1, '1.2.2(A)', 'List of programs in which CBCS/Elective course system implemented in the last completed academic year certified by the principal', '2022-23'),
(1, '1.2.2(B)', 'Structure of the program clearly indicating courses, credits/Electives and Minutes of relevant Academic Council/BOS meetings highlighting the relevant documents to this metric', '2022-23'),
(1, '1.3.1', 'List and Description of courses relevant to Professional Ethics, Gender diversity and equality, Human Values, Environment and Sustainability, Women Empowerment introduced in the Curriculum along with the syllabus should be available in all the departments', '2022-23'),
(1, '1.3.2', 'Number of value-added courses for imparting transferable and life skills offered  during the year:(Data Requirement:  Names of the value-added courses (each with 30 or more contact hours) ,No. of times offered (for each value-added course) during the year ,Total number of students enrolled ,Total number of students completing the course during the year', '2022-23'),
(1, '1.3.2(A)', 'List of value-added courses which are optional and offered outside the curriculum of the programs with authorized sign.', '2022-23'),
(1, '1.3.2(B)', 'Brochure and Course content or syllabus along with course outcome of Value-added courses offered.', '2022-23'),
(1, '1.3.3', 'List of enrolled students for courses addressed in 1.3.2', '2022-23'),
(1, '1.3.4', 'List of students undertaking the field projects/ internships / student projects program-wise in the last completed academic year along with the details of title, place of work etc.', '2022-23'),
(1, '1.4.1', 'Sample Filled in feedback forms from the stakeholders to be provided.', '2022-23'),
(1, '1.4.2', 'Stakeholder feedback analysis report signed by the authority', '2022-23'),
(2, '2.1.1', 'Enrolment of Students ', '2022-23'),
(2, '2.1.1(A)', 'Students admitted(HEI signed documents)', '2022-23'),
(2, '2.1.1(B)', 'Enrolment Number_ AICTE letters of sanctioned intake', '2022-23'),
(2, '2.1.1(C)', 'Enrolment Number _ Ratified list of admitted students', '2022-23'),
(2, '2.1.2', 'Number of seats filled against reserved categories (SC, ST, OBC, Divyangjan,  etc.) as per the reservation policy during the year (exclusive of supernumerary  seats)', '2022-23'),
(2, '2.1.2(A)', 'Reservation seats categories to be considered as per the state rule (APSHE Guidelines)', '2022-23'),
(2, '2.1.2(B)', 'AP regulation for Admissions GO No. 73', '2022-23'),
(2, '2.1.2(C)', 'Guidelines for filling the left-over vacancies under reservation', '2022-23'),
(2, '2.1.2(D)', 'Category wise_ Ratified list of students admitted', '2022-23'),
(2, '2.1.2(E)', 'Admission Abstract(HEI signed documents)', '2022-23'),
(2, '2.2.1', 'The institution assesses students??? learning levels and organises special  programmes for both slow and advanced learners(Present a write-up within a maximum of 200 words)', '2022-23'),
(2, '2.2.1(A)', 'Video records of the specific topics(A document having the links and screen shots of video course/LCS)', '2022-23'),
(2, '2.2.1(B)', 'Classes for the slow learner(Time table conducted for CA failures and attendance sheets)', '2022-23'),
(2, '2.2.1(C)', 'Additional assignment(sample copies of either question papers or evaluated assignment)', '2022-23'),
(2, '2.2.1(D)', 'Make-up classes for course detention students/lateral entry students(Time tables and attendance sheets for each semester(1-8) at least one)', '2022-23'),
(2, '2.2.1(E)', 'Remedial classes conducted for end semester failures(Time tables, attendance sheets and track sheets for each semester(1-8) at least one)', '2022-23'),
(2, '2.2.1(F)', 'List of MOOCs courses(Details of student with course name and sample certificates)', '2022-23'),
(2, '2.2.1(G)', 'List of the advanced learners opted Minors and honors(Abstract certified by controller of examination)', '2022-23'),
(2, '2.2.1(H)', 'Student participation in technical events list with students event details and SAMPLE certificates', '2022-23'),
(2, '2.2.2', 'Student ??? Teacher (full-time) ratio(Data Requirement:  Total number of students in the institution ,Total number of full-time teachers in the institution)', '2022-23'),
(2, '2.2.2(A)', 'List of the full-time teachers', '2022-23'),
(2, '2.2.2(B)', 'Student intake', '2022-23'),
(2, '2.3.1', 'Student-centric methods such as experiential learning, participative learning and  problem-solving methodologies are used for enhancing learning experiences(Present a write-up within a maximum of 200 words.)', '2022-23'),
(2, '2.3.1(A)', 'Academic regulations and curriculum(Recent)', '2022-23'),
(2, '2.3.2', 'Teachers use ICT-enabled tools including online resources for effective teaching  and learning(Present a write-up within a maximum of 200 words.)', '2022-23'),
(2, '2.3.2 (A)', 'ICT Tools Gadgets viz. Graphic tablets, MST, Smart pen, Projector(Geotagged photos with title of photo)', '2022-23'),
(2, '2.3.3', 'Ratio of students to mentor for academic and other related issues', '2022-23'),
(2, '2.3.3(A)', 'Circular of mentor ??? mentees', '2022-23'),
(2, '2.3.3(B)', 'Mentor list as announced by the HEI', '2022-23'),
(2, '2.3.3(C)', 'issues raised and resolved in the mentor system(Total records of a student -&gt; one sample from each semester)', '2022-23'),
(2, '2.3.4', 'Preparation and adherence to Academic Calendar and Teaching Plans by the  institution(Describe the preparation of and adherence to the Academic Calendar and Teaching  Plans by the institution.  Present a write-up within a maximum of 200 words)', '2022-23'),
(2, '2.3.4(A)', 'Academic calendar', '2022-23'),
(2, '2.3.4(B)', 'Proof of course allotment for odd and even semesters', '2022-23'),
(2, '2.3.4(C)', 'Proof for lecture plan, Schedule and diary(one course from each semester)', '2022-23'),
(2, '2.3.4(D)', 'Minutes of AMC', '2022-23'),
(2, '2.4.1', 'Number of full-time teachers against sanctioned posts during the year(Data Requirement:  Number of full-time teachers ,Number of sanctioned posts)', '2022-23'),
(2, '2.4.1(A)', 'Sanction letter indicating number of posts', '2022-23'),
(2, '2.4.1(B)', 'Department wise List of full-time teachers appointed', '2022-23'),
(2, '2.4.2', 'Number of full-time teachers with PhD/ D.M. / M.Ch. / D.N.B Super-Specialty /  DSc / DLitt during the year(Data Requirement: List of full-time teachers with PhD/ D.M. / M.Ch. / D.N.B Super-Specialty /  DSc / DLitt.)', '2022-23'),
(2, '2.4.3', 'Total teaching experience of full-time teachers in the same institution(Data Requirement:  Name and number of full-time teachers and their years of teaching experience in the institution)', '2022-23'),
(2, '2.5.1', 'Number of days from the date of last semester-end/ year- end examination till the  declaration of results during the year(Number of days from the date of last semester-end / year-end examination till the  declaration of results year-wise during the year)', '2022-23'),
(2, '2.5.1(A)', 'Examination Result Notifications', '2022-23'),
(2, '2.5.1(B)', 'List of Programs offered and the last date of the latest semester end exams and date of result declaration', '2022-23'),
(2, '2.5.2', 'Number of students??? complaints/grievances against evaluation against the total  number who appeared in the examinations during the year', '2022-23'),
(2, '2.5.2(A)', 'Number of complaints', '2022-23'),
(2, '2.5.2(B)', 'Minutes of the examination Committee', '2022-23'),
(2, '2.5.3', 'IT integration and reforms in the examination procedures and processes  including Continuous Internal Assessment (CIA) have brought in considerable  improvement in the Examination Management System (EMS) of the Institution(Describe the examination reforms with reference to the following within a minimum  of 200 words :Examination procedures ,Processes/Procedures integrating IT ,Continuous Internal Assessment System) ', '2022-23'),
(2, '2.5.3(A)', 'Examination Regulations', '2022-23'),
(2, '2.5.3(B)', 'Question Paper templates', '2022-23'),
(2, '2.5.3(C)', 'Proof of the hybrid grading sheet', '2022-23'),
(2, '2.5.3(D)', 'Examination Results link in the website', '2022-23'),
(2, '2.5.3(E)', 'Proof of the OMR', '2022-23'),
(2, '2.6.1', 'Programme Outcomes and Course Outcomes for all Programmes offered by the  institution are stated and displayed on the website and communicated to teachers  and students(Describe Course Outcomes (COs) for all courses and the mechanism of  communication to teachers and students within a maximum of 200 words.  Upload COs for all Courses) ', '2022-23'),
(2, '2.6.1(A)', 'List of POs and COs(COs for at least two courses from each semester)', '2022-23'),
(2, '2.6.1(B)', 'Photo gallery of the COs & POs displayed Program wise', '2022-23'),
(2, '2.6.2', 'Attainment of Programme Outcomes and Course Outcomes as evaluated by the  institution(Describe the method of measuring the attainment of POs, PSOs and COs and the  level of attaiment of POs , PSOs and COs in not more than 200 words.)', '2022-23'),
(2, '2.6.2(A)', 'CO and PO attainment', '2022-23'),
(2, '2.6.2(B)', 'Files related to all surveys for the indirect assessment', '2022-23'),
(2, '2.6.3', 'Pass Percentage of students', '2022-23'),
(2, '2.6.3(A)', 'Annual report of CoE indicating the pass percentage', '2022-23'),
(2, '2.6.3(B)', 'Certified report from CoE indicating Students eligible for the degree program', '2022-23'),
(2, '2.7.1', 'Student Satisfaction Survey (SSS) on overall institutional performance  (Institution may design its own questionnaire). Results and details need to be  provided as a weblink', '2022-23'),
(3, '3.1.1', 'The institution???s research facilities are frequently updated and there is a welldefined policy for promotion of research which is uploaded on the institutional  website and implemented(Present a write-up within a maximum of 200 words.)', '2022-23'),
(3, '3.1.1(A)', 'List of research equipment along with the proof of purchases (Bill copies)', '2022-23'),
(3, '3.1.1(B)', 'Policy for Faculty Assessment and Development Scheme', '2022-23'),
(3, '3.1.1(C)', 'Financial Incentives for attending conferences, seminars and QIP', '2022-23'),
(3, '3.1.1(D)', 'Sanction letters for the funded research projects & UCs for the projects completed', '2022-23'),
(3, '3.1.1(E)', 'Copies of all MoUs for collaborative research', '2022-23'),
(3, '3.1.1(F)', 'Minutes of the governing council meeting-reflecting the research promotions', '2022-23'),
(3, '3.1.2', 'Details of Seed money', '2022-23'),
(3, '3.1.3', 'Number of teachers who were awarded national / international fellowship(s) for  advanced studies/research during the year', '2022-23'),
(3, '3.2.1', 'Grants received from Government and Non-Governmental agencies for research  projects, endowments, Chairs during the year (INR in Lakhs)', '2022-23'),
(3, '3.2.1(A)', 'List of Grants received for research projects', '2022-23'),
(3, '3.2.1(B)', 'e-copies of grants sanctioned', '2022-23'),
(3, '3.2.2', 'List of teachers having research projects during the year', '2022-23'),
(3, '3.2.3', 'Number of teachers recognized as research guides', '2022-23'),
(3, '3.2.4', 'Number of departments having research projects', '2022-23'),
(3, '3.2.4(A)', 'Web Links to Funding Agencies', '2022-23'),
(3, '3.3.1', 'Institution has created an ecosystem for innovations and creation and transfer of  knowledge supported by dedicated centres for research, entrepreneurship,  community orientation, incubation, etc.(Present a write-up within a maximum of 200 words)', '2022-23'),
(3, '3.3.1(A)', 'MSME business incubation center ??? sanction letter', '2022-23'),
(3, '3.3.1(B)', 'AICTE sponsored EDC ??? Sanction letters and list of the activities conducted by EDC', '2022-23'),
(3, '3.3.1(C)', 'Dedicated centres for research', '2022-23'),
(3, '3.3.2', 'Detailed report including photos, resource persons etc.', '2022-23'),
(3, '3.4.1', 'The Institution ensures implementation of its Code of Ethics for Research  uploaded in the website through the following:  1. Research Advisory Committee 2. Ethics Committee 3. Inclusion of Research Ethics in the research methodology course work  4. Plagiarism check through authenticated software Options: A. All of the above B. Any 3 of the above C. Any 2 of the above D. Any 1 of the above E. None of the above', '2022-23'),
(3, '3.4.1(A)', 'Research Advisory Committee', '2022-23'),
(3, '3.4.1(B)', 'Ethics Committee', '2022-23'),
(3, '3.4.1(C)', 'Inclusion of Research Ethics in the research methodology course work', '2022-23'),
(3, '3.4.1(D)', 'Anti Plagiarism software approved by JNTU', '2022-23'),
(3, '3.4.2', 'Number of PhD candidates registered per teacher (as per the data given with  regard to recognized PhD guides/ supervisors provided in Metric No. 3.2.3) during  the year', '2022-23'),
(3, '3.4.2(A)', 'List of Faculty along with the names of Research scholars', '2022-23'),
(3, '3.4.2(B)', 'Copy of the Registration letters/Joining letters', '2022-23'),
(3, '3.4.3', 'Number of research papers per teacher in CARE Journals notified on UGC  website during the year', '2022-23'),
(3, '3.4.4', 'Number of books and chapters in edited volumes / books published per teacher  during the year', '2022-23'),
(3, '3.4.5', 'Bibliometrics of the publications based on average Citation Index', '2022-23'),
(3, '3.4.6', 'Bibliometrics of the publication-based h-Index of the University', '2022-23'),
(3, '3.5.1', 'Audited statements for Revenue generated from consultancy', '2022-23'),
(3, '3.5.2', 'Total amount spent on developing facilities, training teachers and clerical/project  staff for undertaking consultancy during the year', '2022-23'),
(3, '3.6.1', 'Extension activities carried out in the neighbourhood sensitising students to social  issues for their holistic development, and the impact thereof during the year', '2022-23'),
(3, '3.6.1(A)', 'List of NSS activities conducted year wise and number of students participated', '2022-23'),
(3, '3.6.1(B)', 'Covid-19 booster report', '2022-23'),
(3, '3.6.1(C)', 'MGNCRE Reports', '2022-23'),
(3, '3.6.1(D)', 'Community Radio report', '2022-23'),
(3, '3.6.1(E)', 'List of NCC activities conducted year wise and number of students participated', '2022-23'),
(3, '3.6.2', 'Number of awards and recognition received by the Institution, its teachers and  students for extension activities from Government / Government-recognised bodies', '2022-23'),
(3, '3.6.3', 'Number of extension and outreach programmes conducted by the institution through  NSS/NCC during the year', '2022-23'),
(3, '3.6.4', 'Number of students participating in extension activities listed in 3.6.3 during the  year', '2022-23'),
(3, '3.7.1', 'Number of collaborative activities during the year for research/ faculty exchange/  student exchange/ internship/ on-the-job training/ project work', '2022-23'),
(3, '3.7.2', 'Number of functional MoUs with institutions of national and/or international  importance, other universities, industries, corporate houses, etc. during the year  (only functional MoUs with ongoing activities to be considered)', '2022-23'),
(3, '3.7.2(A)', 'e-copies of functional MoUs', '2022-23'),
(3, '3.7.2(B)', 'e-copies of Activities of MOUs', '2022-23'),
(4, '4.1.1', 'The Institution has adequate infrastructure and physical facilities for teachinglearning, viz., classrooms, laboratories, computing equipments, etc.', '2022-23'),
(4, '4.1.1(A)', 'Details of the Classrooms, Labs & Other facilities across all the six blocks', '2022-23'),
(4, '4.1.1(B)', 'List of the laboratories with titles', '2022-23'),
(4, '4.1.1(C)', 'Campus LAN diagram', '2022-23'),
(4, '4.1.1(D)', 'Proof of bandwidth', '2022-23'),
(4, '4.1.1(E)', 'List of the software', '2022-23'),
(4, '4.1.1(F)', 'Photo gallery of all the academic blocks, classrooms, drawing hall, seminar hall, auditorium, and Labs', '2022-23'),
(4, '4.1.2', 'The institution has adequate facilities for cultural activities, yoga, sports and games  (indoor and outdoor) including gymnasium, yoga centre, auditorium etc.)', '2022-23'),
(4, '4.1.2(A)', 'Colleague of Geo-tagged pictures', '2022-23'),
(4, '4.1.2(B)', 'Area details of the all the facilities', '2022-23'),
(4, '4.1.2(C)', 'Photo gallery of the various activities', '2022-23'),
(4, '4.1.3', 'Number of classrooms and seminar halls with ICT-enabled facilities', '2022-23'),
(4, '4.1.3(A)', 'Geo-tagged photographs of classrooms with ICT enabled facilities', '2022-23'),
(4, '4.1.3(B)', 'Class Timetables', '2022-23'),
(4, '4.1.4', 'Expenditure for infrastructure augmentation, excluding salary, during the year (INR  in Lakhs)', '2022-23'),
(4, '4.1.4(A)', 'Budget allocation', '2022-23'),
(4, '4.1.4(B)', 'Provide the consolidated fund allocation towards infrastructure augmentation facilities duly certified by Head of the Institution', '2022-23'),
(4, '4.2.1', 'Library is automated using Integrated Library Management System (ILMS)', '2022-23'),
(4, '4.2.1(A)', 'Library operates through LIBSYS', '2022-23'),
(4, '4.2.1(B)', 'All the books are provided with RF Id security tags', '2022-23'),
(4, '4.2.1(C)', 'Copy of the latest License agreement of LIBSYS-7', '2022-23'),
(4, '4.2.1(D)', 'Digital Library', '2022-23'),
(4, '4.2.2', 'Institution has access to the following', '2022-23'),
(4, '4.2.2 (D)', 'Specific details in respect of e-resources selected.', '2022-23'),
(4, '4.2.2 (E)', 'Databases', '2022-23'),
(4, '4.2.2(A)', 'Details of subscriptions of e-journals', '2022-23'),
(4, '4.2.2(B)', 'Letter of subscription', '2022-23'),
(4, '4.2.2(C)', 'Screenshots of the facilities claimed with the name of HEI.', '2022-23'),
(4, '4.2.3', 'Expenditure on purchase of books/ e-books and subscription to journals/e-journals  during the year (INR in lakhs)', '2022-23'),
(4, '4.2.3(A)', 'Consolidated extract of expenditure', '2022-23'),
(4, '4.2.3(B)', 'Invoices of all the expenditure', '2022-23'),
(4, '4.2.4', 'Usage of library by teachers and students (footfalls and login data for online access)', '2022-23'),
(4, '4.2.4(A)', 'Certified e-copy of the ledger for footfalls for 5days', '2022-23'),
(4, '4.2.4(B)', 'Certified screenshots of the data for the same 5 days for online access', '2022-23'),
(4, '4.2.4(C)', 'Last page of accession register details', '2022-23'),
(4, '4.3.1', 'Institution has an IT policy covering Wi-Fi, cyber security, etc. and has allocated  budget for updating its IT facilities', '2022-23'),
(4, '4.3.1(A)', 'Colleague of Geo-tagged pictures', '2022-23'),
(4, '4.3.1(B)', 'Policy document', '2022-23'),
(4, '4.3.1(C)', 'Campus Wi-Fi/Network diagram', '2022-23'),
(4, '4.3.1(D)', 'Budget of the year 2020-21', '2022-23'),
(4, '4.3.2', 'Student - Computer ratio', '2022-23'),
(4, '4.3.2(A)', 'Computer Bills', '2022-23'),
(4, '4.3.2(B)', 'Student strength', '2022-23'),
(4, '4.3.3', 'Bandwidth of internet connection in the Institution and the number of students on  campus', '2022-23'),
(4, '4.3.3(A)', 'Details of available bandwidth of internet connection in the Institution', '2022-23'),
(4, '4.3.3(B)', 'Bills for any one month/one quarter.', '2022-23'),
(4, '4.3.3(C)', 'e-copy of document of agreement with the service provider.', '2022-23'),
(4, '4.3.4', 'Institution has facilities for e-content development', '2022-23'),
(4, '4.3.4(A)', 'Geo tagged photographs of Media Centre, Audio Visual Centre etc.,', '2022-23'),
(4, '4.3.4(B)', 'Purchase bills for Lecture Capturing System', '2022-23'),
(4, '4.3.4(C)', 'Audited income expenditure statement highlighting the relevant expenditure.', '2022-23'),
(4, '4.4.1', 'Expenditure incurred on maintenance of physical and academic support facilities, excluding salary component, during the year (INR in lakhs)', '2022-23'),
(4, '4.4.1(A)', 'Audited statements of accounts.', '2022-23'),
(4, '4.4.2', 'There are established systems and procedures for maintaining and utilizing  physical, academic and support facilities ??? classrooms, laboratory, library, sports  complex, computers, etc.', '2022-23'),
(4, '4.4.2(A)', 'Schedules of Library, Sport complex??', '2022-23'),
(4, '4.4.2(B)', 'Laboratory Timetables', '2022-23'),
(4, '4.4.2(C)', 'SOP for Laboratory utilization', '2022-23'),
(4, '4.4.2(D)', 'SOP for usage of general amenities', '2022-23'),
(4, '4.4.2(E)', 'Geo-tagged photos of GAMYA', '2022-23'),
(4, '4.4.2(F)', 'Maintenance schedules and AMC letters from Estate department', '2022-23'),
(5, '5.1.1', 'Number of students benefitted by scholarships and freeships provided by the  Government during the year', '2022-23'),
(5, '5.1.2', 'Number of students benefitted by scholarships and freeships provided by the  institution and non-government agencies during the year', '2022-23'),
(5, '5.1.3', 'The following Capacity Development and Skill Enhancement activities are  organised for improving students??? capabilities', '2022-23'),
(5, '5.1.3(A)', 'Soft Skill, Language and Communication', '2022-23'),
(5, '5.1.3(B)', 'Yoga Class', '2022-23'),
(5, '5.1.3(C)', 'Awareness of Trends in technology', '2022-23'),
(5, '5.1.4', 'Number of students benefitted from guidance/coaching for competitive  examinations and career counselling offered by the institution during the year', '2022-23'),
(5, '5.1.5', 'The institution adopts the following mechanism for redressal of students???  grievances, including sexual harassment and ragging', '2022-23'),
(5, '5.2.1', 'Number of outgoing students who got placement during the year', '2022-23'),
(5, '5.2.2', 'Number of outgoing students progressing to higher education during the year', '2022-23'),
(5, '5.2.3', 'Number of students qualifying in state/ national/ international level examinations  during the year', '2022-23'),
(5, '5.3.1', 'Number of awards/medals for outstanding performance in sports and/or cultural  activities at inter-university / state /national / international events (award for a team  event should be counted as one) during the year', '2022-23'),
(5, '5.3.2', 'Presence of an active Student Council and representation of students in academic  and administrative bodies/committees of the institution', '2022-23'),
(5, '5.3.3', 'Number of sports and cultural events / competitions organised by the institution', '2022-23'),
(5, '5.4.1', 'The Alumni Association and its Chapters (registered and functional) contribute  significantly to the development of the institution through financial and other  support services', '2022-23'),
(5, '5.4.2', 'Alumni???s financial contribution during the year', '2022-23'),
(6, '6.1.1', 'The governance of the institution is reflective of an effective leadership in tune with  the vision and mission of the Institution', '2022-23'),
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
(6, '6.1.2', 'Effective leadership is reflected in various institutional practices such as decentralization and participative management', '2022-23'),
(6, '6.1.2(A)', 'Strat-Plan', '2022-23'),
(6, '6.1.2(B)', 'Requisition for two set of mid question papers from CoE ??? e-mail proof', '2022-23'),
(6, '6.1.2(C)', 'Declaration of mid question paper set number from CoE??? e-mail proof', '2022-23'),
(6, '6.1.2(D)', 'Uniform Evaluation', '2022-23'),
(6, '6.1.2(E)', 'Research Review meeting minutes', '2022-23'),
(6, '6.2.1', 'The institutional Strategic/ Perspective plan has been clearly articulated and  implemented', '2022-23'),
(6, '6.2.1(A)', 'Strat-Plan', '2022-23'),
(6, '6.2.1(B)', 'Merit scholarships based on AP-EAMCET rank', '2022-23'),
(6, '6.2.1(C)', 'Meritorious scholarships for students', '2022-23'),
(6, '6.2.1(D)', 'Details of the GATE training classes conducted; List of students attended GATE Coaching & secured score', '2022-23'),
(6, '6.2.1(E)', 'Details of the CRT programs/ Technical training/Competitions conducted (Total number of hours), the List of the students attended the training programs & Proof of attendance, Branch wise list of the students placed, and the number of companies visited. List of students attended the WTN.', '2022-23'),
(6, '6.2.1(F)', 'Motivational and inspirational talks by the industry experts', '2022-23'),
(6, '6.2.1(G)', 'Social media updates', '2022-23'),
(6, '6.2.2', 'The functioning of the various institutional bodies is effective and efficient as visible  from the policies, administrative set-up, appointment and service rules, procedures,  etc. ', '2022-23'),
(6, '6.2.2(A)', 'Organogram', '2022-23'),
(6, '6.2.2(B)', 'HR & Service rules/Incentive policies', '2022-23'),
(6, '6.2.2(D)', 'Frequency and conduct of the meetings of governance committees (GC, AC, BOS, FC)', '2022-23'),
(6, '6.2.2(E)', 'SOP for procurement (AOP, MRN, Comparative statements and Purchase Orders)', '2022-23'),
(6, '6.2.3', 'Implementation of e-governance in areas of operation: 1. Administration 2. Finance and Accounts 3. Student Admission and Support 4. Examination', '2022-23'),
(6, '6.3.1', 'The institution has effective welfare measures for teaching and non-teaching staff  and avenues for their career development/ progression', '2022-23'),
(6, '6.3.1(A)', 'HR Policies (Welfare & Career development)', '2022-23'),
(6, '6.3.1(B)', 'Details of the training programs conducted for teaching & non-teaching staff', '2022-23'),
(6, '6.3.1(C)', 'Details of the staff (Teaching & Non-teaching promoted)', '2022-23'),
(6, '6.3.1(D)', 'Details of on campus housing, Term insurance, Medical insurance, Children education, ESI, Cooperative credit society, Concessions in IP/OP services and Gratuity', '2022-23'),
(6, '6.3.1(E)', 'Details of the faculty received incentives for completion of Ph.D./ QIP', '2022-23'),
(6, '6.3.2', 'Number of teachers provided with financial support to attend conferences /  workshops and towards payment of membership fee of professional bodies during the  year', '2022-23'),
(6, '6.3.2(A)', 'Number of teachers provided with financial support to attend conferences / workshops and towards payment of membership fee of professional bodies during the year', '2022-23'),
(6, '6.3.3', 'Number of professional development / administrative training programmes  organized by the Institution for its teaching and non-teaching staff during the year', '2022-23'),
(6, '6.3.3(A)', 'Annual Reports highlighting training programs conducted for teaching & non teaching', '2022-23'),
(6, '6.3.3(B)', 'Training programs conducted for teaching & non-teaching', '2022-23'),
(6, '6.3.4', 'Number of teachers who have undergone online/ face-to-face Faculty Development  Programmes during the year: (Professional Development Programmes, Orientation / Induction Programmes,  Refresher Courses, Short-Term Course, etc.)', '2022-23'),
(6, '6.3.4(A)', 'List of Faculty FDPS Attended & Proofs', '2022-23'),
(6, '6.4.1', 'Institution conducts internal and external financial audits regularly', '2022-23'),
(6, '6.4.2', 'Funds / Grants received from non-government bodies, individuals, and  philanthropists during the year (not covered in Criterion III and V) (INR in lakhs)', '2022-23'),
(6, '6.4.3', 'Institutional strategies for mobilisation of funds and the optimal utilisation of  resources', '2022-23'),
(6, '6.5.1', 'Internal Quality Assurance Cell (IQAC) has contributed significantly for  institutionalizing quality assurance strategies and processes visible in terms of  incremental improvements made during the preceding year with regard to quality (in  case of the First Cycle): Incremental improvements made during the preceding year with regard to quality  and post-accreditation quality initiatives (Second and subsequent cycles)', '2022-23'),
(6, '6.5.1(A)', 'List of companies hosted Internship', '2022-23'),
(6, '6.5.1(B)', 'FADS ??? Supporting Docs', '2022-23'),
(6, '6.5.1(C)', 'Consolidated initiatives of IQAC (strategies & contributions of IQAC mentioned)', '2022-23'),
(6, '6.5.2', 'The institution reviews its teaching-learning process, structures and methodologies  of operation and learning outcomes at periodic intervals through its IQAC as per  norms', '2022-23'),
(6, '6.5.2(A)', 'List of the IQAC initiatives taken up to enhance the students??? performance (Course coordinator meetings, AMC meeting minutes, Remedial classes for slow learners)', '2022-23'),
(6, '6.5.2(B)', 'Details of the progression of the students from 1st to 8th semesters branch wise for 2020-21 for all the batches graduated ??? 1 Bar chart, in each bar chart with eight bars', '2022-23'),
(6, '6.5.2(C)', 'Structures & methodologies of operations ISO audits, Academic audits -Internal & External branch wise', '2022-23'),
(6, '6.5.2(D)', 'Internal & External Academic Audit Report', '2022-23'),
(6, '6.5.3', 'Quality assurance initiatives of the institution include', '2022-23'),
(6, '6.5.3(A)', 'IQAC meeting minutes', '2022-23'),
(6, '6.5.3(B)', 'Feedback system of the institution', '2022-23'),
(6, '6.5.3(C)', 'Feedback system for design and review of syllabus', '2022-23'),
(6, '6.5.3(D)', 'Collaborative quality initiatives', '2022-23'),
(6, '6.5.3(E)', 'Participation in NIRF', '2022-23'),
(6, '6.5.3(F)', 'NBA accreditation', '2022-23'),
(6, '6.5.3(G)', 'NAAC accreditation', '2022-23'),
(6, '6.5.3(H)', 'IQAC Feedback analysis', '2022-23'),
(7, '7.1.1', 'Measures initiated by the institution for the promotion of gender equity during the  year', '2022-23'),
(7, '7.1.1(A)', 'Action plan of WEC for gender sensitization', '2022-23'),
(7, '7.1.1(B)', 'Campus surveillance with CC TV (Audit report)', '2022-23'),
(7, '7.1.1(C)', 'Policy for women security and safety (PASH)', '2022-23'),
(7, '7.1.1(D)', 'Photographs of Exclusive reading rooms, waiting rooms and rest rooms', '2022-23'),
(7, '7.1.1(E)', 'Day care centre for the kids (Beneficiaries)', '2022-23'),
(7, '7.1.1(F)', 'Welfare measures (Maternity leave for two kids)', '2022-23'),
(7, '7.1.1(G)', 'List of the beneficiaries', '2022-23'),
(7, '7.1.10', 'The institution has a prescribed code of conduct for students, teachers, administrators  and other staff and conducts periodic sensitization programmes in this regard', '2022-23'),
(7, '7.1.10(A)', 'Details of the monitoring committee composition and minutes of the committee meeting, number of programmes organized, reports on the various programmes, etc. in support of the claims', '2022-23'),
(7, '7.1.10(B)', 'Policy document on code of ethics', '2022-23'),
(7, '7.1.10(C)', 'Circulars and geo tagged photographs and caption of the activities organized under the metric for teachers, students, administrators and other staffs', '2022-23'),
(7, '7.1.11', 'Institution celebrates / organizes national and international commemorative days,  events and festivals', '2022-23'),
(7, '7.1.11(A)', 'Annual report of the celebrations and commemorative events', '2022-23'),
(7, '7.1.11(B)', 'Photographs of some of the events', '2022-23'),
(7, '7.1.2', 'The Institution has facilities for alternate sources of energy and energy conservation ', '2022-23'),
(7, '7.1.2(A)', 'Geo tagged photographs with caption of the facilities', '2022-23'),
(7, '7.1.2(B)', 'Bills for the purchase of equipment for the facilities', '2022-23'),
(7, '7.1.2(C)', 'Permission document for connection to the grid from Government', '2022-23'),
(7, '7.1.3', 'Describe the facilities in the institution for the management of the following types of  degradable and non-degradable waste (within a maximum of 200 words)', '2022-23'),
(7, '7.1.3(A)', 'Geo tagged photographs of the facilities', '2022-23'),
(7, '7.1.3(B)', 'SOP for solid waste management', '2022-23'),
(7, '7.1.4', 'Water conservation facilities available in the institution', '2022-23'),
(7, '7.1.4(A)', 'Geo tagged photographs with caption of the facilities', '2022-23'),
(7, '7.1.4(B)', 'Bills for the purchase of equipment', '2022-23'),
(7, '7.1.5', 'Green campus initiatives include', '2022-23'),
(7, '7.1.5(A)', 'Policy document on the green campus', '2022-23'),
(7, '7.1.5(B)', 'Geo tagged photographs/Videos with caption of the facilities', '2022-23'),
(7, '7.1.5(C)', 'Circulars for the implementation of the initiatives and any other supporting document', '2022-23'),
(7, '7.1.6', 'Quality audits on environment and energy undertaken by the institution', '2022-23'),
(7, '7.1.6(A)', 'Policy document on environment and energy usage', '2022-23'),
(7, '7.1.6(B)', 'Certificate from the auditing agency', '2022-23'),
(7, '7.1.6(C)', 'Certificates of the awards received from the recognized agency', '2022-23'),
(7, '7.1.6(D)', 'Report on environmental promotional activities conducted beyond the campus', '2022-23'),
(7, '7.1.6(F)', 'Green audit report', '2022-23'),
(7, '7.1.7', 'The Institution has a Divyangjan-friendly and barrier-free environment', '2022-23'),
(7, '7.1.7(A)', 'Policy document and information brochure', '2022-23'),
(7, '7.1.7(B)', 'Geo tagged photos', '2022-23'),
(7, '7.1.7(C)', 'Bills and invoice/purchase order/AMC', '2022-23'),
(7, '7.1.7(D)', 'A rest room should include specific requirements of Divyangjan', '2022-23'),
(7, '7.1.7(E)', 'Bills for the software procured', '2022-23'),
(7, '7.1.8', 'Describe the Institutional efforts/initiatives in providing an inclusive environment i.e.  tolerance and harmony towards cultural, regional, linguistic, communal, socioeconomic and other diversities (within a maximum of 200 words)', '2022-23'),
(7, '7.1.8(A)', 'List of the outbound programs', '2022-23'),
(7, '7.1.8(B)', 'List of national level activities (Cultural, Sports, Academic, Cultural & Sports)', '2022-23'),
(7, '7.1.8(C)', 'List of the faculty & students coming from the out of state', '2022-23'),
(7, '7.1.9', 'Sensitization of students and employees of the institution to constitutional obligations:  values, rights, duties and responsibilities of citizens', '2022-23'),
(7, '7.1.9(A)', 'List of the activities/Events conducted, and the number of students present. (Awareness programs on Women safety & protection, Anti ragging, Judicial rights, Gender equality, Traffic rules, Environment protection, Conservation of natural resources (power & water)) >', '2022-23'),
(7, '7.2.1', 'Provide the weblink on the Institutional website regarding the Best practices as per  the prescribed format of NAAC', '2022-23'),
(7, '7.3.1', 'Institutional Distinctiveness', '2022-23');

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
(1, '1.1.1', 'Copies of PEOs, POs & PSOs for all programs', '2022-23'),
(1, '1.1.2', 'Number of Programmes where syllabus revision was carried out during the year(Data Requirement: Programme Code ,Names of the Programmes revised)', '2022-23'),
(1, '1.1.2(A)', 'List of programs where syllabus revision has been carried out signed by the Principal', '2022-23'),
(1, '1.1.2(B)', 'Approved Minutes of relevant Academic Council/BOS meetings highlighting the specific agenda item relevant to the metric year wise', '2022-23'),
(1, '1.1.3', 'Number of courses focusing on employability/entrepreneurship/ skill development  offered by the Institution during the year(Data Requirement: Name of the Course with Course Code ,Name of the Programme ,Activities which have a direct bearing on employability/ entrepreneurship/ skill  development)', '2022-23'),
(1, '1.1.3(A)', 'Syllabus copy of the courses highlighting Focus on employability/entrepreneurship/ skill development along with their course outcomes.', '2022-23'),
(1, '1.1.3(B)', 'Minutes of the Boards of Studies/ Academic Council meetings with approval for these courses.', '2022-23'),
(1, '1.1.3(C)', 'List of MoUs', '2022-23'),
(1, '1.2.1', 'Number of new courses introduced across all programmes offered during the year(Data Requirement: Name of the newly introduced course (s)  ,Name of the Programme)', '2022-23'),
(1, '1.2.1(A)', 'List of new courses introduced program-wise during the assessment period certified by the Principal.', '2022-23'),
(1, '1.2.1(B)', 'Minutes of relevant Academic Council/BOS meetings highlighting the name of the new courses introduced', '2022-23'),
(1, '1.2.2', 'Number of Programmes offered through Choice Based Credit System  (CBCS)/Elective Course System(Data Requirement: ,Names of all Programmes offered through CBCS ,Names of all Programmes offered through Elective Course System)', '2022-23'),
(1, '1.2.2(A)', 'List of programs in which CBCS/Elective course system implemented in the last completed academic year certified by the principal', '2022-23'),
(1, '1.2.2(B)', 'Structure of the program clearly indicating courses, credits/Electives and Minutes of relevant Academic Council/BOS meetings highlighting the relevant documents to this metric', '2022-23'),
(1, '1.3.1', 'List and Description of courses relevant to Professional Ethics, Gender diversity and equality, Human Values, Environment and Sustainability, Women Empowerment introduced in the Curriculum along with the syllabus should be available in all the departments', '2022-23'),
(1, '1.3.2', 'Number of value-added courses for imparting transferable and life skills offered  during the year:(Data Requirement:  Names of the value-added courses (each with 30 or more contact hours) ,No. of times offered (for each value-added course) during the year ,Total number of students enrolled ,Total number of students completing the course during the year', '2022-23'),
(1, '1.3.2(A)', 'List of value-added courses which are optional and offered outside the curriculum of the programs with authorized sign.', '2022-23'),
(1, '1.3.2(B)', 'Brochure and Course content or syllabus along with course outcome of Value-added courses offered.', '2022-23'),
(1, '1.3.3', 'List of enrolled students for courses addressed in 1.3.2', '2022-23'),
(1, '1.3.4', 'List of students undertaking the field projects/ internships / student projects program-wise in the last completed academic year along with the details of title, place of work etc.', '2022-23'),
(1, '1.4.1', 'Sample Filled in feedback forms from the stakeholders to be provided.', '2022-23'),
(1, '1.4.2', 'Stakeholder feedback analysis report signed by the authority', '2022-23'),
(2, '2.1.1', 'Enrolment of Students ', '2022-23'),
(2, '2.1.1(A)', 'Students admitted(HEI signed documents)', '2022-23'),
(2, '2.1.1(B)', 'Enrolment Number_ AICTE letters of sanctioned intake', '2022-23'),
(2, '2.1.1(C)', 'Enrolment Number _ Ratified list of admitted students', '2022-23'),
(2, '2.1.2', 'Number of seats filled against reserved categories (SC, ST, OBC, Divyangjan,  etc.) as per the reservation policy during the year (exclusive of supernumerary  seats)', '2022-23'),
(2, '2.1.2(A)', 'Reservation seats categories to be considered as per the state rule (APSHE Guidelines)', '2022-23'),
(2, '2.1.2(B)', 'AP regulation for Admissions GO No. 73', '2022-23'),
(2, '2.1.2(C)', 'Guidelines for filling the left-over vacancies under reservation', '2022-23'),
(2, '2.1.2(D)', 'Category wise_ Ratified list of students admitted', '2022-23'),
(2, '2.1.2(E)', 'Admission Abstract(HEI signed documents)', '2022-23'),
(2, '2.2.1', 'The institution assesses students??? learning levels and organises special  programmes for both slow and advanced learners(Present a write-up within a maximum of 200 words)', '2022-23'),
(2, '2.2.1(A)', 'Video records of the specific topics(A document having the links and screen shots of video course/LCS)', '2022-23'),
(2, '2.2.1(B)', 'Classes for the slow learner(Time table conducted for CA failures and attendance sheets)', '2022-23'),
(2, '2.2.1(C)', 'Additional assignment(sample copies of either question papers or evaluated assignment)', '2022-23'),
(2, '2.2.1(D)', 'Make-up classes for course detention students/lateral entry students(Time tables and attendance sheets for each semester(1-8) at least one)', '2022-23'),
(2, '2.2.1(E)', 'Remedial classes conducted for end semester failures(Time tables, attendance sheets and track sheets for each semester(1-8) at least one)', '2022-23'),
(2, '2.2.1(F)', 'List of MOOCs courses(Details of student with course name and sample certificates)', '2022-23'),
(2, '2.2.1(G)', 'List of the advanced learners opted Minors and honors(Abstract certified by controller of examination)', '2022-23'),
(2, '2.2.1(H)', 'Student participation in technical events list with students event details and SAMPLE certificates', '2022-23'),
(2, '2.2.2', 'Student ??? Teacher (full-time) ratio(Data Requirement:  Total number of students in the institution ,Total number of full-time teachers in the institution)', '2022-23'),
(2, '2.2.2(A)', 'List of the full-time teachers', '2022-23'),
(2, '2.2.2(B)', 'Student intake', '2022-23'),
(2, '2.3.1', 'Student-centric methods such as experiential learning, participative learning and  problem-solving methodologies are used for enhancing learning experiences(Present a write-up within a maximum of 200 words.)', '2022-23'),
(2, '2.3.1(A)', 'Academic regulations and curriculum(Recent)', '2022-23'),
(2, '2.3.2', 'Teachers use ICT-enabled tools including online resources for effective teaching  and learning(Present a write-up within a maximum of 200 words.)', '2022-23'),
(2, '2.3.2 (A)', 'ICT Tools Gadgets viz. Graphic tablets, MST, Smart pen, Projector(Geotagged photos with title of photo)', '2022-23'),
(2, '2.3.3', 'Ratio of students to mentor for academic and other related issues', '2022-23'),
(2, '2.3.3(A)', 'Circular of mentor ??? mentees', '2022-23'),
(2, '2.3.3(B)', 'Mentor list as announced by the HEI', '2022-23'),
(2, '2.3.3(C)', 'issues raised and resolved in the mentor system(Total records of a student -&gt; one sample from each semester)', '2022-23'),
(2, '2.3.4', 'Preparation and adherence to Academic Calendar and Teaching Plans by the  institution(Describe the preparation of and adherence to the Academic Calendar and Teaching  Plans by the institution.  Present a write-up within a maximum of 200 words)', '2022-23'),
(2, '2.3.4(A)', 'Academic calendar', '2022-23'),
(2, '2.3.4(B)', 'Proof of course allotment for odd and even semesters', '2022-23'),
(2, '2.3.4(C)', 'Proof for lecture plan, Schedule and diary(one course from each semester)', '2022-23'),
(2, '2.3.4(D)', 'Minutes of AMC', '2022-23'),
(2, '2.4.1', 'Number of full-time teachers against sanctioned posts during the year(Data Requirement:  Number of full-time teachers ,Number of sanctioned posts)', '2022-23'),
(2, '2.4.1(A)', 'Sanction letter indicating number of posts', '2022-23'),
(2, '2.4.1(B)', 'Department wise List of full-time teachers appointed', '2022-23'),
(2, '2.4.2', 'Number of full-time teachers with PhD/ D.M. / M.Ch. / D.N.B Super-Specialty /  DSc / DLitt during the year(Data Requirement: List of full-time teachers with PhD/ D.M. / M.Ch. / D.N.B Super-Specialty /  DSc / DLitt.)', '2022-23'),
(2, '2.4.3', 'Total teaching experience of full-time teachers in the same institution(Data Requirement:  Name and number of full-time teachers and their years of teaching experience in the institution)', '2022-23'),
(2, '2.5.1', 'Number of days from the date of last semester-end/ year- end examination till the  declaration of results during the year(Number of days from the date of last semester-end / year-end examination till the  declaration of results year-wise during the year)', '2022-23'),
(2, '2.5.1(A)', 'Examination Result Notifications', '2022-23'),
(2, '2.5.1(B)', 'List of Programs offered and the last date of the latest semester end exams and date of result declaration', '2022-23'),
(2, '2.5.2', 'Number of students??? complaints/grievances against evaluation against the total  number who appeared in the examinations during the year', '2022-23'),
(2, '2.5.2(A)', 'Number of complaints', '2022-23'),
(2, '2.5.2(B)', 'Minutes of the examination Committee', '2022-23'),
(2, '2.5.3', 'IT integration and reforms in the examination procedures and processes  including Continuous Internal Assessment (CIA) have brought in considerable  improvement in the Examination Management System (EMS) of the Institution(Describe the examination reforms with reference to the following within a minimum  of 200 words :Examination procedures ,Processes/Procedures integrating IT ,Continuous Internal Assessment System) ', '2022-23'),
(2, '2.5.3(A)', 'Examination Regulations', '2022-23'),
(2, '2.5.3(B)', 'Question Paper templates', '2022-23'),
(2, '2.5.3(C)', 'Proof of the hybrid grading sheet', '2022-23'),
(2, '2.5.3(D)', 'Examination Results link in the website', '2022-23'),
(2, '2.5.3(E)', 'Proof of the OMR', '2022-23'),
(2, '2.6.1', 'Programme Outcomes and Course Outcomes for all Programmes offered by the  institution are stated and displayed on the website and communicated to teachers  and students(Describe Course Outcomes (COs) for all courses and the mechanism of  communication to teachers and students within a maximum of 200 words.  Upload COs for all Courses) ', '2022-23'),
(2, '2.6.1(A)', 'List of POs and COs(COs for at least two courses from each semester)', '2022-23'),
(2, '2.6.1(B)', 'Photo gallery of the COs & POs displayed Program wise', '2022-23'),
(2, '2.6.2', 'Attainment of Programme Outcomes and Course Outcomes as evaluated by the  institution(Describe the method of measuring the attainment of POs, PSOs and COs and the  level of attaiment of POs , PSOs and COs in not more than 200 words.)', '2022-23'),
(2, '2.6.2(A)', 'CO and PO attainment', '2022-23'),
(2, '2.6.2(B)', 'Files related to all surveys for the indirect assessment', '2022-23'),
(2, '2.6.3', 'Pass Percentage of students', '2022-23'),
(2, '2.6.3(A)', 'Annual report of CoE indicating the pass percentage', '2022-23'),
(2, '2.6.3(B)', 'Certified report from CoE indicating Students eligible for the degree program', '2022-23'),
(2, '2.7.1', 'Student Satisfaction Survey (SSS) on overall institutional performance  (Institution may design its own questionnaire). Results and details need to be  provided as a weblink', '2022-23'),
(3, '3.1.1', 'The institution???s research facilities are frequently updated and there is a welldefined policy for promotion of research which is uploaded on the institutional  website and implemented(Present a write-up within a maximum of 200 words.)', '2022-23'),
(3, '3.1.1(A)', 'List of research equipment along with the proof of purchases (Bill copies)', '2022-23'),
(3, '3.1.1(B)', 'Policy for Faculty Assessment and Development Scheme', '2022-23'),
(3, '3.1.1(C)', 'Financial Incentives for attending conferences, seminars and QIP', '2022-23'),
(3, '3.1.1(D)', 'Sanction letters for the funded research projects & UCs for the projects completed', '2022-23'),
(3, '3.1.1(E)', 'Copies of all MoUs for collaborative research', '2022-23'),
(3, '3.1.1(F)', 'Minutes of the governing council meeting-reflecting the research promotions', '2022-23'),
(3, '3.1.2', 'Details of Seed money', '2022-23'),
(3, '3.1.3', 'Number of teachers who were awarded national / international fellowship(s) for  advanced studies/research during the year', '2022-23'),
(3, '3.2.1', 'Grants received from Government and Non-Governmental agencies for research  projects, endowments, Chairs during the year (INR in Lakhs)', '2022-23'),
(3, '3.2.1(A)', 'List of Grants received for research projects', '2022-23'),
(3, '3.2.1(B)', 'e-copies of grants sanctioned', '2022-23'),
(3, '3.2.2', 'List of teachers having research projects during the year', '2022-23'),
(3, '3.2.3', 'Number of teachers recognized as research guides', '2022-23'),
(3, '3.2.4', 'Number of departments having research projects', '2022-23'),
(3, '3.2.4(A)', 'Web Links to Funding Agencies', '2022-23'),
(3, '3.3.1', 'Institution has created an ecosystem for innovations and creation and transfer of  knowledge supported by dedicated centres for research, entrepreneurship,  community orientation, incubation, etc.(Present a write-up within a maximum of 200 words)', '2022-23'),
(3, '3.3.1(A)', 'MSME business incubation center ??? sanction letter', '2022-23'),
(3, '3.3.1(B)', 'AICTE sponsored EDC ??? Sanction letters and list of the activities conducted by EDC', '2022-23'),
(3, '3.3.1(C)', 'Dedicated centres for research', '2022-23'),
(3, '3.3.2', 'Detailed report including photos, resource persons etc.', '2022-23'),
(3, '3.4.1', 'The Institution ensures implementation of its Code of Ethics for Research  uploaded in the website through the following:  1. Research Advisory Committee 2. Ethics Committee 3. Inclusion of Research Ethics in the research methodology course work  4. Plagiarism check through authenticated software Options: A. All of the above B. Any 3 of the above C. Any 2 of the above D. Any 1 of the above E. None of the above', '2022-23'),
(3, '3.4.1(A)', 'Research Advisory Committee', '2022-23'),
(3, '3.4.1(B)', 'Ethics Committee', '2022-23'),
(3, '3.4.1(C)', 'Inclusion of Research Ethics in the research methodology course work', '2022-23'),
(3, '3.4.1(D)', 'Anti Plagiarism software approved by JNTU', '2022-23'),
(3, '3.4.2', 'Number of PhD candidates registered per teacher (as per the data given with  regard to recognized PhD guides/ supervisors provided in Metric No. 3.2.3) during  the year', '2022-23'),
(3, '3.4.2(A)', 'List of Faculty along with the names of Research scholars', '2022-23'),
(3, '3.4.2(B)', 'Copy of the Registration letters/Joining letters', '2022-23'),
(3, '3.4.3', 'Number of research papers per teacher in CARE Journals notified on UGC  website during the year', '2022-23'),
(3, '3.4.4', 'Number of books and chapters in edited volumes / books published per teacher  during the year', '2022-23'),
(3, '3.4.5', 'Bibliometrics of the publications based on average Citation Index', '2022-23'),
(3, '3.4.6', 'Bibliometrics of the publication-based h-Index of the University', '2022-23'),
(3, '3.5.1', 'Audited statements for Revenue generated from consultancy', '2022-23'),
(3, '3.5.2', 'Total amount spent on developing facilities, training teachers and clerical/project  staff for undertaking consultancy during the year', '2022-23'),
(3, '3.6.1', 'Extension activities carried out in the neighbourhood sensitising students to social  issues for their holistic development, and the impact thereof during the year', '2022-23'),
(3, '3.6.1(A)', 'List of NSS activities conducted year wise and number of students participated', '2022-23'),
(3, '3.6.1(B)', 'Covid-19 booster report', '2022-23'),
(3, '3.6.1(C)', 'MGNCRE Reports', '2022-23'),
(3, '3.6.1(D)', 'Community Radio report', '2022-23'),
(3, '3.6.1(E)', 'List of NCC activities conducted year wise and number of students participated', '2022-23'),
(3, '3.6.2', 'Number of awards and recognition received by the Institution, its teachers and  students for extension activities from Government / Government-recognised bodies', '2022-23'),
(3, '3.6.3', 'Number of extension and outreach programmes conducted by the institution through  NSS/NCC during the year', '2022-23'),
(3, '3.6.4', 'Number of students participating in extension activities listed in 3.6.3 during the  year', '2022-23'),
(3, '3.7.1', 'Number of collaborative activities during the year for research/ faculty exchange/  student exchange/ internship/ on-the-job training/ project work', '2022-23'),
(3, '3.7.2', 'Number of functional MoUs with institutions of national and/or international  importance, other universities, industries, corporate houses, etc. during the year  (only functional MoUs with ongoing activities to be considered)', '2022-23'),
(3, '3.7.2(A)', 'e-copies of functional MoUs', '2022-23'),
(3, '3.7.2(B)', 'e-copies of Activities of MOUs', '2022-23'),
(4, '4.1.1', 'The Institution has adequate infrastructure and physical facilities for teachinglearning, viz., classrooms, laboratories, computing equipments, etc.', '2022-23'),
(4, '4.1.1(A)', 'Details of the Classrooms, Labs & Other facilities across all the six blocks', '2022-23'),
(4, '4.1.1(B)', 'List of the laboratories with titles', '2022-23'),
(4, '4.1.1(C)', 'Campus LAN diagram', '2022-23'),
(4, '4.1.1(D)', 'Proof of bandwidth', '2022-23'),
(4, '4.1.1(E)', 'List of the software', '2022-23'),
(4, '4.1.1(F)', 'Photo gallery of all the academic blocks, classrooms, drawing hall, seminar hall, auditorium, and Labs', '2022-23'),
(4, '4.1.2', 'The institution has adequate facilities for cultural activities, yoga, sports and games  (indoor and outdoor) including gymnasium, yoga centre, auditorium etc.)', '2022-23'),
(4, '4.1.2(A)', 'Colleague of Geo-tagged pictures', '2022-23'),
(4, '4.1.2(B)', 'Area details of the all the facilities', '2022-23'),
(4, '4.1.2(C)', 'Photo gallery of the various activities', '2022-23'),
(4, '4.1.3', 'Number of classrooms and seminar halls with ICT-enabled facilities', '2022-23'),
(4, '4.1.3(A)', 'Geo-tagged photographs of classrooms with ICT enabled facilities', '2022-23'),
(4, '4.1.3(B)', 'Class Timetables', '2022-23'),
(4, '4.1.4', 'Expenditure for infrastructure augmentation, excluding salary, during the year (INR  in Lakhs)', '2022-23'),
(4, '4.1.4(A)', 'Budget allocation', '2022-23'),
(4, '4.1.4(B)', 'Provide the consolidated fund allocation towards infrastructure augmentation facilities duly certified by Head of the Institution', '2022-23'),
(4, '4.2.1', 'Library is automated using Integrated Library Management System (ILMS)', '2022-23'),
(4, '4.2.1(A)', 'Library operates through LIBSYS', '2022-23'),
(4, '4.2.1(B)', 'All the books are provided with RF Id security tags', '2022-23'),
(4, '4.2.1(C)', 'Copy of the latest License agreement of LIBSYS-7', '2022-23'),
(4, '4.2.1(D)', 'Digital Library', '2022-23'),
(4, '4.2.2', 'Institution has access to the following', '2022-23'),
(4, '4.2.2 (D)', 'Specific details in respect of e-resources selected.', '2022-23'),
(4, '4.2.2 (E)', 'Databases', '2022-23'),
(4, '4.2.2(A)', 'Details of subscriptions of e-journals', '2022-23'),
(4, '4.2.2(B)', 'Letter of subscription', '2022-23'),
(4, '4.2.2(C)', 'Screenshots of the facilities claimed with the name of HEI.', '2022-23'),
(4, '4.2.3', 'Expenditure on purchase of books/ e-books and subscription to journals/e-journals  during the year (INR in lakhs)', '2022-23'),
(4, '4.2.3(A)', 'Consolidated extract of expenditure', '2022-23'),
(4, '4.2.3(B)', 'Invoices of all the expenditure', '2022-23'),
(4, '4.2.4', 'Usage of library by teachers and students (footfalls and login data for online access)', '2022-23'),
(4, '4.2.4(A)', 'Certified e-copy of the ledger for footfalls for 5days', '2022-23'),
(4, '4.2.4(B)', 'Certified screenshots of the data for the same 5 days for online access', '2022-23'),
(4, '4.2.4(C)', 'Last page of accession register details', '2022-23'),
(4, '4.3.1', 'Institution has an IT policy covering Wi-Fi, cyber security, etc. and has allocated  budget for updating its IT facilities', '2022-23'),
(4, '4.3.1(A)', 'Colleague of Geo-tagged pictures', '2022-23'),
(4, '4.3.1(B)', 'Policy document', '2022-23'),
(4, '4.3.1(C)', 'Campus Wi-Fi/Network diagram', '2022-23'),
(4, '4.3.1(D)', 'Budget of the year 2020-21', '2022-23'),
(4, '4.3.2', 'Student - Computer ratio', '2022-23'),
(4, '4.3.2(A)', 'Computer Bills', '2022-23'),
(4, '4.3.2(B)', 'Student strength', '2022-23'),
(4, '4.3.3', 'Bandwidth of internet connection in the Institution and the number of students on  campus', '2022-23'),
(4, '4.3.3(A)', 'Details of available bandwidth of internet connection in the Institution', '2022-23'),
(4, '4.3.3(B)', 'Bills for any one month/one quarter.', '2022-23'),
(4, '4.3.3(C)', 'e-copy of document of agreement with the service provider.', '2022-23'),
(4, '4.3.4', 'Institution has facilities for e-content development', '2022-23'),
(4, '4.3.4(A)', 'Geo tagged photographs of Media Centre, Audio Visual Centre etc.,', '2022-23'),
(4, '4.3.4(B)', 'Purchase bills for Lecture Capturing System', '2022-23'),
(4, '4.3.4(C)', 'Audited income expenditure statement highlighting the relevant expenditure.', '2022-23'),
(4, '4.4.1', 'Expenditure incurred on maintenance of physical and academic support facilities, excluding salary component, during the year (INR in lakhs)', '2022-23'),
(4, '4.4.1(A)', 'Audited statements of accounts.', '2022-23'),
(4, '4.4.2', 'There are established systems and procedures for maintaining and utilizing  physical, academic and support facilities ??? classrooms, laboratory, library, sports  complex, computers, etc.', '2022-23'),
(4, '4.4.2(A)', 'Schedules of Library, Sport complex??', '2022-23'),
(4, '4.4.2(B)', 'Laboratory Timetables', '2022-23'),
(4, '4.4.2(C)', 'SOP for Laboratory utilization', '2022-23'),
(4, '4.4.2(D)', 'SOP for usage of general amenities', '2022-23'),
(4, '4.4.2(E)', 'Geo-tagged photos of GAMYA', '2022-23'),
(4, '4.4.2(F)', 'Maintenance schedules and AMC letters from Estate department', '2022-23'),
(5, '5.1.1', 'Number of students benefitted by scholarships and freeships provided by the  Government during the year', '2022-23'),
(5, '5.1.2', 'Number of students benefitted by scholarships and freeships provided by the  institution and non-government agencies during the year', '2022-23'),
(5, '5.1.3', 'The following Capacity Development and Skill Enhancement activities are  organised for improving students??? capabilities', '2022-23'),
(5, '5.1.3(A)', 'Soft Skill, Language and Communication', '2022-23'),
(5, '5.1.3(B)', 'Yoga Class', '2022-23'),
(5, '5.1.3(C)', 'Awareness of Trends in technology', '2022-23'),
(5, '5.1.4', 'Number of students benefitted from guidance/coaching for competitive  examinations and career counselling offered by the institution during the year', '2022-23'),
(5, '5.1.5', 'The institution adopts the following mechanism for redressal of students???  grievances, including sexual harassment and ragging', '2022-23'),
(5, '5.2.1', 'Number of outgoing students who got placement during the year', '2022-23'),
(5, '5.2.2', 'Number of outgoing students progressing to higher education during the year', '2022-23'),
(5, '5.2.3', 'Number of students qualifying in state/ national/ international level examinations  during the year', '2022-23'),
(5, '5.3.1', 'Number of awards/medals for outstanding performance in sports and/or cultural  activities at inter-university / state /national / international events (award for a team  event should be counted as one) during the year', '2022-23'),
(5, '5.3.2', 'Presence of an active Student Council and representation of students in academic  and administrative bodies/committees of the institution', '2022-23'),
(5, '5.3.3', 'Number of sports and cultural events / competitions organised by the institution', '2022-23'),
(5, '5.4.1', 'The Alumni Association and its Chapters (registered and functional) contribute  significantly to the development of the institution through financial and other  support services', '2022-23'),
(5, '5.4.2', 'Alumni???s financial contribution during the year', '2022-23'),
(6, '6.1.1', 'The governance of the institution is reflective of an effective leadership in tune with  the vision and mission of the Institution', '2022-23'),
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
(6, '6.1.2', 'Effective leadership is reflected in various institutional practices such as decentralization and participative management', '2022-23'),
(6, '6.1.2(A)', 'Strat-Plan', '2022-23'),
(6, '6.1.2(B)', 'Requisition for two set of mid question papers from CoE ??? e-mail proof', '2022-23'),
(6, '6.1.2(C)', 'Declaration of mid question paper set number from CoE??? e-mail proof', '2022-23'),
(6, '6.1.2(D)', 'Uniform Evaluation', '2022-23'),
(6, '6.1.2(E)', 'Research Review meeting minutes', '2022-23'),
(6, '6.2.1', 'The institutional Strategic/ Perspective plan has been clearly articulated and  implemented', '2022-23'),
(6, '6.2.1(A)', 'Strat-Plan', '2022-23'),
(6, '6.2.1(B)', 'Merit scholarships based on AP-EAMCET rank', '2022-23'),
(6, '6.2.1(C)', 'Meritorious scholarships for students', '2022-23'),
(6, '6.2.1(D)', 'Details of the GATE training classes conducted; List of students attended GATE Coaching & secured score', '2022-23'),
(6, '6.2.1(E)', 'Details of the CRT programs/ Technical training/Competitions conducted (Total number of hours), the List of the students attended the training programs & Proof of attendance, Branch wise list of the students placed, and the number of companies visited. List of students attended the WTN.', '2022-23'),
(6, '6.2.1(F)', 'Motivational and inspirational talks by the industry experts', '2022-23'),
(6, '6.2.1(G)', 'Social media updates', '2022-23'),
(6, '6.2.2', 'The functioning of the various institutional bodies is effective and efficient as visible  from the policies, administrative set-up, appointment and service rules, procedures,  etc. ', '2022-23'),
(6, '6.2.2(A)', 'Organogram', '2022-23'),
(6, '6.2.2(B)', 'HR & Service rules/Incentive policies', '2022-23'),
(6, '6.2.2(D)', 'Frequency and conduct of the meetings of governance committees (GC, AC, BOS, FC)', '2022-23'),
(6, '6.2.2(E)', 'SOP for procurement (AOP, MRN, Comparative statements and Purchase Orders)', '2022-23'),
(6, '6.2.3', 'Implementation of e-governance in areas of operation: 1. Administration 2. Finance and Accounts 3. Student Admission and Support 4. Examination', '2022-23'),
(6, '6.3.1', 'The institution has effective welfare measures for teaching and non-teaching staff  and avenues for their career development/ progression', '2022-23'),
(6, '6.3.1(A)', 'HR Policies (Welfare & Career development)', '2022-23'),
(6, '6.3.1(B)', 'Details of the training programs conducted for teaching & non-teaching staff', '2022-23'),
(6, '6.3.1(C)', 'Details of the staff (Teaching & Non-teaching promoted)', '2022-23'),
(6, '6.3.1(D)', 'Details of on campus housing, Term insurance, Medical insurance, Children education, ESI, Cooperative credit society, Concessions in IP/OP services and Gratuity', '2022-23'),
(6, '6.3.1(E)', 'Details of the faculty received incentives for completion of Ph.D./ QIP', '2022-23'),
(6, '6.3.2', 'Number of teachers provided with financial support to attend conferences /  workshops and towards payment of membership fee of professional bodies during the  year', '2022-23'),
(6, '6.3.2(A)', 'Number of teachers provided with financial support to attend conferences / workshops and towards payment of membership fee of professional bodies during the year', '2022-23'),
(6, '6.3.3', 'Number of professional development / administrative training programmes  organized by the Institution for its teaching and non-teaching staff during the year', '2022-23'),
(6, '6.3.3(A)', 'Annual Reports highlighting training programs conducted for teaching & non teaching', '2022-23'),
(6, '6.3.3(B)', 'Training programs conducted for teaching & non-teaching', '2022-23'),
(6, '6.3.4', 'Number of teachers who have undergone online/ face-to-face Faculty Development  Programmes during the year: (Professional Development Programmes, Orientation / Induction Programmes,  Refresher Courses, Short-Term Course, etc.)', '2022-23'),
(6, '6.3.4(A)', 'List of Faculty FDPS Attended & Proofs', '2022-23'),
(6, '6.4.1', 'Institution conducts internal and external financial audits regularly', '2022-23'),
(6, '6.4.2', 'Funds / Grants received from non-government bodies, individuals, and  philanthropists during the year (not covered in Criterion III and V) (INR in lakhs)', '2022-23'),
(6, '6.4.3', 'Institutional strategies for mobilisation of funds and the optimal utilisation of  resources', '2022-23'),
(6, '6.5.1', 'Internal Quality Assurance Cell (IQAC) has contributed significantly for  institutionalizing quality assurance strategies and processes visible in terms of  incremental improvements made during the preceding year with regard to quality (in  case of the First Cycle): Incremental improvements made during the preceding year with regard to quality  and post-accreditation quality initiatives (Second and subsequent cycles)', '2022-23'),
(6, '6.5.1(A)', 'List of companies hosted Internship', '2022-23'),
(6, '6.5.1(B)', 'FADS ??? Supporting Docs', '2022-23'),
(6, '6.5.1(C)', 'Consolidated initiatives of IQAC (strategies & contributions of IQAC mentioned)', '2022-23'),
(6, '6.5.2', 'The institution reviews its teaching-learning process, structures and methodologies  of operation and learning outcomes at periodic intervals through its IQAC as per  norms', '2022-23'),
(6, '6.5.2(A)', 'List of the IQAC initiatives taken up to enhance the students??? performance (Course coordinator meetings, AMC meeting minutes, Remedial classes for slow learners)', '2022-23'),
(6, '6.5.2(B)', 'Details of the progression of the students from 1st to 8th semesters branch wise for 2020-21 for all the batches graduated ??? 1 Bar chart, in each bar chart with eight bars', '2022-23'),
(6, '6.5.2(C)', 'Structures & methodologies of operations ISO audits, Academic audits -Internal & External branch wise', '2022-23'),
(6, '6.5.2(D)', 'Internal & External Academic Audit Report', '2022-23'),
(6, '6.5.3', 'Quality assurance initiatives of the institution include', '2022-23'),
(6, '6.5.3(A)', 'IQAC meeting minutes', '2022-23'),
(6, '6.5.3(B)', 'Feedback system of the institution', '2022-23'),
(6, '6.5.3(C)', 'Feedback system for design and review of syllabus', '2022-23'),
(6, '6.5.3(D)', 'Collaborative quality initiatives', '2022-23'),
(6, '6.5.3(E)', 'Participation in NIRF', '2022-23'),
(6, '6.5.3(F)', 'NBA accreditation', '2022-23'),
(6, '6.5.3(G)', 'NAAC accreditation', '2022-23'),
(6, '6.5.3(H)', 'IQAC Feedback analysis', '2022-23'),
(7, '7.1.1', 'Measures initiated by the institution for the promotion of gender equity during the  year', '2022-23'),
(7, '7.1.1(A)', 'Action plan of WEC for gender sensitization', '2022-23'),
(7, '7.1.1(B)', 'Campus surveillance with CC TV (Audit report)', '2022-23'),
(7, '7.1.1(C)', 'Policy for women security and safety (PASH)', '2022-23'),
(7, '7.1.1(D)', 'Photographs of Exclusive reading rooms, waiting rooms and rest rooms', '2022-23'),
(7, '7.1.1(E)', 'Day care centre for the kids (Beneficiaries)', '2022-23'),
(7, '7.1.1(F)', 'Welfare measures (Maternity leave for two kids)', '2022-23'),
(7, '7.1.1(G)', 'List of the beneficiaries', '2022-23'),
(7, '7.1.10', 'The institution has a prescribed code of conduct for students, teachers, administrators  and other staff and conducts periodic sensitization programmes in this regard', '2022-23'),
(7, '7.1.10(A)', 'Details of the monitoring committee composition and minutes of the committee meeting, number of programmes organized, reports on the various programmes, etc. in support of the claims', '2022-23'),
(7, '7.1.10(B)', 'Policy document on code of ethics', '2022-23'),
(7, '7.1.10(C)', 'Circulars and geo tagged photographs and caption of the activities organized under the metric for teachers, students, administrators and other staffs', '2022-23'),
(7, '7.1.11', 'Institution celebrates / organizes national and international commemorative days,  events and festivals', '2022-23'),
(7, '7.1.11(A)', 'Annual report of the celebrations and commemorative events', '2022-23'),
(7, '7.1.11(B)', 'Photographs of some of the events', '2022-23'),
(7, '7.1.2', 'The Institution has facilities for alternate sources of energy and energy conservation ', '2022-23'),
(7, '7.1.2(A)', 'Geo tagged photographs with caption of the facilities', '2022-23'),
(7, '7.1.2(B)', 'Bills for the purchase of equipment for the facilities', '2022-23'),
(7, '7.1.2(C)', 'Permission document for connection to the grid from Government', '2022-23'),
(7, '7.1.3', 'Describe the facilities in the institution for the management of the following types of  degradable and non-degradable waste (within a maximum of 200 words)', '2022-23'),
(7, '7.1.3(A)', 'Geo tagged photographs of the facilities', '2022-23'),
(7, '7.1.3(B)', 'SOP for solid waste management', '2022-23'),
(7, '7.1.4', 'Water conservation facilities available in the institution', '2022-23'),
(7, '7.1.4(A)', 'Geo tagged photographs with caption of the facilities', '2022-23'),
(7, '7.1.4(B)', 'Bills for the purchase of equipment', '2022-23'),
(7, '7.1.5', 'Green campus initiatives include', '2022-23'),
(7, '7.1.5(A)', 'Policy document on the green campus', '2022-23'),
(7, '7.1.5(B)', 'Geo tagged photographs/Videos with caption of the facilities', '2022-23'),
(7, '7.1.5(C)', 'Circulars for the implementation of the initiatives and any other supporting document', '2022-23'),
(7, '7.1.6', 'Quality audits on environment and energy undertaken by the institution', '2022-23'),
(7, '7.1.6(A)', 'Policy document on environment and energy usage', '2022-23'),
(7, '7.1.6(B)', 'Certificate from the auditing agency', '2022-23'),
(7, '7.1.6(C)', 'Certificates of the awards received from the recognized agency', '2022-23'),
(7, '7.1.6(D)', 'Report on environmental promotional activities conducted beyond the campus', '2022-23'),
(7, '7.1.6(F)', 'Green audit report', '2022-23'),
(7, '7.1.7', 'The Institution has a Divyangjan-friendly and barrier-free environment', '2022-23'),
(7, '7.1.7(A)', 'Policy document and information brochure', '2022-23'),
(7, '7.1.7(B)', 'Geo tagged photos', '2022-23'),
(7, '7.1.7(C)', 'Bills and invoice/purchase order/AMC', '2022-23'),
(7, '7.1.7(D)', 'A rest room should include specific requirements of Divyangjan', '2022-23'),
(7, '7.1.7(E)', 'Bills for the software procured', '2022-23'),
(7, '7.1.8', 'Describe the Institutional efforts/initiatives in providing an inclusive environment i.e.  tolerance and harmony towards cultural, regional, linguistic, communal, socioeconomic and other diversities (within a maximum of 200 words)', '2022-23'),
(7, '7.1.8(A)', 'List of the outbound programs', '2022-23'),
(7, '7.1.8(B)', 'List of national level activities (Cultural, Sports, Academic, Cultural & Sports)', '2022-23'),
(7, '7.1.8(C)', 'List of the faculty & students coming from the out of state', '2022-23'),
(7, '7.1.9', 'Sensitization of students and employees of the institution to constitutional obligations:  values, rights, duties and responsibilities of citizens', '2022-23'),
(7, '7.1.9(A)', 'List of the activities/Events conducted, and the number of students present. (Awareness programs on Women safety & protection, Anti ragging, Judicial rights, Gender equality, Traffic rules, Environment protection, Conservation of natural resources (power & water)) >', '2022-23'),
(7, '7.2.1', 'Provide the weblink on the Institutional website regarding the Best practices as per  the prescribed format of NAAC', '2022-23'),
(7, '7.3.1', 'Institutional Distinctiveness', '2022-23');

-- --------------------------------------------------------

--
-- Table structure for table `dc_up_files`
--

CREATE TABLE `dc_up_files` (
  `id` int(11) NOT NULL,
  `Username` varchar(100) NOT NULL,
  `file_name` varchar(455) NOT NULL,
  `acd_year` varchar(50) DEFAULT NULL,
  `Main_file_type` varchar(100) NOT NULL,
  `file_type` varchar(200) DEFAULT NULL,
  `file_path` varchar(1000) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `dept_id` int(11) NOT NULL,
  `dept_name` varchar(100) NOT NULL,
  `is_academic` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`dept_id`, `dept_name`, `is_academic`, `created_at`) VALUES
(1, 'CSE', 1, '2026-10-30 18:09:41'),
(2, 'AIDS', 1, '2026-10-30 18:09:41'),
(3, 'AIML', 1, '2026-10-30 18:09:41'),
(4, 'ECE', 1, '2026-10-30 18:09:41'),
(5, 'EEE', 1, '2026-10-30 18:09:41'),
(6, 'MECH', 1, '2026-10-30 18:09:41'),
(7, 'CIVIL', 1, '2026-10-30 18:09:41'),
(8, 'IT', 1, '2026-10-30 18:09:41'),
(9, 'BSH', 1, '2026-10-30 18:09:41'),
(10, 'CSE-CS', 0, '2026-10-30 18:09:41'),
(11, 'PE', 0, '2026-10-30 18:09:41'),
(12, 'PG', 0, '2026-10-30 18:09:41'),
(14, 'Mathematics', 1, '2026-10-30 18:09:41'),
(15, 'Physics', 1, '2026-10-30 18:09:41'),
(16, 'Chemistry', 1, '2026-10-30 18:09:41'),
(17, 'IQAC', 0, '2026-10-30 18:09:41'),
(18, 'R&D', 0, '2026-10-30 18:09:41'),
(19, 'Exam_Section', 0, '2026-10-30 18:09:41'),
(20, 'NAAC', 0, '2026-10-30 18:09:41'),
(21, 'NBA', 0, '2026-10-30 18:09:41'),
(22, 'NCC', 0, '2026-10-30 18:09:41'),
(23, 'Sports', 0, '2026-10-30 18:09:41'),
(24, 'Clubs', 0, '2026-10-30 18:09:41'),
(25, 'NSS', 0, '2026-10-30 18:09:41'),
(26, 'Women_Empowerment', 0, '2026-10-30 18:09:41'),
(27, 'IIC', 0, '2026-10-30 18:09:41'),
(28, 'PASH', 0, '2026-10-30 18:09:41'),
(29, 'Antiragging', 0, '2026-10-30 18:09:41'),
(30, 'SAC', 0, '2026-10-30 18:09:41');

-- --------------------------------------------------------

--
-- Table structure for table `dept_files`
--

CREATE TABLE `dept_files` (
  `id` int(255) NOT NULL,
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
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `documents`
--

CREATE TABLE `documents` (
  `doc_id` int(11) NOT NULL,
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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `documents`
--

INSERT INTO `documents` (`doc_id`, `type_id`, `uploaded_by`, `dept_id`, `mentor_id`, `academic_year_id`, `title`, `file_path`, `status`, `current_step`, `rejection_reason`, `created_at`, `updated_at`) VALUES
(17, 6, 64, 1, NULL, 3, 'cse fdpu1', 'uploads/fdp_attended/fdp_attended_merged_6ac52aaf15364.pdf', 'accepted', NULL, NULL, '2026-10-06 17:06:54', '2026-10-06 17:08:40'),
(18, 4, 64, 1, NULL, 1, 'cse u1 conference proceedings', 'uploads/conference/conference_merged_6ac52c26c9ad5.pdf', 'accepted', NULL, NULL, '2026-10-06 17:13:10', '2026-10-26 15:14:32'),
(19, 2, 64, 1, NULL, 1, 'cse u1 patent', '', 'accepted', NULL, NULL, '2026-10-06 17:14:55', '2026-10-26 15:14:32'),
(20, 3, 64, 1, NULL, 1, 'cse u1 research paper', '', 'accepted', NULL, NULL, '2026-10-06 17:17:14', '2026-10-26 15:14:32'),
(21, 7, 64, 1, NULL, 3, 'cse u1 fdp org', 'uploads/fdp_organised/fdp_organised_merged_6ac52e8957c35.pdf', 'accepted', NULL, NULL, '2026-10-06 17:23:21', '2026-10-06 17:53:14'),
(22, 8, 64, 1, NULL, 3, 'cse u1 conference org', 'uploads/conf_organised/conf_organised_merged_6ac52f1f57a7a.pdf', 'accepted', NULL, NULL, '2026-10-06 17:25:51', '2026-10-06 17:53:21'),
(24, 3, 66, 1, NULL, 3, 'cse u2 Journal paper', '', 'accepted', NULL, NULL, '2026-10-07 04:47:36', '2026-10-26 15:14:32'),
(25, 6, 66, 1, NULL, 3, 'cse u2 fdp', 'uploads/fdp_attended/fdp_attended_merged_6ac5cf5a57c67.pdf', 'accepted', NULL, NULL, '2026-10-07 04:49:30', '2026-10-07 04:53:18'),
(26, 2, 66, 1, NULL, 1, 'cse u2 patent', '', 'accepted', NULL, NULL, '2026-10-07 04:51:02', '2026-10-26 15:14:32'),
(27, 7, 67, 2, NULL, 3, 'aids u1 fdp', 'uploads/fdp_organised/fdp_organised_merged_6ac5d47cd1a5f.pdf', 'accepted', NULL, NULL, '2026-10-07 05:11:24', '2026-10-07 05:14:28'),
(28, 29, 64, 1, NULL, 3, 'cse career counsel', 'uploads/student_activity_file/student_activity_file_merged_6ac7bac103554.pdf', 'accepted', NULL, NULL, '2026-10-08 15:46:08', '2026-10-08 16:02:56'),
(29, 29, 64, 1, NULL, 3, 'cse u1 grad talk', 'uploads/student_activity_file/student_activity_file_merged_6ac7bb94c717a.pdf', 'accepted', NULL, NULL, '2026-10-08 15:49:40', '2026-10-08 16:03:04'),
(30, 29, 64, 1, NULL, 3, 'cse u1 soft skills', 'uploads/student_activity_file/student_activity_file_merged_6ac7bc2b5a4db.pdf', 'accepted', NULL, NULL, '2026-10-08 15:52:11', '2026-10-08 16:03:10'),
(31, 29, 64, 1, NULL, 3, 'professional soc ACM grad talks', 'uploads/student_activity_file/student_activity_file_merged_6ac7bd7b91bd0.pdf', 'accepted', NULL, NULL, '2026-10-08 15:57:47', '2026-10-08 16:03:17'),
(32, 29, 64, 1, NULL, 3, 'clubs gdc hackathon', 'uploads/student_activity_file/student_activity_file_merged_6ac7be30bfa41.pdf', 'accepted', NULL, NULL, '2026-10-08 16:00:48', '2026-10-08 16:03:24'),
(33, 29, 67, 2, NULL, 3, 'aids user 1 language and communication skills', 'uploads/student_activity_file/student_activity_file_merged_6acd0144d3d17.pdf', 'accepted', NULL, NULL, '2026-10-12 15:48:20', '2026-10-12 15:49:05'),
(34, 29, 67, 2, NULL, 1, 'aids user 1 soft skill event', 'uploads/student_activity_file/student_activity_file_merged_6acd030e315d4.pdf', 'accepted', NULL, NULL, '2026-10-12 15:55:58', '2026-10-26 15:14:32'),
(36, 29, 6, 1, NULL, 1, 'Test Stepcone Upload', '', 'accepted', NULL, NULL, '2026-10-15 13:09:07', '2026-10-16 05:31:20'),
(37, 29, 6, 1, NULL, 1, 'Test Stepcone Upload', '', 'accepted', NULL, NULL, '2026-10-15 13:09:51', '2026-10-16 05:31:13'),
(41, 29, 64, 1, NULL, 2, 'codechef student', 'uploads/student_activity_file/student_activity_file_merged_6ad1a01f99fdb.pdf', 'accepted', NULL, NULL, '2026-10-16 03:55:11', '2026-10-16 05:31:31'),
(42, 29, 68, 2, NULL, 1, 'soft skills aids', 'uploads/student_activity_file/student_activity_file_merged_6ad1a1e835686.pdf', 'accepted', NULL, NULL, '2026-10-16 04:02:48', '2026-10-16 04:08:57'),
(43, 29, 69, 2, NULL, 2, 'l and c aids', 'uploads/student_activity_file/student_activity_file_merged_6ad1a317f26de.pdf', 'accepted', NULL, NULL, '2026-10-16 04:07:51', '2026-10-16 04:09:04'),
(44, 29, 70, 7, NULL, 2, 'fueling innovation', 'uploads/student_activity_file/student_activity_file_merged_6ad1a60ed0179.pdf', 'accepted', NULL, NULL, '2026-10-16 04:20:30', '2026-10-16 04:32:39'),
(45, 29, 70, 7, NULL, 2, 'Startup and Entrepreneurship', 'uploads/student_activity_file/student_activity_file_merged_6ad1a70963110.pdf', 'accepted', NULL, NULL, '2026-10-16 04:24:41', '2026-10-16 04:32:46'),
(46, 29, 66, 1, NULL, 2, '\"AWS Academy cloud foundation', 'uploads/student_activity_file/student_activity_file_merged_6ad1aa3d579f5.pdf', 'accepted', NULL, NULL, '2026-10-16 04:38:21', '2026-10-16 05:31:39'),
(47, 29, 66, 1, NULL, 2, 'Data analytics and it\'s applications', 'uploads/student_activity_file/student_activity_file_merged_6ad1aafc59823.pdf', 'accepted', NULL, NULL, '2026-10-16 04:41:32', '2026-10-16 05:31:47'),
(48, 29, 66, 1, NULL, 1, 'Hands on dev ops', 'uploads/student_activity_file/student_activity_file_merged_6ad1abb3ea90a.pdf', 'accepted', NULL, NULL, '2026-10-16 04:44:35', '2026-10-16 05:31:55'),
(50, 29, 71, 5, NULL, 1, 'Career Opportunities in Software Sector', 'uploads/student_activity_file/student_activity_file_merged_6ad1ba6e2396b.pdf', 'accepted', NULL, NULL, '2026-10-16 05:47:26', '2026-10-16 06:02:12'),
(52, 29, 71, 5, NULL, 2, 'Preparation for Civil Service Examinations', 'uploads/student_activity_file/student_activity_file_merged_6ad1bb154e296.pdf', 'accepted', NULL, NULL, '2026-10-16 05:50:13', '2026-10-16 06:02:18'),
(53, 29, 71, 5, NULL, 2, 'Technical paper presentation', 'uploads/student_activity_file/student_activity_file_merged_6ad1bbb5345e6.pdf', 'accepted', NULL, NULL, '2026-10-16 05:52:53', '2026-10-16 06:02:24'),
(54, 29, 71, 5, NULL, 1, 'Aurdino and iot', 'uploads/student_activity_file/student_activity_file_merged_6ad1bc461d219.pdf', 'accepted', NULL, NULL, '2026-10-16 05:55:18', '2026-10-16 06:02:30'),
(55, 29, 71, 5, NULL, 1, 'Brain vitae', 'uploads/student_activity_file/student_activity_file_merged_6ad1bcea6016e.pdf', 'accepted', NULL, NULL, '2026-10-16 05:56:29', '2026-10-16 06:02:36'),
(56, 29, 71, 5, NULL, 1, 'Electrical ladder', 'uploads/student_activity_file/student_activity_file_merged_6ad1bd58de634.pdf', 'accepted', NULL, NULL, '2026-10-16 05:59:52', '2026-10-16 06:02:42');

-- --------------------------------------------------------

--
-- Table structure for table `document_actions`
--

CREATE TABLE `document_actions` (
  `action_id` int(11) NOT NULL,
  `doc_id` int(11) NOT NULL,
  `acted_by` int(11) NOT NULL,
  `action` enum('uploaded','approved','rejected','resubmitted') NOT NULL,
  `step_id` int(11) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `acted_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `document_actions`
--

INSERT INTO `document_actions` (`action_id`, `doc_id`, `acted_by`, `action`, `step_id`, `remarks`, `acted_at`) VALUES
(14, 17, 64, 'uploaded', 1, NULL, '2026-10-06 22:36:55'),
(15, 17, 14, '', 1, '', '2026-10-06 22:37:55'),
(16, 17, 6, '', 2, '', '2026-10-06 22:38:16'),
(17, 17, 49, '', 3, '', '2026-10-06 22:38:40'),
(18, 18, 64, 'uploaded', 1, NULL, '2026-10-06 22:43:10'),
(19, 19, 64, 'uploaded', 1, NULL, '2026-10-06 22:44:55'),
(20, 20, 64, 'uploaded', 1, NULL, '2026-10-06 22:47:14'),
(21, 21, 64, 'uploaded', 1, NULL, '2026-10-06 22:53:21'),
(22, 22, 64, 'uploaded', 1, NULL, '2026-10-06 22:55:51'),
(24, 18, 14, '', 1, '', '2026-10-06 23:20:13'),
(25, 19, 14, '', 1, '', '2026-10-06 23:20:23'),
(26, 20, 14, '', 1, '', '2026-10-06 23:20:31'),
(27, 21, 14, '', 1, '', '2026-10-06 23:20:38'),
(28, 22, 14, '', 1, '', '2026-10-06 23:20:45'),
(30, 18, 6, '', 2, '', '2026-10-06 23:21:33'),
(31, 19, 6, '', 2, '', '2026-10-06 23:21:39'),
(32, 20, 6, '', 2, '', '2026-10-06 23:21:45'),
(33, 21, 6, '', 2, '', '2026-10-06 23:22:00'),
(34, 22, 6, '', 2, '', '2026-10-06 23:22:04'),
(36, 18, 49, '', 3, '', '2026-10-06 23:22:50'),
(37, 19, 49, '', 3, '', '2026-10-06 23:22:56'),
(38, 20, 49, '', 3, '', '2026-10-06 23:23:04'),
(39, 21, 49, '', 3, '', '2026-10-06 23:23:14'),
(40, 22, 49, '', 3, '', '2026-10-06 23:23:21'),
(43, 24, 66, 'uploaded', 1, NULL, '2026-10-07 10:17:36'),
(44, 25, 66, 'uploaded', 1, NULL, '2026-10-07 10:19:30'),
(45, 26, 66, 'uploaded', 1, NULL, '2026-10-07 10:21:02'),
(46, 24, 14, '', 1, '', '2026-10-07 10:21:59'),
(47, 25, 14, '', 1, '', '2026-10-07 10:22:06'),
(48, 26, 14, '', 1, '', '2026-10-07 10:22:13'),
(49, 24, 6, '', 2, '', '2026-10-07 10:22:34'),
(50, 25, 6, '', 2, '', '2026-10-07 10:22:40'),
(51, 26, 6, '', 2, '', '2026-10-07 10:22:47'),
(52, 24, 49, '', 3, '', '2026-10-07 10:23:10'),
(53, 25, 49, '', 3, '', '2026-10-07 10:23:18'),
(54, 26, 49, '', 3, '', '2026-10-07 10:23:25'),
(55, 27, 67, 'uploaded', 1, NULL, '2026-10-07 10:41:24'),
(56, 27, 16, '', 1, '', '2026-10-07 10:43:56'),
(57, 27, 8, '', 2, '', '2026-10-07 10:44:13'),
(58, 27, 49, '', 3, '', '2026-10-07 10:44:28'),
(59, 28, 64, 'uploaded', 11, NULL, '2026-10-08 21:16:08'),
(60, 29, 64, 'uploaded', 11, NULL, '2026-10-08 21:19:40'),
(61, 30, 64, 'uploaded', 11, NULL, '2026-10-08 21:22:11'),
(62, 31, 64, 'uploaded', 11, NULL, '2026-10-08 21:27:47'),
(63, 32, 64, 'uploaded', 11, NULL, '2026-10-08 21:30:48'),
(64, 28, 14, '', 11, '', '2026-10-08 21:31:53'),
(65, 29, 14, '', 11, '', '2026-10-08 21:32:00'),
(66, 30, 14, '', 11, '', '2026-10-08 21:32:07'),
(67, 31, 14, '', 11, '', '2026-10-08 21:32:14'),
(68, 32, 14, '', 11, '', '2026-10-08 21:32:21'),
(69, 28, 6, '', 12, '', '2026-10-08 21:32:56'),
(70, 29, 6, '', 12, '', '2026-10-08 21:33:04'),
(71, 30, 6, '', 12, '', '2026-10-08 21:33:10'),
(72, 31, 6, '', 12, '', '2026-10-08 21:33:17'),
(73, 32, 6, '', 12, '', '2026-10-08 21:33:24'),
(74, 33, 67, 'uploaded', 11, NULL, '2026-10-12 21:18:20'),
(75, 33, 16, '', 11, '', '2026-10-12 21:18:50'),
(76, 33, 8, '', 12, '', '2026-10-12 21:19:05'),
(77, 34, 67, 'uploaded', 11, NULL, '2026-10-12 21:25:58'),
(78, 34, 16, '', 11, '', '2026-10-12 21:29:11'),
(79, 34, 8, '', 12, '', '2026-10-12 21:29:54'),
(80, 36, 6, 'uploaded', 11, NULL, '2026-10-15 18:39:07'),
(81, 37, 6, 'uploaded', 11, NULL, '2026-10-15 18:39:51'),
(82, 41, 64, 'uploaded', 11, NULL, '2026-10-16 09:25:11'),
(83, 42, 68, 'uploaded', 11, NULL, '2026-10-16 09:32:48'),
(84, 43, 69, 'uploaded', 11, NULL, '2026-10-16 09:37:51'),
(85, 42, 16, '', 11, '', '2026-10-16 09:38:14'),
(86, 43, 16, '', 11, '', '2026-10-16 09:38:38'),
(87, 42, 8, '', 12, '', '2026-10-16 09:38:57'),
(88, 43, 8, '', 12, '', '2026-10-16 09:39:04'),
(89, 44, 70, 'uploaded', 11, NULL, '2026-10-16 09:50:30'),
(90, 45, 70, 'uploaded', 11, NULL, '2026-10-16 09:54:41'),
(91, 44, 21, '', 11, '', '2026-10-16 10:00:32'),
(92, 45, 21, '', 11, '', '2026-10-16 10:00:58'),
(93, 44, 12, '', 12, '', '2026-10-16 10:02:39'),
(94, 45, 12, '', 12, '', '2026-10-16 10:02:46'),
(95, 46, 66, 'uploaded', 11, NULL, '2026-10-16 10:08:21'),
(96, 47, 66, 'uploaded', 11, NULL, '2026-10-16 10:11:32'),
(97, 48, 66, 'uploaded', 11, NULL, '2026-10-16 10:14:35'),
(98, 36, 14, '', 11, '', '2026-10-16 10:16:09'),
(99, 37, 14, '', 11, '', '2026-10-16 10:16:32'),
(100, 41, 14, '', 11, '', '2026-10-16 10:16:46'),
(101, 46, 14, '', 11, '', '2026-10-16 10:16:59'),
(102, 47, 14, '', 11, '', '2026-10-16 10:17:18'),
(103, 48, 14, '', 11, '', '2026-10-16 10:17:28'),
(104, 37, 6, '', 12, '', '2026-10-16 11:01:13'),
(105, 36, 6, '', 12, '', '2026-10-16 11:01:20'),
(106, 41, 6, '', 12, '', '2026-10-16 11:01:31'),
(107, 46, 6, '', 12, '', '2026-10-16 11:01:39'),
(108, 47, 6, '', 12, '', '2026-10-16 11:01:47'),
(109, 48, 6, '', 12, '', '2026-10-16 11:01:55'),
(110, 50, 71, 'uploaded', 11, NULL, '2026-10-16 11:17:26'),
(111, 52, 71, 'uploaded', 11, NULL, '2026-10-16 11:20:13'),
(112, 53, 71, 'uploaded', 11, NULL, '2026-10-16 11:22:53'),
(113, 54, 71, 'uploaded', 11, NULL, '2026-10-16 11:25:18'),
(114, 55, 71, 'uploaded', 11, NULL, '2026-10-16 11:26:29'),
(115, 55, 71, 'resubmitted', NULL, 'Author updated the document.', '2026-10-16 11:28:02'),
(116, 56, 71, 'uploaded', 11, NULL, '2026-10-16 11:29:52'),
(117, 50, 19, '', 11, '', '2026-10-16 11:30:55'),
(118, 52, 19, '', 11, '', '2026-10-16 11:31:01'),
(119, 53, 19, '', 11, '', '2026-10-16 11:31:07'),
(120, 54, 19, '', 11, '', '2026-10-16 11:31:14'),
(121, 55, 19, '', 11, '', '2026-10-16 11:31:21'),
(122, 56, 19, '', 11, '', '2026-10-16 11:31:28'),
(123, 50, 10, '', 12, '', '2026-10-16 11:32:12'),
(124, 52, 10, '', 12, '', '2026-10-16 11:32:18'),
(125, 53, 10, '', 12, '', '2026-10-16 11:32:24'),
(126, 54, 10, '', 12, '', '2026-10-16 11:32:30'),
(127, 55, 10, '', 12, '', '2026-10-16 11:32:36'),
(128, 56, 10, '', 12, '', '2026-10-16 11:32:42');

-- --------------------------------------------------------

--
-- Table structure for table `document_files`
--

CREATE TABLE `document_files` (
  `file_id` int(11) NOT NULL,
  `doc_id` int(11) NOT NULL,
  `file_label` varchar(100) NOT NULL,
  `original_name` varchar(255) NOT NULL,
  `stored_name` varchar(255) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `mime_type` varchar(100) DEFAULT NULL,
  `file_size` int(11) DEFAULT NULL,
  `uploaded_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `document_files`
--

INSERT INTO `document_files` (`file_id`, `doc_id`, `file_label`, `original_name`, `stored_name`, `file_path`, `mime_type`, `file_size`, `uploaded_at`) VALUES
(13, 17, 'certificate', 'user1-fdps1.pdf', 'fdp_attended_certificate_6ac52aaf02815.pdf', 'uploads/fdp_attended/fdp_attended_certificate_6ac52aaf02815.pdf', 'application/pdf', 5424, '2026-10-06 22:36:55'),
(14, 17, 'brochure', 'user1-fdps1 broucher.pdf', 'fdp_attended_brochure_6ac52aaf042cc.pdf', 'uploads/fdp_attended/fdp_attended_brochure_6ac52aaf042cc.pdf', 'application/pdf', 9318, '2026-10-06 22:36:55'),
(15, 17, 'merged_pdf', 'Merged_Document.pdf', 'fdp_attended_merged_6ac52aaf15364.pdf', 'uploads/fdp_attended/fdp_attended_merged_6ac52aaf15364.pdf', 'application/pdf', 15794, '2026-10-06 22:36:55'),
(16, 18, 'certificate', 'cse u1 conference proceedings.pdf', 'conference_certificate_6ac52c26c22ce.pdf', 'uploads/conference/conference_certificate_6ac52c26c22ce.pdf', 'application/pdf', 85570, '2026-10-06 22:43:10'),
(17, 18, 'paper_file', 'cse u1 conference paper.pdf', 'conference_paper_file_6ac52c26c2d4c.pdf', 'uploads/conference/conference_paper_file_6ac52c26c2d4c.pdf', 'application/pdf', 10433, '2026-10-06 22:43:10'),
(18, 18, 'merged_pdf', 'Merged_Document.pdf', 'conference_merged_6ac52c26c9ad5.pdf', 'uploads/conference/conference_merged_6ac52c26c9ad5.pdf', 'application/pdf', 97050, '2026-10-06 22:43:10'),
(19, 19, 'patent_file', 'cse u1 patent.pdf', 'patent_patent_file_6ac52c8fb7b2a.pdf', 'uploads/patent/patent_patent_file_6ac52c8fb7b2a.pdf', 'application/pdf', 6379, '2026-10-06 22:44:55'),
(20, 20, 'paper_file', 'cse u1 journal paper.pdf', 'journal_paper_file_6ac52d1a4390d.pdf', 'uploads/journal/journal_paper_file_6ac52d1a4390d.pdf', 'application/pdf', 9637, '2026-10-06 22:47:14'),
(21, 21, 'certificate', 'user1-fdps1.pdf', 'fdp_organised_certificate_6ac52e894aa73.pdf', 'uploads/fdp_organised/fdp_organised_certificate_6ac52e894aa73.pdf', 'application/pdf', 5424, '2026-10-06 22:53:21'),
(22, 21, 'brochure', 'user1-fdps1 broucher.pdf', 'fdp_organised_brochure_6ac52e894ba7b.pdf', 'uploads/fdp_organised/fdp_organised_brochure_6ac52e894ba7b.pdf', 'application/pdf', 9318, '2026-10-06 22:53:21'),
(23, 21, 'fdp_schedule_invitation', 'cse u1 fdp schedule.pdf', 'fdp_organised_fdp_schedule_invitation_6ac52e894c760.pdf', 'uploads/fdp_organised/fdp_organised_fdp_schedule_invitation_6ac52e894c760.pdf', 'application/pdf', 8687, '2026-10-06 22:53:21'),
(24, 21, 'attendance_forms', 'cse u1 attendance forms.pdf', 'fdp_organised_attendance_forms_6ac52e894d415.pdf', 'uploads/fdp_organised/fdp_organised_attendance_forms_6ac52e894d415.pdf', 'application/pdf', 10834, '2026-10-06 22:53:21'),
(25, 21, 'feedback_forms', 'cse u1 feed back forms.pdf', 'fdp_organised_feedback_forms_6ac52e8950276.pdf', 'uploads/fdp_organised/fdp_organised_feedback_forms_6ac52e8950276.pdf', 'application/pdf', 10406, '2026-10-06 22:53:21'),
(26, 21, 'fdp_report', 'user1-fdps1.pdf', 'fdp_organised_fdp_report_6ac52e8951236.pdf', 'uploads/fdp_organised/fdp_organised_fdp_report_6ac52e8951236.pdf', 'application/pdf', 5424, '2026-10-06 22:53:21'),
(27, 21, 'photo1', 'CSE user1.jpg', 'fdp_organised_photo1_6ac52e89524a7.jpg', 'uploads/fdp_organised/fdp_organised_photo1_6ac52e89524a7.jpg', 'image/jpeg', 22914, '2026-10-06 22:53:21'),
(28, 21, 'merged_pdf', 'Merged_Document.pdf', 'fdp_organised_merged_6ac52e8957c35.pdf', 'uploads/fdp_organised/fdp_organised_merged_6ac52e8957c35.pdf', 'application/pdf', 51087, '2026-10-06 22:53:21'),
(29, 22, 'brochure', 'cse u1 conference brochure.pdf', 'conf_organised_brochure_6ac52f1f4be2f.pdf', 'uploads/conf_organised/conf_organised_brochure_6ac52f1f4be2f.pdf', 'application/pdf', 11836, '2026-10-06 22:55:51'),
(30, 22, 'fdp_schedule_invitation', 'cse u1  schedule.pdf', 'conf_organised_fdp_schedule_invitation_6ac52f1f4ccdc.pdf', 'uploads/conf_organised/conf_organised_fdp_schedule_invitation_6ac52f1f4ccdc.pdf', 'application/pdf', 8687, '2026-10-06 22:55:51'),
(31, 22, 'attendance_forms', 'cse u1 attendance forms.pdf', 'conf_organised_attendance_forms_6ac52f1f4d78a.pdf', 'uploads/conf_organised/conf_organised_attendance_forms_6ac52f1f4d78a.pdf', 'application/pdf', 10834, '2026-10-06 22:55:51'),
(32, 22, 'feedback_forms', 'cse u1 feed back forms.pdf', 'conf_organised_feedback_forms_6ac52f1f4e4e9.pdf', 'uploads/conf_organised/conf_organised_feedback_forms_6ac52f1f4e4e9.pdf', 'application/pdf', 10406, '2026-10-06 22:55:51'),
(33, 22, 'fdp_report', 'cse u1 conference proceedings.pdf', 'conf_organised_fdp_report_6ac52f1f500d5.pdf', 'uploads/conf_organised/conf_organised_fdp_report_6ac52f1f500d5.pdf', 'application/pdf', 85570, '2026-10-06 22:55:51'),
(34, 22, 'photo1', 'CSE user1.jpg', 'conf_organised_photo1_6ac52f1f512e2.jpg', 'uploads/conf_organised/conf_organised_photo1_6ac52f1f512e2.jpg', 'image/jpeg', 22914, '2026-10-06 22:55:51'),
(35, 22, 'merged_pdf', 'Merged_Document.pdf', 'conf_organised_merged_6ac52f1f57a7a.pdf', 'uploads/conf_organised/conf_organised_merged_6ac52f1f57a7a.pdf', 'application/pdf', 128346, '2026-10-06 22:55:51'),
(41, 24, 'paper_file', 'cse u1 journal paper.pdf', 'journal_paper_file_6ac5cee87b8e5.pdf', 'uploads/journal/journal_paper_file_6ac5cee87b8e5.pdf', 'application/pdf', 9637, '2026-10-07 10:17:36'),
(42, 25, 'certificate', 'cse u2 fdp.pdf', 'fdp_attended_certificate_6ac5cf5a4f5fa.pdf', 'uploads/fdp_attended/fdp_attended_certificate_6ac5cf5a4f5fa.pdf', 'application/pdf', 5425, '2026-10-07 10:19:30'),
(43, 25, 'brochure', 'cse u2 fdp Brochure.pdf', 'fdp_attended_brochure_6ac5cf5a5085f.pdf', 'uploads/fdp_attended/fdp_attended_brochure_6ac5cf5a5085f.pdf', 'application/pdf', 9368, '2026-10-07 10:19:30'),
(44, 25, 'merged_pdf', 'Merged_Document.pdf', 'fdp_attended_merged_6ac5cf5a57c67.pdf', 'uploads/fdp_attended/fdp_attended_merged_6ac5cf5a57c67.pdf', 'application/pdf', 15845, '2026-10-07 10:19:30'),
(45, 26, 'patent_file', 'cse u2 patent.pdf', 'patent_patent_file_6ac5cfb6bab26.pdf', 'uploads/patent/patent_patent_file_6ac5cfb6bab26.pdf', 'application/pdf', 6520, '2026-10-07 10:21:02'),
(46, 27, 'certificate', 'adis u1 fdp.pdf', 'fdp_organised_certificate_6ac5d47cbe57a.pdf', 'uploads/fdp_organised/fdp_organised_certificate_6ac5d47cbe57a.pdf', 'application/pdf', 4824, '2026-10-07 10:41:24'),
(47, 27, 'brochure', 'aids u1 fdp brochure.pdf', 'fdp_organised_brochure_6ac5d47cc1582.pdf', 'uploads/fdp_organised/fdp_organised_brochure_6ac5d47cc1582.pdf', 'application/pdf', 8740, '2026-10-07 10:41:24'),
(48, 27, 'fdp_schedule_invitation', 'schedule.pdf', 'fdp_organised_fdp_schedule_invitation_6ac5d47cc27e1.pdf', 'uploads/fdp_organised/fdp_organised_fdp_schedule_invitation_6ac5d47cc27e1.pdf', 'application/pdf', 7382, '2026-10-07 10:41:24'),
(49, 27, 'attendance_forms', 'attendance forms.pdf', 'fdp_organised_attendance_forms_6ac5d47cc392e.pdf', 'uploads/fdp_organised/fdp_organised_attendance_forms_6ac5d47cc392e.pdf', 'application/pdf', 10766, '2026-10-07 10:41:24'),
(50, 27, 'feedback_forms', 'feedback forms.pdf', 'fdp_organised_feedback_forms_6ac5d47cc4f2e.pdf', 'uploads/fdp_organised/fdp_organised_feedback_forms_6ac5d47cc4f2e.pdf', 'application/pdf', 10299, '2026-10-07 10:41:24'),
(51, 27, 'fdp_report', 'adis u1 fdp.pdf', 'fdp_organised_fdp_report_6ac5d47cc61a1.pdf', 'uploads/fdp_organised/fdp_organised_fdp_report_6ac5d47cc61a1.pdf', 'application/pdf', 4824, '2026-10-07 10:41:24'),
(52, 27, 'photo1', 'CSE user2.jpg', 'fdp_organised_photo1_6ac5d47ccaca0.jpg', 'uploads/fdp_organised/fdp_organised_photo1_6ac5d47ccaca0.jpg', 'image/jpeg', 18180, '2026-10-07 10:41:24'),
(53, 27, 'merged_pdf', 'Merged_Document.pdf', 'fdp_organised_merged_6ac5d47cd1a5f.pdf', 'uploads/fdp_organised/fdp_organised_merged_6ac5d47cd1a5f.pdf', 'application/pdf', 47829, '2026-10-07 10:41:24'),
(54, 28, 'circular', 'brochure.pdf', 'student_activity_file_circular_6ac7bac0ddb2e.pdf', 'uploads/student_activity_file/student_activity_file_circular_6ac7bac0ddb2e.pdf', 'application/pdf', 8421, '2026-10-08 21:16:08'),
(55, 28, 'attendance', 'attendance statement.pdf', 'student_activity_file_attendance_6ac7bac0e1b07.pdf', 'uploads/student_activity_file/student_activity_file_attendance_6ac7bac0e1b07.pdf', 'application/pdf', 13488, '2026-10-08 21:16:08'),
(56, 28, 'report', 'event report.pdf', 'student_activity_file_report_6ac7bac0e2d1f.pdf', 'uploads/student_activity_file/student_activity_file_report_6ac7bac0e2d1f.pdf', 'application/pdf', 9616, '2026-10-08 21:16:08'),
(57, 28, 'photos', 'photos.pdf', 'student_activity_file_photos_6ac7bac0e3ad8.pdf', 'uploads/student_activity_file/student_activity_file_photos_6ac7bac0e3ad8.pdf', 'application/pdf', 7428, '2026-10-08 21:16:08'),
(58, 28, 'certificate', 'certificate.pdf', 'student_activity_file_certificate_6ac7bac0e4de7.pdf', 'uploads/student_activity_file/student_activity_file_certificate_6ac7bac0e4de7.pdf', 'application/pdf', 9243, '2026-10-08 21:16:08'),
(59, 28, 'resource_person_profile', 'resource person profile.pdf', 'student_activity_file_resource_person_profile_6ac7bac0e61c3.pdf', 'uploads/student_activity_file/student_activity_file_resource_person_profile_6ac7bac0e61c3.pdf', 'application/pdf', 13903, '2026-10-08 21:16:08'),
(60, 28, 'merged_pdf', 'Merged_Document.pdf', 'student_activity_file_merged_6ac7bac103554.pdf', 'uploads/student_activity_file/student_activity_file_merged_6ac7bac103554.pdf', 'application/pdf', 63147, '2026-10-08 21:16:09'),
(61, 29, 'circular', 'brochure.pdf', 'student_activity_file_circular_6ac7bb94b4a44.pdf', 'uploads/student_activity_file/student_activity_file_circular_6ac7bb94b4a44.pdf', 'application/pdf', 8421, '2026-10-08 21:19:40'),
(62, 29, 'attendance', 'attendance statement.pdf', 'student_activity_file_attendance_6ac7bb94b6ccf.pdf', 'uploads/student_activity_file/student_activity_file_attendance_6ac7bb94b6ccf.pdf', 'application/pdf', 13488, '2026-10-08 21:19:40'),
(63, 29, 'report', 'event report.pdf', 'student_activity_file_report_6ac7bb94b7e04.pdf', 'uploads/student_activity_file/student_activity_file_report_6ac7bb94b7e04.pdf', 'application/pdf', 9616, '2026-10-08 21:19:40'),
(64, 29, 'photos', 'photos.pdf', 'student_activity_file_photos_6ac7bb94b8f36.pdf', 'uploads/student_activity_file/student_activity_file_photos_6ac7bb94b8f36.pdf', 'application/pdf', 7428, '2026-10-08 21:19:40'),
(65, 29, 'certificate', 'certificate.pdf', 'student_activity_file_certificate_6ac7bb94b9f9b.pdf', 'uploads/student_activity_file/student_activity_file_certificate_6ac7bb94b9f9b.pdf', 'application/pdf', 9243, '2026-10-08 21:19:40'),
(66, 29, 'resource_person_profile', 'resource person profile.pdf', 'student_activity_file_resource_person_profile_6ac7bb94bb040.pdf', 'uploads/student_activity_file/student_activity_file_resource_person_profile_6ac7bb94bb040.pdf', 'application/pdf', 13903, '2026-10-08 21:19:40'),
(67, 29, 'merged_pdf', 'Merged_Document.pdf', 'student_activity_file_merged_6ac7bb94c717a.pdf', 'uploads/student_activity_file/student_activity_file_merged_6ac7bb94c717a.pdf', 'application/pdf', 63147, '2026-10-08 21:19:40'),
(68, 30, 'circular', 'brochure.pdf', 'student_activity_file_circular_6ac7bc2b4e6b7.pdf', 'uploads/student_activity_file/student_activity_file_circular_6ac7bc2b4e6b7.pdf', 'application/pdf', 8421, '2026-10-08 21:22:11'),
(69, 30, 'schedule', 'schedule.pdf', 'student_activity_file_schedule_6ac7bc2b4f63d.pdf', 'uploads/student_activity_file/student_activity_file_schedule_6ac7bc2b4f63d.pdf', 'application/pdf', 8359, '2026-10-08 21:22:11'),
(70, 30, 'attendance', 'attendance statement.pdf', 'student_activity_file_attendance_6ac7bc2b5088a.pdf', 'uploads/student_activity_file/student_activity_file_attendance_6ac7bc2b5088a.pdf', 'application/pdf', 13488, '2026-10-08 21:22:11'),
(71, 30, 'report', 'event report.pdf', 'student_activity_file_report_6ac7bc2b523e6.pdf', 'uploads/student_activity_file/student_activity_file_report_6ac7bc2b523e6.pdf', 'application/pdf', 9616, '2026-10-08 21:22:11'),
(72, 30, 'photos', 'photos.pdf', 'student_activity_file_photos_6ac7bc2b53530.pdf', 'uploads/student_activity_file/student_activity_file_photos_6ac7bc2b53530.pdf', 'application/pdf', 7428, '2026-10-08 21:22:11'),
(73, 30, 'certificate', 'certificate.pdf', 'student_activity_file_certificate_6ac7bc2b5484c.pdf', 'uploads/student_activity_file/student_activity_file_certificate_6ac7bc2b5484c.pdf', 'application/pdf', 9243, '2026-10-08 21:22:11'),
(74, 30, 'merged_pdf', 'Merged_Document.pdf', 'student_activity_file_merged_6ac7bc2b5a4db.pdf', 'uploads/student_activity_file/student_activity_file_merged_6ac7bc2b5a4db.pdf', 'application/pdf', 57589, '2026-10-08 21:22:11'),
(75, 31, 'circular', 'brochure.pdf', 'student_activity_file_circular_6ac7bd7b82481.pdf', 'uploads/student_activity_file/student_activity_file_circular_6ac7bd7b82481.pdf', 'application/pdf', 8421, '2026-10-08 21:27:47'),
(76, 31, 'schedule', 'schedule.pdf', 'student_activity_file_schedule_6ac7bd7b859d5.pdf', 'uploads/student_activity_file/student_activity_file_schedule_6ac7bd7b859d5.pdf', 'application/pdf', 8359, '2026-10-08 21:27:47'),
(77, 31, 'attendance', 'attendance statement.pdf', 'student_activity_file_attendance_6ac7bd7b86d6d.pdf', 'uploads/student_activity_file/student_activity_file_attendance_6ac7bd7b86d6d.pdf', 'application/pdf', 13488, '2026-10-08 21:27:47'),
(78, 31, 'report', 'event report.pdf', 'student_activity_file_report_6ac7bd7b88314.pdf', 'uploads/student_activity_file/student_activity_file_report_6ac7bd7b88314.pdf', 'application/pdf', 9616, '2026-10-08 21:27:47'),
(79, 31, 'photos', 'photos.pdf', 'student_activity_file_photos_6ac7bd7b898e0.pdf', 'uploads/student_activity_file/student_activity_file_photos_6ac7bd7b898e0.pdf', 'application/pdf', 7428, '2026-10-08 21:27:47'),
(80, 31, 'certificate', 'certificate.pdf', 'student_activity_file_certificate_6ac7bd7b8a9e0.pdf', 'uploads/student_activity_file/student_activity_file_certificate_6ac7bd7b8a9e0.pdf', 'application/pdf', 9243, '2026-10-08 21:27:47'),
(81, 31, 'merged_pdf', 'Merged_Document.pdf', 'student_activity_file_merged_6ac7bd7b91bd0.pdf', 'uploads/student_activity_file/student_activity_file_merged_6ac7bd7b91bd0.pdf', 'application/pdf', 57589, '2026-10-08 21:27:47'),
(82, 32, 'circular', 'brochure.pdf', 'student_activity_file_circular_6ac7be30b40fb.pdf', 'uploads/student_activity_file/student_activity_file_circular_6ac7be30b40fb.pdf', 'application/pdf', 8421, '2026-10-08 21:30:48'),
(83, 32, 'schedule', 'schedule.pdf', 'student_activity_file_schedule_6ac7be30b5151.pdf', 'uploads/student_activity_file/student_activity_file_schedule_6ac7be30b5151.pdf', 'application/pdf', 8359, '2026-10-08 21:30:48'),
(84, 32, 'attendance', 'attendance statement.pdf', 'student_activity_file_attendance_6ac7be30b657a.pdf', 'uploads/student_activity_file/student_activity_file_attendance_6ac7be30b657a.pdf', 'application/pdf', 13488, '2026-10-08 21:30:48'),
(85, 32, 'report', 'event report.pdf', 'student_activity_file_report_6ac7be30b749f.pdf', 'uploads/student_activity_file/student_activity_file_report_6ac7be30b749f.pdf', 'application/pdf', 9616, '2026-10-08 21:30:48'),
(86, 32, 'photos', 'photos.pdf', 'student_activity_file_photos_6ac7be30ba8ed.pdf', 'uploads/student_activity_file/student_activity_file_photos_6ac7be30ba8ed.pdf', 'application/pdf', 7428, '2026-10-08 21:30:48'),
(87, 32, 'certificate', 'certificate.pdf', 'student_activity_file_certificate_6ac7be30bb997.pdf', 'uploads/student_activity_file/student_activity_file_certificate_6ac7be30bb997.pdf', 'application/pdf', 9243, '2026-10-08 21:30:48'),
(88, 32, 'merged_pdf', 'Merged_Document.pdf', 'student_activity_file_merged_6ac7be30bfa41.pdf', 'uploads/student_activity_file/student_activity_file_merged_6ac7be30bfa41.pdf', 'application/pdf', 57589, '2026-10-08 21:30:48'),
(89, 33, 'circular', 'brochure.pdf', 'student_activity_file_circular_6acd0144bbd5e.pdf', 'uploads/student_activity_file/student_activity_file_circular_6acd0144bbd5e.pdf', 'application/pdf', 291122, '2026-10-12 21:18:20'),
(90, 33, 'attendance', 'Attendance Statement.pdf', 'student_activity_file_attendance_6acd0144bd787.pdf', 'uploads/student_activity_file/student_activity_file_attendance_6acd0144bd787.pdf', 'application/pdf', 1441530, '2026-10-12 21:18:20'),
(91, 33, 'report', 'report.pdf', 'student_activity_file_report_6acd0144bf52c.pdf', 'uploads/student_activity_file/student_activity_file_report_6acd0144bf52c.pdf', 'application/pdf', 251794, '2026-10-12 21:18:20'),
(92, 33, 'photos', 'photos.pdf', 'student_activity_file_photos_6acd0144c0c59.pdf', 'uploads/student_activity_file/student_activity_file_photos_6acd0144c0c59.pdf', 'application/pdf', 516400, '2026-10-12 21:18:20'),
(93, 33, 'certificate', 'certificate.pdf', 'student_activity_file_certificate_6acd0144c2e7c.pdf', 'uploads/student_activity_file/student_activity_file_certificate_6acd0144c2e7c.pdf', 'application/pdf', 549443, '2026-10-12 21:18:20'),
(94, 33, 'merged_pdf', 'Merged_Document.pdf', 'student_activity_file_merged_6acd0144d3d17.pdf', 'uploads/student_activity_file/student_activity_file_merged_6acd0144d3d17.pdf', 'application/pdf', 3048480, '2026-10-12 21:18:20'),
(95, 34, 'circular', 'brochure.pdf', 'student_activity_file_circular_6acd030e1f98c.pdf', 'uploads/student_activity_file/student_activity_file_circular_6acd030e1f98c.pdf', 'application/pdf', 438474, '2026-10-12 21:25:58'),
(96, 34, 'attendance', 'attendance statement.pdf', 'student_activity_file_attendance_6acd030e22a9f.pdf', 'uploads/student_activity_file/student_activity_file_attendance_6acd030e22a9f.pdf', 'application/pdf', 892974, '2026-10-12 21:25:58'),
(97, 34, 'report', 'report.pdf', 'student_activity_file_report_6acd030e24437.pdf', 'uploads/student_activity_file/student_activity_file_report_6acd030e24437.pdf', 'application/pdf', 251794, '2026-10-12 21:25:58'),
(98, 34, 'photos', 'photos.pdf', 'student_activity_file_photos_6acd030e25b85.pdf', 'uploads/student_activity_file/student_activity_file_photos_6acd030e25b85.pdf', 'application/pdf', 516400, '2026-10-12 21:25:58'),
(99, 34, 'certificate', 'certificate.pdf', 'student_activity_file_certificate_6acd030e2768d.pdf', 'uploads/student_activity_file/student_activity_file_certificate_6acd030e2768d.pdf', 'application/pdf', 549443, '2026-10-12 21:25:58'),
(100, 34, 'merged_pdf', 'Merged_Document.pdf', 'student_activity_file_merged_6acd030e315d4.pdf', 'uploads/student_activity_file/student_activity_file_merged_6acd030e315d4.pdf', 'application/pdf', 2644492, '2026-10-12 21:25:58'),
(101, 41, 'circular', 'circular.pdf', 'student_activity_file_circular_6ad1a01f80c6b.pdf', 'uploads/student_activity_file/student_activity_file_circular_6ad1a01f80c6b.pdf', 'application/pdf', 87011, '2026-10-16 09:25:11'),
(102, 41, 'attendance', 'attendance statement.pdf', 'student_activity_file_attendance_6ad1a01f854f9.pdf', 'uploads/student_activity_file/student_activity_file_attendance_6ad1a01f854f9.pdf', 'application/pdf', 951796, '2026-10-16 09:25:11'),
(103, 41, 'report', 'circular.pdf', 'student_activity_file_report_6ad1a01f86b61.pdf', 'uploads/student_activity_file/student_activity_file_report_6ad1a01f86b61.pdf', 'application/pdf', 87011, '2026-10-16 09:25:11'),
(104, 41, 'photos', 'circular.pdf', 'student_activity_file_photos_6ad1a01f880b9.pdf', 'uploads/student_activity_file/student_activity_file_photos_6ad1a01f880b9.pdf', 'application/pdf', 87011, '2026-10-16 09:25:11'),
(105, 41, 'merged_pdf', 'Merged_Document.pdf', 'student_activity_file_merged_6ad1a01f99fdb.pdf', 'uploads/student_activity_file/student_activity_file_merged_6ad1a01f99fdb.pdf', 'application/pdf', 1213700, '2026-10-16 09:25:11'),
(106, 42, 'circular', 'brochure.pdf', 'student_activity_file_circular_6ad1a1e8279ae.pdf', 'uploads/student_activity_file/student_activity_file_circular_6ad1a1e8279ae.pdf', 'application/pdf', 438474, '2026-10-16 09:32:48'),
(107, 42, 'attendance', 'attendance statement.pdf', 'student_activity_file_attendance_6ad1a1e82aeb7.pdf', 'uploads/student_activity_file/student_activity_file_attendance_6ad1a1e82aeb7.pdf', 'application/pdf', 892974, '2026-10-16 09:32:48'),
(108, 42, 'report', 'report.pdf', 'student_activity_file_report_6ad1a1e82cbe1.pdf', 'uploads/student_activity_file/student_activity_file_report_6ad1a1e82cbe1.pdf', 'application/pdf', 251794, '2026-10-16 09:32:48'),
(109, 42, 'photos', 'photos.pdf', 'student_activity_file_photos_6ad1a1e82e1d7.pdf', 'uploads/student_activity_file/student_activity_file_photos_6ad1a1e82e1d7.pdf', 'application/pdf', 516400, '2026-10-16 09:32:48'),
(110, 42, 'certificate', 'certificate.pdf', 'student_activity_file_certificate_6ad1a1e82f83a.pdf', 'uploads/student_activity_file/student_activity_file_certificate_6ad1a1e82f83a.pdf', 'application/pdf', 549443, '2026-10-16 09:32:48'),
(111, 42, 'merged_pdf', 'Merged_Document.pdf', 'student_activity_file_merged_6ad1a1e835686.pdf', 'uploads/student_activity_file/student_activity_file_merged_6ad1a1e835686.pdf', 'application/pdf', 2644492, '2026-10-16 09:32:48'),
(112, 43, 'circular', 'brochure.pdf', 'student_activity_file_circular_6ad1a317e3ee9.pdf', 'uploads/student_activity_file/student_activity_file_circular_6ad1a317e3ee9.pdf', 'application/pdf', 291122, '2026-10-16 09:37:51'),
(113, 43, 'attendance', 'Attendance Statement.pdf', 'student_activity_file_attendance_6ad1a317e5fca.pdf', 'uploads/student_activity_file/student_activity_file_attendance_6ad1a317e5fca.pdf', 'application/pdf', 1441530, '2026-10-16 09:37:51'),
(114, 43, 'report', 'report.pdf', 'student_activity_file_report_6ad1a317e80b3.pdf', 'uploads/student_activity_file/student_activity_file_report_6ad1a317e80b3.pdf', 'application/pdf', 251794, '2026-10-16 09:37:51'),
(115, 43, 'photos', 'photos.pdf', 'student_activity_file_photos_6ad1a317e96ab.pdf', 'uploads/student_activity_file/student_activity_file_photos_6ad1a317e96ab.pdf', 'application/pdf', 516400, '2026-10-16 09:37:51'),
(116, 43, 'certificate', 'certificate.pdf', 'student_activity_file_certificate_6ad1a317eace4.pdf', 'uploads/student_activity_file/student_activity_file_certificate_6ad1a317eace4.pdf', 'application/pdf', 549443, '2026-10-16 09:37:51'),
(117, 43, 'merged_pdf', 'Merged_Document.pdf', 'student_activity_file_merged_6ad1a317f26de.pdf', 'uploads/student_activity_file/student_activity_file_merged_6ad1a317f26de.pdf', 'application/pdf', 3048480, '2026-10-16 09:37:52'),
(118, 44, 'circular', 'circular.pdf', 'student_activity_file_circular_6ad1a60ec5d17.pdf', 'uploads/student_activity_file/student_activity_file_circular_6ad1a60ec5d17.pdf', 'application/pdf', 144970, '2026-10-16 09:50:30'),
(119, 44, 'attendance', 'attendance statement.pdf', 'student_activity_file_attendance_6ad1a60ec7837.pdf', 'uploads/student_activity_file/student_activity_file_attendance_6ad1a60ec7837.pdf', 'application/pdf', 780840, '2026-10-16 09:50:30'),
(120, 44, 'report', 'report.pdf', 'student_activity_file_report_6ad1a60ec8f3e.pdf', 'uploads/student_activity_file/student_activity_file_report_6ad1a60ec8f3e.pdf', 'application/pdf', 259881, '2026-10-16 09:50:30'),
(121, 44, 'photos', 'photos.pdf', 'student_activity_file_photos_6ad1a60eca0d5.pdf', 'uploads/student_activity_file/student_activity_file_photos_6ad1a60eca0d5.pdf', 'application/pdf', 364018, '2026-10-16 09:50:30'),
(122, 44, 'merged_pdf', 'Merged_Document.pdf', 'student_activity_file_merged_6ad1a60ed0179.pdf', 'uploads/student_activity_file/student_activity_file_merged_6ad1a60ed0179.pdf', 'application/pdf', 1550253, '2026-10-16 09:50:30'),
(123, 45, 'circular', 'circular.pdf', 'student_activity_file_circular_6ad1a70958d47.pdf', 'uploads/student_activity_file/student_activity_file_circular_6ad1a70958d47.pdf', 'application/pdf', 170386, '2026-10-16 09:54:41'),
(124, 45, 'attendance', 'attendance statement.pdf', 'student_activity_file_attendance_6ad1a7095a770.pdf', 'uploads/student_activity_file/student_activity_file_attendance_6ad1a7095a770.pdf', 'application/pdf', 440251, '2026-10-16 09:54:41'),
(125, 45, 'report', 'report.pdf', 'student_activity_file_report_6ad1a7095c32d.pdf', 'uploads/student_activity_file/student_activity_file_report_6ad1a7095c32d.pdf', 'application/pdf', 271695, '2026-10-16 09:54:41'),
(126, 45, 'photos', 'photos.pdf', 'student_activity_file_photos_6ad1a7095d404.pdf', 'uploads/student_activity_file/student_activity_file_photos_6ad1a7095d404.pdf', 'application/pdf', 313155, '2026-10-16 09:54:41'),
(127, 45, 'merged_pdf', 'Merged_Document.pdf', 'student_activity_file_merged_6ad1a70963110.pdf', 'uploads/student_activity_file/student_activity_file_merged_6ad1a70963110.pdf', 'application/pdf', 1195759, '2026-10-16 09:54:41'),
(128, 46, 'circular', 'circular.pdf', 'student_activity_file_circular_6ad1aa3d49549.pdf', 'uploads/student_activity_file/student_activity_file_circular_6ad1aa3d49549.pdf', 'application/pdf', 69002, '2026-10-16 10:08:21'),
(129, 46, 'attendance', 'attendance statement.pdf', 'student_activity_file_attendance_6ad1aa3d4d628.pdf', 'uploads/student_activity_file/student_activity_file_attendance_6ad1aa3d4d628.pdf', 'application/pdf', 1074428, '2026-10-16 10:08:21'),
(130, 46, 'report', 'report.pdf', 'student_activity_file_report_6ad1aa3d4ef66.pdf', 'uploads/student_activity_file/student_activity_file_report_6ad1aa3d4ef66.pdf', 'application/pdf', 170505, '2026-10-16 10:08:21'),
(131, 46, 'photos', 'circular.pdf', 'student_activity_file_photos_6ad1aa3d5053b.pdf', 'uploads/student_activity_file/student_activity_file_photos_6ad1aa3d5053b.pdf', 'application/pdf', 69002, '2026-10-16 10:08:21'),
(132, 46, 'merged_pdf', 'Merged_Document.pdf', 'student_activity_file_merged_6ad1aa3d579f5.pdf', 'uploads/student_activity_file/student_activity_file_merged_6ad1aa3d579f5.pdf', 'application/pdf', 1384728, '2026-10-16 10:08:21'),
(133, 47, 'circular', 'circular.pdf', 'student_activity_file_circular_6ad1aafc4fa33.pdf', 'uploads/student_activity_file/student_activity_file_circular_6ad1aafc4fa33.pdf', 'application/pdf', 59541, '2026-10-16 10:11:32'),
(134, 47, 'attendance', 'attendance statement.pdf', 'student_activity_file_attendance_6ad1aafc5145f.pdf', 'uploads/student_activity_file/student_activity_file_attendance_6ad1aafc5145f.pdf', 'application/pdf', 964206, '2026-10-16 10:11:32'),
(135, 47, 'report', 'report.pdf', 'student_activity_file_report_6ad1aafc52e06.pdf', 'uploads/student_activity_file/student_activity_file_report_6ad1aafc52e06.pdf', 'application/pdf', 85197, '2026-10-16 10:11:32'),
(136, 47, 'photos', 'photos.pdf', 'student_activity_file_photos_6ad1aafc541a1.pdf', 'uploads/student_activity_file/student_activity_file_photos_6ad1aafc541a1.pdf', 'application/pdf', 104356, '2026-10-16 10:11:32'),
(137, 47, 'merged_pdf', 'Merged_Document.pdf', 'student_activity_file_merged_6ad1aafc59823.pdf', 'uploads/student_activity_file/student_activity_file_merged_6ad1aafc59823.pdf', 'application/pdf', 1214170, '2026-10-16 10:11:32'),
(138, 48, 'circular', 'circular.pdf', 'student_activity_file_circular_6ad1abb3dcbcc.pdf', 'uploads/student_activity_file/student_activity_file_circular_6ad1abb3dcbcc.pdf', 'application/pdf', 55777, '2026-10-16 10:14:35'),
(139, 48, 'attendance', 'attendance statement.pdf', 'student_activity_file_attendance_6ad1abb3df4fc.pdf', 'uploads/student_activity_file/student_activity_file_attendance_6ad1abb3df4fc.pdf', 'application/pdf', 944788, '2026-10-16 10:14:35'),
(140, 48, 'report', 'report.pdf', 'student_activity_file_report_6ad1abb3e0eb0.pdf', 'uploads/student_activity_file/student_activity_file_report_6ad1abb3e0eb0.pdf', 'application/pdf', 184817, '2026-10-16 10:14:35'),
(141, 48, 'photos', 'photos.pdf', 'student_activity_file_photos_6ad1abb3e229b.pdf', 'uploads/student_activity_file/student_activity_file_photos_6ad1abb3e229b.pdf', 'application/pdf', 98100, '2026-10-16 10:14:35'),
(142, 48, 'merged_pdf', 'Merged_Document.pdf', 'student_activity_file_merged_6ad1abb3ea90a.pdf', 'uploads/student_activity_file/student_activity_file_merged_6ad1abb3ea90a.pdf', 'application/pdf', 1284352, '2026-10-16 10:14:36'),
(146, 50, 'circular', 'circular.pdf', 'student_activity_file_circular_6ad1ba6e12376.pdf', 'uploads/student_activity_file/student_activity_file_circular_6ad1ba6e12376.pdf', 'application/pdf', 174924, '2026-10-16 11:17:26'),
(147, 50, 'attendance', 'attendance statement.pdf', 'student_activity_file_attendance_6ad1ba6e17767.pdf', 'uploads/student_activity_file/student_activity_file_attendance_6ad1ba6e17767.pdf', 'application/pdf', 1313373, '2026-10-16 11:17:26'),
(148, 50, 'report', 'report.pdf', 'student_activity_file_report_6ad1ba6e1963a.pdf', 'uploads/student_activity_file/student_activity_file_report_6ad1ba6e1963a.pdf', 'application/pdf', 874578, '2026-10-16 11:17:26'),
(149, 50, 'photos', 'report.pdf', 'student_activity_file_photos_6ad1ba6e1b5e9.pdf', 'uploads/student_activity_file/student_activity_file_photos_6ad1ba6e1b5e9.pdf', 'application/pdf', 874578, '2026-10-16 11:17:26'),
(150, 50, 'merged_pdf', 'Merged_Document.pdf', 'student_activity_file_merged_6ad1ba6e2396b.pdf', 'uploads/student_activity_file/student_activity_file_merged_6ad1ba6e2396b.pdf', 'application/pdf', 3235648, '2026-10-16 11:17:26'),
(152, 52, 'circular', 'circular.pdf', 'student_activity_file_circular_6ad1bb15409d4.pdf', 'uploads/student_activity_file/student_activity_file_circular_6ad1bb15409d4.pdf', 'application/pdf', 188513, '2026-10-16 11:20:13'),
(153, 52, 'attendance', 'circular.pdf', 'student_activity_file_attendance_6ad1bb1542871.pdf', 'uploads/student_activity_file/student_activity_file_attendance_6ad1bb1542871.pdf', 'application/pdf', 188513, '2026-10-16 11:20:13'),
(154, 52, 'report', 'report.pdf', 'student_activity_file_report_6ad1bb1545064.pdf', 'uploads/student_activity_file/student_activity_file_report_6ad1bb1545064.pdf', 'application/pdf', 790439, '2026-10-16 11:20:13'),
(155, 52, 'photos', 'photos.pdf', 'student_activity_file_photos_6ad1bb15470c9.pdf', 'uploads/student_activity_file/student_activity_file_photos_6ad1bb15470c9.pdf', 'application/pdf', 790439, '2026-10-16 11:20:13'),
(156, 52, 'merged_pdf', 'Merged_Document.pdf', 'student_activity_file_merged_6ad1bb154e296.pdf', 'uploads/student_activity_file/student_activity_file_merged_6ad1bb154e296.pdf', 'application/pdf', 1956207, '2026-10-16 11:20:13'),
(157, 53, 'circular', 'brochure.pdf', 'student_activity_file_circular_6ad1bbb529991.pdf', 'uploads/student_activity_file/student_activity_file_circular_6ad1bbb529991.pdf', 'application/pdf', 1417598, '2026-10-16 11:22:53'),
(158, 53, 'attendance', 'attendance statement.pdf', 'student_activity_file_attendance_6ad1bbb52b7dc.pdf', 'uploads/student_activity_file/student_activity_file_attendance_6ad1bbb52b7dc.pdf', 'application/pdf', 793419, '2026-10-16 11:22:53'),
(159, 53, 'report', 'report.pdf', 'student_activity_file_report_6ad1bbb52d9e7.pdf', 'uploads/student_activity_file/student_activity_file_report_6ad1bbb52d9e7.pdf', 'application/pdf', 205651, '2026-10-16 11:22:53'),
(160, 53, 'merged_pdf', 'Merged_Document.pdf', 'student_activity_file_merged_6ad1bbb5345e6.pdf', 'uploads/student_activity_file/student_activity_file_merged_6ad1bbb5345e6.pdf', 'application/pdf', 2419537, '2026-10-16 11:22:53'),
(161, 54, 'circular', 'circular.pdf', 'student_activity_file_circular_6ad1bc46125c3.pdf', 'uploads/student_activity_file/student_activity_file_circular_6ad1bc46125c3.pdf', 'application/pdf', 501235, '2026-10-16 11:25:18'),
(162, 54, 'attendance', 'attendance statment.pdf', 'student_activity_file_attendance_6ad1bc4614955.pdf', 'uploads/student_activity_file/student_activity_file_attendance_6ad1bc4614955.pdf', 'application/pdf', 1887195, '2026-10-16 11:25:18'),
(163, 54, 'report', 'report.pdf', 'student_activity_file_report_6ad1bc46163c2.pdf', 'uploads/student_activity_file/student_activity_file_report_6ad1bc46163c2.pdf', 'application/pdf', 299235, '2026-10-16 11:25:18'),
(164, 54, 'photos', 'report.pdf', 'student_activity_file_photos_6ad1bc4617d11.pdf', 'uploads/student_activity_file/student_activity_file_photos_6ad1bc4617d11.pdf', 'application/pdf', 299235, '2026-10-16 11:25:18'),
(165, 54, 'merged_pdf', 'Merged_Document.pdf', 'student_activity_file_merged_6ad1bc461d219.pdf', 'uploads/student_activity_file/student_activity_file_merged_6ad1bc461d219.pdf', 'application/pdf', 2988097, '2026-10-16 11:25:18'),
(166, 55, 'circular', 'circular.pdf', 'student_activity_file_circular_6ad1bcea4fa74.pdf', 'uploads/student_activity_file/student_activity_file_circular_6ad1bcea4fa74.pdf', 'application/pdf', 473220, '2026-10-16 11:28:02'),
(167, 55, 'schedule', 'schedule.pdf', 'student_activity_file_schedule_6ad1bcea53aa7.pdf', 'uploads/student_activity_file/student_activity_file_schedule_6ad1bcea53aa7.pdf', 'application/pdf', 636240, '2026-10-16 11:28:02'),
(168, 55, 'attendance', 'attendance statement.pdf', 'student_activity_file_attendance_6ad1bcea572f2.pdf', 'uploads/student_activity_file/student_activity_file_attendance_6ad1bcea572f2.pdf', 'application/pdf', 1067923, '2026-10-16 11:28:02'),
(169, 55, 'report', 'report.pdf', 'student_activity_file_report_6ad1bcea5a295.pdf', 'uploads/student_activity_file/student_activity_file_report_6ad1bcea5a295.pdf', 'application/pdf', 341870, '2026-10-16 11:28:02'),
(170, 55, 'merged_pdf', 'Merged_Document.pdf', 'student_activity_file_merged_6ad1bcea6016e.pdf', 'uploads/student_activity_file/student_activity_file_merged_6ad1bcea6016e.pdf', 'application/pdf', 2520350, '2026-10-16 11:28:02'),
(171, 56, 'circular', 'circular.pdf', 'student_activity_file_circular_6ad1bd58caf03.pdf', 'uploads/student_activity_file/student_activity_file_circular_6ad1bd58caf03.pdf', 'application/pdf', 845127, '2026-10-16 11:29:52'),
(172, 56, 'schedule', 'schedule.pdf', 'student_activity_file_schedule_6ad1bd58cd2c5.pdf', 'uploads/student_activity_file/student_activity_file_schedule_6ad1bd58cd2c5.pdf', 'application/pdf', 1471813, '2026-10-16 11:29:52'),
(173, 56, 'attendance', 'attendance statement.pdf', 'student_activity_file_attendance_6ad1bd58cfb3f.pdf', 'uploads/student_activity_file/student_activity_file_attendance_6ad1bd58cfb3f.pdf', 'application/pdf', 1515590, '2026-10-16 11:29:52'),
(174, 56, 'report', 'report.pdf', 'student_activity_file_report_6ad1bd58d1b30.pdf', 'uploads/student_activity_file/student_activity_file_report_6ad1bd58d1b30.pdf', 'application/pdf', 330657, '2026-10-16 11:29:52'),
(175, 56, 'photos', 'photos.pdf', 'student_activity_file_photos_6ad1bd58d3a88.pdf', 'uploads/student_activity_file/student_activity_file_photos_6ad1bd58d3a88.pdf', 'application/pdf', 856781, '2026-10-16 11:29:52'),
(176, 56, 'merged_pdf', 'Merged_Document.pdf', 'student_activity_file_merged_6ad1bd58de634.pdf', 'uploads/student_activity_file/student_activity_file_merged_6ad1bd58de634.pdf', 'application/pdf', 5021761, '2026-10-16 11:29:53');

-- --------------------------------------------------------

--
-- Table structure for table `document_metadata`
--

CREATE TABLE `document_metadata` (
  `meta_id` int(11) NOT NULL,
  `doc_id` int(11) NOT NULL,
  `meta_key` varchar(100) NOT NULL,
  `meta_value` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `document_meta_student_activity`
--

CREATE TABLE `document_meta_student_activity` (
  `meta_id` int(11) NOT NULL,
  `doc_id` int(11) NOT NULL,
  `activity_category` varchar(100) NOT NULL,
  `participation_type` enum('Individual','Team','Batch') NOT NULL DEFAULT 'Individual',
  `event_details` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`event_details`)),
  `participants` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`participants`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `document_types`
--

CREATE TABLE `document_types` (
  `type_id` int(11) NOT NULL,
  `type_code` varchar(50) NOT NULL,
  `label` varchar(150) NOT NULL,
  `workflow_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `document_types`
--

INSERT INTO `document_types` (`type_id`, `type_code`, `label`, `workflow_id`) VALUES
(1, 'fdp', 'Faculty Development Program', 1),
(2, 'patent', 'Patent Publication', 1),
(3, 'journal', 'Journal Paper', 1),
(4, 'conference', 'Conference Proceedings Published Form', 1),
(6, 'fdp_attended', 'FDP Attended', 1),
(7, 'fdp_organised', 'FDP Organised', 1),
(8, 'conf_organised', 'Conference Organised', 1),
(9, 'criteria_file', 'Criteria File', 1),
(10, 'dept_file', 'Department File', 5),
(11, 'central_file', 'Central File', 1),
(12, 'scholarship', 'Scholarship', 1),
(13, 'placement', 'Placement', 1),
(14, 'higher_ed', 'Higher Education', 1),
(15, 'exam_qual', 'Exam Qualification', 1),
(16, 'award', 'Award', 1),
(17, 'student_event', 'Student Event', 1),
(18, 'student_body', 'Student Body', 1),
(19, 'student_journal', 'Student Journal', 1),
(20, 'student_conference', 'Student Conference', 1),
(21, 'stu_act', 'Student Activity', 4),
(29, 'student_activity_file', 'Student Activity File', 5);

-- --------------------------------------------------------

--
-- Table structure for table `fdps_org_tab`
--

CREATE TABLE `fdps_org_tab` (
  `id` int(11) NOT NULL,
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
  `rejection_reason` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fdps_tab`
--

CREATE TABLE `fdps_tab` (
  `id` int(255) NOT NULL,
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
  `rejection_reason` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `fdps_tab`
--

INSERT INTO `fdps_tab` (`id`, `username`, `branch`, `title`, `mode`, `date_from`, `date_to`, `organised_by`, `location`, `certificate`, `brochure`, `fdp_schedule`, `submission_time`, `year`, `status`, `rejection_reason`) VALUES
(1, 'cseuser', 'CSE', 'Test FDP', 'Offline', '2026-09-01', '2026-09-15', 'GMRIT', 'GMRIT Campus', 'uploads/certificates/Mars Rover _Problem Statement_AndinoHack.pdf', NULL, NULL, '2026-09-15 01:17:12', '2024-25', 'Pending HOD', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `files`
--

CREATE TABLE `files` (
  `id` int(255) NOT NULL,
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
  `rejection_reason` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `files5_1_1and2`
--

CREATE TABLE `files5_1_1and2` (
  `id` int(11) NOT NULL,
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
  `rejection_reason` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `files5_1_3`
--

CREATE TABLE `files5_1_3` (
  `id` int(11) NOT NULL,
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
  `rejection_reason` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `files5_1_4`
--

CREATE TABLE `files5_1_4` (
  `id` int(11) NOT NULL,
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
  `rejection_reason` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `files5_2_1`
--

CREATE TABLE `files5_2_1` (
  `id` int(11) NOT NULL,
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
  `rejection_reason` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `files5_2_2`
--

CREATE TABLE `files5_2_2` (
  `id` int(11) NOT NULL,
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
  `rejection_reason` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `files5_2_3`
--

CREATE TABLE `files5_2_3` (
  `id` int(11) NOT NULL,
  `username` varchar(100) DEFAULT NULL,
  `faculty_name` varchar(100) DEFAULT NULL,
  `academic_year` varchar(20) DEFAULT NULL,
  `reg_no` varchar(50) DEFAULT NULL,
  `exam` varchar(255) DEFAULT NULL,
  `exam_status` varchar(100) DEFAULT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `file_name` varchar(255) DEFAULT NULL,
  `file_path` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `files5_3_1`
--

CREATE TABLE `files5_3_1` (
  `id` int(11) NOT NULL,
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
  `file_path` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `files5_3_3`
--

CREATE TABLE `files5_3_3` (
  `id` int(11) NOT NULL,
  `username` varchar(100) DEFAULT NULL,
  `faculty_name` varchar(100) DEFAULT NULL,
  `academic_year` varchar(20) DEFAULT NULL,
  `event_name` varchar(255) DEFAULT NULL,
  `event_date` date DEFAULT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `file_name` varchar(255) DEFAULT NULL,
  `file_path` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `meta_award`
--

CREATE TABLE `meta_award` (
  `meta_id` int(11) NOT NULL,
  `doc_id` int(11) NOT NULL,
  `award_name` varchar(255) DEFAULT NULL,
  `participation_type` varchar(255) DEFAULT NULL,
  `student_name` varchar(255) DEFAULT NULL,
  `competition_level` varchar(255) DEFAULT NULL,
  `event_name` varchar(255) DEFAULT NULL,
  `month_year` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `meta_central_file`
--

CREATE TABLE `meta_central_file` (
  `meta_id` int(11) NOT NULL,
  `doc_id` int(11) NOT NULL,
  `event` varchar(255) DEFAULT NULL,
  `club_name` varchar(255) DEFAULT NULL,
  `event_name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `meta_conference`
--

CREATE TABLE `meta_conference` (
  `meta_id` int(11) NOT NULL,
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
  `location` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `meta_conference`
--

INSERT INTO `meta_conference` (`meta_id`, `doc_id`, `paper_title`, `conference_name`, `published_paper_name`, `authors`, `paper_type`, `volume_no`, `issue_no`, `page_no`, `indexing`, `publication_link`, `issn_no`, `doi`, `from_date`, `to_date`, `organised_by`, `location`) VALUES
(1, 18, 'cse u1 conference proceedings', 'cse-conference', 'cse conference paper', '{\"name\":[\"cse u1\"],\"affiliation\":[\"GMRIT\"],\"position\":[\"First author\"]}', 'Paper Publication', '1', '1', '1', 'SCI', NULL, NULL, NULL, '2025-01-01', '2025-01-02', 'GMRIT', 'rajam');

-- --------------------------------------------------------

--
-- Table structure for table `meta_conf_organised`
--

CREATE TABLE `meta_conf_organised` (
  `meta_id` int(11) NOT NULL,
  `doc_id` int(11) NOT NULL,
  `mode` varchar(255) DEFAULT NULL,
  `date_from` date DEFAULT NULL,
  `date_to` date DEFAULT NULL,
  `organised_by` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `meta_conf_organised`
--

INSERT INTO `meta_conf_organised` (`meta_id`, `doc_id`, `mode`, `date_from`, `date_to`, `organised_by`, `location`) VALUES
(1, 22, 'Online', '2025-01-01', '2025-01-02', 'GMRIT', 'rajam');

-- --------------------------------------------------------

--
-- Table structure for table `meta_criteria_file`
--

CREATE TABLE `meta_criteria_file` (
  `meta_id` int(11) NOT NULL,
  `doc_id` int(11) NOT NULL,
  `criteria_no` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `semester` varchar(255) DEFAULT NULL,
  `section` varchar(255) DEFAULT NULL,
  `ext_or_int` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `meta_dept_file`
--

CREATE TABLE `meta_dept_file` (
  `meta_id` int(11) NOT NULL,
  `doc_id` int(11) NOT NULL,
  `file_type` varchar(255) DEFAULT NULL,
  `sub_file_type` varchar(255) DEFAULT NULL,
  `semester` varchar(255) DEFAULT NULL,
  `review_period` varchar(255) DEFAULT NULL,
  `study_year` varchar(255) DEFAULT NULL,
  `meeting_no` varchar(255) DEFAULT NULL,
  `event_details` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`event_details`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `meta_exam_qual`
--

CREATE TABLE `meta_exam_qual` (
  `meta_id` int(11) NOT NULL,
  `doc_id` int(11) NOT NULL,
  `reg_no` varchar(255) DEFAULT NULL,
  `exam` varchar(255) DEFAULT NULL,
  `exam_status` varchar(255) DEFAULT NULL,
  `rank` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `meta_fdp_attended`
--

CREATE TABLE `meta_fdp_attended` (
  `meta_id` int(11) NOT NULL,
  `doc_id` int(11) NOT NULL,
  `date_from` date DEFAULT NULL,
  `date_to` date DEFAULT NULL,
  `organised_by` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `mode` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `meta_fdp_attended`
--

INSERT INTO `meta_fdp_attended` (`meta_id`, `doc_id`, `date_from`, `date_to`, `organised_by`, `location`, `mode`) VALUES
(7, 17, '2025-01-01', '2025-01-02', 'gmrit', 'rajam', 'Online'),
(8, 25, '2025-01-01', '2025-01-02', 'gmrit', 'rajam', 'Online');

-- --------------------------------------------------------

--
-- Table structure for table `meta_fdp_organised`
--

CREATE TABLE `meta_fdp_organised` (
  `meta_id` int(11) NOT NULL,
  `doc_id` int(11) NOT NULL,
  `mode` varchar(50) DEFAULT NULL,
  `date_from` date DEFAULT NULL,
  `date_to` date DEFAULT NULL,
  `organised_by` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `meta_fdp_organised`
--

INSERT INTO `meta_fdp_organised` (`meta_id`, `doc_id`, `mode`, `date_from`, `date_to`, `organised_by`, `location`, `title`) VALUES
(1, 21, 'Online', '2025-01-01', '2025-01-02', 'GMRIT', 'rajam', NULL),
(2, 27, 'Online', '2025-01-01', '2025-01-02', 'gmrit', 'rajam', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `meta_higher_ed`
--

CREATE TABLE `meta_higher_ed` (
  `meta_id` int(11) NOT NULL,
  `doc_id` int(11) NOT NULL,
  `student_name` varchar(255) DEFAULT NULL,
  `programme` varchar(255) DEFAULT NULL,
  `institution` varchar(255) DEFAULT NULL,
  `admitted_programme` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `meta_journal`
--

CREATE TABLE `meta_journal` (
  `meta_id` int(11) NOT NULL,
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
  `payment` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `meta_journal`
--

INSERT INTO `meta_journal` (`meta_id`, `doc_id`, `paper_title`, `journal_name`, `authors`, `issn_no`, `volume_no`, `issue_no`, `page_no`, `doi`, `jcr_quartile`, `scopus_quartile`, `publication_link`, `indexing`, `date_of_publication`, `impact_factor`, `quality_factor`, `payment`) VALUES
(5, 20, 'cse u1 research paper ', 'cse u1 journal', '{\"name\":[\"cse u1\"],\"affiliation\":[\"GMRIT\"],\"position\":[\"First author\"]}', NULL, NULL, NULL, NULL, NULL, 'Q1', NULL, NULL, 'SCI', '2025-01-01', NULL, NULL, 'Open Access'),
(6, 24, 'cse u2 Journal paper', 'cse u2 journal', '{\"name\":[\"cse u2\"],\"affiliation\":[\"GMRIT\"],\"position\":[\"First author\"]}', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'SCI', '2026-01-01', '74', NULL, 'Open Access');

-- --------------------------------------------------------

--
-- Table structure for table `meta_patent`
--

CREATE TABLE `meta_patent` (
  `meta_id` int(11) NOT NULL,
  `doc_id` int(11) NOT NULL,
  `patent_title` varchar(255) DEFAULT NULL,
  `patent_no` varchar(255) DEFAULT NULL,
  `patent_type` varchar(255) DEFAULT NULL,
  `date_of_issue` date DEFAULT NULL,
  `inventors` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `meta_patent`
--

INSERT INTO `meta_patent` (`meta_id`, `doc_id`, `patent_title`, `patent_no`, `patent_type`, `date_of_issue`, `inventors`) VALUES
(1, 19, 'cse u1 patent', '1', 'granted', '2025-01-01', '{\"name\":[\"cse u1\"],\"affiliation\":[\"GMRIT\"],\"position\":[\"Main Inventor\"]}'),
(2, 26, 'cse u2 patent', '1', 'published', '2025-01-01', '{\"name\":[\"cse u2\"],\"affiliation\":[\"\"],\"position\":[\"Main Inventor\"]}');

-- --------------------------------------------------------

--
-- Table structure for table `meta_placement`
--

CREATE TABLE `meta_placement` (
  `meta_id` int(11) NOT NULL,
  `doc_id` int(11) NOT NULL,
  `student_name` varchar(255) DEFAULT NULL,
  `programme` varchar(255) DEFAULT NULL,
  `employer` varchar(255) DEFAULT NULL,
  `pay` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `meta_scholarship`
--

CREATE TABLE `meta_scholarship` (
  `meta_id` int(11) NOT NULL,
  `doc_id` int(11) NOT NULL,
  `scheme_name` varchar(255) DEFAULT NULL,
  `gov_students` varchar(255) DEFAULT NULL,
  `gov_amount` varchar(255) DEFAULT NULL,
  `inst_students` varchar(255) DEFAULT NULL,
  `inst_amount` varchar(255) DEFAULT NULL,
  `ngo_students` varchar(255) DEFAULT NULL,
  `ngo_amount` varchar(255) DEFAULT NULL,
  `ngo_name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `meta_student_activity_file`
--

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
  `objective_outcome` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `meta_student_activity_file`
--

INSERT INTO `meta_student_activity_file` (`doc_id`, `activity_category`, `sub_category`, `event_type`, `topic_domain`, `event_title`, `date_from`, `date_to`, `resource_person`, `target_audience`, `participant_count`, `location`, `event_mode`, `objective_outcome`) VALUES
(28, 'Grad Talks', NULL, NULL, 'Career Counselling', 'cse career counsel', '2025-01-01', '2025-01-02', 'cse user 1', 'Department Wise', 24, 'https://csecareercounselling.com', 'Online', 'cse user conducted career counselling'),
(29, 'Grad Talks', NULL, NULL, 'Awareness of Trends and Technologies', 'cse u1 grad talk', '2025-01-03', '2025-01-05', 'cse user 1', 'Department Wise', 60, 'https://csegradtalk.com', 'Online', 'cse user 1 grad talk '),
(30, 'Soft skills', NULL, NULL, NULL, 'cse u1 soft skills', '2025-01-05', '2025-01-05', 'cse user 1', 'Department Wise', 10, 'seminar hall', 'Offline', 'every one participated'),
(31, 'Professional Societies', 'ACM', 'Grad Talks', 'Career Counselling', 'professional soc ACM grad talks', '2025-01-07', '2025-01-08', 'ACM coord', 'Department Wise', 100, 'Seminar hall', 'Offline', 'nearly 100 students attended'),
(32, 'Clubs', 'Google Developer Student Club (Gdsc)', 'Hackathon', NULL, 'clubs gdc hackathon', '2025-01-10', '2025-01-11', 'Prime Minister ', 'Central / University Wide', 100, 'Block 6 Seminar Hall', 'Offline', 'Hackathon results are not announced till date'),
(33, 'Language and communication skills', NULL, NULL, NULL, 'aids user 1 language and communication skills', '2025-02-01', '2025-02-05', 'Dr.Ch.Srinivasa Rao', 'Department Wise', 99, 'block 1 seminar hall', 'Offline', 'communication skills'),
(34, 'Soft skills', NULL, NULL, NULL, 'aids user 1 soft skill event', '2025-03-01', '2025-03-02', 'Rubicon', 'Department Wise', 164, 'block 2 seminar hall', 'Offline', 'soft skills report'),
(36, 'Stepcone', NULL, NULL, NULL, 'Stepcone Web Challenge', '2025-02-15', '2025-02-16', NULL, 'Central / University Wide', 150, 'Campus Auditorium', 'Offline', NULL),
(37, 'Stepcone', NULL, NULL, NULL, 'Stepcone Web Challenge', '2025-02-15', '2025-02-16', NULL, 'Central / University Wide', 150, 'Campus Auditorium', 'Offline', NULL),
(41, 'Clubs', 'Coding Club', 'Competition / Contest', NULL, 'codechef student', '2023-02-22', '2023-02-23', NULL, 'Central / University Wide', 91, 'cse seminar hall', 'Offline', 'every one are forced to join the event '),
(42, 'Soft skills', NULL, NULL, NULL, 'soft skills aids', '2024-08-16', '2024-08-20', 'crt training program', 'Department Wise', 150, 'coding lab', 'Offline', 'It is mandatory for all '),
(43, 'Language and communication skills', NULL, NULL, NULL, 'l and c aids', '2023-10-09', '2023-10-10', 'Dr.B. Lakshmana Rao', 'Department Wise', 77, 'aids seminar hall', 'Offline', 'Language and communication skills'),
(44, 'Expert Talks / Guest Lectures', NULL, NULL, 'Career Counselling', 'fueling innovation ', '2022-11-25', '2023-11-25', 'K. Hari Krishna, Rocket Wheel.', 'Department Wise', 99, 'civil seminar hall', 'Offline', 'guest lecture on fueling innovation '),
(45, 'Expert Talks / Guest Lectures', NULL, NULL, 'Career Counselling', 'Startup and Entrepreneurship', '2023-08-03', '2023-08-03', NULL, 'Department Wise', 120, 'civil seminar hall', 'Offline', 'B.Tech Civil Engineering that the\r\nVirtual Guest lecture Session on \"Startup and Entrepreneurship for Young\r\nminds\"'),
(46, 'Expert Talks / Guest Lectures', NULL, NULL, 'Awareness of Trends and Technologies', '\"AWS Academy cloud foundation', '2023-09-25', '2023-09-25', ' Dr M.Satish and Ms. Y.Nagamani', 'Department Wise', 221, 'cse seminar hall', 'Offline', 'aws fundamentals'),
(47, 'Expert Talks / Guest Lectures', NULL, NULL, 'Awareness of Trends and Technologies', 'Data analytics and it\'s applications', '2023-07-22', '2023-07-22', 'Mr. Surya B. Ayyalasomayajula,', 'Department Wise', 190, 'cse da lab', 'Offline', 'data analytics and it\'s applications'),
(48, 'Expert Talks / Guest Lectures', NULL, NULL, 'Awareness of Trends and Technologies', 'Hands on dev ops', '2024-03-13', '2024-03-15', ' Ms. Lavanya .J, Deveops Tech. Lead at Kyndryl Solutions, ', 'Department Wise', 240, 'coding lab', 'Offline', ' Ms. Lavanya .J, Deveops Tech. Lead at Kyndryl Solutions conducted hands on dev ops'),
(50, 'Expert Talks / Guest Lectures', NULL, NULL, 'Career Counselling', 'Career Opportunities in Software Sector', '2024-04-27', '2024-04-27', 'Mr. P.Srinivasa \r\nRamarishna, Technical Lead, Coginzant', 'Department Wise', 65, 'Seminar hall', 'Offline', 'Career Opportunities in Software Sector'),
(52, 'Expert Talks / Guest Lectures', NULL, NULL, 'Career Counselling', 'Preparation for Civil Service Examinations ', '2023-11-27', '2023-11-27', 'Mr. V.Bjargav, \r\nAssistant Commissioner-State Tax', 'Department Wise', 97, 'EEE Seminar hall', 'Offline', 'Mr. V.Bjargav, \r\nAssistant Commissioner-State Tax'),
(53, 'Stepcone', NULL, 'Paper Presentation', NULL, 'Technical paper presentation', '2024-02-16', '2024-02-17', 'Dr. B.ANIL KUMAR Dr. T. S. Kishore ', 'Central / University Wide', 183, 'EEE block', 'Offline', 'Technical paper presentation'),
(54, 'Stepcone', NULL, 'Workshop', NULL, 'Aurdino and iot ', '2024-02-26', '2024-02-27', 'Dr. B.ANIL KUMAR Dr. T. S. Kishore ', 'Central / University Wide', 96, 'EEE block', 'Offline', 'Aurdino and iot'),
(55, 'Stepcone', 'EEE', 'Competition', 'Cultural / Arts', 'Brain vitae', '2024-02-16', '0000-00-00', '', 'Central / University Wide', 0, '', 'Offline', ''),
(56, 'Stepcone', NULL, 'Competition', NULL, 'Electrical ladder', '2024-02-16', '2024-02-17', NULL, 'Central / University Wide', 179, 'block 5', 'Offline', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `meta_student_body`
--

CREATE TABLE `meta_student_body` (
  `meta_id` int(11) NOT NULL,
  `doc_id` int(11) NOT NULL,
  `body_name` varchar(255) DEFAULT NULL,
  `event_name` varchar(255) DEFAULT NULL,
  `from_date` date DEFAULT NULL,
  `to_date` date DEFAULT NULL,
  `organised_by` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `participation_status` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `meta_student_conference`
--

CREATE TABLE `meta_student_conference` (
  `meta_id` int(11) NOT NULL,
  `doc_id` int(11) NOT NULL,
  `paper_title` varchar(255) DEFAULT NULL,
  `paper_type` varchar(255) DEFAULT NULL,
  `from_date` date DEFAULT NULL,
  `to_date` date DEFAULT NULL,
  `organised_by` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `meta_student_event`
--

CREATE TABLE `meta_student_event` (
  `meta_id` int(11) NOT NULL,
  `doc_id` int(11) NOT NULL,
  `activity` varchar(255) DEFAULT NULL,
  `event_name` varchar(255) DEFAULT NULL,
  `from_date` date DEFAULT NULL,
  `to_date` date DEFAULT NULL,
  `organised_by` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `participation_status` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `meta_student_journal`
--

CREATE TABLE `meta_student_journal` (
  `meta_id` int(11) NOT NULL,
  `doc_id` int(11) NOT NULL,
  `paper_title` varchar(255) DEFAULT NULL,
  `journal_name` varchar(255) DEFAULT NULL,
  `indexing` varchar(255) DEFAULT NULL,
  `date_of_submission` date DEFAULT NULL,
  `impact_factor` varchar(255) DEFAULT NULL,
  `quality_factor` varchar(255) DEFAULT NULL,
  `payment` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `naac_criteria_data`
--

CREATE TABLE `naac_criteria_data` (
  `id` int(11) NOT NULL,
  `submission_id` int(11) NOT NULL,
  `criterion_number` int(11) NOT NULL,
  `data_json` longtext NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `naac_criteria_data`
--

INSERT INTO `naac_criteria_data` (`id`, `submission_id`, `criterion_number`, `data_json`, `updated_at`) VALUES
(1, 1, 1, '{\"qlm_1_1_1_text\":\"\",\"qnm_1_1_2_revised\":\"\",\"qnm_1_1_2_total\":\"\",\"table_1_1_2\":[],\"qnm_1_1_3_focused\":\"\",\"qnm_1_1_3_total\":\"\",\"table_1_1_3\":[],\"qnm_1_2_1_new\":\"\",\"qnm_1_2_1_total\":\"\",\"table_1_2_1\":[],\"qnm_1_2_2_cbcs\":\"\",\"qnm_1_2_2_total\":\"\",\"table_1_2_2\":[],\"qlm_1_3_1_text\":\"\",\"qnm_1_3_2_courses\":\"\",\"table_1_3_2\":[],\"qnm_1_3_3_enrolled\":\"\",\"qnm_1_3_3_total\":\"\",\"table_1_3_3\":[],\"qnm_1_3_4_undertaking\":\"\",\"qnm_1_3_4_total\":\"\",\"table_1_3_4\":[],\"fb_students\":false,\"fb_teachers\":false,\"fb_employers\":false,\"fb_alumni\":false,\"qlm_1_4_2_process\":\"554\",\"final_pdf_file\":\"final_criterion1_dept_1_2026_27_1793252108.pdf\",\"final_pdf_url\":\"http:\\/\\/localhost\\/mini\\/FMS\\/public\\/index.php?route=reports\\/download&type=naac&file=final_criterion1_dept_1_2026_27_1793252108.pdf\",\"final_pdf_generated_at\":\"2026-10-29 06:35:08\"}', '2026-10-29 05:35:08');

-- --------------------------------------------------------

--
-- Table structure for table `naac_form_schemas`
--

CREATE TABLE `naac_form_schemas` (
  `schema_id` int(11) NOT NULL,
  `criterion_number` int(11) NOT NULL,
  `version_name` varchar(50) NOT NULL DEFAULT 'v1',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `schema_json` longtext NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `naac_form_schemas`
--

INSERT INTO `naac_form_schemas` (`schema_id`, `criterion_number`, `version_name`, `is_active`, `schema_json`, `created_at`) VALUES
(1, 1, 'v1', 0, '{\"sections\":[{\"title\":\"1.1 Curricular Planning and Implementation\",\"metrics\":[{\"id\":\"1.1.1\",\"title\":\"1.1.1: Effective curriculum planning and delivery\",\"description\":\"The Institution ensures effective curriculum planning and delivery through a well-planned and documented process including Academic calendar and conduct of continuous internal Assessment. Write description in maximum of 500 words.\",\"fields\":[{\"name\":\"qnm_1_1_1_text\",\"label\":\"Description\",\"type\":\"textarea\",\"placeholder\":\"Describe the process of curriculum planning and delivery...\"},{\"name\":\"file_1_1_1\",\"label\":\"Supporting Document (Academic Calendar, Delivery Plans, etc.)\",\"type\":\"file\",\"accept\":\".pdf\",\"description\":\"Please upload a combined PDF.\"}]}]},{\"title\":\"1.2 Academic Flexibility\",\"metrics\":[{\"id\":\"1.2.1\",\"title\":\"1.2.1: Certificate\\/Value added courses\",\"description\":\"Number of Certificate\\/Value added courses offered and online courses of MOOCs, SWAYAM, NPTEL etc. (where the students of the institution have enrolled and successfully completed).\",\"fields\":[{\"name\":\"qnm_1_2_1_count\",\"label\":\"Total Number of Courses\",\"type\":\"number\",\"placeholder\":\"Enter number of courses\"},{\"name\":\"file_1_2_1\",\"label\":\"Institutional Data \\/ List of Courses\",\"type\":\"file\",\"accept\":\".pdf\",\"description\":\"Upload PDF containing course list and details.\"}]},{\"id\":\"1.2.2\",\"title\":\"1.2.2: Percentage of students enrolled in these courses\",\"description\":\"Percentage of students enrolled in Certificate\\/ Value added courses and also completed online courses of MOOCs, SWAYAM, NPTEL etc.\",\"fields\":[{\"name\":\"qnm_1_2_2_enrolled\",\"label\":\"Total Students Enrolled\",\"type\":\"number\",\"placeholder\":\"Enrolled students\"},{\"name\":\"qnm_1_2_2_total\",\"label\":\"Total Students in Institution\",\"type\":\"number\",\"placeholder\":\"Total students\"},{\"name\":\"file_1_2_2\",\"label\":\"Attendance \\/ Certificates Proof\",\"type\":\"file\",\"accept\":\".pdf\",\"description\":\"Upload attendance sheets or sample certificates as a single PDF.\"}]}]},{\"title\":\"1.3 Curriculum Enrichment\",\"metrics\":[{\"id\":\"1.3.1\",\"title\":\"1.3.1: Integration of Crosscutting Issues\",\"description\":\"Institution integrates crosscutting issues relevant to Professional Ethics, Gender, Human Values, Environment and Sustainability into the Curriculum. Write description in maximum of 500 words.\",\"fields\":[{\"name\":\"qnm_1_3_1_text\",\"label\":\"Description\",\"type\":\"textarea\",\"placeholder\":\"Describe how these issues are integrated...\"},{\"name\":\"file_1_3_1\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\",\"description\":\"Upload list of relevant courses and syllabus copy.\"}]},{\"id\":\"1.3.2\",\"title\":\"1.3.2: Project Work \\/ Field Work \\/ Internships\",\"description\":\"Percentage of students undertaking project work\\/field work\\/ internships.\",\"fields\":[{\"name\":\"qnm_1_3_2_count\",\"label\":\"Number of students undertaking project\\/field work\\/internships\",\"type\":\"number\",\"placeholder\":\"Number of students\"},{\"name\":\"file_1_3_2\",\"label\":\"List of Students & Completion Certificates\",\"type\":\"file\",\"accept\":\".pdf\",\"description\":\"Upload relevant PDF proofs.\"}]}]},{\"title\":\"1.4 Feedback System\",\"metrics\":[{\"id\":\"1.4.1\",\"title\":\"1.4.1: Structured Feedback System\",\"description\":\"Institution obtains feedback on the academic performance and ambience of the institution from various stakeholders and action taken report is on the website.\",\"fields\":[{\"name\":\"feedback_stakeholders\",\"label\":\"Feedback collected from (Check all that apply):\",\"type\":\"checkbox_group\",\"options\":[{\"name\":\"fb_students\",\"label\":\"Students\"},{\"name\":\"fb_teachers\",\"label\":\"Teachers\"},{\"name\":\"fb_employers\",\"label\":\"Employers\"},{\"name\":\"fb_alumni\",\"label\":\"Alumni\"}]},{\"name\":\"qnm_1_4_1_url\",\"label\":\"Action Taken Report URL (on institutional website)\",\"type\":\"url\",\"placeholder\":\"https:\\/\\/...\"},{\"name\":\"file_1_4_1\",\"label\":\"Feedback Analysis & Action Taken Report\",\"type\":\"file\",\"accept\":\".pdf\",\"description\":\"Upload the official signed PDF.\"}]}]}]}', '2026-10-07 15:29:06'),
(2, 2, 'v1', 0, '{\"sections\":[{\"title\":\"2.1 Student Enrolment and Profile\",\"metrics\":[{\"id\":\"2.1.1\",\"title\":\"2.1.1: Enrolment Percentage\",\"description\":\"Average enrolment percentage (Students admitted vs sanctioned seats).\",\"fields\":[{\"name\":\"qnm_2_1_1_admitted\",\"label\":\"Number of students admitted\",\"type\":\"number\"},{\"name\":\"qnm_2_1_1_sanctioned\",\"label\":\"Number of sanctioned seats\",\"type\":\"number\"},{\"name\":\"file_2_1_1\",\"label\":\"Document Upload\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"2.1.2\",\"title\":\"2.1.2: Reserved Categories\",\"description\":\"Average percentage of seats filled against reserved categories (SC, ST, OBC, Divyangjan, etc.).\",\"fields\":[{\"name\":\"qnm_2_1_2_filled\",\"label\":\"Seats filled against reserved categories\",\"type\":\"number\"},{\"name\":\"qnm_2_1_2_reserved\",\"label\":\"Total seats earmarked for reserved categories\",\"type\":\"number\"},{\"name\":\"file_2_1_2\",\"label\":\"Document Upload\",\"type\":\"file\",\"accept\":\".pdf\"}]}]},{\"title\":\"2.2 Catering to Student Diversity\",\"metrics\":[{\"id\":\"2.2.1\",\"title\":\"2.2.1: Advanced and Slow Learners\",\"description\":\"The institution assesses the learning levels of the students and organizes special Programmes for advanced learners and slow learners. Write description in max 500 words.\",\"fields\":[{\"name\":\"qlm_2_2_1_text\",\"label\":\"Description\",\"type\":\"textarea\"},{\"name\":\"file_2_2_1\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"2.2.2\",\"title\":\"2.2.2: Student-Teacher Ratio\",\"description\":\"Student - Full time teacher ratio (Data for the latest completed academic year).\",\"fields\":[{\"name\":\"qnm_2_2_2_students\",\"label\":\"Total Number of Students\",\"type\":\"number\"},{\"name\":\"qnm_2_2_2_teachers\",\"label\":\"Total Number of Full-Time Teachers\",\"type\":\"number\"}]}]},{\"title\":\"2.3 Teaching-Learning Process\",\"metrics\":[{\"id\":\"2.3.1\",\"title\":\"2.3.1: Student-centric methods\",\"description\":\"Student centric methods, such as experiential learning, participative learning and problem solving methodologies are used for enhancing learning experiences.\",\"fields\":[{\"name\":\"qlm_2_3_1_text\",\"label\":\"Description\",\"type\":\"textarea\"},{\"name\":\"file_2_3_1\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"2.3.2\",\"title\":\"2.3.2: ICT Tools\",\"description\":\"Teachers use ICT enabled tools for effective teaching-learning process.\",\"fields\":[{\"name\":\"qlm_2_3_2_text\",\"label\":\"Description of ICT tools used\",\"type\":\"textarea\"},{\"name\":\"file_2_3_2\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"2.3.3\",\"title\":\"2.3.3: Mentor-Mentee Ratio\",\"description\":\"Ratio of mentor to students for academic and other related issues.\",\"fields\":[{\"name\":\"qnm_2_3_3_mentors\",\"label\":\"Number of Mentors\",\"type\":\"number\"},{\"name\":\"qnm_2_3_3_students\",\"label\":\"Number of Students assigned to mentors\",\"type\":\"number\"},{\"name\":\"file_2_3_3\",\"label\":\"Mentor-Mentee List Document\",\"type\":\"file\",\"accept\":\".pdf\"}]}]},{\"title\":\"2.4 Teacher Profile and Quality\",\"metrics\":[{\"id\":\"2.4.1\",\"title\":\"2.4.1: Full-time teachers against sanctioned posts\",\"description\":\"Average percentage of full time teachers against sanctioned posts.\",\"fields\":[{\"name\":\"qnm_2_4_1_teachers\",\"label\":\"Number of Full-Time Teachers\",\"type\":\"number\"},{\"name\":\"qnm_2_4_1_sanctioned\",\"label\":\"Number of Sanctioned Posts\",\"type\":\"number\"},{\"name\":\"file_2_4_1\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"2.4.2\",\"title\":\"2.4.2: Teachers with Ph.D.\",\"description\":\"Average percentage of full time teachers with Ph. D. \\/ D.M. \\/ M.Ch. \\/ D.N.B Superspeciality \\/ D.Sc. \\/ D.Litt.\",\"fields\":[{\"name\":\"qnm_2_4_2_phd\",\"label\":\"Number of teachers with Ph.D. etc.\",\"type\":\"number\"},{\"name\":\"file_2_4_2\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]}]},{\"title\":\"2.5 Evaluation Process and Reforms\",\"metrics\":[{\"id\":\"2.5.1\",\"title\":\"2.5.1: CIE Reforms\",\"description\":\"Mechanism of internal assessment is transparent and robust in terms of frequency and mode.\",\"fields\":[{\"name\":\"qlm_2_5_1_text\",\"label\":\"Description\",\"type\":\"textarea\"},{\"name\":\"file_2_5_1\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]}]},{\"title\":\"2.6 Student Performance and Learning Outcomes\",\"metrics\":[{\"id\":\"2.6.1\",\"title\":\"2.6.1: Outcomes definition and publication\",\"description\":\"Programme and course outcomes for all Programmes offered by the institution are stated and displayed on website and communicated to teachers and students.\",\"fields\":[{\"name\":\"qlm_2_6_1_text\",\"label\":\"Description\",\"type\":\"textarea\"},{\"name\":\"qlm_2_6_1_url\",\"label\":\"Website URL link for Outcomes\",\"type\":\"url\"},{\"name\":\"file_2_6_1\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"2.6.3\",\"title\":\"2.6.3: Pass percentage\",\"description\":\"Pass percentage of Students during last five years.\",\"fields\":[{\"name\":\"qnm_2_6_3_passed\",\"label\":\"Number of students passed in final year exam\",\"type\":\"number\"},{\"name\":\"qnm_2_6_3_appeared\",\"label\":\"Number of students appeared in final year exam\",\"type\":\"number\"},{\"name\":\"file_2_6_3\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]}]}]}', '2026-10-07 15:35:57'),
(3, 1, 'v2_autonomous', 0, '{\"sections\":[{\"title\":\"1.1 Curriculum Design and Development\",\"metrics\":[{\"id\":\"1.1.1\",\"title\":\"1.1.1: Curricula developed and implemented have relevance to needs\",\"description\":\"Curricula developed and implemented have relevance to the local, national, regional and global developmental needs which is reflected in POs, PSOs and COs.\",\"fields\":[{\"name\":\"qlm_1_1_1_text\",\"label\":\"Description (Max 500 words)\",\"type\":\"textarea\"},{\"name\":\"file_1_1_1\",\"label\":\"Document Upload\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"1.1.2\",\"title\":\"1.1.2: Syllabus revision\",\"description\":\"Percentage of Programmes where syllabus revision was carried out during the last five years.\",\"fields\":[{\"name\":\"qnm_1_1_2_revised\",\"label\":\"Number of programmes where syllabus was revised\",\"type\":\"number\"},{\"name\":\"qnm_1_1_2_total\",\"label\":\"Total number of programmes offered\",\"type\":\"number\"},{\"name\":\"file_1_1_2\",\"label\":\"Minutes of relevant Academic Council\\/BOS meeting\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"1.1.3\",\"title\":\"1.1.3: Courses focusing on employability\\/entrepreneurship\\/skill development\",\"description\":\"Average percentage of courses having focus on employability\\/entrepreneurship\\/skill development.\",\"fields\":[{\"name\":\"qnm_1_1_3_focused\",\"label\":\"Number of courses focusing on employability\\/entrepreneurship\\/skill development\",\"type\":\"number\"},{\"name\":\"qnm_1_1_3_total\",\"label\":\"Total number of courses in all programmes\",\"type\":\"number\"},{\"name\":\"file_1_1_3\",\"label\":\"Programme\\/Curriculum\\/Syllabus of courses\",\"type\":\"file\",\"accept\":\".pdf\"}]}]},{\"title\":\"1.2 Academic Flexibility\",\"metrics\":[{\"id\":\"1.2.1\",\"title\":\"1.2.1: New courses introduced\",\"description\":\"Percentage of new courses introduced of the total number of courses across all Programmes offered during the last five years.\",\"fields\":[{\"name\":\"qnm_1_2_1_new\",\"label\":\"Number of new courses introduced\",\"type\":\"number\"},{\"name\":\"qnm_1_2_1_total\",\"label\":\"Total number of courses offered\",\"type\":\"number\"},{\"name\":\"file_1_2_1\",\"label\":\"Document Upload\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"1.2.2\",\"title\":\"1.2.2: Choice Based Credit System (CBCS)\\/elective course system\",\"description\":\"Percentage of Programmes in which CBCS\\/elective course system has been implemented.\",\"fields\":[{\"name\":\"qnm_1_2_2_cbcs\",\"label\":\"Number of programmes in which CBCS\\/elective course system implemented\",\"type\":\"number\"},{\"name\":\"qnm_1_2_2_total\",\"label\":\"Total number of programmes offered\",\"type\":\"number\"},{\"name\":\"file_1_2_2\",\"label\":\"Document Upload\",\"type\":\"file\",\"accept\":\".pdf\"}]}]},{\"title\":\"1.3 Curriculum Enrichment\",\"metrics\":[{\"id\":\"1.3.1\",\"title\":\"1.3.1: Integration of crosscutting issues\",\"description\":\"Institution integrates crosscutting issues relevant to Professional Ethics, Gender, Human Values, Environment and Sustainability into the Curriculum.\",\"fields\":[{\"name\":\"qlm_1_3_1_text\",\"label\":\"Description (Max 500 words)\",\"type\":\"textarea\"},{\"name\":\"file_1_3_1\",\"label\":\"Document Upload\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"1.3.2\",\"title\":\"1.3.2: Value-added courses\",\"description\":\"Number of value-added courses imparting transferable and life skills offered during the last five years.\",\"fields\":[{\"name\":\"qnm_1_3_2_courses\",\"label\":\"Number of value-added courses\",\"type\":\"number\"},{\"name\":\"file_1_3_2\",\"label\":\"Document Upload\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"1.3.3\",\"title\":\"1.3.3: Students enrolled in value-added courses\",\"description\":\"Average Percentage of students enrolled in the value-added courses (from 1.3.2).\",\"fields\":[{\"name\":\"qnm_1_3_3_enrolled\",\"label\":\"Number of students enrolled\",\"type\":\"number\"},{\"name\":\"qnm_1_3_3_total\",\"label\":\"Total number of students\",\"type\":\"number\"},{\"name\":\"file_1_3_3\",\"label\":\"Document Upload\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"1.3.4\",\"title\":\"1.3.4: Field projects\\/ internships\\/ student projects\",\"description\":\"Percentage of students undertaking field projects\\/ internships \\/ student projects.\",\"fields\":[{\"name\":\"qnm_1_3_4_undertaking\",\"label\":\"Number of students undertaking projects\\/internships\",\"type\":\"number\"},{\"name\":\"qnm_1_3_4_total\",\"label\":\"Total number of students\",\"type\":\"number\"},{\"name\":\"file_1_3_4\",\"label\":\"Document Upload\",\"type\":\"file\",\"accept\":\".pdf\"}]}]},{\"title\":\"1.4 Feedback System\",\"metrics\":[{\"id\":\"1.4.1\",\"title\":\"1.4.1: Structured feedback for design and review of syllabus\",\"description\":\"Structured feedback for design and review of syllabus received from Stakeholders.\",\"fields\":[{\"name\":\"feedback_stakeholders\",\"label\":\"Feedback received from (Check all that apply):\",\"type\":\"checkbox_group\",\"options\":[{\"name\":\"fb_students\",\"label\":\"Students\"},{\"name\":\"fb_teachers\",\"label\":\"Teachers\"},{\"name\":\"fb_employers\",\"label\":\"Employers\"},{\"name\":\"fb_alumni\",\"label\":\"Alumni\"}]},{\"name\":\"file_1_4_1\",\"label\":\"Document Upload (Action Taken Report)\",\"type\":\"file\",\"accept\":\".pdf\"}]}]}]}', '2026-10-07 15:50:40'),
(4, 1, 'v3_university', 0, '{\"sections\":[{\"title\":\"1.1 Curriculum Design and Development\",\"metrics\":[{\"id\":\"1.1.1\",\"title\":\"1.1.1: Curricula developed and implemented have relevance to needs\",\"description\":\"Curricula developed and implemented have relevance to the local, national, regional and global developmental needs which is reflected in POs, PSOs and COs.\",\"fields\":[{\"name\":\"qlm_1_1_1_text\",\"label\":\"Description (Max 500 words)\",\"type\":\"textarea\"},{\"name\":\"file_1_1_1\",\"label\":\"Document Upload\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"1.1.2\",\"title\":\"1.1.2: Syllabus revision\",\"description\":\"Percentage of Programmes where syllabus revision was carried out during the last five years.\",\"fields\":[{\"name\":\"qnm_1_1_2_revised\",\"label\":\"Number of programmes where syllabus was revised\",\"type\":\"number\"},{\"name\":\"qnm_1_1_2_total\",\"label\":\"Total number of programmes offered\",\"type\":\"number\"},{\"name\":\"file_1_1_2\",\"label\":\"Minutes of relevant Academic Council\\/BOS meeting\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"1.1.3\",\"title\":\"1.1.3: Courses focusing on employability\\/entrepreneurship\\/skill development\",\"description\":\"Average percentage of courses having focus on employability\\/entrepreneurship\\/skill development.\",\"fields\":[{\"name\":\"qnm_1_1_3_focused\",\"label\":\"Number of courses focusing on employability\\/entrepreneurship\\/skill development\",\"type\":\"number\"},{\"name\":\"qnm_1_1_3_total\",\"label\":\"Total number of courses in all programmes\",\"type\":\"number\"},{\"name\":\"file_1_1_3\",\"label\":\"Programme\\/Curriculum\\/Syllabus of courses\",\"type\":\"file\",\"accept\":\".pdf\"}]}]},{\"title\":\"1.2 Academic Flexibility\",\"metrics\":[{\"id\":\"1.2.1\",\"title\":\"1.2.1: New courses introduced\",\"description\":\"Percentage of new courses introduced of the total number of courses across all Programmes offered during the last five years.\",\"fields\":[{\"name\":\"qnm_1_2_1_new\",\"label\":\"Number of new courses introduced\",\"type\":\"number\"},{\"name\":\"qnm_1_2_1_total\",\"label\":\"Total number of courses offered\",\"type\":\"number\"},{\"name\":\"file_1_2_1\",\"label\":\"Document Upload\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"1.2.2\",\"title\":\"1.2.2: Choice Based Credit System (CBCS)\\/elective course system\",\"description\":\"Percentage of Programmes in which CBCS\\/elective course system has been implemented.\",\"fields\":[{\"name\":\"qnm_1_2_2_cbcs\",\"label\":\"Number of programmes in which CBCS\\/elective course system implemented\",\"type\":\"number\"},{\"name\":\"qnm_1_2_2_total\",\"label\":\"Total number of programmes offered\",\"type\":\"number\"},{\"name\":\"file_1_2_2\",\"label\":\"Document Upload\",\"type\":\"file\",\"accept\":\".pdf\"}]}]},{\"title\":\"1.3 Curriculum Enrichment\",\"metrics\":[{\"id\":\"1.3.1\",\"title\":\"1.3.1: Integration of crosscutting issues\",\"description\":\"Institution integrates crosscutting issues relevant to Professional Ethics, Gender, Human Values, Environment and Sustainability into the Curriculum.\",\"fields\":[{\"name\":\"qlm_1_3_1_text\",\"label\":\"Description (Max 500 words)\",\"type\":\"textarea\"},{\"name\":\"file_1_3_1\",\"label\":\"Document Upload\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"1.3.2\",\"title\":\"1.3.2: Value-added courses\",\"description\":\"Number of value-added courses imparting transferable and life skills offered during the last five years.\",\"fields\":[{\"name\":\"qnm_1_3_2_courses\",\"label\":\"Number of value-added courses\",\"type\":\"number\"},{\"name\":\"file_1_3_2\",\"label\":\"Document Upload\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"1.3.3\",\"title\":\"1.3.3: Students enrolled in value-added courses\",\"description\":\"Average Percentage of students enrolled in the value-added courses (from 1.3.2).\",\"fields\":[{\"name\":\"qnm_1_3_3_enrolled\",\"label\":\"Number of students enrolled\",\"type\":\"number\"},{\"name\":\"qnm_1_3_3_total\",\"label\":\"Total number of students\",\"type\":\"number\"},{\"name\":\"file_1_3_3\",\"label\":\"Document Upload\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"1.3.4\",\"title\":\"1.3.4: Field projects\\/ internships\\/ student projects\",\"description\":\"Percentage of students undertaking field projects\\/ internships \\/ student projects.\",\"fields\":[{\"name\":\"qnm_1_3_4_undertaking\",\"label\":\"Number of students undertaking projects\\/internships\",\"type\":\"number\"},{\"name\":\"qnm_1_3_4_total\",\"label\":\"Total number of students\",\"type\":\"number\"},{\"name\":\"file_1_3_4\",\"label\":\"Document Upload\",\"type\":\"file\",\"accept\":\".pdf\"}]}]},{\"title\":\"1.4 Feedback System\",\"metrics\":[{\"id\":\"1.4.1\",\"title\":\"1.4.1: Structured feedback for design and review of syllabus\",\"description\":\"Structured feedback for design and review of syllabus received from Stakeholders.\",\"fields\":[{\"name\":\"feedback_stakeholders\",\"label\":\"Feedback received from (Check all that apply):\",\"type\":\"checkbox_group\",\"options\":[{\"name\":\"fb_students\",\"label\":\"Students\"},{\"name\":\"fb_teachers\",\"label\":\"Teachers\"},{\"name\":\"fb_employers\",\"label\":\"Employers\"},{\"name\":\"fb_alumni\",\"label\":\"Alumni\"}]},{\"name\":\"file_1_4_1\",\"label\":\"Document Upload (Action Taken Report)\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"1.4.2\",\"title\":\"1.4.2: Feedback processes\",\"description\":\"Feedback processes of the institution.\",\"fields\":[{\"name\":\"qlm_1_4_2_process\",\"label\":\"Describe the process of collecting and analyzing feedback\",\"type\":\"textarea\"},{\"name\":\"file_1_4_2\",\"label\":\"Document Upload\",\"type\":\"file\",\"accept\":\".pdf\"}]}]}]}', '2026-10-07 16:00:27'),
(5, 2, 'v3_university', 0, '{\"sections\":[{\"title\":\"2.1 Student Enrolment and Profile\",\"metrics\":[{\"id\":\"2.1.1\",\"title\":\"2.1.1: Demand Ratio\",\"description\":\"Demand Ratio: Number of seats available vs Number of eligible applications received.\",\"fields\":[{\"name\":\"qnm_2_1_1_seats\",\"label\":\"Number of seats available\",\"type\":\"number\"},{\"name\":\"qnm_2_1_1_apps\",\"label\":\"Number of eligible applications received\",\"type\":\"number\"},{\"name\":\"file_2_1_1\",\"label\":\"Document Upload\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"2.1.2\",\"title\":\"2.1.2: Reserved Categories\",\"description\":\"Average percentage of seats filled against reserved categories (SC, ST, OBC, Divyangjan, etc.).\",\"fields\":[{\"name\":\"qnm_2_1_2_filled\",\"label\":\"Seats filled against reserved categories\",\"type\":\"number\"},{\"name\":\"qnm_2_1_2_reserved\",\"label\":\"Total seats earmarked for reserved categories\",\"type\":\"number\"},{\"name\":\"file_2_1_2\",\"label\":\"Document Upload\",\"type\":\"file\",\"accept\":\".pdf\"}]}]},{\"title\":\"2.2 Catering to Student Diversity\",\"metrics\":[{\"id\":\"2.2.1\",\"title\":\"2.2.1: Advanced and Slow Learners\",\"description\":\"The institution assesses the learning levels of the students and organizes special Programmes for advanced learners and slow learners.\",\"fields\":[{\"name\":\"qlm_2_2_1_text\",\"label\":\"Description\",\"type\":\"textarea\"},{\"name\":\"file_2_2_1\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"2.2.2\",\"title\":\"2.2.2: Student-Teacher Ratio\",\"description\":\"Student - Full time teacher ratio.\",\"fields\":[{\"name\":\"qnm_2_2_2_students\",\"label\":\"Total Number of Students\",\"type\":\"number\"},{\"name\":\"qnm_2_2_2_teachers\",\"label\":\"Total Number of Full-Time Teachers\",\"type\":\"number\"}]}]},{\"title\":\"2.3 Teaching-Learning Process\",\"metrics\":[{\"id\":\"2.3.1\",\"title\":\"2.3.1: Student-centric methods\",\"description\":\"Student centric methods, such as experiential learning, participative learning and problem solving methodologies.\",\"fields\":[{\"name\":\"qlm_2_3_1_text\",\"label\":\"Description\",\"type\":\"textarea\"},{\"name\":\"file_2_3_1\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"2.3.2\",\"title\":\"2.3.2: ICT Tools\",\"description\":\"Teachers use ICT enabled tools for effective teaching-learning process.\",\"fields\":[{\"name\":\"qlm_2_3_2_text\",\"label\":\"Description of ICT tools used\",\"type\":\"textarea\"},{\"name\":\"file_2_3_2\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"2.3.3\",\"title\":\"2.3.3: Mentor-Mentee Ratio\",\"description\":\"Ratio of students to mentor for academic and other related issues.\",\"fields\":[{\"name\":\"qnm_2_3_3_mentors\",\"label\":\"Number of Mentors\",\"type\":\"number\"},{\"name\":\"qnm_2_3_3_students\",\"label\":\"Number of Students assigned to mentors\",\"type\":\"number\"},{\"name\":\"file_2_3_3\",\"label\":\"Mentor-Mentee List Document\",\"type\":\"file\",\"accept\":\".pdf\"}]}]},{\"title\":\"2.4 Teacher Profile and Quality\",\"metrics\":[{\"id\":\"2.4.1\",\"title\":\"2.4.1: Full-time teachers against sanctioned posts\",\"description\":\"Average percentage of full time teachers against sanctioned posts.\",\"fields\":[{\"name\":\"qnm_2_4_1_teachers\",\"label\":\"Number of Full-Time Teachers\",\"type\":\"number\"},{\"name\":\"qnm_2_4_1_sanctioned\",\"label\":\"Number of Sanctioned Posts\",\"type\":\"number\"},{\"name\":\"file_2_4_1\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"2.4.2\",\"title\":\"2.4.2: Teachers with Ph.D.\",\"description\":\"Average percentage of full time teachers with Ph.D.\\/D.M\\/M.Ch.\\/D.N.B Superspeciality\\/D.Sc.\\/D\'Lit.\",\"fields\":[{\"name\":\"qnm_2_4_2_phd\",\"label\":\"Number of teachers with Ph.D. etc.\",\"type\":\"number\"},{\"name\":\"file_2_4_2\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"2.4.3\",\"title\":\"2.4.3: Teaching experience\",\"description\":\"Average teaching experience of full time teachers in the same institution (Data for the latest completed academic year in number of years).\",\"fields\":[{\"name\":\"qnm_2_4_3_years\",\"label\":\"Total experience of full-time teachers (in years)\",\"type\":\"number\"},{\"name\":\"file_2_4_3\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"2.4.4\",\"title\":\"2.4.4: Teacher Awards\",\"description\":\"Average percentage of full time teachers who received awards, recognition, fellowships at State, National, International level from Government\\/Govt. recognized bodies.\",\"fields\":[{\"name\":\"qnm_2_4_4_awards\",\"label\":\"Number of teachers receiving awards\",\"type\":\"number\"},{\"name\":\"file_2_4_4\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]}]},{\"title\":\"2.5 Evaluation Process and Reforms\",\"metrics\":[{\"id\":\"2.5.1\",\"title\":\"2.5.1: Result Declaration Days\",\"description\":\"Average number of days from the date of last semester-end\\/ year-end examination till the declaration of results.\",\"fields\":[{\"name\":\"qnm_2_5_1_days\",\"label\":\"Average number of days\",\"type\":\"number\"},{\"name\":\"file_2_5_1\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"2.5.2\",\"title\":\"2.5.2: Student complaints\\/grievances\",\"description\":\"Average percentage of student complaints\\/grievances about evaluation against total number appeared in the examinations.\",\"fields\":[{\"name\":\"qnm_2_5_2_grievances\",\"label\":\"Number of complaints\\/grievances\",\"type\":\"number\"},{\"name\":\"qnm_2_5_2_appeared\",\"label\":\"Total number appeared\",\"type\":\"number\"},{\"name\":\"file_2_5_2\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"2.5.3\",\"title\":\"2.5.3: IT integration in exams\",\"description\":\"IT integration and reforms in the examination procedures and processes (continuous internal assessment and end-semester assessment) have brought in considerable improvement in examination management system.\",\"fields\":[{\"name\":\"qlm_2_5_3_text\",\"label\":\"Description\",\"type\":\"textarea\"},{\"name\":\"file_2_5_3\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]}]},{\"title\":\"2.6 Student Performance and Learning Outcomes\",\"metrics\":[{\"id\":\"2.6.1\",\"title\":\"2.6.1: Outcomes definition\",\"description\":\"Programme and course outcomes for all Programmes offered by the institution are stated and displayed on website and communicated to teachers and students.\",\"fields\":[{\"name\":\"qlm_2_6_1_text\",\"label\":\"Description\",\"type\":\"textarea\"},{\"name\":\"qlm_2_6_1_url\",\"label\":\"Website URL link for Outcomes\",\"type\":\"url\"},{\"name\":\"file_2_6_1\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"2.6.2\",\"title\":\"2.6.2: Outcomes attainment\",\"description\":\"Attainment of POs and COs are evaluated.\",\"fields\":[{\"name\":\"qlm_2_6_2_text\",\"label\":\"Description\",\"type\":\"textarea\"},{\"name\":\"file_2_6_2\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"2.6.3\",\"title\":\"2.6.3: Pass percentage\",\"description\":\"Pass percentage of Students (Data for the latest completed academic year).\",\"fields\":[{\"name\":\"qnm_2_6_3_passed\",\"label\":\"Number of students passed in final year exam\",\"type\":\"number\"},{\"name\":\"qnm_2_6_3_appeared\",\"label\":\"Number of students appeared in final year exam\",\"type\":\"number\"},{\"name\":\"file_2_6_3\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]}]}]}', '2026-10-07 16:00:27'),
(6, 1, 'v4_university_with_tables', 0, '{\"sections\":[{\"title\":\"1.1 Curriculum Design and Development\",\"metrics\":[{\"id\":\"1.1.1\",\"title\":\"1.1.1: Curricula developed and implemented have relevance to needs\",\"description\":\"Curricula developed and implemented have relevance to the local, national, regional and global developmental needs which is reflected in POs, PSOs and COs.\",\"fields\":[{\"name\":\"qlm_1_1_1_text\",\"label\":\"Description (Max 500 words)\",\"type\":\"textarea\"},{\"name\":\"file_1_1_1\",\"label\":\"Document Upload\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"1.1.2\",\"title\":\"1.1.2: Syllabus revision\",\"description\":\"Percentage of Programmes where syllabus revision was carried out during the last five years.\",\"fields\":[{\"name\":\"qnm_1_1_2_revised\",\"label\":\"Number of programmes where syllabus was revised (Formula Numerator)\",\"type\":\"number\"},{\"name\":\"qnm_1_1_2_total\",\"label\":\"Total number of programmes offered (Formula Denominator)\",\"type\":\"number\"},{\"name\":\"table_1_1_2\",\"label\":\"Data Template: Programmes and Syllabus Revision\",\"type\":\"table\",\"columns\":[{\"name\":\"prog_code\",\"label\":\"Programme Code\",\"type\":\"text\"},{\"name\":\"prog_name\",\"label\":\"Programme Name\",\"type\":\"text\"},{\"name\":\"dept_name\",\"label\":\"Department\",\"type\":\"text\"},{\"name\":\"year_intro\",\"label\":\"Year of Introduction\",\"type\":\"number\"},{\"name\":\"is_revised\",\"label\":\"Revised in last 5 years? (Yes\\/No)\",\"type\":\"text\"},{\"name\":\"year_revision\",\"label\":\"Year of Revision\",\"type\":\"number\"},{\"name\":\"pct_replaced\",\"label\":\"% Content replaced\",\"type\":\"number\"}]},{\"name\":\"file_1_1_2\",\"label\":\"Minutes of relevant Academic Council\\/BOS meeting\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"1.1.3\",\"title\":\"1.1.3: Courses focusing on employability\\/entrepreneurship\\/skill development\",\"description\":\"Average percentage of courses having focus on employability\\/entrepreneurship\\/skill development.\",\"fields\":[{\"name\":\"qnm_1_1_3_focused\",\"label\":\"Number of courses focusing on employability\\/entrepreneurship\\/skill dev\",\"type\":\"number\"},{\"name\":\"qnm_1_1_3_total\",\"label\":\"Total number of courses in all programmes\",\"type\":\"number\"},{\"name\":\"table_1_1_3\",\"label\":\"Data Template: Employability & Skill Development Courses\",\"type\":\"table\",\"columns\":[{\"name\":\"course_name\",\"label\":\"Course Name\",\"type\":\"text\"},{\"name\":\"course_code\",\"label\":\"Course Code\",\"type\":\"text\"},{\"name\":\"prog_name\",\"label\":\"Programme Name\",\"type\":\"text\"},{\"name\":\"activities\",\"label\":\"Activities bearing on Employability\",\"type\":\"text\"},{\"name\":\"year_intro\",\"label\":\"Year of Introduction\",\"type\":\"number\"}]},{\"name\":\"file_1_1_3\",\"label\":\"Programme\\/Curriculum\\/Syllabus of courses\",\"type\":\"file\",\"accept\":\".pdf\"}]}]},{\"title\":\"1.2 Academic Flexibility\",\"metrics\":[{\"id\":\"1.2.1\",\"title\":\"1.2.1: New courses introduced\",\"description\":\"Percentage of new courses introduced of the total number of courses across all Programmes offered during the last five years.\",\"fields\":[{\"name\":\"qnm_1_2_1_new\",\"label\":\"Number of new courses introduced\",\"type\":\"number\"},{\"name\":\"qnm_1_2_1_total\",\"label\":\"Total number of courses offered\",\"type\":\"number\"},{\"name\":\"table_1_2_1\",\"label\":\"Data Template: New Courses\",\"type\":\"table\",\"columns\":[{\"name\":\"course_name\",\"label\":\"Name of new course\",\"type\":\"text\"},{\"name\":\"course_code\",\"label\":\"Course Code\",\"type\":\"text\"},{\"name\":\"prog_name\",\"label\":\"Programme Name\",\"type\":\"text\"},{\"name\":\"year_intro\",\"label\":\"Year of Introduction\",\"type\":\"number\"}]},{\"name\":\"file_1_2_1\",\"label\":\"Document Upload\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"1.2.2\",\"title\":\"1.2.2: Choice Based Credit System (CBCS)\\/elective course system\",\"description\":\"Percentage of Programmes in which CBCS\\/elective course system has been implemented.\",\"fields\":[{\"name\":\"qnm_1_2_2_cbcs\",\"label\":\"Number of programmes in which CBCS\\/elective course system implemented\",\"type\":\"number\"},{\"name\":\"qnm_1_2_2_total\",\"label\":\"Total number of programmes offered\",\"type\":\"number\"},{\"name\":\"table_1_2_2\",\"label\":\"Data Template: CBCS Programmes\",\"type\":\"table\",\"columns\":[{\"name\":\"prog_name\",\"label\":\"Programme Name\",\"type\":\"text\"},{\"name\":\"prog_code\",\"label\":\"Programme Code\",\"type\":\"text\"},{\"name\":\"is_cbcs\",\"label\":\"CBCS Implemented? (Yes\\/No)\",\"type\":\"text\"},{\"name\":\"year_cbcs\",\"label\":\"Year of Implementation\",\"type\":\"number\"}]},{\"name\":\"file_1_2_2\",\"label\":\"Document Upload\",\"type\":\"file\",\"accept\":\".pdf\"}]}]},{\"title\":\"1.3 Curriculum Enrichment\",\"metrics\":[{\"id\":\"1.3.1\",\"title\":\"1.3.1: Integration of crosscutting issues\",\"description\":\"Institution integrates crosscutting issues relevant to Professional Ethics, Gender, Human Values, Environment and Sustainability into the Curriculum.\",\"fields\":[{\"name\":\"qlm_1_3_1_text\",\"label\":\"Description (Max 500 words)\",\"type\":\"textarea\"},{\"name\":\"file_1_3_1\",\"label\":\"Document Upload\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"1.3.2\",\"title\":\"1.3.2 & 1.3.3: Value-added courses & Student Enrollment\",\"description\":\"Value-added courses imparting transferable and life skills and percentage of students enrolled.\",\"fields\":[{\"name\":\"qnm_1_3_2_courses\",\"label\":\"Number of value-added courses\",\"type\":\"number\"},{\"name\":\"qnm_1_3_3_enrolled\",\"label\":\"Number of students enrolled in value-added courses\",\"type\":\"number\"},{\"name\":\"qnm_1_3_3_total\",\"label\":\"Total number of students in institution\",\"type\":\"number\"},{\"name\":\"table_1_3_2\",\"label\":\"Data Template: Value-added Courses\",\"type\":\"table\",\"columns\":[{\"name\":\"course_name\",\"label\":\"Name of value added course\",\"type\":\"text\"},{\"name\":\"course_code\",\"label\":\"Course Code\",\"type\":\"text\"},{\"name\":\"year_offering\",\"label\":\"Year of offering\",\"type\":\"number\"},{\"name\":\"times_offered\",\"label\":\"Times offered in year\",\"type\":\"number\"},{\"name\":\"duration\",\"label\":\"Duration (Hours)\",\"type\":\"number\"},{\"name\":\"enrolled_count\",\"label\":\"Students enrolled\",\"type\":\"number\"},{\"name\":\"completed_count\",\"label\":\"Students completed\",\"type\":\"number\"}]},{\"name\":\"file_1_3_2\",\"label\":\"Document Upload\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"1.3.4\",\"title\":\"1.3.4: Field projects\\/ internships\\/ student projects\",\"description\":\"Percentage of students undertaking field projects\\/ internships \\/ student projects.\",\"fields\":[{\"name\":\"qnm_1_3_4_undertaking\",\"label\":\"Number of students undertaking projects\\/internships\",\"type\":\"number\"},{\"name\":\"qnm_1_3_4_total\",\"label\":\"Total number of students\",\"type\":\"number\"},{\"name\":\"table_1_3_4\",\"label\":\"Data Template: Internships and Projects\",\"type\":\"table\",\"columns\":[{\"name\":\"prog_name\",\"label\":\"Programme Name\",\"type\":\"text\"},{\"name\":\"prog_code\",\"label\":\"Programme Code\",\"type\":\"text\"},{\"name\":\"student_name\",\"label\":\"Name of Student\",\"type\":\"text\"},{\"name\":\"project_type\",\"label\":\"Type (Project\\/Internship\\/Field Work)\",\"type\":\"text\"}]},{\"name\":\"file_1_3_4\",\"label\":\"Document Upload\",\"type\":\"file\",\"accept\":\".pdf\"}]}]},{\"title\":\"1.4 Feedback System\",\"metrics\":[{\"id\":\"1.4.1\",\"title\":\"1.4.1 & 1.4.2: Structured feedback for design and review of syllabus\",\"description\":\"Structured feedback for design and review of syllabus received from Stakeholders and processes.\",\"fields\":[{\"name\":\"feedback_stakeholders\",\"label\":\"Feedback received from (Check all that apply):\",\"type\":\"checkbox_group\",\"options\":[{\"name\":\"fb_students\",\"label\":\"Students\"},{\"name\":\"fb_teachers\",\"label\":\"Teachers\"},{\"name\":\"fb_employers\",\"label\":\"Employers\"},{\"name\":\"fb_alumni\",\"label\":\"Alumni\"}]},{\"name\":\"qlm_1_4_2_process\",\"label\":\"Describe the process of collecting and analyzing feedback\",\"type\":\"textarea\"},{\"name\":\"file_1_4_1\",\"label\":\"Document Upload (Action Taken Report)\",\"type\":\"file\",\"accept\":\".pdf\"}]}]}]}', '2026-10-07 16:21:47'),
(7, 1, 'v4_university_with_tables', 1, '{\"sections\":[{\"title\":\"1.1 Curriculum Design and Development\",\"metrics\":[{\"id\":\"1.1.1\",\"title\":\"1.1.1: Curricula developed and implemented have relevance to needs\",\"description\":\"Curricula developed and implemented have relevance to the local, national, regional and global developmental needs which is reflected in POs, PSOs and COs.\",\"fields\":[{\"name\":\"qlm_1_1_1_text\",\"label\":\"Description (Max 500 words)\",\"type\":\"textarea\"},{\"name\":\"file_1_1_1\",\"label\":\"Document Upload\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"1.1.2\",\"title\":\"1.1.2: Syllabus revision\",\"description\":\"Percentage of Programmes where syllabus revision was carried out during the last five years.\",\"fields\":[{\"name\":\"qnm_1_1_2_revised\",\"label\":\"Number of programmes where syllabus was revised (Formula Numerator)\",\"type\":\"number\"},{\"name\":\"qnm_1_1_2_total\",\"label\":\"Total number of programmes offered (Formula Denominator)\",\"type\":\"number\"},{\"name\":\"table_1_1_2\",\"label\":\"Data Template: Programmes and Syllabus Revision\",\"type\":\"table\",\"columns\":[{\"name\":\"prog_code\",\"label\":\"Programme Code\",\"type\":\"text\"},{\"name\":\"prog_name\",\"label\":\"Programme Name\",\"type\":\"text\"},{\"name\":\"dept_name\",\"label\":\"Department\",\"type\":\"text\"},{\"name\":\"year_intro\",\"label\":\"Year of Introduction\",\"type\":\"number\"},{\"name\":\"is_revised\",\"label\":\"Revised in last 5 years? (Yes\\/No)\",\"type\":\"text\"},{\"name\":\"year_revision\",\"label\":\"Year of Revision\",\"type\":\"number\"},{\"name\":\"pct_replaced\",\"label\":\"% Content replaced\",\"type\":\"number\"}]},{\"name\":\"file_1_1_2\",\"label\":\"Minutes of relevant Academic Council\\/BOS meeting\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"1.1.3\",\"title\":\"1.1.3: Courses focusing on employability\\/entrepreneurship\\/skill development\",\"description\":\"Average percentage of courses having focus on employability\\/entrepreneurship\\/skill development.\",\"fields\":[{\"name\":\"qnm_1_1_3_focused\",\"label\":\"Number of courses focusing on employability\\/entrepreneurship\\/skill dev\",\"type\":\"number\"},{\"name\":\"qnm_1_1_3_total\",\"label\":\"Total number of courses in all programmes\",\"type\":\"number\"},{\"name\":\"table_1_1_3\",\"label\":\"Data Template: Employability & Skill Development Courses\",\"type\":\"table\",\"columns\":[{\"name\":\"course_name\",\"label\":\"Course Name\",\"type\":\"text\"},{\"name\":\"course_code\",\"label\":\"Course Code\",\"type\":\"text\"},{\"name\":\"prog_name\",\"label\":\"Programme Name\",\"type\":\"text\"},{\"name\":\"activities\",\"label\":\"Activities bearing on Employability\",\"type\":\"text\"},{\"name\":\"year_intro\",\"label\":\"Year of Introduction\",\"type\":\"number\"}]},{\"name\":\"file_1_1_3\",\"label\":\"Programme\\/Curriculum\\/Syllabus of courses\",\"type\":\"file\",\"accept\":\".pdf\"}]}]},{\"title\":\"1.2 Academic Flexibility\",\"metrics\":[{\"id\":\"1.2.1\",\"title\":\"1.2.1: New courses introduced\",\"description\":\"Percentage of new courses introduced of the total number of courses across all Programmes offered during the last five years.\",\"fields\":[{\"name\":\"qnm_1_2_1_new\",\"label\":\"Number of new courses introduced\",\"type\":\"number\"},{\"name\":\"qnm_1_2_1_total\",\"label\":\"Total number of courses offered\",\"type\":\"number\"},{\"name\":\"table_1_2_1\",\"label\":\"Data Template: New Courses\",\"type\":\"table\",\"columns\":[{\"name\":\"course_name\",\"label\":\"Name of new course\",\"type\":\"text\"},{\"name\":\"course_code\",\"label\":\"Course Code\",\"type\":\"text\"},{\"name\":\"prog_name\",\"label\":\"Programme Name\",\"type\":\"text\"},{\"name\":\"year_intro\",\"label\":\"Year of Introduction\",\"type\":\"number\"}]},{\"name\":\"file_1_2_1\",\"label\":\"Document Upload\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"1.2.2\",\"title\":\"1.2.2: Choice Based Credit System (CBCS)\\/elective course system\",\"description\":\"Percentage of Programmes in which CBCS\\/elective course system has been implemented.\",\"fields\":[{\"name\":\"qnm_1_2_2_cbcs\",\"label\":\"Number of programmes in which CBCS\\/elective course system implemented\",\"type\":\"number\"},{\"name\":\"qnm_1_2_2_total\",\"label\":\"Total number of programmes offered\",\"type\":\"number\"},{\"name\":\"table_1_2_2\",\"label\":\"Data Template: CBCS Programmes\",\"type\":\"table\",\"columns\":[{\"name\":\"prog_name\",\"label\":\"Programme Name\",\"type\":\"text\"},{\"name\":\"prog_code\",\"label\":\"Programme Code\",\"type\":\"text\"},{\"name\":\"is_cbcs\",\"label\":\"CBCS Implemented? (Yes\\/No)\",\"type\":\"text\"},{\"name\":\"year_cbcs\",\"label\":\"Year of Implementation\",\"type\":\"number\"}]},{\"name\":\"file_1_2_2\",\"label\":\"Document Upload\",\"type\":\"file\",\"accept\":\".pdf\"}]}]},{\"title\":\"1.3 Curriculum Enrichment\",\"metrics\":[{\"id\":\"1.3.1\",\"title\":\"1.3.1: Integration of crosscutting issues\",\"description\":\"Institution integrates crosscutting issues relevant to Professional Ethics, Gender, Human Values, Environment and Sustainability into the Curriculum.\",\"fields\":[{\"name\":\"qlm_1_3_1_text\",\"label\":\"Description (Max 500 words)\",\"type\":\"textarea\"},{\"name\":\"file_1_3_1\",\"label\":\"Document Upload\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"1.3.2\",\"title\":\"1.3.2: Value-added courses\",\"description\":\"Number of value-added courses imparting transferable and life skills offered during the last five years.\",\"fields\":[{\"name\":\"qnm_1_3_2_courses\",\"label\":\"Number of value-added courses\",\"type\":\"number\"},{\"name\":\"table_1_3_2\",\"label\":\"Data Template: Value-added Courses\",\"type\":\"table\",\"columns\":[{\"name\":\"course_name\",\"label\":\"Name of value added course\",\"type\":\"text\"},{\"name\":\"course_code\",\"label\":\"Course Code\",\"type\":\"text\"},{\"name\":\"year_offering\",\"label\":\"Year of offering\",\"type\":\"number\"},{\"name\":\"times_offered\",\"label\":\"Times offered in year\",\"type\":\"number\"},{\"name\":\"duration\",\"label\":\"Duration (Hours)\",\"type\":\"number\"}]},{\"name\":\"file_1_3_2\",\"label\":\"Document Upload\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"1.3.3\",\"title\":\"1.3.3: Students enrolled in value-added courses\",\"description\":\"Average Percentage of students enrolled in the value-added courses.\",\"fields\":[{\"name\":\"qnm_1_3_3_enrolled\",\"label\":\"Number of students enrolled in value-added courses\",\"type\":\"number\"},{\"name\":\"qnm_1_3_3_total\",\"label\":\"Total number of students in institution\",\"type\":\"number\"},{\"name\":\"table_1_3_3\",\"label\":\"Data Template: Value-added Course Enrollment\",\"type\":\"table\",\"columns\":[{\"name\":\"course_name\",\"label\":\"Name of value added course\",\"type\":\"text\"},{\"name\":\"course_code\",\"label\":\"Course Code\",\"type\":\"text\"},{\"name\":\"enrolled_count\",\"label\":\"Students enrolled\",\"type\":\"number\"},{\"name\":\"completed_count\",\"label\":\"Students completed\",\"type\":\"number\"}]},{\"name\":\"file_1_3_3\",\"label\":\"Document Upload\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"1.3.4\",\"title\":\"1.3.4: Field projects\\/ internships\\/ student projects\",\"description\":\"Percentage of students undertaking field projects\\/ internships \\/ student projects.\",\"fields\":[{\"name\":\"qnm_1_3_4_undertaking\",\"label\":\"Number of students undertaking projects\\/internships\",\"type\":\"number\"},{\"name\":\"qnm_1_3_4_total\",\"label\":\"Total number of students\",\"type\":\"number\"},{\"name\":\"table_1_3_4\",\"label\":\"Data Template: Internships and Projects\",\"type\":\"table\",\"columns\":[{\"name\":\"prog_name\",\"label\":\"Programme Name\",\"type\":\"text\"},{\"name\":\"prog_code\",\"label\":\"Programme Code\",\"type\":\"text\"},{\"name\":\"student_name\",\"label\":\"Name of Student\",\"type\":\"text\"},{\"name\":\"project_type\",\"label\":\"Type (Project\\/Internship\\/Field Work)\",\"type\":\"text\"}]},{\"name\":\"file_1_3_4\",\"label\":\"Document Upload\",\"type\":\"file\",\"accept\":\".pdf\"}]}]},{\"title\":\"1.4 Feedback System\",\"metrics\":[{\"id\":\"1.4.1\",\"title\":\"1.4.1: Structured feedback for design and review of syllabus\",\"description\":\"Structured feedback for design and review of syllabus received from Stakeholders.\",\"fields\":[{\"name\":\"feedback_stakeholders\",\"label\":\"Feedback received from (Check all that apply):\",\"type\":\"checkbox_group\",\"options\":[{\"name\":\"fb_students\",\"label\":\"Students\"},{\"name\":\"fb_teachers\",\"label\":\"Teachers\"},{\"name\":\"fb_employers\",\"label\":\"Employers\"},{\"name\":\"fb_alumni\",\"label\":\"Alumni\"}]},{\"name\":\"file_1_4_1\",\"label\":\"Document Upload (Feedback Reports)\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"1.4.2\",\"title\":\"1.4.2: Feedback processes\",\"description\":\"Feedback processes of the institution.\",\"fields\":[{\"name\":\"qlm_1_4_2_process\",\"label\":\"Describe the process of collecting and analyzing feedback\",\"type\":\"textarea\"},{\"name\":\"file_1_4_2\",\"label\":\"Document Upload (Action Taken Report)\",\"type\":\"file\",\"accept\":\".pdf\"}]}]}]}', '2026-10-07 16:32:07');
INSERT INTO `naac_form_schemas` (`schema_id`, `criterion_number`, `version_name`, `is_active`, `schema_json`, `created_at`) VALUES
(8, 2, 'v4_university_with_tables', 1, '{\"sections\":[{\"title\":\"2.1 Student Enrolment and Profile\",\"metrics\":[{\"id\":\"2.1.1\",\"title\":\"2.1.1: Demand Ratio\",\"description\":\"Demand Ratio: Number of seats available vs Number of eligible applications received.\",\"fields\":[{\"name\":\"qnm_2_1_1_apps\",\"label\":\"Number of eligible applications received (Formula Numerator)\",\"type\":\"number\"},{\"name\":\"qnm_2_1_1_seats\",\"label\":\"Number of seats available (Formula Denominator)\",\"type\":\"number\"},{\"name\":\"table_2_1_1\",\"label\":\"Data Template: Demand Ratio\",\"type\":\"table\",\"columns\":[{\"name\":\"prog_name\",\"label\":\"Programme Name\",\"type\":\"text\"},{\"name\":\"prog_code\",\"label\":\"Programme Code\",\"type\":\"text\"},{\"name\":\"seats_avail\",\"label\":\"Seats Available\",\"type\":\"number\"},{\"name\":\"apps_recv\",\"label\":\"Applications Received\",\"type\":\"number\"},{\"name\":\"students_admit\",\"label\":\"Students Admitted\",\"type\":\"number\"}]},{\"name\":\"file_2_1_1\",\"label\":\"Document Upload\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"2.1.2\",\"title\":\"2.1.2: Reserved Categories\",\"description\":\"Average percentage of seats filled against reserved categories (SC, ST, OBC, Divyangjan, etc.).\",\"fields\":[{\"name\":\"qnm_2_1_2_filled\",\"label\":\"Seats filled against reserved categories (Formula Numerator)\",\"type\":\"number\"},{\"name\":\"qnm_2_1_2_reserved\",\"label\":\"Total seats earmarked for reserved categories (Formula Denominator)\",\"type\":\"number\"},{\"name\":\"table_2_1_2\",\"label\":\"Data Template: Reserved Category Admissions\",\"type\":\"table\",\"columns\":[{\"name\":\"year\",\"label\":\"Year\",\"type\":\"number\"},{\"name\":\"earmark_sc\",\"label\":\"Earmarked SC\",\"type\":\"number\"},{\"name\":\"earmark_st\",\"label\":\"Earmarked ST\",\"type\":\"number\"},{\"name\":\"earmark_obc\",\"label\":\"Earmarked OBC\",\"type\":\"number\"},{\"name\":\"earmark_gen\",\"label\":\"Earmarked Gen\",\"type\":\"number\"},{\"name\":\"admit_sc\",\"label\":\"Admitted SC\",\"type\":\"number\"},{\"name\":\"admit_st\",\"label\":\"Admitted ST\",\"type\":\"number\"},{\"name\":\"admit_obc\",\"label\":\"Admitted OBC\",\"type\":\"number\"},{\"name\":\"admit_gen\",\"label\":\"Admitted Gen\",\"type\":\"number\"}]},{\"name\":\"file_2_1_2\",\"label\":\"Document Upload\",\"type\":\"file\",\"accept\":\".pdf\"}]}]},{\"title\":\"2.2 Catering to Student Diversity\",\"metrics\":[{\"id\":\"2.2.1\",\"title\":\"2.2.1: Advanced and Slow Learners\",\"description\":\"The institution assesses the learning levels of the students and organizes special Programmes for advanced learners and slow learners.\",\"fields\":[{\"name\":\"qlm_2_2_1_text\",\"label\":\"Description\",\"type\":\"textarea\"},{\"name\":\"file_2_2_1\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"2.2.2\",\"title\":\"2.2.2: Student-Teacher Ratio\",\"description\":\"Student - Full time teacher ratio.\",\"fields\":[{\"name\":\"qnm_2_2_2_students\",\"label\":\"Total Number of Students\",\"type\":\"number\"},{\"name\":\"qnm_2_2_2_teachers\",\"label\":\"Total Number of Full-Time Teachers\",\"type\":\"number\"}]}]},{\"title\":\"2.3 Teaching-Learning Process\",\"metrics\":[{\"id\":\"2.3.1\",\"title\":\"2.3.1: Student-centric methods\",\"description\":\"Student centric methods, such as experiential learning, participative learning and problem solving methodologies.\",\"fields\":[{\"name\":\"qlm_2_3_1_text\",\"label\":\"Description\",\"type\":\"textarea\"},{\"name\":\"file_2_3_1\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"2.3.2\",\"title\":\"2.3.2: ICT Tools\",\"description\":\"Teachers use ICT enabled tools for effective teaching-learning process.\",\"fields\":[{\"name\":\"qlm_2_3_2_text\",\"label\":\"Description of ICT tools used\",\"type\":\"textarea\"},{\"name\":\"file_2_3_2\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"2.3.3\",\"title\":\"2.3.3: Mentor-Mentee Ratio\",\"description\":\"Ratio of students to mentor for academic and other related issues.\",\"fields\":[{\"name\":\"qnm_2_3_3_students\",\"label\":\"Number of Students assigned to mentors (Numerator)\",\"type\":\"number\"},{\"name\":\"qnm_2_3_3_mentors\",\"label\":\"Number of Mentors (Denominator)\",\"type\":\"number\"},{\"name\":\"table_2_3_3\",\"label\":\"Data Template: Mentor-Mentee\",\"type\":\"table\",\"columns\":[{\"name\":\"mentor_name\",\"label\":\"Name of Mentor\",\"type\":\"text\"},{\"name\":\"mentor_dept\",\"label\":\"Department\",\"type\":\"text\"},{\"name\":\"assigned_count\",\"label\":\"Students Assigned\",\"type\":\"number\"}]},{\"name\":\"file_2_3_3\",\"label\":\"Mentor-Mentee List Document\",\"type\":\"file\",\"accept\":\".pdf\"}]}]},{\"title\":\"2.4 Teacher Profile and Quality\",\"metrics\":[{\"id\":\"2.4.1\",\"title\":\"2.4.1: Full-time teachers against sanctioned posts\",\"description\":\"Average percentage of full time teachers against sanctioned posts.\",\"fields\":[{\"name\":\"qnm_2_4_1_teachers\",\"label\":\"Number of Full-Time Teachers (Numerator)\",\"type\":\"number\"},{\"name\":\"qnm_2_4_1_sanctioned\",\"label\":\"Number of Sanctioned Posts (Denominator)\",\"type\":\"number\"},{\"name\":\"table_2_4_1\",\"label\":\"Data Template: Teachers vs Sanctioned\",\"type\":\"table\",\"columns\":[{\"name\":\"teacher_name\",\"label\":\"Name of Teacher\",\"type\":\"text\"},{\"name\":\"pan\",\"label\":\"PAN\",\"type\":\"text\"},{\"name\":\"designation\",\"label\":\"Designation\",\"type\":\"text\"},{\"name\":\"year_appt\",\"label\":\"Year of Appointment\",\"type\":\"number\"},{\"name\":\"nature_appt\",\"label\":\"Nature (Permanent\\/Temp)\",\"type\":\"text\"},{\"name\":\"dept\",\"label\":\"Department\",\"type\":\"text\"}]},{\"name\":\"file_2_4_1\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"2.4.2\",\"title\":\"2.4.2: Teachers with Ph.D.\",\"description\":\"Average percentage of full time teachers with Ph.D.\\/D.M\\/M.Ch.\\/D.N.B Superspeciality\\/D.Sc.\\/D\'Lit.\",\"fields\":[{\"name\":\"qnm_2_4_2_phd\",\"label\":\"Number of teachers with Ph.D. etc.\",\"type\":\"number\"},{\"name\":\"table_2_4_2\",\"label\":\"Data Template: Teachers with Ph.D.\",\"type\":\"table\",\"columns\":[{\"name\":\"teacher_name\",\"label\":\"Name of Teacher\",\"type\":\"text\"},{\"name\":\"qualification\",\"label\":\"Qualification (Ph.D\\/D.M etc)\",\"type\":\"text\"},{\"name\":\"year_obt\",\"label\":\"Year Obtained\",\"type\":\"number\"},{\"name\":\"is_guide\",\"label\":\"Research Guide? (Yes\\/No)\",\"type\":\"text\"},{\"name\":\"still_serving\",\"label\":\"Still Serving? (Yes\\/No)\",\"type\":\"text\"}]},{\"name\":\"file_2_4_2\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"2.4.3\",\"title\":\"2.4.3: Teaching experience\",\"description\":\"Average teaching experience of full time teachers in the same institution.\",\"fields\":[{\"name\":\"qnm_2_4_3_years\",\"label\":\"Total experience of full-time teachers (in years)\",\"type\":\"number\"},{\"name\":\"table_2_4_3\",\"label\":\"Data Template: Teaching Experience\",\"type\":\"table\",\"columns\":[{\"name\":\"teacher_name\",\"label\":\"Name of Teacher\",\"type\":\"text\"},{\"name\":\"designation\",\"label\":\"Designation\",\"type\":\"text\"},{\"name\":\"dept\",\"label\":\"Department\",\"type\":\"text\"},{\"name\":\"year_appt\",\"label\":\"Year of Appointment\",\"type\":\"number\"},{\"name\":\"exp_years\",\"label\":\"Experience (Years)\",\"type\":\"number\"},{\"name\":\"still_serving\",\"label\":\"Still Serving?\",\"type\":\"text\"}]},{\"name\":\"file_2_4_3\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"2.4.4\",\"title\":\"2.4.4: Teacher Awards\",\"description\":\"Average percentage of full time teachers who received awards, recognition.\",\"fields\":[{\"name\":\"qnm_2_4_4_awards\",\"label\":\"Number of teachers receiving awards\",\"type\":\"number\"},{\"name\":\"table_2_4_4\",\"label\":\"Data Template: Teacher Awards\",\"type\":\"table\",\"columns\":[{\"name\":\"teacher_name\",\"label\":\"Name of Teacher\",\"type\":\"text\"},{\"name\":\"year_award\",\"label\":\"Year of Award\",\"type\":\"number\"},{\"name\":\"pan\",\"label\":\"PAN\",\"type\":\"text\"},{\"name\":\"award_name\",\"label\":\"Name of Award\",\"type\":\"text\"},{\"name\":\"issuing_body\",\"label\":\"Issuing Body\",\"type\":\"text\"}]},{\"name\":\"file_2_4_4\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]}]},{\"title\":\"2.5 Evaluation Process and Reforms\",\"metrics\":[{\"id\":\"2.5.1\",\"title\":\"2.5.1: Result Declaration Days\",\"description\":\"Average number of days from the date of last semester-end\\/ year-end examination till the declaration of results.\",\"fields\":[{\"name\":\"qnm_2_5_1_days\",\"label\":\"Average number of days\",\"type\":\"number\"},{\"name\":\"table_2_5_1\",\"label\":\"Data Template: Result Declaration\",\"type\":\"table\",\"columns\":[{\"name\":\"prog_name\",\"label\":\"Programme Name\",\"type\":\"text\"},{\"name\":\"prog_code\",\"label\":\"Programme Code\",\"type\":\"text\"},{\"name\":\"sem_year\",\"label\":\"Semester\\/Year\",\"type\":\"text\"},{\"name\":\"date_exam\",\"label\":\"Date of last exam (DD\\/MM\\/YYYY)\",\"type\":\"text\"},{\"name\":\"date_result\",\"label\":\"Date of result (DD\\/MM\\/YYYY)\",\"type\":\"text\"}]},{\"name\":\"file_2_5_1\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"2.5.2\",\"title\":\"2.5.2: Student complaints\\/grievances\",\"description\":\"Average percentage of student complaints\\/grievances about evaluation against total number appeared in the examinations.\",\"fields\":[{\"name\":\"qnm_2_5_2_grievances\",\"label\":\"Number of complaints\\/grievances (Numerator)\",\"type\":\"number\"},{\"name\":\"qnm_2_5_2_appeared\",\"label\":\"Total number appeared (Denominator)\",\"type\":\"number\"},{\"name\":\"file_2_5_2\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"2.5.3\",\"title\":\"2.5.3: IT integration in exams\",\"description\":\"IT integration and reforms in the examination procedures and processes.\",\"fields\":[{\"name\":\"qlm_2_5_3_text\",\"label\":\"Description\",\"type\":\"textarea\"},{\"name\":\"file_2_5_3\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]}]},{\"title\":\"2.6 Student Performance and Learning Outcomes\",\"metrics\":[{\"id\":\"2.6.1\",\"title\":\"2.6.1: Outcomes definition\",\"description\":\"Programme and course outcomes for all Programmes offered by the institution are stated.\",\"fields\":[{\"name\":\"qlm_2_6_1_text\",\"label\":\"Description\",\"type\":\"textarea\"},{\"name\":\"qlm_2_6_1_url\",\"label\":\"Website URL link for Outcomes\",\"type\":\"url\"},{\"name\":\"file_2_6_1\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"2.6.2\",\"title\":\"2.6.2: Outcomes attainment\",\"description\":\"Attainment of POs and COs are evaluated.\",\"fields\":[{\"name\":\"qlm_2_6_2_text\",\"label\":\"Description\",\"type\":\"textarea\"},{\"name\":\"file_2_6_2\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"2.6.3\",\"title\":\"2.6.3: Pass percentage\",\"description\":\"Pass percentage of Students (Data for the latest completed academic year).\",\"fields\":[{\"name\":\"qnm_2_6_3_passed\",\"label\":\"Number of students passed in final year exam (Numerator)\",\"type\":\"number\"},{\"name\":\"qnm_2_6_3_appeared\",\"label\":\"Number of students appeared in final year exam (Denominator)\",\"type\":\"number\"},{\"name\":\"table_2_6_3\",\"label\":\"Data Template: Pass Percentage\",\"type\":\"table\",\"columns\":[{\"name\":\"prog_code\",\"label\":\"Programme Code\",\"type\":\"text\"},{\"name\":\"prog_name\",\"label\":\"Programme Name\",\"type\":\"text\"},{\"name\":\"appeared\",\"label\":\"Appeared Count\",\"type\":\"number\"},{\"name\":\"passed\",\"label\":\"Passed Count\",\"type\":\"number\"},{\"name\":\"pass_pct\",\"label\":\"Pass Percentage\",\"type\":\"number\"}]},{\"name\":\"file_2_6_3\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]}]},{\"title\":\"2.7 Student Satisfaction Survey\",\"metrics\":[{\"id\":\"2.7.1\",\"title\":\"2.7.1: Student Satisfaction Survey\",\"description\":\"Online student satisfaction survey regarding to teaching learning process.\",\"fields\":[{\"name\":\"table_2_7_1\",\"label\":\"Data Template: Student Database for SSS\",\"type\":\"table\",\"columns\":[{\"name\":\"student_name\",\"label\":\"Name of Student\",\"type\":\"text\"},{\"name\":\"gender\",\"label\":\"Gender\",\"type\":\"text\"},{\"name\":\"category\",\"label\":\"Category\",\"type\":\"text\"},{\"name\":\"nationality\",\"label\":\"Nationality\",\"type\":\"text\"},{\"name\":\"email\",\"label\":\"Email ID\",\"type\":\"text\"},{\"name\":\"prog_name\",\"label\":\"Programme Name\",\"type\":\"text\"},{\"name\":\"enrol_id\",\"label\":\"Enrolment ID\",\"type\":\"text\"},{\"name\":\"mobile\",\"label\":\"Mobile Number\",\"type\":\"text\"}]}]}]}]}', '2026-10-07 16:45:15'),
(9, 3, 'v4_university_with_tables', 0, '{\"sections\":[{\"title\":\"3.1 Promotion of Research and Facilities\",\"metrics\":[{\"id\":\"3.1.1\",\"title\":\"3.1.1: Research facilities\",\"description\":\"The institution Research facilities are frequently updated and there is well defined policy for promotion of research.\",\"fields\":[{\"name\":\"qlm_3_1_1_text\",\"label\":\"Description\",\"type\":\"textarea\"},{\"name\":\"file_3_1_1\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"3.1.2\",\"title\":\"3.1.2: Seed money for research\",\"description\":\"The institution provides seed money to its teachers for research.\",\"fields\":[{\"name\":\"qnm_3_1_2_amt\",\"label\":\"Total Amount of seed money provided (in Lakhs)\",\"type\":\"number\"},{\"name\":\"table_3_1_2\",\"label\":\"Data Template: Seed Money\",\"type\":\"table\",\"columns\":[{\"name\":\"teacher_name\",\"label\":\"Name of Teacher\",\"type\":\"text\"},{\"name\":\"amount\",\"label\":\"Amount provided (INR in Lakhs)\",\"type\":\"number\"},{\"name\":\"year\",\"label\":\"Year of receiving\",\"type\":\"number\"}]},{\"name\":\"file_3_1_2\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"3.1.3\",\"title\":\"3.1.3: Fellowships to teachers\",\"description\":\"Percentage of teachers receiving national\\/ international fellowship\\/financial support by various agencies for advanced studies\\/ research.\",\"fields\":[{\"name\":\"qnm_3_1_3_teachers\",\"label\":\"Number of teachers receiving fellowship (Numerator)\",\"type\":\"number\"},{\"name\":\"qnm_3_1_3_total\",\"label\":\"Total number of full time teachers (Denominator)\",\"type\":\"number\"},{\"name\":\"table_3_1_3\",\"label\":\"Data Template: Fellowships\",\"type\":\"table\",\"columns\":[{\"name\":\"teacher_name\",\"label\":\"Name of Teacher\",\"type\":\"text\"},{\"name\":\"fellowship_name\",\"label\":\"Name of Fellowship\",\"type\":\"text\"},{\"name\":\"year\",\"label\":\"Year of Award\",\"type\":\"number\"},{\"name\":\"awarding_agency\",\"label\":\"Awarding Agency\",\"type\":\"text\"}]},{\"name\":\"file_3_1_3\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"3.1.4\",\"title\":\"3.1.4: Research Fellows\",\"description\":\"Number of JRFs, SRFs, Post Doctoral Fellows, Research Associates and other research fellows enrolled.\",\"fields\":[{\"name\":\"qnm_3_1_4_fellows\",\"label\":\"Number of Research Fellows\",\"type\":\"number\"},{\"name\":\"table_3_1_4\",\"label\":\"Data Template: Research Fellows\",\"type\":\"table\",\"columns\":[{\"name\":\"fellow_name\",\"label\":\"Name of Research Fellow\",\"type\":\"text\"},{\"name\":\"year_enroll\",\"label\":\"Year of Enrolment\",\"type\":\"number\"},{\"name\":\"duration\",\"label\":\"Duration\",\"type\":\"text\"},{\"name\":\"type\",\"label\":\"Type (JRF\\/SRF\\/PDF\\/RA)\",\"type\":\"text\"},{\"name\":\"agency\",\"label\":\"Granting Agency\",\"type\":\"text\"}]},{\"name\":\"file_3_1_4\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"3.1.5\",\"title\":\"3.1.5: Institution Facilities\",\"description\":\"Institution has Central Instrumentation Centre, Animal House, Museum, Media laboratory, Business Lab, etc.\",\"fields\":[{\"name\":\"facilities_list\",\"label\":\"Facilities available (Check all that apply):\",\"type\":\"checkbox_group\",\"options\":[{\"name\":\"fac_cic\",\"label\":\"Central Instrumentation Centre\"},{\"name\":\"fac_animal\",\"label\":\"Animal House\\/Green House\"},{\"name\":\"fac_museum\",\"label\":\"Museum\"},{\"name\":\"fac_media\",\"label\":\"Media laboratory\\/Studios\"},{\"name\":\"fac_business\",\"label\":\"Business Lab\"}]},{\"name\":\"file_3_1_5\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"3.1.6\",\"title\":\"3.1.6: Departmental Research Schemes\",\"description\":\"Percentage of departments with UGC-SAP, CAS, DST-FIST, DBT, ICSSR etc.\",\"fields\":[{\"name\":\"qnm_3_1_6_depts\",\"label\":\"Number of Departments with schemes (Numerator)\",\"type\":\"number\"},{\"name\":\"qnm_3_1_6_total\",\"label\":\"Total number of Departments (Denominator)\",\"type\":\"number\"},{\"name\":\"table_3_1_6\",\"label\":\"Data Template: Departmental Schemes\",\"type\":\"table\",\"columns\":[{\"name\":\"dept_name\",\"label\":\"Name of Department\",\"type\":\"text\"},{\"name\":\"scheme_name\",\"label\":\"Name of Scheme\",\"type\":\"text\"},{\"name\":\"funding_agency\",\"label\":\"Funding Agency\",\"type\":\"text\"},{\"name\":\"year_award\",\"label\":\"Year of Award\",\"type\":\"number\"},{\"name\":\"funds\",\"label\":\"Funds Provided (Lakhs)\",\"type\":\"number\"}]},{\"name\":\"file_3_1_6\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]}]},{\"title\":\"3.2 Resource Mobilization for Research\",\"metrics\":[{\"id\":\"3.2.1_3.2.2_3.2.3\",\"title\":\"3.2.1, 3.2.2 & 3.2.3: Research Grants and Projects\",\"description\":\"Extramural funding, government grants, and number of research projects per teacher.\",\"fields\":[{\"name\":\"qnm_3_2_1_funds\",\"label\":\"Total Extramural Funding (in Lakhs)\",\"type\":\"number\"},{\"name\":\"qnm_3_2_2_grants\",\"label\":\"Total Grants for Research Projects (in Lakhs)\",\"type\":\"number\"},{\"name\":\"qnm_3_2_3_projects\",\"label\":\"Number of research projects (Numerator)\",\"type\":\"number\"},{\"name\":\"qnm_3_2_3_teachers\",\"label\":\"Total number of full time teachers (Denominator)\",\"type\":\"number\"},{\"name\":\"table_3_2_x\",\"label\":\"Data Template: Research Projects\",\"type\":\"table\",\"columns\":[{\"name\":\"pi_name\",\"label\":\"Name of PI\\/Co-PI\",\"type\":\"text\"},{\"name\":\"proj_title\",\"label\":\"Title of Project\",\"type\":\"text\"},{\"name\":\"funding_agency\",\"label\":\"Funding Agency\",\"type\":\"text\"},{\"name\":\"dept\",\"label\":\"Department\",\"type\":\"text\"},{\"name\":\"year_award\",\"label\":\"Year of Award\",\"type\":\"number\"},{\"name\":\"funds\",\"label\":\"Funds Provided (Lakhs)\",\"type\":\"number\"},{\"name\":\"duration\",\"label\":\"Duration (Years)\",\"type\":\"number\"}]},{\"name\":\"file_3_2_x\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]}]},{\"title\":\"3.3 Innovation Ecosystem\",\"metrics\":[{\"id\":\"3.3.1\",\"title\":\"3.3.1: Innovation ecosystem\",\"description\":\"Institution has created an eco system for innovations including Incubation centre.\",\"fields\":[{\"name\":\"qlm_3_3_1_text\",\"label\":\"Description\",\"type\":\"textarea\"},{\"name\":\"file_3_3_1\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"3.3.2\",\"title\":\"3.3.2: Workshops\\/seminars on Research\\/IPR\",\"description\":\"Number of workshops\\/seminars conducted on Research methodology, IPR, entrepreneurship.\",\"fields\":[{\"name\":\"qnm_3_3_2_count\",\"label\":\"Number of workshops\\/seminars\",\"type\":\"number\"},{\"name\":\"table_3_3_2\",\"label\":\"Data Template: Workshops\\/Seminars\",\"type\":\"table\",\"columns\":[{\"name\":\"workshop_name\",\"label\":\"Name of Workshop\",\"type\":\"text\"},{\"name\":\"participants\",\"label\":\"Number of Participants\",\"type\":\"number\"},{\"name\":\"date_range\",\"label\":\"Date (From - To)\",\"type\":\"text\"}]},{\"name\":\"file_3_3_2\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"3.3.3\",\"title\":\"3.3.3: Innovation Awards\",\"description\":\"Number of awards \\/ recognitions received for research\\/innovations.\",\"fields\":[{\"name\":\"qnm_3_3_3_awards\",\"label\":\"Number of awards\\/recognitions\",\"type\":\"number\"},{\"name\":\"table_3_3_3\",\"label\":\"Data Template: Innovation Awards\",\"type\":\"table\",\"columns\":[{\"name\":\"title_innov\",\"label\":\"Title of Innovation\",\"type\":\"text\"},{\"name\":\"awardee_name\",\"label\":\"Name of Awardee\",\"type\":\"text\"},{\"name\":\"agency_name\",\"label\":\"Awarding Agency\",\"type\":\"text\"},{\"name\":\"year_award\",\"label\":\"Year of Award\",\"type\":\"number\"}]},{\"name\":\"file_3_3_3\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]}]},{\"title\":\"3.4 Research Publications and Awards\",\"metrics\":[{\"id\":\"3.4.1\",\"title\":\"3.4.1: Code of Ethics\",\"description\":\"The Institution ensures implementation of its stated Code of Ethics for research.\",\"fields\":[{\"name\":\"ethics_list\",\"label\":\"Options (Check all that apply):\",\"type\":\"checkbox_group\",\"options\":[{\"name\":\"eth_comm\",\"label\":\"Ethics Committee exists\"},{\"name\":\"eth_plag\",\"label\":\"Plagiarism check software\"},{\"name\":\"eth_comm_meets\",\"label\":\"Research Advisory Committee meets\"}]},{\"name\":\"file_3_4_1\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"3.4.2\",\"title\":\"3.4.2: Teacher Incentives\",\"description\":\"The institution provides incentives to teachers who receive state, national and international recognitions.\",\"fields\":[{\"name\":\"incentives_list\",\"label\":\"Incentives (Check all that apply):\",\"type\":\"checkbox_group\",\"options\":[{\"name\":\"inc_salary\",\"label\":\"Commendation & monetary reward\"},{\"name\":\"inc_cert\",\"label\":\"Certificate of honor\"}]},{\"name\":\"file_3_4_2\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"3.4.3\",\"title\":\"3.4.3: Patents\",\"description\":\"Number of Patents published\\/awarded.\",\"fields\":[{\"name\":\"qnm_3_4_3_patents\",\"label\":\"Number of Patents\",\"type\":\"number\"},{\"name\":\"table_3_4_3\",\"label\":\"Data Template: Patents\",\"type\":\"table\",\"columns\":[{\"name\":\"patenter_name\",\"label\":\"Name of Patenter\",\"type\":\"text\"},{\"name\":\"patent_num\",\"label\":\"Patent Number\",\"type\":\"text\"},{\"name\":\"patent_title\",\"label\":\"Title of Patent\",\"type\":\"text\"},{\"name\":\"year_award\",\"label\":\"Year of Award\",\"type\":\"number\"}]},{\"name\":\"file_3_4_3\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"3.4.4\",\"title\":\"3.4.4: Ph.D\'s awarded per teacher\",\"description\":\"Number of Ph.D\'s awarded per teacher.\",\"fields\":[{\"name\":\"qnm_3_4_4_phd\",\"label\":\"Number of Ph.D\'s awarded (Numerator)\",\"type\":\"number\"},{\"name\":\"qnm_3_4_4_guides\",\"label\":\"Number of recognized guides (Denominator)\",\"type\":\"number\"},{\"name\":\"table_3_4_4\",\"label\":\"Data Template: Ph.D.s Awarded\",\"type\":\"table\",\"columns\":[{\"name\":\"scholar_name\",\"label\":\"Name of Scholar\",\"type\":\"text\"},{\"name\":\"dept\",\"label\":\"Department\",\"type\":\"text\"},{\"name\":\"guide_name\",\"label\":\"Name of Guide\",\"type\":\"text\"},{\"name\":\"thesis_title\",\"label\":\"Title of Thesis\",\"type\":\"text\"},{\"name\":\"year_reg\",\"label\":\"Year of Registration\",\"type\":\"number\"},{\"name\":\"year_award\",\"label\":\"Year of Award\",\"type\":\"number\"}]},{\"name\":\"file_3_4_4\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"3.4.5\",\"title\":\"3.4.5: Research Papers in UGC Journals\",\"description\":\"Number of research papers per teacher in the Journals notified on UGC website.\",\"fields\":[{\"name\":\"qnm_3_4_5_papers\",\"label\":\"Number of Research Papers (Numerator)\",\"type\":\"number\"},{\"name\":\"qnm_3_4_5_teachers\",\"label\":\"Number of Full-Time Teachers (Denominator)\",\"type\":\"number\"},{\"name\":\"table_3_4_5\",\"label\":\"Data Template: Research Papers\",\"type\":\"table\",\"columns\":[{\"name\":\"paper_title\",\"label\":\"Title of Paper\",\"type\":\"text\"},{\"name\":\"author_name\",\"label\":\"Name of Author\",\"type\":\"text\"},{\"name\":\"dept\",\"label\":\"Department\",\"type\":\"text\"},{\"name\":\"journal_name\",\"label\":\"Journal Name\",\"type\":\"text\"},{\"name\":\"year_pub\",\"label\":\"Year of Publication\",\"type\":\"number\"},{\"name\":\"issn\",\"label\":\"ISSN Number\",\"type\":\"text\"}]},{\"name\":\"file_3_4_5\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"3.4.6\",\"title\":\"3.4.6: Books and Chapters published\",\"description\":\"Number of books and chapters in edited volumes\\/books published.\",\"fields\":[{\"name\":\"qnm_3_4_6_books\",\"label\":\"Number of books and chapters\",\"type\":\"number\"},{\"name\":\"table_3_4_6\",\"label\":\"Data Template: Books and Chapters\",\"type\":\"table\",\"columns\":[{\"name\":\"book_title\",\"label\":\"Title of Book\\/Chapter\",\"type\":\"text\"},{\"name\":\"author_name\",\"label\":\"Author\",\"type\":\"text\"},{\"name\":\"proceedings\",\"label\":\"Title of proceedings\",\"type\":\"text\"},{\"name\":\"publisher\",\"label\":\"Publisher Name\",\"type\":\"text\"},{\"name\":\"isbn\",\"label\":\"ISBN\",\"type\":\"text\"},{\"name\":\"year_pub\",\"label\":\"Year of Publication\",\"type\":\"number\"}]},{\"name\":\"file_3_4_6\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"3.4.7\",\"title\":\"3.4.7: E-content developed by teachers\",\"description\":\"E-content developed by teachers for platforms like NMEICT\\/NPTEL\\/SWAYAM.\",\"fields\":[{\"name\":\"qnm_3_4_7_content\",\"label\":\"Number of E-contents developed\",\"type\":\"number\"},{\"name\":\"table_3_4_7\",\"label\":\"Data Template: E-Content\",\"type\":\"table\",\"columns\":[{\"name\":\"teacher_name\",\"label\":\"Name of Teacher\",\"type\":\"text\"},{\"name\":\"module_name\",\"label\":\"Name of Module\",\"type\":\"text\"},{\"name\":\"platform\",\"label\":\"Platform (SWAYAM etc)\",\"type\":\"text\"},{\"name\":\"date_launch\",\"label\":\"Date of Launch\",\"type\":\"text\"}]},{\"name\":\"file_3_4_7\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"3.4.8_3.4.9\",\"title\":\"3.4.8 & 3.4.9: Bibliometrics (Citation Index and h-index)\",\"description\":\"Bibliometrics of the publications based on average Citation Index and h-index in Scopus\\/ Web of Science.\",\"fields\":[{\"name\":\"qnm_3_4_8_citation\",\"label\":\"Average Citation Index\",\"type\":\"number\"},{\"name\":\"qnm_3_4_9_hindex\",\"label\":\"h-index of the institution\",\"type\":\"number\"},{\"name\":\"file_3_4_8\",\"label\":\"Supporting Document (Citation Index)\",\"type\":\"file\",\"accept\":\".pdf\"},{\"name\":\"file_3_4_9\",\"label\":\"Supporting Document (h-index)\",\"type\":\"file\",\"accept\":\".pdf\"}]}]},{\"title\":\"3.5 Consultancy\",\"metrics\":[{\"id\":\"3.5.1\",\"title\":\"3.5.1: Consultancy Policy\",\"description\":\"Institution has a policy on consultancy including revenue sharing.\",\"fields\":[{\"name\":\"qlm_3_5_1_text\",\"label\":\"Description\",\"type\":\"textarea\"},{\"name\":\"file_3_5_1\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"3.5.2\",\"title\":\"3.5.2: Revenue from Consultancy\",\"description\":\"Revenue generated from consultancy and corporate training.\",\"fields\":[{\"name\":\"qnm_3_5_2_revenue\",\"label\":\"Total Revenue Generated (in Lakhs)\",\"type\":\"number\"},{\"name\":\"table_3_5_2\",\"label\":\"Data Template: Consultancy Revenue\",\"type\":\"table\",\"columns\":[{\"name\":\"consultant_name\",\"label\":\"Name of Consultant\",\"type\":\"text\"},{\"name\":\"project_name\",\"label\":\"Name of Project\",\"type\":\"text\"},{\"name\":\"agency_name\",\"label\":\"Sponsoring Agency\",\"type\":\"text\"},{\"name\":\"revenue\",\"label\":\"Revenue (Lakhs)\",\"type\":\"number\"}]},{\"name\":\"file_3_5_2\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]}]},{\"title\":\"3.6 Extension Activities\",\"metrics\":[{\"id\":\"3.6.1\",\"title\":\"3.6.1: Extension activities in neighbourhood\",\"description\":\"Extension activities in the neighbourhood community in terms of impact.\",\"fields\":[{\"name\":\"qlm_3_6_1_text\",\"label\":\"Description\",\"type\":\"textarea\"},{\"name\":\"file_3_6_1\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"3.6.2\",\"title\":\"3.6.2: Extension Awards\",\"description\":\"Number of awards received by the Institution, its teachers and students for extension activities.\",\"fields\":[{\"name\":\"qnm_3_6_2_awards\",\"label\":\"Number of awards\",\"type\":\"number\"},{\"name\":\"table_3_6_2\",\"label\":\"Data Template: Extension Awards\",\"type\":\"table\",\"columns\":[{\"name\":\"activity_name\",\"label\":\"Name of Activity\",\"type\":\"text\"},{\"name\":\"award_name\",\"label\":\"Name of Award\",\"type\":\"text\"},{\"name\":\"awarding_body\",\"label\":\"Awarding Body\",\"type\":\"text\"},{\"name\":\"year\",\"label\":\"Year of Award\",\"type\":\"number\"}]},{\"name\":\"file_3_6_2\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"3.6.3_3.6.4\",\"title\":\"3.6.3 & 3.6.4: Extension Programs & Student Participation\",\"description\":\"Number of extension and outreach programs conducted and average percentage of students participating.\",\"fields\":[{\"name\":\"qnm_3_6_3_programs\",\"label\":\"Number of programs conducted\",\"type\":\"number\"},{\"name\":\"qnm_3_6_4_participating\",\"label\":\"Number of students participating (Numerator)\",\"type\":\"number\"},{\"name\":\"qnm_3_6_4_total\",\"label\":\"Total number of students (Denominator)\",\"type\":\"number\"},{\"name\":\"table_3_6_x\",\"label\":\"Data Template: Extension Programs\",\"type\":\"table\",\"columns\":[{\"name\":\"activity_name\",\"label\":\"Name of Activity\",\"type\":\"text\"},{\"name\":\"organising_unit\",\"label\":\"Organising Unit\\/Agency\",\"type\":\"text\"},{\"name\":\"scheme_name\",\"label\":\"Name of Scheme\",\"type\":\"text\"},{\"name\":\"year\",\"label\":\"Year\",\"type\":\"number\"},{\"name\":\"student_count\",\"label\":\"Number of Students Participated\",\"type\":\"number\"}]},{\"name\":\"file_3_6_x\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]}]},{\"title\":\"3.7 Collaboration\",\"metrics\":[{\"id\":\"3.7.1\",\"title\":\"3.7.1: Collaborative activities\",\"description\":\"Number of collaborative activities for research, Faculty exchange, Student exchange.\",\"fields\":[{\"name\":\"qnm_3_7_1_collabs\",\"label\":\"Number of collaborative activities\",\"type\":\"number\"},{\"name\":\"table_3_7_1\",\"label\":\"Data Template: Collaborations\",\"type\":\"table\",\"columns\":[{\"name\":\"title_collab\",\"label\":\"Title of Collaboration\",\"type\":\"text\"},{\"name\":\"agency_name\",\"label\":\"Collaborating Agency\",\"type\":\"text\"},{\"name\":\"fin_support\",\"label\":\"Financial Support Source\",\"type\":\"text\"},{\"name\":\"year\",\"label\":\"Year of Collaboration\",\"type\":\"number\"},{\"name\":\"duration\",\"label\":\"Duration\",\"type\":\"text\"}]},{\"name\":\"file_3_7_1\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"3.7.2\",\"title\":\"3.7.2: Functional MoUs\",\"description\":\"Number of functional MoUs with institutions, industries, corporate houses etc.\",\"fields\":[{\"name\":\"qnm_3_7_2_mous\",\"label\":\"Number of functional MoUs\",\"type\":\"number\"},{\"name\":\"table_3_7_2\",\"label\":\"Data Template: Functional MoUs\",\"type\":\"table\",\"columns\":[{\"name\":\"org_name\",\"label\":\"Organisation with MoU\",\"type\":\"text\"},{\"name\":\"inst_name\",\"label\":\"Name of Institution\\/Industry\",\"type\":\"text\"},{\"name\":\"year_sign\",\"label\":\"Year of Signing\",\"type\":\"number\"},{\"name\":\"duration\",\"label\":\"Duration\",\"type\":\"text\"},{\"name\":\"activities_list\",\"label\":\"List of Activities\",\"type\":\"text\"}]},{\"name\":\"file_3_7_2\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]}]}]}', '2026-10-07 16:52:56');
INSERT INTO `naac_form_schemas` (`schema_id`, `criterion_number`, `version_name`, `is_active`, `schema_json`, `created_at`) VALUES
(10, 3, 'v4_university_with_tables', 1, '{\"sections\":[{\"title\":\"3.1 Promotion of Research and Facilities\",\"metrics\":[{\"id\":\"3.1.1\",\"title\":\"3.1.1: Research facilities\",\"description\":\"The institution Research facilities are frequently updated and there is well defined policy for promotion of research.\",\"fields\":[{\"name\":\"qlm_3_1_1_text\",\"label\":\"Description\",\"type\":\"textarea\"},{\"name\":\"file_3_1_1\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"3.1.2\",\"title\":\"3.1.2: Seed money for research\",\"description\":\"The institution provides seed money to its teachers for research.\",\"fields\":[{\"name\":\"qnm_3_1_2_amt\",\"label\":\"Total Amount of seed money provided (in Lakhs)\",\"type\":\"number\"},{\"name\":\"table_3_1_2\",\"label\":\"Data Template: Seed Money\",\"type\":\"table\",\"columns\":[{\"name\":\"teacher_name\",\"label\":\"Name of Teacher\",\"type\":\"text\"},{\"name\":\"amount\",\"label\":\"Amount provided (INR in Lakhs)\",\"type\":\"number\"},{\"name\":\"year\",\"label\":\"Year of receiving\",\"type\":\"number\"}]},{\"name\":\"file_3_1_2\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"3.1.3\",\"title\":\"3.1.3: Fellowships to teachers\",\"description\":\"Percentage of teachers receiving national\\/ international fellowship\\/financial support by various agencies for advanced studies\\/ research.\",\"fields\":[{\"name\":\"qnm_3_1_3_teachers\",\"label\":\"Number of teachers receiving fellowship (Numerator)\",\"type\":\"number\"},{\"name\":\"qnm_3_1_3_total\",\"label\":\"Total number of full time teachers (Denominator)\",\"type\":\"number\"},{\"name\":\"table_3_1_3\",\"label\":\"Data Template: Fellowships\",\"type\":\"table\",\"columns\":[{\"name\":\"teacher_name\",\"label\":\"Name of Teacher\",\"type\":\"text\"},{\"name\":\"fellowship_name\",\"label\":\"Name of Fellowship\",\"type\":\"text\"},{\"name\":\"year\",\"label\":\"Year of Award\",\"type\":\"number\"},{\"name\":\"awarding_agency\",\"label\":\"Awarding Agency\",\"type\":\"text\"}]},{\"name\":\"file_3_1_3\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"3.1.4\",\"title\":\"3.1.4: Research Fellows\",\"description\":\"Number of JRFs, SRFs, Post Doctoral Fellows, Research Associates and other research fellows enrolled.\",\"fields\":[{\"name\":\"qnm_3_1_4_fellows\",\"label\":\"Number of Research Fellows\",\"type\":\"number\"},{\"name\":\"table_3_1_4\",\"label\":\"Data Template: Research Fellows\",\"type\":\"table\",\"columns\":[{\"name\":\"fellow_name\",\"label\":\"Name of Research Fellow\",\"type\":\"text\"},{\"name\":\"year_enroll\",\"label\":\"Year of Enrolment\",\"type\":\"number\"},{\"name\":\"duration\",\"label\":\"Duration\",\"type\":\"text\"},{\"name\":\"type\",\"label\":\"Type (JRF\\/SRF\\/PDF\\/RA)\",\"type\":\"text\"},{\"name\":\"agency\",\"label\":\"Granting Agency\",\"type\":\"text\"}]},{\"name\":\"file_3_1_4\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"3.1.5\",\"title\":\"3.1.5: Institution Facilities\",\"description\":\"Institution has Central Instrumentation Centre, Animal House, Museum, Media laboratory, Business Lab, etc.\",\"fields\":[{\"name\":\"facilities_list\",\"label\":\"Facilities available (Check all that apply):\",\"type\":\"checkbox_group\",\"options\":[{\"name\":\"fac_cic\",\"label\":\"Central Instrumentation Centre\"},{\"name\":\"fac_animal\",\"label\":\"Animal House\\/Green House\"},{\"name\":\"fac_museum\",\"label\":\"Museum\"},{\"name\":\"fac_media\",\"label\":\"Media laboratory\\/Studios\"},{\"name\":\"fac_business\",\"label\":\"Business Lab\"}]},{\"name\":\"file_3_1_5\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"3.1.6\",\"title\":\"3.1.6: Departmental Research Schemes\",\"description\":\"Percentage of departments with UGC-SAP, CAS, DST-FIST, DBT, ICSSR etc.\",\"fields\":[{\"name\":\"qnm_3_1_6_depts\",\"label\":\"Number of Departments with schemes (Numerator)\",\"type\":\"number\"},{\"name\":\"qnm_3_1_6_total\",\"label\":\"Total number of Departments (Denominator)\",\"type\":\"number\"},{\"name\":\"table_3_1_6\",\"label\":\"Data Template: Departmental Schemes\",\"type\":\"table\",\"columns\":[{\"name\":\"dept_name\",\"label\":\"Name of Department\",\"type\":\"text\"},{\"name\":\"scheme_name\",\"label\":\"Name of Scheme\",\"type\":\"text\"},{\"name\":\"funding_agency\",\"label\":\"Funding Agency\",\"type\":\"text\"},{\"name\":\"year_award\",\"label\":\"Year of Award\",\"type\":\"number\"},{\"name\":\"funds\",\"label\":\"Funds Provided (Lakhs)\",\"type\":\"number\"}]},{\"name\":\"file_3_1_6\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]}]},{\"title\":\"3.2 Resource Mobilization for Research\",\"metrics\":[{\"id\":\"3.2.1\",\"title\":\"3.2.1: Extramural funding for Research\",\"description\":\"Extramural funding for Research (Grants in INR Lakhs).\",\"fields\":[{\"name\":\"qnm_3_2_1_funds\",\"label\":\"Total Extramural Funding (in Lakhs)\",\"type\":\"number\"},{\"name\":\"table_3_2_1\",\"label\":\"Data Template: Extramural Funding\",\"type\":\"table\",\"columns\":[{\"name\":\"pi_name\",\"label\":\"Name of PI\\/Co-PI\",\"type\":\"text\"},{\"name\":\"proj_title\",\"label\":\"Title of Project\",\"type\":\"text\"},{\"name\":\"funding_agency\",\"label\":\"Funding Agency\",\"type\":\"text\"},{\"name\":\"dept\",\"label\":\"Department\",\"type\":\"text\"},{\"name\":\"year_award\",\"label\":\"Year of Award\",\"type\":\"number\"},{\"name\":\"funds\",\"label\":\"Funds Provided (Lakhs)\",\"type\":\"number\"},{\"name\":\"duration\",\"label\":\"Duration (Years)\",\"type\":\"number\"}]},{\"name\":\"file_3_2_1\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"3.2.2\",\"title\":\"3.2.2: Grants for research projects\",\"description\":\"Grants for research projects sponsored by the government agencies.\",\"fields\":[{\"name\":\"qnm_3_2_2_grants\",\"label\":\"Total Grants for Research Projects (in Lakhs)\",\"type\":\"number\"},{\"name\":\"table_3_2_2\",\"label\":\"Data Template: Government Grants\",\"type\":\"table\",\"columns\":[{\"name\":\"pi_name\",\"label\":\"Name of PI\\/Co-PI\",\"type\":\"text\"},{\"name\":\"proj_title\",\"label\":\"Title of Project\",\"type\":\"text\"},{\"name\":\"funding_agency\",\"label\":\"Funding Agency\",\"type\":\"text\"},{\"name\":\"dept\",\"label\":\"Department\",\"type\":\"text\"},{\"name\":\"year_award\",\"label\":\"Year of Award\",\"type\":\"number\"},{\"name\":\"funds\",\"label\":\"Funds Provided (Lakhs)\",\"type\":\"number\"},{\"name\":\"duration\",\"label\":\"Duration (Years)\",\"type\":\"number\"}]},{\"name\":\"file_3_2_2\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"3.2.3\",\"title\":\"3.2.3: Research projects per teacher\",\"description\":\"Number of research projects per teacher funded by government and non-government agencies.\",\"fields\":[{\"name\":\"qnm_3_2_3_projects\",\"label\":\"Number of research projects (Numerator)\",\"type\":\"number\"},{\"name\":\"qnm_3_2_3_teachers\",\"label\":\"Total number of full time teachers (Denominator)\",\"type\":\"number\"},{\"name\":\"table_3_2_3\",\"label\":\"Data Template: Projects Per Teacher\",\"type\":\"table\",\"columns\":[{\"name\":\"pi_name\",\"label\":\"Name of PI\\/Co-PI\",\"type\":\"text\"},{\"name\":\"proj_title\",\"label\":\"Title of Project\",\"type\":\"text\"},{\"name\":\"funding_agency\",\"label\":\"Funding Agency\",\"type\":\"text\"},{\"name\":\"dept\",\"label\":\"Department\",\"type\":\"text\"},{\"name\":\"year_award\",\"label\":\"Year of Award\",\"type\":\"number\"},{\"name\":\"funds\",\"label\":\"Funds Provided (Lakhs)\",\"type\":\"number\"},{\"name\":\"duration\",\"label\":\"Duration (Years)\",\"type\":\"number\"}]},{\"name\":\"file_3_2_3\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]}]},{\"title\":\"3.3 Innovation Ecosystem\",\"metrics\":[{\"id\":\"3.3.1\",\"title\":\"3.3.1: Innovation ecosystem\",\"description\":\"Institution has created an eco system for innovations including Incubation centre.\",\"fields\":[{\"name\":\"qlm_3_3_1_text\",\"label\":\"Description\",\"type\":\"textarea\"},{\"name\":\"file_3_3_1\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"3.3.2\",\"title\":\"3.3.2: Workshops\\/seminars on Research\\/IPR\",\"description\":\"Number of workshops\\/seminars conducted on Research methodology, IPR, entrepreneurship.\",\"fields\":[{\"name\":\"qnm_3_3_2_count\",\"label\":\"Number of workshops\\/seminars\",\"type\":\"number\"},{\"name\":\"table_3_3_2\",\"label\":\"Data Template: Workshops\\/Seminars\",\"type\":\"table\",\"columns\":[{\"name\":\"workshop_name\",\"label\":\"Name of Workshop\",\"type\":\"text\"},{\"name\":\"participants\",\"label\":\"Number of Participants\",\"type\":\"number\"},{\"name\":\"date_range\",\"label\":\"Date (From - To)\",\"type\":\"text\"}]},{\"name\":\"file_3_3_2\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"3.3.3\",\"title\":\"3.3.3: Innovation Awards\",\"description\":\"Number of awards \\/ recognitions received for research\\/innovations.\",\"fields\":[{\"name\":\"qnm_3_3_3_awards\",\"label\":\"Number of awards\\/recognitions\",\"type\":\"number\"},{\"name\":\"table_3_3_3\",\"label\":\"Data Template: Innovation Awards\",\"type\":\"table\",\"columns\":[{\"name\":\"title_innov\",\"label\":\"Title of Innovation\",\"type\":\"text\"},{\"name\":\"awardee_name\",\"label\":\"Name of Awardee\",\"type\":\"text\"},{\"name\":\"agency_name\",\"label\":\"Awarding Agency\",\"type\":\"text\"},{\"name\":\"year_award\",\"label\":\"Year of Award\",\"type\":\"number\"}]},{\"name\":\"file_3_3_3\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]}]},{\"title\":\"3.4 Research Publications and Awards\",\"metrics\":[{\"id\":\"3.4.1\",\"title\":\"3.4.1: Code of Ethics\",\"description\":\"The Institution ensures implementation of its stated Code of Ethics for research.\",\"fields\":[{\"name\":\"ethics_list\",\"label\":\"Options (Check all that apply):\",\"type\":\"checkbox_group\",\"options\":[{\"name\":\"eth_comm\",\"label\":\"Ethics Committee exists\"},{\"name\":\"eth_plag\",\"label\":\"Plagiarism check software\"},{\"name\":\"eth_comm_meets\",\"label\":\"Research Advisory Committee meets\"}]},{\"name\":\"file_3_4_1\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"3.4.2\",\"title\":\"3.4.2: Teacher Incentives\",\"description\":\"The institution provides incentives to teachers who receive state, national and international recognitions.\",\"fields\":[{\"name\":\"incentives_list\",\"label\":\"Incentives (Check all that apply):\",\"type\":\"checkbox_group\",\"options\":[{\"name\":\"inc_salary\",\"label\":\"Commendation & monetary reward\"},{\"name\":\"inc_cert\",\"label\":\"Certificate of honor\"}]},{\"name\":\"file_3_4_2\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"3.4.3\",\"title\":\"3.4.3: Patents\",\"description\":\"Number of Patents published\\/awarded.\",\"fields\":[{\"name\":\"qnm_3_4_3_patents\",\"label\":\"Number of Patents\",\"type\":\"number\"},{\"name\":\"table_3_4_3\",\"label\":\"Data Template: Patents\",\"type\":\"table\",\"columns\":[{\"name\":\"patenter_name\",\"label\":\"Name of Patenter\",\"type\":\"text\"},{\"name\":\"patent_num\",\"label\":\"Patent Number\",\"type\":\"text\"},{\"name\":\"patent_title\",\"label\":\"Title of Patent\",\"type\":\"text\"},{\"name\":\"year_award\",\"label\":\"Year of Award\",\"type\":\"number\"}]},{\"name\":\"file_3_4_3\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"3.4.4\",\"title\":\"3.4.4: Ph.D\'s awarded per teacher\",\"description\":\"Number of Ph.D\'s awarded per teacher.\",\"fields\":[{\"name\":\"qnm_3_4_4_phd\",\"label\":\"Number of Ph.D\'s awarded (Numerator)\",\"type\":\"number\"},{\"name\":\"qnm_3_4_4_guides\",\"label\":\"Number of recognized guides (Denominator)\",\"type\":\"number\"},{\"name\":\"table_3_4_4\",\"label\":\"Data Template: Ph.D.s Awarded\",\"type\":\"table\",\"columns\":[{\"name\":\"scholar_name\",\"label\":\"Name of Scholar\",\"type\":\"text\"},{\"name\":\"dept\",\"label\":\"Department\",\"type\":\"text\"},{\"name\":\"guide_name\",\"label\":\"Name of Guide\",\"type\":\"text\"},{\"name\":\"thesis_title\",\"label\":\"Title of Thesis\",\"type\":\"text\"},{\"name\":\"year_reg\",\"label\":\"Year of Registration\",\"type\":\"number\"},{\"name\":\"year_award\",\"label\":\"Year of Award\",\"type\":\"number\"}]},{\"name\":\"file_3_4_4\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"3.4.5\",\"title\":\"3.4.5: Research Papers in UGC Journals\",\"description\":\"Number of research papers per teacher in the Journals notified on UGC website.\",\"fields\":[{\"name\":\"qnm_3_4_5_papers\",\"label\":\"Number of Research Papers (Numerator)\",\"type\":\"number\"},{\"name\":\"qnm_3_4_5_teachers\",\"label\":\"Number of Full-Time Teachers (Denominator)\",\"type\":\"number\"},{\"name\":\"table_3_4_5\",\"label\":\"Data Template: Research Papers\",\"type\":\"table\",\"columns\":[{\"name\":\"paper_title\",\"label\":\"Title of Paper\",\"type\":\"text\"},{\"name\":\"author_name\",\"label\":\"Name of Author\",\"type\":\"text\"},{\"name\":\"dept\",\"label\":\"Department\",\"type\":\"text\"},{\"name\":\"journal_name\",\"label\":\"Journal Name\",\"type\":\"text\"},{\"name\":\"year_pub\",\"label\":\"Year of Publication\",\"type\":\"number\"},{\"name\":\"issn\",\"label\":\"ISSN Number\",\"type\":\"text\"}]},{\"name\":\"file_3_4_5\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"3.4.6\",\"title\":\"3.4.6: Books and Chapters published\",\"description\":\"Number of books and chapters in edited volumes\\/books published.\",\"fields\":[{\"name\":\"qnm_3_4_6_books\",\"label\":\"Number of books and chapters\",\"type\":\"number\"},{\"name\":\"table_3_4_6\",\"label\":\"Data Template: Books and Chapters\",\"type\":\"table\",\"columns\":[{\"name\":\"book_title\",\"label\":\"Title of Book\\/Chapter\",\"type\":\"text\"},{\"name\":\"author_name\",\"label\":\"Author\",\"type\":\"text\"},{\"name\":\"proceedings\",\"label\":\"Title of proceedings\",\"type\":\"text\"},{\"name\":\"publisher\",\"label\":\"Publisher Name\",\"type\":\"text\"},{\"name\":\"isbn\",\"label\":\"ISBN\",\"type\":\"text\"},{\"name\":\"year_pub\",\"label\":\"Year of Publication\",\"type\":\"number\"}]},{\"name\":\"file_3_4_6\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"3.4.7\",\"title\":\"3.4.7: E-content developed by teachers\",\"description\":\"E-content developed by teachers for platforms like NMEICT\\/NPTEL\\/SWAYAM.\",\"fields\":[{\"name\":\"qnm_3_4_7_content\",\"label\":\"Number of E-contents developed\",\"type\":\"number\"},{\"name\":\"table_3_4_7\",\"label\":\"Data Template: E-Content\",\"type\":\"table\",\"columns\":[{\"name\":\"teacher_name\",\"label\":\"Name of Teacher\",\"type\":\"text\"},{\"name\":\"module_name\",\"label\":\"Name of Module\",\"type\":\"text\"},{\"name\":\"platform\",\"label\":\"Platform (SWAYAM etc)\",\"type\":\"text\"},{\"name\":\"date_launch\",\"label\":\"Date of Launch\",\"type\":\"text\"}]},{\"name\":\"file_3_4_7\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"3.4.8\",\"title\":\"3.4.8: Bibliometrics (Citation Index)\",\"description\":\"Bibliometrics of the publications based on average Citation Index in Scopus\\/ Web of Science.\",\"fields\":[{\"name\":\"qnm_3_4_8_citation\",\"label\":\"Average Citation Index\",\"type\":\"number\"},{\"name\":\"file_3_4_8\",\"label\":\"Supporting Document (Citation Index)\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"3.4.9\",\"title\":\"3.4.9: Bibliometrics (h-index)\",\"description\":\"Bibliometrics of the publications based on Scopus\\/ Web of Science - h-index.\",\"fields\":[{\"name\":\"qnm_3_4_9_hindex\",\"label\":\"h-index of the institution\",\"type\":\"number\"},{\"name\":\"file_3_4_9\",\"label\":\"Supporting Document (h-index)\",\"type\":\"file\",\"accept\":\".pdf\"}]}]},{\"title\":\"3.5 Consultancy\",\"metrics\":[{\"id\":\"3.5.1\",\"title\":\"3.5.1: Consultancy Policy\",\"description\":\"Institution has a policy on consultancy including revenue sharing.\",\"fields\":[{\"name\":\"qlm_3_5_1_text\",\"label\":\"Description\",\"type\":\"textarea\"},{\"name\":\"file_3_5_1\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"3.5.2\",\"title\":\"3.5.2: Revenue from Consultancy\",\"description\":\"Revenue generated from consultancy and corporate training.\",\"fields\":[{\"name\":\"qnm_3_5_2_revenue\",\"label\":\"Total Revenue Generated (in Lakhs)\",\"type\":\"number\"},{\"name\":\"table_3_5_2\",\"label\":\"Data Template: Consultancy Revenue\",\"type\":\"table\",\"columns\":[{\"name\":\"consultant_name\",\"label\":\"Name of Consultant\",\"type\":\"text\"},{\"name\":\"project_name\",\"label\":\"Name of Project\",\"type\":\"text\"},{\"name\":\"agency_name\",\"label\":\"Sponsoring Agency\",\"type\":\"text\"},{\"name\":\"revenue\",\"label\":\"Revenue (Lakhs)\",\"type\":\"number\"}]},{\"name\":\"file_3_5_2\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]}]},{\"title\":\"3.6 Extension Activities\",\"metrics\":[{\"id\":\"3.6.1\",\"title\":\"3.6.1: Extension activities in neighbourhood\",\"description\":\"Extension activities in the neighbourhood community in terms of impact.\",\"fields\":[{\"name\":\"qlm_3_6_1_text\",\"label\":\"Description\",\"type\":\"textarea\"},{\"name\":\"file_3_6_1\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"3.6.2\",\"title\":\"3.6.2: Extension Awards\",\"description\":\"Number of awards received by the Institution, its teachers and students for extension activities.\",\"fields\":[{\"name\":\"qnm_3_6_2_awards\",\"label\":\"Number of awards\",\"type\":\"number\"},{\"name\":\"table_3_6_2\",\"label\":\"Data Template: Extension Awards\",\"type\":\"table\",\"columns\":[{\"name\":\"activity_name\",\"label\":\"Name of Activity\",\"type\":\"text\"},{\"name\":\"award_name\",\"label\":\"Name of Award\",\"type\":\"text\"},{\"name\":\"awarding_body\",\"label\":\"Awarding Body\",\"type\":\"text\"},{\"name\":\"year\",\"label\":\"Year of Award\",\"type\":\"number\"}]},{\"name\":\"file_3_6_2\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"3.6.3\",\"title\":\"3.6.3: Extension Programs Conducted\",\"description\":\"Number of extension and outreach programs conducted in collaboration with industry, community and Non-Government Organisations.\",\"fields\":[{\"name\":\"qnm_3_6_3_programs\",\"label\":\"Number of programs conducted\",\"type\":\"number\"},{\"name\":\"table_3_6_3\",\"label\":\"Data Template: Extension Programs\",\"type\":\"table\",\"columns\":[{\"name\":\"activity_name\",\"label\":\"Name of Activity\",\"type\":\"text\"},{\"name\":\"organising_unit\",\"label\":\"Organising Unit\\/Agency\",\"type\":\"text\"},{\"name\":\"scheme_name\",\"label\":\"Name of Scheme\",\"type\":\"text\"},{\"name\":\"year\",\"label\":\"Year\",\"type\":\"number\"}]},{\"name\":\"file_3_6_3\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"3.6.4\",\"title\":\"3.6.4: Student Participation in Extension\",\"description\":\"Average percentage of students participating in extension activities listed at 3.6.3 above.\",\"fields\":[{\"name\":\"qnm_3_6_4_participating\",\"label\":\"Number of students participating (Numerator)\",\"type\":\"number\"},{\"name\":\"qnm_3_6_4_total\",\"label\":\"Total number of students (Denominator)\",\"type\":\"number\"},{\"name\":\"table_3_6_4\",\"label\":\"Data Template: Student Participation\",\"type\":\"table\",\"columns\":[{\"name\":\"activity_name\",\"label\":\"Name of Activity\",\"type\":\"text\"},{\"name\":\"organising_unit\",\"label\":\"Organising Unit\\/Agency\",\"type\":\"text\"},{\"name\":\"scheme_name\",\"label\":\"Name of Scheme\",\"type\":\"text\"},{\"name\":\"year\",\"label\":\"Year\",\"type\":\"number\"},{\"name\":\"student_count\",\"label\":\"Number of Students Participated\",\"type\":\"number\"}]},{\"name\":\"file_3_6_4\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]}]},{\"title\":\"3.7 Collaboration\",\"metrics\":[{\"id\":\"3.7.1\",\"title\":\"3.7.1: Collaborative activities\",\"description\":\"Number of collaborative activities for research, Faculty exchange, Student exchange.\",\"fields\":[{\"name\":\"qnm_3_7_1_collabs\",\"label\":\"Number of collaborative activities\",\"type\":\"number\"},{\"name\":\"table_3_7_1\",\"label\":\"Data Template: Collaborations\",\"type\":\"table\",\"columns\":[{\"name\":\"title_collab\",\"label\":\"Title of Collaboration\",\"type\":\"text\"},{\"name\":\"agency_name\",\"label\":\"Collaborating Agency\",\"type\":\"text\"},{\"name\":\"fin_support\",\"label\":\"Financial Support Source\",\"type\":\"text\"},{\"name\":\"year\",\"label\":\"Year of Collaboration\",\"type\":\"number\"},{\"name\":\"duration\",\"label\":\"Duration\",\"type\":\"text\"}]},{\"name\":\"file_3_7_1\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"3.7.2\",\"title\":\"3.7.2: Functional MoUs\",\"description\":\"Number of functional MoUs with institutions, industries, corporate houses etc.\",\"fields\":[{\"name\":\"qnm_3_7_2_mous\",\"label\":\"Number of functional MoUs\",\"type\":\"number\"},{\"name\":\"table_3_7_2\",\"label\":\"Data Template: Functional MoUs\",\"type\":\"table\",\"columns\":[{\"name\":\"org_name\",\"label\":\"Organisation with MoU\",\"type\":\"text\"},{\"name\":\"inst_name\",\"label\":\"Name of Institution\\/Industry\",\"type\":\"text\"},{\"name\":\"year_sign\",\"label\":\"Year of Signing\",\"type\":\"number\"},{\"name\":\"duration\",\"label\":\"Duration\",\"type\":\"text\"},{\"name\":\"activities_list\",\"label\":\"List of Activities\",\"type\":\"text\"}]},{\"name\":\"file_3_7_2\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]}]}]}', '2026-10-07 17:00:48'),
(11, 4, 'v4_university_with_tables', 0, '{\"sections\":[{\"title\":\"4.1 Physical Facilities\",\"metrics\":[{\"id\":\"4.1.1\",\"title\":\"4.1.1: Facilities for teaching-learning\",\"description\":\"The institution has adequate facilities for teaching- learning. viz., classrooms, laboratories, computing equipment, etc.\",\"fields\":[{\"name\":\"qlm_4_1_1_text\",\"label\":\"Description\",\"type\":\"textarea\"},{\"name\":\"file_4_1_1\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"4.1.2\",\"title\":\"4.1.2: Facilities for sports and cultural activities\",\"description\":\"The institution has adequate facilities for cultural activities, yoga, games (indoor, outdoor) and sports.\",\"fields\":[{\"name\":\"qlm_4_1_2_text\",\"label\":\"Description\",\"type\":\"textarea\"},{\"name\":\"file_4_1_2\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"4.1.3\",\"title\":\"4.1.3: ICT-enabled classrooms\",\"description\":\"Percentage of classrooms and seminar halls with ICT- enabled facilities such as smart class, LMS, etc.\",\"fields\":[{\"name\":\"qnm_4_1_3_ict\",\"label\":\"Number of classrooms and seminar halls with ICT facilities (Numerator)\",\"type\":\"number\"},{\"name\":\"qnm_4_1_3_total\",\"label\":\"Total number of classrooms and seminar halls in the institution (Denominator)\",\"type\":\"number\"},{\"name\":\"table_4_1_3\",\"label\":\"Data Template: ICT Classrooms\",\"type\":\"table\",\"columns\":[{\"name\":\"room_no\",\"label\":\"Room number or Name of classrooms\\/Seminar Hall\",\"type\":\"text\"},{\"name\":\"facility_type\",\"label\":\"Type of ICT facility\",\"type\":\"text\"}]},{\"name\":\"file_4_1_3\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"4.1.4\",\"title\":\"4.1.4: Expenditure for infrastructure augmentation\",\"description\":\"Average percentage of expenditure, excluding salary for infrastructure augmentation during last five years.\",\"fields\":[{\"name\":\"qnm_4_1_4_aug\",\"label\":\"Expenditure for infrastructure augmentation (Numerator) (Lakhs)\",\"type\":\"number\"},{\"name\":\"qnm_4_1_4_total\",\"label\":\"Total expenditure excluding salary (Denominator) (Lakhs)\",\"type\":\"number\"},{\"name\":\"table_4_1_4\",\"label\":\"Data Template: Infrastructure Expenditure\",\"type\":\"table\",\"columns\":[{\"name\":\"year\",\"label\":\"Year\",\"type\":\"number\"},{\"name\":\"budget_infra\",\"label\":\"Budget allocated for infra (Lakhs)\",\"type\":\"number\"},{\"name\":\"exp_infra\",\"label\":\"Exp for infra (Lakhs)\",\"type\":\"number\"},{\"name\":\"total_exp\",\"label\":\"Total exp excluding salary (Lakhs)\",\"type\":\"number\"}]},{\"name\":\"file_4_1_4\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]}]},{\"title\":\"4.2 Library as a Learning Resource\",\"metrics\":[{\"id\":\"4.2.1\",\"title\":\"4.2.1: Library automation (ILMS)\",\"description\":\"Library is automated using Integrated Library Management System (ILMS).\",\"fields\":[{\"name\":\"qlm_4_2_1_text\",\"label\":\"Description\",\"type\":\"textarea\"},{\"name\":\"file_4_2_1\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"4.2.2\",\"title\":\"4.2.2: E-resources subscription\",\"description\":\"The institution has subscription for the following e-resources.\",\"fields\":[{\"name\":\"eresources_list\",\"label\":\"Options (Check all that apply):\",\"type\":\"checkbox_group\",\"options\":[{\"name\":\"e_journals\",\"label\":\"1. e-journals\"},{\"name\":\"e_shodh\",\"label\":\"2. e-ShodhSindhu\"},{\"name\":\"e_shodhganga\",\"label\":\"3. Shodhganga Membership\"},{\"name\":\"e_books\",\"label\":\"4. e-books\"},{\"name\":\"e_databases\",\"label\":\"5. Databases\"},{\"name\":\"e_remote\",\"label\":\"6. Remote access to e-resources\"}]},{\"name\":\"file_4_2_2\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"4.2.3\",\"title\":\"4.2.3: Expenditure on books and journals\",\"description\":\"Average annual expenditure for purchase of books\\/e-books and subscription to journals\\/e-journals during the last five years.\",\"fields\":[{\"name\":\"qnm_4_2_3_exp\",\"label\":\"Annual expenditure on books and journals (Lakhs)\",\"type\":\"number\"},{\"name\":\"table_4_2_3\",\"label\":\"Data Template: Books & Journals Expenditure\",\"type\":\"table\",\"columns\":[{\"name\":\"year\",\"label\":\"Year\",\"type\":\"number\"},{\"name\":\"exp_books\",\"label\":\"Exp on books\\/e-books (Lakhs)\",\"type\":\"number\"},{\"name\":\"exp_journals\",\"label\":\"Exp on journals\\/e-journals (Lakhs)\",\"type\":\"number\"}]},{\"name\":\"file_4_2_3\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"4.2.4\",\"title\":\"4.2.4: Library usage per day\",\"description\":\"Percentage per day usage of library by teachers and students.\",\"fields\":[{\"name\":\"qnm_4_2_4_usage\",\"label\":\"Number of teachers and students using library per day (Numerator)\",\"type\":\"number\"},{\"name\":\"qnm_4_2_4_total\",\"label\":\"Total number of teachers and students (Denominator)\",\"type\":\"number\"},{\"name\":\"file_4_2_4\",\"label\":\"Supporting Document (e.g. Method of computing per day usage)\",\"type\":\"file\",\"accept\":\".pdf\"}]}]},{\"title\":\"4.3 IT Infrastructure\",\"metrics\":[{\"id\":\"4.3.1\",\"title\":\"4.3.1: IT updates and bandwidth\",\"description\":\"Institution frequently updates its IT facilities and provides sufficient bandwidth for internet connection.\",\"fields\":[{\"name\":\"qlm_4_3_1_text\",\"label\":\"Description\",\"type\":\"textarea\"},{\"name\":\"file_4_3_1\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"4.3.2\",\"title\":\"4.3.2: Student - Computer ratio\",\"description\":\"Student \\u2013 Computer ratio.\",\"fields\":[{\"name\":\"qnm_4_3_2_students\",\"label\":\"Total number of students (Numerator)\",\"type\":\"number\"},{\"name\":\"qnm_4_3_2_computers\",\"label\":\"Total number of computers in working condition (Denominator)\",\"type\":\"number\"},{\"name\":\"file_4_3_2\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"4.3.3\",\"title\":\"4.3.3: Internet Bandwidth\",\"description\":\"Bandwidth of internet connection in the Institution.\",\"fields\":[{\"name\":\"bandwidth_opts\",\"label\":\"Select available bandwidth (Check one):\",\"type\":\"checkbox_group\",\"options\":[{\"name\":\"bw_1gbps\",\"label\":\"\\u2265 1 GBPS\"},{\"name\":\"bw_500mbps\",\"label\":\"500 MBPS - 1 GBPS\"},{\"name\":\"bw_250mbps\",\"label\":\"250 MBPS - 500 MBPS\"},{\"name\":\"bw_50mbps\",\"label\":\"50 MBPS - 250 MBPS\"},{\"name\":\"bw_less50\",\"label\":\"< 50 MBPS\"}]},{\"name\":\"file_4_3_3\",\"label\":\"Supporting Document (Bills\\/Agreements)\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"4.3.4\",\"title\":\"4.3.4: E-content development facilities\",\"description\":\"Facilities for e-content development such as Media centre, Audio visual centre, Lecture Capturing System (LCS).\",\"fields\":[{\"name\":\"econtent_facs\",\"label\":\"Available facilities (Check all that apply):\",\"type\":\"checkbox_group\",\"options\":[{\"name\":\"ec_media\",\"label\":\"1. Media centre\"},{\"name\":\"ec_audio\",\"label\":\"2. Audio visual centre\"},{\"name\":\"ec_lecture\",\"label\":\"3. Lecture Capturing System(LCS)\"},{\"name\":\"ec_mixing\",\"label\":\"4. Mixing equipments and softwares for editing\"}]},{\"name\":\"file_4_3_4\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]}]},{\"title\":\"4.4 Maintenance of Campus Infrastructure\",\"metrics\":[{\"id\":\"4.4.1\",\"title\":\"4.4.1: Expenditure on maintenance\",\"description\":\"Average percentage of expenditure incurred on maintenance of infrastructure (physical and academic support facilities) excluding salary component.\",\"fields\":[{\"name\":\"qnm_4_4_1_maint\",\"label\":\"Expenditure on maintenance of infra (Numerator) (Lakhs)\",\"type\":\"number\"},{\"name\":\"qnm_4_4_1_total\",\"label\":\"Total expenditure excluding salary (Denominator) (Lakhs)\",\"type\":\"number\"},{\"name\":\"table_4_4_1\",\"label\":\"Data Template: Maintenance Expenditure\",\"type\":\"table\",\"columns\":[{\"name\":\"year\",\"label\":\"Year\",\"type\":\"number\"},{\"name\":\"budget_acad\",\"label\":\"Assigned budget on academic facilities (Lakhs)\",\"type\":\"number\"},{\"name\":\"exp_acad\",\"label\":\"Exp on academic facilities (Lakhs)\",\"type\":\"number\"},{\"name\":\"budget_phys\",\"label\":\"Assigned budget on physical facilities (Lakhs)\",\"type\":\"number\"},{\"name\":\"exp_phys\",\"label\":\"Exp on physical facilities (Lakhs)\",\"type\":\"number\"}]},{\"name\":\"file_4_4_1\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"4.4.2\",\"title\":\"4.4.2: Maintenance systems and procedures\",\"description\":\"There are established systems and procedures for maintaining and utilizing physical, academic and support facilities.\",\"fields\":[{\"name\":\"qlm_4_4_2_text\",\"label\":\"Description\",\"type\":\"textarea\"},{\"name\":\"qlm_4_4_2_url\",\"label\":\"URL for policy document\",\"type\":\"url\"},{\"name\":\"file_4_4_2\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]}]}]}', '2026-10-07 17:04:31'),
(12, 4, 'v4_university_with_tables', 1, '{\"sections\":[{\"title\":\"4.1 Physical Facilities\",\"metrics\":[{\"id\":\"4.1.1\",\"title\":\"4.1.1: Facilities for teaching-learning\",\"description\":\"The institution has adequate facilities for teaching- learning. viz., classrooms, laboratories, computing equipment, etc.\",\"fields\":[{\"name\":\"qlm_4_1_1_text\",\"label\":\"Description\",\"type\":\"textarea\"},{\"name\":\"file_4_1_1\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"4.1.2\",\"title\":\"4.1.2: Facilities for sports and cultural activities\",\"description\":\"The institution has adequate facilities for cultural activities, yoga, games (indoor, outdoor) and sports.\",\"fields\":[{\"name\":\"qlm_4_1_2_text\",\"label\":\"Description\",\"type\":\"textarea\"},{\"name\":\"file_4_1_2\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"4.1.3\",\"title\":\"4.1.3: General campus facilities\",\"description\":\"Availability of general campus facilities and overall ambience.\",\"fields\":[{\"name\":\"qlm_4_1_3_text\",\"label\":\"Description\",\"type\":\"textarea\"},{\"name\":\"file_4_1_3\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"4.1.4\",\"title\":\"4.1.4: Expenditure for infrastructure augmentation\",\"description\":\"Average percentage of expenditure excluding salary, for infrastructure augmentation during the last five years.\",\"fields\":[{\"name\":\"qnm_4_1_4_aug\",\"label\":\"Expenditure for infrastructure augmentation (Numerator) (Lakhs)\",\"type\":\"number\"},{\"name\":\"qnm_4_1_4_total\",\"label\":\"Total expenditure excluding salary (Denominator) (Lakhs)\",\"type\":\"number\"},{\"name\":\"table_4_1_4\",\"label\":\"Data Template: Infrastructure Expenditure\",\"type\":\"table\",\"columns\":[{\"name\":\"year\",\"label\":\"Year\",\"type\":\"number\"},{\"name\":\"budget_infra\",\"label\":\"Budget allocated for infra (Lakhs)\",\"type\":\"number\"},{\"name\":\"exp_infra\",\"label\":\"Exp for infra (Lakhs)\",\"type\":\"number\"},{\"name\":\"total_exp\",\"label\":\"Total exp excluding salary (Lakhs)\",\"type\":\"number\"}]},{\"name\":\"file_4_1_4\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]}]},{\"title\":\"4.2 Library as a Learning Resource\",\"metrics\":[{\"id\":\"4.2.1\",\"title\":\"4.2.1: Library automation (ILMS)\",\"description\":\"Library is automated using Integrated Library Management System (ILMS).\",\"fields\":[{\"name\":\"qlm_4_2_1_text\",\"label\":\"Description\",\"type\":\"textarea\"},{\"name\":\"file_4_2_1\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"4.2.2\",\"title\":\"4.2.2: E-resources subscription\",\"description\":\"The institution has subscription for the following e-resources.\",\"fields\":[{\"name\":\"eresources_list\",\"label\":\"Options (Check all that apply):\",\"type\":\"checkbox_group\",\"options\":[{\"name\":\"e_journals\",\"label\":\"1. e-journals\"},{\"name\":\"e_shodh\",\"label\":\"2. e-ShodhSindhu\"},{\"name\":\"e_shodhganga\",\"label\":\"3. Shodhganga Membership\"},{\"name\":\"e_books\",\"label\":\"4. e-books\"},{\"name\":\"e_databases\",\"label\":\"5. Databases\"},{\"name\":\"e_remote\",\"label\":\"6. Remote access to e-resources\"}]},{\"name\":\"file_4_2_2\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"4.2.3\",\"title\":\"4.2.3: Expenditure on books and journals\",\"description\":\"Average annual expenditure for purchase of books\\/e-books and subscription to journals\\/e-journals during the last five years.\",\"fields\":[{\"name\":\"qnm_4_2_3_exp\",\"label\":\"Annual expenditure on books and journals (Lakhs)\",\"type\":\"number\"},{\"name\":\"table_4_2_3\",\"label\":\"Data Template: Books & Journals Expenditure\",\"type\":\"table\",\"columns\":[{\"name\":\"year\",\"label\":\"Year\",\"type\":\"number\"},{\"name\":\"exp_books\",\"label\":\"Exp on books\\/e-books (Lakhs)\",\"type\":\"number\"},{\"name\":\"exp_journals\",\"label\":\"Exp on journals\\/e-journals (Lakhs)\",\"type\":\"number\"}]},{\"name\":\"file_4_2_3\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"4.2.4\",\"title\":\"4.2.4: Library usage per day\",\"description\":\"Percentage per day usage of library by teachers and students.\",\"fields\":[{\"name\":\"qnm_4_2_4_usage\",\"label\":\"Number of teachers and students using library per day (Numerator)\",\"type\":\"number\"},{\"name\":\"qnm_4_2_4_total\",\"label\":\"Total number of teachers and students (Denominator)\",\"type\":\"number\"},{\"name\":\"file_4_2_4\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]}]},{\"title\":\"4.3 IT Infrastructure\",\"metrics\":[{\"id\":\"4.3.1\",\"title\":\"4.3.1: ICT-enabled classrooms\",\"description\":\"Percentage of classrooms and seminar halls with ICT- enabled facilities such as LCD, smart board, Wi-Fi\\/LAN.\",\"fields\":[{\"name\":\"qnm_4_3_1_ict\",\"label\":\"Number of classrooms and seminar halls with ICT facilities (Numerator)\",\"type\":\"number\"},{\"name\":\"qnm_4_3_1_total\",\"label\":\"Total number of classrooms and seminar halls (Denominator)\",\"type\":\"number\"},{\"name\":\"table_4_3_1\",\"label\":\"Data Template: ICT Classrooms\",\"type\":\"table\",\"columns\":[{\"name\":\"room_no\",\"label\":\"Room number or Name of classrooms\\/Seminar Hall\",\"type\":\"text\"},{\"name\":\"facility_type\",\"label\":\"Type of ICT facility\",\"type\":\"text\"}]},{\"name\":\"file_4_3_1\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"4.3.2\",\"title\":\"4.3.2: IT updates and Wi-Fi\",\"description\":\"Institution has an IT policy, makes appropriate budgetary provisions and updates its IT facilities including the Wi-Fi facility.\",\"fields\":[{\"name\":\"qlm_4_3_2_text\",\"label\":\"Description\",\"type\":\"textarea\"},{\"name\":\"file_4_3_2\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"4.3.3\",\"title\":\"4.3.3: Student - Computer ratio\",\"description\":\"Student \\u2013 Computer ratio.\",\"fields\":[{\"name\":\"qnm_4_3_3_students\",\"label\":\"Total number of students (Numerator)\",\"type\":\"number\"},{\"name\":\"qnm_4_3_3_computers\",\"label\":\"Total number of computers in working condition (Denominator)\",\"type\":\"number\"},{\"name\":\"file_4_3_3\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"4.3.4\",\"title\":\"4.3.4: Internet Bandwidth\",\"description\":\"Available bandwidth of internet connection in the Institution.\",\"fields\":[{\"name\":\"bandwidth_opts\",\"label\":\"Select available bandwidth (Check one):\",\"type\":\"checkbox_group\",\"options\":[{\"name\":\"bw_1gbps\",\"label\":\"A. \\u2265 1 GBPS\"},{\"name\":\"bw_500mbps\",\"label\":\"B. 500 MBPS - 1 GBPS\"},{\"name\":\"bw_250mbps\",\"label\":\"C. 250 MBPS - 500 MBPS\"},{\"name\":\"bw_50mbps\",\"label\":\"D. 50 MBPS - 250 MBPS\"},{\"name\":\"bw_less50\",\"label\":\"E. < 50 MBPS\"}]},{\"name\":\"file_4_3_4\",\"label\":\"Supporting Document (Bills\\/Agreements)\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"4.3.5\",\"title\":\"4.3.5: E-content development facilities\",\"description\":\"Institution has the following Facilities for e-content development.\",\"fields\":[{\"name\":\"econtent_facs\",\"label\":\"Available facilities (Check all that apply):\",\"type\":\"checkbox_group\",\"options\":[{\"name\":\"ec_media\",\"label\":\"1. Media centre\"},{\"name\":\"ec_audio\",\"label\":\"2. Audio visual centre\"},{\"name\":\"ec_lecture\",\"label\":\"3. Lecture Capturing System(LCS)\"},{\"name\":\"ec_mixing\",\"label\":\"4. Mixing equipments and softwares for editing\"}]},{\"name\":\"file_4_3_5\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]}]},{\"title\":\"4.4 Maintenance of Campus Infrastructure\",\"metrics\":[{\"id\":\"4.4.1\",\"title\":\"4.4.1: Expenditure on maintenance\",\"description\":\"Average percentage of expenditure incurred on maintenance of physical facilities and academic support facilities excluding salary.\",\"fields\":[{\"name\":\"qnm_4_4_1_maint\",\"label\":\"Expenditure on maintenance (Numerator) (Lakhs)\",\"type\":\"number\"},{\"name\":\"qnm_4_4_1_total\",\"label\":\"Total expenditure excluding salary (Denominator) (Lakhs)\",\"type\":\"number\"},{\"name\":\"table_4_4_1\",\"label\":\"Data Template: Maintenance Expenditure\",\"type\":\"table\",\"columns\":[{\"name\":\"year\",\"label\":\"Year\",\"type\":\"number\"},{\"name\":\"budget_acad\",\"label\":\"Assigned budget on academic facilities (Lakhs)\",\"type\":\"number\"},{\"name\":\"exp_acad\",\"label\":\"Exp on academic facilities (Lakhs)\",\"type\":\"number\"},{\"name\":\"budget_phys\",\"label\":\"Assigned budget on physical facilities (Lakhs)\",\"type\":\"number\"},{\"name\":\"exp_phys\",\"label\":\"Exp on physical facilities (Lakhs)\",\"type\":\"number\"}]},{\"name\":\"file_4_4_1\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"4.4.2\",\"title\":\"4.4.2: Maintenance systems and procedures\",\"description\":\"There are established systems and procedures for maintaining and utilizing physical, academic and support facilities.\",\"fields\":[{\"name\":\"qlm_4_4_2_text\",\"label\":\"Description\",\"type\":\"textarea\"},{\"name\":\"qlm_4_4_2_url\",\"label\":\"URL for policy document\",\"type\":\"url\"},{\"name\":\"file_4_4_2\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]}]}]}', '2026-10-07 17:24:46');
INSERT INTO `naac_form_schemas` (`schema_id`, `criterion_number`, `version_name`, `is_active`, `schema_json`, `created_at`) VALUES
(13, 5, 'v4_university_with_tables', 0, '{\"sections\":[{\"title\":\"5.1 Student Support\",\"metrics\":[{\"id\":\"5.1.1\",\"title\":\"5.1.1: Scholarships and Freeships\",\"description\":\"Average percentage of students benefited by scholarships and freeships provided by the institution, Government and non-government bodies.\",\"fields\":[{\"name\":\"qnm_5_1_1_benefited\",\"label\":\"Number of students benefited (Numerator)\",\"type\":\"number\"},{\"name\":\"qnm_5_1_1_total\",\"label\":\"Total number of students (Denominator)\",\"type\":\"number\"},{\"name\":\"table_5_1_1\",\"label\":\"Data Template: Scholarships\",\"type\":\"table\",\"columns\":[{\"name\":\"year\",\"label\":\"Year\",\"type\":\"number\"},{\"name\":\"scheme_name\",\"label\":\"Name of the scheme\",\"type\":\"text\"},{\"name\":\"num_benefited\",\"label\":\"Number of students benefiting\",\"type\":\"number\"}]},{\"name\":\"file_5_1_1\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"5.1.2\",\"title\":\"5.1.2: Career Counseling and Guidance\",\"description\":\"Average percentage of students benefited by career counseling and guidance for competitive examinations.\",\"fields\":[{\"name\":\"qnm_5_1_2_benefited\",\"label\":\"Number of students benefited (Numerator)\",\"type\":\"number\"},{\"name\":\"qnm_5_1_2_total\",\"label\":\"Total number of students (Denominator)\",\"type\":\"number\"},{\"name\":\"table_5_1_2\",\"label\":\"Data Template: Career Counseling\",\"type\":\"table\",\"columns\":[{\"name\":\"year\",\"label\":\"Year\",\"type\":\"number\"},{\"name\":\"scheme_name\",\"label\":\"Name of the scheme\",\"type\":\"text\"},{\"name\":\"num_passed\",\"label\":\"Students passed in competitive exams\",\"type\":\"number\"},{\"name\":\"num_benefited\",\"label\":\"Number of students benefited\",\"type\":\"number\"}]},{\"name\":\"file_5_1_2\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"5.1.3\",\"title\":\"5.1.3: Capacity Development and Skills Enhancement\",\"description\":\"Following Capacity development and skills enhancement initiatives are undertaken by the institution.\",\"fields\":[{\"name\":\"skills_opts\",\"label\":\"Initiatives undertaken (Check all that apply):\",\"type\":\"checkbox_group\",\"options\":[{\"name\":\"skill_soft\",\"label\":\"1. Soft skills\"},{\"name\":\"skill_lang\",\"label\":\"2. Language and communication skills\"},{\"name\":\"skill_life\",\"label\":\"3. Life skills (Yoga, physical fitness, health and hygiene)\"},{\"name\":\"skill_tech\",\"label\":\"4. Awareness of trends in technology\"}]},{\"name\":\"table_5_1_3\",\"label\":\"Data Template: Skill Enhancement\",\"type\":\"table\",\"columns\":[{\"name\":\"scheme_name\",\"label\":\"Name of the scheme\",\"type\":\"text\"},{\"name\":\"year_impl\",\"label\":\"Year of implementation\",\"type\":\"number\"},{\"name\":\"num_enrolled\",\"label\":\"Number of students enrolled\",\"type\":\"number\"},{\"name\":\"agencies\",\"label\":\"Name of agencies involved\",\"type\":\"text\"}]},{\"name\":\"file_5_1_3\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"5.1.4\",\"title\":\"5.1.4: Student Grievances Redressal\",\"description\":\"The Institution adopts the following for redressal of student grievances including sexual harassment and ragging cases.\",\"fields\":[{\"name\":\"grievance_opts\",\"label\":\"Options (Check all that apply):\",\"type\":\"checkbox_group\",\"options\":[{\"name\":\"gr_guide\",\"label\":\"1. Implementation of guidelines of statutory\\/regulatory bodies\"},{\"name\":\"gr_aware\",\"label\":\"2. Organisation-wide awareness and undertakings on policies\"},{\"name\":\"gr_mech\",\"label\":\"3. Mechanisms for submission of online\\/offline grievances\"},{\"name\":\"gr_timely\",\"label\":\"4. Timely redressal of grievances through appropriate committees\"}]},{\"name\":\"file_5_1_4\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]}]},{\"title\":\"5.2 Student Progression\",\"metrics\":[{\"id\":\"5.2.1\",\"title\":\"5.2.1: Qualifying in State\\/National\\/International Exams\",\"description\":\"Average percentage of students qualifying in state\\/ national\\/ international level examinations.\",\"fields\":[{\"name\":\"qnm_5_2_1_qual\",\"label\":\"Number of students qualifying (Numerator)\",\"type\":\"number\"},{\"name\":\"qnm_5_2_1_appear\",\"label\":\"Number of students appeared (Denominator)\",\"type\":\"number\"},{\"name\":\"table_5_2_1\",\"label\":\"Data Template: Competitive Exams\",\"type\":\"table\",\"columns\":[{\"name\":\"year\",\"label\":\"Year\",\"type\":\"number\"},{\"name\":\"exam_name\",\"label\":\"Exam (NET\\/SLET\\/GATE\\/etc)\",\"type\":\"text\"},{\"name\":\"num_selected\",\"label\":\"Number of students selected\",\"type\":\"number\"}]},{\"name\":\"file_5_2_1\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"5.2.2\",\"title\":\"5.2.2: Placement of Outgoing Students\",\"description\":\"Average percentage of placement of outgoing students during the last five years.\",\"fields\":[{\"name\":\"qnm_5_2_2_placed\",\"label\":\"Number of outgoing students placed (Numerator)\",\"type\":\"number\"},{\"name\":\"qnm_5_2_2_total\",\"label\":\"Number of outgoing students (Denominator)\",\"type\":\"number\"},{\"name\":\"table_5_2_2\",\"label\":\"Data Template: Placement\",\"type\":\"table\",\"columns\":[{\"name\":\"year\",\"label\":\"Year\",\"type\":\"number\"},{\"name\":\"employer_name\",\"label\":\"Name of the employer with contact\",\"type\":\"text\"},{\"name\":\"num_placed\",\"label\":\"Number of students placed\",\"type\":\"number\"}]},{\"name\":\"file_5_2_2\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"5.2.3\",\"title\":\"5.2.3: Progression to Higher Education\",\"description\":\"Percentage of recently-graduated students who have progressed to higher education.\",\"fields\":[{\"name\":\"qnm_5_2_3_prog\",\"label\":\"Number of students progressing to higher education (Numerator)\",\"type\":\"number\"},{\"name\":\"qnm_5_2_3_final\",\"label\":\"Total number of final year students (Denominator)\",\"type\":\"number\"},{\"name\":\"table_5_2_3\",\"label\":\"Data Template: Higher Education\",\"type\":\"table\",\"columns\":[{\"name\":\"year\",\"label\":\"Year\",\"type\":\"number\"},{\"name\":\"student_name\",\"label\":\"Name of student enrolling\",\"type\":\"text\"},{\"name\":\"prog_graduated\",\"label\":\"Program graduated from\",\"type\":\"text\"},{\"name\":\"inst_joined\",\"label\":\"Name of institution joined\",\"type\":\"text\"},{\"name\":\"prog_admitted\",\"label\":\"Name of program admitted to\",\"type\":\"text\"}]},{\"name\":\"file_5_2_3\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]}]},{\"title\":\"5.3 Student Participation and Activities\",\"metrics\":[{\"id\":\"5.3.1\",\"title\":\"5.3.1: Awards\\/Medals for Sports\\/Cultural Activities\",\"description\":\"Number of awards\\/medals won by students for outstanding performance in sports\\/cultural activities.\",\"fields\":[{\"name\":\"qnm_5_3_1_awards\",\"label\":\"Number of awards\\/medals\",\"type\":\"number\"},{\"name\":\"table_5_3_1\",\"label\":\"Data Template: Sports\\/Cultural Awards\",\"type\":\"table\",\"columns\":[{\"name\":\"year\",\"label\":\"Year\",\"type\":\"number\"},{\"name\":\"award_name\",\"label\":\"Name of the award\\/medal\",\"type\":\"text\"},{\"name\":\"level\",\"label\":\"Level (Inter-university\\/State\\/National\\/Intl)\",\"type\":\"text\"},{\"name\":\"event_name\",\"label\":\"Name of the event\",\"type\":\"text\"}]},{\"name\":\"file_5_3_1\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"5.3.2\",\"title\":\"5.3.2: Student Council\",\"description\":\"Presence of Student Council and its activities for institutional development and student welfare.\",\"fields\":[{\"name\":\"qlm_5_3_2_text\",\"label\":\"Description\",\"type\":\"textarea\"},{\"name\":\"file_5_3_2\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"5.3.3\",\"title\":\"5.3.3: Sports and Cultural Events\",\"description\":\"Average number of sports and cultural events \\/ competitions organised by the institution per year.\",\"fields\":[{\"name\":\"qnm_5_3_3_events\",\"label\":\"Number of events organized\",\"type\":\"number\"},{\"name\":\"table_5_3_3\",\"label\":\"Data Template: Events Organized\",\"type\":\"table\",\"columns\":[{\"name\":\"year\",\"label\":\"Year\",\"type\":\"number\"},{\"name\":\"event_name\",\"label\":\"Name of the event \\/ competition\",\"type\":\"text\"}]},{\"name\":\"file_5_3_3\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]}]},{\"title\":\"5.4 Alumni Engagement\",\"metrics\":[{\"id\":\"5.4.1\",\"title\":\"5.4.1: Alumni Contribution\",\"description\":\"The Alumni Association\\/Chapters (registered and functional) contributes significantly to the development of the institution.\",\"fields\":[{\"name\":\"qlm_5_4_1_text\",\"label\":\"Description\",\"type\":\"textarea\"},{\"name\":\"file_5_4_1\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]}]}]}', '2026-10-07 17:30:00'),
(14, 5, 'v5_university_with_tables', 1, '{\"sections\":[{\"title\":\"5.1 Student Support\",\"metrics\":[{\"id\":\"5.1.1\",\"title\":\"5.1.1: Scholarships and Freeships\",\"description\":\"Average percentage of students benefited by scholarships and freeships provided by the institution, Government and non-government bodies.\",\"fields\":[{\"name\":\"qnm_5_1_1_benefited\",\"label\":\"Number of students benefited (Numerator)\",\"type\":\"number\"},{\"name\":\"qnm_5_1_1_total\",\"label\":\"Total number of students (Denominator)\",\"type\":\"number\"},{\"name\":\"table_5_1_1\",\"label\":\"Data Template: Scholarships\",\"type\":\"table\",\"columns\":[{\"name\":\"year\",\"label\":\"Year\",\"type\":\"number\"},{\"name\":\"scheme_name\",\"label\":\"Name of the scheme\",\"type\":\"text\"},{\"name\":\"num_benefited\",\"label\":\"Number of students benefiting\",\"type\":\"number\"}]},{\"name\":\"file_5_1_1\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"5.1.2\",\"title\":\"5.1.2: Career Counseling and Guidance\",\"description\":\"Average percentage of students benefited by career counseling and guidance for competitive examinations.\",\"fields\":[{\"name\":\"qnm_5_1_2_benefited\",\"label\":\"Number of students benefited (Numerator)\",\"type\":\"number\"},{\"name\":\"qnm_5_1_2_total\",\"label\":\"Total number of students (Denominator)\",\"type\":\"number\"},{\"name\":\"table_5_1_2\",\"label\":\"Data Template: Career Counseling\",\"type\":\"table\",\"columns\":[{\"name\":\"year\",\"label\":\"Year\",\"type\":\"number\"},{\"name\":\"scheme_name\",\"label\":\"Name of the scheme\",\"type\":\"text\"},{\"name\":\"num_passed\",\"label\":\"Students passed in competitive exams\",\"type\":\"number\"},{\"name\":\"num_benefited\",\"label\":\"Number of students benefited\",\"type\":\"number\"}]},{\"name\":\"file_5_1_2\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"5.1.3\",\"title\":\"5.1.3: Capacity Development and Skills Enhancement\",\"description\":\"Following Capacity development and skills enhancement initiatives are undertaken by the institution.\",\"fields\":[{\"name\":\"skills_opts\",\"label\":\"Initiatives undertaken (Check all that apply):\",\"type\":\"checkbox_group\",\"options\":[{\"name\":\"skill_soft\",\"label\":\"1. Soft skills\"},{\"name\":\"skill_lang\",\"label\":\"2. Language and communication skills\"},{\"name\":\"skill_life\",\"label\":\"3. Life skills (Yoga, physical fitness, health and hygiene)\"},{\"name\":\"skill_tech\",\"label\":\"4. Awareness of trends in technology\"}]},{\"name\":\"table_5_1_3\",\"label\":\"Data Template: Skill Enhancement\",\"type\":\"table\",\"columns\":[{\"name\":\"scheme_name\",\"label\":\"Name of the scheme\",\"type\":\"text\"},{\"name\":\"year_impl\",\"label\":\"Year of implementation\",\"type\":\"number\"},{\"name\":\"num_enrolled\",\"label\":\"Number of students enrolled\",\"type\":\"number\"},{\"name\":\"agencies\",\"label\":\"Name of agencies involved\",\"type\":\"text\"}]},{\"name\":\"file_5_1_3\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"5.1.4\",\"title\":\"5.1.4: Student Grievances Redressal\",\"description\":\"The Institution adopts the following for redressal of student grievances including sexual harassment and ragging cases.\",\"fields\":[{\"name\":\"grievance_opts\",\"label\":\"Options (Check all that apply):\",\"type\":\"checkbox_group\",\"options\":[{\"name\":\"gr_guide\",\"label\":\"1. Implementation of guidelines of statutory\\/regulatory bodies\"},{\"name\":\"gr_aware\",\"label\":\"2. Organisation-wide awareness and undertakings on policies\"},{\"name\":\"gr_mech\",\"label\":\"3. Mechanisms for submission of online\\/offline grievances\"},{\"name\":\"gr_timely\",\"label\":\"4. Timely redressal of grievances through appropriate committees\"}]},{\"name\":\"file_5_1_4\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]}]},{\"title\":\"5.2 Student Progression\",\"metrics\":[{\"id\":\"5.2.1\",\"title\":\"5.2.1: Qualifying in State\\/National\\/International Exams\",\"description\":\"Average percentage of students qualifying in state\\/ national\\/ international level examinations.\",\"fields\":[{\"name\":\"qnm_5_2_1_qual\",\"label\":\"Number of students qualifying (Numerator)\",\"type\":\"number\"},{\"name\":\"qnm_5_2_1_appear\",\"label\":\"Number of students appeared (Denominator)\",\"type\":\"number\"},{\"name\":\"table_5_2_1\",\"label\":\"Data Template: Competitive Exams\",\"type\":\"table\",\"columns\":[{\"name\":\"year\",\"label\":\"Year\",\"type\":\"number\"},{\"name\":\"exam_name\",\"label\":\"Exam (NET\\/SLET\\/GATE\\/etc)\",\"type\":\"text\"},{\"name\":\"num_selected\",\"label\":\"Number of students selected\",\"type\":\"number\"}]},{\"name\":\"file_5_2_1\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"5.2.2\",\"title\":\"5.2.2: Placement of Outgoing Students\",\"description\":\"Average percentage of placement of outgoing students during the last five years.\",\"fields\":[{\"name\":\"qnm_5_2_2_placed\",\"label\":\"Number of outgoing students placed (Numerator)\",\"type\":\"number\"},{\"name\":\"qnm_5_2_2_total\",\"label\":\"Number of outgoing students (Denominator)\",\"type\":\"number\"},{\"name\":\"table_5_2_2\",\"label\":\"Data Template: Placement\",\"type\":\"table\",\"columns\":[{\"name\":\"year\",\"label\":\"Year\",\"type\":\"number\"},{\"name\":\"employer_name\",\"label\":\"Name of the employer with contact\",\"type\":\"text\"},{\"name\":\"num_placed\",\"label\":\"Number of students placed\",\"type\":\"number\"}]},{\"name\":\"file_5_2_2\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"5.2.3\",\"title\":\"5.2.3: Progression to Higher Education\",\"description\":\"Percentage of recently-graduated students who have progressed to higher education.\",\"fields\":[{\"name\":\"qnm_5_2_3_prog\",\"label\":\"Number of students progressing to higher education (Numerator)\",\"type\":\"number\"},{\"name\":\"qnm_5_2_3_final\",\"label\":\"Total number of final year students (Denominator)\",\"type\":\"number\"},{\"name\":\"table_5_2_3\",\"label\":\"Data Template: Higher Education\",\"type\":\"table\",\"columns\":[{\"name\":\"year\",\"label\":\"Year\",\"type\":\"number\"},{\"name\":\"student_name\",\"label\":\"Name of student enrolling\",\"type\":\"text\"},{\"name\":\"prog_graduated\",\"label\":\"Program graduated from\",\"type\":\"text\"},{\"name\":\"inst_joined\",\"label\":\"Name of institution joined\",\"type\":\"text\"},{\"name\":\"prog_admitted\",\"label\":\"Name of program admitted to\",\"type\":\"text\"}]},{\"name\":\"file_5_2_3\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]}]},{\"title\":\"5.3 Student Participation and Activities\",\"metrics\":[{\"id\":\"5.3.1\",\"title\":\"5.3.1: Awards\\/Medals for Sports\\/Cultural Activities\",\"description\":\"Number of awards\\/medals won by students for outstanding performance in sports\\/cultural activities.\",\"fields\":[{\"name\":\"qnm_5_3_1_awards\",\"label\":\"Number of awards\\/medals\",\"type\":\"number\"},{\"name\":\"table_5_3_1\",\"label\":\"Data Template: Sports\\/Cultural Awards\",\"type\":\"table\",\"columns\":[{\"name\":\"year\",\"label\":\"Year\",\"type\":\"number\"},{\"name\":\"award_name\",\"label\":\"Name of the award\\/medal\",\"type\":\"text\"},{\"name\":\"level\",\"label\":\"Level (Inter-university\\/State\\/National\\/Intl)\",\"type\":\"text\"},{\"name\":\"event_name\",\"label\":\"Name of the event\",\"type\":\"text\"}]},{\"name\":\"file_5_3_1\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"5.3.2\",\"title\":\"5.3.2: Student Council\",\"description\":\"Presence of Student Council and its activities for institutional development and student welfare.\",\"fields\":[{\"name\":\"qlm_5_3_2_text\",\"label\":\"Description\",\"type\":\"textarea\"},{\"name\":\"file_5_3_2\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"5.3.3\",\"title\":\"5.3.3: Sports and Cultural Events\",\"description\":\"Average number of sports and cultural events \\/ competitions organised by the institution per year.\",\"fields\":[{\"name\":\"qnm_5_3_3_events\",\"label\":\"Number of events organized\",\"type\":\"number\"},{\"name\":\"table_5_3_3\",\"label\":\"Data Template: Events Organized\",\"type\":\"table\",\"columns\":[{\"name\":\"year\",\"label\":\"Year\",\"type\":\"number\"},{\"name\":\"event_name\",\"label\":\"Name of the event \\/ competition\",\"type\":\"text\"}]},{\"name\":\"file_5_3_3\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]}]},{\"title\":\"5.4 Alumni Engagement\",\"metrics\":[{\"id\":\"5.4.1\",\"title\":\"5.4.1: Alumni Contribution (QlM)\",\"description\":\"The Alumni Association\\/Chapters (registered and functional) contributes significantly to the development of the institution.\",\"fields\":[{\"name\":\"qlm_5_4_1_text\",\"label\":\"Description\",\"type\":\"textarea\"},{\"name\":\"file_5_4_1\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"5.4.2\",\"title\":\"5.4.2: Alumni contribution (INR in lakhs)\",\"description\":\"Alumni contribution during the last five years (INR in lakhs).\",\"fields\":[{\"name\":\"alumni_contrib_opts\",\"label\":\"Select contribution amount (Check one):\",\"type\":\"checkbox_group\",\"options\":[{\"name\":\"ac_100\",\"label\":\"A. \\u2265 100 Lakhs\"},{\"name\":\"ac_50\",\"label\":\"B. 50 Lakhs - 100 Lakhs\"},{\"name\":\"ac_20\",\"label\":\"C. 20 Lakhs - 50 Lakhs\"},{\"name\":\"ac_5\",\"label\":\"D. 5 Lakhs - 20 Lakhs\"},{\"name\":\"ac_less5\",\"label\":\"E. < 5 Lakhs\"}]},{\"name\":\"file_5_4_2\",\"label\":\"Supporting Document (Audited Statement)\",\"type\":\"file\",\"accept\":\".pdf\"}]}]}]}', '2026-10-07 17:35:26'),
(15, 6, 'v1_university_with_tables', 1, '{\"sections\":[{\"title\":\"6.1 Institutional Vision and Leadership\",\"metrics\":[{\"id\":\"6.1.1\",\"title\":\"6.1.1: Vision and Mission\",\"description\":\"The institution has a clearly stated vision and mission which are reflected in its academic and administrative governance.\",\"fields\":[{\"name\":\"qlm_6_1_1_text\",\"label\":\"Description\",\"type\":\"textarea\"},{\"name\":\"file_6_1_1\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"6.1.2\",\"title\":\"6.1.2: Effective Leadership\",\"description\":\"Effective leadership is reflected in various institutional practices such as decentralization and participative management.\",\"fields\":[{\"name\":\"qlm_6_1_2_text\",\"label\":\"Description\",\"type\":\"textarea\"},{\"name\":\"file_6_1_2\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]}]},{\"title\":\"6.2 Strategy Development and Deployment\",\"metrics\":[{\"id\":\"6.2.1\",\"title\":\"6.2.1: Strategic Plan Deployment\",\"description\":\"The institutional Strategic plan is effectively deployed.\",\"fields\":[{\"name\":\"qlm_6_2_1_text\",\"label\":\"Description\",\"type\":\"textarea\"},{\"name\":\"file_6_2_1\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"6.2.2\",\"title\":\"6.2.2: Functioning of Institutional Bodies\",\"description\":\"Functioning of the institutional bodies is effective and efficient as visible from policies, administrative setup, appointment, service rules, and procedures, etc.\",\"fields\":[{\"name\":\"qlm_6_2_2_text\",\"label\":\"Description\",\"type\":\"textarea\"},{\"name\":\"file_6_2_2\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"6.2.3\",\"title\":\"6.2.3: Implementation of e-governance\",\"description\":\"Implementation of e-governance in areas of operation.\",\"fields\":[{\"name\":\"egov_opts\",\"label\":\"Areas of e-governance (Check all that apply):\",\"type\":\"checkbox_group\",\"options\":[{\"name\":\"egov_admin\",\"label\":\"1. Administration\"},{\"name\":\"egov_finance\",\"label\":\"2. Finance and Accounts\"},{\"name\":\"egov_student\",\"label\":\"3. Student Admission and Support\"},{\"name\":\"egov_exam\",\"label\":\"4. Examination\"}]},{\"name\":\"table_6_2_3\",\"label\":\"Data Template: e-Governance\",\"type\":\"table\",\"columns\":[{\"name\":\"area\",\"label\":\"Areas of e-governance\",\"type\":\"text\"},{\"name\":\"vendor\",\"label\":\"Name of the Vendor with contact details\",\"type\":\"text\"},{\"name\":\"year_impl\",\"label\":\"Year of implementation\",\"type\":\"number\"}]},{\"name\":\"file_6_2_3\",\"label\":\"Supporting Document (ERP Document\\/Screenshots)\",\"type\":\"file\",\"accept\":\".pdf\"}]}]},{\"title\":\"6.3 Faculty Empowerment Strategies\",\"metrics\":[{\"id\":\"6.3.1\",\"title\":\"6.3.1: Welfare Measures\",\"description\":\"The institution has effective welfare measures for teaching and non-teaching staff.\",\"fields\":[{\"name\":\"qlm_6_3_1_text\",\"label\":\"Description\",\"type\":\"textarea\"},{\"name\":\"file_6_3_1\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"6.3.2\",\"title\":\"6.3.2: Financial Support for Conferences\",\"description\":\"Average percentage of teachers provided with financial support to attend conferences\\/workshops and towards membership fee of professional bodies.\",\"fields\":[{\"name\":\"qnm_6_3_2_supported\",\"label\":\"Number of teachers provided with financial support (Numerator)\",\"type\":\"number\"},{\"name\":\"qnm_6_3_2_total\",\"label\":\"Total number of teachers (Denominator)\",\"type\":\"number\"},{\"name\":\"table_6_3_2\",\"label\":\"Data Template: Financial Support\",\"type\":\"table\",\"columns\":[{\"name\":\"year\",\"label\":\"Year\",\"type\":\"number\"},{\"name\":\"teacher_name\",\"label\":\"Name of teacher\",\"type\":\"text\"},{\"name\":\"conf_name\",\"label\":\"Name of conference\\/workshop\",\"type\":\"text\"},{\"name\":\"amount\",\"label\":\"Amount of support (INR)\",\"type\":\"number\"}]},{\"name\":\"file_6_3_2\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"6.3.3\",\"title\":\"6.3.3: Professional Development Programs\",\"description\":\"Average number of professional development \\/ administrative training programs organized by the institution for teaching and non-teaching staff.\",\"fields\":[{\"name\":\"qnm_6_3_3_progs\",\"label\":\"Total number of programs organized\",\"type\":\"number\"},{\"name\":\"table_6_3_3\",\"label\":\"Data Template: Development Programs\",\"type\":\"table\",\"columns\":[{\"name\":\"year\",\"label\":\"Year\",\"type\":\"number\"},{\"name\":\"prog_title\",\"label\":\"Title of the professional development program\",\"type\":\"text\"},{\"name\":\"participants\",\"label\":\"No. of participants (Teaching\\/Non-Teaching)\",\"type\":\"number\"}]},{\"name\":\"file_6_3_3\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"6.3.4\",\"title\":\"6.3.4: Faculty Development Programmes (FDP)\",\"description\":\"Average percentage of teachers undergoing online\\/face-to-face Faculty Development Programmes (FDP).\",\"fields\":[{\"name\":\"qnm_6_3_4_fdp\",\"label\":\"Number of teachers undergoing FDP (Numerator)\",\"type\":\"number\"},{\"name\":\"qnm_6_3_4_total\",\"label\":\"Total number of teachers (Denominator)\",\"type\":\"number\"},{\"name\":\"table_6_3_4\",\"label\":\"Data Template: FDP Details\",\"type\":\"table\",\"columns\":[{\"name\":\"year\",\"label\":\"Year\",\"type\":\"number\"},{\"name\":\"teacher_name\",\"label\":\"Name of teacher who attended\",\"type\":\"text\"},{\"name\":\"prog_title\",\"label\":\"Title of the program\",\"type\":\"text\"},{\"name\":\"duration\",\"label\":\"Duration (from - to)\",\"type\":\"text\"}]},{\"name\":\"file_6_3_4\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]}]},{\"title\":\"6.4 Financial Management and Resource Mobilization\",\"metrics\":[{\"id\":\"6.4.1\",\"title\":\"6.4.1: Internal and External Audits\",\"description\":\"Institution conducts internal and external financial audits regularly.\",\"fields\":[{\"name\":\"qlm_6_4_1_text\",\"label\":\"Description\",\"type\":\"textarea\"},{\"name\":\"file_6_4_1\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"6.4.2\",\"title\":\"6.4.2: Funds \\/ Grants from Government Bodies\",\"description\":\"Funds \\/ Grants received from government bodies during the last five years for development and maintenance of infrastructure.\",\"fields\":[{\"name\":\"qnm_6_4_2_grants\",\"label\":\"Total Grants received from government bodies (INR in Lakhs)\",\"type\":\"number\"},{\"name\":\"table_6_4_2\",\"label\":\"Data Template: Govt Grants\",\"type\":\"table\",\"columns\":[{\"name\":\"year\",\"label\":\"Year\",\"type\":\"number\"},{\"name\":\"grant_name\",\"label\":\"Name of the government funding agencies\",\"type\":\"text\"},{\"name\":\"funds\",\"label\":\"Funds\\/ Grants received (INR in Lakhs)\",\"type\":\"number\"}]},{\"name\":\"file_6_4_2\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"6.4.3\",\"title\":\"6.4.3: Funds \\/ Grants from Non-Government Bodies\",\"description\":\"Funds \\/ Grants received from non-government bodies, individuals, philanthropists during the last five years.\",\"fields\":[{\"name\":\"qnm_6_4_3_grants\",\"label\":\"Total Grants received from non-government bodies (INR in Lakhs)\",\"type\":\"number\"},{\"name\":\"table_6_4_3\",\"label\":\"Data Template: Non-Govt Grants\",\"type\":\"table\",\"columns\":[{\"name\":\"year\",\"label\":\"Year\",\"type\":\"number\"},{\"name\":\"grant_name\",\"label\":\"Name of the non-government funding agencies\\/individuals\",\"type\":\"text\"},{\"name\":\"funds\",\"label\":\"Funds\\/ Grants received (INR in Lakhs)\",\"type\":\"number\"}]},{\"name\":\"file_6_4_3\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"6.4.4\",\"title\":\"6.4.4: Institutional strategies for mobilisation of funds\",\"description\":\"Institutional strategies for mobilisation of funds and the optimal utilisation of resources.\",\"fields\":[{\"name\":\"qlm_6_4_4_text\",\"label\":\"Description\",\"type\":\"textarea\"},{\"name\":\"file_6_4_4\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]}]},{\"title\":\"6.5 Internal Quality Assurance System\",\"metrics\":[{\"id\":\"6.5.1\",\"title\":\"6.5.1: IQAC Contribution\",\"description\":\"Internal Quality Assurance Cell (IQAC) has contributed significantly for institutionalizing the quality assurance strategies and processes.\",\"fields\":[{\"name\":\"qlm_6_5_1_text\",\"label\":\"Description\",\"type\":\"textarea\"},{\"name\":\"file_6_5_1\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"6.5.2\",\"title\":\"6.5.2: Quality Assurance Initiatives\",\"description\":\"Quality assurance initiatives of the institution.\",\"fields\":[{\"name\":\"qa_opts\",\"label\":\"Initiatives undertaken (Check all that apply):\",\"type\":\"checkbox_group\",\"options\":[{\"name\":\"qa_meeting\",\"label\":\"1. Regular meeting of Internal Quality Assurance Cell (IQAC)\"},{\"name\":\"qa_feedback\",\"label\":\"2. Feedback collected, analyzed and used for improvements\"},{\"name\":\"qa_collab\",\"label\":\"3. Collaborative quality initiatives with other institution(s)\"},{\"name\":\"qa_audit\",\"label\":\"4. Academic Administrative Audit (AAA) and follow-up action\"},{\"name\":\"qa_cert\",\"label\":\"5. ISO \\/ NBA certification or other quality audits\"}]},{\"name\":\"table_6_5_2\",\"label\":\"Data Template: Quality Initiatives\",\"type\":\"table\",\"columns\":[{\"name\":\"year\",\"label\":\"Year\",\"type\":\"number\"},{\"name\":\"initiative_name\",\"label\":\"Name of quality initiative by IQAC\",\"type\":\"text\"},{\"name\":\"duration\",\"label\":\"Duration (from - to)\",\"type\":\"text\"},{\"name\":\"participants\",\"label\":\"Number of participants\",\"type\":\"number\"}]},{\"name\":\"file_6_5_2\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"6.5.3\",\"title\":\"6.5.3: Incremental Improvements\",\"description\":\"Incremental improvements made for the preceding five years with regard to quality.\",\"fields\":[{\"name\":\"qlm_6_5_3_text\",\"label\":\"Description\",\"type\":\"textarea\"},{\"name\":\"file_6_5_3\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]}]}]}', '2026-10-08 03:54:52'),
(16, 7, 'v1_university_standard', 1, '{\"sections\":[{\"title\":\"7.1 Institutional Values and Social Responsibilities\",\"metrics\":[{\"id\":\"7.1.1\",\"title\":\"7.1.1: Promotion of Gender Equity\",\"description\":\"Measures initiated by the Institution for the promotion of gender equity during the last five years.\",\"fields\":[{\"name\":\"qlm_7_1_1_text\",\"label\":\"Description\",\"type\":\"textarea\"},{\"name\":\"file_7_1_1\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"7.1.2\",\"title\":\"7.1.2: Alternate Sources of Energy\",\"description\":\"The Institution has facilities for alternate sources of energy and energy conservation measures.\",\"fields\":[{\"name\":\"energy_opts\",\"label\":\"Facilities available (Check all that apply):\",\"type\":\"checkbox_group\",\"options\":[{\"name\":\"en_solar\",\"label\":\"1. Solar energy\"},{\"name\":\"en_biogas\",\"label\":\"2. Biogas plant\"},{\"name\":\"en_grid\",\"label\":\"3. Wheeling to the Grid\"},{\"name\":\"en_sensor\",\"label\":\"4. Sensor-based energy conservation\"},{\"name\":\"en_led\",\"label\":\"5. Use of LED bulbs\\/ power efficient equipment\"}]},{\"name\":\"file_7_1_2\",\"label\":\"Supporting Document (Geotagged Photographs)\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"7.1.3\",\"title\":\"7.1.3: Waste Management Facilities\",\"description\":\"Describe the facilities in the Institution for the management of the degradable and non-degradable waste.\",\"fields\":[{\"name\":\"qlm_7_1_3_text\",\"label\":\"Description\",\"type\":\"textarea\"},{\"name\":\"file_7_1_3\",\"label\":\"Supporting Document (Geotagged Photographs)\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"7.1.4\",\"title\":\"7.1.4: Water Conservation Facilities\",\"description\":\"Water conservation facilities available in the Institution.\",\"fields\":[{\"name\":\"water_opts\",\"label\":\"Facilities available (Check all that apply):\",\"type\":\"checkbox_group\",\"options\":[{\"name\":\"wa_rain\",\"label\":\"1. Rain water harvesting\"},{\"name\":\"wa_bore\",\"label\":\"2. Borewell \\/Open well recharge\"},{\"name\":\"wa_tank\",\"label\":\"3. Construction of tanks and bunds\"},{\"name\":\"wa_waste\",\"label\":\"4. Waste water recycling\"},{\"name\":\"wa_maint\",\"label\":\"5. Maintenance of water bodies and distribution system in the campus\"}]},{\"name\":\"file_7_1_4\",\"label\":\"Supporting Document (Geotagged Photographs)\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"7.1.5\",\"title\":\"7.1.5: Green Campus Initiatives\",\"description\":\"Green campus initiatives include restricted entry of automobiles, use of bicycles\\/battery powered vehicles, pedestrian friendly pathways, ban on use of plastic, landscaping.\",\"fields\":[{\"name\":\"green_opts\",\"label\":\"Initiatives available (Check all that apply):\",\"type\":\"checkbox_group\",\"options\":[{\"name\":\"gc_auto\",\"label\":\"1. Restricted entry of automobiles\"},{\"name\":\"gc_battery\",\"label\":\"2. Use of Bicycles\\/ Battery powered vehicles\"},{\"name\":\"gc_path\",\"label\":\"3. Pedestrian Friendly pathways\"},{\"name\":\"gc_plastic\",\"label\":\"4. Ban on use of Plastic\"},{\"name\":\"gc_landscape\",\"label\":\"5. landscaping with trees and plants\"}]},{\"name\":\"file_7_1_5\",\"label\":\"Supporting Document (Geotagged Photographs\\/Circulars)\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"7.1.6\",\"title\":\"7.1.6: Environment and Energy Audits\",\"description\":\"Quality audits on environment and energy are regularly undertaken by the institution.\",\"fields\":[{\"name\":\"audit_opts\",\"label\":\"Audits undertaken (Check all that apply):\",\"type\":\"checkbox_group\",\"options\":[{\"name\":\"au_green\",\"label\":\"1. Green audit\"},{\"name\":\"au_energy\",\"label\":\"2. Energy audit\"},{\"name\":\"au_env\",\"label\":\"3. Environment audit\"},{\"name\":\"au_clean\",\"label\":\"4. Clean and green campus recognitions\\/awards\"},{\"name\":\"au_beyond\",\"label\":\"5. Beyond the campus environmental promotional activities\"}]},{\"name\":\"file_7_1_6\",\"label\":\"Supporting Document (Audit reports\\/Certificates)\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"7.1.7\",\"title\":\"7.1.7: Disabled-friendly Environment\",\"description\":\"The Institution has disabled-friendly, barrier free environment.\",\"fields\":[{\"name\":\"disable_opts\",\"label\":\"Facilities available (Check all that apply):\",\"type\":\"checkbox_group\",\"options\":[{\"name\":\"df_built\",\"label\":\"1. Built environment with ramps\\/lifts for easy access to classrooms\"},{\"name\":\"df_wash\",\"label\":\"2. Disabled-friendly washrooms\"},{\"name\":\"df_sign\",\"label\":\"3. Signage including tactile path, lights, display boards and signposts\"},{\"name\":\"df_assist\",\"label\":\"4. Assistive technology and facilities for Divyangjan accessible website, screen-reading software, mechanized equipment\"},{\"name\":\"df_enquiry\",\"label\":\"5. Provision for enquiry and information: Human assistance, reader, scribe, soft copies of reading material, screen reading\"}]},{\"name\":\"file_7_1_7\",\"label\":\"Supporting Document (Geotagged Photographs)\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"7.1.8\",\"title\":\"7.1.8: Inclusive Environment\",\"description\":\"Describe the Institutional efforts\\/initiatives in providing an inclusive environment i.e., tolerance and harmony towards cultural, regional, linguistic, communal socioeconomic and other diversities.\",\"fields\":[{\"name\":\"qlm_7_1_8_text\",\"label\":\"Description\",\"type\":\"textarea\"},{\"name\":\"file_7_1_8\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"7.1.9\",\"title\":\"7.1.9: Constitutional Obligations\",\"description\":\"Sensitization of students and employees of the Institution to the constitutional obligations: values, rights, duties and responsibilities of citizens.\",\"fields\":[{\"name\":\"qlm_7_1_9_text\",\"label\":\"Description\",\"type\":\"textarea\"},{\"name\":\"file_7_1_9\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"7.1.10\",\"title\":\"7.1.10: Code of Conduct\",\"description\":\"The Institution has a prescribed code of conduct for students, teachers, administrators and other staff and conducts periodic programmes in this regard.\",\"fields\":[{\"name\":\"conduct_opts\",\"label\":\"Initiatives (Check all that apply):\",\"type\":\"checkbox_group\",\"options\":[{\"name\":\"cc_display\",\"label\":\"1. The Code of Conduct is displayed on the website\"},{\"name\":\"cc_comm\",\"label\":\"2. There is a committee to monitor adherence to the Code of Conduct\"},{\"name\":\"cc_prog\",\"label\":\"3. Institution organizes professional ethics programmes for students, teachers, administrators and other staff\"},{\"name\":\"cc_aware\",\"label\":\"4. Annual awareness programmes on Code of Conduct are organized\"}]},{\"name\":\"file_7_1_10\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]},{\"id\":\"7.1.11\",\"title\":\"7.1.11: Commemorative Days and Events\",\"description\":\"Institution celebrates \\/ organizes national and international commemorative days, events and festivals.\",\"fields\":[{\"name\":\"qlm_7_1_11_text\",\"label\":\"Description\",\"type\":\"textarea\"},{\"name\":\"file_7_1_11\",\"label\":\"Supporting Document\",\"type\":\"file\",\"accept\":\".pdf\"}]}]},{\"title\":\"7.2 Best Practices\",\"metrics\":[{\"id\":\"7.2.1\",\"title\":\"7.2.1: Institutional Best Practices\",\"description\":\"Describe two best practices successfully implemented by the Institution as per NAAC format provided in the Manual.\",\"fields\":[{\"name\":\"qlm_7_2_1_text\",\"label\":\"Description of Best Practices\",\"type\":\"textarea\"},{\"name\":\"file_7_2_1\",\"label\":\"Supporting Document \\/ URL\",\"type\":\"file\",\"accept\":\".pdf\"}]}]},{\"title\":\"7.3 Institutional Distinctiveness\",\"metrics\":[{\"id\":\"7.3.1\",\"title\":\"7.3.1: Institutional Distinctiveness\",\"description\":\"Portray the performance of the Institution in one area distinctive to its priority and thrust within 1000 words.\",\"fields\":[{\"name\":\"qlm_7_3_1_text\",\"label\":\"Description\",\"type\":\"textarea\"},{\"name\":\"file_7_3_1\",\"label\":\"Supporting Document \\/ URL\",\"type\":\"file\",\"accept\":\".pdf\"}]}]}]}', '2026-10-08 04:05:02');

-- --------------------------------------------------------

--
-- Table structure for table `naac_submissions`
--

CREATE TABLE `naac_submissions` (
  `submission_id` int(11) NOT NULL,
  `dept_id` int(11) NOT NULL,
  `academic_year` varchar(20) NOT NULL,
  `status` varchar(50) DEFAULT 'Draft',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `naac_submissions`
--

INSERT INTO `naac_submissions` (`submission_id`, `dept_id`, `academic_year`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, '2026-27', 'Draft', '2026-10-29 04:55:50', '2026-10-29 04:55:50');

-- --------------------------------------------------------

--
-- Table structure for table `nba_criteria`
--

CREATE TABLE `nba_criteria` (
  `SI_no` int(10) NOT NULL,
  `Sub_no` varchar(30) NOT NULL,
  `Des` varchar(600) NOT NULL,
  `year` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `nba_criteria1`
--

CREATE TABLE `nba_criteria1` (
  `SI_no` int(10) NOT NULL,
  `Sub_no` varchar(30) NOT NULL,
  `Des` varchar(600) NOT NULL,
  `year` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `nba_criteria2`
--

CREATE TABLE `nba_criteria2` (
  `SI_no` int(10) NOT NULL,
  `Sub_no` varchar(30) NOT NULL,
  `Des` varchar(600) NOT NULL,
  `year` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `nba_criteria_data`
--

CREATE TABLE `nba_criteria_data` (
  `id` int(11) NOT NULL,
  `submission_id` int(11) NOT NULL,
  `criterion_number` int(11) NOT NULL,
  `data_json` longtext DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `nba_criteria_data`
--

INSERT INTO `nba_criteria_data` (`id`, `submission_id`, `criterion_number`, `data_json`, `updated_at`) VALUES
(1, 1, 3, '{\"levelData\":{\"dept_20\":{\"pdf_3_1\":\"\",\"pdf_3_2\":\"\",\"pdf_3_3\":\"\",\"pdf_3_4\":\"\",\"pdf_3_5\":\"\",\"table_3_6\":[],\"pdf_3_7_1\":\"\",\"pdf_3_7_2\":\"\",\"table_3_8\":{\"columns\":[\"PO1\",\"PO2\",\"PO3\",\"PO4\",\"PO5\",\"PO6\",\"PO7\",\"PO8\",\"PO9\",\"PO10\",\"PO11\"],\"rows\":[]},\"table_3_8_2\":{\"columns\":[\"PO1\",\"PO2\",\"PO3\",\"PO4\",\"PO5\",\"PO6\",\"PO7\",\"PO8\",\"PO9\",\"PO10\",\"PO11\"],\"rows\":[]},\"table_3_8_3\":{\"columns\":[\"PO1\",\"PO2\",\"PO3\",\"PO4\",\"PO5\",\"PO6\",\"PO7\",\"PO8\",\"PO9\",\"PO10\",\"PO11\"],\"rows\":[]}},\"dept_2\":{\"pdf_3_1\":\"\",\"pdf_3_2\":\"\",\"pdf_3_3\":\"\",\"pdf_3_4\":\"\",\"pdf_3_5\":\"\",\"table_3_6\":[],\"pdf_3_7_1\":\"\",\"pdf_3_7_2\":\"\",\"table_3_8\":{\"columns\":[\"PO1\",\"PO2\",\"PO3\",\"PO4\",\"PO5\",\"PO6\",\"PO7\",\"PO8\",\"PO9\",\"PO10\",\"PO11\"],\"rows\":[]},\"table_3_8_2\":{\"columns\":[\"PO1\",\"PO2\",\"PO3\",\"PO4\",\"PO5\",\"PO6\",\"PO7\",\"PO8\",\"PO9\",\"PO10\",\"PO11\"],\"rows\":[]},\"table_3_8_3\":{\"columns\":[\"PO1\",\"PO2\",\"PO3\",\"PO4\",\"PO5\",\"PO6\",\"PO7\",\"PO8\",\"PO9\",\"PO10\",\"PO11\"],\"rows\":[]}}}}', '2026-09-22 05:19:33'),
(2, 1, 4, '{\"levelData\":{\"dept_20\":{\"pdf_4_1\":\"\",\"pdf_4_2\":\"\",\"pdf_4_3\":\"\",\"pdf_4_4\":\"\",\"pdf_4_5\":\"\",\"pdf_4_6\":\"\",\"table4a\":[],\"t4711\":[],\"t4712\":{\"caym1\":[],\"caym2\":[],\"caym3\":[]}},\"dept_2\":{\"pdf_4_1\":\"\",\"pdf_4_2\":\"\",\"pdf_4_3\":\"\",\"pdf_4_4\":\"\",\"pdf_4_5\":\"\",\"pdf_4_6\":\"\",\"table4a\":[],\"t4711\":[],\"t4712\":{\"caym1\":[],\"caym2\":[],\"caym3\":[]}}}}', '2026-09-23 05:03:31'),
(3, 2, 9, '{\"levelData\":{\"dept_1\":{\"inst_budget\":[],\"inst_expenditure\":[],\"prog_budget\":[],\"prog_expenditure\":[],\"t91_s4_cay\":\"\",\"t91_ns1_cay\":\"\",\"t91_ns2_cay\":\"\",\"t91_s4_caym1\":\"\",\"t91_ns1_caym1\":\"\",\"t91_ns2_caym1\":\"\",\"t91_s4_caym2\":\"\",\"t91_ns1_caym2\":\"\",\"t91_ns2_caym2\":\"\",\"text_9_2\":\"Sample mentoring system data for verification: Every student is assigned a faculty mentor for academic and personal guidance.\",\"text_9_3_1\":\"\",\"text_9_3_2\":\"\",\"text_9_4\":\"\",\"text_9_5\":\"\",\"text_9_6_1\":\"\",\"text_9_6_2\":\"\",\"text_9_6_3\":\"\",\"text_9_9\":\"\",\"text_9_10\":\"\",\"text_9_11\":\"\",\"text_9_12\":\"\",\"text_9_13\":\"\",\"text_9_14\":\"\"}},\"uploaded_files\":[]}', '2026-10-18 14:56:56'),
(4, 3, 9, '{\"test\":true,\"final_pdf_file\":\"final_criterion9_dept_1_2023_24_1793264136.pdf\",\"final_pdf_url\":\"http:\\/\\/localhost\\/mini\\/FMS\\/public\\/index.php?route=reports\\/download&type=nba&file=final_criterion9_dept_1_2023_24_1793264136.pdf\",\"final_pdf_generated_at\":\"2026-10-29 09:55:36\"}', '2026-10-29 08:55:36'),
(5, 4, 1, '{\"levelData\":{\"univ\":{\"vision\":\"\",\"missions\":[{\"id\":\"M1\",\"text\":\"\"}]},\"dept_1\":{\"vision\":\"\",\"missions\":[{\"id\":\"M1\",\"text\":\"\"}],\"curriculum\":[],\"components\":[{\"name\":\"Basic Sciences\",\"percent\":\"\",\"hours\":\"\",\"credits\":\"\"},{\"name\":\"Basic Engineering\",\"percent\":\"\",\"hours\":\"\",\"credits\":\"\"},{\"name\":\"Humanities and Social Sciences\",\"percent\":\"\",\"hours\":\"\",\"credits\":\"\"},{\"name\":\"Program Core\",\"percent\":\"\",\"hours\":\"\",\"credits\":\"\"},{\"name\":\"Program Electives\",\"percent\":\"\",\"hours\":\"\",\"credits\":\"\"},{\"name\":\"Open Electives\",\"percent\":\"\",\"hours\":\"\",\"credits\":\"\"},{\"name\":\"Project(s)\",\"percent\":\"\",\"hours\":\"\",\"credits\":\"\"},{\"name\":\"Internships\\/Seminars\",\"percent\":\"\",\"hours\":\"\",\"credits\":\"\"}],\"pos\":[{\"id\":\"PO1\",\"text\":\"\"}],\"psos\":[{\"id\":\"PSO1\",\"text\":\"\"},{\"id\":\"PSO2\",\"text\":\"\"}],\"poPsoCourseMapping\":[],\"courseOutcomes\":[{\"semesterNo\":\"\",\"courseTitle\":\"\",\"courseCode\":\"\",\"cos\":[\"\",\"\",\"\",\"\",\"\",\"\"],\"coMapping\":[[],[],[],[],[],[]],\"programMapping\":[]},{\"semesterNo\":\"\",\"courseTitle\":\"\",\"courseCode\":\"\",\"cos\":[\"\",\"\",\"\",\"\",\"\",\"\"],\"coMapping\":[[],[],[],[],[],[]],\"programMapping\":[]},{\"semesterNo\":\"\",\"courseTitle\":\"\",\"courseCode\":\"\",\"cos\":[\"\",\"\",\"\",\"\",\"\",\"\"],\"coMapping\":[[],[],[],[],[],[]],\"programMapping\":[]},{\"semesterNo\":\"\",\"courseTitle\":\"\",\"courseCode\":\"\",\"cos\":[\"\",\"\",\"\",\"\",\"\",\"\"],\"coMapping\":[[],[],[],[],[],[]],\"programMapping\":[]},{\"semesterNo\":\"\",\"courseTitle\":\"\",\"courseCode\":\"\",\"cos\":[\"\",\"\",\"\",\"\",\"\",\"\"],\"coMapping\":[[],[],[],[],[],[]],\"programMapping\":[]},{\"semesterNo\":\"\",\"courseTitle\":\"\",\"courseCode\":\"\",\"cos\":[\"\",\"\",\"\",\"\",\"\",\"\"],\"coMapping\":[[],[],[],[],[],[]],\"programMapping\":[]},{\"semesterNo\":\"\",\"courseTitle\":\"\",\"courseCode\":\"\",\"cos\":[\"\",\"\",\"\",\"\",\"\",\"\"],\"coMapping\":[[],[],[],[],[],[]],\"programMapping\":[]},{\"semesterNo\":\"\",\"courseTitle\":\"\",\"courseCode\":\"\",\"cos\":[\"\",\"\",\"\",\"\",\"\",\"\"],\"coMapping\":[[],[],[],[],[],[]],\"programMapping\":[]},{\"semesterNo\":\"\",\"courseTitle\":\"\",\"courseCode\":\"\",\"cos\":[\"\",\"\",\"\",\"\",\"\",\"\"],\"coMapping\":[[],[],[],[],[],[]],\"programMapping\":[]},{\"semesterNo\":\"\",\"courseTitle\":\"\",\"courseCode\":\"\",\"cos\":[\"\",\"\",\"\",\"\",\"\",\"\"],\"coMapping\":[[],[],[],[],[],[]],\"programMapping\":[]},{\"semesterNo\":\"\",\"courseTitle\":\"\",\"courseCode\":\"\",\"cos\":[\"\",\"\",\"\",\"\",\"\",\"\"],\"coMapping\":[[],[],[],[],[],[]],\"programMapping\":[]},{\"semesterNo\":\"\",\"courseTitle\":\"\",\"courseCode\":\"\",\"cos\":[\"\",\"\",\"\",\"\",\"\",\"\"],\"coMapping\":[[],[],[],[],[],[]],\"programMapping\":[]},{\"semesterNo\":\"\",\"courseTitle\":\"\",\"courseCode\":\"\",\"cos\":[\"\",\"\",\"\",\"\",\"\",\"\"],\"coMapping\":[[],[],[],[],[],[]],\"programMapping\":[]},{\"semesterNo\":\"\",\"courseTitle\":\"\",\"courseCode\":\"\",\"cos\":[\"\",\"\",\"\",\"\",\"\",\"\"],\"coMapping\":[[],[],[],[],[],[]],\"programMapping\":[]},{\"semesterNo\":\"\",\"courseTitle\":\"\",\"courseCode\":\"\",\"cos\":[\"\",\"\",\"\",\"\",\"\",\"\"],\"coMapping\":[[],[],[],[],[],[]],\"programMapping\":[]},{\"semesterNo\":\"\",\"courseTitle\":\"\",\"courseCode\":\"\",\"cos\":[\"\",\"\",\"\",\"\",\"\",\"\"],\"coMapping\":[[],[],[],[],[],[]],\"programMapping\":[]}]},\"school_school_of_advanced_computing\":{\"vision\":\"\",\"missions\":[{\"id\":\"M1\",\"text\":\"\"}]}},\"peos\":[{\"id\":\"PEO1\",\"text\":\"\"}],\"matrix\":[]}', '2026-10-29 07:58:09'),
(6, 4, 2, '{\"levelData\":{\"dept_1\":{\"pdf_2_1\":\"\",\"pdf_2_2\":\"\",\"pdf_2_3\":\"\",\"pdf_2_4\":\"\",\"pdf_2_5\":\"\",\"pdf_2_6\":\"\",\"pdf_2_7\":\"\",\"pdf_2_8\":\"\"}},\"final_pdf_file\":\"final_criterion2_dept_1_2026_27_1793262141.pdf\",\"final_pdf_url\":\"http:\\/\\/localhost\\/mini\\/FMS\\/public\\/index.php?route=reports\\/download&type=nba&file=final_criterion2_dept_1_2026_27_1793262141.pdf\",\"final_pdf_generated_at\":\"2026-10-29 09:22:21\"}', '2026-10-29 08:22:21'),
(7, 4, 9, '{\"levelData\":{\"dept_1\":{\"inst_budget\":[],\"inst_expenditure\":[],\"prog_budget\":[],\"prog_expenditure\":[],\"t91_s4_cay\":\"\",\"t91_ns1_cay\":\"\",\"t91_ns2_cay\":\"\",\"t91_s4_caym1\":\"\",\"t91_ns1_caym1\":\"\",\"t91_ns2_caym1\":\"\",\"t91_s4_caym2\":\"\",\"t91_ns1_caym2\":\"\",\"t91_ns2_caym2\":\"\",\"text_9_2\":\"\",\"text_9_3_1\":\"\",\"text_9_3_2\":\"\",\"text_9_4\":\"\",\"text_9_5\":\"\",\"text_9_6_1\":\"\",\"text_9_6_2\":\"\",\"text_9_6_3\":\"\",\"text_9_9\":\"\",\"text_9_10\":\"\",\"text_9_11\":\"\",\"text_9_12\":\"\",\"text_9_13\":\"\",\"text_9_14\":\"\"}}}', '2026-10-29 08:55:36');

-- --------------------------------------------------------

--
-- Table structure for table `nba_criteria_files`
--

CREATE TABLE `nba_criteria_files` (
  `id` int(11) NOT NULL,
  `submission_id` int(11) NOT NULL,
  `criterion_number` int(11) NOT NULL,
  `subsection` varchar(20) NOT NULL,
  `file_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `nba_submissions`
--

CREATE TABLE `nba_submissions` (
  `submission_id` int(11) NOT NULL,
  `dept_id` int(11) NOT NULL,
  `academic_year` varchar(20) NOT NULL,
  `status` enum('Draft','Submitted','Approved') NOT NULL DEFAULT 'Draft',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `nba_submissions`
--

INSERT INTO `nba_submissions` (`submission_id`, `dept_id`, `academic_year`, `status`, `created_at`, `updated_at`) VALUES
(1, 20, '2025-26', 'Draft', '2026-09-22 05:19:33', '2026-09-22 05:19:33'),
(2, 1, '2024-2025', 'Draft', '2026-10-18 14:56:56', '2026-10-18 14:56:56'),
(3, 1, '2023-24', 'Draft', '2026-10-18 17:25:51', '2026-10-18 17:25:51'),
(4, 1, '2026-27', 'Draft', '2026-10-29 07:58:09', '2026-10-29 07:58:09');

-- --------------------------------------------------------

--
-- Table structure for table `patents_table`
--

CREATE TABLE `patents_table` (
  `id` int(255) NOT NULL,
  `Username` varchar(300) NOT NULL,
  `branch` varchar(100) NOT NULL,
  `patent_title` varchar(300) NOT NULL,
  `type` varchar(100) DEFAULT NULL,
  `date_of_issue` varchar(100) NOT NULL,
  `patent_file` varchar(400) NOT NULL,
  `submission_time` varchar(300) NOT NULL,
  `year` varchar(255) NOT NULL,
  `status` varchar(50) DEFAULT 'Pending HOD',
  `rejection_reason` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `rejection_reason` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `role_id` int(11) NOT NULL,
  `role_name` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`role_id`, `role_name`, `description`) VALUES
(1, 'Admin', 'System administrator'),
(2, 'IQAC', 'IQAC coordinator'),
(3, 'HOD', 'Head of Department'),
(4, 'Faculty', 'Faculty member'),
(5, 'Coordinator', 'Department/Cell coordinator'),
(6, 'Central_Coordinator', 'Central repository coordinator'),
(7, 'Junior_Assistant', 'Administrative assistant'),
(8, 'RnD_Dean', 'Dean of Research and Development'),
(9, 'Student', 'Student Role');

-- --------------------------------------------------------

--
-- Table structure for table `schema_migrations`
--

CREATE TABLE `schema_migrations` (
  `id` int(11) NOT NULL,
  `migration` varchar(255) NOT NULL,
  `applied_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `schema_migrations`
--

INSERT INTO `schema_migrations` (`id`, `migration`, `applied_at`) VALUES
(1, '001_create_target_schema.sql', '2026-11-01 03:39:05'),
(2, '002_intra_college.sql', '2026-11-01 03:39:05'),
(3, '002_workflow_state_machine.sql', '2026-11-01 03:39:05'),
(4, '003_create_gmrdufms.sql', '2026-11-01 03:39:05'),
(5, '004_create_user_profiles.sql', '2026-11-01 03:39:05'),
(6, '005_create_nba_tables.sql', '2026-11-01 03:39:05'),
(7, '006_create_naac_tables.sql', '2026-11-01 03:39:05'),
(8, '007_alter_document_actions_action_column.sql', '2026-11-01 03:39:05'),
(9, '008_add_document_status_indexes.sql', '2026-11-01 03:39:05'),
(10, '009_add_complete_university.sql', '2026-11-01 03:39:05'),
(11, '010_update_departments_is_academic.sql', '2026-11-01 03:39:05'),
(12, '011_seed_all_department_accounts.sql', '2026-11-01 03:39:05'),
(13, '012_create_system_audit_logs.sql', '2026-11-01 03:39:05'),
(14, '013_academic_years_departments_mgmt.sql', '2026-11-01 03:39:05');

-- --------------------------------------------------------

--
-- Table structure for table `system_audit_logs`
--

CREATE TABLE `system_audit_logs` (
  `log_id` int(11) NOT NULL,
  `action` varchar(60) NOT NULL,
  `actor_id` int(11) NOT NULL,
  `target_type` varchar(50) NOT NULL DEFAULT 'system',
  `target_id` int(11) DEFAULT NULL,
  `details` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `system_audit_logs`
--

INSERT INTO `system_audit_logs` (`log_id`, `action`, `actor_id`, `target_type`, `target_id`, `details`, `ip_address`, `created_at`) VALUES
(11, 'USER_CREATE', 1, 'user', 119, 'Created faculty account for Test Automated Professor (test_auto_prof_6ae4dc414d49e@gmrit.edu.in) in department #1 with initial role faculty.', NULL, '2026-10-30 23:34:49'),
(12, 'COORDINATOR_APPOINT', 1, 'user', 119, 'Appointed Test Automated Professor as Coordinator for CSE.', NULL, '2026-10-30 23:34:49'),
(13, 'COORDINATOR_REVOKE', 1, 'user', 119, 'Revoked Coordinator role from Test Automated Professor for CSE.', NULL, '2026-10-30 23:34:49'),
(14, 'HOD_ASSIGN', 1, 'user', 119, 'Appointed Test Automated Professor as HOD of CSE.', NULL, '2026-10-30 23:34:49'),
(15, 'USER_STATUS_CHANGE', 1, 'user', 119, 'Changed account status for Test Automated Professor (test_auto_prof_6ae4dc414d49e@gmrit.edu.in) to inactive.', NULL, '2026-10-30 23:34:49'),
(16, 'PASSWORD_RESET', 1, 'user', 119, 'Reset password for Test Automated Professor (test_auto_prof_6ae4dc414d49e@gmrit.edu.in) to default.', NULL, '2026-10-30 23:34:49'),
(17, 'USER_DELETE', 1, 'user', 119, 'Permanently deleted test user Test Automated Professor (test_auto_prof_6ae4dc414d49e@gmrit.edu.in).', NULL, '2026-10-30 23:34:49'),
(18, 'USER_DELETE', 1, 'user', 119, 'Permanently deleted test user Test Automated Professor (test_auto_prof_6ae4dc414d49e@gmrit.edu.in).', NULL, '2026-10-30 23:34:49'),
(19, 'DOCUMENT_PURGE', 1, 'document', 66, 'Permanently purged Document #66 (\'Auto Test Document For Deletion\') and 1 storage file(s).', NULL, '2026-10-30 23:34:55'),
(20, 'ACADEMIC_YEAR_CREATE', 45, 'academic_year', 6, 'Added new academic year \'2028-29\' (ID #6)', NULL, '2026-10-30 23:48:07'),
(21, 'ACADEMIC_YEAR_SET_CURRENT', 45, 'academic_year', 6, 'Designated Academic Year \'2028-29\' as the current active institutional year', NULL, '2026-10-30 23:48:07'),
(22, 'ACADEMIC_YEAR_DELETE', 45, 'academic_year', 6, 'Deleted unused academic year \'2028-29\' (ID #6)', NULL, '2026-10-30 23:48:07'),
(23, 'DEPARTMENT_CREATE', 45, 'department', 31, 'Created department \'Test_BioTech_Cell_4d60\' (ID #31, academic=1)', NULL, '2026-10-30 23:48:07'),
(24, 'DEPARTMENT_TOGGLE_ACADEMIC', 45, 'department', 31, 'Changed classification of \'Test_BioTech_Cell_4d60\' to Administrative / Support Unit', NULL, '2026-10-30 23:48:07'),
(25, 'DEPARTMENT_DELETE', 45, 'department', 31, 'Deleted empty department \'Test_BioTech_Cell_4d60\' (ID #31)', NULL, '2026-10-30 23:48:07'),
(26, 'ACADEMIC_YEAR_CREATE', 45, 'academic_year', 7, 'Added new academic year \'2028-29\' (ID #7)', NULL, '2026-10-30 23:49:06'),
(27, 'ACADEMIC_YEAR_SET_CURRENT', 45, 'academic_year', 7, 'Designated Academic Year \'2028-29\' as the current active institutional year', NULL, '2026-10-30 23:49:06'),
(28, 'ACADEMIC_YEAR_DELETE', 45, 'academic_year', 7, 'Deleted unused academic year \'2028-29\' (ID #7)', NULL, '2026-10-30 23:49:06'),
(29, 'DEPARTMENT_CREATE', 45, 'department', 32, 'Created department \'Test_BioTech_Cell_29a4\' (ID #32, academic=1)', NULL, '2026-10-30 23:49:06'),
(30, 'DEPARTMENT_TOGGLE_ACADEMIC', 45, 'department', 32, 'Changed classification of \'Test_BioTech_Cell_29a4\' to Administrative / Support Unit', NULL, '2026-10-30 23:49:06'),
(31, 'DEPARTMENT_DELETE', 45, 'department', 32, 'Deleted empty department \'Test_BioTech_Cell_29a4\' (ID #32)', NULL, '2026-10-30 23:49:06'),
(32, 'USER_CREATE', 1, 'user', 120, 'Created faculty account for Test Automated Professor (test_auto_prof_6ae4dfa05d194@gmrit.edu.in) in department #1 with initial role faculty.', NULL, '2026-10-30 23:49:12'),
(33, 'COORDINATOR_APPOINT', 1, 'user', 120, 'Appointed Test Automated Professor as Coordinator for CSE.', NULL, '2026-10-30 23:49:12'),
(34, 'COORDINATOR_REVOKE', 1, 'user', 120, 'Revoked Coordinator role from Test Automated Professor for CSE.', NULL, '2026-10-30 23:49:12'),
(35, 'HOD_ASSIGN', 1, 'user', 120, 'Appointed Test Automated Professor as HOD of CSE.', NULL, '2026-10-30 23:49:12'),
(36, 'USER_STATUS_CHANGE', 1, 'user', 120, 'Changed account status for Test Automated Professor (test_auto_prof_6ae4dfa05d194@gmrit.edu.in) to inactive.', NULL, '2026-10-30 23:49:12'),
(37, 'PASSWORD_RESET', 1, 'user', 120, 'Reset password for Test Automated Professor (test_auto_prof_6ae4dfa05d194@gmrit.edu.in) to default.', NULL, '2026-10-30 23:49:12'),
(38, 'USER_DELETE', 1, 'user', 120, 'Permanently deleted test user Test Automated Professor (test_auto_prof_6ae4dfa05d194@gmrit.edu.in).', NULL, '2026-10-30 23:49:12'),
(39, 'USER_DELETE', 1, 'user', 120, 'Permanently deleted test user Test Automated Professor (test_auto_prof_6ae4dfa05d194@gmrit.edu.in).', NULL, '2026-10-30 23:49:12'),
(40, 'DOCUMENT_PURGE', 1, 'document', 68, 'Permanently purged Document #68 (\'Auto Test Document For Deletion\') and 1 storage file(s).', NULL, '2026-10-30 23:49:39'),
(42, 'ACADEMIC_YEAR_CREATE', 45, 'academic_year', 8, 'Added new academic year \'2028-29\' (ID #8)', NULL, '2026-10-30 23:55:00'),
(43, 'ACADEMIC_YEAR_SET_CURRENT', 45, 'academic_year', 8, 'Designated Academic Year \'2028-29\' as the current active institutional year', NULL, '2026-10-30 23:55:00'),
(44, 'ACADEMIC_YEAR_DELETE', 45, 'academic_year', 8, 'Deleted unused academic year \'2028-29\' (ID #8)', NULL, '2026-10-30 23:55:00'),
(45, 'DEPARTMENT_CREATE', 45, 'department', 33, 'Created department \'Test_BioTech_Cell_0709\' (ID #33, academic=1)', NULL, '2026-10-30 23:55:00'),
(46, 'DEPARTMENT_TOGGLE_ACADEMIC', 45, 'department', 33, 'Changed classification of \'Test_BioTech_Cell_0709\' to Administrative / Support Unit', NULL, '2026-10-30 23:55:00'),
(47, 'DEPARTMENT_DELETE', 45, 'department', 33, 'Deleted empty department \'Test_BioTech_Cell_0709\' (ID #33)', NULL, '2026-10-30 23:55:00'),
(48, 'DEPARTMENT_TOGGLE_ACADEMIC', 45, 'department', 10, 'Changed classification of \'CSE-CS\' to Administrative / Support Unit', '::1', '2026-10-31 00:05:19'),
(49, 'USER_CREATE', 1, 'user', 121, 'Created faculty account for Test Automated Professor (test_auto_prof_6ae4e399910b4@gmrit.edu.in) in department #1 with initial role faculty.', NULL, '2026-10-31 00:06:09'),
(50, 'COORDINATOR_APPOINT', 1, 'user', 121, 'Appointed Test Automated Professor as Coordinator for CSE.', NULL, '2026-10-31 00:06:09'),
(51, 'COORDINATOR_REVOKE', 1, 'user', 121, 'Revoked Coordinator role from Test Automated Professor for CSE.', NULL, '2026-10-31 00:06:09'),
(52, 'HOD_ASSIGN', 1, 'user', 121, 'Appointed Test Automated Professor as HOD of CSE.', NULL, '2026-10-31 00:06:09'),
(53, 'USER_STATUS_CHANGE', 1, 'user', 121, 'Changed account status for Test Automated Professor (test_auto_prof_6ae4e399910b4@gmrit.edu.in) to inactive.', NULL, '2026-10-31 00:06:09'),
(54, 'PASSWORD_RESET', 1, 'user', 121, 'Reset password for Test Automated Professor (test_auto_prof_6ae4e399910b4@gmrit.edu.in) to default.', NULL, '2026-10-31 00:06:09'),
(55, 'USER_DELETE', 1, 'user', 121, 'Permanently deleted test user Test Automated Professor (test_auto_prof_6ae4e399910b4@gmrit.edu.in).', NULL, '2026-10-31 00:06:10'),
(56, 'USER_DELETE', 1, 'user', 121, 'Permanently deleted test user Test Automated Professor (test_auto_prof_6ae4e399910b4@gmrit.edu.in).', NULL, '2026-10-31 00:06:10'),
(57, 'DOCUMENT_PURGE', 1, 'document', 70, 'Permanently purged Document #70 (\'Auto Test Document For Deletion\') and 1 storage file(s).', NULL, '2026-10-31 00:06:16'),
(59, 'ACADEMIC_YEAR_CREATE', 45, 'academic_year', 9, 'Added new academic year \'2028-29\' (ID #9)', NULL, '2026-10-31 00:06:30'),
(60, 'ACADEMIC_YEAR_SET_CURRENT', 45, 'academic_year', 9, 'Designated Academic Year \'2028-29\' as the current active institutional year', NULL, '2026-10-31 00:06:30'),
(61, 'ACADEMIC_YEAR_DELETE', 45, 'academic_year', 9, 'Deleted unused academic year \'2028-29\' (ID #9)', NULL, '2026-10-31 00:06:30'),
(62, 'DEPARTMENT_CREATE', 45, 'department', 34, 'Created department \'Test_BioTech_Cell_9f8f\' (ID #34, academic=1)', NULL, '2026-10-31 00:06:30'),
(63, 'DEPARTMENT_TOGGLE_ACADEMIC', 45, 'department', 34, 'Changed classification of \'Test_BioTech_Cell_9f8f\' to Administrative / Support Unit', NULL, '2026-10-31 00:06:30'),
(64, 'DEPARTMENT_DELETE', 45, 'department', 34, 'Deleted empty department \'Test_BioTech_Cell_9f8f\' (ID #34)', NULL, '2026-10-31 00:06:30'),
(65, 'INSTITUTIONAL_ASSIGN', 45, 'user', 122, 'Appointed Test Faculty One as IQAC Coordinator from IQAC Coordinator college-wide.', NULL, '2026-10-31 20:04:04'),
(66, 'INSTITUTIONAL_ASSIGN', 45, 'user', 123, 'Appointed Test Faculty Two as IQAC Coordinator from Test Faculty One college-wide.', NULL, '2026-10-31 20:04:04'),
(67, 'INSTITUTIONAL_ASSIGN', 45, 'user', 122, 'Appointed Test Faculty One as Central Coordinator from central_cord, central_cord1, central_cord2, central_cord3, central_cord4, central_cord5, central_cord6, central_cord7, central_cord8, central_cord9, central_cord10, Central Coordinator college-wide.', NULL, '2026-10-31 20:04:05'),
(68, 'INSTITUTIONAL_ASSIGN', 45, 'user', 123, 'Appointed Test Faculty Two as Central Coordinator from Test Faculty One college-wide.', NULL, '2026-10-31 20:04:05'),
(69, 'INSTITUTIONAL_ASSIGN', 45, 'user', 122, 'Appointed Test Faculty One as Dean of R&D from Dean R&D college-wide.', NULL, '2026-10-31 20:04:05'),
(70, 'INSTITUTIONAL_RELIEVE', 45, 'user', 123, 'Relieved Test Faculty Two from IQAC upon appointment as HOD.', NULL, '2026-10-31 20:04:05'),
(71, 'INSTITUTIONAL_RELIEVE', 45, 'user', 123, 'Relieved Test Faculty Two from Central_Coordinator upon appointment as HOD.', NULL, '2026-10-31 20:04:05'),
(72, 'HOD_ASSIGN', 45, 'user', 123, 'Appointed Test Faculty Two as HOD of CSE.', NULL, '2026-10-31 20:04:05'),
(73, 'HOD_RELIEVE', 45, 'user', 123, 'Relieved Test Faculty Two from HOD of CSE upon appointment as Dean of R&D.', NULL, '2026-10-31 20:04:05'),
(74, 'INSTITUTIONAL_ASSIGN', 45, 'user', 123, 'Appointed Test Faculty Two as Dean of R&D from Test Faculty One college-wide.', NULL, '2026-10-31 20:04:05'),
(75, 'INSTITUTIONAL_RELIEVE', 45, 'user', 123, 'Relieved Test Faculty Two from RnD_Dean upon appointment as HOD.', NULL, '2026-10-31 20:04:05'),
(76, 'HOD_ASSIGN', 45, 'user', 123, 'Appointed Test Faculty Two as HOD of CSE.', NULL, '2026-10-31 20:04:05'),
(77, 'DEPARTMENT_TRANSFER', 45, 'user', 122, 'Transferred faculty member Test Faculty One from CSE to AIDS.', NULL, '2026-10-31 20:04:05'),
(78, 'USER_CREATE', 45, 'user', 124, 'Created faculty account for Dr. New IQAC Lead (test.new.iqac@gmrit.edu.in) in department #1 with initial role iqac.', NULL, '2026-10-31 20:04:06'),
(79, 'ROLE_REVOKE', 45, 'user', 124, 'Revoked IQAC role from Dr. New IQAC Lead (test.new.iqac@gmrit.edu.in).', NULL, '2026-10-31 20:04:06'),
(80, 'INSTITUTIONAL_ASSIGN', 45, 'user', 0, 'Appointed  as IQAC Coordinator college-wide.', NULL, '2026-10-31 20:09:25'),
(81, 'INSTITUTIONAL_ASSIGN', 45, 'user', 131, 'Appointed Test Faculty One as IQAC Coordinator college-wide.', NULL, '2026-10-31 20:18:02'),
(82, 'INSTITUTIONAL_ASSIGN', 45, 'user', 132, 'Appointed Test Faculty Two as IQAC Coordinator from Test Faculty One college-wide.', NULL, '2026-10-31 20:18:02'),
(83, 'INSTITUTIONAL_ASSIGN', 45, 'user', 131, 'Appointed Test Faculty One as Central Coordinator college-wide.', NULL, '2026-10-31 20:18:02'),
(84, 'INSTITUTIONAL_ASSIGN', 45, 'user', 132, 'Appointed Test Faculty Two as Central Coordinator from Test Faculty One college-wide.', NULL, '2026-10-31 20:18:02'),
(85, 'INSTITUTIONAL_ASSIGN', 45, 'user', 131, 'Appointed Test Faculty One as Dean of R&D college-wide.', NULL, '2026-10-31 20:18:03'),
(86, 'INSTITUTIONAL_RELIEVE', 45, 'user', 132, 'Relieved Test Faculty Two from IQAC upon appointment as HOD.', NULL, '2026-10-31 20:18:03'),
(87, 'INSTITUTIONAL_RELIEVE', 45, 'user', 132, 'Relieved Test Faculty Two from Central_Coordinator upon appointment as HOD.', NULL, '2026-10-31 20:18:03'),
(88, 'HOD_ASSIGN', 45, 'user', 132, 'Appointed Test Faculty Two as HOD of CSE.', NULL, '2026-10-31 20:18:03'),
(89, 'HOD_RELIEVE', 45, 'user', 132, 'Relieved Test Faculty Two from HOD of CSE upon appointment as Dean of R&D.', NULL, '2026-10-31 20:18:03'),
(90, 'INSTITUTIONAL_ASSIGN', 45, 'user', 132, 'Appointed Test Faculty Two as Dean of R&D from Test Faculty One college-wide.', NULL, '2026-10-31 20:18:03'),
(91, 'INSTITUTIONAL_RELIEVE', 45, 'user', 132, 'Relieved Test Faculty Two from RnD_Dean upon appointment as HOD.', NULL, '2026-10-31 20:18:03'),
(92, 'HOD_ASSIGN', 45, 'user', 132, 'Appointed Test Faculty Two as HOD of CSE.', NULL, '2026-10-31 20:18:03'),
(93, 'DEPARTMENT_TRANSFER', 45, 'user', 131, 'Transferred faculty member Test Faculty One from CSE to AIDS.', NULL, '2026-10-31 20:18:03'),
(94, 'USER_CREATE', 45, 'user', 133, 'Created faculty account for Dr. New IQAC Lead (test.new.iqac@gmrit.edu.in) in department #1 with initial role iqac.', NULL, '2026-10-31 20:18:03'),
(95, 'ROLE_REVOKE', 45, 'user', 133, 'Revoked IQAC role from Dr. New IQAC Lead (test.new.iqac@gmrit.edu.in).', NULL, '2026-10-31 20:18:03'),
(96, 'USER_CREATE', 1, 'user', 134, 'Created faculty account for Test Automated Professor (test_auto_prof_6ae5ffbc40e14@gmrit.edu.in) in department #1 with initial role faculty.', NULL, '2026-10-31 20:18:28'),
(97, 'COORDINATOR_APPOINT', 1, 'user', 134, 'Appointed Test Automated Professor as Coordinator for CSE.', NULL, '2026-10-31 20:18:28'),
(98, 'COORDINATOR_REVOKE', 1, 'user', 134, 'Revoked Coordinator role from Test Automated Professor for CSE.', NULL, '2026-10-31 20:18:28'),
(99, 'HOD_ASSIGN', 1, 'user', 134, 'Appointed Test Automated Professor as HOD of CSE.', NULL, '2026-10-31 20:18:28'),
(100, 'USER_STATUS_CHANGE', 1, 'user', 134, 'Changed account status for Test Automated Professor (test_auto_prof_6ae5ffbc40e14@gmrit.edu.in) to inactive.', NULL, '2026-10-31 20:18:28'),
(101, 'PASSWORD_RESET', 1, 'user', 134, 'Reset password for Test Automated Professor (test_auto_prof_6ae5ffbc40e14@gmrit.edu.in) to default.', NULL, '2026-10-31 20:18:28'),
(102, 'USER_DELETE', 1, 'user', 134, 'Permanently deleted test user Test Automated Professor (test_auto_prof_6ae5ffbc40e14@gmrit.edu.in).', NULL, '2026-10-31 20:18:29'),
(103, 'USER_DELETE', 1, 'user', 134, 'Permanently deleted test user Test Automated Professor (test_auto_prof_6ae5ffbc40e14@gmrit.edu.in).', NULL, '2026-10-31 20:18:29'),
(104, 'DOCUMENT_PURGE', 1, 'document', 72, 'Permanently purged Document #72 (\'Auto Test Document For Deletion\') and 1 storage file(s).', NULL, '2026-10-31 20:18:55'),
(106, 'ACADEMIC_YEAR_CREATE', 45, 'academic_year', 10, 'Added new academic year \'2028-29\' (ID #10)', NULL, '2026-10-31 20:19:37'),
(107, 'ACADEMIC_YEAR_SET_CURRENT', 45, 'academic_year', 10, 'Designated Academic Year \'2028-29\' as the current active institutional year', NULL, '2026-10-31 20:19:37'),
(108, 'ACADEMIC_YEAR_DELETE', 45, 'academic_year', 10, 'Deleted unused academic year \'2028-29\' (ID #10)', NULL, '2026-10-31 20:19:38'),
(109, 'DEPARTMENT_CREATE', 45, 'department', 35, 'Created department \'Test_BioTech_Cell_6460\' (ID #35, academic=1)', NULL, '2026-10-31 20:19:38'),
(110, 'DEPARTMENT_TOGGLE_ACADEMIC', 45, 'department', 35, 'Changed classification of \'Test_BioTech_Cell_6460\' to Administrative / Support Unit', NULL, '2026-10-31 20:19:38'),
(111, 'DEPARTMENT_DELETE', 45, 'department', 35, 'Deleted empty department \'Test_BioTech_Cell_6460\' (ID #35)', NULL, '2026-10-31 20:19:38'),
(112, 'DEPARTMENT_TOGGLE_ACADEMIC', 45, 'department', 1, 'Changed classification of \'CSE\' to Administrative / Support Unit', '::1', '2026-10-31 21:16:37'),
(113, 'DEPARTMENT_TOGGLE_ACADEMIC', 45, 'department', 1, 'Changed classification of \'CSE\' to Academic Teaching Department', '::1', '2026-10-31 21:16:47'),
(114, 'ACADEMIC_YEAR_SET_CURRENT', 45, 'academic_year', 5, 'Designated Academic Year \'2026-27\' as the current active institutional year', '::1', '2026-10-31 21:17:15'),
(116, 'INSTITUTIONAL_ASSIGN', 45, 'user', 135, 'Appointed Test Faculty One as IQAC Coordinator college-wide.', NULL, '2026-10-31 23:25:22'),
(117, 'INSTITUTIONAL_ASSIGN', 45, 'user', 136, 'Appointed Test Faculty Two as IQAC Coordinator from Test Faculty One college-wide.', NULL, '2026-10-31 23:25:22'),
(118, 'INSTITUTIONAL_ASSIGN', 45, 'user', 135, 'Appointed Test Faculty One as Central Coordinator college-wide.', NULL, '2026-10-31 23:25:22'),
(119, 'INSTITUTIONAL_ASSIGN', 45, 'user', 136, 'Appointed Test Faculty Two as Central Coordinator from Test Faculty One college-wide.', NULL, '2026-10-31 23:25:22'),
(120, 'INSTITUTIONAL_ASSIGN', 45, 'user', 135, 'Appointed Test Faculty One as Dean of R&D college-wide.', NULL, '2026-10-31 23:25:22'),
(121, 'INSTITUTIONAL_RELIEVE', 45, 'user', 136, 'Relieved Test Faculty Two from IQAC upon appointment as HOD.', NULL, '2026-10-31 23:25:22'),
(122, 'INSTITUTIONAL_RELIEVE', 45, 'user', 136, 'Relieved Test Faculty Two from Central_Coordinator upon appointment as HOD.', NULL, '2026-10-31 23:25:22'),
(123, 'HOD_ASSIGN', 45, 'user', 136, 'Appointed Test Faculty Two as HOD of CSE.', NULL, '2026-10-31 23:25:22'),
(124, 'HOD_RELIEVE', 45, 'user', 136, 'Relieved Test Faculty Two from HOD of CSE upon appointment as Dean of R&D.', NULL, '2026-10-31 23:25:22'),
(125, 'INSTITUTIONAL_ASSIGN', 45, 'user', 136, 'Appointed Test Faculty Two as Dean of R&D from Test Faculty One college-wide.', NULL, '2026-10-31 23:25:22'),
(126, 'INSTITUTIONAL_RELIEVE', 45, 'user', 136, 'Relieved Test Faculty Two from RnD_Dean upon appointment as HOD.', NULL, '2026-10-31 23:25:22'),
(127, 'HOD_ASSIGN', 45, 'user', 136, 'Appointed Test Faculty Two as HOD of CSE.', NULL, '2026-10-31 23:25:22'),
(128, 'DEPARTMENT_TRANSFER', 45, 'user', 135, 'Transferred faculty member Test Faculty One from CSE to AIDS.', NULL, '2026-10-31 23:25:22'),
(129, 'USER_CREATE', 45, 'user', 137, 'Created faculty account for Dr. New IQAC Lead (test.new.iqac@gmrit.edu.in) in department #1 with initial role iqac.', NULL, '2026-10-31 23:25:22'),
(130, 'ROLE_REVOKE', 45, 'user', 137, 'Revoked IQAC role from Dr. New IQAC Lead (test.new.iqac@gmrit.edu.in).', NULL, '2026-10-31 23:25:22'),
(131, 'ACADEMIC_YEAR_CREATE', 45, 'academic_year', 11, 'Added new academic year \'2028-29\' (ID #11)', NULL, '2026-10-31 23:26:32'),
(132, 'ACADEMIC_YEAR_CREATE', 45, 'academic_year', 12, 'Added new academic year \'2028-29\' (ID #12)', NULL, '2026-10-31 23:26:58'),
(133, 'ACADEMIC_YEAR_CREATE', 45, 'academic_year', 13, 'Added new academic year \'2028-35\' (ID #13)', NULL, '2026-10-31 23:26:58'),
(134, 'ACADEMIC_YEAR_CREATE', 45, 'academic_year', 14, 'Added new academic year \'2028-29\' (ID #14)', NULL, '2026-10-31 23:29:19'),
(135, 'ACADEMIC_YEAR_CREATE', 45, 'academic_year', 15, 'Added new academic year \'2028-29\' (ID #15)', NULL, '2026-10-31 23:29:44'),
(136, 'ACADEMIC_YEAR_SET_ACTIVE', 45, 'academic_year', 15, 'Designated \'2028-29\' (ID #15) as the institutional active academic year', NULL, '2026-10-31 23:29:44'),
(137, 'ACADEMIC_YEAR_DELETE', 45, 'academic_year', 15, 'Deleted unused academic year \'2028-29\' (ID #15)', NULL, '2026-10-31 23:29:44'),
(138, 'DEPARTMENT_CREATE', 45, 'department', 36, 'Created department \'Test_BioTech_Cell_dcf8\' (ID #36, academic=1)', NULL, '2026-10-31 23:29:44'),
(139, 'DEPARTMENT_TOGGLE_ACADEMIC', 45, 'department', 36, 'Changed classification of \'Test_BioTech_Cell_dcf8\' to Administrative / Support Unit', NULL, '2026-10-31 23:29:44'),
(140, 'DEPARTMENT_DELETE', 45, 'department', 36, 'Deleted empty department \'Test_BioTech_Cell_dcf8\' (ID #36)', NULL, '2026-10-31 23:29:44'),
(141, 'ACADEMIC_YEAR_CREATE', 45, 'academic_year', 16, 'Added new academic year \'2028-29\' (ID #16)', NULL, '2026-10-31 23:29:51'),
(142, 'ACADEMIC_YEAR_SET_ACTIVE', 45, 'academic_year', 16, 'Designated \'2028-29\' (ID #16) as the institutional active academic year', NULL, '2026-10-31 23:29:51'),
(143, 'ACADEMIC_YEAR_DELETE', 45, 'academic_year', 16, 'Deleted unused academic year \'2028-29\' (ID #16)', NULL, '2026-10-31 23:29:51'),
(144, 'DEPARTMENT_CREATE', 45, 'department', 37, 'Created department \'Test_BioTech_Cell_0f91\' (ID #37, academic=1)', NULL, '2026-10-31 23:29:51'),
(145, 'DEPARTMENT_TOGGLE_ACADEMIC', 45, 'department', 37, 'Changed classification of \'Test_BioTech_Cell_0f91\' to Administrative / Support Unit', NULL, '2026-10-31 23:29:51'),
(146, 'DEPARTMENT_DELETE', 45, 'department', 37, 'Deleted empty department \'Test_BioTech_Cell_0f91\' (ID #37)', NULL, '2026-10-31 23:29:51'),
(147, 'USER_STATUS_CHANGE', 1, 'user', 999, 'Automated test audit event 6ae62c9d8a5fb', NULL, '2026-10-31 23:29:57'),
(148, 'USER_CREATE', 1, 'user', 138, 'Created faculty account for Test Automated Professor (test_auto_prof_6ae62e10d4532@gmrit.edu.in) in department #1 with initial role faculty.', NULL, '2026-10-31 23:36:08'),
(149, 'COORDINATOR_APPOINT', 1, 'user', 138, 'Appointed Test Automated Professor as Coordinator for CSE.', NULL, '2026-10-31 23:36:08'),
(150, 'COORDINATOR_REVOKE', 1, 'user', 138, 'Revoked Coordinator role from Test Automated Professor for CSE.', NULL, '2026-10-31 23:36:08'),
(151, 'HOD_ASSIGN', 1, 'user', 138, 'Appointed Test Automated Professor as HOD of CSE.', NULL, '2026-10-31 23:36:08'),
(152, 'USER_STATUS_CHANGE', 1, 'user', 138, 'Changed account status for Test Automated Professor (test_auto_prof_6ae62e10d4532@gmrit.edu.in) to inactive.', NULL, '2026-10-31 23:36:08'),
(153, 'PASSWORD_RESET', 1, 'user', 138, 'Reset password for Test Automated Professor (test_auto_prof_6ae62e10d4532@gmrit.edu.in) to default.', NULL, '2026-10-31 23:36:09'),
(154, 'USER_DELETE', 1, 'user', 138, 'Permanently deleted test user Test Automated Professor (test_auto_prof_6ae62e10d4532@gmrit.edu.in).', NULL, '2026-10-31 23:36:09'),
(155, 'USER_DELETE', 1, 'user', 138, 'Permanently deleted test user Test Automated Professor (test_auto_prof_6ae62e10d4532@gmrit.edu.in).', NULL, '2026-10-31 23:36:09'),
(156, 'USER_CREATE', 1, 'user', 139, 'Created faculty account for Test Automated Professor (test_auto_prof_6ae62e79ee783@gmrit.edu.in) in department #1 with initial role faculty.', NULL, '2026-10-31 23:37:54'),
(157, 'COORDINATOR_APPOINT', 1, 'user', 139, 'Appointed Test Automated Professor as Coordinator for CSE.', NULL, '2026-10-31 23:37:54'),
(158, 'COORDINATOR_REVOKE', 1, 'user', 139, 'Revoked Coordinator role from Test Automated Professor for CSE.', NULL, '2026-10-31 23:37:54'),
(159, 'HOD_ASSIGN', 1, 'user', 139, 'Appointed Test Automated Professor as HOD of CSE.', NULL, '2026-10-31 23:37:54'),
(160, 'USER_STATUS_CHANGE', 1, 'user', 139, 'Changed account status for Test Automated Professor (test_auto_prof_6ae62e79ee783@gmrit.edu.in) to inactive.', NULL, '2026-10-31 23:37:54'),
(161, 'PASSWORD_RESET', 1, 'user', 139, 'Reset password for Test Automated Professor (test_auto_prof_6ae62e79ee783@gmrit.edu.in) to default.', NULL, '2026-10-31 23:37:54'),
(162, 'USER_DELETE', 1, 'user', 139, 'Permanently deleted test user Test Automated Professor (test_auto_prof_6ae62e79ee783@gmrit.edu.in).', NULL, '2026-10-31 23:37:54'),
(163, 'USER_DELETE', 1, 'user', 139, 'Permanently deleted test user Test Automated Professor (test_auto_prof_6ae62e79ee783@gmrit.edu.in).', NULL, '2026-10-31 23:37:54'),
(164, 'INSTITUTIONAL_ASSIGN', 45, 'user', 140, 'Appointed Test Faculty One as IQAC Coordinator college-wide.', NULL, '2026-10-31 23:38:00'),
(165, 'INSTITUTIONAL_ASSIGN', 45, 'user', 141, 'Appointed Test Faculty Two as IQAC Coordinator from Test Faculty One college-wide.', NULL, '2026-10-31 23:38:00'),
(166, 'INSTITUTIONAL_ASSIGN', 45, 'user', 140, 'Appointed Test Faculty One as Central Coordinator college-wide.', NULL, '2026-10-31 23:38:00'),
(167, 'INSTITUTIONAL_ASSIGN', 45, 'user', 141, 'Appointed Test Faculty Two as Central Coordinator from Test Faculty One college-wide.', NULL, '2026-10-31 23:38:00'),
(168, 'INSTITUTIONAL_ASSIGN', 45, 'user', 140, 'Appointed Test Faculty One as Dean of R&D college-wide.', NULL, '2026-10-31 23:38:00'),
(169, 'INSTITUTIONAL_RELIEVE', 45, 'user', 141, 'Relieved Test Faculty Two from IQAC upon appointment as HOD.', NULL, '2026-10-31 23:38:00'),
(170, 'INSTITUTIONAL_RELIEVE', 45, 'user', 141, 'Relieved Test Faculty Two from Central_Coordinator upon appointment as HOD.', NULL, '2026-10-31 23:38:00'),
(171, 'HOD_ASSIGN', 45, 'user', 141, 'Appointed Test Faculty Two as HOD of CSE.', NULL, '2026-10-31 23:38:00'),
(172, 'HOD_RELIEVE', 45, 'user', 141, 'Relieved Test Faculty Two from HOD of CSE upon appointment as Dean of R&D.', NULL, '2026-10-31 23:38:00'),
(173, 'INSTITUTIONAL_ASSIGN', 45, 'user', 141, 'Appointed Test Faculty Two as Dean of R&D from Test Faculty One college-wide.', NULL, '2026-10-31 23:38:00'),
(174, 'INSTITUTIONAL_RELIEVE', 45, 'user', 141, 'Relieved Test Faculty Two from RnD_Dean upon appointment as HOD.', NULL, '2026-10-31 23:38:00'),
(175, 'HOD_ASSIGN', 45, 'user', 141, 'Appointed Test Faculty Two as HOD of CSE.', NULL, '2026-10-31 23:38:00'),
(176, 'DEPARTMENT_TRANSFER', 45, 'user', 140, 'Transferred faculty member Test Faculty One from CSE to AIDS.', NULL, '2026-10-31 23:38:00'),
(177, 'USER_CREATE', 45, 'user', 142, 'Created faculty account for Dr. New IQAC Lead (test.new.iqac@gmrit.edu.in) in department #1 with initial role iqac.', NULL, '2026-10-31 23:38:00'),
(178, 'ROLE_REVOKE', 45, 'user', 142, 'Revoked IQAC role from Dr. New IQAC Lead (test.new.iqac@gmrit.edu.in).', NULL, '2026-10-31 23:38:00'),
(179, 'USER_CREATE', 1, 'user', 143, 'Created faculty account for Test Automated Professor (test_auto_prof_6ae630534a607@gmrit.edu.in) in department #1 with initial role faculty.', NULL, '2026-10-31 23:45:47'),
(180, 'COORDINATOR_APPOINT', 1, 'user', 143, 'Appointed Test Automated Professor as Coordinator for CSE.', NULL, '2026-10-31 23:45:47'),
(181, 'COORDINATOR_REVOKE', 1, 'user', 143, 'Revoked Coordinator role from Test Automated Professor for CSE.', NULL, '2026-10-31 23:45:47'),
(182, 'HOD_ASSIGN', 1, 'user', 143, 'Appointed Test Automated Professor as HOD of CSE.', NULL, '2026-10-31 23:45:47'),
(183, 'USER_STATUS_CHANGE', 1, 'user', 143, 'Changed account status for Test Automated Professor (test_auto_prof_6ae630534a607@gmrit.edu.in) to inactive.', NULL, '2026-10-31 23:45:47'),
(184, 'PASSWORD_RESET', 1, 'user', 143, 'Reset password for Test Automated Professor (test_auto_prof_6ae630534a607@gmrit.edu.in) to default.', NULL, '2026-10-31 23:45:47'),
(185, 'USER_DELETE', 1, 'user', 143, 'Permanently deleted test user Test Automated Professor (test_auto_prof_6ae630534a607@gmrit.edu.in).', NULL, '2026-10-31 23:45:47'),
(186, 'USER_DELETE', 1, 'user', 143, 'Permanently deleted test user Test Automated Professor (test_auto_prof_6ae630534a607@gmrit.edu.in).', NULL, '2026-10-31 23:45:47'),
(187, 'INSTITUTIONAL_ASSIGN', 45, 'user', 144, 'Appointed Test Faculty One as IQAC Coordinator college-wide.', NULL, '2026-10-31 23:45:54'),
(188, 'INSTITUTIONAL_ASSIGN', 45, 'user', 145, 'Appointed Test Faculty Two as IQAC Coordinator from Test Faculty One college-wide.', NULL, '2026-10-31 23:45:54'),
(189, 'INSTITUTIONAL_ASSIGN', 45, 'user', 144, 'Appointed Test Faculty One as Central Coordinator college-wide.', NULL, '2026-10-31 23:45:54'),
(190, 'INSTITUTIONAL_ASSIGN', 45, 'user', 145, 'Appointed Test Faculty Two as Central Coordinator from Test Faculty One college-wide.', NULL, '2026-10-31 23:45:54'),
(191, 'INSTITUTIONAL_ASSIGN', 45, 'user', 144, 'Appointed Test Faculty One as Dean of R&D college-wide.', NULL, '2026-10-31 23:45:54'),
(192, 'INSTITUTIONAL_RELIEVE', 45, 'user', 145, 'Relieved Test Faculty Two from IQAC upon appointment as HOD.', NULL, '2026-10-31 23:45:54'),
(193, 'INSTITUTIONAL_RELIEVE', 45, 'user', 145, 'Relieved Test Faculty Two from Central_Coordinator upon appointment as HOD.', NULL, '2026-10-31 23:45:54'),
(194, 'HOD_ASSIGN', 45, 'user', 145, 'Appointed Test Faculty Two as HOD of CSE.', NULL, '2026-10-31 23:45:54'),
(195, 'HOD_RELIEVE', 45, 'user', 145, 'Relieved Test Faculty Two from HOD of CSE upon appointment as Dean of R&D.', NULL, '2026-10-31 23:45:54'),
(196, 'INSTITUTIONAL_ASSIGN', 45, 'user', 145, 'Appointed Test Faculty Two as Dean of R&D from Test Faculty One college-wide.', NULL, '2026-10-31 23:45:54'),
(197, 'INSTITUTIONAL_RELIEVE', 45, 'user', 145, 'Relieved Test Faculty Two from RnD_Dean upon appointment as HOD.', NULL, '2026-10-31 23:45:54'),
(198, 'HOD_ASSIGN', 45, 'user', 145, 'Appointed Test Faculty Two as HOD of CSE.', NULL, '2026-10-31 23:45:54'),
(199, 'DEPARTMENT_TRANSFER', 45, 'user', 144, 'Transferred faculty member Test Faculty One from CSE to AIDS.', NULL, '2026-10-31 23:45:54'),
(200, 'USER_CREATE', 45, 'user', 146, 'Created faculty account for Dr. New IQAC Lead (test.new.iqac@gmrit.edu.in) in department #1 with initial role iqac.', NULL, '2026-10-31 23:45:54'),
(201, 'ROLE_REVOKE', 45, 'user', 146, 'Revoked IQAC role from Dr. New IQAC Lead (test.new.iqac@gmrit.edu.in).', NULL, '2026-10-31 23:45:54'),
(202, 'ACADEMIC_YEAR_CREATE', 45, 'academic_year', 17, 'Added new academic year \'2028-29\' (ID #17)', NULL, '2026-10-31 23:46:00'),
(203, 'ACADEMIC_YEAR_SET_ACTIVE', 45, 'academic_year', 17, 'Designated \'2028-29\' (ID #17) as the institutional active academic year', NULL, '2026-10-31 23:46:00'),
(204, 'ACADEMIC_YEAR_DELETE', 45, 'academic_year', 17, 'Deleted unused academic year \'2028-29\' (ID #17)', NULL, '2026-10-31 23:46:00'),
(205, 'DEPARTMENT_CREATE', 45, 'department', 38, 'Created department \'Test_BioTech_Cell_1cfc\' (ID #38, academic=1)', NULL, '2026-10-31 23:46:00'),
(206, 'DEPARTMENT_TOGGLE_ACADEMIC', 45, 'department', 38, 'Changed classification of \'Test_BioTech_Cell_1cfc\' to Administrative / Support Unit', NULL, '2026-10-31 23:46:00'),
(207, 'DEPARTMENT_DELETE', 45, 'department', 38, 'Deleted empty department \'Test_BioTech_Cell_1cfc\' (ID #38)', NULL, '2026-10-31 23:46:00'),
(208, 'USER_STATUS_CHANGE', 1, 'user', 999, 'Automated test audit event 6ae630651065e', NULL, '2026-10-31 23:46:05'),
(209, 'USER_CREATE', 1, 'user', 147, 'Created faculty account for Test Automated Professor (test_auto_prof_6ae699e041e5e@gmrit.edu.in) in department #1 with initial role faculty.', NULL, '2026-11-01 07:16:08'),
(210, 'COORDINATOR_APPOINT', 1, 'user', 147, 'Appointed Test Automated Professor as Coordinator for CSE.', NULL, '2026-11-01 07:16:08'),
(211, 'COORDINATOR_REVOKE', 1, 'user', 147, 'Revoked Coordinator role from Test Automated Professor for CSE.', NULL, '2026-11-01 07:16:08'),
(212, 'HOD_ASSIGN', 1, 'user', 147, 'Appointed Test Automated Professor as HOD of CSE.', NULL, '2026-11-01 07:16:08'),
(213, 'USER_STATUS_CHANGE', 1, 'user', 147, 'Changed account status for Test Automated Professor (test_auto_prof_6ae699e041e5e@gmrit.edu.in) to inactive.', NULL, '2026-11-01 07:16:08'),
(214, 'PASSWORD_RESET', 1, 'user', 147, 'Reset password for Test Automated Professor (test_auto_prof_6ae699e041e5e@gmrit.edu.in) to default.', NULL, '2026-11-01 07:16:08'),
(215, 'USER_DELETE', 1, 'user', 147, 'Permanently deleted test user Test Automated Professor (test_auto_prof_6ae699e041e5e@gmrit.edu.in).', NULL, '2026-11-01 07:16:08'),
(216, 'USER_DELETE', 1, 'user', 147, 'Permanently deleted test user Test Automated Professor (test_auto_prof_6ae699e041e5e@gmrit.edu.in).', NULL, '2026-11-01 07:16:08'),
(217, 'INSTITUTIONAL_ASSIGN', 45, 'user', 148, 'Appointed Test Faculty One as IQAC Coordinator college-wide.', NULL, '2026-11-01 07:16:49'),
(218, 'INSTITUTIONAL_ASSIGN', 45, 'user', 149, 'Appointed Test Faculty Two as IQAC Coordinator from Test Faculty One college-wide.', NULL, '2026-11-01 07:16:49'),
(219, 'INSTITUTIONAL_ASSIGN', 45, 'user', 148, 'Appointed Test Faculty One as Central Coordinator college-wide.', NULL, '2026-11-01 07:16:49'),
(220, 'INSTITUTIONAL_ASSIGN', 45, 'user', 149, 'Appointed Test Faculty Two as Central Coordinator from Test Faculty One college-wide.', NULL, '2026-11-01 07:16:49'),
(221, 'INSTITUTIONAL_ASSIGN', 45, 'user', 148, 'Appointed Test Faculty One as Dean of R&D college-wide.', NULL, '2026-11-01 07:16:49'),
(222, 'INSTITUTIONAL_RELIEVE', 45, 'user', 149, 'Relieved Test Faculty Two from IQAC upon appointment as HOD.', NULL, '2026-11-01 07:16:49'),
(223, 'INSTITUTIONAL_RELIEVE', 45, 'user', 149, 'Relieved Test Faculty Two from Central_Coordinator upon appointment as HOD.', NULL, '2026-11-01 07:16:49'),
(224, 'HOD_ASSIGN', 45, 'user', 149, 'Appointed Test Faculty Two as HOD of CSE.', NULL, '2026-11-01 07:16:49'),
(225, 'HOD_RELIEVE', 45, 'user', 149, 'Relieved Test Faculty Two from HOD of CSE upon appointment as Dean of R&D.', NULL, '2026-11-01 07:16:49'),
(226, 'INSTITUTIONAL_ASSIGN', 45, 'user', 149, 'Appointed Test Faculty Two as Dean of R&D from Test Faculty One college-wide.', NULL, '2026-11-01 07:16:49'),
(227, 'INSTITUTIONAL_RELIEVE', 45, 'user', 149, 'Relieved Test Faculty Two from RnD_Dean upon appointment as HOD.', NULL, '2026-11-01 07:16:49'),
(228, 'HOD_ASSIGN', 45, 'user', 149, 'Appointed Test Faculty Two as HOD of CSE.', NULL, '2026-11-01 07:16:49'),
(229, 'DEPARTMENT_TRANSFER', 45, 'user', 148, 'Transferred faculty member Test Faculty One from CSE to AIDS.', NULL, '2026-11-01 07:16:49'),
(230, 'USER_CREATE', 45, 'user', 150, 'Created faculty account for Dr. New IQAC Lead (test.new.iqac@gmrit.edu.in) in department #1 with initial role iqac.', NULL, '2026-11-01 07:16:49'),
(231, 'ROLE_REVOKE', 45, 'user', 150, 'Revoked IQAC role from Dr. New IQAC Lead (test.new.iqac@gmrit.edu.in).', NULL, '2026-11-01 07:16:49'),
(232, 'USER_CREATE', 1, 'user', 152, 'Created faculty account for Test Automated Professor (test_auto_prof_6ae6a2b8ddaa1@gmrit.edu.in) in department #1 with initial role faculty.', NULL, '2026-11-01 07:53:52'),
(233, 'COORDINATOR_APPOINT', 1, 'user', 152, 'Appointed Test Automated Professor as Coordinator for CSE.', NULL, '2026-11-01 07:53:53'),
(234, 'COORDINATOR_REVOKE', 1, 'user', 152, 'Revoked Coordinator role from Test Automated Professor for CSE.', NULL, '2026-11-01 07:53:53'),
(235, 'HOD_ASSIGN', 1, 'user', 152, 'Appointed Test Automated Professor as HOD of CSE.', NULL, '2026-11-01 07:53:53'),
(236, 'USER_STATUS_CHANGE', 1, 'user', 152, 'Changed account status for Test Automated Professor (test_auto_prof_6ae6a2b8ddaa1@gmrit.edu.in) to inactive.', NULL, '2026-11-01 07:53:53'),
(237, 'PASSWORD_RESET', 1, 'user', 152, 'Reset password for Test Automated Professor (test_auto_prof_6ae6a2b8ddaa1@gmrit.edu.in) to default.', NULL, '2026-11-01 07:53:53'),
(238, 'USER_DELETE', 1, 'user', 152, 'Permanently deleted test user Test Automated Professor (test_auto_prof_6ae6a2b8ddaa1@gmrit.edu.in).', NULL, '2026-11-01 07:53:53'),
(239, 'USER_DELETE', 1, 'user', 152, 'Permanently deleted test user Test Automated Professor (test_auto_prof_6ae6a2b8ddaa1@gmrit.edu.in).', NULL, '2026-11-01 07:53:53'),
(240, 'USER_CREATE', 1, 'user', 153, 'Created faculty account for Test Automated Professor (test_auto_prof_6ae6acd71b36f@gmrit.edu.in) in department #1 with initial role faculty.', NULL, '2026-11-01 08:37:03'),
(241, 'COORDINATOR_APPOINT', 1, 'user', 153, 'Appointed Test Automated Professor as Coordinator for CSE.', NULL, '2026-11-01 08:37:03'),
(242, 'COORDINATOR_REVOKE', 1, 'user', 153, 'Revoked Coordinator role from Test Automated Professor for CSE.', NULL, '2026-11-01 08:37:03'),
(243, 'HOD_ASSIGN', 1, 'user', 153, 'Appointed Test Automated Professor as HOD of CSE.', NULL, '2026-11-01 08:37:03'),
(244, 'USER_STATUS_CHANGE', 1, 'user', 153, 'Changed account status for Test Automated Professor (test_auto_prof_6ae6acd71b36f@gmrit.edu.in) to inactive.', NULL, '2026-11-01 08:37:03'),
(245, 'PASSWORD_RESET', 1, 'user', 153, 'Reset password for Test Automated Professor (test_auto_prof_6ae6acd71b36f@gmrit.edu.in) to default.', NULL, '2026-11-01 08:37:03'),
(246, 'USER_DELETE', 1, 'user', 153, 'Permanently deleted test user Test Automated Professor (test_auto_prof_6ae6acd71b36f@gmrit.edu.in).', NULL, '2026-11-01 08:37:03'),
(247, 'USER_DELETE', 1, 'user', 153, 'Permanently deleted test user Test Automated Professor (test_auto_prof_6ae6acd71b36f@gmrit.edu.in).', NULL, '2026-11-01 08:37:03'),
(248, 'USER_CREATE', 1, 'user', 154, 'Created faculty account for Test Automated Professor (test_auto_prof_6ae6bf1d32661@gmrit.edu.in) in department #1 with initial role faculty.', NULL, '2026-11-01 09:55:01'),
(249, 'COORDINATOR_APPOINT', 1, 'user', 154, 'Appointed Test Automated Professor as Coordinator for CSE.', NULL, '2026-11-01 09:55:01'),
(250, 'COORDINATOR_REVOKE', 1, 'user', 154, 'Revoked Coordinator role from Test Automated Professor for CSE.', NULL, '2026-11-01 09:55:01'),
(251, 'HOD_ASSIGN', 1, 'user', 154, 'Appointed Test Automated Professor as HOD of CSE.', NULL, '2026-11-01 09:55:01'),
(252, 'USER_STATUS_CHANGE', 1, 'user', 154, 'Changed account status for Test Automated Professor (test_auto_prof_6ae6bf1d32661@gmrit.edu.in) to inactive.', NULL, '2026-11-01 09:55:01'),
(253, 'PASSWORD_RESET', 1, 'user', 154, 'Reset password for Test Automated Professor (test_auto_prof_6ae6bf1d32661@gmrit.edu.in) to default.', NULL, '2026-11-01 09:55:01'),
(254, 'USER_DELETE', 1, 'user', 154, 'Permanently deleted test user Test Automated Professor (test_auto_prof_6ae6bf1d32661@gmrit.edu.in).', NULL, '2026-11-01 09:55:02'),
(255, 'USER_DELETE', 1, 'user', 154, 'Permanently deleted test user Test Automated Professor (test_auto_prof_6ae6bf1d32661@gmrit.edu.in).', NULL, '2026-11-01 09:55:02'),
(256, 'USER_CREATE', 1, 'user', 155, 'Created faculty account for  () in department # with initial role faculty.', NULL, '2026-11-01 10:04:18'),
(257, 'USER_CREATE', 1, 'user', 0, 'Created faculty account for  () in department # with initial role faculty.', NULL, '2026-11-01 10:04:19'),
(258, 'USER_CREATE', 1, 'user', 157, 'Created faculty account for Test Automated Professor (test_auto_prof_6ae6c180e3ba5@gmrit.edu.in) in department #1 with initial role faculty.', NULL, '2026-11-01 10:05:13'),
(259, 'USER_CREATE', 1, 'user', 158, 'Created faculty account for Test Automated Professor (test_auto_prof_6ae6c1ff437fa@gmrit.edu.in) in department #1 with initial role faculty.', NULL, '2026-11-01 10:07:19'),
(260, 'COORDINATOR_APPOINT', 1, 'user', 158, 'Appointed Test Automated Professor as Coordinator for CSE.', NULL, '2026-11-01 10:07:19'),
(261, 'COORDINATOR_REVOKE', 1, 'user', 158, 'Revoked Coordinator role from Test Automated Professor for CSE.', NULL, '2026-11-01 10:07:19'),
(262, 'HOD_ASSIGN', 1, 'user', 158, 'Appointed Test Automated Professor as HOD of CSE.', NULL, '2026-11-01 10:07:19'),
(263, 'USER_STATUS_CHANGE', 1, 'user', 158, 'Changed account status for Test Automated Professor (test_auto_prof_6ae6c1ff437fa@gmrit.edu.in) to inactive.', NULL, '2026-11-01 10:07:19'),
(264, 'PASSWORD_RESET', 1, 'user', 158, 'Reset password for Test Automated Professor (test_auto_prof_6ae6c1ff437fa@gmrit.edu.in) to default.', NULL, '2026-11-01 10:07:19'),
(265, 'USER_DELETE', 1, 'user', 158, 'Permanently deleted test user Test Automated Professor (test_auto_prof_6ae6c1ff437fa@gmrit.edu.in).', NULL, '2026-11-01 10:07:20'),
(266, 'USER_CREATE', 1, 'user', 159, 'Created faculty account for Test Automated Professor (test_auto_prof_6ae6c3bad50b0@gmrit.edu.in) in department #1 with initial role faculty.', NULL, '2026-11-01 10:14:43'),
(267, 'COORDINATOR_APPOINT', 1, 'user', 159, 'Appointed Test Automated Professor as Coordinator for CSE.', NULL, '2026-11-01 10:14:43'),
(268, 'COORDINATOR_REVOKE', 1, 'user', 159, 'Revoked Coordinator role from Test Automated Professor for CSE.', NULL, '2026-11-01 10:14:43'),
(269, 'HOD_ASSIGN', 1, 'user', 159, 'Appointed Test Automated Professor as HOD of CSE.', NULL, '2026-11-01 10:14:43'),
(270, 'USER_STATUS_CHANGE', 1, 'user', 159, 'Changed account status for Test Automated Professor (test_auto_prof_6ae6c3bad50b0@gmrit.edu.in) to inactive.', NULL, '2026-11-01 10:14:43'),
(271, 'PASSWORD_RESET', 1, 'user', 159, 'Reset password for Test Automated Professor (test_auto_prof_6ae6c3bad50b0@gmrit.edu.in) to default.', NULL, '2026-11-01 10:14:43'),
(272, 'USER_DELETE', 1, 'user', 159, 'Permanently deleted test user Test Automated Professor (test_auto_prof_6ae6c3bad50b0@gmrit.edu.in).', NULL, '2026-11-01 10:14:43'),
(273, 'USER_CREATE', 1, 'user', 160, 'Created faculty account for Test Automated Professor (test_auto_prof_6ae6ca2f86551@gmrit.edu.in) in department #1 with initial role faculty.', NULL, '2026-11-01 10:42:15'),
(274, 'COORDINATOR_APPOINT', 1, 'user', 160, 'Appointed Test Automated Professor as Coordinator for CSE.', NULL, '2026-11-01 10:42:15'),
(275, 'COORDINATOR_REVOKE', 1, 'user', 160, 'Revoked Coordinator role from Test Automated Professor for CSE.', NULL, '2026-11-01 10:42:15'),
(276, 'HOD_ASSIGN', 1, 'user', 160, 'Appointed Test Automated Professor as HOD of CSE.', NULL, '2026-11-01 10:42:15'),
(277, 'USER_STATUS_CHANGE', 1, 'user', 160, 'Changed account status for Test Automated Professor (test_auto_prof_6ae6ca2f86551@gmrit.edu.in) to inactive.', NULL, '2026-11-01 10:42:15'),
(278, 'PASSWORD_RESET', 1, 'user', 160, 'Reset password for Test Automated Professor (test_auto_prof_6ae6ca2f86551@gmrit.edu.in) to default.', NULL, '2026-11-01 10:42:16'),
(279, 'USER_DELETE', 1, 'user', 160, 'Permanently deleted test user Test Automated Professor (test_auto_prof_6ae6ca2f86551@gmrit.edu.in).', NULL, '2026-11-01 10:42:16'),
(280, 'USER_CREATE', 1, 'user', 161, 'Created faculty account for Test Automated Professor (test_auto_prof_6ae6ce5e08d7d@gmrit.edu.in) in department #1 with initial role faculty.', NULL, '2026-11-01 11:00:06'),
(281, 'COORDINATOR_APPOINT', 1, 'user', 161, 'Appointed Test Automated Professor as Coordinator for CSE.', NULL, '2026-11-01 11:00:06'),
(282, 'COORDINATOR_REVOKE', 1, 'user', 161, 'Revoked Coordinator role from Test Automated Professor for CSE.', NULL, '2026-11-01 11:00:06'),
(283, 'HOD_ASSIGN', 1, 'user', 161, 'Appointed Test Automated Professor as HOD of CSE.', NULL, '2026-11-01 11:00:06'),
(284, 'USER_STATUS_CHANGE', 1, 'user', 161, 'Changed account status for Test Automated Professor (test_auto_prof_6ae6ce5e08d7d@gmrit.edu.in) to inactive.', NULL, '2026-11-01 11:00:06'),
(285, 'PASSWORD_RESET', 1, 'user', 161, 'Reset password for Test Automated Professor (test_auto_prof_6ae6ce5e08d7d@gmrit.edu.in) to default.', NULL, '2026-11-01 11:00:06'),
(286, 'USER_DELETE', 1, 'user', 161, 'Permanently deleted test user Test Automated Professor (test_auto_prof_6ae6ce5e08d7d@gmrit.edu.in).', NULL, '2026-11-01 11:00:06'),
(287, 'DOCUMENT_PURGE', 45, 'document', 91, 'Permanently purged Document #91 (\'Automated Mutation Test Document\') and 0 storage file(s).', NULL, '2026-11-01 18:53:17'),
(288, 'DOCUMENT_PURGE', 45, 'document', 92, 'Permanently purged Document #92 (\'Automated Mutation Test Document\') and 0 storage file(s).', NULL, '2026-11-01 18:54:44');

-- --------------------------------------------------------

--
-- Table structure for table `s_bodies`
--

CREATE TABLE `s_bodies` (
  `ID` int(255) NOT NULL,
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
  `rejection_reason` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `s_conference_tab`
--

CREATE TABLE `s_conference_tab` (
  `id` int(255) NOT NULL,
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
  `rejection_reason` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `s_events`
--

CREATE TABLE `s_events` (
  `ID` int(255) NOT NULL,
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
  `rejection_reason` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `s_journal_tab`
--

CREATE TABLE `s_journal_tab` (
  `id` int(255) NOT NULL,
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
  `rejection_reason` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `full_name` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_login` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `full_name`, `email`, `password`, `status`, `created_at`, `last_login`) VALUES
(6, 'cse-hod', 'cse-hod@gmrit.edu.in', '$2y$10$kMOCkMgofwDgtANRXmoJou9Zqx627jbWrrd4JLzEgok5Eo94ws30K', 'active', '2026-09-15 07:02:43', '2026-10-29 14:16:17'),
(7, 'aiml-hod', 'aiml-hod@gmrit.edu.in', '$2y$10$hFdN4Y5ABnHgtKrJ8ddKM.fX.D1/JO2rZHqmzYMGDLZ.gQGAcNXnm', 'active', '2026-09-15 07:02:44', NULL),
(8, 'aids-hod', 'aids-hod@gmrit.edu.in', '$2y$10$VEm0sgzZ8MXTMl5uKrhItOHMwMRQT3Cn9ZSl4Gs0gRTGBADtKeWJu', 'active', '2026-09-15 07:02:44', '2026-10-16 04:08:51'),
(9, 'ece-hod', 'ece-hod@gmrit.edu.in', '$2y$10$Ah.xEC4fx/m0lUH4Vr9fpeL9QD0WfQME3boIp8Yx.cYaN9mp0IjJa', 'active', '2026-09-15 07:02:44', NULL),
(10, 'eee-hod', 'eee-hod@gmrit.edu.in', '$2y$10$4s0MRqIYS4tNkEcLeX/aBOTO.cvah8O/pLB8KsGObDIy8t0NspkzS', 'active', '2026-09-15 07:02:45', '2026-10-16 06:02:08'),
(11, 'mech-hod', 'mech-hod@gmrit.edu.in', '$2y$10$bICEhuOXVMNLvpgX/AoaF.wFqJ7GJ8jM4gG2wu1OfuWyT5J/ku8j2', 'active', '2026-09-15 07:02:45', NULL),
(12, 'civil-hod', 'civil-hod@gmrit.edu.in', '$2y$10$XYYAf7sTv5kmqwDvDcVbt.kdx3L4..UfRcfhxsLKyhytSbwTKIsTa', 'active', '2026-09-15 07:02:46', '2026-10-16 04:32:33'),
(13, 'it-hod', 'it-hod@gmrit.edu.in', '$2y$10$cQhi/IwlYF3aCJzezo26Ae5yBcdbWCVnK4ZTMCEr.VkH9Ckvbfz/m', 'active', '2026-09-15 07:02:46', NULL),
(14, 'dept_cord1', 'dept_cord1@gmrit.edu.in', '$2y$10$fZBk5GBsbFfzRVLXbNj2FO6HkPRhnuV7yb4EhBWPwNRaXj.4XO87a', 'active', '2026-09-15 07:02:46', '2026-11-01 11:11:26'),
(15, 'dept_cord2', 'dept_cord2@gmrit.edu.in', '$2y$10$dAXpRoz6triivv7H.9Bsm.q5QXMLGNrPhjl9QVc8K7m7QX5TNcVR6', 'active', '2026-09-15 07:02:47', NULL),
(16, 'dept_cord3', 'dept_cord3@gmrit.edu.in', '$2y$10$yC7yMe2DM7NGc95KuJdsYO0xo7R/TLAZgLVKXpsN9.6UG2DFNmaHK', 'active', '2026-09-15 07:02:47', '2026-10-16 04:08:07'),
(17, 'dept_cord4', 'dept_cord4@gmrit.edu.in', '$2y$10$s7U7FEv7erkgmXaRWT8wN.ZzQYNALN93qwz0wLVT/GvPrIS8mywp6', 'active', '2026-09-15 07:02:47', NULL),
(18, 'dept_cord5', 'dept_cord5@gmrit.edu.in', '$2y$10$VTFL0nK95ly5LqJji/4wbuo.cOgwfSzlkfrEYL1FCD79RB8XoJn1K', 'active', '2026-09-15 07:02:48', '2026-10-16 06:00:26'),
(19, 'dept_cord6', 'dept_cord6@gmrit.edu.in', '$2y$10$GQRFo2o4yi591f2L1Fb8G.OBoa4brWB/ZjzoCK0D.X3GBJHRDtwtC', 'active', '2026-09-15 07:02:49', '2026-10-16 06:00:48'),
(20, 'dept_cord7', 'dept_cord7@gmrit.edu.in', '$2y$10$WDcQhvH7owjvIFasTgSOnuCLLuevvkqGohoGAnKUBKwTgDHD33LFa', 'active', '2026-09-15 07:02:50', '2026-10-16 06:00:17'),
(21, 'dept_cord8', 'dept_cord8@gmrit.edu.in', '$2y$10$wNBylS7tmKuo3.Ekg3.itec.PlZYsPSjgrzCfOX0qOYP/EOOxlV0S', 'active', '2026-09-15 07:02:50', '2026-10-16 06:00:37'),
(22, 'dept_cord9', 'dept_cord9@gmrit.edu.in', '$2y$10$aMOIcfpfQJD9FruZJyIiN.KgqoVlj/B5fPUwEyfqjzFYVq0PA8qEO', 'active', '2026-09-15 07:02:51', NULL),
(34, 'central_cord', 'central_cord@gmrit.edu.in', '$2y$10$z0mo25so5fyj2kyZe.kdXekgr2xn6P7/pNxDB8OrTS/2Gz9zdUnLi', 'active', '2026-09-15 07:02:55', '2026-09-30 15:23:46'),
(35, 'central_cord1', 'central_cord1@gmrit.edu.in', '$2y$10$YSYrnyeVI17hDXj490/FoOe5EEmzTWpM2E9hGCu9uGe8I0UAhl8Bq', 'active', '2026-09-15 07:02:55', NULL),
(36, 'central_cord2', 'central_cord2@gmrit.edu.in', '$2y$10$gXrud3c.IclqaBLd.CnKsec6pdcCzOllEoae.6ebe0xQNNarvxddS', 'active', '2026-09-15 07:02:56', NULL),
(37, 'central_cord3', 'central_cord3@gmrit.edu.in', '$2y$10$hzhMmzaS9CQBzwRa26GAq.iC1E4DHM1v8/kYD3cuGaOibOz0Vl7HG', 'active', '2026-09-15 07:02:56', NULL),
(38, 'central_cord4', 'central_cord4@gmrit.edu.in', '$2y$10$EXetV1ia3M5ff6xgJDp/jedhSL85rvmpyd0Mbk5BfaoO0o4oBcvua', 'active', '2026-09-15 07:02:56', NULL),
(39, 'central_cord5', 'central_cord5@gmrit.edu.in', '$2y$10$Zc.xEqb06EKiG4ax1auhQOlkkYxLPhjdSEOpV9mrGnD5u/LQms5x6', 'active', '2026-09-15 07:02:56', NULL),
(40, 'central_cord6', 'central_cord6@gmrit.edu.in', '$2y$10$uKbpVxvBsYm7dEYhqdN90eSe1E.E7So2ot8axkdJr5KUeOBwAlQDi', 'active', '2026-09-15 07:02:57', NULL),
(41, 'central_cord7', 'central_cord7@gmrit.edu.in', '$2y$10$HNM7J3fG9QENzyyPnp4YsexLFcWB/UFtKpYgk90IdeJ/dB/mnN7mm', 'active', '2026-09-15 07:02:57', NULL),
(42, 'central_cord8', 'central_cord8@gmrit.edu.in', '$2y$10$bYlxAlp4C1bte.BskupGAe2ycvWWKjWA8aLX6VRbWakiDCRL4WBeW', 'active', '2026-09-15 07:02:57', NULL),
(43, 'central_cord9', 'central_cord9@gmrit.edu.in', '$2y$10$Wq1i5DRSVWC/yfprHT49TuZ7yeSpjoHK.qCe83aXMS.LiCCKz1Nsy', 'active', '2026-09-15 07:02:58', NULL),
(44, 'central_cord10', 'central_cord10@gmrit.edu.in', '$2y$10$mrnobtD6jeZgIudTfsXnuOEu2ob2miYM2voIDK5bdQVbQUMl6v2eq', 'active', '2026-09-15 07:02:58', NULL),
(45, 'System Administrator', 'admin@gmrit.edu.in', '$2y$10$PwoOS7qwkTpUBFoIx//nx.Ost.I/SrLdLUXjtqn8IeXJDrhbvKMwu', 'active', '2026-09-15 07:02:58', '2026-11-21 04:49:45'),
(46, 'cseuser', 'cseuser@gmrit.edu.in', '$2y$10$/x0kexngMJ5yF4VysCTUyu7grYZ1FxzM66NX46wLtQFFpUz6cvUrO', 'active', '2026-09-15 07:08:28', NULL),
(48, 'IQAC Coordinator', 'iqac@gmrit.edu.in', '$2y$10$PwoOS7qwkTpUBFoIx//nx.Ost.I/SrLdLUXjtqn8IeXJDrhbvKMwu', 'active', '2026-09-15 11:55:28', '2026-09-23 10:06:01'),
(49, 'R&D Coordinator', 'rnd@gmrit.edu.in', '$2y$10$g5WENlAbZSRK/.kL4xTjRe7JICEVYhpvc0genYrGsNV2Jy40Z6Irq', 'active', '2026-09-15 11:55:28', '2026-10-16 04:09:36'),
(50, 'Exam Section Coordinator', 'exam_section@gmrit.edu.in', '$2y$10$yJeArkxqb0tQBW3qD90jy.Ae73iaGDOgG4SrPTloxSvDs0NV7DMoq', 'active', '2026-09-15 11:55:28', '2026-09-15 12:35:52'),
(51, 'NAAC Coordinator', 'naac@gmrit.edu.in', '$2y$10$NLx.LqGe2Q6A0L2ZC2j.Wugxv/wDl98w7dd1YUeL8MQ1kZRK.45lu', 'active', '2026-09-15 11:55:29', '2026-09-22 05:05:20'),
(52, 'NBA Coordinator', 'nba@gmrit.edu.in', '$2y$10$ySk8U47JnGIKQH0cJiGz6uROjPbOdzmxQRTPKiUPFNHlk3NP/2H9e', 'active', '2026-09-15 11:55:29', '2026-10-04 07:25:31'),
(53, 'NCC Coordinator', 'ncc@gmrit.edu.in', '$2y$10$CLutREX.eQt4NZ3Oj8SXGeTvHA0ZUxFwPY9o50tqOSLipY3CmfX1m', 'active', '2026-09-15 11:55:29', '2026-09-15 12:57:51'),
(54, 'Sports Coordinator', 'sports@gmrit.edu.in', '$2y$10$KRQGK7z7cCQ014t9InhjD./E9KU1Eh/IlsYrSOQkcH1rAr/7JIG.y', 'active', '2026-09-15 11:55:29', '2026-09-15 12:58:14'),
(55, 'Clubs Coordinator', 'clubs@gmrit.edu.in', '$2y$10$suNFq9rR5Ce0yfpgIBIkReiSKdL6G0B/rSHnOxu3KuoP3e7W8/fsO', 'active', '2026-09-15 11:55:29', '2026-09-15 13:01:11'),
(56, 'NSS Coordinator', 'nss@gmrit.edu.in', '$2y$10$BWM2qIJswEzOhoRNFkkIquYG2p2HUw2lsNZZPztgj06aLyhvOewkq', 'active', '2026-09-15 11:55:29', '2026-09-15 13:01:28'),
(57, 'Women Empowerment Coordinator', 'women_empowerment@gmrit.edu.in', '$2y$10$Zxp6JK.7PO9G8PhWgM5Q.OOB8itfCZHHEWqui9K9Q0badvTjrOOme', 'active', '2026-09-15 11:55:29', '2026-09-15 13:02:07'),
(58, 'IIC Coordinator', 'iic@gmrit.edu.in', '$2y$10$s95yq.kkHfkbG0eZ2vPvmO/xDvytI7qU.aiZqzH.7GeB4g1xf/keW', 'active', '2026-09-15 11:55:29', '2026-09-15 13:02:30'),
(59, 'PASH Coordinator', 'pash@gmrit.edu.in', '$2y$10$zSLWTNnlHitYcafEcdvVOujJ7S.DBcK5ODpCqwDUknZ6cJDtjW8e2', 'active', '2026-09-15 11:55:29', '2026-09-15 13:02:45'),
(60, 'Antiragging Coordinator', 'antiragging@gmrit.edu.in', '$2y$10$k4qHsskmTP58rpLTzCwY.enKA9WmlJDQblfMifrqw1ziD1ORlLCkK', 'active', '2026-09-15 11:55:29', '2026-09-15 13:03:05'),
(61, 'SAC Coordinator', 'sac@gmrit.edu.in', '$2y$10$keoeNfpxfO0sk1jP3KueB.sKsZNG.5L0jE0I.f2OA584DOERmS9Dy', 'active', '2026-09-15 11:55:29', '2026-09-15 13:03:18'),
(62, 'R&D Coordinator', 'R&D Coordinator', '', 'active', '2026-09-15 12:31:42', NULL),
(64, 'CSE user 1', 'cseuser1@gmrit.edu.in', '$2y$10$rG6E4efQ1ns2DjlOtjm2p.vRQufJV5MseWzudsZ1WIKJqdQjf/sru', 'active', '2026-10-06 08:02:01', '2026-11-21 04:44:41'),
(65, 'gowtham', '23341a4535@gmrit.edu.in', '$2y$10$bM9RedvqzD3YAbHP4IyLquG81D5ByvkQ5tkEK1oe/4/fXeJkROr12', 'active', '2026-10-06 18:16:40', '2026-10-11 16:47:25'),
(66, 'cse user 2', 'cseuser2@gmrit.edu.in', '$2y$10$iHLpkdzFnxV3CEwshROLPu6koI7Gr9HoFy89JcyIDMnW1rS4wJHr.', 'active', '2026-10-07 04:43:15', '2026-10-16 04:35:55'),
(67, 'aidsuser1', 'aidsuser1@gmrit.edu.in', '$2y$10$FUHxkEPoxj/rDgGMmxd9SeApeuWT8bHvAimfo/WUGrkeJLKoITgOu', 'active', '2026-10-07 05:03:45', '2026-10-12 16:00:23'),
(68, 'aidsuser2', 'aidsuser2@gmrit.edu.in', '$2y$10$E7nZprh6huzGm/q131g/g.qiDu74XeMd8F4U9lZ6nTyiy0CZwtA1m', 'active', '2026-10-16 03:59:27', '2026-10-16 04:00:08'),
(69, 'aidsuser3', 'aidsuser3@gmrit.edu.in', '$2y$10$nTN6/7G3N4xg08CuMQN/BunQGOl1FS26MseyTXxNcySEol6xM1Ck6', 'active', '2026-10-16 04:04:39', '2026-10-16 04:04:51'),
(70, 'civiluser1', 'civiluser1@gmrit.edu.in', '$2y$10$PgXtAKQmCA6A5QwRWP2Yf.xQzduA/QjtjRDBtn5/gQSmYXnXXH8vO', 'active', '2026-10-16 04:16:47', '2026-10-16 04:17:19'),
(71, 'eeeuser1', 'eeeuser1@gmrit.edu.in', '$2y$10$/aFsSCbuR/Bp9TVxE5MEcuPuorVIImWfRgDV3qM2vsEytr30LzDQS', 'active', '2026-10-16 05:39:44', '2026-10-16 05:39:57'),
(72, 'Central Coordinator', 'central.coord@gmrit.edu.in', '$2y$10$PwoOS7qwkTpUBFoIx//nx.Ost.I/SrLdLUXjtqn8IeXJDrhbvKMwu', 'active', '2026-10-29 10:28:06', NULL),
(73, 'Dean R&D', 'rnd.dean@gmrit.edu.in', '$2y$10$PwoOS7qwkTpUBFoIx//nx.Ost.I/SrLdLUXjtqn8IeXJDrhbvKMwu', 'active', '2026-10-29 10:28:06', NULL),
(74, 'Dr. HOD CSE', 'hod.cse@gmrit.edu.in', '$2y$10$PwoOS7qwkTpUBFoIx//nx.Ost.I/SrLdLUXjtqn8IeXJDrhbvKMwu', 'active', '2026-10-29 10:28:06', NULL),
(75, 'Faculty Member CSE', 'faculty.cse@gmrit.edu.in', '$2y$10$PwoOS7qwkTpUBFoIx//nx.Ost.I/SrLdLUXjtqn8IeXJDrhbvKMwu', 'active', '2026-10-29 10:28:06', NULL),
(76, 'CSE Dept Coordinator', 'coord.cse@gmrit.edu.in', '$2y$10$PwoOS7qwkTpUBFoIx//nx.Ost.I/SrLdLUXjtqn8IeXJDrhbvKMwu', 'active', '2026-10-29 10:28:06', NULL),
(77, 'Junior Assistant CSE', 'ja.cse@gmrit.edu.in', '$2y$10$PwoOS7qwkTpUBFoIx//nx.Ost.I/SrLdLUXjtqn8IeXJDrhbvKMwu', 'active', '2026-10-29 10:28:06', NULL),
(78, 'Student CSE', 'student.cse@gmrit.edu.in', '$2y$10$PwoOS7qwkTpUBFoIx//nx.Ost.I/SrLdLUXjtqn8IeXJDrhbvKMwu', 'active', '2026-10-29 10:28:06', NULL),
(79, 'Dr. HOD AIDS', 'hod.aids@gmrit.edu.in', '$2y$10$PwoOS7qwkTpUBFoIx//nx.Ost.I/SrLdLUXjtqn8IeXJDrhbvKMwu', 'active', '2026-10-29 10:28:06', NULL),
(80, 'Faculty Member AIDS', 'faculty.aids@gmrit.edu.in', '$2y$10$PwoOS7qwkTpUBFoIx//nx.Ost.I/SrLdLUXjtqn8IeXJDrhbvKMwu', 'active', '2026-10-29 10:28:06', NULL),
(81, 'AIDS Dept Coordinator', 'coord.aids@gmrit.edu.in', '$2y$10$PwoOS7qwkTpUBFoIx//nx.Ost.I/SrLdLUXjtqn8IeXJDrhbvKMwu', 'active', '2026-10-29 10:28:06', NULL),
(82, 'Dr. HOD AIML', 'hod.aiml@gmrit.edu.in', '$2y$10$PwoOS7qwkTpUBFoIx//nx.Ost.I/SrLdLUXjtqn8IeXJDrhbvKMwu', 'active', '2026-10-29 10:28:06', NULL),
(83, 'Faculty Member AIML', 'faculty.aiml@gmrit.edu.in', '$2y$10$PwoOS7qwkTpUBFoIx//nx.Ost.I/SrLdLUXjtqn8IeXJDrhbvKMwu', 'active', '2026-10-29 10:28:06', NULL),
(84, 'AIML Dept Coordinator', 'coord.aiml@gmrit.edu.in', '$2y$10$PwoOS7qwkTpUBFoIx//nx.Ost.I/SrLdLUXjtqn8IeXJDrhbvKMwu', 'active', '2026-10-29 10:28:06', NULL),
(85, 'Dr. HOD ECE', 'hod.ece@gmrit.edu.in', '$2y$10$PwoOS7qwkTpUBFoIx//nx.Ost.I/SrLdLUXjtqn8IeXJDrhbvKMwu', 'active', '2026-10-29 10:28:06', NULL),
(86, 'Faculty Member ECE', 'faculty.ece@gmrit.edu.in', '$2y$10$PwoOS7qwkTpUBFoIx//nx.Ost.I/SrLdLUXjtqn8IeXJDrhbvKMwu', 'active', '2026-10-29 10:28:06', NULL),
(87, 'ECE Dept Coordinator', 'coord.ece@gmrit.edu.in', '$2y$10$PwoOS7qwkTpUBFoIx//nx.Ost.I/SrLdLUXjtqn8IeXJDrhbvKMwu', 'active', '2026-10-29 10:28:06', NULL),
(88, 'Dr. HOD EEE', 'hod.eee@gmrit.edu.in', '$2y$10$PwoOS7qwkTpUBFoIx//nx.Ost.I/SrLdLUXjtqn8IeXJDrhbvKMwu', 'active', '2026-10-29 10:28:06', NULL),
(89, 'Faculty Member EEE', 'faculty.eee@gmrit.edu.in', '$2y$10$PwoOS7qwkTpUBFoIx//nx.Ost.I/SrLdLUXjtqn8IeXJDrhbvKMwu', 'active', '2026-10-29 10:28:06', NULL),
(90, 'EEE Dept Coordinator', 'coord.eee@gmrit.edu.in', '$2y$10$PwoOS7qwkTpUBFoIx//nx.Ost.I/SrLdLUXjtqn8IeXJDrhbvKMwu', 'active', '2026-10-29 10:28:06', NULL),
(91, 'Dr. HOD MECH', 'hod.mech@gmrit.edu.in', '$2y$10$PwoOS7qwkTpUBFoIx//nx.Ost.I/SrLdLUXjtqn8IeXJDrhbvKMwu', 'active', '2026-10-29 10:28:06', NULL),
(92, 'Faculty Member MECH', 'faculty.mech@gmrit.edu.in', '$2y$10$PwoOS7qwkTpUBFoIx//nx.Ost.I/SrLdLUXjtqn8IeXJDrhbvKMwu', 'active', '2026-10-29 10:28:06', NULL),
(93, 'MECH Dept Coordinator', 'coord.mech@gmrit.edu.in', '$2y$10$PwoOS7qwkTpUBFoIx//nx.Ost.I/SrLdLUXjtqn8IeXJDrhbvKMwu', 'active', '2026-10-29 10:28:06', NULL),
(94, 'Dr. HOD CIVIL', 'hod.civil@gmrit.edu.in', '$2y$10$PwoOS7qwkTpUBFoIx//nx.Ost.I/SrLdLUXjtqn8IeXJDrhbvKMwu', 'active', '2026-10-29 10:28:06', NULL),
(95, 'Faculty Member CIVIL', 'faculty.civil@gmrit.edu.in', '$2y$10$PwoOS7qwkTpUBFoIx//nx.Ost.I/SrLdLUXjtqn8IeXJDrhbvKMwu', 'active', '2026-10-29 10:28:06', NULL),
(96, 'CIVIL Dept Coordinator', 'coord.civil@gmrit.edu.in', '$2y$10$PwoOS7qwkTpUBFoIx//nx.Ost.I/SrLdLUXjtqn8IeXJDrhbvKMwu', 'active', '2026-10-29 10:28:06', NULL),
(97, 'Dr. HOD IT', 'hod.it@gmrit.edu.in', '$2y$10$PwoOS7qwkTpUBFoIx//nx.Ost.I/SrLdLUXjtqn8IeXJDrhbvKMwu', 'active', '2026-10-29 10:28:06', NULL),
(98, 'Faculty Member IT', 'faculty.it@gmrit.edu.in', '$2y$10$PwoOS7qwkTpUBFoIx//nx.Ost.I/SrLdLUXjtqn8IeXJDrhbvKMwu', 'active', '2026-10-29 10:28:06', NULL),
(99, 'IT Dept Coordinator', 'coord.it@gmrit.edu.in', '$2y$10$PwoOS7qwkTpUBFoIx//nx.Ost.I/SrLdLUXjtqn8IeXJDrhbvKMwu', 'active', '2026-10-29 10:28:06', NULL),
(100, 'Dr. HOD BSH', 'hod.bsh@gmrit.edu.in', '$2y$10$PwoOS7qwkTpUBFoIx//nx.Ost.I/SrLdLUXjtqn8IeXJDrhbvKMwu', 'active', '2026-10-29 10:28:06', NULL),
(101, 'Faculty Member BSH', 'faculty.bsh@gmrit.edu.in', '$2y$10$PwoOS7qwkTpUBFoIx//nx.Ost.I/SrLdLUXjtqn8IeXJDrhbvKMwu', 'active', '2026-10-29 10:28:06', NULL),
(102, 'BSH Dept Coordinator', 'coord.bsh@gmrit.edu.in', '$2y$10$PwoOS7qwkTpUBFoIx//nx.Ost.I/SrLdLUXjtqn8IeXJDrhbvKMwu', 'active', '2026-10-29 10:28:06', NULL),
(103, 'Dr. HOD CSE-CS', 'hod.csecs@gmrit.edu.in', '$2y$10$PwoOS7qwkTpUBFoIx//nx.Ost.I/SrLdLUXjtqn8IeXJDrhbvKMwu', 'active', '2026-10-29 10:28:06', NULL),
(104, 'Faculty Member CSE-CS', 'faculty.csecs@gmrit.edu.in', '$2y$10$PwoOS7qwkTpUBFoIx//nx.Ost.I/SrLdLUXjtqn8IeXJDrhbvKMwu', 'active', '2026-10-29 10:28:06', NULL),
(105, 'CSE-CS Dept Coordinator', 'coord.csecs@gmrit.edu.in', '$2y$10$PwoOS7qwkTpUBFoIx//nx.Ost.I/SrLdLUXjtqn8IeXJDrhbvKMwu', 'active', '2026-10-29 10:28:06', NULL),
(106, 'Dr. HOD Mathematics', 'hod.maths@gmrit.edu.in', '$2y$10$PwoOS7qwkTpUBFoIx//nx.Ost.I/SrLdLUXjtqn8IeXJDrhbvKMwu', 'active', '2026-10-29 10:28:06', NULL),
(107, 'Faculty Member Mathematics', 'faculty.maths@gmrit.edu.in', '$2y$10$PwoOS7qwkTpUBFoIx//nx.Ost.I/SrLdLUXjtqn8IeXJDrhbvKMwu', 'active', '2026-10-29 10:28:06', NULL),
(108, 'Dr. HOD Physics', 'hod.physics@gmrit.edu.in', '$2y$10$PwoOS7qwkTpUBFoIx//nx.Ost.I/SrLdLUXjtqn8IeXJDrhbvKMwu', 'active', '2026-10-29 10:28:06', NULL),
(109, 'Faculty Member Physics', 'faculty.physics@gmrit.edu.in', '$2y$10$PwoOS7qwkTpUBFoIx//nx.Ost.I/SrLdLUXjtqn8IeXJDrhbvKMwu', 'active', '2026-10-29 10:28:06', NULL),
(110, 'Dr. HOD Chemistry', 'hod.chemistry@gmrit.edu.in', '$2y$10$PwoOS7qwkTpUBFoIx//nx.Ost.I/SrLdLUXjtqn8IeXJDrhbvKMwu', 'active', '2026-10-29 10:28:06', NULL),
(111, 'Faculty Member Chemistry', 'faculty.chemistry@gmrit.edu.in', '$2y$10$PwoOS7qwkTpUBFoIx//nx.Ost.I/SrLdLUXjtqn8IeXJDrhbvKMwu', 'active', '2026-10-29 10:28:06', NULL),
(155, '', '', '$2y$10$Yq1K4JPZ70lCn2M2/3HEhumo05.yCW6lvAjUDI11uwCViAv4txkay', 'active', '2026-11-01 04:34:18', NULL),
(157, 'Test Automated Professor', 'test_auto_prof_6ae6c180e3ba5@gmrit.edu.in', '$2y$10$uupBdWEtDZgHdy7b1MC8EeaJNd/o3TMWP/xjgP4qyudk8/cZ8KjPu', 'active', '2026-11-01 04:35:13', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_profiles`
--

CREATE TABLE `user_profiles` (
  `profile_id` int(11) NOT NULL,
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
  `profile_photo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_profiles`
--

INSERT INTO `user_profiles` (`profile_id`, `user_id`, `per_no`, `pan_no`, `apaar_id`, `highest_degree`, `university`, `specialization`, `doj_institution`, `doj_department`, `experience_years`, `designation_joining`, `designation_present`, `date_designated_prof`, `association_nature`, `contract_type`, `is_currently_associated`, `date_of_leaving`, `profile_photo`) VALUES
(3, 64, '12345', 'ABCDE1234F', '123456789012', 'Phd in Computer Science', 'GMRIT DU', 'Computer Science', '2025-01-01', '2025-01-01', 1.8, '0', 'Assistant Professor', '2025-01-01', 'Regular', NULL, 1, NULL, 'uploads/profiles/profiles_photo_6ac518ef436fd.jpg'),
(6, 66, '12346', 'ABCDE1234G', '123456789013', 'Phd in Computer Science', 'GMRIT DU', 'Computer Science', '2025-01-01', '2025-01-01', 1.8, 'Assisstant Professor', 'Assistant Professor', '2025-01-01', 'Regular', NULL, 1, NULL, 'uploads/profiles/profiles_photo_6ac5cde40579c.jpg'),
(7, 67, '12347', 'ABCDE1234E', '123456789014', 'Phd in  AIDS', 'GMRIT DU', 'AIDS', '2025-01-01', '2025-01-01', 1.8, 'Assisstant Professor', 'Assistant Professor', '2025-01-01', 'Regular', NULL, 1, NULL, NULL),
(8, 68, '12349', 'ABCDE1234I', '123456789013', 'Phd in  AIDS', 'GMRIT DU', 'AIDS', '2022-01-01', '2022-01-01', 4.8, 'Assisstant Professor', 'Assistant Professor', '2022-01-01', 'Regular', NULL, 1, NULL, NULL),
(9, 69, '12350', 'ABCDE1234J', '123456789015', 'Phd in  AIDS', 'GMRIT DU', 'AIDS', '2022-01-01', '2022-01-01', 4.8, 'Assisstant Professor', 'Assistant Professor', NULL, 'Regular', NULL, 1, NULL, 'uploads/profiles/profiles_photo_6ad1a25756a95.jpg'),
(10, 70, '12352', 'ABCDE1234K', '123456789015', 'Phd in  Civil', 'GMRIT DU', 'Civil', '2020-01-01', '2020-01-01', 6.8, 'Assisstant Professor', 'Assistant Professor', NULL, 'Regular', NULL, 1, NULL, 'uploads/profiles/profiles_photo_6ad1a52f2a78a.jpg'),
(11, 71, '12353', 'ABCDE1234L', '123456789014', 'Phd in EEE', 'GMRIT DU', 'EEE', '2020-01-01', '2020-01-01', 6.8, 'Assisstant Professor', 'Assistant Professor', NULL, 'Regular', NULL, 1, NULL, NULL),
(12, 45, 'ADM001', 'ABCDE0001F', 'APAAR-001', 'Ph.D', 'JNTUK', 'Computer Science', '2015-06-01', '2015-06-01', 12.0, 'System Administrator', 'System Administrator', NULL, 'Regular', NULL, 1, NULL, NULL),
(13, 48, 'IQC001', 'ABCDE0002F', 'APAAR-002', 'Ph.D', 'Andhra University', 'Quality Assurance', '2016-07-01', '2016-07-01', 10.0, 'Professor', 'IQAC Coordinator', NULL, 'Regular', NULL, 1, NULL, NULL),
(14, 72, 'CCD001', 'ABCDE0003F', 'APAAR-003', 'Ph.D', 'JNTUK', 'Institutional Operations', '2017-06-01', '2017-06-01', 9.0, 'Associate Professor', 'Central Coordinator', NULL, 'Regular', NULL, 1, NULL, NULL),
(15, 73, 'RND001', 'ABCDE0004F', 'APAAR-004', 'Ph.D', 'IIT Madras', 'Research & Innovation', '2013-05-15', '2013-05-15', 16.0, 'Professor', 'Dean R&D', NULL, 'Regular', NULL, 1, NULL, NULL),
(16, 74, 'HOD_CSE', 'ABCDE0005F', 'APAAR-005', 'Ph.D', 'IIT Madras', 'Computer Science & Engineering', '2014-05-10', '2014-05-10', 15.0, 'Associate Professor', 'Professor & HOD', NULL, 'Regular', NULL, 1, NULL, NULL),
(17, 75, 'FAC_CSE', 'ABCDE0006F', 'APAAR-006', 'M.Tech', 'JNTUK', 'Software Engineering', '2019-08-01', '2019-08-01', 6.0, 'Assistant Professor', 'Assistant Professor', NULL, 'Regular', NULL, 1, NULL, NULL),
(18, 76, 'CRD_CSE', 'ABCDE0007F', 'APAAR-007', 'M.Tech', 'JNTUK', 'Computer Networks', '2018-07-01', '2018-07-01', 7.0, 'Assistant Professor', 'Assistant Professor & Coordinator', NULL, 'Regular', NULL, 1, NULL, NULL),
(19, 77, 'JA_CSE', 'ABCDE0008F', 'APAAR-008', 'B.Com', 'Andhra University', 'Office Administration', '2020-01-15', '2020-01-15', 5.0, 'Junior Assistant', 'Junior Assistant', NULL, 'Regular', NULL, 1, NULL, NULL),
(20, 78, 'STU_CSE', 'ABCDE0009F', 'APAAR-009', 'B.Tech (Pursuing)', 'GMRIT', 'Computer Science', '2022-09-01', '2022-09-01', 0.0, 'Student', 'Student', NULL, 'Regular', NULL, 1, NULL, NULL),
(21, 79, 'HOD_AIDS', 'ABCDE0010F', 'APAAR-010', 'Ph.D', 'IIT Kharagpur', 'Artificial Intelligence & Data Science', '2016-06-01', '2016-06-01', 12.0, 'Associate Professor', 'Professor & HOD', NULL, 'Regular', NULL, 1, NULL, NULL),
(22, 80, 'FAC_AIDS', 'ABCDE0011F', 'APAAR-011', 'M.Tech', 'JNTUK', 'Data Science & Big Data', '2020-07-01', '2020-07-01', 5.0, 'Assistant Professor', 'Assistant Professor', NULL, 'Regular', NULL, 1, NULL, NULL),
(23, 81, 'CRD_AIDS', 'ABCDE0012F', 'APAAR-012', 'M.Tech', 'NIT Warangal', 'Machine Learning', '2019-06-15', '2019-06-15', 6.0, 'Assistant Professor', 'Assistant Professor & Coordinator', NULL, 'Regular', NULL, 1, NULL, NULL),
(24, 82, 'HOD_AIML', 'ABCDE0013F', 'APAAR-013', 'Ph.D', 'IIT Hyderabad', 'Deep Learning & Neural Networks', '2015-07-01', '2015-07-01', 13.0, 'Associate Professor', 'Professor & HOD', NULL, 'Regular', NULL, 1, NULL, NULL),
(25, 83, 'FAC_AIML', 'ABCDE0014F', 'APAAR-014', 'M.Tech', 'Andhra University', 'Artificial Intelligence', '2020-08-01', '2020-08-01', 5.0, 'Assistant Professor', 'Assistant Professor', NULL, 'Regular', NULL, 1, NULL, NULL),
(26, 84, 'CRD_AIML', 'ABCDE0015F', 'APAAR-015', 'M.Tech', 'JNTUK', 'Computer Vision', '2019-07-01', '2019-07-01', 6.0, 'Assistant Professor', 'Assistant Professor & Coordinator', NULL, 'Regular', NULL, 1, NULL, NULL),
(27, 85, 'HOD_ECE', 'ABCDE0016F', 'APAAR-016', 'Ph.D', 'IIT Roorkee', 'VLSI & Embedded Systems', '2013-06-01', '2013-06-01', 16.0, 'Associate Professor', 'Professor & HOD', NULL, 'Regular', NULL, 1, NULL, NULL),
(28, 86, 'FAC_ECE', 'ABCDE0017F', 'APAAR-017', 'M.Tech', 'JNTUK', 'Communication Systems', '2018-06-01', '2018-06-01', 7.0, 'Assistant Professor', 'Assistant Professor', NULL, 'Regular', NULL, 1, NULL, NULL),
(29, 87, 'CRD_ECE', 'ABCDE0018F', 'APAAR-018', 'M.Tech', 'NIT Trichy', 'Signal Processing', '2017-07-01', '2017-07-01', 8.0, 'Assistant Professor', 'Assistant Professor & Coordinator', NULL, 'Regular', NULL, 1, NULL, NULL),
(30, 88, 'HOD_EEE', 'ABCDE0019F', 'APAAR-019', 'Ph.D', 'NIT Warangal', 'Power Systems & Renewable Energy', '2012-07-01', '2012-07-01', 17.0, 'Associate Professor', 'Professor & HOD', NULL, 'Regular', NULL, 1, NULL, NULL),
(31, 89, 'FAC_EEE', 'ABCDE0020F', 'APAAR-020', 'M.Tech', 'JNTUK', 'Power Electronics', '2019-06-01', '2019-06-01', 6.0, 'Assistant Professor', 'Assistant Professor', NULL, 'Regular', NULL, 1, NULL, NULL),
(32, 90, 'CRD_EEE', 'ABCDE0021F', 'APAAR-021', 'M.Tech', 'Andhra University', 'Control Systems', '2018-07-01', '2018-07-01', 7.0, 'Assistant Professor', 'Assistant Professor & Coordinator', NULL, 'Regular', NULL, 1, NULL, NULL),
(33, 91, 'HOD_MECH', 'ABCDE0022F', 'APAAR-022', 'Ph.D', 'IIT Madras', 'Thermal Engineering & Robotics', '2011-06-01', '2011-06-01', 18.0, 'Associate Professor', 'Professor & HOD', NULL, 'Regular', NULL, 1, NULL, NULL),
(34, 92, 'FAC_MECH', 'ABCDE0023F', 'APAAR-023', 'M.Tech', 'JNTUK', 'Machine Design', '2018-06-01', '2018-06-01', 7.0, 'Assistant Professor', 'Assistant Professor', NULL, 'Regular', NULL, 1, NULL, NULL),
(35, 93, 'CRD_MECH', 'ABCDE0024F', 'APAAR-024', 'M.Tech', 'NIT Rourkela', 'Manufacturing Science', '2017-07-01', '2017-07-01', 8.0, 'Assistant Professor', 'Assistant Professor & Coordinator', NULL, 'Regular', NULL, 1, NULL, NULL),
(36, 94, 'HOD_CIVIL', 'ABCDE0025F', 'APAAR-025', 'Ph.D', 'IIT Kharagpur', 'Structural & Geotechnical Engineering', '2013-05-01', '2013-05-01', 16.0, 'Associate Professor', 'Professor & HOD', NULL, 'Regular', NULL, 1, NULL, NULL),
(37, 95, 'FAC_CIVIL', 'ABCDE0026F', 'APAAR-026', 'M.Tech', 'JNTUK', 'Transportation Engineering', '2019-07-01', '2019-07-01', 6.0, 'Assistant Professor', 'Assistant Professor', NULL, 'Regular', NULL, 1, NULL, NULL),
(38, 96, 'CRD_CIVIL', 'ABCDE0027F', 'APAAR-027', 'M.Tech', 'Andhra University', 'Environmental Engineering', '2018-08-01', '2018-08-01', 7.0, 'Assistant Professor', 'Assistant Professor & Coordinator', NULL, 'Regular', NULL, 1, NULL, NULL),
(39, 97, 'HOD_IT', 'ABCDE0028F', 'APAAR-028', 'Ph.D', 'Andhra University', 'Information Technology & Cloud Computing', '2014-06-01', '2014-06-01', 15.0, 'Associate Professor', 'Professor & HOD', NULL, 'Regular', NULL, 1, NULL, NULL),
(40, 98, 'FAC_IT', 'ABCDE0029F', 'APAAR-029', 'M.Tech', 'JNTUK', 'Information Security', '2019-06-01', '2019-06-01', 6.0, 'Assistant Professor', 'Assistant Professor', NULL, 'Regular', NULL, 1, NULL, NULL),
(41, 99, 'CRD_IT', 'ABCDE0030F', 'APAAR-030', 'M.Tech', 'NIT Calicut', 'Data Engineering', '2018-07-01', '2018-07-01', 7.0, 'Assistant Professor', 'Assistant Professor & Coordinator', NULL, 'Regular', NULL, 1, NULL, NULL),
(42, 100, 'HOD_BSH', 'ABCDE0031F', 'APAAR-031', 'Ph.D', 'Andhra University', 'Basic Sciences & Humanities', '2012-06-01', '2012-06-01', 17.0, 'Associate Professor', 'Professor & HOD', NULL, 'Regular', NULL, 1, NULL, NULL),
(43, 101, 'FAC_BSH', 'ABCDE0032F', 'APAAR-032', 'M.Phil / Ph.D', 'JNTUK', 'English & Communication Skills', '2019-07-01', '2019-07-01', 6.0, 'Assistant Professor', 'Assistant Professor', NULL, 'Regular', NULL, 1, NULL, NULL),
(44, 102, 'CRD_BSH', 'ABCDE0033F', 'APAAR-033', 'Ph.D', 'Andhra University', 'Humanities', '2018-08-01', '2018-08-01', 7.0, 'Assistant Professor', 'Assistant Professor & Coordinator', NULL, 'Regular', NULL, 1, NULL, NULL),
(45, 103, 'HOD_CSECS', 'ABCDE0034F', 'APAAR-034', 'Ph.D', 'IIT Delhi', 'Cyber Security & Cryptography', '2016-07-01', '2016-07-01', 12.0, 'Associate Professor', 'Professor & HOD', NULL, 'Regular', NULL, 1, NULL, NULL),
(46, 104, 'FAC_CSECS', 'ABCDE0035F', 'APAAR-035', 'M.Tech', 'JNTUK', 'Network & Cyber Security', '2020-07-01', '2020-07-01', 5.0, 'Assistant Professor', 'Assistant Professor', NULL, 'Regular', NULL, 1, NULL, NULL),
(47, 105, 'CRD_CSECS', 'ABCDE0036F', 'APAAR-036', 'M.Tech', 'NIT Warangal', 'Cyber Defense', '2019-06-01', '2019-06-01', 6.0, 'Assistant Professor', 'Assistant Professor & Coordinator', NULL, 'Regular', NULL, 1, NULL, NULL),
(48, 106, 'HOD_MATHS', 'ABCDE0037F', 'APAAR-037', 'Ph.D', 'Andhra University', 'Applied Mathematics & Statistics', '2013-06-01', '2013-06-01', 16.0, 'Associate Professor', 'Professor & HOD', NULL, 'Regular', NULL, 1, NULL, NULL),
(49, 107, 'FAC_MATHS', 'ABCDE0038F', 'APAAR-038', 'Ph.D', 'JNTUK', 'Pure & Computational Mathematics', '2019-07-01', '2019-07-01', 6.0, 'Assistant Professor', 'Assistant Professor', NULL, 'Regular', NULL, 1, NULL, NULL),
(50, 108, 'HOD_PHYS', 'ABCDE0039F', 'APAAR-039', 'Ph.D', 'IIT Madras', 'Material Science & Condensed Matter', '2014-06-01', '2014-06-01', 15.0, 'Associate Professor', 'Professor & HOD', NULL, 'Regular', NULL, 1, NULL, NULL),
(51, 109, 'FAC_PHYS', 'ABCDE0040F', 'APAAR-040', 'Ph.D', 'Andhra University', 'Optics & Nanotechnology', '2019-06-01', '2019-06-01', 6.0, 'Assistant Professor', 'Assistant Professor', NULL, 'Regular', NULL, 1, NULL, NULL),
(52, 110, 'HOD_CHEM', 'ABCDE0041F', 'APAAR-041', 'Ph.D', 'IIT Kharagpur', 'Organic & Polymer Chemistry', '2013-07-01', '2013-07-01', 16.0, 'Associate Professor', 'Professor & HOD', NULL, 'Regular', NULL, 1, NULL, NULL),
(53, 111, 'FAC_CHEM', 'ABCDE0042F', 'APAAR-042', 'Ph.D', 'JNTUK', 'Analytical Chemistry', '2019-07-01', '2019-07-01', 6.0, 'Assistant Professor', 'Assistant Professor', NULL, 'Regular', NULL, 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_roles`
--

CREATE TABLE `user_roles` (
  `user_role_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `dept_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_roles`
--

INSERT INTO `user_roles` (`user_role_id`, `user_id`, `role_id`, `dept_id`) VALUES
(118, 6, 4, 1),
(7, 7, 3, 3),
(8, 8, 3, 2),
(9, 9, 3, 4),
(10, 10, 3, 5),
(11, 11, 3, 6),
(12, 12, 3, 7),
(13, 13, 3, 8),
(14, 14, 5, 1),
(15, 15, 5, 3),
(16, 16, 5, 2),
(17, 17, 5, 8),
(18, 18, 5, 4),
(19, 19, 5, 5),
(20, 20, 5, 6),
(21, 21, 5, 7),
(22, 22, 5, 9),
(45, 45, 1, NULL),
(47, 48, 6, 17),
(48, 49, 8, 18),
(49, 50, 6, 19),
(50, 51, 6, 20),
(51, 52, 6, 21),
(52, 53, 6, 22),
(53, 54, 6, 23),
(54, 55, 6, 24),
(55, 56, 6, 25),
(56, 57, 6, 26),
(57, 58, 6, 27),
(58, 59, 6, 28),
(59, 60, 6, 29),
(60, 61, 6, 30),
(65, 64, 4, 1),
(66, 65, 9, 2),
(67, 66, 4, 1),
(68, 67, 4, 2),
(69, 68, 4, 2),
(70, 69, 4, 2),
(71, 70, 4, 7),
(72, 71, 4, 5),
(120, 74, 4, 1),
(78, 75, 4, 1),
(79, 76, 5, 1),
(80, 77, 7, 1),
(81, 78, 9, 1),
(82, 79, 3, 2),
(83, 80, 4, 2),
(84, 81, 5, 2),
(85, 82, 3, 3),
(86, 83, 4, 3),
(87, 84, 5, 3),
(88, 85, 3, 4),
(89, 86, 4, 4),
(90, 87, 5, 4),
(91, 88, 3, 5),
(92, 89, 4, 5),
(93, 90, 5, 5),
(94, 91, 3, 6),
(95, 92, 4, 6),
(96, 93, 5, 6),
(97, 94, 3, 7),
(98, 95, 4, 7),
(99, 96, 5, 7),
(100, 97, 3, 8),
(101, 98, 4, 8),
(102, 99, 5, 8),
(103, 100, 3, 9),
(104, 101, 4, 9),
(105, 102, 5, 9),
(106, 103, 3, 10),
(107, 104, 4, 10),
(108, 105, 5, 10),
(109, 106, 3, 14),
(110, 107, 4, 14),
(111, 108, 3, 15),
(112, 109, 4, 15),
(113, 110, 3, 16),
(114, 111, 4, 16),
(261, 155, 4, NULL),
(263, 157, 4, 1);

-- --------------------------------------------------------

--
-- Table structure for table `workflows`
--

CREATE TABLE `workflows` (
  `workflow_id` int(11) NOT NULL,
  `workflow_key` varchar(50) NOT NULL,
  `label` varchar(100) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `workflows`
--

INSERT INTO `workflows` (`workflow_id`, `workflow_key`, `label`, `description`) VALUES
(1, 'department_standard', 'Standard Department Review', NULL),
(4, 'student_activity_wf', 'Student Activity Workflow', NULL),
(5, 'department_internal', 'Internal Department Review', 'Faculty uploads -> Dept Coordinator -> HOD -> Accepted');

-- --------------------------------------------------------

--
-- Table structure for table `workflow_actions`
--

CREATE TABLE `workflow_actions` (
  `action_id` int(11) NOT NULL,
  `action_key` varchar(50) NOT NULL,
  `label` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `workflow_actions`
--

INSERT INTO `workflow_actions` (`action_id`, `action_key`, `label`) VALUES
(1, 'approve', 'Approve'),
(2, 'reject', 'Reject'),
(3, 'resubmit', 'Resubmit');

-- --------------------------------------------------------

--
-- Table structure for table `workflow_steps`
--

CREATE TABLE `workflow_steps` (
  `step_id` int(11) NOT NULL,
  `workflow_id` int(11) NOT NULL,
  `step_order` int(11) NOT NULL,
  `step_label` varchar(100) NOT NULL,
  `responsible_role_id` int(11) NOT NULL,
  `scope` enum('department','global') DEFAULT 'global'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `workflow_steps`
--

INSERT INTO `workflow_steps` (`step_id`, `workflow_id`, `step_order`, `step_label`, `responsible_role_id`, `scope`) VALUES
(1, 1, 1, 'Dept Coordinator Review', 5, 'department'),
(2, 1, 2, 'HOD Review', 3, 'department'),
(3, 1, 3, 'R&D Dean Review', 8, 'global'),
(10, 4, 1, 'Coordinator Review', 5, 'global'),
(11, 5, 1, 'Dept Coordinator Review', 5, 'department'),
(12, 5, 2, 'HOD Review', 3, 'department');

-- --------------------------------------------------------

--
-- Table structure for table `workflow_transitions`
--

CREATE TABLE `workflow_transitions` (
  `transition_id` int(11) NOT NULL,
  `step_id` int(11) NOT NULL,
  `action_id` int(11) NOT NULL,
  `to_step_id` int(11) DEFAULT NULL,
  `resulting_status` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `workflow_transitions`
--

INSERT INTO `workflow_transitions` (`transition_id`, `step_id`, `action_id`, `to_step_id`, `resulting_status`) VALUES
(1, 1, 1, 2, 'pending'),
(2, 1, 2, NULL, 'rejected'),
(3, 2, 1, 3, 'pending'),
(4, 2, 2, NULL, 'rejected'),
(5, 3, 1, NULL, 'accepted'),
(6, 3, 2, NULL, 'rejected'),
(7, 1, 3, 1, 'pending'),
(8, 2, 3, 1, 'pending'),
(9, 3, 3, 1, 'pending'),
(10, 10, 1, NULL, 'accepted'),
(11, 10, 2, NULL, 'rejected'),
(12, 11, 1, 12, 'pending'),
(13, 11, 2, NULL, 'rejected'),
(14, 11, 3, 11, 'pending'),
(15, 12, 1, NULL, 'accepted'),
(16, 12, 2, NULL, 'rejected'),
(17, 12, 3, 11, 'pending');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `academic_years`
--
ALTER TABLE `academic_years`
  ADD PRIMARY KEY (`year_id`),
  ADD UNIQUE KEY `year_label` (`year_label`);

--
-- Indexes for table `a_cri_files`
--
ALTER TABLE `a_cri_files`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `a_c_files`
--
ALTER TABLE `a_c_files`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `a_files`
--
ALTER TABLE `a_files`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `central_files`
--
ALTER TABLE `central_files`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `conference_tab`
--
ALTER TABLE `conference_tab`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `conf_org_tab`
--
ALTER TABLE `conf_org_tab`
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
-- Indexes for table `dc_up_files`
--
ALTER TABLE `dc_up_files`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`dept_id`),
  ADD UNIQUE KEY `dept_name` (`dept_name`);

--
-- Indexes for table `dept_files`
--
ALTER TABLE `dept_files`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `documents`
--
ALTER TABLE `documents`
  ADD PRIMARY KEY (`doc_id`),
  ADD KEY `type_id` (`type_id`),
  ADD KEY `uploaded_by` (`uploaded_by`),
  ADD KEY `dept_id` (`dept_id`),
  ADD KEY `academic_year_id` (`academic_year_id`),
  ADD KEY `current_step` (`current_step`);

--
-- Indexes for table `document_actions`
--
ALTER TABLE `document_actions`
  ADD PRIMARY KEY (`action_id`),
  ADD KEY `acted_by` (`acted_by`),
  ADD KEY `step_id` (`step_id`),
  ADD KEY `idx_doc` (`doc_id`);

--
-- Indexes for table `document_files`
--
ALTER TABLE `document_files`
  ADD PRIMARY KEY (`file_id`),
  ADD KEY `idx_doc` (`doc_id`);

--
-- Indexes for table `document_metadata`
--
ALTER TABLE `document_metadata`
  ADD PRIMARY KEY (`meta_id`),
  ADD UNIQUE KEY `uq_doc_meta` (`doc_id`,`meta_key`);

--
-- Indexes for table `document_meta_student_activity`
--
ALTER TABLE `document_meta_student_activity`
  ADD PRIMARY KEY (`meta_id`),
  ADD KEY `doc_id` (`doc_id`);

--
-- Indexes for table `document_types`
--
ALTER TABLE `document_types`
  ADD PRIMARY KEY (`type_id`),
  ADD UNIQUE KEY `type_code` (`type_code`),
  ADD KEY `workflow_id` (`workflow_id`);

--
-- Indexes for table `fdps_org_tab`
--
ALTER TABLE `fdps_org_tab`
  ADD PRIMARY KEY (`id`);

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
-- Indexes for table `files5_1_1and2`
--
ALTER TABLE `files5_1_1and2`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `files5_1_3`
--
ALTER TABLE `files5_1_3`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `files5_1_4`
--
ALTER TABLE `files5_1_4`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `files5_2_1`
--
ALTER TABLE `files5_2_1`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `files5_2_2`
--
ALTER TABLE `files5_2_2`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `files5_2_3`
--
ALTER TABLE `files5_2_3`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `files5_3_1`
--
ALTER TABLE `files5_3_1`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `files5_3_3`
--
ALTER TABLE `files5_3_3`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `meta_award`
--
ALTER TABLE `meta_award`
  ADD PRIMARY KEY (`meta_id`),
  ADD KEY `idx_doc_id` (`doc_id`);

--
-- Indexes for table `meta_central_file`
--
ALTER TABLE `meta_central_file`
  ADD PRIMARY KEY (`meta_id`),
  ADD KEY `idx_doc_id` (`doc_id`);

--
-- Indexes for table `meta_conference`
--
ALTER TABLE `meta_conference`
  ADD PRIMARY KEY (`meta_id`),
  ADD KEY `idx_doc_id` (`doc_id`);

--
-- Indexes for table `meta_conf_organised`
--
ALTER TABLE `meta_conf_organised`
  ADD PRIMARY KEY (`meta_id`),
  ADD KEY `idx_doc_id` (`doc_id`);

--
-- Indexes for table `meta_criteria_file`
--
ALTER TABLE `meta_criteria_file`
  ADD PRIMARY KEY (`meta_id`),
  ADD KEY `idx_doc_id` (`doc_id`);

--
-- Indexes for table `meta_dept_file`
--
ALTER TABLE `meta_dept_file`
  ADD PRIMARY KEY (`meta_id`),
  ADD KEY `idx_doc_id` (`doc_id`);

--
-- Indexes for table `meta_exam_qual`
--
ALTER TABLE `meta_exam_qual`
  ADD PRIMARY KEY (`meta_id`),
  ADD KEY `idx_doc_id` (`doc_id`);

--
-- Indexes for table `meta_fdp_attended`
--
ALTER TABLE `meta_fdp_attended`
  ADD PRIMARY KEY (`meta_id`),
  ADD KEY `idx_doc_id` (`doc_id`);

--
-- Indexes for table `meta_fdp_organised`
--
ALTER TABLE `meta_fdp_organised`
  ADD PRIMARY KEY (`meta_id`),
  ADD KEY `idx_doc_id` (`doc_id`);

--
-- Indexes for table `meta_higher_ed`
--
ALTER TABLE `meta_higher_ed`
  ADD PRIMARY KEY (`meta_id`),
  ADD KEY `idx_doc_id` (`doc_id`);

--
-- Indexes for table `meta_journal`
--
ALTER TABLE `meta_journal`
  ADD PRIMARY KEY (`meta_id`),
  ADD KEY `idx_doc_id` (`doc_id`);

--
-- Indexes for table `meta_patent`
--
ALTER TABLE `meta_patent`
  ADD PRIMARY KEY (`meta_id`),
  ADD KEY `idx_doc_id` (`doc_id`);

--
-- Indexes for table `meta_placement`
--
ALTER TABLE `meta_placement`
  ADD PRIMARY KEY (`meta_id`),
  ADD KEY `idx_doc_id` (`doc_id`);

--
-- Indexes for table `meta_scholarship`
--
ALTER TABLE `meta_scholarship`
  ADD PRIMARY KEY (`meta_id`),
  ADD KEY `idx_doc_id` (`doc_id`);

--
-- Indexes for table `meta_student_activity_file`
--
ALTER TABLE `meta_student_activity_file`
  ADD PRIMARY KEY (`doc_id`);

--
-- Indexes for table `meta_student_body`
--
ALTER TABLE `meta_student_body`
  ADD PRIMARY KEY (`meta_id`),
  ADD KEY `idx_doc_id` (`doc_id`);

--
-- Indexes for table `meta_student_conference`
--
ALTER TABLE `meta_student_conference`
  ADD PRIMARY KEY (`meta_id`),
  ADD KEY `idx_doc_id` (`doc_id`);

--
-- Indexes for table `meta_student_event`
--
ALTER TABLE `meta_student_event`
  ADD PRIMARY KEY (`meta_id`),
  ADD KEY `idx_doc_id` (`doc_id`);

--
-- Indexes for table `meta_student_journal`
--
ALTER TABLE `meta_student_journal`
  ADD PRIMARY KEY (`meta_id`),
  ADD KEY `idx_doc_id` (`doc_id`);

--
-- Indexes for table `naac_criteria_data`
--
ALTER TABLE `naac_criteria_data`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sub_crit` (`submission_id`,`criterion_number`);

--
-- Indexes for table `naac_form_schemas`
--
ALTER TABLE `naac_form_schemas`
  ADD PRIMARY KEY (`schema_id`);

--
-- Indexes for table `naac_submissions`
--
ALTER TABLE `naac_submissions`
  ADD PRIMARY KEY (`submission_id`),
  ADD UNIQUE KEY `dept_year` (`dept_id`,`academic_year`);

--
-- Indexes for table `nba_criteria`
--
ALTER TABLE `nba_criteria`
  ADD PRIMARY KEY (`Sub_no`);

--
-- Indexes for table `nba_criteria1`
--
ALTER TABLE `nba_criteria1`
  ADD PRIMARY KEY (`Sub_no`);

--
-- Indexes for table `nba_criteria2`
--
ALTER TABLE `nba_criteria2`
  ADD PRIMARY KEY (`Sub_no`);

--
-- Indexes for table `nba_criteria_data`
--
ALTER TABLE `nba_criteria_data`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_sub_crit` (`submission_id`,`criterion_number`);

--
-- Indexes for table `nba_criteria_files`
--
ALTER TABLE `nba_criteria_files`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_nba_file_sub` (`submission_id`),
  ADD KEY `fk_nba_file_fid` (`file_id`);

--
-- Indexes for table `nba_submissions`
--
ALTER TABLE `nba_submissions`
  ADD PRIMARY KEY (`submission_id`),
  ADD UNIQUE KEY `uk_dept_year` (`dept_id`,`academic_year`);

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
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`role_id`),
  ADD UNIQUE KEY `role_name` (`role_name`);

--
-- Indexes for table `schema_migrations`
--
ALTER TABLE `schema_migrations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `migration` (`migration`);

--
-- Indexes for table `system_audit_logs`
--
ALTER TABLE `system_audit_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `idx_actor` (`actor_id`),
  ADD KEY `idx_action` (`action`),
  ADD KEY `idx_created` (`created_at`);

--
-- Indexes for table `s_bodies`
--
ALTER TABLE `s_bodies`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `s_conference_tab`
--
ALTER TABLE `s_conference_tab`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `s_events`
--
ALTER TABLE `s_events`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `s_journal_tab`
--
ALTER TABLE `s_journal_tab`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_profiles`
--
ALTER TABLE `user_profiles`
  ADD PRIMARY KEY (`profile_id`),
  ADD UNIQUE KEY `uk_user_id` (`user_id`);

--
-- Indexes for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD PRIMARY KEY (`user_role_id`),
  ADD UNIQUE KEY `uq_user_role_dept` (`user_id`,`role_id`,`dept_id`),
  ADD KEY `role_id` (`role_id`),
  ADD KEY `dept_id` (`dept_id`);

--
-- Indexes for table `workflows`
--
ALTER TABLE `workflows`
  ADD PRIMARY KEY (`workflow_id`),
  ADD UNIQUE KEY `workflow_key` (`workflow_key`);

--
-- Indexes for table `workflow_actions`
--
ALTER TABLE `workflow_actions`
  ADD PRIMARY KEY (`action_id`),
  ADD UNIQUE KEY `action_key` (`action_key`);

--
-- Indexes for table `workflow_steps`
--
ALTER TABLE `workflow_steps`
  ADD PRIMARY KEY (`step_id`),
  ADD UNIQUE KEY `uq_workflow_step` (`workflow_id`,`step_order`),
  ADD KEY `responsible_role_id` (`responsible_role_id`);

--
-- Indexes for table `workflow_transitions`
--
ALTER TABLE `workflow_transitions`
  ADD PRIMARY KEY (`transition_id`),
  ADD UNIQUE KEY `uq_step_action` (`step_id`,`action_id`),
  ADD KEY `action_id` (`action_id`),
  ADD KEY `to_step_id` (`to_step_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `academic_years`
--
ALTER TABLE `academic_years`
  MODIFY `year_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `a_cri_files`
--
ALTER TABLE `a_cri_files`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `a_c_files`
--
ALTER TABLE `a_c_files`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `a_files`
--
ALTER TABLE `a_files`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `central_files`
--
ALTER TABLE `central_files`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `conference_tab`
--
ALTER TABLE `conference_tab`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `conf_org_tab`
--
ALTER TABLE `conf_org_tab`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dc_up_files`
--
ALTER TABLE `dc_up_files`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `dept_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `dept_files`
--
ALTER TABLE `dept_files`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `documents`
--
ALTER TABLE `documents`
  MODIFY `doc_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=93;

--
-- AUTO_INCREMENT for table `document_actions`
--
ALTER TABLE `document_actions`
  MODIFY `action_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=138;

--
-- AUTO_INCREMENT for table `document_files`
--
ALTER TABLE `document_files`
  MODIFY `file_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=183;

--
-- AUTO_INCREMENT for table `document_metadata`
--
ALTER TABLE `document_metadata`
  MODIFY `meta_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `document_meta_student_activity`
--
ALTER TABLE `document_meta_student_activity`
  MODIFY `meta_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `document_types`
--
ALTER TABLE `document_types`
  MODIFY `type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `fdps_org_tab`
--
ALTER TABLE `fdps_org_tab`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fdps_tab`
--
ALTER TABLE `fdps_tab`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `files`
--
ALTER TABLE `files`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `files5_1_1and2`
--
ALTER TABLE `files5_1_1and2`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `files5_1_3`
--
ALTER TABLE `files5_1_3`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `files5_1_4`
--
ALTER TABLE `files5_1_4`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `files5_2_1`
--
ALTER TABLE `files5_2_1`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `files5_2_2`
--
ALTER TABLE `files5_2_2`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `files5_2_3`
--
ALTER TABLE `files5_2_3`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `files5_3_1`
--
ALTER TABLE `files5_3_1`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `files5_3_3`
--
ALTER TABLE `files5_3_3`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `meta_award`
--
ALTER TABLE `meta_award`
  MODIFY `meta_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `meta_central_file`
--
ALTER TABLE `meta_central_file`
  MODIFY `meta_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `meta_conference`
--
ALTER TABLE `meta_conference`
  MODIFY `meta_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `meta_conf_organised`
--
ALTER TABLE `meta_conf_organised`
  MODIFY `meta_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `meta_criteria_file`
--
ALTER TABLE `meta_criteria_file`
  MODIFY `meta_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `meta_dept_file`
--
ALTER TABLE `meta_dept_file`
  MODIFY `meta_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `meta_exam_qual`
--
ALTER TABLE `meta_exam_qual`
  MODIFY `meta_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `meta_fdp_attended`
--
ALTER TABLE `meta_fdp_attended`
  MODIFY `meta_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `meta_fdp_organised`
--
ALTER TABLE `meta_fdp_organised`
  MODIFY `meta_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `meta_higher_ed`
--
ALTER TABLE `meta_higher_ed`
  MODIFY `meta_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `meta_journal`
--
ALTER TABLE `meta_journal`
  MODIFY `meta_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `meta_patent`
--
ALTER TABLE `meta_patent`
  MODIFY `meta_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `meta_placement`
--
ALTER TABLE `meta_placement`
  MODIFY `meta_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `meta_scholarship`
--
ALTER TABLE `meta_scholarship`
  MODIFY `meta_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `meta_student_body`
--
ALTER TABLE `meta_student_body`
  MODIFY `meta_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `meta_student_conference`
--
ALTER TABLE `meta_student_conference`
  MODIFY `meta_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `meta_student_event`
--
ALTER TABLE `meta_student_event`
  MODIFY `meta_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `meta_student_journal`
--
ALTER TABLE `meta_student_journal`
  MODIFY `meta_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `naac_criteria_data`
--
ALTER TABLE `naac_criteria_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `naac_form_schemas`
--
ALTER TABLE `naac_form_schemas`
  MODIFY `schema_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `naac_submissions`
--
ALTER TABLE `naac_submissions`
  MODIFY `submission_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `nba_criteria_data`
--
ALTER TABLE `nba_criteria_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `nba_criteria_files`
--
ALTER TABLE `nba_criteria_files`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nba_submissions`
--
ALTER TABLE `nba_submissions`
  MODIFY `submission_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `patents_table`
--
ALTER TABLE `patents_table`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `published_tab`
--
ALTER TABLE `published_tab`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `schema_migrations`
--
ALTER TABLE `schema_migrations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `system_audit_logs`
--
ALTER TABLE `system_audit_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=290;

--
-- AUTO_INCREMENT for table `s_bodies`
--
ALTER TABLE `s_bodies`
  MODIFY `ID` int(255) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `s_conference_tab`
--
ALTER TABLE `s_conference_tab`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `s_events`
--
ALTER TABLE `s_events`
  MODIFY `ID` int(255) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `s_journal_tab`
--
ALTER TABLE `s_journal_tab`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=162;

--
-- AUTO_INCREMENT for table `user_profiles`
--
ALTER TABLE `user_profiles`
  MODIFY `profile_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT for table `user_roles`
--
ALTER TABLE `user_roles`
  MODIFY `user_role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=276;

--
-- AUTO_INCREMENT for table `workflows`
--
ALTER TABLE `workflows`
  MODIFY `workflow_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `workflow_actions`
--
ALTER TABLE `workflow_actions`
  MODIFY `action_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `workflow_steps`
--
ALTER TABLE `workflow_steps`
  MODIFY `step_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `workflow_transitions`
--
ALTER TABLE `workflow_transitions`
  MODIFY `transition_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `documents`
--
ALTER TABLE `documents`
  ADD CONSTRAINT `documents_ibfk_1` FOREIGN KEY (`type_id`) REFERENCES `document_types` (`type_id`),
  ADD CONSTRAINT `documents_ibfk_2` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `documents_ibfk_3` FOREIGN KEY (`dept_id`) REFERENCES `departments` (`dept_id`),
  ADD CONSTRAINT `documents_ibfk_4` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`year_id`),
  ADD CONSTRAINT `documents_ibfk_5` FOREIGN KEY (`current_step`) REFERENCES `workflow_steps` (`step_id`);

--
-- Constraints for table `document_actions`
--
ALTER TABLE `document_actions`
  ADD CONSTRAINT `document_actions_ibfk_1` FOREIGN KEY (`doc_id`) REFERENCES `documents` (`doc_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `document_actions_ibfk_2` FOREIGN KEY (`acted_by`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `document_actions_ibfk_3` FOREIGN KEY (`step_id`) REFERENCES `workflow_steps` (`step_id`);

--
-- Constraints for table `document_files`
--
ALTER TABLE `document_files`
  ADD CONSTRAINT `document_files_ibfk_1` FOREIGN KEY (`doc_id`) REFERENCES `documents` (`doc_id`) ON DELETE CASCADE;

--
-- Constraints for table `document_metadata`
--
ALTER TABLE `document_metadata`
  ADD CONSTRAINT `document_metadata_ibfk_1` FOREIGN KEY (`doc_id`) REFERENCES `documents` (`doc_id`) ON DELETE CASCADE;

--
-- Constraints for table `document_meta_student_activity`
--
ALTER TABLE `document_meta_student_activity`
  ADD CONSTRAINT `document_meta_student_activity_ibfk_1` FOREIGN KEY (`doc_id`) REFERENCES `documents` (`doc_id`) ON DELETE CASCADE;

--
-- Constraints for table `document_types`
--
ALTER TABLE `document_types`
  ADD CONSTRAINT `document_types_ibfk_1` FOREIGN KEY (`workflow_id`) REFERENCES `workflows` (`workflow_id`);

--
-- Constraints for table `meta_award`
--
ALTER TABLE `meta_award`
  ADD CONSTRAINT `fk_meta_award_doc` FOREIGN KEY (`doc_id`) REFERENCES `documents` (`doc_id`) ON DELETE CASCADE;

--
-- Constraints for table `meta_central_file`
--
ALTER TABLE `meta_central_file`
  ADD CONSTRAINT `fk_meta_central_file_doc` FOREIGN KEY (`doc_id`) REFERENCES `documents` (`doc_id`) ON DELETE CASCADE;

--
-- Constraints for table `meta_conference`
--
ALTER TABLE `meta_conference`
  ADD CONSTRAINT `fk_meta_conference_doc` FOREIGN KEY (`doc_id`) REFERENCES `documents` (`doc_id`) ON DELETE CASCADE;

--
-- Constraints for table `meta_conf_organised`
--
ALTER TABLE `meta_conf_organised`
  ADD CONSTRAINT `fk_meta_conf_organised_doc` FOREIGN KEY (`doc_id`) REFERENCES `documents` (`doc_id`) ON DELETE CASCADE;

--
-- Constraints for table `meta_criteria_file`
--
ALTER TABLE `meta_criteria_file`
  ADD CONSTRAINT `fk_meta_criteria_file_doc` FOREIGN KEY (`doc_id`) REFERENCES `documents` (`doc_id`) ON DELETE CASCADE;

--
-- Constraints for table `meta_dept_file`
--
ALTER TABLE `meta_dept_file`
  ADD CONSTRAINT `fk_meta_dept_file_doc` FOREIGN KEY (`doc_id`) REFERENCES `documents` (`doc_id`) ON DELETE CASCADE;

--
-- Constraints for table `meta_exam_qual`
--
ALTER TABLE `meta_exam_qual`
  ADD CONSTRAINT `fk_meta_exam_qual_doc` FOREIGN KEY (`doc_id`) REFERENCES `documents` (`doc_id`) ON DELETE CASCADE;

--
-- Constraints for table `meta_fdp_attended`
--
ALTER TABLE `meta_fdp_attended`
  ADD CONSTRAINT `fk_meta_fdp_attended_doc` FOREIGN KEY (`doc_id`) REFERENCES `documents` (`doc_id`) ON DELETE CASCADE;

--
-- Constraints for table `meta_fdp_organised`
--
ALTER TABLE `meta_fdp_organised`
  ADD CONSTRAINT `fk_meta_fdp_organised_doc` FOREIGN KEY (`doc_id`) REFERENCES `documents` (`doc_id`) ON DELETE CASCADE;

--
-- Constraints for table `meta_higher_ed`
--
ALTER TABLE `meta_higher_ed`
  ADD CONSTRAINT `fk_meta_higher_ed_doc` FOREIGN KEY (`doc_id`) REFERENCES `documents` (`doc_id`) ON DELETE CASCADE;

--
-- Constraints for table `meta_journal`
--
ALTER TABLE `meta_journal`
  ADD CONSTRAINT `fk_meta_journal_doc` FOREIGN KEY (`doc_id`) REFERENCES `documents` (`doc_id`) ON DELETE CASCADE;

--
-- Constraints for table `meta_patent`
--
ALTER TABLE `meta_patent`
  ADD CONSTRAINT `fk_meta_patent_doc` FOREIGN KEY (`doc_id`) REFERENCES `documents` (`doc_id`) ON DELETE CASCADE;

--
-- Constraints for table `meta_placement`
--
ALTER TABLE `meta_placement`
  ADD CONSTRAINT `fk_meta_placement_doc` FOREIGN KEY (`doc_id`) REFERENCES `documents` (`doc_id`) ON DELETE CASCADE;

--
-- Constraints for table `meta_scholarship`
--
ALTER TABLE `meta_scholarship`
  ADD CONSTRAINT `fk_meta_scholarship_doc` FOREIGN KEY (`doc_id`) REFERENCES `documents` (`doc_id`) ON DELETE CASCADE;

--
-- Constraints for table `meta_student_activity_file`
--
ALTER TABLE `meta_student_activity_file`
  ADD CONSTRAINT `meta_student_activity_file_ibfk_1` FOREIGN KEY (`doc_id`) REFERENCES `documents` (`doc_id`) ON DELETE CASCADE;

--
-- Constraints for table `meta_student_body`
--
ALTER TABLE `meta_student_body`
  ADD CONSTRAINT `fk_meta_student_body_doc` FOREIGN KEY (`doc_id`) REFERENCES `documents` (`doc_id`) ON DELETE CASCADE;

--
-- Constraints for table `meta_student_conference`
--
ALTER TABLE `meta_student_conference`
  ADD CONSTRAINT `fk_meta_student_conference_doc` FOREIGN KEY (`doc_id`) REFERENCES `documents` (`doc_id`) ON DELETE CASCADE;

--
-- Constraints for table `meta_student_event`
--
ALTER TABLE `meta_student_event`
  ADD CONSTRAINT `fk_meta_student_event_doc` FOREIGN KEY (`doc_id`) REFERENCES `documents` (`doc_id`) ON DELETE CASCADE;

--
-- Constraints for table `meta_student_journal`
--
ALTER TABLE `meta_student_journal`
  ADD CONSTRAINT `fk_meta_student_journal_doc` FOREIGN KEY (`doc_id`) REFERENCES `documents` (`doc_id`) ON DELETE CASCADE;

--
-- Constraints for table `nba_criteria_data`
--
ALTER TABLE `nba_criteria_data`
  ADD CONSTRAINT `fk_nba_data_sub` FOREIGN KEY (`submission_id`) REFERENCES `nba_submissions` (`submission_id`) ON DELETE CASCADE;

--
-- Constraints for table `nba_criteria_files`
--
ALTER TABLE `nba_criteria_files`
  ADD CONSTRAINT `fk_nba_file_fid` FOREIGN KEY (`file_id`) REFERENCES `document_files` (`file_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_nba_file_sub` FOREIGN KEY (`submission_id`) REFERENCES `nba_submissions` (`submission_id`) ON DELETE CASCADE;

--
-- Constraints for table `nba_submissions`
--
ALTER TABLE `nba_submissions`
  ADD CONSTRAINT `fk_nba_sub_dept` FOREIGN KEY (`dept_id`) REFERENCES `departments` (`dept_id`) ON DELETE CASCADE;

--
-- Constraints for table `user_profiles`
--
ALTER TABLE `user_profiles`
  ADD CONSTRAINT `fk_profile_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD CONSTRAINT `user_roles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `user_roles_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`),
  ADD CONSTRAINT `user_roles_ibfk_3` FOREIGN KEY (`dept_id`) REFERENCES `departments` (`dept_id`);

--
-- Constraints for table `workflow_steps`
--
ALTER TABLE `workflow_steps`
  ADD CONSTRAINT `workflow_steps_ibfk_1` FOREIGN KEY (`workflow_id`) REFERENCES `workflows` (`workflow_id`),
  ADD CONSTRAINT `workflow_steps_ibfk_2` FOREIGN KEY (`responsible_role_id`) REFERENCES `roles` (`role_id`);

--
-- Constraints for table `workflow_transitions`
--
ALTER TABLE `workflow_transitions`
  ADD CONSTRAINT `workflow_transitions_ibfk_1` FOREIGN KEY (`step_id`) REFERENCES `workflow_steps` (`step_id`),
  ADD CONSTRAINT `workflow_transitions_ibfk_2` FOREIGN KEY (`action_id`) REFERENCES `workflow_actions` (`action_id`),
  ADD CONSTRAINT `workflow_transitions_ibfk_3` FOREIGN KEY (`to_step_id`) REFERENCES `workflow_steps` (`step_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
