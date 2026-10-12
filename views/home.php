<?php
// views/home.php — loaded by index.php. Shows featured products, or products
// filtered by ?cat=N or ?brand=N.
require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/ProductController.php';

$controller = new ProductController();

$catId   = filter_input(INPUT_GET, 'cat', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$brandId = filter_input(INPUT_GET, 'brand', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

$heading = 'Featured Products';
if ($catId) {
    $products = $controller->productsByCategory($catId);
    $category = $controller->categoryById($catId);
    $heading  = $category ? 'Category: ' . $category['cat_name'] : 'Products';
} elseif ($brandId) {
    $products = $controller->productsByBrand($brandId);
    $brand    = $controller->brandById($brandId);
    $heading  = $brand ? 'Brand: ' . $brand['brand_name'] : 'Products';
} else {
    $products = $controller->getFeaturedProducts(6);
}

require_once __DIR__ . '/layout/header.php';
?>

<button id="sidebar-toggle" class="sidebar-toggle" aria-label="Show categories and brands">☰</button>
<div id="sidebar-overlay" class="sidebar-overlay"></div>

<section class="hero-centered">
    <h1>Welcome to Shoppn</h1>

    <div class="hero-icon">
        <svg viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="10" r="4"/>
            <path d="M8 16 L18 16 L22 40 L48 40 L54 20 L24 20"/>
            <line x1="22" y1="40" x2="16" y2="50"/>
            <line x1="16" y1="50" x2="6" y2="50"/>
            <circle cx="28" cy="56" r="3"/>
            <circle cx="46" cy="56" r="3"/>
        </svg>
    </div>

    <div class="marquee">
        <div class="marquee-track">
            <span class="marquee-item">👗 Dresses</span>
            <span class="marquee-item">👜 Bags</span>
            <span class="marquee-item">👟 Sneakers</span>
            <span class="marquee-item">🕶️ Sunglasses</span>
            <span class="marquee-item">👒 Hats</span>
            <span class="marquee-item">💍 Jewelry</span>
            <span class="marquee-item">🧥 Jackets</span>
            <span class="marquee-item">👚 Tops</span>
            <!-- duplicate set — required for a seamless infinite loop -->
            <span class="marquee-item">👗 Dresses</span>
            <span class="marquee-item">👜 Bags</span>
            <span class="marquee-item">👟 Sneakers</span>
            <span class="marquee-item">🕶️ Sunglasses</span>
            <span class="marquee-item">👒 Hats</span>
            <span class="marquee-item">💍 Jewelry</span>
            <span class="marquee-item">🧥 Jackets</span>
            <span class="marquee-item">👚 Tops</span>
        </div>
    </div>
</section>

<hr class="hero-divider">

<div class="home-layout">
    <?php require_once __DIR__ . '/layout/sidebar.php'; ?>

    <section class="product-area">
        <h2><?= htmlspecialchars($heading) ?></h2>
        <?php if ($catId || $brandId): ?>
            <p><a href="<?= ROOT_PREFIX ?>index.php">&larr; Show featured products</a></p>
        <?php endif; ?>

        <div class="product-grid">
            <?php if (empty($products)): ?>
                <p>No products found</p>
            <?php else: ?>
                <?php foreach ($products as $product): ?>
                    <div class="product-card">
                        <?php if (!empty($product['product_image'])): ?>
                            <img src="<?= ROOT_PREFIX ?>images/products/<?= htmlspecialchars($product['product_image']) ?>" alt="<?= htmlspecialchars($product['product_title']) ?>">
                        <?php else: ?>
                            <div class="no-image">No image</div>
                        <?php endif; ?>
                        <h3><?= htmlspecialchars($product['product_title']) ?></h3>
                        <p class="price">GH&#8373; <?= number_format((float)$product['product_price'], 2) ?></p>
                        <a class="btn btn-small" href="<?= ROOT_PREFIX ?>views/single_product.php?pro_id=<?= (int)$product['product_id'] ?>">Details</a>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var sidebar   = document.getElementById('sidebar');
    var toggleBtn = document.getElementById('sidebar-toggle');
    var closeBtn  = document.getElementById('sidebar-close');
    var overlay   = document.getElementById('sidebar-overlay');

    if (!sidebar || !toggleBtn) return;

    function openSidebar() {
        sidebar.classList.add('sidebar-open');
        toggleBtn.classList.add('sidebar-open');
        if (overlay) overlay.classList.add('sidebar-open');
    }

    function closeSidebar() {
        sidebar.classList.remove('sidebar-open');
        toggleBtn.classList.remove('sidebar-open');
        if (overlay) overlay.classList.remove('sidebar-open');
    }

    toggleBtn.addEventListener('click', function () {
        if (sidebar.classList.contains('sidebar-open')) {
            closeSidebar();
        } else {
            openSidebar();
        }
    });

    if (closeBtn) closeBtn.addEventListener('click', closeSidebar);
    if (overlay) overlay.addEventListener('click', closeSidebar);
});
</script>
<?php require_once __DIR__ . '/layout/footer.php'; ?>