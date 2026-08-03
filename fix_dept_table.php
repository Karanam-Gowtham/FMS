<?php
$conn = mysqli_connect('localhost', 'root', '', 'project-fms');

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$queries = [
    "DROP TABLE IF EXISTS `dept`",
    "CREATE TABLE `dept` (
      `dept_id` int(11) NOT NULL AUTO_INCREMENT,
      `dept_name` varchar(50) NOT NULL,
      PRIMARY KEY (`dept_id`),
      UNIQUE KEY `dept_name` (`dept_name`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci",
    "INSERT INTO `dept` VALUES (2,'AI_DS'),(3,'AI_ML'),(19,'Anti_Ragging'),(9,'BSH'),(24,'Chemistry'),(8,'CIVIL'),(14,'Clubs'),(1,'CSE'),(21,'CSE-CS'),(5,'ECE'),(6,'EEE'),(16,'IIC'),(4,'IT'),(22,'MatheMatics'),(7,'MECH'),(10,'NAAC'),(11,'NBA'),(12,'NCC'),(15,'NSS'),(18,'PASH'),(23,'Physics'),(20,'SAC'),(13,'Sports'),(17,'Women_Empowerment')"
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
