<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!-- Header CSS -->
<link rel="stylesheet" href="../assets/css/header.css">
<style>
    /* Header Styles */
    header {
        position: fixed;
        top: 0;
        width: 100%;
        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(8px);
        padding: 1rem 0;
        z-index: 100;
    }

    header .container1 {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0px 20px;
        max-width: 100vw;
        box-sizing: border-box;
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

    a,
    button,
    .btn {
        text-decoration: none !important;
    }

    .a_ {
        text-decoration: none;
    }

    nav {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn {
        display: flex;
        align-items: center;
        gap: 0.3rem;
        padding: 0.3rem 0.5rem;
        border-radius: 0.5rem;
        font-weight: 500;
        font-size: 0.9rem;
        cursor: pointer;
        transition: all 0.3s;
    }

    .btn-primary {
        background: none;
        border: none;
        color: white;
    }

    .btn-primary:hover {
        color: #60a5fa;
    }

    .btn-outline {
        background: transparent;
        border: 2px solid #3b82f6;
        color: white;
    }

    .btn-outline:hover {
        background: #3b82f6;
    }

    /* Dropdown styles */
    .dropdown {
        position: relative;
        display: inline-block;
    }

    .dropdown-content {
        display: none;
        position: absolute;
        top: 100%;
        right: 30%;
        background-color: rgba(0, 0, 0, 0.9);
        min-width: 200px;
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        border-radius: 8px;
        z-index: 1000;
        margin-top: 1px;
    }

    .dropdown:hover .dropdown-content {
        display: block;
    }

    .dropdown-content a {
        color: white;
        padding: 12px 16px;
        text-decoration: none;
        display: block;
        transition: all 0.3s;
    }

    .dropdown-content a:hover {
        background-color: #3b82f6;
        border-radius: 4px;
    }

    /* Mobile menu styles */
    .nav-menu {
        display: flex;
        gap: 1.5rem;
    }

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

    .hm {
        margin-right: 10vw;
    }

    .cn {
        margin-right: 10vw;
    }

    .dp {
        margin-right: 7vw;
    }

    @media (max-width: 768px) {
        .hamburger {
            display: flex;
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

        .dropdown {
            width: 100%;
        }

        .dropdown-content {
            position: relative;
            width: 100%;
            display: none;
            opacity: 0;
            transform: translateY(-10px);
            transition: opacity 0.3s ease, transform 0.3s ease;
        }

        .dropdown:hover .dropdown-content {
            display: block;
            opacity: 1;
            transform: translateY(0);
        }

        .btn-outline {
            width: 100%;
            justify-content: center;
        }

        .dropdown-content a {
            padding: 15px;
            border-radius: 0;
            background-color: rgba(0, 0, 0, 0.5);
            margin: 2px 0;
        }

        .dropdown-content a:first-child {
            border-top-left-radius: 4px;
            border-top-right-radius: 4px;
        }

        .dropdown-content a:last-child {
            border-bottom-left-radius: 4px;
            border-bottom-right-radius: 4px;
        }
    }
</style>

<header>
    <div class="container1">
        <a class="a_" href="../index.php">
            <div class="logo">
                <img src="../assets/img/gmr_logo.jpg" width="100" height="34" alt="GMR Logo">
            </div>
        </a>

        <button class="hamburger" aria-label="Toggle navigation">
            <span></span>
            <span></span>
            <span></span>
        </button>

        <nav class="nav-menu">
            <a class="a_" href="../index.php">
                <button class="btn btn-primary hm">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                        <polyline points="9 22 9 12 15 12 15 22" />
                    </svg>
                    Home
                </button>
            </a>
            <div class="dropdown">
                <button class="btn btn-outline cn">Central</button>
                <div class="dropdown-content">
                    <a class="a_" href="../modules/central/c_login_n.php?event=NAAC">NAAC</a>
                    <a class="a_" href="../modules/central/c_login_n.php?event=NBA">NBA</a>
                    <a class="a_" href="../modules/central/c_login.php?event=NCC">NCC</a>
                    <a class="a_" href="../modules/central/c_login.php?event=Sports">Sports</a>
                    <a class="a_" href="../modules/central/c_login.php?event=Clubs">Clubs</a>
                    <a class="a_" href="../modules/central/c_login.php?event=NSS">NSS</a>
                    <a class="a_" href="../modules/central/c_login.php?event=Women_Empowerment">Women Empowerment</a>
                    <a class="a_" href="../modules/central/c_login.php?event=IIC">IIC</a>
                    <a class="a_" href="../modules/central/c_login.php?event=PASH">PASH</a>
                    <a class="a_" href="../modules/central/c_login.php?event=Antiragging">Antiragging</a>
                    <a class="a_" href="../modules/central/c_login.php?event=SAC">SAC</a>
                </div>
            </div>
            <!-- Since HOD usually only sees their own dept, we might want to hide this or keep it. Keeping it as per admin template for consistency if HOD navigates around. -->
            <div class="dropdown">
                <button class="btn btn-outline dp">Department</button>
                <div class="dropdown-content">
                    <a class="a_" href="../admin/admins.php?dept=CSE">CSE</a>
                    <a class="a_" href="../admin/admins.php?dept=AIML">AIML</a>
                    <a class="a_" href="../admin/admins.php?dept=AIDS">AIDS</a>
                    <a class="a_" href="../admin/admins.php?dept=IT">IT</a>
                    <a class="a_" href="../admin/admins.php?dept=ECE">ECE</a>
                    <a class="a_" href="../admin/admins.php?dept=EEE">EEE</a>
                    <a class="a_" href="../admin/admins.php?dept=MECH">MECH</a>
                    <a class="a_" href="../admin/admins.php?dept=CIVIL">CIVIL</a>
                    <a class="a_" href="../admin/admins.php?dept=BSH">BSH</a>
                </div>
            </div>
            <button class="btn dp btn-outline" onclick="openDashboard()">
                Dashboard
                <span id="dashboard-badge"
                    style="display:none; background:#ff4444; color:white; border-radius:50%; padding:2px 6px; font-size:10px; margin-left:5px; vertical-align: top;">0</span>
            </button>
            <a href="../modules/common/pdf_merger.php"><button class="btn dp btn-outline"
                    style="white-space: nowrap;">Pdf Merger</button></a>
            <?php if (isset($_SESSION['username']) || isset($_SESSION['a_username']) || isset($_SESSION['j_username']) || isset($_SESSION['h_username']) || isset($_SESSION['admin']) || isset($_SESSION['c_cord'])): ?>
                <a href="../modules/auth/logout.php"><button class="btn dp btn-outline"
                        style="border-color: #dc3545; color: white; white-space: nowrap;">Logout</button></a>
            <?php endif; ?>
        </nav>
    </div>
</header>

<!-- Dashboard Modal -->
<div id="dashboardModal"
    style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:9999;">
    <div
        style="position:relative; width:90%; height:90%; background:white; border-radius:10px; box-shadow:0 0 20px rgba(0,0,0,0.5); overflow:hidden; margin: 2.5% auto;">
        <button onclick="closeDashboard()"
            style="position:absolute; top:10px; right:15px; font-size:30px; line-height:30px; font-weight:bold; color:#333; background:white; border:none; cursor:pointer; z-index:10000; padding:0 5px; border-radius:50%;">&times;</button>
        <iframe id="dashboardFrame" src="" style="width:100%; height:100%; border:none;"></iframe>
    </div>
</div>

<script>
    function openDashboard() {
        var modal = document.getElementById('dashboardModal');
        var frame = document.getElementById('dashboardFrame');
        // Use absolute path to ensure it loads from anywhere
        var dashboardUrl = "/mini/FMS/dashboard.php?mode=iframe";

        if (!frame.src || frame.src === 'about:blank' || frame.src.indexOf('dashboard.php') === -1) {
            frame.src = dashboardUrl;
        }
        modal.style.display = "block";
        document.body.style.overflow = "hidden";
    }

    function closeDashboard() {
        document.getElementById('dashboardModal').style.display = "none";
        document.body.style.overflow = "auto";
        var frame = document.getElementById('dashboardFrame');
        frame.contentWindow.location.reload();
    }
</script>

<script>
    // Mobile menu toggle
    const hamburger = document.querySelector('.hamburger');
    const navMenu = document.querySelector('.nav-menu');

    hamburger.addEventListener('click', () => {
        navMenu.classList.toggle('active');
    });
</script>
<script>
    function updateDashboardBadge() {
        const baseUrl = "/mini/FMS/";
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

    document.addEventListener('DOMContentLoaded', updateDashboardBadge);
    setInterval(updateDashboardBadge, 30000); 
</script>