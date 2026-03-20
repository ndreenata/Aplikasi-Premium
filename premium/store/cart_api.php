<?php
/**
 * CART_API.PHP — Cart AJAX endpoint
 * Actions: add, remove, count, clear
 */
require_once __DIR__ . '/../includes/koneksi.php';

header('Content-Type: application/json');

$action = $_REQUEST['action'] ?? '';

// Initialize cart if needed
if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

switch ($action) {

    case 'add':
        $pid = (int)($_POST['product_id'] ?? 0);
        if ($pid <= 0) { echo json_encode(['ok'=>false,'msg'=>'Invalid product']); exit; }

        // Check product exists
        $stmt = $conn->prepare("SELECT id, name, price, category FROM products WHERE id=? AND is_active=1 LIMIT 1");
        $stmt->bind_param("i", $pid);
        $stmt->execute();
        $product = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        if (!$product) { echo json_encode(['ok'=>false,'msg'=>'Produk tidak ditemukan']); exit; }

        // Check stock
        $stock = stockCount($conn, $pid);
        if ($stock <= 0) { echo json_encode(['ok'=>false,'msg'=>'Stok habis']); exit; }

        // Check if already in cart
        foreach ($_SESSION['cart'] as $item) {
            if ($item['product_id'] == $pid) {
                echo json_encode(['ok'=>false,'msg'=>'Produk sudah ada di keranjang','count'=>count($_SESSION['cart'])]);
                exit;
            }
        }

        // Add to cart
        $_SESSION['cart'][] = [
            'product_id' => $product['id'],
            'name' => $product['name'],
            'price' => (float)$product['price'],
            'category' => $product['category']
        ];

        echo json_encode(['ok'=>true,'msg'=>'Ditambahkan ke keranjang!','count'=>count($_SESSION['cart'])]);
        break;

    case 'remove':
        $pid = (int)($_POST['product_id'] ?? 0);
        $_SESSION['cart'] = array_values(array_filter($_SESSION['cart'], function($item) use ($pid) {
            return $item['product_id'] != $pid;
        }));
        echo json_encode(['ok'=>true,'msg'=>'Dihapus dari keranjang','count'=>count($_SESSION['cart'])]);
        break;

    case 'count':
        echo json_encode(['ok'=>true,'count'=>count($_SESSION['cart'])]);
        break;

    case 'clear':
        $_SESSION['cart'] = [];
        echo json_encode(['ok'=>true,'msg'=>'Keranjang dikosongkan','count'=>0]);
        break;

    default:
        echo json_encode(['ok'=>false,'msg'=>'Invalid action']);
}
