<?php
session_start(); 
    
    // Check if the user is logged in and has the role of Administrator
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Administrator') {
        header("Location: index.php");
        exit();
    }

include 'db_connection.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Kape-Kada Coffee Shop</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Google Font - League Spartan -->
    <link href="https://fonts.googleapis.com/css2?family=League+Spartan&display=swap" rel="stylesheet">
    <style>
        body {
            padding-top: 56px; 
            color: #FFFFFF; 
        }
        .navbar {
            background-color: #A0522D; 
            transition: background-color 0.5s ease; 
        }
        .navbar-nav .nav-link,
        .dropdown-menu .dropdown-item {
            font-family: 'League Spartan', sans-serif;
            font-size: 18px;
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
            border-color: #FFF8DC; 
        }
        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml;charset=utf8,%3Csvg viewBox='0 0 30 30' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath stroke='%23FFF8DC' stroke-width='2' stroke-linecap='round' stroke-miterlimit='10' d='M4 7h22M4 15h22M4 23h22'/%3E%3C/svg%3E");
        }
        .dropdown-menu {
            background-color: #A0522D; 
            border: none; 
            padding: 5px; 
            left: auto !important;
            right: 8px;
        }
        .dropdown-item {
            color: #A52A2A !important;
            transition: color 0.3s ease; 
        }
        .dropdown-item:hover {
            color: blue !important; 
            background-color: transparent !important; 
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg fixed-top">
    <a class="navbar-brand" href="admin.php">
        <img src="logo.png" alt="Admin Panel" style="width: 120px; height: auto;"> Admin Panel
    </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item active">
                <a class="nav-link" href="admin.php">Home <span class="sr-only">(current)</span></a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownManage" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Manage
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdownManage">
                    <a class="dropdown-item" href="admin_menu.php">Menu Management</a>
                    <a class="dropdown-item" href="admin_specials.php">Specials Management</a>
                    <a class="dropdown-item" href="admin_combo.php">Combo Meal Management</a>
                </div>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownReports" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Reports
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdownReports">
                    <a class="dropdown-item" href="admin_report_orders.php">Orders Report</a>
                    <a class="dropdown-item" href="admin_report_combo_meals.php">Combo Meals Report</a>
                    <a class="dropdown-item" href="admin_report_menu_items.php">Menu Items Report</a>
                    <a class="dropdown-item" href="admin_report_specials.php">Specials Report</a>
                    <a class="dropdown-item" href="admin_report_users.php">Users Report</a>
                </div>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownAdministrator" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Administrator
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdownAdministrator">
                    <a class="dropdown-item" href="logout.php">Logout</a>
                </div>
            </li>
        </ul>
    </div>
</nav>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<!-- Bootstrap JavaScript -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<!-- Bootstrap Bundle with Popper.js -->
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
