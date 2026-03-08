<!DOCTYPE html>
<html lang="th">

<head>
    <script>
        (function () {
            var allowedRoles = ['admin', 'material', 'purchasing', 'viewer'];
            var role = localStorage.getItem('mentra_role');
            if (!role || allowedRoles.indexOf(role) === -1) {
                window.location.href = 'index.php';
            }
        })();
    </script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ไฟล์เขียนแบบ (Drawings) — Mentra BOM</title>
    <!-- Resource Hints: ให้ browser เตรียม connection ล่วงหน้า -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://www.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdn.tailwindcss.com">
    <link rel="dns-prefetch" href="https://cdnjs.cloudflare.com">
    <link rel="dns-prefetch" href="https://cdn.jsdelivr.net">
    <link rel="dns-prefetch" href="https://firestore.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" defer></script>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Prompt', sans-serif;
            background: linear-gradient(135deg, #f0f4ff 0%, #faf5ff 50%, #fff7ed 100%);
            min-height: 100vh;
        }

        .glass-panel {
            background: rgba(255, 255, 255, 0.88);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border-radius: 1.25rem;
            box-shadow: 0 4px 24px -4px rgba(0, 0, 0, 0.06), 0 1px 4px rgba(0, 0, 0, 0.04);
            transition: box-shadow 0.3s ease;
        }

        .glass-panel:hover {
            box-shadow: 0 8px 32px -4px rgba(0, 0, 0, 0.1);
        }

        #mainLoading {
            position: fixed;
            inset: 0;
            background: linear-gradient(135deg, #f0f4ff, #fff7ed);
            z-index: 9999;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            transition: opacity 0.5s ease;
        }

        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        /* PDF Modal Styles */
        #pdfModal {
            transition: opacity 0.3s ease;
        }

        #pdfModal iframe {
            width: 100%;
            height: calc(100vh - 120px);
            border: none;
            border-radius: 0.75rem;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 6px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        .btn-lift {
            transition: all 0.2s ease;
        }

        .btn-lift:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .btn-lift:active {
            transform: translateY(0);
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(12px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-in-up {
            animation: fadeInUp 0.4s ease-out;
        }

        @media (min-width: 1280px) {
            .sidebar-sticky {
                position: sticky;
                top: 80px;
                max-height: calc(100vh - 96px);
                overflow-y: auto;
            }

            .sidebar-sticky::-webkit-scrollbar {
                width: 0;
            }
        }

        .nav-mobile-menu {
            display: none;
        }

        @media (max-width: 767px) {
            .nav-desktop-right {
                display: none !important;
            }

            .nav-mobile-toggle {
                display: flex !important;
            }

            .nav-mobile-menu.open {
                display: flex;
            }
        }

        @media (min-width: 768px) {
            .nav-mobile-toggle {
                display: none !important;
            }
        }
    </style>
</head>

<body class="text-slate-700 bg-slate-50">

    <!-- Loading Screen -->
    <div id="mainLoading">
        <div class="relative">
            <div class="w-14 h-14 border-4 border-indigo-200/50 border-dashed rounded-full animate-spin"></div>
            <div
                class="absolute top-0 left-0 w-14 h-14 border-4 border-indigo-500 border-t-transparent rounded-full animate-spin">
            </div>
        </div>
        <p class="mt-4 text-slate-600 font-medium text-sm animate-pulse">กำลังตรวจสอบสิทธิ์...</p>
    </div>

    <!-- Sidebar and Header -->
    <?php include 'sidebar.php'; ?>

    <div
        class="bento-grid max-w-[95rem] mx-auto px-4 md:px-6 py-6 pb-24 grid grid-cols-1 xl:grid-cols-12 gap-6 relative z-10 w-full">

        <!-- Sidebar -->
        <div class="xl:col-span-3 space-y-6 sidebar-sticky">
            <!-- 1. ส่วนเลือกโครงการ -->
            <div class="glass-panel p-4 md:p-5 border-l-4 border-indigo-500 relative overflow-hidden transition-all duration-500 fade-in-up"
                id="projectPanel">

                <div
                    class="absolute top-0 right-0 -mt-2 -mr-2 w-16 h-16 bg-blue-500 opacity-10 rounded-full pointer-events-none">
                </div>

                <label class="block text-xs font-bold text-slate-500 uppercase mb-2 tracking-wider">
                    <i class="fa-solid fa-folder-tree mr-1 text-indigo-600"></i> เลือกโครงการ
                </label>

                <div class="flex gap-2 mb-3">
                    <div class="relative w-full">
                        <select id="projectSelect" onchange="window.changeProject(this.value)"
                            class="w-full appearance-none bg-slate-50 border border-slate-300 text-slate-800 text-sm rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 block p-2.5 pr-8 cursor-pointer font-medium transition-all hover:bg-white shadow-sm">
                            <option value="" disabled selected>-- โหลดโครงการ... --</option>
                        </select>
                        <div
                            class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-500">
                            <i class="fa-solid fa-chevron-down text-xs"></i>
                        </div>
                    </div>
                </div>

                <div id="noProjectAlert"
                    class="mt-3 text-xs text-indigo-700 bg-indigo-50 p-3 rounded-lg border border-indigo-200 flex items-start gap-2 animate-pulse">
                    <i class="fa-solid fa-circle-exclamation mt-0.5 text-indigo-500"></i>
                    <span>กรุณาเลือกโครงการ<br>เพื่อจัดการไฟล์เขียนแบบ</span>
                </div>
            </div>

            <!-- 2. ส่วนฟอร์มอัปโหลด -->
            <div id="formPanel"
                class="glass-panel p-4 md:p-6 border border-gray-100/50 opacity-50 pointer-events-none transition-all duration-300 relative fade-in-up">

                <div id="guestOverlay"
                    class="hidden absolute inset-0 bg-slate-50/80 backdrop-blur-[2px] z-20 flex flex-col items-center justify-center rounded-xl border border-slate-200 text-center p-4">
                    <div
                        class="w-12 h-12 bg-white rounded-full flex items-center justify-center shadow-sm mb-3 text-slate-400 text-xl border border-slate-100">
                        <i class="fa-solid fa-lock"></i>
                    </div>
                    <h4 class="text-sm font-bold text-slate-700">Locked</h4>
                    <p class="text-xs text-slate-400 mt-1">สิทธิ์ของคุณไม่สามารถอัปโหลดไฟล์ได้</p>
                </div>

                <div class="absolute top-4 right-4 text-indigo-100 text-6xl opacity-20 -z-10 transform rotate-12">
                    <i class="fa-solid fa-upload"></i>
                </div>

                <div class="flex items-center justify-between mb-5 border-b pb-2 border-slate-100">
                    <h2 class="text-lg font-bold flex items-center gap-2 text-slate-800">
                        <i class="fa-brands fa-google-drive text-indigo-600"></i> เพิ่มไฟล์เขียนแบบ
                    </h2>
                </div>

                <form id="uploadForm" class="space-y-4" onsubmit="window.uploadFile(event)">
                    <div>
                        <label class="text-xs font-bold text-slate-500 uppercase mb-1 block">ชื่อไฟล์/รายละเอียด</label>
                        <input type="text" id="drawingName" required
                            class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-200 focus:border-indigo-500 outline-none transition-all placeholder-slate-400"
                            placeholder="เช่น แบบแปลนชั้น 1, โครงสร้างหลังคา...">
                    </div>

                    <div>
                        <label class="text-xs font-bold text-slate-500 uppercase mb-1 block">ลิงก์ Google Drive</label>
                        <div class="relative">
                            <span class="absolute left-3 top-3 text-slate-400"><i
                                    class="fa-brands fa-google-drive text-sm"></i></span>
                            <input type="url" id="drawingLink" required
                                class="w-full pl-9 pr-4 py-2.5 bg-slate-50 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-200 focus:border-indigo-500 outline-none transition-all placeholder-slate-400"
                                placeholder="https://drive.google.com/file/d/...">
                        </div>
                        <p class="text-[10px] text-slate-400 mt-1.5 flex items-start gap-1">
                            <i class="fa-solid fa-circle-info mt-0.5 text-indigo-400"></i>
                            เปิดไฟล์ใน Google Drive → กด Share → Copy Link → วางที่นี่
                        </p>
                    </div>

                    <button type="submit" id="submitBtn"
                        class="w-full bg-gradient-to-r from-indigo-600 to-blue-500 hover:from-indigo-700 hover:to-blue-600 text-white font-bold py-3 rounded-xl shadow-lg hover:shadow-xl transition-all flex justify-center items-center gap-2 mt-4 btn-lift">
                        <i class="fa-solid fa-link"></i> <span>บันทึกลิงก์</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- Right: Data Table -->
        <div class="xl:col-span-9">
            <div
                class="glass-panel p-3 md:p-6 min-h-[400px] md:min-h-[600px] relative border border-gray-100/50 flex flex-col h-full fade-in-up">

                <!-- Table Header -->
                <div
                    class="flex flex-col sm:flex-row justify-between items-start sm:items-end border-b border-slate-100 pb-4 mb-4 gap-3">
                    <div>
                        <h2 class="text-lg md:text-xl font-bold text-slate-800 flex items-center gap-2" id="tableTitle">
                            <i class="fa-regular fa-folder-open text-indigo-500"></i> ไฟล์เขียนแบบ
                        </h2>
                        <p class="text-xs md:text-sm text-slate-400 mt-1 pl-7" id="fileCount">รอเลือกโครงการ...</p>
                    </div>

                    <div class="flex items-center gap-2 md:gap-3 w-full sm:w-auto justify-end flex-wrap"
                        id="projectActionMenu" style="display: none;">
                        <a href="calculator.php" id="btnLinkCalculator"
                            class="magnetic bg-violet-600 hover:bg-violet-700 text-white px-3 md:px-4 py-2 rounded-xl shadow-sm text-xs md:text-sm font-medium transition-all flex items-center gap-1.5 btn-lift">
                            <i class="fa-solid fa-calculator text-base"></i>
                            <span class="hidden md:inline">คำนวณราคา</span>
                        </a>
                        <a href="bom.php" id="btnLinkBom"
                            class="magnetic bg-slate-700 hover:bg-slate-800 text-white px-3 md:px-4 py-2 rounded-xl shadow-sm text-xs md:text-sm font-medium transition-all flex items-center gap-1.5 btn-lift">
                            <i class="fa-solid fa-list-check text-base"></i>
                            <span class="hidden md:inline">รายการ BOM</span>
                        </a>
                    </div>
                </div>

                <!-- Table Content -->
                <div
                    class="flex-1 overflow-x-auto rounded-xl border border-slate-200/80 bg-white/50 shadow-sm relative table-container">
                    <table class="w-full text-left border-collapse min-w-[600px]">
                        <thead>
                            <tr
                                class="bg-gradient-to-r from-slate-50 to-slate-100/50 text-xs text-slate-500 uppercase tracking-wider sticky top-0 font-semibold border-b border-slate-200 shadow-sm">
                                <th class="p-4 text-center w-16 whitespace-nowrap">#</th>
                                <th class="p-4 min-w-[200px]">ชื่อไฟล์ / รายละเอียด</th>
                                <th class="p-4 whitespace-nowrap text-center">ขนาดไฟล์</th>
                                <th class="p-4 whitespace-nowrap text-center">อัพโหลดโดย</th>
                                <th class="p-4 whitespace-nowrap text-center">วันที่อัพโหลด</th>
                                <th class="p-4 text-center whitespace-nowrap min-w-[120px]">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody id="drawingsList" class="text-sm divide-y divide-slate-100">
                            <tr>
                                <td colspan="6" class="text-center py-20 text-slate-400">กรุณาเลือกโครงการด้านซ้าย</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>

    </div>

    <!-- PDF Viewer Modal -->
    <div id="pdfModal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4 sm:p-6 opacity-0">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"
            onclick="window.closePdfModal()"></div>
        <div class="relative w-full max-w-6xl bg-white rounded-2xl shadow-2xl overflow-hidden flex flex-col transform scale-95 transition-transform duration-300"
            id="pdfModalContent">

            <div class="flex items-center justify-between px-4 py-3 border-b border-slate-100 bg-slate-50">
                <h3 class="font-bold text-slate-700 flex items-center gap-2">
                    <i class="fa-regular fa-file-pdf text-red-500"></i> <span id="pdfModalTitle">PDF Viewer</span>
                </h3>
                <div class="flex items-center gap-2">
                    <a id="pdfExternalLink" href="#" target="_blank"
                        class="text-slate-500 hover:text-indigo-600 p-2 rounded-lg hover:bg-indigo-50 transition-colors"
                        title="เปิดในหน้าต่างใหม่">
                        <i class="fa-solid fa-arrow-up-right-from-square"></i>
                    </a>
                    <button onclick="window.closePdfModal()"
                        class="text-slate-400 hover:text-red-500 p-2 rounded-lg hover:bg-red-50 transition-colors">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>
            </div>

            <div class="p-2 bg-slate-100/50">
                <iframe id="pdfIframe" src="" loading="lazy"></iframe>
            </div>

        </div>
    </div>

    <!-- Firebase Storage Scripts -->
    <script type="module">
        import { initializeApp } from "https://www.gstatic.com/firebasejs/11.6.1/firebase-app.js";
        import { getFirestore, collection, addDoc, query, where, getDocs, onSnapshot, deleteDoc, doc, serverTimestamp } from "https://www.gstatic.com/firebasejs/11.6.1/firebase-firestore.js";
        import { getAuth, signInAnonymously, signInWithCustomToken, onAuthStateChanged, signOut } from "https://www.gstatic.com/firebasejs/11.6.1/firebase-auth.js";
        import { getStorage, ref, uploadBytesResumable, getDownloadURL, deleteObject } from "https://www.gstatic.com/firebasejs/11.6.1/firebase-storage.js";

        let firebaseConfig;
        let isCanvasEnv = false;
        try { if (typeof __firebase_config !== 'undefined') { firebaseConfig = JSON.parse(__firebase_config); isCanvasEnv = true; } } catch (e) { }

        const appId = typeof __app_id !== 'undefined' ? __app_id : 'default-bom-app';

        if (!firebaseConfig) {
            firebaseConfig = {
                apiKey: "AIzaSyBj8bKeS9Whnh8uOXbAxY_znNgIyzcE-Sg",
                authDomain: "bom-mentra.firebaseapp.com",
                projectId: "bom-mentra",
                storageBucket: "bom-mentra.firebasestorage.app",
                messagingSenderId: "916019460525",
                appId: "1:916019460525:web:11328f705e57d00d53c924",
                measurementId: "G-S7RC954PEK"
            };
        }

        const app = initializeApp(firebaseConfig);
        const db = getFirestore(app);
        const auth = getAuth(app);
        const storage = getStorage(app);

        // References
        const getProjectsRef = () => collection(db, isCanvasEnv ? `artifacts/${appId}/public/data/bom_projects` : 'bom_projects');
        const getDrawingsRef = () => collection(db, isCanvasEnv ? `artifacts/${appId}/public/data/bom_drawings` : 'bom_drawings');

        // Global State
        let currentProjectId = null;
        let currentProjectName = "ยังไม่ได้เลือกโครงการ";
        let allProjectsData = {};
        let unsubscribeDrawings = null;
        let currentUser = null;
        let role = localStorage.getItem('mentra_role') || 'viewer';
        let isAdmin = role === 'admin';

        const escapeHtml = (str) => {
            if (str == null) return '';
            return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#39;');
        };

        const formatBytes = (bytes, decimals = 2) => {
            if (!+bytes) return '0 Bytes';
            const k = 1024;
            const dm = decimals < 0 ? 0 : decimals;
            const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return `${parseFloat((bytes / Math.pow(k, i)).toFixed(dm))} ${sizes[i]}`;
        };

        const checkPermission = () => {
            document.body.className = `role-${role}`;

            const badge = document.getElementById('roleBadge');
            const badgeMobile = document.getElementById('roleBadgeMobile');
            if (badge) {
                badge.innerText = role.toUpperCase();
                badge.className = `text-[10px] uppercase font-bold px-2.5 py-1 rounded-full border ${isAdmin ? 'bg-indigo-600 text-white border-indigo-500' : 'bg-white/10 text-slate-300 border-white/10'}`;
            }
            if (badgeMobile) {
                badgeMobile.innerText = role.toUpperCase();
                badgeMobile.className = badge?.className || '';
            }

            // Material & Admin: เห็นฟอร์มและใช้งานได้
            if (role === 'admin' || role === 'material') {
                document.getElementById('formPanel').classList.remove('pointer-events-none', 'opacity-50');
                const overlay = document.getElementById('guestOverlay');
                if (overlay) overlay.classList.add('hidden');
            } else {
                document.getElementById('formPanel').classList.add('pointer-events-none', 'opacity-50');
                const overlay = document.getElementById('guestOverlay');
                if (overlay) overlay.classList.remove('hidden');
            }

            if (isAdmin) document.body.classList.add('is-admin');
        };
        checkPermission();

        // Init Auth
        const initAuth = async () => {
            try {
                if (typeof __initial_auth_token !== 'undefined' && __initial_auth_token) await signInWithCustomToken(auth, __initial_auth_token);
                else await signInAnonymously(auth);
            } catch (error) { console.error("Auth Failed:", error); loadProjects(); }
        };

        onAuthStateChanged(auth, (user) => {
            if (user) {
                currentUser = user;
                const loader = document.getElementById('mainLoading');
                if (loader) {
                    loader.style.opacity = '0';
                    setTimeout(() => loader.style.display = 'none', 400);
                }
                loadProjects();
            } else initAuth();
        });

        // ----- localStorage cache helpers -----
        const PROJ_CACHE_KEY = 'mentra_projects_cache';
        const PROJ_CACHE_TTL = 5 * 60 * 1000; // 5 นาที
        const saveProjectsCache = (projects) => {
            try { localStorage.setItem(PROJ_CACHE_KEY, JSON.stringify({ ts: Date.now(), data: projects })); } catch (e) { }
        };
        const getProjectsCache = () => {
            try {
                const raw = localStorage.getItem(PROJ_CACHE_KEY);
                if (!raw) return null;
                const { ts, data } = JSON.parse(raw);
                return (Date.now() - ts < PROJ_CACHE_TTL) ? data : null;
            } catch (e) { return null; }
        };

        const renderProjectDropdown = (projects) => {
            const select = document.getElementById('projectSelect');
            const urlParams = new URLSearchParams(window.location.search);
            const urlProject = urlParams.get('project');
            if (urlProject && !currentProjectId && projects.some(p => p.id === urlProject)) {
                currentProjectId = urlProject;
            }
            let html = '<option value="" disabled' + (currentProjectId ? '' : ' selected') + '>-- เลือกโครงการ --</option>';
            projects.forEach(p => {
                allProjectsData[p.id] = p;
                html += `<option value="${escapeHtml(p.id)}" ${p.id === currentProjectId ? 'selected' : ''}>📂 ${escapeHtml(p.name)}</option>`;
            });
            select.innerHTML = html;
            if (currentProjectId && allProjectsData[currentProjectId]) {
                updateProjectHeaderUI(allProjectsData[currentProjectId]);
                loadDrawings(currentProjectId);
            } else if (currentProjectId && !allProjectsData[currentProjectId]) {
                window.changeProject("");
            }
        };

        const loadProjects = () => {
            // แสดง cache ทันทีถ้ามี
            const cached = getProjectsCache();
            if (cached && cached.length > 0) {
                allProjectsData = {};
                renderProjectDropdown(cached);
            }
            const q = query(getProjectsRef());
            onSnapshot(q, (snapshot) => {
                let projects = [];
                snapshot.forEach(doc => projects.push({ id: doc.id, ...doc.data() }));
                projects.sort((a, b) => (b.createdAt?.seconds || 0) - (a.createdAt?.seconds || 0));
                saveProjectsCache(projects);
                allProjectsData = {};
                renderProjectDropdown(projects);
            }, (error) => console.error("Error loading projects:", error));
        }

        const updateProjectHeaderUI = (project) => {
            currentProjectName = project.name;
            document.getElementById('headerProjectName').innerText = project.name;
            document.getElementById('tableTitle').innerHTML = `<span class="text-indigo-600">${escapeHtml(project.name)}</span>`;
            document.getElementById('noProjectAlert').style.display = 'none';

            const badge = document.getElementById('currentProjectBadge');
            if (badge) badge.classList.remove('hidden');

            const formPanel = document.getElementById('formPanel');
            if (role === 'admin' || role === 'material') {
                formPanel.classList.remove('opacity-50', 'pointer-events-none');
            } else {
                formPanel.classList.add('opacity-50', 'pointer-events-none');
            }
        }

        window.changeProject = (projectId) => {
            if (!projectId) {
                currentProjectId = null;
                document.getElementById('formPanel').classList.add('opacity-50', 'pointer-events-none');
                document.getElementById('noProjectAlert').style.display = 'flex';
                document.getElementById('drawingsList').innerHTML = '<tr><td colspan="6" class="text-center py-20 text-slate-300">กรุณาเลือกโครงการด้านซ้าย</td></tr>';
                document.getElementById('headerProjectName').innerText = "ยังไม่ได้เลือกโครงการ";
                document.getElementById('currentProjectBadge').classList.add('hidden');
                if (document.getElementById('projectActionMenu')) document.getElementById('projectActionMenu').style.display = 'none';
                updateNavLinks(null);
                return;
            }
            currentProjectId = projectId;
            if (document.getElementById('projectActionMenu')) document.getElementById('projectActionMenu').style.display = 'flex';

            // Update URL parameters without reloading
            const url = new URL(window.location);
            url.searchParams.set('project', projectId);
            window.history.replaceState({}, '', url);
            updateNavLinks(projectId);

            if (allProjectsData[projectId]) updateProjectHeaderUI(allProjectsData[projectId]);
            loadDrawings(projectId);
        }

        const updateNavLinks = (projectId) => {
            const suffix = projectId ? `?project=${projectId}` : '';
            const links = document.querySelectorAll('a[href^="bom.php"], a[href^="logs.php"], a[href^="drawings.php"], a[href^="calculator.php"]');
            links.forEach(link => {
                const baseHref = link.getAttribute('href').split('?')[0];
                link.setAttribute('href', baseHref + suffix);
            });
        };

        const loadDrawings = (projectId) => {
            if (unsubscribeDrawings) unsubscribeDrawings();
            document.getElementById('drawingsList').innerHTML = '<tr><td colspan="6" class="text-center py-20"><i class="fa-solid fa-spinner fa-spin text-2xl text-indigo-500"></i></td></tr>';

            const q = query(getDrawingsRef(), where("projectId", "==", projectId));
            unsubscribeDrawings = onSnapshot(q, snap => {
                let list = [];
                snap.forEach(d => { const i = { id: d.id, ...d.data() }; list.push(i); });
                list.sort((a, b) => (b.createdAt?.seconds || 0) - (a.createdAt?.seconds || 0));

                document.getElementById('fileCount').innerText = `${list.length} ไฟล์`;
                renderDrawings(list);
            });
        }

        const renderDrawings = (list) => {
            let h = '';
            list.forEach((i, index) => {
                const safeId = escapeHtml(i.id);
                const safeUrl = escapeHtml(i.downloadUrl);
                const fileDate = i.createdAt ? new Date(i.createdAt.toDate()).toLocaleDateString('th-TH') : '-';
                const fileSize = i.sizeBytes ? formatBytes(i.sizeBytes) : '-';
                const uploadedBy = escapeHtml(i.uploaderName || 'Unknown');

                let manageBtn = `
                    <div class="flex justify-center gap-1.5 flex-wrap">
                        <button onclick="window.openPdfModal('${safeUrl}', '${escapeHtml(i.name)}')" class="text-indigo-600 bg-indigo-50 hover:bg-indigo-100 px-3 py-1.5 rounded-lg text-xs font-bold shadow-sm transition-all flex items-center gap-1" title="ดูไฟล์">
                            <i class="fa-solid fa-eye"></i> View
                        </button>
                `;

                if (isAdmin || role === 'material') {
                    manageBtn += `
                        <button onclick="window.deleteDrawing('${safeId}', '${escapeHtml(i.storagePath)}')" class="text-red-500 bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-lg text-xs font-bold shadow-sm transition-all flex items-center gap-1" title="ลบไฟล์">
                            <i class="fa-solid fa-trash-can"></i>
                        </button>
                    `;
                }

                manageBtn += `</div>`;

                h += `<tr class="item-row-enter hover:bg-indigo-50/30 transition-colors group border-b border-slate-50 last:border-0 opacity-0" style="transform: translateY(15px);">
                    <td class="p-4 text-center align-middle font-medium text-slate-500">${index + 1}</td>
                    <td class="p-4 align-middle font-bold text-sm text-slate-700">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-file-pdf text-red-500 text-2xl"></i>
                            <div>
                                <span class="block">${escapeHtml(i.name)}</span>
                            </div>
                        </div>
                    </td>
                    <td class="p-4 align-middle text-center text-xs text-slate-500">${fileSize}</td>
                    <td class="p-4 align-middle text-center text-xs text-slate-500">${uploadedBy}</td>
                    <td class="p-4 align-middle text-center text-xs text-slate-500">${fileDate}</td>
                    <td class="p-4 text-center align-middle">${manageBtn}</td>
                </tr>`;
            });
            document.getElementById('drawingsList').innerHTML = h || `<tr><td colspan="6" class="text-center py-16 text-slate-400">ยังไม่มีไฟล์เขียนแบบในโครงการนี้</td></tr>`;

            // GSAP Enter Animation
            if (typeof gsap !== 'undefined' && h !== '') {
                gsap.to('.item-row-enter', {
                    opacity: 1,
                    y: 0,
                    duration: 0.5,
                    stagger: 0.04,
                    ease: "cubic-bezier(0.22, 1, 0.36, 1)",
                    onComplete: function () {
                        document.querySelectorAll('.item-row-enter').forEach(el => {
                            el.style.transform = '';
                            el.style.opacity = '';
                            el.classList.remove('item-row-enter', 'opacity-0');
                        });
                    }
                });
            }
        };

        // Modal Functions
        window.openPdfModal = (url, name) => {
            const modal = document.getElementById('pdfModal');
            const iframe = document.getElementById('pdfIframe');
            const externalLink = document.getElementById('pdfExternalLink');
            const modalContent = document.getElementById('pdfModalContent');
            const modalTitle = document.getElementById('pdfModalTitle');

            if (modalTitle) {
                modalTitle.innerText = name || 'PDF Viewer';
            }

            // ใช้ลิงก์ preview ของ Google Drive (ต่อยอดจาก /viewที่บันทึกไว้ และเปลี่ยนให้เป็น /preview เพื่อแสดงผลได้ดีขึ้นใน iframe)
            let embedUrl = url;
            if (url.includes('drive.google.com') && url.includes('/view')) {
                embedUrl = url.replace('/view', '/preview');
            }

            iframe.src = embedUrl;
            externalLink.href = url;

            modal.classList.remove('hidden');
            modal.classList.add('flex');

            // Trigger animation
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                modalContent.classList.remove('scale-95');
                modalContent.classList.add('scale-100');
            }, 10);
        };

        window.closePdfModal = () => {
            const modal = document.getElementById('pdfModal');
            const iframe = document.getElementById('pdfIframe');
            const modalContent = document.getElementById('pdfModalContent');

            modal.classList.add('opacity-0');
            modalContent.classList.remove('scale-100');
            modalContent.classList.add('scale-95');

            setTimeout(() => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                iframe.src = ''; // Clear iframe to stop loading/playing
            }, 300);
        };

        window.uploadFile = async (e) => {
            e.preventDefault();
            if (role !== 'admin' && role !== 'material') return;

            if (!currentProjectId) { Swal.fire('เตือน', 'เลือกโครงการก่อน', 'warning'); return; }

            const name = document.getElementById('drawingName').value.trim();
            const rawLink = document.getElementById('drawingLink').value.trim();

            if (!rawLink) { Swal.fire('เตือน', 'กรุณาใส่ลิงก์ Google Drive', 'warning'); return; }

            // แปลง Google Drive Share Link เป็น Direct View Link
            let downloadUrl = rawLink;
            const driveMatch = rawLink.match(/\/file\/d\/([^/]+)/);
            if (driveMatch) {
                // ผลลัพธ์เป็น embed link ปง pdf viewer ใน Google Drive
                downloadUrl = `https://drive.google.com/file/d/${driveMatch[1]}/view`;
            }

            const btn = document.getElementById('submitBtn');
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> กำลังบันทึก...';

            try {
                const mentraUserStr = localStorage.getItem('mentra_user');
                const uploaderName = mentraUserStr ? JSON.parse(mentraUserStr).name : 'Unknown';

                const data = {
                    projectId: currentProjectId,
                    name,
                    downloadUrl,
                    storagePath: '', // ไม่มี storage path เพราะใช้ Google Drive
                    sizeBytes: 0,
                    createdAt: serverTimestamp(),
                    uploaderName
                };

                await addDoc(getDrawingsRef(), data);

                try {
                    const mentraRole = localStorage.getItem('mentra_role') || 'Unknown';
                    const logsRef = collection(db, isCanvasEnv ? `artifacts/${appId}/public/data/bom_logs` : 'bom_logs');
                    await addDoc(logsRef, {
                        projectId: currentProjectId,
                        projectName: currentProjectName,
                        itemName: name,
                        action: 'เพิ่มลิงก์ไฟล์เขียนแบบ',
                        actorRole: mentraRole,
                        details: `เพิ่มลิงก์ Google Drive: ${name}`,
                        actorName: uploaderName,
                        timestamp: serverTimestamp()
                    });
                } catch (err) { console.error('Log error:', err); }

                document.getElementById('uploadForm').reset();
                Swal.mixin({ toast: true, position: 'bottom-end', showConfirmButton: false, timer: 3000 }).fire({ icon: 'success', title: 'บันทึกลิงก์เรียบร้อย' });
            } catch (error) {
                Swal.fire("Error", error.message, "error");
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<i class="fa-solid fa-link"></i> <span>บันทึกลิงก์</span>';
            }
        }

        window.deleteDrawing = async (id, storagePath) => {
            if (role !== 'admin' && role !== 'material') return;

            const result = await Swal.fire({
                title: 'ยืนยันการลบไฟล์?',
                text: "ไฟล์ที่ลบจะไม่สามารถกู้คืนได้!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'ลบไฟล์',
                cancelButtonText: 'ยกเลิก'
            });

            if (result.isConfirmed) {
                Swal.fire({ title: 'กำลังลบ...', didOpen: () => Swal.showLoading(), allowOutsideClick: false });
                try {
                    // Delete from Firebase Storage
                    if (storagePath) {
                        const storageRef = ref(storage, storagePath);
                        try {
                            await deleteObject(storageRef);
                        } catch (err) {
                            console.warn("Storage file might already be deleted or missing permissions", err);
                        }
                    }
                    // Delete from firestore
                    await deleteDoc(doc(getDrawingsRef(), id));

                    Swal.fire('ลบสำเร็จ!', '', 'success');
                } catch (error) {
                    Swal.fire('Error', error.message, 'error');
                }
            }
        }
    </script>
    </main>
    </div>
    </div>
</body>

</html>