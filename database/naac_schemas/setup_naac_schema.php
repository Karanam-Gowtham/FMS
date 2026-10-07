<?php
require_once __DIR__ . '/core/bootstrap.php';
global $conn;

// Create Schema Table
$sql = "
CREATE TABLE IF NOT EXISTS `naac_form_schemas` (
  `schema_id` int(11) NOT NULL AUTO_INCREMENT,
  `criterion_number` int(11) NOT NULL,
  `version_name` varchar(50) NOT NULL DEFAULT 'v1',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `schema_json` longtext NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`schema_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";
$conn->query($sql);

// Initial JSON Schema for Criterion 1
$schema_json = [
    "sections" => [
        [
            "title" => "1.1 Curricular Planning and Implementation",
            "metrics" => [
                [
                    "id" => "1.1.1",
                    "title" => "1.1.1: Effective curriculum planning and delivery",
                    "description" => "The Institution ensures effective curriculum planning and delivery through a well-planned and documented process including Academic calendar and conduct of continuous internal Assessment. Write description in maximum of 500 words.",
                    "fields" => [
                        [
                            "name" => "qnm_1_1_1_text",
                            "label" => "Description",
                            "type" => "textarea",
                            "placeholder" => "Describe the process of curriculum planning and delivery..."
                        ],
                        [
                            "name" => "file_1_1_1",
                            "label" => "Supporting Document (Academic Calendar, Delivery Plans, etc.)",
                            "type" => "file",
                            "accept" => ".pdf",
                            "description" => "Please upload a combined PDF."
                        ]
                    ]
                ]
            ]
        ],
        [
            "title" => "1.2 Academic Flexibility",
            "metrics" => [
                [
                    "id" => "1.2.1",
                    "title" => "1.2.1: Certificate/Value added courses",
                    "description" => "Number of Certificate/Value added courses offered and online courses of MOOCs, SWAYAM, NPTEL etc. (where the students of the institution have enrolled and successfully completed).",
                    "fields" => [
                        [
                            "name" => "qnm_1_2_1_count",
                            "label" => "Total Number of Courses",
                            "type" => "number",
                            "placeholder" => "Enter number of courses"
                        ],
                        [
                            "name" => "file_1_2_1",
                            "label" => "Institutional Data / List of Courses",
                            "type" => "file",
                            "accept" => ".pdf",
                            "description" => "Upload PDF containing course list and details."
                        ]
                    ]
                ],
                [
                    "id" => "1.2.2",
                    "title" => "1.2.2: Percentage of students enrolled in these courses",
                    "description" => "Percentage of students enrolled in Certificate/ Value added courses and also completed online courses of MOOCs, SWAYAM, NPTEL etc.",
                    "fields" => [
                        [
                            "name" => "qnm_1_2_2_enrolled",
                            "label" => "Total Students Enrolled",
                            "type" => "number",
                            "placeholder" => "Enrolled students"
                        ],
                        [
                            "name" => "qnm_1_2_2_total",
                            "label" => "Total Students in Institution",
                            "type" => "number",
                            "placeholder" => "Total students"
                        ],
                        [
                            "name" => "file_1_2_2",
                            "label" => "Attendance / Certificates Proof",
                            "type" => "file",
                            "accept" => ".pdf",
                            "description" => "Upload attendance sheets or sample certificates as a single PDF."
                        ]
                    ]
                ]
            ]
        ],
        [
            "title" => "1.3 Curriculum Enrichment",
            "metrics" => [
                [
                    "id" => "1.3.1",
                    "title" => "1.3.1: Integration of Crosscutting Issues",
                    "description" => "Institution integrates crosscutting issues relevant to Professional Ethics, Gender, Human Values, Environment and Sustainability into the Curriculum. Write description in maximum of 500 words.",
                    "fields" => [
                        [
                            "name" => "qnm_1_3_1_text",
                            "label" => "Description",
                            "type" => "textarea",
                            "placeholder" => "Describe how these issues are integrated..."
                        ],
                        [
                            "name" => "file_1_3_1",
                            "label" => "Supporting Document",
                            "type" => "file",
                            "accept" => ".pdf",
                            "description" => "Upload list of relevant courses and syllabus copy."
                        ]
                    ]
                ],
                [
                    "id" => "1.3.2",
                    "title" => "1.3.2: Project Work / Field Work / Internships",
                    "description" => "Percentage of students undertaking project work/field work/ internships.",
                    "fields" => [
                        [
                            "name" => "qnm_1_3_2_count",
                            "label" => "Number of students undertaking project/field work/internships",
                            "type" => "number",
                            "placeholder" => "Number of students"
                        ],
                        [
                            "name" => "file_1_3_2",
                            "label" => "List of Students & Completion Certificates",
                            "type" => "file",
                            "accept" => ".pdf",
                            "description" => "Upload relevant PDF proofs."
                        ]
                    ]
                ]
            ]
        ],
        [
            "title" => "1.4 Feedback System",
            "metrics" => [
                [
                    "id" => "1.4.1",
                    "title" => "1.4.1: Structured Feedback System",
                    "description" => "Institution obtains feedback on the academic performance and ambience of the institution from various stakeholders and action taken report is on the website.",
                    "fields" => [
                        [
                            "name" => "feedback_stakeholders",
                            "label" => "Feedback collected from (Check all that apply):",
                            "type" => "checkbox_group",
                            "options" => [
                                ["name" => "fb_students", "label" => "Students"],
                                ["name" => "fb_teachers", "label" => "Teachers"],
                                ["name" => "fb_employers", "label" => "Employers"],
                                ["name" => "fb_alumni", "label" => "Alumni"]
                            ]
                        ],
                        [
                            "name" => "qnm_1_4_1_url",
                            "label" => "Action Taken Report URL (on institutional website)",
                            "type" => "url",
                            "placeholder" => "https://..."
                        ],
                        [
                            "name" => "file_1_4_1",
                            "label" => "Feedback Analysis & Action Taken Report",
                            "type" => "file",
                            "accept" => ".pdf",
                            "description" => "Upload the official signed PDF."
                        ]
                    ]
                ]
            ]
        ]
    ]
];

$json_str = json_encode($schema_json);

// Check if criterion 1 schema already exists
$check = $conn->query("SELECT schema_id FROM naac_form_schemas WHERE criterion_number = 1 AND version_name = 'v1'");
if ($check->num_rows > 0) {
    $stmt = $conn->prepare("UPDATE naac_form_schemas SET schema_json = ? WHERE criterion_number = 1 AND version_name = 'v1'");
    $stmt->bind_param("s", $json_str);
    $stmt->execute();
    echo "Updated Criterion 1 Schema.\n";
} else {
    $stmt = $conn->prepare("INSERT INTO naac_form_schemas (criterion_number, version_name, is_active, schema_json) VALUES (1, 'v1', 1, ?)");
    $stmt->bind_param("s", $json_str);
    $stmt->execute();
    echo "Inserted Criterion 1 Schema.\n";
}
