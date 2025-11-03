<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?><?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/public/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="nav-brand">
                <a href="<?php echo APP_URL; ?>/public/index.php"><?php echo APP_NAME; ?></a>
            </div>
            <div class="nav-menu">
                <?php if (isset($current_user) && is_array($current_user) && !empty($current_user)): ?>
                    <a href="index.php?controller=dashboard&action=index">Dashboard</a>
                    <a href="index.php?controller=item&action=index">Items</a>

                    <?php if (isset($current_user['role']) && $current_user['role'] === 'administrator'): ?>
                        <a href="index.php?controller=admin&action=users">Users</a>
                    <?php endif; ?>

                    <?php if (isset($current_user['role']) && ($current_user['role'] === 'auditor' || $current_user['role'] === 'administrator')): ?>
                        <a href="index.php?controller=audit&action=index">Audit Logs</a>
                    <?php endif; ?>

                    <div class="dropdown">
                        <button class="dropbtn">
                            <?php echo htmlspecialchars($current_user['username'] ?? 'User'); ?>
                            (<?php echo htmlspecialchars($current_user['role'] ?? 'user'); ?>)
                        </button>
                        <div class="dropdown-content">
                            <a href="index.php?controller=auth&action=profile">Profile</a>
                            <a href="index.php?controller=auth&action=changePassword">Change Password</a>
                            <a href="index.php?controller=auth&action=logout">Logout</a>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="index.php?controller=auth&action=login">Login</a>
                    <a href="index.php?controller=auth&action=register">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <main class="container">
