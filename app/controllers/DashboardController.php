<?php
/**
 * Dashboard Controller
 * Handles main dashboard and home page
 */

require_once APP_ROOT . '/app/controllers/BaseController.php';

class DashboardController extends BaseController {
    private $itemModel;
    private $userModel;

    public function __construct() {
        parent::__construct();
        $this->itemModel = new Item();
        $this->userModel = new User();
    }

    /**
     * Show dashboard
     */
    public function index() {
        $this->requireAuth();

        $error = $this->getGet('error');
        $success = $this->getGet('success');

        // Get statistics based on role
        $stats = [];

        if ($this->hasRole('administrator')) {
            $stats = [
                'total_users' => $this->userModel->getUserCount(),
                'total_items' => $this->itemModel->getCount(),
                'item_stats' => $this->itemModel->getStatistics()
            ];
        } elseif ($this->hasRole('auditor')) {
            $auditModel = new AuditLog();
            $stats = [
                'audit_stats' => $auditModel->getStatistics(),
                'total_items' => $this->itemModel->getCount()
            ];
        } else {
            // Regular user - show their items
            $stats = [
                'my_items' => $this->itemModel->getByUser($this->user['id'], 5, 0),
                'total_items' => $this->itemModel->getCount()
            ];
        }

        $this->view('dashboard/index', [
            'user' => $this->user,
            'stats' => $stats,
            'error' => $error,
            'success' => $success
        ]);
    }

    /**
     * Show home page (for non-authenticated users)
     */
    public function home() {
        if ($this->isLoggedIn()) {
            $this->redirect('index.php?controller=dashboard&action=index');
        }

        $this->view('home', []);
    }
}
