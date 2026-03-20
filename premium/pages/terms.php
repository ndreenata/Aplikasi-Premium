<?php
/**
 * TERMS.PHP — Syarat & Ketentuan
 */
require_once __DIR__ . '/../includes/koneksi.php';
$pageTitle = 'Syarat & Ketentuan — ' . SITE_NAME;
include __DIR__ . '/../includes/header.php';
?>

<section class="py-12 sm:py-20">
    <div class="max-w-3xl mx-auto px-4">
        <div class="glass-strong rounded-3xl p-7 sm:p-10" data-aos="fade-up">
            <div class="text-center mb-8">
                <div class="w-14 h-14 mx-auto mb-4 rounded-2xl bg-green-500/10 border border-green-500/20 flex items-center justify-center">
                    <i class="ri-file-text-fill text-2xl text-green-500"></i>
                </div>
                <h1 class="text-2xl font-black" style="color:var(--text)">Syarat & Ketentuan</h1>
                <p class="text-xs mt-1" style="color:var(--muted)">Terakhir diperbarui: <?= date('d F Y') ?></p>
            </div>

            <div class="prose-content space-y-5 text-sm leading-relaxed" style="color:var(--text2)">
                <div>
                    <h3 class="text-base font-bold mb-2" style="color:var(--text)">1. Penggunaan Layanan</h3>
                    <p>Dengan menggunakan layanan <strong><?= SITE_NAME ?></strong>, Anda setuju untuk mematuhi semua syarat dan ketentuan yang berlaku. Layanan kami menyediakan akun premium digital untuk berbagai platform dengan harga terjangkau.</p>
                </div>

                <div>
                    <h3 class="text-base font-bold mb-2" style="color:var(--text)">2. Akun Pengguna</h3>
                    <ul class="list-disc pl-5 space-y-1">
                        <li>Anda bertanggung jawab menjaga kerahasiaan informasi akun Anda.</li>
                        <li>Satu akun hanya untuk satu pengguna.</li>
                        <li>Dilarang membagikan detail login ke pihak lain.</li>
                        <li>Kami berhak menonaktifkan akun yang melanggar ketentuan.</li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-base font-bold mb-2" style="color:var(--text)">3. Pembelian & Pembayaran</h3>
                    <ul class="list-disc pl-5 space-y-1">
                        <li>Semua harga tertera dalam Rupiah (IDR).</li>
                        <li>Pembayaran harus diselesaikan sebelum akun dikirimkan.</li>
                        <li>Akun akan dikirim otomatis ke WhatsApp setelah pembayaran terverifikasi.</li>
                        <li>Harga dapat berubah sewaktu-waktu tanpa pemberitahuan.</li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-base font-bold mb-2" style="color:var(--text)">4. Kebijakan Pengembalian</h3>
                    <p>Pengembalian dana hanya berlaku jika:</p>
                    <ul class="list-disc pl-5 space-y-1">
                        <li>Akun yang dikirimkan tidak bisa digunakan sama sekali.</li>
                        <li>Klaim diajukan dalam waktu 24 jam setelah pembelian.</li>
                        <li>Kami akan menyediakan akun pengganti atau refund penuh.</li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-base font-bold mb-2" style="color:var(--text)">5. Larangan</h3>
                    <ul class="list-disc pl-5 space-y-1">
                        <li>Mengubah password akun premium yang dibeli.</li>
                        <li>Membagikan akun ke orang lain.</li>
                        <li>Menggunakan layanan untuk aktivitas ilegal.</li>
                        <li>Melakukan chargeback tanpa konfirmasi.</li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-base font-bold mb-2" style="color:var(--text)">6. Batasan Tanggung Jawab</h3>
                    <p><?= SITE_NAME ?> tidak bertanggung jawab atas kerugian yang timbul dari penggunaan layanan di luar ketentuan yang berlaku. Kami berusaha memberikan layanan terbaik namun tidak menjamin ketersediaan 100%.</p>
                </div>

                <div>
                    <h3 class="text-base font-bold mb-2" style="color:var(--text)">7. Perubahan Ketentuan</h3>
                    <p>Kami berhak mengubah syarat dan ketentuan ini kapan saja. Perubahan akan berlaku segera setelah dipublikasikan di halaman ini.</p>
                </div>

                <div class="glass rounded-xl p-4 flex items-start gap-2 text-xs">
                    <i class="ri-question-fill text-green-500 text-base mt-0.5"></i>
                    <span>Ada pertanyaan? Hubungi kami melalui WhatsApp untuk bantuan lebih lanjut.</span>
                </div>
            </div>

            <div class="mt-8 text-center">
                <a href="register.php" class="text-xs font-medium text-green-500 hover:underline"><i class="ri-arrow-left-line"></i> Kembali ke Registrasi</a>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
