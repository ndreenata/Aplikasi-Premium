<?php
/**
 * LEADERBOARD.PHP — User Leaderboard (#46)
 */
require_once __DIR__ . '/../includes/koneksi.php';
$pageTitle = 'Leaderboard — ' . SITE_NAME;

$month = date('F Y');
$topBuyers = $conn->query("SELECT u.name, u.total_purchases, u.total_spent, u.tier FROM users u WHERE u.role='user' ORDER BY u.total_spent DESC LIMIT 20");

include __DIR__ . '/../includes/header.php';
$medals = ['🥇','🥈','🥉'];
?>

<section class="py-10 min-h-screen" style="background:var(--bg)">
    <div class="max-w-3xl mx-auto px-4 sm:px-6">
        <nav class="flex items-center gap-2 text-xs mb-6" style="color:var(--muted)">
            <a href="<?= BASE_URL ?>/index.php" class="hover:text-green-500">Home</a><i class="ri-arrow-right-s-line"></i>
            <span style="color:var(--text)">Leaderboard</span>
        </nav>

        <div class="text-center mb-8" data-aos="fade-up">
            <h1 class="text-2xl font-black mb-1" style="color:var(--text)">🏆 Top Buyer</h1>
            <p class="text-xs" style="color:var(--muted)"><?= $month ?></p>
        </div>

        <div class="space-y-3">
            <?php $rank = 0; while($buyer = $topBuyers->fetch_assoc()): $rank++; ?>
            <div class="glass-strong rounded-xl p-4 flex items-center gap-4 <?= $rank <= 3 ? 'ring-1 ring-green-500/20' : '' ?>" data-aos="fade-up">
                <div class="w-10 h-10 rounded-xl <?= $rank <= 3 ? 'bg-green-500/10' : 'glass' ?> flex items-center justify-center text-lg font-black shrink-0" style="<?= $rank > 3 ? 'color:var(--muted)' : '' ?>">
                    <?= $rank <= 3 ? $medals[$rank-1] : $rank ?>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-bold" style="color:var(--text)"><?= htmlspecialchars($buyer['name']) ?></p>
                    <div class="flex items-center gap-3 text-[10px]" style="color:var(--muted)">
                        <span><i class="ri-shopping-bag-3-line mr-1"></i><?= $buyer['total_purchases'] ?> order</span>
                        <span class="px-1.5 py-0.5 rounded text-[8px] font-bold uppercase <?= $buyer['tier']==='gold' ? 'bg-yellow-500/10 text-yellow-600' : ($buyer['tier']==='silver' ? 'bg-gray-400/10 text-gray-500' : ($buyer['tier']==='platinum' ? 'bg-purple-500/10 text-purple-500' : 'bg-orange-500/10 text-orange-500')) ?>"><?= $buyer['tier'] ?></span>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-sm font-bold text-green-500">Rp <?= number_format($buyer['total_spent'],0,',','.') ?></p>
                </div>
            </div>
            <?php endwhile; ?>
        </div>

        <?php if($rank === 0): ?>
        <div class="glass-strong rounded-xl p-12 text-center">
            <i class="ri-trophy-line text-5xl mb-3" style="color:var(--muted)"></i>
            <p class="text-sm font-bold" style="color:var(--text)">Belum ada data</p>
            <p class="text-xs" style="color:var(--muted)">Jadilah yang pertama berbelanja!</p>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
