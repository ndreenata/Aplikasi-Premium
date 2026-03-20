        </main><!-- /content -->
    </div><!-- /admin-main -->
</div><!-- /admin-wrapper -->

<script>
// ─── Sidebar toggle (mobile) ───
function toggleSidebar() {
    document.getElementById('adminSidebar').classList.toggle('open');
    document.getElementById('mobileOverlay').classList.toggle('active');
}

// ─── Dark mode ───
function toggleAdminTheme() {
    var d = document.documentElement;
    d.classList.toggle('dark');
    localStorage.setItem('theme', d.classList.contains('dark') ? 'dark' : 'light');
    updateThemeIcon();
}
function updateThemeIcon() {
    var icon = document.getElementById('adminThemeIcon');
    if (icon) icon.className = document.documentElement.classList.contains('dark') ? 'ri-sun-line text-lg' : 'ri-moon-line text-lg';
}
updateThemeIcon();

// ─── Modal helpers ───
function openModal(id) { document.getElementById(id).classList.add('active'); }
function closeModal(id) { document.getElementById(id).classList.remove('active'); }

// ─── API helper ───
function adminAPI(action, data, callback) {
    var fd = new FormData();
    fd.append('action', action);
    if (data) Object.keys(data).forEach(function(k) { fd.append(k, data[k]); });
    fetch('<?= BASE_URL ?>/admin/api.php', { method: 'POST', body: fd })
        .then(function(r) { return r.json(); })
        .then(function(d) { if (callback) callback(d); })
        .catch(function(e) { console.error(e); alert('Error: ' + e.message); });
}

// ─── Toast notification ───
function showToast(msg, type) {
    var t = document.createElement('div');
    t.style.cssText = 'position:fixed;bottom:24px;right:24px;z-index:999;padding:12px 20px;border-radius:14px;font-size:13px;font-weight:600;color:white;display:flex;align-items:center;gap:8px;animation:adminFadeIn 0.3s ease;box-shadow:0 8px 30px rgba(0,0,0,0.15);';
    t.style.background = type === 'error' ? '#EF4444' : '#16A34A';
    t.innerHTML = '<i class="ri-' + (type==='error'?'close-circle':'check') + '-fill"></i>' + msg;
    document.body.appendChild(t);
    setTimeout(function() { t.style.opacity = '0'; t.style.transition = 'opacity 0.3s'; setTimeout(function() { t.remove(); }, 300); }, 2500);
}

// ─── Confirm delete ───
function confirmAction(msg, callback) {
    if (confirm(msg)) callback();
}
</script>
</body>
</html>
