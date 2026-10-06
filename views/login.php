<?php

error_reporting(E_ALL);
ini_set("display_errors", 1);

require __DIR__ . "/../core/db_class.php";
$db = new Database();
$conn = $db -> getConnection();

$errorMessage = "";
session_start();
$errorMessage = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"]);
    $password = ($_POST["password"]);

    $stmt = $conn->prepare("SELECT customer_id, customer_name, customer_pass, user_role FROM customer WHERE customer_email = ?");
    $stmt -> bind_param("s", $email);
    $stmt -> execute();
    $stmt -> store_result();

    if ($stmt -> num_rows === 1) {
        $stmt -> bind_result($customer_id, $customer_name, $customer_pass, $user_role);
        $stmt -> fetch();
        

        if(password_verify($password, $customer_pass)) {
            $_SESSION["customer_id"] = $customer_id;
            $_SESSION["customer_name"] = $customer_name;
            $_SESSION["user_role"] = $user_role;

            header("Location: ../index.php");
            exit();

        }

        else {
            $errorMessage = "Wrong email or passowrd.";
        }
    }

    else {
        $errorMessage = "Incorrect email or password";
    }
    $stmt -> close();
    $conn -> close();
    
}
elseif (isset($_GET["registered"])) {
    $errorMessage = "Account created";
}


?>
<!-- Login Page - Users sign in to their existing account -->
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Basic page setup -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Login Page</title>
    <link rel="stylesheet" href="../css/style.css" />
    
   
</head>
<body>
    <div class="login-layout">
        
        <div class = "wrapper">
            <!-- Large heading at the top -->
            <h1>Login</h1>

            <p id="error-message">
                <?php echo $errorMessage; ?>
            </p>

            <form id="login-form" method="POST" action="login.php" novalidate>

                <div class="form-group">
                    <label for = "email-input">Email:
                    </label>
                    <input type ="email" required name ="email" id ="email-input" placeholder="Email" />
                </div>

                <div class="form-group">
                    <label for ="password-input">Password:
                    </label>
                    <input type ="password" required name="password" id ="password-input" placeholder="Password" minlength="8" pattern="(?=.*[A-Za-z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}" title="Ät least 8 characters, including a letter,a number, and a symbol."/>
                </div>

                <!-- Remember me checkbox - lets users stay logged in -->
                <div class="remember-me-container">
                    <input type="checkbox" name="remember_me" id="remember-me-checkbox" />
                    <label class="form-label" for="remember-me-checkbox">Remember Me</label>
                </div>
            
                <!-- Submit button - triggers the login process via JavaScript -->
                <button type="submit" id="login-button" >Log In</button>

                <!-- Link to signup page for users who don't have an account yet -->
                <p>Don't have an account? <a href="register.php"> Register</a></p>
        
            </form>
        </div>
        

    </div>

    <script src="../js/validate.js"></script>
    



</body>
</html>