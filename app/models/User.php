<?php
/**
 * User Model
 * Handles user authentication, registration, and management
 */

class User {
    private $db;
    private $conn;

    public $id;
    public $username;
    public $email;
    public $password;
    public $full_name;
    public $role;
    public $status;
    public $email_verified;
    public $created_at;
    public $updated_at;
    public $last_login;

    public function __construct() {
        $this->db = Database::getInstance();
        $this->conn = $this->db->getConnection();
    }

    /**
     * Register a new user
     */
    public function register($username, $email, $password, $full_name, $role = 'user') {
        // Validate inputs
        if (empty($username) || empty($email) || empty($password) || empty($full_name)) {
            return ['success' => false, 'message' => 'All fields are required'];
        }

        // Validate email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Invalid email format'];
        }

        // Validate password length
        if (strlen($password) < PASSWORD_MIN_LENGTH) {
            return ['success' => false, 'message' => 'Password must be at least ' . PASSWORD_MIN_LENGTH . ' characters'];
        }

        // Check if username already exists
        if ($this->usernameExists($username)) {
            return ['success' => false, 'message' => 'Username already exists'];
        }

        // Check if email already exists
        if ($this->emailExists($email)) {
            return ['success' => false, 'message' => 'Email already exists'];
        }

        // Hash password
        $hashed_password = password_hash($password, PASSWORD_HASH_ALGO);

        // Insert user
        $query = "INSERT INTO users (username, email, password, full_name, role, status, email_verified)
                  VALUES (:username, :email, :password, :full_name, :role, 'active', 0)";

        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $hashed_password);
            $stmt->bindParam(':full_name', $full_name);
            $stmt->bindParam(':role', $role);

            if ($stmt->execute()) {
                $user_id = $this->conn->lastInsertId();
                $this->logAction($user_id, 'USER_REGISTER', 'users', $user_id);
                return ['success' => true, 'message' => 'Registration successful', 'user_id' => $user_id];
            }
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Registration failed: ' . $e->getMessage()];
        }

        return ['success' => false, 'message' => 'Registration failed'];
    }

    /**
     * Login user
     */
    public function login($username, $password) {
        if (empty($username) || empty($password)) {
            return ['success' => false, 'message' => 'Username and password are required'];
        }

        $query = "SELECT * FROM users WHERE username = :username OR email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $username);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch();

            // Check if account is active
            if ($user['status'] !== 'active') {
                $this->logAction($user['id'], 'LOGIN_FAILED_INACTIVE', 'users', $user['id']);
                return ['success' => false, 'message' => 'Account is inactive'];
            }

            // Verify password
            if (password_verify($password, $user['password'])) {
                // Update last login
                $this->updateLastLogin($user['id']);

                // Log successful login
                $this->logAction($user['id'], 'LOGIN_SUCCESS', 'users', $user['id']);

                // Return user data (without password)
                unset($user['password']);
                return ['success' => true, 'message' => 'Login successful', 'user' => $user];
            } else {
                $this->logAction($user['id'], 'LOGIN_FAILED_PASSWORD', 'users', $user['id']);
                return ['success' => false, 'message' => 'Invalid password'];
            }
        }

        return ['success' => false, 'message' => 'User not found'];
    }

    /**
     * Get user by ID
     */
    public function getUserById($id) {
        $query = "SELECT id, username, email, full_name, role, status, email_verified, created_at, updated_at, last_login
                  FROM users WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return $stmt->fetch();
        }
        return null;
    }

    /**
     * Get all users (for admin)
     */
    public function getAllUsers($limit = 50, $offset = 0) {
        $query = "SELECT id, username, email, full_name, role, status, email_verified, created_at, last_login
                  FROM users ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Update user profile
     */
    public function updateProfile($id, $email, $full_name) {
        // Get old values for audit
        $old_user = $this->getUserById($id);

        $query = "UPDATE users SET email = :email, full_name = :full_name WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':full_name', $full_name);
        $stmt->bindParam(':id', $id);

        if ($stmt->execute()) {
            $new_user = $this->getUserById($id);
            $this->logAction($id, 'PROFILE_UPDATE', 'users', $id, json_encode($old_user), json_encode($new_user));
            return ['success' => true, 'message' => 'Profile updated successfully'];
        }
        return ['success' => false, 'message' => 'Update failed'];
    }

    /**
     * Update password
     */
    public function updatePassword($id, $old_password, $new_password) {
        // Get user
        $query = "SELECT password FROM users WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $user = $stmt->fetch();

        // Verify old password
        if (!password_verify($old_password, $user['password'])) {
            return ['success' => false, 'message' => 'Current password is incorrect'];
        }

        // Validate new password
        if (strlen($new_password) < PASSWORD_MIN_LENGTH) {
            return ['success' => false, 'message' => 'Password must be at least ' . PASSWORD_MIN_LENGTH . ' characters'];
        }

        // Hash new password
        $hashed_password = password_hash($new_password, PASSWORD_HASH_ALGO);

        // Update password
        $query = "UPDATE users SET password = :password WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':password', $hashed_password);
        $stmt->bindParam(':id', $id);

        if ($stmt->execute()) {
            $this->logAction($id, 'PASSWORD_CHANGE', 'users', $id);
            return ['success' => true, 'message' => 'Password updated successfully'];
        }
        return ['success' => false, 'message' => 'Password update failed'];
    }

    /**
     * Request password reset
     */
    public function requestPasswordReset($email) {
        // Check if user exists
        $query = "SELECT id, username, email, full_name FROM users WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() === 0) {
            // Don't reveal that email doesn't exist
            return ['success' => true, 'message' => 'If the email exists, a reset link has been sent'];
        }

        $user = $stmt->fetch();

        // Generate token
        $token = bin2hex(random_bytes(32));
        $expires_at = date('Y-m-d H:i:s', time() + TOKEN_EXPIRY);

        // Insert token
        $query = "INSERT INTO password_resets (user_id, token, expires_at) VALUES (:user_id, :token, :expires_at)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user['id']);
        $stmt->bindParam(':token', $token);
        $stmt->bindParam(':expires_at', $expires_at);
        $stmt->execute();

        // Send email
        $this->sendPasswordResetEmail($user, $token);

        $this->logAction($user['id'], 'PASSWORD_RESET_REQUEST', 'users', $user['id']);

        return ['success' => true, 'message' => 'Password reset link has been sent to your email'];
    }

    /**
     * Reset password with token
     */
    public function resetPassword($token, $new_password) {
        // Validate token
        $query = "SELECT pr.*, u.id as user_id FROM password_resets pr
                  JOIN users u ON pr.user_id = u.id
                  WHERE pr.token = :token AND pr.expires_at > NOW() AND pr.used = 0 LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':token', $token);
        $stmt->execute();

        if ($stmt->rowCount() === 0) {
            return ['success' => false, 'message' => 'Invalid or expired token'];
        }

        $reset = $stmt->fetch();

        // Validate password
        if (strlen($new_password) < PASSWORD_MIN_LENGTH) {
            return ['success' => false, 'message' => 'Password must be at least ' . PASSWORD_MIN_LENGTH . ' characters'];
        }

        // Hash password
        $hashed_password = password_hash($new_password, PASSWORD_HASH_ALGO);

        // Update password
        $query = "UPDATE users SET password = :password WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':password', $hashed_password);
        $stmt->bindParam(':id', $reset['user_id']);
        $stmt->execute();

        // Mark token as used
        $query = "UPDATE password_resets SET used = 1 WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $reset['id']);
        $stmt->execute();

        $this->logAction($reset['user_id'], 'PASSWORD_RESET_COMPLETE', 'users', $reset['user_id']);

        return ['success' => true, 'message' => 'Password has been reset successfully'];
    }

    /**
     * Check if username exists
     */
    private function usernameExists($username) {
        $query = "SELECT id FROM users WHERE username = :username LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    /**
     * Check if email exists
     */
    private function emailExists($email) {
        $query = "SELECT id FROM users WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    /**
     * Update last login timestamp
     */
    private function updateLastLogin($user_id) {
        $query = "UPDATE users SET last_login = CURRENT_TIMESTAMP WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $user_id);
        $stmt->execute();
    }

    /**
     * Send password reset email
     */
    private function sendPasswordResetEmail($user, $token) {
        if (!EMAIL_ENABLED) {
            return; // Email disabled for testing
        }

        $reset_link = APP_URL . "/public/index.php?controller=auth&action=reset_password&token=" . $token;

        $subject = "Password Reset Request - " . APP_NAME;
        $message = "Hello {$user['full_name']},\n\n";
        $message .= "You requested a password reset. Click the link below to reset your password:\n\n";
        $message .= $reset_link . "\n\n";
        $message .= "This link will expire in " . (TOKEN_EXPIRY / 60) . " minutes.\n\n";
        $message .= "If you didn't request this, please ignore this email.\n\n";
        $message .= "Regards,\n" . APP_NAME;

        $headers = "From: " . EMAIL_FROM_NAME . " <" . EMAIL_FROM . ">\r\n";
        $headers .= "Reply-To: " . EMAIL_FROM . "\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion();

        mail($user['email'], $subject, $message, $headers);
    }

    /**
     * Log user action to audit log
     */
    private function logAction($user_id, $action, $table_name = null, $record_id = null, $old_values = null, $new_values = null) {
        $ip_address = $_SERVER['REMOTE_ADDR'] ?? null;
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? null;

        $query = "INSERT INTO audit_logs (user_id, action, table_name, record_id, old_values, new_values, ip_address, user_agent)
                  VALUES (:user_id, :action, :table_name, :record_id, :old_values, :new_values, :ip_address, :user_agent)";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':action', $action);
        $stmt->bindParam(':table_name', $table_name);
        $stmt->bindParam(':record_id', $record_id);
        $stmt->bindParam(':old_values', $old_values);
        $stmt->bindParam(':new_values', $new_values);
        $stmt->bindParam(':ip_address', $ip_address);
        $stmt->bindParam(':user_agent', $user_agent);
        $stmt->execute();
    }

    /**
     * Get user count
     */
    public function getUserCount() {
        $query = "SELECT COUNT(*) as count FROM users";
        $stmt = $this->conn->query($query);
        $result = $stmt->fetch();
        return $result['count'];
    }

    /**
     * Delete user (admin only)
     */
    public function deleteUser($id) {
        $query = "DELETE FROM users WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);

        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'User deleted successfully'];
        }
        return ['success' => false, 'message' => 'Delete failed'];
    }
}
