<?php
/**
 * SERVICE_STATUS.PHP — Service Health Dashboard (#35)
 */
require_once __DIR__ . '/../includes/koneksi.php';
$pageTitle = 'Status Layanan — ' . SITE_NAME;
$pageDesc = 'Cek status terkini semua layanan Natsy Premiums.';

$services = $conn->query("SELECT * FROM service_status ORDER BY service_name");
include __DIR__ . '/../includes/header.php';

$statusColors = [
    'operational' => ['bg-green-500','text-green-500','Operasional'],
    'degraded' => ['bg-yellow-500','text-yellow-500','Terganggu'],
    'maintenance' => ['bg-blue-500','text-blue-500','Maintenance'],
    'outage' => ['bg-red-500','text-red-500','Gangguan']
];
$allOk = true;
$services->data_seek(0);
while($s = $services->fetch_assoc()) { if($s['status'] !== 'operational') $allOk = false; }
$services->data_seek(0);
?>

<section class="py-10 min-h-screen" style="background:var(--bg)">
    <div class="max-w-3xl mx-auto px-4 sm:px-6">
        <nav class="flex items-center gap-2 text-xs mb-6" style="color:var(--muted)">
            <a href="<?= BASE_URL ?>/index.php" class="hover:text-green-500">Home</a><i class="ri-arrow-right-s-line"></i>
            <span style="color:var(--text)">Status Layanan</span>
        </nav>

        <!-- Overall Status -->
        <div class="glass-strong rounded-2xl p-6 text-center mb-8" data-aos="fade-up">
            <?php if($allOk): ?>
            <div class="w-16 h-16 rounded-full bg-green-500/10 flex items-center justify-center mx-auto mb-3">
                <i class="ri-check-double-fill text-3xl text-green-500"></i>
            </div>
            <h1 class="text-xl font-black mb-1" style="color:var(--text)">Semua Sistem Normal ✅</h1>
            <p class="text-xs" style="color:var(--muted)">Semua layanan berjalan dengan baik</p>
            <?php else: ?>
            <div class="w-16 h-16 rounded-full bg-yellow-500/10 flex items-center justify-center mx-auto mb-3">
                <i class="ri-error-warning-fill text-3xl text-yellow-500"></i>
            </div>
            <h1 class="text-xl font-black mb-1" style="color:var(--text)">Ada Gangguan ⚠️</h1>
            <p class="text-xs" style="color:var(--muted)">Beberapa layanan sedang mengalami gangguan</p>
            <?php endif; ?>
        </div>

        <!-- Service List -->
        <div class="space-y-3">
            <?php while($s = $services->fetch_assoc()):
                $sc = $statusColors[$s['status']];
            ?>
            <div class="glass-strong rounded-xl p-4 flex items-center justify-between" data-aos="fade-up">
                <div class="flex items-center gap-3">
                    <span class="w-2.5 h-2.5 rounded-full <?= $sc[0] ?> <?= $s['status']==='operational' ? 'animate-pulse' : '' ?>"></span>
                    <span class="text-sm font-bold" style="color:var(--text)"><?= htmlspecialchars($s['service_name']) ?></span>
                </div>
                <div class="text-right">
                    <span class="text-xs font-bold <?= $sc[1] ?>"><?= $sc[2] ?></span>
                    <?php if($s['message'] && $s['status'] !== 'operational'): ?>
                    <p class="text-[10px] mt-0.5" style="color:var(--muted)"><?= htmlspecialchars($s['message']) ?></p>
                    <?php endif; ?>
                </div>
            </div>
            <?php endwhile; ?>
        </div>

        <div class="text-center mt-8 text-xs" style="color:var(--muted)">
            <p>Terakhir diperbarui: <?= date('d M Y, H:i') ?> WIB</p>
            <p class="mt-1">Ada masalah? <a href="https://wa.me/6281234567890" class="text-green-500 font-bold hover:underline">Hubungi Admin</a></p>
        </div>
    </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
