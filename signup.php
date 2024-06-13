<?php
// Include database connection
include 'db_connection.php';
session_start(); // Start session

$message = ""; // Initialize the message variable

// Check if the user is already logged in and redirect based on their role
if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'Administrator') {
        header("Location: admin.php");
        exit();
    } else if ($_SESSION['role'] == 'User') {
        header("Location: user.php");
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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $phoneNumber = $_POST['phoneNumber']; // Get phone number
    $role = 'User'; // Default role for signed up users
    $address = isset($_POST['address']) ? $_POST['address'] : null; // Get address if provided
    $wallet = isset($_POST['wallet']) ? $_POST['wallet'] : 0.00; // Get wallet balance if provided
    $picture = null; // Initialize picture variable
    
    $uploadOk = 1; // Flag to check upload success
    $targetDir = "uploads/";
    $targetFile = $targetDir.basename($_FILES['profile']['name']);
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    // Check if the uploaded file is an image
    if (isset($_FILES['profile']) && $_FILES['profile']['error'] == UPLOAD_ERR_OK) {
        $check = getimagesize($_FILES['profile']['tmp_name']);
        if ($check !== false) {
            $message = "File is an image - " . $check['mime'] . ".";
        } else {
            $message = "File is not an image.";
            $uploadOk = 0;
        }

        // Check file size (e.g., limit to 5MB)
        if ($_FILES['profile']['size'] > 5000000) {
            $message = "Your file is too large.";
            $uploadOk = 0;
        }

        // Allow certain file formats
        $allowedFileTypes = ['jpg', 'jpeg', 'png'];
        if (!in_array($imageFileType, $allowedFileTypes)) {
            $message = "Only JPG, JPEG, and PNG files are allowed.";
            $uploadOk = 0;
        }

        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 1) {
            if (move_uploaded_file($_FILES['profile']['tmp_name'], $targetFile)) {
                $picture = $targetFile; // Set $picture to the path of the uploaded file
            } else {
                $message = "Sorry, there was an error uploading your file.";
            }
        }
    } else {
        $message = "No file uploaded or upload error.";
    }

    // Validate email and phone number
    if (!isValidEmail($email)) {
        if (!isValidPhoneNumber($phoneNumber)) {
            $message = "Invalid email and phone number.";
        } else {
            $message = "Invalid email address. Please enter a valid email.";
        } 
    } elseif (!isValidPhoneNumber($phoneNumber)) {
        $message = "Invalid phone number. Please enter a valid phone number.";
    } else {
        // Ensures the password is not empty
        if (!empty($password) && !empty($fullname) && !empty($address) ) {
            // Hash the password with salted rounds
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert user data into the database with hashed password
            $sql = "INSERT INTO users (fullname, email, phoneNumber, password, role, wallet, address, picture)
                    VALUES ('$fullname', '$email', '$phoneNumber', '$hashed_password', '$role', $wallet, '$address', '$picture')";

            $result = mysqli_query($conn, $sql);

            if ($result) {
                $message = "Welcome, $fullname! Registration successful. Redirecting...";
                // Set session variables after successful registration
                $_SESSION['email'] = $email;
                $_SESSION['fullname'] = $fullname;
                $_SESSION['role'] = $role;
                // Redirect to index.php after 4 seconds
                echo '<meta http-equiv="refresh" content="4;url=index.php">';
            } else {
                $message = "Error: " . mysqli_error($conn);
            }
        } else {
            $message = "Please fill in all credentials.";
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
            background-color: #A52A2A;
            color: #FFFFFF;
        }
        .login-link {
            text-align: center;
            margin-top: 20px;
        }
        .login-link a {
            color: #A52A2A;
            text-decoration: none;
        }
        .login-link a:hover {
            text-decoration: underline;
        }
        .alert-slide {
            position: fixed;
            top: 170px; /* Adjusted top position to be below the navbar */
            left: 20px; /* Adjusted left position */
            z-index: 9999;
            background-color: #A52A2A;
            color: #FFFFFF;
            padding: 10px 20px;
            border-radius: 8px;
            animation: slideIn 0.5s ease forwards;
        }
        @keyframes slideIn {
            0% {
                left: -100%;
            }
            100% {
                left: 20px; /* Slide in from the left */
            }
        }
        @keyframes slideOut {
            0% {
                left: 20px;
            }
            100% {
                left: -100%; /* Slide out to the left */
            }
        }
    </style>
</head>
<body>

<div class="container signup-container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <form class="signup-form" method="post" action="signup.php" enctype="multipart/form-data">
                <h2 class="signup-title">Sign Up</h2>
                <div class="form-group">
                    <input type="text" class="form-control" id="fullname" name="fullname" placeholder="Full Name"> <!-- Add name attribute -->
                </div>
                <div class="form-group">
                    <input type="text" class="form-control" id="email" name="email" placeholder="Email" > <!-- Add name attribute -->
                </div>
                <div class="form-group">
                    <input type="password" class="form-control" id="password" name="password" placeholder="Password"> <!-- Add name attribute -->
                </div>
                <div class="form-group">
                    <input type="text" class="form-control" id="phoneNumber" name="phoneNumber" placeholder="Phone Number"> <!-- Add phoneNumber field -->
                </div>
                <div class="form-group">
                    <input type="text" class="form-control" id="address" name="address" placeholder="Address"> <!-- Add address field -->
                </div>
                <div class="form-group">
                    <input type="number" step="0.01" class="form-control" id="wallet" name="wallet" placeholder="Wallet Balance"> <!-- Add wallet balance field -->
                </div>
                <div class = "form-group">
                    <input type="file" class="form-control" id="profile" name="profile" placeholder="Upload Your Profile Pic" accept="image/*">
                </div>
                <div class="login-link">
                    <a href="index.php">Already have an account?</a>
                </div>
                <button type="submit" class="btn btn-block btn-signup">Sign Up</button>
                
            </form> 
    </div>
</div>

<!-- Display slide prompt message if set -->
<?php if (!empty($message)) : ?>
<div class="alert-slide" id="alertSlide"><?php echo $message; ?></div>
<script>
    // Slide up the prompt message after 5 seconds
    setTimeout(function() {
        var alertSlide = document.getElementById('alertSlide');
        if (alertSlide) {
            alertSlide.style.animation = 'slideIn 0.5s ease forwards';
            setTimeout(function() {
                alertSlide.style.display = 'none'; // Hide the prompt message after sliding up
            }, 5000);
        }
    }, 500);
</script>
<?php endif; ?>

<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
