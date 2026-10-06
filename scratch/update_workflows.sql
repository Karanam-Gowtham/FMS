-- Insert new workflow
INSERT INTO workflows (workflow_key, label, description) 
VALUES ('department_internal', 'Internal Department Review', 'Faculty uploads -> Dept Coordinator -> HOD -> Accepted');

SET @wf_id = LAST_INSERT_ID();

-- Insert steps
INSERT INTO workflow_steps (workflow_id, step_order, step_label, responsible_role_id, scope) VALUES
(@wf_id, 1, 'Dept Coordinator Review', 5, 'department'),
(@wf_id, 2, 'HOD Review', 3, 'department');

SET @step1 = (SELECT step_id FROM workflow_steps WHERE workflow_id = @wf_id AND step_order = 1);
SET @step2 = (SELECT step_id FROM workflow_steps WHERE workflow_id = @wf_id AND step_order = 2);

-- Insert transitions
-- Action IDs: 1 (Approve), 2 (Reject), 3 (Resubmit)
INSERT INTO workflow_transitions (step_id, action_id, to_step_id, resulting_status) VALUES
(@step1, 1, @step2, 'pending'),     -- Coord Approve -> HOD
(@step1, 2, NULL, 'rejected'),      -- Coord Reject -> Rejected
(@step1, 3, @step1, 'pending'),     -- Coord Resubmit

(@step2, 1, NULL, 'accepted'),      -- HOD Approve -> Accepted
(@step2, 2, NULL, 'rejected'),      -- HOD Reject -> Rejected
(@step2, 3, @step1, 'pending');     -- HOD Resubmit -> Back to Coord

-- Update document types
UPDATE document_types SET workflow_id = @wf_id WHERE type_code IN ('dept_file', 'student_activity_file');
