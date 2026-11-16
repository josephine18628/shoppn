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

// Recommendation: up to 4 other products from the same category
$similar = [];
foreach ($controller -> productsByCategory((int)$product['product_cat']) as $other) {
    if ((int)$other['product_id'] !== (int)$product['product_id']) {
        $similar[] = $other;
    }
}
$similar = array_slice($similar, 0, 4);

require_once __DIR__ . '/layout/header.php';
?>

<?php
if (isset($_SESSION['success'])) {
    $message = $_SESSION['success'];

    unset($_SESSION['success']);

    echo '<p class="success-message">' . htmlspecialchars($message) . '</p>';
}
?>


<?php
if (isset($_SESSION['error'])) {
    $message = $_SESSION['error'];

    unset($_SESSION['error']);

    echo '<p class="error-message">' . htmlspecialchars($message) . '</p>';
}
?>

<p><a href="../index.php"> &larr; Back to all products</a></p>
<section class="product-detail">
    <div class="product-detail-image">
        <?php if (!empty($product['product_image'])): ?>
            <img src="../images/products/<?= htmlspecialchars($product['product_image']) ?>" alt="<?= htmlspecialchars($product['product_title']) ?>">
        <?php else: ?>
            <div class="no-image">No image</div>
        <?php endif; ?>
    </div>

    <div class="product-detail-info">
        <h1><?= htmlspecialchars($product['product_title']) ?></h1>
        <p class="price">GH&#8373; <?= number_format($product['product_price'], 2) ?></p>
        <p class="meta">
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
        <a class="btn" href="../actions/add_to_cart_action.php?add_cart=<?= (int)$product['product_id'] ?>">Add to Cart</a>
        
    </div>
</section>

<?php if (!empty($similar)): ?>
    <h2>You May Also Like</h2>
    <div class="product-grid">
        <?php foreach ($similar as $other): ?>
            <div class="product-card">
                <?php if (!empty($other['product_image'])): ?>
                    <img src="../images/products/<?= htmlspecialchars($other['product_image']) ?>" alt="<?= htmlspecialchars($other['product_title']) ?>">
                <?php else: ?>
                    <div class="no-image">No image</div>
                <?php endif; ?>
                <h3><?= htmlspecialchars($other['product_title']) ?></h3>
                <p class="price">GH&#8373; <?= number_format((float)$other['product_price'], 2) ?></p>
                <a class="btn btn-small" href="single_product.php?pro_id=<?= (int)$other['product_id'] ?>">Details</a>
                <a class="btn btn-small" href="../actions/add_to_cart_action.php?add_cart=<?= (int)$other['product_id'] ?>">Add to Cart</a>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
<?php require_once __DIR__ . '/layout/footer.php'; ?>