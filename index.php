<?php
session_start(); // Start session
include 'header.php'; // Include the header
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FMS</title>
    <link rel="stylesheet" href="./css/index1.css">
    <style>
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

a{
    text-decoration: none;
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

</style>
</head>
<body>
    <main class="hero">
        <div class="container">
            <div class="hero-content">
                <?php
                    // Check if the user is logged in
                    if (isset($_SESSION['username'])) {
                        // Logout logic when button is clicked?>
                        <a href="./edit_profile.php"><button class="btn1-outline1">Edit Profile</button></a><?php
                        if (isset($_POST['logout'])) {
                            session_unset();  // Unset all session variables
                            session_destroy(); // Destroy the session
                            echo"<script>alert('YOU successfully loged out'); window.location.href='index.php';</script>"; // Redirect to the login page"
                            
                        }
                        // Display the logout button if the user is logged in
                        echo '<form method="POST">
                                <button type="submit" name="logout" class="logout-btn1">Logout</button>
                              </form>';
                    } 

                ?>
                
                
                
                
                <h2>Welcome to GMRIT</h2>
                <h1>File Management System</h1>
                
                <div class="description">
                    <p>
                    This is a user-friendly platform designed to store, organize, and manage files efficiently. It allows users to upload, search, retrieve, and share files securely with role-based access controls. Simplify file handling with our intuitive and reliable solution. Designed for efficiency and collaboration, it ensures data protection and easy accessibility.
                    </p>
                </div>
                
               
                
            </div>
        </div>
    </main>
</body>
</html>
