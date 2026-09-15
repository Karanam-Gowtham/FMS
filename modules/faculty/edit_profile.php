<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once '../../includes/connection.php';

if (empty($_SESSION['username'])) {
    ?>
    <div class="contain11" style="max-width: 100%; height: 100vh;text-align: center; background-image: linear-gradient(to right, #4CAF50, #81C784); margin: 0px auto; padding: 20px; border-radius: 10px;">
        <h2 class='login-message'>You are not Logged in. Please <a href='../auth/login.php' class='register-link'>Login</a> to edit your profile.</h2>
        <h2>If you're not registered, <a href='../auth/reg.php' class='register-link'>register here</a>.</h2>
    </div>
    <?php
    exit;
}

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
    ?>
    <div class="contain11" style="max-width: 100%; height: 100vh;text-align: center; background-image: linear-gradient(to right, #4CAF50, #81C784); margin: 0px auto; padding: 20px; border-radius: 10px;">
        <h2 class='login-message' style="color:white; margin-top: 100px;">Profile editing is only available for registered Faculty members.</h2>
        <h3 style="color:white;"><a href='../../dashboard.php' style="color: white; text-decoration: underline;">Return to Dashboard</a> or <a href='../auth/logout.php' style="color: white; text-decoration: underline;">Log out</a></h3>
    </div>
    <?php
    exit;
}

// Handle update
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
    $photo_path = $_FILES['photo_path']['name'];
    if ($photo_path) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
        $target_file = $target_dir . uniqid() . "_" . basename($_FILES["photo_path"]["name"]);
        move_uploaded_file($_FILES["photo_path"]["tmp_name"], $target_file);
    } else {
        $target_file = $user['photo_path'];
    }

    $update_query = "UPDATE reg_tab 
        SET faculty_name=?, designation=?, qualification=?, dept=?, pern_no=?, dob=?, gender=?, address=?, email=?, aadhar=?, pan=?, phone=?, experience=?, password=?, photo_path=? 
        WHERE userid=?";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param(
        "ssssssssssssssss",
        $faculty_name, $designation, $qualification,$dept, $pern_no, $dob, $gender, $address, $email,
        $aadhar, $pan, $phone, $experience, $password, $target_file, $username
    );

    if ($update_stmt->execute()) {
        echo "<script>alert('Profile updated successfully!'); window.location.href='edit_profile.php';</script>";
    } else {
        echo "<script>alert('Failed to update profile. Try again.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <style>
        body { 
            font-family: 'Inter', Arial, sans-serif; 
            background: #0f172a; 
            color: #e2e8f0;
            margin: 0;
            background-image: radial-gradient(circle at top right, #1e293b, #0f172a);
            min-height: 100vh;
        }
        .container11 {
            max-width: 700px; 
            margin: 60px auto; 
            padding: 40px;
            background: rgba(30, 41, 59, 0.7); 
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px; 
            box-shadow: 0 10px 40px rgba(0,0,0,0.5);
            backdrop-filter: blur(12px);
        }
        h1 { 
            text-align: center; 
            color: #38bdf8; 
            margin-bottom: 30px; 
            font-weight: 700;
            letter-spacing: -0.5px;
        }
        .profile-image { 
            display: block; 
            margin: 0 auto 35px; 
            width: 140px; 
            height: 140px; 
            border-radius: 50%; 
            object-fit: cover;
            border: 4px solid #38bdf8;
            box-shadow: 0 8px 25px rgba(56, 189, 248, 0.3);
            background: #1e293b;
        }
        label { 
            font-weight: 600; 
            color: #94a3b8;
            display: block;
            margin-bottom: 8px;
            font-size: 0.9em;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        input, select, textarea {
            width: 100%; 
            padding: 14px 16px; 
            margin-bottom: 24px; 
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.1); 
            border-radius: 8px;
            color: #f8fafc;
            box-sizing: border-box;
            transition: all 0.3s ease;
            font-size: 1em;
        }
        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: #38bdf8;
            box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.2);
            background: rgba(15, 23, 42, 0.8);
        }
        button { 
            background: linear-gradient(135deg, #38bdf8, #2563eb); 
            color: white; 
            padding: 16px 24px; 
            border: none; 
            border-radius: 8px; 
            cursor: pointer; 
            width: 100%;
            font-size: 1.1em;
            font-weight: 600;
            transition: all 0.3s ease;
            margin-top: 10px;
        }
        button:hover { 
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(37, 99, 235, 0.4);
        }
        select option {
            background: #1e293b;
            color: #f1f5f9;
        }
    </style>
</head>
<body>
    <?php include "../../includes/header.php"; ?>
    <div class="container11">
        <h1>Edit Profile</h1>
        <?php
        $photo_path = htmlspecialchars($user['photo_path']);
        $fallback_url = 'https://ui-avatars.com/api/?name=' . urlencode($user['faculty_name']) . '&background=random&color=fff&size=150';
        $actual_src = $photo_path ? "../../" . $photo_path : $fallback_url;
        echo "<img src='" . $actual_src . "' class='profile-image' onerror=\"this.onerror=null; this.src='" . $fallback_url . "';\">";
        ?>
        <form method="post" enctype="multipart/form-data">
            <label>Name:</label>
            <input type="text" name="faculty_name" value="<?= htmlspecialchars($user['faculty_name']); ?>" required>

            <label>Designation:</label>
            <input type="text" name="designation" value="<?= htmlspecialchars($user['designation']); ?>" required>

            <label for="qualification">Highest Qualification:</label>
                <select name="qualification" required>
                    <option value="">Select Qualification</option>
                    <option value="B.Sc" <?= ($user['qualification']=="B.Sc"?"selected":"") ?>>B.Sc</option>
                    <option value="B.Com" <?= ($user['qualification']=="B.Com"?"selected":"") ?>>B.Com</option>
                    <option value="B.A" <?= ($user['qualification']=="B.A"?"selected":"") ?>>B.A</option>
                    <option value="B.Tech" <?= ($user['qualification']=="B.Tech"?"selected":"") ?>>B.Tech</option>
                    <option value="M.Sc" <?= ($user['qualification']=="M.Sc"?"selected":"") ?>>M.Sc</option>
                    <option value="M.Com" <?= ($user['qualification']=="M.Com"?"selected":"") ?>>M.Com</option>
                    <option value="M.A" <?= ($user['qualification']=="M.A"?"selected":"") ?>>M.A</option>
                    <option value="M.Tech" <?= ($user['qualification']=="M.Tech"?"selected":"") ?>>M.Tech</option>
                    <option value="MBA" <?= ($user['qualification']=="MBA"?"selected":"") ?>>MBA</option>
                    <option value="MCA" <?= ($user['qualification']=="MCA"?"selected":"") ?>>MCA</option>
                    <option value="Ph.D" <?= ($user['qualification']=="Ph.D"?"selected":"") ?>>Ph.D</option>
                    <option value="Post Doctorate" <?= ($user['qualification']=="Post Doctorate"?"selected":"") ?>>Post Doctorate</option>
                    <option value="Other" <?= ($user['qualification']=="Other"?"selected":"") ?>>Other</option>
                </select>

                <label for="dept">Department:</label>
                <select name="dept" required>
                    <option value="">Select Department</option>
                    <option value="CSE-AI&DS" <?= ($user['dept']=="CSE-AI&DS"?"selected":"") ?>>CSE-AI&DS</option>
                    <option value="CSE-AI&ML" <?= ($user['dept']=="CSE-AI&ML"?"selected":"") ?>>CSE-AI&ML</option>
                    <option value="CSE" <?= ($user['dept']=="CSE"?"selected":"") ?>>CSE</option>
                    <option value="CSE-CS" <?= ($user['dept']=="CSE-CS"?"selected":"") ?>>CSE-CS</option>
                    <option value="CIVIL" <?= ($user['dept']=="CIVIL"?"selected":"") ?>>CIVIL</option>
                    <option value="MatheMatics" <?= ($user['dept']=="MatheMatics"?"selected":"") ?>>MatheMatics</option>
                    <option value="Physics" <?= ($user['dept']=="Physics"?"selected":"") ?>>Physics</option>
                    <option value="Chemistry" <?= ($user['dept']=="Chemistry"?"selected":"") ?>>Chemistry</option>
                    <option value="BSH" <?= ($user['dept']=="BSH"?"selected":"") ?>>BSH</option>
                    <option value="MECH" <?= ($user['dept']=="MECH"?"selected":"") ?>>MECH</option>
                    <option value="EEE" <?= ($user['dept']=="EEE"?"selected":"") ?>>EEE</option>
                    <option value="ECE" <?= ($user['dept']=="ECE"?"selected":"") ?>>ECE</option>
                    <option value="IT" <?= ($user['dept']=="IT"?"selected":"") ?>>IT</option>
                </select>



            <label>PERN Number:</label>
            <input type="text" name="pern_no" value="<?= htmlspecialchars($user['pern_no']); ?>" required>

            <label>Date of Birth:</label>
            <input type="date" name="dob" value="<?= htmlspecialchars($user['dob']); ?>" required>

            <label>Gender:</label>
            <select name="gender" required>
                <option value="Male" <?= ($user['gender']=="Male"?"selected":"") ?>>Male</option>
                <option value="Female" <?= ($user['gender']=="Female"?"selected":"") ?>>Female</option>
                <option value="Other" <?= ($user['gender']=="Other"?"selected":"") ?>>Other</option>
            </select>

            <label>Email:</label>
            <input type="email" name="email" value="<?= htmlspecialchars($user['email']); ?>" required>

            <label>Aadhar:</label>
            <input type="text" name="aadhar" value="<?= htmlspecialchars($user['aadhar']); ?>" required>

            <label>PAN:</label>
            <input type="text" name="pan" value="<?= htmlspecialchars($user['pan']); ?>" required>

            <label>Phone:</label>
            <input type="text" name="phone" value="<?= htmlspecialchars($user['phone']); ?>" required>

            <label>Address:</label>
            <textarea name="address" required><?= htmlspecialchars($user['address']); ?></textarea>

            <label>Experience:</label>
            <textarea name="experience"><?= htmlspecialchars($user['experience']); ?></textarea>

            <label>Password:</label>
            <input type="input" name="password" value="<?= htmlspecialchars($user['password']); ?>" required>
            
            <label>Upload New Photo:</label>
            <input type="file" name="photo_path" accept="image/*">

            <button type="submit">Update Profile</button>
        </form>
    </div>
</body>
</html>
