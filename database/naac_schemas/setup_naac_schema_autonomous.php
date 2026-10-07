<?php
require_once __DIR__ . '/core/bootstrap.php';
global $conn;

$schema_json = [
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
                        ["name" => "qnm_1_1_2_revised", "label" => "Number of programmes where syllabus was revised", "type" => "number"],
                        ["name" => "qnm_1_1_2_total", "label" => "Total number of programmes offered", "type" => "number"],
                        ["name" => "file_1_1_2", "label" => "Minutes of relevant Academic Council/BOS meeting", "type" => "file", "accept" => ".pdf"]
                    ]
                ],
                [
                    "id" => "1.1.3",
                    "title" => "1.1.3: Courses focusing on employability/entrepreneurship/skill development",
                    "description" => "Average percentage of courses having focus on employability/entrepreneurship/skill development.",
                    "fields" => [
                        ["name" => "qnm_1_1_3_focused", "label" => "Number of courses focusing on employability/entrepreneurship/skill development", "type" => "number"],
                        ["name" => "qnm_1_1_3_total", "label" => "Total number of courses in all programmes", "type" => "number"],
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
                        ["name" => "file_1_3_2", "label" => "Document Upload", "type" => "file", "accept" => ".pdf"]
                    ]
                ],
                [
                    "id" => "1.3.3",
                    "title" => "1.3.3: Students enrolled in value-added courses",
                    "description" => "Average Percentage of students enrolled in the value-added courses (from 1.3.2).",
                    "fields" => [
                        ["name" => "qnm_1_3_3_enrolled", "label" => "Number of students enrolled", "type" => "number"],
                        ["name" => "qnm_1_3_3_total", "label" => "Total number of students", "type" => "number"],
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
                        ["name" => "file_1_4_1", "label" => "Document Upload (Action Taken Report)", "type" => "file", "accept" => ".pdf"]
                    ]
                ]
            ]
        ]
    ]
];

$json_str = json_encode($schema_json);

$check = $conn->query("SELECT schema_id FROM naac_form_schemas WHERE criterion_number = 1 AND version_name = 'v2_autonomous'");
if ($check->num_rows > 0) {
    $stmt = $conn->prepare("UPDATE naac_form_schemas SET schema_json = ? WHERE criterion_number = 1 AND version_name = 'v2_autonomous'");
    $stmt->bind_param("s", $json_str);
    $stmt->execute();
    echo "Updated Criterion 1 (Autonomous) Schema.\n";
} else {
    // Disable previous v1 schemas for Criterion 1 to make this the active one
    $conn->query("UPDATE naac_form_schemas SET is_active = 0 WHERE criterion_number = 1");
    
    $stmt = $conn->prepare("INSERT INTO naac_form_schemas (criterion_number, version_name, is_active, schema_json) VALUES (1, 'v2_autonomous', 1, ?)");
    $stmt->bind_param("s", $json_str);
    $stmt->execute();
    echo "Inserted Criterion 1 (Autonomous) Schema and set it as active.\n";
}
