<?php
require_once __DIR__ . '/../../core/core.php';
require_once __DIR__ . '/../../controllers/ProductController.php';
require_admin();

$controller = new ProductController();
$categories = $controller->getEveryCategory();
$brands = $controller->getEveryBrand();
$products = $controller->getEveryProduct();

// Edit mode: ?edit_id=N loads that product into the form
$editProduct = null;
$editId = filter_input(INPUT_GET, 'edit_id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
if ($editId) {
    $editProduct = $controller->productById($editId);
}

// Value for a form field: the product's value when editing, empty when adding
function field($product, $key) {
    return $product ? htmlspecialchars((string)$product[$key]) : '';
}

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
    <h2><?= $editProduct ? 'Edit Product' : 'Add a Product' ?></h2>
    <?php if ($editProduct): ?>
        <p><a href="product.php">&larr; Cancel editing</a></p>
    <?php endif; ?>
    <form id="product-form" class="admin-form" action="../../actions/<?= $editProduct ? 'update_product_action.php' : 'add_product_action.php' ?>" method="POST" enctype="multipart/form-data" data-editing="<?= $editProduct ? '1' : '0' ?>" novalidate>
        <?php if ($editProduct): ?>
            <input type="hidden" name="product_id" value="<?= (int)$editProduct['product_id'] ?>">
            <input type="hidden" name="current_image" value="<?= field($editProduct, 'product_image') ?>">
        <?php endif; ?>
        <div class="form-group">
            <label for="product-title-input">Product Title</label>
            <input type="text" id="product-title-input" name="product_title" maxlength="200" required value="<?= field($editProduct, 'product_title') ?>">
            <small class="field-error" id="product-title-error"></small>
        </div>

        <div class="form-group">
            <label for="product-price-input">Price (GH&#8373;)</label>
            <input type="number" id="product-price-input" name="product_price" step="0.01" min="0.01" required value="<?= field($editProduct, 'product_price') ?>">
            <small class="field-error" id="product-price-error"></small>
        </div>

        <div class="form-group">
            <label for="product-cat-input">Category</label>
            <select id="product-cat-input" name="product_cat" required>
                <option value="">Select a category</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= (int)$category['cat_id'] ?>" <?= ($editProduct && (int)$editProduct['product_cat'] === (int)$category['cat_id']) ? 'selected' : '' ?>><?= htmlspecialchars($category['cat_name']) ?></option>
                <?php endforeach; ?>
            </select>
            <small class="field-error" id="product-cat-error"></small>
        </div>

        <div class="form-group">
            <label for="product-brand-input">Brand</label>
            <select id="product-brand-input" name="product_brand" required>
                <option value="">Select a brand</option>
                <?php foreach ($brands as $brand): ?>
                    <option value="<?= (int)$brand['brand_id'] ?>" <?= ($editProduct && (int)$editProduct['product_brand'] === (int)$brand['brand_id']) ? 'selected' : '' ?>><?= htmlspecialchars($brand['brand_name']) ?></option>
                <?php endforeach; ?>
            </select>
            <small class="field-error" id="product-brand-error"></small>
        </div>

        <div class="form-group">
            <label for="product-desc-input">Description</label>
            <textarea id="product-desc-input" name="product_desc" maxlength="500" rows="4"><?= field($editProduct, 'product_desc') ?></textarea>
        </div>

        <div class="form-group">
            <label for="product-keywords-input">Keywords (used by search)</label>
            <input type="text" id="product-keywords-input" name="product_keywords" maxlength="100" placeholder="e.g. winter, shirt, floral" value="<?= field($editProduct, 'product_keywords') ?>">
        </div>

        <div class="form-group">
            <label for="product-image-input">Product Image (JPG, PNG, GIF or WEBP, max 2 MB)<?= $editProduct ? ' — leave empty to keep the current image' : '' ?></label>
            <?php if ($editProduct && !empty($editProduct['product_image'])): ?>
                <img class="thumb" src="<?= ROOT_PREFIX ?>images/products/<?= field($editProduct, 'product_image') ?>" alt="Current image">
            <?php endif; ?>
            <input type="file" id="product-image-input" name="product_image" accept="image/jpeg,image/png,image/gif,image/webp" <?= $editProduct ? '' : 'required' ?>>
            <small class="field-error" id="product-image-error"></small>
        </div>

        <button type="submit" id="product-button"><?= $editProduct ? 'Update Product' : 'Add Product' ?></button>
    </form>
<?php endif; ?>

<h2>All Products</h2>

<table>
    <thead>
        <tr><th>Image</th><th>Title</th><th>Category</th><th>Brand</th><th>Price</th><th>Action</th></tr>
    </thead>
    <tbody>
        <?php if (empty($products)): ?>
            <tr><td colspan="6">No products yet.</td></tr>
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
                    <td><a href="product.php?edit_id=<?= (int)$product['product_id'] ?>">Edit</a></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<script src="../../js/validate.js"></script>
<?php require_once __DIR__ . '/../layout/footer.php'; ?>