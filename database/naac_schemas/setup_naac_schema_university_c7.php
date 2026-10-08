<?php
require_once __DIR__ . '/core/bootstrap.php';
global $conn;

$schema_c7 = [
    "sections" => [
        [
            "title" => "7.1 Institutional Values and Social Responsibilities",
            "metrics" => [
                [
                    "id" => "7.1.1",
                    "title" => "7.1.1: Promotion of Gender Equity",
                    "description" => "Measures initiated by the Institution for the promotion of gender equity during the last five years.",
                    "fields" => [
                        ["name" => "qlm_7_1_1_text", "label" => "Description", "type" => "textarea"],
                        ["name" => "file_7_1_1", "label" => "Supporting Document", "type" => "file", "accept" => ".pdf"]
                    ]
                ],
                [
                    "id" => "7.1.2",
                    "title" => "7.1.2: Alternate Sources of Energy",
                    "description" => "The Institution has facilities for alternate sources of energy and energy conservation measures.",
                    "fields" => [
                        [
                            "name" => "energy_opts",
                            "label" => "Facilities available (Check all that apply):",
                            "type" => "checkbox_group",
                            "options" => [
                                ["name" => "en_solar", "label" => "1. Solar energy"],
                                ["name" => "en_biogas", "label" => "2. Biogas plant"],
                                ["name" => "en_grid", "label" => "3. Wheeling to the Grid"],
                                ["name" => "en_sensor", "label" => "4. Sensor-based energy conservation"],
                                ["name" => "en_led", "label" => "5. Use of LED bulbs/ power efficient equipment"]
                            ]
                        ],
                        ["name" => "file_7_1_2", "label" => "Supporting Document (Geotagged Photographs)", "type" => "file", "accept" => ".pdf"]
                    ]
                ],
                [
                    "id" => "7.1.3",
                    "title" => "7.1.3: Waste Management Facilities",
                    "description" => "Describe the facilities in the Institution for the management of the degradable and non-degradable waste.",
                    "fields" => [
                        ["name" => "qlm_7_1_3_text", "label" => "Description", "type" => "textarea"],
                        ["name" => "file_7_1_3", "label" => "Supporting Document (Geotagged Photographs)", "type" => "file", "accept" => ".pdf"]
                    ]
                ],
                [
                    "id" => "7.1.4",
                    "title" => "7.1.4: Water Conservation Facilities",
                    "description" => "Water conservation facilities available in the Institution.",
                    "fields" => [
                        [
                            "name" => "water_opts",
                            "label" => "Facilities available (Check all that apply):",
                            "type" => "checkbox_group",
                            "options" => [
                                ["name" => "wa_rain", "label" => "1. Rain water harvesting"],
                                ["name" => "wa_bore", "label" => "2. Borewell /Open well recharge"],
                                ["name" => "wa_tank", "label" => "3. Construction of tanks and bunds"],
                                ["name" => "wa_waste", "label" => "4. Waste water recycling"],
                                ["name" => "wa_maint", "label" => "5. Maintenance of water bodies and distribution system in the campus"]
                            ]
                        ],
                        ["name" => "file_7_1_4", "label" => "Supporting Document (Geotagged Photographs)", "type" => "file", "accept" => ".pdf"]
                    ]
                ],
                [
                    "id" => "7.1.5",
                    "title" => "7.1.5: Green Campus Initiatives",
                    "description" => "Green campus initiatives include restricted entry of automobiles, use of bicycles/battery powered vehicles, pedestrian friendly pathways, ban on use of plastic, landscaping.",
                    "fields" => [
                        [
                            "name" => "green_opts",
                            "label" => "Initiatives available (Check all that apply):",
                            "type" => "checkbox_group",
                            "options" => [
                                ["name" => "gc_auto", "label" => "1. Restricted entry of automobiles"],
                                ["name" => "gc_battery", "label" => "2. Use of Bicycles/ Battery powered vehicles"],
                                ["name" => "gc_path", "label" => "3. Pedestrian Friendly pathways"],
                                ["name" => "gc_plastic", "label" => "4. Ban on use of Plastic"],
                                ["name" => "gc_landscape", "label" => "5. landscaping with trees and plants"]
                            ]
                        ],
                        ["name" => "file_7_1_5", "label" => "Supporting Document (Geotagged Photographs/Circulars)", "type" => "file", "accept" => ".pdf"]
                    ]
                ],
                [
                    "id" => "7.1.6",
                    "title" => "7.1.6: Environment and Energy Audits",
                    "description" => "Quality audits on environment and energy are regularly undertaken by the institution.",
                    "fields" => [
                        [
                            "name" => "audit_opts",
                            "label" => "Audits undertaken (Check all that apply):",
                            "type" => "checkbox_group",
                            "options" => [
                                ["name" => "au_green", "label" => "1. Green audit"],
                                ["name" => "au_energy", "label" => "2. Energy audit"],
                                ["name" => "au_env", "label" => "3. Environment audit"],
                                ["name" => "au_clean", "label" => "4. Clean and green campus recognitions/awards"],
                                ["name" => "au_beyond", "label" => "5. Beyond the campus environmental promotional activities"]
                            ]
                        ],
                        ["name" => "file_7_1_6", "label" => "Supporting Document (Audit reports/Certificates)", "type" => "file", "accept" => ".pdf"]
                    ]
                ],
                [
                    "id" => "7.1.7",
                    "title" => "7.1.7: Disabled-friendly Environment",
                    "description" => "The Institution has disabled-friendly, barrier free environment.",
                    "fields" => [
                        [
                            "name" => "disable_opts",
                            "label" => "Facilities available (Check all that apply):",
                            "type" => "checkbox_group",
                            "options" => [
                                ["name" => "df_built", "label" => "1. Built environment with ramps/lifts for easy access to classrooms"],
                                ["name" => "df_wash", "label" => "2. Disabled-friendly washrooms"],
                                ["name" => "df_sign", "label" => "3. Signage including tactile path, lights, display boards and signposts"],
                                ["name" => "df_assist", "label" => "4. Assistive technology and facilities for Divyangjan accessible website, screen-reading software, mechanized equipment"],
                                ["name" => "df_enquiry", "label" => "5. Provision for enquiry and information: Human assistance, reader, scribe, soft copies of reading material, screen reading"]
                            ]
                        ],
                        ["name" => "file_7_1_7", "label" => "Supporting Document (Geotagged Photographs)", "type" => "file", "accept" => ".pdf"]
                    ]
                ],
                [
                    "id" => "7.1.8",
                    "title" => "7.1.8: Inclusive Environment",
                    "description" => "Describe the Institutional efforts/initiatives in providing an inclusive environment i.e., tolerance and harmony towards cultural, regional, linguistic, communal socioeconomic and other diversities.",
                    "fields" => [
                        ["name" => "qlm_7_1_8_text", "label" => "Description", "type" => "textarea"],
                        ["name" => "file_7_1_8", "label" => "Supporting Document", "type" => "file", "accept" => ".pdf"]
                    ]
                ],
                [
                    "id" => "7.1.9",
                    "title" => "7.1.9: Constitutional Obligations",
                    "description" => "Sensitization of students and employees of the Institution to the constitutional obligations: values, rights, duties and responsibilities of citizens.",
                    "fields" => [
                        ["name" => "qlm_7_1_9_text", "label" => "Description", "type" => "textarea"],
                        ["name" => "file_7_1_9", "label" => "Supporting Document", "type" => "file", "accept" => ".pdf"]
                    ]
                ],
                [
                    "id" => "7.1.10",
                    "title" => "7.1.10: Code of Conduct",
                    "description" => "The Institution has a prescribed code of conduct for students, teachers, administrators and other staff and conducts periodic programmes in this regard.",
                    "fields" => [
                        [
                            "name" => "conduct_opts",
                            "label" => "Initiatives (Check all that apply):",
                            "type" => "checkbox_group",
                            "options" => [
                                ["name" => "cc_display", "label" => "1. The Code of Conduct is displayed on the website"],
                                ["name" => "cc_comm", "label" => "2. There is a committee to monitor adherence to the Code of Conduct"],
                                ["name" => "cc_prog", "label" => "3. Institution organizes professional ethics programmes for students, teachers, administrators and other staff"],
                                ["name" => "cc_aware", "label" => "4. Annual awareness programmes on Code of Conduct are organized"]
                            ]
                        ],
                        ["name" => "file_7_1_10", "label" => "Supporting Document", "type" => "file", "accept" => ".pdf"]
                    ]
                ],
                [
                    "id" => "7.1.11",
                    "title" => "7.1.11: Commemorative Days and Events",
                    "description" => "Institution celebrates / organizes national and international commemorative days, events and festivals.",
                    "fields" => [
                        ["name" => "qlm_7_1_11_text", "label" => "Description", "type" => "textarea"],
                        ["name" => "file_7_1_11", "label" => "Supporting Document", "type" => "file", "accept" => ".pdf"]
                    ]
                ]
            ]
        ],
        [
            "title" => "7.2 Best Practices",
            "metrics" => [
                [
                    "id" => "7.2.1",
                    "title" => "7.2.1: Institutional Best Practices",
                    "description" => "Describe two best practices successfully implemented by the Institution as per NAAC format provided in the Manual.",
                    "fields" => [
                        ["name" => "qlm_7_2_1_text", "label" => "Description of Best Practices", "type" => "textarea"],
                        ["name" => "file_7_2_1", "label" => "Supporting Document / URL", "type" => "file", "accept" => ".pdf"]
                    ]
                ]
            ]
        ],
        [
            "title" => "7.3 Institutional Distinctiveness",
            "metrics" => [
                [
                    "id" => "7.3.1",
                    "title" => "7.3.1: Institutional Distinctiveness",
                    "description" => "Portray the performance of the Institution in one area distinctive to its priority and thrust within 1000 words.",
                    "fields" => [
                        ["name" => "qlm_7_3_1_text", "label" => "Description", "type" => "textarea"],
                        ["name" => "file_7_3_1", "label" => "Supporting Document / URL", "type" => "file", "accept" => ".pdf"]
                    ]
                ]
            ]
        ]
    ]
];

$json_str_c7 = json_encode($schema_c7);
$conn->query("UPDATE naac_form_schemas SET is_active = 0 WHERE criterion_number = 7");
$stmt7 = $conn->prepare("INSERT INTO naac_form_schemas (criterion_number, version_name, is_active, schema_json) VALUES (7, 'v1_university_standard', 1, ?)");
$stmt7->bind_param("s", $json_str_c7);
$stmt7->execute();
echo "Inserted Criterion 7 (University Standard) Schema and set it as active.\n";
