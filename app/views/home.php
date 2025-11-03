<?php $page_title = 'Welcome'; ?>
<?php include APP_ROOT . '/app/views/layouts/header.php'; ?>

<div class="container">
    <div class="home-hero">
        <h1>Welcome to <?php echo APP_NAME; ?></h1>
        <p class="lead">A complete MVC PHP CRUD application with authentication and role-based access control.</p>
        <div class="cta-buttons">
            <a href="index.php?controller=auth&action=login" class="btn btn-primary btn-lg">Login</a>
            <a href="index.php?controller=auth&action=register" class="btn btn-secondary btn-lg">Register</a>
        </div>
    </div>

    <div class="features">
        <h2>Features</h2>
        <div class="feature-grid">
            <div class="feature-card">
                <h3>User Authentication</h3>
                <p>Secure login and registration with session management</p>
            </div>
            <div class="feature-card">
                <h3>Role-Based Access</h3>
                <p>Administrator, Auditor, and User roles with different permissions</p>
            </div>
            <div class="feature-card">
                <h3>CRUD Operations</h3>
                <p>Complete Create, Read, Update, Delete functionality for items</p>
            </div>
            <div class="feature-card">
                <h3>Audit Logging</h3>
                <p>Comprehensive audit trail for all system actions</p>
            </div>
            <div class="feature-card">
                <h3>Password Reset</h3>
                <p>Email-based password recovery system</p>
            </div>
            <div class="feature-card">
                <h3>User Management</h3>
                <p>Administrator tools for managing users</p>
            </div>
        </div>
    </div>

    <div class="info-section">
        <h2>Default Test Accounts</h2>
        <div class="account-info">
            <div class="account-card">
                <h3>Administrator</h3>
                <p><strong>Username:</strong> admin</p>
                <p><strong>Password:</strong> password123</p>
                <p>Full access to all features including user management</p>
            </div>
            <div class="account-card">
                <h3>Auditor</h3>
                <p><strong>Username:</strong> auditor</p>
                <p><strong>Password:</strong> password123</p>
                <p>Read-only access with full audit log viewing</p>
            </div>
            <div class="account-card">
                <h3>Regular User</h3>
                <p><strong>Username:</strong> user</p>
                <p><strong>Password:</strong> password123</p>
                <p>Standard user with CRUD access to own items</p>
            </div>
        </div>
    </div>
</div>

<?php include APP_ROOT . '/app/views/layouts/footer.php'; ?>
