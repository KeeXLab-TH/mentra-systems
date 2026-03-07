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

    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Export Libraries -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/exceljs@4.4.0/dist/exceljs.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/file-saver@2.0.5/dist/FileSaver.min.js"></script>

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
            <div>
                <p class="text-[10px] text-slate-400 uppercase font-bold tracking-wider mb-1"><i
                        class="fa-regular fa-folder-open mr-1 text-violet-500"></i>โครงการ</p>
                <h2 class="text-lg md:text-xl font-bold text-slate-800" id="projectNameDisplay">กำลังโหลด...</h2>
            </div>
            <div class="flex items-center gap-2 flex-wrap">
                <button onclick="selectAll()"
                    class="bg-blue-50 hover:bg-blue-100 text-blue-700 px-3 py-2 rounded-xl text-xs font-bold transition-all border border-blue-200 btn-lift">
                    <i class="fa-solid fa-check-double mr-1"></i> <span class="hidden sm:inline">เลือก</span>ทั้งหมด
                </button>
                <button onclick="deselectAll()"
                    class="bg-slate-50 hover:bg-slate-100 text-slate-600 px-3 py-2 rounded-xl text-xs font-bold transition-all border border-slate-200 btn-lift">
                    <i class="fa-solid fa-xmark mr-1"></i> ยกเลิก
                </button>
                <div class="h-5 w-px bg-slate-200 mx-0.5 hidden sm:block"></div>
                <button onclick="window.calcExportPDF()"
                    class="bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded-xl text-xs font-bold transition-all shadow-sm flex items-center gap-1.5 btn-lift">
                    <i class="fa-solid fa-file-pdf"></i> <span class="hidden sm:inline">Export</span> PDF
                </button>
                <button onclick="window.calcExportExcel()"
                    class="bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-2 rounded-xl text-xs font-bold transition-all shadow-sm flex items-center gap-1.5 btn-lift">
                    <i class="fa-solid fa-file-excel"></i> <span class="hidden sm:inline">Export</span> Excel
                </button>
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
            <div class="flex items-center justify-between mb-4 border-b pb-3 border-slate-100">
                <h3 class="text-base md:text-lg font-bold text-slate-800 flex items-center gap-2">
                    <i class="fa-solid fa-list-check text-violet-500"></i> เลือกรายการวัสดุ
                </h3>
                <span class="text-xs text-slate-400" id="selectedCount">เลือกแล้ว 0 รายการ</span>
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
        import { getFirestore, collection, query, where, getDocs } from "https://www.gstatic.com/firebasejs/11.6.1/firebase-firestore.js";
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
        let selectedIds = new Set();

        const escapeHtml = (str) => {
            if (str == null) return '';
            return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#39;');
        };

        const loadItems = async () => {
            try {
                const q = query(getItemsRef(), where("projectId", "==", projectId));
                const snap = await getDocs(q);
                items = [];
                snap.forEach(d => items.push({ id: d.id, ...d.data() }));
                items.sort((a, b) => (a.name || '').localeCompare(b.name || ''));
                renderItemsList();
            } catch (e) {
                console.error('Load error:', e);
                document.getElementById('itemsList').innerHTML = `<div class="text-center py-12 text-red-400"><p class="text-sm">โหลดข้อมูลไม่สำเร็จ: ${escapeHtml(e.message)}</p></div>`;
            }
        };

        const renderItemsList = () => {
            if (!items.length) {
                document.getElementById('itemsList').innerHTML = '<div class="text-center py-12 text-slate-300"><i class="fa-regular fa-folder-open text-3xl"></i><p class="mt-2 text-sm">ไม่มีรายการในโครงการนี้</p></div>';
                return;
            }

            let html = '';
            items.forEach(item => {
                const isSelected = selectedIds.has(item.id);
                const statusText = item.status === 'ordered' ? '🚚 สั่งแล้ว' : item.status === 'received' ? '✅ ได้รับ' : item.status === 'cancelled' ? '❌ ยกเลิก' : '⏳ รอ';
                html += `
                <div class="item-row flex items-center gap-3 p-3 md:p-4 rounded-xl border border-slate-200/80 cursor-pointer ${isSelected ? 'selected-item' : ''}" data-id="${escapeHtml(item.id)}" onclick="window.toggleItem('${escapeHtml(item.id)}')">
                    <div class="custom-check ${isSelected ? 'checked' : ''}" id="check-${escapeHtml(item.id)}"></div>
                    ${item.image ? `<img src="${item.image}" class="w-10 h-10 rounded-lg object-cover border border-slate-100 flex-shrink-0">` : '<span class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-slate-100 text-slate-300 flex-shrink-0"><i class="fa-regular fa-image"></i></span>'}
                    <div class="flex-grow min-w-0">
                        <p class="font-bold text-sm text-slate-700 truncate">${escapeHtml(item.name)}</p>
                        <p class="text-[10px] text-slate-400 truncate">${escapeHtml(item.details) || '-'}</p>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <p class="font-extrabold text-sm text-blue-600">฿${(item.price || 0).toLocaleString(undefined, { minimumFractionDigits: 2 })}</p>
                        <p class="text-[10px] text-slate-400">x${item.qty || 1} = <span class="font-bold text-slate-600">฿${(item.total || 0).toLocaleString(undefined, { minimumFractionDigits: 2 })}</span></p>
                    </div>
                    <div class="text-[10px] text-slate-400 w-16 text-center flex-shrink-0 hidden sm:block">${statusText}</div>
                </div>`;
            });
            document.getElementById('itemsList').innerHTML = html;
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

        window.recalculate = () => {
            const selectedItems = items.filter(i => selectedIds.has(i.id));
            const sum = selectedItems.reduce((acc, i) => acc + (i.total || 0), 0);
            const qty = Math.max(1, parseInt(document.getElementById('setQty').value || '1'));
            const grandTotal = sum * qty;

            document.getElementById('sumDisplay').textContent = '฿' + sum.toLocaleString(undefined, { minimumFractionDigits: 2 });
            document.getElementById('sumItemsNote').textContent = `${selectedItems.length} รายการ`;
            document.getElementById('selectedCount').textContent = `เลือกแล้ว ${selectedItems.length} รายการ`;
            document.getElementById('grandTotalCalc').textContent = '฿' + grandTotal.toLocaleString(undefined, { minimumFractionDigits: 2 });
            document.getElementById('grandTotalNote').textContent = `${selectedItems.length} รายการ × ${qty} ชุด`;

            document.getElementById('formulaSum').textContent = '฿' + sum.toLocaleString(undefined, { minimumFractionDigits: 2 });
            document.getElementById('formulaQty').textContent = qty + ' ชุด';
            document.getElementById('formulaTotal').textContent = '฿' + grandTotal.toLocaleString(undefined, { minimumFractionDigits: 2 });
        };

        // ============================
        // Export PDF — Calculator
        // ============================
        window.calcExportPDF = () => {
            const selectedItems = items.filter(i => selectedIds.has(i.id));
            if (!selectedItems.length) return Swal.fire('แจ้งเตือน', 'กรุณาเลือกรายการอย่างน้อย 1 รายการ', 'warning');
            Swal.fire({ title: 'กำลังสร้าง PDF...', didOpen: () => Swal.showLoading() });

            const qty = Math.max(1, parseInt(document.getElementById('setQty').value || '1'));
            const sum = selectedItems.reduce((acc, i) => acc + (i.total || 0), 0);
            const grandTotal = sum * qty;
            const fmtNum = (n) => n.toLocaleString(undefined, { minimumFractionDigits: 2 });
            const dateStr = new Date().toLocaleDateString('th-TH', { year: 'numeric', month: 'long', day: 'numeric' });

            const ITEMS_PER_PAGE = 15;
            const totalPages = Math.ceil(selectedItems.length / ITEMS_PER_PAGE);

            const makeHeader = (pageNum) => `
                <div style="display:flex; justify-content:space-between; margin-bottom:14px; border-bottom:3px solid #7c3aed; padding-bottom:12px;">
                    <div style="display:flex; align-items:center; gap:12px;">
                        <img src="Mentra_Solution_Tranparency.png" style="height:40px;">
                        <div><h1 style="font-size:17px; font-weight:bold; margin:0;">Mentra Solution Co., Ltd.</h1><p style="font-size:11px; color:#666; margin:2px 0 0;">ใบคำนวณราคาวัสดุ (Price Calculator)</p></div>
                    </div>
                    <div style="text-align:right;">
                        <h2 style="font-size:15px; color:#7c3aed; margin:0;">${escapeHtml(projectName)}</h2>
                        <p style="font-size:9px; color:#94a3b8; margin:2px 0 0;">วันที่: ${dateStr} | หน้า ${pageNum}/${totalPages}</p>
                    </div>
                </div>`;

            const tableHead = `<thead><tr style="background:#f1f5f9;">
                <th style="border:1px solid #ddd; padding:7px; width:30px;">#</th>
                <th style="border:1px solid #ddd; padding:7px;">รายการ / รายละเอียด</th>
                <th style="border:1px solid #ddd; padding:7px; width:85px;">ราคา/หน่วย</th>
                <th style="border:1px solid #ddd; padding:7px; width:50px;">จำนวน</th>
                <th style="border:1px solid #ddd; padding:7px; width:95px;">รวม</th>
            </tr></thead>`;

            let pagesHtml = '';
            for (let p = 0; p < totalPages; p++) {
                const startIdx = p * ITEMS_PER_PAGE;
                const pageItems = selectedItems.slice(startIdx, startIdx + ITEMS_PER_PAGE);
                const isLastPage = (p === totalPages - 1);

                let rows = pageItems.map((i, x) => {
                    const linkHtml = i.link ? `<br><a href="${escapeHtml(i.link)}" style="color:#3b82f6;font-size:9px;word-break:break-all;">🔗 ${escapeHtml(i.link).substring(0, 50)}${i.link.length > 50 ? '...' : ''}</a>` : '';
                    return `<tr>
                        <td style="border:1px solid #ddd;padding:5px;text-align:center;font-size:10px;">${startIdx + x + 1}</td>
                        <td style="border:1px solid #ddd;padding:5px;font-size:10px;">
                            <b>${escapeHtml(i.name)}</b>${linkHtml}
                            ${i.details ? '<br><span style="color:#64748b;font-size:9px;">' + escapeHtml(i.details) + '</span>' : ''}
                        </td>
                        <td style="border:1px solid #ddd;padding:5px;text-align:right;font-size:10px;">฿${fmtNum(i.price || 0)}</td>
                        <td style="border:1px solid #ddd;padding:5px;text-align:center;font-size:10px;">${i.qty || 1}</td>
                        <td style="border:1px solid #ddd;padding:5px;text-align:right;font-weight:bold;color:#2563eb;font-size:10px;">฿${fmtNum(i.total || 0)}</td>
                    </tr>`;
                }).join('');

                // Summary info bar only on first page
                const infoBar = p === 0 ? `<div style="background:#f5f3ff; border:1px solid #ddd5f9; border-radius:8px; padding:10px 14px; margin-bottom:14px; display:flex; justify-content:space-between; align-items:center;">
                    <div>
                        <span style="font-size:10px; color:#7c3aed; font-weight:bold;">🧮 จำนวนชุด: ${qty} ชุด</span>
                        <span style="margin-left:16px; font-size:10px; color:#64748b;">📦 ${selectedItems.length} รายการที่เลือก</span>
                    </div>
                    <div style="font-size:10px; color:#64748b;">ราคาต่อ 1 ชุด: <b style="color:#2563eb;">฿${fmtNum(sum)}</b></div>
                </div>` : '';

                // Footer totals only on last page
                let footer = '';
                if (isLastPage) {
                    footer = `<tfoot>
                        <tr style="background:#eff6ff;">
                            <td colspan="4" style="border:1px solid #bfdbfe; padding:7px; text-align:right; font-weight:bold;">รวมต่อ 1 ชุด</td>
                            <td style="border:1px solid #bfdbfe; padding:7px; text-align:right; color:#2563eb; font-weight:bold;">฿${fmtNum(sum)}</td>
                        </tr>
                        <tr style="background:#f5f3ff;">
                            <td colspan="4" style="border:1px solid #ddd5f9; padding:7px; text-align:right; font-weight:bold;">× ${qty} ชุด</td>
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
            if (user && projectId) loadItems();
        });

        setTimeout(() => {
            const loader = document.getElementById('calcLoading');
            if (loader && loader.style.display !== 'none') {
                loader.style.opacity = '0';
                setTimeout(() => loader.style.display = 'none', 500);
            }
        }, 3500);

        initAuth();
    </script>
    </main>
    </div>
    </div>
</body>

</html>