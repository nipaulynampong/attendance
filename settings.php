<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if user is logged in as admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin.php");
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "hris";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submissions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add_admin':
                if (isset($_POST['username']) && isset($_POST['password']) && isset($_POST['email']) && isset($_POST['full_name'])) {
                    $username = $conn->real_escape_string($_POST['username']);
                    $password = $conn->real_escape_string($_POST['password']);
                    $email = $conn->real_escape_string($_POST['email']);
                    $full_name = $conn->real_escape_string($_POST['full_name']);
                    
                    $sql = "INSERT INTO admin (username, password, email, full_name) VALUES (?, ?, ?, ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("ssss", $username, $password, $email, $full_name);
                    
                    if ($stmt->execute()) {
                        $message = "Admin added successfully";
                    } else {
                        $error = "Error adding admin: " . $conn->error;
                    }
                }
                break;

            case 'update_admin':
                if (isset($_POST['admin_id']) && isset($_POST['username']) && isset($_POST['email']) && isset($_POST['full_name'])) {
                    $admin_id = $conn->real_escape_string($_POST['admin_id']);
                    $username = $conn->real_escape_string($_POST['username']);
                    $email = $conn->real_escape_string($_POST['email']);
                    $full_name = $conn->real_escape_string($_POST['full_name']);
                    
                    // Debug information
                    error_log("Updating admin ID: " . $admin_id);
                    error_log("Username: " . $username);
                    error_log("Email: " . $email);
                    error_log("Full Name: " . $full_name);
                    
                    $sql = "UPDATE admin SET username=?, email=?, full_name=? WHERE id=?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("sssi", $username, $email, $full_name, $admin_id);
                    
                    if ($stmt->execute()) {
                        // Check if any rows were affected
                        if ($stmt->affected_rows > 0) {
                            $message = "Admin updated successfully (" . $stmt->affected_rows . " rows affected)";
                        } else {
                            $message = "No changes were made. The data might be the same or the ID might be incorrect.";
                            error_log("Update executed but no rows affected. Admin ID: " . $admin_id);
                        }
                        
                        if (!empty($_POST['password'])) {
                            $password = $conn->real_escape_string($_POST['password']);
                            $sql = "UPDATE admin SET password=? WHERE id=?";
                            $stmt = $conn->prepare($sql);
                            $stmt->bind_param("si", $password, $admin_id);
                            $stmt->execute();
                            error_log("Password update affected rows: " . $stmt->affected_rows);
                        }
                    } else {
                        $error = "Error updating admin: " . $conn->error;
                        error_log("SQL Error: " . $conn->error);
                    }
                } else {
                    $error = "Missing required fields for admin update";
                    error_log("Missing fields in admin update. POST data: " . print_r($_POST, true));
                }
                break;

            case 'backup':
                $mysqldump_path = "C:\\xampp\\mysql\\bin\\mysqldump.exe";
                if (!file_exists($mysqldump_path)) {
                    $error = "mysqldump not found at: " . $mysqldump_path;
                    break;
                }

                $backup_file = 'backup_' . date("Y-m-d_H-i-s") . '.sql';
                $command = '"' . $mysqldump_path . '" -u ' . escapeshellarg($username);
                if (!empty($password)) {
                    $command .= " -p" . escapeshellarg($password);
                }
                $command .= " -h " . escapeshellarg($servername) . " " . escapeshellarg($dbname) . " > " . escapeshellarg($backup_file) . " 2>&1";
                
                $output = [];
                $return_var = 0;
                exec($command, $output, $return_var);
                
                if ($return_var === 0 && file_exists($backup_file) && filesize($backup_file) > 0) {
                    header('Content-Description: File Transfer');
                    header('Content-Type: application/octet-stream');
                    header('Content-Disposition: attachment; filename="'.basename($backup_file).'"');
                    header('Expires: 0');
                    header('Cache-Control: must-revalidate');
                    header('Pragma: public');
                    header('Content-Length: ' . filesize($backup_file));
                    readfile($backup_file);
                    unlink($backup_file); // Delete the file after download
                    exit;
                } else {
                    $error = "Error creating backup: " . implode("\n", $output);
                    // Log the error for debugging
                    error_log("Backup command failed: " . $command . "\nOutput: " . implode("\n", $output));
                }
                break;

            case 'restore':
                if (isset($_FILES['restore_file'])) {
                    $mysql_path = "C:\\xampp\\mysql\\bin\\mysql.exe";
                    if (!file_exists($mysql_path)) {
                        $error = "mysql client not found at: " . $mysql_path;
                        break;
                    }

                    $restore_file = $_FILES['restore_file']['tmp_name'];
                    
                    // Validate that it's an SQL file
                    $file_info = pathinfo($_FILES['restore_file']['name']);
                    if (strtolower($file_info['extension']) !== 'sql') {
                        $error = "Invalid file type. Please upload a .sql file.";
                        break;
                    }

                    // Validate file size (not empty)
                    if ($_FILES['restore_file']['size'] == 0) {
                        $error = "The uploaded file is empty.";
                        break;
                    }

                    $command = '"' . $mysql_path . '" -u ' . escapeshellarg($username);
                    if (!empty($password)) {
                        $command .= " -p" . escapeshellarg($password);
                    }
                    $command .= " -h " . escapeshellarg($servername) . " " . escapeshellarg($dbname) . " < " . escapeshellarg($restore_file) . " 2>&1";
                    
                    $output = [];
                    $return_var = 0;
                    exec($command, $output, $return_var);

                    if ($return_var === 0) {
                        $message = "Database restored successfully";
                    } else {
                        $error = "Error restoring backup: " . implode("\n", $output);
                        // Log the error for debugging
                        error_log("Restore command failed: " . $command . "\nOutput: " . implode("\n", $output));
                    }
                }
                break;
        }
    }
}

// Get list of admins
$sql = "SELECT id, username, email, full_name, created_at, last_login FROM admin";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Admin Management</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        :root {
            --primary-color: #4F6F52;
            --secondary-color: #739072;
            --background-color: #f5efe6;
            --card-color: #FFFFFF;
        }

        body {
            background-color: var(--background-color);
            font-family: 'Poppins', sans-serif;
        }

        .settings-button {
            height: 200px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            transition: transform 0.2s;
            border-radius: 15px;
            border: none;
            background-color: var(--primary-color);
            color: white;
        }

        .settings-button:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            background-color: var(--secondary-color);
        }

        .settings-button i {
            font-size: 3em;
            margin-bottom: 15px;
        }

        .settings-button h3 {
            margin: 0;
            font-size: 1.5em;
        }

        .content-section {
            display: none;
            margin-top: 20px;
        }

        .show {
            display: block;
        }

        .back-button {
            margin-bottom: 20px;
            background-color: var(--secondary-color);
            border: none;
        }

        .back-button:hover {
            background-color: var(--primary-color);
        }

        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background-color: var(--primary-color) !important;
            color: white !important;
            border-radius: 10px 10px 0 0 !important;
        }

        .table th {
            background-color: var(--primary-color);
            color: white;
        }

        .table tr:hover {
            background-color: rgba(79, 111, 82, 0.05);
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }

        .btn-success {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-success:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }

        .btn-warning {
            background-color: #FFECD6;
            border-color: #FFECD6;
            color: #996600;
        }

        .btn-warning:hover {
            background-color: #FFE0B2;
            border-color: #FFE0B2;
            color: #996600;
        }
        
        /* Toggle password styling */
        .toggle-password {
            cursor: pointer;
            color: #6c757d;
            transition: color 0.2s;
        }
        
        .toggle-password:hover {
            color: #4f6f52;
        }
        
        .email-note {
            color: #666;
            background-color: #f8f9fa;
            border-left: 3px solid #4f6f52;
            padding: 8px 12px;
            margin-top: 5px;
            border-radius: 3px;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <?php if (isset($message)): ?>
            <div class="alert alert-success"><?php echo $message; ?></div>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <!-- Main Buttons Section -->
        <div id="main-buttons">
            <div class="row">
                <div class="col-md-4">
                    <button class="btn btn-primary settings-button w-100" onclick="showSection('add-admin')">
                        <i class="fas fa-user-plus"></i>
                        <h3>Add New Admin</h3>
                    </button>
                </div>
                <div class="col-md-4">
                    <button class="btn btn-info settings-button w-100" onclick="showSection('manage-admin')">
                        <i class="fas fa-users-cog"></i>
                        <h3>Manage Admins</h3>
                    </button>
                </div>
                <div class="col-md-4">
                    <button class="btn btn-success settings-button w-100" onclick="showSection('backup-restore')">
                        <i class="fas fa-database"></i>
                        <h3>Backup & Restore</h3>
                    </button>
                </div>
            </div>
        </div>

        <!-- Add Admin Section -->
        <div id="add-admin-section" class="content-section">
            <button class="btn btn-secondary back-button" onclick="showMainButtons()">
                <i class="fas fa-arrow-left"></i> Back
            </button>
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0"><i class="fas fa-user-plus"></i> Add New Admin</h3>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <input type="hidden" name="action" value="add_admin">
                        <div class="form-group">
                            <label>Username</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" name="password" id="password" class="form-control" required>
                                <div class="input-group-append">
                                    <span class="input-group-text toggle-password" onclick="togglePasswordVisibility('password')">
                                        <i class="fas fa-eye"></i>
                                    </span>
                                </div>
                            </div>
                            <small class="form-text text-muted email-note">
                                <i class="fas fa-info-circle"></i> Create a strong password with at least 8 characters including uppercase, lowercase, numbers, and special characters.
                            </small>
                        </div>
                        <div class="form-group">
                            <label>Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" required>
                            <small class="form-text text-muted email-note">
                                <i class="fas fa-info-circle"></i> Email is required for password recovery.
                            </small>
                        </div>
                        <div class="form-group">
                            <label>Full Name</label>
                            <input type="text" name="full_name" id="add_full_name" class="form-control" pattern="[A-Za-z\s-]+" title="Only letters, spaces, and dashes are allowed" required onkeypress="return /[A-Za-z\s-]/i.test(event.key)">
                            <small class="form-text text-muted validation-message">
                                Only letters, spaces, and dashes are allowed
                            </small>
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg btn-block">Add Admin</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Manage Admins Section -->
        <div id="manage-admin-section" class="content-section">
            <button class="btn btn-secondary back-button" onclick="showMainButtons()">
                <i class="fas fa-arrow-left"></i> Back
            </button>
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h3 class="mb-0"><i class="fas fa-users-cog"></i> Manage Admins</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Full Name</th>
                                    <th>Created At</th>
                                    <th>Last Login</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['username']); ?></td>
                                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                                    <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                                    <td><?php echo $row['created_at']; ?></td>
                                    <td><?php echo $row['last_login']; ?></td>
                                    <td>
                                        <button class="btn btn-primary btn-sm" onclick="editAdmin(<?php echo $row['id']; ?>)">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Backup and Restore Section -->
        <div id="backup-restore-section" class="content-section">
            <button class="btn btn-secondary back-button" onclick="showMainButtons()">
                <i class="fas fa-arrow-left"></i> Back
            </button>
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h3 class="mb-0"><i class="fas fa-database"></i> Database Backup and Restore</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-body text-center">
                                    <h4><i class="fas fa-download mb-3"></i> Backup Database</h4>
                                    <form method="POST">
                                        <input type="hidden" name="action" value="backup">
                                        <button type="submit" class="btn btn-success btn-lg btn-block">
                                            Create Backup
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body text-center">
                                    <h4><i class="fas fa-upload mb-3"></i> Restore Database</h4>
                                    <form method="POST" enctype="multipart/form-data">
                                        <input type="hidden" name="action" value="restore">
                                        <div class="form-group">
                                            <input type="file" name="restore_file" class="form-control-file" accept=".sql" required>
                                            <small class="form-text text-muted">Please select a .sql backup file</small>
                                        </div>
                                        <button type="submit" class="btn btn-warning btn-lg btn-block">
                                            Restore Database
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Admin Modal -->
    <div class="modal fade" id="editAdminModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-user-edit"></i> Edit Admin</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form method="POST" id="editAdminForm">
                        <input type="hidden" name="action" value="update_admin">
                        <input type="hidden" name="admin_id" id="edit_admin_id">
                        <div class="form-group">
                            <label>Username</label>
                            <input type="text" name="username" id="edit_username" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Password (leave blank to keep current)</label>
                            <div class="input-group">
                                <input type="password" name="password" id="edit_password" class="form-control">
                                <div class="input-group-append">
                                    <span class="input-group-text toggle-password" onclick="togglePasswordVisibility('edit_password')">
                                        <i class="fas fa-eye"></i>
                                    </span>
                                </div>
                            </div>
                            <small class="form-text text-muted email-note">
                                <i class="fas fa-info-circle"></i> Create a strong password with at least 8 characters including uppercase, lowercase, numbers, and special characters.
                            </small>
                        </div>
                        <div class="form-group">
                            <label>Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="edit_email" class="form-control" required>
                            <small class="form-text text-muted email-note">
                                <i class="fas fa-info-circle"></i> Email is required for password recovery.
                            </small>
                        </div>
                        <div class="form-group">
                            <label>Full Name</label>
                            <input type="text" name="full_name" id="edit_full_name" class="form-control" pattern="[A-Za-z\s-]+" title="Only letters, spaces, and dashes are allowed" required onkeypress="return /[A-Za-z\s-]/i.test(event.key)">
                            <small class="form-text text-muted validation-message">
                                Only letters, spaces, and dashes are allowed
                            </small>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Update Admin</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
    function showSection(sectionId) {
        document.getElementById('main-buttons').style.display = 'none';
        document.querySelectorAll('.content-section').forEach(section => {
            section.classList.remove('show');
        });
        document.getElementById(sectionId + '-section').classList.add('show');
    }

    function showMainButtons() {
        document.getElementById('main-buttons').style.display = 'block';
        document.querySelectorAll('.content-section').forEach(section => {
            section.classList.remove('show');
        });
    }

    function editAdmin(id) {
        $.get('get_admin_details.php?id=' + id, function(data) {
            $('#edit_admin_id').val(data.id);
            $('#edit_username').val(data.username);
            $('#edit_email').val(data.email);
            $('#edit_full_name').val(data.full_name);
            $('#editAdminModal').modal('show');
        });
    }
    
    function togglePasswordVisibility(inputId) {
        const passwordInput = document.getElementById(inputId);
        const icon = document.querySelector(`[onclick="togglePasswordVisibility('${inputId}')"] i`);
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
    
    // Function to validate full name input in real-time
    function validateFullName(input) {
        const namePattern = /^[A-Za-z\s-]*$/;
        const validationMessage = input.nextElementSibling;
        
        if (!namePattern.test(input.value)) {
            // Invalid character detected
            validationMessage.innerHTML = '<i class="fas fa-exclamation-circle text-danger"></i> Numbers and special characters are not allowed';
            validationMessage.classList.add('text-danger');
            validationMessage.classList.remove('text-muted');
            
            // Force the value to only contain valid characters
            input.value = input.value.replace(/[^A-Za-z\s-]/g, '');
        } else if (input.value.length > 0) {
            // Valid input
            validationMessage.innerHTML = '<i class="fas fa-check-circle text-success"></i> Valid name format';
            validationMessage.classList.remove('text-danger', 'text-muted');
            validationMessage.classList.add('text-success');
        } else {
            // Empty input
            validationMessage.innerHTML = '<i class="fas fa-info-circle"></i> Only letters, spaces, and dashes are allowed';
            validationMessage.classList.remove('text-danger', 'text-success');
            validationMessage.classList.add('text-muted');
        }
    }
    
    // Add event listeners when the document is ready
    $(document).ready(function() {
        // Add event listeners to full name inputs
        $('#add_full_name, #edit_full_name').on('input', function() {
            validateFullName(this);
        });
        
        // Additional layer of protection - prevent pasting invalid characters
        $('#add_full_name, #edit_full_name').on('paste', function(e) {
            // Get pasted data
            let pastedData = (e.originalEvent.clipboardData || window.clipboardData).getData('text');
            // Replace any invalid characters
            pastedData = pastedData.replace(/[^A-Za-z\s-]/g, '');
            
            // Cancel the paste event
            e.preventDefault();
            // Insert the cleaned data
            document.execCommand('insertText', false, pastedData);
        });
    });
    </script>
</body>
</html>