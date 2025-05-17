<?php
session_start();
require_once "db.php";

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'C') {
    header('Location: login.php');
    exit;
}

$uid = $_SESSION['user_id'];

$sql = "SELECT c.*, p.title, p.normalPrice, p.discounted, p.img, p.stock FROM cart c
        JOIN products p ON c.product_id = p.id
        WHERE c.user_id = :uid";
$stmt = $db->prepare($sql);
$stmt->bindParam(':uid', $uid, PDO::PARAM_INT);
$stmt->execute();

$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

function calcGrandTotal($items) {
    $total = 0;
    foreach ($items as $item) {
        $total += $item['quantity'] * $item['normalPrice'];
    }
    return $total;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Shopping Cart</title>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link rel="stylesheet" href="cart.css" />
</head>
<body>

<h2>Your Shopping Cart</h2>
<div id="messages"></div>

<div id="cartContainer">
<?php if (count($items) === 0): ?>
    <p>Your cart is empty.</p>
<?php else: ?>
    <?php foreach ($items as $item): ?>
        <div class="cart-item" 
             data-cartid="<?= $item['id'] ?>" 
             data-productid="<?= $item['product_id'] ?>" 
             data-price="<?= $item['normalPrice'] ?>">
            <img src="./img/<?= htmlspecialchars($item['img']) ?>" alt="<?= htmlspecialchars($item['title']) ?>" />
            <div class="title"><?= htmlspecialchars($item['title']) ?></div>
            <div class="price"><?= number_format($item['normalPrice'], 2) ?> TL</div>
            <select class="qtySelect">
                <?php for ($i = 1; $i <= $item['stock']; $i++): ?>
                    <option value="<?= $i ?>" <?= $item['quantity'] == $i ? 'selected' : '' ?>><?= $i ?></option>
                <?php endfor; ?>
            </select>
            <div class="total"><?= number_format($item['quantity'] * $item['normalPrice'], 2) ?> TL</div>
            <button class="removeBtn btn">Remove</button>
        </div>
    <?php endforeach; ?>
<?php endif; ?>
</div>

<div id="totalPrice">Total: <?= number_format(calcGrandTotal($items), 2) ?> TL</div>

<button id="purchaseBtn" class="btn">Complete Purchase</button>
<button id="backBtn" class="btn">Back to Dashboard</button>

<script>
function updateGrandTotal() {
    let grandTotal = 0;
    $('.cart-item').each(function() {
        let price = parseFloat($(this).data('price'));
        let qty = parseInt($(this).find('.qtySelect').val());
        grandTotal += price * qty;
    });
    $('#totalPrice').text('Total: ' + grandTotal.toFixed(2) + ' TL');
}

$(document).on('change', '.qtySelect', function() {
    let parent = $(this).closest('.cart-item');
    let cartId = parent.data('cartid');
    let productId = parent.data('productid');
    let quantity = parseInt($(this).val());
    let price = parseFloat(parent.data('price'));

    $.post('ajax_cart_actions.php', {
        action: 'update',
        cart_id: cartId,
        product_id: productId,
        quantity: quantity
    }, function(res) {
        // Dinamik olarak toplam fiyat güncelle
        parent.find('.total').text((price * quantity).toFixed(2) + ' TL');
        updateGrandTotal();
    });
});

$(document).on('click', '.removeBtn', function() {
    let parent = $(this).closest('.cart-item');
    let productId = parent.data('productid');

    $.post('ajax_cart_actions.php', {
        action: 'remove',
        product_id: productId
    }, function(res) {
        parent.remove();
        updateGrandTotal();
        if ($('.cart-item').length === 0) {
            $('#cartContainer').html('<p>Your cart is empty.</p>');
            $('#totalPrice').text('Total: 0.00 TL');
        }
    });
});

$('#purchaseBtn').click(function() {
    $.post('ajax_cart_actions.php', { action: 'purchase' }, function(response) {
        let res = JSON.parse(response);
        if (res.status === 'success') {
            $('#messages').html('<div class="success">' + res.message + '</div>');
            $('#cartContainer').html('<p>Your cart is empty.</p>');
            $('#totalPrice').text('Total: 0.00 TL');
        } else {
            $('#messages').html('<div class="error">' + res.message + '</div>');
        }
    });
});

$('#backBtn').click(function() {
    window.location.href = 'consumer_dashboard.php';
});
</script>

</body>
</html>
