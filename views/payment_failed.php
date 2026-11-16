<?php
// views/payment_failed.php — shown when a payment could not be confirmed
require_once __DIR__ . '/../core/core.php';
require_login();

$message = $_SESSION['error'] ?? 'Your payment could not be completed.';
unset($_SESSION['error']);

require_once __DIR__ . '/layout/header.php';
?>

<section class="checkout-box">
    <h1>Payment Failed</h1>
    <p class="error-message"><?= htmlspecialchars($message) ?></p>
    <p>No order was created. Your cart has been kept so you can try again.</p>

    <div class="cart-actions">
        <a class="btn btn-secondary" href="cart.php">Back to Cart</a>
        <a class="btn" href="payment.php">Try Again</a>
    </div>
</section>

<?php require_once __DIR__ . '/layout/footer.php'; ?>