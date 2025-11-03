<?php
/**
 * Base Controller
 * Parent class for all controllers with common functionality
 */

class BaseController {
    protected $user = null;

    public function __construct() {
        $this->startSession();
        $this->checkAuth();
    }

    /**
     * Start session if not already started
     */
    protected function startSession() {
        if (session_status() === PHP_SESSION_NONE) {
            session_name(SESSION_NAME);
            session_set_cookie_params([
                'lifetime' => SESSION_LIFETIME,
                'path' => '/',
                'secure' => SESSION_SECURE,
                'httponly' => SESSION_HTTPONLY,
                'samesite' => 'Lax'
            ]);
            session_start();
        }
    }

    /**
     * Check if user is authenticated
     */
    protected function checkAuth() {
        if (isset($_SESSION['user_id']) && isset($_SESSION['user'])) {
            $this->user = $_SESSION['user'];

            // Check session timeout
            if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > SESSION_LIFETIME)) {
                $this->logout();
                $this->redirect('index.php?controller=auth&action=login&error=session_expired');
            }

            $_SESSION['last_activity'] = time();
        }
    }

    /**
     * Check if user is logged in
     */
    protected function isLoggedIn() {
        return $this->user !== null;
    }

    /**
     * Require authentication
     */
    protected function requireAuth() {
        if (!$this->isLoggedIn()) {
            $this->redirect('index.php?controller=auth&action=login&error=auth_required');
        }
    }

    /**
     * Require specific role
     */
    protected function requireRole($roles) {
        $this->requireAuth();

        if (!is_array($roles)) {
            $roles = [$roles];
        }

        if (!in_array($this->user['role'], $roles)) {
            $this->redirect('index.php?controller=dashboard&action=index&error=permission_denied');
        }
    }

    /**
     * Check if user has role
     */
    protected function hasRole($role) {
        if (!$this->isLoggedIn()) {
            return false;
        }
        return $this->user['role'] === $role;
    }

    /**
     * Set user session
     */
    protected function setUserSession($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user'] = $user;
        $_SESSION['last_activity'] = time();

        // Regenerate session ID for security
        session_regenerate_id(true);
    }

    /**
     * Logout user
     */
    protected function logout() {
        $_SESSION = [];

        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }

        session_destroy();
    }

    /**
     * Redirect to URL
     */
    protected function redirect($url) {
        header("Location: {$url}");
        exit();
    }

    /**
     * Load view
     */
    protected function view($view, $data = []) {
        // Extract data array to variables
        extract($data);

        // Set user data for views
        $current_user = $this->user;

        // Include view file
        $viewFile = APP_ROOT . '/app/views/' . $view . '.php';

        if (file_exists($viewFile)) {
            require_once $viewFile;
        } else {
            die("View not found: {$view}");
        }
    }

    /**
     * Return JSON response
     */
    protected function json($data, $status = 200) {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }

    /**
     * Get POST data
     */
    protected function getPost($key, $default = null) {
        return $_POST[$key] ?? $default;
    }

    /**
     * Get GET data
     */
    protected function getGet($key, $default = null) {
        return $_GET[$key] ?? $default;
    }

    /**
     * Sanitize input
     */
    protected function sanitize($input) {
        if (is_array($input)) {
            foreach ($input as $key => $value) {
                $input[$key] = $this->sanitize($value);
            }
            return $input;
        }
        return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Validate CSRF token
     */
    protected function validateCsrfToken() {
        $token = $_POST['csrf_token'] ?? '';

        if (!isset($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
            die('CSRF token validation failed');
        }
    }

    /**
     * Generate CSRF token
     */
    protected function generateCsrfToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Set flash message
     */
    protected function setFlash($type, $message) {
        $_SESSION['flash'] = [
            'type' => $type,
            'message' => $message
        ];
    }

    /**
     * Get flash message and clear it
     */
    protected function getFlash() {
        if (isset($_SESSION['flash'])) {
            $flash = $_SESSION['flash'];
            unset($_SESSION['flash']);
            return $flash;
        }
        return null;
    }

    /**
     * Validate email
     */
    protected function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    /**
     * Handle file upload
     */
    protected function uploadFile($file, $destination, $allowed_types = ['jpg', 'jpeg', 'png', 'gif']) {
        if (!isset($file['error']) || is_array($file['error'])) {
            return ['success' => false, 'message' => 'Invalid file'];
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'message' => 'Upload error'];
        }

        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (!in_array($extension, $allowed_types)) {
            return ['success' => false, 'message' => 'File type not allowed'];
        }

        $filename = uniqid() . '.' . $extension;
        $filepath = $destination . '/' . $filename;

        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            return ['success' => true, 'filename' => $filename, 'filepath' => $filepath];
        }

        return ['success' => false, 'message' => 'Failed to move uploaded file'];
    }

    /**
     * Paginate results
     */
    protected function paginate($total, $per_page, $current_page = 1) {
        $total_pages = ceil($total / $per_page);
        $current_page = max(1, min($current_page, $total_pages));
        $offset = ($current_page - 1) * $per_page;

        return [
            'total' => $total,
            'per_page' => $per_page,
            'current_page' => $current_page,
            'total_pages' => $total_pages,
            'offset' => $offset,
            'has_prev' => $current_page > 1,
            'has_next' => $current_page < $total_pages
        ];
    }
}
