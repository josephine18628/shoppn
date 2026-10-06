<?php

require_once __DIR__ . '/../../core/core.php';
require_once __DIR__ . '/../../controllers/ProductController.php';
require_admin();
require_once __DIR__ . '/../layout/header.php';

$controller = new ProductController();

$editBrand = null;
if (isset($_GET['edit_id']) && is_numeric($_GET['edit_id'])) {
    $editBrand = $controller->brandById((int)$_GET['edit_id']);
}

$brands = $controller -> getEveryBrand();
?>

<h1>Manage Brands</h1>

<?php if(isset($_SESSION['success'])): ?>
    <p class= "success-message"><?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']);  ?></p>
<?php endif; ?>
<?php if(isset($_SESSION['error'])): ?>
    <p class= "error-message"><?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']);  ?></p>
<?php endif; ?>   

<form action="<?= $editBrand ? '../../actions/update_brand_action.php' : '../../actions/add_brand_action.php' ?>" method="POST">
    <?php if($editBrand): ?>
        <input type="hidden" name="brand_id" value="<?= $editBrand['brand_id'] ?>">
    <?php endif; ?>
    <label for="brand_name">Brand Name</label>
    <input type="text" id="brand_name" name="brand_name" required maxlength="100" value="<?= $editBrand ? htmlspecialchars($editBrand['brand_name']) : '' ?>">
    <button type="submit"><?= $editBrand ? 'Update Brand' : 'Add Brand' ?></button>
</form>

<table>
    <thead>
        <tr><th>Brand Name</th><th>Action</th></tr>
    </thead>
    <tbody>
        <?php foreach ($brands as $brand): ?>
            <tr>
                <td><?= htmlspecialchars($brand['brand_name']) ?></td>
                
                <td><a href="brand.php?edit_id=<?= $brand['brand_id'] ?>">Edit</a></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>