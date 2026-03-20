<?php
/**
 * ADMIN_HEADER.PHP — Admin Dashboard Layout
 * Sidebar + Topbar + CSS + Role Check
 */
require_once __DIR__ . '/koneksi.php';

// Auth: must be logged in AND admin
if (!isLoggedIn() || (currentUser()['role'] ?? '') !== 'admin') {
    header('Location: ' . BASE_URL . '/index.php');
    exit;
}

$admin = currentUser();
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Admin Dashboard' ?> — <?= SITE_NAME ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.1.0/fonts/remixicon.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config={darkMode:'class',theme:{extend:{fontFamily:{sans:['"Plus Jakarta Sans"','system-ui','sans-serif']}}}}</script>
    <script>
        if(localStorage.getItem('theme')==='dark'||(!localStorage.getItem('theme')&&window.matchMedia('(prefers-color-scheme:dark)').matches)){
            document.documentElement.classList.add('dark');
        }
    </script>
    <style>
        *{font-family:'Plus Jakarta Sans',system-ui,sans-serif;margin:0;box-sizing:border-box}

        /* ─── CSS Variables ─── */
        :root {
            --bg: #F8FAF9; --bg2: #F0F4F2; --surface: #FFFFFF;
            --text: #111827; --text2: #374151; --muted: #9CA3AF;
            --border: rgba(0,0,0,0.06); --accent: #16A34A; --accent-glow: rgba(34,197,94,0.15);
            --sidebar-bg: #FFFFFF; --sidebar-w: 260px;
            --card-bg: rgba(255,255,255,0.85); --card-border: rgba(0,0,0,0.06);
        }
        .dark {
            --bg: #0B1120; --bg2: #111827; --surface: #1E293B;
            --text: #F1F5F9; --text2: #CBD5E1; --muted: #64748B;
            --border: rgba(255,255,255,0.06); --accent: #22C55E; --accent-glow: rgba(34,197,94,0.2);
            --sidebar-bg: #0F172A; --card-bg: rgba(30,41,59,0.7); --card-border: rgba(255,255,255,0.06);
        }

        /* ─── Layout ─── */
        .admin-wrapper { display: flex; min-height: 100vh; background: var(--bg); }
        .admin-sidebar {
            width: var(--sidebar-w); position: fixed; top: 0; left: 0; bottom: 0; z-index: 50;
            background: var(--sidebar-bg); border-right: 1px solid var(--border);
            display: flex; flex-direction: column; transition: transform 0.3s cubic-bezier(0.4,0,0.2,1);
        }
        .admin-main { flex: 1; margin-left: var(--sidebar-w); min-width: 0; transition: margin-left 0.3s; }
        .admin-topbar {
            position: sticky; top: 0; z-index: 40; height: 64px;
            background: var(--bg); border-bottom: 1px solid var(--border);
            display: flex; align-items: center; justify-content: space-between; padding: 0 24px;
            backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px);
        }

        /* ─── Sidebar Nav ─── */
        .nav-item {
            display: flex; align-items: center; gap: 12px; padding: 10px 16px; margin: 2px 12px;
            border-radius: 12px; font-size: 13px; font-weight: 500; color: var(--text2);
            transition: all 0.2s; text-decoration: none; position: relative;
        }
        .nav-item:hover { background: var(--accent-glow); color: var(--accent); }
        .nav-item.active {
            background: linear-gradient(135deg, rgba(34,197,94,0.12), rgba(16,185,129,0.08));
            color: var(--accent); font-weight: 700;
        }
        .nav-item.active::before {
            content: ''; position: absolute; left: 0; top: 50%; transform: translateY(-50%);
            width: 3px; height: 20px; background: var(--accent); border-radius: 0 4px 4px 0;
        }
        .nav-item i { font-size: 18px; width: 20px; text-align: center; }
        .nav-section { padding: 8px 24px 4px; font-size: 10px; font-weight: 800; letter-spacing: 1.5px; text-transform: uppercase; color: var(--muted); }

        /* ─── Cards ─── */
        .admin-card {
            background: var(--card-bg); border: 1px solid var(--card-border);
            border-radius: 16px; padding: 20px; transition: all 0.2s;
        }
        .admin-card:hover { border-color: var(--accent-glow); }
        .stat-card {
            background: var(--card-bg); border: 1px solid var(--card-border);
            border-radius: 20px; padding: 24px; position: relative; overflow: hidden;
        }
        .stat-card::after {
            content: ''; position: absolute; top: -30px; right: -30px;
            width: 100px; height: 100px; border-radius: 50%;
            background: var(--accent-glow); opacity: 0.5;
        }

        /* ─── Table ─── */
        .admin-table { width: 100%; border-collapse: separate; border-spacing: 0; }
        .admin-table th {
            padding: 12px 16px; text-align: left; font-size: 10px; font-weight: 800;
            letter-spacing: 1px; text-transform: uppercase; color: var(--muted);
            border-bottom: 1px solid var(--border); background: var(--bg2);
        }
        .admin-table th:first-child { border-radius: 12px 0 0 0; }
        .admin-table th:last-child { border-radius: 0 12px 0 0; }
        .admin-table td {
            padding: 14px 16px; font-size: 13px; color: var(--text2);
            border-bottom: 1px solid var(--border); vertical-align: middle;
        }
        .admin-table tr:hover td { background: var(--accent-glow); }
        .admin-table tr:last-child td { border-bottom: none; }

        /* ─── Badges ─── */
        .badge { display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; border-radius: 8px; font-size: 11px; font-weight: 700; }
        .badge-success { background: rgba(34,197,94,0.1); color: #16A34A; }
        .badge-warning { background: rgba(251,191,36,0.1); color: #D97706; }
        .badge-danger { background: rgba(239,68,68,0.1); color: #EF4444; }
        .badge-info { background: rgba(59,130,246,0.1); color: #3B82F6; }
        .badge-muted { background: var(--bg2); color: var(--muted); }

        /* ─── Buttons ─── */
        .btn { display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; border-radius: 12px; font-size: 12px; font-weight: 700; border: none; cursor: pointer; transition: all 0.2s; text-decoration: none; }
        .btn-primary { background: var(--accent); color: white; }
        .btn-primary:hover { background: #15803D; }
        .btn-outline { background: transparent; border: 1px solid var(--border); color: var(--text2); }
        .btn-outline:hover { border-color: var(--accent); color: var(--accent); }
        .btn-danger { background: rgba(239,68,68,0.08); color: #EF4444; }
        .btn-danger:hover { background: rgba(239,68,68,0.15); }
        .btn-sm { padding: 5px 10px; font-size: 11px; border-radius: 8px; }

        /* ─── Input ─── */
        .admin-input {
            width: 100%; padding: 10px 14px; border-radius: 12px; font-size: 13px;
            background: var(--bg); border: 1px solid var(--border); color: var(--text);
            outline: none; transition: all 0.2s;
        }
        .admin-input:focus { border-color: var(--accent); box-shadow: 0 0 0 3px var(--accent-glow); }
        .admin-input::placeholder { color: var(--muted); }
        .admin-select { appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%239CA3AF' viewBox='0 0 16 16'%3E%3Cpath d='M8 11L3 6h10l-5 5z'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 12px center; padding-right: 32px; }

        /* ─── Modal ─── */
        .admin-modal {
            position: fixed; inset: 0; z-index: 100; display: none; align-items: center; justify-content: center;
            background: rgba(0,0,0,0.5); backdrop-filter: blur(8px); padding: 16px;
        }
        .admin-modal.active { display: flex; }
        .admin-modal-content {
            background: var(--surface); border: 1px solid var(--border); border-radius: 24px;
            padding: 32px; width: 100%; max-width: 500px; max-height: 90vh; overflow-y: auto;
            box-shadow: 0 20px 60px rgba(0,0,0,0.2);
        }

        /* ─── Utility ─── */
        .text-accent { color: var(--accent); }
        .truncate-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        .animate-fade-in { animation: adminFadeIn 0.4s ease both; }
        @keyframes adminFadeIn { from{opacity:0;transform:translateY(12px)} to{opacity:1;transform:translateY(0)} }

        /* ─── Staggered Entrance Animations ─── */
        .anim-up { opacity:0; animation: animSlideUp 0.5s cubic-bezier(0.16,1,0.3,1) forwards; }
        .anim-scale { opacity:0; animation: animScaleIn 0.45s cubic-bezier(0.16,1,0.3,1) forwards; }
        .anim-fade { opacity:0; animation: animFadeIn 0.4s ease forwards; }
        @keyframes animSlideUp { from{opacity:0;transform:translateY(24px)} to{opacity:1;transform:translateY(0)} }
        @keyframes animScaleIn { from{opacity:0;transform:scale(0.92)} to{opacity:1;transform:scale(1)} }
        @keyframes animFadeIn { from{opacity:0} to{opacity:1} }
        .d1{animation-delay:.05s}.d2{animation-delay:.1s}.d3{animation-delay:.15s}
        .d4{animation-delay:.2s}.d5{animation-delay:.25s}.d6{animation-delay:.3s}
        .d7{animation-delay:.35s}.d8{animation-delay:.4s}.d9{animation-delay:.45s}.d10{animation-delay:.5s}
        @media(max-width:640px){
            .anim-up,.anim-scale,.anim-fade{animation-duration:0.3s}
            .d1,.d2,.d3,.d4,.d5,.d6,.d7,.d8,.d9,.d10{animation-delay:0s}
        }

        /* ─── Mobile ─── */
        .mobile-overlay { display: none; position: fixed; inset: 0; z-index: 45; background: rgba(0,0,0,0.4); }
        @media(max-width:1023px) {
            .admin-sidebar { transform: translateX(-100%); z-index: 50; }
            .admin-sidebar.open { transform: translateX(0); }
            .admin-main { margin-left: 0; }
            .mobile-overlay.active { display: block; }
        }

        /* ─── Chart bars ─── */
        .chart-bar { border-radius: 6px 6px 0 0; background: linear-gradient(180deg, var(--accent) 0%, rgba(34,197,94,0.4) 100%); transition: height 0.6s cubic-bezier(0.4,0,0.2,1); min-width: 32px; }
        .chart-bar:hover { opacity: 0.85; }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: var(--border); border-radius: 3px; }
    </style>
</head>
<body>

<!-- Mobile overlay -->
<div class="mobile-overlay" id="mobileOverlay" onclick="toggleSidebar()"></div>

<div class="admin-wrapper">
    <!-- ═══ SIDEBAR ═══ -->
    <aside class="admin-sidebar" id="adminSidebar">
        <!-- Logo -->
        <div style="padding:20px 20px 16px;border-bottom:1px solid var(--border)">
            <a href="<?= BASE_URL ?>/admin/" class="flex items-center gap-3 no-underline">
                <div class="w-10 h-10 rounded-xl bg-green-600 flex items-center justify-center text-white font-black text-sm shadow-lg shadow-green-600/30">N</div>
                <div>
                    <p class="text-sm font-black" style="color:var(--text)">Natsy</p>
                    <p class="text-[9px] font-bold tracking-widest uppercase" style="color:var(--accent)">ADMIN PANEL</p>
                </div>
            </a>
        </div>

        <!-- Nav -->
        <nav style="flex:1;padding:12px 0;overflow-y:auto">
            <div class="nav-section">Menu</div>
            <a href="<?= BASE_URL ?>/admin/" class="nav-item <?= $currentPage==='index'?'active':'' ?>">
                <i class="ri-dashboard-3-fill"></i> Overview
            </a>
            <a href="<?= BASE_URL ?>/admin/products.php" class="nav-item <?= $currentPage==='products'?'active':'' ?>">
                <i class="ri-shopping-bag-3-fill"></i> Produk
            </a>
            <a href="<?= BASE_URL ?>/admin/stocks.php" class="nav-item <?= $currentPage==='stocks'?'active':'' ?>">
                <i class="ri-archive-fill"></i> Stok
            </a>
            <a href="<?= BASE_URL ?>/admin/transactions.php" class="nav-item <?= $currentPage==='transactions'?'active':'' ?>">
                <i class="ri-exchange-funds-fill"></i> Transaksi
            </a>

            <a href="<?= BASE_URL ?>/admin/vouchers.php" class="nav-item <?= $currentPage==='vouchers'?'active':'' ?>">
                <i class="ri-coupon-3-fill"></i> Voucher
            </a>

            <div class="nav-section" style="margin-top:12px">Kelola</div>
            <a href="<?= BASE_URL ?>/admin/users.php" class="nav-item <?= $currentPage==='users'?'active':'' ?>">
                <i class="ri-group-fill"></i> Users
            </a>
            <a href="<?= BASE_URL ?>/admin/articles.php" class="nav-item <?= $currentPage==='articles'?'active':'' ?>">
                <i class="ri-article-fill"></i> Artikel
            </a>
            <a href="<?= BASE_URL ?>/admin/export.php" class="nav-item <?= $currentPage==='export'?'active':'' ?>">
                <i class="ri-file-chart-fill"></i> Laporan
            </a>
            <a href="<?= BASE_URL ?>/admin/analytics.php" class="nav-item <?= $currentPage==='analytics'?'active':'' ?>">
                <i class="ri-bar-chart-grouped-fill"></i> Analytics
            </a>
            <a href="<?= BASE_URL ?>/admin/broadcast.php" class="nav-item <?= $currentPage==='broadcast'?'active':'' ?>">
                <i class="ri-broadcast-fill"></i> Broadcast
            </a>
            <a href="<?= BASE_URL ?>/admin/logs.php" class="nav-item <?= $currentPage==='logs'?'active':'' ?>">
                <i class="ri-history-fill"></i> Log Aktivitas
            </a>
            <a href="<?= BASE_URL ?>/admin/settings.php" class="nav-item <?= $currentPage==='settings'?'active':'' ?>">
                <i class="ri-settings-3-fill"></i> Pengaturan Tema
            </a>
        </nav>

        <!-- Bottom -->
        <div style="padding:16px;border-top:1px solid var(--border)">
            <a href="<?= BASE_URL ?>/index.php" class="nav-item" style="margin:0 0 4px 0">
                <i class="ri-store-2-fill"></i> Lihat Toko
            </a>
            <a href="<?= BASE_URL ?>/auth/logout.php" class="nav-item" style="margin:0;color:#EF4444">
                <i class="ri-logout-box-r-fill"></i> Logout
            </a>
        </div>
    </aside>

    <!-- ═══ MAIN ═══ -->
    <div class="admin-main">
        <!-- Topbar -->
        <header class="admin-topbar">
            <div class="flex items-center gap-3">
                <button onclick="toggleSidebar()" class="lg:hidden w-9 h-9 rounded-xl flex items-center justify-center hover:bg-green-500/10 transition" style="color:var(--text)">
                    <i class="ri-menu-line text-lg"></i>
                </button>
                <h2 class="text-sm font-bold hidden sm:block" style="color:var(--text)"><?= $pageTitle ?? 'Dashboard' ?></h2>
            </div>
            <div class="flex items-center gap-2">
                <button id="adminThemeToggle" onclick="toggleAdminTheme()" class="w-9 h-9 rounded-xl flex items-center justify-center hover:bg-green-500/10 transition" style="color:var(--text2)" title="Toggle Dark Mode">
                    <i class="ri-moon-line text-lg" id="adminThemeIcon"></i>
                </button>
                <div class="flex items-center gap-2.5 px-3 py-1.5 rounded-xl" style="background:var(--bg2)">
                    <div class="w-7 h-7 rounded-lg bg-green-600 flex items-center justify-center text-white text-[10px] font-bold"><?= strtoupper(substr($admin['name'],0,1)) ?></div>
                    <span class="text-xs font-semibold hidden sm:inline" style="color:var(--text)"><?= htmlspecialchars($admin['name']) ?></span>
                </div>
            </div>
        </header>

        <!-- Content -->
        <main style="padding:24px" class="animate-fade-in">
