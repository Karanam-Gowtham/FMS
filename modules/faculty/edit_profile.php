<?php
require_once '../../config.php';
require_once CONNECTION_PATH;
require_once INCLUDES_PATH . '/helpers.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isLoggedIn() && empty($_SESSION['logged_in']) && empty($_SESSION['username']) && empty($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "/modules/auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'] ?? null;
$identifier = $_SESSION['user_identifier'] ?? '';
$email = $_SESSION['email'] ?? '';
$uname = $_SESSION['username'] ?? '';
$full_name = $_SESSION['full_name'] ?? '';

// Try to find user in reg_tab first
$stmt = $conn->prepare("
    SELECT faculty_name, designation, qualification, dept, pern_no, dob, gender, address, email, aadhar, pan, phone, experience, password, photo_path, userid 
    FROM reg_tab 
    WHERE userid = ? OR email = ? OR userid = ? OR email = ? OR faculty_name = ?
    LIMIT 1
");
$stmt->bind_param("sssss", $identifier, $email, $uname, $uname, $full_name);
$stmt->execute();
$result = $stmt->get_result();

$user = null;
if ($result && $result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    // Fallback to users table
    $u_stmt = $conn->prepare("SELECT user_id, full_name, email, phone, password, profile_photo FROM users WHERE user_id = ? OR email = ? OR full_name = ? LIMIT 1");
    $u_stmt->bind_param("iss", $user_id, $email, $full_name);
    $u_stmt->execute();
    $u_res = $u_stmt->get_result();
    if ($u_res && $u_res->num_rows > 0) {
        $u_data = $u_res->fetch_assoc();
        $user = [
            'faculty_name' => $u_data['full_name'] ?? $full_name,
            'designation' => 'Faculty',
            'qualification' => 'B.Tech',
            'dept' => $_SESSION['dept'] ?? 'CSE',
            'pern_no' => '',
            'dob' => '',
            'gender' => 'Male',
            'address' => '',
            'email' => $u_data['email'] ?? $email,
            'aadhar' => '',
            'pan' => '',
            'phone' => $u_data['phone'] ?? '',
            'experience' => '',
            'password' => $u_data['password'] ?? '',
            'photo_path' => $u_data['profile_photo'] ?? '',
            'userid' => !empty($identifier) ? $identifier : ($u_data['email'] ?? $email)
        ];
    } else {
        $user = [
            'faculty_name' => $full_name ?: $uname,
            'designation' => 'Faculty',
            'qualification' => 'B.Tech',
            'dept' => $_SESSION['dept'] ?? 'CSE',
            'pern_no' => '',
            'dob' => '',
            'gender' => 'Male',
            'address' => '',
            'email' => $email,
            'aadhar' => '',
            'pan' => '',
            'phone' => '',
            'experience' => '',
            'password' => '',
            'photo_path' => '',
            'userid' => !empty($identifier) ? $identifier : ($email ?: $uname)
        ];
    }
    $u_stmt->close();
}
$stmt->close();

$message = '';
$msg_type = '';

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $faculty_name = trim($_POST['faculty_name'] ?? '');
    $designation = trim($_POST['designation'] ?? '');
    $qualification = trim($_POST['qualification'] ?? '');
    $dept = trim($_POST['dept'] ?? '');
    $pern_no = trim($_POST['pern_no'] ?? '');
    $dob = trim($_POST['dob'] ?? '');
    $gender = trim($_POST['gender'] ?? 'Male');
    $address = trim($_POST['address'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $aadhar = trim($_POST['aadhar'] ?? '');
    $pan = trim($_POST['pan'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $experience = trim($_POST['experience'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Handle photo upload
    $target_file = $user['photo_path'] ?? '';
    if (isset($_FILES['photo_path']) && !empty($_FILES['photo_path']['name']) && $_FILES['photo_path']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = ROOT_PATH . "/uploads/profiles/";
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        $ext = pathinfo($_FILES["photo_path"]["name"], PATHINFO_EXTENSION);
        $filename = "photo_" . uniqid() . "." . strtolower($ext);
        if (move_uploaded_file($_FILES["photo_path"]["tmp_name"], $upload_dir . $filename)) {
            $target_file = "uploads/profiles/" . $filename;
        }
    }

    $current_userid = !empty($user['userid']) ? $user['userid'] : $email;

    // Check if user exists in reg_tab
    $chk = $conn->prepare("SELECT id FROM reg_tab WHERE userid = ? OR email = ?");
    $chk->bind_param("ss", $current_userid, $email);
    $chk->execute();
    $exists = $chk->get_result()->num_rows > 0;
    $chk->close();

    if ($exists) {
        $update_query = "UPDATE reg_tab 
            SET faculty_name=?, designation=?, qualification=?, dept=?, pern_no=?, dob=?, gender=?, address=?, email=?, aadhar=?, pan=?, phone=?, experience=?, password=?, photo_path=? 
            WHERE userid=? OR email=?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param(
            "sssssssssssssssss",
            $faculty_name, $designation, $qualification, $dept, $pern_no, $dob, $gender, $address, $email,
            $aadhar, $pan, $phone, $experience, $password, $target_file, $current_userid, $email
        );
        $success = $update_stmt->execute();
        $update_stmt->close();
    } else {
        $ins_query = "INSERT INTO reg_tab (faculty_name, designation, qualification, dept, pern_no, dob, gender, address, email, aadhar, pan, phone, experience, password, photo_path, userid)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $ins_stmt = $conn->prepare($ins_query);
        $ins_stmt->bind_param(
            "ssssssssssssssss",
            $faculty_name, $designation, $qualification, $dept, $pern_no, $dob, $gender, $address, $email,
            $aadhar, $pan, $phone, $experience, $password, $target_file, $current_userid
        );
        $success = $ins_stmt->execute();
        $ins_stmt->close();
    }

    // Sync users table if available
    $u_up = $conn->prepare("UPDATE users SET full_name = ?, phone = ?, password = ?, profile_photo = ? WHERE email = ? OR user_id = ?");
    if ($u_up) {
        $u_up->bind_param("sssssi", $faculty_name, $phone, $password, $target_file, $email, $user_id);
        $u_up->execute();
        $u_up->close();
    }

    // Update session info
    $_SESSION['full_name'] = $faculty_name;
    $_SESSION['username'] = $faculty_name;
    $_SESSION['email'] = $email;

    if ($success) {
        $message = 'Profile updated successfully!';
        $msg_type = 'alert-success';
        // Refresh $user data
        $user['faculty_name'] = $faculty_name;
        $user['designation'] = $designation;
        $user['qualification'] = $qualification;
        $user['dept'] = $dept;
        $user['pern_no'] = $pern_no;
        $user['dob'] = $dob;
        $user['gender'] = $gender;
        $user['address'] = $address;
        $user['email'] = $email;
        $user['aadhar'] = $aadhar;
        $user['pan'] = $pan;
        $user['phone'] = $phone;
        $user['experience'] = $experience;
        $user['password'] = $password;
        $user['photo_path'] = $target_file;
    } else {
        $message = 'Failed to update profile: ' . $conn->error;
        $msg_type = 'alert-danger';
    }
}

include_once HEADER;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile - FMS</title>
    <style>
        .profile-wrapper {
            max-width: 850px;
            margin: 30px auto 60px;
            padding: 0 15px;
        }

        .profile-card {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
            padding: 35px 40px;
            border: 1px solid #e2e8f0;
        }

        .profile-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .profile-header h1 {
            color: #1e293b;
            font-size: 2rem;
            margin-bottom: 8px;
            font-weight: 700;
        }

        .profile-header p {
            color: #64748b;
            font-size: 0.95rem;
        }

        .photo-container {
            position: relative;
            width: 130px;
            height: 130px;
            margin: 0 auto 20px;
        }

        .profile-image {
            width: 130px;
            height: 130px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #3b82f6;
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.2);
        }

        .alert-box {
            padding: 14px 18px;
            border-radius: 8px;
            margin-bottom: 25px;
            font-weight: 500;
            font-size: 0.95rem;
        }

        .alert-success {
            background-color: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .alert-danger {
            background-color: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
        }

        .form-full {
            grid-column: 1 / -1;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            font-weight: 600;
            color: #334155;
            margin-bottom: 6px;
            font-size: 0.9rem;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            padding: 10px 14px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 0.95rem;
            color: #1e293b;
            background-color: #f8fafc;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #3b82f6;
            background-color: #ffffff;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 80px;
        }

        .btn-submit {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            color: white;
            padding: 14px 28px;
            font-size: 1rem;
            font-weight: 600;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            width: 100%;
            transition: all 0.3s;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
            margin-top: 15px;
        }

        .btn-submit:hover {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            box-shadow: 0 6px 18px rgba(37, 99, 235, 0.35);
            transform: translateY(-1px);
        }

        @media (max-width: 640px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
            .profile-card {
                padding: 25px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="profile-wrapper">
        <div class="profile-card">
            <div class="profile-header">
                <div class="photo-container">
                    <?php
                    $photo_path = $user['photo_path'] ?? '';
                    $img_src = !empty($photo_path) ? (BASE_URL . '/' . ltrim($photo_path, '/')) : (IMAGES_PATH . '/logo.png');
                    ?>
                    <img src="<?= htmlspecialchars($img_src); ?>" class="profile-image" alt="Profile Photo" onerror="this.src='<?= IMAGES_PATH ?>/logo.png'">
                </div>
                <h1>Edit Faculty Profile</h1>
                <p>Keep your personal and academic credentials up to date</p>
            </div>

            <?php if (!empty($message)): ?>
                <div class="alert-box <?= $msg_type; ?>">
                    <?= htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="faculty_name">Full Name *</label>
                        <input type="text" id="faculty_name" name="faculty_name" value="<?= htmlspecialchars($user['faculty_name'] ?? ''); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="designation">Designation *</label>
                        <input type="text" id="designation" name="designation" value="<?= htmlspecialchars($user['designation'] ?? ''); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="qualification">Highest Qualification *</label>
                        <select id="qualification" name="qualification" required>
                            <option value="">Select Qualification</option>
                            <?php
                            $quals = ["B.Sc", "B.Com", "B.A", "B.Tech", "M.Sc", "M.Com", "M.A", "M.Tech", "MBA", "MCA", "Ph.D", "Post Doctorate", "Other"];
                            foreach ($quals as $q):
                            ?>
                                <option value="<?= $q ?>" <?= (($user['qualification'] ?? '') == $q) ? 'selected' : '' ?>><?= $q ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="dept">Department *</label>
                        <select id="dept" name="dept" required>
                            <option value="">Select Department</option>
                            <?php
                            $depts = ["CSE-AI&DS", "CSE-AI&ML", "CSE", "CSE-CS", "CIVIL", "MatheMatics", "Physics", "Chemistry", "BSH", "MECH", "EEE", "ECE", "IT"];
                            foreach ($depts as $d):
                            ?>
                                <option value="<?= $d ?>" <?= (($user['dept'] ?? '') == $d) ? 'selected' : '' ?>><?= $d ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="pern_no">PERN Number</label>
                        <input type="text" id="pern_no" name="pern_no" value="<?= htmlspecialchars($user['pern_no'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label for="dob">Date of Birth</label>
                        <input type="date" id="dob" name="dob" value="<?= htmlspecialchars($user['dob'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label for="gender">Gender</label>
                        <select id="gender" name="gender">
                            <option value="Male" <?= (($user['gender'] ?? '') == 'Male') ? 'selected' : '' ?>>Male</option>
                            <option value="Female" <?= (($user['gender'] ?? '') == 'Female') ? 'selected' : '' ?>>Female</option>
                            <option value="Other" <?= (($user['gender'] ?? '') == 'Other') ? 'selected' : '' ?>>Other</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" id="phone" name="phone" value="<?= htmlspecialchars($user['phone'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label for="email">Email Address *</label>
                        <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email'] ?? ''); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="password">Password *</label>
                        <input type="text" id="password" name="password" value="<?= htmlspecialchars($user['password'] ?? ''); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="aadhar">Aadhar Number</label>
                        <input type="text" id="aadhar" name="aadhar" value="<?= htmlspecialchars($user['aadhar'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label for="pan">PAN Card Number</label>
                        <input type="text" id="pan" name="pan" value="<?= htmlspecialchars($user['pan'] ?? ''); ?>">
                    </div>

                    <div class="form-group form-full">
                        <label for="photo_path">Upload New Profile Photo</label>
                        <input type="file" id="photo_path" name="photo_path" accept="image/*">
                    </div>

                    <div class="form-group form-full">
                        <label for="address">Address</label>
                        <textarea id="address" name="address"><?= htmlspecialchars($user['address'] ?? ''); ?></textarea>
                    </div>

                    <div class="form-group form-full">
                        <label for="experience">Experience Summary</label>
                        <textarea id="experience" name="experience"><?= htmlspecialchars($user['experience'] ?? ''); ?></textarea>
                    </div>

                    <div class="form-full">
                        <button type="submit" class="btn-submit">Update Profile</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
