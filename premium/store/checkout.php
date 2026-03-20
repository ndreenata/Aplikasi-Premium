<?php
/**
 * CHECKOUT.PHP — Process Order
 * Supports both single-item and cart (multi-item) checkout
 * Validasi → Cek stok → Simpan PENDING → Redirect dummy payment
 */
require_once __DIR__ . '/../includes/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: ' . BASE_URL . '/index.php'); exit; }
csrfCheck();

$customer_name = trim($_POST['customer_name'] ?? '');
$phone         = preg_replace('/[^0-9]/', '', $_POST['phone'] ?? ($_POST['phone_number'] ?? ''));
$user_id       = isLoggedIn() ? $_SESSION['user_id'] : null;
$from_cart     = isset($_POST['from_cart']);

// Validasi
if (empty($customer_name) || strlen($phone) < 9) {
    $_SESSION['flash'] = ['type'=>'error','msg'=>'Data tidak valid. Pastikan semua field terisi.'];
    header('Location: ' . BASE_URL . ($from_cart ? '/store/cart.php' : '/index.php')); exit;
}

// Format phone
if (substr($phone,0,1)==='0') $phone = '62'.substr($phone,1);
elseif (substr($phone,0,2)!=='62') $phone = '62'.$phone;

// Build list of products to checkout
$checkoutItems = [];

if ($from_cart) {
    // Cart checkout: get items from session
    if (empty($_SESSION['cart'])) {
        $_SESSION['flash'] = ['type'=>'error','msg'=>'Keranjang kosong.'];
        header('Location: ' . BASE_URL . '/store/cart.php'); exit;
    }
    foreach ($_SESSION['cart'] as $item) {
        $checkoutItems[] = $item['product_id'];
    }
} else {
    // Single item checkout
    $product_id = (int)($_POST['product_id'] ?? 0);
    if ($product_id <= 0) {
        $_SESSION['flash'] = ['type'=>'error','msg'=>'Produk tidak valid.'];
        header('Location: ' . BASE_URL . '/index.php'); exit;
    }
    $checkoutItems[] = $product_id;
}

// Voucher handling
$voucherCode = trim($_POST['voucher_code'] ?? '');
$voucherDiscount = 0;
if (!empty($voucherCode)) {
    $vstmt = $conn->prepare("SELECT * FROM vouchers WHERE code=? AND is_active=1");
    $vstmt->bind_param("s", $voucherCode);
    $vstmt->execute();
    $voucher = $vstmt->get_result()->fetch_assoc();
    $vstmt->close();
    if ($voucher && (!$voucher['expires_at'] || strtotime($voucher['expires_at']) >= time())
        && ($voucher['usage_limit'] === null || $voucher['used_count'] < $voucher['usage_limit'])) {
        $voucherDiscount = (float)($_POST['voucher_discount'] ?? 0);
        // Increment used_count
        $conn->query("UPDATE vouchers SET used_count = used_count + 1 WHERE id = {$voucher['id']}");
        // Record usage (anti-double-claim)
        $vu = $conn->prepare("INSERT INTO voucher_usage (voucher_id, user_id, phone_number) VALUES (?,?,?)");
        $vu->bind_param("iis", $voucher['id'], $user_id, $phone);
        $vu->execute();
        $vu->close();
    }
}

// Process each item
$lastInvoice = '';
$totalAmount = 0;
$invoice = 'INV-'.date('Ymd').'-'.strtoupper(substr(uniqid(),-6));
$itemCount = count($checkoutItems);

foreach ($checkoutItems as $idx => $pid) {
    // Ambil produk
    $stmt = $conn->prepare("SELECT * FROM products WHERE id=? AND is_active=1 LIMIT 1");
    $stmt->bind_param("i", $pid); $stmt->execute();
    $product = $stmt->get_result()->fetch_assoc(); $stmt->close();

    if (!$product) continue;

    // Cek stok
    if (stockCount($conn, $pid) <= 0) {
        $_SESSION['flash'] = ['type'=>'error','msg'=>'Stok '.$product['name'].' habis.'];
        header('Location: ' . BASE_URL . '/store/cart.php'); exit;
    }

    // Calculate item amount (distribute voucher discount evenly)
    $itemDiscount = ($itemCount > 0 && $voucherDiscount > 0) ? round($voucherDiscount / $itemCount) : 0;
    $itemAmount = max(0, $product['price'] - $itemDiscount);

    // Simpan transaksi
    $stmt = $conn->prepare("INSERT INTO transactions (user_id, invoice_number, phone_number, customer_name, product_id, amount, status) VALUES (?,?,?,?,?,?,'PENDING')");
    $stmt->bind_param("isssis", $user_id, $invoice, $phone, $customer_name, $pid, $itemAmount);
    $stmt->execute(); $stmt->close();

    $totalAmount += $itemAmount;
    $lastInvoice = $invoice;
}

// Clear cart if cart checkout
if ($from_cart) {
    $_SESSION['cart'] = [];
}

if (empty($lastInvoice)) {
    $_SESSION['flash'] = ['type'=>'error','msg'=>'Tidak ada produk valid untuk checkout.'];
    header('Location: ' . BASE_URL . '/store/cart.php'); exit;
}

// ═══ LOYALTY POINTS & BADGES ═══
if ($user_id && $totalAmount > 0) {
    // Award points: 10% of purchase amount
    $earnedPoints = (int)floor($totalAmount * 0.1);
    if ($earnedPoints > 0) {
        addPoints($conn, $user_id, $earnedPoints, "Pembelian #{$lastInvoice}");
        addNotification($conn, $user_id, "Kamu dapat {$earnedPoints} poin! 🎉", "Poin dari pembelian {$lastInvoice} senilai " . rupiah($totalAmount), 'success', BASE_URL . '/pages/profile.php');
    }

    // First purchase badge
    $cntStmt = $conn->prepare("SELECT COUNT(*) as c FROM transactions WHERE user_id=?");
    $cntStmt->bind_param("i", $user_id); $cntStmt->execute();
    $orderCount = $cntStmt->get_result()->fetch_assoc()['c'];
    $cntStmt->close();
    if ($orderCount <= $itemCount) {
        awardBadge($conn, $user_id, 'first_purchase', 'Pembeli Pertama', 'ri-shopping-bag-fill', 'green');
        addNotification($conn, $user_id, "Badge Baru: Pembeli Pertama! 🏅", "Selamat atas pembelian pertamamu!", 'success');
    }
    // Loyal buyer badge (5+ orders)
    if ($orderCount >= 5) {
        awardBadge($conn, $user_id, 'loyal_buyer', 'Pelanggan Setia', 'ri-heart-fill', 'pink');
    }
    // Big spender badge (total > 500k)
    $spendStmt = $conn->prepare("SELECT SUM(amount) as total FROM transactions WHERE user_id=? AND status='SUCCESS'");
    $spendStmt->bind_param("i", $user_id); $spendStmt->execute();
    $totalSpend = $spendStmt->get_result()->fetch_assoc()['total'] ?? 0;
    $spendStmt->close();
    if ($totalSpend >= 500000) {
        awardBadge($conn, $user_id, 'big_spender', 'Sultan Premium', 'ri-vip-crown-fill', 'amber');
    }
}

header("Location: " . BASE_URL . "/store/bayar_dummy.php?invoice=".urlencode($lastInvoice));
exit;
