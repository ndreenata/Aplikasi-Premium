<?php
/**
 * PROFILE.PHP — Aesthetic Member Profile Page
 * Points, Badges, Order History, Stats
 */
require_once __DIR__ . '/../includes/koneksi.php';

if (!isLoggedIn()) {
    header('Location: ' . BASE_URL . '/auth/login.php');
    exit;
}

$uid = $_SESSION['user_id'];
$user = currentUser();
$pageTitle = 'Profil Saya — ' . SITE_NAME;

// Fetch points
$points = getUserPoints($conn, $uid);

// Fetch badges
$badges = $conn->prepare("SELECT * FROM user_badges WHERE user_id=? ORDER BY earned_at DESC");
$badges->bind_param("i", $uid); $badges->execute();
$badgeList = $badges->get_result();

// Fetch orders
$orders = $conn->prepare("SELECT t.*, p.name as product_name, p.category FROM transactions t JOIN products p ON t.product_id=p.id WHERE t.user_id=? ORDER BY t.created_at DESC LIMIT 20");
$orders->bind_param("i", $uid); $orders->execute();
$orderList = $orders->get_result();

// Stats
$totalSpend = $conn->prepare("SELECT COALESCE(SUM(amount),0) as total, COUNT(*) as count FROM transactions WHERE user_id=? AND status='SUCCESS'");
$totalSpend->bind_param("i", $uid); $totalSpend->execute();
$stats = $totalSpend->get_result()->fetch_assoc();

// Point transactions
$ptx = $conn->prepare("SELECT * FROM point_transactions WHERE user_id=? ORDER BY created_at DESC LIMIT 10");
$ptx->bind_param("i", $uid); $ptx->execute();
$pointHistory = $ptx->get_result();

// Referral code
$refCode = $user['referral_code'] ?? null;
if (!$refCode) {
    $refCode = strtoupper(substr($user['username'] ?? 'USER', 0, 4)) . rand(1000, 9999);
    $conn->query("UPDATE users SET referral_code='$refCode' WHERE id=$uid");
}

include __DIR__ . '/../includes/header.php';
?>

<section class="pt-24 pb-16 min-h-screen">
    <div class="max-w-4xl mx-auto px-4 sm:px-6">

        <!-- Profile Header -->
        <div class="glass-strong rounded-3xl p-6 sm:p-8 mb-6 relative overflow-hidden" data-aos="fade-up">
            <div class="absolute top-0 right-0 w-60 h-60 bg-gradient-to-br from-green-500/10 to-cyan-500/5 rounded-full -translate-y-1/2 translate-x-1/2 blur-3xl"></div>
            <div class="flex flex-col sm:flex-row items-center gap-5 relative z-10">
                <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-green-500 to-emerald-600 flex items-center justify-center shadow-xl shadow-green-600/20">
                    <span class="text-3xl font-black text-white"><?= strtoupper(substr($user['username'] ?? 'U', 0, 1)) ?></span>
                </div>
                <div class="text-center sm:text-left flex-1">
                    <h1 class="text-xl font-black" style="color:var(--text)"><?= htmlspecialchars($user['username'] ?? '') ?></h1>
                    <p class="text-xs mt-0.5" style="color:var(--muted)"><?= htmlspecialchars($user['email'] ?? '') ?></p>
                    <p class="text-[10px] mt-1" style="color:var(--muted)">Member sejak <?= date('d M Y', strtotime($user['created_at'] ?? 'now')) ?></p>
                </div>
                <div class="flex gap-3">
                    <div class="text-center px-4 py-2 rounded-xl" style="background:var(--surface)">
                        <p class="text-lg font-black text-green-500"><?= $stats['count'] ?></p>
                        <p class="text-[9px] font-semibold" style="color:var(--muted)">Pesanan</p>
                    </div>
                    <div class="text-center px-4 py-2 rounded-xl" style="background:var(--surface)">
                        <p class="text-lg font-black text-green-500"><?= rupiah($stats['total']) ?></p>
                        <p class="text-[9px] font-semibold" style="color:var(--muted)">Total Spend</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="glass-strong rounded-2xl p-4" data-aos="fade-up" data-aos-delay="50">
                <div class="flex items-center gap-2 mb-2">
                    <div class="w-8 h-8 rounded-lg bg-amber-500/10 flex items-center justify-center"><i class="ri-coin-fill text-amber-400"></i></div>
                    <span class="text-[9px] font-bold" style="color:var(--muted)">Poin</span>
                </div>
                <p class="text-2xl font-black text-amber-400"><?= number_format($points) ?></p>
            </div>
            <div class="glass-strong rounded-2xl p-4" data-aos="fade-up" data-aos-delay="100">
                <div class="flex items-center gap-2 mb-2">
                    <div class="w-8 h-8 rounded-lg bg-purple-500/10 flex items-center justify-center"><i class="ri-award-fill text-purple-400"></i></div>
                    <span class="text-[9px] font-bold" style="color:var(--muted)">Badge</span>
                </div>
                <p class="text-2xl font-black text-purple-400"><?= $badgeList->num_rows ?></p>
            </div>
            <div class="glass-strong rounded-2xl p-4" data-aos="fade-up" data-aos-delay="150">
                <div class="flex items-center gap-2 mb-2">
                    <div class="w-8 h-8 rounded-lg bg-pink-500/10 flex items-center justify-center"><i class="ri-heart-fill text-pink-400"></i></div>
                    <span class="text-[9px] font-bold" style="color:var(--muted)">Wishlist</span>
                </div>
                <p class="text-2xl font-black text-pink-400" id="profileWishlistCount">0</p>
            </div>
            <div class="glass-strong rounded-2xl p-4" data-aos="fade-up" data-aos-delay="200">
                <div class="flex items-center gap-2 mb-2">
                    <div class="w-8 h-8 rounded-lg bg-blue-500/10 flex items-center justify-center"><i class="ri-share-forward-fill text-blue-400"></i></div>
                    <span class="text-[9px] font-bold" style="color:var(--muted)">Referral</span>
                </div>
                <div class="flex items-center gap-1">
                    <code class="text-sm font-black text-blue-400"><?= $refCode ?></code>
                    <button onclick="navigator.clipboard.writeText('<?= $refCode ?>');this.innerHTML='<i class=\'ri-check-line\'></i>'" class="w-6 h-6 rounded-md flex items-center justify-center text-[10px] hover:bg-blue-500/10 text-blue-400"><i class="ri-file-copy-line"></i></button>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="flex gap-2 mb-5 overflow-x-auto pb-1" data-aos="fade-up">
            <button onclick="showTab('badges')" class="profile-tab active px-4 py-2 rounded-xl text-xs font-bold transition" id="tab-badges">🏅 Badge</button>
            <button onclick="showTab('orders')" class="profile-tab px-4 py-2 rounded-xl text-xs font-bold transition" id="tab-orders">📦 Pesanan</button>
            <button onclick="showTab('points')" class="profile-tab px-4 py-2 rounded-xl text-xs font-bold transition" id="tab-points">💰 Poin</button>
        </div>

        <!-- Badge Tab -->
        <div id="panel-badges" class="profile-panel">
            <?php if ($badgeList->num_rows > 0): ?>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                <?php while ($b = $badgeList->fetch_assoc()): ?>
                <div class="glass-strong rounded-2xl p-4 text-center hover-lift" data-aos="fade-up">
                    <div class="w-12 h-12 mx-auto rounded-2xl bg-<?= $b['badge_color'] ?>-500/10 flex items-center justify-center mb-2">
                        <i class="<?= $b['badge_icon'] ?> text-2xl text-<?= $b['badge_color'] ?>-400"></i>
                    </div>
                    <p class="text-xs font-bold" style="color:var(--text)"><?= htmlspecialchars($b['badge_name']) ?></p>
                    <p class="text-[9px] mt-0.5" style="color:var(--muted)"><?= date('d M Y', strtotime($b['earned_at'])) ?></p>
                </div>
                <?php endwhile; ?>
            </div>
            <?php else: ?>
            <div class="text-center py-12 glass-strong rounded-2xl">
                <i class="ri-award-line text-4xl mb-2" style="color:var(--muted)"></i>
                <p class="text-sm font-medium" style="color:var(--text)">Belum ada badge</p>
                <p class="text-[10px] mt-1" style="color:var(--muted)">Mulai belanja untuk mendapatkan badge!</p>
            </div>
            <?php endif; ?>
        </div>

        <!-- Orders Tab -->
        <div id="panel-orders" class="profile-panel hidden">
            <?php if ($orderList->num_rows > 0): ?>
            <div class="space-y-3">
                <?php while ($o = $orderList->fetch_assoc()):
                    $sc = ['PENDING'=>'amber','PAID'=>'blue','SUCCESS'=>'green','CANCELLED'=>'red','REFUNDED'=>'purple'][$o['status']] ?? 'gray';
                ?>
                <div class="glass-strong rounded-2xl p-4 flex items-center gap-4" data-aos="fade-up">
                    <div class="w-10 h-10 rounded-xl bg-<?= $sc ?>-500/10 flex items-center justify-center shrink-0">
                        <i class="ri-box-3-fill text-<?= $sc ?>-500"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-bold truncate" style="color:var(--text)"><?= htmlspecialchars($o['product_name']) ?></p>
                        <p class="text-[10px]" style="color:var(--muted)"><?= $o['invoice_number'] ?> · <?= date('d M Y', strtotime($o['created_at'])) ?></p>
                    </div>
                    <div class="text-right shrink-0">
                        <p class="text-sm font-bold text-green-500"><?= rupiah($o['amount']) ?></p>
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-bold bg-<?= $sc ?>-500/10 text-<?= $sc ?>-500"><?= $o['status'] ?></span>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
            <?php else: ?>
            <div class="text-center py-12 glass-strong rounded-2xl">
                <i class="ri-shopping-bag-line text-4xl mb-2" style="color:var(--muted)"></i>
                <p class="text-sm font-medium" style="color:var(--text)">Belum ada pesanan</p>
            </div>
            <?php endif; ?>
        </div>

        <!-- Points Tab -->
        <div id="panel-points" class="profile-panel hidden">
            <?php if ($pointHistory->num_rows > 0): ?>
            <div class="space-y-3">
                <?php while ($pt = $pointHistory->fetch_assoc()): ?>
                <div class="glass-strong rounded-2xl p-4 flex items-center gap-4" data-aos="fade-up">
                    <div class="w-10 h-10 rounded-xl bg-<?= $pt['type']==='earn' ? 'green' : 'red' ?>-500/10 flex items-center justify-center">
                        <i class="ri-<?= $pt['type']==='earn' ? 'add' : 'subtract' ?>-line text-<?= $pt['type']==='earn' ? 'green' : 'red' ?>-500 text-lg"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs font-bold" style="color:var(--text)"><?= htmlspecialchars($pt['description']) ?></p>
                        <p class="text-[10px]" style="color:var(--muted)"><?= date('d M Y H:i', strtotime($pt['created_at'])) ?></p>
                    </div>
                    <span class="text-sm font-black <?= $pt['type']==='earn' ? 'text-green-500' : 'text-red-400' ?>"><?= $pt['type']==='earn' ? '+' : '-' ?><?= number_format($pt['amount']) ?></span>
                </div>
                <?php endwhile; ?>
            </div>
            <?php else: ?>
            <div class="text-center py-12 glass-strong rounded-2xl">
                <i class="ri-coin-line text-4xl mb-2" style="color:var(--muted)"></i>
                <p class="text-sm font-medium" style="color:var(--text)">Belum ada riwayat poin</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<style>
.profile-tab { background:var(--surface); color:var(--muted); }
.profile-tab.active { background:var(--accent); color:white; box-shadow: 0 4px 15px rgba(34,197,94,0.3); }
</style>

<script>
function showTab(name) {
    document.querySelectorAll('.profile-panel').forEach(function(p){p.classList.add('hidden');});
    document.querySelectorAll('.profile-tab').forEach(function(t){t.classList.remove('active');});
    document.getElementById('panel-'+name).classList.remove('hidden');
    document.getElementById('tab-'+name).classList.add('active');
}
try{
    var wl = JSON.parse(localStorage.getItem('wishlist')) || [];
    document.getElementById('profileWishlistCount').textContent = wl.length;
}catch(e){}
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
