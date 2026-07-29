<?php
$temp_sql_path = 'e:/set/xampp/htdocs/mini/temp/dataBase/master.sql';
$corrupted_sql_path = 'e:/set/xampp/htdocs/mini/FMS/database/master.sql';
$output_path = 'e:/set/xampp/htdocs/mini/FMS/database/master_new.sql';

if (file_exists($temp_sql_path) && file_exists($corrupted_sql_path)) {
    $temp_sql = file_get_contents($temp_sql_path);
    $corrupted_sql = file_get_contents($corrupted_sql_path);

    // Extract everything from the corrupted sql starting at "-- --- LEGACY CRITERIA TABLES ---" or similar
    $pos = strpos($corrupted_sql, "-- --- LEGACY CRITERIA TABLES ---");
    if ($pos !== false) {
        $legacy_sql = substr($corrupted_sql, $pos);
    } else {
        // Find the start of the first legacy table, e.g. "CREATE TABLE `academic_year`"
        $pos = strpos($corrupted_sql, "CREATE TABLE `academic_year`");
        if ($pos !== false) {
            $legacy_sql = "-- ==========================================\n-- LEGACY SCHEMA\n-- ==========================================\n" . substr($corrupted_sql, $pos);
        } else {
            $legacy_sql = "";
        }
    }

    // Patch temp_sql to introduce User_Roles
    $temp_sql = str_replace(
        "last_login timestamp null,\r\n        dept_id int not null,\r\n        foreign key(dept_id) references Dept(dept_id),\r\n        role_id int not null,\r\n        foreign key(role_id) references Roles(role_id)\r\n    );",
        "last_login timestamp null\r\n    );\r\n\r\n-- User_Roles (Mapping table for multiple roles)\r\n    create table if not exists User_Roles(\r\n        user_role_id int primary key auto_increment,\r\n        user_id int not null,\r\n        role_id int not null,\r\n        dept_id int not null,\r\n        foreign key(user_id) references Users(user_id),\r\n        foreign key(role_id) references Roles(role_id),\r\n        foreign key(dept_id) references Dept(dept_id),\r\n        unique(user_id, role_id, dept_id)\r\n    );",
        $temp_sql
    );
    
    // Patch temp_sql to handle the admin insert
    $temp_sql = str_replace(
        "-- Admin\r\ninsert into Users(\r\n    full_name,\r\n    email,\r\n    phone,\r\n    password,\r\n    dept_id,\r\n    role_id\r\n) values (\r\n    'Admin', \r\n    'admin@gmrit.edu',\r\n    '9876543210',  -- Need to change \r\n    'admin123',    -- Need to change\r\n    10,\r\n    1\r\n);",
        "-- Admin\r\ninsert into Users(\r\n    user_id,\r\n    full_name,\r\n    email,\r\n    phone,\r\n    password\r\n) values (\r\n    1,\r\n    'Admin', \r\n    'admin@gmrit.edu',\r\n    '9876543210',  -- Need to change \r\n    'admin123'     -- Need to change\r\n);\r\n\r\ninsert into User_Roles(user_id, role_id, dept_id) values (1, 1, 10);",
        $temp_sql
    );

    // If the replace failed due to \r\n issues, try with \n
    if (strpos($temp_sql, 'User_Roles') === false) {
        $temp_sql = str_replace(
            "last_login timestamp null,\n        dept_id int not null,\n        foreign key(dept_id) references Dept(dept_id),\n        role_id int not null,\n        foreign key(role_id) references Roles(role_id)\n    );",
            "last_login timestamp null\n    );\n\n-- User_Roles (Mapping table for multiple roles)\n    create table if not exists User_Roles(\n        user_role_id int primary key auto_increment,\n        user_id int not null,\n        role_id int not null,\n        dept_id int not null,\n        foreign key(user_id) references Users(user_id),\n        foreign key(role_id) references Roles(role_id),\n        foreign key(dept_id) references Dept(dept_id),\n        unique(user_id, role_id, dept_id)\n    );",
            $temp_sql
        );
        $temp_sql = str_replace(
            "-- Admin\ninsert into Users(\n    full_name,\n    email,\n    phone,\n    password,\n    dept_id,\n    role_id\n) values (\n    'Admin', \n    'admin@gmrit.edu',\n    '9876543210',  -- Need to change \n    'admin123',    -- Need to change\n    10,\n    1\n);",
            "-- Admin\ninsert into Users(\n    user_id,\n    full_name,\n    email,\n    phone,\n    password\n) values (\n    1,\n    'Admin', \n    'admin@gmrit.edu',\n    '9876543210',  -- Need to change \n    'admin123'     -- Need to change\n);\n\ninsert into User_Roles(user_id, role_id, dept_id) values (1, 1, 10);",
            $temp_sql
        );
    }

    $final_sql = "-- ==========================================\n";
    $final_sql .= "-- NORMALIZED SCHEMA (NEW)\n";
    $final_sql .= "-- ==========================================\n\n";
    $final_sql .= $temp_sql . "\n\n";
    $final_sql .= $legacy_sql;

    file_put_contents($output_path, $final_sql);
    echo "Fixed master.sql generated successfully with length: " . strlen($final_sql) . "\n";
} else {
    echo "Source files missing!\n";
}
?>
