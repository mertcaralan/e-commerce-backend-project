<?php 
session_start(); 
require 'db.php';  

$action = $_POST['action'] ?? ''; 
$product_id = intval($_POST['product_id'] ?? 0); 
$quantity = intval($_POST['quantity'] ?? 1);  
$user_id = $_SESSION['user_id'] ?? null;

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = []; 
}  

switch ($action) {
    case 'add':
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id] += $quantity;
        } else {
            $_SESSION['cart'][$product_id] = $quantity;
        }
        
        // Aynı zamanda DB'ye ekle/güncelle
        if ($user_id) {
            $check = $db->prepare("SELECT quantity FROM cart WHERE user_id = ? AND product_id = ?");
            $check->execute([$user_id, $product_id]);
            $row = $check->fetch(PDO::FETCH_ASSOC);
            
            if ($row) {
                $new_quantity = $row['quantity'] + $quantity;
                $update = $db->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?");
                $update->execute([$new_quantity, $user_id, $product_id]);
            } else {
                $insert = $db->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
                $insert->execute([$user_id, $product_id, $quantity]);
            }
        }
        
        echo json_encode(['status' => 'success', 'message' => 'Product added']);
        break;
        
    case 'update':
    if ($quantity > 0) {
        // Eğer cart_id geldi ise doğrudan onu kullanarak güncelle
        $cart_id = intval($_POST['cart_id'] ?? 0);

        if ($cart_id) {
            // DB güncelle
            $update = $db->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
            $update->execute([$quantity, $cart_id]);

            // Session güncellemesi (isteğe bağlı)
            if (isset($_SESSION['cart'][$product_id])) {
                $_SESSION['cart'][$product_id] = $quantity;
            }
        } else {
            // Alternatif olarak user_id ve product_id ile update/insert işlemi
            if ($user_id) {
                $check = $db->prepare("SELECT id FROM cart WHERE user_id = ? AND product_id = ?");
                $check->execute([$user_id, $product_id]);

                if ($row = $check->fetch()) {
                    $update = $db->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
                    $update->execute([$quantity, $row['id']]);
                } else {
                    $insert = $db->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
                    $insert->execute([$user_id, $product_id, $quantity]);
                }
            }

            $_SESSION['cart'][$product_id] = $quantity;
        }

        echo json_encode(['status' => 'success', 'message' => 'Quantity updated']);
    } else {
        // quantity <= 0 ise ürün silme
        $cart_id = intval($_POST['cart_id'] ?? 0);

        if ($cart_id) {
            $delete = $db->prepare("DELETE FROM cart WHERE id = ?");
            $delete->execute([$cart_id]);
        } else if ($user_id) {
            $delete = $db->prepare("DELETE FROM cart WHERE user_id = ? AND product_id = ?");
            $delete->execute([$user_id, $product_id]);
        }

        unset($_SESSION['cart'][$product_id]);

        echo json_encode(['status' => 'success', 'message' => 'Product removed']);
    }
    break;
        
    case 'remove':
        unset($_SESSION['cart'][$product_id]);
        
        // DB'den de sil
        if ($user_id) {
            $delete = $db->prepare("DELETE FROM cart WHERE user_id = ? AND product_id = ?");
            $delete->execute([$user_id, $product_id]);
        }
        
        echo json_encode(['status' => 'success', 'message' => 'Product removed']);
        break;
        
case 'purchase':
    if (!$user_id || empty($_SESSION['cart'])) {
        echo json_encode(['status' => 'error', 'message' => 'User not logged in or cart empty']);
        exit;
    }

    try {
        $db->beginTransaction();

        // Stok kontrolü ve stok azaltma
        foreach ($_SESSION['cart'] as $pid => $qty) {
            $stmt = $db->prepare("SELECT stock FROM products WHERE id = ? FOR UPDATE");
            $stmt->execute([$pid]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$product) {
                throw new Exception("Product ID $pid not found.");
            }

            if ($product['stock'] < $qty) {
                throw new Exception("Insufficient stock for product ID $pid.");
            }

            $newStock = $product['stock'] - $qty;
            $update_stmt = $db->prepare("UPDATE products SET stock = ? WHERE id = ?");
            $update_stmt->execute([$newStock, $pid]);
        }

        // Sepeti veritabanından tamamen temizle
        $delete_stmt = $db->prepare("DELETE FROM cart WHERE user_id = ?");
        $delete_stmt->execute([$user_id]);

        // Session'dan sepeti temizle
        $_SESSION['cart'] = [];

        $db->commit();

        echo json_encode(['status' => 'success', 'message' => 'Purchase completed']);

    } catch (Exception $e) {
        $db->rollBack();
        echo json_encode(['status' => 'error', 'message' => 'Purchase failed: ' . $e->getMessage()]);
    }

    break;

        
    default:
        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
        break;
}
?>