<?php
session_start();
include 'admin_navbar.php';
include 'db_connection.php';

$message = "";
$low_stock_threshold = 10;

// Function to log actions to admin_actions.log
function logAction($action, $details) {
    $logFile = 'logs\admin_actions.log';
    $timestamp = date('[Y-m-d H:i:s]');
    $logMessage = "$timestamp [$action] $details" . PHP_EOL;
    file_put_contents($logFile, $logMessage, FILE_APPEND | LOCK_EX);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['create_item'])) {
    $name = isset($_POST['name']) ? htmlspecialchars($_POST['name']) : '';
    $price = isset($_POST['price']) ? htmlspecialchars($_POST['price']) : '';
    $category = isset($_POST['category']) ? htmlspecialchars($_POST['category']) : '';
    $stock_quantity = isset($_POST['stock_quantity']) ? htmlspecialchars($_POST['stock_quantity']) : '';
    $description = isset($_POST['description']) ? htmlspecialchars($_POST['description']) : '';

    $fileName = $_FILES["image"]["name"];
    $fileSize = $_FILES["image"]["size"];
    $tmpName = $_FILES["image"]["tmp_name"];
    $validImageExtension = ['jpg', 'jpeg', 'png', 'webp'];
    $imageExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    if ($_FILES["image"]["error"] != 4) {
        if (!in_array($imageExtension, $validImageExtension)) {
            $message = "Error: Invalid Image Extension";
        } elseif ($fileSize > 1000000) {
            $message = "Error: Image Size Is Too Large";
        } else {
            $newImageName = uniqid() . '.' . $imageExtension;
            move_uploaded_file($tmpName, 'C:/xampp/htdocs/Milestone1-ITSECWB/images/' . $newImageName);

            // Prepared Statement to prevent SQL injection
            $stmt = $conn->prepare("INSERT INTO menu_items (name, price, category, description, stock_quantity, image) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param('sdssis', $name, $price, $category, $description, $stock_quantity, $newImageName);

            if ($stmt->execute()) {
                $message = "Menu item created successfully.";
                logAction('ADD_MENU_ITEM', "Added menu item: $name");
                echo '<meta http-equiv="refresh" content="2;url=admin_menu.php">';
            } else {
                $message = "Error: " . $stmt->error;
            }
            $stmt->close();
        }
    } else {
        $message = "Error: Image Does Not Exist";
    }
}

if (isset($_POST['delete_id'])) {
    $delete_id = $_POST['delete_id'];

    // Prepared Statement to prevent SQL injection
    $stmt = $conn->prepare("DELETE FROM menu_items WHERE menu_item_id = ?");
    $stmt->bind_param('i', $delete_id);

    if ($stmt->execute()) {
        $message = "Menu item successfully deleted.";
        logAction('DELETE_MENU_ITEM', "Deleted menu item with ID: $delete_id");
        echo '<meta http-equiv="refresh" content="2;url=admin_menu.php">';
    } else {
        $message = "Error deleting menu item: " . $stmt->error;
    }
    $stmt->close();
}

// Fetch menu items
$sql = "SELECT * FROM menu_items";
$result = mysqli_query($conn, $sql);
$menu_items = [];
$low_stock_alerts = [];
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $menu_items[] = $row;
        if ($row['stock_quantity'] < $low_stock_threshold) {
            $low_stock_alerts[] = htmlspecialchars($row['name']);
        }
    }
}

if (!empty($low_stock_alerts)) {
    $message = "Low stock alerts: " . implode(", ", $low_stock_alerts) . " stock quantity is below the threshold.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Item Management - Your Restaurant Name</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
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
            margin-top: 70px;
        }
        .promotion-container {
            background-color: #FFFFFF;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .promotion-title {
            color: #A52A2A;
            font-weight: 700;
            margin-bottom: 30px;
        }
        .promotion-form input[type="text"],
        .promotion-form input[type="number"],
        .promotion-form select {
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 8px;
            width: 100%;
        }
        .promotion-form button[type="submit"] {
            background-color: #A52A2A;
            color: #FFFFFF;
            border: none;
            border-radius: 5px;
            padding: 10px 20px;
            cursor: pointer;
        }
        .promotion-form button[type="submit"]:hover {
            background-color: #8B4513;
        }
        .promotion-list h3 {
            margin-top: 40px;
            margin-bottom: 15px;
        }
        .promotion-list .promotion-item {
            background-color: #F9F9F9;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .promotion-list .promotion-item h4 {
            color: #A52A2A;
            font-weight: 700;
            margin-bottom: 5px;
        }
        .promotion-list .promotion-item p {
            margin-bottom: 5px;
        }
        .promotion-buttons {
            display: flex;
        }
        .promotion-buttons .delete-btn,
        .promotion-buttons .update-btn {
            margin-right: 10px;
        }
        .promotion-buttons .delete-btn {
            background-color: #A52A2A;
            color: #FFFFFF;
        }
        .promotion-buttons .update-btn {
            background-color: #A52A2A;
            color: #FFFFFF;
        }
        .alert-slide {
            position: fixed;
            top: 170px;
            right: 20px;
            z-index: 9999;
            background-color: #A52A2A;
            color: #FFFFFF;
            padding: 10px 20px;
            border-radius: 8px;
            animation: slideIn 0.5s ease forwards;
        }
        @keyframes slideIn {
            0% {
                right: -100%;
            }
            100% {
                right: 20px;
            }
        }
        @keyframes slideOut {
            0% {
                right: 20px;
            }
            100% {
                right: -100%;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <!-- Modified form for creating menu items -->
            <div class="promotion-container">
                <h2 class="promotion-title">Menu Item Management</h2>
                <form class="promotion-form" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="price">Price</label>
                        <input type="number" class="form-control" id="price" name="price" min="0.01" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label for="category">Category</label>
                        <select class="form-control" id="category" name="category" required>
                            <option value="Appetizers">Appetizers</option>
                            <option value="Main Courses">Main Courses</option>
                            <option value="Desserts">Desserts</option>
                            <option value="Beverages">Beverages</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="stock_quantity">Stock Quantity</label>
                        <input type="number" class="form-control" id="stock_quantity" name="stock_quantity" min="0" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="image">Image</label>
                        <input type="file" class="form-control-file" id="image" name="image" accept="image/*" required>
                    </div>
                    <button type="submit" class="btn btn-primary" name="create_item">Create Item</button>
                </form>
            </div>

            <!-- Display messages -->
            <?php if ($message): ?>
                <div class="alert-slide"><?php echo $message; ?></div>
            <?php endif; ?>

            <!-- List of menu items with delete option -->
            <div class="promotion-list">
                <h3>Menu Items</h3>
                <?php if (empty($menu_items)): ?>
                    <p>No menu items available.</p>
                <?php else: ?>
                    <?php foreach ($menu_items as $item): ?>
                        <div class="promotion-item">
                            <h4><?php echo htmlspecialchars($item['name']); ?></h4>
                            <p>Price: $<?php echo htmlspecialchars($item['price']); ?></p>
                            <p>Category: <?php echo htmlspecialchars($item['category']); ?></p>
                            <p>Description: <?php echo htmlspecialchars($item['description']); ?></p>
                            <p>Stock Quantity: <?php echo htmlspecialchars($item['stock_quantity']); ?></p>
                            <?php if ($item['image']): ?>
                                <img src="images/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" style="width: 100px; height: auto;">
                            <?php endif; ?>
                            <div class="promotion-buttons">
                                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" style="display: inline;">
                                    <input type="hidden" name="delete_id" value="<?php echo htmlspecialchars($item['menu_item_id']); ?>">
                                    <button type="submit" class="btn btn-danger delete-btn">Delete</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
