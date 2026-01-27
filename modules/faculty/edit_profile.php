<?php
session_start();
require_once 'connection.php';

if (empty($_SESSION['username'])) {
    ?>
    <div class="contain11" style="max-width: 100%; height: 100vh;text-align: center; background-image: linear-gradient(to right, #4CAF50, #81C784); margin: 0px auto; padding: 20px; border-radius: 10px;">
        <h2 class='login-message'>You are not Logged in. Please <a href='./admin/admins.php' class='register-link'>Login</a> to edit your profile.</h2>
        <h2>If you're not registered, <a href='./reg.php' class='register-link'>register here</a>.</h2>
    </div>
    <?php
    exit;
}

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
    echo "<p>User data not found. Please <a href='./logout.php'>log in again</a>.</p>";
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
        $target_dir = "../../uploads/";
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
        body { font-family: Arial, sans-serif; background: linear-gradient(135deg, #8f69b8, #2575fc); }
        .container11 {
            max-width: 800px; margin: 50px auto; padding: 20px;
            background: #fff; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1 { text-align: center; color: #4CAF50; margin-bottom: 20px; }
        .profile-image { display:block; margin:0 auto 20px; width:150px; height:150px; border-radius:50%; object-fit:cover; }
        label { font-weight: bold; }
        input, select, textarea {
            width: 97%; padding: 10px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 5px;
        }
        button { background: #4CAF50; color: white; padding: 12px; border: none; border-radius: 5px; cursor: pointer; }
        button:hover { background: #45a049; }
        header{
            margin-top:-50px;
            margin-left:-8px;
        }
    </style>
</head>
<body>
    <div class="container11">
        <h1>Edit Profile</h1>
        <?php
        $photo_path = htmlspecialchars($user['photo_path']);
        echo "<img src='" . ($photo_path ?: "../../uploads/default_pic.png") . "' class='profile-image'>";
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
                    <option value="AIDS" <?= ($user['dept']=="AIDS"?"selected":"") ?>>AIDS</option>
                    <option value="AIML" <?= ($user['dept']=="AIML"?"selected":"") ?>>AIML</option>
                    <option value="CSE" <?= ($user['dept']=="CSE"?"selected":"") ?>>CSE</option>
                    <option value="CIVIL" <?= ($user['dept']=="CIVIL"?"selected":"") ?>>CIVIL</option>
                    <option value="MECH" <?= ($user['dept']=="MECH"?"selected":"") ?>>MECH</option>
                    <option value="EEE" <?= ($user['dept']=="EEE"?"selected":"") ?>>EEE</option>
                    <option value="ECE" <?= ($user['dept']=="ECE"?"selected":"") ?>>ECE</option>
                    <option value="IT" <?= ($user['dept']=="IT"?"selected":"") ?>>IT</option>
                    <option value="BSH" <?= ($user['dept']=="BSH"?"selected":"") ?>>BSH</option>
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
