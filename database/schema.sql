-- ==========================================
-- MVC PHP CRUD Application Database Schema
-- For MySQL/MariaDB (XAMPP Compatible)
-- ==========================================

-- Create Database
CREATE DATABASE IF NOT EXISTS mvc_crud_app CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE mvc_crud_app;

-- ==========================================
-- Users Table
-- ==========================================
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role ENUM('user', 'administrator', 'auditor') DEFAULT 'user',
    status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
    email_verified TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    INDEX idx_username (username),
    INDEX idx_email (email),
    INDEX idx_role (role),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==========================================
-- Password Reset Tokens Table
-- ==========================================
CREATE TABLE IF NOT EXISTS password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token VARCHAR(255) NOT NULL UNIQUE,
    expires_at TIMESTAMP NOT NULL,
    used TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_token (token),
    INDEX idx_user_id (user_id),
    INDEX idx_expires (expires_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==========================================
-- User Sessions Table
-- ==========================================
CREATE TABLE IF NOT EXISTS user_sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    session_token VARCHAR(255) NOT NULL UNIQUE,
    ip_address VARCHAR(45),
    user_agent TEXT,
    expires_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_session_token (session_token),
    INDEX idx_user_id (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==========================================
-- Items Table (CRUD Example)
-- ==========================================
CREATE TABLE IF NOT EXISTS items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    category VARCHAR(50),
    price DECIMAL(10, 2),
    quantity INT DEFAULT 0,
    status ENUM('active', 'inactive', 'discontinued') DEFAULT 'active',
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_category (category),
    INDEX idx_status (status),
    INDEX idx_created_by (created_by)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==========================================
-- Audit Log Table
-- ==========================================
CREATE TABLE IF NOT EXISTS audit_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action VARCHAR(50) NOT NULL,
    table_name VARCHAR(50),
    record_id INT,
    old_values TEXT,
    new_values TEXT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_user_id (user_id),
    INDEX idx_action (action),
    INDEX idx_table_name (table_name),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==========================================
-- Insert Default Users
-- ==========================================
-- Default password for all: "password123"
-- Password hash: $2y$10$YlzC49aJ9RZ4GvpuO/LqmevUGvIV3hdPCNu0.pUrskv0eq0jzg8xG

INSERT INTO users (username, email, password, full_name, role, status, email_verified) VALUES
('admin', 'admin@example.com', '$2y$10$YlzC49aJ9RZ4GvpuO/LqmevUGvIV3hdPCNu0.pUrskv0eq0jzg8xG', 'System Administrator', 'administrator', 'active', 1),
('auditor', 'auditor@example.com', '$2y$10$YlzC49aJ9RZ4GvpuO/LqmevUGvIV3hdPCNu0.pUrskv0eq0jzg8xG', 'System Auditor', 'auditor', 'active', 1),
('user', 'user@example.com', '$2y$10$YlzC49aJ9RZ4GvpuO/LqmevUGvIV3hdPCNu0.pUrskv0eq0jzg8xG', 'Regular User', 'user', 'active', 1);

-- ==========================================
-- Insert Sample Items
-- ==========================================
INSERT INTO items (title, description, category, price, quantity, status, created_by) VALUES
('Laptop', 'High-performance laptop for professionals', 'Electronics', 999.99, 15, 'active', 1),
('Office Desk', 'Ergonomic standing desk', 'Furniture', 299.99, 8, 'active', 1),
('Wireless Mouse', 'Bluetooth wireless mouse', 'Electronics', 29.99, 50, 'active', 1),
('Office Chair', 'Comfortable office chair with lumbar support', 'Furniture', 199.99, 12, 'active', 1),
('Monitor', '27-inch 4K monitor', 'Electronics', 399.99, 20, 'active', 1);

-- ==========================================
-- Views for Reporting
-- ==========================================

-- View: User Activity Summary
CREATE OR REPLACE VIEW user_activity_summary AS
SELECT
    u.id,
    u.username,
    u.full_name,
    u.role,
    u.last_login,
    COUNT(DISTINCT i.id) as items_created,
    COUNT(DISTINCT al.id) as actions_performed
FROM users u
LEFT JOIN items i ON u.id = i.created_by
LEFT JOIN audit_logs al ON u.id = al.user_id
GROUP BY u.id, u.username, u.full_name, u.role, u.last_login;

-- View: Recent Audit Logs
CREATE OR REPLACE VIEW recent_audit_logs AS
SELECT
    al.id,
    al.action,
    al.table_name,
    al.record_id,
    u.username,
    u.full_name,
    al.ip_address,
    al.created_at
FROM audit_logs al
LEFT JOIN users u ON al.user_id = u.id
ORDER BY al.created_at DESC
LIMIT 100;

-- ==========================================
-- Cleanup: Remove expired password resets and sessions
-- ==========================================
-- Run this periodically via cron job or scheduled task

DELIMITER $$

CREATE EVENT IF NOT EXISTS cleanup_expired_tokens
ON SCHEDULE EVERY 1 DAY
DO
BEGIN
    DELETE FROM password_resets WHERE expires_at < NOW() OR used = 1;
    DELETE FROM user_sessions WHERE expires_at < NOW();
END$$

DELIMITER ;

-- Enable event scheduler (if not already enabled)
SET GLOBAL event_scheduler = ON;

-- ==========================================
-- Stored Procedures
-- ==========================================

DELIMITER $$

-- Procedure: Log User Action
CREATE PROCEDURE IF NOT EXISTS log_user_action(
    IN p_user_id INT,
    IN p_action VARCHAR(50),
    IN p_table_name VARCHAR(50),
    IN p_record_id INT,
    IN p_old_values TEXT,
    IN p_new_values TEXT,
    IN p_ip_address VARCHAR(45),
    IN p_user_agent TEXT
)
BEGIN
    INSERT INTO audit_logs (user_id, action, table_name, record_id, old_values, new_values, ip_address, user_agent)
    VALUES (p_user_id, p_action, p_table_name, p_record_id, p_old_values, p_new_values, p_ip_address, p_user_agent);
END$$

-- Procedure: Update Last Login
CREATE PROCEDURE IF NOT EXISTS update_last_login(IN p_user_id INT)
BEGIN
    UPDATE users SET last_login = CURRENT_TIMESTAMP WHERE id = p_user_id;
END$$

DELIMITER ;

-- ==========================================
-- Grant Permissions (Optional)
-- ==========================================
-- GRANT ALL PRIVILEGES ON mvc_crud_app.* TO 'mvc_user'@'localhost' IDENTIFIED BY 'mvc_password';
-- FLUSH PRIVILEGES;

-- ==========================================
-- Verification Queries
-- ==========================================
-- SELECT 'Database setup completed successfully!' as status;
-- SELECT COUNT(*) as user_count FROM users;
-- SELECT COUNT(*) as item_count FROM items;
