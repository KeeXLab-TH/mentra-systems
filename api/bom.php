<!DOCTYPE html>
<html lang="th">

<head>
    <!-- *** 1. ระบบป้องกัน: ตรวจสอบสิทธิ์ก่อนโหลดหน้าเว็บ *** -->
    <script>
        // ถ้าไม่มี 'mentra_role' ในเครื่อง หรือ role ไม่ถูกต้อง ให้ดีดกลับไป index.php ทันที
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
    <title>Mentra BOM Manager</title>

    <!-- ฟอนต์และ Style -->
    <!-- Resource Hints: ให้ browser เตรียม connection ล่วงหน้า -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://www.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdn.tailwindcss.com">
    <link rel="dns-prefetch" href="https://cdnjs.cloudflare.com">
    <link rel="dns-prefetch" href="https://cdn.jsdelivr.net">
    <link rel="dns-prefetch" href="https://firestore.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- SweetAlert2 สำหรับ Popup สวยๆ -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" defer></script>
    <!-- html2pdf.js สำหรับ Export PDF -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js" defer></script>
    <!-- ExcelJS + FileSaver สำหรับ Export Excel สวยๆ -->
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
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-radius: 1.25rem;
            box-shadow: 0 4px 24px -4px rgba(0, 0, 0, 0.06), 0 1px 4px rgba(0, 0, 0, 0.04);
            transition: box-shadow 0.3s ease;
        }

        .glass-panel:hover {
            box-shadow: 0 8px 32px -4px rgba(0, 0, 0, 0.1);
        }

        /* Loading Overlay */
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

        .swal2-popup {
            font-family: 'Prompt', sans-serif;
            border-radius: 1.25rem !important;
        }

        /* Scrollbar */
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

        /* Role Guards */
        body:not(.is-admin) .admin-only {
            display: none !important;
        }

        body.lock-form #guestOverlay {
            display: flex !important;
        }

        /* Input Focus */
        input:focus,
        select:focus,
        textarea:focus {
            border-color: #3b82f6 !important;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15) !important;
        }

        /* Mobile Nav Toggle */
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

        /* Mobile Card Layout for Items */
        @media (max-width: 767px) {
            .table-container table thead {
                display: none;
            }

            .table-container table,
            .table-container table tbody {
                display: block;
                width: 100%;
            }

            .table-container table tr {
                display: grid;
                grid-template-columns: 64px 1fr;
                grid-template-rows: auto auto auto auto auto;
                gap: 0;
                background: white;
                border-radius: 16px;
                padding: 0;
                margin-bottom: 12px;
                border: 1px solid #e2e8f0;
                box-shadow: 0 2px 8px -2px rgba(0, 0, 0, 0.06);
                overflow: hidden;
                position: relative;
            }

            .table-container table td {
                padding: 0 !important;
                border: none !important;
                text-align: left !important;
                display: block;
                width: 100%;
            }

            .table-container table td::before {
                display: none !important;
            }

            /* -- Image: top-left corner -- */
            .table-container table td:nth-child(1) {
                grid-column: 1;
                grid-row: 1 / 3;
                padding: 12px 0 12px 12px !important;
                display: flex !important;
                align-items: flex-start;
                justify-content: center;
            }

            .table-container table td:nth-child(1) img {
                width: 52px !important;
                height: 52px !important;
                border-radius: 10px !important;
                object-fit: cover;
                border: 2px solid #f1f5f9;
                cursor: pointer;
            }

            /* -- Name + Link: top-right -- */
            .table-container table td:nth-child(2) {
                grid-column: 2;
                grid-row: 1;
                padding: 12px 14px 2px 8px !important;
                font-size: 14px !important;
                font-weight: 700 !important;
                color: #1e293b;
                line-height: 1.3;
            }

            /* -- Details: below name -- */
            .table-container table td:nth-child(3) {
                grid-column: 2;
                grid-row: 2;
                padding: 0 14px 8px 8px !important;
                font-size: 12px !important;
                color: #64748b !important;
                max-width: none !important;
                white-space: normal !important;
                overflow: visible !important;
                text-overflow: unset !important;
            }

            /* -- Divider area for qty/total/status -- */
            .table-container table td:nth-child(4),
            .table-container table td:nth-child(5),
            .table-container table td:nth-child(6) {
                grid-row: 3;
                background: #f8fafc;
                border-top: 1px solid #f1f5f9 !important;
                padding: 8px 6px !important;
                text-align: center !important;
                font-size: 12px !important;
            }

            /* Qty */
            .table-container table td:nth-child(4) {
                grid-column: 1;
                padding: 8px 6px 8px 14px !important;
                text-align: left !important;
            }

            .table-container table td:nth-child(4)::after {
                display: block !important;
                content: "จำนวน";
                font-size: 9px;
                color: #94a3b8;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 0.05em;
                margin-top: 1px;
            }

            /* Total */
            .table-container table td:nth-child(5) {
                grid-column: 2;
                text-align: right !important;
                padding: 8px 14px 8px 6px !important;
                font-weight: 800 !important;
                color: #2563eb !important;
                font-size: 14px !important;
            }

            .table-container table td:nth-child(5)::after {
                display: block !important;
                content: "ยอดรวม";
                font-size: 9px;
                color: #94a3b8;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 0.05em;
                margin-top: 1px;
                text-align: right;
            }

            /* Status */
            .table-container table td:nth-child(6) {
                grid-column: 1 / 3;
                grid-row: 4;
                padding: 6px 14px !important;
                text-align: left !important;
                background: #f8fafc;
                border-bottom: 1px solid #f1f5f9 !important;
            }

            /* -- Manage buttons -- */
            .table-container table td:nth-child(7) {
                grid-column: 2;
                grid-row: 5;
                padding: 8px 14px 10px !important;
                text-align: right !important;
            }

            .table-container table td:nth-child(7) .flex {
                justify-content: flex-end !important;
            }

            /* -- Remark -- */
            .table-container table td:nth-child(8) {
                grid-column: 1;
                grid-row: 5;
                padding: 8px 6px 10px 14px !important;
                font-size: 11px !important;
            }

            .table-container table td:nth-child(8):not(:empty)::before {
                display: inline !important;
                content: "📝 ";
                font-size: 10px;
            }

            /* Card no-image fallback */
            .table-container table td:nth-child(1):has(> span) {
                display: flex !important;
                align-items: center;
                justify-content: center;
                color: #cbd5e1;
                font-size: 20px;
            }

            /* Container cleanup */
            .table-container {
                border: none !important;
                box-shadow: none !important;
                background: transparent !important;
                border-radius: 0 !important;
                padding: 0 !important;
            }
        }

        /* Micro Animations */
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

        @keyframes slideDown {
            from {
                opacity: 0;
                max-height: 0;
            }

            to {
                opacity: 1;
                max-height: 500px;
            }
        }

        .slide-down {
            animation: slideDown 0.3s ease-out;
        }

        /* Button hover lift */
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
            background: linear-gradient(135deg, #2563eb, #f97316);
            color: white;
            border-color: transparent;
            box-shadow: 0 2px 8px rgba(37, 99, 235, 0.3);
        }

        .filter-pill.active-filter:hover {
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.4);
        }

        /* Status Card Selector */
        .status-card {
            cursor: pointer;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 14px 12px;
            text-align: center;
            transition: all 0.2s ease;
            background: white;
            position: relative;
        }

        .status-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .status-card.selected {
            border-width: 2px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }

        .status-card.selected::after {
            content: '\f00c';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            position: absolute;
            top: -6px;
            right: -6px;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            color: white;
        }

        .status-card[data-status="pending"].selected {
            border-color: #64748b;
            background: #f8fafc;
        }

        .status-card[data-status="pending"].selected::after {
            background: #64748b;
        }

        .status-card[data-status="ordered"].selected {
            border-color: #2563eb;
            background: #eff6ff;
        }

        .status-card[data-status="ordered"].selected::after {
            background: #2563eb;
        }

        .status-card[data-status="received"].selected {
            border-color: #16a34a;
            background: #f0fdf4;
        }

        .status-card[data-status="received"].selected::after {
            background: #16a34a;
        }

        .status-card[data-status="cancelled"].selected {
            border-color: #dc2626;
            background: #fef2f2;
        }

        .status-card[data-status="cancelled"].selected::after {
            background: #dc2626;
        }

        /* Sticky sidebar on desktop */
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
    </style>
</head>

<body class="text-slate-700 bg-slate-50">

    <!-- Loading Screen -->
    <div id="mainLoading">
        <div class="relative">
            <div class="w-14 h-14 border-4 border-blue-200/50 border-dashed rounded-full animate-spin"></div>
            <div
                class="absolute top-0 left-0 w-14 h-14 border-4 border-orange-500 border-t-transparent rounded-full animate-spin">
            </div>
        </div>
        <p class="mt-4 text-slate-600 font-medium text-sm animate-pulse">กำลังตรวจสอบสิทธิ์...</p>
    </div>

    <!-- Sidebar and Header -->
    <?php include 'sidebar.php'; ?>

    <div
        class="w-full px-3 md:px-4 lg:px-6 xl:px-8 py-4 md:py-6 grid grid-cols-1 xl:grid-cols-12 gap-4 md:gap-5 relative z-10">

        <!-- Sidebar / Left Panel inside Workspace -->
        <div class="xl:col-span-4 space-y-4 md:space-y-5 sidebar-sticky">
            <!-- 1. ส่วนเลือกโครงการ -->
            <div class="glass-panel p-4 md:p-5 border-l-4 border-orange-500 relative overflow-hidden transition-all duration-500 fade-in-up"
                id="projectPanel">
                <!-- Project Cover Image Display -->
                <div id="projectCoverContainer"
                    class="hidden mb-4 rounded-xl overflow-hidden h-36 md:h-40 w-full relative shadow-md group border border-gray-100">
                    <img id="projectCoverImg" src=""
                        class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-700">
                    <div
                        class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent flex items-end p-3">
                        <span id="projectCoverTitle"
                            class="text-white font-bold text-lg drop-shadow-md truncate w-full"></span>
                    </div>
                </div>

                <div
                    class="absolute top-0 right-0 -mt-2 -mr-2 w-16 h-16 bg-blue-500 opacity-10 rounded-full pointer-events-none">
                </div>

                <label class="block text-xs font-bold text-slate-500 uppercase mb-2 tracking-wider">
                    <i class="fa-solid fa-folder-tree mr-1 text-blue-600"></i> จัดการโครงการ
                </label>

                <div class="flex gap-2 mb-3">
                    <div class="relative w-full">
                        <select id="projectSelect" onchange="window.changeProject(this.value)"
                            class="w-full appearance-none bg-slate-50 border border-slate-300 text-slate-800 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block p-2.5 pr-8 cursor-pointer font-medium transition-all hover:bg-white shadow-sm">
                            <option value="" disabled selected>-- เลือกโครงการ --</option>
                        </select>
                        <div
                            class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-500">
                            <i class="fa-solid fa-chevron-down text-xs"></i>
                        </div>
                    </div>
                </div>

                <!-- ปุ่มควบคุมโครงการ (เพิ่ม class admin-only) -->
                <div class="grid grid-cols-3 gap-2 admin-only">
                    <button onclick="window.createNewProject()"
                        class="bg-blue-600 hover:bg-blue-700 text-white rounded-lg px-2 py-2 transition-all shadow-sm active:scale-95 text-xs flex items-center justify-center gap-1 font-medium btn-lift">
                        <i class="fa-solid fa-plus"></i> สร้างใหม่
                    </button>
                    <button onclick="window.editCurrentProject()"
                        class="bg-orange-50 hover:bg-orange-100 text-orange-600 border border-orange-200 rounded-lg px-2 py-2 transition-all shadow-sm active:scale-95 text-xs flex items-center justify-center gap-1 font-medium btn-lift">
                        <i class="fa-solid fa-pen-to-square"></i> แก้ไข
                    </button>
                    <button onclick="window.deleteCurrentProject()"
                        class="bg-red-50 hover:bg-red-100 text-red-500 border border-red-200 rounded-lg px-2 py-2 transition-all shadow-sm active:scale-95 text-xs flex items-center justify-center gap-1 font-medium btn-lift">
                        <i class="fa-solid fa-trash-can"></i> ลบ
                    </button>
                </div>

                <div id="noProjectAlert"
                    class="mt-3 text-xs text-orange-700 bg-orange-50 p-3 rounded-lg border border-orange-200 flex items-start gap-2 animate-pulse">
                    <i class="fa-solid fa-circle-exclamation mt-0.5 text-orange-500"></i>
                    <span>กรุณาเลือกโครงการ หรือสร้างใหม่<br>เพื่อเริ่มบันทึกรายการ</span>
                </div>
            </div>

            <!-- 2. ส่วนฟอร์มบันทึก -->
            <div id="formPanel"
                class="glass-panel p-4 md:p-6 border border-gray-100/50 opacity-50 pointer-events-none transition-all duration-300 relative fade-in-up">

                <!-- GUEST OVERLAY: เพิ่มมาใหม่ บังฟอร์มถ้าไม่ใช่ Admin หรือ Material -->
                <div id="guestOverlay"
                    class="hidden absolute inset-0 bg-slate-50/80 backdrop-blur-[2px] z-20 flex flex-col items-center justify-center rounded-xl border border-slate-200 text-center p-4">
                    <div
                        class="w-12 h-12 bg-white rounded-full flex items-center justify-center shadow-sm mb-3 text-slate-400 text-xl border border-slate-100">
                        <i class="fa-solid fa-lock"></i>
                    </div>
                    <h4 class="text-sm font-bold text-slate-700">Locked</h4>
                    <p class="text-xs text-slate-400 mt-1">สิทธิ์ของคุณไม่สามารถเพิ่มรายการได้</p>
                </div>

                <div class="absolute top-4 right-4 text-blue-100 text-6xl opacity-20 -z-10 transform rotate-12">
                    <i class="fa-solid fa-pen-nib"></i>
                </div>

                <div class="flex items-center justify-between mb-5 border-b pb-2 border-slate-100">
                    <h2 class="text-lg font-bold flex items-center gap-2 text-slate-800">
                        <i class="fa-solid fa-circle-plus text-blue-600"></i> เพิ่มรายการวัสดุ
                    </h2>
                    <button type="button" onclick="window.importExistingItem()"
                        class="text-xs bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 px-3 py-1.5 rounded-lg transition-all shadow-sm flex items-center gap-1.5 font-medium active:scale-95">
                        <i class="fa-solid fa-rotate-right"></i> ดึงรายการเดิม
                    </button>
                </div>

                <form id="bomForm" class="space-y-4" onsubmit="window.addItem(event)">
                    <!-- Import Mode Banner -->
                    <div id="importModeBanner"
                        class="hidden bg-emerald-50 border border-emerald-200 rounded-lg p-3 flex items-center justify-between gap-2 animate-pulse-once">
                        <div class="flex items-center gap-2 min-w-0">
                            <img id="importModeImg" src=""
                                class="w-9 h-9 rounded-md object-cover border border-emerald-200 hidden flex-shrink-0">
                            <div class="min-w-0">
                                <p class="text-xs font-bold text-emerald-700 truncate" id="importModeName"></p>
                                <p class="text-[10px] text-emerald-500">ดึงจากรายการเดิม — แก้ได้เฉพาะ <b>จำนวน</b> และ
                                    <b>รายละเอียด</b>
                                </p>
                            </div>
                        </div>
                        <button type="button" onclick="window.clearImportMode()"
                            class="text-xs text-red-500 hover:text-red-700 flex-shrink-0 font-bold px-2 py-1 rounded hover:bg-red-50 transition-all"
                            title="ยกเลิกโหมดดึงรายการ">
                            <i class="fa-solid fa-xmark"></i> ยกเลิก
                        </button>
                    </div>
                    <div>
                        <label class="text-xs font-bold text-slate-500 uppercase mb-1 block">ชื่อสินค้า / วัสดุ</label>
                        <input type="text" id="itemName" required
                            class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 outline-none transition-all placeholder-slate-400"
                            placeholder="เช่น ปูนซีเมนต์, เหล็กเส้น...">
                    </div>

                    <div>
                        <label class="text-xs font-bold text-slate-500 uppercase mb-1 block">รายละเอียดเพิ่มเติม</label>
                        <textarea id="itemDetails" rows="2"
                            class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 outline-none transition-all text-sm"
                            placeholder="เช่น สี, ขนาด, สเปค..."></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs font-bold text-slate-500 uppercase mb-1 block">ราคาต่อหน่วย
                                (บาท)</label>
                            <input type="number" id="itemPrice" required min="0" step="0.01"
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-lg outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200"
                                placeholder="0.00">
                        </div>
                        <div>
                            <label class="text-xs font-bold text-slate-500 uppercase mb-1 block">จำนวน</label>
                            <input type="number" id="itemQty" required min="1" value="1"
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-lg outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200 text-center font-semibold text-blue-700">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs font-bold text-slate-500 uppercase mb-1 block">สถานะ</label>
                            <select id="itemStatus"
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-lg outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200 text-sm cursor-pointer">
                                <option value="pending">⏳ รอดำเนินการ</option>
                                <option value="ordered">🚚 สั่งซื้อแล้ว</option>
                                <option value="received">✅ ได้รับของแล้ว</option>
                                <option value="cancelled">❌ ยกเลิก</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-slate-500 uppercase mb-1 block">หมายเหตุ</label>
                            <input type="text" id="itemRemark"
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-lg outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200 text-sm"
                                placeholder="...">
                        </div>
                    </div>

                    <div>
                        <label class="text-xs font-bold text-slate-500 uppercase mb-1 block">ลิงก์ร้านค้า</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2.5 text-slate-400"><i
                                    class="fa-solid fa-link text-xs"></i></span>
                            <input type="url" id="itemLink"
                                class="w-full pl-9 pr-4 py-2.5 bg-slate-50 border border-slate-300 rounded-lg outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200 text-sm"
                                placeholder="https://...">
                        </div>
                    </div>

                    <div>
                        <label class="text-xs font-bold text-slate-500 uppercase mb-1 block">รูปภาพ</label>
                        <input type="file" id="itemImage" accept="image/*"
                            class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer border border-dashed border-slate-300 rounded-lg p-1" />
                    </div>

                    <button type="submit" id="submitBtn"
                        class="w-full bg-gradient-to-r from-blue-600 to-orange-500 hover:from-blue-700 hover:to-orange-600 text-white font-bold py-3 rounded-xl shadow-lg hover:shadow-xl transition-all flex justify-center items-center gap-2 mt-4 btn-lift">
                        <i class="fa-solid fa-floppy-disk"></i> <span>บันทึกลงโครงการ</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- Right: Data Table -->
        <div class="xl:col-span-8">
            <div
                class="glass-panel p-3 md:p-6 min-h-[400px] md:min-h-[600px] relative border border-gray-100/50 flex flex-col h-full fade-in-up">

                <!-- Table Header -->
                <div
                    class="flex flex-col sm:flex-row justify-between items-start sm:items-end border-b border-slate-100 pb-4 mb-4 gap-3">
                    <div>
                        <h2 class="text-lg md:text-xl font-bold text-slate-800 flex items-center gap-2" id="tableTitle">
                            <i class="fa-solid fa-list-check text-blue-500"></i> รายการวัสดุ
                        </h2>
                        <p class="text-xs md:text-sm text-slate-400 mt-1 pl-7" id="itemCount">รอเลือกโครงการ...</p>
                    </div>

                    <div class="flex items-center gap-2 md:gap-3 w-full sm:w-auto justify-end flex-wrap">

                        <!-- Export PDF Button -->
                        <button onclick="window.exportToPDF()"
                            class="bg-red-500 hover:bg-red-600 text-white px-3 md:px-4 py-2 rounded-xl shadow-sm text-xs md:text-sm font-medium transition-all flex items-center gap-1.5 btn-lift">
                            <i class="fa-solid fa-file-pdf text-base"></i>
                            <span class="hidden md:inline">Export PDF</span>
                        </button>

                        <!-- Export Excel Button -->
                        <button onclick="window.exportToExcel()"
                            class="bg-emerald-600 hover:bg-emerald-700 text-white px-3 md:px-4 py-2 rounded-xl shadow-sm text-xs md:text-sm font-medium transition-all flex items-center gap-1.5 btn-lift">
                            <i class="fa-solid fa-file-excel text-base"></i>
                            <span class="hidden md:inline">Export Excel</span>
                        </button>

                        <!-- Calculator Button -->
                        <button onclick="window.openCalculator()"
                            class="bg-violet-600 hover:bg-violet-700 text-white px-3 md:px-4 py-2 rounded-xl shadow-sm text-xs md:text-sm font-medium transition-all flex items-center gap-1.5 btn-lift">
                            <i class="fa-solid fa-calculator text-base"></i>
                            <span class="hidden md:inline">คำนวณราคา</span>
                        </button>

                        <!-- Item Count Box -->
                        <div
                            class="hidden sm:block text-right bg-white/80 px-3 py-2 rounded-xl border border-slate-200/80 shadow-sm min-w-[80px]">
                            <p class="text-[9px] text-slate-400 uppercase font-bold tracking-wider mb-0.5">จำนวนรายการ
                            </p>
                            <h3 class="text-lg font-bold text-slate-600 flex justify-end items-baseline gap-1">
                                <span id="summaryItemCount">0</span>
                                <span class="text-[10px] font-normal text-slate-400">รายการ</span>
                            </h3>
                        </div>

                        <!-- Grand Total Box -->
                        <div
                            class="text-right bg-gradient-to-br from-blue-50 to-orange-50 px-4 md:px-6 py-2.5 md:py-3 rounded-2xl border border-blue-100/80 shadow-sm flex-grow sm:flex-grow-0">
                            <p
                                class="text-[9px] md:text-[10px] text-slate-500 uppercase font-extrabold tracking-widest mb-0.5">
                                ยอดรวมสุทธิ</p>
                            <h3 class="text-2xl md:text-3xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-orange-500"
                                id="grandTotalDisplay">฿0.00</h3>
                        </div>
                    </div>
                </div>

                <!-- Filter Bar -->
                <div id="filterBar" class="flex flex-wrap items-center gap-2 mb-4 px-1">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider mr-1"><i
                            class="fa-solid fa-filter mr-1"></i>Filter:</span>
                    <button onclick="window.filterItems('all')" data-filter="all"
                        class="filter-pill active-filter px-3 py-1.5 rounded-full text-xs font-bold transition-all duration-200 shadow-sm border">
                        ทั้งหมด
                    </button>
                    <button onclick="window.filterItems('pending')" data-filter="pending"
                        class="filter-pill px-3 py-1.5 rounded-full text-xs font-bold transition-all duration-200 shadow-sm border">
                        ⏳ รอดำเนินการ
                    </button>
                    <button onclick="window.filterItems('ordered')" data-filter="ordered"
                        class="filter-pill px-3 py-1.5 rounded-full text-xs font-bold transition-all duration-200 shadow-sm border">
                        🚚 สั่งซื้อแล้ว
                    </button>
                    <button onclick="window.filterItems('received')" data-filter="received"
                        class="filter-pill px-3 py-1.5 rounded-full text-xs font-bold transition-all duration-200 shadow-sm border">
                        ✅ ได้รับของแล้ว
                    </button>
                    <button onclick="window.filterItems('cancelled')" data-filter="cancelled"
                        class="filter-pill px-3 py-1.5 rounded-full text-xs font-bold transition-all duration-200 shadow-sm border">
                        ❌ ยกเลิก
                    </button>
                </div>

                <!-- Table Content -->
                <div
                    class="overflow-x-auto rounded-xl md:border md:border-slate-200/80 flex-grow bg-transparent md:bg-white table-container">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr
                                class="bg-gradient-to-r from-slate-50 to-slate-100/50 text-xs text-slate-500 uppercase tracking-wider sticky top-0 z-10 font-semibold border-b border-slate-200">
                                <th class="p-3 w-14 text-center whitespace-nowrap">รูป</th>
                                <th class="p-3 md:p-4">ชื่อรายการ / รายละเอียด</th>
                                <th class="p-3 md:p-4 text-center w-20 whitespace-nowrap">จำนวน</th>
                                <th class="p-3 md:p-4 text-right min-w-[100px] whitespace-nowrap">ราคา/หน่วย</th>
                                <th class="p-3 md:p-4 text-right min-w-[100px] whitespace-nowrap">รวม</th>
                                <th class="p-3 md:p-4 text-center min-w-[120px] whitespace-nowrap">สถานะ</th>
                                <th class="p-3 md:p-4 text-center w-24 whitespace-nowrap">จัดการ</th>
                                <th class="p-3 md:p-4 min-w-[130px] max-w-[200px]">หมายเหตุ</th>
                            </tr>
                        </thead>
                        <tbody id="bomList" class="text-sm divide-y divide-slate-100">
                            <!-- Empty State -->
                            <tr>
                                <td colspan="8" class="text-center py-24 text-slate-300 bg-slate-50/30">
                                    <div class="flex flex-col items-center">
                                        <div class="bg-slate-100 p-4 rounded-full mb-3">
                                            <i class="fa-solid fa-arrow-left text-2xl text-slate-400 animate-pulse"></i>
                                        </div>
                                        <p class="font-medium">เลือกโครงการด้านซ้าย</p>
                                        <p class="text-xs mt-1">เพื่อเริ่มจัดการรายการวัสดุ</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Table Footer Info -->
                <div
                    class="mt-4 md:mt-6 pt-4 md:pt-6 border-t border-slate-100 text-[10px] md:text-xs text-slate-400 flex flex-col md:flex-row justify-between items-center gap-3">
                    <div class="flex items-center gap-2 opacity-70">
                        <span class="flex items-center gap-1.5"><i class="fa-solid fa-shield-halved text-green-500"></i>
                            Secured by Firebase</span>
                        <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                        <span>Mentra BOM v3.0</span>
                    </div>

                    <div class="group flex items-center gap-2">
                        <span class="text-slate-400">พัฒนาระบบโดย</span>
                        <a href="https://keexlab-th.github.io/" target="_blank"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-slate-200 rounded-full shadow-sm hover:shadow-md hover:border-orange-200 hover:bg-orange-50 transition-all duration-300 no-underline">
                            <span
                                class="w-2 h-2 rounded-full bg-gradient-to-r from-blue-500 to-orange-500 animate-pulse"></span>
                            <span class="font-bold text-slate-700 group-hover:text-orange-600 transition-colors">ธนภูมิ
                                แดงประดับ</span>
                            <i
                                class="fa-solid fa-arrow-up-right-from-square text-[10px] text-slate-300 group-hover:text-orange-400"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- JAVASCRIPT LOGIC -->
    <!-- ========================================== -->
    <script type="module">
        import { initializeApp } from "https://www.gstatic.com/firebasejs/11.6.1/firebase-app.js";
        import { getFirestore, collection, addDoc, onSnapshot, deleteDoc, updateDoc, doc, query, orderBy, where, serverTimestamp, getDocs } from "https://www.gstatic.com/firebasejs/11.6.1/firebase-firestore.js";
        import { getAuth, signInAnonymously, signInWithCustomToken, onAuthStateChanged, signOut } from "https://www.gstatic.com/firebasejs/11.6.1/firebase-auth.js";

        // ----------------------------------------------------
        // Security Utilities
        // ----------------------------------------------------
        const ALLOWED_ROLES = ['admin', 'material', 'purchasing', 'viewer'];

        const escapeHtml = (str) => {
            if (str == null) return '';
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;');
        };

        const sanitizeUrl = (url) => {
            if (!url) return '';
            const trimmed = String(url).trim();
            if (/^https?:\/\//i.test(trimmed)) return trimmed;
            return '';
        };

        // ----------------------------------------------------
        // Configuration: Use Environment Variables OR Fallback
        // ----------------------------------------------------
        let firebaseConfig;
        let isCanvasEnv = false;

        try {
            if (typeof __firebase_config !== 'undefined') {
                firebaseConfig = JSON.parse(__firebase_config);
                isCanvasEnv = true;
            }
        } catch (e) { console.warn("Using fallback config"); }

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

        // --- Paths ---
        const getProjectsRef = () => isCanvasEnv ? collection(db, 'artifacts', appId, 'public', 'data', 'bom_projects') : collection(db, 'bom_projects');
        const getItemsRef = () => isCanvasEnv ? collection(db, 'artifacts', appId, 'public', 'data', 'bom_items') : collection(db, 'bom_items');
        const getLogsRef = () => isCanvasEnv ? collection(db, 'artifacts', appId, 'public', 'data', 'bom_logs') : collection(db, 'bom_logs');

        let currentUser = null;
        let isAdmin = false;
        let currentProjectId = null;
        let currentProjectName = "";
        let unsubscribeItems = null;
        let allProjectsData = {};
        let allItemsCache = {};
        let currentItemsList = [];
        let role = '';
        let currentFilter = 'all';
        let importedImageData = null; // เก็บ base64 รูปที่ดึงมาจากรายการเดิม

        // ----------------------------------------------------
        // 1. ระบบเช็คสิทธิ์ (Security Gate) & Config UI
        // ----------------------------------------------------
        const checkPermission = () => {
            const roleToken = localStorage.getItem('mentra_role');
            const badge = document.getElementById('roleBadge');

            if (!roleToken || !ALLOWED_ROLES.includes(roleToken)) {
                localStorage.removeItem('mentra_role');
                localStorage.removeItem('mentra_user');
                window.location.href = 'index.php';
                return;
            }

            role = roleToken; // เก็บ Role ไว้ใช้งาน
            isAdmin = (role === 'admin');

            // อัปเดต CSS Classes ตาม Role
            document.body.className = `role-${role}`;

            // แสดงสถานะบน Badge (ทั้ง Desktop และ Mobile)
            if (badge) {
                badge.innerText = role.toUpperCase();
                badge.className = `text-[10px] uppercase font-bold px-2.5 py-1 rounded-full border ${isAdmin ? 'bg-blue-600 text-white border-blue-500' : 'bg-white/10 text-slate-300 border-white/10'}`;
            }
            const badgeMobile = document.getElementById('roleBadgeMobile');
            if (badgeMobile) {
                badgeMobile.innerText = role.toUpperCase();
                badgeMobile.className = badge?.className || '';
            }

            // *** FORM VISIBILITY LOGIC (ฟอร์มซ้าย) ***
            // Material & Admin: เห็นฟอร์มและใช้งานได้ (ปลดล็อค)
            if (role === 'admin' || role === 'material') {
                document.getElementById('formPanel').classList.remove('pointer-events-none', 'opacity-50');
                document.body.classList.remove('lock-form');
                const overlay = document.getElementById('guestOverlay');
                if (overlay) { overlay.classList.add('hidden'); overlay.style.display = 'none'; }
            }
            // Purchasing & Viewer: ซ่อน/ล็อคฟอร์ม (และมี Overlay บัง)
            else {
                document.getElementById('formPanel').classList.add('pointer-events-none', 'opacity-50');
                document.body.classList.add('lock-form');
                const overlay = document.getElementById('guestOverlay');
                if (overlay) { overlay.classList.remove('hidden'); overlay.style.display = 'flex'; }
            }

            if (isAdmin) document.body.classList.add('is-admin');
            else document.body.classList.add('is-guest');
        };

        checkPermission();

        window.handleLogout = () => {
            Swal.fire({
                title: 'ออกจากระบบ?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                confirmButtonText: 'Logout'
            }).then((result) => {
                if (result.isConfirmed) {
                    localStorage.removeItem('mentra_role');
                    localStorage.removeItem('mentra_user');
                    signOut(auth).then(() => window.location.href = 'index.php');
                }
            });
        };

        // Image Preview Popup — Event Delegation (safe for base64 src)
        document.addEventListener('click', (e) => {
            const img = e.target.closest('[data-preview]');
            if (!img) return;
            Swal.fire({
                title: img.dataset.name || 'ภาพรายการ',
                imageUrl: img.src,
                imageAlt: img.dataset.name || 'Item Image',
                imageWidth: 'auto',
                width: Math.min(window.innerWidth * 0.9, 600),
                showConfirmButton: false,
                showCloseButton: true,
                customClass: { image: 'rounded-xl', popup: 'rounded-2xl' }
            });
        });

        // ----------------------------------------------------
        // Authentication (Connect Firebase)
        // ----------------------------------------------------
        const initAuth = async () => {
            try {
                if (typeof __initial_auth_token !== 'undefined' && __initial_auth_token) await signInWithCustomToken(auth, __initial_auth_token);
                else await signInAnonymously(auth);
            } catch (error) { console.error("Auth Failed:", error); loadProjects(); }
        };

        onAuthStateChanged(auth, (user) => {
            if (user) {
                currentUser = user;
                // ปิด Loading Screen (แก้หมุนค้าง)
                const loader = document.getElementById('mainLoading');
                if (loader) {
                    loader.style.opacity = '0';
                    setTimeout(() => loader.style.display = 'none', 400);
                }
                loadProjects();
            } else initAuth();
        });

        // Timeout fallback — ลดเหลือ 1800ms เพื่อไม่ให้ loading ค้างนาน
        setTimeout(() => {
            const loader = document.getElementById('mainLoading');
            if (loader && loader.style.display !== 'none') {
                loader.style.opacity = '0';
                setTimeout(() => loader.style.display = 'none', 400);
            }
        }, 1800);

        initAuth();

        // --- Helper: Status Badge ---
        const getStatusBadge = (status) => {
            switch (status) {
                case 'ordered': return '<span class="inline-block px-3 py-1 rounded-full text-[10px] font-bold bg-blue-100 text-blue-700 border border-blue-200 whitespace-nowrap shadow-sm"><i class="fa-solid fa-truck mr-1"></i> สั่งแล้ว</span>';
                case 'received': return '<span class="inline-block px-3 py-1 rounded-full text-[10px] font-bold bg-green-100 text-green-700 border border-green-200 whitespace-nowrap shadow-sm"><i class="fa-solid fa-check mr-1"></i> ได้ของแล้ว</span>';
                case 'cancelled': return '<span class="inline-block px-3 py-1 rounded-full text-[10px] font-bold bg-red-100 text-red-700 border border-red-200 whitespace-nowrap shadow-sm"><i class="fa-solid fa-xmark mr-1"></i> ยกเลิก</span>';
                default: return '<span class="inline-block px-3 py-1 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-200 whitespace-nowrap shadow-sm"><i class="fa-solid fa-hourglass mr-1"></i> รอดำเนินการ</span>';
            }
        };

        // --- Helper: Status Text for PDF ---
        const getStatusTextPDF = (status) => {
            switch (status) {
                case 'ordered': return 'สั่งแล้ว';
                case 'received': return 'ได้รับแล้ว';
                case 'cancelled': return 'ยกเลิก';
                default: return 'รอดำเนินการ';
            }
        };

        // ----- localStorage cache helpers -----
        const PROJ_CACHE_KEY = 'mentra_projects_cache';
        const PROJ_CACHE_TTL = 5 * 60 * 1000; // 5 นาที

        const saveProjectsCache = (projects) => {
            try {
                localStorage.setItem(PROJ_CACHE_KEY, JSON.stringify({ ts: Date.now(), data: projects }));
            } catch (e) { /* quota full — ข้ามได้ */ }
        };

        const getProjectsCache = () => {
            try {
                const raw = localStorage.getItem(PROJ_CACHE_KEY);
                if (!raw) return null;
                const { ts, data } = JSON.parse(raw);
                return (Date.now() - ts < PROJ_CACHE_TTL) ? data : null;
            } catch (e) { return null; }
        };

        const renderProjectDropdown = (projects, preserveSelected = false) => {
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
                if (!preserveSelected) loadItems(currentProjectId);
            } else if (currentProjectId && !allProjectsData[currentProjectId]) {
                window.changeProject("");
            }
        };

        const loadProjects = () => {
            // แสดง cache ทันทีถ้ามี (ก่อน Firestore ตอบกลับ)
            const cached = getProjectsCache();
            if (cached && cached.length > 0) {
                allProjectsData = {};
                renderProjectDropdown(cached, false);
            }

            const q = query(getProjectsRef());
            onSnapshot(q, (snapshot) => {
                let projects = [];
                snapshot.forEach(doc => projects.push({ id: doc.id, ...doc.data() }));
                projects.sort((a, b) => (b.createdAt?.seconds || 0) - (a.createdAt?.seconds || 0));

                saveProjectsCache(projects);
                allProjectsData = {};
                renderProjectDropdown(projects, !!cached);
            }, (error) => console.error("Error loading projects:", error));
        }

        const updateProjectHeaderUI = (project) => {
            currentProjectName = project.name; // Fix: set ชื่อโครงการสำหรับ PDF Export
            document.getElementById('headerProjectName').innerText = project.name;
            document.getElementById('tableTitle').innerHTML = `<span class="text-blue-600">${escapeHtml(project.name)}</span>`;
            document.getElementById('projectCoverTitle').innerText = project.name;
            const cover = document.getElementById('projectCoverContainer');
            if (project.coverImage) {
                document.getElementById('projectCoverImg').src = project.coverImage;
                cover.classList.remove('hidden');
            } else cover.classList.add('hidden');

            // อัปเดตการแสดงผลฟอร์มตามสิทธิ์อีกครั้ง (เพื่อความชัวร์)
            const formPanel = document.getElementById('formPanel');
            document.getElementById('noProjectAlert').style.display = 'none';

            if (role === 'admin' || role === 'material') {
                formPanel.classList.remove('opacity-50', 'pointer-events-none');
                document.body.classList.remove('lock-form');
            } else {
                formPanel.classList.add('opacity-50', 'pointer-events-none');
                document.body.classList.add('lock-form');
            }
        }

        window.createNewProject = async () => {
            if (!isAdmin) return;
            const { value: result } = await Swal.fire({ title: 'สร้างโครงการ', html: `<div class="text-left text-sm space-y-2"><label class="font-bold">ชื่อโครงการ</label><input id="sw-name" class="swal2-input w-full m-0" placeholder="ชื่อโครงการ..."><label class="font-bold block mt-3">รูปปก</label><input type="file" id="sw-file" class="swal2-file w-full m-0" accept="image/*"></div>`, showCancelButton: true, preConfirm: () => ({ name: document.getElementById('sw-name').value, file: document.getElementById('sw-file').files[0] }) });
            if (result && result.name) {
                Swal.showLoading();
                let img = result.file ? await resizeImage(result.file, 800) : null;
                const docRef = await addDoc(getProjectsRef(), { name: result.name, coverImage: img, createdAt: serverTimestamp(), createdBy: currentUser?.uid });
                Swal.fire('สำเร็จ', '', 'success');
                window.changeProject(docRef.id);
            }
        }
        window.editCurrentProject = async () => {
            if (!isAdmin || !currentProjectId) return;
            const p = allProjectsData[currentProjectId];
            const { value: result } = await Swal.fire({ title: 'แก้ไขโครงการ', html: `<div class="text-left text-sm space-y-2"><label class="font-bold">ชื่อโครงการ</label><input id="sw-name" class="swal2-input w-full m-0" value="${p.name}"><label class="font-bold block mt-3">เปลี่ยนรูปปก</label><input type="file" id="sw-file" class="swal2-file w-full m-0" accept="image/*"></div>`, showCancelButton: true, preConfirm: () => ({ name: document.getElementById('sw-name').value, file: document.getElementById('sw-file').files[0] }) });
            if (result && result.name) {
                Swal.showLoading();
                let upData = { name: result.name };
                if (result.file) upData.coverImage = await resizeImage(result.file, 800);
                await updateDoc(doc(getProjectsRef(), currentProjectId), upData);
                Swal.fire('แก้ไขเรียบร้อย', '', 'success');
            }
        }
        window.deleteCurrentProject = async () => {
            if (!isAdmin || !currentProjectId) return;
            if ((await Swal.fire({ title: 'ลบโครงการนี้?', text: 'ข้อมูลวัสดุทั้งหมดในโครงการนี้จะถูกลบ!', icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33', confirmButtonText: 'ยืนยันลบ' })).isConfirmed) {
                Swal.showLoading();
                const q = query(getItemsRef(), where("projectId", "==", currentProjectId));
                const snap = await getDocs(q);
                await Promise.all(snap.docs.map(d => deleteDoc(doc(getItemsRef(), d.id))));
                await deleteDoc(doc(getProjectsRef(), currentProjectId));
                Swal.fire('ลบสำเร็จ!', '', 'success');
                window.changeProject("");
            }
        }

        window.changeProject = (projectId) => {
            if (!projectId) {
                currentProjectId = null;
                document.getElementById('projectCoverContainer').classList.add('hidden');
                document.getElementById('formPanel').classList.add('opacity-50', 'pointer-events-none');
                document.getElementById('noProjectAlert').style.display = 'flex';
                document.getElementById('bomList').innerHTML = '<tr><td colspan="8" class="text-center py-20 text-slate-300">กรุณาเลือกโครงการด้านซ้าย</td></tr>';
                document.getElementById('grandTotalDisplay').innerText = '฿0.00';
                document.getElementById('headerProjectName').innerText = "ยังไม่ได้เลือกโครงการ";
                updateNavLinks(null);
                return;
            }
            currentProjectId = projectId;

            // Update URL parameters without reloading
            const url = new URL(window.location);
            url.searchParams.set('project', projectId);
            window.history.replaceState({}, '', url);
            updateNavLinks(projectId);

            if (allProjectsData[projectId]) updateProjectHeaderUI(allProjectsData[projectId]);
            loadItems(projectId);
        }

        const updateNavLinks = (projectId) => {
            const suffix = projectId ? `?project=${projectId}` : '';
            const links = document.querySelectorAll('a[href^="bom.php"], a[href^="logs.php"], a[href^="drawings.php"], a[href^="calculator.php"]');
            links.forEach(link => {
                const baseHref = link.getAttribute('href').split('?')[0];
                link.setAttribute('href', baseHref + suffix);
            });
        };

        const loadItems = (projectId) => {
            if (unsubscribeItems) unsubscribeItems();
            document.getElementById('bomList').innerHTML = '<tr><td colspan="8" class="text-center py-20"><i class="fa-solid fa-spinner fa-spin text-2xl text-blue-500"></i></td></tr>';

            const q = query(getItemsRef(), where("projectId", "==", projectId));
            unsubscribeItems = onSnapshot(q, snap => {
                let total = 0, list = [];
                snap.forEach(d => { const i = { id: d.id, ...d.data() }; list.push(i); total += i.total; allItemsCache[d.id] = i; });
                list.sort((a, b) => (b.createdAt?.seconds || 0) - (a.createdAt?.seconds || 0));
                currentItemsList = list;
                renderItems();
                document.getElementById('grandTotalDisplay').innerText = '฿' + total.toLocaleString(undefined, { minimumFractionDigits: 2 });
                document.getElementById('itemCount').innerText = `${list.length} รายการ`;
                document.getElementById('summaryItemCount').innerText = list.length;
            });
        }

        // *** renderItems: Render table rows with filter support ***
        const renderItems = () => {
            const filtered = currentFilter === 'all'
                ? currentItemsList
                : currentItemsList.filter(i => i.status === currentFilter);

            let h = '';
            filtered.forEach(i => {
                const img = i.image
                    ? `<img src="${i.image}" data-preview data-name="${escapeHtml(i.name)}" class="w-10 h-10 object-cover rounded-lg shadow-sm cursor-pointer border border-slate-100 hover:shadow-md hover:border-blue-300 transition-all" title="คลิกดูภาพขยาย">`
                    : '<span class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-slate-100 text-slate-300"><i class="fa-regular fa-image text-lg"></i></span>';
                const safeLink = sanitizeUrl(i.link);
                const linkTag = safeLink ? `<a href="${escapeHtml(safeLink)}" target="_blank" rel="noopener noreferrer" class="text-blue-500 text-[10px] block mt-1 hover:underline"><i class="fa-solid fa-link"></i> Link</a>` : ``;

                let manageBtn = '';
                const safeId = escapeHtml(i.id);

                if (isAdmin) {
                    manageBtn = `
                        <div class="flex justify-center gap-1">
                            <button onclick="window.editItem('${safeId}')" class="text-slate-400 hover:text-yellow-500 w-7 h-7 flex items-center justify-center rounded-full transition-all" title="แก้ไข"><i class="fa-solid fa-pen"></i></button>
                            <button onclick="window.deleteItem('${safeId}')" class="text-slate-400 hover:text-red-500 w-7 h-7 flex items-center justify-center rounded-full transition-all" title="ลบ"><i class="fa-solid fa-trash-can"></i></button>
                        </div>
                    `;
                } else if (role === 'purchasing') {
                    manageBtn = `
                        <div class="flex justify-center">
                            <button onclick="window.updateStatus('${safeId}')" class="text-blue-600 bg-blue-50 hover:bg-blue-100 px-2 py-1 rounded text-xs font-bold shadow-sm border border-blue-200">
                                <i class="fa-solid fa-rotate"></i> สถานะ
                            </button>
                        </div>
                    `;
                } else {
                    manageBtn = `<span class="text-slate-300 text-xs">-</span>`;
                }

                const remarkCell = i.remark
                    ? `<td class="p-3 align-middle" style="max-width:180px"><span class="block text-xs italic text-slate-500 truncate" title="${escapeHtml(i.remark)}">📝 ${escapeHtml(i.remark)}</span></td>`
                    : `<td class="p-3 align-middle text-slate-300 text-xs">-</td>`;
                h += `<tr class="hover:bg-indigo-50/20 transition-colors group border-b border-slate-100 last:border-0">
                    <td class="p-3 text-center align-middle">${img}</td>
                    <td class="p-3 align-middle">
                        <div class="font-bold text-sm text-slate-800">${escapeHtml(i.name)} ${linkTag}</div>
                        <div class="text-xs text-slate-400 mt-0.5 line-clamp-2">${escapeHtml(i.details) || ''}</div>
                    </td>
                    <td class="p-3 text-center align-middle font-bold text-slate-700">×${i.qty}</td>
                    <td class="p-3 text-right align-middle text-xs text-slate-500">฿${i.price ? i.price.toLocaleString(undefined, { minimumFractionDigits: 2 }) : '0.00'}</td>
                    <td class="p-3 text-right align-middle font-extrabold text-blue-600 text-sm">฿${i.total.toLocaleString(undefined, { minimumFractionDigits: 2 })}</td>
                    <td class="p-3 align-middle text-center">${getStatusBadge(i.status)}</td>
                    <td class="p-3 text-center align-middle">${manageBtn}</td>
                    ${remarkCell}
                </tr>`;
            });

            const emptyMsg = currentFilter === 'all'
                ? 'ยังไม่มีรายการ'
                : `ไม่พบรายการสถานะ "${currentFilter === 'pending' ? 'รอดำเนินการ' : currentFilter === 'ordered' ? 'สั่งซื้อแล้ว' : currentFilter === 'received' ? 'ได้รับของแล้ว' : 'ยกเลิก'}"`;
            document.getElementById('bomList').innerHTML = h || `<tr><td colspan="8" class="text-center py-16 text-slate-400">${emptyMsg}</td></tr>`;
        };

        // *** filterItems: Toggle active filter and re-render ***
        window.filterItems = (status) => {
            currentFilter = status;
            // Update active pill UI
            document.querySelectorAll('.filter-pill').forEach(btn => {
                btn.classList.remove('active-filter');
                if (btn.dataset.filter === status) btn.classList.add('active-filter');
            });
            renderItems();
        };

        // *** openCalculator: Navigate to calculator page ***
        window.openCalculator = () => {
            if (!currentProjectId) {
                Swal.fire('เตือน', 'กรุณาเลือกโครงการก่อน', 'warning');
                return;
            }
            window.open(`calculator.php?project=${encodeURIComponent(currentProjectId)}&name=${encodeURIComponent(currentProjectName)}`, '_blank');
        };

        // *** 3. Add Item: Admin & Material Only ***
        window.addItem = async (e) => {
            e.preventDefault();
            if (role !== 'admin' && role !== 'material') return; // Guard

            if (!currentProjectId) { Swal.fire('เตือน', 'เลือกโครงการก่อน', 'warning'); return; }

            const btn = document.getElementById('submitBtn'); btn.disabled = true;
            try {
                const name = document.getElementById('itemName').value.trim();
                const price = parseFloat(document.getElementById('itemPrice').value) || 0;
                const qty = parseFloat(document.getElementById('itemQty').value) || 1;
                const rawLink = document.getElementById('itemLink').value.trim();
                const data = {
                    projectId: currentProjectId, name, price, qty,
                    total: price * qty,
                    details: document.getElementById('itemDetails').value.trim(),
                    remark: document.getElementById('itemRemark').value.trim(),
                    status: document.getElementById('itemStatus').value,
                    link: sanitizeUrl(rawLink),
                    createdAt: serverTimestamp(), updatedBy: currentUser?.uid
                };
                const file = document.getElementById('itemImage').files[0];
                if (file) {
                    data.image = await resizeImage(file, 500);
                } else if (importedImageData) {
                    data.image = importedImageData;
                }

                await addDoc(getItemsRef(), data);
                try {
                    await addDoc(getLogsRef(), {
                        projectId: currentProjectId,
                        projectName: currentProjectName,
                        itemName: name,
                        action: 'เพิ่มรายการ',
                        actorRole: localStorage.getItem('mentra_role') || 'Unknown',
                        details: `เพิ่มจำนวน ${qty} ชิ้น (ราคา ฿${parseFloat(price).toLocaleString('en-US')})`,
                        actorName: JSON.parse(localStorage.getItem('mentra_user'))?.name || 'Unknown',
                        timestamp: serverTimestamp()
                    });
                } catch (err) { console.error('Log error:', err); }
                document.getElementById('bomForm').reset();
                window.clearImportMode();
                Swal.mixin({ toast: true, position: 'bottom-end', showConfirmButton: false, timer: 3000 }).fire({ icon: 'success', title: 'บันทึกเรียบร้อย' });
            } catch (error) { Swal.fire("Error", error.message, "error"); }
            finally { btn.disabled = false; }
        }

        // *** ยกเลิกโหมดดึงรายการเดิม ***
        window.clearImportMode = () => {
            importedImageData = null;
            document.getElementById('importModeBanner').classList.add('hidden');
            // Unlock all fields
            ['itemName', 'itemPrice', 'itemStatus', 'itemRemark', 'itemLink'].forEach(id => {
                const el = document.getElementById(id);
                el.disabled = false;
                el.classList.remove('opacity-50', 'cursor-not-allowed');
            });
            document.getElementById('itemImage').closest('div').classList.remove('hidden');
        };

        // *** IMPORT EXISTING ITEM: ดึงรายการเดิมจากโครงการอื่น ***
        window.importExistingItem = async () => {
            if (role !== 'admin' && role !== 'material') return;
            if (!currentProjectId) { Swal.fire('เตือน', 'เลือกโครงการก่อน', 'warning'); return; }

            Swal.fire({ title: 'กำลังโหลดรายการ...', didOpen: () => Swal.showLoading(), allowOutsideClick: false });

            try {
                const snap = await getDocs(query(getItemsRef()));
                const items = [];
                snap.forEach(d => {
                    const data = { id: d.id, ...d.data() };
                    items.push(data);
                });

                if (!items.length) {
                    Swal.fire('ไม่มีรายการ', 'ยังไม่มีรายการวัสดุในระบบ', 'info');
                    return;
                }

                // สร้าง lookup ชื่อโครงการ
                const projNames = {};
                Object.entries(allProjectsData).forEach(([id, p]) => { projNames[id] = p.name; });

                const buildRows = (filter) => {
                    const filtered = filter
                        ? items.filter(i => (i.name || '').toLowerCase().includes(filter.toLowerCase()) || (i.details || '').toLowerCase().includes(filter.toLowerCase()))
                        : items;
                    if (!filtered.length) return '<tr><td colspan="4" style="text-align:center;padding:20px;color:#94a3b8;">ไม่พบรายการที่ค้นหา</td></tr>';
                    return filtered.map(i => {
                        const pName = escapeHtml(projNames[i.projectId] || 'ไม่ทราบ');
                        return `<tr style="border-bottom:1px solid #f1f5f9;cursor:pointer;" class="import-row" data-id="${escapeHtml(i.id)}" onmouseover="this.style.background='#eff6ff'" onmouseout="this.style.background=''">
                            <td style="padding:8px 10px;font-weight:600;font-size:13px;">${escapeHtml(i.name)}</td>
                            <td style="padding:8px 10px;font-size:11px;color:#64748b;max-width:120px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">${escapeHtml(i.details)}</td>
                            <td style="padding:8px 10px;font-size:11px;color:#94a3b8;"><i class="fa-regular fa-folder-open" style="margin-right:3px"></i>${pName}</td>
                            <td style="padding:8px 10px;text-align:right;font-weight:700;color:#2563eb;font-size:13px;">฿${(i.price || 0).toLocaleString()}</td>
                        </tr>`;
                    }).join('');
                };

                await Swal.fire({
                    title: '<i class="fa-solid fa-rotate-right" style="color:#10b981;margin-right:6px;"></i> ดึงรายการเดิม',
                    width: '650px',
                    html: `
                        <div style="text-align:left;">
                            <input id="importSearch" placeholder="🔍 ค้นหาชื่อวัสดุ..." style="width:100%;padding:10px 14px;border:1px solid #e2e8f0;border-radius:10px;font-size:13px;outline:none;margin-bottom:12px;font-family:Prompt;" oninput="window._filterImportItems(this.value)">
                            <div style="max-height:350px;overflow-y:auto;border:1px solid #e2e8f0;border-radius:10px;">
                                <table style="width:100%;border-collapse:collapse;font-family:Prompt;">
                                    <thead><tr style="background:#f8fafc;position:sticky;top:0;z-index:1;">
                                        <th style="padding:8px 10px;text-align:left;font-size:10px;text-transform:uppercase;color:#94a3b8;font-weight:700;letter-spacing:0.05em;">ชื่อรายการ</th>
                                        <th style="padding:8px 10px;text-align:left;font-size:10px;text-transform:uppercase;color:#94a3b8;font-weight:700;letter-spacing:0.05em;">รายละเอียด</th>
                                        <th style="padding:8px 10px;text-align:left;font-size:10px;text-transform:uppercase;color:#94a3b8;font-weight:700;letter-spacing:0.05em;">โครงการ</th>
                                        <th style="padding:8px 10px;text-align:right;font-size:10px;text-transform:uppercase;color:#94a3b8;font-weight:700;letter-spacing:0.05em;">ราคา/หน่วย</th>
                                    </tr></thead>
                                    <tbody id="importTableBody">${buildRows('')}</tbody>
                                </table>
                            </div>
                            <p style="font-size:10px;color:#94a3b8;margin-top:8px;text-align:center;"><i class="fa-solid fa-hand-pointer" style="margin-right:4px;"></i>คลิกที่รายการเพื่อเติมข้อมูลลงฟอร์ม</p>
                        </div>
                    `,
                    showConfirmButton: false,
                    showCancelButton: true,
                    cancelButtonText: 'ปิด',
                    didOpen: () => {
                        window._filterImportItems = (val) => {
                            document.getElementById('importTableBody').innerHTML = buildRows(val);
                            // Re-attach click listeners
                            document.querySelectorAll('.import-row').forEach(row => {
                                row.onclick = () => selectImportItem(row.dataset.id);
                            });
                        };
                        const selectImportItem = (itemId) => {
                            const item = items.find(i => i.id === itemId);
                            if (!item) return;
                            // Pre-fill form
                            document.getElementById('itemName').value = item.name || '';
                            document.getElementById('itemDetails').value = item.details || '';
                            document.getElementById('itemPrice').value = item.price || '';
                            document.getElementById('itemQty').value = 1;
                            document.getElementById('itemStatus').value = item.status || 'pending';
                            document.getElementById('itemRemark').value = item.remark || '';
                            document.getElementById('itemLink').value = item.link || '';

                            // เก็บรูปภาพเดิม
                            importedImageData = item.image || null;

                            // Lock fields — แก้ได้แค่จำนวน + รายละเอียด
                            ['itemName', 'itemPrice', 'itemStatus', 'itemRemark', 'itemLink'].forEach(id => {
                                const el = document.getElementById(id);
                                el.disabled = true;
                                el.classList.add('opacity-50', 'cursor-not-allowed');
                            });
                            // ซ่อน file input เพราะใช้รูปเดิม
                            document.getElementById('itemImage').closest('div').classList.add('hidden');

                            // แสดง Import Mode Banner
                            const banner = document.getElementById('importModeBanner');
                            banner.classList.remove('hidden');
                            document.getElementById('importModeName').textContent = item.name || '';
                            const imgEl = document.getElementById('importModeImg');
                            if (item.image) { imgEl.src = item.image; imgEl.classList.remove('hidden'); }
                            else { imgEl.classList.add('hidden'); }

                            Swal.close();
                            // Focus ช่องจำนวน
                            setTimeout(() => document.getElementById('itemQty')?.focus(), 200);
                        };
                        // Attach click listeners
                        document.querySelectorAll('.import-row').forEach(row => {
                            row.onclick = () => selectImportItem(row.dataset.id);
                        });
                        // Focus search
                        setTimeout(() => document.getElementById('importSearch')?.focus(), 100);
                    }
                });
            } catch (e) {
                console.error('Import error:', e);
                Swal.fire('Error', e.message, 'error');
            }
        };

        // *** 4. Update Status: Admin & Purchasing Only (Visual Card UI) ***
        window.updateStatus = async (itemId) => {
            if (role !== 'admin' && role !== 'purchasing') return;
            const item = allItemsCache[itemId];
            const currentStatus = item?.status || 'pending';

            const { value: status } = await Swal.fire({
                title: '<i class="fa-solid fa-rotate" style="color:#2563eb;margin-right:6px;"></i> อัปเดตสถานะ',
                html: `
                    <p style="font-size:12px;color:#94a3b8;margin-bottom:16px;">เลือกสถานะสำหรับ <b style="color:#1e293b;">${escapeHtml(item?.name || '')}</b></p>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;" id="statusCardGrid">
                        <div class="status-card ${currentStatus === 'pending' ? 'selected' : ''}" data-status="pending" onclick="window._selectStatusCard(this)">
                            <div style="font-size:28px;margin-bottom:6px;">⏳</div>
                            <div style="font-size:12px;font-weight:700;color:#475569;">รอดำเนินการ</div>
                            <div style="font-size:10px;color:#94a3b8;margin-top:2px;">Pending</div>
                        </div>
                        <div class="status-card ${currentStatus === 'ordered' ? 'selected' : ''}" data-status="ordered" onclick="window._selectStatusCard(this)">
                            <div style="font-size:28px;margin-bottom:6px;">🚚</div>
                            <div style="font-size:12px;font-weight:700;color:#1e40af;">สั่งซื้อแล้ว</div>
                            <div style="font-size:10px;color:#94a3b8;margin-top:2px;">Ordered</div>
                        </div>
                        <div class="status-card ${currentStatus === 'received' ? 'selected' : ''}" data-status="received" onclick="window._selectStatusCard(this)">
                            <div style="font-size:28px;margin-bottom:6px;">✅</div>
                            <div style="font-size:12px;font-weight:700;color:#166534;">ได้รับของแล้ว</div>
                            <div style="font-size:10px;color:#94a3b8;margin-top:2px;">Received</div>
                        </div>
                        <div class="status-card ${currentStatus === 'cancelled' ? 'selected' : ''}" data-status="cancelled" onclick="window._selectStatusCard(this)">
                            <div style="font-size:28px;margin-bottom:6px;">❌</div>
                            <div style="font-size:12px;font-weight:700;color:#991b1b;">ยกเลิก</div>
                            <div style="font-size:10px;color:#94a3b8;margin-top:2px;">Cancelled</div>
                        </div>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: '<i class="fa-solid fa-check"></i> บันทึกสถานะ',
                cancelButtonText: 'ยกเลิก',
                confirmButtonColor: '#2563eb',
                customClass: { popup: 'rounded-2xl' },
                preConfirm: () => {
                    const selected = document.querySelector('#statusCardGrid .status-card.selected');
                    if (!selected) {
                        Swal.showValidationMessage('กรุณาเลือกสถานะ');
                        return false;
                    }
                    return selected.dataset.status;
                }
            });

            if (status) {
                await updateDoc(doc(getItemsRef(), itemId), { status: status });
                try {
                    await addDoc(getLogsRef(), {
                        projectId: currentProjectId,
                        projectName: currentProjectName,
                        itemName: item.name,
                        action: 'อัปเดตสถานะ',
                        actorRole: localStorage.getItem('mentra_role') || 'Unknown',
                        details: `เปลี่ยนสถานะเป็น: ${document.querySelector(`option[value="${status}"]`)?.text || status}`,
                        actorName: JSON.parse(localStorage.getItem('mentra_user'))?.name || 'Unknown',
                        timestamp: serverTimestamp()
                    });
                } catch (err) { console.error('Log error:', err); }
                const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 2000 });
                Toast.fire({ icon: 'success', title: 'สถานะอัปเดตแล้ว' });
            }
        }

        window._selectStatusCard = (el) => {
            document.querySelectorAll('#statusCardGrid .status-card').forEach(c => c.classList.remove('selected'));
            el.classList.add('selected');
        }

        // *** EDIT ITEM FULL (ADMIN ONLY) ***
        window.editItem = async (itemId) => {
            if (!isAdmin) return;
            const item = allItemsCache[itemId]; if (!item) return;

            const { value: f } = await Swal.fire({
                title: 'แก้ไขรายการวัสดุ',
                width: '600px',
                html: `
                    <div class="text-left text-sm space-y-4">
                        <div><label class="font-bold">ชื่อสินค้า</label><input id="sw-name" class="swal2-input w-full m-0" value="${escapeHtml(item.name)}"></div>
                        <div><label class="font-bold">รายละเอียด</label><textarea id="sw-details" class="swal2-textarea w-full m-0 p-2">${escapeHtml(item.details)}</textarea></div>
                        <div class="grid grid-cols-2 gap-4">
                            <div><label class="font-bold">ราคาต่อหน่วย</label><input id="sw-price" type="number" class="swal2-input w-full m-0" value="${item.price}"></div>
                            <div><label class="font-bold">จำนวน</label><input id="sw-qty" type="number" class="swal2-input w-full m-0" value="${item.qty}"></div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="font-bold">สถานะ</label>
                                <select id="sw-status" class="swal2-select w-full m-0">
                                    <option value="pending" ${item.status === 'pending' ? 'selected' : ''}>⏳ รอดำเนินการ</option>
                                    <option value="ordered" ${item.status === 'ordered' ? 'selected' : ''}>🚚 สั่งซื้อแล้ว</option>
                                    <option value="received" ${item.status === 'received' ? 'selected' : ''}>✅ ได้รับของแล้ว</option>
                                    <option value="cancelled" ${item.status === 'cancelled' ? 'selected' : ''}>❌ ยกเลิก</option>
                                </select>
                            </div>
                            <div><label class="font-bold">หมายเหตุ</label><input id="sw-remark" class="swal2-input w-full m-0" value="${escapeHtml(item.remark)}"></div>
                        </div>
                        <div><label class="font-bold">ลิงก์</label><input id="sw-link" class="swal2-input w-full m-0" value="${escapeHtml(item.link)}"></div>
                        <div><label class="font-bold block mb-1">เปลี่ยนรูปภาพ</label><input type="file" id="sw-file" class="w-full text-xs" accept="image/*"></div>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'บันทึกการแก้ไข',
                confirmButtonColor: '#2563eb',
                preConfirm: () => ({
                    name: document.getElementById('sw-name').value,
                    details: document.getElementById('sw-details').value,
                    price: parseFloat(document.getElementById('sw-price').value) || 0,
                    qty: parseFloat(document.getElementById('sw-qty').value) || 0,
                    status: document.getElementById('sw-status').value,
                    remark: document.getElementById('sw-remark').value,
                    link: document.getElementById('sw-link').value,
                    file: document.getElementById('sw-file').files[0]
                })
            });

            if (f) {
                Swal.fire({ title: 'กำลังบันทึก...', didOpen: () => Swal.showLoading() });
                try {
                    let upData = {
                        name: (f.name || '').trim(), details: (f.details || '').trim(), price: f.price, qty: f.qty,
                        status: f.status, remark: (f.remark || '').trim(), link: sanitizeUrl(f.link),
                        total: f.price * f.qty
                    };
                    if (f.file) upData.image = await resizeImage(f.file, 500);
                    await updateDoc(doc(getItemsRef(), itemId), upData);
                    try {
                        await addDoc(getLogsRef(), {
                            projectId: currentProjectId,
                            projectName: currentProjectName,
                            itemName: upData.name || item.name,
                            action: 'อัปเดตรายการ',
                            actorRole: localStorage.getItem('mentra_role') || 'Unknown',
                            details: `อัปเดตข้อมูล ${item.qty !== upData.qty ? `[จำนวน: ${item.qty} -> ${upData.qty}] ` : ''}${item.price !== upData.price ? `[ราคา: ฿${item.price} -> ฿${upData.price}] ` : ''}`.trim() || 'แก้ไขรายละเอียด/ลิงก์ทั่วไป',
                            actorName: JSON.parse(localStorage.getItem('mentra_user'))?.name || 'Unknown',
                            timestamp: serverTimestamp()
                        });
                    } catch (err) { console.error('Log error:', err); }
                    Swal.fire({ icon: 'success', title: 'อัปเดตเรียบร้อย', timer: 1000, showConfirmButton: false });
                } catch (e) { Swal.fire('Error', e.message, 'error'); }
            }
        }

        window.deleteItem = async (docId) => {
            if (!isAdmin) return;
            if ((await Swal.fire({ title: 'ยืนยันลบ?', text: 'คุณต้องการลบรายการนี้ใช่หรือไม่?', icon: 'warning', showCancelButton: true, confirmButtonColor: '#ef4444', confirmButtonText: 'ยืนยันลบ' })).isConfirmed) {
                const item = allItemsCache[docId];
                await deleteDoc(doc(getItemsRef(), docId));
                if (item) {
                    try {
                        await addDoc(getLogsRef(), {
                            projectId: currentProjectId,
                            projectName: currentProjectName,
                            itemName: item.name,
                            action: 'ลบรายการ',
                            actorRole: localStorage.getItem('mentra_role') || 'Unknown',
                            details: `ลบสินค้าจำนวน ${item.qty} ชิ้น (ราคา ฿${parseFloat(item.price).toLocaleString('en-US')})`,
                            actorName: JSON.parse(localStorage.getItem('mentra_user'))?.name || 'Unknown',
                            timestamp: serverTimestamp()
                        });
                    } catch (err) { console.error('Log error:', err); }
                }
                Swal.mixin({ toast: true, position: 'bottom-end', showConfirmButton: false, timer: 2000 }).fire({ icon: 'success', title: 'ลบรายการแล้ว' });
            }
        }

        const resizeImage = (file, maxWidth) => new Promise((resolve) => {
            const r = new FileReader(); r.readAsDataURL(file);
            r.onload = (e) => {
                const i = new Image(); i.src = e.target.result; i.onload = () => {
                    const c = document.createElement('canvas'); const s = maxWidth / i.width; c.width = maxWidth; c.height = i.height * s;
                    c.getContext('2d').drawImage(i, 0, 0, c.width, c.height); resolve(c.toDataURL('image/jpeg', 0.7));
                }
            };
        });

        // PDF Export
        window.exportToPDF = () => {
            if (!currentItemsList.length) return Swal.fire('แจ้งเตือน', 'ไม่มีข้อมูลในโครงการนี้', 'warning');
            Swal.fire({ title: 'กำลังสร้างไฟล์ PDF...', didOpen: () => Swal.showLoading() });

            const ITEMS_PER_PAGE = 17;
            const totalPages = Math.ceil(currentItemsList.length / ITEMS_PER_PAGE);
            const grandTotal = currentItemsList.reduce((s, i) => s + (i.total || 0), 0);
            const fmtNum = (n) => n.toLocaleString(undefined, { minimumFractionDigits: 2 });
            const dateStr = new Date().toLocaleDateString('th-TH', { year: 'numeric', month: 'long', day: 'numeric' });

            const makeHeader = (pageNum) => `
                <div style="display:flex; justify-content:space-between; margin-bottom:14px; border-bottom:3px solid #f97316; padding-bottom:12px;">
                    <div style="display:flex; align-items:center; gap:12px;">
                        <img src="Mentra_Solution_Tranparency.png" style="height:40px;">
                        <div><h1 style="font-size:17px; font-weight:bold; margin:0;">Mentra Solution Co., Ltd.</h1><p style="font-size:11px; color:#666; margin:2px 0 0;">BOM Report (รายงานวัสดุ)</p></div>
                    </div>
                    <div style="text-align:right;">
                        <h2 style="font-size:15px; color:#2563eb; margin:0;">${escapeHtml(currentProjectName)}</h2>
                        <p style="font-size:9px; color:#94a3b8; margin:2px 0 0;">วันที่: ${dateStr} | หน้า ${pageNum}/${totalPages}</p>
                    </div>
                </div>`;

            const tableHead = `<thead><tr style="background:#f1f5f9;">
                <th style="border:1px solid #ddd; padding:7px; width:30px;">#</th>
                <th style="border:1px solid #ddd; padding:7px; width:44px;">รูป</th>
                <th style="border:1px solid #ddd; padding:7px;">รายการ / รายละเอียด</th>
                <th style="border:1px solid #ddd; padding:7px; width:85px;">ราคา/หน่วย</th>
                <th style="border:1px solid #ddd; padding:7px; width:50px;">จำนวน</th>
                <th style="border:1px solid #ddd; padding:7px; width:95px;">รวม</th>
                <th style="border:1px solid #ddd; padding:7px; width:75px;">สถานะ</th>
            </tr></thead>`;

            let pagesHtml = '';
            for (let p = 0; p < totalPages; p++) {
                const startIdx = p * ITEMS_PER_PAGE;
                const pageItems = currentItemsList.slice(startIdx, startIdx + ITEMS_PER_PAGE);
                const isLastPage = (p === totalPages - 1);

                let rows = pageItems.map((i, x) => {
                    const imgTag = i.image
                        ? `<img src="${i.image}" style="width:36px;height:36px;object-fit:cover;border-radius:4px;border:1px solid #e2e8f0;">`
                        : `<div style="width:36px;height:36px;background:#f1f5f9;border-radius:4px;display:flex;align-items:center;justify-content:center;color:#cbd5e1;font-size:14px;">—</div>`;
                    const linkHtml = i.link ? `<br><a href="${escapeHtml(i.link)}" style="color:#3b82f6;font-size:8px;word-break:break-all;">🔗 ${escapeHtml(i.link).substring(0, 45)}${i.link.length > 45 ? '...' : ''}</a>` : '';
                    const detailHtml = i.details ? `<br><span style="color:#64748b;font-size:9px;">${escapeHtml(i.details)}</span>` : '';

                    return `<tr>
                        <td style="border:1px solid #ddd;padding:5px;text-align:center;font-size:10px;vertical-align:middle;">${startIdx + x + 1}</td>
                        <td style="border:1px solid #ddd;padding:4px;text-align:center;vertical-align:middle;">${imgTag}</td>
                        <td style="border:1px solid #ddd;padding:5px;font-size:10px;vertical-align:middle;">
                            <b>${escapeHtml(i.name)}</b>${detailHtml}${linkHtml}
                        </td>
                        <td style="border:1px solid #ddd;padding:5px;text-align:right;font-size:10px;vertical-align:middle;">฿${fmtNum(i.price)}</td>
                        <td style="border:1px solid #ddd;padding:5px;text-align:center;font-size:10px;vertical-align:middle;">${i.qty}</td>
                        <td style="border:1px solid #ddd;padding:5px;text-align:right;font-weight:bold;color:#2563eb;font-size:10px;vertical-align:middle;">฿${fmtNum(i.total)}</td>
                        <td style="border:1px solid #ddd;padding:5px;text-align:center;font-size:9px;vertical-align:middle;">${getStatusTextPDF(i.status)}</td>
                    </tr>`;
                }).join('');

                let footer = '';
                if (isLastPage) {
                    footer = `<tfoot>
                        <tr style="background:#eff6ff;">
                            <td colspan="5" style="border:1px solid #bfdbfe; padding:7px; text-align:right; font-weight:bold;">รวมทั้งหมด (${currentItemsList.length} รายการ)</td>
                            <td style="border:1px solid #bfdbfe; padding:7px; text-align:right; color:#2563eb; font-weight:bold; font-size:13px;">฿${fmtNum(grandTotal)}</td>
                            <td style="border:1px solid #bfdbfe; padding:7px; text-align:center; font-size:9px; color:#94a3b8;">บาท</td>
                        </tr>
                    </tfoot>`;
                }

                pagesHtml += `<div style="font-family:'Prompt'; padding:24px 30px; width:210mm; background:white; color:#333; ${!isLastPage ? 'page-break-after:always;' : ''}">
                    ${makeHeader(p + 1)}
                    <table style="width:100%; border-collapse:collapse; font-size:10px;">
                        ${tableHead}
                        <tbody>${rows}</tbody>
                        ${footer}
                    </table>
                    ${isLastPage ? '<p style="text-align:center; font-size:8px; color:#94a3b8; margin-top:14px;">Generated by Mentra BOM Manager v3.0 • พัฒนาโดย ธนภูมิ แดงประดับ</p>' : ''}
                </div>`;
            }

            const el = document.createElement('div');
            el.innerHTML = pagesHtml;
            const safeFileName = currentProjectName.replace(/[^a-zA-Z0-9ก-๙\s_-]/g, '').trim() || 'Unnamed';
            html2pdf().set({ margin: 0, filename: `BOM-${safeFileName}.pdf`, image: { type: 'jpeg', quality: 0.98 }, html2canvas: { scale: 2 }, jsPDF: { unit: 'mm', format: 'a4' } }).from(el).save().then(Swal.close);
        }

        // Excel Export (ExcelJS — สวยๆ พร้อมปริ้น)
        window.exportToExcel = async () => {
            if (!currentItemsList.length) return Swal.fire('แจ้งเตือน', 'ไม่มีข้อมูลในโครงการนี้', 'warning');
            Swal.fire({ title: 'กำลังสร้างไฟล์ Excel...', didOpen: () => Swal.showLoading(), allowOutsideClick: false });

            try {
                const wb = new ExcelJS.Workbook();
                wb.creator = 'Mentra BOM Manager';
                wb.created = new Date();

                const ws = wb.addWorksheet('BOM Report', {
                    properties: { tabColor: { argb: 'FF2563EB' } },
                    pageSetup: {
                        paperSize: 9, // A4
                        orientation: 'landscape',
                        fitToPage: true,
                        fitToWidth: 1,
                        fitToHeight: 0,
                        margins: { left: 0.4, right: 0.4, top: 0.6, bottom: 0.5, header: 0.3, footer: 0.3 },
                        printArea: `A1:I${7 + currentItemsList.length + 2}`,
                        horizontalCentered: true
                    },
                    headerFooter: {
                        oddFooter: '&L&8Mentra BOM Manager&C&8หน้า &P / &N&R&8&D'
                    }
                });


                // ===== กำหนดความกว้างคอลัมน์ =====
                ws.columns = [
                    { key: 'no', width: 6 },
                    { key: 'name', width: 32 },
                    { key: 'details', width: 32 },
                    { key: 'link', width: 24 },
                    { key: 'price', width: 16 },
                    { key: 'qty', width: 10 },
                    { key: 'total', width: 18 },
                    { key: 'status', width: 16 },
                    { key: 'remark', width: 22 }
                ];

                // ===== สีหลัก =====
                const DARK_NAVY = 'FF1E293B';
                const ORANGE = 'FFF97316';
                const BLUE = 'FF2563EB';
                const LIGHT_BLUE = 'FFEFF6FF';
                const LIGHT_ORANGE = 'FFFFF7ED';
                const WHITE = 'FFFFFFFF';
                const GRAY_BG = 'FFF8FAFC';
                const BORDER_COLOR = 'FFE2E8F0';

                const thinBorder = { style: 'thin', color: { argb: BORDER_COLOR } };
                const allBorders = { top: thinBorder, left: thinBorder, bottom: thinBorder, right: thinBorder };

                // ===== ROW 1: ชื่อบริษัท =====
                ws.mergeCells('A1:I1');
                const row1 = ws.getRow(1);
                row1.height = 36;
                const cellA1 = ws.getCell('A1');
                cellA1.value = '     Mentra Solution Co., Ltd.';
                cellA1.font = { name: 'Prompt', size: 18, bold: true, color: { argb: WHITE } };
                cellA1.fill = {
                    type: 'gradient', gradient: 'angle', degree: 0, stops: [
                        { position: 0, color: { argb: DARK_NAVY } },
                        { position: 1, color: { argb: 'FF334155' } }
                    ]
                };
                cellA1.alignment = { vertical: 'middle', horizontal: 'left' };

                // ===== ROW 2: ชื่อรายงาน + แถบสีส้ม =====
                ws.mergeCells('A2:I2');
                const row2 = ws.getRow(2);
                row2.height = 28;
                const cellA2 = ws.getCell('A2');
                cellA2.value = '     BOM Report — รายงานวัสดุ';
                cellA2.font = { name: 'Prompt', size: 12, bold: false, color: { argb: ORANGE } };
                cellA2.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: DARK_NAVY } };
                cellA2.alignment = { vertical: 'middle', horizontal: 'left' };

                // ===== ROW 3: ชื่อโครงการ =====
                ws.mergeCells('A3:I3');
                const row3 = ws.getRow(3);
                row3.height = 26;
                const cellA3 = ws.getCell('A3');
                cellA3.value = `📁  โครงการ: ${currentProjectName}`;
                cellA3.font = { name: 'Prompt', size: 13, bold: true, color: { argb: BLUE } };
                cellA3.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: LIGHT_BLUE } };
                cellA3.alignment = { vertical: 'middle', horizontal: 'left' };
                cellA3.border = { bottom: { style: 'medium', color: { argb: BLUE } } };

                // ===== ROW 4: วันที่ =====
                ws.mergeCells('A4:I4');
                const row4 = ws.getRow(4);
                row4.height = 22;
                const cellA4 = ws.getCell('A4');
                cellA4.value = `📅  วันที่พิมพ์: ${new Date().toLocaleDateString('th-TH', { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' })}`;
                cellA4.font = { name: 'Prompt', size: 10, color: { argb: 'FF64748B' } };
                cellA4.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: LIGHT_ORANGE } };
                cellA4.alignment = { vertical: 'middle', horizontal: 'left' };

                // ===== ROW 5: เว้นว่าง + เส้นคั่นสีส้ม =====
                ws.mergeCells('A5:I5');
                const row5 = ws.getRow(5);
                row5.height = 6;
                ws.getCell('A5').fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: ORANGE } };

                // ===== ROW 6: หัวตาราง =====
                const headerLabels = ['#', 'ชื่อรายการ', 'รายละเอียด', 'ลิงก์ร้านค้า', 'ราคา/หน่วย', 'จำนวน', 'รวม (บาท)', 'สถานะ', 'หมายเหตุ'];
                const headerRow = ws.getRow(6);
                headerRow.height = 30;
                headerLabels.forEach((label, idx) => {
                    const cell = headerRow.getCell(idx + 1);
                    cell.value = label;
                    cell.font = { name: 'Prompt', size: 11, bold: true, color: { argb: WHITE } };
                    cell.fill = {
                        type: 'gradient', gradient: 'angle', degree: 0, stops: [
                            { position: 0, color: { argb: BLUE } },
                            { position: 1, color: { argb: 'FF3B82F6' } }
                        ]
                    };
                    cell.alignment = { vertical: 'middle', horizontal: (idx === 0 || idx === 5 || idx === 7) ? 'center' : (idx === 4 || idx === 6) ? 'right' : 'left', wrapText: true };
                    cell.border = allBorders;
                });

                // ===== DATA ROWS =====
                const statusColors = {
                    'สั่งแล้ว': { bg: 'FFDBEAFE', fg: 'FF1E40AF' },
                    'ได้รับแล้ว': { bg: 'FFDCFCE7', fg: 'FF166534' },
                    'ยกเลิก': { bg: 'FFFEE2E2', fg: 'FF991B1B' },
                    'รอดำเนินการ': { bg: 'FFF1F5F9', fg: 'FF475569' }
                };

                currentItemsList.forEach((item, idx) => {
                    const rowNum = 7 + idx;
                    const row = ws.getRow(rowNum);
                    row.height = 24;
                    const isEven = idx % 2 === 0;
                    const bgColor = isEven ? WHITE : GRAY_BG;

                    // ค่าแต่ละคอลัมน์
                    const statusText = getStatusTextPDF(item.status);
                    const sColor = statusColors[statusText] || statusColors['รอดำเนินการ'];
                    const values = [
                        idx + 1,
                        item.name || '',
                        item.details || '-',
                        item.link || '-',
                        item.price || 0,
                        item.qty || 0,
                        item.total || 0,
                        statusText,
                        item.remark || '-'
                    ];

                    values.forEach((val, colIdx) => {
                        const cell = row.getCell(colIdx + 1);
                        cell.value = val;
                        cell.border = allBorders;

                        // Font
                        cell.font = { name: 'Prompt', size: 10 };

                        // Background
                        if (colIdx === 7) {
                            // สถานะ — สีพิเศษ
                            cell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: sColor.bg } };
                            cell.font = { name: 'Prompt', size: 9, bold: true, color: { argb: sColor.fg } };
                            cell.alignment = { vertical: 'middle', horizontal: 'center' };
                        } else {
                            cell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: bgColor } };
                        }

                        // Alignment
                        if (colIdx === 0 || colIdx === 5) cell.alignment = { vertical: 'middle', horizontal: 'center' };
                        else if (colIdx === 4 || colIdx === 6) cell.alignment = { vertical: 'middle', horizontal: 'right' };
                        else if (colIdx !== 7) cell.alignment = { vertical: 'middle', horizontal: 'left', wrapText: true };

                        // Number format
                        if (colIdx === 4 || colIdx === 6) cell.numFmt = '#,##0.00';
                        if (colIdx === 5) cell.numFmt = '#,##0';

                        // Bold ชื่อสินค้า
                        if (colIdx === 1) cell.font = { name: 'Prompt', size: 10, bold: true, color: { argb: 'FF1E293B' } };
                        // Bold ยอดรวม
                        if (colIdx === 6) cell.font = { name: 'Prompt', size: 10, bold: true, color: { argb: BLUE } };

                        // HYPERLINK for link column
                        if (colIdx === 3 && item.link) {
                            cell.value = { text: item.link, hyperlink: item.link };
                            cell.font = { name: 'Prompt', size: 9, color: { argb: 'FF3B82F6' }, underline: true };
                        }
                    });
                });

                // ===== BLANK ROW =====
                const blankRowNum = 7 + currentItemsList.length;
                const blankRow = ws.getRow(blankRowNum);
                blankRow.height = 4;
                for (let c = 1; c <= 9; c++) {
                    const cell = blankRow.getCell(c);
                    cell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: ORANGE } };
                }

                // ===== GRAND TOTAL ROW =====
                const grandTotal = currentItemsList.reduce((sum, i) => sum + (i.total || 0), 0);
                const totalRowNum = blankRowNum + 1;
                const totalRow = ws.getRow(totalRowNum);
                totalRow.height = 36;

                // Merge A-F สำหรับข้อความ
                ws.mergeCells(`A${totalRowNum}:F${totalRowNum}`);
                const labelCell = totalRow.getCell(1);
                labelCell.value = 'รวมทั้งหมด (Grand Total)';
                labelCell.font = { name: 'Prompt', size: 14, bold: true, color: { argb: DARK_NAVY } };
                labelCell.fill = {
                    type: 'gradient', gradient: 'angle', degree: 0, stops: [
                        { position: 0, color: { argb: LIGHT_BLUE } },
                        { position: 1, color: { argb: LIGHT_ORANGE } }
                    ]
                };
                labelCell.alignment = { vertical: 'middle', horizontal: 'right' };
                labelCell.border = { top: { style: 'medium', color: { argb: BLUE } }, bottom: { style: 'double', color: { argb: DARK_NAVY } }, left: thinBorder, right: thinBorder };

                // Total value (column 7)
                const totalValCell = totalRow.getCell(7);
                totalValCell.value = grandTotal;
                totalValCell.numFmt = '#,##0.00';
                totalValCell.font = { name: 'Prompt', size: 16, bold: true, color: { argb: BLUE } };
                totalValCell.fill = {
                    type: 'gradient', gradient: 'angle', degree: 0, stops: [
                        { position: 0, color: { argb: LIGHT_BLUE } },
                        { position: 1, color: { argb: LIGHT_ORANGE } }
                    ]
                };
                totalValCell.alignment = { vertical: 'middle', horizontal: 'right' };
                totalValCell.border = { top: { style: 'medium', color: { argb: BLUE } }, bottom: { style: 'double', color: { argb: DARK_NAVY } }, left: thinBorder, right: thinBorder };

                // Merge H-I สำหรับ "บาท"
                ws.mergeCells(`H${totalRowNum}:I${totalRowNum}`);
                const unitCell = totalRow.getCell(8);
                unitCell.value = 'บาท (THB)';
                unitCell.font = { name: 'Prompt', size: 11, bold: true, color: { argb: 'FF64748B' } };
                unitCell.fill = {
                    type: 'gradient', gradient: 'angle', degree: 0, stops: [
                        { position: 0, color: { argb: LIGHT_BLUE } },
                        { position: 1, color: { argb: LIGHT_ORANGE } }
                    ]
                };
                unitCell.alignment = { vertical: 'middle', horizontal: 'left' };
                unitCell.border = { top: { style: 'medium', color: { argb: BLUE } }, bottom: { style: 'double', color: { argb: DARK_NAVY } }, left: thinBorder, right: thinBorder };

                // ===== FOOTER INFO ROW =====
                const footerRowNum = totalRowNum + 1;
                ws.mergeCells(`A${footerRowNum}:I${footerRowNum}`);
                const footerRow = ws.getRow(footerRowNum);
                footerRow.height = 20;
                const footerCell = footerRow.getCell(1);
                footerCell.value = `Generated by Mentra BOM Manager v3.0  •  ${currentItemsList.length} รายการ  •  พัฒนาโดย ธนภูมิ แดงประดับ`;
                footerCell.font = { name: 'Prompt', size: 8, italic: true, color: { argb: 'FF94A3B8' } };
                footerCell.alignment = { vertical: 'middle', horizontal: 'center' };

                // ===== AUTOFILTER บนหัวตาราง =====
                ws.autoFilter = { from: 'A6', to: `H${6 + currentItemsList.length}` };

                // ===== FREEZE PANES (ตรึงหัวตาราง) =====
                ws.views = [{ state: 'frozen', ySplit: 6, activeCell: 'A7' }];

                // ===== SAVE FILE =====
                const buffer = await wb.xlsx.writeBuffer();
                const blob = new Blob([buffer], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
                const safeFileName = currentProjectName.replace(/[^a-zA-Z0-9ก-๙\s_-]/g, '').trim() || 'Unnamed';
                saveAs(blob, `BOM-${safeFileName}.xlsx`);

                Swal.mixin({ toast: true, position: 'bottom-end', showConfirmButton: false, timer: 3000 })
                    .fire({ icon: 'success', title: 'ดาวน์โหลด Excel สำเร็จ' });
            } catch (e) {
                console.error('Excel export error:', e);
                Swal.fire('Error', 'ไม่สามารถสร้างไฟล์ Excel ได้: ' + e.message, 'error');
            }
        }
    </script>
    </main>
    </div>
    </div>
</body>

</html>