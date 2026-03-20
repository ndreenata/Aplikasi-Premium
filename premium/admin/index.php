<?php
/**
 * ADMIN/INDEX.PHP — Overview Dashboard
 * Stats, revenue chart, recent transactions, top products
 */
$pageTitle = 'Overview';
require_once __DIR__ . '/../includes/admin_header.php';

// ─── Stats ───
$totalRevenue = $conn->query("SELECT COALESCE(SUM(amount),0) as r FROM transactions WHERE status='SUCCESS'")->fetch_assoc()['r'];
$todayTx = $conn->query("SELECT COUNT(*) as c FROM transactions WHERE DATE(created_at)=CURDATE()")->fetch_assoc()['c'];
$totalProducts = $conn->query("SELECT COUNT(*) as c FROM products")->fetch_assoc()['c'];
$totalUsers = $conn->query("SELECT COUNT(*) as c FROM users")->fetch_assoc()['c'];
$pendingOrders = $conn->query("SELECT COUNT(*) as c FROM transactions WHERE status='PENDING'")->fetch_assoc()['c'];
$availableStock = $conn->query("SELECT COUNT(*) as c FROM stocks WHERE status='available'")->fetch_assoc()['c'];

// ─── Net Profit ───
$netProfit = $conn->query("SELECT COALESCE(SUM(t.amount - p.cost_price),0) as np FROM transactions t JOIN products p ON t.product_id=p.id WHERE t.status='SUCCESS'")->fetch_assoc()['np'];

// ─── Low stock products (< 5 available) ───
$lowStock = $conn->query("SELECT p.id, p.name, COUNT(s.id) as stock FROM products p LEFT JOIN stocks s ON p.id=s.product_id AND s.status='available' WHERE p.is_active=1 GROUP BY p.id HAVING stock < 5 ORDER BY stock ASC LIMIT 6");

// ─── Last 7 days revenue ───
$chart = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-{$i} days"));
    $label = date('D', strtotime($date));
    $rev = $conn->query("SELECT COALESCE(SUM(amount),0) as r FROM transactions WHERE status='SUCCESS' AND DATE(created_at)='$date'")->fetch_assoc()['r'];
    $chart[] = ['label' => $label, 'value' => (float)$rev, 'date' => date('d/m', strtotime($date))];
}
$maxChart = max(array_column($chart, 'value'));
if ($maxChart == 0) $maxChart = 1;

// ─── Recent transactions ───
$recentTx = $conn->query("SELECT t.*, p.name as product_name FROM transactions t JOIN products p ON t.product_id=p.id ORDER BY t.created_at DESC LIMIT 8");

// ─── Top products ───
$topProducts = $conn->query("SELECT p.name, p.price, p.category, COUNT(t.id) as sold, COALESCE(SUM(t.amount),0) as revenue FROM products p LEFT JOIN transactions t ON p.id=t.product_id AND t.status='SUCCESS' GROUP BY p.id ORDER BY sold DESC LIMIT 5");
?>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="stat-card anim-up d1">
        <div class="flex items-center gap-3 mb-3">
            <div class="w-10 h-10 rounded-xl bg-green-500/10 flex items-center justify-center"><i class="ri-money-dollar-circle-fill text-green-500 text-lg"></i></div>
            <span class="text-[10px] font-bold uppercase tracking-wider" style="color:var(--muted)">Total Revenue</span>
        </div>
        <p class="text-xl font-black" style="color:var(--text)"><?= rupiah($totalRevenue) ?></p>
    </div>
    <div class="stat-card anim-up d2">
        <div class="flex items-center gap-3 mb-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-500/10 flex items-center justify-center"><i class="ri-line-chart-fill text-emerald-500 text-lg"></i></div>
            <span class="text-[10px] font-bold uppercase tracking-wider" style="color:var(--muted)">Net Profit</span>
        </div>
        <p class="text-xl font-black" style="color:var(--text)"><?= rupiah($netProfit) ?></p>
    </div>
    <div class="stat-card anim-up d3">
        <div class="flex items-center gap-3 mb-3">
            <div class="w-10 h-10 rounded-xl bg-blue-500/10 flex items-center justify-center"><i class="ri-exchange-funds-fill text-blue-500 text-lg"></i></div>
            <span class="text-[10px] font-bold uppercase tracking-wider" style="color:var(--muted)">Transaksi Hari Ini</span>
        </div>
        <p class="text-xl font-black" style="color:var(--text)"><?= $todayTx ?></p>
    </div>
    <div class="stat-card anim-up d4">
        <div class="flex items-center gap-3 mb-3">
            <div class="w-10 h-10 rounded-xl bg-purple-500/10 flex items-center justify-center"><i class="ri-shopping-bag-3-fill text-purple-500 text-lg"></i></div>
            <span class="text-[10px] font-bold uppercase tracking-wider" style="color:var(--muted)">Total Produk</span>
        </div>
        <p class="text-xl font-black" style="color:var(--text)"><?= $totalProducts ?></p>
    </div>
    <div class="stat-card anim-up d5">
        <div class="flex items-center gap-3 mb-3">
            <div class="w-10 h-10 rounded-xl bg-amber-500/10 flex items-center justify-center"><i class="ri-group-fill text-amber-500 text-lg"></i></div>
            <span class="text-[10px] font-bold uppercase tracking-wider" style="color:var(--muted)">Total Users</span>
        </div>
        <p class="text-xl font-black" style="color:var(--text)"><?= $totalUsers ?></p>
    </div>
    <div class="stat-card anim-up d6">
        <div class="flex items-center gap-3 mb-3">
            <div class="w-10 h-10 rounded-xl bg-orange-500/10 flex items-center justify-center"><i class="ri-time-fill text-orange-500 text-lg"></i></div>
            <span class="text-[10px] font-bold uppercase tracking-wider" style="color:var(--muted)">Pending</span>
        </div>
        <p class="text-xl font-black" style="color:var(--text)"><?= $pendingOrders ?></p>
    </div>
    <div class="stat-card anim-up d7">
        <div class="flex items-center gap-3 mb-3">
            <div class="w-10 h-10 rounded-xl bg-teal-500/10 flex items-center justify-center"><i class="ri-archive-fill text-teal-500 text-lg"></i></div>
            <span class="text-[10px] font-bold uppercase tracking-wider" style="color:var(--muted)">Stok Tersedia</span>
        </div>
        <p class="text-xl font-black" style="color:var(--text)"><?= $availableStock ?></p>
    </div>
</div>

<?php if ($lowStock->num_rows > 0): ?>
<!-- ═══ LOW STOCK ALERT ═══ -->
<div class="admin-card mb-6 anim-up d8" style="border-color:rgba(239,68,68,0.3);background:rgba(239,68,68,0.04)">
    <div class="flex items-center gap-2 mb-3">
        <i class="ri-error-warning-fill text-red-500 text-lg"></i>
        <h3 class="text-sm font-bold text-red-500">Stok Menipis!</h3>
    </div>
    <div class="flex flex-wrap gap-3">
        <?php while ($ls = $lowStock->fetch_assoc()): ?>
        <a href="<?= BASE_URL ?>/admin/stocks.php" class="flex items-center gap-2 px-3 py-2 rounded-xl text-xs font-semibold" style="background:rgba(239,68,68,0.08);color:#EF4444">
            <span><?= htmlspecialchars($ls['name']) ?></span>
            <span class="badge badge-danger" style="font-size:10px"><?= $ls['stock'] ?> sisa</span>
        </a>
        <?php endwhile; ?>
    </div>
</div>
<?php endif; ?>

<div class="grid grid-cols-1 lg:grid-cols-5 gap-4 mb-6">
    <!-- ═══ REVENUE CHART ═══ -->
    <div class="lg:col-span-3 admin-card anim-scale d8">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-sm font-bold" style="color:var(--text)">Revenue 7 Hari Terakhir</h3>
                <p class="text-[10px]" style="color:var(--muted)">Pendapatan harian transaksi sukses</p>
            </div>
        </div>
        <div class="flex items-end justify-between gap-2" style="height:160px">
            <?php foreach ($chart as $c): ?>
            <div class="flex-1 flex flex-col items-center gap-1" style="height:100%">
                <span class="text-[9px] font-bold" style="color:var(--muted)"><?= $c['value'] > 0 ? 'Rp'.number_format($c['value']/1000,0).'K' : '-' ?></span>
                <div class="w-full flex-1 flex items-end">
                    <div class="chart-bar w-full" style="height:<?= ($c['value']/$maxChart)*100 ?>%"></div>
                </div>
                <span class="text-[10px] font-semibold" style="color:var(--text2)"><?= $c['label'] ?></span>
                <span class="text-[8px]" style="color:var(--muted)"><?= $c['date'] ?></span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- ═══ TOP PRODUCTS ═══ -->
    <div class="lg:col-span-2 admin-card anim-scale d9">
        <h3 class="text-sm font-bold mb-4" style="color:var(--text)">Top Produk</h3>
        <div class="space-y-3">
            <?php $rank=1; while ($tp = $topProducts->fetch_assoc()): ?>
            <div class="flex items-center gap-3">
                <span class="w-6 h-6 rounded-lg flex items-center justify-center text-[10px] font-black <?= $rank<=3?'bg-green-500/10 text-green-500':'bg-gray-100 dark:bg-gray-800' ?>" style="<?= $rank>3?'color:var(--muted)':'' ?>"><?= $rank ?></span>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-semibold truncate" style="color:var(--text)"><?= htmlspecialchars($tp['name']) ?></p>
                    <p class="text-[10px]" style="color:var(--muted)"><?= $tp['sold'] ?> terjual</p>
                </div>
                <span class="text-xs font-bold text-accent"><?= rupiah($tp['revenue']) ?></span>
            </div>
            <?php $rank++; endwhile; ?>
        </div>
    </div>
</div>

<!-- ═══ RECENT TRANSACTIONS ═══ -->
<div class="admin-card anim-up d10">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-sm font-bold" style="color:var(--text)">Transaksi Terbaru</h3>
        <a href="<?= BASE_URL ?>/admin/transactions.php" class="text-[11px] font-semibold text-accent hover:underline">Lihat Semua →</a>
    </div>
    <div class="overflow-x-auto">
        <table class="admin-table">
            <thead>
                <tr><th>Invoice</th><th>Customer</th><th>Produk</th><th>Jumlah</th><th>Status</th><th>Tanggal</th></tr>
            </thead>
            <tbody>
                <?php while ($tx = $recentTx->fetch_assoc()): ?>
                <tr>
                    <td><span class="font-mono text-[11px] font-bold" style="color:var(--text)"><?= htmlspecialchars($tx['invoice_number']) ?></span></td>
                    <td><?= htmlspecialchars($tx['customer_name'] ?? '-') ?></td>
                    <td class="truncate max-w-[140px]"><?= htmlspecialchars($tx['product_name']) ?></td>
                    <td class="font-bold text-accent"><?= rupiah($tx['amount']) ?></td>
                    <td>
                        <?php if ($tx['status']==='SUCCESS'): ?>
                            <span class="badge badge-success"><i class="ri-check-fill"></i>Success</span>
                        <?php elseif ($tx['status']==='PENDING'): ?>
                            <span class="badge badge-warning"><i class="ri-time-fill"></i>Pending</span>
                        <?php else: ?>
                            <span class="badge badge-danger"><i class="ri-close-fill"></i>Failed</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-[11px]" style="color:var(--muted)"><?= date('d M Y H:i', strtotime($tx['created_at'])) ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
