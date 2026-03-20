<?php
/**
 * ADMIN/SETTINGS.PHP — Theme Controller
 * Manage seasonal themes from admin panel
 */
$pageTitle = 'Pengaturan Tema';
require_once __DIR__ . '/../includes/admin_header.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['active_theme'])) {
    $theme = $_POST['active_theme'];
    $allowed = ['normal','auto','ramadan','lebaran','christmas','valentine','merdeka','galungan','newyear'];
    if (in_array($theme, $allowed)) {
        $stmt = $conn->prepare("INSERT INTO site_settings (setting_key, setting_value) VALUES ('active_theme', ?) ON DUPLICATE KEY UPDATE setting_value = ?");
        $stmt->bind_param("ss", $theme, $theme);
        $stmt->execute();
        $successMsg = 'Tema berhasil diubah ke: ' . ucfirst($theme);
    }
}

// Get current theme
$currentTheme = getSetting($conn, 'active_theme', 'auto');

$themes = [
    'auto' => ['label'=>'Otomatis (berdasarkan tanggal)', 'icon'=>'ri-magic-fill', 'color'=>'text-blue-500', 'desc'=>'Tema akan berubah otomatis sesuai hari raya', 'gradient'=>'from-blue-500/10 to-cyan-500/10'],
    'normal' => ['label'=>'Normal (Default)', 'icon'=>'ri-home-smile-2-fill', 'color'=>'text-green-500', 'desc'=>'Tema hijau standar tanpa dekorasi hari raya', 'gradient'=>'from-green-500/10 to-emerald-500/10'],
    'ramadan' => ['label'=>'Ramadhan', 'icon'=>'ri-moon-fill', 'color'=>'text-amber-500', 'desc'=>'Tema gold & amber untuk bulan Ramadhan', 'gradient'=>'from-amber-500/10 to-yellow-500/10', 'emoji'=>'🌙'],
    'lebaran' => ['label'=>'Lebaran / Idul Fitri', 'icon'=>'ri-gift-fill', 'color'=>'text-emerald-500', 'desc'=>'Tema hijau emas untuk Hari Raya Idul Fitri', 'gradient'=>'from-emerald-500/10 to-green-500/10', 'emoji'=>'🎉'],
    'christmas' => ['label'=>'Natal', 'icon'=>'ri-gift-2-fill', 'color'=>'text-red-500', 'desc'=>'Tema merah & hijau untuk perayaan Natal', 'gradient'=>'from-red-500/10 to-green-500/10', 'emoji'=>'🎄'],
    'valentine' => ['label'=>'Valentine', 'icon'=>'ri-hearts-fill', 'color'=>'text-pink-500', 'desc'=>'Tema pink & merah untuk Hari Valentine', 'gradient'=>'from-pink-500/10 to-rose-500/10', 'emoji'=>'💕'],
    'merdeka' => ['label'=>'Hari Kemerdekaan', 'icon'=>'ri-flag-fill', 'color'=>'text-red-600', 'desc'=>'Tema merah putih untuk 17 Agustus', 'gradient'=>'from-red-500/10 to-white/10', 'emoji'=>'🇮🇩'],
    'galungan' => ['label'=>'Galungan & Kuningan', 'icon'=>'ri-plant-fill', 'color'=>'text-yellow-600', 'desc'=>'Tema tradisional untuk Galungan & Kuningan', 'gradient'=>'from-yellow-500/10 to-orange-500/10', 'emoji'=>'🏵️'],
    'newyear' => ['label'=>'Tahun Baru', 'icon'=>'ri-sparkling-2-fill', 'color'=>'text-purple-500', 'desc'=>'Tema pesta untuk perayaan Tahun Baru', 'gradient'=>'from-purple-500/10 to-blue-500/10', 'emoji'=>'🎆'],
];
?>

<?php if (!empty($successMsg)): ?>
<div class="admin-card mb-6" style="border-color:rgba(34,197,94,0.3);background:rgba(34,197,94,0.06)">
    <div class="flex items-center gap-2">
        <i class="ri-checkbox-circle-fill text-green-500"></i>
        <span class="text-sm font-bold" style="color:var(--text)"><?= htmlspecialchars($successMsg) ?></span>
    </div>
</div>
<?php endif; ?>

<div class="admin-card mb-6 anim-up d1">
    <div class="flex items-center gap-3 mb-2">
        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-green-500/20 to-emerald-500/20 flex items-center justify-center">
            <i class="ri-palette-fill text-green-500 text-lg"></i>
        </div>
        <div>
            <h2 class="text-lg font-black" style="color:var(--text)">Pengaturan Tema Website</h2>
            <p class="text-[11px]" style="color:var(--muted)">Pilih tema yang aktif di halaman utama. Tema bisa di-toggle kapan saja.</p>
        </div>
    </div>
</div>

<form method="POST">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
        <?php foreach ($themes as $key => $t): ?>
        <label class="admin-card cursor-pointer transition-all hover:scale-[1.02] anim-up <?= $currentTheme === $key ? 'ring-2 ring-green-500' : '' ?>" style="<?= $currentTheme === $key ? 'border-color:rgba(34,197,94,0.5)' : '' ?>">
            <input type="radio" name="active_theme" value="<?= $key ?>" class="hidden" <?= $currentTheme === $key ? 'checked' : '' ?>>
            <div class="flex items-start gap-3">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br <?= $t['gradient'] ?> flex items-center justify-center shrink-0">
                    <i class="<?= $t['icon'] ?> <?= $t['color'] ?> text-xl"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-bold" style="color:var(--text)"><?= $t['label'] ?></span>
                        <?php if (isset($t['emoji'])): ?><span class="text-base"><?= $t['emoji'] ?></span><?php endif; ?>
                    </div>
                    <p class="text-[10px] mt-0.5" style="color:var(--muted)"><?= $t['desc'] ?></p>
                    <?php if ($currentTheme === $key): ?>
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-green-500/10 text-green-500 text-[9px] font-bold mt-2">
                        <i class="ri-checkbox-circle-fill"></i> Aktif
                    </span>
                    <?php endif; ?>
                </div>
            </div>
        </label>
        <?php endforeach; ?>
    </div>

    <div class="flex justify-end">
        <button type="submit" class="px-8 py-3 rounded-2xl bg-green-600 text-white text-sm font-bold hover:bg-green-700 transition-all flex items-center gap-2">
            <i class="ri-save-fill"></i> Simpan Tema
        </button>
    </div>
</form>

<script>
// Highlight selected theme card
document.querySelectorAll('input[name="active_theme"]').forEach(function(radio){
    radio.addEventListener('change', function(){
        document.querySelectorAll('input[name="active_theme"]').forEach(function(r){
            var card = r.closest('.admin-card');
            if(r.checked){
                card.classList.add('ring-2','ring-green-500');
                card.style.borderColor = 'rgba(34,197,94,0.5)';
            } else {
                card.classList.remove('ring-2','ring-green-500');
                card.style.borderColor = '';
            }
        });
    });
});
</script>

<?php include __DIR__ . '/../includes/admin_footer.php'; ?>
