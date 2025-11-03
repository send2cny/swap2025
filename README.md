# MVC PHP CRUD Application

A complete MVC (Model-View-Controller) based PHP web application with user authentication, role-based access control, and comprehensive CRUD operations. Built for XAMPP environment with MySQL database.

## Features

### Authentication & Security
- **User Registration** - Self-registration with email validation
- **Secure Login** - Password hashing with bcrypt
- **Session Management** - Secure session handling with timeout
- **Password Reset** - Email-based password recovery system
- **Role-Based Access Control** - Three user roles with different permissions
- **CSRF Protection** - Cross-Site Request Forgery protection
- **Audit Logging** - Comprehensive activity tracking

### User Roles
1. **Administrator** - Full system access
   - User management (view, delete users)
   - Full CRUD operations on all items
   - Access to audit logs
   - System statistics

2. **Auditor** - Read-only monitoring access
   - View audit logs
   - View audit statistics
   - Export audit logs to CSV
   - View items (read-only)

3. **User** - Standard user access
   - CRUD operations on own items
   - Profile management
   - Password change

### CRUD Operations
- **Create** - Add new items with validation
- **Read** - View items with filtering and pagination
- **Update** - Edit existing items
- **Delete** - Remove items with confirmation
- **Search & Filter** - By category, status, and keywords
- **Pagination** - Efficient data browsing

### Additional Features
- Responsive design
- Dashboard with statistics
- Profile management
- Data export (CSV)
- Comprehensive error handling
- Clean and modern UI

## Requirements

- **XAMPP** (or similar LAMP/WAMP stack)
- **PHP 7.4 or higher**
- **MySQL 5.7 or higher**
- **Apache Web Server** with mod_rewrite enabled

## Installation

### 1. Install XAMPP

Download and install XAMPP from [https://www.apachefriends.org](https://www.apachefriends.org)

### 2. Clone/Copy Project

Copy the project folder to your XAMPP htdocs directory:
```
C:\xampp\htdocs\swap2025\  (Windows)
/opt/lampp/htdocs/swap2025/  (Linux)
```

### 3. Create Database

1. Start XAMPP Control Panel
2. Start Apache and MySQL services
3. Open phpMyAdmin: `http://localhost/phpmyadmin`
4. Create a new database or import the SQL file:

**Option A: Using phpMyAdmin**
- Click "New" to create database
- Name it: `mvc_crud_app`
- Go to "Import" tab
- Select `database/schema.sql`
- Click "Go"

**Option B: Using MySQL Command Line**
```bash
mysql -u root -p < database/schema.sql
```

### 4. Configure Database Connection

Edit `config/config.php` and update database credentials if needed:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'mvc_crud_app');
define('DB_USER', 'root');
define('DB_PASS', ''); // XAMPP default is empty
```

### 5. Update Application URL

Edit `config/config.php` and set your application URL:
```php
define('APP_URL', 'http://localhost/swap2025');
```

### 6. Enable mod_rewrite

Ensure Apache's mod_rewrite is enabled in XAMPP:

1. Open `C:\xampp\apache\conf\httpd.conf`
2. Find and uncomment (remove #):
   ```
   LoadModule rewrite_module modules/mod_rewrite.so
   ```
3. Find `AllowOverride None` and change to `AllowOverride All`
4. Restart Apache

### 7. Set Permissions (Linux/Mac)

```bash
chmod -R 755 /opt/lampp/htdocs/swap2025
chmod -R 777 /opt/lampp/htdocs/swap2025/public
```

## Access the Application

Open your browser and navigate to:
```
http://localhost/swap2025
```

or directly:
```
http://localhost/swap2025/public/index.php
```

## Default User Accounts

### Administrator
- **Username:** `admin`
- **Password:** `password123`
- **Access:** Full system access

### Auditor
- **Username:** `auditor`
- **Password:** `password123`
- **Access:** Audit logs and reports

### Regular User
- **Username:** `user`
- **Password:** `password123`
- **Access:** Standard CRUD operations

**Important:** Change these default passwords after first login!

## Project Structure

```
swap2025/
├── app/
│   ├── controllers/
│   │   ├── AdminController.php
│   │   ├── AuditController.php
│   │   ├── AuthController.php
│   │   ├── BaseController.php
│   │   ├── DashboardController.php
│   │   └── ItemController.php
│   ├── models/
│   │   ├── AuditLog.php
│   │   ├── Item.php
│   │   └── User.php
│   └── views/
│       ├── admin/
│       ├── audit/
│       ├── auth/
│       ├── dashboard/
│       ├── items/
│       └── layouts/
├── config/
│   ├── config.php
│   └── Database.php
├── database/
│   └── schema.sql
├── public/
│   ├── css/
│   │   └── style.css
│   ├── js/
│   │   └── script.js
│   ├── .htaccess
│   └── index.php
├── .htaccess
└── README.md
```

## Database Schema

### Main Tables
- **users** - User accounts and authentication
- **items** - CRUD data items
- **password_resets** - Password reset tokens
- **user_sessions** - Session management
- **audit_logs** - Activity tracking

### Views
- **user_activity_summary** - User statistics
- **recent_audit_logs** - Recent activity

### Stored Procedures
- **log_user_action** - Log activities
- **update_last_login** - Update login timestamp

## Configuration

### Email Settings

Edit `config/config.php` to configure email for password reset:
```php
define('EMAIL_FROM', 'noreply@example.com');
define('EMAIL_FROM_NAME', 'MVC CRUD App');
define('EMAIL_ENABLED', true);
```

For production, configure SMTP settings:
```php
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'your-email@gmail.com');
define('SMTP_PASSWORD', 'your-app-password');
```

### Security Settings

```php
define('SESSION_LIFETIME', 3600); // 1 hour
define('PASSWORD_MIN_LENGTH', 8);
define('TOKEN_EXPIRY', 3600); // Password reset expiry
```

### Debug Mode

For development, enable debug mode in `config/config.php`:
```php
define('DEBUG_MODE', true);
```

**Important:** Set to `false` in production!

## Usage Guide

### User Registration
1. Click "Register" on login page
2. Fill in username, email, full name, and password
3. Submit to create account
4. Login with new credentials

### Creating Items
1. Login to your account
2. Navigate to "Items" menu
3. Click "Create New Item"
4. Fill in item details
5. Submit to save

### Managing Items
- **View:** Click "View" button on item list
- **Edit:** Click "Edit" button (only for own items or admin)
- **Delete:** Click "Delete" button with confirmation
- **Search:** Use search box to filter items
- **Filter:** Use category and status dropdowns

### Audit Logs (Auditor/Admin)
1. Navigate to "Audit Logs" menu
2. Use filters to narrow down results
3. View detailed changes
4. Export to CSV using date range

### User Management (Admin)
1. Navigate to "Users" menu
2. View all registered users
3. Click "View" for detailed user information
4. Delete users if needed (cannot delete self)

## Troubleshooting

### Database Connection Error
- Verify MySQL is running in XAMPP
- Check database credentials in `config/config.php`
- Ensure database `mvc_crud_app` exists

### 404 Errors / URL Issues
- Enable mod_rewrite in Apache
- Check `.htaccess` files are present
- Verify `AllowOverride All` in httpd.conf

### Session Issues
- Check session directory permissions
- Verify session settings in php.ini
- Clear browser cookies

### Permission Denied
- Check file permissions (755 for directories, 644 for files)
- Ensure Apache has read access to project directory

### Email Not Sending
- Set `EMAIL_ENABLED` to `false` for testing
- Configure SMTP settings for production
- Check PHP mail() function is enabled

## Security Best Practices

1. **Change Default Passwords** - Immediately after installation
2. **Use HTTPS** - In production environment
3. **Update Config** - Set `DEBUG_MODE` to false in production
4. **Regular Backups** - Backup database regularly
5. **Update Dependencies** - Keep PHP and MySQL updated
6. **Monitor Logs** - Review audit logs regularly
7. **Strong Passwords** - Enforce strong password policy
8. **Secure Config** - Protect `config/config.php` from web access

## Development

### Adding New Features
1. Create model in `app/models/`
2. Create controller in `app/controllers/`
3. Create views in `app/views/`
4. Update `public/index.php` controller mapping

### Database Migrations
- Modify `database/schema.sql`
- Create backup before applying changes
- Test in development first

### Styling
- CSS: `public/css/style.css`
- JavaScript: `public/js/script.js`

## API Documentation

This application uses a traditional MVC pattern. Controllers handle requests via URL parameters:

```
index.php?controller=<name>&action=<method>&param=value
```

### Examples
- Login: `?controller=auth&action=login`
- View Item: `?controller=item&action=show&id=1`
- List Items: `?controller=item&action=index`

## Support

For issues or questions:
1. Check this README
2. Review error logs
3. Check XAMPP logs
4. Verify database connection

## License

This project is open source and available for educational purposes.

## Credits

Built with PHP, MySQL, HTML5, CSS3, and JavaScript.

---

**Version:** 1.0.0
**Last Updated:** 2025
**Compatible:** XAMPP 3.x, PHP 7.4+, MySQL 5.7+
