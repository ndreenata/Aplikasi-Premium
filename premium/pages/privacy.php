<?php
/**
 * PRIVACY.PHP — Kebijakan Privasi
 */
require_once __DIR__ . '/../includes/koneksi.php';
$pageTitle = 'Kebijakan Privasi — ' . SITE_NAME;
include __DIR__ . '/../includes/header.php';
?>

<section class="py-12 sm:py-20">
    <div class="max-w-3xl mx-auto px-4">
        <div class="glass-strong rounded-3xl p-7 sm:p-10" data-aos="fade-up">
            <div class="text-center mb-8">
                <div class="w-14 h-14 mx-auto mb-4 rounded-2xl bg-blue-500/10 border border-blue-500/20 flex items-center justify-center">
                    <i class="ri-shield-check-fill text-2xl text-blue-400"></i>
                </div>
                <h1 class="text-2xl font-black" style="color:var(--text)">Kebijakan Privasi</h1>
                <p class="text-xs mt-1" style="color:var(--muted)">Terakhir diperbarui: <?= date('d F Y') ?></p>
            </div>

            <div class="prose-content space-y-5 text-sm leading-relaxed" style="color:var(--text2)">
                <div>
                    <h3 class="text-base font-bold mb-2" style="color:var(--text)">1. Informasi yang Kami Kumpulkan</h3>
                    <p>Kami mengumpulkan informasi berikut saat Anda menggunakan layanan kami:</p>
                    <ul class="list-disc pl-5 space-y-1">
                        <li><strong>Data Pribadi:</strong> Nama, email, nomor WhatsApp, username, tanggal lahir, jenis kelamin, dan alamat.</li>
                        <li><strong>Data Transaksi:</strong> Riwayat pembelian, invoice, dan metode pembayaran.</li>
                        <li><strong>Data Teknis:</strong> Alamat IP, jenis browser, dan perangkat yang digunakan.</li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-base font-bold mb-2" style="color:var(--text)">2. Penggunaan Informasi</h3>
                    <p>Informasi Anda digunakan untuk:</p>
                    <ul class="list-disc pl-5 space-y-1">
                        <li>Memproses pesanan dan mengirimkan akun premium.</li>
                        <li>Mengirim notifikasi terkait transaksi via WhatsApp.</li>
                        <li>Meningkatkan kualitas layanan kami.</li>
                        <li>Menghubungi Anda jika diperlukan terkait pesanan.</li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-base font-bold mb-2" style="color:var(--text)">3. Perlindungan Data</h3>
                    <p>Kami berkomitmen melindungi data Anda dengan:</p>
                    <ul class="list-disc pl-5 space-y-1">
                        <li>Enkripsi password menggunakan hashing modern.</li>
                        <li>Tidak menjual data pribadi ke pihak ketiga.</li>
                        <li>Akses data terbatas hanya untuk tim yang berwenang.</li>
                        <li>Penyimpanan data di server yang aman.</li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-base font-bold mb-2" style="color:var(--text)">4. Cookies</h3>
                    <p>Website kami menggunakan cookies dan local storage untuk:</p>
                    <ul class="list-disc pl-5 space-y-1">
                        <li>Menyimpan preferensi tampilan (dark/light mode).</li>
                        <li>Mempertahankan sesi login.</li>
                        <li>Meningkatkan pengalaman browsing.</li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-base font-bold mb-2" style="color:var(--text)">5. Hak Pengguna</h3>
                    <p>Anda memiliki hak untuk:</p>
                    <ul class="list-disc pl-5 space-y-1">
                        <li>Mengakses data pribadi yang kami simpan.</li>
                        <li>Meminta koreksi data yang tidak akurat.</li>
                        <li>Meminta penghapusan akun dan data terkait.</li>
                        <li>Menolak komunikasi pemasaran.</li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-base font-bold mb-2" style="color:var(--text)">6. Berbagi Data</h3>
                    <p>Kami hanya membagikan data Anda kepada pihak ketiga dalam kondisi berikut:</p>
                    <ul class="list-disc pl-5 space-y-1">
                        <li>Diperlukan oleh hukum yang berlaku.</li>
                        <li>Untuk memproses pembayaran melalui payment gateway.</li>
                        <li>Untuk mengirim notifikasi melalui layanan WhatsApp API.</li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-base font-bold mb-2" style="color:var(--text)">7. Perubahan Kebijakan</h3>
                    <p>Kebijakan privasi ini dapat diperbarui sewaktu-waktu. Kami akan memberitahukan perubahan signifikan melalui website kami.</p>
                </div>

                <div class="glass rounded-xl p-4 flex items-start gap-2 text-xs">
                    <i class="ri-shield-user-fill text-blue-400 text-base mt-0.5"></i>
                    <span>Privasi Anda penting bagi kami. Hubungi admin jika ada pertanyaan tentang kebijakan ini.</span>
                </div>
            </div>

            <div class="mt-8 text-center">
                <a href="register.php" class="text-xs font-medium text-green-500 hover:underline"><i class="ri-arrow-left-line"></i> Kembali ke Registrasi</a>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
