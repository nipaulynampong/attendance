<?php
require_once __DIR__ . '/../config/constants.php';

class SessionManager {
    private static $instance = null;

    private function __construct() {
        $this->initSession();
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function initSession() {
        if (session_status() === PHP_SESSION_NONE) {
            // Set secure session parameters
            ini_set('session.use_strict_mode', 1);
            ini_set('session.use_only_cookies', 1);
            ini_set('session.cookie_httponly', 1);
            
            session_name(SESSION_NAME);
            session_start();
            
            // Regenerate session ID periodically
            if (!isset($_SESSION['last_regeneration'])) {
                $this->regenerateSession();
            } else if (time() - $_SESSION['last_regeneration'] > 1800) { // 30 minutes
                $this->regenerateSession();
            }
        }
    }

    private function regenerateSession() {
        // Save old session data
        $old_session_data = isset($_SESSION) ? $_SESSION : array();
        
        // Generate new session ID
        session_regenerate_id(true);
        
        // Restore old session data
        $_SESSION = $old_session_data;
        $_SESSION['last_regeneration'] = time();
    }

    public function set($key, $value) {
        $_SESSION[$key] = $value;
    }

    public function get($key, $default = null) {
        return isset($_SESSION[$key]) ? $_SESSION[$key] : $default;
    }

    public function remove($key) {
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }

    public function destroy() {
        session_destroy();
        $_SESSION = array();
        
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }
    }

    public function isLoggedIn() {
        return isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id']);
    }

    public function getAdminId() {
        return $this->get('admin_id');
    }

    public function setAdminId($id) {
        $this->set('admin_id', $id);
    }

    public function getLastActivity() {
        return $this->get('last_activity', 0);
    }

    public function updateLastActivity() {
        $this->set('last_activity', time());
    }

    public function isSessionExpired() {
        $lastActivity = $this->getLastActivity();
        return ($lastActivity > 0 && time() - $lastActivity > SESSION_LIFETIME);
    }

    public function generateCSRFToken() {
        $token = bin2hex(random_bytes(CSRF_TOKEN_LENGTH));
        $this->set(CSRF_TOKEN_NAME, $token);
        return $token;
    }

    public function validateCSRFToken($token) {
        return hash_equals($this->get(CSRF_TOKEN_NAME, ''), $token);
    }
} 