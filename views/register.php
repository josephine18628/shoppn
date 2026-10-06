<?php

error_reporting(E_ALL);
ini_set("display_errors", 1);

require_once __DIR__ . '/../core/core.php';
function checkPassword($pwd, &$errors) {
    $errors_init = $errors;

    if (strlen($pwd) < 8) {
        $errors[] = "Password too short!";
    }

    if (!preg_match("#[0-9]+#", $pwd)) {
        $errors[] = "Password must include at least one number!";
    }

    if (!preg_match("#[a-zA-Z]+#", $pwd)) {
        $errors[] = "Password must include at least one letter!";
    }  
    
    if (!preg_match("#[^a-zA-Z0-9]+#", $pwd)) {
        $errors[] = "Password must include at least one symbol!";
    }  

    return ($errors == $errors_init);
}

$errorMessage = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $db = new Database();
    $conn = $db -> getConnection();

    $firstname = trim($_POST["firstname"]);
    $lastname = trim($_POST["Lastname"]);
    $email = trim($_POST["email"]);
    $password = ($_POST["password"]);
    $confirmPassword = $_POST["confirm-password"];
    $user_role = $_POST["user_role"];
    $country = trim($_POST["country"]);
    $city = trim($_POST["city"]);
    $phoneCode = trim($_POST["phone_code"]);
    $phoneNumber = trim($_POST["contact"]);
    $contact = $phoneCode . " " . $phoneNumber;


    $customer_name = $firstname . " " . $lastname;

    $passwordErrors = [];
    $passwordIsStrong = checkPassword($password, $passwordErrors);


    if(!$passwordIsStrong) {
        $errorMessage = implode(" ", $passwordErrors);
    }

    else if($password !== $confirmPassword) {
        $errorMessage = "Passwords are not the same.";
    }
    else {
        $checkStmt = $conn->prepare("SELECT customer_id FROM customer WHERE customer_email = ?");
        $checkStmt -> bind_param("s", $email);
        $checkStmt -> execute();
        $checkStmt -> store_result();

        if ($checkStmt -> num_rows > 0) {
            $errorMessage = "An account with the same email already exists.";
        }
        else {
            $hashedpassword = password_hash($password, PASSWORD_DEFAULT);
            $insertStmt = $conn -> prepare("INSERT INTO customer(customer_name, customer_email, customer_pass, user_role, customer_country, customer_city, customer_contact) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $insertStmt -> bind_param("sssisss", $customer_name, $email, $hashedpassword, $user_role, $country, $city, $contact);
            if ($insertStmt -> execute()) {
                header("Location: login.php?registered=1");
                exit();
            }
            else {
                $errorMessage = "Error. Please try again";
            }
            $insertStmt -> close();
        }
        $checkStmt -> close();
    }
    $conn -> close();
}



?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Register Page</title>
    <link rel="stylesheet" href="../css/style.css" />
    
</head>

<body>
    <!-- White card container centered on blue background -->
    <div class = "wrapper">
        <!-- Large heading -->
        <h1> Register </h1>
        <!-- Error message area - shows validation errors if form submission fails -->
        <p id ="error-message"><?php echo $errorMessage; ?></p>
        <form id="register-form" method="POST" action="register.php" novalidate>
            <div class="form-row-pair">
                <div class="form-group">
                    <label for ="firstname-input">First Name:
                    </label>
                    <input type ="text" required name="firstname" id ="firstname-input" placeholder="First Name" />
                    <small class="field-error" id="firstname-error"></small>
                </div>

                <div class="form-group">
                    <label for ="lastname-input">Last name:
                    </label>
                    <input type ="text" required name="Lastname" id ="lastname-input" placeholder="Last Name" />
                </div>
            </div>
        
            
            <div class="form-group">
                <label for = "email-input">Email:
                </label>
                <input type ="email" required name="email" id ="email-input" placeholder="Email" />
            </div>

            <div class="form-group">
                <label for ="password-input">Password:
                </label>
                <input type="password"  required name="password" id ="password-input" placeholder="Password" minlength="8" pattern="(?=.*[A-Za-z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}" title="Ät least 8 characters, including a letter,a number, and a symbol." />
            </div>

            <!-- Confirm password - user re-enters password to make sure they typed it correctly -->
            <div class="form-group">
                <label for ="confirm-password-input">Confirm password: 
                </label>    
                <input type="password" required name="confirm-password" id ="confirm-password-input" placeholder="Confirm Password" />
            </div>

            <div class="form-group">
                <label for="role-input">Account Type:</label>
                <select required name="user_role" id="role-input">
                    <option value="1">Admin</option>
                    <option value="2" selected>Customer</option>
                </select>
            </div>

            <div class="form-group">
                <label for="country-input">Country:</label>
                <select required name="country" id="country-input">
                    <option value="" disabled selected>Select your country</option>
                    <option value="Ghana">Ghana</option>
                    <option value="Nigeria">Nigeria</option>
                    <option value="Kenya">Kenya</option>
                    <option value="South Africa">South Africa</option>
                    <option value="United States">United States</option>
                    <option value="United Kingdom">United Kingdom</option>
                    <option value="Canada">Canada</option>
                    <option value="Other">Other</option>
                </select>
            </div>

            <div class="form-group">
                <label for="city-input">City:</label>
                <input type="text" required name="city" id="city-input" placeholder="City" />
            </div>

            <div class="form-group">
                <label for="contact-input">Phone number:</label>
                <div class="phone-row">
                    <select required name="phone_code" id="phone-code-input">
                        <option value="" disabled selected>Code</option>
                        <option value="+233">🇬🇭 +233 (Ghana)</option>
                        <option value="+234">🇳🇬 +234 (Nigeria)</option>
                        <option value="+254">🇰🇪 +254 (Kenya)</option>
                        <option value="+27">🇿🇦 +27 (South Africa)</option>
                        <option value="+1">🇺🇸 +1 (US/Canada)</option>
                        <option value="+44">🇬🇧 +44 (UK)</option>
                        <option value="other">Other</option>
                    </select>
                    <input
                        type="tel"
                        required
                        name="contact"
                        id="contact-input"
                        placeholder="e.g. 24 123 4567"
                        pattern="[0-9\s\-]{6,15}"
                        title="Enter digits only (spaces and dashes allowed), 6 to 15 characters."
                    />
                </div>
            </div>

            <!-- Submit button - validates and submits the form -->
            <button type="submit" id="signup-button"  >Sign Up</button>

            <!-- Link back to login page for existing users -->
            <p>Already have an account? <a href="login.php">Log In</a></p>
        

        </form>
    </div>

    <script src="../js/validate.js"></script>
    
   
</body>
</html>