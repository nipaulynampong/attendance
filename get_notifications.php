<?php
// Include notification functions
require_once 'notifications_functions.php';

// Set header to return JSON
header('Content-Type: application/json');

// Get action from request
$action = isset($_GET['action']) ? $_GET['action'] : '';

// Response array
$response = ['success' => false, 'data' => null, 'message' => ''];

switch ($action) {
    case 'get_count':
        // Get unread notifications count
        $count = getUnreadNotificationsCount();
        $response = [
            'success' => true,
            'data' => $count,
            'message' => 'Notification count retrieved successfully'
        ];
        break;
        
    case 'get_notifications':
        // Get recent notifications
        $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;
        $notifications = getRecentNotifications($limit);
        
        // Format timestamps
        foreach ($notifications as &$notification) {
            $timestamp = strtotime($notification['timestamp']);
            $notification['formatted_time'] = date('M d, Y h:i A', $timestamp);
            
            // Calculate time ago
            $now = time();
            $diff = $now - $timestamp;
            
            if ($diff < 60) {
                $notification['time_ago'] = 'Just now';
            } elseif ($diff < 3600) {
                $minutes = floor($diff / 60);
                $notification['time_ago'] = $minutes . ' min' . ($minutes > 1 ? 's' : '') . ' ago';
            } elseif ($diff < 86400) {
                $hours = floor($diff / 3600);
                $notification['time_ago'] = $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
            } elseif ($diff < 604800) {
                $days = floor($diff / 86400);
                $notification['time_ago'] = $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
            } else {
                $notification['time_ago'] = date('M d, Y', $timestamp);
            }
            
            // Add icon class based on event type
            switch ($notification['event_type']) {
                case 'login':
                    $notification['icon_class'] = 'fa-sign-in-alt';
                    $notification['icon_color'] = '#4f6f52';
                    break;
                case 'attendance_scan':
                    $notification['icon_class'] = 'fa-qrcode';
                    $notification['icon_color'] = '#3e3f5b';
                    break;
                case 'attendance_modification':
                    $notification['icon_class'] = 'fa-edit';
                    $notification['icon_color'] = '#e67e22';
                    break;
                case 'new_employee':
                    $notification['icon_class'] = 'fa-user-plus';
                    $notification['icon_color'] = '#2980b9';
                    break;
                case 'new_department':
                    $notification['icon_class'] = 'fa-building';
                    $notification['icon_color'] = '#8e44ad';
                    break;
                case 'new_event':
                    $notification['icon_class'] = 'fa-calendar-plus';
                    $notification['icon_color'] = '#16a085';
                    break;
                case 'new_admin':
                    $notification['icon_class'] = 'fa-user-shield';
                    $notification['icon_color'] = '#c0392b';
                    break;
                default:
                    $notification['icon_class'] = 'fa-bell';
                    $notification['icon_color'] = '#7f8c8d';
            }
        }
        
        $response = [
            'success' => true,
            'data' => $notifications,
            'message' => 'Notifications retrieved successfully'
        ];
        break;
        
    case 'mark_read':
        // Mark notification as read
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        
        if ($id > 0) {
            $result = markNotificationAsRead($id);
            $response = [
                'success' => $result,
                'message' => $result ? 'Notification marked as read' : 'Failed to mark notification as read'
            ];
        } else {
            $response['message'] = 'Invalid notification ID';
        }
        break;
        
    case 'mark_all_read':
        // Mark all notifications as read
        $result = markAllNotificationsAsRead();
        $response = [
            'success' => $result,
            'message' => $result ? 'All notifications marked as read' : 'Failed to mark all notifications as read'
        ];
        break;
        
    default:
        $response['message'] = 'Invalid action';
}

// Return JSON response
echo json_encode($response);
?>
