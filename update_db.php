<?php
$conn = mysqli_connect('localhost', 'root', '', 'project-fms');

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$queries = [
    "ALTER TABLE published_tab ADD COLUMN authors TEXT",
    "ALTER TABLE published_tab ADD COLUMN volume_no VARCHAR(255)",
    "ALTER TABLE published_tab ADD COLUMN issue_no VARCHAR(255)",
    "ALTER TABLE published_tab ADD COLUMN page_no VARCHAR(255)",
    "ALTER TABLE published_tab ADD COLUMN jcr_quartile VARCHAR(255)",
    "ALTER TABLE published_tab ADD COLUMN publication_link VARCHAR(255)",
    "ALTER TABLE published_tab ADD COLUMN scopus_quartile VARCHAR(255)",
    "ALTER TABLE published_tab ADD COLUMN issn_no VARCHAR(255)",
    "ALTER TABLE published_tab ADD COLUMN doi VARCHAR(255)"
];

foreach ($queries as $query) {
    if (mysqli_query($conn, $query)) {
        echo "Successfully executed: $query\n";
    } else {
        echo "Error or already exists ($query): " . mysqli_error($conn) . "\n";
    }
}

mysqli_close($conn);
?>
