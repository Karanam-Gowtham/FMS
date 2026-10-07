<?php
require_once __DIR__ . '/core/bootstrap.php';
global $conn;

$schema_c2 = [
    "sections" => [
        [
            "title" => "2.1 Student Enrolment and Profile",
            "metrics" => [
                [
                    "id" => "2.1.1",
                    "title" => "2.1.1: Demand Ratio",
                    "description" => "Demand Ratio: Number of seats available vs Number of eligible applications received.",
                    "fields" => [
                        ["name" => "qnm_2_1_1_apps", "label" => "Number of eligible applications received (Formula Numerator)", "type" => "number"],
                        ["name" => "qnm_2_1_1_seats", "label" => "Number of seats available (Formula Denominator)", "type" => "number"],
                        [
                            "name" => "table_2_1_1",
                            "label" => "Data Template: Demand Ratio",
                            "type" => "table",
                            "columns" => [
                                ["name" => "prog_name", "label" => "Programme Name", "type" => "text"],
                                ["name" => "prog_code", "label" => "Programme Code", "type" => "text"],
                                ["name" => "seats_avail", "label" => "Seats Available", "type" => "number"],
                                ["name" => "apps_recv", "label" => "Applications Received", "type" => "number"],
                                ["name" => "students_admit", "label" => "Students Admitted", "type" => "number"]
                            ]
                        ],
                        ["name" => "file_2_1_1", "label" => "Document Upload", "type" => "file", "accept" => ".pdf"]
                    ]
                ],
                [
                    "id" => "2.1.2",
                    "title" => "2.1.2: Reserved Categories",
                    "description" => "Average percentage of seats filled against reserved categories (SC, ST, OBC, Divyangjan, etc.).",
                    "fields" => [
                        ["name" => "qnm_2_1_2_filled", "label" => "Seats filled against reserved categories (Formula Numerator)", "type" => "number"],
                        ["name" => "qnm_2_1_2_reserved", "label" => "Total seats earmarked for reserved categories (Formula Denominator)", "type" => "number"],
                        [
                            "name" => "table_2_1_2",
                            "label" => "Data Template: Reserved Category Admissions",
                            "type" => "table",
                            "columns" => [
                                ["name" => "year", "label" => "Year", "type" => "number"],
                                ["name" => "earmark_sc", "label" => "Earmarked SC", "type" => "number"],
                                ["name" => "earmark_st", "label" => "Earmarked ST", "type" => "number"],
                                ["name" => "earmark_obc", "label" => "Earmarked OBC", "type" => "number"],
                                ["name" => "earmark_gen", "label" => "Earmarked Gen", "type" => "number"],
                                ["name" => "admit_sc", "label" => "Admitted SC", "type" => "number"],
                                ["name" => "admit_st", "label" => "Admitted ST", "type" => "number"],
                                ["name" => "admit_obc", "label" => "Admitted OBC", "type" => "number"],
                                ["name" => "admit_gen", "label" => "Admitted Gen", "type" => "number"]
                            ]
                        ],
                        ["name" => "file_2_1_2", "label" => "Document Upload", "type" => "file", "accept" => ".pdf"]
                    ]
                ]
            ]
        ],
        [
            "title" => "2.2 Catering to Student Diversity",
            "metrics" => [
                [
                    "id" => "2.2.1",
                    "title" => "2.2.1: Advanced and Slow Learners",
                    "description" => "The institution assesses the learning levels of the students and organizes special Programmes for advanced learners and slow learners.",
                    "fields" => [
                        ["name" => "qlm_2_2_1_text", "label" => "Description", "type" => "textarea"],
                        ["name" => "file_2_2_1", "label" => "Supporting Document", "type" => "file", "accept" => ".pdf"]
                    ]
                ],
                [
                    "id" => "2.2.2",
                    "title" => "2.2.2: Student-Teacher Ratio",
                    "description" => "Student - Full time teacher ratio.",
                    "fields" => [
                        ["name" => "qnm_2_2_2_students", "label" => "Total Number of Students", "type" => "number"],
                        ["name" => "qnm_2_2_2_teachers", "label" => "Total Number of Full-Time Teachers", "type" => "number"]
                    ]
                ]
            ]
        ],
        [
            "title" => "2.3 Teaching-Learning Process",
            "metrics" => [
                [
                    "id" => "2.3.1",
                    "title" => "2.3.1: Student-centric methods",
                    "description" => "Student centric methods, such as experiential learning, participative learning and problem solving methodologies.",
                    "fields" => [
                        ["name" => "qlm_2_3_1_text", "label" => "Description", "type" => "textarea"],
                        ["name" => "file_2_3_1", "label" => "Supporting Document", "type" => "file", "accept" => ".pdf"]
                    ]
                ],
                [
                    "id" => "2.3.2",
                    "title" => "2.3.2: ICT Tools",
                    "description" => "Teachers use ICT enabled tools for effective teaching-learning process.",
                    "fields" => [
                        ["name" => "qlm_2_3_2_text", "label" => "Description of ICT tools used", "type" => "textarea"],
                        ["name" => "file_2_3_2", "label" => "Supporting Document", "type" => "file", "accept" => ".pdf"]
                    ]
                ],
                [
                    "id" => "2.3.3",
                    "title" => "2.3.3: Mentor-Mentee Ratio",
                    "description" => "Ratio of students to mentor for academic and other related issues.",
                    "fields" => [
                        ["name" => "qnm_2_3_3_students", "label" => "Number of Students assigned to mentors (Numerator)", "type" => "number"],
                        ["name" => "qnm_2_3_3_mentors", "label" => "Number of Mentors (Denominator)", "type" => "number"],
                        [
                            "name" => "table_2_3_3",
                            "label" => "Data Template: Mentor-Mentee",
                            "type" => "table",
                            "columns" => [
                                ["name" => "mentor_name", "label" => "Name of Mentor", "type" => "text"],
                                ["name" => "mentor_dept", "label" => "Department", "type" => "text"],
                                ["name" => "assigned_count", "label" => "Students Assigned", "type" => "number"]
                            ]
                        ],
                        ["name" => "file_2_3_3", "label" => "Mentor-Mentee List Document", "type" => "file", "accept" => ".pdf"]
                    ]
                ]
            ]
        ],
        [
            "title" => "2.4 Teacher Profile and Quality",
            "metrics" => [
                [
                    "id" => "2.4.1",
                    "title" => "2.4.1: Full-time teachers against sanctioned posts",
                    "description" => "Average percentage of full time teachers against sanctioned posts.",
                    "fields" => [
                        ["name" => "qnm_2_4_1_teachers", "label" => "Number of Full-Time Teachers (Numerator)", "type" => "number"],
                        ["name" => "qnm_2_4_1_sanctioned", "label" => "Number of Sanctioned Posts (Denominator)", "type" => "number"],
                        [
                            "name" => "table_2_4_1",
                            "label" => "Data Template: Teachers vs Sanctioned",
                            "type" => "table",
                            "columns" => [
                                ["name" => "teacher_name", "label" => "Name of Teacher", "type" => "text"],
                                ["name" => "pan", "label" => "PAN", "type" => "text"],
                                ["name" => "designation", "label" => "Designation", "type" => "text"],
                                ["name" => "year_appt", "label" => "Year of Appointment", "type" => "number"],
                                ["name" => "nature_appt", "label" => "Nature (Permanent/Temp)", "type" => "text"],
                                ["name" => "dept", "label" => "Department", "type" => "text"]
                            ]
                        ],
                        ["name" => "file_2_4_1", "label" => "Supporting Document", "type" => "file", "accept" => ".pdf"]
                    ]
                ],
                [
                    "id" => "2.4.2",
                    "title" => "2.4.2: Teachers with Ph.D.",
                    "description" => "Average percentage of full time teachers with Ph.D./D.M/M.Ch./D.N.B Superspeciality/D.Sc./D'Lit.",
                    "fields" => [
                        ["name" => "qnm_2_4_2_phd", "label" => "Number of teachers with Ph.D. etc.", "type" => "number"],
                        [
                            "name" => "table_2_4_2",
                            "label" => "Data Template: Teachers with Ph.D.",
                            "type" => "table",
                            "columns" => [
                                ["name" => "teacher_name", "label" => "Name of Teacher", "type" => "text"],
                                ["name" => "qualification", "label" => "Qualification (Ph.D/D.M etc)", "type" => "text"],
                                ["name" => "year_obt", "label" => "Year Obtained", "type" => "number"],
                                ["name" => "is_guide", "label" => "Research Guide? (Yes/No)", "type" => "text"],
                                ["name" => "still_serving", "label" => "Still Serving? (Yes/No)", "type" => "text"]
                            ]
                        ],
                        ["name" => "file_2_4_2", "label" => "Supporting Document", "type" => "file", "accept" => ".pdf"]
                    ]
                ],
                [
                    "id" => "2.4.3",
                    "title" => "2.4.3: Teaching experience",
                    "description" => "Average teaching experience of full time teachers in the same institution.",
                    "fields" => [
                        ["name" => "qnm_2_4_3_years", "label" => "Total experience of full-time teachers (in years)", "type" => "number"],
                        [
                            "name" => "table_2_4_3",
                            "label" => "Data Template: Teaching Experience",
                            "type" => "table",
                            "columns" => [
                                ["name" => "teacher_name", "label" => "Name of Teacher", "type" => "text"],
                                ["name" => "designation", "label" => "Designation", "type" => "text"],
                                ["name" => "dept", "label" => "Department", "type" => "text"],
                                ["name" => "year_appt", "label" => "Year of Appointment", "type" => "number"],
                                ["name" => "exp_years", "label" => "Experience (Years)", "type" => "number"],
                                ["name" => "still_serving", "label" => "Still Serving?", "type" => "text"]
                            ]
                        ],
                        ["name" => "file_2_4_3", "label" => "Supporting Document", "type" => "file", "accept" => ".pdf"]
                    ]
                ],
                [
                    "id" => "2.4.4",
                    "title" => "2.4.4: Teacher Awards",
                    "description" => "Average percentage of full time teachers who received awards, recognition.",
                    "fields" => [
                        ["name" => "qnm_2_4_4_awards", "label" => "Number of teachers receiving awards", "type" => "number"],
                        [
                            "name" => "table_2_4_4",
                            "label" => "Data Template: Teacher Awards",
                            "type" => "table",
                            "columns" => [
                                ["name" => "teacher_name", "label" => "Name of Teacher", "type" => "text"],
                                ["name" => "year_award", "label" => "Year of Award", "type" => "number"],
                                ["name" => "pan", "label" => "PAN", "type" => "text"],
                                ["name" => "award_name", "label" => "Name of Award", "type" => "text"],
                                ["name" => "issuing_body", "label" => "Issuing Body", "type" => "text"]
                            ]
                        ],
                        ["name" => "file_2_4_4", "label" => "Supporting Document", "type" => "file", "accept" => ".pdf"]
                    ]
                ]
            ]
        ],
        [
            "title" => "2.5 Evaluation Process and Reforms",
            "metrics" => [
                [
                    "id" => "2.5.1",
                    "title" => "2.5.1: Result Declaration Days",
                    "description" => "Average number of days from the date of last semester-end/ year-end examination till the declaration of results.",
                    "fields" => [
                        ["name" => "qnm_2_5_1_days", "label" => "Average number of days", "type" => "number"],
                        [
                            "name" => "table_2_5_1",
                            "label" => "Data Template: Result Declaration",
                            "type" => "table",
                            "columns" => [
                                ["name" => "prog_name", "label" => "Programme Name", "type" => "text"],
                                ["name" => "prog_code", "label" => "Programme Code", "type" => "text"],
                                ["name" => "sem_year", "label" => "Semester/Year", "type" => "text"],
                                ["name" => "date_exam", "label" => "Date of last exam (DD/MM/YYYY)", "type" => "text"],
                                ["name" => "date_result", "label" => "Date of result (DD/MM/YYYY)", "type" => "text"]
                            ]
                        ],
                        ["name" => "file_2_5_1", "label" => "Supporting Document", "type" => "file", "accept" => ".pdf"]
                    ]
                ],
                [
                    "id" => "2.5.2",
                    "title" => "2.5.2: Student complaints/grievances",
                    "description" => "Average percentage of student complaints/grievances about evaluation against total number appeared in the examinations.",
                    "fields" => [
                        ["name" => "qnm_2_5_2_grievances", "label" => "Number of complaints/grievances (Numerator)", "type" => "number"],
                        ["name" => "qnm_2_5_2_appeared", "label" => "Total number appeared (Denominator)", "type" => "number"],
                        ["name" => "file_2_5_2", "label" => "Supporting Document", "type" => "file", "accept" => ".pdf"]
                    ]
                ],
                [
                    "id" => "2.5.3",
                    "title" => "2.5.3: IT integration in exams",
                    "description" => "IT integration and reforms in the examination procedures and processes.",
                    "fields" => [
                        ["name" => "qlm_2_5_3_text", "label" => "Description", "type" => "textarea"],
                        ["name" => "file_2_5_3", "label" => "Supporting Document", "type" => "file", "accept" => ".pdf"]
                    ]
                ]
            ]
        ],
        [
            "title" => "2.6 Student Performance and Learning Outcomes",
            "metrics" => [
                [
                    "id" => "2.6.1",
                    "title" => "2.6.1: Outcomes definition",
                    "description" => "Programme and course outcomes for all Programmes offered by the institution are stated.",
                    "fields" => [
                        ["name" => "qlm_2_6_1_text", "label" => "Description", "type" => "textarea"],
                        ["name" => "qlm_2_6_1_url", "label" => "Website URL link for Outcomes", "type" => "url"],
                        ["name" => "file_2_6_1", "label" => "Supporting Document", "type" => "file", "accept" => ".pdf"]
                    ]
                ],
                [
                    "id" => "2.6.2",
                    "title" => "2.6.2: Outcomes attainment",
                    "description" => "Attainment of POs and COs are evaluated.",
                    "fields" => [
                        ["name" => "qlm_2_6_2_text", "label" => "Description", "type" => "textarea"],
                        ["name" => "file_2_6_2", "label" => "Supporting Document", "type" => "file", "accept" => ".pdf"]
                    ]
                ],
                [
                    "id" => "2.6.3",
                    "title" => "2.6.3: Pass percentage",
                    "description" => "Pass percentage of Students (Data for the latest completed academic year).",
                    "fields" => [
                        ["name" => "qnm_2_6_3_passed", "label" => "Number of students passed in final year exam (Numerator)", "type" => "number"],
                        ["name" => "qnm_2_6_3_appeared", "label" => "Number of students appeared in final year exam (Denominator)", "type" => "number"],
                        [
                            "name" => "table_2_6_3",
                            "label" => "Data Template: Pass Percentage",
                            "type" => "table",
                            "columns" => [
                                ["name" => "prog_code", "label" => "Programme Code", "type" => "text"],
                                ["name" => "prog_name", "label" => "Programme Name", "type" => "text"],
                                ["name" => "appeared", "label" => "Appeared Count", "type" => "number"],
                                ["name" => "passed", "label" => "Passed Count", "type" => "number"],
                                ["name" => "pass_pct", "label" => "Pass Percentage", "type" => "number"]
                            ]
                        ],
                        ["name" => "file_2_6_3", "label" => "Supporting Document", "type" => "file", "accept" => ".pdf"]
                    ]
                ]
            ]
        ],
        [
            "title" => "2.7 Student Satisfaction Survey",
            "metrics" => [
                [
                    "id" => "2.7.1",
                    "title" => "2.7.1: Student Satisfaction Survey",
                    "description" => "Online student satisfaction survey regarding to teaching learning process.",
                    "fields" => [
                        [
                            "name" => "table_2_7_1",
                            "label" => "Data Template: Student Database for SSS",
                            "type" => "table",
                            "columns" => [
                                ["name" => "student_name", "label" => "Name of Student", "type" => "text"],
                                ["name" => "gender", "label" => "Gender", "type" => "text"],
                                ["name" => "category", "label" => "Category", "type" => "text"],
                                ["name" => "nationality", "label" => "Nationality", "type" => "text"],
                                ["name" => "email", "label" => "Email ID", "type" => "text"],
                                ["name" => "prog_name", "label" => "Programme Name", "type" => "text"],
                                ["name" => "enrol_id", "label" => "Enrolment ID", "type" => "text"],
                                ["name" => "mobile", "label" => "Mobile Number", "type" => "text"]
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ]
];

$json_str_c2 = json_encode($schema_c2);
$conn->query("UPDATE naac_form_schemas SET is_active = 0 WHERE criterion_number = 2");
$stmt2 = $conn->prepare("INSERT INTO naac_form_schemas (criterion_number, version_name, is_active, schema_json) VALUES (2, 'v4_university_with_tables', 1, ?)");
$stmt2->bind_param("s", $json_str_c2);
$stmt2->execute();
echo "Inserted Criterion 2 (University with Tables) Schema and set it as active.\n";
