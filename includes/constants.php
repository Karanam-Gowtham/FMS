<?php
// Criteria Constants
define('CRIT_1_1_1', '1.1.1');
define('CRIT_1_1_2', '1.1.2');
define('CRIT_1_1_3', '1.1.3');
define('CRIT_1_2_1', '1.2.1');
define('CRIT_1_2_2', '1.2.2');
define('CRIT_1_3_1', '1.3.1');
define('CRIT_1_3_2', '1.3.2');
define('CRIT_1_3_3', '1.3.3');
define('CRIT_1_3_4', '1.3.4');
define('CRIT_1_4_1', '1.4.1');
define('CRIT_1_4_2', '1.4.2');

define('CRIT_5_1_1', '5.1.1');
define('CRIT_5_1_2', '5.1.2');
define('CRIT_5_1_3', '5.1.3');
define('CRIT_5_1_4', '5.1.4');
define('CRIT_5_1_5', '5.1.5');
define('CRIT_5_2_1', '5.2.1');
define('CRIT_5_2_2', '5.2.2');
define('CRIT_5_2_3', '5.2.3');
define('CRIT_5_3_1', '5.3.1');
define('CRIT_5_3_2', '5.3.2');
define('CRIT_5_3_3', '5.3.3');
define('CRIT_5_4_1', '5.4.1');
define('CRIT_5_4_2', '5.4.2');

define('CRIT_6_1_1_A', '6.1.1(A)');
define('CRIT_6_1_1_B', '6.1.1(B)');
define('CRIT_6_1_1_C', '6.1.1(C)');
define('CRIT_6_1_1_D', '6.1.1(D)');
define('CRIT_6_1_1_E', '6.1.1(E)');
define('CRIT_6_1_1_F', '6.1.1(F)');
define('CRIT_6_1_1_G', '6.1.1(G)');
define('CRIT_6_1_1_H', '6.1.1(H)');
define('CRIT_6_1_1_I', '6.1.1(I)');

// Table Names
define('TABLE_FILES', 'files');
define('TABLE_ACADEMIC_YEAR', 'academic_year');

// Column Labels
define('COL_FACULTY', 'Faculty Name');
define('COL_YEAR', 'Academic Year');
define('COL_FILE', 'file name');
define('COL_DESC', 'description');
define('COL_STUDENT_NAME', 'Student Name');

// User Role Tables
define('TABLE_REG_FACULTY', 'reg_tab');
define('TABLE_REG_DEPT_CORD', 'reg_dept_cord');
define('TABLE_REG_HOD', 'reg_hod');
define('TABLE_REG_JR_ASSISTANT', 'reg_jr_assistant');
define('TABLE_LOGIN_PG', 'login_pg');

// Achievement Tables
define('TABLE_PUBLISHED', 'published_tab');
define('TABLE_FDPS_ATTENDED', 'fdps_tab');
define('TABLE_CONFERENCE', 'conference_tab');
define('TABLE_PATENTS', 'patents_tab');
define('TABLE_DEPT_FILES', 'dept_files');

// Common Strings
define('CATEGORY_JOURNALS', "Journals");
define('CATEGORY_CONFERENCES', "Conferences");
define('PROFESSIONAL_BODIES', "Professional Bodies");
define('LABEL_UPLOADED_BY', "Uploaded By");
define('LABEL_SUBMISSION_TIME', "Submission Time");
define('LABEL_ORGANISED_BY', "Organised By");
define('LABEL_FROM_DATE', "From Date");
define('LABEL_TO_DATE', "To Date");
define('PARAM_DESIGNATION', "&designation=");
define('PARAM_CRITERIA', "&criteria=");
define('PARAM_EVENT', "&event=");
define('SQL_AND_STATUS_EQ', " AND status = '");
define('LOC_C_AQAR_FILES', "Location: c_aqar_files.php?designation=");
define('REGEX_SPECIAL_CHARS', "/[^a-zA-Z0-9_.-]/");
define('DATE_FORMAT_DMY', "d/m/Y");
define('REGEX_UPLOADS', "/uploads\/.*/");
define('PATH_UPLOADS', "../uploads/");
define('PATH_DEEP_UPLOADS', "../../uploads/");
define('HTM_QUOT', "&quot;");
define('DATA_FILES_PREFIX', "' data-files='");

// Statuses
define('STATUS_PENDING_HOD', 'Pending HOD');
define('STATUS_PENDING_DEPT_COORD', 'Pending Dept Coordinator');
define('STATUS_PENDING_CENTRAL', 'Pending Central Coordinator');
define('STATUS_ACCEPTED', 'Accepted');
define('STATUS_REJECTED', 'Rejected');
define('STATUS_REJECTED_HOD', 'Rejected by HOD');
define('STATUS_REJECTED_DEPT_COORD', 'Rejected by Dept Coordinator');

// Error Messages
define('ERR_INVALID_CRIT', 'Invalid criteria or sub-criteria.');

// Other Constants
define('DIR_UP', '../');
define('DIR_UP_TWO', '../../');
define('ATTR_DATA_FILEPATH', " data-filepath='");
define('ATTR_VAL_DATA_PATH', "' data-filepath='");
define('QUOTE_SPACE', "' ");
define('NOT_SELECTED', 'Not Selected');
define('HEADER_CONTENT_DISPOSITION', 'Content-Disposition: attachment; filename="');
define('HEADER_CONTENT_LENGTH', 'Content-Length: ');
define('TYPE_EXCEL', 'Content-Type: application/vnd.ms-excel');
define('TYPE_OCTET_STREAM', 'Content-Type: application/octet-stream');
define('TYPE_JSON', 'Content-Type: application/json');
?>
