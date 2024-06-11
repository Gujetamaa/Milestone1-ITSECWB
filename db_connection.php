<?php
// Database connection parameters
$db_host = "localhost";             // Host name
$db_user = "root";                  // Database username
$db_pass = "";            // Database password
$db_name = "kapekada";              // Database name

// Establishing connection to the database
$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error()  );
}
?>
