<?php
require_once 'BaseController.php';

class AdminController extends BaseController {
    public function index() {
        Config::requireRole('admin');
        
        $this->data['title'] = 'Admin Dashboard - ' . Config::APP_NAME;
        
        // Get statistics
        $submissionModel = new Submission();
        $userModel = new User();
        $departmentModel = new Department();
        
        $this->data['statistics'] = $submissionModel->getStatistics();
        $this->data['total_users'] = $userModel->count();
        $this->data['total_departments'] = $departmentModel->count();
        $this->data['recent_submissions'] = $submissionModel->getRecentSubmissions(5);
        
        $this->view('admin/index', $this->data);
    }
    
    public function users() {
        Config::requireRole('admin');
        
        $this->data['title'] = 'User Management - ' . Config::APP_NAME;
        
        $userModel = new User();
        $this->data['users'] = $userModel->findAll();
        
        $this->view('admin/users', $this->data);
    }
    
    public function departments() {
        Config::requireRole('admin');
        
        $this->data['title'] = 'Department Management - ' . Config::APP_NAME;
        $this->data['csrf_token'] = Config::generateCSRFToken();
        
        $departmentModel = new Department();
        $this->data['departments'] = $departmentModel->getAllDepartmentsWithStats();
        
        $this->view('admin/departments', $this->data);
    }
    
    public function storeDepartment() {
        Config::requireRole('admin');
        $this->validateCSRF();
        
        $name = $this->sanitizeInput($_POST['name'] ?? '');
        $description = $this->sanitizeInput($_POST['description'] ?? '');
        
        if (empty($name)) {
            $this->data['error'] = 'Department name is required';
            $this->departments();
            return;
        }
        
        $departmentData = [
            'name' => $name,
            'description' => $description,
            'is_active' => 1
        ];
        
        $departmentModel = new Department();
        if ($departmentModel->create($departmentData)) {
            $this->data['success'] = 'Department created successfully!';
        } else {
            $this->data['error'] = 'Failed to create department.';
        }
        
        $this->departments();
    }
    
    public function submissions() {
        Config::requireRole('admin');
        
        $this->data['title'] = 'All Submissions - ' . Config::APP_NAME;
        
        $submissionModel = new Submission();
        $this->data['submissions'] = $submissionModel->getAllSubmissions();
        
        $this->view('admin/submissions', $this->data);
    }
}
?>
