<?php
session_start();
require_once 'config.php';

function verify_password($input_password, $stored_password) {
    if (password_verify($input_password, $stored_password)) return true;
    if ($input_password === $stored_password) return true;
    if (md5($input_password) === $stored_password) return true;
    if (sha1($input_password) === $stored_password) return true;
    return false;
}

$error = '';
$cooldown_time = 0;
$locked_time = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $stmt = $conn->prepare("SELECT * FROM admin WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
      
        if ($user['locked_until'] && strtotime($user['locked_until']) > time()) {
            $locked_time = strtotime($user['locked_until']) - time();
            $error = 'Account locked. Please try again in ' . ceil($locked_time/3600) . ' hours.';
        } else if ($user['cooldown_until'] && strtotime($user['cooldown_until']) > time()) {
            $cooldown_time = strtotime($user['cooldown_until']) - time();
            $error = 'Please wait ' . $cooldown_time . ' seconds before trying again.';
        } else if (verify_password($password, $user['password'])) {
           
            $stmt = $conn->prepare("UPDATE admin SET login_attempts = 0, last_login = NOW() WHERE id = ?");
            $stmt->bind_param("i", $user['id']);
            $stmt->execute();
            
            date_default_timezone_set('Asia/Manila');
            $login_time = date('F d, Y h:i A');
            $admin_name = $user['full_name'];
            $system_name = "Computerized QR Code Attendance Management System";
            $message = "{$admin_name} just logged in to {$system_name} at {$login_time}";
            
            $notification_sql = "INSERT INTO notifications (message, type, reference_id, is_read, created_at) 
                                VALUES ('" . $conn->real_escape_string($message) . "', 'login', {$user['id']}, 0, NOW())"; 
            $conn->query($notification_sql);
        
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header('Location: AdminPanel.php');
            exit;
        } else {
            // Wrong password
            $new_attempts = $user['login_attempts'] + 1;
            $cooldown_until = null;
            $locked_until = null;
            if ($new_attempts === 4) {
                $cooldown_time = 60;
                $cooldown_until = date('Y-m-d H:i:s', strtotime('+60 seconds'));
                $error = 'Cooldown: Please wait 60 seconds before trying again.';
            } else if ($new_attempts === 7) {
                $cooldown_time = 300;
                $cooldown_until = date('Y-m-d H:i:s', strtotime('+5 minutes'));
                $error = 'Cooldown: Please wait 5 minutes before trying again.';
            } else if ($new_attempts >= 10) {
                $locked_until = date('Y-m-d H:i:s', strtotime('+24 hours'));
                $error = 'Account locked for 24 hours due to too many failed attempts.';
            } else {
                $error = 'Invalid credentials';
            }
            $stmt = $conn->prepare("UPDATE admin SET login_attempts = ?, last_attempt = NOW(), cooldown_until = ?, locked_until = ? WHERE id = ?");
            $stmt->bind_param("issi", $new_attempts, $cooldown_until, $locked_until, $user['id']);
            $stmt->execute();
        }
    } else {
        $error = 'Invalid credentials';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
            overflow: hidden;
        }
        .background-elements {
            position: fixed;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 0;
            transform-style: preserve-3d;
            perspective: 1000px;
        }
        .sun-seal {
            position: absolute;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(129, 199, 132, 0.1) 0%, transparent 70%);
            border-radius: 50%;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            animation: pulseGlow 4s infinite;
        }
        @keyframes pulseGlow {
            0%, 100% { box-shadow: 0 0 20px rgba(46, 125, 50, 0.2); }
            50% { box-shadow: 0 0 40px rgba(46, 125, 50, 0.4); }
        }
        .three-stars {
            position: absolute;
            top: 20%;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 20px;
        }
        .star {
            font-size: 24px;
            color: var(--light-green);
            animation: float 3s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        .star:nth-child(2) { animation-delay: 0.3s; }
        .star:nth-child(3) { animation-delay: 0.6s; }
        .attendance-icons {
            position: absolute;
            width: 100%;
            height: 100%;
        }
        .clock-icon {
            position: absolute;
            color: rgba(200, 230, 201, 0.1);
            animation: clockRotate 20s linear infinite;
            transform-style: preserve-3d;
            transition: transform 0.3s ease;
        }
        @keyframes clockRotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        .container {
            position: relative;
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
            animation: waveAnimation 3s infinite;
        }
        @keyframes waveAnimation {
            0% { transform: translateY(0); }
            50% { transform: translateY(-15px); }
            100% { transform: translateY(0); }
        }
        .center h1 {
            text-align: center;
            font-size: 24px;
            color: var(--primary-green);
            margin-bottom: 30px;
            font-weight: 600;
            position: relative;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .center h1::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 3px;
            background: var(--light-green);
            border-radius: 3px;
        }
        .text {
            position: relative;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
        }
        .text input {
            width: 100%;
            height: 50px;
            padding: 0 15px 0 45px;
            border: 2px solid var(--pale-green);
            border-radius: 10px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: white;
            box-sizing: border-box;
            line-height: 45px;
        }
        .text i.fa-user,
        .text i.fa-lock {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
            transition: all 0.3s ease;
            z-index: 1;
            font-size: 16px;
            height: 16px;
            line-height: 16px;
            display: flex;
            align-items: center;
        }
        .text .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
            cursor: pointer;
            transition: all 0.3s ease;
            z-index: 2;
            font-size: 14px;
            padding: 5px;
        }
        .text label {
            position: absolute;
            top: 50%;
            left: 45px;
            transform: translateY(-50%);
            font-size: 16px;
            color: #666;
            transition: all 0.3s ease;
            pointer-events: none;
            padding: 0 5px;
        }
        .text input:focus ~ .fa-user,
        .text input:focus ~ .fa-lock,
        .text input:focus ~ .password-toggle {
            color: var(--primary-green);
        }
        .text input:focus ~ label,
        .text input:valid ~ label {
            top: 0;
            left: 15px;
            font-size: 14px;
            background: white;
            color: var(--primary-green);
            padding: 0 5px;
        }
        .login-button {
            width: 100%;
            padding: 15px;
            border: none;
            background: linear-gradient(45deg, var(--accent-green), var(--primary-green));
            color: white;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease, transform 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            position: relative;
            overflow: hidden;
            pointer-events: auto;
            z-index: 1;
        }
        .login-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(46, 125, 50, 0.4);
        }
        .login-button:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            pointer-events: none;
        }
        .pass {
            text-align: center;
            margin-top: 20px;
            color: #666;
            cursor: pointer;
            transition: color 0.3s ease;
            font-size: 14px;
        }
        .pass:hover {
            color: var(--primary-green);
        }
        #overlay {
            position: fixed;
            top: 20px;
            right: -300px;
            background: #dc3545;
            color: white;
            padding: 15px 25px;
            border-radius: 10px;
            display: none;
            animation: slideIn 0.3s ease forwards;
            z-index: 1000;
        }
        @keyframes slideIn {
            to { right: 20px; opacity: 1; }
        }
        #cooldownTimer {
            text-align: center;
            color: #dc3545;
            font-size: 14px;
            margin-top: 15px;
            display: none;
        }
        @media (max-width: 500px) {
            .container { width: 95vw; padding: 20px; }
            .logo-container { margin-bottom: 15px; }
        }
    </style>
</head>
<body>
    <div class="background-elements">
        <div class="sun-seal"></div>
        <div class="three-stars">
            <i class="fas fa-star star"></i>
            <i class="fas fa-star star"></i>
            <i class="fas fa-star star"></i>
        </div>
        <div class="attendance-icons">
            <i class="far fa-clock clock-icon" style="top: 20%; left: 20%; font-size: 40px;"></i>
            <i class="far fa-calendar-alt clock-icon" style="top: 70%; left: 80%; font-size: 50px;"></i>
            <i class="fas fa-user-clock clock-icon" style="top: 40%; left: 85%; font-size: 45px;"></i>
            <i class="fas fa-clipboard-check clock-icon" style="top: 80%; left: 15%; font-size: 35px;"></i>
        </div>
    </div>
    <div class="container">
        <div class="logo-container">
            <img src="Styles/1.png" alt="Barangay Logo" class="barangay-logo">
        </div>
        <div class="center">
            <h1>Barangay Malinta<br>Administrator Login</h1>
            <?php if ($error && $cooldown_time > 0): ?>
                <div class="error" id="cooldownBox">
                    Cooldown: Please wait <span id="cooldownSeconds"><?php echo $cooldown_time; ?></span> seconds before trying again.
                </div>
            <?php elseif ($error): ?>
                <div class="error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <form method="POST" autocomplete="off">
                <div class="text">
                    <input type="text" name="username" id="usernameInput" required autofocus>
                    <i class="fas fa-user"></i>
                    <label>Username</label>
                </div>
                <div class="text">
                    <input type="password" name="password" id="passwordInput" required>
                    <i class="fas fa-lock"></i>
                    <i class="fas fa-eye password-toggle" id="passwordToggle" style="font-size: 14px;"></i>
                    <label>Password</label>
                </div>
                <button type="submit" class="login-button">Login</button>
                <div class="pass"><a href="ForgotPassword.php" class="forgot-pass">Forgot Password?</a> Contact the developer</div>
            </form>
        </div>
        <div id="cooldownTimer" style="<?php if (strpos($error, 'wait') !== false) echo 'display:block;'; ?>">
            Cooldown remaining: <span id="cooldownTimerCount"><?php echo $cooldown_time; ?></span> seconds
        </div>
    </div>
    <script>
        // Password toggle functionality
        document.getElementById('passwordToggle').addEventListener('click', function() {
            const passwordInput = document.getElementById('passwordInput');
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
        // Forgot password link
        document.getElementById('forgotPass').addEventListener('click', function() {
            window.location.href = "https://www.facebook.com/pauchi.wani";
        });
        // Cooldown timer countdown
        <?php if ($cooldown_time > 0): ?>
        let cooldown = <?php echo $cooldown_time; ?>;
        const cooldownBox = document.getElementById('cooldownBox');
        const cooldownSeconds = document.getElementById('cooldownSeconds');
        const usernameInput = document.getElementById('usernameInput');
        const passwordInput = document.getElementById('passwordInput');
        const loginButton = document.querySelector('.login-button');
        usernameInput.disabled = true;
        passwordInput.disabled = true;
        loginButton.disabled = true;
        let interval = setInterval(() => {
            cooldown--;
            cooldownSeconds.textContent = cooldown;
            if (cooldown <= 0) {
                clearInterval(interval);
                cooldownBox.style.display = 'none';
                usernameInput.disabled = false;
                passwordInput.disabled = false;
                loginButton.disabled = false;
            }
        }, 1000);
        <?php endif; ?>
    </script>
</body>
</html> 