<?php
// classes/CustomerClass.php
require_once __DIR__ . '/../core/db_class.php';

class CustomerClass extends Database {
    // Finds one customer by email; returns the row or false if not found
    public function getCustomerByEmail($email) {
        $stmt = $this -> conn -> prepare('SELECT * FROM customer WHERE customer_email = ?');
        $stmt -> bind_param('s', $email);
        $stmt -> execute();
        $row = $stmt -> get_result() -> fetch_assoc();
        $stmt -> close();
        return $row ? $row : false;
    }

    // Checks the email and password; returns the customer row if correct, false if not
    public function login($email, $pass) {
        $row = $this -> getCustomerByEmail($email);
        if ($row && password_verify($pass, $row['customer_pass'])) {
            return $row;
        }
        return false;
    }
}