<?php
include("includes/config.php");
include("includes/classes/Account.php");
include("includes/classes/Constants.php");

$account = new Account($con);

include("includes/handlers/register-handler.php");
include("includes/handlers/login-handler.php");

function getInputValue($value){
    if(isset($_POST[$value])) echo $_POST[$value];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Welcome to spotify</title>
</head>
<body>
    <div id="inputContainer">
        <form action="register.php" id="loginForm" method="POST">
            <h2>Login to your acount</h2>
            <p>
                <label for="loginUsername">Username</label>
                <input type="text" name="loginUsername" id="loginUsername" placeholder="JohnSmith" required>
            </p>
            <p>
                <label for="loginPassword">Password</label>
                <input type="password" name="loginPassword" id="loginPassword" placeholder="Your password" required>
            </p>
            <button type="submit" name="loginButton">LOG IN</button>
        </form>

        <form action="register.php" id="loginForm" method="POST">
            <h2>Create your free account</h2>
            <p>
                <?php echo $account->getError(Constants::$usernameCharacters); ?>
                <label for="username">Username</label>
                <input type="text" name="username" id="username" placeholder="JohnSmith" value="<? getInputValue('username'); ?>" required>
            </p>

            <p>
                <?php echo $account->getError(Constants::$firstNameCharacters); ?>
                <label for="firstName">First name</label>
                <input type="text" name="firstName" id="firstName" placeholder="john" value="<? getInputValue('firstName'); ?>" required>
            </p>

            <p>
                <?php echo $account->getError(Constants::$lastNameCharacters); ?>
                <label for="lastName">Last name</label>
                <input type="text" name="lastName" id="lastName" placeholder="smith" value="<? getInputValue('lastName'); ?>" required>
            </p>

            <p>
                <?php echo $account->getError(Constants::$emailsDoNotMatch); ?>
                <?php echo $account->getError(Constants::$emailInvalid); ?>
                <label for="email">Email</label>
                <input type="email" name="email" id="email" placeholder="john@example.com" value="<? getInputValue('email'); ?>" required>
            </p>

            <p>
                <label for="email2">Confirm email</label>
                <input type="email" name="email2" id="email2" placeholder="john@example.com" value="<? getInputValue('email2'); ?>" required>
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
        </form>
    </div>
</body>
</html>