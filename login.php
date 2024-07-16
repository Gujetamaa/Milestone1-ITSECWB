<?php 
// Start the session
session_start();

// Logging function
function logAction($action, $details = '') {
<<<<<<< Updated upstream
    $logFile = 'C:/xampp/htdocs/Milestone1-ITSECWB/logs/login_actions.log'; // Adjust path and filename as needed
=======
    $logFile = 'C:\xampp\htdocs\Milestone1-ITSECWB\logs\login_actions.log'; // Adjust path and filename as needed
>>>>>>> Stashed changes
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[{$timestamp}] [{$action}] {$details}\n";
    file_put_contents($logFile, $logMessage, FILE_APPEND);
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = ['items' => [], 'combos' => []];
}

// Include database connection
include 'db_connection.php';

if(isset($_COOKIE['cart_data'])) {
    $cookieCartData = unserialize($_COOKIE['cart_data']);
    $_SESSION['cart'] = array_merge($_SESSION['cart'], $cookieCartData);
    setcookie('cart_data', '', time() - 3600, "/"); 
}

if (isset($_SESSION['email']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'Administrator') {
        header("Location: admin.php");
        exit();
    } else if ($_SESSION['role'] == 'User') {
        header("Location: index.php");
        exit();
    }
}

include 'navbar.php';

$message = ""; // Initialize the message variable

// Function to check if the user is banned
function is_banned($email) {
    global $conn;
    //$sql = "SELECT ban_time FROM users WHERE email='$email'";
    $sql = "SELECT ban_time FROM users WHERE BINARY email='$email'";

    $result = mysqli_query($conn, $sql);
    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        if ($user['ban_time'] > time()) {
            return true;
        }
    }
    return false;
}

// Function to check if ban time has expired
function is_ban_time_expired($email) {
    global $conn;
    //$sql = "SELECT ban_time FROM users WHERE email='$email'";
    $sql = "SELECT ban_time FROM users WHERE BINARY email='$email'";

    $result = mysqli_query($conn, $sql);
    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        if ($user['ban_time'] && $user['ban_time'] <= time()) {
            return true;
        }
    }
    return false;
}

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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check if ban time has expired
    if (is_ban_time_expired($email)) {
        // Reset login attempts and ban time
        //$sql = "UPDATE users SET login_attempts = 0, ban_time = NULL WHERE email='$email'";
        $sql = "UPDATE users SET login_attempts = 0, ban_time = NULL WHERE BINARY email='$email'";

        mysqli_query($conn, $sql);
    }

    // Query to fetch user data based on email
    //$sql = "SELECT * FROM users WHERE email='$email'";
    $sql = "SELECT * FROM users WHERE BINARY email='$email'";

    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        
        // Increment login attempts
        $login_attempts = $user['login_attempts'] + 1;

        // Update login attempts
        //$sql = "UPDATE users SET login_attempts = $login_attempts WHERE email='$email'";
        $sql = "UPDATE users SET login_attempts = $login_attempts WHERE BINARY email='$email'";
        
        mysqli_query($conn, $sql);

        if (is_banned($email)) {
            if ($login_attempts == 3) {
                $message = "Your access has been temporarily disabled for 5 minutes due to multiple failed login attempts. Please try again later.";
                logAction('Login', "User {$email} temporarily banned due to multiple failed login attempts");
            } elseif ($login_attempts >= 4){
                $message = "Access denied. Please try again later.";
            }

             // Store message in session and redirect
             $_SESSION['message'] = $message;
             header("Location: " . $_SERVER['PHP_SELF']);
             exit();
        } else {
            // Verify hashed password
            if (password_verify($password, $user['password'])) {
                // Reset login attempts and ban time on successful login
                //$sql = "UPDATE users SET login_attempts = 0, ban_time = NULL WHERE email='$email'";
                $sql = "UPDATE users SET login_attempts = 0, ban_time = NULL WHERE BINARY email='$email'";

                mysqli_query($conn, $sql);

                if ($user['role'] == 'Administrator') {
                    $message = "Welcome back, " . $user['fullname'] . "! You are logged in as an Administrator. Redirecting...";
                    // Set session variables after successful login
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['fullname'] = $user['fullname'];
                    $_SESSION['role'] = 'Administrator';
                    // Redirect to admin.php after 4 seconds
                    echo '<meta http-equiv="refresh" content="4;url=admin.php">';
                } else if ($user['role'] == 'User') {
                    $message = "Welcome back, " . $user['fullname'] . "! You are logged in as a User. Redirecting...";
                    // Set session variables after successful login
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['fullname'] = $user['fullname'];
                    $_SESSION['role'] = 'User';
                    // Redirect to index.php after 4 seconds
                    echo '<meta http-equiv="refresh" content="4;url=index.php">';
                } 
                logAction('Login', "Successful login by {$user['email']}");
            } else {
                if ($login_attempts == 3) { //EDITED THIS LINE
                    $ban_time = time() + 300; // Ban for 5 minutes
                    //$sql = "UPDATE users SET ban_time = $ban_time WHERE email='$email'";
                    $sql = "UPDATE users SET ban_time = $ban_time WHERE BINARY email='$email'";
                    mysqli_query($conn, $sql);
                    
                    $message = "Your access has been temporarily disabled for 5 minutes due to multiple failed login attempts. Please try again later.";
                    logAction('Login', "User {$email} temporarily banned due to multiple failed login attempts");
                } else {
                    $message = "Incorrect password. Please try again.";
                    logAction('Login', "Failed login attempt for {$email}");
                }

                 // Store message in session and redirect
                 $_SESSION['message'] = $message;
                 header("Location: " . $_SERVER['PHP_SELF']);
                 exit();
            }
        }
    } else {
        $message = "No user found with this email. Please sign up.";

        // Store message in session and redirect
            $_SESSION['message'] = $message;
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
    }
    
}

// Display message from session if exists and clear it
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Kape-Kada Coffee Shop</title>
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
        .login-container {
            margin-top: 125px;
        }
        .login-form {
            background: #FFFFFF;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .login-title {
            color: #A52A2A; /* Rich brown color for titles */
            font-weight: 700;
            margin-bottom: 30px;
        }
        .form-control {
            margin-bottom: 20px;
        }
        .btn-login {
            background-color: #A52A2A;
            color: #FFFFFF;
        }
        .btn-login:hover {
            background-color: #6B4F4E; /* Darker brown on hover for contrast */
        }
        .login-footer {
            margin-top: 15px;
            text-align: center;
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <?php include 'navbar.php'; ?>
    
    <div class="container login-container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="login-form">
                    <h2 class="login-title">User Login</h2>
                    <!-- Display messages here -->
                    <?php if (!empty($message)) { ?>
                        <div class="alert alert-danger"><?php echo $message; ?></div>
                    <?php } ?>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="form-group">
                            <label>Email address</label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" class="form-control" name="password" required>
                        </div>
                        <button type="submit" class="btn btn-login btn-block">Login</button>
                    </form>
                    <div class="login-footer">
                        Don't have an account? <a href="signup.php">Sign Up</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
