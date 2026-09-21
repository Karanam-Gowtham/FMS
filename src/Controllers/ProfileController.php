<?php
namespace App\Controllers;

class ProfileController {
    public function view() {
        require_once __DIR__ . '/../../core/bootstrap.php';
        require_login();

        $auth = auth_context();
        global $conn;

        $stmt = $conn->prepare("SELECT u.name, u.email, p.* FROM users u LEFT JOIN user_profiles p ON u.user_id = p.user_id WHERE u.user_id = ?");
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
            $highest_degree = trim($_POST['highest_degree'] ?? '');
            $university = trim($_POST['university'] ?? '');
            $specialization = trim($_POST['specialization'] ?? '');
            
            if (empty($name)) {
                $error = 'Name cannot be empty.';
            } else {
                $conn->begin_transaction();
                try {
                    $stmt = $conn->prepare("UPDATE users SET name = ? WHERE user_id = ?");
                    $stmt->bind_param("si", $name, $auth['user_id']);
                    $stmt->execute();
                    $stmt->close();

                    $stmt = $conn->prepare("UPDATE user_profiles SET highest_degree = ?, university = ?, specialization = ? WHERE user_id = ?");
                    $stmt->bind_param("sssi", $highest_degree, $university, $specialization, $auth['user_id']);
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

        $stmt = $conn->prepare("SELECT u.name, u.email, p.* FROM users u LEFT JOIN user_profiles p ON u.user_id = p.user_id WHERE u.user_id = ?");
        $stmt->bind_param("i", $auth['user_id']);
        $stmt->execute();
        $profile = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        include __DIR__ . '/../Views/profile/edit.php';
    }
}
