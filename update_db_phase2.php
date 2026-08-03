<?php
$conn = mysqli_connect('localhost', 'root', '', 'project-fms');

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$queries = [
    // conference_tab missing/new columns
    "ALTER TABLE conference_tab ADD COLUMN authors TEXT",
    "ALTER TABLE conference_tab ADD COLUMN conference_name VARCHAR(255)",
    "ALTER TABLE conference_tab ADD COLUMN published_paper_name VARCHAR(255)",
    "ALTER TABLE conference_tab ADD COLUMN volume_no VARCHAR(255)",
    "ALTER TABLE conference_tab ADD COLUMN issue_no VARCHAR(255)",
    "ALTER TABLE conference_tab ADD COLUMN page_no VARCHAR(255)",
    "ALTER TABLE conference_tab ADD COLUMN indexing VARCHAR(255)",
    "ALTER TABLE conference_tab ADD COLUMN publication_link VARCHAR(255)",
    "ALTER TABLE conference_tab ADD COLUMN issn_no VARCHAR(255)",
    "ALTER TABLE conference_tab ADD COLUMN doi VARCHAR(255)",

    // fdps_tab new columns
    "ALTER TABLE fdps_tab ADD COLUMN brochure VARCHAR(255)",
    "ALTER TABLE fdps_tab ADD COLUMN fdp_schedule VARCHAR(255)",

    // fdps_org_tab new columns
    "ALTER TABLE fdps_org_tab ADD COLUMN funded_by VARCHAR(255)",
    "ALTER TABLE fdps_org_tab ADD COLUMN external_funder_name VARCHAR(255)"
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
