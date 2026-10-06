<?php
require_once __DIR__ . '/../../core/core.php';
require_once __DIR__ . '/../../controllers/ProductController.php';
require_admin();
require_once __DIR__ . '/../layout/header.php';

$controller = new ProductController();

$editCategory = null;
if (isset($_GET['edit_id']) && is_numeric($_GET['edit_id'])) {
    $editCategory = $controller->categoryById((int)$_GET['edit_id']);
}

$categories = $controller -> getEverycategory();
?>

<h1>Manage Categories</h1>

<?php if(isset($_SESSION['success'])): ?>
    <p class= "success-message"><?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']);  ?></p>
<?php endif; ?>
<?php if(isset($_SESSION['error'])): ?>
    <p class= "error-message"><?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']);  ?></p>
<?php endif; ?>   

<form action="<?= $editCategory ? '../../actions/update_category_action.php' : '../../actions/add_category_action.php' ?>" method="POST">
    <?php if($editCategory): ?>
        <input type="hidden" name="cat_id" value="<?= $editCategory['cat_id'] ?>">
    <?php endif; ?>
    <label for="cat_name">category Name</label>
    <input type="text" id="cat_name" name="cat_name" required maxlength="100" value="<?= $editCategory ? htmlspecialchars($editCategory['cat_name']) : '' ?>">
    <button type="submit"><?= $editCategory ? 'Update Category' : 'Add Category' ?></button>
</form>

<table>
    <thead>
        <tr><th>Category Name</th><th>Action</th></tr>
    </thead>
    <tbody>
       
        <?php foreach ($categories as $category): ?>
            <tr>
                
                <td><?= htmlspecialchars($category['cat_name']) ?></td>
                
                <td><a href="category.php?edit_id=<?= $category['cat_id'] ?>">Edit</a></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>