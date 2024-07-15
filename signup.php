<?php
// Include database connection
include 'db_connection.php';
include 'navbar.php';
session_start(); // Start session
$message = ""; // Initialize the message variable

$debug = true; 

function one(){
    two();
}
function two(){
    three();
} 
function three(){
   four();
}

function four(){
    $traceLines = callTracer();
    echo "<pre>";
    print_r($traceLines);  // Or handle $traceLines as needed
    echo "</pre>";
}
function callTracer(){                        
   // Start output buffering
   ob_start();
    
   // Print the backtrace to the buffer
   debug_print_backtrace();
   
   // Get the contents of the buffer
   $backtrace = ob_get_contents();
   
   // End output buffering and clean the buffer
   ob_end_clean();
   
   // Split the backtrace string into an array of lines
   $backtraceLines = explode("\n", $backtrace);
   return $backtraceLines;
   
}

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
    one();
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
    two();
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
    $password = $_POST['password'];
    $phoneNumber = $_POST['phoneNumber']; // Get phone number
    $role = 'User'; // Default role for signed up users
    $address = isset($_POST['address']) ? $_POST['address'] : null; // Get address if provided
    $wallet = isset($_POST['wallet']) ? $_POST['wallet'] : 0.00; // Get wallet balance if provided
    $picture = null; // Initialize picture variable
    
    $uploadOk = 0; // Flag to check upload success
    $targetDir = "uploads/";
    $targetFile = $targetDir.basename($_FILES['profile']['name']);
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    
    // Check if the uploaded file is an image
    
    if (isset($_FILES['profile']) && $_FILES['profile']['error'] == UPLOAD_ERR_OK) {
        $check = getimagesize($_FILES['profile']['tmp_name']);
        if ($check !== false) {
                $uploadOk = 1;
        } else {
            $message = "File is not an image.";
            $uploadOk = 0;
        }
        
        // Check file size (e.g., limit to 5MB)
        if ($_FILES['profile']['size'] > 5000000) {
            $message = "Your Image is too large.";
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
        three();
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

   if ($debug) {
    // Display detailed error message with stack trace
}

} else {
    // Ensures the password is not empty
    if (!empty($password)) {
        if ($uploadOk == 1) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert user data into the database with hashed password
            $sql = "INSERT INTO users (fullname, email, phoneNumber, password, role, wallet, address, picture)
                    VALUES ('$fullname', '$email', '$phoneNumber', '$hashed_password', '$role', $wallet, '$address', '$picture')";

            $result = mysqli_query($conn, $sql);

            if ($result) {
                $userId = mysqli_insert_id($conn); // Get the ID of the inserted user
                logSignupAction($userId, $fullname, $wallet); // Log signup action

                $message = "Welcome, $fullname! Registration successful. Redirecting...";
                // Set session variables after successful registration
                $_SESSION['email'] = $email;
                $_SESSION['fullname'] = $fullname;
                $_SESSION['role'] = $role;
                // Redirect to index.php after 4 seconds
                echo '<meta http-equiv="refresh" content="4;url=index.php">';
            } else {
                    // Display generic error message
                    $message = "Registration failed. Please try again later.";
            }
            four();
        } else {
            $message = "Please fill in all credentials.";
        }
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
