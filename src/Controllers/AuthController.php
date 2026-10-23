<?php
namespace App\Controllers;

class AuthController {

    public function login() {
        require_once __DIR__ . '/../../core/bootstrap.php';

        if (auth_is_logged_in()) {
            $active = auth_active_role();
            if ($active) {
                header("Location: " . get_role_landing_url($active));
            } else {
                header("Location: " . BASE_URL . "/public/index.php?route=auth/select_role");
            }
            exit();
        }

        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            csrfValidate();

            $identifier = trim($_POST['identifier'] ?? '');
            $password = $_POST['password'] ?? '';

            if (empty($identifier) || empty($password)) {
                $error = 'Please enter your User ID and password.';
            } else {
                global $conn;
                $user = auth_authenticate($conn, $identifier, $password);

                if ($user === false) {
                    $error = 'Invalid credentials or inactive account.';
                } else {
                    $roles = $user['roles'];

                    if (empty($roles)) {
                        $error = 'Your account has no assigned roles. Contact an administrator.';
                    } elseif (count($roles) === 1) {
                        auth_create_session($user, $roles[0]);
                        auth_update_last_login($conn, $user['user_id']);

                        header("Location: " . get_role_landing_url($roles[0]));
                        exit();
                    } else {
                        $_SESSION['_pending_auth'] = $user;
                        header("Location: " . BASE_URL . "/public/index.php?route=auth/select_role");
                        exit();
                    }
                }
            }
        }

        include __DIR__ . '/../Views/auth/login.php';
    }

    public function selectRole() {
        require_once __DIR__ . '/../../core/bootstrap.php';

        if (auth_is_logged_in()) {
            $user = auth_context();
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                if (count($user['roles'] ?? []) <= 1) {
                    $active = auth_active_role();
                    if ($active) {
                        header("Location: " . get_role_landing_url($active));
                    } else {
                        header("Location: " . BASE_URL . "/public/index.php?route=auth/login");
                    }
                    exit();
                }
            }
        } elseif (isset($_SESSION['_pending_auth'])) {
            $user = $_SESSION['_pending_auth'];
        } else {
            header("Location: " . BASE_URL . "/public/index.php?route=auth/login");
            exit();
        }

        $roles = $user['roles'];
        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            csrfValidate();

            $selected_urid = (int)($_POST['user_role_id'] ?? 0);

            $valid_role = null;
            foreach ($roles as $role) {
                if ((int)$role['user_role_id'] === $selected_urid) {
                    $valid_role = $role;
                    break;
                }
            }

            $is_ajax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
                || (str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json'));

            if ($valid_role) {
                global $conn;
                auth_create_session($user, $valid_role);
                auth_update_last_login($conn, $user['user_id']);

                unset($_SESSION['_pending_auth']);

                if ($is_ajax) {
                    header('Content-Type: application/json; charset=UTF-8');
                    echo json_encode([
                        'status' => 'success',
                        'redirect_url' => get_role_landing_url($valid_role)
                    ]);
                    exit();
                }

                header("Location: " . get_role_landing_url($valid_role));
                exit();
            } else {
                $error = 'Invalid role selected. Please try again.';
                if ($is_ajax) {
                    http_response_code(400);
                    header('Content-Type: application/json; charset=UTF-8');
                    echo json_encode([
                        'status' => 'error',
                        'message' => $error
                    ]);
                    exit();
                }
            }
        }

        $display_roles = get_distinct_role_assignments($roles);

        include __DIR__ . '/../Views/auth/select_role.php';
    }

    public function register() {
        require_once __DIR__ . '/../../core/bootstrap.php';
        
        if (auth_is_logged_in()) {
            $active = auth_active_role();
            if ($active) {
                header("Location: " . get_role_landing_url($active));
            } else {
                header("Location: " . BASE_URL . "/public/index.php?route=auth/select_role");
            }
            exit();
        }

        $error = '';
        $success = '';

        global $conn;
        $dept_result = $conn->query("SELECT dept_id, dept_name FROM departments ORDER BY dept_name");
        $departments = [];
        if ($dept_result instanceof \mysqli_result) {
            while ($row = $dept_result->fetch_assoc()) {
                $departments[] = $row;
            }
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            csrfValidate();

            $full_name = trim($_POST['full_name'] ?? '');
            $email     = trim($_POST['email'] ?? '');
            $password  = $_POST['password'] ?? '';
            $confirm   = $_POST['confirm_password'] ?? '';
            $dept_id   = (int)($_POST['dept_id'] ?? 0);

            $per_no = trim($_POST['per_no'] ?? '');
            $pan_no = strtoupper(trim($_POST['pan_no'] ?? ''));
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
            $experience_years = max(0.0, round((float)($_POST['experience_years'] ?? 0), 2));

            if (empty($doj_department)) $doj_department = null;
            if (empty($date_designated_prof)) $date_designated_prof = null;
            if (empty($contract_type)) $contract_type = null;
            if (empty($date_of_leaving) || $is_currently_associated) $date_of_leaving = null;
            if (empty($apaar_id)) $apaar_id = null;

            if (empty($full_name) || empty($email) || empty($password)) {
                $error = 'Name, email, and password are required.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = 'Please enter a valid email address.';
            } elseif (!preg_match('/@gmrit\.edu\.in$/i', $email)) {
                $error = 'Registration is restricted to @gmrit.edu.in email addresses.';
            } elseif (strlen($password) < 6) {
                $error = 'Password must be at least 6 characters.';
            } elseif ($password !== $confirm) {
                $error = 'Passwords do not match.';
            } elseif ($dept_id <= 0) {
                $error = 'Please select a department.';
            } else {
                // Role assignment based on explicit dropdown
                $role_type = $_POST['role_type'] ?? 'faculty';
                $role_id = ($role_type === 'student') ? ROLE_STUDENT : ROLE_FACULTY;

                // Strict security check for Student Role
                if ($role_id === ROLE_STUDENT) {
                    $is_student_email = preg_match('/^[0-9]{2}[0-9A-Za-z]{2}[0-9A-Za-z][A-Za-z0-9]{5}@gmrit\.edu\.in$/i', $email);
                    if (!$is_student_email) {
                        $error = 'Student registration requires a valid JNTU Roll Number email (e.g., 21341A0501@gmrit.edu.in).';
                    }
                }

                // If faculty, validate faculty-specific fields
                if (empty($error) && $role_id === ROLE_FACULTY) {
                    if (empty($per_no) || empty($pan_no) || empty($highest_degree) || empty($university) || empty($specialization) || empty($doj_institution) || empty($designation_joining) || empty($designation_present) || empty($association_nature)) {
                        $error = 'All mandatory faculty fields must be filled.';
                    }
                }

                if (empty($error)) {
                    $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
                    if ($stmt) {
                        $stmt->bind_param("s", $email);
                        $stmt->execute();
                        if ($stmt->get_result()->num_rows > 0) {
                            $error = 'An account with this email already exists.';
                        }
                        $stmt->close();
                    } else {
                        $error = 'Database error checking email availability.';
                    }
                }

                if (empty($error) && $role_id === ROLE_FACULTY) {
                    $check_sql = "SELECT user_id FROM user_profiles WHERE pan_no = ? OR per_no = ?";
                    if (!empty($apaar_id)) {
                        $check_sql .= " OR (apaar_id IS NOT NULL AND apaar_id = ?)";
                    }
                    $stmt2 = $conn->prepare($check_sql);
                    if ($stmt2) {
                        if (!empty($apaar_id)) {
                            $stmt2->bind_param("sss", $pan_no, $per_no, $apaar_id);
                        } else {
                            $stmt2->bind_param("ss", $pan_no, $per_no);
                        }
                        $stmt2->execute();
                        if ($stmt2->get_result()->num_rows > 0) {
                            $error = 'An account with this PAN number, Per No, or APAAR ID already exists.';
                        }
                        $stmt2->close();
                    } else {
                        $error = 'Database error verifying faculty credentials.';
                    }
                }

                if (empty($error) && $role_id === ROLE_FACULTY) {
                    if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] !== UPLOAD_ERR_NO_FILE) {
                        if ($_FILES['profile_photo']['error'] === UPLOAD_ERR_INI_SIZE || $_FILES['profile_photo']['error'] === UPLOAD_ERR_FORM_SIZE) {
                            $error = 'Profile photo exceeds the maximum permitted file size.';
                        } elseif ($_FILES['profile_photo']['error'] !== UPLOAD_ERR_OK) {
                            $error = 'Error uploading profile photo (code: ' . (int)$_FILES['profile_photo']['error'] . ').';
                        } else {
                            $allowed_exts = ['jpg', 'jpeg', 'png', 'webp'];
                            $f_ext = strtolower(pathinfo($_FILES['profile_photo']['name'] ?? '', PATHINFO_EXTENSION));
                            if (!in_array($f_ext, $allowed_exts, true)) {
                                $error = 'Profile photo must be a valid image file (JPG, PNG, or WebP).';
                            }
                        }
                    }
                }

                if (empty($error)) {
                    $conn->begin_transaction();
                    try {
                        $hashed = password_hash($password, PASSWORD_DEFAULT);
                        $stmt = $conn->prepare("INSERT INTO users (full_name, email, password, status) VALUES (?, ?, ?, 'active')");
                        if (!$stmt) {
                            throw new \Exception("Database error preparing user account statement.");
                        }
                        $stmt->bind_param("sss", $full_name, $email, $hashed);
                        $stmt->execute();
                        $new_user_id = $conn->insert_id;
                        $stmt->close();

                        $role_stmt = $conn->prepare("INSERT INTO user_roles (user_id, role_id, dept_id) VALUES (?, ?, ?)");
                        if (!$role_stmt) {
                            throw new \Exception("Database error preparing user roles statement.");
                        }
                        $role_stmt->bind_param("iii", $new_user_id, $role_id, $dept_id);
                        $role_stmt->execute();
                        $role_stmt->close();

                        if ($role_id === ROLE_FACULTY) {
                            $profile_photo_path = null;
                            if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
                                require_once __DIR__ . '/../../core/file_service.php';
                                $upload_result = file_store($_FILES['profile_photo'], 'profiles', 'photo');
                                if ($upload_result['success']) {
                                    $profile_photo_path = $upload_result['data']['file_path'];
                                } else {
                                    throw new \Exception('Profile photo upload failed: ' . ($upload_result['error'] ?? 'Invalid file format or size.'));
                                }
                            }

                            $prof_stmt = $conn->prepare("
                                INSERT INTO user_profiles (
                                    user_id, per_no, pan_no, apaar_id, highest_degree, university, specialization, 
                                    doj_institution, doj_department, experience_years, designation_joining, 
                                    designation_present, date_designated_prof, association_nature, 
                                    contract_type, is_currently_associated, date_of_leaving, profile_photo
                                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                            ");
                            if (!$prof_stmt) {
                                throw new \Exception("Database error preparing faculty profile statement.");
                            }
                            $prof_stmt->bind_param(
                                "issssssssdsssssiss", 
                                $new_user_id, $per_no, $pan_no, $apaar_id, $highest_degree, $university, $specialization,
                                $doj_institution, $doj_department, $experience_years, $designation_joining,
                                $designation_present, $date_designated_prof, $association_nature,
                                $contract_type, $is_currently_associated, $date_of_leaving, $profile_photo_path
                            );
                            $prof_stmt->execute();
                            $prof_stmt->close();
                        }

                        $conn->commit();
                        $success = 'Registration successful! You can now log in.';
                        $_POST = [];
                    } catch (\Throwable $e) {
                        $conn->rollback();
                        if (!empty($profile_photo_path)) {
                            $photo_file = __DIR__ . '/../../' . ltrim($profile_photo_path, '/\\');
                            if (file_exists($photo_file) && is_file($photo_file)) {
                                @unlink($photo_file);
                            }
                        }
                        error_log("Registration failed DB error: " . $e->getMessage());
                        if (str_starts_with($e->getMessage(), 'Profile photo upload failed')) {
                            $error = $e->getMessage();
                        } else {
                            $error = 'Registration could not be completed due to a system error. Please verify your details or contact support.';
                        }
                    }
                }
            }
        }

        include __DIR__ . '/../Views/auth/register.php';
    }

    public function logout() {
        require_once __DIR__ . '/../../core/bootstrap.php';
        
        auth_logout();
        
        // Start a new session just to set a flash message
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['_flash_logout'] = true;
        
        header("Location: " . BASE_URL . "/public/index.php?route=auth/login");
        exit();
    }
}
