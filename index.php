<?php
session_start(); // Start session
include "includes/connection.php";

// Define extra head content (CSS/Styles) before including the header
$extra_head = '<link rel="stylesheet" href="' . $base_url . 'assets/css/index1.css">
    <style>
        /* Hero Background Image Override */
        .hero::before {
            background-image: url("' . $base_url . 'assets/img/gmr_landing_page.jpg");
        }

        /* Header Styles */
header {
    position: fixed;
    width: 100%;
    background: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(8px);
    padding: 1rem 0;
    z-index: 100;
    position: fixed;
}

header .container1 {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding:0px 50px ;
}

.logo {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: #60a5fa;
}

.logo svg {
    width: 2rem;
    height: 2rem;
}

.logo span {
    font-size: 1.25rem;
    font-weight: bold;
}

a, button, .btn {
    text-decoration: none !important;
}

nav {
    display: flex;
    align-items: center;
    gap: 1.5rem;
}


.btn {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    border-radius: 0.5rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
}

.btn-primary {
    background: none;
    border: none;
    color: white;
}

.btn-primary:hover {
    color:#60a5fa;
}

.btn-outline {
    background: transparent;
    border: 2px solid #3b82f6;
    color: white;
}

.btn-outline:hover {
    background: #3b82f6;
}
.nav-menu {
    display: none; /* Hide by default */
}

.nav-menu.active {
    display: flex; /* Show when active */
}


/* Hamburger Menu Styles */
.hamburger {
    display: none;
    flex-direction: column;
    gap: 0.4rem;
    background: none;
    border: none;
    cursor: pointer;
    z-index: 110;
}

.hamburger span {
    width: 25px;
    height: 3px;
    background-color: white;
    transition: all 0.3s;
}

/* Hide nav by default */
.nav-menu {
    display: flex;
    gap: 1.5rem;
    transition: max-height 0.3s ease;
}

.nav-menu.hidden {
    max-height: 0;
    overflow: hidden;
    flex-direction: column;
    gap: 1rem;
}

@media (max-width: 768px) {
    .hamburger {
        display: flex;
    }

    nav {
        display: none;
    }

    .nav-menu {
        display: none;
        position: absolute;
        top: 100%;
        right: 0;
        width: 100%;
        background-color: rgba(0, 0, 0, 0.8);
        flex-direction: column;
        gap: 1rem;
        padding: 1rem;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.5);
    }

    .nav-menu.active {
        display: flex;
    }
}

</style>';

include 'includes/header.php'; // Include the header
?>
<main class="hero">
    <div class="container">
        <div class="hero-content">
            <?php
            // Check if the user is logged in
            if (isset($_SESSION['username'])) {
                // Logout logic when button is clicked ?>
                <a href="modules/faculty/edit_profile.php"><button class="btn1-outline1"
                        style="position: absolute; top: 90px; right: 20px; z-index: 2000 !important; white-space: nowrap;">Edit
                        Profile</button></a><?php
                // Logout logic previously here is now handled by header and top-page logic if needed
            }
            ?>




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



        </div>
    </div>
</main>
</body>

</html>