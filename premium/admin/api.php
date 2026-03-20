<?php
/**
 * ADMIN/API.PHP — Single AJAX endpoint for all admin operations
 * Includes: audit logging, stock logs, voucher CRUD, image upload
 */
require_once __DIR__ . '/../includes/koneksi.php';

header('Content-Type: application/json');

// Public-facing actions that don't require admin
$publicActions = ['voucher_validate', 'review_save', 'newsletter_subscribe', 'notifications_get', 'notifications_read_all'];
$action = $_POST['action'] ?? $_GET['action'] ?? '';

// Actions that require login but not admin
$userActions = ['notifications_get', 'notifications_read_all'];

if (!in_array($action, $publicActions)) {
    // Auth check - admin only
    if (!isLoggedIn() || (currentUser()['role'] ?? '') !== 'admin') {
        echo json_encode(['ok' => false, 'msg' => 'Unauthorized']); exit;
    }
} elseif (in_array($action, $userActions) && !isLoggedIn()) {
    echo json_encode(['ok' => false, 'msg' => 'Login required']); exit;
}

$admin = currentUser();

// ═══════════════════ AUDIT LOG HELPER ═══════════════════
function logAction($conn, $admin, $action, $entity_type = null, $entity_id = null, $details = null) {
    $stmt = $conn->prepare("INSERT INTO audit_logs (admin_id, admin_name, action, entity_type, entity_id, details, ip_address) VALUES (?,?,?,?,?,?,?)");
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    $name = $admin['name'] ?? 'Unknown';
    $stmt->bind_param("isssis", $admin['id'], $name, $action, $entity_type, $entity_id, $details, $ip);
    $stmt->execute();
    $stmt->close();
}

// ═══════════════════ STOCK LOG HELPER ═══════════════════
function logStock($conn, $stock_id, $product_id, $action, $masked_data, $performed_by, $buyer_info = null) {
    $stmt = $conn->prepare("INSERT INTO stock_logs (stock_id, product_id, action, account_data_masked, performed_by, buyer_info) VALUES (?,?,?,?,?,?)");
    $stmt->bind_param("iissis", $stock_id, $product_id, $action, $masked_data, $performed_by, $buyer_info);
    $stmt->execute();
    $stmt->close();
}

function maskAccount($data) {
    $parts = explode('|', $data);
    $email = $parts[0] ?? $data;
    $at = strpos($email, '@');
    if ($at !== false && $at > 2) {
        return substr($email, 0, 2) . str_repeat('*', $at - 2) . substr($email, $at);
    }
    return substr($data, 0, 3) . '***';
}

switch ($action) {

    // ═══════════════════ PRODUCTS ═══════════════════
    case 'product_save':
        $id   = (int)($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $desc = trim($_POST['description'] ?? '');
        $price = (float)($_POST['price'] ?? 0);
        $cost  = (float)($_POST['cost_price'] ?? 0);
        $cat  = trim($_POST['category'] ?? 'productivity');
        $active = (int)($_POST['is_active'] ?? 1);

        if (empty($name) || $price <= 0) {
            echo json_encode(['ok' => false, 'msg' => 'Nama dan harga wajib diisi']); exit;
        }

        // Handle image upload
        $imagePath = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../assets/products/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg','jpeg','png','webp','gif'];
            if (!in_array($ext, $allowed)) {
                echo json_encode(['ok' => false, 'msg' => 'Format gambar tidak valid (jpg/png/webp/gif)']); exit;
            }
            if ($_FILES['image']['size'] > 2 * 1024 * 1024) {
                echo json_encode(['ok' => false, 'msg' => 'Ukuran gambar max 2MB']); exit;
            }
            $fileName = 'product_' . time() . '_' . mt_rand(1000,9999) . '.' . $ext;
            move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $fileName);
            $imagePath = 'assets/products/' . $fileName;
        }

        if ($id > 0) {
            if ($imagePath) {
                $stmt = $conn->prepare("UPDATE products SET name=?, description=?, price=?, cost_price=?, category=?, is_active=?, image=? WHERE id=?");
                $stmt->bind_param("ssddsisi", $name, $desc, $price, $cost, $cat, $active, $imagePath, $id);
            } else {
                $stmt = $conn->prepare("UPDATE products SET name=?, description=?, price=?, cost_price=?, category=?, is_active=? WHERE id=?");
                $stmt->bind_param("ssddsis", $name, $desc, $price, $cost, $cat, $active, $id);
            }
            $stmt->execute(); $stmt->close();
            logAction($conn, $admin, 'product_update', 'product', $id, "Updated: $name, price=$price, cost=$cost");
            echo json_encode(['ok' => true, 'msg' => 'Produk berhasil diupdate']);
        } else {
            $stmt = $conn->prepare("INSERT INTO products (name, description, price, cost_price, category, is_active, image) VALUES (?,?,?,?,?,?,?)");
            $img = $imagePath ?? null;
            $stmt->bind_param("ssddsss", $name, $desc, $price, $cost, $cat, $active, $img);
            $stmt->execute();
            $newId = $conn->insert_id;
            $stmt->close();
            logAction($conn, $admin, 'product_create', 'product', $newId, "Created: $name, price=$price");
            echo json_encode(['ok' => true, 'msg' => 'Produk berhasil ditambahkan']);
        }
        break;

    case 'product_toggle':
        $id = (int)($_POST['id'] ?? 0);
        $active = (int)($_POST['is_active'] ?? 0);
        $conn->query("UPDATE products SET is_active=$active WHERE id=$id");
        logAction($conn, $admin, 'product_toggle', 'product', $id, "Set is_active=$active");
        echo json_encode(['ok' => true, 'msg' => 'Status produk diubah']);
        break;

    case 'product_delete':
        $id = (int)($_POST['id'] ?? 0);
        $has = $conn->query("SELECT COUNT(*) c FROM transactions WHERE product_id=$id")->fetch_assoc()['c'];
        if ($has > 0) {
            echo json_encode(['ok' => false, 'msg' => 'Tidak bisa hapus — produk memiliki transaksi']);
        } else {
            $pName = $conn->query("SELECT name FROM products WHERE id=$id")->fetch_assoc()['name'] ?? '';
            $conn->query("DELETE FROM stocks WHERE product_id=$id");
            $conn->query("DELETE FROM products WHERE id=$id");
            logAction($conn, $admin, 'product_delete', 'product', $id, "Deleted: $pName");
            echo json_encode(['ok' => true, 'msg' => 'Produk dihapus']);
        }
        break;

    // ═══════════════════ STOCKS ═══════════════════
    case 'stock_add':
        $pid = (int)($_POST['product_id'] ?? 0);
        $data = trim($_POST['account_data'] ?? '');
        if ($pid <= 0 || empty($data)) {
            echo json_encode(['ok' => false, 'msg' => 'Pilih produk dan isi data akun']); exit;
        }
        $lines = array_filter(array_map('trim', explode("\n", $data)));
        $count = 0;
        $stmt = $conn->prepare("INSERT INTO stocks (product_id, account_data, status) VALUES (?, ?, 'available')");
        foreach ($lines as $line) {
            if (empty($line)) continue;
            $stmt->bind_param("is", $pid, $line);
            $stmt->execute();
            $stockId = $conn->insert_id;
            logStock($conn, $stockId, $pid, 'added', maskAccount($line), $admin['id']);
            $count++;
        }
        $stmt->close();
        logAction($conn, $admin, 'stock_add', 'stock', $pid, "Added $count stocks for product #$pid");
        echo json_encode(['ok' => true, 'msg' => "$count stok berhasil ditambahkan"]);
        break;

    case 'stock_delete':
        $id = (int)($_POST['id'] ?? 0);
        $stock = $conn->query("SELECT * FROM stocks WHERE id=$id AND status='available'")->fetch_assoc();
        if ($stock) {
            logStock($conn, $id, $stock['product_id'], 'deleted', maskAccount($stock['account_data']), $admin['id']);
            $conn->query("DELETE FROM stocks WHERE id=$id AND status='available'");
            logAction($conn, $admin, 'stock_delete', 'stock', $id, "Deleted stock #$id");
        }
        echo json_encode(['ok' => true, 'msg' => 'Stok dihapus']);
        break;

    // ═══════════════════ TRANSACTIONS ═══════════════════
    case 'transaction_update':
        $id = (int)($_POST['id'] ?? 0);
        $status = $_POST['status'] ?? '';
        if (!in_array($status, ['SUCCESS','FAILED'])) {
            echo json_encode(['ok' => false, 'msg' => 'Status tidak valid']); exit;
        }
        $conn->query("UPDATE transactions SET status='$status' WHERE id=$id");

        if ($status === 'SUCCESS') {
            $tx = $conn->query("SELECT t.*, p.name as product_name FROM transactions t JOIN products p ON t.product_id=p.id WHERE t.id=$id")->fetch_assoc();
            if ($tx) {
                $soldStock = $conn->query("SELECT id, account_data FROM stocks WHERE product_id={$tx['product_id']} AND status='available' LIMIT 1")->fetch_assoc();
                if ($soldStock) {
                    $conn->query("UPDATE stocks SET status='sold' WHERE id={$soldStock['id']}");
                    logStock($conn, $soldStock['id'], $tx['product_id'], 'sold', maskAccount($soldStock['account_data']), $admin['id'], $tx['customer_name'] ?? 'N/A');
                }
            }
        }

        logAction($conn, $admin, 'transaction_update', 'transaction', $id, "Status changed to $status");
        echo json_encode(['ok' => true, 'msg' => "Transaksi di-update ke $status"]);
        break;

    // ═══════════════════ USERS ═══════════════════
    case 'user_toggle_role':
        $id = (int)($_POST['id'] ?? 0);
        $role = $_POST['role'] ?? 'user';
        if (!in_array($role, ['user','admin'])) {
            echo json_encode(['ok' => false, 'msg' => 'Role tidak valid']); exit;
        }
        if ($id == $admin['id']) {
            echo json_encode(['ok' => false, 'msg' => 'Tidak bisa mengubah role sendiri']); exit;
        }
        $stmt = $conn->prepare("UPDATE users SET role=? WHERE id=?");
        $stmt->bind_param("si", $role, $id);
        $stmt->execute(); $stmt->close();
        logAction($conn, $admin, 'user_role_change', 'user', $id, "Role changed to $role");
        echo json_encode(['ok' => true, 'msg' => "Role diubah ke $role"]);
        break;

    // ═══════════════════ ARTICLES ═══════════════════
    case 'article_save':
        $id = (int)($_POST['id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $slug = trim($_POST['slug'] ?? '');
        $content = $_POST['content'] ?? '';

        if (empty($title)) {
            echo json_encode(['ok' => false, 'msg' => 'Judul wajib diisi']); exit;
        }
        if (empty($slug)) {
            $slug = preg_replace('/[^a-z0-9\s-]/', '', strtolower($title));
            $slug = preg_replace('/[\s-]+/', '-', $slug);
        }

        if ($id > 0) {
            $stmt = $conn->prepare("UPDATE articles SET title=?, slug=?, content=? WHERE id=?");
            $stmt->bind_param("sssi", $title, $slug, $content, $id);
            $stmt->execute(); $stmt->close();
            logAction($conn, $admin, 'article_update', 'article', $id, "Updated: $title");
            echo json_encode(['ok' => true, 'msg' => 'Artikel berhasil diupdate']);
        } else {
            $stmt = $conn->prepare("INSERT INTO articles (title, slug, content) VALUES (?,?,?)");
            $stmt->bind_param("sss", $title, $slug, $content);
            $stmt->execute();
            $newId = $conn->insert_id;
            $stmt->close();
            logAction($conn, $admin, 'article_create', 'article', $newId, "Created: $title");
            echo json_encode(['ok' => true, 'msg' => 'Artikel berhasil ditambahkan']);
        }
        break;

    case 'article_delete':
        $id = (int)($_POST['id'] ?? 0);
        $art = $conn->query("SELECT title FROM articles WHERE id=$id")->fetch_assoc();
        $conn->query("DELETE FROM articles WHERE id=$id");
        logAction($conn, $admin, 'article_delete', 'article', $id, "Deleted: " . ($art['title'] ?? ''));
        echo json_encode(['ok' => true, 'msg' => 'Artikel dihapus']);
        break;

    // ═══════════════════ VOUCHERS ═══════════════════
    case 'voucher_save':
        $id = (int)($_POST['id'] ?? 0);
        $code = strtoupper(trim($_POST['code'] ?? ''));
        $type = $_POST['discount_type'] ?? 'percent';
        $value = (float)($_POST['discount_value'] ?? 0);
        $minPurchase = (float)($_POST['min_purchase'] ?? 0);
        $limit = !empty($_POST['usage_limit']) ? (int)$_POST['usage_limit'] : null;
        $expires = !empty($_POST['expires_at']) ? $_POST['expires_at'] : null;

        if (empty($code) || $value <= 0) {
            echo json_encode(['ok' => false, 'msg' => 'Kode dan nilai diskon wajib diisi']); exit;
        }
        if (!in_array($type, ['percent','fixed'])) {
            echo json_encode(['ok' => false, 'msg' => 'Tipe diskon tidak valid']); exit;
        }
        if ($type === 'percent' && $value > 100) {
            echo json_encode(['ok' => false, 'msg' => 'Diskon persen maksimal 100%']); exit;
        }

        if ($id > 0) {
            $stmt = $conn->prepare("UPDATE vouchers SET code=?, discount_type=?, discount_value=?, min_purchase=?, usage_limit=?, expires_at=? WHERE id=?");
            $stmt->bind_param("ssddisd", $code, $type, $value, $minPurchase, $limit, $expires, $id);
            $stmt->execute(); $stmt->close();
            logAction($conn, $admin, 'voucher_update', 'voucher', $id, "Updated: $code ($type $value)");
            echo json_encode(['ok' => true, 'msg' => 'Voucher berhasil diupdate']);
        } else {
            $stmt = $conn->prepare("INSERT INTO vouchers (code, discount_type, discount_value, min_purchase, usage_limit, expires_at) VALUES (?,?,?,?,?,?)");
            $stmt->bind_param("ssddis", $code, $type, $value, $minPurchase, $limit, $expires);
            $stmt->execute();
            $newId = $conn->insert_id;
            $stmt->close();
            logAction($conn, $admin, 'voucher_create', 'voucher', $newId, "Created: $code ($type $value)");
            echo json_encode(['ok' => true, 'msg' => 'Voucher berhasil ditambahkan']);
        }
        break;

    case 'voucher_toggle':
        $id = (int)($_POST['id'] ?? 0);
        $active = (int)($_POST['is_active'] ?? 0);
        $conn->query("UPDATE vouchers SET is_active=$active WHERE id=$id");
        logAction($conn, $admin, 'voucher_toggle', 'voucher', $id, "Set is_active=$active");
        echo json_encode(['ok' => true, 'msg' => 'Status voucher diubah']);
        break;

    case 'voucher_delete':
        $id = (int)($_POST['id'] ?? 0);
        $v = $conn->query("SELECT code FROM vouchers WHERE id=$id")->fetch_assoc();
        $conn->query("DELETE FROM vouchers WHERE id=$id");
        logAction($conn, $admin, 'voucher_delete', 'voucher', $id, "Deleted: " . ($v['code'] ?? ''));
        echo json_encode(['ok' => true, 'msg' => 'Voucher dihapus']);
        break;

    case 'voucher_validate':
        // Public-facing validation (used from cart) — with anti-double-claim
        $code = strtoupper(trim($_POST['code'] ?? ''));
        $subtotal = (float)($_POST['subtotal'] ?? 0);
        $userId = isLoggedIn() ? (int)$_SESSION['user_id'] : null;
        $phone = trim($_POST['phone'] ?? '');

        $stmt = $conn->prepare("SELECT * FROM vouchers WHERE code=? AND is_active=1");
        $stmt->bind_param("s", $code);
        $stmt->execute();
        $voucher = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$voucher) {
            echo json_encode(['ok' => false, 'msg' => 'Kode voucher tidak ditemukan']); exit;
        }
        if ($voucher['expires_at'] && strtotime($voucher['expires_at']) < time()) {
            echo json_encode(['ok' => false, 'msg' => 'Voucher sudah expired']); exit;
        }
        if ($voucher['usage_limit'] !== null && $voucher['used_count'] >= $voucher['usage_limit']) {
            echo json_encode(['ok' => false, 'msg' => 'Voucher sudah mencapai batas pemakaian']); exit;
        }
        if ($subtotal < $voucher['min_purchase']) {
            echo json_encode(['ok' => false, 'msg' => 'Minimal pembelian Rp ' . number_format($voucher['min_purchase'],0,',','.')]); exit;
        }

        // Anti-double-claim: check if this user/phone already used this voucher
        if ($userId) {
            $chk = $conn->prepare("SELECT id FROM voucher_usage WHERE voucher_id=? AND user_id=?");
            $chk->bind_param("ii", $voucher['id'], $userId);
            $chk->execute();
            if ($chk->get_result()->num_rows > 0) {
                $chk->close();
                echo json_encode(['ok' => false, 'msg' => 'Kamu sudah pernah menggunakan voucher ini']); exit;
            }
            $chk->close();
        }
        if (!empty($phone)) {
            $chk = $conn->prepare("SELECT id FROM voucher_usage WHERE voucher_id=? AND phone_number=?");
            $chk->bind_param("is", $voucher['id'], $phone);
            $chk->execute();
            if ($chk->get_result()->num_rows > 0) {
                $chk->close();
                echo json_encode(['ok' => false, 'msg' => 'Nomor ini sudah pernah menggunakan voucher ini']); exit;
            }
            $chk->close();
        }

        $discount = $voucher['discount_type'] === 'percent'
            ? round($subtotal * $voucher['discount_value'] / 100)
            : $voucher['discount_value'];
        if ($discount > $subtotal) $discount = $subtotal;

        echo json_encode([
            'ok' => true,
            'msg' => 'Voucher berhasil digunakan!',
            'discount' => $discount,
            'discount_type' => $voucher['discount_type'],
            'discount_value' => $voucher['discount_value'],
            'code' => $voucher['code']
        ]);
        break;
    // ═══════════════════ REVIEWS (public-facing) ═══════════════════
    case 'review_save':
        if (!isLoggedIn()) {
            echo json_encode(['ok' => false, 'msg' => 'Silakan login terlebih dahulu']); exit;
        }
        $userId = (int)$_SESSION['user_id'];
        $productId = (int)($_POST['product_id'] ?? 0);
        $transactionId = (int)($_POST['transaction_id'] ?? 0);
        $rating = max(1, min(5, (int)($_POST['rating'] ?? 5)));
        $reviewText = trim($_POST['review_text'] ?? '');

        if ($productId <= 0 || $transactionId <= 0) {
            echo json_encode(['ok' => false, 'msg' => 'Data tidak valid']); exit;
        }
        if (empty($reviewText)) {
            echo json_encode(['ok' => false, 'msg' => 'Ulasan tidak boleh kosong']); exit;
        }

        // Verify transaction belongs to user and is SUCCESS
        $chk = $conn->prepare("SELECT id FROM transactions WHERE id=? AND user_id=? AND product_id=? AND status='SUCCESS'");
        $chk->bind_param("iii", $transactionId, $userId, $productId);
        $chk->execute();
        if ($chk->get_result()->num_rows === 0) {
            $chk->close();
            echo json_encode(['ok' => false, 'msg' => 'Transaksi tidak valid atau belum SUCCESS']); exit;
        }
        $chk->close();

        // Check if already reviewed this transaction
        $chk2 = $conn->prepare("SELECT id FROM reviews WHERE transaction_id=? AND user_id=?");
        $chk2->bind_param("ii", $transactionId, $userId);
        $chk2->execute();
        if ($chk2->get_result()->num_rows > 0) {
            $chk2->close();
            echo json_encode(['ok' => false, 'msg' => 'Kamu sudah memberikan ulasan untuk transaksi ini']); exit;
        }
        $chk2->close();

        $stmt = $conn->prepare("INSERT INTO reviews (user_id, product_id, transaction_id, rating, review_text) VALUES (?,?,?,?,?)");
        $stmt->bind_param("iiiis", $userId, $productId, $transactionId, $rating, $reviewText);
        $stmt->execute();
        $stmt->close();

        echo json_encode(['ok' => true, 'msg' => 'Terima kasih atas ulasan kamu!']);
        break;

    case 'review_delete':
        $id = (int)($_POST['id'] ?? 0);
        $conn->query("DELETE FROM reviews WHERE id=$id");
        echo json_encode(['ok' => true, 'msg' => 'Review dihapus']);
        break;

    // ═══════════════════ NEWSLETTER ═══════════════════
    case 'newsletter_subscribe':
        $email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
        if (!$email) { echo json_encode(['ok' => false, 'msg' => 'Email tidak valid']); break; }
        $stmt = $conn->prepare("INSERT IGNORE INTO newsletter_subscribers (email) VALUES (?)");
        $stmt->bind_param("s", $email); $stmt->execute(); $stmt->close();
        echo json_encode(['ok' => true, 'msg' => 'Berhasil subscribe!']);
        break;

    // ═══════════════════ NOTIFICATIONS ═══════════════════
    case 'notifications_get':
        $uid = $_SESSION['user_id'];
        $stmt = $conn->prepare("SELECT id, title, message, type, is_read, link, created_at FROM notifications WHERE user_id=? ORDER BY created_at DESC LIMIT 20");
        $stmt->bind_param("i", $uid); $stmt->execute();
        $result = $stmt->get_result();
        $notifs = [];
        while ($row = $result->fetch_assoc()) { $notifs[] = $row; }
        $stmt->close();
        echo json_encode(['ok' => true, 'data' => $notifs]);
        break;

    case 'notifications_read_all':
        $uid = $_SESSION['user_id'];
        $conn->query("UPDATE notifications SET is_read=1 WHERE user_id=$uid AND is_read=0");
        echo json_encode(['ok' => true, 'msg' => 'Semua notifikasi ditandai dibaca']);
        break;

    default:
        echo json_encode(['ok' => false, 'msg' => 'Unknown action']);
}
