<?php
/**
 * ADMIN/ARTICLES.PHP — Article CRUD
 */
$pageTitle = 'Kelola Artikel';
require_once __DIR__ . '/../includes/admin_header.php';

$articles = $conn->query("SELECT * FROM articles ORDER BY id DESC");
?>

<!-- Header -->
<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 mb-5">
    <div>
        <h1 class="text-lg font-black" style="color:var(--text)">Artikel</h1>
        <p class="text-[11px]" style="color:var(--muted)">Kelola artikel & blog</p>
    </div>
    <button onclick="openArticleModal()" class="btn btn-primary"><i class="ri-add-fill"></i> Tambah Artikel</button>
</div>

<!-- Table -->
<div class="admin-card" style="padding:0;overflow:hidden">
    <div class="overflow-x-auto">
        <table class="admin-table">
            <thead>
                <tr><th>ID</th><th>Judul</th><th>Slug</th><th>Dibuat</th><th style="text-align:right">Aksi</th></tr>
            </thead>
            <tbody>
                <?php if ($articles->num_rows === 0): ?>
                <tr><td colspan="5" class="text-center py-8" style="color:var(--muted)">Belum ada artikel</td></tr>
                <?php endif; ?>
                <?php while ($a = $articles->fetch_assoc()): ?>
                <tr>
                    <td class="text-[11px] font-mono" style="color:var(--muted)">#<?= $a['id'] ?></td>
                    <td>
                        <p class="text-xs font-bold" style="color:var(--text)"><?= htmlspecialchars($a['title']) ?></p>
                        <p class="text-[10px] truncate-2" style="color:var(--muted);max-width:300px"><?= htmlspecialchars(strip_tags(substr($a['content'],0,80))) ?>...</p>
                    </td>
                    <td><span class="font-mono text-[11px] px-2 py-1 rounded-lg" style="background:var(--bg2);color:var(--text2)"><?= htmlspecialchars($a['slug']) ?></span></td>
                    <td class="text-[11px]" style="color:var(--muted)"><?= date('d M Y', strtotime($a['created_at'])) ?></td>
                    <td style="text-align:right">
                        <div class="flex items-center justify-end gap-1">
                            <a href="<?= BASE_URL ?>/article.php?slug=<?= urlencode($a['slug']) ?>" target="_blank" class="btn btn-outline btn-sm" title="Preview"><i class="ri-eye-fill"></i></a>
                            <button onclick='editArticle(<?= json_encode($a) ?>)' class="btn btn-outline btn-sm" title="Edit"><i class="ri-pencil-fill"></i></button>
                            <button onclick="confirmAction('Hapus artikel ini?',function(){deleteArticle(<?= $a['id'] ?>)})" class="btn btn-danger btn-sm" title="Hapus"><i class="ri-delete-bin-6-fill"></i></button>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ═══ ARTICLE MODAL ═══ -->
<div class="admin-modal" id="articleModal">
    <div class="admin-modal-content" style="max-width:600px">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-base font-bold" style="color:var(--text)" id="articleModalTitle">Tambah Artikel</h3>
            <button onclick="closeModal('articleModal')" class="w-8 h-8 rounded-lg flex items-center justify-center hover:bg-red-500/10" style="color:var(--muted)"><i class="ri-close-line text-lg"></i></button>
        </div>
        <form id="articleForm" onsubmit="saveArticle(event)">
            <input type="hidden" id="aId" value="">
            <div class="space-y-4">
                <div>
                    <label class="block text-[11px] font-bold mb-1.5" style="color:var(--text2)">Judul *</label>
                    <input type="text" id="aTitle" class="admin-input" required placeholder="Judul artikel" oninput="autoSlug()">
                </div>
                <div>
                    <label class="block text-[11px] font-bold mb-1.5" style="color:var(--text2)">Slug</label>
                    <input type="text" id="aSlug" class="admin-input" placeholder="otomatis-dari-judul">
                </div>
                <div>
                    <label class="block text-[11px] font-bold mb-1.5" style="color:var(--text2)">Konten (HTML) *</label>
                    <textarea id="aContent" class="admin-input" rows="8" required placeholder="<p>Tulis konten artikel...</p>"></textarea>
                </div>
                <button type="submit" class="btn btn-primary w-full justify-center" style="padding:12px"><i class="ri-save-fill"></i> Simpan Artikel</button>
            </div>
        </form>
    </div>
</div>

<script>
function autoSlug() {
    var t = document.getElementById('aTitle').value;
    document.getElementById('aSlug').value = t.toLowerCase().replace(/[^a-z0-9\s-]/g,'').replace(/\s+/g,'-').replace(/-+/g,'-');
}

function openArticleModal() {
    document.getElementById('articleModalTitle').textContent = 'Tambah Artikel';
    document.getElementById('aId').value = '';
    document.getElementById('articleForm').reset();
    openModal('articleModal');
}

function editArticle(a) {
    document.getElementById('articleModalTitle').textContent = 'Edit Artikel';
    document.getElementById('aId').value = a.id;
    document.getElementById('aTitle').value = a.title;
    document.getElementById('aSlug').value = a.slug;
    document.getElementById('aContent').value = a.content || '';
    openModal('articleModal');
}

function saveArticle(e) {
    e.preventDefault();
    adminAPI('article_save', {
        id: document.getElementById('aId').value,
        title: document.getElementById('aTitle').value,
        slug: document.getElementById('aSlug').value,
        content: document.getElementById('aContent').value
    }, function(d) {
        if (d.ok) { showToast(d.msg); location.reload(); }
        else showToast(d.msg, 'error');
    });
}

function deleteArticle(id) {
    adminAPI('article_delete', { id: id }, function(d) {
        if (d.ok) { showToast(d.msg); location.reload(); }
        else showToast(d.msg, 'error');
    });
}
</script>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
