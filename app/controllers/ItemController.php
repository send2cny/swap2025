<?php
/**
 * Item Controller
 * Handles CRUD operations for items
 */

require_once APP_ROOT . '/app/controllers/BaseController.php';

class ItemController extends BaseController {
    private $itemModel;

    public function __construct() {
        parent::__construct();
        $this->itemModel = new Item();
    }

    /**
     * List all items
     */
    public function index() {
        $this->requireAuth();

        // Get filters and pagination
        $page = max(1, (int)$this->getGet('page', 1));
        $search = $this->sanitize($this->getGet('search', ''));
        $category = $this->sanitize($this->getGet('category', ''));
        $status = $this->sanitize($this->getGet('status', ''));

        // Get total count
        $total = $this->itemModel->getCount($search, $category, $status);

        // Calculate pagination
        $pagination = $this->paginate($total, ITEMS_PER_PAGE, $page);

        // Get items
        $items = $this->itemModel->getAll(ITEMS_PER_PAGE, $pagination['offset'], $search, $category, $status);

        // Get categories for filter
        $categories = $this->itemModel->getCategories();

        $this->view('items/index', [
            'items' => $items,
            'pagination' => $pagination,
            'search' => $search,
            'category' => $category,
            'status' => $status,
            'categories' => $categories,
            'success' => $this->getGet('success'),
            'error' => $this->getGet('error')
        ]);
    }

    /**
     * Show create item form
     */
    public function create() {
        $this->requireAuth();

        $categories = $this->itemModel->getCategories();

        $this->view('items/create', [
            'categories' => $categories,
            'csrf_token' => $this->generateCsrfToken(),
            'error' => $this->getGet('error')
        ]);
    }

    /**
     * Store new item
     */
    public function store() {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('index.php?controller=item&action=create');
        }

        $this->validateCsrfToken();

        $title = $this->sanitize($this->getPost('title'));
        $description = $this->sanitize($this->getPost('description'));
        $category = $this->sanitize($this->getPost('category'));
        $price = (float)$this->getPost('price', 0);
        $quantity = (int)$this->getPost('quantity', 0);
        $status = $this->sanitize($this->getPost('status', 'active'));

        $result = $this->itemModel->create(
            $title,
            $description,
            $category,
            $price,
            $quantity,
            $this->user['id'],
            $status
        );

        if ($result['success']) {
            $this->redirect('index.php?controller=item&action=index&success=' . urlencode($result['message']));
        } else {
            $this->redirect('index.php?controller=item&action=create&error=' . urlencode($result['message']));
        }
    }

    /**
     * Show item details
     */
    public function show() {
        $this->requireAuth();

        $id = (int)$this->getGet('id');

        if (!$id) {
            $this->redirect('index.php?controller=item&action=index&error=' . urlencode('Invalid item ID'));
        }

        $item = $this->itemModel->getById($id);

        if (!$item) {
            $this->redirect('index.php?controller=item&action=index&error=' . urlencode('Item not found'));
        }

        $this->view('items/view', [
            'item' => $item
        ]);
    }

    /**
     * Show edit item form
     */
    public function edit() {
        $this->requireAuth();

        $id = (int)$this->getGet('id');

        if (!$id) {
            $this->redirect('index.php?controller=item&action=index&error=' . urlencode('Invalid item ID'));
        }

        $item = $this->itemModel->getById($id);

        if (!$item) {
            $this->redirect('index.php?controller=item&action=index&error=' . urlencode('Item not found'));
        }

        // Check permission - only creator or administrator can edit
        if ($item['created_by'] != $this->user['id'] && !$this->hasRole('administrator')) {
            $this->redirect('index.php?controller=item&action=index&error=' . urlencode('Permission denied'));
        }

        $categories = $this->itemModel->getCategories();

        $this->view('items/edit', [
            'item' => $item,
            'categories' => $categories,
            'csrf_token' => $this->generateCsrfToken(),
            'error' => $this->getGet('error')
        ]);
    }

    /**
     * Update item
     */
    public function update() {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('index.php?controller=item&action=index');
        }

        $this->validateCsrfToken();

        $id = (int)$this->getPost('id');

        if (!$id) {
            $this->redirect('index.php?controller=item&action=index&error=' . urlencode('Invalid item ID'));
        }

        $item = $this->itemModel->getById($id);

        if (!$item) {
            $this->redirect('index.php?controller=item&action=index&error=' . urlencode('Item not found'));
        }

        // Check permission
        if ($item['created_by'] != $this->user['id'] && !$this->hasRole('administrator')) {
            $this->redirect('index.php?controller=item&action=index&error=' . urlencode('Permission denied'));
        }

        $title = $this->sanitize($this->getPost('title'));
        $description = $this->sanitize($this->getPost('description'));
        $category = $this->sanitize($this->getPost('category'));
        $price = (float)$this->getPost('price', 0);
        $quantity = (int)$this->getPost('quantity', 0);
        $status = $this->sanitize($this->getPost('status', 'active'));

        $result = $this->itemModel->update(
            $id,
            $title,
            $description,
            $category,
            $price,
            $quantity,
            $status,
            $this->user['id']
        );

        if ($result['success']) {
            $this->redirect('index.php?controller=item&action=show&id=' . $id . '&success=' . urlencode($result['message']));
        } else {
            $this->redirect('index.php?controller=item&action=edit&id=' . $id . '&error=' . urlencode($result['message']));
        }
    }

    /**
     * Delete item
     */
    public function delete() {
        $this->requireAuth();

        $id = (int)$this->getGet('id');

        if (!$id) {
            $this->redirect('index.php?controller=item&action=index&error=' . urlencode('Invalid item ID'));
        }

        $item = $this->itemModel->getById($id);

        if (!$item) {
            $this->redirect('index.php?controller=item&action=index&error=' . urlencode('Item not found'));
        }

        // Check permission - only creator or administrator can delete
        if ($item['created_by'] != $this->user['id'] && !$this->hasRole('administrator')) {
            $this->redirect('index.php?controller=item&action=index&error=' . urlencode('Permission denied'));
        }

        // Confirm deletion
        $confirm = $this->getGet('confirm');

        if ($confirm === 'yes') {
            $result = $this->itemModel->delete($id, $this->user['id']);

            if ($result['success']) {
                $this->redirect('index.php?controller=item&action=index&success=' . urlencode($result['message']));
            } else {
                $this->redirect('index.php?controller=item&action=index&error=' . urlencode($result['message']));
            }
        } else {
            // Show confirmation page
            $this->view('items/delete', [
                'item' => $item,
                'csrf_token' => $this->generateCsrfToken()
            ]);
        }
    }
}
