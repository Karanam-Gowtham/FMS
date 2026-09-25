<?php
namespace App\Controllers;

class ProfileController {
    public function view() {
        require_once __DIR__ . '/../../core/bootstrap.php';
        require_login();

        $auth = auth_context();
        global $conn;

        $stmt = $conn->prepare("SELECT u.full_name as name, u.email, p.* FROM users u LEFT JOIN user_profiles p ON u.user_id = p.user_id WHERE u.user_id = ?");
        $stmt->bind_param("i", $auth['user_id']);
        $stmt->execute();
        $profile = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        include __DIR__ . '/../Views/profile/view.php';
    }

    public function edit() {
        require_once __DIR__ . '/../../core/bootstrap.php';
        require_login();

        $auth = auth_context();
        global $conn;
        $error = '';
        $success = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            csrfValidate();
            
            $name = trim($_POST['name'] ?? '');
            $pan_no = trim($_POST['pan_no'] ?? '');
            $apaar_id = trim($_POST['apaar_id'] ?? '');
            $highest_degree = trim($_POST['highest_degree'] ?? '');
            $university = trim($_POST['university'] ?? '');
            $specialization = trim($_POST['specialization'] ?? '');
            $doj_institution = trim($_POST['doj_institution'] ?? '');
            $doj_department = trim($_POST['doj_department'] ?? '');
            $experience_years = (float)($_POST['experience_years'] ?? 0);
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
                $conn->begin_transaction();
                try {
                    $stmt = $conn->prepare("UPDATE users SET full_name = ? WHERE user_id = ?");
                    $stmt->bind_param("si", $name, $auth['user_id']);
                    $stmt->execute();
                    $stmt->close();

                    $stmt = $conn->prepare("
                        INSERT INTO user_profiles (
                            user_id, pan_no, apaar_id, highest_degree, university, specialization, 
                            doj_institution, doj_department, experience_years, designation_joining, 
                            designation_present, date_designated_prof, association_nature, 
                            contract_type, is_currently_associated, date_of_leaving
                        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?) 
                        ON DUPLICATE KEY UPDATE 
                            pan_no = VALUES(pan_no),
                            apaar_id = VALUES(apaar_id),
                            highest_degree = VALUES(highest_degree), 
                            university = VALUES(university), 
                            specialization = VALUES(specialization),
                            doj_institution = VALUES(doj_institution),
                            doj_department = VALUES(doj_department),
                            experience_years = VALUES(experience_years),
                            designation_joining = VALUES(designation_joining),
                            designation_present = VALUES(designation_present),
                            date_designated_prof = VALUES(date_designated_prof),
                            association_nature = VALUES(association_nature),
                            contract_type = VALUES(contract_type),
                            is_currently_associated = VALUES(is_currently_associated),
                            date_of_leaving = VALUES(date_of_leaving)
                    ");
                    
                    $stmt->bind_param(
                        "isssssssddssssis", 
                        $auth['user_id'], $pan_no, $apaar_id, $highest_degree, $university, $specialization,
                        $doj_institution, $doj_department, $experience_years, $designation_joining,
                        $designation_present, $date_designated_prof, $association_nature,
                        $contract_type, $is_currently_associated, $date_of_leaving
                    );
                    
                    $stmt->execute();
                    $stmt->close();

                    $conn->commit();
                    $success = 'Profile updated successfully!';
                    
                    // Update auth context name
                    $_SESSION['_fms_auth']['full_name'] = $name;
                } catch (\Exception $e) {
                    $conn->rollback();
                    $error = 'Failed to update profile: ' . $e->getMessage();
                }
            }
        }

        $stmt = $conn->prepare("SELECT u.full_name as name, u.email, p.* FROM users u LEFT JOIN user_profiles p ON u.user_id = p.user_id WHERE u.user_id = ?");
        $stmt->bind_param("i", $auth['user_id']);
        $stmt->execute();
        $profile = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        include __DIR__ . '/../Views/profile/edit.php';
    }
}
