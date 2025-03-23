<?php
// Include database connection
include 'db_connection.php';
include 'navbar.php';
require 'vendor/autoload.php'; // Composer PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

session_start(); // Start session

$message = ""; // Initialize the message variable

// Check if the user is already logged in and redirect based on their role
if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'Administrator') {
        header("Location: admin.php");
        exit();
    } else if ($_SESSION['role'] == 'User') {
        header("Location: index.php");
        exit();
    }
}

function isValidEmail($email) {
    // Check for the presence of one "@" symbol
    if (substr_count($email, '@') !== 1) {
        return false;
    }

    // Split the email into local part and domain
    list($localPart, $domain) = explode('@', $email);

    // Check the length of the local part
    if (strlen($localPart) > 64) {
        return false;
    }

    // Check the length of the domain
    if (strlen($domain) > 255) {
        return false;
    }

    // Validate the local part and domain with a regular expression
    $localPartPattern = '/^[a-zA-Z0-9.!#$%&\'*+\/=?^_`{|}~-]+$/';
    $domainPattern = '/^[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';

    if (!preg_match($localPartPattern, $localPart) || !preg_match($domainPattern, $domain)) {
        return false;
    }

    return true;
}

function isValidPhoneNumber($phoneNumber) {
    // Check if the phone number starts with "+63"
    if (substr($phoneNumber, 0, 3) === "+63") {
        // Remove "+63" prefix for further validation
        $phoneNumber = substr($phoneNumber, 3);

        // Check if the remaining phone number has exactly 10 digits
        if (strlen($phoneNumber) !== 10) {
            return false;
        }
    } else {
        // Check if the phone number starts with "09"
        if (substr($phoneNumber, 0, 2) !== "09") {
            return false;
        }

        // Check if the phone number has exactly 11 digits
        if (strlen($phoneNumber) !== 11) {
            return false;
        }
    }

    // Check if the remaining phone number contains only numeric digits
    if (!ctype_digit($phoneNumber)) {
        return false;
    }

    return true;
}

// Function to log signup actions
function logSignupAction($userId, $fullname, $wallet) {
    $logFile = 'logs\signup_actions.log';
    $signupTime = date('Y-m-d H:i:s');
    $logMessage = "User ID: $userId | Fullname: $fullname | Wallet: $wallet | Signup Time: $signupTime" . PHP_EOL;
    file_put_contents($logFile, $logMessage, FILE_APPEND);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = $_POST['fullname']; 
    $email = $_POST['email'];
    $birthday = $_POST['birthday']; 
    $password = $_POST['password'];
    $phoneNumber = $_POST['phoneNumber'];
    $role = 'User';
    $address = $_POST['address'] ?? null;
    $wallet = $_POST['wallet'] ?? 0.00;
    $picture = null; 

    $uploadOk = 0; 
    $targetDir = "uploads/";

    if (!empty($_FILES['profile']['name'])) {
        $targetFile = $targetDir . basename($_FILES['profile']['name']);
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        if (getimagesize($_FILES['profile']['tmp_name']) !== false) {
            $uploadOk = 1;
        } else {
            $message = "File is not an image.";
        }

        if ($_FILES['profile']['size'] > 5000000) {
            $message = "Your image is too large.";
            $uploadOk = 0;
        }

        if (!in_array($imageFileType, ['jpg', 'jpeg', 'png'])) {
            $message = "Only JPG, JPEG, and PNG files are allowed.";
            $uploadOk = 0;
        }

        if ($uploadOk == 1) {
            if (move_uploaded_file($_FILES['profile']['tmp_name'], $targetFile)) {
                $picture = $targetFile;
            } else {
                $message = "Error uploading file.";
            }
        }
    }

    if (!isValidEmail($email)) {
        $message = !isValidPhoneNumber($phoneNumber) ? "Invalid email and phone number." : "Invalid email address.";
    } elseif (!isValidPhoneNumber($phoneNumber)) {
        $message = "Invalid phone number.";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $verification_token = bin2hex(random_bytes(32));
        $mfa_code = mt_rand(100000, 999999);
        $otp_expiry = date("Y-m-d H:i:s", strtotime("+10 minutes"));

        $sql = "INSERT INTO users (fullname, email, phoneNumber, birthday, password, role, wallet, address, picture, verification_token, verified, otp, otp_expiry) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssdsssss", $fullname, $email, $phoneNumber, $birthday, $hashed_password, $role, $wallet, $address, $picture, $verification_token, $mfa_code, $otp_expiry);

        if ($stmt->execute()) {
            $_SESSION['pending_email'] = $email;

            // Send OTP email
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = "lunarmoonzpt@gmail.com"; 
                $mail->Password = "efcd qrbo lbzj jgqe"; 
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                $mail->setFrom("lunarmoonzpt@gmail.com", "Kape-Kada Coffee Shop");
                $mail->addAddress($email, $fullname);
                $mail->isHTML(true);
                $mail->Subject = 'Your MFA Code for Kape-Kada Coffee Shop';
                $mail->Body = "Hello $fullname, <br>Your MFA code is: <b>$mfa_code</b>. This code will expire in 10 minutes.";
                $mail->send();

                $_SESSION['pending_email'] = $email;
                $message = "Redirecting to OTP Verification...";
                echo '<meta http-equiv="refresh" content="4;url=verify_mfa.php">';
            } catch (Exception $e) {
                $message = "Email could not be sent. Error: {$mail->ErrorInfo}";
            }
        } else {
            $message = "Error: " . $stmt->error;
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Kape-Kada Coffee Shop</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@400;700&family=Montserrat:wght@300;400;700&display=swap" rel="stylesheet">
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
        .navbar {
            font-family: 'Merriweather', serif;
        }
        .signup-container {
            margin-top: 125px;
        }
        .signup-form {
            background: #FFFFFF;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .signup-title {
            color: #A52A2A; /* Rich brown color for titles */
            font-weight: 700;
            margin-bottom: 30px;
        }
        .form-control {
            margin-bottom: 20px;
        }
        .btn-signup {
            background-color: #6B4F4E; /* Coffee brown button */
            color: #FFFFFF;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .btn-signup:hover {
            background-color: #A52A2A; /* Darker brown on hover */
        }
        .login-link {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #6B4F4E; /* Coffee brown link color */
        }
        .login-link a {
            color: #A52A2A; /* Darker brown link color */
            text-decoration: none;
        }
        .login-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <!-- Sign Up Form -->
    <div class="container signup-container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="signup-form">
                    <h2 class="signup-title">Sign Up</h2>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
                        <?php if (!empty($message)) : ?>
                            <div class="alert alert-danger"><?php echo $message; ?></div>
                        <?php endif; ?>
                        <div class="form-group">
                            <label for="fullname">Full Name</label>
                            <input type="text" name="fullname" id="fullname" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="email" name="email" id="email" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="birthday">Birthday</label>
                            <input type="date" name="birthday" id="birthday" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" name="password" id="password" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="phoneNumber">Phone Number</label>
                            <input type="tel" name="phoneNumber" id="phoneNumber" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="address">Address</label>
                            <textarea name="address" id="address" class="form-control"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="wallet">Initial Wallet Balance (PHP)</label>
                            <input type="number" name="wallet" id="wallet" class="form-control" step="any" min="0">
                        </div>
                        <div class="form-group">
                            <label for="profile">Profile Picture</label>
                            <input type="file" name="profile" id="profile" class="form-control-file">
                        </div>
                        <button type="submit" class="btn btn-signup btn-block">Sign Up</button>
                    </form>
                    <div class="login-link">
                        <p>Already have an account? <a href="login.php">Log in here</a>.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
