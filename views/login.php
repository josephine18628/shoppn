<?php
session_start();
$errorMessage = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    require_once __DIR__ . "/../core/db_class.php";
    $db = new Database();
    $conn = $db -> getConnection();

    $email = trim($_POST["email"]);
    $password = ($_POST["password"]);

    $stmt = $conn->prepare("SELECT customer_id, customer_name, customer_pass, user_role FROM customer WHERE customer_email = ?");
    $stmt -> bind_param("s", $email);
    $stmt -> execute();
    $result = $stmt -> get_result();

    if ($stmt -> num_rows === 1) {
        $stmt -> bind_result($customer_id, $customer_name, $customer_pass, $user_role);
        $stmt -> fetch();
        

        if(password_verify($password, $customer["customer_pass"])) {
            $_SESSION["customer_id"] = $customer["customer_id"];
            $_SESSION["customer_name"] = $customer["customer_name"];
            $_SESSION["user_role"] = $customer["user_role"];

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
    
   
</head>
<body>
    <!-- White card container centered on the blue background -->
    <div class = "wrapper">
        <!-- Large heading at the top -->
        <h1>Login</h1>

        <form method="POST" action="login.php">
    
        
            <!-- Hidden error message that shows up if login fails -->
            <p id ="error-message"><?php echo $errorMessage; ?></p>
          
            <div>
                <label for = "email-input">Email:
                </label>
                <input type ="email" required name ="email" id ="email-input" placeholder="Email" />
            </div>

            <div>
                <label for ="password-input">Password:
                </label>
                <input type ="password" required name="password" id ="password-input" placeholder="Password" />
            </div>

            <!-- Remember me checkbox - lets users stay logged in -->
            <div class="remember-me-container">
                <input type="checkbox" name="remember_me" id="remember-me-checkbox" />
                <label for="remember-me-checkbox">Remember Me</label>
            </div>
            
            <!-- Submit button - triggers the login process via JavaScript -->
            <button type="submit" id="login-button" >Log In</button>

            <!-- Link to signup page for users who don't have an account yet -->
            <p>Don't have an account? <a href="register.php"> Register</a></p>
        
        </form>
    </div>



</body>
</html>