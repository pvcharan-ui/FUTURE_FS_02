<?php
// cart_api.php - returns JSON for ajax add/update
session_start();
header('Content-Type: application/json');

require __DIR__ . '/inc/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success'=>false,'message'=>'Invalid method']);
    exit;
}

$action = $_POST['action'] ?? '';

if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

if ($action === 'add') {
    $pid = (int)($_POST['product_id'] ?? 0);
    $qty = max(1,(int)($_POST['qty'] ?? 1));

    // check product exists and stock
    $stmt = $pdo->prepare("SELECT stock FROM products WHERE id = ?");
    $stmt->execute([$pid]);
    $stock = $stmt->fetchColumn();
    if ($stock === false) {
        echo json_encode(['success'=>false,'message'=>'Product not found']);
        exit;
    }
    $qty = min($qty, (int)$stock);
    if (isset($_SESSION['cart'][$pid])) $_SESSION['cart'][$pid] += $qty;
    else $_SESSION['cart'][$pid] = $qty;

    // compute total items
    $count = array_sum($_SESSION['cart']);
    echo json_encode(['success'=>true,'count'=>$count]);
    exit;
}

if ($action === 'update') {
    // expected inputs: qty[ID] = value
    foreach ($_POST as $k=>$v) {
        if (strpos($k, 'qty[') === 0) {
            // key like qty[3]
            preg_match('/qty\[(\d+)\]/', $k, $m);
            $pid = (int)($m[1] ?? 0);
            $q = max(0, (int)$v);
            if ($q === 0) {
                unset($_SESSION['cart'][$pid]);
            } else {
                // optional: ensure not above stock
                $stmt = $pdo->prepare("SELECT stock FROM products WHERE id = ?");
                $stmt->execute([$pid]);
                $stock = $stmt->fetchColumn();
                if ($stock !== false) $q = min($q, (int)$stock);
                $_SESSION['cart'][$pid] = $q;
            }
        }
    }
    $count = array_sum($_SESSION['cart']);
    echo json_encode(['success'=>true,'count'=>$count]);
    exit;
}

echo json_encode(['success'=>false,'message'=>'Unknown action']);
exit;
