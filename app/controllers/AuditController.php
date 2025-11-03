<?php
/**
 * Audit Controller
 * Handles audit log viewing (for auditors and administrators)
 */

require_once APP_ROOT . '/app/controllers/BaseController.php';

class AuditController extends BaseController {
    private $auditModel;

    public function __construct() {
        parent::__construct();
        $this->auditModel = new AuditLog();
    }

    /**
     * List all audit logs
     */
    public function index() {
        $this->requireRole(['auditor', 'administrator']);

        // Get filters and pagination
        $page = max(1, (int)$this->getGet('page', 1));
        $user_id = $this->getGet('user_id') ? (int)$this->getGet('user_id') : null;
        $action = $this->sanitize($this->getGet('action', ''));
        $table_name = $this->sanitize($this->getGet('table', ''));

        // Get total count
        $total = $this->auditModel->getCount($user_id, $action, $table_name);

        // Calculate pagination
        $pagination = $this->paginate($total, 50, $page);

        // Get audit logs
        $logs = $this->auditModel->getAll(50, $pagination['offset'], $user_id, $action, $table_name);

        // Get filter options
        $actions = $this->auditModel->getActions();
        $tables = $this->auditModel->getTables();

        $this->view('audit/index', [
            'logs' => $logs,
            'pagination' => $pagination,
            'user_id' => $user_id,
            'action' => $action,
            'table_name' => $table_name,
            'actions' => $actions,
            'tables' => $tables
        ]);
    }

    /**
     * Show audit statistics
     */
    public function statistics() {
        $this->requireRole(['auditor', 'administrator']);

        $stats = $this->auditModel->getStatistics();

        $this->view('audit/statistics', [
            'stats' => $stats
        ]);
    }

    /**
     * Export audit logs to CSV
     */
    public function export() {
        $this->requireRole(['auditor', 'administrator']);

        $start_date = $this->getGet('start_date');
        $end_date = $this->getGet('end_date');

        if (empty($start_date) || empty($end_date)) {
            $this->redirect('index.php?controller=audit&action=index&error=' . urlencode('Please provide date range'));
        }

        $logs = $this->auditModel->getByDateRange($start_date, $end_date, 10000, 0);

        // Set headers for CSV download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="audit_logs_' . date('Y-m-d') . '.csv"');

        $output = fopen('php://output', 'w');

        // Write header
        fputcsv($output, ['ID', 'User', 'Action', 'Table', 'Record ID', 'IP Address', 'Date']);

        // Write data
        foreach ($logs as $log) {
            fputcsv($output, [
                $log['id'],
                $log['username'] ?? 'N/A',
                $log['action'],
                $log['table_name'] ?? 'N/A',
                $log['record_id'] ?? 'N/A',
                $log['ip_address'] ?? 'N/A',
                $log['created_at']
            ]);
        }

        fclose($output);
        exit();
    }
}
