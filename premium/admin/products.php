<?php
/**
 * ADMIN/PRODUCTS.PHP — Product CRUD with cost_price, image upload, profit column
 */
$pageTitle = 'Kelola Produk';
require_once __DIR__ . '/../includes/admin_header.php';

$products = $conn->query("SELECT p.*, (SELECT COUNT(*) FROM stocks s WHERE s.product_id=p.id AND s.status='available') as stock_count FROM products p ORDER BY p.id DESC");
$categories = $conn->query("SELECT DISTINCT category FROM products ORDER BY category");
$catList = [];
while ($c = $categories->fetch_assoc()) $catList[] = $c['category'];
?>

<!-- Header -->
<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 mb-5 anim-up d1">
    <div>
        <h1 class="text-lg font-black" style="color:var(--text)">Produk</h1>
        <p class="text-[11px]" style="color:var(--muted)">Kelola semua produk premium</p>
    </div>
    <button onclick="openProductModal()" class="btn btn-primary"><i class="ri-add-fill"></i> Tambah Produk</button>
</div>

<!-- Filters -->
<div class="flex flex-col sm:flex-row gap-3 mb-4 anim-up d2">
    <input type="text" id="searchProduct" placeholder="Cari produk..." class="admin-input" style="max-width:280px" oninput="filterTable()">
    <select id="filterCat" class="admin-input admin-select" style="max-width:180px" onchange="filterTable()">
        <option value="">Semua Kategori</option>
        <?php foreach ($catList as $cat): ?>
        <option value="<?= htmlspecialchars($cat) ?>"><?= htmlspecialchars(ucwords(str_replace('_',' & ',$cat))) ?></option>
        <?php endforeach; ?>
    </select>
</div>

<!-- Table -->
<div class="admin-card anim-up d3" style="padding:0;overflow:hidden">
    <div class="overflow-x-auto">
        <table class="admin-table" id="productTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Produk</th>
                    <th>Kategori</th>
                    <th>Harga</th>
                    <th>Modal</th>
                    <th>Profit</th>
                    <th>Stok</th>
                    <th>Status</th>
                    <th style="text-align:right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($p = $products->fetch_assoc()):
                    $profit = $p['price'] - ($p['cost_price'] ?? 0);
                ?>
                <tr data-name="<?= htmlspecialchars(strtolower($p['name'])) ?>" data-cat="<?= htmlspecialchars($p['category']) ?>">
                    <td class="text-[11px] font-mono" style="color:var(--muted)">#<?= $p['id'] ?></td>
                    <td>
                        <div class="flex items-center gap-2">
                            <?php if (!empty($p['image'])): ?>
                            <img src="<?= BASE_URL ?>/<?= htmlspecialchars($p['image']) ?>" class="w-8 h-8 rounded-lg object-cover" alt="">
                            <?php endif; ?>
                            <div>
                                <p class="text-xs font-bold" style="color:var(--text)"><?= htmlspecialchars($p['name']) ?></p>
                                <p class="text-[10px] truncate-2" style="color:var(--muted);max-width:200px"><?= htmlspecialchars($p['description'] ?? '') ?></p>
                            </div>
                        </div>
                    </td>
                    <td><span class="badge badge-info"><?= htmlspecialchars(ucwords(str_replace('_',' & ',$p['category']))) ?></span></td>
                    <td class="font-bold text-accent text-xs"><?= rupiah($p['price']) ?></td>
                    <td class="text-xs" style="color:var(--muted)"><?= $p['cost_price'] > 0 ? rupiah($p['cost_price']) : '-' ?></td>
                    <td class="text-xs font-bold" style="color:<?= $profit >= 0 ? '#10B981' : '#EF4444' ?>"><?= $p['cost_price'] > 0 ? rupiah($profit) : '-' ?></td>
                    <td>
                        <?php if ($p['stock_count'] > 0): ?>
                            <span class="badge <?= $p['stock_count'] < 5 ? 'badge-warning' : 'badge-success' ?>"><?= $p['stock_count'] ?> ready</span>
                        <?php else: ?>
                            <span class="badge badge-danger">Habis</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <button onclick="toggleProduct(<?= $p['id'] ?>,<?= $p['is_active']?0:1 ?>)" class="badge <?= $p['is_active']?'badge-success':'badge-muted' ?> cursor-pointer hover:opacity-80 transition" title="Toggle aktif">
                            <?= $p['is_active'] ? '<i class="ri-check-fill"></i>Aktif' : '<i class="ri-close-fill"></i>Off' ?>
                        </button>
                    </td>
                    <td style="text-align:right">
                        <div class="flex items-center justify-end gap-1">
                            <button onclick='editProduct(<?= json_encode($p) ?>)' class="btn btn-outline btn-sm" title="Edit"><i class="ri-pencil-fill"></i></button>
                            <button onclick="confirmAction('Hapus produk ini?',function(){deleteProduct(<?= $p['id'] ?>)})" class="btn btn-danger btn-sm" title="Hapus"><i class="ri-delete-bin-6-fill"></i></button>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ═══ PRODUCT MODAL ═══ -->
<div class="admin-modal" id="productModal">
    <div class="admin-modal-content">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-base font-bold" style="color:var(--text)" id="productModalTitle">Tambah Produk</h3>
            <button onclick="closeModal('productModal')" class="w-8 h-8 rounded-lg flex items-center justify-center hover:bg-red-500/10 transition" style="color:var(--muted)"><i class="ri-close-line text-lg"></i></button>
        </div>
        <form id="productForm" onsubmit="saveProduct(event)" enctype="multipart/form-data">
            <input type="hidden" id="pId" value="">
            <div class="space-y-4">
                <div>
                    <label class="block text-[11px] font-bold mb-1.5" style="color:var(--text2)">Nama Produk *</label>
                    <input type="text" id="pName" class="admin-input" required placeholder="Netflix Premium 1 Bulan">
                </div>
                <div>
                    <label class="block text-[11px] font-bold mb-1.5" style="color:var(--text2)">Deskripsi</label>
                    <textarea id="pDesc" class="admin-input" rows="3" placeholder="Deskripsi produk..."></textarea>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[11px] font-bold mb-1.5" style="color:var(--text2)">Harga Jual (Rp) *</label>
                        <input type="number" id="pPrice" class="admin-input" required placeholder="25000" min="0">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold mb-1.5" style="color:var(--text2)">Harga Modal (Rp)</label>
                        <input type="number" id="pCost" class="admin-input" placeholder="15000" min="0">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[11px] font-bold mb-1.5" style="color:var(--text2)">Kategori *</label>
                        <select id="pCategory" class="admin-input admin-select" required>
                            <option value="musik_video">Musik & Video</option>
                            <option value="desain">Desain</option>
                            <option value="productivity">Productivity</option>
                            <option value="gaming">Gaming</option>
                            <option value="education">Education</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold mb-1.5" style="color:var(--text2)">Gambar Produk</label>
                        <input type="file" id="pImage" accept="image/jpeg,image/png,image/webp,image/gif" class="admin-input" style="padding:7px 12px">
                        <div id="imagePreview" class="mt-2 hidden">
                            <img id="imgPreviewEl" src="" class="w-16 h-16 rounded-xl object-cover">
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" id="pActive" checked class="w-4 h-4 rounded accent-green-500">
                    <label for="pActive" class="text-xs font-semibold" style="color:var(--text2)">Aktif (tampil di toko)</label>
                </div>
                <button type="submit" class="btn btn-primary w-full justify-center" style="padding:12px"><i class="ri-save-fill"></i> Simpan Produk</button>
            </div>
        </form>
    </div>
</div>

<script>
// Image preview
document.getElementById('pImage').addEventListener('change', function(e) {
    var file = e.target.files[0];
    if (file) {
        var reader = new FileReader();
        reader.onload = function(ev) {
            document.getElementById('imgPreviewEl').src = ev.target.result;
            document.getElementById('imagePreview').classList.remove('hidden');
        };
        reader.readAsDataURL(file);
    }
});

function filterTable() {
    var q = document.getElementById('searchProduct').value.toLowerCase();
    var cat = document.getElementById('filterCat').value;
    document.querySelectorAll('#productTable tbody tr').forEach(function(tr) {
        var mn = !q || tr.dataset.name.indexOf(q) !== -1;
        var mc = !cat || tr.dataset.cat === cat;
        tr.style.display = (mn && mc) ? '' : 'none';
    });
}

function openProductModal() {
    document.getElementById('productModalTitle').textContent = 'Tambah Produk';
    document.getElementById('pId').value = '';
    document.getElementById('productForm').reset();
    document.getElementById('pActive').checked = true;
    document.getElementById('imagePreview').classList.add('hidden');
    openModal('productModal');
}

function editProduct(p) {
    document.getElementById('productModalTitle').textContent = 'Edit Produk';
    document.getElementById('pId').value = p.id;
    document.getElementById('pName').value = p.name;
    document.getElementById('pDesc').value = p.description || '';
    document.getElementById('pPrice').value = p.price;
    document.getElementById('pCost').value = p.cost_price || '';
    document.getElementById('pCategory').value = p.category;
    document.getElementById('pActive').checked = p.is_active == 1;
    if (p.image) {
        document.getElementById('imgPreviewEl').src = '<?= BASE_URL ?>/' + p.image;
        document.getElementById('imagePreview').classList.remove('hidden');
    } else {
        document.getElementById('imagePreview').classList.add('hidden');
    }
    openModal('productModal');
}

function saveProduct(e) {
    e.preventDefault();
    var fd = new FormData();
    fd.append('action', 'product_save');
    fd.append('id', document.getElementById('pId').value);
    fd.append('name', document.getElementById('pName').value);
    fd.append('description', document.getElementById('pDesc').value);
    fd.append('price', document.getElementById('pPrice').value);
    fd.append('cost_price', document.getElementById('pCost').value || '0');
    fd.append('category', document.getElementById('pCategory').value);
    fd.append('is_active', document.getElementById('pActive').checked ? 1 : 0);

    var imageFile = document.getElementById('pImage').files[0];
    if (imageFile) fd.append('image', imageFile);

    fetch('<?= BASE_URL ?>/admin/api.php', {
        method: 'POST',
        body: fd
    }).then(function(r){ return r.json(); }).then(function(d) {
        if (d.ok) { showToast(d.msg); location.reload(); }
        else showToast(d.msg, 'error');
    });
}

function toggleProduct(id, val) {
    adminAPI('product_toggle', { id: id, is_active: val }, function(d) {
        if (d.ok) location.reload();
        else showToast(d.msg, 'error');
    });
}

function deleteProduct(id) {
    adminAPI('product_delete', { id: id }, function(d) {
        if (d.ok) { showToast(d.msg); location.reload(); }
        else showToast(d.msg, 'error');
    });
}
</script>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
