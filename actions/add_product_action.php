<?php
require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/ProductController.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit('Invalid request method.');
}

function back_error($message) {
    $_SESSION['error'] = $message;
    header('Location: ../views/admin/product.php');
    exit();
}

function post_text($key) {
    $value = $_POST[$key] ?? '';
    return is_string($value) ? trim(strip_tags($value)) : '';
}

$title    = post_text('product_title');
$desc     = post_text('product_desc');
$keywords = post_text('product_keywords');
$cat   = filter_input(INPUT_POST, 'product_cat', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$brand = filter_input(INPUT_POST, 'product_brand', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$price = filter_input(INPUT_POST, 'product_price', FILTER_VALIDATE_FLOAT);

if ($title === '' || strlen($title) > 200) {
    back_error('Product title is required and must be 200 characters or less.');
}
if ($price === false || $price === null || $price <= 0 || $price > 1000000) {
    back_error('Enter a valid price greater than 0.');
}
if (strlen($desc) > 500) {
    back_error('Description must be 500 characters or less.');
}
if (strlen($keywords) > 100) {
    back_error('Keywords must be 100 characters or less.');
}
if (!$cat || !$brand) {
    back_error('Choose a category and a brand.');
}

$controller = new ProductController();
if (!$controller->categoryById($cat) || !$controller->brandById($brand)) {
    back_error('The chosen category or brand does not exist.');
}

// Image upload (validated on the PHP side) 
$maxBytes = 2 * 1024 * 1024; // 2 MB
$allowed  = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif', 'image/webp' => 'webp'];

if (!isset($_FILES['product_image']) || $_FILES['product_image']['error'] === UPLOAD_ERR_NO_FILE) {
    back_error('Choose an image for the product.');
}
$file = $_FILES['product_image'];

if ($file['error'] === UPLOAD_ERR_INI_SIZE || $file['error'] === UPLOAD_ERR_FORM_SIZE || $file['size'] > $maxBytes) {
    back_error('The image is too large (max 2 MB).');
}
if ($file['error'] !== UPLOAD_ERR_OK) {
    back_error('Image upload failed. Try again.');
}

$info = @getimagesize($file['tmp_name']);
if ($info === false || !isset($allowed[$info['mime']])) {
    back_error('The image must be a JPG, PNG, GIF or WEBP file.');
}

$dir = __DIR__ . '/../images/products/';
if (!is_dir($dir) && !mkdir($dir, 0775, true)) {
    back_error('Could not create the images/products folder.');
}
if (!is_writable($dir)) {
    back_error('The images/products folder is not writable by the web server.');
}

$filename = bin2hex(random_bytes(8)) . '.' . $allowed[$info['mime']];

if (!move_uploaded_file($file['tmp_name'], $dir . $filename)) {
    back_error('Could not save the uploaded image.');
}

//  Save the product 
try {
    $saved = $controller->addProduct($cat, $brand, $title, round($price, 2), $desc, $filename, $keywords);
} catch (Throwable $e) {
    error_log($e->getMessage());
    $saved = false;
}

if (!$saved) {
    @unlink($dir . $filename);
    back_error('Could not save the product. Try again.');
}

$_SESSION['success'] = 'Product added.';
header('Location: ../views/admin/product.php');
exit();