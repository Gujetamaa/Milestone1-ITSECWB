<?php 
include 'navbar.php'; 
include 'db_connection.php';

// Function to log actions to user_actions.log
function logAction($action, $details) {
    $logFile = 'logs\user_actions.log';
    $timestamp = date('[Y-m-d H:i:s]');

    // Prepare log message
    $logMessage = "$timestamp [$action] $details" . PHP_EOL;

    // Append to log file
    file_put_contents($logFile, $logMessage, FILE_APPEND | LOCK_EX);
}

if(isset($_SESSION['email'])) {
    $userEmail = $_SESSION['email'];
    $walletQuery = "SELECT wallet FROM users WHERE email = '$userEmail'";
    $walletResult = mysqli_query($conn, $walletQuery);
    $userWallet = mysqli_fetch_assoc($walletResult)['wallet'];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['topup_button'])) {
        $topupAmount = 0; 

        if (!empty($_POST['custom_topup_amount'])) {
            $topupAmount = floatval($_POST['custom_topup_amount']);
        } elseif (!empty($_POST['topup_amount'])) {
            $topupAmount = floatval($_POST['topup_amount']);
        }

        if ($topupAmount > 0) {
            $message = "Top-up successful! ₱" . number_format($topupAmount, 2) . " added to your wallet.";

            $updateQuery = "UPDATE users SET wallet = wallet + $topupAmount WHERE email = '$userEmail'";
            mysqli_query($conn, $updateQuery);
            
            // Retrieve updated wallet balance
            $walletQuery = "SELECT wallet FROM users WHERE email = '$userEmail'";
            $walletResult = mysqli_query($conn, $walletQuery);
            $userWallet = mysqli_fetch_assoc($walletResult)['wallet'];

            // Log the action
            logAction('TOP_UP_WALLET', "User $userEmail topped up wallet by ₱$topupAmount");
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wallet - Kape-Kada Coffee Shop</title>
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
        .wallet-container {
            margin-top: 140px;
            background-color: #FFFFFF;
            padding: 30px;
            border-radius: 10px;
        }
        .wallet-info {
            text-align: center;
            margin-bottom: 30px;
        }
        .wallet-balance {
            font-size: 24px;
            color: #D17A22;
        }
        .topup-form {
            max-width: 400px;
            margin: 0 auto;
        }
        .btn-primary {
            background-color: #A0522D;
            color: #FFFFFF;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        .btn-primary:hover {
            background-color: #874B21;
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

<div class="container wallet-container">
    <div class="wallet-info">
        <h1>Your Wallet Balance</h1>
        <p class="wallet-balance">₱<?php echo number_format($userWallet, 2); ?></p>
    </div>
    <div class="topup-form">
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="form-group">
                <label for="topup_amount">Select or Enter Top-up Amount (PHP)</label>
                <div class="input-group">
                    <select class="form-control" id="topup_amount" name="topup_amount">
                        <option value="20">₱20</option>
                        <option value="50">₱50</option>
                        <option value="100">₱100</option>
                        <option value="200">₱200</option>
                        <option value="500">₱500</option>
                        <option value="1000">₱1000</option>
                    </select>
                    <div class="input-group-append">
                        <span class="input-group-text">Or</span>
                    </div>
                    <input type="number" step="0.01" min="0" class="form-control" id="custom_topup_amount" name="custom_topup_amount" placeholder="Enter amount">
                </div>
                <small class="form-text text-muted">Enter your own amount or choose from the list</small>
            </div>
            <button type="submit" class="btn btn-primary" name="topup_button">Top-up</button>
        </form>
    </div>
</div>

<?php if (!empty($message)) : ?>
<div class="alert-slide" id="alertSlide"><?php echo $message; ?></div>
<script>
    setTimeout(function() {
        var alertSlide = document.getElementById('alertSlide');
        if (alertSlide && alertSlide.innerText.trim() !== '') {
            alertSlide.style.animation = 'slideOut 1.5s ease forwards';
            setTimeout(function() {
                alertSlide.style.display = 'none';
            }, 500);
        }
    }, 5000);
</script>
<?php endif; ?>

<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
