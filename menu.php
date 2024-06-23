<?php 
include 'navbar.php'; 

include 'db_connection.php';

$filter = isset($_GET['filter']) ? $_GET['filter'] : '';

$sql = "SELECT * FROM menu_items";
if ($filter) {
    $sql .= " WHERE category = '$filter'";
}
$result = mysqli_query($conn, $sql);

$menuItems = [];

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $menuItems[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu - Kape-Kada Coffee Shop</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.1/css/all.css">
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@400;700&family=Montserrat:wght@300;400;700&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #F5F5DC; 
            color: #6B4F4E; 
            font-family: 'Montserrat', sans-serif;
            background-image: url('bg.svg');
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed; 
            background-position: center;
        }
        .navbar {
            font-family: 'Merriweather', serif;
        }
        .menu-item-card {
            transition: transform 0.2s;
            cursor: pointer;
        }
        .menu-item-card:hover {
            transform: scale(1.03);
            box-shadow: 0 8px 16px 0 rgba(0,0,0,0.2);
        }
        .card-title {
            color: #D17A22;
        }
        .filter-form {
            margin-bottom: 20px;
        }
        label[for="filter"] {
            color: #FFFFFF; 
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h1 class="text-center mb-4">Our Menu</h1>
    <form action="menu.php" method="get" class="filter-form">
        <div class="form-group">
            <label for="filter">Filter by Category:</label>
            <select id="filter" name="filter" class="form-control" onchange="this.form.submit()">
                <option value="">All Items</option>
                <option value="Mains" <?php if ($filter == 'Mains') echo 'selected'; ?>>Mains</option>
                <option value="Sides" <?php if ($filter == 'Sides') echo 'selected'; ?>>Sides</option>
                <option value="Drink" <?php if ($filter == 'Drink') echo 'selected'; ?>>Drink</option>
            </select>
        </div>
    </form>
    <div class="row">
        <?php foreach ($menuItems as $item): ?>
            <div class="col-md-4 mb-4">
                <div class="card menu-item-card h-100" onclick="window.location.href='item_detail.php?id=<?php echo $item['menu_item_id']; ?>';">
                <img src="images/<?php echo $item['image']; ?>" class="card-img-top" alt="<?php echo $item['name']; ?>">    
                <div class="card-body">
                        <h5 class="card-title"><?php echo $item['name']; ?></h5>
                        <h6 class="card-subtitle mb-2 text-muted">₱<?php echo number_format($item['price'], 2); ?></h6>
                        <p class="card-text"><?php echo $item['description']; ?></p>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
