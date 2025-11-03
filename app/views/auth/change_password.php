<?php $page_title = 'Change Password'; ?>
<?php include APP_ROOT . '/app/views/layouts/header.php'; ?>

<div class="container">
    <div class="page-header">
        <h1>Change Password</h1>
    </div>

    <?php if (isset($error)): ?>
        <div class="alert alert-error">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($success)): ?>
        <div class="alert alert-success">
            <?php echo htmlspecialchars($success); ?>
        </div>
    <?php endif; ?>

    <div class="form-container">
        <form action="index.php?controller=auth&action=doChangePassword" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

            <div class="form-group">
                <label for="old_password">Current Password</label>
                <input type="password" id="old_password" name="old_password" required autofocus>
            </div>

            <div class="form-group">
                <label for="new_password">New Password</label>
                <input type="password" id="new_password" name="new_password" required minlength="8">
                <small>Minimum 8 characters</small>
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirm New Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Change Password</button>
                <a href="index.php?controller=dashboard&action=index" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php include APP_ROOT . '/app/views/layouts/footer.php'; ?>
