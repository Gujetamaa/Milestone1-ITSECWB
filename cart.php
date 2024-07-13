<?php
include 'db_connection.php';

session_start();

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = ['items' => [], 'combos' => []];
} else {
    if (!isset($_SESSION['cart']['items'])) {
        $_SESSION['cart']['items'] = [];
    }
    if (!isset($_SESSION['cart']['combos'])) {
        $_SESSION['cart']['combos'] = [];
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['resetCart'])) {
        $_SESSION['cart'] = ['items' => [], 'combos' => []];
    } else {
        foreach ($_POST['quantities'] as $type => $typeArray) {
            foreach ($typeArray as $id => $quantity) {
                if ($type === 'items' || $type === 'combos') {
                    $_SESSION['cart'][$type][$id]['quantity'] = (int)$quantity;
                }
            }
        }
    }
    setcookie('cart_data', serialize($_SESSION['cart']), time() + (86400 * 30), "/"); // 86400 = 1 day

    header('Location: cart.php');
    exit;
}

$totalPrice = 0;

$menuItemsQuery = "SELECT * FROM menu_items";
$menuItemsResult = mysqli_query($conn, $menuItemsQuery);
$menuItems = [];
if ($menuItemsResult) {
    while ($menuItem = mysqli_fetch_assoc($menuItemsResult)) {
        $menuItems[$menuItem['menu_item_id']] = $menuItem;
    }
}

$combosQuery = "SELECT * FROM combo_meals";
$combosResult = mysqli_query($conn, $combosQuery);
$combos = [];
if ($combosResult) {
    while ($combo = mysqli_fetch_assoc($combosResult)) {
        $combos[$combo['combo_id']] = $combo;
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart - Kape-Kada Coffee Shop</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.1/css/all.css">
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@400;700&family=Montserrat:wght@300;400;700&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #F5F5DC; 
            color: #6B4F4E; 
            font-family: 'Montserrat', sans-serif;
        }
        .container {
            max-width: 800px;
        }
        h2, h3 {
            font-family: 'Montserrat', sans-serif;
            font-weight: normal;
            color: #A0522D;
        }
        .btn-info, .btn-warning, .btn-success, .btn-secondary {
            color: #fff;
            background-color: #A0522D;
            border-color: #A0522D;
        }
        .btn-info:hover, .btn-warning:hover, .btn-success:hover, .btn-secondary:hover {
            background-color: #8B4513;
            border-color: #8B4513;
        }
        input[type=number] {
            width: 80px;
            padding: 8px;
            border: 1px solid #A0522D;
            border-radius: 4px;
            margin-right: 10px;
        }
        .white-container {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .cart-item {
            margin-bottom: 15px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
        }
        .cart-item:last-child {
            border-bottom: none;
        }
        .cart-item input[type="number"] {
            width: 60px;
        }
        .cart-item p {
            margin: 0;
        }
        .total-price {
            font-size: 20px;
            font-weight: bold;
            margin-top: 15px;
        }
        .discount {
            color: green;
            font-weight: bold;
            margin-top: 10px;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <div class="white-container">
        <h2>Your Shopping Cart</h2>
        <?php
        $numComboDeals = count($_SESSION['cart']['combos']);

        $discountMessage = '';
        if ($numComboDeals == 1) {
            $discountPercentage = 0.10; // 10% discount for 1 combo deal
            $discountMessage = 'Classic Special: 10% discount applied to total bill - 1 combo deal in cart';
        } elseif ($numComboDeals == 2) {
            $discountPercentage = 0.15; // 15% discount for 2 combo deals
            $discountMessage = 'Deluxe Special: 15% discount applied to total bill - 2 combo deals in cart';
        } elseif ($numComboDeals >= 3) {
            $discountPercentage = 0.20; // 20% discount for 3 or more combo deals
            $discountMessage = 'Premium Special: 20% discount applied to total bill - 3 or more combo deals in cart';
        } else {
            $discountPercentage = 0; // no discount if no combo deals
        }

        $totalPrice = 0;

        // calculate total price before discount
        foreach ($_SESSION['cart']['items'] as $itemId => $details) {
            $totalPrice += $menuItems[$itemId]['price'] * $details['quantity'];
        }
        foreach ($_SESSION['cart']['combos'] as $comboId => $details) {
            $totalPrice += $combos[$comboId]['price'] * $details['quantity'];
        }

        $totalPriceAfterDiscount = $totalPrice - ($totalPrice * $discountPercentage);

        if (!empty($discountMessage)) {
            echo '<p class="discount">' . $discountMessage . '</p>';
        }
        ?>
        <form method="POST" action="cart.php">
            <div class="cart-items">
                <h3>Items</h3>
                <?php foreach ($_SESSION['cart']['items'] as $itemId => $details): ?>
                    <div class="cart-item">
                        <p><?php echo $menuItems[$itemId]['name']; ?>:
                            <input type="number" name="quantities[items][<?php echo $itemId; ?>]" value="<?php echo $details['quantity']; ?>" min="0">
                            - ₱<?php echo number_format($menuItems[$itemId]['price'] * $details['quantity'], 2); ?>
                        </p>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="cart-items">
                <h3>Combos</h3>
                <?php foreach ($_SESSION['cart']['combos'] as $comboId => $details): ?>
                    <div class="cart-item">
                        <p><?php echo $combos[$comboId]['name']; ?>:
                            <input type="number" name="quantities[combos][<?php echo $comboId; ?>]" value="<?php echo $details['quantity']; ?>" min="0">
                            - ₱<?php echo number_format($combos[$comboId]['price'] * $details['quantity'], 2); ?>
                        </p>
                    </div>
                <?php endforeach; ?>
            </div>

            <p class="total-price">Total Price: ₱<?php echo number_format($totalPriceAfterDiscount, 2); ?></p>
            <div class="text-right mt-3">
                <button type="submit" class="btn btn-info mr-2">Update Cart</button>
                <button type="submit" name="resetCart" class="btn btn-warning mr-2">Reset Cart</button>
                <a href="menu.php" class="btn btn-secondary">Continue Shopping</a>
            </div>
        </form>
        <a href="checkout.php" class="btn btn-success mt-3">Proceed to Checkout</a>
    </div>
</div>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
