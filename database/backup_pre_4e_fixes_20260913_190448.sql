-- FMS Backup (Pre-4E Fixes)
-- Date: 2026-09-13 19:04:48

SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `workflow_transitions`;
CREATE TABLE `workflow_transitions` (
  `transition_id` int(11) NOT NULL AUTO_INCREMENT,
  `step_id` int(11) NOT NULL,
  `action_id` int(11) NOT NULL,
  `to_step_id` int(11) DEFAULT NULL,
  `resulting_status` varchar(50) NOT NULL,
  PRIMARY KEY (`transition_id`),
  UNIQUE KEY `uq_step_action` (`step_id`,`action_id`),
  KEY `action_id` (`action_id`),
  KEY `to_step_id` (`to_step_id`),
  CONSTRAINT `workflow_transitions_ibfk_1` FOREIGN KEY (`step_id`) REFERENCES `workflow_steps` (`step_id`),
  CONSTRAINT `workflow_transitions_ibfk_2` FOREIGN KEY (`action_id`) REFERENCES `workflow_actions` (`action_id`),
  CONSTRAINT `workflow_transitions_ibfk_3` FOREIGN KEY (`to_step_id`) REFERENCES `workflow_steps` (`step_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `workflow_transitions` VALUES ('1', '1', '1', '2', 'pending');
INSERT INTO `workflow_transitions` VALUES ('2', '1', '2', NULL, 'rejected');
INSERT INTO `workflow_transitions` VALUES ('3', '1', '3', '1', 'pending');
INSERT INTO `workflow_transitions` VALUES ('4', '2', '1', NULL, 'accepted');
INSERT INTO `workflow_transitions` VALUES ('5', '2', '2', NULL, 'rejected');
INSERT INTO `workflow_transitions` VALUES ('6', '2', '3', '1', 'pending');
INSERT INTO `workflow_transitions` VALUES ('7', '3', '1', NULL, 'accepted');
INSERT INTO `workflow_transitions` VALUES ('8', '3', '2', NULL, 'rejected');
INSERT INTO `workflow_transitions` VALUES ('9', '3', '3', '3', 'pending');

DROP TABLE IF EXISTS `document_actions`;
CREATE TABLE `document_actions` (
  `action_id` int(11) NOT NULL AUTO_INCREMENT,
  `doc_id` int(11) NOT NULL,
  `acted_by` int(11) NOT NULL,
  `action` enum('uploaded','approved','rejected','resubmitted') NOT NULL,
  `step_id` int(11) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `acted_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`action_id`),
  KEY `acted_by` (`acted_by`),
  KEY `idx_doc` (`doc_id`),
  KEY `fk_doc_actions_step` (`step_id`),
  CONSTRAINT `document_actions_ibfk_1` FOREIGN KEY (`doc_id`) REFERENCES `documents` (`doc_id`),
  CONSTRAINT `document_actions_ibfk_2` FOREIGN KEY (`acted_by`) REFERENCES `users` (`user_id`),
  CONSTRAINT `fk_doc_actions_step` FOREIGN KEY (`step_id`) REFERENCES `workflow_steps` (`step_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `documents`;
CREATE TABLE `documents` (
  `doc_id` int(11) NOT NULL AUTO_INCREMENT,
  `doc_type_id` int(11) NOT NULL,
  `uploaded_by` int(11) NOT NULL,
  `dept_id` int(11) NOT NULL,
  `year_id` int(11) DEFAULT NULL,
  `title` varchar(500) NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'pending',
  `current_step` int(11) DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`doc_id`),
  KEY `idx_status` (`status`),
  KEY `idx_dept_status` (`dept_id`,`status`),
  KEY `idx_uploader` (`uploaded_by`),
  KEY `idx_type_year` (`doc_type_id`,`year_id`),
  KEY `fk_documents_current_step` (`current_step`),
  CONSTRAINT `documents_ibfk_1` FOREIGN KEY (`doc_type_id`) REFERENCES `document_types` (`type_id`),
  CONSTRAINT `documents_ibfk_2` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`user_id`),
  CONSTRAINT `documents_ibfk_3` FOREIGN KEY (`dept_id`) REFERENCES `dept` (`dept_id`),
  CONSTRAINT `fk_documents_current_step` FOREIGN KEY (`current_step`) REFERENCES `workflow_steps` (`step_id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `documents` VALUES ('1', '1', '1050', '1', '5', 'cseres', 'accepted', NULL, NULL, '0000-00-00 00:00:00', '2026-08-12 07:40:16');
INSERT INTO `documents` VALUES ('2', '2', '1050', '1', '5', 'cseconfpro', 'accepted', NULL, NULL, '0000-00-00 00:00:00', '2026-08-12 07:41:40');
INSERT INTO `documents` VALUES ('3', '3', '1050', '1', '5', 'csepatent', 'accepted', NULL, NULL, '0000-00-00 00:00:00', '2026-08-12 07:42:41');
INSERT INTO `documents` VALUES ('4', '4', '1050', '1', '5', 'csefdps', 'accepted', NULL, NULL, '0000-00-00 00:00:00', '2026-08-12 07:01:11');
INSERT INTO `documents` VALUES ('5', '4', '1050', '1', '5', 'fdps2', 'accepted', NULL, NULL, '0000-00-00 00:00:00', '2026-08-12 13:36:56');
INSERT INTO `documents` VALUES ('6', '5', '1050', '1', '5', 'fdpsorg', 'accepted', NULL, NULL, '0000-00-00 00:00:00', '2026-08-12 07:26:27');
INSERT INTO `documents` VALUES ('7', '6', '1050', '1', '5', 'cseconforg', 'accepted', NULL, NULL, '0000-00-00 00:00:00', '2026-08-12 07:34:20');
INSERT INTO `documents` VALUES ('8', '8', '1050', '1', '5', 'result analysis', 'pending', '1', NULL, '0000-00-00 00:00:00', '2026-08-12 07:49:47');
INSERT INTO `documents` VALUES ('9', '8', '1050', '1', '5', 'studentrelfile', 'pending', '1', NULL, '0000-00-00 00:00:00', '2026-08-12 07:51:00');
INSERT INTO `documents` VALUES ('10', '8', '1050', '1', '5', 'studentrelfile', 'pending', '1', NULL, '0000-00-00 00:00:00', '2026-08-12 07:51:41');
INSERT INTO `documents` VALUES ('11', '8', '1050', '1', '5', 'examsec', 'pending', '1', NULL, '0000-00-00 00:00:00', '2026-08-12 07:52:22');
INSERT INTO `documents` VALUES ('12', '8', '1050', '1', '4', 'kgjm,hbv', 'rejected', '1', NULL, '0000-00-00 00:00:00', '2026-08-12 10:31:41');
INSERT INTO `documents` VALUES ('13', '17', '1050', '1', '5', 'sjournal1', 'pending', '1', NULL, '0000-00-00 00:00:00', '2026-08-12 08:07:42');

DROP TABLE IF EXISTS `user_roles`;
CREATE TABLE `user_roles` (
  `user_role_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `dept_id` int(11) NOT NULL,
  PRIMARY KEY (`user_role_id`),
  UNIQUE KEY `user_id` (`user_id`,`role_id`,`dept_id`),
  KEY `role_id` (`role_id`),
  KEY `dept_id` (`dept_id`),
  CONSTRAINT `user_roles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  CONSTRAINT `user_roles_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`),
  CONSTRAINT `user_roles_ibfk_3` FOREIGN KEY (`dept_id`) REFERENCES `dept` (`dept_id`)
) ENGINE=InnoDB AUTO_INCREMENT=100 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `user_roles` VALUES ('4', '1', '1', '1');
INSERT INTO `user_roles` VALUES ('1', '1', '1', '10');
INSERT INTO `user_roles` VALUES ('3', '999', '1', '1');
INSERT INTO `user_roles` VALUES ('10', '1004', '3', '1');
INSERT INTO `user_roles` VALUES ('11', '1005', '3', '3');
INSERT INTO `user_roles` VALUES ('12', '1006', '3', '2');
INSERT INTO `user_roles` VALUES ('13', '1007', '3', '5');
INSERT INTO `user_roles` VALUES ('14', '1008', '3', '6');
INSERT INTO `user_roles` VALUES ('15', '1009', '3', '7');
INSERT INTO `user_roles` VALUES ('16', '1010', '3', '8');
INSERT INTO `user_roles` VALUES ('17', '1011', '3', '4');
INSERT INTO `user_roles` VALUES ('18', '1012', '3', '1');
INSERT INTO `user_roles` VALUES ('19', '1013', '5', '1');
INSERT INTO `user_roles` VALUES ('20', '1014', '5', '1');
INSERT INTO `user_roles` VALUES ('21', '1015', '5', '1');
INSERT INTO `user_roles` VALUES ('22', '1016', '5', '4');
INSERT INTO `user_roles` VALUES ('23', '1017', '5', '5');
INSERT INTO `user_roles` VALUES ('24', '1018', '5', '6');
INSERT INTO `user_roles` VALUES ('25', '1019', '5', '7');
INSERT INTO `user_roles` VALUES ('26', '1020', '5', '8');
INSERT INTO `user_roles` VALUES ('27', '1021', '5', '9');
INSERT INTO `user_roles` VALUES ('28', '1022', '6', '14');
INSERT INTO `user_roles` VALUES ('29', '1023', '6', '14');
INSERT INTO `user_roles` VALUES ('30', '1024', '6', '14');
INSERT INTO `user_roles` VALUES ('31', '1025', '6', '14');
INSERT INTO `user_roles` VALUES ('32', '1026', '6', '14');
INSERT INTO `user_roles` VALUES ('33', '1027', '6', '14');
INSERT INTO `user_roles` VALUES ('34', '1028', '6', '14');
INSERT INTO `user_roles` VALUES ('35', '1029', '6', '14');
INSERT INTO `user_roles` VALUES ('36', '1030', '6', '14');
INSERT INTO `user_roles` VALUES ('37', '1031', '6', '14');
INSERT INTO `user_roles` VALUES ('38', '1032', '6', '14');
INSERT INTO `user_roles` VALUES ('39', '1033', '6', '14');
INSERT INTO `user_roles` VALUES ('40', '1034', '7', '1');
INSERT INTO `user_roles` VALUES ('42', '1036', '2', '10');
INSERT INTO `user_roles` VALUES ('43', '1036', '2', '11');
INSERT INTO `user_roles` VALUES ('44', '1037', '2', '10');
INSERT INTO `user_roles` VALUES ('45', '1037', '2', '11');
INSERT INTO `user_roles` VALUES ('46', '1038', '2', '10');
INSERT INTO `user_roles` VALUES ('47', '1038', '2', '11');
INSERT INTO `user_roles` VALUES ('48', '1039', '2', '10');
INSERT INTO `user_roles` VALUES ('49', '1039', '2', '11');
INSERT INTO `user_roles` VALUES ('50', '1040', '2', '10');
INSERT INTO `user_roles` VALUES ('51', '1040', '2', '11');
INSERT INTO `user_roles` VALUES ('52', '1041', '2', '10');
INSERT INTO `user_roles` VALUES ('53', '1041', '2', '11');
INSERT INTO `user_roles` VALUES ('54', '1042', '2', '10');
INSERT INTO `user_roles` VALUES ('55', '1042', '2', '11');
INSERT INTO `user_roles` VALUES ('56', '1043', '2', '10');
INSERT INTO `user_roles` VALUES ('57', '1043', '2', '11');
INSERT INTO `user_roles` VALUES ('58', '1044', '2', '10');
INSERT INTO `user_roles` VALUES ('59', '1044', '2', '11');
INSERT INTO `user_roles` VALUES ('60', '1045', '2', '10');
INSERT INTO `user_roles` VALUES ('61', '1045', '2', '11');
INSERT INTO `user_roles` VALUES ('62', '1046', '2', '10');
INSERT INTO `user_roles` VALUES ('63', '1046', '2', '11');
INSERT INTO `user_roles` VALUES ('64', '1047', '2', '10');
INSERT INTO `user_roles` VALUES ('65', '1048', '2', '11');
INSERT INTO `user_roles` VALUES ('67', '1049', '1', '1');
INSERT INTO `user_roles` VALUES ('99', '1050', '4', '1');

SET FOREIGN_KEY_CHECKS=1;
