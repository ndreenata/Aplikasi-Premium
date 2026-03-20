<?php
/**
 * PROSES_LUNAS.PHP — Payment Success · Premium Animation
 * Fonnte WA Delivery · Gen Z Aesthetic
 */
require_once __DIR__ . '/../includes/koneksi.php';

$invoice = trim($_GET['invoice'] ?? '');
if (empty($invoice)) { header('Location: ' . BASE_URL . '/index.php'); exit; }

$stmt = $conn->prepare("SELECT t.*, p.name as product_name FROM transactions t JOIN products p ON t.product_id=p.id WHERE t.invoice_number=? LIMIT 1");
$stmt->bind_param("s",$invoice); $stmt->execute();
$trx = $stmt->get_result()->fetch_assoc(); $stmt->close();

if (!$trx) { $_SESSION['flash']=['type'=>'error','msg'=>'Invoice tidak ditemukan.']; header('Location: ' . BASE_URL . '/index.php'); exit; }
if ($trx['status']==='SUCCESS') { $_SESSION['flash']=['type'=>'success','msg'=>'Transaksi sudah diproses.']; header('Location: ' . BASE_URL . '/index.php'); exit; }

$stmt=$conn->prepare("UPDATE transactions SET status='SUCCESS' WHERE invoice_number=?");
$stmt->bind_param("s",$invoice);$stmt->execute();$stmt->close();

$stmt=$conn->prepare("SELECT * FROM stocks WHERE product_id=? AND status='available' LIMIT 1");
$stmt->bind_param("i",$trx['product_id']);$stmt->execute();
$stock=$stmt->get_result()->fetch_assoc();$stmt->close();

$email='-';$pass='-';
if ($stock) {
    $stmt=$conn->prepare("UPDATE stocks SET status='sold' WHERE id=?");
    $stmt->bind_param("i",$stock['id']);$stmt->execute();$stmt->close();
    $parts=explode('|',$stock['account_data']);
    $email=$parts[0]??'-';$pass=$parts[1]??'-';
}

$custName=$trx['customer_name']??'Customer';
$message="Halo {$custName}! Terima kasih telah membeli di *Natsy Premiums*.\n\n"
    ."Pembayaran kamu sudah lunas. Berikut detail akun:\n\n"
    ."Invoice: {$invoice}\nProduk: {$trx['product_name']}\nEmail: {$email}\nPassword: {$pass}\n\n"
    ."Jangan ubah password & jangan share ke orang lain ya.\nAda masalah? Langsung chat admin!\n\nTerima kasih, *Natsy Premiums*";

$curl=curl_init();
curl_setopt_array($curl,[CURLOPT_URL=>'https://api.fonnte.com/send',CURLOPT_RETURNTRANSFER=>true,CURLOPT_POST=>true,CURLOPT_POSTFIELDS=>http_build_query(['target'=>$trx['phone_number'],'message'=>$message,'countryCode'=>'62']),CURLOPT_HTTPHEADER=>['Authorization: '.FONNTE_TOKEN]]);
$waResp=curl_exec($curl);$waErr=curl_error($curl);curl_close($curl);
error_log("[PROSES_LUNAS] Invoice:{$invoice} | ".($waErr?:"OK: {$waResp}"));

$pageTitle = 'Berhasil! — ' . SITE_NAME;
include __DIR__ . '/../includes/header.php';
?>

<style>
    /* Success Loading Overlay */
    .success-overlay {
        position: fixed; inset: 0; z-index: 9999;
        display: flex; align-items: center; justify-content: center; flex-direction: column;
        background: var(--bg);
        animation: overlayFade 0.6s 2.8s ease forwards;
    }
    @keyframes overlayFade { to { opacity:0; pointer-events:none; } }

    /* Spinner ring */
    .success-spinner {
        width: 56px; height: 56px; border-radius: 50%;
        border: 3px solid var(--border);
        border-top-color: var(--accent);
        animation: spinRing 0.8s linear infinite, hideSpinner 0s 1.6s forwards;
    }
    @keyframes spinRing { to { transform: rotate(360deg) } }
    @keyframes hideSpinner { to { display:none; width:0; height:0; overflow:hidden } }

    /* After spin: morph into checkmark */
    .success-check {
        width: 64px; height: 64px; border-radius:50%;
        background: linear-gradient(135deg, #22c55e 0%, #10b981 100%);
        display: flex; align-items: center; justify-content: center;
        opacity: 0; transform: scale(0.3);
        animation: checkPop 0.5s 1.6s cubic-bezier(0.34,1.56,0.64,1) forwards;
        box-shadow: 0 12px 40px rgba(34,197,94,0.3);
    }
    @keyframes checkPop { to { opacity:1; transform:scale(1) } }

    .success-check i {
        color: white; font-size: 28px;
        opacity: 0; transform: scale(0);
        animation: iconPop 0.3s 2s cubic-bezier(0.34,1.56,0.64,1) forwards;
    }
    @keyframes iconPop { to { opacity:1; transform:scale(1) } }

    .success-label {
        margin-top: 16px; font-size: 14px; font-weight: 800;
        color: var(--text); opacity: 0; transform: translateY(8px);
        animation: labelSlide 0.4s 2.2s ease forwards;
    }
    @keyframes labelSlide { to { opacity:1; transform:translateY(0) } }

    /* Confetti particles */
    .confetti {
        position: absolute; width:8px; height:8px; border-radius:2px;
        opacity:0; animation: confettiFall 1.2s 1.8s ease forwards;
    }
    @keyframes confettiFall {
        0% { opacity:1; transform:translateY(0) scale(1) rotate(0deg) }
        100% { opacity:0; transform:translateY(120px) scale(0.3) rotate(360deg) }
    }

    /* Content entrance */
    .content-enter {
        opacity:0; transform:translateY(24px);
        animation: contentUp 0.6s 3.2s cubic-bezier(0.16,1,0.3,1) forwards;
    }
    .content-enter.d2 { animation-delay: 3.35s }
    .content-enter.d3 { animation-delay: 3.5s }
</style>

<!-- Success Animation Overlay -->
<div class="success-overlay">
    <div class="success-spinner"></div>
    <div class="success-check"><i class="ri-check-line"></i></div>
    <p class="success-label">Pembayaran Berhasil!</p>
    <!-- Mini confetti -->
    <div class="confetti" style="top:40%;left:35%;background:#22c55e;animation-delay:1.9s"></div>
    <div class="confetti" style="top:38%;left:55%;background:#34d399;animation-delay:2s"></div>
    <div class="confetti" style="top:42%;left:42%;background:#06b6d4;animation-delay:2.1s"></div>
    <div class="confetti" style="top:36%;left:60%;background:#8b5cf6;animation-delay:1.85s"></div>
    <div class="confetti" style="top:44%;left:48%;background:#f59e0b;animation-delay:2.15s"></div>
    <div class="confetti" style="top:39%;left:38%;background:#ec4899;animation-delay:1.95s"></div>
    <div class="confetti" style="top:41%;left:52%;background:#22c55e;animation-delay:2.05s"></div>
    <div class="confetti" style="top:37%;left:45%;background:#06b6d4;animation-delay:2.2s"></div>
</div>

<section class="py-12 sm:py-20 flex items-center justify-center min-h-[60vh]">
    <div class="w-full max-w-lg mx-4">
        <div class="glass-strong rounded-3xl p-7 sm:p-9 text-center content-enter">
            <div class="w-20 h-20 mx-auto mb-5 rounded-2xl bg-green-500/10 border border-green-500/20 flex items-center justify-center">
                <i class="ri-checkbox-circle-fill text-4xl text-green-500"></i>
            </div>
            <h1 class="text-2xl font-black mb-1" style="color:var(--text)">Pembayaran Berhasil! 🎉</h1>
            <p class="text-xs mb-6" style="color:var(--muted)">Akun premium sudah dikirim ke WhatsApp-mu</p>

            <div class="content-enter d2 rounded-2xl p-5 mb-5 text-left space-y-3" style="background:var(--surface)">
                <div class="flex justify-between"><span class="text-[11px] flex items-center gap-1" style="color:var(--muted)"><i class="ri-bill-line"></i>Invoice</span><span class="text-xs font-mono font-bold" style="color:var(--text)"><?= htmlspecialchars($invoice) ?></span></div>
                <div class="h-px" style="background:var(--border)"></div>
                <div class="flex justify-between"><span class="text-[11px] flex items-center gap-1" style="color:var(--muted)"><i class="ri-box-3-line"></i>Produk</span><span class="text-xs font-semibold" style="color:var(--text)"><?= htmlspecialchars($trx['product_name']) ?></span></div>
                <div class="h-px" style="background:var(--border)"></div>
                <div class="flex justify-between"><span class="text-[11px] flex items-center gap-1" style="color:var(--muted)"><i class="ri-mail-line"></i>Email Akun</span><span class="text-xs font-semibold text-green-500"><?= htmlspecialchars($email) ?></span></div>
                <div class="h-px" style="background:var(--border)"></div>
                <div class="flex justify-between"><span class="text-[11px] flex items-center gap-1" style="color:var(--muted)"><i class="ri-key-line"></i>Password</span><span class="text-xs font-semibold text-green-500"><?= htmlspecialchars($pass) ?></span></div>
                <div class="h-px" style="background:var(--border)"></div>
                <div class="flex justify-between"><span class="text-[11px] flex items-center gap-1" style="color:var(--muted)"><i class="ri-whatsapp-line text-[#25D366]"></i>Dikirim ke</span><span class="text-xs" style="color:var(--text2)">+<?= htmlspecialchars($trx['phone_number']) ?></span></div>
            </div>

            <div class="content-enter d2 glass rounded-xl p-2.5 mb-5 text-[11px] text-green-500 flex items-start gap-2 text-left"><i class="ri-whatsapp-fill mt-0.5"></i><span>Detail akun juga sudah dikirim ke WhatsApp <strong>+<?= htmlspecialchars($trx['phone_number']) ?></strong></span></div>

            <div class="content-enter d3 flex gap-3">
                <a href="<?= BASE_URL ?>/index.php" class="flex-1 inline-flex items-center justify-center gap-1.5 py-3.5 bg-green-600 text-white text-xs font-bold rounded-xl hover:bg-green-500 btn-press shadow-lg shadow-green-600/20"><i class="ri-home-5-line"></i> Beranda</a>
                <?php if (isLoggedIn()): ?>
                <a href="<?= BASE_URL ?>/pages/profile.php" class="flex-1 inline-flex items-center justify-center gap-1.5 py-3.5 glass text-xs font-bold rounded-xl btn-press" style="color:var(--text)"><i class="ri-file-list-3-line text-green-500"></i> Pesanan</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
<script>
// ═══ CONFETTI CANNON ═══
(function(){
    var colors = ['#22c55e','#10b981','#06b6d4','#8b5cf6','#f59e0b','#ec4899','#ef4444','#3b82f6','#14b8a6','#f97316'];
    var canvas = document.createElement('canvas');
    canvas.id='confettiCanvas';
    canvas.style.cssText='position:fixed;inset:0;z-index:99999;pointer-events:none;';
    document.body.appendChild(canvas);
    var ctx = canvas.getContext('2d');
    canvas.width = window.innerWidth;
    canvas.height = window.innerHeight;
    var particles = [];
    for(var i=0; i<80; i++){
        particles.push({
            x: canvas.width/2 + (Math.random()-0.5)*200,
            y: canvas.height/2,
            vx: (Math.random()-0.5)*16,
            vy: -Math.random()*18 - 4,
            w: Math.random()*10+4,
            h: Math.random()*6+3,
            color: colors[Math.floor(Math.random()*colors.length)],
            rot: Math.random()*360,
            rotV: (Math.random()-0.5)*12,
            gravity: 0.35 + Math.random()*0.15,
            opacity: 1,
            delay: Math.random()*600
        });
    }
    var startTime = Date.now();
    function animate(){
        ctx.clearRect(0,0,canvas.width,canvas.height);
        var now = Date.now();
        var alive = false;
        particles.forEach(function(p){
            if(now - startTime < p.delay) { alive=true; return; }
            p.vy += p.gravity;
            p.x += p.vx;
            p.y += p.vy;
            p.rot += p.rotV;
            p.vx *= 0.99;
            if(p.y > canvas.height) p.opacity -= 0.03;
            if(p.opacity <= 0) return;
            alive = true;
            ctx.save();
            ctx.translate(p.x, p.y);
            ctx.rotate(p.rot * Math.PI/180);
            ctx.globalAlpha = Math.max(0, p.opacity);
            ctx.fillStyle = p.color;
            ctx.fillRect(-p.w/2, -p.h/2, p.w, p.h);
            ctx.restore();
        });
        if(alive) requestAnimationFrame(animate);
        else canvas.remove();
    }
    setTimeout(function(){ animate(); }, 1800);
})();
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
