<?php $page_title = 'Delete Item'; ?>
<?php include APP_ROOT . '/app/views/layouts/header.php'; ?>

<div class="container">
    <div class="page-header">
        <h1>Delete Item</h1>
    </div>

    <div class="confirm-delete">
        <div class="alert alert-warning">
            <strong>Warning!</strong> This action cannot be undone.
        </div>

        <div class="item-info">
            <h2>Are you sure you want to delete this item?</h2>
            <table class="info-table">
                <tr>
                    <th>Title:</th>
                    <td><?php echo htmlspecialchars($item['title']); ?></td>
                </tr>
                <tr>
                    <th>Category:</th>
                    <td><?php echo htmlspecialchars($item['category'] ?? 'N/A'); ?></td>
                </tr>
                <tr>
                    <th>Price:</th>
                    <td>$<?php echo number_format($item['price'], 2); ?></td>
                </tr>
                <tr>
                    <th>Quantity:</th>
                    <td><?php echo $item['quantity']; ?></td>
                </tr>
            </table>
        </div>

        <div class="form-actions">
            <a href="index.php?controller=item&action=delete&id=<?php echo $item['id']; ?>&confirm=yes" class="btn btn-danger">Yes, Delete Item</a>
            <a href="index.php?controller=item&action=view&id=<?php echo $item['id']; ?>" class="btn btn-secondary">Cancel</a>
        </div>
    </div>
</div>

<?php include APP_ROOT . '/app/views/layouts/footer.php'; ?>
