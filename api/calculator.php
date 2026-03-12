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
    <title>คำนวณราคา — Mentra BOM</title>

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
    <!-- Export Libraries -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/exceljs@4.4.0/dist/exceljs.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/file-saver@2.0.5/dist/FileSaver.min.js" defer></script>

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

        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
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

        /* Item row checkbox */
        .item-row {
            transition: all 0.15s ease;
        }

        .item-row:hover {
            background: #f8fafc;
        }

        .item-row.selected-item {
            background: linear-gradient(135deg, #eff6ff, #faf5ff);
            border-color: #93c5fd;
        }

        .custom-check {
            width: 22px;
            height: 22px;
            border: 2px solid #cbd5e1;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .custom-check:hover {
            border-color: #3b82f6;
        }

        .custom-check.checked {
            background: linear-gradient(135deg, #2563eb, #7c3aed);
            border-color: transparent;
        }

        .custom-check.checked::after {
            content: '\f00c';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            font-size: 11px;
            color: white;
        }

        /* Result card pulse */
        @keyframes gentlePulse {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.02);
            }
        }

        .result-pulse {
            animation: gentlePulse 2s ease-in-out infinite;
        }

        /* Set input */
        .set-input:focus {
            border-color: #7c3aed !important;
            box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.15) !important;
        }

        /* Loading */
        #calcLoading {
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
    </style>
</head>

<body class="text-slate-700">

    <!-- Loading -->
    <div id="calcLoading">
        <div class="relative">
            <div class="w-14 h-14 border-4 border-violet-200/50 border-dashed rounded-full animate-spin"></div>
            <div
                class="absolute top-0 left-0 w-14 h-14 border-4 border-violet-500 border-t-transparent rounded-full animate-spin">
            </div>
        </div>
        <p class="mt-4 text-slate-600 font-medium text-sm animate-pulse">กำลังโหลดข้อมูล...</p>
    </div>

    <!-- Sidebar and Header -->
    <?php include 'sidebar.php'; ?>

    <!-- Main Content -->
    <div class="max-w-5xl mx-auto px-3 md:px-6 py-4 md:py-6 space-y-5 fade-in-up w-full">

        <!-- Project Info Bar -->
        <div
            class="glass-panel p-4 md:p-5 border-l-4 border-violet-500 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <div class="flex flex-col gap-1">
                <p class="text-[10px] text-slate-400 uppercase font-bold tracking-wider mb-1"><i
                        class="fa-solid fa-layer-group mr-1 text-violet-500"></i>โหมดการคำนวณ</p>
                <div class="flex items-center gap-3">
                    <button onclick="startSelection()"
                        class="bg-violet-600 hover:bg-violet-700 text-white px-5 py-2.5 rounded-2xl text-xs font-bold transition-all shadow-lg shadow-violet-200 flex items-center gap-2 active:scale-95 group">
                        <i class="fa-solid fa-magnifying-glass-plus group-hover:rotate-12 transition-transform"></i>
                        เลือกโครงการ & โหมด
                    </button>
                    <div class="h-8 w-px bg-slate-200 hidden md:block"></div>
                </div>
            </div>
            <div class="flex-1 px-2">
                <p class="text-[10px] text-slate-400 uppercase font-bold tracking-wider mb-0.5">โครงการที่เลือก</p>
                <h2 class="text-base md:text-xl font-extrabold text-slate-800 truncate max-w-[200px] md:max-w-md"
                    id="projectNameDisplay">กรุณาเลือกโครงการ</h2>
            </div>
            <div class="flex items-center gap-2 flex-wrap sm:flex-nowrap">
                <div class="flex bg-slate-100 p-1 rounded-xl border border-slate-200">
                    <button onclick="selectAll()"
                        class="hover:bg-white hover:text-blue-600 text-slate-500 px-3 py-1.5 rounded-lg text-[11px] font-bold transition-all flex items-center gap-1.5">
                        <i class="fa-solid fa-check"></i> ทั้งหมด
                    </button>
                    <button onclick="deselectAll()"
                        class="hover:bg-white hover:text-slate-700 text-slate-500 px-3 py-1.5 rounded-lg text-[11px] font-bold transition-all flex items-center gap-1.5">
                        <i class="fa-solid fa-xmark"></i> ยกเลิก
                    </button>
                </div>
                <div class="h-6 w-px bg-slate-200 mx-1 hidden sm:block"></div>
                <button onclick="window.calcExportPDF()"
                    class="bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded-xl text-xs font-bold transition-all shadow-sm flex items-center gap-1.5 active:scale-95">
                    <i class="fa-solid fa-file-pdf"></i> PDF
                </button>
                <button onclick="window.calcExportExcel()"
                    class="bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-2 rounded-xl text-xs font-bold transition-all shadow-sm flex items-center gap-1.5 active:scale-95">
                    <i class="fa-solid fa-file-excel"></i> Excel
                </button>
            </div>
        </div>
    </div>

    <!-- Calculator Panel -->
    <div class="glass-panel p-5 md:p-8 border border-violet-100/50 relative overflow-hidden">
        <div
            class="absolute top-0 right-0 -mt-4 -mr-4 w-28 h-28 bg-violet-500 opacity-5 rounded-full pointer-events-none">
        </div>
        <div
            class="absolute bottom-0 left-0 -mb-6 -ml-6 w-20 h-20 bg-orange-500 opacity-5 rounded-full pointer-events-none">
        </div>

        <h3 class="text-lg md:text-xl font-bold text-slate-800 flex items-center gap-2 mb-6">
            <i class="fa-solid fa-calculator text-violet-500"></i> ผลการคำนวณ
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-6">
            <!-- Sum Box -->
            <div
                class="bg-gradient-to-br from-blue-50 to-blue-100/50 p-5 rounded-2xl border border-blue-200/60 text-center">
                <p class="text-[10px] text-blue-500 uppercase font-extrabold tracking-widest mb-2">ราคารวมที่เลือก
                    (SUM)</p>
                <h3 class="text-2xl md:text-3xl font-extrabold text-blue-600" id="sumDisplay">฿0.00</h3>
                <p class="text-[10px] text-blue-400 mt-1" id="sumItemsNote">0 รายการ</p>
            </div>

            <!-- Multiplier Box -->
            <div
                class="bg-gradient-to-br from-violet-50 to-purple-100/50 p-5 rounded-2xl border border-violet-200/60 text-center flex flex-col items-center justify-center">
                <p class="text-[10px] text-violet-500 uppercase font-extrabold tracking-widest mb-3">จำนวนชุด (×)
                </p>
                <div class="flex items-center gap-2">
                    <button onclick="changeQty(-1)"
                        class="w-10 h-10 rounded-xl bg-white border border-violet-200 text-violet-600 font-bold text-lg shadow-sm hover:bg-violet-50 transition-all active:scale-95">−</button>
                    <input type="number" id="setQty" value="1" min="1" max="9999"
                        class="set-input w-20 text-center text-2xl font-extrabold text-violet-700 bg-white border-2 border-violet-200 rounded-xl py-2 outline-none transition-all"
                        oninput="recalculate()">
                    <button onclick="changeQty(1)"
                        class="w-10 h-10 rounded-xl bg-white border border-violet-200 text-violet-600 font-bold text-lg shadow-sm hover:bg-violet-50 transition-all active:scale-95">+</button>
                </div>
                <p class="text-[10px] text-violet-400 mt-2">ชุด / Set</p>
            </div>

            <!-- Grand Total Box -->
            <div
                class="bg-gradient-to-br from-orange-50 to-amber-100/50 p-5 rounded-2xl border border-orange-200/60 text-center relative">
                <div class="result-pulse">
                    <p class="text-[10px] text-orange-600 uppercase font-extrabold tracking-widest mb-2">ยอดรวมสุทธิ
                    </p>
                    <h3 class="text-3xl md:text-4xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-orange-600 to-red-500"
                        id="grandTotalCalc">฿0.00</h3>
                    <p class="text-[10px] text-orange-400 mt-1" id="grandTotalNote">0 รายการ × 1 ชุด</p>
                </div>
            </div>
        </div>

        <!-- Formula Display -->
        <div class="mt-5 bg-slate-50 rounded-xl p-4 border border-slate-100 text-center">
            <p class="text-xs text-slate-400 mb-1 font-bold uppercase tracking-wider">สูตรคำนวณ</p>
            <p class="text-sm md:text-base font-bold text-slate-600">
                <span class="text-blue-600" id="formulaSum">฿0.00</span>
                <span class="text-slate-400 mx-2">×</span>
                <span class="text-violet-600" id="formulaQty">1 ชุด</span>
                <span class="text-slate-400 mx-2">=</span>
                <span class="text-orange-600 text-lg" id="formulaTotal">฿0.00</span>
            </p>
        </div>
    </div>

    <!-- Items List -->
    <div class="glass-panel p-4 md:p-6 border border-gray-100/50">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-4 border-b pb-4 border-slate-100">
            <h3 class="text-base md:text-lg font-bold text-slate-800 flex items-center gap-2 flex-shrink-0">
                <i class="fa-solid fa-list-check text-violet-500"></i> เลือกรายการวัสดุ
            </h3>
            <div class="flex-1 max-w-md w-full relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <i class="fa-solid fa-magnifying-glass text-slate-400"></i>
                </div>
                <input type="text" id="searchInput" class="bg-slate-50 border border-slate-200 text-slate-900 text-sm rounded-xl focus:ring-violet-500 focus:border-violet-500 block w-full pl-10 p-2.5 transition-all shadow-sm" placeholder="ค้นหาชื่อวัสดุ หรือ รายละเอียด..." oninput="window.filterItems()">
                <button type="button" id="clearSearchBtn" onclick="window.clearSearch()" class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-slate-600 hidden">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <span class="text-xs text-slate-400 flex-shrink-0 font-medium" id="selectedCount">เลือกแล้ว 0 รายการ</span>
        </div>

        <div id="itemsList" class="space-y-2">
            <div class="text-center py-12 text-slate-300">
                <i class="fa-solid fa-spinner fa-spin text-2xl text-violet-400"></i>
                <p class="mt-2 text-sm">กำลังโหลดรายการ...</p>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="text-center text-[10px] text-slate-400 py-4">
        <span class="flex items-center gap-2 justify-center opacity-70">
            <i class="fa-solid fa-shield-halved text-green-500"></i> Secured by Firebase
            <span class="w-1 h-1 rounded-full bg-slate-300"></span>
            Mentra BOM v3.0 — Price Calculator
        </span>
    </div>
    </div>

    <script type="module">
        import { initializeApp } from "https://www.gstatic.com/firebasejs/11.6.1/firebase-app.js";
        import { getFirestore, collection, query, where, getDocs, orderBy } from "https://www.gstatic.com/firebasejs/11.6.1/firebase-firestore.js";
        import { getAuth, signInAnonymously, signInWithCustomToken, onAuthStateChanged } from "https://www.gstatic.com/firebasejs/11.6.1/firebase-auth.js";

        // Config
        let firebaseConfig;
        let isCanvasEnv = false;
        try {
            if (typeof __firebase_config !== 'undefined') {
                firebaseConfig = JSON.parse(__firebase_config);
                isCanvasEnv = true;
            }
        } catch (e) { }

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

        const getItemsRef = () => isCanvasEnv ? collection(db, 'artifacts', appId, 'public', 'data', 'bom_items') : collection(db, 'bom_items');
        const getProjectsRef = () => isCanvasEnv ? collection(db, 'artifacts', appId, 'public', 'data', 'bom_projects') : collection(db, 'bom_projects');

        // URL params
        const params = new URLSearchParams(window.location.search);
        const projectId = params.get('project');
        const projectName = decodeURIComponent(params.get('name') || 'โครงการ');

        document.getElementById('projectNameDisplay').textContent = projectName;

        if (!projectId) {
            document.getElementById('itemsList').innerHTML = '<div class="text-center py-12 text-red-400"><i class="fa-solid fa-circle-exclamation text-2xl"></i><p class="mt-2 text-sm">ไม่พบ Project ID — กรุณากลับไปเลือกโครงการ</p></div>';
            document.getElementById('calcLoading').style.display = 'none';
        }

        let items = [];
        let filteredItems = [];
        let selectedIds = new Set();
        let currentMode = 'per_project'; // 'per_project' or 'all_projects'
        let selectedProjectIds = [];
        let allProjects = [];
        let searchTerm = '';

        const fetchAllProjects = async () => {
            try {
                const q = query(getProjectsRef(), orderBy("name", "asc"));
                const snap = await getDocs(q);
                allProjects = [];
                snap.forEach(d => allProjects.push({ id: d.id, ...d.data() }));
            } catch (e) {
                console.error("Fetch projects error:", e);
            }
        };

        const startSelection = async () => {
            await fetchAllProjects();

            const { value: mode } = await Swal.fire({
                title: 'เลือกรูปแบบการคำนวณ',
                html: `
                    <div class="grid grid-cols-1 gap-3 p-2 text-left">
                        <div onclick="Swal.clickConfirm(); window._selMode='single'" class="p-4 bg-blue-50/50 hover:bg-blue-50 border-2 border-blue-100 hover:border-blue-400 rounded-2xl cursor-pointer transition-all flex items-center gap-4 group">
                            <div class="w-12 h-12 bg-blue-600 text-white rounded-xl flex items-center justify-center text-xl shadow-lg shadow-blue-200"><i class="fa-solid fa-file-circle-check"></i></div>
                            <div><p class="font-bold text-blue-900">คำนวณรายโครงการเดียว</p><p class="text-xs text-blue-600/70">แสดงรายการวัสดุแบบละเอียดรายชิ้น</p></div>
                        </div>
                        <div onclick="Swal.clickConfirm(); window._selMode='multi'" class="p-4 bg-violet-50/50 hover:bg-violet-50 border-2 border-violet-100 hover:border-violet-400 rounded-2xl cursor-pointer transition-all flex items-center gap-4 group">
                            <div class="w-12 h-12 bg-violet-600 text-white rounded-xl flex items-center justify-center text-xl shadow-lg shadow-violet-200"><i class="fa-solid fa-layer-group"></i></div>
                            <div><p class="font-bold text-violet-900">คำนวณรวบรวมหลายโครงการ</p><p class="text-xs text-violet-600/70">เลือกเฉพาะบางโครงการและแสดงสรุปยอดรวม</p></div>
                        </div>
                        <div onclick="Swal.clickConfirm(); window._selMode='all'" class="p-4 bg-emerald-50/50 hover:bg-emerald-50 border-2 border-emerald-100 hover:border-emerald-400 rounded-2xl cursor-pointer transition-all flex items-center gap-4 group">
                            <div class="w-12 h-12 bg-emerald-600 text-white rounded-xl flex items-center justify-center text-xl shadow-lg shadow-emerald-200"><i class="fa-solid fa-globe"></i></div>
                            <div><p class="font-bold text-emerald-900">คำนวณทุกโครงการที่มีทั้งหมด</p><p class="text-xs text-emerald-600/70">รวบรวมข้อมูลจากทุกโครงการในระบบทันที</p></div>
                        </div>
                    </div>
                `,
                showConfirmButton: false,
                showCancelButton: true,
                cancelButtonText: 'ปิดหน้าต่าง',
                width: '500px'
            });

            const modeChoice = window._selMode;
            if (!modeChoice) return;

            if (modeChoice === 'all') {
                currentMode = 'all_projects';
                selectedProjectIds = allProjects.map(p => p.id);
                document.getElementById('projectNameDisplay').textContent = 'สรุปทุกโครงการ';
                document.getElementById('projectNameDisplay').classList.add('text-violet-600');
                loadItems();
            } else if (modeChoice === 'single') {
                currentMode = 'per_project';
                const { value: selectedId } = await Swal.fire({
                    title: 'เลือกโครงการหลัก',
                    width: '700px',
                    showCancelButton: true,
                    cancelButtonText: 'กลับ',
                    html: `<div class="grid grid-cols-1 sm:grid-cols-2 gap-3 max-h-[60vh] overflow-y-auto p-4 text-left">${allProjects.map(p => `
                        <label class="relative flex flex-col gap-2 p-3 bg-white rounded-2xl cursor-pointer hover:ring-2 hover:ring-blue-200 transition-all border border-slate-100 shadow-sm group">
                            <input type="radio" name="proj_radio" value="${p.id}" class="peer hidden">
                            <div class="w-full h-24 bg-slate-100 rounded-xl overflow-hidden mb-1">${p.coverImage ? `<img src="${p.coverImage}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">` : `<div class="w-full h-full flex items-center justify-center text-slate-300"><i class="fa-solid fa-folder-open text-3xl"></i></div>`}</div>
                            <div class="flex flex-col"><span class="text-sm font-bold text-slate-700 truncate">${p.name}</span><span class="text-[10px] text-slate-400">ID: ${p.id.substring(0, 8)}...</span></div>
                            <div class="peer-checked:bg-blue-50 absolute inset-0 rounded-2xl -z-0 border-2 border-transparent peer-checked:border-blue-500 transition-all"></div>
                        </label>`).join('')}</div>`,
                    confirmButtonText: 'ตกลง',
                    preConfirm: () => (document.querySelector('input[name="proj_radio"]:checked'))?.value
                });
                if (selectedId) {
                    selectedProjectIds = [selectedId];
                    const p = allProjects.find(x => x.id === selectedId);
                    document.getElementById('projectNameDisplay').textContent = p ? p.name : 'โครงการ';
                    document.getElementById('projectNameDisplay').classList.remove('text-violet-600');
                    loadItems();
                } else if (selectedId === undefined && modeChoice) return startSelection();
            } else if (modeChoice === 'multi') {
                currentMode = 'all_projects';
                const { value: multiSelected } = await Swal.fire({
                    title: 'เลือกโครงการที่ต้องการรวม',
                    width: '700px',
                    showCancelButton: true,
                    cancelButtonText: 'กลับ',
                    html: `<div class="grid grid-cols-1 sm:grid-cols-2 gap-3 max-h-[60vh] overflow-y-auto p-4 text-left">${allProjects.map(p => `
                        <label class="relative flex flex-col gap-2 p-3 bg-white rounded-2xl cursor-pointer hover:ring-2 hover:ring-violet-200 transition-all border border-slate-100 shadow-sm group">
                            <input type="checkbox" name="proj_check" value="${p.id}" class="peer absolute top-3 right-3 w-5 h-5 rounded-full text-violet-600 focus:ring-violet-500 z-10">
                            <div class="w-full h-24 bg-slate-100 rounded-xl overflow-hidden mb-1">${p.coverImage ? `<img src="${p.coverImage}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">` : `<div class="w-full h-full flex items-center justify-center text-slate-300"><i class="fa-solid fa-folder-open text-3xl"></i></div>`}</div>
                            <div class="flex flex-col"><span class="text-sm font-bold text-slate-700 truncate">${p.name}</span><span class="text-[10px] text-slate-400">ID: ${p.id.substring(0, 8)}...</span></div>
                            <div class="peer-checked:bg-violet-50 absolute inset-0 rounded-2xl -z-0 border-2 border-transparent peer-checked:border-violet-500 transition-all"></div>
                        </label>`).join('')}</div>`,
                    confirmButtonText: 'สรุปข้อมูลที่เลือก',
                    preConfirm: () => {
                        const sel = Array.from(document.querySelectorAll('input[name="proj_check"]:checked')).map(el => el.value);
                        if (sel.length === 0) Swal.showValidationMessage('กรุณาเลือกอย่างน้อย 1 โครงการ');
                        return sel;
                    }
                });
                if (multiSelected) {
                    selectedProjectIds = multiSelected;
                    document.getElementById('projectNameDisplay').textContent = `สรุป ${selectedProjectIds.length} โครงการ`;
                    document.getElementById('projectNameDisplay').classList.add('text-violet-600');
                    loadItems();
                } else if (multiSelected === undefined && modeChoice) return startSelection();
            }
        };

        const selectPerProject = () => startSelection();
        const selectAllProjects = () => startSelection();
        const showModeSelection = () => startSelection();

        // Expose to global scope (required for onclick= in HTML inside type="module")
        window.startSelection = startSelection;

        const escapeHtml = (str) => {
            if (str == null) return '';
            return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#39;');
        };

        // Primary Functions
        const recalculate = () => {
            const selectedItems = items.filter(i => selectedIds.has(i.id));
            const sum = selectedItems.reduce((acc, i) => acc + (i.total || 0), 0);
            const qty = Math.max(1, parseInt(document.getElementById('setQty').value || '1'));
            const grandTotal = sum * qty;

            if (document.getElementById('sumDisplay')) document.getElementById('sumDisplay').textContent = '฿' + sum.toLocaleString(undefined, { minimumFractionDigits: 2 });
            if (document.getElementById('sumItemsNote')) document.getElementById('sumItemsNote').textContent = `${selectedItems.length} รายการ`;
            if (document.getElementById('selectedCount')) document.getElementById('selectedCount').textContent = `เลือกแล้ว ${selectedItems.length} รายการ`;
            if (document.getElementById('grandTotalCalc')) document.getElementById('grandTotalCalc').textContent = '฿' + grandTotal.toLocaleString(undefined, { minimumFractionDigits: 2 });
            if (document.getElementById('grandTotalNote')) document.getElementById('grandTotalNote').textContent = `${selectedItems.length} รายการ × ${qty} ชุด`;

            if (document.getElementById('formulaSum')) document.getElementById('formulaSum').textContent = '฿' + sum.toLocaleString(undefined, { minimumFractionDigits: 2 });
            if (document.getElementById('formulaQty')) document.getElementById('formulaQty').textContent = qty + ' ชุด';
            if (document.getElementById('formulaTotal')) document.getElementById('formulaTotal').textContent = '฿' + grandTotal.toLocaleString(undefined, { minimumFractionDigits: 2 });
        };
        window.recalculate = recalculate;

        const renderItemsList = () => {
            if (!filteredItems.length) {
                if (searchTerm) {
                    document.getElementById('itemsList').innerHTML = '<div class="text-center py-12 text-slate-400"><i class="fa-solid fa-magnifying-glass text-3xl mb-3 text-slate-300"></i><p class="text-sm font-medium">ไม่พบรายการ \'<span class="text-violet-500">' + escapeHtml(searchTerm) + '</span>\'</p><p class="text-[10px] mt-1">ลองค้นหาด้วยคำอื่น หรือตรวจสอบตัวสะกดอีกครั้ง</p></div>';
                } else {
                    document.getElementById('itemsList').innerHTML = '<div class="text-center py-12 text-slate-300"><i class="fa-regular fa-folder-open text-3xl"></i><p class="mt-2 text-sm">ไม่มีรายการในโครงการนี้</p></div>';
                }
                return;
            }

            let html = '';
            if (selectedProjectIds.length > 1 && !searchTerm) {
                const projectSummary = {};
                filteredItems.forEach(i => {
                    if (!projectSummary[i.projectId]) {
                        projectSummary[i.projectId] = { name: i.projectName, total: 0, count: 0, ids: [] };
                    }
                    projectSummary[i.projectId].total += (i.total || 0);
                    projectSummary[i.projectId].count++;
                    projectSummary[i.projectId].ids.push(i.id);
                });

                Object.entries(projectSummary).forEach(([pid, data]) => {
                    const allSelected = data.ids.every(id => selectedIds.has(id));
                    const someSelected = data.ids.some(id => selectedIds.has(id));

                    html += `
                    <div class="item-row flex items-center gap-4 p-5 rounded-3xl border border-slate-200/80 cursor-pointer mb-3 transition-all ${allSelected ? 'selected-item bg-violet-50/50 border-violet-200 shadow-sm' : 'hover:border-slate-300 hover:bg-slate-50'}" onclick="window.toggleProjectGroup('${pid}')">
                        <div class="custom-check ${allSelected ? 'checked' : someSelected ? 'opacity-50 checked' : ''}"></div>
                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-violet-500 to-fuchsia-500 flex items-center justify-center text-white flex-shrink-0 shadow-lg shadow-violet-200">
                            <i class="fa-solid fa-folder-tree text-lg"></i>
                        </div>
                        <div class="flex-grow min-w-0">
                            <p class="font-bold text-lg text-slate-800 truncate">${escapeHtml(data.name)}</p>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="px-2 py-0.5 rounded-lg bg-slate-100 text-slate-500 text-[10px] font-bold border border-slate-200">ID: ${pid.substring(0, 6)}</span>
                                <span class="text-[11px] text-slate-400 font-medium">${data.count} รายการวัสดุ</span>
                            </div>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <p class="font-black text-xl text-violet-600">฿${data.total.toLocaleString(undefined, { minimumFractionDigits: 2 })}</p>
                            <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest mt-0.5">Project Total</p>
                        </div>
                    </div>`;
                });
            } else {
                filteredItems.forEach(item => {
                    const isSelected = selectedIds.has(item.id);
                    const statusText = item.status === 'ordered' ? '🚚 สั่งแล้ว' : item.status === 'received' ? '✅ ได้รับ' : item.status === 'cancelled' ? '❌ ยกเลิก' : '⏳ รอ';
                    html += `
                    <div class="item-row flex items-center gap-3 p-3 md:p-4 rounded-xl border border-slate-200/80 cursor-pointer mb-2 transition-all ${isSelected ? 'selected-item ring-2 ring-violet-200 border-transparent shadow-sm' : 'hover:border-slate-300 hover:bg-slate-50'}" data-id="${escapeHtml(item.id)}" onclick="window.toggleItem('${escapeHtml(item.id)}')">
                        <div class="custom-check ${isSelected ? 'checked' : ''}" id="check-${escapeHtml(item.id)}"></div>
                        ${item.image ? `<img src="${item.image}" class="w-10 h-10 rounded-lg object-cover border border-slate-100 flex-shrink-0">` : '<span class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-slate-100 text-slate-300 flex-shrink-0"><i class="fa-regular fa-image"></i></span>'}
                        <div class="flex-grow min-w-0">
                            <div class="flex items-center gap-2">
                                <p class="font-bold text-sm text-slate-700 truncate">${escapeHtml(item.name)}</p>
                                ${selectedProjectIds.length > 1 ? `<span class="px-1.5 py-0.5 rounded-md bg-violet-50 text-violet-500 text-[9px] font-bold border border-violet-100 truncate max-w-[150px]"><i class="fa-solid fa-folder-open mr-1 opacity-70"></i>${escapeHtml(item.projectName || 'N/A')}</span>` : ''}
                            </div>
                            <p class="text-[10px] text-slate-400 truncate">${escapeHtml(item.details) || '-'}</p>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <p class="font-extrabold text-sm text-blue-600">฿${(item.price || 0).toLocaleString(undefined, { minimumFractionDigits: 2 })}</p>
                            <p class="text-[10px] text-slate-400">x${item.qty || 1} = <span class="font-bold text-slate-600">฿${(item.total || 0).toLocaleString(undefined, { minimumFractionDigits: 2 })}</span></p>
                        </div>
                        <div class="text-[10px] text-slate-400 w-16 text-center flex-shrink-0 hidden sm:block">${statusText}</div>
                    </div>`;
                });
            }
            document.getElementById('itemsList').innerHTML = html;
        };

        const loadItems = async () => {
            try {
                if (selectedProjectIds.length === 0) {
                    document.getElementById('itemsList').innerHTML = '<div class="text-center py-12 text-slate-300"><i class="fa-solid fa-folder-open text-3xl"></i><p class="mt-2 text-sm">กรุณาเลือกโครงการเพื่อนนำข้อมูลมาคำนวณ</p></div>';
                    return;
                }

                document.getElementById('itemsList').innerHTML = '<div class="text-center py-12 text-slate-300"><i class="fa-solid fa-spinner fa-spin text-2xl text-violet-400 mb-3"></i><p class="text-sm font-medium">กำลังโหลดและประมวลผลข้อมูลวัสดุ...</p><p class="text-[10px] mt-1 opacity-70">รอสักครู่ ระบบกำลังรวบรวมข้อมูลจาก ' + selectedProjectIds.length + ' โครงการ</p></div>';

                items = [];
                // Bypassing the 30-item 'in' query limit using Promise.allSettled with multiple queries
                // This fetches all items for any number of projects concurrently like we did for global search
                const fetchPromises = selectedProjectIds.map(pid => {
                    const qItem = query(getItemsRef(), where("projectId", "==", pid));
                    return getDocs(qItem);
                });

                const results = await Promise.allSettled(fetchPromises);
                
                results.forEach((result, index) => {
                    if (result.status === "fulfilled") {
                        const snap = result.value;
                        const pid = selectedProjectIds[index];
                        const p = allProjects.find(x => x.id === pid);
                        const pName = p ? p.name : 'N/A';
                        
                        snap.forEach(d => {
                            const data = d.data();
                            items.push({ id: d.id, ...data, projectName: pName });
                            if (selectedProjectIds.length > 1) selectedIds.add(d.id);
                        });
                    } else {
                        console.warn(`Failed to fetch items for project ${selectedProjectIds[index]}:`, result.reason);
                    }
                });

                // In-memory sort by item name using Thai locale
                items.sort((a, b) => (a.name || '').localeCompare(b.name || '', 'th'));
                
                // Clear search initially when loading new project contexts
                document.getElementById('searchInput').value = '';
                searchTerm = '';
                document.getElementById('clearSearchBtn').classList.add('hidden');
                
                filteredItems = [...items];
                renderItemsList();
                recalculate();
            } catch (e) {
                console.error('Load error:', e);
                document.getElementById('itemsList').innerHTML = `<div class="text-center py-12 text-red-400"><p class="text-sm">โหลดข้อมูลไม่สำเร็จ: ${escapeHtml(e.message)}</p></div>`;
            }
        };

        window.filterItems = () => {
            const input = document.getElementById('searchInput').value;
            searchTerm = input.trim().toLowerCase();
            
            const clearBtn = document.getElementById('clearSearchBtn');
            if (searchTerm.length > 0) {
                clearBtn.classList.remove('hidden');
            } else {
                clearBtn.classList.add('hidden');
            }

            if (!searchTerm) {
                filteredItems = [...items];
            } else {
                filteredItems = items.filter(item => {
                    const nameMatch = (item.name || '').toLowerCase().includes(searchTerm);
                    const detailMatch = (item.details || '').toLowerCase().includes(searchTerm);
                    const projMatch = (item.projectName || '').toLowerCase().includes(searchTerm);
                    return nameMatch || detailMatch || projMatch;
                });
            }
            
            renderItemsList();
        };

        window.clearSearch = () => {
            document.getElementById('searchInput').value = '';
            window.filterItems();
            document.getElementById('searchInput').focus();
        };

        window.toggleProjectGroup = (pid) => {
            const pItems = items.filter(i => i.projectId === pid);
            const allSel = pItems.every(i => selectedIds.has(i.id));
            pItems.forEach(i => {
                if (allSel) selectedIds.delete(i.id);
                else selectedIds.add(i.id);
            });
            renderItemsList();
            recalculate();
        };

        window.toggleItem = (id) => {
            if (selectedIds.has(id)) selectedIds.delete(id);
            else selectedIds.add(id);
            renderItemsList();
            recalculate();
        };

        window.selectAll = () => {
            items.forEach(i => selectedIds.add(i.id));
            renderItemsList();
            recalculate();
        };

        window.deselectAll = () => {
            selectedIds.clear();
            renderItemsList();
            recalculate();
        };

        window.changeQty = (delta) => {
            const input = document.getElementById('setQty');
            let val = Math.max(1, parseInt(input.value || '1') + delta);
            input.value = val;
            recalculate();
        };

        // ============================
        // Export PDF — Calculator
        // ============================
        window.calcExportPDF = () => {
            const selectedItems = items.filter(i => selectedIds.has(i.id));
            if (!selectedItems.length) return Swal.fire('แจ้งเตือน', 'กรุณาเลือกรายการอย่างน้อย 1 รายการ', 'warning');
            Swal.fire({ title: 'กำลังสร้าง PDF...', didOpen: () => Swal.showLoading() });

            const qty = Math.max(1, parseInt(document.getElementById('setQty').value || '1'));
            const fmtNum = (n) => n.toLocaleString(undefined, { minimumFractionDigits: 2 });
            const dateStr = new Date().toLocaleDateString('th-TH', { year: 'numeric', month: 'long', day: 'numeric' });

            let displayItems = [];
            let isAggregated = selectedProjectIds.length > 1;

            if (isAggregated) {
                const summary = {};
                selectedItems.forEach(i => {
                    if (!summary[i.projectId]) summary[i.projectId] = { name: i.projectName, total: 0, count: 0 };
                    summary[i.projectId].total += (i.total || 0);
                    summary[i.projectId].count++;
                });
                displayItems = Object.values(summary).map(s => ({
                    name: `${s.name}`,
                    details: `รวม ${s.count} รายการวัสดุ`,
                    price: s.total,
                    qty: 1,
                    total: s.total
                }));
            } else {
                displayItems = selectedItems;
            }

            const sum = selectedItems.reduce((acc, i) => acc + (i.total || 0), 0);
            const grandTotal = sum * qty;

            const ITEMS_PER_PAGE = 15;
            const totalPages = Math.ceil(displayItems.length / ITEMS_PER_PAGE);

            const makeHeader = (pageNum) => `
                <div style="display:flex; justify-content:space-between; margin-bottom:14px; border-bottom:3px solid #7c3aed; padding-bottom:12px;">
                    <div style="display:flex; align-items:center; gap:12px;">
                        <img src="Mentra_Solution_Tranparency.png" style="height:40px;">
                        <div><h1 style="font-size:17px; font-weight:bold; margin:0;">Mentra Solution Co., Ltd.</h1><p style="font-size:11px; color:#666; margin:2px 0 0;">ใบคำนวณราคาวัสดุ (Price Calculator)</p></div>
                    </div>
                    <div style="text-align:right;">
                        <h2 style="font-size:15px; color:#7c3aed; margin:0;">${escapeHtml(isAggregated ? 'สรุปหลายโครงการ' : projectName)}</h2>
                        <p style="font-size:9px; color:#94a3b8; margin:2px 0 0;">วันที่: ${dateStr} | หน้า ${pageNum}/${totalPages}</p>
                    </div>
                </div>`;

            const tableHead = `<thead><tr style="background:#f1f5f9;">
                <th style="border:1px solid #ddd; padding:7px; width:30px;">#</th>
                <th style="border:1px solid #ddd; padding:7px;">${isAggregated ? 'โครงการ' : 'รายการ / รายละเอียด'}</th>
                <th style="border:1px solid #ddd; padding:7px; width:85px;">${isAggregated ? 'ยอดรวมโครงการ' : 'ราคา/หน่วย'}</th>
                <th style="border:1px solid #ddd; padding:7px; width:50px;">จำนวน</th>
                <th style="border:1px solid #ddd; padding:7px; width:95px;">รวม</th>
            </tr></thead>`;

            let pagesHtml = '';
            for (let p = 0; p < totalPages; p++) {
                const startIdx = p * ITEMS_PER_PAGE;
                const pageItems = displayItems.slice(startIdx, startIdx + ITEMS_PER_PAGE);
                const isLastPage = (p === totalPages - 1);

                let rows = pageItems.map((i, x) => {
                    const linkHtml = i.link ? `<br><a href="${escapeHtml(i.link)}" style="color:#3b82f6;font-size:9px;word-break:break-all;">🔗 ${escapeHtml(i.link).substring(0, 50)}${i.link.length > 50 ? '...' : ''}</a>` : '';
                    return `<tr>
                        <td style="border:1px solid #ddd;padding:8px;text-align:center;font-size:10px;">${startIdx + x + 1}</td>
                        <td style="border:1px solid #ddd;padding:8px;font-size:11px;">
                            <b style="${isAggregated ? 'font-size:12px; color:#1e293b;' : ''}">${escapeHtml(i.name)}</b>${linkHtml}
                            ${i.details ? '<br><span style="color:#64748b;font-size:10px;">' + escapeHtml(i.details) + '</span>' : ''}
                        </td>
                        <td style="border:1px solid #ddd;padding:8px;text-align:right;font-size:11px;">฿${fmtNum(i.price || 0)}</td>
                        <td style="border:1px solid #ddd;padding:8px;text-align:center;font-size:11px;">${i.qty || 1}</td>
                        <td style="border:1px solid #ddd;padding:8px;text-align:right;font-weight:bold;color:#2563eb;font-size:11px;">฿${fmtNum(i.total || 0)}</td>
                    </tr>`;
                }).join('');

                const infoBar = p === 0 ? `<div style="background:#f5f3ff; border:1px solid #ddd5f9; border-radius:8px; padding:10px 14px; margin-bottom:14px; display:flex; justify-content:space-between; align-items:center;">
                    <div>
                        <span style="font-size:10px; color:#7c3aed; font-weight:bold;">🧮 จำนวนชุด: ${qty} ชุด</span>
                        <span style="margin-left:16px; font-size:10px; color:#64748b;">📦 ${isAggregated ? `${displayItems.length} โครงการ` : `${selectedItems.length} รายการ`} ที่รวม</span>
                    </div>
                    <div style="font-size:10px; color:#64748b;">ราคารวมต่อชุด: <b style="color:#2563eb;">฿${fmtNum(sum)}</b></div>
                </div>` : '';

                let footer = '';
                if (isLastPage) {
                    footer = `<tfoot>
                        <tr style="background:#eff6ff;">
                            <td colspan="4" style="border:1px solid #bfdbfe; padding:7px; text-align:right; font-weight:bold;">ยอดรวมวัสดุทั้งหมด</td>
                            <td style="border:1px solid #bfdbfe; padding:7px; text-align:right; color:#2563eb; font-weight:bold;">฿${fmtNum(sum)}</td>
                        </tr>
                        <tr style="background:#f5f3ff;">
                            <td colspan="4" style="border:1px solid #ddd5f9; padding:7px; text-align:right; font-weight:bold;">รวมเป็นเซต (× ${qty} ชุด)</td>
                            <td style="border:1px solid #ddd5f9; padding:7px; text-align:right; color:#7c3aed; font-weight:bold;">× ${qty}</td>
                        </tr>
                        <tr style="background:#fff7ed;">
                            <td colspan="4" style="border:2px solid #f97316; padding:9px; text-align:right; font-weight:bold; font-size:12px;">ยอดรวมสุทธิ (${qty} ชุด)</td>
                            <td style="border:2px solid #f97316; padding:9px; text-align:right; color:#ea580c; font-weight:bold; font-size:14px;">฿${fmtNum(grandTotal)}</td>
                        </tr>
                    </tfoot>`;
                }

                pagesHtml += `<div style="font-family:'Prompt'; padding:24px 30px; width:210mm; background:white; color:#333; ${!isLastPage ? 'page-break-after:always;' : ''}">
                    ${makeHeader(p + 1)}
                    ${infoBar}
                    <table style="width:100%; border-collapse:collapse; font-size:10px;">
                        ${tableHead}
                        <tbody>${rows}</tbody>
                        ${footer}
                    </table>
                    ${isLastPage ? '<p style="text-align:center; font-size:8px; color:#94a3b8; margin-top:14px;">Generated by Mentra BOM Calculator v3.0 • พัฒนาโดย ธนภูมิ แดงประดับ</p>' : ''}
                </div>`;
            }

            const el = document.createElement('div');
            el.innerHTML = pagesHtml;
            const safeFileName = projectName.replace(/[^a-zA-Z0-9ก-๙\s_-]/g, '').trim() || 'Unnamed';
            html2pdf().set({ margin: 0, filename: `Calc-${safeFileName}-${qty}set.pdf`, image: { type: 'jpeg', quality: 0.98 }, html2canvas: { scale: 2 }, jsPDF: { unit: 'mm', format: 'a4' } }).from(el).save().then(Swal.close);
        };

        // ============================
        // Export Excel — Calculator
        // ============================
        window.calcExportExcel = async () => {
            const selectedItems = items.filter(i => selectedIds.has(i.id));
            if (!selectedItems.length) return Swal.fire('แจ้งเตือน', 'กรุณาเลือกรายการอย่างน้อย 1 รายการ', 'warning');
            Swal.fire({ title: 'กำลังสร้าง Excel...', didOpen: () => Swal.showLoading(), allowOutsideClick: false });

            const qty = Math.max(1, parseInt(document.getElementById('setQty').value || '1'));
            const sum = selectedItems.reduce((acc, i) => acc + (i.total || 0), 0);
            const grandTotal = sum * qty;

            try {
                const wb = new ExcelJS.Workbook();
                wb.creator = 'Mentra BOM Calculator';
                wb.created = new Date();

                const ws = wb.addWorksheet('Price Calculator', {
                    properties: { tabColor: { argb: 'FF7C3AED' } },
                    pageSetup: {
                        paperSize: 9, orientation: 'landscape', fitToPage: true, fitToWidth: 1, fitToHeight: 0,
                        margins: { left: 0.4, right: 0.4, top: 0.6, bottom: 0.5, header: 0.3, footer: 0.3 },
                        horizontalCentered: true
                    },
                    headerFooter: { oddFooter: '&L&8Mentra BOM Calculator&C&8หน้า &P / &N&R&8&D' }
                });

                ws.columns = [
                    { key: 'no', width: 6 },
                    { key: 'name', width: 32 },
                    { key: 'details', width: 28 },
                    { key: 'link', width: 24 },
                    { key: 'price', width: 16 },
                    { key: 'qty', width: 10 },
                    { key: 'total', width: 18 }
                ];

                const DARK_NAVY = 'FF1E293B', VIOLET = 'FF7C3AED', BLUE = 'FF2563EB', ORANGE = 'FFF97316';
                const LIGHT_VIOLET = 'FFF5F3FF', LIGHT_BLUE = 'FFEFF6FF', LIGHT_ORANGE = 'FFFFF7ED', WHITE = 'FFFFFFFF', GRAY_BG = 'FFF8FAFC';
                const BORDER_COLOR = 'FFE2E8F0';
                const thinBorder = { style: 'thin', color: { argb: BORDER_COLOR } };
                const allBorders = { top: thinBorder, left: thinBorder, bottom: thinBorder, right: thinBorder };

                // Row 1: Company
                ws.mergeCells('A1:G1');
                const r1 = ws.getRow(1); r1.height = 36;
                const c1 = ws.getCell('A1');
                c1.value = '     Mentra Solution Co., Ltd.';
                c1.font = { name: 'Prompt', size: 18, bold: true, color: { argb: WHITE } };
                c1.fill = { type: 'gradient', gradient: 'angle', degree: 0, stops: [{ position: 0, color: { argb: DARK_NAVY } }, { position: 1, color: { argb: 'FF334155' } }] };
                c1.alignment = { vertical: 'middle', horizontal: 'left' };

                // Row 2: Report title
                ws.mergeCells('A2:G2');
                const r2 = ws.getRow(2); r2.height = 28;
                const c2 = ws.getCell('A2');
                c2.value = '     ใบคำนวณราคาวัสดุ — Price Calculator';
                c2.font = { name: 'Prompt', size: 12, color: { argb: VIOLET } };
                c2.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: DARK_NAVY } };
                c2.alignment = { vertical: 'middle', horizontal: 'left' };

                // Row 3: Project
                ws.mergeCells('A3:G3');
                const r3 = ws.getRow(3); r3.height = 26;
                const c3 = ws.getCell('A3');
                c3.value = `📁  โครงการ: ${projectName}`;
                c3.font = { name: 'Prompt', size: 13, bold: true, color: { argb: VIOLET } };
                c3.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: LIGHT_VIOLET } };
                c3.alignment = { vertical: 'middle', horizontal: 'left' };
                c3.border = { bottom: { style: 'medium', color: { argb: VIOLET } } };

                // Row 4: Date + Set info
                ws.mergeCells('A4:G4');
                const r4 = ws.getRow(4); r4.height = 22;
                const c4 = ws.getCell('A4');
                c4.value = `📅  วันที่: ${new Date().toLocaleDateString('th-TH', { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' })}   |   🧮 จำนวน ${qty} ชุด   |   📦 ${selectedItems.length} รายการ`;
                c4.font = { name: 'Prompt', size: 10, color: { argb: 'FF64748B' } };
                c4.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: LIGHT_ORANGE } };
                c4.alignment = { vertical: 'middle', horizontal: 'left' };

                // Row 5: Accent bar
                ws.mergeCells('A5:G5');
                const r5 = ws.getRow(5); r5.height = 6;
                ws.getCell('A5').fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: VIOLET } };

                // Row 6: Headers
                const headerLabels = ['#', 'ชื่อรายการ', 'รายละเอียด', 'ลิงก์ร้านค้า', 'ราคา/หน่วย', 'จำนวน', 'รวม (บาท)'];
                const headerRow = ws.getRow(6); headerRow.height = 30;
                headerLabels.forEach((label, idx) => {
                    const cell = headerRow.getCell(idx + 1);
                    cell.value = label;
                    cell.font = { name: 'Prompt', size: 11, bold: true, color: { argb: WHITE } };
                    cell.fill = { type: 'gradient', gradient: 'angle', degree: 0, stops: [{ position: 0, color: { argb: VIOLET } }, { position: 1, color: { argb: 'FF8B5CF6' } }] };
                    cell.alignment = { vertical: 'middle', horizontal: (idx === 0 || idx === 5) ? 'center' : (idx === 4 || idx === 6) ? 'right' : 'left', wrapText: true };
                    cell.border = allBorders;
                });

                // Data rows
                selectedItems.forEach((item, idx) => {
                    const rowNum = 7 + idx;
                    const row = ws.getRow(rowNum); row.height = 26;
                    const isEven = idx % 2 === 0;
                    const bgColor = isEven ? WHITE : GRAY_BG;

                    const values = [idx + 1, item.name || '', item.details || '-', item.link || '-', item.price || 0, item.qty || 0, item.total || 0];
                    values.forEach((val, colIdx) => {
                        const cell = row.getCell(colIdx + 1);
                        cell.value = val;
                        cell.border = allBorders;
                        cell.font = { name: 'Prompt', size: 10 };
                        cell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: bgColor } };

                        if (colIdx === 0 || colIdx === 5) cell.alignment = { vertical: 'middle', horizontal: 'center' };
                        else if (colIdx === 4 || colIdx === 6) cell.alignment = { vertical: 'middle', horizontal: 'right' };
                        else cell.alignment = { vertical: 'middle', horizontal: 'left', wrapText: true };

                        if (colIdx === 4 || colIdx === 6) cell.numFmt = '#,##0.00';
                        if (colIdx === 5) cell.numFmt = '#,##0';
                        if (colIdx === 1) cell.font = { name: 'Prompt', size: 10, bold: true, color: { argb: 'FF1E293B' } };
                        if (colIdx === 6) cell.font = { name: 'Prompt', size: 10, bold: true, color: { argb: BLUE } };
                        if (colIdx === 3 && item.link) {
                            cell.value = { text: item.link, hyperlink: item.link };
                            cell.font = { name: 'Prompt', size: 9, color: { argb: 'FF3B82F6' }, underline: true };
                        }
                    });
                });

                // Accent bar
                const blankRowNum = 7 + selectedItems.length;
                const blankRow = ws.getRow(blankRowNum); blankRow.height = 4;
                for (let c = 1; c <= 7; c++) { ws.getCell(blankRowNum, c).fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: VIOLET } }; }

                // Subtotal row
                const subRowNum = blankRowNum + 1;
                ws.mergeCells(`A${subRowNum}:F${subRowNum}`);
                const subRow = ws.getRow(subRowNum); subRow.height = 28;
                const subLabel = subRow.getCell(1);
                subLabel.value = 'รวมต่อ 1 ชุด';
                subLabel.font = { name: 'Prompt', size: 12, bold: true, color: { argb: BLUE } };
                subLabel.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: LIGHT_BLUE } };
                subLabel.alignment = { vertical: 'middle', horizontal: 'right' };
                subLabel.border = allBorders;
                const subVal = subRow.getCell(7);
                subVal.value = sum; subVal.numFmt = '#,##0.00';
                subVal.font = { name: 'Prompt', size: 13, bold: true, color: { argb: BLUE } };
                subVal.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: LIGHT_BLUE } };
                subVal.alignment = { vertical: 'middle', horizontal: 'right' };
                subVal.border = allBorders;

                // Multiplier row
                const mulRowNum = subRowNum + 1;
                ws.mergeCells(`A${mulRowNum}:F${mulRowNum}`);
                const mulRow = ws.getRow(mulRowNum); mulRow.height = 26;
                const mulLabel = mulRow.getCell(1);
                mulLabel.value = `× ${qty} ชุด`;
                mulLabel.font = { name: 'Prompt', size: 11, bold: true, color: { argb: VIOLET } };
                mulLabel.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: LIGHT_VIOLET } };
                mulLabel.alignment = { vertical: 'middle', horizontal: 'right' };
                mulLabel.border = allBorders;
                const mulVal = mulRow.getCell(7);
                mulVal.value = `× ${qty}`;
                mulVal.font = { name: 'Prompt', size: 11, bold: true, color: { argb: VIOLET } };
                mulVal.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: LIGHT_VIOLET } };
                mulVal.alignment = { vertical: 'middle', horizontal: 'right' };
                mulVal.border = allBorders;

                // Grand total row
                const totalRowNum = mulRowNum + 1;
                ws.mergeCells(`A${totalRowNum}:F${totalRowNum}`);
                const totalRow = ws.getRow(totalRowNum); totalRow.height = 36;
                const totalLabel = totalRow.getCell(1);
                totalLabel.value = `ยอดรวมสุทธิ (${qty} ชุด)`;
                totalLabel.font = { name: 'Prompt', size: 14, bold: true, color: { argb: DARK_NAVY } };
                totalLabel.fill = { type: 'gradient', gradient: 'angle', degree: 0, stops: [{ position: 0, color: { argb: LIGHT_BLUE } }, { position: 1, color: { argb: LIGHT_ORANGE } }] };
                totalLabel.alignment = { vertical: 'middle', horizontal: 'right' };
                totalLabel.border = { top: { style: 'medium', color: { argb: ORANGE } }, bottom: { style: 'double', color: { argb: DARK_NAVY } }, left: thinBorder, right: thinBorder };
                const totalVal = totalRow.getCell(7);
                totalVal.value = grandTotal; totalVal.numFmt = '#,##0.00';
                totalVal.font = { name: 'Prompt', size: 16, bold: true, color: { argb: 'FFEA580C' } };
                totalVal.fill = { type: 'gradient', gradient: 'angle', degree: 0, stops: [{ position: 0, color: { argb: LIGHT_BLUE } }, { position: 1, color: { argb: LIGHT_ORANGE } }] };
                totalVal.alignment = { vertical: 'middle', horizontal: 'right' };
                totalVal.border = { top: { style: 'medium', color: { argb: ORANGE } }, bottom: { style: 'double', color: { argb: DARK_NAVY } }, left: thinBorder, right: thinBorder };

                // Footer
                const footerRowNum = totalRowNum + 1;
                ws.mergeCells(`A${footerRowNum}:G${footerRowNum}`);
                const footerRow = ws.getRow(footerRowNum); footerRow.height = 20;
                const footerCell = footerRow.getCell(1);
                footerCell.value = `Generated by Mentra BOM Calculator v3.0  •  ${selectedItems.length} รายการ × ${qty} ชุด  •  พัฒนาโดย ธนภูมิ แดงประดับ`;
                footerCell.font = { name: 'Prompt', size: 8, italic: true, color: { argb: 'FF94A3B8' } };
                footerCell.alignment = { vertical: 'middle', horizontal: 'center' };

                ws.autoFilter = { from: 'A6', to: `G${6 + selectedItems.length}` };
                ws.views = [{ state: 'frozen', ySplit: 6, activeCell: 'A7' }];

                const buffer = await wb.xlsx.writeBuffer();
                const blob = new Blob([buffer], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
                const safeFileName = projectName.replace(/[^a-zA-Z0-9ก-๙\s_-]/g, '').trim() || 'Unnamed';
                saveAs(blob, `Calc-${safeFileName}-${qty}set.xlsx`);

                Swal.mixin({ toast: true, position: 'bottom-end', showConfirmButton: false, timer: 3000 }).fire({ icon: 'success', title: 'ดาวน์โหลด Excel สำเร็จ' });
            } catch (e) {
                console.error('Excel export error:', e);
                Swal.fire('Error', 'ไม่สามารถสร้างไฟล์ Excel ได้: ' + e.message, 'error');
            }
        };

        if (projectId) {
            const links = document.querySelectorAll('a[href^="bom.php"], a[href^="logs.php"], a[href^="drawings.php"]');
            links.forEach(link => {
                const baseHref = link.getAttribute('href').split('?')[0];
                const queryStr = `?project=${encodeURIComponent(projectId)}&name=${encodeURIComponent(projectName)}`;
                link.setAttribute('href', baseHref + queryStr);
            });
        }

        // Init
        const initAuth = async () => {
            try {
                if (typeof __initial_auth_token !== 'undefined' && __initial_auth_token) await signInWithCustomToken(auth, __initial_auth_token);
                else await signInAnonymously(auth);
            } catch (e) { console.error("Auth:", e); if (projectId) loadItems(); }
        };

        onAuthStateChanged(auth, (user) => {
            const loader = document.getElementById('calcLoading');
            if (loader) {
                loader.style.opacity = '0';
                setTimeout(() => loader.style.display = 'none', 500);
            }
            if (user) {
                // Modified: No longer auto-show on login
                // showModeSelection();
                if (!projectId) {
                    renderItemsList(); // Show placeholder
                }
            }
        });

        setTimeout(() => {
            const loader = document.getElementById('calcLoading');
            if (loader && loader.style.display !== 'none') {
                loader.style.opacity = '0';
                setTimeout(() => loader.style.display = 'none', 400);
            }
        }, 1800);

        initAuth();
    </script>
</body>

</html>