<?php
require 'includes/connection.php';

// Fix aids-hod -> AI_DS (dept_id = 2)
$conn->query("UPDATE user_roles SET dept_id = 2 WHERE user_id = (SELECT user_id FROM users WHERE email = 'aids-hod@gmrit.edu.in')");

// Fix aiml-hod -> AI_ML (dept_id = 3)
$conn->query("UPDATE user_roles SET dept_id = 3 WHERE user_id = (SELECT user_id FROM users WHERE email = 'aiml-hod@gmrit.edu.in')");

echo "Fixed department assignments for aids-hod and aiml-hod.\n";
$conn->close();
?>
