<?php $page_title = 'My Profile'; ?>
<?php include APP_ROOT . '/app/views/layouts/header.php'; ?>

<div class="container">
    <div class="page-header">
        <h1>My Profile</h1>
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

    <div class="profile-container">
        <div class="profile-info">
            <h2>Account Information</h2>
            <table class="info-table">
                <tr>
                    <th>Username:</th>
                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                </tr>
                <tr>
                    <th>Role:</th>
                    <td><span class="badge badge-<?php echo $user['role']; ?>"><?php echo htmlspecialchars($user['role']); ?></span></td>
                </tr>
                <tr>
                    <th>Status:</th>
                    <td><span class="badge badge-<?php echo $user['status']; ?>"><?php echo htmlspecialchars($user['status']); ?></span></td>
                </tr>
                <tr>
                    <th>Member Since:</th>
                    <td><?php echo date('F j, Y', strtotime($user['created_at'])); ?></td>
                </tr>
                <tr>
                    <th>Last Login:</th>
                    <td><?php echo $user['last_login'] ? date('F j, Y g:i A', strtotime($user['last_login'])) : 'Never'; ?></td>
                </tr>
            </table>
        </div>

        <div class="profile-edit">
            <h2>Update Profile</h2>
            <form action="index.php?controller=auth&action=updateProfile" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="full_name">Full Name</label>
                    <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Update Profile</button>
                    <a href="index.php?controller=auth&action=changePassword" class="btn btn-secondary">Change Password</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include APP_ROOT . '/app/views/layouts/footer.php'; ?>
