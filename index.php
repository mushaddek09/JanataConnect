<?php
// JanataConnect - Main Entry Point
session_start();

// Define constants
define('ROOT_PATH', __DIR__);
define('APP_PATH', ROOT_PATH . '/app');
define('CONFIG_PATH', ROOT_PATH . '/config');
define('PUBLIC_PATH', ROOT_PATH . '/public');

// Include configuration and models
require_once CONFIG_PATH . '/database.php';
require_once CONFIG_PATH . '/config.php';
require_once APP_PATH . '/models/BaseModel.php';
require_once APP_PATH . '/models/User.php';
require_once APP_PATH . '/models/Submission.php';
require_once APP_PATH . '/models/Department.php';

// Get the current path
$path = $_SERVER['REQUEST_URI'];
$path = str_replace('/JanataConnect', '', $path);
$path = parse_url($path, PHP_URL_PATH); // Remove query parameters
$path = rtrim($path, '/');
$path = $path ?: '/';

// Route the request
routeRequest($path);

/**
 * Main routing function - clean and organized
 */
function routeRequest($path) {
    // Public routes (no login required)
    if (isPublicRoute($path)) {
        handlePublicRoute($path);
        return;
    }
    
    // Protected routes (login required)
    if (isProtectedRoute($path)) {
        handleProtectedRoute($path);
        return;
    }
    
    // Dynamic routes (like edit/delete with IDs)
    if (isDynamicRoute($path)) {
        handleDynamicRoute($path);
        return;
    }
    
    // 404 - Route not found
    show404();
}

/**
 * Check if route is public (no login required)
 */
function isPublicRoute($path) {
    $publicRoutes = ['/', '/login', '/register'];
    return in_array($path, $publicRoutes);
}

/**
 * Check if route is protected (login required)
 */
function isProtectedRoute($path) {
    $protectedRoutes = [
        '/dashboard', '/logout', '/submit-suggestion', '/my-submissions',
        '/admin', '/admin/users', '/admin/departments',
        '/official', '/official/submissions'
    ];
    return in_array($path, $protectedRoutes);
}

/**
 * Check if route is dynamic (contains IDs)
 */
function isDynamicRoute($path) {
    return strpos($path, '/submission/edit/') === 0 ||
           strpos($path, '/submission/update/') === 0 ||
           strpos($path, '/submission/delete/') === 0 ||
           strpos($path, '/official/submission/update/') === 0;
}

/**
 * Handle public routes
 */
function handlePublicRoute($path) {
    switch ($path) {
        case '/':
            showHomePage();
            break;
            
        case '/login':
            handleLogin();
            break;
            
        case '/register':
            handleRegister();
            break;
    }
}

/**
 * Handle protected routes
 */
function handleProtectedRoute($path) {
    // Check if user is logged in
    if (!Config::isLoggedIn()) {
        redirectToLogin();
        return;
    }
    
    switch ($path) {
        case '/dashboard':
            showDashboard();
            break;
            
        case '/logout':
            handleLogout();
            break;
            
        case '/submit-suggestion':
            handleSubmitSuggestion();
            break;
            
        case '/my-submissions':
            showMySubmissions();
            break;
            
        case '/admin':
            showAdminDashboard();
            break;
            
        case '/admin/users':
            showAdminUsers();
            break;
            
        case '/admin/departments':
            showAdminDepartments();
            break;
            
        case '/official':
            showOfficialDashboard();
            break;
        
        case '/official/submissions':
            showOfficialSubmissions();
            break;
    }
}

/**
 * Handle dynamic routes
 */
function handleDynamicRoute($path) {
    if (!Config::isLoggedIn()) {
        redirectToLogin();
        return;
    }
    
    if (strpos($path, '/submission/edit/') === 0) {
        showEditSubmission($path);
    } elseif (strpos($path, '/submission/update/') === 0) {
        updateSubmission($path);
    } elseif (strpos($path, '/submission/delete/') === 0) {
        deleteSubmission($path);
    } elseif (strpos($path, '/official/submission/update/') === 0) {
        updateOfficialSubmission($path);
    }
}

// ============================================================================
// PAGE FUNCTIONS - Each function handles one specific page
// ============================================================================

/**
 * Show home page
 */
function showHomePage() {
    $title = 'Welcome to ' . Config::APP_NAME;
    $departments = Config::getDepartments();
    
    $submissionModel = new Submission();
    $recent_submissions = $submissionModel->getRecentSubmissions(5);
    
    include APP_PATH . '/views/home/index.php';
}

/**
 * Handle login
 */
function handleLogin() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = Config::sanitizeInput($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        
        if (empty($email) || empty($password)) {
            $error = 'Email and password are required';
        } else {
            $userModel = new User();
            $user = $userModel->findByEmail($email);
            
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['csrf_token'] = Config::generateCSRFToken();
                
                // Redirect based on role
                switch ($user['role']) {
                    case 'admin':
                        header('Location: /JanataConnect/admin');
                        break;
                    case 'official':
                        header('Location: /JanataConnect/official');
                        break;
                    default:
                        header('Location: /JanataConnect/dashboard');
                }
                exit;
            } else {
                $error = 'Invalid email or password';
            }
        }
    }
    
    $title = 'Login - ' . Config::APP_NAME;
    include APP_PATH . '/views/auth/login.php';
}

/**
 * Handle registration
 */
function handleRegister() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = Config::sanitizeInput($_POST['name'] ?? '');
        $email = Config::sanitizeInput($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        
        if (empty($name) || empty($email) || empty($password)) {
            $error = 'All fields are required';
        } elseif ($password !== $confirm_password) {
            $error = 'Passwords do not match';
        } elseif (strlen($password) < 6) {
            $error = 'Password must be at least 6 characters';
        } else {
            $userModel = new User();
            
            if ($userModel->findByEmail($email)) {
                $error = 'Email already exists';
            } else {
                $userData = [
                    'name' => $name,
                    'email' => $email,
                    'password' => password_hash($password, PASSWORD_DEFAULT),
                    'role' => 'citizen'
                ];
                
                if ($userModel->create($userData)) {
                    $success = 'Registration successful! Please login.';
                } else {
                    $error = 'Registration failed. Please try again.';
                }
            }
        }
    }
    
    $title = 'Register - ' . Config::APP_NAME;
    include APP_PATH . '/views/auth/register.php';
}

/**
 * Show dashboard
 */
function showDashboard() {
    // Dashboard is accessible to all logged-in users
    $title = 'Dashboard - ' . Config::APP_NAME;
    
    // Get user-specific data based on role
    if ($_SESSION['user_role'] === 'citizen') {
        $submissionModel = new Submission();
        $total_submissions = count($submissionModel->getSubmissionsByUser($_SESSION['user_id']));
    } else {
        $total_submissions = 0; // For admin/official users
    }
    
    include APP_PATH . '/views/dashboard/index.php';
}

/**
 * Handle logout
 */
function handleLogout() {
    session_destroy();
    header('Location: /JanataConnect/');
    exit;
}

/**
 * Handle submit suggestion
 */
function handleSubmitSuggestion() {
    if ($_SESSION['user_role'] !== 'citizen') {
        header('Location: /JanataConnect/dashboard');
        exit;
    }
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $title = Config::sanitizeInput($_POST['title'] ?? '');
        $description = Config::sanitizeInput($_POST['description'] ?? '');
        $department_id = (int)($_POST['department_id'] ?? 0);
        $location = Config::sanitizeInput($_POST['location'] ?? '');
        
        if (empty($title) || empty($description) || empty($department_id)) {
            $error = 'Title, description, and department are required';
        } else {
            $submissionModel = new Submission();
            $submissionData = [
                'user_id' => $_SESSION['user_id'],
                'title' => $title,
                'description' => $description,
                'department_id' => $department_id,
                'location' => $location,
                'status' => 'pending'
            ];
            
            $submissionId = $submissionModel->create($submissionData);
            if ($submissionId) {
                // Handle file uploads if any
                if (!empty($_FILES['images']['name'][0])) {
                    $uploadResult = handleFileUploads($submissionId, $_FILES['images']);
                    if (!$uploadResult['success']) {
                        $error = 'Submission created but file upload failed: ' . $uploadResult['message'];
                    }
                }
                
                if (!isset($error)) {
                    header('Location: /JanataConnect/my-submissions?success=1');
                    exit;
                }
            } else {
                $error = 'Failed to submit suggestion. Please try again.';
            }
        }
    }
    
    $title = 'Submit Suggestion - ' . Config::APP_NAME;
    $csrf_token = Config::generateCSRFToken();
    $departmentModel = new Department();
    $departments = $departmentModel->getActiveDepartments();
    include APP_PATH . '/views/submissions/create.php';
}

/**
 * Show my submissions
 */
function showMySubmissions() {
    if ($_SESSION['user_role'] !== 'citizen') {
        header('Location: /JanataConnect/dashboard');
        exit;
    }
    
    $title = 'My Submissions - ' . Config::APP_NAME;
    $submissionModel = new Submission();
    $submissions = $submissionModel->getSubmissionsByUser($_SESSION['user_id']);
    
    // Display session messages
    if (isset($_SESSION['success_message'])) {
        $success = $_SESSION['success_message'];
        unset($_SESSION['success_message']);
    }
    if (isset($_SESSION['error_message'])) {
        $error = $_SESSION['error_message'];
        unset($_SESSION['error_message']);
    }
    
    if (isset($_GET['success'])) {
        $success = 'Suggestion submitted successfully!';
    }
    
    include APP_PATH . '/views/submissions/index.php';
}

/**
 * Show admin dashboard
 */
function showAdminDashboard() {
    if ($_SESSION['user_role'] !== 'admin') {
        header('Location: /JanataConnect/login');
        exit;
    }
    
    $title = 'Admin Dashboard - ' . Config::APP_NAME;
    include APP_PATH . '/views/admin/index.php';
}

/**
 * Show admin users
 */
function showAdminUsers() {
    if ($_SESSION['user_role'] !== 'admin') {
        header('Location: /JanataConnect/login');
        exit;
    }
    
    $title = 'Manage Users - ' . Config::APP_NAME;
    $userModel = new User();
    $users = $userModel->findAll();
    include APP_PATH . '/views/admin/users.php';
}

/**
 * Show admin departments
 */
function showAdminDepartments() {
    if ($_SESSION['user_role'] !== 'admin') {
        header('Location: /JanataConnect/login');
        exit;
    }
    
    $title = 'Manage Departments - ' . Config::APP_NAME;
    $departmentModel = new Department();
    $departments = $departmentModel->findAll();
    include APP_PATH . '/views/admin/departments.php';
}

/**
 * Show official dashboard
 */
function showOfficialDashboard() {
    if ($_SESSION['user_role'] !== 'official') {
        header('Location: /JanataConnect/login');
        exit;
    }
    
    $title = 'Official Dashboard - ' . Config::APP_NAME;
    include APP_PATH . '/views/official/index.php';
}

/**
 * Show official submissions
 */
function showOfficialSubmissions() {
    if ($_SESSION['user_role'] !== 'official') {
        header('Location: /JanataConnect/login');
        exit;
    }
    
    $title = 'Review Submissions - ' . Config::APP_NAME;
    $submissionModel = new Submission();
    $submissions = $submissionModel->getAllSubmissions(50, 0);
    
    // Display session messages
    if (isset($_SESSION['success_message'])) {
        $success = $_SESSION['success_message'];
        unset($_SESSION['success_message']);
    }
    if (isset($_SESSION['error_message'])) {
        $error = $_SESSION['error_message'];
        unset($_SESSION['error_message']);
    }
    
    include APP_PATH . '/views/official/submissions.php';
}

// ============================================================================
// DYNAMIC ROUTE FUNCTIONS - Handle routes with IDs
// ============================================================================

/**
 * Show edit submission form
 */
function showEditSubmission($path) {
    if ($_SESSION['user_role'] !== 'citizen') {
        redirectToLogin();
        return;
    }
    
    $submissionId = getSubmissionIdFromPath($path);
    if (!$submissionId) {
        redirectToMySubmissions();
        return;
    }
    
    $submissionModel = new Submission();
    $submission = $submissionModel->find($submissionId);
    
    if (!$submission || $submission['user_id'] != $_SESSION['user_id']) {
        redirectToMySubmissions();
        return;
    }
    
    $title = 'Edit Submission - ' . Config::APP_NAME;
    $csrf_token = Config::generateCSRFToken();
    $departmentModel = new Department();
    $departments = $departmentModel->getActiveDepartments();
    include APP_PATH . '/views/submissions/edit.php';
}

/**
 * Update submission
 */
function updateSubmission($path) {
    if ($_SESSION['user_role'] !== 'citizen') {
        redirectToLogin();
        return;
    }
    
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirectToMySubmissions();
        return;
    }
    
    $submissionId = getSubmissionIdFromPath($path);
    if (!$submissionId) {
        redirectToMySubmissions();
        return;
    }
    
    // Validate CSRF token
    if (!validateCSRFToken()) {
        redirectToMySubmissions();
        return;
    }
    
    $submissionModel = new Submission();
    $submission = $submissionModel->find($submissionId);
    
    if (!$submission || $submission['user_id'] != $_SESSION['user_id']) {
        redirectToMySubmissions();
        return;
    }
    
    $title = Config::sanitizeInput($_POST['title'] ?? '');
    $description = Config::sanitizeInput($_POST['description'] ?? '');
    $department_id = (int)($_POST['department_id'] ?? 0);
    $location = Config::sanitizeInput($_POST['location'] ?? '');
    
    if (empty($title) || empty($description) || empty($department_id)) {
        $_SESSION['error_message'] = 'Title, description, and department are required';
    } else {
        $updateData = [
            'title' => $title,
            'description' => $description,
            'department_id' => $department_id,
            'location' => $location
        ];
        
        if ($submissionModel->update($submissionId, $updateData)) {
            $_SESSION['success_message'] = 'Submission updated successfully!';
        } else {
            $_SESSION['error_message'] = 'Failed to update submission. Please try again.';
        }
    }
    
    redirectToMySubmissions();
}

/**
 * Delete submission
 */
function deleteSubmission($path) {
    if ($_SESSION['user_role'] !== 'citizen') {
        redirectToLogin();
        return;
    }
    
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirectToMySubmissions();
        return;
    }
    
    $submissionId = getSubmissionIdFromPath($path);
    if (!$submissionId) {
        redirectToMySubmissions();
        return;
    }
    
    // Validate CSRF token
    if (!validateCSRFToken()) {
        redirectToMySubmissions();
        return;
    }
    
    $submissionModel = new Submission();
    $submission = $submissionModel->find($submissionId);
    
    if (!$submission || $submission['user_id'] != $_SESSION['user_id']) {
        redirectToMySubmissions();
        return;
    }
    
    if ($submissionModel->delete($submissionId)) {
        $_SESSION['success_message'] = 'Submission deleted successfully!';
    } else {
        $_SESSION['error_message'] = 'Failed to delete submission.';
    }
    
    redirectToMySubmissions();
}

/**
 * Update official submission status
 */
function updateOfficialSubmission($path) {
    if ($_SESSION['user_role'] !== 'official') {
        redirectToLogin();
        return;
    }
    
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirectToOfficialSubmissions();
        return;
    }
    
    $submissionId = getSubmissionIdFromPath($path);
    if (!$submissionId) {
        redirectToOfficialSubmissions();
        return;
    }
    
    // Validate CSRF token
    if (!validateCSRFToken()) {
        redirectToOfficialSubmissions();
        return;
    }
    
    $status = Config::sanitizeInput($_POST['status'] ?? '');
    $comment = Config::sanitizeInput($_POST['comment'] ?? '');
    
    if (!in_array($status, ['pending', 'under_review', 'approved', 'rejected', 'completed'])) {
        redirectToOfficialSubmissions();
        return;
    }
    
    $submissionModel = new Submission();
    if ($submissionModel->updateStatus($submissionId, $status, $comment)) {
        $_SESSION['success_message'] = 'Submission status updated successfully!';
    } else {
        $_SESSION['error_message'] = 'Failed to update submission status.';
    }
    
    redirectToOfficialSubmissions();
}

// ============================================================================
// HELPER FUNCTIONS - Utility functions for common tasks
// ============================================================================

/**
 * Get submission ID from path
 */
function getSubmissionIdFromPath($path) {
    $pathParts = explode('/', trim($path, '/'));
    $submissionId = end($pathParts);
    return is_numeric($submissionId) ? (int)$submissionId : null;
}

/**
 * Validate CSRF token
 */
function validateCSRFToken() {
    return isset($_POST['csrf_token']) && 
           hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token']);
}

/**
 * Redirect to login
 */
function redirectToLogin() {
    header('Location: /JanataConnect/login');
    exit;
}

/**
 * Redirect to my submissions
 */
function redirectToMySubmissions() {
    header('Location: /JanataConnect/my-submissions');
    exit;
}

/**
 * Redirect to official submissions
 */
function redirectToOfficialSubmissions() {
    header('Location: /JanataConnect/official/submissions');
    exit;
}

/**
 * Show 404 page
 */
function show404() {
    http_response_code(404);
    $title = '404 - Page Not Found';
    include APP_PATH . '/views/shared/404.php';
}

/**
 * Handle file uploads for submissions
 */
function handleFileUploads($submissionId, $files) {
    $uploadDir = ROOT_PATH . '/public/uploads/submissions/';
    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
    $maxFileSize = 2 * 1024 * 1024; // 2MB
    $maxFiles = 5;
    
    // Create upload directory if it doesn't exist
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    $uploadedFiles = [];
    $errors = [];
    
    // Count existing files for this submission
    $existingFiles = glob($uploadDir . $submissionId . '_*');
    $fileCount = count($existingFiles);
    
    for ($i = 0; $i < count($files['name']); $i++) {
        if ($files['error'][$i] === UPLOAD_ERR_OK) {
            // Check file count limit
            if ($fileCount >= $maxFiles) {
                $errors[] = "Maximum {$maxFiles} files allowed per submission";
                break;
            }
            
            $fileName = $files['name'][$i];
            $fileSize = $files['size'][$i];
            $fileType = $files['type'][$i];
            $tmpName = $files['tmp_name'][$i];
            
            // Validate file type
            if (!in_array($fileType, $allowedTypes)) {
                $errors[] = "File '{$fileName}' is not a valid image type";
                continue;
            }
            
            // Validate file size
            if ($fileSize > $maxFileSize) {
                $errors[] = "File '{$fileName}' is too large (max 2MB)";
                continue;
            }
            
            // Generate unique filename
            $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
            $newFileName = $submissionId . '_' . time() . '_' . $i . '.' . $fileExtension;
            $filePath = $uploadDir . $newFileName;
            
            // Move uploaded file
            if (move_uploaded_file($tmpName, $filePath)) {
                // Save file info to database
                $database = new Database();
                $conn = $database->getConnection();
                
                if ($conn) {
                    $stmt = $conn->prepare("INSERT INTO submission_files (submission_id, file_name, file_path, file_type, file_size) VALUES (?, ?, ?, ?, ?)");
                    $relativePath = 'public/uploads/submissions/' . $newFileName;
                    $stmt->bind_param("isssi", $submissionId, $fileName, $relativePath, $fileType, $fileSize);
                    
                    if ($stmt->execute()) {
                        $uploadedFiles[] = $fileName;
                        $fileCount++;
                    } else {
                        $errors[] = "Failed to save file info for '{$fileName}': " . $conn->error;
                        unlink($filePath);
                    }
                } else {
                    $errors[] = "Database connection failed for '{$fileName}'";
                    unlink($filePath);
                }
            } else {
                $errors[] = "Failed to upload file '{$fileName}'. Check directory permissions.";
            }
        } else {
            $errors[] = "Upload error for file: " . $files['name'][$i];
        }
    }
    
    if (!empty($errors)) {
        return [
            'success' => false,
            'message' => implode(', ', $errors)
        ];
    }
    
    return [
        'success' => true,
        'files' => $uploadedFiles
    ];
}
?>