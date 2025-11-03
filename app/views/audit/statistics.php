<?php $page_title = 'Audit Statistics'; ?>
<?php include APP_ROOT . '/app/views/layouts/header.php'; ?>

<div class="container">
    <div class="page-header">
        <h1>Audit Statistics</h1>
        <a href="index.php?controller=audit&action=index" class="btn btn-secondary">Back to Logs</a>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <h3>Total Logs</h3>
            <p class="stat-number"><?php echo $stats['total']; ?></p>
        </div>
        <div class="stat-card">
            <h3>Last 24 Hours</h3>
            <p class="stat-number"><?php echo $stats['last_24_hours']; ?></p>
        </div>
    </div>

    <div class="stats-section">
        <h2>Actions Breakdown</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Action</th>
                    <th>Count</th>
                    <th>Percentage</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($stats['by_action'] as $action): ?>
                    <tr>
                        <td><span class="badge badge-action"><?php echo htmlspecialchars($action['action']); ?></span></td>
                        <td><?php echo $action['count']; ?></td>
                        <td><?php echo number_format(($action['count'] / $stats['total']) * 100, 2); ?>%</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="stats-section">
        <h2>Tables Breakdown</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Table</th>
                    <th>Count</th>
                    <th>Percentage</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($stats['by_table'] as $table): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($table['table_name']); ?></td>
                        <td><?php echo $table['count']; ?></td>
                        <td><?php echo number_format(($table['count'] / $stats['total']) * 100, 2); ?>%</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="stats-section">
        <h2>Top Active Users</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($stats['top_users'] as $user): ?>
                    <tr>
                        <td>
                            <?php echo htmlspecialchars($user['full_name']); ?><br>
                            <small>(<?php echo htmlspecialchars($user['username']); ?>)</small>
                        </td>
                        <td><?php echo $user['action_count']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include APP_ROOT . '/app/views/layouts/footer.php'; ?>
