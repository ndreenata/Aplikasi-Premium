<?php
/**
 * USER_DASHBOARD.PHP — Order History · Dark Mode
 */
require_once __DIR__ . '/../includes/koneksi.php';
if (!isLoggedIn()) { header('Location: ' . BASE_URL . '/auth/login.php'); exit; }

$user = currentUser();
$stmt = $conn->prepare("SELECT t.*, p.name as product_name FROM transactions t JOIN products p ON t.product_id=p.id WHERE t.user_id=? ORDER BY t.created_at DESC");
$stmt->bind_param("i",$user['id']); $stmt->execute();
$orders = $stmt->get_result(); $stmt->close();

$pageTitle = 'Pesanan Saya — ' . SITE_NAME;
include __DIR__ . '/../includes/header.php';
?>

<section class="py-10 sm:py-14">
    <div class="max-w-3xl mx-auto px-4 sm:px-6">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6" data-aos="fade-down">
            <div>
                <h1 class="text-xl font-black" style="color:var(--text)"><i class="ri-file-list-3-fill text-green-500 mr-1"></i> Pesanan Saya</h1>
                <p class="text-xs mt-1" style="color:var(--muted)">Riwayat pembelian akun premium</p>
            </div>
            <div class="flex items-center gap-2.5 px-4 py-2.5 glass rounded-xl">
                <div class="w-8 h-8 rounded-lg bg-green-600 flex items-center justify-center text-white text-xs font-bold"><?= strtoupper(substr($user['name'],0,1)) ?></div>
                <div><p class="text-xs font-semibold" style="color:var(--text)"><?= htmlspecialchars($user['name']) ?></p><p class="text-[10px]" style="color:var(--muted)"><?= htmlspecialchars($user['email']) ?></p></div>
            </div>
        </div>

        <?php if ($orders->num_rows === 0): ?>
        <div class="text-center py-14" data-aos="fade-up">
            <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-green-500/10 flex items-center justify-center"><i class="ri-inbox-line text-3xl text-green-500"></i></div>
            <h3 class="text-sm font-semibold mb-1" style="color:var(--text2)">Belum ada pesanan</h3>
            <p class="text-xs mb-5" style="color:var(--muted)">Yuk mulai belanja akun premium!</p>
            <a href="index.php#products" class="inline-flex items-center gap-1.5 px-5 py-2.5 bg-green-600 text-white text-xs font-semibold rounded-xl hover:bg-green-500 btn-press shadow-lg shadow-green-600/20"><i class="ri-shopping-bag-3-line"></i> Lihat Produk</a>
        </div>
        <?php else: ?>
        <div class="space-y-3">
        <?php while ($o = $orders->fetch_assoc()):
            $isPending = $o['status']==='PENDING';
            $isSuccess = $o['status']==='SUCCESS';
        ?>
            <div class="glass-strong rounded-2xl p-5 hover-lift" data-aos="fade-up">
                <div class="flex items-start justify-between gap-3 mb-2">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-green-500/10 flex items-center justify-center"><i class="ri-shopping-bag-3-fill text-green-500"></i></div>
                        <div>
                            <h3 class="text-sm font-bold" style="color:var(--text)"><?= htmlspecialchars($o['product_name']) ?></h3>
                            <p class="text-[10px] font-mono" style="color:var(--muted)"><?= htmlspecialchars($o['invoice_number']) ?></p>
                        </div>
                    </div>
                    <?php if ($isPending): ?>
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-amber-500/10 text-amber-400 text-[10px] font-bold"><span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span>Pending</span>
                    <?php elseif ($isSuccess): ?>
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-green-500/10 text-green-500 text-[10px] font-bold"><i class="ri-check-fill"></i>Success</span>
                    <?php endif; ?>
                </div>
                <div class="flex items-center justify-between mt-3 pt-3" style="border-top:1px solid var(--border)">
                    <div class="flex items-center gap-4">
                        <span class="text-sm font-bold text-green-500">Rp <?= number_format($o['amount'],0,',','.') ?></span>
                        <span class="text-[10px] flex items-center gap-1" style="color:var(--muted)"><i class="ri-calendar-event-fill"></i><?= date('d M Y H:i', strtotime($o['created_at'])) ?></span>
                    </div>
                    <?php if ($isPending): ?>
                    <a href="<?= BASE_URL ?>/store/bayar_dummy.php?invoice=<?= urlencode($o['invoice_number']) ?>" class="inline-flex items-center gap-1 px-4 py-1.5 bg-green-600 text-white text-[11px] font-semibold rounded-lg hover:bg-green-500 btn-press"><i class="ri-wallet-3-line"></i> Bayar</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endwhile; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
