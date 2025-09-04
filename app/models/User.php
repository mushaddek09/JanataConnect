<?php
require_once 'BaseModel.php';

class User extends BaseModel {
    protected $table = 'users';
    
    public function findByEmail($email) {
        $query = "SELECT * FROM {$this->table} WHERE email = ? AND is_active = 1";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
    
    public function authenticate($email, $password) {
        $user = $this->findByEmail($email);
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }
    
    public function createUser($data) {
        // Hash password
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        return $this->create($data);
    }
    
    public function updatePassword($userId, $newPassword) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        return $this->update($userId, ['password' => $hashedPassword]);
    }
    
    public function getUsersByRole($role, $limit = null, $offset = 0) {
        return $this->where(['role' => $role], $limit, $offset);
    }
    
    public function getOfficialsByDepartment($departmentId) {
        $query = "SELECT * FROM {$this->table} WHERE role = 'official' AND department_id = ? AND is_active = 1";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $departmentId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    public function deactivateUser($userId) {
        return $this->update($userId, ['is_active' => 0]);
    }
    
    public function activateUser($userId) {
        return $this->update($userId, ['is_active' => 1]);
    }
}
?>
