<?php
include 'db_connection.php';
include 'navbar.php';

if(isset($_GET['id'])) {
    $special_id = mysqli_real_escape_string($conn, $_GET['id']);

    $sql = "SELECT * FROM specials WHERE specials_id = '$special_id'";
    $result = mysqli_query($conn, $sql);

    if(mysqli_num_rows($result) > 0) {
        $special = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Specials Details - Kape-Kada Coffee Shop</title>
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
        .promotion-details-section {
            padding: 40px 0;
        }
        .promotion-item {
            background: #FFFFFF;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .promotion-title {
            color: #A52A2A; 
            font-weight: 700;
        }
        .promotion-price {
            font-weight: bold;
            color: #333;
        }
        .promotion-description {
            font-size: 16px;
        }
        .promotion-duration {
            font-weight: bold; 
        }
        h1 {
            margin-top: 78px; 
            margin-bottom: 25px;
            color: #A52A2A; 
            font-weight: 700; 
            text-align: center; 
        }
        .btn-return-to-menu {
            background-color: #D17A22; 
            color: #FFFFFF; 
            border: none; 
            padding: 10px 20px; 
            border-radius: 5px; 
            font-size: 16px; 
            cursor: pointer; 
            transition: background-color 0.3s; 
        }
        .btn-return-to-menu:hover {
            background-color: #A52A2A; 
            color: #FFFFFF; 
        }
    </style>
</head>
<body>
<div class="container promotion-details-section">
    <h1 class="text-center">Specials Details</h1>
    <div class="promotion-item">
        <h2 class="promotion-title"><?php echo $special['name']; ?></h2>
        <p class="promotion-description"><?php echo $special['description']; ?></p>
        <p class="promotion-duration">Duration: <?php echo $special['start_date']; ?> to <?php echo $special['end_date']; ?></p>
    </div>
    <div class="text-right">
        <a href="menu.php" class="btn btn-return-to-menu">Return to Menu</a>
    </div>
</div>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
    } else {
        echo "Specials not found.";
    }
} else {
    echo "Invalid request. Specials ID not provided.";
}
?>
