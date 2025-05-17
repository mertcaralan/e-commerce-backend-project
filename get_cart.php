<?php
session_start();
require 'db.php';

$cart = $_SESSION['cart'] ?? [];

$response = [];
$grandTotal = 0;

foreach ($cart as $product_id => $quantity) {
    $stmt = $db->prepare("SELECT name, price, img, stock FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($product) {
        $total = $product['price'] * $quantity;
        $grandTotal += $total;
        $response[] = [
            'id' => $product_id,
            'name' => $product['name'],
            'price' => $product['price'],
            'img' => $product['img'],
            'stock' => $product['stock'],
            'quantity' => $quantity,
            'total' => $total
        ];
    }
}

echo json_encode(['items' => $response, 'grandTotal' => $grandTotal]);
?>