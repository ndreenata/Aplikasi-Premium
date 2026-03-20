<?php
/**
 * PRODUCT_DETAIL.PHP — Full product detail page with real reviews
 * Layout: Icon left + Details right, Variants, DB Reviews, Review Form
 */
require_once __DIR__ . '/../includes/koneksi.php';

$pid = (int)($_GET['id'] ?? 0);
if ($pid <= 0) { header('Location: ' . BASE_URL . '/index.php'); exit; }

// AJAX Quick Buy handler
if (isset($_GET['ajax_add']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
    $exists = false;
    foreach ($_SESSION['cart'] as &$ci) {
        if ($ci['product_id'] == $pid) { $ci['qty']++; $exists = true; break; }
    }
    unset($ci);
    if (!$exists) { $_SESSION['cart'][] = ['product_id' => $pid, 'qty' => 1]; }
    header('Content-Type: application/json');
    echo json_encode(['ok' => true, 'cart_count' => count($_SESSION['cart'])]);
    exit;
}

$stmt = $conn->prepare("SELECT * FROM products WHERE id=? AND is_active=1 LIMIT 1");
$stmt->bind_param("i", $pid);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$product) { header('Location: ' . BASE_URL . '/index.php'); exit; }

$stock = stockCount($conn, $pid);

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
    'ibis'=>['ri-brush-fill','bg-orange-500/10','text-orange-500'],
    'picsart'=>['ri-magic-fill','bg-purple-500/10','text-purple-500'],
    'gamma'=>['ri-slideshow-fill','bg-indigo-500/10','text-indigo-500'],
    'meitu'=>['ri-camera-fill','bg-pink-500/10','text-pink-500'],
    'inshot'=>['ri-film-fill','bg-pink-500/10','text-pink-500'],
    'remini'=>['ri-image-fill','bg-purple-500/10','text-purple-500'],
    'gemini'=>['ri-sparkling-2-fill','bg-blue-500/10','text-blue-500'],
    'apple'=>['ri-apple-fill','bg-gray-500/10','text-gray-600'],
    'tiktok'=>['ri-tiktok-fill','bg-pink-500/10','text-pink-500'],
    'iflix'=>['ri-live-fill','bg-green-500/10','text-green-500'],
    'iqiyi'=>['ri-play-circle-fill','bg-emerald-500/10','text-emerald-500'],
    'mango'=>['ri-tv-fill','bg-orange-500/10','text-orange-500'],
    'youku'=>['ri-play-fill','bg-blue-500/10','text-blue-500'],
    'dramabox'=>['ri-clapperboard-fill','bg-pink-500/10','text-pink-500'],
    'drakor'=>['ri-heart-fill','bg-rose-500/10','text-rose-500'],
    'reelshort'=>['ri-movie-2-fill','bg-violet-500/10','text-violet-500'],
    'shortmax'=>['ri-film-fill','bg-amber-500/10','text-amber-500'],
    'bilibili'=>['ri-bilibili-fill','bg-sky-500/10','text-sky-500'],
    'vision'=>['ri-eye-fill','bg-blue-500/10','text-blue-600'],
    'wps'=>['ri-file-text-fill','bg-orange-500/10','text-orange-500'],
    'getcontact'=>['ri-phone-find-fill','bg-green-500/10','text-green-500'],
    'camscanner'=>['ri-scan-fill','bg-purple-500/10','text-purple-500'],
    'duolingo'=>['ri-translate-2','bg-green-500/10','text-green-500'],
    'fizzo'=>['ri-book-fill','bg-amber-500/10','text-amber-500'],
    'nordvpn'=>['ri-shield-check-fill','bg-blue-500/10','text-blue-500'],
    'expressvpn'=>['ri-shield-fill','bg-red-500/10','text-red-500'],
    'surfshark'=>['ri-shield-star-fill','bg-teal-500/10','text-teal-500'],
    'wibuku'=>['ri-book-open-fill','bg-purple-500/10','text-purple-500'],
    'wattpad'=>['ri-quill-pen-fill','bg-orange-500/10','text-orange-500'],
    'serial'=>['ri-play-list-fill','bg-blue-500/10','text-blue-500'],
];
$k = strtolower(explode(' ', $product['name'])[0]);
$ic = $iconMap[$k] ?? ['ri-apps-fill','bg-gray-500/10','text-gray-400'];

// Image file map
$imgMap = [
    'netflix'=>'netflix.png','disney'=>'disney.png','viu'=>'viu.png','wetv'=>'wetv.png',
    'prime'=>'prime.png','hbo'=>'hbo.png','iflix'=>'iflix.png','iqiyi'=>'iqiyi.png',
    'mango'=>'mango.png','youku'=>'youku.png','dramabox'=>'dramabox.png','drakor'=>'drakor.png',
    'youtube'=>'youtube.png','reelshort'=>'reelshort.png','shortmax'=>'shortmax.png',
    'bilibili'=>'bilibili.png','vision'=>'vision.png',
    'spotify'=>'spotify.png','apple'=>'apple.png','tiktok'=>'tiktok.png',
    'canva'=>'canva.png','ibis'=>'ibis.png','picsart'=>'picsart.png','gamma'=>'gamma.png','meitu'=>'meitu.png',
    'capcut'=>'capcut.png','inshot'=>'inshot.png','lightroom'=>'lightroom.png','remini'=>'remini.png',
    'vsco'=>'vsco.png','gemini'=>'gemini.png','alight'=>'alight.png',
    'chatgpt'=>'chatgpt.png','zoom'=>'zoom.png','google'=>'google.png','microsoft'=>'microsoft.png',
    'wps'=>'wps.png','getcontact'=>'getcontact.png','camscanner'=>'camscanner.png',
    'duolingo'=>'duolingo.png','fizzo'=>'fizzo.png',
    'nordvpn'=>'nordvpn.png','expressvpn'=>'expressvpn.png','surfshark'=>'surfshark.png',
    'wibuku'=>'wibuku.png','wattpad'=>'wattpad.png','serial'=>'serial.png',
];
$imgFile = $imgMap[$k] ?? null;
$imgPath = $imgFile ? __DIR__ . '/../images/products/' . $imgFile : null;

function origPrice2($p) { return round($p * 5 / 1000) * 1000; }
function hematPct2($p) { return round((1 - ($p / origPrice2($p))) * 100); }

$orig = origPrice2($product['price']);
$hemat = hematPct2($product['price']);

// Best sellers / promo tags
$bestSellers = ['netflix','spotify','canva'];
$promoTags = ['youtube','chatgpt'];
$tag = '';
if (in_array($k, $bestSellers)) $tag = 'TERLARIS';
elseif (in_array($k, $promoTags)) $tag = 'PROMO';

// Related products
$relatedStmt = $conn->prepare("SELECT * FROM products WHERE category=? AND id!=? AND is_active=1 ORDER BY RAND() LIMIT 4");
$relatedStmt->bind_param("si", $product['category'], $pid);
$relatedStmt->execute();
$related = $relatedStmt->get_result();
$relatedStmt->close();

// ═══ REAL REVIEWS FROM DATABASE ═══
$reviewStmt = $conn->prepare("SELECT r.*, u.name as user_name FROM reviews r JOIN users u ON r.user_id=u.id WHERE r.product_id=? ORDER BY r.created_at DESC LIMIT 20");
$reviewStmt->bind_param("i", $pid);
$reviewStmt->execute();
$dbReviews = $reviewStmt->get_result();
$reviewStmt->close();

// Avg rating
$avgStmt = $conn->prepare("SELECT AVG(rating) as avg_rating, COUNT(*) as total_reviews FROM reviews WHERE product_id=?");
$avgStmt->bind_param("i", $pid);
$avgStmt->execute();
$avgData = $avgStmt->get_result()->fetch_assoc();
$avgStmt->close();
$avgRating = round($avgData['avg_rating'] ?? 0, 1);
$totalReviews = (int)($avgData['total_reviews'] ?? 0);

// Check if current user can review (has SUCCESS transaction for this product, hasn't reviewed yet)
$canReview = false;
$userTransactionId = null;
if (isLoggedIn()) {
    $uid = (int)$_SESSION['user_id'];
    $trxCheck = $conn->prepare("SELECT t.id FROM transactions t LEFT JOIN reviews rv ON rv.transaction_id=t.id AND rv.user_id=? WHERE t.user_id=? AND t.product_id=? AND t.status='SUCCESS' AND rv.id IS NULL LIMIT 1");
    $trxCheck->bind_param("iii", $uid, $uid, $pid);
    $trxCheck->execute();
    $trxResult = $trxCheck->get_result()->fetch_assoc();
    $trxCheck->close();
    if ($trxResult) {
        $canReview = true;
        $userTransactionId = $trxResult['id'];
    }
}

// View count (simulated)
$views = rand(8000, 35000);
$sold = rand(15000, 45000);

$catLabel = str_replace('_', ' & ', $product['category']);

$pageTitle = $product['name'] . ' — ' . SITE_NAME;
include __DIR__ . '/../includes/header.php';
?>

<style>
    .tab-btn { transition: all 0.2s; }
    .tab-btn.active { background: var(--accent); color: white; }
    .variant-card { transition: all 0.3s cubic-bezier(0.4,0,0.2,1); cursor: pointer; }
    .variant-card:hover { border-color: var(--accent) !important; }
    .variant-card.selected { border-color: var(--accent) !important; box-shadow: 0 0 0 3px var(--accent-glow); }
    .desc-fade { position:relative; max-height: 80px; overflow: hidden; transition: max-height 0.4s ease; }
    .desc-fade.expanded { max-height: 600px; }
    .desc-fade::after { content:''; position:absolute; bottom:0; left:0; right:0; height: 40px; background: linear-gradient(transparent, var(--bg)); pointer-events:none; transition: opacity 0.3s; }
    .desc-fade.expanded::after { opacity: 0; }
    @keyframes cartBounce { 0%,100%{transform:scale(1)} 50%{transform:scale(1.25)} }
    .star-btn { cursor:pointer; transition: transform 0.15s, color 0.15s; }
    .star-btn:hover { transform: scale(1.2); }
    .star-btn.active { color: #F59E0B; }
</style>

<!-- ═══ PRODUCT DETAIL ═══ -->
<section class="py-10" style="background:var(--bg)">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Breadcrumb -->
        <nav class="flex items-center gap-2 text-xs mb-6" style="color:var(--muted)" data-aos="fade-right">
            <a href="<?= BASE_URL ?>/index.php" class="hover:text-green-500 transition"><i class="ri-home-5-line mr-0.5"></i>Home</a>
            <i class="ri-arrow-right-s-line"></i>
            <a href="<?= BASE_URL ?>/index.php#products" class="hover:text-green-500 transition">Produk</a>
            <i class="ri-arrow-right-s-line"></i>
            <span style="color:var(--text)"><?= htmlspecialchars($product['name']) ?></span>
        </nav>

        <!-- Main Layout: Icon + Details -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-8" data-aos="fade-up">

            <!-- LEFT: Product Icon (2 cols) -->
            <div class="md:col-span-2">
                <div class="glass-strong rounded-3xl p-8 sm:p-12 flex items-center justify-center aspect-square relative">
                    <?php if ($imgFile && file_exists($imgPath)): ?>
                    <img src="<?= BASE_URL ?>/images/products/<?= $imgFile ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="w-28 h-28 sm:w-36 sm:h-36 rounded-3xl object-contain shadow-lg" style="background:white;padding:8px">
                    <?php else: ?>
                    <div class="w-28 h-28 sm:w-36 sm:h-36 rounded-3xl <?= $ic[1] ?> flex items-center justify-center">
                        <i class="<?= $ic[0] ?> text-6xl sm:text-7xl <?= $ic[2] ?>"></i>
                    </div>
                    <?php endif; ?>
                    <?php if ($tag): ?>
                    <span class="absolute top-4 left-4 px-3 py-1.5 rounded-xl text-[10px] font-black text-white shadow-sm <?= $tag==='TERLARIS'?'badge-shimmer':'bg-orange-500' ?>"><?= $tag ?></span>
                    <?php endif; ?>
                </div>

                <!-- Avg Rating Card below icon -->
                <?php if ($totalReviews > 0): ?>
                <div class="glass-strong rounded-2xl p-4 mt-3 text-center" data-aos="fade-up">
                    <div class="flex items-center justify-center gap-1 mb-1">
                        <?php for ($s = 1; $s <= 5; $s++): ?>
                        <i class="ri-star-fill text-lg <?= $s <= round($avgRating) ? 'text-amber-400' : '' ?>" style="<?= $s > round($avgRating) ? 'color:var(--border)' : '' ?>"></i>
                        <?php endfor; ?>
                    </div>
                    <p class="text-lg font-black" style="color:var(--text)"><?= $avgRating ?><span class="text-xs font-normal" style="color:var(--muted)"> / 5</span></p>
                    <p class="text-[10px]" style="color:var(--muted)"><?= $totalReviews ?> ulasan</p>
                </div>
                <?php endif; ?>
            </div>

            <!-- RIGHT: Details (3 cols) -->
            <div class="md:col-span-3">
                <!-- Category + Badges -->
                <div class="flex flex-wrap items-center gap-2 mb-2">
                    <span class="px-3 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider glass" style="color:var(--text2)"><?= htmlspecialchars(ucwords($catLabel)) ?></span>
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[10px] font-bold glass" style="color:var(--muted)"><i class="ri-eye-line"></i><?= number_format($views) ?></span>
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[10px] font-bold glass text-green-500"><i class="ri-download-2-line"></i><?= number_format($sold) ?></span>
                    <?php if ($totalReviews > 0): ?>
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[10px] font-bold glass text-amber-400"><i class="ri-star-fill"></i><?= $avgRating ?> (<?= $totalReviews ?>)</span>
                    <?php endif; ?>
                    <button onclick="shareProduct()" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[10px] font-bold glass hover:border-green-500 transition btn-press" style="color:var(--muted)"><i class="ri-share-line"></i>Share</button>
                </div>

                <!-- Name -->
                <h1 class="text-2xl sm:text-3xl font-black mb-3" style="color:var(--text)"><?= htmlspecialchars($product['name']) ?></h1>

                <!-- Description -->
                <div class="mb-4">
                    <div class="desc-fade text-sm leading-relaxed" style="color:var(--text2)" id="descBox">
                        <?= $product['description'] ? nl2br(htmlspecialchars($product['description'])) : 'Akun premium berkualitas dengan harga terjangkau. Garansi full replace selama 30 hari. Auto-delivery via WhatsApp dalam waktu kurang dari 1 menit setelah pembayaran.' ?>
                    </div>
                    <button onclick="toggleDesc()" class="text-green-500 text-xs font-bold mt-1 hover:underline" id="descToggle">Lihat selengkapnya...</button>
                </div>

                <!-- Login notice -->
                <?php if (!isLoggedIn()): ?>
                <div class="rounded-xl p-3 mb-4 text-[11px] flex items-start gap-2" style="background:rgba(251,191,36,0.08);border:1px solid rgba(251,191,36,0.15);color:var(--text2)">
                    <i class="ri-error-warning-fill text-amber-400 mt-0.5"></i>
                    <span>Kamu belum login, jumlah pembelian akan dibatasi. Silahkan <a href="<?= BASE_URL ?>/auth/login.php" class="text-green-500 font-bold hover:underline">Login</a> terlebih dahulu.</span>
                </div>
                <?php endif; ?>

                <!-- Tabs -->
                <div class="flex items-center gap-2 mb-4">
                    <button class="tab-btn px-5 py-2.5 rounded-xl text-xs font-bold active" onclick="switchTab('variants',this)"><i class="ri-sparkling-fill mr-1"></i>Variants</button>
                    <button class="tab-btn px-5 py-2.5 rounded-xl text-xs font-bold glass" style="color:var(--text2)" onclick="switchTab('reviews',this)"><i class="ri-star-fill mr-1"></i>Reviews <?php if ($totalReviews > 0): ?><span class="ml-1 px-1.5 py-0.5 rounded text-[9px] bg-amber-400/15 text-amber-400"><?= $totalReviews ?></span><?php endif; ?></button>
                </div>

                <!-- Tab: Variants -->
                <div id="tabVariants">
                    <p class="text-xs font-bold mb-3" style="color:var(--muted)">Choose your needs</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-5">
                        <div class="variant-card glass-strong rounded-2xl p-4 selected" data-variant="main">
                            <p class="text-sm font-bold mb-1" style="color:var(--text)"><?= htmlspecialchars($product['name']) ?></p>
                            <div class="flex items-center gap-2 mb-2">
                                <?php if ($stock > 0): ?>
                                <span class="inline-flex items-center gap-1 text-[10px] font-bold text-green-500"><i class="ri-flashlight-fill"></i>TERSEDIA <?= $stock ?></span>
                                <?php else: ?>
                                <span class="inline-flex items-center gap-1 text-[10px] font-bold text-red-400"><i class="ri-close-circle-fill"></i>HABIS</span>
                                <?php endif; ?>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-base font-black text-green-500"><?= rupiah($product['price']) ?></span>
                                <span class="text-[10px] line-through" style="color:var(--muted)"><?= rupiah($orig) ?></span>
                                <span class="px-1.5 py-0.5 rounded text-[9px] font-black bg-red-500/10 text-red-400"><i class="ri-arrow-down-s-fill"></i><?= $hemat ?>%</span>
                            </div>
                        </div>
                    </div>

                    <!-- CTA Buttons -->
                    <?php if ($stock > 0): ?>
                    <div class="flex gap-3">
                        <button onclick="addToCart(<?= $product['id'] ?>)" class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-4 glass text-sm font-bold rounded-2xl hover:border-green-500 btn-press transition-all" style="color:var(--text)" id="addCartBtn">
                            <i class="ri-shopping-cart-2-line text-green-500"></i> Tambah ke Keranjang
                        </button>
                        <a href="<?= BASE_URL ?>/store/cart.php" class="inline-flex items-center justify-center gap-2 px-6 py-4 bg-green-600 text-white text-sm font-bold rounded-2xl hover:bg-green-500 btn-press shadow-lg shadow-green-600/25 pulse-glow" id="buyNowBtn" onclick="addToCartDirect(<?= $product['id'] ?>)">
                            <i class="ri-flashlight-fill"></i> Beli Sekarang
                        </a>
                    </div>
                    <?php else: ?>
                    <button disabled class="w-full inline-flex items-center justify-center gap-2 px-4 py-4 rounded-2xl text-sm font-semibold cursor-not-allowed" style="background:var(--surface);color:var(--muted)"><i class="ri-close-circle-line"></i> Stok Habis</button>
                    <?php endif; ?>

                    <!-- Stock Indicator -->
                    <?php if ($stock > 0 && $stock <= 5): ?>
                    <div class="flex items-center gap-2 px-3 py-2 rounded-xl bg-amber-500/10 border border-amber-500/20">
                        <i class="ri-fire-fill text-amber-500 animate-pulse"></i>
                        <span class="text-xs font-bold text-amber-500">Sisa <?= $stock ?> akun — hampir habis!</span>
                    </div>
                    <?php elseif ($stock > 5): ?>
                    <div class="flex items-center gap-2 px-3 py-2 rounded-xl" style="background:var(--surface)">
                        <i class="ri-check-double-fill text-green-500"></i>
                        <span class="text-xs font-semibold" style="color:var(--muted)">Stok tersedia: <?= $stock ?> akun</span>
                    </div>
                    <?php endif; ?>

                    <!-- Social Sharing -->
                    <div class="flex items-center gap-2 pt-2">
                        <span class="text-[10px] font-semibold" style="color:var(--muted)">Share:</span>
                        <a href="https://wa.me/?text=<?= urlencode($product['name'] . ' — ' . rupiah($product['price']) . ' ✨ Cek di ' . BASE_URL . '/store/product_detail.php?id=' . $product['id']) ?>" target="_blank" class="w-8 h-8 rounded-lg bg-green-500/10 flex items-center justify-center hover:bg-green-500/20 transition" title="Share via WhatsApp"><i class="ri-whatsapp-fill text-[#25D366] text-sm"></i></a>
                        <a href="https://twitter.com/intent/tweet?text=<?= urlencode($product['name'] . ' — ' . rupiah($product['price']) . ' di ' . SITE_NAME) ?>&url=<?= urlencode(BASE_URL . '/store/product_detail.php?id=' . $product['id']) ?>" target="_blank" class="w-8 h-8 rounded-lg bg-blue-500/10 flex items-center justify-center hover:bg-blue-500/20 transition" title="Share to Twitter"><i class="ri-twitter-x-fill text-blue-400 text-sm"></i></a>
                        <button onclick="navigator.clipboard.writeText(window.location.href).then(()=>{this.innerHTML='<i class=\'ri-check-fill text-green-500 text-sm\'></i>';setTimeout(()=>{this.innerHTML='<i class=\'ri-link text-sm\' style=\'color:var(--muted)\'></i>';},2000)})" class="w-8 h-8 rounded-lg flex items-center justify-center hover:bg-green-500/10 transition" style="background:var(--surface)" title="Copy Link"><i class="ri-link text-sm" style="color:var(--muted)"></i></button>
                    </div>
                </div>

                <!-- Tab: Reviews (REAL from DB) -->
                <div id="tabReviews" class="hidden">
                    <?php if ($canReview): ?>
                    <!-- Review Form -->
                    <div class="glass-strong rounded-2xl p-5 mb-4">
                        <h4 class="text-xs font-bold mb-3" style="color:var(--text)"><i class="ri-edit-2-fill text-green-500 mr-1"></i>Tulis Ulasan</h4>
                        <form id="reviewForm" onsubmit="submitReview(event)">
                            <input type="hidden" id="rvProductId" value="<?= $pid ?>">
                            <input type="hidden" id="rvTransactionId" value="<?= $userTransactionId ?>">
                            <div class="flex items-center gap-1 mb-3" id="starRating">
                                <?php for ($s = 1; $s <= 5; $s++): ?>
                                <i class="ri-star-fill text-xl star-btn active" data-star="<?= $s ?>" onclick="setRating(<?= $s ?>)"></i>
                                <?php endfor; ?>
                                <span class="text-[10px] ml-2 font-bold" style="color:var(--muted)" id="ratingLabel">5/5</span>
                            </div>
                            <input type="hidden" id="rvRating" value="5">
                            <textarea id="rvText" class="w-full px-4 py-3 rounded-xl text-xs" style="background:var(--surface);border:1px solid var(--border);color:var(--text);resize:vertical" rows="3" placeholder="Ceritakan pengalamanmu menggunakan produk ini..." required></textarea>
                            <button type="submit" class="mt-3 inline-flex items-center gap-1.5 px-5 py-2.5 bg-green-600 text-white text-xs font-bold rounded-xl hover:bg-green-500 btn-press shadow-lg shadow-green-600/20" id="submitReviewBtn">
                                <i class="ri-send-plane-fill"></i> Kirim Ulasan
                            </button>
                        </form>
                    </div>
                    <?php elseif (!isLoggedIn()): ?>
                    <div class="glass rounded-2xl p-4 mb-4 text-center">
                        <p class="text-xs" style="color:var(--muted)"><a href="<?= BASE_URL ?>/auth/login.php" class="text-green-500 font-bold hover:underline">Login</a> dan beli produk ini untuk menulis ulasan</p>
                    </div>
                    <?php endif; ?>

                    <!-- Reviews List -->
                    <?php if ($dbReviews->num_rows > 0): ?>
                    <div class="space-y-3">
                        <?php while ($r = $dbReviews->fetch_assoc()): ?>
                        <div class="glass rounded-2xl p-4">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-xl bg-green-500/10 flex items-center justify-center text-green-500 text-[10px] font-bold"><?= strtoupper(substr($r['user_name'], 0, 1)) ?></div>
                                    <div>
                                        <p class="text-xs font-bold" style="color:var(--text)"><?= htmlspecialchars($r['user_name']) ?></p>
                                        <p class="text-[9px]" style="color:var(--muted)"><?= date('d M Y', strtotime($r['created_at'])) ?></p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-0.5">
                                    <?php for ($s = 1; $s <= 5; $s++): ?>
                                    <i class="ri-star-fill text-xs <?= $s <= $r['rating'] ? 'text-amber-400' : '' ?>" style="<?= $s > $r['rating'] ? 'color:var(--border)' : '' ?>"></i>
                                    <?php endfor; ?>
                                </div>
                            </div>
                            <p class="text-xs leading-relaxed" style="color:var(--text2)">"<?= htmlspecialchars($r['review_text']) ?>"</p>
                        </div>
                        <?php endwhile; ?>
                    </div>
                    <?php else: ?>
                    <div class="text-center py-8">
                        <div class="w-14 h-14 mx-auto mb-3 rounded-2xl bg-amber-500/10 flex items-center justify-center"><i class="ri-star-line text-2xl text-amber-400"></i></div>
                        <p class="text-xs font-semibold mb-1" style="color:var(--text2)">Belum ada ulasan</p>
                        <p class="text-[10px]" style="color:var(--muted)">Jadilah yang pertama memberikan ulasan!</p>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Features grid -->
                <div class="grid grid-cols-2 gap-2.5 mt-6">
                    <div class="rounded-2xl p-3.5 flex items-center gap-3" style="background:var(--surface)"><div class="w-9 h-9 shrink-0 rounded-xl bg-green-500/10 flex items-center justify-center"><i class="ri-timer-flash-fill text-green-500"></i></div><div><p class="text-[10px] font-bold" style="color:var(--text)">Instan</p><p class="text-[9px]" style="color:var(--muted)">Auto via WA</p></div></div>
                    <div class="rounded-2xl p-3.5 flex items-center gap-3" style="background:var(--surface)"><div class="w-9 h-9 shrink-0 rounded-xl bg-green-500/10 flex items-center justify-center"><i class="ri-shield-check-fill text-green-500"></i></div><div><p class="text-[10px] font-bold" style="color:var(--text)">Garansi 30H</p><p class="text-[9px]" style="color:var(--muted)">Full replace</p></div></div>
                    <div class="rounded-2xl p-3.5 flex items-center gap-3" style="background:var(--surface)"><div class="w-9 h-9 shrink-0 rounded-xl bg-green-500/10 flex items-center justify-center"><i class="ri-lock-fill text-green-500"></i></div><div><p class="text-[10px] font-bold" style="color:var(--text)">Private</p><p class="text-[9px]" style="color:var(--muted)">Akun khusus</p></div></div>
                    <div class="rounded-2xl p-3.5 flex items-center gap-3" style="background:var(--surface)"><div class="w-9 h-9 shrink-0 rounded-xl bg-green-500/10 flex items-center justify-center"><i class="ri-shield-star-fill text-green-500"></i></div><div><p class="text-[10px] font-bold" style="color:var(--text)">Terdaftar</p><p class="text-[9px]" style="color:var(--muted)">Komdigi</p></div></div>
                </div>
            </div>
        </div>
    </div>
</section>


<!-- ═══ PRODUK LAINNYA ═══ -->
<?php if ($related->num_rows > 0): ?>
<section class="py-10" style="background:var(--bg)">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-lg font-black mb-5 flex items-center gap-2" style="color:var(--text)" data-aos="fade-right">
            <span class="text-green-500">PRODUK</span> LAINNYA
        </h2>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <?php while ($rp = $related->fetch_assoc()):
                $rk = strtolower(explode(' ', $rp['name'])[0]);
                $ric = $iconMap[$rk] ?? ['ri-apps-fill','bg-gray-500/10','text-gray-400'];
                $rCatLabel = str_replace('_',' & ',$rp['category']);
            ?>
            <a href="product_detail.php?id=<?= $rp['id'] ?>" class="glass-strong rounded-2xl overflow-hidden hover-lift group" data-aos="fade-up">
                <div class="h-28 flex items-center justify-center <?= $ric[1] ?>">
                    <div class="w-14 h-14 rounded-2xl glass flex items-center justify-center">
                        <i class="<?= $ric[0] ?> text-2xl <?= $ric[2] ?> group-hover:scale-110 transition-transform duration-300"></i>
                    </div>
                </div>
                <div class="p-3">
                    <p class="text-[9px] font-bold uppercase tracking-wider mb-0.5" style="color:var(--muted)"><?= htmlspecialchars(ucwords($rCatLabel)) ?></p>
                    <h3 class="text-xs font-bold group-hover:text-green-500 transition-colors truncate" style="color:var(--text)"><?= htmlspecialchars($rp['name']) ?></h3>
                </div>
            </a>
            <?php endwhile; ?>
        </div>
    </div>
</section>
<?php endif; ?>


<script>
// Star rating
var currentRating = 5;
function setRating(n) {
    currentRating = n;
    document.getElementById('rvRating').value = n;
    document.getElementById('ratingLabel').textContent = n + '/5';
    document.querySelectorAll('#starRating .star-btn').forEach(function(s) {
        var v = parseInt(s.dataset.star);
        s.classList.toggle('active', v <= n);
        s.style.color = v <= n ? '#F59E0B' : 'var(--border)';
    });
}

// Submit review
function submitReview(e) {
    e.preventDefault();
    var btn = document.getElementById('submitReviewBtn');
    btn.innerHTML = '<i class="ri-loader-4-line animate-spin"></i> Mengirim...';
    btn.disabled = true;

    var fd = new FormData();
    fd.append('action', 'review_save');
    fd.append('product_id', document.getElementById('rvProductId').value);
    fd.append('transaction_id', document.getElementById('rvTransactionId').value);
    fd.append('rating', document.getElementById('rvRating').value);
    fd.append('review_text', document.getElementById('rvText').value);

    fetch('<?= BASE_URL ?>/admin/api.php', { method: 'POST', body: fd })
    .then(function(r) { return r.json(); })
    .then(function(d) {
        if (d.ok) {
            btn.innerHTML = '<i class="ri-check-line"></i> Terkirim!';
            setTimeout(function(){ location.reload(); }, 1000);
        } else {
            alert(d.msg);
            btn.innerHTML = '<i class="ri-send-plane-fill"></i> Kirim Ulasan';
            btn.disabled = false;
        }
    })
    .catch(function() {
        btn.innerHTML = '<i class="ri-send-plane-fill"></i> Kirim Ulasan';
        btn.disabled = false;
    });
}

// Description toggle
function toggleDesc() {
    var box = document.getElementById('descBox');
    var btn = document.getElementById('descToggle');
    box.classList.toggle('expanded');
    btn.textContent = box.classList.contains('expanded') ? 'Sembunyikan' : 'Lihat selengkapnya...';
}

// Tab switching
function switchTab(tab, btn) {
    document.querySelectorAll('.tab-btn').forEach(b => { b.classList.remove('active'); b.classList.add('glass'); b.style.color = 'var(--text2)'; });
    btn.classList.add('active'); btn.classList.remove('glass'); btn.style.color = '';
    document.getElementById('tabVariants').classList.toggle('hidden', tab !== 'variants');
    document.getElementById('tabReviews').classList.toggle('hidden', tab !== 'reviews');
}

// Add to cart via AJAX
function addToCart(pid) {
    var btn = document.getElementById('addCartBtn');
    btn.innerHTML = '<i class="ri-loader-4-line animate-spin"></i> Menambahkan...';
    btn.disabled = true;

    fetch('<?= BASE_URL ?>/store/cart_api.php', {
        method: 'POST',
        headers: {'Content-Type':'application/x-www-form-urlencoded'},
        body: 'action=add&product_id=' + pid
    })
    .then(r => r.json())
    .then(data => {
        if (data.ok) {
            btn.innerHTML = '<i class="ri-check-line text-green-500"></i> Ditambahkan!';
            btn.style.borderColor = 'var(--accent)';
            var badge = document.getElementById('cartBadge');
            if (badge) { badge.textContent = data.count; badge.classList.remove('hidden'); badge.style.animation = 'cartBounce 0.4s'; }
            setTimeout(() => {
                btn.innerHTML = '<i class="ri-shopping-cart-2-line text-green-500"></i> Sudah di Keranjang';
                btn.disabled = true;
            }, 1500);
        } else {
            btn.innerHTML = '<i class="ri-information-line text-amber-400"></i> ' + data.msg;
            setTimeout(() => {
                btn.innerHTML = '<i class="ri-shopping-cart-2-line text-green-500"></i> Tambah ke Keranjang';
                btn.disabled = false;
            }, 2000);
        }
    })
    .catch(() => {
        btn.innerHTML = '<i class="ri-shopping-cart-2-line text-green-500"></i> Tambah ke Keranjang';
        btn.disabled = false;
    });
}

function addToCartDirect(pid) {
    fetch('<?= BASE_URL ?>/store/cart_api.php', {
        method: 'POST',
        headers: {'Content-Type':'application/x-www-form-urlencoded'},
        body: 'action=add&product_id=' + pid
    });
}

function shareProduct() {
    var url = window.location.href;
    if (navigator.share) {
        navigator.share({ title: '<?= addslashes($product['name']) ?> — <?= SITE_NAME ?>', url: url });
    } else {
        navigator.clipboard.writeText(url).then(() => alert('Link berhasil disalin!'));
    }
}

// Track recently viewed product
(function(){
    try {
        var rv = JSON.parse(localStorage.getItem('recentlyViewed')) || [];
        var item = { id: <?= $pid ?>, name: '<?= addslashes($product['name']) ?>', price: <?= $product['price'] ?>, category: '<?= addslashes($product['category']) ?>' };
        rv = rv.filter(function(x){ return x.id !== item.id; });
        rv.unshift(item);
        if (rv.length > 8) rv = rv.slice(0, 8);
        localStorage.setItem('recentlyViewed', JSON.stringify(rv));
    } catch(e){}
})();
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
