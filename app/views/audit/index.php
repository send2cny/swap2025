<?php $page_title = 'Audit Logs'; ?>
<?php include APP_ROOT . '/app/views/layouts/header.php'; ?>

<div class="container">
    <div class="page-header">
        <h1>Audit Logs</h1>
        <a href="index.php?controller=audit&action=statistics" class="btn btn-primary">View Statistics</a>
    </div>

    <!-- Filters -->
    <div class="filters">
        <form method="GET" action="index.php" class="filter-form">
            <input type="hidden" name="controller" value="audit">
            <input type="hidden" name="action" value="index">

            <div class="filter-group">
                <select name="action">
                    <option value="">All Actions</option>
                    <?php foreach ($actions as $act): ?>
                        <option value="<?php echo htmlspecialchars($act); ?>" <?php echo $action === $act ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($act); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <select name="table">
                    <option value="">All Tables</option>
                    <?php foreach ($tables as $tbl): ?>
                        <option value="<?php echo htmlspecialchars($tbl); ?>" <?php echo $table_name === $tbl ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($tbl); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <button type="submit" class="btn btn-secondary">Filter</button>
                <a href="index.php?controller=audit&action=index" class="btn btn-secondary">Clear</a>
            </div>
        </form>
    </div>

    <!-- Audit Logs Table -->
    <?php if (empty($logs)): ?>
        <div class="empty-state">
            <p>No audit logs found.</p>
        </div>
    <?php else: ?>
        <table class="table table-sm">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>User</th>
                    <th>Action</th>
                    <th>Table</th>
                    <th>Record ID</th>
                    <th>IP Address</th>
                    <th>Timestamp</th>
                    <th>Details</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($logs as $log): ?>
                    <tr>
                        <td><?php echo $log['id']; ?></td>
                        <td>
                            <?php if ($log['username']): ?>
                                <?php echo htmlspecialchars($log['full_name']); ?><br>
                                <small>(<?php echo htmlspecialchars($log['username']); ?>)</small>
                            <?php else: ?>
                                <em>System</em>
                            <?php endif; ?>
                        </td>
                        <td><span class="badge badge-action"><?php echo htmlspecialchars($log['action']); ?></span></td>
                        <td><?php echo htmlspecialchars($log['table_name'] ?? 'N/A'); ?></td>
                        <td><?php echo $log['record_id'] ?? 'N/A'; ?></td>
                        <td><?php echo htmlspecialchars($log['ip_address'] ?? 'N/A'); ?></td>
                        <td><?php echo date('Y-m-d H:i:s', strtotime($log['created_at'])); ?></td>
                        <td>
                            <?php if ($log['old_values'] || $log['new_values']): ?>
                                <button class="btn btn-sm" onclick="showDetails(<?php echo $log['id']; ?>)">View</button>
                                <div id="details-<?php echo $log['id']; ?>" style="display: none;">
                                    <?php if ($log['old_values']): ?>
                                        <strong>Old:</strong><br>
                                        <pre><?php echo htmlspecialchars($log['old_values']); ?></pre>
                                    <?php endif; ?>
                                    <?php if ($log['new_values']): ?>
                                        <strong>New:</strong><br>
                                        <pre><?php echo htmlspecialchars($log['new_values']); ?></pre>
                                    <?php endif; ?>
                                </div>
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
                    <a href="?controller=audit&action=index&page=<?php echo $pagination['current_page'] - 1; ?>&action=<?php echo urlencode($action); ?>&table=<?php echo urlencode($table_name); ?>" class="btn btn-sm">Previous</a>
                <?php endif; ?>

                <span class="page-info">
                    Page <?php echo $pagination['current_page']; ?> of <?php echo $pagination['total_pages']; ?>
                </span>

                <?php if ($pagination['has_next']): ?>
                    <a href="?controller=audit&action=index&page=<?php echo $pagination['current_page'] + 1; ?>&action=<?php echo urlencode($action); ?>&table=<?php echo urlencode($table_name); ?>" class="btn btn-sm">Next</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <div class="export-section">
        <h3>Export Logs</h3>
        <form method="GET" action="index.php" class="export-form">
            <input type="hidden" name="controller" value="audit">
            <input type="hidden" name="action" value="export">
            <input type="date" name="start_date" required>
            <input type="date" name="end_date" required>
            <button type="submit" class="btn btn-secondary">Export to CSV</button>
        </form>
    </div>
</div>

<script>
function showDetails(id) {
    var details = document.getElementById('details-' + id);
    if (details.style.display === 'none') {
        details.style.display = 'block';
    } else {
        details.style.display = 'none';
    }
}
</script>

<?php include APP_ROOT . '/app/views/layouts/footer.php'; ?>
