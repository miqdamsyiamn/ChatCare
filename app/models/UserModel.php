<?php
/**
 * User Model
 * 
 * Model untuk menangani operasi terkait pengguna
 */

class UserModel {
    private $db;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->db = new Database();
    }
    
    /**
     * Get all users
     * 
     * @return array
     */
    public function getUsers() {
        $this->db->query("SELECT * FROM user ORDER BY username");
        return $this->db->resultSet();
    }
    
    /**
     * Get user by ID
     * 
     * @param string $id User ID
     * @return array
     */
    public function getUserById($id) {
        $this->db->query("SELECT * FROM user WHERE user_id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }
    
    /**
     * Get user by username
     * 
     * @param string $username Username
     * @return array
     */
    public function getUserByUsername($username) {
        $this->db->query("SELECT * FROM user WHERE username = :username");
        $this->db->bind(':username', $username);
        return $this->db->single();
    }
    
    /**
     * Find user by username - used for registration validation
     * 
     * @param string $username Username
     * @return bool
     */
    public function findUserByUsername($username) {
        $this->db->query("SELECT * FROM user WHERE username = :username");
        $this->db->bind(':username', $username);
        $row = $this->db->single();
        
        // Check if row exists
        return $row ? true : false;
    }
    
    /**
     * Find user by email - used for registration validation
     * 
     * @param string $email Email
     * @return bool
     */
    public function findUserByEmail($email) {
        $this->db->query("SELECT * FROM user WHERE email = :email");
        $this->db->bind(':email', $email);
        $row = $this->db->single();
        
        // Check if row exists
        return $row ? true : false;
    }
    
    /**
     * Create new user
     * 
     * @param array $data User data
     * @return bool
     */
    public function create($data) {
        // Generate UUID for user_id
        $userId = $this->generateUuid();
        
        $this->db->query("INSERT INTO user (user_id, username, email, password, role) VALUES (:user_id, :username, :email, :password, :role)");
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':username', $data['username']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':password', password_hash($data['password'], PASSWORD_DEFAULT));
        $this->db->bind(':role', $data['role']);
        
        return $this->db->execute();
    }
    
    /**
     * Register new member
     * 
     * @param array $data User data
     * @return bool
     */
    public function register($data) {
        return $this->create($data);
    }
    
    /**
     * Update user
     * 
     * @param array $data User data
     * @return bool
     */
    public function update($data) {
        // Check if password is being updated
        if (!empty($data['password'])) {
            $this->db->query("UPDATE user SET username = :username, email = :email, password = :password, role = :role WHERE user_id = :user_id");
            $this->db->bind(':password', password_hash($data['password'], PASSWORD_DEFAULT));
        } else {
            $this->db->query("UPDATE user SET username = :username, email = :email, role = :role WHERE user_id = :user_id");
        }
        
        $this->db->bind(':username', $data['username']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':role', $data['role']);
        $this->db->bind(':user_id', $data['user_id']);
        
        return $this->db->execute();
    }
    
    /**
     * Delete user
     * 
     * @param string $id User ID
     * @return bool
     */
    public function delete($id) {
        $this->db->query("DELETE FROM user WHERE user_id = :user_id");
        $this->db->bind(':user_id', $id);
        return $this->db->execute();
    }
    
    /**
     * Login user
     * 
     * @param string $username Username
     * @param string $password Password
     * @return array|bool User data if login successful, false otherwise
     */
    public function login($username, $password) {
        // Debug: Tampilkan query yang akan dijalankan
        echo '<pre>Mencari user dengan username: ' . $username . '</pre>';
        
        $user = $this->getUserByUsername($username);
        
        // Debug: Tampilkan hasil query
        if ($user) {
            echo '<pre>User ditemukan: ' . $user['username'] . '</pre>';
            echo '<pre>Password terenkripsi: ' . $user['password'] . '</pre>';
            echo '<pre>Role: ' . $user['role'] . '</pre>';
        } else {
            echo '<pre>User tidak ditemukan</pre>';
        }
        
        if (!$user) {
            return false;
        }
        
        // Debug: Cek verifikasi password
        // Coba dengan password_verify terlebih dahulu (jika password di-hash)
        $passwordVerified = password_verify($password, $user['password']);
        
        // Jika password_verify gagal, coba bandingkan langsung (jika password plaintext)
        if (!$passwordVerified) {
            $passwordVerified = ($password === $user['password']);
        }
        
        echo '<pre>Verifikasi password: ' . ($passwordVerified ? 'Berhasil' : 'Gagal') . '</pre>';
        
        if ($passwordVerified) {
            return $user;
        }
        
        return false;
    }
    
    /**
     * Generate UUID v4
     * 
     * @return string UUID
     */
    private function generateUuid() {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }
}
