
<?php
    if($_POST["submit"]) {
        echo $_POST["username"];
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>Login</h1>
    <form method="POST" action="./index.php">
        <p><input type="text" name="username" placeholder="Username"/></p>
        <p><input type="password"  placeholder="Password"/></p>
        <p><input type="submit" value="Login" name="submit"/></p>
    </form>
</body>
</html>