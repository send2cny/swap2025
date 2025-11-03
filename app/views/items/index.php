<?php $page_title = 'Items'; ?>
<?php include APP_ROOT . '/app/views/layouts/header.php'; ?>

<div class="container">
    <div class="page-header">
        <h1>Items</h1>
        <a href="index.php?controller=item&action=create" class="btn btn-primary">Create New Item</a>
    </div>

    <?php if (isset($success)): ?>
        <div class="alert alert-success">
            <?php echo htmlspecialchars($success); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($error)): ?>
        <div class="alert alert-error">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <!-- Filters -->
    <div class="filters">
        <form method="GET" action="index.php" class="filter-form">
            <input type="hidden" name="controller" value="item">
            <input type="hidden" name="action" value="index">

            <div class="filter-group">
                <input type="text" name="search" placeholder="Search..." value="<?php echo htmlspecialchars($search); ?>">

                <select name="category">
                    <option value="">All Categories</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo htmlspecialchars($cat); ?>" <?php echo $category === $cat ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <select name="status">
                    <option value="">All Status</option>
                    <option value="active" <?php echo $status === 'active' ? 'selected' : ''; ?>>Active</option>
                    <option value="inactive" <?php echo $status === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                    <option value="discontinued" <?php echo $status === 'discontinued' ? 'selected' : ''; ?>>Discontinued</option>
                </select>

                <button type="submit" class="btn btn-secondary">Filter</button>
                <a href="index.php?controller=item&action=index" class="btn btn-secondary">Clear</a>
            </div>
        </form>
    </div>

    <!-- Items Table -->
    <?php if (empty($items)): ?>
        <div class="empty-state">
            <p>No items found.</p>
            <a href="index.php?controller=item&action=create" class="btn btn-primary">Create Your First Item</a>
        </div>
    <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Status</th>
                    <th>Created By</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?php echo $item['id']; ?></td>
                        <td><?php echo htmlspecialchars($item['title']); ?></td>
                        <td><?php echo htmlspecialchars($item['category'] ?? 'N/A'); ?></td>
                        <td>$<?php echo number_format($item['price'], 2); ?></td>
                        <td><?php echo $item['quantity']; ?></td>
                        <td><span class="badge badge-<?php echo $item['status']; ?>"><?php echo $item['status']; ?></span></td>
                        <td><?php echo htmlspecialchars($item['created_by_username'] ?? 'Unknown'); ?></td>
                        <td class="actions">
                            <a href="index.php?controller=item&action=view&id=<?php echo $item['id']; ?>" class="btn btn-sm">View</a>
                            <?php if (isset($current_user) && is_array($current_user) && ((isset($current_user['id']) && $item['created_by'] == $current_user['id']) || (isset($current_user['role']) && $current_user['role'] === 'administrator'))): ?>
                                <a href="index.php?controller=item&action=edit&id=<?php echo $item['id']; ?>" class="btn btn-sm">Edit</a>
                                <a href="index.php?controller=item&action=delete&id=<?php echo $item['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Pagination -->
        <?php if ($pagination['total_pages'] > 1): ?>
            <div class="pagination">
                <?php if ($pagination['has_prev']): ?>
                    <a href="?controller=item&action=index&page=<?php echo $pagination['current_page'] - 1; ?>&search=<?php echo urlencode($search); ?>&category=<?php echo urlencode($category); ?>&status=<?php echo urlencode($status); ?>" class="btn btn-sm">Previous</a>
                <?php endif; ?>

                <span class="page-info">
                    Page <?php echo $pagination['current_page']; ?> of <?php echo $pagination['total_pages']; ?>
                </span>

                <?php if ($pagination['has_next']): ?>
                    <a href="?controller=item&action=index&page=<?php echo $pagination['current_page'] + 1; ?>&search=<?php echo urlencode($search); ?>&category=<?php echo urlencode($category); ?>&status=<?php echo urlencode($status); ?>" class="btn btn-sm">Next</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php include APP_ROOT . '/app/views/layouts/footer.php'; ?>
