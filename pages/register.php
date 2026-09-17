<?php
/**
 * FMS Registration Page
 * 
 * CONSERVATIVE implementation:
 *   - Only Faculty (role_id=4) self-registration is allowed.
 *   - Captures comprehensive faculty profile details for NBA/NAAC compliance.
 */
require_once __DIR__ . '/../core/bootstrap.php';

// Already logged in? Go to dashboard
if (auth_is_logged_in()) {
    $active = auth_active_role();
    if ($active) {
        header("Location: " . get_role_landing_url($active));
    } else {
        header("Location: " . BASE_URL . "/pages/select_role.php");
    }
    exit();
}

$error = '';
$success = '';

// Load academic departments
$dept_result = $conn->query("SELECT dept_id, dept_name FROM departments ORDER BY dept_name");
$departments = [];
while ($row = $dept_result->fetch_assoc()) {
    $departments[] = $row;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrfValidate();

    // Core User Fields
    $full_name = trim($_POST['full_name'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $password  = $_POST['password'] ?? '';
    $confirm   = $_POST['confirm_password'] ?? '';
    $dept_id   = (int)($_POST['dept_id'] ?? 0);

    // Profile Fields
    $pan_no = trim($_POST['pan_no'] ?? '');
    $apaar_id = trim($_POST['apaar_id'] ?? '');
    $highest_degree = trim($_POST['highest_degree'] ?? '');
    $university = trim($_POST['university'] ?? '');
    $specialization = trim($_POST['specialization'] ?? '');
    $doj_institution = trim($_POST['doj_institution'] ?? '');
    $doj_department = trim($_POST['doj_department'] ?? '');
    $designation_joining = trim($_POST['designation_joining'] ?? '');
    $designation_present = trim($_POST['designation_present'] ?? '');
    $date_designated_prof = trim($_POST['date_designated_prof'] ?? '');
    $association_nature = trim($_POST['association_nature'] ?? '');
    $contract_type = trim($_POST['contract_type'] ?? '');
    $is_currently_associated = isset($_POST['is_currently_associated']) && $_POST['is_currently_associated'] === '1' ? 1 : 0;
    $date_of_leaving = trim($_POST['date_of_leaving'] ?? '');
    $experience_years = (float)($_POST['experience_years'] ?? 0);

    if (empty($doj_department)) $doj_department = null;
    if (empty($date_designated_prof)) $date_designated_prof = null;
    if (empty($contract_type)) $contract_type = null;
    if (empty($date_of_leaving) || $is_currently_associated) $date_of_leaving = null;
    if (empty($apaar_id)) $apaar_id = null;

    // Validation
    if (empty($full_name) || empty($email) || empty($password) || empty($pan_no) || empty($highest_degree) || empty($university) || empty($specialization) || empty($doj_institution) || empty($designation_joining) || empty($designation_present) || empty($association_nature)) {
        $error = 'All mandatory fields must be filled.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } elseif ($dept_id <= 0) {
        $error = 'Please select a department.';
    } else {
        // Validate dept_id exists
        $dept_check = $conn->prepare("SELECT dept_id FROM departments WHERE dept_id = ?");
        $dept_check->bind_param("i", $dept_id);
        $dept_check->execute();
        if ($dept_check->get_result()->num_rows === 0) {
            $error = 'Invalid department selected.';
            $dept_check->close();
        } else {
            $dept_check->close();

            // Check if email already exists
            $email_check = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
            $email_check->bind_param("s", $email);
            $email_check->execute();
            if ($email_check->get_result()->num_rows > 0) {
                $error = 'An account with this email already exists.';
            }
            $email_check->close();
        }
    }

    if (empty($error)) {
        $conn->begin_transaction();
        try {
            // 1. Insert User
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (name, email, password_hash, is_active) VALUES (?, ?, ?, 1)");
            $stmt->bind_param("sss", $full_name, $email, $hashed);
            $stmt->execute();
            $new_user_id = $conn->insert_id;
            $stmt->close();

            // 2. Assign Faculty Role
            $role_id = ROLE_FACULTY;
            $role_stmt = $conn->prepare("INSERT INTO user_roles (user_id, role_id, dept_id) VALUES (?, ?, ?)");
            $role_stmt->bind_param("iii", $new_user_id, $role_id, $dept_id);
            $role_stmt->execute();
            $role_stmt->close();

            // 3. Insert Profile Data
            $prof_stmt = $conn->prepare("
                INSERT INTO user_profiles (
                    user_id, pan_no, apaar_id, highest_degree, university, specialization, 
                    doj_institution, doj_department, experience_years, designation_joining, 
                    designation_present, date_designated_prof, association_nature, 
                    contract_type, is_currently_associated, date_of_leaving
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $prof_stmt->bind_param(
                "isssssssddssssis", 
                $new_user_id, $pan_no, $apaar_id, $highest_degree, $university, $specialization,
                $doj_institution, $doj_department, $experience_years, $designation_joining,
                $designation_present, $date_designated_prof, $association_nature,
                $contract_type, $is_currently_associated, $date_of_leaving
            );
            $prof_stmt->execute();
            $prof_stmt->close();

            $conn->commit();
            $success = 'Registration successful! You can now log in.';
            
            // Clear POST array so the form doesn't repopulate
            $_POST = [];
            
        } catch (Exception $e) {
            $conn->rollback();
            $error = 'Registration failed due to a database error. ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty Registration &mdash; FMS</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            background-image: url('<?= BASE_URL ?>/assets/img/gmr_landing_page.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            font-family: 'Segoe UI', Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: 100vh;
            padding: 40px 20px;
        }

        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.65);
            z-index: 0;
        }

        .card {
            position: relative;
            z-index: 1;
            background: rgba(15, 23, 42, 0.9);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            padding: 36px;
            border-radius: 12px;
            color: #f1f5f9;
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.5);
            width: 100%;
            max-width: 800px;
            animation: fadeIn 0.5s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .card h1 { font-size: 1.8em; margin-bottom: 8px; color: #fff; text-align: center; }
        .card .subtitle { color: #94a3b8; font-size: 0.9em; margin-bottom: 24px; text-align: center; }

        .role-note {
            background: rgba(59, 130, 246, 0.15);
            border: 1px solid rgba(59, 130, 246, 0.3);
            color: #93c5fd;
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 0.85em;
            margin-bottom: 24px;
        }

        h3.section-title {
            color: #38bdf8;
            font-size: 1.1em;
            border-bottom: 1px solid rgba(56, 189, 248, 0.3);
            padding-bottom: 8px;
            margin-top: 24px;
            margin-bottom: 16px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }
        
        .form-group.full-width { grid-column: 1 / -1; }

        .form-group label {
            display: block;
            font-size: 0.82em;
            color: #cbd5e1;
            margin-bottom: 6px;
            font-weight: 500;
        }

        .form-group input, .form-group select {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 6px;
            background: rgba(0, 0, 0, 0.3);
            color: #fff;
            font-size: 0.9em;
            transition: all 0.2s;
        }

        .form-group input[type="date"]::-webkit-calendar-picker-indicator { filter: invert(1); }
        .form-group select option { background: #1e293b; color: #fff; }

        .form-group input:focus, .form-group select:focus {
            outline: none;
            border-color: #3b82f6;
            background: rgba(0, 0, 0, 0.5);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.3);
        }

        .btn-register {
            width: 100%;
            padding: 14px;
            background: #10b981;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1.1em;
            font-weight: 600;
            margin-top: 30px;
            transition: background 0.2s;
        }

        .btn-register:hover { background: #059669; }

        .error-msg { background: rgba(239, 68, 68, 0.2); border: 1px solid rgba(239, 68, 68, 0.4); color: #fca5a5; padding: 12px; border-radius: 6px; margin-bottom: 20px; font-size: 0.9em; }
        .success-msg { background: rgba(16, 185, 129, 0.2); border: 1px solid rgba(16, 185, 129, 0.4); color: #6ee7b7; padding: 12px; border-radius: 6px; margin-bottom: 20px; font-size: 0.9em; text-align: center; }

        .footer-links { margin-top: 24px; text-align: center; font-size: 0.9em; color: #94a3b8; }
        .footer-links a { color: #60a5fa; text-decoration: none; }
        .footer-links a:hover { text-decoration: underline; }

        @media (max-width: 640px) {
            .form-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="card">
        <h1>Faculty Registration</h1>
        <p class="subtitle">Complete your FMS Faculty Profile</p>

        <div class="role-note">
            ℹ️ This form is exclusively for <strong>Faculty</strong> members to register their comprehensive profiles for accreditation purposes.
        </div>

        <?php if ($error): ?>
            <div class="error-msg"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="success-msg">
                <?= htmlspecialchars($success) ?><br><br>
                <a href="<?= BASE_URL ?>/pages/login.php" style="color: #6ee7b7; text-decoration: underline; font-weight: bold;">Click here to login</a>
            </div>
        <?php else: ?>

        <form method="POST" action="" id="regForm">
            <?= csrfField() ?>

            <h3 class="section-title">1. Account Credentials</h3>
            <div class="form-grid">
                <div class="form-group">
                    <label for="full_name">Full Name *</label>
                    <input type="text" id="full_name" name="full_name" value="<?= htmlspecialchars($_POST['full_name'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">Email Address *</label>
                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" placeholder="you@gmrit.edu.in" required>
                </div>
                <div class="form-group">
                    <label for="password">Password *</label>
                    <input type="password" id="password" name="password" minlength="6" required>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirm Password *</label>
                    <input type="password" id="confirm_password" name="confirm_password" minlength="6" required>
                </div>
                <div class="form-group full-width">
                    <label for="dept_id">Department *</label>
                    <select id="dept_id" name="dept_id" required>
                        <option value="">&mdash; Select Department &mdash;</option>
                        <?php foreach ($departments as $d): ?>
                            <option value="<?= $d['dept_id'] ?>" <?= (isset($_POST['dept_id']) && (int)$_POST['dept_id'] === (int)$d['dept_id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($d['dept_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <h3 class="section-title">2. Professional Details</h3>
            <div class="form-grid">
                <div class="form-group">
                    <label for="pan_no">PAN No. *</label>
                    <input type="text" id="pan_no" name="pan_no" value="<?= htmlspecialchars($_POST['pan_no'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label for="apaar_id">APAAR faculty ID (if any)</label>
                    <input type="text" id="apaar_id" name="apaar_id" value="<?= htmlspecialchars($_POST['apaar_id'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label for="highest_degree">Highest Degree *</label>
                    <input type="text" id="highest_degree" name="highest_degree" value="<?= htmlspecialchars($_POST['highest_degree'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label for="university">University *</label>
                    <input type="text" id="university" name="university" value="<?= htmlspecialchars($_POST['university'] ?? '') ?>" required>
                </div>
                <div class="form-group full-width">
                    <label for="specialization">Area of Specialization *</label>
                    <input type="text" id="specialization" name="specialization" value="<?= htmlspecialchars($_POST['specialization'] ?? '') ?>" required>
                </div>
            </div>

            <h3 class="section-title">3. Employment History</h3>
            <div class="form-grid">
                <div class="form-group">
                    <label for="doj_institution">Date of Joining (Institution) *</label>
                    <input type="date" id="doj_institution" name="doj_institution" value="<?= htmlspecialchars($_POST['doj_institution'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label for="doj_department">Date of Joining (Department)</label>
                    <input type="date" id="doj_department" name="doj_department" value="<?= htmlspecialchars($_POST['doj_department'] ?? '') ?>" title="In case of transfer from one Department to another">
                </div>
                <div class="form-group">
                    <label for="designation_joining">Designation at Time of Joining *</label>
                    <input type="text" id="designation_joining" name="designation_joining" value="<?= htmlspecialchars($_POST['designation_joining'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label for="designation_present">Present Designation *</label>
                    <input type="text" id="designation_present" name="designation_present" value="<?= htmlspecialchars($_POST['designation_present'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label for="date_designated_prof">Date Designated as Prof/Assoc. Prof (if any)</label>
                    <input type="date" id="date_designated_prof" name="date_designated_prof" value="<?= htmlspecialchars($_POST['date_designated_prof'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label for="experience_years">Experience in current institute (Years) *</label>
                    <input type="number" step="0.1" id="experience_years" name="experience_years" value="<?= htmlspecialchars($_POST['experience_years'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label for="association_nature">Nature of Association *</label>
                    <select id="association_nature" name="association_nature" required onchange="toggleContractFields()">
                        <option value="">&mdash; Select &mdash;</option>
                        <option value="Regular" <?= (isset($_POST['association_nature']) && $_POST['association_nature'] === 'Regular') ? 'selected' : '' ?>>Regular</option>
                        <option value="Contract" <?= (isset($_POST['association_nature']) && $_POST['association_nature'] === 'Contract') ? 'selected' : '' ?>>Contract</option>
                        <option value="Ad hoc" <?= (isset($_POST['association_nature']) && $_POST['association_nature'] === 'Ad hoc') ? 'selected' : '' ?>>Ad hoc</option>
                    </select>
                </div>
                <div class="form-group" id="contract_type_grp" style="display: none;">
                    <label for="contract_type">Contract Type</label>
                    <select id="contract_type" name="contract_type">
                        <option value="">&mdash; Select &mdash;</option>
                        <option value="Full time" <?= (isset($_POST['contract_type']) && $_POST['contract_type'] === 'Full time') ? 'selected' : '' ?>>Full time</option>
                        <option value="Part time" <?= (isset($_POST['contract_type']) && $_POST['contract_type'] === 'Part time') ? 'selected' : '' ?>>Part time</option>
                        <option value="Hourly based" <?= (isset($_POST['contract_type']) && $_POST['contract_type'] === 'Hourly based') ? 'selected' : '' ?>>Hourly based</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="is_currently_associated">Currently Associated? *</label>
                    <select id="is_currently_associated" name="is_currently_associated" required onchange="toggleLeavingDate()">
                        <option value="1" <?= (isset($_POST['is_currently_associated']) && $_POST['is_currently_associated'] === '1') ? 'selected' : '' ?>>Yes</option>
                        <option value="0" <?= (isset($_POST['is_currently_associated']) && $_POST['is_currently_associated'] === '0') ? 'selected' : '' ?>>No</option>
                    </select>
                </div>
                <div class="form-group" id="date_leaving_grp" style="display: none;">
                    <label for="date_of_leaving">Date of Leaving *</label>
                    <input type="date" id="date_of_leaving" name="date_of_leaving" value="<?= htmlspecialchars($_POST['date_of_leaving'] ?? '') ?>">
                </div>
            </div>

            <button type="submit" class="btn-register">Complete Registration</button>
        </form>

        <div class="footer-links">
            Already have an account? <a href="<?= BASE_URL ?>/pages/login.php">Log in</a>
        </div>
        
        <?php endif; ?>
    </div>

    <script>
        function toggleContractFields() {
            const nature = document.getElementById('association_nature').value;
            const contractGrp = document.getElementById('contract_type_grp');
            if (nature === 'Contract') {
                contractGrp.style.display = 'block';
                document.getElementById('contract_type').setAttribute('required', 'required');
            } else {
                contractGrp.style.display = 'none';
                document.getElementById('contract_type').removeAttribute('required');
                document.getElementById('contract_type').value = '';
            }
        }

        function toggleLeavingDate() {
            const isAssoc = document.getElementById('is_currently_associated').value;
            const leavingGrp = document.getElementById('date_leaving_grp');
            if (isAssoc === '0') {
                leavingGrp.style.display = 'block';
                document.getElementById('date_of_leaving').setAttribute('required', 'required');
            } else {
                leavingGrp.style.display = 'none';
                document.getElementById('date_of_leaving').removeAttribute('required');
                document.getElementById('date_of_leaving').value = '';
            }
        }

        // Auto-calculate experience from Date of Joining
        document.getElementById('doj_institution').addEventListener('change', function() {
            if (this.value) {
                const doj = new Date(this.value);
                const today = new Date();
                const diffTime = Math.abs(today - doj);
                const diffYears = diffTime / (1000 * 60 * 60 * 24 * 365.25);
                document.getElementById('experience_years').value = diffYears.toFixed(1);
            }
        });

        // Initialize on load to restore state if form submission failed
        window.onload = function() {
            toggleContractFields();
            toggleLeavingDate();
        };
    </script>
</body>
</html>
