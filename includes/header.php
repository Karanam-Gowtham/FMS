<?php
ob_start(); // Start output buffering
$base_url = (defined('BASE_URL') ? BASE_URL : '/mini/FMS') . '/';
if (isset($extra_head)) { echo $extra_head; }
?>
<style>
        body {
            margin: 0;
            padding: 0;
            padding-top: 70px; /* Globally clear the fixed header */
            font-family: 'Segoe UI', sans-serif;
        }

        .main-header-navbar {
            background-color: #111827;
            color: white;
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            height: 70px; /* Enforce strict height to prevent gap inconsistencies */
            padding: 0 5%; /* Adjusted padding since height is fixed */
            box-sizing: border-box;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }

        .main-header-navbar .logo img {
            height: 40px;
            padding: 5px;
            border-radius: 12px;
            width: auto;
        }

        .main-header-navbar .left-nav {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .main-header-navbar .home-icon {
            color: white;
            transition: color 0.2s;
            display: flex;
            align-items: center;
        }

        .main-header-navbar .home-icon:hover {
            color: #60a5fa;
        }

        .main-header-navbar .nav-links {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .main-header-navbar .nav-links a {
            color: #e2e8f0;
            text-decoration: none;
            font-family: 'Segoe UI', sans-serif;
            font-size: 0.9em;
            padding: 8px 14px;
            border-radius: 8px;
            transition: all 0.2s;
            cursor: pointer;
        }

        .main-header-navbar .nav-links button.nav-btn-link {
            color: #e2e8f0;
            background: transparent;
            border: none;
            font-family: 'Segoe UI', sans-serif;
            font-size: 0.9em;
            padding: 8px 14px;
            border-radius: 8px;
            transition: all 0.2s;
            cursor: pointer;
        }

        .main-header-navbar .nav-links a:hover, .main-header-navbar .nav-links button.nav-btn-link:hover {
            background: rgba(96, 165, 250, 0.1);
            color: #60a5fa;
        }

        /* Dropdown */
        .main-header-navbar .dropdown {
            position: relative;
            display: inline-block;
            padding: 6px 0;
        }

        .main-header-navbar .dropdown > span {
            cursor: pointer;
            padding: 8px 14px;
            border-radius: 8px;
            font-size: 0.9em;
            font-family: 'Segoe UI', sans-serif;
            transition: all 0.2s;
            color: #e2e8f0;
        }

        .main-header-navbar .dropdown > span:hover {
            background: rgba(96, 165, 250, 0.1);
            color: #60a5fa;
        }

        .main-header-navbar .dropdown-content {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            background-color: #1e293b;
            min-width: 180px;
            box-shadow: 0 12px 40px rgba(0,0,0,0.4);
            z-index: 1001;
            border-radius: 10px;
            max-height: 400px;
            overflow-y: auto;
            overflow-x: hidden;
            border: 1px solid #334155;
            margin-top: 2px;
        }

        /* Custom Scrollbar for Dropdown */
        .main-header-navbar .dropdown-content::-webkit-scrollbar {
            width: 6px;
        }
        .main-header-navbar .dropdown-content::-webkit-scrollbar-track {
            background: #1e293b;
            border-radius: 10px;
        }
        .main-header-navbar .dropdown-content::-webkit-scrollbar-thumb {
            background: #475569;
            border-radius: 10px;
        }
        .main-header-navbar .dropdown-content::-webkit-scrollbar-thumb:hover {
            background: #64748b;
        }

        .main-header-navbar .dropdown-content a {
            color: #e2e8f0 !important;
            padding: 11px 18px !important;
            display: block !important;
            text-decoration: none;
            border-bottom: 1px solid #293548;
            font-size: 0.88em !important;
            transition: all 0.2s;
            border-radius: 0 !important;
        }

        .main-header-navbar .dropdown-content a:last-child {
            border-bottom: none;
        }

        .main-header-navbar .dropdown-content a:hover {
            background-color: rgba(59, 130, 246, 0.15) !important;
            color: #60a5fa !important;
        }

        .main-header-navbar .dropdown:hover .dropdown-content {
            display: block;
        }

        /* Dashboard badge */
        .main-header-navbar .notif-badge {
            display: none;
            background: #ef4444;
            color: white;
            border-radius: 50%;
            padding: 2px 7px;
            font-size: 0.7em;
            font-weight: 700;
            margin-left: 4px;
            vertical-align: top;
            line-height: 1.4;
        }

        /* Auth buttons */
        .main-header-navbar .nav-btn {
            padding: 8px 18px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.88em;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            border: none;
            font-family: 'Segoe UI', sans-serif;
            display: inline-block;
        }

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

        /* Hamburger */
        .main-header-navbar .hamburger {
            display: none;
            flex-direction: column;
            gap: 5px;
            background: none;
            border: none;
            cursor: pointer;
            z-index: 1100;
            padding: 5px;
        }

        .main-header-navbar .hamburger span {
            width: 24px;
            height: 2.5px;
            background-color: white;
            border-radius: 2px;
            transition: all 0.3s;
        }

        @media (max-width: 768px) {
            .main-header-navbar .hamburger {
                display: flex;
            }
            .main-header-navbar .nav-links {
                display: none;
                position: absolute;
                top: 100%;
                right: 0;
                width: 100%;
                background-color: #111827;
                flex-direction: column;
                gap: 4px;
                padding: 15px;
                box-sizing: border-box;
                box-shadow: 0 10px 30px rgba(0,0,0,0.4);
                border-top: 1px solid #1e293b;
            }
            .main-header-navbar .nav-links.active {
                display: flex;
            }
            .main-header-navbar .dropdown-content {
                position: relative;
                box-shadow: none;
                border: none;
                background: #0f172a;
                margin-top: 0;
                border-radius: 6px;
                min-width: 100%;
                max-height: none; /* Disable scroll on mobile since parent container handles it */
                overflow: visible;
            }
        }
        /* --- Global Interaction Layer CSS --- */
        
        /* Page Load Transition */
        body {
            opacity: 0;
            transition: opacity 0.5s ease-in-out;
        }
        body.fms-loaded {
            opacity: 1;
        }

        /* Loading Button State */
        button.is-loading, input.is-loading {
            position: relative;
            pointer-events: none;
            opacity: 0.8;
        }
        .spinner {
            display: inline-block;
            width: 14px;
            height: 14px;
            border: 2px solid rgba(255,255,255,0.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
            margin-left: 8px;
            vertical-align: middle;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Toast Notifications */
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
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
            transform: translateX(120%);
            transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            min-width: 250px;
            border-left: 5px solid #3b82f6;
        }
        .fms-toast.show {
            transform: translateX(0);
        }
        .toast-success { border-left-color: #10b981; }
        .toast-error { border-left-color: #ef4444; }
        .toast-info { border-left-color: #3b82f6; }
        
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
    </style>
    <script src="<?php echo $base_url; ?>assets/js/main.js" defer></script>
    <header>
        <nav class="main-header-navbar">
            <div class="left-nav">
                <a href="<?php echo $base_url; ?>index.php" class="logo">
                    <img src="<?php echo $base_url; ?>assets/img/gmr_logo.png" alt="GMRIT Logo">
                </a>
                <a href="<?php echo $base_url; ?>index.php" class="home-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 10.5L12 3l9 7.5"/><path d="M5 10v11h14V10"/><path d="M9 21v-6h6v6"/>
                    </svg>
                </a>
            </div>

            <button class="hamburger" aria-label="Toggle navigation" onclick="document.querySelector('.nav-links').classList.toggle('active')">
                <span></span><span></span><span></span>
            </button>

            <div class="nav-links">
                
                <div class="dropdown">
                    <span>Departments &#9662;</span>
                    <div class="dropdown-content">
                        <a href="<?php echo $base_url; ?>public/dept.php?dept=CSE">CSE</a>
                        <a href="<?php echo $base_url; ?>public/dept.php?dept=CSE-CS">CSE-CS</a>
                        <a href="<?php echo $base_url; ?>public/dept.php?dept=CSE-AI&ML">CSE-AI&ML</a>
                        <a href="<?php echo $base_url; ?>public/dept.php?dept=CSE-AI&DS">CSE-AI&DS</a>
                        <a href="<?php echo $base_url; ?>public/dept.php?dept=IT">IT</a>
                        <a href="<?php echo $base_url; ?>public/dept.php?dept=ECE">ECE</a>
                        <a href="<?php echo $base_url; ?>public/dept.php?dept=EEE">EEE</a>
                        <a href="<?php echo $base_url; ?>public/dept.php?dept=MECH">MECH</a>
                        <a href="<?php echo $base_url; ?>public/dept.php?dept=CIVIL">CIVIL</a>
                        <a href="<?php echo $base_url; ?>public/dept.php?dept=MatheMatics">MatheMatics</a>
                        <a href="<?php echo $base_url; ?>public/dept.php?dept=Physics">Physics</a>
                        <a href="<?php echo $base_url; ?>public/dept.php?dept=Chemistry">Chemistry</a>
                        <a href="<?php echo $base_url; ?>public/dept.php?dept=BSH">BSH</a>
                    </div>
                </div>

                <div class="dropdown">
                    <span>Central Login &#9662;</span>
                    <div class="dropdown-content">
                        <a href="<?php echo $base_url; ?>modules/central/c_login_n.php?event=NAAC">NAAC</a>
                        <a href="<?php echo $base_url; ?>modules/central/c_login_n.php?event=NBA">NBA</a>
                        <a href="<?php echo $base_url; ?>modules/central/c_login.php?event=NCC">NCC</a>
                        <a href="<?php echo $base_url; ?>modules/central/c_login.php?event=Sports">Sports</a>
                        <a href="<?php echo $base_url; ?>modules/central/c_login.php?event=Clubs">Clubs & Professional Bodies</a>
                        <a href="<?php echo $base_url; ?>modules/central/c_login.php?event=NSS">NSS</a>
                        <a href="<?php echo $base_url; ?>modules/central/c_login.php?event=Women_Empowerment">Women Empowerment</a>
                        <a href="<?php echo $base_url; ?>modules/central/c_login.php?event=IIC">IIC</a>
                        <a href="<?php echo $base_url; ?>modules/central/c_login.php?event=PASH">PASH</a>
                        <a href="<?php echo $base_url; ?>modules/central/c_login.php?event=Antiragging">Antiragging</a>
                        <a href="<?php echo $base_url; ?>modules/central/c_login.php?event=SAC">SAC</a>
                        <a href="<?php echo $base_url; ?>modules/central/c_login.php?event=R&D">R&D</a>
                        <a href="<?php echo $base_url; ?>modules/central/c_login.php?event=IQAC">IQAC</a>
                        <a href="<?php echo $base_url; ?>modules/central/c_login.php?event=Exam_Section">Exam Section</a>
                    </div>
                </div>

                <?php if (isset($_SESSION['admin'])): ?>
                <div class="dropdown">
                    <span>Switch Depts &#9662;</span>
                    <div class="dropdown-content">
                        <a href="<?php echo $base_url; ?>admin/admins.php?dept=CSE">CSE</a>
                        <a href="<?php echo $base_url; ?>admin/admins.php?dept=CSE-CS">CSE-CS</a>
                        <a href="<?php echo $base_url; ?>admin/admins.php?dept=CSE-AI&ML">CSE-AI&ML</a>
                        <a href="<?php echo $base_url; ?>admin/admins.php?dept=CSE-AI&DS">CSE-AI&DS</a>
                        <a href="<?php echo $base_url; ?>admin/admins.php?dept=IT">IT</a>
                        <a href="<?php echo $base_url; ?>admin/admins.php?dept=ECE">ECE</a>
                        <a href="<?php echo $base_url; ?>admin/admins.php?dept=EEE">EEE</a>
                        <a href="<?php echo $base_url; ?>admin/admins.php?dept=MECH">MECH</a>
                        <a href="<?php echo $base_url; ?>admin/admins.php?dept=CIVIL">CIVIL</a>
                        <a href="<?php echo $base_url; ?>admin/admins.php?dept=MatheMatics">MatheMatics</a>
                        <a href="<?php echo $base_url; ?>admin/admins.php?dept=Physics">Physics</a>
                        <a href="<?php echo $base_url; ?>admin/admins.php?dept=Chemistry">Chemistry</a>
                        <a href="<?php echo $base_url; ?>admin/admins.php?dept=BSH">BSH</a>
                    </div>
                </div>
                <?php endif; ?>

                <a href="<?php echo $base_url; ?>modules/common/pdf_merger.php">Pdf Merger</a>

                <?php if (isset($_SESSION['username']) || isset($_SESSION['a_username']) || isset($_SESSION['j_username']) || isset($_SESSION['h_username']) || isset($_SESSION['admin']) || isset($_SESSION['c_cord'])): ?>
                    <a href="<?php echo $base_url; ?>dashboard.php" class="nav-btn-link" style="display:inline-flex; align-items:center;">
                        Dashboard <span id="dashboard-badge" class="notif-badge">0</span>
                    </a>
                    <a href="<?php echo $base_url; ?>modules/auth/logout.php" class="nav-btn nav-btn-logout">Logout</a>
                <?php else: ?>
                    <a href="<?php echo $base_url; ?>modules/auth/login.php" class="nav-btn">Sign In</a>
                    <a href="<?php echo $base_url; ?>modules/auth/reg.php" class="nav-btn" style="background-color: transparent; border: 1px solid white; margin-left: 10px;">Register</a>
                <?php endif; ?>
            </div>
        </nav>
    </header>

    <script>
        function updateDashboardBadge() {
            const baseUrl = "<?php echo $base_url; ?>";
            fetch(baseUrl + 'check_notifications.php')
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(data => {
                    const badge = document.getElementById('dashboard-badge');
                    if (data.count && data.count > 0) {
                        badge.textContent = data.count;
                        badge.style.display = 'inline-block';
                    } else {
                        badge.style.display = 'none';
                    }
                })
                .catch(error => console.error('Error fetching notifications:', error));
        }

        // Check notifications on load and every minute
        document.addEventListener('DOMContentLoaded', function() {
            updateDashboardBadge();
            setInterval(updateDashboardBadge, 60000);
        });
    </script>
