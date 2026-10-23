<?php
namespace App\Controllers;

class ProfileController {
    public function view() {
        require_once __DIR__ . '/../../core/bootstrap.php';
        require_login();

        $auth = auth_context();
        global $conn;

        $target_user_id = (int)$auth['user_id'];
        $active_role_id = auth_active_role_id();
        $can_view_others = in_array($active_role_id, [ROLE_ADMIN, ROLE_IQAC, ROLE_HOD, ROLE_RND_DEAN, ROLE_CENTRAL_COORDINATOR], true);
        if ($can_view_others && isset($_GET['id']) && (int)$_GET['id'] > 0) {
            $req_id = (int)$_GET['id'];
            if ($active_role_id === ROLE_HOD) {
                // Verify target user belongs to HOD's department
                $hod_dept = auth_active_dept_id();
                $chk_stmt = $conn->prepare("SELECT 1 FROM user_roles WHERE user_id = ? AND dept_id = ? LIMIT 1");
                if ($chk_stmt) {
                    $chk_stmt->bind_param("ii", $req_id, $hod_dept);
                    $chk_stmt->execute();
                    $in_dept = $chk_stmt->get_result()->num_rows > 0;
                    $chk_stmt->close();
                    if ($in_dept) {
                        $target_user_id = $req_id;
                    } else {
                        $_SESSION['error_msg'] = "Access denied. You can only view profiles within your department.";
                        header("Location: " . BASE_URL . "/public/index.php?route=profile/view");
                        exit();
                    }
                }
            } else {
                $target_user_id = $req_id;
            }
        }

        $profile = null;
        $stmt = $conn->prepare("SELECT u.full_name as name, u.email, p.* FROM users u LEFT JOIN user_profiles p ON u.user_id = p.user_id WHERE u.user_id = ?");
        if ($stmt) {
            $stmt->bind_param("i", $target_user_id);
            $stmt->execute();
            $profile = $stmt->get_result()->fetch_assoc();
            $stmt->close();
        }

        if (!$profile) {
            $_SESSION['error_msg'] = "The requested user profile was not found.";
            header("Location: " . BASE_URL . "/public/index.php?route=dashboard");
            exit();
        }

        $is_own_profile = ($target_user_id === (int)$auth['user_id']);

        include __DIR__ . '/../Views/profile/view.php';
    }

    public function edit() {
        require_once __DIR__ . '/../../core/bootstrap.php';
        require_login();

        $auth = auth_context();
        global $conn;
        $error = '';
        $success = '';

        if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
            csrfValidate();
            
            $name = trim($_POST['name'] ?? '');
            $per_no = trim($_POST['per_no'] ?? '');
            $pan_no = trim($_POST['pan_no'] ?? '');
            $apaar_id = trim($_POST['apaar_id'] ?? '');
            $highest_degree = trim($_POST['highest_degree'] ?? '');
            $university = trim($_POST['university'] ?? '');
            $specialization = trim($_POST['specialization'] ?? '');
            $doj_institution = trim($_POST['doj_institution'] ?? '');
            $doj_department = trim($_POST['doj_department'] ?? '');
            $experience_years = max(0.0, round((float)($_POST['experience_years'] ?? 0), 2));
            $designation_joining = trim($_POST['designation_joining'] ?? '');
            $designation_present = trim($_POST['designation_present'] ?? '');
            $date_designated_prof = trim($_POST['date_designated_prof'] ?? '');
            $association_nature = trim($_POST['association_nature'] ?? 'Regular');
            $contract_type = trim($_POST['contract_type'] ?? '');
            $is_currently_associated = isset($_POST['is_currently_associated']) && $_POST['is_currently_associated'] === '1' ? 1 : 0;
            $date_of_leaving = trim($_POST['date_of_leaving'] ?? '');
            
            if (empty($doj_department)) $doj_department = null;
            if (empty($date_designated_prof)) $date_designated_prof = null;
            if (empty($contract_type)) $contract_type = null;
            if (empty($date_of_leaving) || $is_currently_associated) $date_of_leaving = null;
            if (empty($apaar_id)) $apaar_id = null;
            if (empty($doj_institution)) $doj_institution = null;
            
            if (empty($name)) {
                $error = 'Name cannot be empty.';
            } else {
                $check_sql = "SELECT user_id FROM user_profiles WHERE user_id != ? AND (";
                $conditions = [];
                $params = [$auth['user_id']];
                $types = "i";
                if ($per_no !== '') {
                    $conditions[] = "per_no = ?";
                    $params[] = $per_no;
                    $types .= "s";
                }
                if ($pan_no !== '') {
                    $conditions[] = "pan_no = ?";
                    $params[] = $pan_no;
                    $types .= "s";
                }
                if ($apaar_id !== '' && $apaar_id !== null) {
                    $conditions[] = "apaar_id = ?";
                    $params[] = $apaar_id;
                    $types .= "s";
                }
                
                if (!empty($conditions)) {
                    $check_sql .= implode(" OR ", $conditions) . ")";
                    $stmt = $conn->prepare($check_sql);
                    if ($stmt) {
                        $stmt->bind_param($types, ...$params);
                        $stmt->execute();
                        if ($stmt->get_result()->num_rows > 0) {
                            $error = 'Another user is already using this Per No, PAN No, or APAAR ID.';
                        }
                        $stmt->close();
                    } else {
                        $error = 'Database error verifying unique credentials.';
                    }
                }

                if (empty($error)) {
                    $conn->begin_transaction();
                try {
                    $stmt = $conn->prepare("UPDATE users SET full_name = ? WHERE user_id = ?");
                    if (!$stmt) {
                        throw new \Exception("Database error preparing name update.");
                    }
                    $stmt->bind_param("si", $name, $auth['user_id']);
                    $stmt->execute();
                    $stmt->close();

                    $update_photo = false;
                    $profile_photo_path = null;
                    $old_photo_to_delete = null;
                    if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
                        $photo_ext = strtolower(pathinfo($_FILES['profile_photo']['name'] ?? '', PATHINFO_EXTENSION));
                        if (!in_array($photo_ext, ['jpg', 'jpeg', 'png', 'webp'], true)) {
                            throw new \Exception('Profile photo must be a valid image file (JPG, PNG, or WebP).');
                        }
                        require_once __DIR__ . '/../../core/file_service.php';
                        $upload_result = file_store($_FILES['profile_photo'], 'profiles', 'photo');
                        if ($upload_result['success']) {
                            $profile_photo_path = $upload_result['data']['file_path'];
                            $update_photo = true;

                            // Stage previous profile photo on disk for removal only after commit succeeds
                            $old_photo_stmt = $conn->prepare("SELECT profile_photo FROM user_profiles WHERE user_id = ?");
                            if ($old_photo_stmt) {
                                $old_photo_stmt->bind_param("i", $auth['user_id']);
                                $old_photo_stmt->execute();
                                $old_photo_res = $old_photo_stmt->get_result()->fetch_assoc();
                                $old_photo_stmt->close();
                                if (!empty($old_photo_res['profile_photo'])) {
                                    $prev_file = (defined('ROOT_PATH') ? ROOT_PATH : dirname(__DIR__, 2)) . '/' . ltrim($old_photo_res['profile_photo'], '/\\');
                                    if (file_exists($prev_file) && is_file($prev_file)) {
                                        $old_photo_to_delete = $prev_file;
                                    }
                                }
                            }
                        } else {
                            throw new \Exception('Profile photo upload failed: ' . ($upload_result['error'] ?? 'Invalid file format or size.'));
                        }
                    }

                    if ($update_photo) {
                        $stmt = $conn->prepare("
                            INSERT INTO user_profiles (
                                user_id, per_no, pan_no, apaar_id, highest_degree, university, specialization, 
                                doj_institution, doj_department, experience_years, designation_joining, 
                                designation_present, date_designated_prof, association_nature, 
                                contract_type, is_currently_associated, date_of_leaving, profile_photo
                            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?) 
                            ON DUPLICATE KEY UPDATE 
                                per_no = VALUES(per_no), pan_no = VALUES(pan_no), apaar_id = VALUES(apaar_id),
                                highest_degree = VALUES(highest_degree), university = VALUES(university), 
                                specialization = VALUES(specialization), doj_institution = VALUES(doj_institution),
                                doj_department = VALUES(doj_department), experience_years = VALUES(experience_years),
                                designation_joining = VALUES(designation_joining), designation_present = VALUES(designation_present),
                                date_designated_prof = VALUES(date_designated_prof), association_nature = VALUES(association_nature),
                                contract_type = VALUES(contract_type), is_currently_associated = VALUES(is_currently_associated),
                                date_of_leaving = VALUES(date_of_leaving), profile_photo = VALUES(profile_photo)
                        ");
                        if (!$stmt) {
                            throw new \Exception("Database error preparing profile photo statement.");
                        }
                        $stmt->bind_param("issssssssdsssssiss", $auth['user_id'], $per_no, $pan_no, $apaar_id, $highest_degree, $university, $specialization, $doj_institution, $doj_department, $experience_years, $designation_joining, $designation_present, $date_designated_prof, $association_nature, $contract_type, $is_currently_associated, $date_of_leaving, $profile_photo_path);
                    } else {
                        $stmt = $conn->prepare("
                            INSERT INTO user_profiles (
                                user_id, per_no, pan_no, apaar_id, highest_degree, university, specialization, 
                                doj_institution, doj_department, experience_years, designation_joining, 
                                designation_present, date_designated_prof, association_nature, 
                                contract_type, is_currently_associated, date_of_leaving
                            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?) 
                            ON DUPLICATE KEY UPDATE 
                                per_no = VALUES(per_no), pan_no = VALUES(pan_no), apaar_id = VALUES(apaar_id),
                                highest_degree = VALUES(highest_degree), university = VALUES(university), 
                                specialization = VALUES(specialization), doj_institution = VALUES(doj_institution),
                                doj_department = VALUES(doj_department), experience_years = VALUES(experience_years),
                                designation_joining = VALUES(designation_joining), designation_present = VALUES(designation_present),
                                date_designated_prof = VALUES(date_designated_prof), association_nature = VALUES(association_nature),
                                contract_type = VALUES(contract_type), is_currently_associated = VALUES(is_currently_associated),
                                date_of_leaving = VALUES(date_of_leaving)
                        ");
                        if (!$stmt) {
                            throw new \Exception("Database error preparing profile statement.");
                        }
                        $stmt->bind_param("issssssssdsssssis", $auth['user_id'], $per_no, $pan_no, $apaar_id, $highest_degree, $university, $specialization, $doj_institution, $doj_department, $experience_years, $designation_joining, $designation_present, $date_designated_prof, $association_nature, $contract_type, $is_currently_associated, $date_of_leaving);
                    }
                    
                    $stmt->execute();
                    $stmt->close();

                    $conn->commit();
                    $success = 'Profile updated successfully!';

                    // Safely remove superseded photo on disk only after DB commit succeeds
                    if ($old_photo_to_delete && file_exists($old_photo_to_delete)) {
                        @unlink($old_photo_to_delete);
                    }
                    
                    // Update auth context name
                    $_SESSION[SESSION_AUTH_KEY]['full_name'] = $name;
                } catch (\Throwable $e) {
                    $conn->rollback();
                    // On failure, clean up the newly uploaded file from disk to avoid orphaned files
                    if ($update_photo && !empty($profile_photo_path)) {
                        $new_file = (defined('ROOT_PATH') ? ROOT_PATH : dirname(__DIR__, 2)) . '/' . ltrim($profile_photo_path, '/\\');
                        if (file_exists($new_file) && is_file($new_file)) {
                            @unlink($new_file);
                        }
                    }
                    $error = 'Failed to update profile: ' . $e->getMessage();
                }
                }
            }
        }

        $profile = null;
        $stmt = $conn->prepare("SELECT u.full_name as name, u.email, p.* FROM users u LEFT JOIN user_profiles p ON u.user_id = p.user_id WHERE u.user_id = ?");
        if ($stmt) {
            $stmt->bind_param("i", $auth['user_id']);
            $stmt->execute();
            $profile = $stmt->get_result()->fetch_assoc();
            $stmt->close();
        }

        include __DIR__ . '/../Views/profile/edit.php';
    }
}
