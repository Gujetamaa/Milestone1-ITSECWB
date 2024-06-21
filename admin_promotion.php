<?php
session_start(); 
include 'admin_navbar.php';
include 'db_connection.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['create_promotion'])) {
    $name = isset($_POST['name']) ? $_POST['name'] : '';
    $description = isset($_POST['description']) ? $_POST['description'] : '';
    $price = isset($_POST['price']) ? $_POST['price'] : '';

    $start_date = isset($_POST['start_date']) ? $_POST['start_date'] : '';
    $end_date = isset($_POST['end_date']) ? $_POST['end_date'] : '';

    if ($start_date > $end_date) {
        $message = "End date should be after start date.";
    } else {
        $sql = "INSERT INTO promotions (name, description, price, start_date, end_date) VALUES ('$name', '$description', '$price', '$start_date', '$end_date')";
        if (mysqli_query($conn, $sql)) {
            $message = "Promotion created successfully.";
            echo '<meta http-equiv="refresh" content="2;url=admin_promotion.php">';
        } else {
            $message = "Error: " . $sql . "<br>" . mysqli_error($conn);
        }
    }
}

if(isset($_POST['delete_id'])) {
    $delete_id = $_POST['delete_id'];
    $delete_sql = "DELETE FROM promotions WHERE id = '$delete_id'";
    if (mysqli_query($conn, $delete_sql)) {
        $message = "Promotion successfully deleted.";
        echo '<meta http-equiv="refresh" content="2;url=admin_promotion.php">';
    } else {
        $message = "Error deleting promotion: " . mysqli_error($conn);
    }
}

$sql = "SELECT * FROM promotions";
$result = mysqli_query($conn, $sql);
$promotions = [];
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $promotions[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Promotions Management - Kape-Kada Coffee Shop</title>
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
                <h2 class="promotion-title">Promotions Management</h2>
                <form class="promotion-form" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <div class="form-group">
                        <label for="name">Promotion Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <input type="text" class="form-control" id="description" name="description" required>
                    </div>
                    <div class="form-group">
                        <label for="price">Price</label>
                        <input type="number" class="form-control" id="price" name="price" min="0" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label for="start_date">Start Date</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" required>
                    </div>
                    <div class="form-group">
                        <label for="end_date">End Date</label>
                        <input type="date" class="form-control" id="end_date" name="end_date" required>
                    </div>
                    <button type="submit" class="btn btn-block" name="create_promotion">Create Promotion</button>
                </form>
            </div>
            <div class="promotion-list">
                <h3 class="promotion-title">Current Promotions</h3>
                <?php if (!empty($promotions)) : ?>
                    <?php foreach ($promotions as $key => $promotion) : ?>
                        <div class="promotion-item" id="promotion_<?php echo $key; ?>">
                            <h4><?php echo $promotion['name']; ?></h4>
                            <p>Description: <?php echo $promotion['description']; ?></p>
                            <p>Price: ₱<?php echo number_format($promotion['price'], 2); ?></p>
                            <p>Start Date: <?php echo $promotion['start_date']; ?></p>
                            <p>End Date: <?php echo $promotion['end_date']; ?></p>
                            <div class="promotion-buttons">
                                <form method="post" action="update_promotion.php">
                                    <input type="hidden" name="update_id" value="<?php echo $promotion['id']; ?>">
                                    <button type="submit" class="btn btn-primary update-btn">Update</button>
                                </form>
                                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                    <input type="hidden" name="delete_id" value="<?php echo $promotion['id']; ?>">
                                    <button type="submit" class="btn btn-danger delete-btn" onclick="return confirm('Are you sure you want to delete this promotion?')">Delete</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else : ?>
                    <p>No promotions available.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

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

<!-- Bootstrap JavaScript -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>