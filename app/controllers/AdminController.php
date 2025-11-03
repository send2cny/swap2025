<?php
/**
 * Admin Controller
 * Handles administrative tasks (user management, etc.)
 */

require_once APP_ROOT . '/app/controllers/BaseController.php';

class AdminController extends BaseController {
    private $userModel;

    public function __construct() {
        parent::__construct();
        $this->userModel = new User();
    }

    /**
     * Show admin dashboard
     */
    public function index() {
        $this->requireRole(['administrator']);

        $this->view('admin/index', [
            'user' => $this->user
        ]);
    }

    /**
     * List all users
     */
    public function users() {
        $this->requireRole(['administrator']);

        $page = max(1, (int)$this->getGet('page', 1));
        $total = $this->userModel->getUserCount();
        $pagination = $this->paginate($total, ITEMS_PER_PAGE, $page);

        $users = $this->userModel->getAllUsers(ITEMS_PER_PAGE, $pagination['offset']);

        $this->view('admin/users', [
            'users' => $users,
            'pagination' => $pagination,
            'success' => $this->getGet('success'),
            'error' => $this->getGet('error')
        ]);
    }

    /**
     * Show user details
     */
    public function viewUser() {
        $this->requireRole(['administrator']);

        $id = (int)$this->getGet('id');

        if (!$id) {
            $this->redirect('index.php?controller=admin&action=users&error=' . urlencode('Invalid user ID'));
        }

        $user = $this->userModel->getUserById($id);

        if (!$user) {
            $this->redirect('index.php?controller=admin&action=users&error=' . urlencode('User not found'));
        }

        // Get user's items
        $itemModel = new Item();
        $items = $itemModel->getByUser($id, 10, 0);

        $this->view('admin/view_user', [
            'user' => $user,
            'items' => $items
        ]);
    }

    /**
     * Delete user
     */
    public function deleteUser() {
        $this->requireRole(['administrator']);

        $id = (int)$this->getGet('id');

        if (!$id) {
            $this->redirect('index.php?controller=admin&action=users&error=' . urlencode('Invalid user ID'));
        }

        // Prevent self-deletion
        if ($id == $this->user['id']) {
            $this->redirect('index.php?controller=admin&action=users&error=' . urlencode('Cannot delete your own account'));
        }

        $confirm = $this->getGet('confirm');

        if ($confirm === 'yes') {
            $result = $this->userModel->deleteUser($id);

            if ($result['success']) {
                $this->redirect('index.php?controller=admin&action=users&success=' . urlencode($result['message']));
            } else {
                $this->redirect('index.php?controller=admin&action=users&error=' . urlencode($result['message']));
            }
        } else {
            $user = $this->userModel->getUserById($id);

            if (!$user) {
                $this->redirect('index.php?controller=admin&action=users&error=' . urlencode('User not found'));
            }

            $this->view('admin/delete_user', [
                'user' => $user,
                'csrf_token' => $this->generateCsrfToken()
            ]);
        }
    }
}
