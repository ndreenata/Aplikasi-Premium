<?php
/**
 * SITEMAP.XML — Auto-generate sitemap for SEO (#41)
 */
require_once __DIR__ . '/includes/koneksi.php';

header('Content-Type: application/xml; charset=utf-8');
$base = 'http://' . $_SERVER['HTTP_HOST'] . '/premium';

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url><loc><?= $base ?>/</loc><changefreq>daily</changefreq><priority>1.0</priority></url>
    <url><loc><?= $base ?>/auth/login.php</loc><changefreq>monthly</changefreq><priority>0.5</priority></url>
    <url><loc><?= $base ?>/auth/register.php</loc><changefreq>monthly</changefreq><priority>0.5</priority></url>
    <url><loc><?= $base ?>/pages/privacy.php</loc><changefreq>yearly</changefreq><priority>0.3</priority></url>
    <url><loc><?= $base ?>/pages/terms.php</loc><changefreq>yearly</changefreq><priority>0.3</priority></url>
    <url><loc><?= $base ?>/pages/service_status.php</loc><changefreq>daily</changefreq><priority>0.6</priority></url>
    <url><loc><?= $base ?>/pages/blog.php</loc><changefreq>weekly</changefreq><priority>0.7</priority></url>
<?php
$products = $conn->query("SELECT id, name FROM products WHERE is_active=1");
while ($p = $products->fetch_assoc()):
?>
    <url><loc><?= $base ?>/store/product_detail.php?id=<?= $p['id'] ?></loc><changefreq>weekly</changefreq><priority>0.8</priority></url>
<?php endwhile; ?>
<?php
$blogs = $conn->query("SELECT slug FROM blog_posts WHERE is_published=1");
while ($b = $blogs->fetch_assoc()):
?>
    <url><loc><?= $base ?>/pages/blog_post.php?slug=<?= $b['slug'] ?></loc><changefreq>monthly</changefreq><priority>0.6</priority></url>
<?php endwhile; ?>
</urlset>
