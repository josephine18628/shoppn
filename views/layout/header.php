<?php
// Keeps what the visitor typed in the search box after they search.
$headerQuery = filter_input(INPUT_GET, 'user_query', FILTER_UNSAFE_RAW);
$headerQuery = is_string($headerQuery) ? $headerQuery : '';
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

        <?php if (is_logged_in()): ?>
            <span>Welcome, <?= htmlspecialchars($_SESSION['customer_name']) ?></span>
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

                <!-- Product search box, visible on every page -->
        <form action="<?= ROOT_PREFIX ?>views/search_results.php" method="GET">
            <input type="text" name="user_query" placeholder="Search products..." value="<?= htmlspecialchars($headerQuery) ?>">
            <button type="submit">Search</button>
        </form>
    </nav>
</header>
</body>