<?php
require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/CartController.php';
require_login(); // only logged-in customers can use the cart

$back = '../index.php';

//Gets the URL page the user visited before arriving at the current PHP page
$referer = '';
if(!empty($_SERVER['HTTP_REFERER'])) {
    $referer = $_SERVER['HTTP_REFERER'];
}
$referHost = parse_url($referer, PHP_URL_HOST);
$referPort = parse_url($referer, PHP_URL_PORT);
if($referPort) {
    $referHost .= ':' . $referPort;
}
if($referer !== '' && $referHost === ($_SERVER['HTTP_HOST'] ?? '')) {
    $back = $referer;
}

// Gets the product ID from the URL, and if it is missing, not a number, or not above 0, shows an "Invalid product" error and sends the user back to the previous page.
$id = $_GET['add_cart'] ?? '';
if(!is_numeric($id) || $id <= 0) {
    $_SESSION['error'] = 'Invalid product';
    header('Location: ' . $back);
    exit();
}

// Gets the user's IP address, gets their customer ID if they are logged in (or null if not), and creates a CartController to use the cart functions.
$ip = get_ip();
$customer_id = is_logged_in() ? (int)$_SESSION['customer_id'] : null;
$controller = new CartController();

// Checks if the product is already in the user's cart, and if it is, shows a message telling them to update the quantity in the cart and sends them back to the previous page.
if($controller -> inCart((int)$id, $ip)) {
    $_SESSION['error'] = 'This item is already in your cart. Go to cart to update quantity';
    header('Location: ' . $back);
    exit();
}

// Adds the product to the cart with a quantity of 1, then shows a success message if it worked or an error message if it did not.
if($controller -> cartAddition((int)$id, 1, $ip, $customer_id)) {
    $_SESSION['success'] = 'Item added to cart';
}
else {
    $_SESSION['error'] = 'Product could not be added to cart';
}
header('Location: ' . $back);
exit();