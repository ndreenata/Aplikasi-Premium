<?php
/**
 * ADMIN/TRANSACTIONS.PHP — Transaction Management
 */
$pageTitle = 'Kelola Transaksi';
require_once __DIR__ . '/../includes/admin_header.php';

$statusFilter = $_GET['status'] ?? '';
$search = trim($_GET['q'] ?? '');

$sql = "SELECT t.*, p.name as product_name, u.name as user_name, u.email as user_email FROM transactions t JOIN products p ON t.product_id=p.id LEFT JOIN users u ON t.user_id=u.id WHERE 1=1";
if ($statusFilter && in_array($statusFilter, ['PENDING','SUCCESS','FAILED'])) $sql .= " AND t.status='$statusFilter'";
if ($search) $sql .= " AND (t.invoice_number LIKE '%$search%' OR t.customer_name LIKE '%$search%')";
$sql .= " ORDER BY t.created_at DESC";
$transactions = $conn->query($sql);

// Counts
$allCount = $conn->query("SELECT COUNT(*) c FROM transactions")->fetch_assoc()['c'];
$pendingCount = $conn->query("SELECT COUNT(*) c FROM transactions WHERE status='PENDING'")->fetch_assoc()['c'];
$successCount = $conn->query("SELECT COUNT(*) c FROM transactions WHERE status='SUCCESS'")->fetch_assoc()['c'];
$failedCount = $conn->query("SELECT COUNT(*) c FROM transactions WHERE status='FAILED'")->fetch_assoc()['c'];
?>

<!-- Header -->
<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 mb-5">
    <div>
        <h1 class="text-lg font-black" style="color:var(--text)">Transaksi</h1>
        <p class="text-[11px]" style="color:var(--muted)">Kelola semua transaksi pelanggan</p>
    </div>
</div>

<!-- Tabs + Search -->
<div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 mb-4">
    <div class="flex items-center gap-1 rounded-xl p-1" style="background:var(--bg2)">
        <a href="?<?= $search?"q=$search&":'' ?>status=" class="px-3 py-1.5 rounded-lg text-[11px] font-bold transition <?= !$statusFilter?'bg-white dark:bg-gray-700 shadow-sm':'' ?>" style="color:<?= !$statusFilter?'var(--accent)':'var(--muted)' ?>">Semua <span class="ml-1 text-[10px]"><?= $allCount ?></span></a>
        <a href="?<?= $search?"q=$search&":'' ?>status=PENDING" class="px-3 py-1.5 rounded-lg text-[11px] font-bold transition <?= $statusFilter==='PENDING'?'bg-white dark:bg-gray-700 shadow-sm':'' ?>" style="color:<?= $statusFilter==='PENDING'?'#D97706':'var(--muted)' ?>">Pending <span class="ml-1 text-[10px]"><?= $pendingCount ?></span></a>
        <a href="?<?= $search?"q=$search&":'' ?>status=SUCCESS" class="px-3 py-1.5 rounded-lg text-[11px] font-bold transition <?= $statusFilter==='SUCCESS'?'bg-white dark:bg-gray-700 shadow-sm':'' ?>" style="color:<?= $statusFilter==='SUCCESS'?'#16A34A':'var(--muted)' ?>">Success <span class="ml-1 text-[10px]"><?= $successCount ?></span></a>
        <a href="?<?= $search?"q=$search&":'' ?>status=FAILED" class="px-3 py-1.5 rounded-lg text-[11px] font-bold transition <?= $statusFilter==='FAILED'?'bg-white dark:bg-gray-700 shadow-sm':'' ?>" style="color:<?= $statusFilter==='FAILED'?'#EF4444':'var(--muted)' ?>">Failed <span class="ml-1 text-[10px]"><?= $failedCount ?></span></a>
    </div>
    <form method="GET" class="flex gap-2">
        <input type="hidden" name="status" value="<?= htmlspecialchars($statusFilter) ?>">
        <input type="text" name="q" value="<?= htmlspecialchars($search) ?>" placeholder="Cari invoice / customer..." class="admin-input" style="max-width:250px">
        <button type="submit" class="btn btn-outline btn-sm"><i class="ri-search-line"></i></button>
    </form>
</div>

<!-- Table -->
<div class="admin-card" style="padding:0;overflow:hidden">
    <div class="overflow-x-auto">
        <table class="admin-table">
            <thead>
                <tr><th>Invoice</th><th>Customer</th><th>Produk</th><th>Jumlah</th><th>Status</th><th>Tanggal</th><th style="text-align:right">Aksi</th></tr>
            </thead>
            <tbody>
                <?php if ($transactions->num_rows === 0): ?>
                <tr><td colspan="7" class="text-center py-8" style="color:var(--muted)">Tidak ada data transaksi</td></tr>
                <?php endif; ?>
                <?php while ($t = $transactions->fetch_assoc()): ?>
                <tr>
                    <td><span class="font-mono text-[11px] font-bold" style="color:var(--text)"><?= htmlspecialchars($t['invoice_number']) ?></span></td>
                    <td>
                        <p class="text-xs font-semibold" style="color:var(--text)"><?= htmlspecialchars($t['customer_name'] ?? '-') ?></p>
                        <p class="text-[10px]" style="color:var(--muted)"><?= htmlspecialchars($t['phone_number'] ?? '') ?></p>
                    </td>
                    <td class="text-xs" style="color:var(--text2)"><?= htmlspecialchars($t['product_name']) ?></td>
                    <td class="font-bold text-accent text-xs"><?= rupiah($t['amount']) ?></td>
                    <td>
                        <?php if ($t['status']==='SUCCESS'): ?>
                            <span class="badge badge-success"><i class="ri-check-fill"></i>Success</span>
                        <?php elseif ($t['status']==='PENDING'): ?>
                            <span class="badge badge-warning"><i class="ri-time-fill"></i>Pending</span>
                        <?php else: ?>
                            <span class="badge badge-danger"><i class="ri-close-fill"></i>Failed</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-[11px]" style="color:var(--muted)"><?= date('d M Y H:i', strtotime($t['created_at'])) ?></td>
                    <td style="text-align:right">
                        <?php if ($t['status']==='PENDING'): ?>
                        <div class="flex items-center justify-end gap-1">
                            <button onclick="updateTxStatus(<?= $t['id'] ?>,'SUCCESS')" class="btn btn-sm" style="background:rgba(34,197,94,0.08);color:#16A34A" title="Mark Success"><i class="ri-check-fill"></i></button>
                            <button onclick="updateTxStatus(<?= $t['id'] ?>,'FAILED')" class="btn btn-danger btn-sm" title="Mark Failed"><i class="ri-close-fill"></i></button>
                        </div>
                        <?php else: ?>
                        <span class="text-[10px]" style="color:var(--muted)">—</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function updateTxStatus(id, status) {
    var msg = status === 'SUCCESS' ? 'Konfirmasi transaksi ini sebagai SUKSES?' : 'Tandai transaksi ini sebagai GAGAL?';
    confirmAction(msg, function() {
        adminAPI('transaction_update', { id: id, status: status }, function(d) {
            if (d.ok) { showToast(d.msg); location.reload(); }
            else showToast(d.msg, 'error');
        });
    });
}
</script>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
