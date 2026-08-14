<?php
include_once '../../config.php';
include_once CONNECTION_PATH;
include_once INCLUDES_PATH . '/helpers.php';

// Redirect if already logged in
if (isLoggedIn()) {
    header("Location: " . BASE_URL . "/dashboard.php");
    exit();
}

$message = '';
$msg_type = '';

$departments = getDepartments($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = $_POST['password'];
    $conf_password = $_POST['conf_password'];
    $dept_id = (int) $_POST['dept_id'];
    $role_id = ROLE_FACULTY; // Default role is Faculty

    if (empty($full_name) || empty($email) || empty($password)) {
        $message = "Please fill in all required fields.";
        $msg_type = "alert-danger";
    } elseif ($_POST['password'] !== $_POST['conf_password']) {
        $message = "Password and Confirm Password do not match!";
        $msg_type = "alert-danger";
    } else {
        // Check if email already exists
        $check = $conn->prepare("SELECT email FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        if ($check->get_result()->num_rows > 0) {
            $message = "An account with this email already exists!";
            $msg_type = "alert-danger";
        } else {
            // Handle profile photo upload
            $profile_photo = null;
            if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
                $upload = handleFileUpload($_FILES['profile_photo'], 'profiles');
                if ($upload) {
                    $profile_photo = $upload['file_path'];
                }
            }

            $stmt = $conn->prepare("
                INSERT INTO users (full_name, email, phone, password, profile_photo)
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->bind_param("sssss", $full_name, $email, $phone, $password, $profile_photo);

            if ($stmt->execute()) {
                $new_user_id = $conn->insert_id;
                
                $role_stmt = $conn->prepare("INSERT INTO user_roles (user_id, role_id, dept_id) VALUES (?, ?, ?)");
                $role_stmt->bind_param("iii", $new_user_id, $role_id, $dept_id);
                $role_stmt->execute();
                $role_stmt->close();

                // Find dept_name for legacy reg_tab compatibility
                $d_name = 'CSE';
                foreach ($departments as $d) {
                    if ($d['dept_id'] == $dept_id) { $d_name = $d['dept_name']; break; }
                }

                $reg_stmt = $conn->prepare("INSERT IGNORE INTO reg_tab (faculty_name, userid, email, password, dept) VALUES (?, ?, ?, ?, ?)");
                if ($reg_stmt) {
                    $reg_stmt->bind_param("sssss", $full_name, $email, $email, $password, $d_name);
                    @$reg_stmt->execute();
                    @$reg_stmt->close();
                }

                $message = "Registration successful! You can now log in.";
                $msg_type = "alert-success";
            } else {
                $message = "Registration failed: " . $stmt->error;
                $msg_type = "alert-danger";
            }
            $stmt->close();
        }
        $check->close();
    }
}

include_once HEADER;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - FMS</title>
    <link rel="stylesheet" href="<?php echo CSS_PATH . '/auth.css'; ?>">
</head>
<body class="auth-page">
    <div class="auth-container">
        <div class="auth-card wide">
            <h1 style="text-align:center;">Faculty Registration</h1>
            <h2 style="text-align:center;">Create your FMS account</h2>

            <?php if (!empty($message)): ?>
                <div class="alert <?php echo $msg_type; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <div class="form-row">
                    <div>
                        <label for="full_name">Full Name *</label>
                        <input type="text" name="full_name" id="full_name" required
                            value="<?php echo isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : ''; ?>">
                    </div>
                    <div>
                        <label for="email">Email Address *</label>
                        <input type="email" name="email" id="email" required placeholder="name@gmrit.edu.in"
                            value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                    </div>
                </div>

                <div class="form-row">
                    <div>
                        <label for="phone">Phone Number</label>
                        <input type="tel" name="phone" id="phone" pattern="\d{10}" title="Phone number should be 10 digits"
                            value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>">
                    </div>
                    <div>
                        <label for="dept_id">Department *</label>
                        <select name="dept_id" id="dept_id" required>
                            <option value="" disabled selected>Select Department</option>
                            <?php foreach ($departments as $dept): ?>
                                <option value="<?php echo $dept['dept_id']; ?>"
                                    <?php echo (isset($_POST['dept_id']) && $_POST['dept_id'] == $dept['dept_id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($dept['dept_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div>
                        <label for="password">Password *</label>
                        <input type="password" name="password" id="password" required>
                    </div>
                    <div>
                        <label for="conf_password">Confirm Password *</label>
                        <input type="password" name="conf_password" id="conf_password" required>
                    </div>
                </div>

                <label for="profile_photo">Profile Photo</label>
                <input type="file" name="profile_photo" id="profile_photo" accept="image/*">

                <button type="submit" name="register" class="auth-btn success">Create Account</button>
            </form>

            <a href="login.php" class="auth-link" style="text-align:center;">Already have an account? Sign in</a>
        </div>
    </div>
</body>
</html>
