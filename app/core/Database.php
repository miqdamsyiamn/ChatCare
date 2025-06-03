<?php
/**
 * Database Class
 * 
 * Kelas untuk menangani koneksi dan operasi database
 */

class Database {
    private $host;
    private $username;
    private $password;
    private $database;
    private $charset;
    private $conn;
    private $stmt;
    
    /**
     * Constructor
     */
    public function __construct() {
        // Load database configuration
        $config = require BASE_PATH . '/config/database.php';
        
        $this->host = $config['host'];
        $this->username = $config['username'];
        $this->password = $config['password'];
        $this->database = $config['database'];
        $this->charset = $config['charset'];
        
        // Connect to database
        $this->connect();
    }
    
    /**
     * Connect to database
     * 
     * @return void
     */
    private function connect() {
        $dsn = "mysql:host={$this->host};dbname={$this->database};charset={$this->charset}";
        
        $options = [
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ];
        
        try {
            $this->conn = new PDO($dsn, $this->username, $this->password, $options);
        } catch (PDOException $e) {
            die('Database connection failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Prepare SQL statement
     * 
     * @param string $sql SQL statement
     * @return void
     */
    public function query($sql) {
        $this->stmt = $this->conn->prepare($sql);
    }
    
    /**
     * Bind values to prepared statement
     * 
     * @param string $param Parameter name or position
     * @param mixed $value Value to bind
     * @param mixed $type Parameter type
     * @return void
     */
    public function bind($param, $value, $type = null) {
        if (is_null($type)) {
            switch (true) {
                case is_int($value):
                    $type = PDO::PARAM_INT;
                    break;
                case is_bool($value):
                    $type = PDO::PARAM_BOOL;
                    break;
                case is_null($value):
                    $type = PDO::PARAM_NULL;
                    break;
                default:
                    $type = PDO::PARAM_STR;
            }
        }
        
        $this->stmt->bindValue($param, $value, $type);
    }
    
    /**
     * Execute prepared statement
     * 
     * @return bool
     */
    public function execute() {
        return $this->stmt->execute();
    }
    
    /**
     * Get all records
     * 
     * @return array
     */
    public function resultSet() {
        $this->execute();
        return $this->stmt->fetchAll();
    }
    
    /**
     * Get single record
     * 
     * @return object
     */
    public function single() {
        $this->execute();
        return $this->stmt->fetch();
    }
    
    /**
     * Get row count
     * 
     * @return int
     */
    public function rowCount() {
        return $this->stmt->rowCount();
    }
    
    /**
     * Get last insert ID
     * 
     * @return string
     */
    public function lastInsertId() {
        return $this->conn->lastInsertId();
    }
    
    /**
     * Begin transaction
     * 
     * @return bool
     */
    public function beginTransaction() {
        return $this->conn->beginTransaction();
    }
    
    /**
     * Commit transaction
     * 
     * @return bool
     */
    public function commit() {
        return $this->conn->commit();
    }
    
    /**
     * Rollback transaction
     * 
     * @return bool
     */
    public function rollback() {
        return $this->conn->rollBack();
    }
}
