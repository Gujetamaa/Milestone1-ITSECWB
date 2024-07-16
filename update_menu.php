<?php
session_start(); 
include 'admin_navbar.php';
include 'db_connection.php';

$message = ""; 

$low_stock_threshold = 10; 

// Logging function
function logAction($action, $details) {
    $logFile = '/Applications/XAMPP/xamppfiles/htdocs/Milestone1-ITSECWB/logs/admin_actions.log';
    $logMessage = date('[Y-m-d H:i:s]') . ' ' . $action . ': ' . $details . PHP_EOL;
    error_log($logMessage, 3, $logFile);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_id'])) {
    $update_id = $_POST['update_id'];
    $sql = "SELECT * FROM menu_items WHERE menu_item_id = '$update_id'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) == 1) {
        $menu_item = mysqli_fetch_assoc($result);
    } else {
        $_SESSION['message'] = "Menu item not found.";
        header("Location: admin_menu.php");
        exit();
    }
} else {
    $_SESSION['message'] = "Invalid request to update menu item.";
    header("Location: admin_menu.php");
    exit();
}

$name = isset($_POST['name']) ? $_POST['name'] : $menu_item['name'];
$price = isset($_POST['price']) ? $_POST['price'] : $menu_item['price'];
$category = isset($_POST['category']) ? $_POST['category'] : $menu_item['category'];
$stock_quantity = isset($_POST['stock_quantity']) ? $_POST['stock_quantity'] : $menu_item['stock_quantity'];
$description = isset($_POST['description']) ? $_POST['description'] : $menu_item['description'];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_menu'])) {
    $name = isset($_POST['name']) ? $_POST['name'] : '';
    $price = isset($_POST['price']) ? $_POST['price'] : '';
    $category = isset($_POST['category']) ? $_POST['category'] : '';
    $stock_quantity = isset($_POST['stock_quantity']) ? $_POST['stock_quantity'] : '';
    $description = isset($_POST['description']) ? $_POST['description'] : '';
    $update_id = isset($_POST['update_id']) ? $_POST['update_id'] : '';

    $fileName = $_FILES["image"]["name"];
    $fileSize = $_FILES["image"]["size"];
    $tmpName = $_FILES["image"]["tmp_name"];
    $validImageExtension = ['jpg', 'jpeg', 'png', 'webp'];
    $imageExtension = explode('.', $fileName);
    $imageExtension = strtolower(end($imageExtension));

    if ($stock_quantity < $low_stock_threshold) {
        $message = "Alert: Stock quantity is below the threshold.";
    } else {
        if ($_FILES["image"]["error"] != 4) { 
            if (!in_array($imageExtension, $validImageExtension)) {
                $message = "Error: Invalid Image Extension";
            } elseif ($fileSize > 1000000) {
                $message = "Error: Image Size Is Too Large";
            } else {
                $newImageName = uniqid() . '.' . $imageExtension;
                move_uploaded_file($tmpName, 'C:/xampp/htdocs/Milestone1-ITSECWB/images/' . $newImageName);

                $sql = "UPDATE menu_items SET name='$name', price='$price', category='$category', description='$description', stock_quantity='$stock_quantity', image='$newImageName' WHERE menu_item_id='$update_id'";
                if (mysqli_query($conn, $sql)) {
                    $message = "Menu item details have been successfully updated. You will now be redirected to the menu item page.";
                    logAction('Update Menu Item', 'Menu item updated: ' . $name); // Log the update action
                    echo '<meta http-equiv="refresh" content="4;url=admin_menu.php">';
                } else {
                    $message = "Error: " . $sql . "<br>" . mysqli_error($conn);
                }
            }
        } else {
            $sql = "UPDATE menu_items SET name='$name', price='$price', category='$category', description='$description', stock_quantity='$stock_quantity' WHERE menu_item_id='$update_id'";
            if (mysqli_query($conn, $sql)) {
                $message = "Menu item details have been successfully updated. You will now be redirected to the menu item page.";
                logAction('Update Menu Item', 'Menu item updated: ' . $name); // Log the update action
                echo '<meta http-equiv="refresh" content="4;url=admin_menu.php">';
            } else {
                $message = "Error: " . $sql . "<br>" . mysqli_error($conn);
            }
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['upload_xml'])) {
    if ($_FILES['xml_file']['error'] == UPLOAD_ERR_OK && $_FILES['xml_file']['tmp_name'] != "") {
        $xmlFileType = strtolower(pathinfo($_FILES['xml_file']['name'], PATHINFO_EXTENSION));
        if ($xmlFileType == 'xml') {
            $xmlData = simplexml_load_file($_FILES['xml_file']['tmp_name']);
            if ($xmlData !== false) {
                foreach ($xmlData->menu_item as $menuItem) {
                    $name = $menuItem->name;
                    $price = $menuItem->price;
                    $category = $menuItem->category;
                    $stock_quantity = $menuItem->stock_quantity;
                    $description = $menuItem->description;

                    $sql = "INSERT INTO menu_items (name, price, category, description, stock_quantity) VALUES ('$name', '$price', '$category', '$description', '$stock_quantity')";
                    if (mysqli_query($conn, $sql)) {
                        $message .= "Menu item '$name' added successfully.<br>";
                        logAction('Add Menu Item', 'Menu item added: ' . $name); // Log the add action
                        echo '<meta http-equiv="refresh" content="4;url=admin_menu.php">';
                    } else {
                        $message .= "Error adding menu item '$name'.<br>";
                    }
                }
            } else {
                $message = "Error: Invalid XML file.";
            }
        } else {
            $message = "Error: Please upload a valid XML file.";
        }
    } else {
        $message = "Error uploading XML file.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Menu Item - Your Restaurant Name</title>
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
            margin-bottom: 50px;
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
        .promotion-form select,
        .promotion-form textarea {
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

<!-- Main content -->
<div class="container">
    <!-- Menu item update form -->
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="promotion-container">
                <h2 class="promotion-title">Update Menu Item</h2>
                <?php if (!empty($message)) { ?>
                    <div class="alert-slide">
                        <?php echo $message; ?>
                    </div>
                <?php } ?>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="update_id" value="<?php echo $update_id; ?>">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?php echo $name; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="price">Price ($)</label>
                        <input type="number" step="0.01" min="0" class="form-control" id="price" name="price" value="<?php echo $price; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="category">Category</label>
                        <input type="text" class="form-control" id="category" name="category" value="<?php echo $category; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="stock_quantity">Stock Quantity</label>
                        <input type="number" class="form-control" id="stock_quantity" name="stock_quantity" value="<?php echo $stock_quantity; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"><?php echo $description; ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="image">Image (optional)</label>
                        <input type="file" class="form-control-file" id="image" name="image">
                    </div>
                    <button type="submit" class="btn btn-primary" name="update_menu">Update Menu Item</button>
                </form>
            </div>
        </div>
    </div>
</div>

</body>
</html>
