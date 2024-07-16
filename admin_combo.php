<?php
ob_start();
session_start();
include 'admin_navbar.php';
include 'db_connection.php';

$message = "";
$low_stock_threshold = 10;

// Function to log actions
function logAction($action)
{
<<<<<<< Updated upstream
    $logfile = 'C:/xampp/htdocs/Milestone1-ITSECWB/logs/admin_actions.log';
=======
    $logfile = 'C:\xampp\htdocs\Milestone1-ITSECWB\logs\admin_actions.log';
>>>>>>> Stashed changes
    $logtime = date("Y-m-d H:i:s");
    $log_message = "[{$logtime}] {$action}\n";
    file_put_contents($logfile, $log_message, FILE_APPEND | LOCK_EX);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['create_combo'])) {
    $name = isset($_POST['name']) ? htmlspecialchars($_POST['name']) : '';
    $description = isset($_POST['description']) ? htmlspecialchars($_POST['description']) : '';
    $main_dish = isset($_POST['main_dish']) ? htmlspecialchars($_POST['main_dish']) : '';
    $side_dish = isset($_POST['side_dish']) ? htmlspecialchars($_POST['side_dish']) : '';
    $drink = isset($_POST['drink']) ? htmlspecialchars($_POST['drink']) : '';
    $price = isset($_POST['price']) ? floatval($_POST['price']) : '';
    $discount_percentage = isset($_POST['discount_percentage']) ? floatval($_POST['discount_percentage']) : '';
    $category = isset($_POST['category']) ? htmlspecialchars($_POST['category']) : '';
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : '';

    $stmt = $conn->prepare("INSERT INTO combo_meals (name, description, main_dish, side_dish, drink, price, discount_percentage, category, quantity) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssdsi", $name, $description, $main_dish, $side_dish, $drink, $price, $discount_percentage, $category, $quantity);
    if ($stmt->execute()) {
        $message = "Combo meal created successfully.";
        logAction("Combo meal created: {$name}");
        header("refresh:2;url=admin_combo.php");
    } else {
        $message = "Error: " . $stmt->error;
    }
}

if (isset($_POST['delete_id'])) {
    $delete_id = $_POST['delete_id'];
    $stmt = $conn->prepare("DELETE FROM combo_meals WHERE combo_id = ?");
    $stmt->bind_param("i", $delete_id);
    if ($stmt->execute()) {
        $message = "Combo meal successfully deleted.";
        logAction("Combo meal deleted: ID {$delete_id}");
        header("refresh:2;url=admin_combo.php");
    } else {
        $message = "Error deleting combo meal: " . $stmt->error;
    }
}

$sql = "SELECT * FROM combo_meals";
$result = $conn->query($sql);
$combo_meals = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $combo_meals[] = $row;
    }
}

$menu_items = [];
$categories = ['Mains', 'Sides', 'Drink'];
foreach ($categories as $category) {
    $sql = "SELECT * FROM menu_items WHERE category = '$category'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $menu_items[$category][] = $row;
        }
    }
}

if (!empty($low_stock_alerts)) {
    $message = "Low stock alerts for combo meals: " . implode(", ", $low_stock_alerts) . ". Quantity is below the threshold.";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Combo Meal Management - Kape-Kada Coffee Shop</title>
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
        .promotion-form input[type="date"] {
            margin-bottom: 20px;
        }

        .promotion-form button[type="submit"] {
            background-color: #A52A2A;
            color: #FFFFFF;
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

        .promotion-list {
            margin-top: 30px;
        }

        .promotion-item {
            background-color: #F9F9F9;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }

        .promotion-item h4 {
            color: #A52A2A;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .promotion-item p {
            margin-bottom: 5px;
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

        .promotion-list h3 {
            margin-bottom: 15px;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="promotion-container">
                    <h2 class="promotion-title">Combo Meal Management</h2>
                    <form class="promotion-form" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                        <div class="form-group">
                            <label for="name">Combo Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="description">Description</label>
                            <input type="text" class="form-control" id="description" name="description" required>
                        </div>
                        <div class="form-group">
                            <label for="main_dish">Main Dish</label>
                            <select class="form-control" id="main_dish" name="main_dish" required>
                                <option value="">Select Main Dish</option>
                                <?php
                                $sql = "SELECT * FROM menu_items WHERE category = 'Mains'";
                                $result = mysqli_query($conn, $sql);
                                while ($row = mysqli_fetch_assoc($result)) {
                                    echo "<option value='" . $row['name'] . "'>" . $row['name'] . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="side_dish">Side Dish</label>
                            <select class="form-control" id="side_dish" name="side_dish" required>
                                <option value="">Select Side Dish</option>
                                <?php
                                $sql = "SELECT * FROM menu_items WHERE category = 'Sides'";
                                $result = mysqli_query($conn, $sql);
                                while ($row = mysqli_fetch_assoc($result)) {
                                    echo "<option value='" . $row['name'] . "'>" . $row['name'] . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="drink">Drink</label>
                            <select class="form-control" id="drink" name="drink" required>
                                <option value="">Select Drink</option>
                                <?php
                                $sql = "SELECT * FROM menu_items WHERE category = 'Drink'";
                                $result = mysqli_query($conn, $sql);
                                while ($row = mysqli_fetch_assoc($result)) {
                                    echo "<option value='" . $row['name'] . "'>" . $row['name'] . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="price">Price</label>
                            <input type="number" step="any" class="form-control" id="price" name="price" required>
                        </div>
                        <div class="form-group">
                            <label for="discount_percentage">Discount Percentage</label>
                            <input type="number" step="any" class="form-control" id="discount_percentage" name="discount_percentage">
                        </div>
                        <div class="form-group">
                            <label for="category">Category</label>
                            <select class="form-control" id="category" name="category" required>
                            <option value="Morning">Morning</option>
                            <option value="Evening">Evening</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="quantity">Quantity</label>
                            <input type="number" class="form-control" id="quantity" name="quantity" required>
                        </div>
                        <button type="submit" class="btn btn-primary" name="create_combo">Create Combo Meal</button>
                    </form>

                    <?php if (!empty($message)) : ?>
                        <div class="alert-slide" id="alertSlide"><?php echo $message; ?></div>
                        <script>
                            setTimeout(function() {
                                var alertSlide = document.getElementById('alertSlide');
                                if (alertSlide && alertSlide.innerText.trim() !== '') {
                                    alertSlide.style.animation = 'slideOut 0.5s ease forwards';
                                    setTimeout(function() {
                                        alertSlide.style.display = 'none';
                                    }, 500);
                                }
                            }, 5000);
                        </script>
                    <?php endif; ?>

                    <div class="promotion-list">
                        <h3>List of Combo Meals</h3>
                        <?php foreach ($combo_meals as $combo) : ?>
                            <div class="promotion-item">
                                <h4><?php echo $combo['name']; ?></h4>
                                <p><strong>Description:</strong> <?php echo $combo['description']; ?></p>
                                <p><strong>Main Dish:</strong> <?php echo $combo['main_dish']; ?></p>
                                <p><strong>Side Dish:</strong> <?php echo $combo['side_dish']; ?></p>
                                <p><strong>Drink:</strong> <?php echo $combo['drink']; ?></p>
                                <p><strong>Price:</strong> <?php echo $combo['price']; ?></p>
                                <p><strong>Discount Percentage:</strong> <?php echo $combo['discount_percentage']; ?></p>
                                <p><strong>Category:</strong> <?php echo $combo['category']; ?></p>
                                <p><strong>Quantity:</strong> <?php echo $combo['quantity']; ?></p>
                                <form method="post" action="update_combo.php">
                                    <input type="hidden" name="update_id" value="<?php echo $combo['combo_id']; ?>">
                                    <button type="submit" class="btn btn-primary update-btn">Update</button>
                                    </form>
                                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                    <input type="hidden" name="delete_id" value="<?php echo $combo['combo_id']; ?>">
                                    <button type="submit" class="btn btn-danger">Delete</button>
                                </form>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>

</html>
