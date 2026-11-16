<?php
require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/CustomerController.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit('Invalid request method.');
}

// Sends the user back to the form with an error, keeping what they typed (except passwords)
function register_error($message, $old) {
    $_SESSION['error'] = $message;
    $_SESSION['old_register'] = $old;
    header('Location: ../views/register.php');
    exit();
}

// Clean every text input
$firstname = trim(strip_tags($_POST['firstname'] ?? ''));
$lastname  = trim(strip_tags($_POST['Lastname'] ?? ''));
$email     = trim(strip_tags($_POST['email'] ?? ''));
$password  = $_POST['password'] ?? '';
$confirm   = $_POST['confirm-password'] ?? '';
$country   = trim(strip_tags($_POST['country'] ?? ''));
$city      = trim(strip_tags($_POST['city'] ?? ''));
$phoneCode = trim(strip_tags($_POST['phone_code'] ?? ''));
$number    = trim(strip_tags($_POST['contact'] ?? ''));

$old = ['firstname' => $firstname, 'lastname' => $lastname, 'email' => $email, 'country' => $country, 'city' => $city, 'phone_code' => $phoneCode, 'contact' => $number];

$name = $firstname . ' ' . $lastname;
// Phone is stored as code + digits, e.g. +233241234567 (customer_contact is VARCHAR 15)
$contact = ($phoneCode === 'other' ? '' : $phoneCode) . preg_replace('/[\s\-]/', '', $number);

$countries = ['Ghana', 'Nigeria', 'Kenya', 'South Africa', 'United States', 'United Kingdom', 'Canada', 'Other'];

// Check every field against the database limits
if (strlen($firstname) < 2 || strlen($lastname) < 2 || strlen($name) > 100) {
    register_error('First and last name must each be at least 2 characters (100 max in total).', $old);
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 50) {
    register_error('Enter a valid email address (50 characters max).', $old);
}
if (!preg_match('/^(?=.*[A-Za-z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$/', $password)) {
    register_error('Password needs 8+ characters, including a letter, a number and a symbol.', $old);
}
if ($password !== $confirm) {
    register_error('Passwords are not the same.', $old);
}
if (!in_array($country, $countries, true)) {
    register_error('Please select your country.', $old);
}
if ($city === '' || strlen($city) > 30) {
    register_error('City is required (30 characters max).', $old);
}
if (!preg_match('/^[0-9\s\-]{6,15}$/', $number) || !preg_match('/^\+?[0-9]{6,15}$/', $contact)) {
    register_error('Enter a valid phone number (15 digits max including the country code).', $old);
}

$controller = new CustomerController();
$result = $controller -> register([
    'name' => $name,
    'email' => $email,
    'pass' => $password,
    'country' => $country,
    'city' => $city,
    'contact' => $contact,
]);

if (!$result['success']) {
    register_error($result['error'], $old);
}

// Account created: log the new customer in
unset($_SESSION['old_register']);
session_regenerate_id(true);
$_SESSION['customer_id'] = $result['customer_id'];
$_SESSION['customer_name'] = $name;
$_SESSION['customer_email'] = $email;
$_SESSION['user_role'] = 2;
$_SESSION['success'] = 'Welcome to Shoppn! Your account has been created.';

header('Location: ../views/account/my_account.php');
exit();