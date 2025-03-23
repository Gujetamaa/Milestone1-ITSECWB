<?php  
include 'db_connection.php';
include 'navbar.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $entered_otp = trim($_POST['otp']);

    // Ensure session has pending email
    if (!isset($_SESSION['pending_email'])) {
        $message = "Session expired or OTP not found. Please request a new OTP.";
    } else {
        $email = $_SESSION['pending_email'];

        // Retrieve OTP from the database and check expiry
        $stmt = $conn->prepare("SELECT otp, otp_expiry FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->bind_result($stored_otp, $otp_expiry);
        $stmt->fetch();
        $stmt->close();

        if ($stored_otp && $entered_otp === $stored_otp) {
            // Check if OTP is expired
            if (strtotime($otp_expiry) < time()) {
                $message = "Your OTP has expired. Please request a new one.";
            } else {
                // OTP matches, verification successful
                $stmt = $conn->prepare("UPDATE users SET otp = NULL, otp_expiry = NULL, verified = 1 WHERE email = ?");
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $stmt->close();

                unset($_SESSION['pending_email']); // Clear session
                $_SESSION['is_verified'] = true; // Mark user as verified

                echo "<script>
                        alert('OTP Verified Successfully! Redirecting to login...');
                        window.location.href='login.php';
                      </script>";
                exit();
            }
        } else {
            $message = "Invalid OTP. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP Verification</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@400;700&family=Montserrat:wght@300;400;700&display=swap" rel="stylesheet">
</head>
<style>
    body {
        background-color: #F5F5DC; /* Beige background for a warm feel */
        color: #6B4F4E; /* Coffee brown text for contrast */
        font-family: 'Montserrat', sans-serif;
        background-image: url('loginbg.svg');
        background-size: cover;
        background-repeat: no-repeat;
        background-attachment: fixed; 
        background-position: center;
    }
    .otp-container { 
        margin-top: 125px;
        display: flex;
        justify-content: center;
    }
    .otp-form {
        background: #FFFFFF;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        width: 100%;
        max-width: 500px;
        text-align: center;
    }
    .otp-title {
        color: #A52A2A; /* Rich brown color for titles */
        font-weight: 700;
        margin-bottom: 20px;
    }
    .form-control {
        margin-bottom: 20px;
    }
    .btn-verify {
        background-color: #6B4F4E; /* Coffee brown button */
        color: #FFFFFF;
        border: none;
        padding: 10px 20px;
        border-radius: 4px;
        cursor: pointer;
        transition: background-color 0.3s ease;
        width: 100%;
    }
    .btn-verify:hover {
        background-color: #A52A2A; /* Darker brown on hover */
    }
</style>
<body>
<div class="otp-container">
    <div class="otp-form">
        <h2 class="otp-title">OTP Verification</h2>
        <p>An OTP has been sent to your email. Please enter it below.</p>

        <?php if (!empty($message)) : ?>
            <div class="alert alert-<?php echo (strpos($message, 'Successfully') !== false) ? 'success' : 'danger'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form action="verify_mfa.php" method="post">
            <div class="form-group">
                <label for="otp">OTP Code</label>
                <input type="text" name="otp" id="otp" class="form-control" required>
            </div>
            <button type="submit" class="btn-verify">Verify OTP</button>
        </form>
    </div>
</div>
</body>
</html>
