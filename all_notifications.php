<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin_id'])) {
    header("Location: homepage.php");
    exit();
}

require_once 'notifications.php';

// Pagination parameters
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$perPage = 20;
$offset = ($page - 1) * $perPage;

// Get connection
$conn = getConnection();

// Get total notifications count
$countSql = "SELECT COUNT(*) as total FROM notifications";
$countResult = $conn->query($countSql);
$countRow = $countResult->fetch_assoc();
$totalNotifications = $countRow['total'];
$totalPages = ceil($totalNotifications / $perPage);

// Get paginated notifications
$sql = "SELECT * FROM notifications ORDER BY created_at DESC LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $perPage, $offset);
$stmt->execute();
$result = $stmt->get_result();

$notifications = [];
while ($row = $result->fetch_assoc()) {
    $notifications[] = $row;
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Notifications</title>
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
            padding: 20px;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
            background-color: white;
            border-radius: 8px;
            box-shadow: var(--shadow-md);
            padding: 20px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
            margin-bottom: 20px;
        }

        h1 {
            font-size: 1.5rem;
            color: var(--primary);
            font-weight: 600;
        }

        .back-link {
            color: var(--primary);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 0.9rem;
            padding: 5px 10px;
            border-radius: 4px;
            transition: all 0.2s;
        }

        .back-link:hover {
            background-color: rgba(79, 111, 82, 0.1);
        }

        .notification-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .notification-item {
            padding: 15px;
            border-radius: 8px;
            background-color: white;
            box-shadow: var(--shadow-sm);
            display: flex;
            align-items: flex-start;
            gap: 15px;
            transition: all 0.2s;
            border-left: 3px solid transparent;
        }

        .notification-item.unread {
            background-color: rgba(79, 111, 82, 0.05);
            border-left-color: var(--primary);
        }

        .notification-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: rgba(79, 111, 82, 0.1);
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
        }

        .notification-content {
            flex: 1;
        }

        .notification-message {
            font-size: 0.95rem;
            margin-bottom: 5px;
            color: var(--text-dark);
        }

        .notification-item.unread .notification-message {
            font-weight: 500;
        }

        .notification-meta {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .notification-type {
            font-size: 0.75rem;
            padding: 3px 8px;
            border-radius: 20px;
            background-color: rgba(79, 111, 82, 0.1);
            color: var(--primary);
        }

        .notification-time {
            font-size: 0.75rem;
            color: #666;
        }

        .mark-read-btn {
            background: none;
            border: none;
            color: var(--primary);
            cursor: pointer;
            font-size: 0.8rem;
            padding: 5px 10px;
            border-radius: 4px;
            margin-left: auto;
            opacity: 0.7;
            transition: all 0.2s;
        }

        .mark-read-btn:hover {
            background-color: rgba(79, 111, 82, 0.1);
            opacity: 1;
        }

        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            margin-top: 30px;
        }

        .page-item {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background-color: white;
            box-shadow: var(--shadow-sm);
            color: var(--text-dark);
            text-decoration: none;
            font-size: 0.9rem;
            transition: all 0.2s;
        }

        .page-item:hover {
            background-color: rgba(79, 111, 82, 0.1);
            color: var(--primary);
        }

        .page-item.active {
            background-color: var(--primary);
            color: white;
        }

        .page-item.disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .no-notifications {
            padding: 40px 20px;
            text-align: center;
            color: #666;
        }

        .no-notifications i {
            font-size: 3rem;
            color: #ddd;
            margin-bottom: 15px;
        }

        .no-notifications p {
            font-size: 1.1rem;
            margin-bottom: 5px;
        }

        .actions {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 15px;
        }

        .mark-all-read-btn {
            background-color: var(--primary);
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.9rem;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .mark-all-read-btn:hover {
            background-color: var(--primary-dark);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>All Notifications</h1>
            <a href="AdminPanel.php" class="back-link">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>

        <?php if (!empty($notifications)): ?>
            <div class="actions">
                <?php 
                $unreadCount = 0;
                foreach ($notifications as $notification) {
                    if ($notification['is_read'] == 0) {
                        $unreadCount++;
                    }
                }
                
                if ($unreadCount > 0):
                ?>
                <button class="mark-all-read-btn" onclick="markAllAsRead()">
                    <i class="fas fa-check-double"></i> Mark All as Read
                </button>
                <?php endif; ?>
            </div>
            
            <div class="notification-list">
                <?php foreach ($notifications as $notification): ?>
                    <div class="notification-item <?php echo $notification['is_read'] ? '' : 'unread'; ?>" data-id="<?php echo $notification['id']; ?>">
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
                        
                        // Format the type for display
                        $displayType = ucfirst($notification['type']);
                        ?>
                        <div class="notification-icon">
                            <i class="fas fa-<?php echo $icon; ?>"></i>
                        </div>
                        <div class="notification-content">
                            <div class="notification-message">
                                <?php echo htmlspecialchars($notification['message']); ?>
                            </div>
                            <div class="notification-meta">
                                <div class="notification-type"><?php echo $displayType; ?></div>
                                <div class="notification-time"><?php echo date('M d, Y g:i a', strtotime($notification['created_at'])); ?></div>
                                <?php if (!$notification['is_read']): ?>
                                    <button class="mark-read-btn" onclick="markAsRead(<?php echo $notification['id']; ?>)">
                                        <i class="fas fa-check"></i> Mark as read
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?php echo $page - 1; ?>" class="page-item">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    <?php else: ?>
                        <span class="page-item disabled">
                            <i class="fas fa-chevron-left"></i>
                        </span>
                    <?php endif; ?>
                    
                    <?php
                    $startPage = max(1, $page - 2);
                    $endPage = min($totalPages, $page + 2);
                    
                    if ($startPage > 1) {
                        echo '<a href="?page=1" class="page-item">1</a>';
                        if ($startPage > 2) {
                            echo '<span class="page-item disabled">...</span>';
                        }
                    }
                    
                    for ($i = $startPage; $i <= $endPage; $i++) {
                        if ($i == $page) {
                            echo '<span class="page-item active">' . $i . '</span>';
                        } else {
                            echo '<a href="?page=' . $i . '" class="page-item">' . $i . '</a>';
                        }
                    }
                    
                    if ($endPage < $totalPages) {
                        if ($endPage < $totalPages - 1) {
                            echo '<span class="page-item disabled">...</span>';
                        }
                        echo '<a href="?page=' . $totalPages . '" class="page-item">' . $totalPages . '</a>';
                    }
                    ?>
                    
                    <?php if ($page < $totalPages): ?>
                        <a href="?page=<?php echo $page + 1; ?>" class="page-item">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    <?php else: ?>
                        <span class="page-item disabled">
                            <i class="fas fa-chevron-right"></i>
                        </span>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            
        <?php else: ?>
            <div class="no-notifications">
                <i class="fas fa-bell-slash"></i>
                <p>No notifications found</p>
                <span>You'll see notifications here when there's activity in the system</span>
            </div>
        <?php endif; ?>
    </div>

    <script>
        function markAsRead(id) {
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
                    const notification = document.querySelector(`.notification-item[data-id="${id}"]`);
                    notification.classList.remove('unread');
                    const markButton = notification.querySelector('.mark-read-btn');
                    if (markButton) markButton.remove();
                }
            })
            .catch(error => console.error('Error marking notification as read:', error));
        }
        
        function markAllAsRead() {
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
                    document.querySelectorAll('.notification-item.unread').forEach(notification => {
                        notification.classList.remove('unread');
                        const markButton = notification.querySelector('.mark-read-btn');
                        if (markButton) markButton.remove();
                    });
                    
                    const markAllButton = document.querySelector('.mark-all-read-btn');
                    if (markAllButton) markAllButton.remove();
                }
            })
            .catch(error => console.error('Error marking all notifications as read:', error));
        }
    </script>
</body>
</html> 