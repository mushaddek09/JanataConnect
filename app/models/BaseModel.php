<?php
// Base Model Class
class BaseModel {
    protected $db;
    protected $table;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }
    
    // Generic CRUD operations
    public function find($id) {
        $query = "SELECT * FROM {$this->table} WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
    
    public function findAll($limit = null, $offset = 0) {
        $query = "SELECT * FROM {$this->table}";
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
    
    public function create($data) {
        $columns = implode(',', array_keys($data));
        $placeholders = implode(',', array_fill(0, count($data), '?'));
        $query = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
        
        $stmt = $this->db->prepare($query);
        $types = str_repeat('s', count($data));
        $stmt->bind_param($types, ...array_values($data));
        
        if ($stmt->execute()) {
            return $this->db->insert_id;
        }
        return false;
    }
    
    public function update($id, $data) {
        $setClause = implode('=?,', array_keys($data)) . '=?';
        $query = "UPDATE {$this->table} SET {$setClause} WHERE id = ?";
        
        $stmt = $this->db->prepare($query);
        $types = str_repeat('s', count($data)) . 'i';
        $values = array_values($data);
        $values[] = $id;
        $stmt->bind_param($types, ...$values);
        
        return $stmt->execute();
    }
    
    public function delete($id) {
        $query = "DELETE FROM {$this->table} WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
    
    public function where($conditions, $limit = null, $offset = 0) {
        $whereClause = implode(' AND ', array_map(function($key) {
            return "{$key} = ?";
        }, array_keys($conditions)));
        
        $query = "SELECT * FROM {$this->table} WHERE {$whereClause}";
        if ($limit) {
            $query .= " LIMIT ? OFFSET ?";
            $stmt = $this->db->prepare($query);
            $types = str_repeat('s', count($conditions)) . 'ii';
            $values = array_values($conditions);
            $values[] = $limit;
            $values[] = $offset;
            $stmt->bind_param($types, ...$values);
        } else {
            $stmt = $this->db->prepare($query);
            $types = str_repeat('s', count($conditions));
            $stmt->bind_param($types, ...array_values($conditions));
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    public function count($conditions = []) {
        if (empty($conditions)) {
            $query = "SELECT COUNT(*) as count FROM {$this->table}";
            $stmt = $this->db->prepare($query);
        } else {
            $whereClause = implode(' AND ', array_map(function($key) {
                return "{$key} = ?";
            }, array_keys($conditions)));
            $query = "SELECT COUNT(*) as count FROM {$this->table} WHERE {$whereClause}";
            $stmt = $this->db->prepare($query);
            $types = str_repeat('s', count($conditions));
            $stmt->bind_param($types, ...array_values($conditions));
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc()['count'];
    }
}
?>
