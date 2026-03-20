<?php
/**
 * TRACK_ORDER.PHP — Public Order Tracking Page
 * Users can track their order via invoice number or phone number
 */
require_once __DIR__ . '/../includes/koneksi.php';
$pageTitle = 'Lacak Pesanan — ' . SITE_NAME;

$result = null;
$searchQuery = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' || !empty($_GET['q'])) {
    $searchQuery = trim($_POST['query'] ?? $_GET['q'] ?? '');
    if (!empty($searchQuery)) {
        // Search by invoice or phone
        $q = '%' . $searchQuery . '%';
        $stmt = $conn->prepare("SELECT t.*, p.name as product_name, p.category 
            FROM transactions t 
            JOIN products p ON t.product_id = p.id 
            WHERE t.invoice_number LIKE ? OR t.phone_number LIKE ? 
            ORDER BY t.created_at DESC LIMIT 20");
        $stmt->bind_param("ss", $q, $q);
        $stmt->execute();
        $result = $stmt->get_result();
    }
}

include __DIR__ . '/../includes/header.php';
?>

<section class="pt-24 pb-16 min-h-screen">
    <div class="max-w-3xl mx-auto px-4 sm:px-6">
        <!-- Header -->
        <div class="text-center mb-8" data-aos="fade-up">
            <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-blue-500/10 border border-blue-500/20 flex items-center justify-center">
                <i class="ri-search-2-fill text-3xl text-blue-400"></i>
            </div>
            <h1 class="text-2xl font-black" style="color:var(--text)">Lacak <span class="text-green-500">Pesanan</span></h1>
            <p class="text-sm mt-1" style="color:var(--muted)">Cek status pesananmu dengan nomor invoice atau WhatsApp</p>
        </div>

        <!-- Search Form -->
        <form method="POST" class="mb-8" data-aos="fade-up" data-aos-delay="100">
            <div class="glass-strong rounded-2xl p-5">
                <div class="flex gap-3">
                    <div class="relative flex-1">
                        <i class="ri-search-line absolute left-4 top-1/2 -translate-y-1/2 text-sm" style="color:var(--muted)"></i>
                        <input type="text" name="query" value="<?= htmlspecialchars($searchQuery) ?>" required placeholder="Masukkan invoice (INV-xxx) atau nomor WhatsApp" class="w-full pl-11 pr-4 py-4 rounded-xl text-sm outline-none transition" style="background:var(--bg);border:1.5px solid var(--border);color:var(--text)" onfocus="this.style.borderColor='#22C55E'" onblur="this.style.borderColor='var(--border)'">
                    </div>
                    <button type="submit" class="px-6 py-4 bg-green-600 text-white text-sm font-bold rounded-xl hover:bg-green-500 btn-press shadow-lg shadow-green-600/20 shrink-0">
                        <i class="ri-search-2-line mr-1"></i> Lacak
                    </button>
                </div>
            </div>
        </form>

        <!-- Results -->
        <?php if ($result !== null): ?>
            <?php if ($result->num_rows === 0): ?>
            <div class="text-center py-12 glass-strong rounded-2xl" data-aos="fade-up">
                <i class="ri-file-unknow-line text-4xl mb-3" style="color:var(--muted)"></i>
                <p class="text-sm font-medium" style="color:var(--text)">Pesanan tidak ditemukan</p>
                <p class="text-xs mt-1" style="color:var(--muted)">Pastikan nomor invoice atau WhatsApp benar</p>
            </div>
            <?php else: ?>
            <div class="space-y-4">
                <p class="text-xs font-semibold" style="color:var(--muted)" data-aos="fade-up">Ditemukan <?= $result->num_rows ?> pesanan</p>
                <?php while ($t = $result->fetch_assoc()): 
                    $statusColors = [
                        'PENDING' => 'amber',
                        'PAID' => 'blue',
                        'SUCCESS' => 'green',
                        'CANCELLED' => 'red',
                        'REFUNDED' => 'purple'
                    ];
                    $sc = $statusColors[$t['status']] ?? 'gray';
                    $statusIcons = [
                        'PENDING' => 'ri-time-fill',
                        'PAID' => 'ri-check-fill',
                        'SUCCESS' => 'ri-check-double-fill',
                        'CANCELLED' => 'ri-close-circle-fill',
                        'REFUNDED' => 'ri-refund-fill'
                    ];
                    $si = $statusIcons[$t['status']] ?? 'ri-question-fill';
                ?>
                <div class="glass-strong rounded-2xl p-5" data-aos="fade-up">
                    <div class="flex items-start justify-between mb-3">
                        <div>
                            <p class="text-xs font-mono font-bold" style="color:var(--text)"><?= htmlspecialchars($t['invoice_number']) ?></p>
                            <p class="text-[10px] mt-0.5" style="color:var(--muted)"><?= date('d M Y, H:i', strtotime($t['created_at'])) ?></p>
                        </div>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[10px] font-bold bg-<?= $sc ?>-500/10 text-<?= $sc ?>-500">
                            <i class="<?= $si ?>"></i> <?= $t['status'] ?>
                        </span>
                    </div>
                    <div class="flex items-center gap-3 p-3 rounded-xl" style="background:var(--surface)">
                        <div class="w-10 h-10 rounded-xl bg-green-500/10 flex items-center justify-center"><i class="ri-box-3-fill text-green-500"></i></div>
                        <div class="flex-1">
                            <p class="text-sm font-bold" style="color:var(--text)"><?= htmlspecialchars($t['product_name']) ?></p>
                            <p class="text-xs" style="color:var(--muted)"><?= ucfirst(str_replace('_',' & ',$t['category'])) ?></p>
                        </div>
                        <p class="text-sm font-extrabold text-green-500"><?= rupiah($t['amount']) ?></p>
                    </div>
                    <!-- Progress Bar -->
                    <div class="mt-4 flex items-center gap-2">
                        <?php 
                        $steps = ['PENDING','PAID','SUCCESS'];
                        $currentStep = array_search($t['status'], $steps);
                        if($currentStep === false) $currentStep = -1;
                        foreach($steps as $i => $step):
                            $done = $i <= $currentStep;
                            $stepColors = ['amber','blue','green'];
                        ?>
                        <div class="flex-1">
                            <div class="h-1.5 rounded-full <?= $done ? 'bg-'.$stepColors[$i].'-500' : '' ?>" style="<?= !$done ? 'background:var(--border)' : '' ?>"></div>
                            <p class="text-[9px] font-semibold mt-1 text-center <?= $done ? 'text-'.$stepColors[$i].'-500' : '' ?>" style="<?= !$done ? 'color:var(--muted)' : '' ?>"><?= $step ?></p>
                        </div>
                        <?php if($i < count($steps)-1): ?>
                        <i class="ri-arrow-right-s-line text-xs mt-[-12px]" style="color:var(--muted)"></i>
                        <?php endif; endforeach; ?>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
