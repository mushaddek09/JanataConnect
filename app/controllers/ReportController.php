<?php

require_once 'BaseController.php';
require_once APP_PATH . '/models/ReportModel.php';

class ReportController extends BaseController {
    
    /**
     * Report model instance
     * @var ReportModel
     */
    private $reportModel;
    
    /**
     * Constructor - Initialize the controller
     */
    public function __construct() {
        $this->reportModel = new ReportModel();
    }
    
    /**
     * Show the main reports dashboard
     * This is the main page where users can view different types of reports
     */
    public function index() {
        // Check if user is logged in
        if (!Config::isLoggedIn()) {
            $this->redirect('/login');
            return;
        }
        
        // Only admin and officials can access reports
        $userRole = $_SESSION['user_role'];
        if (!in_array($userRole, ['admin', 'official'])) {
            $this->redirect('/dashboard');
            return;
        }
        
        // Get basic statistics for the dashboard
        $stats = $this->reportModel->getBasicStatistics();
        
        // Get department statistics if user is admin or official
        $departmentStats = [];
        if (in_array($userRole, ['admin', 'official'])) {
            $departmentStats = $this->reportModel->getDepartmentStatistics();
        }
        
        // Get user's own statistics if they are a citizen
        $userStats = [];
        if ($userRole === 'citizen') {
            $userStats = $this->reportModel->getUserStatistics($_SESSION['user_id']);
        }
        
        // Include the view
        include APP_PATH . '/views/reports/index.php';
    }
    
    /**
     * Generate submission status report with pie chart
     * Shows distribution of submissions by status
     */
    public function submissionStatus() {
        // Check if user is logged in
        if (!Config::isLoggedIn()) {
            $this->redirect('/login');
            return;
        }
        
        // Only admin and officials can access reports
        $userRole = $_SESSION['user_role'];
        if (!in_array($userRole, ['admin', 'official'])) {
            $this->redirect('/dashboard');
            return;
        }
        
        $userId = $_SESSION['user_id'];
        
        // Get status statistics
        $statusStats = $this->reportModel->getSubmissionStatusStatistics($userRole, $userId);
        
        // Get detailed breakdown
        $statusBreakdown = $this->reportModel->getStatusBreakdown($userRole, $userId);
        
        // Include the view
        include APP_PATH . '/views/reports/submission_status.php';
    }
    
    /**
     * Generate department-wise report with pie chart
     * Shows distribution of submissions by department
     */
    public function departmentWise() {
        // Check if user is logged in
        if (!Config::isLoggedIn()) {
            $this->redirect('/login');
            return;
        }
        
        // Only admin and officials can view department reports
        $userRole = $_SESSION['user_role'];
        if (!in_array($userRole, ['admin', 'official'])) {
            $this->redirect('/dashboard');
            return;
        }
        
        // Get department statistics
        $departmentStats = $this->reportModel->getDepartmentStatistics();
        
        // Get detailed breakdown
        $departmentBreakdown = $this->reportModel->getDepartmentBreakdown();
        
        // Include the view
        include APP_PATH . '/views/reports/department_wise.php';
    }
    
    
    /**
     * Generate monthly trend report
     * Shows submission trends over time
     */
    public function monthlyTrend($months = 12) {
        // Check if user is logged in
        if (!Config::isLoggedIn()) {
            $this->redirect('/login');
            return;
        }
        
        // Only admin and officials can access reports
        $userRole = $_SESSION['user_role'];
        if (!in_array($userRole, ['admin', 'official'])) {
            $this->redirect('/dashboard');
            return;
        }
        
        $userId = $_SESSION['user_id'];
        
        // Get monthly statistics with specified months
        $monthlyStats = $this->reportModel->getMonthlySubmissionTrend($months);
        
        // Include the view
        include APP_PATH . '/views/reports/monthly_trend.php';
    }
    
    /**
     * Generate comprehensive report (Admin only)
     * Shows all statistics in one comprehensive view
     */
    public function comprehensive() {
        // Check if user is logged in and is admin
        if (!Config::isLoggedIn() || $_SESSION['user_role'] !== 'admin') {
            $this->redirect('/dashboard');
            return;
        }
        
        // Get all statistics
        $allStats = $this->reportModel->getComprehensiveStatistics();
        
        // Include the view
        include APP_PATH . '/views/reports/comprehensive.php';
    }
    
    /**
     * Export report data as JSON (for AJAX requests)
     * This is used by the frontend to get data for charts
     */
    public function exportData() {
        // Check if user is logged in
        if (!Config::isLoggedIn()) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }
        
        // Only admin and officials can access reports
        $userRole = $_SESSION['user_role'];
        if (!in_array($userRole, ['admin', 'official'])) {
            http_response_code(403);
            echo json_encode(['error' => 'Access denied']);
            return;
        }
        
        // Get the report type from URL parameter
        $reportType = $_GET['type'] ?? '';
        $userRole = $_SESSION['user_role'];
        $userId = $_SESSION['user_id'];
        
        // Set content type to JSON
        header('Content-Type: application/json');
        
        try {
            switch ($reportType) {
                case 'status':
                    $data = $this->reportModel->getSubmissionStatusStatistics($userRole, $userId);
                    break;
                    
                case 'department':
                    if (!in_array($userRole, ['admin', 'official'])) {
                        throw new Exception('Access denied');
                    }
                    $data = $this->reportModel->getDepartmentStatistics();
                    break;
                    
                    
                case 'monthly':
                    $data = $this->reportModel->getMonthlyStatistics($userRole, $userId);
                    break;
                    
                default:
                    throw new Exception('Invalid report type');
            }
            
            echo json_encode([
                'success' => true,
                'data' => $data
            ]);
            
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Download report as PDF (Future enhancement)
     * This is a placeholder for PDF generation functionality
     */
    public function downloadPdf() {
        // Check if user is logged in
        if (!Config::isLoggedIn()) {
            $this->redirect('/login');
            return;
        }
        
        // For now, redirect to the report page
        // In the future, this would generate and download a PDF
        $this->redirect('/reports');
    }
}
?>
