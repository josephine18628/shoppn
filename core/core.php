<?php
// core/core.php — included once at the top of every page and action.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
date_default_timezone_set('Africa/Accra');
require_once __DIR__ . '/db_class.php';

// ROOT_PREFIX is the relative URL prefix that leads from the current page back to the project root:  ''  for index.php,  '../'  for views/*.php and actions/*.php,  '../../'  for views/admin/*.php and views/account/*.php.
$scriptDir = realpath(dirname($_SERVER['SCRIPT_FILENAME']));
$rootDir   = realpath(__DIR__ . '/..');
$depth     = 0;

if ($scriptDir && $rootDir && strpos($scriptDir, $rootDir) === 0) {
    $relative = trim(str_replace('\\', '/', substr($scriptDir, strlen($rootDir))), '/');
    $depth = ($relative === '') ? 0 : substr_count($relative, '/') + 1;
}

define('ROOT_PREFIX', str_repeat('../', $depth));   
unset($scriptDir, $rootDir, $depth, $relative);

function get_ip() {
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

function redirect($url) {
    header('Location: ' . $url);
    exit;
}

function is_logged_in() {
    return isset($_SESSION['customer_id']);
}

function is_admin() {
    return isset($_SESSION['user_role']) && (int)$_SESSION['user_role'] === 1;
}

function require_login() {
    if (!is_logged_in()) {
        $_SESSION['error'] = 'Please log in first.';
        redirect(ROOT_PREFIX . 'views/login.php');
    }
}

function require_admin() {
    if (!is_admin()) {
        $_SESSION['error'] = 'You must be an admin to view that page.';   
        redirect(ROOT_PREFIX . 'index.php');                             
    }
}