-- ═══════════════════════════════════════════════════
-- NATSY PREMIUMS — COMPREHENSIVE FEATURE MIGRATION
-- 34 New Features Database Schema
-- ═══════════════════════════════════════════════════

-- ─── Reviews & Ratings (#43) ───
CREATE TABLE IF NOT EXISTS reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    transaction_id INT,
    rating TINYINT NOT NULL DEFAULT 5,
    comment TEXT,
    is_approved TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_review (user_id, product_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ─── Store Credit / Saldo (#20) ───
ALTER TABLE users ADD COLUMN IF NOT EXISTS store_credit DECIMAL(12,2) DEFAULT 0;
ALTER TABLE users ADD COLUMN IF NOT EXISTS loyalty_points INT DEFAULT 0;
ALTER TABLE users ADD COLUMN IF NOT EXISTS tier ENUM('bronze','silver','gold','platinum') DEFAULT 'bronze';

CREATE TABLE IF NOT EXISTS credit_transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    amount DECIMAL(12,2) NOT NULL,
    type ENUM('topup','purchase','refund','reward','referral') NOT NULL,
    description VARCHAR(255),
    balance_after DECIMAL(12,2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ─── Subscriptions (#22) ───
CREATE TABLE IF NOT EXISTS subscriptions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    plan ENUM('monthly','quarterly','yearly') DEFAULT 'monthly',
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    auto_renew TINYINT(1) DEFAULT 1,
    status ENUM('active','expired','cancelled','paused') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ─── Invoices (#23) ───
CREATE TABLE IF NOT EXISTS invoices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    transaction_id INT,
    invoice_number VARCHAR(50) UNIQUE NOT NULL,
    user_id INT NOT NULL,
    product_name VARCHAR(255),
    amount DECIMAL(12,2),
    tax DECIMAL(12,2) DEFAULT 0,
    total DECIMAL(12,2),
    status ENUM('paid','pending','cancelled') DEFAULT 'paid',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ─── Custom Product Requests (#25) ───
CREATE TABLE IF NOT EXISTS product_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    guest_name VARCHAR(100),
    guest_email VARCHAR(150),
    product_name VARCHAR(255) NOT NULL,
    description TEXT,
    budget VARCHAR(100),
    status ENUM('pending','reviewing','approved','rejected','fulfilled') DEFAULT 'pending',
    admin_notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ─── Restock Notifications (#10) ───
CREATE TABLE IF NOT EXISTS restock_notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    product_id INT NOT NULL,
    email VARCHAR(150),
    whatsapp VARCHAR(20),
    notified TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ─── Flash Sales Scheduler (#2) ───
CREATE TABLE IF NOT EXISTS flash_sales (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    discount_percent INT NOT NULL DEFAULT 10,
    start_time DATETIME NOT NULL,
    end_time DATETIME NOT NULL,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS flash_sale_products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    flash_sale_id INT NOT NULL,
    product_id INT NOT NULL,
    special_price DECIMAL(12,2),
    FOREIGN KEY (flash_sale_id) REFERENCES flash_sales(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ─── Activity Log (#32) ───
CREATE TABLE IF NOT EXISTS activity_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action VARCHAR(100) NOT NULL,
    entity_type VARCHAR(50),
    entity_id INT,
    details TEXT,
    ip_address VARCHAR(45),
    user_agent VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ─── 2FA Two-Factor Auth (#34) ───
CREATE TABLE IF NOT EXISTS user_2fa (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL UNIQUE,
    method ENUM('email','whatsapp') DEFAULT 'email',
    is_enabled TINYINT(1) DEFAULT 0,
    otp_code VARCHAR(6),
    otp_expires_at DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ─── Service Status (#35) ───
CREATE TABLE IF NOT EXISTS service_status (
    id INT AUTO_INCREMENT PRIMARY KEY,
    service_name VARCHAR(100) NOT NULL,
    product_id INT,
    status ENUM('operational','degraded','maintenance','outage') DEFAULT 'operational',
    message TEXT,
    updated_by INT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed initial service statuses
INSERT IGNORE INTO service_status (service_name, product_id, status, message) VALUES
('Netflix Premium', 1, 'operational', 'Semua berjalan normal'),
('Spotify Premium', 2, 'operational', 'Semua berjalan normal'),
('YouTube Premium', 3, 'operational', 'Semua berjalan normal'),
('Disney+ Hotstar', 4, 'operational', 'Semua berjalan normal'),
('Canva Pro', NULL, 'operational', 'Semua berjalan normal'),
('ChatGPT Plus', NULL, 'operational', 'Semua berjalan normal');

-- ─── Warranty & Replacements (#36, #37) ───
CREATE TABLE IF NOT EXISTS warranties (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    transaction_id INT NOT NULL,
    product_id INT NOT NULL,
    purchase_date DATE NOT NULL,
    expiry_date DATE NOT NULL,
    status ENUM('active','expired','claimed','void') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS replacement_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    warranty_id INT NOT NULL,
    user_id INT NOT NULL,
    reason TEXT NOT NULL,
    status ENUM('pending','processing','completed','rejected') DEFAULT 'pending',
    admin_notes TEXT,
    resolved_at DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (warranty_id) REFERENCES warranties(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ─── Blog / Tutorial System (#39) ───
CREATE TABLE IF NOT EXISTS blog_posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    excerpt TEXT,
    content LONGTEXT,
    cover_image VARCHAR(255),
    category ENUM('tutorial','news','tips','promo') DEFAULT 'tutorial',
    author_id INT,
    views INT DEFAULT 0,
    is_published TINYINT(1) DEFAULT 0,
    published_at DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed sample blog posts
INSERT IGNORE INTO blog_posts (id, title, slug, excerpt, content, category, author_id, is_published, published_at) VALUES
(1, 'Cara Menggunakan Netflix Shared Account dengan Aman', 'cara-netflix-shared-account', 'Panduan lengkap cara menggunakan akun Netflix shared tanpa masalah.', '<h2>Cara Aman Menggunakan Netflix Shared</h2><p>Berikut langkah-langkah yang perlu kamu ikuti untuk menggunakan akun Netflix shared dengan aman dan nyaman...</p><h3>1. Jangan Ubah Password</h3><p>Pastikan kamu tidak mengubah password akun yang diberikan.</p><h3>2. Gunakan Profil yang Ditentukan</h3><p>Login dan gunakan profil yang sudah ditentukan oleh admin.</p><h3>3. Jangan Share ke Orang Lain</h3><p>Akun hanya untuk kamu pribadi, jangan share ke orang lain.</p>', 'tutorial', 1, 1, NOW()),
(2, 'Tips Hemat Berlangganan Premium', 'tips-hemat-berlangganan', 'Mau langganan premium tapi hemat? Simak tips berikut!', '<h2>Tips Hemat Langganan Premium</h2><p>Berlangganan akun premium tidak harus mahal. Berikut tips agar kamu bisa hemat...</p><h3>1. Pilih Shared Account</h3><p>Shared account jauh lebih murah dibanding private.</p><h3>2. Manfaatkan Flash Sale</h3><p>Pantau terus flash sale kami untuk diskon besar-besaran.</p><h3>3. Gunakan Bundle</h3><p>Beli paket bundle lebih hemat dari beli satuan.</p>', 'tips', 1, 1, NOW()),
(3, 'Promo Spesial Bulan Ini!', 'promo-spesial-bulan-ini', 'Diskon hingga 50% untuk semua produk streaming!', '<h2>Promo Spesial!</h2><p>Bulan ini kami memberikan diskon besar-besaran untuk semua produk streaming. Jangan sampai kelewatan!</p>', 'promo', 1, 1, NOW());

-- ─── User Leaderboard data (#46) ───
ALTER TABLE users ADD COLUMN IF NOT EXISTS total_purchases INT DEFAULT 0;
ALTER TABLE users ADD COLUMN IF NOT EXISTS total_spent DECIMAL(12,2) DEFAULT 0;

-- ─── Admin Notifications (#28) ───
CREATE TABLE IF NOT EXISTS admin_notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type ENUM('order','restock','request','review','replacement','system') NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT,
    reference_id INT,
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ─── Customer Segments (#30) ───
CREATE TABLE IF NOT EXISTS customer_segments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    conditions JSON,
    user_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO customer_segments (id, name, description, conditions) VALUES
(1, 'VIP Customers', 'Pelanggan dengan total belanja > Rp 500.000', '{"min_spent": 500000}'),
(2, 'New Users', 'User yang baru daftar 30 hari terakhir', '{"days_since_register": 30}'),
(3, 'Inactive Users', 'User yang tidak order 60+ hari', '{"days_since_order": 60}'),
(4, 'Frequent Buyers', 'User dengan 5+ transaksi', '{"min_purchases": 5}');

-- ─── Database Backups (#51) ───
CREATE TABLE IF NOT EXISTS database_backups (
    id INT AUTO_INCREMENT PRIMARY KEY,
    filename VARCHAR(255) NOT NULL,
    size_bytes BIGINT,
    type ENUM('manual','scheduled') DEFAULT 'manual',
    status ENUM('completed','failed','in_progress') DEFAULT 'completed',
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ─── Performance Metrics (#52) ───
CREATE TABLE IF NOT EXISTS performance_metrics (
    id INT AUTO_INCREMENT PRIMARY KEY,
    page_url VARCHAR(255),
    load_time_ms INT,
    memory_usage_mb DECIMAL(8,2),
    db_queries INT,
    error_count INT DEFAULT 0,
    recorded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ─── Rate Limiting (#50) ───
CREATE TABLE IF NOT EXISTS rate_limits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ip_address VARCHAR(45) NOT NULL,
    endpoint VARCHAR(255) NOT NULL,
    request_count INT DEFAULT 1,
    window_start TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_ip_endpoint (ip_address, endpoint)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ─── Multi-Currency settings (#24) ───
INSERT IGNORE INTO site_settings (setting_key, setting_value) VALUES
('currency_default', 'IDR'),
('currency_usd_rate', '0.000063'),
('currency_myr_rate', '0.00028'),
('currency_sgd_rate', '0.000085');

-- ─── Site settings additions ───
INSERT IGNORE INTO site_settings (setting_key, setting_value) VALUES
('chat_widget_enabled', '1'),
('chat_widget_url', 'https://wa.me/6281234567890'),
('social_instagram', 'https://instagram.com/natsypremiums'),
('social_tiktok', 'https://tiktok.com/@natsypremiums'),
('social_youtube', ''),
('pwa_enabled', '1'),
('accessibility_font_sizes', '1'),
('backup_schedule', 'weekly'),
('maintenance_mode', '0');
