<?php
require_once __DIR__ . '/../../core/core.php';
require_once __DIR__ . '/../../controllers/ProductController.php';
require_admin();

$controller = new ProductController();
$categories = $controller->getEveryCategory();
$brands = $controller->getEveryBrand();
$products = $controller->getEveryProduct();

require_once __DIR__ . '/../layout/header.php';
?>

<h1>Manage Products</h1>

<?php if (isset($_SESSION['success'])): ?>
    <p class="success-message"><?= htmlspecialchars($_SESSION['success']) ?></p>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>
<?php if (isset($_SESSION['error'])): ?>
    <p class="error-message"><?= htmlspecialchars($_SESSION['error']) ?></p>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<?php if (empty($categories) || empty($brands)): ?>
    <p class="error-message">
        A product needs a category and a brand. Add at least one of each first:
        <a href="category.php">Categories</a> &middot;
        <a href="brand.php">Brands</a>
    </p>

<?php else: ?>
    <form id="product-form" class="admin-form" action="../../actions/add_product_action.php" method="POST" enctype="multipart/form-data" novalidate>
        <div class="form-group">
            <label for="product-title-input">Product Title</label>
            <input type="text" id="product-title-input" name="product_title" maxlength="200" required>
            <small class="field-error" id="product-title-error"></small>
        </div>

        <div class="form-group">
            <label for="product-price-input">Price (GH&#8373;)</label>
            <input type="number" id="product-price-input" name="product_price" step="0.01" min="0.01" required>
            <small class="field-error" id="product-price-error"></small>
        </div>

        <div class="form-group">
            <label for="product-cat-input">Category</label>
            <select id="product-cat-input" name="product_cat" required>
                <option value="">Select a category</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= (int)$category['cat_id'] ?>"><?= htmlspecialchars($category['cat_name']) ?></option>
                <?php endforeach; ?>
            </select>
            <small class="field-error" id="product-cat-error"></small>
        </div>

        <div class="form-group">
            <label for="product-brand-input">Brand</label>
            <select id="product-brand-input" name="product_brand" required>
                <option value="">Select a brand</option>
                <?php foreach ($brands as $brand): ?>
                    <option value="<?= (int)$brand['brand_id'] ?>"><?= htmlspecialchars($brand['brand_name']) ?></option>
                <?php endforeach; ?>
            </select>
            <small class="field-error" id="product-brand-error"></small>
        </div>

        <div class="form-group">
            <label for="product-desc-input">Description</label>
            <textarea id="product-desc-input" name="product_desc" maxlength="500" rows="4"></textarea>
        </div>

        <div class="form-group">
            <label for="product-keywords-input">Keywords (used by search)</label>
            <input type="text" id="product-keywords-input" name="product_keywords" maxlength="100" placeholder="e.g. winter, shirt, floral">
        </div>

        <div class="form-group">
            <label for="product-image-input">Product Image (JPG, PNG, GIF or WEBP, max 2 MB)</label>
            <input type="file" id="product-image-input" name="product_image" accept="image/jpeg,image/png,image/gif,image/webp" required>
            <small class="field-error" id="product-image-error"></small>
        </div>

        <button type="submit" id="product-button">Add Product</button>
    </form>
<?php endif; ?>

<h2>All Products</h2>

<table>
    <thead>
        <tr><th>Image</th><th>Title</th><th>Category</th><th>Brand</th><th>Price</th></tr>
    </thead>
    <tbody>
        <?php if (empty($products)): ?>
            <tr><td colspan="5">No products yet.</td></tr>
        <?php else: ?>
            <?php foreach ($products as $product): ?>
                <tr>
                    <td>
                        <?php if (!empty($product['product_image'])): ?>
                            <img class="thumb" src="<?= ROOT_PREFIX ?>images/products/<?= htmlspecialchars($product['product_image']) ?>" alt="">
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($product['product_title']) ?></td>
                    <td><?= htmlspecialchars($product['cat_name']) ?></td>
                    <td><?= htmlspecialchars($product['brand_name']) ?></td>
                    <td>GH&#8373; <?= number_format((float)$product['product_price'], 2) ?></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<script src="../../js/validate.js"></script>
<?php require_once __DIR__ . '/../layout/footer.php'; ?>