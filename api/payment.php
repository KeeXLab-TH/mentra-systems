<!DOCTYPE html>
<html lang="th">

<head>
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
    <title>หลักฐานการโอนเงิน — Mentra BOM</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" defer></script>

    <style>
        * { box-sizing: border-box; }

        body {
            font-family: 'Prompt', sans-serif;
            background: linear-gradient(135deg, #f0fdf4 0%, #f0f4ff 50%, #fff7ed 100%);
            min-height: 100vh;
        }

        .glass-panel {
            background: rgba(255,255,255,0.88);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border-radius: 1.25rem;
            box-shadow: 0 4px 24px -4px rgba(0,0,0,0.06), 0 1px 4px rgba(0,0,0,0.04);
            transition: box-shadow 0.3s ease;
        }
        .glass-panel:hover { box-shadow: 0 8px 32px -4px rgba(0,0,0,0.1); }

        #payLoading {
            position: fixed; inset: 0;
            background: linear-gradient(135deg, #f0fdf4, #fff7ed);
            z-index: 9999;
            display: flex; justify-content: center; align-items: center; flex-direction: column;
            transition: opacity 0.5s ease;
        }

        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 6px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        ::-webkit-scrollbar-track { background: transparent; }

        .btn-lift { transition: all 0.2s ease; }
        .btn-lift:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        .btn-lift:active { transform: translateY(0); }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .fade-in-up { animation: fadeInUp 0.4s ease-out; }

        /* Gallery card */
        .payment-card {
            background: white;
            border-radius: 20px;
            border: 1.5px solid #f1f5f9;
            overflow: hidden;
            transition: all 0.25s ease;
            box-shadow: 0 2px 10px rgba(0,0,0,0.04);
            animation: fadeInUp 0.35s ease-out;
        }
        .payment-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 30px rgba(0,0,0,0.1);
            border-color: #c7d2fe;
        }

        /* Stacked images */
        .img-stack { position: relative; height: 180px; overflow: hidden; background: #f8fafc; cursor: pointer; }
        .img-stack img {
            position: absolute;
            object-fit: cover;
            border-radius: 6px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
            transition: transform 0.3s ease;
        }
        .img-stack:hover img { transform: scale(1.03) !important; }

        /* Single image */
        .img-stack.single img { inset: 0; width: 100%; height: 100%; border-radius: 0; box-shadow: none; }

        /* 2-image stack */
        .img-stack.two img:nth-child(1) { top:8px; left:20px; width:68%; height:88%; transform: rotate(-4deg); }
        .img-stack.two img:nth-child(2) { top:8px; right:16px; width:68%; height:88%; transform: rotate(3deg); z-index:1; }

        /* 3+ image stack */
        .img-stack.many img:nth-child(1) { top:16px; left:8%; width:62%; height:82%; transform: rotate(-6deg); }
        .img-stack.many img:nth-child(2) { top:10px; left:22%; width:62%; height:82%; transform: rotate(-1deg); z-index:1; }
        .img-stack.many img:nth-child(3) { top:8px; right:6%; width:62%; height:82%; transform: rotate(5deg); z-index:2; }

        .img-stack-overlay {
            position: absolute; inset: 0; z-index: 10;
            display: flex; align-items: flex-end; justify-content: flex-end;
            padding: 10px; pointer-events: none;
        }
        .img-count-badge {
            background: rgba(15,23,42,0.75);
            color: white; font-size: 11px; font-weight: 700;
            padding: 4px 10px; border-radius: 20px;
            backdrop-filter: blur(4px);
            display: flex; align-items: center; gap: 4px;
        }

        /* Modal */
        .modal-overlay {
            position: fixed; inset: 0; z-index: 200;
            background: rgba(15,23,42,0.6);
            backdrop-filter: blur(6px);
            display: flex; align-items: center; justify-content: center;
            padding: 16px;
            opacity: 0; pointer-events: none;
            transition: opacity 0.25s ease;
        }
        .modal-overlay.open { opacity: 1; pointer-events: all; }
        .modal-box {
            background: white; border-radius: 24px;
            width: 100%; max-width: 560px;
            max-height: 90vh; overflow-y: auto;
            padding: 28px;
            transform: translateY(20px) scale(0.97);
            transition: all 0.25s ease;
            box-shadow: 0 24px 48px rgba(0,0,0,0.2);
        }
        .modal-overlay.open .modal-box { transform: translateY(0) scale(1); }

        /* Upload zone */
        .upload-zone {
            border: 2px dashed #c7d2fe;
            border-radius: 16px;
            padding: 28px 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s ease;
            background: #f5f3ff;
        }
        .upload-zone:hover, .upload-zone.drag-over {
            border-color: #6366f1;
            background: #eff0ff;
        }
        .upload-zone input { display: none; }

        /* Preview grid */
        .preview-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(90px, 1fr));
            gap: 8px;
            margin-top: 12px;
        }
        .preview-thumb {
            position: relative;
            aspect-ratio: 1;
            border-radius: 12px;
            overflow: hidden;
            border: 2px solid #e2e8f0;
            group: true;
        }
        .preview-thumb img { width: 100%; height: 100%; object-fit: cover; }
        .preview-thumb .remove-btn {
            position: absolute; top: 4px; right: 4px;
            width: 22px; height: 22px;
            background: rgba(220,38,38,0.9);
            color: white; border: none; border-radius: 50%;
            cursor: pointer; font-size: 10px;
            display: flex; align-items: center; justify-content: center;
            opacity: 0; transition: opacity 0.15s;
        }
        .preview-thumb:hover .remove-btn { opacity: 1; }

        /* Lightbox */
        .lightbox {
            position: fixed; inset: 0; z-index: 500;
            background: rgba(0,0,0,0.92);
            display: flex; align-items: center; justify-content: center;
            opacity: 0; pointer-events: none;
            transition: opacity 0.25s ease;
            flex-direction: column;
            gap: 16px;
        }
        .lightbox.open { opacity: 1; pointer-events: all; }
        .lightbox img {
            max-width: 90vw; max-height: 80vh;
            border-radius: 16px;
            box-shadow: 0 24px 60px rgba(0,0,0,0.5);
            transform: scale(0.9);
            transition: transform 0.25s ease;
        }
        .lightbox.open img { transform: scale(1); }
        .lightbox-nav {
            display: flex; align-items: center; gap: 20px;
        }
        .lightbox-nav button {
            background: rgba(255,255,255,0.15);
            border: 1px solid rgba(255,255,255,0.2);
            color: white; border-radius: 50%;
            width: 44px; height: 44px;
            cursor: pointer; font-size: 16px;
            transition: all 0.2s; display: flex;
            align-items: center; justify-content: center;
        }
        .lightbox-nav button:hover { background: rgba(255,255,255,0.3); }
        .lightbox-close {
            position: absolute; top: 20px; right: 20px;
            background: rgba(255,255,255,0.15);
            border: 1px solid rgba(255,255,255,0.2);
            color: white; border-radius: 50%;
            width: 40px; height: 40px;
            cursor: pointer; font-size: 18px;
            display: flex; align-items: center; justify-content: center;
            transition: all 0.2s;
        }
        .lightbox-close:hover { background: rgba(239,68,68,0.6); }
        .lightbox-counter {
            color: rgba(255,255,255,0.7);
            font-size: 12px; font-family: 'Prompt',sans-serif;
        }

        /* Empty gallery */
        .empty-gallery {
            grid-column: 1/-1;
            display: flex; flex-direction: column; align-items: center;
            padding: 80px 20px; text-align: center; color: #94a3b8;
        }
        .empty-gallery i { font-size: 60px; margin-bottom: 20px; opacity: 0.3; }

        /* Swal custom */
        .swal2-popup { font-family: 'Prompt', sans-serif !important; border-radius: 1.25rem !important; }
    </style>
</head>

<body class="text-slate-700">

    <!-- Loading -->
    <div id="payLoading">
        <div class="relative">
            <div class="w-14 h-14 border-4 border-emerald-200/50 border-dashed rounded-full animate-spin"></div>
            <div class="absolute top-0 left-0 w-14 h-14 border-4 border-emerald-500 border-t-transparent rounded-full animate-spin"></div>
        </div>
        <p class="mt-4 text-slate-600 font-medium text-sm animate-pulse">กำลังโหลด...</p>
    </div>

    <!-- Sidebar -->
    <?php include 'sidebar.php'; ?>

    <!-- Main Content -->
    <div class="max-w-6xl mx-auto px-3 md:px-6 py-4 md:py-6 space-y-5 fade-in-up w-full">

        <!-- Total Summary Bar -->
        <div class="glass-panel relative overflow-hidden bg-gradient-to-br from-emerald-600 via-emerald-500 to-teal-600 border border-emerald-400/30 p-4 md:p-5 flex flex-col sm:flex-row items-center justify-between gap-3 shadow-md shadow-emerald-500/20 group z-10 mb-5">
            <!-- Decorative background elements -->
            <div class="absolute top-0 right-0 w-64 h-64 bg-white opacity-10 rounded-full blur-3xl transform translate-x-1/3 -translate-y-1/3 pointer-events-none group-hover:opacity-20 transition-opacity duration-700"></div>
            <div class="absolute bottom-0 left-0 w-40 h-40 bg-white opacity-5 rounded-full blur-2xl transform -translate-x-1/2 translate-y-1/2 pointer-events-none"></div>
            <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iMiIgY3k9IjIiIHI9IjEiIGZpbGw9IiNmZmYiIGZpbGwtb3BhY2l0eT0iMC4wNSIvPjwvc3ZnPg==')] opacity-50 pointer-events-none"></div>
            
            <div class="relative z-10 flex items-center justify-center sm:justify-start gap-4 w-full sm:w-auto text-left">
                <div class="w-11 h-11 md:w-12 md:h-12 rounded-xl bg-white/20 backdrop-blur-md border border-white/30 flex items-center justify-center text-white flex-shrink-0 shadow-inner group-hover:scale-110 transition-transform duration-500">
                    <i class="fa-solid fa-file-invoice-dollar text-xl md:text-2xl drop-shadow-md"></i>
                </div>
                <div>
                    <h2 class="text-[9px] md:text-[10px] font-bold text-emerald-50/90 uppercase tracking-widest mb-0.5">ยอดโอนรวมทั้งหมด (Total Amount)</h2>
                    <div class="flex items-baseline gap-1">
                        <span class="text-2xl md:text-3xl font-black text-white tracking-tight drop-shadow-md leading-none" id="totalAmountDisplay">฿0.00</span>
                    </div>
                </div>
            </div>
            
            <div class="relative z-10 hidden sm:flex items-center">
                <div class="px-3 py-1.5 rounded-lg bg-black/10 backdrop-blur-sm border border-white/10 flex items-center gap-2 shadow-inner">
                    <div class="relative flex h-2 w-2">
                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-300 opacity-75"></span>
                      <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-400"></span>
                    </div>
                    <span class="text-[9px] uppercase font-bold text-emerald-50 tracking-wider">Live Sync</span>
                </div>
            </div>
        </div>

        <!-- Page Header -->
        <div class="glass-panel p-5 md:p-6 border-l-4 border-emerald-500 relative overflow-hidden">
            <div class="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-emerald-400 opacity-5 rounded-full pointer-events-none"></div>
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 relative z-10">
                <div class="flex-shrink-0">
                    <p class="text-[10px] text-slate-400 uppercase font-bold tracking-widest mb-1">
                        <i class="fa-solid fa-receipt mr-1 text-emerald-500"></i>Payment Proofs
                    </p>
                    <h1 class="text-xl md:text-2xl font-extrabold text-slate-800">หลักฐานการโอนเงิน</h1>
                    <p class="text-xs text-slate-400 mt-1" id="galleryStatus">กำลังโหลด...</p>
                </div>

                <div class="flex-shrink-0 w-full sm:w-auto mt-2 sm:mt-0">
                    <button onclick="openAddModal()"
                        class="w-full sm:w-auto flex items-center justify-center gap-2 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white px-5 py-3 rounded-2xl font-bold shadow-lg shadow-emerald-200 transition-all btn-lift active:scale-95 whitespace-nowrap">
                        <i class="fa-solid fa-plus text-lg"></i>
                        เพิ่มรายการอัปโหลด
                    </button>
                </div>
            </div>
        </div>

        <!-- Gallery Grid -->
        <div id="galleryGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 md:gap-5">
            <!-- Cards rendered here -->
            <div class="empty-gallery" style="grid-column:1/-1">
                <i class="fa-solid fa-spinner fa-spin text-emerald-400"></i>
                <p class="font-semibold">กำลังโหลดรายการ...</p>
            </div>
        </div>
    </div>

    <!-- ADD MODAL -->
    <div class="modal-overlay" id="addModal" onclick="onModalOverlayClick(event)">
        <div class="modal-box" onclick="event.stopPropagation()">
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-lg font-extrabold text-slate-800 flex items-center gap-2" id="modalTitleText">
                    <i class="fa-solid fa-image text-emerald-500"></i> เพิ่มหลักฐานการโอน
                </h2>
                <button onclick="closeAddModal()" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-red-50 text-slate-500 hover:text-red-500 flex items-center justify-center transition-all">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <!-- Title input -->
            <div class="mb-4">
                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5 block">ชื่อ / หัวข้อรายการ <span class="text-red-400">*</span></label>
                <input type="text" id="entryTitle" placeholder="เช่น โอนเงินค่าวัสดุ โครงการ A, ชำระค่าน็อต M10..."
                    class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-200 focus:border-emerald-500 outline-none transition-all text-sm font-medium">
            </div>

            <!-- Date -->
            <div class="mb-4">
                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5 block">วันที่โอน</label>
                <input type="date" id="entryDate"
                    class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-200 focus:border-emerald-500 outline-none transition-all text-sm">
            </div>

            <!-- Amount -->
            <div class="mb-4">
                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5 block">จำนวนเงิน (บาท)</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                        <span class="text-emerald-500 font-bold text-sm">฿</span>
                    </div>
                    <input type="number" id="entryAmount" min="0" step="0.01" placeholder="0.00"
                        class="w-full pl-9 pr-4 py-3 bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-200 focus:border-emerald-500 outline-none transition-all text-sm font-medium">
                </div>
            </div>

            <!-- Note -->
            <div class="mb-5">
                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5 block">หมายเหตุ</label>
                <textarea id="entryNote" rows="2" placeholder="รายละเอียดเพิ่มเติม..."
                    class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-200 focus:border-emerald-500 outline-none transition-all text-sm resize-none"></textarea>
            </div>

            <!-- Upload zone -->
            <div class="mb-4">
                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5 block">
                    รูปภาพหลักฐาน <span class="text-slate-400 font-normal">(เลือกได้หลายรูป)</span>
                </label>
                <div class="upload-zone" id="uploadZone" onclick="document.getElementById('imgInput').click()"
                     ondragover="onDragOver(event)" ondragleave="onDragLeave(event)" ondrop="onDrop(event)">
                    <input type="file" id="imgInput" accept="image/*" multiple onchange="handleFiles(this.files)">
                    <i class="fa-solid fa-cloud-arrow-up text-4xl text-indigo-300 mb-3 block"></i>
                    <p class="font-bold text-slate-600">คลิกหรือลากรูปมาวางที่นี่</p>
                    <p class="text-xs text-slate-400 mt-1">รองรับ JPG, PNG, WEBP — หลายรูปพร้อมกัน</p>
                </div>
                <div class="preview-grid" id="previewGrid"></div>
            </div>

            <!-- Buttons -->
            <div class="flex gap-3">
                <button onclick="closeAddModal()" class="flex-1 py-3 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-2xl font-bold transition-all">
                    ยกเลิก
                </button>
                <button onclick="saveEntry()" id="saveBtn"
                    class="flex-1 py-3 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white rounded-2xl font-bold shadow-lg shadow-emerald-200 transition-all flex items-center justify-center gap-2 btn-lift">
                    <i class="fa-solid fa-floppy-disk"></i> บันทึก
                </button>
            </div>
        </div>
    </div>

    <!-- LIGHTBOX -->
    <div class="lightbox" id="lightbox" onclick="closeLightbox()">
        <button class="lightbox-close" onclick="closeLightbox()"><i class="fa-solid fa-xmark"></i></button>
        <img id="lightboxImg" src="" alt="รูปหลักฐาน" onclick="event.stopPropagation()">
        <div class="lightbox-nav" onclick="event.stopPropagation()">
            <button onclick="lightboxPrev()"><i class="fa-solid fa-chevron-left"></i></button>
            <span class="lightbox-counter" id="lightboxCounter">1 / 1</span>
            <button onclick="lightboxNext()"><i class="fa-solid fa-chevron-right"></i></button>
        </div>
    </div>

    <script type="module">
        import { initializeApp } from "https://www.gstatic.com/firebasejs/11.6.1/firebase-app.js";
        import { getFirestore, collection, addDoc, getDocs, deleteDoc, doc, query, orderBy, serverTimestamp, onSnapshot, updateDoc }
            from "https://www.gstatic.com/firebasejs/11.6.1/firebase-firestore.js";
        import { getAuth, signInAnonymously, signInWithCustomToken, onAuthStateChanged }
            from "https://www.gstatic.com/firebasejs/11.6.1/firebase-auth.js";

        let firebaseConfig;
        let isCanvasEnv = false;
        try { 
            if (typeof __firebase_config !== 'undefined') {
                firebaseConfig = JSON.parse(__firebase_config); 
                isCanvasEnv = true;
            }
        } catch(e){}

        const appId = typeof __app_id !== 'undefined' ? __app_id : 'default-bom-app';
        if (!firebaseConfig) {
            firebaseConfig = {
                apiKey: "AIzaSyBj8bKeS9Whnh8uOXbAxY_znNgIyzcE-Sg",
                authDomain: "bom-mentra.firebaseapp.com",
                projectId: "bom-mentra",
                storageBucket: "bom-mentra.firebasestorage.app",
                messagingSenderId: "916019460525",
                appId: "1:916019460525:web:11328f705e57d00d53c924",
            };
        }

        const app = initializeApp(firebaseConfig);
        const db = getFirestore(app);
        const auth = getAuth(app);
        const getProofsRef = () => isCanvasEnv ? collection(db, 'artifacts', appId, 'public', 'data', 'payment_proofs') : collection(db, 'payment_proofs');

        // State
        let pendingImages = []; // { dataUrl, file }
        let allEntries = [];
        let lightboxImages = [];
        let lightboxIndex = 0;
        let currentRole = localStorage.getItem('mentra_role') || '';
        let editingId = null;

        // ============================
        // File handling
        // ============================
        const fileToDataUrl = (file) => new Promise((resolve, reject) => {
            const reader = new FileReader();
            reader.onload = e => resolve(e.target.result);
            reader.onerror = reject;
            reader.readAsDataURL(file);
        });

        const compressImage = (dataUrl, maxSize = 800) => new Promise(resolve => {
            const img = new Image();
            img.onload = () => {
                const canvas = document.createElement('canvas');
                let w = img.width, h = img.height;
                if (w > maxSize || h > maxSize) {
                    if (w > h) { h = Math.round(h * maxSize / w); w = maxSize; }
                    else { w = Math.round(w * maxSize / h); h = maxSize; }
                }
                canvas.width = w; canvas.height = h;
                canvas.getContext('2d').drawImage(img, 0, 0, w, h);
                resolve(canvas.toDataURL('image/jpeg', 0.82));
            };
            img.src = dataUrl;
        });

        window.handleFiles = async (files) => {
            for (const file of Array.from(files)) {
                if (!file.type.startsWith('image/')) continue;
                const raw = await fileToDataUrl(file);
                const compressed = await compressImage(raw, 1200);
                pendingImages.push({ dataUrl: compressed, name: file.name });
            }
            renderPreviews();
        };

        const renderPreviews = () => {
            const grid = document.getElementById('previewGrid');
            if (!pendingImages.length) { grid.innerHTML = ''; return; }
            grid.innerHTML = pendingImages.map((img, i) => `
                <div class="preview-thumb">
                    <img src="${img.dataUrl}" alt="${img.name}">
                    <button class="remove-btn" onclick="removePreview(${i})">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>`).join('');
        };

        window.removePreview = (idx) => {
            pendingImages.splice(idx, 1);
            renderPreviews();
        };

        // Drag & Drop
        window.onDragOver = (e) => { e.preventDefault(); document.getElementById('uploadZone').classList.add('drag-over'); };
        window.onDragLeave = () => document.getElementById('uploadZone').classList.remove('drag-over');
        window.onDrop = (e) => {
            e.preventDefault();
            document.getElementById('uploadZone').classList.remove('drag-over');
            handleFiles(e.dataTransfer.files);
        };

        // ============================
        // Modal
        // ============================
        window.openAddModal = () => {
            editingId = null;
            document.getElementById('modalTitleText').innerHTML = '<i class="fa-solid fa-image text-emerald-500"></i> เพิ่มหลักฐานการโอน';
            pendingImages = [];
            document.getElementById('entryTitle').value = '';
            document.getElementById('entryDate').value = new Date().toISOString().split('T')[0];
            document.getElementById('entryAmount').value = '';
            document.getElementById('entryNote').value = '';
            document.getElementById('previewGrid').innerHTML = '';
            document.getElementById('addModal').classList.add('open');
        };

        window.openEditModal = (idx) => {
            const entry = allEntries[idx];
            if (!entry) return;
            
            editingId = entry.id;
            document.getElementById('modalTitleText').innerHTML = '<i class="fa-solid fa-pen text-amber-500"></i> แก้ไขหลักฐานการโอน';
            document.getElementById('entryTitle').value = entry.title || '';
            document.getElementById('entryDate').value = entry.date || new Date().toISOString().split('T')[0];
            document.getElementById('entryAmount').value = entry.amount || '';
            document.getElementById('entryNote').value = entry.note || '';
            
            pendingImages = (entry.images || []).map(img => ({ dataUrl: img, name: 'saved_image' }));
            renderPreviews();
            
            document.getElementById('addModal').classList.add('open');
        };

        window.closeAddModal = () => {
            document.getElementById('addModal').classList.remove('open');
            pendingImages = [];
        };

        window.onModalOverlayClick = (e) => {
            if (e.target === document.getElementById('addModal')) closeAddModal();
        };

        // ============================
        // Save entry
        // ============================
        window.saveEntry = async () => {
            const title = document.getElementById('entryTitle').value.trim();
            const date = document.getElementById('entryDate').value;
            const amountVal = document.getElementById('entryAmount').value;
            const amount = amountVal ? parseFloat(amountVal) : null;
            const note = document.getElementById('entryNote').value.trim();

            if (!title) {
                document.getElementById('entryTitle').focus();
                document.getElementById('entryTitle').style.borderColor = '#ef4444';
                setTimeout(() => document.getElementById('entryTitle').style.borderColor = '', 2000);
                Swal.fire({ toast:true, position:'top', icon:'warning', title:'กรุณากรอกชื่อ/หัวข้อรายการ', showConfirmButton:false, timer:2500, timerProgressBar:true });
                return;
            }
            if (!pendingImages.length) {
                Swal.fire({ toast:true, position:'top', icon:'warning', title:'กรุณาเลือกรูปภาพอย่างน้อย 1 รูป', showConfirmButton:false, timer:2500 });
                return;
            }

            const saveBtn = document.getElementById('saveBtn');
            saveBtn.disabled = true;
            saveBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> กำลังบันทึก...';

            try {
                const payload = {
                    title,
                    date: date || new Date().toISOString().split('T')[0],
                    amount,
                    note,
                    images: pendingImages.map(p => p.dataUrl),
                    imageCount: pendingImages.length
                };

                if (editingId) {
                    await updateDoc(doc(getProofsRef(), editingId), payload);
                    const idx = allEntries.findIndex(e => e.id === editingId);
                    if (idx > -1) {
                        allEntries[idx] = { ...allEntries[idx], ...payload };
                    }
                    closeAddModal();
                    renderGallery();
                    Swal.mixin({ toast:true, position:'bottom-end', showConfirmButton:false, timer:3000, timerProgressBar:true })
                        .fire({ icon:'success', title:'แก้ไขหลักฐานเรียบร้อย!' });
                } else {
                    payload.uploadedBy = localStorage.getItem('mentra_user') ? JSON.parse(localStorage.getItem('mentra_user')).name || 'Unknown' : 'Unknown';
                    payload.createdAt = serverTimestamp();
                    const docRef = await addDoc(getProofsRef(), payload);
                    const newEntry = { id: docRef.id, ...payload, createdAt: { seconds: Date.now()/1000 } };
                    allEntries.unshift(newEntry);

                    closeAddModal();
                    renderGallery();

                    Swal.mixin({ toast:true, position:'bottom-end', showConfirmButton:false, timer:3000, timerProgressBar:true })
                        .fire({ icon:'success', title:'บันทึกหลักฐานเรียบร้อย!' });
                }
            } catch(e) {
                console.error('Save error:', e);
                Swal.fire('Error', 'บันทึกไม่สำเร็จ: ' + e.message, 'error');
            } finally {
                saveBtn.disabled = false;
                saveBtn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> บันทึก';
            }
        };

        // ============================
        // Load + Render gallery
        // ============================
        let unsubEntries = null;

        const loadEntries = () => {
            if (unsubEntries) unsubEntries();
            
            unsubEntries = onSnapshot(query(getProofsRef(), orderBy('createdAt', 'desc')), (snap) => {
                allEntries = [];
                let totalAmount = 0;
                snap.forEach(d => {
                    const data = d.data();
                    if (data.amount) totalAmount += Number(data.amount);
                    allEntries.push({ id: d.id, ...data });
                });
                renderGallery();
                document.getElementById('galleryStatus').textContent =
                    `${allEntries.length} รายการหลักฐาน • คลิกรูปเพื่อดูขนาดใหญ่`;
                const totalEl = document.getElementById('totalAmountDisplay');
                if (totalEl) {
                    totalEl.textContent = `฿${totalAmount.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
                }
            }, (e) => {
                console.error('Load error:', e);
                document.getElementById('galleryStatus').textContent = 'โหลดข้อมูลล้มเหลว (Permission Denied)';
                allEntries = [];
                renderGallery();
            });
        };

        const renderGallery = () => {
            const grid = document.getElementById('galleryGrid');

            if (!allEntries.length) {
                grid.innerHTML = `<div class="empty-gallery">
                    <i class="fa-solid fa-receipt"></i>
                    <p class="text-lg font-bold text-slate-500 mb-2">ยังไม่มีหลักฐานการโอนเงิน</p>
                    <p class="text-sm">กดปุ่ม "เพิ่มรายการอัปโหลด" เพื่อเริ่มบันทึก</p>
                </div>`;
                return;
            }

            grid.innerHTML = allEntries.map((entry, idx) => {
                const imgs = entry.images || [];
                const count = imgs.length;
                let stackClass = count === 1 ? 'single' : count === 2 ? 'two' : 'many';
                const show = imgs.slice(0, 3);

                const dateStr = entry.date
                    ? new Date(entry.date).toLocaleDateString('th-TH', {year:'numeric',month:'long',day:'numeric'})
                    : '—';
                const timeStr = entry.createdAt?.seconds
                    ? new Date(entry.createdAt.seconds*1000).toLocaleString('th-TH',{hour:'2-digit',minute:'2-digit'})
                    : '';

                return `<div class="payment-card">
                    <!-- Image stack -->
                    <div class="img-stack ${stackClass}" onclick="openLightbox(${idx}, 0)">
                        ${show.map(img => `<img src="${img}" alt="หลักฐาน" loading="lazy">`).join('')}
                        <div class="img-stack-overlay">
                            ${count > 1 ? `<span class="img-count-badge"><i class="fa-solid fa-images"></i> ${count} รูป</span>` : ''}
                        </div>
                    </div>
                    <!-- Card content -->
                    <div class="p-4">
                        <h3 class="font-bold text-slate-800 text-sm leading-snug mb-1.5 line-clamp-2">${escHtml(entry.title)}</h3>
                        ${entry.amount ? `<div class="flex items-center gap-1.5 mb-2"><span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200 shadow-sm"><i class="fa-solid fa-coins text-emerald-400"></i> ฿${Number(entry.amount).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}</span></div>` : ''}
                        <div class="flex items-center gap-2 text-xs text-slate-400 mb-2">
                            <i class="fa-regular fa-calendar text-emerald-400"></i>
                            <span>${dateStr}</span>
                            ${timeStr ? `<span class="w-1 h-1 bg-slate-300 rounded-full"></span><span>${timeStr}</span>` : ''}
                        </div>
                        ${entry.note ? `<p class="text-xs text-slate-500 mb-3 line-clamp-2 bg-slate-50 rounded-lg px-2.5 py-1.5">📝 ${escHtml(entry.note)}</p>` : ''}
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-1.5 text-xs text-slate-400">
                                <i class="fa-solid fa-user-circle text-slate-300"></i>
                                <span class="truncate max-w-[100px]">${escHtml(entry.uploadedBy || '—')}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <button onclick="openLightbox(${idx}, 0)"
                                    class="text-xs bg-indigo-50 hover:bg-indigo-100 text-indigo-600 border border-indigo-100 px-2.5 py-1.5 rounded-lg font-bold transition-all flex items-center gap-1">
                                    <i class="fa-solid fa-expand"></i> ดูรูป
                                </button>
                                ${currentRole === 'admin' ? `
                                <button onclick="openEditModal(${idx})"
                                    class="text-xs bg-amber-50 hover:bg-amber-100 text-amber-600 border border-amber-100 px-2.5 py-1.5 rounded-lg font-bold transition-all flex items-center gap-1">
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                                <button onclick="deleteEntry('${entry.id}', ${idx})"
                                    class="text-xs bg-red-50 hover:bg-red-100 text-red-500 border border-red-100 px-2.5 py-1.5 rounded-lg font-bold transition-all flex items-center gap-1">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>` : ''}
                            </div>
                        </div>
                    </div>
                </div>`;
            }).join('');
        };

        const escHtml = (str) => {
            if (!str) return '';
            return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
        };

        // ============================
        // Lightbox
        // ============================
        window.openLightbox = (entryIdx, imgIdx) => {
            const entry = allEntries[entryIdx];
            if (!entry || !entry.images?.length) return;
            lightboxImages = entry.images;
            lightboxIndex = imgIdx;
            updateLightboxImg();
            document.getElementById('lightbox').classList.add('open');
            document.body.style.overflow = 'hidden';
        };

        window.closeLightbox = () => {
            document.getElementById('lightbox').classList.remove('open');
            document.body.style.overflow = '';
        };

        const updateLightboxImg = () => {
            document.getElementById('lightboxImg').src = lightboxImages[lightboxIndex];
            document.getElementById('lightboxCounter').textContent = `${lightboxIndex+1} / ${lightboxImages.length}`;
        };

        window.lightboxPrev = () => {
            lightboxIndex = (lightboxIndex - 1 + lightboxImages.length) % lightboxImages.length;
            updateLightboxImg();
        };

        window.lightboxNext = () => {
            lightboxIndex = (lightboxIndex + 1) % lightboxImages.length;
            updateLightboxImg();
        };

        // Keyboard nav for lightbox
        document.addEventListener('keydown', (e) => {
            if (!document.getElementById('lightbox').classList.contains('open')) return;
            if (e.key === 'ArrowLeft') lightboxPrev();
            if (e.key === 'ArrowRight') lightboxNext();
            if (e.key === 'Escape') closeLightbox();
        });

        // ============================
        // Delete entry (admin only)
        // ============================
        window.deleteEntry = async (id, idx) => {
            const result = await Swal.fire({
                title: 'ลบรายการนี้?',
                text: 'หลักฐานและรูปภาพทั้งหมดจะถูกลบถาวร',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonText: 'ยกเลิก',
                confirmButtonText: 'ลบเลย'
            });
            if (!result.isConfirmed) return;

            try {
                await deleteDoc(doc(getProofsRef(), id));
                allEntries.splice(idx, 1);
                renderGallery();
                document.getElementById('galleryStatus').textContent = `${allEntries.length} รายการหลักฐาน`;
                Swal.mixin({ toast:true, position:'bottom-end', showConfirmButton:false, timer:2000 })
                    .fire({ icon:'success', title:'ลบรายการเรียบร้อยแล้ว' });
            } catch(e) {
                Swal.fire('Error', 'ลบไม่สำเร็จ: ' + e.message, 'error');
            }
        };

        // ============================
        // Auth & Init
        // ============================
        const hidePayLoader = () => {
            const loader = document.getElementById('payLoading');
            if (loader) { loader.style.opacity = '0'; setTimeout(() => loader.style.display = 'none', 500); }
        };

        const initAuth = async () => {
            try {
                if (typeof __initial_auth_token !== 'undefined' && __initial_auth_token) {
                    await signInWithCustomToken(auth, __initial_auth_token);
                } else {
                    await signInAnonymously(auth);
                }
            } catch (error) { 
                console.error("Auth Failed:", error); 
                hidePayLoader();
                loadEntries(); // Fallback
            }
        };

        onAuthStateChanged(auth, (user) => {
            if (user) {
                hidePayLoader();
                loadEntries();
            } else {
                initAuth();
            }
        });

        initAuth();

        // Safety timeout
        setTimeout(hidePayLoader, 3500);
    </script>
</body>
</html>
