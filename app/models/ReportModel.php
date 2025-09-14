<?php

require_once 'BaseModel.php';

class ReportModel extends BaseModel {
    
    /**
     * Table name for submissions
     * @var string
     */
    protected $table = 'submissions';
    
    // ============================================================================
    // BASIC STATISTICS METHODS
    // ============================================================================
    
    /**
     * Get basic statistics for the dashboard
     * 
     * @return array Basic statistics
     */
    public function getBasicStatistics() {
        $query = "SELECT 
                    COUNT(*) as total_submissions,
                    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                    SUM(CASE WHEN status = 'under_review' THEN 1 ELSE 0 END) as under_review,
                    SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved,
                    SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected,
                    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
                    SUM(CASE WHEN priority = 'high' THEN 1 ELSE 0 END) as `high_priority`,
                    SUM(CASE WHEN priority = 'medium' THEN 1 ELSE 0 END) as `medium_priority`,
                    SUM(CASE WHEN priority = 'low' THEN 1 ELSE 0 END) as `low_priority`
                  FROM {$this->table}";
        
        $stmt = $this->db->prepare($query);
        
        if (!$stmt) {
            throw new Exception('Database query preparation failed: ' . $this->db->error);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc();
    }
    
    /**
     * Get department statistics
     * 
     * @return array Department statistics
     */
    public function getDepartmentStatistics() {
        $query = "SELECT 
                    d.name as department_name,
                    COUNT(s.id) as total_submissions,
                    SUM(CASE WHEN s.status = 'pending' THEN 1 ELSE 0 END) as pending,
                    SUM(CASE WHEN s.status = 'under_review' THEN 1 ELSE 0 END) as under_review,
                    SUM(CASE WHEN s.status = 'approved' THEN 1 ELSE 0 END) as approved,
                    SUM(CASE WHEN s.status = 'rejected' THEN 1 ELSE 0 END) as rejected,
                    SUM(CASE WHEN s.status = 'completed' THEN 1 ELSE 0 END) as completed
                  FROM departments d 
                  LEFT JOIN {$this->table} s ON d.id = s.department_id 
                  GROUP BY d.id, d.name 
                  ORDER BY total_submissions DESC";
        
        $stmt = $this->db->prepare($query);
        
        if (!$stmt) {
            throw new Exception('Database query preparation failed: ' . $this->db->error);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * Get user statistics
     * 
     * @param int $userId The user's ID
     * @return array User statistics
     */
    public function getUserStatistics($userId) {
        $query = "SELECT 
                    COUNT(*) as total_submissions,
                    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                    SUM(CASE WHEN status = 'under_review' THEN 1 ELSE 0 END) as under_review,
                    SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved,
                    SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected,
                    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
                    SUM(CASE WHEN priority = 'high' THEN 1 ELSE 0 END) as `high_priority`,
                    SUM(CASE WHEN priority = 'medium' THEN 1 ELSE 0 END) as `medium_priority`,
                    SUM(CASE WHEN priority = 'low' THEN 1 ELSE 0 END) as `low_priority`
                  FROM {$this->table} 
                  WHERE user_id = ?";
        
        $stmt = $this->db->prepare($query);
        
        if (!$stmt) {
            throw new Exception('Database query preparation failed: ' . $this->db->error);
        }
        
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc();
    }
    
    // ============================================================================
    // SUBMISSION STATUS METHODS
    // ============================================================================
    
    /**
     * Get submission status statistics for charts
     * 
     * @param string $userRole User's role
     * @param int $userId User's ID
     * @return array Status statistics formatted for charts
     */
    public function getSubmissionStatusStatistics($userRole, $userId) {
        // Build query based on user role
        if ($userRole === 'citizen') {
            $query = "SELECT 
                        status,
                        COUNT(*) as count
                      FROM {$this->table} 
                      WHERE user_id = ? 
                      GROUP BY status 
                      ORDER BY count DESC";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("i", $userId);
        } else {
            $query = "SELECT 
                        status,
                        COUNT(*) as count
                      FROM {$this->table} 
                      GROUP BY status 
                      ORDER BY count DESC";
            $stmt = $this->db->prepare($query);
        }
        
        if (!$stmt) {
            throw new Exception('Database query preparation failed: ' . $this->db->error);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_all(MYSQLI_ASSOC);
        
        // Format data for Chart.js
        $labels = [];
        $values = [];
        $colors = [
            'pending' => '#ffc107',
            'under_review' => '#17a2b8',
            'approved' => '#28a745',
            'rejected' => '#dc3545',
            'completed' => '#6f42c1'
        ];
        
        foreach ($data as $row) {
            $labels[] = ucfirst(str_replace('_', ' ', $row['status']));
            $values[] = (int)$row['count'];
        }
        
        return [
            'labels' => $labels,
            'data' => $values,
            'colors' => $colors
        ];
    }
    
    /**
     * Get detailed status breakdown
     * 
     * @param string $userRole User's role
     * @param int $userId User's ID
     * @return array Detailed status breakdown
     */
    public function getStatusBreakdown($userRole, $userId) {
        if ($userRole === 'citizen') {
            $query = "SELECT 
                        s.status,
                        s.priority,
                        COUNT(*) as count
                      FROM {$this->table} s 
                      WHERE s.user_id = ? 
                      GROUP BY s.status, s.priority 
                      ORDER BY s.status, s.priority";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("i", $userId);
        } else {
            $query = "SELECT 
                        s.status,
                        s.priority,
                        COUNT(*) as count
                      FROM {$this->table} s 
                      GROUP BY s.status, s.priority 
                      ORDER BY s.status, s.priority";
            $stmt = $this->db->prepare($query);
        }
        
        if (!$stmt) {
            throw new Exception('Database query preparation failed: ' . $this->db->error);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    // ============================================================================
    // DEPARTMENT METHODS
    // ============================================================================
    
    /**
     * Get department breakdown for charts
     * 
     * @return array Department statistics formatted for charts
     */
    public function getDepartmentBreakdown() {
        $query = "SELECT 
                    d.name as department_name,
                    COUNT(s.id) as total_submissions
                  FROM departments d 
                  LEFT JOIN {$this->table} s ON d.id = s.department_id 
                  GROUP BY d.id, d.name 
                  HAVING total_submissions > 0
                  ORDER BY total_submissions DESC";
        
        $stmt = $this->db->prepare($query);
        
        if (!$stmt) {
            throw new Exception('Database query preparation failed: ' . $this->db->error);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_all(MYSQLI_ASSOC);
        
        // Format data for Chart.js
        $labels = [];
        $values = [];
        
        foreach ($data as $row) {
            $labels[] = $row['department_name'];
            $values[] = (int)$row['total_submissions'];
        }
        
        return [
            'labels' => $labels,
            'data' => $values
        ];
    }
    
    
    // ============================================================================
    // MONTHLY TREND METHODS
    // ============================================================================
    
    /**
     * Get monthly statistics for trend analysis
     * 
     * @param string $userRole User's role
     * @param int $userId User's ID
     * @return array Monthly statistics formatted for charts
     */
    public function getMonthlyStatistics($userRole, $userId) {
        if ($userRole === 'citizen') {
            $query = "SELECT 
                        DATE_FORMAT(created_at, '%Y-%m') as month,
                        COUNT(*) as count
                      FROM {$this->table} 
                      WHERE user_id = ? 
                        AND created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
                      GROUP BY DATE_FORMAT(created_at, '%Y-%m')
                      ORDER BY month ASC";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("i", $userId);
        } else {
            $query = "SELECT 
                        DATE_FORMAT(created_at, '%Y-%m') as month,
                        COUNT(*) as count
                      FROM {$this->table} 
                      WHERE created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
                      GROUP BY DATE_FORMAT(created_at, '%Y-%m')
                      ORDER BY month ASC";
            $stmt = $this->db->prepare($query);
        }
        
        if (!$stmt) {
            throw new Exception('Database query preparation failed: ' . $this->db->error);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_all(MYSQLI_ASSOC);
        
        // Format data for Chart.js
        $labels = [];
        $values = [];
        
        foreach ($data as $row) {
            $labels[] = date('M Y', strtotime($row['month'] . '-01'));
            $values[] = (int)$row['count'];
        }
        
        return [
            'labels' => $labels,
            'data' => $values
        ];
    }
    
    // ============================================================================
    // COMPREHENSIVE REPORT METHODS
    // ============================================================================
    
    /**
     * Get comprehensive statistics (Admin only)
     * 
     * @return array Comprehensive statistics
     */
    public function getComprehensiveStatistics() {
        return [
            'basic' => $this->getBasicStatistics(),
            'department' => $this->getDepartmentStatistics(),
            'status_chart' => $this->getSubmissionStatusStatistics('admin', 0),
            'department_chart' => $this->getDepartmentBreakdown(),
            'monthly_chart' => $this->getMonthlyStatistics('admin', 0)
        ];
    }
    
    // ============================================================================
    // UTILITY METHODS
    // ============================================================================
    
    /**
     * Get top performing departments
     * 
     * @param int $limit Number of departments to return
     * @return array Top performing departments
     */
    public function getTopPerformingDepartments($limit = 5) {
        $query = "SELECT 
                    d.name as department_name,
                    COUNT(s.id) as total_submissions,
                    SUM(CASE WHEN s.status = 'completed' THEN 1 ELSE 0 END) as completed,
                    ROUND(
                        (SUM(CASE WHEN s.status = 'completed' THEN 1 ELSE 0 END) / COUNT(s.id)) * 100, 
                        2
                    ) as completion_rate
                  FROM departments d 
                  LEFT JOIN {$this->table} s ON d.id = s.department_id 
                  GROUP BY d.id, d.name 
                  HAVING total_submissions > 0
                  ORDER BY completion_rate DESC, total_submissions DESC
                  LIMIT ?";
        
        $stmt = $this->db->prepare($query);
        
        if (!$stmt) {
            throw new Exception('Database query preparation failed: ' . $this->db->error);
        }
        
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * Get recent activity summary
     * 
     * @param int $days Number of days to look back
     * @return array Recent activity summary
     */
    public function getRecentActivity($days = 7) {
        $query = "SELECT 
                    DATE(created_at) as date,
                    COUNT(*) as submissions,
                    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed
                  FROM {$this->table} 
                  WHERE created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
                  GROUP BY DATE(created_at)
                  ORDER BY date DESC";
        
        $stmt = $this->db->prepare($query);
        
        if (!$stmt) {
            throw new Exception('Database query preparation failed: ' . $this->db->error);
        }
        
        $stmt->bind_param("i", $days);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * Get monthly submission trend data
     * 
     * @param int $months Number of months to look back
     * @return array Monthly trend data
     */
    public function getMonthlySubmissionTrend($months = 12) {
        $query = "SELECT 
                    DATE_FORMAT(created_at, '%Y-%m') as month,
                    COUNT(*) as submissions,
                    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                    SUM(CASE WHEN status = 'under_review' THEN 1 ELSE 0 END) as under_review,
                    SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved,
                    SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected,
                    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed
                  FROM {$this->table} 
                  WHERE created_at >= DATE_SUB(NOW(), INTERVAL ? MONTH)
                  GROUP BY DATE_FORMAT(created_at, '%Y-%m')
                  ORDER BY month ASC";
        
        $stmt = $this->db->prepare($query);
        
        if (!$stmt) {
            throw new Exception('Database query preparation failed: ' . $this->db->error);
        }
        
        $stmt->bind_param("i", $months);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $monthlyData = $result->fetch_all(MYSQLI_ASSOC);
        
        // Format data for Chart.js
        $labels = [];
        $data = [];
        
        foreach ($monthlyData as $row) {
            $labels[] = date('M Y', strtotime($row['month'] . '-01'));
            $data[] = (int)$row['submissions'];
        }
        
        return [
            'labels' => $labels,
            'data' => $data,
            'raw_data' => $monthlyData
        ];
    }
}
?>
