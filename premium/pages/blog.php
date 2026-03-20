<?php
/**
 * BLOG.PHP — Blog & Tutorial System (#39)
 */
require_once __DIR__ . '/../includes/koneksi.php';
$pageTitle = 'Blog & Tutorial — ' . SITE_NAME;
$pageDesc = 'Baca artikel, tutorial, dan tips seputar akun premium digital.';

$cat = $_GET['cat'] ?? '';
$where = "WHERE is_published=1";
if ($cat && in_array($cat, ['tutorial','news','tips','promo'])) {
    $where .= " AND category='" . $conn->real_escape_string($cat) . "'";
}
$posts = $conn->query("SELECT * FROM blog_posts $where ORDER BY published_at DESC");

include __DIR__ . '/../includes/header.php';
?>

<!-- Rich Snippets (#42) -->
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Blog",
    "name": "Natsy Premiums Blog",
    "url": "<?= BASE_URL ?>/pages/blog.php",
    "description": "Artikel, tutorial, dan tips seputar akun premium digital."
}
</script>

<section class="py-10 min-h-screen" style="background:var(--bg)">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Breadcrumb -->
        <nav class="flex items-center gap-2 text-xs mb-6" style="color:var(--muted)" data-aos="fade-right">
            <a href="<?= BASE_URL ?>/index.php" class="hover:text-green-500">Home</a>
            <i class="ri-arrow-right-s-line"></i>
            <span style="color:var(--text)">Blog & Tutorial</span>
        </nav>

        <!-- Header -->
        <div class="text-center mb-10" data-aos="fade-up">
            <h1 class="text-3xl font-black mb-2" style="color:var(--text)"><i class="ri-article-fill text-green-500 mr-2"></i>Blog & Tutorial</h1>
            <p class="text-sm" style="color:var(--text2)">Tips, tutorial, dan berita terbaru seputar akun premium</p>
        </div>

        <!-- Category Filters -->
        <div class="flex flex-wrap justify-center gap-2 mb-8" data-aos="fade-up">
            <a href="?cat=" class="px-4 py-2 rounded-xl text-xs font-bold transition <?= !$cat ? 'bg-green-600 text-white' : 'glass' ?>" style="<?= $cat ? 'color:var(--text2)' : '' ?>">Semua</a>
            <a href="?cat=tutorial" class="px-4 py-2 rounded-xl text-xs font-bold transition <?= $cat==='tutorial' ? 'bg-green-600 text-white' : 'glass' ?>" style="<?= $cat!=='tutorial' ? 'color:var(--text2)' : '' ?>"><i class="ri-book-mark-fill mr-1"></i>Tutorial</a>
            <a href="?cat=tips" class="px-4 py-2 rounded-xl text-xs font-bold transition <?= $cat==='tips' ? 'bg-green-600 text-white' : 'glass' ?>" style="<?= $cat!=='tips' ? 'color:var(--text2)' : '' ?>"><i class="ri-lightbulb-fill mr-1"></i>Tips</a>
            <a href="?cat=promo" class="px-4 py-2 rounded-xl text-xs font-bold transition <?= $cat==='promo' ? 'bg-green-600 text-white' : 'glass' ?>" style="<?= $cat!=='promo' ? 'color:var(--text2)' : '' ?>"><i class="ri-price-tag-3-fill mr-1"></i>Promo</a>
            <a href="?cat=news" class="px-4 py-2 rounded-xl text-xs font-bold transition <?= $cat==='news' ? 'bg-green-600 text-white' : 'glass' ?>" style="<?= $cat!=='news' ? 'color:var(--text2)' : '' ?>"><i class="ri-newspaper-fill mr-1"></i>News</a>
        </div>

        <!-- Blog Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php while ($post = $posts->fetch_assoc()): ?>
            <a href="<?= BASE_URL ?>/pages/blog_post.php?slug=<?= $post['slug'] ?>" class="glass-strong rounded-2xl overflow-hidden hover-lift transition-all group" data-aos="fade-up">
                <div class="h-40 flex items-center justify-center" style="background:linear-gradient(135deg, rgba(34,197,94,0.1), rgba(6,182,212,0.1))">
                    <i class="ri-<?= $post['category']==='tutorial' ? 'book-mark' : ($post['category']==='tips' ? 'lightbulb' : ($post['category']==='promo' ? 'price-tag-3' : 'newspaper')) ?>-fill text-5xl text-green-500/30 group-hover:scale-110 transition-transform"></i>
                </div>
                <div class="p-5">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="px-2 py-0.5 rounded-lg text-[9px] font-bold uppercase bg-green-500/10 text-green-600"><?= $post['category'] ?></span>
                        <span class="text-[10px]" style="color:var(--muted)"><i class="ri-eye-line mr-1"></i><?= number_format($post['views']) ?> views</span>
                    </div>
                    <h3 class="text-sm font-bold mb-1 group-hover:text-green-500 transition" style="color:var(--text)"><?= htmlspecialchars($post['title']) ?></h3>
                    <p class="text-xs line-clamp-2" style="color:var(--text2)"><?= htmlspecialchars($post['excerpt']) ?></p>
                    <div class="mt-3 flex items-center gap-2 text-[10px]" style="color:var(--muted)">
                        <i class="ri-calendar-line"></i>
                        <?= $post['published_at'] ? date('d M Y', strtotime($post['published_at'])) : '-' ?>
                    </div>
                </div>
            </a>
            <?php endwhile; ?>
        </div>

        <?php if ($posts->num_rows === 0): ?>
        <div class="text-center py-20 glass-strong rounded-2xl">
            <i class="ri-article-line text-5xl mb-3" style="color:var(--muted)"></i>
            <p class="text-sm font-bold" style="color:var(--text)">Belum ada artikel</p>
            <p class="text-xs" style="color:var(--muted)">Artikel akan segera ditambahkan!</p>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
