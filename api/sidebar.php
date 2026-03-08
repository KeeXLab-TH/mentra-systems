<?php
// sidebar.php
// Get current page name to highlight the active menu
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<!-- Note: FontAwesome and Google Fonts are loaded by the parent page's <head> -->

<!-- Global Animation Libraries -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
<script src="https://unpkg.com/split-type"></script>

<!-- Noise Texture Overlay -->
<div class="noise-overlay"></div>



<style>
    /* ============================================================
       MENTRA GLOBAL ANIMATION SYSTEM v4.0
       cubic-bezier [0.22, 1, 0.36, 1] — Premium Easing
    ============================================================ */
    :root {
        --ease-expo: cubic-bezier(0.22, 1, 0.36, 1);
        --ease-quart: cubic-bezier(0.25, 1, 0.5, 1);
        --ease-back: cubic-bezier(0.34, 1.56, 0.64, 1);
        --dur-fast: 0.25s;
        --dur-base: 0.45s;
        --dur-slow: 0.65s;
        --sidebar-w: 16rem;
        --sidebar-w-collapsed: 5rem;
        --header-h: 4rem;
    }

    /* ── Base ─────────────────────────────────────────────── */
    *,
    *::before,
    *::after {
        box-sizing: border-box;
    }

    body {
        font-family: 'Prompt', sans-serif;
        background-color: #f8fafc;
        margin: 0;
        overflow: hidden;
        -webkit-font-smoothing: antialiased;
        letter-spacing: 0.01em;
        line-height: 1.6;
    }

    /* ── Scrollbar ─────────────────────────────────────────── */
    .no-scrollbar::-webkit-scrollbar {
        display: none;
    }

    .no-scrollbar {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }

    /* ── Glass Header ──────────────────────────────────────── */
    .glass-header {
        background: rgba(255, 255, 255, 0.88);
        backdrop-filter: blur(16px) saturate(180%);
        -webkit-backdrop-filter: blur(16px) saturate(180%);
        border-bottom: 1px solid rgba(226, 232, 240, 0.5);
        transition: box-shadow var(--dur-fast) var(--ease-expo);
    }

    .glass-header:hover {
        box-shadow: 0 2px 16px rgba(0, 0, 0, 0.06);
    }

    /* ── Sidebar Wrapper ────────────────────────────────────── */
    .sidebar-transition {
        transition:
            width var(--dur-base) var(--ease-expo),
            transform var(--dur-base) var(--ease-expo),
            opacity var(--dur-fast) var(--ease-expo);
    }

    /* ── Nav Items ─────────────────────────────────────────── */
    .nav-item {
        position: relative;
        overflow: hidden;
        border-radius: 0.75rem;
        transition:
            background var(--dur-fast) var(--ease-expo),
            color var(--dur-fast) var(--ease-expo),
            transform var(--dur-fast) var(--ease-expo);
        will-change: transform;
    }

    /* Left accent bar */
    .nav-item::before {
        content: '';
        position: absolute;
        left: 0;
        top: 50%;
        transform: translateY(-50%) scaleY(0);
        transform-origin: center;
        width: 3px;
        height: 60%;
        background: linear-gradient(180deg, #818cf8, #6366f1);
        border-radius: 0 2px 2px 0;
        transition: transform var(--dur-fast) var(--ease-back);
    }

    .nav-item:hover::before,
    .nav-item.active::before {
        transform: translateY(-50%) scaleY(1);
    }

    /* Ripple bg on hover */
    .nav-item::after {
        content: '';
        position: absolute;
        inset: 0;
        background: rgba(99, 102, 241, 0.0);
        border-radius: inherit;
        transition: background var(--dur-fast) var(--ease-expo);
    }

    .nav-item:hover::after {
        background: rgba(99, 102, 241, 0.06);
    }

    .nav-item.active {
        background-color: #f97316;
        box-shadow: 0 4px 15px rgba(249, 115, 22, 0.3);
    }

    .nav-item.active .menu-text {
        color: white;
    }

    .nav-item.active i {
        color: white;
        filter: drop-shadow(0 0 8px rgba(255, 255, 255, 0.4));
    }

    /* Micro-lift on click */
    .nav-item:active {
        transform: scale(0.97);
    }

    /* ── Stagger Entrance ──────────────────────────────────── */
    @keyframes m-fadeSlideUp {
        from {
            opacity: 0;
            transform: translateY(14px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes m-fadeSlideIn {
        from {
            opacity: 0;
            transform: translateX(-10px);
        }

        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    @keyframes m-scaleIn {
        from {
            opacity: 0;
            transform: scale(0.95);
        }

        to {
            opacity: 1;
            transform: scale(1);
        }
    }

    .m-enter {
        animation: m-fadeSlideUp var(--dur-base) var(--ease-expo) both;
        animation-delay: calc(var(--i, 0) * 55ms);
    }

    .m-enter-x {
        animation: m-fadeSlideIn var(--dur-base) var(--ease-expo) both;
        animation-delay: calc(var(--i, 0) * 50ms);
    }

    .m-enter-scale {
        animation: m-scaleIn var(--dur-base) var(--ease-expo) both;
        animation-delay: calc(var(--i, 0) * 60ms);
    }

    /* Nav items stagger */
    .nav-item {
        animation: m-fadeSlideIn var(--dur-base) var(--ease-expo) both;
    }

    #desktop-sidebar .nav-item:nth-child(1) {
        animation-delay: 80ms;
    }

    #desktop-sidebar .nav-item:nth-child(2) {
        animation-delay: 130ms;
    }

    #desktop-sidebar .nav-item:nth-child(3) {
        animation-delay: 180ms;
    }

    #desktop-sidebar .nav-item:nth-child(4) {
        animation-delay: 230ms;
    }

    #desktop-sidebar .nav-item:nth-child(5) {
        animation-delay: 280ms;
    }

    /* ── Global Button Micro-interactions ──────────────────── */
    button,
    .btn-lift,
    a[class*="rounded"] {
        transition:
            transform var(--dur-fast) var(--ease-expo),
            box-shadow var(--dur-fast) var(--ease-expo),
            background-color var(--dur-fast) var(--ease-expo),
            color var(--dur-fast) var(--ease-expo),
            border-color var(--dur-fast) var(--ease-expo);
        will-change: transform;
    }

    .btn-lift:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
    }

    .btn-lift:active {
        transform: translateY(0) scale(0.97);
    }

    /* ── Glass Panel ───────────────────────────────────────── */
    .glass-panel {
        background: rgba(255, 255, 255, 0.82);
        backdrop-filter: blur(20px) saturate(180%);
        -webkit-backdrop-filter: blur(20px) saturate(180%);
        border: 1px solid rgba(255, 255, 255, 0.4);
        border-radius: 1.5rem;
        box-shadow: 0 4px 24px -4px rgba(0, 0, 0, 0.04), 0 1px 4px rgba(0, 0, 0, 0.02);
        transition:
            box-shadow var(--dur-fast) var(--ease-expo),
            transform var(--dur-fast) var(--ease-expo);
    }

    .glass-panel:hover {
        box-shadow: 0 12px 40px -8px rgba(0, 0, 0, 0.08), 0 2px 8px rgba(0, 0, 0, 0.03);
    }

    /* ── Noise Texture ─────────────────────────────────────── */
    .noise-overlay {
        position: fixed;
        inset: 0;
        z-index: 9999;
        pointer-events: none;
        background-image: url('data:image/svg+xml,%3Csvg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg"%3E%3Cfilter id="noiseFilter"%3E%3CfeTurbulence type="fractalNoise" baseFrequency="0.65" numOctaves="3" stitchTiles="stitch"/%3E%3C/filter%3E%3Crect width="100%25" height="100%25" filter="url(%23noiseFilter)"/%3E%3C/svg%3E');
        opacity: 0.035;
        mix-blend-mode: multiply;
    }

    /* Custom Cursor Removed */

    /* ── Mobile Bottom Tab Bar ─────────────────────────────── */
    .bottom-tab-bar {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        height: 64px;
        background: rgba(15, 23, 42, 0.95);
        backdrop-filter: blur(20px) saturate(180%);
        -webkit-backdrop-filter: blur(20px) saturate(180%);
        border-top: 1px solid rgba(255, 255, 255, 0.05);
        z-index: 50;
        display: none;
        box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.02);
    }

    .tab-item {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: #94a3b8;
        font-size: 10px;
        font-weight: 500;
        transition: color 0.3s var(--ease-expo), transform 0.3s var(--ease-expo);
    }

    .tab-item i {
        font-size: 18px;
        margin-bottom: 4px;
        transition: transform 0.3s var(--ease-expo);
    }

    .tab-item.active {
        color: #f97316;
    }

    .tab-item.active i {
        transform: translateY(-2px);
        filter: drop-shadow(0 0 8px rgba(249, 115, 22, 0.4));
    }

    .tab-item:active {
        transform: scale(0.92);
    }

    @media (max-width: 767px) {
        .bottom-tab-bar {
            display: flex;
        }

        .desktop-only-sidebar {
            display: none !important;
        }

        /* Pad absolute bottom for body to clear tab bar */
        #main-content-wrapper {
            padding-bottom: 64px !important;
        }

        .glass-header {
            background: rgba(15, 23, 42, 0.95) !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05) !important;
        }

        /* Reset Cursor on mobile Removed */
    }

    /* ── Fade-in-up (page sections) ────────────────────────── */
    .fade-in-up {
        animation: m-fadeSlideUp var(--dur-slow) var(--ease-expo) both;
    }

    /* ── Skeleton Shimmer ──────────────────────────────────── */
    @keyframes m-shimmer {
        0% {
            background-position: -200% center;
        }

        100% {
            background-position: 200% center;
        }
    }

    .skeleton {
        background: linear-gradient(90deg, #f1f5f9 25%, #e2e8f0 50%, #f1f5f9 75%);
        background-size: 200% auto;
        animation: m-shimmer 1.5s var(--ease-expo) infinite;
        border-radius: 0.5rem;
    }

    /* ── Sidebar Collapsed State ───────────────────────────── */
    .sidebar-collapsed {
        width: var(--sidebar-w-collapsed) !important;
    }

    .sidebar-collapsed .menu-text,
    .sidebar-collapsed .logo-text {
        opacity: 0;
        width: 0;
        overflow: hidden;
    }

    /* ── Tooltip ───────────────────────────────────────────── */
    .sidebar-tooltip {
        opacity: 0;
        pointer-events: none;
        transition: opacity var(--dur-fast) var(--ease-expo);
        white-space: nowrap;
    }

    .sidebar-collapsed .nav-item:hover .sidebar-tooltip {
        opacity: 1;
    }

    /* ── Hover Glow ────────────────────────────────────────── */
    .hover-glow:hover {
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2), 0 4px 16px rgba(99, 102, 241, 0.15);
    }

    /* ── Input Focus Enhancement ───────────────────────────── */
    input:focus,
    select:focus,
    textarea:focus {
        transition: border-color var(--dur-fast) var(--ease-expo), box-shadow var(--dur-fast) var(--ease-expo);
    }

    /* ── Page transition wrapper ───────────────────────────── */
    #main-content-wrapper {
        animation: m-fadeSlideUp var(--dur-slow) var(--ease-expo) both;
    }

    /* ── Reduced Motion Support ────────────────────────────── */
    @media (prefers-reduced-motion: reduce) {

        *,
        *::before,
        *::after {
            animation-duration: 0.01ms !important;
            transition-duration: 0.01ms !important;
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
<!-- DASHBOARD WRAPPER START                    -->
<!-- ========================================== -->
<div class="flex h-screen bg-slate-50 overflow-hidden font-prompt w-full">

    <!-- OVERLAY FOR MOBILE (Kept for compatibility, no longer used for sidebar) -->
    <div id="mobile-overlay"
        class="fixed inset-0 bg-slate-900/50 z-40 hidden md:hidden backdrop-blur-sm transition-opacity"
        onclick="toggleMobileSidebar()"></div>

    <!-- DESKTOP SIDEBAR -->
    <aside id="desktop-sidebar"
        class="desktop-only-sidebar sidebar-transition relative z-50 h-screen w-64 bg-[#0f172a] text-slate-300 border-r border-[#1e293b] flex flex-col shadow-2xl">

        <!-- Logo Area -->
        <div class="h-16 flex items-center px-5 border-b border-[#1e293b] bg-[#020617]/50 overflow-hidden">
            <div class="flex items-center flex-shrink-0">
                <div class="bg-white p-1.5 rounded-xl shadow-inner border border-white/20">
                    <img src="Mentra_Solution_Tranparency.png"
                        onerror="this.src='https://cdn-icons-png.flaticon.com/512/2881/2881142.png'" alt="Mentra Logo"
                        class="h-6 w-auto object-contain transition-all duration-300 logo-img">
                </div>
                <span
                    class="logo-text ml-3 font-semibold text-white tracking-widest text-sm whitespace-nowrap uppercase">
                    Mentra <span class="text-indigo-400 font-light ml-1">BOM</span>
                </span>
            </div>
        </div>

        <!-- Navigation Links -->
        <div class="flex-1 overflow-y-auto no-scrollbar py-6 px-4 space-y-1.5">
            <p class="logo-text px-3 text-[10px] font-bold text-slate-500 uppercase tracking-[0.2em] mb-3 mt-2">
                Workspace
            </p>

            <a href="bom.php"
                class="magnetic nav-item flex items-center px-4 py-3.5 rounded-xl transition-all hover:bg-slate-800/60 hover:text-white group <?php echo $currentPage == 'bom.php' ? 'active' : ''; ?>">
                <i
                    class="fa-solid fa-cubes text-sm w-6 text-center group-hover:text-orange-400 <?php echo $currentPage == 'bom.php' ? 'text-orange-400' : 'text-slate-400'; ?> transition-colors"></i>
                <span class="menu-text ml-3 font-medium text-sm">BOM Manager</span>
                <div
                    class="sidebar-tooltip absolute left-14 bg-slate-800 text-white text-[10px] px-2 py-1 rounded shadow-lg z-50 ml-2 tracking-wide">
                    BOM Manager</div>
            </a>

            <a href="drawings.php"
                class="magnetic nav-item flex items-center px-4 py-3.5 rounded-xl transition-all hover:bg-slate-800/60 hover:text-white group <?php echo $currentPage == 'drawings.php' ? 'active' : ''; ?>">
                <i
                    class="fa-regular fa-file-pdf text-sm w-6 text-center group-hover:text-orange-400 <?php echo $currentPage == 'drawings.php' ? 'text-orange-400' : 'text-slate-400'; ?> transition-colors"></i>
                <span class="menu-text ml-3 font-medium text-sm">แบบโครงสร้าง</span>
                <div
                    class="sidebar-tooltip absolute left-14 bg-slate-800 text-white text-[10px] px-2 py-1 rounded shadow-lg z-50 ml-2 tracking-wide">
                    Drawings</div>
            </a>

            <a href="calculator.php"
                class="magnetic nav-item flex items-center px-4 py-3.5 rounded-xl transition-all hover:bg-slate-800/60 hover:text-white group <?php echo $currentPage == 'calculator.php' ? 'active' : ''; ?>">
                <i
                    class="fa-solid fa-calculator text-sm w-6 text-center group-hover:text-orange-400 <?php echo $currentPage == 'calculator.php' ? 'text-orange-400' : 'text-slate-400'; ?> transition-colors"></i>
                <span class="menu-text ml-3 font-medium text-sm">คำนวณราคา</span>
                <div
                    class="sidebar-tooltip absolute left-14 bg-slate-800 text-white text-[10px] px-2 py-1 rounded shadow-lg z-50 ml-2 tracking-wide">
                    Calculator</div>
            </a>

            <p class="logo-text px-3 text-[10px] font-bold text-slate-500 uppercase tracking-[0.2em] mb-3 mt-8">System
            </p>

            <a href="logs.php"
                class="magnetic nav-item flex items-center px-4 py-3.5 rounded-xl transition-all hover:bg-slate-800/60 hover:text-white group <?php echo $currentPage == 'logs.php' ? 'active' : ''; ?>">
                <i
                    class="fa-solid fa-clock-rotate-left text-sm w-6 text-center group-hover:text-orange-400 <?php echo $currentPage == 'logs.php' ? 'text-orange-400' : 'text-slate-400'; ?> transition-colors"></i>
                <span class="menu-text ml-3 font-medium text-sm">Action Logs</span>
                <div
                    class="sidebar-tooltip absolute left-14 bg-slate-800 text-white text-[10px] px-2 py-1 rounded shadow-lg z-50 ml-2 tracking-wide">
                    Logs</div>
            </a>

        </div>

        <!-- Footer / User / Logout -->
        <div class="p-4 border-t border-[#1e293b] bg-[#020617]/50">
            <button
                onclick="if(window.handleLogout) { window.handleLogout(); } else { window.location.href='index.php'; }"
                class="magnetic w-full flex items-center justify-center gap-2 bg-slate-800/50 border border-slate-700 hover:bg-red-500 hover:border-red-500 text-slate-300 hover:text-white py-3 rounded-xl transition-all duration-300 font-medium text-xs tracking-wide group">
                <i class="fa-solid fa-power-off group-hover:rotate-90 transition-transform duration-300"></i>
                <span class="menu-text uppercase tracking-wider">Log Out</span>
            </button>
            <div class="logo-text text-center mt-4 text-[9px] text-slate-500 uppercase tracking-widest leading-relaxed">
                Mentra Solution Co., Ltd.<br>
                <span class="text-slate-600">v3.0 Premium Dashboard</span>
            </div>
        </div>
    </aside>

    <!-- MOBILE BOTTOM TAB BAR -->
    <nav class="bottom-tab-bar">
        <a href="bom.php" class="tab-item <?php echo $currentPage == 'bom.php' ? 'active' : ''; ?>">
            <i class="fa-solid fa-cubes"></i>
            <span>BOM</span>
        </a>
        <a href="drawings.php" class="tab-item <?php echo $currentPage == 'drawings.php' ? 'active' : ''; ?>">
            <i class="fa-regular fa-file-pdf"></i>
            <span>Drawings</span>
        </a>
        <a href="calculator.php" class="tab-item <?php echo $currentPage == 'calculator.php' ? 'active' : ''; ?>">
            <i class="fa-solid fa-calculator"></i>
            <span>Calc</span>
        </a>
        <a href="logs.php" class="tab-item <?php echo $currentPage == 'logs.php' ? 'active' : ''; ?>">
            <i class="fa-solid fa-clock-rotate-left"></i>
            <span>Logs</span>
        </a>
        <a href="#"
            onclick="if(window.handleLogout) { window.handleLogout(); } else { window.location.href='index.php'; }"
            class="tab-item text-red-400">
            <i class="fa-solid fa-power-off"></i>
            <span>Log Out</span>
        </a>
    </nav>

    <!-- RIGHT CONTENT AREA -->
    <div class="flex-1 flex flex-col h-screen overflow-hidden relative transition-all duration-300 w-full"
        id="main-content-wrapper">

        <!-- Header Topbar -->
        <header class="glass-header sticky top-0 z-40 px-4 md:px-6 py-4 flex justify-between items-center h-16 w-full">
            <div class="flex items-center gap-4">

                <!-- Desktop Collapse Button -->
                <button
                    class="hidden md:flex w-8 h-8 items-center justify-center rounded-lg text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 transition-colors"
                    onclick="toggleDesktopSidebar()">
                    <i class="fa-solid fa-bars-staggered" id="collapseIcon"></i>
                </button>

                <!-- Mobile Header Logo & Text -->
                <div class="flex md:hidden items-center gap-2">
                    <div class="bg-white p-1 rounded-md shadow-sm border border-white/20">
                        <img src="Mentra_Solution_Tranparency.png"
                            onerror="this.src='https://cdn-icons-png.flaticon.com/512/2881/2881142.png'" alt="Logo"
                            class="h-5 w-auto object-contain">
                    </div>
                    <span class="font-bold text-white tracking-widest text-xs uppercase whitespace-nowrap">
                        Mentra <span class="text-indigo-400 font-light">BOM</span>
                    </span>
                </div>

                <!-- Dynamic Page Title based on the page (Hidden on Mobile) -->
                <div class="hidden md:flex flex-col">
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
                <!-- User Status Info -->
                <div class="flex flex-col items-end text-right">
                    <span id="headerUserName"
                        class="text-[11px] md:text-xs font-bold text-white md:text-slate-800 tracking-wide capitalize truncate max-w-[150px] md:max-w-[200px]">
                        กำลังโหลด...
                    </span>
                    <div class="flex items-center gap-1.5 mt-0.5">
                        <div class="relative flex h-1.5 w-1.5">
                            <span
                                class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-emerald-500"></span>
                        </div>
                        <span
                            class="text-[9px] text-emerald-400 md:text-emerald-500 font-bold uppercase tracking-widest leading-none mt-0.5">Online</span>
                    </div>
                </div>

                <!-- User Avatar Premium -->
                <div class="relative group cursor-pointer">
                    <div
                        class="absolute -top-1.5 -right-1.5 z-10 text-yellow-400 drop-shadow-md transform -rotate-12 group-hover:rotate-0 transition-transform">
                        <i class="fa-solid fa-crown text-[10px] md:text-xs"></i>
                    </div>
                    <div
                        class="h-8 w-8 md:h-10 md:w-10 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center overflow-hidden border-2 border-white md:border-indigo-100 shadow-md hover-glow transition-all">
                        <span id="headerUserInitial"
                            class="font-bold text-white text-[12px] md:text-sm uppercase tracking-wider">U</span>
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
                // Disable normal Mobile Toggle
                function toggleMobileSidebar() { /* Now using Tab Bar instead */ }

                function toggleDesktopSidebar() {
                    const sidebar = document.getElementById('desktop-sidebar');
                    sidebar.classList.toggle('sidebar-collapsed');
                }

                // Sync the hidden span text with the visual UI
                document.addEventListener("DOMContentLoaded", () => {
                    const hiddenProjectNode = document.getElementById('headerProjectName');
                    const visualProjectNode = document.getElementById('visualProjectName');

                    if (hiddenProjectNode && visualProjectNode) {
                        const observer = new MutationObserver(() => {
                            const text = hiddenProjectNode.innerText || hiddenProjectNode.textContent;
                            visualProjectNode.innerHTML = text ? `<i class="fa-regular fa-folder text-indigo-400 mr-1"></i> ${text}` : '';
                        });
                        observer.observe(hiddenProjectNode, { childList: true, characterData: true, subtree: true });
                    }

                    // Dynamically set user name from LocalStorage
                    const userStr = localStorage.getItem('mentra_user');
                    if (userStr) {
                        try {
                            const userObj = JSON.parse(userStr);
                            const nameEl = document.getElementById('headerUserName');
                            const initialEl = document.getElementById('headerUserInitial');
                            if (nameEl && userObj.name) {
                                nameEl.innerText = userObj.name;
                                if (initialEl) {
                                    initialEl.innerText = userObj.name.charAt(0);
                                }
                            }
                        } catch (e) { }
                    }


                });
            </script>