<?php
$conn = mysqli_connect('localhost', 'root', '', 'project-fms');

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Ensure foreign key checks are disabled during drop/create
mysqli_query($conn, "SET FOREIGN_KEY_CHECKS = 0");

$queries = [
    "DROP TABLE IF EXISTS `user_roles`",
    "DROP TABLE IF EXISTS `roles`",
    "CREATE TABLE `roles` (
      `role_id` int(11) NOT NULL AUTO_INCREMENT,
      `role_name` varchar(50) NOT NULL,
      `role_description` varchar(255) DEFAULT NULL,
      PRIMARY KEY (`role_id`),
      UNIQUE KEY `role_name` (`role_name`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci",
    "INSERT INTO `roles` VALUES (1,'Admin','System administrator'),(2,'IQAC','IQAC coordinator'),(3,'HOD','Head of Department'),(4,'Faculty','Faculty member'),(5,'Coordinator','Department/Cell coordinator'),(6,'Central_Coordinator','Central repository coordinator'),(7,'Junior_Assistant','Administrative assistant')",
    "CREATE TABLE `user_roles` (
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
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci",
    "INSERT INTO `user_roles` VALUES (1,1,1,10),(2,2,4,2),(3,999,1,1)"
];

foreach ($queries as $query) {
    if (mysqli_query($conn, $query)) {
        echo "Successfully executed query.\n";
    } else {
        echo "Error: " . mysqli_error($conn) . "\n";
    }
}

mysqli_query($conn, "SET FOREIGN_KEY_CHECKS = 1");
mysqli_close($conn);
?>
