<?php
ob_start();
session_start();
include 'admin_navbar.php';
include 'db_connection.php';

$message = "";

// Function to log actions
function logAction($action)
{
    $logfile = 'logs\admin_actions.log';
    $logtime = date("Y-m-d H:i:s");
    $log_message = "[{$logtime}] {$action}\n";
    file_put_contents($logfile, $log_message, FILE_APPEND | LOCK_EX);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['create_specials'])) {
    $name = htmlspecialchars($_POST['name'], ENT_QUOTES, 'UTF-8');
    $description = htmlspecialchars($_POST['description'], ENT_QUOTES, 'UTF-8');
    $price = htmlspecialchars($_POST['price'], ENT_QUOTES, 'UTF-8');
    $start_date = htmlspecialchars($_POST['start_date'], ENT_QUOTES, 'UTF-8');
    $end_date = htmlspecialchars($_POST['end_date'], ENT_QUOTES, 'UTF-8');

    if ($start_date > $end_date) {
        $message = "End date should be after start date.";
    } else {
        $stmt = $conn->prepare("INSERT INTO specials (name, description, price, start_date, end_date) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $name, $description, $price, $start_date, $end_date);

        if ($stmt->execute()) {
            $message = "Specials created successfully.";
            logAction("Specials created: {$name}");
            echo '<meta http-equiv="refresh" content="2;url=admin_specials.php">';
        } else {
            $message = "Error: " . $stmt->error;
        }

        $stmt->close();
    }
}

if (isset($_POST['delete_id'])) {
    $delete_id = htmlspecialchars($_POST['delete_id'], ENT_QUOTES, 'UTF-8');
    $stmt = $conn->prepare("DELETE FROM specials WHERE specials_id = ?");
    $stmt->bind_param("i", $delete_id);

    if ($stmt->execute()) {
        $message = "Specials successfully deleted.";
        logAction("Specials deleted: ID {$delete_id}");
        echo '<meta http-equiv="refresh" content="2;url=admin_specials.php">';
    } else {
        $message = "Error deleting specials: " . $stmt->error;
    }

    $stmt->close();
}

$sql = "SELECT * FROM specials";
$result = mysqli_query($conn, $sql);
$specials = [];
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $specials[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Specials Management - Kape-Kada Coffee Shop</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@400;700&family=Montserrat:wght@300;400;700&display=swap" rel="stylesheet">
    <style>
        /* Your CSS styles here */
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="promotion-container">
                <h2 class="promotion-title">Specials Management</h2>
                <form class="promotion-form" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <div class="form-group">
                        <label for="name">Specials Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <input type="text" class="form-control" id="description" name="description" required>
                    </div>
                    <div class="form-group">
                        <label for="price">Price</label>
                        <input type="number" class="form-control" id="price" name="price" min="0.01" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label for="start_date">Start Date</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" required>
                    </div>
                    <div class="form-group">
                        <label for="end_date">End Date</label>
                        <input type="date" class="form-control" id="end_date" name="end_date" required>
                    </div>
                    <button type="submit" class="btn btn-primary" name="create_specials">Create Specials</button>
                </form>

                <?php if (!empty($message)) : ?>
                    <div class="alert-slide" id="alertSlide"><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></div>
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
                    <h3>List of Specials</h3>
                    <?php foreach ($specials as $special) : ?>
                        <div class="promotion-item">
                            <h4><?php echo htmlspecialchars($special['name'], ENT_QUOTES, 'UTF-8'); ?></h4>
                            <p><strong>Description:</strong> <?php echo htmlspecialchars($special['description'], ENT_QUOTES, 'UTF-8'); ?></p>
                            <p><strong>Price:</strong> <?php echo htmlspecialchars($special['price'], ENT_QUOTES, 'UTF-8'); ?></p>
                            <p><strong>Start Date:</strong> <?php echo htmlspecialchars($special['start_date'], ENT_QUOTES, 'UTF-8'); ?></p>
                            <p><strong>End Date:</strong> <?php echo htmlspecialchars($special['end_date'], ENT_QUOTES, 'UTF-8'); ?></p>
                            <form method="post" action="update_specials.php">
                                <input type="hidden" name="update_id" value="<?php echo htmlspecialchars($special['specials_id'], ENT_QUOTES, 'UTF-8'); ?>">
                                <button type="submit" class="btn btn-primary update-btn">Update</button>
                            </form>
                            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                <input type="hidden" name="delete_id" value="<?php echo htmlspecialchars($special['specials_id'], ENT_QUOTES, 'UTF-8'); ?>">
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
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
    // Timeout duration in milliseconds (15 minutes)
    const timeoutDuration = 15 * 60 * 1000;

    let logoutTimer;

    function resetLogoutTimer() {
        clearTimeout(logoutTimer);
        logoutTimer = setTimeout(() => {
            // Clear session and redirect to logout page when timeout occurs
            fetch('logout.php', { method: 'POST' })
                .then(() => {
                    window.location.href = "index.php";
                });
        }, timeoutDuration);
    }

    // Reset timer on user activity
    document.addEventListener("mousemove", resetLogoutTimer);
    document.addEventListener("keypress", resetLogoutTimer);

    // Initial setup of the timer
    resetLogoutTimer();
</script>

</body>
</html>

<?php
ob_end_flush();
?>
