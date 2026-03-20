<?php
/**
 * WISHLIST.PHP — Wishlist Page (localStorage-based)
 * Displays wishlisted products stored in browser localStorage
 */
require_once __DIR__ . '/../includes/koneksi.php';
$pageTitle = 'Wishlist — ' . SITE_NAME;
include __DIR__ . '/../includes/header.php';
?>

<section class="pt-24 pb-16 min-h-screen">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-8" data-aos="fade-up">
            <div>
                <h1 class="text-2xl font-black" style="color:var(--text)"><i class="ri-heart-fill text-pink-500 mr-2"></i>Wishlist Kamu</h1>
                <p class="text-xs mt-1" style="color:var(--muted)">Produk yang kamu simpan untuk nanti</p>
            </div>
            <button onclick="clearWishlist()" class="hidden px-4 py-2 rounded-xl glass text-xs font-bold hover:bg-red-500/10 text-red-400 btn-press" id="clearWishlistBtn"><i class="ri-delete-bin-line mr-1"></i>Hapus Semua</button>
        </div>

        <div id="wishlistGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <!-- Populated by JS -->
        </div>

        <div id="wishlistEmpty" class="hidden text-center py-20" data-aos="fade-up">
            <div class="w-20 h-20 mx-auto mb-4 rounded-3xl bg-pink-500/10 flex items-center justify-center">
                <i class="ri-heart-line text-4xl text-pink-400"></i>
            </div>
            <h2 class="text-lg font-bold mb-2" style="color:var(--text)">Wishlist Kosong</h2>
            <p class="text-sm mb-6" style="color:var(--muted)">Belum ada produk yang disimpan. Yuk mulai belanja!</p>
            <a href="<?= BASE_URL ?>/index.php#products" class="inline-flex items-center gap-2 px-6 py-3 bg-green-600 text-white text-sm font-bold rounded-xl hover:bg-green-500 btn-press shadow-lg shadow-green-600/20"><i class="ri-store-2-line"></i> Lihat Produk</a>
        </div>
    </div>
</section>

<script>
function getWishlist(){try{return JSON.parse(localStorage.getItem('wishlist'))||[];}catch(e){return [];}}
function saveWishlist(list){localStorage.setItem('wishlist',JSON.stringify(list));}

function renderWishlist(){
    var list = getWishlist();
    var grid = document.getElementById('wishlistGrid');
    var empty = document.getElementById('wishlistEmpty');
    var clearBtn = document.getElementById('clearWishlistBtn');

    if(list.length === 0){
        grid.innerHTML = '';
        empty.classList.remove('hidden');
        clearBtn.classList.add('hidden');
        return;
    }

    empty.classList.add('hidden');
    clearBtn.classList.remove('hidden');

    var html = '';
    list.forEach(function(item){
        html += '<div class="glass-strong rounded-2xl p-5 flex items-center gap-4 hover-lift" data-aos="fade-up">'
            + '<div class="w-14 h-14 rounded-2xl bg-pink-500/10 flex items-center justify-center shrink-0"><i class="ri-heart-fill text-2xl text-pink-500"></i></div>'
            + '<div class="flex-1 min-w-0">'
            + '<h3 class="text-sm font-bold truncate" style="color:var(--text)">' + item.name + '</h3>'
            + '<p class="text-lg font-extrabold text-green-500 mt-0.5">Rp ' + item.price.toLocaleString('id-ID') + '</p>'
            + '</div>'
            + '<div class="flex gap-2 shrink-0">'
            + '<a href="<?= BASE_URL ?>/store/product_detail.php?id=' + item.id + '" class="w-10 h-10 rounded-xl bg-green-600 text-white flex items-center justify-center hover:bg-green-500 btn-press shadow-lg shadow-green-600/20" title="Lihat"><i class="ri-eye-line"></i></a>'
            + '<button onclick="removeWishlistItem(' + item.id + ')" class="w-10 h-10 rounded-xl glass flex items-center justify-center hover:bg-red-500/10 text-red-400 btn-press" title="Hapus"><i class="ri-close-line"></i></button>'
            + '</div></div>';
    });
    grid.innerHTML = html;
}

function removeWishlistItem(pid){
    var list = getWishlist();
    list = list.filter(function(i){return i.id !== pid;});
    saveWishlist(list);
    renderWishlist();
}

function clearWishlist(){
    if(confirm('Hapus semua wishlist?')){
        localStorage.removeItem('wishlist');
        renderWishlist();
    }
}

renderWishlist();
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
