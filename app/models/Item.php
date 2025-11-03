<?php
/**
 * Item Model
 * Handles CRUD operations for items
 */

class Item {
    private $db;
    private $conn;

    public $id;
    public $title;
    public $description;
    public $category;
    public $price;
    public $quantity;
    public $status;
    public $created_by;
    public $created_at;
    public $updated_at;

    public function __construct() {
        $this->db = Database::getInstance();
        $this->conn = $this->db->getConnection();
    }

    /**
     * Create a new item
     */
    public function create($title, $description, $category, $price, $quantity, $created_by, $status = 'active') {
        // Validate inputs
        if (empty($title)) {
            return ['success' => false, 'message' => 'Title is required'];
        }

        $query = "INSERT INTO items (title, description, category, price, quantity, status, created_by)
                  VALUES (:title, :description, :category, :price, :quantity, :status, :created_by)";

        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':title', $title);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':category', $category);
            $stmt->bindParam(':price', $price);
            $stmt->bindParam(':quantity', $quantity);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':created_by', $created_by);

            if ($stmt->execute()) {
                $item_id = $this->conn->lastInsertId();
                $this->logAction($created_by, 'ITEM_CREATE', 'items', $item_id);
                return ['success' => true, 'message' => 'Item created successfully', 'item_id' => $item_id];
            }
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Failed to create item: ' . $e->getMessage()];
        }

        return ['success' => false, 'message' => 'Failed to create item'];
    }

    /**
     * Get item by ID
     */
    public function getById($id) {
        $query = "SELECT i.*, u.username as created_by_username, u.full_name as created_by_name
                  FROM items i
                  LEFT JOIN users u ON i.created_by = u.id
                  WHERE i.id = :id LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return $stmt->fetch();
        }
        return null;
    }

    /**
     * Get all items with pagination
     */
    public function getAll($limit = ITEMS_PER_PAGE, $offset = 0, $search = '', $category = '', $status = '') {
        $query = "SELECT i.*, u.username as created_by_username, u.full_name as created_by_name
                  FROM items i
                  LEFT JOIN users u ON i.created_by = u.id
                  WHERE 1=1";

        $params = [];

        // Add search filter
        if (!empty($search)) {
            $query .= " AND (i.title LIKE :search OR i.description LIKE :search)";
            $params[':search'] = "%{$search}%";
        }

        // Add category filter
        if (!empty($category)) {
            $query .= " AND i.category = :category";
            $params[':category'] = $category;
        }

        // Add status filter
        if (!empty($status)) {
            $query .= " AND i.status = :status";
            $params[':status'] = $status;
        }

        $query .= " ORDER BY i.created_at DESC LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($query);

        // Bind search/filter parameters
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
     * Update an item
     */
    public function update($id, $title, $description, $category, $price, $quantity, $status, $user_id) {
        // Get old values for audit
        $old_item = $this->getById($id);

        if (!$old_item) {
            return ['success' => false, 'message' => 'Item not found'];
        }

        // Validate inputs
        if (empty($title)) {
            return ['success' => false, 'message' => 'Title is required'];
        }

        $query = "UPDATE items SET
                  title = :title,
                  description = :description,
                  category = :category,
                  price = :price,
                  quantity = :quantity,
                  status = :status
                  WHERE id = :id";

        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':title', $title);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':category', $category);
            $stmt->bindParam(':price', $price);
            $stmt->bindParam(':quantity', $quantity);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':id', $id);

            if ($stmt->execute()) {
                $new_item = $this->getById($id);
                $this->logAction($user_id, 'ITEM_UPDATE', 'items', $id, json_encode($old_item), json_encode($new_item));
                return ['success' => true, 'message' => 'Item updated successfully'];
            }
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Failed to update item: ' . $e->getMessage()];
        }

        return ['success' => false, 'message' => 'Failed to update item'];
    }

    /**
     * Delete an item
     */
    public function delete($id, $user_id) {
        // Get item for audit
        $item = $this->getById($id);

        if (!$item) {
            return ['success' => false, 'message' => 'Item not found'];
        }

        $query = "DELETE FROM items WHERE id = :id";

        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);

            if ($stmt->execute()) {
                $this->logAction($user_id, 'ITEM_DELETE', 'items', $id, json_encode($item));
                return ['success' => true, 'message' => 'Item deleted successfully'];
            }
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Failed to delete item: ' . $e->getMessage()];
        }

        return ['success' => false, 'message' => 'Failed to delete item'];
    }

    /**
     * Get total count of items (for pagination)
     */
    public function getCount($search = '', $category = '', $status = '') {
        $query = "SELECT COUNT(*) as count FROM items WHERE 1=1";

        $params = [];

        // Add search filter
        if (!empty($search)) {
            $query .= " AND (title LIKE :search OR description LIKE :search)";
            $params[':search'] = "%{$search}%";
        }

        // Add category filter
        if (!empty($category)) {
            $query .= " AND category = :category";
            $params[':category'] = $category;
        }

        // Add status filter
        if (!empty($status)) {
            $query .= " AND status = :status";
            $params[':status'] = $status;
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
     * Get all categories
     */
    public function getCategories() {
        $query = "SELECT DISTINCT category FROM items WHERE category IS NOT NULL AND category != '' ORDER BY category";
        $stmt = $this->conn->query($query);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Get items by user
     */
    public function getByUser($user_id, $limit = ITEMS_PER_PAGE, $offset = 0) {
        $query = "SELECT i.*, u.username as created_by_username, u.full_name as created_by_name
                  FROM items i
                  LEFT JOIN users u ON i.created_by = u.id
                  WHERE i.created_by = :user_id
                  ORDER BY i.created_at DESC
                  LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Get statistics
     */
    public function getStatistics() {
        $stats = [];

        // Total items
        $query = "SELECT COUNT(*) as total FROM items";
        $stmt = $this->conn->query($query);
        $stats['total'] = $stmt->fetch()['total'];

        // Active items
        $query = "SELECT COUNT(*) as active FROM items WHERE status = 'active'";
        $stmt = $this->conn->query($query);
        $stats['active'] = $stmt->fetch()['active'];

        // Total value
        $query = "SELECT SUM(price * quantity) as total_value FROM items WHERE status = 'active'";
        $stmt = $this->conn->query($query);
        $stats['total_value'] = $stmt->fetch()['total_value'] ?? 0;

        // Items by category
        $query = "SELECT category, COUNT(*) as count FROM items GROUP BY category ORDER BY count DESC";
        $stmt = $this->conn->query($query);
        $stats['by_category'] = $stmt->fetchAll();

        return $stats;
    }

    /**
     * Log action to audit log
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
}
