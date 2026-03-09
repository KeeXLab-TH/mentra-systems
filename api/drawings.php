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
    <title>แบบแปลนโครงสร้าง — Mentra BOM</title>

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
            background: rgba(255, 255, 255, 0.88);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border-radius: 1.25rem;
            box-shadow: 0 4px 24px -4px rgba(0, 0, 0, 0.06), 0 1px 4px rgba(0, 0, 0, 0.04);
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
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.2);
        }

        /* Drawing Card Hover Effects */
        .drawing-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .drawing-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px -8px rgba(0, 0, 0, 0.15);
            border-color: #93c5fd;
        }

        .img-zoom-container {
            overflow: hidden;
            border-radius: 0.75rem 0.75rem 0 0;
        }

        .img-zoom {
            transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .drawing-card:hover .img-zoom {
            transform: scale(1.08);
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

<body class="text-slate-700 bg-slate-50">

    <!-- Loading Overlay -->
    <div id="mainLoading"
        class="fixed inset-0 bg-white/80 backdrop-blur-sm z-50 flex justify-center items-center transition-opacity duration-300">
        <div class="flex flex-col items-center">
            <i class="fa-solid fa-compass-drafting text-4xl text-blue-500 animate-bounce mb-4"></i>
            <p class="text-slate-500 font-bold animate-pulse">กำลังสแกนพิมพ์เขียว...</p>
        </div>
    </div>

    <!-- Sidebar and Header -->
    <?php include 'sidebar.php'; ?>

    <div class="max-w-7xl mx-auto px-4 py-8 fade-in-up">
        <!-- Header Section -->
        <div class="glass-panel p-6 mb-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-800 flex items-center gap-3">
                    <i class="fa-solid fa-compass-drafting text-blue-600"></i> ศูนย์รวมแบบแปลน
                </h1>
                <p class="text-slate-500 text-sm mt-1" id="projectScopeText">ดูแบบแปลนทั้งหมดในระบบ
                    หรือเลือกดูตามโครงการ
                </p>
                <div class="mt-2" id="currentProjectBadge" style="display: none;">
                    <span
                        class="px-3 py-1 bg-blue-100 text-blue-700 font-bold text-xs rounded-full border border-blue-200">
                        <i class="fa-regular fa-folder-open mr-1"></i> โครงการ: <span id="currentProjectName"></span>
                    </span>
                    <button onclick="clearProjectFilter()"
                        class="ml-2 text-[10px] text-slate-400 hover:text-red-500 underline">ดูแบบทั้งหมด</button>
                </div>
            </div>

            <!-- Contextual Actions -->
            <div class="flex items-center gap-3 w-full md:w-auto">
                <button id="uploadBtn" onclick="openUploadModal()"
                    class="hidden w-full md:w-auto bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl font-bold shadow-sm flex items-center justify-center gap-2 btn-lift">
                    <i class="fa-solid fa-cloud-arrow-up"></i> อัพโหลดแบบแปลน
                </button>
            </div>
        </div>

        <!-- Drawings Grid -->
        <div id="drawingsGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <!-- Loading State -->
            <div class="col-span-full py-20 text-center text-slate-400" id="drawingsLoading">
                <i class="fa-solid fa-circle-notch fa-spin text-3xl mb-3 text-blue-500"></i>
                <p>กำลังโหลดข้อมูลแบบแปลน...</p>
            </div>
        </div>

        <!-- Empty State -->
        <div id="emptyState" class="hidden glass-panel py-24 text-center">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-slate-100 mb-4">
                <i class="fa-regular fa-image text-4xl text-slate-300"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-700 mb-2">ยังไม่มีแบบแปลน</h3>
            <p class="text-slate-500 text-sm max-w-sm mx-auto">ยังไม่มีการอัพโหลดแบบรูปภาพในขณะนี้
                <br>สงวนสิทธิ์การอัพโหลดเฉพาะ Admin และฝ่ายวัสดุ (Material)</p>
        </div>
    </div>

    <!-- Upload Modal -->
    <div id="uploadModal"
        class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 hidden flex justify-center items-center opacity-0 transition-opacity duration-300">
        <div
            class="bg-white w-full max-w-md rounded-2xl shadow-2xl transform scale-95 transition-transform duration-300 m-4">
            <div class="p-5 border-b border-slate-100 flex justify-between items-center">
                <h3 class="font-bold text-lg text-slate-800"><i
                        class="fa-solid fa-cloud-arrow-up text-blue-500 mr-2"></i>
                    อัพโหลดแบบแปลน</h3>
                <button onclick="closeUploadModal()"
                    class="text-slate-400 hover:text-red-500 w-8 h-8 rounded-full hover:bg-red-50 flex items-center justify-center transition-colors">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <div class="p-6">
                <form id="uploadForm" class="space-y-4">
                    <!-- Project Selection (Required) -->
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1.5">*
                            เลือกโครงการเชื่อมโยง</label>
                        <select id="modalProjectSelect" required
                            class="w-full bg-slate-50 border border-slate-200 text-slate-700 text-sm rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-4 py-2.5 outline-none transition-all cursor-pointer">
                            <option value="" disabled selected>-- โหลดรายชื่อโครงการ... --</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1.5">* ชื่อแบบ /
                            ส่วนงาน</label>
                        <input type="text" id="drawingName" required
                            class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none text-sm transition-all"
                            placeholder="เช่น แปลนพื้นชั้น 1">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1.5">* เลือกไฟล์รูปภาพ</label>
                        <div class="relative group">
                            <input type="file" id="drawingFile" accept="image/*" required
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl outline-none text-sm file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition-all cursor-pointer file:cursor-pointer">
                        </div>
                        <p class="text-[10px] text-slate-400 mt-1 italic">รองรับไฟล์: JPG, PNG
                            (ขนาดภาพจะถูกย่ออัตโนมัติ)</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1.5">คำอธิบายเพิ่มเติม
                            (ถ้ามี)</label>
                        <textarea id="drawingDetails" rows="2"
                            class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none text-sm transition-all resize-none"
                            placeholder="รายละเอียดแบบ..."></textarea>
                    </div>

                    <div class="pt-4 border-t border-slate-100">
                        <button type="submit" id="submitUploadBtn"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-xl shadow-md transition-all flex items-center justify-center gap-2 btn-lift">
                            <i class="fa-solid fa-check"></i> บันทึกข้อมูล
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <script type="module">
        import { initializeApp } from "https://www.gstatic.com/firebasejs/11.6.1/firebase-app.js";
        import { getFirestore, collection, addDoc, onSnapshot, deleteDoc, updateDoc, doc, query, orderBy, serverTimestamp, getDoc, getDocs, where } from "https://www.gstatic.com/firebasejs/11.6.1/firebase-firestore.js";
        import { getAuth, signInAnonymously, signInWithCustomToken, onAuthStateChanged } from "https://www.gstatic.com/firebasejs/11.6.1/firebase-auth.js";

        // Config
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

        const getDrawingsRef = () => isCanvasEnv ? collection(db, 'artifacts', appId, 'public', 'data', 'bom_drawings') : collection(db, 'bom_drawings');
        const getProjectsRef = () => isCanvasEnv ? collection(db, 'artifacts', appId, 'public', 'data', 'bom_projects') : collection(db, 'bom_projects');

        const role = localStorage.getItem('mentra_role');
        const isAdminOrMaterial = (role === 'admin' || role === 'material');

        let currentUser = null;
        let unsubscribeDrawings = null;
        let allProjectsCache = {}; // Cache ชื่อโครงการ
        let targetProjectId = null; // ดึงมาจาก URL

        // --- Init Function ---
        const init = async () => {
            // ดึง Project ID จาก URL
            const urlParams = new URLSearchParams(window.location.search);
            targetProjectId = urlParams.get('project');

            if (isAdminOrMaterial) {
                document.getElementById('uploadBtn').classList.remove('hidden');
                loadProjectsForModal();
            }

            // ถ้ามี URL Project, ไปดึงชื่อมาแสดง
            if (targetProjectId) {
                try {
                    const docRef = doc(getProjectsRef(), targetProjectId);
                    const docSnap = await getDoc(docRef);
                    if (docSnap.exists()) {
                        const pname = docSnap.data().name;
                        allProjectsCache[targetProjectId] = pname;
                        document.getElementById('currentProjectName').innerText = pname;
                        document.getElementById('currentProjectBadge').style.display = 'inline-block';
                        document.getElementById('projectScopeText').innerText = 'แสดงแบบแปลนเฉพาะโครงการที่เลือก';
                    } else {
                        targetProjectId = null; // ถ้าระบุ ID มั่ว ให้ลียร์
                    }
                } catch (e) { console.error('Error fetching context project:', e); }
            } else {
                // ถ้าไม่มี Project Context, สร้าง Cache ของระบบไว้ก่อนสำหรับชื่อ
                const snap = await getDocs(getProjectsRef());
                snap.forEach(d => { allProjectsCache[d.id] = d.data().name; });
            }

            loadDrawings();

            // Handle Auth (เพื่อความปลอดภัยหาก Firestore มี rules)
            onAuthStateChanged(auth, (user) => {
                if (user) {
                    currentUser = user;
                    setTimeout(() => {
                        document.getElementById('mainLoading').style.opacity = '0';
                        setTimeout(() => document.getElementById('mainLoading').style.display = 'none', 300);
                    }, 500);
                } else {
                    signInAnonymously(auth).catch(e => console.error(e));
                }
            });
        };

        const escapeHtml = (str) => {
            if (str == null) return '';
            return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#39;');
        };

        window.clearProjectFilter = () => {
            // นำหนทางกลับไปดูทั้งหมด
            window.location.href = 'drawings.php';
        };

        // --- Load Projects Info ---
        const loadProjectsForModal = () => {
            const select = document.getElementById('modalProjectSelect');
            const q = query(getProjectsRef(), orderBy('createdAt', 'desc'));

            onSnapshot(q, (snap) => {
                let html = '<option value="" disabled selected>-- เลือกโครงการ --</option>';
                snap.forEach(doc => {
                    const name = doc.data().name;
                    allProjectsCache[doc.id] = name; // Update cache
                    html += `<option value="${doc.id}" ${doc.id === targetProjectId ? 'selected' : ''}>📂 ${escapeHtml(name)}</option>`;
                });
                select.innerHTML = html;
            });
        };

        // --- Load Drawings (with optional filtering) ---
        const loadDrawings = () => {
            if (unsubscribeDrawings) unsubscribeDrawings();

            let baseQuery = getDrawingsRef();
            let constraints = [orderBy('createdAt', 'desc')];
            // เลี่ยงปัญหา Index ด้วยการ query ปกติแล้ว filter ฝั่ง client ถ้าจำเป็น, 
            // หรือสร้าง composite index. เพื่อความปลอดภัยเราใช้ == ได้ถ้าสร้าง index (projectId)
            if (targetProjectId) {
                constraints.unshift(where("projectId", "==", targetProjectId));
            }

            const q = query(baseQuery, ...constraints);

            unsubscribeDrawings = onSnapshot(q, (snapshot) => {
                const grid = document.getElementById('drawingsGrid');
                const empty = document.getElementById('emptyState');
                const load = document.getElementById('drawingsLoading');

                if (load) load.style.display = 'none';

                if (snapshot.empty) {
                    grid.innerHTML = '';
                    empty.classList.remove('hidden');
                    return;
                }

                empty.classList.add('hidden');
                let html = '';

                snapshot.forEach(docSnap => {
                    const data = docSnap.data();
                    const id = docSnap.id;
                    const dateStr = data.createdAt ? new Date(data.createdAt.toDate()).toLocaleDateString('th-TH') : 'กำลังซิงค์...';
                    const projectName = allProjectsCache[data.projectId] || 'ไม่ระบุโครงการ';

                    let actionBtn = isAdminOrMaterial ?
                        `<button onclick="deleteDrawing('${id}', '${escapeHtml(data.name)}')" class="absolute top-3 right-3 w-8 h-8 bg-white/90 backdrop-blur rounded-full text-red-500 hover:bg-red-50 hover:text-red-600 shadow-sm flex items-center justify-center transition-colors opacity-0 group-hover:opacity-100 z-10" title="ลบรูปนี้"><i class="fa-solid fa-trash-can text-xs"></i></button>` : '';

                    html += `
                    <div class="drawing-card item-row-enter bg-white rounded-xl border border-slate-200/60 shadow-sm relative group overflow-hidden opacity-0" style="transform: translateY(15px);">
                        ${actionBtn}
                        <div class="img-zoom-container h-48 w-full bg-slate-100 relative cursor-pointer" onclick="viewImage('${escapeHtml(data.image)}', '${escapeHtml(data.name)}')">
                            <span class="absolute top-2 left-2 bg-slate-900/60 backdrop-blur-sm text-white text-[9px] font-bold px-2 py-1 rounded flex items-center gap-1 z-10"><i class="fa-solid fa-expand"></i> ขยาย</span>
                            <img src="${data.image}" class="img-zoom w-full h-full object-cover">
                        </div>
                        <div class="p-4 border-t border-slate-100/80">
                            <h3 class="font-bold text-slate-800 text-sm truncate uppercase tracking-wide mb-1">${escapeHtml(data.name)}</h3>
                            <div class="text-[10px] text-blue-600 mb-2 font-semibold truncate bg-blue-50 px-2 py-0.5 rounded inline-block max-w-full"><i class="fa-regular fa-folder-open mr-1"></i>${escapeHtml(projectName)}</div>
                            <p class="text-xs text-slate-500 line-clamp-2 min-h-[32px]">${escapeHtml(data.details || 'ไม่มีรายละเอียด')}</p>
                            <div class="mt-3 text-[10px] text-slate-400 font-bold tracking-widest uppercase border-t border-slate-50 pt-2"><i class="fa-regular fa-clock mr-1"></i> ${dateStr}</div>
                        </div>
                    </div>`;
                });

                grid.innerHTML = html;

                // Animation
                if (typeof gsap !== 'undefined') {
                    gsap.to('.item-row-enter', {
                        opacity: 1,
                        y: 0,
                        duration: 0.4,
                        stagger: 0.05,
                        ease: "cubic-bezier(0.22, 1, 0.36, 1)",
                        onComplete: function () {
                            document.querySelectorAll('.item-row-enter').forEach(el => {
                                el.classList.remove('item-row-enter', 'opacity-0');
                                el.style.transform = '';
                            });
                        }
                    });
                }
            });
        };

        // --- Modals ---
        window.openUploadModal = () => {
            const modal = document.getElementById('uploadModal');
            const inner = modal.querySelector('div');
            modal.classList.remove('hidden');
            // Force reflow
            void modal.offsetWidth;
            modal.classList.add('opacity-100');
            inner.classList.replace('scale-95', 'scale-100');
        };

        window.closeUploadModal = () => {
            const modal = document.getElementById('uploadModal');
            const inner = modal.querySelector('div');
            modal.classList.remove('opacity-100');
            inner.classList.replace('scale-100', 'scale-95');
            setTimeout(() => {
                modal.classList.add('hidden');
                document.getElementById('uploadForm').reset();
            }, 300);
        };

        // --- Image Resize/Compressor ---
        const resizeImage = (file, maxWidth) => {
            return new Promise((resolve) => {
                const reader = new FileReader();
                reader.readAsDataURL(file);
                reader.onload = (e) => {
                    const img = new Image();
                    img.src = e.target.result;
                    img.onload = () => {
                        const canvas = document.createElement('canvas');
                        let width = img.width;
                        let height = img.height;
                        if (width > maxWidth) {
                            height = Math.round((height * maxWidth) / width);
                            width = maxWidth;
                        } else {
                            // ไม่ต้องย่อ
                            resolve(e.target.result);
                            return;
                        }
                        canvas.width = width;
                        canvas.height = height;
                        const ctx = canvas.getContext('2d');
                        ctx.drawImage(img, 0, 0, width, height);
                        // บีบอัดเป็น WebP (หรือ jpeg) สำหรับ Drawing แนะนำให้ค่อนข้างชัด
                        resolve(canvas.toDataURL('image/jpeg', 0.85));
                    };
                };
            });
        };

        // --- Handle Upload ---
        document.getElementById('uploadForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            if (!isAdminOrMaterial) return;

            const projSelect = document.getElementById('modalProjectSelect');
            const pId = projSelect.value;
            if (!pId) { Swal.fire('Error', 'กรุณาเลือกโครงการ', 'error'); return; }

            const btn = document.getElementById('submitUploadBtn');
            btn.disabled = true;
            const originalContent = btn.innerHTML;
            btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> กำลังอัพโหลด...';

            try {
                const file = document.getElementById('drawingFile').files[0];
                const base64Img = await resizeImage(file, 1600); // แปลนให้อนุญาตความละเอียดสูงกว่าปกติ (1600px width)

                const data = {
                    projectId: pId,
                    name: document.getElementById('drawingName').value.trim(),
                    details: document.getElementById('drawingDetails').value.trim(),
                    image: base64Img,
                    createdAt: serverTimestamp(),
                    uploaderUid: currentUser?.uid
                };

                await addDoc(getDrawingsRef(), data);
                closeUploadModal();
                Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 }).fire({ icon: 'success', title: 'อัพโหลดแปลนสำเร็จ' });

            } catch (err) {
                console.error(err);
                Swal.fire('Error', 'ไม่สามารถบันทึกได้', 'error');
            } finally {
                btn.disabled = false;
                btn.innerHTML = originalContent;
            }
        });

        // --- Delete ---
        window.deleteDrawing = async (id, name) => {
            if (!isAdminOrMaterial) return;
            const res = await Swal.fire({
                title: 'ลบภาพแปลนนี้?',
                text: `คุณต้องการลบ "${name}" หรือไม่? การกระทำนี้ไม่สามารถกู้คืนได้`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                confirmButtonText: 'ยืนยันลบ'
            });

            if (res.isConfirmed) {
                try {
                    await deleteDoc(doc(getDrawingsRef(), id));
                    Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 2000 }).fire({ icon: 'success', title: 'ลบเรียบร้อย' });
                } catch (e) {
                    Swal.fire('Error', e.message, 'error');
                }
            }
        };

        // --- Image Viewer (Lightbox) ---
        window.viewImage = (src, title) => {
            Swal.fire({
                title: title,
                imageUrl: src,
                imageAlt: title,
                imageWidth: 'auto',
                width: Math.min(window.innerWidth * 0.95, 1200),
                showConfirmButton: false,
                showCloseButton: true,
                padding: '1rem',
                customClass: {
                    image: 'rounded-xl max-h-[85vh] object-contain',
                    popup: 'rounded-2xl',
                    title: 'text-sm font-bold uppercase tracking-wider text-slate-500'
                }
            });
        };

        // Initialize App
        document.addEventListener('DOMContentLoaded', init);
    </script>
</body>

</html>