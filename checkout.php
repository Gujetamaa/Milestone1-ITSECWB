<?php
session_start();
include 'db_connection.php';

if (isset($_SESSION['cart'])) {
    $totalPrice = 0;

    $menuItemsQuery = "SELECT * FROM menu_items";
    $menuItemsResult = mysqli_query($conn, $menuItemsQuery);
    $menuItems = [];
    if ($menuItemsResult) {
        while ($menuItem = mysqli_fetch_assoc($menuItemsResult)) {
            $menuItems[$menuItem['id']] = $menuItem;
        }
    }

    $combosQuery = "SELECT * FROM combo_meals";
    $combosResult = mysqli_query($conn, $combosQuery);
    $combos = [];
    if ($combosResult) {
        while ($combo = mysqli_fetch_assoc($combosResult)) {
            $combos[$combo['id']] = $combo;
        }
    }

    $numComboDeals = count($_SESSION['cart']['combos']);

    if ($numComboDeals == 1) {
        $discountPercentage = 0.10; // 10% discount for 1 combo deal
    } elseif ($numComboDeals == 2) {
        $discountPercentage = 0.15; // 15% discount for 2 combo deals
    } elseif ($numComboDeals >= 3) {
        $discountPercentage = 0.20; // 20% discount for 3 or more combo deals
    } else {
        $discountPercentage = 0; // no discount if no combo deals
    }

    if (isset($_SESSION['cart']['items'])) {
        foreach ($_SESSION['cart']['items'] as $itemId => $details) {
            $itemPrice = $menuItems[$itemId]['price']; 
            $totalPrice += $itemPrice * $details['quantity']; 
        }
    }

    if (isset($_SESSION['cart']['combos'])) {
        foreach ($_SESSION['cart']['combos'] as $comboId => $details) {
            $comboPrice = $combos[$comboId]['price']; 
            $totalPrice += $comboPrice * $details['quantity']; 
        }
    }

    $totalPrice -= $totalPrice * $discountPercentage;
} else {
    header('Location: cart.php');
    exit;
}

if (isset($_SESSION['email'])) {
    $userEmail = $_SESSION['email'];
    if (isset($_SESSION['client_info'][$userEmail])) {
        $clientInfo = $_SESSION['client_info'][$userEmail];
    } else {
        $clientInfoQuery = "SELECT * FROM users WHERE email = '$userEmail'";
        $clientInfoResult = mysqli_query($conn, $clientInfoQuery);
        if ($clientInfoResult && mysqli_num_rows($clientInfoResult) > 0) {
            $clientInfo = mysqli_fetch_assoc($clientInfoResult);
            $_SESSION['client_info'][$userEmail] = $clientInfo;
        }
    }
} else {
    if (isset($_SESSION['cart'])) {
        $_SESSION['cart_serialized'] = serialize($_SESSION['cart']);
    }
}

$clientName = isset($clientInfo['name']) ? htmlspecialchars($clientInfo['name']) : '';
$clientAddress = isset($clientInfo['address']) ? htmlspecialchars($clientInfo['address']) : '';
$newAddress = '';

if (isset($_SESSION['last_order_info'])) {
    $lastOrderInfo = $_SESSION['last_order_info'];
    if (isset($lastOrderInfo['name'])) {
        $clientName = htmlspecialchars($lastOrderInfo['name']);
    }
    if (isset($lastOrderInfo['address'])) {
        $clientAddress = htmlspecialchars($lastOrderInfo['address']);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Kape-Kada Coffee Shop</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.1/css/all.css">
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@400;700&family=Montserrat:wght@300;400;700&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #F5F5DC; 
            color: #6B4F4E; 
            font-family: 'Montserrat', sans-serif;
        }
        .navbar {
            font-family: 'Merriweather', serif;
        }
        .container {
            margin-top: 80px;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Checkout Summary</h2>
    <form action="process_payment.php" method="post">
        <div class="mb-3">
            <label for="name" class="form-label">Name:</label>
            <input type="text" class="form-control" id="name" name="name" value="<?php echo $clientName; ?>" required>
        </div>
        
        <div class="mb-3">
            <label for="address" class="form-label">Address:</label>
            <select class="form-control" id="address" name="address">
                <option value="">Select an existing address or enter a new one...</option>
                <?php 
                if($clientAddress !== '') {
                    echo '<option value="' . $clientAddress . '">' . $clientAddress . '</option>';
                }
                ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="new_address" class="form-label">Or enter a new address:</label>
            <input type="text" class="form-control" id="new_address" name="new_address" value="<?php echo $newAddress; ?>">
        </div>
        <h4>Items in Your Cart:</h4>
        <ul>
            <?php if (isset($_SESSION['cart']['items'])): ?>
                <?php foreach ($_SESSION['cart']['items'] as $itemId => $details): ?>
                    <li><?php echo htmlspecialchars($menuItems[$itemId]['name']) . " - Quantity: " . $details['quantity'] . " - Price: ₱" . number_format($menuItems[$itemId]['price'] * $details['quantity'], 2); ?></li>
                <?php endforeach; ?>
            <?php endif; ?>
            <?php if (isset($_SESSION['cart']['combos'])): ?>
                <?php foreach ($_SESSION['cart']['combos'] as $comboId => $details): ?>
                    <li><?php echo htmlspecialchars($combos[$comboId]['name']) . " - Quantity: " . $details['quantity'] . " - Price: ₱" . number_format($combos[$comboId]['price'] * $details['quantity'], 2); ?></li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>
        <p><strong>Total Price: ₱<?php echo number_format($totalPrice, 2); ?></strong></p>
        <button type="submit" class="btn btn-primary">Pay</button>
    </form>
</div>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
