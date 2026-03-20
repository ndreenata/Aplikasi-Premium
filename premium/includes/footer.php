<?php /** FOOTER.PHP — Natsy Premiums v2 · Enhanced Aesthetic */ ?>

<footer class="relative overflow-hidden" style="border-top:1px solid var(--border);background:var(--bg2)">
    <!-- Footer mesh gradient background -->
    <div class="absolute inset-0 pointer-events-none">
        <div class="absolute bottom-0 left-0 w-[400px] h-[400px] rounded-full" style="background:radial-gradient(circle,rgba(34,197,94,0.06),transparent 70%);filter:blur(50px)"></div>
        <div class="absolute top-0 right-0 w-[350px] h-[350px] rounded-full" style="background:radial-gradient(circle,rgba(6,182,212,0.04),transparent 70%);filter:blur(40px)"></div>
        <div class="absolute inset-0" style="background-image:radial-gradient(var(--border) 1px,transparent 1px);background-size:24px 24px;opacity:0.04"></div>
    </div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            <!-- Brand -->
            <div>
                <div class="flex items-center gap-2.5 mb-3">
                    <div class="w-9 h-9 rounded-xl bg-green-600 flex items-center justify-center text-white text-xs font-black shadow-lg shadow-green-600/20">N</div>
                    <div class="flex flex-col leading-none">
                        <span class="text-sm font-extrabold" style="color:var(--text)">Natsy</span>
                        <span class="text-[9px] font-semibold text-green-500 tracking-widest uppercase">Premiums</span>
                    </div>
                </div>
                <p class="text-xs leading-relaxed mb-4" style="color:var(--text2)">Akun premium digital berkualitas dengan harga terjangkau. Auto-delivery via WhatsApp.</p>
                <div class="inline-flex items-center gap-2 px-3 py-2 rounded-xl glass">
                    <i class="ri-shield-check-fill text-green-500"></i>
                    <span class="text-[10px] font-semibold" style="color:var(--text2)">Terdaftar di Komdigi</span>
                </div>
            </div>

            <!-- Quick Links -->
            <div>
                <h4 class="text-[10px] font-black uppercase tracking-[0.15em] mb-4" style="color:var(--text)">Link Cepat</h4>
                <ul class="space-y-2.5">
                    <li><a href="<?= BASE_URL ?>/index.php" class="text-sm hover:text-green-500 transition flex items-center gap-2" style="color:var(--text2)"><i class="ri-arrow-right-s-fill text-xs text-green-500/50"></i>Home</a></li>
                    <li><a href="<?= BASE_URL ?>/index.php#products" class="text-sm hover:text-green-500 transition flex items-center gap-2" style="color:var(--text2)"><i class="ri-arrow-right-s-fill text-xs text-green-500/50"></i>Produk</a></li>
                    <li><a href="<?= BASE_URL ?>/pages/blog.php" class="text-sm hover:text-green-500 transition flex items-center gap-2" style="color:var(--text2)"><i class="ri-arrow-right-s-fill text-xs text-green-500/50"></i>Blog & Tutorial</a></li>
                    <li><a href="<?= BASE_URL ?>/pages/service_status.php" class="text-sm hover:text-green-500 transition flex items-center gap-2" style="color:var(--text2)"><i class="ri-arrow-right-s-fill text-xs text-green-500/50"></i>Status Layanan</a></li>
                    <li><a href="<?= BASE_URL ?>/pages/request_product.php" class="text-sm hover:text-green-500 transition flex items-center gap-2" style="color:var(--text2)"><i class="ri-arrow-right-s-fill text-xs text-green-500/50"></i>Request Produk</a></li>
                    <li><a href="<?= BASE_URL ?>/pages/track_order.php" class="text-sm hover:text-green-500 transition flex items-center gap-2" style="color:var(--text2)"><i class="ri-arrow-right-s-fill text-xs text-green-500/50"></i>Lacak Pesanan</a></li>
                    <li><a href="<?= BASE_URL ?>/pages/terms.php" class="text-sm hover:text-green-500 transition flex items-center gap-2" style="color:var(--text2)"><i class="ri-arrow-right-s-fill text-xs text-green-500/50"></i>Syarat & Ketentuan</a></li>
                </ul>
            </div>

            <!-- Contact + Hours -->
            <div>
                <h4 class="text-[10px] font-black uppercase tracking-[0.15em] mb-4" style="color:var(--text)">Hubungi Kami</h4>
                <div class="space-y-3">
                    <a href="https://wa.me/6281234567890" target="_blank" class="flex items-center gap-3 p-3 rounded-xl glass hover:border-green-500 transition group">
                        <div class="w-9 h-9 rounded-xl bg-green-500/10 flex items-center justify-center group-hover:scale-105 transition-transform"><i class="ri-whatsapp-fill text-[#25D366]"></i></div>
                        <div><span class="text-[10px]" style="color:var(--muted)">WhatsApp</span><br><span class="text-xs font-bold" style="color:var(--text)">Tanya Admin</span></div>
                    </a>
                    <a href="mailto:hello@natsypremiums.com" class="flex items-center gap-3 p-3 rounded-xl glass hover:border-blue-500 transition group">
                        <div class="w-9 h-9 rounded-xl bg-blue-500/10 flex items-center justify-center group-hover:scale-105 transition-transform"><i class="ri-mail-fill text-blue-400"></i></div>
                        <div><span class="text-[10px]" style="color:var(--muted)">Email</span><br><span class="text-xs font-bold" style="color:var(--text)">hello@natsypremiums.com</span></div>
                    </a>
                </div>
                <div class="flex items-center gap-2 mt-3 px-3 py-2 rounded-xl glass">
                    <i class="ri-time-fill text-green-500"></i>
                    <span class="text-xs font-semibold" style="color:var(--text2)">08.00 - 23.00 WIB</span>
                </div>
            </div>

            <!-- Payment Methods -->
            <div>
                <h4 class="text-[10px] font-black uppercase tracking-[0.15em] mb-4" style="color:var(--text)">Metode Pembayaran</h4>
                <div class="grid grid-cols-3 gap-2">
                    <div class="glass rounded-xl px-3 py-2.5 text-center"><span class="text-[10px] font-bold" style="color:var(--text)">BCA</span></div>
                    <div class="glass rounded-xl px-3 py-2.5 text-center"><span class="text-[10px] font-bold" style="color:var(--text)">BRI</span></div>
                    <div class="glass rounded-xl px-3 py-2.5 text-center"><span class="text-[10px] font-bold" style="color:var(--text)">BNI</span></div>
                    <div class="glass rounded-xl px-3 py-2.5 text-center"><span class="text-[10px] font-bold text-blue-400">Dana</span></div>
                    <div class="glass rounded-xl px-3 py-2.5 text-center"><span class="text-[10px] font-bold text-green-500">GoPay</span></div>
                    <div class="glass rounded-xl px-3 py-2.5 text-center"><span class="text-[10px] font-bold text-orange-400">Shopee</span></div>
                    <div class="glass rounded-xl px-3 py-2.5 text-center"><span class="text-[10px] font-bold text-purple-400">OVO</span></div>
                    <div class="glass rounded-xl px-3 py-2.5 text-center col-span-2">
                        <span class="text-[10px] font-bold" style="color:var(--text)"><i class="ri-qr-code-fill text-green-500 mr-1"></i>QRIS</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom bar -->
        <div class="mt-10 pt-6 flex flex-col sm:flex-row items-center justify-between gap-3" style="border-top:1px solid var(--border)">
            <span class="text-xs" style="color:var(--muted)">&copy; <?= date('Y') ?> <?= SITE_NAME ?>. All rights reserved.</span>
            <span class="flex items-center gap-2 text-xs" style="color:var(--muted)"><span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span>System Online</span>
        </div>

        <!-- Trust Seals (#38) -->
        <div class="mt-6 flex flex-wrap items-center justify-center gap-3">
            <div class="glass rounded-xl px-3 py-2 flex items-center gap-2"><i class="ri-shield-check-fill text-green-500"></i><span class="text-[9px] font-bold" style="color:var(--text2)">SSL Secured</span></div>
            <div class="glass rounded-xl px-3 py-2 flex items-center gap-2"><i class="ri-verified-badge-fill text-blue-400"></i><span class="text-[9px] font-bold" style="color:var(--text2)">Verified Seller</span></div>
            <div class="glass rounded-xl px-3 py-2 flex items-center gap-2"><i class="ri-lock-fill text-purple-400"></i><span class="text-[9px] font-bold" style="color:var(--text2)">Safe Payment</span></div>
            <div class="glass rounded-xl px-3 py-2 flex items-center gap-2"><i class="ri-customer-service-2-fill text-orange-400"></i><span class="text-[9px] font-bold" style="color:var(--text2)">24/7 Support</span></div>
            <div class="glass rounded-xl px-3 py-2 flex items-center gap-2"><i class="ri-refund-2-fill text-emerald-400"></i><span class="text-[9px] font-bold" style="color:var(--text2)">Garansi 30 Hari</span></div>
        </div>
    </div>
</footer>

<!-- ═══ BACK TO TOP BUTTON ═══ -->
<button id="backToTop" onclick="window.scrollTo({top:0,behavior:'smooth'})" class="fixed bottom-24 right-6 z-50 w-12 h-12 rounded-2xl bg-green-600 text-white shadow-xl shadow-green-600/30 flex items-center justify-center opacity-0 invisible translate-y-4 transition-all duration-300 hover:bg-green-500 hover:scale-110 active:scale-95 group" aria-label="Back to top">
    <i class="ri-arrow-up-line text-lg group-hover:-translate-y-0.5 transition-transform"></i>
    <svg class="absolute inset-0 w-full h-full -rotate-90" viewBox="0 0 48 48">
        <circle cx="24" cy="24" r="22" fill="none" stroke="rgba(255,255,255,0.15)" stroke-width="2"/>
        <circle id="scrollProgress" cx="24" cy="24" r="22" fill="none" stroke="white" stroke-width="2" stroke-dasharray="138.23" stroke-dashoffset="138.23" stroke-linecap="round"/>
    </svg>
</button>

<!-- ═══ NEWSLETTER POPUP ═══ -->
<div id="newsletterPopup" class="fixed inset-0 z-[999] flex items-center justify-center p-4 opacity-0 invisible transition-all duration-300">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeNewsletter()"></div>
    <div class="relative w-full max-w-sm rounded-3xl p-7 shadow-2xl transform scale-95 transition-transform duration-300" style="background:var(--card);border:1px solid var(--card-border);backdrop-filter:blur(20px)">
        <button onclick="closeNewsletter()" class="absolute top-4 right-4 w-8 h-8 rounded-xl flex items-center justify-center hover:bg-red-500/10 transition" style="color:var(--muted)"><i class="ri-close-line text-lg"></i></button>
        <div class="text-center mb-5">
            <div class="w-14 h-14 mx-auto mb-3 rounded-2xl bg-green-500/10 flex items-center justify-center">
                <i class="ri-mail-star-fill text-2xl text-green-500"></i>
            </div>
            <h3 class="text-lg font-black" style="color:var(--text)">Mau Diskon Eksklusif? 🎉</h3>
            <p class="text-xs mt-1" style="color:var(--muted)">Subscribe newsletter kami & dapatkan voucher diskon 10%!</p>
        </div>
        <form id="newsletterForm" class="space-y-3">
            <input type="email" id="nlEmail" required placeholder="email@kamu.com" class="w-full px-4 py-3 rounded-xl text-sm outline-none transition" style="background:var(--bg);border:1.5px solid var(--border);color:var(--text)" onfocus="this.style.borderColor='#22C55E'" onblur="this.style.borderColor='var(--border)'">
            <button type="submit" class="w-full py-3 bg-green-600 text-white text-sm font-bold rounded-xl hover:bg-green-500 transition shadow-lg shadow-green-600/20">
                <i class="ri-gift-fill mr-1"></i> Dapatkan Voucher
            </button>
        </form>
        <p class="text-center text-[10px] mt-3" style="color:var(--muted)">Tanpa spam. Bisa unsubscribe kapan saja.</p>
    </div>
</div>

<script>
AOS.init({ duration: 600, easing: 'ease-out-cubic', once: true, offset: 50 });

// ═══ BACK TO TOP + Scroll Progress ═══
(function(){
    const btn = document.getElementById('backToTop');
    const circle = document.getElementById('scrollProgress');
    const circumference = 2 * Math.PI * 22;
    let ticking = false;
    window.addEventListener('scroll', function(){
        if(!ticking){ requestAnimationFrame(function(){
            const scrollTop = window.scrollY;
            const docHeight = document.documentElement.scrollHeight - window.innerHeight;
            const progress = Math.min(scrollTop / docHeight, 1);
            // Show/hide
            if(scrollTop > 400) { btn.classList.remove('opacity-0','invisible','translate-y-4'); }
            else { btn.classList.add('opacity-0','invisible','translate-y-4'); }
            // Progress ring
            if(circle) circle.style.strokeDashoffset = circumference - (progress * circumference);
            ticking = false;
        }); ticking = true; }
    });
})();

// ═══ SMOOTH DARK MODE TOGGLE ═══
(function(){
    const toggle = document.getElementById('themeToggle');
    if(!toggle) return;
    toggle.addEventListener('click', function(){
        // Add smooth transition to body
        document.documentElement.style.transition = 'background 0.4s ease, color 0.3s ease';
        document.body.style.transition = 'background 0.4s ease';
        document.documentElement.classList.toggle('dark');
        const isDark = document.documentElement.classList.contains('dark');
        localStorage.setItem('theme', isDark ? 'dark' : 'light');
        // Update icon
        const icon = toggle.querySelector('i');
        if(icon){ icon.className = isDark ? 'ri-sun-fill' : 'ri-moon-fill'; }
        // Remove transition after animation
        setTimeout(()=>{
            document.documentElement.style.transition='';
            document.body.style.transition='';
        }, 500);
    });
})();

// ═══ NEWSLETTER POPUP ═══
(function(){
    const popup = document.getElementById('newsletterPopup');
    if(!popup || localStorage.getItem('nl_closed')) return;
    // Show after 30 seconds or 60% scroll
    let shown = false;
    function showNL(){
        if(shown) return; shown=true;
        popup.classList.remove('opacity-0','invisible');
        popup.querySelector('.relative').classList.remove('scale-95');
    }
    setTimeout(showNL, 30000);
    window.addEventListener('scroll', function(){
        if(window.scrollY > document.documentElement.scrollHeight * 0.6) showNL();
    });
    // Form submit
    document.getElementById('newsletterForm').addEventListener('submit', function(e){
        e.preventDefault();
        const email = document.getElementById('nlEmail').value;
        fetch('<?= BASE_URL ?>/admin/api.php?action=newsletter_subscribe', {
            method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'},
            body:'email='+encodeURIComponent(email)
        }).then(r=>r.json()).then(d=>{
            popup.querySelector('form').innerHTML = '<div class="text-center py-4"><i class="ri-check-double-fill text-4xl text-green-500 mb-2"></i><p class="text-sm font-bold" style="color:var(--text)">Terima kasih! 🎉</p><p class="text-xs mt-1" style="color:var(--muted)">Voucher akan dikirim ke email kamu.</p></div>';
            localStorage.setItem('nl_closed','1');
            setTimeout(closeNewsletter, 3000);
        }).catch(()=>{});
    });
})();
function closeNewsletter(){
    const p = document.getElementById('newsletterPopup');
    p.classList.add('opacity-0','invisible');
    p.querySelector('.relative').classList.add('scale-95');
    localStorage.setItem('nl_closed','1');
}
// ═══ ACCESSIBILITY FONT SIZE (#17) ═══
(function(){
    const saved = localStorage.getItem('fontSize');
    if(saved) document.documentElement.style.fontSize = saved;
})();
function cycleFontSize(){
    const sizes = ['16px','18px','20px','14px'];
    const current = getComputedStyle(document.documentElement).fontSize;
    const idx = sizes.indexOf(current);
    const next = sizes[(idx+1) % sizes.length];
    document.documentElement.style.fontSize = next;
    localStorage.setItem('fontSize', next);
}

// ═══ PWA SERVICE WORKER (#48) ═══
if('serviceWorker' in navigator){
    navigator.serviceWorker.register('<?= BASE_URL ?>/sw.js').catch(()=>{});
}

// ═══ LAZY LOADING (#49) ═══
(function(){
    document.querySelectorAll('img[data-src]').forEach(function(img){
        const observer = new IntersectionObserver(function(entries){
            entries.forEach(e=>{ if(e.isIntersecting){ img.src=img.dataset.src; img.removeAttribute('data-src'); observer.unobserve(img); } });
        });
        observer.observe(img);
    });
    // Also add loading="lazy" to all images
    document.querySelectorAll('img:not([loading])').forEach(i=>i.setAttribute('loading','lazy'));
})();
</script>

<!-- Accessibility Toggle (#17) -->
<button onclick="cycleFontSize()" class="fixed bottom-40 right-6 z-50 w-10 h-10 rounded-xl glass shadow-lg flex items-center justify-center hover:scale-110 transition-transform" title="Ubah Ukuran Font" style="color:var(--text2)">
    <i class="ri-font-size-2 text-sm"></i>
</button>

</body>
</html>

