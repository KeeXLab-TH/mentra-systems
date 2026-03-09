<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Mentra System</title>

    <!-- Libraries -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- GSAP / Animations -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://unpkg.com/split-type"></script>

    <style>
        :root {
            --ease-expo: cubic-bezier(0.22, 1, 0.36, 1);
            --ease-back: cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        * {
            box-sizing: border-box;
            -webkit-font-smoothing: antialiased;
        }

        body {
            font-family: 'Prompt', sans-serif;
            background-color: #f8fafc;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            margin: 0;
        }

        /* ── Split Layout ── */
        .login-wrapper {
            display: flex;
            width: 100%;
            height: 100vh;
            overflow: hidden;
        }

        .login-left {
            flex: 1;
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%);
            position: relative;
            display: none;
            overflow: hidden;
        }

        @media (min-width: 1024px) {
            .login-left {
                display: flex;
                flex-direction: column;
                justify-content: center;
                padding: 4rem;
            }
        }

        .login-left::before {
            content: '';
            position: absolute;
            inset: 0;
            background: url('https://images.unsplash.com/photo-1503387762-592deb58ef4e?q=80&w=2071&auto=format&fit=crop') center/cover;
            opacity: 0.2;
            mix-blend-mode: luminosity;
        }

        .login-left::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(to right, rgba(15, 23, 42, 0.8) 0%, rgba(15, 23, 42, 0.2) 100%);
        }

        .left-content {
            position: relative;
            z-index: 10;
            color: white;
            max-width: 480px;
        }

        .login-right {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            position: relative;
            background: radial-gradient(circle at top right, #fff5f5 0%, #f8fafc 100%);
        }

        /* ── Glassmorphic Form Card ── */
        .login-card {
            width: 100%;
            max-width: 440px;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.6);
            border-radius: 1.5rem;
            padding: 2.5rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.05), 0 0px 15px rgba(249, 115, 22, 0.05);
            position: relative;
            z-index: 10;
        }

        .floating-shape {
            position: absolute;
            border-radius: 50%;
            filter: blur(60px);
            z-index: 0;
            opacity: 0.5;
            animation: float 10s infinite alternate ease-in-out;
        }

        .shape-1 {
            width: 300px;
            height: 300px;
            background: #fb923c;
            top: -100px;
            right: -100px;
            animation-delay: 0s;
        }

        .shape-2 {
            width: 250px;
            height: 250px;
            background: #60a5fa;
            bottom: -50px;
            left: -100px;
            animation-delay: -5s;
        }

        @keyframes float {
            0% {
                transform: translateY(0) scale(1);
            }

            100% {
                transform: translateY(30px) scale(1.1);
            }
        }

        /* ── Form Inputs ── */
        .input-group {
            position: relative;
            margin-bottom: 1.5rem;
        }

        .input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            transition: color 0.3s;
        }

        .input-field {
            width: 100%;
            background: #f8fafc;
            border: 2px solid #e2e8f0;
            border-radius: 1rem;
            padding: 1rem 1rem 1rem 3rem;
            font-size: 0.95rem;
            color: #1e293b;
            transition: all 0.3s;
        }

        .input-field:focus {
            outline: none;
            border-color: #f97316;
            background: white;
            box-shadow: 0 0 0 4px rgba(249, 115, 22, 0.1);
        }

        .input-field:focus+.input-icon {
            color: #f97316;
        }

        .btn-primary {
            width: 100%;
            background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
            color: white;
            border: none;
            border-radius: 1rem;
            padding: 1rem;
            font-weight: 700;
            font-size: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            cursor: pointer;
            transition: all 0.3s var(--ease-expo);
            box-shadow: 0 10px 20px -5px rgba(234, 88, 12, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 25px -5px rgba(234, 88, 12, 0.4);
        }

        .btn-primary:active {
            transform: translateY(1px);
        }

        /* Initial GSAP States */
        .gsap-reveal {
            opacity: 0;
            transform: translateY(20px);
        }
    </style>
</head>

<body>

    <div class="login-wrapper">
        <!-- ═══ LEFT SIDE (Brand Intro) ═══ -->
        <div class="login-left">
            <div class="left-content">
                <div class="gsap-reveal mb-6 inline-flex p-3 rounded-2xl bg-white shadow-lg border border-slate-100">
                    <img src="Mentra_Solution_Tranparency.png"
                        onerror="this.src='https://cdn-icons-png.flaticon.com/512/2881/2881142.png'" alt="Mentra Logo"
                        class="w-16 h-16 object-contain">
                </div>

                <h1 class="gsap-reveal text-4xl md:text-5xl font-bold mb-4 leading-tight">สมาร์ทกว่า เร็วกว่า<br><span
                        class="text-orange-400">แม่นยำทุกโครงการ</span></h1>

                <p class="gsap-reveal text-slate-300 text-lg mb-8 leading-relaxed max-w-lg">เข้าสู่ระบบการจัดการ BOM และ
                    Drawing ที่ทันสมัยที่สุด เพื่อควบคุมต้นทุนและวัสดุการสร้างของคุณ</p>

                <div class="gsap-reveal flex items-center gap-4 text-sm font-medium text-slate-400">
                    <div class="flex items-center gap-2"><i class="fa-solid fa-cloud text-blue-400"></i> Cloud Sync
                    </div>
                    <div class="w-1 h-1 rounded-full bg-slate-600"></div>
                    <div class="flex items-center gap-2"><i class="fa-solid fa-shield-halved text-green-400"></i>
                        Secure
                    </div>
                    <div class="w-1 h-1 rounded-full bg-slate-600"></div>
                    <div class="flex items-center gap-2"><i class="fa-solid fa-bolt text-orange-400"></i> Real-time
                    </div>
                </div>
            </div>
        </div>

        <!-- ═══ RIGHT SIDE (Login Form) ═══ -->
        <div class="login-right">
            <!-- Decorative blur shapes -->
            <div class="floating-shape shape-1"></div>
            <div class="floating-shape shape-2"></div>

            <div class="login-card">
                <!-- Mobile Logo (Shows only on small screens) -->
                <div class="lg:hidden text-center mb-6 gsap-form-el">
                    <div
                        class="w-16 h-16 bg-white rounded-2xl shadow-sm border border-slate-100 flex items-center justify-center mx-auto mb-3 p-2">
                        <img src="Mentra_Solution_Tranparency.png"
                            onerror="this.src='https://cdn-icons-png.flaticon.com/512/2881/2881142.png'"
                            alt="Mentra Logo" class="w-full h-full object-contain">
                    </div>
                </div>

                <div class="text-center mb-8 gsap-form-el">
                    <h2 class="text-2xl font-bold text-slate-800 tracking-tight">ยินดีต้อนรับ</h2>
                    <p class="text-slate-500 text-sm mt-1">กรุณาเข้าสู่ระบบเพื่อใช้งาน Mentra BOM Systems</p>
                </div>

                <form id="loginForm">
                    <div class="input-group gsap-form-el">
                        <label
                            class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5 ml-1">ชื่อผู้ใช้งาน
                            หรือ อีเมล</label>
                        <div class="relative">
                            <input type="text" id="userInput" required class="input-field peer"
                                placeholder="admin หรือ user@example.com">
                            <i class="fa-solid fa-user input-icon peer-focus:text-orange-500"></i>
                        </div>
                    </div>

                    <div class="input-group gsap-form-el">
                        <label
                            class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5 ml-1 flex justify-between">
                            <span>รหัสผ่าน</span>
                        </label>
                        <div class="relative">
                            <input type="password" id="password" required class="input-field peer"
                                placeholder="••••••••">
                            <i class="fa-solid fa-lock input-icon peer-focus:text-orange-500"></i>
                        </div>
                    </div>

                    <div class="gsap-form-el mt-8">
                        <button type="submit" class="btn-primary">
                            <span>เข้าสู่ระบบ</span>
                            <i class="fa-solid fa-arrow-right-to-bracket"></i>
                        </button>
                    </div>
                </form>

                <div class="mt-8 pt-6 border-t border-slate-100 flex flex-col items-center gap-4 gsap-form-el">
                    <div class="group flex items-center justify-center gap-2 text-xs">
                        <span class="text-slate-400">พัฒนาระบบโดย</span>
                        <a href="https://keexlab-th.github.io/" target="_blank"
                            class="inline-flex items-center gap-1.5 px-3 py-1 bg-slate-50 border border-slate-200 rounded-full hover:border-orange-300 hover:text-orange-600 transition-all font-semibold text-slate-600 no-underline">
                            ธนภูมิ แดงประดับ
                            <i
                                class="fa-solid fa-arrow-up-right-from-square text-[10px] text-slate-400 group-hover:text-orange-400"></i>
                        </a>
                    </div>
                    <p class="text-[10px] text-slate-400 font-medium">&copy; 2026 Mentra Solution Co., Ltd.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Script Logic -->
    <script type="module">
        import { initializeApp } from "https://www.gstatic.com/firebasejs/11.6.1/firebase-app.js";
        import { getAuth, signInWithEmailAndPassword, signInAnonymously } from "https://www.gstatic.com/firebasejs/11.6.1/firebase-auth.js";
        import { getFirestore, doc, getDoc, collection, query, where, getDocs } from "https://www.gstatic.com/firebasejs/11.6.1/firebase-firestore.js";

        let firebaseConfig;
        try { if (typeof __firebase_config !== 'undefined') firebaseConfig = JSON.parse(__firebase_config); } catch (e) { console.log("Using fallback config"); }
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

        // Clear previous session
        localStorage.removeItem('mentra_role');
        localStorage.removeItem('mentra_user');

        async function getEmailByUsername(username) {
            try { await signInAnonymously(auth); } catch (e) { }
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

            if (!userInput.includes('@')) {
                const foundEmail = await getEmailByUsername(userInput);
                if (foundEmail) userInput = foundEmail;
            }

            try {
                const userCred = await signInWithEmailAndPassword(auth, userInput, password);
                const uid = userCred.user.uid;
                const userRef = doc(db, 'artifacts', appId, 'public', 'data', 'user_registry', uid);
                const userSnap = await getDoc(userRef);

                if (userSnap.exists()) {
                    const userData = userSnap.data();
                    if (userData.status === 'approved') {
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
                Swal.fire({ icon: 'error', title: 'ข้อมูลไม่ถูกต้อง', text: 'ชื่อผู้ใช้งาน/อีเมล หรือรหัสผ่านไม่ถูกต้อง', confirmButtonColor: '#0f172a' });
            }
        };

        // GSAP Animations
        document.addEventListener('DOMContentLoaded', () => {
            gsap.set('.gsap-reveal', { opacity: 0, y: 30 });
            gsap.set('.gsap-form-el', { opacity: 0, x: 20 });
            gsap.set('.floating-shape', { scale: 0.8, opacity: 0 });

            const tl = gsap.timeline();

            // Left side reveal (if visible)
            if (window.innerWidth >= 1024) {
                tl.to('.gsap-reveal', {
                    opacity: 1,
                    y: 0,
                    duration: 0.8,
                    stagger: 0.15,
                    ease: "power3.out"
                });
            }

            // Right side (Form elements) reveal
            tl.to('.gsap-form-el', {
                opacity: 1,
                x: 0,
                duration: 0.8,
                stagger: 0.1,
                ease: "power3.out"
            }, "-=0.4");

            // Shapes reveal
            tl.to('.floating-shape', {
                scale: 1,
                opacity: 0.5,
                duration: 1.5,
                ease: "elastic.out(1, 0.5)"
            }, "-=0.5");
        });
    </script>
</body>

</html>