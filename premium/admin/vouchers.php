<?php
/**
 * ADMIN/VOUCHERS.PHP — Voucher/Promo Code Management
 */
$pageTitle = 'Voucher';
require_once __DIR__ . '/../includes/admin_header.php';

$vouchers = $conn->query("SELECT * FROM vouchers ORDER BY created_at DESC");
?>

<!-- Header -->
<div class="flex items-center justify-between mb-6 anim-up d1">
    <div>
        <h2 class="text-lg font-black" style="color:var(--text)">Manajemen Voucher</h2>
        <p class="text-xs" style="color:var(--muted)">Buat dan kelola kode promo / diskon</p>
    </div>
    <button onclick="openModal('voucherModal');resetForm()" class="btn btn-primary">
        <i class="ri-add-line"></i> Buat Voucher
    </button>
</div>

<!-- Voucher Table -->
<div class="admin-card anim-up d2">
    <div class="overflow-x-auto">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Diskon</th>
                    <th>Min. Belanja</th>
                    <th>Pemakaian</th>
                    <th>Berlaku Sampai</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($v = $vouchers->fetch_assoc()): ?>
                <tr>
                    <td><span class="font-mono text-xs font-bold" style="color:var(--accent)"><?= htmlspecialchars($v['code']) ?></span></td>
                    <td class="text-xs font-semibold">
                        <?php if ($v['discount_type'] === 'percent'): ?>
                            <span class="badge badge-info"><?= (int)$v['discount_value'] ?>%</span>
                        <?php else: ?>
                            <span class="badge badge-success"><?= rupiah($v['discount_value']) ?></span>
                        <?php endif; ?>
                    </td>
                    <td class="text-xs"><?= $v['min_purchase'] > 0 ? rupiah($v['min_purchase']) : '-' ?></td>
                    <td class="text-xs">
                        <?= $v['used_count'] ?> / <?= $v['usage_limit'] ?? '∞' ?>
                    </td>
                    <td class="text-xs" style="color:var(--muted)">
                        <?php if ($v['expires_at']): ?>
                            <?php $expired = strtotime($v['expires_at']) < time(); ?>
                            <span class="<?= $expired ? 'text-red-500' : '' ?>"><?= date('d M Y', strtotime($v['expires_at'])) ?></span>
                        <?php else: ?>
                            Selamanya
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php
                        $isExpired = $v['expires_at'] && strtotime($v['expires_at']) < time();
                        $isLimited = $v['usage_limit'] !== null && $v['used_count'] >= $v['usage_limit'];
                        if (!$v['is_active']): ?>
                            <span class="badge badge-muted">Nonaktif</span>
                        <?php elseif ($isExpired): ?>
                            <span class="badge badge-danger">Expired</span>
                        <?php elseif ($isLimited): ?>
                            <span class="badge badge-warning">Habis</span>
                        <?php else: ?>
                            <span class="badge badge-success">Aktif</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="flex gap-1">
                            <button onclick="editVoucher(<?= htmlspecialchars(json_encode($v)) ?>)" class="btn btn-outline btn-sm" title="Edit">
                                <i class="ri-edit-line"></i>
                            </button>
                            <button onclick="toggleVoucher(<?= $v['id'] ?>, <?= $v['is_active'] ? 0 : 1 ?>)" class="btn btn-outline btn-sm" title="Toggle">
                                <i class="ri-<?= $v['is_active'] ? 'eye-off-line' : 'eye-line' ?>"></i>
                            </button>
                            <button onclick="confirmAction('Hapus voucher ini?', function(){ deleteVoucher(<?= $v['id'] ?>) })" class="btn btn-danger btn-sm" title="Hapus">
                                <i class="ri-delete-bin-line"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Voucher Modal -->
<div class="admin-modal" id="voucherModal">
    <div class="admin-modal-content">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-base font-bold" style="color:var(--text)" id="modalTitle">Buat Voucher Baru</h3>
            <button onclick="closeModal('voucherModal')" class="w-8 h-8 rounded-lg flex items-center justify-center hover:bg-red-500/10 transition" style="color:var(--muted)">
                <i class="ri-close-line text-lg"></i>
            </button>
        </div>
        <form id="voucherForm" onsubmit="saveVoucher(event)">
            <input type="hidden" id="vId" value="0">
            <div class="space-y-4">
                <div>
                    <label class="text-xs font-bold mb-1 block" style="color:var(--text2)">Kode Voucher</label>
                    <input type="text" id="vCode" class="admin-input" placeholder="PROMO2024" style="text-transform:uppercase" required>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs font-bold mb-1 block" style="color:var(--text2)">Tipe Diskon</label>
                        <select id="vType" class="admin-input admin-select">
                            <option value="percent">Persen (%)</option>
                            <option value="fixed">Nominal (Rp)</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-bold mb-1 block" style="color:var(--text2)">Nilai Diskon</label>
                        <input type="number" id="vValue" class="admin-input" placeholder="10" step="0.01" min="0" required>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs font-bold mb-1 block" style="color:var(--text2)">Min. Pembelian</label>
                        <input type="number" id="vMin" class="admin-input" placeholder="0" value="0" min="0">
                    </div>
                    <div>
                        <label class="text-xs font-bold mb-1 block" style="color:var(--text2)">Batas Pemakaian</label>
                        <input type="number" id="vLimit" class="admin-input" placeholder="Kosongkan = unlimited" min="1">
                    </div>
                </div>
                <div>
                    <label class="text-xs font-bold mb-1 block" style="color:var(--text2)">Berlaku Sampai</label>
                    <input type="datetime-local" id="vExpires" class="admin-input">
                </div>
            </div>
            <div class="flex gap-3 mt-6">
                <button type="button" onclick="closeModal('voucherModal')" class="btn btn-outline flex-1">Batal</button>
                <button type="submit" class="btn btn-primary flex-1"><i class="ri-save-line"></i> Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
function resetForm(){
    document.getElementById('vId').value = 0;
    document.getElementById('vCode').value = '';
    document.getElementById('vType').value = 'percent';
    document.getElementById('vValue').value = '';
    document.getElementById('vMin').value = '0';
    document.getElementById('vLimit').value = '';
    document.getElementById('vExpires').value = '';
    document.getElementById('modalTitle').textContent = 'Buat Voucher Baru';
}
function editVoucher(v){
    document.getElementById('vId').value = v.id;
    document.getElementById('vCode').value = v.code;
    document.getElementById('vType').value = v.discount_type;
    document.getElementById('vValue').value = v.discount_value;
    document.getElementById('vMin').value = v.min_purchase;
    document.getElementById('vLimit').value = v.usage_limit || '';
    document.getElementById('vExpires').value = v.expires_at ? v.expires_at.replace(' ','T').substring(0,16) : '';
    document.getElementById('modalTitle').textContent = 'Edit Voucher';
    openModal('voucherModal');
}
function saveVoucher(e){
    e.preventDefault();
    var fd = new FormData();
    fd.append('action','voucher_save');
    fd.append('id', document.getElementById('vId').value);
    fd.append('code', document.getElementById('vCode').value);
    fd.append('discount_type', document.getElementById('vType').value);
    fd.append('discount_value', document.getElementById('vValue').value);
    fd.append('min_purchase', document.getElementById('vMin').value);
    fd.append('usage_limit', document.getElementById('vLimit').value);
    fd.append('expires_at', document.getElementById('vExpires').value);
    adminAPI('voucher_save', fd, function(r){
        showToast(r.msg, r.ok?'success':'error');
        if(r.ok){ closeModal('voucherModal'); setTimeout(function(){location.reload()},800); }
    });
}
function toggleVoucher(id, active){
    var fd = new FormData();
    fd.append('action','voucher_toggle');
    fd.append('id',id);
    fd.append('is_active',active);
    adminAPI('voucher_toggle', fd, function(r){
        showToast(r.msg, r.ok?'success':'error');
        if(r.ok) setTimeout(function(){location.reload()},600);
    });
}
function deleteVoucher(id){
    var fd = new FormData();
    fd.append('action','voucher_delete');
    fd.append('id',id);
    adminAPI('voucher_delete', fd, function(r){
        showToast(r.msg, r.ok?'success':'error');
        if(r.ok) setTimeout(function(){location.reload()},600);
    });
}
</script>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
