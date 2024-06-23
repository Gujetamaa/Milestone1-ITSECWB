<?php
session_start();
include 'admin_navbar.php';
include 'db_connection.php';

$message = ""; 

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_id'])) {
    $update_id = $_POST['update_id'];
    $sql = "SELECT * FROM specials WHERE specials_id = '$update_id'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) == 1) {
        $special = mysqli_fetch_assoc($result);
    } else {
       
        $_SESSION['message'] = "Specials not found.";
        header("Location: admin_specials.php");
        exit();
    }
} else {
    
    $_SESSION['message'] = "Invalid request to update specials.";
    header("Location: admin_specials.php");
    exit();
}


$name = isset($_POST['name']) ? $_POST['name'] : $special['name'];
$description = isset($_POST['description']) ? $_POST['description'] : $special['description'];
$price = isset($_POST['price']) ? $_POST['price'] : $special['price'];
$start_date = isset($_POST['start_date']) ? $_POST['start_date'] : $special['start_date'];
$end_date = isset($_POST['end_date']) ? $_POST['end_date'] : $special['end_date'];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_special'])) {
    $name = isset($_POST['name']) ? $_POST['name'] : '';
    $description = isset($_POST['description']) ? $_POST['description'] : '';
    $price = isset($_POST['price']) ? $_POST['price'] : '';
    $update_id = isset($_POST['update_id']) ? $_POST['update_id'] : '';

    $start_date = isset($_POST['start_date']) ? $_POST['start_date'] : '';
    $end_date = isset($_POST['end_date']) ? $_POST['end_date'] : '';

    if ($start_date > $end_date) {
        $message = "End date should be after start date.";
    } else {
        $sql = "UPDATE specials SET name='$name', description='$description', price='$price', start_date='$start_date', end_date='$end_date' WHERE id='$update_id'";
        if (mysqli_query($conn, $sql)) {
            $message = "Specials details have been successfully updated. You will now be redirected to the specials page.";
            echo '<meta http-equiv="refresh" content="4;url=admin_specials.php">';
        } else {
            $message = "Error: " . $sql . "<br>" . mysqli_error($conn);
        }        
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Specials - Kape-Kada Coffee Shop</title>
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
        .promotion-form input[type="date"] {
            margin-bottom: 20px;
        }
        .promotion-form button[type="submit"] {
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

<!-- Form for updating specials -->
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="promotion-container">
                <h2 class="promotion-title">Update Specials</h2>
                <form class="promotion-form" id="updateForm" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <div class="form-group">
                        <label for="name">Specials Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?php echo $special['name']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <input type="text" class="form-control" id="description" name="description" value="<?php echo $special['description']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="start_date">Start Date</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" value="<?php echo $special['start_date']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="end_date">End Date</label>
                        <input type="date" class="form-control" id="end_date" name="end_date" value="<?php echo $special['end_date']; ?>" required>
                    </div>
                    <input type="hidden" name="update_id" value="<?php echo $update_id; ?>">
                    <button type="submit" class="btn btn-block" name="update_special" onclick="return confirm('Are you sure you want to update this specials?')">Update Specials</button>
                </form>
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
