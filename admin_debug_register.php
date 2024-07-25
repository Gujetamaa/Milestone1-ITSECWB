<?php
    include 'admin_navbar.php';

    $message =  "";
   
    function logSignupAction($fullname, $wallet, $type = 'INFO', $details = '') {
        $logFile = '/Applications/XAMPP/xamppfiles/htdocs/Milestone1-ITSECWB/logs/debug_register.log';
        $signupTime = date('Y-m-d H:i:s');
        $logMessage = "[$type] | Fullname: $fullname | Wallet: $wallet | Signup Time: $signupTime | Details: $details" . PHP_EOL;
        file_put_contents($logFile, $logMessage, FILE_APPEND);
    }
    function callTracer() {
        global $message;
        // Get the backtrace using debug_backtrace
        $backtrace = debug_backtrace();
    
        // Initialize an array for formatted trace lines
        $formattedTrace = [];
    
        // Iterate through each call in the backtrace
        foreach ($backtrace as $index => $call) {
            // Format the trace line with function name and file location
            $function = $call['function'] ?? 'unknown function';
            $file = $call['file'] ?? 'unknown file';
            $line = $call['line'] ?? 'unknown line';
            
            // Initialize argument string
            $argsString = '';
            if (isset($call['args'])) {
                $argsArray = array_map(function($arg) {
                    if (is_array($arg)) {
                        return 'Array';
                    } else if (is_object($arg)) {
                        return 'Object('.get_class($arg).')';
                    } else if (is_null($arg)) {
                        return 'NULL';
                    } else if (is_bool($arg)) {
                        return $arg ? 'true' : 'false';
                    } else if (is_string($arg)) {
                        return "'$arg'";
                    } else {
                        return $arg;
                    }
                }, $call['args']);
                $argsString = implode(', ', $argsArray);
            }
    
            $formattedTrace[] = "[$index] => #$index $function(<b>$argsString</b>) called at [$file:$line]";
        }
    
        // Append the stack trace to the message
        foreach ($formattedTrace as $line) {
            $message .= "{$line}<br>";
        }
    }
    
    
    
    function isValidEmail($email) {
        // Check for the presence of one "@" symbol
        global $debug;
        global $message;
        if (substr_count($email, '@') !== 1) {
            $message= "<b>ERROR: Missing '@' symbol </b><br>";
            logSignupAction('Unknown', 'N/A', 'ERROR', $message);
                if($debug){
                    callTracer();
                    logSignupAction('Unknown', 'N/A', 'ERROR', "Tracer called");
                }
            return false;
        }
    
        // Split the email into local part and domain
        list($localPart, $domain) = explode('@', $email);
    
        // Check the length of the local part
        if (strlen($localPart) > 64) {
            $message = "<b>ERROR: Invalid Email Local Part Length </b><br>";
            logSignupAction('Unknown', 'N/A', 'ERROR', $message);
            if($debug){
                callTracer();
                logSignupAction('Unknown', 'N/A', 'ERROR', "Tracer called");
            }
            return false;
        }
    
        // Check the length of the domain
        if (strlen($domain) > 255) {
            $message = "<b>ERROR: Invalid Email Domain Length </b><br>";
            logSignupAction('Unknown', 'N/A', 'ERROR', $message);
            if($debug){
                callTracer();
                logSignupAction('Unknown', 'N/A', 'ERROR', "Tracer called");
            }
            return false;
        }
    
        // Validate the local part and domain with a regular expression
        $localPartPattern = '/^[a-zA-Z0-9.!#$%&\'*+\/=?^_`{|}~-]+$/';
        $domainPattern = '/^[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';
    
        if (!preg_match($localPartPattern, $localPart) || !preg_match($domainPattern, $domain)) {
            $message = "<b>ERROR: Invalid Email Format </b><br>";
            logSignupAction('Unknown', 'N/A', 'ERROR', $message);
            if($debug){
                callTracer();
                logSignupAction('Unknown', 'N/A', 'ERROR', "Tracer called");
            }
            return false;
        }
    
        return true;
    }
    
    function isValidPhoneNumber($phoneNumber) {
        // Check if the phone number starts with "+63"
        global $debug;
        global $message;
        if (substr($phoneNumber, 0, 3) === "+63") {
            // Remove "+63" prefix for further validation
            $phoneNumber = substr($phoneNumber, 3);
            
            // Check if the remaining phone number has exactly 10 digits
            if (strlen($phoneNumber) !== 10) {
                $message = "<b>ERROR: Invalid Phone Number Length </b><br>";
                logSignupAction('Unknown', 'N/A', 'ERROR', $message);
                if($debug){
                    callTracer();
                    logSignupAction('Unknown', 'N/A', 'ERROR', "Tracer called");
                }
                return false;
            }
        } else {
            // Check if the phone number starts with "09"
            if (substr($phoneNumber, 0, 2) !== "09") {
                $message = "<b>ERROR: Invalid Phone Number Prefix </b><br>";
                logSignupAction('Unknown', 'N/A', 'ERROR', $message);
                if($debug){
                    callTracer();
                    logSignupAction('Unknown', 'N/A', 'ERROR', "Tracer called");
                }
                return false;
            }
    
            // Check if the phone number has exactly 11 digits
            if (strlen($phoneNumber) !== 11) {
                $message = "<b>ERROR: Invalid Phone Number Length </b><br>";
                logSignupAction('Unknown', 'N/A', 'ERROR', $message);
                if($debug){
                    callTracer();
                    logSignupAction('Unknown', 'N/A', 'ERROR', "Tracer called");
                }
                return false;
            }
        }
    
        // Check if the remaining phone number contains only numeric digits
        if (!ctype_digit($phoneNumber)) {
            $message = "<b>ERROR: Phone Number Contains Non-Numeric Characters </b><br>";
            logSignupAction('Unknown', 'N/A', 'ERROR', $message);
            if($debug){
                callTracer();
                logSignupAction('Unknown', 'N/A', 'ERROR', "Tracer called");
            }
            return false;
        }
    
        return true;
    }

    
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $debug = isset($_POST['debugger']) ? true : false;
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $birthday = $_POST['birthday']; 
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
            $message = "<b>ERROR: File is not an image. </b><br>";
            logSignupAction('Unknown', 'N/A', 'ERROR', $message);
            if($debug){
                callTracer();
                logSignupAction('Unknown', 'N/A', 'ERROR', "Tracer called");
            }
            $uploadOk = 0;
        }
        
        // Check file size (e.g., limit to 5MB)
        if ($_FILES['profile']['size'] > 5000000) {
            $message = "<b>ERROR: Your Image is too large.</b><br>";
            logSignupAction('Unknown', 'N/A', 'ERROR', $message);
            if($debug){
                callTracer();
                logSignupAction('Unknown', 'N/A', 'ERROR', "Tracer called");
            }
            $uploadOk = 0;
        }

        // Allow certain file formats
        $allowedFileTypes = ['jpg', 'jpeg', 'png'];
        if (!in_array($imageFileType, $allowedFileTypes)) {
            $message = "<b>ERROR: Only JPG, JPEG, and PNG files are allowed.</b><br>";
            logSignupAction('Unknown', 'N/A', 'ERROR', $message);
            if($debug){
                callTracer();
                logSignupAction('Unknown', 'N/A', 'ERROR', "Tracer called");
            }
            $uploadOk = 0;
        }
        
        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 1) {
            if (move_uploaded_file($_FILES['profile']['tmp_name'], $targetFile)) {
                $picture = $targetFile; // Set $picture to the path of the uploaded file
            } else {
                $message = "<b>ERROR: Sorry, there was an error uploading your file. </b><br>";
                logSignupAction('Unknown', 'N/A', 'ERROR', $message);
                if($debug){
                    callTracer();
                    logSignupAction('Unknown', 'N/A', 'ERROR', "Tracer called");
                }                                                               
            }
        }
    } else {
        $message = "No file uploaded or upload error.";
        
    }

    
    if(!isValidPhoneNumber($phoneNumber) || !isValidEmail($email)){
      
    }else{
        // Ensures the password is not empty
        if (!empty($password)) {
            if($uploadOk == 1){
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $message = "Welcome, $fullname! Registration successful $debug";
                logSignupAction($fullname, $wallet, 'SUCCESS', "Tracer called");      
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
    <title>(Debug)Sign Up - Kape-Kada Coffee Shop</title>
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
                    <h2 class="signup-title">Sign Up (Debug)</h2>
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

                        <input type="checkbox" id="debugBox" name = "debugger" value="true" >
                        <label for="debugBox">Enable Debug Mode</label>
                    </form>
                      
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