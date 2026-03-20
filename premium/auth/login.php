<?php
/**
 * LOGIN.PHP — Natsy Premiums · Clean Aesthetic
 */
require_once __DIR__ . '/../includes/koneksi.php';
if (isLoggedIn()) { header('Location: ' . BASE_URL . '/index.php'); exit; }

$error = '';
// Show session flash (e.g. from session timeout)
if (!empty($_SESSION['flash'])) {
    $error = $_SESSION['flash']['msg'] ?? '';
    unset($_SESSION['flash']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrfCheck();
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';

    // Rate limit check
    if (!rateLimitCheck($conn, 'login', 5, 900)) {
        $error = 'Terlalu banyak percobaan login. Coba lagi dalam 15 menit.';
        logLogin($conn, null, 'blocked');
    } elseif (empty($email)||empty($pass)) {
        $error = 'Email dan password wajib diisi.';
    } else {
        $stmt=$conn->prepare("SELECT * FROM users WHERE email=? LIMIT 1");
        $stmt->bind_param("s",$email);$stmt->execute();
        $user=$stmt->get_result()->fetch_assoc();$stmt->close();
        if ($user&&password_verify($pass,$user['password'])) {
            rateLimitReset($conn, 'login');
            logLogin($conn, $user['id'], 'success');
            $_SESSION['user_id']=$user['id'];
            $_SESSION['user']=['id'=>$user['id'],'name'=>$user['name'],'email'=>$user['email'],'phone'=>$user['phone'],'role'=>$user['role']];
            header('Location: ' . BASE_URL . '/index.php'); exit;
        } else {
            rateLimitRecord($conn, 'login');
            logLogin($conn, $user['id'] ?? null, 'failed');
            $error='Email atau password salah.';
        }
    }
}
$isDark = "(localStorage.getItem('theme')==='dark'||(!localStorage.getItem('theme')&&window.matchMedia('(prefers-color-scheme:dark)').matches))";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk — <?= SITE_NAME ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.1.0/fonts/remixicon.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config={darkMode:'class',theme:{extend:{fontFamily:{sans:['"Plus Jakarta Sans"','system-ui','sans-serif']}}}}</script>
    <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">
    <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
    <script>if(<?= $isDark ?>){document.documentElement.classList.add('dark');}</script>
    <style>
        *{font-family:'Plus Jakarta Sans',system-ui,sans-serif}
        :root {
            --bg-page: #F0FDF4; --card-bg: rgba(255,255,255,0.72);
            --card-border: rgba(22,163,74,0.1); --card-shadow: 0 8px 40px rgba(22,163,74,0.06);
            --input-bg: rgba(255,255,255,0.8); --input-border: #E5E7EB;
            --input-focus: rgba(34,197,94,0.2); --text: #111827; --text2: #374151;
            --muted: #9CA3AF; --divider: #E5E7EB; --surface: #F0FDF4;
        }
        .dark {
            --bg-page: #0B1120; --card-bg: rgba(30,41,59,0.6);
            --card-border: rgba(34,197,94,0.15); --card-shadow: 0 8px 40px rgba(0,0,0,0.3);
            --input-bg: rgba(15,29,50,0.7); --input-border: rgba(51,65,85,0.5);
            --input-focus: rgba(34,197,94,0.3); --text: #F1F5F9; --text2: #CBD5E1;
            --muted: #64748B; --divider: rgba(51,65,85,0.4); --surface: rgba(30,41,59,0.4);
        }
        body { min-height:100vh; background:var(--bg-page); display:flex; align-items:center; justify-content:center; }
        .auth-card {
            background: var(--card-bg); backdrop-filter: blur(24px) saturate(180%);
            -webkit-backdrop-filter: blur(24px) saturate(180%);
            border: 1px solid var(--card-border); box-shadow: var(--card-shadow);
        }
        .auth-input {
            width:100%; padding:14px 16px; border-radius:14px; font-size:14px;
            background:var(--input-bg); border:1.5px solid var(--input-border);
            color:var(--text); transition:all 0.2s ease; outline:none;
        }
        .auth-input:focus { border-color:#22C55E; box-shadow:0 0 0 3px var(--input-focus); }
        .auth-input::placeholder { color:var(--muted); }
        .btn-primary {
            width:100%; padding:14px; border-radius:14px; font-size:14px; font-weight:700;
            color:#fff; border:none; cursor:pointer; transition:all 0.2s;
            background:linear-gradient(135deg,#16A34A,#22C55E);
            box-shadow:0 4px 16px rgba(34,197,94,0.3);
        }
        .btn-primary:hover { box-shadow:0 6px 24px rgba(34,197,94,0.4); transform:translateY(-1px); }
        .btn-primary:active { transform:scale(0.98); }
        .btn-social {
            width:100%; padding:12px; border-radius:14px; font-size:13px; font-weight:600;
            border:1.5px solid var(--input-border); background:var(--input-bg);
            color:var(--text); cursor:pointer; transition:all 0.2s; display:flex;
            align-items:center; justify-content:center; gap:10px;
        }
        .btn-social:hover { border-color:#22C55E; background:rgba(34,197,94,0.05); }

        /* Decorative */
        .decor-orb { position:fixed; border-radius:50%; pointer-events:none; filter:blur(80px); opacity:0.35; z-index:0; }
        .orb-1 { top:-80px; right:-60px; width:400px; height:400px; background:radial-gradient(circle,rgba(34,197,94,0.2),transparent 70%); animation:oFloat 20s ease-in-out infinite; }
        .orb-2 { bottom:-100px; left:-80px; width:450px; height:450px; background:radial-gradient(circle,rgba(16,185,129,0.12),transparent 70%); animation:oFloat 25s ease-in-out infinite reverse; }
        .dark .orb-1 { background:radial-gradient(circle,rgba(34,197,94,0.08),transparent 70%); }
        .dark .orb-2 { background:radial-gradient(circle,rgba(16,185,129,0.05),transparent 70%); }
        @keyframes oFloat { 0%,100%{transform:translate(0,0)}50%{transform:translate(-20px,30px)} }
    </style>
</head>
<body class="antialiased p-4 sm:p-6">
    <div class="decor-orb orb-1"></div>
    <div class="decor-orb orb-2"></div>

    <div class="w-full max-w-[400px] relative z-10" data-aos="fade-up" data-aos-duration="600">
        <!-- Brand -->
        <div class="text-center mb-8">
            <a href="index.php" class="inline-flex items-center gap-2.5 group">
                <div class="w-11 h-11 rounded-2xl bg-green-600 flex items-center justify-center text-white text-sm font-black shadow-lg shadow-green-600/25 group-hover:scale-105 transition-transform">N</div>
                <div class="flex flex-col leading-none text-left"><span class="text-lg font-extrabold" style="color:var(--text)">Natsy</span><span class="text-[9px] font-bold text-green-500 tracking-[0.2em] uppercase">Premiums</span></div>
            </a>
        </div>

        <!-- Card -->
        <div class="auth-card rounded-[24px] p-7 sm:p-8">
            <div class="text-center mb-6">
                <h1 class="text-xl font-black" style="color:var(--text)">Masuk ke Akunmu</h1>
                <p class="text-xs mt-1.5" style="color:var(--muted)">Selamat datang kembali!</p>
            </div>

            <?php if ($error): ?>
            <div class="flex items-center gap-2 px-4 py-3 rounded-xl bg-red-500/10 text-red-400 text-xs font-medium border border-red-500/10 mb-5"><i class="ri-error-warning-fill"></i> <?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <!-- Social Login -->
            <div class="space-y-2.5 mb-5">
                <button type="button" class="btn-social">
                    <svg width="18" height="18" viewBox="0 0 24 24"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 01-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>
                    Masuk dengan Google
                </button>
                <button type="button" class="btn-social">
                    <i class="ri-apple-fill text-lg"></i> Masuk dengan Apple
                </button>
            </div>

            <div class="flex items-center gap-3 mb-5"><div class="flex-1 h-px" style="background:var(--divider)"></div><span class="text-[10px] font-semibold" style="color:var(--muted)">ATAU</span><div class="flex-1 h-px" style="background:var(--divider)"></div></div>

            <!-- Email Form -->
            <form method="POST" class="space-y-4">
                <?= csrfField() ?>
                <div>
                    <label class="block text-xs font-semibold mb-1.5" style="color:var(--text)">Email</label>
                    <input type="email" name="email" required placeholder="nama@email.com" class="auth-input" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                </div>
                <div>
                    <label class="flex items-center justify-between text-xs font-semibold mb-1.5" style="color:var(--text)">
                        <span>Password</span>
                        <a href="forgot_password.php" class="text-green-500 font-medium text-[11px] hover:underline">Lupa password?</a>
                    </label>
                    <div class="relative">
                        <input type="password" name="password" id="passField" required placeholder="Masukkan password" class="auth-input" style="padding-right:44px">
                        <button type="button" onclick="togglePass()" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-sm hover:text-green-500 transition" style="color:var(--muted)"><i id="passEye" class="ri-eye-off-line"></i></button>
                    </div>
                </div>
                <button type="submit" class="btn-primary"><i class="ri-login-box-line mr-1"></i> Masuk</button>
            </form>

            <p class="text-center text-xs mt-5" style="color:var(--muted)">Belum punya akun? <a href="register.php" class="text-green-500 font-bold hover:underline">Daftar gratis</a></p>
        </div>

        <p class="text-center text-[10px] mt-4" style="color:var(--muted)">
            <a href="index.php" class="hover:text-green-500 transition"><i class="ri-arrow-left-s-line"></i> Kembali ke Beranda</a>
        </p>
    </div>

<script>
AOS.init({duration:500,once:true});
function togglePass(){
    var f=document.getElementById('passField'),e=document.getElementById('passEye');
    if(f.type==='password'){f.type='text';e.className='ri-eye-line';}
    else{f.type='password';e.className='ri-eye-off-line';}
}
</script>
</body>
</html>
