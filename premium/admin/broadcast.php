<?php
/**
 * ADMIN/BROADCAST.PHP — Broadcast Notification to Users
 * Send notifications to all users or specific groups
 */
$pageTitle = 'Broadcast Notifikasi';
require_once __DIR__ . '/../includes/admin_header.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrfCheck();
    $title = trim($_POST['title'] ?? '');
    $message = trim($_POST['message'] ?? '');
    $type = $_POST['type'] ?? 'info';
    $link = trim($_POST['link'] ?? '');
    $target = $_POST['target'] ?? 'all';

    if (empty($title) || empty($message)) {
        $error = 'Judul dan pesan wajib diisi.';
    } else {
        $users = [];
        if ($target === 'all') {
            $res = $conn->query("SELECT id FROM users");
            while ($u = $res->fetch_assoc()) { $users[] = $u['id']; }
        } elseif ($target === 'buyers') {
            $res = $conn->query("SELECT DISTINCT user_id FROM transactions WHERE user_id IS NOT NULL");
            while ($u = $res->fetch_assoc()) { $users[] = $u['user_id']; }
        }

        $count = 0;
        foreach ($users as $uid) {
            addNotification($conn, $uid, $title, $message, $type, $link ?: null);
            $count++;
        }
        $success = "Notifikasi berhasil dikirim ke $count pengguna!";
        logAction($conn, $admin, 'broadcast_notification', 'notification', null, "Target: $target, Title: $title, Sent to: $count users");
    }
}

// Recent broadcasts from audit log
$recentBroadcasts = $conn->query("SELECT * FROM audit_logs WHERE action='broadcast_notification' ORDER BY created_at DESC LIMIT 10");
?>

<div class="p-4 sm:p-6 lg:p-8">
    <div class="mb-6">
        <h1 class="text-xl font-black" style="color:var(--text)"><i class="ri-broadcast-fill text-amber-400 mr-2"></i>Broadcast Notifikasi</h1>
        <p class="text-xs mt-1" style="color:var(--muted)">Kirim notifikasi massal ke pengguna</p>
    </div>

    <?php if ($success): ?>
    <div class="mb-4 p-4 rounded-xl bg-green-500/10 border border-green-500/20 flex items-center gap-3">
        <i class="ri-check-double-fill text-green-500 text-xl"></i>
        <p class="text-sm font-semibold text-green-500"><?= $success ?></p>
    </div>
    <?php endif; ?>
    <?php if ($error): ?>
    <div class="mb-4 p-4 rounded-xl bg-red-500/10 border border-red-500/20 flex items-center gap-3">
        <i class="ri-error-warning-fill text-red-400 text-xl"></i>
        <p class="text-sm font-semibold text-red-400"><?= $error ?></p>
    </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Send Form -->
        <div class="lg:col-span-2">
            <form method="POST" class="glass-strong rounded-2xl p-6 space-y-5">
                <?= csrfField() ?>
                <div>
                    <label class="block text-xs font-bold mb-2" style="color:var(--text)">Judul Notifikasi</label>
                    <input type="text" name="title" required placeholder="Contoh: Promo Flash Sale 🔥" class="w-full px-4 py-3 rounded-xl text-sm outline-none transition" style="background:var(--bg);border:1.5px solid var(--border);color:var(--text)" onfocus="this.style.borderColor='#22C55E'" onblur="this.style.borderColor='var(--border)'">
                </div>
                <div>
                    <label class="block text-xs font-bold mb-2" style="color:var(--text)">Pesan</label>
                    <textarea name="message" required rows="4" placeholder="Tulis pesan notifikasi..." class="w-full px-4 py-3 rounded-xl text-sm outline-none transition resize-none" style="background:var(--bg);border:1.5px solid var(--border);color:var(--text)" onfocus="this.style.borderColor='#22C55E'" onblur="this.style.borderColor='var(--border)'"></textarea>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold mb-2" style="color:var(--text)">Tipe</label>
                        <select name="type" class="w-full px-4 py-3 rounded-xl text-sm outline-none" style="background:var(--bg);border:1.5px solid var(--border);color:var(--text)">
                            <option value="info">ℹ️ Info</option>
                            <option value="success">✅ Success</option>
                            <option value="warning">⚠️ Warning</option>
                            <option value="promo">🎁 Promo</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold mb-2" style="color:var(--text)">Target</label>
                        <select name="target" class="w-full px-4 py-3 rounded-xl text-sm outline-none" style="background:var(--bg);border:1.5px solid var(--border);color:var(--text)">
                            <option value="all">Semua User</option>
                            <option value="buyers">Pembeli Saja</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold mb-2" style="color:var(--text)">Link (opsional)</label>
                        <input type="text" name="link" placeholder="https://..." class="w-full px-4 py-3 rounded-xl text-sm outline-none" style="background:var(--bg);border:1.5px solid var(--border);color:var(--text)">
                    </div>
                </div>
                <button type="submit" class="w-full px-6 py-4 bg-amber-500 text-white text-sm font-bold rounded-xl hover:bg-amber-400 btn-press shadow-lg shadow-amber-500/20">
                    <i class="ri-send-plane-fill mr-2"></i>Kirim Broadcast
                </button>
            </form>
        </div>

        <!-- Recent Broadcasts -->
        <div>
            <div class="glass-strong rounded-2xl p-5">
                <h3 class="text-sm font-bold mb-4" style="color:var(--text)"><i class="ri-history-fill text-blue-400 mr-1"></i>Riwayat Broadcast</h3>
                <div class="space-y-3 max-h-96 overflow-y-auto">
                    <?php if ($recentBroadcasts && $recentBroadcasts->num_rows > 0): ?>
                        <?php while ($b = $recentBroadcasts->fetch_assoc()): ?>
                        <div class="p-3 rounded-xl" style="background:var(--surface)">
                            <p class="text-[10px] font-semibold" style="color:var(--muted)"><?= date('d M Y H:i', strtotime($b['created_at'])) ?></p>
                            <p class="text-xs mt-1" style="color:var(--text)"><?= htmlspecialchars($b['details']) ?></p>
                        </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p class="text-xs text-center py-6" style="color:var(--muted)">Belum ada broadcast</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/admin_footer.php'; ?>
