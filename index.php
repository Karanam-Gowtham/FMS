<?php
require_once __DIR__ . '/core/bootstrap.php';
$isLoggedIn = auth_is_logged_in();

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
    <?php include_once HEADER; ?>

    <main class="hero">
        <div class="container">
            <div class="hero-content">

                <h2>Welcome to GMRIT</h2>

                <h1>File Management System</h1>

                <div class="description">
                    <p>
                        This is a user-friendly platform designed to store, organize, and manage files efficiently.
                        It allows users to upload, search, retrieve, and share files securely with role-based access
                        controls. Simplify file handling with our intuitive and reliable solution. Designed for
                        efficiency and collaboration, it ensures data protection and easy accessibility.
                    </p>
                </div>

                <div class="hero-buttons">

                    <?php if (!$isLoggedIn): ?>

                        <!-- Guest User -->
                        <a href="<?php echo BASE_URL; ?>/public/index.php?route=auth/login"
                            class="hero-btn hero-btn-primary">
                            Sign In
                        </a>

                        <a href="<?php echo BASE_URL; ?>/public/index.php?route=auth/register"
                            class="hero-btn hero-btn-outline">
                            Register
                        </a>

                    <?php else: ?>

                        <!-- Logged-in User -->
                        <a href="<?php echo BASE_URL; ?>/public/index.php?route=dashboard"
                            class="hero-btn hero-btn-primary">
                            Dashboard
                        </a>

                        <a href="<?php echo BASE_URL; ?>/public/index.php?route=profile/edit"
                            class="hero-btn hero-btn-outline">
                            Edit Profile
                        </a>

                    <?php endif; ?>

                </div>

            </div>
        </div>
    </main>

</body>

</html>