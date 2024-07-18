<?php
session_start();
include 'db_connection.php';

// Function to log user actions
function logAction($email, $action, $itemId, $quantity) {
    $logFile = 'logs\user_actions.log';
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[{$timestamp}] [User Email: {$email}] Action: {$action} Item ID: {$itemId} Quantity: {$quantity}\n";
    file_put_contents($logFile, $logMessage, FILE_APPEND);
}

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit;
}

// Assuming you have received $_POST['type'], $_POST['itemId'], $_POST['quantity'] from your form
$type = $_POST['type'] ?? 'item'; 
$itemId = $_POST['itemId'] ?? '';
$quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 0;

if ($itemId && $quantity > 0) {
    if ($type === 'combo') {
        // Handle combo item addition
        if (isset($_SESSION['cart']['combos'][$itemId])) {
            $_SESSION['cart']['combos'][$itemId]['quantity'] += $quantity;
        } else {
            $_SESSION['cart']['combos'][$itemId] = ['quantity' => $quantity];
        }
        logAction($_SESSION['email'], 'Add Combo to Cart', $itemId, $quantity);
    } else {
        // Handle individual item addition
        if (isset($_SESSION['cart']['items'][$itemId])) {
            $_SESSION['cart']['items'][$itemId]['quantity'] += $quantity;
        } else {
            $_SESSION['cart']['items'][$itemId] = ['quantity' => $quantity];
        }
        logAction($_SESSION['email'], 'Add Item to Cart', $itemId, $quantity);
    }

    header('Location: cart.php?status=success');
} else {
    // Handle error condition
    $redirectUrl = $type === 'combo' ? 'combo_detail.php?id=' . $itemId : 'item_detail.php?id=' . $itemId;
    header("Location: $redirectUrl&status=error");
}
?>