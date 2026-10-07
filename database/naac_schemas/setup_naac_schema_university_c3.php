<?php
require_once __DIR__ . '/core/bootstrap.php';
global $conn;

$schema_c3 = [
    "sections" => [
        [
            "title" => "3.1 Promotion of Research and Facilities",
            "metrics" => [
                [
                    "id" => "3.1.1",
                    "title" => "3.1.1: Research facilities",
                    "description" => "The institution Research facilities are frequently updated and there is well defined policy for promotion of research.",
                    "fields" => [
                        ["name" => "qlm_3_1_1_text", "label" => "Description", "type" => "textarea"],
                        ["name" => "file_3_1_1", "label" => "Supporting Document", "type" => "file", "accept" => ".pdf"]
                    ]
                ],
                [
                    "id" => "3.1.2",
                    "title" => "3.1.2: Seed money for research",
                    "description" => "The institution provides seed money to its teachers for research.",
                    "fields" => [
                        ["name" => "qnm_3_1_2_amt", "label" => "Total Amount of seed money provided (in Lakhs)", "type" => "number"],
                        [
                            "name" => "table_3_1_2",
                            "label" => "Data Template: Seed Money",
                            "type" => "table",
                            "columns" => [
                                ["name" => "teacher_name", "label" => "Name of Teacher", "type" => "text"],
                                ["name" => "amount", "label" => "Amount provided (INR in Lakhs)", "type" => "number"],
                                ["name" => "year", "label" => "Year of receiving", "type" => "number"]
                            ]
                        ],
                        ["name" => "file_3_1_2", "label" => "Supporting Document", "type" => "file", "accept" => ".pdf"]
                    ]
                ],
                [
                    "id" => "3.1.3",
                    "title" => "3.1.3: Fellowships to teachers",
                    "description" => "Percentage of teachers receiving national/ international fellowship/financial support by various agencies for advanced studies/ research.",
                    "fields" => [
                        ["name" => "qnm_3_1_3_teachers", "label" => "Number of teachers receiving fellowship (Numerator)", "type" => "number"],
                        ["name" => "qnm_3_1_3_total", "label" => "Total number of full time teachers (Denominator)", "type" => "number"],
                        [
                            "name" => "table_3_1_3",
                            "label" => "Data Template: Fellowships",
                            "type" => "table",
                            "columns" => [
                                ["name" => "teacher_name", "label" => "Name of Teacher", "type" => "text"],
                                ["name" => "fellowship_name", "label" => "Name of Fellowship", "type" => "text"],
                                ["name" => "year", "label" => "Year of Award", "type" => "number"],
                                ["name" => "awarding_agency", "label" => "Awarding Agency", "type" => "text"]
                            ]
                        ],
                        ["name" => "file_3_1_3", "label" => "Supporting Document", "type" => "file", "accept" => ".pdf"]
                    ]
                ],
                [
                    "id" => "3.1.4",
                    "title" => "3.1.4: Research Fellows",
                    "description" => "Number of JRFs, SRFs, Post Doctoral Fellows, Research Associates and other research fellows enrolled.",
                    "fields" => [
                        ["name" => "qnm_3_1_4_fellows", "label" => "Number of Research Fellows", "type" => "number"],
                        [
                            "name" => "table_3_1_4",
                            "label" => "Data Template: Research Fellows",
                            "type" => "table",
                            "columns" => [
                                ["name" => "fellow_name", "label" => "Name of Research Fellow", "type" => "text"],
                                ["name" => "year_enroll", "label" => "Year of Enrolment", "type" => "number"],
                                ["name" => "duration", "label" => "Duration", "type" => "text"],
                                ["name" => "type", "label" => "Type (JRF/SRF/PDF/RA)", "type" => "text"],
                                ["name" => "agency", "label" => "Granting Agency", "type" => "text"]
                            ]
                        ],
                        ["name" => "file_3_1_4", "label" => "Supporting Document", "type" => "file", "accept" => ".pdf"]
                    ]
                ],
                [
                    "id" => "3.1.5",
                    "title" => "3.1.5: Institution Facilities",
                    "description" => "Institution has Central Instrumentation Centre, Animal House, Museum, Media laboratory, Business Lab, etc.",
                    "fields" => [
                        [
                            "name" => "facilities_list",
                            "label" => "Facilities available (Check all that apply):",
                            "type" => "checkbox_group",
                            "options" => [
                                ["name" => "fac_cic", "label" => "Central Instrumentation Centre"],
                                ["name" => "fac_animal", "label" => "Animal House/Green House"],
                                ["name" => "fac_museum", "label" => "Museum"],
                                ["name" => "fac_media", "label" => "Media laboratory/Studios"],
                                ["name" => "fac_business", "label" => "Business Lab"]
                            ]
                        ],
                        ["name" => "file_3_1_5", "label" => "Supporting Document", "type" => "file", "accept" => ".pdf"]
                    ]
                ],
                [
                    "id" => "3.1.6",
                    "title" => "3.1.6: Departmental Research Schemes",
                    "description" => "Percentage of departments with UGC-SAP, CAS, DST-FIST, DBT, ICSSR etc.",
                    "fields" => [
                        ["name" => "qnm_3_1_6_depts", "label" => "Number of Departments with schemes (Numerator)", "type" => "number"],
                        ["name" => "qnm_3_1_6_total", "label" => "Total number of Departments (Denominator)", "type" => "number"],
                        [
                            "name" => "table_3_1_6",
                            "label" => "Data Template: Departmental Schemes",
                            "type" => "table",
                            "columns" => [
                                ["name" => "dept_name", "label" => "Name of Department", "type" => "text"],
                                ["name" => "scheme_name", "label" => "Name of Scheme", "type" => "text"],
                                ["name" => "funding_agency", "label" => "Funding Agency", "type" => "text"],
                                ["name" => "year_award", "label" => "Year of Award", "type" => "number"],
                                ["name" => "funds", "label" => "Funds Provided (Lakhs)", "type" => "number"]
                            ]
                        ],
                        ["name" => "file_3_1_6", "label" => "Supporting Document", "type" => "file", "accept" => ".pdf"]
                    ]
                ]
            ]
        ],
        [
            "title" => "3.2 Resource Mobilization for Research",
            "metrics" => [
                [
                    "id" => "3.2.1",
                    "title" => "3.2.1: Extramural funding for Research",
                    "description" => "Extramural funding for Research (Grants in INR Lakhs).",
                    "fields" => [
                        ["name" => "qnm_3_2_1_funds", "label" => "Total Extramural Funding (in Lakhs)", "type" => "number"],
                        [
                            "name" => "table_3_2_1",
                            "label" => "Data Template: Extramural Funding",
                            "type" => "table",
                            "columns" => [
                                ["name" => "pi_name", "label" => "Name of PI/Co-PI", "type" => "text"],
                                ["name" => "proj_title", "label" => "Title of Project", "type" => "text"],
                                ["name" => "funding_agency", "label" => "Funding Agency", "type" => "text"],
                                ["name" => "dept", "label" => "Department", "type" => "text"],
                                ["name" => "year_award", "label" => "Year of Award", "type" => "number"],
                                ["name" => "funds", "label" => "Funds Provided (Lakhs)", "type" => "number"],
                                ["name" => "duration", "label" => "Duration (Years)", "type" => "number"]
                            ]
                        ],
                        ["name" => "file_3_2_1", "label" => "Supporting Document", "type" => "file", "accept" => ".pdf"]
                    ]
                ],
                [
                    "id" => "3.2.2",
                    "title" => "3.2.2: Grants for research projects",
                    "description" => "Grants for research projects sponsored by the government agencies.",
                    "fields" => [
                        ["name" => "qnm_3_2_2_grants", "label" => "Total Grants for Research Projects (in Lakhs)", "type" => "number"],
                        [
                            "name" => "table_3_2_2",
                            "label" => "Data Template: Government Grants",
                            "type" => "table",
                            "columns" => [
                                ["name" => "pi_name", "label" => "Name of PI/Co-PI", "type" => "text"],
                                ["name" => "proj_title", "label" => "Title of Project", "type" => "text"],
                                ["name" => "funding_agency", "label" => "Funding Agency", "type" => "text"],
                                ["name" => "dept", "label" => "Department", "type" => "text"],
                                ["name" => "year_award", "label" => "Year of Award", "type" => "number"],
                                ["name" => "funds", "label" => "Funds Provided (Lakhs)", "type" => "number"],
                                ["name" => "duration", "label" => "Duration (Years)", "type" => "number"]
                            ]
                        ],
                        ["name" => "file_3_2_2", "label" => "Supporting Document", "type" => "file", "accept" => ".pdf"]
                    ]
                ],
                [
                    "id" => "3.2.3",
                    "title" => "3.2.3: Research projects per teacher",
                    "description" => "Number of research projects per teacher funded by government and non-government agencies.",
                    "fields" => [
                        ["name" => "qnm_3_2_3_projects", "label" => "Number of research projects (Numerator)", "type" => "number"],
                        ["name" => "qnm_3_2_3_teachers", "label" => "Total number of full time teachers (Denominator)", "type" => "number"],
                        [
                            "name" => "table_3_2_3",
                            "label" => "Data Template: Projects Per Teacher",
                            "type" => "table",
                            "columns" => [
                                ["name" => "pi_name", "label" => "Name of PI/Co-PI", "type" => "text"],
                                ["name" => "proj_title", "label" => "Title of Project", "type" => "text"],
                                ["name" => "funding_agency", "label" => "Funding Agency", "type" => "text"],
                                ["name" => "dept", "label" => "Department", "type" => "text"],
                                ["name" => "year_award", "label" => "Year of Award", "type" => "number"],
                                ["name" => "funds", "label" => "Funds Provided (Lakhs)", "type" => "number"],
                                ["name" => "duration", "label" => "Duration (Years)", "type" => "number"]
                            ]
                        ],
                        ["name" => "file_3_2_3", "label" => "Supporting Document", "type" => "file", "accept" => ".pdf"]
                    ]
                ]
            ]
        ],
        [
            "title" => "3.3 Innovation Ecosystem",
            "metrics" => [
                [
                    "id" => "3.3.1",
                    "title" => "3.3.1: Innovation ecosystem",
                    "description" => "Institution has created an eco system for innovations including Incubation centre.",
                    "fields" => [
                        ["name" => "qlm_3_3_1_text", "label" => "Description", "type" => "textarea"],
                        ["name" => "file_3_3_1", "label" => "Supporting Document", "type" => "file", "accept" => ".pdf"]
                    ]
                ],
                [
                    "id" => "3.3.2",
                    "title" => "3.3.2: Workshops/seminars on Research/IPR",
                    "description" => "Number of workshops/seminars conducted on Research methodology, IPR, entrepreneurship.",
                    "fields" => [
                        ["name" => "qnm_3_3_2_count", "label" => "Number of workshops/seminars", "type" => "number"],
                        [
                            "name" => "table_3_3_2",
                            "label" => "Data Template: Workshops/Seminars",
                            "type" => "table",
                            "columns" => [
                                ["name" => "workshop_name", "label" => "Name of Workshop", "type" => "text"],
                                ["name" => "participants", "label" => "Number of Participants", "type" => "number"],
                                ["name" => "date_range", "label" => "Date (From - To)", "type" => "text"]
                            ]
                        ],
                        ["name" => "file_3_3_2", "label" => "Supporting Document", "type" => "file", "accept" => ".pdf"]
                    ]
                ],
                [
                    "id" => "3.3.3",
                    "title" => "3.3.3: Innovation Awards",
                    "description" => "Number of awards / recognitions received for research/innovations.",
                    "fields" => [
                        ["name" => "qnm_3_3_3_awards", "label" => "Number of awards/recognitions", "type" => "number"],
                        [
                            "name" => "table_3_3_3",
                            "label" => "Data Template: Innovation Awards",
                            "type" => "table",
                            "columns" => [
                                ["name" => "title_innov", "label" => "Title of Innovation", "type" => "text"],
                                ["name" => "awardee_name", "label" => "Name of Awardee", "type" => "text"],
                                ["name" => "agency_name", "label" => "Awarding Agency", "type" => "text"],
                                ["name" => "year_award", "label" => "Year of Award", "type" => "number"]
                            ]
                        ],
                        ["name" => "file_3_3_3", "label" => "Supporting Document", "type" => "file", "accept" => ".pdf"]
                    ]
                ]
            ]
        ],
        [
            "title" => "3.4 Research Publications and Awards",
            "metrics" => [
                [
                    "id" => "3.4.1",
                    "title" => "3.4.1: Code of Ethics",
                    "description" => "The Institution ensures implementation of its stated Code of Ethics for research.",
                    "fields" => [
                        [
                            "name" => "ethics_list",
                            "label" => "Options (Check all that apply):",
                            "type" => "checkbox_group",
                            "options" => [
                                ["name" => "eth_comm", "label" => "Ethics Committee exists"],
                                ["name" => "eth_plag", "label" => "Plagiarism check software"],
                                ["name" => "eth_comm_meets", "label" => "Research Advisory Committee meets"]
                            ]
                        ],
                        ["name" => "file_3_4_1", "label" => "Supporting Document", "type" => "file", "accept" => ".pdf"]
                    ]
                ],
                [
                    "id" => "3.4.2",
                    "title" => "3.4.2: Teacher Incentives",
                    "description" => "The institution provides incentives to teachers who receive state, national and international recognitions.",
                    "fields" => [
                        [
                            "name" => "incentives_list",
                            "label" => "Incentives (Check all that apply):",
                            "type" => "checkbox_group",
                            "options" => [
                                ["name" => "inc_salary", "label" => "Commendation & monetary reward"],
                                ["name" => "inc_cert", "label" => "Certificate of honor"]
                            ]
                        ],
                        ["name" => "file_3_4_2", "label" => "Supporting Document", "type" => "file", "accept" => ".pdf"]
                    ]
                ],
                [
                    "id" => "3.4.3",
                    "title" => "3.4.3: Patents",
                    "description" => "Number of Patents published/awarded.",
                    "fields" => [
                        ["name" => "qnm_3_4_3_patents", "label" => "Number of Patents", "type" => "number"],
                        [
                            "name" => "table_3_4_3",
                            "label" => "Data Template: Patents",
                            "type" => "table",
                            "columns" => [
                                ["name" => "patenter_name", "label" => "Name of Patenter", "type" => "text"],
                                ["name" => "patent_num", "label" => "Patent Number", "type" => "text"],
                                ["name" => "patent_title", "label" => "Title of Patent", "type" => "text"],
                                ["name" => "year_award", "label" => "Year of Award", "type" => "number"]
                            ]
                        ],
                        ["name" => "file_3_4_3", "label" => "Supporting Document", "type" => "file", "accept" => ".pdf"]
                    ]
                ],
                [
                    "id" => "3.4.4",
                    "title" => "3.4.4: Ph.D's awarded per teacher",
                    "description" => "Number of Ph.D's awarded per teacher.",
                    "fields" => [
                        ["name" => "qnm_3_4_4_phd", "label" => "Number of Ph.D's awarded (Numerator)", "type" => "number"],
                        ["name" => "qnm_3_4_4_guides", "label" => "Number of recognized guides (Denominator)", "type" => "number"],
                        [
                            "name" => "table_3_4_4",
                            "label" => "Data Template: Ph.D.s Awarded",
                            "type" => "table",
                            "columns" => [
                                ["name" => "scholar_name", "label" => "Name of Scholar", "type" => "text"],
                                ["name" => "dept", "label" => "Department", "type" => "text"],
                                ["name" => "guide_name", "label" => "Name of Guide", "type" => "text"],
                                ["name" => "thesis_title", "label" => "Title of Thesis", "type" => "text"],
                                ["name" => "year_reg", "label" => "Year of Registration", "type" => "number"],
                                ["name" => "year_award", "label" => "Year of Award", "type" => "number"]
                            ]
                        ],
                        ["name" => "file_3_4_4", "label" => "Supporting Document", "type" => "file", "accept" => ".pdf"]
                    ]
                ],
                [
                    "id" => "3.4.5",
                    "title" => "3.4.5: Research Papers in UGC Journals",
                    "description" => "Number of research papers per teacher in the Journals notified on UGC website.",
                    "fields" => [
                        ["name" => "qnm_3_4_5_papers", "label" => "Number of Research Papers (Numerator)", "type" => "number"],
                        ["name" => "qnm_3_4_5_teachers", "label" => "Number of Full-Time Teachers (Denominator)", "type" => "number"],
                        [
                            "name" => "table_3_4_5",
                            "label" => "Data Template: Research Papers",
                            "type" => "table",
                            "columns" => [
                                ["name" => "paper_title", "label" => "Title of Paper", "type" => "text"],
                                ["name" => "author_name", "label" => "Name of Author", "type" => "text"],
                                ["name" => "dept", "label" => "Department", "type" => "text"],
                                ["name" => "journal_name", "label" => "Journal Name", "type" => "text"],
                                ["name" => "year_pub", "label" => "Year of Publication", "type" => "number"],
                                ["name" => "issn", "label" => "ISSN Number", "type" => "text"]
                            ]
                        ],
                        ["name" => "file_3_4_5", "label" => "Supporting Document", "type" => "file", "accept" => ".pdf"]
                    ]
                ],
                [
                    "id" => "3.4.6",
                    "title" => "3.4.6: Books and Chapters published",
                    "description" => "Number of books and chapters in edited volumes/books published.",
                    "fields" => [
                        ["name" => "qnm_3_4_6_books", "label" => "Number of books and chapters", "type" => "number"],
                        [
                            "name" => "table_3_4_6",
                            "label" => "Data Template: Books and Chapters",
                            "type" => "table",
                            "columns" => [
                                ["name" => "book_title", "label" => "Title of Book/Chapter", "type" => "text"],
                                ["name" => "author_name", "label" => "Author", "type" => "text"],
                                ["name" => "proceedings", "label" => "Title of proceedings", "type" => "text"],
                                ["name" => "publisher", "label" => "Publisher Name", "type" => "text"],
                                ["name" => "isbn", "label" => "ISBN", "type" => "text"],
                                ["name" => "year_pub", "label" => "Year of Publication", "type" => "number"]
                            ]
                        ],
                        ["name" => "file_3_4_6", "label" => "Supporting Document", "type" => "file", "accept" => ".pdf"]
                    ]
                ],
                [
                    "id" => "3.4.7",
                    "title" => "3.4.7: E-content developed by teachers",
                    "description" => "E-content developed by teachers for platforms like NMEICT/NPTEL/SWAYAM.",
                    "fields" => [
                        ["name" => "qnm_3_4_7_content", "label" => "Number of E-contents developed", "type" => "number"],
                        [
                            "name" => "table_3_4_7",
                            "label" => "Data Template: E-Content",
                            "type" => "table",
                            "columns" => [
                                ["name" => "teacher_name", "label" => "Name of Teacher", "type" => "text"],
                                ["name" => "module_name", "label" => "Name of Module", "type" => "text"],
                                ["name" => "platform", "label" => "Platform (SWAYAM etc)", "type" => "text"],
                                ["name" => "date_launch", "label" => "Date of Launch", "type" => "text"]
                            ]
                        ],
                        ["name" => "file_3_4_7", "label" => "Supporting Document", "type" => "file", "accept" => ".pdf"]
                    ]
                ],
                [
                    "id" => "3.4.8",
                    "title" => "3.4.8: Bibliometrics (Citation Index)",
                    "description" => "Bibliometrics of the publications based on average Citation Index in Scopus/ Web of Science.",
                    "fields" => [
                        ["name" => "qnm_3_4_8_citation", "label" => "Average Citation Index", "type" => "number"],
                        ["name" => "file_3_4_8", "label" => "Supporting Document (Citation Index)", "type" => "file", "accept" => ".pdf"]
                    ]
                ],
                [
                    "id" => "3.4.9",
                    "title" => "3.4.9: Bibliometrics (h-index)",
                    "description" => "Bibliometrics of the publications based on Scopus/ Web of Science - h-index.",
                    "fields" => [
                        ["name" => "qnm_3_4_9_hindex", "label" => "h-index of the institution", "type" => "number"],
                        ["name" => "file_3_4_9", "label" => "Supporting Document (h-index)", "type" => "file", "accept" => ".pdf"]
                    ]
                ]
            ]
        ],
        [
            "title" => "3.5 Consultancy",
            "metrics" => [
                [
                    "id" => "3.5.1",
                    "title" => "3.5.1: Consultancy Policy",
                    "description" => "Institution has a policy on consultancy including revenue sharing.",
                    "fields" => [
                        ["name" => "qlm_3_5_1_text", "label" => "Description", "type" => "textarea"],
                        ["name" => "file_3_5_1", "label" => "Supporting Document", "type" => "file", "accept" => ".pdf"]
                    ]
                ],
                [
                    "id" => "3.5.2",
                    "title" => "3.5.2: Revenue from Consultancy",
                    "description" => "Revenue generated from consultancy and corporate training.",
                    "fields" => [
                        ["name" => "qnm_3_5_2_revenue", "label" => "Total Revenue Generated (in Lakhs)", "type" => "number"],
                        [
                            "name" => "table_3_5_2",
                            "label" => "Data Template: Consultancy Revenue",
                            "type" => "table",
                            "columns" => [
                                ["name" => "consultant_name", "label" => "Name of Consultant", "type" => "text"],
                                ["name" => "project_name", "label" => "Name of Project", "type" => "text"],
                                ["name" => "agency_name", "label" => "Sponsoring Agency", "type" => "text"],
                                ["name" => "revenue", "label" => "Revenue (Lakhs)", "type" => "number"]
                            ]
                        ],
                        ["name" => "file_3_5_2", "label" => "Supporting Document", "type" => "file", "accept" => ".pdf"]
                    ]
                ]
            ]
        ],
        [
            "title" => "3.6 Extension Activities",
            "metrics" => [
                [
                    "id" => "3.6.1",
                    "title" => "3.6.1: Extension activities in neighbourhood",
                    "description" => "Extension activities in the neighbourhood community in terms of impact.",
                    "fields" => [
                        ["name" => "qlm_3_6_1_text", "label" => "Description", "type" => "textarea"],
                        ["name" => "file_3_6_1", "label" => "Supporting Document", "type" => "file", "accept" => ".pdf"]
                    ]
                ],
                [
                    "id" => "3.6.2",
                    "title" => "3.6.2: Extension Awards",
                    "description" => "Number of awards received by the Institution, its teachers and students for extension activities.",
                    "fields" => [
                        ["name" => "qnm_3_6_2_awards", "label" => "Number of awards", "type" => "number"],
                        [
                            "name" => "table_3_6_2",
                            "label" => "Data Template: Extension Awards",
                            "type" => "table",
                            "columns" => [
                                ["name" => "activity_name", "label" => "Name of Activity", "type" => "text"],
                                ["name" => "award_name", "label" => "Name of Award", "type" => "text"],
                                ["name" => "awarding_body", "label" => "Awarding Body", "type" => "text"],
                                ["name" => "year", "label" => "Year of Award", "type" => "number"]
                            ]
                        ],
                        ["name" => "file_3_6_2", "label" => "Supporting Document", "type" => "file", "accept" => ".pdf"]
                    ]
                ],
                [
                    "id" => "3.6.3",
                    "title" => "3.6.3: Extension Programs Conducted",
                    "description" => "Number of extension and outreach programs conducted in collaboration with industry, community and Non-Government Organisations.",
                    "fields" => [
                        ["name" => "qnm_3_6_3_programs", "label" => "Number of programs conducted", "type" => "number"],
                        [
                            "name" => "table_3_6_3",
                            "label" => "Data Template: Extension Programs",
                            "type" => "table",
                            "columns" => [
                                ["name" => "activity_name", "label" => "Name of Activity", "type" => "text"],
                                ["name" => "organising_unit", "label" => "Organising Unit/Agency", "type" => "text"],
                                ["name" => "scheme_name", "label" => "Name of Scheme", "type" => "text"],
                                ["name" => "year", "label" => "Year", "type" => "number"]
                            ]
                        ],
                        ["name" => "file_3_6_3", "label" => "Supporting Document", "type" => "file", "accept" => ".pdf"]
                    ]
                ],
                [
                    "id" => "3.6.4",
                    "title" => "3.6.4: Student Participation in Extension",
                    "description" => "Average percentage of students participating in extension activities listed at 3.6.3 above.",
                    "fields" => [
                        ["name" => "qnm_3_6_4_participating", "label" => "Number of students participating (Numerator)", "type" => "number"],
                        ["name" => "qnm_3_6_4_total", "label" => "Total number of students (Denominator)", "type" => "number"],
                        [
                            "name" => "table_3_6_4",
                            "label" => "Data Template: Student Participation",
                            "type" => "table",
                            "columns" => [
                                ["name" => "activity_name", "label" => "Name of Activity", "type" => "text"],
                                ["name" => "organising_unit", "label" => "Organising Unit/Agency", "type" => "text"],
                                ["name" => "scheme_name", "label" => "Name of Scheme", "type" => "text"],
                                ["name" => "year", "label" => "Year", "type" => "number"],
                                ["name" => "student_count", "label" => "Number of Students Participated", "type" => "number"]
                            ]
                        ],
                        ["name" => "file_3_6_4", "label" => "Supporting Document", "type" => "file", "accept" => ".pdf"]
                    ]
                ]
            ]
        ],
        [
            "title" => "3.7 Collaboration",
            "metrics" => [
                [
                    "id" => "3.7.1",
                    "title" => "3.7.1: Collaborative activities",
                    "description" => "Number of collaborative activities for research, Faculty exchange, Student exchange.",
                    "fields" => [
                        ["name" => "qnm_3_7_1_collabs", "label" => "Number of collaborative activities", "type" => "number"],
                        [
                            "name" => "table_3_7_1",
                            "label" => "Data Template: Collaborations",
                            "type" => "table",
                            "columns" => [
                                ["name" => "title_collab", "label" => "Title of Collaboration", "type" => "text"],
                                ["name" => "agency_name", "label" => "Collaborating Agency", "type" => "text"],
                                ["name" => "fin_support", "label" => "Financial Support Source", "type" => "text"],
                                ["name" => "year", "label" => "Year of Collaboration", "type" => "number"],
                                ["name" => "duration", "label" => "Duration", "type" => "text"]
                            ]
                        ],
                        ["name" => "file_3_7_1", "label" => "Supporting Document", "type" => "file", "accept" => ".pdf"]
                    ]
                ],
                [
                    "id" => "3.7.2",
                    "title" => "3.7.2: Functional MoUs",
                    "description" => "Number of functional MoUs with institutions, industries, corporate houses etc.",
                    "fields" => [
                        ["name" => "qnm_3_7_2_mous", "label" => "Number of functional MoUs", "type" => "number"],
                        [
                            "name" => "table_3_7_2",
                            "label" => "Data Template: Functional MoUs",
                            "type" => "table",
                            "columns" => [
                                ["name" => "org_name", "label" => "Organisation with MoU", "type" => "text"],
                                ["name" => "inst_name", "label" => "Name of Institution/Industry", "type" => "text"],
                                ["name" => "year_sign", "label" => "Year of Signing", "type" => "number"],
                                ["name" => "duration", "label" => "Duration", "type" => "text"],
                                ["name" => "activities_list", "label" => "List of Activities", "type" => "text"]
                            ]
                        ],
                        ["name" => "file_3_7_2", "label" => "Supporting Document", "type" => "file", "accept" => ".pdf"]
                    ]
                ]
            ]
        ]
    ]
];

$json_str_c3 = json_encode($schema_c3);
$conn->query("UPDATE naac_form_schemas SET is_active = 0 WHERE criterion_number = 3");
$stmt3 = $conn->prepare("INSERT INTO naac_form_schemas (criterion_number, version_name, is_active, schema_json) VALUES (3, 'v4_university_with_tables', 1, ?)");
$stmt3->bind_param("s", $json_str_c3);
$stmt3->execute();
echo "Inserted Criterion 3 (University with Tables) Schema and set it as active.\n";
