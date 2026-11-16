<?php
require_once __DIR__ . '/../classes/CustomerClass.php';
class CustomerController {
    private $customerModel;
    public function __construct() {
        $this->customerModel = new CustomerClass();
    }

    // $data: name, email, pass, country, city, contact
    public function register($data) {
        if ($this->customerModel->emailExists($data['email'])) {
            return ['success' => false, 'error' => 'Email already registered'];
        }
        $id = $this->customerModel->addCustomer($data['name'], $data['email'], $data['pass'], $data['country'], $data['city'], $data['contact']);
        if (!$id) {
            return ['success' => false, 'error' => 'Could not create the account. Try again.'];
        }
        return ['success' => true, 'customer_id' => $id];
    }

    // Returns the customer array if login works, or an error array if it does not
    public function login($email, $pass) {
        $customer = $this->customerModel->login($email, $pass);
        if ($customer) {
            return $customer;
        }
        return ['error' => 'Incorrect email or password'];
    }

    public function getCustomerById($id) {
        return $this->customerModel->getCustomerById($id);
    }

    // $data: name, email, country, city, contact, image
    public function editAccount($id, $data) {
        if ($this->customerModel->emailTakenByOther($data['email'], $id)) {
            return ['success' => false, 'error' => 'That email is already used by another account'];
        }
        $ok = $this->customerModel->editCustomer($id, $data['name'], $data['email'], $data['country'], $data['city'], $data['contact'], $data['image']);
        return $ok ? ['success' => true] : ['success' => false, 'error' => 'Could not update your details. Try again.'];
    }

    public function changePassword($id, $currentPass, $newPass) {
        $customer = $this->customerModel->getCustomerById($id);
        if (!$customer || !password_verify($currentPass, $customer['customer_pass'])) {
            return ['success' => false, 'error' => 'Your current password is wrong'];
        }
        $ok = $this->customerModel->changePassword($id, $newPass);
        return $ok ? ['success' => true] : ['success' => false, 'error' => 'Could not change the password. Try again.'];
    }

    public function deleteAccount($id, $password) {
        $customer = $this->customerModel->getCustomerById($id);
        if (!$customer || !password_verify($password, $customer['customer_pass'])) {
            return ['success' => false, 'error' => 'Wrong password. Your account was not deleted.'];
        }
        $ok = $this->customerModel->deleteCustomer($id);
        return $ok ? ['success' => true, 'image' => $customer['customer_image']] : ['success' => false, 'error' => 'Could not delete the account. Try again.'];
    }
}