<?php

// ============================================================
// FMS - COMMON HEADER
// ============================================================

// Load config.php only once
require_once __DIR__ . '/../config.php';


// Start session if it is not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


// ============================================================
// BASE URL
// ============================================================

$app_url = rtrim(
    defined('BASE_URL') ? BASE_URL : '/mini/FMS',
    '/'
);


// ============================================================
// OPTIONAL EXTRA HEAD CONTENT
// ============================================================

if (isset($extra_head)) {
    echo $extra_head;
}

?>

<!-- ============================================================
     COMMON JAVASCRIPT
============================================================ -->

<script>
    // Make BASE URL available to JavaScript
    window.FMS_BASE_URL = <?= json_encode($app_url) ?>;
</script>

<script src="<?= htmlspecialchars($app_url, ENT_QUOTES, 'UTF-8') ?>/assets/js/main.js" defer>
</script>


<style>
    /* ============================================================
   GLOBAL BODY
============================================================ */

    body {
        margin: 0;
        padding-top: 85px;
        font-family: "Segoe UI", sans-serif;
    }


    /* ============================================================
   MAIN HEADER
============================================================ */

    .main-header-navbar {
        position: fixed;

        top: 0;
        left: 0;

        width: 100%;
        height: 70px;

        padding: 0 5%;

        box-sizing: border-box;

        display: flex;
        align-items: center;
        justify-content: space-between;

        background-color: #111827;

        color: white;

        z-index: 1000;

        border-bottom: 1px solid rgba(255, 255, 255, 0.08);

        backdrop-filter: blur(10px);
    }


    /* ============================================================
   LEFT SIDE
============================================================ */

    .main-header-navbar .left-nav {
        display: flex;

        align-items: center;

        gap: 20px;
    }


    /* ============================================================
   LOGO
============================================================ */

    .main-header-navbar .logo {
        display: flex;

        align-items: center;
    }

    .main-header-navbar .logo img {
        height: 40px;

        width: auto;

        padding: 5px;

        border-radius: 12px;
    }


    /* ============================================================
   NAVIGATION
============================================================ */

    .main-header-navbar .nav-links {
        display: flex;

        align-items: center;

        gap: 8px;
    }


    /* Normal navigation links */

    .main-header-navbar .nav-links>a,
    .main-header-navbar .nav-btn-link {

        color: #e2e8f0;

        text-decoration: none;

        font-size: 0.9em;

        padding: 8px 14px;

        border-radius: 8px;

        transition: all 0.2s;

    }


    /* Hover */

    .main-header-navbar .nav-links>a:hover,
    .main-header-navbar .nav-btn-link:hover {

        background: rgba(96, 165, 250, 0.1);

        color: #60a5fa;

    }


    /* ============================================================
   DROPDOWN
============================================================ */

    .main-header-navbar .dropdown {
        position: relative;
    }


    /* Dropdown button */

    .main-header-navbar .dropdown-toggle {

        color: #e2e8f0;

        background: transparent;

        border: none;

        font-family: "Segoe UI", sans-serif;

        font-size: 0.9em;

        padding: 8px 14px;

        border-radius: 8px;

        cursor: pointer;

        transition: all 0.2s;

    }


    /* Dropdown button hover */

    .main-header-navbar .dropdown-toggle:hover {

        background: rgba(96, 165, 250, 0.1);

        color: #60a5fa;

    }


    /* Arrow */

    .main-header-navbar .arrow {

        font-size: 0.75em;

        margin-left: 4px;

    }


    /* ============================================================
   DROPDOWN CONTENT
============================================================ */

    .main-header-navbar .dropdown-content {

        display: none;

        position: absolute;

        top: 100%;

        left: 0;

        min-width: 180px;

        max-height: 400px;

        overflow-y: auto;

        overflow-x: hidden;

        background-color: #1e293b;

        border: 1px solid #334155;

        border-radius: 10px;

        box-shadow: 0 12px 40px rgba(0, 0, 0, 0.4);

        z-index: 1001;

    }


    /* Desktop dropdown */

    .main-header-navbar .dropdown:hover .dropdown-content {

        display: block;

    }


    /* ============================================================
   DROPDOWN LINKS
============================================================ */

    .main-header-navbar .dropdown-content a {

        display: block;

        color: #e2e8f0;

        padding: 11px 18px;

        text-decoration: none;

        font-size: 0.88em;

        border-bottom: 1px solid #293548;

        transition: all 0.2s;

    }


    .main-header-navbar .dropdown-content a:last-child {

        border-bottom: none;

    }


    .main-header-navbar .dropdown-content a:hover {

        background-color: rgba(59, 130, 246, 0.15);

        color: #60a5fa;

    }


    /* ============================================================
   DROPDOWN SCROLLBAR
============================================================ */

    .main-header-navbar .dropdown-content::-webkit-scrollbar {

        width: 6px;

    }


    .main-header-navbar .dropdown-content::-webkit-scrollbar-track {

        background: #1e293b;

    }


    .main-header-navbar .dropdown-content::-webkit-scrollbar-thumb {

        background: #475569;

        border-radius: 10px;

    }


    .main-header-navbar .dropdown-content::-webkit-scrollbar-thumb:hover {

        background: #64748b;

    }


    /* ============================================================
   DASHBOARD NOTIFICATION BADGE
============================================================ */

    .main-header-navbar .notif-badge {

        display: none;

        background: #ef4444;

        color: white;

        border-radius: 50%;

        padding: 2px 7px;

        margin-left: 4px;

        font-size: 0.7em;

        font-weight: 700;

        line-height: 1.4;

    }


    /* ============================================================
   COMMON BUTTON
============================================================ */

    .main-header-navbar .nav-btn {

        display: inline-block;

        padding: 8px 18px;

        border-radius: 8px;

        font-size: 0.88em;

        font-weight: 600;

        text-decoration: none;

        cursor: pointer;

        transition: all 0.3s;

    }


    /* ============================================================
   LOGOUT
============================================================ */

    .main-header-navbar .nav-btn-logout {

        background: transparent;

        border: 1.5px solid #ef4444;

        color: #f87171;

        margin-left: 10px;

    }


    .main-header-navbar .nav-btn-logout:hover {

        background: #ef4444;

        color: white;

    }


    /* ============================================================
   REGISTER
============================================================ */

    .main-header-navbar .nav-btn-register {

        background: transparent;

        border: 1px solid white;

        color: white;

        margin-left: 10px;

    }


    .main-header-navbar .nav-btn-register:hover {

        background: white;

        color: #111827;

    }


    /* ============================================================
   HAMBURGER
============================================================ */

    .main-header-navbar .hamburger {

        display: none;

        flex-direction: column;

        gap: 5px;

        padding: 5px;

        background: transparent;

        border: none;

        cursor: pointer;

        z-index: 1100;

    }


    .main-header-navbar .hamburger span {

        width: 24px;

        height: 2.5px;

        background-color: white;

        border-radius: 2px;

        transition: all 0.3s;

    }


    /* ============================================================
   LOADING BUTTON
============================================================ */

    button.is-loading,
    input.is-loading {

        position: relative;

        pointer-events: none;

        opacity: 0.8;

    }


    /* ============================================================
   SPINNER
============================================================ */

    .spinner {

        display: inline-block;

        width: 14px;

        height: 14px;

        border: 2px solid rgba(255, 255, 255, 0.3);

        border-radius: 50%;

        border-top-color: #fff;

        animation: spin 1s ease-in-out infinite;

        margin-left: 8px;

        vertical-align: middle;

    }


    @keyframes spin {

        to {

            transform: rotate(360deg);

        }

    }


    /* ============================================================
   TOAST NOTIFICATIONS
============================================================ */

    #toast-container {

        position: fixed;

        bottom: 20px;

        right: 20px;

        z-index: 9999;

        display: flex;

        flex-direction: column;

        gap: 10px;

    }


    .fms-toast {

        display: flex;

        align-items: center;

        background: #1f2937;

        color: white;

        padding: 12px 20px;

        border-radius: 8px;

        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);

        transform: translateX(120%);

        transition:
            transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);

        min-width: 250px;

        border-left: 5px solid #3b82f6;

    }


    .fms-toast.show {

        transform: translateX(0);

    }


    .toast-success {

        border-left-color: #10b981;

    }


    .toast-error {

        border-left-color: #ef4444;

    }


    .toast-info {

        border-left-color: #3b82f6;

    }


    .toast-icon {

        margin-right: 12px;

        display: flex;

    }


    .toast-message {

        flex-grow: 1;

        font-size: 0.95rem;

    }


    .toast-close {

        background: transparent;

        border: none;

        color: #9ca3af;

        font-size: 1.2rem;

        cursor: pointer;

        margin-left: 15px;

        padding: 0;

        line-height: 1;

    }


    .toast-close:hover {

        color: white;

    }


    /* ============================================================
   MOBILE
============================================================ */

    @media (max-width: 768px) {


        /* Hamburger visible */

        .main-header-navbar .hamburger {

            display: flex;

        }


        /* Navigation hidden initially */

        .main-header-navbar .nav-links {

            display: none;

            position: absolute;

            top: 70px;

            right: 0;

            width: 100%;

            box-sizing: border-box;

            background-color: #111827;

            flex-direction: column;

            align-items: stretch;

            gap: 4px;

            padding: 15px;

            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4);

            border-top: 1px solid #1e293b;

        }


        /* Navigation opened */

        .main-header-navbar .nav-links.active {

            display: flex;

        }


        /* Mobile links */

        .main-header-navbar .nav-links>a,
        .main-header-navbar .nav-btn-link,
        .main-header-navbar .dropdown-toggle {

            width: 100%;

            box-sizing: border-box;

            text-align: left;

        }


        /* Mobile dropdown */

        .main-header-navbar .dropdown {

            width: 100%;

        }


        .main-header-navbar .dropdown-content {

            position: static;

            width: 100%;

            min-width: 100%;

            max-height: none;

            overflow: visible;

            box-shadow: none;

            border: none;

            background: #0f172a;

            border-radius: 6px;

        }


        /*
     * On mobile, don't use hover.
     * JavaScript will control the dropdown.
     */

        .main-header-navbar .dropdown:hover .dropdown-content {

            display: none;

        }


        .main-header-navbar .dropdown.open .dropdown-content {

            display: block;

        }


        /* Mobile buttons */

        .main-header-navbar .nav-btn-logout,
        .main-header-navbar .nav-btn-register {

            margin-left: 0;

        }

    }
</style>


<!-- ============================================================
     HEADER HTML
============================================================ -->

<header>

    <nav class="main-header-navbar">


        <!-- ====================================================
             LEFT SIDE
        ===================================================== -->

        <div class="left-nav">


            <!-- Logo -->

            <a href="<?= htmlspecialchars($app_url, ENT_QUOTES, 'UTF-8') ?>/index.php" class="logo">

                <img src="<?= htmlspecialchars($app_url, ENT_QUOTES, 'UTF-8') ?>/assets/img/gmr_logo.png"
                    alt="GMRIT Logo">

            </a>


            <!-- Breadcrumb -->

            <?php

            $breadcrumb_file = __DIR__ . '/breadcrumb.php';

            if (file_exists($breadcrumb_file)) {

                include $breadcrumb_file;

            }

            ?>

        </div>


        <!-- ====================================================
             MOBILE HAMBURGER
        ===================================================== -->

        <button type="button" class="hamburger" id="hamburger-btn" aria-label="Toggle navigation" aria-expanded="false">

            <span></span>

            <span></span>

            <span></span>

        </button>


        <!-- ====================================================
             NAVIGATION
        ===================================================== -->

        <div class="nav-links" id="nav-links">

            <!-- ==================================================
                 CHECK LOGIN STATUS
            =================================================== -->

            <?php
            $is_logged_in = auth_is_logged_in();
            ?>

            <!-- ==================================================
                 LOGGED IN
            =================================================== -->

            <?php if ($is_logged_in): ?>

                <!-- Dashboard -->
                <a href="<?= $app_url ?>/public/index.php?route=dashboard" class="nav-btn-link dashboard-link">
                    Dashboard
                </a>

                <!-- Edit Profile -->
                <a href="<?= $app_url ?>/public/index.php?route=profile/edit" class="nav-btn-link dashboard-link">
                    Edit Profile
                </a>

                <!-- Logout -->
                <a href="<?= $app_url ?>/public/index.php?route=auth/logout" class="nav-btn nav-btn-logout">
                    Logout
                </a>

            <!-- ==================================================
                 LOGGED OUT
            =================================================== -->

            <?php else: ?>

                <!-- Sign In -->
                <a href="<?= $app_url ?>/public/index.php?route=auth/login" class="nav-btn">
                    Sign In
                </a>

                <!-- Register -->
                <a href="<?= $app_url ?>/public/index.php?route=auth/register" class="nav-btn nav-btn-register">
                    Register
                </a>

            <?php endif; ?>

        </div>

    </nav>

</header>


<!-- ============================================================
     HEADER JAVASCRIPT
============================================================ -->

<script>

    document.addEventListener("DOMContentLoaded", function () {


        /* ========================================================
           MOBILE HAMBURGER
        ======================================================== */

        const hamburger =
            document.getElementById("hamburger-btn");

        const navLinks =
            document.getElementById("nav-links");


        if (hamburger && navLinks) {

            hamburger.addEventListener("click", function () {

                const isOpen =
                    navLinks.classList.toggle("active");

                hamburger.setAttribute(
                    "aria-expanded",
                    isOpen ? "true" : "false"
                );

            });

        }


        /* ========================================================
           MOBILE DROPDOWNS
        ======================================================== */

        const dropdowns =
            document.querySelectorAll(".dropdown");


        dropdowns.forEach(function (dropdown) {

            const toggle =
                dropdown.querySelector(".dropdown-toggle");


            if (!toggle) {
                return;
            }


            toggle.addEventListener("click", function (event) {

                /*
                 * On mobile JavaScript controls
                 * the dropdown.
                 *
                 * On desktop CSS hover controls it.
                 */

                if (window.innerWidth <= 768) {

                    event.preventDefault();

                    dropdown.classList.toggle("open");

                }

            });

        });


        /* ========================================================
           DASHBOARD NOTIFICATION BADGE
        ======================================================== */

        function updateDashboardBadge() {


            const badge =
                document.getElementById("dashboard-badge");


            /*
             * If the badge does not exist,
             * user is probably logged out.
             */

            if (!badge) {
                return;
            }


            const baseUrl =
                window.FMS_BASE_URL || "";


            fetch(
                baseUrl + "/check_notifications.php"
            )

                .then(function (response) {


                    if (!response.ok) {

                        throw new Error(
                            "Notification request failed"
                        );

                    }


                    return response.json();

                })


                .then(function (data) {


                    const count =
                        Number(data.count) || 0;


                    if (count > 0) {


                        badge.textContent = count;

                        badge.style.display =
                            "inline-block";


                    } else {


                        badge.style.display =
                            "none";


                    }

                })


                .catch(function (error) {


                    console.error(
                        "Notification error:",
                        error
                    );


                });

        }


        /* ========================================================
           INITIAL NOTIFICATION CHECK
        ======================================================== */

        updateDashboardBadge();


        /* ========================================================
           CHECK EVERY 60 SECONDS
        ======================================================== */

        setInterval(
            updateDashboardBadge,
            60000
        );


    });

</script>