<?php $page_title = 'Forgot Password'; ?>
<?php include APP_ROOT . '/app/views/layouts/header.php'; ?>

<div class="auth-container">
    <div class="auth-box">
        <h1>Forgot Password</h1>

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

        <p>Enter your email address and we'll send you a link to reset your password.</p>

        <form action="index.php?controller=auth&action=doForgotPassword" method="POST" class="auth-form">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required autofocus>
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary btn-block">Send Reset Link</button>
            </div>

            <div class="form-links">
                <a href="index.php?controller=auth&action=login">Back to Login</a>
            </div>
        </form>
    </div>
</div>

<?php include APP_ROOT . '/app/views/layouts/footer.php'; ?>
