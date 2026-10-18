<?php
require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/ProductController.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit('Invalid request method.');
}

//gets cat_name from the submitted form, uses an empty string if it is missing, removes HTML tags with strip_tags(), removes extra whitespace from the beginning and end with trim(), and stores the cleaned value in $nam
$name = trim(strip_tags($_POST['cat_name'] ?? ''));

if ($name === '' || strlen($name) > 100) {
    $_SESSION['error'] = 'Category name is necessary and must be below a 100 characters.';
    header('Location: ../views/admin/category.php');
    exit();
}

$controller = new ProductController();
$controller -> addCategory($name);

$_SESSION['success'] = 'Category added';
header('Location: ../views/admin/category.php');
exit();