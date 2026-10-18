<?php
require_once __DIR__ . '/../core/core.php';
require_login(); // only logged-in customers can see the cart
require_once __DIR__ . '/../controllers/CartController.php';
require_once __DIR__ . '/layout/header.php';

$controller = new CartController();
$items = $controller -> getCartItems(get_ip());
$total = $controller -> cartTotal(get_ip());

?>

<h1>Your Cart</h1>
<?php if(isset($_SESSION['success'])): ?>
    <p class="success-message">
        <?php
        echo htmlspecialchars($_SESSION['success']);
        unset($_SESSION['success']);
        ?>
    </p>
<?php endif; ?>
<?php if(isset($_SESSION['error'])): ?>
    <p class="error-message">
        <?php
        echo htmlspecialchars($_SESSION['error']);
        unset($_SESSION['error']);
        ?>
    </p>
<?php endif; ?>

<?php if(empty($items)): ?>
    <div class="cart-empty">
        <p>Cart is empty</p>
        <a class="btn" href="../index.php">Continue Shopping</a>
    </div>
<?php else: ?>

    <form id="remove-form" action="../actions/remove_from_cart_action.php" method="POST"></form>
    <form id="update-form" action="../actions/update_qty_action.php" method="POST"></form>

    <table class="cart-table">
        <thead>
            <tr>
                <th>Remove</th>
                <th>Product</th>
                <th>Unit Price</th>
                <th>Quantity</th>
                <th>Subtotal</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($items as $item): ?>
                <?php
                $pid = (int)$item['p_id'];
                $qty = (int)$item['qty'];
                $title = htmlspecialchars($item['product_title']);
                ?>
                <tr>
                    <td data-label="Remove">
                        <input type="checkbox" name="remove[]" value="<?= $pid ?>" form="remove-form" aria-label="Select <?= $title ?>">
                    </td>
                    <td data-label="Product">
                        <div class="cart-product">
                            <?php if (!empty($item['product_image'])): ?>
                                <img src="../images/products/<?= htmlspecialchars($item['product_image']) ?>" alt="<?= $title ?>">
                            <?php else: ?>
                                <div class="no-image">No image</div>
                            <?php endif; ?>
                            <a href="single_product.php?pro_id=<?= $pid ?>"><?= $title ?></a>
                        </div>
                    </td>
                    <td data-label="Unit Price">GH&#8373; <?= number_format((float)$item['product_price'], 2) ?></td>
                    <td data-label="Quantity">
                        <div class="qty-control">
                            <button type="button" class="qty-btn" data-step="-1" aria-label="Decrease quantity" <?= $qty <= 1 ? 'disabled' : '' ?>>&minus;</button>
                            <input type="hidden" name="update[]" value="<?= $pid ?>" form="update-form">
                            <input type="number" class="qty-input" name="qty[]" value="<?= $qty ?>" min="1" max="999" step="1" form="update-form" aria-label="Quantity of <?= $title ?>">
                            <button type="button" class="qty-btn" data-step="1" aria-label="Increase quantity" <?= $qty >= 999 ? 'disabled' : '' ?>>+</button>
                        </div>
                    </td>
                    <td data-label="Subtotal">GH&#8373; <?= number_format($qty * (float)$item['product_price'], 2) ?></td>
                    <td>
                        <button type="submit" class="btn-link" name="remove[]" value="<?= $pid ?>" form="remove-form">Remove</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" class="cart-total-label">Total</td>
                <td colspan="2" class="cart-total">GH&#8373; <?= number_format($total, 2) ?></td>
            </tr>
        </tfoot>
    </table>

    <div class="cart-actions">
        <button type="submit" form="remove-form" class="btn-secondary">Remove Selected</button>
        <button type="submit" form="update-form" class="btn-secondary">Update Quantity</button>
        <a class="btn btn-secondary" href="../index.php">Continue Shopping</a>
        <a class="btn" href="checkout.php">Checkout</a>
    </div>

    <script src="../js/cart.js"></script>
<?php endif; ?>

<?php require_once __DIR__ . '/layout/footer.php'; ?>