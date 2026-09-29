-- Migration for Student Activities Files (Intra College Activities)
-- Creates the new document type and meta table.

-- 1. Create the new meta table for Student Activity Files
CREATE TABLE IF NOT EXISTS meta_student_activity_file (
    doc_id INT PRIMARY KEY,
    activity_category VARCHAR(100) NOT NULL,
    sub_category VARCHAR(200),
    event_type VARCHAR(100),
    topic_domain VARCHAR(200),
    event_title VARCHAR(300) NOT NULL,
    date_from DATE,
    date_to DATE,
    resource_person TEXT,
    target_audience VARCHAR(200),
    participant_count INT,
    location VARCHAR(200),
    event_mode VARCHAR(50),
    objective_outcome TEXT,
    FOREIGN KEY (doc_id) REFERENCES documents(doc_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Insert into document_types
INSERT INTO document_types (type_code, label, workflow_id) 
VALUES ('student_activity_file', 'Student Activity File', 1);
