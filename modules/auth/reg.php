<?php
    include("../../includes/connection.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty Registration</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-image: url('../../assets/img/gmr_landing_page.jpg');
            background-size: cover;
            background-position: center;
            display: flex;
            justify-content: center;
            margin-top: 0px;
        }
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height:159vh;
            background: rgba(0, 0, 0, 0.6);
            z-index: -1;
        }

        .registration-form {
            margin-top: 100px;
            background: rgba(0, 0, 0, 0.7);
            padding: 25px;
            width: 700px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            animation: fadeIn 1.5s;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .registration-form h2 {
            text-align: center;
            color:rgb(93, 162, 215);
            font-size: 1.8em;
            margin-bottom: 20px;
        }

        .registration-form label {
            font-weight: bold;
            color: white;
        }

        .registration-form input,
        .registration-form select,
        .registration-form textarea {
            width: 100%;
            padding: 10px;
            margin: 8px 0 15px 0;
            border: 1px solid #ddd;
            border-radius: 6px;
            box-sizing: border-box;
        }
        input[type="file"] {
            background-color: white;
            color: black;
        }

        .registration-form button {
            margin-left:150px;
            width: 50%;
            padding: 12px;
            background: linear-gradient(135deg,green,yellow);
            color: #fff;
            font-weight: bold;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }
    </style>
</head>
<?php include '../../includes/header.php'; ?>
<body>
<div class="registration-form">
    <h2>Faculty Registration</h2>
    <form method="post" action="" enctype="multipart/form-data">

        <label for="faculty_name">Faculty Name:</label>
        <input type="text" name="faculty_name" required>

        <label for="designation">Designation:</label>
        <input type="text" name="designation" required>

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
            <option value="AIDS" <?php if (isset($_POST['dept']) && $_POST['dept'] == 'AIDS') echo 'selected'; ?>>AIDS</option>
            <option value="AIML" <?php if (isset($_POST['dept']) && $_POST['dept'] == 'AIML') echo 'selected'; ?>>AIML</option>
            <option value="CSE" <?php if (isset($_POST['dept']) && $_POST['dept'] == 'CSE') echo 'selected'; ?>>CSE</option>
            <option value="CIVIL" <?php if (isset($_POST['dept']) && $_POST['dept'] == 'CIVIL') echo 'selected'; ?>>CIVIL</option>
            <option value="MECH" <?php if (isset($_POST['dept']) && $_POST['dept'] == 'MECH') echo 'selected'; ?>>MECH</option>
            <option value="EEE" <?php if (isset($_POST['dept']) && $_POST['dept'] == 'EEE') echo 'selected'; ?>>EEE</option>
            <option value="ECE" <?php if (isset($_POST['dept']) && $_POST['dept'] == 'ECE') echo 'selected'; ?>>ECE</option>
            <option value="IT" <?php if (isset($_POST['dept']) && $_POST['dept'] == 'IT') echo 'selected'; ?>>IT</option>
            <option value="BSH" <?php if (isset($_POST['dept']) && $_POST['dept'] == 'BSH') echo 'selected'; ?>>BSH</option>
        </select>

        <label for="doj">Date of Joining:</label>
        <input type="date" name="doj" required>

        <label for="pern">PERN No:</label>
        <input type="text" name="pern" required>

        <label for="dob">Date of Birth:</label>
        <input type="date" name="dob" required>

        <label for="gender">Gender:</label>
        <select name="gender" required>
            <option value="">Select Gender</option>
            <option value="Male">Male</option>
            <option value="Female">Female</option>
            <option value="Other">Other</option>
        </select>

        <label for="address">Address:</label>
        <input type="text" name="address" required>

        <label for="email">Mail ID:</label>
        <input type="email" name="email" id="email" required oninput="document.getElementById('username').value=this.value">

        <label for="aadhar">Aadhar Card Number:</label>
        <input type="text" name="aadhar" pattern="\d{12}" required title="Aadhar should be 12 digits">

        <label for="pan">PAN Card Number:</label>
        <input type="text" name="pan" pattern="[A-Z]{5}[0-9]{4}[A-Z]" required title="PAN should be 10 characters">

        <label for="username">Username:</label>
        <input type="text" name="username" id="username" readonly required>

        <label for="password">Password:</label>
        <input type="password" name="password" required>

        <label for="conf_password">Confirm Password:</label>
        <input type="password" name="conf_password" required>

        <label for="phone">Contact No:</label>
        <input type="text" name="phone" pattern="\d{10}" required title="Phone number should be 10 digits">

        <label for="experience">Experience outside the GMRIT:</label>
        <textarea name="experience" rows="4"></textarea>

        <label for="exp_cert">Experience Certificate (PDF):</label>
        <input type="file" name="exp_cert" accept="application/pdf" required>

        <label for="edu_cert">Certificates (SSC to M.Tech/Ph.D. in one PDF):</label>
        <input type="file" name="edu_cert" accept="application/pdf" required>

        <label for="photo">Picture:</label>
        <input type="file" name="photo" accept="image/*" required>

        <button type="submit" name="register">Register</button>
    </form>
</div>

<?php
if (isset($_POST['register'])) {
    $faculty_name = $_POST['faculty_name'];
    $designation = $_POST['designation'];
    $qualification = $_POST['qualification'];
    $pern = $_POST['pern'];
    $dob = $_POST['dob'];
    $doj = $_POST['doj'];
    $gender = $_POST['gender'];
    $address = $_POST['address'];
    $email = $_POST['email'];
    $aadhar = $_POST['aadhar'];
    $pan = $_POST['pan'];
    $dept = $_POST['dept'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $conf_password = $_POST['conf_password'];
    $phone = $_POST['phone'];
    $experience = $_POST['experience'];


    // Handle Experience Certificate PDF
        $exp_cert = $_FILES['exp_cert'];
        $exp_cert_name = basename($exp_cert['name']);
        $exp_cert_file = "../../uploads/" . uniqid() . "_exp_" . $exp_cert_name;

        if (!move_uploaded_file($exp_cert['tmp_name'], $exp_cert_file)) {
            echo "<script>alert('Failed to upload Experience Certificate.');</script>";
            exit;
        }

        // Handle Education Certificates PDF
        $edu_cert = $_FILES['edu_cert'];
        $edu_cert_name = basename($edu_cert['name']);
        $edu_cert_file = "../../uploads/" . uniqid() . "_edu_" . $edu_cert_name;

        if (!move_uploaded_file($edu_cert['tmp_name'], $edu_cert_file)) {
            echo "<script>alert('Failed to upload Education Certificates.');</script>";
            exit;
        }


    if ($password != $conf_password) {
        echo "<script>alert('Password and Confirm Password should be equal!');</script>";
    } else {
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Handle file upload
        $photo = $_FILES['photo'];
        $photo_name = basename($photo['name']);
        $target_dir = "../../uploads/";
        $target_file = $target_dir . uniqid() . "_" . $photo_name;

        if (move_uploaded_file($photo['tmp_name'], $target_file)) {
            $stmt = $conn->prepare("INSERT INTO reg_tab 
            (faculty_name, designation, qualification, dept, pern_no, dob, gender, address, email, aadhar, pan, userid, password, phone, experience, photo_path, doj, exp_cert_path, edu_cert_path) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

            
            $stmt->bind_param("sssssssssssssssssss", 
            $faculty_name, $designation, $qualification, $dept, $pern, $dob, $gender, $address, 
            $email, $aadhar, $pan, $username, $password, $phone, $experience, $target_file, 
            $doj, $exp_cert_file, $edu_cert_file);

            if ($stmt->execute()) {
                echo "<script>alert('Registration successful!');</script>";
                echo "<script>window.location.href = '../../admin/admins.php';</script>";
            } else {
                echo "<p class='error'>Error: " . $stmt->error . "</p>";
            }
            $stmt->close();
        } else {
            echo "<script>alert('Failed to upload photo. Please try again.');</script>";
        }
        $conn->close();
    }
}
?>

</body>
</html>
