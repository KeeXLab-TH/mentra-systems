<!DOCTYPE html>
<html lang="th">

<head>
    <!-- *** ระบบป้องกัน: ตรวจสอบสิทธิ์ก่อนโหลดหน้าเว็บ *** -->
    <script>
        (function () {
            var allowedRoles = ['admin', 'material', 'purchasing', 'viewer'];
            var role = localStorage.getItem('mentra_role');
            if (!role || allowedRoles.indexOf(role) === -1) {
                localStorage.removeItem('mentra_role');
                localStorage.removeItem('mentra_user');
                window.location.href = 'index.php';
            }
        })();
    </script>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สถานะการสั่งซื้อ — Mentra BOM</title>

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

        /* Filter Pills */
        .filter-pill {
            background: white;
            color: #64748b;
            border-color: #e2e8f0;
            cursor: pointer;
        }

        .filter-pill:hover {
            background: #f1f5f9;
            border-color: #cbd5e1;
            transform: translateY(-1px);
        }

        .filter-pill.active-filter {
            background: linear-gradient(135deg, #2563eb, #7c3aed);
            color: white;
            border-color: transparent;
            box-shadow: 0 2px 8px rgba(37, 99, 235, 0.3);
        }

        .filter-pill.active-filter:hover {
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.4);
        }

        /* Summary Cards */
        .summary-card {
            cursor: pointer;
            border: 2px solid transparent;
            border-radius: 1.25rem;
            padding: 1.25rem;
            transition: all 0.25s ease;
            position: relative;
            overflow: hidden;
        }

        .summary-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 24px -4px rgba(0, 0, 0, 0.12);
        }

        .summary-card.active-card {
            border-color: currentColor;
            transform: translateY(-3px);
            box-shadow: 0 8px 24px -4px rgba(0, 0, 0, 0.12);
        }

        .summary-card::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 80px;
            height: 80px;
            border-radius: 50%;
            opacity: 0.08;
            transform: translate(20px, -20px);
        }

        /* Progress Bar */
        .progress-track {
            height: 6px;
            background: #e2e8f0;
            border-radius: 999px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            border-radius: 999px;
            transition: width 0.8s cubic-bezier(0.22, 1, 0.36, 1);
        }

        /* Project Group Header */
        .project-group-header {
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .project-group-header:hover {
            background: rgba(99, 102, 241, 0.04);
        }

        /* Item Row */
        .status-item-row {
            transition: all 0.15s ease;
        }

        .status-item-row:hover {
            background: #fafbff;
        }

        /* Loading */
        #statusLoading {
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

        /* Mobile Card Layout */
        @media (max-width: 767px) {
            .status-table thead {
                display: none;
            }

            .status-table,
            .status-table tbody {
                display: block;
                width: 100%;
            }

            .status-table tr {
                display: flex;
                flex-direction: column;
                background: white;
                border-radius: 16px;
                padding: 14px;
                margin-bottom: 10px;
                border: 1px solid #e2e8f0;
                box-shadow: 0 2px 8px -2px rgba(0, 0, 0, 0.06);
                gap: 6px;
            }

            .status-table td {
                padding: 0 !important;
                border: none !important;
                text-align: left !important;
            }

            .status-table td::before {
                content: attr(data-label);
                display: block;
                font-size: 9px;
                font-weight: 700;
                color: #94a3b8;
                text-transform: uppercase;
                letter-spacing: 0.05em;
                margin-bottom: 1px;
            }

            .status-table td:first-child::before {
                display: none;
            }

            .status-table td:first-child {
                font-weight: 700;
                font-size: 14px;
                color: #1e293b;
                padding-bottom: 4px !important;
                border-bottom: 1px solid #f1f5f9 !important;
            }

            .summary-cards-grid {
                grid-template-columns: repeat(2, 1fr) !important;
            }
        }

        .swal2-popup {
            font-family: 'Prompt', sans-serif;
            border-radius: 1.25rem !important;
        }

        @keyframes countUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .count-animate {
            animation: countUp 0.5s ease-out;
        }

        /* Multiplier Input Group */
        .multiplier-group {
            display: flex;
            align-items: center;
            gap: 6px;
            background: rgba(99, 102, 241, 0.06);
            border: 1.5px solid rgba(99, 102, 241, 0.2);
            border-radius: 12px;
            padding: 4px 8px;
            transition: all 0.2s ease;
        }
        .multiplier-group:hover,
        .multiplier-group:focus-within {
            border-color: rgba(99, 102, 241, 0.5);
            background: rgba(99, 102, 241, 0.1);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.08);
        }
        .multiplier-group input {
            width: 52px;
            padding: 4px 6px;
            border: none;
            background: transparent;
            font-family: 'Prompt', sans-serif;
            font-size: 13px;
            font-weight: 700;
            color: #4338ca;
            text-align: center;
            outline: none;
        }
        .multiplier-group input::placeholder {
            color: #a5b4fc;
            font-weight: 400;
        }
        .multiplier-group input::-webkit-outer-spin-button,
        .multiplier-group input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        .multiplier-group input[type=number] {
            -moz-appearance: textfield;
        }
        .multiplier-save-btn {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 4px 10px;
            font-size: 10px;
            font-weight: 700;
            font-family: 'Prompt', sans-serif;
            cursor: pointer;
            transition: all 0.2s ease;
            white-space: nowrap;
        }
        .multiplier-save-btn:hover {
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            transform: translateY(-1px);
            box-shadow: 0 3px 8px rgba(99, 102, 241, 0.3);
        }
        .multiplier-save-btn:active {
            transform: translateY(0);
        }
        .multiplier-save-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }
        .multiplier-badge {
            display: inline-flex;
            align-items: center;
            gap: 3px;
            padding: 2px 8px;
            border-radius: 8px;
            font-size: 10px;
            font-weight: 800;
            background: linear-gradient(135deg, #eef2ff, #e0e7ff);
            color: #4338ca;
            border: 1px solid #c7d2fe;
        }
        .multiplied-value {
            color: #4338ca;
            font-weight: 800;
        }
        .original-value {
            color: #94a3b8;
            font-size: 10px;
            text-decoration: line-through;
            margin-left: 4px;
        }

        /* Energy Flow Animation */
        @keyframes energyShimmer {
            0% { transform: translateX(-150%) skewX(-15deg); }
            100% { transform: translateX(150%) skewX(-15deg); }
        }
        .energy-bar-container {
            position: relative;
            overflow: hidden;
            border-radius: 999px;
            background: #f1f5f9;
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.06);
        }
        .energy-flow {
            position: relative;
            overflow: hidden;
        }
        .energy-flow::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 150%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.45), rgba(255, 255, 255, 0.8), rgba(255, 255, 255, 0.45), transparent);
            animation: energyShimmer 1.8s infinite linear;
            pointer-events: none;
        }
        .energy-glow-amber { box-shadow: 0 0 12px rgba(245, 158, 11, 0.5); }
        .energy-glow-blue { box-shadow: 0 0 12px rgba(59, 130, 246, 0.5); }
        .energy-glow-green { box-shadow: 0 0 15px rgba(34, 197, 94, 0.6); }
        .energy-glow-slate { box-shadow: 0 0 8px rgba(148, 163, 184, 0.3); }
    </style>
</head>

<body class="text-slate-700 bg-slate-50">

    <!-- Loading Screen -->
    <div id="statusLoading">
        <div class="relative">
            <div class="w-14 h-14 border-4 border-indigo-200/50 border-dashed rounded-full animate-spin"></div>
            <div
                class="absolute top-0 left-0 w-14 h-14 border-4 border-indigo-500 border-t-transparent rounded-full animate-spin">
            </div>
        </div>
        <p class="mt-4 text-slate-600 font-medium text-sm animate-pulse">กำลังโหลดสถานะการสั่งซื้อ...</p>
    </div>

    <!-- Sidebar and Header -->
    <?php include 'sidebar.php'; ?>

    <!-- Main Content -->
    <div class="max-w-6xl mx-auto px-3 md:px-6 py-4 md:py-6 space-y-5 fade-in-up w-full">

        <!-- Page Title Bar -->
        <div class="glass-panel p-4 md:p-5 border-l-4 border-indigo-500 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div class="flex-1">
                <p class="text-[10px] text-slate-400 uppercase font-bold tracking-wider mb-1">
                    <i class="fa-solid fa-truck-fast mr-1 text-indigo-500"></i> Order Tracking
                </p>
                <h2 class="text-lg md:text-xl font-extrabold text-slate-800">
                    สถานะการสั่งซื้อทั้งหมด
                </h2>
                <p class="text-xs text-slate-400 mt-0.5" id="lastUpdateText">กำลังโหลดข้อมูล...</p>
            </div>
            <div class="flex-1 w-full sm:w-auto flex justify-center sm:justify-end" id="overallProgressContainer">
                <!-- UI drawn via JS -->
            </div>
            <div class="flex items-center gap-2 flex-shrink-0">
                <button onclick="window.refreshData()"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 rounded-2xl text-xs font-bold transition-all shadow-lg shadow-indigo-200 flex items-center gap-2 active:scale-95 btn-lift">
                    <i class="fa-solid fa-arrows-rotate"></i> รีเฟรชข้อมูล
                </button>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4 summary-cards-grid">
            <!-- All -->
            <div class="summary-card glass-panel active-card" onclick="window.filterByStatus('all')" data-status="all"
                style="color: #6366f1;">
                <div class="flex items-center justify-between mb-2">
                    <div class="w-10 h-10 rounded-xl bg-indigo-100 flex items-center justify-center text-indigo-600">
                        <i class="fa-solid fa-layer-group text-lg"></i>
                    </div>
                    <span
                        class="text-[10px] font-bold uppercase tracking-wider text-indigo-400 bg-indigo-50 px-2 py-1 rounded-lg">ALL</span>
                </div>
                <p class="text-3xl font-extrabold text-indigo-600 count-animate" id="countAll">0</p>
                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mt-1">ทั้งหมด</p>
                <div class="progress-track mt-3">
                    <div class="progress-fill bg-indigo-500" style="width: 100%;" id="progressAll"></div>
                </div>
            </div>

            <!-- Pending -->
            <div class="summary-card glass-panel" onclick="window.filterByStatus('pending')" data-status="pending"
                style="color: #64748b;">
                <div class="flex items-center justify-between mb-2">
                    <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-500">
                        <i class="fa-solid fa-hourglass-half text-lg"></i>
                    </div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 bg-slate-50 px-2 py-1 rounded-lg">⏳</span>
                </div>
                <p class="text-3xl font-extrabold text-slate-600 count-animate" id="countPending">0</p>
                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mt-1">รอดำเนินการ</p>
                <div class="progress-track mt-3">
                    <div class="progress-fill bg-slate-400" style="width: 0%;" id="progressPending"></div>
                </div>
            </div>

            <!-- Ordered -->
            <div class="summary-card glass-panel" onclick="window.filterByStatus('ordered')" data-status="ordered"
                style="color: #2563eb;">
                <div class="flex items-center justify-between mb-2">
                    <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center text-blue-600">
                        <i class="fa-solid fa-truck text-lg"></i>
                    </div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-blue-400 bg-blue-50 px-2 py-1 rounded-lg">🚚</span>
                </div>
                <p class="text-3xl font-extrabold text-blue-600 count-animate" id="countOrdered">0</p>
                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mt-1">สั่งซื้อแล้ว</p>
                <div class="progress-track mt-3">
                    <div class="progress-fill bg-blue-500" style="width: 0%;" id="progressOrdered"></div>
                </div>
            </div>

            <!-- Received -->
            <div class="summary-card glass-panel" onclick="window.filterByStatus('received')" data-status="received"
                style="color: #16a34a;">
                <div class="flex items-center justify-between mb-2">
                    <div class="w-10 h-10 rounded-xl bg-green-100 flex items-center justify-center text-green-600">
                        <i class="fa-solid fa-circle-check text-lg"></i>
                    </div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-green-400 bg-green-50 px-2 py-1 rounded-lg">✅</span>
                </div>
                <p class="text-3xl font-extrabold text-green-600 count-animate" id="countReceived">0</p>
                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mt-1">ได้รับของแล้ว</p>
                <div class="progress-track mt-3">
                    <div class="progress-fill bg-green-500" style="width: 0%;" id="progressReceived"></div>
                </div>
            </div>
        </div>

        <!-- Filter + Search Bar -->
        <div class="glass-panel p-4 md:p-5 border border-gray-100/50">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
                <!-- Filter Pills -->
                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider mr-1">
                        <i class="fa-solid fa-filter mr-1"></i>Filter:
                    </span>
                    <button onclick="window.filterByStatus('all')" data-filter="all"
                        class="filter-pill active-filter px-3 py-1.5 rounded-full text-xs font-bold transition-all duration-200 shadow-sm border">
                        ทั้งหมด
                    </button>
                    <button onclick="window.filterByStatus('pending')" data-filter="pending"
                        class="filter-pill px-3 py-1.5 rounded-full text-xs font-bold transition-all duration-200 shadow-sm border">
                        ⏳ รอดำเนินการ
                    </button>
                    <button onclick="window.filterByStatus('ordered')" data-filter="ordered"
                        class="filter-pill px-3 py-1.5 rounded-full text-xs font-bold transition-all duration-200 shadow-sm border">
                        🚚 สั่งซื้อแล้ว
                    </button>
                    <button onclick="window.filterByStatus('received')" data-filter="received"
                        class="filter-pill px-3 py-1.5 rounded-full text-xs font-bold transition-all duration-200 shadow-sm border">
                        ✅ ได้รับของแล้ว
                    </button>
                    <button onclick="window.filterByStatus('cancelled')" data-filter="cancelled"
                        class="filter-pill px-3 py-1.5 rounded-full text-xs font-bold transition-all duration-200 shadow-sm border">
                        ❌ ยกเลิก
                    </button>
                </div>

                <!-- Search -->
                <div class="flex-1 max-w-sm w-full relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <i class="fa-solid fa-magnifying-glass text-slate-400"></i>
                    </div>
                    <input type="text" id="searchInput"
                        class="bg-slate-50 border border-slate-200 text-slate-900 text-sm rounded-xl focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-10 p-2.5 transition-all shadow-sm"
                        placeholder="ค้นหาชื่อวัสดุ, โครงการ..." oninput="window.handleSearch()">
                    <button type="button" id="clearSearchBtn" onclick="window.clearSearch()"
                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-slate-600 hidden">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Items Table (grouped by project) -->
        <div class="glass-panel p-3 md:p-6 border border-gray-100/50" id="itemsContainer">
            <div class="flex items-center justify-between mb-4 border-b pb-3 border-slate-100">
                <h3 class="text-base md:text-lg font-bold text-slate-800 flex items-center gap-2">
                    <i class="fa-solid fa-list-check text-indigo-500"></i> รายการทั้งหมด
                </h3>
                <span class="text-xs text-slate-400 font-medium" id="visibleCount">0 รายการ</span>
            </div>

            <div id="itemsList">
                <div class="text-center py-16 text-slate-300">
                    <i class="fa-solid fa-spinner fa-spin text-2xl text-indigo-400"></i>
                    <p class="mt-2 text-sm">กำลังโหลดข้อมูล...</p>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div
            class="text-center text-[10px] text-slate-400 py-4 flex flex-col md:flex-row justify-between items-center gap-2">
            <span class="flex items-center gap-2 opacity-70">
                <i class="fa-solid fa-shield-halved text-green-500"></i> Secured by Firebase
                <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                Mentra BOM v3.0 — Order Status
            </span>
            <div class="group flex items-center gap-2">
                <span class="text-slate-400">พัฒนาระบบโดย</span>
                <a href="https://keexlab-th.github.io/" target="_blank"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-slate-200 rounded-full shadow-sm hover:shadow-md hover:border-orange-200 hover:bg-orange-50 transition-all duration-300 no-underline">
                    <span
                        class="w-2 h-2 rounded-full bg-gradient-to-r from-blue-500 to-orange-500 animate-pulse"></span>
                    <span
                        class="font-bold text-slate-700 group-hover:text-orange-600 transition-colors">ธนภูมิ แดงประดับ</span>
                    <i class="fa-solid fa-arrow-up-right-from-square text-[10px] text-slate-300 group-hover:text-orange-400"></i>
                </a>
            </div>
        </div>
    </div>
    </div> <!-- close main -->
    </div> <!-- close flex wrapper from sidebar -->

    <!-- ========================================== -->
    <!-- JAVASCRIPT LOGIC -->
    <!-- ========================================== -->
    <script type="module">
        import { initializeApp } from "https://www.gstatic.com/firebasejs/11.6.1/firebase-app.js";
        import { getFirestore, collection, query, where, getDocs, orderBy, doc, updateDoc } from "https://www.gstatic.com/firebasejs/11.6.1/firebase-firestore.js";
        import { getAuth, signInAnonymously, signInWithCustomToken, onAuthStateChanged, signOut } from "https://www.gstatic.com/firebasejs/11.6.1/firebase-auth.js";

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

        const getProjectsRef = () => isCanvasEnv ? collection(db, 'artifacts', appId, 'public', 'data', 'bom_projects') : collection(db, 'bom_projects');
        const getItemsRef = () => isCanvasEnv ? collection(db, 'artifacts', appId, 'public', 'data', 'bom_items') : collection(db, 'bom_items');

        const escapeHtml = (str) => {
            if (str == null) return '';
            return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#39;');
        };

        // State
        let allProjects = [];
        let allItems = [];
        let filteredItems = [];
        let currentStatusFilter = 'all';
        let searchTerm = '';
        let projectMultipliers = {}; // { projectId: multiplierValue }

        // Logout
        window.handleLogout = () => {
            Swal.fire({
                title: 'ออกจากระบบ?', icon: 'warning', showCancelButton: true,
                confirmButtonColor: '#ef4444', confirmButtonText: 'Logout'
            }).then((result) => {
                if (result.isConfirmed) {
                    localStorage.removeItem('mentra_role');
                    localStorage.removeItem('mentra_user');
                    signOut(auth).then(() => window.location.href = 'index.php');
                }
            });
        };

        // Auth
        const initAuth = async () => {
            try {
                if (typeof __initial_auth_token !== 'undefined' && __initial_auth_token) await signInWithCustomToken(auth, __initial_auth_token);
                else await signInAnonymously(auth);
            } catch (error) { console.error("Auth Failed:", error); loadAllData(); }
        };

        onAuthStateChanged(auth, (user) => {
            if (user) {
                const loader = document.getElementById('statusLoading');
                if (loader) { loader.style.opacity = '0'; setTimeout(() => loader.style.display = 'none', 400); }
                loadAllData();
            } else initAuth();
        });

        // Timeout fallback
        setTimeout(() => {
            const loader = document.getElementById('statusLoading');
            if (loader && loader.style.display !== 'none') {
                loader.style.opacity = '0';
                setTimeout(() => loader.style.display = 'none', 400);
            }
        }, 3000);

        initAuth();

        // --- Status Badge Helper ---
        const getStatusBadge = (status, itemId) => {
            const role = localStorage.getItem('mentra_role');
            const canEdit = ['admin', 'purchasing'].includes(role);
            let badgeHtml = '';

            switch (status) {
                case 'ordered': badgeHtml = '<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-blue-100 text-blue-700 border border-blue-200 whitespace-nowrap shadow-sm"><i class="fa-solid fa-truck"></i> สั่งแล้ว</span>'; break;
                case 'received': badgeHtml = '<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-green-100 text-green-700 border border-green-200 whitespace-nowrap shadow-sm"><i class="fa-solid fa-check"></i> ได้ของแล้ว</span>'; break;
                case 'cancelled': badgeHtml = '<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-red-100 text-red-700 border border-red-200 whitespace-nowrap shadow-sm"><i class="fa-solid fa-xmark"></i> ยกเลิก</span>'; break;
                default: badgeHtml = '<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-200 whitespace-nowrap shadow-sm"><i class="fa-solid fa-hourglass"></i> รอดำเนินการ</span>'; break;
            }

            if (canEdit && itemId) {
                return `<div class="relative inline-block text-left group">
                            <button type="button" class="focus:outline-none transition-transform active:scale-95" onclick="toggleStatusDropdown('${itemId}')">
                                ${badgeHtml}
                                <i class="fa-solid fa-caret-down text-[10px] ml-1 text-slate-400 group-hover:text-indigo-500"></i>
                            </button>
                            <div id="dropdown-${itemId}" class="hidden absolute right-0 sm:left-0 sm:right-auto mt-2 w-40 origin-top-right rounded-xl bg-white shadow-xl ring-1 ring-black ring-opacity-5 focus:outline-none z-50 overflow-hidden transform opacity-0 scale-95 transition-all duration-200">
                                <div class="py-1" role="none">
                                    <a href="#" onclick="updateItemStatus('${itemId}', 'pending'); return false;" class="text-slate-700 hover:bg-slate-50 hover:text-slate-900 block px-4 py-2 text-xs font-medium"><i class="fa-solid fa-hourglass w-4 mr-1 text-slate-400"></i> รอดำเนินการ</a>
                                    <a href="#" onclick="updateItemStatus('${itemId}', 'ordered'); return false;" class="text-blue-700 hover:bg-blue-50 hover:text-blue-900 block px-4 py-2 text-xs font-medium"><i class="fa-solid fa-truck w-4 mr-1 text-blue-400"></i> สั่งซื้อแล้ว</a>
                                    <a href="#" onclick="updateItemStatus('${itemId}', 'received'); return false;" class="text-green-700 hover:bg-green-50 hover:text-green-900 block px-4 py-2 text-xs font-medium"><i class="fa-solid fa-check w-4 mr-1 text-green-400"></i> ได้รับของแล้ว</a>
                                    <a href="#" onclick="updateItemStatus('${itemId}', 'cancelled'); return false;" class="text-red-700 hover:bg-red-50 hover:text-red-900 block px-4 py-2 text-xs font-medium"><i class="fa-solid fa-xmark w-4 mr-1 text-red-400"></i> ยกเลิก</a>
                                </div>
                            </div>
                        </div>`;
            }
            return badgeHtml;
        };
        
        // Status Dropdown Toggle
        window.toggleStatusDropdown = (itemId) => {
            // Close all other dropdowns
            document.querySelectorAll('[id^="dropdown-"]').forEach(el => {
                if(el.id !== `dropdown-${itemId}`) {
                    el.classList.add('hidden');
                    el.classList.replace('opacity-100', 'opacity-0');
                    el.classList.replace('scale-100', 'scale-95');
                }
            });

            const dropdown = document.getElementById(`dropdown-${itemId}`);
            if (dropdown.classList.contains('hidden')) {
                dropdown.classList.remove('hidden');
                // Small delay to allow display:block to apply before animating opacity/transform
                setTimeout(() => {
                    dropdown.classList.replace('opacity-0', 'opacity-100');
                    dropdown.classList.replace('scale-95', 'scale-100');
                }, 10);
            } else {
                dropdown.classList.replace('opacity-100', 'opacity-0');
                dropdown.classList.replace('scale-100', 'scale-95');
                setTimeout(() => dropdown.classList.add('hidden'), 200);
            }
        };

        // Close dropdowns when clicking outside
        window.onclick = function(event) {
            if (!event.target.closest('.group')) {
                document.querySelectorAll('[id^="dropdown-"]').forEach(el => {
                    if(!el.classList.contains('hidden')){
                        el.classList.replace('opacity-100', 'opacity-0');
                        el.classList.replace('scale-100', 'scale-95');
                        setTimeout(() => el.classList.add('hidden'), 200);
                    }
                });
            }
        }

        window.updateItemStatus = async (itemId, newStatus) => {
            const item = allItems.find(i => i.id === itemId);
            if(!item) return;

            // Optimistic UI update
            const oldStatus = item.status;
            item.status = newStatus;
            
            // Hide dropdown
            const dropdown = document.getElementById(`dropdown-${itemId}`);
            if(dropdown) {
                dropdown.classList.replace('opacity-100', 'opacity-0');
                dropdown.classList.replace('scale-100', 'scale-95');
                setTimeout(() => dropdown.classList.add('hidden'), 200);
            }

            // Quick re-render to show updated badge
            updateSummaryCards();
            applyFilters();

            try {
                // Background update to Firebase
                const itemRef = doc(getItemsRef(), itemId);
                await updateDoc(itemRef, { status: newStatus });
                
                Swal.mixin({ toast: true, position: 'bottom-end', showConfirmButton: false, timer: 1500 })
                    .fire({ icon: 'success', title: 'อัปเดตสถานะแล้ว' });
            } catch (error) {
                console.error("Error updating status:", error);
                // Revert optimistic update
                item.status = oldStatus;
                updateSummaryCards();
                applyFilters();
                
                Swal.mixin({ toast: true, position: 'bottom-end', showConfirmButton: false, timer: 3000 })
                    .fire({ icon: 'error', title: 'ไม่สามารถอัปเดตสถานะได้' });
            }
        };

        // --- Load All Data ---
        const loadAllData = async () => {
            try {
                // Load all projects
                const projSnap = await getDocs(query(getProjectsRef()));
                allProjects = [];
                projSnap.forEach(d => {
                    const projData = { id: d.id, ...d.data() };
                    allProjects.push(projData);
                    // Load multiplier from project doc
                    if (projData.multiplier && projData.multiplier > 0) {
                        projectMultipliers[projData.id] = projData.multiplier;
                    }
                });

                // Load all items from all projects concurrently
                const fetchPromises = allProjects.map(proj => {
                    const q = query(getItemsRef(), where("projectId", "==", proj.id));
                    return getDocs(q);
                });

                const results = await Promise.allSettled(fetchPromises);
                allItems = [];

                results.forEach((result, index) => {
                    if (result.status === "fulfilled") {
                        const snap = result.value;
                        const proj = allProjects[index];
                        snap.forEach(d => {
                            allItems.push({
                                id: d.id,
                                ...d.data(),
                                projectName: proj.name,
                                projectCover: proj.coverImage || null
                            });
                        });
                    }
                });

                // Sort by createdAt desc
                allItems.sort((a, b) => (b.createdAt?.seconds || 0) - (a.createdAt?.seconds || 0));

                updateSummaryCards();
                applyFilters();

                // Update timestamp
                const now = new Date().toLocaleTimeString('th-TH', { hour: '2-digit', minute: '2-digit' });
                document.getElementById('lastUpdateText').textContent = `อัปเดตล่าสุด: ${now} — พบ ${allItems.length} รายการจาก ${allProjects.length} โครงการ`;

            } catch (e) {
                console.error('Load error:', e);
                document.getElementById('itemsList').innerHTML = `<div class="text-center py-16 text-red-400"><i class="fa-solid fa-circle-exclamation text-3xl mb-3"></i><p class="text-sm font-medium">โหลดข้อมูลไม่สำเร็จ</p><p class="text-xs mt-1">${escapeHtml(e.message)}</p></div>`;
            }
        };

        // --- Summary Cards ---
        const updateSummaryCards = () => {
            const total = allItems.length;
            const pending = allItems.filter(i => (i.status || 'pending') === 'pending').length;
            const ordered = allItems.filter(i => i.status === 'ordered').length;
            const received = allItems.filter(i => i.status === 'received').length;
            const cancelled = allItems.filter(i => i.status === 'cancelled').length;

            document.getElementById('countAll').textContent = total;
            document.getElementById('countPending').textContent = pending;
            document.getElementById('countOrdered').textContent = ordered;
            document.getElementById('countReceived').textContent = received;

            // Progress bars
            const pct = (n) => total > 0 ? (n / total * 100).toFixed(1) : 0;
            document.getElementById('progressAll').style.width = '100%';
            document.getElementById('progressPending').style.width = pct(pending) + '%';
            document.getElementById('progressOrdered').style.width = pct(ordered) + '%';
            document.getElementById('progressReceived').style.width = pct(received) + '%';

            // Overall Progress
            const overallPct = total > 0 ? Math.round((received / total) * 100) : 0;
            let overallTextColor = 'text-slate-600';
            let overallBgColor = 'bg-slate-300';
            let overallBorderColor = 'border-slate-200';
            let glowClass = 'energy-glow-slate';
            
            if (overallPct === 100 && total > 0) {
                overallTextColor = 'text-green-600 font-extrabold';
                overallBgColor = 'bg-gradient-to-r from-green-400 via-green-500 to-emerald-500';
                overallBorderColor = 'border-green-300';
                glowClass = 'energy-glow-green';
            } else if (overallPct >= 50) {
                overallTextColor = 'text-blue-600 font-extrabold';
                overallBgColor = 'bg-gradient-to-r from-blue-400 via-blue-500 to-violet-500';
                overallBorderColor = 'border-blue-300';
                glowClass = 'energy-glow-blue';
            } else if (overallPct > 0) {
                overallTextColor = 'text-amber-600 font-extrabold';
                overallBgColor = 'bg-gradient-to-r from-amber-400 via-amber-500 to-orange-500';
                overallBorderColor = 'border-amber-300';
                glowClass = 'energy-glow-amber';
            }

            const overallContainer = document.getElementById('overallProgressContainer');
            if (overallContainer) {
                overallContainer.innerHTML = `
                    <div class="flex flex-col w-full max-w-sm bg-white/95 px-5 py-3 rounded-2xl border-2 ${overallBorderColor} shadow-md backdrop-blur-md transform transition-all hover:scale-[1.02]">
                        <div class="flex items-center gap-2 justify-between mb-2">
                            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider flex items-center gap-1.5"><i class="fa-solid fa-bolt text-indigo-400"></i> Overall Progress</span>
                            <span class="text-lg md:text-xl font-black ${overallTextColor} tracking-tighter shadow-sm">${overallPct}%</span>
                        </div>
                        <div class="w-full h-3 md:h-4 energy-bar-container border border-slate-200/50">
                            <div class="h-full ${overallBgColor} ${glowClass} transition-all duration-1000 ease-in-out energy-flow relative" style="width: ${overallPct}%">
                            </div>
                        </div>
                    </div>
                `;
            }
        };

        // --- Filter & Search ---
        const applyFilters = () => {
            let items = [...allItems];

            // Status filter
            if (currentStatusFilter !== 'all') {
                items = items.filter(i => (i.status || 'pending') === currentStatusFilter);
            }

            // Search filter
            if (searchTerm) {
                items = items.filter(i => {
                    const nameMatch = (i.name || '').toLowerCase().includes(searchTerm);
                    const detailMatch = (i.details || '').toLowerCase().includes(searchTerm);
                    const projMatch = (i.projectName || '').toLowerCase().includes(searchTerm);
                    const remarkMatch = (i.remark || '').toLowerCase().includes(searchTerm);
                    return nameMatch || detailMatch || projMatch || remarkMatch;
                });
            }

            filteredItems = items;
            document.getElementById('visibleCount').textContent = `${filteredItems.length} รายการ`;
            renderItems();
        };

        window.filterByStatus = (status) => {
            currentStatusFilter = status;

            // Update filter pills
            document.querySelectorAll('.filter-pill').forEach(btn => {
                btn.classList.remove('active-filter');
                if (btn.dataset.filter === status) btn.classList.add('active-filter');
            });

            // Update summary cards
            document.querySelectorAll('.summary-card').forEach(card => {
                card.classList.remove('active-card');
                if (card.dataset.status === status) card.classList.add('active-card');
            });

            applyFilters();
        };

        window.handleSearch = () => {
            searchTerm = document.getElementById('searchInput').value.trim().toLowerCase();
            const clearBtn = document.getElementById('clearSearchBtn');
            if (searchTerm.length > 0) clearBtn.classList.remove('hidden');
            else clearBtn.classList.add('hidden');
            applyFilters();
        };

        window.clearSearch = () => {
            document.getElementById('searchInput').value = '';
            searchTerm = '';
            document.getElementById('clearSearchBtn').classList.add('hidden');
            applyFilters();
            document.getElementById('searchInput').focus();
        };

        window.refreshData = () => {
            document.getElementById('itemsList').innerHTML = '<div class="text-center py-16 text-slate-300"><i class="fa-solid fa-spinner fa-spin text-2xl text-indigo-400"></i><p class="mt-2 text-sm">กำลังรีเฟรชข้อมูล...</p></div>';
            loadAllData();
        };

        // --- Render Items (grouped by project) ---
        const renderItems = () => {
            if (!filteredItems.length) {
                const msg = searchTerm
                    ? `ไม่พบรายการ '<span class="text-indigo-500">${escapeHtml(searchTerm)}</span>'`
                    : `ไม่มีรายการ${currentStatusFilter !== 'all' ? 'ในสถานะนี้' : ''}`;
                document.getElementById('itemsList').innerHTML = `<div class="text-center py-16 text-slate-400"><i class="fa-solid fa-inbox text-4xl text-slate-300 mb-3"></i><p class="text-sm font-medium">${msg}</p></div>`;
                return;
            }

            // Group by project
            const groups = {};
            const role = localStorage.getItem('mentra_role');
            const canEditMultiplier = ['admin', 'purchasing'].includes(role);

            filteredItems.forEach(item => {
                const pid = item.projectId || 'unknown';
                if (!groups[pid]) {
                    groups[pid] = {
                        name: item.projectName || 'ไม่ระบุโครงการ',
                        cover: item.projectCover,
                        items: [],
                        totalValue: 0,
                        statusCounts: { pending: 0, ordered: 0, received: 0, cancelled: 0 }
                    };
                }
                groups[pid].items.push(item);
                groups[pid].totalValue += (item.total || 0);
                const st = item.status || 'pending';
                if (groups[pid].statusCounts[st] !== undefined) groups[pid].statusCounts[st]++;
            });

            let html = '';

            Object.entries(groups).forEach(([pid, group]) => {
                const mult = projectMultipliers[pid] || 0;
                const multipliedTotal = mult > 0 ? group.totalValue * mult : group.totalValue;

                const totalGroupItems = group.items.length;
                let groupProgressPct = 0;
                if (totalGroupItems > 0) {
                    groupProgressPct = Math.round((group.statusCounts.received / totalGroupItems) * 100);
                }

                let progressTextColor = 'text-slate-500';
                let progressBgColor = 'bg-slate-300';
                let glowClass = 'energy-glow-slate';
                
                if (groupProgressPct === 100 && totalGroupItems > 0) {
                    progressTextColor = 'text-green-600 font-extrabold';
                    progressBgColor = 'bg-gradient-to-r from-green-400 via-green-500 to-emerald-500';
                    glowClass = 'energy-glow-green';
                } else if (groupProgressPct >= 50) {
                    progressTextColor = 'text-blue-600 font-extrabold';
                    progressBgColor = 'bg-gradient-to-r from-blue-400 via-blue-500 to-indigo-500';
                    glowClass = 'energy-glow-blue';
                } else if (groupProgressPct > 0) {
                    progressTextColor = 'text-amber-600 font-extrabold';
                    progressBgColor = 'bg-gradient-to-r from-amber-400 via-amber-500 to-orange-500';
                    glowClass = 'energy-glow-amber';
                }

                const groupProgressHtml = `
                    <div class="flex flex-col items-end gap-1.5 ml-2 md:ml-4 pl-3 md:pl-5 border-l-2 border-slate-100 justify-center min-w-[120px]">
                        <div class="flex items-center gap-1.5 w-full justify-between">
                            <span class="text-[10px] font-bold text-slate-400 hidden sm:inline tracking-wider uppercase">Received</span>
                            <span class="text-sm md:text-base font-black ${progressTextColor} tracking-tight">${groupProgressPct}%</span>
                        </div>
                        <div class="w-full h-2 md:h-2.5 energy-bar-container border border-slate-200/60">
                            <div class="h-full ${progressBgColor} ${glowClass} transition-all duration-1000 ease-out energy-flow relative" style="width: ${groupProgressPct}%">
                            </div>
                        </div>
                    </div>
                `;

                // Multiplier UI
                let multiplierHtml = '';
                if (canEditMultiplier) {
                    multiplierHtml = `
                        <div class="multiplier-group" title="ตัวคูณจำนวนสำหรับทุกรายการในชุดนี้">
                            <i class="fa-solid fa-xmark text-indigo-400" style="font-size:10px;"></i>
                            <input type="number" id="mult-${pid}" min="0" step="1" placeholder="—" value="${mult > 0 ? mult : ''}" 
                                onkeydown="if(event.key==='Enter'){saveMultiplier('${pid}');event.preventDefault();}">
                            <button class="multiplier-save-btn" onclick="saveMultiplier('${pid}')" id="multBtn-${pid}">
                                <i class="fa-solid fa-check"></i> บันทึก
                            </button>
                        </div>`;
                } else if (mult > 0) {
                    multiplierHtml = `<span class="multiplier-badge" title="ตัวคูณ ×${mult}"><i class="fa-solid fa-xmark" style="font-size:8px;"></i> ${mult}</span>`;
                }

                // Group Header
                html += `
                <div class="mb-5">
                    <div class="project-group-header flex items-center gap-2 p-3 md:p-4 rounded-2xl bg-gradient-to-r from-slate-50 to-white border border-slate-200/60 mb-3 shadow-sm">
                        <div class="w-11 h-11 rounded-xl overflow-hidden flex-shrink-0 border border-slate-200 shadow-sm bg-slate-100">
                            ${group.cover
                        ? `<img src="${group.cover}" class="w-full h-full object-cover">`
                        : `<div class="w-full h-full flex items-center justify-center text-slate-300"><i class="fa-solid fa-folder-open text-lg"></i></div>`
                    }
                        </div>
                        <div class="flex-grow min-w-0">
                            <h4 class="font-bold text-sm md:text-base text-slate-800 truncate">${escapeHtml(group.name)}</h4>
                            <div class="flex items-center gap-2 mt-0.5 flex-wrap">
                                <span class="text-[10px] text-slate-400 font-medium">${group.items.length} รายการ</span>
                                <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                                ${mult > 0 
                                    ? `<span class="text-[10px] font-bold text-indigo-600">฿${multipliedTotal.toLocaleString(undefined, { minimumFractionDigits: 2 })}</span>
                                       <span class="text-[9px] text-slate-400 line-through">฿${group.totalValue.toLocaleString(undefined, { minimumFractionDigits: 2 })}</span>`
                                    : `<span class="text-[10px] font-bold text-indigo-500">฿${group.totalValue.toLocaleString(undefined, { minimumFractionDigits: 2 })}</span>`
                                }
                            </div>
                        </div>
                        <div class="flex items-center gap-2 flex-shrink-0 flex-wrap justify-end">
                            ${multiplierHtml}
                            <div class="flex items-center gap-1 flex-wrap">
                                ${group.statusCounts.pending > 0 ? `<span class="px-1.5 py-0.5 rounded-md bg-slate-100 text-slate-500 text-[9px] font-bold border border-slate-200">⏳${group.statusCounts.pending}</span>` : ''}
                                ${group.statusCounts.ordered > 0 ? `<span class="px-1.5 py-0.5 rounded-md bg-blue-50 text-blue-600 text-[9px] font-bold border border-blue-200">🚚${group.statusCounts.ordered}</span>` : ''}
                                ${group.statusCounts.received > 0 ? `<span class="px-1.5 py-0.5 rounded-md bg-green-50 text-green-600 text-[9px] font-bold border border-green-200">✅${group.statusCounts.received}</span>` : ''}
                                ${group.statusCounts.cancelled > 0 ? `<span class="px-1.5 py-0.5 rounded-md bg-red-50 text-red-600 text-[9px] font-bold border border-red-200">❌${group.statusCounts.cancelled}</span>` : ''}
                            </div>
                            ${groupProgressHtml}
                        </div>
                    </div>

                    <!-- Items Table -->
                    <div class="overflow-x-auto rounded-xl md:border md:border-slate-200/80 bg-transparent md:bg-white">
                        <table class="w-full text-left border-collapse status-table">
                            <thead>
                                <tr class="bg-gradient-to-r from-slate-50 to-slate-100/50 text-xs text-slate-500 uppercase tracking-wider sticky top-0 z-10 font-semibold border-b border-slate-200">
                                    <th class="p-3 w-14 text-center">รูป</th>
                                    <th class="p-3 md:p-4">ชื่อรายการ</th>
                                    <th class="p-3 md:p-4 text-center w-16">จำนวน</th>
                                    <th class="p-3 md:p-4 text-right w-24">ราคารวม</th>
                                    <th class="p-3 md:p-4 text-center w-28">สถานะ</th>
                                    <th class="p-3 md:p-4 w-32">หมายเหตุ</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm divide-y divide-slate-100">`;

                group.items.forEach(item => {
                    const img = item.image
                        ? `<img src="${item.image}" class="w-9 h-9 object-cover rounded-lg shadow-sm border border-slate-100">`
                        : `<span class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-slate-100 text-slate-300"><i class="fa-regular fa-image text-sm"></i></span>`;

                    html += `
                                <tr class="status-item-row border-b border-slate-100 last:border-0">
                                    <td class="p-3 text-center align-middle" data-label="">${img}</td>
                                    <td class="p-3 align-middle" data-label="รายการ">
                                        <div class="font-bold text-sm text-slate-800">${escapeHtml(item.name)}</div>
                                        <div class="text-xs text-slate-400 mt-0.5 line-clamp-1">${escapeHtml(item.details) || ''}</div>
                                    </td>
                                    <td class="p-3 text-center align-middle font-bold text-slate-700" data-label="จำนวน">
                                        ${mult > 0 
                                            ? `<span class="multiplied-value">×${(item.qty || 1) * mult}</span><span class="original-value">×${item.qty || 1}</span>`
                                            : `×${item.qty || 1}`
                                        }
                                    </td>
                                    <td class="p-3 text-right align-middle font-extrabold text-sm" data-label="ราคารวม">
                                        ${mult > 0
                                            ? `<span class="multiplied-value">฿${((item.total || 0) * mult).toLocaleString(undefined, { minimumFractionDigits: 2 })}</span><span class="original-value block">฿${(item.total || 0).toLocaleString(undefined, { minimumFractionDigits: 2 })}</span>`
                                            : `<span class="text-indigo-600">฿${(item.total || 0).toLocaleString(undefined, { minimumFractionDigits: 2 })}</span>`
                                        }
                                    </td>
                                    <td class="p-3 align-middle text-center" data-label="สถานะ">${getStatusBadge(item.status, item.id)}</td>
                                    <td class="p-3 align-middle" data-label="หมายเหตุ">
                                        ${item.remark ? `<span class="text-xs italic text-slate-500 truncate block max-w-[150px]" title="${escapeHtml(item.remark)}">📝 ${escapeHtml(item.remark)}</span>` : '<span class="text-slate-300 text-xs">-</span>'}
                                    </td>
                                </tr>`;
                });

                html += `
                            </tbody>
                        </table>
                    </div>
                </div>`;
            });

            document.getElementById('itemsList').innerHTML = html;
        };

        // --- Save Multiplier ---
        window.saveMultiplier = async (projectId) => {
            const input = document.getElementById(`mult-${projectId}`);
            const btn = document.getElementById(`multBtn-${projectId}`);
            if (!input || !btn) return;

            const val = input.value.trim();
            const multiplier = val ? parseFloat(val) : 0;

            if (multiplier < 0) {
                Swal.mixin({ toast: true, position: 'top', showConfirmButton: false, timer: 2000 })
                    .fire({ icon: 'warning', title: 'ตัวคูณต้องเป็นจำนวนบวก' });
                return;
            }

            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';

            try {
                const projRef = doc(getProjectsRef(), projectId);
                await updateDoc(projRef, { multiplier: multiplier });

                // Update local state
                if (multiplier > 0) {
                    projectMultipliers[projectId] = multiplier;
                } else {
                    delete projectMultipliers[projectId];
                }

                // Re-render to reflect changes
                applyFilters();

                Swal.mixin({ toast: true, position: 'bottom-end', showConfirmButton: false, timer: 2000, timerProgressBar: true })
                    .fire({ icon: 'success', title: `บันทึกตัวคูณ${multiplier > 0 ? ' ×' + multiplier : ' (ยกเลิก)'} เรียบร้อย` });
            } catch (error) {
                console.error('Save multiplier error:', error);
                Swal.mixin({ toast: true, position: 'bottom-end', showConfirmButton: false, timer: 3000 })
                    .fire({ icon: 'error', title: 'บันทึกตัวคูณไม่สำเร็จ: ' + error.message });
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<i class="fa-solid fa-check"></i> บันทึก';
            }
        };

    </script>

</body>

</html>
