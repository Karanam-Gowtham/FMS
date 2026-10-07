<?php
require_once __DIR__ . '/core/bootstrap.php';
global $conn;

// ---------------------------------------------------------
// CRITERION 1 - UNIVERSITY SCHEMA WITH DATA TEMPLATES
// ---------------------------------------------------------
$schema_c1 = [
    "sections" => [
        [
            "title" => "1.1 Curriculum Design and Development",
            "metrics" => [
                [
                    "id" => "1.1.1",
                    "title" => "1.1.1: Curricula developed and implemented have relevance to needs",
                    "description" => "Curricula developed and implemented have relevance to the local, national, regional and global developmental needs which is reflected in POs, PSOs and COs.",
                    "fields" => [
                        ["name" => "qlm_1_1_1_text", "label" => "Description (Max 500 words)", "type" => "textarea"],
                        ["name" => "file_1_1_1", "label" => "Document Upload", "type" => "file", "accept" => ".pdf"]
                    ]
                ],
                [
                    "id" => "1.1.2",
                    "title" => "1.1.2: Syllabus revision",
                    "description" => "Percentage of Programmes where syllabus revision was carried out during the last five years.",
                    "fields" => [
                        ["name" => "qnm_1_1_2_revised", "label" => "Number of programmes where syllabus was revised (Formula Numerator)", "type" => "number"],
                        ["name" => "qnm_1_1_2_total", "label" => "Total number of programmes offered (Formula Denominator)", "type" => "number"],
                        [
                            "name" => "table_1_1_2",
                            "label" => "Data Template: Programmes and Syllabus Revision",
                            "type" => "table",
                            "columns" => [
                                ["name" => "prog_code", "label" => "Programme Code", "type" => "text"],
                                ["name" => "prog_name", "label" => "Programme Name", "type" => "text"],
                                ["name" => "dept_name", "label" => "Department", "type" => "text"],
                                ["name" => "year_intro", "label" => "Year of Introduction", "type" => "number"],
                                ["name" => "is_revised", "label" => "Revised in last 5 years? (Yes/No)", "type" => "text"],
                                ["name" => "year_revision", "label" => "Year of Revision", "type" => "number"],
                                ["name" => "pct_replaced", "label" => "% Content replaced", "type" => "number"]
                            ]
                        ],
                        ["name" => "file_1_1_2", "label" => "Minutes of relevant Academic Council/BOS meeting", "type" => "file", "accept" => ".pdf"]
                    ]
                ],
                [
                    "id" => "1.1.3",
                    "title" => "1.1.3: Courses focusing on employability/entrepreneurship/skill development",
                    "description" => "Average percentage of courses having focus on employability/entrepreneurship/skill development.",
                    "fields" => [
                        ["name" => "qnm_1_1_3_focused", "label" => "Number of courses focusing on employability/entrepreneurship/skill dev", "type" => "number"],
                        ["name" => "qnm_1_1_3_total", "label" => "Total number of courses in all programmes", "type" => "number"],
                        [
                            "name" => "table_1_1_3",
                            "label" => "Data Template: Employability & Skill Development Courses",
                            "type" => "table",
                            "columns" => [
                                ["name" => "course_name", "label" => "Course Name", "type" => "text"],
                                ["name" => "course_code", "label" => "Course Code", "type" => "text"],
                                ["name" => "prog_name", "label" => "Programme Name", "type" => "text"],
                                ["name" => "activities", "label" => "Activities bearing on Employability", "type" => "text"],
                                ["name" => "year_intro", "label" => "Year of Introduction", "type" => "number"]
                            ]
                        ],
                        ["name" => "file_1_1_3", "label" => "Programme/Curriculum/Syllabus of courses", "type" => "file", "accept" => ".pdf"]
                    ]
                ]
            ]
        ],
        [
            "title" => "1.2 Academic Flexibility",
            "metrics" => [
                [
                    "id" => "1.2.1",
                    "title" => "1.2.1: New courses introduced",
                    "description" => "Percentage of new courses introduced of the total number of courses across all Programmes offered during the last five years.",
                    "fields" => [
                        ["name" => "qnm_1_2_1_new", "label" => "Number of new courses introduced", "type" => "number"],
                        ["name" => "qnm_1_2_1_total", "label" => "Total number of courses offered", "type" => "number"],
                        [
                            "name" => "table_1_2_1",
                            "label" => "Data Template: New Courses",
                            "type" => "table",
                            "columns" => [
                                ["name" => "course_name", "label" => "Name of new course", "type" => "text"],
                                ["name" => "course_code", "label" => "Course Code", "type" => "text"],
                                ["name" => "prog_name", "label" => "Programme Name", "type" => "text"],
                                ["name" => "year_intro", "label" => "Year of Introduction", "type" => "number"]
                            ]
                        ],
                        ["name" => "file_1_2_1", "label" => "Document Upload", "type" => "file", "accept" => ".pdf"]
                    ]
                ],
                [
                    "id" => "1.2.2",
                    "title" => "1.2.2: Choice Based Credit System (CBCS)/elective course system",
                    "description" => "Percentage of Programmes in which CBCS/elective course system has been implemented.",
                    "fields" => [
                        ["name" => "qnm_1_2_2_cbcs", "label" => "Number of programmes in which CBCS/elective course system implemented", "type" => "number"],
                        ["name" => "qnm_1_2_2_total", "label" => "Total number of programmes offered", "type" => "number"],
                        [
                            "name" => "table_1_2_2",
                            "label" => "Data Template: CBCS Programmes",
                            "type" => "table",
                            "columns" => [
                                ["name" => "prog_name", "label" => "Programme Name", "type" => "text"],
                                ["name" => "prog_code", "label" => "Programme Code", "type" => "text"],
                                ["name" => "is_cbcs", "label" => "CBCS Implemented? (Yes/No)", "type" => "text"],
                                ["name" => "year_cbcs", "label" => "Year of Implementation", "type" => "number"]
                            ]
                        ],
                        ["name" => "file_1_2_2", "label" => "Document Upload", "type" => "file", "accept" => ".pdf"]
                    ]
                ]
            ]
        ],
        [
            "title" => "1.3 Curriculum Enrichment",
            "metrics" => [
                [
                    "id" => "1.3.1",
                    "title" => "1.3.1: Integration of crosscutting issues",
                    "description" => "Institution integrates crosscutting issues relevant to Professional Ethics, Gender, Human Values, Environment and Sustainability into the Curriculum.",
                    "fields" => [
                        ["name" => "qlm_1_3_1_text", "label" => "Description (Max 500 words)", "type" => "textarea"],
                        ["name" => "file_1_3_1", "label" => "Document Upload", "type" => "file", "accept" => ".pdf"]
                    ]
                ],
                [
                    "id" => "1.3.2",
                    "title" => "1.3.2: Value-added courses",
                    "description" => "Number of value-added courses imparting transferable and life skills offered during the last five years.",
                    "fields" => [
                        ["name" => "qnm_1_3_2_courses", "label" => "Number of value-added courses", "type" => "number"],
                        [
                            "name" => "table_1_3_2",
                            "label" => "Data Template: Value-added Courses",
                            "type" => "table",
                            "columns" => [
                                ["name" => "course_name", "label" => "Name of value added course", "type" => "text"],
                                ["name" => "course_code", "label" => "Course Code", "type" => "text"],
                                ["name" => "year_offering", "label" => "Year of offering", "type" => "number"],
                                ["name" => "times_offered", "label" => "Times offered in year", "type" => "number"],
                                ["name" => "duration", "label" => "Duration (Hours)", "type" => "number"]
                            ]
                        ],
                        ["name" => "file_1_3_2", "label" => "Document Upload", "type" => "file", "accept" => ".pdf"]
                    ]
                ],
                [
                    "id" => "1.3.3",
                    "title" => "1.3.3: Students enrolled in value-added courses",
                    "description" => "Average Percentage of students enrolled in the value-added courses.",
                    "fields" => [
                        ["name" => "qnm_1_3_3_enrolled", "label" => "Number of students enrolled in value-added courses", "type" => "number"],
                        ["name" => "qnm_1_3_3_total", "label" => "Total number of students in institution", "type" => "number"],
                        [
                            "name" => "table_1_3_3",
                            "label" => "Data Template: Value-added Course Enrollment",
                            "type" => "table",
                            "columns" => [
                                ["name" => "course_name", "label" => "Name of value added course", "type" => "text"],
                                ["name" => "course_code", "label" => "Course Code", "type" => "text"],
                                ["name" => "enrolled_count", "label" => "Students enrolled", "type" => "number"],
                                ["name" => "completed_count", "label" => "Students completed", "type" => "number"]
                            ]
                        ],
                        ["name" => "file_1_3_3", "label" => "Document Upload", "type" => "file", "accept" => ".pdf"]
                    ]
                ],
                [
                    "id" => "1.3.4",
                    "title" => "1.3.4: Field projects/ internships/ student projects",
                    "description" => "Percentage of students undertaking field projects/ internships / student projects.",
                    "fields" => [
                        ["name" => "qnm_1_3_4_undertaking", "label" => "Number of students undertaking projects/internships", "type" => "number"],
                        ["name" => "qnm_1_3_4_total", "label" => "Total number of students", "type" => "number"],
                        [
                            "name" => "table_1_3_4",
                            "label" => "Data Template: Internships and Projects",
                            "type" => "table",
                            "columns" => [
                                ["name" => "prog_name", "label" => "Programme Name", "type" => "text"],
                                ["name" => "prog_code", "label" => "Programme Code", "type" => "text"],
                                ["name" => "student_name", "label" => "Name of Student", "type" => "text"],
                                ["name" => "project_type", "label" => "Type (Project/Internship/Field Work)", "type" => "text"]
                            ]
                        ],
                        ["name" => "file_1_3_4", "label" => "Document Upload", "type" => "file", "accept" => ".pdf"]
                    ]
                ]
            ]
        ],
        [
            "title" => "1.4 Feedback System",
            "metrics" => [
                [
                    "id" => "1.4.1",
                    "title" => "1.4.1: Structured feedback for design and review of syllabus",
                    "description" => "Structured feedback for design and review of syllabus received from Stakeholders.",
                    "fields" => [
                        [
                            "name" => "feedback_stakeholders",
                            "label" => "Feedback received from (Check all that apply):",
                            "type" => "checkbox_group",
                            "options" => [
                                ["name" => "fb_students", "label" => "Students"],
                                ["name" => "fb_teachers", "label" => "Teachers"],
                                ["name" => "fb_employers", "label" => "Employers"],
                                ["name" => "fb_alumni", "label" => "Alumni"]
                            ]
                        ],
                        ["name" => "file_1_4_1", "label" => "Document Upload (Feedback Reports)", "type" => "file", "accept" => ".pdf"]
                    ]
                ],
                [
                    "id" => "1.4.2",
                    "title" => "1.4.2: Feedback processes",
                    "description" => "Feedback processes of the institution.",
                    "fields" => [
                        ["name" => "qlm_1_4_2_process", "label" => "Describe the process of collecting and analyzing feedback", "type" => "textarea"],
                        ["name" => "file_1_4_2", "label" => "Document Upload (Action Taken Report)", "type" => "file", "accept" => ".pdf"]
                    ]
                ]
            ]
        ]
    ]
];

$json_str_c1 = json_encode($schema_c1);
// Disable previous schemas for C1
$conn->query("UPDATE naac_form_schemas SET is_active = 0 WHERE criterion_number = 1");
$stmt1 = $conn->prepare("INSERT INTO naac_form_schemas (criterion_number, version_name, is_active, schema_json) VALUES (1, 'v4_university_with_tables', 1, ?)");
$stmt1->bind_param("s", $json_str_c1);
$stmt1->execute();
echo "Inserted Criterion 1 (University with Tables) Schema and set it as active.\n";
