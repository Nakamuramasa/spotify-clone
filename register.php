<?php
include("includes/config.php");
include("includes/classes/Account.php");
include("includes/classes/Constants.php");

$account = new Account($con);

include("includes/handlers/register-handler.php");
include("includes/handlers/login-handler.php");

function getInputValue($name){
    if(isset($_POST[$name])) echo $_POST[$name];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Welcome to spotify</title>
    <link rel="stylesheet" type="text/css" href="assets/css/register.css">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="assets/js/register.js"></script>
</head>
<body>
    <div id="background">
        <div id="loginContainer">
            <div id="inputContainer">
                <form action="register.php" id="loginForm" method="POST">
                    <h2>Login to your acount</h2>
                    <p>
                        <?php echo $account->getError(Constants::$loginFailed); ?>
                        <label for="loginUsername">Username</label>
                        <input type="text" name="loginUsername" id="loginUsername" placeholder="JohnSmith" required>
                    </p>
                    <p>
                        <label for="loginPassword">Password</label>
                        <input type="password" name="loginPassword" id="loginPassword" placeholder="Your password" required>
                    </p>

                    <button type="submit" name="loginButton">LOG IN</button>

                    <div class="hasAccountText">
                        <span id="hideLogin">Don't have an account yet? Sign up here.</span>
                    </div>
                </form>

                <form action="register.php" id="registerForm" method="POST">
                    <h2>Create your free account</h2>
                    <p>
                        <?php echo $account->getError(Constants::$usernameCharacters); ?>
                        <?php echo $account->getError(Constants::$usernameTaken); ?>
                        <label for="username">Username</label>
                        <input type="text" name="username" id="username" placeholder="JohnSmith" value="<?php getInputValue('username') ?>" required>
                    </p>

                    <p>
                        <?php echo $account->getError(Constants::$firstNameCharacters); ?>
                        <label for="firstName">First name</label>
                        <input type="text" name="firstName" id="firstName" placeholder="john" value="<?php getInputValue('firstName') ?>" required>
                    </p>

                    <p>
                        <?php echo $account->getError(Constants::$lastNameCharacters); ?>
                        <label for="lastName">Last name</label>
                        <input type="text" name="lastName" id="lastName" placeholder="smith" value="<?php getInputValue('lastName') ?>" required>
                    </p>

                    <p>
                        <?php echo $account->getError(Constants::$emailsDoNotMatch); ?>
                        <?php echo $account->getError(Constants::$emailInvalid); ?>
                        <?php echo $account->getError(Constants::$emailTaken); ?>
                        <label for="email">Email</label>
                        <input type="email" name="email" id="email" placeholder="john@example.com" value="<?php getInputValue('email') ?>" required>
                    </p>

                    <p>
                        <label for="email2">Confirm email</label>
                        <input type="email" name="email2" id="email2" placeholder="john@example.com" value="<?php getInputValue('email2'); ?>" required>
                    </p>

                    <p>
                        <?php echo $account->getError(Constants::$passwordsDoNoMatch); ?>
                        <?php echo $account->getError(Constants::$passwordCharacters); ?>
                        <?php echo $account->getError(Constants::$passwordNotAlphanumeric); ?>
                        <label for="password">Password</label>
                        <input type="password" name="password" id="password" placeholder="Your password" required>
                    </p>

                    <p>
                        <label for="password2">Confirm password</label>
                        <input type="password" name="password2" id="password2" placeholder="Your password" required>
                    </p>

                    <button type="submit" name="registerButton">SIGN UP</button>

                    <div class="hasAccountText">
                        <span id="hideRegister">Already have an account? Click here.</span>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>