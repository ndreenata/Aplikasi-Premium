-- ═══════════════════════════════════
-- DATABASE.SQL — Natsy Premiums
-- Warm Earth Tones Edition
-- ═══════════════════════════════════

CREATE DATABASE IF NOT EXISTS premium_store;
USE premium_store;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    username VARCHAR(50) DEFAULT NULL UNIQUE,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20) DEFAULT NULL,
    birthdate DATE DEFAULT NULL,
    gender ENUM('male','female','other') DEFAULT NULL,
    address TEXT DEFAULT NULL,
    role ENUM('user','admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Products table
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    description TEXT,
    price DECIMAL(12,2) NOT NULL,
    category VARCHAR(50) DEFAULT 'productivity',
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Stocks table
CREATE TABLE IF NOT EXISTS stocks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    account_data TEXT NOT NULL,
    status ENUM('available','sold') DEFAULT 'available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Transactions table
CREATE TABLE IF NOT EXISTS transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT DEFAULT NULL,
    customer_name VARCHAR(100) DEFAULT NULL,
    invoice_number VARCHAR(50) NOT NULL,
    product_id INT NOT NULL,
    phone_number VARCHAR(20) NOT NULL,
    amount DECIMAL(12,2) NOT NULL,
    status ENUM('PENDING','SUCCESS','FAILED') DEFAULT 'PENDING',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Articles table
CREATE TABLE IF NOT EXISTS articles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    content TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Password resets table
CREATE TABLE IF NOT EXISTS password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token VARCHAR(255) NOT NULL,
    expires_at DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- ═══════════════════════════════════
-- SEED: 21 Dummy Products
-- ═══════════════════════════════════

-- STREAMING (8)
INSERT INTO products (name, description, price, category) VALUES
('Netflix Premium',       'Akses streaming film & series tanpa batas. Ultra HD 4K, multi-device.',                   45000,  'musik_video'),
('Spotify Premium',       'Dengarkan musik tanpa iklan, offline mode, kualitas audio tertinggi.',                     25000,  'musik_video'),
('YouTube Premium',       'Nonton YouTube tanpa iklan, background play, dan YouTube Music.',                          35000,  'musik_video'),
('Disney+ Hotstar',       'Streaming konten Disney, Marvel, Star Wars, Pixar dan National Geographic.',               30000,  'musik_video'),
('Viu Premium',           'Drama Korea & Asia terlengkap tanpa iklan dengan subtitle Bahasa Indonesia.',              20000,  'musik_video'),
('WeTV VIP',              'Nonton drama China, Korea & variety show eksklusif tanpa iklan.',                           18000,  'musik_video'),
('Prime Video',           'Film & series eksklusif Amazon Original dengan kualitas 4K HDR.',                           28000,  'musik_video'),
('HBO Go',                'Series premium HBO Original: House of the Dragon, The Last of Us, dan lainnya.',           40000,  'musik_video');

-- CREATIVE (5)
INSERT INTO products (name, description, price, category) VALUES
('Canva Pro',             'Desain grafis profesional tanpa batas. Template premium, brand kit, dan magic resize.',    35000,  'desain'),
('CapCut Pro',            'Edit video profesional dengan efek premium, tanpa watermark, dan fitur AI.',               25000,  'desain'),
('VSCO X',                'Filter & preset foto premium, tools editing canggih untuk Instagram aesthetic.',            20000,  'desain'),
('Lightroom Premium',     'Adobe Lightroom dengan preset premium dan cloud storage untuk editing foto mobile.',        28000,  'desain'),
('Alight Motion Pro',     'Motion graphics & animasi di HP. Efek premium, tanpa watermark.',                           22000,  'desain');

-- PRODUCTIVITY (4)
INSERT INTO products (name, description, price, category) VALUES
('ChatGPT Plus',          'Akses GPT-4o, respon lebih cepat, fitur analisis gambar dan DALL-E 3.',                    55000,  'productivity'),
('Zoom Pro',              'Meeting online tanpa batas waktu, recording cloud, hingga 100 peserta.',                    30000,  'productivity'),
('Google Drive 100GB',    'Penyimpanan cloud 100GB untuk backup file, foto, dan dokumen penting.',                     25000,  'productivity'),
('Microsoft 365',         'Word, Excel, PowerPoint, Outlook + 1TB OneDrive cloud storage.',                            40000,  'productivity');


-- ═══════════════════════════════════
-- SEED: Sample Stocks (2 per product)
-- ═══════════════════════════════════
INSERT INTO stocks (product_id, account_data) VALUES
(1, 'netflix01@mail.com|Pass123!'), (1, 'netflix02@mail.com|Pass456!'),
(2, 'spotify01@mail.com|Sp0t123'), (2, 'spotify02@mail.com|Sp0t456'),
(3, 'yt01@mail.com|YtPrem1'), (3, 'yt02@mail.com|YtPrem2'),
(4, 'disney01@mail.com|Dis123'), (4, 'disney02@mail.com|Dis456'),
(5, 'viu01@mail.com|ViuPrem1'), (5, 'viu02@mail.com|ViuPrem2'),
(6, 'wetv01@mail.com|WeTv123'), (6, 'wetv02@mail.com|WeTv456'),
(7, 'prime01@mail.com|Prime1'), (7, 'prime02@mail.com|Prime2'),
(8, 'hbo01@mail.com|HBOgo1'), (8, 'hbo02@mail.com|HBOgo2'),
(9, 'canva01@mail.com|Cnv123'), (9, 'canva02@mail.com|Cnv456'),
(10, 'capcut01@mail.com|Cap123'), (10, 'capcut02@mail.com|Cap456'),
(11, 'vsco01@mail.com|Vsco123'), (11, 'vsco02@mail.com|Vsco456'),
(12, 'lr01@mail.com|LrPrem1'), (12, 'lr02@mail.com|LrPrem2'),
(13, 'alight01@mail.com|Alight1'), (13, 'alight02@mail.com|Alight2'),
(14, 'gpt01@mail.com|GPT123'), (14, 'gpt02@mail.com|GPT456'),
(15, 'zoom01@mail.com|Zoom123'), (15, 'zoom02@mail.com|Zoom456'),
(16, 'gdrive01@mail.com|GDrv1'), (16, 'gdrive02@mail.com|GDrv2'),
(17, 'ms36501@mail.com|Ms365!'), (17, 'ms36502@mail.com|Ms365@');


-- ═══════════════════════════════════
-- SEED: Articles
-- ═══════════════════════════════════
INSERT INTO articles (title, slug, content) VALUES
('Cara Order di Website Natsy Premiums', 'cara-order-di-website-natsy-premiums',
 '<p>Membeli akun premium di Natsy Premiums sangat mudah! Cukup buka website, pilih produk yang kamu butuhkan, lalu klik <b>Beli Sekarang</b>.</p><h3>Langkah-langkah:</h3><ol><li>Pilih produk dari halaman utama</li><li>Isi nama dan nomor WhatsApp</li><li>Klik Checkout dan selesaikan pembayaran</li><li>Akun otomatis dikirim ke WhatsApp-mu</li></ol><p>Semua prosesnya cepat, aman, dan otomatis. Selamat berbelanja!</p>'),

('Tips Hemat Berlangganan Layanan Digital', 'tips-hemat-berlangganan-layanan-digital',
 '<p>Berlangganan layanan digital premium tidak harus mahal. Berikut tips hemat yang bisa kamu coba:</p><h3>1. Beli di Toko Terpercaya</h3><p>Pilih toko yang sudah terdaftar dan memiliki review positif seperti Natsy Premiums.</p><h3>2. Manfaatkan Akun Shared</h3><p>Banyak layanan premium yang menawarkan akun shared dengan harga lebih murah tanpa mengurangi fitur.</p><h3>3. Pantau Promo</h3><p>Follow media sosial kami untuk info promo dan diskon spesial.</p>'),

('Keuntungan Menggunakan Akun Premium', 'keuntungan-menggunakan-akun-premium',
 '<p>Menggunakan akun premium memberikan banyak keuntungan dibanding versi gratis:</p><ul><li><strong>Tanpa Iklan</strong> — Nikmati konten tanpa gangguan</li><li><strong>Kualitas Terbaik</strong> — Streaming 4K, audio lossless</li><li><strong>Fitur Eksklusif</strong> — Download offline, background play</li><li><strong>Multi Device</strong> — Gunakan di beberapa perangkat sekaligus</li></ul><p>Upgrade sekarang di Natsy Premiums dan rasakan perbedaannya!</p>');
