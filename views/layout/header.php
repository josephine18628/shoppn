<?php
require_once __DIR__ . '/../../controllers/CartController.php';
// Keeps what the visitor typed in the search box after they search.
$headerQuery = filter_input(INPUT_GET, 'user_query', FILTER_UNSAFE_RAW);
$headerQuery = is_string($headerQuery) ? $headerQuery : '';

// Cart summary is only loaded for logged-in customers
$headerCartCount = 0;
$headerCartTotal = 0;
if (is_logged_in()) {
    $headerController = new CartController();
    $headerCartCount = $headerController->cartCount(get_ip());
    $headerCartTotal = $headerController->cartTotal(get_ip());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Shoppn</title>
    <link rel="stylesheet" href="<?= ROOT_PREFIX ?>css/style.css" />
    
   
</head>
<body>
<header>
    <nav class="navbar">
        <a href="<?= ROOT_PREFIX ?>index.php" style="font-weight:700; font-size:1.3rem;">Shoppn</a>

        <!-- If the user is logged in, shows a welcome message with their name and a Logout link; if not, shows Log In and Register links. -->
        <!-- ROOT_PREFIX adds the right number of "../" to the start of each link so it always points to the correct file, whichever folder the current page is in. -->
        <?php if (is_logged_in()): ?>
            <span>Welcome, <?= htmlspecialchars($_SESSION['customer_name'] ?? '') ?></span>
            <a href="<?= ROOT_PREFIX ?>logout.php">Logout</a>
        <?php else: ?>
            <a href="<?= ROOT_PREFIX ?>views/login.php">Log In</a>
            <a href="<?= ROOT_PREFIX ?>views/register.php">Register</a>
        <?php endif; ?>

        <?php if (is_admin()): ?>
            <a href="<?= ROOT_PREFIX ?>views/admin/brand.php">Brand</a>
            <a href="<?= ROOT_PREFIX ?>views/admin/category.php">Category</a>
            <a href="<?= ROOT_PREFIX ?>views/admin/product.php">Product</a>
        <?php endif; ?>

        <?php if (is_logged_in()): ?>
            <a href="<?= ROOT_PREFIX ?>views/cart.php">Cart (<?= $headerCartCount ?>)</a>
        <?php endif; ?>

                <!-- Product search box, visible on every page -->
        <form action="<?= ROOT_PREFIX ?>views/search_results.php" method="GET">
            <input type="text" name="user_query" placeholder="Search products..." value="<?= htmlspecialchars($headerQuery) ?>">
            <button type="submit">Search</button>
        </form>
    </nav>

    <!-- If the user is logged in, shows a cart bar with the total number of items, the total price in cedis, and a link to the cart page. -->
    <?php if (is_logged_in()): ?>
        <div id="cart-summary">
            <!-- &mdash; is the HTML code for a long dash (—), and GH&#8373; shows "GH₵" because &#8373; is the HTML code for the Ghana cedi sign (₵). -->
            Shopping Cart &mdash; Total Items: <?= $headerCartCount ?>
            | Total Price: GH&#8373; <?= number_format($headerCartTotal, 2) ?>
            | <a href="<?= ROOT_PREFIX ?>views/cart.php">Go to Cart</a>
        </div>
    <?php endif; ?>
</header>
<main></main>