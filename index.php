<?php
require_once __DIR__ . '/auth.php';
$error = '';
$databaseError = '';

if (isset($_POST['submit'])) {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    try {
        // Konekcija na lokalnu MySQL bazu (XAMPP).
        $conn = new mysqli('localhost', 'root', '', 'ranjiva_app');
        $conn->set_charset('utf8mb4');

        // Prvo provjeravamo username. Upit je namjerno ranjiv.
        $result = $conn->query("SELECT id FROM users WHERE username = '$username'");
        if ($result->num_rows === 0) {
            $error = 'Neispravno korisničko ime.';
        } else {
            // Tek kada username postoji, provjeravamo njegovu lozinku.
            $result = $conn->query("SELECT id FROM users WHERE username = '$username' AND password = '$password'");
            if ($result->num_rows === 0) {
                $error = 'Neispravna lozinka.';
            } else {
                // Preusmjeravanje mora biti prije HTML-a.
                header('Location: welcome.php');
                exit;
            }
        }
    } catch (mysqli_sql_exception $e) {
        $error = 'Greška pri pristupu bazi.';
        $databaseError = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="sr-Latn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login lab</title>
</head>
<body>
        <?php if ($databaseError !== ''): ?>
            <script>
                console.log(<?= json_encode($databaseError, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_INVALID_UTF8_SUBSTITUTE) ?>);
            </script>
        <?php endif; ?>
        <?php if ($error !== ''): ?>
            <p><?= $error ?></p>
        <?php endif; ?>
        <form method="POST" action="./index.php">
            <p><input type="text" name="username" placeholder="Username" required></p>
            <p><input type="password" name="password" placeholder="Password" required></p>
            <p><label><input type="checkbox" name="remember_me" value="1"> Remember me</label></p>
            <button type="submit" name="submit" value="1">Login</button>
        </form>
</body>
</html>
