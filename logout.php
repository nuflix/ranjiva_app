<?php
// Odjava je dozvoljena samo kroz POST zahtjev.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    exit('Koristi POST zahtjev za odjavu.');
}

require_once __DIR__ . '/auth.php';
logout($authDb);
