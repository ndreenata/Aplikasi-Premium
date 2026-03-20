<?php
/**
 * HEADER.PHP — Natsy Premiums v2
 * Dark/Light Mode · Glassmorphism · Micro-Animations · Floating WA · Live Sales
 * Font: Plus Jakarta Sans · Icons: RemixIcon · Framework: Tailwind CDN
 */
$_page = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? SITE_NAME ?></title>
    <meta name="description" content="<?= $pageDesc ?? 'Natsy Premiums — Akun premium digital terjangkau. Netflix, Spotify, Canva & lainnya. Auto-delivery via WhatsApp.' ?>">
    <!-- SEO Meta (#40) -->
    <meta property="og:title" content="<?= $pageTitle ?? SITE_NAME ?>">
    <meta property="og:description" content="<?= $pageDesc ?? 'Akun premium digital terjangkau. Auto-delivery via WhatsApp.' ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'] ?>">
    <meta property="og:image" content="<?= BASE_URL ?>/images/og-image.png">
    <meta property="og:site_name" content="Natsy Premiums">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="<?= 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'] ?>">
    <!-- PWA (#48) -->
    <link rel="manifest" href="<?= BASE_URL ?>/manifest.json">
    <meta name="theme-color" content="#16a34a">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.1.0/fonts/remixicon.css" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
    tailwind.config = {
        darkMode: 'class',
        theme: {
            extend: {
                fontFamily: { sans: ['"Plus Jakarta Sans"', 'system-ui', 'sans-serif'] },
            }
        }
    }
    </script>

    <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">
    <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>

    <style>
        * { font-family: 'Plus Jakarta Sans', system-ui, sans-serif; }

        /* ─── LIGHT MODE (default) ─── */
        :root {
            --bg: #F8FAF8;
            --bg2: #FFFFFF;
            --surface: #F1F5F1;
            --card: rgba(255,255,255,0.8);
            --card-border: rgba(22,163,74,0.12);
            --text: #111827;
            --text2: #374151;
            --muted: #9CA3AF;
            --accent: #16A34A;
            --accent2: #22C55E;
            --accent-glow: rgba(34,197,94,0.15);
            --neon: #22C55E;
            --border: #E5E7EB;
            --nav-bg: rgba(255,255,255,0.82);
            --nav-border: rgba(22,163,74,0.1);
        }

        /* ─── DARK MODE ─── */
        .dark {
            --bg: #0B1120;
            --bg2: #111827;
            --surface: #1E293B;
            --card: rgba(30,41,59,0.6);
            --card-border: rgba(34,197,94,0.2);
            --text: #F1F5F9;
            --text2: #CBD5E1;
            --muted: #64748B;
            --accent: #22C55E;
            --accent2: #4ADE80;
            --accent-glow: rgba(34,197,94,0.25);
            --neon: #4ADE80;
            --border: #1E293B;
            --nav-bg: rgba(11,17,32,0.85);
            --nav-border: rgba(34,197,94,0.15);
        }

        body {
            background: var(--bg);
            color: var(--text2);
        }
        .dark body, body.dark-body {
            background: var(--bg);
        }

        /* ─── GLASSMORPHISM ─── */
        .glass {
            background: var(--card);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--card-border);
        }
        .glass-strong {
            background: var(--card);
            backdrop-filter: blur(40px);
            -webkit-backdrop-filter: blur(40px);
            border: 1px solid var(--card-border);
            box-shadow: 0 8px 32px rgba(0,0,0,0.06);
        }

        /* ─── MICRO-ANIMATIONS ─── */
        .btn-press:active { transform: scale(0.96); }
        .btn-press { transition: all 0.2s cubic-bezier(0.4,0,0.2,1); }
        .hover-lift { transition: all 0.3s cubic-bezier(0.4,0,0.2,1); }
        .hover-lift:hover { transform: translateY(-6px); box-shadow: 0 12px 40px var(--accent-glow); border-color: var(--accent) !important; }

        /* Interactive card tilt on hover */
        .hover-tilt { transition: transform 0.2s ease, box-shadow 0.3s ease; perspective: 800px; }
        .hover-tilt:hover { box-shadow: 0 8px 30px var(--accent-glow); }

        /* Glass cards glow on hover */
        .glass-strong { transition: all 0.3s cubic-bezier(0.4,0,0.2,1); }
        .glass-strong:hover { border-color: rgba(34,197,94,0.2) !important; box-shadow: 0 4px 20px rgba(34,197,94,0.08); }

        /* ─── CARD FLIP 3D EFFECT ─── */
        .product-card {
            perspective: 1000px;
            transform-style: preserve-3d;
        }
        .product-card:hover {
            transform: translateY(-8px) rotateX(2deg) rotateY(-2deg);
            box-shadow: 0 20px 60px rgba(0,0,0,0.12), 0 0 30px var(--accent-glow);
        }
        .product-card .relative {
            transition: transform 0.4s cubic-bezier(0.4,0,0.2,1);
        }
        .product-card:hover .relative {
            transform: translateZ(20px);
        }

        /* ─── ADVANCED SEARCH PANEL ─── */
        .adv-search-panel {
            display: none;
            animation: panelSlide 0.3s ease;
        }
        .adv-search-panel.open { display: block; }
        @keyframes panelSlide { from { opacity:0; transform:translateY(-10px); } to { opacity:1; transform:translateY(0); } }
        .price-range-slider { -webkit-appearance: none; width: 100%; height: 6px; border-radius: 8px; background: linear-gradient(90deg, var(--accent), var(--accent2)); outline: none; }
        .price-range-slider::-webkit-slider-thumb { -webkit-appearance: none; width: 18px; height: 18px; border-radius: 50%; background: white; border: 3px solid var(--accent); cursor: pointer; box-shadow: 0 2px 8px rgba(0,0,0,0.15); }
        .price-range-slider::-moz-range-thumb { width: 18px; height: 18px; border-radius: 50%; background: white; border: 3px solid var(--accent); cursor: pointer; }

        /* Pulse glow for CTA */
        .pulse-glow {
            animation: pulseGlow 2.5s ease-in-out infinite;
        }
        @keyframes pulseGlow {
            0%,100% { box-shadow: 0 0 0 0 rgba(34,197,94,0.4); }
            50% { box-shadow: 0 0 20px 6px rgba(34,197,94,0.15); }
        }

        /* ─── HERO SLIDER ─── */
        .hero-track { display: flex; animation: heroSlide 18s ease-in-out infinite; }
        @keyframes heroSlide {
            0%,30%{transform:translateX(0)}
            33%,63%{transform:translateX(-100%)}
            66%,96%{transform:translateX(-200%)}
            100%{transform:translateX(0)}
        }
        .hero-dot.active { width:24px; border-radius:5px; background:var(--accent); }

        /* ─── MOBILE NAV ─── */
        .nav-mobile { max-height:0; overflow:hidden; transition:max-height 0.35s ease; }
        .nav-mobile.open { max-height:500px; }

        /* ─── MODAL ─── */
        .modal-overlay { display:none; }
        .modal-overlay.active { display:flex; }

        /* ─── PRODUCT CARD GLOW ─── */
        .card-glow:hover {
            border-color: var(--accent) !important;
            box-shadow: 0 0 30px var(--accent-glow), 0 8px 24px rgba(0,0,0,0.08);
        }

        /* ─── FLOATING WA ─── */
        .floating-wa {
            animation: floatWa 3s ease-in-out infinite;
        }
        @keyframes floatWa {
            0%,100% { transform: translateY(0); }
            50% { transform: translateY(-6px); }
        }
        .wa-ping {
            animation: waPing 1.5s ease-in-out infinite;
        }
        @keyframes waPing {
            0%,100% { box-shadow: 0 0 0 0 rgba(37,211,102,0.5); }
            50% { box-shadow: 0 0 0 12px rgba(37,211,102,0); }
        }

        /* ─── LIVE SALES POPUP ─── */
        .sales-popup {
            transform: translateX(-120%);
            transition: transform 0.5s cubic-bezier(0.4,0,0.2,1);
        }
        .sales-popup.show {
            transform: translateX(0);
        }

        /* ─── BADGE SHIMMER ─── */
        .badge-shimmer {
            background: linear-gradient(110deg, #EF4444 45%, #F87171 50%, #EF4444 55%);
            background-size: 200% 100%;
            animation: shimmer 2s linear infinite;
        }
        @keyframes shimmer {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }

        /* ─── TESTIMONIAL SLIDE ─── */
        .testi-track { transition: transform 0.5s ease; }

        /* ─── COUNTDOWN ─── */
        .countdown-num {
            font-variant-numeric: tabular-nums;
        }

        /* ─── NEON TEXT ─── */
        .dark .neon-text {
            color: #4ADE80;
            text-shadow: 0 0 10px rgba(74,222,128,0.3);
        }

        /* ─── SCROLLBAR ─── */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: var(--bg); }
        ::-webkit-scrollbar-thumb { background: var(--accent); border-radius: 3px; }

        /* ─── DECORATIVE BACKGROUND (GEN-Z ULTRA AESTHETIC v2) ─── */

        /* ─── ANIMATED MESH GRADIENT ON BODY ─── */
        body::before {
            content:''; position:fixed; inset:0; z-index:0; pointer-events:none;
            background:
                radial-gradient(ellipse 600px 400px at 15% 20%, rgba(34,197,94,0.08) 0%, transparent 70%),
                radial-gradient(ellipse 500px 500px at 85% 30%, rgba(6,182,212,0.06) 0%, transparent 70%),
                radial-gradient(ellipse 400px 600px at 50% 80%, rgba(167,139,250,0.05) 0%, transparent 70%),
                radial-gradient(ellipse 500px 300px at 70% 60%, rgba(236,72,153,0.04) 0%, transparent 70%);
            animation: meshShift 30s ease-in-out infinite alternate;
        }
        .dark body::before {
            background:
                radial-gradient(ellipse 600px 400px at 15% 20%, rgba(34,197,94,0.05) 0%, transparent 70%),
                radial-gradient(ellipse 500px 500px at 85% 30%, rgba(6,182,212,0.04) 0%, transparent 70%),
                radial-gradient(ellipse 400px 600px at 50% 80%, rgba(167,139,250,0.03) 0%, transparent 70%),
                radial-gradient(ellipse 500px 300px at 70% 60%, rgba(236,72,153,0.02) 0%, transparent 70%);
        }
        @keyframes meshShift {
            0% { background-position: 0% 0%; }
            100% { background-position: 5% 5%; }
        }

        /* ─── GRADIENT ORBS (sharper, less blur, more vivid) ─── */
        .decor-orb {
            position: fixed; border-radius: 50%; pointer-events: none; z-index: 0;
        }
        .decor-orb-1 { top: -10%; right: -8%; width: 700px; height: 700px; background: radial-gradient(circle, rgba(34,197,94,0.18) 0%, rgba(16,185,129,0.06) 40%, transparent 70%); opacity:0.7; animation: orbFloat1 18s ease-in-out infinite; }
        .decor-orb-2 { bottom: -15%; left: -10%; width: 800px; height: 800px; background: radial-gradient(circle, rgba(6,182,212,0.14) 0%, rgba(20,184,166,0.04) 40%, transparent 70%); opacity:0.6; animation: orbFloat2 24s ease-in-out infinite; }
        .decor-orb-3 { top: 20%; left: 50%; width: 550px; height: 550px; background: radial-gradient(circle, rgba(52,211,153,0.12) 0%, rgba(16,185,129,0.03) 40%, transparent 70%); opacity:0.5; animation: orbFloat3 16s ease-in-out infinite; }
        .decor-orb-4 { top: 50%; right: 2%; width: 500px; height: 500px; background: radial-gradient(circle, rgba(34,197,94,0.10) 0%, rgba(74,222,128,0.03) 40%, transparent 70%); opacity:0.45; animation: orbFloat2 20s ease-in-out infinite reverse; }
        .decor-orb-5 { top: 5%; left: 10%; width: 450px; height: 450px; background: radial-gradient(circle, rgba(20,184,166,0.12) 0%, rgba(6,182,212,0.03) 40%, transparent 70%); opacity:0.5; animation: orbFloat1 14s ease-in-out infinite reverse; }
        .decor-orb-6 { top: 38%; left: -8%; width: 400px; height: 400px; background: radial-gradient(circle, rgba(167,139,250,0.10) 0%, rgba(139,92,246,0.02) 40%, transparent 70%); opacity:0.4; animation: orbFloat3 22s ease-in-out infinite reverse; }
        .decor-orb-7 { bottom: 15%; right: 20%; width: 350px; height: 350px; background: radial-gradient(circle, rgba(251,191,36,0.08) 0%, rgba(245,158,11,0.02) 40%, transparent 70%); opacity:0.35; animation: orbFloat1 26s ease-in-out infinite; }
        .decor-orb-8 { top: 65%; left: 30%; width: 300px; height: 300px; background: radial-gradient(circle, rgba(236,72,153,0.07) 0%, rgba(244,114,182,0.01) 40%, transparent 70%); opacity:0.25; animation: orbFloat2 28s ease-in-out infinite; }

        .dark .decor-orb-1 { background: radial-gradient(circle, rgba(34,197,94,0.14) 0%, transparent 65%); opacity:0.5; }
        .dark .decor-orb-2 { background: radial-gradient(circle, rgba(6,182,212,0.10) 0%, transparent 65%); opacity:0.4; }
        .dark .decor-orb-3 { background: radial-gradient(circle, rgba(52,211,153,0.08) 0%, transparent 65%); opacity:0.35; }
        .dark .decor-orb-4 { background: radial-gradient(circle, rgba(34,197,94,0.07) 0%, transparent 65%); opacity:0.3; }
        .dark .decor-orb-5 { background: radial-gradient(circle, rgba(20,184,166,0.07) 0%, transparent 65%); opacity:0.3; }
        .dark .decor-orb-6 { background: radial-gradient(circle, rgba(167,139,250,0.06) 0%, transparent 65%); opacity:0.25; }
        .dark .decor-orb-7 { background: radial-gradient(circle, rgba(251,191,36,0.04) 0%, transparent 65%); opacity:0.2; }
        .dark .decor-orb-8 { background: radial-gradient(circle, rgba(236,72,153,0.04) 0%, transparent 65%); opacity:0.15; }

        @keyframes orbFloat1 { 0%,100%{transform:translate(0,0) scale(1)} 33%{transform:translate(-50px,60px) scale(1.08)} 66%{transform:translate(30px,-40px) scale(0.94)} }
        @keyframes orbFloat2 { 0%,100%{transform:translate(0,0) scale(1)} 50%{transform:translate(60px,-50px) scale(1.1)} }
        @keyframes orbFloat3 { 0%,100%{transform:translate(0,0)} 25%{transform:translate(-30px,45px)} 50%{transform:translate(20px,-25px)} 75%{transform:translate(40px,20px)} }

        /* ─── AURORA GRADIENT SWEEP ─── */
        .decor-aurora {
            position: fixed; top: 0; left: -25%; width: 150%; height: 55vh; pointer-events: none; z-index: 0;
            background: linear-gradient(135deg, rgba(34,197,94,0.08) 0%, rgba(6,182,212,0.06) 20%, rgba(167,139,250,0.05) 40%, rgba(236,72,153,0.03) 60%, rgba(52,211,153,0.06) 80%, transparent 100%);
            opacity: 0.7;
            animation: auroraShift 25s ease-in-out infinite alternate;
        }
        .dark .decor-aurora { opacity: 0.4; }
        @keyframes auroraShift { 0%{transform:translateX(-8%) rotate(-3deg)} 100%{transform:translateX(8%) rotate(3deg)} }

        /* ─── FLOATING ICON PARTICLES ─── */
        .decor-particle {
            position: fixed; pointer-events: none; z-index: 0;
            font-size: 20px; opacity: 0.12;
            animation-timing-function: ease-in-out;
            animation-iteration-count: infinite;
        }
        .decor-particle-1 { top: 12%; left: 8%; animation: particleFloat1 15s infinite; }
        .decor-particle-2 { top: 28%; right: 12%; animation: particleFloat2 18s infinite; animation-delay: -3s; }
        .decor-particle-3 { top: 45%; left: 20%; animation: particleFloat3 20s infinite; animation-delay: -6s; }
        .decor-particle-4 { top: 60%; right: 8%; animation: particleFloat1 16s infinite reverse; animation-delay: -2s; }
        .decor-particle-5 { top: 75%; left: 35%; animation: particleFloat2 22s infinite; animation-delay: -8s; }
        .decor-particle-6 { top: 18%; left: 55%; animation: particleFloat3 19s infinite reverse; animation-delay: -4s; }
        .decor-particle-7 { top: 85%; right: 25%; animation: particleFloat1 21s infinite; animation-delay: -10s; }
        .decor-particle-8 { top: 35%; left: 75%; animation: particleFloat2 17s infinite reverse; animation-delay: -5s; }
        .dark .decor-particle { opacity: 0.07; }
        @keyframes particleFloat1 { 0%,100%{transform:translate(0,0) rotate(0deg)} 25%{transform:translate(20px,-30px) rotate(90deg)} 50%{transform:translate(-15px,-60px) rotate(180deg)} 75%{transform:translate(25px,-20px) rotate(270deg)} }
        @keyframes particleFloat2 { 0%,100%{transform:translate(0,0) rotate(0deg)} 33%{transform:translate(-25px,35px) rotate(120deg)} 66%{transform:translate(30px,-25px) rotate(240deg)} }
        @keyframes particleFloat3 { 0%,100%{transform:translate(0,0) scale(1)} 50%{transform:translate(20px,-40px) scale(1.3)} }

        /* ─── SPARKLE / GLITTER DOTS ─── */
        .decor-sparkle {
            position: fixed; pointer-events: none; z-index: 0;
            width: 4px; height: 4px; border-radius: 50%;
            background: var(--accent2);
        }
        .decor-sparkle-1 { top: 15%; left: 25%; animation: sparkle 3s ease-in-out infinite; }
        .decor-sparkle-2 { top: 30%; right: 18%; animation: sparkle 4s ease-in-out infinite; animation-delay: -1s; }
        .decor-sparkle-3 { top: 50%; left: 40%; animation: sparkle 3.5s ease-in-out infinite; animation-delay: -0.5s; }
        .decor-sparkle-4 { top: 70%; right: 30%; animation: sparkle 2.8s ease-in-out infinite; animation-delay: -2s; }
        .decor-sparkle-5 { top: 22%; left: 65%; animation: sparkle 4.2s ease-in-out infinite; animation-delay: -1.5s; }
        .decor-sparkle-6 { top: 80%; left: 15%; animation: sparkle 3.8s ease-in-out infinite; animation-delay: -3s; }
        .decor-sparkle-7 { top: 42%; right: 10%; animation: sparkle 2.5s ease-in-out infinite; animation-delay: -0.8s; }
        .decor-sparkle-8 { top: 60%; left: 58%; animation: sparkle 3.2s ease-in-out infinite; animation-delay: -2.5s; }
        .decor-sparkle-9 { top: 8%; right: 40%; animation: sparkle 4.5s ease-in-out infinite; animation-delay: -1.2s; }
        .decor-sparkle-10 { top: 90%; left: 70%; animation: sparkle 3s ease-in-out infinite; animation-delay: -0.3s; }
        .dark .decor-sparkle { background: var(--neon); }
        @keyframes sparkle { 0%,100%{opacity:0; transform:scale(0)} 50%{opacity:0.6; transform:scale(1)} }

        /* ─── GEOMETRIC SHAPES ─── */
        .decor-geo {
            position: fixed; pointer-events: none; z-index: 0;
            border: 1.5px solid; border-radius: 4px;
            opacity: 0.08;
        }
        .decor-geo-1 { top: 20%; left: 5%; width: 40px; height: 40px; border-color: var(--accent); transform: rotate(15deg); animation: geoSpin 30s linear infinite; }
        .decor-geo-2 { top: 55%; right: 8%; width: 30px; height: 30px; border-color: rgba(6,182,212,0.5); border-radius: 50%; animation: geoSpin 25s linear infinite reverse; }
        .decor-geo-3 { top: 40%; left: 80%; width: 50px; height: 50px; border-color: rgba(167,139,250,0.4); transform: rotate(45deg); animation: geoSpin 35s linear infinite; }
        .decor-geo-4 { top: 75%; left: 12%; width: 25px; height: 25px; border-color: rgba(251,191,36,0.4); border-radius: 50%; animation: geoSpin 20s linear infinite reverse; }
        .decor-geo-5 { top: 10%; right: 30%; width: 35px; height: 35px; border-color: rgba(236,72,153,0.3); transform: rotate(30deg); animation: geoSpin 28s linear infinite; }
        .decor-geo-6 { top: 88%; left: 55%; width: 45px; height: 45px; border-color: var(--accent); border-radius: 12px; animation: geoSpin 32s linear infinite reverse; }
        .dark .decor-geo { opacity: 0.05; }
        @keyframes geoSpin { 0%{transform:rotate(0deg)} 100%{transform:rotate(360deg)} }

        /* ─── FLOWING WAVE RIBBONS (NEW) ─── */
        .decor-wave {
            position: fixed; pointer-events: none; z-index: 0; overflow: hidden;
        }
        .decor-wave-1 {
            top: 25%; left: -5%; width: 110%; height: 120px;
            background: linear-gradient(90deg, transparent 0%, rgba(34,197,94,0.06) 20%, rgba(6,182,212,0.08) 50%, rgba(167,139,250,0.05) 80%, transparent 100%);
            border-radius: 50%;
            transform: rotate(-3deg) scaleY(0.4);
            animation: waveFlow 20s ease-in-out infinite alternate;
        }
        .decor-wave-2 {
            top: 60%; left: -5%; width: 110%; height: 100px;
            background: linear-gradient(90deg, transparent 0%, rgba(236,72,153,0.04) 30%, rgba(251,191,36,0.06) 60%, rgba(34,197,94,0.05) 90%, transparent 100%);
            border-radius: 50%;
            transform: rotate(2deg) scaleY(0.35);
            animation: waveFlow 25s ease-in-out infinite alternate-reverse;
        }
        .decor-wave-3 {
            top: 85%; left: -5%; width: 110%; height: 80px;
            background: linear-gradient(90deg, transparent 0%, rgba(6,182,212,0.05) 25%, rgba(52,211,153,0.06) 55%, transparent 100%);
            border-radius: 50%;
            transform: rotate(-1.5deg) scaleY(0.3);
            animation: waveFlow 18s ease-in-out infinite alternate;
        }
        .dark .decor-wave { opacity: 0.4; }
        @keyframes waveFlow { 0%{transform:rotate(-3deg) scaleY(0.4) translateX(-3%)} 100%{transform:rotate(-3deg) scaleY(0.4) translateX(3%)} }

        /* ─── CONCENTRIC PULSE RINGS (NEW) ─── */
        .decor-ring {
            position: fixed; pointer-events: none; z-index: 0;
            border: 1px solid; border-radius: 50%;
            animation: ringPulse 8s ease-in-out infinite;
        }
        .decor-ring-1 { top: 15%; right: 10%; width: 200px; height: 200px; border-color: rgba(34,197,94,0.08); }
        .decor-ring-2 { top: 15%; right: 10%; width: 280px; height: 280px; border-color: rgba(34,197,94,0.05); animation-delay: -2s; margin-top: -40px; margin-right: -40px; }
        .decor-ring-3 { top: 15%; right: 10%; width: 360px; height: 360px; border-color: rgba(34,197,94,0.03); animation-delay: -4s; margin-top: -80px; margin-right: -80px; }
        .decor-ring-4 { bottom: 20%; left: 8%; width: 180px; height: 180px; border-color: rgba(167,139,250,0.07); animation-delay: -1s; }
        .decor-ring-5 { bottom: 20%; left: 8%; width: 260px; height: 260px; border-color: rgba(167,139,250,0.04); animation-delay: -3s; margin-bottom: -40px; margin-left: -40px; }
        .dark .decor-ring { opacity: 0.6; }
        @keyframes ringPulse { 0%,100%{transform:scale(1);opacity:1} 50%{transform:scale(1.06);opacity:0.5} }

        /* ─── CROSS / PLUS MARKERS (NEW) ─── */
        .decor-cross {
            position: fixed; pointer-events: none; z-index: 0;
            width: 16px; height: 16px; opacity: 0.06;
        }
        .decor-cross::before, .decor-cross::after {
            content:''; position:absolute; background: var(--accent);
        }
        .decor-cross::before { left:50%; top:0; width:2px; height:100%; transform:translateX(-50%); }
        .decor-cross::after { top:50%; left:0; width:100%; height:2px; transform:translateY(-50%); }
        .decor-cross-1 { top: 18%; left: 15%; animation: crossSpin 20s linear infinite; }
        .decor-cross-2 { top: 35%; right: 18%; animation: crossSpin 25s linear infinite reverse; width:12px; height:12px; }
        .decor-cross-3 { top: 55%; left: 45%; animation: crossSpin 30s linear infinite; width:10px; height:10px; }
        .decor-cross-4 { top: 72%; right: 25%; animation: crossSpin 22s linear infinite reverse; width:14px; height:14px; }
        .decor-cross-5 { top: 88%; left: 30%; animation: crossSpin 28s linear infinite; }
        .decor-cross-6 { top: 8%; left: 70%; animation: crossSpin 18s linear infinite reverse; width:10px; height:10px; }
        .decor-cross-7 { top: 45%; left: 6%; animation: crossSpin 24s linear infinite; width:12px; height:12px; }
        .decor-cross-8 { top: 65%; right: 8%; animation: crossSpin 32s linear infinite reverse; }
        .dark .decor-cross { opacity: 0.04; }
        .dark .decor-cross::before, .dark .decor-cross::after { background: var(--neon); }
        @keyframes crossSpin { 0%{transform:rotate(0deg)} 100%{transform:rotate(360deg)} }

        /* ─── DIAGONAL ACCENT CORNERS (NEW) ─── */
        .decor-diagonal-tl {
            position: fixed; top: -50px; left: -50px; width: 300px; height: 300px;
            pointer-events: none; z-index: 0;
            background: repeating-linear-gradient(45deg, transparent, transparent 8px, rgba(34,197,94,0.03) 8px, rgba(34,197,94,0.03) 9px);
            border-radius: 0 0 50% 0;
            opacity: 0.8;
        }
        .decor-diagonal-br {
            position: fixed; bottom: -50px; right: -50px; width: 300px; height: 300px;
            pointer-events: none; z-index: 0;
            background: repeating-linear-gradient(-45deg, transparent, transparent 8px, rgba(6,182,212,0.03) 8px, rgba(6,182,212,0.03) 9px);
            border-radius: 50% 0 0 0;
            opacity: 0.8;
        }
        .dark .decor-diagonal-tl, .dark .decor-diagonal-br { opacity: 0.4; }

        /* ─── IRIDESCENT SHIMMER OVERLAY (NEW) ─── */
        .decor-shimmer {
            position: fixed; inset: 0; pointer-events: none; z-index: 0;
            background: linear-gradient(
                105deg,
                transparent 40%,
                rgba(34,197,94,0.02) 45%,
                rgba(6,182,212,0.02) 48%,
                rgba(167,139,250,0.02) 51%,
                rgba(236,72,153,0.015) 54%,
                transparent 60%
            );
            background-size: 200% 100%;
            animation: iriShimmer 12s ease-in-out infinite;
        }
        .dark .decor-shimmer {
            background: linear-gradient(
                105deg,
                transparent 40%,
                rgba(34,197,94,0.01) 45%,
                rgba(6,182,212,0.01) 48%,
                rgba(167,139,250,0.01) 51%,
                transparent 60%
            );
            background-size: 200% 100%;
        }
        @keyframes iriShimmer { 0%{background-position:200% 0} 100%{background-position:-200% 0} }

        /* ─── MESH GRADIENT BANDS ─── */
        .decor-band {
            position: fixed; pointer-events: none; z-index: 0;
            height: 200px; width: 150%;
        }
        .decor-band-1 { top: 10%; left: -25%; background: linear-gradient(90deg, transparent, rgba(34,197,94,0.06), rgba(6,182,212,0.06), rgba(167,139,250,0.04), transparent); animation: bandDrift 40s ease-in-out infinite alternate; opacity: 0.7; }
        .decor-band-2 { top: 60%; left: -25%; background: linear-gradient(90deg, transparent, rgba(236,72,153,0.04), rgba(251,191,36,0.05), rgba(34,197,94,0.06), transparent); animation: bandDrift 35s ease-in-out infinite alternate-reverse; opacity: 0.5; }
        .dark .decor-band { opacity: 0.3 !important; }
        @keyframes bandDrift { 0%{transform:translateX(-10%)} 100%{transform:translateX(10%)} }

        /* ─── DOT GRID ─── */
        .decor-grid {
            position: fixed; inset: 0; z-index: 0; pointer-events: none;
            background-image: radial-gradient(circle, var(--card-border) 1px, transparent 1px);
            background-size: 36px 36px;
            opacity: 0.35;
            mask-image: radial-gradient(ellipse 80% 60% at 50% 40%, black 25%, transparent 100%);
            -webkit-mask-image: radial-gradient(ellipse 80% 60% at 50% 40%, black 25%, transparent 100%);
        }
        .dark .decor-grid { opacity: 0.12; }

        /* ─── NOISE TEXTURE ─── */
        .decor-noise {
            position: fixed; inset: 0; z-index: 0; pointer-events: none; opacity: 0.025;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)'/%3E%3C/svg%3E");
        }

        /* ─── HOLOGRAPHIC GRADIENT LINES ─── */
        .decor-line {
            position: fixed; pointer-events: none; z-index: 0;
        }
        .decor-line-1 { top: 18%; left: -5%; width: 110%; height: 2px; background: linear-gradient(90deg, transparent 0%, rgba(34,197,94,0.4) 15%, rgba(6,182,212,0.3) 35%, rgba(167,139,250,0.2) 55%, rgba(52,211,153,0.35) 75%, transparent 100%); transform: rotate(-2.5deg); opacity:0.25; animation: lineGlow 8s ease-in-out infinite alternate; }
        .decor-line-2 { top: 55%; left: -5%; width: 110%; height: 2px; background: linear-gradient(90deg, transparent 0%, rgba(52,211,153,0.3) 20%, rgba(34,197,94,0.4) 50%, rgba(6,182,212,0.25) 80%, transparent 100%); transform: rotate(1.5deg); opacity:0.2; animation: lineGlow 10s ease-in-out infinite alternate-reverse; }
        .decor-line-3 { top: 82%; left: -5%; width: 110%; height: 1.5px; background: linear-gradient(90deg, transparent 0%, rgba(167,139,250,0.25) 25%, rgba(34,197,94,0.25) 60%, transparent 100%); transform: rotate(-1deg); opacity:0.15; }
        .dark .decor-line { opacity: 0.08 !important; }
        @keyframes lineGlow { 0%{opacity:0.12;transform:rotate(-2.5deg) scaleX(0.97)} 100%{opacity:0.3;transform:rotate(-2.5deg) scaleX(1.03)} }

        /* ─── SECTION GLOW BORDER (NEW) ─── */
        .section-glow { position: relative; overflow: hidden; }
        .section-glow::before {
            content: ''; position: absolute; top: 0; left: -100%; width: 200%; height: 2px;
            background: linear-gradient(90deg, transparent, rgba(34,197,94,0.3), rgba(6,182,212,0.3), rgba(167,139,250,0.2), transparent);
            animation: sectionGlowSlide 8s ease-in-out infinite;
        }
        @keyframes sectionGlowSlide { 0%{transform:translateX(-30%)} 100%{transform:translateX(30%)} }

        @keyframes shimmerBanner { 0%{transform:translateX(-100%)} 50%{transform:translateX(100%)} 100%{transform:translateX(100%)} }
        @keyframes spinGlow { 0%{filter:blur(8px) hue-rotate(0deg)} 100%{filter:blur(8px) hue-rotate(360deg)} }

        /* ─── SKELETON LOADING ─── */
        .skeleton {
            background: linear-gradient(90deg, var(--surface) 25%, var(--card) 50%, var(--surface) 75%);
            background-size: 200% 100%;
            animation: skeletonPulse 1.5s ease-in-out infinite;
            border-radius: 12px;
        }
        .skeleton-text { height: 12px; margin-bottom: 8px; border-radius: 6px; }
        .skeleton-text.w-60 { width: 60%; }
        .skeleton-text.w-40 { width: 40%; }
        .skeleton-text.w-80 { width: 80%; }
        .skeleton-card { height: 200px; border-radius: 16px; }
        .skeleton-avatar { width: 40px; height: 40px; border-radius: 12px; }
        @keyframes skeletonPulse { 0% { background-position: 200% 0; } 100% { background-position: -200% 0; } }

        /* ─── GLOBAL LOADER OVERLAY (PREMIUM) ─── */
        #globalLoader {
            position: fixed; inset: 0; z-index: 9999;
            display: flex; align-items: center; justify-content: center; flex-direction: column;
            background: var(--bg);
            transition: opacity 0.6s cubic-bezier(0.4,0,0.2,1), visibility 0.6s;
        }
        #globalLoader.hidden { opacity: 0; visibility: hidden; pointer-events: none; }
        .loader-container { position: relative; width: 80px; height: 80px; margin-bottom: 20px; }
        .loader-logo {
            position: absolute; top: 50%; left: 50%; transform: translate(-50%,-50%);
            width: 44px; height: 44px; border-radius: 14px;
            background: linear-gradient(135deg, #22c55e 0%, #10b981 100%);
            display: flex; align-items: center; justify-content: center;
            color: white; font-size: 17px; font-weight: 900;
            animation: logoPulse 1.6s cubic-bezier(0.4,0,0.6,1) infinite;
            box-shadow: 0 8px 32px rgba(34,197,94,0.3);
        }
        .loader-ring {
            position: absolute; inset: 0;
            border: 2px solid transparent;
            border-top-color: var(--accent);
            border-right-color: rgba(34,197,94,0.3);
            border-radius: 50%;
            animation: loaderSpin 1s linear infinite;
        }
        .loader-ring-2 {
            position: absolute; inset: 4px;
            border: 1.5px solid transparent;
            border-bottom-color: rgba(6,182,212,0.5);
            border-left-color: rgba(6,182,212,0.15);
            border-radius: 50%;
            animation: loaderSpin 1.5s linear infinite reverse;
        }
        .loader-glow {
            position: absolute; inset: -8px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(34,197,94,0.08) 0%, transparent 70%);
            animation: loaderGlow 2s ease-in-out infinite;
        }
        @keyframes loaderSpin { 0%{transform:rotate(0deg)} 100%{transform:rotate(360deg)} }
        @keyframes logoPulse {
            0%, 100% { transform: translate(-50%,-50%) scale(1); box-shadow: 0 8px 32px rgba(34,197,94,0.25); }
            50% { transform: translate(-50%,-50%) scale(1.06); box-shadow: 0 12px 40px rgba(34,197,94,0.4); }
        }
        @keyframes loaderGlow {
            0%, 100% { opacity: 0.5; transform: scale(1); }
            50% { opacity: 1; transform: scale(1.15); }
        }
        .loader-progress {
            width: 120px; height: 3px; border-radius: 3px;
            background: var(--surface); overflow: hidden; margin-bottom: 10px;
        }
        .loader-progress-bar {
            height: 100%; border-radius: 3px;
            background: linear-gradient(90deg, var(--accent), #06b6d4, var(--accent));
            background-size: 200% 100%;
            animation: loaderProgress 1.5s ease-in-out infinite;
            width: 60%;
        }
        @keyframes loaderProgress { 0%{width:10%;background-position:0% 0%} 50%{width:80%;background-position:100% 0%} 100%{width:10%;background-position:0% 0%} }

        /* ─── PAGE ENTRANCE ANIMATIONS ─── */
        .anim-in {
            opacity: 0; transform: translateY(20px);
            animation: animSlideIn 0.6s cubic-bezier(0.16,1,0.3,1) forwards;
        }
        .anim-in.d1 { animation-delay: 0.05s; }
        .anim-in.d2 { animation-delay: 0.1s; }
        .anim-in.d3 { animation-delay: 0.15s; }
        .anim-in.d4 { animation-delay: 0.2s; }
        .anim-in.d5 { animation-delay: 0.25s; }
        @keyframes animSlideIn { to { opacity: 1; transform: translateY(0); } }

        .fade-in-up { opacity:0; transform:translateY(16px); transition:opacity 0.5s ease,transform 0.5s ease; }
        .fade-in-up.visible { opacity:1; transform:translateY(0); }

        /* ─── SCROLL PROGRESS BAR ─── */
        #scrollProgressBar {
            position:fixed; top:0; left:0; height:3px; z-index:9999;
            background:linear-gradient(90deg,#22C55E,#10B981,#06B6D4);
            width:0%; transition: width 0.1s linear;
            box-shadow: 0 0 10px rgba(34,197,94,0.4);
        }

        /* ─── NOTIFICATION DROPDOWN ─── */
        .notif-dropdown { display:none; position:absolute; top:calc(100% + 8px); right:0; width:320px; max-height:400px; overflow-y:auto; border-radius:16px; padding:8px; z-index:100; background:var(--card); backdrop-filter:blur(20px); border:1px solid var(--card-border); box-shadow:0 12px 40px rgba(0,0,0,0.15); }
        .notif-dropdown.open { display:block; animation: dropIn 0.2s ease-out; }
        @keyframes dropIn { from{opacity:0;transform:translateY(-8px)} to{opacity:1;transform:translateY(0)} }
        .notif-item { display:flex; gap:10px; padding:10px 12px; border-radius:12px; transition:background 0.15s; cursor:pointer; }
        .notif-item:hover { background:var(--surface); }
        .notif-item.unread { background:rgba(34,197,94,0.05); }

        /* ─── SEASONAL THEMES (FULL VISUAL OVERHAUL) ─── */

        /* RAMADAN 🌙 — Gold, Amber, Deep Emerald */
        .season-ramadan {
            --accent:#F59E0B; --accent2:#FBBF24; --neon:#FBBF24; --accent-glow:rgba(245,158,11,0.2);
            --bg: #FFFBEB; --surface: rgba(245,158,11,0.04); --card: rgba(255,251,235,0.7); --card-border: rgba(245,158,11,0.12);
        }
        .season-ramadan.dark { --bg:#1a1408; --surface:rgba(245,158,11,0.06); --card:rgba(30,25,10,0.6); --card-border:rgba(245,158,11,0.1); }
        .season-ramadan body::before { background: linear-gradient(135deg, rgba(245,158,11,0.06) 0%, rgba(217,119,6,0.04) 30%, transparent 60%) !important; }
        .season-ramadan .decor-orb-1 { background: radial-gradient(circle, rgba(245,158,11,0.25) 0%, transparent 65%) !important; }
        .season-ramadan .decor-orb-2 { background: radial-gradient(circle, rgba(217,119,6,0.2) 0%, transparent 65%) !important; }
        .season-ramadan .bg-green-600, .season-ramadan .bg-green-500 { background-color: #D97706 !important; }
        .season-ramadan .shadow-green-600\/20 { --tw-shadow-color: rgba(217,119,6,0.2) !important; }
        .season-ramadan .text-green-500 { color: #F59E0B !important; }
        .season-ramadan .text-green-600 { color: #D97706 !important; }
        .season-ramadan .glass-strong:hover { border-color: rgba(245,158,11,0.25) !important; box-shadow: 0 4px 20px rgba(245,158,11,0.1) !important; }

        /* CHRISTMAS 🎄 — Red, Green, Gold */
        .season-christmas {
            --accent:#EF4444; --accent2:#22C55E; --neon:#F87171; --accent-glow:rgba(239,68,68,0.2);
            --bg: #FEF2F2; --surface: rgba(239,68,68,0.04); --card: rgba(254,242,242,0.7); --card-border: rgba(239,68,68,0.12);
        }
        .season-christmas.dark { --bg:#1a0808; --surface:rgba(239,68,68,0.06); --card:rgba(30,10,10,0.6); --card-border:rgba(239,68,68,0.1); }
        .season-christmas .decor-orb-1 { background: radial-gradient(circle, rgba(239,68,68,0.2) 0%, transparent 65%) !important; }
        .season-christmas .decor-orb-2 { background: radial-gradient(circle, rgba(34,197,94,0.2) 0%, transparent 65%) !important; }
        .season-christmas .bg-green-600 { background-color: #EF4444 !important; }
        .season-christmas .text-green-500 { color: #EF4444 !important; }

        /* VALENTINE 💝 — Pink, Rose, Coral */
        .season-valentine {
            --accent:#EC4899; --accent2:#F472B6; --neon:#F472B6; --accent-glow:rgba(236,72,153,0.2);
            --bg: #FDF2F8; --surface: rgba(236,72,153,0.04); --card: rgba(253,242,248,0.7); --card-border: rgba(236,72,153,0.12);
        }
        .season-valentine.dark { --bg:#1a0812; --surface:rgba(236,72,153,0.06); --card:rgba(30,10,20,0.6); --card-border:rgba(236,72,153,0.1); }
        .season-valentine .bg-green-600 { background-color: #EC4899 !important; }
        .season-valentine .text-green-500 { color: #EC4899 !important; }

        /* MERDEKA 🇮🇩 — Red, White */
        .season-merdeka {
            --accent:#EF4444; --accent2:#FBBF24; --neon:#F87171; --accent-glow:rgba(239,68,68,0.2);
            --bg: #FFF5F5; --surface: rgba(239,68,68,0.04); --card: rgba(255,245,245,0.7); --card-border: rgba(239,68,68,0.1);
        }

        /* GALUNGAN 🌺 — Amber, Orange, Sacred Gold */
        .season-galungan {
            --accent:#D97706; --accent2:#F59E0B; --neon:#FBBF24; --accent-glow:rgba(217,119,6,0.2);
            --bg: #FFFBEB; --card: rgba(255,251,235,0.7); --card-border: rgba(217,119,6,0.1);
        }
        .season-galungan .bg-green-600 { background-color: #D97706 !important; }
        .season-galungan .text-green-500 { color: #D97706 !important; }

        /* LEBARAN 🎉 — Deep Green, Gold */
        .season-lebaran {
            --accent:#059669; --accent2:#10B981; --neon:#34D399; --accent-glow:rgba(5,150,105,0.2);
            --bg: #ECFDF5; --card: rgba(236,253,245,0.7); --card-border: rgba(5,150,105,0.1);
        }

        /* NEW YEAR 🎆 — Purple, Pink, Gold */
        .season-newyear {
            --accent:#8B5CF6; --accent2:#A78BFA; --neon:#C4B5FD; --accent-glow:rgba(139,92,246,0.2);
            --bg: #F5F3FF; --card: rgba(245,243,255,0.7); --card-border: rgba(139,92,246,0.1);
        }
        .season-newyear .bg-green-600 { background-color: #8B5CF6 !important; }
        .season-newyear .text-green-500 { color: #8B5CF6 !important; }

        /* Seasonal floating emojis */
        .season-emoji { position:fixed; z-index:1; pointer-events:none; font-size:18px; opacity:0; animation: seasonalFall linear forwards; }
        @keyframes seasonalFall {
            0% { opacity:0; transform: translateY(-20px) rotate(0deg); }
            10% { opacity:0.7; }
            90% { opacity:0.5; }
            100% { opacity:0; transform: translateY(100vh) rotate(360deg); }
        }

        /* Seasonal banner */
        .season-banner { display:none; position:relative; overflow:hidden; padding: 10px 16px; text-align:center; font-size:12px; font-weight:700; z-index:60; }
        .season-ramadan .season-banner, .season-christmas .season-banner, .season-valentine .season-banner,
        .season-merdeka .season-banner, .season-galungan .season-banner, .season-lebaran .season-banner, .season-newyear .season-banner { display:block; }
        .season-ramadan .season-banner { background:linear-gradient(135deg, #F59E0B, #92400E); color:white; }
        .season-christmas .season-banner { background:linear-gradient(135deg, #DC2626, #166534); color:white; }
        .season-valentine .season-banner { background:linear-gradient(135deg, #EC4899, #BE185D); color:white; }
        .season-merdeka .season-banner { background:linear-gradient(135deg, #EF4444, #FBBF24); color:white; }
        .season-galungan .season-banner { background:linear-gradient(135deg, #D97706, #92400E); color:white; }
        .season-lebaran .season-banner { background:linear-gradient(135deg, #059669, #065F46); color:white; }
        .season-newyear .season-banner { background:linear-gradient(135deg, #7C3AED, #EC4899); color:white; }
    </style>

    <script>
    // Dark mode init (before render to prevent flash)
    if (localStorage.getItem('theme')==='dark' || (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        document.documentElement.classList.add('dark');
    }
    </script>
</head>
<body class="antialiased <?= ($st = getSeasonalTheme()) ? 'season-'.$st : '' ?>">

<!-- Scroll Progress Bar -->
<div id="scrollProgressBar"></div>

<!-- ═══ SEASONAL BANNER ═══ -->
<?php
$seasonBanners = [
    'ramadan'   => ['🌙 Ramadhan Kareem! Diskon spesial Ramadhan sudah tersedia ✨', '🌙'],
    'lebaran'   => ['🎉 Selamat Hari Raya Idul Fitri! Mohon Maaf Lahir dan Batin 🕌', '🎉'],
    'christmas' => ['🎄 Merry Christmas! Nikmati promo spesial akhir tahun 🎅', '❄️'],
    'valentine' => ['💝 Happy Valentine\'s Day! Upgrade premium untuk orang tersayang 💕', '💖'],
    'merdeka'   => ['🇮🇩 Dirgahayu Indonesia! Promo kemerdekaan berlaku sekarang 🎆', '🎆'],
    'galungan'  => ['🙏 Rahajeng Rahina Galungan! Diskon spesial menanti 🌺', '🌺'],
    'newyear'   => ['🎆 Happy New Year! Tahun baru, gaya baru! ✨', '🎊'],
];
$currentSeason = getSeasonalTheme();
$bannerInfo = $seasonBanners[$currentSeason] ?? null;
$seasonEmoji = $bannerInfo[1] ?? '';
?>
<div class="season-banner" id="seasonBanner">
    <?php if ($bannerInfo): ?>
    <span><?= $bannerInfo[0] ?></span>
    <button onclick="this.parentElement.style.display='none'" class="absolute right-3 top-1/2 -translate-y-1/2 w-5 h-5 flex items-center justify-center rounded-full hover:bg-white/20 text-white/70 hover:text-white transition text-[10px]">✕</button>
    <?php endif; ?>
</div>

<!-- Seasonal Floating Emojis -->
<?php if ($currentSeason): ?>
<script>
(function(){
    var seasonEmojis = {
        'ramadan': ['🌙','⭐','🕌','✨','🌟'],
        'lebaran': ['🎉','🕌','✨','🎊','🌙'],
        'christmas': ['❄️','🎄','⭐','🎅','🔔'],
        'valentine': ['💖','💕','❤️','💗','🌹'],
        'merdeka': ['🎆','🇮🇩','⭐','🎊','✨'],
        'galungan': ['🌺','🙏','🌸','✨','🕉️'],
        'newyear': ['🎊','🎉','✨','🥂','⭐']
    };
    var emojis = seasonEmojis['<?= $currentSeason ?>'] || ['✨'];
    var maxEmojis = 12;
    function spawnEmoji() {
        if (document.querySelectorAll('.season-emoji').length >= maxEmojis) return;
        var el = document.createElement('div');
        el.className = 'season-emoji';
        el.textContent = emojis[Math.floor(Math.random() * emojis.length)];
        el.style.left = Math.random() * 100 + 'vw';
        el.style.animationDuration = (8 + Math.random() * 12) + 's';
        el.style.fontSize = (14 + Math.random() * 10) + 'px';
        document.body.appendChild(el);
        el.addEventListener('animationend', function(){ el.remove(); });
    }
    // Spawn a few initially
    for (var i = 0; i < 4; i++) setTimeout(spawnEmoji, i * 2000);
    // Then every 6-10 seconds
    setInterval(spawnEmoji, 6000 + Math.random() * 4000);
})();
</script>
<?php endif; ?>

<!-- ═══ DECORATIVE BACKGROUND (GEN-Z ULTRA) ═══ -->
<div class="decor-aurora"></div>
<div class="decor-band decor-band-1"></div>
<div class="decor-band decor-band-2"></div>
<div class="decor-orb decor-orb-1"></div>
<div class="decor-orb decor-orb-2"></div>
<div class="decor-orb decor-orb-3"></div>
<div class="decor-orb decor-orb-4"></div>
<div class="decor-orb decor-orb-5"></div>
<div class="decor-orb decor-orb-6"></div>
<div class="decor-orb decor-orb-7"></div>
<div class="decor-orb decor-orb-8"></div>
<!-- Floating icon particles -->
<div class="decor-particle decor-particle-1">🎬</div>
<div class="decor-particle decor-particle-2">🎵</div>
<div class="decor-particle decor-particle-3">🎨</div>
<div class="decor-particle decor-particle-4">💻</div>
<div class="decor-particle decor-particle-5">🚀</div>
<div class="decor-particle decor-particle-6">✨</div>
<div class="decor-particle decor-particle-7">🎧</div>
<div class="decor-particle decor-particle-8">📱</div>
<!-- Sparkle dots -->
<div class="decor-sparkle decor-sparkle-1"></div>
<div class="decor-sparkle decor-sparkle-2"></div>
<div class="decor-sparkle decor-sparkle-3"></div>
<div class="decor-sparkle decor-sparkle-4"></div>
<div class="decor-sparkle decor-sparkle-5"></div>
<div class="decor-sparkle decor-sparkle-6"></div>
<div class="decor-sparkle decor-sparkle-7"></div>
<div class="decor-sparkle decor-sparkle-8"></div>
<div class="decor-sparkle decor-sparkle-9"></div>
<div class="decor-sparkle decor-sparkle-10"></div>
<!-- Geometric shapes -->
<div class="decor-geo decor-geo-1"></div>
<div class="decor-geo decor-geo-2"></div>
<div class="decor-geo decor-geo-3"></div>
<div class="decor-geo decor-geo-4"></div>
<div class="decor-geo decor-geo-5"></div>
<div class="decor-geo decor-geo-6"></div>
<!-- Grid + noise -->
<div class="decor-line decor-line-1"></div>
<div class="decor-line decor-line-2"></div>
<div class="decor-line decor-line-3"></div>
<div class="decor-grid"></div>
<div class="decor-noise"></div>
<!-- Wave ribbons -->
<div class="decor-wave decor-wave-1"></div>
<div class="decor-wave decor-wave-2"></div>
<div class="decor-wave decor-wave-3"></div>
<!-- Concentric pulse rings -->
<div class="decor-ring decor-ring-1"></div>
<div class="decor-ring decor-ring-2"></div>
<div class="decor-ring decor-ring-3"></div>
<div class="decor-ring decor-ring-4"></div>
<div class="decor-ring decor-ring-5"></div>
<!-- Cross markers -->
<div class="decor-cross decor-cross-1"></div>
<div class="decor-cross decor-cross-2"></div>
<div class="decor-cross decor-cross-3"></div>
<div class="decor-cross decor-cross-4"></div>
<div class="decor-cross decor-cross-5"></div>
<div class="decor-cross decor-cross-6"></div>
<div class="decor-cross decor-cross-7"></div>
<div class="decor-cross decor-cross-8"></div>
<!-- Diagonal accent corners -->
<div class="decor-diagonal-tl"></div>
<div class="decor-diagonal-br"></div>
<!-- Iridescent shimmer -->
<div class="decor-shimmer"></div>


<!-- ═══ NAVBAR ═══ -->
<nav class="fixed top-0 left-0 right-0 z-50 backdrop-blur-xl" style="background:var(--nav-bg);border-bottom:1px solid var(--nav-border)">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
        <!-- Logo -->
        <a href="<?= BASE_URL ?>/index.php" class="flex items-center gap-2.5 group">
            <div class="w-9 h-9 rounded-xl bg-green-600 flex items-center justify-center text-white text-xs font-black shadow-lg shadow-green-600/20 group-hover:scale-110 transition-transform">N</div>
            <div class="flex flex-col leading-none">
                <span class="text-sm font-extrabold" style="color:var(--text)">Natsy</span>
                <span class="text-[9px] font-semibold text-green-500 tracking-widest uppercase">Premiums</span>
            </div>
        </a>

        <!-- Desktop Nav -->
        <div class="hidden md:flex items-center gap-1">
            <a href="<?= BASE_URL ?>/index.php" class="px-4 py-2 rounded-xl text-sm font-medium transition-all <?= $_page==='index' ? 'text-green-500 bg-green-500/10' : '' ?>" style="color:<?= $_page==='index'?'var(--accent)':'var(--text2)' ?>">
                <i class="ri-home-5-line mr-1"></i><span data-i18n="nav-home">Home</span>
            </a>
            <a href="<?= BASE_URL ?>/index.php#products" class="px-4 py-2 rounded-xl text-sm font-medium hover:text-green-500 transition-all" style="color:var(--text2)">
                <i class="ri-store-2-line mr-1"></i><span data-i18n="nav-produk">Produk</span>
            </a>
            <a href="<?= BASE_URL ?>/index.php#cara-order" class="px-4 py-2 rounded-xl text-sm font-medium hover:text-green-500 transition-all" style="color:var(--text2)">
                <i class="ri-route-line mr-1"></i><span data-i18n="nav-cara">Cara Order</span>
            </a>
            <a href="<?= BASE_URL ?>/pages/article.php" class="px-4 py-2 rounded-xl text-sm font-medium hover:text-green-500 transition-all" style="color:var(--text2)">
                <i class="ri-article-line mr-1"></i><span data-i18n="nav-artikel">Artikel</span>
            </a>
            <a href="<?= BASE_URL ?>/index.php#faq" class="px-4 py-2 rounded-xl text-sm font-medium hover:text-green-500 transition-all" style="color:var(--text2)">
                <i class="ri-question-line mr-1"></i><span data-i18n="nav-faq">FAQ</span>
            </a>
            <?php if (isLoggedIn()): ?>
            <a href="<?= BASE_URL ?>/pages/user_dashboard.php" class="px-4 py-2 rounded-xl text-sm font-medium transition-all <?= $_page==='user_dashboard' ? 'text-green-500 bg-green-500/10' : '' ?>" style="color:<?= $_page==='user_dashboard'?'var(--accent)':'var(--text2)' ?>">
                <i class="ri-file-list-3-line mr-1"></i>Pesanan
            </a>
            <?php endif; ?>
        </div>

        <!-- Right side -->
        <div class="flex items-center gap-2">
            <!-- Admin link -->
            <?php if (isLoggedIn() && (currentUser()['role'] ?? '') === 'admin'): ?>
            <a href="<?= BASE_URL ?>/admin/" class="w-9 h-9 rounded-xl flex items-center justify-center transition-all hover:bg-purple-500/10 text-purple-500" title="Admin Dashboard">
                <i class="ri-shield-star-fill text-lg"></i>
            </a>
            <?php endif; ?>
            <!-- Cart icon -->
            <?php $cartCount = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0; ?>
            <a href="<?= BASE_URL ?>/store/cart.php" class="relative w-9 h-9 rounded-xl flex items-center justify-center transition-all hover:bg-green-500/10" style="color:var(--text2)" title="Keranjang">
                <i class="ri-shopping-cart-2-line text-lg"></i>
                <?php if ($cartCount > 0): ?>
                <span class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center shadow-lg" id="cartBadge"><?= $cartCount ?></span>
                <?php else: ?>
                <span class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center shadow-lg hidden" id="cartBadge">0</span>
                <?php endif; ?>
            </a>
            <!-- Wishlist icon -->
            <a href="<?= BASE_URL ?>/store/wishlist.php" class="relative w-9 h-9 rounded-xl flex items-center justify-center transition-all hover:bg-pink-500/10" style="color:var(--text2)" title="Wishlist">
                <i class="ri-heart-line text-lg"></i>
                <span class="absolute -top-1 -right-1 w-5 h-5 bg-pink-500 text-white text-[10px] font-bold rounded-full items-center justify-center shadow-lg hidden" id="wishlistBadge" style="display:none">0</span>
            </a>
            <!-- Notification Bell -->
            <?php if (isLoggedIn()): $notifCount = getUnreadNotifCount($conn, $_SESSION['user_id']); ?>
            <div class="relative">
                <button onclick="toggleNotifs()" class="w-9 h-9 rounded-xl flex items-center justify-center transition-all hover:bg-amber-500/10" style="color:var(--text2)" title="Notifikasi" id="notifBtn">
                    <i class="ri-notification-3-line text-lg"></i>
                    <?php if ($notifCount > 0): ?>
                    <span class="absolute -top-1 -right-1 w-5 h-5 bg-amber-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center shadow-lg animate-pulse"><?= $notifCount > 9 ? '9+' : $notifCount ?></span>
                    <?php endif; ?>
                </button>
                <div id="notifDropdown" class="notif-dropdown">
                    <div class="flex items-center justify-between px-3 py-2 mb-1">
                        <span class="text-xs font-black" style="color:var(--text)">Notifikasi</span>
                        <button onclick="markAllRead()" class="text-[10px] font-semibold text-green-500 hover:underline">Tandai semua dibaca</button>
                    </div>
                    <div id="notifList">
                        <div class="text-center py-6"><i class="ri-notification-off-line text-2xl" style="color:var(--muted)"></i><p class="text-xs mt-2" style="color:var(--muted)">Belum ada notifikasi</p></div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            <!-- Language toggle -->
            <button id="langToggle" class="w-9 h-9 rounded-xl flex items-center justify-center transition-all hover:bg-blue-500/10 text-[10px] font-black" style="color:var(--text2)" title="Switch Language">
                <span id="langLabel">ID</span>
            </button>
            <!-- Dark mode toggle -->
            <button id="themeToggle" class="w-9 h-9 rounded-xl flex items-center justify-center transition-all hover:bg-green-500/10" style="color:var(--text2)" title="Toggle Dark Mode">
                <i class="ri-moon-line text-lg" id="themeIcon"></i>
            </button>

            <?php if (isLoggedIn()): $u = currentUser(); ?>
                <a href="<?= BASE_URL ?>/pages/profile.php" class="hidden sm:flex items-center gap-2 px-3 py-1.5 rounded-xl glass hover:border-green-500/30 transition-all" title="Profil Saya">
                    <div class="w-7 h-7 rounded-lg bg-green-600 flex items-center justify-center text-white text-[10px] font-bold"><?= strtoupper(substr($u['name'],0,1)) ?></div>
                    <span class="text-xs font-semibold max-w-[80px] truncate" style="color:var(--text)"><?= htmlspecialchars($u['name']) ?></span>
                </a>
                <a href="<?= BASE_URL ?>/auth/logout.php" class="p-2 rounded-lg hover:bg-red-500/10 hover:text-red-400 transition-all" style="color:var(--muted)" title="Logout"><i class="ri-logout-box-r-line text-lg"></i></a>
            <?php else: ?>
                <a href="<?= BASE_URL ?>/auth/login.php" class="hidden sm:inline-flex items-center gap-1.5 px-5 py-2.5 bg-green-600 text-white text-xs font-bold rounded-xl hover:bg-green-500 btn-press shadow-lg shadow-green-600/20 pulse-glow"><i class="ri-user-line"></i> Masuk</a>
                <a href="<?= BASE_URL ?>/auth/login.php" class="sm:hidden p-2 rounded-lg text-green-500 hover:bg-green-500/10 transition-all"><i class="ri-user-line text-lg"></i></a>
            <?php endif; ?>
            <button class="md:hidden p-2 rounded-lg hover:bg-green-500/10 transition-all" style="color:var(--text2)" id="hamburger"><i class="ri-menu-4-line text-xl" id="hamburgerIcon"></i></button>
        </div>
    </div>

    <!-- Mobile Nav -->
    <div class="nav-mobile md:hidden" style="background:var(--bg2);border-top:1px solid var(--border)" id="mobileNav">
        <div class="px-4 py-3 space-y-1">
            <a href="<?= BASE_URL ?>/index.php" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium hover:bg-green-500/10 transition-all" style="color:var(--text2)"><i class="ri-home-5-line text-lg text-green-500"></i>Home</a>
            <a href="<?= BASE_URL ?>/index.php#products" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium hover:bg-green-500/10 transition-all" style="color:var(--text2)"><i class="ri-store-2-line text-lg text-green-500"></i>Produk</a>
            <a href="<?= BASE_URL ?>/index.php#cara-order" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium hover:bg-green-500/10 transition-all" style="color:var(--text2)"><i class="ri-route-line text-lg text-green-500"></i>Cara Order</a>
            <a href="<?= BASE_URL ?>/pages/article.php" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium hover:bg-green-500/10 transition-all" style="color:var(--text2)"><i class="ri-article-line text-lg text-green-500"></i>Artikel</a>
            <a href="<?= BASE_URL ?>/index.php#faq" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium hover:bg-green-500/10 transition-all" style="color:var(--text2)"><i class="ri-question-line text-lg text-green-500"></i>FAQ</a>
            <?php if (isLoggedIn()): ?>
            <a href="<?= BASE_URL ?>/pages/profile.php" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium hover:bg-green-500/10 transition-all" style="color:var(--text2)"><i class="ri-user-3-line text-lg text-green-500"></i>Profil Saya</a>
            <a href="<?= BASE_URL ?>/pages/user_dashboard.php" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium hover:bg-green-500/10 transition-all" style="color:var(--text2)"><i class="ri-file-list-3-line text-lg text-green-500"></i>Pesanan</a>
            <div style="border-top:1px solid var(--border);margin:4px 0"></div>
            <a href="<?= BASE_URL ?>/auth/logout.php" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-red-400 hover:bg-red-500/10 transition-all"><i class="ri-logout-box-r-line text-lg"></i>Logout</a>
            <?php else: ?>
            <div style="border-top:1px solid var(--border);margin:4px 0"></div>
            <a href="<?= BASE_URL ?>/auth/login.php" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-green-500 hover:bg-green-500/10 transition-all"><i class="ri-login-box-line text-lg"></i>Masuk</a>
            <a href="<?= BASE_URL ?>/auth/register.php" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-green-500 hover:bg-green-500/10 transition-all"><i class="ri-user-add-line text-lg"></i>Daftar</a>
            <?php endif; ?>
        </div>
    </div>
</nav>
<div class="h-16"></div>

<!-- ═══ GLOBAL LOADER (PREMIUM) ═══ -->
<div id="globalLoader">
    <div class="loader-container">
        <div class="loader-glow"></div>
        <div class="loader-ring"></div>
        <div class="loader-ring-2"></div>
        <div class="loader-logo">N</div>
    </div>
    <div class="loader-progress"><div class="loader-progress-bar"></div></div>
    <p class="text-[9px] font-bold tracking-[0.2em] uppercase" style="color:var(--muted)">Loading</p>
</div>

<!-- ═══ FLOATING WHATSAPP ═══ -->
<a href="https://wa.me/6281234567890" target="_blank" class="fixed bottom-6 right-6 z-50 w-14 h-14 bg-[#25D366] rounded-2xl flex items-center justify-center text-white shadow-2xl floating-wa wa-ping hover:scale-110 transition-transform" title="Chat Admin via WhatsApp">
    <i class="ri-whatsapp-line text-2xl"></i>
</a>

<!-- ═══ LIVE SALES NOTIFICATION ═══ -->
<div id="salesPopup" class="sales-popup fixed bottom-6 left-4 sm:left-6 z-40 glass rounded-2xl p-3 pr-5 flex items-center gap-3 max-w-xs shadow-xl cursor-pointer" onclick="this.classList.remove('show')">
    <div class="w-10 h-10 rounded-xl bg-green-500/15 flex items-center justify-center shrink-0">
        <i class="ri-shopping-bag-3-fill text-green-500"></i>
    </div>
    <div>
        <p class="text-xs font-bold" style="color:var(--text)" id="salesName">Someone</p>
        <p class="text-[10px]" style="color:var(--muted)" id="salesProduct">baru saja membeli Netflix Premium</p>
        <p class="text-[9px] text-green-500 font-semibold mt-0.5" id="salesTime">2 menit lalu</p>
    </div>
    <button class="absolute top-2 right-2 text-[10px]" style="color:var(--muted)" onclick="event.stopPropagation();document.getElementById('salesPopup').classList.remove('show')"><i class="ri-close-line"></i></button>
</div>

<script>
// ─── Hamburger ───
(function(){
    var hBtn=document.getElementById('hamburger'),hNav=document.getElementById('mobileNav'),hIcon=document.getElementById('hamburgerIcon');
    if(hBtn && hNav && hIcon) {
        hBtn.addEventListener('click',function(){var o=hNav.classList.toggle('open');hIcon.className=o?'ri-close-line text-xl':'ri-menu-4-line text-xl';});
    }
})();

// ─── Dark Mode Toggle (Bulletproof v3) ───
(function(){
    var themeBtn=document.getElementById('themeToggle');
    var themeIcon=document.getElementById('themeIcon');
    if(!themeBtn || !themeIcon) { console.warn('[DARK] Toggle elements not found'); return; }
    
    function isDark() { return document.documentElement.classList.contains('dark'); }
    
    function applyTheme(){
        var dark = isDark();
        themeIcon.className = dark ? 'ri-sun-line text-lg' : 'ri-moon-line text-lg';
        document.documentElement.style.colorScheme = dark ? 'dark' : 'light';
        // Force repaint
        document.body.style.transition = 'background 0.3s ease, color 0.3s ease';
        console.log('[DARK] Theme applied:', dark ? 'DARK' : 'LIGHT');
    }
    
    applyTheme();
    
    themeBtn.addEventListener('click', function(e){
        e.preventDefault();
        e.stopPropagation();
        console.log('[DARK] Button clicked, current dark:', isDark());
        document.documentElement.classList.toggle('dark');
        var dark = isDark();
        localStorage.setItem('theme', dark ? 'dark' : 'light');
        applyTheme();
        console.log('[DARK] After toggle, dark:', dark, 'classList:', document.documentElement.className);
    });
    
    themeBtn.addEventListener('keydown', function(e){
        if(e.key==='Enter'||e.key===' '){ e.preventDefault(); themeBtn.click(); }
    });
    
    console.log('[DARK] Toggle initialized. Button found:', !!themeBtn, 'Current:', isDark() ? 'dark' : 'light');
})();

// ─── Multi-Bahasa (ID/EN) Toggle ───
(function(){
    var langBtn = document.getElementById('langToggle');
    var langLabel = document.getElementById('langLabel');
    if(!langBtn || !langLabel) return;

    var translations = {
        en: {
            'nav-home':'Home','nav-produk':'Products','nav-cara':'How to Order','nav-artikel':'Articles','nav-faq':'FAQ',
            'hero-title':'Upgrade Your Lifestyle Now','hero-sub':'Premium subscriptions at the best price. Guaranteed & instant delivery.',
            'hero-cta':'View Products','search-placeholder':'Search your favorite product...',
            'filter-semua':'All','sort-default':'Sort','sort-low':'Cheapest','sort-high':'Most Expensive',
            'section-testi':'What They Say','section-faq':'Frequently Asked Questions',
            'btn-buy':'Quick Buy','stock-empty':'Out of Stock','leaderboard-title':'Top Buyer This Month'
        },
        id: {
            'nav-home':'Home','nav-produk':'Produk','nav-cara':'Cara Order','nav-artikel':'Artikel','nav-faq':'FAQ',
            'hero-title':'Upgrade Gaya Hidupmu Sekarang','hero-sub':'Langganan premium harga terjangkau. Garansi & pengiriman instan.',
            'hero-cta':'Lihat Produk','search-placeholder':'Cari produk favoritmu...',
            'filter-semua':'Semua','sort-default':'Urutkan','sort-low':'Termurah','sort-high':'Termahal',
            'section-testi':'Apa Kata Mereka','section-faq':'Pertanyaan Umum',
            'btn-buy':'Quick Buy','stock-empty':'Stok Habis','leaderboard-title':'Top Buyer Bulan Ini'
        }
    };

    var currentLang = localStorage.getItem('lang') || 'id';
    langLabel.textContent = currentLang.toUpperCase();

    function applyLang(lang) {
        var dict = translations[lang];
        if(!dict) return;
        document.querySelectorAll('[data-i18n]').forEach(function(el){
            var key = el.getAttribute('data-i18n');
            if(dict[key]){
                if(el.tagName === 'INPUT') el.placeholder = dict[key];
                else el.textContent = dict[key];
            }
        });
        langLabel.textContent = lang.toUpperCase();
        localStorage.setItem('lang', lang);
        currentLang = lang;
    }

    langBtn.addEventListener('click', function(e){
        e.preventDefault();
        applyLang(currentLang === 'id' ? 'en' : 'id');
    });

    if(currentLang === 'en') applyLang('en');
})();

// ─── Parallax Scroll Effect for Background Orbs ───
(function(){
    var orbs = document.querySelectorAll('[class*="decor-orb"]');
    if(orbs.length === 0) return;
    var ticking = false;
    window.addEventListener('scroll', function(){
        if(!ticking){
            requestAnimationFrame(function(){
                var scrollY = window.pageYOffset;
                orbs.forEach(function(orb, i){
                    var speed = 0.02 + (i * 0.008);
                    var direction = i % 2 === 0 ? 1 : -1;
                    orb.style.transform = 'translateY(' + (scrollY * speed * direction) + 'px)';
                });
                ticking = false;
            });
            ticking = true;
        }
    });
})();

// ─── Live Sales Notification (1-2x per hour) ───
var salesNames=['Budi','Rina','Andi','Devi','Fajar','Maya','Reza','Sari','Nadia','Rizky','Putri','Agus'];
var salesProducts=['Netflix Premium','Spotify Premium','Canva Pro','YouTube Premium','Disney+ Hotstar','ChatGPT Plus','Microsoft 365','Google One'];
var salesTimes=['5 menit lalu','12 menit lalu','18 menit lalu','23 menit lalu','30 menit lalu'];
function showSalesPopup(){
    var popup=document.getElementById('salesPopup');
    document.getElementById('salesName').textContent=salesNames[Math.floor(Math.random()*salesNames.length)];
    document.getElementById('salesProduct').textContent='baru membeli '+salesProducts[Math.floor(Math.random()*salesProducts.length)];
    document.getElementById('salesTime').textContent=salesTimes[Math.floor(Math.random()*salesTimes.length)];
    popup.classList.add('show');
    setTimeout(function(){popup.classList.remove('show');},6000);
}
setTimeout(showSalesPopup, 15000);
function scheduleSalesPopup(){
    var delay = 1800000 + Math.floor(Math.random() * 900000); // 30-45 min
    setTimeout(function(){ showSalesPopup(); scheduleSalesPopup(); }, delay);
}
scheduleSalesPopup();

// ─── Global Loader ───
window.addEventListener('load', function() {
    var loader = document.getElementById('globalLoader');
    if (loader) { setTimeout(function(){ loader.classList.add('hidden'); }, 300); }
});

// ─── Fade-in-up intersection observer ───
var fiu = document.querySelectorAll('.fade-in-up');
if (fiu.length > 0 && 'IntersectionObserver' in window) {
    var obs = new IntersectionObserver(function(entries) {
        entries.forEach(function(e) { if (e.isIntersecting) { e.target.classList.add('visible'); obs.unobserve(e.target); } });
    }, { threshold: 0.1 });
    fiu.forEach(function(el) { obs.observe(el); });
}

// ─── Wishlist Badge Init ───
(function(){
    try{
        var list = JSON.parse(localStorage.getItem('wishlist')) || [];
        var badge = document.getElementById('wishlistBadge');
        if(badge && list.length > 0){ badge.textContent = list.length; badge.style.display = 'flex'; }
    }catch(e){}
})();

// ─── Scroll Progress Bar ───
(function(){
    var bar = document.getElementById('scrollProgressBar');
    if(!bar) return;
    window.addEventListener('scroll', function(){
        var st = window.scrollY;
        var dh = document.documentElement.scrollHeight - window.innerHeight;
        bar.style.width = Math.min((st/dh)*100, 100) + '%';
    });
})();

// ─── Notification Toggle ───
function toggleNotifs(){
    var dd = document.getElementById('notifDropdown');
    if(!dd) return;
    dd.classList.toggle('open');
    if(dd.classList.contains('open')) loadNotifs();
}
function loadNotifs(){
    fetch('<?= BASE_URL ?>/admin/api.php?action=notifications_get')
    .then(function(r){return r.json()})
    .then(function(d){
        var list = document.getElementById('notifList');
        if(!list || !d.ok) return;
        if(!d.data || d.data.length===0){
            list.innerHTML='<div class="text-center py-6"><i class="ri-notification-off-line text-2xl" style="color:var(--muted)"></i><p class="text-xs mt-2" style="color:var(--muted)">Belum ada notifikasi</p></div>';
            return;
        }
        var html='';
        d.data.forEach(function(n){
            var icons = {info:'ri-information-fill text-blue-400',success:'ri-check-double-fill text-green-500',warning:'ri-alert-fill text-amber-400',promo:'ri-gift-fill text-pink-500'};
            html+='<div class="notif-item '+(n.is_read==0?'unread':'')+'"><div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0" style="background:var(--surface)"><i class="'+(icons[n.type]||icons.info)+'"></i></div><div class="flex-1 min-w-0"><p class="text-xs font-semibold truncate" style="color:var(--text)">'+n.title+'</p><p class="text-[10px] truncate" style="color:var(--muted)">'+n.message+'</p></div></div>';
        });
        list.innerHTML=html;
    }).catch(function(){});
}
function markAllRead(){
    fetch('<?= BASE_URL ?>/admin/api.php?action=notifications_read_all',{method:'POST'})
    .then(function(){
        var badge = document.querySelector('#notifBtn .animate-pulse');
        if(badge) badge.remove();
        loadNotifs();
    });
}
// Close dropdown when clicking outside
document.addEventListener('click',function(e){
    var dd=document.getElementById('notifDropdown');
    if(dd && dd.classList.contains('open') && !e.target.closest('#notifBtn') && !e.target.closest('#notifDropdown')){
        dd.classList.remove('open');
    }
});
</script>
