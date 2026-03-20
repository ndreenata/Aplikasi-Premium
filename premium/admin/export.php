<?php
/**
 * ADMIN/EXPORT.PHP — Report Export (CSV/Excel)
 */
$pageTitle = 'Laporan';
require_once __DIR__ . '/../includes/admin_header.php';

// Handle export download
if (isset($_GET['download'])) {
    $format = $_GET['download'];
    $from = $_GET['from'] ?? date('Y-m-01');
    $to = $_GET['to'] ?? date('Y-m-d');
    $status = $_GET['status_filter'] ?? '';

    $where = "DATE(t.created_at) BETWEEN '$from' AND '$to'";
    if ($status) $where .= " AND t.status='$status'";

    $rows = $conn->query("SELECT t.invoice_number, t.customer_name, p.name as product_name, p.category, t.amount, p.cost_price, (t.amount - p.cost_price) as profit, t.status, t.phone_number, t.created_at FROM transactions t JOIN products p ON t.product_id=p.id WHERE $where ORDER BY t.created_at DESC");

    if ($format === 'csv') {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="laporan_' . $from . '_' . $to . '.csv"');
        $out = fopen('php://output', 'w');
        fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM for Excel UTF-8
        fputcsv($out, ['Invoice', 'Customer', 'Produk', 'Kategori', 'Harga Jual', 'Harga Modal', 'Profit', 'Status', 'No HP', 'Tanggal']);
        $totalRev = 0; $totalProfit = 0;
        while ($r = $rows->fetch_assoc()) {
            fputcsv($out, [
                $r['invoice_number'], $r['customer_name'], $r['product_name'], $r['category'],
                $r['amount'], $r['cost_price'], $r['profit'],
                $r['status'], $r['phone_number'],
                date('d/m/Y H:i', strtotime($r['created_at']))
            ]);
            if ($r['status'] === 'SUCCESS') { $totalRev += $r['amount']; $totalProfit += $r['profit']; }
        }
        fputcsv($out, []);
        fputcsv($out, ['', '', '', '', 'Total Revenue:', $totalRev, 'Total Profit:', $totalProfit]);
        fclose($out);
        exit;
    }
}

// Report preview
$from = $_GET['from'] ?? date('Y-m-01');
$to = $_GET['to'] ?? date('Y-m-d');
$statusFilter = $_GET['status_filter'] ?? '';

$where = "DATE(t.created_at) BETWEEN '$from' AND '$to'";
if ($statusFilter) $where .= " AND t.status='$statusFilter'";

$transactions = $conn->query("SELECT t.*, p.name as product_name, p.category, p.cost_price FROM transactions t JOIN products p ON t.product_id=p.id WHERE $where ORDER BY t.created_at DESC");

$summary = $conn->query("SELECT
    COUNT(*) as total_tx,
    SUM(CASE WHEN t.status='SUCCESS' THEN t.amount ELSE 0 END) as total_revenue,
    SUM(CASE WHEN t.status='SUCCESS' THEN (t.amount - p.cost_price) ELSE 0 END) as total_profit,
    SUM(CASE WHEN t.status='SUCCESS' THEN 1 ELSE 0 END) as success_count,
    SUM(CASE WHEN t.status='PENDING' THEN 1 ELSE 0 END) as pending_count,
    SUM(CASE WHEN t.status='FAILED' THEN 1 ELSE 0 END) as failed_count
    FROM transactions t JOIN products p ON t.product_id=p.id WHERE $where")->fetch_assoc();

// Category stats
$catStats = $conn->query("SELECT p.category, COUNT(t.id) as sold, COALESCE(SUM(t.amount),0) as revenue, COALESCE(SUM(t.amount - p.cost_price),0) as profit
    FROM transactions t JOIN products p ON t.product_id=p.id WHERE t.status='SUCCESS' AND DATE(t.created_at) BETWEEN '$from' AND '$to'
    GROUP BY p.category ORDER BY revenue DESC");
$catData = [];
$maxCatRev = 1;
while ($c = $catStats->fetch_assoc()) {
    $catData[] = $c;
    if ($c['revenue'] > $maxCatRev) $maxCatRev = $c['revenue'];
}
$catColors = ['musik_video'=>'#3B82F6','desain'=>'#8B5CF6','productivity'=>'#F59E0B','gaming'=>'#EF4444','education'=>'#10B981'];
?>

<!-- Header -->
<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6 anim-up d1">
    <div>
        <h2 class="text-lg font-black" style="color:var(--text)">Laporan & Statistik</h2>
        <p class="text-xs" style="color:var(--muted)">Analisis pendapatan, profit, dan kategori</p>
    </div>
    <div class="flex gap-2">
        <a href="?download=csv&from=<?= $from ?>&to=<?= $to ?>&status_filter=<?= $statusFilter ?>" class="btn btn-primary">
            <i class="ri-download-line"></i> Download CSV
        </a>
    </div>
</div>

<!-- Filters -->
<div class="admin-card mb-4 anim-up d2">
    <form method="get" class="flex flex-wrap gap-3 items-end">
        <div>
            <label class="text-[10px] font-bold uppercase tracking-wider block mb-1" style="color:var(--muted)">Dari</label>
            <input type="date" name="from" value="<?= $from ?>" class="admin-input" style="min-width:140px">
        </div>
        <div>
            <label class="text-[10px] font-bold uppercase tracking-wider block mb-1" style="color:var(--muted)">Sampai</label>
            <input type="date" name="to" value="<?= $to ?>" class="admin-input" style="min-width:140px">
        </div>
        <div>
            <label class="text-[10px] font-bold uppercase tracking-wider block mb-1" style="color:var(--muted)">Status</label>
            <select name="status_filter" class="admin-input admin-select" style="min-width:130px">
                <option value="">Semua</option>
                <option value="SUCCESS" <?= $statusFilter==='SUCCESS'?'selected':'' ?>>Success</option>
                <option value="PENDING" <?= $statusFilter==='PENDING'?'selected':'' ?>>Pending</option>
                <option value="FAILED" <?= $statusFilter==='FAILED'?'selected':'' ?>>Failed</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary"><i class="ri-filter-line"></i> Filter</button>
    </form>
</div>

<!-- Summary Cards -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="stat-card anim-up d3">
        <span class="text-[10px] font-bold uppercase tracking-wider block mb-2" style="color:var(--muted)">Total Transaksi</span>
        <p class="text-xl font-black" style="color:var(--text)"><?= $summary['total_tx'] ?? 0 ?></p>
        <div class="flex gap-2 mt-2">
            <span class="badge badge-success" style="font-size:9px"><?= $summary['success_count'] ?? 0 ?> ok</span>
            <span class="badge badge-warning" style="font-size:9px"><?= $summary['pending_count'] ?? 0 ?> wait</span>
            <span class="badge badge-danger" style="font-size:9px"><?= $summary['failed_count'] ?? 0 ?> fail</span>
        </div>
    </div>
    <div class="stat-card anim-up d4">
        <span class="text-[10px] font-bold uppercase tracking-wider block mb-2" style="color:var(--muted)">Revenue (Success)</span>
        <p class="text-xl font-black text-accent"><?= rupiah($summary['total_revenue'] ?? 0) ?></p>
    </div>
    <div class="stat-card anim-up d5">
        <span class="text-[10px] font-bold uppercase tracking-wider block mb-2" style="color:var(--muted)">Net Profit</span>
        <p class="text-xl font-black" style="color:#10B981"><?= rupiah($summary['total_profit'] ?? 0) ?></p>
    </div>
    <div class="stat-card anim-up d6">
        <span class="text-[10px] font-bold uppercase tracking-wider block mb-2" style="color:var(--muted)">Margin</span>
        <p class="text-xl font-black" style="color:var(--text)">
            <?= ($summary['total_revenue'] ?? 0) > 0 ? round(($summary['total_profit'] / $summary['total_revenue']) * 100, 1) . '%' : '0%' ?>
        </p>
    </div>
</div>

<!-- Category Stats -->
<?php if (!empty($catData)): ?>
<div class="admin-card mb-6 anim-scale d7">
    <h3 class="text-sm font-bold mb-4" style="color:var(--text)"><i class="ri-pie-chart-fill text-accent"></i> Statistik per Kategori</h3>
    <div class="space-y-3">
        <?php foreach ($catData as $cat): ?>
        <div>
            <div class="flex items-center justify-between mb-1">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full" style="background:<?= $catColors[$cat['category']] ?? '#6B7280' ?>"></span>
                    <span class="text-xs font-semibold" style="color:var(--text)"><?= htmlspecialchars(ucfirst(str_replace('_',' ',$cat['category']))) ?></span>
                    <span class="text-[10px]" style="color:var(--muted)">(<?= $cat['sold'] ?> terjual)</span>
                </div>
                <div class="text-right">
                    <span class="text-xs font-bold text-accent"><?= rupiah($cat['revenue']) ?></span>
                    <span class="text-[10px] ml-2" style="color:#10B981">↑ <?= rupiah($cat['profit']) ?></span>
                </div>
            </div>
            <div class="w-full rounded-full h-2" style="background:var(--bg2)">
                <div class="h-2 rounded-full transition-all" style="width:<?= ($cat['revenue']/$maxCatRev)*100 ?>%;background:<?= $catColors[$cat['category']] ?? '#6B7280' ?>"></div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<!-- Transaction Table -->
<div class="admin-card anim-up d8">
    <h3 class="text-sm font-bold mb-4" style="color:var(--text)">Detail Transaksi</h3>
    <div class="overflow-x-auto">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Invoice</th>
                    <th>Customer</th>
                    <th>Produk</th>
                    <th>Kategori</th>
                    <th>Harga</th>
                    <th>Modal</th>
                    <th>Profit</th>
                    <th>Status</th>
                    <th>Tanggal</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($transactions->num_rows === 0): ?>
                <tr><td colspan="9" class="text-center py-8" style="color:var(--muted)">Tidak ada transaksi di periode ini</td></tr>
                <?php endif; ?>
                <?php while ($t = $transactions->fetch_assoc()): ?>
                <tr>
                    <td><span class="font-mono text-[11px] font-bold" style="color:var(--text)"><?= htmlspecialchars($t['invoice_number']) ?></span></td>
                    <td class="text-xs"><?= htmlspecialchars($t['customer_name'] ?? '-') ?></td>
                    <td class="text-xs truncate max-w-[120px]"><?= htmlspecialchars($t['product_name']) ?></td>
                    <td><span class="badge badge-muted" style="font-size:9px"><?= htmlspecialchars($t['category']) ?></span></td>
                    <td class="text-xs font-bold text-accent"><?= rupiah($t['amount']) ?></td>
                    <td class="text-xs" style="color:var(--muted)"><?= rupiah($t['cost_price']) ?></td>
                    <td class="text-xs font-bold" style="color:<?= ($t['amount']-$t['cost_price'])>=0 ? '#10B981' : '#EF4444' ?>"><?= rupiah($t['amount'] - $t['cost_price']) ?></td>
                    <td>
                        <?php if ($t['status']==='SUCCESS'): ?>
                            <span class="badge badge-success"><i class="ri-check-fill"></i>OK</span>
                        <?php elseif ($t['status']==='PENDING'): ?>
                            <span class="badge badge-warning"><i class="ri-time-fill"></i>Wait</span>
                        <?php else: ?>
                            <span class="badge badge-danger"><i class="ri-close-fill"></i>Fail</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-[11px]" style="color:var(--muted)"><?= date('d M H:i', strtotime($t['created_at'])) ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
