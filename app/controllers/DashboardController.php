<?php
require_once 'BaseController.php';

class DashboardController extends BaseController {
    public function index() {
        Config::requireLogin();
        
        $this->data['title'] = 'Dashboard - ' . Config::APP_NAME;
        $this->data['user_role'] = $_SESSION['user_role'];
        $this->data['user_name'] = $_SESSION['user_name'];
        
        // Get statistics based on user role
        if ($_SESSION['user_role'] === 'citizen') {
            $submissionModel = new Submission();
            $this->data['user_submissions'] = $submissionModel->getSubmissionsByUser($_SESSION['user_id'], 5);
            $this->data['total_submissions'] = $submissionModel->count(['user_id' => $_SESSION['user_id']]);
        } elseif ($_SESSION['user_role'] === 'official') {
            $submissionModel = new Submission();
            $this->data['department_submissions'] = $submissionModel->getSubmissionsByDepartment($_SESSION['department_id'], 5);
        } elseif ($_SESSION['user_role'] === 'admin') {
            $submissionModel = new Submission();
            $this->data['statistics'] = $submissionModel->getStatistics();
            $this->data['recent_submissions'] = $submissionModel->getRecentSubmissions(5);
        }
        
        $this->view('dashboard/index', $this->data);
    }
}
?>
