<?php
/**
 * app/controllers/logout.php
 * Destrucción segura de la sesión del usuario
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Limpiar todas las variables de sesión
$_SESSION = array();

// 2. Destruir la cookie de sesión en el navegador si existe
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(), 
        '', 
        time() - 42000,
        $params["path"], 
        $params["domain"],
        $params["secure"], 
        $params["httponly"]
    );
}

// 3. Destruir la sesión en el servidor
session_destroy();

// 4. Redirigir al Login usando la ruta absoluta limpia
$protocolo = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
$baseUrl = $protocolo . $_SERVER['HTTP_HOST'] . "/clinica_pura/public/";

header("Location: " . $baseUrl . "login");
exit();