<?php
/**
 * Authentication Controller
 * Handles login, registration, logout, and password reset
 */

require_once APP_ROOT . '/app/controllers/BaseController.php';

class AuthController extends BaseController {
    private $userModel;

    public function __construct() {
        parent::__construct();
        $this->userModel = new User();
    }

    /**
     * Show login page
     */
    public function login() {
        // If already logged in, redirect to dashboard
        if ($this->isLoggedIn()) {
            $this->redirect('index.php?controller=dashboard&action=index');
        }

        $error = $this->getGet('error');
        $success = $this->getGet('success');

        $this->view('auth/login', [
            'error' => $error,
            'success' => $success,
            'csrf_token' => $this->generateCsrfToken()
        ]);
    }

    /**
     * Process login
     */
    public function doLogin() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('index.php?controller=auth&action=login');
        }

        $this->validateCsrfToken();

        $username = $this->sanitize($this->getPost('username'));
        $password = $this->getPost('password');
        $remember = $this->getPost('remember');

        $result = $this->userModel->login($username, $password);

        if ($result['success']) {
            $this->setUserSession($result['user']);

            // Set remember me cookie if checked
            if ($remember) {
                setcookie('remember_user', $username, time() + (86400 * 30), '/');
            }

            $this->redirect('index.php?controller=dashboard&action=index');
        } else {
            $this->redirect('index.php?controller=auth&action=login&error=' . urlencode($result['message']));
        }
    }

    /**
     * Show registration page
     */
    public function register() {
        // If already logged in, redirect to dashboard
        if ($this->isLoggedIn()) {
            $this->redirect('index.php?controller=dashboard&action=index');
        }

        $error = $this->getGet('error');

        $this->view('auth/register', [
            'error' => $error,
            'csrf_token' => $this->generateCsrfToken()
        ]);
    }

    /**
     * Process registration
     */
    public function doRegister() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('index.php?controller=auth&action=register');
        }

        $this->validateCsrfToken();

        $username = $this->sanitize($this->getPost('username'));
        $email = $this->sanitize($this->getPost('email'));
        $password = $this->getPost('password');
        $confirm_password = $this->getPost('confirm_password');
        $full_name = $this->sanitize($this->getPost('full_name'));

        // Validate password match
        if ($password !== $confirm_password) {
            $this->redirect('index.php?controller=auth&action=register&error=' . urlencode('Passwords do not match'));
        }

        $result = $this->userModel->register($username, $email, $password, $full_name);

        if ($result['success']) {
            $this->redirect('index.php?controller=auth&action=login&success=' . urlencode('Registration successful! Please login.'));
        } else {
            $this->redirect('index.php?controller=auth&action=register&error=' . urlencode($result['message']));
        }
    }

    /**
     * Logout
     */
    public function logout() {
        parent::logout();
        $this->redirect('index.php?controller=auth&action=login&success=' . urlencode('Logged out successfully'));
    }

    /**
     * Show forgot password page
     */
    public function forgotPassword() {
        if ($this->isLoggedIn()) {
            $this->redirect('index.php?controller=dashboard&action=index');
        }

        $error = $this->getGet('error');
        $success = $this->getGet('success');

        $this->view('auth/forgot_password', [
            'error' => $error,
            'success' => $success,
            'csrf_token' => $this->generateCsrfToken()
        ]);
    }

    /**
     * Process forgot password request
     */
    public function doForgotPassword() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('index.php?controller=auth&action=forgotPassword');
        }

        $this->validateCsrfToken();

        $email = $this->sanitize($this->getPost('email'));

        $result = $this->userModel->requestPasswordReset($email);

        $this->redirect('index.php?controller=auth&action=forgotPassword&success=' . urlencode($result['message']));
    }

    /**
     * Show reset password page
     */
    public function resetPassword() {
        if ($this->isLoggedIn()) {
            $this->redirect('index.php?controller=dashboard&action=index');
        }

        $token = $this->getGet('token');

        if (empty($token)) {
            $this->redirect('index.php?controller=auth&action=login&error=' . urlencode('Invalid reset link'));
        }

        $error = $this->getGet('error');

        $this->view('auth/reset_password', [
            'token' => $token,
            'error' => $error,
            'csrf_token' => $this->generateCsrfToken()
        ]);
    }

    /**
     * Process password reset
     */
    public function doResetPassword() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('index.php?controller=auth&action=login');
        }

        $this->validateCsrfToken();

        $token = $this->sanitize($this->getPost('token'));
        $password = $this->getPost('password');
        $confirm_password = $this->getPost('confirm_password');

        // Validate password match
        if ($password !== $confirm_password) {
            $this->redirect('index.php?controller=auth&action=resetPassword&token=' . $token . '&error=' . urlencode('Passwords do not match'));
        }

        $result = $this->userModel->resetPassword($token, $password);

        if ($result['success']) {
            $this->redirect('index.php?controller=auth&action=login&success=' . urlencode($result['message']));
        } else {
            $this->redirect('index.php?controller=auth&action=resetPassword&token=' . $token . '&error=' . urlencode($result['message']));
        }
    }

    /**
     * Show change password page (for logged-in users)
     */
    public function changePassword() {
        $this->requireAuth();

        $error = $this->getGet('error');
        $success = $this->getGet('success');

        $this->view('auth/change_password', [
            'error' => $error,
            'success' => $success,
            'csrf_token' => $this->generateCsrfToken()
        ]);
    }

    /**
     * Process change password
     */
    public function doChangePassword() {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('index.php?controller=auth&action=changePassword');
        }

        $this->validateCsrfToken();

        $old_password = $this->getPost('old_password');
        $new_password = $this->getPost('new_password');
        $confirm_password = $this->getPost('confirm_password');

        // Validate password match
        if ($new_password !== $confirm_password) {
            $this->redirect('index.php?controller=auth&action=changePassword&error=' . urlencode('Passwords do not match'));
        }

        $result = $this->userModel->updatePassword($this->user['id'], $old_password, $new_password);

        if ($result['success']) {
            $this->redirect('index.php?controller=auth&action=changePassword&success=' . urlencode($result['message']));
        } else {
            $this->redirect('index.php?controller=auth&action=changePassword&error=' . urlencode($result['message']));
        }
    }

    /**
     * Show profile page
     */
    public function profile() {
        $this->requireAuth();

        $error = $this->getGet('error');
        $success = $this->getGet('success');

        $this->view('auth/profile', [
            'user' => $this->user,
            'error' => $error,
            'success' => $success,
            'csrf_token' => $this->generateCsrfToken()
        ]);
    }

    /**
     * Update profile
     */
    public function updateProfile() {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('index.php?controller=auth&action=profile');
        }

        $this->validateCsrfToken();

        $email = $this->sanitize($this->getPost('email'));
        $full_name = $this->sanitize($this->getPost('full_name'));

        $result = $this->userModel->updateProfile($this->user['id'], $email, $full_name);

        if ($result['success']) {
            // Update session with new data
            $updated_user = $this->userModel->getUserById($this->user['id']);
            $this->setUserSession($updated_user);

            $this->redirect('index.php?controller=auth&action=profile&success=' . urlencode($result['message']));
        } else {
            $this->redirect('index.php?controller=auth&action=profile&error=' . urlencode($result['message']));
        }
    }
}
