<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Basic page setup -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Shoppn</title>
    <link rel="stylesheet" href="css/style.css" />
    
   
</head>
<body>
<header>
    <nav class="navbar">
        <a href="index.php" style="font-weight:700; font-size:1.3rem;">Shoppn</a>

        <?php if (is_logged_in()): ?>
            <span>Welcome, <?= htmlspecialchars($_SESSION['customer_name']) ?></span>
            <a href="logout.php">Logout</a>
        <?php else: ?>
            <a href="views/login.php">Log In</a>
            <a href="views/register.php">Register</a>
        <?php endif; ?>

        <?php if (is_admin()): ?>
            <a href="views/admin/brand.php">Brand</a>
            <a href="views/admin/category.php">Category</a>
        <?php endif; ?>
    </nav>
</header>
</body>