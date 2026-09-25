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
            $active = auth_active_role();
            if ($active) {
                header("Location: " . get_role_landing_url($active));
            } else {
                header("Location: " . BASE_URL . "/public/index.php?route=auth/login");
            }
            exit();
        }

        if (!isset($_SESSION['_pending_auth'])) {
            header("Location: " . BASE_URL . "/public/index.php?route=auth/login");
            exit();
        }

        $user = $_SESSION['_pending_auth'];
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

            if ($valid_role) {
                global $conn;
                auth_create_session($user, $valid_role);
                auth_update_last_login($conn, $user['user_id']);

                unset($_SESSION['_pending_auth']);

                header("Location: " . get_role_landing_url($valid_role));
                exit();
            } else {
                $error = 'Invalid role selected. Please try again.';
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
        while ($row = $dept_result->fetch_assoc()) {
            $departments[] = $row;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            csrfValidate();

            $full_name = trim($_POST['full_name'] ?? '');
            $email     = trim($_POST['email'] ?? '');
            $password  = $_POST['password'] ?? '';
            $confirm   = $_POST['confirm_password'] ?? '';
            $dept_id   = (int)($_POST['dept_id'] ?? 0);

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
                $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
                $stmt->bind_param("s", $email);
                $stmt->execute();
                if ($stmt->get_result()->num_rows > 0) {
                    $error = 'An account with this email already exists.';
                }
                $stmt->close();

                if (empty($error)) {
                    $stmt2 = $conn->prepare("SELECT user_id FROM user_profiles WHERE pan_no = ?");
                    $stmt2->bind_param("s", $pan_no);
                    $stmt2->execute();
                    if ($stmt2->get_result()->num_rows > 0) {
                        $error = 'An account with this PAN number already exists.';
                    }
                    $stmt2->close();
                }

                if (empty($error)) {
                    $conn->begin_transaction();
                    try {
                        $hashed = password_hash($password, PASSWORD_DEFAULT);
                        $stmt = $conn->prepare("INSERT INTO users (full_name, email, password, status) VALUES (?, ?, ?, 'active')");
                        $stmt->bind_param("sss", $full_name, $email, $hashed);
                        $stmt->execute();
                        $new_user_id = $conn->insert_id;
                        $stmt->close();

                        $role_id = ROLE_FACULTY;
                        $role_stmt = $conn->prepare("INSERT INTO user_roles (user_id, role_id, dept_id) VALUES (?, ?, ?)");
                        $role_stmt->bind_param("iii", $new_user_id, $role_id, $dept_id);
                        $role_stmt->execute();
                        $role_stmt->close();

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
                        $_POST = [];
                    } catch (\Exception $e) {
                        $conn->rollback();
                        $error = 'Registration failed due to a database error. ' . $e->getMessage();
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
        session_start();
        $_SESSION['_flash_logout'] = true;
        
        header("Location: " . BASE_URL . "/public/index.php?route=auth/login");
        exit();
    }
}
