<?php
/**
 * Department Model - Handles department-related database operations
 * 
 * This model manages all department-related functionality including creating,
 * updating, retrieving, and managing government departments.
 * 
 * @author JanataConnect Team
 * @version 1.0.0
 * @since 2025-01-01
 */
require_once 'BaseModel.php';

class Department extends BaseModel {
    
    /**
     * Table name for departments
     * @var string
     */
    protected $table = 'departments';
    
    // ============================================================================
    // DEPARTMENT RETRIEVAL METHODS
    // ============================================================================
    
    /**
     * Get all active departments
     * 
     * @return array Array of active departments
     */
    public function getActiveDepartments() {
        return $this->where(['is_active' => 1]);
    }
    
    /**
     * Get all departments including inactive ones
     * 
     * @return array Array of all departments
     */
    public function getAllDepartments() {
        return $this->findAll();
    }
    
    /**
     * Get departments that have officials assigned to them
     * 
     * @return array Array of departments with officials
     */
    public function getDepartmentsWithOfficials() {
        $query = "SELECT DISTINCT d.* 
                  FROM {$this->table} d 
                  INNER JOIN users u ON d.id = u.department_id 
                  WHERE d.is_active = 1 AND u.role = 'official' AND u.is_active = 1
                  ORDER BY d.name";
        
        $result = $this->db->query($query);
        
        if (!$result) {
            throw new Exception('Database query failed: ' . $this->db->error);
        }
        
        $departments = [];
        while ($row = $result->fetch_assoc()) {
            $departments[] = $row;
        }
        
        return $departments;
    }
    
    /**
     * Get department with submission count
     * 
     * @param int $id The department ID
     * @return array|null Department with submission count or null if not found
     */
    public function getDepartmentWithSubmissionCount($id) {
        $query = "SELECT d.*, COUNT(s.id) as submission_count 
                  FROM {$this->table} d 
                  LEFT JOIN submissions s ON d.id = s.department_id 
                  WHERE d.id = ? AND d.is_active = 1 
                  GROUP BY d.id";
        
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
     * Get all departments with comprehensive statistics
     * 
     * @return array Array of departments with statistics
     */
    public function getAllDepartmentsWithStats() {
        $query = "SELECT d.*, 
                    COUNT(s.id) as total_submissions,
                    SUM(CASE WHEN s.status = 'pending' THEN 1 ELSE 0 END) as pending_submissions,
                    SUM(CASE WHEN s.status = 'under_review' THEN 1 ELSE 0 END) as under_review_submissions,
                    SUM(CASE WHEN s.status = 'approved' THEN 1 ELSE 0 END) as approved_submissions,
                    SUM(CASE WHEN s.status = 'rejected' THEN 1 ELSE 0 END) as rejected_submissions,
                    SUM(CASE WHEN s.status = 'completed' THEN 1 ELSE 0 END) as completed_submissions
                  FROM {$this->table} d 
                  LEFT JOIN submissions s ON d.id = s.department_id 
                  WHERE d.is_active = 1 
                  GROUP BY d.id 
                  ORDER BY d.name";
        
        $stmt = $this->db->prepare($query);
        
        if (!$stmt) {
            throw new Exception('Database query preparation failed: ' . $this->db->error);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * Get departments with recent activity
     * 
     * @param int $limit Number of departments to return
     * @return array Array of departments with recent activity
     */
    public function getDepartmentsWithRecentActivity($limit = 10) {
        $query = "SELECT d.*, 
                    COUNT(s.id) as total_submissions,
                    MAX(s.created_at) as last_submission_date
                  FROM {$this->table} d 
                  LEFT JOIN submissions s ON d.id = s.department_id 
                  WHERE d.is_active = 1 
                  GROUP BY d.id 
                  ORDER BY last_submission_date DESC, d.name ASC
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
    
    // ============================================================================
    // DEPARTMENT STATUS MANAGEMENT
    // ============================================================================
    
    /**
     * Deactivate a department
     * 
     * @param int $id The department ID
     * @return bool True on success, false on failure
     */
    public function deactivateDepartment($id) {
        return $this->update($id, ['is_active' => 0]);
    }
    
    /**
     * Activate a department
     * 
     * @param int $id The department ID
     * @return bool True on success, false on failure
     */
    public function activateDepartment($id) {
        return $this->update($id, ['is_active' => 1]);
    }
    
    /**
     * Toggle department active status
     * 
     * @param int $id The department ID
     * @return bool True on success, false on failure
     */
    public function toggleDepartmentStatus($id) {
        $department = $this->find($id);
        
        if (!$department) {
            throw new Exception('Department not found');
        }
        
        $newStatus = $department['is_active'] ? 0 : 1;
        return $this->update($id, ['is_active' => $newStatus]);
    }
    
    // ============================================================================
    // DEPARTMENT CREATION AND UPDATES
    // ============================================================================
    
    /**
     * Create a new department
     * 
     * @param array $data Department data
     * @return int|false New department ID on success, false on failure
     */
    public function createDepartment($data) {
        // Validate required fields
        if (empty($data['name'])) {
            throw new Exception('Department name is required');
        }
        
        // Check if department name already exists
        if ($this->departmentNameExists($data['name'])) {
            throw new Exception('Department name already exists');
        }
        
        // Set default values
        $data['is_active'] = $data['is_active'] ?? 1;
        
        return $this->create($data);
    }
    
    /**
     * Update department information
     * 
     * @param int $id The department ID
     * @param array $data Department data to update
     * @return bool True on success, false on failure
     */
    public function updateDepartment($id, $data) {
        // Remove fields that shouldn't be updated
        unset($data['created_at']);
        
        if (empty($data)) {
            throw new Exception('No data provided for update');
        }
        
        // Check if department name already exists (excluding current department)
        if (!empty($data['name']) && $this->departmentNameExists($data['name'], $id)) {
            throw new Exception('Department name already exists');
        }
        
        return $this->update($id, $data);
    }
    
    /**
     * Check if department name already exists
     * 
     * @param string $name The department name
     * @param int|null $excludeId Department ID to exclude from check
     * @return bool True if name exists, false otherwise
     */
    public function departmentNameExists($name, $excludeId = null) {
        $query = "SELECT COUNT(*) as count FROM {$this->table} WHERE name = ?";
        
        if ($excludeId) {
            $query .= " AND id != ?";
        }
        
        $stmt = $this->db->prepare($query);
        
        if (!$stmt) {
            throw new Exception('Database query preparation failed: ' . $this->db->error);
        }
        
        if ($excludeId) {
            $stmt->bind_param("si", $name, $excludeId);
        } else {
            $stmt->bind_param("s", $name);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        return $row['count'] > 0;
    }
    
    // ============================================================================
    // STATISTICS AND ANALYTICS
    // ============================================================================
    
    /**
     * Get department statistics
     * 
     * @return array Department statistics
     */
    public function getDepartmentStatistics() {
        $query = "SELECT 
                    COUNT(*) as total_departments,
                    SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active_departments,
                    SUM(CASE WHEN is_active = 0 THEN 1 ELSE 0 END) as inactive_departments
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
     * Get top departments by submission count
     * 
     * @param int $limit Number of top departments to return
     * @return array Array of top departments
     */
    public function getTopDepartmentsBySubmissions($limit = 10) {
        $query = "SELECT d.*, COUNT(s.id) as submission_count
                  FROM {$this->table} d 
                  LEFT JOIN submissions s ON d.id = s.department_id 
                  WHERE d.is_active = 1 
                  GROUP BY d.id 
                  ORDER BY submission_count DESC, d.name ASC
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
     * Get department performance metrics
     * 
     * @param int $id The department ID
     * @return array Department performance metrics
     */
    public function getDepartmentPerformance($id) {
        $query = "SELECT 
                    d.name as department_name,
                    COUNT(s.id) as total_submissions,
                    SUM(CASE WHEN s.status = 'pending' THEN 1 ELSE 0 END) as pending_submissions,
                    SUM(CASE WHEN s.status = 'under_review' THEN 1 ELSE 0 END) as under_review_submissions,
                    SUM(CASE WHEN s.status = 'approved' THEN 1 ELSE 0 END) as approved_submissions,
                    SUM(CASE WHEN s.status = 'rejected' THEN 1 ELSE 0 END) as rejected_submissions,
                    SUM(CASE WHEN s.status = 'completed' THEN 1 ELSE 0 END) as completed_submissions,
                    AVG(CASE 
                        WHEN s.status = 'completed' 
                        THEN TIMESTAMPDIFF(DAY, s.created_at, s.updated_at) 
                        ELSE NULL 
                    END) as avg_resolution_days
                  FROM {$this->table} d 
                  LEFT JOIN submissions s ON d.id = s.department_id 
                  WHERE d.id = ? AND d.is_active = 1 
                  GROUP BY d.id";
        
        $stmt = $this->db->prepare($query);
        
        if (!$stmt) {
            throw new Exception('Database query preparation failed: ' . $this->db->error);
        }
        
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc();
    }
    
    // ============================================================================
    // SEARCH AND FILTERING
    // ============================================================================
    
    /**
     * Search departments by name or description
     * 
     * @param string $searchTerm The search term
     * @param int|null $limit Maximum number of departments to return
     * @param int $offset Number of departments to skip
     * @return array Array of matching departments
     */
    public function searchDepartments($searchTerm, $limit = null, $offset = 0) {
        $searchTerm = "%{$searchTerm}%";
        $query = "SELECT * FROM {$this->table} 
                  WHERE (name LIKE ? OR description LIKE ?) 
                  AND is_active = 1 
                  ORDER BY name ASC";
        
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
     * Get departments with filters
     * 
     * @param array $filters Optional filters (is_active, search)
     * @param int|null $limit Maximum number of departments to return
     * @param int $offset Number of departments to skip
     * @return array Array of filtered departments
     */
    public function getDepartmentsWithFilters($filters = [], $limit = null, $offset = 0) {
        $whereConditions = [];
        $params = [];
        $types = '';
        
        // Build where conditions based on filters
        if (isset($filters['is_active'])) {
            $whereConditions[] = "is_active = ?";
            $params[] = $filters['is_active'];
            $types .= 'i';
        }
        
        if (!empty($filters['search'])) {
            $whereConditions[] = "(name LIKE ? OR description LIKE ?)";
            $searchTerm = "%{$filters['search']}%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $types .= 'ss';
        }
        
        $whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';
        
        $query = "SELECT * FROM {$this->table} 
                  {$whereClause}
                  ORDER BY name ASC";
        
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
     * Validate department data
     * 
     * @param array $data Department data to validate
     * @return array Validation result with success status and errors
     */
    public function validateDepartmentData($data) {
        $errors = [];
        
        // Required fields
        if (empty($data['name'])) {
            $errors[] = 'Department name is required';
        }
        
        // Field length validation
        if (!empty($data['name']) && strlen($data['name']) > 100) {
            $errors[] = 'Department name must be less than 100 characters';
        }
        
        if (!empty($data['description']) && strlen($data['description']) > 1000) {
            $errors[] = 'Description must be less than 1000 characters';
        }
        
        // Name uniqueness validation
        if (!empty($data['name'])) {
            $excludeId = $data['id'] ?? null;
            if ($this->departmentNameExists($data['name'], $excludeId)) {
                $errors[] = 'Department name already exists';
            }
        }
        
        return [
            'success' => empty($errors),
            'errors' => $errors
        ];
    }
}
?>