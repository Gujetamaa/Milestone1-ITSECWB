<?php 
// Start the session
session_start();

// Include database connection
include 'db_connection.php';

$message = ""; // Initialize the message variable

// Function to check if the user is banned
function is_banned($email) {
    global $conn;
    $sql = "SELECT ban_time FROM users WHERE email='$email'";
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
    $sql = "SELECT ban_time FROM users WHERE email='$email'";
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
        $sql = "UPDATE users SET login_attempts = 0, ban_time = NULL WHERE email='$email'";
        mysqli_query($conn, $sql);
    }

    // Query to fetch user data based on email
    $sql = "SELECT * FROM users WHERE email='$email'";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        
        // Increment login attempts
        $login_attempts = $user['login_attempts'] + 1;
 

        // Update login attempts
        $sql = "UPDATE users SET login_attempts = $login_attempts WHERE email='$email'";
        mysqli_query($conn, $sql);

        if (is_banned($email)) {
            if ($login_attempts == 3) {
                $message = "Your access has been temporarily disabled for 5 minutes due to multiple failed login attempts. Please try again later.";
            } elseif ($login_attempts >= 4){
                $message = "Access denied. Please try again later.";
            }
        } else {
            // Verify hashed password
            if (password_verify($password, $user['password'])) {
                // Reset login attempts and ban time on successful login
                $sql = "UPDATE users SET login_attempts = 0, ban_time = NULL WHERE email='$email'";
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
                    echo '<meta http-equiv="refresh" content="4;url=user.php">';
                } else {
                    $message = "Unknown user role. Please contact the administrator.";
                }
            } else {
                if ($login_attempts >= 3) {
                    $ban_time = time() + 300; // Ban for 5 minutes
                    $sql = "UPDATE users SET ban_time = $ban_time WHERE email='$email'";
                    mysqli_query($conn, $sql);
                    
                    $message = "Your access has been temporarily disabled for 5 minutes due to multiple failed login attempts. Please try again later.";
                } else {
                    $message = "Incorrect password. Please try again.";
                }
            }
        }
    } else {
        $message = "No user found with this email. Please sign up.";
    }
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
        .signup-link {
            text-align: center;
            margin-top: 20px;
        }
        .signup-link a {
            color: #A52A2A;
            text-decoration: none;
        }
        .signup-link a:hover {
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

<div class="container login-container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <form class="login-form" method="post" action="index.php">
                <h2 class="login-title">Login</h2>
                <div class="form-group">
                    <input type="email" class="form-control" id="email" name="email" placeholder="Email"> <!-- Add name attribute -->
                </div>
                <div class="form-group">
                    <input type="password" class="form-control" id="password" name="password" placeholder="Password"> <!-- Add name attribute -->
                </div>
                <button type="submit" class="btn btn-block btn-login">Login</button>
                <div class="signup-link">
                    <a href="signup.php">Don't have an account? Sign Up</a>
                </div>
            </form>
        </div>
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

