<?php $page_title = 'Dashboard'; ?>
<?php include APP_ROOT . '/app/views/layouts/header.php'; ?>

<div class="container">
    <div class="page-header">
        <h1>Dashboard</h1>
        <p>Welcome back, <?php echo htmlspecialchars($user['full_name']); ?>!</p>
    </div>

    <?php if (isset($error)): ?>
        <div class="alert alert-error">
            <?php
            $error_messages = [
                'permission_denied' => 'Permission denied. You do not have access to that resource.'
            ];
            echo htmlspecialchars($error_messages[$error] ?? $error);
            ?>
        </div>
    <?php endif; ?>

    <?php if (isset($success)): ?>
        <div class="alert alert-success">
            <?php echo htmlspecialchars($success); ?>
        </div>
    <?php endif; ?>

    <!-- Administrator Dashboard -->
    <?php if ($user['role'] === 'administrator'): ?>
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Users</h3>
                <p class="stat-number"><?php echo $stats['total_users']; ?></p>
                <a href="index.php?controller=admin&action=users">Manage Users</a>
            </div>
            <div class="stat-card">
                <h3>Total Items</h3>
                <p class="stat-number"><?php echo $stats['total_items']; ?></p>
                <a href="index.php?controller=item&action=index">View Items</a>
            </div>
            <div class="stat-card">
                <h3>Active Items</h3>
                <p class="stat-number"><?php echo $stats['item_stats']['active']; ?></p>
                <a href="index.php?controller=item&action=index">View Items</a>
            </div>
            <div class="stat-card">
                <h3>Total Value</h3>
                <p class="stat-number">$<?php echo number_format($stats['item_stats']['total_value'], 2); ?></p>
                <a href="index.php?controller=item&action=index">View Items</a>
            </div>
        </div>

        <div class="dashboard-section">
            <h2>Items by Category</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th>Category</th>
                        <th>Count</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($stats['item_stats']['by_category'] as $cat): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($cat['category'] ?? 'Uncategorized'); ?></td>
                            <td><?php echo $cat['count']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    <!-- Auditor Dashboard -->
    <?php elseif ($user['role'] === 'auditor'): ?>
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Audit Logs</h3>
                <p class="stat-number"><?php echo $stats['audit_stats']['total']; ?></p>
                <a href="index.php?controller=audit&action=index">View Logs</a>
            </div>
            <div class="stat-card">
                <h3>Last 24 Hours</h3>
                <p class="stat-number"><?php echo $stats['audit_stats']['last_24_hours']; ?></p>
                <a href="index.php?controller=audit&action=index">View Logs</a>
            </div>
            <div class="stat-card">
                <h3>Total Items</h3>
                <p class="stat-number"><?php echo $stats['total_items']; ?></p>
                <a href="index.php?controller=item&action=index">View Items</a>
            </div>
            <div class="stat-card">
                <h3>Statistics</h3>
                <p class="stat-number">View</p>
                <a href="index.php?controller=audit&action=statistics">View Stats</a>
            </div>
        </div>

        <div class="dashboard-section">
            <h2>Top Actions</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th>Action</th>
                        <th>Count</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach (array_slice($stats['audit_stats']['by_action'], 0, 5) as $action): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($action['action']); ?></td>
                            <td><?php echo $action['count']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    <!-- Regular User Dashboard -->
    <?php else: ?>
        <div class="stats-grid">
            <div class="stat-card">
                <h3>My Items</h3>
                <p class="stat-number"><?php echo count($stats['my_items']); ?></p>
                <a href="index.php?controller=item&action=index">View All</a>
            </div>
            <div class="stat-card">
                <h3>Create New</h3>
                <p class="stat-number">+</p>
                <a href="index.php?controller=item&action=create">Create Item</a>
            </div>
            <div class="stat-card">
                <h3>Total Items</h3>
                <p class="stat-number"><?php echo $stats['total_items']; ?></p>
                <a href="index.php?controller=item&action=index">Browse</a>
            </div>
            <div class="stat-card">
                <h3>My Profile</h3>
                <p class="stat-number">Edit</p>
                <a href="index.php?controller=auth&action=profile">View Profile</a>
            </div>
        </div>

        <div class="dashboard-section">
            <h2>My Recent Items</h2>
            <?php if (empty($stats['my_items'])): ?>
                <p>You haven't created any items yet.</p>
                <a href="index.php?controller=item&action=create" class="btn btn-primary">Create Your First Item</a>
            <?php else: ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($stats['my_items'] as $item): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['title']); ?></td>
                                <td><?php echo htmlspecialchars($item['category'] ?? 'N/A'); ?></td>
                                <td>$<?php echo number_format($item['price'], 2); ?></td>
                                <td><span class="badge badge-<?php echo $item['status']; ?>"><?php echo $item['status']; ?></span></td>
                                <td>
                                    <a href="index.php?controller=item&action=show&id=<?php echo $item['id']; ?>" class="btn btn-sm">View</a>
                                    <a href="index.php?controller=item&action=edit&id=<?php echo $item['id']; ?>" class="btn btn-sm">Edit</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="quick-actions">
        <h2>Quick Actions</h2>
        <div class="action-buttons">
            <a href="index.php?controller=item&action=index" class="btn btn-primary">Browse Items</a>
            <a href="index.php?controller=item&action=create" class="btn btn-primary">Create Item</a>
            <a href="index.php?controller=auth&action=profile" class="btn btn-secondary">My Profile</a>
            <?php if ($user['role'] === 'administrator'): ?>
                <a href="index.php?controller=admin&action=users" class="btn btn-secondary">Manage Users</a>
            <?php endif; ?>
            <?php if ($user['role'] === 'auditor' || $user['role'] === 'administrator'): ?>
                <a href="index.php?controller=audit&action=index" class="btn btn-secondary">View Audit Logs</a>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include APP_ROOT . '/app/views/layouts/footer.php'; ?>
