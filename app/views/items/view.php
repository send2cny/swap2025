<?php $page_title = 'View Item'; ?>
<?php include APP_ROOT . '/app/views/layouts/header.php'; ?>

<div class="container">
    <div class="page-header">
        <h1><?php echo htmlspecialchars($item['title']); ?></h1>
        <div class="actions">
            <?php if (isset($current_user) && is_array($current_user) && ((isset($current_user['id']) && $item['created_by'] == $current_user['id']) || (isset($current_user['role']) && $current_user['role'] === 'administrator'))): ?>
                <a href="index.php?controller=item&action=edit&id=<?php echo $item['id']; ?>" class="btn btn-primary">Edit</a>
                <a href="index.php?controller=item&action=delete&id=<?php echo $item['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
            <?php endif; ?>
            <a href="index.php?controller=item&action=index" class="btn btn-secondary">Back to List</a>
        </div>
    </div>

    <div class="item-details">
        <div class="detail-section">
            <h2>Item Information</h2>
            <table class="info-table">
                <tr>
                    <th>ID:</th>
                    <td><?php echo $item['id']; ?></td>
                </tr>
                <tr>
                    <th>Title:</th>
                    <td><?php echo htmlspecialchars($item['title']); ?></td>
                </tr>
                <tr>
                    <th>Description:</th>
                    <td><?php echo nl2br(htmlspecialchars($item['description'] ?? 'No description')); ?></td>
                </tr>
                <tr>
                    <th>Category:</th>
                    <td><?php echo htmlspecialchars($item['category'] ?? 'Uncategorized'); ?></td>
                </tr>
                <tr>
                    <th>Price:</th>
                    <td>$<?php echo number_format($item['price'], 2); ?></td>
                </tr>
                <tr>
                    <th>Quantity:</th>
                    <td><?php echo $item['quantity']; ?></td>
                </tr>
                <tr>
                    <th>Status:</th>
                    <td><span class="badge badge-<?php echo $item['status']; ?>"><?php echo $item['status']; ?></span></td>
                </tr>
                <tr>
                    <th>Total Value:</th>
                    <td>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                </tr>
            </table>
        </div>

        <div class="detail-section">
            <h2>Metadata</h2>
            <table class="info-table">
                <tr>
                    <th>Created By:</th>
                    <td><?php echo htmlspecialchars($item['created_by_name']); ?> (<?php echo htmlspecialchars($item['created_by_username']); ?>)</td>
                </tr>
                <tr>
                    <th>Created At:</th>
                    <td><?php echo date('F j, Y g:i A', strtotime($item['created_at'])); ?></td>
                </tr>
                <tr>
                    <th>Last Updated:</th>
                    <td><?php echo date('F j, Y g:i A', strtotime($item['updated_at'])); ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>

<?php include APP_ROOT . '/app/views/layouts/footer.php'; ?>
