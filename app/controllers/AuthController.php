<?php

/**
 * Auth Controller
 * 
 * Controller untuk menangani autentikasi pengguna
 */

class AuthController extends Controller
{
    private $userModel;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->userModel = $this->model('User');

        // Start session if not already started
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Register page for members
     * 
     * @return void
     */
    public function register()
    {
        // Check if user is already logged in
        if ($this->isLoggedIn()) {
            $this->redirect('/dashboard');
            return;
        }

        // Check if form is submitted
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Sanitize POST data
            try {
                $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

                // Get form data
                $data = [
                    'username' => isset($_POST['username']) ? trim($_POST['username']) : '',
                    'email' => isset($_POST['email']) ? trim($_POST['email']) : '',
                    'password' => isset($_POST['password']) ? trim($_POST['password']) : '',
                    'confirm_password' => isset($_POST['confirm_password']) ? trim($_POST['confirm_password']) : '',
                    'username_err' => '',
                    'email_err' => '',
                    'password_err' => '',
                    'confirm_password_err' => ''
                ];
            } catch (Exception $e) {
                // Log error but don't display to user
                error_log('Error in register form processing: ' . $e->getMessage());

                $data = [
                    'username' => '',
                    'email' => '',
                    'password' => '',
                    'confirm_password' => '',
                    'username_err' => '',
                    'email_err' => '',
                    'password_err' => '',
                    'confirm_password_err' => ''
                ];

                $_SESSION['error_message'] = 'Terjadi kesalahan saat memproses data. Silakan coba lagi.';
            }

            // Validate username
            if (empty($data['username'])) {
                $data['username_err'] = 'Silakan masukkan username';
            } else if ($this->userModel->findUserByUsername($data['username'])) {
                $data['username_err'] = 'Username sudah digunakan';
            }

            // Validate email
            if (empty($data['email'])) {
                $data['email_err'] = 'Silakan masukkan email';
            } else if ($this->userModel->findUserByEmail($data['email'])) {
                $data['email_err'] = 'Email sudah terdaftar';
            }

            // Validate password
            if (empty($data['password'])) {
                $data['password_err'] = 'Silakan masukkan password';
            } else if (strlen($data['password']) < 6) {
                $data['password_err'] = 'Password minimal 6 karakter';
            }

            // Validate confirm password
            if (empty($data['confirm_password'])) {
                $data['confirm_password_err'] = 'Silakan konfirmasi password';
            } else if ($data['password'] != $data['confirm_password']) {
                $data['confirm_password_err'] = 'Password tidak sama';
            }

            // Check for errors
            if (
                empty($data['username_err']) && empty($data['email_err']) &&
                empty($data['password_err']) && empty($data['confirm_password_err'])
            ) {
                // Jangan hash password di sini, biarkan model yang hash
                // $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT); // HAPUS BARIS INI

                // Register user as member
                $userData = [
                    'username' => $data['username'],
                    'email' => $data['email'],
                    'password' => $data['password'], // password plain
                    'role' => 'member' // Set role as member
                ];

                try {
                    if ($this->userModel->register($userData)) {
                        // Set success message
                        $_SESSION['success_message'] = 'Registrasi berhasil, silakan login';
                        $this->redirect('/login');
                    } else {
                        // Set error message
                        $_SESSION['error_message'] = 'Terjadi kesalahan saat mendaftar';
                        $this->view('auth/register', $data);
                    }
                } catch (Exception $e) {
                    // Log error but don't display to user
                    error_log('Error in user registration: ' . $e->getMessage());

                    // Set generic error message
                    $_SESSION['error_message'] = 'Terjadi kesalahan saat mendaftar. Silakan coba lagi.';
                    $this->view('auth/register', $data);
                }
            } else {
                // Load view with errors
                $this->view('auth/register', $data);
            }
        } else {
            // Init data
            $data = [
                'username' => '',
                'email' => '',
                'password' => '',
                'confirm_password' => '',
                'username_err' => '',
                'email_err' => '',
                'password_err' => '',
                'confirm_password_err' => ''
            ];

            // Load view
            $this->view('auth/register', $data);
        }
    }

    /**
     * Login page
     * 
     * @return void
     */
    public function login()
    {
        // Check if user is already logged in
        if ($this->isLoggedIn()) {
            // Gunakan require_once untuk menampilkan dashboard daripada redirect
            // untuk menghindari redirect loop
            require_once BASE_PATH . '/app/controllers/DashboardController.php';
            $dashboard = new DashboardController();
            $dashboard->index();
            return;
        }

        // Check if form is submitted
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Sanitize POST data
            try {
                $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

                // Get form data
                $data = [
                    'username' => isset($_POST['username']) ? trim($_POST['username']) : '',
                    'password' => isset($_POST['password']) ? trim($_POST['password']) : '',
                    'username_err' => '',
                    'password_err' => ''
                ];
            } catch (Exception $e) {
                // Log error but don't display to user
                error_log('Error in login form processing: ' . $e->getMessage());

                $data = [
                    'username' => '',
                    'password' => '',
                    'username_err' => '',
                    'password_err' => ''
                ];

                $_SESSION['error_message'] = 'Terjadi kesalahan saat memproses data. Silakan coba lagi.';
            }

            // Validate username
            if (empty($data['username'])) {
                $data['username_err'] = 'Silakan masukkan username';
            }

            // Validate password
            if (empty($data['password'])) {
                $data['password_err'] = 'Silakan masukkan password';
            }

            // Check for errors
            if (empty($data['username_err']) && empty($data['password_err'])) {
                // Check and set logged in user
                try {
                    $loggedInUser = $this->userModel->login($data['username'], $data['password']);

                    if ($loggedInUser) {
                        // Create session
                        $this->createUserSession($loggedInUser);
                        $this->redirect('/dashboard');
                    } else {
                        $data['password_err'] = 'Username atau password salah';
                        $this->view('auth/login', $data);
                    }
                } catch (Exception $e) {
                    // Log error but don't display to user
                    error_log('Error in login process: ' . $e->getMessage());

                    // Set generic error message
                    $_SESSION['error_message'] = 'Terjadi kesalahan saat login. Silakan coba lagi.';
                    $this->view('auth/login', $data);
                }
            } else {
                // Load view with errors
                $this->view('auth/login', $data);
            }
        } else {
            // Init data
            $data = [
                'username' => '',
                'password' => '',
                'username_err' => '',
                'password_err' => ''
            ];

            // Load view
            $this->view('auth/login', $data);
        }
    }

    /**
     * Create user session
     * 
     * @param array $user User data
     * @return void
     */
    private function createUserSession($user)
    {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
    }

    /**
     * Logout user
     * 
     * @return void
     */
    public function logout()
    {
        // Unset session variables
        unset($_SESSION['user_id']);
        unset($_SESSION['username']);
        unset($_SESSION['role']);

        // Destroy session
        session_destroy();

        // Redirect to login
        $this->redirect('/login');
    }
}
