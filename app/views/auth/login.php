<?php $page_title = 'Login'; ?>
<?php include APP_ROOT . '/app/views/layouts/header.php'; ?>

<div class="auth-container">
    <div class="auth-box">
        <h1>Login</h1>

        <?php if (isset($error)): ?>
            <div class="alert alert-error">
                <?php
                $error_messages = [
                    'auth_required' => 'Please login to continue',
                    'session_expired' => 'Your session has expired. Please login again.',
                    'permission_denied' => 'Permission denied'
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

        <form action="index.php?controller=auth&action=doLogin" method="POST" class="auth-form">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

            <div class="form-group">
                <label for="username">Username or Email</label>
                <input type="text" id="username" name="username" required autofocus>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>

            <div class="form-group">
                <label class="checkbox">
                    <input type="checkbox" name="remember" value="1">
                    Remember me
                </label>
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary btn-block">Login</button>
            </div>

            <div class="form-links">
                <a href="index.php?controller=auth&action=forgotPassword">Forgot Password?</a>
                <span>|</span>
                <a href="index.php?controller=auth&action=register">Create Account</a>
            </div>
        </form>

        <div class="info-box">
            <h3>Default Accounts</h3>
            <p><strong>Administrator:</strong> admin / password123</p>
            <p><strong>Auditor:</strong> auditor / password123</p>
            <p><strong>User:</strong> user / password123</p>
        </div>
    </div>
</div>

<?php include APP_ROOT . '/app/views/layouts/footer.php'; ?>
