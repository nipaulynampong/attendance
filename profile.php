<?php
session_start();
require_once 'get_admin_details.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: homepage.php");
    exit();
}

$adminDetails = getAdminDetails($_SESSION['admin_id']);
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_profile'])) {
        $newUsername = trim($_POST['username']);
        $newEmail = trim($_POST['email']);
        $currentPassword = $_POST['current_password'];
        $newPassword = $_POST['new_password'];
        $confirmPassword = $_POST['confirm_password'];

        // Validate current password
        $conn = mysqli_connect("localhost", "root", "", "hris");
        $stmt = $conn->prepare("SELECT password FROM admin WHERE id = ?");
        $stmt->bind_param("i", $_SESSION['admin_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $admin = $result->fetch_assoc();

        if ($currentPassword === $admin['password']) {
            // Update profile information
            if (!empty($newUsername) && !empty($newEmail)) {
                $updateStmt = $conn->prepare("UPDATE admin SET username = ?, email = ? WHERE id = ?");
                $updateStmt->bind_param("ssi", $newUsername, $newEmail, $_SESSION['admin_id']);
                $updateStmt->execute();
            }

            // Update password if provided
            if (!empty($newPassword) && $newPassword === $confirmPassword) {
                $updatePwStmt = $conn->prepare("UPDATE admin SET password = ? WHERE id = ?");
                $updatePwStmt->bind_param("si", $newPassword, $_SESSION['admin_id']);
                $updatePwStmt->execute();
                $message = "Profile updated successfully!";
            } elseif (!empty($newPassword)) {
                $error = "New passwords do not match!";
            } else {
                $message = "Profile updated successfully!";
            }
        } else {
            $error = "Current password is incorrect!";
        }
        $conn->close();
    }
}

// Refresh admin details after update
$adminDetails = getAdminDetails($_SESSION['admin_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Profile</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background-color: #F5F5F0;
            color: #3e3f5b;
            padding: 2rem;
        }

        .profile-container {
            max-width: 800px;
            margin: 0 auto;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.06);
            padding: 2rem;
        }

        .profile-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .profile-header h1 {
            color: #4f6f52;
            font-size: 1.8rem;
            margin-bottom: 0.5rem;
        }

        .profile-avatar {
            width: 120px;
            height: 120px;
            background-color: #4f6f52;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
        }

        .profile-avatar i {
            font-size: 3rem;
            color: white;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #4f6f52;
            font-weight: 500;
        }

        .form-group input {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 0.9rem;
        }

        .form-group input:focus {
            outline: none;
            border-color: #4f6f52;
        }

        .password-section {
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid #eee;
        }

        .btn-update {
            background-color: #4f6f52;
            color: white;
            border: none;
            padding: 0.8rem 2rem;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.9rem;
            transition: background-color 0.3s ease;
        }

        .btn-update:hover {
            background-color: #3e5840;
        }

        .message {
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1rem;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        /* Password field with show/hide icon */
        .password-field {
            position: relative;
            display: flex;
            align-items: center;
        }
        
        .toggle-password {
            position: absolute;
            right: 10px;
            cursor: pointer;
            color: #4f6f52;
            font-size: 1.1rem;
            z-index: 10;
        }
        
        .toggle-password:hover {
            color: #3e5840;
        }
        
        /* Password note styling */
        .password-note {
            background-color: #f8f9fa;
            border-left: 3px solid #4f6f52;
            padding: 10px 15px;
            margin-bottom: 20px;
            font-size: 0.85rem;
            color: #555;
            border-radius: 4px;
            line-height: 1.4;
        }
        
        .password-note i {
            color: #4f6f52;
            margin-right: 5px;
        }
    </style>
</head>
<body>
    <div class="profile-container">
        <div class="profile-header">
            <div class="profile-avatar">
                <i class="fas fa-user"></i>
            </div>
            <h1>Admin Profile</h1>
        </div>

        <?php if (!empty($message)): ?>
            <div class="message success">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="message error">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" value="<?php echo isset($adminDetails['username']) ? htmlspecialchars($adminDetails['username']) : ''; ?>" required>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?php echo isset($adminDetails['email']) ? htmlspecialchars($adminDetails['email']) : ''; ?>" required>
            </div>

            <div class="password-section">
                <h2 style="margin-bottom: 1rem; color: #4f6f52;">Change Password</h2>
                <div class="password-note">
                    <i class="fas fa-info-circle"></i> Password should be at least 8 characters long and include a mix of letters, numbers, and special characters for better security.
                </div>
                
                <div class="form-group">
                    <label for="current_password">Current Password</label>
                    <div class="password-field">
                        <input type="password" id="current_password" name="current_password" required>
                        <i class="toggle-password fas fa-eye" onclick="togglePasswordVisibility('current_password')"></i>
                    </div>
                </div>

                <div class="form-group">
                    <label for="new_password">New Password</label>
                    <div class="password-field">
                        <input type="password" id="new_password" name="new_password">
                        <i class="toggle-password fas fa-eye" onclick="togglePasswordVisibility('new_password')"></i>
                    </div>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm New Password</label>
                    <div class="password-field">
                        <input type="password" id="confirm_password" name="confirm_password">
                        <i class="toggle-password fas fa-eye" onclick="togglePasswordVisibility('confirm_password')"></i>
                    </div>
                </div>
            </div>

            <button type="submit" name="update_profile" class="btn-update">Update Profile</button>
        </form>
    </div>

    <script>
        function togglePasswordVisibility(id) {
            var passwordField = document.getElementById(id);
            var toggleIcon = passwordField.parentNode.querySelector('.toggle-password');

            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordField.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html> 