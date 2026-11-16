<?php

// Include core configuration and helper functions file from the parent directory
require_once __DIR__ . '/../core/core.php';

// Include the CartController class file to manage cart items and calculations
require_once __DIR__ . '/../controllers/CartController.php';

// Include the CustomerController class file to fetch customer details
require_once __DIR__ . '/../controllers/CustomerController.php';

// Ensure the user is logged in; redirects to login page if authentication fails
require_login();

// Instantiate a new CartController object
$controller = new CartController();

// Fetch all items in the cart using the user's IP address
$items = $controller->getCartItems(get_ip());

// Calculate the total cost of the cart items using the user's IP address
$total = $controller->cartTotal(get_ip());

// Check if the cart is empty or if the total amount is zero or negative
if (empty($items) || $total <= 0) {
    // Store an error message in the session variable
    $_SESSION['error'] = 'Your cart is empty or the total is invalid.';
    // Redirect the user back to the cart page
    redirect('cart.php');
}

// Instantiate a new CustomerController object
$customerController = new CustomerController();

// Retrieve customer details from the database using the customer ID stored in the session
$customer = $customerController->getCustomerById((int)$_SESSION['customer_id']);

// Determine the customer's email (from database record if available, fallback to session, or empty string)
$email = $customer ? $customer['customer_email'] : ($_SESSION['customer_email'] ?? '');

// Validate if the resulting email address has a correct format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    // Store an error message in the session variable if email is invalid
    $_SESSION['error'] = 'Please provide a valid email address.';
    // Redirect the user to the checkout page to fix their details
    redirect('checkout.php');
}

// Convert the total currency amount into pesewas (smallest currency unit for GHS) by multiplying by 100 and rounding
$amountPesewas = (int)round($total * 100);

// Define the Paystack test public key for payment processing
$paystackPublicKey = 'pk_test_058b0144c874f4dada0dadf4390f3bd698264c90';

// Include the page header HTML template layout
require_once __DIR__ . '/layout/header.php';

?>

<!-- Main page heading for the payment page -->
<h1>Payment</h1>

<!-- Navigation link allowing the user to return to the checkout page -->
<p><a href="checkout.php">&larr; Back to checkout</a></p>

<!-- Main content container box for checkout and payment summary -->
<section class="checkout-box">

    <!-- Subheading for the order summary list -->
    <h2>You are paying for</h2>

    <!-- HTML table displaying order items -->
    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th>Quantity</th>
                <th>Unit Price</th>
                <th>Subtotal</th>
            </tr>
        </thead>

        <tbody>
            <!-- Loop through each item in the cart array -->
            <?php foreach ($items as $item): ?>
                <tr>
                    <!-- Display sanitized product title -->
                    <td><?= htmlspecialchars($item['product_title']) ?></td>
                    <!-- Display product quantity -->
                    <td><?= (int)$item['qty'] ?></td>
                    <!-- Display formatted unit price with Ghana Cedis symbol -->
                    <td>GH&#8373; <?= number_format((float)$item['product_price'], 2) ?></td>
                    <!-- Display calculated subtotal for the line item -->
                    <td>GH&#8373; <?= number_format($item['qty'] * $item['product_price'], 2) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>

        <tfoot>
            <tr>
                <!-- Label spanning 3 columns for the total row -->
                <td colspan="3" class="cart-total-label">Total</td>
                <!-- Display final formatted grand total -->
                <td class="cart-total">GH&#8373; <?= number_format($total, 2) ?></td>
            </tr>
        </tfoot>
    </table>

    <!-- Hidden paragraph element used to display dynamic payment error or status messages -->
    <p id="pay-message" class="error-message" style="display:none;"></p>

    <!-- Container wrapper for payment action buttons -->
    <div class="cart-actions">
        <!-- Button that triggers the Paystack payment popup modal when clicked -->
        <button type="button" id="pay-button" onclick="pay()">
            Pay GH&#8373; <?= number_format($total, 2) ?>
        </button>
    </div>

    <!-- Notice informing the user that Paystack test environment is active -->
    <p class="meta">Paystack Test Mode is enabled.</p>

</section>

<!-- Load the external Paystack JavaScript library script for inline payments -->
<script src="https://js.paystack.co/v1/inline.js"></script>

<script>

// Track whether the payment has been successfully completed
var paid = false;

// Track whether the payment popup is currently in the process of opening
var paymentOpening = false;

// Pass the PHP Paystack public key safely into JavaScript
var paystackPublicKey = <?= json_encode($paystackPublicKey) ?>;

// Pass the PHP customer email safely into JavaScript
var customerEmail = <?= json_encode($email) ?>;

// Pass the PHP payment amount in pesewas safely into JavaScript
var paymentAmount = <?= json_encode($amountPesewas) ?>;

// Cache a reference to the DOM payment button element
var button = document.getElementById('pay-button');

// Function to display status or error messages in the message box
function showPayMessage(message) {
    var box = document.getElementById('pay-message');
    box.textContent = message;
    box.style.display = 'block';
}

// Callback function executed when Paystack successfully finishes a payment
function paymentDone(response) {

    // Prevent duplicate execution if payment was already marked as paid
    if (paid) return;

    // Validate that Paystack returned a response object containing a valid transaction reference
    if (!response || !response.reference) {
        paymentOpening = false;
        button.disabled = false;
        showPayMessage('Paystack did not return a valid payment reference.');
        return;
    }

    // Set payment status flag to true
    paid = true;

    // Redirect user to the backend verification action script with the transaction reference string
    window.location.href =
        '../actions/process_payment_action.php?ref=' +
        encodeURIComponent(response.reference);
}

// Callback function executed when the user closes or cancels the Paystack payment modal
function paymentCancelled() {

    // Check if the payment has not yet been finalized/paid
    if (!paid) {
        paymentOpening = false;
        button.disabled = false;
        // Reset the button text back to its original payment prompt
        button.textContent = 'Pay GH₵ <?= number_format($total, 2) ?>';
        showPayMessage('Payment was cancelled. You can try again.');
    }
}

// Main function triggered when the user clicks the payment button
function pay() {

    // Exit early if a payment is already completed or currently opening
    if (paid || paymentOpening) return;

    // Check if the external Paystack popup library loaded correctly in the browser
    if (typeof PaystackPop === 'undefined') {
        showPayMessage('Paystack could not load. Check your internet connection.');
        return;
    }

    // Verify that a valid test public key has been provided
    if (!paystackPublicKey || !paystackPublicKey.startsWith('pk_test_')) {
        showPayMessage('Invalid Paystack test public key.');
        return;
    }

    // Ensure a customer email address exists before proceeding
    if (!customerEmail) {
        showPayMessage('A valid customer email is required.');
        return;
    }

    // Validate that the payment amount is greater than zero
    if (!paymentAmount || paymentAmount <= 0) {
        showPayMessage('Invalid payment amount.');
        return;
    }

    // Update state flags and button UI to indicate the payment popup is loading
    paymentOpening = true;
    button.disabled = true;
    button.textContent = 'Opening Paystack...';

    try {

        // Initialize and configure the Paystack popup handler instance
        var handler = PaystackPop.setup({
            key: paystackPublicKey,
            email: customerEmail,
            amount: paymentAmount,
            currency: 'GHS',
            callback: paymentDone,
            onClose: paymentCancelled
        });

        // Open the Paystack payment checkout iframe modal
        handler.openIframe();

    } catch (error) {

        // Reset state flags and re-enable button if an exception occurs while opening
        paymentOpening = false;
        button.disabled = false;
        button.textContent = 'Pay GH₵ <?= number_format($total, 2) ?>';

        // Display configuration failure message to the user
        showPayMessage('Could not start payment. Please check your Paystack configuration.');

        // Log the exception error details to the browser developer console
        console.error(error);
    }
}

</script>

<?php 

// Include the page footer HTML template layout
require_once __DIR__ . '/layout/footer.php'; 

?>