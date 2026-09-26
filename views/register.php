<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Register Page</title>
    <!-- Same styling as login page - blue background with white form card -->
    <link rel="stylesheet" href="../css/loginandsignup.css">
    
</head>

<body>
    <!-- White card container centered on blue background -->
    <div class = "wrapper">
        <!-- Large heading -->
        <h1> Register </h1>
        <!-- Error message area - shows validation errors if form submission fails -->
        <p id ="error-message"></p>
           
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
                <input type="password" name="password" id ="password-input" placeholder="Password" />
            </div>

            <!-- Confirm password - user re-enters password to make sure they typed it correctly -->
            <div>
                <label for ="confirm-password-input">Confirm password: 
                </label>    
                <input type="password" required name="confirm-password" id ="confirm-password-input" placeholder="Confirm Password" />
            </div>

            <!-- Submit button - validates and submits the form -->
            <button type="submit" id="signup-button" onclick="Validate(event)" >Sign Up</button>

            <!-- Link back to login page for existing users -->
            <p>Already have an account? <a href="login.php">Log In</a></p>
        

        </form>
    </div>
    
   
</body>
</html>