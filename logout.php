<?php
session_start();

// Function to log user actions
function logAction($email, $action) {
    $logFile = 'C:/xampp/htdocs/Milestone1-ITSECWB/logs/logout_actions.log';
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[{$timestamp}] [User Email: {$email}] Action: {$action}\n";
    file_put_contents($logFile, $logMessage, FILE_APPEND);
}

if (isset($_SESSION['email'])) {
    $userEmail = $_SESSION['email'];
    logAction($userEmail, 'Logged Out');
}

$_SESSION = [];

// Destroy the session
session_unset();
session_destroy();

header("Location: index.php");
exit();
?>
