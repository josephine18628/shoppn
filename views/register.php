<?php

error_reporting(E_ALL);
ini_set("display_errors", 1);

$errorMessage = "";
$errorMessage = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    require __DIR__ . "/../core/db_class.php";
    $db = new Database();
    $conn = $db -> getConnection();

    $firstname = trim($_POST["firstname"]);
    $lastname = trim($_POST["Lastname"]);
    $email = trim($_POST["email"]);
    $password = ($_POST["password"]);
    $confirmPassword = $_POST["confirm-password"];
    $country = trim($_POST["country"]);
    $city = trim($_POST["city"]);
    $contact = trim($_POST["contact"]);


    $customer_name = $firstname . " " . $lastname;

    if($password !== $confirmPassword) {
        $errorMessage = "Passwords are not the same.";
    }
    else {
        $checkStmt = $conn->prepare("SELECT customer_id FROM customer WHERE customer_email = ?");
        $checkStmt -> bind_param("s", $email);
        $checkStmt -> execute();
        $checkStmt -> store_result();

        if ($checkStmt -> num_rows > 0) {
            $errorMessage = "An account with the smae email already exists.";
        }
        else {
            $hashedpassword = password_hash($password, PASSWORD_DEFAULT);
            $insertStmt = $conn -> prepare("INSERT INTO customer(customer_name, customer_email, customer_pass, customer_country, customer_city, customer_contact) VALUES (?, ?, ?, ?, ?, ?)");
            $insertStmt -> bind_param("ssssss", $customer_name, $email, $hashedpassword, $country, $city, $contact);

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
    
</head>

<body>
    <!-- White card container centered on blue background -->
    <div class = "wrapper">
        <!-- Large heading -->
        <h1> Register </h1>
        <!-- Error message area - shows validation errors if form submission fails -->
        <p id ="error-message"><?php echo $errorMessage; ?></p>

        <form method="POST" action="register.php">
            <div>
                <label for ="firstname-input">First Name:
                </label>
                <input type ="text" required name="firstname" id ="firstname-input" placeholder="First Name" />
            </div>

            
            <div>
                <label for ="lastname-input">Last name:
                </label>
                <input type ="text" required name="Lastname" id ="lastname-input" placeholder="Last Name" />
            </div>
            
            <div>
                <label for = "email-input">Email:
                </label>
                <input type ="email" required name="email" id ="email-input" placeholder="Email" />
            </div>

            <div>
                <label for ="password-input">Password:
                </label>
                <input type="password"  required name="password" id ="password-input" placeholder="Password" />
            </div>

            <!-- Confirm password - user re-enters password to make sure they typed it correctly -->
            <div>
                <label for ="confirm-password-input">Confirm password: 
                </label>    
                <input type="password" required name="confirm-password" id ="confirm-password-input" placeholder="Confirm Password" />
            </div>

            <div>
                <label for="country-input">Country:</label>
                <input type="text" required name="country" id="country-input" placeholder="Country" />
            </div>

            <div>
                <label for="city-input">City:</label>
                <input type="text" required name="city" id="city-input" placeholder="City" />
            </div>

            <div>
                <label for="contact-input">Phone number:</label>
                <input type="text" required name="contact" id="contact-input" placeholder="Phone number" />
            </div>

            <!-- Submit button - validates and submits the form -->
            <button type="submit" id="signup-button"  >Sign Up</button>

            <!-- Link back to login page for existing users -->
            <p>Already have an account? <a href="login.php">Log In</a></p>
        

        </form>
    </div>
    
   
</body>
</html>