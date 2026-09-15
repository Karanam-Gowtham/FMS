<?php
require_once __DIR__ . '/core/bootstrap.php';

$central_events = [
    'IQAC',
    'R&D',
    'Exam_Section',
    'NAAC',
    'NBA',
    'NCC',
    'Sports',
    'Clubs',
    'NSS',
    'Women_Empowerment',
    'IIC',
    'PASH',
    'Antiragging',
    'SAC'
];

$role_id_central = 6; // Central_Coordinator

foreach ($central_events as $event) {
    $email = strtolower(str_replace('&', 'n', $event)) . '@gmrit.edu.in';
    $password = password_hash('123456', PASSWORD_DEFAULT);
    $full_name = str_replace('_', ' ', $event) . ' Coordinator';
    
    // 1. Ensure 'department' exists (as a virtual department for the event)
    $d_stmt = $conn->prepare("SELECT dept_id FROM departments WHERE dept_name = ?");
    $d_stmt->bind_param("s", $event);
    $d_stmt->execute();
    $d_res = $d_stmt->get_result();
    
    if ($d_res->num_rows > 0) {
        $dept_id = $d_res->fetch_assoc()['dept_id'];
    } else {
        $di_stmt = $conn->prepare("INSERT INTO departments (dept_name) VALUES (?)");
        $di_stmt->bind_param("s", $event);
        $di_stmt->execute();
        $dept_id = $conn->insert_id;
        $di_stmt->close();
    }
    $d_stmt->close();

    // 2. Ensure User exists
    $u_stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
    $u_stmt->bind_param("s", $email);
    $u_stmt->execute();
    $u_res = $u_stmt->get_result();

    if ($u_res->num_rows > 0) {
        $user_id = $u_res->fetch_assoc()['user_id'];
    } else {
        $ui_stmt = $conn->prepare("INSERT INTO users (full_name, email, password) VALUES (?, ?, ?)");
        $ui_stmt->bind_param("sss", $full_name, $email, $password);
        $ui_stmt->execute();
        $user_id = $conn->insert_id;
        $ui_stmt->close();
    }
    $u_stmt->close();

    // 3. Assign Central Coordinator Role
    $r_stmt = $conn->prepare("SELECT * FROM user_roles WHERE user_id = ? AND role_id = ? AND dept_id = ?");
    $r_stmt->bind_param("iii", $user_id, $role_id_central, $dept_id);
    $r_stmt->execute();
    if ($r_stmt->get_result()->num_rows === 0) {
        $ri_stmt = $conn->prepare("INSERT INTO user_roles (user_id, role_id, dept_id) VALUES (?, ?, ?)");
        $ri_stmt->bind_param("iii", $user_id, $role_id_central, $dept_id);
        $ri_stmt->execute();
        $ri_stmt->close();
    }
    $r_stmt->close();

    echo "Provisioned: $email (Event: $event)\n";
}

echo "\nAll Central Coordinator accounts provisioned successfully. Password is: 123456\n";
