<?php
session_start();
include 'admin_navbar.php';
include 'db_connection.php';

$message = "";

// Function to log actions to admin_actions.log
function logAction($action, $details) {
    $logFile = 'logs\admin_actions.log';
    $timestamp = date('[Y-m-d H:i:s]');

    $logMessage = "$timestamp [$action] $details" . PHP_EOL;

    file_put_contents($logFile, $logMessage, FILE_APPEND | LOCK_EX);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['generate_report'])) {
    $sql = "SELECT * FROM specials";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $xml = new SimpleXMLElement('<report></report>');

        while ($row = mysqli_fetch_assoc($result)) {
            $special = $xml->addChild('special');
            $special->addChild('specials_id', $row['specials_id']);
            $special->addChild('name', $row['name']);
            $special->addChild('description', $row['description']);
            $special->addChild('price', $row['price']);
            $special->addChild('start_date', $row['start_date']);
            $special->addChild('end_date', $row['end_date']);
        }

        $file_name = 'specials_report_' . date('Y-m-d') . '.xml';
        $xml->asXML($file_name);

        $message = "Specials report generated successfully.";
        $download_link = '<a href="' . $file_name . '" download>Download Report</a>';

        logAction('GENERATE_SPECIALS_REPORT', 'Generated specials report');
    } else {
        $message = "No specials found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Specials Reporting - Kape Kada</title>
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
    <div class="container">
        <!-- Specials report generation form -->
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="promotion-container">
                    <h2 class="promotion-title">Specials Reporting</h2>
                    <form class="promotion-form" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
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
