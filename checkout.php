<?php
session_start();
  
// Check if the user is logged in and has the role of Administrator
if (!isset($_SESSION['role'])) {
    // Redirect to login page or display an error message
    header("Location: login.php");
    exit();
}

include 'db_connection.php';
include 'navbar.php';

// Function to log actions to user_actions.log
function logAction($action) {
    $logFile = 'C:/xampp/htdocs/Milestone1-ITSECWB/logs/user_actions.log';
    $logTime = date('Y-m-d H:i:s');
    $logMessage = "[$logTime] $action" . PHP_EOL;
    file_put_contents($logFile, $logMessage, FILE_APPEND);
}

// Check if cart exists in session
if (isset($_SESSION['cart'])) {
    $totalPrice = 0;

    // Fetch menu items from database
    $menuItemsQuery = "SELECT * FROM menu_items";
    $menuItemsResult = mysqli_query($conn, $menuItemsQuery);
    $menuItems = [];
    if ($menuItemsResult) {
        while ($menuItem = mysqli_fetch_assoc($menuItemsResult)) {
            $menuItems[$menuItem['menu_item_id']] = $menuItem;
        }
    }

    // Fetch combo meals from database
    $combosQuery = "SELECT * FROM combo_meals";
    $combosResult = mysqli_query($conn, $combosQuery);
    $combos = [];
    if ($combosResult) {
        while ($combo = mysqli_fetch_assoc($combosResult)) {
            $combos[$combo['combo_id']] = $combo;
        }
    }

    // Calculate total price considering discounts
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

    // Calculate total price for items and combos in cart
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

    // Apply discount
    $totalPrice -= $totalPrice * $discountPercentage;
} else {
    header('Location: cart.php');
    exit;
}

// Fetch user information if logged in
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
    // Serialize cart for non-logged in users
    if (isset($_SESSION['cart'])) {
        $_SESSION['cart_serialized'] = serialize($_SESSION['cart']);
    }
}

// Retrieve client name and address for form pre-filling
$clientName = isset($clientInfo['name']) ? htmlspecialchars($clientInfo['name']) : '';
$clientAddress = isset($clientInfo['address']) ? htmlspecialchars($clientInfo['address']) : '';
$newAddress = '';

// Update client info from last order if available
if (isset($_SESSION['last_order_info'])) {
    $lastOrderInfo = $_SESSION['last_order_info'];
    if (isset($lastOrderInfo['name'])) {
        $clientName = htmlspecialchars($lastOrderInfo['name']);
    }
    if (isset($lastOrderInfo['address'])) {
        $clientAddress = htmlspecialchars($lastOrderInfo['address']);
    }
}

// Log user action: Accessing checkout page
logAction("User accessed checkout page.");

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
        .whitecontainer{
            margin-top: 170px;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
<div class="container">
    <div class="whitecontainer">
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
</div>

<?php

// Log user action: Submitting order
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    logAction("User submitted an order.");
}

?>

<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
