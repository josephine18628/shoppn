<?php
require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/CustomerController.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit('Invalid request method.');
}

// Clean the inputs (the password is not stripped, so special characters still work)
$email = trim(strip_tags($_POST['email'] ?? ''));
$password = $_POST['password'] ?? '';

if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
    $_SESSION['error'] = 'Enter a valid email and password';
    header('Location: ../views/login.php');
    exit();
}

$controller = new CustomerController();
$result = $controller -> login($email, $password);

if (isset($result['error'])) {
    $_SESSION['error'] = $result['error'];
    header('Location: ../views/login.php');
    exit();
}

// Login worked: remember who the customer is
session_regenerate_id(true);
$_SESSION['customer_id'] = $result['customer_id'];
$_SESSION['customer_name'] = $result['customer_name'];
$_SESSION['customer_email'] = $result['customer_email'];
$_SESSION['user_role'] = $result['user_role'];

header('Location: ../index.php');
exit();