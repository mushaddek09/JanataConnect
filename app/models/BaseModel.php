<?php
/**
 * BaseModel - Foundation class for all database models
 * 
 * This class provides common database operations that can be used by all models.
 * It implements the Active Record pattern and provides CRUD operations.
 * 
 * @author JanataConnect Team
 * @version 1.0.0
 * @since 2025-01-01
 */
class BaseModel {
    
    /**
     * Database connection instance
     * @var mysqli
     */
    protected $db;
    
    /**
     * Table name for this model
     * @var string
     */
    protected $table;
    
    /**
     * Constructor - Initialize database connection
     */
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        
        if (!$this->db) {
            throw new Exception('Database connection failed');
        }
    }
    
    // ============================================================================
    // BASIC CRUD OPERATIONS
    // ============================================================================
    
    /**
     * Find a single record by ID
     * 
     * @param int $id The record ID
     * @return array|null The record data or null if not found
     */
    public function find($id) {
        $query = "SELECT * FROM {$this->table} WHERE id = ?";
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
     * Find all records with optional pagination
     * 
     * @param int|null $limit Maximum number of records to return
     * @param int $offset Number of records to skip
     * @return array Array of records
     */
    public function findAll($limit = null, $offset = 0) {
        $query = "SELECT * FROM {$this->table}";
        
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
     * Create a new record
     * 
     * @param array $data Associative array of column => value pairs
     * @return int|false The new record ID on success, false on failure
     */
    public function create($data) {
        if (empty($data)) {
            throw new Exception('No data provided for creation');
        }
        
        $columns = implode(',', array_keys($data));
        $placeholders = implode(',', array_fill(0, count($data), '?'));
        $query = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
        
        $stmt = $this->db->prepare($query);
        
        if (!$stmt) {
            throw new Exception('Database query preparation failed: ' . $this->db->error);
        }
        
        $types = str_repeat('s', count($data));
        $stmt->bind_param($types, ...array_values($data));
        
        if ($stmt->execute()) {
            return $this->db->insert_id;
        }
        
        return false;
    }
    
    /**
     * Update an existing record
     * 
     * @param int $id The record ID to update
     * @param array $data Associative array of column => value pairs
     * @return bool True on success, false on failure
     */
    public function update($id, $data) {
        if (empty($data)) {
            throw new Exception('No data provided for update');
        }
        
        $setClause = implode('=?,', array_keys($data)) . '=?';
        $query = "UPDATE {$this->table} SET {$setClause} WHERE id = ?";
        
        $stmt = $this->db->prepare($query);
        
        if (!$stmt) {
            throw new Exception('Database query preparation failed: ' . $this->db->error);
        }
        
        $types = str_repeat('s', count($data)) . 'i';
        $values = array_values($data);
        $values[] = $id;
        $stmt->bind_param($types, ...$values);
        
        return $stmt->execute();
    }
    
    /**
     * Delete a record by ID
     * 
     * @param int $id The record ID to delete
     * @return bool True on success, false on failure
     */
    public function delete($id) {
        $query = "DELETE FROM {$this->table} WHERE id = ?";
        $stmt = $this->db->prepare($query);
        
        if (!$stmt) {
            throw new Exception('Database query preparation failed: ' . $this->db->error);
        }
        
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
    
    // ============================================================================
    // ADVANCED QUERY METHODS
    // ============================================================================
    
    /**
     * Find records by conditions with optional pagination
     * 
     * @param array $conditions Associative array of column => value pairs
     * @param int|null $limit Maximum number of records to return
     * @param int $offset Number of records to skip
     * @return array Array of records
     */
    public function where($conditions, $limit = null, $offset = 0) {
        if (empty($conditions)) {
            throw new Exception('No conditions provided for where clause');
        }
        
        $whereClause = implode(' AND ', array_map(function($key) {
            return "{$key} = ?";
        }, array_keys($conditions)));
        
        $query = "SELECT * FROM {$this->table} WHERE {$whereClause}";
        
        if ($limit) {
            $query .= " LIMIT ? OFFSET ?";
            $stmt = $this->db->prepare($query);
            
            if (!$stmt) {
                throw new Exception('Database query preparation failed: ' . $this->db->error);
            }
            
            $types = str_repeat('s', count($conditions)) . 'ii';
            $values = array_values($conditions);
            $values[] = $limit;
            $values[] = $offset;
            $stmt->bind_param($types, ...$values);
        } else {
            $stmt = $this->db->prepare($query);
            
            if (!$stmt) {
                throw new Exception('Database query preparation failed: ' . $this->db->error);
            }
            
            $types = str_repeat('s', count($conditions));
            $stmt->bind_param($types, ...array_values($conditions));
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * Count records with optional conditions
     * 
     * @param array $conditions Associative array of column => value pairs
     * @return int Number of records
     */
    public function count($conditions = []) {
        if (empty($conditions)) {
            $query = "SELECT COUNT(*) as count FROM {$this->table}";
            $stmt = $this->db->prepare($query);
            
            if (!$stmt) {
                throw new Exception('Database query preparation failed: ' . $this->db->error);
            }
        } else {
            $whereClause = implode(' AND ', array_map(function($key) {
                return "{$key} = ?";
            }, array_keys($conditions)));
            
            $query = "SELECT COUNT(*) as count FROM {$this->table} WHERE {$whereClause}";
            $stmt = $this->db->prepare($query);
            
            if (!$stmt) {
                throw new Exception('Database query preparation failed: ' . $this->db->error);
            }
            
            $types = str_repeat('s', count($conditions));
            $stmt->bind_param($types, ...array_values($conditions));
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        return (int)$row['count'];
    }
    
    /**
     * Find a single record by conditions
     * 
     * @param array $conditions Associative array of column => value pairs
     * @return array|null The record data or null if not found
     */
    public function findOne($conditions) {
        $records = $this->where($conditions, 1);
        return !empty($records) ? $records[0] : null;
    }
    
    /**
     * Check if a record exists with given conditions
     * 
     * @param array $conditions Associative array of column => value pairs
     * @return bool True if record exists, false otherwise
     */
    public function exists($conditions) {
        return $this->count($conditions) > 0;
    }
    
    // ============================================================================
    // UTILITY METHODS
    // ============================================================================
    
    /**
     * Get the last insert ID
     * 
     * @return int The last insert ID
     */
    public function getLastInsertId() {
        return $this->db->insert_id;
    }
    
    /**
     * Get the number of affected rows from the last query
     * 
     * @return int Number of affected rows
     */
    public function getAffectedRows() {
        return $this->db->affected_rows;
    }
    
    /**
     * Execute a raw SQL query
     * 
     * @param string $query The SQL query
     * @param array $params Parameters for prepared statement
     * @return mysqli_result|bool Query result
     */
    public function query($query, $params = []) {
        if (empty($params)) {
            return $this->db->query($query);
        }
        
        $stmt = $this->db->prepare($query);
        
        if (!$stmt) {
            throw new Exception('Database query preparation failed: ' . $this->db->error);
        }
        
        if (!empty($params)) {
            $types = str_repeat('s', count($params));
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        return $stmt->get_result();
    }
    
    /**
     * Begin a database transaction
     * 
     * @return bool True on success, false on failure
     */
    public function beginTransaction() {
        return $this->db->begin_transaction();
    }
    
    /**
     * Commit a database transaction
     * 
     * @return bool True on success, false on failure
     */
    public function commit() {
        return $this->db->commit();
    }
    
    /**
     * Rollback a database transaction
     * 
     * @return bool True on success, false on failure
     */
    public function rollback() {
        return $this->db->rollback();
    }
    
    /**
     * Get the database connection
     * 
     * @return mysqli Database connection
     */
    public function getConnection() {
        return $this->db;
    }
    
    /**
     * Close the database connection
     */
    public function closeConnection() {
        if ($this->db) {
            $this->db->close();
        }
    }
    
    /**
     * Destructor - Close database connection
     */
    public function __destruct() {
        $this->closeConnection();
    }
}
?>