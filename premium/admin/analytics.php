<?php
/**
 * ADMIN/ANALYTICS.PHP — Advanced Analytics Dashboard with Chart.js
 * Revenue trends, product performance, user growth, top products
 */
$pageTitle = 'Analytics';
require_once __DIR__ . '/../includes/admin_header.php';

// ─── Fetch Analytics Data ───
// Monthly revenue (last 6 months)
$monthlyRevenue = [];
for ($i = 5; $i >= 0; $i--) {
    $month = date('Y-m', strtotime("-$i months"));
    $stmt = $conn->prepare("SELECT COALESCE(SUM(amount),0) as total FROM transactions WHERE status='SUCCESS' AND DATE_FORMAT(created_at,'%Y-%m')=?");
    $stmt->bind_param("s", $month); $stmt->execute();
    $r = $stmt->get_result()->fetch_assoc();
    $monthlyRevenue[] = ['month' => date('M Y', strtotime($month)), 'total' => (int)$r['total']];
    $stmt->close();
}

// Top 5 products
$topProducts = $conn->query("SELECT p.name, COUNT(t.id) as sales, SUM(t.amount) as revenue 
    FROM transactions t JOIN products p ON t.product_id=p.id 
    WHERE t.status='SUCCESS' GROUP BY t.product_id ORDER BY sales DESC LIMIT 5");

// User growth (last 6 months)
$userGrowth = [];
for ($i = 5; $i >= 0; $i--) {
    $month = date('Y-m', strtotime("-$i months"));
    $r = $conn->query("SELECT COUNT(*) as c FROM users WHERE DATE_FORMAT(created_at,'%Y-%m')='$month'")->fetch_assoc();
    $userGrowth[] = ['month' => date('M', strtotime($month)), 'count' => (int)$r['c']];
}

// Today stats
$todayRevenue = $conn->query("SELECT COALESCE(SUM(amount),0) as t FROM transactions WHERE status='SUCCESS' AND DATE(created_at)=CURDATE()")->fetch_assoc()['t'];
$todayOrders = $conn->query("SELECT COUNT(*) as c FROM transactions WHERE DATE(created_at)=CURDATE()")->fetch_assoc()['c'];
$todayUsers = $conn->query("SELECT COUNT(*) as c FROM users WHERE DATE(created_at)=CURDATE()")->fetch_assoc()['c'];
$totalSubscribers = $conn->query("SELECT COUNT(*) as c FROM newsletter_subscribers WHERE is_active=1")->fetch_assoc()['c'];

// Status distribution
$statusDist = $conn->query("SELECT status, COUNT(*) as c FROM transactions GROUP BY status");
$statuses = [];
while ($s = $statusDist->fetch_assoc()) { $statuses[$s['status']] = (int)$s['c']; }
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<div class="p-4 sm:p-6 lg:p-8">
    <div class="mb-6">
        <h1 class="text-xl font-black" style="color:var(--text)"><i class="ri-bar-chart-grouped-fill text-blue-500 mr-2"></i>Analytics Dashboard</h1>
        <p class="text-xs mt-1" style="color:var(--muted)">Insight performa bisnis secara real-time</p>
    </div>

    <!-- Today Stats -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="glass-strong rounded-2xl p-5">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 rounded-xl bg-green-500/10 flex items-center justify-center"><i class="ri-money-dollar-circle-fill text-green-500 text-xl"></i></div>
                <span class="text-[10px] font-semibold" style="color:var(--muted)">Revenue Hari Ini</span>
            </div>
            <p class="text-xl font-black text-green-500"><?= rupiah($todayRevenue) ?></p>
        </div>
        <div class="glass-strong rounded-2xl p-5">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 rounded-xl bg-blue-500/10 flex items-center justify-center"><i class="ri-shopping-bag-fill text-blue-400 text-xl"></i></div>
                <span class="text-[10px] font-semibold" style="color:var(--muted)">Order Hari Ini</span>
            </div>
            <p class="text-xl font-black" style="color:var(--text)"><?= $todayOrders ?></p>
        </div>
        <div class="glass-strong rounded-2xl p-5">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 rounded-xl bg-purple-500/10 flex items-center justify-center"><i class="ri-user-add-fill text-purple-400 text-xl"></i></div>
                <span class="text-[10px] font-semibold" style="color:var(--muted)">User Baru Hari Ini</span>
            </div>
            <p class="text-xl font-black" style="color:var(--text)"><?= $todayUsers ?></p>
        </div>
        <div class="glass-strong rounded-2xl p-5">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 rounded-xl bg-amber-500/10 flex items-center justify-center"><i class="ri-mail-star-fill text-amber-400 text-xl"></i></div>
                <span class="text-[10px] font-semibold" style="color:var(--muted)">Newsletter Subs</span>
            </div>
            <p class="text-xl font-black" style="color:var(--text)"><?= $totalSubscribers ?></p>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Revenue Chart -->
        <div class="glass-strong rounded-2xl p-5">
            <h3 class="text-sm font-bold mb-4" style="color:var(--text)"><i class="ri-line-chart-fill text-green-500 mr-1"></i> Revenue Trend (6 bulan)</h3>
            <canvas id="revenueChart" height="200"></canvas>
        </div>
        <!-- User Growth -->
        <div class="glass-strong rounded-2xl p-5">
            <h3 class="text-sm font-bold mb-4" style="color:var(--text)"><i class="ri-user-star-fill text-purple-400 mr-1"></i> User Growth (6 bulan)</h3>
            <canvas id="userChart" height="200"></canvas>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Top Products -->
        <div class="glass-strong rounded-2xl p-5">
            <h3 class="text-sm font-bold mb-4" style="color:var(--text)"><i class="ri-trophy-fill text-amber-400 mr-1"></i> Top 5 Produk</h3>
            <div class="space-y-3">
                <?php $rank=1; while($tp = $topProducts->fetch_assoc()): $medals=['🥇','🥈','🥉','4️⃣','5️⃣']; ?>
                <div class="flex items-center gap-3 p-3 rounded-xl" style="background:var(--surface)">
                    <span class="text-lg"><?= $medals[$rank-1] ?></span>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-bold truncate" style="color:var(--text)"><?= htmlspecialchars($tp['name']) ?></p>
                        <p class="text-[10px]" style="color:var(--muted)"><?= $tp['sales'] ?> sales</p>
                    </div>
                    <span class="text-xs font-bold text-green-500"><?= rupiah($tp['revenue']) ?></span>
                </div>
                <?php $rank++; endwhile; ?>
            </div>
        </div>
        <!-- Order Status Distribution -->
        <div class="glass-strong rounded-2xl p-5">
            <h3 class="text-sm font-bold mb-4" style="color:var(--text)"><i class="ri-pie-chart-fill text-blue-400 mr-1"></i> Status Pesanan</h3>
            <canvas id="statusChart" height="200"></canvas>
        </div>
    </div>
</div>

<script>
var isDark = document.documentElement.classList.contains('dark');
var textColor = isDark ? '#CBD5E1' : '#374151';
var gridColor = isDark ? 'rgba(148,163,184,0.1)' : 'rgba(0,0,0,0.05)';
Chart.defaults.color = textColor;
Chart.defaults.font.family = '"Plus Jakarta Sans", system-ui';

// Revenue Chart
new Chart(document.getElementById('revenueChart'), {
    type: 'line',
    data: {
        labels: <?= json_encode(array_column($monthlyRevenue, 'month')) ?>,
        datasets: [{
            label: 'Revenue',
            data: <?= json_encode(array_column($monthlyRevenue, 'total')) ?>,
            borderColor: '#22C55E',
            backgroundColor: isDark ? 'rgba(34,197,94,0.1)' : 'rgba(34,197,94,0.08)',
            fill: true, tension: 0.4, borderWidth: 2,
            pointBackgroundColor: '#22C55E', pointRadius: 4
        }]
    },
    options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { grid: { color: gridColor }, ticks: { callback: function(v){return 'Rp'+v.toLocaleString('id-ID')} } }, x: { grid: { display: false } } } }
});

// User Growth Chart
new Chart(document.getElementById('userChart'), {
    type: 'bar',
    data: {
        labels: <?= json_encode(array_column($userGrowth, 'month')) ?>,
        datasets: [{
            label: 'New Users',
            data: <?= json_encode(array_column($userGrowth, 'count')) ?>,
            backgroundColor: isDark ? 'rgba(167,139,250,0.4)' : 'rgba(167,139,250,0.6)',
            borderRadius: 8, borderSkipped: false
        }]
    },
    options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { grid: { color: gridColor }, beginAtZero: true }, x: { grid: { display: false } } } }
});

// Status Doughnut
new Chart(document.getElementById('statusChart'), {
    type: 'doughnut',
    data: {
        labels: <?= json_encode(array_keys($statuses)) ?>,
        datasets: [{
            data: <?= json_encode(array_values($statuses)) ?>,
            backgroundColor: ['#F59E0B','#3B82F6','#22C55E','#EF4444','#A855F7'],
            borderWidth: 0
        }]
    },
    options: { responsive: true, cutout: '65%', plugins: { legend: { position: 'bottom', labels: { padding: 12, usePointStyle: true, font: { size: 11 } } } } }
});
</script>

<?php include __DIR__ . '/../includes/admin_footer.php'; ?>
