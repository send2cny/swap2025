<?php $page_title = 'View User'; ?>
<?php include APP_ROOT . '/app/views/layouts/header.php'; ?>

<div class="container">
    <div class="page-header">
        <h1>User Details</h1>
        <div class="actions">
            <?php if ($user['id'] != $current_user['id']): ?>
                <a href="index.php?controller=admin&action=deleteUser&id=<?php echo $user['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete User</a>
            <?php endif; ?>
            <a href="index.php?controller=admin&action=users" class="btn btn-secondary">Back to List</a>
        </div>
    </div>

    <div class="user-details">
        <div class="detail-section">
            <h2>User Information</h2>
            <table class="info-table">
                <tr>
                    <th>ID:</th>
                    <td><?php echo $user['id']; ?></td>
                </tr>
                <tr>
                    <th>Username:</th>
                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                </tr>
                <tr>
                    <th>Email:</th>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                </tr>
                <tr>
                    <th>Full Name:</th>
                    <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                </tr>
                <tr>
                    <th>Role:</th>
                    <td><span class="badge badge-<?php echo $user['role']; ?>"><?php echo $user['role']; ?></span></td>
                </tr>
                <tr>
                    <th>Status:</th>
                    <td><span class="badge badge-<?php echo $user['status']; ?>"><?php echo $user['status']; ?></span></td>
                </tr>
                <tr>
                    <th>Email Verified:</th>
                    <td><?php echo $user['email_verified'] ? 'Yes' : 'No'; ?></td>
                </tr>
                <tr>
                    <th>Created At:</th>
                    <td><?php echo date('F j, Y g:i A', strtotime($user['created_at'])); ?></td>
                </tr>
                <tr>
                    <th>Last Updated:</th>
                    <td><?php echo date('F j, Y g:i A', strtotime($user['updated_at'])); ?></td>
                </tr>
                <tr>
                    <th>Last Login:</th>
                    <td><?php echo $user['last_login'] ? date('F j, Y g:i A', strtotime($user['last_login'])) : 'Never'; ?></td>
                </tr>
            </table>
        </div>

        <div class="detail-section">
            <h2>User's Items (<?php echo count($items); ?>)</h2>
            <?php if (empty($items)): ?>
                <p>This user has not created any items.</p>
            <?php else: ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Created</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['title']); ?></td>
                                <td><?php echo htmlspecialchars($item['category'] ?? 'N/A'); ?></td>
                                <td>$<?php echo number_format($item['price'], 2); ?></td>
                                <td><span class="badge badge-<?php echo $item['status']; ?>"><?php echo $item['status']; ?></span></td>
                                <td><?php echo date('Y-m-d', strtotime($item['created_at'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include APP_ROOT . '/app/views/layouts/footer.php'; ?>
