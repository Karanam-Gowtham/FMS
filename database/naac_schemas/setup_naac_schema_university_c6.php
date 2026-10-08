<?php
require_once __DIR__ . '/core/bootstrap.php';
global $conn;

$schema_c6 = [
    "sections" => [
        [
            "title" => "6.1 Institutional Vision and Leadership",
            "metrics" => [
                [
                    "id" => "6.1.1",
                    "title" => "6.1.1: Vision and Mission",
                    "description" => "The institution has a clearly stated vision and mission which are reflected in its academic and administrative governance.",
                    "fields" => [
                        ["name" => "qlm_6_1_1_text", "label" => "Description", "type" => "textarea"],
                        ["name" => "file_6_1_1", "label" => "Supporting Document", "type" => "file", "accept" => ".pdf"]
                    ]
                ],
                [
                    "id" => "6.1.2",
                    "title" => "6.1.2: Effective Leadership",
                    "description" => "Effective leadership is reflected in various institutional practices such as decentralization and participative management.",
                    "fields" => [
                        ["name" => "qlm_6_1_2_text", "label" => "Description", "type" => "textarea"],
                        ["name" => "file_6_1_2", "label" => "Supporting Document", "type" => "file", "accept" => ".pdf"]
                    ]
                ]
            ]
        ],
        [
            "title" => "6.2 Strategy Development and Deployment",
            "metrics" => [
                [
                    "id" => "6.2.1",
                    "title" => "6.2.1: Strategic Plan Deployment",
                    "description" => "The institutional Strategic plan is effectively deployed.",
                    "fields" => [
                        ["name" => "qlm_6_2_1_text", "label" => "Description", "type" => "textarea"],
                        ["name" => "file_6_2_1", "label" => "Supporting Document", "type" => "file", "accept" => ".pdf"]
                    ]
                ],
                [
                    "id" => "6.2.2",
                    "title" => "6.2.2: Functioning of Institutional Bodies",
                    "description" => "Functioning of the institutional bodies is effective and efficient as visible from policies, administrative setup, appointment, service rules, and procedures, etc.",
                    "fields" => [
                        ["name" => "qlm_6_2_2_text", "label" => "Description", "type" => "textarea"],
                        ["name" => "file_6_2_2", "label" => "Supporting Document", "type" => "file", "accept" => ".pdf"]
                    ]
                ],
                [
                    "id" => "6.2.3",
                    "title" => "6.2.3: Implementation of e-governance",
                    "description" => "Implementation of e-governance in areas of operation.",
                    "fields" => [
                        [
                            "name" => "egov_opts",
                            "label" => "Areas of e-governance (Check all that apply):",
                            "type" => "checkbox_group",
                            "options" => [
                                ["name" => "egov_admin", "label" => "1. Administration"],
                                ["name" => "egov_finance", "label" => "2. Finance and Accounts"],
                                ["name" => "egov_student", "label" => "3. Student Admission and Support"],
                                ["name" => "egov_exam", "label" => "4. Examination"]
                            ]
                        ],
                        [
                            "name" => "table_6_2_3",
                            "label" => "Data Template: e-Governance",
                            "type" => "table",
                            "columns" => [
                                ["name" => "area", "label" => "Areas of e-governance", "type" => "text"],
                                ["name" => "vendor", "label" => "Name of the Vendor with contact details", "type" => "text"],
                                ["name" => "year_impl", "label" => "Year of implementation", "type" => "number"]
                            ]
                        ],
                        ["name" => "file_6_2_3", "label" => "Supporting Document (ERP Document/Screenshots)", "type" => "file", "accept" => ".pdf"]
                    ]
                ]
            ]
        ],
        [
            "title" => "6.3 Faculty Empowerment Strategies",
            "metrics" => [
                [
                    "id" => "6.3.1",
                    "title" => "6.3.1: Welfare Measures",
                    "description" => "The institution has effective welfare measures for teaching and non-teaching staff.",
                    "fields" => [
                        ["name" => "qlm_6_3_1_text", "label" => "Description", "type" => "textarea"],
                        ["name" => "file_6_3_1", "label" => "Supporting Document", "type" => "file", "accept" => ".pdf"]
                    ]
                ],
                [
                    "id" => "6.3.2",
                    "title" => "6.3.2: Financial Support for Conferences",
                    "description" => "Average percentage of teachers provided with financial support to attend conferences/workshops and towards membership fee of professional bodies.",
                    "fields" => [
                        ["name" => "qnm_6_3_2_supported", "label" => "Number of teachers provided with financial support (Numerator)", "type" => "number"],
                        ["name" => "qnm_6_3_2_total", "label" => "Total number of teachers (Denominator)", "type" => "number"],
                        [
                            "name" => "table_6_3_2",
                            "label" => "Data Template: Financial Support",
                            "type" => "table",
                            "columns" => [
                                ["name" => "year", "label" => "Year", "type" => "number"],
                                ["name" => "teacher_name", "label" => "Name of teacher", "type" => "text"],
                                ["name" => "conf_name", "label" => "Name of conference/workshop", "type" => "text"],
                                ["name" => "amount", "label" => "Amount of support (INR)", "type" => "number"]
                            ]
                        ],
                        ["name" => "file_6_3_2", "label" => "Supporting Document", "type" => "file", "accept" => ".pdf"]
                    ]
                ],
                [
                    "id" => "6.3.3",
                    "title" => "6.3.3: Professional Development Programs",
                    "description" => "Average number of professional development / administrative training programs organized by the institution for teaching and non-teaching staff.",
                    "fields" => [
                        ["name" => "qnm_6_3_3_progs", "label" => "Total number of programs organized", "type" => "number"],
                        [
                            "name" => "table_6_3_3",
                            "label" => "Data Template: Development Programs",
                            "type" => "table",
                            "columns" => [
                                ["name" => "year", "label" => "Year", "type" => "number"],
                                ["name" => "prog_title", "label" => "Title of the professional development program", "type" => "text"],
                                ["name" => "participants", "label" => "No. of participants (Teaching/Non-Teaching)", "type" => "number"]
                            ]
                        ],
                        ["name" => "file_6_3_3", "label" => "Supporting Document", "type" => "file", "accept" => ".pdf"]
                    ]
                ],
                [
                    "id" => "6.3.4",
                    "title" => "6.3.4: Faculty Development Programmes (FDP)",
                    "description" => "Average percentage of teachers undergoing online/face-to-face Faculty Development Programmes (FDP).",
                    "fields" => [
                        ["name" => "qnm_6_3_4_fdp", "label" => "Number of teachers undergoing FDP (Numerator)", "type" => "number"],
                        ["name" => "qnm_6_3_4_total", "label" => "Total number of teachers (Denominator)", "type" => "number"],
                        [
                            "name" => "table_6_3_4",
                            "label" => "Data Template: FDP Details",
                            "type" => "table",
                            "columns" => [
                                ["name" => "year", "label" => "Year", "type" => "number"],
                                ["name" => "teacher_name", "label" => "Name of teacher who attended", "type" => "text"],
                                ["name" => "prog_title", "label" => "Title of the program", "type" => "text"],
                                ["name" => "duration", "label" => "Duration (from - to)", "type" => "text"]
                            ]
                        ],
                        ["name" => "file_6_3_4", "label" => "Supporting Document", "type" => "file", "accept" => ".pdf"]
                    ]
                ]
            ]
        ],
        [
            "title" => "6.4 Financial Management and Resource Mobilization",
            "metrics" => [
                [
                    "id" => "6.4.1",
                    "title" => "6.4.1: Internal and External Audits",
                    "description" => "Institution conducts internal and external financial audits regularly.",
                    "fields" => [
                        ["name" => "qlm_6_4_1_text", "label" => "Description", "type" => "textarea"],
                        ["name" => "file_6_4_1", "label" => "Supporting Document", "type" => "file", "accept" => ".pdf"]
                    ]
                ],
                [
                    "id" => "6.4.2",
                    "title" => "6.4.2: Funds / Grants from Government Bodies",
                    "description" => "Funds / Grants received from government bodies during the last five years for development and maintenance of infrastructure.",
                    "fields" => [
                        ["name" => "qnm_6_4_2_grants", "label" => "Total Grants received from government bodies (INR in Lakhs)", "type" => "number"],
                        [
                            "name" => "table_6_4_2",
                            "label" => "Data Template: Govt Grants",
                            "type" => "table",
                            "columns" => [
                                ["name" => "year", "label" => "Year", "type" => "number"],
                                ["name" => "grant_name", "label" => "Name of the government funding agencies", "type" => "text"],
                                ["name" => "funds", "label" => "Funds/ Grants received (INR in Lakhs)", "type" => "number"]
                            ]
                        ],
                        ["name" => "file_6_4_2", "label" => "Supporting Document", "type" => "file", "accept" => ".pdf"]
                    ]
                ],
                [
                    "id" => "6.4.3",
                    "title" => "6.4.3: Funds / Grants from Non-Government Bodies",
                    "description" => "Funds / Grants received from non-government bodies, individuals, philanthropists during the last five years.",
                    "fields" => [
                        ["name" => "qnm_6_4_3_grants", "label" => "Total Grants received from non-government bodies (INR in Lakhs)", "type" => "number"],
                        [
                            "name" => "table_6_4_3",
                            "label" => "Data Template: Non-Govt Grants",
                            "type" => "table",
                            "columns" => [
                                ["name" => "year", "label" => "Year", "type" => "number"],
                                ["name" => "grant_name", "label" => "Name of the non-government funding agencies/individuals", "type" => "text"],
                                ["name" => "funds", "label" => "Funds/ Grants received (INR in Lakhs)", "type" => "number"]
                            ]
                        ],
                        ["name" => "file_6_4_3", "label" => "Supporting Document", "type" => "file", "accept" => ".pdf"]
                    ]
                ],
                [
                    "id" => "6.4.4",
                    "title" => "6.4.4: Institutional strategies for mobilisation of funds",
                    "description" => "Institutional strategies for mobilisation of funds and the optimal utilisation of resources.",
                    "fields" => [
                        ["name" => "qlm_6_4_4_text", "label" => "Description", "type" => "textarea"],
                        ["name" => "file_6_4_4", "label" => "Supporting Document", "type" => "file", "accept" => ".pdf"]
                    ]
                ]
            ]
        ],
        [
            "title" => "6.5 Internal Quality Assurance System",
            "metrics" => [
                [
                    "id" => "6.5.1",
                    "title" => "6.5.1: IQAC Contribution",
                    "description" => "Internal Quality Assurance Cell (IQAC) has contributed significantly for institutionalizing the quality assurance strategies and processes.",
                    "fields" => [
                        ["name" => "qlm_6_5_1_text", "label" => "Description", "type" => "textarea"],
                        ["name" => "file_6_5_1", "label" => "Supporting Document", "type" => "file", "accept" => ".pdf"]
                    ]
                ],
                [
                    "id" => "6.5.2",
                    "title" => "6.5.2: Quality Assurance Initiatives",
                    "description" => "Quality assurance initiatives of the institution.",
                    "fields" => [
                        [
                            "name" => "qa_opts",
                            "label" => "Initiatives undertaken (Check all that apply):",
                            "type" => "checkbox_group",
                            "options" => [
                                ["name" => "qa_meeting", "label" => "1. Regular meeting of Internal Quality Assurance Cell (IQAC)"],
                                ["name" => "qa_feedback", "label" => "2. Feedback collected, analyzed and used for improvements"],
                                ["name" => "qa_collab", "label" => "3. Collaborative quality initiatives with other institution(s)"],
                                ["name" => "qa_audit", "label" => "4. Academic Administrative Audit (AAA) and follow-up action"],
                                ["name" => "qa_cert", "label" => "5. ISO / NBA certification or other quality audits"]
                            ]
                        ],
                        [
                            "name" => "table_6_5_2",
                            "label" => "Data Template: Quality Initiatives",
                            "type" => "table",
                            "columns" => [
                                ["name" => "year", "label" => "Year", "type" => "number"],
                                ["name" => "initiative_name", "label" => "Name of quality initiative by IQAC", "type" => "text"],
                                ["name" => "duration", "label" => "Duration (from - to)", "type" => "text"],
                                ["name" => "participants", "label" => "Number of participants", "type" => "number"]
                            ]
                        ],
                        ["name" => "file_6_5_2", "label" => "Supporting Document", "type" => "file", "accept" => ".pdf"]
                    ]
                ],
                [
                    "id" => "6.5.3",
                    "title" => "6.5.3: Incremental Improvements",
                    "description" => "Incremental improvements made for the preceding five years with regard to quality.",
                    "fields" => [
                        ["name" => "qlm_6_5_3_text", "label" => "Description", "type" => "textarea"],
                        ["name" => "file_6_5_3", "label" => "Supporting Document", "type" => "file", "accept" => ".pdf"]
                    ]
                ]
            ]
        ]
    ]
];

$json_str_c6 = json_encode($schema_c6);
$conn->query("UPDATE naac_form_schemas SET is_active = 0 WHERE criterion_number = 6");
$stmt6 = $conn->prepare("INSERT INTO naac_form_schemas (criterion_number, version_name, is_active, schema_json) VALUES (6, 'v1_university_with_tables', 1, ?)");
$stmt6->bind_param("s", $json_str_c6);
$stmt6->execute();
echo "Inserted Criterion 6 (University with Tables) Schema and set it as active.\n";
