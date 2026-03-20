<?php
/**
 * REGISTER.PHP — Natsy Premiums · Clean Aesthetic · Extended Fields
 */
require_once __DIR__ . '/../includes/koneksi.php';
if (isLoggedIn()) { header('Location: ' . BASE_URL . '/index.php'); exit; }

$error=$success='';
if ($_SERVER['REQUEST_METHOD']==='POST') {
    csrfCheck();
    $name=trim($_POST['name']??'');
    $username=trim($_POST['username']??'');
    $email=trim($_POST['email']??'');
    $phone=preg_replace('/[^0-9]/','',($_POST['phone']??''));
    $birthdate=trim($_POST['birthdate']??'');
    $gender=trim($_POST['gender']??'');
    $address=trim($_POST['address']??'');
    $pass=$_POST['password']??'';
    $pass2=$_POST['password_confirm']??'';
    $agree=isset($_POST['agree']);

    if (empty($name)||empty($username)||empty($email)||empty($pass)) {
        $error='Semua field bertanda * wajib diisi.';
    } elseif (strlen($pass)<6) {
        $error='Password minimal 6 karakter.';
    } elseif ($pass!==$pass2) {
        $error='Konfirmasi password tidak cocok.';
    } elseif (!$agree) {
        $error='Kamu harus menyetujui Syarat & Ketentuan.';
    } elseif (!preg_match('/^[a-zA-Z0-9_]{3,30}$/',$username)) {
        $error='Username hanya boleh huruf, angka, underscore (3-30 karakter).';
    } else {
        // Check email
        $chk=$conn->prepare("SELECT id FROM users WHERE email=? LIMIT 1");
        $chk->bind_param("s",$email);$chk->execute();
        if ($chk->get_result()->num_rows>0) { $error='Email sudah terdaftar.'; $chk->close(); }
        else {
            $chk->close();
            // Check username
            $chk2=$conn->prepare("SELECT id FROM users WHERE username=? LIMIT 1");
            $chk2->bind_param("s",$username);$chk2->execute();
            if ($chk2->get_result()->num_rows>0) { $error='Username sudah digunakan.'; }
            else {
                $hash=password_hash($pass,PASSWORD_DEFAULT);
                if(!empty($phone)){if(substr($phone,0,1)==='0')$phone='62'.substr($phone,1);elseif(substr($phone,0,2)!=='62')$phone='62'.$phone;}
                $bdate=$birthdate?:null;
                $gen=in_array($gender,['male','female','other'])?$gender:null;
                $addr=$address?:null;
                $ins=$conn->prepare("INSERT INTO users (name,username,email,password,phone,birthdate,gender,address) VALUES (?,?,?,?,?,?,?,?)");
                $ins->bind_param("ssssssss",$name,$username,$email,$hash,$phone,$bdate,$gen,$addr);
                $ins->execute();$ins->close();
                $success='Akun berhasil dibuat! Silakan masuk.';
            }
            $chk2->close();
        }
    }
}
$isDark = "(localStorage.getItem('theme')==='dark'||(!localStorage.getItem('theme')&&window.matchMedia('(prefers-color-scheme:dark)').matches))";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar — <?= SITE_NAME ?></title>
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
            width:100%; padding:12px 14px; border-radius:12px; font-size:13px;
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
            width:100%; padding:11px; border-radius:12px; font-size:13px; font-weight:600;
            border:1.5px solid var(--input-border); background:var(--input-bg);
            color:var(--text); cursor:pointer; transition:all 0.2s; display:flex;
            align-items:center; justify-content:center; gap:8px;
        }
        .btn-social:hover { border-color:#22C55E; background:rgba(34,197,94,0.05); }
        .decor-orb { position:fixed; border-radius:50%; pointer-events:none; filter:blur(80px); opacity:0.35; z-index:0; }
        .orb-1 { top:-80px; left:-60px; width:400px; height:400px; background:radial-gradient(circle,rgba(34,197,94,0.2),transparent 70%); animation:oFloat 20s ease-in-out infinite; }
        .orb-2 { bottom:-100px; right:-80px; width:450px; height:450px; background:radial-gradient(circle,rgba(16,185,129,0.12),transparent 70%); animation:oFloat 25s ease-in-out infinite reverse; }
        .dark .orb-1{background:radial-gradient(circle,rgba(34,197,94,0.08),transparent 70%)}
        .dark .orb-2{background:radial-gradient(circle,rgba(16,185,129,0.05),transparent 70%)}
        @keyframes oFloat{0%,100%{transform:translate(0,0)}50%{transform:translate(-20px,30px)}}
        select.auth-input { appearance:none; background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%239CA3AF' stroke-width='2'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E"); background-repeat:no-repeat; background-position:right 14px center; padding-right:36px; }
    </style>
</head>
<body class="antialiased p-4 sm:p-6">
    <div class="decor-orb orb-1"></div>
    <div class="decor-orb orb-2"></div>

    <div class="w-full max-w-[480px] relative z-10" data-aos="fade-up" data-aos-duration="600">
        <!-- Brand -->
        <div class="text-center mb-6">
            <a href="index.php" class="inline-flex items-center gap-2.5 group">
                <div class="w-10 h-10 rounded-2xl bg-green-600 flex items-center justify-center text-white text-sm font-black shadow-lg shadow-green-600/25 group-hover:scale-105 transition-transform">N</div>
                <div class="flex flex-col leading-none text-left"><span class="text-lg font-extrabold" style="color:var(--text)">Natsy</span><span class="text-[9px] font-bold text-green-500 tracking-[0.2em] uppercase">Premiums</span></div>
            </a>
        </div>

        <!-- Card -->
        <div class="auth-card rounded-[24px] p-6 sm:p-7">
            <div class="text-center mb-5">
                <h1 class="text-xl font-black" style="color:var(--text)">Buat Akun Baru</h1>
                <p class="text-xs mt-1" style="color:var(--muted)">Daftar gratis dan mulai belanja</p>
            </div>

            <?php if ($error): ?>
            <div class="flex items-center gap-2 px-3.5 py-3 rounded-xl bg-red-500/10 text-red-400 text-xs font-medium border border-red-500/10 mb-4"><i class="ri-error-warning-fill"></i> <?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
            <div class="flex items-center gap-2 px-3.5 py-3 rounded-xl bg-green-500/10 text-green-500 text-xs font-medium border border-green-500/10 mb-4"><i class="ri-check-double-fill"></i> <?= htmlspecialchars($success) ?> <a href="login.php" class="ml-auto font-bold hover:underline">Masuk &rarr;</a></div>
            <?php endif; ?>

            <!-- Social -->
            <div class="grid grid-cols-2 gap-2.5 mb-4">
                <button type="button" class="btn-social">
                    <svg width="16" height="16" viewBox="0 0 24 24"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 01-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>
                    Google
                </button>
                <button type="button" class="btn-social">
                    <i class="ri-apple-fill text-base"></i> Apple
                </button>
            </div>

            <div class="flex items-center gap-3 mb-4"><div class="flex-1 h-px" style="background:var(--divider)"></div><span class="text-[10px] font-semibold" style="color:var(--muted)">ATAU ISI MANUAL</span><div class="flex-1 h-px" style="background:var(--divider)"></div></div>

            <!-- Form -->
            <form method="POST" class="space-y-3">
                <?= csrfField() ?>
                <!-- Row 1: Name + Username -->
                <div class="grid grid-cols-2 gap-2.5">
                    <div>
                        <label class="block text-[11px] font-semibold mb-1" style="color:var(--text)">Nama Lengkap <span class="text-red-400">*</span></label>
                        <input type="text" name="name" required placeholder="John Doe" class="auth-input" value="<?= htmlspecialchars($_POST['name']??'') ?>">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold mb-1" style="color:var(--text)">Username <span class="text-red-400">*</span></label>
                        <input type="text" name="username" required placeholder="johndoe" pattern="[a-zA-Z0-9_]{3,30}" class="auth-input" value="<?= htmlspecialchars($_POST['username']??'') ?>">
                    </div>
                </div>
                <!-- Email -->
                <div>
                    <label class="block text-[11px] font-semibold mb-1" style="color:var(--text)">Email <span class="text-red-400">*</span></label>
                    <input type="email" name="email" required placeholder="nama@email.com" class="auth-input" value="<?= htmlspecialchars($_POST['email']??'') ?>">
                </div>
                <!-- Row 2: Phone + Birthdate -->
                <div class="grid grid-cols-2 gap-2.5">
                    <div>
                        <label class="block text-[11px] font-semibold mb-1" style="color:var(--text)">No. WhatsApp</label>
                        <input type="tel" name="phone" placeholder="08123456789" class="auth-input" value="<?= htmlspecialchars($_POST['phone']??'') ?>">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold mb-1" style="color:var(--text)">Tanggal Lahir</label>
                        <input type="date" name="birthdate" class="auth-input" value="<?= htmlspecialchars($_POST['birthdate']??'') ?>">
                    </div>
                </div>
                <!-- Row 3: Gender + Address -->
                <div class="grid grid-cols-2 gap-2.5">
                    <div>
                        <label class="block text-[11px] font-semibold mb-1" style="color:var(--text)">Jenis Kelamin</label>
                        <select name="gender" class="auth-input">
                            <option value="">Pilih</option>
                            <option value="male" <?= ($_POST['gender']??'')==='male'?'selected':'' ?>>Laki-laki</option>
                            <option value="female" <?= ($_POST['gender']??'')==='female'?'selected':'' ?>>Perempuan</option>
                            <option value="other" <?= ($_POST['gender']??'')==='other'?'selected':'' ?>>Lainnya</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold mb-1" style="color:var(--text)">Kota / Alamat</label>
                        <input type="text" name="address" placeholder="Alamat lengkap" class="auth-input" value="<?= htmlspecialchars($_POST['address']??'') ?>">
                    </div>
                </div>
                <!-- Row 4: Password + Confirm -->
                <div class="grid grid-cols-2 gap-2.5">
                    <div>
                        <label class="block text-[11px] font-semibold mb-1" style="color:var(--text)">Password <span class="text-red-400">*</span></label>
                        <div class="relative">
                            <input type="password" name="password" id="passField" required placeholder="Min. 6 karakter" minlength="6" class="auth-input" style="padding-right:36px">
                            <button type="button" onclick="togglePass()" class="absolute right-3 top-1/2 -translate-y-1/2 text-xs hover:text-green-500 transition" style="color:var(--muted)"><i id="passEye" class="ri-eye-off-line"></i></button>
                        </div>
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold mb-1" style="color:var(--text)">Konfirmasi <span class="text-red-400">*</span></label>
                        <input type="password" name="password_confirm" required placeholder="Ulangi password" minlength="6" class="auth-input">
                    </div>
                </div>
                <!-- Terms -->
                <label class="flex items-start gap-2 cursor-pointer mt-1">
                    <input type="checkbox" name="agree" class="w-4 h-4 rounded accent-green-600 mt-0.5 shrink-0" required>
                    <span class="text-[11px] leading-relaxed" style="color:var(--muted)">Saya setuju dengan <a href="terms.php" target="_blank" class="text-green-500 font-semibold hover:underline">Syarat &amp; Ketentuan</a> dan <a href="privacy.php" target="_blank" class="text-green-500 font-semibold hover:underline">Kebijakan Privasi</a></span>
                </label>
                <button type="submit" class="btn-primary mt-1"><i class="ri-user-add-line mr-1"></i> Daftar Sekarang</button>
            </form>

            <p class="text-center text-xs mt-4" style="color:var(--muted)">Sudah punya akun? <a href="login.php" class="text-green-500 font-bold hover:underline">Masuk</a></p>
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

// ═══ KONFETTI ANIMATION ═══
<?php if (!empty($success)): ?>
(function(){
    var canvas=document.createElement('canvas');
    canvas.style.cssText='position:fixed;top:0;left:0;width:100%;height:100%;pointer-events:none;z-index:9999';
    document.body.appendChild(canvas);
    var ctx=canvas.getContext('2d');
    canvas.width=window.innerWidth;canvas.height=window.innerHeight;
    var particles=[];
    var colors=['#22C55E','#10B981','#FBBF24','#06B6D4','#A78BFA','#F472B6'];
    for(var i=0;i<120;i++){
        particles.push({
            x:canvas.width/2+((Math.random()-0.5)*200),
            y:canvas.height/2,
            vx:(Math.random()-0.5)*12,
            vy:Math.random()*-14-4,
            w:Math.random()*8+4,
            h:Math.random()*6+2,
            color:colors[Math.floor(Math.random()*colors.length)],
            rotation:Math.random()*360,
            rotSpeed:(Math.random()-0.5)*10,
            gravity:0.15+Math.random()*0.1,
            opacity:1
        });
    }
    function animate(){
        ctx.clearRect(0,0,canvas.width,canvas.height);
        var alive=false;
        particles.forEach(function(p){
            if(p.opacity<=0) return;
            alive=true;
            p.x+=p.vx; p.y+=p.vy; p.vy+=p.gravity;
            p.rotation+=p.rotSpeed;
            p.opacity-=0.006;
            p.vx*=0.99;
            ctx.save();
            ctx.translate(p.x,p.y);
            ctx.rotate(p.rotation*Math.PI/180);
            ctx.globalAlpha=Math.max(0,p.opacity);
            ctx.fillStyle=p.color;
            ctx.fillRect(-p.w/2,-p.h/2,p.w,p.h);
            ctx.restore();
        });
        if(alive) requestAnimationFrame(animate);
        else { canvas.remove(); }
    }
    animate();
})();
<?php endif; ?>
</script>
</body>
</html>
