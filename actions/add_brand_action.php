<?php
require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/ProductController.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit('Invalid request method.');
}

$name = trim(strip_tags($_POST['brand_name'] ?? ''));

if ($name === '' || strlen($name) > 100) {
    $_SESSION['error'] = 'Brand name is necessary and must be 100 characters or less.';
    header('Location: ../views/admin/brand.php');
    exit();
}

$controller = new ProductController();
$controller->addBrand($name);

$_SESSION['success'] = 'Brand added';
header('Location: ../views/admin/brand.php');
exit();
