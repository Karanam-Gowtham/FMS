<?php
require 'includes/connection.php';

function registerUserWithRole($conn, $name, $identifier, $password, $role_id, $dept_id)
{
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

echo "Migrating Criteria Coordinators...\n";
$res = $conn->query("SELECT * FROM reg_cri_cord");
while ($row = $res->fetch_assoc()) {
    $userid = $row['userid'];
    $pwd = $row['password'];
    
    // Assign them to Role 2 (IQAC/Criteria) and Dept 10 (NAAC) for now. They can switch depts if needed, or we can assign them to both NAAC and NBA.
    registerUserWithRole($conn, "Criteria Coordinator", $userid, $pwd, 2, 10); // NAAC
    registerUserWithRole($conn, "Criteria Coordinator", $userid, $pwd, 2, 11); // NBA
    echo "Migrated Criteria Coordinator: $userid to NAAC and NBA\n";
}

// Also add dedicated NAAC and NBA test accounts based on test_logic.php
registerUserWithRole($conn, "NAAC Coordinator", "naac@gmail.com", "123", 2, 10);
registerUserWithRole($conn, "NBA Coordinator", "nba@gmail.com", "123", 2, 11);

echo "Done migrating Criteria Coordinators.\n";
$conn->close();
?>
