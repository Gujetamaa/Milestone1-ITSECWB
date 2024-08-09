<?php 
// Start the session
session_start();

// Check if the user is logged in and has the role of Administrator
if (!isset($_SESSION['role'])) {
    // Redirect to login page or display an error message
    header("Location: login.php");
    exit();
}

include 'navbar.php'; 
include 'db_connection.php';

// Function to log actions to user_actions.log
function logAction($action, $details) {
    $logFile = 'logs\user_actions.log';
    $timestamp = date('[Y-m-d H:i:s]');
    $logMessage = "$timestamp [$action] $details" . PHP_EOL;
    file_put_contents($logFile, $logMessage, FILE_APPEND | LOCK_EX);
}

if (isset($_SESSION['email'])) {
    $userEmail = $_SESSION['email'];
    $userQuery = "SELECT fullname, email, birthday, phoneNumber, address, picture FROM users WHERE email = '$userEmail'";
    $userResult = mysqli_query($conn, $userQuery);
    $userData = mysqli_fetch_assoc($userResult);
}

$message = ""; // Variable to store update message

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
    $localPartPattern = '/^[a-zA-Z0-9.!#$%&\'*+\/=?^_{|}~-]+$/';
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
    // Get user input
    $fullName = $_POST['full_name'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $newEmail = $_POST['email'];
    $birthday = $_POST['birthday'];

    // Validate email and phone number
    if (!isValidEmail($userEmail)) {
        if (!isValidPhoneNumber($phone)) {
            $message = "Invalid email and phone number.";
        } else {
            $message = "Invalid email address. Please enter a valid email.";
        }
    } elseif (!isValidPhoneNumber($phone)) {
        $message = "Invalid phone number. Please enter a valid phone number.";
    } else {
        // Initialize upload flag and message
        $uploadOk = 1;
        $message = "";
        $targetDir = "uploads/";
        $targetFile = $targetDir . basename($_FILES["picture"]["name"]);
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        // Handle file upload for profile picture
        if (isset($_FILES['picture']) && $_FILES['picture']['error'] == UPLOAD_ERR_OK) {
            $check = getimagesize($_FILES['picture']['tmp_name']);
            if ($check === false) {
                $message = "File is not an image.";
                $uploadOk = 0;
            }

            // Check file size (limit to 5MB)
            if ($_FILES['picture']['size'] > 5000000) {
                $message = "Your image is too large.";
                $uploadOk = 0;
            }

            // Allow certain file formats
            $allowedFileTypes = ['jpg', 'jpeg', 'png'];
            if (!in_array($imageFileType, $allowedFileTypes)) {
                $message = "Only JPG, JPEG, and PNG files are allowed.";
                $uploadOk = 0;
            }

            // Attempt to upload the file if all checks passed
            if ($uploadOk == 1) {
                if (!move_uploaded_file($_FILES['picture']['tmp_name'], $targetFile)) {
                    $message = "Sorry, there was an error uploading your file.";
                    $uploadOk = 0;
                }
            }
        } else {
            $targetFile = $userData['picture']; // Keep current picture if no new upload
        }

        // Update user information in the database
        if ($uploadOk === 1 || $targetFile === $userData['picture']) {
            $updateQuery = "UPDATE users SET fullname = ?, phoneNumber = ?, birthday = ?, address = ?, picture = ?, email = ? WHERE email = ?";
            $stmt = $conn->prepare($updateQuery);
            $stmt->bind_param("sssssss", $fullName, $phone, $birthday, $address, $targetFile, $newEmail, $userEmail);

            if ($stmt->execute()) {
                // Update session email if it has changed
                if ($newEmail !== $userEmail) {
                    $_SESSION['email'] = $newEmail;
                }

                $message = "Information updated successfully.";
                logAction('Update', "User {$userEmail} updated their information.");
                // Refresh user data after update
                $userData = [
                    'fullname' => $fullName,
                    'email' => $newEmail,
                    'phoneNumber' => $phone,
                    'address' => $address,
                    'picture' => $targetFile,
                    'birthday' => $birthday,
                ];
            } else {
                $message = "Error updating information: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $message = $message ?: "No changes were made due to upload errors.";
        }
    }

    // Handle password update
    if (isset($_POST['update_password'])) {
       
        $userEmail = $_SESSION['email'];
        $currentPassword = trim($_POST['current_password']);
        $newPassword = trim($_POST['new_password']);
        $confirmPassword = trim($_POST['confirm_password']);


        // Verify current password
        $passwordQuery = "SELECT password FROM users WHERE email = ?";
        $stmt = $conn->prepare($passwordQuery);
        $stmt->bind_param("s", $userEmail);
        $stmt->execute();
        $stmt->bind_result($hashedPassword);
        $stmt->fetch();
        $stmt->close();


        if (!password_verify($currentPassword, $hashedPassword)) {
            $message = "Current password is incorrect.";
        } else {
            // Continue with password update
            if ($newPassword !== $confirmPassword) {
                $message = "New passwords do not match.";
            } else {
                $newHashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                /// Update password
                $newHashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                $updatePasswordQuery = "UPDATE users SET password = ? WHERE email = ?";
                $stmt = $conn->prepare($updatePasswordQuery);
                $stmt->bind_param("ss", $newHashedPassword, $userEmail);

                if ($stmt->execute()) {
                    $message = "Password updated successfully.";
                    logAction('Update', "User {$userEmail} updated their password.");
                } else {
                    $message = "Error updating password: " . $stmt->error;
                }
                $stmt->close();
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
    <title>My Account - Kape-Kada Coffee Shop</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.1/css/all.css">
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@400;700&family=Montserrat:wght@300;400;700&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #F5F5DC; 
            color: #6B4F4E; 
            font-family: 'Montserrat', sans-serif;
            background-image: url('bg.svg');
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed; 
            background-position: center;
        }
        .navbar {
            font-family: 'Merriweather', serif;
        }
        .account-container {
            margin-top: 140px;
            background-color: #FFFFFF;
            padding: 30px;
            border-radius: 10px;
        }
        .account-info {
            text-align: center;
            margin-bottom: 30px;
        }
        .profile-picture {
            border-radius: 50%;
            width: 100px;
            height: 100px;
        }
        .btn-update {
            background-color: #A52A2A;
            color: #FFFFFF;
            border: none;
            transition: background-color 0.3s ease;
        }
        .btn-update:hover {
            background-color: #6B4F4E; /* Darker brown on hover for contrast */
        }
        .alert {
            margin-bottom: 20px;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
        }
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>

<div class="container account-container">
    <?php if ($message): ?>
        <div class="alert <?php echo strpos($message, 'Error') !== false ? 'alert-danger' : 'alert-success'; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>
    
    <div class="account-info">
        <h1>Welcome <?php echo $userData['fullname']; ?>!</h1>
        <img src="<?php echo $userData['picture']; ?>" alt="Profile Picture" class="profile-picture">
        <p>Email: <?php echo $userData['email']; ?></p>
        <p>Birthday: <?php echo $userData['birthday']; ?></p>
        <p>Phone: <?php echo $userData['phoneNumber']; ?></p>
        <p>Address: <?php echo $userData['address']; ?></p>
    </div>
    
    <div class="update-form">
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
            <h3>Update Information</h3>
            <div class="form-group">
                <label for="full_name">Full Name</label>
                <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo $userData['fullname']; ?>">
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo $userData['email']; ?>">
            </div>
            <div class="form-group">
                <label for="birthday">Birthday</label>
                <input type="date" class="form-control" id="birthday" name="birthday" value="<?php echo $userData['birthday']; ?>">
            </div>
            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="text" class="form-control" id="phone" name="phone" value="<?php echo $userData['phoneNumber']; ?>">
            </div>
            <div class="form-group">
                <label for="address">Address</label>
                <input type="text" class="form-control" id="address" name="address" value="<?php echo $userData['address']; ?>">
            </div>
            <div class="form-group">
                <label for="picture">Profile Picture</label>
                <input type="file" class="form-control" id="picture" name="picture">
                <small class="form-text text-muted">Current picture: <img src="<?php echo $userData['picture']; ?>" alt="Profile Picture" class="profile-picture" style="width: 50px; height: 50px;"></small>
            </div>
            <button type="submit" class="btn btn-update btn-block" name="update_button">Update Information</button>
        </form>
    </div>

    <div class="update-password">
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <br><br>
            <h3>Update Password</h3>
            <div class="form-group">
                <label for="current_password">Current Password</label>
                <input type="password" class="form-control" id="current_password" name="current_password" required>
            </div>
            <div class="form-group">
                <label for="new_password">New Password</label>
                <input type="password" class="form-control" id="new_password" name="new_password" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm New Password</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
            </div>
            <button type="submit" class="btn btn-update btn-block" name="update_password">Update Password</button>
        </form>
    </div>
</div>

<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>