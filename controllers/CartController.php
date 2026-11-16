<?php
require_once __DIR__ . '/../classes/CartClass.php';
class CartController {
    private $cartModel;
    public function __construct() {
        $this->cartModel = new CartClass();
    }

    public function inCart($product_id, $ip) {
        return $this->cartModel->inCart($product_id, $ip);
    }

    public function cartAddition($product_id, $qty, $ip, $customer_id = null) {
    return $this->cartModel->cartAddition($product_id, $qty, $ip, $customer_id);
    }

    public function getCartItems($ip) {
        return $this-> cartModel->getCartItems($ip);
    }

    public function cartCount($ip) {
        return $this->cartModel->cartCount($ip);
    }

    public function cartTotal($ip) {
        return $this->cartModel->cartTotal($ip);
    }

    public function removeFromCart($product_id, $ip) {
        return $this->cartModel->removeFromCart($product_id, $ip);
    }
 
    public function updateQty($product_id, $new_qty, $ip) {
        return $this->cartModel->updateQty($product_id, $new_qty, $ip);
    }

    public function processPayment($customer_id, $ip, $reference, $amount) {
        return $this->cartModel->processPayment($customer_id, $ip, $reference, $amount);
    }

    public function getOrderById($order_id) {
        return $this->cartModel->getOrderById($order_id);
    }

    public function getOrdersByCustomer($customer_id) {
        return $this->cartModel->getOrdersByCustomer($customer_id);
    }

    public function getRecentlyPurchased($customer_id, $limit = 4) {
        return $this->cartModel->getRecentlyPurchased($customer_id, $limit);
    }
}
