<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Upload and Download with PHP & MySQL</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 50px;
        }
        h1 {
            color: #333;
        }
        form {
            margin-bottom: 30px;
        }
        input[type="file"] {
            margin-bottom: 10px;
        }
        button {
            padding: 10px 20px;
            background-color: #28a745;
            color: #fff;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background-color: #218838;
        }
        .file-list {
            margin-top: 30px;
        }
        .file-list ul {
            list-style-type: none;
            padding: 0;
        }
        .file-list li {
            margin-bottom: 10px;
        }
        .file-list a {
            text-decoration: none;
            color: #007bff;
        }
        .file-list a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <h1>Upload Files</h1>
    <form action="index.php" method="POST" enctype="multipart/form-data">
        <label for="username">Username:</label><br>
        <input type="text" id="username" name="username" required><br><br>

        <label for="email">Email:</label><br>
        <input type="email" id="email" name="email" required><br><br>

        <label for="file">Choose files to upload:</label><br>
        <input type="file" id="file" name="files[]" multiple required><br><br>

        <button type="submit" name="upload">Upload</button>
    </form>

    <h1>Download Files</h1>
    <div class="file-list">
        <ul>
            <?php
            // Database connection
            $conn = new mysqli("localhost", "root", "your_secure_password", "file_uploads");

            // Check connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Fetch files from the database
            $sql = "SELECT * FROM files";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<li><a href='".$row['filepath']."' download>".$row['filename']."</a></li>";
                }
            } else {
                echo "<li>No files found</li>";
            }

            $conn->close();
            ?>
        </ul>
    </div>

    <?php
    if (isset($_POST['upload'])) {
        $username = $_POST['username'];
        $email = $_POST['email'];
        $targetDir = "uploads/";

        // Create directory if it doesn't exist
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $conn = new mysqli("localhost", "root", "your_secure_password", "file_uploads");

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        foreach ($_FILES['files']['name'] as $key => $filename) {
            $filepath = $targetDir . basename($filename);
            if (move_uploaded_file($_FILES['files']['tmp_name'][$key], $filepath)) {
                $sql = "INSERT INTO files (username, email, filename, filepath) VALUES ('$username', '$email', '$filename', '$filepath')";
                if ($conn->query($sql) !== TRUE) {
                    echo "Error: " . $sql . "<br>" . $conn->error;
                }
            }
        }

        $conn->close();
        header("Location: index.php"); // Refresh to show updated file list
    }
    ?>
</body>
</html>
