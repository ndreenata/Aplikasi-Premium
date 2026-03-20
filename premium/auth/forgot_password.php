<?php
/**
 * FORGOT_PASSWORD.PHP — Password Reset Request
 * Kirim token reset ke WhatsApp via Fonnte
 */
require_once __DIR__ . '/../includes/koneksi.php';

$msg = ''; $msgType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrfCheck();
    $email = trim($_POST['email'] ?? '');
    
    if (empty($email)) {
        $msg = 'Masukkan email kamu.'; $msgType = 'error';
    } else {
        // Cari user
        $stmt = $conn->prepare("SELECT id, name, phone FROM users WHERE email=? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        
        if ($user && !empty($user['phone'])) {
            // Generate reset token
            $token = bin2hex(random_bytes(32));
            $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            // Hapus token lama
            $stmt = $conn->prepare("DELETE FROM password_resets WHERE user_id=?");
            $stmt->bind_param("i", $user['id']);
            $stmt->execute();
            $stmt->close();
            
            // Simpan token baru
            $stmt = $conn->prepare("INSERT INTO password_resets (user_id, token, expires_at) VALUES (?,?,?)");
            $stmt->bind_param("iss", $user['id'], $token, $expiry);
            $stmt->execute();
            $stmt->close();
            
            // Kirim via WA
            $resetLink = BASE_URL . "/reset_password.php?token=" . $token;
            $phone = $user['phone'];
            if (substr($phone,0,1)==='0') $phone='62'.substr($phone,1);
            elseif (substr($phone,0,2)!=='62') $phone='62'.$phone;
            
            $message = "Halo {$user['name']}! 👋\n\n"
                ."Kamu meminta reset password di *".SITE_NAME."*.\n\n"
                ."Klik link berikut untuk reset password:\n"
                ."{$resetLink}\n\n"
                ."⚠️ Link berlaku selama 1 jam.\n"
                ."Jika bukan kamu yang meminta, abaikan pesan ini.\n\n"
                ."— *".SITE_NAME."*";
            
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => 'https://api.fonnte.com/send',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => http_build_query([
                    'target' => $phone,
                    'message' => $message,
                    'countryCode' => '62'
                ]),
                CURLOPT_HTTPHEADER => ['Authorization: '.FONNTE_TOKEN]
            ]);
            $waResp = curl_exec($curl);
            $waErr = curl_error($curl);
            curl_close($curl);
            error_log("[FORGOT_PWD] Email:{$email} Phone:{$phone} | ".($waErr ?: "OK: {$waResp}"));
            
            $msg = 'Link reset password telah dikirim ke WhatsApp kamu! Cek pesan masuk.';
            $msgType = 'success';
        } else {
            // Security: jangan kasih tau apakah email ada atau tidak  
            $msg = 'Jika email terdaftar dan memiliki nomor WhatsApp, link reset akan dikirim.';
            $msgType = 'success';
        }
    }
}

$pageTitle = 'Lupa Password — ' . SITE_NAME;
include __DIR__ . '/../includes/header.php';
?>

<section class="py-12 sm:py-20 flex items-center justify-center min-h-[70vh]">
    <div class="w-full max-w-md mx-4">
        <div class="glass-strong rounded-3xl p-7 sm:p-9" data-aos="fade-up">
            <div class="text-center mb-6">
                <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center">
                    <i class="ri-lock-unlock-fill text-3xl text-amber-400"></i>
                </div>
                <h1 class="text-xl font-black" style="color:var(--text)">Lupa Password?</h1>
                <p class="text-xs mt-1" style="color:var(--muted)">Masukkan email yang terdaftar, link reset akan dikirim ke WhatsApp-mu</p>
            </div>

            <?php if ($msg): ?>
            <div class="rounded-xl p-3 mb-5 text-xs flex items-start gap-2 <?= $msgType==='success' ? 'bg-green-500/10 text-green-500' : 'bg-red-500/10 text-red-400' ?>">
                <i class="<?= $msgType==='success' ? 'ri-checkbox-circle-fill' : 'ri-error-warning-fill' ?> mt-0.5"></i>
                <span><?= htmlspecialchars($msg) ?></span>
            </div>
            <?php endif; ?>

            <form method="POST" class="space-y-4">
                <?= csrfField() ?>
                <div>
                    <label class="block text-xs font-semibold mb-1.5" style="color:var(--text)">Email</label>
                    <div class="relative">
                        <i class="ri-mail-line absolute left-3 top-1/2 -translate-y-1/2 text-sm" style="color:var(--muted)"></i>
                        <input type="email" name="email" required placeholder="email@contoh.com" class="auth-input" style="padding-left:36px">
                    </div>
                </div>
                <button type="submit" class="w-full flex items-center justify-center gap-2 py-3.5 bg-green-600 text-white text-sm font-bold rounded-2xl hover:bg-green-500 btn-press shadow-lg shadow-green-600/20">
                    <i class="ri-send-plane-fill"></i> Kirim Link Reset
                </button>
            </form>

            <div class="mt-5 text-center">
                <a href="login.php" class="text-[11px] font-medium hover:underline" style="color:var(--muted)"><i class="ri-arrow-left-line"></i> Kembali ke Login</a>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
