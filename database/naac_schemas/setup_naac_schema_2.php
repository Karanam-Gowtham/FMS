<?php
require_once __DIR__ . '/core/bootstrap.php';
global $conn;

$schema_json = [
    "sections" => [
        [
            "title" => "2.1 Student Enrolment and Profile",
            "metrics" => [
                [
                    "id" => "2.1.1",
                    "title" => "2.1.1: Enrolment Percentage",
                    "description" => "Average enrolment percentage (Students admitted vs sanctioned seats).",
                    "fields" => [
                        ["name" => "qnm_2_1_1_admitted", "label" => "Number of students admitted", "type" => "number"],
                        ["name" => "qnm_2_1_1_sanctioned", "label" => "Number of sanctioned seats", "type" => "number"],
                        ["name" => "file_2_1_1", "label" => "Document Upload", "type" => "file", "accept" => ".pdf"]
                    ]
                ],
                [
                    "id" => "2.1.2",
                    "title" => "2.1.2: Reserved Categories",
                    "description" => "Average percentage of seats filled against reserved categories (SC, ST, OBC, Divyangjan, etc.).",
                    "fields" => [
                        ["name" => "qnm_2_1_2_filled", "label" => "Seats filled against reserved categories", "type" => "number"],
                        ["name" => "qnm_2_1_2_reserved", "label" => "Total seats earmarked for reserved categories", "type" => "number"],
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
                    "description" => "The institution assesses the learning levels of the students and organizes special Programmes for advanced learners and slow learners. Write description in max 500 words.",
                    "fields" => [
                        ["name" => "qlm_2_2_1_text", "label" => "Description", "type" => "textarea"],
                        ["name" => "file_2_2_1", "label" => "Supporting Document", "type" => "file", "accept" => ".pdf"]
                    ]
                ],
                [
                    "id" => "2.2.2",
                    "title" => "2.2.2: Student-Teacher Ratio",
                    "description" => "Student - Full time teacher ratio (Data for the latest completed academic year).",
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
                    "description" => "Student centric methods, such as experiential learning, participative learning and problem solving methodologies are used for enhancing learning experiences.",
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
                    "description" => "Ratio of mentor to students for academic and other related issues.",
                    "fields" => [
                        ["name" => "qnm_2_3_3_mentors", "label" => "Number of Mentors", "type" => "number"],
                        ["name" => "qnm_2_3_3_students", "label" => "Number of Students assigned to mentors", "type" => "number"],
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
                        ["name" => "qnm_2_4_1_teachers", "label" => "Number of Full-Time Teachers", "type" => "number"],
                        ["name" => "qnm_2_4_1_sanctioned", "label" => "Number of Sanctioned Posts", "type" => "number"],
                        ["name" => "file_2_4_1", "label" => "Supporting Document", "type" => "file", "accept" => ".pdf"]
                    ]
                ],
                [
                    "id" => "2.4.2",
                    "title" => "2.4.2: Teachers with Ph.D.",
                    "description" => "Average percentage of full time teachers with Ph. D. / D.M. / M.Ch. / D.N.B Superspeciality / D.Sc. / D.Litt.",
                    "fields" => [
                        ["name" => "qnm_2_4_2_phd", "label" => "Number of teachers with Ph.D. etc.", "type" => "number"],
                        ["name" => "file_2_4_2", "label" => "Supporting Document", "type" => "file", "accept" => ".pdf"]
                    ]
                ]
            ]
        ],
        [
            "title" => "2.5 Evaluation Process and Reforms",
            "metrics" => [
                [
                    "id" => "2.5.1",
                    "title" => "2.5.1: CIE Reforms",
                    "description" => "Mechanism of internal assessment is transparent and robust in terms of frequency and mode.",
                    "fields" => [
                        ["name" => "qlm_2_5_1_text", "label" => "Description", "type" => "textarea"],
                        ["name" => "file_2_5_1", "label" => "Supporting Document", "type" => "file", "accept" => ".pdf"]
                    ]
                ]
            ]
        ],
        [
            "title" => "2.6 Student Performance and Learning Outcomes",
            "metrics" => [
                [
                    "id" => "2.6.1",
                    "title" => "2.6.1: Outcomes definition and publication",
                    "description" => "Programme and course outcomes for all Programmes offered by the institution are stated and displayed on website and communicated to teachers and students.",
                    "fields" => [
                        ["name" => "qlm_2_6_1_text", "label" => "Description", "type" => "textarea"],
                        ["name" => "qlm_2_6_1_url", "label" => "Website URL link for Outcomes", "type" => "url"],
                        ["name" => "file_2_6_1", "label" => "Supporting Document", "type" => "file", "accept" => ".pdf"]
                    ]
                ],
                [
                    "id" => "2.6.3",
                    "title" => "2.6.3: Pass percentage",
                    "description" => "Pass percentage of Students during last five years.",
                    "fields" => [
                        ["name" => "qnm_2_6_3_passed", "label" => "Number of students passed in final year exam", "type" => "number"],
                        ["name" => "qnm_2_6_3_appeared", "label" => "Number of students appeared in final year exam", "type" => "number"],
                        ["name" => "file_2_6_3", "label" => "Supporting Document", "type" => "file", "accept" => ".pdf"]
                    ]
                ]
            ]
        ]
    ]
];

$json_str = json_encode($schema_json);

$check = $conn->query("SELECT schema_id FROM naac_form_schemas WHERE criterion_number = 2 AND version_name = 'v1'");
if ($check->num_rows > 0) {
    $stmt = $conn->prepare("UPDATE naac_form_schemas SET schema_json = ? WHERE criterion_number = 2 AND version_name = 'v1'");
    $stmt->bind_param("s", $json_str);
    $stmt->execute();
    echo "Updated Criterion 2 Schema.\n";
} else {
    $stmt = $conn->prepare("INSERT INTO naac_form_schemas (criterion_number, version_name, is_active, schema_json) VALUES (2, 'v1', 1, ?)");
    $stmt->bind_param("s", $json_str);
    $stmt->execute();
    echo "Inserted Criterion 2 Schema.\n";
}
