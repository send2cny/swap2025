<?php $page_title = 'Register'; ?>
<?php include APP_ROOT . '/app/views/layouts/header.php'; ?>

<div class="auth-container">
    <div class="auth-box">
        <h1>Create Account</h1>

        <?php if (isset($error)): ?>
            <div class="alert alert-error">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form action="index.php?controller=auth&action=doRegister" method="POST" class="auth-form">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required autofocus>
                <small>Choose a unique username</small>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="full_name">Full Name</label>
                <input type="text" id="full_name" name="full_name" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required minlength="8">
                <small>Minimum 8 characters</small>
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary btn-block">Register</button>
            </div>

            <div class="form-links">
                <a href="index.php?controller=auth&action=login">Already have an account? Login</a>
            </div>
        </form>
    </div>
</div>

<?php include APP_ROOT . '/app/views/layouts/footer.php'; ?>
