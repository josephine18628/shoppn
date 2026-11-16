<?php
// classes/CustomerClass.php
require_once __DIR__ . '/../core/db_class.php';

class CustomerClass extends Database {

    // ---------- Register ----------

    // true if an account already uses this email
    public function emailExists($email) {
        $stmt = $this -> conn -> prepare('SELECT customer_email FROM customer WHERE customer_email = ?');
        $stmt -> bind_param('s', $email);
        $stmt -> execute();
        $found = $stmt -> get_result() -> num_rows > 0;
        $stmt -> close();
        return $found;
    }

    // Adds a new customer (always role 2 = customer); returns the new customer_id
    public function addCustomer($name, $email, $pass, $country, $city, $contact) {
        $hash = password_hash($pass, PASSWORD_BCRYPT);
        $stmt = $this -> conn -> prepare('INSERT INTO customer (customer_name, customer_email, customer_pass, customer_country, customer_city, customer_contact, user_role) VALUES (?, ?, ?, ?, ?, ?, 2)');
        $stmt -> bind_param('ssssss', $name, $email, $hash, $country, $city, $contact);
        $stmt -> execute();
        $newId = $stmt -> insert_id;
        $stmt -> close();
        return $newId;
    }

  
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

    public function getCustomerById($id) {
        $stmt = $this -> conn -> prepare('SELECT * FROM customer WHERE customer_id = ?');
        $stmt -> bind_param('i', $id);
        $stmt -> execute();
        $row = $stmt -> get_result() -> fetch_assoc();
        $stmt -> close();
        return $row ? $row : false;
    }

    // true if the email belongs to a different customer
    public function emailTakenByOther($email, $id) {
        $stmt = $this -> conn -> prepare('SELECT customer_id FROM customer WHERE customer_email = ? AND customer_id <> ?');
        $stmt -> bind_param('si', $email, $id);
        $stmt -> execute();
        $found = $stmt -> get_result() -> num_rows > 0;
        $stmt -> close();
        return $found;
    }

    public function editCustomer($id, $name, $email, $country, $city, $contact, $image) {
        $stmt = $this -> conn -> prepare('UPDATE customer SET customer_name = ?, customer_email = ?, customer_country = ?, customer_city = ?, customer_contact = ?, customer_image = ? WHERE customer_id = ?');
        $stmt -> bind_param('ssssssi', $name, $email, $country, $city, $contact, $image, $id);
        $ok = $stmt -> execute();
        $stmt -> close();
        return $ok;
    }

    public function changePassword($id, $newPass) {
        $hash = password_hash($newPass, PASSWORD_BCRYPT);
        $stmt = $this -> conn -> prepare('UPDATE customer SET customer_pass = ? WHERE customer_id = ?');
        $stmt -> bind_param('si', $hash, $id);
        $ok = $stmt -> execute();
        $stmt -> close();
        return $ok;
    }

    // Deletes the customer and everything linked to them, all or nothing
    public function deleteCustomer($id) {
        $this -> conn -> begin_transaction();
        try {
            $queries = [
                'DELETE FROM payment WHERE customer_id = ?',
                'DELETE orderdetails FROM orderdetails JOIN orders ON orderdetails.order_id = orders.order_id WHERE orders.customer_id = ?',
                'DELETE FROM orders WHERE customer_id = ?',
                'DELETE FROM cart WHERE c_id = ?',
                'DELETE FROM customer WHERE customer_id = ?',
            ];
            foreach ($queries as $sql) {
                $stmt = $this -> conn -> prepare($sql);
                $stmt -> bind_param('i', $id);
                $stmt -> execute();
                $stmt -> close();
            }
            $this -> conn -> commit();
            return true;
        } catch (Throwable $e) {
            $this -> conn -> rollback();
            error_log($e -> getMessage());
            return false;
        }
    }
}