<?php
/**
 * Main Entry Point - Front Controller
 * Routes all requests to appropriate controllers
 */

// Load configuration
require_once __DIR__ . '/../config/config.php';

// Get controller and action from URL parameters
$controller = $_GET['controller'] ?? 'dashboard';
$action = $_GET['action'] ?? 'index';

// Sanitize input
$controller = preg_replace('/[^a-zA-Z]/', '', $controller);
$action = preg_replace('/[^a-zA-Z]/', '', $action);

// Define controller mapping
$controllerMap = [
    'auth' => 'AuthController',
    'dashboard' => 'DashboardController',
    'item' => 'ItemController',
    'audit' => 'AuditController',
    'admin' => 'AdminController'
];

// Get controller class name
$controllerClass = $controllerMap[$controller] ?? 'DashboardController';

// Check if controller file exists
$controllerFile = APP_ROOT . '/app/controllers/' . $controllerClass . '.php';

if (!file_exists($controllerFile)) {
    die("Controller not found: {$controllerClass}");
}

// Load controller
require_once $controllerFile;

// Instantiate controller
if (!class_exists($controllerClass)) {
    die("Controller class not found: {$controllerClass}");
}

$controllerInstance = new $controllerClass();

// Check if action method exists
if (!method_exists($controllerInstance, $action)) {
    die("Action not found: {$action} in {$controllerClass}");
}

// Execute action
try {
    $controllerInstance->$action();
} catch (Exception $e) {
    if (DEBUG_MODE) {
        die("Error: " . $e->getMessage());
    } else {
        die("An error occurred. Please try again later.");
    }
}
