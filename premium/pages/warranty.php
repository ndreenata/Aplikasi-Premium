<?php
/**
 * WARRANTY.PHP — Warranty Tracker & Replacement Request (#36, #37)
 */
require_once __DIR__ . '/../includes/koneksi.php';
requireLogin();
$pageTitle = 'Garansi & Klaim — ' . SITE_NAME;
$uid = (int)$_SESSION['user_id'];

// Handle replacement request
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['warranty_id'])) {
    $wid = (int)$_POST['warranty_id'];
    $reason = $conn->real_escape_string($_POST['reason'] ?? '');
    $w = $conn->query("SELECT * FROM warranties WHERE id=$wid AND user_id=$uid AND status='active'")->fetch_assoc();
    if ($w && $reason) {
        $conn->query("INSERT INTO replacement_requests (warranty_id, user_id, reason) VALUES ($wid, $uid, '$reason')");
        $conn->query("UPDATE warranties SET status='claimed' WHERE id=$wid");
        $conn->query("INSERT INTO admin_notifications (type, title, message, reference_id) VALUES ('replacement', 'Klaim Garansi Baru', 'User #$uid request replacement', $wid)");
        $msg = 'success';
    }
}

$warranties = $conn->query("SELECT w.*, p.name as product_name, t.created_at as txn_date FROM warranties w JOIN products p ON w.product_id=p.id JOIN transactions t ON w.transaction_id=t.id WHERE w.user_id=$uid ORDER BY w.created_at DESC");
$replacements = $conn->query("SELECT r.*, w.id as wid, p.name as product_name FROM replacement_requests r JOIN warranties w ON r.warranty_id=w.id JOIN products p ON w.product_id=p.id WHERE r.user_id=$uid ORDER BY r.created_at DESC");

include __DIR__ . '/../includes/header.php';
?>

<section class="py-10 min-h-screen" style="background:var(--bg)">
    <div class="max-w-4xl mx-auto px-4 sm:px-6">
        <nav class="flex items-center gap-2 text-xs mb-6" style="color:var(--muted)">
            <a href="<?= BASE_URL ?>/index.php" class="hover:text-green-500">Home</a><i class="ri-arrow-right-s-line"></i>
            <span style="color:var(--text)">Garansi & Klaim</span>
        </nav>

        <h1 class="text-2xl font-black mb-6" style="color:var(--text)" data-aos="fade-up"><i class="ri-shield-star-fill text-green-500 mr-2"></i>Garansi & Klaim</h1>

        <?php if($msg === 'success'): ?>
        <div class="rounded-xl p-4 bg-green-500/10 mb-6" data-aos="fade-up">
            <p class="text-sm font-bold text-green-600"><i class="ri-check-line mr-1"></i>Request klaim berhasil dikirim! Admin akan segera memproses.</p>
        </div>
        <?php endif; ?>

        <!-- Active Warranties -->
        <h2 class="text-sm font-black mb-3 uppercase tracking-wider" style="color:var(--muted)">Garansi Aktif</h2>
        <div class="space-y-3 mb-8">
            <?php if($warranties->num_rows > 0): ?>
            <?php while($w = $warranties->fetch_assoc()):
                $isActive = $w['status'] === 'active' && strtotime($w['expiry_date']) > time();
                $daysLeft = max(0, (int)((strtotime($w['expiry_date']) - time()) / 86400));
            ?>
            <div class="glass-strong rounded-xl p-4" data-aos="fade-up">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-bold" style="color:var(--text)"><?= htmlspecialchars($w['product_name']) ?></span>
                    <span class="px-2 py-0.5 rounded-lg text-[9px] font-bold <?= $isActive ? 'bg-green-500/10 text-green-600' : 'bg-red-500/10 text-red-500' ?>">
                        <?= $isActive ? "Aktif ($daysLeft hari)" : ucfirst($w['status']) ?>
                    </span>
                </div>
                <div class="flex items-center gap-4 text-[10px]" style="color:var(--muted)">
                    <span><i class="ri-calendar-line mr-1"></i>Beli: <?= date('d M Y', strtotime($w['purchase_date'])) ?></span>
                    <span><i class="ri-timer-line mr-1"></i>Expired: <?= date('d M Y', strtotime($w['expiry_date'])) ?></span>
                </div>
                <?php if($isActive && $w['status'] === 'active'): ?>
                <form method="POST" class="mt-3">
                    <input type="hidden" name="warranty_id" value="<?= $w['id'] ?>">
                    <div class="flex gap-2">
                        <input type="text" name="reason" class="flex-1 px-3 py-2 rounded-lg text-xs glass" style="color:var(--text);background:var(--surface)" placeholder="Jelaskan masalah akun..." required>
                        <button type="submit" class="px-4 py-2 rounded-lg bg-orange-500 text-white text-xs font-bold hover:bg-orange-400 transition">
                            <i class="ri-exchange-fill mr-1"></i>Klaim
                        </button>
                    </div>
                </form>
                <?php endif; ?>
            </div>
            <?php endwhile; ?>
            <?php else: ?>
            <div class="glass-strong rounded-xl p-8 text-center">
                <i class="ri-shield-line text-4xl mb-2" style="color:var(--muted)"></i>
                <p class="text-sm" style="color:var(--muted)">Belum ada garansi aktif</p>
            </div>
            <?php endif; ?>
        </div>

        <!-- Replacement History -->
        <h2 class="text-sm font-black mb-3 uppercase tracking-wider" style="color:var(--muted)">Riwayat Klaim</h2>
        <div class="space-y-3">
            <?php if($replacements->num_rows > 0): ?>
            <?php while($r = $replacements->fetch_assoc()): ?>
            <div class="glass-strong rounded-xl p-4" data-aos="fade-up">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-sm font-bold" style="color:var(--text)"><?= htmlspecialchars($r['product_name']) ?></span>
                        <p class="text-[10px] mt-0.5" style="color:var(--muted)"><?= htmlspecialchars($r['reason']) ?></p>
                    </div>
                    <span class="px-2 py-0.5 rounded-lg text-[9px] font-bold <?= $r['status']==='completed' ? 'bg-green-500/10 text-green-600' : ($r['status']==='rejected' ? 'bg-red-500/10 text-red-500' : 'bg-yellow-500/10 text-yellow-600') ?>">
                        <?= ucfirst($r['status']) ?>
                    </span>
                </div>
            </div>
            <?php endwhile; ?>
            <?php else: ?>
            <div class="glass-strong rounded-xl p-8 text-center">
                <p class="text-sm" style="color:var(--muted)">Belum ada riwayat klaim</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
