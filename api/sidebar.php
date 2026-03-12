<?php
// sidebar.php
// Get current page name to highlight the active menu
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<!-- Note: FontAwesome and Google Fonts are loaded by the parent page's <head> -->

<style>
    /* Premium Dashboard Styles */
    body {
        font-family: 'Prompt', sans-serif;
        background-color: #f8fafc;
        margin: 0;
        overflow: hidden;
    }

    /* Sidebar Animations */
    .sidebar-transition {
        transition: all 0.3s ease-in-out;
    }

    .nav-item {
        position: relative;
        overflow: hidden;
    }

    .nav-item::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        height: 100%;
        width: 3px;
        background: #6366f1;
        transform: scaleY(0);
        transition: transform 0.2s ease;
        transform-origin: left;
    }

    .nav-item:hover::before,
    .nav-item.active::before {
        transform: scaleY(1);
    }

    .nav-item.active {
        background: linear-gradient(90deg, rgba(99, 102, 241, 0.1) 0%, transparent 100%);
        color: #6366f1;
    }

    .nav-item.active i {
        color: #6366f1;
    }

    /* Hover Glow */
    .hover-glow:hover {
        box-shadow: 0 0 15px rgba(99, 102, 241, 0.4);
    }

    /* Glassmorphism */
    .glass-header {
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border-bottom: 1px solid rgba(226, 232, 240, 0.6);
    }

    /* Hide scrollbar for sidebar */
    .no-scrollbar::-webkit-scrollbar {
        display: none;
    }

    .no-scrollbar {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }

    /* Sidebar Tooltip (Desktop collapsed) */
    .sidebar-tooltip {
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.2s;
        white-space: nowrap;
    }

    .sidebar-collapsed .nav-item:hover .sidebar-tooltip {
        opacity: 1;
        transform: translateX(0);
    }

    .sidebar-collapsed .menu-text {
        display: none;
    }

    .sidebar-collapsed .logo-text {
        display: none;
    }

    .sidebar-collapsed {
        width: 5rem;
    }

    /* =================================== */
    /* MOBILE BOTTOM TAB BAR               */
    /* =================================== */
    .mobile-bottom-nav {
        display: none;
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        height: 68px;
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        border-top: 1px solid rgba(99, 102, 241, 0.3);
        z-index: 100;
        padding-bottom: env(safe-area-inset-bottom, 4px);
        box-shadow: 0 -8px 32px rgba(0, 0, 0, 0.25);
    }

    .mobile-bottom-nav-inner {
        display: flex;
        height: 100%;
        align-items: stretch;
    }

    .mob-nav-item {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 3px;
        color: rgba(148, 163, 184, 0.8);
        text-decoration: none;
        font-size: 9px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        transition: all 0.2s ease;
        position: relative;
        padding-top: 4px;
    }

    .mob-nav-item i {
        font-size: 18px;
        transition: all 0.2s ease;
    }

    .mob-nav-item.active {
        color: #818cf8;
    }

    .mob-nav-item.active i {
        color: #818cf8;
        filter: drop-shadow(0 0 6px rgba(129, 140, 248, 0.6));
    }

    .mob-nav-item.active::before {
        content: '';
        position: absolute;
        top: 0;
        left: 20%;
        right: 20%;
        height: 2px;
        background: linear-gradient(90deg, #6366f1, #818cf8);
        border-radius: 0 0 4px 4px;
    }

    .mob-nav-item:active {
        transform: scale(0.92);
    }

    /* Logout tab special color */
    .mob-nav-item.logout-tab {
        color: rgba(239, 68, 68, 0.7);
    }

    .mob-nav-item.logout-tab.active,
    .mob-nav-item.logout-tab:hover {
        color: #f87171;
    }

    .mob-nav-item.logout-tab.active i {
        color: #f87171;
        filter: drop-shadow(0 0 6px rgba(239, 68, 68, 0.5));
    }

    @media (max-width: 767px) {
        .mobile-bottom-nav {
            display: block;
        }

        body {
            padding-bottom: 68px;
            overflow: auto !important;
        }

        #main-content-wrapper {
            overflow: auto !important;
        }

        main {
            overflow: visible !important;
        }

        /* Hide desktop sidebar on mobile */
        #desktop-sidebar {
            display: none !important;
        }
    }

    /* =================================== */
    /* MOBILE HEADER - GRADIENT/COLORED    */
    /* =================================== */
    .mobile-header {
        display: none;
        background: linear-gradient(135deg, #0f172a 0%, #312e81 50%, #1e1b4b 100%);
        height: 60px;
        padding: 0 16px;
        align-items: center;
        justify-content: space-between;
        position: sticky;
        top: 0;
        z-index: 50;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        border-bottom: 1px solid rgba(99, 102, 241, 0.4);
    }

    .mobile-header-logo {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .mobile-header-logo img {
        height: 32px;
        width: auto;
        object-fit: contain;
        border-radius: 8px;
        background: rgba(255, 255, 255, 0.9);
        padding: 3px;
    }

    .mobile-header-logo-text {
        font-family: 'Prompt', sans-serif;
        font-weight: 800;
        font-size: 17px;
        color: white;
        letter-spacing: -0.02em;
        line-height: 1;
    }

    .mobile-header-logo-text span {
        color: #818cf8;
        font-weight: 300;
    }

    .mobile-user-avatar {
        display: flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
    }

    .avatar-wrap {
        position: relative;
    }

    .avatar-circle {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background: linear-gradient(135deg, #6366f1, #818cf8);
        border: 2px solid rgba(129, 140, 248, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        font-weight: 800;
        color: white;
        letter-spacing: -0.02em;
        box-shadow: 0 0 12px rgba(99, 102, 241, 0.5);
        font-family: 'Prompt', sans-serif;
    }

    .avatar-crown {
        position: absolute;
        top: -8px;
        left: 50%;
        transform: translateX(-50%);
        font-size: 11px;
        line-height: 1;
    }

    .online-dot {
        position: absolute;
        bottom: 0;
        right: 0;
        width: 10px;
        height: 10px;
        background: #22c55e;
        border-radius: 50%;
        border: 2px solid #0f172a;
        animation: pulse-online 2s infinite;
    }

    @keyframes pulse-online {

        0%,
        100% {
            box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.4);
        }

        50% {
            box-shadow: 0 0 0 4px rgba(34, 197, 94, 0);
        }
    }

    .user-info-text {
        text-align: right;
    }

    .user-info-name {
        font-size: 12px;
        font-weight: 700;
        color: white;
        line-height: 1.2;
        font-family: 'Prompt', sans-serif;
        max-width: 90px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .user-info-status {
        display: flex;
        align-items: center;
        gap: 4px;
        justify-content: flex-end;
    }

    .user-info-status-dot {
        width: 6px;
        height: 6px;
        background: #22c55e;
        border-radius: 50%;
        animation: pulse-online 2s infinite;
    }

    .user-info-status-text {
        font-size: 9px;
        color: rgba(134, 239, 172, 0.9);
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        font-family: 'Prompt', sans-serif;
    }

    @media (max-width: 767px) {
        .mobile-header {
            display: flex;
        }

        .glass-header {
            display: none !important;
        }
    }
</style>

<!-- HIDDEN ELEMENTS TO PREVENT JS ERRORS FROM bom.php PREVIOUS NAV -->
<div style="display:none;">
    <div id="headerProjectName"></div>
    <div id="currentProjectBadge"></div>
    <div id="roleBadge"></div>
    <div id="roleBadgeMobile"></div>
</div>

<!-- ========================================== -->
<!-- MOBILE HEADER (Colored, App-style)         -->
<!-- ========================================== -->
<div class="mobile-header" id="mobileHeader">
    <!-- Left: Logo + Name -->
    <div class="mobile-header-logo">
        <img src="Mentra_Solution_Tranparency.png"
            onerror="this.src='https://cdn-icons-png.flaticon.com/512/2881/2881142.png'" alt="Mentra Logo">
        <div class="mobile-header-logo-text">
            Mentra <span>BOM</span>
        </div>
    </div>

    <!-- Right: User Avatar + Name + Online -->
    <div class="mobile-user-avatar"
        onclick="if(window.handleLogout) window.handleLogout(); else window.location.href='index.php';">
        <div class="user-info-text">
            <div class="user-info-name" id="mobileUserName">กำลังโหลด...</div>
            <div class="user-info-status">
                <div class="user-info-status-dot"></div>
                <span class="user-info-status-text">Online</span>
            </div>
        </div>
        <div class="avatar-wrap">
            <div id="mobileAvatarCrown" class="avatar-crown" style="display:none;">👑</div>
            <div class="avatar-circle" id="mobileAvatarInitial">?</div>
            <div class="online-dot"></div>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- DASHBOARD WRAPPER START                    -->
<!-- ========================================== -->
<div class="flex h-screen bg-slate-50 overflow-hidden font-prompt w-full">

    <!-- OVERLAY FOR MOBILE -->
    <div id="mobile-overlay"
        class="fixed inset-0 bg-slate-900/50 z-40 hidden md:hidden backdrop-blur-sm transition-opacity"
        onclick="toggleMobileSidebar()"></div>

    <!-- SIDEBAR (Desktop only) -->
    <aside id="desktop-sidebar"
        class="sidebar-transition fixed md:relative z-50 h-screen w-64 bg-slate-900 text-slate-300 border-r border-slate-800 flex flex-col shadow-2xl transform -translate-x-full md:translate-x-0">

        <!-- Logo Area -->
        <div class="h-16 flex items-center px-4 border-b border-slate-800/60 bg-slate-950/50 overflow-hidden">
            <div class="flex items-center flex-shrink-0">
                <div class="bg-white/95 p-1 rounded-lg shadow-sm border border-slate-700/50">
                    <img src="Mentra_Solution_Tranparency.png"
                        onerror="this.src='https://cdn-icons-png.flaticon.com/512/2881/2881142.png'" alt="Mentra Logo"
                        class="h-7 w-auto object-contain transition-all duration-300 logo-img">
                </div>
                <span class="logo-text ml-3 font-bold text-white tracking-wide text-lg whitespace-nowrap">
                    Mentra <span class="text-indigo-400 font-light">BOM</span>
                </span>
            </div>

            <!-- Close button for mobile -->
            <button class="md:hidden ml-auto text-slate-400 hover:text-white" onclick="toggleMobileSidebar()">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>

        <!-- Navigation Links -->
        <div class="flex-1 overflow-y-auto no-scrollbar py-4 px-3 space-y-1">
            <p class="logo-text px-3 text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2 mt-2">Main Menu
            </p>

            <a href="bom.php"
                class="nav-item flex items-center px-3 py-3 rounded-xl transition-all hover:bg-slate-800/50 hover:text-white group <?php echo $currentPage == 'bom.php' ? 'active' : ''; ?>">
                <i
                    class="fa-solid fa-cubes text-lg w-6 text-center group-hover:text-indigo-400 <?php echo $currentPage == 'bom.php' ? 'text-indigo-400' : 'text-slate-400'; ?> transition-colors"></i>
                <span class="menu-text ml-3 font-medium text-sm">BOM Manager</span>
                <div
                    class="sidebar-tooltip absolute left-14 bg-slate-800 text-white text-xs px-2 py-1 rounded shadow-lg z-50 ml-2">
                    BOM Manager</div>
            </a>

            <a href="drawings.php"
                class="nav-item flex items-center px-3 py-3 rounded-xl transition-all hover:bg-slate-800/50 hover:text-white group <?php echo $currentPage == 'drawings.php' ? 'active' : ''; ?>">
                <i
                    class="fa-regular fa-file-pdf text-lg w-6 text-center group-hover:text-indigo-400 <?php echo $currentPage == 'drawings.php' ? 'text-indigo-400' : 'text-slate-400'; ?> transition-colors"></i>
                <span class="menu-text ml-3 font-medium text-sm">แบบโครงสร้าง</span>
                <div
                    class="sidebar-tooltip absolute left-14 bg-slate-800 text-white text-xs px-2 py-1 rounded shadow-lg z-50 ml-2">
                    แบบโครงสร้าง (Drawings)</div>
            </a>

            <a href="calculator.php"
                class="nav-item flex items-center px-3 py-3 rounded-xl transition-all hover:bg-slate-800/50 hover:text-white group <?php echo $currentPage == 'calculator.php' ? 'active' : ''; ?>">
                <i
                    class="fa-solid fa-calculator text-lg w-6 text-center group-hover:text-indigo-400 <?php echo $currentPage == 'calculator.php' ? 'text-indigo-400' : 'text-slate-400'; ?> transition-colors"></i>
                <span class="menu-text ml-3 font-medium text-sm">คำนวณราคา</span>
                <div
                    class="sidebar-tooltip absolute left-14 bg-slate-800 text-white text-xs px-2 py-1 rounded shadow-lg z-50 ml-2">
                    คำนวณราคา (Calculator)</div>
            </a>

            <a href="payment.php"
                class="nav-item flex items-center px-3 py-3 rounded-xl transition-all hover:bg-slate-800/50 hover:text-white group <?php echo $currentPage == 'payment.php' ? 'active' : ''; ?>">
                <i
                    class="fa-solid fa-receipt text-lg w-6 text-center group-hover:text-indigo-400 <?php echo $currentPage == 'payment.php' ? 'text-indigo-400' : 'text-slate-400'; ?> transition-colors"></i>
                <span class="menu-text ml-3 font-medium text-sm">หลักฐานการโอน</span>
                <div
                    class="sidebar-tooltip absolute left-14 bg-slate-800 text-white text-xs px-2 py-1 rounded shadow-lg z-50 ml-2">
                    หลักฐานการโอนเงิน</div>
            </a>

            <a href="order_status.php"
                class="nav-item flex items-center px-3 py-3 rounded-xl transition-all hover:bg-slate-800/50 hover:text-white group <?php echo $currentPage == 'order_status.php' ? 'active' : ''; ?>">
                <i
                    class="fa-solid fa-truck-fast text-lg w-6 text-center group-hover:text-indigo-400 <?php echo $currentPage == 'order_status.php' ? 'text-indigo-400' : 'text-slate-400'; ?> transition-colors"></i>
                <span class="menu-text ml-3 font-medium text-sm">สถานะการสั่งซื้อ</span>
                <div
                    class="sidebar-tooltip absolute left-14 bg-slate-800 text-white text-xs px-2 py-1 rounded shadow-lg z-50 ml-2">
                    สถานะการสั่งซื้อ (Order Status)</div>
            </a>

            <p class="logo-text px-3 text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2 mt-6">System
            </p>

            <a href="logs.php"
                class="nav-item flex items-center px-3 py-3 rounded-xl transition-all hover:bg-slate-800/50 hover:text-white group <?php echo $currentPage == 'logs.php' ? 'active' : ''; ?>">
                <i
                    class="fa-solid fa-clock-rotate-left text-lg w-6 text-center group-hover:text-indigo-400 <?php echo $currentPage == 'logs.php' ? 'text-indigo-400' : 'text-slate-400'; ?> transition-colors"></i>
                <span class="menu-text ml-3 font-medium text-sm">ประวัติการทำรายการ</span>
                <div
                    class="sidebar-tooltip absolute left-14 bg-slate-800 text-white text-xs px-2 py-1 rounded shadow-lg z-50 ml-2">
                    ประวัติ (Logs)</div>
            </a>

        </div>

        <!-- Footer / User / Logout -->
        <div class="p-4 border-t border-slate-800/60 bg-slate-950/30">
            <!-- Desktop user info -->
            <div class="logo-text flex items-center gap-3 mb-3 px-1">
                <div class="relative flex-shrink-0">
                    <div class="w-9 h-9 rounded-full bg-gradient-to-br from-indigo-500 to-indigo-700 flex items-center justify-center font-bold text-white text-sm"
                        id="desktopAvatarInitial">?</div>
                    <div
                        class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-green-500 rounded-full border-2 border-slate-950">
                    </div>
                </div>
                <div class="min-w-0">
                    <div class="text-white font-bold text-xs truncate" id="desktopUserName">--</div>
                    <div class="text-slate-500 text-[9px] uppercase tracking-widest" id="desktopUserRole">--</div>
                </div>
            </div>
            <button
                onclick="if(window.handleLogout) { window.handleLogout(); } else { window.location.href='index.php'; }"
                class="w-full flex items-center justify-center gap-2 bg-red-500/10 hover:bg-red-500 text-red-500 hover:text-white py-2.5 rounded-xl transition-all duration-300 font-medium text-sm group">
                <i class="fa-solid fa-right-from-bracket group-hover:-translate-x-1 transition-transform"></i>
                <span class="menu-text">ออกจากระบบ</span>
            </button>
            <div class="logo-text text-center mt-3 text-[9px] text-slate-500">
                Mentra Solution Co., Ltd. <br> v3.0 Premium Dashboard
            </div>
        </div>
    </aside>

    <!-- RIGHT CONTENT AREA -->
    <div class="flex-1 flex flex-col h-screen overflow-hidden relative transition-all duration-300 w-full"
        id="main-content-wrapper">

        <!-- Header Topbar (Desktop only) -->
        <header
            class="glass-header sticky top-0 z-40 px-4 py-3 flex justify-between items-center shadow-sm h-16 w-full">
            <div class="flex items-center gap-3">
                <!-- Mobile Menu Button -->
                <button
                    class="md:hidden w-9 h-9 flex items-center justify-center rounded-lg bg-white border border-slate-200 text-slate-600 shadow-sm active:bg-slate-50"
                    onclick="toggleMobileSidebar()">
                    <i class="fa-solid fa-bars"></i>
                </button>

                <!-- Desktop Collapse Button -->
                <button
                    class="hidden md:flex w-8 h-8 items-center justify-center rounded-lg text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 transition-colors"
                    onclick="toggleDesktopSidebar()">
                    <i class="fa-solid fa-bars-staggered" id="collapseIcon"></i>
                </button>

                <!-- Dynamic Page Title based on the page -->
                <div class="flex flex-col">
                    <h2 class="font-bold text-slate-800 text-sm md:text-base leading-tight">
                        <?php
                        if ($currentPage == 'bom.php')
                            echo 'Project Workspace <span class="text-xs font-normal text-slate-500 ml-2 hidden sm:inline" id="visualProjectName"></span>';
                        elseif ($currentPage == 'drawings.php')
                            echo 'Drawings &amp; Documents';
                        elseif ($currentPage == 'calculator.php')
                            echo 'Price Calculator';
                        elseif ($currentPage == 'logs.php')
                            echo 'Action Logs';
                        elseif ($currentPage == 'manage_users.php')
                            echo 'User Management';
                        elseif ($currentPage == 'search.php')
                            echo '<i class="fa-solid fa-magnifying-glass text-indigo-500 mr-2"></i> ค้นหาวัสดุ';
                        elseif ($currentPage == 'payment.php')
                            echo '<i class="fa-solid fa-receipt text-emerald-500 mr-2"></i> หลักฐานการโอนเงิน';
                        elseif ($currentPage == 'order_status.php')
                            echo '<i class="fa-solid fa-truck-fast text-indigo-500 mr-2"></i> สถานะการสั่งซื้อ';
                        else
                            echo 'Dashboard';
                        ?>
                    </h2>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <!-- Desktop header user avatar -->
                <div class="flex items-center gap-2">
                    <div class="text-right hidden sm:block">
                        <div class="text-xs font-bold text-slate-700" id="headerUserName">--</div>
                        <div class="text-[9px] text-slate-400 uppercase tracking-widest" id="headerUserRole">--</div>
                    </div>
                    <div class="relative">
                        <div class="h-9 w-9 rounded-full bg-gradient-to-br from-indigo-500 to-indigo-700 flex items-center justify-center overflow-hidden border-2 border-indigo-200 shadow-sm cursor-pointer hover-glow font-bold text-white text-sm"
                            id="headerAvatarInitial">?</div>
                        <div
                            class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-green-500 rounded-full border-2 border-white">
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Scrolling Content Area -->
        <main class="flex-1 overflow-y-auto no-scrollbar w-full relative">
            <div
                class="absolute inset-0 z-0 bg-[radial-gradient(#e5e7eb_1px,transparent_1px)] [background-size:16px_16px] opacity-30 pointer-events-none">
            </div>
            <!-- PAGE CONTENT BEGINS HERE -->

            <script>
                // ==============================
                // Sidebar Toggle Functions
                // ==============================
                function toggleMobileSidebar() {
                    const sidebar = document.getElementById('desktop-sidebar');
                    const overlay = document.getElementById('mobile-overlay');

                    if (sidebar.classList.contains('-translate-x-full')) {
                        sidebar.classList.remove('-translate-x-full');
                        overlay.classList.remove('hidden');
                        setTimeout(() => overlay.classList.add('opacity-100'), 10);
                    } else {
                        sidebar.classList.add('-translate-x-full');
                        overlay.classList.remove('opacity-100');
                        setTimeout(() => overlay.classList.add('hidden'), 300);
                    }
                }

                function toggleDesktopSidebar() {
                    const sidebar = document.getElementById('desktop-sidebar');
                    sidebar.classList.toggle('sidebar-collapsed');
                }

                // ==============================
                // User Info from localStorage
                // ==============================
                document.addEventListener("DOMContentLoaded", () => {
                    // Sync project name in header
                    const hiddenProjectNode = document.getElementById('headerProjectName');
                    const visualProjectNode = document.getElementById('visualProjectName');

                    if (hiddenProjectNode && visualProjectNode) {
                        const observer = new MutationObserver(() => {
                            const text = hiddenProjectNode.innerText || hiddenProjectNode.textContent;
                            visualProjectNode.innerHTML = text ? `<i class="fa-regular fa-folder text-indigo-400 mr-1"></i> ${text}` : '';
                        });
                        observer.observe(hiddenProjectNode, { childList: true, characterData: true, subtree: true });
                    }

                    // Load user info
                    try {
                        const userStr = localStorage.getItem('mentra_user');
                        const roleStr = localStorage.getItem('mentra_role') || '';

                        if (userStr) {
                            const user = JSON.parse(userStr);
                            const name = user.name || 'Unknown';
                            const initial = name.charAt(0).toUpperCase();
                            const isAdmin = roleStr === 'admin';

                            // Desktop sidebar
                            const desktopInit = document.getElementById('desktopAvatarInitial');
                            const desktopName = document.getElementById('desktopUserName');
                            const desktopRole = document.getElementById('desktopUserRole');
                            if (desktopInit) desktopInit.textContent = initial;
                            if (desktopName) desktopName.textContent = name;
                            if (desktopRole) desktopRole.textContent = roleStr.toUpperCase();

                            // Desktop header
                            const headerName = document.getElementById('headerUserName');
                            const headerRole = document.getElementById('headerUserRole');
                            const headerInit = document.getElementById('headerAvatarInitial');
                            if (headerName) headerName.textContent = name;
                            if (headerRole) headerRole.textContent = roleStr.toUpperCase();
                            if (headerInit) headerInit.textContent = initial;

                            // Mobile header
                            const mobileUserName = document.getElementById('mobileUserName');
                            const mobileAvatarInitial = document.getElementById('mobileAvatarInitial');
                            const mobileAvatarCrown = document.getElementById('mobileAvatarCrown');
                            if (mobileUserName) mobileUserName.textContent = name;
                            if (mobileAvatarInitial) mobileAvatarInitial.textContent = initial;
                            if (mobileAvatarCrown && isAdmin) mobileAvatarCrown.style.display = 'block';
                        }
                    } catch (e) {
                        console.warn('Could not load user info:', e);
                    }
                });
            </script>

            <!-- ========================================== -->
            <!-- MOBILE BOTTOM TAB BAR                      -->
            <!-- ========================================== -->
            <nav class="mobile-bottom-nav" id="mobileBottomNav">
                <div class="mobile-bottom-nav-inner">

                    <a href="bom.php" class="mob-nav-item <?php echo $currentPage == 'bom.php' ? 'active' : ''; ?>">
                        <i class="fa-solid fa-cubes"></i>
                        <span>BOM</span>
                    </a>

                    <a href="calculator.php"
                        class="mob-nav-item <?php echo $currentPage == 'calculator.php' ? 'active' : ''; ?>">
                        <i class="fa-solid fa-calculator"></i>
                        <span>คำนวณ</span>
                    </a>

                    <a href="payment.php"
                        class="mob-nav-item <?php echo $currentPage == 'payment.php' ? 'active' : ''; ?>">
                        <i class="fa-solid fa-receipt"></i>
                        <span>โอนเงิน</span>
                    </a>

                    <a href="order_status.php"
                        class="mob-nav-item <?php echo $currentPage == 'order_status.php' ? 'active' : ''; ?>">
                        <i class="fa-solid fa-truck-fast"></i>
                        <span>สถานะ</span>
                    </a>

                    <a href="javascript:void(0)"
                        onclick="if(window.handleLogout) { window.handleLogout(); } else { window.location.href='index.php'; }"
                        class="mob-nav-item logout-tab">
                        <i class="fa-solid fa-right-from-bracket"></i>
                        <span>ออก</span>
                    </a>

                </div>
            </nav>