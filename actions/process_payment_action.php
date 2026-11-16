<?php // Opens the PHP execution block

// Include core configuration and global helper functions
require_once __DIR__ . '/../core/core.php';

// Include the CartController class for managing cart data and orders
require_once __DIR__ . '/../controllers/CartController.php';

// Include the CustomerController class for managing customer details
require_once __DIR__ . '/../controllers/CustomerController.php';

// Ensure the user is logged in before continuing
require_login();

// Define Paystack API credentials and endpoints
define('PAYSTACK_SECRET_KEY', 'sk_test_ddd754d1713bdf305aa871f8c4052378f63f78dc');
define('PAYSTACK_VERIFY_URL', 'https://api.paystack.co/transaction/verify/');

// Simple helper function to handle failures: saves error message and redirects
function payment_failed($message) {
    $_SESSION['error'] = $message;
    header('Location: ../views/payment_failed.php');
    exit();
}

// Get the payment reference string from the URL query parameters
$reference = $_GET['ref'] ?? '';

// Check if the reference exists and follows a valid format
if (!is_string($reference) || !preg_match('/^[A-Za-z0-9._=-]{1,100}$/', $reference)) {
    payment_failed('Invalid payment reference.');
}

// Get the user's IP address and customer ID
$ip = get_ip();
$customer_id = (int) $_SESSION['customer_id'];

// Create a cart controller instance and fetch cart items
$controller = new CartController();
$items = $controller->getCartItems($ip);

// If the cart is empty, show an error
if (empty($items)) {
    payment_failed('Your cart is empty. Reference: ' . $reference);
}

// Calculate total amount and convert it to pesewas (smallest currency unit)
$total = $controller->cartTotal($ip);
$expectedPesewas = (int) round($total * 100);

// Setup stream context options to securely query the Paystack API via HTTP GET
$options = [
    'http' => [
        'method' => 'GET',
        'header' => "Authorization: Bearer " . PAYSTACK_SECRET_KEY . "\r\n" .
                    "Content-Type: application/json\r\n"
    ]
];
$context = stream_context_create($options);

// Call Paystack API to verify the transaction (using native file_get_contents for simplicity)
$verifyUrl = PAYSTACK_VERIFY_URL . rawurlencode($reference);
$response = @file_get_contents($verifyUrl, false, $context);

// Check if the API request failed entirely
if ($response === false) {
    error_log('Paystack connection failed for reference: ' . $reference);
    payment_failed('Could not connect to payment gateway. Please try again.');
}

// Decode the JSON response from Paystack into an associative array
$result = json_decode($response, true);

// Safely extract verification data from the response result array
$apiData   = $result['data'] ?? [];
$isSuccess = ($result['status'] ?? false) === true;
$isRefMatch = ($apiData['reference'] ?? '') === $reference;
$isPaid    = ($apiData['status'] ?? '') === 'success';
$isAmountOk = ((int) ($apiData['amount'] ?? 0)) === $expectedPesewas;
$isCurrencyOk = ($apiData['currency'] ?? '') === 'GHS';

// Verify that all transaction checks passed successfully
if (!$isSuccess || !$isRefMatch || !$isPaid || !$isAmountOk || !$isCurrencyOk) {
    error_log('Payment verification failed for ' . $reference . ': ' . $response);
    payment_failed('Payment could not be verified or amount mismatch.');
}

// Save the verified order into the database and get the new order ID
$order_id = $controller->processPayment($customer_id, $ip, $reference, $total);

// Check if saving the order failed
if (!$order_id) {
    payment_failed('Payment was successful, but saving your order failed. Reference: ' . $reference);
}

// Fetch complete order and customer details to send a confirmation email
$order = $controller->getOrderById($order_id);
$customer = (new CustomerController())->getCustomerById($customer_id);

if ($order && $customer) {
    // Build the list of purchased products for the email body
    $itemLines = [];
    foreach ($order['items'] as $item) {
        $lineTotal = number_format($item['qty'] * $item['product_price'], 2);
        $itemLines[] = "{$item['product_title']} x {$item['qty']} - GH₵ {$lineTotal}";
    }

    // Format the full confirmation email message body cleanly
    $emailBody = "Hello " . $customer['customer_name'] . ",\n\n"
               . "Thank you for your order! Your payment was successful.\n\n"
               . "Invoice #: " . $order['invoice_no'] . "\n"
               . "Date: " . $order['order_date'] . "\n\n"
               . implode("\n", $itemLines) . "\n\n"
               . "Total Paid: GH₵ " . number_format($order['amt'], 2) . "\n"
               . "Reference: " . $reference . "\n\n"
               . "Shoppn Team";

    $emailHeaders = "From: Shoppn <no-reply@shoppn.com>\r\nContent-Type: text/plain; charset=UTF-8";

    // Attempt to send the receipt email (silently logs an error if mail server fails)
    @mail($customer['customer_email'], 'Your Shoppn Receipt - Invoice #' . $order['invoice_no'], $emailBody, $emailHeaders);
}

// Redirect the user to the success receipt page with their new order ID
header('Location: ../views/payment_success.php?order_id=' . (int) $order_id);
exit();

?> 