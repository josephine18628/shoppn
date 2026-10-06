<?php
require_once __DIR__. '/../../controllers/ProductController.php';
$sidebarController = new ProductController();

$categories = $sidebarController->getEveryCategory();
$brands = $sidebarController->getEveryBrand();
?>
<aside id="sidebar">
    <button id="sidebar-close" class="sidebar-close-btn" aria-label="Close menu">✕</button>
    <h3>Categories</h3>
    <u1>
        <?php if (empty($categories)): ?>
            <li>No categories yet</li>
        <?php else: ?>
            <?php foreach ($categories as $category): ?>
                <li><a href="index.php?cat=<?= $category['cat_id'] ?>"><?= htmlspecialchars($category['cat_name']) ?></a></li>
            <?php endforeach; ?>
        <?php endif; ?>
    </u1>

    <h3>Brands</h3>
    <u1>
        <?php if (empty($brands)): ?>
            <li>No brands yet</li>
        <?php else: ?>
            <?php foreach ($brands as $brand): ?>
                <li><a href="index.php?brand=<?= $brand['brand_id'] ?>"><?= htmlspecialchars($brand['brand_name']) ?></a></li>
            <?php endforeach; ?>
        <?php endif; ?>
    </u1>
</aside>