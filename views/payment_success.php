<?php // Open PHP block

// views/payment_success.php — the invoice / receipt for one order
require_once __DIR__ . '/../core/core.php'; // Include core application configuration and helper functions

require_once __DIR__ . '/../controllers/CartController.php'; // Include the CartController class file

require_login(); // Ensure the user is authenticated, redirecting to login if not

// Validate 'order_id' from the GET request URL parameters as a positive integer (minimum range 1)
$orderId = filter_input(INPUT_GET, 'order_id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

// If orderId is valid, instantiate CartController and fetch the order data; otherwise set to null
$order = $orderId ? (new CartController())->getOrderById($orderId) : null;

// Verify that the order exists and that the logged-in user is either the owner or an admin
if (!$order || ((int)$order['customer_id'] !== (int)$_SESSION['customer_id'] && !is_admin())) {
    
    $_SESSION['error'] = 'Order not found.'; // Set an error message session variable
    
    redirect('account/my_account.php'); // Redirect unauthorized or missing orders back to account page
}

require_once __DIR__ . '/layout/header.php'; // Include the standard HTML page header template layout

?> <!-- Close PHP block -->

<!-- Main container section for the invoice display -->
<section class="invoice">

    <!-- Success message notice that is automatically hidden when printing -->
    <p class="success-message no-print">Payment successful. Thank you for your order!</p>

    <!-- Header wrapper containing metadata and billing details -->
    <div class="invoice-head">

        <!-- First column: Invoice title, ID, date, and status -->
        <div>
            <h1>Shoppn Receipt</h1> <!-- Main invoice title heading -->
            <p><strong>Invoice #<?= (int)$order['invoice_no'] ?></strong></p> <!-- Display casted invoice reference number -->
            <p>Order date: <?= htmlspecialchars(date('d M Y', strtotime($order['order_date']))) ?></p> <!-- Format and display order date securely -->
            <p>Status: <?= htmlspecialchars(ucfirst($order['order_status'])) ?></p> <!-- Display capitalized and sanitized order status -->
        </div>

        <!-- Second column: Customer billing name and email information -->
        <div>
            <p><strong>Billed to</strong></p> <!-- Section subheading label -->
            <p><?= htmlspecialchars($order['customer_name']) ?></p> <!-- Display sanitized customer name -->
            <p><?= htmlspecialchars($order['customer_email']) ?></p> <!-- Display sanitized customer email -->
        </div>

    </div> <!-- Close invoice-head div -->

    <!-- Table outlining ordered products, quantities, and prices -->
    <table>

        <!-- Table header group -->
        <thead>
            <tr>
                <th>Product</th> <!-- Header for item name -->
                <th>Quantity</th> <!-- Header for ordered quantity -->
                <th>Unit Price</th> <!-- Header for price per item -->
                <th>Subtotal</th> <!-- Header for line total -->
            </tr>
        </thead>

        <!-- Table body group containing order items -->
        <tbody>
            <!-- Loop through each item in the order's items array -->
            <?php foreach ($order['items'] as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['product_title']) ?></td> <!-- Display sanitized product title -->
                    <td><?= (int)$item['qty'] ?></td> <!-- Cast and display item quantity as integer -->
                    <td>GH&#8373; <?= number_format($item['product_price'], 2) ?></td> <!-- Format unit price with currency symbol -->
                    <td>GH&#8373; <?= number_format($item['qty'] * $item['product_price'], 2) ?></td> <!-- Calculate and format line subtotal -->
                </tr>
            <?php endforeach; // End the items loop ?>
        </tbody>

        <!-- Table footer group for grand totals -->
        <tfoot>
            <tr>
                <td colspan="3" class="cart-total-label">Total Paid</td> <!-- Label spanning across 3 columns -->
                <td class="cart-total">GH&#8373; <?= number_format($order['amt'], 2) ?></td> <!-- Display final formatted total amount paid -->
            </tr>
        </tfoot>

    </table> <!-- Close products table -->

    <!-- Action buttons container (hidden when printing) -->
    <div class="cart-actions no-print">
        <button type="button" onclick="window.print()">Print Receipt</button> <!-- Button to trigger browser print dialog -->
        <a class="btn btn-secondary" href="account/my_account.php">My Orders</a> <!-- Link back to customer's order history -->
        <a class="btn" href="../index.php">Continue Shopping</a> <!-- Link to return to homepage/store -->
    </div>

</section> <!-- Close invoice section -->

<?php // Open PHP block for footer inclusion

require_once __DIR__ . '/layout/footer.php'; // Include standard HTML page footer template layout

?> 