<?php
/**
 * User Model - Handles user-related database operations
 * 
 * This model manages all user-related functionality including authentication,
 * registration, password management, and user role operations.
 * 
 * @author JanataConnect Team
 * @version 1.0.0
 * @since 2025-01-01
 */
require_once 'BaseModel.php';

class User extends BaseModel {
    
    /**
     * Table name for users
     * @var string
     */
    protected $table = 'users';
    
    // ============================================================================
    // AUTHENTICATION METHODS
    // ============================================================================
    
    /**
     * Find a user by email address
     * 
     * @param string $email The user's email address
     * @return array|null User data or null if not found
     */
    public function findByEmail($email) {
        $query = "SELECT * FROM {$this->table} WHERE email = ? AND is_active = 1";
        $stmt = $this->db->prepare($query);
        
        if (!$stmt) {
            throw new Exception('Database query preparation failed: ' . $this->db->error);
        }
        
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc();
    }
    
    /**
     * Authenticate a user with email and password
     * 
     * @param string $email The user's email address
     * @param string $password The user's password
     * @return array|false User data on success, false on failure
     */
    public function authenticate($email, $password) {
        $user = $this->findByEmail($email);
        
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        
        return false;
    }
    
    /**
     * Create a new user with hashed password
     * 
     * @param array $data User data including password
     * @return int|false New user ID on success, false on failure
     */
    public function createUser($data) {
        // Validate required fields
        if (empty($data['name']) || empty($data['email']) || empty($data['password'])) {
            throw new Exception('Name, email, and password are required');
        }
        
        // Check if email already exists
        if ($this->findByEmail($data['email'])) {
            throw new Exception('Email already exists');
        }
        
        // Hash the password
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        
        // Set default values
        $data['role'] = $data['role'] ?? 'citizen';
        $data['is_active'] = $data['is_active'] ?? 1;
        
        return $this->create($data);
    }
    
    // ============================================================================
    // PASSWORD MANAGEMENT
    // ============================================================================
    
    /**
     * Update a user's password
     * 
     * @param int $userId The user's ID
     * @param string $newPassword The new password
     * @return bool True on success, false on failure
     */
    public function updatePassword($userId, $newPassword) {
        if (empty($newPassword)) {
            throw new Exception('Password cannot be empty');
        }
        
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        return $this->update($userId, ['password' => $hashedPassword]);
    }
    
    /**
     * Verify a user's current password
     * 
     * @param int $userId The user's ID
     * @param string $currentPassword The current password
     * @return bool True if password is correct, false otherwise
     */
    public function verifyCurrentPassword($userId, $currentPassword) {
        $user = $this->find($userId);
        
        if (!$user) {
            return false;
        }
        
        return password_verify($currentPassword, $user['password']);
    }
    
    /**
     * Change a user's password with current password verification
     * 
     * @param int $userId The user's ID
     * @param string $currentPassword The current password
     * @param string $newPassword The new password
     * @return bool True on success, false on failure
     */
    public function changePassword($userId, $currentPassword, $newPassword) {
        if (!$this->verifyCurrentPassword($userId, $currentPassword)) {
            throw new Exception('Current password is incorrect');
        }
        
        return $this->updatePassword($userId, $newPassword);
    }
    
    // ============================================================================
    // USER ROLE MANAGEMENT
    // ============================================================================
    
    /**
     * Get users by role with optional pagination
     * 
     * @param string $role The user role (citizen, official, admin)
     * @param int|null $limit Maximum number of users to return
     * @param int $offset Number of users to skip
     * @return array Array of users
     */
    public function getUsersByRole($role, $limit = null, $offset = 0) {
        if (!in_array($role, ['citizen', 'official', 'admin'])) {
            throw new Exception('Invalid user role');
        }
        
        return $this->where(['role' => $role], $limit, $offset);
    }
    
    /**
     * Get officials by department
     * 
     * @param int $departmentId The department ID
     * @return array Array of officials
     */
    public function getOfficialsByDepartment($departmentId) {
        $query = "SELECT * FROM {$this->table} 
                  WHERE role = 'official' 
                  AND department_id = ? 
                  AND is_active = 1 
                  ORDER BY name ASC";
        
        $stmt = $this->db->prepare($query);
        
        if (!$stmt) {
            throw new Exception('Database query preparation failed: ' . $this->db->error);
        }
        
        $stmt->bind_param("i", $departmentId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * Update user role
     * 
     * @param int $userId The user's ID
     * @param string $role The new role
     * @return bool True on success, false on failure
     */
    public function updateUserRole($userId, $role) {
        if (!in_array($role, ['citizen', 'official', 'admin'])) {
            throw new Exception('Invalid user role');
        }
        
        return $this->update($userId, ['role' => $role]);
    }
    
    /**
     * Assign user to department
     * 
     * @param int $userId The user's ID
     * @param int $departmentId The department ID
     * @return bool True on success, false on failure
     */
    public function assignToDepartment($userId, $departmentId) {
        return $this->update($userId, ['department_id' => $departmentId]);
    }
    
    // ============================================================================
    // USER STATUS MANAGEMENT
    // ============================================================================
    
    /**
     * Deactivate a user account
     * 
     * @param int $userId The user's ID
     * @return bool True on success, false on failure
     */
    public function deactivateUser($userId) {
        return $this->update($userId, ['is_active' => 0]);
    }
    
    /**
     * Activate a user account
     * 
     * @param int $userId The user's ID
     * @return bool True on success, false on failure
     */
    public function activateUser($userId) {
        return $this->update($userId, ['is_active' => 1]);
    }
    
    /**
     * Toggle user active status
     * 
     * @param int $userId The user's ID
     * @return bool True on success, false on failure
     */
    public function toggleUserStatus($userId) {
        $user = $this->find($userId);
        
        if (!$user) {
            throw new Exception('User not found');
        }
        
        $newStatus = $user['is_active'] ? 0 : 1;
        return $this->update($userId, ['is_active' => $newStatus]);
    }
    
    // ============================================================================
    // USER PROFILE MANAGEMENT
    // ============================================================================
    
    /**
     * Update user profile information
     * 
     * @param int $userId The user's ID
     * @param array $profileData Profile data to update
     * @return bool True on success, false on failure
     */
    public function updateProfile($userId, $profileData) {
        // Remove sensitive fields that shouldn't be updated through this method
        unset($profileData['password']);
        unset($profileData['role']);
        unset($profileData['is_active']);
        unset($profileData['created_at']);
        
        if (empty($profileData)) {
            throw new Exception('No profile data provided');
        }
        
        return $this->update($userId, $profileData);
    }
    
    /**
     * Get user profile with department information
     * 
     * @param int $userId The user's ID
     * @return array|null User profile with department info or null if not found
     */
    public function getProfileWithDepartment($userId) {
        $query = "SELECT u.*, d.name as department_name 
                  FROM {$this->table} u 
                  LEFT JOIN departments d ON u.department_id = d.id 
                  WHERE u.id = ?";
        
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
    // STATISTICS AND ANALYTICS
    // ============================================================================
    
    /**
     * Get user statistics
     * 
     * @return array User statistics
     */
    public function getUserStatistics() {
        $query = "SELECT 
                    COUNT(*) as total_users,
                    SUM(CASE WHEN role = 'citizen' THEN 1 ELSE 0 END) as citizens,
                    SUM(CASE WHEN role = 'official' THEN 1 ELSE 0 END) as officials,
                    SUM(CASE WHEN role = 'admin' THEN 1 ELSE 0 END) as admins,
                    SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active_users,
                    SUM(CASE WHEN is_active = 0 THEN 1 ELSE 0 END) as inactive_users
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
     * Get recent user registrations
     * 
     * @param int $limit Number of recent users to return
     * @return array Array of recent users
     */
    public function getRecentUsers($limit = 10) {
        $query = "SELECT id, name, email, role, created_at 
                  FROM {$this->table} 
                  ORDER BY created_at DESC 
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
    // SEARCH AND FILTERING
    // ============================================================================
    
    /**
     * Search users by name or email
     * 
     * @param string $searchTerm The search term
     * @param int|null $limit Maximum number of users to return
     * @param int $offset Number of users to skip
     * @return array Array of matching users
     */
    public function searchUsers($searchTerm, $limit = null, $offset = 0) {
        $searchTerm = "%{$searchTerm}%";
        $query = "SELECT * FROM {$this->table} 
                  WHERE (name LIKE ? OR email LIKE ?) 
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
     * Get users with pagination and optional filters
     * 
     * @param array $filters Optional filters (role, department_id, is_active)
     * @param int|null $limit Maximum number of users to return
     * @param int $offset Number of users to skip
     * @return array Array of users
     */
    public function getUsersWithFilters($filters = [], $limit = null, $offset = 0) {
        $whereConditions = [];
        $params = [];
        $types = '';
        
        // Build where conditions based on filters
        if (!empty($filters['role'])) {
            $whereConditions[] = "role = ?";
            $params[] = $filters['role'];
            $types .= 's';
        }
        
        if (!empty($filters['department_id'])) {
            $whereConditions[] = "department_id = ?";
            $params[] = $filters['department_id'];
            $types .= 'i';
        }
        
        if (isset($filters['is_active'])) {
            $whereConditions[] = "is_active = ?";
            $params[] = $filters['is_active'];
            $types .= 'i';
        }
        
        $whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';
        
        $query = "SELECT u.*, d.name as department_name 
                  FROM {$this->table} u 
                  LEFT JOIN departments d ON u.department_id = d.id 
                  {$whereClause}
                  ORDER BY u.name ASC";
        
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
}
?>