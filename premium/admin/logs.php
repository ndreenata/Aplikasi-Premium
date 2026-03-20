<?php
/**
 * ADMIN/LOGS.PHP — Audit Log / Activity History
 */
$pageTitle = 'Log Aktivitas';
require_once __DIR__ . '/../includes/admin_header.php';

// Filters
$filterAction = $_GET['action_filter'] ?? '';
$filterDate = $_GET['date'] ?? '';
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 25;
$offset = ($page - 1) * $perPage;

$where = "1=1";
$params = [];
$types = "";
if ($filterAction) {
    $where .= " AND action LIKE ?";
    $params[] = "%$filterAction%";
    $types .= "s";
}
if ($filterDate) {
    $where .= " AND DATE(created_at) = ?";
    $params[] = $filterDate;
    $types .= "s";
}

$countStmt = $conn->prepare("SELECT COUNT(*) as total FROM audit_logs WHERE $where");
if (!empty($params)) $countStmt->bind_param($types, ...$params);
$countStmt->execute();
$total = $countStmt->get_result()->fetch_assoc()['total'];
$countStmt->close();
$totalPages = ceil($total / $perPage);

$stmt = $conn->prepare("SELECT * FROM audit_logs WHERE $where ORDER BY created_at DESC LIMIT $perPage OFFSET $offset");
if (!empty($params)) $stmt->bind_param($types, ...$params);
$stmt->execute();
$logs = $stmt->get_result();
$stmt->close();

// Unique actions for filter
$actions = $conn->query("SELECT DISTINCT action FROM audit_logs ORDER BY action");
?>

<!-- Header -->
<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6 anim-up d1">
    <div>
        <h2 class="text-lg font-black" style="color:var(--text)">Log Aktivitas</h2>
        <p class="text-xs" style="color:var(--muted)">Riwayat semua perubahan yang dilakukan admin</p>
    </div>
    <span class="badge badge-info"><?= $total ?> total log</span>
</div>

<!-- Filters -->
<div class="admin-card mb-4 anim-up d2">
    <form method="get" class="flex flex-wrap gap-3 items-end">
        <div>
            <label class="text-[10px] font-bold uppercase tracking-wider block mb-1" style="color:var(--muted)">Aksi</label>
            <select name="action_filter" class="admin-input admin-select" style="min-width:160px">
                <option value="">Semua Aksi</option>
                <?php while ($a = $actions->fetch_assoc()): ?>
                <option value="<?= htmlspecialchars($a['action']) ?>" <?= $filterAction === $a['action'] ? 'selected' : '' ?>><?= htmlspecialchars($a['action']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div>
            <label class="text-[10px] font-bold uppercase tracking-wider block mb-1" style="color:var(--muted)">Tanggal</label>
            <input type="date" name="date" value="<?= htmlspecialchars($filterDate) ?>" class="admin-input" style="min-width:140px">
        </div>
        <button type="submit" class="btn btn-primary"><i class="ri-filter-line"></i> Filter</button>
        <a href="<?= BASE_URL ?>/admin/logs.php" class="btn btn-outline">Reset</a>
    </form>
</div>

<!-- Logs Table -->
<div class="admin-card anim-up d3">
    <div class="overflow-x-auto">
        <table class="admin-table">
            <thead>
                <tr>
                    <th style="width:160px">Waktu</th>
                    <th>Admin</th>
                    <th>Aksi</th>
                    <th>Entitas</th>
                    <th>Detail</th>
                    <th style="width:100px">IP</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($logs->num_rows === 0): ?>
                <tr><td colspan="6" class="text-center py-8" style="color:var(--muted)">Belum ada log aktivitas</td></tr>
                <?php endif; ?>
                <?php while ($l = $logs->fetch_assoc()): ?>
                <tr>
                    <td class="text-[11px] font-mono" style="color:var(--muted)"><?= date('d M Y H:i:s', strtotime($l['created_at'])) ?></td>
                    <td class="text-xs font-semibold" style="color:var(--text)"><?= htmlspecialchars($l['admin_name'] ?? '-') ?></td>
                    <td>
                        <?php
                        $color = 'info';
                        if (strpos($l['action'],'create')!==false || strpos($l['action'],'add')!==false) $color = 'success';
                        if (strpos($l['action'],'delete')!==false) $color = 'danger';
                        if (strpos($l['action'],'update')!==false || strpos($l['action'],'toggle')!==false) $color = 'warning';
                        ?>
                        <span class="badge badge-<?= $color ?>" style="font-size:10px"><?= htmlspecialchars($l['action']) ?></span>
                    </td>
                    <td class="text-xs">
                        <?php if ($l['entity_type']): ?>
                        <span style="color:var(--text2)"><?= htmlspecialchars($l['entity_type']) ?></span>
                        <span style="color:var(--muted)">#<?= $l['entity_id'] ?></span>
                        <?php else: ?>
                        <span style="color:var(--muted)">-</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-xs truncate max-w-[250px]" style="color:var(--text2)" title="<?= htmlspecialchars($l['details'] ?? '') ?>"><?= htmlspecialchars($l['details'] ?? '-') ?></td>
                    <td class="text-[10px] font-mono" style="color:var(--muted)"><?= htmlspecialchars($l['ip_address'] ?? '-') ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <?php if ($totalPages > 1): ?>
    <div class="flex items-center justify-between mt-4 pt-4" style="border-top:1px solid var(--border)">
        <span class="text-xs" style="color:var(--muted)">Halaman <?= $page ?> dari <?= $totalPages ?></span>
        <div class="flex gap-2">
            <?php if ($page > 1): ?>
            <a href="?page=<?= $page-1 ?>&action_filter=<?= urlencode($filterAction) ?>&date=<?= urlencode($filterDate) ?>" class="btn btn-outline btn-sm"><i class="ri-arrow-left-s-line"></i> Prev</a>
            <?php endif; ?>
            <?php if ($page < $totalPages): ?>
            <a href="?page=<?= $page+1 ?>&action_filter=<?= urlencode($filterAction) ?>&date=<?= urlencode($filterDate) ?>" class="btn btn-outline btn-sm">Next <i class="ri-arrow-right-s-line"></i></a>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
