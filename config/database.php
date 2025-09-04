<?php
// Database Configuration
class Database {
    private $host = 'localhost';
    private $db_name = 'janataconnect';
    private $username = 'root';        // XAMPP default username
    private $password = '';            // XAMPP default password (empty)
    private $conn;
    
    public function getConnection() {
        $this->conn = null;
        
        try {
            // Use XAMPP MySQL socket path
            $socket = '/Applications/XAMPP/xamppfiles/var/mysql/mysql.sock';
            $this->conn = new mysqli($this->host, $this->username, $this->password, $this->db_name, 3306, $socket);
            
            if ($this->conn->connect_error) {
                throw new Exception("Connection failed: " . $this->conn->connect_error);
            }
            
            // Set charset to utf8
            $this->conn->set_charset("utf8");
            
        } catch (Exception $e) {
            echo "Connection error: " . $e->getMessage();
        }
        
        return $this->conn;
    }
    
    public function closeConnection() {
        if ($this->conn) {
            $this->conn->close();
        }
    }
}
?>
