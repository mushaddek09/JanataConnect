<?php
require_once 'BaseController.php';

class HomeController extends BaseController {
    public function index() {
        $this->data['title'] = 'Welcome to ' . Config::APP_NAME;
        $this->data['departments'] = Config::getDepartments();
        
        // Get recent submissions for public view
        $submissionModel = new Submission();
        $this->data['recent_submissions'] = $submissionModel->getRecentSubmissions(5);
        
        $this->view('home/index', $this->data);
    }
}
?>
