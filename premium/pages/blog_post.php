<?php
/**
 * BLOG_POST.PHP — Single Blog Post View (#39)
 */
require_once __DIR__ . '/../includes/koneksi.php';

$slug = $conn->real_escape_string($_GET['slug'] ?? '');
$post = $conn->query("SELECT * FROM blog_posts WHERE slug='$slug' AND is_published=1")->fetch_assoc();
if (!$post) { header('Location: ' . BASE_URL . '/pages/blog.php'); exit; }

// Increment views
$conn->query("UPDATE blog_posts SET views=views+1 WHERE id=" . $post['id']);

$pageTitle = $post['title'] . ' — ' . SITE_NAME;
$pageDesc = $post['excerpt'];
include __DIR__ . '/../includes/header.php';
?>

<script type="application/ld+json">
{"@context":"https://schema.org","@type":"Article","headline":"<?= htmlspecialchars($post['title']) ?>","datePublished":"<?= $post['published_at'] ?>","author":{"@type":"Organization","name":"Natsy Premiums"}}
</script>

<section class="py-10 min-h-screen" style="background:var(--bg)">
    <div class="max-w-3xl mx-auto px-4 sm:px-6">
        <nav class="flex items-center gap-2 text-xs mb-6" style="color:var(--muted)">
            <a href="<?= BASE_URL ?>/index.php" class="hover:text-green-500">Home</a><i class="ri-arrow-right-s-line"></i>
            <a href="<?= BASE_URL ?>/pages/blog.php" class="hover:text-green-500">Blog</a><i class="ri-arrow-right-s-line"></i>
            <span style="color:var(--text)"><?= htmlspecialchars($post['title']) ?></span>
        </nav>

        <article class="glass-strong rounded-2xl p-8" data-aos="fade-up">
            <div class="flex items-center gap-2 mb-4">
                <span class="px-3 py-1 rounded-lg text-[10px] font-bold uppercase bg-green-500/10 text-green-600"><?= $post['category'] ?></span>
                <span class="text-xs" style="color:var(--muted)"><i class="ri-calendar-line mr-1"></i><?= date('d M Y', strtotime($post['published_at'])) ?></span>
                <span class="text-xs" style="color:var(--muted)"><i class="ri-eye-line mr-1"></i><?= number_format($post['views']) ?></span>
            </div>
            <h1 class="text-2xl font-black mb-6" style="color:var(--text)"><?= htmlspecialchars($post['title']) ?></h1>
            <div class="prose max-w-none text-sm leading-relaxed" style="color:var(--text2)">
                <?= $post['content'] ?>
            </div>

            <!-- Share -->
            <div class="mt-8 pt-6 flex items-center gap-3" style="border-top:1px solid var(--border)">
                <span class="text-xs font-bold" style="color:var(--muted)">Share:</span>
                <a href="https://wa.me/?text=<?= urlencode($post['title'] . ' ' . BASE_URL . '/pages/blog_post.php?slug=' . $post['slug']) ?>" target="_blank" class="w-8 h-8 rounded-lg bg-green-500/10 flex items-center justify-center hover:scale-110 transition"><i class="ri-whatsapp-fill text-[#25D366]"></i></a>
                <a href="https://twitter.com/intent/tweet?text=<?= urlencode($post['title']) ?>&url=<?= urlencode(BASE_URL . '/pages/blog_post.php?slug=' . $post['slug']) ?>" target="_blank" class="w-8 h-8 rounded-lg bg-blue-500/10 flex items-center justify-center hover:scale-110 transition"><i class="ri-twitter-x-fill text-blue-400"></i></a>
            </div>
        </article>
    </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
