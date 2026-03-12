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
    <title>แบบไฟฟ้า — Mentra BOM</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
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
            background: rgba(255, 255, 255, 0.90);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border-radius: 1.25rem;
            box-shadow: 0 4px 24px -4px rgba(0, 0, 0, 0.07), 0 1px 4px rgba(0, 0, 0, 0.04);
        }

        ::-webkit-scrollbar {
            width: 5px;
            height: 5px;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 6px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        .swal2-popup {
            font-family: 'Prompt', sans-serif;
            border-radius: 1.25rem !important;
        }

        /* File link rows */
        .elec-row {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 14px 16px;
            border-radius: 16px;
            border: 1.5px solid #e2e8f0;
            background: white;
            transition: all 0.22s ease;
            position: relative;
            overflow: hidden;
        }

        .elec-row::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            background: linear-gradient(180deg, #f59e0b, #f97316);
            border-radius: 16px 0 0 16px;
        }

        .elec-row.is-confirmed::before {
            background: linear-gradient(180deg, #22c55e, #10b981);
        }

        .elec-row.is-confirmed {
            border-color: #bbf7d0;
        }

        .elec-row.is-confirmed:hover {
            border-color: #86efac;
            box-shadow: 0 6px 20px -4px rgba(34, 197, 94, 0.14);
        }

        .elec-row:hover {
            border-color: #fcd34d;
            box-shadow: 0 6px 20px -4px rgba(245, 158, 11, 0.16);
            transform: translateY(-2px);
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-in {
            animation: fadeInUp 0.3s ease-out;
        }

        /* Custom select */
        .custom-select-wrapper {
            position: relative;
            display: inline-block;
            width: 100%;
        }

        .custom-select-wrapper::after {
            content: '\f078';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            pointer-events: none;
            color: #f59e0b;
            font-size: 12px;
        }

        /* Stat pill */
        .stat-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 700;
            transition: all 0.2s ease;
        }

        /* type badge */
        .type-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 3px 10px;
            border-radius: 8px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        /* Card grid for mobile */
        @media (max-width: 767px) {
            .elec-row {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }

            .elec-row .action-btns {
                width: 100%;
                justify-content: flex-end;
            }
        }
    </style>
</head>

<body class="text-slate-700">

    <!-- Loading -->
    <div id="mainLoading" class="fixed inset-0 bg-white/90 backdrop-blur-sm z-50 flex justify-center items-center">
        <div class="flex flex-col items-center">
            <i class="fa-solid fa-bolt text-4xl text-amber-500 animate-bounce mb-4"></i>
            <p class="text-slate-500 font-bold animate-pulse">กำลังโหลดแบบไฟฟ้า...</p>
        </div>
    </div>

    <?php include 'sidebar.php'; ?>

    <div class="w-full px-4 md:px-6 xl:px-8 py-6">

        <!-- ═══ HEADER + PROJECT DROPDOWN ═══ -->
        <div class="glass-panel p-5 mb-6 fade-in border-l-4 border-amber-500">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-xl font-bold text-slate-800 flex items-center gap-2">
                        <i class="fa-solid fa-bolt text-amber-500"></i>
                        แบบไฟฟ้า & ลิงก์ Google Drive
                    </h1>
                    <p class="text-slate-400 text-xs mt-0.5">แนบลิงก์ไฟล์แบบไฟฟ้าของแต่ละโครงการ — Google Drive, PDF,
                        CAD ฯลฯ</p>
                </div>
                <!-- Add button (admin/material only) -->
                <button id="addLinkBtn" onclick="openAddModal()"
                    class="hidden w-full sm:w-auto bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white px-5 py-2.5 rounded-xl font-bold shadow-lg shadow-amber-200 flex items-center justify-center gap-2 transition-all active:scale-95">
                    <i class="fa-solid fa-plus"></i> เพิ่มลิงก์ไฟล์
                </button>
            </div>

            <!-- Project Dropdown -->
            <div class="mt-4">
                <label class="block text-xs font-bold text-slate-500 uppercase mb-1.5 tracking-wider">
                    <i class="fa-solid fa-folder-tree text-amber-500 mr-1"></i> เลือกโครงการ
                </label>
                <div class="flex items-center gap-2">
                    <div class="custom-select-wrapper flex-grow" style="max-width:420px">
                        <select id="projectDropdown"
                            onchange="handleProjectChange(this.value, this.options[this.selectedIndex].text)"
                            class="w-full appearance-none bg-amber-50 border-2 border-amber-200 text-slate-800 font-semibold text-sm rounded-xl px-4 py-3 cursor-pointer focus:ring-2 focus:ring-amber-500 focus:border-amber-400 outline-none transition-all">
                            <option value="" disabled selected>— กำลังโหลดโครงการ —</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Stats bar (shown after project selected) -->
            <div id="statsBar" class="hidden mt-4 flex flex-wrap gap-2">
                <div class="stat-pill bg-amber-50 text-amber-700 border border-amber-200">
                    <i class="fa-solid fa-file-lines"></i>
                    <span id="totalFilesCount">0</span> ไฟล์ทั้งหมด
                </div>
                <div class="stat-pill bg-green-50 text-green-700 border border-green-200">
                    <i class="fa-solid fa-check-circle"></i>
                    <span id="confirmedCount">0</span> คอนเฟิร์มแล้ว
                </div>
                <div class="stat-pill bg-slate-50 text-slate-500 border border-slate-200">
                    <i class="fa-regular fa-circle"></i>
                    <span id="pendingCount">0</span> รอตรวจสอบ
                </div>
            </div>
        </div>

        <!-- ═══ FILE LINKS AREA ═══ -->
        <div id="contentArea" class="hidden fade-in">
            <!-- Info bar -->
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="font-bold text-slate-700 flex items-center gap-2">
                        <i class="fa-solid fa-bolt text-amber-500"></i>
                        <span id="projNameDisplay">--</span>
                    </h2>
                    <p class="text-xs text-slate-400 mt-0.5">รายการแบบไฟฟ้าที่แนบไว้ <span id="linkCountDisplay"
                            class="font-bold text-amber-600"></span></p>
                </div>
                <!-- Search -->
                <div class="relative hidden sm:block" style="max-width: 280px;">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <i class="fa-solid fa-magnifying-glass text-slate-400 text-xs"></i>
                    </div>
                    <input type="text" id="searchInput" oninput="window.filterFiles()"
                        class="bg-white border border-slate-200 text-slate-800 text-xs rounded-xl pl-9 pr-3 py-2.5 w-full focus:ring-2 focus:ring-amber-300 focus:border-amber-400 outline-none transition-all shadow-sm"
                        placeholder="ค้นหาชื่อไฟล์...">
                </div>
            </div>

            <!-- List of file links -->
            <div id="fileLinksList" class="space-y-3">
                <!-- Filled by JS -->
            </div>

            <!-- Empty state -->
            <div id="emptyState" class="hidden glass-panel py-20 text-center mt-4">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-amber-50 mb-4">
                    <i class="fa-solid fa-bolt text-3xl text-amber-300"></i>
                </div>
                <h3 class="font-bold text-slate-600 mb-1">ยังไม่มีลิงก์แบบไฟฟ้า</h3>
                <p class="text-slate-400 text-sm" id="emptyMsg">ยังไม่มีการแนบไฟล์ในโครงการนี้</p>
            </div>
        </div>

        <!-- ═══ INITIAL PROMPT (before project selected) ═══ -->
        <div id="noProjectState" class="glass-panel py-20 text-center fade-in">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-amber-50 mb-5">
                <i class="fa-solid fa-bolt text-4xl text-amber-300"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-700 mb-2">เลือกโครงการก่อน</h3>
            <p class="text-slate-400 text-sm">เลือกโครงการจาก Dropdown ด้านบน เพื่อดูและจัดการแบบไฟฟ้า</p>
        </div>
    </div>

    <!-- ═══ ADD/EDIT FILE MODAL ═══ -->
    <div id="addModal"
        class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 hidden flex justify-center items-center px-4">
        <div class="bg-white w-full max-w-lg rounded-2xl shadow-2xl">
            <div
                class="p-5 border-b border-slate-100 flex justify-between items-center bg-gradient-to-r from-amber-50 to-orange-50 rounded-t-2xl">
                <h3 class="font-bold text-lg text-slate-800" id="modalTitle">
                    <i class="fa-solid fa-bolt text-amber-500 mr-2"></i>เพิ่มลิงก์แบบไฟฟ้า
                </h3>
                <button onclick="closeAddModal()"
                    class="w-8 h-8 rounded-full bg-white hover:bg-red-100 hover:text-red-500 flex items-center justify-center transition-colors text-slate-500 shadow-sm">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1.5">* ชื่อเอกสาร / แบบ</label>
                    <input type="text" id="docName"
                        class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-amber-400 outline-none text-sm transition-all"
                        placeholder="เช่น แบบไฟฟ้าชั้น 1, Single Line Diagram">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1.5">* URL ลิงก์ไฟล์</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"><i
                                class="fa-solid fa-link text-xs"></i></span>
                        <input type="url" id="docUrl"
                            class="w-full pl-9 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-amber-400 outline-none text-sm transition-all"
                            placeholder="https://drive.google.com/file/...">
                    </div>
                    <p class="text-[10px] text-slate-400 mt-1">รองรับ Google Drive, Dropbox, OneDrive, หรือ URL
                        ตรงสู่ไฟล์ใดก็ได้</p>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1.5">ประเภทแบบ</label>
                    <select id="docType"
                        class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-amber-400 outline-none text-sm cursor-pointer">
                        <option value="electrical">⚡ แบบไฟฟ้า</option>
                        <option value="single_line">📊 Single Line Diagram</option>
                        <option value="layout">📐 Layout</option>
                        <option value="riser">🏢 Riser Diagram</option>
                        <option value="panel">🔌 Panel Schedule</option>
                        <option value="lighting">💡 Lighting</option>
                        <option value="other">📄 อื่นๆ</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1.5">คำอธิบาย (ไม่บังคับ)</label>
                    <textarea id="docDesc" rows="2"
                        class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-amber-400 outline-none text-sm resize-none"
                        placeholder="บอกรายละเอียดเพิ่มเติม เช่น Rev.2, อัปเดตล่าสุด"></textarea>
                </div>
                <button type="button" onclick="submitLink()" id="submitBtn"
                    class="w-full bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white font-bold py-3 rounded-xl shadow-md transition-all flex items-center justify-center gap-2 active:scale-95">
                    <i class="fa-solid fa-check"></i> บันทึกลิงก์
                </button>
            </div>
        </div>
    </div>

    <script type="module">
        import { initializeApp } from "https://www.gstatic.com/firebasejs/11.6.1/firebase-app.js";
        import {
            getFirestore, collection, addDoc, onSnapshot, deleteDoc, doc,
            query, orderBy, serverTimestamp, where, updateDoc
        } from "https://www.gstatic.com/firebasejs/11.6.1/firebase-firestore.js";
        import { getAuth, signInAnonymously, signInWithCustomToken, onAuthStateChanged, signOut } from "https://www.gstatic.com/firebasejs/11.6.1/firebase-auth.js";

        // ── Firebase config ──
        const firebaseConfig = {
            apiKey: "AIzaSyBj8bKeS9Whnh8uOXbAxY_znNgIyzcE-Sg",
            authDomain: "bom-mentra.firebaseapp.com",
            projectId: "bom-mentra",
            storageBucket: "bom-mentra.firebasestorage.app",
            messagingSenderId: "916019460525",
            appId: "1:916019460525:web:11328f705e57d00d53c924",
            measurementId: "G-S7RC954PEK"
        };

        let isCanvasEnv = false, fbAppId = 'default-bom-app';
        try { if (typeof __firebase_config !== 'undefined') isCanvasEnv = true; if (typeof __app_id !== 'undefined') fbAppId = __app_id; } catch (e) { }

        const fbApp = initializeApp(firebaseConfig);
        const db = getFirestore(fbApp);
        const auth = getAuth(fbApp);

        const col = (name) => isCanvasEnv
            ? collection(db, 'artifacts', fbAppId, 'public', 'data', name)
            : collection(db, name);

        const projectsRef = () => col('bom_projects');
        const elecRef = () => col('bom_electrical_links');

        // ── State ──
        const role = localStorage.getItem('mentra_role');
        const canEdit = (role === 'admin' || role === 'material');
        let selectedProjectId = null;
        let selectedProjectName = '';
        let currentUser = null;
        let elecUnsub = null;
        let allLinks = []; // for search filtering

        // ── Helpers ──
        const esc = (s) => s == null ? '' : String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#39;');
        const hideLoader = () => {
            const l = document.getElementById('mainLoading');
            if (l) { l.style.opacity = '0'; setTimeout(() => l.style.display = 'none', 300); }
        };
        const toast = (icon, title) =>
            Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 2500 }).fire({ icon, title });

        // ── Type helpers ──
        const getTypeInfo = (type) => {
            switch (type) {
                case 'single_line': return { label: 'Single Line', icon: 'fa-chart-line', color: 'text-violet-600', bg: 'bg-violet-50', border: 'border-violet-200' };
                case 'layout': return { label: 'Layout', icon: 'fa-vector-square', color: 'text-blue-600', bg: 'bg-blue-50', border: 'border-blue-200' };
                case 'riser': return { label: 'Riser', icon: 'fa-building', color: 'text-cyan-600', bg: 'bg-cyan-50', border: 'border-cyan-200' };
                case 'panel': return { label: 'Panel', icon: 'fa-table-cells', color: 'text-emerald-600', bg: 'bg-emerald-50', border: 'border-emerald-200' };
                case 'lighting': return { label: 'Lighting', icon: 'fa-lightbulb', color: 'text-yellow-600', bg: 'bg-yellow-50', border: 'border-yellow-200' };
                case 'other': return { label: 'อื่นๆ', icon: 'fa-file', color: 'text-slate-500', bg: 'bg-slate-50', border: 'border-slate-200' };
                default: return { label: 'แบบไฟฟ้า', icon: 'fa-bolt', color: 'text-amber-600', bg: 'bg-amber-50', border: 'border-amber-200' };
            }
        };

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

        // ── Load projects ──
        const loadProjects = () => {
            const q = query(projectsRef(), orderBy('createdAt', 'desc'));
            onSnapshot(q, (snap) => {
                const sel = document.getElementById('projectDropdown');
                let html = '<option value="" disabled>— เลือกโครงการ —</option>';
                snap.forEach(d => {
                    const sel_attr = d.id === selectedProjectId ? 'selected' : '';
                    html += `<option value="${esc(d.id)}" ${sel_attr}>📂 ${esc(d.data().name)}</option>`;
                });
                sel.innerHTML = html;
                if (selectedProjectId) sel.value = selectedProjectId;
            });
        };

        // ── Handle project selection ──
        window.handleProjectChange = (projectId, rawText) => {
            if (!projectId) return;
            const projectName = rawText.replace(/^📂\s*/, '').trim();
            selectedProjectId = projectId;
            selectedProjectName = projectName;

            document.getElementById('noProjectState').classList.add('hidden');
            document.getElementById('contentArea').classList.remove('hidden');
            document.getElementById('projNameDisplay').textContent = projectName;
            document.getElementById('statsBar').classList.remove('hidden');

            if (canEdit) {
                document.getElementById('addLinkBtn').classList.remove('hidden');
            } else {
                document.getElementById('addLinkBtn').classList.add('hidden');
            }

            loadElecLinks(projectId);
        };

        // ── Load electrical links ──
        const loadElecLinks = (projectId) => {
            if (elecUnsub) { elecUnsub(); elecUnsub = null; }

            document.getElementById('fileLinksList').innerHTML =
                '<div class="text-center py-12 text-slate-400"><i class="fa-solid fa-circle-notch fa-spin text-2xl text-amber-400 block mb-3"></i>กำลังโหลด...</div>';
            document.getElementById('emptyState').classList.add('hidden');

            const q = query(elecRef(), where('projectId', '==', projectId));
            elecUnsub = onSnapshot(q, (snap) => {
                const empty = document.getElementById('emptyState');
                const emptyMsg = document.getElementById('emptyMsg');
                const countEl = document.getElementById('linkCountDisplay');

                if (snap.empty) {
                    allLinks = [];
                    document.getElementById('fileLinksList').innerHTML = '';
                    countEl.textContent = '';
                    emptyMsg.textContent = canEdit ? 'กดปุ่ม "+ เพิ่มลิงก์ไฟล์" เพื่อแนบเอกสาร' : 'ยังไม่มีการแนบไฟล์ในโครงการนี้';
                    empty.classList.remove('hidden');
                    updateStats(0, 0, 0);
                    return;
                }

                empty.classList.add('hidden');
                countEl.textContent = snap.size + ' ไฟล์';

                let docs = [];
                snap.forEach(docSnap => {
                    docs.push({ id: docSnap.id, data: docSnap.data() });
                });
                docs.sort((a, b) => {
                    const timeA = a.data.createdAt?.seconds || 0;
                    const timeB = b.data.createdAt?.seconds || 0;
                    return timeB - timeA;
                });

                allLinks = docs;
                const confirmed = docs.filter(d => d.data.confirmed === true).length;
                updateStats(docs.length, confirmed, docs.length - confirmed);

                renderLinks(docs);

            }, (err) => {
                console.error('Electrical listener error:', err);
                document.getElementById('fileLinksList').innerHTML =
                    `<div class="text-center py-10 text-red-400"><i class="fa-solid fa-triangle-exclamation text-2xl mb-2 block"></i>โหลดไม่สำเร็จ: ${esc(err.message)}</div>`;
            });
        };

        const updateStats = (total, confirmed, pending) => {
            document.getElementById('totalFilesCount').textContent = total;
            document.getElementById('confirmedCount').textContent = confirmed;
            document.getElementById('pendingCount').textContent = pending;
        };

        // ── Render links ──
        const renderLinks = (docs) => {
            const list = document.getElementById('fileLinksList');
            let html = '';

            docs.forEach(docObj => {
                const d = docObj.data;
                const id = docObj.id;
                const dateStr = d.createdAt ? new Date(d.createdAt.toDate()).toLocaleDateString('th-TH') : '...';
                const typeInfo = getTypeInfo(d.type || 'electrical');
                const isConfirmed = d.confirmed === true;
                const confirmIcon = isConfirmed ? 'fa-solid fa-check-circle' : 'fa-regular fa-circle';
                const confirmText = isConfirmed ? 'คอนเฟิร์มแล้ว' : 'ยังไม่คอนเฟิร์ม';
                const confirmBtnClass = isConfirmed ? 'bg-green-500 text-white shadow-sm shadow-green-200' : 'bg-slate-100 text-slate-400 hover:bg-slate-200';

                const actionBtns = canEdit
                    ? `<div class="flex ml-auto gap-1.5 items-center action-btns flex-shrink-0 flex-wrap">
                            <button onclick="window.toggleConfirm('${id}', ${isConfirmed})"
                                class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-[10px] font-bold transition-all ${confirmBtnClass}" title="เปลี่ยนสถานะคอนเฟิร์ม">
                                <i class="${confirmIcon}"></i> ${confirmText}
                            </button>
                            <button onclick="window.openEditModal('${id}', '${esc(d.name).replace(/'/g, "\\'")}', '${esc(d.url).replace(/'/g, "\\'")}', '${esc(d.desc || '').replace(/'/g, "\\'")}', '${d.type || 'electrical'}')"
                                class="flex-shrink-0 w-9 h-9 rounded-xl bg-orange-50 text-orange-400 hover:bg-orange-100 hover:text-orange-600 flex items-center justify-center transition-colors" title="แก้ไขลิงก์">
                                <i class="fa-solid fa-pen text-sm"></i>
                            </button>
                            <button onclick="window.deleteFile('${id}', '${esc(d.name).replace(/'/g, "\\'")}')"
                                class="flex-shrink-0 w-9 h-9 rounded-xl bg-red-50 text-red-400 hover:bg-red-100 hover:text-red-600 flex items-center justify-center transition-colors" title="ลบลิงก์">
                                <i class="fa-solid fa-trash-can text-sm"></i>
                            </button>
                       </div>`
                    : `<div class="flex ml-auto items-center action-btns flex-shrink-0">
                            <span class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-[10px] font-bold ${isConfirmed ? 'bg-green-100 text-green-600' : 'bg-slate-100 text-slate-400'}">
                                <i class="${confirmIcon}"></i> ${confirmText}
                            </span>
                       </div>`;

                html += `
                <div class="elec-row fade-in ${isConfirmed ? 'is-confirmed' : ''}">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br ${isConfirmed ? 'from-green-100 to-emerald-100' : 'from-amber-100 to-orange-100'} flex items-center justify-center flex-shrink-0 shadow-sm">
                        <i class="fa-solid ${typeInfo.icon} text-xl ${isConfirmed ? 'text-green-500' : 'text-amber-500'}"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="font-bold text-slate-800 truncate">${esc(d.name)}</span>
                            <span class="type-badge ${typeInfo.bg} ${typeInfo.color} ${typeInfo.border} border">
                                <i class="fa-solid ${typeInfo.icon} text-[9px]"></i> ${typeInfo.label}
                            </span>
                        </div>
                        ${d.desc ? `<div class="text-xs text-slate-400 mt-0.5 line-clamp-1">${esc(d.desc)}</div>` : ''}
                        <div class="flex items-center gap-3 mt-2 flex-wrap">
                            <button onclick="window.openFilePopup('${esc(d.url).replace(/'/g, "\\'")}', '${esc(d.name).replace(/'/g, "\\'")}')"
                               class="inline-flex items-center gap-1.5 ${isConfirmed ? 'bg-green-500 hover:bg-green-600 shadow-green-200' : 'bg-amber-500 hover:bg-amber-600 shadow-amber-200'} text-white text-xs font-bold px-4 py-1.5 rounded-xl transition-all shadow-sm cursor-pointer active:scale-95">
                                <i class="fa-solid fa-magnifying-glass text-xs"></i> เปิดดูไฟล์
                            </button>
                            <a href="${esc(d.url)}" target="_blank" rel="noopener noreferrer"
                               class="inline-flex items-center gap-1 text-[10px] text-slate-400 hover:text-amber-600 transition-colors">
                                <i class="fa-solid fa-arrow-up-right-from-square"></i> เปิดต้นฉบับ
                            </a>
                            <span class="text-[10px] text-slate-400 flex items-center gap-1">
                                <i class="fa-regular fa-clock"></i> ${dateStr}
                            </span>
                        </div>
                    </div>
                    ${actionBtns}
                </div>`;
            });

            list.innerHTML = html;
        };

        // ── Search/filter ──
        window.filterFiles = () => {
            const term = (document.getElementById('searchInput').value || '').trim().toLowerCase();
            if (!term) {
                renderLinks(allLinks);
                return;
            }
            const filtered = allLinks.filter(d => {
                const nameMatch = (d.data.name || '').toLowerCase().includes(term);
                const descMatch = (d.data.desc || '').toLowerCase().includes(term);
                return nameMatch || descMatch;
            });
            renderLinks(filtered);
        };

        // ── Popup File Viewer ──
        window.openFilePopup = (url, name) => {
            let embedUrl = url;
            // Google Drive: /view → /preview
            if (url.includes('drive.google.com/file/d/')) {
                embedUrl = url.replace(/\/view.*$/, '/preview');
            }
            // Google Drive folder
            if (url.includes('drive.google.com/drive/folders/')) {
                embedUrl = url; // folders don't embed well, show link
            }

            Swal.fire({
                title: `<span class="text-base font-bold text-slate-700 block text-left truncate"><i class="fa-solid fa-bolt text-amber-500 mr-2"></i>${name}</span>`,
                html: `<div class="relative w-full overflow-hidden bg-slate-100 rounded-xl border border-slate-200" style="height: 75vh;">
                           <iframe src="${embedUrl}" class="absolute inset-0 w-full h-full border-0" sandbox="allow-scripts allow-same-origin allow-popups allow-forms" loading="lazy"></iframe>
                           <div class="absolute inset-0 flex items-center justify-center pointer-events-none" id="iframeLoading">
                               <div class="flex flex-col items-center">
                                   <i class="fa-solid fa-spinner fa-spin text-3xl text-amber-400 mb-3"></i>
                                   <p class="text-sm text-slate-400 font-medium">กำลังโหลดไฟล์...</p>
                               </div>
                           </div>
                       </div>
                       <div class="mt-3 flex items-center justify-between">
                           <a href="${url}" target="_blank" class="text-xs text-blue-500 hover:underline"><i class="fa-solid fa-arrow-up-right-from-square mr-1"></i>เปิดในหน้าต่างใหม่ (กรณีดูไม่ได้)</a>
                       </div>`,
                showCloseButton: true,
                showConfirmButton: false,
                width: '95%',
                padding: '1.25rem',
                customClass: { popup: 'rounded-2xl max-w-5xl' },
                didOpen: () => {
                    // Hide loading indicator when iframe loads
                    const iframe = document.querySelector('.swal2-html-container iframe');
                    if (iframe) {
                        iframe.addEventListener('load', () => {
                            const loader = document.getElementById('iframeLoading');
                            if (loader) loader.style.display = 'none';
                        });
                    }
                }
            });
        };

        // ── Add/Edit Modal ──
        let editLinkId = null;

        window.openAddModal = () => {
            if (!selectedProjectId) return;
            editLinkId = null;
            document.getElementById('modalTitle').innerHTML = '<i class="fa-solid fa-bolt text-amber-500 mr-2"></i>เพิ่มลิงก์แบบไฟฟ้า';
            document.getElementById('addModal').classList.remove('hidden');
        };

        window.openEditModal = (id, name, url, desc, type) => {
            editLinkId = id;
            document.getElementById('docName').value = name;
            document.getElementById('docUrl').value = url;
            document.getElementById('docDesc').value = desc || '';
            document.getElementById('docType').value = type || 'electrical';
            document.getElementById('modalTitle').innerHTML = '<i class="fa-solid fa-pen text-orange-500 mr-2"></i>แก้ไขลิงก์แบบไฟฟ้า';
            document.getElementById('addModal').classList.remove('hidden');
        };

        window.closeAddModal = () => {
            document.getElementById('addModal').classList.add('hidden');
            ['docName', 'docUrl', 'docDesc'].forEach(id => {
                const el = document.getElementById(id);
                if (el) el.value = '';
            });
            document.getElementById('docType').value = 'electrical';
            editLinkId = null;
        };

        window.submitLink = async () => {
            const name = document.getElementById('docName').value.trim();
            const url = document.getElementById('docUrl').value.trim();
            if (!name) { Swal.fire('กรุณาใส่ชื่อเอกสาร', '', 'warning'); return; }
            if (!url || !/^https?:\/\//i.test(url)) { Swal.fire('URL ไม่ถูกต้อง', 'กรุณาใส่ URL ที่เริ่มต้นด้วย https://', 'warning'); return; }

            const btn = document.getElementById('submitBtn');
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin mr-2"></i>กำลังบันทึก...';

            try {
                const docData = {
                    name: name,
                    url: url,
                    desc: document.getElementById('docDesc').value.trim(),
                    type: document.getElementById('docType').value
                };

                if (editLinkId) {
                    await updateDoc(doc(elecRef(), editLinkId), docData);
                    toast('success', 'แก้ไขสำเร็จ');
                } else {
                    docData.projectId = selectedProjectId;
                    docData.createdAt = serverTimestamp();
                    docData.addedBy = currentUser?.uid || null;
                    docData.confirmed = false;
                    await addDoc(elecRef(), docData);
                    toast('success', 'เพิ่มลิงก์สำเร็จ');
                }
                closeAddModal();
            } catch (e) {
                Swal.fire('Error', e.message, 'error');
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<i class="fa-solid fa-check mr-2"></i>บันทึกลิงก์';
            }
        };

        // ── Delete ──
        window.deleteFile = async (id, name) => {
            const res = await Swal.fire({
                title: `ลบ "${name}"?`,
                text: 'ลิงก์นี้จะถูกลบออกจากระบบ',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonText: 'ยกเลิก',
                confirmButtonText: 'ยืนยันลบ'
            });
            if (res.isConfirmed) {
                await deleteDoc(doc(elecRef(), id));
                toast('success', 'ลบลิงก์แล้ว');
            }
        };

        // ── Toggle Confirm Status ──
        window.toggleConfirm = async (id, currentStatus) => {
            if (!canEdit) return;
            try {
                await updateDoc(doc(elecRef(), id), {
                    confirmed: !currentStatus
                });
                toast('success', !currentStatus ? 'คอนเฟิร์มแบบแล้ว' : 'ยกเลิกการคอนเฟิร์ม');
            } catch (e) {
                console.error("Error updating confirm status:", e);
                Swal.fire('Error', 'ไม่สามารถอัปเดตสถานะได้: ' + e.message, 'error');
            }
        };

        // ── Init ──
        const initAuth = async () => {
            try {
                if (typeof __initial_auth_token !== 'undefined' && __initial_auth_token)
                    await signInWithCustomToken(auth, __initial_auth_token);
                else
                    await signInAnonymously(auth);
            } catch (error) {
                console.error("Auth Failed:", error);
                hideLoader();
                loadProjects();
            }
        };

        onAuthStateChanged(auth, (user) => {
            if (user) {
                currentUser = user;
                hideLoader();
                loadProjects();

                // Auto-select if ?project= in URL
                const p = new URLSearchParams(window.location.search);
                const pid = p.get('project');
                const pname = p.get('name') || '';
                if (pid) {
                    selectedProjectId = pid;
                    selectedProjectName = pname;
                    document.getElementById('noProjectState').classList.add('hidden');
                    document.getElementById('contentArea').classList.remove('hidden');
                    document.getElementById('projNameDisplay').textContent = pname || 'โครงการที่เลือก';
                    document.getElementById('statsBar').classList.remove('hidden');
                    if (canEdit) document.getElementById('addLinkBtn').classList.remove('hidden');
                    loadElecLinks(pid);
                }
            } else {
                initAuth();
            }
        });

        // Timeout fallback
        setTimeout(() => {
            const loader = document.getElementById('mainLoading');
            if (loader && loader.style.display !== 'none') {
                loader.style.opacity = '0';
                setTimeout(() => loader.style.display = 'none', 400);
            }
        }, 3000);

        initAuth();
    </script>
    </main>
    </div>
    </div>
</body>

</html>
