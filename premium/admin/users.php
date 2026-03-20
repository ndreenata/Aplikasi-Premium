<?php
/**
 * ADMIN/USERS.PHP — User Management
 */
$pageTitle = 'Kelola Users';
require_once __DIR__ . '/../includes/admin_header.php';

$search = trim($_GET['q'] ?? '');
$sql = "SELECT u.*, (SELECT COUNT(*) FROM transactions t WHERE t.user_id=u.id) as order_count FROM users u WHERE 1=1";
if ($search) $sql .= " AND (u.name LIKE '%$search%' OR u.email LIKE '%$search%')";
$sql .= " ORDER BY u.id DESC";
$users = $conn->query($sql);
?>

<!-- Header -->
<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 mb-5">
    <div>
        <h1 class="text-lg font-black" style="color:var(--text)">Users</h1>
        <p class="text-[11px]" style="color:var(--muted)">Kelola pengguna terdaftar</p>
    </div>
</div>

<!-- Search -->
<div class="mb-4">
    <form method="GET" class="flex gap-2">
        <input type="text" name="q" value="<?= htmlspecialchars($search) ?>" placeholder="Cari nama / email..." class="admin-input" style="max-width:280px">
        <button type="submit" class="btn btn-outline btn-sm"><i class="ri-search-line"></i></button>
    </form>
</div>

<!-- Table -->
<div class="admin-card" style="padding:0;overflow:hidden">
    <div class="overflow-x-auto">
        <table class="admin-table">
            <thead>
                <tr><th>ID</th><th>User</th><th>Phone</th><th>Role</th><th>Orders</th><th>Bergabung</th><th style="text-align:right">Aksi</th></tr>
            </thead>
            <tbody>
                <?php if ($users->num_rows === 0): ?>
                <tr><td colspan="7" class="text-center py-8" style="color:var(--muted)">Tidak ada data user</td></tr>
                <?php endif; ?>
                <?php while ($u = $users->fetch_assoc()): ?>
                <tr>
                    <td class="text-[11px] font-mono" style="color:var(--muted)">#<?= $u['id'] ?></td>
                    <td>
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center text-white text-[10px] font-bold <?= $u['role']==='admin'?'bg-purple-600':'bg-green-600' ?>"><?= strtoupper(substr($u['name'],0,1)) ?></div>
                            <div>
                                <p class="text-xs font-bold" style="color:var(--text)"><?= htmlspecialchars($u['name']) ?></p>
                                <p class="text-[10px]" style="color:var(--muted)"><?= htmlspecialchars($u['email']) ?></p>
                            </div>
                        </div>
                    </td>
                    <td class="text-xs" style="color:var(--text2)"><?= htmlspecialchars($u['phone'] ?? '-') ?></td>
                    <td>
                        <span class="badge <?= $u['role']==='admin'?'badge-info':'badge-muted' ?>">
                            <i class="ri-<?= $u['role']==='admin'?'shield-star-fill':'user-fill' ?>"></i>
                            <?= ucfirst($u['role']) ?>
                        </span>
                    </td>
                    <td class="text-xs font-semibold" style="color:var(--text2)"><?= $u['order_count'] ?></td>
                    <td class="text-[11px]" style="color:var(--muted)"><?= date('d M Y', strtotime($u['created_at'])) ?></td>
                    <td style="text-align:right">
                        <?php if ($u['id'] != $admin['id']): // Can't change own role ?>
                        <button onclick="toggleRole(<?= $u['id'] ?>,'<?= $u['role']==='admin'?'user':'admin' ?>')" class="btn btn-outline btn-sm" title="Toggle role">
                            <i class="ri-refresh-fill"></i> <?= $u['role']==='admin'?'Set User':'Set Admin' ?>
                        </button>
                        <?php else: ?>
                        <span class="text-[10px]" style="color:var(--muted)">You</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function toggleRole(id, role) {
    confirmAction('Ubah role user ini menjadi ' + role.toUpperCase() + '?', function() {
        adminAPI('user_toggle_role', { id: id, role: role }, function(d) {
            if (d.ok) { showToast(d.msg); location.reload(); }
            else showToast(d.msg, 'error');
        });
    });
}
</script>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
