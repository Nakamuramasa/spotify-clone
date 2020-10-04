<?php
include("includes/config.php");

// session_destroy();

if(isset($_SESSION['userLoggedIn'])) $userLoggedIn = $_SESSION['userLoggedIn'];
else header("Location: register.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Hello</title>
</head>
<body>
    <h1>Hello World from Enginx!!</h1>
</body>
</html>
