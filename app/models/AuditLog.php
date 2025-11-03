<?php
/**
 * AuditLog Model
 * Handles audit log queries for auditors
 */

class AuditLog {
    private $db;
    private $conn;

    public function __construct() {
        $this->db = Database::getInstance();
        $this->conn = $this->db->getConnection();
    }

    /**
     * Get all audit logs with pagination
     */
    public function getAll($limit = 50, $offset = 0, $user_id = null, $action = null, $table_name = null) {
        $query = "SELECT al.*, u.username, u.full_name, u.role
                  FROM audit_logs al
                  LEFT JOIN users u ON al.user_id = u.id
                  WHERE 1=1";

        $params = [];

        // Filter by user
        if ($user_id !== null) {
            $query .= " AND al.user_id = :user_id";
            $params[':user_id'] = $user_id;
        }

        // Filter by action
        if (!empty($action)) {
            $query .= " AND al.action = :action";
            $params[':action'] = $action;
        }

        // Filter by table
        if (!empty($table_name)) {
            $query .= " AND al.table_name = :table_name";
            $params[':table_name'] = $table_name;
        }

        $query .= " ORDER BY al.created_at DESC LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($query);

        // Bind filter parameters
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        // Bind pagination parameters
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get audit log count
     */
    public function getCount($user_id = null, $action = null, $table_name = null) {
        $query = "SELECT COUNT(*) as count FROM audit_logs WHERE 1=1";

        $params = [];

        // Filter by user
        if ($user_id !== null) {
            $query .= " AND user_id = :user_id";
            $params[':user_id'] = $user_id;
        }

        // Filter by action
        if (!empty($action)) {
            $query .= " AND action = :action";
            $params[':action'] = $action;
        }

        // Filter by table
        if (!empty($table_name)) {
            $query .= " AND table_name = :table_name";
            $params[':table_name'] = $table_name;
        }

        $stmt = $this->conn->prepare($query);

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->execute();
        $result = $stmt->fetch();
        return $result['count'];
    }

    /**
     * Get all distinct actions
     */
    public function getActions() {
        $query = "SELECT DISTINCT action FROM audit_logs ORDER BY action";
        $stmt = $this->conn->query($query);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Get all distinct tables
     */
    public function getTables() {
        $query = "SELECT DISTINCT table_name FROM audit_logs WHERE table_name IS NOT NULL ORDER BY table_name";
        $stmt = $this->conn->query($query);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Get audit statistics
     */
    public function getStatistics() {
        $stats = [];

        // Total logs
        $query = "SELECT COUNT(*) as total FROM audit_logs";
        $stmt = $this->conn->query($query);
        $stats['total'] = $stmt->fetch()['total'];

        // Logs by action
        $query = "SELECT action, COUNT(*) as count FROM audit_logs GROUP BY action ORDER BY count DESC";
        $stmt = $this->conn->query($query);
        $stats['by_action'] = $stmt->fetchAll();

        // Logs by table
        $query = "SELECT table_name, COUNT(*) as count FROM audit_logs WHERE table_name IS NOT NULL GROUP BY table_name ORDER BY count DESC";
        $stmt = $this->conn->query($query);
        $stats['by_table'] = $stmt->fetchAll();

        // Recent activity (last 24 hours)
        $query = "SELECT COUNT(*) as count FROM audit_logs WHERE created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)";
        $stmt = $this->conn->query($query);
        $stats['last_24_hours'] = $stmt->fetch()['count'];

        // Top users by activity
        $query = "SELECT u.username, u.full_name, COUNT(al.id) as action_count
                  FROM audit_logs al
                  LEFT JOIN users u ON al.user_id = u.id
                  GROUP BY al.user_id, u.username, u.full_name
                  ORDER BY action_count DESC
                  LIMIT 10";
        $stmt = $this->conn->query($query);
        $stats['top_users'] = $stmt->fetchAll();

        return $stats;
    }

    /**
     * Get logs by date range
     */
    public function getByDateRange($start_date, $end_date, $limit = 100, $offset = 0) {
        $query = "SELECT al.*, u.username, u.full_name
                  FROM audit_logs al
                  LEFT JOIN users u ON al.user_id = u.id
                  WHERE al.created_at BETWEEN :start_date AND :end_date
                  ORDER BY al.created_at DESC
                  LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':start_date', $start_date);
        $stmt->bindParam(':end_date', $end_date);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}
