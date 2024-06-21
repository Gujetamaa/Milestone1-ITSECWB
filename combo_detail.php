<?php
include 'navbar.php';
include 'db_connection.php';

$comboId = $_GET['id'] ?? '';

$sql = "SELECT * FROM combo_meals WHERE id = '$comboId'";
$result = mysqli_query($conn, $sql);

$combo = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Combo Detail - Kape-Kada Coffee Shop</title>
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
            margin-top: 120px;
        }
        .white-container {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .btn-primary {
            background-color: #A0522D;
            border-color: #A0522D;
            margin-bottom: 5px;
        }
        .btn-primary:hover {
            background-color: #8B4513;
            border-color: #A0522D;
        }
        .btn-secondary {
            background-color: #A52A2A; 
            color: #FFFFFF; 
        }
        .btn-secondary:hover {
            background-color: #8B4513;
            border-color: #8B4513;
        }
    </style>
    <script>
        function updateTotalPrice() {
            var price = parseFloat(document.getElementById('comboPrice').value);
            var quantity = parseInt(document.getElementById('quantity').value || 1);
            var totalPrice = price * quantity;
            document.getElementById('totalPrice').innerText = 'Total Price: ₱' + totalPrice.toFixed(2);
        }
        window.onload = updateTotalPrice;
    </script>
</head>
<body>

<div class="container">
    <div class="white-container">
        <?php if ($combo): ?>
            <h2><?php echo htmlspecialchars($combo['name']); ?></h2>
            <p><?php echo htmlspecialchars($combo['description']); ?></p>
            <p><strong>Price:</strong> ₱<span id="comboPriceText"><?php echo number_format($combo['price'], 2); ?></span></p>
            <form id="addToCartForm" method="post" action="add_to_cart.php"> 
                <input type="hidden" name="itemId" value="<?php echo htmlspecialchars($comboId); ?>">
                <input type="hidden" name="type" value="combo">
                <input type="hidden" id="comboPrice" value="<?php echo $combo['price']; ?>">
                <div class="form-group">
                    <label for="quantity">Quantity:</label>
                    <input type="number" id="quantity" name="quantity" class="form-control" min="1" value="1" onchange="updateTotalPrice()">
                </div>
                <p id="totalPrice"></p>
                <button type="submit" class="btn btn-primary">Add to Cart</button>
            </form>
            <a href="combos.php" class="btn btn-secondary">Back to Combos</a>
        <?php else: ?>
            <p>Combo not found.</p>
        <?php endif; ?>
    </div>
</div>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
