<?php
/**
 * Submission Model - Handles submission-related database operations
 * 
 * This model manages all submission-related functionality including creating,
 * updating, retrieving, and managing citizen suggestions and complaints.
 * 
 * @author JanataConnect Team
 * @version 1.0.0
 * @since 2025-01-01
 */
require_once 'BaseModel.php';

class Submission extends BaseModel {
    
    /**
     * Table name for submissions
     * @var string
     */
    protected $table = 'submissions';
    
    // ============================================================================
    // SUBMISSION RETRIEVAL METHODS
    // ============================================================================
    
    /**
     * Get submissions by user ID with department information
     * 
     * @param int $userId The user's ID
     * @param int|null $limit Maximum number of submissions to return
     * @param int $offset Number of submissions to skip
     * @return array Array of submissions with department names
     */
    public function getSubmissionsByUser($userId, $limit = null, $offset = 0) {
        $query = "SELECT s.*, d.name as department_name 
                  FROM {$this->table} s 
                  JOIN departments d ON s.department_id = d.id 
                  WHERE s.user_id = ? 
                  ORDER BY s.created_at DESC";
        
        if ($limit) {
            $query .= " LIMIT ? OFFSET ?";
            $stmt = $this->db->prepare($query);
            
            if (!$stmt) {
                throw new Exception('Database query preparation failed: ' . $this->db->error);
            }
            
            $stmt->bind_param("iii", $userId, $limit, $offset);
        } else {
            $stmt = $this->db->prepare($query);
            
            if (!$stmt) {
                throw new Exception('Database query preparation failed: ' . $this->db->error);
            }
            
            $stmt->bind_param("i", $userId);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * Get submissions by department ID with user information
     * 
     * @param int $departmentId The department's ID
     * @param int|null $limit Maximum number of submissions to return
     * @param int $offset Number of submissions to skip
     * @return array Array of submissions with user information
     */
    public function getSubmissionsByDepartment($departmentId, $limit = null, $offset = 0) {
        $query = "SELECT s.*, u.name as user_name, u.email as user_email 
                  FROM {$this->table} s 
                  JOIN users u ON s.user_id = u.id 
                  WHERE s.department_id = ? 
                  ORDER BY s.created_at DESC";
        
        if ($limit) {
            $query .= " LIMIT ? OFFSET ?";
            $stmt = $this->db->prepare($query);
            
            if (!$stmt) {
                throw new Exception('Database query preparation failed: ' . $this->db->error);
            }
            
            $stmt->bind_param("iii", $departmentId, $limit, $offset);
        } else {
            $stmt = $this->db->prepare($query);
            
            if (!$stmt) {
                throw new Exception('Database query preparation failed: ' . $this->db->error);
            }
            
            $stmt->bind_param("i", $departmentId);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * Get all submissions with user and department information (Admin only)
     * 
     * @param int|null $limit Maximum number of submissions to return
     * @param int $offset Number of submissions to skip
     * @return array Array of all submissions with user and department info
     */
    public function getAllSubmissions($limit = null, $offset = 0) {
        $query = "SELECT s.*, u.name as user_name, d.name as department_name 
                  FROM {$this->table} s 
                  JOIN users u ON s.user_id = u.id 
                  JOIN departments d ON s.department_id = d.id 
                  ORDER BY s.created_at DESC";
        
        if ($limit) {
            $query .= " LIMIT ? OFFSET ?";
            $stmt = $this->db->prepare($query);
            
            if (!$stmt) {
                throw new Exception('Database query preparation failed: ' . $this->db->error);
            }
            
            $stmt->bind_param("ii", $limit, $offset);
        } else {
            $stmt = $this->db->prepare($query);
            
            if (!$stmt) {
                throw new Exception('Database query preparation failed: ' . $this->db->error);
            }
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * Get a single submission with detailed information
     * 
     * @param int $id The submission ID
     * @return array|null Submission details or null if not found
     */
    public function getSubmissionWithDetails($id) {
        $query = "SELECT s.*, u.name as user_name, u.email as user_email, d.name as department_name 
                  FROM {$this->table} s 
                  JOIN users u ON s.user_id = u.id 
                  JOIN departments d ON s.department_id = d.id 
                  WHERE s.id = ?";
        
        $stmt = $this->db->prepare($query);
        
        if (!$stmt) {
            throw new Exception('Database query preparation failed: ' . $this->db->error);
        }
        
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc();
    }
    
    /**
     * Get recent submissions
     * 
     * @param int $limit Number of recent submissions to return
     * @return array Array of recent submissions
     */
    public function getRecentSubmissions($limit = 10) {
        return $this->getAllSubmissions($limit, 0);
    }
    
    // ============================================================================
    // SUBMISSION STATUS MANAGEMENT
    // ============================================================================
    
    /**
     * Update submission status
     * 
     * @param int $id The submission ID
     * @param string $status The new status
     * @param string|null $comment Optional official comment
     * @return bool True on success, false on failure
     */
    public function updateStatus($id, $status, $comment = null) {
        $validStatuses = ['pending', 'under_review', 'approved', 'rejected', 'completed'];
        
        if (!in_array($status, $validStatuses)) {
            throw new Exception('Invalid submission status');
        }
        
        $data = ['status' => $status];
        
        if ($comment) {
            $data['official_comment'] = $comment;
        }
        
        return $this->update($id, $data);
    }
    
    /**
     * Get submissions by status
     * 
     * @param string $status The submission status
     * @param int|null $limit Maximum number of submissions to return
     * @param int $offset Number of submissions to skip
     * @return array Array of submissions with the specified status
     */
    public function getSubmissionsByStatus($status, $limit = null, $offset = 0) {
        $validStatuses = ['pending', 'under_review', 'approved', 'rejected', 'completed'];
        
        if (!in_array($status, $validStatuses)) {
            throw new Exception('Invalid submission status');
        }
        
        return $this->where(['status' => $status], $limit, $offset);
    }
    
    /**
     * Get submissions by priority
     * 
     * @param string $priority The submission priority
     * @param int|null $limit Maximum number of submissions to return
     * @param int $offset Number of submissions to skip
     * @return array Array of submissions with the specified priority
     */
    public function getSubmissionsByPriority($priority, $limit = null, $offset = 0) {
        $validPriorities = ['low', 'medium', 'high'];
        
        if (!in_array($priority, $validPriorities)) {
            throw new Exception('Invalid submission priority');
        }
        
        return $this->where(['priority' => $priority], $limit, $offset);
    }
    
    // ============================================================================
    // FILE MANAGEMENT
    // ============================================================================
    
    /**
     * Get files associated with a submission
     * 
     * @param int $submissionId The submission ID
     * @return array Array of file information
     */
    public function getSubmissionFiles($submissionId) {
        $query = "SELECT * FROM submission_files 
                  WHERE submission_id = ? 
                  ORDER BY uploaded_at ASC";
        
        $stmt = $this->db->prepare($query);
        
        if (!$stmt) {
            throw new Exception('Database query preparation failed: ' . $this->db->error);
        }
        
        $stmt->bind_param("i", $submissionId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * Get submission with associated files
     * 
     * @param int $id The submission ID
     * @return array|null Submission with files or null if not found
     */
    public function getSubmissionWithFiles($id) {
        $submission = $this->getSubmissionWithDetails($id);
        
        if ($submission) {
            $submission['files'] = $this->getSubmissionFiles($id);
        }
        
        return $submission;
    }
    
    /**
     * Add file to submission
     * 
     * @param int $submissionId The submission ID
     * @param array $fileData File information
     * @return int|false File ID on success, false on failure
     */
    public function addFileToSubmission($submissionId, $fileData) {
        $requiredFields = ['file_name', 'file_path', 'file_type', 'file_size'];
        
        foreach ($requiredFields as $field) {
            if (!isset($fileData[$field])) {
                throw new Exception("Missing required field: {$field}");
            }
        }
        
        $fileData['submission_id'] = $submissionId;
        
        // Insert into submission_files table directly
        $query = "INSERT INTO submission_files (submission_id, file_name, file_path, file_type, file_size) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($query);
        
        if (!$stmt) {
            throw new Exception('Database prepare failed: ' . $this->db->error);
        }
        
        $stmt->bind_param('isssi', 
            $fileData['submission_id'],
            $fileData['file_name'],
            $fileData['file_path'],
            $fileData['file_type'],
            $fileData['file_size']
        );
        
        if ($stmt->execute()) {
            return $this->db->insert_id;
        } else {
            throw new Exception('Database execute failed: ' . $stmt->error);
        }
    }
    
    /**
     * Remove file from submission
     * 
     * @param int $fileId The file ID
     * @return bool True on success, false on failure
     */
    public function removeFileFromSubmission($fileId) {
        $query = "DELETE FROM submission_files WHERE id = ?";
        $stmt = $this->db->prepare($query);
        
        if (!$stmt) {
            throw new Exception('Database query preparation failed: ' . $this->db->error);
        }
        
        $stmt->bind_param("i", $fileId);
        return $stmt->execute();
    }
    
    // ============================================================================
    // STATISTICS AND ANALYTICS
    // ============================================================================
    
    /**
     * Get submission statistics
     * 
     * @return array Submission statistics
     */
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
        
        if (!$stmt) {
            throw new Exception('Database query preparation failed: ' . $this->db->error);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc();
    }
    
    /**
     * Get statistics by department
     * 
     * @return array Statistics grouped by department
     */
    public function getStatisticsByDepartment() {
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
     * Get statistics by user
     * 
     * @param int $userId The user's ID
     * @return array User's submission statistics
     */
    public function getStatisticsByUser($userId) {
        $query = "SELECT 
                    COUNT(*) as total_submissions,
                    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                    SUM(CASE WHEN status = 'under_review' THEN 1 ELSE 0 END) as under_review,
                    SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved,
                    SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected,
                    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed
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
    // SEARCH AND FILTERING
    // ============================================================================
    
    /**
     * Search submissions by title or description
     * 
     * @param string $searchTerm The search term
     * @param int|null $limit Maximum number of submissions to return
     * @param int $offset Number of submissions to skip
     * @return array Array of matching submissions
     */
    public function searchSubmissions($searchTerm, $limit = null, $offset = 0) {
        $searchTerm = "%{$searchTerm}%";
        $query = "SELECT s.*, u.name as user_name, d.name as department_name 
                  FROM {$this->table} s 
                  JOIN users u ON s.user_id = u.id 
                  JOIN departments d ON s.department_id = d.id 
                  WHERE (s.title LIKE ? OR s.description LIKE ?) 
                  ORDER BY s.created_at DESC";
        
        if ($limit) {
            $query .= " LIMIT ? OFFSET ?";
            $stmt = $this->db->prepare($query);
            
            if (!$stmt) {
                throw new Exception('Database query preparation failed: ' . $this->db->error);
            }
            
            $stmt->bind_param("ssii", $searchTerm, $searchTerm, $limit, $offset);
        } else {
            $stmt = $this->db->prepare($query);
            
            if (!$stmt) {
                throw new Exception('Database query preparation failed: ' . $this->db->error);
            }
            
            $stmt->bind_param("ss", $searchTerm, $searchTerm);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * Get submissions with filters
     * 
     * @param array $filters Optional filters (status, priority, department_id, user_id)
     * @param int|null $limit Maximum number of submissions to return
     * @param int $offset Number of submissions to skip
     * @return array Array of filtered submissions
     */
    public function getSubmissionsWithFilters($filters = [], $limit = null, $offset = 0) {
        $whereConditions = [];
        $params = [];
        $types = '';
        
        // Build where conditions based on filters
        if (!empty($filters['status'])) {
            $whereConditions[] = "s.status = ?";
            $params[] = $filters['status'];
            $types .= 's';
        }
        
        if (!empty($filters['priority'])) {
            $whereConditions[] = "s.priority = ?";
            $params[] = $filters['priority'];
            $types .= 's';
        }
        
        if (!empty($filters['department_id'])) {
            $whereConditions[] = "s.department_id = ?";
            $params[] = $filters['department_id'];
            $types .= 'i';
        }
        
        if (!empty($filters['user_id'])) {
            $whereConditions[] = "s.user_id = ?";
            $params[] = $filters['user_id'];
            $types .= 'i';
        }
        
        $whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';
        
        $query = "SELECT s.*, u.name as user_name, d.name as department_name 
                  FROM {$this->table} s 
                  JOIN users u ON s.user_id = u.id 
                  JOIN departments d ON s.department_id = d.id 
                  {$whereClause}
                  ORDER BY s.created_at DESC";
        
        if ($limit) {
            $query .= " LIMIT ? OFFSET ?";
            $params[] = $limit;
            $params[] = $offset;
            $types .= 'ii';
        }
        
        $stmt = $this->db->prepare($query);
        
        if (!$stmt) {
            throw new Exception('Database query preparation failed: ' . $this->db->error);
        }
        
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    // ============================================================================
    // VALIDATION METHODS
    // ============================================================================
    
    /**
     * Validate submission data
     * 
     * @param array $data Submission data to validate
     * @return array Validation result with success status and errors
     */
    public function validateSubmissionData($data) {
        $errors = [];
        
        // Required fields
        if (empty($data['title'])) {
            $errors[] = 'Title is required';
        }
        
        if (empty($data['description'])) {
            $errors[] = 'Description is required';
        }
        
        if (empty($data['department_id'])) {
            $errors[] = 'Department is required';
        }
        
        if (empty($data['user_id'])) {
            $errors[] = 'User ID is required';
        }
        
        // Field length validation
        if (!empty($data['title']) && strlen($data['title']) > 200) {
            $errors[] = 'Title must be less than 200 characters';
        }
        
        if (!empty($data['description']) && strlen($data['description']) > 5000) {
            $errors[] = 'Description must be less than 5000 characters';
        }
        
        if (!empty($data['location']) && strlen($data['location']) > 200) {
            $errors[] = 'Location must be less than 200 characters';
        }
        
        // Status validation
        if (!empty($data['status'])) {
            $validStatuses = ['pending', 'under_review', 'approved', 'rejected', 'completed'];
            if (!in_array($data['status'], $validStatuses)) {
                $errors[] = 'Invalid status';
            }
        }
        
        // Priority validation
        if (!empty($data['priority'])) {
            $validPriorities = ['low', 'medium', 'high'];
            if (!in_array($data['priority'], $validPriorities)) {
                $errors[] = 'Invalid priority';
            }
        }
        
        return [
            'success' => empty($errors),
            'errors' => $errors
        ];
    }
    
    /**
     * Get all submissions with user and department details
     * 
     * @return array Array of submissions with user and department information
     */
    public function getAllSubmissionsWithDetails() {
        $query = "SELECT s.*, u.name as user_name, d.name as department_name 
                  FROM submissions s 
                  LEFT JOIN users u ON s.user_id = u.id 
                  LEFT JOIN departments d ON s.department_id = d.id 
                  ORDER BY s.created_at DESC";
        
        $result = $this->db->query($query);
        
        if (!$result) {
            throw new Exception('Database query failed: ' . $this->db->error);
        }
        
        $submissions = [];
        while ($row = $result->fetch_assoc()) {
            $submissions[] = $row;
        }
        
        return $submissions;
    }
}
?>