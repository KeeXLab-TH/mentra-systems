<?php
// sidebar.php
// Get current page name to highlight the active menu
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<!-- Add FontAwesome and Google Fonts if not already in the head (safe inclusion) -->
<link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

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
        transition: all 0.3sease-in-out;
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

    /* Fix Tooltip for collapsed sidebar */
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

    /* 80px */
</style>

<!-- HIDDEN ELEMENTS TO PREVENT JS ERRORS FROM bom.php PREVIOUS NAV -->
<div style="display:none;">
    <div id="headerProjectName"></div>
    <div id="currentProjectBadge"></div>
    <div id="roleBadge"></div>
    <div id="roleBadgeMobile"></div>
</div>

<!-- ========================================== -->
<!-- DASHBOARD WRAPPER START                    -->
<!-- ========================================== -->
<div class="flex h-screen bg-slate-50 overflow-hidden font-prompt w-full">

    <!-- OVERLAY FOR MOBILE -->
    <div id="mobile-overlay"
        class="fixed inset-0 bg-slate-900/50 z-40 hidden md:hidden backdrop-blur-sm transition-opacity"
        onclick="toggleMobileSidebar()"></div>

    <!-- SIDEBAR -->
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

        <!-- Header Topbar -->
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
                            echo 'Drawings & Documents';
                        elseif ($currentPage == 'calculator.php')
                            echo 'Price Calculator';
                        elseif ($currentPage == 'logs.php')
                            echo 'Action Logs';
                        elseif ($currentPage == 'manage_users.php')
                            echo 'User Management';
                        else
                            echo 'Dashboard';
                        ?>
                    </h2>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <div
                    class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center overflow-hidden border border-indigo-200 shadow-sm cursor-pointer hover-glow">
                    <i class="fa-solid fa-user text-indigo-500 text-sm"></i>
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
                // Sidebar logic
                function toggleMobileSidebar() {
                    const sidebar = document.getElementById('desktop-sidebar');
                    const overlay = document.getElementById('mobile-overlay');

                    if (sidebar.classList.contains('-translate-x-full')) {
                        // Open mobile
                        sidebar.classList.remove('-translate-x-full');
                        overlay.classList.remove('hidden');
                        setTimeout(() => overlay.classList.add('opacity-100'), 10);
                    } else {
                        // Close mobile
                        sidebar.classList.add('-translate-x-full');
                        overlay.classList.remove('opacity-100');
                        setTimeout(() => overlay.classList.add('hidden'), 300);
                    }
                }

                function toggleDesktopSidebar() {
                    const sidebar = document.getElementById('desktop-sidebar');
                    sidebar.classList.toggle('sidebar-collapsed');
                }

                // Sync the hidden span text with the visual UI
                // Observe changes to the hidden #headerProjectName if it exists
                document.addEventListener("DOMContentLoaded", () => {
                    const hiddenProjectNode = document.getElementById('headerProjectName');
                    const visualProjectNode = document.getElementById('visualProjectName');
                    const hiddenRoleBadge = document.getElementById('roleBadge');

                    if (hiddenProjectNode && visualProjectNode) {
                        const observer = new MutationObserver(() => {
                            const text = hiddenProjectNode.innerText || hiddenProjectNode.textContent;
                            visualProjectNode.innerHTML = text ? `<i class="fa-regular fa-folder text-indigo-400 mr-1"></i> ${text}` : '';
                        });
                        observer.observe(hiddenProjectNode, { childList: true, characterData: true, subtree: true });
                    }
                });
            </script>