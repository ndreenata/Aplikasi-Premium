<?php
/**
 * RESET_PASSWORD.PHP — Set New Password
 * Validasi token → Form password baru → Update
 */
require_once __DIR__ . '/../includes/koneksi.php';

$token = trim($_GET['token'] ?? '');
$msg = ''; $msgType = ''; $valid = false;

if (empty($token)) {
    $msg = 'Token tidak valid.'; $msgType = 'error';
} else {
    // Cek token
    $stmt = $conn->prepare("SELECT pr.*, u.name, u.email FROM password_resets pr JOIN users u ON pr.user_id=u.id WHERE pr.token=? AND pr.expires_at > NOW() LIMIT 1");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $reset = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    
    if (!$reset) {
        $msg = 'Token tidak valid atau sudah kedaluwarsa. Silakan minta link baru.';
        $msgType = 'error';
    } else {
        $valid = true;
    }
}

if ($valid && $_SERVER['REQUEST_METHOD'] === 'POST') {
    csrfCheck();
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['password_confirm'] ?? '';
    
    if (strlen($password) < 6) {
        $msg = 'Password minimal 6 karakter.'; $msgType = 'error';
    } elseif ($password !== $confirm) {
        $msg = 'Konfirmasi password tidak cocok.'; $msgType = 'error';
    } else {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET password=? WHERE id=?");
        $stmt->bind_param("si", $hashed, $reset['user_id']);
        $stmt->execute();
        $stmt->close();
        
        // Hapus token
        $stmt = $conn->prepare("DELETE FROM password_resets WHERE user_id=?");
        $stmt->bind_param("i", $reset['user_id']);
        $stmt->execute();
        $stmt->close();
        
        $_SESSION['flash'] = ['type'=>'success', 'msg'=>'Password berhasil diubah! Silakan login.'];
        header('Location: ' . BASE_URL . '/auth/login.php');
        exit;
    }
}

$pageTitle = 'Reset Password — ' . SITE_NAME;
include __DIR__ . '/../includes/header.php';
?>

<section class="py-12 sm:py-20 flex items-center justify-center min-h-[70vh]">
    <div class="w-full max-w-md mx-4">
        <div class="glass-strong rounded-3xl p-7 sm:p-9" data-aos="fade-up">
            <div class="text-center mb-6">
                <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-green-500/10 border border-green-500/20 flex items-center justify-center">
                    <i class="ri-key-2-fill text-3xl text-green-500"></i>
                </div>
                <h1 class="text-xl font-black" style="color:var(--text)">Reset Password</h1>
                <p class="text-xs mt-1" style="color:var(--muted)">
                    <?= $valid ? 'Buat password baru untuk akun <strong class="text-green-500">'.htmlspecialchars($reset['email']).'</strong>' : 'Verifikasi token reset' ?>
                </p>
            </div>

            <?php if ($msg): ?>
            <div class="rounded-xl p-3 mb-5 text-xs flex items-start gap-2 <?= $msgType==='success' ? 'bg-green-500/10 text-green-500' : 'bg-red-500/10 text-red-400' ?>">
                <i class="<?= $msgType==='success' ? 'ri-checkbox-circle-fill' : 'ri-error-warning-fill' ?> mt-0.5"></i>
                <span><?= htmlspecialchars($msg) ?></span>
            </div>
            <?php endif; ?>

            <?php if ($valid): ?>
            <form method="POST" class="space-y-4">
                <?= csrfField() ?>
                <div>
                    <label class="block text-xs font-semibold mb-1.5" style="color:var(--text)">Password Baru</label>
                    <div class="relative">
                        <i class="ri-lock-line absolute left-3 top-1/2 -translate-y-1/2 text-sm" style="color:var(--muted)"></i>
                        <input type="password" name="password" required minlength="6" placeholder="Minimal 6 karakter" class="auth-input" style="padding-left:36px">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5" style="color:var(--text)">Konfirmasi Password</label>
                    <div class="relative">
                        <i class="ri-lock-check-line absolute left-3 top-1/2 -translate-y-1/2 text-sm" style="color:var(--muted)"></i>
                        <input type="password" name="password_confirm" required minlength="6" placeholder="Ulangi password baru" class="auth-input" style="padding-left:36px">
                    </div>
                </div>
                <button type="submit" class="w-full flex items-center justify-center gap-2 py-3.5 bg-green-600 text-white text-sm font-bold rounded-2xl hover:bg-green-500 btn-press shadow-lg shadow-green-600/20">
                    <i class="ri-check-double-fill"></i> Simpan Password Baru
                </button>
            </form>
            <?php else: ?>
            <div class="text-center mt-4">
                <a href="forgot_password.php" class="inline-flex items-center gap-1.5 px-5 py-3 bg-green-600 text-white text-xs font-bold rounded-2xl hover:bg-green-500 btn-press shadow-lg shadow-green-600/20">
                    <i class="ri-refresh-line"></i> Minta Link Baru
                </a>
            </div>
            <?php endif; ?>

            <div class="mt-5 text-center">
                <a href="login.php" class="text-[11px] font-medium hover:underline" style="color:var(--muted)"><i class="ri-arrow-left-line"></i> Kembali ke Login</a>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
