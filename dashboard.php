<?php
include_once 'config.php';
include_once CONNECTION_PATH;
include_once INCLUDES_PATH . '/helpers.php';

requireLogin();

$user = getCurrentUser($conn);
$roles = $_SESSION['roles'] ?? [];

// --- Handle Role Switching and Routing ---
if (count($roles) === 1 && !isset($_GET['switch_role'])) {
    $r = $roles[0];
    header("Location: " . BASE_URL . "/dashboard.php?switch_role=" . $r['role_id'] . "&dept_id=" . $r['dept_id']);
    exit();
}

if (isset($_GET['switch_role'])) {
    $sw_role_id = (int) $_GET['switch_role'];
    $sw_dept_id = (int) $_GET['dept_id'];

    foreach ($roles as $r) {
        if ($r['role_id'] == $sw_role_id && $r['dept_id'] == $sw_dept_id) {
            $role_name = $r['role_name'];
            $dept_name = $r['dept_name'];
            $email = $_SESSION['email'];

            // Set legacy session variables and route
            if ($role_name === 'Faculty') {
                $_SESSION['username'] = $email;
                header("Location: " . BASE_URL . "/modules/faculty/acd_year.php?dept=" . urlencode($dept_name));
                exit();
            } elseif ($role_name === 'Dept Coordinator') {
                $_SESSION['a_username'] = $email;
                header("Location: " . BASE_URL . "/modules/dept_coordinator/dc_acd_year.php?dept=" . urlencode($dept_name));
                exit();
            } elseif ($role_name === 'HOD') {
                $_SESSION['h_username'] = $email;
                header("Location: " . BASE_URL . "/HOD/see_uploads.php?dept=" . urlencode($dept_name) . "&designation=HOD");
                exit();
            } elseif ($role_name === 'Junior Assistant') {
                $_SESSION['j_username'] = $email;
                header("Location: " . BASE_URL . "/modules/jr_assistant/jr_acd_year.php?dept=" . urlencode($dept_name));
                exit();
            } elseif ($role_name === 'Admin') {
                $_SESSION['admin'] = $email;
                header("Location: " . BASE_URL . "/HOD/acd_year_aa.php?designation=admin");
                exit();
            } elseif ($role_name === 'Central Coordinator') {
                $_SESSION['c_username'] = $email;
                header("Location: " . BASE_URL . "/modules/central/c_aqar_files.php?designation=central_coordinator&event=" . urlencode($dept_name));
                exit();
            } elseif ($role_name === 'Criteria Coordinator') {
                $_SESSION['cri_username'] = $email;
                header("Location: " . BASE_URL . "/modules/central/c_aqar_files.php?designation=criteria_coordinator&event=" . urlencode($dept_name));
                exit();
            }
        }
    }
}

include_once HEADER;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - FMS</title>
    <style>
        body {
            background-color: #f1f5f9;
            font-family: 'Inter', -apple-system, sans-serif;
            color: #1e293b;
        }

        .gateway-container {
            max-width: 1200px;
            margin: 120px auto 50px;
            padding: 0 20px;
        }

        .gateway-header {
            margin-bottom: 40px;
            text-align: center;
        }

        .gateway-header h1 {
            font-size: 2.5rem;
            color: #0f172a;
            margin-bottom: 10px;
        }

        .gateway-header p {
            color: #64748b;
            font-size: 1.1rem;
        }

        .role-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 25px;
        }

        .role-card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            transition: transform 0.2s, box-shadow 0.2s;
            text-decoration: none;
            display: flex;
            flex-direction: column;
            border-top: 4px solid #3b82f6;
        }

        .role-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        .role-icon {
            font-size: 2rem;
            margin-bottom: 15px;
        }

        .role-dept {
            font-weight: 700;
            color: #3b82f6;
            font-size: 1.25rem;
            margin-bottom: 5px;
        }

        .role-name {
            color: #475569;
            font-size: 1rem;
            font-weight: 500;
        }
    </style>
</head>

<body>
    <div class="gateway-container">
        <div class="gateway-header">
            <h1>Welcome, <?php echo htmlspecialchars($user['full_name']); ?></h1>
            <p>Select a role to access your files and dashboards.</p>
        </div>

        <div class="role-grid">
            <?php if (empty($roles)): ?>
                <div
                    style="grid-column: 1 / -1; text-align: center; padding: 40px; background: white; border-radius: 12px; color: #64748b;">
                    No roles assigned. Please contact the administrator.
                </div>
            <?php else: ?>
                <?php foreach ($roles as $r): ?>
                    <a href="dashboard.php?switch_role=<?php echo $r['role_id']; ?>&dept_id=<?php echo $r['dept_id']; ?>"
                        class="role-card">
                        <div class="role-icon">
                            <?php
                            if ($r['role_name'] === 'Faculty')
                                echo '👨‍🏫';
                            elseif ($r['role_name'] === 'HOD')
                                echo '👑';
                            elseif (strpos($r['role_name'], 'Coordinator') !== false)
                                echo '📊';
                            else
                                echo '📁';
                            ?>
                        </div>
                        <div class="role-dept">
                            <?php echo htmlspecialchars($r['dept_name'] ? $r['dept_name'] : 'Institution'); ?>
                        </div>
                        <div class="role-name">
                            <?php echo htmlspecialchars($r['role_name']); ?>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>