<?php
require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/ProductController.php';

//A missing unkown id sends the visitor back home
$proId = filter_input(INPUT_GET, 'pro_id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
if (!$proId) {
    redirect('../index.php');
}

$controller = new ProductController();
$product = $controller -> productById($proId);
if(!$product) {
    redirect('../index.php');
}

require_once __DIR__ . '/layout/header.php';
?>
<p><a href="../index.php"> &larr; Back to all products</a></p>
<section class="product-detail">
    <div class="product-detail-image">
        <?php if (!empty($product['product-image'])): ?>
            <img src="../images/products/<?= htmlspecialchars($product['product_image']) ?>" alt="<?= htmlspecialchars($product['product_title']) ?>">
        <?php else: ?>
            <div class="no-image">No image</div>
        <?php endif; ?>
    </div>

    <div class="product-detail-info">
        <h1><?= htmlspecialchars($product['product_title']) ?></h1>
        <p class="price">GH&#8373; <?= number_format($product['product_price'], 2) ?></p>
        <p claass="meta">
            <?php
            $categoryId = (int)$product['product_cat'];
            $categoryName = htmlspecialchars($product['cat_name']);

            $brandId = (int)$product['product_brand'];
            $brandName = htmlspecialchars($product['brand_name']);
            ?>

            Category: <a href="../index.php?cat=<?= $categoryId ?>"><?= $categoryName ?></a>
            &middot;
            Brand: <a href="../index.php?brand=<?= $brandId ?>"><?= $brandName ?></a>
        </p>
        <?php if(!empty($product['product_desc'])): ?>
            <p><?= nl2br(htmlspecialchars($product['product_desc'])) ?></p>
        <?php endif; ?>
        <?php if (!empty($product['product_keywords'])): ?>
            <p class="meta">Keywords: <?= htmlspecialchars($product['product_keywords']) ?></p>
        <?php endif; ?>
        
    </div>
</section>
<?php require_once __DIR__ . '/layout/footer.php'; ?>