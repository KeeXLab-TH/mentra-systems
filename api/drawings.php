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
    <title>ไฟล์ & PDF — Mentra BOM</title>
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

        /* PDF Link rows */
        .pdf-row {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 14px 16px;
            border-radius: 14px;
            border: 1.5px solid #e2e8f0;
            background: white;
            transition: all 0.22s ease;
            position: relative;
            overflow: hidden;
        }

        .pdf-row::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            background: linear-gradient(180deg, #ef4444, #f97316);
            border-radius: 14px 0 0 14px;
        }

        .pdf-row.is-confirmed::before {
            background: linear-gradient(180deg, #22c55e, #10b981);
        }

        .pdf-row.is-confirmed {
            border-color: #bbf7d0;
        }

        .pdf-row.is-confirmed:hover {
            border-color: #86efac;
            box-shadow: 0 6px 20px -4px rgba(34, 197, 94, 0.14);
        }

        .pdf-row:hover {
            border-color: #fca5a5;
            box-shadow: 0 6px 20px -4px rgba(239, 68, 68, 0.14);
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
            color: #6366f1;
            font-size: 12px;
        }
    </style>
</head>

<body class="text-slate-700">

    <!-- Loading -->
    <div id="mainLoading" class="fixed inset-0 bg-white/90 backdrop-blur-sm z-50 flex justify-center items-center">
        <div class="flex flex-col items-center">
            <i class="fa-solid fa-file-pdf text-4xl text-red-500 animate-bounce mb-4"></i>
            <p class="text-slate-500 font-bold animate-pulse">กำลังโหลด...</p>
        </div>
    </div>

    <?php include 'sidebar.php'; ?>

    <div class="w-full px-4 md:px-6 xl:px-8 py-6">

        <!-- ═══ HEADER + PROJECT DROPDOWN ═══ -->
        <div class="glass-panel p-5 mb-6 fade-in">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-xl font-bold text-slate-800 flex items-center gap-2">
                        <i class="fa-solid fa-file-pdf text-red-500"></i>
                        ไฟล์ & ลิงก์ PDF
                    </h1>
                    <p class="text-slate-400 text-xs mt-0.5">แนบลิงก์ PDF ของแต่ละโครงการ — Google Drive, Dropbox,
                        OneDrive ฯลฯ</p>
                </div>
                <!-- Add PDF button (admin/material only) -->
                <button id="addLinkBtn" onclick="openAddModal()"
                    class="hidden w-full sm:w-auto bg-red-500 hover:bg-red-600 text-white px-5 py-2.5 rounded-xl font-bold shadow-sm flex items-center justify-center gap-2 transition-all">
                    <i class="fa-solid fa-plus"></i> เพิ่มลิงก์ PDF
                </button>
            </div>

            <!-- Project Dropdown -->
            <div class="mt-4">
                <label class="block text-xs font-bold text-slate-500 uppercase mb-1.5 tracking-wider">
                    <i class="fa-solid fa-folder-tree text-indigo-500 mr-1"></i> เลือกโครงการ
                </label>
                <div class="flex items-center gap-2">
                    <div class="custom-select-wrapper flex-grow" style="max-width:420px">
                        <select id="projectDropdown"
                            onchange="handleProjectChange(this.value, this.options[this.selectedIndex].text)"
                            class="w-full appearance-none bg-indigo-50 border-2 border-indigo-200 text-slate-800 font-semibold text-sm rounded-xl px-4 py-3 cursor-pointer focus:ring-2 focus:ring-indigo-500 focus:border-indigo-400 outline-none transition-all">
                            <option value="" disabled selected>— กำลังโหลดโครงการ —</option>
                        </select>
                    </div>
                    <!-- Edit Project Button -->
                    <button id="editProjBtn" onclick="editProjectName()"
                        class="hidden w-11 h-11 flex-shrink-0 bg-slate-100 hover:bg-orange-100 text-slate-400 hover:text-orange-500 rounded-xl flex items-center justify-center transition-colors border border-slate-200"
                        title="แก้ไขชื่อโครงการ">
                        <i class="fa-solid fa-pen"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- ═══ PDF LINKS AREA ═══ -->
        <div id="contentArea" class="hidden fade-in">
            <!-- Info bar -->
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="font-bold text-slate-700 flex items-center gap-2">
                        <i class="fa-regular fa-folder-open text-indigo-500"></i>
                        <span id="projNameDisplay">--</span>
                    </h2>
                    <p class="text-xs text-slate-400 mt-0.5">รายการ PDF ที่แนบไว้ <span id="linkCountDisplay"
                            class="font-bold text-red-500"></span></p>
                </div>
            </div>

            <!-- List of PDF links -->
            <div id="pdfLinksList" class="space-y-3">
                <!-- Filled by JS -->
            </div>

            <!-- Empty state (hidden by default) -->
            <div id="emptyState" class="hidden glass-panel py-20 text-center mt-4">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-red-50 mb-4">
                    <i class="fa-regular fa-file-pdf text-3xl text-red-300"></i>
                </div>
                <h3 class="font-bold text-slate-600 mb-1">ยังไม่มีลิงก์ PDF</h3>
                <p class="text-slate-400 text-sm" id="emptyMsg">ยังไม่มีการแนบ PDF ในโครงการนี้</p>
            </div>
        </div>

        <!-- ═══ INITIAL PROMPT (before project selected) ═══ -->
        <div id="noProjectState" class="glass-panel py-20 text-center fade-in">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-indigo-50 mb-5">
                <i class="fa-solid fa-folder-open text-4xl text-indigo-300"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-700 mb-2">เลือกโครงการก่อน</h3>
            <p class="text-slate-400 text-sm">เลือกโครงการจาก Dropdown ด้านบน เพื่อดูและจัดการไฟล์ PDF</p>
        </div>
    </div>

    <!-- ═══ ADD PDF MODAL ═══ -->
    <div id="addModal"
        class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 hidden flex justify-center items-center px-4">
        <div class="bg-white w-full max-w-lg rounded-2xl shadow-2xl">
            <div class="p-5 border-b border-slate-100 flex justify-between items-center">
                <h3 class="font-bold text-lg text-slate-800">
                    <i class="fa-solid fa-link text-red-500 mr-2"></i>เพิ่มลิงก์ PDF
                </h3>
                <button onclick="closeAddModal()"
                    class="w-8 h-8 rounded-full bg-slate-100 hover:bg-red-100 hover:text-red-500 flex items-center justify-center transition-colors text-slate-500">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1.5">* ชื่อเอกสาร / แบบ</label>
                    <input type="text" id="docName"
                        class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-red-500 outline-none text-sm transition-all"
                        placeholder="เช่น แปลนพื้นชั้น 1, แบบโครงสร้างหลังคา">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1.5">* URL ลิงก์ PDF</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"><i
                                class="fa-solid fa-link text-xs"></i></span>
                        <input type="url" id="docUrl"
                            class="w-full pl-9 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-red-500 outline-none text-sm transition-all"
                            placeholder="https://drive.google.com/file/...">
                    </div>
                    <p class="text-[10px] text-slate-400 mt-1">รองรับ Google Drive, Dropbox, OneDrive, หรือ URL
                        ตรงสู่ไฟล์ใดก็ได้</p>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1.5">คำอธิบาย (ไม่บังคับ)</label>
                    <textarea id="docDesc" rows="2"
                        class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-red-500 outline-none text-sm resize-none"
                        placeholder="บอกรายละเอียดเพิ่มเติม เช่น ฉบับแก้ไขครั้งที่ 2"></textarea>
                </div>
                <button type="button" onclick="submitLink()" id="submitBtn"
                    class="w-full bg-red-500 hover:bg-red-600 text-white font-bold py-3 rounded-xl shadow-md transition-all flex items-center justify-center gap-2">
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
        import { getAuth, signInAnonymously } from "https://www.gstatic.com/firebasejs/11.6.1/firebase-auth.js";

        // ── Firebase config ──────────────────────────────────────
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

        // ── Collection helpers (same path logic as bom.php) ──────
        const col = (name) => isCanvasEnv
            ? collection(db, 'artifacts', fbAppId, 'public', 'data', name)
            : collection(db, name);

        const projectsRef = () => col('bom_projects');
        const pdfRef = () => col('bom_pdf_links');

        // ── State ─────────────────────────────────────────────────
        const role = localStorage.getItem('mentra_role');
        const canEdit = (role === 'admin' || role === 'material');
        let selectedProjectId = null;
        let selectedProjectName = '';
        let currentUser = null;
        let pdfUnsub = null; // Track listener so we can clean up

        // ── Helpers ───────────────────────────────────────────────
        const esc = (s) => s == null ? '' : String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#39;');
        const hideLoader = () => {
            const l = document.getElementById('mainLoading');
            if (l) { l.style.opacity = '0'; setTimeout(() => l.style.display = 'none', 300); }
        };
        const toast = (icon, title) =>
            Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 2500 }).fire({ icon, title });

        // ── Load projects into dropdown ───────────────────────────
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
                // If we already have a project selected, keep it
                if (selectedProjectId) sel.value = selectedProjectId;
            });
        };

        // ── Handle project selection ──────────────────────────────
        window.handleProjectChange = (projectId, rawText) => {
            if (!projectId) return;
            const projectName = rawText.replace(/^📂\s*/, '').trim();
            selectedProjectId = projectId;
            selectedProjectName = projectName;

            document.getElementById('noProjectState').classList.add('hidden');
            document.getElementById('contentArea').classList.remove('hidden');
            document.getElementById('projNameDisplay').textContent = projectName;

            if (canEdit) {
                document.getElementById('addLinkBtn').classList.remove('hidden');
                document.getElementById('editProjBtn').classList.remove('hidden');
            } else {
                document.getElementById('addLinkBtn').classList.add('hidden');
                document.getElementById('editProjBtn').classList.add('hidden');
            }

            loadPdfLinks(projectId);
        };

        // ── Load PDF links for a project ─────────────────────────
        const loadPdfLinks = (projectId) => {
            // Cancel previous listener to avoid ghost data
            if (pdfUnsub) {
                pdfUnsub();
                pdfUnsub = null;
            }

            // Show loading spinners
            document.getElementById('pdfLinksList').innerHTML =
                '<div class="text-center py-12 text-slate-400"><i class="fa-solid fa-circle-notch fa-spin text-2xl text-red-400 block mb-3"></i>กำลังโหลด...</div>';
            document.getElementById('emptyState').classList.add('hidden');

            const q = query(pdfRef(), where('projectId', '==', projectId));
            pdfUnsub = onSnapshot(q, (snap) => {
                const list = document.getElementById('pdfLinksList');
                const empty = document.getElementById('emptyState');
                const emptyMsg = document.getElementById('emptyMsg');
                const countEl = document.getElementById('linkCountDisplay');

                if (snap.empty) {
                    list.innerHTML = '';
                    countEl.textContent = '';
                    emptyMsg.textContent = canEdit ? 'กดปุ่ม "+ เพิ่มลิงก์ PDF" เพื่อแนบเอกสาร' : 'ยังไม่มีการแนบ PDF ในโครงการนี้';
                    empty.classList.remove('hidden');
                    return;
                }

                empty.classList.add('hidden');
                countEl.textContent = snap.size + ' ไฟล์';

                // หาวิธีเรียงลำดับฝั่ง Client เพื่อเลี่ยง Composite Index Error
                let docs = [];
                snap.forEach(docSnap => {
                    docs.push({ id: docSnap.id, data: docSnap.data() });
                });
                docs.sort((a, b) => {
                    const timeA = a.data.createdAt?.seconds || 0;
                    const timeB = b.data.createdAt?.seconds || 0;
                    return timeB - timeA; // Descending
                });

                let html = '';
                docs.forEach(docObj => {
                    const d = docObj.data;
                    const id = docObj.id;
                    const dateStr = d.createdAt ? new Date(d.createdAt.toDate()).toLocaleDateString('th-TH') : '...';

                    const isConfirmed = d.confirmed === true;
                    const confirmIcon = isConfirmed ? 'fa-solid fa-check-circle' : 'fa-regular fa-circle';
                    const confirmText = isConfirmed ? 'คอนเฟิร์มแล้ว' : 'ยังไม่คอนเฟิร์ม';
                    const confirmBtnClass = isConfirmed ? 'bg-green-500 text-white' : 'bg-slate-100 text-slate-400 hover:bg-slate-200';

                    const actionBtns = canEdit
                        ? `<div class="flex ml-2 gap-1 items-center">
                               <button onclick="window.toggleConfirm('${id}', ${isConfirmed})"
                                    class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-[10px] font-bold transition-all ${confirmBtnClass}" title="เปลี่ยนสถานะคอนเฟิร์ม">
                                    <i class="${confirmIcon}"></i> ${confirmText}
                                </button>
                               <button onclick="window.openEditModal('${id}', '${esc(d.name).replace(/'/g, "\\'")}', '${esc(d.url).replace(/'/g, "\\'")}', '${esc(d.desc || '').replace(/'/g, "\\'")}')"
                                    class="flex-shrink-0 w-9 h-9 rounded-xl bg-orange-50 text-orange-400 hover:bg-orange-100 hover:text-orange-600 flex items-center justify-center transition-colors" title="แก้ไขลิงก์">
                                    <i class="fa-solid fa-pen text-sm"></i>
                               </button>
                               <button onclick="window.deletePdf('${id}', '${esc(d.name).replace(/'/g, "\\'")}') "
                                    class="flex-shrink-0 w-9 h-9 rounded-xl bg-red-50 text-red-400 hover:bg-red-100 hover:text-red-600 flex items-center justify-center transition-colors" title="ลบลิงก์">
                                    <i class="fa-solid fa-trash-can text-sm"></i>
                               </button>
                           </div>`
                        : `<div class="flex ml-2 items-center">
                                <span class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-[10px] font-bold ${isConfirmed ? 'bg-green-100 text-green-600' : 'bg-slate-100 text-slate-400'}">
                                    <i class="${confirmIcon}"></i> ${confirmText}
                                </span>
                           </div>`;

                    html += `
                    <div class="pdf-row fade-in ${isConfirmed ? 'is-confirmed' : ''}">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br ${isConfirmed ? 'from-green-100 to-emerald-100' : 'from-red-100 to-orange-100'} flex items-center justify-center flex-shrink-0">
                            <i class="fa-regular fa-file-pdf text-2xl ${isConfirmed ? 'text-green-500' : 'text-red-500'}"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="font-bold text-slate-800 truncate">${esc(d.name)}</div>
                            ${d.desc ? `<div class="text-xs text-slate-400 mt-0.5 line-clamp-1">${esc(d.desc)}</div>` : ''}
                            <div class="flex items-center gap-3 mt-2 flex-wrap">
                                <button onclick="window.openPdfPopup('${esc(d.url).replace(/'/g, "\\'")}', '${esc(d.name).replace(/'/g, "\\'")}')"
                                   class="inline-flex items-center gap-1.5 ${isConfirmed ? 'bg-green-500 hover:bg-green-600' : 'bg-red-500 hover:bg-red-600'} text-white text-xs font-bold px-4 py-1.5 rounded-xl transition-all shadow-sm cursor-pointer">
                                    <i class="fa-solid fa-magnifying-glass text-xs"></i> เปิดดูไฟล์
                                </button>
                                <span class="text-[10px] text-slate-400 flex items-center gap-1">
                                    <i class="fa-regular fa-clock"></i> ${dateStr}
                                </span>
                            </div>
                        </div>
                        ${actionBtns}
                    </div>`;
                });
                list.innerHTML = html;

            }, (err) => {
                console.error('PDF listener error:', err);
                document.getElementById('pdfLinksList').innerHTML =
                    `<div class="text-center py-10 text-red-400"><i class="fa-solid fa-triangle-exclamation text-2xl mb-2 block"></i>โหลดไม่สำเร็จ: ${esc(err.message)}</div>`;
            });
        };

        // ── Edit Project Name ──
        window.editProjectName = async () => {
            if (!selectedProjectId) return;
            const dropdown = document.getElementById('projectDropdown');
            const currentName = dropdown.options[dropdown.selectedIndex].text;

            const { value: newName } = await Swal.fire({
                title: 'แก้ไขชื่อโครงการ',
                input: 'text',
                inputValue: currentName,
                inputPlaceholder: 'ชื่อโครงการใหม่...',
                showCancelButton: true,
                confirmButtonText: 'บันทึก',
                cancelButtonText: 'ยกเลิก',
                inputValidator: (value) => {
                    if (!value.trim()) return 'กรุณาระบุชื่อโครงการ!';
                }
            });

            if (newName && newName.trim() !== currentName) {
                try {
                    const btn = document.getElementById('editProjBtn');
                    const origIcon = btn.innerHTML;
                    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';
                    btn.disabled = true;

                    // Update project name in bom_projects collection
                    const projRef = doc(db, 'artifacts', appId, 'public', 'data', 'bom_projects', selectedProjectId);
                    await updateDoc(projRef, { name: newName.trim() });

                    // Update UI immediately for snappiness
                    dropdown.options[dropdown.selectedIndex].text = newName.trim();
                    if (document.getElementById('projNameDisplay')) {
                        document.getElementById('projNameDisplay').innerText = newName.trim();
                    }

                    toast('success', 'เปลี่ยนชื่อโครงการแล้ว');

                    btn.innerHTML = origIcon;
                    btn.disabled = false;
                } catch (err) {
                    console.error("Error updating project name:", err);
                    Swal.fire('ข้อผิดพลาด', 'ไม่สามารถแก้ไขชื่อโครงการได้ เลิกพิมพ์ชื่อที่ยาวเกินไปหรือลองใหม่อีกครั้ง', 'error');
                }
            }
        };

        // ── global State for Edit ──
        let editLinkId = null;

        // ── popup PDF viewer ──
        window.openPdfPopup = (url, name) => {
            // แปลงลิงก์ Google Drive จาก /view เป็น /preview เพื่อให้เปิดใน iframe ได้
            let embedUrl = url;
            if (url.includes('drive.google.com/file/d/')) {
                embedUrl = url.replace(/\/view.*$/, '/preview');
            }

            Swal.fire({
                title: `<span class="text-base font-bold text-slate-700 block text-left truncate"><i class="fa-regular fa-file-pdf text-red-500 mr-2"></i>${name}</span>`,
                html: `<div class="relative w-full overflow-hidden bg-slate-100 rounded-xl border border-slate-200" style="height: 75vh;">
                           <iframe src="${embedUrl}" class="absolute inset-0 w-full h-full border-0" sandbox="allow-scripts allow-same-origin allow-popups allow-forms"></iframe>
                       </div>
                       <div class="mt-3 text-left">
                           <a href="${url}" target="_blank" class="text-xs text-blue-500 hover:underline"><i class="fa-solid fa-arrow-up-right-from-square mr-1"></i>เปิดในหน้าต่างใหม่ (กรณีดูไม่ได้)</a>
                       </div>`,
                showCloseButton: true,
                showConfirmButton: false,
                width: '95%',
                padding: '1.25rem',
                customClass: { popup: 'rounded-2xl max-w-5xl' }
            });
        };

        // ── Add/Edit PDF Modal ─────────────────────────────────────────
        window.openAddModal = () => {
            if (!selectedProjectId) return;
            editLinkId = null;
            document.getElementById('addModal').classList.remove('hidden');
            document.querySelector('#addModal h3').innerHTML = '<i class="fa-solid fa-link text-red-500 mr-2"></i>เพิ่มลิงก์ PDF';
        };

        window.openEditModal = (id, name, url, desc) => {
            editLinkId = id;
            document.getElementById('docName').value = name;
            document.getElementById('docUrl').value = url;
            document.getElementById('docDesc').value = desc || '';
            document.getElementById('addModal').classList.remove('hidden');
            document.querySelector('#addModal h3').innerHTML = '<i class="fa-solid fa-pen text-orange-500 mr-2"></i>แก้ไขลิงก์ PDF';
        };

        window.closeAddModal = () => {
            document.getElementById('addModal').classList.add('hidden');
            ['docName', 'docUrl', 'docDesc'].forEach(id => {
                const el = document.getElementById(id);
                if (el) el.value = '';
            });
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
                if (editLinkId) {
                    await updateDoc(doc(pdfRef(), editLinkId), {
                        name: name,
                        url: url,
                        desc: document.getElementById('docDesc').value.trim()
                    });
                    toast('success', 'แก้ไขสำเร็จ');
                } else {
                    await addDoc(pdfRef(), {
                        projectId: selectedProjectId,
                        name: name,
                        url: url,
                        desc: document.getElementById('docDesc').value.trim(),
                        createdAt: serverTimestamp(),
                        addedBy: currentUser?.uid || null
                    });
                    toast('success', 'เพิ่มลิงก์ PDF สำเร็จ');
                }
                closeAddModal();
            } catch (e) {
                Swal.fire('Error', e.message, 'error');
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<i class="fa-solid fa-check mr-2"></i>บันทึกลิงก์';
            }
        };

        // ── Delete PDF Link ───────────────────────────────────────
        window.deletePdf = async (id, name) => {
            const res = await Swal.fire({
                title: `ลบ "${name}"?`,
                text: 'ลิงก์ PDF นี้จะถูกลบออกจากระบบ',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonText: 'ยกเลิก',
                confirmButtonText: 'ยืนยันลบ'
            });
            if (res.isConfirmed) {
                await deleteDoc(doc(pdfRef(), id));
                toast('success', 'ลบลิงก์แล้ว');
            }
        };

        // ── Toggle Confirm Status ──────────────────────────────────
        window.toggleConfirm = async (id, currentStatus) => {
            if (!canEdit) return;
            try {
                await updateDoc(doc(pdfRef(), id), {
                    confirmed: !currentStatus
                });
                toast('success', !currentStatus ? 'คอนเฟิร์มแบบแล้ว' : 'ยกเลิกการคอนเฟิร์ม');
            } catch (e) {
                console.error("Error updating confirm status:", e);
                Swal.fire('Error', 'ไม่สามารถอัปเดตสถานะได้: ' + e.message, 'error');
            }
        };

        // ── Init ──────────────────────────────────────────────────
        signInAnonymously(auth).then(cred => {
            currentUser = cred.user;
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
                if (canEdit) document.getElementById('addLinkBtn').classList.remove('hidden');
                loadPdfLinks(pid);
            }
        }).catch(err => {
            console.error(err);
            hideLoader();
            loadProjects();
        });
    </script>
    </main>
    </div>
    </div>
</body>

</html>