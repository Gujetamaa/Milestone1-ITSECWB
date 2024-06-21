<?php
session_start(); 
include 'admin_navbar.php';
include 'db_connection.php';

$message = ""; 

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['generate_report'])) {
    $report_date = $_POST['report_date'];
    $sql = "SELECT * FROM orders WHERE DATE(order_date) = '$report_date'";
    $result = mysqli_query($conn, $sql);

    $total_dishes_sold = 0;
    $total_amount = 0;
    $total_discount = 0;

    if (mysqli_num_rows($result) > 0) {
        $xml = new SimpleXMLElement('<report></report>');
        $xml->addChild('date', $report_date);

        while ($row = mysqli_fetch_assoc($result)) {
            $order = $xml->addChild('order');
            $order->addChild('id', $row['id']);
            $order->addChild('user_id', $row['user_id']);
            $order->addChild('order_date', $row['order_date']);
            $order->addChild('total_price', $row['total_price']);
            $order->addChild('discount_amount', $row['discount_amount']);
            $order->addChild('quantity', $row['quantity']);
            $order->addChild('customer_address', $row['customer_address']);

            $total_dishes_sold += $row['quantity'];
            $total_amount += $row['total_price'];
            $total_discount += $row['discount_amount'];
        }

        $xml->addChild('total_dishes_sold', $total_dishes_sold);
        $xml->addChild('total_amount', $total_amount);
        $xml->addChild('total_discount', $total_discount);

        $file_name = 'order_report_' . $report_date . '.xml';
        $xml->asXML($file_name);

        $message = "Report generated successfully.";
        $download_link = '<a href="' . $file_name . '" download>Download Report</a>';
    } else {
        $message = "No orders found for the specified date.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Reporting - Your Restaurant Name</title>
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
        .promotion-form input[type="date"] {
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
<body>
    <div class="container">

        <!-- Order report generation form -->
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="promotion-container">
                    <h2 class="promotion-title">Order Reporting</h2>
                    <form class="promotion-form" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                        <div class="form-group">
                            <label for="report_date">Select Date:</label>
                            <input type="date" class="form-control" id="report_date" name="report_date" required>
                        </div>
                        <button type="submit" class="btn btn-block" name="generate_report">Generate Report</button>
                    </form>
                    <?php if (isset($download_link)) : ?>
                        <div class="download-link"><?php echo $download_link; ?></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <?php if (!empty($message)) : ?>
    <!-- Alert message -->
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

    <!-- Include necessary JavaScript -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>

</html>
