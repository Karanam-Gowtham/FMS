<?php
require_once __DIR__ . '/core/bootstrap.php';
global $conn;

$schema_c5 = [
    "sections" => [
        [
            "title" => "5.1 Student Support",
            "metrics" => [
                [
                    "id" => "5.1.1",
                    "title" => "5.1.1: Scholarships and Freeships",
                    "description" => "Average percentage of students benefited by scholarships and freeships provided by the institution, Government and non-government bodies.",
                    "fields" => [
                        ["name" => "qnm_5_1_1_benefited", "label" => "Number of students benefited (Numerator)", "type" => "number"],
                        ["name" => "qnm_5_1_1_total", "label" => "Total number of students (Denominator)", "type" => "number"],
                        [
                            "name" => "table_5_1_1",
                            "label" => "Data Template: Scholarships",
                            "type" => "table",
                            "columns" => [
                                ["name" => "year", "label" => "Year", "type" => "number"],
                                ["name" => "scheme_name", "label" => "Name of the scheme", "type" => "text"],
                                ["name" => "num_benefited", "label" => "Number of students benefiting", "type" => "number"]
                            ]
                        ],
                        ["name" => "file_5_1_1", "label" => "Supporting Document", "type" => "file", "accept" => ".pdf"]
                    ]
                ],
                [
                    "id" => "5.1.2",
                    "title" => "5.1.2: Career Counselling and Guidance",
                    "description" => "Average percentage of students benefited by career counselling and guidance for competitive examinations.",
                    "fields" => [
                        ["name" => "qnm_5_1_2_benefited", "label" => "Number of students benefited (Numerator)", "type" => "number"],
                        ["name" => "qnm_5_1_2_total", "label" => "Total number of students (Denominator)", "type" => "number"],
                        [
                            "name" => "table_5_1_2",
                            "label" => "Data Template: Career Counselling",
                            "type" => "table",
                            "columns" => [
                                ["name" => "year", "label" => "Year", "type" => "number"],
                                ["name" => "scheme_name", "label" => "Name of the scheme", "type" => "text"],
                                ["name" => "num_passed", "label" => "Students passed in competitive exams", "type" => "number"],
                                ["name" => "num_benefited", "label" => "Number of students benefited", "type" => "number"]
                            ]
                        ],
                        ["name" => "file_5_1_2", "label" => "Supporting Document", "type" => "file", "accept" => ".pdf"]
                    ]
                ],
                [
                    "id" => "5.1.3",
                    "title" => "5.1.3: Capacity Development and Skills Enhancement",
                    "description" => "Following Capacity development and skills enhancement initiatives are undertaken by the institution.",
                    "fields" => [
                        [
                            "name" => "skills_opts",
                            "label" => "Initiatives undertaken (Check all that apply):",
                            "type" => "checkbox_group",
                            "options" => [
                                ["name" => "skill_soft", "label" => "1. Soft skills"],
                                ["name" => "skill_lang", "label" => "2. Language and communication skills"],
                                ["name" => "skill_life", "label" => "3. Life skills (Yoga, physical fitness, health and hygiene)"],
                                ["name" => "skill_tech", "label" => "4. Awareness of trends in technology"]
                            ]
                        ],
                        [
                            "name" => "table_5_1_3",
                            "label" => "Data Template: Skill Enhancement",
                            "type" => "table",
                            "columns" => [
                                ["name" => "scheme_name", "label" => "Name of the scheme", "type" => "text"],
                                ["name" => "year_impl", "label" => "Year of implementation", "type" => "number"],
                                ["name" => "num_enrolled", "label" => "Number of students enrolled", "type" => "number"],
                                ["name" => "agencies", "label" => "Name of agencies involved", "type" => "text"]
                            ]
                        ],
                        ["name" => "file_5_1_3", "label" => "Supporting Document", "type" => "file", "accept" => ".pdf"]
                    ]
                ],
                [
                    "id" => "5.1.4",
                    "title" => "5.1.4: Student Grievances Redressal",
                    "description" => "The Institution adopts the following for redressal of student grievances including sexual harassment and ragging cases.",
                    "fields" => [
                        [
                            "name" => "grievance_opts",
                            "label" => "Options (Check all that apply):",
                            "type" => "checkbox_group",
                            "options" => [
                                ["name" => "gr_guide", "label" => "1. Implementation of guidelines of statutory/regulatory bodies"],
                                ["name" => "gr_aware", "label" => "2. Organisation-wide awareness and undertakings on policies"],
                                ["name" => "gr_mech", "label" => "3. Mechanisms for submission of online/offline grievances"],
                                ["name" => "gr_timely", "label" => "4. Timely redressal of grievances through appropriate committees"]
                            ]
                        ],
                        ["name" => "file_5_1_4", "label" => "Supporting Document", "type" => "file", "accept" => ".pdf"]
                    ]
                ]
            ]
        ],
        [
            "title" => "5.2 Student Progression",
            "metrics" => [
                [
                    "id" => "5.2.1",
                    "title" => "5.2.1: Qualifying in State/National/International Exams",
                    "description" => "Average percentage of students qualifying in state/ national/ international level examinations.",
                    "fields" => [
                        ["name" => "qnm_5_2_1_qual", "label" => "Number of students qualifying (Numerator)", "type" => "number"],
                        ["name" => "qnm_5_2_1_appear", "label" => "Number of students appeared (Denominator)", "type" => "number"],
                        [
                            "name" => "table_5_2_1",
                            "label" => "Data Template: Competitive Exams",
                            "type" => "table",
                            "columns" => [
                                ["name" => "year", "label" => "Year", "type" => "number"],
                                ["name" => "exam_name", "label" => "Exam (NET/SLET/GATE/etc)", "type" => "text"],
                                ["name" => "num_selected", "label" => "Number of students selected", "type" => "number"]
                            ]
                        ],
                        ["name" => "file_5_2_1", "label" => "Supporting Document", "type" => "file", "accept" => ".pdf"]
                    ]
                ],
                [
                    "id" => "5.2.2",
                    "title" => "5.2.2: Placement of Outgoing Students",
                    "description" => "Average percentage of placement of outgoing students during the last five years.",
                    "fields" => [
                        ["name" => "qnm_5_2_2_placed", "label" => "Number of outgoing students placed (Numerator)", "type" => "number"],
                        ["name" => "qnm_5_2_2_total", "label" => "Number of outgoing students (Denominator)", "type" => "number"],
                        [
                            "name" => "table_5_2_2",
                            "label" => "Data Template: Placement",
                            "type" => "table",
                            "columns" => [
                                ["name" => "year", "label" => "Year", "type" => "number"],
                                ["name" => "employer_name", "label" => "Name of the employer with contact", "type" => "text"],
                                ["name" => "num_placed", "label" => "Number of students placed", "type" => "number"]
                            ]
                        ],
                        ["name" => "file_5_2_2", "label" => "Supporting Document", "type" => "file", "accept" => ".pdf"]
                    ]
                ],
                [
                    "id" => "5.2.3",
                    "title" => "5.2.3: Progression to Higher Education",
                    "description" => "Percentage of recently-graduated students who have progressed to higher education.",
                    "fields" => [
                        ["name" => "qnm_5_2_3_prog", "label" => "Number of students progressing to higher education (Numerator)", "type" => "number"],
                        ["name" => "qnm_5_2_3_final", "label" => "Total number of final year students (Denominator)", "type" => "number"],
                        [
                            "name" => "table_5_2_3",
                            "label" => "Data Template: Higher Education",
                            "type" => "table",
                            "columns" => [
                                ["name" => "year", "label" => "Year", "type" => "number"],
                                ["name" => "student_name", "label" => "Name of student enrolling", "type" => "text"],
                                ["name" => "prog_graduated", "label" => "Program graduated from", "type" => "text"],
                                ["name" => "inst_joined", "label" => "Name of institution joined", "type" => "text"],
                                ["name" => "prog_admitted", "label" => "Name of program admitted to", "type" => "text"]
                            ]
                        ],
                        ["name" => "file_5_2_3", "label" => "Supporting Document", "type" => "file", "accept" => ".pdf"]
                    ]
                ]
            ]
        ],
        [
            "title" => "5.3 Student Participation and Activities",
            "metrics" => [
                [
                    "id" => "5.3.1",
                    "title" => "5.3.1: Awards/Medals for Sports/Cultural Activities",
                    "description" => "Number of awards/medals won by students for outstanding performance in sports/cultural activities.",
                    "fields" => [
                        ["name" => "qnm_5_3_1_awards", "label" => "Number of awards/medals", "type" => "number"],
                        [
                            "name" => "table_5_3_1",
                            "label" => "Data Template: Sports/Cultural Awards",
                            "type" => "table",
                            "columns" => [
                                ["name" => "year", "label" => "Year", "type" => "number"],
                                ["name" => "award_name", "label" => "Name of the award/medal", "type" => "text"],
                                ["name" => "level", "label" => "Level (Inter-university/State/National/Intl)", "type" => "text"],
                                ["name" => "event_name", "label" => "Name of the event", "type" => "text"]
                            ]
                        ],
                        ["name" => "file_5_3_1", "label" => "Supporting Document", "type" => "file", "accept" => ".pdf"]
                    ]
                ],
                [
                    "id" => "5.3.2",
                    "title" => "5.3.2: Student Council",
                    "description" => "Presence of Student Council and its activities for institutional development and student welfare.",
                    "fields" => [
                        ["name" => "qlm_5_3_2_text", "label" => "Description", "type" => "textarea"],
                        ["name" => "file_5_3_2", "label" => "Supporting Document", "type" => "file", "accept" => ".pdf"]
                    ]
                ],
                [
                    "id" => "5.3.3",
                    "title" => "5.3.3: Sports and Cultural Events",
                    "description" => "Average number of sports and cultural events / competitions organised by the institution per year.",
                    "fields" => [
                        ["name" => "qnm_5_3_3_events", "label" => "Number of events organized", "type" => "number"],
                        [
                            "name" => "table_5_3_3",
                            "label" => "Data Template: Events Organized",
                            "type" => "table",
                            "columns" => [
                                ["name" => "year", "label" => "Year", "type" => "number"],
                                ["name" => "event_name", "label" => "Name of the event / competition", "type" => "text"]
                            ]
                        ],
                        ["name" => "file_5_3_3", "label" => "Supporting Document", "type" => "file", "accept" => ".pdf"]
                    ]
                ]
            ]
        ],
        [
            "title" => "5.4 Alumni Engagement",
            "metrics" => [
                [
                    "id" => "5.4.1",
                    "title" => "5.4.1: Alumni Contribution (QlM)",
                    "description" => "The Alumni Association/Chapters (registered and functional) contributes significantly to the development of the institution.",
                    "fields" => [
                        ["name" => "qlm_5_4_1_text", "label" => "Description", "type" => "textarea"],
                        ["name" => "file_5_4_1", "label" => "Supporting Document", "type" => "file", "accept" => ".pdf"]
                    ]
                ],
                [
                    "id" => "5.4.2",
                    "title" => "5.4.2: Alumni contribution (INR in lakhs)",
                    "description" => "Alumni contribution during the last five years (INR in lakhs).",
                    "fields" => [
                        [
                            "name" => "alumni_contrib_opts",
                            "label" => "Select contribution amount (Check one):",
                            "type" => "checkbox_group",
                            "options" => [
                                ["name" => "ac_100", "label" => "A. ≥ 100 Lakhs"],
                                ["name" => "ac_50", "label" => "B. 50 Lakhs - 100 Lakhs"],
                                ["name" => "ac_20", "label" => "C. 20 Lakhs - 50 Lakhs"],
                                ["name" => "ac_5", "label" => "D. 5 Lakhs - 20 Lakhs"],
                                ["name" => "ac_less5", "label" => "E. < 5 Lakhs"]
                            ]
                        ],
                        ["name" => "file_5_4_2", "label" => "Supporting Document (Audited Statement)", "type" => "file", "accept" => ".pdf"]
                    ]
                ]
            ]
        ]
    ]
];

$json_str_c5 = json_encode($schema_c5);
$conn->query("UPDATE naac_form_schemas SET is_active = 0 WHERE criterion_number = 5");
$stmt5 = $conn->prepare("INSERT INTO naac_form_schemas (criterion_number, version_name, is_active, schema_json) VALUES (5, 'v5_university_with_tables', 1, ?)");
$stmt5->bind_param("s", $json_str_c5);
$stmt5->execute();
echo "Inserted Corrected Criterion 5 (University with Tables) Schema and set it as active.\n";
