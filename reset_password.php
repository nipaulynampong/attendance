<?php
session_start();
require_once 'config.php';

$error = '';
$success = '';
$token = '';

// Check if token is provided
if (isset($_GET['token']) && !empty($_GET['token'])) {
    $token = trim($_GET['token']);
    
    // Check if token exists and is valid
    $stmt = $conn->prepare("SELECT email, expires FROM password_reset WHERE token = ? AND expires > NOW()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        $error = 'Invalid or expired password reset token. Please request a new password reset link.';
        $token = '';
    }
} else if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Handle form submission
    if (isset($_POST['token']) && !empty($_POST['token']) && 
        isset($_POST['password']) && !empty($_POST['password']) && 
        isset($_POST['confirm_password']) && !empty($_POST['confirm_password'])) {
        
        $token = trim($_POST['token']);
        $password = trim($_POST['password']);
        $confirm_password = trim($_POST['confirm_password']);
        
        // Validate password
        if (strlen($password) < 8) {
            $error = 'Password must be at least 8 characters long.';
        } else if ($password !== $confirm_password) {
            $error = 'Passwords do not match.';
        } else {
            // Check if token exists and is valid
            $stmt = $conn->prepare("SELECT email FROM password_reset WHERE token = ? AND expires > NOW()");
            $stmt->bind_param("s", $token);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 1) {
                $row = $result->fetch_assoc();
                $email = $row['email'];
                
                // Hash the password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                // Update the user's password
                $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
                $stmt->bind_param("ss", $hashed_password, $email);
                
                if ($stmt->execute()) {
                    // Delete the token
                    $stmt = $conn->prepare("DELETE FROM password_reset WHERE token = ?");
                    $stmt->bind_param("s", $token);
                    $stmt->execute();
                    
                    $success = 'Your password has been reset successfully. You can now <a href="login.php">login</a> with your new password.';
                    $token = '';
                } else {
                    $error = 'Error updating password. Please try again.';
                }
            } else {
                $error = 'Invalid or expired password reset token. Please request a new password reset link.';
                $token = '';
            }
        }
    } else {
        $error = 'Please fill in all fields.';
    }
} else {
    $error = 'Invalid request. Please use the password reset link sent to your email.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-green: #4F6F52;
            --secondary-green: #739072;
            --light-green: #ECE3CE;
            --accent-green: #3A4D39;
            --pale-green: #E8ECEB;
        }
        body {
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, var(--accent-green), var(--primary-green));
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }
        .container {
            width: 400px;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3),
                        0 5px 15px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(10px);
            z-index: 1;
            border: 2px solid rgba(129, 199, 132, 0.3);
        }
        .logo-container {
            text-align: center;
            margin-bottom: 30px;
        }
        .barangay-logo {
            width: 100px;
            height: 100px;
            margin-bottom: 20px;
        }
        h1 {
            text-align: center;
            font-size: 24px;
            color: var(--primary-green);
            margin-bottom: 30px;
            font-weight: 600;
        }
        .text {
            position: relative;
            width: 100%;
            margin-bottom: 20px;
        }
        .text input {
            width: 100%;
            padding: 12px 20px;
            border: 2px solid var(--pale-green);
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        .text input:focus {
            outline: none;
            border-color: var(--primary-green);
            box-shadow: 0 0 10px rgba(79, 111, 82, 0.2);
        }
        .btn {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            background: var(--primary-green);
            color: white;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .btn:hover {
            background: var(--secondary-green);
            transform: translateY(-2px);
        }
        .error, .success {
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }
        .error {
            background: #ffebee;
            color: #c62828;
        }
        .success {
            background: #e8f5e9;
            color: #2e7d32;
        }
        .back-link {
            text-align: center;
            margin-top: 20px;
        }
        .back-link a {
            color: var(--primary-green);
            text-decoration: none;
            font-weight: 500;
        }
        .back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo-container">
            <img src="Styles/1.png" alt="Barangay Malinta Logo" class="barangay-logo">
        </div>
        <h1>Reset Password</h1>
        
        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success"><?php echo $success; ?></div>
        <?php else: ?>
            <?php if ($token): ?>
                <form method="POST" action="">
                    <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                    
                    <div class="text">
                        <input type="password" name="password" placeholder="New Password" required>
                    </div>
                    
                    <div class="text">
                        <input type="password" name="confirm_password" placeholder="Confirm New Password" required>
                    </div>
                    
                    <button type="submit" class="btn">Reset Password</button>
                </form>
            <?php endif; ?>
        <?php endif; ?>
        
        <div class="back-link">
            <a href="login.php">Back to Login</a>
        </div>
    </div>
</body>
</html>
