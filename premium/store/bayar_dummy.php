<?php
/**
 * BAYAR_DUMMY.PHP — Premium Payment Page · Gen Z Aesthetic
 */
require_once __DIR__ . '/../includes/koneksi.php';

$invoice = trim($_GET['invoice'] ?? '');
if (empty($invoice)) { header('Location: ' . BASE_URL . '/index.php'); exit; }

$stmt = $conn->prepare("SELECT t.*, p.name as product_name FROM transactions t JOIN products p ON t.product_id=p.id WHERE t.invoice_number=? LIMIT 1");
$stmt->bind_param("s",$invoice); $stmt->execute();
$trx = $stmt->get_result()->fetch_assoc(); $stmt->close();

if (!$trx) { $_SESSION['flash']=['type'=>'error','msg'=>'Invoice tidak ditemukan.']; header('Location: ' . BASE_URL . '/index.php'); exit; }
if ($trx['status']==='SUCCESS') { $_SESSION['flash']=['type'=>'success','msg'=>'Transaksi sudah diproses.']; header('Location: ' . BASE_URL . '/index.php'); exit; }

$pageTitle = 'Pembayaran — ' . SITE_NAME;
include __DIR__ . '/../includes/header.php';
?>

<style>
    /* Payment Processing Overlay */
    .pay-overlay {
        position: fixed; inset: 0; z-index: 9999;
        display: none; align-items: center; justify-content: center; flex-direction: column;
        background: var(--bg);
        backdrop-filter: blur(8px);
    }
    .pay-overlay.active { display: flex; }

    .pay-dots { display: flex; gap: 6px; margin-bottom: 16px; }
    .pay-dots span {
        width: 10px; height: 10px; border-radius: 50%;
        background: var(--accent);
        animation: dotBounce 1.2s ease infinite;
    }
    .pay-dots span:nth-child(2) { animation-delay: 0.15s }
    .pay-dots span:nth-child(3) { animation-delay: 0.3s }
    @keyframes dotBounce {
        0%, 80%, 100% { transform: scale(0.6); opacity: 0.4 }
        40% { transform: scale(1.1); opacity: 1 }
    }

    .pay-loader-ring {
        width: 48px; height: 48px; border-radius: 50%;
        border: 3px solid var(--border);
        border-top-color: var(--accent);
        animation: spinRing 0.7s linear infinite;
        margin-bottom: 14px;
    }
    @keyframes spinRing { to { transform: rotate(360deg) } }

    .pay-overlay .pay-text {
        font-size: 13px; font-weight: 700; color: var(--text);
    }
    .pay-overlay .pay-sub {
        font-size: 11px; color: var(--muted); margin-top: 4px;
    }

    /* Card entrance */
    .pay-card-enter { animation: payCardIn 0.5s cubic-bezier(0.16,1,0.3,1) both; }
    @keyframes payCardIn { from { opacity:0; transform:translateY(20px) } to { opacity:1; transform:translateY(0) } }
</style>

<!-- Payment Processing Overlay -->
<div class="pay-overlay" id="payOverlay">
    <div class="pay-loader-ring"></div>
    <div class="pay-dots">
        <span></span><span></span><span></span>
    </div>
    <p class="pay-text">Memproses Pembayaran...</p>
    <p class="pay-sub">Mohon tunggu sebentar</p>
</div>

<section class="py-12 sm:py-20 flex items-center justify-center min-h-[60vh]">
    <div class="w-full max-w-lg mx-4">
        <div class="glass-strong rounded-3xl p-7 sm:p-9 pay-card-enter">
            <div class="text-center mb-6">
                <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center"><i class="ri-wallet-3-fill text-3xl text-amber-400"></i></div>
                <h1 class="text-xl font-black" style="color:var(--text)">Konfirmasi Pembayaran</h1>
                <p class="text-xs mt-1" style="color:var(--muted)">Selesaikan pembayaran untuk menerima akun</p>
            </div>

            <div class="rounded-2xl p-5 mb-6 space-y-3" style="background:var(--surface)">
                <div class="flex justify-between"><span class="text-[11px] flex items-center gap-1" style="color:var(--muted)"><i class="ri-bill-line"></i>Invoice</span><span class="text-xs font-mono font-bold" style="color:var(--text)"><?= htmlspecialchars($trx['invoice_number']) ?></span></div>
                <div class="h-px" style="background:var(--border)"></div>
                <div class="flex justify-between"><span class="text-[11px] flex items-center gap-1" style="color:var(--muted)"><i class="ri-box-3-line"></i>Produk</span><span class="text-xs font-semibold" style="color:var(--text)"><?= htmlspecialchars($trx['product_name']) ?></span></div>
                <div class="h-px" style="background:var(--border)"></div>
                <div class="flex justify-between"><span class="text-[11px] flex items-center gap-1" style="color:var(--muted)"><i class="ri-user-3-line"></i>Nama</span><span class="text-xs" style="color:var(--text)"><?= htmlspecialchars($trx['customer_name']) ?></span></div>
                <div class="h-px" style="background:var(--border)"></div>
                <div class="flex justify-between"><span class="text-[11px] flex items-center gap-1" style="color:var(--muted)"><i class="ri-whatsapp-line text-[#25D366]"></i>WhatsApp</span><span class="text-xs" style="color:var(--text2)">+<?= htmlspecialchars($trx['phone_number']) ?></span></div>
                <div class="h-px" style="background:var(--border)"></div>
                <div class="flex justify-between items-center"><span class="text-[11px] flex items-center gap-1" style="color:var(--muted)"><i class="ri-money-dollar-circle-line"></i>Total</span><span class="text-xl font-black text-green-500">Rp <?= number_format($trx['amount'],0,',','.') ?></span></div>
            </div>

            <!-- QR Code Payment Section -->
            <div class="rounded-2xl p-5 mb-6" style="background:var(--surface)">
                <div class="text-center mb-3">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full glass text-green-500 text-[10px] font-bold"><i class="ri-qr-code-fill"></i>Scan QR untuk Bayar</span>
                </div>
                <div class="flex justify-center mb-3">
                    <div class="p-3 rounded-2xl bg-white" id="qrCodeContainer" style="display:inline-block">
                        <!-- QR code renders here -->
                    </div>
                </div>
                <p class="text-[10px] text-center" style="color:var(--muted)">Scan QR code ini dengan aplikasi e-wallet atau mobile banking</p>
                <div class="flex justify-center gap-3 mt-3">
                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg glass text-[9px] font-semibold" style="color:var(--text2)"><i class="ri-bank-card-fill text-blue-500"></i>QRIS</span>
                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg glass text-[9px] font-semibold" style="color:var(--text2)"><i class="ri-wallet-3-fill text-green-500"></i>GoPay</span>
                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg glass text-[9px] font-semibold" style="color:var(--text2)"><i class="ri-wallet-fill text-blue-400"></i>DANA</span>
                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg glass text-[9px] font-semibold" style="color:var(--text2)"><i class="ri-shopping-bag-fill text-purple-500"></i>OVO</span>
                </div>
            </div>

            <div class="glass rounded-xl p-3 mb-6 text-[11px] flex items-start gap-2" style="color:var(--text2)">
                <i class="ri-information-fill text-green-500 mt-0.5"></i>
                <span>Ini adalah halaman simulasi pembayaran. Pada produksi, halaman ini akan terhubung ke payment gateway.</span>
            </div>

            <a href="<?= BASE_URL ?>/store/proses_lunas.php?invoice=<?= urlencode($trx['invoice_number']) ?>" id="payBtn" onclick="showPayLoading(event)" class="w-full inline-flex items-center justify-center gap-2 px-4 py-4 bg-green-600 text-white text-sm font-bold rounded-2xl hover:bg-green-500 btn-press shadow-lg shadow-green-600/20 pulse-glow"><i class="ri-check-double-fill"></i> Konfirmasi Pembayaran</a>
        </div>
    </div>
</section>

<script>
function showPayLoading(e) {
    e.preventDefault();
    var overlay = document.getElementById('payOverlay');
    var btn = document.getElementById('payBtn');
    overlay.classList.add('active');
    btn.style.pointerEvents = 'none';
    btn.style.opacity = '0.5';
    // Redirect after animation plays
    setTimeout(function() {
        window.location.href = btn.href;
    }, 1800);
}
</script>

<!-- QR Code Generator -->
<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
<script>
(function(){
    var container = document.getElementById('qrCodeContainer');
    if(container){
        try {
            new QRCode(container, {
                text: 'NATSY-PAY:<?= htmlspecialchars($trx["invoice_number"]) ?>:<?= $trx["amount"] ?>',
                width: 160,
                height: 160,
                colorDark: '#111827',
                colorLight: '#ffffff',
                correctLevel: QRCode.CorrectLevel.H
            });
        } catch(e) {
            container.innerHTML = '<div style="width:160px;height:160px;display:flex;align-items:center;justify-content:center;background:#f3f4f6;border-radius:12px"><i class="ri-qr-code-fill text-4xl" style="color:#9ca3af"></i></div>';
        }
    }
})();
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
