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
    <title>ประวัติการทำรายการ — Mentra BOM</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
    </style>
</head>

<body class="text-slate-700">

    <!-- Sidebar and Header -->
    <?php include 'sidebar.php'; ?>

    <div class="max-w-5xl mx-auto px-3 md:px-6 py-6 fade-in-up w-full">
        <div class="glass-panel p-4 md:p-6 border border-gray-100/50 min-h-[500px]">
            <div class="flex items-center justify-between border-b border-slate-100 mb-4 pb-4">
                <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                    <i class="fa-solid fa-list text-indigo-500"></i> ประวัติการทำรายการล่าสุด
                </h3>
                <span class="text-xs bg-indigo-100 text-indigo-600 px-3 py-1 rounded-full font-bold"
                    id="logCount"></span>
            </div>

            <div class="overflow-x-auto rounded-xl border border-slate-200/80 bg-white shadow-sm">
                <table class="w-full text-left border-collapse min-w-[700px]">
                    <thead>
                        <tr
                            class="bg-gradient-to-r from-slate-50 to-slate-100/50 text-xs text-slate-500 uppercase tracking-wider sticky top-0 font-semibold border-b border-slate-200">
                            <th class="p-3 md:p-4 whitespace-nowrap">วันเวลา</th>
                            <th class="p-3 md:p-4 whitespace-nowrap">ผู้ทำรายการ</th>
                            <th class="p-3 md:p-4 whitespace-nowrap">การกระทำ</th>
                            <th class="p-3 md:p-4 min-w-[200px]">รายละเอียด</th>
                            <th class="p-3 md:p-4 min-w-[150px]">ชื่อสินค้า</th>
                            <th class="p-3 md:p-4 min-w-[150px]">โครงการ</th>
                        </tr>
                    </thead>
                    <tbody id="logsList" class="text-sm divide-y divide-slate-100">
                        <tr>
                            <td colspan="6" class="text-center py-12 text-slate-400">
                                <i class="fa-solid fa-spinner fa-spin text-indigo-500 text-xl"></i> กำลังโหลดประวัติ...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="text-center text-[10px] text-slate-400 py-6 mt-4">
                <span class="flex items-center gap-2 justify-center opacity-70">
                    <i class="fa-solid fa-shield-halved text-green-500"></i> Secured by Firebase
                    <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                    Mentra BOM v3.0 — Action Logs
                </span>
            </div>
        </div>
    </div>

    <script type="module">
        import { initializeApp } from "https://www.gstatic.com/firebasejs/11.6.1/firebase-app.js";
        import { getFirestore, collection, query, orderBy, onSnapshot, limit } from "https://www.gstatic.com/firebasejs/11.6.1/firebase-firestore.js";
        import { getAuth, signInAnonymously } from "https://www.gstatic.com/firebasejs/11.6.1/firebase-auth.js";

        const firebaseConfig = {
            apiKey: "AIzaSyBj8bKeS9Whnh8uOXbAxY_znNgIyzcE-Sg",
            authDomain: "bom-mentra.firebaseapp.com",
            projectId: "bom-mentra",
            storageBucket: "bom-mentra.firebasestorage.app",
            messagingSenderId: "916019460525",
            appId: "1:916019460525:web:11328f705e57d00d53c924",
            measurementId: "G-S7RC954PEK"
        };

        const app = initializeApp(firebaseConfig);
        const db = getFirestore(app);
        const auth = getAuth(app);

        // Use the same path logic as bom.php
        let isCanvasEnv = false;
        let appId = 'default-bom-app';
        try {
            if (typeof __firebase_config !== 'undefined') { isCanvasEnv = true; }
            if (typeof __app_id !== 'undefined') { appId = __app_id; }
        } catch (e) { }

        const logsRef = isCanvasEnv
            ? collection(db, 'artifacts', appId, 'public', 'data', 'bom_logs')
            : collection(db, 'bom_logs');

        const escapeHtml = (str) => {
            if (str == null) return '';
            return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#39;');
        };

        const getActionBadge = (action) => {
            if (action === 'เพิ่มรายการ') return '<span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-green-100 text-green-700 font-bold text-[10px]"><i class="fa-solid fa-plus mr-1"></i> เพิ่ม</span>';
            if (action === 'อัปเดตรายการ') return '<span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-blue-100 text-blue-700 font-bold text-[10px]"><i class="fa-solid fa-pen mr-1"></i> แก้ไข</span>';
            if (action === 'ลบรายการ') return '<span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-red-100 text-red-700 font-bold text-[10px]"><i class="fa-solid fa-trash mr-1"></i> ลบ</span>';
            if (action === 'อัปเดตสถานะ') return '<span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-purple-100 text-purple-700 font-bold text-[10px]"><i class="fa-solid fa-rotate mr-1"></i> สถานะ</span>';
            return `<span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 font-bold text-[10px]">${escapeHtml(action)}</span>`;
        };

        const loadLogs = () => {
            const q = query(logsRef, orderBy('timestamp', 'desc'), limit(200));
            onSnapshot(q, (snap) => {
                let html = '';
                if (snap.empty) {
                    html = '<tr><td colspan="6" class="text-center py-16 text-slate-400"><i class="fa-solid fa-inbox text-3xl block mb-3 opacity-30"></i><p>ยังไม่มีประวัติการทำรายการ</p></td></tr>';
                } else {
                    snap.forEach(docSnap => {
                        const data = docSnap.data();
                        const timeStr = data.timestamp ? new Date(data.timestamp.toDate()).toLocaleString('th-TH') : 'กำลังบันทึก...';
                        html += `
                        <tr class="hover:bg-indigo-50/30 transition-colors">
                            <td class="p-3 md:p-4 text-xs text-slate-500 align-middle whitespace-nowrap">${timeStr}</td>
                            <td class="p-3 md:p-4 align-middle">
                                <div class="font-bold text-slate-700">${escapeHtml(data.actorName || '-')}</div>
                                <div class="text-[9px] text-slate-400 uppercase tracking-widest mt-0.5">${escapeHtml(data.actorRole || 'Unknown')}</div>
                            </td>
                            <td class="p-3 md:p-4 align-middle">${getActionBadge(data.action || '-')}</td>
                            <td class="p-3 md:p-4 text-xs text-slate-600 align-middle">${escapeHtml(data.details || '-')}</td>
                            <td class="p-3 md:p-4 text-slate-700 font-medium align-middle"><span class="truncate block max-w-[200px]">${escapeHtml(data.itemName || '-')}</span></td>
                            <td class="p-3 md:p-4 text-xs text-indigo-500 font-semibold align-middle"><i class="fa-regular fa-folder-open mr-1"></i>${escapeHtml(data.projectName || '-')}</td>
                        </tr>`;
                    });
                    const countEl = document.getElementById('logCount');
                    if (countEl) countEl.textContent = snap.size + ' รายการ';
                }
                document.getElementById('logsList').innerHTML = html;
            }, (err) => {
                console.error('Logs snapshot error:', err);
                document.getElementById('logsList').innerHTML = `<tr><td colspan="6" class="text-center py-12 text-red-400"><i class="fa-solid fa-triangle-exclamation text-2xl mb-2 block"></i>โหลดไม่สำเร็จ: ${escapeHtml(err.message)}</td></tr>`;
            });
        };

        // Authenticate first, then load data
        signInAnonymously(auth).then(() => loadLogs()).catch(err => {
            console.error('Auth failed:', err);
            loadLogs(); // load anyway in case already authed
        });
    </script>
    </main>
    </div>
    </div>
</body>

</html>