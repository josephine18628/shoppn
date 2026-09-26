<!-- Login Page - Users sign in to their existing account -->
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Basic page setup -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Login Page</title>
    <!-- Using the authentication stylesheet which has the blue background and form styling -->
    <link rel="stylesheet" href="../css/loginandsignup.css">
   
</head>
<body>
    <!-- White card container centered on the blue background -->
    <div class = "wrapper">
        <!-- Large heading at the top -->
        <h1>Login</h1>
    
        
            <!-- Hidden error message that shows up if login fails -->
            <p id ="error-message"></p>
          
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
            <button type="button" id="login-button" >Log In</button>

            <!-- Link to signup page for users who don't have an account yet -->
            <p>Don't have an account? <a href="register.php"> Register</a></p>
        
        </form>
    </div>



</body>
</html>