<?php
session_start(); 

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = ['items' => [], 'combos' => []];
}

$type = $_POST['type'] ?? 'item'; 
$itemId = $_POST['itemId'] ?? '';
$quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 0;

if ($itemId && $quantity > 0) {
    if ($type === 'combo') {
        if (isset($_SESSION['cart']['combos'][$itemId])) {
            $_SESSION['cart']['combos'][$itemId]['quantity'] += $quantity;
        } else {
            $_SESSION['cart']['combos'][$itemId] = ['quantity' => $quantity];
        }
    } else {
        if (isset($_SESSION['cart']['items'][$itemId])) {
            $_SESSION['cart']['items'][$itemId]['quantity'] += $quantity;
        } else {
            $_SESSION['cart']['items'][$itemId] = ['quantity' => $quantity];
        }
    }

    header('Location: cart.php?status=success');
} else {
    $redirectUrl = $type === 'combo' ? 'combo_detail.php?id=' . $itemId : 'item_detail.php?id=' . $itemId;
    header("Location: $redirectUrl&status=error");
}
?>