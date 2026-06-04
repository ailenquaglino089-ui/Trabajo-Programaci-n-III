<?php
// SaludWEB/logout.php
// Cierra la sesión actual y redirige al inicio del proyecto.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Limpiamos toda la sesión.
$_SESSION = [];

if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params['path'] ?: '/', $params['domain'],
        $params['secure'], $params['httponly']
    );
}

session_destroy();
header('Location: registro?mensaje=' . urlencode('Sesión cerrada correctamente.'));
exit;
