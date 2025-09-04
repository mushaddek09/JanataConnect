<?php
require_once 'BaseController.php';

class OfficialController extends BaseController {
    public function index() {
        Config::requireRole('official');
        
        $this->data['title'] = 'Official Dashboard - ' . Config::APP_NAME;
        
        $submissionModel = new Submission();
        $this->data['department_submissions'] = $submissionModel->getSubmissionsByDepartment($_SESSION['department_id'], 10);
        
        $this->view('official/index', $this->data);
    }
    
    public function submissions() {
        Config::requireRole('official');
        
        $this->data['title'] = 'Review Submissions - ' . Config::APP_NAME;
        $this->data['csrf_token'] = Config::generateCSRFToken();
        
        $submissionModel = new Submission();
        $this->data['submissions'] = $submissionModel->getSubmissionsByDepartment($_SESSION['department_id']);
        
        $this->view('official/submissions', $this->data);
    }
    
    public function updateSubmission() {
        Config::requireRole('official');
        $this->validateCSRF();
        
        $id = (int)($_GET['id'] ?? 0);
        if (!$id) {
            $this->redirect('/official/submissions');
        }
        
        $status = $this->sanitizeInput($_POST['status'] ?? '');
        $comment = $this->sanitizeInput($_POST['comment'] ?? '');
        
        if (!in_array($status, ['pending', 'under_review', 'approved', 'rejected', 'completed'])) {
            $this->redirect('/official/submissions');
        }
        
        $submissionModel = new Submission();
        if ($submissionModel->updateStatus($id, $status, $comment)) {
            $this->data['success'] = 'Submission status updated successfully!';
        } else {
            $this->data['error'] = 'Failed to update submission status.';
        }
        
        $this->redirect('/official/submissions');
    }
}
?>
