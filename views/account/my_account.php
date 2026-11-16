<?php
require_once __DIR__ . '/../../core/core.php';
require_once __DIR__ . '/../../controllers/CustomerController.php';
require_once __DIR__ . '/../../controllers/CartController.php';
require_login();

$controller = new CustomerController();
$customer = $controller -> getCustomerById((int)$_SESSION['customer_id']);
if (!$customer) {
    // The account no longer exists, so log out
    session_unset();
    session_destroy();
    redirect('../login.php');
}

$cartController = new CartController();
$orders = $cartController -> getOrdersByCustomer((int)$customer['customer_id']);
$recent = $cartController -> getRecentlyPurchased((int)$customer['customer_id'], 4);

require_once __DIR__ . '/../layout/header.php';
?>

<h1>My Account</h1>

<?php if (isset($_SESSION['success'])): ?>
    <p class="success-message"><?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></p>
<?php endif; ?>
<?php if (isset($_SESSION['error'])): ?>
    <p class="error-message"><?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></p>
<?php endif; ?>

<section class="account-card">
    <div class="account-photo">
        <?php if (!empty($customer['customer_image'])): ?>
            <img src="../../images/customers/<?= htmlspecialchars($customer['customer_image']) ?>" alt="Profile picture">
        <?php else: ?>
            <div class="no-image"><?= htmlspecialchars(strtoupper(substr($customer['customer_name'], 0, 1))) ?></div>
        <?php endif; ?>
    </div>
    <div class="account-details">
        <h2><?= htmlspecialchars($customer['customer_name']) ?></h2>
        <p><strong>Email:</strong> <?= htmlspecialchars($customer['customer_email']) ?></p>
        <p><strong>Phone:</strong> <?= htmlspecialchars($customer['customer_contact']) ?></p>
        <p><strong>Location:</strong> <?= htmlspecialchars($customer['customer_city']) ?>, <?= htmlspecialchars($customer['customer_country']) ?></p>
        <p><strong>Account type:</strong> <?= (int)$customer['user_role'] === 1 ? 'Admin' : 'Customer' ?></p>
        <div class="account-links">
            <a class="btn btn-small" href="edit_account.php">Edit Details</a>
            <a class="btn btn-small btn-secondary" href="change_pass.php">Change Password</a>
            <a class="btn btn-small btn-danger" href="delete_account.php">Delete Account</a>
        </div>
    </div>
</section>

<h2>My Orders</h2>
<?php if (empty($orders)): ?>
    <p>You have not placed any orders yet. <a href="../../index.php">Start shopping</a></p>
<?php else: ?>
    <table>
        <thead>
            <tr><th>Invoice</th><th>Date</th><th>Items</th><th>Total</th><th>Status</th><th></th></tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $order): ?>
                <tr>
                    <td>#<?= (int)$order['invoice_no'] ?></td>
                    <td><?= htmlspecialchars(date('d M Y', strtotime($order['order_date']))) ?></td>
                    <td><?= (int)$order['item_count'] ?></td>
                    <td>GH&#8373; <?= number_format((float)$order['amt'], 2) ?></td>
                    <td><?= htmlspecialchars(ucfirst($order['order_status'])) ?></td>
                    <td><a href="../payment_success.php?order_id=<?= (int)$order['order_id'] ?>">View receipt</a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php if (!empty($recent)): ?>
    <h2>Recently Purchased</h2>
    <div class="product-grid">
        <?php foreach ($recent as $product): ?>
            <div class="product-card">
                <?php if (!empty($product['product_image'])): ?>
                    <img src="../../images/products/<?= htmlspecialchars($product['product_image']) ?>" alt="<?= htmlspecialchars($product['product_title']) ?>">
                <?php else: ?>
                    <div class="no-image">No image</div>
                <?php endif; ?>
                <h3><?= htmlspecialchars($product['product_title']) ?></h3>
                <p class="price">GH&#8373; <?= number_format((float)$product['product_price'], 2) ?></p>
                <a class="btn btn-small" href="../../actions/add_to_cart_action.php?add_cart=<?= (int)$product['product_id'] ?>">Buy Again</a>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>