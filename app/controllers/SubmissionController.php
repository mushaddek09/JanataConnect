<?php
require_once 'BaseController.php';

class SubmissionController extends BaseController {
    public function create() {
        Config::requireRole('citizen');
        
        $this->data['title'] = 'Submit Suggestion - ' . Config::APP_NAME;
        $this->data['csrf_token'] = Config::generateCSRFToken();
        
        $departmentModel = new Department();
        $this->data['departments'] = $departmentModel->getActiveDepartments();
        
        $this->view('submissions/create', $this->data);
    }
    
    public function store() {
        Config::requireRole('citizen');
        $this->validateCSRF();
        
        $title = $this->sanitizeInput($_POST['title'] ?? '');
        $description = $this->sanitizeInput($_POST['description'] ?? '');
        $department_id = (int)($_POST['department_id'] ?? 0);
        $location = $this->sanitizeInput($_POST['location'] ?? '');
        $latitude = $_POST['latitude'] ?? null;
        $longitude = $_POST['longitude'] ?? null;
        
        if (empty($title) || empty($description) || empty($department_id)) {
            $this->data['error'] = 'Title, description, and department are required';
            $this->create();
            return;
        }
        
        $submissionData = [
            'user_id' => $_SESSION['user_id'],
            'department_id' => $department_id,
            'title' => $title,
            'description' => $description,
            'location' => $location,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'status' => Config::STATUS_PENDING
        ];
        
        $submissionModel = new Submission();
        $submissionId = $submissionModel->create($submissionData);
        
        if ($submissionId) {
            $this->data['success'] = 'Suggestion submitted successfully!';
            $this->redirect('/my-submissions');
        } else {
            $this->data['error'] = 'Failed to submit suggestion. Please try again.';
            $this->create();
        }
    }
    
    public function index() {
        Config::requireRole('citizen');
        
        $this->data['title'] = 'My Submissions - ' . Config::APP_NAME;
        
        $submissionModel = new Submission();
        $this->data['submissions'] = $submissionModel->getSubmissionsByUser($_SESSION['user_id']);
        
        $this->view('submissions/index', $this->data);
    }
    
    public function edit() {
        Config::requireRole('citizen');
        
        $id = (int)($_GET['id'] ?? 0);
        if (!$id) {
            $this->redirect('/my-submissions');
        }
        
        $submissionModel = new Submission();
        $submission = $submissionModel->find($id);
        
        if (!$submission || $submission['user_id'] != $_SESSION['user_id']) {
            $this->redirect('/my-submissions');
        }
        
        $this->data['title'] = 'Edit Submission - ' . Config::APP_NAME;
        $this->data['submission'] = $submission;
        $this->data['csrf_token'] = Config::generateCSRFToken();
        
        $departmentModel = new Department();
        $this->data['departments'] = $departmentModel->getActiveDepartments();
        
        $this->view('submissions/edit', $this->data);
    }
    
    public function update() {
        Config::requireRole('citizen');
        $this->validateCSRF();
        
        $id = (int)($_GET['id'] ?? 0);
        if (!$id) {
            $this->redirect('/my-submissions');
        }
        
        $submissionModel = new Submission();
        $submission = $submissionModel->find($id);
        
        if (!$submission || $submission['user_id'] != $_SESSION['user_id']) {
            $this->redirect('/my-submissions');
        }
        
        $title = $this->sanitizeInput($_POST['title'] ?? '');
        $description = $this->sanitizeInput($_POST['description'] ?? '');
        $department_id = (int)($_POST['department_id'] ?? 0);
        $location = $this->sanitizeInput($_POST['location'] ?? '');
        
        if (empty($title) || empty($description) || empty($department_id)) {
            $this->data['error'] = 'Title, description, and department are required';
            $this->edit();
            return;
        }
        
        $updateData = [
            'title' => $title,
            'description' => $description,
            'department_id' => $department_id,
            'location' => $location
        ];
        
        if ($submissionModel->update($id, $updateData)) {
            $this->data['success'] = 'Submission updated successfully!';
            $this->redirect('/my-submissions');
        } else {
            $this->data['error'] = 'Failed to update submission. Please try again.';
            $this->edit();
        }
    }
    
    public function delete() {
        Config::requireRole('citizen');
        $this->validateCSRF();
        
        $id = (int)($_GET['id'] ?? 0);
        if (!$id) {
            $this->redirect('/my-submissions');
        }
        
        $submissionModel = new Submission();
        $submission = $submissionModel->find($id);
        
        if (!$submission || $submission['user_id'] != $_SESSION['user_id']) {
            $this->redirect('/my-submissions');
        }
        
        if ($submissionModel->delete($id)) {
            $this->data['success'] = 'Submission deleted successfully!';
        } else {
            $this->data['error'] = 'Failed to delete submission.';
        }
        
        $this->redirect('/my-submissions');
    }
}
?>
