<?php
require_once __DIR__ . '/../core/db_class.php';
class CartClass extends Database {
    public function inCart($product_id, $ip) {
        $stmt = $this->conn->prepare('SELECT p_id FROM cart WHERE p_id = ? AND ip_add = ?');
        $stmt -> bind_param('is', $product_id, $ip);
        $stmt -> execute();
        $found = $stmt -> get_result() -> num_rows > 0;
        $stmt -> close();
        return $found;
    }

    public function cartAddition($product_id, $qty, $ip, $customer_id = null) {
        if ($qty < 1) {
        return false;
        }

        $stmt = $this -> conn -> prepare('INSERT INTO cart (p_id, ip_add, c_id, qty) SELECT product_id, ?, ?, ? FROM products WHERE product_id = ?');
        $stmt -> bind_param('siii', $ip, $customer_id, $qty, $product_id);
        $stmt -> execute();
        $added = $stmt -> affected_rows > 0;
        $stmt -> close();
        return $added;

    }
    
    public function getCartItems($ip) {
        $stmt = $this -> conn -> prepare('SELECT cart.*, products.product_title, products.product_price, products.product_image FROM cart JOIN products ON cart.p_id = products.product_id WHERE cart.ip_add = ?');
        $stmt -> bind_param('s', $ip);
        $stmt -> execute();
        $rows = $stmt -> get_result() -> fetch_all(MYSQLI_ASSOC);
        $stmt -> close();
        return $rows;
    }

    public function cartCount($ip) {
        $stmt = $this -> conn -> prepare ('SELECT COALESCE(SUM(qty), 0) AS total FROM cart WHERE ip_add = ?');
        $stmt -> bind_param('s', $ip);
        $stmt -> execute();
        $rows = $stmt -> get_result() -> fetch_assoc();
        $stmt -> close();
        return (int)$rows['total'];
    }

    public function cartTotal($ip) {
    $total = 0.0;
    foreach($this -> getCartItems($ip) as $item) {
        $total += (int)$item['qty'] * (float)$item['product_price'];
    }
    return $total;
    }

    public function removeFromCart($product_id, $ip) {
        $stmt = $this -> conn -> prepare ('DELETE FROM cart WHERE p_id = ? AND ip_add = ?');
        $stmt -> bind_param('is', $product_id, $ip);
        $stmt -> execute();
        $removed = $stmt -> affected_rows > 0;
        $stmt -> close();
        return $removed;
    }

    public function updateQty($product_id, $new_qty, $ip) {
    if($new_qty < 1) {
        return false;
    }
    $stmt = $this -> conn -> prepare ('UPDATE cart SET qty = ? WHERE p_id = ? AND ip_add = ?');
        $stmt -> bind_param('iis', $new_qty, $product_id, $ip);
        $ok = $stmt -> execute();
        $stmt -> close();
        return $ok;
    }

    public function processPayment($customer_id, $ip, $reference, $amount) {
        $items = $this -> getCartItems($ip);
        if (empty($items)) {
            return false;
        }
        $this -> conn -> begin_transaction();
        try {
            // 1. Invoice number
            //mt_rand() generates a pseudo-random integer using the Mersenne Twister algorithm
            $invoice = mt_rand(100000, 999999);

            // 2. The order
            $stmt = $this -> conn -> prepare("INSERT INTO orders (customer_id, invoice_no, order_date, order_status) VALUES (?, ?, CURDATE(), 'paid')");
            $stmt -> bind_param('ii', $customer_id, $invoice);
            $stmt -> execute();
            $order_id = $stmt -> insert_id;
            $stmt -> close();

            // 3. Copy the cart rows into orderdetails
            $stmt = $this -> conn -> prepare('INSERT INTO orderdetails (order_id, product_id, qty) SELECT ?, p_id, qty FROM cart WHERE ip_add = ?');
            $stmt -> bind_param('is', $order_id, $ip);
            $stmt -> execute();
            $stmt -> close();

            // The payment record (amount, currency, date)
            $currency = 'GHS';
            $stmt = $this -> conn -> prepare('INSERT INTO payment (amt, customer_id, order_id, currency, payment_date) VALUES (?, ?, ?, ?, CURDATE())');
            $stmt -> bind_param('diis', $amount, $customer_id, $order_id, $currency);
            $stmt -> execute();
            $stmt -> close();

            // 4. Empty the cart
            $stmt = $this -> conn -> prepare('DELETE FROM cart WHERE ip_add = ?');
            $stmt -> bind_param('s', $ip);
            $stmt -> execute();
            $stmt -> close();

            $this -> conn -> commit();
            // 5. Return the new order_id
            return $order_id;
        } 
        catch (Throwable $e) {
            $this -> conn -> rollback();
            error_log('processPayment failed (ref ' . $reference . '): ' . $e -> getMessage());
            return false;
        }
    }

    // One order with its products, quantities, prices and the amount paid
    public function getOrderById($order_id) {
        $stmt = $this -> conn -> prepare('SELECT orders.*, payment.amt, payment.currency, payment.payment_date, customer.customer_name, customer.customer_email FROM orders LEFT JOIN payment ON payment.order_id = orders.order_id JOIN customer ON customer.customer_id = orders.customer_id WHERE orders.order_id = ?');
        $stmt -> bind_param('i', $order_id);
        $stmt -> execute();
        $order = $stmt -> get_result() -> fetch_assoc();
        $stmt -> close();
        if (!$order) {
            return false;
        }

        $stmt = $this -> conn -> prepare('SELECT orderdetails.product_id, orderdetails.qty, products.product_title, products.product_price, products.product_image FROM orderdetails JOIN products ON orderdetails.product_id = products.product_id WHERE orderdetails.order_id = ?');
        $stmt -> bind_param('i', $order_id);
        $stmt -> execute();
        $order['items'] = $stmt -> get_result() -> fetch_all(MYSQLI_ASSOC);
        $stmt -> close();
        return $order;
    }

    // Every order a customer has made, newest first
    public function getOrdersByCustomer($customer_id) {
        $stmt = $this -> conn -> prepare('SELECT orders.order_id, orders.invoice_no, orders.order_date, orders.order_status, payment.amt, COALESCE(SUM(orderdetails.qty), 0) AS item_count FROM orders LEFT JOIN payment ON payment.order_id = orders.order_id LEFT JOIN orderdetails ON orderdetails.order_id = orders.order_id WHERE orders.customer_id = ? GROUP BY orders.order_id, orders.invoice_no, orders.order_date, orders.order_status, payment.amt ORDER BY orders.order_id DESC');
        $stmt -> bind_param('i', $customer_id);
        $stmt -> execute();
        $rows = $stmt -> get_result() -> fetch_all(MYSQLI_ASSOC);
        $stmt -> close();
        return $rows;
    }

    // Products this customer bought most recently (no repeats)
    public function getRecentlyPurchased($customer_id, $limit = 4) {
        $stmt = $this -> conn -> prepare('SELECT products.*, MAX(orders.order_id) AS last_order FROM orderdetails JOIN orders ON orderdetails.order_id = orders.order_id JOIN products ON orderdetails.product_id = products.product_id WHERE orders.customer_id = ? GROUP BY products.product_id ORDER BY last_order DESC LIMIT ?');
        $stmt -> bind_param('ii', $customer_id, $limit);
        $stmt -> execute();
        $rows = $stmt -> get_result() -> fetch_all(MYSQLI_ASSOC);
        $stmt -> close();
        return $rows;
    }
}
