<?php
$conn = mysqli_connect("localhost", "root", "", "project-fms");
if (!$conn) die("Connection failed: " . mysqli_connect_error());

// Add mode to fdps_tab if it doesn't exist
$result = mysqli_query($conn, "SHOW COLUMNS FROM fdps_tab LIKE 'mode'");
if (mysqli_num_rows($result) == 0) {
    if (mysqli_query($conn, "ALTER TABLE fdps_tab ADD COLUMN mode VARCHAR(50) AFTER title")) {
        echo "Added mode to fdps_tab\n";
    } else {
        echo "Error adding mode to fdps_tab: " . mysqli_error($conn) . "\n";
    }
} else {
    echo "mode already exists in fdps_tab\n";
}

// Add mode to fdps_org_tab if it doesn't exist (since it's in the form)
$result = mysqli_query($conn, "SHOW COLUMNS FROM fdps_org_tab LIKE 'mode'");
if (mysqli_num_rows($result) == 0) {
    if (mysqli_query($conn, "ALTER TABLE fdps_org_tab ADD COLUMN mode VARCHAR(50) AFTER title")) {
        echo "Added mode to fdps_org_tab\n";
    } else {
        echo "Error adding mode to fdps_org_tab: " . mysqli_error($conn) . "\n";
    }
} else {
    echo "mode already exists in fdps_org_tab\n";
}
?>
