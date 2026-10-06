<?php
require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/ProductController.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit('Invalid request method.');
}

$id = $_POST['cat_id'] ?? '';
$name = trim(strip_tags($_POST['cat_name'] ?? ''));

if (!is_numeric($id) || $id <= 0) {
    $_SESSION['error'] = 'Invalid category';
    header('Location: ../views/admin/category.php');
    exit();
}

if($name == '' || strlen($name) > 100) {
    $_SESSION['error'] = 'Category name is necessary and must be below a 100 characters';
    header('Location: ../views/admin/category.php');
    exit();
}

$controller = new ProductController();
$controller -> updateCategory((int)$id, $name);

$_SESSION['success'] = 'Category updated';
header('Location: ../views/admin/category.php');
exit();