<?php
require_once __DIR__ . '/core/bootstrap.php';
global $conn;

$schema_c4 = [
    "sections" => [
        [
            "title" => "4.1 Physical Facilities",
            "metrics" => [
                [
                    "id" => "4.1.1",
                    "title" => "4.1.1: Facilities for teaching-learning",
                    "description" => "The institution has adequate facilities for teaching- learning. viz., classrooms, laboratories, computing equipment, etc.",
                    "fields" => [
                        ["name" => "qlm_4_1_1_text", "label" => "Description", "type" => "textarea"],
                        ["name" => "file_4_1_1", "label" => "Supporting Document", "type" => "file", "accept" => ".pdf"]
                    ]
                ],
                [
                    "id" => "4.1.2",
                    "title" => "4.1.2: Facilities for sports and cultural activities",
                    "description" => "The institution has adequate facilities for cultural activities, yoga, games (indoor, outdoor) and sports.",
                    "fields" => [
                        ["name" => "qlm_4_1_2_text", "label" => "Description", "type" => "textarea"],
                        ["name" => "file_4_1_2", "label" => "Supporting Document", "type" => "file", "accept" => ".pdf"]
                    ]
                ],
                [
                    "id" => "4.1.3",
                    "title" => "4.1.3: General campus facilities",
                    "description" => "Availability of general campus facilities and overall ambience.",
                    "fields" => [
                        ["name" => "qlm_4_1_3_text", "label" => "Description", "type" => "textarea"],
                        ["name" => "file_4_1_3", "label" => "Supporting Document", "type" => "file", "accept" => ".pdf"]
                    ]
                ],
                [
                    "id" => "4.1.4",
                    "title" => "4.1.4: Expenditure for infrastructure augmentation",
                    "description" => "Average percentage of expenditure excluding salary, for infrastructure augmentation during the last five years.",
                    "fields" => [
                        ["name" => "qnm_4_1_4_aug", "label" => "Expenditure for infrastructure augmentation (Numerator) (Lakhs)", "type" => "number"],
                        ["name" => "qnm_4_1_4_total", "label" => "Total expenditure excluding salary (Denominator) (Lakhs)", "type" => "number"],
                        [
                            "name" => "table_4_1_4",
                            "label" => "Data Template: Infrastructure Expenditure",
                            "type" => "table",
                            "columns" => [
                                ["name" => "year", "label" => "Year", "type" => "number"],
                                ["name" => "budget_infra", "label" => "Budget allocated for infra (Lakhs)", "type" => "number"],
                                ["name" => "exp_infra", "label" => "Exp for infra (Lakhs)", "type" => "number"],
                                ["name" => "total_exp", "label" => "Total exp excluding salary (Lakhs)", "type" => "number"]
                            ]
                        ],
                        ["name" => "file_4_1_4", "label" => "Supporting Document", "type" => "file", "accept" => ".pdf"]
                    ]
                ]
            ]
        ],
        [
            "title" => "4.2 Library as a Learning Resource",
            "metrics" => [
                [
                    "id" => "4.2.1",
                    "title" => "4.2.1: Library automation (ILMS)",
                    "description" => "Library is automated using Integrated Library Management System (ILMS).",
                    "fields" => [
                        ["name" => "qlm_4_2_1_text", "label" => "Description", "type" => "textarea"],
                        ["name" => "file_4_2_1", "label" => "Supporting Document", "type" => "file", "accept" => ".pdf"]
                    ]
                ],
                [
                    "id" => "4.2.2",
                    "title" => "4.2.2: E-resources subscription",
                    "description" => "The institution has subscription for the following e-resources.",
                    "fields" => [
                        [
                            "name" => "eresources_list",
                            "label" => "Options (Check all that apply):",
                            "type" => "checkbox_group",
                            "options" => [
                                ["name" => "e_journals", "label" => "1. e-journals"],
                                ["name" => "e_shodh", "label" => "2. e-ShodhSindhu"],
                                ["name" => "e_shodhganga", "label" => "3. Shodhganga Membership"],
                                ["name" => "e_books", "label" => "4. e-books"],
                                ["name" => "e_databases", "label" => "5. Databases"],
                                ["name" => "e_remote", "label" => "6. Remote access to e-resources"]
                            ]
                        ],
                        ["name" => "file_4_2_2", "label" => "Supporting Document", "type" => "file", "accept" => ".pdf"]
                    ]
                ],
                [
                    "id" => "4.2.3",
                    "title" => "4.2.3: Expenditure on books and journals",
                    "description" => "Average annual expenditure for purchase of books/e-books and subscription to journals/e-journals during the last five years.",
                    "fields" => [
                        ["name" => "qnm_4_2_3_exp", "label" => "Annual expenditure on books and journals (Lakhs)", "type" => "number"],
                        [
                            "name" => "table_4_2_3",
                            "label" => "Data Template: Books & Journals Expenditure",
                            "type" => "table",
                            "columns" => [
                                ["name" => "year", "label" => "Year", "type" => "number"],
                                ["name" => "exp_books", "label" => "Exp on books/e-books (Lakhs)", "type" => "number"],
                                ["name" => "exp_journals", "label" => "Exp on journals/e-journals (Lakhs)", "type" => "number"]
                            ]
                        ],
                        ["name" => "file_4_2_3", "label" => "Supporting Document", "type" => "file", "accept" => ".pdf"]
                    ]
                ],
                [
                    "id" => "4.2.4",
                    "title" => "4.2.4: Library usage per day",
                    "description" => "Percentage per day usage of library by teachers and students.",
                    "fields" => [
                        ["name" => "qnm_4_2_4_usage", "label" => "Number of teachers and students using library per day (Numerator)", "type" => "number"],
                        ["name" => "qnm_4_2_4_total", "label" => "Total number of teachers and students (Denominator)", "type" => "number"],
                        ["name" => "file_4_2_4", "label" => "Supporting Document", "type" => "file", "accept" => ".pdf"]
                    ]
                ]
            ]
        ],
        [
            "title" => "4.3 IT Infrastructure",
            "metrics" => [
                [
                    "id" => "4.3.1",
                    "title" => "4.3.1: ICT-enabled classrooms",
                    "description" => "Percentage of classrooms and seminar halls with ICT- enabled facilities such as LCD, smart board, Wi-Fi/LAN.",
                    "fields" => [
                        ["name" => "qnm_4_3_1_ict", "label" => "Number of classrooms and seminar halls with ICT facilities (Numerator)", "type" => "number"],
                        ["name" => "qnm_4_3_1_total", "label" => "Total number of classrooms and seminar halls (Denominator)", "type" => "number"],
                        [
                            "name" => "table_4_3_1",
                            "label" => "Data Template: ICT Classrooms",
                            "type" => "table",
                            "columns" => [
                                ["name" => "room_no", "label" => "Room number or Name of classrooms/Seminar Hall", "type" => "text"],
                                ["name" => "facility_type", "label" => "Type of ICT facility", "type" => "text"]
                            ]
                        ],
                        ["name" => "file_4_3_1", "label" => "Supporting Document", "type" => "file", "accept" => ".pdf"]
                    ]
                ],
                [
                    "id" => "4.3.2",
                    "title" => "4.3.2: IT updates and Wi-Fi",
                    "description" => "Institution has an IT policy, makes appropriate budgetary provisions and updates its IT facilities including the Wi-Fi facility.",
                    "fields" => [
                        ["name" => "qlm_4_3_2_text", "label" => "Description", "type" => "textarea"],
                        ["name" => "file_4_3_2", "label" => "Supporting Document", "type" => "file", "accept" => ".pdf"]
                    ]
                ],
                [
                    "id" => "4.3.3",
                    "title" => "4.3.3: Student - Computer ratio",
                    "description" => "Student – Computer ratio.",
                    "fields" => [
                        ["name" => "qnm_4_3_3_students", "label" => "Total number of students (Numerator)", "type" => "number"],
                        ["name" => "qnm_4_3_3_computers", "label" => "Total number of computers in working condition (Denominator)", "type" => "number"],
                        ["name" => "file_4_3_3", "label" => "Supporting Document", "type" => "file", "accept" => ".pdf"]
                    ]
                ],
                [
                    "id" => "4.3.4",
                    "title" => "4.3.4: Internet Bandwidth",
                    "description" => "Available bandwidth of internet connection in the Institution.",
                    "fields" => [
                        [
                            "name" => "bandwidth_opts",
                            "label" => "Select available bandwidth (Check one):",
                            "type" => "checkbox_group",
                            "options" => [
                                ["name" => "bw_1gbps", "label" => "A. ≥ 1 GBPS"],
                                ["name" => "bw_500mbps", "label" => "B. 500 MBPS - 1 GBPS"],
                                ["name" => "bw_250mbps", "label" => "C. 250 MBPS - 500 MBPS"],
                                ["name" => "bw_50mbps", "label" => "D. 50 MBPS - 250 MBPS"],
                                ["name" => "bw_less50", "label" => "E. < 50 MBPS"]
                            ]
                        ],
                        ["name" => "file_4_3_4", "label" => "Supporting Document (Bills/Agreements)", "type" => "file", "accept" => ".pdf"]
                    ]
                ],
                [
                    "id" => "4.3.5",
                    "title" => "4.3.5: E-content development facilities",
                    "description" => "Institution has the following Facilities for e-content development.",
                    "fields" => [
                        [
                            "name" => "econtent_facs",
                            "label" => "Available facilities (Check all that apply):",
                            "type" => "checkbox_group",
                            "options" => [
                                ["name" => "ec_media", "label" => "1. Media centre"],
                                ["name" => "ec_audio", "label" => "2. Audio visual centre"],
                                ["name" => "ec_lecture", "label" => "3. Lecture Capturing System(LCS)"],
                                ["name" => "ec_mixing", "label" => "4. Mixing equipments and softwares for editing"]
                            ]
                        ],
                        ["name" => "file_4_3_5", "label" => "Supporting Document", "type" => "file", "accept" => ".pdf"]
                    ]
                ]
            ]
        ],
        [
            "title" => "4.4 Maintenance of Campus Infrastructure",
            "metrics" => [
                [
                    "id" => "4.4.1",
                    "title" => "4.4.1: Expenditure on maintenance",
                    "description" => "Average percentage of expenditure incurred on maintenance of physical facilities and academic support facilities excluding salary.",
                    "fields" => [
                        ["name" => "qnm_4_4_1_maint", "label" => "Expenditure on maintenance (Numerator) (Lakhs)", "type" => "number"],
                        ["name" => "qnm_4_4_1_total", "label" => "Total expenditure excluding salary (Denominator) (Lakhs)", "type" => "number"],
                        [
                            "name" => "table_4_4_1",
                            "label" => "Data Template: Maintenance Expenditure",
                            "type" => "table",
                            "columns" => [
                                ["name" => "year", "label" => "Year", "type" => "number"],
                                ["name" => "budget_acad", "label" => "Assigned budget on academic facilities (Lakhs)", "type" => "number"],
                                ["name" => "exp_acad", "label" => "Exp on academic facilities (Lakhs)", "type" => "number"],
                                ["name" => "budget_phys", "label" => "Assigned budget on physical facilities (Lakhs)", "type" => "number"],
                                ["name" => "exp_phys", "label" => "Exp on physical facilities (Lakhs)", "type" => "number"]
                            ]
                        ],
                        ["name" => "file_4_4_1", "label" => "Supporting Document", "type" => "file", "accept" => ".pdf"]
                    ]
                ],
                [
                    "id" => "4.4.2",
                    "title" => "4.4.2: Maintenance systems and procedures",
                    "description" => "There are established systems and procedures for maintaining and utilizing physical, academic and support facilities.",
                    "fields" => [
                        ["name" => "qlm_4_4_2_text", "label" => "Description", "type" => "textarea"],
                        ["name" => "qlm_4_4_2_url", "label" => "URL for policy document", "type" => "url"],
                        ["name" => "file_4_4_2", "label" => "Supporting Document", "type" => "file", "accept" => ".pdf"]
                    ]
                ]
            ]
        ]
    ]
];

$json_str_c4 = json_encode($schema_c4);
$conn->query("UPDATE naac_form_schemas SET is_active = 0 WHERE criterion_number = 4");
$stmt4 = $conn->prepare("INSERT INTO naac_form_schemas (criterion_number, version_name, is_active, schema_json) VALUES (4, 'v4_university_with_tables', 1, ?)");
$stmt4->bind_param("s", $json_str_c4);
$stmt4->execute();
echo "Inserted Corrected Criterion 4 (University with Tables) Schema and set it as active.\n";
