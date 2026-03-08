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
            background-color: #0f172a;
            background-image:
                radial-gradient(at 0% 0%, rgba(37, 99, 235, 0.25) 0px, transparent 60%),
                radial-gradient(at 100% 100%, rgba(249, 115, 22, 0.2) 0px, transparent 60%),
                radial-gradient(at 50% 50%, rgba(99, 102, 241, 0.08) 0px, transparent 70%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            letter-spacing: 0.01em;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.82);
            backdrop-filter: blur(20px) saturate(180%);
            -webkit-backdrop-filter: blur(20px) saturate(180%);
            border: 1px solid rgba(255, 255, 255, 0.4);
            transform-style: preserve-3d;
            will-change: transform;
            opacity: 0;
        }

        .noise-overlay {
            position: fixed;
            inset: 0;
            z-index: 9999;
            pointer-events: none;
            background-image: url('data:image/svg+xml,%3Csvg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg"%3E%3Cfilter id="noiseFilter"%3E%3CfeTurbulence type="fractalNoise" baseFrequency="0.65" numOctaves="3" stitchTiles="stitch"/%3E%3C/filter%3E%3Crect width="100%25" height="100%25" filter="url(%23noiseFilter)"/%3E%3C/svg%3E');
            opacity: 0.035;
            mix-blend-mode: multiply;
        }

        .char {
            transform: translateY(115%);
            transition: transform 0.5s;
        }

        .header-title-clip {
            clip-path: polygon(0 0, 100% 0, 100% 100%, 0% 100%);
        }

        .btn-primary {
            position: relative;
            overflow: hidden;
            transition: transform 0.25s var(--ease-expo), box-shadow 0.25s var(--ease-expo), background-color 0.25s var(--ease-expo);
            will-change: transform;
        }
    </style>
</head>

<body>
    <!-- Noise Texture Overlay -->
    <div class="noise-overlay"></div>

    <!-- Orbs for Parallax BG -->
    <div
        class="orb orb-1 absolute rounded-full opacity-30 blur-[90px] w-[55vw] h-[55vw] top-[-25%] left-[-20%] bg-gradient-to-tr from-blue-500 to-blue-700 pointer-events-none -z-10">
    </div>
    <div
        class="orb orb-2 absolute rounded-full opacity-30 blur-[90px] w-[50vw] h-[50vw] bottom-[-25%] right-[-20%] bg-gradient-to-tr from-orange-400 to-orange-600 pointer-events-none -z-10">
    </div>
    <div
        class="orb orb-3 absolute rounded-full opacity-15 blur-[90px] w-[30vw] h-[30vw] top-[30%] left-[40%] bg-gradient-to-tr from-purple-500 to-purple-700 pointer-events-none -z-10">
    </div>

    <!-- Container for Tilt Effect -->
    <div class="tilt-container perspective-1000 w-full max-w-md mx-4 pb-24 md:pb-0 pt-8 md:pt-0">
        <div
            class="login-card w-full p-8 rounded-[2rem] shadow-[0_20px_50px_rgba(0,0,0,0.15)] relative overflow-visible">
            <!-- แถบสีด้านบน -->
            <div
                class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-blue-600 to-orange-500 rounded-t-[2rem]">
            </div>

            <!-- Logo & Header -->
            <div class="text-center mb-8 title-enter">
                <div
                    class="w-20 h-20 bg-white rounded-2xl shadow-md flex items-center justify-center mx-auto mb-4 p-2 logo-enter">
                    <img src="Mentra_Solution_Tranparency.png"
                        onerror="this.src='https://cdn-icons-png.flaticon.com/512/2881/2881142.png'" alt="Mentra Logo"
                        class="w-full h-full object-contain">
                </div>
                <div class="header-title-clip overflow-hidden">
                    <h1 id="brandTitle" class="text-2xl font-bold text-slate-800 tracking-tight">Mentra Solution Co.,
                        Ltd</h1>
                </div>
                <div class="header-title-clip overflow-hidden mt-1.5">
                    <p id="subTitle" class="text-slate-400 text-[11px] uppercase tracking-[0.25em] font-bold">BOM
                        Manager Access</p>
                </div>
            </div>

            <!-- Login Form -->
            <form id="loginForm" class="space-y-4">
                <div class="field-enter">
                    <label
                        class="block text-[11px] font-bold text-slate-400 uppercase tracking-[0.12em] mb-1.5 ml-1">ชื่อผู้ใช้งาน
                        / Username / Email</label>
                    <div class="relative group">
                        <span
                            class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-indigo-500 transition-colors">
                            <i class="fa-solid fa-user text-xs"></i>
                        </span>
                        <input type="text" id="userInput" required
                            class="input-field w-full pl-11 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-xl text-sm placeholder-slate-300 shadow-sm"
                            placeholder="admin หรือ user@example.com">
                    </div>
                </div>

                <div class="field-enter">
                    <label
                        class="block text-[11px] font-bold text-slate-400 uppercase tracking-[0.12em] mb-1.5 ml-1">รหัสผ่าน
                        (Password)</label>
                    <div class="relative group">
                        <span
                            class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-orange-500 transition-colors">
                            <i class="fa-solid fa-lock text-xs"></i>
                        </span>
                        <input type="password" id="password" required
                            class="input-field w-full pl-11 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-xl text-sm placeholder-slate-300 shadow-sm"
                            placeholder="••••••••">
                    </div>
                </div>

                <div class="field-enter">
                    <button type="submit"
                        class="magnetic btn-primary w-full bg-slate-900 text-white font-bold py-4 rounded-2xl shadow-[0_10px_20px_rgba(15,23,42,0.15)] flex justify-center items-center gap-3 mt-4">
                        <span class="tracking-wide">เข้าสู่ระบบ</span>
                        <i class="fa-solid fa-arrow-right-to-bracket"></i>
                    </button>
                </div>
            </form>

            <div class="mt-8 pt-6 border-t border-slate-100 flex flex-col items-center gap-6">
                <!-- Developer Credit -->
                <div class="group flex items-center justify-center gap-2 text-xs">
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
                <p class="text-center text-[10px] text-slate-500 uppercase tracking-widest font-medium">&copy; 2026
                    Mentra Solution Co., Ltd.</p>
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

        // GSAP & High-End Interactions
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof SplitType !== 'undefined') {
                const title = new SplitType('#brandTitle', { types: 'chars' });
                const subtitle = new SplitType('#subTitle', { types: 'chars' });

                gsap.set('.login-card', { opacity: 0, y: 40 });
                gsap.set('.logo-enter', { opacity: 0, scale: 0.5, y: 20 });
                gsap.set('.field-enter', { opacity: 0, y: 15 });

                const masterTl = gsap.timeline();
                masterTl.to('.login-card', { opacity: 1, y: 0, duration: 1, ease: 'expo.out' })
                    .to('.logo-enter', { opacity: 1, scale: 1, y: 0, duration: 0.8, ease: 'back.out(1.5)' }, "-=0.6")
                    .to(title.chars, { y: 0, duration: 0.8, stagger: 0.02, ease: 'expo.out' }, "-=0.6")
                    .to(subtitle.chars, { y: 0, duration: 0.6, stagger: 0.015, ease: 'expo.out' }, "-=0.6")
                    .to('.field-enter', { opacity: 1, y: 0, duration: 0.8, stagger: 0.1, ease: 'expo.out' }, "-=0.4");
            } else {
                gsap.to('.login-card, .logo-enter, .field-enter', { opacity: 1, y: 0, scale: 1, duration: 0.5 });
            }

            if (window.innerWidth >= 768) {
                const magneticBtn = document.querySelector('.magnetic');
                if (magneticBtn) {
                    magneticBtn.addEventListener('mousemove', function (e) {
                        const rect = this.getBoundingClientRect();
                        const x = e.clientX - rect.left - rect.width / 2;
                        const y = e.clientY - rect.top - rect.height / 2;
                        gsap.to(this, { x: x * 0.25, y: y * 0.25, duration: 0.3, ease: 'power2.out' });
                    });

                    magneticBtn.addEventListener('mouseleave', function () {
                        gsap.to(this, { x: 0, y: 0, duration: 0.7, ease: 'elastic.out(1, 0.3)' });
                    });
                }
            }

            document.addEventListener('mousemove', (e) => {
                const x = (e.clientX / window.innerWidth - 0.5) * 2;
                const y = (e.clientY / window.innerHeight - 0.5) * 2;

                gsap.to('.orb-1', { x: x * 30, y: y * 30, duration: 1.5, ease: 'power2.out' });
                gsap.to('.orb-2', { x: x * -40, y: y * -40, duration: 1.5, ease: 'power2.out' });
                gsap.to('.orb-3', { x: x * 20, y: y * -20, duration: 1.5, ease: 'power2.out' });
            });
        });
    </script>
</body>

</html>