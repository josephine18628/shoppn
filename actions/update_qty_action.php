<?php
require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/CartController.php';
require_login(); // only logged-in customers can use the cart

if($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit('Invalid request method');
}

$update = $_POST['update'] ?? [];
$qty = $_POST['qty'] ?? [];

if(!is_array($update) || !is_array($qty) || count($update) !== count($qty)) {
    $_SESSION['error'] = 'Invalid cart update';
    header('Location: ../views/cart.php');
    exit();
}
$update = array_values($update);
$qty = array_values($qty);

foreach($update as $i => $id) {
    if(!is_numeric($id) || $id <= 0) {
    $_SESSION['error'] = 'Invalid product';
    header('Location: ../views/cart.php');
    exit();
    }
    $quantity = $qty[$i];
    $error = 'Quantity must be a whole number from 1 to 999';

    if (!ctype_digit((string)$quantity) || (int)$quantity < 1 || (int)$quantity > 999) {
        $_SESSION['error'] = $error;
        header('Location: ../views/cart.php');
        exit();
    } 
}
$ip = get_ip();
$controller = new CartController();

foreach($update as $i => $id) {
    $controller -> updateQty((int)$id, (int)$qty[$i], $ip);
}
$_SESSION['success'] = 'Cart updated';
header('Location: ../views/cart.php');
exit();