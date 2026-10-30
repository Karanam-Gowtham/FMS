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
    // Make BASE URL and CSRF Token available to JavaScript
    window.FMS_BASE_URL = <?= json_encode($app_url, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
    window.FMS_CSRF_TOKEN = <?= json_encode(function_exists('csrfToken') ? csrfToken() : ($_SESSION['csrf_token'] ?? ''), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
</script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer">
<script src="<?= htmlspecialchars($app_url, ENT_QUOTES, 'UTF-8') ?>/assets/js/main.js" defer></script>
<script src="<?= htmlspecialchars($app_url, ENT_QUOTES, 'UTF-8') ?>/assets/js/accreditation.js?v=2.2"></script>


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
                 HOME
            =================================================== -->
            <a href="<?= $app_url ?>" class="nav-btn-link" style="display: flex; align-items: center; gap: 5px;">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                  <path d="M8.354 1.146a.5.5 0 0 0-.708 0l-6 6A.5.5 0 0 0 1.5 7.5v7a.5.5 0 0 0 .5.5h4.5a.5.5 0 0 0 .5-.5v-4h2v4a.5.5 0 0 0 .5.5H14a.5.5 0 0 0 .5-.5v-7a.5.5 0 0 0-.146-.354L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293z"/>
                </svg>
                Home
            </a>

            <!-- ==================================================
                 DEPARTMENTS
            =================================================== -->
            <div class="dropdown">
                <button type="button" class="dropdown-toggle">
                    Departments
                </button>
                <div class="dropdown-content">
                    <a href="<?= $app_url ?>/public/index.php?route=public/department&amp;dept=CSE">CSE</a>
                    <a href="<?= $app_url ?>/public/index.php?route=public/department&amp;dept=CSE-CS">CSE-CS</a>
                    <a href="<?= $app_url ?>/public/index.php?route=public/department&amp;dept=AIML">CSE-AI&amp;ML</a>
                    <a href="<?= $app_url ?>/public/index.php?route=public/department&amp;dept=AIDS">CSE-AI&amp;DS</a>
                    <a href="<?= $app_url ?>/public/index.php?route=public/department&amp;dept=IT">IT</a>
                    <a href="<?= $app_url ?>/public/index.php?route=public/department&amp;dept=ECE">ECE</a>
                    <a href="<?= $app_url ?>/public/index.php?route=public/department&amp;dept=EEE">EEE</a>
                    <a href="<?= $app_url ?>/public/index.php?route=public/department&amp;dept=MECH">MECH</a>
                    <a href="<?= $app_url ?>/public/index.php?route=public/department&amp;dept=CIVIL">CIVIL</a>
                    <a href="<?= $app_url ?>/public/index.php?route=public/department&amp;dept=MatheMatics">MatheMatics</a>
                    <a href="<?= $app_url ?>/public/index.php?route=public/department&amp;dept=Physics">Physics</a>
                    <a href="<?= $app_url ?>/public/index.php?route=public/department&amp;dept=Chemistry">Chemistry</a>
                    <a href="<?= $app_url ?>/public/index.php?route=public/department&amp;dept=BSH">BSH</a>
                </div>
            </div>

            <!-- ==================================================
                 CHECK LOGIN STATUS
            =================================================== -->

            <?php
            $is_logged_in = auth_is_logged_in();
            $header_auth = $is_logged_in ? auth_context() : null;
            $header_role_id = $header_auth ? (int)($header_auth['active_role']['role_id'] ?? 0) : 0;
            ?>

            <!-- ==================================================
                 MORE MODULES
            =================================================== -->
            <div class="dropdown">
                <button type="button" class="dropdown-toggle">
                    More
                </button>
                <div class="dropdown-content">
                    <a href="<?= $app_url ?>/public/index.php?route=nba/dashboard">NBA</a>
                    <a href="<?= $app_url ?>/public/index.php?route=naac/dashboard">NAAC</a>
                    <a href="<?= $app_url ?>/public/index.php?route=student_activities/dashboard">Student Activities</a>
                    <?php if (in_array($header_role_id, [ROLE_ADMIN, ROLE_IQAC, ROLE_CENTRAL_COORDINATOR, ROLE_HOD], true)): ?>
                    <a href="<?= $app_url ?>/public/index.php?route=academic_years/list">Academic Years</a>
                    <?php endif; ?>
                    <?php if (in_array($header_role_id, [ROLE_ADMIN, ROLE_IQAC], true)): ?>
                    <a href="<?= $app_url ?>/public/index.php?route=admin/audit_logs">Audit Trail</a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- ==================================================
                 LOGGED IN
            =================================================== -->

            <?php if ($is_logged_in): ?>

                <!-- Dashboard -->
                <?php
                $header_dashboard_url = !empty($header_auth['active_role'])
                    ? get_role_landing_url($header_auth['active_role'])
                    : ($app_url . '/public/index.php?route=auth/select_role');
                ?>
                <a href="<?= htmlspecialchars($header_dashboard_url) ?>" class="nav-btn-link dashboard-link">
                    Dashboard
                </a>

                <?php if ($header_role_id === ROLE_ADMIN): ?>
                <!-- User Management Link for Admin -->
                <a href="<?= $app_url ?>/public/index.php?route=admin/users" class="nav-btn-link dashboard-link">
                    Users & Leadership
                </a>
                <!-- Audit Trail Direct Link for Admin -->
                <a href="<?= $app_url ?>/public/index.php?route=admin/audit_logs" class="nav-btn-link dashboard-link">
                    Audit Trail
                </a>
                <!-- Academic Years Direct Link for Admin -->
                <a href="<?= $app_url ?>/public/index.php?route=academic_years/list" class="nav-btn-link dashboard-link">
                    Academic Years
                </a>
                <?php endif; ?>

                <?php if ($header_role_id === ROLE_FACULTY): ?>
                <!-- Edit Profile -->
                <a href="<?= $app_url ?>/public/index.php?route=profile/edit" class="nav-btn-link dashboard-link">
                    Edit Profile
                </a>
                <?php endif; ?>

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
           DASHBOARD NOTIFICATION BADGE (Optional)
        ======================================================== */
        const badge = document.getElementById("dashboard-badge");
        if (badge) {
            badge.style.display = "none";
        }


    });

</script>

<div id="toast" class="toast" style="display: none; position: fixed !important; top: 90px !important; right: 24px !important; bottom: auto !important; left: auto !important; width: auto !important; max-width: 380px !important; height: auto !important; min-height: unset !important; padding: 12px 24px !important; border-radius: 8px !important; background: #10b981 !important; color: #fff !important; font-weight: 600 !important; font-size: 0.95rem !important; box-shadow: 0 10px 25px -3px rgba(0,0,0,0.3) !important; z-index: 99999 !important; pointer-events: none !important; box-sizing: border-box !important;">Data saved successfully!</div>