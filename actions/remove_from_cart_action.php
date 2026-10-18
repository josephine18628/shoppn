<?php
require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/CartController.php';
require_login(); // only logged-in customers can use the cart

if($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit('Invalid request method');
}

$remove = $_POST['remove'] ?? [];
if(!is_array($remove)) {
    $remove = [$remove];
}

if(empty($remove)) {
    $_SESSION['error'] = 'Select at least one item to remove';
    header('Location: ../views/cart.php');
    exit();
}

$ip = get_ip();
$controller = new CartController();

foreach($remove as $id) {
    if(!is_numeric($id) || $id <= 0) {
    $_SESSION['error'] = 'Invalid product';
    header('Location: ../views/cart.php');
    exit();
    }
    $controller -> removeFromCart((int)$id, $ip);
}
$_SESSION['success'] = 'Item removed from cart';
header('Location: ../views/cart.php');
exit();