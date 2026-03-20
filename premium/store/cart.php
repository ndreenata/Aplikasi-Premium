<?php
/**
 * CART.PHP — Shopping cart page (premiumisme-style)
 * Displays cart items, summary, voucher input, contact info, checkout
 */
require_once __DIR__ . '/../includes/koneksi.php';

$cart = $_SESSION['cart'] ?? [];

// Icon map
$iconMap = [
    'netflix'=>['ri-movie-2-fill','bg-red-500/10','text-red-500'],
    'spotify'=>['ri-spotify-fill','bg-green-500/10','text-green-500'],
    'youtube'=>['ri-youtube-fill','bg-red-500/10','text-red-600'],
    'disney'=>['ri-movie-fill','bg-blue-500/10','text-blue-600'],
    'viu'=>['ri-film-fill','bg-pink-500/10','text-pink-500'],
    'wetv'=>['ri-tv-2-fill','bg-indigo-500/10','text-indigo-500'],
    'prime'=>['ri-video-fill','bg-sky-500/10','text-sky-600'],
    'hbo'=>['ri-film-fill','bg-purple-500/10','text-purple-600'],
    'canva'=>['ri-palette-fill','bg-teal-500/10','text-teal-500'],
    'capcut'=>['ri-scissors-fill','bg-violet-500/10','text-violet-500'],
    'vsco'=>['ri-camera-lens-fill','bg-gray-500/10','text-gray-500'],
    'lightroom'=>['ri-contrast-2-fill','bg-blue-500/10','text-blue-500'],
    'alight'=>['ri-movie-2-fill','bg-pink-500/10','text-pink-500'],
    'chatgpt'=>['ri-openai-fill','bg-emerald-500/10','text-emerald-600'],
    'zoom'=>['ri-video-chat-fill','bg-blue-500/10','text-blue-500'],
    'google'=>['ri-google-fill','bg-amber-500/10','text-amber-500'],
    'microsoft'=>['ri-microsoft-fill','bg-blue-500/10','text-blue-600'],
];

$pageTitle = 'Keranjang — ' . SITE_NAME;
include __DIR__ . '/../includes/header.php';

// Calculate total
$subtotal = 0;
foreach ($cart as $item) $subtotal += $item['price'];
?>

<section class="min-h-screen py-10" style="background:var(--bg)">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Header -->
        <div class="flex items-center justify-between mb-6" data-aos="fade-down">
            <div class="flex items-center gap-3">
                <a href="index.php" class="w-10 h-10 rounded-xl glass flex items-center justify-center hover:border-green-500 transition btn-press" style="color:var(--text)"><i class="ri-arrow-left-line"></i></a>
                <div>
                    <h1 class="text-lg font-black" style="color:var(--text)">Keranjang Belanja</h1>
                    <p class="text-[10px]" style="color:var(--muted)"><?= count($cart) ?> item</p>
                </div>
            </div>
            <?php if (count($cart) > 0): ?>
            <button onclick="clearCart()" class="text-xs font-bold text-red-400 hover:text-red-300 transition"><i class="ri-delete-bin-6-line mr-1"></i>Kosongkan</button>
            <?php endif; ?>
        </div>

        <?php if (count($cart) === 0): ?>
        <!-- Empty cart -->
        <div class="glass-strong rounded-3xl p-12 text-center" data-aos="fade-up">
            <div class="w-20 h-20 mx-auto mb-5 rounded-2xl bg-green-500/10 flex items-center justify-center">
                <i class="ri-shopping-cart-2-line text-3xl text-green-500"></i>
            </div>
            <h3 class="text-lg font-bold mb-2" style="color:var(--text)">Keranjang Kosong</h3>
            <p class="text-xs mb-6" style="color:var(--muted)">Belum ada produk yang ditambahkan ke keranjang.</p>
            <a href="index.php#products" class="inline-flex items-center gap-2 px-7 py-3.5 bg-green-600 text-white text-xs font-bold rounded-2xl hover:bg-green-500 btn-press shadow-lg shadow-green-600/25 pulse-glow"><i class="ri-store-2-line"></i>Jelajahi Produk</a>
        </div>
        <?php else: ?>

        <!-- Info banner -->
        <div class="rounded-2xl p-4 mb-5 flex items-start gap-3" style="background:rgba(34,197,94,0.06);border:1px solid rgba(34,197,94,0.12)" data-aos="fade-up">
            <i class="ri-information-line text-green-500 text-lg mt-0.5"></i>
            <div>
                <p class="text-xs font-bold mb-0.5" style="color:var(--text)">Periksa kembali produk yang akan dibeli</p>
                <p class="text-[10px]" style="color:var(--muted)">Pastikan produk dan jumlah sesuai sebelum checkout. Setiap produk hanya bisa dibeli 1 item.</p>
            </div>
        </div>

        <!-- Cart items -->
        <div class="space-y-3 mb-6" id="cartItems" data-aos="fade-up">
            <?php foreach ($cart as $i => $item):
                $ik = strtolower(explode(' ', $item['name'])[0]);
                $iic = $iconMap[$ik] ?? ['ri-apps-fill','bg-gray-500/10','text-gray-400'];
            ?>
            <div class="glass-strong rounded-2xl p-4 flex items-center gap-4 cart-item" id="cartItem<?= $item['product_id'] ?>">
                <div class="w-12 h-12 shrink-0 rounded-xl <?= $iic[1] ?> flex items-center justify-center">
                    <i class="<?= $iic[0] ?> text-xl <?= $iic[2] ?>"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="text-sm font-bold truncate" style="color:var(--text)"><?= htmlspecialchars($item['name']) ?></h3>
                    <p class="text-[10px] uppercase tracking-wider" style="color:var(--muted)"><?= htmlspecialchars(str_replace('_',' & ',$item['category'])) ?></p>
                </div>
                <div class="text-right shrink-0">
                    <p class="text-sm font-black text-green-500"><?= rupiah($item['price']) ?></p>
                </div>
                <button onclick="removeItem(<?= $item['product_id'] ?>)" class="w-9 h-9 shrink-0 rounded-xl flex items-center justify-center glass text-red-400 hover:bg-red-500/10 transition btn-press" title="Hapus">
                    <i class="ri-delete-bin-6-line"></i>
                </button>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Summary + Checkout form -->
        <div class="glass-strong rounded-3xl p-6 sm:p-8" data-aos="fade-up">
            <!-- Voucher -->
            <div class="mb-5">
                <label class="block text-[10px] font-black uppercase tracking-widest mb-2" style="color:var(--muted)">Kode Voucher</label>
                <div class="flex gap-2">
                    <input type="text" id="voucherInput" placeholder="Masukkan kode voucher" class="flex-1 glass rounded-xl px-4 py-3 text-xs outline-none focus:border-green-500 transition" style="color:var(--text);background:var(--surface)">
                    <button onclick="applyVoucher()" class="px-5 py-3 glass rounded-xl text-xs font-bold text-green-500 hover:border-green-500 transition btn-press">Gunakan</button>
                </div>
            </div>

            <!-- Summary -->
            <div class="space-y-2 mb-5">
                <div class="flex justify-between items-center">
                    <span class="text-xs" style="color:var(--text2)">Subtotal (<span id="summaryCount"><?= count($cart) ?></span> item)</span>
                    <span class="text-sm font-bold" style="color:var(--text)" id="summarySubtotal"><?= rupiah($subtotal) ?></span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-xs" style="color:var(--text2)">Diskon Voucher</span>
                    <span class="text-sm font-bold text-green-500" id="summaryDiscount">-Rp 0</span>
                </div>
                <div class="pt-3" style="border-top:1px solid var(--border)">
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-bold" style="color:var(--text)">Total</span>
                        <span class="text-xl font-black text-green-500" id="summaryTotal"><?= rupiah($subtotal) ?></span>
                    </div>
                </div>
            </div>

            <!-- Contact Info -->
            <form id="checkoutForm" action="checkout.php" method="POST">
                <?= csrfField() ?>
                <input type="hidden" name="from_cart" value="1">
                <input type="hidden" name="voucher_code" id="voucherCodeHidden" value="">
                <input type="hidden" name="voucher_discount" id="voucherDiscountHidden" value="0">
                <div class="mb-5">
                    <label class="block text-[10px] font-black uppercase tracking-widest mb-2" style="color:var(--muted)">Informasi Kontak</label>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-[10px] font-semibold mb-1" style="color:var(--text2)">Nama Lengkap</label>
                            <input type="text" name="customer_name" required placeholder="Nama kamu"
                                value="<?= isLoggedIn() ? htmlspecialchars(currentUser()['name']) : '' ?>"
                                class="w-full glass rounded-xl px-4 py-3 text-xs outline-none focus:border-green-500 transition" style="color:var(--text);background:var(--surface)">
                        </div>
                        <div>
                            <label class="block text-[10px] font-semibold mb-1" style="color:var(--text2)">Nomor WhatsApp</label>
                            <div class="flex items-center gap-2">
                                <span class="glass rounded-xl px-3 py-3 text-xs font-bold" style="color:var(--text)">+62</span>
                                <input type="tel" name="phone" required placeholder="8123456789" pattern="[0-9]{9,13}"
                                    value="<?= isLoggedIn() && !empty(currentUser()['phone']) ? htmlspecialchars(ltrim(currentUser()['phone'],'0')) : '' ?>"
                                    class="flex-1 glass rounded-xl px-4 py-3 text-xs outline-none focus:border-green-500 transition" style="color:var(--text);background:var(--surface)">
                            </div>
                        </div>
                    </div>
                </div>

                <?php if (!isLoggedIn()): ?>
                <div class="rounded-xl p-3 mb-4 text-[11px] flex items-start gap-2" style="background:rgba(251,191,36,0.08);border:1px solid rgba(251,191,36,0.15);color:var(--text2)">
                    <i class="ri-error-warning-fill text-amber-400 mt-0.5"></i>
                    <span><a href="<?= BASE_URL ?>/auth/login.php" class="text-green-500 font-bold hover:underline">Login</a> dulu untuk riwayat pesanan otomatis.</span>
                </div>
                <?php endif; ?>

                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-4 bg-green-600 text-white text-sm font-bold rounded-2xl hover:bg-green-500 btn-press shadow-lg shadow-green-600/25 pulse-glow">
                    <i class="ri-shopping-bag-3-fill"></i> Order Sekarang — <span id="btnTotal"><?= rupiah($subtotal) ?></span>
                </button>
            </form>
        </div>

        <?php endif; ?>
    </div>
</section>


<script>
function removeItem(pid) {
    var el = document.getElementById('cartItem' + pid);
    if (el) {
        el.style.opacity = '0'; el.style.transform = 'translateX(-100%)';
        el.style.transition = 'all 0.3s ease';
    }
    fetch('cart_api.php', {
        method: 'POST',
        headers: {'Content-Type':'application/x-www-form-urlencoded'},
        body: 'action=remove&product_id=' + pid
    }).then(r => r.json()).then(data => {
        setTimeout(() => location.reload(), 350);
    });
}

function clearCart() {
    if (!confirm('Kosongkan semua item dari keranjang?')) return;
    fetch('cart_api.php', {
        method: 'POST',
        headers: {'Content-Type':'application/x-www-form-urlencoded'},
        body: 'action=clear'
    }).then(r => r.json()).then(data => {
        location.reload();
    });
}

var currentDiscount = 0;
var subtotal = <?= $subtotal ?>;

function applyVoucher() {
    var code = document.getElementById('voucherInput').value.trim();
    if (!code) return;
    var btn = event.target;
    btn.disabled = true; btn.textContent = 'Memvalidasi...';
    fetch('<?= BASE_URL ?>/admin/api.php', {
        method: 'POST',
        headers: {'Content-Type':'application/x-www-form-urlencoded'},
        body: 'action=voucher_validate&code=' + encodeURIComponent(code) + '&subtotal=' + subtotal
    }).then(r => r.json()).then(data => {
        btn.disabled = false; btn.textContent = 'Gunakan';
        if (data.ok) {
            currentDiscount = data.discount;
            document.getElementById('summaryDiscount').textContent = '-' + formatRp(data.discount);
            document.getElementById('summaryTotal').textContent = formatRp(subtotal - data.discount);
            document.getElementById('btnTotal').textContent = formatRp(subtotal - data.discount);
            document.getElementById('voucherCodeHidden').value = data.code;
            document.getElementById('voucherDiscountHidden').value = data.discount;
            document.getElementById('voucherInput').disabled = true;
            document.getElementById('voucherInput').style.borderColor = '#16A34A';
            btn.textContent = '✓ Applied';
            btn.style.color = '#16A34A';
        } else {
            alert(data.msg);
        }
    }).catch(function(){ btn.disabled = false; btn.textContent = 'Gunakan'; alert('Gagal memvalidasi voucher'); });
}

function formatRp(n) {
    return 'Rp ' + n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
