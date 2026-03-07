<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Mentra System</title>
    
    <!-- Libraries -->
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body { 
            font-family: 'Prompt', sans-serif; 
            background-color: #0f172a;
            /* พื้นหลังสีน้ำเงินเข้ม พร้อมลายจุดจางๆ */
            background-image: 
                radial-gradient(at 0% 0%, rgba(37, 99, 235, 0.2) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(249, 115, 22, 0.2) 0px, transparent 50%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        /* Animation */
        .orb {
            position: absolute; border-radius: 50%; filter: blur(80px); opacity: 0.4;
            animation: float 10s infinite ease-in-out; z-index: -1;
        }
        .orb-1 { top: -20%; left: -20%; width: 50vw; height: 50vw; background: #2563eb; }
        .orb-2 { bottom: -20%; right: -20%; width: 50vw; height: 50vw; background: #ea580c; animation-delay: -5s; }

        @keyframes float {
            0%, 100% { transform: translate(0, 0); }
            50% { transform: translate(20px, 20px); }
        }
    </style>
</head>
<body>

    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>

    <div class="login-card w-full max-w-md p-8 rounded-3xl shadow-2xl m-4 relative overflow-hidden">
        <!-- แถบสีด้านบน -->
        <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-blue-600 to-orange-500"></div>

        <!-- Logo & Header -->
        <div class="text-center mb-8">
            <div class="w-20 h-20 bg-white rounded-2xl shadow-md flex items-center justify-center mx-auto mb-4 p-2">
                <img src="Mentra_Solution_Tranparency.png" onerror="this.src='https://cdn-icons-png.flaticon.com/512/2881/2881142.png'" alt="Mentra Logo" class="w-full h-full object-contain">
            </div>
            <h1 class="text-2xl font-bold text-slate-800">Mentra Solution Co., Ltd</h1>
            <p class="text-slate-500 text-sm mt-1 uppercase tracking-widest font-light">BOM Manager Access</p>
        </div>

        <!-- Login Form -->
        <form id="loginForm" class="space-y-5">
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5 ml-1">ชื่อผู้ใช้งาน หรือ อีเมล (Username / Email)</label>
                <div class="relative group">
                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-blue-600 transition-colors">
                        <i class="fa-solid fa-user"></i>
                    </span>
                    <input type="text" id="userInput" required
                        class="w-full pl-11 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-500 transition-all placeholder-slate-300 shadow-sm" 
                        placeholder="admin หรือ user@example.com">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5 ml-1">รหัสผ่าน (Password)</label>
                <div class="relative group">
                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-orange-500 transition-colors">
                        <i class="fa-solid fa-lock"></i>
                    </span>
                    <input type="password" id="password" required
                        class="w-full pl-11 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-orange-50 focus:border-orange-500 transition-all placeholder-slate-300 shadow-sm" 
                        placeholder="••••••••">
                </div>
            </div>

            <button type="submit" 
                class="w-full bg-slate-900 hover:bg-blue-700 text-white font-bold py-4 rounded-2xl shadow-xl shadow-slate-200 transition-all transform active:scale-[0.98] flex justify-center items-center gap-3 mt-8">
                <span>เข้าสู่ระบบ</span> <i class="fa-solid fa-arrow-right-to-bracket"></i>
            </button>
        </form>

        <div class="mt-8 pt-6 border-t border-slate-100 flex flex-col items-center gap-6">
            
            <!-- Developer Credit (เพิ่มใหม่) -->
            <div class="group flex items-center justify-center gap-2 text-xs">
                <span class="text-slate-400">พัฒนาระบบโดย</span>
                <a href="https://keexlab-th.github.io/" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-slate-200 rounded-full shadow-sm hover:shadow-md hover:border-orange-200 hover:bg-orange-50 transition-all duration-300 no-underline">
                    <span class="w-2 h-2 rounded-full bg-gradient-to-r from-blue-500 to-orange-500 animate-pulse"></span>
                    <span class="font-bold text-slate-700 group-hover:text-orange-600 transition-colors">ธนภูมิ แดงประดับ</span>
                    <i class="fa-solid fa-arrow-up-right-from-square text-[10px] text-slate-300 group-hover:text-orange-400"></i>
                </a>
            </div>

            <p class="text-center text-[10px] text-slate-300 uppercase tracking-widest">&copy; 2026 Mentra Solution Co., Ltd.</p>
        </div>
    </div>

    <script type="module">
        import { initializeApp } from "https://www.gstatic.com/firebasejs/11.6.1/firebase-app.js";
        import { getAuth, signInWithEmailAndPassword, signInAnonymously } from "https://www.gstatic.com/firebasejs/11.6.1/firebase-auth.js";
        import { getFirestore, doc, getDoc, collection, query, where, getDocs } from "https://www.gstatic.com/firebasejs/11.6.1/firebase-firestore.js";

        // --- Config (ใส่ fallback ไว้ให้กันเหนียว) ---
        let firebaseConfig;
        try {
            if (typeof __firebase_config !== 'undefined') {
                firebaseConfig = JSON.parse(__firebase_config);
            }
        } catch (e) { console.log("Using fallback config"); }

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
        const auth = getAuth(app);
        const db = getFirestore(app);
        const appId = typeof __app_id !== 'undefined' ? __app_id : 'default-bom-app';

        // ล้างสิทธิ์เก่าเมื่อเข้าหน้า Login
        localStorage.removeItem('mentra_role');
        localStorage.removeItem('mentra_user');

        // ฟังก์ชันช่วยหา Email จาก Username
        async function getEmailByUsername(username) {
            // ต้อง sign in anonymous ก่อนถึงจะอ่าน database ได้ (ตาม rule ของระบบนี้)
            try { await signInAnonymously(auth); } catch(e) {}
            
            const usersRef = collection(db, 'artifacts', appId, 'public', 'data', 'user_registry');
            const q = query(usersRef, where("username", "==", username));
            const querySnapshot = await getDocs(q);

            if (!querySnapshot.empty) {
                return querySnapshot.docs[0].data().email;
            }
            return null;
        }

        document.getElementById('loginForm').onsubmit = async (e) => {
            e.preventDefault();
            let userInput = document.getElementById('userInput').value.trim();
            const password = document.getElementById('password').value;

            Swal.fire({ title: 'กำลังตรวจสอบ...', didOpen: () => Swal.showLoading() });

            // 2. ถ้าไม่ใช่ Email ให้ลองค้นหาว่าเป็น Username หรือไม่
            if (!userInput.includes('@')) {
                const foundEmail = await getEmailByUsername(userInput);
                if (foundEmail) {
                    userInput = foundEmail; // แทนที่ username ด้วย email ที่เจอ
                } else {
                    // ถ้าหาไม่เจอ ให้ลอง login ด้วย username ดิบๆ เผื่อฟลุ๊ค (หรือแจ้งเตือน)
                    // แต่ในที่นี้ถ้าไม่เจอ Username ใน DB ก็จะส่งไป auth แล้ว error กลับมาเอง
                }
            }
            
            // 3. Login ตามปกติ
            try {
                // Login กับ Firebase Auth
                const userCred = await signInWithEmailAndPassword(auth, userInput, password);
                const uid = userCred.user.uid;

                // ดึงข้อมูลสิทธิ์จาก Firestore
                const userRef = doc(db, 'artifacts', appId, 'public', 'data', 'user_registry', uid);
                const userSnap = await getDoc(userRef);

                if (userSnap.exists()) {
                    const userData = userSnap.data();
                    
                    if (userData.status === 'approved') {
                        // บันทึกสิทธิ์และชื่อลง LocalStorage
                        localStorage.setItem('mentra_role', userData.role);
                        localStorage.setItem('mentra_user', JSON.stringify({ name: userData.name, role: userData.role, uid: uid }));
                        
                        Swal.fire({ 
                            icon: 'success', 
                            title: 'เข้าสู่ระบบสำเร็จ', 
                            text: `สวัสดีคุณ ${userData.name}`, 
                            timer: 1000, 
                            showConfirmButton: false 
                        }).then(() => window.location.href = 'bom.php');
                    } else {
                        Swal.fire('รอการอนุมัติ', 'บัญชีของคุณกำลังรอ Admin ตรวจสอบสิทธิ์', 'warning');
                        auth.signOut();
                    }
                } else {
                    Swal.fire('ไม่พบข้อมูล', 'ไม่พบข้อมูลสิทธิ์ของคุณในระบบ กรุณาติดต่อ Admin', 'error');
                    auth.signOut();
                }
            } catch (err) {
                console.error(err);
                Swal.fire({
                    icon: 'error', title: 'ข้อมูลไม่ถูกต้อง',
                    text: 'ชื่อผู้ใช้งาน/อีเมล หรือรหัสผ่านไม่ถูกต้อง',
                    confirmButtonColor: '#0f172a'
                });
            }
        };
    </script>
</body>
</html>