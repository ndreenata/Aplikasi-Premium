<?php
/**
 * KONEKSI.PHP — Database & Config & Security
 * Natsy Premiums
 */

$conn = new mysqli('localhost', 'root', '', 'premium_store');
if ($conn->connect_error) die("DB Error: " . $conn->connect_error);
$conn->set_charset("utf8mb4");

// Fonnte WhatsApp Token
define('FONNTE_TOKEN', 'LA46WFUmjRVEgNz6cqrV');
define('SITE_NAME', 'Natsy Premiums');
define('BASE_URL', 'http://localhost/premium');

// Session config
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_strict_mode', 1);
    session_start();
}

// ═══ SESSION TIMEOUT (30 minutes) ═══
define('SESSION_TIMEOUT', 1800); // 30 min
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > SESSION_TIMEOUT) {
    $was_logged_in = isset($_SESSION['user_id']);
    session_unset();
    session_destroy();
    session_start();
    if ($was_logged_in) {
        $_SESSION['flash'] = ['type' => 'info', 'msg' => 'Sesi kamu telah berakhir. Silakan masuk kembali.'];
    }
}
$_SESSION['last_activity'] = time();

// ═══ HELPERS ═══
function rupiah($n) { return 'Rp ' . number_format($n, 0, ',', '.'); }

function stockCount($conn, $pid) {
    $s = $conn->prepare("SELECT COUNT(*) as c FROM stocks WHERE product_id=? AND status='available'");
    $s->bind_param("i", $pid);
    $s->execute();
    $r = $s->get_result()->fetch_assoc()['c'];
    $s->close();
    return (int)$r;
}

function isLoggedIn() { return isset($_SESSION['user_id']); }
function currentUser() { return $_SESSION['user'] ?? null; }

// ═══ CSRF TOKEN ═══
function csrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrfField() {
    return '<input type="hidden" name="csrf_token" value="' . csrfToken() . '">';
}

function csrfCheck() {
    $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!hash_equals(csrfToken(), $token)) {
        http_response_code(403);
        die('<h2>403 — Forbidden</h2><p>Token keamanan tidak valid. <a href="javascript:history.back()">Kembali</a></p>');
    }
    // Regenerate token after successful check
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    return true;
}

// ═══ RATE LIMITING ═══
function getClientIP() {
    return $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['HTTP_CLIENT_IP'] ?? $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

function rateLimitCheck($conn, $action = 'login', $maxAttempts = 5, $windowSeconds = 900) {
    $ip = getClientIP();
    // Clean old entries
    $conn->query("DELETE FROM rate_limits WHERE last_attempt < DATE_SUB(NOW(), INTERVAL {$windowSeconds} SECOND)");
    // Check current
    $stmt = $conn->prepare("SELECT attempts FROM rate_limits WHERE ip_address=? AND action=?");
    $stmt->bind_param("ss", $ip, $action);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return !$row || $row['attempts'] < $maxAttempts;
}

function rateLimitRecord($conn, $action = 'login') {
    $ip = getClientIP();
    $stmt = $conn->prepare("INSERT INTO rate_limits (ip_address, action, attempts, last_attempt) VALUES (?,?,1,NOW()) ON DUPLICATE KEY UPDATE attempts = attempts + 1, last_attempt = NOW()");
    $stmt->bind_param("ss", $ip, $action);
    $stmt->execute();
    $stmt->close();
}

function rateLimitReset($conn, $action = 'login') {
    $ip = getClientIP();
    $stmt = $conn->prepare("DELETE FROM rate_limits WHERE ip_address=? AND action=?");
    $stmt->bind_param("ss", $ip, $action);
    $stmt->execute();
    $stmt->close();
}

// ═══ IP / LOGIN LOGGING ═══
function logLogin($conn, $user_id, $status = 'success') {
    $ip = getClientIP();
    $ua = substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 500);
    $stmt = $conn->prepare("INSERT INTO login_logs (user_id, ip_address, user_agent, status) VALUES (?,?,?,?)");
    $stmt->bind_param("isss", $user_id, $ip, $ua, $status);
    $stmt->execute();
    $stmt->close();
}

// ═══ AUTO-CREATE SECURITY TABLES ═══
$conn->query("CREATE TABLE IF NOT EXISTS rate_limits (
    ip_address VARCHAR(45) NOT NULL,
    action VARCHAR(30) NOT NULL DEFAULT 'login',
    attempts INT DEFAULT 1,
    last_attempt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (ip_address, action)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

$conn->query("CREATE TABLE IF NOT EXISTS login_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT DEFAULT NULL,
    ip_address VARCHAR(45) NOT NULL,
    user_agent VARCHAR(500) DEFAULT NULL,
    status ENUM('success','failed','blocked') DEFAULT 'success',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user (user_id),
    INDEX idx_ip (ip_address)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

// ═══ AUTO-CREATE FEATURE TABLES (Phase 2) ═══
$conn->query("CREATE TABLE IF NOT EXISTS user_points (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL UNIQUE,
    balance INT DEFAULT 0,
    total_earned INT DEFAULT 0,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

$conn->query("CREATE TABLE IF NOT EXISTS point_transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    amount INT NOT NULL,
    type ENUM('earn','spend') NOT NULL,
    description VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

$conn->query("CREATE TABLE IF NOT EXISTS user_badges (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    badge_key VARCHAR(50) NOT NULL,
    badge_name VARCHAR(100) NOT NULL,
    badge_icon VARCHAR(50) DEFAULT 'ri-award-fill',
    badge_color VARCHAR(20) DEFAULT 'green',
    earned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_badge (user_id, badge_key),
    FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

$conn->query("CREATE TABLE IF NOT EXISTS bundles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    description TEXT,
    discount_percent INT DEFAULT 10,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

$conn->query("CREATE TABLE IF NOT EXISTS bundle_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    bundle_id INT NOT NULL,
    product_id INT NOT NULL,
    FOREIGN KEY (bundle_id) REFERENCES bundles(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

$conn->query("CREATE TABLE IF NOT EXISTS newsletter_subscribers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(150) NOT NULL UNIQUE,
    name VARCHAR(100) DEFAULT NULL,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

$conn->query("CREATE TABLE IF NOT EXISTS banners (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    subtitle VARCHAR(255) DEFAULT NULL,
    image_url VARCHAR(500) DEFAULT NULL,
    link_url VARCHAR(500) DEFAULT NULL,
    is_active TINYINT(1) DEFAULT 1,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

$conn->query("CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    message TEXT,
    type ENUM('info','success','warning','promo') DEFAULT 'info',
    is_read TINYINT(1) DEFAULT 0,
    link VARCHAR(500) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    INDEX idx_user_read (user_id, is_read)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
// ═══ REVIEWS TABLE ═══
$conn->query("CREATE TABLE IF NOT EXISTS reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    rating TINYINT NOT NULL CHECK(rating BETWEEN 1 AND 5),
    review_text TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_review (user_id, product_id),
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

// ═══ REFERRAL COLUMNS ═══
$conn->query("ALTER TABLE users ADD COLUMN IF NOT EXISTS referral_code VARCHAR(20) DEFAULT NULL");
$conn->query("ALTER TABLE users ADD COLUMN IF NOT EXISTS referred_by INT DEFAULT NULL");

// ═══ REVIEW HELPERS ═══
function getProductRating($conn, $pid) {
    $s = $conn->prepare("SELECT AVG(rating) as avg_rating, COUNT(*) as count FROM reviews WHERE product_id=?");
    $s->bind_param("i", $pid); $s->execute();
    $r = $s->get_result()->fetch_assoc(); $s->close();
    return ['avg' => round($r['avg_rating'] ?? 0, 1), 'count' => (int)$r['count']];
}

function getUserReview($conn, $uid, $pid) {
    $s = $conn->prepare("SELECT * FROM reviews WHERE user_id=? AND product_id=? LIMIT 1");
    $s->bind_param("ii", $uid, $pid); $s->execute();
    $r = $s->get_result()->fetch_assoc(); $s->close();
    return $r;
}

function hasPurchased($conn, $uid, $pid) {
    $s = $conn->prepare("SELECT COUNT(*) as c FROM transactions WHERE user_id=? AND product_id=? AND status='SUCCESS'");
    $s->bind_param("ii", $uid, $pid); $s->execute();
    $r = $s->get_result()->fetch_assoc()['c']; $s->close();
    return $r > 0;
}

// ═══ SEASONAL THEME DETECTION ═══
function getSeasonalTheme() {
    global $conn;
    // Check admin-set theme from site_settings
    $result = $conn->query("SELECT setting_value FROM site_settings WHERE setting_key='active_theme' LIMIT 1");
    if ($result && $row = $result->fetch_assoc()) {
        $adminTheme = trim($row['setting_value']);
        if ($adminTheme && $adminTheme !== 'auto') {
            return $adminTheme === 'normal' ? '' : $adminTheme;
        }
    }
    // Auto-detect based on date
    $m = (int)date('m'); $d = (int)date('d');
    if (($m === 3 && $d >= 1) || ($m === 4 && $d <= 15)) return 'ramadan';
    if ($m === 4 && $d >= 10 && $d <= 16) return 'lebaran';
    if ($m === 12 && $d >= 15) return 'christmas';
    if ($m === 2 && $d >= 10 && $d <= 16) return 'valentine';
    if ($m === 8 && $d >= 10 && $d <= 20) return 'merdeka';
    if (($m === 1 && $d >= 4 && $d <= 14) || ($m === 7 && $d >= 31) || ($m === 8 && $d <= 10)) return 'galungan';
    if ($m === 1 && $d <= 5) return 'newyear';
    return '';
}

// ═══ GET SITE SETTING HELPER ═══
function getSetting($conn, $key, $default = '') {
    $stmt = $conn->prepare("SELECT setting_value FROM site_settings WHERE setting_key=? LIMIT 1");
    $stmt->bind_param("s", $key); $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) return $row['setting_value'];
    return $default;
}

// ═══ POINTS HELPER ═══
function addPoints($conn, $user_id, $amount, $description = '') {
    if ($amount <= 0 || !$user_id) return;
    $conn->query("INSERT INTO user_points (user_id, balance, total_earned) VALUES ($user_id, $amount, $amount)
        ON DUPLICATE KEY UPDATE balance = balance + $amount, total_earned = total_earned + $amount");
    $stmt = $conn->prepare("INSERT INTO point_transactions (user_id, amount, type, description) VALUES (?,?,'earn',?)");
    $stmt->bind_param("iis", $user_id, $amount, $description);
    $stmt->execute(); $stmt->close();
}

function getUserPoints($conn, $user_id) {
    $stmt = $conn->prepare("SELECT balance FROM user_points WHERE user_id=?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $r = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return $r ? (int)$r['balance'] : 0;
}

// ═══ BADGE HELPER ═══
function awardBadge($conn, $user_id, $key, $name, $icon = 'ri-award-fill', $color = 'green') {
    if (!$user_id) return;
    $stmt = $conn->prepare("INSERT IGNORE INTO user_badges (user_id, badge_key, badge_name, badge_icon, badge_color) VALUES (?,?,?,?,?)");
    $stmt->bind_param("issss", $user_id, $key, $name, $icon, $color);
    $stmt->execute(); $stmt->close();
}

// ═══ NOTIFICATION HELPER ═══
function addNotification($conn, $user_id, $title, $message = '', $type = 'info', $link = '') {
    if (!$user_id) return;
    $stmt = $conn->prepare("INSERT INTO notifications (user_id, title, message, type, link) VALUES (?,?,?,?,?)");
    $stmt->bind_param("issss", $user_id, $title, $message, $type, $link);
    $stmt->execute(); $stmt->close();
}

function getUnreadNotifCount($conn, $user_id) {
    if (!$user_id) return 0;
    $stmt = $conn->prepare("SELECT COUNT(*) as c FROM notifications WHERE user_id=? AND is_read=0");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $r = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return (int)$r['c'];
}
