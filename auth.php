<?php
// Ovaj fajl ukljuciti prije bilo kakvog HTML-a.
session_start();

function logout(mysqli $db): void
{
    // Ponistavamo token da cookie ne obnovi prijavu poslije odjave.
    if (isset($_SESSION['user_id'])) {
        $userId = (int) $_SESSION['user_id'];
        $db->query("UPDATE users SET remember_token = NULL WHERE id = $userId");
    }
    $_SESSION = [];
    session_destroy();
    setcookie(session_name(), '', ['expires' => time() - 3600, 'path' => '/']);
    setcookie('remember_token', '', AUTH_COOKIE_OPTIONS + ['expires' => time() - 3600]);
    header('Location: /ranjiva_app/index.php', true, 303);
    exit;
}

$authLoginPage = basename($_SERVER['SCRIPT_NAME']) === 'index.php';
$authLogoutPage = basename($_SERVER['SCRIPT_NAME']) === 'logout.php';
const AUTH_COOKIE_OPTIONS = [
    'path' => '/ranjiva_app/',
    'httponly' => true,
    'samesite' => 'Lax',
];

try {
    $authDb = new mysqli('localhost', 'root', '', 'ranjiva_app');
    $authDb->set_charset('utf8mb4');

    // Cookie obnavlja sesiju kada ona vise ne postoji.
    if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_token'])) {
        $authTokenQuery = $authDb->prepare('SELECT id FROM users WHERE remember_token = ?');
        $authTokenQuery->bind_param('s', $_COOKIE['remember_token']);
        $authTokenQuery->execute();
        $authUser = $authTokenQuery->get_result()->fetch_assoc();
        if ($authUser) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = (int) $authUser['id'];
        } else {
            setcookie('remember_token', '', AUTH_COOKIE_OPTIONS + ['expires' => time() - 3600]);
        }
    }

    // Obradjujemo uspjesnu prijavu ovdje da index.php ostane neizmijenjen.
    // Neuspjesna prijava nastavlja u index.php, koji prikazuje postojece poruke.
    if ($authLoginPage && !isset($_SESSION['user_id']) && isset($_POST['submit'])) {
        $authUsername = $_POST['username'] ?? '';
        $authPassword = $_POST['password'] ?? '';
        // Namjerno zadrzavamo SQL injection ranjivost login-a.
        $authUser = $authDb->query("SELECT id FROM users WHERE username = '$authUsername' AND password = '$authPassword'")->fetch_assoc();
        if ($authUser) {
            $authUserId = (int) $authUser['id'];
            $authRemember = isset($_POST['remember_me']);
            // Namjerna ranjivost: token izgleda nasumicno, ali zavisi samo od ID-a.
            $authToken = $authRemember ? md5('remember:' . $authUserId) : null;
            $authSaveToken = $authDb->prepare('UPDATE users SET remember_token = ? WHERE id = ?');
            $authSaveToken->bind_param('si', $authToken, $authUserId);
            $authSaveToken->execute();
            session_regenerate_id(true);
            $_SESSION['user_id'] = $authUserId;
            setcookie('remember_token', $authToken ?? '', AUTH_COOKIE_OPTIONS + [
                'expires' => $authRemember ? time() + 30 * 86400 : time() - 3600,
            ]);
        }
    }
} catch (mysqli_sql_exception $e) {
    if ($authLogoutPage) {
        http_response_code(500);
        exit('Greška pri pristupu bazi.');
    }
    // Login zadrzava svoju postojecu obradu gresaka; zasticene stranice ne otvaramo.
    if (!$authLoginPage && !isset($_SESSION['user_id'])) {
        header('Location: /ranjiva_app/index.php');
        exit;
    }
}

// POST endpoint sam poziva logout(), bez preusmjeravanja na welcome.
if ($authLogoutPage) {
    return;
}

if (isset($_SESSION['user_id']) && $authLoginPage) {
    header('Location: /ranjiva_app/welcome.php');
    exit;
}
if (!isset($_SESSION['user_id']) && !$authLoginPage) {
    header('Location: /ranjiva_app/index.php');
    exit;
}
