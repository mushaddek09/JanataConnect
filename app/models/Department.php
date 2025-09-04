<?php
require_once 'BaseModel.php';

class Department extends BaseModel {
    protected $table = 'departments';
    
    public function getActiveDepartments() {
        return $this->where(['is_active' => 1]);
    }
    
    public function getDepartmentWithSubmissionCount($id) {
        $query = "SELECT d.*, COUNT(s.id) as submission_count 
                  FROM {$this->table} d 
                  LEFT JOIN submissions s ON d.id = s.department_id 
                  WHERE d.id = ? AND d.is_active = 1 
                  GROUP BY d.id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
    
    public function getAllDepartmentsWithStats() {
        $query = "SELECT d.*, 
                    COUNT(s.id) as total_submissions,
                    SUM(CASE WHEN s.status = 'pending' THEN 1 ELSE 0 END) as pending_submissions,
                    SUM(CASE WHEN s.status = 'completed' THEN 1 ELSE 0 END) as completed_submissions
                  FROM {$this->table} d 
                  LEFT JOIN submissions s ON d.id = s.department_id 
                  WHERE d.is_active = 1 
                  GROUP BY d.id 
                  ORDER BY d.name";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    public function deactivateDepartment($id) {
        return $this->update($id, ['is_active' => 0]);
    }
    
    public function activateDepartment($id) {
        return $this->update($id, ['is_active' => 1]);
    }
}
?>


