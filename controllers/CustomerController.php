<?php
require_once __DIR__ . '/../classes/CustomerClass.php';
class CustomerController {
    private $customerModel;
    public function __construct() {
        $this->customerModel = new CustomerClass();
    }

    // Returns the customer array if login works, or an error array if it does not
    public function login($email, $pass) {
        $customer = $this->customerModel->login($email, $pass);
        if ($customer) {
            return $customer;
        }
        return ['error' => 'Incorrect email or password'];
    }
}