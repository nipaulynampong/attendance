<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'get_admin_details.php';
require_once 'notifications.php'; 
$adminDetails = getAdminDetails($_SESSION['admin_id']);
$notificationCount = getUnreadNotificationsCount();
$recentNotifications = getRecentNotifications(5);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barangay Malinta - Attendance Management System</title>
    <link rel="icon" href="Styles/1.png" type="image/png">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary: #4f6f52;
            --primary-dark: #3e5840;
            --primary-light: #607d63;
            --secondary: #9cafaa;
            --text-dark: #3e3f5b;
            --text-light: #f5efe6;
            --background: #F5F5F0;
            --shadow-sm: 0 2px 5px rgba(0, 0, 0, 0.06);
            --shadow-md: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background-color: var(--background);
            color: var(--text-dark);
            min-height: 100vh;
        }

        .topnav {
            background-color: var(--primary);
            padding: 1rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: var(--shadow-md);
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            height: 70px;
        }

        .nav-brand {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            color: var(--text-light);
            text-decoration: none;
            padding: 0 1rem;
            border-right: 1px solid rgba(255, 255, 255, 0.1);
        }

        .nav-brand img {
            height: 32px;
            width: auto;
        }

        .nav-brand h1 {
            font-size: 1rem;
            font-weight: 500;
            white-space: nowrap;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 0;
            height: 100%;
        }

        .nav-link {
            color: var(--text-light);
            text-decoration: none;
            padding: 0 1.2rem;
            transition: all 0.3s ease;
            font-weight: 400;
            font-size: 0.9rem;
            height: 70px;
            display: flex;
            align-items: center;
            position: relative;
        }

        .nav-link.has-dropdown {
            padding-right: 2rem;
            cursor: pointer;
            position: relative;
            display: inline-block;
        }

        .nav-link.has-dropdown::before {
            content: '\f107';
            font-family: 'Font Awesome 5 Free';
            font-weight: 900;
            position: absolute;
            right: 0.8rem;
            top: 50%;
            transform: translateY(-50%);
            transition: transform 0.3s ease;
        }

        .nav-link.has-dropdown:hover::before {
            transform: translateY(-50%) rotate(180deg);
        }

        .nav-dropdown {
            position: absolute;
            top: 70px;
            left: 0;
            background-color: #ffffff;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            min-width: 220px;
            display: none;
            z-index: 1001;
            padding: 8px;
        }

        .nav-dropdown.show {
            display: block;
        }

        .nav-dropdown-item {
            display: flex;
            align-items: center;
            padding: 12px 16px;
            color: var(--text-dark);
            text-decoration: none;
            transition: all 0.3s ease;
            font-size: 0.9rem;
            border-radius: 6px;
            margin: 4px 0;
            background-color: transparent;
        }

        .nav-dropdown-item i {
            margin-right: 12px;
            width: 20px;
            text-align: center;
            color: var(--primary);
            font-size: 1rem;
        }

        .nav-dropdown-item:hover {
            background-color: var(--primary);
            color: white;
            transform: translateX(5px);
        }

        .nav-dropdown-item:hover i {
            color: white;
        }

        .nav-dropdown::before {
            content: '';
            position: absolute;
            top: -6px;
            left: 20px;
            width: 12px;
            height: 12px;
            background-color: #ffffff;
            border-left: 1px solid #e0e0e0;
            border-top: 1px solid #e0e0e0;
            transform: rotate(45deg);
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: 15px;
            left: 50%;
            width: 0;
            height: 2px;
            background-color: var(--text-light);
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }

        .nav-link:hover::after, 
        .nav-link.active::after {
            width: calc(100% - 2.4rem);
        }

        .nav-link:hover, .nav-link.active {
            background-color: transparent;
        }

        .nav-right {
            display: flex;
            align-items: center;
            gap: 0;
            height: 100%;
            margin-left: -3rem;
        }

        .notification-icon {
            position: relative;
            color: var(--text-light);
            cursor: pointer;
            padding: 0 0.6rem;
            height: 70px;
            display: flex;
            align-items: center;
            gap: 0.3rem;
            border-right: 1px solid rgba(255, 255, 255, 0.1);
        }

        .notification-icon i {
            font-size: 0.9rem;
        }

        .notification-badge {
            position: absolute;
            top: 18px;
            right: 8px;
            background-color: #ff4444;
            color: white;
            border-radius: 50%;
            padding: 1px 4px;
            font-size: 0.65rem;
        }

        .notification-dropdown {
            position: absolute;
            top: 100%;
            right: 0;
            background-color: white;
            border-radius: 4px;
            box-shadow: var(--shadow-md);
            min-width: 300px;
            display: none;
            z-index: 1001;
            max-height: 400px;
            overflow-y: auto;
        }

        .notification-icon:hover .notification-dropdown {
            display: block;
        }

        .notification-header {
            padding: 1rem;
            border-bottom: 1px solid #eee;
            font-weight: 500;
            color: var(--text-dark);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .mark-all-read {
            background: none;
            border: none;
            color: var(--primary);
            font-size: 0.75rem;
            cursor: pointer;
            padding: 4px 8px;
            border-radius: 4px;
            transition: all 0.2s;
        }
        
        .mark-all-read:hover {
            background-color: rgba(79, 111, 82, 0.1);
        }

        .notification-item {
            padding: 0.8rem 1rem;
            border-bottom: 1px solid #eee;
            display: flex;
            align-items: flex-start;
            gap: 0.8rem;
            transition: all 0.3s ease;
            position: relative;
        }

        .notification-item:hover {
            background-color: var(--background);
        }
        
        .notification-item.unread {
            background-color: rgba(79, 111, 82, 0.05);
        }
        
        .notification-item.unread .notification-title {
            font-weight: 500;
        }
        
        .mark-read {
            background: none;
            border: none;
            color: var(--primary);
            font-size: 0.75rem;
            cursor: pointer;
            position: absolute;
            right: 8px;
            top: 8px;
            padding: 4px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: all 0.2s;
        }
        
        .notification-item:hover .mark-read {
            opacity: 1;
        }
        
        .mark-read:hover {
            background-color: rgba(79, 111, 82, 0.1);
        }

        .notification-item i {
            color: var(--primary);
            margin-top: 0.2rem;
        }

        .notification-content {
            flex: 1;
        }

        .notification-title {
            font-size: 0.9rem;
            color: var(--text-dark);
            margin-bottom: 0.2rem;
        }

        .notification-time {
            font-size: 0.75rem;
            color: #666;
        }

        .no-notifications {
            padding: 1rem;
            text-align: center;
            color: #666;
            font-size: 0.9rem;
        }
        
        .notification-footer {
            padding: 0.8rem 1rem;
            text-align: center;
            border-top: 1px solid #eee;
        }
        
        .notification-footer a {
            color: var(--primary);
            font-size: 0.8rem;
            text-decoration: none;
            transition: all 0.2s;
        }
        
        .notification-footer a:hover {
            text-decoration: underline;
        }

        .admin-dropdown {
            height: 100%;
            position: relative;
            display: flex;
            align-items: center;
        }

        .admin-info {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            color: var(--text-light);
            padding: 0 0.6rem;
            height: 50px;
            transition: all 0.3s ease;
            font-size: 0.9rem;
            cursor: pointer;
            position: relative;
            margin-top: -2px;
        }

        .admin-info .fa-chevron-down {
            position: relative;
            top: 1px;
            font-size: 0.75rem;
            margin-left: 2px;
        }

        .admin-info i {
            font-size: 0.9rem;
        }

        .admin-info:hover {
            background-color: transparent;
        }

        .dropdown-menu {
            position: absolute;
            top: 120%;
            right: 0;
            background-color: white;
            border-radius: 4px;
            box-shadow: var(--shadow-md);
            min-width: 180px;
            display: none;
            z-index: 1001;
        }

        .dropdown-menu.show {
            display: block;
        }

        .admin-dropdown:hover .dropdown-menu {
            display: block;
        }

        .dropdown-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.6rem 1rem;
            color: var(--text-dark);
            text-decoration: none;
            transition: all 0.3s ease;
            font-size: 0.9rem;
        }

        .dropdown-item:hover {
            background-color: var(--background);
        }

        .content-container {
            margin-top: 70px;
            padding: 1rem;
            height: calc(100vh - 70px);
        }

        iframe {
            width: 100%;
            height: 100%;
            border: none;
            border-radius: 8px;
            box-shadow: var(--shadow-sm);
            background-color: #f5efe6; /* Match the department.php background color */
        }

        @media (max-width: 768px) {
            .nav-brand h1 {
                display: none;
            }

            .nav-links {
                gap: 0.8rem;
            }

            .nav-link {
                padding: 0.4rem 0.8rem;
                font-size: 0.9rem;
            }
        }

        /* Modify the dropdown styles */
        .dropdown-content {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            background-color: #ffffff;
            min-width: 200px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            border-radius: 8px;
            padding: 8px;
            z-index: 1001;
            margin-top: 1px;
        }

        .nav-link.dropdown:hover .dropdown-content {
            display: block;
        }

        .dropdown-item {
            color: var(--text-dark);
            padding: 12px 16px;
            text-decoration: none;
            display: flex;
            align-items: center;
            transition: all 0.3s ease;
            border-radius: 4px;
            font-size: 0.9rem;
        }

        .dropdown-item:hover {
            background-color: var(--primary);
            color: white;
        }

        .dropdown-item i {
            margin-right: 12px;
            width: 20px;
            text-align: center;
            font-size: 0.9rem;
        }

        /* Add arrow indicator */
        .dropdown-content::before {
            content: '';
            position: absolute;
            top: -8px;
            left: 20px;
            border-left: 8px solid transparent;
            border-right: 8px solid transparent;
            border-bottom: 8px solid #ffffff;
        }
    </style>
</head>
<body>
    <nav class="topnav">
        <a href="#" class="nav-brand">
            <img src="Styles/1.png" alt="Barangay Malinta Logo">
            <h1>Barangay Malinta</h1>
        </a>
        
        <div class="nav-links">
            <a href="#" class="nav-link active" data-content="dashboardd.php">Dashboard</a>
            <div class="nav-link dropdown">
                <span>Employees <i class="fas fa-chevron-down" style="margin-left: 5px;"></i></span>
                <div class="dropdown-content">
                    <a href="#" class="dropdown-item" data-content="employee.php">
                        <i class="fas fa-users"></i>
                        View Employees
                    </a>
                    <a href="#" class="dropdown-item" data-content="addemployees.php">
                        <i class="fas fa-user-plus"></i>
                        Add New Employee
                    </a>
                    <a href="#" class="dropdown-item" data-content="archive_employee.php">
                        <i class="fas fa-archive"></i>
                        Archived Employees
                    </a>
                </div>
            </div>
            <a href="#" class="nav-link" data-content="department.php">Departments</a>
            <a href="#" class="nav-link" data-content="attendance_view.php">Attendance</a>
            <a href="#" class="nav-link" data-content="events.php">Events</a>
            <a href="#" class="nav-link" data-content="holidays.php">Holidays</a>
            <div class="nav-link dropdown">
                <span>Reports <i class="fas fa-chevron-down" style="margin-left: 5px;"></i></span>
                <div class="dropdown-content">
                    <a href="#" class="dropdown-item" data-content="reportemployee.php">
                        <i class="fas fa-file-alt"></i>
                        Employee Reports
                    </a>
                    <a href="#" class="dropdown-item" data-content="attendancereport.php">
                        <i class="fas fa-user-clock"></i>
                        Individual Reports
                    </a>
                </div>
            </div>
        </div>

        <div class="nav-right">
            <div class="notification-icon">
                <i class="fas fa-bell"></i>
                <span class="notification-badge" id="notification-count"><?php echo $notificationCount; ?></span>
                <div class="notification-dropdown">
                    <div class="notification-header">
                        <span>Notifications</span>
                        <?php if ($notificationCount > 0): ?>
                        <button id="mark-all-read" class="mark-all-read">Mark all as read</button>
                        <?php endif; ?>
                    </div>
                    
                    <?php if (empty($recentNotifications)): ?>
                    <div class="no-notifications">
                        No new notifications
                    </div>
                    <?php else: ?>
                        <?php foreach ($recentNotifications as $notification): ?>
                        <div class="notification-item <?php echo $notification['is_read'] ? 'read' : 'unread'; ?>" data-id="<?php echo $notification['id']; ?>">
                            <?php 
                            // Set icon based on notification type
                            $icon = 'info-circle';
                            switch ($notification['type']) {
                                case 'login':
                                    $icon = 'sign-in-alt';
                                    break;
                                case 'employee':
                                    $icon = 'user-plus';
                                    break;
                                case 'attendance':
                                    $icon = 'clock';
                                    break;
                                case 'admin':
                                    $icon = 'user-shield';
                                    break;
                            }
                            ?>
                            <i class="fas fa-<?php echo $icon; ?>"></i>
                            <div class="notification-content">
                                <div class="notification-title"><?php echo htmlspecialchars($notification['message']); ?></div>
                                <div class="notification-time"><?php echo date('M d, g:i a', strtotime($notification['created_at'])); ?></div>
                            </div>
                            <?php if (!$notification['is_read']): ?>
                            <button class="mark-read" data-id="<?php echo $notification['id']; ?>">
                                <i class="fas fa-check"></i>
                            </button>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    
                    <div class="notification-footer">
                        <a href="all_notifications.php" id="view-all-notifications">View All Notifications</a>
                    </div>
                </div>
            </div>
            
            <div class="admin-dropdown">
                <div class="admin-info">
                    <i class="fas fa-user-circle"></i>
                    <span><?php echo htmlspecialchars($adminDetails['full_name']); ?></span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="dropdown-menu">
                    <a href="#" class="dropdown-item" data-content="profile.php">
                        <i class="fas fa-user"></i>
                        Profile
                    </a>
                    <a href="#" class="dropdown-item" data-content="settings.php">
                        <i class="fas fa-cog"></i>
                        Settings
                    </a>
                    <hr style="margin: 0.5rem 0; border-color: #eee;">
                    <a href="#" class="dropdown-item" id="logout-link">
                        <i class="fas fa-sign-out-alt mr-2"></i>
                        Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="content-container">
        <iframe id="content-frame" src="dashboardd.php"></iframe>
    </div>

    <script>
        // Handle navigation including dropdowns
        document.addEventListener('DOMContentLoaded', function() {
            // Handle all navigation links including dropdown items
            document.querySelectorAll('.nav-link:not(.dropdown), .dropdown-item').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    // Update active state
                    document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
                    
                    if (this.classList.contains('dropdown-item')) {
                        this.closest('.dropdown').classList.add('active');
                    } else {
                        this.classList.add('active');
                    }
                    
                    // Load content
                    const contentFrame = document.getElementById('content-frame');
                    contentFrame.src = this.getAttribute('data-content');
                });
            });

            // Handle admin dropdown
            const adminDropdown = document.querySelector('.admin-dropdown');
            const adminDropdownMenu = adminDropdown.querySelector('.dropdown-menu');
            
            adminDropdown.querySelector('.admin-info').addEventListener('click', function(e) {
                e.stopPropagation();
                adminDropdownMenu.classList.toggle('show');
            });

            document.addEventListener('click', function(e) {
                if (!adminDropdown.contains(e.target)) {
                    adminDropdownMenu.classList.remove('show');
                }
            });
        });

        // Handle iframe resizing
        window.addEventListener('message', function(e) {
            if (e.data && e.data.type === 'iframe-resize') {
                const iframe = document.getElementById('content-frame');
                iframe.style.height = e.data.height + 'px';
            }
        });

        // Notification handling
        document.addEventListener('DOMContentLoaded', function() {
            // Add click handlers for admin dropdown items
            document.querySelector('.dropdown-item[data-content="profile.php"]').addEventListener('click', function(e) {
                e.preventDefault();
                document.getElementById('content-frame').src = 'profile.php';
            });
            
            document.querySelector('.dropdown-item[data-content="settings.php"]').addEventListener('click', function(e) {
                e.preventDefault();
                document.getElementById('content-frame').src = 'settings.php';
            });
            
            // Add logout functionality
            document.getElementById('logout-link').addEventListener('click', function(e) {
                e.preventDefault();
                if (confirm('Are you sure you want to logout?')) {
                    window.location.href = 'logout.php';
                }
            });
            
            // Mark individual notification as read
            document.querySelectorAll('.mark-read').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const id = this.getAttribute('data-id');
                    markNotificationRead(id);
                });
            });
            
            // Mark all notifications as read
            document.getElementById('mark-all-read')?.addEventListener('click', function(e) {
                e.stopPropagation();
                markAllNotificationsRead();
            });
            
            // Periodically check for new notifications every 30 seconds
            setInterval(checkNewNotifications, 30000);
        });
        
        function markNotificationRead(id) {
            fetch('notifications.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=mark_read&id=${id}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update UI to mark notification as read
                    const notification = document.querySelector(`.notification-item[data-id="${id}"]`);
                    notification.classList.add('read');
                    notification.classList.remove('unread');
                    const markButton = notification.querySelector('.mark-read');
                    if (markButton) markButton.remove();
                    
                    // Update notification count
                    updateNotificationCount();
                }
            })
            .catch(error => console.error('Error marking notification as read:', error));
        }
        
        function markAllNotificationsRead() {
            fetch('notifications.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=mark_all_read'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update UI to mark all notifications as read
                    document.querySelectorAll('.notification-item.unread').forEach(notification => {
                        notification.classList.add('read');
                        notification.classList.remove('unread');
                        const markButton = notification.querySelector('.mark-read');
                        if (markButton) markButton.remove();
                    });
                    
                    // Update notification count
                    document.getElementById('notification-count').textContent = '0';
                    document.getElementById('mark-all-read').style.display = 'none';
                }
            })
            .catch(error => console.error('Error marking all notifications as read:', error));
        }
        
        function updateNotificationCount() {
            fetch('notifications.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=get_count'
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById('notification-count').textContent = data.count;
                
                // Hide mark all as read button if no unread notifications
                if (data.count === 0) {
                    const markAllButton = document.getElementById('mark-all-read');
                    if (markAllButton) markAllButton.style.display = 'none';
                }
            })
            .catch(error => console.error('Error getting notification count:', error));
        }
        
        function checkNewNotifications() {
            fetch('notifications.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=get_notifications'
            })
            .then(response => response.json())
            .then(data => {
                updateNotificationCount();
                
                // Check if we need to refresh the notification list
                const notificationDropdown = document.querySelector('.notification-dropdown');
                const lastNotification = data.notifications[0]; // Get most recent notification
                const currentFirstId = notificationDropdown.querySelector('.notification-item')?.dataset.id;
                
                if (lastNotification && (!currentFirstId || lastNotification.id > parseInt(currentFirstId))) {
                    // We have newer notifications, refresh the page to show them
                    // Alternatively, you could dynamically update the notification list here
                    location.reload();
                }
            })
            .catch(error => console.error('Error checking for new notifications:', error));
        }
    </script>
</body>
</html> 