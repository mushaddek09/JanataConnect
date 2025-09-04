<?php
require_once 'BaseModel.php';

class Submission extends BaseModel {
    protected $table = 'submissions';
    
    public function getSubmissionsByUser($userId, $limit = null, $offset = 0) {
        $query = "SELECT s.*, d.name as department_name 
                  FROM {$this->table} s 
                  JOIN departments d ON s.department_id = d.id 
                  WHERE s.user_id = ? 
                  ORDER BY s.created_at DESC";
        
        if ($limit) {
            $query .= " LIMIT ? OFFSET ?";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("iii", $userId, $limit, $offset);
        } else {
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("i", $userId);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    public function getSubmissionsByDepartment($departmentId, $limit = null, $offset = 0) {
        $query = "SELECT s.*, u.name as user_name, u.email as user_email 
                  FROM {$this->table} s 
                  JOIN users u ON s.user_id = u.id 
                  WHERE s.department_id = ? 
                  ORDER BY s.created_at DESC";
        
        if ($limit) {
            $query .= " LIMIT ? OFFSET ?";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("iii", $departmentId, $limit, $offset);
        } else {
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("i", $departmentId);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    public function getAllSubmissions($limit = null, $offset = 0) {
        $query = "SELECT s.*, u.name as user_name, d.name as department_name 
                  FROM {$this->table} s 
                  JOIN users u ON s.user_id = u.id 
                  JOIN departments d ON s.department_id = d.id 
                  ORDER BY s.created_at DESC";
        
        if ($limit) {
            $query .= " LIMIT ? OFFSET ?";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("ii", $limit, $offset);
        } else {
            $stmt = $this->db->prepare($query);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    public function getSubmissionWithDetails($id) {
        $query = "SELECT s.*, u.name as user_name, u.email as user_email, d.name as department_name 
                  FROM {$this->table} s 
                  JOIN users u ON s.user_id = u.id 
                  JOIN departments d ON s.department_id = d.id 
                  WHERE s.id = ?";
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
    
    public function updateStatus($id, $status, $comment = null) {
        $data = ['status' => $status];
        if ($comment) {
            $data['official_comment'] = $comment;
        }
        return $this->update($id, $data);
    }
    
    public function getSubmissionsByStatus($status, $limit = null, $offset = 0) {
        return $this->where(['status' => $status], $limit, $offset);
    }
    
    public function getStatistics() {
        $query = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                    SUM(CASE WHEN status = 'under_review' THEN 1 ELSE 0 END) as under_review,
                    SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved,
                    SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected,
                    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed
                  FROM {$this->table}";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
    
    public function getRecentSubmissions($limit = 10) {
        return $this->getAllSubmissions($limit, 0);
    }
    
    public function getSubmissionFiles($submissionId) {
        $query = "SELECT * FROM submission_files WHERE submission_id = ? ORDER BY uploaded_at ASC";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $submissionId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    public function getSubmissionWithFiles($id) {
        $submission = $this->getSubmissionWithDetails($id);
        if ($submission) {
            $submission['files'] = $this->getSubmissionFiles($id);
        }
        return $submission;
    }
}
?>


