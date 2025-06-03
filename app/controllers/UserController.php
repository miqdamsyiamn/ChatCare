<?php
/**
 * User Controller
 * 
 * Controller untuk menangani manajemen pengguna (khusus admin)
 */

class UserController extends Controller {
    private $userModel;
    
    /**
     * Constructor
     */
    public function __construct() {
        // Start session if not already started
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        // Check if user is logged in
        if (!$this->isLoggedIn()) {
            $this->redirect('/login');
        }
        
        // Check if user is admin
        if (!$this->hasRole('admin')) {
            $this->redirect('/dashboard');
        }
        
        $this->userModel = $this->model('User');
    }
    
    /**
     * User index page
     * 
     * @return void
     */
    public function index() {
        $users = $this->userModel->getUsers();
        
        $data = [
            'title' => 'Manajemen Pengguna',
            'users' => $users
        ];
        
        $this->view('users/index', $data);
    }
    
    /**
     * Create user page
     * 
     * @return void
     */
    public function create() {
        $data = [
            'title' => 'Tambah Pengguna Baru',
            'username' => '',
            'password' => '',
            'role' => '',
            'username_err' => '',
            'password_err' => '',
            'role_err' => ''
        ];
        
        $this->view('users/create', $data);
    }
    
    /**
     * Store new user
     * 
     * @return void
     */
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Sanitize POST data
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            
            // Get form data
            $data = [
                'title' => 'Tambah Pengguna Baru',
                'username' => trim($_POST['username']),
                'password' => trim($_POST['password']),
                'role' => trim($_POST['role']),
                'username_err' => '',
                'password_err' => '',
                'role_err' => ''
            ];
            
            // Validate username
            if (empty($data['username'])) {
                $data['username_err'] = 'Silakan masukkan username';
            } else {
                // Check if username exists
                if ($this->userModel->getUserByUsername($data['username'])) {
                    $data['username_err'] = 'Username sudah digunakan';
                }
            }
            
            // Validate password
            if (empty($data['password'])) {
                $data['password_err'] = 'Silakan masukkan password';
            } elseif (strlen($data['password']) < 6) {
                $data['password_err'] = 'Password minimal 6 karakter';
            }
            
            // Validate role
            if (empty($data['role'])) {
                $data['role_err'] = 'Silakan pilih role';
            }
            
            // Check for errors
            if (empty($data['username_err']) && empty($data['password_err']) && empty($data['role_err'])) {
                // Create user
                if ($this->userModel->create($data)) {
                    // Set flash message
                    $_SESSION['success_message'] = 'Pengguna berhasil ditambahkan';
                    $this->redirect('/users');
                } else {
                    die('Terjadi kesalahan');
                }
            } else {
                // Load view with errors
                $this->view('users/create', $data);
            }
        } else {
            $this->redirect('/users/create');
        }
    }
    
    /**
     * Edit user page
     * 
     * @return void
     */
    public function edit() {
        if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['id'])) {
            $userId = $_GET['id'];
            $user = $this->userModel->getUserById($userId);
            
            if (!$user) {
                $this->redirect('/users');
            }
            
            $data = [
                'title' => 'Edit Pengguna',
                'user_id' => $user['user_id'],
                'username' => $user['username'],
                'password' => '',
                'role' => $user['role'],
                'username_err' => '',
                'password_err' => '',
                'role_err' => ''
            ];
            
            $this->view('users/edit', $data);
        } else {
            $this->redirect('/users');
        }
    }
    
    /**
     * Update user
     * 
     * @return void
     */
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Sanitize POST data
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            
            // Get form data
            $data = [
                'title' => 'Edit Pengguna',
                'user_id' => trim($_POST['user_id']),
                'username' => trim($_POST['username']),
                'password' => trim($_POST['password']),
                'role' => trim($_POST['role']),
                'username_err' => '',
                'password_err' => '',
                'role_err' => ''
            ];
            
            // Validate username
            if (empty($data['username'])) {
                $data['username_err'] = 'Silakan masukkan username';
            } else {
                // Check if username exists and is not the current user
                $existingUser = $this->userModel->getUserByUsername($data['username']);
                if ($existingUser && $existingUser['user_id'] != $data['user_id']) {
                    $data['username_err'] = 'Username sudah digunakan';
                }
            }
            
            // Validate password (optional for update)
            if (!empty($data['password']) && strlen($data['password']) < 6) {
                $data['password_err'] = 'Password minimal 6 karakter';
            }
            
            // Validate role
            if (empty($data['role'])) {
                $data['role_err'] = 'Silakan pilih role';
            }
            
            // Check for errors
            if (empty($data['username_err']) && empty($data['password_err']) && empty($data['role_err'])) {
                // Update user
                if ($this->userModel->update($data)) {
                    // Set flash message
                    $_SESSION['success_message'] = 'Pengguna berhasil diperbarui';
                    $this->redirect('/users');
                } else {
                    die('Terjadi kesalahan');
                }
            } else {
                // Load view with errors
                $this->view('users/edit', $data);
            }
        } else {
            $this->redirect('/users');
        }
    }
    
    /**
     * Delete user
     * 
     * @return void
     */
    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $userId = $_POST['user_id'];
            
            // Check if user exists
            $user = $this->userModel->getUserById($userId);
            if (!$user) {
                $this->redirect('/users');
            }
            
            // Delete user
            if ($this->userModel->delete($userId)) {
                // Set flash message
                $_SESSION['success_message'] = 'Pengguna berhasil dihapus';
            } else {
                // Set flash message
                $_SESSION['error_message'] = 'Gagal menghapus pengguna';
            }
            
            $this->redirect('/users');
        } else {
            $this->redirect('/users');
        }
    }
}
