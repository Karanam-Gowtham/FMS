<?php
include_once 'config.php';
include_once CONNECTION_PATH;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once HEADER;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Management System - GMRIT</title>
    <link rel="stylesheet" href="<?php echo CSS_PATH . '/index1.css'; ?>">
</head>
<body>
    <main class="hero">
        <div class="container">
            <div class="hero-content">
                <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true): ?>
                    <a href="<?php echo PORTAL_PATH; ?>/faculty/edit_profile.php" class="btn-profile">
                        <button class="btn-outline">Edit Profile</button>
                    </a>
                <?php endif; ?>

                <h2>Welcome to GMRIT</h2>
                <h1>File Management System</h1>

                <div class="description">
                    <p>
                        This is a user-friendly platform designed to store, organize, and manage files efficiently. It
                        allows users to upload, search, retrieve, and share files securely with role-based access controls.
                        Simplify file handling with our intuitive and reliable solution. Designed for efficiency and
                        collaboration, it ensures data protection and easy accessibility.
                    </p>
                </div>

                <?php if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true): ?>
                <div class="hero-buttons">
                    <a href="<?php echo BASE_URL; ?>/modules/auth/login.php" class="hero-btn hero-btn-primary">
                        Sign In
                    </a>
                    <a href="<?php echo BASE_URL; ?>/modules/auth/reg.php" class="hero-btn hero-btn-outline">
                        Register
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
</body>
</html>