<?php
include_once '../../includes/session.php';
require_once '../../includes/connection.php';

if (empty($_SESSION['username'])) {
    ?>
        <div class="contain11" style="max-width: 100%; height: 100vh;text-align: center; background-image: linear-gradient(to right, #4CAF50, #81C784); margin: 0px auto; padding: 20px; border-radius: 10px;">
            <h2 class='login-message'>You are not Logged in. Please <a href='./admin/admins.php' class='register-link'>Login</a> to edit your profile.</h2>
            <h2>If you're not registered, <a href='./reg.php' class='register-link'>register here</a>.</h2>
        </div>
        <?php
        exit;
}


// Define styles in $extra_head before including header
$extra_head = "
    <link rel='stylesheet' href='../../assets/css/index1.css'>
    <style>
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('../../assets/img/gmr_landing_page.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            margin: 0;
            padding-top: 100px; /* Ensure enough space for header */
            min-height: 100vh;
        }
        .container11 {
            max-width: 500px; 
            margin: 30px auto 50px auto; 
            padding: 40px; 
            background: #ffffff; 
            border-radius: 8px; 
            border: 1px solid #ddd; /* Simple border instead of heavy shadow */
            position: relative;
        }
        h1 { 
            text-align: center; 
            color: #333; 
            margin-bottom: 30px; 
            font-size: 2em;
            border-bottom: 1px solid #eee;
            padding-bottom: 15px;
        }
        .profile-image { 
            display:block; 
            margin:0 auto 30px; 
            width:150px; 
            height:150px; 
            border-radius:50%; 
            object-fit:cover; 
            border: 3px solid #fff;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        label { 
            font-weight: 600; 
            color: #555;
            display: block;
            margin-bottom: 5px;
            margin-top: 15px;
            font-size: 0.95rem;
        }
        input, select, textarea {
            width: 100%; 
            padding: 10px; 
            margin-bottom: 5px; 
            border: 1px solid #ccc; 
            border-radius: 4px;
            box-sizing: border-box; 
            font-size: 1rem;
        }
        input:focus, select:focus, textarea:focus {
            border-color: #2575fc;
            outline: none;
        }
        button[type='submit'] { 
            background-color: #4CAF50; 
            color: white; 
            padding: 12px; 
            border: none; 
            border-radius: 4px; 
            cursor: pointer; 
            width: 100%;
            font-size: 1.1rem;
            font-weight: bold;
            margin-top: 30px;
        }
        button[type='submit']:hover { 
            background-color: #45a049;
        }
        textarea {
            resize: vertical;
            min-height: 100px;
        }
    </style>
";

include "../../includes/header.php";
$username = $_SESSION['username'];

$query = "SELECT faculty_name, designation, qualification, dept, pern_no, dob, gender, address, email, aadhar, pan, phone, experience, password, photo_path, userid 
          FROM reg_tab WHERE userid = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    echo "<p style='text-align:center; margin-top:100px;'>User data not found. Please <a href='../../modules/auth/logout.php'>log in again</a>.</p>";
    exit;
}

// Fetch user data FIRST
$query = "SELECT faculty_name, designation, qualification, dept, pern_no, dob, gender, address, email, aadhar, pan, phone, experience, password, photo_path, userid 
          FROM reg_tab WHERE userid = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    echo "<p style='text-align:center; margin-top:100px;'>User data not found. Please <a href='../../modules/auth/logout.php'>log in again</a>.</p>";
    exit;
}

// NOW handle Update (using $user for defaults if needed)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $faculty_name = $_POST['faculty_name'];
    $designation = $_POST['designation'];
    $qualification = $_POST['qualification'];
    $dept = $_POST['dept'];
    $pern_no = $_POST['pern_no'];
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $address = $_POST['address'];
    $email = $_POST['email'];
    $aadhar = $_POST['aadhar'];
    $pan = $_POST['pan'];
    $phone = $_POST['phone'];
    $experience = $_POST['experience'];
    $password = $_POST['password'];

    // Handle photo
    if (isset($_FILES['photo_path']) && $_FILES['photo_path']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "../../uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $target_file = $target_dir . uniqid() . "_" . basename($_FILES["photo_path"]["name"]);
        if (move_uploaded_file($_FILES["photo_path"]["tmp_name"], $target_file)) {
            // Success
        } else {
            $target_file = $user['photo_path']; // Fallback
        }
    } else {
        $target_file = $user['photo_path'];
    }

    $update_query = "UPDATE reg_tab 
        SET faculty_name=?, designation=?, qualification=?, dept=?, pern_no=?, dob=?, gender=?, address=?, email=?, aadhar=?, pan=?, phone=?, experience=?, password=?, photo_path=? 
        WHERE userid=?";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param(
        "ssssssssssssssss",
        $faculty_name,
        $designation,
        $qualification,
        $dept,
        $pern_no,
        $dob,
        $gender,
        $address,
        $email,
        $aadhar,
        $pan,
        $phone,
        $experience,
        $password,
        $target_file,
        $username
    );

    if ($update_stmt->execute()) {
        echo "<script>alert('Profile updated successfully!'); window.location.href='edit_profile.php';</script>";
        // Update local object to reflect changes immediately if we don't redirect (but we do redirect)
    } else {
        echo "<script>alert('Failed to update profile. Try again.');</script>";
    }
}

?>

    <div class="container11">
        <h1>Edit Profile</h1>
        <?php
        $photo_path = isset($user['photo_path']) ? trim($user['photo_path']) : '';

        // Determine the correct image source
        // Handles paths stored as "uploads/..." (from registration) or "../../uploads/..." (from edits)
        $img_src = "../../assets/img/profile_icon.png"; // Default
        
        if ($photo_path) {
            // If path starts exactly with "uploads/", it's from reg.php, so we need to go up two levels
            if (strpos($photo_path, 'uploads/') === 0) {
                $img_src = "../../" . $photo_path;
            }
            // If it's just a filename (no slashes), assume it's in uploads
            elseif (strpos($photo_path, '/') === false) {
                $img_src = "../../uploads/" . $photo_path;
            }
            // Otherwise assume it's a valid relative path (like ../../uploads/...)
            else {
                $img_src = $photo_path;
            }
        }

        echo "<img src='" . $img_src . "' class='profile-image' alt='Profile Photo' onerror=\"this.src='../../assets/img/profile_icon.png'\">";
        ?>
        <form method="post" enctype="multipart/form-data">
            <label for="faculty_name">Name:</label>
            <input type="text" name="faculty_name" id="faculty_name" value="<?= htmlspecialchars($user['faculty_name']); ?>" required>

            <label for="designation">Designation:</label>
            <input type="text" name="designation" id="designation" value="<?= htmlspecialchars($user['designation']); ?>" required>

            <label for="qualification">Highest Qualification:</label>
                <select name="qualification" id="qualification" required>
                    <option value="">Select Qualification</option>
                    <option value="B.Sc" <?= ($user['qualification'] == "B.Sc" ? "selected" : "") ?>>B.Sc</option>
                    <option value="B.Com" <?= ($user['qualification'] == "B.Com" ? "selected" : "") ?>>B.Com</option>
                    <option value="B.A" <?= ($user['qualification'] == "B.A" ? "selected" : "") ?>>B.A</option>
                    <option value="B.Tech" <?= ($user['qualification'] == "B.Tech" ? "selected" : "") ?>>B.Tech</option>
                    <option value="M.Sc" <?= ($user['qualification'] == "M.Sc" ? "selected" : "") ?>>M.Sc</option>
                    <option value="M.Com" <?= ($user['qualification'] == "M.Com" ? "selected" : "") ?>>M.Com</option>
                    <option value="M.A" <?= ($user['qualification'] == "M.A" ? "selected" : "") ?>>M.A</option>
                    <option value="M.Tech" <?= ($user['qualification'] == "M.Tech" ? "selected" : "") ?>>M.Tech</option>
                    <option value="MBA" <?= ($user['qualification'] == "MBA" ? "selected" : "") ?>>MBA</option>
                    <option value="MCA" <?= ($user['qualification'] == "MCA" ? "selected" : "") ?>>MCA</option>
                    <option value="Ph.D" <?= ($user['qualification'] == "Ph.D" ? "selected" : "") ?>>Ph.D</option>
                    <option value="Post Doctorate" <?= ($user['qualification'] == "Post Doctorate" ? "selected" : "") ?>>Post Doctorate</option>
                    <option value="Other" <?= ($user['qualification'] == "Other" ? "selected" : "") ?>>Other</option>
                </select>

                <label for="dept">Department:</label>
                <select name="dept" id="dept" required>
                    <option value="">Select Department</option>
                    <option value="AIDS" <?= ($user['dept'] == "AIDS" ? "selected" : "") ?>>AIDS</option>
                    <option value="AIML" <?= ($user['dept'] == "AIML" ? "selected" : "") ?>>AIML</option>
                    <option value="CSE" <?= ($user['dept'] == "CSE" ? "selected" : "") ?>>CSE</option>
                    <option value="CIVIL" <?= ($user['dept'] == "CIVIL" ? "selected" : "") ?>>CIVIL</option>
                    <option value="MECH" <?= ($user['dept'] == "MECH" ? "selected" : "") ?>>MECH</option>
                    <option value="EEE" <?= ($user['dept'] == "EEE" ? "selected" : "") ?>>EEE</option>
                    <option value="ECE" <?= ($user['dept'] == "ECE" ? "selected" : "") ?>>ECE</option>
                    <option value="IT" <?= ($user['dept'] == "IT" ? "selected" : "") ?>>IT</option>
                    <option value="BSH" <?= ($user['dept'] == "BSH" ? "selected" : "") ?>>BSH</option>
                </select>



            <label for="pern_no">PERN Number:</label>
            <input type="text" name="pern_no" id="pern_no" value="<?= htmlspecialchars($user['pern_no']); ?>" required>

            <label for="dob">Date of Birth:</label>
            <input type="date" name="dob" id="dob" value="<?= htmlspecialchars($user['dob']); ?>" required>

            <label for="gender">Gender:</label>
            <select name="gender" id="gender" required>
                <option value="Male" <?= ($user['gender'] == "Male" ? "selected" : "") ?>>Male</option>
                <option value="Female" <?= ($user['gender'] == "Female" ? "selected" : "") ?>>Female</option>
                <option value="Other" <?= ($user['gender'] == "Other" ? "selected" : "") ?>>Other</option>
            </select>

            <label for="email">Email:</label>
            <input type="email" name="email" id="email" value="<?= htmlspecialchars($user['email']); ?>" required>

            <label for="aadhar">Aadhar:</label>
            <input type="text" name="aadhar" id="aadhar" value="<?= htmlspecialchars($user['aadhar']); ?>" required>

            <label for="pan">PAN:</label>
            <input type="text" name="pan" id="pan" value="<?= htmlspecialchars($user['pan']); ?>" required>

            <label for="phone">Phone:</label>
            <input type="text" name="phone" id="phone" value="<?= htmlspecialchars($user['phone']); ?>" required>

            <label for="address">Address:</label>
            <textarea name="address" id="address" required><?= htmlspecialchars($user['address']); ?></textarea>

            <label for="experience">Experience:</label>
            <textarea name="experience" id="experience"><?= htmlspecialchars($user['experience']); ?></textarea>

            <label for="password">Password:</label>
            <input type="text" name="password" id="password" value="<?= htmlspecialchars($user['password']); ?>" required>
            
            <label for="photo">Upload New Photo:</label>
            <input type="file" name="photo_path" id="photo" accept="image/*">

            <button type="submit">Update Profile</button>
        </form>
    </div>
</body>
</html>
