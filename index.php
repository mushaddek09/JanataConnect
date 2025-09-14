<?php


// Start session for user authentication
session_start();

// Define application paths
define('ROOT_PATH', __DIR__);
define('APP_PATH', ROOT_PATH . '/app');
define('CONFIG_PATH', ROOT_PATH . '/config');
define('PUBLIC_PATH', ROOT_PATH . '/public');

// Include required files
require_once CONFIG_PATH . '/database.php';
require_once CONFIG_PATH . '/config.php';
require_once APP_PATH . '/models/BaseModel.php';
require_once APP_PATH . '/models/User.php';
require_once APP_PATH . '/models/Submission.php';
require_once APP_PATH . '/models/Department.php';
require_once APP_PATH . '/models/ReportModel.php';

// Get the current request path and clean it
$requestPath = getCurrentPath();

// Route the request to appropriate handler
routeRequest($requestPath);

/**
 * Get the current request path from URL
 * Removes query parameters and cleans the path
 * 
 * @return string Clean request path
 */
function getCurrentPath() {
    $path = $_SERVER['REQUEST_URI'];
    
    // Remove the application base path
    $path = str_replace('/JanataConnect', '', $path);
    
    // Remove query parameters (everything after ?)
    $path = parse_url($path, PHP_URL_PATH);
    
    // Remove trailing slash and ensure we have a path
    $path = rtrim($path, '/');
    $path = $path ?: '/';
    
    return $path;
}

/**
 * Main routing function - determines which handler to call
 * 
 * @param string $path The request path
 */
function routeRequest($path) {
    // Check if it's a public route (no login required)
    if (isPublicRoute($path)) {
        handlePublicRoute($path);
        return;
    }
    
    // Check if it's a protected route (login required)
    if (isProtectedRoute($path)) {
        handleProtectedRoute($path);
        return;
    }
    
    // Check if it's a dynamic route (with parameters like IDs)
    if (isDynamicRoute($path)) {
        handleDynamicRoute($path);
        return;
    }
    
    // If no route matches, show 404 page
    show404Page();
}

/**
 * Check if the path is a public route (no authentication required)
 * 
 * @param string $path The request path
 * @return bool True if public route
 */
function isPublicRoute($path) {
    $publicRoutes = [
        '/',
        '/login',
        '/register',
        '/logout'
    ];
    
    return in_array($path, $publicRoutes);
}

/**
 * Check if the path is a protected route (authentication required)
 * 
 * @param string $path The request path
 * @return bool True if protected route
 */
function isProtectedRoute($path) {
    $protectedRoutes = [
        '/dashboard',
        '/submit-suggestion',
        '/my-submissions',
        '/admin',
        '/admin/users',
        '/admin/departments',
        '/admin/submissions',
        '/admin/user/toggle',
        '/admin/department/toggle',
        '/admin/department/delete',
        '/official',
        '/official/submissions',
        '/reports',
        '/reports/submission-status',
        '/reports/department-wise',
        '/reports/monthly-trend',
        '/reports/comprehensive',
        '/reports/export-data'
    ];
    
    return in_array($path, $protectedRoutes);
}

/**
 * Check if the path is a dynamic route (contains parameters)
 * 
 * @param string $path The request path
 * @return bool True if dynamic route
 */
function isDynamicRoute($path) {
    $dynamicPatterns = [
        '/submission/edit/',
        '/submission/update/',
        '/submission/delete/',
        '/official/submission/update/',
        '/admin/user/toggle/',
        '/admin/department/toggle/',
        '/admin/department/delete/'
    ];
    
    foreach ($dynamicPatterns as $pattern) {
        if (strpos($path, $pattern) === 0) {
            return true;
        }
    }
    
    return false;
}

/**
 * Handle public routes (no authentication required)
 * 
 * @param string $path The request path
 */
function handlePublicRoute($path) {
    switch ($path) {
        case '/':
            showHomePage();
            break;
            
        case '/login':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                handleLogin();
            } else {
                showLoginPage();
            }
            break;
            
        case '/register':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                handleRegistration();
            } else {
                showRegistrationPage();
            }
            break;
            
        case '/logout':
            handleLogout();
            break;
            
        default:
            show404Page();
            break;
    }
}

/**
 * Handle protected routes (authentication required)
 * 
 * @param string $path The request path
 */
function handleProtectedRoute($path) {
    // Check if user is logged in
    if (!Config::isLoggedIn()) {
        redirectToLogin();
        return;
    }
    
    // Handle dynamic routes with parameters
    if (preg_match('/\/admin\/user\/toggle\/(\d+)/', $path, $matches)) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            handleToggleUserStatus();
        }
        return;
    }
    
    if (preg_match('/\/admin\/department\/toggle\/(\d+)/', $path, $matches)) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            handleToggleDepartmentStatus();
        }
        return;
    }
    
    switch ($path) {
        case '/dashboard':
            showDashboard();
            break;
            
        case '/submit-suggestion':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                handleSubmitSuggestion();
            } else {
                showSubmitSuggestionPage();
            }
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
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                handleCreateDepartment();
            } else {
                showAdminDepartments();
            }
            break;
            
        case '/admin/submissions':
            showAdminSubmissions();
            break;
            
        case '/admin/user/toggle':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                handleToggleUserStatus();
            }
            break;
            
        case '/admin/department/toggle':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                handleToggleDepartmentStatus();
            }
            break;
            
        case '/admin/department/delete':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                handleDeleteDepartment();
            }
            break;
            
        case '/official':
            showOfficialDashboard();
            break;
            
        case '/official/submissions':
            showOfficialSubmissions();
            break;
            
        case '/reports':
            showReportsDashboard();
            break;
            
        case '/reports/submission-status':
            showSubmissionStatusReport();
            break;
            
        case '/reports/department-wise':
            showDepartmentWiseReport();
            break;
            
            
        case '/reports/monthly-trend':
            showMonthlyTrendReport();
            break;
            
        case '/reports/comprehensive':
            showComprehensiveReport();
            break;
            
        case '/reports/export-data':
            handleReportExport();
            break;
            
        default:
            show404Page();
            break;
    }
}

/**
 * Handle dynamic routes (with parameters like IDs)
 * 
 * @param string $path The request path
 */
function handleDynamicRoute($path) {
    // Check if user is logged in
    if (!Config::isLoggedIn()) {
        redirectToLogin();
        return;
    }
    
    // Extract ID from path
    $pathParts = explode('/', $path);
    $id = end($pathParts);
    
    if (!is_numeric($id)) {
        show404Page();
        return;
    }
    
    // Route based on path pattern
    if (strpos($path, '/submission/edit/') === 0) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            handleUpdateSubmission($id);
        } else {
            showEditSubmissionPage($id);
        }
    } elseif (strpos($path, '/submission/delete/') === 0) {
        handleDeleteSubmission($id);
    } elseif (strpos($path, '/official/submission/update/') === 0) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            handleUpdateSubmissionStatus($id);
        } else {
            showUpdateStatusPage($id);
        }
    } elseif (strpos($path, '/admin/user/toggle/') === 0) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            handleToggleUserStatus();
        } else {
            show404Page();
        }
    } elseif (strpos($path, '/admin/department/toggle/') === 0) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            handleToggleDepartmentStatus();
        } else {
            show404Page();
        }
    } elseif (strpos($path, '/admin/department/delete/') === 0) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            handleDeleteDepartment();
        } else {
            show404Page();
        }
    } else {
        show404Page();
    }
}

// ============================================================================
// PAGE HANDLERS - These functions display the actual pages
// ============================================================================

/**
 * Show the home page
 */
function showHomePage() {
    $title = 'Welcome - ' . Config::APP_NAME;
    
    // Get recent submissions for display
    $submissionModel = new Submission();
    $recentSubmissions = $submissionModel->getRecentSubmissions(5);
    
    include APP_PATH . '/views/home/index.php';
}

/**
 * Show the login page
 */
function showLoginPage() {
    $title = 'Login - ' . Config::APP_NAME;
    $csrf_token = Config::generateCSRFToken();
    include APP_PATH . '/views/auth/login.php';
}

/**
 * Show the registration page
 */
function showRegistrationPage() {
    $title = 'Register - ' . Config::APP_NAME;
    $csrf_token = Config::generateCSRFToken();
    include APP_PATH . '/views/auth/register.php';
}

/**
 * Show the dashboard (role-based)
 */
function showDashboard() {
    $userRole = $_SESSION['user_role'];
    $title = 'Dashboard - ' . Config::APP_NAME;
    
    // Get data based on user role
    $submissionModel = new Submission();
    $userModel = new User();
    $departmentModel = new Department();
    
    if ($userRole === 'admin') {
        // Admin dashboard data
        $statistics = $submissionModel->getStatistics();
        $recent_submissions = $submissionModel->getRecentSubmissions(10);
        $total_users = $userModel->count();
        $total_departments = $departmentModel->count(['is_active' => 1]); // Only count active departments
    } elseif ($userRole === 'official') {
        // Official dashboard data
        $departmentId = $_SESSION['department_id'];
        $statistics = $submissionModel->getStatistics();
        $department_submissions = $submissionModel->getSubmissionsByDepartment($departmentId, 10);
    } else {
        // Citizen dashboard data
        $userId = $_SESSION['user_id'];
        $user_submissions = $submissionModel->getSubmissionsByUser($userId);
        $recent_submissions = array_slice($user_submissions, 0, 5);
        $total_submissions = count($user_submissions);
    }
    
    include APP_PATH . '/views/dashboard/index.php';
}

/**
 * Show the submit suggestion page
 */
function showSubmitSuggestionPage($error = null) {
    $title = 'Submit Suggestion - ' . Config::APP_NAME;
    $csrf_token = Config::generateCSRFToken();
    
    // Get all active departments for dropdown
    $departmentModel = new Department();
    $departments = $departmentModel->getActiveDepartments();
    
    include APP_PATH . '/views/submissions/create.php';
}

/**
 * Show user's submissions
 */
function showMySubmissions() {
    $title = 'My Submissions - ' . Config::APP_NAME;
    $userId = $_SESSION['user_id'];
    
    // Get user's submissions
    $submissionModel = new Submission();
    $submissions = $submissionModel->getSubmissionsByUser($userId);
    
    include APP_PATH . '/views/submissions/index.php';
}

/**
 * Show admin dashboard
 */
function showAdminDashboard() {
    if ($_SESSION['user_role'] !== 'admin') {
        redirectToLogin();
        return;
    }
    
    $title = 'Admin Dashboard - ' . Config::APP_NAME;
    
    // Get admin statistics
    $submissionModel = new Submission();
    $userModel = new User();
    $departmentModel = new Department();
    
    $statistics = $submissionModel->getStatistics();
    $total_users = $userModel->count();
    $total_departments = $departmentModel->count(['is_active' => 1]); // Only count active departments
    $recent_submissions = $submissionModel->getRecentSubmissions(10);
    
    include APP_PATH . '/views/admin/index.php';
}

/**
 * Show admin users page
 */
function showAdminUsers() {
    if ($_SESSION['user_role'] !== 'admin') {
        redirectToLogin();
        return;
    }
    
    $title = 'Manage Users - ' . Config::APP_NAME;
    
    // Get all users
    $userModel = new User();
    $users = $userModel->findAll();
    
    // Generate CSRF token for the form
    $csrf_token = Config::generateCSRFToken();
    
    include APP_PATH . '/views/admin/users.php';
}

/**
 * Show admin departments page
 */
function showAdminDepartments() {
    if ($_SESSION['user_role'] !== 'admin') {
        redirectToLogin();
        return;
    }
    
    $title = 'Manage Departments - ' . Config::APP_NAME;
    
    // Get all departments with statistics
    $departmentModel = new Department();
    $departments = $departmentModel->getAllDepartmentsWithStats();
    
    // Generate CSRF token for the form
    $csrf_token = Config::generateCSRFToken();
    
    // Handle success/error messages
    if (isset($_GET['deleted']) && $_GET['deleted'] == '1') {
        $success = 'Department deleted successfully.';
    }
    
    include APP_PATH . '/views/admin/departments.php';
}

/**
 * Show admin submissions page
 */
function showAdminSubmissions() {
    if ($_SESSION['user_role'] !== 'admin') {
        redirectToLogin();
        return;
    }
    
    $title = 'All Submissions - ' . Config::APP_NAME;
    
    // Get all submissions with user and department details
    $submissionModel = new Submission();
    $submissions = $submissionModel->getAllSubmissionsWithDetails();
    
    include APP_PATH . '/views/admin/submissions.php';
}

/**
 * Handle create department
 */
function handleCreateDepartment() {
    if ($_SESSION['user_role'] !== 'admin') {
        redirectToLogin();
        return;
    }
    
    $name = Config::sanitizeInput($_POST['name'] ?? '');
    $description = Config::sanitizeInput($_POST['description'] ?? '');
    $csrf_token = $_POST['csrf_token'] ?? '';
    
    // Validate CSRF token
    if (!Config::validateCSRFToken($csrf_token)) {
        $error = 'Invalid security token. Please try again.';
        showAdminDepartments();
        return;
    }
    
    // Validate input
    if (empty($name)) {
        $error = 'Department name is required.';
        showAdminDepartments();
        return;
    }
    
    // Create department
    $departmentModel = new Department();
    if ($departmentModel->create(['name' => $name, 'description' => $description])) {
        $success = 'Department created successfully.';
        showAdminDepartments();
        return;
    } else {
        $error = 'Failed to create department. Please try again.';
        showAdminDepartments();
        return;
    }
}

/**
 * Handle toggle user status
 */
function handleToggleUserStatus() {
    if ($_SESSION['user_role'] !== 'admin') {
        redirectToLogin();
        return;
    }
    
    // Extract user ID from URL path
    $path = $_SERVER['REQUEST_URI'];
    preg_match('/\/admin\/user\/toggle\/(\d+)/', $path, $matches);
    $userId = (int)($matches[1] ?? 0);
    
    $isActive = $_POST['is_active'] === 'true';
    $csrf_token = $_POST['csrf_token'] ?? '';
    
    // Validate CSRF token
    if (!Config::validateCSRFToken($csrf_token)) {
        $error = 'Invalid security token. Please try again.';
        showAdminUsers();
        return;
    }
    
    // Update user status (convert boolean to integer for MySQL)
    $userModel = new User();
    if ($userModel->update($userId, ['is_active' => $isActive ? 1 : 0])) {
        header('Location: /JanataConnect/admin/users?updated=1');
        exit;
    } else {
        $error = 'Failed to update user status. Please try again.';
        showAdminUsers();
        return;
    }
}

/**
 * Handle toggle department status
 */
function handleToggleDepartmentStatus() {
    if ($_SESSION['user_role'] !== 'admin') {
        redirectToLogin();
        return;
    }
    
    // Extract department ID from URL path
    $path = $_SERVER['REQUEST_URI'];
    preg_match('/\/admin\/department\/toggle\/(\d+)/', $path, $matches);
    $departmentId = (int)($matches[1] ?? 0);
    
    $isActive = $_POST['is_active'] === 'true';
    $csrf_token = $_POST['csrf_token'] ?? '';
    
    // Validate CSRF token
    if (!Config::validateCSRFToken($csrf_token)) {
        $error = 'Invalid security token. Please try again.';
        showAdminDepartments();
        return;
    }
    
    // Update department status (convert boolean to integer for MySQL)
    $departmentModel = new Department();
    if ($departmentModel->update($departmentId, ['is_active' => $isActive ? 1 : 0])) {
        header('Location: /JanataConnect/admin/departments?updated=1');
        exit;
    } else {
        $error = 'Failed to update department status. Please try again.';
        showAdminDepartments();
        return;
    }
}

/**
 * Handle delete department
 */
function handleDeleteDepartment() {
    if ($_SESSION['user_role'] !== 'admin') {
        redirectToLogin();
        return;
    }
    
    // Extract department ID from URL path
    $path = $_SERVER['REQUEST_URI'];
    preg_match('/\/admin\/department\/delete\/(\d+)/', $path, $matches);
    $departmentId = (int)($matches[1] ?? 0);
    
    $csrf_token = $_POST['csrf_token'] ?? '';
    
    // Validate CSRF token
    if (!Config::validateCSRFToken($csrf_token)) {
        $error = 'Invalid security token. Please try again.';
        showAdminDepartments();
        return;
    }
    
    // Check if department has submissions
    $submissionModel = new Submission();
    $submissions = $submissionModel->getSubmissionsByDepartment($departmentId);
    
    if (!empty($submissions)) {
        $error = 'Cannot delete department. It has active submissions. Please reassign or delete submissions first.';
        showAdminDepartments();
        return;
    }
    
    // Delete department
    $departmentModel = new Department();
    if ($departmentModel->delete($departmentId)) {
        header('Location: /JanataConnect/admin/departments?deleted=1');
        exit;
    } else {
        $error = 'Failed to delete department. Please try again.';
        showAdminDepartments();
        return;
    }
}

/**
 * Show official dashboard
 */
function showOfficialDashboard() {
    if ($_SESSION['user_role'] !== 'official') {
        redirectToLogin();
        return;
    }
    
    $title = 'Official Dashboard - ' . Config::APP_NAME;
    $departmentId = $_SESSION['department_id'];
    
    // Get department submissions
    $submissionModel = new Submission();
    $submissions = $submissionModel->getSubmissionsByDepartment($departmentId, 10);
    $stats = $submissionModel->getStatistics();
    
    include APP_PATH . '/views/official/index.php';
}

/**
 * Show official submissions page
 */
function showOfficialSubmissions() {
    if ($_SESSION['user_role'] !== 'official') {
        redirectToLogin();
        return;
    }
    
    $title = 'Review Submissions - ' . Config::APP_NAME;
    
    // Get ALL submissions for officials (not just their department)
    $submissionModel = new Submission();
    $submissions = $submissionModel->getAllSubmissions();
    
    // Generate CSRF token for the form
    $csrf_token = Config::generateCSRFToken();
    
    include APP_PATH . '/views/official/submissions.php';
}

/**
 * Show edit submission page
 */
function showEditSubmissionPage($submissionId) {
    $title = 'Edit Submission - ' . Config::APP_NAME;
    
    // Get submission details
    $submissionModel = new Submission();
    $submission = $submissionModel->getSubmissionWithDetails($submissionId);
    
    // Check if user owns this submission
    if (!$submission || $submission['user_id'] != $_SESSION['user_id']) {
        show404Page();
        return;
    }
    
    // Get departments for dropdown
    $departmentModel = new Department();
    $departments = $departmentModel->getActiveDepartments();
    
    include APP_PATH . '/views/submissions/edit.php';
}

/**
 * Show update status page for officials
 */
function showUpdateStatusPage($submissionId) {
    if ($_SESSION['user_role'] !== 'official') {
        redirectToLogin();
        return;
    }
    
    $title = 'Update Status - ' . Config::APP_NAME;
    
    // Get submission details
    $submissionModel = new Submission();
    $submission = $submissionModel->getSubmissionWithDetails($submissionId);
    
    if (!$submission) {
        show404Page();
        return;
    }
    
    include APP_PATH . '/views/official/update_status.php';
}

/**
 * Show 404 error page
 */
function show404Page() {
    $title = '404 - Page Not Found';
    include APP_PATH . '/views/shared/404.php';
}

// ============================================================================
// ACTION HANDLERS - These functions process form submissions and actions
// ============================================================================

/**
 * Handle user login
 */
function handleLogin() {
    $email = Config::sanitizeInput($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $csrf_token = $_POST['csrf_token'] ?? '';
    
    // Validate CSRF token
    if (!Config::validateCSRFToken($csrf_token)) {
        $error = 'Invalid security token. Please try again.';
        showLoginPage();
        return;
    }
    
    // Validate input
    if (empty($email) || empty($password)) {
        $error = 'Please fill in all fields.';
        showLoginPage();
        return;
    }
    
    // Authenticate user
    $userModel = new User();
    $user = $userModel->authenticate($email, $password);
    
    if ($user) {
        // Set session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['department_id'] = $user['department_id'];
        
        // Redirect to dashboard
        header('Location: /JanataConnect/dashboard');
        exit;
    } else {
        $error = 'Invalid email or password.';
        showLoginPage();
        return;
    }
}

/**
 * Handle user registration
 */
function handleRegistration() {
    $name = Config::sanitizeInput($_POST['name'] ?? '');
    $email = Config::sanitizeInput($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $phone = Config::sanitizeInput($_POST['phone'] ?? '');
    $address = Config::sanitizeInput($_POST['address'] ?? '');
    $csrf_token = $_POST['csrf_token'] ?? '';
    
    // Validate CSRF token
    if (!Config::validateCSRFToken($csrf_token)) {
        $error = 'Invalid security token. Please try again.';
        showRegistrationPage();
        return;
    }
    
    // Validate input
    if (empty($name) || empty($email) || empty($password)) {
        $error = 'Please fill in all required fields.';
        showRegistrationPage();
        return;
    }
    
    if ($password !== $confirm_password) {
        $error = 'Passwords do not match.';
        showRegistrationPage();
        return;
    }
    
    if (strlen($password) < Config::PASSWORD_MIN_LENGTH) {
        $error = 'Password must be at least ' . Config::PASSWORD_MIN_LENGTH . ' characters long.';
        showRegistrationPage();
        return;
    }
    
    // Check if email already exists
    $userModel = new User();
    if ($userModel->findByEmail($email)) {
        $error = 'Email already exists. Please use a different email.';
        showRegistrationPage();
        return;
    }
    
    // Create user
    $userData = [
        'name' => $name,
        'email' => $email,
        'password' => $password,
        'phone' => $phone,
        'address' => $address,
        'role' => 'citizen'
    ];
    
    $userId = $userModel->createUser($userData);
    
    if ($userId) {
        // Set session variables
        $_SESSION['user_id'] = $userId;
        $_SESSION['user_name'] = $name;
        $_SESSION['user_email'] = $email;
        $_SESSION['user_role'] = 'citizen';
        $_SESSION['department_id'] = null;
        
        // Redirect to dashboard
        header('Location: /JanataConnect/dashboard');
        exit;
    } else {
        $error = 'Registration failed. Please try again.';
        showRegistrationPage();
        return;
    }
}

/**
 * Handle user logout
 */
function handleLogout() {
    // Destroy session
    session_destroy();
    
    // Redirect to home page
    header('Location: /JanataConnect/');
    exit;
}

/**
 * Handle submit suggestion form
 */
function handleSubmitSuggestion() {
    $title = Config::sanitizeInput($_POST['title'] ?? '');
    $description = Config::sanitizeInput($_POST['description'] ?? '');
    $department_id = (int)($_POST['department_id'] ?? 0);
    $location = Config::sanitizeInput($_POST['location'] ?? '');
    $priority = Config::sanitizeInput($_POST['priority'] ?? 'medium');
    $csrf_token = $_POST['csrf_token'] ?? '';
    
    // Validate CSRF token
    if (!Config::validateCSRFToken($csrf_token)) {
        $error = 'Invalid security token. Please try again.';
        showSubmitSuggestionPage($error);
        return;
    }
    
    // Validate input
    if (empty($title) || empty($description) || empty($department_id)) {
        $error = 'Please fill in all required fields.';
        showSubmitSuggestionPage($error);
        return;
    }
    
    // Create submission
    $submissionModel = new Submission();
    $submissionData = [
        'user_id' => $_SESSION['user_id'],
        'title' => $title,
        'description' => $description,
        'department_id' => $department_id,
        'location' => $location,
        'priority' => $priority,
        'status' => 'pending'
    ];
    
    $submissionId = $submissionModel->create($submissionData);
    
    if ($submissionId) {
        // Handle file uploads if any
        if (!empty($_FILES['images']['name'][0])) {
            $uploadResult = handleFileUploads($submissionId, $_FILES['images']);
            if (!$uploadResult['success']) {
                $error = 'Submission created but file upload failed: ' . $uploadResult['message'];
                showSubmitSuggestionPage($error);
                return;
            }
        }
        
        // Success - redirect to submissions page
        header('Location: /JanataConnect/my-submissions?success=1');
        exit;
    } else {
        $error = 'Failed to submit suggestion. Please try again.';
        showSubmitSuggestionPage($error);
    }
}

/**
 * Handle update submission
 */
function handleUpdateSubmission($submissionId) {
    $title = Config::sanitizeInput($_POST['title'] ?? '');
    $description = Config::sanitizeInput($_POST['description'] ?? '');
    $department_id = (int)($_POST['department_id'] ?? 0);
    $location = Config::sanitizeInput($_POST['location'] ?? '');
    $csrf_token = $_POST['csrf_token'] ?? '';
    
    // Validate CSRF token
    if (!Config::validateCSRFToken($csrf_token)) {
        $error = 'Invalid security token. Please try again.';
        showEditSubmissionPage($submissionId);
        return;
    }
    
    // Validate input
    if (empty($title) || empty($description) || empty($department_id)) {
        $error = 'Please fill in all required fields.';
        showEditSubmissionPage($submissionId);
        return;
    }
    
    // Update submission
    $submissionModel = new Submission();
    $submissionData = [
        'title' => $title,
        'description' => $description,
        'department_id' => $department_id,
        'location' => $location
    ];
    
    if ($submissionModel->update($submissionId, $submissionData)) {
        header('Location: /JanataConnect/my-submissions?updated=1');
        exit;
    } else {
        $error = 'Failed to update submission. Please try again.';
        showEditSubmissionPage($submissionId);
        return;
    }
}

/**
 * Handle delete submission
 */
function handleDeleteSubmission($submissionId) {
    $csrf_token = $_POST['csrf_token'] ?? '';
    
    // Validate CSRF token
    if (!Config::validateCSRFToken($csrf_token)) {
        $error = 'Invalid security token. Please try again.';
        showMySubmissions();
        return;
    }
    
    // Delete submission
    $submissionModel = new Submission();
    if ($submissionModel->delete($submissionId)) {
        header('Location: /JanataConnect/my-submissions?deleted=1');
        exit;
    } else {
        $error = 'Failed to delete submission. Please try again.';
        showMySubmissions();
        return;
    }
}

/**
 * Handle update submission status (for officials)
 */
function handleUpdateSubmissionStatus($submissionId) {
    $status = Config::sanitizeInput($_POST['status'] ?? '');
    $comment = Config::sanitizeInput($_POST['comment'] ?? '');
    $csrf_token = $_POST['csrf_token'] ?? '';
    
    // Validate CSRF token
    if (!Config::validateCSRFToken($csrf_token)) {
        $error = 'Invalid security token. Please try again.';
        showOfficialSubmissions();
        return;
    }
    
    // Validate input
    if (empty($status)) {
        $error = 'Please select a status.';
        showOfficialSubmissions();
        return;
    }
    
    // Update submission status
    $submissionModel = new Submission();
    if ($submissionModel->updateStatus($submissionId, $status, $comment)) {
        header('Location: /JanataConnect/official/submissions?updated=1');
        exit;
    } else {
        $error = 'Failed to update status. Please try again.';
        showOfficialSubmissions();
        return;
    }
}

// ============================================================================
// HELPER FUNCTIONS - Utility functions used throughout the application
// ============================================================================

/**
 * Redirect to login page
 */
function redirectToLogin() {
    header('Location: /JanataConnect/login');
    exit;
}

/**
 * Handle file uploads for submissions
 * 
 * @param int $submissionId The submission ID
 * @param array $files The uploaded files
 * @return array Result with success status and message
 */
function handleFileUploads($submissionId, $files) {
    $uploadDir = PUBLIC_PATH . '/uploads/submissions/';
    
    // Create directory if it doesn't exist
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    $uploadedFiles = [];
    $errors = [];
    
    // Process each file
    for ($i = 0; $i < count($files['name']); $i++) {
        if ($files['error'][$i] === UPLOAD_ERR_OK) {
            $fileName = $files['name'][$i];
            $fileSize = $files['size'][$i];
            $fileType = $files['type'][$i];
            $fileTmpName = $files['tmp_name'][$i];
            
            // Validate file
            $validation = validateUploadedFile($fileName, $fileSize, $fileType);
            if (!$validation['valid']) {
                $errors[] = $validation['message'];
                continue;
            }
            
            // Generate unique filename
            $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
            $uniqueFileName = $submissionId . '_' . time() . '_' . $i . '.' . $fileExtension;
            $filePath = $uploadDir . $uniqueFileName;
            
            // Move uploaded file
            if (move_uploaded_file($fileTmpName, $filePath)) {
                // Save file info to database
                $submissionModel = new Submission();
                $fileData = [
                    'submission_id' => $submissionId,
                    'file_name' => $fileName,
                    'file_path' => 'public/uploads/submissions/' . $uniqueFileName,
                    'file_type' => $fileType,
                    'file_size' => $fileSize
                ];
                
                $submissionModel->addFileToSubmission($submissionId, $fileData);
                $uploadedFiles[] = $uniqueFileName;
            } else {
                $errors[] = "Failed to upload file '{$fileName}'.";
            }
        }
    }
    
    if (empty($errors)) {
        return ['success' => true, 'message' => 'Files uploaded successfully.'];
    } else {
        return ['success' => false, 'message' => implode(' ', $errors)];
    }
}

/**
 * Validate uploaded file
 * 
 * @param string $fileName The file name
 * @param int $fileSize The file size
 * @param string $fileType The file type
 * @return array Validation result
 */
function validateUploadedFile($fileName, $fileSize, $fileType) {
    // Check file size
    if ($fileSize > Config::MAX_FILE_SIZE) {
        return [
            'valid' => false,
            'message' => "File '{$fileName}' is too large. Maximum size is " . (Config::MAX_FILE_SIZE / 1024 / 1024) . "MB."
        ];
    }
    
    // Check file type
    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    if (!in_array($fileExtension, Config::ALLOWED_FILE_TYPES)) {
        return [
            'valid' => false,
            'message' => "File '{$fileName}' has an invalid type. Allowed types: " . implode(', ', Config::ALLOWED_FILE_TYPES)
        ];
    }
    
    return ['valid' => true, 'message' => 'File is valid.'];
}

/**
 * Display success message from session
 */
function displaySuccessMessage() {
    if (isset($_GET['success']) && $_GET['success'] == '1') {
        echo '<div class="alert alert-success"><i class="fas fa-check-circle"></i> Suggestion submitted successfully!</div>';
    }
    if (isset($_GET['updated']) && $_GET['updated'] == '1') {
        echo '<div class="alert alert-success"><i class="fas fa-check-circle"></i> Submission updated successfully!</div>';
    }
    if (isset($_GET['deleted']) && $_GET['deleted'] == '1') {
        echo '<div class="alert alert-success"><i class="fas fa-check-circle"></i> Submission deleted successfully!</div>';
    }
}

/**
 * Display error message
 */
function displayErrorMessage($message) {
    if (!empty($message)) {
        echo '<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> ' . htmlspecialchars($message) . '</div>';
    }
}

// ============================================================================
// REPORT HANDLERS - These functions handle report generation
// ============================================================================

/**
 * Show the reports dashboard
 */
function showReportsDashboard() {
    require_once APP_PATH . '/controllers/ReportController.php';
    $reportController = new ReportController();
    $reportController->index();
}

/**
 * Show submission status report
 */
function showSubmissionStatusReport() {
    require_once APP_PATH . '/controllers/ReportController.php';
    $reportController = new ReportController();
    $reportController->submissionStatus();
}

/**
 * Show department-wise report
 */
function showDepartmentWiseReport() {
    require_once APP_PATH . '/controllers/ReportController.php';
    $reportController = new ReportController();
    $reportController->departmentWise();
}


/**
 * Show monthly trend report
 */
function showMonthlyTrendReport() {
    require_once APP_PATH . '/controllers/ReportController.php';
    $reportController = new ReportController();
    
    // Get months parameter from URL, default to 12
    $months = isset($_GET['months']) ? (int)$_GET['months'] : 12;
    $reportController->monthlyTrend($months);
}

/**
 * Show comprehensive report (Admin only)
 */
function showComprehensiveReport() {
    require_once APP_PATH . '/controllers/ReportController.php';
    $reportController = new ReportController();
    $reportController->comprehensive();
}

/**
 * Handle report data export
 */
function handleReportExport() {
    require_once APP_PATH . '/controllers/ReportController.php';
    $reportController = new ReportController();
    $reportController->exportData();
}

?>