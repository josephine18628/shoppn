<?php
require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/ProductController.php';

$query = filter_input(INPUT_GET, 'user_query', FILTER_UNSAFE_RAW);
if (is_string($query)) {
    $query = substr(trim($query), 0, 100);
} else {
    $query = '';
}

$products = [];
if($query !== '') {
    $controller = new ProductController();
    $products = $controller -> searchProducts($query);
}

require_once __DIR__ . '/layout/header.php';
?>

<h1>Search results</h1>

<?php if($query === ''): ?>
    <p>Type in the search box above to find products.</p>
<?php else: ?>
    <?php
    $count = count($products);
    ?>

    <p class="search-meta">
        <?php
        echo $count . ' result';

        if ($count !== 1) {
            echo 's';
        }
    ////&ldquo; and &rdquo; display curly quotation marks.
        echo ' for &ldquo;' . htmlspecialchars($query) . '&rdquo;'; 
        ?>
    </p>

    <div class="product-grid">
        <?php if(empty($products)): ?>
            <p>No products found</p>
        <?php else: ?>
            <?php foreach($products as $product): ?>
                <div class="product-card">
                    <?php if(!empty($product['product_image'])): ?>
                        <img src="../images/products/<?= htmlspecialchars($product['product_image']) ?>" alt="<?= htmlspecialchars($product['product_title']) ?>">
                    <?php else: ?>
                        <div class="no-image">No image</div>
                    <?php endif; ?>
                    <h3><?= htmlspecialchars($product['product_title']) ?></h3>
                    <p class="price">GH&#8373; <?= number_format($product['product_price'], 2) ?></p>
                    <a class="btn btn-small" href="single_product.php?pro_id=<?= (int)$product['product_id'] ?>">Details</a>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
<?php endif; ?>
<?php require_once __DIR__ . '/layout/footer.php'; ?>


