<?php

class Config {
    

    
    /**
     * Application name
     */
    const APP_NAME = 'JanataConnect';
    
    /**
     * Application version
     */
    const APP_VERSION = '1.0.0';
    
    /**
     * Application URL (base URL for the application)
     */
    const APP_URL = 'http://localhost/JanataConnect';
    
    /**
     * Application environment (development, production, testing)
     */
    const APP_ENV = 'development';
    
    /**
     * Application timezone
     */
    const APP_TIMEZONE = 'Asia/Dhaka';
    

    /**
     * Session timeout in seconds (1 hour)
     */
    const SESSION_TIMEOUT = 3600;
    
    /**
     * Minimum password length
     */
    const PASSWORD_MIN_LENGTH = 6;
    
    /**
     * Maximum login attempts before lockout
     */
    const MAX_LOGIN_ATTEMPTS = 5;
    
    /**
     * Lockout duration in seconds (15 minutes)
     */
    const LOCKOUT_DURATION = 900;
    
    /**
     * CSRF token lifetime in seconds (1 hour)
     */
    const CSRF_TOKEN_LIFETIME = 3600;
    
    // ============================================================================
    // FILE UPLOAD SETTINGS
    // ============================================================================
    
    /**
     * Maximum file size in bytes (5MB)
     */
    const MAX_FILE_SIZE = 5242880;
    
    /**
     * Allowed file types for uploads
     */
    const ALLOWED_FILE_TYPES = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx'];
    
    /**
     * Upload directory path
     */
    const UPLOAD_PATH = 'public/uploads/';
    
    /**
     * Maximum number of files per submission
     */
    const MAX_FILES_PER_SUBMISSION = 5;
    
    // ============================================================================
    // PAGINATION SETTINGS
    // ============================================================================
    
    /**
     * Default number of items per page
     */
    const ITEMS_PER_PAGE = 10;
    
    /**
     * Maximum number of items per page
     */
    const MAX_ITEMS_PER_PAGE = 100;
    
    // ============================================================================
    // USER ROLES
    // ============================================================================
    
    /**
     * Citizen role
     */
    const ROLE_CITIZEN = 'citizen';
    
    /**
     * Official role
     */
    const ROLE_OFFICIAL = 'official';
    
    /**
     * Admin role
     */
    const ROLE_ADMIN = 'admin';
    
    /**
     * Get all valid user roles
     * 
     * @return array Array of valid user roles
     */
    public static function getUserRoles() {
        return [
            self::ROLE_CITIZEN,
            self::ROLE_OFFICIAL,
            self::ROLE_ADMIN
        ];
    }
    
    // ============================================================================
    // SUBMISSION STATUS CONSTANTS
    // ============================================================================
    
    /**
     * Pending status
     */
    const STATUS_PENDING = 'pending';
    
    /**
     * Under review status
     */
    const STATUS_UNDER_REVIEW = 'under_review';
    
    /**
     * Approved status
     */
    const STATUS_APPROVED = 'approved';
    
    /**
     * Rejected status
     */
    const STATUS_REJECTED = 'rejected';
    
    /**
     * Completed status
     */
    const STATUS_COMPLETED = 'completed';
    
    /**
     * Get all valid submission statuses
     * 
     * @return array Array of valid submission statuses
     */
    public static function getSubmissionStatuses() {
        return [
            self::STATUS_PENDING,
            self::STATUS_UNDER_REVIEW,
            self::STATUS_APPROVED,
            self::STATUS_REJECTED,
            self::STATUS_COMPLETED
        ];
    }
    
    // ============================================================================
    // SUBMISSION PRIORITY CONSTANTS
    // ============================================================================
    
    /**
     * Low priority
     */
    const PRIORITY_LOW = 'low';
    
    /**
     * Medium priority
     */
    const PRIORITY_MEDIUM = 'medium';
    
    /**
     * High priority
     */
    const PRIORITY_HIGH = 'high';
    
    /**
     * Get all valid submission priorities
     * 
     * @return array Array of valid submission priorities
     */
    public static function getSubmissionPriorities() {
        return [
            self::PRIORITY_LOW,
            self::PRIORITY_MEDIUM,
            self::PRIORITY_HIGH
        ];
    }
    
    // ============================================================================
    // DEPARTMENT CATEGORIES
    // ============================================================================
    
    /**
     * Get predefined government departments
     * 
     * @return array Associative array of department codes and names
     */
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
    
    // ============================================================================
    // HELPER FUNCTIONS
    // ============================================================================
    
    /**
     * Redirect to a URL
     * 
     * @param string $url The URL to redirect to
     */
    public static function redirect($url) {
        header("Location: " . self::APP_URL . $url);
        exit();
    }
    
    /**
     * Check if user is logged in
     * 
     * @return bool True if user is logged in, false otherwise
     */
    public static function isLoggedIn() {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }
    
    /**
     * Require user to be logged in
     * Redirects to login page if not logged in
     */
    public static function requireLogin() {
        if (!self::isLoggedIn()) {
            self::redirect('/login');
        }
    }
    
    /**
     * Require user to have specific role
     * 
     * @param string $role The required role
     */
    public static function requireRole($role) {
        self::requireLogin();
        
        if ($_SESSION['user_role'] !== $role) {
            self::redirect('/dashboard');
        }
    }
    
    /**
     * Check if user has specific role
     * 
     * @param string $role The role to check
     * @return bool True if user has the role, false otherwise
     */
    public static function hasRole($role) {
        return self::isLoggedIn() && $_SESSION['user_role'] === $role;
    }
    
    /**
     * Check if user is admin
     * 
     * @return bool True if user is admin, false otherwise
     */
    public static function isAdmin() {
        return self::hasRole(self::ROLE_ADMIN);
    }
    
    /**
     * Check if user is official
     * 
     * @return bool True if user is official, false otherwise
     */
    public static function isOfficial() {
        return self::hasRole(self::ROLE_OFFICIAL);
    }
    
    /**
     * Check if user is citizen
     * 
     * @return bool True if user is citizen, false otherwise
     */
    public static function isCitizen() {
        return self::hasRole(self::ROLE_CITIZEN);
    }
    
    /**
     * Sanitize input data
     * 
     * @param string $input The input to sanitize
     * @return string Sanitized input
     */
    public static function sanitizeInput($input) {
        return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Generate CSRF token
     * 
     * @return string CSRF token
     */
    public static function generateCSRFToken() {
        if (!isset($_SESSION['csrf_token']) || !isset($_SESSION['csrf_token_time'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            $_SESSION['csrf_token_time'] = time();
        }
        
        // Check if token has expired
        if (time() - $_SESSION['csrf_token_time'] > self::CSRF_TOKEN_LIFETIME) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            $_SESSION['csrf_token_time'] = time();
        }
        
        return $_SESSION['csrf_token'];
    }
    
    /**
     * Validate CSRF token
     * 
     * @param string $token The token to validate
     * @return bool True if token is valid, false otherwise
     */
    public static function validateCSRFToken($token) {
        if (!isset($_SESSION['csrf_token']) || !isset($_SESSION['csrf_token_time'])) {
            return false;
        }
        
        // Check if token has expired
        if (time() - $_SESSION['csrf_token_time'] > self::CSRF_TOKEN_LIFETIME) {
            unset($_SESSION['csrf_token']);
            unset($_SESSION['csrf_token_time']);
            return false;
        }
        
        return hash_equals($_SESSION['csrf_token'], $token);
    }
    
    /**
     * Format file size in human readable format
     * 
     * @param int $bytes File size in bytes
     * @return string Formatted file size
     */
    public static function formatFileSize($bytes) {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }
    
    /**
     * Format date in a readable format
     * 
     * @param string $date The date to format
     * @param string $format The output format
     * @return string Formatted date
     */
    public static function formatDate($date, $format = 'M d, Y') {
        return date($format, strtotime($date));
    }
    
    /**
     * Format datetime in a readable format
     * 
     * @param string $datetime The datetime to format
     * @param string $format The output format
     * @return string Formatted datetime
     */
    public static function formatDateTime($datetime, $format = 'M d, Y H:i') {
        return date($format, strtotime($datetime));
    }
    
    /**
     * Get time ago string
     * 
     * @param string $datetime The datetime to compare
     * @return string Time ago string
     */
    public static function timeAgo($datetime) {
        $time = time() - strtotime($datetime);
        
        if ($time < 60) {
            return 'just now';
        } elseif ($time < 3600) {
            $minutes = floor($time / 60);
            return $minutes . ' minute' . ($minutes > 1 ? 's' : '') . ' ago';
        } elseif ($time < 86400) {
            $hours = floor($time / 3600);
            return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
        } elseif ($time < 2592000) {
            $days = floor($time / 86400);
            return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
        } else {
            return self::formatDate($datetime);
        }
    }
    
    /**
     * Generate a random string
     * 
     * @param int $length The length of the string
     * @return string Random string
     */
    public static function generateRandomString($length = 10) {
        return bin2hex(random_bytes($length / 2));
    }
    
    /**
     * Validate email address
     * 
     * @param string $email The email to validate
     * @return bool True if email is valid, false otherwise
     */
    public static function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    /**
     * Validate password strength
     * 
     * @param string $password The password to validate
     * @return array Validation result with success status and errors
     */
    public static function validatePassword($password) {
        $errors = [];
        
        if (strlen($password) < self::PASSWORD_MIN_LENGTH) {
            $errors[] = 'Password must be at least ' . self::PASSWORD_MIN_LENGTH . ' characters long';
        }
        
        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = 'Password must contain at least one uppercase letter';
        }
        
        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = 'Password must contain at least one lowercase letter';
        }
        
        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = 'Password must contain at least one number';
        }
        
        return [
            'success' => empty($errors),
            'errors' => $errors
        ];
    }
    
    /**
     * Get status badge class for CSS styling
     * 
     * @param string $status The status
     * @return string CSS class name
     */
    public static function getStatusBadgeClass($status) {
        $classes = [
            self::STATUS_PENDING => 'badge-pending',
            self::STATUS_UNDER_REVIEW => 'badge-under-review',
            self::STATUS_APPROVED => 'badge-approved',
            self::STATUS_REJECTED => 'badge-rejected',
            self::STATUS_COMPLETED => 'badge-completed'
        ];
        
        return $classes[$status] ?? 'badge-default';
    }
    
    /**
     * Get priority badge class for CSS styling
     * 
     * @param string $priority The priority
     * @return string CSS class name
     */
    public static function getPriorityBadgeClass($priority) {
        $classes = [
            self::PRIORITY_LOW => 'badge-low',
            self::PRIORITY_MEDIUM => 'badge-medium',
            self::PRIORITY_HIGH => 'badge-high'
        ];
        
        return $classes[$priority] ?? 'badge-default';
    }
    
    /**
     * Log error message
     * 
     * @param string $message The error message
     * @param string $file The file where error occurred
     * @param int $line The line where error occurred
     */
    public static function logError($message, $file = '', $line = 0) {
        $logMessage = date('Y-m-d H:i:s') . " - Error: {$message}";
        
        if ($file) {
            $logMessage .= " in {$file}";
        }
        
        if ($line) {
            $logMessage .= " on line {$line}";
        }
        
        $logMessage .= PHP_EOL;
        
        error_log($logMessage, 3, ROOT_PATH . '/logs/error.log');
    }
    
    /**
     * Get application information
     * 
     * @return array Application information
     */
    public static function getAppInfo() {
        return [
            'name' => self::APP_NAME,
            'version' => self::APP_VERSION,
            'url' => self::APP_URL,
            'environment' => self::APP_ENV,
            'timezone' => self::APP_TIMEZONE
        ];
    }
}
?>