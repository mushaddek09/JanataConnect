<?php
require_once 'BaseController.php';

class AuthController extends BaseController {
    public function login() {
        if (Config::isLoggedIn()) {
            $this->redirect('/dashboard');
        }
        
        $this->data['title'] = 'Login - ' . Config::APP_NAME;
        $this->data['csrf_token'] = Config::generateCSRFToken();
        $this->view('auth/login', $this->data);
    }
    
    public function processLogin() {
        $this->validateCSRF();
        
        $email = $this->sanitizeInput($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        
        if (empty($email) || empty($password)) {
            $this->data['error'] = 'Email and password are required';
            $this->data['title'] = 'Login - ' . Config::APP_NAME;
            $this->data['csrf_token'] = Config::generateCSRFToken();
            $this->view('auth/login', $this->data);
            return;
        }
        
        $userModel = new User();
        $user = $userModel->authenticate($email, $password);
        
        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['department_id'] = $user['department_id'];
            
            // Redirect based on role
            switch ($user['role']) {
                case 'admin':
                    $this->redirect('/admin');
                    break;
                case 'official':
                    $this->redirect('/official');
                    break;
                default:
                    $this->redirect('/dashboard');
            }
        } else {
            $this->data['error'] = 'Invalid email or password';
            $this->data['title'] = 'Login - ' . Config::APP_NAME;
            $this->data['csrf_token'] = Config::generateCSRFToken();
            $this->view('auth/login', $this->data);
        }
    }
    
    public function register() {
        if (Config::isLoggedIn()) {
            $this->redirect('/dashboard');
        }
        
        $this->data['title'] = 'Register - ' . Config::APP_NAME;
        $this->data['csrf_token'] = Config::generateCSRFToken();
        $this->view('auth/register', $this->data);
    }
    
    public function processRegister() {
        $this->validateCSRF();
        
        $name = $this->sanitizeInput($_POST['name'] ?? '');
        $email = $this->sanitizeInput($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        $phone = $this->sanitizeInput($_POST['phone'] ?? '');
        $address = $this->sanitizeInput($_POST['address'] ?? '');
        
        // Validation
        $errors = [];
        
        if (empty($name)) {
            $errors[] = 'Name is required';
        }
        
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Valid email is required';
        }
        
        if (empty($password) || strlen($password) < Config::PASSWORD_MIN_LENGTH) {
            $errors[] = 'Password must be at least ' . Config::PASSWORD_MIN_LENGTH . ' characters';
        }
        
        if ($password !== $confirmPassword) {
            $errors[] = 'Passwords do not match';
        }
        
        if (!empty($errors)) {
            $this->data['errors'] = $errors;
            $this->data['title'] = 'Register - ' . Config::APP_NAME;
            $this->data['csrf_token'] = Config::generateCSRFToken();
            $this->view('auth/register', $this->data);
            return;
        }
        
        $userModel = new User();
        
        // Check if email already exists
        if ($userModel->findByEmail($email)) {
            $this->data['error'] = 'Email already registered';
            $this->data['title'] = 'Register - ' . Config::APP_NAME;
            $this->data['csrf_token'] = Config::generateCSRFToken();
            $this->view('auth/register', $this->data);
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
            $this->data['success'] = 'Registration successful! Please login.';
            $this->data['title'] = 'Login - ' . Config::APP_NAME;
            $this->data['csrf_token'] = Config::generateCSRFToken();
            $this->view('auth/login', $this->data);
        } else {
            $this->data['error'] = 'Registration failed. Please try again.';
            $this->data['title'] = 'Register - ' . Config::APP_NAME;
            $this->data['csrf_token'] = Config::generateCSRFToken();
            $this->view('auth/register', $this->data);
        }
    }
    
    public function logout() {
        session_destroy();
        $this->redirect('/');
    }
}
?>


