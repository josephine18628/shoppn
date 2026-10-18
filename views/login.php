<?php
require_once __DIR__ . '/../core/core.php';

// Message to show above the form (set by login_action.php or the cart)
$errorMessage = "";
if (isset($_SESSION["error"])) {
    $errorMessage = $_SESSION["error"];
    unset($_SESSION["error"]);
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

            <form id="login-form" method="POST" action="../actions/login_action.php" novalidate>

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