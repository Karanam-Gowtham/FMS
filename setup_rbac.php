<?php
require_once __DIR__ . '/includes/connection.php';

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error() . "\n");
}

echo "--- Initializing Unified RBAC Tables ---\n";

// 1. Roles table
mysqli_query($conn, "CREATE TABLE IF NOT EXISTS `roles` (
  `role_id` int(11) NOT NULL AUTO_INCREMENT,
  `role_name` varchar(50) NOT NULL,
  `role_description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`role_id`),
  UNIQUE KEY `role_name` (`role_name`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4;");

$roles_data = [
    [1, 'Admin', 'System administrator'],
    [2, 'IQAC', 'IQAC coordinator'],
    [3, 'HOD', 'Head of Department'],
    [4, 'Faculty', 'Faculty member'],
    [5, 'Dept Coordinator', 'Department coordinator'],
    [6, 'Central Coordinator', 'Central repository coordinator'],
    [7, 'Junior Assistant', 'Administrative assistant']
];
foreach ($roles_data as $r) {
    mysqli_query($conn, "INSERT INTO `roles` (role_id, role_name, role_description) VALUES ({$r[0]}, '{$r[1]}', '{$r[2]}') ON DUPLICATE KEY UPDATE role_name='{$r[1]}', role_description='{$r[2]}'");
}

// 2. Dept table
mysqli_query($conn, "CREATE TABLE IF NOT EXISTS `dept` (
  `dept_id` int(11) NOT NULL AUTO_INCREMENT,
  `dept_name` varchar(50) NOT NULL,
  PRIMARY KEY (`dept_id`),
  UNIQUE KEY `dept_name` (`dept_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

$depts = [
    1 => 'CSE', 2 => 'AI_DS', 3 => 'AI_ML', 4 => 'IT', 5 => 'ECE', 6 => 'EEE', 7 => 'MECH', 8 => 'CIVIL', 9 => 'BSH',
    10 => 'NAAC', 11 => 'NBA', 12 => 'NCC', 13 => 'Sports', 14 => 'Clubs', 15 => 'NSS', 16 => 'IIC', 17 => 'Women_Empowerment',
    18 => 'PASH', 19 => 'Anti_Ragging', 20 => 'SAC', 21 => 'CSE-CS', 22 => 'MatheMatics', 23 => 'Physics', 24 => 'Chemistry'
];
foreach ($depts as $id => $name) {
    mysqli_query($conn, "INSERT INTO `dept` (dept_id, dept_name) VALUES ($id, '$name') ON DUPLICATE KEY UPDATE dept_name='$name'");
}

// 3. Users table
mysqli_query($conn, "CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `profile_photo` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `last_login` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

// 4. User_Roles table
mysqli_query($conn, "CREATE TABLE IF NOT EXISTS `user_roles` (
  `user_role_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `dept_id` int(11) NOT NULL,
  PRIMARY KEY (`user_role_id`),
  UNIQUE KEY `user_role_dept` (`user_id`,`role_id`,`dept_id`),
  KEY `role_id` (`role_id`),
  KEY `dept_id` (`dept_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

// 5. Document_Actions table
mysqli_query($conn, "CREATE TABLE IF NOT EXISTS `document_actions` (
  `action_id` int(11) NOT NULL AUTO_INCREMENT,
  `document_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `action_type` varchar(50) NOT NULL,
  `remarks` text DEFAULT NULL,
  `action_date` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`action_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

// Helper function to insert user and assign role
function registerUserWithRole($conn, $name, $identifier, $password, $role_id, $dept_name = 'CSE') {
    $dept_res = mysqli_query($conn, "SELECT dept_id FROM dept WHERE dept_name = '" . mysqli_real_escape_string($conn, $dept_name) . "' LIMIT 1");
    if ($dept_row = mysqli_fetch_assoc($dept_res)) {
        $dept_id = (int)$dept_row['dept_id'];
    } else {
        $dept_id = 1; // Default to CSE
    }

    $email = (strpos($identifier, '@') !== false) ? $identifier : ($identifier . "@gmrit.edu.in");

    $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ? OR full_name = ?");
    $stmt->bind_param("ss", $email, $identifier);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($row = $res->fetch_assoc()) {
        $user_id = $row['user_id'];
    } else {
        $ins = $conn->prepare("INSERT INTO users (full_name, email, password) VALUES (?, ?, ?)");
        $ins->bind_param("sss", $identifier, $email, $password);
        $ins->execute();
        $user_id = $conn->insert_id;
        $ins->close();
    }
    $stmt->close();

    $r_ins = $conn->prepare("INSERT IGNORE INTO user_roles (user_id, role_id, dept_id) VALUES (?, ?, ?)");
    $r_ins->bind_param("iii", $user_id, $role_id, $dept_id);
    $r_ins->execute();
    $r_ins->close();
    
    return $user_id;
}

echo "\n--- Migrating Users from Legacy Tables ---\n";

// Seed Admin accounts
registerUserWithRole($conn, 'Admin', 'admin', 'admin123', 1, 'CSE');
registerUserWithRole($conn, 'Chandu Admin', 'chandu', '123', 1, 'CSE');

// Migrate Faculty (reg_tab)
$res = mysqli_query($conn, "SELECT * FROM reg_tab");
if ($res) {
    while ($row = mysqli_fetch_assoc($res)) {
        $userid = !empty($row['userid']) ? $row['userid'] : $row['email'];
        $name = !empty($row['name']) ? $row['name'] : $userid;
        $pwd = !empty($row['password']) ? $row['password'] : '123';
        $dept = !empty($row['dept']) ? $row['dept'] : 'CSE';
        registerUserWithRole($conn, $name, $userid, $pwd, 4, $dept);
        echo "Migrated Faculty: $userid ($dept)\n";
    }
}

// Migrate HODs (reg_hod)
$res = mysqli_query($conn, "SELECT * FROM reg_hod");
if ($res) {
    while ($row = mysqli_fetch_assoc($res)) {
        $userid = $row['userid'];
        $pwd = $row['password'];
        $dept = $row['department'];
        registerUserWithRole($conn, "HOD $dept", $userid, $pwd, 3, $dept);
        echo "Migrated HOD: $userid ($dept)\n";
    }
}
// Add generic 'hod' account
registerUserWithRole($conn, 'Generic HOD', 'hod', '123', 3, 'CSE');

// Migrate Dept Coordinators (reg_dept_cord)
$res = mysqli_query($conn, "SELECT * FROM reg_dept_cord");
if ($res) {
    while ($row = mysqli_fetch_assoc($res)) {
        $userid = $row['userid'];
        $pwd = $row['password'];
        $dept = $row['department'];
        registerUserWithRole($conn, "Dept Coordinator $dept", $userid, $pwd, 5, $dept);
        echo "Migrated Dept Coordinator: $userid ($dept)\n";
    }
}

// Migrate Central Coordinators (reg_central_cord)
$res = mysqli_query($conn, "SELECT * FROM reg_central_cord");
if ($res) {
    while ($row = mysqli_fetch_assoc($res)) {
        $userid = $row['userid'];
        $pwd = $row['password'];
        registerUserWithRole($conn, "Central Coordinator", $userid, $pwd, 6, 'Clubs');
        echo "Migrated Central Coordinator: $userid\n";
    }
}
registerUserWithRole($conn, 'Generic Central Coordinator', 'central', '123', 6, 'Clubs');

// Migrate Jr Assistants (reg_jr_assistant)
$res = mysqli_query($conn, "SELECT * FROM reg_jr_assistant");
if ($res) {
    while ($row = mysqli_fetch_assoc($res)) {
        $userid = $row['userid'];
        $pwd = $row['password'];
        $dept = !empty($row['dept']) ? $row['dept'] : (!empty($row['department']) ? $row['department'] : 'CSE');
        registerUserWithRole($conn, "Junior Assistant $dept", $userid, $pwd, 7, $dept);
        echo "Migrated Junior Assistant: $userid ($dept)\n";
    }
}

mysqli_close($conn);
echo "\n--- RBAC Setup & Migration Complete ---\n";
