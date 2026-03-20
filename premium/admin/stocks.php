<?php
/**
 * ADMIN/STOCKS.PHP — Stock Management with Buyer Details, Bulk Formatter & Low Stock Alerts
 */
$pageTitle = 'Kelola Stok';
require_once __DIR__ . '/../includes/admin_header.php';

$productList = $conn->query("SELECT id, name FROM products ORDER BY name");
$prodOpts = [];
while ($r = $productList->fetch_assoc()) $prodOpts[] = $r;

$filterPid = (int)($_GET['product'] ?? 0);
$filterStatus = $_GET['status'] ?? '';

$sql = "SELECT s.*, p.name as product_name,
        sl.buyer_info, sl.action as log_action, sl.created_at as log_date,
        t.customer_name as buyer_name, t.phone_number as buyer_phone, t.invoice_number
        FROM stocks s 
        JOIN products p ON s.product_id=p.id 
        LEFT JOIN stock_logs sl ON s.id=sl.stock_id AND sl.action='sold'
        LEFT JOIN transactions t ON t.product_id=s.product_id AND t.status='SUCCESS' AND sl.buyer_info IS NOT NULL AND t.customer_name=sl.buyer_info";
if ($filterPid > 0) $sql .= " AND s.product_id=$filterPid";
if ($filterStatus === 'available') $sql .= " AND s.status='available'";
elseif ($filterStatus === 'sold') $sql .= " AND s.status='sold'";
$sql .= " ORDER BY s.id DESC";
$stocks = $conn->query($sql);

// Low stock summary
$lowStockProducts = $conn->query("SELECT p.id, p.name, COUNT(s.id) as available FROM products p LEFT JOIN stocks s ON p.id=s.product_id AND s.status='available' WHERE p.is_active=1 GROUP BY p.id HAVING available < 5 ORDER BY available ASC");
?>

<!-- Header -->
<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 mb-5 anim-up d1">
    <div>
        <h1 class="text-lg font-black" style="color:var(--text)">Stok</h1>
        <p class="text-[11px]" style="color:var(--muted)">Kelola akun & stok produk</p>
    </div>
    <button onclick="openModal('stockModal')" class="btn btn-primary"><i class="ri-add-fill"></i> Tambah Stok</button>
</div>

<?php if ($lowStockProducts->num_rows > 0): ?>
<!-- Low Stock Alert -->
<div class="admin-card mb-4 anim-up d2" style="border-color:rgba(239,68,68,0.3);background:rgba(239,68,68,0.04)">
    <div class="flex items-center gap-2 mb-2">
        <i class="ri-error-warning-fill text-red-500"></i>
        <span class="text-xs font-bold text-red-500">Produk Stok Menipis</span>
    </div>
    <div class="flex flex-wrap gap-2">
        <?php while ($ls = $lowStockProducts->fetch_assoc()): ?>
        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-[11px] font-semibold" style="background:rgba(239,68,68,0.08);color:#EF4444">
            <?= htmlspecialchars($ls['name']) ?>
            <span class="px-1.5 py-0.5 rounded-md text-[9px] font-black" style="background:rgba(239,68,68,0.15)"><?= $ls['available'] ?></span>
        </span>
        <?php endwhile; ?>
    </div>
</div>
<?php endif; ?>

<!-- Filter -->
<div class="flex flex-col sm:flex-row gap-3 mb-4 anim-up d3">
    <select id="filterProduct" class="admin-input admin-select" style="max-width:240px" onchange="applyFilter()">
        <option value="0">Semua Produk</option>
        <?php foreach ($prodOpts as $po): ?>
        <option value="<?= $po['id'] ?>" <?= $filterPid==$po['id']?'selected':'' ?>><?= htmlspecialchars($po['name']) ?></option>
        <?php endforeach; ?>
    </select>
    <select id="filterStatus" class="admin-input admin-select" style="max-width:160px" onchange="applyFilter()">
        <option value="">Semua Status</option>
        <option value="available" <?= $filterStatus==='available'?'selected':'' ?>>Available</option>
        <option value="sold" <?= $filterStatus==='sold'?'selected':'' ?>>Sold</option>
    </select>
</div>

<!-- Table -->
<div class="admin-card anim-up d4" style="padding:0;overflow:hidden">
    <div class="overflow-x-auto">
        <table class="admin-table">
            <thead>
                <tr><th>ID</th><th>Produk</th><th>Akun</th><th>Status</th><th>Pembeli</th><th>Dibuat</th><th style="text-align:right">Aksi</th></tr>
            </thead>
            <tbody>
                <?php while ($s = $stocks->fetch_assoc()):
                    // Mask account data
                    $parts = explode('|', $s['account_data']);
                    $masked = substr($parts[0], 0, 3) . '***' . (isset($parts[1]) ? '|'.str_repeat('*',strlen($parts[1])) : '');
                ?>
                <tr>
                    <td class="text-[11px] font-mono" style="color:var(--muted)">#<?= $s['id'] ?></td>
                    <td class="text-xs font-semibold" style="color:var(--text)"><?= htmlspecialchars($s['product_name']) ?></td>
                    <td>
                        <span class="font-mono text-[11px] px-2 py-1 rounded-lg" style="background:var(--bg2);color:var(--text2)" title="<?= htmlspecialchars($s['account_data']) ?>"><?= htmlspecialchars($masked) ?></span>
                        <button onclick="copyText('<?= htmlspecialchars(addslashes($s['account_data'])) ?>')" class="ml-1 text-[10px] hover:text-green-500 transition" style="color:var(--muted)" title="Copy"><i class="ri-file-copy-fill"></i></button>
                    </td>
                    <td>
                        <?= $s['status']==='available'
                            ? '<span class="badge badge-success"><i class="ri-check-fill"></i>Available</span>'
                            : '<span class="badge badge-muted"><i class="ri-close-fill"></i>Sold</span>'
                        ?>
                    </td>
                    <td>
                        <?php if ($s['status'] === 'sold' && !empty($s['buyer_info'])): ?>
                        <div>
                            <p class="text-[11px] font-semibold" style="color:var(--text)"><?= htmlspecialchars($s['buyer_info']) ?></p>
                            <?php if (!empty($s['buyer_phone'])): ?>
                            <p class="text-[10px]" style="color:var(--muted)">+<?= htmlspecialchars($s['buyer_phone']) ?></p>
                            <?php endif; ?>
                            <?php if (!empty($s['invoice_number'])): ?>
                            <p class="text-[9px] font-mono" style="color:var(--muted)"><?= htmlspecialchars($s['invoice_number']) ?></p>
                            <?php endif; ?>
                        </div>
                        <?php elseif ($s['status'] === 'sold'): ?>
                        <span class="text-[10px]" style="color:var(--muted)">—</span>
                        <?php else: ?>
                        <span class="text-[10px]" style="color:var(--muted)">—</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-[11px]" style="color:var(--muted)"><?= date('d M Y H:i', strtotime($s['created_at'])) ?></td>
                    <td style="text-align:right">
                        <?php if ($s['status']==='available'): ?>
                        <button onclick="confirmAction('Hapus stok ini?',function(){deleteStock(<?= $s['id'] ?>)})" class="btn btn-danger btn-sm"><i class="ri-delete-bin-6-fill"></i></button>
                        <?php else: ?>
                        <span class="text-[10px]" style="color:var(--muted)">—</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ═══ ADD STOCK MODAL ═══ -->
<div class="admin-modal" id="stockModal">
    <div class="admin-modal-content">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-base font-bold" style="color:var(--text)">Tambah Stok</h3>
            <button onclick="closeModal('stockModal')" class="w-8 h-8 rounded-lg flex items-center justify-center hover:bg-red-500/10" style="color:var(--muted)"><i class="ri-close-line text-lg"></i></button>
        </div>
        <form onsubmit="addStock(event)">
            <div class="space-y-4">
                <div>
                    <label class="block text-[11px] font-bold mb-1.5" style="color:var(--text2)">Pilih Produk *</label>
                    <select id="sProduct" class="admin-input admin-select" required>
                        <option value="">— Pilih —</option>
                        <?php foreach ($prodOpts as $po): ?>
                        <option value="<?= $po['id'] ?>"><?= htmlspecialchars($po['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="text-[11px] font-bold" style="color:var(--text2)">Data Akun *</label>
                        <button type="button" onclick="formatData()" class="text-[10px] font-bold text-accent hover:underline"><i class="ri-magic-fill mr-1"></i>Format Data</button>
                    </div>
                    <textarea id="sData" class="admin-input" rows="5" required placeholder="email@mail.com|Password123&#10;email2@mail.com|Password456&#10;(satu per baris untuk bulk add)"></textarea>
                    <div class="flex items-center justify-between mt-1">
                        <p class="text-[10px]" style="color:var(--muted)">Format: email|password — satu baris = satu akun</p>
                        <span class="text-[10px] font-bold" style="color:var(--muted)" id="lineCount">0 baris</span>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary w-full justify-center" style="padding:12px"><i class="ri-save-fill"></i> Simpan Stok</button>
            </div>
        </form>
    </div>
</div>

<script>
function applyFilter() {
    var pid = document.getElementById('filterProduct').value;
    var st = document.getElementById('filterStatus').value;
    var url = '?product=' + pid;
    if (st) url += '&status=' + st;
    location.href = url;
}

// Bulk formatter
function formatData() {
    var ta = document.getElementById('sData');
    var lines = ta.value.split('\n');
    var cleaned = [];
    for (var i = 0; i < lines.length; i++) {
        var line = lines[i].trim();
        if (!line) continue;
        line = line.replace(/\t/g, '|').replace(/;/g, '|').replace(/: /g, '|').replace(/:(?=[^\s])/g, '|');
        line = line.replace(/\|{2,}/g, '|');
        var parts = line.split('|').map(function(p){ return p.trim(); }).filter(function(p){ return p.length > 0; });
        cleaned.push(parts.join('|'));
    }
    ta.value = cleaned.join('\n');
    updateLineCount();
    showToast(cleaned.length + ' baris diformat!', 'success');
}

document.getElementById('sData').addEventListener('input', updateLineCount);
function updateLineCount() {
    var lines = document.getElementById('sData').value.split('\n').filter(function(l){ return l.trim().length > 0; });
    document.getElementById('lineCount').textContent = lines.length + ' baris';
}

function addStock(e) {
    e.preventDefault();
    adminAPI('stock_add', {
        product_id: document.getElementById('sProduct').value,
        account_data: document.getElementById('sData').value
    }, function(d) {
        if (d.ok) { showToast(d.msg); location.reload(); }
        else showToast(d.msg, 'error');
    });
}

function deleteStock(id) {
    adminAPI('stock_delete', { id: id }, function(d) {
        if (d.ok) { showToast(d.msg); location.reload(); }
        else showToast(d.msg, 'error');
    });
}

function copyText(t) {
    navigator.clipboard.writeText(t).then(function() { showToast('Copied!'); });
}
</script>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
