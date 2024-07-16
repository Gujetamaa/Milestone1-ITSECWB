<?php
session_start();

include 'db_connection.php';

$userData = null;

if (isset($_SESSION['email'])) {
    $userEmail = $_SESSION['email'];
    $userQuery = "SELECT fullname, picture FROM users WHERE email = ?";
    $stmt = $conn->prepare($userQuery);
    $stmt->bind_param("s", $userEmail);
    $stmt->execute();
    $result = $stmt->get_result();
    $userData = $result->fetch_assoc();
    $stmt->close();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kape-Kada Coffee Shop</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Google Font - Playfair Display -->
    <link href="https://fonts.googleapis.com/css2?family=League+Spartan&display=swap" rel="stylesheet">
    <style>
        body {
            padding-top: 46px; 
            color: #FFFFFF; 
        }
        .navbar {
            background-color: #A0522D; 
            transition: background-color 0.5s ease; 
        }
        .navbar-brand {
            color: #FFFFFF; 
            font-family: 'League Spartan', sans-serif;
            font-weight: bold; 
            transition: transform 0.5s ease, color 0.3s ease; 
        }
        .navbar-brand img {
            transition: transform 0.5s ease; 
        }
        .navbar-brand:hover img {
            transform: scale(1.1); 
        }
        .navbar-brand:hover {
            color: #000000; 
        }
        .nav-link {
            color: #FFFFFF !important; 
            transition: color 0.3s ease; 
        }
        .nav-link:hover {
            color: #FFDEAD !important; 
        }
        .navbar-toggler {
            border-color: #FFF8DC; /
        }
        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml;charset=utf8,%3Csvg viewBox='0 0 30 30' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath stroke='%23FFF8DC' stroke-width='2' stroke-linecap='round' stroke-miterlimit='10' d='M4 7h22M4 15h22M4 23h22'/%3E%3C/svg%3E");
        }
        .dropdown-menu {
            background-color: #A0522D; 
            border: none; 
            padding: 5px; 
        }
        .dropdown-menu-login {
            left: auto !important;
            right: 0;
        }
        .dropdown-item {
            color: #A52A2A !important;
            transition: color 0.3s ease; 
        }
        .dropdown-item:hover {
            color: blue !important; 
            background-color: transparent !important; 
        }
        .nav-link {
            font-family: 'League Spartan', sans-serif; 
            color: #FFFFFF !important; 
            transition: color 0.3s ease; 
            font-size: 18px;
        }
        .nav-link,
        .dropdown-item {
            font-family: 'League Spartan', sans-serif; 
            font-size: 18px; 
            color: solid black !important; 
            transition: color 0.3s ease; 
        }
        
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg fixed-top">
    <a class="navbar-brand" href="index.php">
        <img src="logo.png" alt="Kape-Kada Coffee Shop" style="width: 120px; height: auto;"> Kape-Kada Coffee Shop
    </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item active">
                <a class="nav-link" href="index.php">Home <span class="sr-only">(current)</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="menu.php">Menu</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="combos.php">Combo Deals</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="cart.php">Cart</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="checkout.php">Checkout</a>
            </li>
            <?php if ($userData): ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <?php if ($userData['picture']): ?>
                            <img src="<?php echo $userData['picture']; ?>" alt="Profile Picture" style="width: 35px; height: 35px; border-radius: 50%;">
                        <?php endif; ?>
                        <?php echo $userData['fullname']; ?>
                    </a>
                    <div class="dropdown-menu dropdown-menu-login" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="myaccount.php">My Account</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="wallet.php">Wallet</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="logout.php">Logout</a>
                    </div>
                </li>
            <?php else : ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Login
                    </a>
                    <div class="dropdown-menu dropdown-menu-login" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="login.php">Login</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="signup.php">Sign Up</a>
                    </div>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</nav>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<!-- Bootstrap JavaScript -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<!-- Bootstrap Bundle with Popper.js -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
