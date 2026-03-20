<?php
/**
 * BUNDLES.PHP — Paket Hemat / Bundle Page
 * Displays active bundles with discounted prices
 */
require_once __DIR__ . '/../includes/koneksi.php';

$pageTitle = 'Paket Hemat — ' . SITE_NAME;

// Get active bundles with items
$bundles = $conn->query("SELECT b.*, 
    GROUP_CONCAT(p.name SEPARATOR '||') as product_names,
    GROUP_CONCAT(p.price SEPARATOR '||') as product_prices,
    GROUP_CONCAT(p.id SEPARATOR '||') as product_ids,
    GROUP_CONCAT(p.category SEPARATOR '||') as product_categories,
    SUM(p.price) as total_price
    FROM bundles b 
    JOIN bundle_items bi ON b.id=bi.bundle_id 
    JOIN products p ON bi.product_id=p.id 
    WHERE b.is_active=1 
    GROUP BY b.id 
    ORDER BY b.created_at DESC");

include __DIR__ . '/../includes/header.php';
?>

<section class="pt-24 pb-16 min-h-screen" style="background:var(--bg)">
    <div class="max-w-5xl mx-auto px-4 sm:px-6">

        <div class="text-center mb-8" data-aos="fade-up">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold bg-green-500/10 text-green-600 mb-3">📦 Bundle Deals</span>
            <h1 class="text-2xl sm:text-3xl font-black" style="color:var(--text)">Paket <span class="text-green-500">Hemat</span></h1>
            <p class="text-xs mt-1" style="color:var(--muted)">Beli bundling, lebih murah dan lebih lengkap!</p>
        </div>

        <?php if ($bundles->num_rows > 0): ?>
        <div class="grid gap-5">
            <?php while ($b = $bundles->fetch_assoc()):
                $names = explode('||', $b['product_names']);
                $prices = explode('||', $b['product_prices']);
                $ids = explode('||', $b['product_ids']);
                $cats = explode('||', $b['product_categories']);
                $totalOriginal = (int)$b['total_price'];
                $discountedPrice = round($totalOriginal * (1 - $b['discount_percent']/100));
                $saved = $totalOriginal - $discountedPrice;
            ?>
            <div class="glass-strong rounded-3xl p-5 sm:p-6 hover-lift" data-aos="fade-up">
                <!-- Header -->
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 mb-4">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1">
                            <h2 class="text-lg font-black" style="color:var(--text)"><?= htmlspecialchars($b['name']) ?></h2>
                            <span class="px-2 py-0.5 rounded-md text-[9px] font-black bg-red-500/10 text-red-500">-<?= $b['discount_percent'] ?>%</span>
                        </div>
                        <?php if ($b['description']): ?>
                        <p class="text-xs" style="color:var(--muted)"><?= htmlspecialchars($b['description']) ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="text-right">
                        <p class="text-xs line-through" style="color:var(--muted)"><?= rupiah($totalOriginal) ?></p>
                        <p class="text-xl font-black text-green-500"><?= rupiah($discountedPrice) ?></p>
                        <p class="text-[9px] font-bold text-amber-500">Hemat <?= rupiah($saved) ?></p>
                    </div>
                </div>
                <!-- Items -->
                <div class="grid grid-cols-2 sm:grid-cols-<?= min(count($names), 4) ?> gap-3 mb-4">
                    <?php for ($i = 0; $i < count($names); $i++): ?>
                    <a href="<?= BASE_URL ?>/store/product_detail.php?id=<?= $ids[$i] ?>" class="flex items-center gap-2 p-3 rounded-xl transition hover:border-green-500/30" style="background:var(--surface)">
                        <div class="w-8 h-8 rounded-lg bg-green-500/10 flex items-center justify-center shrink-0">
                            <i class="ri-apps-fill text-green-500 text-sm"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="text-[11px] font-bold truncate" style="color:var(--text)"><?= htmlspecialchars($names[$i]) ?></p>
                            <p class="text-[9px]" style="color:var(--muted)"><?= rupiah((int)$prices[$i]) ?></p>
                        </div>
                    </a>
                    <?php endfor; ?>
                </div>
                <!-- CTA -->
                <a href="https://wa.me/6281234567890?text=<?= urlencode('Halo, saya mau beli paket ' . $b['name'] . ' (' . rupiah($discountedPrice) . ')') ?>" target="_blank" class="inline-flex items-center gap-2 px-6 py-3 bg-green-600 text-white text-xs font-bold rounded-xl hover:bg-green-500 btn-press shadow-lg shadow-green-600/20 transition">
                    <i class="ri-whatsapp-line"></i> Beli Paket via WhatsApp
                </a>
            </div>
            <?php endwhile; ?>
        </div>
        <?php else: ?>
        <div class="text-center py-16 glass-strong rounded-3xl" data-aos="fade-up">
            <i class="ri-gift-line text-5xl mb-3" style="color:var(--muted)"></i>
            <p class="text-base font-bold" style="color:var(--text)">Belum ada paket tersedia</p>
            <p class="text-xs mt-1 mb-4" style="color:var(--muted)">Nantikan bundle deals menarik dari kami!</p>
            <a href="<?= BASE_URL ?>/index.php" class="inline-flex items-center gap-1.5 px-5 py-2.5 bg-green-600 text-white text-xs font-bold rounded-xl hover:bg-green-500 btn-press transition"><i class="ri-store-2-line"></i> Lihat Produk</a>
        </div>
        <?php endif; ?>

    </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
