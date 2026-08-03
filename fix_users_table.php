<?php
$conn = mysqli_connect('localhost', 'root', '', 'project-fms');

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$queries = [
    "DROP TABLE IF EXISTS `users`",
    "CREATE TABLE `users` (
      `user_id` int(11) NOT NULL AUTO_INCREMENT,
      `full_name` varchar(50) NOT NULL,
      `email` varchar(100) NOT NULL,
      `phone` varchar(15) DEFAULT NULL,
      `password` varchar(255) NOT NULL,
      `profile_photo` varchar(255) DEFAULT NULL,
      `status` enum('active','inactive') DEFAULT 'active',
      `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
      `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
      `last_login` timestamp NULL DEFAULT NULL,
      PRIMARY KEY (`user_id`),
      UNIQUE KEY `email` (`email`),
      UNIQUE KEY `phone` (`phone`)
    ) ENGINE=InnoDB AUTO_INCREMENT=1000 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci",
    "INSERT INTO `users` VALUES (1,'Admin','admin@gmrit.edu','9876543210','admin123',NULL,'active','2026-07-29 09:25:39','2026-07-29 09:25:39',NULL),(2,'gowtham','gowtham.lite@gmail.com','6304779532','123','uploads/profiles/doc_6a6a171134c8d4.40223605.png','active','2026-07-29 09:36:57','2026-08-02 06:39:17','2026-08-02 06:39:17'),(999,'Test User','test@gmrit.edu.in',NULL,'password123',NULL,'active','2026-07-29 09:40:24','2026-07-29 09:40:24',NULL)"
];

foreach ($queries as $query) {
    if (mysqli_query($conn, $query)) {
        echo "Successfully executed query.\n";
    } else {
        echo "Error ($query): " . mysqli_error($conn) . "\n";
    }
}

mysqli_close($conn);
?>
