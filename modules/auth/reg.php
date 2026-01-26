<?php
    include("../../includes/connection.php");
?>
<?php
    include("../../includes/connection.php");

    // Handle Registration Logic
    $message = "";
    $msg_type = "";
    
    if (isset($_POST['register'])) {
        $faculty_name = trim($_POST['faculty_name']);
        $designation = trim($_POST['designation']);
        $qualification = trim($_POST['qualification']);
        $pern = trim($_POST['pern']);
        $dob = $_POST['dob'];
        $doj = $_POST['doj'];
        $gender = $_POST['gender'];
        $address = trim($_POST['address']);
        $email = trim($_POST['email']);
        $aadhar = trim($_POST['aadhar']);
        $pan = trim($_POST['pan']);
        $dept = $_POST['dept'];
        $username = trim($_POST['username']);
        $password = $_POST['password'];
        $conf_password = $_POST['conf_password'];
        $phone = trim($_POST['phone']);
        $experience = trim($_POST['experience']);

        // Define directories
        $upload_dir_fs = "../../uploads/"; // File system path for moving files
        $upload_dir_db = "uploads/";       // Database path

        // Improved Login for File Upload with Debugging
        function handleUploadDebug($fileInputName, $prefix, $upload_dir_fs, $upload_dir_db, &$errors) {
            if (!isset($_FILES[$fileInputName])) {
                $errors[] = "File input '$fileInputName' is missing.";
                return false;
            }
            
            $file = $_FILES[$fileInputName];
            
            if ($file['error'] != UPLOAD_ERR_OK) {
                // ... error mapping ...
                $errors[] = "Upload error for '$fileInputName' code: " . $file['error'];
                return false;
            }

            $filename = basename($file['name']);
            $filename = preg_replace("/[^a-zA-Z0-9\._-]/", "", $filename);
            
            $unique_name = uniqid() . "_" . $prefix . "_" . $filename;
            $target_fs = $upload_dir_fs . $unique_name;
            $target_db = $upload_dir_db . $unique_name;

            if (move_uploaded_file($file['tmp_name'], $target_fs)) {
                return $target_db;
            } else {
                $errors[] = "Failed to move file '$fileInputName' to '$target_fs'";
                return false;
            }
        }

        $errors = [];

        if (empty($faculty_name) || empty($username) || empty($password)) {
            $message = "Please fill in all required fields.";
            $msg_type = "alert-danger";
        } elseif ($password != $conf_password) {
            $message = "Password and Confirm Password do not match!";
            $msg_type = "alert-danger";
        } else {
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Upload files with debug
            $exp_cert_db = handleUploadDebug('exp_cert', 'exp', $upload_dir_fs, $upload_dir_db, $errors);
            $edu_cert_db = handleUploadDebug('edu_cert', 'edu', $upload_dir_fs, $upload_dir_db, $errors);
            $photo_db = handleUploadDebug('photo', 'photo', $upload_dir_fs, $upload_dir_db, $errors);

            if ($exp_cert_db && $edu_cert_db && $photo_db) {
                $stmt = $conn->prepare("INSERT INTO reg_tab 
                (faculty_name, designation, qualification, dept, pern_no, dob, gender, address, email, aadhar, pan, userid, password, phone, experience, photo_path, doj, exp_cert_path, edu_cert_path) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

                if ($stmt) {
                    $stmt->bind_param("sssssssssssssssssss", 
                    $faculty_name, $designation, $qualification, $dept, $pern, $dob, $gender, $address, 
                    $email, $aadhar, $pan, $username, $password, $phone, $experience, $photo_db, 
                    $doj, $exp_cert_db, $edu_cert_db);

                    if ($stmt->execute()) {
                        $message = "Registration successful! You will be redirected shortly.";
                        $msg_type = "alert-success";
                        echo "<script>
                            setTimeout(function() {
                                window.location.href = '../../admin/admins.php';
                            }, 2000);
                        </script>";
                    } else {
                        $message = "Database Insert Error: " . $stmt->error;
                        $msg_type = "alert-danger";
                    }
                    $stmt->close();
                } else {
                    $message = "Database Prepare Error: " . $conn->error;
                    $msg_type = "alert-danger";
                }
            } else {
                $message = "File Upload Failed:<br>" . implode("<br>", $errors);
                $msg_type = "alert-danger";
            }
            $conn->close();
        }
    }

    // Prepare Extra Head CSS for header.php
    $extra_head = "
    <style>
        /* Override body background to match visual design */
        body {
            font-family: Arial, sans-serif;
            background-image: url('../../assets/img/gmr_landing_page.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            min-height: 100vh;
        }

        /* Dark overlay for readability */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            z-index: -1;
        }

        .registration-container {
            display: flex;
            justify-content: center;
            align-items: center;
            padding-top: 120px; /* Space for fixed header */
            padding-bottom: 50px;
        }

        .registration-form {
            background: rgba(0, 0, 0, 0.85);
            padding: 30px;
            width: 90%;
            max-width: 700px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.5);
            color: white;
            animation: fadeIn 0.8s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .registration-form h2 {
            text-align: center;
            color: #60a5fa;
            font-size: 2em;
            margin-bottom: 25px;
        }

        .registration-form label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #e5e7eb;
        }

        .registration-form input,
        .registration-form select,
        .registration-form textarea {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #555;
            background-color: rgba(255, 255, 255, 0.95);
            border-radius: 6px;
            box-sizing: border-box;
            font-size: 16px;
        }

        .registration-form input:focus, .registration-form select:focus, .registration-form textarea:focus {
            outline: none;
            border-color: #60a5fa;
            box-shadow: 0 0 5px rgba(96, 165, 250, 0.5);
        }

        .registration-form button {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #10b981, #059669);
            color: #fff;
            font-weight: bold;
            font-size: 18px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 10px;
        }

        .registration-form button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.4);
        }

        .alert-box {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 6px;
            display: none;
        }
        .alert-success { background-color: #d1fae5; color: #065f46; border: 1px solid #34d399; }
        .alert-danger { background-color: #fee2e2; color: #991b1b; border: 1px solid #f87171; }
    </style>
    ";

    include '../../includes/header.php'; 
?>

<div class="registration-container">
    <div class="registration-form">
        <h2>Faculty Registration</h2>
        
        <?php if (!empty($message)): ?>
            <div class="alert-box <?php echo $msg_type; ?>" style="display: block;">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form method="post" action="" enctype="multipart/form-data">
            
            <label for="faculty_name">Faculty Name:</label>
            <input type="text" name="faculty_name" required value="<?php echo isset($_POST['faculty_name']) ? htmlspecialchars($_POST['faculty_name']) : ''; ?>">

            <label for="designation">Designation:</label>
            <input type="text" name="designation" required value="<?php echo isset($_POST['designation']) ? htmlspecialchars($_POST['designation']) : ''; ?>">

            <label for="qualification">Highest Qualification:</label>
            <select name="qualification" required>
                <option value="">Select Qualification</option>
                <option value="B.Sc">B.Sc</option>
                <option value="B.Com">B.Com</option>
                <option value="B.A">B.A</option>
                <option value="B.Tech">B.Tech</option>
                <option value="M.Sc">M.Sc</option>
                <option value="M.Com">M.Com</option>
                <option value="M.A">M.A</option>
                <option value="M.Tech">M.Tech</option>
                <option value="MBA">MBA</option>
                <option value="MCA">MCA</option>
                <option value="Ph.D">Ph.D</option>
                <option value="Post Doctorate">Post Doctorate</option>
                <option value="Other">Other</option>
            </select>

            <label for="dept">Department:</label>
            <select name="dept" required>
                <option value="">Select Department</option>
                <option value="AIDS">AIDS</option>
                <option value="AIML">AIML</option>
                <option value="CSE">CSE</option>
                <option value="CIVIL">CIVIL</option>
                <option value="MECH">MECH</option>
                <option value="EEE">EEE</option>
                <option value="ECE">ECE</option>
                <option value="IT">IT</option>
                <option value="BSH">BSH</option>
            </select>

            <label for="doj">Date of Joining:</label>
            <input type="date" name="doj" required value="<?php echo isset($_POST['doj']) ? $_POST['doj'] : ''; ?>">

            <label for="pern">PERN No:</label>
            <input type="text" name="pern" required value="<?php echo isset($_POST['pern']) ? htmlspecialchars($_POST['pern']) : ''; ?>">

            <label for="dob">Date of Birth:</label>
            <input type="date" name="dob" required value="<?php echo isset($_POST['dob']) ? $_POST['dob'] : ''; ?>">

            <label for="gender">Gender:</label>
            <select name="gender" required>
                <option value="">Select Gender</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
                <option value="Other">Other</option>
            </select>

            <label for="address">Address:</label>
            <input type="text" name="address" required value="<?php echo isset($_POST['address']) ? htmlspecialchars($_POST['address']) : ''; ?>">

            <label for="email">Mail ID:</label>
            <input type="email" name="email" id="email" required oninput="document.getElementById('username').value=this.value" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">

            <label for="aadhar">Aadhar Card Number:</label>
            <input type="text" name="aadhar" pattern="\d{12}" required title="Aadhar should be 12 digits" value="<?php echo isset($_POST['aadhar']) ? htmlspecialchars($_POST['aadhar']) : ''; ?>">

            <label for="pan">PAN Card Number:</label>
            <input type="text" name="pan" pattern="[A-Z]{5}[0-9]{4}[A-Z]" required title="PAN should be 10 characters" value="<?php echo isset($_POST['pan']) ? htmlspecialchars($_POST['pan']) : ''; ?>">

            <label for="username">Username:</label>
            <input type="text" name="username" id="username" readonly required value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">

            <label for="password">Password:</label>
            <input type="password" name="password" required>

            <label for="conf_password">Confirm Password:</label>
            <input type="password" name="conf_password" required>

            <label for="phone">Contact No:</label>
            <input type="text" name="phone" pattern="\d{10}" required title="Phone number should be 10 digits" value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>">

            <label for="experience">Experience outside the GMRIT:</label>
            <textarea name="experience" rows="4"><?php echo isset($_POST['experience']) ? htmlspecialchars($_POST['experience']) : ''; ?></textarea>

            <label for="exp_cert">Experience Certificate (PDF):</label>
            <input type="file" name="exp_cert" accept="application/pdf" required>

            <label for="edu_cert">Certificates (SSC to M.Tech/Ph.D. in one PDF):</label>
            <input type="file" name="edu_cert" accept="application/pdf" required>

            <label for="photo">Picture:</label>
            <input type="file" name="photo" accept="image/*" required>

            <button type="submit" name="register">Register</button>
        </form>
    </div>
</div>

