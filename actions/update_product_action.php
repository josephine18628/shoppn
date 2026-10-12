<?php
// actions/update_product_action.php — saves changes made in the edit form on views/admin/product.php.
// A new image is optional: if none is uploaded, the product keeps its current image.
require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/ProductController.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit('Invalid request method.');
}

$id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
if (!$id) {
    $_SESSION['error'] = 'Invalid product.';
    redirect('../views/admin/product.php');
}

// Errors send the admin back to the same edit form.
function back_error($message, $id) {
    $_SESSION['error'] = $message;
    redirect('../views/admin/product.php?edit_id=' . (int)$id);
}

function post_text($key) {
    $value = $_POST[$key] ?? '';
    return is_string($value) ? trim(strip_tags($value)) : '';
}

$controller = new ProductController();
$existing   = $controller->productById($id);
if (!$existing) {
    $_SESSION['error'] = 'That product no longer exists.';
    redirect('../views/admin/product.php');
}

$title    = post_text('product_title');
$desc     = post_text('product_desc');
$keywords = post_text('product_keywords');
$cat   = filter_input(INPUT_POST, 'product_cat', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$brand = filter_input(INPUT_POST, 'product_brand', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$price = filter_input(INPUT_POST, 'product_price', FILTER_VALIDATE_FLOAT);

if ($title === '' || strlen($title) > 200) {
    back_error('Product title is required and must be 200 characters or less.', $id);
}
if ($price === false || $price === null || $price <= 0 || $price > 1000000) {
    back_error('Enter a valid price greater than 0.', $id);
}
if (strlen($desc) > 500) {
    back_error('Description must be 500 characters or less.', $id);
}
if (strlen($keywords) > 100) {
    back_error('Keywords must be 100 characters or less.', $id);
}
if (!$cat || !$brand) {
    back_error('Choose a category and a brand.', $id);
}
if (!$controller->categoryById($cat) || !$controller->brandById($brand)) {
    back_error('The chosen category or brand does not exist.', $id);
}

// ---------- Image: keep the current one unless a new one is uploaded ----------
// The current filename is taken from the database, not from the hidden field,
// so it cannot be tampered with in the browser.
$filename    = $existing['product_image'];
$newUploaded = false;
$dir         = __DIR__ . '/../images/products/';

$hasNewFile = isset($_FILES['product_image']) && $_FILES['product_image']['error'] !== UPLOAD_ERR_NO_FILE;

if ($hasNewFile) {
    $maxBytes = 2 * 1024 * 1024; // 2 MB
    $allowed  = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif', 'image/webp' => 'webp'];
    $file     = $_FILES['product_image'];

    if ($file['error'] === UPLOAD_ERR_INI_SIZE || $file['error'] === UPLOAD_ERR_FORM_SIZE || $file['size'] > $maxBytes) {
        back_error('The image is too large (max 2 MB).', $id);
    }
    if ($file['error'] !== UPLOAD_ERR_OK) {
        back_error('Image upload failed. Try again.', $id);
    }

    $info = @getimagesize($file['tmp_name']);
    if ($info === false || !isset($allowed[$info['mime']])) {
        back_error('The image must be a JPG, PNG, GIF or WEBP file.', $id);
    }

    if (!is_dir($dir) && !mkdir($dir, 0775, true)) {
        back_error('Could not create the images/products folder.', $id);
    }
    if (!is_writable($dir)) {
        back_error('The images/products folder is not writable by the web server.', $id);
    }

    $filename = bin2hex(random_bytes(8)) . '.' . $allowed[$info['mime']];
    if (!move_uploaded_file($file['tmp_name'], $dir . $filename)) {
        back_error('Could not save the uploaded image.', $id);
    }
    $newUploaded = true;
}

// ---------- Save the changes ----------
try {
    $saved = $controller->updateProduct($id, $cat, $brand, $title, round($price, 2), $desc, $filename, $keywords);
} catch (Throwable $e) {
    error_log($e->getMessage());
    $saved = false;
}

if (!$saved) {
    if ($newUploaded) {
        @unlink($dir . $filename);           // discard the new image, keep the old one
    }
    back_error('Could not update the product. Try again.', $id);
}

// The new image is saved, so the old file is no longer needed.
if ($newUploaded && !empty($existing['product_image'])) {
    @unlink($dir . basename($existing['product_image']));
}

$_SESSION['success'] = 'Product updated.';
redirect('../views/admin/product.php');