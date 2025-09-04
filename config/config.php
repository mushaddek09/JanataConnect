<?php
// Application Configuration
class Config {
    // Application settings
    const APP_NAME = 'JanataConnect';
    const APP_VERSION = '1.0.0';
    const APP_URL = 'http://localhost/JanataConnect';
    
    // Security settings
    const SESSION_TIMEOUT = 3600; // 1 hour
    const PASSWORD_MIN_LENGTH = 6;
    
    // File upload settings
    const MAX_FILE_SIZE = 5242880; // 5MB
    const ALLOWED_FILE_TYPES = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx'];
    const UPLOAD_PATH = 'public/uploads/';
    
    // Pagination
    const ITEMS_PER_PAGE = 10;
    
    // User roles
    const ROLE_CITIZEN = 'citizen';
    const ROLE_OFFICIAL = 'official';
    const ROLE_ADMIN = 'admin';
    
    // Submission status
    const STATUS_PENDING = 'pending';
    const STATUS_UNDER_REVIEW = 'under_review';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_COMPLETED = 'completed';
    
    // Department categories
    public static function getDepartments() {
        return [
            'local_government' => 'Local Government & Rural Development',
            'education' => 'Education',
            'health' => 'Health & Family Welfare',
            'transport' => 'Road Transport & Highways',
            'water' => 'Water Resources',
            'agriculture' => 'Agriculture',
            'power' => 'Power & Energy',
            'environment' => 'Environment & Forest',
            'housing' => 'Housing & Public Works',
            'social_welfare' => 'Social Welfare',
            'youth_sports' => 'Youth & Sports',
            'women_children' => 'Women & Children Affairs',
            'others' => 'Others'
        ];
    }
    
    // Helper functions
    public static function redirect($url) {
        header("Location: " . self::APP_URL . $url);
        exit();
    }
    
    public static function isLoggedIn() {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }
    
    public static function requireLogin() {
        if (!self::isLoggedIn()) {
            self::redirect('/login');
        }
    }
    
    public static function requireRole($role) {
        self::requireLogin();
        if ($_SESSION['user_role'] !== $role) {
            self::redirect('/dashboard');
        }
    }
    
    public static function sanitizeInput($input) {
        return htmlspecialchars(strip_tags(trim($input)));
    }
    
    public static function generateCSRFToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    public static function validateCSRFToken($token) {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
}
?>
