<?php
/**
 * INDEX.PHP — Homepage Natsy Premiums v2
 * Dark/Light · Glassmorphism · Trust · Marketing · Gen Z
 */
require_once __DIR__ . '/includes/koneksi.php';

$products = $conn->query("SELECT * FROM products WHERE is_active = 1 ORDER BY id ASC");
$articles = $conn->query("SELECT * FROM articles ORDER BY created_at DESC LIMIT 3");

$flash = null;
if (isset($_SESSION['flash'])) { $flash = $_SESSION['flash']; unset($_SESSION['flash']); }

// Icon map with consistent rounded frames
$iconMap = [
    // Streaming
    'netflix'=>['ri-movie-2-fill','bg-red-500/10','text-red-500'],
    'disney'=>['ri-movie-fill','bg-blue-500/10','text-blue-600'],
    'viu'=>['ri-film-fill','bg-pink-500/10','text-pink-500'],
    'wetv'=>['ri-tv-2-fill','bg-indigo-500/10','text-indigo-500'],
    'prime'=>['ri-video-fill','bg-sky-500/10','text-sky-600'],
    'hbo'=>['ri-film-fill','bg-purple-500/10','text-purple-600'],
    'iflix'=>['ri-live-fill','bg-green-500/10','text-green-500'],
    'iqiyi'=>['ri-play-circle-fill','bg-emerald-500/10','text-emerald-500'],
    'mango'=>['ri-tv-fill','bg-orange-500/10','text-orange-500'],
    'youku'=>['ri-play-fill','bg-blue-500/10','text-blue-500'],
    'dramabox'=>['ri-clapperboard-fill','bg-pink-500/10','text-pink-500'],
    'drakor'=>['ri-heart-fill','bg-rose-500/10','text-rose-500'],
    // Video
    'youtube'=>['ri-youtube-fill','bg-red-500/10','text-red-600'],
    'reelshort'=>['ri-movie-2-fill','bg-violet-500/10','text-violet-500'],
    'shortmax'=>['ri-film-fill','bg-amber-500/10','text-amber-500'],
    'bilibili'=>['ri-bilibili-fill','bg-sky-500/10','text-sky-500'],
    'vision'=>['ri-eye-fill','bg-blue-500/10','text-blue-600'],
    // Music
    'spotify'=>['ri-spotify-fill','bg-green-500/10','text-green-500'],
    'apple'=>['ri-apple-fill','bg-gray-500/10','text-gray-600'],
    'tiktok'=>['ri-tiktok-fill','bg-pink-500/10','text-pink-500'],
    // Design
    'canva'=>['ri-palette-fill','bg-teal-500/10','text-teal-500'],
    'ibis'=>['ri-brush-fill','bg-orange-500/10','text-orange-500'],
    'picsart'=>['ri-magic-fill','bg-purple-500/10','text-purple-500'],
    'gamma'=>['ri-slideshow-fill','bg-indigo-500/10','text-indigo-500'],
    'meitu'=>['ri-camera-fill','bg-pink-500/10','text-pink-500'],
    // Editing
    'capcut'=>['ri-scissors-fill','bg-violet-500/10','text-violet-500'],
    'inshot'=>['ri-film-fill','bg-pink-500/10','text-pink-500'],
    'lightroom'=>['ri-contrast-2-fill','bg-blue-500/10','text-blue-500'],
    'remini'=>['ri-image-fill','bg-purple-500/10','text-purple-500'],
    'vsco'=>['ri-camera-lens-fill','bg-gray-500/10','text-gray-500'],
    'gemini'=>['ri-sparkling-2-fill','bg-blue-500/10','text-blue-500'],
    // Productivity
    'chatgpt'=>['ri-openai-fill','bg-emerald-500/10','text-emerald-600'],
    'zoom'=>['ri-video-chat-fill','bg-blue-500/10','text-blue-500'],
    'google'=>['ri-google-fill','bg-amber-500/10','text-amber-500'],
    'microsoft'=>['ri-microsoft-fill','bg-blue-500/10','text-blue-600'],
    'wps'=>['ri-file-text-fill','bg-orange-500/10','text-orange-500'],
    'getcontact'=>['ri-phone-find-fill','bg-green-500/10','text-green-500'],
    'camscanner'=>['ri-scan-fill','bg-purple-500/10','text-purple-500'],
    // Learning
    'duolingo'=>['ri-translate-2','bg-green-500/10','text-green-500'],
    'fizzo'=>['ri-book-fill','bg-amber-500/10','text-amber-500'],
    // Security
    'nordvpn'=>['ri-shield-check-fill','bg-blue-500/10','text-blue-500'],
    'expressvpn'=>['ri-shield-fill','bg-red-500/10','text-red-500'],
    'surfshark'=>['ri-shield-star-fill','bg-teal-500/10','text-teal-500'],
    // Otaku
    'wibuku'=>['ri-book-open-fill','bg-purple-500/10','text-purple-500'],
    'wattpad'=>['ri-quill-pen-fill','bg-orange-500/10','text-orange-500'],
    'serial'=>['ri-play-list-fill','bg-blue-500/10','text-blue-500'],
];
function icon($n){ global $iconMap; $k=strtolower(explode(' ',$n)[0]); return $iconMap[$k]??['ri-apps-fill','bg-gray-500/10 dark:bg-gray-500/20','text-gray-400']; }

// Image file map — maps product name keyword to image filename
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
function imgFile($n){ global $imgMap; $k=strtolower(explode(' ',$n)[0]); return $imgMap[$k] ?? null; }

// Best sellers / promo tags
$bestSellers = ['netflix','spotify','canva'];
$promoTags = ['youtube','chatgpt'];
function getTag($name){ global $bestSellers,$promoTags; $k=strtolower(explode(' ',$name)[0]); if(in_array($k,$bestSellers))return 'TERLARIS'; if(in_array($k,$promoTags))return 'PROMO'; return ''; }

// Original prices (5x markup for "hemat" display)
function origPrice($p){ return round($p * 5 / 1000) * 1000; }
function hematPct($p){ return round((1-($p/origPrice($p)))*100); }

$pageTitle = SITE_NAME . ' — Upgrade Gaya Hidupmu Sekarang';
include __DIR__ . '/includes/header.php';
?>

<?php if ($flash): ?>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-3" data-aos="fade-down">
    <div class="flex items-center gap-2 px-4 py-3 rounded-2xl text-sm glass <?= $flash['type']==='success' ? 'text-green-500' : 'text-red-400' ?>">
        <i class="<?= $flash['type']==='success'?'ri-check-line':'ri-error-warning-line' ?>"></i>
        <?= htmlspecialchars($flash['msg']) ?>
    </div>
</div>
<?php endif; ?>


<!-- ═══ PREMIUM HERO SECTION ═══ -->
<section class="relative overflow-hidden" style="min-height:520px">
    <!-- Hero Mesh Gradient Background (Enhanced) -->
    <div class="absolute inset-0 pointer-events-none" style="z-index:0">
        <div class="absolute top-[-20%] left-[-10%] w-[600px] h-[600px] rounded-full" style="background:radial-gradient(circle,rgba(34,197,94,0.14) 0%,transparent 70%);filter:blur(60px)"></div>
        <div class="absolute bottom-[-15%] right-[-5%] w-[500px] h-[500px] rounded-full" style="background:radial-gradient(circle,rgba(6,182,212,0.12) 0%,transparent 70%);filter:blur(50px)"></div>
        <div class="absolute top-[30%] right-[20%] w-[400px] h-[400px] rounded-full" style="background:radial-gradient(circle,rgba(167,139,250,0.10) 0%,transparent 70%);filter:blur(45px)"></div>
        <div class="absolute bottom-[10%] left-[30%] w-[350px] h-[350px] rounded-full" style="background:radial-gradient(circle,rgba(251,191,36,0.08) 0%,transparent 70%);filter:blur(40px)"></div>
        <div class="absolute top-[10%] right-[50%] w-[300px] h-[300px] rounded-full" style="background:radial-gradient(circle,rgba(236,72,153,0.06) 0%,transparent 70%);filter:blur(50px)"></div>
        <!-- Grid texture -->
        <div class="absolute inset-0" style="background-image:linear-gradient(var(--border) 1px,transparent 1px),linear-gradient(90deg,var(--border) 1px,transparent 1px);background-size:48px 48px;opacity:0.04"></div>
    </div>

    <div class="relative max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-20 lg:py-24" style="z-index:1">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-16 items-center">

            <!-- LEFT: Text Column -->
            <div class="text-center lg:text-left">
                <!-- Badge -->
                <div class="hero-stagger" style="animation-delay:0.1s">
                    <span class="inline-flex items-center gap-2 px-4 py-2.5 rounded-full mb-5" style="background:var(--card);border:1px solid var(--card-border);backdrop-filter:blur(12px)">
                        <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                        <span class="text-xs font-bold" style="color:var(--text2)">⚡ Auto-Delivery via WhatsApp</span>
                    </span>
                </div>
                <!-- Headline -->
                <div class="hero-stagger" style="animation-delay:0.2s">
                    <h1 class="text-2xl sm:text-4xl lg:text-[3.2rem] font-black mb-5" style="color:var(--text);line-height:1.25">
                        Lupakan Iklan Mengganggu.<br>
                        <span class="hero-gradient-text">Upgrade Gaya Hidupmu.</span>
                    </h1>
                </div>
                <!-- Description -->
                <div class="hero-stagger" style="animation-delay:0.3s">
                    <p class="text-sm sm:text-base leading-relaxed mb-7 max-w-md mx-auto lg:mx-0" style="color:var(--text2)">Akun premium Netflix, Spotify, Canva & lainnya. Bayar instan, akun langsung dikirim ke WhatsApp dalam <strong class="text-green-500">1 menit</strong>!</p>
                </div>
                <!-- CTA Buttons -->
                <div class="hero-stagger flex flex-col sm:flex-row items-center lg:items-start justify-center lg:justify-start gap-3" style="animation-delay:0.4s">
                    <a href="#products" class="group inline-flex items-center gap-2 px-8 py-4 bg-green-600 text-white text-sm font-bold rounded-2xl hover:bg-green-500 btn-press shadow-xl shadow-green-600/25 pulse-glow transition-all">
                        <i class="ri-shopping-bag-3-line group-hover:scale-110 transition-transform"></i> Lihat Produk
                    </a>
                    <a href="https://wa.me/6281234567890" target="_blank" class="inline-flex items-center gap-2 px-7 py-4 text-sm font-semibold rounded-2xl hover:border-green-500 btn-press transition-all" style="background:var(--card);border:1px solid var(--card-border);backdrop-filter:blur(8px);color:var(--text2)">
                        <i class="ri-whatsapp-line text-[#25D366]"></i> Chat Admin
                    </a>
                </div>
                <!-- Trust badges -->
                <div class="hero-stagger mt-7 flex items-center justify-center lg:justify-start gap-4" style="animation-delay:0.5s">
                    <div class="flex items-center gap-1.5 text-[10px] font-bold" style="color:var(--muted)"><i class="ri-shield-check-fill text-green-500 text-sm"></i> Garansi 30 Hari</div>
                    <div class="flex items-center gap-1.5 text-[10px] font-bold" style="color:var(--muted)"><i class="ri-timer-flash-fill text-green-500 text-sm"></i> Instan via WA</div>
                    <div class="flex items-center gap-1.5 text-[10px] font-bold" style="color:var(--muted)"><i class="ri-verified-badge-fill text-green-500 text-sm"></i> Terdaftar</div>
                </div>
            </div>

            <!-- RIGHT: Floating Glass Cards (Responsive) -->
            <div class="relative hero-stagger" style="animation-delay:0.5s">
                <!-- Glow behind cards -->
                <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[250px] h-[250px] lg:w-[300px] lg:h-[300px] rounded-full" style="background:radial-gradient(circle,rgba(34,197,94,0.15),transparent 70%);filter:blur(40px)"></div>

                <!-- Desktop: Absolute floating cards -->
                <div class="hidden lg:block" style="min-height:420px">
                    <!-- Card 1 - Netflix -->
                    <div class="absolute hero-float" style="top:15%;left:15%;animation-delay:0s">
                        <div class="w-48 rounded-2xl p-5 shadow-xl" style="background:var(--card);border:1px solid var(--card-border);backdrop-filter:blur(16px)">
                            <img src="images/products/netflix.png" alt="Netflix" class="w-14 h-14 rounded-xl object-contain shadow-sm" style="background:white;padding:3px">
                            <p class="text-sm font-bold mb-0.5" style="color:var(--text)">Netflix Premium</p>
                            <p class="text-[10px]" style="color:var(--muted)">4K · Multi-device</p>
                            <div class="mt-2 flex items-center gap-1.5">
                                <span class="text-xs font-bold text-green-500">Rp 35.000</span>
                                <span class="text-[9px] line-through" style="color:var(--muted)">Rp 175.000</span>
                            </div>
                        </div>
                    </div>
                    <!-- Card 2 - Spotify -->
                    <div class="absolute hero-float" style="top:0%;right:5%;animation-delay:-2s">
                        <div class="w-40 rounded-2xl p-4 shadow-lg" style="background:var(--card);border:1px solid var(--card-border);backdrop-filter:blur(16px)">
                            <img src="images/products/spotify.png" alt="Spotify" class="w-12 h-12 rounded-xl object-contain shadow-sm" style="background:white;padding:3px">
                            <p class="text-xs font-bold" style="color:var(--text)">Spotify Premium</p>
                            <p class="text-[10px] text-green-500 font-bold mt-1">Rp 18.000</p>
                        </div>
                    </div>
                    <!-- Card 3 - Canva -->
                    <div class="absolute hero-float" style="bottom:5%;left:25%;animation-delay:-4s">
                        <div class="w-44 rounded-2xl p-4 shadow-lg" style="background:var(--card);border:1px solid var(--card-border);backdrop-filter:blur(16px)">
                            <div class="flex items-center gap-3">
                                <img src="images/products/canva.png" alt="Canva" class="w-11 h-11 rounded-xl object-contain shadow-sm shrink-0" style="background:white;padding:2px">
                                <div><p class="text-xs font-bold" style="color:var(--text)">Canva Pro</p><p class="text-[10px] text-green-500 font-bold">Rp 35.000</p></div>
                            </div>
                        </div>
                    </div>
                    <!-- Card 4 - ChatGPT -->
                    <div class="absolute hero-float" style="bottom:15%;right:0%;animation-delay:-1s">
                        <div class="w-36 rounded-2xl p-3.5 shadow-lg" style="background:var(--card);border:1px solid var(--card-border);backdrop-filter:blur(16px)">
                            <div class="flex items-center gap-2.5">
                                <img src="images/products/chatgpt.png" alt="ChatGPT" class="w-10 h-10 rounded-lg object-contain shadow-sm shrink-0" style="background:white;padding:2px">
                                <div><p class="text-[11px] font-bold" style="color:var(--text)">ChatGPT Plus</p><p class="text-[9px] text-green-500 font-bold">Rp 55.000</p></div>
                            </div>
                        </div>
                    </div>
                    <!-- Card 5 - YouTube -->
                    <div class="absolute hero-float" style="top:5%;left:0%;animation-delay:-3s">
                        <div class="w-32 rounded-xl p-3 shadow-md" style="background:var(--card);border:1px solid var(--card-border);backdrop-filter:blur(16px)">
                            <div class="flex items-center gap-2">
                                <img src="images/products/youtube.png" alt="YouTube" class="w-8 h-8 rounded-lg object-contain shadow-sm shrink-0" style="background:white;padding:2px">
                                <div><p class="text-[10px] font-bold" style="color:var(--text)">YouTube</p><p class="text-[9px]" style="color:var(--muted)">No Ads</p></div>
                            </div>
                        </div>
                    </div>
                    <!-- Floating decorative elements -->
                    <div class="absolute w-3 h-3 rounded-full bg-green-500/30 hero-float" style="top:40%;right:40%;animation-delay:-2.5s"></div>
                    <div class="absolute w-2 h-2 rounded-full bg-teal-400/25 hero-float" style="top:70%;right:55%;animation-delay:-1.5s"></div>
                    <div class="absolute w-4 h-4 rounded-full bg-purple-400/15 hero-float" style="top:25%;right:32%;animation-delay:-3.5s"></div>
                </div>

                <!-- Mobile: Mini floating cards grid -->
                <div class="lg:hidden grid grid-cols-2 gap-3 max-w-xs mx-auto">
                    <div class="rounded-2xl p-3.5 shadow-lg hero-float" style="background:var(--card);border:1px solid var(--card-border);backdrop-filter:blur(12px);animation-delay:0s">
                        <img src="images/products/netflix.png" alt="Netflix" class="w-9 h-9 rounded-lg object-contain shadow-sm mb-2" style="background:white;padding:2px">
                        <p class="text-[11px] font-bold" style="color:var(--text)">Netflix</p>
                        <p class="text-[10px] text-green-500 font-bold">Rp 35.000</p>
                    </div>
                    <div class="rounded-2xl p-3.5 shadow-lg hero-float" style="background:var(--card);border:1px solid var(--card-border);backdrop-filter:blur(12px);animation-delay:-1.5s">
                        <img src="images/products/spotify.png" alt="Spotify" class="w-9 h-9 rounded-lg object-contain shadow-sm mb-2" style="background:white;padding:2px">
                        <p class="text-[11px] font-bold" style="color:var(--text)">Spotify</p>
                        <p class="text-[10px] text-green-500 font-bold">Rp 18.000</p>
                    </div>
                    <div class="rounded-2xl p-3.5 shadow-lg hero-float" style="background:var(--card);border:1px solid var(--card-border);backdrop-filter:blur(12px);animation-delay:-3s">
                        <img src="images/products/canva.png" alt="Canva" class="w-9 h-9 rounded-lg object-contain shadow-sm mb-2" style="background:white;padding:2px">
                        <p class="text-[11px] font-bold" style="color:var(--text)">Canva Pro</p>
                        <p class="text-[10px] text-green-500 font-bold">Rp 35.000</p>
                    </div>
                    <div class="rounded-2xl p-3.5 shadow-lg hero-float" style="background:var(--card);border:1px solid var(--card-border);backdrop-filter:blur(12px);animation-delay:-4.5s">
                        <img src="images/products/chatgpt.png" alt="ChatGPT" class="w-9 h-9 rounded-lg object-contain shadow-sm mb-2" style="background:white;padding:2px">
                        <p class="text-[11px] font-bold" style="color:var(--text)">ChatGPT</p>
                        <p class="text-[10px] text-green-500 font-bold">Rp 55.000</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    /* Hero Staggered Entrance */
    .hero-stagger {
        opacity: 0; transform: translateY(24px);
        animation: heroEnter 0.7s cubic-bezier(0.16,1,0.3,1) forwards;
    }
    @keyframes heroEnter { to { opacity:1; transform:translateY(0); } }

    /* Hero Text Gradient */
    .hero-gradient-text {
        background: linear-gradient(135deg, var(--accent) 0%, #34d399 40%, #06b6d4 100%);
        -webkit-background-clip: text; -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    .dark .hero-gradient-text {
        background: linear-gradient(135deg, #4ade80 0%, #34d399 40%, #22d3ee 100%);
        -webkit-background-clip: text; -webkit-text-fill-color: transparent;
        background-clip: text;
        filter: drop-shadow(0 0 20px rgba(74,222,128,0.3));
    }

    /* Hero Floating Animation */
    .hero-float {
        animation: heroFloat 6s ease-in-out infinite;
    }
    @keyframes heroFloat {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-14px); }
    }

    /* Section gradient overlays */
    .section-glow { position: relative; overflow: hidden; }
    .section-glow::before {
        content: ''; position: absolute; inset: 0; pointer-events: none; z-index: 0;
    }
    .section-glow > * { position: relative; z-index: 1; }
    .glow-green::before { background: radial-gradient(ellipse 70% 50% at 20% 50%, rgba(34,197,94,0.06) 0%, transparent 70%); }
    .glow-cyan::before { background: radial-gradient(ellipse 60% 60% at 80% 30%, rgba(6,182,212,0.06) 0%, transparent 70%); }
    .glow-purple::before { background: radial-gradient(ellipse 50% 70% at 30% 70%, rgba(167,139,250,0.05) 0%, transparent 70%); }
    .glow-amber::before { background: radial-gradient(ellipse 60% 50% at 70% 60%, rgba(251,191,36,0.05) 0%, transparent 70%); }
    .glow-pink::before { background: radial-gradient(ellipse 55% 55% at 50% 40%, rgba(236,72,153,0.04) 0%, transparent 70%); }
</style>


<!-- ═══ GARANSI TIMELINE ═══ -->
<section class="py-14 section-glow glow-green" style="background:var(--bg2)">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-10" data-aos="fade-up">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 glass text-green-500 text-[11px] font-bold rounded-full mb-3"><i class="ri-shield-star-fill"></i> Garansi Anti-Ribet</span>
            <h2 class="text-2xl sm:text-3xl font-black" style="color:var(--text)">Akun Mati? <span class="text-green-500 neon-text">Kami Ganti Baru.</span></h2>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="glass-strong rounded-2xl p-6 text-center hover-lift" data-aos="fade-up" data-aos-delay="0">
                <div class="w-14 h-14 mx-auto mb-4 rounded-2xl bg-green-500/10 flex items-center justify-center"><i class="ri-timer-flash-fill text-2xl text-green-500"></i></div>
                <h3 class="text-sm font-bold mb-1" style="color:var(--text)">Proses 5 Menit</h3>
                <p class="text-[11px] leading-relaxed" style="color:var(--text2)">Order → Bayar → Akun dikirim. Semua otomatis.</p>
            </div>
            <div class="glass-strong rounded-2xl p-6 text-center hover-lift" data-aos="fade-up" data-aos-delay="100">
                <div class="w-14 h-14 mx-auto mb-4 rounded-2xl bg-green-500/10 flex items-center justify-center"><i class="ri-shield-check-fill text-2xl text-green-500"></i></div>
                <h3 class="text-sm font-bold mb-1" style="color:var(--text)">Garansi 30 Hari</h3>
                <p class="text-[11px] leading-relaxed" style="color:var(--text2)">Akun bermasalah? Langsung ganti tanpa biaya.</p>
            </div>
            <div class="glass-strong rounded-2xl p-6 text-center hover-lift" data-aos="fade-up" data-aos-delay="200">
                <div class="w-14 h-14 mx-auto mb-4 rounded-2xl bg-green-500/10 flex items-center justify-center"><i class="ri-refund-2-fill text-2xl text-green-500"></i></div>
                <h3 class="text-sm font-bold mb-1" style="color:var(--text)">Uang Kembali</h3>
                <p class="text-[11px] leading-relaxed" style="color:var(--text2)">Gagal total? 100% uang kamu kembali.</p>
            </div>
        </div>
    </div>
</section>


<!-- ═══ CARA ORDER ═══ -->
<section class="py-14 section-glow glow-cyan" style="background:var(--bg)" id="cara-order">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-10" data-aos="fade-up">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 glass text-green-500 text-[11px] font-bold rounded-full mb-3"><i class="ri-route-fill"></i> 3 Langkah Mudah</span>
            <h2 class="text-2xl sm:text-3xl font-black" style="color:var(--text)">Cara <span class="text-green-500 neon-text">Order</span></h2>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
            <div class="relative text-center" data-aos="fade-up" data-aos-delay="0">
                <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-green-600 text-white flex items-center justify-center shadow-lg shadow-green-600/25">
                    <i class="ri-shopping-cart-2-fill text-2xl"></i>
                </div>
                <h3 class="text-sm font-bold mb-1" style="color:var(--text)">Pilih Produk</h3>
                <p class="text-xs leading-relaxed" style="color:var(--text2)">Cari produk favorit lalu klik "Beli Sekarang"</p>
                <div class="hidden sm:block absolute top-8 -right-3" style="color:var(--muted)"><i class="ri-arrow-right-line text-xl"></i></div>
            </div>
            <div class="relative text-center" data-aos="fade-up" data-aos-delay="150">
                <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-green-600 text-white flex items-center justify-center shadow-lg shadow-green-600/25">
                    <i class="ri-wallet-3-fill text-2xl"></i>
                </div>
                <h3 class="text-sm font-bold mb-1" style="color:var(--text)">Bayar</h3>
                <p class="text-xs leading-relaxed" style="color:var(--text2)">Isi data & selesaikan pembayaran</p>
                <div class="hidden sm:block absolute top-8 -right-3" style="color:var(--muted)"><i class="ri-arrow-right-line text-xl"></i></div>
            </div>
            <div class="text-center" data-aos="fade-up" data-aos-delay="300">
                <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-[#25D366] text-white flex items-center justify-center shadow-lg shadow-green-500/25">
                    <i class="ri-whatsapp-fill text-2xl"></i>
                </div>
                <h3 class="text-sm font-bold mb-1" style="color:var(--text)">Terima Akun</h3>
                <p class="text-xs leading-relaxed" style="color:var(--text2)">Akun dikirim otomatis ke WhatsApp!</p>
            </div>
        </div>
    </div>
</section>


<!-- ═══ PRODUCTS ═══ -->
<section class="py-14 section-glow glow-purple" style="background:var(--bg)" id="products">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-8" data-aos="fade-up">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 glass text-green-500 text-[11px] font-bold rounded-full mb-3"><i class="ri-apps-2-fill"></i> Koleksi Lengkap</span>
            <h2 class="text-2xl sm:text-3xl font-black" style="color:var(--text)">Pilih Produk <span class="text-green-500 neon-text">Favoritmu</span></h2>
        </div>

        <!-- ✨ SEARCH BAR (aesthetic Gen-Z) -->
        <div class="max-w-xl mx-auto mb-8" data-aos="fade-up" data-aos-delay="50">
            <div class="relative group">
                <!-- Animated gradient glow border -->
                <div class="absolute -inset-[2px] rounded-2xl opacity-0 group-focus-within:opacity-100 transition-all duration-700 blur-md" style="background:conic-gradient(from 0deg, rgba(34,197,94,0.4), rgba(6,182,212,0.3), rgba(167,139,250,0.2), rgba(52,211,153,0.3), rgba(34,197,94,0.4));animation:spinGlow 4s linear infinite"></div>
                <div class="relative flex items-center rounded-2xl overflow-hidden transition-all duration-300 group-focus-within:shadow-lg group-focus-within:shadow-green-500/10" style="background:var(--card);border:1.5px solid var(--card-border)">
                    <div class="pl-4 pr-1 shrink-0">
                        <div class="w-9 h-9 rounded-xl flex items-center justify-center transition-all duration-300 group-focus-within:bg-green-500/10" style="background:var(--surface)">
                            <i class="ri-search-2-line text-base transition-colors duration-300 group-focus-within:text-green-500" style="color:var(--muted)"></i>
                        </div>
                    </div>
                    <input type="text" id="searchInput" placeholder="Cari produk favoritmu..." class="w-full px-3 py-4 bg-transparent text-sm focus:outline-none placeholder:text-gray-400" style="color:var(--text)">
                    <div class="pr-3 shrink-0 flex items-center gap-2">
                        <button id="advSearchToggle" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-[10px] font-bold glass hover:bg-green-500/10 transition-all" style="color:var(--text2)" title="Advanced Search">
                            <i class="ri-equalizer-fill"></i><span class="hidden sm:inline">Filter</span>
                        </button>
                        <kbd class="hidden sm:inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[10px] font-bold border transition-colors group-focus-within:border-green-500/30 group-focus-within:text-green-500" style="color:var(--muted);background:var(--surface);border-color:var(--card-border)">⌘K</kbd>
                    </div>
                </div>
            </div>
            <!-- Advanced Search Panel -->
            <div id="advSearchPanel" class="adv-search-panel glass-strong rounded-2xl p-5 mt-3">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-xs font-black flex items-center gap-1.5" style="color:var(--text)"><i class="ri-equalizer-fill text-green-500"></i>Filter Lanjutan</span>
                    <button onclick="resetAdvSearch()" class="text-[10px] font-bold text-green-500 hover:underline">Reset</button>
                </div>
                <div class="mb-4">
                    <label class="text-[10px] font-bold mb-2 block" style="color:var(--text2)">Range Harga</label>
                    <input type="range" min="0" max="100000" value="100000" step="5000" class="price-range-slider" id="priceRange">
                    <div class="flex justify-between mt-1">
                        <span class="text-[9px] font-semibold" style="color:var(--muted)">Rp 0</span>
                        <span class="text-[10px] font-bold text-green-500" id="priceRangeLabel">Maks Rp 100.000</span>
                    </div>
                </div>
                <div>
                    <label class="text-[10px] font-bold mb-2 block" style="color:var(--text2)">Stok</label>
                    <div class="flex gap-2">
                        <button class="stock-filter-btn px-3 py-1.5 rounded-lg text-[10px] font-bold glass active" data-sf="all" style="color:var(--text2)">Semua</button>
                        <button class="stock-filter-btn px-3 py-1.5 rounded-lg text-[10px] font-bold glass" data-sf="available" style="color:var(--text2)">Tersedia</button>
                        <button class="stock-filter-btn px-3 py-1.5 rounded-lg text-[10px] font-bold glass" data-sf="empty" style="color:var(--text2)">Habis</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters + Sort -->
        <div class="flex flex-wrap items-center justify-between gap-3 mb-5">
            <div class="flex flex-wrap gap-2" id="catTabs" data-aos="fade-right">
                <button class="px-4 py-2 rounded-2xl text-[11px] font-bold bg-green-600 text-white btn-press transition-all active" data-cat="all"><i class="ri-apps-fill mr-1"></i>Semua</button>
                <button class="px-4 py-2 rounded-2xl text-[11px] font-bold glass btn-press transition-all" style="color:var(--text2)" data-cat="streaming"><i class="ri-live-fill mr-1 text-red-400"></i>Streaming</button>
                <button class="px-4 py-2 rounded-2xl text-[11px] font-bold glass btn-press transition-all" style="color:var(--text2)" data-cat="video"><i class="ri-video-fill mr-1 text-pink-400"></i>Video</button>
                <button class="px-4 py-2 rounded-2xl text-[11px] font-bold glass btn-press transition-all" style="color:var(--text2)" data-cat="music"><i class="ri-music-2-fill mr-1 text-green-400"></i>Music</button>
                <button class="px-4 py-2 rounded-2xl text-[11px] font-bold glass btn-press transition-all" style="color:var(--text2)" data-cat="design"><i class="ri-palette-fill mr-1 text-teal-400"></i>Design</button>
                <button class="px-4 py-2 rounded-2xl text-[11px] font-bold glass btn-press transition-all" style="color:var(--text2)" data-cat="editing"><i class="ri-film-fill mr-1 text-purple-400"></i>Editing</button>
                <button class="px-4 py-2 rounded-2xl text-[11px] font-bold glass btn-press transition-all" style="color:var(--text2)" data-cat="productivity"><i class="ri-briefcase-4-fill mr-1 text-blue-400"></i>Productivity</button>
                <button class="px-4 py-2 rounded-2xl text-[11px] font-bold glass btn-press transition-all" style="color:var(--text2)" data-cat="learning"><i class="ri-book-fill mr-1 text-amber-400"></i>Learning</button>
                <button class="px-4 py-2 rounded-2xl text-[11px] font-bold glass btn-press transition-all" style="color:var(--text2)" data-cat="security"><i class="ri-shield-fill mr-1 text-cyan-400"></i>Security</button>
                <button class="px-4 py-2 rounded-2xl text-[11px] font-bold glass btn-press transition-all" style="color:var(--text2)" data-cat="otaku"><i class="ri-book-open-fill mr-1 text-orange-400"></i>Otaku</button>
            </div>
            <div class="flex items-center gap-3" data-aos="fade-left">
                <select id="sortPrice" class="glass rounded-xl text-xs font-semibold px-3 py-2 focus:outline-none focus:ring-1 focus:ring-green-500/20" style="color:var(--text2);background:var(--card)">
                    <option value="default">Urutkan</option>
                    <option value="low">Termurah</option>
                    <option value="high">Termahal</option>
                </select>
                <label class="flex items-center gap-2 cursor-pointer select-none">
                    <input type="checkbox" id="hideOOS" class="w-4 h-4 rounded accent-green-600">
                    <span class="text-xs font-medium" style="color:var(--muted)">Stok Kosong</span>
                </label>
            </div>
        </div>

        <!-- 🔥 FLASH SALE BANNER (below filters) -->
        <div class="mb-6 relative" data-aos="fade-up" data-aos-delay="100">
            <div class="relative rounded-2xl p-4 sm:p-5 overflow-hidden" style="background:linear-gradient(135deg, rgba(239,68,68,0.08) 0%, rgba(251,146,60,0.06) 50%, rgba(34,197,94,0.08) 100%);border:1px solid rgba(239,68,68,0.12)">
                <!-- Animated shimmer overlay -->
                <div class="absolute inset-0 opacity-20" style="background:linear-gradient(90deg, transparent 0%, rgba(255,255,255,0.15) 50%, transparent 100%);animation:shimmerBanner 3s ease-in-out infinite"></div>
                <div class="relative flex flex-col sm:flex-row items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-red-500 to-orange-500 flex items-center justify-center shadow-lg shadow-red-500/25 shrink-0">
                            <i class="ri-fire-fill text-white text-base animate-pulse"></i>
                        </div>
                        <div>
                            <div class="flex items-center gap-2 flex-wrap">
                                <h3 class="text-sm font-black" style="color:var(--text)">Flash Sale!</h3>
                                <span class="relative flex items-center gap-1 px-2 py-0.5 rounded-lg text-[9px] font-black bg-red-500/15 text-red-500 uppercase tracking-wider">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500 animate-ping absolute -left-0.5"></span>
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500 relative ml-1.5"></span>
                                    Live
                                </span>
                            </div>
                            <p class="text-[11px] mt-0.5" style="color:var(--muted)">Diskon hingga <strong class="text-green-500">85%</strong> — hari ini saja!</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-mono font-black" style="background:var(--surface);color:var(--text)">
                            <span class="text-red-500" id="cdH">00</span><span class="text-red-400 animate-pulse">:</span>
                            <span class="text-red-500" id="cdM">00</span><span class="text-red-400 animate-pulse">:</span>
                            <span class="text-red-500" id="cdS">00</span>
                        </div>
                        <a href="#products" class="hidden sm:inline-flex items-center gap-1.5 px-4 py-2 bg-gradient-to-r from-red-500 to-orange-500 text-white text-[11px] font-bold rounded-xl hover:shadow-lg hover:shadow-red-500/25 btn-press transition-all shrink-0">
                            <i class="ri-shopping-bag-3-line"></i> Belanja
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Grid 1→2→4 -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5" id="prodGrid">
        <?php while ($p = $products->fetch_assoc()):
            $stock = stockCount($conn, $p['id']);
            $ic = icon($p['name']);
            $tag = getTag($p['name']);
            $orig = origPrice($p['price']);
            $hemat = hematPct($p['price']);
        ?>
            <a href="store/product_detail.php?id=<?= $p['id'] ?>" class="product-card glass-strong rounded-2xl overflow-hidden hover-lift card-glow product-item cursor-pointer block" data-cat="<?= htmlspecialchars($p['category']) ?>" data-stock="<?= $stock ?>" data-name="<?= htmlspecialchars(strtolower($p['name'])) ?>" data-price="<?= $p['price'] ?>" data-aos="fade-up">
                <div class="relative h-32 flex items-center justify-center <?= $ic[1] ?> group">
                    <?php $img = imgFile($p['name']); ?>
                    <?php if ($img && file_exists(__DIR__.'/images/products/'.$img)): ?>
                    <img src="images/products/<?= $img ?>" alt="<?= htmlspecialchars($p['name']) ?>" class="w-16 h-16 rounded-2xl object-contain group-hover:scale-110 transition-transform duration-300 shadow-md" style="background:white;padding:4px">
                    <?php else: ?>
                    <div class="w-16 h-16 rounded-2xl glass flex items-center justify-center">
                        <i class="<?= $ic[0] ?> text-3xl <?= $ic[2] ?> group-hover:scale-110 transition-transform duration-300"></i>
                    </div>
                    <?php endif; ?>
                    <?php if ($tag): ?>
                    <span class="absolute top-3 left-3 px-2.5 py-1 rounded-lg text-[9px] font-black text-white <?= $tag==='TERLARIS'?'badge-shimmer':'bg-orange-500' ?> shadow-sm"><?= $tag ?></span>
                    <?php endif; ?>
                    <?php if ($stock <= 0): ?>
                        <span class="absolute top-3 right-3 inline-flex items-center gap-1 px-2.5 py-1 rounded-lg glass text-red-400 text-[10px] font-bold"><i class="ri-close-circle-fill"></i>Habis</span>
                    <?php else: ?>
                        <span class="absolute top-3 right-3 inline-flex items-center gap-1 px-2.5 py-1 rounded-lg glass text-green-500 text-[10px] font-bold"><span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span><?= $stock ?> stok</span>
                    <?php endif; ?>
                </div>
                <div class="p-4">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-[9px] font-bold uppercase tracking-wider" style="color:var(--muted)"><?= htmlspecialchars(str_replace('_',' & ',$p['category'])) ?></span>
                        <span class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded text-[8px] font-bold glass text-green-500"><i class="ri-lock-fill"></i>Private</span>
                    </div>
                    <h3 class="text-sm font-bold mt-1 mb-2" style="color:var(--text)"><?= htmlspecialchars($p['name']) ?></h3>
                    <div class="flex items-center gap-2 mb-3">
                        <span class="text-lg font-extrabold text-green-500"><?= rupiah($p['price']) ?></span>
                        <span class="text-[10px] line-through" style="color:var(--muted)"><?= rupiah($orig) ?></span>
                        <span class="px-1.5 py-0.5 rounded text-[9px] font-black bg-red-500/10 text-red-400">-<?= $hemat ?>%</span>
                    </div>
                    <?php if ($stock > 0): ?>
                        <div class="flex gap-2">
                            <button onclick="event.preventDefault();quickBuy(<?= $p['id'] ?>)" class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-3 bg-green-600 text-white text-xs font-bold rounded-xl hover:bg-green-500 btn-press shadow-lg shadow-green-600/15"><i class="ri-flashlight-fill"></i> Quick Buy</button>
                            <button onclick="event.preventDefault();toggleWishlist(<?= $p['id'] ?>,'<?= htmlspecialchars($p['name'],ENT_QUOTES) ?>',<?= $p['price'] ?>)" class="w-11 h-11 rounded-xl glass flex items-center justify-center hover:bg-pink-500/10 btn-press transition wishlist-btn" data-pid="<?= $p['id'] ?>" title="Wishlist"><i class="ri-heart-line text-lg" style="color:var(--muted)"></i></button>
                        </div>
                        <?php if ($stock <= 5): ?>
                        <div class="flex items-center gap-1 mt-1.5"><i class="ri-fire-fill text-amber-500 text-[10px] animate-pulse"></i><span class="text-[9px] font-bold text-amber-500">Sisa <?= $stock ?> — hampir habis!</span></div>
                        <?php endif; ?>
                    <?php else: ?>
                        <button disabled class="w-full inline-flex items-center justify-center gap-1.5 px-4 py-3 rounded-xl text-xs font-semibold cursor-not-allowed" style="background:var(--surface);color:var(--muted)"><i class="ri-close-circle-line"></i> Stok Habis</button>
                    <?php endif; ?>
                </div>
            </a>
        <?php endwhile; ?>
        </div>
    </div>
</section>


<!-- ═══ TESTIMONIALS ═══ -->
<section class="py-14 section-glow glow-amber" style="background:var(--bg2)">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-8" data-aos="fade-up">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 glass text-green-500 text-[11px] font-bold rounded-full mb-3"><i class="ri-chat-smile-3-fill"></i> Testimoni</span>
            <h2 class="text-2xl sm:text-3xl font-black" style="color:var(--text)">Apa Kata <span class="text-green-500 neon-text">Mereka</span></h2>
        </div>
        <div class="overflow-hidden rounded-2xl" data-aos="fade-up">
            <div class="testi-track flex" id="testiTrack">
                <?php
                $testimonials = [
                    ['name'=>'Rina A.','text'=>'Cepat banget! Baru bayar langsung dapet akun Netflix-nya via WA. Garansi juga aman, pernah mati langsung diganti.','product'=>'Netflix Premium','stars'=>5],
                    ['name'=>'Andi S.','text'=>'Spotify Premium murah banget di sini. Udah langganan 4 bulan belum pernah bermasalah. Recommended!','product'=>'Spotify Premium','stars'=>5],
                    ['name'=>'Devi P.','text'=>'Canva Pro buat tugas kuliah jadi gampang banget. Auto-delivery via WA beneran kurang dari 1 menit!','product'=>'Canva Pro','stars'=>5],
                    ['name'=>'Fajar R.','text'=>'Admin responsif banget, saya chat malam-malam pun dibales. ChatGPT Plus lancar jaya, worth it!','product'=>'ChatGPT Plus','stars'=>5],
                    ['name'=>'Maya L.','text'=>'Udah 3x beli di sini, selalu aman. YouTube Premium tanpa iklan, mantap!','product'=>'YouTube Premium','stars'=>5],
                    ['name'=>'Budi K.','text'=>'Pertama ragu karena murah banget, ternyata legit. Disney+ Hotstar lancar nonton bareng keluarga.','product'=>'Disney+ Hotstar','stars'=>5],
                ];
                foreach ($testimonials as $i => $t):
                ?>
                <div class="min-w-full sm:min-w-[50%] lg:min-w-[33.333%] p-2">
                    <div class="glass-strong rounded-2xl p-5 h-full">
                        <div class="flex items-center gap-1 mb-3">
                            <?php for($s=0;$s<$t['stars'];$s++): ?><i class="ri-star-fill text-amber-400 text-sm"></i><?php endfor; ?>
                        </div>
                        <p class="text-xs leading-relaxed mb-4" style="color:var(--text2)">"<?= $t['text'] ?>"</p>
                        <div class="flex items-center gap-3 pt-3" style="border-top:1px solid var(--border)">
                            <div class="w-9 h-9 rounded-xl bg-green-500/10 flex items-center justify-center text-green-500 text-xs font-bold"><?= strtoupper(substr($t['name'],0,1)) ?></div>
                            <div>
                                <p class="text-xs font-bold" style="color:var(--text)"><?= $t['name'] ?></p>
                                <p class="text-[10px]" style="color:var(--muted)">Pembeli <?= $t['product'] ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="flex items-center justify-center gap-2 mt-5">
            <button onclick="slideTesti(-1)" class="w-9 h-9 rounded-xl glass flex items-center justify-center hover:text-green-500 btn-press" style="color:var(--text2)"><i class="ri-arrow-left-s-line text-lg"></i></button>
            <button onclick="slideTesti(1)" class="w-9 h-9 rounded-xl glass flex items-center justify-center hover:text-green-500 btn-press" style="color:var(--text2)"><i class="ri-arrow-right-s-line text-lg"></i></button>
        </div>
    </div>
</section>


<!-- ═══ ARTICLES ═══ -->
<?php if ($articles && $articles->num_rows > 0): ?>
<section class="py-14 section-glow glow-pink" style="background:var(--bg)">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-lg font-bold flex items-center gap-2 mb-6" style="color:var(--text)" data-aos="fade-right"><i class="ri-newspaper-fill text-green-500"></i> Artikel Terbaru</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <?php
        $aIcons=['ri-file-text-fill text-green-500','ri-lightbulb-flash-fill text-amber-400','ri-tools-fill text-blue-400'];
        $ai=0;
        while ($a = $articles->fetch_assoc()): ?>
            <a href="pages/article.php?slug=<?= urlencode($a['slug']) ?>" class="block glass-strong rounded-2xl overflow-hidden hover-lift group" data-aos="fade-up" data-aos-delay="<?= $ai*100 ?>">
                <div class="h-24 flex items-center justify-center" style="background:var(--surface)">
                    <i class="<?= $aIcons[$ai%3] ?> text-3xl group-hover:scale-110 transition-transform duration-300"></i>
                </div>
                <div class="p-4">
                    <p class="text-[10px] mb-1 flex items-center gap-1" style="color:var(--muted)"><i class="ri-calendar-event-fill"></i><?= date('d M Y', strtotime($a['created_at'])) ?></p>
                    <h3 class="font-bold text-sm mb-2 group-hover:text-green-500 transition-colors line-clamp-2" style="color:var(--text)"><?= htmlspecialchars($a['title']) ?></h3>
                    <span class="text-xs font-bold text-green-500 flex items-center gap-1">Baca Selengkapnya <i class="ri-arrow-right-s-line group-hover:translate-x-1 transition-transform"></i></span>
                </div>
            </a>
        <?php $ai++; endwhile; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ═══ TOP BUYER OF THE MONTH ═══ -->
<?php
// Get top buyer of current month
$currentMonth = date('Y-m');
$topBuyerQuery = $conn->query("SELECT u.name, u.id, SUM(t.amount) as total_spent, COUNT(t.id) as total_orders 
    FROM transactions t JOIN users u ON t.user_id=u.id 
    WHERE t.status='SUCCESS' AND DATE_FORMAT(t.created_at, '%Y-%m')='$currentMonth' 
    GROUP BY t.user_id ORDER BY total_spent DESC LIMIT 1");
$topBuyer = $topBuyerQuery ? $topBuyerQuery->fetch_assoc() : null;

$rewardPrizes = [
    ['icon'=>'ri-scissors-fill','color'=>'text-violet-500','bg'=>'bg-violet-500/10','name'=>'CapCut Pro','duration'=>'1 Bulan'],
    ['icon'=>'ri-spotify-fill','color'=>'text-green-500','bg'=>'bg-green-500/10','name'=>'Spotify Premium','duration'=>'1 Bulan'],
    ['icon'=>'ri-youtube-fill','color'=>'text-red-500','bg'=>'bg-red-500/10','name'=>'YouTube Premium','duration'=>'3 Bulan'],
];
?>
<section class="py-14 section-glow glow-amber" style="background:var(--bg)">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-8" data-aos="fade-up">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 glass text-amber-400 text-[11px] font-bold rounded-full mb-3"><i class="ri-trophy-fill"></i> Reward Bulanan</span>
            <h2 class="text-2xl sm:text-3xl font-black" style="color:var(--text)">Top Buyer <span class="hero-gradient-text">of the Month</span></h2>
            <p class="text-xs mt-2 max-w-md mx-auto" style="color:var(--muted)">Pembeli dengan total belanja terbanyak setiap bulan berhak mendapatkan hadiah produk premium GRATIS!</p>
        </div>

        <div class="glass-strong rounded-3xl p-6 sm:p-8 mb-6" data-aos="fade-up">
            <?php if ($topBuyer): ?>
            <div class="flex flex-col sm:flex-row items-center gap-5 mb-6">
                <div class="relative">
                    <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-amber-400 to-orange-500 flex items-center justify-center text-white text-3xl font-black shadow-xl shadow-amber-500/25">
                        <?= strtoupper(substr($topBuyer['name'], 0, 1)) ?>
                    </div>
                    <div class="absolute -top-2 -right-2 w-8 h-8 rounded-full bg-amber-400 flex items-center justify-center shadow-lg">
                        <i class="ri-vip-crown-2-fill text-white text-sm"></i>
                    </div>
                </div>
                <div class="text-center sm:text-left">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-amber-400 mb-1">🏆 Top Buyer — <?= date('F Y') ?></p>
                    <h3 class="text-xl font-black mb-1" style="color:var(--text)"><?= htmlspecialchars($topBuyer['name']) ?></h3>
                    <div class="flex items-center gap-3 justify-center sm:justify-start">
                        <span class="text-xs font-bold text-green-500"><i class="ri-money-dollar-circle-fill mr-0.5"></i><?= rupiah($topBuyer['total_spent']) ?></span>
                        <span class="text-[10px]" style="color:var(--muted)"><?= $topBuyer['total_orders'] ?> pesanan</span>
                    </div>
                </div>
            </div>
            <?php else: ?>
            <div class="text-center py-4 mb-4">
                <div class="w-16 h-16 mx-auto mb-3 rounded-2xl bg-amber-500/10 flex items-center justify-center"><i class="ri-trophy-line text-3xl text-amber-400"></i></div>
                <p class="text-sm font-bold mb-1" style="color:var(--text2)">Belum ada top buyer bulan ini</p>
                <p class="text-xs" style="color:var(--muted)">Jadilah yang pertama belanja bulan ini untuk jadi Top Buyer!</p>
            </div>
            <?php endif; ?>

            <!-- Reward prizes -->
            <div class="pt-5" style="border-top:1px solid var(--border)">
                <p class="text-[10px] font-bold uppercase tracking-widest mb-3" style="color:var(--muted)"><i class="ri-gift-2-fill text-amber-400 mr-1"></i>Hadiah yang bisa didapatkan:</p>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <?php foreach ($rewardPrizes as $prize): ?>
                    <div class="rounded-xl p-3.5 flex items-center gap-3" style="background:var(--surface)">
                        <div class="w-10 h-10 rounded-xl <?= $prize['bg'] ?> flex items-center justify-center shrink-0">
                            <i class="<?= $prize['icon'] ?> text-lg <?= $prize['color'] ?>"></i>
                        </div>
                        <div>
                            <p class="text-xs font-bold" style="color:var(--text)"><?= $prize['name'] ?></p>
                            <p class="text-[10px]" style="color:var(--muted)">Gratis <?= $prize['duration'] ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>


<!-- ═══ FAQ ═══ -->
<section class="py-14 section-glow glow-cyan" style="background:var(--bg2)">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-8" data-aos="fade-up">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 glass text-green-500 text-[11px] font-bold rounded-full mb-3"><i class="ri-question-answer-fill"></i> FAQ</span>
            <h2 class="text-2xl sm:text-3xl font-black" style="color:var(--text)">Pertanyaan <span class="text-green-500 neon-text">Umum</span></h2>
        </div>
        <div class="space-y-3" id="faqList">
            <?php
            $faqs = [
                ['q'=>'Apakah akun yang dijual aman dan legal?','a'=>'Akun kami adalah akun premium private yang legal. Kami memberikan garansi full — akun mati langsung diganti tanpa biaya.'],
                ['q'=>'Berapa lama akun dikirim setelah bayar?','a'=>'Akun dikirim otomatis kurang dari 1 menit setelah pembayaran dikonfirmasi, langsung ke WhatsApp yang kamu daftarkan.'],
                ['q'=>'Bagaimana jika akun tiba-tiba mati?','a'=>'Langsung chat admin via WhatsApp, akun diganti baru tanpa biaya tambahan selama masih dalam masa garansi 30 hari.'],
                ['q'=>'Harus daftar akun dulu untuk beli?','a'=>'Tidak wajib! Kamu bisa langsung beli. Tapi kalau daftar, bisa lihat riwayat pesanan dan auto-fill data checkout.'],
                ['q'=>'Metode pembayaran apa saja yang tersedia?','a'=>'Kami menerima transfer bank (BCA, BRI, BNI, Mandiri), e-wallet (Dana, GoPay, ShopeePay, OVO), dan QRIS.'],
            ];
            foreach ($faqs as $fi => $faq):
            ?>
            <div class="glass-strong rounded-2xl overflow-hidden" data-aos="fade-up" data-aos-delay="<?= $fi*50 ?>">
                <button class="faq-toggle w-full flex items-center justify-between p-5 text-left" onclick="toggleFaq(this)">
                    <span class="text-sm font-bold pr-4" style="color:var(--text)"><?= $faq['q'] ?></span>
                    <i class="ri-add-line text-green-500 text-lg transition-transform shrink-0"></i>
                </button>
                <div class="faq-content hidden px-5 pb-5 pt-0">
                    <p class="text-xs leading-relaxed" style="color:var(--text2)"><?= $faq['a'] ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ═══ LEADERBOARD ═══ -->
<section class="py-14 section-glow glow-cyan" style="background:var(--bg)">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-8" data-aos="fade-up">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 glass text-amber-500 text-[11px] font-bold rounded-full mb-3"><i class="ri-trophy-fill"></i> Leaderboard</span>
            <h2 class="text-2xl sm:text-3xl font-black" style="color:var(--text)">Top Buyer <span class="text-green-500 neon-text">Bulan Ini</span></h2>
            <p class="text-xs mt-2" style="color:var(--muted)">Member paling aktif berbelanja di Natsy Premiums</p>
        </div>

        <div class="glass-strong rounded-3xl p-6 sm:p-8" data-aos="fade-up">
            <?php
            // Fetch top buyers this month
            $monthStart = date('Y-m-01');
            $monthEnd = date('Y-m-t');
            $topBuyers = $conn->query("SELECT customer_name, COUNT(*) as total_orders, SUM(amount) as total_spent
                FROM transactions WHERE status='SUCCESS' AND created_at BETWEEN '$monthStart 00:00:00' AND '$monthEnd 23:59:59'
                GROUP BY customer_name ORDER BY total_orders DESC, total_spent DESC LIMIT 5")->fetch_all(MYSQLI_ASSOC);

            // If not enough data, use sample data
            if (count($topBuyers) < 3) {
                $topBuyers = [
                    ['customer_name'=>'Putra D.','total_orders'=>12,'total_spent'=>540000],
                    ['customer_name'=>'Sari M.','total_orders'=>9,'total_spent'=>405000],
                    ['customer_name'=>'Raka A.','total_orders'=>7,'total_spent'=>315000],
                    ['customer_name'=>'Dewi N.','total_orders'=>5,'total_spent'=>225000],
                    ['customer_name'=>'Andi R.','total_orders'=>4,'total_spent'=>180000],
                ];
            }
            $medals=['🥇','🥈','🥉','4','5'];
            $medalColors=['text-amber-500','text-gray-400','text-orange-600','',''];
            foreach ($topBuyers as $bi => $buyer):
                $initials = strtoupper(substr($buyer['customer_name'], 0, 2));
            ?>
            <div class="flex items-center gap-4 py-4 <?= $bi > 0 ? 'border-t' : '' ?>" style="border-color:var(--border)">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center text-lg font-black <?= $bi < 3 ? $medalColors[$bi] : '' ?>" style="<?= $bi >= 3 ? 'color:var(--muted)' : '' ?>">
                    <?= $medals[$bi] ?>
                </div>
                <div class="w-10 h-10 rounded-xl bg-green-600/10 flex items-center justify-center text-green-500 text-xs font-black"><?= $initials ?></div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold truncate" style="color:var(--text)"><?= htmlspecialchars($buyer['customer_name']) ?></p>
                    <p class="text-[10px]" style="color:var(--muted)"><?= $buyer['total_orders'] ?> pesanan bulan ini</p>
                </div>
                <div class="text-right">
                    <p class="text-sm font-extrabold text-green-500"><?= rupiah($buyer['total_spent']) ?></p>
                    <p class="text-[9px]" style="color:var(--muted)">total belanja</p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>



<script>
// ─── Category filter ───
document.querySelectorAll('#catTabs button').forEach(function(b){
    b.addEventListener('click',function(){
        document.querySelectorAll('#catTabs button').forEach(function(t){
            t.className='px-5 py-2.5 rounded-2xl text-xs font-bold glass btn-press transition-all';
            t.style.color='var(--text2)';
        });
        this.className='px-5 py-2.5 rounded-2xl text-xs font-bold bg-green-600 text-white btn-press transition-all active';
        this.style.color='';
        filterProducts();
    });
});
document.getElementById('hideOOS').addEventListener('change',filterProducts);
document.getElementById('searchInput').addEventListener('input',filterProducts);
document.getElementById('sortPrice').addEventListener('change',sortProducts);

// ─── Advanced Search Panel ───
(function(){
    var toggle = document.getElementById('advSearchToggle');
    var panel = document.getElementById('advSearchPanel');
    var priceRange = document.getElementById('priceRange');
    var priceLabel = document.getElementById('priceRangeLabel');
    if(toggle && panel){
        toggle.addEventListener('click',function(){
            panel.classList.toggle('open');
            toggle.classList.toggle('bg-green-500/20');
        });
    }
    if(priceRange){
        priceRange.addEventListener('input',function(){
            var v=parseInt(this.value);
            priceLabel.textContent='Maks Rp '+v.toLocaleString('id-ID');
            filterProducts();
        });
    }
    document.querySelectorAll('.stock-filter-btn').forEach(function(btn){
        btn.addEventListener('click',function(){
            document.querySelectorAll('.stock-filter-btn').forEach(function(b){ b.classList.remove('active','bg-green-600','text-white'); b.style.color='var(--text2)'; });
            this.classList.add('active','bg-green-600','text-white');
            this.style.color='';
            filterProducts();
        });
    });
})();

window.resetAdvSearch = function(){
    var pr = document.getElementById('priceRange');
    if(pr) { pr.value=100000; document.getElementById('priceRangeLabel').textContent='Maks Rp 100.000'; }
    document.querySelectorAll('.stock-filter-btn').forEach(function(b){ b.classList.remove('active','bg-green-600','text-white'); b.style.color='var(--text2)'; });
    var allBtn = document.querySelector('.stock-filter-btn[data-sf="all"]');
    if(allBtn) { allBtn.classList.add('active','bg-green-600','text-white'); allBtn.style.color=''; }
    filterProducts();
};

function filterProducts(){
    var catEl=document.querySelector('#catTabs button.active');
    var cat=catEl?catEl.getAttribute('data-cat'):'all';
    var hide=document.getElementById('hideOOS').checked;
    var q=document.getElementById('searchInput').value.toLowerCase().trim();
    var maxPrice=parseInt(document.getElementById('priceRange')?.value||100000);
    var stockFilter=document.querySelector('.stock-filter-btn.active')?.getAttribute('data-sf')||'all';
    document.querySelectorAll('.product-item').forEach(function(c){
        var mc=(cat==='all'||c.getAttribute('data-cat')===cat);
        var ms=!hide||parseInt(c.getAttribute('data-stock'))>0;
        var mq=!q||c.getAttribute('data-name').indexOf(q)!==-1;
        var mp=parseFloat(c.getAttribute('data-price'))<=maxPrice;
        var sf=true;
        if(stockFilter==='available') sf=parseInt(c.getAttribute('data-stock'))>0;
        else if(stockFilter==='empty') sf=parseInt(c.getAttribute('data-stock'))<=0;
        c.style.display=(mc&&ms&&mq&&mp&&sf)?'':'none';
    });
}

function sortProducts(){
    var val=document.getElementById('sortPrice').value;
    var grid=document.getElementById('prodGrid');
    var items=Array.from(grid.querySelectorAll('.product-item'));
    if(val==='low') items.sort(function(a,b){return parseFloat(a.dataset.price)-parseFloat(b.dataset.price);});
    else if(val==='high') items.sort(function(a,b){return parseFloat(b.dataset.price)-parseFloat(a.dataset.price);});
    items.forEach(function(item){grid.appendChild(item);});
}

// ─── FAQ ───
function toggleFaq(btn){
    var content=btn.nextElementSibling;
    var icon=btn.querySelector('i');
    var isOpen=!content.classList.contains('hidden');
    document.querySelectorAll('.faq-content').forEach(function(c){c.classList.add('hidden');});
    document.querySelectorAll('.faq-toggle i').forEach(function(i){i.className='ri-add-line text-green-500 text-lg transition-transform shrink-0';});
    if(!isOpen){content.classList.remove('hidden');icon.className='ri-subtract-line text-green-500 text-lg transition-transform shrink-0';}
}

// ─── Testimonial Slider ───
var testiIdx=0;
function slideTesti(dir){
    var track=document.getElementById('testiTrack');
    var items=track.children.length;
    var visibleCount=window.innerWidth>=1024?3:(window.innerWidth>=640?2:1);
    var maxIdx=Math.max(0,items-visibleCount);
    testiIdx=Math.max(0,Math.min(maxIdx,testiIdx+dir));
    track.style.transform='translateX(-'+(testiIdx*(100/visibleCount))+'%)';
}

// ─── Countdown (reset daily at midnight) ───
function updateCountdown(){
    var now=new Date();
    var end=new Date(now);
    end.setHours(23,59,59,999);
    var diff=Math.max(0,Math.floor((end-now)/1000));
    var h=Math.floor(diff/3600);
    var m=Math.floor((diff%3600)/60);
    var s=diff%60;
    document.getElementById('cdH').textContent=String(h).padStart(2,'0');
    document.getElementById('cdM').textContent=String(m).padStart(2,'0');
    document.getElementById('cdS').textContent=String(s).padStart(2,'0');
}
updateCountdown();setInterval(updateCountdown,1000);

// ─── Quick Buy ───
function quickBuy(pid){
    fetch('<?= BASE_URL ?>/store/product_detail.php?id='+pid+'&ajax_add=1',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:'add_to_cart=1&csrf_token=<?= csrfToken() ?>'})
    .then(function(){window.location.href='<?= BASE_URL ?>/store/cart.php';})
    .catch(function(){window.location.href='<?= BASE_URL ?>/store/product_detail.php?id='+pid;});
}

// ─── Wishlist (localStorage) ───
function getWishlist(){try{return JSON.parse(localStorage.getItem('wishlist'))||[];}catch(e){return [];}}
function saveWishlist(list){localStorage.setItem('wishlist',JSON.stringify(list));}
function toggleWishlist(pid,name,price){
    var list=getWishlist();
    var idx=list.findIndex(function(i){return i.id===pid;});
    var btn=document.querySelector('.wishlist-btn[data-pid="'+pid+'"]');
    if(idx>-1){
        list.splice(idx,1);
        if(btn) btn.querySelector('i').className='ri-heart-line text-lg';
        if(btn) btn.querySelector('i').style.color='var(--muted)';
    } else {
        list.push({id:pid,name:name,price:price});
        if(btn){btn.querySelector('i').className='ri-heart-fill text-lg';btn.querySelector('i').style.color='#EC4899';}
        // Micro-animation
        if(btn){btn.style.transform='scale(1.2)';setTimeout(function(){btn.style.transform='';},200);}
    }
    saveWishlist(list);
    updateWishlistCount();
}
function updateWishlistCount(){
    var c=getWishlist().length;
    var badge=document.getElementById('wishlistBadge');
    if(badge){badge.textContent=c;badge.style.display=c>0?'flex':'none';}
}
// Init wishlist UI on load
(function(){
    var list=getWishlist();
    list.forEach(function(item){
        var btn=document.querySelector('.wishlist-btn[data-pid="'+item.id+'"]');
        if(btn){btn.querySelector('i').className='ri-heart-fill text-lg';btn.querySelector('i').style.color='#EC4899';}
    });
    updateWishlistCount();
})();
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
