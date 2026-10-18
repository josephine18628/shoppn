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
}