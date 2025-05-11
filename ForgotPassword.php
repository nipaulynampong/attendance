<?php
session_start();
require_once 'config.php';

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['email'])) {
    $email = trim($_POST['email']);
    if (empty($email)) {
        $error = 'Please enter your email address.';
    } else {
        // Check if email exists
        $stmt = $conn->prepare("SELECT id, username, full_name FROM admin WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            $new_password = generateRandomPassword(10);
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            // Update password in DB
            $stmt = $conn->prepare("UPDATE admin SET password = ? WHERE email = ?");
            $stmt->bind_param("ss", $hashed_password, $email);
            if ($stmt->execute()) {
                // Send email
                $sent = sendPasswordEmail($email, $user['full_name'] ?? $user['username'], $new_password);
                if ($sent === true) {
                    $success = 'A new password has been sent to your email address.';
                } else {
                    $error = 'Failed to send email: ' . htmlspecialchars($sent);
                }
            } else {
                $error = 'Failed to update password.';
            }
        } else {
            $success = 'If your email exists in our database, a new password will be sent.';
        }
    }
}

function generateRandomPassword($length = 10) {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_+';
    $password = '';
    for ($i = 0; $i < $length; $i++) {
        $password .= $chars[random_int(0, strlen($chars) - 1)];
    }
    return $password;
}

function sendPasswordEmail($to, $username, $new_password) {
    // Use the downloaded PHPMailer library
    require_once 'PHPMailer-6.8.1/src/Exception.php';
    require_once 'PHPMailer-6.8.1/src/PHPMailer.php';
    require_once 'PHPMailer-6.8.1/src/SMTP.php';
    
    $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
    
    try {
        // Server settings
        $mail->SMTPDebug = 0;                      // Disable debug output
        $mail->isSMTP();                           // Send using SMTP
        $mail->Host       = 'smtp.gmail.com';      // SMTP server
        $mail->SMTPAuth   = true;                  // Enable SMTP authentication
        $mail->Username   = 'wannieolaf71@gmail.com'; // SMTP username
        $mail->Password   = 'frzn vafk pknn xgox'; // SMTP password
        $mail->SMTPSecure = 'tls';                 // Enable TLS encryption
        $mail->Port       = 587;                   // TCP port to connect to
        
        // TLS settings to fix SSL/TLS issues
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
        
        // Recipients
        $mail->setFrom('wannieolaf71@gmail.com', 'Nipaulyn');
        $mail->addAddress($to);                    // Add a recipient
        
        // Content
        $mail->isHTML(true);                       // Set email format to HTML
        $mail->Subject = 'Nipaulyn - Password Reset';
        
        // Get current date and time in Manila/Philippines time zone
        date_default_timezone_set('Asia/Manila');
        $currentDateTime = date('F j, Y, g:i A') . ' (Manila Time)';
        
        $mail->Body    = '<html><body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">'
            . '<div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px;">'
            . '<h2 style="color: #4F6F52; text-align: center;">Password Reset Notification</h2>'
            . '<p>Dear <b>' . htmlspecialchars($username) . '</b>,</p>'
            . '<p>We have received a request to reset your password for your account in the <b>Computerized QR Code Attendance Management System</b>.</p>'
            . '<p>Your new temporary password is: <b style="background-color: #f8f9fa; padding: 5px 10px; border-radius: 3px;">' . htmlspecialchars($new_password) . '</b></p>'
            . '<p><strong>Important Security Information:</strong></p>'
            . '<ul>'
            . '<li>Please log in with this temporary password as soon as possible.</li>'
            . '<li>For security reasons, we strongly recommend changing this password immediately after logging in.</li>'
            . '<li>If you did not request this password reset, please contact your system administrator immediately.</li>'
            . '</ul>'
            . '<p>This password reset was processed on: <em>' . $currentDateTime . '</em></p>'
            . '<p>Thank you,<br><b>Nipaulyn\'s Team</b></p>'
            . '<hr>'
            . '<p style="font-size: 12px; color: #777; text-align: center;">This is an automated message from the Computerized QR Code Attendance Management System. Please do not reply to this email.</p>'
            . '</div>'
            . '</body></html>';
        
        $mail->send();
        return true;
    } catch (\Exception $e) {
        return 'Message could not be sent. Mailer Error: ' . $mail->ErrorInfo;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Barangay Malinta Admin Panel</title>
    <link rel="icon" href="Styles/1.png" type="image/png">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap">
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
        <h1>Forgot Password</h1>
        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        <form method="POST" action="">
            <div class="text">
                <input type="email" name="email" placeholder="Enter your email" required>
            </div>
            <button type="submit" class="btn">Send Reset Link</button>
        </form>
        <div class="back-link">
            <a href="login.php">Back to Login</a>
        </div>
    </div>
</body>
</html>
