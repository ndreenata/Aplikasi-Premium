<?php
/**
 * REQUEST_PRODUCT.PHP — Custom Product Request Form (#25)
 */
require_once __DIR__ . '/../includes/koneksi.php';
$pageTitle = 'Request Produk — ' . SITE_NAME;
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $conn->real_escape_string($_POST['product_name'] ?? '');
    $desc = $conn->real_escape_string($_POST['description'] ?? '');
    $budget = $conn->real_escape_string($_POST['budget'] ?? '');
    $userId = isLoggedIn() ? (int)$_SESSION['user_id'] : null;
    $guestName = $conn->real_escape_string($_POST['guest_name'] ?? '');
    $guestEmail = $conn->real_escape_string($_POST['guest_email'] ?? '');

    if ($name) {
        $conn->query("INSERT INTO product_requests (user_id, guest_name, guest_email, product_name, description, budget) VALUES (" . ($userId ? $userId : 'NULL') . ", '$guestName', '$guestEmail', '$name', '$desc', '$budget')");
        $conn->query("INSERT INTO admin_notifications (type, title, message) VALUES ('request', 'Request Produk Baru', 'Seseorang request: $name')");
        $msg = 'success';
    }
}

include __DIR__ . '/../includes/header.php';
?>

<section class="py-10 min-h-screen" style="background:var(--bg)">
    <div class="max-w-xl mx-auto px-4 sm:px-6">
        <nav class="flex items-center gap-2 text-xs mb-6" style="color:var(--muted)">
            <a href="<?= BASE_URL ?>/index.php" class="hover:text-green-500">Home</a><i class="ri-arrow-right-s-line"></i>
            <span style="color:var(--text)">Request Produk</span>
        </nav>

        <div class="glass-strong rounded-2xl p-8" data-aos="fade-up">
            <div class="text-center mb-6">
                <div class="w-16 h-16 rounded-2xl bg-green-500/10 flex items-center justify-center mx-auto mb-3">
                    <i class="ri-add-circle-fill text-3xl text-green-500"></i>
                </div>
                <h1 class="text-xl font-black" style="color:var(--text)">Request Produk Baru</h1>
                <p class="text-xs mt-1" style="color:var(--muted)">Produk yang kamu cari belum ada? Request di sini!</p>
            </div>

            <?php if($msg === 'success'): ?>
            <div class="rounded-xl p-4 bg-green-500/10 text-center">
                <i class="ri-check-double-fill text-3xl text-green-500 mb-2"></i>
                <p class="text-sm font-bold text-green-600">Request Terkirim! 🎉</p>
                <p class="text-xs mt-1" style="color:var(--muted)">Kami akan review dan kabari kamu via WhatsApp/Email.</p>
            </div>
            <?php else: ?>
            <form method="POST" class="space-y-4">
                <?php if(!isLoggedIn()): ?>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold mb-1" style="color:var(--text)">Nama</label>
                        <input type="text" name="guest_name" class="w-full px-4 py-3 rounded-xl text-sm glass" style="color:var(--text);background:var(--surface)" placeholder="Nama kamu" required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold mb-1" style="color:var(--text)">Email/WA</label>
                        <input type="text" name="guest_email" class="w-full px-4 py-3 rounded-xl text-sm glass" style="color:var(--text);background:var(--surface)" placeholder="Email atau no WA" required>
                    </div>
                </div>
                <?php endif; ?>
                <div>
                    <label class="block text-xs font-bold mb-1" style="color:var(--text)">Nama Produk/Aplikasi *</label>
                    <input type="text" name="product_name" class="w-full px-4 py-3 rounded-xl text-sm glass" style="color:var(--text);background:var(--surface)" placeholder="Contoh: Adobe Premiere Pro, Grammarly Premium" required>
                </div>
                <div>
                    <label class="block text-xs font-bold mb-1" style="color:var(--text)">Deskripsi (opsional)</label>
                    <textarea name="description" rows="3" class="w-full px-4 py-3 rounded-xl text-sm glass" style="color:var(--text);background:var(--surface)" placeholder="Jelaskan kebutuhan kamu..."></textarea>
                </div>
                <div>
                    <label class="block text-xs font-bold mb-1" style="color:var(--text)">Budget (opsional)</label>
                    <input type="text" name="budget" class="w-full px-4 py-3 rounded-xl text-sm glass" style="color:var(--text);background:var(--surface)" placeholder="Contoh: Rp 50.000 - Rp 100.000">
                </div>
                <button type="submit" class="w-full py-3 rounded-xl bg-green-600 text-white font-bold text-sm hover:bg-green-500 transition shadow-lg shadow-green-600/25">
                    <i class="ri-send-plane-fill mr-1"></i> Kirim Request
                </button>
            </form>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
