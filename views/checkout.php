<?php
// views/payment.php — order summary and the Paystack "Pay" button
require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/CartController.php';
require_once __DIR__ . '/../controllers/CustomerController.php';
require_login();

$controller = new CartController();
$items = $controller -> getCartItems(get_ip());
$total = $controller -> cartTotal(get_ip());

if (empty($items)) {
    $_SESSION['error'] = 'Your cart is empty. Add something before paying.';
    redirect('cart.php');
}

// Email comes from the database so it is always up to date
$customerController = new CustomerController();
$customer = $customerController -> getCustomerById((int)$_SESSION['customer_id']);
$email = $customer ? $customer['customer_email'] : ($_SESSION['customer_email'] ?? '');

// Paystack works in pesewas: GH₵ 1.00 = 100 pesewas
$amountPesewas = (int)round($total * 100);

require_once __DIR__ . '/layout/header.php';
?>

<h1>Payment</h1>
<p><a href="checkout.php">&larr; Back to checkout</a></p>

<section class="checkout-box">
    <h2>You are paying for</h2>
    <table>
        <thead>
            <tr><th>Product</th><th>Quantity</th><th>Unit Price</th><th>Subtotal</th></tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['product_title']) ?></td>
                    <td><?= (int)$item['qty'] ?></td>
                    <td>GH&#8373; <?= number_format((float)$item['product_price'], 2) ?></td>
                    <td>GH&#8373; <?= number_format((int)$item['qty'] * (float)$item['product_price'], 2) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" class="cart-total-label">Total</td>
                <td class="cart-total">GH&#8373; <?= number_format($total, 2) ?></td>
            </tr>
        </tfoot>
    </table>

    <p id="pay-message" class="error-message" style="display:none;"></p>

    <div class="cart-actions">
        <button type="button" id="pay-button" onclick="pay()">Pay GH&#8373; <?= number_format($total, 2) ?> </button>
    </div>
    <p class="meta">Test card: 4084 0840 8408 4081, any future expiry date, CVV 408.</p>
</section>

<script src="https://js.paystack.co/v1/inline.js"></script>
<script>
    var paid = false;

    function showPayMessage(text) {
        var box = document.getElementById('pay-message');
        box.textContent = text;
        box.style.display = 'block';
    }

    // Paystack calls this after a successful payment.
    // The server checks the payment again before saving the order.
    function paymentDone(response) {
        if (paid) return;
        paid = true;
        window.location = '../actions/process_payment_action.php?ref=' + encodeURIComponent(response.reference);
    }

    function paymentCancelled() {
        if (!paid) {
            alert('Payment cancelled.');
            document.getElementById('pay-button').disabled = false;
        }
    }

    function pay() {
        if (typeof PaystackPop === 'undefined') {
            showPayMessage('Could not load Paystack. Check your internet connection and refresh the page.');
            return;
        }
        document.getElementById('pay-button').disabled = true;

        var handler = PaystackPop.setup({
            key: '<?= htmlspecialchars(PAYSTACK_PUBLIC_KEY, ENT_QUOTES) ?>',
            email: '<?= htmlspecialchars($email, ENT_QUOTES) ?>',
            amount: <?= $amountPesewas ?>,
            currency: 'GHS',
            onSuccess: paymentDone,
            callback: paymentDone,
            onCancel: paymentCancelled,
            onClose: paymentCancelled
        });
        handler.openIframe();
    }
</script>

<?php require_once __DIR__ . '/layout/footer.php'; ?>