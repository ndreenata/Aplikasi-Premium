<?php
/**
 * ARTICLE.PHP — Detail Artikel · Dark Mode
 */
require_once __DIR__ . '/../includes/koneksi.php';

$slug = trim($_GET['slug'] ?? '');
if (empty($slug)) { header('Location: ' . BASE_URL . '/index.php'); exit; }

$stmt = $conn->prepare("SELECT * FROM articles WHERE slug=? LIMIT 1");
$stmt->bind_param("s",$slug); $stmt->execute();
$article = $stmt->get_result()->fetch_assoc(); $stmt->close();

if (!$article) { header('Location: ' . BASE_URL . '/index.php'); exit; }

$pageTitle = htmlspecialchars($article['title']) . ' — ' . SITE_NAME;
include __DIR__ . '/../includes/header.php';
?>

<section class="py-10 sm:py-14">
    <div class="max-w-3xl mx-auto px-4 sm:px-6">
        <div class="flex items-center gap-1.5 text-xs mb-5" style="color:var(--muted)" data-aos="fade-right">
            <a href="index.php" class="hover:text-green-500 transition"><i class="ri-home-5-line"></i></a>
            <i class="ri-arrow-right-s-line" style="color:var(--border)"></i>
            <span class="truncate max-w-[250px]" style="color:var(--text2)"><?= htmlspecialchars($article['title']) ?></span>
        </div>

        <div class="glass-strong rounded-2xl p-6 sm:p-10" data-aos="fade-up">
            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-lg glass text-[10px] font-semibold mb-3" style="color:var(--muted)"><i class="ri-calendar-event-fill text-green-500"></i> <?= date('d M Y', strtotime($article['created_at'])) ?></span>
            <h1 class="text-2xl sm:text-3xl font-black mb-6 leading-tight" style="color:var(--text)"><?= htmlspecialchars($article['title']) ?></h1>
            <div class="prose max-w-none text-sm leading-relaxed" style="color:var(--text2)"><?= $article['content'] ?></div>
        </div>

        <div class="mt-6 flex items-center gap-3" data-aos="fade-up">
            <a href="index.php" class="inline-flex items-center gap-1.5 px-4 py-2.5 glass text-xs font-semibold rounded-xl btn-press" style="color:var(--text2)"><i class="ri-arrow-left-s-line"></i> Beranda</a>
            <a href="index.php#products" class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-green-600 text-white text-xs font-semibold rounded-xl hover:bg-green-500 btn-press shadow-lg shadow-green-600/20"><i class="ri-shopping-bag-3-line"></i> Lihat Produk</a>
        </div>
    </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
