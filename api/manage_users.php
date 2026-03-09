<!DOCTYPE html>
<html lang="th">

<head>
    <!-- Security Check: ต้องเป็น Admin เท่านั้นถึงเข้าหน้านี้ได้ -->
    <script>
        const role = localStorage.getItem('mentra_role');
        if (role !== 'admin') {
            alert('คุณไม่มีสิทธิ์เข้าถึงหน้านี้!');
            window.location.href = 'index.php';
        }
    </script>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Mentra System</title>

    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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

<body class="text-slate-700 bg-slate-50">

    <!-- Sidebar and Header -->
    <?php include 'sidebar.php'; ?>

    <div class="max-w-7xl mx-auto px-4 py-8 grid grid-cols-1 lg:grid-cols-12 gap-8 fade-in-up">

        <!-- Column 1: Add New User Form -->
        <div class="lg:col-span-4">
            <div class="glass-panel p-6 sticky top-24 border-t-4 border-green-500">
                <h2 class="text-lg font-bold mb-4 flex items-center gap-2 text-slate-800">
                    <i class="fa-solid fa-user-plus text-green-600"></i> เพิ่มพนักงานใหม่
                </h2>

                <form id="addUserForm" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">ชื่อ-นามสกุล (Full
                            Name)</label>
                        <input type="text" id="new_name" required
                            class="w-full px-4 py-2 bg-slate-50 border rounded-lg focus:ring-2 focus:ring-green-500 outline-none text-sm"
                            placeholder="เช่น สมชาย ใจดี">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">ชื่อผู้ใช้งาน
                            (Username)</label>
                        <input type="text" id="new_username" required
                            class="w-full px-4 py-2 bg-slate-50 border rounded-lg focus:ring-2 focus:ring-green-500 outline-none text-sm"
                            placeholder="เช่น somchai.j">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">อีเมล (Email)</label>
                        <input type="email" id="new_email" required
                            class="w-full px-4 py-2 bg-slate-50 border rounded-lg focus:ring-2 focus:ring-green-500 outline-none text-sm"
                            placeholder="somchai@mentra.co.th">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">รหัสผ่าน (ขั้นต่ำ 6
                            ตัว)</label>
                        <input type="password" id="new_password" required minlength="6"
                            class="w-full px-4 py-2 bg-slate-50 border rounded-lg focus:ring-2 focus:ring-green-500 outline-none text-sm"
                            placeholder="••••••••">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">กำหนดสิทธิ์ (Role)</label>
                        <select id="new_role"
                            class="w-full px-4 py-2 bg-slate-50 border rounded-lg focus:ring-2 focus:ring-green-500 outline-none text-sm cursor-pointer">
                            <option value="viewer">Viewer (ดูได้อย่างเดียว)</option>
                            <option value="material">Material (ฝ่ายวัสดุ - เพิ่มของได้)</option>
                            <option value="purchasing">Purchasing (จัดซื้อ - อัปเดตสถานะได้)</option>
                            <option value="admin">Admin (สิทธิ์สูงสุด)</option>
                        </select>
                    </div>

                    <button type="submit"
                        class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 rounded-lg shadow-md transition-all flex justify-center items-center gap-2">
                        <i class="fa-solid fa-check-circle"></i> สร้างบัญชีผู้ใช้
                    </button>
                </form>
            </div>
        </div>

        <!-- Column 2: User List Table -->
        <div class="lg:col-span-8">
            <div class="glass-panel p-6 min-h-[500px]">
                <div class="flex justify-between items-center mb-6 pb-4 border-b border-slate-100">
                    <h2 class="text-xl font-bold text-slate-800 flex items-center gap-2">
                        <i class="fa-solid fa-users text-blue-600"></i> รายชื่อผู้ใช้งานทั้งหมด
                    </h2>
                    <span class="text-xs bg-blue-100 text-blue-600 px-3 py-1 rounded-full font-bold" id="userCount">0
                        Users</span>
                </div>

                <div class="overflow-x-auto rounded-xl border border-slate-200">
                    <table class="w-full text-left">
                        <thead class="bg-slate-50 text-xs text-slate-500 uppercase font-bold border-b border-slate-200">
                            <tr>
                                <th class="p-4">ชื่อพนักงาน</th>
                                <th class="p-4">Username</th>
                                <th class="p-4">อีเมล</th>
                                <th class="p-4 text-center">สิทธิ์</th>
                                <th class="p-4 text-center">สถานะ</th>
                                <th class="p-4 text-center">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody id="userTableBody" class="divide-y divide-slate-100 text-sm">
                            <tr>
                                <td colspan="6" class="p-8 text-center text-slate-400">กำลังโหลดข้อมูล...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer spacer -->
    <div class="h-24"></div>

    <!-- Script Logic -->
    <script type="module">
        import { initializeApp, deleteApp } from "https://www.gstatic.com/firebasejs/11.6.1/firebase-app.js";
        import { getFirestore, collection, doc, setDoc, onSnapshot, updateDoc, deleteDoc } from "https://www.gstatic.com/firebasejs/11.6.1/firebase-firestore.js";
        import { getAuth, createUserWithEmailAndPassword, signOut, onAuthStateChanged } from "https://www.gstatic.com/firebasejs/11.6.1/firebase-auth.js";

        // --- Config (ใช้ชุดเดิม) ---
        let firebaseConfig;
        try { if (typeof __firebase_config !== 'undefined') firebaseConfig = JSON.parse(__firebase_config); } catch (e) { }
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

        const app = initializeApp(firebaseConfig); // App หลักสำหรับดึงข้อมูล
        const db = getFirestore(app);
        const appId = typeof __app_id !== 'undefined' ? __app_id : 'default-bom-app';

        const userRef = collection(db, 'artifacts', appId, 'public', 'data', 'user_registry');
        const auth = getAuth(app);

        // --- 1. โหลดรายชื่อผู้ใช้ ---
        onAuthStateChanged(auth, (user) => {
            if (user) {
                onSnapshot(userRef, (snapshot) => {
                    const tbody = document.getElementById('userTableBody');
                    let html = '';
                    let count = 0;

                    snapshot.forEach(d => {
                        const u = d.data();
                        count++;

                        // Badge สีตาม Role
                        let roleColor = 'bg-slate-100 text-slate-500';
                        if (u.role === 'admin') roleColor = 'bg-red-100 text-red-600';
                        if (u.role === 'purchasing') roleColor = 'bg-blue-100 text-blue-600';
                        if (u.role === 'material') roleColor = 'bg-orange-100 text-orange-600';

                        html += `
                        <tr class="item-row-enter hover:bg-slate-50 transition-colors">
                            <td class="p-4 font-semibold text-slate-700">${u.name}</td>
                            <td class="p-4 text-slate-600 font-mono text-xs">${u.username || '-'}</td>
                            <td class="p-4 text-slate-500 text-xs">${u.email}</td>
                            <td class="p-4 text-center">
                                <span class="px-2 py-1 rounded text-[10px] font-bold uppercase ${roleColor}">${u.role}</span>
                            </td>
                            <td class="p-4 text-center">
                                <span class="text-green-600 font-bold text-xs"><i class="fa-solid fa-check-circle"></i> Active</span>
                            </td>
                            <td class="p-4 text-center flex justify-center gap-2">
                                 <button onclick="window.changeRole('${d.id}', '${u.role}')" class="magnetic text-slate-400 hover:text-blue-500 transition-colors" title="เปลี่ยนสิทธิ์">
                                    <i class="fa-solid fa-user-gear"></i>
                                </button>
                                <button onclick="window.deleteUser('${d.id}', '${u.name}')" class="magnetic text-slate-400 hover:text-red-500 transition-colors" title="ลบผู้ใช้">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                    });

                    if (count === 0) html = '<tr><td colspan="6" class="p-8 text-center text-slate-400">ยังไม่มีผู้ใช้งานในระบบ</td></tr>';

                    tbody.innerHTML = html;
                    document.getElementById('userCount').innerText = `${count} Users`;

                    // GSAP Enter Animation
                    if (typeof gsap !== 'undefined' && html !== '') {
                        gsap.from('.item-row-enter', {
                            opacity: 0,
                            y: 15,
                            duration: 0.5,
                            stagger: 0.04,
                            ease: "cubic-bezier(0.22, 1, 0.36, 1)"
                        });
                    }
                }, (error) => {
                    console.error("Firestore error:", error);
                    document.getElementById('userTableBody').innerHTML = `<tr><td colspan="6" class="p-8 text-center text-red-500">ไม่สามารถโหลดข้อมูลผู้ใช้ได้ (Permissions Error)</td></tr>`;
                });
            } else {
                document.getElementById('userTableBody').innerHTML = '<tr><td colspan="6" class="p-8 text-center text-slate-400">รอการตรวจสอบสิทธิ์ Firebase...</td></tr>';
            }
        });

        // --- 2. ฟังก์ชันเพิ่มผู้ใช้ (เทคนิคพิเศษ: สร้าง App ซ้อนเพื่อไม่ให้ Admin หลุด) ---
        window.handleAddUser = async (e) => {
            e.preventDefault();

            const name = document.getElementById('new_name').value;
            const username = document.getElementById('new_username').value;
            const email = document.getElementById('new_email').value;
            const pass = document.getElementById('new_password').value;
            const role = document.getElementById('new_role').value;

            Swal.fire({
                title: 'กำลังสร้างบัญชี...',
                html: 'กรุณารอสักครู่ ระบบกำลังเพิ่มข้อมูล',
                didOpen: () => Swal.showLoading()
            });

            try {
                // เทคนิค: Initialize Secondary App เพื่อ Create User โดยไม่กระทบ Session ปัจจุบัน
                const secondaryApp = initializeApp(firebaseConfig, "SecondaryApp");
                const secondaryAuth = getAuth(secondaryApp);

                const userCred = await createUserWithEmailAndPassword(secondaryAuth, email, pass);
                const newUserUid = userCred.user.uid;

                // บันทึกข้อมูลลง Firestore (ใช้ db ของ Main App)
                await setDoc(doc(userRef, newUserUid), {
                    name: name,
                    username: username,
                    email: email,
                    role: role,
                    status: 'approved', // สร้างโดย Admin ให้ผ่านเลย
                    createdAt: new Date().toISOString()
                });

                // Clean up Secondary App
                await signOut(secondaryAuth);
                deleteApp(secondaryApp);

                // Reset Form
                document.getElementById('addUserForm').reset();

                Swal.fire({
                    icon: 'success',
                    title: 'เพิ่มพนักงานเรียบร้อย',
                    text: `User: ${username} (Email: ${email})`,
                    confirmButtonColor: '#16a34a'
                });

            } catch (error) {
                console.error(error);
                let msg = error.message;
                if (error.code === 'auth/email-already-in-use') msg = 'อีเมลนี้มีในระบบแล้ว';
                if (error.code === 'auth/weak-password') msg = 'รหัสผ่านต้องมีอย่างน้อย 6 ตัว';
                Swal.fire('เกิดข้อผิดพลาด', msg, 'error');
            }
        };

        // ผูก Event
        document.getElementById('addUserForm').addEventListener('submit', window.handleAddUser);

        // --- 3. ฟังก์ชันเปลี่ยนสิทธิ์ ---
        window.changeRole = async (uid, currentRole) => {
            const { value: newRole } = await Swal.fire({
                title: 'กำหนดสิทธิ์ใหม่',
                input: 'select',
                inputOptions: {
                    'viewer': 'Viewer (ดูอย่างเดียว)',
                    'material': 'Material (ฝ่ายวัสดุ)',
                    'purchasing': 'Purchasing (จัดซื้อ)',
                    'admin': 'Admin (สูงสุด)'
                },
                inputValue: currentRole,
                showCancelButton: true,
                confirmButtonColor: '#3b82f6',
                confirmButtonText: 'บันทึก'
            });

            if (newRole) {
                await updateDoc(doc(userRef, uid), { role: newRole });
                const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 2000 });
                Toast.fire({ icon: 'success', title: 'อัปเดตสิทธิ์แล้ว' });
            }
        };

        // --- 4. ฟังก์ชันลบผู้ใช้ ---
        window.deleteUser = async (uid, name) => {
            const result = await Swal.fire({
                title: `ลบคุณ ${name}?`,
                text: "ผู้ใช้นี้จะไม่สามารถล็อกอินได้อีกต่อไป",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                confirmButtonText: 'ยืนยันลบ'
            });

            if (result.isConfirmed) {
                // ลบข้อมูลใน Firestore (ทำให้ Login ไม่ได้เพราะ index.php เช็คข้อมูลนี้)
                await deleteDoc(doc(userRef, uid));
                Swal.fire('ลบเรียบร้อย', 'ปิดกั้นการเข้าถึงผู้ใช้นี้แล้ว', 'success');
            }
        };

    </script>
</body>

</html>